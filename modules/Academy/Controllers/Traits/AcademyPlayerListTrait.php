<?php

namespace Modules\Academy\Controllers\Traits;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\Academy\Models\AcademyModel;
use Modules\Player\Models\PlayerModel;
use \Hermawan\DataTables\DataTable;
use App\Libraries\Date_thai;
use Modules\Academy\Controllers\Traits\AcademyAiTrait;
use Modules\Academy\Controllers\Traits\AcademyPlayerTrait;
use Modules\Academy\Controllers\Traits\AcademyIdpTrait;
use Modules\Academy\Controllers\Traits\AcademyReportTrait;
use Modules\Academy\Controllers\Traits\AcademyAssessmentTrait;
use Modules\Academy\Controllers\Traits\AcademyTrainingTrait;
use App\Traits\JerseyManagementTrait;
use App\Traits\PlayerAjaxTrait;
use App\Traits\ChartSelectionTrait;
use Modules\Academy\Controllers\Traits\AcademyRequestsTrait;
use Modules\Academy\Controllers\Traits\AcademyExportTrait;
use Modules\Academy\Controllers\Traits\AcademySubmitTrait;
use Modules\Academy\Controllers\Traits\AcademyPositionTrait;
use Modules\Academy\Controllers\Traits\AcademyMemberTrait;

