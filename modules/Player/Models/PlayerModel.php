<?php

namespace Modules\Player\Models;

use CodeIgniter\CLI\Console;
use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class PlayerModel extends Model
{
    protected $table      = "player";
    protected $primaryKey = "id";
    protected $allowedFields = [];

    function player_position($filter = array())
    {
        $builder = $this->db->table('tbl_player_position');
        if ($filter) {
            $builder->whereIn('id', $filter);
        }
        $result = $builder->select("*")->get()->getResultArray();
        return $result;
    }
    function player_sub_position($filter = array())
    {
        $builder = $this->db->table('tbl_player_position');

        // บังคับให้เป็น Flat Array และกำจัดค่าว่าง

        if (!is_array($filter)) {
            $filter = [$filter];
        }

        $filter = array_values(array_filter($filter));

        if (!empty($filter)) {
            // ระบุชื่อ Column ให้ตรงกับใน DB (สมมติว่าเป็น id)
            $builder->whereIn('id', $filter);
        }

        $builder->where('parent_id !=', 0);

        // ย้าย get() มาไว้บรรทัดสุดท้าย
        $query = $builder->get();
        return $query->getResultArray();
    }
    function get_Player($id = '', $teamId = null)
    {
        if ($id) {
            $builder = $this->db->table($this->table);
            $builder->select("player.*,
                COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id,
                ape_gp.shirt_number as shirt_number,
                ape_gp.sub_position_ids as player_sub_position_id,
                coarse_gp.name_th as position_name_th,
                coarse_gp.name_en as position_name_en");
            $builder->join("academy_player_enrollments ape_gp", "ape_gp.player_id = player.id AND ape_gp.is_primary = 1 AND ape_gp.is_active = 1", "LEFT");
            $builder->join("tbl_player_position", "tbl_player_position.id = ape_gp.position_id", "LEFT");
            $builder->join("tbl_player_position coarse_gp", "coarse_gp.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "LEFT");
            $builder->where("player.id", $id);
            $result = $builder->get()->getRowArray();
            return $result;
        } else {
            // Fetch all positions for lookup
            $positions = $this->db->table('tbl_player_position')->get()->getResultArray();
            $positionMap = [];
            foreach ($positions as $pos) {
                $positionMap[$pos['id']] = $pos['name_en'];
            }

            $builder = $this->db->table($this->table);
            $builder->select("player.id, player.first_name, player.last_name, player.nick_name, player.is_active, player.birth_date,
                COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id,
                pt_filter.shirt_number as shirt_number,
                pt_filter.sub_position_ids as player_sub_position_id,
                player.image_path,
                coarse_filter.name_th as position_name_th, coarse_filter.name_en as position_name_en");
            $builder->where($this->table.".is_active", 1);

            // Use passed team_id if provided, otherwise use session team_id
            $teamIdToUse = $teamId ? $teamId : session()->get("team.id");

            // Check for parent team (to share player pool)
            $teamInfo = $this->db->table('team')->select('id, parent_team_id')->where('id', $teamIdToUse)->get()->getRowArray();

            // JOIN academy_player_enrollments (multi-team support)
            $builder->join("academy_player_enrollments pt_filter", "pt_filter.player_id = player.id AND pt_filter.is_active = 1", "INNER");
            $builder->join("tbl_player_position", "tbl_player_position.id = pt_filter.position_id", "LEFT");
            $builder->join("tbl_player_position coarse_filter", "coarse_filter.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "LEFT");

            if ($teamInfo && !empty($teamInfo['parent_team_id'])) {
                // If sub-team, show players from both this team and parent team
                $builder->groupStart()
                    ->where("pt_filter.team_id", $teamIdToUse)
                    ->orWhere("pt_filter.team_id", $teamInfo['parent_team_id'])
                    ->groupEnd();
            } else {
                // Normal team or parent team
                $builder->where("pt_filter.team_id", $teamIdToUse);
            }

            $builder->groupBy("player.id");

            return DataTable::of($builder)
                ->add('image', function ($row) {
                    $imgSrc = !empty($row->image_path) ? base_url($row->image_path) : base_url('public/img/img_profile.jpg');
                    return '<img src="' . $imgSrc . '" class="rounded-circle border border-2 border-white shadow-sm" style="width: 40px; height: 40px; object-fit: cover;">';
                })
                ->add('full_name', function ($row) {
                    helper('academy');
                    return $row->first_name . ' ' . mask_last_name($row->last_name);
                })
                ->add('player_sub_position', function ($row) use ($positionMap) {
                    $positionNames = [];
                    $subPositions = [];

                    // Decode JSON or handle comma-separated string
                    if (!empty($row->player_sub_position_id)) {
                        // Decode HTML entities first (if present)
                        $decoded_html = html_entity_decode($row->player_sub_position_id);
                        $decoded = json_decode($decoded_html, true);

                        if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                            $subPositions = array_map('intval', $decoded); // Convert to integer
                        } else {
                            $subPositions = array_map('intval', array_filter(explode(',', $decoded_html))); // Convert to integer
                        }
                    }

                    // Map IDs to names (English names)
                    foreach ($subPositions as $posId) {
                        if (isset($positionMap[$posId])) {
                            $positionNames[] = $positionMap[$posId];
                        }
                    }

                    return !empty($positionNames) ? implode(', ', $positionNames) : '-';
                })
                ->edit('birth_date', function ($row) {
                    if (empty($row->birth_date)) {
                        return '-';
                    }

                    $months = [
                        1 => lang('Common.month_1'), lang('Common.month_2'), lang('Common.month_3'),
                        lang('Common.month_4'), lang('Common.month_5'), lang('Common.month_6'),
                        lang('Common.month_7'), lang('Common.month_8'), lang('Common.month_9'),
                        lang('Common.month_10'), lang('Common.month_11'), lang('Common.month_12')
                    ];

                    $timestamp = strtotime($row->birth_date);
                    $day = date('d', $timestamp);
                    $month = (int)date('m', $timestamp);
                    $isThai = service('request')->getLocale() === 'th';
                    $year = $isThai ? ((int)date('Y', $timestamp) + 543) : date('Y', $timestamp);

                    return "$day {$months[$month]} $year";
                })
                ->add('age', function ($row) {
                    if (empty($row->birth_date)) {
                        return '-';
                    }
                    $birthDate = new \DateTime($row->birth_date);
                    $now = new \DateTime();
                    return $now->diff($birthDate)->y;
                })
                ->add('action', function ($row) {
                    $encrypter = \Config\Services::encrypter();
                    $link = base_url('player/manage/' . bin2hex($encrypter->encrypt($row->id)));
                    $link_report_people = base_url('player_ex/report_people/' . bin2hex($encrypter->encrypt($row->id)));

                    $mangeIcon = "<a class='btn btn-info btn-sm rounded-pill' href='" . $link_report_people . "'target='_blank'><i class='ti ti-file text-white' style='font-size: 16px;'></i></a>";
                    $mangeIcon .= "<a class='btn btn-primary btn-sm rounded-pill' href=' " . $link  . "'><i class='ti ti-edit text-white' style='font-size: 16px;'></i></a>";

                    $mangeIcon .= " <button onclick='deletePlayer(" . $row->id . ")' class='btn btn-danger btn-sm rounded-pill'><i class='ti ti-trash text-white' style='font-size: 16px;'></i></button>";
                    return $mangeIcon;
                })->addNumbering()
                ->setSearchableColumns([])
                ->filter(function ($builder) {
                    $search = \Hermawan\DataTables\Request::get('search');
                    if (!empty($search['value'])) {
                        $val = trim($search['value']);
                        $builder->groupStart();
                        $builder->orLike('player.first_name', $val);
                        $builder->orLike('player.last_name', $val);
                        $builder->orLike('player.nick_name', $val);
                        $builder->orLike('pt_filter.shirt_number', $val);
                        $builder->orLike('player.birth_date', $val);
                        $builder->orLike('tbl_player_position.name_th', $val);
                        $builder->orLike('tbl_player_position.name_en', $val);
                        if (is_numeric($val)) {
                            $numVal = (int)$val;
                            // ค้นหาปี พ.ศ. (เช่น 2543) → แปลงเป็น ค.ศ.
                            if (strlen($val) == 4 && $numVal > 2400) {
                                $adYear = $numVal - 543;
                                $builder->orWhere('YEAR(player.birth_date)', $adYear);
                            }
                            // ค้นหาปี ค.ศ. (เช่น 2000)
                            if (strlen($val) == 4 && $numVal >= 1900 && $numVal <= 2400) {
                                $builder->orWhere('YEAR(player.birth_date)', $numVal);
                            }
                            // ค้นหาอายุ (1-2 หลัก)
                            if ($numVal >= 1 && $numVal <= 99 && strlen($val) <= 2) {
                                $builder->orWhere('TIMESTAMPDIFF(YEAR, player.birth_date, CURDATE())', $numVal);
                            }
                        }
                        $builder->groupEnd();
                    }
                })
                ->toJson(true);
        }
    }

    function get_Coachs($id = '', $teamId = null)
    {
        if ($id) {
            $builder = $this->db->table("coach");
            $builder->select("coach.*")
                ->where("id", $id);
            $result = $builder->get()->getRowArray();
            return $result;
        } else {
            if (!$teamId) {
                $teamId = session()->get("team.id");
            }
            if (!$teamId) {
                return DataTable::of($this->db->table("coach")->where("1", "0"))->toJson(true);
            }
            $teamId = (int) $teamId;
            $builder = $this->db->table("coach");
            $builder->select("coach.id, coach.first_name, coach.last_name, coach.nick_name, coach.birth_date,
                             coach.team_id, coach_invitations.status as invitation_status,
                             CASE
                                 WHEN coach.team_id = {$teamId} THEN 'member'
                                 WHEN coach_invitations.status = 'pending' THEN 'invited'
                                 ELSE 'none'
                             END as status", false);

            // Join กับตาราง coach_invitations เพื่อดูสถานะการเชิญ
            $builder->join("coach_invitations", "coach_invitations.coach_id = coach.id AND coach_invitations.team_id = {$teamId} AND coach_invitations.status = 'pending'", "LEFT");

            // ดึงข้อมูลที่เป็นสมาชิกในทีม หรือ ถูกเชิญ (pending)
            $builder->groupStart();
            $builder->where("coach.team_id", $teamId);
            $builder->orWhere("coach_invitations.status", 'pending');
            $builder->groupEnd();

            return DataTable::of($builder)
                ->add('full_name', function ($row) {
                    helper('academy');
                    $fullName = $row->first_name . ' ' . mask_last_name($row->last_name);
                    return '<a href="javascript:void(0)" onclick="viewCoachProfile(' . $row->id . ')" class="text-primary fw-bold" style="cursor: pointer; text-decoration: none;">' . $fullName . '</a>';
                })
                ->edit('birth_date', function ($row) {
                    // ตรวจสอบว่ามีข้อมูลวันเกิดหรือไม่
                    if (empty($row->birth_date)) {
                        return '-';
                    }

                    // สร้าง Array ของเดือนไทย (แบบย่อ)
                    $thai_months = [
                        1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                        'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
                    ];

                    // แปลงวันที่จาก string
                    $timestamp = strtotime($row->birth_date);

                    // ดึงส่วนต่างๆ ของวันที่
                    $day = date('d', $timestamp);
                    $month = (int)date('m', $timestamp); // แปลงเป็น int เพื่อใช้เป็น key ของ array
                    $year = (int)date('Y', $timestamp) + 543; // แปลงเป็น พ.ศ.

                    // ประกอบร่างวันที่รูปแบบใหม่
                    return "$day {$thai_months[$month]} $year";
                })
                ->add('status_badge', function ($row) {
                    // แสดง badge ตามสถานะ
                    if ($row->status == 'member') {
                        return '<span class="badge bg-success">สมาชิกในทีม</span>';
                    } elseif ($row->status == 'invited') {
                        return '<span class="badge bg-warning text-dark">รอการยืนยัน</span>';
                    } else {
                        return '<span class="badge bg-secondary">-</span>';
                    }
                })
                ->add('action', function ($row) {
                    $mangeIcon = '';

                    if ($row->status == 'member') {
                        // สมาชิกในทีม - แสดงปุ่มลบ
                        $mangeIcon = "<button onclick='deleteCoach(" . $row->id . ")' class='btn btn-danger btn-sm rounded-pill'><i class='ti ti-trash text-white' style='font-size: 16px;'></i> ลบออกจากทีม</button>";
                    } elseif ($row->status == 'invited') {
                        // ถูกเชิญแล้ว - แสดงปุ่มยกเลิกคำเชิญ
                        $mangeIcon = "<button onclick='cancelInvitation(" . $row->id . ")' class='btn btn-secondary btn-sm rounded-pill'><i class='ti ti-x text-white' style='font-size: 16px;'></i> ยกเลิกคำเชิญ</button>";
                    }

                    return $mangeIcon;
                })->addNumbering()
                ->setSearchableColumns(['first_name', 'last_name', 'nick_name'])
                ->toJson(true);
        }
    }

    function get_AllCoachs()
    {
        $builder = $this->db->table("coach");
        $builder->select("coach.id, coach.first_name, coach.last_name, coach.nick_name, coach.birth_date,
                         team.name as team_name,
                         coach_invitations.status as invitation_status,
                         CASE
                             WHEN coach.team_id = " . session()->get("team.id") . " THEN 'member'
                             WHEN coach_invitations.status = 'pending' THEN 'invited'
                             ELSE 'available'
                         END as status");
        $builder->join("team", "team.id = coach.team_id", "LEFT");
        $builder->join("coach_invitations", "coach_invitations.coach_id = coach.id AND coach_invitations.team_id = " . session()->get("team.id") . " AND coach_invitations.status = 'pending'", "LEFT");
        $builder->where("coach.is_active", 1);

        return DataTable::of($builder)
            ->add('full_name', function ($row) {
                helper('academy');
                return $row->first_name . ' ' . mask_last_name($row->last_name);
            })
            ->edit('birth_date', function ($row) {
                if (empty($row->birth_date)) {
                    return '-';
                }
                $thai_months = [
                    1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
                    'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
                ];
                $date = date_create($row->birth_date);
                $day = date_format($date, "d");
                $month_num = intval(date_format($date, "m"));
                $year = intval(date_format($date, "Y")) + 543;
                return $day . ' ' . $thai_months[$month_num] . ' ' . $year;
            })
            ->add('action', function ($row) {
                $mangeIcon = '';
                if ($row->status == 'available') {
                    $mangeIcon .= "<button onclick='inviteCoach(" . $row->id . ")' class='btn btn-success btn-sm rounded-pill mx-1'><i class='ti ti-plus text-white' style='font-size: 16px;'></i> เชิญ</button>";
                } elseif ($row->status == 'invited') {
                    $mangeIcon .= "<span class='badge bg-warning text-dark'>รอการยืนยัน</span>";
                    $mangeIcon .= " <button onclick='cancelInvitation(" . $row->id . ")' class='btn btn-secondary btn-sm rounded-pill mx-1'><i class='ti ti-x text-white' style='font-size: 16px;'></i> ยกเลิกคำเชิญ</button>";
                } else {
                    $mangeIcon .= "<span class='badge bg-primary'>สมาชิกในทีม</span>";
                    $mangeIcon .= " <button onclick='deleteCoach(" . $row->id . ")' class='btn btn-danger btn-sm rounded-pill mx-1'><i class='ti ti-trash text-white' style='font-size: 16px;'></i></button>";
                }
                return $mangeIcon;
            })
            ->addNumbering()
            ->setSearchableColumns(['first_name', 'last_name', 'nick_name', 'team.name'])
            ->toJson(true);
    }

    function inviteCoach($coachId)
    {
        try {
            // ตรวจสอบว่ามี team หรือไม่
            $teamId = session()->get("team.id") ?? session()->get("team")['id'] ?? null;
            $teamName = session()->get("team.name") ?? session()->get("team")['name'] ?? null;
            $userId = session()->get("user_id");

            if (!$teamId || !$userId) {
                log_message('error', 'Invite coach failed: No team or user in session');
                return false;
            }

            // Check if there's already a pending invitation
            $existingInvitation = $this->db->table("coach_invitations")
                ->where('coach_id', $coachId)
                ->where('team_id', $teamId)
                ->where('status', 'pending')
                ->get()->getRowArray();

            if ($existingInvitation) {
                return false; // Already invited
            }

            // Create new invitation
            $builder = $this->db->table("coach_invitations");
            $data = [
                'coach_id' => $coachId,
                'team_id' => $teamId,
                'invited_by' => $userId,
                'status' => 'pending',
                'message' => 'คุณได้รับคำเชิญให้เข้าร่วมทีม ' . $teamName,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $result = $builder->insert($data);

            // If invitation was created successfully, also create a notification
            if ($result === true || (is_object($result) && $result)) {
                $invitationId = $this->db->insertID();

                if ($invitationId) {
                    // Load notification model and create notification
                    $notificationModel = new \Modules\Coach\Models\NotificationModel();
                    $notificationModel->createInvitationNotification(
                        $coachId,
                        $teamName,
                        $invitationId
                    );
                }
                return true;
            }

            return false;
        } catch (\Exception $e) {
            log_message('error', 'Invite coach exception: ' . $e->getMessage());
            return false;
        }
    }

    function cancelInvitation($coachId)
    {
        // ตรวจสอบว่ามี team หรือไม่
        $teamId = session()->get("team.id") ?? session()->get("team")['id'] ?? null;

        if (!$teamId) {
            log_message('error', 'Cancel invitation failed: No team in session');
            return false;
        }

        $builder = $this->db->table("coach_invitations");
        $builder->set('status', 'rejected');
        $builder->set('responded_at', date('Y-m-d H:i:s'));
        $builder->where('coach_id', $coachId);
        $builder->where('team_id', $teamId);
        $builder->where('status', 'pending');

        return $builder->update();
    }

    function getCoachInvitations($coachId)
    {
        $builder = $this->db->table("coach_invitations");
        $builder->select("coach_invitations.*, team.name as team_name, team.logo as team_logo,
                         user.first_name as invited_by_name, user.last_name as invited_by_lastname")
                ->join("team", "team.id = coach_invitations.team_id", "LEFT")
                ->join("user", "user.id = coach_invitations.invited_by", "LEFT")
                ->where("coach_invitations.coach_id", $coachId)
                ->where("coach_invitations.status", "pending")
                ->where("coach_invitations.is_active", 1)
                ->orderBy("coach_invitations.created_at", "DESC");

        return $builder->get()->getResultArray();
    }

    function respondToInvitation($invitationId, $response, $coachId)
    {
        // First check if this invitation belongs to the coach
        $invitation = $this->db->table("coach_invitations")
            ->where('id', $invitationId)
            ->where('coach_id', $coachId)
            ->where('status', 'pending')
            ->get()->getRowArray();

        if (!$invitation) {
            return false;
        }

        // Update invitation status
        $builder = $this->db->table("coach_invitations");
        $builder->set('status', $response);
        $builder->set('responded_at', date('Y-m-d H:i:s'));
        $builder->where('id', $invitationId);
        $result = $builder->update();

        // If accepted, update coach's team_id
        if ($response === 'accepted' && $result) {
            $coachBuilder = $this->db->table("coach");
            $coachBuilder->set('team_id', $invitation['team_id']);
            $coachBuilder->set('update_date', date('Y-m-d H:i:s'));
            $coachBuilder->where('id', $coachId);
            $coachBuilder->update();
        }

        return $result;
    }

    function get_PlayerList($id, $position = array(), $orderBy = '', $maskNames = true)
    {
        $builder = $this->db->table($this->table);
        // ANY_VALUE: คอลัมน์จาก join ไม่อยู่ใน GROUP BY player.id — จำเป็นเมื่อ sql_mode มี ONLY_FULL_GROUP_BY
        $builder->select("player.id, player.first_name, player.last_name, player.nick_name, player.birth_date,
            ANY_VALUE(COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)) as player_position_id,
            ANY_VALUE(pt_list.shirt_number) as shirt_number,
            ANY_VALUE(pt_list.sub_position_ids) as player_sub_position_id,
            player.image_path, player.id_card, player.id_card_image,
            ANY_VALUE(coarse_list.name_th) as position_name_th, ANY_VALUE(coarse_list.name_en) as position_name_en");
        $builder->join("academy_player_enrollments pt_list", "pt_list.player_id = player.id AND pt_list.is_active = 1", "INNER");
        $builder->join("tbl_player_position", "tbl_player_position.id = pt_list.position_id", "LEFT");
        $builder->join("tbl_player_position coarse_list", "coarse_list.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)", "LEFT");

        // Check for parent team (to share player pool matching logic in get_Player)
        $teamInfo = $this->db->table('team')->select('id, parent_team_id')->where('id', $id)->get()->getRowArray();

        if ($teamInfo && !empty($teamInfo['parent_team_id'])) {
            $builder->groupStart()
                ->where("pt_list.team_id", $id)
                ->orWhere("pt_list.team_id", $teamInfo['parent_team_id'])
                ->groupEnd();
        } else {
            $builder->where("pt_list.team_id", $id);
        }
        if (!empty($position)) {
            $builder->where("COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) NOT IN (".implode(',', array_map('intval', $position)).")", null, false);
        }
        if (!empty($orderBy)) {
            $builder->orderBy($orderBy);
        }
        $builder->groupBy("player.id");
        $builder->orderBy("CAST(ANY_VALUE(pt_list.shirt_number) AS unsigned)", 'ASC', false);
        $result = $builder->get()->getResultArray();

        // Censor last names for display
        if ($maskNames) {
            helper('academy');
            foreach ($result as &$player) {
                if (!empty($player['last_name'])) {
                    $player['last_name'] = mask_last_name($player['last_name']);
                }
            }
        }

        return $result;
    }


    function get_PlayerListById($playerIds = array(), $position = array(), $orderBy = '', $maskNames = true)
    {
        $builder = $this->db->table($this->table);

        $builder->select("
            player.id,
            player.first_name,
            player.last_name,
            player.nick_name,
            player.birth_date,
            COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id,
            ape_byid.shirt_number as shirt_number,
            ape_byid.sub_position_ids as player_sub_position_id,
            player.image_path,
            player.id_card,
            player.id_card_image,
            coarse_byid.name_th as position_name_th,
            coarse_byid.name_en as position_name_en
        ");

        $builder->join(
            "academy_player_enrollments ape_byid",
            "ape_byid.player_id = player.id AND ape_byid.is_primary = 1 AND ape_byid.is_active = 1",
            "LEFT"
        );

        $builder->join(
            "tbl_player_position",
            "tbl_player_position.id = ape_byid.position_id",
            "LEFT"
        );

        $builder->join(
            "tbl_player_position coarse_byid",
            "coarse_byid.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)",
            "LEFT"
        );

        if (!empty($playerIds)) {
            $builder->whereIn("player.id", $playerIds);
        }

        if (!empty($position)) {
            $builder->where("COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) NOT IN (".implode(',', array_map('intval', $position)).")", null, false);
        }

        if (!empty($orderBy)) {
            $builder->orderBy($orderBy);
        }

        $builder->groupBy("player.id");
        $builder->orderBy("CAST(ape_byid.shirt_number AS unsigned) ASC");

        $result = $builder->get()->getResultArray();

        // Censor last names for display
        if ($maskNames) {
            helper('academy');
            foreach ($result as &$player) {
                if (!empty($player['last_name'])) {
                    $player['last_name'] = mask_last_name($player['last_name']);
                }
            }
        }

        return $result;
    }

    /**
     * ดึงทีมทั้งหมดที่นักเตะสังกัดอยู่
     */
    function get_PlayerTeams(int $player_id): array
    {
        return $this->db->table('academy_player_enrollments ape')
            ->select('ape.*, team.name as team_name, team.logo as team_logo,
                      ape.academy_id, academy.name as academy_name')
            ->join('team', 'team.id = ape.team_id', 'LEFT')
            ->join('academy', 'academy.id = ape.academy_id', 'LEFT')
            ->where('ape.player_id', $player_id)
            ->where('ape.is_active', 1)
            ->orderBy('ape.is_primary', 'DESC')
            ->orderBy('ape.enrolled_at', 'ASC')
            ->get()->getResultArray();
    }

    /**
     * เพิ่มนักเตะเข้าทีม
     */
    function addPlayerToTeam(int $player_id, int $team_id, bool $isPrimary = false): bool
    {
        $academyId = $this->academyIdForTeam($team_id);
        if ($academyId === null) return false; // team not under any academy
        $existing = $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)->where('team_id', $team_id)
            ->get()->getRowArray();
        if ($existing) return false;
        // นักเตะที่ถูกปิดไว้ (soft-deleted) → เปิดใช้งานกลับเมื่อเพิ่มเข้าทีม
        $this->db->table('player')->where('id', $player_id)->where('is_active', 0)->update(['is_active' => 1]);
        if ($isPrimary) {
            $this->db->table('academy_player_enrollments')
                ->where('player_id', $player_id)->update(['is_primary' => 0]);
        }
        $count = $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)->where('is_active', 1)->countAllResults();
        if ($count === 0) $isPrimary = true;
        $this->db->table('academy_player_enrollments')->insert([
            'player_id'  => $player_id, 'academy_id' => $academyId, 'team_id' => $team_id,
            'is_primary' => $isPrimary ? 1 : 0, 'is_active' => 1,
            'enrolled_at' => date('Y-m-d H:i:s'),
        ]);
        $this->syncLegacyTeamId($player_id);
        return true;
    }

    /**
     * ลบนักเตะออกจากทีม โดยยังคงสถานะ enrolled ใน academy ไว้
     *
     * academy_player_enrollments เก็บทั้ง "อยู่ academy ไหน" และ "อยู่ทีมไหน" ในแถวเดียวกัน
     * การลบแถวทิ้งจึงถอนนักเตะออกจาก academy ไปด้วย ไม่ใช่แค่ออกจากทีม
     * ถ้าเป็นแถวสุดท้ายของนักเตะใน academy นั้น จึงแปลงเป็น team_id=0
     * ("อยู่ academy แต่ยังไม่มีทีม") แทนการลบ
     */
    function removePlayerFromTeam(int $player_id, int $team_id): bool
    {
        $existing = $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)->where('team_id', $team_id)
            ->get()->getRowArray();
        if (!$existing) return false;

        $wasPrimary = (bool)$existing['is_primary'];
        $academyId  = (int)$existing['academy_id'];

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

        if ($otherTeams > 0 || $limbo) {
            // มีแถวอื่นยึดโยงกับ academy อยู่แล้ว → ลบแถวของทีมนี้ได้เลย
            // (ถ้าดันแปลงเป็น team_id=0 จะกลายเป็นแถวซ้ำที่ทำให้ตัวเลือกทีมเพี้ยน)
            $this->db->table('academy_player_enrollments')
                ->where('player_id', $player_id)->where('team_id', $team_id)->delete();

            if ($otherTeams === 0 && $limbo && (int)$limbo['is_active'] !== 1) {
                $this->db->table('academy_player_enrollments')
                    ->where('id', $limbo['id'])
                    ->update(['is_active' => 1, 'left_at' => null]);
            }
        } else {
            // แถวสุดท้ายใน academy → แปลงเป็น "อยู่ academy ไม่มีทีม" แทนการลบ
            $this->db->table('academy_player_enrollments')
                ->where('id', $existing['id'])
                ->update([
                    'team_id'    => 0,
                    'is_primary' => 0,
                    'is_active'  => 1,
                    'left_at'    => null,
                ]);
        }

        if ($wasPrimary) {
            // ต้องเป็นแถวที่มีทีมจริง ไม่งั้น syncLegacyTeamId จะไปเซ็ต player.team_id = 0
            $next = $this->db->table('academy_player_enrollments')
                ->where('player_id', $player_id)->where('is_active', 1)
                ->where('team_id >', 0)
                ->orderBy('enrolled_at', 'ASC')->limit(1)->get()->getRowArray();
            if ($next) {
                $this->db->table('academy_player_enrollments')
                    ->where('id', $next['id'])->update(['is_primary' => 1]);
            }
        }
        $this->syncLegacyTeamId($player_id);
        return true;
    }

    /**
     * Sync player.team_id (legacy) จาก academy_player_enrollments ที่ is_primary = 1
     */
    private function syncLegacyTeamId(int $player_id): void
    {
        $primary = $this->db->table('academy_player_enrollments')
            ->where('player_id', $player_id)->where('is_primary', 1)
            ->where('is_active', 1)->limit(1)->get()->getRowArray();
        $newTeamId = $primary ? (int)$primary['team_id'] : null;
        $this->db->table('player')->where('id', $player_id)
            ->update(['team_id' => $newTeamId]);
    }

    /** team_id -> academy_id via academy_teams (every team has one after migration) */
    private function academyIdForTeam(int $team_id): ?int
    {
        $row = $this->db->table('academy_teams')->select('academy_id')
            ->where('team_id', $team_id)->limit(1)->get()->getRowArray();
        return $row ? (int)$row['academy_id'] : null;
    }

    /**
     * หานักเตะที่ใช้เลขบัตรประชาชนเดียวกัน (active) — คืน row [id, first_name, last_name] หรือ null
     * ใช้ก่อนบันทึกนักเตะใหม่ เพื่อเด้งถามเพิ่มจากระบบแทนการสร้างซ้ำ (normalize ตัดขีด/ช่องว่าง)
     */
    public function findDuplicateIdCard(string $rawIdCard, $excludeId = null): ?array
    {
        helper('id_card');
        $norm = id_card_normalize($rawIdCard);
        if (strlen($norm) !== 13) {
            return null;
        }
        $sql    = "SELECT id, first_name, last_name FROM player
                   WHERE REPLACE(REPLACE(id_card, '-', ''), ' ', '') = ?";
        $params = [$norm];
        if (!empty($excludeId)) {
            $sql     .= " AND id <> ?";
            $params[] = $excludeId;
        }
        return $this->db->query($sql, $params)->getRowArray() ?: null;
    }

    function savePlayer($input)
    {
        // Prevention: block creating/updating to a duplicate or malformed 13-digit id_card.
        // Only full 13-digit ids are checked (matches the uq_player_id_card DB index scope);
        // partial / empty ids are left unvalidated. Throws so controllers surface the message.
        helper('id_card');
        $rawIdCard  = $input['id_card'] ?? '';
        $normIdCard = id_card_normalize($rawIdCard);
        if (strlen($normIdCard) === 13) {
            if (!id_card_is_valid($rawIdCard)) {
                throw new \RuntimeException('เลขบัตรประชาชนไม่ถูกต้อง (กรุณาตรวจสอบเลข 13 หลัก)');
            }
            $dupSql    = "SELECT id, first_name, last_name FROM player
                          WHERE REPLACE(REPLACE(id_card, '-', ''), ' ', '') = ?";
            $dupParams = [$normIdCard];
            if (!empty($input['id'])) {
                $dupSql      .= " AND id <> ?";
                $dupParams[] = $input['id'];
            }
            $dup = $this->db->query($dupSql, $dupParams)->getRowArray();
            if ($dup) {
                throw new \RuntimeException(
                    "เลขบัตรนี้มีนักเตะอยู่แล้ว: {$dup['first_name']} {$dup['last_name']} "
                    . "(id {$dup['id']}) — กรุณาเพิ่มนักเตะเดิมเข้าทีมแทนการสร้างใหม่"
                );
            }
        }

        $builder = $this->db->table($this->table);

        if (!empty($input['first_name'])) {
            $builder->set('first_name', $input['first_name']);
        }
        if (!empty($input['last_name'])) {
            $builder->set('last_name', $input['last_name']);
        }
        if (!empty($input['nick_name'])) {
            $builder->set('nick_name', $input['nick_name']);
        }
        if (!empty($input['nation'])) {
            $builder->set('nation', $input['nation']);
        }
        if (!empty($input['id_card'])) {
            $builder->set('id_card', $input['id_card']);
        }
        // Handle sex field (radio button: M or W)
        if (isset($input['sex']) && in_array($input['sex'], ['M', 'W'])) {
            $builder->set('sex', $input['sex']);
        } else {
            $builder->set('sex', null);
        }
        if (!empty($input['birth_date'])) {
            $format_birthdate = date('Y-m-d', strtotime(str_replace('/', '-', $input['birth_date'])));
            $builder->set('birth_date', $format_birthdate);
        }
        if (!empty($input['player_position_id'])) {
            $builder->set('player_position_id', $input['player_position_id']);
         } else {
         $builder->set('player_position_id', null);
         }
        if (!empty($input['shirt_number'])) {
            $builder->set('shirt_number', $input['shirt_number']);
        } else {
            $builder->set('shirt_number', "");
        }

        if (!empty($input['dominant_foot'])) {
            $builder->set('dominant_foot', $input['dominant_foot']);
        }
        if (!empty($input['height'])) {
            $builder->set('height', $input['height']);
        }
        if (!empty($input['weight'])) {
            $builder->set('weight', $input['weight']);
        }
        if (!empty($input['bmi'])) {
            $builder->set('bmi', $input['bmi']);
        }
        if (!empty($input['province_id'])) {
            $builder->set('province_id', $input['province_id']);
        } else {
            $builder->set('province_id', null);
        }
        if (!empty($input['amphure_id'])) {
            $builder->set('amphure_id', $input['amphure_id']);
        } else {
            $builder->set('amphure_id', null);
        }
        if (!empty($input['tambon_id'])) {
            $builder->set('tambon_id', $input['tambon_id']);
        } else {
            $builder->set('tambon_id', null);
        }
        // Handle player_sub_position_id as an array
        if (!empty($input['player_sub_position_id']) && is_array($input['player_sub_position_id'])) {
        $builder->set('player_sub_position_id', json_encode($input['player_sub_position_id']));
        } else {
        $builder->set('player_sub_position_id', "");
        }

        if (!empty($input['image_path'])) {
            $builder->set('image_path', $input['image_path']);
        }

        if (isset($input['id_card_image'])) {
            $builder->set('id_card_image', $input['id_card_image']);
        }

        // จัดการ team_id ให้ถูกต้อง (legacy field - sync จาก academy_player_enrollments)
        $resolvedTeamId = null;
        if (isset($input['team_id']) && $input['team_id'] !== '' && $input['team_id'] !== null) {
            $resolvedTeamId = (int)$input['team_id'];
        } elseif (empty($input['id'])) {
            $resolvedTeamId = (int)session()->get('team.id') ?: null;
        }
        if ($resolvedTeamId) {
            $builder->set('team_id', $resolvedTeamId);
        }

        if (!empty($input['id'])) {
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->where('id', $input['id']);
            $builder->update();
            $savedId = (int)$input['id'];
        } else {
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->set('update_date', date('Y-m-d H:i:s'));
            try {
                $builder->insert();
            } catch (\Throwable $e) {
                if (stripos($e->getMessage(), 'uq_player_id_card') !== false
                    || stripos($e->getMessage(), 'Duplicate entry') !== false) {
                    throw new \RuntimeException('เลขบัตรนี้มีนักเตะอยู่แล้วในระบบ');
                }
                throw $e;
            }
            $savedId = (int)$this->db->insertID();
        }

        // Sync academy_player_enrollments: เพิ่มทีมหลักถ้ายังไม่มี
        if ($savedId && $resolvedTeamId) {
            $this->addPlayerToTeam($savedId, $resolvedTeamId, true);
        }

        // เขียน shirt/position/sub ลง enrollment (source of truth สำหรับการอ่าน)
        // เป้าหมาย: enrollment ของทีมที่บันทึก ถ้าไม่ระบุทีม → enrollment หลัก (primary)
        if ($savedId) {
            $enroll = [];
            if (isset($input['shirt_number'])) {
                $enroll['shirt_number'] = $input['shirt_number'] ?: '';
            }
            if (array_key_exists('player_position_id', $input)) {
                $enroll['position_id'] = !empty($input['player_position_id']) ? $input['player_position_id'] : null;
            }
            if (isset($input['player_sub_position_id'])) {
                $enroll['sub_position_ids'] = is_array($input['player_sub_position_id'])
                    ? json_encode($input['player_sub_position_id'])
                    : ($input['player_sub_position_id'] ?: null);
            }
            if (!empty($enroll)) {
                $target = $this->db->table('academy_player_enrollments')->where('player_id', $savedId);
                if ($resolvedTeamId) {
                    $target->where('team_id', $resolvedTeamId);
                } else {
                    $target->where('is_primary', 1)->where('is_active', 1);
                }
                $target->update($enroll);
            }
        }

        return $savedId;
    }
    function saveCoach($input)
    {
        $builder = $this->db->table("coach");

        if (!empty($input['first_name'])) {
            $builder->set('first_name', $input['first_name']);
        }
        if (!empty($input['last_name'])) {
            $builder->set('last_name', $input['last_name']);
        }
        if (!empty($input['nick_name'])) {
            $builder->set('nick_name', $input['nick_name']);
        }
        if (!empty($input['nation'])) {
            $builder->set('nation', $input['nation']);
        }
        if (!empty($input['id_card'])) {
            $builder->set('id_card', $input['id_card']);
        }
        // Handle sex field (radio button: M or W)
        if (isset($input['sex']) && in_array($input['sex'], ['M', 'W'])) {
            $builder->set('sex', $input['sex']);
        } else {
            $builder->set('sex', null);
        }
        if (!empty($input['birth_date'])) {
            $format_birthdate = date('Y-m-d', strtotime(str_replace('/', '-', $input['birth_date'])));
            $builder->set('birth_date', $format_birthdate);
        }

        if (!empty($input['dominant_foot'])) {
            $builder->set('dominant_foot', $input['dominant_foot']);
        }
        if (!empty($input['height'])) {
            $builder->set('height', $input['height']);
        }
        if (!empty($input['weight'])) {
            $builder->set('weight', $input['weight']);
        }
        if (!empty($input['bmi'])) {
            $builder->set('bmi', $input['bmi']);
        }
        if (!empty($input['province_id'])) {
            $builder->set('province_id', $input['province_id']);
        }
        if (!empty($input['amphure_id'])) {
            $builder->set('amphure_id', $input['amphure_id']);
        }
        if (!empty($input['tambon_id'])) {
            $builder->set('tambon_id', $input['tambon_id']);
        }

        if (!empty($input['image_path'])) {
            $builder->set('image_path', $input['image_path']);
        }

        // จัดการ team_id ให้ถูกต้อง
        if (isset($input['team_id']) && $input['team_id'] !== '' && $input['team_id'] !== null) {
            $builder->set('team_id', (int)$input['team_id']);
        } elseif (empty($input['id'])) {
            // กรณีเพิ่มใหม่ ต้องมี team_id จาก session
            $teamId = session()->get('team.id');
            if ($teamId) {
                $builder->set('team_id', (int)$teamId);
            }
        }

        if (!empty($input['id'])) {
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->where('id', $input['id']);
            $builder->update();
        } else {
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->insert();
        }
    }

    function deleteCoach($id)
    {
        $teamId = session()->get("team.id");
        if (!$teamId) {
            return false;
        }

        // Use the improved removeCoachFromClub method
        return $this->removeCoachFromClub($id, $teamId);
    }
    function deletePlayer($id)
    {
        $builder = $this->db->table($this->table);
        $builder->set("is_active", 0);
        $builder->set("update_date", date('Y-m-d H:i:s'));
        $builder->where('id', $id);
        $builder->update();
    }

    function get_PlayerGroupByPosition($team_id = '')
    {
        $result = [];
        $positionBuilder = $this->db->table('tbl_player_position');
        $positions = $positionBuilder->select("*")->get()->getResultArray();

        // Get all players with position information for the team
        $builder = $this->db->table($this->table);
        $builder->select("player.*,
                    COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id) as player_position_id,
                    pt_grp.shirt_number as shirt_number,
                    pt_grp.sub_position_ids as player_sub_position_id,
                    coarse_grp.name_th as position_name_th, coarse_grp.name_en as position_name_en")
                ->join('academy_player_enrollments pt_grp', 'pt_grp.player_id = player.id AND pt_grp.is_active = 1', 'INNER')
                ->join('tbl_player_position', 'tbl_player_position.id = pt_grp.position_id', 'LEFT')
                ->join('tbl_player_position coarse_grp', 'coarse_grp.id = COALESCE(NULLIF(tbl_player_position.parent_id,0), tbl_player_position.id)', 'LEFT');

        // Check for parent team (to share player pool)
        $teamInfo = $this->db->table('team')->select('id, parent_team_id')->where('id', $team_id)->get()->getRowArray();

        if ($teamInfo && !empty($teamInfo['parent_team_id'])) {
            // If sub-team, show players from both this team and parent team
            $builder->groupStart()
                ->where("pt_grp.team_id", $team_id)
                ->orWhere("pt_grp.team_id", $teamInfo['parent_team_id'])
                ->groupEnd();
        } else {
            $builder->where("pt_grp.team_id", $team_id);
        }

        $builder->groupBy('player.id')
                ->orderBy('CAST(pt_grp.shirt_number AS UNSIGNED)', 'ASC');
        $allPlayers = $builder->get()->getResultArray();

        // Censor last names for display
        helper('academy');
        foreach ($allPlayers as &$player) {
            if (!empty($player['last_name'])) {
                $player['last_name'] = mask_last_name($player['last_name']);
            }
        }

        // Group players by their primary position
        foreach ($positions as $pos) {
            $result[$pos['id']] = [];
        }

        // Initialize for uncategorized players
        $result[0] = [];

        foreach ($allPlayers as $player) {
            if (!empty($player['player_position_id']) && isset($result[$player['player_position_id']])) {
                $result[$player['player_position_id']][] = $player;
            } else {
                $result[0][] = $player;
            }
        }

        return $result;
    }

    function getCoachById($coachId)
    {
        $builder = $this->db->table("coach");
        $coach = $builder->select('coach.*, users.first_name, users.last_name')
                        ->join('users', 'users.id = coach.coach_user_id', 'left')
                        ->where('coach.id', $coachId)
                        ->get()
                        ->getRowArray();
        return $coach;
    }

    function removeCoachFromClub($coachId, $teamId)
    {
        try {
            $this->db->transBegin();

            // ตรวจสอบว่าโค๊ชอยู่ในทีมนี้จริงหรือไม่ (ผ่าน coach.team_id หรือ coach_invitations)
            $coachBuilder = $this->db->table("coach");
            $coach = $coachBuilder->select('*')
                           ->where('id', $coachId)
                           ->get()
                           ->getRowArray();

            if (!$coach) {
                $this->db->transRollback();
                return false;
            }

            $hasRelationship = false;

            // ตรวจสอบว่ามี team_id ตรงกับทีมนี้หรือไม่
            if ((int)$coach['team_id'] === (int)$teamId) {
                $hasRelationship = true;
                // ลบผู้ฝึกสอนออกจากทีม โดยการ set team_id เป็น 0
                $this->db->table("coach")
                    ->set("team_id", 0)
                    ->set("update_date", date('Y-m-d H:i:s'))
                    ->where('id', $coachId)
                    ->update();
            }

            // ตรวจสอบและ update coach_invitations table ด้วย
            $invitation = $this->db->table("coach_invitations")
                ->where('coach_id', $coachId)
                ->where('team_id', $teamId)
                ->where('status', 'accepted')
                ->get()
                ->getRowArray();

            if ($invitation) {
                $hasRelationship = true;
                // Update invitation status เป็น 'removed'
                $this->db->table("coach_invitations")
                    ->set('status', 'removed')
                    ->set('responded_at', date('Y-m-d H:i:s'))
                    ->where('id', $invitation['id'])
                    ->update();
            }

            // ตรวจสอบและ deactivate coach_teams record ด้วย
            if ($this->db->tableExists('coach_teams')) {
                $coachTeam = $this->db->table("coach_teams")
                    ->where('coach_id', $coachId)
                    ->where('team_id', $teamId)
                    ->where('is_active', 1)
                    ->get()
                    ->getRowArray();

                if ($coachTeam) {
                    $hasRelationship = true;
                    $this->db->table("coach_teams")
                        ->set('is_active', 0)
                        ->where('coach_id', $coachId)
                        ->where('team_id', $teamId)
                        ->update();
                }
            }

            if (!$hasRelationship) {
                $this->db->transRollback();
                return false;
            }

            $this->db->transCommit();
            return true;

        } catch (\Exception $e) {
            $this->db->transRollback();
            log_message('error', 'Error removing coach from club: ' . $e->getMessage());
            return false;
        }
    }

    function searchCoaches($teamId, $searchName = '')
    {
        try {
            $builder = $this->db->table("coach");
            $builder->select("coach.id, coach.first_name, coach.last_name, coach.nick_name, coach.birth_date, coach.team_id, coach_invitations.id as invitation_id, coach_invitations.status as invitation_status", false);

            $builder->join("coach_invitations", "coach_invitations.coach_id = coach.id AND coach_invitations.team_id = {$teamId} AND coach_invitations.status = 'pending'", "LEFT");

            // ค้นหาเฉพาะผู้ฝึกสอนที่ active
            $builder->where("coach.is_active", 1);

            // ค้นหาจากชื่อ, นามสกุล หรือชื่อเล่น
            if (!empty($searchName)) {
                $builder->groupStart();
                $builder->like('coach.first_name', $searchName);
                $builder->orLike('coach.last_name', $searchName);
                $builder->orLike('coach.nick_name', $searchName);
                $builder->groupEnd();
            }

            $coaches = $builder->get()->getResultArray();

            log_message('debug', 'Search coaches result count: ' . count($coaches));

            // จัดรูปแบบข้อมูล
            $result = [];
            helper('academy');
            foreach ($coaches as $coach) {
                // ตรวจสอบสถานะ
                $isMember = ($coach['team_id'] == $teamId);
                $isInvited = !empty($coach['invitation_id']) && $coach['invitation_status'] == 'pending';

                $formattedCoach = [
                    'id' => $coach['id'],
                    'first_name' => $coach['first_name'],
                    'last_name' => mask_last_name($coach['last_name']),
                    'full_name' => $coach['first_name'] . ' ' . mask_last_name($coach['last_name']),
                    'nick_name' => $coach['nick_name'] ?? '-',
                    'birth_date' => $this->formatThaiDate($coach['birth_date']),
                    'is_member' => $isMember,
                    'is_invited' => $isInvited
                ];
                $result[] = $formattedCoach;
            }

            return $result;

        } catch (\Exception $e) {
            log_message('error', 'Error searching coaches: ' . $e->getMessage());
            return [];
        }
    }

    private function formatThaiDate($date)
    {
        if (empty($date)) {
            return '-';
        }

        $thai_months = [
            1 => 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.',
            'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'
        ];

        $timestamp = strtotime($date);
        $day = date('d', $timestamp);
        $month = (int)date('m', $timestamp);
        $year = (int)date('Y', $timestamp) + 543;

        return "$day {$thai_months[$month]} $year";
    }
}