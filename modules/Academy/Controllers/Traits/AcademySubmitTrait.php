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

trait AcademySubmitTrait
{
    /**
     * Source-tournament blacklist status for the player chart page
     * GET /academy/get-source-eligibility/{teamId}/{tourId}
     */
    public function getSourceEligibility($teamId, $tourId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.academy_auth_required')
            ])->setStatusCode(401);
        }

        $tournamentModel = new \Modules\Admin\Models\TournamentModel();
        $tournament = $tournamentModel->getOneTournament($tourId);

        if (!$tournament || empty($tournament['require_source_players'])) {
            return $this->response->setJSON(['status' => 'success', 'enabled' => false]);
        }

        $sourceTourIds = array_column($tournamentModel->getSourceTournaments($tourId), 'source_tour_id');
        if (empty($sourceTourIds)) {
            return $this->response->setJSON(['status' => 'success', 'enabled' => false]);
        }

        // จำกัดผลลัพธ์เฉพาะนักเตะบนแผงของทีมนี้ (team + parent team)
        // — กัน payload ใหญ่ และไม่ leak รายชื่อนักเตะที่ถูกส่งทั้งระบบให้ user ทุกคน
        $teamIds = [(int) $teamId];
        $teamInfo = $this->db->table('team')->select('parent_team_id')->where('id', (int) $teamId)->get()->getRowArray();
        if (!empty($teamInfo['parent_team_id'])) {
            $teamIds[] = (int) $teamInfo['parent_team_id'];
        }
        $candidateRows = $this->db->table('player')
            ->select('player.id')
            ->join('academy_player_enrollments', 'academy_player_enrollments.player_id = player.id AND academy_player_enrollments.is_active = 1', 'inner')
            ->where('player.is_active', 1)
            ->whereIn('academy_player_enrollments.team_id', $teamIds)
            ->groupBy('player.id')
            ->get()
            ->getResultArray();
        $candidateIds = array_map('intval', array_column($candidateRows, 'id'));

        // ชุดเดิมที่เคยส่งโดยทีมนี้เอง (ทัวร์ต้นทาง + รายชื่อที่ส่งไว้แล้วในทัวร์นี้) → ยกเว้น
        // บล็อคเฉพาะนักเตะใหม่ที่เคยส่งกับทีมอื่น
        $ownTourTeamRow = $this->db->table('tournament_team')
            ->select('tour_team_id')
            ->where('tour_id', (int) $tourId)
            ->where('team_id', (int) $teamId)
            ->get()
            ->getRowArray();
        $ownTourTeamId = $ownTourTeamRow['tour_team_id'] ?? null;

        $sets = $tournamentModel->getSourceSubmissionSets($sourceTourIds, (int) $teamId, $ownTourTeamId);
        $blockedIds = $tournamentModel->resolveSourceIneligibleIds($candidateIds, $sets);

        // รายละเอียดต่อคน: เคยส่งรายการไหน โดยทีมไหน วันที่เท่าไหร่ (tooltip + การ์ดเตือน)
        $blockedDetails = $tournamentModel->getSourceSubmissionHistory($blockedIds, $sourceTourIds, (int) $teamId);

        $sourceTournaments = $this->db->table('tournament')
            ->select('tour_id, name, generation')
            ->whereIn('tour_id', $sourceTourIds)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status' => 'success',
            'enabled' => true,
            'blocked_ids' => $blockedIds,
            'blocked_details' => $blockedDetails,
            'source_tournaments' => $sourceTournaments,
        ]);
    }

    /**
     * Submit player list for a tournament
     * POST /academy/submit-player-list
     */
    public function submitPlayerList()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.academy_auth_required')
            ])->setStatusCode(401);
        }

        try {
            $tourTeamId = $this->request->getPost('tour_team_id');

            if (!$tourTeamId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => lang('Common.academy_select_tournament')
                ]);
            }

            $tournamentModel = new \Modules\Admin\Models\TournamentModel();

            // Get team_id and tour_id from tournament_team
            $tourTeamData = $this->db->table('tournament_team')
                ->where('tour_team_id', $tourTeamId)
                ->get()
                ->getRowArray();

            if (!$tourTeamData || !isset($tourTeamData['team_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => lang('Common.academy_tournament_not_found')
                ]);
            }

            $tourTeamTeamId = (int)$tourTeamData['team_id'];
            $tourId = $tourTeamData['tour_id'];

            // รายการที่จบไปแล้วห้ามส่ง/แก้รายชื่อ — เกณฑ์เดียวกับ dropdown และ modal เลือกทัวร์
            // "จบแล้ว" = แอดมินปิด is_active แล้ว และเลยวันจบมาแล้ว
            // (end_date คือวันปิดรับสมัคร วันแข่งจริงอยู่ที่ tournament_end_date)
            $tourRow = $this->db->table('tournament')
                ->select('is_active, end_date, tournament_end_date')
                ->where('tour_id', $tourId)
                ->get()->getRowArray();
            if ($tourRow) {
                $pickDate = static function ($v) {
                    return (!empty($v) && strpos((string) $v, '0000') !== 0) ? (string) $v : '';
                };
                $effEnd = $pickDate($tourRow['tournament_end_date'] ?? null)
                       ?: $pickDate($tourRow['end_date'] ?? null);
                if ((int) ($tourRow['is_active'] ?? 0) !== 1 && $effEnd !== '' && $effEnd < date('Y-m-d')) {
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => lang('Common.tournament_ended_cannot_submit')
                    ]);
                }
            }

            // ใช้ team_id ที่ส่งมาจากหน้าเลือก (ทีมที่ user กำลัง edit) เป็นแหล่งดึง chart_selection
            // หาก JS ไม่ได้ส่งมาให้ fallback เป็น team_id จาก tournament_team
            $postedTeamId = (int)($this->request->getPost('team_id') ?? 0);
            $selectionTeamId = $postedTeamId > 0 ? $postedTeamId : $tourTeamTeamId;

            // teamId ใช้สำหรับ jersey + parent_team check ต่อจากนี้ — ใช้ทีมที่ user เลือกอยู่
            $teamId = $selectionTeamId;

            // Get saved selection from database for this team
            $selection = $this->academyModel->getPlayerChartSelection($selectionTeamId);

            if (empty($selection)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => lang('Common.academy_save_players_first')
                ]);
            }

            // Filter out stale player IDs (players no longer in team / not enrolled in academy)
            // ใช้ logic เดียวกับหน้า player-list / player-chart:
            // นักเตะต้อง active + อยู่ใน player_teams ของทีมนี้ (หรือ parent team) + enrolled active ใน academy
            $teamInfo = $this->db->table('team')->select('id, parent_team_id')->where('id', $teamId)->get()->getRowArray();
            $teamIds = [(int)$teamId];
            if (!empty($teamInfo['parent_team_id'])) {
                $teamIds[] = (int)$teamInfo['parent_team_id'];
            }

            // resolve academy_id จาก academy_teams (relation team -> academy)
            $academyRow = $this->db->table('academy_teams')
                ->select('academy_id')
                ->where('team_id', (int)$teamId)
                ->get()->getRowArray();
            $resolvedAcademyId = (int)($academyRow['academy_id'] ?? 0);

            $validQuery = $this->db->table('player')
                ->select('player.id')
                ->join('academy_player_enrollments', 'academy_player_enrollments.player_id = player.id AND academy_player_enrollments.is_active = 1', 'inner')
                ->whereIn('academy_player_enrollments.team_id', $teamIds);
            if ($resolvedAcademyId > 0) {
                $validQuery->where('academy_player_enrollments.academy_id', $resolvedAcademyId);
            }
            $validRows = $validQuery->groupBy('player.id')->get()->getResultArray();
            $validIds = array_map('intval', array_column($validRows, 'id'));

            $beforeCount = count($selection);
            $selection = array_values(array_filter($selection, function ($id) use ($validIds) {
                return in_array((int)$id, $validIds, true);
            }));
            $removedCount = $beforeCount - count($selection);
            if ($removedCount > 0) {
                log_message('debug', "submitPlayerList: filtered out {$removedCount} stale player IDs for team {$teamId}");
            }

            if (empty($selection)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => lang('Common.academy_save_players_first')
                ]);
            }

            // Get tournament details to check player_count limit
            $tournament = $tournamentModel->getOneTournament($tourId);

            if ($tournament && !empty($tournament['player_count'])) {
                $maxPlayers = (int)$tournament['player_count'];
                $selectedCount = count($selection);

                if ($selectedCount > $maxPlayers) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => sprintf(lang('Common.academy_players_exceed'), $selectedCount, $maxPlayers)
                    ]);
                }
            }

            // Blacklist: players previously submitted in the configured source tournament(s) cannot be submitted
            if ($tournament && !empty($tournament['require_source_players'])) {
                $sourceTourIds = array_column($tournamentModel->getSourceTournaments($tourId), 'source_tour_id');
                if (!empty($sourceTourIds)) {
                    $ineligible = $tournamentModel->checkIneligibleSourcePlayers($tourTeamId, $selection, $sourceTourIds);
                    if (!empty($ineligible)) {
                        $ineligibleMessages = [];
                        foreach ($ineligible as $inel) {
                            $shirt = !empty($inel['shirt_number']) ? ' (' . $inel['shirt_number'] . ')' : '';
                            $line = $inel['player_name'] . $shirt;
                            if (!empty($inel['history_text'])) {
                                $line .= ' — ' . lang('Common.source_prev_submitted_label') . ': ' . $inel['history_text'];
                            }
                            $ineligibleMessages[] = $line;
                        }
                        return $this->response->setJSON([
                            'status' => 'error',
                            'message' => lang('Common.source_player_not_eligible_found'),
                            'ineligible_players' => $ineligible,
                            'ineligible_list' => implode("\n", $ineligibleMessages)
                        ]);
                    }
                } else {
                    // require_source_players เปิดอยู่ แต่ยังไม่ได้ตั้ง source tournaments
                    // Fail-safe: บล็อกการส่ง (กันกรณี config ไม่สมบูรณ์แล้วปล่อยนักเตะทุกคนผ่าน)
                    log_message('warning', "require_source_players enabled for tour {$tourId} but no source tournaments configured");
                    return $this->response->setJSON([
                        'status'  => 'error',
                        'message' => lang('Common.source_config_incomplete')
                    ]);
                }
            }

            // Check for duplicate players across teams (if enabled)
            if ($tournament && !empty($tournament['prevent_duplicate_player'])) {
                $linkedTours = $tournamentModel->getLinkedTournaments($tourId);
                $linkedTourIds = array_column($linkedTours, 'linked_tour_id');
                $duplicates = $tournamentModel->checkDuplicatePlayers($tourId, $tourTeamId, $selection, $linkedTourIds);

                if (!empty($duplicates)) {
                    $duplicateMessages = [];
                    foreach ($duplicates as $dup) {
                        $duplicateMessages[] = $dup['player_name'] . ' (' . lang('Common.duplicate_claimed_by') . ': ' . $dup['claimed_by_team'] . ')';
                    }
                    $duplicateList = implode("\n", $duplicateMessages);

                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => lang('Common.duplicate_player_found'),
                        'duplicates' => $duplicates,
                        'duplicate_list' => $duplicateList
                    ]);
                }
            }

            // Check for duplicate players in same tournament via another team (multi-academy support)
            // ตรวจสอบนักเตะที่ถูก submit ในทัวร์นาเมนต์เดียวกันผ่านทีมอื่นแล้ว (ตรวจจาก player_id ตรงๆ)
            if (!empty($selection)) {
                $selectedIds = array_values(array_map('intval', $selection));
                $idStr = implode(',', $selectedIds);
                // exclude the submitting team's parent/sub family (same club ≠ duplicate)
                $familyIds = $tournamentModel->getTeamFamilyIds($tourTeamId);
                $familyStr = !empty($familyIds) ? implode(',', array_map('intval', $familyIds)) : '0';
                $sql = "
                    SELECT tsp.player_id,
                           CONCAT(p.first_name, ' ', p.last_name) AS player_name,
                           p.nick_name,
                           t.name AS team_name,
                           tt2.team_id AS claiming_team_id,
                           tsp.tour_team_id AS other_tour_team_id
                    FROM tournament_submitted_players tsp
                    JOIN tournament_team tt2 ON tt2.tour_team_id = tsp.tour_team_id
                    JOIN team t ON t.id = tt2.team_id
                    JOIN player p ON p.id = tsp.player_id
                    WHERE tsp.player_id IN ({$idStr})
                      AND tt2.tour_id = ?
                      AND tt2.team_id NOT IN ({$familyStr})
                ";
                $duplicateByPlayerId = $this->db->query($sql, [$tourId])->getResultArray();

                // stale guard (v2): นับซ้ำเฉพาะเมื่อทีมที่ claim ยังมีนักเตะคนนี้เป็น active enrollment จริง
                // (เดิมเช็ค chart_selection JSON ที่ไม่ถูกล้างตอนลบนักเตะ → false positive block ทีมอื่นผิด)
                $duplicateByPlayerId = array_values(array_filter($duplicateByPlayerId, function ($dp) use ($tournamentModel) {
                    return $tournamentModel->isPlayerActiveInTeamFamily(
                        (int) ($dp['player_id'] ?? 0),
                        (int) ($dp['claiming_team_id'] ?? 0)
                    );
                }));

                if (!empty($duplicateByPlayerId)) {
                    // เติมทีมปัจจุบันที่นักเตะ active อยู่ + academy ของทีมที่ claim
                    // (นักเตะอาจย้ายจากทีมที่ submit เดิมไปอีกทีมในสโมสรเดียวกัน → โชว์ทีมปัจจุบันให้ไล่หาถูก)
                    foreach ($duplicateByPlayerId as &$dpRef) {
                        $ctid = (int) ($dpRef['claiming_team_id'] ?? 0);
                        $cur = $tournamentModel->getPlayerCurrentTeamInFamily((int) ($dpRef['player_id'] ?? 0), $ctid);
                        $dpRef['submitted_by_team'] = $dpRef['team_name'];
                        $dpRef['team_name']         = !empty($cur['team_name']) ? $cur['team_name'] : $dpRef['team_name'];
                        $dpRef['academy_name']      = $tournamentModel->getTeamAcademyName($ctid);
                    }
                    unset($dpRef);

                    $names = array_map(function ($dp) {
                        $name = $dp['player_name'];
                        if (!empty($dp['nick_name'])) {
                            $name .= ' (' . $dp['nick_name'] . ')';
                        }
                        $teamLabel = $dp['team_name'];
                        if (!empty($dp['academy_name']) && $dp['academy_name'] !== $dp['team_name']) {
                            $teamLabel .= ' / ' . $dp['academy_name'];
                        }
                        return $name . ' [ทีม ' . $teamLabel . ']';
                    }, $duplicateByPlayerId);

                    return $this->response->setJSON([
                        'status'             => 'error',
                        'message'            => 'พบนักเตะที่ลงทะเบียนในทัวร์นาเมนต์นี้แล้วผ่านทีมอื่น: ' . implode(', ', $names),
                        'duplicate_players'  => $duplicateByPlayerId,
                        'duplicates'         => array_map(function ($dp) {
                            return [
                                'player_id'          => $dp['player_id'],
                                'player_name'        => $dp['player_name'] . (!empty($dp['nick_name']) ? ' (' . $dp['nick_name'] . ')' : ''),
                                'claimed_by_team'    => $dp['team_name'],
                                'submitted_by_team'  => $dp['submitted_by_team'] ?? '',
                                'claimed_by_academy' => $dp['academy_name'] ?? '',
                                'tournament_name'    => '',
                                'submitted_at'       => '',
                            ];
                        }, $duplicateByPlayerId),
                    ]);
                }
            }

            // Save submitted players ก่อน — ถ้า fail จะไม่ set status (กัน status=submitted แต่ไม่มีรายชื่อ)
            if (!$tournamentModel->saveSubmittedPlayers($tourTeamId, $selection)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการบันทึกรายชื่อนักเตะ กรุณาลองใหม่อีกครั้ง'
                ]);
            }

            // Update player list status (หลังบันทึกรายชื่อสำเร็จแล้วเท่านั้น)
            $result = $tournamentModel->submitPlayerListStatus($tourTeamId);

            // (ยกเลิกแล้ว) เดิมคัดลอกรูปชุดจาก team_player_chart_selection มาลง tournament_team.jersey_*
            // ตอนนี้ชุดถูกแก้ที่ tournament_team โดยตรง (home_set_img/away_set_img/... ต่อทัวร์นาเมนต์)
            // ผ่านหน้า player-chart จึงไม่ต้องคัดลอก และห้ามเขียนทับของใหม่

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => lang('Common.academy_submit_success')
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => lang('Common.academy_submit_failed')
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Get submitted players for a tournament team
     * GET /academy/get-submitted-players/{tour_team_id}
     *
     * @param int $tourTeamId
     * @return \CodeIgniter\HTTP\Response
     */
    public function getSubmittedPlayers($tourTeamId)
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.academy_auth_required')
            ])->setStatusCode(401);
        }

        try {
            $tournamentModel = new \Modules\Admin\Models\TournamentModel();
            $players = $tournamentModel->getSubmittedPlayersWithDetails($tourTeamId);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $players
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
