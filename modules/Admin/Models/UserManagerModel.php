<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class UserManagerModel extends Model
{
    protected $table      = "user";
    protected $primaryKey = "id";

    /** จำนวน academy ที่โชว์ในตาราง ที่เหลือย้ายไปดูใน popup */
    private const ACADEMY_PREVIEW_LIMIT = 2;

    /**
     * Get users for DataTable or single user by ID
     */
    public function getUsers($id = '')
    {
        if ($id) {
            return $this->db->table('user')
                ->select('*')
                ->where('id', $id)
                ->get()->getRowArray();
        }

        $builder = $this->db->table('user');
        // academy ที่ user ดูแล + บทบาท — รูปแบบ "ชื่อ academy~role" คั่นด้วย ||
        // ใช้ subquery ไม่ใช่ join เพราะ join จะทำให้แถวซ้ำแล้วยอดใน DataTable เพี้ยน
        $builder->select('user.*,
            (SELECT COUNT(DISTINCT tm.team_id) FROM team_managers tm
              WHERE tm.user_id = user.id AND tm.status = "approved") as team_count,
            (SELECT GROUP_CONCAT(CONCAT(am.role, "~", a.name) ORDER BY a.name SEPARATOR "||")
               FROM academy_members am
               JOIN academy a ON a.id = am.academy_id
              WHERE am.user_id = user.id AND am.status = "approved" AND a.is_active = 1) as academy_roles', false);

        // ค้นหาเอง แทนที่จะใช้ setSearchableColumns ของไลบรารี
        // เพราะไลบรารีสร้างแค่ WHERE (col LIKE ...) ใช้กับ subquery ของ academy ไม่ได้
        // และถ้า JOIN academy_members เข้ามาตรง ๆ แถวจะซ้ำตามจำนวน academy ที่ดูแล
        $search = trim((string) (service('request')->getVar('search')['value'] ?? ''));
        if ($search !== '') {
            $like = $this->db->escape('%' . $search . '%');
            $builder->groupStart()
                ->like('user.name', $search)
                ->orLike('user.username', $search)
                ->orLike('user.uid_line', $search)
                // ค้นจากชื่อ academy ที่ผู้ใช้คนนั้นดูแลอยู่ด้วย
                ->orWhere("user.id IN (
                        SELECT am2.user_id FROM academy_members am2
                        JOIN academy a2 ON a2.id = am2.academy_id
                        WHERE am2.status = 'approved' AND a2.is_active = 1
                          AND a2.name LIKE {$like}
                   )", null, false)
                ->groupEnd();
        }

        return DataTable::of($builder)
            ->add('profile', function ($row) {
                if (!empty($row->profile_image)) {
                    $image = filter_var($row->profile_image, FILTER_VALIDATE_URL)
                        ? $row->profile_image
                        : base_url($row->profile_image);
                } else {
                    $image = base_url('public/img/img_profile.jpg');
                }
                return '<img src="' . $image . '" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;" onerror="this.src=\'' . base_url('public/img/img_profile.jpg') . '\'">';
            })
            ->add('display_name', function ($row) {
                $name = esc($row->name ?: '-');
                $username = $row->username ? '<br><small class="text-muted">@' . esc($row->username) . '</small>' : '';
                return $name . $username;
            })
            ->add('login_type', function ($row) {
                $badges = '';
                if (!empty($row->uid_line)) {
                    $badges .= '<span class="badge bg-success-subtle text-success me-1">LINE</span>';
                }
                if (!empty($row->username) && !empty($row->password)) {
                    $badges .= '<span class="badge bg-primary-subtle text-primary">Password</span>';
                }
                return $badges ?: '<span class="badge bg-secondary-subtle text-secondary">-</span>';
            })
            ->add('teams', function ($row) {
                $count = $row->team_count ?? 0;
                if ($count > 0) {
                    return '<span class="badge bg-info-subtle text-info">' . $count . '</span>';
                }
                return '<span class="text-muted">0</span>';
            })
            ->add('academies', function ($row) {
                if (empty($row->academy_roles)) {
                    return '<span class="text-muted">-</span>';
                }

                // ค่าจาก $row ถูก esc() มาแล้วโดยไลบรารี (DataTableQuery::getDataResult)
                // ห้าม esc ซ้ำ ไม่งั้นชื่อที่มีเครื่องหมาย ' จะโผล่เป็น &#039; บนหน้าจอ
                $roleClass = [
                    'owner'  => 'bg-danger-subtle text-danger',
                    'admin'  => 'bg-primary-subtle text-primary',
                    'coach'  => 'bg-info-subtle text-info',
                    'viewer' => 'bg-secondary-subtle text-secondary',
                ];

                // role มาก่อนชื่อ เพื่อให้ชื่อที่มี ~ ปนอยู่ไม่ถูกตัด (explode limit 2)
                $items = [];
                foreach (explode('||', $row->academy_roles) as $pair) {
                    [$role, $academyName] = array_pad(explode('~', $pair, 2), 2, '');
                    $academyName = trim($academyName);
                    if ($academyName === '') continue;

                    $role  = trim($role) ?: 'viewer';
                    $label = lang('Common.role_' . $role);
                    // คีย์ที่ไม่มีจริง lang() จะคืนชื่อคีย์กลับมา ใช้ค่าดิบแทน
                    if ($label === 'Common.role_' . $role) $label = $role;

                    $items[] = [
                        'label' => $label,
                        'name'  => $academyName,
                        'cls'   => $roleClass[$role] ?? 'bg-secondary-subtle text-secondary',
                    ];
                }

                if (empty($items)) {
                    return '<span class="text-muted">-</span>';
                }

                $render = static function (array $it): string {
                    return '<div class="mb-1 text-start">'
                         . '<span class="badge ' . $it['cls'] . ' me-1">' . $it['label'] . '</span>'
                         . '<span class="small">' . $it['name'] . '</span>'
                         . '</div>';
                };

                $total = count($items);
                if ($total <= self::ACADEMY_PREVIEW_LIMIT) {
                    return implode('', array_map($render, $items));
                }

                // เกินกำหนด → โชว์ไม่กี่รายการ ที่เหลือส่งเป็น JSON ให้ popup ไป render + ค้นหาเอง
                // ชื่อถูก esc มาแล้วจากไลบรารี จึง decode กลับก่อนใส่ JSON
                // ไม่งั้นค้นหาคำที่มีเครื่องหมาย ' จะไม่เจอ (ฝั่ง JS escape ตอน render อยู่แล้ว)
                $payload = array_map(static function (array $it): array {
                    return [
                        'label' => html_entity_decode($it['label'], ENT_QUOTES, 'UTF-8'),
                        'name'  => html_entity_decode($it['name'], ENT_QUOTES, 'UTF-8'),
                        'cls'   => $it['cls'],
                    ];
                }, $items);

                $more    = $total - self::ACADEMY_PREVIEW_LIMIT;
                $json    = htmlspecialchars(json_encode($payload, JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
                $langAll = lang('Common.um_academies_more');
                $title   = htmlspecialchars(lang('Common.um_academies_popup_title'), ENT_QUOTES, 'UTF-8');

                return implode('', array_map($render, array_slice($items, 0, self::ACADEMY_PREVIEW_LIMIT)))
                     . '<button type="button" class="btn btn-link btn-sm p-0 text-decoration-none"'
                     . ' data-list-popup="' . $json . '"'
                     . ' data-list-total="' . $total . '"'
                     . ' data-list-title="' . $title . '">'
                     . '<i class="ti ti-list-details me-1"></i>' . $langAll . ' (+' . $more . ')'
                     . '</button>';
            })
            ->add('registered_date', function ($row) {
                return $row->create_date ? date('d/m/Y', strtotime($row->create_date)) : '-';
            })
            ->add('action', function ($row) {
                $langDelete = lang('Common.delete_btn');
                $langAccess = lang('Common.ag_manage_academy_access');
                $impersonateUrl = base_url('admin/users/impersonate/user/' . $row->id);
                $impersonateBtn = '';
                if (session()->get('is_super_admin')) {
                    $impersonateBtn = <<<HTML
                    <a class="btn btn-primary btn-sm px-2" href="{$impersonateUrl}" title="หน้าบ้าน">
                        <i class="ti ti-login-2"></i>
                    </a>
                    HTML;
                }
                // ชื่อผ่าน htmlspecialchars แล้ว escape เครื่องหมายคำพูดอีกชั้น กัน onclick พัง
                $safeName = str_replace("'", "\\'", esc($row->name ?: '-', 'js'));
                // ไอคอนอย่างเดียว กันคอลัมน์ action ล้นตกบรรทัด (ชื่อเต็มอยู่ใน title)
                $accessBtn = <<<HTML
                <button class="btn btn-info btn-sm text-white px-2" title="{$langAccess}"
                        onclick="openAcademyAccess({$row->id}, '{$safeName}')">
                    <i class="ti ti-school"></i>
                </button>
                HTML;
                // flex-nowrap กันปุ่มตกบรรทัด (ตารางอยู่ใน .table-responsive จึงเลื่อนแนวนอนแทน)
                return <<<HTML
                    <div class="d-inline-flex flex-nowrap gap-1 justify-content-center">
                        {$impersonateBtn}
                        {$accessBtn}
                        <button class="btn btn-danger btn-sm px-2" onclick="deleteUser({$row->id})" title="{$langDelete}">
                            <i class="ti ti-trash"></i>
                        </button>
                    </div>
                HTML;
            })
            ->addNumbering()
            // ปล่อยว่างไว้ตั้งใจ — กรองเองไปแล้วด้านบน ถ้าใส่ที่นี่ด้วยจะกลายเป็น AND กันสองชั้น
            ->setSearchableColumns([])
            ->toJson(true);
    }

    /**
     * Save user (insert or update)
     */
    public function saveUser($input)
    {
        $builder = $this->db->table('user');

        $builder->set('name', $input['name'] ?? '');

        if (!empty($input['username'])) {
            $builder->set('username', $input['username']);
        }

        if (!empty($input['password'])) {
            $builder->set('password', password_hash($input['password'], PASSWORD_DEFAULT));
        }

        if (!empty($input['id'])) {
            $builder->where('id', $input['id']);
            $builder->update();
            return $input['id'];
        } else {
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->insert();
            return $this->db->insertID();
        }
    }

    /**
     * Delete user and all related data
     */
    public function deleteUser($id)
    {
        try {
            $this->db->transStart();

            // ลบข้อมูลที่เกี่ยวข้อง โดยตรวจสอบทั้ง table และ column ก่อนทุกครั้ง
            $deleteTargets = [
                ['table' => 'password_reset_tokens', 'column' => 'user_id'],
                ['table' => 'logs_login',            'column' => 'user_id'],
                ['table' => 'team_managers',         'column' => 'user_id'],
                ['table' => 'academy_members',       'column' => 'user_id'],
            ];

            foreach ($deleteTargets as $target) {
                if ($this->db->tableExists($target['table']) && $this->db->fieldExists($target['column'], $target['table'])) {
                    $this->db->table($target['table'])->where($target['column'], $id)->delete();
                }
            }

            // Nullify create_by references (audit trail) - เฉพาะคอลัมน์ที่เป็น INT เท่านั้น
            $nullifyTargets = [
                ['table' => 'team',                    'column' => 'create_by'],
                ['table' => 'academy',                 'column' => 'create_by'],
                ['table' => 'tournament_team',         'column' => 'create_by'],
                ['table' => 'tournament_match_result', 'column' => 'create_by'],
                ['table' => 'idp_progress',            'column' => 'recorded_by'],
            ];

            foreach ($nullifyTargets as $target) {
                if ($this->db->tableExists($target['table']) && $this->db->fieldExists($target['column'], $target['table'])) {
                    // ตรวจสอบว่าคอลัมน์เป็น INT ก่อน update
                    $fieldData = $this->db->getFieldData($target['table']);
                    foreach ($fieldData as $field) {
                        if ($field->name === $target['column'] && stripos($field->type, 'int') !== false) {
                            $this->db->table($target['table'])->where($target['column'], $id)->update([$target['column'] => null]);
                            break;
                        }
                    }
                }
            }

            // Delete user
            $this->db->table('user')->where('id', $id)->delete();

            $this->db->transComplete();

            return $this->db->transStatus();
        } catch (\Exception $e) {
            log_message('error', 'Delete user error: ' . $e->getMessage());
            return false;
        }
    }

    // ==================== MEMBER (Admin) ====================

    /**
     * Get members for DataTable or single member by ID
     */
    public function getMembers($id = '')
    {
        if ($id) {
            return $this->db->table('member')
                ->select('member.*, GROUP_CONCAT(roles.display_name SEPARATOR ", ") as role_names')
                ->join('user_roles', 'user_roles.member_id = member.id', 'left')
                ->join('roles', 'roles.id = user_roles.role_id', 'left')
                ->where('member.id', $id)
                ->groupBy('member.id')
                ->get()->getRowArray();
        }

        $builder = $this->db->table('member');
        $builder->select('member.*, GROUP_CONCAT(roles.display_name SEPARATOR ", ") as role_names')
            ->join('user_roles', 'user_roles.member_id = member.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id', 'left')
            ->groupBy('member.id');

        return DataTable::of($builder)
            ->add('display_username', function ($row) {
                return '<strong>' . esc($row->username) . '</strong>';
            })
            ->add('roles', function ($row) {
                if (empty($row->role_names)) {
                    return '<span class="text-muted">-</span>';
                }
                $roles = explode(', ', $row->role_names);
                $badges = '';
                foreach ($roles as $role) {
                    $badges .= '<span class="badge bg-primary-subtle text-primary me-1">' . esc($role) . '</span>';
                }
                return $badges;
            })
            ->add('status', function ($row) {
                $langActive = lang('Common.active');
                $langInactive = lang('Common.inactive');
                if ($row->access_login == 1) {
                    return '<span class="badge bg-success-subtle text-success">' . $langActive . '</span>';
                }
                return '<span class="badge bg-danger-subtle text-danger">' . $langInactive . '</span>';
            })
            ->add('action', function ($row) {
                $langEdit = lang('Common.edit_btn');
                $langDelete = lang('Common.delete_btn');
                return <<<HTML
                    <button class="btn btn-warning btn-sm text-dark" onclick="editMember({$row->id})">
                        <i class="ti ti-pencil"></i> {$langEdit}
                    </button>
                    <button class="btn btn-danger btn-sm" onclick="deleteMember({$row->id})">
                        <i class="ti ti-trash"></i> {$langDelete}
                    </button>
                HTML;
            })
            ->add('member_id', function ($row) {
                return $row->id;
            })
            ->addNumbering()
            ->setSearchableColumns(['username'])
            ->toJson(true);
    }

    /**
     * Save member (insert or update)
     */
    public function saveMember($input)
    {
        $builder = $this->db->table('member');

        $builder->set('username', $input['username']);

        if (!empty($input['password'])) {
            $builder->set('password', password_hash($input['password'], PASSWORD_DEFAULT));
        }

        $builder->set('access_login', isset($input['access_login']) ? 1 : 0);

        if (!empty($input['id'])) {
            $builder->where('id', $input['id']);
            $builder->update();
            return $input['id'];
        } else {
            if (empty($input['password'])) {
                return false;
            }
            $builder->insert();
            return $this->db->insertID();
        }
    }

    /**
     * Delete member
     */
    public function deleteMember($id)
    {
        $this->db->transStart();

        // Remove role assignments
        $this->db->table('user_roles')->where('member_id', $id)->delete();

        // Remove remember tokens
        if ($this->db->tableExists('admin_remember_tokens')) {
            $this->db->table('admin_remember_tokens')->where('member_id', $id)->delete();
        }

        // Delete member
        $this->db->table('member')->where('id', $id)->delete();

        $this->db->transComplete();

        return $this->db->transStatus();
    }

    /**
     * Check if username exists in user table
     */
    public function userUsernameExists($username, $excludeId = null)
    {
        $builder = $this->db->table('user')->where('username', $username);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }

    /**
     * Check if username exists in member table
     */
    public function memberUsernameExists($username, $excludeId = null)
    {
        $builder = $this->db->table('member')->where('username', $username);
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        return $builder->countAllResults() > 0;
    }
}