trait AcademyPlayerListTrait
{
    /**
     * Player list for a specific team within academy
     * GET /academy/player-list/{academy_id}/{team_id}
     */
    /**
     * AJAX: ทัวร์นาเมนต์ที่ทีมใน academy นี้ลงแข่งและยัง active อยู่
     * GET /academy/chart-tournaments/{academy_id}
     *
     * ใช้กับเมนู "แผงรูป" บน subnav — เลือกทัวร์แล้วเด้งไปหน้าแผงรูปของทีมนั้น
     * (หน้าแผงรูปต้องรู้ team_id จึงคืนคู่ ทัวร์นาเมนต์+ทีม มาให้เลือก)
     */
    public function chartTournaments($academy_id)
    {
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_view_players');
        if ($err) return $err;

        $teamIds = $this->getAcademyTeamIds((int) $academy_id);
        if (empty($teamIds)) {
            return $this->response->setJSON(['status' => 'success', 'data' => []]);
        }

        // วันแข่งจริงอยู่ที่ tournament_start_date/tournament_end_date ส่วน start_date/end_date
        // คือช่วงรับสมัคร — บางรายการยังไม่กรอกวันแข่ง จึงถอยไปใช้ช่วงรับสมัครแทน
        $startExpr = "COALESCE(NULLIF(t.tournament_start_date,'0000-00-00'), NULLIF(t.start_date,'0000-00-00'))";
        $endExpr   = "COALESCE(NULLIF(t.tournament_end_date,'0000-00-00'), NULLIF(t.end_date,'0000-00-00'))";

        $rows = $this->db->table('tournament_team tt')
            ->select("t.tour_id, t.name AS tour_name, t.generation, t.logo AS tour_logo,
                      t.start_date AS reg_start_date, t.end_date AS reg_end_date,
                      t.tournament_start_date, t.tournament_end_date,
                      tt.team_id, tt.register_status, tt.player_list_status,
                      te.name AS team_name, te.logo AS team_logo,
                      ({$startExpr} IS NOT NULL AND {$startExpr} > CURDATE()) AS is_upcoming", false)
            ->join('tournament t', 't.tour_id = tt.tour_id', 'inner')
            ->join('team te', 'te.id = tt.team_id', 'inner')
            ->whereIn('tt.team_id', $teamIds)
            // ทีมยัง active อยู่ในรายการ = สมัครไว้และยังไม่ถูกปฏิเสธ (2)
            ->whereIn('tt.register_status', [0, 1, 3])
            ->where('te.is_active', 1)
            ->where('t.is_deleted', 0)
            // เอาทั้งรายการที่แอดมินยังเปิดอยู่ (is_active) และรายการที่ยังไม่ถึงวันจบ
            // — ครอบคลุมรายการที่ยังไม่ถึงเวลาแข่งด้วย
            ->groupStart()
                ->where('t.is_active', 1)
                ->orWhere("{$endExpr} IS NULL", null, false)
                ->orWhere("{$endExpr} >= CURDATE()", null, false)
            ->groupEnd()
            // รายการที่ยังไม่ถึงเวลาแข่งขึ้นก่อน แล้วค่อยไล่จากรายการใหม่สุด
            ->orderBy('is_upcoming', 'DESC', false)
            ->orderBy('t.tour_id', 'DESC')
            ->orderBy('te.name', 'ASC')
            ->get()->getResultArray();

        return $this->response->setJSON(['status' => 'success', 'data' => $rows]);
    }

    public function player_list($academy_id, $team_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $teamResult = $this->resolveTeamForAcademy($academy_id, $team_id);
        if ($teamResult['redirect']) return $teamResult['redirect'];
        $data['team'] = $teamResult['team'];

        // โหลด roster (นักเตะในทีม) + available (อยู่ใน Academy แต่ยังไม่อยู่ทีมนี้)
        $rosterData = $this->academyModel->getAcademyTeamRoster(
            (int)$academy_id, (int)$data['team']['id']
        );
        $data['roster']    = $rosterData['roster'];
        $data['available'] = $rosterData['available'];
        $data['canManage'] = $this->hasPermission($academy_id, 'perm_enroll_player');

        return view('Modules\Academy\Views\player_team_board.php', $data);
    }

    /**
     * AJAX: เพิ่มนักเตะเข้าทีม (multi-team support)
     * POST /academy/team-add-player
     */
    public function teamAddPlayer()
    {
        $input = $this->getInput();
        $academy_id = (int)($input['academy_id'] ?? 0);
        $team_id    = (int)($input['team_id'] ?? 0);
        $player_id  = (int)($input['player_id'] ?? 0);

        if (!$academy_id || !$team_id || !$player_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
        }
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_enroll_player');
        if ($err) return $err;
        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ทีมไม่ได้อยู่ใน Academy นี้']);
        }
        $result = $this->academyModel->addPlayerToTeam($team_id, $player_id);
        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /**
     * AJAX: นำนักเตะออกจากทีม (ไม่ใช่ unenroll Academy)
     * POST /academy/team-remove-player
     */
    public function teamRemovePlayer()
    {
        $input = $this->getInput();
        $academy_id = (int)($input['academy_id'] ?? 0);
        $team_id    = (int)($input['team_id'] ?? 0);
        $player_id  = (int)($input['player_id'] ?? 0);

        if (!$academy_id || !$team_id || !$player_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
        }
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_enroll_player');
        if ($err) return $err;
        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ทีมไม่ได้อยู่ใน Academy นี้']);
        }
        $result = $this->academyModel->removePlayerFromTeam($team_id, $player_id);
        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /**
     * Player chart (formation) for a specific team within academy
     * GET /academy/player-chart/{academy_id}/{team_id}
     */
    public function football_player_chart($academy_id, $team_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['position'] = $this->playerModel->player_position();
                // เพิ่ม role display names
        $data['positionMapping'] = [
            'team_manager' => lang('Common.coach_role_team_manager'),
            'head_coach' => lang('Common.coach_role_head_coach'),
            'coach' => lang('Common.coach_role_coach'),
            'assistant_coach' => lang('Common.coach_role_assistant_coach'),
            'team_staff' => lang('Common.coach_role_team_staff'),
            'trainer' => lang('Common.coach_role_trainer'),
            'scout' => lang('Common.coach_role_scout')
        ];

        $teamResult = $this->resolveTeamForAcademy($academy_id, $team_id);
        if ($teamResult['redirect']) return $teamResult['redirect'];
        $data['team'] = $teamResult['team'];
        $team_id = $teamResult['team_id'];

        // ดึงเฉพาะนักเตะที่อยู่ในทีมนี้ + enrolled ในกmunity academy เดียวกัน
        // (ตรงกับหน้า academy/player-list ที่ใช้ getAcademyTeamPlayersDataTable)
        $db = \Config\Database::connect();
        $playerRows = $db->table('player')
            ->select("player.*,
                      COALESCE(academy_player_enrollments.shirt_number, player.shirt_number) as shirt_number,
                      academy_player_enrollments.position_id as player_position_id,
                      academy_player_enrollments.sub_position_ids as player_sub_position_id,
                      tbl_player_position.name_th as position_name_th,
                      tbl_player_position.name_en as position_name_en")
            ->join('academy_player_enrollments',
                'academy_player_enrollments.player_id = player.id AND academy_player_enrollments.academy_id = ' . (int)$academy_id . ' AND academy_player_enrollments.is_active = 1',
                'inner')
            ->join('tbl_player_position', 'tbl_player_position.id = academy_player_enrollments.position_id', 'left')
            ->where('academy_player_enrollments.team_id', (int)$team_id)
            ->groupBy('player.id')
            ->orderBy('CAST(COALESCE(academy_player_enrollments.shirt_number, player.shirt_number) AS UNSIGNED) ASC', '', false)
            ->get()->getResultArray();
        $data['players'] = $playerRows;

        // Prepare available teams for selection dropdown (all academy teams)
        $data['available_teams'] = $this->academyModel->getAcademyTeams($academy_id);

        // Get joined tournaments for all academy teams (always include current team_id)
        $tournamentModel = new \Modules\Admin\Models\TournamentModel();
        $allTeamIds = $this->getAcademyTeamIds($academy_id);
        if ($team_id && !in_array($team_id, $allTeamIds)) {
            $allTeamIds[] = $team_id;
        }
        $data['joined_tournaments'] = !empty($allTeamIds)
            ? $tournamentModel->getTeamJoinedTournamentsMultiple($allTeamIds)
            : [];

        // Get coaches for the team (approved only) — ถ้าทีมนี้ไม่มี coach ให้ดึงจากทุกทีมใน academy
        $data['coaches'] = [];
        if ($team_id) {
            $data['coaches'] = $this->academyModel->getTeamCoaches($team_id, false);
            /*
            if (empty($data['coaches'])) { //
                // Fallback: ดึง coach จากทุกทีมใน academy
                $academyTeamIds = $this->getAcademyTeamIds($academy_id);
                foreach ($academyTeamIds as $aTeamId) {
                    if ($aTeamId == $team_id) continue;
                    $teamCoaches = $this->academyModel->getTeamCoaches($aTeamId, false);
                    if (!empty($teamCoaches)) {
                        $data['coaches'] = array_merge($data['coaches'], $teamCoaches);
                    }
                }
                // ลบ coach ซ้ำ
                $seen = [];
                $data['coaches'] = array_filter($data['coaches'], function ($c) use (&$seen) {
                    if (in_array($c['id'], $seen)) return false;
                    $seen[] = $c['id'];
                    return true;
                });
            }
            */
        }

        return view('Modules\Academy\Views\player_chart.php', $data);
    }

    // =====================================================
    // All Players / All Coaches across Academy
    // =====================================================

    /**
     * All players across all teams in the academy
     * GET /academy/players/{academy_id}
     */
    public function players($academy_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['players'] = $this->academyModel->getAcademyPlayers($academy_id);
        $data['teams'] = $this->academyModel->getAcademyTeams($academy_id);

        return view('Modules\Academy\Views\player_list.php', $data);
    }

    public function footballStatsDashboard($academy_id = null)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $tourId = (int)($this->request->getGet('tour_id') ?? 0);
        $data['stats'] = $this->academyModel->getFootballStatsDashboard((int)$academy_id, $tourId ?: null);

        return view('Modules\Academy\Views\football_stats_dashboard.php', $data);
    }

    private function getPlayerPositionNameMap(): array
    {
        $isThai = service('request')->getLocale() === 'th';
        $nameColumn = $isThai ? 'name_th' : 'name_en';
        $rows = \Config\Database::connect()
            ->table('tbl_player_position')
            ->select("id, {$nameColumn} as position_name")
            ->get()
            ->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $map[(string)$row['id']] = $row['position_name'];
        }

        return $map;
    }

