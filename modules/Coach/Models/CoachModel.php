<?php

namespace Modules\Coach\Models;

use CodeIgniter\Model;

class CoachModel extends Model
{
    protected $table = "coach";
    protected $primaryKey = "id";
    protected $allowedFields = [
        'first_name', 'last_name', 'nick_name', 'position', 'nation', 'id_card', 'id_card_image', 'birth_date',
        'team_id', 'coach_user_id', 'dominant_foot', 'height', 'weight', 'bmi',
        'province_id', 'amphure_id', 'tambon_id', 'create_date', 'update_date',
        'image_path', 'is_active'
    ];
    protected $useTimestamps = false;

    public function createCoach($data)
    {
        try {
            $builder = $this->db->table($this->table);
            $result = $builder->insert($data);

            if (!$result) {
                $error = $this->db->error();
                log_message('error', 'Failed to create coach profile: ' . json_encode($error));
                log_message('error', 'Coach data: ' . json_encode($data));
                return false;
            }

            return $this->db->insertID();

        } catch (\Exception $e) {
            log_message('error', 'Exception creating coach profile: ' . $e->getMessage());
            log_message('error', 'Coach data: ' . json_encode($data));
            return false;
        }
    }

    public function getCoachByCoachUserId($coachUserId)
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*,
                            tbl_provinces.province_name,
                            tbl_districts.district_name as amphure_name,
                            tbl_subdistricts.subdistrict_name as tambon_name,
                            team.name as team_name,
                            team.logo as team_logo")
                ->join("tbl_provinces", "coach.province_id = tbl_provinces.province_id", "LEFT")
                ->join("tbl_districts", "coach.amphure_id = tbl_districts.district_id", "LEFT")
                ->join("tbl_subdistricts", "coach.tambon_id = tbl_subdistricts.subdistrict_id", "LEFT")
                ->join("team", "coach.team_id = team.id", "LEFT")
                ->where("coach.coach_user_id", $coachUserId)
                ->where("coach.is_active", 1);

