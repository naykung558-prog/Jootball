<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class CoachGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // ตรวจสอบว่าล็อกอินแล้วหรือยัง
        if (!$session->get('isLoggedIn')) {
            if ($request->isAJAX()) {
                return service('response')->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
            }
            return redirect()->to('/coach')->with('error', 'กรุณาเข้าสู่ระบบก่อน');
        }

        // ตรวจสอบว่าเป็น coach หรือไม่
        if ($session->get('user_type') !== 'coach') {
            if ($request->isAJAX()) {
                return service('response')->setJSON(['status' => 'error', 'message' => 'Forbidden'])->setStatusCode(403);
            }
            return redirect()->to('/coach')->with('error', 'คุณไม่มีสิทธิ์เข้าใช้งานหน้านี้');
        }

        // ตรวจสอบว่า coach ยังใช้งานอยู่หรือไม่
        $coachData = $session->get('coach_data');
        if (!$coachData || $coachData['is_active'] != 1) {
            if ($request->isAJAX()) {
                return service('response')->setJSON(['status' => 'error', 'message' => 'Account suspended'])->setStatusCode(403);
            }
            session_destroy();
            return redirect()->to('/coach')->with('error', 'บัญชีของคุณถูกระงับการใช้งาน');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do something here
    }
}