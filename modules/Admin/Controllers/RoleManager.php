<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\RoleModel;
use Modules\Admin\Models\PermissionModel;
use Modules\Admin\Models\UserRoleModel;
use Modules\Admin\Models\MemberModel;

class RoleManager extends BaseController
{
    protected $roleModel;
    protected $permissionModel;
    protected $userRoleModel;
    protected $memberModel;
    protected $db;

    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->userRoleModel = new UserRoleModel();
        $this->memberModel = new MemberModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * Role management index page
     */
    public function index()
    {
        $data = [
            'title' => 'จัดการบทบาทผู้ใช้งาน',
            'roles' => $this->roleModel->getRolesWithPermissionCounts(),
        ];

        return view('Modules\Admin\Views\Role\index', $data);
    }

    /**
     * Role management form
     */
    public function manage($roleId = null)
    {
        $data = [
            'title' => $roleId ? 'แก้ไขบทบาท' : 'เพิ่มบทบาทใหม่',
            'role' => $roleId ? $this->roleModel->find($roleId) : null,
            'permissions' => $this->permissionModel->getPermissionsByModule(),
            'rolePermissions' => $roleId ? array_column($this->roleModel->getRolePermissions($roleId), 'id') : [],
        ];

        return view('Modules\Admin\Views\Role\manage', $data);
    }

    /**
     * Save role
     */
    public function save()
    {
        $input = $this->request->getPost();

        $rules = [
            'name' => 'required|min_length[3]|max_length[100]',
            'display_name' => 'required|min_length[3]|max_length[255]',
            'description' => 'permit_empty|max_length[500]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $roleData = [
            'name' => strtolower(str_replace(' ', '_', $input['name'])),
            'display_name' => $input['display_name'],
            'description' => $input['description'] ?? '',
            'status' => $input['status'] ?? 1,
        ];

        try {
            if (!empty($input['id'])) {
                // Update role
                $this->roleModel->update($input['id'], $roleData);
                $roleId = $input['id'];
                $message = 'บันทึกข้อมูลบทบาทสำเร็จ';
            } else {
                // Create new role
                $roleId = $this->roleModel->insert($roleData);
                $message = 'สร้างบทบาทใหม่สำเร็จ';
            }

            // Assign permissions
            if (!empty($input['permissions'])) {
                $this->roleModel->assignPermissions($roleId, $input['permissions']);
            } else {
                $this->roleModel->assignPermissions($roleId, []);
            }

            return redirect()->to(base_url('admin/role'))->with('success', $message);
        } catch (\Exception $e) {
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    /**
     * Delete role
     */
    public function delete()
    {
        $input = $this->request->getPost();

        if (empty($input['id'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'ไม่พบข้อมูลที่ต้องการลบ']);
        }

        try {
            // Check if role is being used
            $usersWithRole = $this->userRoleModel->where('role_id', $input['id'])->countAllResults();

            if ($usersWithRole > 0) {
                return $this->response->setJSON(['status' => false, 'message' => 'ไม่สามารถลบบทบาทนี้ได้ เนื่องจากมีผู้ใช้งานใช้บทบาทนี้อยู่']);
            }

            // Check if it's a system role
            $role = $this->roleModel->find($input['id']);
            if (in_array($role['name'], ['super_admin', 'admin'])) {
                return $this->response->setJSON(['status' => false, 'message' => 'ไม่สามารถลบบทบาทระบบได้']);
            }

            $this->roleModel->delete($input['id']);

            return $this->response->setJSON(['status' => true, 'message' => 'ลบบทบาทสำเร็จ']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * User role management
     */
    public function userRoles()
    {
        $data = [
            'title' => 'จัดการสิทธิ์และสมาชิกผู้ใช้งาน',
            'users' => $this->userRoleModel->getUsersWithRoles(),
        ];

        return view('Modules\Admin\Views\Role\user_roles', $data);
    }

    /**
     * Assign roles to user
     */
    public function assignUserRoles($memberId = null)
    {
        if (!$memberId) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลผู้ใช้งาน');
        }

        $user = $this->db->table('member')->where('id', $memberId)->get()->getRowArray();

        if (!$user) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลผู้ใช้งาน');
        }

        $data = [
            'title' => 'กำหนดบทบาทผู้ใช้งาน: ' . $user['username'],
            'user' => $user,
            'roles' => $this->roleModel->getActiveRoles(),
            'userRoles' => array_column($this->userRoleModel->getUserRoles($memberId), 'role_id'),
        ];

        return view('Modules\Admin\Views\Role\assign_user_roles', $data);
    }

    /**
     * Save user roles
     */
    public function saveUserRoles()
    {
        $input = $this->request->getPost();

        if (empty($input['member_id'])) {
            return redirect()->back()->with('error', 'ไม่พบข้อมูลผู้ใช้งาน');
        }

        $roleIds = $input['roles'] ?? [];
        $assignedBy = session()->get('member_id');

        $result = $this->userRoleModel->updateUserRoles($input['member_id'], $roleIds, $assignedBy);

        if ($result['status']) {
            return redirect()->to(base_url('admin/role/user-roles'))->with('success', $result['message']);
        } else {
            return redirect()->back()->with('error', $result['message']);
        }
    }

    /**
     * Get role data for AJAX
     */
    public function getRoles()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $roles = $this->roleModel->getRolesWithPermissionCounts();

        return $this->response->setJSON(['data' => $roles]);
    }

    /**
     * Get user data for editing
     */
    public function getUser($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403);
        }

        $user = $this->memberModel->find($id);

        if (!$user) {
            return $this->response->setJSON(['status' => false, 'message' => 'ไม่พบข้อมูลผู้ใช้งาน']);
        }

        // Remove password from response
        unset($user['password']);

        return $this->response->setJSON(['status' => true, 'data' => $user]);
    }

