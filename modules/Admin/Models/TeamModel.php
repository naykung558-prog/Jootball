<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class TeamModel extends Model
{
    protected $table      = "team";
    protected $primaryKey = "";
    protected $allowedFields = [];

    function get_Teams($id = '')
    {
        if ($id) {
            $builder = $this->db->table($this->table);
            $builder->select("team.*")
                ->where("id", $id);
            $result = $builder->get()->getRowArray();
            return $result;
        } else {
            $builder = $this->db->table($this->table);
            $builder->select("team.*, a.name AS academy_name, a.logo AS academy_logo", false)
                ->join('academy_teams atx', 'atx.team_id = team.id', 'LEFT')
                ->join('academy a', 'a.id = atx.academy_id AND a.is_active = 1', 'LEFT')
                ->where('team.is_active', '1')
                ->groupBy('team.id');

            return DataTable::of($builder)
                ->add('team_name', function ($row) {
                    $displayName = !empty($row->name) ? $row->name : $row->name_en;
                    $name = '<a href="'.base_url('detail/team/'.$row->id).'" target="_blank">'.$displayName.'</a>';
                    return $name;
                })
                ->add('academy_display', function ($row) {
                    if (!empty($row->academy_name)) {
                        $logoUrl = !empty($row->academy_logo)
                            ? (filter_var($row->academy_logo, FILTER_VALIDATE_URL) ? $row->academy_logo : base_url($row->academy_logo))
                            : base_url('public/img/jootball_logo.png');
                        $fallback = base_url('public/img/jootball_logo.png');
                        $name = esc($row->academy_name);
                        return '<div class="d-flex align-items-center gap-2">'
                             . '<img src="' . $logoUrl . '" class="rounded-circle" style="width:28px;height:28px;object-fit:cover;" onerror="this.src=\'' . $fallback . '\'">'
                             . '<span>' . $name . '</span>'
                             . '</div>';
                    }
                    return '<span class="text-muted">-</span>';
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
                    $image = <<<HTML
                        <img src='$image_url' class="img-fluid rounded-circle" style="width: 50px;">
                    HTML;
                    return $image;
                })
                ->add('action', function ($row) {
                    $link = base_url('team/player-document-list?id=' . $row->id);
                    $link2 = base_url('team/export-player-chart?id=' . $row->id);
                    $edit_team = base_url('admin/team/manage/' . $row->id);
                    $langExport = lang('Common.export');
                    $langNameList = lang('Common.name_list_doc');
                    $langPhotoChart = lang('Common.photo_chart');
                    $langEdit = lang('Common.edit_btn');
                    $langDelete = lang('Common.delete_btn');
                    $tools = <<<HTML
                        <div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle btn-sm" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fa fa-cloud-download"></i> $langExport
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton">
                                <li><a class="dropdown-item" target="_blank" href="$link">$langNameList</a></li>
                                <li>
                                    <a class="dropdown-item" target="_blank" href="$link2">$langPhotoChart</a>
                                </li>
                            </ul>
                        </div>

                        <a href="$edit_team" class="btn btn-warning text-dark btn-sm">
                            <i class="fa fa-pencil"></i> $langEdit
                        </a>

                        <button type="button" class="btn btn-danger btn-sm" onclick="deletedTeam($row->id)">
                            <i class="fa fa-trash"></i> $langDelete
                        </button>
                    HTML;
                    return $tools;
                })->addNumbering()
                ->add('name', function ($row) {
                    return $row->name;
                })
                ->add('id', function ($row) {
                    return $row->id;
                })
                ->setSearchableColumns(['team.name', 'team.name_en', 'a.name'])
                ->toJson(true);
        }
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


        if (!empty($input['id'])) {
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->where('id', $input['id']);
            $builder->update();
        } else {
            $builder->set('create_by', session()->get('user_id'));
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->insert();
        }
    }

    function deleteTeam($id)
    {
        $builder = $this->db->table($this->table);
        $builder->set('is_active', '0')->where('id', $id);
        $builder->update();
        return "success";
    }

    /**
     * Get all pending team join requests for admin
     */
    function getAllPendingRequests()
    {
        $builder = $this->db->table('team_managers');
        $builder->select('team_managers.id, team_managers.team_id, team_managers.user_id, team_managers.status, team_managers.created_at, team_managers.updated_at, user.name as user_name, user.username, user.profile_image, team.name as team_name, team.logo as team_logo')
            ->join('user', 'user.id = team_managers.user_id', 'left')
            ->join('team', 'team.id = team_managers.team_id', 'left')
            ->where('team_managers.status', 'pending');
            // ->orderBy('team_managers.created_at', 'DESC');

        return DataTable::of($builder)
            ->add('user_info', function ($row) {
                $profile = !empty($row->profile_image) ? base_url($row->profile_image) : base_url('public/img/img_profile.jpg');
                return '<div class="d-flex align-items-center">
                    <img src="'.$profile.'" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                    <div>
                        <strong>'.$row->user_name.'</strong><br>
                        <small class="text-muted">@'.$row->username.'</small>
                    </div>
                </div>';
            })
            ->add('team_info', function ($row) {
                $logo = !empty($row->team_logo) ? base_url($row->team_logo) : base_url('public/img/jootball_logo.png');
                return '<div class="d-flex align-items-center">
                    <img src="'.$logo.'" class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                    <strong>'.$row->team_name.'</strong>
                </div>';
            })
            ->add('request_date', function ($row) {
                return date('d/m/Y H:i', strtotime($row->created_at));
            })
            ->add('action', function ($row) {
                return '<div class="btn-group" role="group">
                    <button type="button" class="btn btn-success btn-sm" onclick="approveRequest('.$row->id.')">
                        <i class="fas fa-check"></i> อนุมัติ
                    </button>
                    <button type="button" class="btn btn-danger btn-sm" onclick="rejectRequest('.$row->id.')">
                        <i class="fas fa-times"></i> ปฏิเสธ
                    </button>
                </div>';
            })
            ->edit('user_name', function ($row, $meta) {
                return $row->user_name;
            })
            ->edit('team_name', function ($row, $meta) {
                return $row->team_name;
            })
            ->edit('created_at', function ($row, $meta) {
                return $row->created_at;
            })
            ->addNumbering()
            ->toJson(true);
    }

    /**
     * Update team join request status
     */
    function updateRequestStatus($id, $status)
    {
        return $this->db->table('team_managers')
            ->where('id', $id)
            ->update([
                'status' => $status,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
    }

    /**
     * Get all teams for verification (include all active and inactive)
     */
    function get_Teams_Verify()
    {
        $builder = $this->db->table($this->table);
        $builder->select("$this->table.*");

        return DataTable::of($builder)
            ->add('team_name', function ($row) {
                $displayName = !empty($row->name) ? $row->name : $row->name_en;
                $name = '<a href="' . base_url('detail/team/' . $row->id) . '" target="_blank">' . $displayName . '</a>';
                return $name;
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
                $image = <<<HTML
                    <img src='$image_url' class="img-fluid rounded-circle" style="width: 50px;">
                HTML;
                return $image;
            })
            ->add('action', function ($row) {
                $langPermanentDelete = lang('Common.permanent_delete_label');
                $tools = <<<HTML
                    <button type="button" class="btn btn-danger btn-sm" onclick="permanentDeleteTeam($row->id, '$row->name')">
                        <i class="fa fa-trash"></i> $langPermanentDelete
                    </button>
                HTML;
                return $tools;
            })->addNumbering()
            ->add('id', function ($row) {
                return $row->id;
            })
            ->add('name', function ($row) {
                return $row->name;
            })
            ->setSearchableColumns(['name', 'name_en'])
            ->toJson(true);
    }

    /**
     * Permanently delete a team and set related data team_id = 0
     */
    function permanentDeleteTeam($id)
    {
        try {
            $this->db->transStart();

            // รวบรวม ID ทั้งหมดที่ต้องลบ (ทีมหลัก + ทีมรอง)
            $allTeamIds = [$id];
            $subteams = $this->db->table($this->table)
                ->where('parent_team_id', $id)
                ->get()->getResultArray();
            foreach ($subteams as $subteam) {
                $allTeamIds[] = $subteam['id'];
            }

            // ลบข้อมูลที่อ้างอิง team_id ทั้งหมด (รวมทีมหลัก + ทีมรอง)
            // 1. tournament_team (มี FK constraint → ต้องลบก่อน)
            $this->db->table('tournament_team')
                ->whereIn('team_id', $allTeamIds)
                ->delete();

            // 2. tournament_match_event
            $this->db->table('tournament_match_event')
                ->whereIn('team_id', $allTeamIds)
                ->delete();

            // 3. player_to_match
            $this->db->table('player_to_match')
                ->whereIn('team_id', $allTeamIds)
                ->delete();

            // 4. team_player_chart_selection
            if ($this->db->tableExists('team_player_chart_selection')) {
                $this->db->table('team_player_chart_selection')
                    ->whereIn('team_id', $allTeamIds)
                    ->delete();
            }

            // 5. coach_invitations
            if ($this->db->tableExists('coach_invitations')) {
                $this->db->table('coach_invitations')
                    ->whereIn('team_id', $allTeamIds)
                    ->delete();
            }

            // 6. coach_team_requests
            if ($this->db->tableExists('coach_team_requests')) {
                $this->db->table('coach_team_requests')
                    ->whereIn('team_id', $allTeamIds)
                    ->delete();
            }

            // 7. logs_player
            if ($this->db->tableExists('logs_player')) {
                $this->db->table('logs_player')
                    ->whereIn('team_id', $allTeamIds)
                    ->delete();
            }

            // 8. ลบนักเตะออกจากทีมที่ถูกลบ (academy_player_enrollments + legacy)
            $this->db->table('academy_player_enrollments')
                ->whereIn('team_id', $allTeamIds)
                ->delete();
            $this->db->table('player')
                ->whereIn('team_id', $allTeamIds)
                ->update(['team_id' => 0]);

            // 9. Update coaches: set team_id = 0 (เก็บผู้ฝึกสอนไว้)
            if ($this->db->tableExists('coach')) {
                $this->db->table('coach')
                    ->whereIn('team_id', $allTeamIds)
                    ->update(['team_id' => 0]);
            }

            // 10. Update team_managers: set team_id = 0
            if ($this->db->tableExists('team_managers')) {
                $this->db->table('team_managers')
                    ->whereIn('team_id', $allTeamIds)
                    ->update(['team_id' => 0]);
            }

            // ลบทีมรองก่อน แล้วลบทีมหลัก
            if (!empty($subteams)) {
                $subteamIds = array_column($subteams, 'id');
                $this->db->table($this->table)
                    ->whereIn('id', $subteamIds)
                    ->delete();
            }

            // ลบทีมหลัก
            $this->db->table($this->table)
                ->where('id', $id)
                ->delete();

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => lang('Common.error_delete_team')];
            }

            return ['status' => true, 'message' => lang('Common.permanent_delete_team_success')];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => lang('Common.error_occurred_msg') . ': ' . $e->getMessage()];
        }
    }
}
