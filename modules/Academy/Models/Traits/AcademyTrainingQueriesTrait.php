<?php

namespace Modules\Academy\Models\Traits;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;
use Modules\Academy\Models\Traits\AcademyMemberQueriesTrait;
use Modules\Academy\Models\Traits\AcademyIdpQueriesTrait;

trait AcademyTrainingQueriesTrait
{
    // =====================================================
    // Training Groups
    // =====================================================

    /**
     * ดึงกลุ่มฝึกซ้อมทั้งหมดใน Academy
     */
    function getTrainingGroups($academy_id)
    {
        $groups = $this->db->table('academy_training_groups')
            ->where('academy_id', $academy_id)
            ->where('is_active', 1)
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();

        foreach ($groups as &$group) {
            // จำนวนนักเตะ
            $group['player_count'] = $this->db->table('academy_training_group_players')
                ->where('group_id', $group['id'])
                ->where('status', 'active')
                ->countAllResults();

            // จำนวนโค้ช (เฉพาะ is_active=1)
            $group['coach_count'] = $this->db->table('academy_training_group_coaches gc')
                ->join('coach c', 'c.id = gc.coach_id')
                ->where('gc.group_id', $group['id'])
                ->where('c.is_active', 1)
                ->countAllResults();

            // โค้ชในกลุ่ม
            $group['coaches'] = $this->getGroupCoaches($group['id']);
        }

        return $groups;
    }

    /**
     * ดึงข้อมูลกลุ่มฝึกซ้อมเดียว
     */
    function getTrainingGroup($id)
    {
        return $this->db->table('academy_training_groups')
            ->where('id', $id)
            ->get()->getRowArray();
    }

