<?php

namespace Modules\Team\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class TeamModel extends Model
{
    protected $table      = "team";
    protected $primaryKey = "id";
    protected $allowedFields = [];

    function get_Teams($id = '')
    {
        if ($id) {
            $builder = $this->db->table($this->table);
            $builder->select("team.*, CASE WHEN mapping_glo_team.generation = 1 THEN 'U13' ELSE 'U15' END as generation_name, tbl_zone.description as zone_name, tbl_districts.district_name as amphure_name, tbl_subdistricts.subdistrict_name as tambon_name, tbl_country.country_name_th as country_name")
                ->where("team.id", $id);
            $builder->join('mapping_glo_team', 'mapping_glo_team.jootball_team_id = team.id', 'left');
            $builder->join('tbl_zone', 'tbl_zone.id = team.zone_id', 'left');
            $builder->join('tbl_districts', 'tbl_districts.district_id = team.amphure_id', 'left');
            $builder->join('tbl_subdistricts', 'tbl_subdistricts.subdistrict_id = team.tambon_id', 'left');
            $builder->join('tbl_country', 'tbl_country.country_id = team.country_id', 'left');
            $result = $builder->get()->getRowArray();
        }
        return $result;
    }

    function getUserteam($user_id)
    {
        // Check for team created by user
        $builder = $this->db->table($this->table);
        $builder->select('team.*, tbl_zone.name as zone_name, tbl_zone.description as zone_description, "owner" as role');
        $builder->where('create_by', $user_id);
        $builder->join('tbl_zone', 'team.zone_id = tbl_zone.id', 'left');
        $result = $builder->get()->getRowArray();

        if ($result) {
            return $result;
        }

        // Check for team user is manager of
        $builder = $this->db->table('team_managers');
        $builder->select('team.*, tbl_zone.name as zone_name, tbl_zone.description as zone_description, "manager" as role');
        $builder->join('team', 'team.id = team_managers.team_id');
        $builder->join('tbl_zone', 'team.zone_id = tbl_zone.id', 'left');
        $builder->where('team_managers.user_id', $user_id);
        $builder->where('team_managers.status', 'approved');

        return $builder->get()->getRowArray(); // Return first found
    }

    function getTeamManagers($team_id)
    {
        return $this->db->table('team_managers')
            ->select('team_managers.*, user.name, user.username, user.profile_image')
            ->join('user', 'user.id = team_managers.user_id')
            ->where('team_id', $team_id)
            ->where('status', 'approved')
            ->get()->getResultArray();
    }

    function getPendingRequests($team_id)
    {
        return $this->db->table('team_managers')
            ->select('team_managers.*, user.name, user.username, user.profile_image')
            ->join('user', 'user.id = team_managers.user_id')
            ->where('team_id', $team_id)
            ->where('status', 'pending')
            ->get()->getResultArray();
    }