        return $builder->get()->getRowArray();
    }

    public function getCoachById($id)
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*,
                            coach_users.line_picture,
                            coach_users.line_name,
                            coach_users.username,
                            coach_users.email,
                            tbl_provinces.province_name,
                            tbl_districts.district_name as amphure_name,
                            tbl_subdistricts.subdistrict_name as tambon_name")
                ->join("coach_users", "coach.coach_user_id = coach_users.id", "LEFT")
                ->join("tbl_provinces", "coach.province_id = tbl_provinces.province_id", "LEFT")
                ->join("tbl_districts", "coach.amphure_id = tbl_districts.district_id", "LEFT")
                ->join("tbl_subdistricts", "coach.tambon_id = tbl_subdistricts.subdistrict_id", "LEFT")
                ->where("coach.id", $id)
                ->where("coach.is_active", 1);

        return $builder->get()->getRowArray();
    }

    public function getCoachTeams($coachId)
    {
        $teams = [];

        if ($this->db->tableExists('coach_invitations')) {
            $teams = array_merge($teams, $this->db->table('coach_invitations')
                ->select('team.*')
                ->join('team', 'team.id = coach_invitations.team_id', 'inner')
                ->where('coach_invitations.coach_id', $coachId)
                ->where('coach_invitations.status', 'accepted')
                ->where('team.is_active', 1)
                ->get()
                ->getResultArray());
        }

        if ($this->db->tableExists('coach_teams')) {
            $teams = array_merge($teams, $this->db->table('coach_teams')
                ->select('team.*')
                ->join('team', 'team.id = coach_teams.team_id', 'inner')
                ->where('coach_teams.coach_id', $coachId)
                ->where('coach_teams.is_active', 1)
                ->where('team.is_active', 1)
                ->get()
                ->getResultArray());
        }

        $coach = $this->getCoachById($coachId);
        if ($coach && !empty($coach['team_id'])) {
            $teamIds = array_column($teams, 'id');
            if (!in_array($coach['team_id'], $teamIds)) {
                $additionalTeam = $this->db->table('team')
                    ->where('id', $coach['team_id'])
                    ->where('is_active', 1)
                    ->get()
                    ->getRowArray();
                if ($additionalTeam) {
                    $teams[] = $additionalTeam;
                }
            }
        }

        $uniqueTeams = [];
        foreach ($teams as $t) {
            $uniqueTeams[$t['id']] = $t;
        }

        return array_values($uniqueTeams);
    }

    public function updateCoach($id, $data)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id', $id);
        $builder->set('update_date', date('Y-m-d H:i:s'));

        // Fields that can be set to empty string (e.g. deleting an image)
        $nullableFields = ['id_card_image'];

        foreach ($data as $key => $value) {
            if (!empty($value)) {
                $builder->set($key, $value);
            } elseif (in_array($key, $nullableFields) && isset($data[$key])) {
                $builder->set($key, $value);
            }
        }

        return $builder->update();
    }

    public function getCoachInvitations($coachId)
    {
        // ตรวจสอบว่าตาราง coach_invitations มีอยู่หรือไม่
        if (!$this->db->tableExists('coach_invitations')) {
            return [];
        }

        $builder = $this->db->table('coach_invitations');
        $builder->select('coach_invitations.*, team.name as team_name, team.logo')
                ->join('team', 'coach_invitations.team_id = team.id', 'LEFT')
                ->where('coach_invitations.coach_id', $coachId)
                ->where('coach_invitations.status', 'pending')
                ->where('coach_invitations.is_active', 1)
                ->orderBy('coach_invitations.created_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * Create an invitation and notification
     */
    public function createInvitation($coachId, $teamId, $invitedBy, $message = null)
    {
        // ตรวจสอบว่าตาราง coach_invitations มีอยู่หรือไม่
        if (!$this->db->tableExists('coach_invitations')) {
            return false;
        }

        try {
            $this->db->transBegin();

            // Create invitation
            $invitationData = [
                'coach_id' => $coachId,
                'team_id' => $teamId,
                'invited_by' => $invitedBy,
                'message' => $message,
                'status' => 'pending'
            ];

            $this->db->table('coach_invitations')->insert($invitationData);
            $invitationId = $this->db->insertID();

            // Get team name for notification
            $team = $this->db->table('team')
                            ->select('name')
                            ->where('id', $teamId)
                            ->get()->getRowArray();

            if ($team && $invitationId) {
                // Create notification
                $notificationModel = new \Modules\Coach\Models\NotificationModel();
                $notificationModel->createInvitationNotification(
                    $coachId,
                    $team['name'],
                    $invitationId
                );
            }

            $this->db->transCommit();
            return $invitationId;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error creating invitation: ' . $e->getMessage());
            return false;
        }
    }

    public function respondToInvitation($invitationId, $response, $coachId)
    {
        // ตรวจสอบว่าตาราง coach_invitations มีอยู่หรือไม่
        if (!$this->db->tableExists('coach_invitations')) {
            return false;
        }

        try {
            $this->db->transBegin();

            // Get invitation details before updating
            $invitation = $this->db->table('coach_invitations')
                                  ->select('coach_invitations.*, team.name as team_name')
                                  ->join('team', 'coach_invitations.team_id = team.id', 'LEFT')
                                  ->where('coach_invitations.id', $invitationId)
                                  ->where('coach_invitations.coach_id', $coachId)
                                  ->get()->getRowArray();

            if (!$invitation) {
                $this->db->transRollback();
                return false;
            }

            // อัปเดตสถานะการเชิญ
            $builder = $this->db->table('coach_invitations');
            $builder->where('id', $invitationId)
                    ->where('coach_id', $coachId);
            $builder->set('status', $response);
            $builder->set('responded_at', date('Y-m-d H:i:s'));
            $builder->update();

            // ถ้าตอบรับ ให้อัปเดต team_id + เพิ่มเข้า academy_members
            if ($response === 'accepted') {
                $this->db->table($this->table)
                         ->where('id', $coachId)
                         ->set('team_id', $invitation['team_id'])
                         ->update();

                // เพิ่มโค้ชเข้า academy_members (ถ้าทีมอยู่ใน academy)
                $academyTeam = $this->db->table('academy_teams')
                    ->where('team_id', $invitation['team_id'])
                    ->get()->getRowArray();

                if ($academyTeam) {
                    $coachData = $this->getCoachById($coachId);
                    $coachUserId = $coachData['coach_user_id'] ?? null;

                    if ($coachUserId) {
                        // ตรวจสอบว่ายังไม่มี record อยู่
                        $existing = $this->db->table('academy_members')
                            ->where('academy_id', $academyTeam['academy_id'])
                            ->where('coach_user_id', $coachUserId)
                            ->get()->getRowArray();

                        if (!$existing) {
                            $this->db->table('academy_members')->insert([
                                'academy_id' => $academyTeam['academy_id'],
                                'coach_user_id' => $coachUserId,
                                'role' => 'coach',
                                'status' => 'approved',
                                'created_at' => date('Y-m-d H:i:s'),
                            ]);
                        } elseif ($existing['status'] !== 'approved') {
                            $this->db->table('academy_members')
                                ->where('id', $existing['id'])
                                ->update(['status' => 'approved', 'role' => 'coach']);
                        }
                    }
                }
            }

            // Mark the original invitation notification as read (non-critical)
            try {
                $this->db->table('notifications')
                    ->where('coach_id', $coachId)
                    ->where('type', 'coach_invitation')
                    ->like('data', '"invitation_id":' . (int)$invitationId)
                    ->set('is_read', 1)
                    ->update();
            } catch (\Exception $e) {
                log_message('error', 'Failed to mark notification as read: ' . $e->getMessage());
            }

            // Create notification for team admin (invited_by) - non-critical
            try {
                if ($invitation['invited_by']) {
                    $statusText = $response === 'accepted' ? 'ยอมรับ' : 'ปฏิเสธ';
                    $coachData = $this->getCoachById($coachId);
                    $coachName = ($coachData['first_name'] ?? '') . ' ' . ($coachData['last_name'] ?? '');
                    $coachName = trim($coachName) ?: 'ผู้ฝึกสอน';

                    // Find coach_id from user who sent invitation (if they are a coach)
                    $adminCoach = $this->db->table('coach')
                                         ->where('coach_user_id', $invitation['invited_by'])
                                         ->get()->getRowArray();

                    if ($adminCoach) {
                        $this->db->table('notifications')->insert([
                            'coach_id' => $adminCoach['id'],
                            'type' => 'invitation_response',
                            'title' => 'การตอบกลับคำเชิญ',
                            'message' => "{$coachName} ได้{$statusText}คำเชิญจากสโมสร {$invitation['team_name']}",
                            'data' => json_encode([
                                'invitation_id' => $invitationId,
                                'response' => $response,
                                'coach_name' => $coachName,
                                'team_name' => $invitation['team_name']
                            ]),
                            'is_read' => 0
                        ]);
                    }
                }
            } catch (\Exception $e) {
                log_message('error', 'Failed to create response notification: ' . $e->getMessage());
            }

            $this->db->transCommit();
            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error responding to invitation: ' . $e->getMessage());
            return false;
        }
    }

    public function getCertifications($coachId)
    {
        // ตรวจสอบว่าตาราง coach_certs มีอยู่หรือไม่
        if (!$this->db->tableExists('coach_certs')) {
            return [];
        }

        $builder = $this->db->table('coach_certs');
        $builder->select('coach_certs.*,
                            tbl_certs_level.name as level_name,
                            tbl_fifa_organizations.name as organ_name')
                ->join('tbl_certs_level', 'coach_certs.cert_level_id = tbl_certs_level.id', 'LEFT')
                ->join('tbl_fifa_organizations', 'coach_certs.organ_id = tbl_fifa_organizations.id', 'LEFT')
                ->where('coach_certs.coach_id', $coachId)
                ->where('coach_certs.is_active', 1);

        return $builder->get()->getResultArray();
    }

    public function getCoachCertificates($coachId)
    {
        // ตรวจสอบว่าตาราง coach_certs มีอยู่หรือไม่
        if (!$this->db->tableExists('coach_certs')) {
            return [];
        }

        $result = [];
        $table = "coach_certs";
        $builder = $this->db->table($table);
        $builder->select($table . ".*");
        $builder->where("coach_id", $coachId);
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getCertificationLevels()
    {
        $table = "tbl_certs_level";
        $builder = $this->db->table($table);
        $builder->select($table.".*");
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getOrganizations()
    {
        $table = "tbl_fifa_organizations";
        $builder = $this->db->table($table);
        $builder->select($table.".*");
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function saveCoachCertificates($input, $certImages = [])
    {
        // ตรวจสอบว่าตาราง coach_certs มีอยู่หรือไม่
        if (!$this->db->tableExists('coach_certs')) {
            return false;
        }

        try {
            $builder = $this->db->table("coach_certs");
            $coach_id = $input['coach_id'];

            // Clear existing certificates (same as admin logic)
            $builder = $this->db->table('coach_certs');
            $builder->where('coach_id', $coach_id)->delete();

            // Insert new certificates if data exists
            if (isset($input['id']) && is_array($input['id'])) {
                foreach ($input['id'] as $index => $value) {
                    // Only insert if organ_id and cert_level_id are provided
                    if (!empty($input['organ_id'][$index]) && !empty($input['cert_level_id'][$index])) {
                        $builder->set('coach_id', $coach_id);
                        $builder->set('organ_id', $input['organ_id'][$index]);
                        $builder->set('cert_level_id', $input['cert_level_id'][$index]);
                        $builder->set('license_code', $input['license_code'][$index] ?? '');
                        $builder->set('year', $input['year'][$index] ?? null);

                        // cert_image: new upload > existing > none
                        if (!empty($certImages[$index])) {
                            $builder->set('cert_image', $certImages[$index]);
                        } elseif (!empty($input['existing_cert_image'][$index])) {
                            $builder->set('cert_image', $input['existing_cert_image'][$index]);
                        }

                        $builder->set('create_date', date('Y-m-d H:i:s'));
                        $builder->insert();
                    }
                }
            }

            return true;

        } catch (\Exception $e) {
            log_message('error', 'Error saving coach certificates: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ตรวจสอบว่าผู้ฝึกสอนมีสิทธิ์เข้าดูทีมหรือไม่
     */
    public function canAccessTeam($coachId, $teamId)
    {
        $coach = $this->getCoachById($coachId);

        if (!$coach) {
            return false;
        }

        // ตรวจสอบว่าผู้ฝึกสอนอยู่ในทีมนี้หรือไม่
        return (int)$coach['team_id'] === (int)$teamId;
    }

    /**
     * ดึงข้อมูลทีมที่ผู้ฝึกสอนดูแล พร้อมข้อมูลรายละเอียด
     */
    public function getTeamDetails($teamId)
    {
        $builder = $this->db->table('team');
        $builder->select('team.*,
                            tbl_provinces.province_name,
                            tbl_districts.district_name as amphure_name,
                            tbl_subdistricts.subdistrict_name as tambon_name')
                ->join('tbl_provinces', 'team.zone_id = tbl_provinces.province_id', 'LEFT')
                ->join('tbl_districts', 'team.zone_id = tbl_districts.district_id', 'LEFT')
                ->join('tbl_subdistricts', 'team.zone_id = tbl_subdistricts.subdistrict_id', 'LEFT')
                ->where('team.id', $teamId)
                ->where('team.is_active', 1);

        return $builder->get()->getRowArray();
    }

    /**
     * ดึงรายชื่อผู้เล่นในทีม
     */
    public function getTeamPlayers($teamId)
    {
        $builder = $this->db->table('player');
        $builder->select('player.*,
                            tbl_player_position.name_th')
                ->join('tbl_player_position', 'player.position_id = tbl_player_position.id', 'LEFT')
                ->join('academy_player_enrollments pt_coach', 'pt_coach.player_id = player.id AND pt_coach.is_active = 1', 'INNER')
                ->where('pt_coach.team_id', $teamId)
                ->where('player.is_active', 1)
                ->groupBy('player.id')
                ->orderBy('player.first_name', 'ASC');

        return $builder->get()->getResultArray();
    }

    /**
     * ดึงรายชื่อผู้ฝึกสอนในทีม (หากมีผู้ฝึกสอนคนอื่นด้วย)
     */
    public function getTeamCoaches($teamId)
    {
        $coaches = [];
        $addedCoachIds = [];

        $addCoach = function (array $coach) use (&$coaches, &$addedCoachIds) {
            $coachId = (int) ($coach['id'] ?? 0);
            if ($coachId <= 0) {
                return;
            }

            if (!empty($coach['team_role'])) {
                $coach['position'] = $coach['team_role'];
            }

            if (in_array($coachId, $addedCoachIds, true)) {
                if (!empty($coach['team_role'])) {
                    foreach ($coaches as &$existingCoach) {
                        if ((int) ($existingCoach['id'] ?? 0) === $coachId) {
                            $existingCoach['position'] = $coach['team_role'];
                            $existingCoach['team_role'] = $coach['team_role'];
                            break;
                        }
                    }
                    unset($existingCoach);
                }
                return;
            }

            $coaches[] = $coach;
            $addedCoachIds[] = $coachId;
        };

        if ($this->db->tableExists('coach_invitations')) {
            $invitationCoaches = $this->db->table('coach_invitations')
                ->select('coach.*, coach_users.line_picture')
                ->join('coach', 'coach.id = coach_invitations.coach_id', 'inner')
                ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
                ->where('coach_invitations.team_id', $teamId)
                ->where('coach_invitations.status', 'accepted')
                ->where('coach.is_active', 1)
                ->get()
                ->getResultArray();

            foreach ($invitationCoaches as $coach) {
                $addCoach($coach);
            }
        }

        if ($this->db->tableExists('coach_teams')) {
            $teamCoaches = $this->db->table('coach_teams')
                ->select('coach.*, coach_users.line_picture, coach_teams.role AS team_role')
                ->join('coach', 'coach.id = coach_teams.coach_id', 'inner')
                ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
                ->where('coach_teams.team_id', $teamId)
                ->where('coach_teams.is_active', 1)
                ->where('coach.is_active', 1)
                ->get()
                ->getResultArray();

            foreach ($teamCoaches as $coach) {
                $addCoach($coach);
            }
        }

        $directCoaches = $this->db->table('coach')
            ->select('coach.*, coach_users.line_picture')
            ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
            ->where('coach.team_id', $teamId)
            ->where('coach.is_active', 1)
            ->orderBy('coach.first_name', 'ASC')
            ->get()
            ->getResultArray();

        foreach ($directCoaches as $coach) {
            $addCoach($coach);
        }

        usort($coaches, function ($a, $b) {
            return strcmp($a['first_name'] ?? '', $b['first_name'] ?? '');
        });

        return $coaches;
    }

    // =====================================================
    // Coach Team Join Request Methods
    // =====================================================

    /**
     * ค้นหาทีมที่ coach ยังไม่ได้เป็นสมาชิกและไม่มีคำขอ pending
     * @param int $coachId
     * @param string $keyword
     * @return array
     */
    public function searchTeamsForJoinRequest($coachId, $keyword)
    {
        try {
            // Get teams that coach already belongs to
            $existingTeams = $this->getCoachTeams($coachId);
            $existingTeamIds = array_column($existingTeams, 'id');

            // Get teams with pending requests (check if table exists first)
            $pendingTeamIds = [];
            if ($this->db->tableExists('coach_team_requests')) {
                $pendingRequests = $this->db->table('coach_team_requests')
                    ->select('team_id')
                    ->where('coach_id', $coachId)
                    ->where('status', 'pending')
                    ->where('is_active', 1)
                    ->get()
                    ->getResultArray();
                $pendingTeamIds = array_column($pendingRequests, 'team_id');
            }

            // Merge excluded team IDs
            $excludedTeamIds = array_unique(array_merge($existingTeamIds, $pendingTeamIds));

            // Search teams - remove is_active condition if column doesn't exist or has different values
            $builder = $this->db->table('team');
            $builder->select('team.id, team.name, team.logo, team.organize_name')
                    ->like('team.name', $keyword);

            // Check if is_active column exists and use it
            $fields = $this->db->getFieldNames('team');
            if (in_array('is_active', $fields)) {
                $builder->where('team.is_active', 1);
            }

            if (!empty($excludedTeamIds)) {
                $builder->whereNotIn('team.id', $excludedTeamIds);
            }

            $builder->limit(15);

            return $builder->get()->getResultArray();
        } catch (\Exception $e) {
            log_message('error', 'Error searching teams: ' . $e->getMessage());
            return [];
        }
    }

    /**
     * ตรวจสอบว่า coach มีความสัมพันธ์กับทีมอยู่แล้วหรือไม่
     * @param int $coachId
     * @param int $teamId
     * @return array ['exists' => bool, 'type' => string|null]
     */
    public function hasExistingRelationship($coachId, $teamId)
    {
        // Check if coach is already a member of the team
        $existingTeams = $this->getCoachTeams($coachId);
        foreach ($existingTeams as $team) {
            if ((int)$team['id'] === (int)$teamId) {
                return ['exists' => true, 'type' => 'member'];
            }
        }

        // Check if there's a pending request
        $pendingRequest = $this->db->table('coach_team_requests')
            ->where('coach_id', $coachId)
            ->where('team_id', $teamId)
            ->where('status', 'pending')
            ->where('is_active', 1)
            ->get()
            ->getRowArray();

        if ($pendingRequest) {
            return ['exists' => true, 'type' => 'pending_request'];
        }

        return ['exists' => false, 'type' => null];
    }

    /**
     * สร้างคำขอเข้าร่วมทีม
     * @param int $coachId
     * @param int $teamId
     * @param string|null $message
     * @param string $role
     * @return int|false Request ID or false on failure
     */
    public function createTeamJoinRequest($coachId, $teamId, $message = null, $role = 'assistant_coach')
    {
        // Check for existing relationship first
        $relationship = $this->hasExistingRelationship($coachId, $teamId);
        if ($relationship['exists']) {
            return false;
        }

        try {
            $data = [
                'coach_id' => $coachId,
                'team_id' => $teamId,
                'message' => $message,
                'role_requested' => $role,
                'status' => 'pending',
                'requested_at' => date('Y-m-d H:i:s'),
                'is_active' => 1
            ];

            $this->db->table('coach_team_requests')->insert($data);
            return $this->db->insertID();
        } catch (\Exception $e) {
            log_message('error', 'Error creating team join request: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงคำขอเข้าร่วมทีมของ coach
     * @param int $coachId
     * @param string|null $status Filter by status (null = all)
     * @return array
     */
    public function getCoachJoinRequests($coachId, $status = null)
    {
        $builder = $this->db->table('coach_team_requests');
        $builder->select('coach_team_requests.*, team.name as team_name, team.logo as team_logo')
                ->join('team', 'team.id = coach_team_requests.team_id', 'left')
                ->where('coach_team_requests.coach_id', $coachId)
                ->where('coach_team_requests.is_active', 1);

        if ($status !== null) {
            $builder->where('coach_team_requests.status', $status);
        }

        $builder->orderBy('coach_team_requests.requested_at', 'DESC');

        return $builder->get()->getResultArray();
    }

    /**
     * ดึงจำนวนคำขอ pending ของ coach
     * @param int $coachId
     * @return int
     */
    public function getPendingRequestsCount($coachId)
    {
        return $this->db->table('coach_team_requests')
            ->where('coach_id', $coachId)
            ->where('status', 'pending')
            ->where('is_active', 1)
            ->countAllResults();
    }

    /**
     * ยกเลิกคำขอเข้าร่วมทีม
     * @param int $requestId
     * @param int $coachId
     * @return bool
     */
    public function cancelJoinRequest($requestId, $coachId)
    {
        try {
            return $this->db->table('coach_team_requests')
                ->where('id', $requestId)
                ->where('coach_id', $coachId)
                ->where('status', 'pending')
                ->update(['is_active' => 0]);
        } catch (\Exception $e) {
            log_message('error', 'Error canceling join request: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * ดึงข้อมูลคำขอเข้าร่วมทีม
     * @param int $requestId
     * @return array|null
     */
    public function getJoinRequest($requestId)
    {
        return $this->db->table('coach_team_requests')
            ->select('coach_team_requests.*, team.name as team_name, team.logo as team_logo,
                      coach.first_name as coach_first_name, coach.last_name as coach_last_name,
                      coach.image_path as coach_image')
            ->join('team', 'team.id = coach_team_requests.team_id', 'left')
            ->join('coach', 'coach.id = coach_team_requests.coach_id', 'left')
            ->where('coach_team_requests.id', $requestId)
            ->get()
            ->getRowArray();
    }
}
