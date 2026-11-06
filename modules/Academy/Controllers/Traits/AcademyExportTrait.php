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

trait AcademyExportTrait
{
    /**
     * Export player document list as PDF
     * GET /academy/player-document-list?id={team_id}
     */
    public function export_player_document()
    {
        $input = $this->request->getGet();
        $requestedId = !empty($input['id']) ? (int)$input['id'] : (int)(session()->get("team.id") ?? 0);
        $tourId = !empty($input['tour_id']) ? (int)$input['tour_id'] : null;
        $tourTeamId = !empty($input['tour_team_id']) ? (int)$input['tour_team_id'] : null;

        if (empty($requestedId) && empty($tourTeamId)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $resolvedTournamentTeam = null;
        if (!empty($tourTeamId)) {
            $resolvedTournamentTeam = $this->db->table('tournament_team')
                ->where('tour_team_id', $tourTeamId)
                ->get()
                ->getRowArray();
        }

        $id = $requestedId;
        if ($resolvedTournamentTeam) {
            $id = (int) $resolvedTournamentTeam['team_id'];
            $tourId = $tourId ?: (int) $resolvedTournamentTeam['tour_id'];
        }

        // PDPA/Security: Check ownership or super admin
        $userAcademyId = getUserAcademyId();
        $teamInfo = getTeamAcademyInfo($id);
        $isTeamOwner = session()->get('team.id') == $id;
        $isAcademyOwner = ($teamInfo['academy_id'] ?? null) == $userAcademyId && $userAcademyId !== null;
        if (!$isTeamOwner && !$isAcademyOwner && !isSuperAdmin()) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $data['team'] = $this->academyModel->getTeam($id);
        if (empty($data['team']) && !empty($requestedId)) {
            $fallbackTournamentTeam = $this->db->table('tournament_team')
                ->where('tour_team_id', $requestedId)
                ->get()
                ->getRowArray();

            if (!empty($fallbackTournamentTeam)) {
                $resolvedTournamentTeam = $fallbackTournamentTeam;
                $tourTeamId = (int) $fallbackTournamentTeam['tour_team_id'];
                $tourId = $tourId ?: (int) $fallbackTournamentTeam['tour_id'];
                $id = (int) $fallbackTournamentTeam['team_id'];
                $data['team'] = $this->academyModel->getTeam($id);
            }
        }

        if (empty($data['team'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $input['id'] = $id;
        if (!empty($tourId)) {
            $input['tour_id'] = $tourId;
        }
        if (!empty($tourTeamId)) {
            $input['tour_team_id'] = $tourTeamId;
        }
        $data['input'] = $input;

        // Check if tour_id is provided to filter submitted players
        if (!empty($input['tour_id'])) {
            $tourId = $input['tour_id'];

            // Get tournament data for title
            $tournament = $this->db->table('tournament')
                ->where('tour_id', $tourId)
                ->get()
                ->getRowArray();
            if ($tournament) {
                $data['input']['title'] = $tournament['name'];
            }

            // Get tour_team_id
            $tourTeam = $this->db->table('tournament_team')
                ->where('tour_id', $tourId)
                ->where('team_id', $id)
                ->get()
                ->getRowArray();

            if ($tourTeam) {
                // Get submitted player IDs
                $submittedPlayers = $this->db->table('tournament_submitted_players')
                    ->where('tour_team_id', $tourTeam['tour_team_id'])
                    ->get()
                    ->getResultArray();

                $selectedPlayerIds = array_column($submittedPlayers, 'player_id');

                if (!empty($selectedPlayerIds)) {
                    $allPlayers = $this->playerModel->get_PlayerListById($selectedPlayerIds, [], '', false);
                    $data['players'] = array_filter($allPlayers, function ($player) use ($selectedPlayerIds) {
                        return in_array($player['id'], $selectedPlayerIds);
                    });
                } else {
                    $data['players'] = $this->playerModel->get_PlayerListById($selectedPlayerIds, [5, 6, 7], '', false);
                }
            } else {
                $data['players'] = $this->playerModel->get_PlayerList($id, [5, 6, 7], '', false);
            }
        } else {
            $data['players'] = $this->playerModel->get_PlayerList($id, [5, 6, 7], '', false);
        }

        $html = view('Modules\Academy\Views\export\player_document_1.php', $data);
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'thsarabun',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 30,
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_header' => 1,
            'margin_footer' => 1,
            'orientation' => 'P',
        ]);

        $headerText = '<table style="width: 100%;">
        <tr>
            <td style="text-align:center;width: 15%;">
                <img src="' . base_url("public/img/jootball_logo.png") . '" width="70" alt="">&nbsp;<br>
            </td>
            <td style="width:70%; text-align:center;">
                  <span style="font-size: 18px;">เอกสารฉบับนี้ถูกสร้างโดยแอพพลิเคชั่น Jootball โดยทุกทีมสามารถใช้ได้ฟรี<br>สนใจใช้งานได้ทาง Line ค้นหาคำว่า @511lucuy หรือสแกน Qr Code เพื่อใช้งาน</span>
            </td>
            <td style="text-align:center;width: 15%;">
                <img src="' . base_url("public/img/jigsaw-logo-2020_0.png") . '" width="70" alt="">&nbsp;
            </td>
        </tr>
    </table>';
        $mpdf->SetHTMLHeader($headerText);

        $footerText = '<table style="width: 100%;">
        <tr>
            <td style="text-align:center;width: 15%;">
                <img src="' . base_url("public/img/jootball_logo.png") . '" width="70" alt="" style=""><br>
            </td>
            <td style="width:70%">
             <img width="70" src="https://qr-official.line.me/gs/M_511ulcuy_BW.png?oat_content=qr">
             <br/>
                <span style="font-size: 20px;">เอกสารฉบับนี้ถูกสร้างโดยแอพพลิเคชั่น Jootball โดยทุกทีมสามารถใช้ได้ฟรี<br>สนใจใช้งานได้ทาง Line ค้นหาคำว่า @511lucuy หรือสแกน Qr Code เพื่อใช้งาน</span>
            </td>
             <td style="text-align:center;width: 15%;">
               <img src="' . base_url("public/img/jigsaw-logo-2020_0.png") . '" width="70" alt="" style="">
            </td>
        </tr>
        </table>';
        $footer = array(
            'odd' => array(
                'C' => array(
                    'content' => $footerText,
                    'font-size' => 10,
                    'font-style' => 'I',
                    'font-family' => 'thsarabun',
                    'color' => '#000000'
                ),
                'line' => 0,
            ),
            'even' => array(
                'C' => array(
                    'content' => $footerText,
                    'font-size' => 10,
                    'font-style' => 'I',
                    'font-family' => 'thsarabun',
                    'color' => '#000000'
                ),
                'line' => 0,
            )
        );
        $fileName = str_replace('.', '', $data['team']['name'] ?? 'export') . '.pdf';
        $mpdf->SetFooter($footer);
        $mpdf->WriteHTML($html);
        $mpdf->Output('รายชื่อ_' . $fileName, 'I');
    }

    /**
     * Export player chart (formation) as PDF
     * GET /academy/export-player-chart?id={team_id}
     */
    public function export_player_chart()
    {
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $input = $this->request->getGet();
        $debugMode = !empty($input['debug']);
        $debugLog = [];
        $requestedId = !empty($input['id']) ? (int) $input['id'] : (int) (session()->get("team.id") ?? 0);
        $tourId = !empty($input['tour_id']) ? (int) $input['tour_id'] : 0;
        $tourTeamId = !empty($input['tour_team_id']) ? (int) $input['tour_team_id'] : 0;
        $resolvedTournamentTeam = null;

        if (empty($requestedId) && empty($tourTeamId)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!empty($tourTeamId)) {
            $resolvedTournamentTeam = $this->db->table('tournament_team')
                ->where('tour_team_id', $tourTeamId)
                ->get()
                ->getRowArray();
        }
        if ($debugMode) {
            $debugLog[] = 'requestedId=' . $requestedId;
            $debugLog[] = 'tourId=' . $tourId;
            $debugLog[] = 'tourTeamId=' . $tourTeamId;
            $debugLog[] = 'resolvedTournamentTeam(initial)=' . json_encode($resolvedTournamentTeam, JSON_UNESCAPED_UNICODE);
        }

        $id = $requestedId;
        if (!empty($resolvedTournamentTeam['team_id'])) {
            $id = (int) $resolvedTournamentTeam['team_id'];
            if (empty($tourId) && !empty($resolvedTournamentTeam['tour_id'])) {
                $tourId = (int) $resolvedTournamentTeam['tour_id'];
            }
        }

        // PDPA/Security: Check ownership or super admin
        $userAcademyId = getUserAcademyId();
        $teamInfo = getTeamAcademyInfo($id);
        $isTeamOwner = session()->get('team.id') == $id;
        $isAcademyOwner = ($teamInfo['academy_id'] ?? null) == $userAcademyId && $userAcademyId !== null;
        if (!$isTeamOwner && !$isAcademyOwner && !isSuperAdmin()) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $data['team'] = $this->academyModel->getTeam($id);
        if (empty($data['team']) && !empty($requestedId)) {
            $fallbackTournamentTeam = $this->db->table('tournament_team')
                ->where('tour_team_id', $requestedId)
                ->get()
                ->getRowArray();

            if (!empty($fallbackTournamentTeam['team_id'])) {
                $resolvedTournamentTeam = $fallbackTournamentTeam;
                $tourTeamId = (int) $fallbackTournamentTeam['tour_team_id'];
                $tourId = !empty($tourId) ? $tourId : (int) $fallbackTournamentTeam['tour_id'];
                $id = (int) $fallbackTournamentTeam['team_id'];
                $data['team'] = $this->academyModel->getTeam($id);
            }
        }

        if (empty($data['team'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        if ($debugMode) {
            $debugLog[] = 'resolved team id=' . $id;
            $debugLog[] = 'resolved team=' . json_encode($data['team'], JSON_UNESCAPED_UNICODE);
        }

        $input['id'] = $id;
        if (!empty($tourId)) {
            $input['tour_id'] = $tourId;
        }
        if (!empty($tourTeamId)) {
            $input['tour_team_id'] = $tourTeamId;
        }
        $data['input'] = $input;

        // Get selected player IDs if provided
        if (!empty($input['players'])) {
            $selectedPlayerIds = array_map('intval', explode(',', $input['players']));
            // Query players directly by IDs (ไม่ต้องผ่าน player_teams เพราะนักเตะอาจย้ายทีมแล้ว)
            $filteredPlayers = $this->db->table('player')
                ->select('player.id, player.first_name, player.last_name, player.nick_name, player.birth_date, COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id, ape_exp.shirt_number as shirt_number, ape_exp.sub_position_ids as player_sub_position_id, player.image_path, player.id_card, player.id_card_image, coarse_exp1.name_th as position_name_th, coarse_exp1.name_en as position_name_en')
                ->join('academy_player_enrollments ape_exp', 'ape_exp.player_id = player.id AND ape_exp.team_id = ' . (int)$id . ' AND ape_exp.is_active = 1', 'LEFT')
                ->join('tbl_player_position', 'tbl_player_position.id = ape_exp.position_id', 'LEFT')
                ->join('tbl_player_position coarse_exp1', 'coarse_exp1.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)', 'LEFT')
                ->whereIn('player.id', $selectedPlayerIds)
                ->orderBy('CAST(ape_exp.shirt_number AS unsigned)', 'ASC')
                ->get()
                ->getResultArray();
        } elseif (!empty($tourId) || !empty($tourTeamId)) {
            $tourTeam = $resolvedTournamentTeam;
            if (empty($tourTeam) && !empty($tourTeamId)) {
                $tourTeam = $this->db->table('tournament_team')
                    ->where('tour_team_id', $tourTeamId)
                    ->get()
                    ->getRowArray();
            }
            if (empty($tourTeam) && !empty($tourId)) {
                $tourTeam = $this->db->table('tournament_team')
                    ->where('tour_id', $tourId)
                    ->where('team_id', $id)
                    ->get()
                    ->getRowArray();
            }

            if ($tourTeam) {
                if ($debugMode) {
                    $debugLog[] = 'tourTeam(used)=' . json_encode($tourTeam, JSON_UNESCAPED_UNICODE);
                }
                $submittedPlayers = $this->db->table('tournament_submitted_players')
                    ->where('tour_team_id', $tourTeam['tour_team_id'])
                    ->get()
                    ->getResultArray();

                $selectedPlayerIds = array_column($submittedPlayers, 'player_id');

                if (!empty($selectedPlayerIds)) {
                    // Query submitted players directly by IDs (ไม่ต้องพึ่ง player_teams JOIN)
                    $filteredPlayers = $this->db->table('player')
                        ->select('player.id, player.first_name, player.last_name, player.nick_name, player.birth_date, COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id, ape_exp.shirt_number as shirt_number, ape_exp.sub_position_ids as player_sub_position_id, player.image_path, player.id_card, player.id_card_image, coarse_exp2.name_th as position_name_th, coarse_exp2.name_en as position_name_en')
                        ->join('academy_player_enrollments ape_exp', 'ape_exp.player_id = player.id AND ape_exp.team_id = ' . (int)$id . ' AND ape_exp.is_active = 1', 'LEFT')
                        ->join('tbl_player_position', 'tbl_player_position.id = ape_exp.position_id', 'LEFT')
                        ->join('tbl_player_position coarse_exp2', 'coarse_exp2.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)', 'LEFT')
                        ->whereIn('player.id', $selectedPlayerIds)
                        ->orderBy('CAST(ape_exp.shirt_number AS unsigned)', 'ASC')
                        ->get()
                        ->getResultArray();
                } else {
                    $filteredPlayers = $this->playerModel->get_PlayerList($id, [5, 6, 7], '', false);
                }
            } else {
                $filteredPlayers = $this->playerModel->get_PlayerList($id, [5, 6, 7], '', false);
            }
        } else {
            $filteredPlayers = $this->playerModel->get_PlayerList($id, [5, 6, 7], '', false);
        }

        // Add player_sub_position field to each player
        $positions = $this->db->table('tbl_player_position')->get()->getResultArray();
        $positionMap = [];
        foreach ($positions as $pos) {
            $positionMap[$pos['id']] = $pos['name_en'];
        }

        $data['players'] = [];
        foreach ($filteredPlayers as $player) {
            $positionNames = [];
            $subPositions = [];

            if (!empty($player['player_sub_position_id'])) {
                $decoded_html = html_entity_decode($player['player_sub_position_id']);
                $decoded = json_decode($decoded_html, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $subPositions = array_map('intval', $decoded);
                } else {
                    $subPositions = array_map('intval', array_filter(explode(',', $decoded_html)));
                }
            }

            foreach ($subPositions as $posId) {
                if (isset($positionMap[$posId])) {
                    $positionNames[] = $positionMap[$posId];
                }
            }

            $player['player_sub_position'] = !empty($positionNames) ? implode(', ', $positionNames) : '-';
            $data['players'][] = $player;
        }
        if ($debugMode) {
            $debugLog[] = 'players count=' . count($data['players']);
        }

        // Handle selected coaches for export
        $data['selected_coaches'] = [];
        if (!empty($input['coaches'])) {
            $selectedCoachIds = array_map('intval', explode(',', $input['coaches']));
            $coaches = [];
            foreach ($selectedCoachIds as $coachId) {
                $coachData = $this->academyModel->getCoachesByIds([$coachId], $id);
                if (!empty($coachData)) {
                    $coaches[] = $coachData[0];
                }
            }
            $data['selected_coaches'] = $coaches;
        } else {
            $savedCoachIds = $this->academyModel->getCoachChartSelection($id);
            if (!empty($savedCoachIds)) {
                $coaches = [];
                foreach ($savedCoachIds as $coachId) {
                    $coachData = $this->academyModel->getCoachesByIds([$coachId], $id);
                    if (!empty($coachData)) {
                        $coaches[] = $coachData[0];
                    }
                }
                $data['selected_coaches'] = $coaches;
            }
        }
        if ($debugMode) {
            $debugLog[] = 'selected_coaches count=' . count($data['selected_coaches']);
            header('Content-Type: text/html; charset=utf-8');
            echo '<h2>DEBUG export_player_chart (Academy)</h2>';
            echo '<pre style="white-space:pre-wrap;background:#f6f8fa;padding:12px;border:1px solid #ddd;border-radius:8px;">' . htmlspecialchars(implode("\n", $debugLog)) . '</pre>';
            echo '<h3>Selected Coaches</h3>';
            echo '<table border="1" cellpadding="6" cellspacing="0" style="border-collapse:collapse;width:100%;font-family:Arial,sans-serif;font-size:14px;">';
            echo '<tr style="background:#eef3f8;"><th>ID</th><th>coach_user_id</th><th>Name</th><th>image_path</th><th>line_picture</th><th>role_name</th><th>approval_status</th></tr>';
            foreach ($data['selected_coaches'] as $coach) {
                $coachName = trim(($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? ''));
                echo '<tr>';
                echo '<td>' . htmlspecialchars((string)($coach['id'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars((string)($coach['coach_user_id'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars($coachName) . '</td>';
                echo '<td>' . htmlspecialchars((string)($coach['image_path'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars((string)($coach['line_picture'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars((string)($coach['role_name_display'] ?? '')) . '</td>';
                echo '<td>' . htmlspecialchars((string)($coach['approval_status'] ?? '')) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
            echo '<h3>Selected Coaches Raw</h3>';
            echo '<pre style="white-space:pre-wrap;background:#f6f8fa;padding:12px;border:1px solid #ddd;border-radius:8px;">' . htmlspecialchars(json_encode($data['selected_coaches'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
            exit;
        }

        // Get jersey images for export
        $data['jerseys'] = $this->academyModel->getJerseys($id);

        $html = view('Modules\Academy\Views\export\player_chart.php', $data);
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'kanit',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 18,
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_bottom' => 25,
            'margin_header' => 1,
            'margin_footer' => 5,
            'orientation' => 'P',
            'tempDir' => WRITEPATH . 'cache',
        ]);

        $publicPath = FCPATH;
        foreach ([FCPATH . 'public/', FCPATH, dirname(FCPATH) . '/public/'] as $_c) {
            if (file_exists($_c . 'img/jootball_logo.png')) { $publicPath = $_c; break; }
        }
        $jootballLogo = $publicPath . 'img/jootball_logo.png';
        $jigsawLogo = $publicPath . 'img/jigsaw-logo-2020_0.png';
        $lineQr = $publicPath . 'img/line_qr.png';

        $headerText = '<table style="width: 100%;">
        <tr>
            <td style="text-align:center;width: 10%; vertical-align: middle;">
                <img src="' . $jootballLogo . '" width="35" alt="">&nbsp;
            </td>
            <td style="width:80%; text-align:center; vertical-align: middle;">
                <span style="font-size: 10px;">เอกสารฉบับนี้ถูกสร้างโดยแอพพลิเคชั่น Jootball โดยทุกทีมสามารถใช้ได้ฟรี | สนใจใช้งานได้ทาง Line ค้นหาคำว่า @511lucuy หรือสแกน Qr Code</span>
            </td>
            <td style="text-align:center;width: 10%; vertical-align: middle;">
                <img src="' . $jigsawLogo . '" width="35" alt="">&nbsp;
            </td>
        </tr>
    </table>';
        $mpdf->SetHTMLHeader($headerText);

        $footerText = '<table style="width: 100%;">
        <tr>
            <td style="text-align:center;width: 12%; vertical-align: middle;">
                <img src="' . $jootballLogo . '" width="40" alt="">&nbsp;
            </td>
            <td style="width:76%; text-align:center; vertical-align: middle;">
             <img width="40" src="' . $lineQr . '"><br/>
                <span style="font-size: 11px;">เอกสารฉบับนี้ถูกสร้างโดยแอพพลิเคชั่น Jootball โดยทุกทีมสามารถใช้ได้ฟรี<br>สนใจใช้งานได้ทาง Line ค้นหาคำว่า @511lucuy หรือสแกน Qr Code เพื่อใช้งาน</span>
            </td>
            <td style="text-align:center;width: 12%; vertical-align: middle;">
               <img src="' . $jigsawLogo . '" width="40" alt="">&nbsp;
            </td>
        </tr>
        </table>';
        $footer = array(
            'odd' => array(
                'C' => array(
                    'content' => $footerText,
                    'font-size' => 10,
                    'font-style' => 'I',
                    'font-family' => 'thsarabun',
                    'color' => '#000000'
                ),
                'line' => 0,
            ),
            'even' => array(
                'C' => array(
                    'content' => $footerText,
                    'font-size' => 10,
                    'font-style' => 'I',
                    'font-family' => 'thsarabun',
                    'color' => '#000000'
                ),
                'line' => 0,
            )
        );
        $fileName = str_replace('.', '', $data['team']['name'] ?? 'export') . '.pdf';
        $mpdf->SetFooter($footer);
        // Suppress mPDF "Undefined array key" warnings in table width calculation (known mPDF bug with image-only cells)
        $prevReporting = error_reporting(error_reporting() & ~E_WARNING);
        $mpdf->WriteHTML($html);
        error_reporting($prevReporting);
        $mpdf->Output('แผงรูป_' . $fileName, 'I');
    }

    /**
     * Download remote image and cache locally for PDF export.
     *
     * @param string $url
     * @param string $cacheDir
     * @return string|null Local file path or null on failure
     */
    private function cacheRemoteImage(string $url, string $cacheDir): ?string
    {
        $hash = md5($url);
        $ext = '.jpg';
        $cachedFile = $cacheDir . $hash . $ext;

        // Use cache if less than 24 hours old
        if (file_exists($cachedFile) && (time() - filemtime($cachedFile)) < 86400) {
            return $cachedFile;
        }

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0',
        ]);
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200 && !empty($imageData)) {
            file_put_contents($cachedFile, $imageData);
            return $cachedFile;
        }

        return null;
    }

    /**
     * Export player cards as PDF
     * GET /academy/export-player-card?id={team_id}
     */
    public function export_player_card()
    {
        $input = $this->request->getGet();

        if (!empty($input['id'])) {
            $id = $input['id'];
        } else {
            $id = session()->get("team.id");
        }

        // PDPA/Security: Check ownership or super admin
        $userAcademyId = getUserAcademyId();
        $teamInfo = getTeamAcademyInfo($id);
        $isTeamOwner = session()->get('team.id') == $id;
        $isAcademyOwner = ($teamInfo['academy_id'] ?? null) == $userAcademyId && $userAcademyId !== null;
        if (!$isTeamOwner && !$isAcademyOwner && !isSuperAdmin()) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        if (empty($id)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $data['team'] = $this->academyModel->getTeam($id);
        if (empty($data['team'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $data['input'] = $input;

        // Check if tour_id is provided to filter submitted players
        if (!empty($input['tour_id'])) {
            $tourId = $input['tour_id'];

            $tournament = $this->db->table('tournament')
                ->where('tour_id', $tourId)
                ->get()
                ->getRowArray();
            $data['tournament'] = $tournament;

            $tourTeam = $this->db->table('tournament_team')
                ->where('tour_id', $tourId)
                ->where('team_id', $id)
                ->get()
                ->getRowArray();

            if ($tourTeam) {
                $submittedPlayers = $this->db->table('tournament_submitted_players')
                    ->where('tour_team_id', $tourTeam['tour_team_id'])
                    ->get()
                    ->getResultArray();

                $selectedPlayerIds = array_column($submittedPlayers, 'player_id');

                if (!empty($selectedPlayerIds)) {
                    $allPlayers = $this->playerModel->get_PlayerList($id, [], '', false);
                    $data['players'] = array_filter($allPlayers, function ($player) use ($selectedPlayerIds) {
                        return in_array($player['id'], $selectedPlayerIds);
                    });
                } else {
                    $data['players'] = $this->playerModel->get_PlayerList($id, [5, 6, 7], '', false);
                }
            } else {
                $data['players'] = $this->playerModel->get_PlayerList($id, [5, 6, 7], '', false);
            }
        } else {
            $data['players'] = $this->playerModel->get_PlayerList($id, [5, 6, 7], '', false);
        }

        $html = view('Modules\Academy\Views\export\player_card.php', $data);
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'thsarabun',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 5,
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'orientation' => 'P',
        ]);

        $footerText = '<span style="font-size: 16px;">เอกสารฉบับนี้ถูกสร้างโดยแอพพลิเคชั่น Jootball โดยทุกทีมสามารถใช้ได้ฟรี<br>สนใจใช้งานได้ทาง Line ค้นหาคำว่า @511lucuy หรือสแกน Qr Code เพื่อใช้งาน</span>';
        $footer = array(
            'odd' => array(
                'C' => array(
                    'content' => $footerText,
                    'font-size' => 10,
                    'font-style' => 'I',
                    'font-family' => 'thsarabun',
                    'color' => '#000000'
                ),
                'line' => 0,
            ),
            'even' => array(
                'C' => array(
                    'content' => $footerText,
                    'font-size' => 10,
                    'font-style' => 'I',
                    'font-family' => 'thsarabun',
                    'color' => '#000000'
                ),
                'line' => 0,
            )
        );
        $mpdf->SetFooter($footer);
        $mpdf->WriteHTML($html);
        $mpdf->Output('', 'I');
    }
}