    function requestJoinTeam($team_id, $user_id)
    {
        $exists = $this->db->table('team_managers')
            ->where('team_id', $team_id)
            ->where('user_id', $user_id)
            ->countAllResults();

        if ($exists > 0) {
            return ['status' => false, 'message' => 'คุณได้ส่งคำขอหรืออยู่ในทีมนี้แล้ว'];
        }

        // Check if user is owner
        $isOwner = $this->db->table($this->table)
            ->where('id', $team_id)
            ->where('create_by', $user_id)
            ->countAllResults();

        if ($isOwner > 0) {
            return ['status' => false, 'message' => 'คุณเป็นเจ้าของทีมนี้อยู่แล้ว'];
        }

        $this->db->table('team_managers')->insert([
            'team_id' => $team_id,
            'user_id' => $user_id,
            'status'  => 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        return ['status' => true, 'message' => 'ส่งคำขอเรียบร้อยแล้ว รอการอนุมัติจากทีม'];
    }

    function updateRequestStatus($id, $status)
    {
        return $this->db->table('team_managers')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    function removeManager($manager_id)
    {
        return $this->db->table('team_managers')
            ->where('id', $manager_id)
            ->delete();
    }

    function searchTeams($keyword)
    {
        return $this->db->table($this->table)
            ->select('id, name, logo')
            ->like('name', $keyword)
            ->limit(10)
            ->get()->getResultArray();
    }

    function saveTeam($input)
    {
        $builder = $this->db->table($this->table);

        if (!empty($input['name'])) {
            $builder->set('name', $input['name']);
        }
        if (!empty($input['name_en'])) {
            $builder->set('name_en', $input['name_en']);
        }
        if (!empty($input['organize_name'])) {
            $builder->set('organize_name', $input['organize_name']);
        }
        if (!empty($input['organize_type'])) {
            $builder->set('organize_type', $input['organize_type']);
        } else {
            $builder->set('organize_type', 0);
        }
        if (!empty($input['coach_name'])) {
            $builder->set('coach_name', $input['coach_name']);
        }
        if (!empty($input['coach_phone'])) {
            $builder->set('coach_phone', $input['coach_phone']);
        }
        if (!empty($input['contact_name'])) {
            $builder->set('contact_name', $input['contact_name']);
        }
        if (!empty($input['contact_phone'])) {
            $builder->set('contact_phone', $input['contact_phone']);
        }
        if (!empty($input['contact_email'])) {
            $builder->set('contact_email', $input['contact_email']);
        }
        if (!empty($input['contact_line'])) {
            $builder->set('contact_line', $input['contact_line']);
        }
        if (!empty($input['latitude'])) {
            $builder->set('latitude', $input['latitude']);
        }
        if (!empty($input['longitude'])) {
            $builder->set('longitude', $input['longitude']);
        }
        if (!empty($input['zone_id'])) {
            $builder->set('zone_id', $input['zone_id']);
        } else {
            $builder->set('zone_id', 0);
        }
        if (!empty($input['logo'])) {
            $builder->set('logo', $input['logo']);
        }
        if (!empty($input['parent_team_id'])) {
            $builder->set('parent_team_id', $input['parent_team_id']);
        }
        if (isset($input['country_id'])) {
            $builder->set('country_id', $input['country_id'] ?: null);
        }
        if (isset($input['province_id'])) {
            $builder->set('province_id', $input['province_id'] ?: null);
        }
        if (isset($input['amphure_id'])) {
            $builder->set('amphure_id', $input['amphure_id'] ?: null);
        }
        if (isset($input['tambon_id'])) {
            $builder->set('tambon_id', $input['tambon_id'] ?: null);
        }

        if (!empty($input['id'])) {
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->where('id', $input['id']);
            $builder->update();
        } else {
            $builder->set('create_by', session()->get('user_id'));
            $builder->set('coach_id', 0); // Set default value for coach_id
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->insert();
        }
    }

    function get_zone()
    {
        $builder = $this->db->table('tbl_zone');
        $result = $builder->select('*')->get()->getResultArray();
        return $result;
    }

    function getCountries()
    {
        $builder = $this->db->table('tbl_country');
        $result = $builder->select('*')->orderBy('country_id', 'ASC')->get()->getResultArray();
        return $result;
    }

    function getProvinces()
    {
        $builder = $this->db->table('tbl_provinces');
        $result = $builder->select('*')->orderBy('province_name', 'ASC')->get()->getResultArray();
        return $result;
    }

    function checkDuplicateName($name, $id = null)
    {
        $builder = $this->db->table($this->table);
        $builder->where('name', $name);
        $builder->where('is_active', 1); // เช็คเฉพาะทีมที่ active อยู่
        if ($id) {
            $builder->where('id !=', $id);
        }
        return $builder->countAllResults() > 0;
    }
    function getUserRequests($user_id)
    {
        return $this->db->table('team_managers')
            ->select('team_managers.*, team.name as team_name, team.logo as team_logo')
            ->join('team', 'team.id = team_managers.team_id', 'left')
            ->where('team_managers.user_id', $user_id)
            ->orderBy('team_managers.created_at', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Get teams where user is approved as manager
     */
    function getApprovedManagedTeams($user_id)
    {
        return $this->db->table('team_managers')
            ->select('team_managers.*, team.*, team.name as team_name, team.logo as team_logo')
            ->join('team', 'team.id = team_managers.team_id', 'left')
            ->where('team_managers.user_id', $user_id)
            ->where('team_managers.status', 'approved')
            ->where('team.is_active', 1)
            ->orderBy('team_managers.created_at', 'DESC')
            ->get()->getResultArray();
    }

    /**
     * Get all sub-teams of a parent team (club)
     */
    function getSubTeams($parent_team_id)
    {
        $builder = $this->db->table($this->table);
        $builder->select('team.*, tbl_zone.name as zone_name')
            ->join('tbl_zone', 'tbl_zone.id = team.zone_id', 'left')
            ->where('team.parent_team_id', $parent_team_id)
            ->where('team.is_active', 1);
        return $builder->get()->getResultArray();
    }

    /**
     * Get parent team (club) of a sub-team
     */
    function getParentTeam($team_id)
    {
        $team = $this->get_Teams($team_id);
        if (!empty($team['parent_team_id'])) {
            return $this->get_Teams($team['parent_team_id']);
        }
        return null;
    }

    /**
     * Check if this team is a parent team (has sub-teams)
     */
    function hasSubTeams($team_id)
    {
        $count = $this->db->table($this->table)
            ->where('parent_team_id', $team_id)
            ->where('is_active', 1)
            ->countAllResults();
        return $count > 0;
    }

    /**
     * Get saved player chart selection for a team
     */
    function getPlayerChartSelection($team_id)
    {
        $result = $this->db->table('team_player_chart_selection')
            ->where('team_id', $team_id)
            ->get()
            ->getRowArray();

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


    /**
     * Save player chart selection for a team
     */
    function savePlayerChartSelection($team_id, $player_ids)
    {
        $existing = $this->db->table('team_player_chart_selection')
            ->where('team_id', $team_id)
            ->get()
            ->getRowArray();

        $data = [
            'team_id'    => $team_id,
            'player_ids' => json_encode($player_ids),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            $result = $this->db->table('team_player_chart_selection')
                ->where('id', $existing['id'])
                ->update($data);
        } else {
            $data['created_at'] = date('Y-m-d H:i:s');
            $result = $this->db->table('team_player_chart_selection')
                ->insert($data);
        }

        // Sync tournament_submitted_players: ลบนักเตะที่ถูกเอาออกจาก chart
        // เพื่อป้องกัน stale data ที่ทำให้ทีมอื่นส่งนักเตะซ้ำไม่ได้
        //$this->syncSubmittedPlayersWithSelection($team_id, $player_ids);

        return $result;
    }

    /**
     * Sync tournament_submitted_players กับ chart selection ปัจจุบัน
     * ลบนักเตะที่ไม่อยู่ใน selection ออกจาก tournament_submitted_players
     */
    function syncSubmittedPlayersWithSelection($team_id, $selectedPlayerIds)
    {
        // หา tour_team_id ทั้งหมดที่ทีมนี้เคยส่งรายชื่อแล้ว
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

    /**
     * Get coaches associated with a team
     * Gets coaches from: 1) coach_invitations (accepted), 2) coach_teams, 3) coach.team_id
     *
     * @param int $teamId
     * @return array Array of coaches with their info
     */
    function getTeamCoaches($teamId, $includePending = false)
    {
        $coaches = [];
        $addedCoachIds = [];

        // 1. Get coaches from coach_invitations (accepted)
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

        // 2. Get coaches from coach_teams
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

        // 3. Get coaches where coach.team_id matches
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

        // 4. Get pending coaches from coach_team_requests (only if includePending is true)
        if ($includePending && $this->db->tableExists('coach_team_requests')) {
            $pendingCoaches = $this->db->table('coach_team_requests')
                ->select('coach.*, coach_users.line_picture, coach_team_requests.role_requested as role, coach_team_requests.status as request_status')
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

        // Add role display names
        $positionMapping = [
            'team_manager' => 'ผู้ฝึกสอน',
            'head_coach' => 'หัวหน้าผู้ฝึกสอน',
            'coach' => 'ผู้ฝึกสอน',
            'assistant_coach' => 'ผู้ช่วยผู้ฝึกสอน',
            'team_staff' => 'เจ้าหน้าที่ทีม',
            'trainer' => 'เทรนเนอร์',
            'scout' => 'นักสำรวจ'
        ];

        foreach ($coaches as &$coach) {
            if (!empty($coach['position']) && isset($positionMapping[$coach['position']])) {
                $coach['role_name'] = $positionMapping[$coach['position']];
            } else {
                $role = $coach['role'] ?? 'head_coach';
                $coach['role_name'] = $positionMapping[$role] ?? 'ผู้ฝึกสอน';
            }
        }

        return $this->hydrateCoachMediaFallback($coaches);
    }

    /**
     * เติม media ของ coach จาก assignment อื่นที่ใช้ coach_user_id เดียวกัน
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
     * Save coach chart selection for a team
     *
     * @param int $teamId
     * @param array $coachIds Array of coach IDs
     * @return bool
     */
    function saveCoachChartSelection($teamId, $coachIds)
    {
        $existing = $this->db->table('team_player_chart_selection')
            ->where('team_id', $teamId)
            ->get()
            ->getRowArray();

        $data = [
            'coach_ids' => json_encode($coachIds),
            'updated_at' => date('Y-m-d H:i:s')
        ];

        if ($existing) {
            return $this->db->table('team_player_chart_selection')
                ->where('id', $existing['id'])
                ->update($data);
        } else {
            $data['team_id'] = $teamId;
            $data['created_at'] = date('Y-m-d H:i:s');
            return $this->db->table('team_player_chart_selection')
                ->insert($data);
        }
    }

    /**
     * Get saved coach chart selection for a team
     *
     * @param int $teamId
     * @return array Array of coach IDs
     */
    function getCoachChartSelection($teamId)
    {
        $result = $this->db->table('team_player_chart_selection')
            ->where('team_id', $teamId)
            ->get()
            ->getRowArray();

        if ($result && !empty($result['coach_ids'])) {
            return json_decode($result['coach_ids'], true);
        }
        return [];
    }

    /**
     * Get coaches by their IDs with full details
     *
     * @param array $coachIds Array of coach IDs
     * @return array Array of coach data
     */
    function getCoachesByIds($coachIds, $teamId = null)
    {
        if (empty($coachIds)) {
            return [];
        }

        $query = $this->db->table('coach')
            ->select('coach.*, coach_users.line_picture')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->whereIn('coach.id', $coachIds)
            ->where('coach.is_active', 1);

        // ORDER BY FIELD เพื่อรักษาลำดับตาม $coachIds ที่ส่งมา
        if (count($coachIds) > 1) {
            $query->orderBy("FIELD(coach.id, " . implode(',', array_map('intval', $coachIds)) . ")", '', false);
        }

        $coaches = $query->get()->getResultArray();

        // Add role display names
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
            // ใช้ position จาก profile เป็นหลัก, fallback เป็น role
            if (!empty($coach['position']) && isset($positionMapping[$coach['position']])) {
                $coach['role_name'] = $positionMapping[$coach['position']];
            } else {
                $role = $coach['role'] ?? 'head_coach';
                $coach['role_name'] = $positionMapping[$role] ?? 'ผู้ฝึกสอน';
            }

            // Determine approval status if team_id is provided
            $coach['approval_status'] = 'approved'; // Default to approved

            if ($teamId) {
                // Check if this is a pending request
                $isPending = false;

                if ($this->db->tableExists('coach_team_requests')) {
                    $pendingRequest = $this->db->table('coach_team_requests')
                        ->where('coach_id', $coach['id'])
                        ->where('team_id', $teamId)
                        ->where('status', 'pending')
                        ->get()
                        ->getRowArray();

                    if ($pendingRequest) {
                        $isPending = true;
                        $coach['role'] = $pendingRequest['role_requested'] ?? $coach['role'];
                        $coach['role_name'] = $positionMapping[$coach['role']] ?? 'ผู้ฝึกสอน';
                    }
                }

                $coach['approval_status'] = $isPending ? 'pending' : 'approved';
            }
        }

        return $this->hydrateCoachMediaFallback($coaches);
    }

    // =====================================================
    // Coach Join Request Methods (Team Side)
    // =====================================================

    /**
     * ดึงคำขอเข้าร่วมจาก coach ที่รอดำเนินการ
     * @param int $teamId
     * @return array
     */
    function getPendingCoachRequests($teamId)
    {
        return $this->db->table('coach_team_requests')
            ->select('coach_team_requests.*,
                      coach.first_name as coach_first_name,
                      coach.last_name as coach_last_name,
                      coach.nick_name as coach_nick_name,
                      coach.image_path as coach_image,
                      coach_users.line_picture as coach_line_picture')
            ->join('coach', 'coach.id = coach_team_requests.coach_id', 'left')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->where('coach_team_requests.team_id', $teamId)
            ->where('coach_team_requests.status', 'pending')
            ->where('coach_team_requests.is_active', 1)
            ->orderBy('coach_team_requests.requested_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * ดึงจำนวนคำขอ pending สำหรับทีม
     * @param int $teamId
     * @return int
     */
    function getTeamCoachRequestsCount($teamId)
    {
        return $this->db->table('coach_team_requests')
            ->where('team_id', $teamId)
            ->where('status', 'pending')
            ->where('is_active', 1)
            ->countAllResults();
    }

    /**
     * ดึงประวัติคำขอทั้งหมดของทีม
     * @param int $teamId
     * @param string|null $status Filter by status
     * @return array
     */
    function getAllCoachRequests($teamId, $status = null)
    {
        $builder = $this->db->table('coach_team_requests')
            ->select('coach_team_requests.*,
                      coach.first_name as coach_first_name,
                      coach.last_name as coach_last_name,
                      coach.nick_name as coach_nick_name,
                      coach.image_path as coach_image,
                      coach_users.line_picture as coach_line_picture,
                      responder.name as responder_name')
            ->join('coach', 'coach.id = coach_team_requests.coach_id', 'left')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->join('user as responder', 'responder.id = coach_team_requests.responded_by', 'left')
            ->where('coach_team_requests.team_id', $teamId)
            ->where('coach_team_requests.is_active', 1);

        if ($status !== null) {
            $builder->where('coach_team_requests.status', $status);
        }

        return $builder->orderBy('coach_team_requests.requested_at', 'DESC')
            ->get()
            ->getResultArray();
    }

    /**
     * ตอบกลับคำขอเข้าร่วมจาก coach
     * @param int $requestId
     * @param int $teamId
     * @param int $userId User ID who is responding
     * @param string $response 'approved' or 'rejected'
     * @param string|null $message Response message
     * @return bool
     */
    function respondToCoachJoinRequest($requestId, $teamId, $userId, $response, $message = null)
    {
        // Log the incoming parameters for debugging
        log_message('debug', "respondToCoachJoinRequest - requestId: {$requestId}, teamId: {$teamId}, response: {$response}");

        // Verify request belongs to this team (removed is_active check for compatibility)
        $request = $this->db->table('coach_team_requests')
            ->where('id', $requestId)
            ->where('team_id', $teamId)
            ->where('status', 'pending')
            ->get()
            ->getRowArray();

        // Log query result
        log_message('debug', "respondToCoachJoinRequest - request found: " . ($request ? 'yes' : 'no'));
        log_message('debug', "respondToCoachJoinRequest - request data: " . json_encode($request));

        if (!$request) {
            // Try to get request without filters to see what's wrong
            $requestCheck = $this->db->table('coach_team_requests')
                ->where('id', $requestId)
                ->get()
                ->getRowArray();
            log_message('debug', "Request without filters: " . json_encode($requestCheck));
            return ['error' => 'request_not_found_in_model', 'check' => $requestCheck];
        }

        log_message('debug', "Starting transaction for request ID: " . $requestId);

        try {
            $this->db->transBegin();

            // Update request status
            $this->db->table('coach_team_requests')
                ->where('id', $requestId)
                ->update([
                    'status' => $response,
                    'response_message' => $message,
                    'responded_at' => date('Y-m-d H:i:s'),
                    'responded_by' => $userId
                ]);

            // If approved, add coach to team
            if ($response === 'approved') {
                // Check if coach_invitations table exists (for compatibility)
                if ($this->db->tableExists('coach_invitations')) {
                    // Check if invitation already exists
                    $existingInvitation = $this->db->table('coach_invitations')
                        ->where('coach_id', $request['coach_id'])
                        ->where('team_id', $teamId)
                        ->get()
                        ->getRowArray();

                    if ($existingInvitation) {
                        // Update existing invitation
                        $this->db->table('coach_invitations')
                            ->where('id', $existingInvitation['id'])
                            ->update([
                                'status' => 'accepted',
                                'responded_at' => date('Y-m-d H:i:s')
                            ]);
                    } else {
                        // Create new invitation record
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

                // Also update coach's team_id for backwards compatibility
                $this->db->table('coach')
                    ->where('id', $request['coach_id'])
                    ->update(['team_id' => $teamId]);

                // ถ้าทีมอยู่ใน Academy → เพิ่มผู้ฝึกสอนเข้า academy_members ด้วย
                if ($this->db->tableExists('academy_teams') && $this->db->tableExists('academy_members')) {
                    $academyTeam = $this->db->table('academy_teams')
                        ->where('team_id', $teamId)
                        ->get()
                        ->getRowArray();

                    if ($academyTeam) {
                        // ดึง coach_user_id จากตาราง coach
                        $coachData = $this->db->table('coach')
                            ->select('coach_user_id')
                            ->where('id', $request['coach_id'])
                            ->get()
                            ->getRowArray();

                        if ($coachData && !empty($coachData['coach_user_id'])) {
                            // ตรวจว่ายังไม่มี record ใน academy_members
                            $existingMember = $this->db->table('academy_members')
                                ->where('academy_id', $academyTeam['academy_id'])
                                ->where('coach_user_id', $coachData['coach_user_id'])
                                ->get()
                                ->getRowArray();

                            if ($existingMember) {
                                // มีอยู่แล้ว → อัพเดท status เป็น approved
                                $this->db->table('academy_members')
                                    ->where('id', $existingMember['id'])
                                    ->update([
                                        'status' => 'approved',
                                        'role' => 'coach',
                                        'updated_at' => date('Y-m-d H:i:s')
                                    ]);
                            } else {
                                // ยังไม่มี → สร้างใหม่
                                $this->db->table('academy_members')->insert([
                                    'academy_id' => $academyTeam['academy_id'],
                                    'coach_user_id' => $coachData['coach_user_id'],
                                    'role' => 'coach',
                                    'status' => 'approved',
                                    'created_at' => date('Y-m-d H:i:s'),
                                    'updated_at' => date('Y-m-d H:i:s')
                                ]);
                            }

                            log_message('debug', "Coach (coach_user_id: {$coachData['coach_user_id']}) added to academy_members for academy_id: {$academyTeam['academy_id']}");
                        }
                    }
                }

                if (class_exists('\Modules\Coach\Models\NotificationModel')) {
                    try {
                        $notificationModel = new \Modules\Coach\Models\NotificationModel();
                        $team = $this->get_Teams($teamId);
                        $notificationModel->createNotification(
                            $request['coach_id'],
                            'join_request_approved',
                            'คำขอเข้าร่วมทีมได้รับการอนุมัติ',
                            'คำขอเข้าร่วมทีม ' . ($team['name'] ?? '') . ' ได้รับการอนุมัติแล้ว',
                            ['team_id' => $teamId, 'request_id' => $requestId]
                        );
                    } catch (\Throwable $e) {
                        log_message('error', 'createNotification (approved) failed: ' . $e->getMessage());
                    }
                }
            } else {
                if (class_exists('\Modules\Coach\Models\NotificationModel')) {
                    try {
                        $notificationModel = new \Modules\Coach\Models\NotificationModel();
                        $team = $this->get_Teams($teamId);
                        $notificationModel->createNotification(
                            $request['coach_id'],
                            'join_request_rejected',
                            'คำขอเข้าร่วมทีมถูกปฏิเสธ',
                            'คำขอเข้าร่วมทีม ' . ($team['name'] ?? '') . ' ถูกปฏิเสธ' . ($message ? ': ' . $message : ''),
                            ['team_id' => $teamId, 'request_id' => $requestId]
                        );
                    } catch (\Throwable $e) {
                        log_message('error', 'createNotification (rejected) failed: ' . $e->getMessage());
                    }
                }
            }

            if ($this->db->transStatus() === false) {
                $dbError = $this->db->error();
                log_message('error', 'Transaction failed - transStatus is false');
                log_message('error', 'Last DB error: ' . json_encode($dbError));
                $this->db->transRollback();
                return ['error' => 'transaction_failed', 'db_error' => $dbError];
            }

            $this->db->transCommit();
            log_message('debug', 'Coach join request approved successfully');
            return true;
        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Exception in respondToCoachJoinRequest: ' . $e->getMessage());
            log_message('error', 'Stack trace: ' . $e->getTraceAsString());
            return ['error' => 'exception', 'message' => $e->getMessage()];
        }
    }

    /**
     * ดึงข้อมูลคำขอเข้าร่วมทีม
     * @param int $requestId
     * @return array|null
     */
    function getCoachJoinRequest($requestId)
    {
        return $this->db->table('coach_team_requests')
            ->select('coach_team_requests.*,
                      coach.first_name as coach_first_name,
                      coach.last_name as coach_last_name,
                      coach.nick_name as coach_nick_name,
                      coach.image_path as coach_image,
                      coach_users.line_picture as coach_line_picture,
                      team.name as team_name')
            ->join('coach', 'coach.id = coach_team_requests.coach_id', 'left')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->join('team', 'team.id = coach_team_requests.team_id', 'left')
            ->where('coach_team_requests.id', $requestId)
            ->get()
            ->getRowArray();
    }

    // =====================================================
    // Jersey Management
    // =====================================================


    /**
     * รูปชุดของทีม — อ่านจากฟิลด์ใหม่ใน tournament_team
     * (เดิมอ่าน jersey_home/away/gk จาก team_player_chart_selection ซึ่งกำลังถูกยกเลิก)
     *
     * @param int      $teamId
     * @param int|null $tourId  ระบุทัวร์นาเมนต์; ไม่ระบุ = เอาแถวล่าสุด
     * @return array ['home_set_img' => path, 'away_set_img' => path, 'home_goal_set_img' => path]
     */
    function getJerseys($teamId, $tourId = null)
    {
        $b = $this->db->table('tournament_team')
            ->select('home_set_img, away_set_img, home_goal_set_img')
            ->where('team_id', $teamId);
        if ($tourId) {
            $b->where('tour_id', (int)$tourId);
        } else {
            $b->orderBy('tour_team_id', 'DESC');
        }
        $result = $b->limit(1)->get()->getRowArray();

        return $result ?: ['home_set_img' => null, 'away_set_img' => null, 'home_goal_set_img' => null];
    }
}
