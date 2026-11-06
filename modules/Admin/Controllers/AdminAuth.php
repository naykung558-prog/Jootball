<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\MemberModel;
use Modules\Log\Models\LogModel;

class AdminAuth extends BaseController
{
    protected $memberModel;
    protected $logLoginModel;

    public function __construct()
    {
        $this->memberModel = new MemberModel();
        $this->logLoginModel = new LogModel();
    }

    public function login()
    {
        // Check if admin is already logged in
        if (session()->get('isAdminLoggedIn')) {
            $permissionModel = new \Modules\Admin\Models\PermissionModel();
            $memberId = session()->get('member_id');
            $userPermissions = $permissionModel->getUserPermissions($memberId);
            $redirectUrl = $this->getFirstAccessiblePage($memberId, $userPermissions);
            return redirect()->to(base_url($redirectUrl));
        }

        // Handle language switch
        $lang = $this->request->getGet('lang');
        if ($lang && in_array($lang, ['th', 'en'])) {
            helper('language');
            set_language($lang);
            return redirect()->to(base_url('admin/login'));
        }

        $data = [];

        // Dev quick login: show member list only on localhost + development
        if (ENVIRONMENT === 'development' && $this->isLocalhost()) {
            $db = \Config\Database::connect();
            $members = $db->table('member')
                ->select('id, username, password')
                ->where('access_login', 1)
                ->get()->getResultArray();
            $data['dev_members'] = $members;
        }

        return view('Modules\Admin\Views\Auth\login', $data);
    }

    private function isLocalhost(): bool
    {
        $host = $_SERVER['HTTP_HOST'] ?? '';
        $localHosts = ['localhost', '127.0.0.1', '::1'];
        foreach ($localHosts as $lh) {
            if (strpos($host, $lh) !== false) {
                return true;
            }
        }
        return false;
    }

    public function signin()
    {
        // If already logged in, redirect to first accessible page
        if (session()->get('isAdminLoggedIn')) {
            $permissionModel = new \Modules\Admin\Models\PermissionModel();
            $memberId = session()->get('member_id');
            $userPermissions = $permissionModel->getUserPermissions($memberId);
            $redirectUrl = $this->getFirstAccessiblePage($memberId, $userPermissions);
            return redirect()->to(base_url($redirectUrl));
        }

        $input = $this->request->getPost();
        $user = $this->memberModel->verifyUser($input['username'], $input['password']);

        if ($user) {
            // Load user permissions
            $permissionModel = new \Modules\Admin\Models\PermissionModel();
            $userRoleModel = new \Modules\Admin\Models\UserRoleModel();

            $userPermissions = $permissionModel->getUserPermissions($user['id']);
            $userRoles = $userRoleModel->getUserRoles($user['id']);

            $ses_data = [
                'member_id' => $user['id'],
                'username' => $user['username'],
                'isAdminLoggedIn' => TRUE,
                'user_permissions' => array_column($userPermissions, 'name'),
                'user_roles' => array_column($userRoles, 'role_name'),
                'is_super_admin' => $userRoleModel->userHasRole($user['id'], 'super_admin'),
            ];
            session()->set($ses_data);

            // Remember Me
            if ($this->request->getPost('remember_me')) {
                $token = $this->memberModel->createRememberToken($user['id']);
                if ($token) {
                    setcookie('admin_remember', $token, time() + (30 * 24 * 60 * 60), '/', '', false, true);
                }
            }

        $this->logLoginModel->saveLoginLog(
            array(
            'post_data' => json_encode($this->request->getPost(), JSON_UNESCAPED_UNICODE),
            'response_data'=>'success',
            'ip_address'=>$this->request->getIPAddress(),
            'user_id' => $user['id'],
            'type'=>'web'
        ));

            // Redirect ไปหน้าแรกที่ user มีสิทธิ์เข้าถึง
            $redirectUrl = $this->getFirstAccessiblePage($user['id'], $userPermissions);
            return redirect()->to(base_url($redirectUrl))->with('success', 'เข้าสู่ระบบสำเร็จ');
        }

        $this->logLoginModel->saveLoginLog(
            array(
            'post_data' => json_encode($this->request->getPost(), JSON_UNESCAPED_UNICODE),
            'response_data'=>'error',
            'ip_address'=>$this->request->getIPAddress(),
            'user_id' => null,
            'type'=>'web'
        ));
        session()->setFlashdata('msg', 'ไม่พบชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง');
        return redirect()->to(base_url('admin/login'));
    }

    /**
     * หาหน้าแรกที่ user มีสิทธิ์เข้าถึง เพื่อป้องกัน redirect loop
     */
    private function getFirstAccessiblePage($userId, $userPermissions)
    {
        $permissionNames = array_column($userPermissions, 'name');

        // Super admin ไป dashboard เลย
        $userRoleModel = new \Modules\Admin\Models\UserRoleModel();
        if ($userRoleModel->userHasRole($userId, 'super_admin')) {
            return 'admin/dashboard';
        }

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
            'tournament.manage_matches'   => 'admin/tournament',
        ];

        foreach ($pageMap as $permission => $url) {
            if (in_array($permission, $permissionNames)) {
                return $url;
            }
        }

        // ถ้าไม่มีสิทธิ์อะไรเลย ไป dashboard (จะแสดงหน้าเปล่า)
        return 'admin/dashboard';
    }

    public function logout()
    {
        // ลบ Remember Me token
        $cookieToken = $_COOKIE['admin_remember'] ?? null;
        if ($cookieToken) {
            $parts = explode(':', $cookieToken);
            if (count($parts) === 2) {
                $this->memberModel->deleteRememberTokenBySelector($parts[0]);
            }
            setcookie('admin_remember', '', time() - 3600, '/', '', false, true);
        }

        $session = session();
        $session->set(array(
            'member_id' => '',
            'username' => '',
            'isAdminLoggedIn' => FALSE,
        ));
        // $session->destroy();
        return redirect()->to(base_url('/'));
    }
}
