<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;

/**
 * ให้สิทธิ์แบบกดครั้งเดียวจากหลังบ้าน
 *
 * สิทธิ์ของคนหนึ่งคนกระจายอยู่หลายตาราง และแต่ละหน้าจออ่านคนละตัว
 * ถ้าเขียนไม่ครบจะเกิดอาการ "เพิ่มสำเร็จแต่เจ้าตัวไม่เห็นอะไรเลย"
 *
 *   ผู้จัดการ academy : academy_members (user_id)
 *   โค้ชประจำทีม     : coach_teams        → รายชื่อโค้ชในทีม ฝั่ง academy
 *                      coach_invitations  → รายการสโมสร ฝั่งโค้ช (ต้อง accepted)
 *                      academy_members    → สิทธิ์ระดับ academy (coach_user_id)
 *                      coach.team_id      → ทีมหลัก (legacy) เซ็ตให้เมื่อยังว่าง
 *
 * ทุกเมธอดที่เขียนข้อมูลเป็น idempotent — กดซ้ำได้ไม่เกิดแถวซ้ำ
 */
class AccessGrantModel extends Model
{
    /** role ที่อนุญาตให้ตั้งให้ user ได้ */
    public const USER_ROLES = ['owner', 'admin', 'viewer'];

    // =====================================================
    // ค้นหา
    // =====================================================

