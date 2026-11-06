<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class AcademyModel extends Model
{
    protected $table      = "academy";
    protected $primaryKey = "id";
    protected $allowedFields = [];

    // =====================================================
    // Academy List / Detail
    // =====================================================

    /**
     * ดึงข้อมูล Academy - ถ้ามี $id ดึงแถวเดียว, ถ้าไม่มีคืน DataTable JSON
     */
    function getAcademies($id = '')
    {
        if ($id) {
            $builder = $this->db->table($this->table);
            $builder->select("academy.*,
                              tbl_provinces.province_name,
                              tbl_districts.district_name as amphure_name,
                              tbl_subdistricts.subdistrict_name as tambon_name,
                              user.name as owner_name")
                ->join('tbl_provinces', 'tbl_provinces.province_id = academy.province_id', 'left')
                ->join('tbl_districts', 'tbl_districts.district_id = academy.amphure_id', 'left')
                ->join('tbl_subdistricts', 'tbl_subdistricts.subdistrict_id = academy.tambon_id', 'left')
                ->join('user', 'user.id = academy.create_by', 'left')
                ->where("academy.id", $id);
            $result = $builder->get()->getRowArray();
            return $result;
        } else {
            $builder = $this->db->table($this->table);
            $builder->select("academy.*");

            return DataTable::of($builder)
                ->add('image_logo', function ($row) {
                    $image_url = '';
                    if (!empty($row->logo)) {
                        $image_url = filter_var($row->logo, FILTER_VALIDATE_URL)
                            ? $row->logo
                            : base_url($row->logo);
                    } else {
                        $image_url = base_url('public/img/jootball_logo.png');
                    }
                    $image = <<<HTML
                        <img src='$image_url' class="img-fluid rounded-circle" style="width: 50px;">
                    HTML;
                    return $image;
                })
                ->add('academy_name', function ($row) {
                    $displayName = !empty($row->name) ? $row->name : $row->name_en;
                    $detail_url = base_url('admin/academy/detail/' . $row->id);
                    $name = '<a href="' . $detail_url . '">' . $displayName . '</a>';
                    return $name;
                })
                ->add('teams_count', function ($row) {
                    return $this->db->table('academy_teams')->where('academy_id', $row->id)->countAllResults();
                })
                ->add('members_count', function ($row) {
                    return $this->db->table('academy_members')->where('academy_id', $row->id)->countAllResults();
                })
                ->add('owner', function ($row) {
                    $user = $this->db->table('user')
                        ->select('name')
                        ->where('id', $row->create_by)
                        ->get()
                        ->getRowArray();
                    return $user ? $user['name'] : '-';
                })
                ->add('action', function ($row) {
                    $detail_url = base_url('admin/academy/detail/' . $row->id);
                    $edit_url = base_url('admin/academy/manage/' . $row->id);
                    $langDetail = lang('Common.detail');
                    $langEdit = lang('Common.edit_btn');
                    $rowId = $row->id;
                    $tools = <<<HTML
                        <a href="$detail_url" class="btn btn-info btn-sm"><i class="fa fa-eye"></i> $langDetail</a>
                        <a href="$edit_url" class="btn btn-warning text-dark btn-sm"><i class="fa fa-pencil"></i> $langEdit</a>
                    HTML;
                    return $tools;
                })
                ->addNumbering()
                ->add('name', function ($row) {
                    return $row->name;
                })
                ->add('id', function ($row) {
                    return $row->id;
                })
                ->setSearchableColumns(['name', 'name_en'])
                ->toJson(true);
        }
    }

    /**
     * Alias สำหรับ getAcademies($id) - ดึง Academy เดียว
     */
    function getAcademy($id)
    {
        return $this->getAcademies($id);
    }

    // =====================================================
    // Academy Teams
    // =====================================================

    /**
     * ดึงทีมทั้งหมดใน Academy พร้อมจำนวนผู้เล่น
     */
    function getAcademyTeams($academy_id)
    {
        return $this->db->table('academy_teams')
            ->select('team.id, team.name, team.name_en, team.logo, team.generation_name,
                      (SELECT COUNT(*) FROM academy_player_enrollments WHERE academy_player_enrollments.team_id = team.id AND academy_player_enrollments.is_active = 1) as player_count')
            ->join('team', 'team.id = academy_teams.team_id')
            ->where('academy_teams.academy_id', $academy_id)
            ->where('team.is_active', '1')
            ->orderBy('academy_teams.sort_order', 'ASC')
            ->get()
            ->getResultArray();
    }

    // =====================================================
    // Academy Members
    // =====================================================

    /**
     * ดึงสมาชิกทั้งหมดใน Academy พร้อมข้อมูล user/coach
     */
    function getAcademyMembers($academy_id)
    {
        return $this->db->table('academy_members')
            ->select('academy_members.*,
                      user.name as user_name, user.username as user_username, user.profile_image,
                      coach_users.username as coach_username, coach.first_name as coach_first_name,
                      coach.last_name as coach_last_name, coach.image_path as coach_image')
            ->join('user', 'user.id = academy_members.user_id', 'left')
            ->join('coach_users', 'coach_users.id = academy_members.coach_user_id', 'left')
            ->join('coach', 'coach.coach_user_id = academy_members.coach_user_id', 'left')
            ->where('academy_members.academy_id', $academy_id)
            ->orderBy("FIELD(academy_members.role, 'owner', 'admin', 'coach', 'viewer')", '', false)
            ->get()
            ->getResultArray();
    }

    // =====================================================
    // Save Academy
    // =====================================================

    /**
     * บันทึก Academy (สร้างใหม่หรืออัพเดท)
     */
    function saveAcademy($input)
    {
        $builder = $this->db->table($this->table);

        $fields = [
            'name', 'name_en', 'description', 'contact_name', 'contact_phone',
            'contact_email', 'contact_line', 'address',
            'province_id', 'amphure_id', 'tambon_id', 'latitude', 'longitude'
        ];

        foreach ($fields as $field) {
            if (!empty($input[$field])) {
                $builder->set($field, $input[$field]);
            }
        }

        // Handle numeric fields that could legitimately be 0
        $numericFields = ['province_id', 'amphure_id', 'tambon_id'];
        foreach ($numericFields as $field) {
            if (isset($input[$field]) && empty($input[$field])) {
                $builder->set($field, 0);
            }
        }

        if (!empty($input['logo'])) {
            $builder->set('logo', $input['logo']);
        }

        if (!empty($input['id'])) {
            // Update
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->where('id', $input['id']);
            $builder->update();
            return $input['id'];
        } else {
            // Insert
            $builder->set('create_by', session()->get('member_id') ?: session()->get('user_id'));
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->insert();
            return $this->db->insertID();
        }
    }

    // =====================================================
    // Toggle Active
    // =====================================================

    /**
     * สลับสถานะ is_active ระหว่าง 1 กับ 0
     */
    function toggleActive($id)
    {
        $academy = $this->db->table($this->table)
            ->select('is_active')
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$academy) {
            return false;
        }

        $newStatus = ($academy['is_active'] == 1) ? 0 : 1;

        $this->db->table($this->table)
            ->where('id', $id)
            ->update([
                'is_active' => $newStatus,
                'update_date' => date('Y-m-d H:i:s')
            ]);

        return $newStatus;
    }

    // =====================================================
    // Delete Academy (soft delete)
    // =====================================================

    /**
     * Soft delete Academy โดยเปลี่ยน is_active เป็น 0
     */
    function deleteAcademy($id)
    {
        $academy = $this->db->table($this->table)
            ->where('id', $id)
            ->get()
            ->getRowArray();

        if (!$academy) {
            return ['success' => false, 'message' => 'Academy not found'];
        }

        $this->db->table($this->table)
            ->where('id', $id)
            ->update([
                'is_active' => 0,
                'update_date' => date('Y-m-d H:i:s')
            ]);

        return ['success' => true, 'message' => 'Academy deleted successfully'];
    }

    // =====================================================
    // Zones
    // =====================================================

    /**
     * ดึง Zone ทั้งหมด
     */
    function getZones()
    {
        return $this->db->table('tbl_zone')
            ->select('*')
            ->get()
            ->getResultArray();
    }

    // =====================================================
    // Merge Teams into Academy
    // =====================================================

    /**
     * DataTable ของทีมทั้งหมด พร้อมแสดงสถานะว่าอยู่ใน Academy แล้วหรือไม่
     */
    function getAvailableTeamsForMerge()
    {
        $builder = $this->db->table('team');
        $builder->select("team.id, team.name, team.name_en, team.logo,
            team.contact_name, team.contact_phone, team.contact_email, team.contact_line,
            team.latitude, team.longitude,
            (SELECT COUNT(*) FROM academy_player_enrollments WHERE academy_player_enrollments.team_id = team.id AND academy_player_enrollments.is_active = 1) as player_count,
            (SELECT COUNT(*) FROM tournament_team WHERE tournament_team.team_id = team.id AND tournament_team.register_status = 1) as tournament_count,
            (SELECT academy.name FROM academy_teams JOIN academy ON academy.id = academy_teams.academy_id WHERE academy_teams.team_id = team.id LIMIT 1) as in_academy_name")
            ->where('team.is_active', 1);

        return DataTable::of($builder)
            ->add('checkbox', function ($row) {
                $dataAttrs = sprintf(
                    ' data-name="%s" data-name-en="%s" data-logo="%s" data-logo-url="%s" data-contact-name="%s" data-contact-phone="%s" data-contact-email="%s" data-contact-line="%s" data-latitude="%s" data-longitude="%s"',
                    esc($row->name ?? '', 'attr'),
                    esc($row->name_en ?? '', 'attr'),
                    esc($row->logo ?? '', 'attr'),
                    esc(!empty($row->logo) ? (filter_var($row->logo, FILTER_VALIDATE_URL) ? $row->logo : base_url($row->logo)) : '', 'attr'),
                    esc($row->contact_name ?? '', 'attr'),
                    esc($row->contact_phone ?? '', 'attr'),
                    esc($row->contact_email ?? '', 'attr'),
                    esc($row->contact_line ?? '', 'attr'),
                    esc($row->latitude ?? '', 'attr'),
                    esc($row->longitude ?? '', 'attr')
                );
                // ทีมที่อยู่ใน academy แล้ว = ยังเลือกได้ แต่บังคับเข้า academy ที่มีอยู่เท่านั้น (data-in-academy)
                $inAcademyAttr = !empty($row->in_academy_name)
                    ? ' data-in-academy="1" title="' . lang('Common.merge_team_already_in_academy') . '"'
                    : '';
                return '<input type="checkbox" class="form-check-input team-checkbox" value="' . $row->id . '"' . $dataAttrs . $inAcademyAttr . '>';
            })
            ->add('image_logo', function ($row) {
                $image_url = '';
                if (!empty($row->logo)) {
                    $image_url = filter_var($row->logo, FILTER_VALIDATE_URL)
                        ? $row->logo
                        : base_url($row->logo);
                } else {
                    $image_url = base_url('public/img/jootball_logo.png');
                }
                return '<img src="' . $image_url . '" class="img-fluid rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">';
            })
            ->add('team_name', function ($row) {
                $name = !empty($row->name) ? $row->name : $row->name_en;
                if (!empty($row->in_academy_name)) {
                    $name .= ' <span class="badge bg-warning text-dark ms-1" title="' . lang('Common.merge_team_already_in_academy') . '"><i class="fas fa-school me-1"></i>' . $row->in_academy_name . '</span>';
                }
                return $name;
            })
            ->add('coach_count', function ($row) {
                $count = $this->db->table('coach_teams')
                    ->where('team_id', $row->id)
                    ->countAllResults();
                return $count;
            })
            ->add('action', function ($row) {
                $detailUrl = base_url('admin/team/manage/' . $row->id);
                $langDetail = lang('Common.detail');
                return '<a href="' . $detailUrl . '" target="_blank" class="btn btn-info btn-sm"><i class="fa fa-eye me-1"></i>' . $langDetail . '</a>';
            })
            ->setSearchableColumns(['name', 'name_en'])
            ->toJson(true);
    }

    /**
     * ดึงรายละเอียดทีม (players + coaches) สำหรับ quick view
     */
    function getTeamDetailForMerge($teamId)
    {
        $team = $this->db->table('team')
            ->select('id, name, name_en, logo, generation_name')
            ->where('id', $teamId)
            ->get()
            ->getRowArray();

        if (!$team) return null;

        $team['players'] = $this->db->table('player')
            ->select('player.id, player.first_name, player.last_name, pt_adm.shirt_number as shirt_number, player.position, player.image')
            ->join('academy_player_enrollments pt_adm', 'pt_adm.player_id = player.id AND pt_adm.is_active = 1', 'INNER')
            ->where('pt_adm.team_id', $teamId)
            ->where('player.is_active', 1)
            ->groupBy('player.id, pt_adm.shirt_number')
            ->orderBy('pt_adm.shirt_number', 'ASC')
            ->get()
            ->getResultArray();

        $team['coaches'] = $this->db->table('coach_teams')
            ->select('coach.first_name, coach.last_name, coach.image_path, coach_roles.display_name as role_name')
            ->join('coach', 'coach.id = coach_teams.coach_id')
            ->join('coach_user_roles', 'coach_user_roles.coach_user_id = coach.coach_user_id', 'left')
            ->join('coach_roles', 'coach_roles.id = coach_user_roles.role_id', 'left')
            ->where('coach_teams.team_id', $teamId)
            ->get()
            ->getResultArray();

        $team['tournaments'] = $this->db->table('tournament_team')
            ->select('tournament.name as tour_name, tournament.name_en as tour_name_en')
            ->join('tournament', 'tournament.tour_id = tournament_team.tournament_id')
            ->where('tournament_team.team_id', $teamId)
            ->where('tournament_team.register_status', 1)
            ->get()
            ->getResultArray();

        return $team;
    }

    /**
     * ค้นหา Academy ที่ active สำหรับ Select2
     */
    function searchActiveAcademies($search = '')
    {
        $builder = $this->db->table('academy')
            ->select('id, name, name_en, logo')
            ->where('is_active', 1);

        if (!empty($search)) {
            $builder->groupStart()
                ->like('name', $search)
                ->orLike('name_en', $search)
                ->groupEnd();
        }

        if (!empty($search)) {
            $builder->limit(20);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * คืน team_id ที่อยู่ใน academy อยู่แล้ว (จากรายการที่ส่งมา) — ใช้ตรวจก่อน merge
     */
    public function getTeamsAlreadyInAcademy(array $teamIds): array
    {
        $teamIds = array_values(array_filter(array_map('intval', $teamIds)));
        if (empty($teamIds)) {
            return [];
        }
        $rows = $this->db->table('academy_teams')
            ->select('team_id')
            ->whereIn('team_id', $teamIds)
            ->groupBy('team_id')
            ->get()->getResultArray();
        return array_map('intval', array_column($rows, 'team_id'));
    }

    /**
     * แสดง preview ก่อนรวมทีม
     */
    function getMergePreview($teamIds, $targetType, $academyId = null)
    {
        $teams = [];
        foreach ($teamIds as $teamId) {
            $team = $this->db->table('team')
                ->select('id, name, name_en, logo, create_by')
                ->where('id', $teamId)
                ->get()
                ->getRowArray();

            if (!$team) continue;

            $team['player_count'] = $this->db->table('player')
                ->where('team_id', $teamId)
                ->where('is_active', 1)
                ->countAllResults();

            $team['coach_count'] = $this->db->table('coach_teams')
                ->where('team_id', $teamId)
                ->countAllResults();

            $team['tournament_count'] = $this->db->table('tournament_team')
                ->where('team_id', $teamId)
                ->where('register_status', 1)
                ->countAllResults();

            $team['already_in_academy'] = $this->db->table('academy_teams')
                ->where('team_id', $teamId)
                ->countAllResults() > 0;

            $team['has_owner'] = !empty($team['create_by']);

            $teams[] = $team;
        }

        $academy = null;
        if ($targetType === 'existing' && $academyId) {
            $academy = $this->db->table('academy')
                ->select('id, name, name_en, logo')
                ->where('id', $academyId)
                ->get()
                ->getRowArray();

            if ($academy) {
                $academy['current_teams'] = $this->db->table('academy_teams')
                    ->where('academy_id', $academyId)
                    ->countAllResults();
            }
        }

        return ['teams' => $teams, 'academy' => $academy];
    }

    /**
     * รวมทีมเข้า Academy จริง (Transaction)
     */
    function executeMerge($teamIds, $targetType, $academyId = null, $academyData = [])
    {
        $academyModule = new \Modules\Academy\Models\AcademyModel();

        $this->db->transBegin();

        try {
            if ($targetType === 'new') {
                $academyId = $this->saveAcademy($academyData);
                if (!$academyId) {
                    $this->db->transRollback();
                    return ['status' => false, 'message' => lang('Common.merge_error')];
                }

                // ปัญหา 1: เพิ่ม admin ที่ login อยู่เป็น owner ของ Academy ใหม่
                $currentAdminId = session()->get('member_id');
                if ($currentAdminId) {
                    $now = date('Y-m-d H:i:s');
                    $this->db->query(
                        "INSERT IGNORE INTO academy_members (academy_id, user_id, role, status, created_at, updated_at) VALUES (?, ?, 'owner', 'approved', ?, ?)",
                        [$academyId, $currentAdminId, $now, $now]
                    );
                }
            }

            $errors = [];
            foreach ($teamIds as $teamId) {
                $result = $academyModule->addTeamToAcademy($academyId, $teamId);
                if (!$result['status']) {
                    // ปัญหา 7: break ออกทันทีเมื่อ error
                    $errors[] = $result['message'];
                    break;
                }

                $now = date('Y-m-d H:i:s');

                // เพิ่ม team owner เป็น academy_members role=admin (INSERT IGNORE ป้องกัน duplicate)
                $team = $this->db->table('team')
                    ->select('create_by')
                    ->where('id', $teamId)
                    ->get()
                    ->getRowArray();

                if ($team && !empty($team['create_by'])) {
                    $this->db->query(
                        "INSERT IGNORE INTO academy_members (academy_id, user_id, role, status, created_at, updated_at) VALUES (?, ?, 'admin', 'approved', ?, ?)",
                        [$academyId, $team['create_by'], $now, $now]
                    );
                }

                // ปัญหา 2: เพิ่ม coach ทั้งหมดของทีมเป็น academy_members role=coach
                $coaches = $this->db->table('coach_teams')
                    ->select('coach.coach_user_id')
                    ->join('coach', 'coach.id = coach_teams.coach_id')
                    ->where('coach_teams.team_id', $teamId)
                    ->get()
                    ->getResultArray();

                foreach ($coaches as $coach) {
                    if (!empty($coach['coach_user_id'])) {
                        $this->db->query(
                            "INSERT IGNORE INTO academy_members (academy_id, coach_user_id, role, status, created_at, updated_at) VALUES (?, ?, 'coach', 'approved', ?, ?)",
                            [$academyId, $coach['coach_user_id'], $now, $now]
                        );
                    }
                }
            }

            if (!empty($errors)) {
                $this->db->transRollback();
                return ['status' => false, 'message' => implode(', ', $errors)];
            }

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return ['status' => false, 'message' => lang('Common.merge_error')];
            }

            $this->db->transCommit();
            return ['status' => true, 'academy_id' => $academyId, 'message' => lang('Common.merge_success')];

        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