    /**
     * บันทึกกลุ่มฝึกซ้อม (สร้างใหม่/แก้ไข)
     */
    function saveTrainingGroup($data)
    {
        $fields = ['academy_id', 'name', 'description', 'age_range', 'max_players', 'schedule_info', 'is_active'];
        $save = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $save[$f] = $data[$f];
            }
        }

        if (!empty($data['id'])) {
            $this->db->table('academy_training_groups')
                ->where('id', $data['id'])
                ->update($save);
            return $data['id'];
        } else {
            $this->db->table('academy_training_groups')->insert($save);
            return $this->db->insertID();
        }
    }

    /**
     * ลบกลุ่มฝึกซ้อม (soft delete)
     */
    function deleteTrainingGroup($id)
    {
        return $this->db->table('academy_training_groups')
            ->where('id', $id)
            ->update(['is_active' => 0]);
    }

    /**
     * ดึงโค้ชในกลุ่มฝึกซ้อม
     */
    function getGroupCoaches($group_id)
    {
        return $this->db->table('academy_training_group_coaches gc')
            ->select("gc.*, c.first_name, c.last_name, c.nick_name, c.image_path, c.position as coach_position, CONCAT(c.first_name, ' ', c.last_name) as name", false)
            ->join('coach c', 'c.id = gc.coach_id')
            ->where('gc.group_id', $group_id)
            ->where('c.is_active', 1)
            ->orderBy('gc.role', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * ดึง group_id ทั้งหมดที่โค้ชได้รับมอบหมาย (ผ่าน coach_user_id)
     */
    function getCoachAssignedGroupIds($coach_user_id)
    {
        $coach = $this->db->table('coach')
            ->select('id')
            ->where('coach_user_id', $coach_user_id)
            ->get()->getRowArray();

        if (!$coach) {
            return [];
        }

        $rows = $this->db->table('academy_training_group_coaches')
            ->select('group_id')
            ->where('coach_id', $coach['id'])
            ->get()->getResultArray();

        return array_column($rows, 'group_id');
    }

    /**
     * ดึงนักเตะในกลุ่มฝึกซ้อม
     */
    function getGroupPlayers($group_id)
    {
        return $this->db->table('academy_training_group_players gp')
            ->select("p.id as id, gp.id as gp_id, gp.player_id, gp.group_id, gp.status,
                      p.first_name, p.last_name, p.nick_name, p.image_path,
                      COALESCE(ape_gp.shirt_number, p.shirt_number) as shirt_number,
                      p.birth_date, COALESCE(NULLIF(pos_gp.parent_id,0), pos_gp.id, p.player_position_id) as player_position_id,
                      t.name as team_name,
                      CONCAT(p.first_name, ' ', p.last_name) as name", false)
            ->join('player p', 'p.id = gp.player_id')
            ->join('academy_training_groups g', 'g.id = gp.group_id', 'left', false)
            ->join('academy_player_enrollments ape_gp', 'ape_gp.player_id = gp.player_id AND ape_gp.academy_id = g.academy_id AND ape_gp.is_active = 1 AND ape_gp.is_primary = 1', 'left', false)
            ->join('tbl_player_position pos_gp', 'pos_gp.id = COALESCE(NULLIF(ape_gp.position_id,0), p.player_position_id)', 'left', false)
            ->join('team t', 't.id = ape_gp.team_id', 'left')
            ->where('gp.group_id', $group_id)
            ->where('gp.status', 'active')
            ->orderBy('p.first_name', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * เพิ่มโค้ชเข้ากลุ่ม
     */
    function assignCoachToGroup($group_id, $coach_id, $role = 'assistant')
    {
        // ตรวจซ้ำ
        $exists = $this->db->table('academy_training_group_coaches')
            ->where('group_id', $group_id)
            ->where('coach_id', $coach_id)
            ->countAllResults();

        if ($exists > 0) {
            return ['success' => false, 'message' => lang('Common.training_coach_already_assigned')];
        }

        $this->db->table('academy_training_group_coaches')->insert([
            'group_id' => $group_id,
            'coach_id' => $coach_id,
            'role'     => $role
        ]);

        return ['success' => true, 'message' => lang('Common.training_coach_assigned_success')];
    }

    /**
     * ลบโค้ชออกจากกลุ่ม
     */
    function removeCoachFromGroup($group_id, $coach_id)
    {
        return $this->db->table('academy_training_group_coaches')
            ->where('group_id', $group_id)
            ->where('coach_id', $coach_id)
            ->delete();
    }

    /**
     * เพิ่มนักเตะเข้ากลุ่ม
     */
    function assignPlayerToGroup($group_id, $player_id)
    {
        // ตรวจซ้ำ
        $exists = $this->db->table('academy_training_group_players')
            ->where('group_id', $group_id)
            ->where('player_id', $player_id)
            ->countAllResults();

        if ($exists > 0) {
            return ['success' => false, 'message' => lang('Common.training_player_already_in_group')];
        }

        $this->db->table('academy_training_group_players')->insert([
            'group_id'  => $group_id,
            'player_id' => $player_id,
            'status'    => 'active'
        ]);

        return ['success' => true, 'message' => lang('Common.training_player_added_success')];
    }

    /**
     * ลบนักเตะออกจากกลุ่ม
     */
    function removePlayerFromGroup($group_id, $player_id)
    {
        return $this->db->table('academy_training_group_players')
            ->where('group_id', $group_id)
            ->where('player_id', $player_id)
            ->delete();
    }

    /**
     * ดึงโค้ชทั้งหมดใน Academy (สำหรับ dropdown เลือกโค้ช)
     * รองรับโค้ชที่คุมหลายทีมผ่าน coach_teams junction table
     */
    function getAvailableCoaches($academy_id)
    {
        $teamIds = $this->getAcademyTeamIds($academy_id);
        if (empty($teamIds)) return [];

        $teamIdStr = implode(',', array_map('intval', $teamIds));

        $sql = "
            SELECT DISTINCT c.id, c.first_name, c.last_name, c.nick_name, c.image_path, c.position,
                   CONCAT(c.first_name, ' ', c.last_name) AS name
            FROM coach c
            WHERE c.is_active = 1
              AND (
                  c.team_id IN ({$teamIdStr})
                  OR EXISTS (
                      SELECT 1 FROM coach_teams ct
                      WHERE ct.coach_id = c.id AND ct.team_id IN ({$teamIdStr}) AND ct.is_active = 1
                  )
              )
            ORDER BY c.first_name ASC
        ";

        return $this->db->query($sql)->getResultArray();
    }

    /**
     * ดึงนักเตะทั้งหมดใน Academy (สำหรับ dropdown เลือกนักเตะ)
     */
    function getAvailablePlayers($academy_id)
    {
        return $this->db->table('academy_player_enrollments')
            ->select("player.id, player.first_name, player.last_name, player.nick_name, player.image_path, player.birth_date,
                      academy_player_enrollments.shirt_number, academy_player_enrollments.team_id,
                      team.name as team_name,
                      CONCAT(player.first_name, ' ', player.last_name) as name", false)
            ->join('player', 'player.id = academy_player_enrollments.player_id')
            ->join('team', 'team.id = academy_player_enrollments.team_id', 'left')
            ->where('academy_player_enrollments.academy_id', $academy_id)
            ->where('academy_player_enrollments.is_active', 1)
            ->orderBy('player.first_name', 'ASC')
            ->get()->getResultArray();

        // Censor last names for display
        /*
        helper('academy');
        foreach ($players as &$p) {
            if (!empty($p['last_name'])) {
                $p['last_name'] = mask_last_name($p['last_name']);
            }
        }
        */

        return $players;
    }

    /**
     * ดึงนักเตะทั้งหมดใน Academy พร้อมสถานะว่าอยู่ในกลุ่มหรือไม่ (สำหรับ picker)
     */
    function getAllPlayersForGroupPicker($academy_id, $group_id)
    {
        // หา player_id ที่อยู่ในกลุ่มนี้แล้ว
        $inGroupIds = [];
        if ($group_id) {
            $rows = $this->db->table('academy_training_group_players')
                ->select('player_id')
                ->where('group_id', $group_id)
                ->where('status', 'active')
                ->get()->getResultArray();
            foreach ($rows as $r) {
                $inGroupIds[] = (int)$r['player_id'];
            }
        }

        $players = $this->db->table('academy_player_enrollments')
            ->select("player.id, player.first_name, player.last_name, player.nick_name,
                      player.image_path, academy_player_enrollments.shirt_number, academy_player_enrollments.team_id,
                      team.name as team_name,
                      CONCAT(player.first_name, ' ', player.last_name) as full_name", false)
            ->join('player', 'player.id = academy_player_enrollments.player_id')
            ->join('team', 'team.id = academy_player_enrollments.team_id', 'left')
            ->where('academy_player_enrollments.academy_id', $academy_id)
            ->where('academy_player_enrollments.is_active', 1)
            ->orderBy('player.first_name', 'ASC')
            ->get()->getResultArray();

        foreach ($players as &$p) {
            $p['in_group'] = in_array((int)$p['id'], $inGroupIds);
        }
        unset($p);

        return $players;
    }

    /**
     * ตรวจสอบว่านักเตะที่จะเพิ่มมีกลุ่มฝึกซ้อมอื่นอยู่แล้วหรือไม่
     * ใช้ก่อนบันทึก เพื่อแสดง warning แก่ผู้ใช้
     * คืนค่า array ของนักเตะที่อยู่ในกลุ่มอื่น พร้อมชื่อกลุ่ม
     */
    function checkPlayersInOtherGroups($current_group_id, array $player_ids)
    {
        if (empty($player_ids)) return [];

        $playerIdStr = implode(',', array_map('intval', $player_ids));
        $curGroupId  = (int)$current_group_id;

        $sql = "
            SELECT
                p.id AS player_id,
                CONCAT(p.first_name, ' ', p.last_name) AS player_name,
                p.nick_name,
                GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ', ') AS group_names
            FROM academy_training_group_players agp
            JOIN academy_training_groups g ON g.id = agp.group_id
            JOIN player p ON p.id = agp.player_id
            WHERE agp.player_id IN ({$playerIdStr})
              AND agp.group_id != {$curGroupId}
              AND agp.status = 'active'
            GROUP BY p.id, p.first_name, p.last_name, p.nick_name
        ";

        return $this->db->query($sql)->getResultArray();
    }

    /**
     * อัพเดทนักเตะในกลุ่มฝึกซ้อมแบบ batch (เพิ่ม/ลบพร้อมกัน)
     */
    function updateGroupPlayers($group_id, $add_ids, $remove_ids)
    {
        $added   = 0;
        $removed = 0;
        $errors  = [];

        foreach ($remove_ids as $pid) {
            $this->removePlayerFromGroup($group_id, (int)$pid);
            $removed++;
        }

        foreach ($add_ids as $pid) {
            $result = $this->assignPlayerToGroup($group_id, (int)$pid);
            if (!empty($result['success'])) {
                $added++;
            } else {
                $errors[] = $result['message'] ?? '';
            }
        }

        return [
            'success' => empty($errors),
            'added'   => $added,
            'removed' => $removed,
            'errors'  => $errors,
        ];
    }

    /**
     * ดึงโค้ชทั้งหมดใน Academy สำหรับตัวจัดการโค้ชทีม
     */
    function getAcademyAllCoachesForTeamPicker($academy_id, $current_team_id)
    {
        $teamIds = $this->getAcademyTeamIds($academy_id);
        if (empty($teamIds)) return [];

        $teamIdStr = implode(',', array_map('intval', $teamIds));
        $curTeamId = (int)$current_team_id;

        // สถานะคำเชิญ pending ในทีมนี้ (มี table ค่อยเช็ค กันพัง)
        $pendExpr = $this->db->tableExists('coach_invitations')
            ? "IF(EXISTS(SELECT 1 FROM coach_invitations ci WHERE ci.coach_id = c.id AND ci.team_id = {$curTeamId} AND ci.status = 'pending'), 1, 0)"
            : "0";

        // รองรับ coach ที่คุมหลายทีม: in_team = สถานะใน current team ผ่าน coach_teams
        $sql = "
            SELECT DISTINCT c.id, c.first_name, c.last_name, c.nick_name,
                   c.image_path, c.position, c.team_id,
                   t.name AS team_name,
                   CONCAT(c.first_name, ' ', c.last_name) AS full_name,
                   {$pendExpr} AS is_pending,
                   IF(
                       EXISTS(
                           SELECT 1 FROM coach_teams ct
                           WHERE ct.coach_id = c.id AND ct.team_id = {$curTeamId} AND ct.is_active = 1
                       ), 1, 0
                   ) AS in_team,
                   (
                       SELECT GROUP_CONCAT(t2.name ORDER BY t2.name SEPARATOR ', ')
                       FROM coach_teams ct2
                       JOIN team t2 ON t2.id = ct2.team_id
                       WHERE ct2.coach_id = c.id AND ct2.is_active = 1
                         AND ct2.team_id IN ({$teamIdStr})
                         AND ct2.team_id != {$curTeamId}
                   ) AS other_teams
            FROM coach c
            LEFT JOIN team t ON t.id = c.team_id
            WHERE c.is_active = 1
              AND (
                  c.team_id IN ({$teamIdStr})
                  OR EXISTS (
                      SELECT 1 FROM coach_teams ct
                      WHERE ct.coach_id = c.id AND ct.team_id IN ({$teamIdStr}) AND ct.is_active = 1
                  )
              )
            ORDER BY in_team DESC, c.first_name ASC
        ";

        return $this->db->query($sql)->getResultArray();
    }

    /**
     * อัปเดตโค้ชของทีม (เพิ่ม/ลบ) แบบ batch
     * รองรับโค้ชคุมหลายทีม ผ่าน coach_teams junction table
     * พร้อม auto-sync academy_members
     */
    function updateTeamCoaches($academy_id, $team_id, array $add_ids, array $remove_ids)
    {
        $academyTeamIds = $this->getAcademyTeamIds($academy_id);
        if (empty($academyTeamIds)) return false;

        $this->db->transBegin();
        try {
            // --- เพิ่มโค้ชเข้าทีม (ผ่าน coach_teams) ---
            if (!empty($add_ids)) {
                foreach (array_map('intval', $add_ids) as $coachId) {
                    // ตรวจว่าโค้ชอยู่ใน academy นี้
                    $coachExists = $this->db->table('coach')
                        ->where('id', $coachId)
                        ->where('is_active', 1)
                        ->countAllResults();
                    if (!$coachExists) continue;

                    // INSERT INTO coach_teams หรือ reactivate
                    $existing = $this->db->table('coach_teams')
                        ->where('coach_id', $coachId)
                        ->where('team_id', (int)$team_id)
                        ->get()->getRowArray();

                    if ($existing) {
                        $this->db->table('coach_teams')
                            ->where('coach_id', $coachId)
                            ->where('team_id', (int)$team_id)
                            ->update(['is_active' => 1, 'updated_at' => date('Y-m-d H:i:s')]);
                    } else {
                        $this->db->table('coach_teams')->insert([
                            'coach_id'   => $coachId,
                            'team_id'    => (int)$team_id,
                            'is_active'  => 1,
                            'created_at' => date('Y-m-d H:i:s'),
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                    }

                    // ถ้าโค้ชยังไม่มี primary team → set coach.team_id
                    $coachRow = $this->db->table('coach')
                        ->select('team_id, coach_user_id')
                        ->where('id', $coachId)
                        ->get()->getRowArray();
                    if ($coachRow && (int)$coachRow['team_id'] === 0) {
                        $this->db->table('coach')
                            ->where('id', $coachId)
                            ->update(['team_id' => (int)$team_id]);
                    }

                    // auto-create/approve academy_members
                    if (!empty($coachRow['coach_user_id'])) {
                        $coachUserId = (int)$coachRow['coach_user_id'];
                        $memberExists = $this->db->table('academy_members')
                            ->where('academy_id', (int)$academy_id)
                            ->where('coach_user_id', $coachUserId)
                            ->countAllResults();

                        if ($memberExists === 0) {
                            $this->db->table('academy_members')->insert([
                                'academy_id'    => (int)$academy_id,
                                'coach_user_id' => $coachUserId,
                                'role'          => 'coach',
                                'status'        => 'approved',
                                'created_at'    => date('Y-m-d H:i:s'),
                            ]);
                        } else {
                            $this->db->table('academy_members')
                                ->where('academy_id', (int)$academy_id)
                                ->where('coach_user_id', $coachUserId)
                                ->where('status', 'pending')
                                ->update(['status' => 'approved']);
                        }
                    }
                }
            }

            // --- ลบโค้ชออกจากทีม (ผ่าน coach_teams) ---
            if (!empty($remove_ids)) {
                foreach (array_map('intval', $remove_ids) as $coachId) {
                    // deactivate ใน coach_teams
                    $this->db->table('coach_teams')
                        ->where('coach_id', $coachId)
                        ->where('team_id', (int)$team_id)
                        ->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);

                    // ถ้า coach.team_id ชี้ไปที่ทีมนี้ → เปลี่ยนเป็นทีมอื่นที่ยังอยู่ หรือ 0
                    $coachRow = $this->db->table('coach')
                        ->select('team_id, coach_user_id')
                        ->where('id', $coachId)
                        ->get()->getRowArray();

                    if ($coachRow && (int)$coachRow['team_id'] === (int)$team_id) {
                        $otherTeam = $this->db->table('coach_teams')
                            ->select('team_id')
                            ->where('coach_id', $coachId)
                            ->where('is_active', 1)
                            ->where('team_id !=', (int)$team_id)
                            ->get()->getRowArray();

                        $newPrimary = $otherTeam ? (int)$otherTeam['team_id'] : 0;
                        $this->db->table('coach')
                            ->where('id', $coachId)
                            ->update(['team_id' => $newPrimary]);
                    }

                    // ลบ academy_members ถ้าโค้ชไม่มีทีมอื่นใน academy นี้แล้ว
                    if (!empty($coachRow['coach_user_id'])) {
                        $coachUserId = (int)$coachRow['coach_user_id'];
                        $academyTeamIdStr = implode(',', array_map('intval', $academyTeamIds));
                        $hasOtherTeam = $this->db->query(
                            "SELECT COUNT(*) AS cnt FROM coach_teams
                             WHERE coach_id = ? AND is_active = 1
                               AND team_id IN ({$academyTeamIdStr}) AND team_id != ?",
                            [$coachId, (int)$team_id]
                        )->getRowArray();

                        if (empty($hasOtherTeam['cnt'])) {
                            $this->db->table('academy_members')
                                ->where('academy_id', (int)$academy_id)
                                ->where('coach_user_id', $coachUserId)
                                ->where('role', 'coach')
                                ->delete();
                        }
                    }
                }
            }

            $this->db->transCommit();
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            return false;
        }
    }

    /**
     * ดึงนักเตะทั้งหมดใน Academy สำหรับตัวจัดการสมาชิกทีม
     * รวมทุกทีมใน academy (ใช้ใน team-info player picker)
     */
    function getAcademyAllPlayersForTeamPicker($academy_id, $current_team_id)
    {
        $rows = $this->db->table('academy_player_enrollments')
            ->select("player.id, player.first_name, player.last_name, player.nick_name,
                      player.image_path, academy_player_enrollments.shirt_number,
                      academy_player_enrollments.position_id as player_position_id,
                      academy_player_enrollments.team_id, team.name as team_name,
                      CONCAT(player.first_name, ' ', player.last_name) as full_name", false)
            ->join('player', 'player.id = academy_player_enrollments.player_id')
            ->join('team', 'team.id = academy_player_enrollments.team_id', 'left')
            ->where('academy_player_enrollments.academy_id', $academy_id)
            ->where('academy_player_enrollments.is_active', 1)
            // priority: ตรงทีมปัจจุบัน > อยู่ทีมจริง (team_id>0) > team_id=0
            ->orderBy('(academy_player_enrollments.team_id = ' . (int)$current_team_id . ')', 'DESC')
            ->orderBy('(academy_player_enrollments.team_id > 0)', 'DESC')
            ->orderBy('academy_player_enrollments.shirt_number', 'ASC')
            ->get()->getResultArray();

        // Dedup: นักเตะที่มีหลาย enrollment ใน academy เดียวกัน (เช่น team_id=0 + team จริง)
        // ต้องแสดงแถวเดียว/นักเตะ ไม่งั้น picker จะ render 2 การ์ด id ซ้ำ แล้ว JS last-wins
        // ทำให้แถว team_id=0 ทับสถานะ inTeam เป็น false → หลุดจากฝั่งขวา
        // orderBy ข้างบนดันแถว priority สูงสุดมาก่อน จึงเก็บแถวแรกที่เจอ
        $seen = [];
        $result = [];
        foreach ($rows as $r) {
            $pid = (int)$r['id'];
            if (isset($seen[$pid])) continue;
            $seen[$pid] = true;
            $result[] = $r;
        }
        return $result;
    }

    /**
     * อัปเดตสมาชิกนักเตะของทีม (เพิ่ม/ลบ) แบบ batch
     * - add_ids: ย้ายเข้าทีมนี้ (team_id = $team_id)
     * - remove_ids: ย้ายออกจากทีมนี้ (team_id = 0)
     */
    function updateTeamPlayers($academy_id, $team_id, array $add_ids, array $remove_ids)
    {
        $academyTeamIds = $this->getAcademyTeamIds($academy_id);
        if (empty($academyTeamIds)) return false;

        $this->db->transBegin();
        try {
            // --- เพิ่มนักเตะเข้าทีมนี้ ---
            if (!empty($add_ids)) {
                foreach (array_map('intval', $add_ids) as $playerId) {
                    // อัพเดท enrollment ที่มีอยู่ใน academy นี้
                    $existing = $this->db->table('academy_player_enrollments')
                        ->where('player_id', $playerId)
                        ->where('academy_id', (int)$academy_id)
                        ->where('is_active', 1)
                        ->get()->getRowArray();

                    if ($existing) {
                        // อัพเดท team_id ใน enrollment
                        $this->db->table('academy_player_enrollments')
                            ->where('id', $existing['id'])
                            ->update(['team_id' => (int)$team_id]);
                    } else {
                        // สร้าง enrollment ใหม่
                        $this->db->table('academy_player_enrollments')->insert([
                            'player_id'   => $playerId,
                            'academy_id'  => (int)$academy_id,
                            'team_id'     => (int)$team_id,
                            'is_active'   => 1,
                            'enrolled_at' => date('Y-m-d H:i:s'),
                        ]);
                    }

                    // Sync player.team_id (legacy) = enrollment แรกสุดที่ active
                    $this->syncPlayerLegacyTeam($playerId);
                }
            }

            // --- ถอนนักเตะออกจากทีมนี้ ---
            if (!empty($remove_ids)) {
                foreach (array_map('intval', $remove_ids) as $playerId) {
                    // ตั้ง team_id = 0 ใน enrollment (ยังอยู่ใน academy แต่ไม่มีทีม)
                    $this->db->table('academy_player_enrollments')
                        ->where('player_id', $playerId)
                        ->where('academy_id', (int)$academy_id)
                        ->where('team_id', (int)$team_id)
                        ->where('is_active', 1)
                        ->update(['team_id' => 0]);

                    // Sync player.team_id legacy
                    $this->syncPlayerLegacyTeam($playerId);
                }
            }

            $this->db->transCommit();
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            return false;
        }
    }

    /**
     * Sync player.team_id (legacy) จาก primary enrollment ที่ active
     * เรียกหลัง enrollment ถูกแก้ไข เพื่อให้ทุก module ยังทำงานได้
     */
    private function syncPlayerLegacyTeam(int $player_id): void
    {
        // Sync legacy player.team_id = ทีมหลัก (primary) จาก enrollment ที่ active
        // (enrollment คือ source of truth — ตารางสมาชิกทีมเดิมถูกยุบแล้ว)
        $first = $this->db->table('academy_player_enrollments')
            ->select('team_id, shirt_number')
            ->where('player_id', $player_id)
            ->where('is_active', 1)
            ->where('team_id >', 0)
            ->orderBy('is_primary', 'DESC')
            ->orderBy('enrolled_at', 'ASC')
            ->limit(1)
            ->get()->getRowArray();

        $this->db->table('player')
            ->where('id', $player_id)
            ->update([
                'team_id'      => $first ? ((int)$first['team_id'] ?: null) : null,
                'shirt_number' => $first ? $first['shirt_number'] : '',
            ]);
    }

    // =====================================================
    // Training Sessions
    // =====================================================

    /**
     * ดึง sessions ของกลุ่ม (พร้อม filter วันที่/เดือน)
     */
    function getTrainingSessions($group_id, $filters = [])
    {
        $builder = $this->db->table('academy_training_sessions s')
            ->select("s.*, c.first_name as coach_first_name, c.last_name as coach_last_name, CONCAT(c.first_name, ' ', c.last_name) as coach_name, g.name as group_name", false)
            ->join('coach c', 'c.id = s.coach_id', 'left')
            ->join('academy_training_groups g', 'g.id = s.group_id', 'left')
            ->where('s.group_id', $group_id);

        if (!empty($filters['date'])) {
            $builder->where('s.training_date', $filters['date']);
        }
        if (!empty($filters['month'])) {
            $builder->where('MONTH(s.training_date)', $filters['month']);
        }
        if (!empty($filters['year'])) {
            $builder->where('YEAR(s.training_date)', $filters['year']);
        }
        if (!empty($filters['status'])) {
            $builder->where('s.status', $filters['status']);
        }

        return $builder->orderBy('s.training_date', 'DESC')
            ->orderBy('s.start_time', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * ดึง sessions ทั้งหมดของ Academy (ข้ามกลุ่ม)
     */
    function getAcademyTrainingSessions($academy_id, $filters = [])
    {
        $builder = $this->db->table('academy_training_sessions s')
            ->select("s.*, c.first_name as coach_first_name, c.last_name as coach_last_name, CONCAT(c.first_name, ' ', c.last_name) as coach_name, g.name as group_name", false)
            ->join('coach c', 'c.id = s.coach_id', 'left')
            ->join('academy_training_groups g', 'g.id = s.group_id', 'left')
            ->where('s.academy_id', $academy_id);

        if (!empty($filters['date'])) {
            $builder->where('s.training_date', $filters['date']);
        }
        if (!empty($filters['month'])) {
            $builder->where('MONTH(s.training_date)', $filters['month']);
        }
        if (!empty($filters['year'])) {
            $builder->where('YEAR(s.training_date)', $filters['year']);
        }
        if (!empty($filters['status'])) {
            $builder->where('s.status', $filters['status']);
        }
        if (!empty($filters['group_id'])) {
            $builder->where('s.group_id', $filters['group_id']);
        }

        return $builder->orderBy('s.training_date', 'DESC')
            ->orderBy('s.start_time', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * ดึง session เดียว
     */
    function getTrainingSession($id)
    {
        return $this->db->table('academy_training_sessions s')
            ->select("s.*, c.first_name as coach_first_name, c.last_name as coach_last_name, CONCAT(c.first_name, ' ', c.last_name) as coach_name, g.name as group_name, g.academy_id", false)
            ->join('coach c', 'c.id = s.coach_id', 'left')
            ->join('academy_training_groups g', 'g.id = s.group_id', 'left')
            ->where('s.id', $id)
            ->get()->getRowArray();
    }

    /**
     * บันทึก session
     */
    function saveTrainingSession($data)
    {
        $fields = ['academy_id', 'group_id', 'coach_id', 'training_date', 'start_time', 'end_time',
                    'title', 'description', 'location', 'training_type', 'status', 'notes'];
        $save = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $save[$f] = $data[$f];
            }
        }

        if (!empty($data['id'])) {
            $this->db->table('academy_training_sessions')
                ->where('id', $data['id'])
                ->update($save);
            return $data['id'];
        } else {
            $this->db->table('academy_training_sessions')->insert($save);
            return $this->db->insertID();
        }
    }

    // =====================================================
    // Training Topics CRUD
    // =====================================================

    function getTrainingTopics($academy_id)
    {
        return $this->db->table('academy_training_topics')
            ->where('academy_id', $academy_id)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('name', 'ASC')
            ->get()->getResultArray();
    }

    function getTrainingTopic($id)
    {
        return $this->db->table('academy_training_topics')
            ->where('id', $id)
            ->get()->getRowArray();
    }

    function saveTrainingTopic($data)
    {
        $fields = ['academy_id', 'name', 'description', 'sort_order', 'is_active'];
        $save = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $save[$f] = $data[$f];
            }
        }

        if (!empty($data['id'])) {
            $save['updated_at'] = date('Y-m-d H:i:s');
            $this->db->table('academy_training_topics')
                ->where('id', $data['id'])
                ->update($save);
            return $data['id'];
        } else {
            $save['created_at'] = date('Y-m-d H:i:s');
            $this->db->table('academy_training_topics')->insert($save);
            return $this->db->insertID();
        }
    }

    function deleteTrainingTopic($id)
    {
        return $this->db->table('academy_training_topics')
            ->where('id', $id)
            ->update(['is_active' => 0, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * ลบ session
     */
    function deleteTrainingSession($id)
    {
        $this->db->transBegin();

        // ลบ attendance records ก่อน (ป้องกัน FK constraint)
        $this->db->table('academy_training_attendance')
            ->where('session_id', $id)
            ->delete();

        $this->db->table('academy_training_sessions')
            ->where('id', $id)
            ->delete();

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }
        $this->db->transCommit();
        return true;
    }

    // =====================================================
    // Attendance
    // =====================================================

    /**
     * ดึง attendance ของ session
     */
    function getSessionAttendance($session_id)
    {
        return $this->db->table('academy_training_attendance a')
            ->select('a.*, p.first_name, p.last_name, p.nick_name, p.image_path,
                      COALESCE(ape_att.shirt_number, p.shirt_number) as shirt_number', false)
            ->join('player p', 'p.id = a.player_id')
            ->join('academy_training_sessions s', 's.id = a.session_id', 'left', false)
            ->join('academy_training_groups g', 'g.id = s.group_id', 'left', false)
            ->join('academy_player_enrollments ape_att', 'ape_att.player_id = a.player_id AND ape_att.academy_id = g.academy_id AND ape_att.is_active = 1 AND ape_att.is_primary = 1', 'left', false)
            ->where('a.session_id', $session_id)
            ->orderBy('p.first_name', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * บันทึก attendance (bulk: รับ array ของ player records)
     */
    function saveAttendance($session_id, $attendanceData, $recorded_by)
    {
        $this->db->transBegin();

        // ลบ attendance เดิมของ session นี้
        $this->db->table('academy_training_attendance')
            ->where('session_id', $session_id)
            ->delete();

        $recorded_at = date('Y-m-d H:i:s');

        // Insert ใหม่ทั้งหมด
        foreach ($attendanceData as $record) {
            $this->db->table('academy_training_attendance')->insert([
                'session_id'    => $session_id,
                'player_id'     => $record['player_id'],
                'status'        => $record['status'] ?? 'present',
                'check_in_time' => $record['check_in_time'] ?? null,
                'notes'         => $record['notes'] ?? null,
                'recorded_by'   => $recorded_by,
                'recorded_at'   => $recorded_at
            ]);
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }
        $this->db->transCommit();
        return true;
    }

    /**
     * สถิติ attendance ของนักเตะ
     */
    function getPlayerAttendanceStats($player_id, $academy_id, $filters = [])
    {
        $builder = $this->db->table('academy_training_attendance a')
            ->join('academy_training_sessions s', 's.id = a.session_id')
            ->where('a.player_id', $player_id)
            ->where('s.academy_id', $academy_id)
            ->where('s.status', 'completed');

        if (!empty($filters['month'])) {
            $builder->where('MONTH(s.training_date)', $filters['month']);
        }
        if (!empty($filters['year'])) {
            $builder->where('YEAR(s.training_date)', $filters['year']);
        }

        $total = (clone $builder)->countAllResults();
        $present = (clone $builder)->where('a.status', 'present')->countAllResults();
        $late = (clone $builder)->where('a.status', 'late')->countAllResults();
        $absent = (clone $builder)->where('a.status', 'absent')->countAllResults();
        $excused = (clone $builder)->where('a.status', 'excused')->countAllResults();

        return [
            'total'        => $total,
            'present'      => $present,
            'late'         => $late,
            'absent'       => $absent,
            'excused'      => $excused,
            'attendance_rate' => $total > 0 ? round(($present + $late) / $total * 100, 1) : 0
        ];
    }

    // =====================================================
    // Assessments
    // =====================================================

    /**
     * ดึง assessments ทั้งหมดของ Academy
     */
    function getAssessments($academy_id, $filters = [])
    {
        $builder = $this->db->table('academy_assessments a')
            ->select('a.*, c.first_name as coach_first_name, c.last_name as coach_last_name, g.name as group_name')
            ->join('coach c', 'c.id = a.created_by', 'left')
            ->join('academy_training_groups g', 'g.id = a.group_id', 'left')
            ->where('a.academy_id', $academy_id)
            ->where('a.is_active', 1);

        if (!empty($filters['type'])) {
            $builder->where('a.assessment_type', $filters['type']);
        }
        if (!empty($filters['group_id'])) {
            $builder->where('a.group_id', $filters['group_id']);
        }

        return $builder->orderBy('a.assessment_date', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * ดึง assessment เดียว
     */
    function getAssessment($id)
    {
        return $this->db->table('academy_assessments a')
            ->select('a.*, c.first_name as coach_first_name, c.last_name as coach_last_name, g.name as group_name')
            ->join('coach c', 'c.id = a.created_by', 'left')
            ->join('academy_training_groups g', 'g.id = a.group_id', 'left')
            ->where('a.id', $id)
            ->get()->getRowArray();
    }

    /**
     * บันทึก assessment
     */
    function saveAssessment($data)
    {
        $fields = ['academy_id', 'group_id', 'name', 'description', 'assessment_date', 'assessment_type', 'created_by'];
        $save = [];
        foreach ($fields as $f) {
            if (isset($data[$f])) {
                $save[$f] = $data[$f];
            }
        }
        // group_id อาจเป็น empty string → set null
        if (isset($save['group_id']) && $save['group_id'] === '') {
            $save['group_id'] = null;
        }

        if (!empty($data['id'])) {
            $this->db->table('academy_assessments')
                ->where('id', $data['id'])
                ->update($save);
            return $data['id'];
        } else {
            $this->db->table('academy_assessments')->insert($save);
            return $this->db->insertID();
        }
    }

    /**
     * ลบ assessment (soft delete)
     */
    function deleteAssessment($id)
    {
        return $this->db->table('academy_assessments')
            ->where('id', $id)
            ->update(['is_active' => 0]);
    }

    /**
     * ดึง criteria ของ assessment
     */
    function getAssessmentCriteria($assessment_id)
    {
        return $this->db->table('academy_assessment_criteria')
            ->where('assessment_id', $assessment_id)
            ->orderBy('sort_order', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * บันทึก criteria (bulk: ลบเก่า + insert ใหม่)
     */
    function saveAssessmentCriteria($assessment_id, $criteriaList)
    {
        $this->db->transBegin();

        // ลบ criteria เก่า (cascade จะลบ results ด้วย)
        $this->db->table('academy_assessment_criteria')
            ->where('assessment_id', $assessment_id)
            ->delete();

        foreach ($criteriaList as $i => $criteria) {
            $this->db->table('academy_assessment_criteria')->insert([
                'assessment_id' => $assessment_id,
                'name'          => $criteria['name'],
                'description'   => $criteria['description'] ?? null,
                'max_score'     => $criteria['max_score'] ?? 100,
                'sort_order'    => $i
            ]);
        }

        if ($this->db->transStatus() === false) {
            $this->db->transRollback();
            return false;
        }
        $this->db->transCommit();
        return true;
    }

    /**
     * ดึงผลทดสอบของ assessment (แยกตาม player)
     */
    function getAssessmentResults($assessment_id)
    {
        return $this->db->table('academy_assessment_results r')
            ->select('r.*, p.first_name, p.last_name, p.nick_name, p.image_path,
                      COALESCE(ape_ar.shirt_number, p.shirt_number) as shirt_number, c.name as criteria_name, c.max_score', false)
            ->join('player p', 'p.id = r.player_id')
            ->join('academy_assessment_criteria c', 'c.id = r.criteria_id')
            ->join('academy_assessments aa_ar', 'aa_ar.id = r.assessment_id', 'left', false)
            ->join('academy_training_groups g_ar', 'g_ar.id = aa_ar.group_id', 'left', false)
            ->join('academy_player_enrollments ape_ar', 'ape_ar.player_id = r.player_id AND ape_ar.academy_id = g_ar.academy_id AND ape_ar.is_active = 1 AND ape_ar.is_primary = 1', 'left', false)
            ->where('r.assessment_id', $assessment_id)
            ->orderBy('p.first_name', 'ASC')
            ->orderBy('c.sort_order', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * บันทึกผลทดสอบ (bulk)
     */
    function saveAssessmentResults($assessment_id, $results, $assessed_by)
    {
        foreach ($results as $result) {
            // Check if exists
            $existing = $this->db->table('academy_assessment_results')
                ->where('criteria_id', $result['criteria_id'])
                ->where('player_id', $result['player_id'])
                ->get()->getRowArray();

            $data = [
                'assessment_id' => $assessment_id,
                'criteria_id'   => $result['criteria_id'],
                'player_id'     => $result['player_id'],
                'score'         => $result['score'] ?? null,
                'grade'         => $result['grade'] ?? null,
                'notes'         => $result['notes'] ?? null,
                'assessed_by'   => $assessed_by
            ];

            if ($existing) {
                $this->db->table('academy_assessment_results')
                    ->where('id', $existing['id'])
                    ->update($data);
            } else {
                $this->db->table('academy_assessment_results')->insert($data);
            }
        }

        return true;
    }

    /**
     * ดึงประวัติทดสอบของนักเตะ
     */
    function getPlayerAssessmentHistory($player_id, $academy_id)
    {
        return $this->db->table('academy_assessment_results r')
            ->select('r.*, a.name as assessment_name, a.assessment_date, a.assessment_type, c.name as criteria_name, c.max_score')
            ->join('academy_assessments a', 'a.id = r.assessment_id')
            ->join('academy_assessment_criteria c', 'c.id = r.criteria_id')
            ->where('r.player_id', $player_id)
            ->where('a.academy_id', $academy_id)
            ->where('a.is_active', 1)
            ->orderBy('a.assessment_date', 'DESC')
            ->orderBy('c.sort_order', 'ASC')
            ->get()->getResultArray();
    }

    // =====================================================
    // Reports & Statistics
    // =====================================================

    /**
     * สรุปรายวัน — sessions + attendance ของวันที่กำหนด
     */
    function getDailySummary($academy_id, $date)
    {
        $sessions = $this->db->table('academy_training_sessions s')
            ->select('s.*, g.name as group_name, c.first_name as coach_first_name, c.last_name as coach_last_name')
            ->join('academy_training_groups g', 'g.id = s.group_id', 'left')
            ->join('coach c', 'c.id = s.coach_id', 'left')
            ->where('s.academy_id', $academy_id)
            ->where('s.training_date', $date)
            ->orderBy('s.start_time', 'ASC')
            ->get()->getResultArray();

        foreach ($sessions as &$session) {
            $attendance = $this->getSessionAttendance($session['id']);
            $session['total_players'] = count($attendance);
            $session['present_count'] = 0;
            $session['absent_count'] = 0;
            $session['late_count'] = 0;

            foreach ($attendance as $a) {
                if ($a['status'] === 'present') $session['present_count']++;
                elseif ($a['status'] === 'absent') $session['absent_count']++;
                elseif ($a['status'] === 'late') $session['late_count']++;
            }
        }

        return $sessions;
    }

    /**
     * สรุปรายเดือน — สถิติ sessions + attendance ของเดือนที่กำหนด
     */
    function getMonthlySummary($academy_id, $month, $year, $groupId = null)
    {
        // group filter (optional) — ถ้าระบุ จะ scope ทุก metric เฉพาะกลุ่มนั้น
        $groupId = $groupId ? (int)$groupId : null;

        // จำนวน sessions ในเดือน
        $totalBuilder = $this->db->table('academy_training_sessions')
            ->where('academy_id', $academy_id)
            ->where('MONTH(training_date)', $month)
            ->where('YEAR(training_date)', $year);
        if ($groupId) $totalBuilder->where('group_id', $groupId);
        $totalSessions = $totalBuilder->countAllResults();

        $completedBuilder = $this->db->table('academy_training_sessions')
            ->where('academy_id', $academy_id)
            ->where('MONTH(training_date)', $month)
            ->where('YEAR(training_date)', $year)
            ->where('status', 'completed');
        if ($groupId) $completedBuilder->where('group_id', $groupId);
        $completedSessions = $completedBuilder->countAllResults();

        // Attendance rate รวม
        $totalAttBuilder = $this->db->table('academy_training_attendance a')
            ->join('academy_training_sessions s', 's.id = a.session_id')
            ->where('s.academy_id', $academy_id)
            ->where('MONTH(s.training_date)', $month)
            ->where('YEAR(s.training_date)', $year)
            ->where('s.status', 'completed');
        if ($groupId) $totalAttBuilder->where('s.group_id', $groupId);
        $totalAttendance = $totalAttBuilder->countAllResults();

        $presentBuilder = $this->db->table('academy_training_attendance a')
            ->join('academy_training_sessions s', 's.id = a.session_id')
            ->where('s.academy_id', $academy_id)
            ->where('MONTH(s.training_date)', $month)
            ->where('YEAR(s.training_date)', $year)
            ->where('s.status', 'completed')
            ->whereIn('a.status', ['present', 'late']);
        if ($groupId) $presentBuilder->where('s.group_id', $groupId);
        $presentAttendance = $presentBuilder->countAllResults();

        // สถิติรายกลุ่ม
        $groupStatsBuilder = $this->db->table('academy_training_sessions s')
            ->select('g.id as group_id, g.name as group_name, COUNT(s.id) as session_count')
            ->join('academy_training_groups g', 'g.id = s.group_id')
            ->where('s.academy_id', $academy_id)
            ->where('MONTH(s.training_date)', $month)
            ->where('YEAR(s.training_date)', $year)
            ->groupBy('g.id');
        if ($groupId) $groupStatsBuilder->where('s.group_id', $groupId);
        $groupStats = $groupStatsBuilder->get()->getResultArray();

        return [
            'total_sessions'     => $totalSessions,
            'completed_sessions' => $completedSessions,
            'cancelled_sessions' => $totalSessions - $completedSessions,
            'total_attendance'   => $totalAttendance,
            'present_attendance' => $presentAttendance,
            'attendance_rate'    => $totalAttendance > 0 ? round($presentAttendance / $totalAttendance * 100, 1) : 0,
            'group_stats'        => $groupStats
        ];
    }

    /**
     * สถิติรายนักเตะ — attendance + assessment scores
     */
    function getPlayerTrainingStats($player_id, $academy_id)
    {
        $attendanceStats = $this->getPlayerAttendanceStats($player_id, $academy_id);
        $assessmentHistory = $this->getPlayerAssessmentHistory($player_id, $academy_id);

        // คำนวณค่าเฉลี่ย score
        $totalScore = 0;
        $scoreCount = 0;
        foreach ($assessmentHistory as $result) {
            if ($result['score'] !== null && $result['max_score'] > 0) {
                $totalScore += ($result['score'] / $result['max_score']) * 100;
                $scoreCount++;
            }
        }

        return [
            'attendance' => $attendanceStats,
            'assessments' => $assessmentHistory,
            'avg_assessment_score' => $scoreCount > 0 ? round($totalScore / $scoreCount, 1) : null,
            'assessment_count' => $scoreCount
        ];
    }

    /**
     * สรุปผลการทดสอบทั้งหมดของ Academy
     */
    function getAssessmentSummary($academy_id)
    {
        // จำนวนการทดสอบทั้งหมด
        $totalAssessments = $this->db->table('academy_assessments')
            ->where('academy_id', $academy_id)
            ->where('is_active', 1)
            ->countAllResults();

        // จำนวนนักเตะที่ถูกทดสอบ (unique)
        $totalPlayersTested = $this->db->query("
            SELECT COUNT(DISTINCT r.player_id) as cnt
            FROM academy_assessment_results r
            JOIN academy_assessments a ON a.id = r.assessment_id
            WHERE a.academy_id = ? AND a.is_active = 1
        ", [$academy_id])->getRowArray();

        // คะแนนเฉลี่ยรวม (คิดจาก score/max_score * 10)
        $avgRow = $this->db->query("
            SELECT ROUND(AVG(r.score), 1) as avg_score
            FROM academy_assessment_results r
            JOIN academy_assessments a ON a.id = r.assessment_id
            JOIN academy_assessment_criteria c ON c.id = r.criteria_id
            WHERE a.academy_id = ? AND a.is_active = 1 AND r.score IS NOT NULL
        ", [$academy_id])->getRowArray();

        // รายการทดสอบล่าสุด 5 รายการ พร้อมคะแนนเฉลี่ย
        $recentAssessments = $this->db->query("
            SELECT a.id, a.name, a.assessment_date, a.assessment_type, a.group_id,
                   g.name as group_name,
                   ROUND(AVG(r.score), 1) as avg_score,
                   COUNT(DISTINCT r.player_id) as player_count,
                   CONCAT(c.first_name, ' ', c.last_name) as coach_name
            FROM academy_assessments a
            LEFT JOIN academy_training_groups g ON g.id = a.group_id
            LEFT JOIN academy_assessment_results r ON r.assessment_id = a.id
            LEFT JOIN coach c ON c.id = a.created_by
            WHERE a.academy_id = ? AND a.is_active = 1
            GROUP BY a.id
            ORDER BY a.assessment_date DESC
            LIMIT 6
        ", [$academy_id])->getResultArray();

        // สรุปคะแนนเฉลี่ยตามกลุ่ม (latest assessment per group)
        $groupAvg = $this->db->query("
            SELECT g.id as group_id, g.name as group_name,
                   ROUND(AVG(r.score), 1) as avg_score,
                   COUNT(DISTINCT a.id) as assessment_count
            FROM academy_assessments a
            JOIN academy_training_groups g ON g.id = a.group_id
            LEFT JOIN academy_assessment_results r ON r.assessment_id = a.id
            WHERE a.academy_id = ? AND a.is_active = 1 AND r.score IS NOT NULL
            GROUP BY g.id
            ORDER BY g.name
        ", [$academy_id])->getResultArray();

        return [
            'total_assessments'   => $totalAssessments,
            'total_players_tested' => (int)($totalPlayersTested['cnt'] ?? 0),
            'avg_score'           => (float)($avgRow['avg_score'] ?? 0),
            'recent_assessments'  => $recentAssessments,
            'group_avg'           => $groupAvg,
        ];
    }

    /**
     * สถิติรายกลุ่ม
     */
    function getGroupTrainingStats($group_id, $filters = [])
    {
        $builder = $this->db->table('academy_training_sessions')
            ->where('group_id', $group_id)
            ->where('status', 'completed');

        if (!empty($filters['month'])) {
            $builder->where('MONTH(training_date)', $filters['month']);
        }
        if (!empty($filters['year'])) {
            $builder->where('YEAR(training_date)', $filters['year']);
        }

        $sessionCount = (clone $builder)->countAllResults();

        // Attendance
        $attendanceBuilder = $this->db->table('academy_training_attendance a')
            ->join('academy_training_sessions s', 's.id = a.session_id')
            ->where('s.group_id', $group_id)
            ->where('s.status', 'completed');

        if (!empty($filters['month'])) {
            $attendanceBuilder->where('MONTH(s.training_date)', $filters['month']);
        }
        if (!empty($filters['year'])) {
            $attendanceBuilder->where('YEAR(s.training_date)', $filters['year']);
        }

        $totalRecords = (clone $attendanceBuilder)->countAllResults();
        $presentRecords = (clone $attendanceBuilder)->whereIn('a.status', ['present', 'late'])->countAllResults();

        return [
            'session_count'   => $sessionCount,
            'total_records'   => $totalRecords,
            'present_records' => $presentRecords,
            'attendance_rate' => $totalRecords > 0 ? round($presentRecords / $totalRecords * 100, 1) : 0
        ];
    }
}
