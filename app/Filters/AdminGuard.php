<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AdminGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // 1. ตรวจ session ปกติ
        if (session()->get('isAdminLoggedIn')) {
            // Reload permissions จาก DB ทุก request เพื่อให้การแก้ไขสิทธิ์มีผลทันที
            $memberId = session()->get('member_id');
            if ($memberId) {
                $permissionModel = new \Modules\Admin\Models\PermissionModel();
                $userRoleModel = new \Modules\Admin\Models\UserRoleModel();

                $userPermissions = $permissionModel->getUserPermissions($memberId);
                $userRoles = $userRoleModel->getUserRoles($memberId);

                session()->set([
                    'user_permissions' => array_column($userPermissions, 'name'),
                    'user_roles'       => array_column($userRoles, 'role_name'),
                    'is_super_admin'   => $userRoleModel->userHasRole($memberId, 'super_admin'),
                ]);
            }
            return;
        }

        // 2. ตรวจ Remember Me cookie
        $cookieToken = $_COOKIE['admin_remember'] ?? null;
        if ($cookieToken) {
            $memberModel = new \Modules\Admin\Models\MemberModel();
            $member = $memberModel->validateRememberToken($cookieToken);

            if ($member) {
                // Token ถูกต้อง - สร้าง session ใหม่ (auto-login)
                $permissionModel = new \Modules\Admin\Models\PermissionModel();
                $userRoleModel = new \Modules\Admin\Models\UserRoleModel();

                $userPermissions = $permissionModel->getUserPermissions($member['id']);
                $userRoles = $userRoleModel->getUserRoles($member['id']);

                $ses_data = [
                    'member_id'        => $member['id'],
                    'username'         => $member['username'],
                    'isAdminLoggedIn'  => TRUE,
                    'user_permissions' => array_column($userPermissions, 'name'),
                    'user_roles'       => array_column($userRoles, 'role_name'),
                    'is_super_admin'   => $userRoleModel->userHasRole($member['id'], 'super_admin'),
                ];
                session()->set($ses_data);

                // Token Rotation - ลบ token เก่า สร้างใหม่
                $memberModel->deleteRememberTokenBySelector($member['selector']);
                $newToken = $memberModel->createRememberToken($member['id']);
                if ($newToken) {
                    setcookie('admin_remember', $newToken, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                }

                return;
            }

            // Token ไม่ถูกต้อง - ลบ cookie
            setcookie('admin_remember', '', time() - 3600, '/', '', false, true);
        }

        // 3. ไม่มี session + ไม่มี cookie -> redirect ไป login
        return redirect()->to(base_url('admin/login'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null) {}
}
