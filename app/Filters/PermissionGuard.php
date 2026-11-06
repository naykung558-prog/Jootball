<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;
use Modules\Admin\Models\PermissionModel;

class PermissionGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Check if user is logged in first
        if (!session()->get('isAdminLoggedIn')) {
            return redirect()->to(base_url('admin/login'));
        }
        
        // If no permission specified, allow access (for basic admin access)
        if (empty($arguments)) {
            return;
        }
        
        $memberId = session()->get('member_id');

        // Super admin bypasses all permission checks
        if ($this->isSuperAdmin($memberId)) {
            return;
        }

        // Check if user has ANY of the required permissions
        $permissionModel = new PermissionModel();
        foreach ($arguments as $permissionName) {
            if ($permissionModel->userHasPermission($memberId, $permissionName)) {
                return;
            }
        }

        // Log the access attempt
        log_message('warning', "User ID {$memberId} attempted to access [" . implode(', ', $arguments) . "] without permission");

        // Redirect ไปหน้าแรกที่ user มีสิทธิ์ (ไม่ redirect ไป dashboard เพราะอาจไม่มีสิทธิ์ → redirect loop)
        $redirectUrl = $this->getFirstAccessiblePage($memberId);
        return redirect()->to(base_url($redirectUrl))->with('error', 'คุณไม่มีสิทธิ์เข้าถึงหน้านี้');
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Nothing to do after
    }
    
    /**
     * Check if user is super admin
     */
    private function isSuperAdmin($memberId)
    {
        $userRoleModel = new \Modules\Admin\Models\UserRoleModel();
        return $userRoleModel->userHasRole($memberId, 'super_admin');
    }

    /**
     * หาหน้าแรกที่ user มีสิทธิ์เข้าถึง เพื่อป้องกัน redirect loop
     */
    private function getFirstAccessiblePage($memberId)
    {
        $permissionModel = new PermissionModel();

        // ครอบคลุมทุก route group ที่มี permissionGuard
        $pageMap = [
            'dashboard.view'  => 'admin/dashboard',
            'tournament.view' => 'admin/tournament',
            'team.view'       => 'admin/team',
            'team.requests'   => 'admin/team-requests',
            'team.verify'     => 'admin/team-verify',
            'academy.view'    => 'admin/academy',
            'stadium.view'    => 'admin/stadium',
            'idp.view'        => 'admin/idp',
            'coach.view'      => 'admin/coach',
            'report.view'     => 'admin/report/report-players',
            'report.guest'    => 'admin/report-guest/report-players',
            'scout.view'      => 'admin/scout',
            'user.view'       => 'admin/users',
            'role.view'       => 'admin/role',
            'settings.view'   => 'admin/settings/email',
        ];

        foreach ($pageMap as $permission => $url) {
            if ($permissionModel->userHasPermission($memberId, $permission)) {
                return $url;
            }
        }

        // ถ้าไม่มีสิทธิ์อะไรเลย ให้ logout
        return 'admin/logout';
    }
}