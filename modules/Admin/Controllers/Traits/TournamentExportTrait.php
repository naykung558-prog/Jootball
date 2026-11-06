<?php

namespace Modules\Admin\Controllers\Traits;

use App\Libraries\MatchScheduler;
use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\TeamModel;
use Modules\Admin\Models\TournamentModel;
use Modules\Player\Models\PlayerModel;
use Modules\Admin\Models\PlayerStateModel;
use App\Models\Main_Model;
use Modules\Admin\Controllers\Traits\TournamentScoringTrait;

trait TournamentExportTrait
{
    public function export_players($tour_id)
    {
        $tour_id = (int) $tour_id;
        $tournament = $this->tournamentModel->get_Tournament($tour_id);

        if (!$tournament) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลทัวร์นาเมนต์');
        }

        // Security Check: System Admin OR Academy Owner/Staff
        $isAdmin = (bool) session()->get('isAdminLoggedIn');
        $isLoggedIn = (bool) session()->get('isLoggedIn');
        $userAcademyId = getUserAcademyId(); // From academy_helper, checks academy_members

        if (!$isAdmin && !$isLoggedIn) {
            return redirect()->to(base_url('admin/login'))->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }

        // If not system admin, check if the user belongs to an academy involved in this tournament
        if (!$isAdmin && !isSuperAdmin()) {
            if (!$userAcademyId) {
                return redirect()->to(base_url())->with('error', 'Unauthorized access');
            }
            // (Optional) We could further restrict to only show players from their academy
            // but for now, we follow the "User with duties" requirement.
        }

        $players = $this->tournamentPlayerRows($tour_id);

