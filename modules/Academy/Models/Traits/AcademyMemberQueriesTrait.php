<?php

namespace Modules\Academy\Models\Traits;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

trait AcademyMemberQueriesTrait
{
    // =====================================================
    // Enrollment Management (Multi-Academy Support)
    // =====================================================

    /**
     * เพิ่มนักเตะเข้า Academy (enrollment)
     * บังคับ: 1 active enrollment ต่อ (player, academy)
     */
    function enrollPlayerToAcademy(int $academy_id, int $player_id, int $team_id, string $shirt_number = '', int $enrolled_by = 0): array
    {
        // ตรวจ existing enrollment (รวม is_active=0 — รองรับ re-enroll แถวเดิม)
        // deterministic: ถ้ามีแถว active ต้องเจอก่อน (กันสร้างแถว active ซ้อน), แล้วเลือกแถวที่มีทีมจริงก่อน team_id=0
        $existing = $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)
            ->where('academy_id', $academy_id)
            ->orderBy('is_active', 'DESC')
            ->orderBy('team_id', 'DESC')
            ->orderBy('id', 'DESC')
            ->get()->getRowArray();

        if ($existing && (int)$existing['is_active'] === 1) {
            return ['success' => false, 'message' => 'นักเตะอยู่ใน Academy นี้อยู่แล้ว'];
        }

        // ตรวจว่า player มีอยู่ในระบบ (ไม่สน is_active — รองรับเพิ่มนักเตะที่ถูกปิดกลับเข้าระบบ)
        $player = $this->db->table('player')->where('id', $player_id)->get()->getRowArray();
        if (!$player) {
            return ['success' => false, 'message' => 'ไม่พบนักเตะในระบบ'];
        }