    public function searchAcademies(string $q, int $limit = 20): array
    {
        $builder = $this->db->table('academy')
            ->select('id, name, logo,
                      (SELECT COUNT(*) FROM academy_teams at
                        WHERE at.academy_id = academy.id) AS team_count', false)
            ->where('is_active', 1);

        if ($q !== '') {
            $builder->like('name', $q);
        }

        return $builder->orderBy('name', 'ASC')->limit($limit)->get()->getResultArray();
    }

    /**
     * ค้นหาทีม พร้อมบอกว่าอยู่ academy ไหน (ทีมที่ไม่มี academy ก็ยังเลือกได้)
     */
    public function searchTeams(string $q, int $limit = 20): array
    {
        $builder = $this->db->table('team t')
            ->select('t.id, t.name, t.logo, a.id AS academy_id, a.name AS academy_name')
            ->join('academy_teams at', 'at.team_id = t.id', 'left')
            ->join('academy a', 'a.id = at.academy_id', 'left')
            ->where('t.is_active', 1)
            // ทีมที่ไม่มีชื่อจะ render ออกมาเป็นแถวว่าง เลือกไม่ได้จริง จึงตัดทิ้งจากผลค้นหา
            ->where('t.name IS NOT NULL')
            ->where("TRIM(t.name) !=", '');

        if ($q !== '') {
            $builder->groupStart()
                ->like('t.name', $q)
                ->orLike('a.name', $q)
            ->groupEnd();
        }

        return $builder->orderBy('t.name', 'ASC')->limit($limit)->get()->getResultArray();
    }

    // =====================================================
    // สิทธิ์ที่มีอยู่แล้ว
    // =====================================================

    public function userAcademies(int $userId): array
    {
        return $this->db->table('academy_members am')
            ->select('am.academy_id, am.role, am.status, a.name AS academy_name,
                      (SELECT COUNT(*) FROM academy_teams at
                        WHERE at.academy_id = a.id) AS team_count', false)
            ->join('academy a', 'a.id = am.academy_id')
            ->where('am.user_id', $userId)
            ->orderBy('a.name', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * ทีมของโค้ช พร้อมสถานะรายตาราง เพื่อให้เห็นว่าขาดชิ้นไหน
     */
    public function coachTeams(int $coachId): array
    {
        $rows = $this->db->table('coach_teams ct')
            ->select('ct.team_id, t.name AS team_name, ct.is_active,
                      a.id AS academy_id, a.name AS academy_name')
            ->join('team t', 't.id = ct.team_id')
            ->join('academy_teams at', 'at.team_id = ct.team_id', 'left')
            ->join('academy a', 'a.id = at.academy_id', 'left')
            ->where('ct.coach_id', $coachId)
            ->orderBy('t.name', 'ASC')
            ->get()->getResultArray();

        if (empty($rows)) return [];

        $accepted = array_column(
            $this->db->table('coach_invitations')
                ->select('team_id')
                ->where('coach_id', $coachId)
                ->where('status', 'accepted')
                ->get()->getResultArray(),
            'team_id'
        );
        $accepted = array_map('intval', $accepted);

        foreach ($rows as &$r) {
            // ถ้า false แปลว่าฝั่งโค้ชจะยังไม่เห็นสโมสรนี้
            $r['visible_to_coach'] = in_array((int) $r['team_id'], $accepted, true);
        }
        return $rows;
    }

    // =====================================================
    // ผู้จัดการ academy
    // =====================================================

    public function grantAcademyToUser(int $userId, int $academyId, string $role): array
    {
        if (!in_array($role, self::USER_ROLES, true)) {
            return ['success' => false, 'message' => lang('Common.ag_invalid_role')];
        }
        if (!$this->db->table('user')->where('id', $userId)->countAllResults()) {
            return ['success' => false, 'message' => lang('Common.ag_user_not_found')];
        }
        if (!$this->db->table('academy')->where('id', $academyId)->countAllResults()) {
            return ['success' => false, 'message' => lang('Common.ag_academy_not_found')];
        }

        $existing = $this->db->table('academy_members')
            ->where('academy_id', $academyId)
            ->where('user_id', $userId)
            ->get()->getRowArray();

        if ($existing) {
            $this->db->table('academy_members')
                ->where('id', $existing['id'])
                ->update(['role' => $role, 'status' => 'approved']);
        } else {
            $this->db->table('academy_members')->insert([
                'academy_id' => $academyId,
                'user_id'    => $userId,
                'role'       => $role,
                'status'     => 'approved',
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        }

        return ['success' => true, 'message' => lang('Common.ag_grant_success')];
    }

    public function revokeAcademyFromUser(int $userId, int $academyId): array
    {
        $this->db->table('academy_members')
            ->where('academy_id', $academyId)
            ->where('user_id', $userId)
            ->delete();

        return ['success' => true, 'message' => lang('Common.ag_revoke_success')];
    }

    // =====================================================
    // โค้ชประจำทีม
    // =====================================================

    /**
     * เขียนครบทั้ง 4 จุดในคราวเดียว ให้โค้ชเห็นสโมสรทันทีโดยไม่ต้องรอกดตอบรับ
     */
    public function grantTeamToCoach(int $coachId, int $teamId): array
    {
        $coach = $this->db->table('coach')
            ->select('id, coach_user_id, team_id')
            ->where('id', $coachId)->get()->getRowArray();
        if (!$coach) {
            return ['success' => false, 'message' => lang('Common.ag_coach_not_found')];
        }
        if (!$this->db->table('team')->where('id', $teamId)->countAllResults()) {
            return ['success' => false, 'message' => lang('Common.ag_team_not_found')];
        }

        // ทีมอาจยังไม่ถูกผูกกับ academy ใด — ยังให้สิทธิ์ระดับทีมได้ แต่ข้ามส่วน academy
        $academyId = (int) ($this->db->table('academy_teams')
            ->select('academy_id')
            ->where('team_id', $teamId)
            ->get()->getRowArray()['academy_id'] ?? 0);

        $this->db->transBegin();
        try {
            $now = date('Y-m-d H:i:s');

            // 1) coach_teams — รายชื่อโค้ชในทีม (ฝั่ง academy)
            $ct = $this->db->table('coach_teams')
                ->where('coach_id', $coachId)->where('team_id', $teamId)
                ->get()->getRowArray();
            if ($ct) {
                $this->db->table('coach_teams')->where('id', $ct['id'])
                    ->update(['is_active' => 1, 'updated_at' => $now]);
            } else {
                $this->db->table('coach_teams')->insert([
                    'coach_id'   => $coachId,
                    'team_id'    => $teamId,
                    'is_active'  => 1,
                    'created_at' => $now,
                    'updated_at' => $now,
                ]);
            }

            // 2) coach_invitations — ตัวเดียวที่ทำให้สโมสรโผล่ในหน้าโค้ช
            $inv = $this->db->table('coach_invitations')
                ->where('coach_id', $coachId)->where('team_id', $teamId)
                ->orderBy('id', 'DESC')->get()->getRowArray();
            if ($inv) {
                $this->db->table('coach_invitations')->where('id', $inv['id'])
                    ->update(['status' => 'accepted', 'is_active' => 1, 'responded_at' => $now]);
            } else {
                $this->db->table('coach_invitations')->insert([
                    'coach_id'     => $coachId,
                    'team_id'      => $teamId,
                    'invited_by'   => session()->get('user_id') ?: null,
                    'message'      => null,
                    'status'       => 'accepted',
                    'created_at'   => $now,
                    'responded_at' => $now,
                    'is_active'    => 1,
                ]);
            }

            // 3) academy_members — สิทธิ์ระดับ academy (match ด้วย coach_user_id)
            $coachUserId = (int) ($coach['coach_user_id'] ?? 0);
            if ($academyId > 0 && $coachUserId > 0) {
                $am = $this->db->table('academy_members')
                    ->where('academy_id', $academyId)
                    ->where('coach_user_id', $coachUserId)
                    ->get()->getRowArray();
                if ($am) {
                    if ($am['status'] !== 'approved') {
                        $this->db->table('academy_members')->where('id', $am['id'])
                            ->update(['status' => 'approved']);
                    }
                } else {
                    $this->db->table('academy_members')->insert([
                        'academy_id'    => $academyId,
                        'coach_user_id' => $coachUserId,
                        'role'          => 'coach',
                        'status'        => 'approved',
                        'created_at'    => $now,
                    ]);
                }
            }

            // 4) coach.team_id — ทีมหลัก ตั้งให้เฉพาะตอนยังว่าง (ตาม updateTeamCoaches)
            if ((int) $coach['team_id'] === 0) {
                $this->db->table('coach')->where('id', $coachId)->update(['team_id' => $teamId]);
            }

            $this->db->transCommit();

            return [
                'success'      => true,
                'message'      => lang('Common.ag_grant_success'),
                'academy_id'   => $academyId ?: null,
                'no_academy'   => $academyId === 0,
            ];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => lang('Common.ag_error') . ': ' . $e->getMessage()];
        }
    }

    /**
     * ถอดโค้ชออกจากทีม — ไม่แตะสิทธิ์ระดับ academy เพราะโค้ชอาจคุมทีมอื่นใน academy เดียวกันอยู่
     */
    public function revokeTeamFromCoach(int $coachId, int $teamId): array
    {
        $this->db->transBegin();
        try {
            $this->db->table('coach_teams')
                ->where('coach_id', $coachId)->where('team_id', $teamId)->delete();

            $this->db->table('coach_invitations')
                ->where('coach_id', $coachId)->where('team_id', $teamId)->delete();

            // ทีมหลักชี้ทีมที่เพิ่งถอดออก → ย้ายไปทีมอื่นที่ยังคุมอยู่ ไม่งั้นค้างเป็นทีมที่ไม่มีสิทธิ์แล้ว
            $coach = $this->db->table('coach')->select('team_id')
                ->where('id', $coachId)->get()->getRowArray();
            if ($coach && (int) $coach['team_id'] === $teamId) {
                $next = $this->db->table('coach_teams')
                    ->select('team_id')
                    ->where('coach_id', $coachId)
                    ->where('is_active', 1)
                    ->orderBy('id', 'ASC')->limit(1)->get()->getRowArray();
                $this->db->table('coach')->where('id', $coachId)
                    ->update(['team_id' => $next ? (int) $next['team_id'] : 0]);
            }

            $this->db->transCommit();
            return ['success' => true, 'message' => lang('Common.ag_revoke_success')];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => lang('Common.ag_error') . ': ' . $e->getMessage()];
        }
    }
}