        return view('Modules\Admin\Views\Tournament\export\players_table.php', [
            'tour_id' => $tour_id,
            'tournament' => $tournament,
            'players' => $players,
        ]);
    }

    /**
     * รายชื่อนักกีฬาทั้งหมดที่ถูกส่งเข้าทัวร์นาเมนต์ (ทุกทีม)
     * ใช้ร่วมกันระหว่างหน้าตารางบนจอกับไฟล์ Excel
     */
    private function tournamentPlayerRows(int $tour_id): array
    {
        $sql = "
            SELECT
                ts.player_id,
                t.`name` AS team_name,
                tt.register_status,
                CASE
                    WHEN tt.register_status = 1 THEN 'รอพิจารณา'
                    WHEN tt.register_status = 2 THEN 'สำเร็จ'
                    WHEN tt.register_status = 3 THEN 'ปฏิเสธ'
                    ELSE 'ไม่ทราบสถานะ'
                END AS register_status_text,
                p.first_name,
                p.last_name,
                p.nick_name,
                COALESCE(ap.shirt_number, p.shirt_number) AS shirt_number,
                tpp.name_th AS position_name_th,
                tpp.name_en AS position_name,
                p.birth_date,
                tpro.province_name,
                tdis.district_name,
                tsub.subdistrict_name
            FROM tournament_submitted_players AS ts
            LEFT JOIN tournament_team AS tt
                ON ts.tour_team_id = tt.tour_team_id
            LEFT JOIN player AS p
                ON ts.player_id = p.id
            LEFT JOIN academy_player_enrollments AS ap
                ON ap.team_id = tt.team_id
                AND ap.player_id = ts.player_id
                AND ap.is_active = 1
            LEFT JOIN tbl_player_position AS tpp
                ON tpp.id = COALESCE(NULLIF(ap.position_id,0), p.player_position_id)
            LEFT JOIN team AS t
                ON t.id = tt.team_id
            LEFT JOIN tbl_provinces AS tpro
                ON p.province_id = tpro.province_id
            LEFT JOIN tbl_districts AS tdis
                ON p.amphure_id = tdis.district_id
            LEFT JOIN tbl_subdistricts AS tsub
                ON p.tambon_id = tsub.subdistrict_id
            WHERE tt.tour_id = ?
            ORDER BY t.`name` ASC, CAST(COALESCE(ap.shirt_number, p.shirt_number) AS UNSIGNED) ASC, p.first_name ASC
        ";

        return $this->db->query($sql, [$tour_id])->getResultArray();
    }

    /**
     * ดาวน์โหลดรายชื่อนักกีฬาทั้งทัวร์นาเมนต์เป็นไฟล์ CSV
     * GET /admin/tournament/export-players-csv/{tour_id}
     */
    public function export_players_csv($tour_id)
    {
        $tour_id = (int) $tour_id;
        $tournament = $this->tournamentModel->get_Tournament($tour_id);

        if (!$tournament) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลทัวร์นาเมนต์');
        }

        // สิทธิ์: แอดมินระบบ หรือผู้ใช้ที่มีหน้าที่ใน academy (ชุดเดียวกับ export อื่น)
        $isAdmin    = (bool) session()->get('isAdminLoggedIn');
        $isLoggedIn = (bool) session()->get('isLoggedIn');

        if (!$isAdmin && !$isLoggedIn) {
            return redirect()->to(base_url('admin/login'))->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }
        if (!$isAdmin && !isSuperAdmin() && !getUserAcademyId()) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $players = $this->tournamentPlayerRows($tour_id);

        // อายุ ณ วันที่ export — birth_date อาจว่าง เป็น 0000-00-00 หรือพิมพ์ปีผิดจนเป็นอนาคต
        // (ในข้อมูลจริงมีปี 2105) ปล่อยว่างดีกว่าใส่ตัวเลขที่ผิด
        $ageOf = static function (?string $birthDate): string {
            if (empty($birthDate) || $birthDate === '0000-00-00') return '';
            try {
                $dob = new \DateTime($birthDate);
                $now = new \DateTime();
                return $dob > $now ? '' : (string) $dob->diff($now)->y;
            } catch (\Exception $e) {
                return '';
            }
        };

        $fmtDate = static function (?string $d): string {
            return (empty($d) || $d === '0000-00-00') ? '' : date('d/m/Y', strtotime($d));
        };

        $out = fopen('php://temp', 'r+');

        fputcsv($out, [
            lang('Common.order_number'),
            lang('Common.team_name'),
            lang('Common.shirt_number'),
            lang('Common.full_name'),
            lang('Common.nickname'),
            lang('Common.position'),
            lang('Common.birth_date'),
            lang('Common.age'),
        ]);

        foreach ($players as $i => $p) {
            fputcsv($out, [
                $i + 1,
                $p['team_name'] ?? '',
                $p['shirt_number'] ?? '',
                trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')),
                $p['nick_name'] ?? '',
                $p['position_name_th'] ?: ($p['position_name'] ?? ''),
                $fmtDate($p['birth_date'] ?? null),
                $ageOf($p['birth_date'] ?? null),
            ]);
        }

        rewind($out);
        $csv = stream_get_contents($out);
        fclose($out);

        // ชื่อไฟล์: ตัดอักขระที่ใช้ในชื่อไฟล์ไม่ได้ออก แล้วห่อด้วย filename* ให้รองรับภาษาไทย
        $safeName = preg_replace('/[\/\\\\:*?"<>|]+/u', '-', (string) $tournament['name']);
        $fileName = 'players-' . $safeName . '-' . date('Ymd') . '.csv';

        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=UTF-8')
            ->setHeader('Content-Disposition',
                "attachment; filename=\"players-{$tour_id}.csv\"; filename*=UTF-8''" . rawurlencode($fileName))
            // BOM เพื่อให้ Excel อ่านภาษาไทยเป็น UTF-8 ไม่กลายเป็นตัวต่างดาว
            ->setBody("\xEF\xBB\xBF" . $csv);
    }

    public function export_registration($tour_id)
    {
        // Security Check: System Admin OR Academy Owner/Staff
        $isAdmin = (bool) session()->get('isAdminLoggedIn');
        $isLoggedIn = (bool) session()->get('isLoggedIn');
        $userAcademyId = getUserAcademyId();

        if (!$isAdmin && !$isLoggedIn) {
            return redirect()->to(base_url('admin/login'))->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }

        if (!$isAdmin && !isSuperAdmin() && !$userAcademyId) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $tournamentModel = new TournamentModel();

        // Get Tournament Info
        $tournament = $tournamentModel->get_Tournament($tour_id);

        // Get All Teams ordered by registration date
        $db = \Config\Database::connect();
        $builder = $db->table('tournament_team');
        $builder->select('tournament_team.create_date, team.name, team.logo');
        $builder->join('team', 'tournament_team.team_id = team.id', 'LEFT');
        $builder->where('tournament_team.tour_id', $tour_id);
        $builder->orderBy('tournament_team.create_date', 'ASC');
        $teams = $builder->get()->getResultArray();

        $data = [
            'tournament' => $tournament,
            'teams' => $teams,
        ];

        $html = view('Modules\Admin\Views\Tournament\export\registration_report_pdf', $data);

        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'thsarabun',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
            'orientation' => 'P',
        ]);

        $mpdf->WriteHTML($html);
        $mpdf->Output('registration_report_' . $tour_id . '.pdf', 'I');
    }

    public function export_registration_status($tour_id)
    {
        // Security Check: System Admin OR Academy Owner/Staff
        $isAdmin = (bool) session()->get('isAdminLoggedIn');
        $isLoggedIn = (bool) session()->get('isLoggedIn');
        $userAcademyId = getUserAcademyId();

        if (!$isAdmin && !$isLoggedIn) {
            return redirect()->to(base_url('admin/login'))->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }

        if (!$isAdmin && !isSuperAdmin() && !$userAcademyId) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $tournamentModel = new TournamentModel();
        $tournament = $tournamentModel->get_Tournament($tour_id);

        $db = \Config\Database::connect();

        // 1. ปรับ SQL Query (ลบ comment ออกเพื่อลดปัญหา Syntax error near '?')
        $sql = "SELECT
                    t.name as team_name,
                    t.logo as team_logo,
                    CASE WHEN tt.register_status = 1 THEN '✓' ELSE '-' END AS approved_status,
                    CASE WHEN tt.registration_document IS NULL OR tt.register_status IN (0, 3) THEN '✓' ELSE '-' END AS waiting_status,
                    CASE WHEN tt.registration_document IS NULL THEN '✓' ELSE '-' END AS document_status,
                    CASE WHEN tt.register_status = 2 THEN '✓' ELSE '-' END AS rejected_status,
                    tt.*
                FROM tournament_team AS tt
                LEFT JOIN team AS t ON tt.team_id = t.id
                WHERE tt.tour_id = ?";

        $teams = $db->query($sql, [(int) $tour_id])->getResultArray();

        $data = [
            'tournament' => $tournament,
            'teams'      => $teams,
        ];

        $html = view('Modules\Admin\Views\Tournament\export\registration_report_status_pdf', $data);

        // 2. ตั้งค่า mPDF เพื่อให้รองรับภาษาไทยแบบ 100%
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'thsarabun',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 10,
            'margin_left' => 10,
            'margin_right' => 10,
            'margin_bottom' => 10,
            'margin_header' => 5,
            'margin_footer' => 5,
            'orientation' => 'P',
        ]);

        $mpdf->WriteHTML($html);

        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf->Output('registration_report_' . $tour_id . '.pdf', 'I');
    }
}
