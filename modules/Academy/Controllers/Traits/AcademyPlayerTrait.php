<?php

namespace Modules\Academy\Controllers\Traits;

trait AcademyPlayerTrait
{
    /**
     * ค้นหานักเตะ system-wide สำหรับ enroll เข้า academy
     * GET /academy/player-search/{academy_id}?q=keyword
     */
    public function playerSearch($academy_id)
    {
        if (!$academy_id || !$this->canViewAcademy($academy_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่มีสิทธิ์เข้าถึง']);
        }

        $keyword = $this->request->getGet('q') ?? '';
        if (strlen($keyword) < 2) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'กรุณาพิมพ์อย่างน้อย 2 ตัวอักษร']);
        }

        $players = $this->academyModel->searchPlayersGlobal($keyword, (int)$academy_id);

        // เพิ่ม image_url
        foreach ($players as &$p) {
            $p['image_url'] = !empty($p['image_path']) ? base_url($p['image_path']) : base_url('public/img/img_profile.jpg');
        }
        unset($p);

        return $this->response->setJSON(['status' => 'success', 'data' => $players]);
    }

    /**
     * เพิ่มนักเตะเข้า Academy (enrollment)
     * POST /academy/player-enroll/{academy_id}
     * Body: { player_id, team_id, shirt_number }
     */
    public function playerEnroll($academy_id)
    {
        $errorResp = $this->ensureAjaxPermAccess($academy_id, 'perm_enroll_player');
        if ($errorResp) return $errorResp;

        $input = $this->getInput();
        $playerId = (int)($input['player_id'] ?? 0);
        $teamId   = (int)($input['team_id'] ?? 0);
        $shirt    = trim($input['shirt_number'] ?? '');

        if (!$playerId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่ระบุนักเตะ']);
        }

        // ตรวจว่า team_id อยู่ใน academy นี้ (ถ้าระบุ)
        if ($teamId && !$this->validateTeamInAcademy($academy_id, $teamId)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ทีมไม่ได้อยู่ใน Academy นี้']);
        }

        $result = $this->academyModel->enrollPlayerToAcademy(
            (int)$academy_id, $playerId, $teamId, $shirt, (int)session()->get('user_id')
        );

        return $this->response->setJSON([
            'status'  => $result['success'] ? 'success' : 'error',
            'message' => $result['message'],
        ]);
    }

    /**
     * ถอนนักเตะออกจาก Academy
     * POST /academy/player-unenroll/{academy_id}
     * Body: { player_id }
     */
    public function playerUnenroll($academy_id)
    {
        $errorResp = $this->ensureAjaxPermAccess($academy_id, 'perm_enroll_player');
        if ($errorResp) return $errorResp;

        $input    = $this->getInput();
        $playerId = (int)($input['player_id'] ?? 0);

        if (!$playerId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่ระบุนักเตะ']);
        }

        //เช็คก่อนว่านักเตะอยู่ในแผงรูปไหม ถ้ามีต้องไปลบก่อน
        $checkVal = $this->academyModel->checkPlayerCardSubmmited((int)$academy_id, $playerId);
        if($checkVal) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถลบได้ ต้องไปลบนักเตะออกจากแผงรูปก่อน!']);
        }
        //
        
        $success = $this->academyModel->unenrollPlayerFromAcademy((int)$academy_id, $playerId);

        return $this->response->setJSON([
            'status'  => $success ? 'success' : 'error',
            'message' => $success ? 'ถอนนักเตะสำเร็จ' : 'เกิดข้อผิดพลาด',
        ]);
    }

    /**
     * หน้าแก้ไขข้อมูลนักเตะ (3 sections ตาม permission)
     * GET /academy/player-edit/{academy_id}/{player_id}
     */
    public function playerEdit($academy_id, $player_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $currentUserId = session()->get('user_id');

        // ตรวจสิทธิ์
        $canEditAll      = $this->academyModel->canEditAllPlayerFields((int)$player_id, $currentUserId);
        $canEditPhysical = $this->academyModel->canEditPlayerPhysical((int)$player_id, $currentUserId);
        $canEditTeam     = $this->academyModel->canEditPlayerTeamData((int)$player_id, (int)$academy_id, $currentUserId);

        if (!$canEditAll && !$canEditPhysical && !$canEditTeam) {
            return redirect()->to(base_url('academy/player-view/' . $academy_id . '/' . $player_id))
                ->with('error', 'ไม่มีสิทธิ์แก้ไขข้อมูลนักเตะ');
        }

        $player = $this->db->table('player')->where('id', $player_id)->get()->getRowArray();
        if (!$player) {
            return redirect()->to(base_url('academy/players/' . $academy_id))->with('error', 'ไม่พบนักเตะ');
        }

        $enrollment = $this->academyModel->getPlayerEnrollment((int)$player_id, (int)$academy_id);

        $data['player']       = $player;
        $data['enrollment']   = $enrollment;
        $data['academy_id']   = $academy_id;
        $data['canEditAll']   = $canEditAll;
        $data['canEditPhysical'] = $canEditPhysical;
        $data['canEditTeam']  = $canEditTeam;
        $data['teams']        = $this->academyModel->getAcademyTeams($academy_id);
        $data['positions']    = $this->db->table('tbl_player_position')->get()->getResultArray();
        $data['provinces']    = $this->academyModel->getProvinces();

        return view('Modules\Academy\Views\player_edit.php', $data);
    }

    /**
     * บันทึกข้อมูลนักเตะ (แยก section ตาม permission)
     * POST /academy/player-save/{academy_id}
     */
    public function playerSave($academy_id)
    {
        // Support both JSON and FormData
        $input = $this->request->getPost();
        if (empty($input)) {
            $input = $this->getInput();
        }

        $playerId = (int)($input['player_id'] ?? 0);
        $section  = $input['section'] ?? '';
        $currentUserId = session()->get('user_id');

        if (!$playerId || !$academy_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
        }

        if (!$this->canViewAcademy($academy_id)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่มีสิทธิ์']);
        }

        $update = [];
        $response = ['status' => 'success', 'message' => 'บันทึกสำเร็จ'];

        // Section A: ข้อมูลส่วนตัว
        if ($section === 'A' || $section === 'ALL') {
            if ($this->academyModel->canEditAllPlayerFields($playerId, $currentUserId)) {
                $fields = ['first_name', 'last_name', 'nick_name', 'birth_date', 'id_card', 'nation', 'sex', 'contract', 'contract_note'];
                foreach ($fields as $f) {
                    if (isset($input[$f])) $update[$f] = $input[$f] ?: null;
                }
                if (!empty($input['birth_date'])) {
                    $update['birth_date'] = date('Y-m-d', strtotime(str_replace('/', '-', $input['birth_date'])));
                }
                if (!empty($input['contract_start'])) {
                    $update['contract_start'] = date('Y-m-d', strtotime(str_replace('/', '-', $input['contract_start'])));
                } else if (isset($input['contract_start'])) {
                    $update['contract_start'] = null;
                }
                if (!empty($input['contract_end'])) {
                    $update['contract_end'] = date('Y-m-d', strtotime(str_replace('/', '-', $input['contract_end'])));
                } else if (isset($input['contract_end'])) {
                    $update['contract_end'] = null;
                }

                if (empty($update['contract'])) {
                    $update['contract_start'] = null;
                    $update['contract_end'] = null;
                    $update['contract_note'] = null;
                }

                // Handle ID card image upload
                $file = $this->request->getFile('id_card_image');
                if ($file && $file->isValid() && !$file->hasMoved()) {
                    $uploadPath = FCPATH . 'writable/secure_uploads/id_cards/';
                    if (!is_dir($uploadPath)) mkdir($uploadPath, 0755, true);

                    $oldPlayer = $this->db->table('player')->where('id', $playerId)->get()->getRowArray();
                    if (!empty($oldPlayer['id_card_image']) && file_exists(FCPATH . $oldPlayer['id_card_image'])) {
                        @unlink(FCPATH . $oldPlayer['id_card_image']);
                    }

                    $newName = 'id_card_' . $playerId . '_' . time() . '.' . $file->getExtension();
                    if ($file->move($uploadPath, $newName)) {
                        $update['id_card_image'] = 'writable/secure_uploads/id_cards/' . $newName;
                        $response['id_card_image'] = $update['id_card_image'];
                    }
                }
            } else if ($section === 'A') {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่มีสิทธิ์แก้ไขข้อมูลส่วนตัว']);
            }
        }

        // Section B: ข้อมูลกายภาพ
        if ($section === 'B' || $section === 'ALL') {
            if ($this->academyModel->canEditPlayerPhysical($playerId, $currentUserId)) {
                $physicalData = [
                    'height'        => $input['height'] ?? null,
                    'weight'        => $input['weight'] ?? null,
                    'bmi'           => $input['bmi']    ?? null,
                    'dominant_foot' => $input['dominant_foot'] ?? null,
                ];
                $update = array_merge($update, $physicalData, [
                    'physical_updated_at' => date('Y-m-d H:i:s'),
                    'physical_updated_by' => $currentUserId,
                ]);

                // Record history if anything changed (simplified)
                $this->academyModel->recordPhysicalHistory($playerId, $physicalData, $currentUserId, (int)$academy_id);
            } else if ($section === 'B') {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่มีสิทธิ์แก้ไขข้อมูลกายภาพ']);
            }
        }

        // Section C: เบอร์เสื้อ + ตำแหน่ง
        if ($section === 'C' || $section === 'ALL') {
            if ($this->academyModel->canEditPlayerTeamData($playerId, (int)$academy_id, $currentUserId)) {
                if (isset($input['shirt_number'])) {
                    $this->academyModel->updatePlayerShirtNumber($playerId, (int)$academy_id, $input['shirt_number']);
                }
                if (isset($input['shirt_size'])) {
                    $this->academyModel->updatePlayerShirtSize($playerId, (int)$academy_id, $input['shirt_size']);
                }
                if (isset($input['position_id'])) {
                    $posId = (int)$input['position_id'];
                    $subIds = !empty($input['sub_position_ids']) ? (array)$input['sub_position_ids'] : [];
                    $this->academyModel->updatePlayerTeamPosition($playerId, (int)$academy_id, $posId, $subIds);
                }
            } else if ($section === 'C') {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่มีสิทธิ์แก้ไขข้อมูลทีม']);
            }
        }

        if (!empty($update)) {
            $update['update_date'] = date('Y-m-d H:i:s');
            $this->db->table('player')->where('id', $playerId)->update($update);
        }

        $response['redirect_url'] = base_url('academy/players/' . (int)$academy_id);
        return $this->response->setJSON($response);
    }

    /**
     * AJAX: Upload player profile image
     * POST /academy/player-upload-image
     */
    public function playerUploadImage()
    {
        try {
            $playerId = (int)($this->request->getPost('player_id') ?? 0);
            $academyId = (int)($this->request->getPost('academy_id') ?? 0);
            $currentUserId = session()->get('user_id');

            if (!$playerId || !$academyId) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'ข้อมูลไม่ครบ']);
            }
            if (!$this->canViewAcademy($academyId)) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่มีสิทธิ์']);
            }
            if (!$this->academyModel->canEditAllPlayerFields($playerId, $currentUserId)) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่มีสิทธิ์แก้ไขข้อมูลส่วนตัว']);
            }

            $uploadPath = FCPATH . 'public/uploads/players/';
            if (!is_dir($uploadPath) && !mkdir($uploadPath, 0755, true)) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'สร้างโฟลเดอร์อัปโหลดไม่ได้']);
            }
            if (!is_writable($uploadPath)) {
                return $this->response->setJSON(['status' => 'error', 'msg' => 'โฟลเดอร์อัปโหลดเขียนไฟล์ไม่ได้']);
            }

            $imagePath = '';
            $base64Image = $this->request->getPost('base64_image');

            if (!empty($base64Image)) {
                // Handle Base64 Upload (from Cropper)
                if (preg_match('/^data:image\/(\w+);base64,/', $base64Image, $type)) {
                    $data = substr($base64Image, strpos($base64Image, ',') + 1);
                    $type = strtolower($type[1]); // jpg, png, etc

                    if (!in_array($type, ['jpg', 'jpeg', 'png'])) {
                        return $this->response->setJSON(['status' => 'error', 'msg' => 'รองรับเฉพาะ JPG/PNG']);
                    }

                    $data = base64_decode($data);
                    if ($data === false) {
                        return $this->response->setJSON(['status' => 'error', 'msg' => 'Base64 decode failed']);
                    }

                    $newName = time() . '_' . bin2hex(random_bytes(16)) . '.' . $type;
                    if (file_put_contents($uploadPath . $newName, $data) === false) {
                        return $this->response->setJSON(['status' => 'error', 'msg' => 'บันทึกไฟล์รูปภาพล้มเหลว']);
                    }
                    $imagePath = 'public/uploads/players/' . $newName;
                } else {
                    return $this->response->setJSON(['status' => 'error', 'msg' => 'รูปแบบ Base64 ไม่ถูกต้อง']);
                }
            } else {
                // Handle Direct File Upload (Fallback)
                $profileFile = $this->request->getFile('image_path');
                if (!$profileFile) {
                    return $this->response->setJSON(['status' => 'error', 'msg' => 'ไม่พบไฟล์รูปภาพ']);
                }
                if (!$profileFile->isValid()) {
                    return $this->response->setJSON(['status' => 'error', 'msg' => 'อัปโหลดไม่สำเร็จ: ' . $profileFile->getErrorString()]);
                }

                $mime = $profileFile->getMimeType();
                if (!in_array($mime, ['image/jpeg', 'image/jpg', 'image/png'], true)) {
                    return $this->response->setJSON(['status' => 'error', 'msg' => 'รองรับเฉพาะ JPG/PNG']);
                }
                if ($profileFile->getSize() > 5 * 1024 * 1024) {
                    return $this->response->setJSON(['status' => 'error', 'msg' => 'ขนาดไฟล์รูปภาพต้องไม่เกิน 5MB']);
                }

                $ext = $profileFile->guessExtension() ?: 'jpg';
                $newName = time() . '_' . bin2hex(random_bytes(16)) . '.' . $ext;
                if (!$profileFile->move($uploadPath, $newName, true)) {
                    return $this->response->setJSON(['status' => 'error', 'msg' => 'ย้ายไฟล์ไม่สำเร็จ']);
                }
                $imagePath = 'public/uploads/players/' . $newName;
            }

            // Delete old image
            $oldPlayer = $this->db->table('player')->where('id', $playerId)->get()->getRowArray();
            if (!empty($oldPlayer['image_path']) && file_exists(FCPATH . $oldPlayer['image_path'])) {
                @unlink(FCPATH . $oldPlayer['image_path']);
            }

            // Update Database
            $this->db->table('player')->where('id', $playerId)->update([
                'image_path'  => $imagePath,
                'update_date' => date('Y-m-d H:i:s'),
            ]);

            return $this->response->setJSON([
                'status'     => 'success',
                'image_path' => $imagePath,
            ]);
        } catch (\Throwable $e) {
            log_message('error', 'playerUploadImage exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'msg'    => 'เกิดข้อผิดพลาด: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * อัพเดทเบอร์เสื้อนักเตะ (AJAX quick update)
     * POST /academy/player-shirt/{academy_id}/{team_id}
     */
    public function playerShirtUpdate($academy_id, $team_id)
    {
        $errorResp = $this->ensureAjaxPermAccess($academy_id, 'perm_enroll_player');
        if ($errorResp) return $errorResp;

        $input    = $this->getInput();
        $playerId = (int)($input['player_id'] ?? 0);
        $shirt    = trim($input['shirt_number'] ?? '');

        if (!$playerId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่ระบุนักเตะ']);
        }

        $this->academyModel->updatePlayerShirtNumber($playerId, (int)$academy_id, $shirt);
        return $this->response->setJSON(['status' => 'success', 'message' => 'อัพเดทเบอร์เสื้อสำเร็จ']);
    }

    /**
     * หน้าดูข้อมูลนักเตะ
     * GET /academy/player-view/{academy_id}/{player_id}
     */
    public function playerView($academy_id, $player_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $player = $this->db->table('player')
            ->select('player.*, tbl_provinces.province_name, tbl_districts.district_name as amphure_name,
                      tbl_subdistricts.subdistrict_name as tambon_name, coarse_pd.name_th as position_name,
                      ape_pd.shirt_number as shirt_number,
                      COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id,
                      ape_pd.sub_position_ids as player_sub_position_id')
            ->join('tbl_provinces', 'tbl_provinces.province_id = player.province_id', 'left')
            ->join('tbl_districts', 'tbl_districts.district_id = player.amphure_id', 'left')
            ->join('tbl_subdistricts', 'tbl_subdistricts.subdistrict_id = player.tambon_id', 'left')
            ->join('academy_player_enrollments ape_pd', 'ape_pd.player_id = player.id AND ape_pd.is_primary = 1 AND ape_pd.is_active = 1', 'left')
            ->join('tbl_player_position', 'tbl_player_position.id = ape_pd.position_id', 'left')
            ->join('tbl_player_position coarse_pd', 'coarse_pd.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)', 'left')
            ->where('player.id', $player_id)
            ->get()->getRowArray();

        if (!$player) {
            return redirect()->to(base_url('academy/players/' . $academy_id))->with('error', 'ไม่พบนักเตะ');
        }

        $enrollment = $this->academyModel->getPlayerEnrollment((int)$player_id, (int)$academy_id);
        $allAcademies = $this->academyModel->getPlayerAcademies((int)$player_id);
        $currentUserId = session()->get('user_id');

        $data['player']     = $player;
        $data['enrollment'] = $enrollment;
        $data['academies']  = $allAcademies;
        $data['academy_id'] = $academy_id;
        $data['canEditAll'] = $this->academyModel->canEditAllPlayerFields((int)$player_id, $currentUserId);
        $data['canEditPhysical'] = $this->academyModel->canEditPlayerPhysical((int)$player_id, $currentUserId);
        $data['canEditTeam'] = $this->academyModel->canEditPlayerTeamData((int)$player_id, (int)$academy_id, $currentUserId);
        $data['canManage']  = $this->hasPermission($academy_id, 'perm_enroll_player');

        return view('Modules\Academy\Views\player_view.php', $data);
    }

    /**
     * ประวัติการเข้า-ออก Academy ของนักเตะ
     * GET /academy/player-history/{academy_id}/{player_id}
     */
    /**
     * ประวัติข้อมูลกายภาพนักเตะ
     * GET /academy/player-physical-history/{academy_id}/{player_id}
     */
    public function playerPhysicalHistory($academy_id, $player_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $player = $this->db->table('player')->where('id', $player_id)->get()->getRowArray();
        if (!$player) {
            return redirect()->to(base_url('academy/players/' . $academy_id))->with('error', 'ไม่พบนักเตะ');
        }

        $data['player']     = $player;
        $data['history']    = $this->academyModel->getPlayerPhysicalHistory((int)$player_id, 50);
        $data['academy_id'] = $academy_id;

        return view('Modules\Academy\Views\player_physical_history.php', $data);
    }

    /**
     * ประวัติการเข้า-ออก Academy ของนักเตะ
     * GET /academy/player-history/{academy_id}/{player_id}
     */
    public function playerHistory($academy_id, $player_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $player = $this->db->table('player')->where('id', $player_id)->get()->getRowArray();
        if (!$player) {
            return redirect()->to(base_url('academy/players/' . $academy_id))->with('error', 'ไม่พบนักเตะ');
        }

        $data['player']   = $player;
        $data['history']  = $this->academyModel->getPlayerEnrollmentHistory((int)$player_id);
        $data['academy_id'] = $academy_id;

        return view('Modules\Academy\Views\player_history.php', $data);
    }

    /**
     * หน้าเลือก academy สำหรับ super academy operator
     * GET /academy/select
     */
    public function selectAcademy()
    {
        // CSRF: trigger sendCookie() บน GET เพื่อให้ POST /academy/switch มี cookie ตรง
        // (ปกติงานนี้ทำโดย global CSRF filter ที่ปิดอยู่ในโปรเจกต์)
        \Config\Services::security()->verify($this->request);

        $academies = $this->db->table('academy')
            ->select('id, name, logo')
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        // Batch counts (เดิม N+1 = 1+189×2=379 queries → ตอนนี้ 3 queries รวม)
        $teamCounts = $this->db->table('academy_teams')
            ->select('academy_id, COUNT(*) AS cnt')
            ->groupBy('academy_id')->get()->getResultArray();
        $teamMap = array_column($teamCounts, 'cnt', 'academy_id');

        $memberCounts = $this->db->table('academy_members')
            ->select('academy_id, COUNT(*) AS cnt')
            ->where('status', 'approved')
            ->groupBy('academy_id')->get()->getResultArray();
        $memberMap = array_column($memberCounts, 'cnt', 'academy_id');

        foreach ($academies as &$a) {
            $a['team_count']   = (int)($teamMap[$a['id']]   ?? 0);
            $a['member_count'] = (int)($memberMap[$a['id']] ?? 0);
        }
        unset($a);

        return view('Modules\Academy\Views\select_academy', [
            'academies'           => $academies,
            'current_academy_id'  => (int)session()->get('current_academy_id'),
        ]);
    }

    /**
     * AJAX search ของ /academy/select
     * GET /academy/select-search?q=...
     */
    public function selectSearch()
    {
        $q = trim((string)$this->request->getGet('q'));
        $b = $this->db->table('academy')
            ->select('id, name, logo')
            ->where('is_active', 1);
        if ($q !== '') $b->like('name', $q);
        $rows = $b->orderBy('name', 'ASC')->limit(50)->get()->getResultArray();
        return $this->response->setJSON(['data' => $rows]);
    }

    /**
     * Set session.current_academy_id แล้ว redirect ไป academy dashboard
     * POST /academy/switch
     *
     * CSRF: ตรวจ token ใน controller (CI4 4.1.1 ไม่รองรับ filter array per-route)
     */
    public function switchAcademy()
    {
        // CSRF protection — verify ก่อนเปลี่ยน session state
        try {
            \Config\Services::security()->verify($this->request);
        } catch (\Throwable $e) {
            return redirect()->to('/academy/select')->with('error', 'CSRF validation failed');
        }

        $id = (int)$this->request->getPost('academy_id');
        if ($id <= 0) {
            return redirect()->to('/academy/select')->with('error', 'invalid academy_id');
        }
        // double-check ว่า academy active จริง + ดึงชื่อมา cache ใน session (ลบ DB query ใน view)
        $row = $this->db->table('academy')
            ->select('id, name')
            ->where('id', $id)->where('is_active', 1)
            ->limit(1)->get()->getRowArray();
        if (!$row) {
            return redirect()->to('/academy/select')->with('error', 'academy not found');
        }
        session()->set('current_academy_id', (int)$row['id']);
        session()->set('current_academy_name', $row['name']);
        return redirect()->to('/academy/info/' . $id);
    }

    public function updateCoachRole()
    {
        $input = $this->getInput();
        $coachId = (int)($input['id'] ?? 0);
        $teamId = (int)($input['team_id'] ?? 0);
        $roleLabel = trim($input['role_display_label'] ?? '');

        if (!$coachId || !$teamId) {
            return $this->response->setJSON(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน (Missing ID or Team ID)']);
        }

        // Check academy and permission
        $academy = $this->academyModel->getAcademyByTeamId($teamId);
        if (!$academy) {
            return $this->response->setJSON(['success' => false, 'message' => 'ไม่พบทีมนี้ใน อเดคามี่']);
        }

        // Check if the record exists in coach_teams
        $exists = $this->db->table('coach_teams')
            ->where('coach_id', $coachId)
            ->where('team_id', $teamId)
            ->get()->getRow();

        if (!$exists) {
            return $this->response->setJSON(['success' => false, 'message' => 'ไม่พบข้อมูลโค้ชในทีมนี้']);
        }

        $success = $this->db->table('coach_teams')
            ->where('coach_id', $coachId)
            ->where('team_id', $teamId)
            ->update(['role_display_label' => $roleLabel]);

        return $this->response->setJSON(['success' => $success, 'message' => $success ? 'บันทึกสำเร็จ' : 'ไม่สามารถบันทึกข้อมูลได้']);

    }
}
