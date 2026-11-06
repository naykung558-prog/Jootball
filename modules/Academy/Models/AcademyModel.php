<?php

namespace Modules\Academy\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;
use Modules\Academy\Models\Traits\AcademyMemberQueriesTrait;
use Modules\Academy\Models\Traits\AcademyIdpQueriesTrait;
use Modules\Academy\Models\Traits\AcademyTrainingQueriesTrait;

class AcademyModel extends Model
{
    use AcademyTrainingQueriesTrait;
    use AcademyIdpQueriesTrait;
    use AcademyMemberQueriesTrait;
    protected $table      = "academy";
    protected $primaryKey = "id";
    protected $allowedFields = [];

    // =====================================================
    // Academy CRUD
    // =====================================================

    /**
     * ดึงข้อมูล Academy พร้อม geographic data
     */
    function getAcademy($id)
    {
        return $this->db->table('academy')
            ->select('academy.*,
                      tbl_provinces.province_name,
                      tbl_districts.district_name as amphure_name,
                      tbl_subdistricts.subdistrict_name as tambon_name')
            ->join('tbl_provinces', 'tbl_provinces.province_id = academy.province_id', 'left')
            ->join('tbl_districts', 'tbl_districts.district_id = academy.amphure_id', 'left')
            ->join('tbl_subdistricts', 'tbl_subdistricts.subdistrict_id = academy.tambon_id', 'left')
            ->where('academy.id', $id)
            ->get()
            ->getRowArray();
    }

    /**
     * ดึง Academy ที่ user เป็นสมาชิก (owner/admin/coach/viewer)
     */
    function getAcademyByUser($user_id)
    {
        // Super academy operator → return all active academies with virtual owner role
        helper('academy');
        if (isSuperAcademyUser($user_id)) {
            return $this->db->table('academy')
                ->select("academy.*, 'owner' as member_role")
                ->where('academy.is_active', 1)
                ->orderBy('academy.name', 'ASC')
                ->get()
                ->getResultArray();
        }

        return $this->db->table('academy_members')
            ->select('academy.*, academy_members.role as member_role')
            ->join('academy', 'academy.id = academy_members.academy_id')
            ->where('academy_members.user_id', $user_id)
            ->where('academy_members.status', 'approved')
            ->where('academy.is_active', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * ดึง Academy ที่ coach เป็นสมาชิก
     */
    function getAcademyByCoach($coach_user_id)
    {
        return $this->db->table('academy_members')
            ->select('academy.*, academy_members.role as member_role')
            ->join('academy', 'academy.id = academy_members.academy_id')
            ->where('academy_members.coach_user_id', $coach_user_id)
            ->where('academy_members.status', 'approved')
            ->where('academy.is_active', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * บันทึก Academy (สร้างใหม่หรืออัพเดท)
     */
    function saveAcademy($input)
    {
        $data = [];
        $fields = ['name', 'name_en', 'description', 'contact_name', 'contact_phone',
                    'contact_email', 'contact_line', 'address', 'country_id',
                    'province_id', 'amphure_id', 'tambon_id', 'latitude', 'longitude'];

        foreach ($fields as $field) {
            if (isset($input[$field])) {
                $data[$field] = $input[$field] ?: null;
            }
        }

        if (!empty($input['logo'])) {
            $data['logo'] = $input['logo'];
        }

        if (!empty($input['id'])) {
            // Update
            $data['update_date'] = date('Y-m-d H:i:s');
            $this->db->table('academy')
                ->where('id', $input['id'])
                ->update($data);
            return $input['id'];
        } else {
            // Insert
            $data['create_by'] = session()->get('user_id');
            $data['create_date'] = date('Y-m-d H:i:s');
            $data['update_date'] = date('Y-m-d H:i:s');
            $this->db->table('academy')->insert($data);
            $academyId = $this->db->insertID();

            // สร้าง owner ใน academy_members
            $this->db->table('academy_members')->insert([
                'academy_id' => $academyId,
                'user_id' => session()->get('user_id'),
                'role' => 'owner',
                'status' => 'approved',
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->createDefaultTeam($academyId);

            return $academyId;
        }
    }

    /**
     * สร้างทีมเริ่มต้นให้ academy โดยคัดลอกข้อมูลจากตัว academy เอง
     *
     * academy ที่ไม่มีทีมเลยจะรับนักเตะเข้ามาเป็นแถว team_id = 0 ได้
     * (enrollPlayerToAcademy ปล่อยผ่านเมื่อไม่ระบุทีม) นักเตะกลุ่มนั้นจะลอยอยู่
     * ไม่โผล่ในหน้าทีมไหนเลย การมีทีมตั้งต้นไว้ตั้งแต่แรกจึงกันปัญหานี้
     *
     * @return int|null team_id ที่สร้าง หรือ null ถ้ามีทีมอยู่แล้ว/หา academy ไม่เจอ
     */
    public function createDefaultTeam(int $academyId): ?int
    {
        $academy = $this->db->table('academy')->where('id', $academyId)->get()->getRowArray();
        if (!$academy) return null;

        // มีทีมอยู่แล้วไม่ต้องสร้างซ้ำ
        $hasTeam = $this->db->table('academy_teams')->where('academy_id', $academyId)->countAllResults();
        if ($hasTeam > 0) return null;

        $now = date('Y-m-d H:i:s');

        $this->db->table('team')->insert([
            'coach_id'      => 0,
            'name'          => $academy['name'],
            'name_en'       => $academy['name_en'] ?? null,
            'organize_name' => $academy['name'],
            'contact_name'  => $academy['contact_name'] ?? null,
            'contact_phone' => $academy['contact_phone'] ?? null,
            'contact_email' => $academy['contact_email'] ?? null,
            'contact_line'  => $academy['contact_line'] ?? null,
            'country_id'    => $academy['country_id'] ?? null,
            'province_id'   => $academy['province_id'] ?? null,
            'amphure_id'    => $academy['amphure_id'] ?? null,
            'tambon_id'     => $academy['tambon_id'] ?? null,
            'latitude'      => $academy['latitude'] ?? null,
            'longitude'     => $academy['longitude'] ?? null,
            'zone_id'       => $academy['zone_id'] ?? 0,
            'logo'          => $academy['logo'] ?? null,
            'create_by'     => $academy['create_by'] ?? session()->get('user_id'),
            'create_date'   => $now,
            'update_date'   => $now,
            'is_active'     => '1',
        ]);
        $teamId = $this->db->insertID();

        $this->db->table('academy_teams')->insert([
            'academy_id' => $academyId,
            'team_id'    => $teamId,
            'sort_order' => 1,
            'created_at' => $now,
        ]);

        // ดึงนักเตะที่ค้างอยู่แบบ "อยู่ academy ไม่มีทีม" เข้าทีมตั้งต้น
        $this->db->table('academy_player_enrollments')
            ->where('academy_id', $academyId)
            ->where('team_id', 0)
            ->where('is_active', 1)
            ->update(['team_id' => $teamId]);

        return $teamId;
    }

    /**
     * ตรวจชื่อซ้ำ
     */
    function checkDuplicateName($name, $id = null)
    {
        $builder = $this->db->table('academy');
        $builder->where('name', $name);
        $builder->where('is_active', 1);
        if ($id) {
            $builder->where('id !=', $id);
        }
        return $builder->countAllResults() > 0;
    }

    // =====================================================
    // Academy Teams (mapping academy → team)
    // =====================================================

    /**
     * ดึงทีมทั้งหมดใน Academy
     */
    function getAcademyTeams($academy_id)
    {
        return $this->db->table('academy_teams')
            ->select('team.*, academy_teams.sort_order,
                      tbl_zone.description as zone_name,
                      (SELECT COUNT(DISTINCT ape.player_id)
                       FROM academy_player_enrollments ape
                       INNER JOIN player p ON p.id = ape.player_id
                       WHERE ape.team_id = team.id
                         AND ape.is_active = 1
                         AND ape.academy_id = academy_teams.academy_id) as player_count,
                      (SELECT GROUP_CONCAT(CONCAT(c.first_name, \' \', c.last_name) ORDER BY c.first_name SEPARATOR \', \')
                       FROM coach c
                       WHERE c.is_active = 1
                         AND (
                             c.team_id = team.id
                             OR EXISTS (
                                 SELECT 1 FROM coach_teams ct
                                 WHERE ct.coach_id = c.id AND ct.team_id = team.id AND ct.is_active = 1
                             )
                         )) as coach_names', false)
            ->join('team', 'team.id = academy_teams.team_id')
            ->join('tbl_zone', 'tbl_zone.id = team.zone_id', 'left')
            ->where('academy_teams.academy_id', $academy_id)
            ->where('team.is_active', '1')
            ->orderBy('academy_teams.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * ดึงทัวร์นาเมนต์ที่ aktif อยู่ในช่วงเวลาปัจจุบัน
     * Filter by tournament_start_date, tournament_end_date and is_deleted
     */
    function getActiveTournaments()
    {
        $today = date('Y-m-d');

        return $this->db->table('tournament')
            ->select('tour_id, name, name_en, logo, tournament_start_date, tournament_end_date, start_date, end_date')
            ->where('tournament_start_date <=', $today)
            ->where('tournament_end_date >=', $today)
            ->where('is_deleted !=', 1)
            ->where('is_active', 1)
            ->orderBy('tournament_start_date', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * ดึงการแข่งขันของทีมทั้งหมด
     * Query from tournament_match_result table
     */
    function getTeamMatches($teams)
    {
        if (empty($teams)) {
            return [];
        }

        // Get all team IDs
        $teamIds = array_column($teams, 'id');

        // Query matches where team is either team1 or team2
        $matches = $this->db->table('tournament_match_result tmr')
            ->select('tmr.match_id, tmr.tour_id, tmr.match_round, tmr.team1_id, tmr.team2_id,
                      tmr.time as match_time, tmr.date as match_date, tmr.stadium as venue, tmr.match_status,
                      t.name as tournament_name,
                      team1.name as team1_name, team1.logo as team1_logo,
                      team2.name as team2_name, team2.logo as team2_logo')
            ->join('tournament t', 't.tour_id = tmr.tour_id')
            ->join('team team1', 'team1.id = tmr.team1_id', 'left')
            ->join('team team2', 'team2.id = tmr.team2_id', 'left')
            ->whereIn('tmr.team1_id', $teamIds)
            ->orWhereIn('tmr.team2_id', $teamIds)
            ->where('t.is_deleted !=', 1)
            ->where('t.is_active', 1)
            ->orderBy('tmr.date', 'ASC')
            ->orderBy('tmr.time', 'ASC')
            ->get()
            ->getResultArray();

        // Format matches for JavaScript
        $formattedMatches = [];
        foreach ($matches as $match) {
            $formattedMatches[] = [
                'match_id' => $match['match_id'],
                'tour_id' => $match['tour_id'],
                'tournament_name' => $match['tournament_name'],
                'round' => $match['match_round'] ?? '-',
                'match_date' => $match['match_date'],
                'match_time' => $match['match_time'] ? date('H:i', strtotime($match['match_time'])) : '-',
                'team1_id' => $match['team1_id'],
                'team1_name' => $match['team1_name'],
                'team1_logo' => $match['team1_logo'],
                'team2_id' => $match['team2_id'],
                'team2_name' => $match['team2_name'],
                'team2_logo' => $match['team2_logo'],
                'venue' => $match['venue'] ?? '-',
                'status' => $match['match_status'] == 3 ? 'finished' : 'scheduled'
            ];
        }

        return $formattedMatches;
    }

    /**
     * เพิ่มทีมที่มีอยู่แล้วเข้า Academy
     */
    function addTeamToAcademy($academy_id, $team_id)
    {
        // ตรวจว่าทีมนี้อยู่ใน Academy อื่นแล้วหรือไม่
        $exists = $this->db->table('academy_teams')
            ->where('team_id', $team_id)
            ->countAllResults();

        if ($exists > 0) {
            return ['status' => false, 'message' => lang('Common.academy_team_already_in_other')];
        }

        // หา sort_order ถัดไป
        $maxOrder = $this->db->table('academy_teams')
            ->selectMax('sort_order')
            ->where('academy_id', $academy_id)
            ->get()
            ->getRowArray();

        $this->db->table('academy_teams')->insert([
            'academy_id' => $academy_id,
            'team_id' => $team_id,
            'sort_order' => ($maxOrder['sort_order'] ?? 0) + 1,
            'created_at' => date('Y-m-d H:i:s')
        ]);

        // Enroll นักเตะที่อยู่ในทีมเข้า academy_player_enrollments (ทีมนี้ภายใต้ academy เป้าหมาย)
        $players = $this->db->table('academy_player_enrollments ape')
            ->select('ape.player_id, ape.shirt_number, ape.position_id as player_position_id')
            ->join('player', 'player.id = ape.player_id')
            ->where('ape.team_id', $team_id)
            ->where('ape.is_active', 1)
            ->get()
            ->getResultArray();

        $now = date('Y-m-d H:i:s');
        foreach ($players as $p) {
            // ตรวจแถว enrollment เดิม (ไม่ว่าจะ active หรือถูกถอนออกแล้ว)
            // unique key uq_player_academy ผูกที่ (player_id, academy_id) ไม่รวม is_active
            // จึงต้องเช็คทุกแถว แล้ว update แถวเดิมแทน insert เพื่อกัน Duplicate entry
            $existing = $this->db->table('academy_player_enrollments')
                ->where('player_id', $p['player_id'])
                ->where('academy_id', $academy_id)
                ->get()
                ->getRowArray();

            if ($existing) {
                // re-enroll แถวเดิมที่ถูกถอนออก (is_active = 0); ถ้า active อยู่แล้วก็ refresh ทีม
                if ((int)$existing['is_active'] === 0) {
                    $this->db->table('academy_player_enrollments')
                        ->where('id', $existing['id'])
                        ->update([
                            'team_id'      => $team_id,
                            'shirt_number' => $p['shirt_number'] ?? '',
                            'position_id'  => $p['player_position_id'] ?? null,
                            'enrolled_at'  => $now,
                            'left_at'      => null,
                            'is_active'    => 1,
                        ]);
                }
            } else {
                $this->db->table('academy_player_enrollments')->insert([
                    'player_id'   => $p['player_id'],
                    'academy_id'  => $academy_id,
                    'team_id'     => $team_id,
                    'shirt_number'=> $p['shirt_number'] ?? '',
                    'position_id' => $p['player_position_id'] ?? null,
                    'enrolled_at' => $now,
                    'is_active'   => 1,
                ]);
            }
        }

        // Backfill โค้ชของทีมเข้า academy_members (role=coach) — กันเคสโค้ชอยู่ในทีมก่อนทีมเข้า academy
        // แล้วเข้าหน้า academy ไม่ได้ เพราะ getMemberRole ใช้ academy_members เป็นหลัก
        $coaches = $this->db->table('coach_teams ct')
            ->select('c.coach_user_id')
            ->join('coach c', 'c.id = ct.coach_id')
            ->where('ct.team_id', $team_id)
            ->where('ct.is_active', 1)
            ->get()
            ->getResultArray();
        foreach ($coaches as $co) {
            if (empty($co['coach_user_id'])) continue;
            $cuid = (int)$co['coach_user_id'];
            $has = $this->db->table('academy_members')
                ->where('academy_id', $academy_id)
                ->where('coach_user_id', $cuid)
                ->countAllResults();
            if ($has === 0) {
                $this->db->table('academy_members')->insert([
                    'academy_id'    => $academy_id,
                    'coach_user_id' => $cuid,
                    'role'          => 'coach',
                    'status'        => 'approved',
                    'created_at'    => $now,
                ]);
            }
        }

        return ['status' => true, 'message' => lang('Common.academy_team_added_success')];
    }

    /**
     * สร้างทีมใหม่และเพิ่มเข้า Academy
     */
    function createTeamForAcademy($academy_id, $teamData)
    {
        $this->db->transBegin();

        try {
            // สร้างทีมในตาราง team
            $teamData['create_by'] = session()->get('user_id');
            $teamData['coach_id'] = 0;
            $teamData['create_date'] = date('Y-m-d H:i:s');
            $teamData['update_date'] = date('Y-m-d H:i:s');

            $this->db->table('team')->insert($teamData);
            $teamId = $this->db->insertID();

            // เพิ่มเข้า academy_teams
            $maxOrder = $this->db->table('academy_teams')
                ->selectMax('sort_order')
                ->where('academy_id', $academy_id)
                ->get()
                ->getRowArray();

            $this->db->table('academy_teams')->insert([
                'academy_id' => $academy_id,
                'team_id' => $teamId,
                'sort_order' => ($maxOrder['sort_order'] ?? 0) + 1,
                'created_at' => date('Y-m-d H:i:s')
            ]);

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return ['status' => false, 'message' => lang('Common.academy_team_create_error')];
            }

            $this->db->transCommit();
            return ['status' => true, 'team_id' => $teamId, 'message' => lang('Common.academy_team_created_success')];

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'createTeamForAcademy error: ' . $e->getMessage());
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * ลบทีมออกจาก Academy (ไม่ลบทีมจริง แค่ลบ mapping)
     */
    function removeTeamFromAcademy($academy_id, $team_id)
    {
        return $this->db->table('academy_teams')
            ->where('academy_id', $academy_id)
            ->where('team_id', $team_id)
            ->delete();
    }

    /**
     * หา Academy ที่ team_id อยู่
     */
    function getAcademyByTeamId($team_id)
    {
        return $this->db->table('academy_teams')
            ->select('academy.*')
            ->join('academy', 'academy.id = academy_teams.academy_id')
            ->where('academy_teams.team_id', $team_id)
            ->where('academy.is_active', 1)
            ->get()
            ->getRowArray();
    }

    // =====================================================
    // Academy Members
    // =====================================================

    /**
     * ดึงสมาชิกทั้งหมดใน Academy
     */
    function getAcademyMembers($academy_id)
    {
        // แสดงเฉพาะ regular users (user_id ไม่ null, coach_user_id = null)
        // coach accounts จัดการในหน้าผู้ฝึกสอน (tab แยก)
        return $this->db->table('academy_members')
            ->select('academy_members.*,
                      user.name as user_name, user.username, user.profile_image')
            ->join('user', 'user.id = academy_members.user_id', 'left')
            ->where('academy_members.academy_id', $academy_id)
            ->where('academy_members.user_id IS NOT NULL', null, false)
            ->where('academy_members.coach_user_id IS NULL', null, false)
            ->orderBy("FIELD(academy_members.role, 'owner', 'admin', 'coach', 'viewer')", '', false)
            ->get()
            ->getResultArray();
    }

    /**
     * เพิ่มสมาชิกใหม่
     */
    function addMember($academy_id, $data)
    {
        $insert = [
            'academy_id' => $academy_id,
            'role' => $data['role'] ?? 'viewer',
            'status' => $data['status'] ?? 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];

        if (!empty($data['user_id'])) {
            $insert['user_id'] = $data['user_id'];
        }
        if (!empty($data['coach_user_id'])) {
            $insert['coach_user_id'] = $data['coach_user_id'];
        }

        return $this->db->table('academy_members')->insert($insert);
    }

    /**
     * อัพเดท role ของสมาชิก
     */
    function updateMemberRole($id, $role)
    {
        return $this->db->table('academy_members')
            ->where('id', $id)
            ->update(['role' => $role, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * อัพเดท status ของสมาชิก
     */
    function updateMemberStatus($id, $status)
    {
        return $this->db->table('academy_members')
            ->where('id', $id)
            ->update(['status' => $status, 'updated_at' => date('Y-m-d H:i:s')]);
    }

    /**
     * ลบสมาชิก
     */
    function removeMember($id)
    {
        return $this->db->table('academy_members')
            ->where('id', $id)
            ->delete();
    }

    /**
     * ตรวจสอบสิทธิ์ user ใน Academy
     * @return string|null role ถ้ามีสิทธิ์, null ถ้าไม่มี
     */
    function getMemberRole($academy_id, $user_id = null, $coach_user_id = null)
    {
        // Super academy operator → virtual owner of any academy
        if ($user_id) {
            helper('academy');
            if (isSuperAcademyUser($user_id)) {
                return 'owner';
            }
        }

        $builder = $this->db->table('academy_members')
            ->select('role')
            ->where('academy_id', $academy_id)
            ->where('status', 'approved');

        if ($user_id && $coach_user_id) {
            $builder->groupStart()
                ->where('user_id', $user_id)
                ->orWhere('coach_user_id', $coach_user_id)
            ->groupEnd();
        } elseif ($user_id) {
            $builder->where('user_id', $user_id);
        } elseif ($coach_user_id) {
            $builder->where('coach_user_id', $coach_user_id);
        } else {
            return null;
        }

        $result = $builder->get()->getRowArray();
        return $result ? $result['role'] : null;
    }

    // =====================================================
    // Academy Statistics (ข้ามทุกทีม)
    // =====================================================

    /**
     * สถิติรวมของ Academy
     */
    function getAcademyStats($academy_id)
    {
        $teamIds = $this->getAcademyTeamIds($academy_id);

        $stats = [
            'team_count' => count($teamIds),
            'player_count' => 0,
            'coach_count' => 0,
            'member_count' => 0
        ];

        if (!empty($teamIds)) {
            $stats['player_count'] = $this->db->table('academy_player_enrollments')
                ->where('academy_id', $academy_id)
                ->where('is_active', 1)
                ->countAllResults();

            // นับโค้ชที่ไม่ซ้ำ (ใช้ raw query เพราะ distinct+countAllResults ไม่ทำงานถูกต้อง)
            $teamIdStr = implode(',', array_map('intval', $teamIds));
            $coachCountResult = $this->db->query(
                "SELECT COUNT(DISTINCT id) as cnt FROM coach WHERE team_id IN ({$teamIdStr}) AND is_active = 1"
            )->getRowArray();
            $stats['coach_count'] = (int)($coachCountResult['cnt'] ?? 0);
        }

        $stats['member_count'] = $this->db->table('academy_members')
            ->where('academy_id', $academy_id)
            ->where('status', 'approved')
            ->countAllResults();

        return $stats;
    }

    /**
     * ดึง array ของ team_id ทั้งหมดใน Academy
     */
    function getAcademyTeamIds($academy_id)
    {
        $teams = $this->db->table('academy_teams')
            ->select('team_id')
            ->where('academy_id', $academy_id)
            ->get()
            ->getResultArray();

        return array_column($teams, 'team_id');
    }

    /**
     * ดึงนักเตะทั้งหมดจากทุกทีมใน Academy
     */
    function getAcademyPlayers($academy_id)
    {
        $players = $this->db->table('academy_player_enrollments')
            ->select('player.*, academy_player_enrollments.shirt_number as enrollment_shirt_number,
                      academy_player_enrollments.team_id as enrollment_team_id,
                      academy_player_enrollments.position_id as enrollment_position_id,
                      academy_player_enrollments.sub_position_ids,
                      academy_player_enrollments.id as enrollment_id,
                      team.name as team_name')
            ->join('player', 'player.id = academy_player_enrollments.player_id')
            ->join('team', 'team.id = academy_player_enrollments.team_id', 'left')
            ->where('academy_player_enrollments.academy_id', $academy_id)
            ->where('academy_player_enrollments.is_active', 1)
            ->orderBy('team.name', 'ASC')
            ->orderBy('academy_player_enrollments.shirt_number', 'ASC')
            ->get()
            ->getResultArray();

        // Censor last names for display
        /*
        helper('academy');
        foreach ($players as &$player) {
            if (!empty($player['last_name'])) {
                $player['last_name'] = mask_last_name($player['last_name']);
            }
        }
        */

        return $players;
    }

    /**
     * ดึงนักเตะใน Academy แยกเป็น 2 กอง:
     *  - roster:    อยู่ในทีมที่ระบุแล้ว (ผ่าน academy_player_enrollments)
     *  - available: นักเตะ "ทั้งหมดของ Academy" (ตรงกับ /academy/players/{academy_id})
     *                ในแต่ละ row จะมี flag is_in_team ระบุว่าอยู่ในทีม $team_id แล้วหรือไม่
     * ใช้สำหรับ UI 2-column board (Academy ↔ Team)
     */
    function getAcademyTeamRoster(int $academy_id, int $team_id): array
    {
        // ดึง player_id ที่อยู่ในทีมนี้แล้ว
        $rosterIds = array_map('intval', array_column(
            $this->db->table('academy_player_enrollments')
                ->select('player_id')
                ->where('team_id', $team_id)
                ->where('is_active', 1)
                ->get()->getResultArray(),
            'player_id'
        ));

        // ดึงนักเตะทั้งหมดของ Academy
        $all = $this->db->table('academy_player_enrollments ape')
            ->select('player.id, player.first_name, player.last_name, player.nick_name,
                      player.image_path, player.birth_date, player.shirt_number as default_shirt,
                      ape.shirt_number as enrollment_shirt, ape.position_id,
                      tbl_player_position.name_th as position_name_th,
                      tbl_player_position.name_en as position_name_en,
                      ape.team_id as primary_team_id')
            ->join('player', 'player.id = ape.player_id')
            ->join('tbl_player_position', 'tbl_player_position.id = ape.position_id', 'left')
            ->where('ape.academy_id', $academy_id)
            ->where('ape.is_active', 1)
            ->orderBy('player.first_name', 'ASC')
            ->get()->getResultArray();

        // Dedup: query คืน 1 แถวต่อ 1 enrollment ไม่ใช่ต่อ 1 นักเตะ
        // นักเตะที่มีหลาย enrollment ใน academy เดียวกัน (อยู่หลายทีม หรือมีแถว team_id=0
        // ค้างอยู่คู่กับทีมจริง) จึงโผล่ซ้ำทั้งใน roster และ available
        // เลือกแถวที่ตรงทีมปัจจุบันก่อน > แถวที่อยู่ทีมจริง > แถว team_id=0
        // เพื่อให้เบอร์เสื้อ/ตำแหน่งที่แสดงเป็นของทีมนี้ (ลำดับชื่อจาก orderBy ยังคงอยู่
        // เพราะ key ที่ถูกแทนที่ยังอยู่ตำแหน่งเดิมใน array)
        $byPlayer = [];
        foreach ($all as $p) {
            $pid  = (int) $p['id'];
            $tid  = (int) $p['primary_team_id'];
            $rank = $tid === $team_id ? 2 : ($tid > 0 ? 1 : 0);

            if (!isset($byPlayer[$pid]) || $rank > $byPlayer[$pid]['_rank']) {
                $p['_rank']     = $rank;
                $byPlayer[$pid] = $p;
            }
        }
        $all = array_values(array_map(static function ($p) {
            unset($p['_rank']);
            return $p;
        }, $byPlayer));

        // Censor last names for display
        /*
        helper('academy');
        foreach ($all as &$p) {
            if (!empty($p['last_name'])) {
                $p['last_name'] = mask_last_name($p['last_name']);
            }
        }
        */

        // available = ทุกคนใน Academy (มี flag is_in_team)
        // roster    = เฉพาะคนที่อยู่ในทีมนี้
        $available = [];
        $roster    = [];
        foreach ($all as $p) {
            $isInTeam = in_array((int)$p['id'], $rosterIds, true);
            $p['is_in_team'] = $isInTeam;
            $available[] = $p;
            if ($isInTeam) {
                $roster[] = $p;
            }
        }

        // เพิ่มข้อมูลทีมอื่นที่ player อยู่ (เพื่อแสดง badge)
        if (!empty($all)) {
            $allPlayerIds = array_column($all, 'id');
            $teams = $this->db->table('academy_player_enrollments pt')
                ->select('pt.player_id, pt.team_id, t.name as team_name')
                ->join('team t', 't.id = pt.team_id')
                ->where('pt.is_active', 1)
                ->whereIn('pt.player_id', $allPlayerIds)
                ->get()->getResultArray();
            $teamsByPlayer = [];
            foreach ($teams as $t) {
                $teamsByPlayer[$t['player_id']][] = [
                    'team_id'   => (int)$t['team_id'],
                    'team_name' => $t['team_name'],
                ];
            }
            foreach ($roster as &$p) {
                $p['other_teams'] = array_values(array_filter(
                    $teamsByPlayer[$p['id']] ?? [],
                    fn($t) => $t['team_id'] !== $team_id
                ));
            }
            unset($p);
            foreach ($available as &$p) {
                // ฝั่ง Academy: แสดงทีมอื่น ๆ ที่ไม่ใช่ทีมปัจจุบัน
                $p['other_teams'] = array_values(array_filter(
                    $teamsByPlayer[$p['id']] ?? [],
                    fn($t) => $t['team_id'] !== $team_id
                ));
            }
            unset($p);
        }

        return ['roster' => $roster, 'available' => $available];
    }

    /**
     * เพิ่ม player เข้าทีม (academy_player_enrollments) — รองรับ multi-team
     */
    function addPlayerToTeam(int $team_id, int $player_id): array
    {
        $a = $this->db->table('academy_teams')->select('academy_id')
            ->where('team_id', $team_id)->limit(1)->get()->getRowArray();
        if (!$a) {
            return ['success' => false, 'message' => 'ไม่สามารถเพิ่มนักเตะได้'];
        }

        $academyId = (int)$a['academy_id'];

        // แถวของทีมเป้าหมาย (รวม is_active=0 — เคยถูกถอนออก)
        $existing = $this->db->table('academy_player_enrollments')
            ->where('team_id', $team_id)
            ->where('player_id', $player_id)
            ->get()->getRowArray();
        if ($existing && (int)$existing['is_active'] === 1) {
            return ['success' => false, 'message' => 'นักเตะอยู่ในทีมนี้อยู่แล้ว'];
        }

        // แถว "อยู่ academy ไม่มีทีม" (team_id=0) ใน academy เดียวกัน — ยุบทิ้ง/แปลงเมื่อเข้าทีมจริง
        $limbo = $this->db->table('academy_player_enrollments')
            ->where('academy_id', $academyId)
            ->where('player_id', $player_id)
            ->where('team_id', 0)
            ->get()->getRowArray();

        $hasPrimary = $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)
            ->where('is_primary', 1)
            ->where('is_active', 1)
            ->where('team_id >', 0)
            ->countAllResults();
        $isPrimary = $hasPrimary > 0 ? 0 : 1;

        // เบอร์เสื้อ/ตำแหน่งเป็นข้อมูลระดับนักเตะใน academy ไม่ใช่ของทีมใดทีมหนึ่ง
        // แถวไหนที่ยังว่างจึงยกมาจากแถวเดิมของนักเตะคนนี้ใน academy เดียวกัน
        // ไม่งั้นดึงลงทีมแล้วข้อมูลจะกลายเป็นว่าง (แถว insert ใหม่ไม่มีค่าพวกนี้เลย
        // ส่วนแถว limbo ที่ถือข้อมูลไว้ก็ถูกลบทิ้ง)
        $donor = $this->db->table('academy_player_enrollments')
            ->where('academy_id', $academyId)
            ->where('player_id', $player_id)
            ->groupStart()
                ->where("shirt_number !=", '')
                ->orWhere('position_id IS NOT NULL')
            ->groupEnd()
            ->orderBy('is_active', 'DESC')
            ->orderBy('enrolled_at', 'DESC')
            ->get()->getRowArray();

        $carryOver = static function (array $target) use ($donor) {
            $out = [];
            if (!$donor) return $out;

            if (($target['shirt_number'] ?? '') === '' && ($donor['shirt_number'] ?? '') !== '') {
                $out['shirt_number'] = $donor['shirt_number'];
            }
            if (($target['position_id'] ?? null) === null && ($donor['position_id'] ?? null) !== null) {
                $out['position_id']      = $donor['position_id'];
                $out['sub_position_ids'] = $donor['sub_position_ids'] ?? null;
            }
            return $out;
        };

        $this->db->transBegin();
        try {
            if ($existing) {
                // มีแถวทีมนี้อยู่แล้วแต่ถูกถอน (is_active=0) → reactivate แทน insert (กันชน unique)
                $this->db->table('academy_player_enrollments')
                    ->where('id', $existing['id'])
                    ->update(array_merge([
                        'is_primary'  => $isPrimary,
                        'is_active'   => 1,
                        'left_at'     => null,
                        'enrolled_at' => date('Y-m-d H:i:s'),
                    ], $carryOver($existing)));
                // ยุบแถว team_id=0 ที่ซ้ำซ้อน (นักเตะเข้าทีมแล้ว ไม่ใช่ "ไม่มีทีม")
                if ($limbo) {
                    $this->db->table('academy_player_enrollments')->where('id', $limbo['id'])->delete();
                }
            } elseif ($limbo) {
                // ยังไม่มีแถวทีมนี้ แต่มีแถว team_id=0 → แปลงแถวนั้นเป็นทีมจริง (ไม่ insert ใหม่)
                $this->db->table('academy_player_enrollments')
                    ->where('id', $limbo['id'])
                    ->update([
                        'team_id'     => $team_id,
                        'is_primary'  => $isPrimary,
                        'is_active'   => 1,
                        'left_at'     => null,
                        'enrolled_at' => date('Y-m-d H:i:s'),
                    ]);
            } else {
                $this->db->table('academy_player_enrollments')->insert(array_merge([
                    'player_id'   => $player_id,
                    'academy_id'  => $academyId,
                    'team_id'     => $team_id,
                    'is_primary'  => $isPrimary,
                    'is_active'   => 1,
                    'enrolled_at' => date('Y-m-d H:i:s'),
                ], $carryOver(['shirt_number' => '', 'position_id' => null])));
            }

            $this->syncPlayerLegacyTeam($player_id);
            $this->db->transCommit();
            return ['success' => true, 'message' => 'เพิ่มนักเตะเข้าทีมสำเร็จ'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'ไม่สามารถเพิ่มนักเตะได้'];
        }
    }

    /**
     * นำ player ออกจากทีม โดยยังคงสถานะ enrolled ใน academy ไว้
     *
     * academy_player_enrollments เก็บทั้ง "อยู่ academy ไหน" และ "อยู่ทีมไหน" ในแถวเดียวกัน
     * การลบแถวทิ้งจึงถอนนักเตะออกจาก academy ไปด้วย ไม่ใช่แค่ออกจากทีม
     * ถ้าเป็นแถวสุดท้ายของนักเตะใน academy นั้น จึงแปลงเป็น team_id=0
     * ("อยู่ academy แต่ยังไม่มีทีม") แทนการลบ
     */
    function removePlayerFromTeam(int $team_id, int $player_id): array
    {
        $rows = $this->db->table('academy_player_enrollments')
            ->where('team_id', $team_id)
            ->where('player_id', $player_id)
            ->orderBy('id', 'ASC')
            ->get()->getResultArray();

        if (empty($rows)) {
            return ['success' => false, 'message' => 'ไม่พบนักเตะในทีมนี้'];
        }

        $row       = $rows[0];
        $extraIds  = array_column(array_slice($rows, 1), 'id'); // แถวซ้ำ (ถ้ามี) เก็บไว้ไม่ได้
        $academyId = (int) $row['academy_id'];

        // ยังอยู่ทีมอื่นใน academy เดียวกันอยู่ไหม
        $otherTeams = $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)
            ->where('academy_id', $academyId)
            ->where('team_id >', 0)
            ->where('team_id !=', $team_id)
            ->where('is_active', 1)
            ->countAllResults();

        // แถว "อยู่ academy ไม่มีทีม" ที่มีอยู่ก่อนแล้ว
        $limbo = $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)
            ->where('academy_id', $academyId)
            ->where('team_id', 0)
            ->get()->getRowArray();

        $this->db->transBegin();
        try {
            if (!empty($extraIds)) {
                $this->db->table('academy_player_enrollments')->whereIn('id', $extraIds)->delete();
            }

            if ($otherTeams > 0 || $limbo) {
                // มีแถวอื่นยึดโยงกับ academy อยู่แล้ว → ลบแถวของทีมนี้ได้เลย
                // (ถ้าดันแปลงเป็น team_id=0 จะกลายเป็นแถวซ้ำที่ทำให้ตัวเลือกทีมเพี้ยน)
                $this->db->table('academy_player_enrollments')->where('id', $row['id'])->delete();

                // ไม่เหลือทีมแล้วแต่มีแถว limbo ที่ถูกปิดไว้ → ปลุกให้ active เป็นตัวยึด academy
                if ($otherTeams === 0 && $limbo && (int) $limbo['is_active'] !== 1) {
                    $this->db->table('academy_player_enrollments')
                        ->where('id', $limbo['id'])
                        ->update(['is_active' => 1, 'left_at' => null]);
                }
            } else {
                // แถวสุดท้ายใน academy → แปลงเป็น "อยู่ academy ไม่มีทีม" แทนการลบ
                $this->db->table('academy_player_enrollments')
                    ->where('id', $row['id'])
                    ->update([
                        'team_id'    => 0,
                        'is_primary' => 0,
                        'is_active'  => 1,
                        'left_at'    => null,
                    ]);
            }

            // ถ้าแถวที่ถอดออกเป็นทีมหลัก ต้องเลื่อนทีมอื่นขึ้นแทน ไม่งั้นนักเตะจะไม่เหลือทีมหลัก
            // (is_primary เป็นระดับนักเตะ ไม่ผูกกับ academy — ดู addPlayerToTeam)
            if ((int) $row['is_primary'] === 1) {
                $next = $this->db->table('academy_player_enrollments')
                    ->where('player_id', $player_id)
                    ->where('team_id >', 0)
                    ->where('is_active', 1)
                    ->orderBy('enrolled_at', 'ASC')
                    ->limit(1)->get()->getRowArray();

                if ($next) {
                    $this->db->table('academy_player_enrollments')
                        ->where('id', $next['id'])
                        ->update(['is_primary' => 1]);
                }
            }

            $this->syncPlayerLegacyTeam($player_id);
            $this->db->transCommit();
            return ['success' => true, 'message' => 'นำนักเตะออกจากทีมสำเร็จ'];
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['success' => false, 'message' => 'ไม่สามารถนำนักเตะออกจากทีมได้'];
        }
    }

    /**
     * ดึงคำเชิญที่รอการตอบรับของทีมใน Academy
     */
    /**
     * คืน coach_id ที่มีคำเชิญ pending อยู่ในทีมของ academy (หรือเฉพาะทีมที่ระบุ)
     * ใช้ทำ marker "รอตอบรับ" ในหน้าค้นหา/จัดการโค้ช
     */
    public function getPendingCoachIds($academy_id, $team_id = null): array
    {
        if (!$this->db->tableExists('coach_invitations')) return [];
        $teamIds = $team_id ? [(int)$team_id] : $this->getAcademyTeamIds($academy_id);
        if (empty($teamIds)) return [];

        $rows = $this->db->table('coach_invitations')
            ->select('coach_id')
            ->whereIn('team_id', array_map('intval', $teamIds))
            ->where('status', 'pending')
            ->groupBy('coach_id')
            ->get()->getResultArray();
        return array_map('intval', array_column($rows, 'coach_id'));
    }

    function getPendingInvitations($academy_id)
    {
        $teamIds = $this->getAcademyTeamIds($academy_id);
        if (empty($teamIds) || !$this->db->tableExists('coach_invitations')) return [];

        return $this->db->table('coach_invitations')
            ->select('coach_invitations.*, coach.first_name, coach.last_name, coach.nick_name, coach.image_path, coach.position, coach_users.line_picture, team.name as team_name,
                      (SELECT MAX(cc.cert_level_id) FROM coach_certs cc WHERE cc.coach_id = coach.id) AS max_cert_level_id,
                      cl.name AS cert_level_name')
            ->join('coach', 'coach.id = coach_invitations.coach_id', 'inner')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->join('team', 'team.id = coach_invitations.team_id', 'left')
            ->join('coach_certs cc_max', 'cc_max.coach_id = coach.id AND cc_max.cert_level_id = (SELECT MAX(cc.cert_level_id) FROM coach_certs cc WHERE cc.coach_id = coach.id)', 'left')
            ->join('tbl_certs_level cl', 'cl.id = cc_max.cert_level_id', 'left')
            ->whereIn('coach_invitations.team_id', $teamIds)
            ->where('coach_invitations.status', 'pending')
            ->where('coach.is_active', 1)
            ->orderBy('coach_invitations.created_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * ดึงโค้ชทั้งหมดจากทุกทีมใน Academy (ไม่ซ้ำ)
     * รองรับโค้ชที่คุมหลายทีมผ่าน coach_teams junction table
     */
    function getAcademyCoaches($academy_id)
    {
        $teamIds = $this->getAcademyTeamIds($academy_id);
        if (empty($teamIds)) return [];

        $teamIdStr = implode(',', array_map('intval', $teamIds));

        // ดึงโค้ชจาก coach_teams (multi-team) + fallback coach.team_id
        // เพิ่ม join กับ coach_certs, tbl_certs_level และ academy_members เพื่อดึง role
        $sql = "
            SELECT DISTINCT c.*,
                   cu.line_picture,
                   t.name AS team_name,
                   (
                       SELECT GROUP_CONCAT(t2.name ORDER BY t2.name SEPARATOR ', ')
                       FROM coach_teams ct2
                       JOIN team t2 ON t2.id = ct2.team_id
                       WHERE ct2.coach_id = c.id AND ct2.is_active = 1
                         AND ct2.team_id IN ({$teamIdStr})
                   ) AS all_team_names,
                   (
                       SELECT GROUP_CONCAT(ct3.team_id ORDER BY ct3.team_id SEPARATOR ',')
                       FROM coach_teams ct3
                       WHERE ct3.coach_id = c.id AND ct3.is_active = 1
                         AND ct3.team_id IN ({$teamIdStr})
                   ) AS team_ids_str,
                   (
                       SELECT MAX(cc.cert_level_id)
                       FROM coach_certs cc
                       WHERE cc.coach_id = c.id
                   ) AS max_cert_level_id,
                   cl.name AS cert_level_name,
                   am.id   AS member_id,
                   am.role AS member_role,
                   am.status AS member_status
            FROM coach c
            LEFT JOIN coach_users cu ON cu.id = c.coach_user_id
            LEFT JOIN team t ON t.id = c.team_id
            LEFT JOIN coach_certs cc_max ON cc_max.coach_id = c.id
                AND cc_max.cert_level_id = (
                    SELECT MAX(cc.cert_level_id)
                    FROM coach_certs cc
                    WHERE cc.coach_id = c.id
                )
            LEFT JOIN tbl_certs_level cl ON cl.id = cc_max.cert_level_id
            INNER JOIN academy_members am ON am.coach_user_id = c.coach_user_id
                AND am.academy_id = {$academy_id}
            WHERE c.is_active = 1
            ORDER BY c.first_name ASC
        ";

        $coaches = $this->db->query($sql)->getResultArray();

        // Censor last names for display
        /*
        helper('academy');
        foreach ($coaches as &$coach) {
            if (!empty($coach['last_name'])) {
                $coach['last_name'] = mask_last_name($coach['last_name']);
            }
        }
        */

        return $coaches;
    }

    // =====================================================
    // Team-Level Methods (ใช้จัดการทีมภายใน Academy)
    // ยังคง query ตาราง team เดิม
    // =====================================================

    /**
     * ดึงข้อมูลทีม (เหมือน TeamModel::get_Teams)
     */
    function getTeam($team_id)
    {
        return $this->db->table('team')
            ->select("team.*, tbl_zone.description as zone_name,
                      tbl_districts.district_name as amphure_name,
                      tbl_subdistricts.subdistrict_name as tambon_name")
            ->join('tbl_zone', 'tbl_zone.id = team.zone_id', 'left')
            ->join('tbl_districts', 'tbl_districts.district_id = team.amphure_id', 'left')
            ->join('tbl_subdistricts', 'tbl_subdistricts.subdistrict_id = team.tambon_id', 'left')
            ->where('team.id', $team_id)
            ->get()
            ->getRowArray();
    }

    /**
     * ตรวจว่า team_id อยู่ใน academy_id หรือไม่
     */
    function isTeamInAcademy($academy_id, $team_id)
    {
        return $this->db->table('academy_teams')
            ->where('academy_id', $academy_id)
            ->where('team_id', $team_id)
            ->countAllResults() > 0;
    }

    /**
     * ดึงโค้ชของทีม (reuse logic จาก TeamModel)
     */
    function getTeamCoaches($teamId, $includePending = false)
    {
        $coaches = [];
        $addedCoachIds = [];

        // 1. จาก coach_invitations (accepted)
        if ($this->db->tableExists('coach_invitations')) {
            $invitationCoaches = $this->db->table('coach_invitations')
                ->select('coach.*, coach_users.line_picture, coach_invitations.status')
                ->join('coach', 'coach.id = coach_invitations.coach_id', 'inner')
                ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
                ->where('coach_invitations.team_id', $teamId)
                ->where('coach_invitations.status', 'accepted')
                ->where('coach.is_active', 1)
                ->get()
                ->getResultArray();

            foreach ($invitationCoaches as $coach) {
                if (!in_array($coach['id'], $addedCoachIds)) {
                    $coach['source'] = 'invitation';
                    $coach['approval_status'] = 'approved';
                    $coaches[] = $coach;
                    $addedCoachIds[] = $coach['id'];
                }
            }
        }

        // 2. จาก coach_teams
        if ($this->db->tableExists('coach_teams')) {
            $teamCoaches = $this->db->table('coach_teams')
                ->select('coach.*, coach_users.line_picture, coach_teams.role')
                ->join('coach', 'coach.id = coach_teams.coach_id', 'inner')
                ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
                ->where('coach_teams.team_id', $teamId)
                ->where('coach_teams.is_active', 1)
                ->where('coach.is_active', 1)
                ->get()
                ->getResultArray();

            foreach ($teamCoaches as $coach) {
                if (!in_array($coach['id'], $addedCoachIds)) {
                    $coach['source'] = 'coach_teams';
                    $coach['approval_status'] = 'approved';
                    $coaches[] = $coach;
                    $addedCoachIds[] = $coach['id'];
                }
            }
        }

        // 3. จาก coach.team_id
        $directCoaches = $this->db->table('coach')
            ->select('coach.*, coach_users.line_picture')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->where('coach.team_id', $teamId)
            ->where('coach.is_active', 1)
            ->get()
            ->getResultArray();

        foreach ($directCoaches as $coach) {
            if (!in_array($coach['id'], $addedCoachIds)) {
                $coach['source'] = 'direct';
                $coach['approval_status'] = 'approved';
                $coaches[] = $coach;
                $addedCoachIds[] = $coach['id'];
            }
        }

        // 4. pending coach requests
        if ($includePending && $this->db->tableExists('coach_team_requests')) {
            $pendingCoaches = $this->db->table('coach_team_requests')
                ->select('coach.*, coach_users.line_picture, coach_team_requests.role_requested as role')
                ->join('coach', 'coach.id = coach_team_requests.coach_id', 'inner')
                ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
                ->where('coach_team_requests.team_id', $teamId)
                ->where('coach_team_requests.status', 'pending')
                ->where('coach.is_active', 1)
                ->get()
                ->getResultArray();

            foreach ($pendingCoaches as $coach) {
                if (!in_array($coach['id'], $addedCoachIds)) {
                    $coach['source'] = 'request';
                    $coach['approval_status'] = 'pending';
                    $coaches[] = $coach;
                    $addedCoachIds[] = $coach['id'];
                }
            }
        }

        // เพิ่ม role display names
        $positionMapping = [
            'team_manager' => lang('Common.coach_role_team_manager'),
            'head_coach' => lang('Common.coach_role_head_coach'),
            'coach' => lang('Common.coach_role_coach'),
            'assistant_coach' => lang('Common.coach_role_assistant_coach'),
            'team_staff' => lang('Common.coach_role_team_staff'),
            'trainer' => lang('Common.coach_role_trainer'),
            'scout' => lang('Common.coach_role_scout')
        ];

        // Query roles from coach_teams table
        $coachRoles = [];
        $coachTeamsQuery = $this->db->table('coach_teams')
            ->select('*')
            ->where('team_id', $teamId)
            ->where('is_active', 1)
            ->get()
            ->getResultArray();

        foreach ($coachTeamsQuery as $ct) {
            $coachRoles[$ct['coach_id']] = $ct['role'];
        }

        foreach ($coaches as &$coach) {
            // Use role from coach_teams if available
            if (isset($coachRoles[$coach['id']])) {
                $role = $coachRoles[$coach['id']];
                $coach['role_name'] = $positionMapping[$role] ?? lang('Common.coach_role_coach');
            } elseif (!empty($coach['position']) && isset($positionMapping[$coach['position']])) {
                $coach['role_name'] = $positionMapping[$coach['position']];
            } else {
                $role = $coach['role'] ?? 'head_coach';
                $coach['role_name'] = $positionMapping[$role] ?? lang('Common.coach_role_coach');
            }
        }

        foreach ($coachTeamsQuery as $ct) {
            $coachRoles[$ct['coach_id']] = $ct['role_display_label'];
        }

        foreach ($coaches as &$coach) {
            // Use role from coach_teams if available
            if (isset($coachRoles[$coach['id']])) {
                $role = $coachRoles[$coach['id']];
                $coach['role_name_display'] = $positionMapping[$role] ?? lang('Common.coach_role_coach');
            } elseif (!empty($coach['position']) && isset($positionMapping[$coach['position']])) {
                $coach['role_name_display'] = $positionMapping[$coach['position']];
            } else {
                $role = $coach['role'] ?? 'head_coach';
                $coach['role_name_display'] = $positionMapping[$role] ?? lang('Common.coach_role_coach');
            }
        }

        return $this->hydrateCoachMediaFallback($coaches);
    }

    /**
     * เติมข้อมูล media ของ coach จาก assignment อื่นใน coach_user_id เดียวกัน
     * ช่วยกรณีโค้ชคนเดียวกันอยู่หลาย section แต่บาง record ไม่มีรูป
     */
    protected function hydrateCoachMediaFallback(array $coaches): array
    {
        if (empty($coaches)) {
            return $coaches;
        }

        $coachUserIds = [];
        foreach ($coaches as $coach) {
            if (!empty($coach['coach_user_id'])) {
                $coachUserIds[] = (int) $coach['coach_user_id'];
            }
        }
        $coachUserIds = array_values(array_unique(array_filter($coachUserIds)));

        if (empty($coachUserIds)) {
            return $coaches;
        }

        $fallbackRows = $this->db->table('coach')
            ->select('coach.coach_user_id, coach.image_path, coach.id_card_image, coach_users.line_picture')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->whereIn('coach.coach_user_id', $coachUserIds)
            ->where('coach.is_active', 1)
            ->orderBy('(CASE WHEN coach.image_path IS NOT NULL AND coach.image_path != "" THEN 1 ELSE 0 END)', 'DESC', false)
            ->orderBy('(CASE WHEN coach.id_card_image IS NOT NULL AND coach.id_card_image != "" THEN 1 ELSE 0 END)', 'DESC', false)
            ->orderBy('(CASE WHEN coach_users.line_picture IS NOT NULL AND coach_users.line_picture != "" THEN 1 ELSE 0 END)', 'DESC', false)
            ->orderBy('coach.id', 'DESC')
            ->get()
            ->getResultArray();

        $fallbackByUser = [];
        foreach ($fallbackRows as $row) {
            $userId = (int) ($row['coach_user_id'] ?? 0);
            if (!$userId || isset($fallbackByUser[$userId])) {
                continue;
            }
            $fallbackByUser[$userId] = $row;
        }

        foreach ($coaches as &$coach) {
            $userId = (int) ($coach['coach_user_id'] ?? 0);
            if (!$userId || empty($fallbackByUser[$userId])) {
                continue;
            }

            $fallback = $fallbackByUser[$userId];
            if (empty($coach['image_path']) && !empty($fallback['image_path'])) {
                $coach['image_path'] = $fallback['image_path'];
            }
            if (empty($coach['id_card_image']) && !empty($fallback['id_card_image'])) {
                $coach['id_card_image'] = $fallback['id_card_image'];
            }
            if (empty($coach['line_picture']) && !empty($fallback['line_picture'])) {
                $coach['line_picture'] = $fallback['line_picture'];
            }
        }

        return $coaches;
    }

    /**
     * ดึงโค้ชตาม IDs พร้อมรักษาลำดับ
     */
    function getCoachesByIds($coachIds, $teamId = null)
    {
        if (empty($coachIds)) return [];

        $query = $this->db->table('coach')
            ->select('coach.*, coach_users.line_picture')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->whereIn('coach.id', $coachIds)
            ->where('coach.is_active', 1);

        if (count($coachIds) > 1) {
            $query->orderBy("FIELD(coach.id, " . implode(',', array_map('intval', $coachIds)) . ")", '', false);
        }

        $coaches = $query->get()->getResultArray();

        $positionMapping = [
            'team_manager' => lang('Common.coach_role_team_manager'),
            'head_coach' => lang('Common.coach_role_head_coach'),
            'coach' => lang('Common.coach_role_coach'),
            'assistant_coach' => lang('Common.coach_role_assistant_coach'),
            'team_staff' => lang('Common.coach_role_team_staff'),
            'trainer' => lang('Common.coach_role_trainer'),
            'scout' => lang('Common.coach_role_scout')
        ];

        foreach ($coaches as &$coach) {
            if (!empty($coach['position']) && isset($positionMapping[$coach['position']])) {
                $coach['role_name'] = $positionMapping[$coach['position']];
            } else {
                $role = $coach['role'] ?? 'head_coach';
                $coach['role_name'] = $positionMapping[$role] ?? lang('Common.coach_role_coach');
            }

            $coach['approval_status'] = 'approved';

            $coach['role_name_display'] = '';

            if ($teamId && $this->db->tableExists('coach_team_requests')) {

                $pendingRequest = $this->db->table('coach_team_requests')
                    ->where('coach_id', $coach['id'])
                    ->where('team_id', $teamId)
                    ->where('status', 'pending')
                    ->get()
                    ->getRowArray();

                if ($pendingRequest) {
                    $coach['approval_status'] = 'pending';
                    $coach['role'] = $pendingRequest['role_requested'] ?? $coach['role'];
                    $coach['role_name'] = $positionMapping[$coach['role']] ?? lang('Common.coach_role_coach');
                }

                $query = $this->db->table('coach_teams')
                    ->select('*')
                    ->where('coach_teams.coach_id', $coach['id'])
                    ->where('coach_teams.team_id', $teamId);
                $coach_teams = $query->get()->getRowArray();
                if ($coach_teams) {
                    $coach['role_name_display'] = $positionMapping[$coach_teams['role_display_label']] ?? $coach['role_name'];
                }

            }
        }

        return $this->hydrateCoachMediaFallback($coaches);
    }

    // =====================================================
    // Chart Selection & Jersey (team-level, reuse เดิม)
    // =====================================================

    function getPlayerChartSelection($team_id)
    {
        $result = $this->db->table('team_player_chart_selection')
            ->where('team_id', $team_id)
            ->get()->getRowArray();

        if ($result && !empty($result['player_ids'])) {
            return json_decode($result['player_ids'], true);
        }
        return [];
    }

    function getPlayerOldChartSelection($team_id, $tournamentId)
    {
        // 1. ดึงรายชื่อนักเตะที่เคยส่งในทัวร์นาเม้นท์นี้ (จากประวัติการส่ง)
        $submitted = $this->db->table('tournament_submitted_players as ts')
            ->select('ts.player_id')
            ->join('tournament_team as tt', 'ts.tour_team_id = tt.tour_team_id', 'inner')
            ->where('tt.tour_id', $tournamentId)
            ->where('tt.team_id', $team_id)
            ->get()
            ->getResultArray();

        if (empty($submitted)) {
            return [];
        }

        $submittedIds = array_values(array_unique(array_column($submitted, 'player_id')));

        // 2. คืนเฉพาะนักเตะที่ยัง active enrollment อยู่จริง (v2: enrollments เป็น source of truth)
        //    เช็คภายใน family (parent + subs) เพราะทีมลูกดึงนักเตะจากทีมแม่
        //    READ-ONLY: กรองค่าที่ return เท่านั้น — ไม่ลบ tournament_submitted_players (กันลบประวัติ valid)
        $fam = $this->teamFamilyIds((int) $team_id);
        if (empty($fam)) $fam = [(int) $team_id];

        $valid = $this->db->table('academy_player_enrollments')
            ->select('player_id')
            ->whereIn('player_id', $submittedIds)
            ->whereIn('team_id', $fam)
            ->where('is_active', 1)
            ->get()
            ->getResultArray();

        return !empty($valid) ? array_values(array_unique(array_column($valid, 'player_id'))) : [];
    }

    /** family (สโมสรเดียวกัน: parent + subs) จาก team_id — ทีมลูกใช้ roster ของทีมแม่ */
    private function teamFamilyIds(int $teamId): array
    {
        if ($teamId <= 0) return [];
        $ti   = $this->db->table('team')->select('id, parent_team_id')->where('id', $teamId)->get()->getRowArray();
        $root = (!empty($ti['parent_team_id'])) ? (int) $ti['parent_team_id'] : $teamId;
        $rows = $this->db->table('team')->select('id')
            ->groupStart()->where('id', $root)->orWhere('parent_team_id', $root)->groupEnd()
            ->get()->getResultArray();
        return array_map('intval', array_column($rows, 'id'));
    }


    function savePlayerChartSelection($team_id, $player_ids)
    {
        $existing = $this->db->table('team_player_chart_selection')
            ->where('team_id', $team_id)->get()->getRowArray();

        $data = [
            'team_id' => $team_id,
            'player_ids' => json_encode($player_ids),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $result = $this->db->table('team_player_chart_selection')
                ->where('id', $existing['id'])->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $result = $this->db->table('team_player_chart_selection')->insert($data);
        }

        // Sync tournament_submitted_players: ลบนักเตะที่ถูกเอาออกจาก chart
        //$this->syncSubmittedPlayersWithSelection($team_id, $player_ids);

        return $result;
    }

    /**
     * Sync tournament_submitted_players กับ chart selection ปัจจุบัน
     */
    function syncSubmittedPlayersWithSelection($team_id, $selectedPlayerIds)
    {
        $submittedTourTeams = $this->db->table('tournament_team')
            ->select('tour_team_id')
            ->where('team_id', $team_id)
            ->where('player_list_status', 1)
            ->get()
            ->getResultArray();

        if (empty($submittedTourTeams)) return;

        $tourTeamIds = array_column($submittedTourTeams, 'tour_team_id');
        $selectedPlayerIds = is_array($selectedPlayerIds) ? array_unique(array_filter($selectedPlayerIds)) : [];

        if (empty($selectedPlayerIds)) {
            // ถ้าไม่มีนักเตะเลือกเลย ลบทั้งหมด
            $this->db->table('tournament_submitted_players')
                ->whereIn('tour_team_id', $tourTeamIds)
                ->delete();
        } else {
            // 1. ลบนักเตะที่ถูกเอาออกจาก selection
            $this->db->table('tournament_submitted_players')
                ->whereIn('tour_team_id', $tourTeamIds)
                ->whereNotIn('player_id', $selectedPlayerIds)
                ->delete();

            // 2. เพิ่มนักเตะใหม่ที่ยังไม่มีในทัวร์นาเมนต์ (Sync เพิ่ม)
            foreach ($tourTeamIds as $tourTeamId) {
                // ดึงรายชื่อที่มีอยู่แล้วในทัวร์นี้
                $existing = $this->db->table('tournament_submitted_players')
                    ->select('player_id')
                    ->where('tour_team_id', $tourTeamId)
                    ->get()
                    ->getResultArray();
                $existingIds = array_column($existing, 'player_id');

                // หา ID ที่ยังไม่มี
                $newIds = array_diff($selectedPlayerIds, $existingIds);

                foreach ($newIds as $newId) {
                    $this->db->table('tournament_submitted_players')->insert([
                        'tour_team_id' => $tourTeamId,
                        'player_id'    => $newId,
                        'created_at'   => date('Y-m-d H:i:s')
                    ]);
                }
            }
        }
    }

    function saveCoachChartSelection($teamId, $coachIds)
    {
        $existing = $this->db->table('team_player_chart_selection')
            ->where('team_id', $teamId)->get()->getRowArray();

        $data = ['coach_ids' => json_encode($coachIds), 'updated_at' => date('Y-m-d H:i:s')];

        if ($existing) {
            return $this->db->table('team_player_chart_selection')
                ->where('id', $existing['id'])->update($data);
        } else {
            $data['team_id'] = $teamId;
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->table('team_player_chart_selection')->insert($data);
        }
    }

    function getCoachChartSelection($teamId)
    {
        $result = $this->db->table('team_player_chart_selection')
            ->where('team_id', $teamId)->get()->getRowArray();

        if ($result && !empty($result['coach_ids'])) {
            return json_decode($result['coach_ids'], true);
        }
        return [];
    }

    /**
     * รายชื่อ 24 คอลัมน์ชุดทีมใน academy_player_enrollments
     */
    public static function teamKitColumns(): array
    {
        $cols = [];
        foreach (['home', 'away', 'third'] as $k) {
            foreach (['', 'goal_'] as $g) {
                foreach (['shirt', 'trousers', 'socks'] as $p) {
                    $cols[] = "{$k}_{$g}{$p}";
                }
                $cols[] = "{$k}_{$g}set_img";
            }
        }
        return $cols; // 24 คอลัมน์
    }

    /**
     * อ่านชุดทีม (อ่านจากแถวผู้เล่นแถวใดแถวหนึ่งของทีม เพราะค่าเหมือนกันทุกแถว)
     */
    function getTeamKit($academyId, $teamId)
    {
        $row = $this->db->table('academy_player_enrollments')
            ->select(implode(',', self::teamKitColumns()))
            ->where('academy_id', $academyId)
            ->where('team_id', $teamId)
            ->where('is_active', 1)
            ->limit(1)
            ->get()->getRowArray();
        return $row ?: [];
    }

    /**
     * บันทึกชุดทีมลงทุกแถวผู้เล่นของทีม (academy_id + team_id + is_active)
     * @return bool  true=สำเร็จ (รวมกรณีไม่มีแถวให้อัปเดต), false=ผิดพลาด
     */
    function saveTeamKit($academyId, $teamId, array $data)
    {
        // กรองเฉพาะคอลัมน์ที่อนุญาต
        $allowed = array_flip(self::teamKitColumns());
        $clean = array_intersect_key($data, $allowed);
        if (empty($clean)) {
            return true; // ไม่มีอะไรต้องบันทึก
        }

        return $this->db->table('academy_player_enrollments')
            ->where('academy_id', $academyId)
            ->where('team_id', $teamId)
            ->where('is_active', 1)
            ->update($clean);
    }

    /**
     * อ่านชุดทีมระดับทัวร์นาเมนต์ (tournament_team: tour_id + team_id)
     * ใช้ในหน้า player-chart ที่ชุดผูกกับทัวร์นาเมนต์ที่เลือก
     */
    function getTournamentTeamKit($tourId, $teamId)
    {
        $row = $this->db->table('tournament_team')
            ->select(implode(',', self::teamKitColumns()))
            ->where('tour_id', (int)$tourId)
            ->where('team_id', (int)$teamId)
            ->limit(1)
            ->get()->getRowArray();
        return $row ?: [];
    }

    /**
     * บันทึกชุดทีมระดับทัวร์นาเมนต์
     * @return bool true=สำเร็จ (รวมกรณีไม่มีอะไรต้องบันทึก), false=ผิดพลาด
     */
    function saveTournamentTeamKit($tourId, $teamId, array $data)
    {
        $allowed = array_flip(self::teamKitColumns());
        $clean = array_intersect_key($data, $allowed);
        if (empty($clean)) {
            return true;
        }

        return $this->db->table('tournament_team')
            ->where('tour_id', (int)$tourId)
            ->where('team_id', (int)$teamId)
            ->update($clean);
    }

    // (ยกเลิกแล้ว) saveJersey()/removeJersey() เดิมเขียน team_player_chart_selection.jersey_home/away/gk
    // ซึ่งกำลังถูกลบ — ชุดทีมย้ายไปแก้ที่ tournament_team (home_set_img/away_set_img/...) ผ่านหน้า player-chart

    /**
     * รูปชุดของทีม — อ่านจากฟิลด์ใหม่ใน tournament_team
     * (เดิมอ่าน jersey_home/away/gk จาก team_player_chart_selection ซึ่งกำลังถูกยกเลิก)
     * @return array ['home_set_img'=>path, 'away_set_img'=>path, 'home_goal_set_img'=>path]
     */
    function getJerseys($teamId, $tourId = null)
    {
        $b = $this->db->table('tournament_team')
            ->select('home_set_img, away_set_img, home_goal_set_img')
            ->where('team_id', $teamId);
        if ($tourId) {
            $b->where('tour_id', (int)$tourId);
        } else {
            $b->orderBy('tour_team_id', 'DESC'); // ไม่ระบุทัวร์ → เอาล่าสุด
        }
        $result = $b->limit(1)->get()->getRowArray();

        return $result ?: ['home_set_img' => null, 'away_set_img' => null, 'home_goal_set_img' => null];
    }

    // =====================================================
    // Coach Join Requests (team-level)
    // =====================================================

    function getPendingCoachRequests($teamId)
    {
        return $this->db->table('coach_team_requests')
            ->select('coach_team_requests.*,
                      coach.first_name as coach_first_name, coach.last_name as coach_last_name,
                      coach.nick_name as coach_nick_name, coach.image_path as coach_image,
                      coach_users.line_picture as coach_line_picture')
            ->join('coach', 'coach.id = coach_team_requests.coach_id', 'left')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->where('coach_team_requests.team_id', $teamId)
            ->where('coach_team_requests.status', 'pending')
            ->where('coach_team_requests.is_active', 1)
            ->orderBy('coach_team_requests.requested_at', 'DESC')
            ->get()->getResultArray();
    }

    function respondToCoachJoinRequest($requestId, $teamId, $userId, $response, $message = null)
    {
        $request = $this->db->table('coach_team_requests')
            ->where('id', $requestId)
            ->where('team_id', $teamId)
            ->where('status', 'pending')
            ->get()->getRowArray();

        if (!$request) return false;

        try {
            $this->db->transBegin();

            $this->db->table('coach_team_requests')
                ->where('id', $requestId)
                ->update([
                    'status' => $response,
                    'response_message' => $message,
                    'responded_at' => date('Y-m-d H:i:s'),
                    'responded_by' => $userId
                ]);

            if ($response === 'approved') {
                if ($this->db->tableExists('coach_invitations')) {
                    $existingInvitation = $this->db->table('coach_invitations')
                        ->where('coach_id', $request['coach_id'])
                        ->where('team_id', $teamId)
                        ->get()->getRowArray();

                    if ($existingInvitation) {
                        $this->db->table('coach_invitations')
                            ->where('id', $existingInvitation['id'])
                            ->update(['status' => 'accepted', 'responded_at' => date('Y-m-d H:i:s')]);
                    } else {
                        $this->db->table('coach_invitations')->insert([
                            'coach_id' => $request['coach_id'],
                            'team_id' => $teamId,
                            'invited_by' => $userId,
                            'status' => 'accepted',
                            'responded_at' => date('Y-m-d H:i:s'),
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }

                $this->db->table('coach')
                    ->where('id', $request['coach_id'])
                    ->update(['team_id' => $teamId]);

                // สร้าง academy_members เพื่อให้โค้ชมีสิทธิ์เข้าถึง Academy
                $coachData = $this->db->table('coach')
                    ->select('coach_user_id')
                    ->where('id', $request['coach_id'])
                    ->get()->getRowArray();

                $academyTeam = $this->db->table('academy_teams')
                    ->select('academy_id')
                    ->where('team_id', $teamId)
                    ->get()->getRowArray();

                if ($coachData && $coachData['coach_user_id'] && $academyTeam) {
                    $existing = $this->db->table('academy_members')
                        ->where('academy_id', $academyTeam['academy_id'])
                        ->where('coach_user_id', $coachData['coach_user_id'])
                        ->get()->getRowArray();

                    if ($existing) {
                        $this->db->table('academy_members')
                            ->where('id', $existing['id'])
                            ->update(['status' => 'approved', 'role' => 'coach']);
                    } else {
                        $this->db->table('academy_members')->insert([
                            'academy_id' => $academyTeam['academy_id'],
                            'coach_user_id' => $coachData['coach_user_id'],
                            'role' => 'coach',
                            'status' => 'approved',
                            'created_at' => date('Y-m-d H:i:s')
                        ]);
                    }
                }
            }

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return false;
            }

            $this->db->transCommit();
            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'respondToCoachJoinRequest: ' . $e->getMessage());
            return false;
        }
    }

    function getCoachJoinRequest($requestId)
    {
        return $this->db->table('coach_team_requests')
            ->select('coach_team_requests.*,
                      coach.first_name as coach_first_name, coach.last_name as coach_last_name,
                      coach.nick_name as coach_nick_name, coach.image_path as coach_image,
                      coach_users.line_picture as coach_line_picture, team.name as team_name')
            ->join('coach', 'coach.id = coach_team_requests.coach_id', 'left')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->join('team', 'team.id = coach_team_requests.team_id', 'left')
            ->where('coach_team_requests.id', $requestId)
            ->get()->getRowArray();
    }

    // =====================================================
    // Geographic Lookups (reuse)
    // =====================================================

    function get_zone()
    {
        return $this->db->table('tbl_zone')->select('*')->get()->getResultArray();
    }

    function getCountries()
    {
        return $this->db->table('tbl_country')->select('*')
            ->orderBy('country_id', 'ASC')->get()->getResultArray();
    }

    function getProvinces()
    {
        return $this->db->table('tbl_provinces')->select('*')
            ->orderBy('province_name', 'ASC')->get()->getResultArray();
    }

    // =====================================================
    // Search
    // =====================================================

    function searchAcademies($keyword)
    {
        return $this->db->table('academy')
            ->select('id, name, logo')
            ->like('name', $keyword)
            ->where('is_active', 1)
            ->limit(10)
            ->get()->getResultArray();
    }



}