    /**
     * Save user (Create/Update)
     */
    public function saveUser()
    {
        $input = $this->request->getPost();

        $rules = [
            'username' => 'required|min_length[3]|max_length[100]',
        ];

        // Password validation
        if (empty($input['id'])) {
            // New user: Password required
            $rules['password'] = 'required|min_length[4]';
            $rules['confirm_password'] = 'required|matches[password]';
        } else {
            // Edit user: Password optional
            if (!empty($input['password'])) {
                $rules['password'] = 'min_length[4]';
                $rules['confirm_password'] = 'required|matches[password]';
            }
        }

        if (!$this->validate($rules)) {
            return $this->response->setJSON(['status' => false, 'errors' => $this->validator->getErrors()]);
        }

        // Check for duplicate username
        $existingUser = $this->memberModel->where('username', $input['username'])->first();
        if ($existingUser && (empty($input['id']) || $existingUser['id'] != $input['id'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'ชื่อผู้ใช้งานนี้มีอยู่ในระบบแล้ว']);
        }

        $userData = [
            'username' => $input['username'],
            'access_login' => isset($input['access_login']) ? 1 : 0,
        ];

        // Update password if provided
        if (!empty($input['password'])) {
            $userData['password'] = password_hash($input['password'], PASSWORD_DEFAULT);
        }

        try {
            if (!empty($input['id'])) {
                $this->db->table('member')->where('id', $input['id'])->update($userData);
                $message = 'บันทึกข้อมูลสำเร็จ';
            } else {
                $this->db->table('member')->insert($userData);
                $message = 'สร้างผู้ใช้งานใหม่สำเร็จ';
            }

            return $this->response->setJSON(['status' => true, 'message' => $message]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete user
     */
    public function deleteUser()
    {
        $input = $this->request->getPost();

        if (empty($input['id'])) {
            return $this->response->setJSON(['status' => false, 'message' => 'ไม่พบข้อมูลที่ต้องการลบ']);
        }

        // Prevent deleting yourself
        if ($input['id'] == session()->get('member_id')) {
            return $this->response->setJSON(['status' => false, 'message' => 'ไม่สามารถลบบัญชีของตัวเองได้']);
        }

        try {
            // Use transaction to ensure cleanup
            $this->db->transStart();

            // Delete user roles first
            $this->userRoleModel->where('member_id', $input['id'])->delete();

            // Delete user
            $this->memberModel->delete($input['id']);

            $this->db->transComplete();

            if ($this->db->transStatus() === false) {
                return $this->response->setJSON(['status' => false, 'message' => 'เกิดข้อผิดพลาดในการลบข้อมูล']);
            }

            return $this->response->setJSON(['status' => true, 'message' => 'ลบผู้ใช้งานสำเร็จ']);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }
}
