<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class CoachModel extends Model
{
    /** จำนวนทีมที่โชว์ในตาราง ที่เหลือย้ายไปดูใน popup */
    private const TEAM_PREVIEW_LIMIT = 2;

    protected $table      = "coach";
    protected $primaryKey = "";
    protected $allowedFields = [];

    function get_Coach($id = '')
    {
        if ($id) {
            $builder = $this->db->table($this->table);
            $builder->select($this->table . ".*,
                                tbl_provinces.province_name as province_name,
                                tbl_districts.district_name as amphure_name,
                                tbl_subdistricts.subdistrict_name as tambon_name")
                    ->join("tbl_provinces", "coach.province_id = tbl_provinces.province_id", "LEFT")
                    ->join("tbl_districts", "coach.amphure_id = tbl_districts.district_id", "LEFT")
                    ->join("tbl_subdistricts", "coach.tambon_id = tbl_subdistricts.subdistrict_id", "LEFT")
                    ->where("id", $id);
            $result = $builder->get()->getRowArray();
            return $result;
        } else {
            $builder = $this->db->table($this->table);
            // ทีมทั้งหมดที่โค้ชคุมจาก coach_teams — coach.team_id เป็นแค่ "ทีมหลัก" ทีมเดียว
            // ใช้ subquery ไม่ใช่ join เพราะ join จะทำให้แถวซ้ำแล้วยอดใน DataTable เพี้ยน
            $builder->select("coach.*,CONCAT(first_name, ' ', last_name) AS fullname,
                                team.name as team_name,
                                team.name_en as team_name_en,
                                (SELECT GROUP_CONCAT(DISTINCT t2.name ORDER BY t2.name SEPARATOR '||')
                                   FROM coach_teams ct2
                                   JOIN team t2 ON t2.id = ct2.team_id
                                  WHERE ct2.coach_id = coach.id AND ct2.is_active = 1) as coach_team_names,
                                tbl_provinces.province_name as province_name,
                                tbl_districts.district_name as amphure_name ,
                                tbl_subdistricts.subdistrict_name as tambon_name
                                ", false);
            $builder->join("team", "coach.team_id = team.id", "LEFT");
            $builder->join("tbl_provinces", "coach.province_id = tbl_provinces.province_id", "LEFT");
            $builder->join("tbl_districts", "coach.amphure_id = tbl_districts.district_id", "LEFT");
            $builder->join("tbl_subdistricts", "coach.tambon_id = tbl_subdistricts.subdistrict_id", "LEFT");
            $builder->where("coach.is_active", 1);

            // ค้นหาเอง แทน setSearchableColumns ของไลบรารี
            // เพราะไลบรารีสร้างแค่ WHERE (col LIKE ...) ใช้กับ subquery ของทีมไม่ได้
            // และถ้า JOIN coach_teams เข้ามาตรง ๆ แถวจะซ้ำตามจำนวนทีมที่คุม
            $search = trim((string) (service('request')->getVar('search')['value'] ?? ''));
            if ($search !== '') {
                $like = $this->db->escape('%' . $search . '%');
                $builder->groupStart()
                    ->like('coach.first_name', $search)
                    ->orLike('coach.last_name', $search)
                    ->orLike('coach.nick_name', $search)
                    // ค้นจากชื่อทีมที่โค้ชคุมอยู่ (coach_teams) และทีมหลักใน coach.team_id
                    ->orWhere("coach.id IN (
                            SELECT ct2.coach_id FROM coach_teams ct2
                            JOIN team t2 ON t2.id = ct2.team_id
                            WHERE ct2.is_active = 1 AND t2.name LIKE {$like}
                       )", null, false)
                    ->orWhere("coach.team_id IN (
                            SELECT t3.id FROM team t3 WHERE t3.name LIKE {$like}
                       )", null, false)
                    ->groupEnd();
            }


            return DataTable::of($builder)
                ->edit('fullname', function ($row) {
                    return $row->first_name . ' ' . $row->last_name;
                })
                ->edit('team_name', function ($row) {
                    // ค่าจาก $row ถูก esc() มาแล้วโดยไลบรารี (DataTableQuery::getDataResult) ห้าม esc ซ้ำ
                    $names = !empty($row->coach_team_names)
                        ? explode('||', $row->coach_team_names)
                        : (!empty($row->team_name) ? [$row->team_name] : []);

                    $names = array_values(array_filter(array_map('trim', $names), static fn($n) => $n !== ''));
                    if (empty($names)) {
                        return '<span class="text-muted">-</span>';
                    }

                    $render = static fn(string $n): string => '<div class="mb-1 text-start small">' . $n . '</div>';

                    $total = count($names);
                    if ($total <= self::TEAM_PREVIEW_LIMIT) {
                        return implode('', array_map($render, $names));
                    }

                    // เกินกำหนด → โชว์ไม่กี่ทีม ที่เหลือส่งเป็น JSON ให้ popup ไป render + ค้นหาเอง
                    // decode กลับก่อนใส่ JSON เพื่อให้ค้นหาชื่อที่มีอักขระพิเศษได้ (JS escape ตอน render)
                    $payload = array_map(static function (string $n): array {
                        return ['name' => html_entity_decode($n, ENT_QUOTES, 'UTF-8')];
                    }, $names);

                    $more    = $total - self::TEAM_PREVIEW_LIMIT;
                    $json    = htmlspecialchars(json_encode($payload, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                    $title   = htmlspecialchars(lang('Common.um_teams_popup_title'), ENT_QUOTES, 'UTF-8');
                    $langAll = lang('Common.um_academies_more');

                    return implode('', array_map($render, array_slice($names, 0, self::TEAM_PREVIEW_LIMIT)))
                         . '<button type="button" class="btn btn-link btn-sm p-0 text-decoration-none"'
                         . ' data-list-popup="' . $json . '"'
                         . ' data-list-total="' . $total . '"'
                         . ' data-list-title="' . $title . '">'
                         . '<i class="ti ti-list-details me-1"></i>' . $langAll . ' (+' . $more . ')'
                         . '</button>';
                })
                ->add('profile', function ($row) {
                    $defaultImg = base_url('public/img/img_profile.jpg');
                    if (!empty($row->image_path)) {
                        $image = filter_var($row->image_path, FILTER_VALIDATE_URL)
                            ? $row->image_path
                            : base_url($row->image_path);
                    } else {
                        $image = $defaultImg;
                    }
                    return '<img src="' . $image . '" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" onerror="this.src=\'' . $defaultImg . '\'">';
                })
                ->add('status_badge', function ($row) {
                    $langActive = lang('Common.active');
                    $langInactive = lang('Common.inactive');
                    if ($row->is_active == 1) {
                        return '<span class="badge bg-success-subtle text-success">' . $langActive . '</span>';
                    }
                    return '<span class="badge bg-danger-subtle text-danger">' . $langInactive . '</span>';
                })
                ->add('action', function ($row) {
                    $langEdit = lang('Common.edit_btn');
                    $langDelete = lang('Common.delete_btn');
                    $langAccess = lang('Common.ag_manage_team_access');
                    $link = base_url('admin/coach/coach-manage/' . $row->id);
                    $impersonateUrl = base_url('admin/coach/impersonate/' . $row->id);
                    $impersonateBtn = '';
                    if (session()->get('is_super_admin')) {
                        $impersonateBtn = <<<HTML
                        <a class="btn btn-primary btn-sm px-2" href="{$impersonateUrl}" title="หน้าบ้าน">
                            <i class="ti ti-login-2"></i>
                        </a>
                        HTML;
                    }
                    // escape เครื่องหมายคำพูดอีกชั้น กัน onclick พัง
                    $safeName = str_replace("'", "\\'", esc(trim($row->first_name . ' ' . $row->last_name) ?: '-', 'js'));
                    // flex-nowrap กันปุ่มตกบรรทัด (ตารางอยู่ใน .table-responsive จึงเลื่อนแนวนอนแทน)
                    return <<<HTML
                        <div class="d-inline-flex flex-nowrap gap-1 justify-content-center">
                            {$impersonateBtn}
                            <button class="btn btn-info btn-sm text-white px-2" title="{$langAccess}"
                                    onclick="openTeamAccess({$row->id}, '{$safeName}')">
                                <i class="ti ti-shield-check"></i>
                            </button>
                            <a class="btn btn-warning btn-sm text-dark px-2" href="{$link}" title="{$langEdit}">
                                <i class="ti ti-pencil"></i>
                            </a>
                            <button onclick="deleteCoach({$row->id})" class="btn btn-danger btn-sm px-2" title="{$langDelete}">
                                <i class="ti ti-trash"></i>
                            </button>
                        </div>
                    HTML;
                })
                // ปล่อยว่างไว้ตั้งใจ — กรองเองไปแล้วด้านบน ถ้าใส่ที่นี่ด้วยจะกลายเป็น AND กันสองชั้น
                ->setSearchableColumns([])
                ->addNumbering()
                ->toJson(true);
            //

        }
    }

    function get_CoachList()
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table.".*,team.name as team_name,team.name_en as team_name_en");
        $builder->join("team", "team.team_id = team.team_id", "LEFT");
        $builder->where("is_active", 1);
        //$builder->orderBy("player_position_id ASC, CAST(shirt_number AS unsigned) ASC");
        $result = $builder->get()->getResultArray();

        // Censor last names for display
        /*
        helper('academy');
        foreach ($result as &$coach) {
            if (!empty($coach['last_name'])) {
                $coach['last_name'] = mask_last_name($coach['last_name']);
            }
        }
        */

        return $result;
    }

    function saveCoach($input)
    {
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
        if (!empty($input['birth_date'])) {
            $formatDate = str_replace('/', '-', $input['birth_date']);
            $builder->set('birth_date', date('Y-m-d', strtotime(date($formatDate))));
        }

        if (!empty($input['team_id'])) {
            $builder->set('team_id', $input['team_id']);
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

        if (isset($input['position'])) {
            $builder->set('position', $input['position']);
        }

        if (isset($input['id_card_image'])) {
            $builder->set('id_card_image', $input['id_card_image']);
        }

        if (!empty($input['id'])) {
            //$builder->set('update_by', session()->get('member_id'));
            $builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->where('id', $input['id']);
            $builder->update();
        } else {
            //$builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->set('create_by', session()->get('member_id'));
            //$builder->set('update_date', date('Y-m-d H:i:s'));
            $builder->insert();
        }
    }

    function getOneCoach($id = '')
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . '.*');
        if ($id) {
            $builder->where('id', $id);
            $result = $builder->get()->getRowArray();
        }else {
            $result = $builder->get()->getResultArray();
        }

        return $result;
    }
    function deleteCoach($id)
    {
        // deleted subtable
        $builder = $this->db->table('coach_certs');
        $builder->where('coach_id', $id)->delete();

        // deleted main
        $builder = $this->db->table($this->table);
        $builder->where('id', $id)->delete();
        return "success";
    }


    function get_Certs($coach_id = '')
    {
        $result = [];
        $table = "coach_certs";
        $builder = $this->db->table($table);
        $builder->select($table . ".*");
        $builder->where("coach_id", $coach_id);
        $result = $builder->get()->getResultArray();
        //die(print_r($result));
        return $result;
    }

    /*
    function get_CertsTable($id = '')
    {
        $table = "coach_certs";
        $builder = $this->db->table($table);
        $builder->select("{$table}.tour_team_id,{$table}.id,{$table}.team_id, {$table}.register_status, team.name, team.logo");
        $builder->join("team", "{$table}.team_id = team.id", "LEFT");
        //$builder->where("is_active", 1);
        $builder->where("id", $id);

        return DataTable::of($builder)
            ->edit('logo', function ($row, $meta) {
                return '<img class="" style="width:50px" src="' . base_url($row->logo) . '">';
            })
            ->edit('name', function ($row, $meta) {
                $link = base_url() . '/detail/team/' . $row->id;
                $name = '<a href="'.$link.'" target="_blank">'.$row->name.'</a>';
                return $name;
            })
            ->add('register_badge', function ($row) {
                if ($row->register_status == 0) {
                    $text = '<span class="badge text-bg-warning rounded-pill">รออนุมัติ</span>';
                } else if ($row->register_status == 1) {
                    $text = '<span class="badge text-bg-success rounded-pill">อนุมัติ</span>';
                } else if ($row->register_status == 2) {
                    $text = '<span class="badge text-bg-danger rounded-pill">ไม่อนุมัติ</span>';
                }
                return $text;
            })
            ->add('register_tools', function ($row) {
                $tools = '';
                $changeFunction = "onchange='changeRegisterStatus($row->tour_team_id, this.value)'";
                if ($row->register_status == 0) {
                    $tools = "<select name='register_status' class='form-select bg-white' $changeFunction>
                        <option value='0' selected>รออนุมัติ</option>
                        <option value='1'>อนุมัติ</option>
                        <option value='2'>ไม่อนุมัติ</option>
                    </select>";
                } else if ($row->register_status == 1) {
                    $tools = "<select name='register_status' class='form-select bg-white' $changeFunction>
                        <option value='0'>รออนุมัติ</option>
                        <option value='1'selected>อนุมัติ</option>
                        <option value='2'>ไม่อนุมัติ</option>
                    </select>";
                } else if ($row->register_status == 2) {
                    $tools = "<select name='register_status' class='form-select bg-white' $changeFunction>
                        <option value='0'>รออนุมัติ</option>
                        <option value='1'>อนุมัติ</option>
                        <option value='2' selected>ไม่อนุมัติ</option>
                    </select>";
                }

                return $tools;
            })
            ->add('action', function ($row) {
                $manageIcon = '';
                $manageIcon .= "<button onclick='deleteCoachTeam(" . $row->tour_team_id . ")' class='btn btn-danger btn-sm mx-1'><i class='ti ti-trash text-white' style='font-size: 16px;'></i> ลบรายการ</button>";
                return $manageIcon;
            })
            ->setSearchableColumns(['name'])
            ->addNumbering()
            ->toJson(true);
        //
    }
    */

    function delete_certs($id)
    {
        $builder = $this->db->table('coach_certs');
        $builder->where('id', $id)->delete();
        return "success";
    }

    /*
    function getOnceCerts($tour_team_id = '')
    {
        $table = "certs";
        $builder = $this->db->table($table);
        $builder->select($table . '.*');
        $result = [];
        if ($tour_team_id) {
            $builder->where('tour_team_id', $tour_team_id);
        }
        $result = $builder->get()->getRowArray();
        return $result;
    }
    */

    function save_certs($input, $certImages = [])
    {
        $builder = $this->db->table("coach_certs");
        $coach_id = $input['coach_id'];

        // clear
        $builder = $this->db->table('coach_certs');
        $builder->where('coach_id', $coach_id)->delete();

        foreach ($input['id'] as $index => $value) {
            $builder->set('coach_id', $coach_id);
            $builder->set('organ_id', $input['organ_id'][$index]);
            $builder->set('cert_level_id', $input['cert_level_id'][$index]);
            $builder->set('license_code', $input['license_code'][$index]);
            $builder->set('year', $input['year'][$index]);

            // cert_image
            if (!empty($certImages[$index])) {
                $builder->set('cert_image', $certImages[$index]);
            } elseif (!empty($input['existing_cert_image'][$index])) {
                $builder->set('cert_image', $input['existing_cert_image'][$index]);
            }

            if (!empty(session()->get('member_id'))) {
                $builder->set('create_by', session()->get('member_id'));
            } else {
                $builder->set('create_by', 0);
            }
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->insert();
        }
        return "success";
    }

    function get_certLevel() {
        $table = "tbl_certs_level";
        $builder = $this->db->table($table);
        $builder->select($table.".*");
        //$builder->orderBy("id ASC");
        $result = $builder->get()->getResultArray();
        return $result;
    }

    function get_Organ() {
        $table = "tbl_fifa_organizations";
        $builder = $this->db->table($table);
        $builder->select($table.".*");
        //$builder->orderBy("order","ASC");
        $result = $builder->get()->getResultArray();
        return $result;
    }

    function get_Teams()
    {
        $result = [];
        $table = "team";
        $builder = $this->db->table($table);
        $builder->select($table . ".*");
        $builder->where('is_active', 1);
        $result = $builder->get()->getResultArray();
        return $result;
    }

    public function getCoachByUserId($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*,
                            tbl_provinces.province_name,
                            tbl_districts.district_name as amphure_name,
                            tbl_subdistricts.subdistrict_name as tambon_name")
                ->join("tbl_provinces", "coach.province_id = tbl_provinces.province_id", "LEFT")
                ->join("tbl_districts", "coach.amphure_id = tbl_districts.district_id", "LEFT")
                ->join("tbl_subdistricts", "coach.tambon_id = tbl_subdistricts.subdistrict_id", "LEFT")
                ->where("coach.create_by", $userId)
                ->where("coach.is_active", 1);

        return $builder->get()->getRowArray();
    }

    public function getCoachByCoachUserId($coachUserId)
    {
        $builder = $this->db->table($this->table);
        $builder->select($this->table . ".*,
                            tbl_provinces.province_name,
                            tbl_districts.district_name as amphure_name,
                            tbl_subdistricts.subdistrict_name as tambon_name")
                ->join("tbl_provinces", "coach.province_id = tbl_provinces.province_id", "LEFT")
                ->join("tbl_districts", "coach.amphure_id = tbl_districts.district_id", "LEFT")
                ->join("tbl_subdistricts", "coach.tambon_id = tbl_subdistricts.subdistrict_id", "LEFT")
                ->where("coach.coach_user_id", $coachUserId)
                ->where("coach.is_active", 1);

        return $builder->get()->getRowArray();
    }
}
