<?php

namespace Modules\Academy\Controllers\Traits;

/**
 * AcademyJerseyKitTrait
 *
 * Team Jersey / Kit color management (เหย้า/เยือน/สาม × ผู้เล่น/ผู้รักษาประตู).
 * เก็บสี 18 ฟิลด์ + รูป set 6 ฟิลด์ ลงทุกแถวผู้เล่นของทีมใน academy_player_enrollments.
 *
 * อาศัย helper ของ Academy controller: ensureAcademyAccess, ensureAjaxPermAccess,
 * validateTeamInAcademy + $this->academyModel (getTeam/getTeamKit/saveTeamKit).
 */
trait AcademyJerseyKitTrait
{
    /**
     * หน้าจัดการชุดทีม
     * GET /academy/team-jersey/{academy_id}/{team_id}
     */
    public function teamJersey($academy_id, $team_id)
    {
        $access = $this->ensureAcademyAccess($academy_id, true);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return redirect()->to(base_url('academy/teams/' . $academy_id))
                ->with('error', lang('Common.academy_team_not_in_academy'));
        }

        $data['team']  = $this->academyModel->getTeam($team_id);
        $data['kit']   = $this->academyModel->getTeamKit($academy_id, $team_id);
        $data['title'] = lang('Common.jersey_manager');

        return view('Modules\Academy\Views\team_jersey', $data);
    }

    /**
     * บันทึกชุดทีม (multipart: สี 18 ฟิลด์ + รูป set 6 ฟิลด์)
     * เก็บค่าเดียวกันลงทุกแถวผู้เล่นของทีม (academy_id + team_id)
     * POST /academy/team-jersey/save/{academy_id}/{team_id}
     */
    public function saveTeamJersey($academy_id, $team_id)
    {
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_create_edit_team');
        if ($err) return $err;
        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_team_not_in_academy')]);
        }

        $save = $this->buildKitPayload();

        $result = $this->academyModel->saveTeamKit($academy_id, $team_id, $save);
        if ($result !== false) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.jersey_saved')]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.save_failed')]);
    }

    /**
     * โหลดชุดทีมของทัวร์นาเมนต์ (AJAX) — ใช้ในหน้า player-chart
     * GET /academy/tournament-jersey/{academy_id}/{team_id}?tour_id=
     */
    public function tournamentJersey($academy_id, $team_id)
    {
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_create_edit_team');
        if ($err) return $err;
        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_team_not_in_academy')]);
        }

        $tourId = (int) $this->request->getGet('tour_id');
        if ($tourId <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.please_select_tournament')]);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'kit'    => $this->academyModel->getTournamentTeamKit($tourId, $team_id),
        ]);
    }

    /**
     * บันทึกชุดทีมของทัวร์นาเมนต์ → tournament_team (tour_id + team_id)
     * POST /academy/tournament-jersey/save/{academy_id}/{team_id}   (tour_id มากับ POST)
     */
    public function saveTournamentJersey($academy_id, $team_id)
    {
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_create_edit_team');
        if ($err) return $err;
        if (!$this->validateTeamInAcademy($academy_id, $team_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_team_not_in_academy')]);
        }

        $tourId = (int) $this->request->getPost('tour_id');
        if ($tourId <= 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.please_select_tournament')]);
        }

        // ทีมต้องลงทะเบียนในทัวร์นาเมนต์นี้จริง
        $exists = $this->db->table('tournament_team')
            ->where('tour_id', $tourId)
            ->where('team_id', (int) $team_id)
            ->countAllResults();
        if ($exists === 0) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.data_not_found')]);
        }

        $save = $this->buildKitPayload();

        $result = $this->academyModel->saveTournamentTeamKit($tourId, $team_id, $save);
        if ($result !== false) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.jersey_saved')]);
        }
        return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.save_failed')]);
    }

    /**
     * แปลง POST → payload ชุด (สี 18 ฟิลด์ + รูป set 6 ฟิลด์)
     * ใช้ร่วมกันทั้งแบบระดับทีม (academy_player_enrollments) และระดับทัวร์นาเมนต์ (tournament_team)
     */
    private function buildKitPayload(): array
    {
        $kits  = ['home', 'away', 'third'];
        $sets  = ['', 'goal_'];
        $parts = ['shirt', 'trousers', 'socks'];
        $save  = [];

        // สี (18 ฟิลด์) — รับเฉพาะ hex 6 หลักที่ถูกต้อง ไม่งั้นเป็น NULL
        foreach ($kits as $k) {
            foreach ($sets as $g) {
                foreach ($parts as $p) {
                    $col = "{$k}_{$g}{$p}";
                    $val = trim((string) $this->request->getPost($col));
                    $save[$col] = preg_match('/^#[0-9A-Fa-f]{6}$/', $val) ? strtoupper($val) : null;
                }
            }
        }

        // รูป set (6 ฟิลด์) — อัปโหลดใหม่ถ้ามี / ลบถ้ากดลบ / ไม่งั้นคงค่าเดิม
        $imgFields = ['home_set_img', 'home_goal_set_img', 'away_set_img', 'away_goal_set_img', 'third_set_img', 'third_goal_set_img'];
        $uploadDir = FCPATH . 'public/uploads/jerseys/';
        if (!is_dir($uploadDir)) { @mkdir($uploadDir, 0777, true); }

        foreach ($imgFields as $col) {
            $data64 = (string) $this->request->getPost($col . '_data');
            if (strpos($data64, 'data:image/') === 0) {
                // อัปโหลดรูปใหม่ (base64) — แนวเดียวกับ uploadJersey เดิม
                $img = preg_replace('/^data:image\/\w+;base64,/', '', $data64);
                $img = str_replace(' ', '+', $img);
                $bin = base64_decode($img);
                if ($bin !== false && strlen($bin) > 0) {
                    $newName = 'kit_' . $col . '_' . substr(number_format(time() * mt_rand(), 0, '', ''), 0, 10) . '.jpeg';
                    file_put_contents($uploadDir . $newName, $bin);
                    $save[$col] = 'public/uploads/jerseys/' . $newName;
                }
            } elseif ($this->request->getPost($col . '_remove') === '1') {
                $save[$col] = null;
            }
            // ไม่อัปโหลดและไม่ลบ → ไม่ใส่ใน $save เพื่อคงค่าเดิม
        }

        return $save;
    }
}