    private function renderPlayerPositionLabel($row, array $positionMap): string
    {
        if (!empty($row->player_position_id)) {
            $mainPositionId = (string)$row->player_position_id;
            $mainPositionName = !empty($row->position_name_th)
                ? $row->position_name_th
                : ($positionMap[$mainPositionId] ?? '');

            if ($mainPositionName !== '') {
                return '<span class="badge bg-success">' . esc($mainPositionName) . '</span>';
            }
        }

        if (empty($row->player_sub_position_id)) {
            return '-';
        }

        $decodedHtml = html_entity_decode($row->player_sub_position_id);
        $subIds = json_decode($decodedHtml, true);
        if (!is_array($subIds)) {
            $subIds = array_filter(explode(',', $decodedHtml));
        }

        $subNames = [];
        foreach ($subIds as $subId) {
            $subId = trim((string)$subId, " \t\n\r\0\x0B\"[]");
            if ($subId !== '' && isset($positionMap[$subId])) {
                $subNames[] = $positionMap[$subId];
            }
        }

        if (empty($subNames)) {
            return '-';
        }

        return '<span class="badge bg-warning text-dark">' . esc(implode(', ', $subNames)) . '</span>';
    }

    /**
     * DataTable AJAX endpoint: Get all players across all teams in the academy
     * POST /academy/get-academy-players/{academy_id}
     */
    public function getAcademyPlayersDataTable($academy_id = null)
    {
        if (!$academy_id || !$this->canViewAcademy($academy_id)) {
            return $this->response->setJSON(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $db = \Config\Database::connect();
        $currentUserId = session()->get('user_id');

        $builder = $db->table('academy_player_enrollments');
        $builder->select("player.id, player.first_name, player.last_name, player.nick_name, player.is_active,
                          player.birth_date, player.image_path,
                          academy_player_enrollments.shirt_number, academy_player_enrollments.team_id,
                          academy_player_enrollments.position_id as player_position_id,
                          academy_player_enrollments.sub_position_ids as player_sub_position_id,
                          tbl_player_position.name_th as position_name_th, tbl_player_position.name_en as position_name_en,
                          team.name as team_name");
        $builder->join('player', 'player.id = academy_player_enrollments.player_id');
        $builder->join('tbl_player_position', 'tbl_player_position.id = academy_player_enrollments.position_id', 'LEFT');
        $builder->join('team', 'team.id = academy_player_enrollments.team_id', 'LEFT');
        $builder->where('academy_player_enrollments.academy_id', $academy_id);
        $builder->where('academy_player_enrollments.is_active', 1);

        // Handle custom ordering
        $orderBy = $this->request->getPost('order');
        if ($orderBy) {
            foreach ($orderBy as $order) {
                $columnIndex = $order['column'];
                $orderDir = $order['dir'];
                $columnName = $this->request->getPost('columns')[$columnIndex]['name'] ?? null;

                if ($columnName === 'academy_player_enrollments.shirt_number') {
                    // Use CAST for numeric sorting
                    $builder->orderBy('CAST(academy_player_enrollments.shirt_number AS SIGNED)', $orderDir);
                } elseif (in_array($columnName, ['player.first_name', 'player.last_name', 'player.nick_name', 'tbl_player_position.name_th'])) {
                    // Use Thai collation for proper Thai sorting (จัดการใน library ไม่ให้ sort ซ้ำ)
                    // ไม่เพิ่ม orderBy ที่นี่ เพราะ library จะจัดการให้
                }
            }
        }

        $canManage = $this->hasPermission($academy_id, 'perm_enroll_player');
        $positionMap = $this->getPlayerPositionNameMap();

        return \Hermawan\DataTables\DataTable::of($builder)
            ->add('image', function ($row) {
                $imgSrc = !empty($row->image_path) ? base_url($row->image_path) : base_url('public/img/img_profile.jpg');
                return '<img src="' . $imgSrc . '" class="rounded-circle border border-2 border-white shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">';
            })
            ->add('full_name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })
            ->add('player_sub_position', function ($row) use ($positionMap) {
                return $this->renderPlayerPositionLabel($row, $positionMap);
            })
            ->edit('birth_date', function ($row) {
                if (empty($row->birth_date)) {
                    return '-';
                }
                $thai_months = [
                    1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
                ];
                $timestamp = strtotime($row->birth_date);
                $day = date('d', $timestamp);
                $month = (int)date('m', $timestamp);
                $year = (int)date('Y', $timestamp) + 543;
                return "$day {$thai_months[$month]} $year";
            })
            ->add('action', function ($row) use ($academy_id, $canManage) {
                $encrypter = \Config\Services::encrypter();
                $editLink = base_url('academy/player-edit/' . $academy_id . '/' . $row->id);
                $viewLink = base_url('academy/player-view/' . $academy_id . '/' . $row->id);
                $reportUrl = base_url('academy/report-player/' . $academy_id . '/' . $row->id);

                $reportBtn = "<a class='btn btn-success btn-sm rounded-pill me-1' href='" . $reportUrl . "' title='รายงาน'><i class='fas fa-info-circle text-white' style='font-size: 14px;'></i></a>";
                $viewBtn = "<a class='btn btn-info btn-sm rounded-pill me-1' href='" . $viewLink . "'><i class='ti ti-eye text-white' style='font-size: 16px;'></i></a>";

                if ($canManage) {
                    $editBtn = "<a class='btn btn-primary btn-sm rounded-pill me-1' href='" . $editLink . "'><i class='ti ti-edit text-white' style='font-size: 16px;'></i></a>";
                    $unenrollBtn = "<button onclick='unenrollPlayer(" . $row->id . ", \"" . htmlspecialchars($row->first_name . ' ' . $row->last_name) . "\")' class='btn btn-danger btn-sm rounded-pill'><i class='ti ti-user-minus text-white' style='font-size: 16px;'></i></button>";
                    return $reportBtn . $editBtn . $unenrollBtn;
                }
                return $reportBtn;
            })
            ->addNumbering()
            ->setSearchableColumns(['player.first_name', 'player.last_name', 'player.nick_name', 'academy_player_enrollments.shirt_number', 'tbl_player_position.name_th', 'tbl_player_position.name_en'])
            ->toJson(true);
    }

    /**
     * DataTable AJAX endpoint: Players in a specific team within academy
     * (intersect of player_teams.team_id AND academy_player_enrollments.academy_id)
     * POST /academy/get-team-players/{academy_id}/{team_id}
     */
    public function getAcademyTeamPlayersDataTable($academy_id = null, $team_id = null)
    {
        if (!$academy_id || !$team_id || !$this->canViewAcademy($academy_id)) {
            return $this->response->setJSON(['data' => [], 'recordsTotal' => 0, 'recordsFiltered' => 0]);
        }

        $db = \Config\Database::connect();

        $builder = $db->table('player');
        $builder->select("player.id, player.first_name, player.last_name, player.nick_name, player.is_active,
                          player.birth_date, player.image_path,
                          COALESCE(academy_player_enrollments.shirt_number, player.shirt_number) as shirt_number,
                          academy_player_enrollments.position_id as player_position_id,
                          academy_player_enrollments.sub_position_ids as player_sub_position_id,
                          tbl_player_position.name_th as position_name_th,
                          tbl_player_position.name_en as position_name_en");
        // Numeric helper for sorting shirt_number (stored as VARCHAR). escape=false to keep CAST/COALESCE intact.
        $builder->select("CAST(COALESCE(academy_player_enrollments.shirt_number, player.shirt_number) AS UNSIGNED) as shirt_sort", false);
        $builder->join('academy_player_enrollments',
            'academy_player_enrollments.player_id = player.id AND academy_player_enrollments.academy_id = ' . (int)$academy_id . ' AND academy_player_enrollments.is_active = 1',
            'inner');
        $builder->join('tbl_player_position', 'tbl_player_position.id = academy_player_enrollments.position_id', 'LEFT');
        $builder->where('academy_player_enrollments.team_id', (int)$team_id);
        $builder->groupBy('player.id');
        $positionMap = $this->getPlayerPositionNameMap();

        return \Hermawan\DataTables\DataTable::of($builder)
            ->add('image', function ($row) {
                $imgSrc = !empty($row->image_path) ? base_url($row->image_path) : base_url('public/img/img_profile.jpg');
                return '<img src="' . $imgSrc . '" class="rounded-circle border border-2 border-white shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">';
            })
            ->add('full_name', function ($row) {
                return $row->first_name . ' ' . $row->last_name;
            })
            ->add('player_sub_position', function ($row) use ($positionMap) {
                return $this->renderPlayerPositionLabel($row, $positionMap);
            })
            ->edit('birth_date', function ($row) {
                if (empty($row->birth_date)) {
                    return '-';
                }
                $months = [
                    1 => lang('Common.month_1'), lang('Common.month_2'), lang('Common.month_3'),
                    lang('Common.month_4'), lang('Common.month_5'), lang('Common.month_6'),
                    lang('Common.month_7'), lang('Common.month_8'), lang('Common.month_9'),
                    lang('Common.month_10'), lang('Common.month_11'), lang('Common.month_12')
                ];
                $timestamp = strtotime($row->birth_date);
                $day = date('d', $timestamp);
                $month = (int)date('m', $timestamp);
                $isThai = service('request')->getLocale() === 'th';
                $year = $isThai ? ((int)date('Y', $timestamp) + 543) : date('Y', $timestamp);
                return "$day {$months[$month]} $year";
            })
            ->add('age', function ($row) {
                if (empty($row->birth_date)) return '-';
                try {
                    $bd  = new \DateTime($row->birth_date);
                    $now = new \DateTime();
                    return $now->diff($bd)->y;
                } catch (\Throwable $e) { return '-'; }
            })
            ->addNumbering()
            ->setSearchableColumns(['player.first_name', 'player.last_name', 'player.nick_name', 'tbl_player_position.name_th', 'tbl_player_position.name_en'])
            ->toJson(true);
    }
}