        $this->db->transBegin();
        try {
            // ถ้านักเตะถูกปิดไว้ (soft-deleted) → เปิดใช้งานกลับ เพราะตั้งใจเพิ่มเข้า academy
            if ((int)($player['is_active'] ?? 1) !== 1) {
                $this->db->table('player')->where('id', $player_id)->update(['is_active' => 1]);
            }

            if ($existing) {
                // re-enroll: นักเตะเคยอยู่แล้วถูกถอนออก → อัปเดตแทน insert
                $this->db->table('academy_player_enrollments')
                    ->where('id', $existing['id'])
                    ->update([
                        'team_id'      => $team_id,
                        'shirt_number' => $shirt_number,
                        'enrolled_at'  => date('Y-m-d H:i:s'),
                        'enrolled_by'  => $enrolled_by ?: null,
                        'left_at'      => null,
                        'is_active'    => 1,
                    ]);
            } else {
                $this->db->table('academy_player_enrollments')->insert([
                    'player_id'   => $player_id,
                    'academy_id'  => $academy_id,
                    'team_id'     => $team_id,
                    'shirt_number'=> $shirt_number,
                    'enrolled_at' => date('Y-m-d H:i:s'),
                    'enrolled_by' => $enrolled_by ?: null,
                    'is_active'   => 1,
                ]);
            }

            // Sync legacy fields
            $this->syncPlayerLegacyTeam($player_id);

            $this->db->transCommit();
            return ['success' => true, 'message' => 'เพิ่มนักเตะสำเร็จ'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    function checkPlayerCardSubmmited($academy_id, $player_id) {
        $today = date('Y-m-d');

        $builder = $this->db->table('tournament');
        $builder->select("tour_id");
        $builder->where("is_active", 1);
        $builder->where("is_deleted", 0);
        $builder->where("tournament_end_date >=", $today); 

        $data = $builder->get()->getResultArray();
        
        if (empty($data)) {
            return false;
        }

        $tour_id_arr = array_column($data, 'tour_id');

        $builder = $this->db->table('tournament_submitted_players as ts');
        // Select เอาเฉพาะชื่อทัวร์นาเมนต์
        $builder->select('t.name as tournament_name'); 
        $builder->join('tournament_team as tt', 'ts.tour_team_id = tt.tour_team_id', 'left');
        $builder->join('tournament as t', 'tt.tour_id = t.tour_id', 'left'); // Join เอาชื่อทัวร์นาเมนต์
        $builder->join('academy_teams as ats', 'tt.team_id = ats.team_id', 'left');
        
        $builder->where('tt.register_status', 1);
        $builder->whereIn('tt.tour_id', $tour_id_arr);
        $builder->where('ats.academy_id', $academy_id);
        $builder->where('ts.player_id', $player_id);
        
        // ดึงมาแค่แถวเดียวเพื่อความเร็ว (กรณีต้องการชื่อเดียว)
        $result = $builder->get()->getRowArray(); 

        if (!empty($result)) {
            return $result['tournament_name']; // ส่งชื่อทัวร์นาเมนต์กลับไปเป็น String
        }
        
        return false; // ถ้าไม่พบข้อมูลส่งกลับ false เหมือนเดิม
    }

    /**
     * ถอนนักเตะออกจาก Academy (soft delete enrollment)
     */
    function unenrollPlayerFromAcademy(int $academy_id, int $player_id): bool
    {
        $this->db->transBegin();
        try {
            $this->db->table('academy_player_enrollments')
                ->where('player_id', $player_id)
                ->where('academy_id', $academy_id)
                ->where('is_active', 1)
                ->update([
                    'is_active' => 0,
                    'left_at'   => date('Y-m-d H:i:s'),
                ]);

            // Sync legacy
            $this->syncPlayerLegacyTeam($player_id);

            $this->db->transCommit();
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            return false;
        }
    }

    /**
     * ดึง enrollment record ของ player ใน academy
     */
    function getPlayerEnrollment(int $player_id, int $academy_id, bool $activeOnly = true): ?array
    {
        $builder = $this->db->table('academy_player_enrollments')
            ->select('academy_player_enrollments.*, team.name as team_name', false)
            ->join('team', 'team.id = academy_player_enrollments.team_id', 'left')
            ->where('academy_player_enrollments.player_id', $player_id)
            ->where('academy_player_enrollments.academy_id', $academy_id);

        if ($activeOnly) {
            $builder->where('academy_player_enrollments.is_active', 1);
        }

        // a player may have several enrollments in one academy (multiple teams);
        // return the primary one deterministically instead of an arbitrary row
        $builder->orderBy('academy_player_enrollments.is_primary', 'DESC')
                ->orderBy('academy_player_enrollments.is_active', 'DESC')
                ->orderBy('academy_player_enrollments.enrolled_at', 'DESC')
                ->orderBy('academy_player_enrollments.id', 'DESC')
                ->limit(1);

        $result = $builder->get()->getRowArray();
        return $result ?: null;
    }

    /**
     * ดึง academies ทั้งหมดที่ player สังกัด (active enrollments)
     */
    function getPlayerAcademies(int $player_id): array
    {
        return $this->db->table('academy_player_enrollments')
            ->select('academy_player_enrollments.*, academy.name as academy_name, academy.logo,
                      team.name as team_name')
            ->join('academy', 'academy.id = academy_player_enrollments.academy_id')
            ->join('team', 'team.id = academy_player_enrollments.team_id', 'left')
            ->where('academy_player_enrollments.player_id', $player_id)
            ->where('academy_player_enrollments.is_active', 1)
            ->orderBy('academy_player_enrollments.enrolled_at', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * ดึงประวัติ join/leave academy ของ player
     */
    function getPlayerEnrollmentHistory(int $player_id): array
    {
        $sql = "SELECT ape.*, a.name AS academy_name, a.logo,
                       t.name AS team_name, u.username AS enrolled_by_name
                FROM academy_player_enrollments ape
                JOIN academy a ON a.id = ape.academy_id
                LEFT JOIN team t ON t.id = ape.team_id
                LEFT JOIN `user` u ON u.id = ape.enrolled_by
                WHERE ape.player_id = ?
                ORDER BY ape.enrolled_at DESC";

        return $this->db->query($sql, [$player_id])->getResultArray();
    }

    /**
     * อัพเดทเบอร์เสื้อนักเตะใน enrollment
     */
    function updatePlayerShirtNumber(int $player_id, int $academy_id, string $shirt_number): bool
    {
        $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)
            ->where('academy_id', $academy_id)
            ->where('is_active', 1)
            ->update(['shirt_number' => $shirt_number]);

        // Sync legacy
        $this->syncPlayerLegacyTeam($player_id);

        $this->db->table('player')
            ->where('id', $player_id)
            ->update(['shirt_number' => $shirt_number]);

        return true;
    }

    /**
     * อัพเดทไซส์เสื้อนักเตะใน enrollment
     */
    function updatePlayerShirtSize(int $player_id, int $academy_id, string $shirt_size): bool
    {
        $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)
            ->where('academy_id', $academy_id)
            ->where('is_active', 1)
            ->update(['shirt_size' => $shirt_size]);

        return true;
    }

    /**
     * อัพเดทตำแหน่งนักเตะใน enrollment
     */
    function updatePlayerTeamPosition(int $player_id, int $academy_id, int $position_id, array $sub_ids = []): bool
    {
        $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)
            ->where('academy_id', $academy_id)
            ->where('is_active', 1)
            ->update([
                'position_id'      => $position_id ?: null,
                'sub_position_ids' => !empty($sub_ids) ? json_encode($sub_ids) : null,
            ]);
        return true;
    }

    // =====================================================
    // Permission Checks
    // =====================================================

    /**
     * ตรวจสอบว่า user สามารถแก้ไขข้อมูลทั้งหมดของนักเตะได้หรือไม่
     * (ผู้สร้าง หรือ system super admin)
     */
    function canEditAllPlayerFields(int $player_id, ?int $user_id): bool
    {
        $sessionUserId = session()->get('user_id');
        $sessionCoachUserId = session()->get('coach_user_id');
        $resolvedUserId = $sessionUserId ?: $user_id;
        $resolvedCoachUserId = $sessionCoachUserId ?: null;

        if (!$resolvedUserId && !$resolvedCoachUserId) return false;

        // Super admin
        if (session()->get('is_super_admin')) return true;

        // Super academy operator → virtual owner of any academy (same rule as getMemberRole)
        if ($resolvedUserId) {
            helper('academy');
            if (isSuperAcademyUser($resolvedUserId)) return true;
        }

        // ผู้สร้างนักเตะ (user ที่ลงทะเบียนโดยตรง)
        if ($resolvedUserId) {
            $perm = $this->db->table('player_edit_permissions')
                ->where('player_id', $player_id)
                ->where('created_by_user_id', $resolvedUserId)
                ->countAllResults();
            if ($perm > 0) return true;
        }

        // Academy owner/admin ของ academy ที่ "สร้าง" นักเตะ (enrollment แรกสุด)
        $firstEnrollment = $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)
            ->orderBy('id', 'ASC')
            ->limit(1)
            ->get()->getRowArray();

        if ($firstEnrollment) {
            $originAcademyId = (int)$firstEnrollment['academy_id'];

            if ($resolvedUserId) {
                $membership = $this->db->table('academy_members')
                    ->where('user_id', $resolvedUserId)
                    ->whereIn('role', ['owner', 'admin'])
                    ->where('status', 'approved')
                    ->where('academy_id', $originAcademyId)
                    ->countAllResults();
                if ($membership > 0) return true;
            }

            if ($resolvedCoachUserId) {
                $membership = $this->db->table('academy_members')
                    ->where('coach_user_id', $resolvedCoachUserId)
                    ->whereIn('role', ['coach'])
                    ->where('status', 'approved')
                    ->where('academy_id', $originAcademyId)
                    ->countAllResults();
                if ($membership > 0) return true;
            }
            
        }

        return false;
    }

    /**
     * ตรวจสอบว่า user สามารถแก้ข้อมูลกายภาพนักเตะได้หรือไม่
     * (canEditAll หรือ เป็น owner/admin/coach ของ academy ที่นักเตะสังกัด)
     */
    function canEditPlayerPhysical(int $player_id, ?int $user_id): bool
    {
        $sessionUserId = session()->get('user_id');
        $sessionCoachUserId = session()->get('coach_user_id');
        $resolvedUserId = $sessionUserId ?: $user_id;
        $resolvedCoachUserId = $sessionCoachUserId ?: null;

        if (!$resolvedUserId && !$resolvedCoachUserId) return false;
        if ($this->canEditAllPlayerFields($player_id, $user_id)) return true;

        // ตรวจว่าเป็น owner/admin/coach ของ academy ที่ player สังกัด
        $enrollments = $this->getPlayerAcademies($player_id);
        if (empty($enrollments)) return false;

        $academyIds = array_column($enrollments, 'academy_id');

        $builder = $this->db->table('academy_members')
            ->where('status', 'approved')
            ->whereIn('academy_id', $academyIds);

        $builder->groupStart();
        if ($resolvedUserId) {
            $builder->groupStart()
                ->where('user_id', $resolvedUserId)
                ->whereIn('role', ['owner', 'admin', 'coach'])
                ->groupEnd();
        }
        if ($resolvedCoachUserId) {
            if ($resolvedUserId) {
                $builder->orGroupStart();
            } else {
                $builder->groupStart();
            }
            $builder->where('coach_user_id', $resolvedCoachUserId)
                ->whereIn('role', ['coach'])
                ->groupEnd();
        }
        $builder->groupEnd();

        $membership = $builder->countAllResults();

        return $membership > 0;
    }

    /**
     * ตรวจสอบว่า user สามารถแก้ข้อมูลทีม/ตำแหน่งนักเตะได้หรือไม่
     * (canEditAll หรือ เป็น owner/admin/coach ของ academy ที่เป็นเจ้าของทีมนั้น)
     */
    function canEditPlayerTeamData(int $player_id, int $academy_id, ?int $user_id): bool
    {
        $sessionUserId = session()->get('user_id');
        $sessionCoachUserId = session()->get('coach_user_id');
        $resolvedUserId = $sessionUserId ?: $user_id;
        $resolvedCoachUserId = $sessionCoachUserId ?: null;

        if (!$resolvedUserId && !$resolvedCoachUserId) return false;
        if ($this->canEditAllPlayerFields($player_id, $user_id)) return true;

        // ตรวจว่าเป็น member ของ academy นี้ที่มีสิทธิ์
        $builder = $this->db->table('academy_members')
            ->where('academy_id', $academy_id)
            ->where('status', 'approved');

        $builder->groupStart();
        if ($resolvedUserId) {
            $builder->groupStart()
                ->where('user_id', $resolvedUserId)
                ->whereIn('role', ['owner', 'admin', 'coach'])
                ->groupEnd();
        }
        if ($resolvedCoachUserId) {
            if ($resolvedUserId) {
                $builder->orGroupStart();
            } else {
                $builder->groupStart();
            }
            $builder->where('coach_user_id', $resolvedCoachUserId)
                ->whereIn('role', ['coach'])
                ->groupEnd();
        }
        $builder->groupEnd();

        $membership = $builder->countAllResults();

        return $membership > 0;
    }

    /**
     * ค้นหานักเตะในระบบ (system-wide) ยกเว้น academy ที่ระบุ
     */
    /**
     * ค้นหานักเตะ system-wide (ทั้งระบบ) ตาม keyword
     * ไม่กรองออกแม้จะอยู่ใน academy นี้แล้ว แต่จะแนบสถานะ enrollment สำหรับ UI
     *
     * @param string $keyword
     * @param int $current_academy_id  academy ปัจจุบัน (ใช้แสดงสถานะ "อยู่ใน academy นี้แล้ว")
     */
    function searchPlayersGlobal(string $keyword, int $current_academy_id = 0): array
    {
        // บัตรประชาชนใน DB เก็บปนกัน (มีขีด/มีช่องว่าง/ตัวเลขล้วน) → ค้นแบบ normalize
        // ตัด - และช่องว่างออกจากทั้ง keyword และคอลัมน์ ให้ match กันได้ทุกรูปแบบ
        $idKeyword = str_replace(['-', ' '], '', $keyword);

        $builder = $this->db->table('player')
            ->select('player.id, player.first_name, player.last_name, player.nick_name,
                      player.image_path, player.birth_date, player.id_card,
                      CONCAT(player.first_name, " ", player.last_name) as full_name', false)
            ->groupStart()
                ->like('player.first_name', $keyword)
                ->orLike('player.last_name', $keyword)
                ->orLike('player.nick_name', $keyword)
                ->orLike('player.id_card', $keyword);

        if ($idKeyword !== '') {
            // เทียบเลขบัตรแบบตัดขีด/ช่องว่างทั้งสองฝั่ง — ใช้ RawSql ที่คอลัมน์ (ค่า value ยัง escape/quote ปกติ)
            $builder->orLike(new \CodeIgniter\Database\RawSql("REPLACE(REPLACE(player.id_card, '-', ''), ' ', '')"), $idKeyword);
        }

        $players = $builder->groupEnd()
            ->orderBy('player.first_name', 'ASC')
            ->limit(50)
            ->get()->getResultArray();

        if (empty($players)) return [];

        // โหลด enrollments ทั้งหมดของผู้เล่นใน result เพื่อระบุ status
        $playerIds = array_column($players, 'id');
        $enrollments = $this->db->table('academy_player_enrollments ape')
            ->select('ape.player_id, ape.academy_id, ape.team_id, a.name AS academy_name, t.name AS team_name')
            ->join('academy a', 'a.id = ape.academy_id', 'left')
            ->join('team t', 't.id = ape.team_id', 'left')
            ->whereIn('ape.player_id', $playerIds)
            ->where('ape.is_active', 1)
            ->get()->getResultArray();

        $byPlayer = [];
        foreach ($enrollments as $e) {
            $byPlayer[$e['player_id']][] = $e;
        }

        foreach ($players as &$p) {
            $enrolls = $byPlayer[$p['id']] ?? [];
            $p['enrollments'] = $enrolls;
            $p['enrollment_count'] = count($enrolls);
            $p['in_current_academy'] = false;
            $p['current_team_name']  = null;
            $p['other_academies']    = [];

            foreach ($enrolls as $e) {
                if ((int)$e['academy_id'] === $current_academy_id) {
                    $p['in_current_academy'] = true;
                    $p['current_team_name']  = $e['team_name'];
                } else {
                    $p['other_academies'][] = [
                        'academy_id'   => (int)$e['academy_id'],
                        'academy_name' => $e['academy_name'],
                        'team_name'    => $e['team_name'],
                    ];
                }
            }
        }
        unset($p);

        return $players;
    }

    /**
     * บันทึกผู้สร้างนักเตะ (เรียกหลัง INSERT player)
     */
    function setPlayerCreator(int $player_id, int $user_id): void
    {
        $exists = $this->db->table('player_edit_permissions')
            ->where('player_id', $player_id)
            ->countAllResults();

        if (!$exists) {
            $this->db->table('player_edit_permissions')->insert([
                'player_id'          => $player_id,
                'created_by_user_id' => $user_id,
                'created_at'         => date('Y-m-d H:i:s'),
            ]);
        }
    }

    /**
     * บันทึกข้อมูลกายภาพลง history
     * - ถ้ามี record ของวันนี้อยู่แล้ว → UPDATE record นั้น (แก้ไขซ้ำวันเดียวกัน)
     * - ถ้าไม่มี → INSERT record ใหม่ (วัดใหม่)
     */
    function recordPhysicalHistory(int $player_id, array $data, ?int $recorded_by = null, ?int $academy_id = null): void
    {
        $today = date('Y-m-d');
        $row   = $data;
        $existing = $this->db->table('player_physical_history')
            ->where('player_id', $player_id)
            ->where("DATE(recorded_at) = '$today'")
            ->orderBy('recorded_at', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        $payload = [
            'height'        => !empty($row['height'])        ? $row['height']        : null,
            'weight'        => !empty($row['weight'])        ? $row['weight']        : null,
            'bmi'           => !empty($row['bmi'])           ? $row['bmi']           : null,
            'dominant_foot' => !empty($row['dominant_foot']) ? $row['dominant_foot'] : null,
            'recorded_by'   => $recorded_by,
            'academy_id'    => $academy_id,
        ];

        if ($existing) {
            // อัพเดท record วันนี้
            $this->db->table('player_physical_history')
                ->where('id', $existing['id'])
                ->update(array_merge($payload, ['recorded_at' => date('Y-m-d H:i:s')]));
        } else {
            // สร้าง record ใหม่
            $this->db->table('player_physical_history')->insert(array_merge($payload, [
                'player_id'   => $player_id,
                'recorded_at' => date('Y-m-d H:i:s'),
            ]));
        }
    }

    /**
     * ดึงประวัติข้อมูลกายภาพของนักเตะ
     */
    function getPlayerPhysicalHistory(int $player_id, int $limit = 20): array
    {
        $sql = "SELECT pph.*, u.username AS recorded_by_name, a.name AS academy_name
                FROM player_physical_history pph
                LEFT JOIN `user` u ON u.id = pph.recorded_by
                LEFT JOIN academy a ON a.id = pph.academy_id
                WHERE pph.player_id = ?
                ORDER BY pph.recorded_at DESC
                LIMIT ?";

        return $this->db->query($sql, [$player_id, $limit])->getResultArray();
    }

    // =====================================================
    // Training Session Attachments
    // =====================================================

    /**
     * ดึงเอกสารแนบทั้งหมดของ session (is_active=1)
     */
    public function getSessionAttachments(int $session_id): array
    {
        return $this->db->table('academy_training_session_attachments')
            ->where('session_id', $session_id)
            ->where('is_active', 1)
            ->orderBy('created_date', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * นับจำนวนเอกสารแนบของ session (is_active=1)
     */
    public function countSessionAttachments(int $session_id): int
    {
        return (int) $this->db->table('academy_training_session_attachments')
            ->where('session_id', $session_id)
            ->where('is_active', 1)
            ->countAllResults();
    }

    /**
     * นับเอกสารแนบหลาย session พร้อมกัน (batch สำหรับหน้า list)
     * @param array $session_ids
     * @return array [session_id => count]
     */
    public function getAttachmentsForSessions(array $session_ids): array
    {
        if (empty($session_ids)) return [];
        $rows = $this->db->table('academy_training_session_attachments')
            ->select('session_id, COUNT(*) as cnt', false)
            ->whereIn('session_id', $session_ids)
            ->where('is_active', 1)
            ->groupBy('session_id')
            ->get()->getResultArray();
        $map = [];
        foreach ($rows as $r) $map[(int)$r['session_id']] = (int)$r['cnt'];
        return $map;
    }

    /**
     * Insert attachment rows from uploaded files (after file move)
     * @param int $session_id
     * @param int $academy_id
     * @param array $files — array of [attach_name, attach_file, attach_file_type, attach_size] already moved
     * @param int|null $created_by user.id or coach_user.id
     * @param string $created_by_type 'user' or 'coach'
     * @return array of inserted filenames (for reference)
     */
    public function insertSessionAttachments(
        int $session_id,
        int $academy_id,
        array $files,
        ?int $created_by,
        string $created_by_type
    ): array {
        $now = date('Y-m-d H:i:s');
        $rows = [];
        foreach ($files as $f) {
            $rows[] = [
                'session_id'       => $session_id,
                'academy_id'       => $academy_id,
                'attach_name'      => $f['attach_name'],
                'attach_file'      => $f['attach_file'],
                'attach_file_type' => $f['attach_file_type'],
                'attach_size'      => $f['attach_size'],
                'created_by'       => $created_by,
                'created_by_type'  => $created_by_type,
                'created_date'     => $now,
                'is_active'        => 1,
            ];
        }
        if (empty($rows)) return [];
        $this->db->table('academy_training_session_attachments')->insertBatch($rows);
        return array_column($rows, 'attach_name');
    }

    /**
     * Get attachment by ID (any is_active value — controller decides)
     */
    public function getAttachmentById(int $id): ?array
    {
        $row = $this->db->table('academy_training_session_attachments')
            ->where('id', $id)
            ->get()->getRowArray();
        return $row ?: null;
    }

    /**
     * Soft delete attachment
     */
    public function deleteSessionAttachment(int $attachment_id): bool
    {
        return (bool) $this->db->table('academy_training_session_attachments')
            ->where('id', $attachment_id)
            ->update(['is_active' => 0]);
    }

    // =====================================================
    // Academy Role Permissions (custom per-academy)
    // =====================================================

    /**
     * Get custom permission overrides for an academy.
     * Returns: [permission_key][role] => bool
     */
    public function getAcademyRolePermissions(int $academy_id): array
    {
        $rows = $this->db->table('academy_role_permissions')
            ->where('academy_id', $academy_id)
            ->get()->getResultArray();

        $map = [];
        foreach ($rows as $row) {
            $map[$row['permission_key']][$row['role']] = (bool)$row['allowed'];
        }
        return $map;
    }

    /**
     * Insert or update a single permission toggle.
     */
    public function togglePermission(int $academy_id, string $permission_key, string $role, bool $allowed): bool
    {
        $existing = $this->db->table('academy_role_permissions')
            ->where('academy_id', $academy_id)
            ->where('permission_key', $permission_key)
            ->where('role', $role)
            ->get()->getRowArray();

        if ($existing) {
            return (bool) $this->db->table('academy_role_permissions')
                ->where('id', $existing['id'])
                ->update(['allowed' => $allowed ? 1 : 0, 'updated_at' => date('Y-m-d H:i:s')]);
        }

        return (bool) $this->db->table('academy_role_permissions')->insert([
            'academy_id'     => $academy_id,
            'permission_key' => $permission_key,
            'role'           => $role,
            'allowed'        => $allowed ? 1 : 0,
            'updated_at'     => date('Y-m-d H:i:s'),
        ]);
    }

    public function getFootballStatsDashboard(int $academy_id, ?int $tour_id = null,$chanel=''): array
    {
        $academyTeamIds = array_column($this->getAcademyTeams($academy_id), 'id');

        if($chanel=='') {
            $tournamentBuilder = $this->db->table('tournament_team tt')
                ->select('t.tour_id, t.name, t.name_en, t.logo')
                ->join('tournament t', 't.tour_id = tt.tour_id', 'inner')
                ->whereIn('tt.team_id', !empty($academyTeamIds) ? $academyTeamIds : [0])
                 ->where('t.tournament_end_date <', date('Y-m-d'))
                ->groupBy('t.tour_id, t.name, t.name_en, t.logo')
                ->orderBy('t.tour_id', 'DESC');
            $tournaments = $tournamentBuilder->get()->getResultArray();
        }else {
            $tournamentBuilder = $this->db->table('tournament_team tt')
                ->select('t.tour_id, t.name, t.name_en, t.logo')
                ->join('tournament t', 't.tour_id = tt.tour_id', 'inner')
                ->where('t.tournament_end_date <', date('Y-m-d'))
                //->where('t.is_active', 1)
                ->groupBy('t.tour_id, t.name, t.name_en, t.logo')
                ->orderBy('t.tour_id', 'DESC');
            $tournaments = $tournamentBuilder->get()->getResultArray();  
        }

        $tourIds = array_map('intval', array_column($tournaments, 'tour_id'));

        if ($tour_id && !in_array($tour_id, $tourIds, true)) {
            $tour_id = null;
        }

        $matchBuilder = $this->db->table('tournament_match_result m')
            ->select('m.*, t1.name as team1_name, t1.logo as team1_logo, t2.name as team2_name, t2.logo as team2_logo, 
                      ts.format as stage_format, ts.order_num as stage_order, ts.final_match')
            ->join('team t1', 't1.id = m.team1_id', 'left')
            ->join('team t2', 't2.id = m.team2_id', 'left')
            ->join('tournament_stages ts', 'ts.stage_id = m.stage_id', 'left')
            ->where('m.match_status', 3);

        if ($tour_id) {
            $matchBuilder->where('m.tour_id', $tour_id);
        } elseif (!empty($tourIds)) {
            $matchBuilder->whereIn('m.tour_id', $tourIds);
        } else {
            $matchBuilder->where('m.tour_id', 0);
        }

        $matches = $matchBuilder
            ->orderBy('m.tour_id', 'ASC')
            ->orderBy('ts.order_num', 'ASC')
            ->orderBy('m.date', 'ASC')
            ->orderBy('m.match_id', 'ASC')
            ->get()
            ->getResultArray();

        $matchIds = array_column($matches, 'match_id');
        // Index matches by id once so per-event lookups are O(1) instead of scanning $matches each time.
        $matchById = [];
        foreach ($matches as $m) {
            $matchById[(int)$m['match_id']] = $m;
        }
        $events = [];
        if (!empty($matchIds)) {
            // Fetch raw events with all potential position source columns
            $events = $this->db->table('tournament_match_event e')
                ->select('e.*, p1.first_name, p1.last_name, p1.image_path, 
                          ape.position_id as ape_main, ape.sub_position_ids as ape_sub,
                          p1.player_position_id as p_main, p1.player_sub_position_id as p_sub')
                ->join('player p1', 'p1.id = e.player_1_id', 'left')
                ->join('academy_player_enrollments ape', 'ape.player_id = p1.id AND ape.academy_id = ' . (int)$academy_id . ' AND ape.is_active = 1 AND ape.is_primary = 1', 'left')
                ->whereIn('e.match_id', $matchIds)
                ->get()
                ->getResultArray();

            // Load position names map for resolution
            $posMapData = $this->db->table('tbl_player_position')->select('id, name_th, name_en')->get()->getResultArray();
            $posMapTh = [];
            $posMapEn = [];
            foreach ($posMapData as $pm) {
                $posMapTh[(string)$pm['id']] = $pm['name_th'];
                $posMapEn[(string)$pm['id']] = $pm['name_en'];
            }

            // Resolve position hierarchy for each event
            foreach ($events as &$e) {
                $resolvedPosId = null;

                if (!empty($e['ape_main'])) {
                    $resolvedPosId = (string)$e['ape_main'];
                } elseif (!empty($e['ape_sub'])) {
                    $subs = json_decode((string)$e['ape_sub'], true);
                    if (is_array($subs) && !empty($subs)) $resolvedPosId = (string)$subs[0];
                } elseif (!empty($e['p_main'])) {
                    $resolvedPosId = (string)$e['p_main'];
                } elseif (!empty($e['p_sub'])) {
                    $subs = json_decode((string)$e['p_sub'], true);
                    if (is_array($subs) && !empty($subs)) $resolvedPosId = (string)$subs[0];
                }

                $e['position_name'] = ($resolvedPosId && isset($posMapTh[$resolvedPosId])) ? $posMapTh[$resolvedPosId] : 'ไม่ระบุ';
                $e['position_code'] = ($resolvedPosId && isset($posMapEn[$resolvedPosId])) ? $posMapEn[$resolvedPosId] : 'N/A';
            }
            unset($e);
        }
        
        $teams = [];
        $goalMinutes = []; // Changed to store per-minute counts
        $halfGoals = ['1st' => 0, '2nd' => 0];
        $positionGoals = [];
        $homeAway = ['home_win' => 0, 'draw' => 0, 'away_win' => 0];
        $subCounts = [];
        $topScorers = [];
        $groupStageMatchesByTeam = [];
        $stageIds = [];

        $touchTeam = function (int $teamId, string $name = '', string $logo = '') use (&$teams) {
            if (!isset($teams[$teamId])) {
                $teams[$teamId] = [
                    'team_id' => $teamId,
                    'team_name' => $name ?: 'ทีม #' . $teamId,
                    'logo' => $logo,
                    'played' => 0,
                    'gf' => 0, 'ga' => 0,
                    'wins' => 0, 'draws' => 0, 'losses' => 0,
                ];
            }
        };

        foreach ($matches as $match) {
            $team1Id = (int)$match['team1_id'];
            $team2Id = (int)$match['team2_id'];
            $team1Score = (int)($match['team1_score'] ?? 0);
            $team2Score = (int)($match['team2_score'] ?? 0);

            $touchTeam($team1Id, $match['team1_name'] ?? '', $match['team1_logo'] ?? '');
            $touchTeam($team2Id, $match['team2_name'] ?? '', $match['team2_logo'] ?? '');

            $teams[$team1Id]['played']++;
            $teams[$team1Id]['gf'] += $team1Score;
            $teams[$team1Id]['ga'] += $team2Score;
            $teams[$team2Id]['played']++;
            $teams[$team2Id]['gf'] += $team2Score;
            $teams[$team2Id]['ga'] += $team1Score;

            if ((int)$match['result'] === 1) {
                $teams[$team1Id]['wins']++;
                $teams[$team2Id]['losses']++;
                $homeAway['home_win']++;
            } elseif ((int)$match['result'] === 2) {
                $teams[$team2Id]['wins']++;
                $teams[$team1Id]['losses']++;
                $homeAway['away_win']++;
            } else {
                $teams[$team1Id]['draws']++;
                $teams[$team2Id]['draws']++;
                $homeAway['draw']++;
            }

            if (!empty($match['stage_id'])) $stageIds[] = (int)$match['stage_id'];

            // CRITICAL: First Round is defined by ts.order_num = 1
            $isFirstRound = ((int)($match['stage_order'] ?? 0) === 1);
            if ($isFirstRound) {
                $groupStageMatchesByTeam[$team1Id][] = [
                    'match_id' => (int)$match['match_id'],
                    'date' => $match['date'] ?? '',
                    'result' => (int)$match['result'] === 1 ? 'W' : ((int)$match['result'] === 0 ? 'D' : 'L'),
                    'group_id' => $match['group_id'] ?? '',
                ];
                $groupStageMatchesByTeam[$team2Id][] = [
                    'match_id' => (int)$match['match_id'],
                    'date' => $match['date'] ?? '',
                    'result' => (int)$match['result'] === 2 ? 'W' : ((int)$match['result'] === 0 ? 'D' : 'L'),
                    'group_id' => $match['group_id'] ?? '',
                ];
            }
        }

        foreach ($events as $e) {
            $minute = (int)$e['minute'];
            $event_id = (int)$e['event_id'];
            $isGoal = ($event_id === 1 || $event_id === 5); // 1=Goal, 5=Own Goal
            $isSub = ($event_id === 2); // 2=Substitution

            if ($isGoal) {
                // 1. Goal Minutes (Actual counts)
                $goalMinutes[$minute] = ($goalMinutes[$minute] ?? 0) + 1;

                // 2. Half Goals
                if ($minute <= 45) $halfGoals['1st']++;
                else $halfGoals['2nd']++;


                // 3. Position Goals
                $code = $e['position_code'] ?: 'N/A';
                $name = $e['position_name'] ?: 'ไม่ระบุ';
                if (!isset($positionGoals[$code])) {
                    $positionGoals[$code] = ['name_th' => $name, 'count' => 0];
                }
                $positionGoals[$code]['count']++;

                // 8. Top Scorers
                if ($event_id === 1 && $e['player_1_id']) {
                    $pid = (int)$e['player_1_id'];
                    if (!isset($topScorers[$pid])) {
                        helper('academy');
                        $topScorers[$pid] = [
                            'name' => ($e['first_name'] . ' ' . mask_last_name($e['last_name'])),
                            'photo' => $e['image_path'],
                            'goals' => 0,
                            'team_id' => $e['team_id']
                        ];
                    }
                    $topScorers[$pid]['goals']++;
                }
            }

            if ($isSub) {
                // 7. Substitutions (Match must be in order_num=1)
                $targetMatch = $matchById[(int)$e['match_id']] ?? null;
                if ($targetMatch && (int)($targetMatch['stage_order'] ?? 0) === 1) {
                    $tid = (int)$e['team_id'];
                    $subCounts[$tid] = ($subCounts[$tid] ?? 0) + 1;
                }
            }
        }

        // Final 4 Resolution
        $finalFourTeamIds = $this->resolveFinalFourTeamIds($matches, $stageIds);
        $twoWinsCount = 0; $comebackCount = 0;
        $twoWinsTeams = []; $comebackTeams = [];

        foreach ($finalFourTeamIds as $teamId) {
            $mList = $groupStageMatchesByTeam[$teamId] ?? [];
            usort($mList, fn($a, $b) => strcmp($a['date'] ?? '', $b['date'] ?? ''));
            $firstTwo = array_slice($mList, 0, 2);
            if (count($firstTwo) >= 2 && $firstTwo[0]['result'] === 'W' && $firstTwo[1]['result'] === 'W') {
                $twoWinsCount++;
                $twoWinsTeams[] = $teams[$teamId];
            }
            if (count(array_filter($mList, fn($x) => $x['result'] === 'L')) > 0) {
                $comebackCount++;
                $comebackTeams[] = $teams[$teamId];
            }
        }

        // Final 4 Stats (10)
        $finalFourGoalStats = [];
        $finalFourGroupOrigin = [];
        foreach ($finalFourTeamIds as $tid) {
            if (!isset($teams[$tid])) continue;
            $finalFourGoalStats[$tid] = ['name' => $teams[$tid]['team_name'], 'logo' => $teams[$tid]['logo'], 'gf' => 0, 'ga' => 0];
            $group = !empty($groupStageMatchesByTeam[$tid]) ? $groupStageMatchesByTeam[$tid][0]['group_id'] : 'N/A';
            $finalFourGroupOrigin[$group] = ($finalFourGroupOrigin[$group] ?? 0) + 1;
        }
        foreach ($matches as $match) {
            if ($this->isFinalFourMatch($match, $stageIds)) {
                $t1 = (int)$match['team1_id']; $t2 = (int)$match['team2_id'];
                if (isset($finalFourGoalStats[$t1])) {
                    $finalFourGoalStats[$t1]['gf'] += (int)($match['team1_score'] ?? 0);
                    $finalFourGoalStats[$t1]['ga'] += (int)($match['team2_score'] ?? 0);
                }
                if (isset($finalFourGoalStats[$t2])) {
                    $finalFourGoalStats[$t2]['gf'] += (int)($match['team2_score'] ?? 0);
                    $finalFourGoalStats[$t2]['ga'] += (int)($match['team1_score'] ?? 0);
                }
            }
        }

        // 7. Most Subs Ranking
        $subRanking = [];
        foreach ($subCounts as $tid => $count) {
            if (isset($teams[$tid])) {
                $subRanking[] = ['name' => $teams[$tid]['team_name'], 'logo' => $teams[$tid]['logo'], 'subs' => $count];
            }
        }
        usort($subRanking, fn($a, $b) => $b['subs'] <=> $a['subs']);
        $subRanking = array_slice($subRanking, 0, 5);

        // 8. Top Scorers sort
        usort($topScorers, fn($a, $b) => $b['goals'] <=> $a['goals']);
        // Sort goal minutes by key (minute)
        ksort($goalMinutes);

        // Calculate Home/Away Percentages
        $totalMatches = count($matches);
        $homeAwayPct = ['home_win' => 0, 'draw' => 0, 'away_win' => 0];
        if ($totalMatches > 0) {
            $homeAwayPct['home_win'] = round(($homeAway['home_win'] / $totalMatches) * 100, 1);
            $homeAwayPct['draw'] = round(($homeAway['draw'] / $totalMatches) * 100, 1);
            $homeAwayPct['away_win'] = round(($homeAway['away_win'] / $totalMatches) * 100, 1);
        }

        return [
            'tournaments' => $tournaments,

            'selected_tour_id' => $tour_id,
            'summary' => ['match_count' => count($matches), 'goal_count' => array_sum($halfGoals)],
            'stats' => [
                'goal_minutes' => $goalMinutes,
                'half_goals' => $halfGoals,
                'position_goals' => $positionGoals,
                'home_away' => $homeAwayPct,
                'two_wins' => ['count' => $twoWinsCount, 'total' => count($finalFourTeamIds), 'teams' => $twoWinsTeams],
                'comeback' => ['count' => $comebackCount, 'total' => count($finalFourTeamIds), 'teams' => $comebackTeams],
                'most_subs' => $subRanking,
                'top_scorers' => $topScorers,
                'final_four_groups' => $finalFourGroupOrigin,
                'final_four_goals' => array_values($finalFourGoalStats)
            ]
        ];
    }

    private function isFirstRoundMatch(array $match): bool
    {
        // Use stage format if available, otherwise fallback to name check
        if (!empty($match['stage_format'])) {
            return (strpos($match['stage_format'], 'league') !== false);
        }

        $round = mb_strtolower((string)($match['match_round'] ?? ''));
        return !empty($match['group_id'])
            || strpos($round, 'รอบแรก') !== false
            || strpos($round, 'group') !== false
            || strpos($round, 'preliminary') !== false;
    }

    private function isFinalFourMatch(array $match, array $stageIds): bool
    {
        // Priority 1: Use final_match flag from tournament_stages table
        if (isset($match['final_match']) && (int)$match['final_match'] === 1) {
            return true;
        }

        // Priority 2: Fallback to round name check
        $round = mb_strtolower((string)($match['match_round'] ?? ''));
        if (
            strpos($round, 'รอบตัดเชือก') !== false ||
            strpos($round, 'รองชิง') !== false ||
            strpos($round, 'ชิงชนะเลิศ') !== false ||
            strpos($round, 'semi') !== false ||
            strpos($round, 'final') !== false
        ) {
            return true;
        }

        return false;
    }

    private function resolveFinalFourTeamIds(array $matches, array $stageIds): array
    {
        if (empty($stageIds)) return [];
        
        // Collect unique stages and their order (Higher order = closer to final)
        $uniqueStages = [];
        foreach($matches as $m) {
            if(!empty($m['stage_id'])) {
                $uniqueStages[(int)$m['stage_id']] = (int)($m['stage_order'] ?? 0);
            }
        }
        arsort($uniqueStages);

        $ids = [];
        foreach ($uniqueStages as $stageId => $order) {
            foreach ($matches as $match) {
                if ((int)$match['stage_id'] === $stageId) {
                    $ids[(int)$match['team1_id']] = true;
                    $ids[(int)$match['team2_id']] = true;
                }
            }
            // Stop when we have at least 8 teams (or no more stages)
            if (count($ids) >= 8) break;
        }

        return array_slice(array_keys($ids), 0, 8);
    }
}
