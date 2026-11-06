<?php

namespace Modules\Team\Controllers\Traits;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\Team\Models\TeamModel;
use Modules\Player\Models\PlayerModel;
use \Hermawan\DataTables\DataTable;
use App\Libraries\Date_thai;
use App\Traits\JerseyManagementTrait;
use App\Traits\PlayerAjaxTrait;
use App\Traits\ChartSelectionTrait;
use Modules\Team\Controllers\Traits\TeamNotificationTrait;
use Modules\Team\Controllers\Traits\TeamSubmitCoachTrait;

trait TeamExportTrait
{
    public function export_player_document()
    {
        $input = $this->request->getGet();
        $id = !empty($input['id']) ? (int)$input['id'] : (int)session()->get("team.id");

        if (empty($id)) {
            return redirect()->to(base_url())->with('error', 'Team ID is required');
        }

        // PDPA/Security: Check ownership or academy duties or system admin
        helper('academy');
        $userAcademyId = getUserAcademyId();
        $teamInfo = getTeamAcademyInfo($id);
        
        $isAdmin = (bool) session()->get('isAdminLoggedIn');
        $isTeamOwner = session()->get('team.id') == $id;
        $isAcademyStaff = ($teamInfo['academy_id'] ?? null) == $userAcademyId && $userAcademyId !== null;
        
        if (!$isAdmin && !$isTeamOwner && !$isAcademyStaff && !isSuperAdmin()) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $data['team'] = $this->teamModel->get_Teams($id);
        if (empty($data['team'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $data['input'] = $input;

        // tour_id ใช้สำหรับชื่อเอกสาร (title) + Age Group + สีชุด เท่านั้น (ไม่กรองรายชื่อ)
        if (!empty($input['tour_id'])) {
            $tournament = $this->db->table('tournament')
                ->where('tour_id', $input['tour_id'])
                ->get()
                ->getRowArray();
            if ($tournament) {
                $data['input']['title'] = $tournament['name'];
                $data['tournament'] = $tournament;
            }
        }

        // ดึงรายชื่อนักเตะแบบเดียวกับหน้า player-chart (academy/player-chart/{academy}/{team})
        // = roster ของทีมจาก academy_player_enrollments (team_id + is_active=1) ไม่ผูกกับทัวร์นาเมนต์
        $data['players'] = $this->db->table('player')
            ->select("player.*,
                      COALESCE(academy_player_enrollments.shirt_number, player.shirt_number) as shirt_number,
                      academy_player_enrollments.position_id as player_position_id")
            ->join('academy_player_enrollments',
                'academy_player_enrollments.player_id = player.id AND academy_player_enrollments.is_active = 1',
                'inner')
            ->where('academy_player_enrollments.team_id', (int)$id)
            ->groupBy('player.id')
            ->orderBy('CAST(COALESCE(academy_player_enrollments.shirt_number, player.shirt_number) AS UNSIGNED) ASC', '', false)
            ->get()->getResultArray();
        // ดึงสีชุด (hex) ของทีมในทัวร์นาเมนต์นี้จาก tournament_team
        // (ชุดผูกกับทัวร์นาเมนต์ แก้ผ่านหน้า academy/player-chart — 1 แถวต่อ tour_id+team_id)
        $data['jersey'] = [];
        if (!empty($input['tour_id'])) {
            $data['jersey'] = $this->db->table('tournament_team')
                ->select('home_shirt, home_trousers, home_socks,
                          home_goal_shirt, home_goal_trousers, home_goal_socks,
                          away_shirt, away_trousers, away_socks,
                          away_goal_shirt, away_goal_trousers, away_goal_socks')
                ->where('tour_id', (int)$input['tour_id'])
                ->where('team_id', (int)$id)
                ->limit(1)
                ->get()->getRowArray() ?: [];
        }

        // เลือก view ตามชื่อทัวร์ — ถ้ามีคำว่า GLO ใช้ template GLO
        $isGlo = !empty($data['tournament']['name']) && stripos($data['tournament']['name'], 'GLO') !== false;
        $viewFile = $isGlo
            ? 'Modules\Team\Views\export\player_document_glo.php'
            : 'Modules\Team\Views\export\player_document_1.php';
        $html = view($viewFile, $data);
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'thsarabun',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => $isGlo ? 5 : 30,
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_bottom' => $isGlo ? 25 : 15,
            'margin_header' => $isGlo ? 1 : 1,
            'margin_footer' => 5,
            'orientation' => 'P', // L แนวนอน P แนวตั้ง
        ]);
        $headerText = '<table style="width: 100%;">
        <tr>
            <td style="text-align:center;width: 15%;">
                <img src="' . base_url("public/img/jootball_logo.png") . '" width="70" alt=""><br>
            </td>
            <td style="width:70%; text-align:center;">
                  <span style="font-size: 18px;">เอกสารฉบับนี้ถูกสร้างโดยแอพพลิเคชั่น Jootball โดยทุกทีมสามารถใช้ได้ฟรี<br>สนใจใช้งานได้ทาง Line ค้นหาคำว่า @511lucuy หรือสแกน Qr Code เพื่อใช้งาน</span>
            </td>
            <td style="text-align:center;width: 15%;">
                <img src="' . base_url("public/img/jigsaw-logo-2020_0.png") . '" width="70" alt="">
            </td>
        </tr>
    </table>';
        // GLO template ไม่ใช้ header ด้านบน (โลโก้ Jootball/Jigsaw)
        if (!$isGlo) {
            $mpdf->SetHTMLHeader($headerText);
        }

        $footerText = '<table style="width: 100%;">
        <tr>
            <td style="text-align:center;width: 15%;">
                <img src="' . base_url("public/img/jootball_logo.png") . '" width="40" alt="" style=""><br>
            </td>
            <td style="width:70%">
             <img width="40" src="https://qr-official.line.me/gs/M_511ulcuy_BW.png?oat_content=qr">
             <br/>
                <span style="font-size: 9px;">เอกสารฉบับนี้ถูกสร้างโดยแอพพลิเคชั่น Jootball โดยทุกทีมสามารถใช้ได้ฟรี<br>สนใจใช้งานได้ทาง Line ค้นหาคำว่า @511lucuy หรือสแกน Qr Code เพื่อใช้งาน</span>
            </td>
             <td style="text-align:center;width: 15%;">
               <img src="' . base_url("public/img/jigsaw-logo-2020_0.png") . '" width="40" alt="" style="">
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
        $fileName = str_replace('.', '', $data['team']['name']) . '.pdf';
        $mpdf->SetFooter($footer);
        $mpdf->WriteHTML($html);
        $mpdf->Output('รายชื่อ_' . $fileName, 'I');
    }



    public function export_player_chart()
    {
        // เพิ่ม time limit และ memory สำหรับ PDF generation
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        $input = $this->request->getGet();
        $id = !empty($input['id']) ? (int)$input['id'] : (int)session()->get("team.id");

        if (empty($id)) {
            return redirect()->to(base_url())->with('error', 'Team ID is required');
        }

        // PDPA/Security: Check ownership or academy duties or system admin
        helper('academy');
        $userAcademyId = getUserAcademyId();
        $teamInfo = getTeamAcademyInfo($id);
        
        $isAdmin = (bool) session()->get('isAdminLoggedIn');
        $isTeamOwner = session()->get('team.id') == $id;
        $isAcademyStaff = ($teamInfo['academy_id'] ?? null) == $userAcademyId && $userAcademyId !== null;
        
        if (!$isAdmin && !$isTeamOwner && !$isAcademyStaff && !isSuperAdmin()) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $debugMode = !empty($input['debug']);
        $debugLog = [];

        $data['team'] = $this->teamModel->get_Teams($id);
        if (empty($data['team'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if ($debugMode) {
            $debugLog[] = "team_id: {$id}";
            $debugLog[] = "team data: " . ($data['team'] ? $data['team']['name'] : 'NULL');
            $debugLog[] = "input: " . json_encode($input);
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
                ->where('player.is_active', 1)
                ->orderBy('CAST(ape_exp.shirt_number AS unsigned)', 'ASC')
                ->get()
                ->getResultArray();
        } elseif (!empty($input['tour_id'])) {
            // Get submitted players for this team in the specified tournament (raw SQL)
            $tourId = $input['tour_id'];
            if ($debugMode) { $debugLog[] = "BRANCH: tour_id={$tourId}"; }

            $sql = "SELECT p.id, p.first_name, p.last_name, p.nick_name, p.birth_date,
                           COALESCE(NULLIF(pp.parent_id,0), pp.id) as player_position_id, pt.shirt_number, pt.sub_position_ids as player_sub_position_id,
                           p.image_path, p.id_card, p.id_card_image,
                           cpp.name_th as position_name_th, cpp.name_en as position_name_en
                    FROM tournament_team tt
                    INNER JOIN tournament_submitted_players tsp ON tsp.tour_team_id = tt.tour_team_id
                    INNER JOIN player p ON p.id = tsp.player_id
                    LEFT JOIN academy_player_enrollments pt ON pt.player_id = p.id AND pt.team_id = tt.team_id AND pt.is_active = 1
                    LEFT JOIN tbl_player_position pp ON pp.id = pt.position_id
                    LEFT JOIN tbl_player_position cpp ON cpp.id = COALESCE(NULLIF(pp.parent_id,0), pp.id)
                    WHERE tt.tour_id = ? AND tt.team_id = ?
                    ORDER BY CAST(pt.shirt_number AS unsigned) ASC";

            $filteredPlayers = $this->db->query($sql, [$tourId, $id])->getResultArray();

            if ($debugMode) {
                $debugLog[] = "SQL submitted players count: " . count($filteredPlayers);
                $debugLog[] = "SQL: tour_id={$tourId}, team_id={$id}";
            }

            // Fallback: ถ้าไม่มี submitted players ให้ดึงนักเตะทั้งหมดของทีม (ยกเว้น staff)
            // ใช้ raw SQL รองรับ parent_team_id + NULL position_id
            if (empty($filteredPlayers)) {
                $teamInfo = $this->db->table('team')->select('id, parent_team_id')->where('id', $id)->get()->getRowArray();
                $teamIds = [$id];
                if (!empty($teamInfo['parent_team_id'])) {
                    $teamIds[] = $teamInfo['parent_team_id'];
                }
                $placeholders = implode(',', array_fill(0, count($teamIds), '?'));

                $fallbackSql = "SELECT p.id, p.first_name, p.last_name, p.nick_name, p.birth_date,
                                       COALESCE(NULLIF(pp.parent_id,0), pp.id) as player_position_id, pt.shirt_number, pt.sub_position_ids as player_sub_position_id,
                                       p.image_path, p.id_card, p.id_card_image,
                                       cpp.name_th as position_name_th, cpp.name_en as position_name_en
                                FROM player p
                                INNER JOIN academy_player_enrollments pt ON pt.player_id = p.id AND pt.is_active = 1
                                LEFT JOIN tbl_player_position pp ON pp.id = pt.position_id
                                LEFT JOIN tbl_player_position cpp ON cpp.id = COALESCE(NULLIF(pp.parent_id,0), pp.id)
                                WHERE pt.team_id IN ({$placeholders})
                                  AND p.is_active = 1
                                  AND (pt.position_id IS NULL OR COALESCE(NULLIF(pp.parent_id,0), pp.id) NOT IN (5,6,7))
                                GROUP BY p.id
                                ORDER BY CAST(pt.shirt_number AS unsigned) ASC";

                $filteredPlayers = $this->db->query($fallbackSql, $teamIds)->getResultArray();
                if ($debugMode) { $debugLog[] = "filteredPlayers (fallback SQL) count: " . count($filteredPlayers) . " teamIds: " . json_encode($teamIds); }
            }
        } else {
            // If no players selected, get all players with position filter (exclude staff)
            // ใช้ raw SQL รองรับ parent_team_id + NULL position_id
            $teamInfo = $this->db->table('team')->select('id, parent_team_id')->where('id', $id)->get()->getRowArray();
            $teamIds = [$id];
            if (!empty($teamInfo['parent_team_id'])) {
                $teamIds[] = $teamInfo['parent_team_id'];
            }
            $placeholders = implode(',', array_fill(0, count($teamIds), '?'));

            $fallbackSql = "SELECT p.id, p.first_name, p.last_name, p.nick_name, p.birth_date,
                                   COALESCE(NULLIF(pp.parent_id,0), pp.id) as player_position_id, pt.shirt_number, pt.sub_position_ids as player_sub_position_id,
                                   p.image_path, p.id_card, p.id_card_image,
                                   cpp.name_th as position_name_th, cpp.name_en as position_name_en
                            FROM player p
                            INNER JOIN academy_player_enrollments pt ON pt.player_id = p.id AND pt.is_active = 1
                            LEFT JOIN tbl_player_position pp ON pp.id = pt.position_id
                            LEFT JOIN tbl_player_position cpp ON cpp.id = COALESCE(NULLIF(pp.parent_id,0), pp.id)
                            WHERE pt.team_id IN ({$placeholders})
                              AND p.is_active = 1
                              AND (pt.position_id IS NULL OR COALESCE(NULLIF(pp.parent_id,0), pp.id) NOT IN (5,6,7))
                            GROUP BY p.id
                            ORDER BY CAST(pt.shirt_number AS unsigned) ASC";

            $filteredPlayers = $this->db->query($fallbackSql, $teamIds)->getResultArray();
            if ($debugMode) { $debugLog[] = "BRANCH: no tour_id, no players - fallback SQL, count: " . count($filteredPlayers); }
        }

        // Add player_sub_position field to each player (same logic as PlayerModel)
        $positions = $this->db->table('tbl_player_position')->get()->getResultArray();
        $positionMap = [];
        foreach ($positions as $pos) {
            $positionMap[$pos['id']] = $pos['name_en'];
        }

        $data['players'] = [];
        foreach ($filteredPlayers as $player) {
            $positionNames = [];
            $subPositions = [];

            // Decode JSON or handle comma-separated string
            if (!empty($player['player_sub_position_id'])) {
                // Decode HTML entities first (if present)
                $decoded_html = html_entity_decode($player['player_sub_position_id']);
                $decoded = json_decode($decoded_html, true);

                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $subPositions = array_map('intval', $decoded); // Convert to integer
                } else {
                    $subPositions = array_map('intval', array_filter(explode(',', $decoded_html))); // Convert to integer
                }
            }

            // Map IDs to names (English names)
            foreach ($subPositions as $posId) {
                if (isset($positionMap[$posId])) {
                    $positionNames[] = $positionMap[$posId];
                }
            }

            $player['player_sub_position'] = !empty($positionNames) ? implode(', ', $positionNames) : '-';
            $data['players'][] = $player;
        }

        // Handle selected coaches for export (pass team_id to get approval status)
        $data['selected_coaches'] = [];
        if (!empty($input['coaches'])) {
            // กรณีมี coaches param (จาก player_chart.php form)
            $selectedCoachIds = array_map('intval', explode(',', $input['coaches']));
            // แก้ไข: ดึงข้อมูลโค้ชแต่ละคนแยกกันเพื่อรองรับโค้ชซ้ำ (คนเดียวกันอยู่คนละรุ่น)
            $coaches = [];
            foreach ($selectedCoachIds as $coachId) {
                $coachData = $this->teamModel->getCoachesByIds([$coachId], $id);
                if (!empty($coachData)) {
                    $coaches[] = $coachData[0];
                }
            }
            $data['selected_coaches'] = $coaches;
        } else {
            // กรณีไม่มี coaches param → ดึงจาก team_player_chart_selection (draft)
            $savedCoachIds = $this->teamModel->getCoachChartSelection($id);
            if (!empty($savedCoachIds)) {
                // แก้ไข: ดึงข้อมูลโค้ชแต่ละคนแยกกันเพื่อรองรับโค้ชซ้ำ (คนเดียวกันอยู่คนละรุ่น)
                $coaches = [];
                foreach ($savedCoachIds as $coachId) {
                    $coachData = $this->teamModel->getCoachesByIds([$coachId], $id);
                    if (!empty($coachData)) {
                        $coaches[] = $coachData[0];
                    }
                }
                $data['selected_coaches'] = $coaches;
            }
        }

        // Get jersey images for export
        $data['jerseys'] = $this->teamModel->getJerseys($id);

        // $data['staffs'] = $this->playerModel->get_PlayerList($id, [1, 2, 3, 4], 'player.player_position_id ASC');

        // Debug mode: แสดง debug info แทน PDF
        if ($debugMode) {
            $debugLog[] = "final players count: " . count($data['players']);
            $debugLog[] = "selected_coaches count: " . count($data['selected_coaches']);
            $debugLog[] = "jerseys: " . json_encode($data['jerseys']);
            header('Content-Type: text/html; charset=utf-8');
            echo '<h2>DEBUG export_player_chart</h2><pre>' . htmlspecialchars(implode("\n", $debugLog)) . '</pre>';
            echo '<h3>Players Data</h3><pre>' . htmlspecialchars(json_encode($data['players'], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) . '</pre>';
            die();
        }

        $html = view('Modules\Team\Views\export\player_chart.php', $data);
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'kanit',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 18,
            'margin_left' => 5,
            'margin_right' => 5,
            'margin_bottom' => 25, // เว้นที่ให้ footer ไม่ทับเนื้อหา
            'margin_header' => 1,
            'margin_footer' => 5,
            'orientation' => 'P',
            'tempDir' => WRITEPATH . 'cache',
        ]);

        // Detect public path (รองรับทั้ง root index.php และ public/index.php)
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
                <img src="' . $jootballLogo . '" width="35" alt="">
            </td>
            <td style="width:80%; text-align:center; vertical-align: middle;">
                <span style="font-size: 10px;">เอกสารฉบับนี้ถูกสร้างโดยแอพพลิเคชั่น Jootball โดยทุกทีมสามารถใช้ได้ฟรี | สนใจใช้งานได้ทาง Line ค้นหาคำว่า @511lucuy หรือสแกน Qr Code</span>
            </td>
            <td style="text-align:center;width: 10%; vertical-align: middle;">
                <img src="' . $jigsawLogo . '" width="35" alt="">
            </td>
        </tr>
    </table>';
        $mpdf->SetHTMLHeader($headerText);

        $footerText = '<table style="width: 100%;">
        <tr>
            <td style="text-align:center;width: 12%; vertical-align: middle;">
                <img src="' . $jootballLogo . '" width="40" alt="">
            </td>
            <td style="width:76%; text-align:center; vertical-align: middle;">
             <img width="40" src="' . $lineQr . '"><br/>
                <span style="font-size: 11px;">เอกสารฉบับนี้ถูกสร้างโดยแอพพลิเคชั่น Jootball โดยทุกทีมสามารถใช้ได้ฟรี<br>สนใจใช้งานได้ทาง Line ค้นหาคำว่า @511lucuy หรือสแกน Qr Code เพื่อใช้งาน</span>
            </td>
            <td style="text-align:center;width: 12%; vertical-align: middle;">
               <img src="' . $jigsawLogo . '" width="40" alt="">
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
        $fileName = str_replace('.', '', $data['team']['name']) . '.pdf';
        $mpdf->SetFooter($footer);
        // Suppress mPDF "Undefined array key" warnings in table width calculation (known mPDF bug with image-only cells)
        $prevReporting = error_reporting(error_reporting() & ~E_WARNING);
        $mpdf->WriteHTML($html);
        error_reporting($prevReporting);
        $mpdf->Output('แผงรูป_' . $fileName, 'I');
    }

    /**
     * ดาวน์โหลดรูปจาก URL แล้วเก็บ cache ใน local directory
     * ใช้สำหรับ PDF export เพื่อไม่ให้ mPDF ต้องโหลดรูปผ่าน HTTP ทีละรูป (ช้ามาก)
     */
    private function cacheRemoteImage(string $url, string $cacheDir): ?string
    {
        if (!is_dir($cacheDir)) {
            @mkdir($cacheDir, 0777, true);
        }

        // สร้างชื่อไฟล์จาก URL hash
        $hash = md5($url);
        $ext = '.jpg';
        $cachedFile = $cacheDir . $hash . $ext;

        // ถ้ามี cache แล้ว (ไม่เกิน 24 ชม.) ใช้เลย
        if (file_exists($cachedFile) && (time() - filemtime($cachedFile)) < 86400) {
            return $cachedFile;
        }

        // ดาวน์โหลดด้วย cURL (timeout 5 วินาที เพื่อไม่ให้ค้างนาน)
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

        return null; // ดาวน์โหลดไม่สำเร็จ ให้ใช้ default image แทน
    }

    /**
     * Resolve coach photo for card exports.
     * Coach records use image_path; line_picture may be a remote LINE URL.
     */
    private function resolveCoachCardImagePath(array $coach): string
    {
        $candidates = [];

        foreach (['image_path', 'line_picture'] as $field) {
            if (!empty($coach[$field])) {
                $candidates[] = $coach[$field];
            }
        }

        $coachUserId = (int)($coach['coach_user_id'] ?? 0);
        if ($coachUserId > 0) {
            try {
                $fallbackRows = $this->db->table('coach')
                    ->select('coach.image_path, coach_users.line_picture')
                    ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
                    ->where('coach.coach_user_id', $coachUserId)
                    ->where('coach.is_active', 1)
                    ->orderBy('(CASE WHEN coach.image_path IS NOT NULL AND coach.image_path != "" THEN 1 ELSE 0 END)', 'DESC', false)
                    ->orderBy('(CASE WHEN coach_users.line_picture IS NOT NULL AND coach_users.line_picture != "" THEN 1 ELSE 0 END)', 'DESC', false)
                    ->orderBy('coach.id', 'DESC')
                    ->get()
                    ->getResultArray();

                foreach ($fallbackRows as $row) {
                    foreach (['image_path', 'line_picture'] as $field) {
                        if (!empty($row[$field])) {
                            $candidates[] = $row[$field];
                        }
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', '[resolveCoachCardImagePath] ' . $e->getMessage());
            }
        }

        $cacheDir = WRITEPATH . 'cache/remote_images/';
        foreach (array_values(array_unique(array_filter($candidates))) as $candidate) {
            if (filter_var($candidate, FILTER_VALIDATE_URL)) {
                $cached = $this->cacheRemoteImage($candidate, $cacheDir);
                if ($cached && is_file($cached)) {
                    return $cached;
                }
                continue;
            }

            return $candidate;
        }

        return '';
    }

    public function export_player_card()
    {
        $input = $this->request->getGet();
        $id = !empty($input['id']) ? (int)$input['id'] : (int)session()->get("team.id");

        if (empty($id)) {
            return redirect()->to(base_url())->with('error', 'Team ID is required');
        }

        // PDPA/Security: Check ownership or academy duties or system admin
        helper('academy');
        $userAcademyId = getUserAcademyId();
        $teamInfo = getTeamAcademyInfo($id);
        
        $isAdmin = (bool) session()->get('isAdminLoggedIn');
        $isTeamOwner = session()->get('team.id') == $id;
        $isAcademyStaff = ($teamInfo['academy_id'] ?? null) == $userAcademyId && $userAcademyId !== null;
        
        if (!$isAdmin && !$isTeamOwner && !$isAcademyStaff && !isSuperAdmin()) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $debug = !empty($input['debug']);
        $debugInfo = [];

        $data['team'] = $this->teamModel->get_Teams($id);
        if (empty($data['team'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $data['input'] = $input;

        if ($debug) {
            $debugInfo['team_id'] = $id;
            $debugInfo['team_found'] = !empty($data['team']);
            $debugInfo['team_name'] = $data['team']['name'] ?? null;
            $debugInfo['parent_team_id'] = $data['team']['parent_team_id'] ?? null;
        }

        // Check if tour_id is provided to filter submitted players
        if (!empty($input['tour_id'])) {
            $tourId = $input['tour_id'];

            // Get tournament data
            $tournament = $this->db->table('tournament')
                ->where('tour_id', $tourId)
                ->get()
                ->getRowArray();
            $data['tournament'] = $tournament;

            // Check for custom ID card template (player)
            $playerTemplate = $this->db->table('tournament_id_card')
                ->where('tour_id', $tourId)
                ->where('card_type', 'player')
                ->where('is_active', 1)
                ->where('deleted_at', null)
                ->get()
                ->getRowArray();
            if ($playerTemplate) {
                $data['id_card_template'] = $playerTemplate;
            }

            if ($debug) {
                $debugInfo['tour_id'] = $tourId;
                $debugInfo['tournament_found'] = !empty($tournament);
                $debugInfo['custom_template_found'] = !empty($playerTemplate);
                $debugInfo['custom_template_id'] = $playerTemplate['id'] ?? null;
            }

            // Get tour_team_id
            $tourTeam = $this->db->table('tournament_team')
                ->where('tour_id', $tourId)
                ->where('team_id', $id)
                ->get()
                ->getRowArray();
            
            if ($debug) {
                $debugInfo['tour_team_found'] = !empty($tourTeam);
                $debugInfo['tour_team_id'] = $tourTeam['tour_team_id'] ?? null;
                $debugInfo['register_status'] = $tourTeam['register_status'] ?? null;
            }

            if ($tourTeam) {
                // Get submitted player IDs
                $submittedPlayers = $this->db->table('tournament_submitted_players')
                    ->where('tour_team_id', $tourTeam['tour_team_id'])
                    ->get()
                    ->getResultArray();

                $selectedPlayerIds = array_column($submittedPlayers, 'player_id');
                //die(print_r($tourTeam['tour_team_id']));

                if ($debug) {
                    $debugInfo['submitted_players_count'] = count($submittedPlayers);
                    $debugInfo['submitted_player_ids'] = $selectedPlayerIds;
                }

                if (!empty($selectedPlayerIds)) {
                    // Query players directly by IDs (ไม่ต้องผ่าน player_teams เพราะนักเตะอาจย้ายทีมแล้ว)
                    $data['players'] = $this->db->table('player')
                        ->select('player.id, player.first_name, player.last_name, player.nick_name, player.birth_date, COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id, ape_exp.shirt_number as shirt_number, ape_exp.sub_position_ids as player_sub_position_id, player.image_path, player.id_card, player.id_card_image, coarse_exp2.name_th as position_name_th, coarse_exp2.name_en as position_name_en')
                        ->join('academy_player_enrollments ape_exp', 'ape_exp.player_id = player.id AND ape_exp.team_id = ' . (int)$id . ' AND ape_exp.is_active = 1', 'LEFT')
                        ->join('tbl_player_position', 'tbl_player_position.id = ape_exp.position_id', 'LEFT')
                        ->join('tbl_player_position coarse_exp2', 'coarse_exp2.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)', 'LEFT')
                        ->whereIn('player.id', $selectedPlayerIds)
                        ->orderBy('CAST(ape_exp.shirt_number AS unsigned)', 'ASC')
                        ->get()
                        ->getResultArray();
                    if ($debug) $debugInfo['players_source'] = 'submitted_players (direct query)';
                } else {
                    $data['players'] = $this->getTeamPlayersForCard($id);
                    if ($debug) $debugInfo['players_source'] = 'fallback direct player query (no submitted)';
                }
            } else {
                $data['players'] = $this->getTeamPlayersForCard($id);
                if ($debug) $debugInfo['players_source'] = 'fallback direct player query (no tour_team)';
            }
        } else {
            $data['players'] = $this->getTeamPlayersForCard($id);
            if ($debug) $debugInfo['players_source'] = 'no tour_id provided';
        }


        if ($debug) {
            $debugInfo['final_players_count'] = is_array($data['players']) ? count($data['players']) : 0;
            $debugInfo['player_teams_row_count'] = $this->db->table('academy_player_enrollments')
                ->where('team_id', $id)
                ->where('is_active', 1)
                ->countAllResults();
            $debugInfo['player_table_row_count'] = $this->db->table('player')
                ->where('team_id', $id)
                ->where('is_active', 1)
                ->countAllResults();
            if (!empty($data['players'])) {
                $debugInfo['first_player'] = $data['players'][0];
            }
            $this->response->setHeader('Content-Type', 'application/json; charset=utf-8');
            return $this->response->setBody(json_encode($debugInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        }
        // $data['staffs'] = $this->playerModel->get_PlayerList($id, [5, 6, 7], 'player.player_position_id ASC');

        if (empty($data["players"])) { return "ไม่พบข้อมูลนักเตะสำหรับทีมนี้ในรายการที่ระบุ กรุณาตรวจสอบการส่งรายชื่อนักเตะ"; }        // Use custom player template if available
        if (!empty($data['id_card_template'])) {
            $this->response->setHeader('Content-Type', 'application/pdf');
            $mpdf = new \Mpdf\Mpdf([
                'default_font' => 'garuda',
                'default_font_size' => 14,
                'mode' => 'utf-8',
                'format' => 'A4',
                'margin_top' => 0,
                'margin_left' => 0,
                'margin_right' => 0,
                'margin_header' => 0,
                'margin_footer' => 0,
                'orientation' => 'P',
            ]);

            $id_card_template = $data['id_card_template'];
            $team = $data['team'];
            $tournament = $data['tournament'] ?? [];
            $players = $data['players'];

            include ROOTPATH . 'modules/Team/Views/export/player_card_template.php';

            $mpdf->Output('player_cards.pdf', 'I');
            return;
        }

        $html = view('Modules\Team\Views\export\player_card.php', $data);
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
        // $mpdf->SetWatermarkImage(
        //     base_url('public/img/jootball_logo.png'),
        //     0.1,
        //     '',
        //     [0, 50]
        // );
        // $mpdf->showWatermarkImage = true;
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

    /**
     * Direct player query for ID card export — bypasses player_teams INNER JOIN
     * which can return empty when team has only player.team_id rows but no player_teams entries,
     * or when whereNotIn position filter eliminates all rows.
     * Excludes staff positions [5, 6, 7] (manager/coach/staff).
     */
    private function getTeamPlayersForCard($teamId)
    {
        $select = 'player.id, player.first_name, player.last_name, player.nick_name, player.birth_date, COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id, ape_exp.shirt_number as shirt_number, ape_exp.sub_position_ids as player_sub_position_id, player.image_path, player.id_card, player.id_card_image, coarse_exp3.name_th as position_name_th, coarse_exp3.name_en as position_name_en';

        // Collect player IDs from both player.team_id and player_teams (union)
        $idsFromPlayer = $this->db->table('player')
            ->select('id')
            ->where('team_id', $teamId)
            ->where('is_active', 1)
            ->get()->getResultArray();
        $idsFromPivot = $this->db->table('academy_player_enrollments')
            ->select('player_id')
            ->where('team_id', $teamId)
            ->where('is_active', 1)
            ->get()->getResultArray();

        $playerIds = array_unique(array_merge(
            array_column($idsFromPlayer, 'id'),
            array_column($idsFromPivot, 'player_id')
        ));

        //die(print_r($playerIds));

        if (empty($playerIds)) {
            return [];
        }


        return $this->db->table('player')
            ->select($select)
            ->join('academy_player_enrollments ape_exp', "ape_exp.player_id = player.id AND ape_exp.team_id = " . (int) $teamId . " AND ape_exp.is_active = 1", 'LEFT')
            ->join('tbl_player_position', 'tbl_player_position.id = ape_exp.position_id', 'LEFT')
            ->join('tbl_player_position coarse_exp3', 'coarse_exp3.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)', 'LEFT')
            ->whereIn('player.id', $playerIds)
            ->where("COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) NOT IN (" . implode(',', array_map('intval', [5, 6, 7])) . ")", null, false)
            ->orderBy('CAST(ape_exp.shirt_number AS unsigned)', 'ASC')
            ->get()
            ->getResultArray();
    }

    public function export_manager_card()
    {
        $input = $this->request->getGet();
        $id = !empty($input['id']) ? (int)$input['id'] : (int)session()->get("team.id");

        if (empty($id)) {
            return redirect()->to(base_url())->with('error', 'Team ID is required');
        }

        // PDPA/Security: Check ownership or academy duties or system admin
        helper('academy');
        $userAcademyId = getUserAcademyId();
        $teamInfo = getTeamAcademyInfo($id);
        
        $isAdmin = (bool) session()->get('isAdminLoggedIn');
        $isTeamOwner = session()->get('team.id') == $id;
        $isAcademyStaff = ($teamInfo['academy_id'] ?? null) == $userAcademyId && $userAcademyId !== null;
        
        if (!$isAdmin && !$isTeamOwner && !$isAcademyStaff && !isSuperAdmin()) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $data['team'] = $this->teamModel->get_Teams($id);
        if (empty($data['team'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }
        $data['input'] = $input;

        if (empty($input['tour_id'])) {
            echo 'tour_id is required';
            return;
        }

        $tourId = $input['tour_id'];

        // Get tournament data
        $data['tournament'] = $this->db->table('tournament')
            ->where('tour_id', $tourId)
            ->get()
            ->getRowArray();

        // Get manager ID card template
        $managerTemplate = $this->db->table('tournament_id_card')
            ->where('tour_id', $tourId)
            ->where('card_type', 'manager')
            ->where('is_active', 1)
            ->where('deleted_at', null)
            ->get()
            ->getRowArray();

        // Fallback to default template values if no custom template exists
        if (!$managerTemplate) {
            $managerTemplate = $this->getDefaultIdCardTemplate('manager');
        }

        // Get coaches for this team
        $savedCoachIds = $this->teamModel->getCoachChartSelection($id);
        $coaches = [];
        if (!empty($savedCoachIds)) {
            foreach ($savedCoachIds as $coachId) {
                $coachData = $this->teamModel->getCoachesByIds([$coachId], $id);
                if (!empty($coachData)) {
                    $coaches[] = $coachData[0];
                }
            }
        }

        // Convert coaches to players format for the template
        $managerPlayers = [];
        foreach ($coaches as $coach) {
            $coachImg = $this->resolveCoachCardImagePath($coach);
            $managerPlayers[] = [
                'id' => $coach['id'],
                'first_name' => $coach['first_name'] ?? '',
                'last_name' => $coach['last_name'] ?? '',
                'shirt_number' => '-',
                'position_name' => $coach['role_name'] ?? 'ผู้ฝึกสอน',
                'image_path' => $coachImg,
            ];
        }

        // Render PDF
        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'garuda',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'orientation' => 'P',
        ]);

        $id_card_template = $managerTemplate;
        $team = $data['team'];
        $tournament = $data['tournament'] ?? [];
        $players = $managerPlayers;

        include ROOTPATH . 'modules/Team/Views/export/player_card_template.php';

        $mpdf->Output('manager_cards.pdf', 'I');
    }
}
