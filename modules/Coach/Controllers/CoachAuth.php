<?php

namespace Modules\Coach\Controllers;

use App\Controllers\BaseController;
use Modules\Coach\Models\CoachUserModel;
use Modules\Coach\Models\CoachModel;
use App\Libraries\LineLogin;

class CoachAuth extends BaseController
{
    protected $coachUserModel;
    protected $coachModel;

    public function __construct()
    {
        $this->coachUserModel = new CoachUserModel();
        $this->coachModel = new CoachModel();
    }

    public function index()
    {
        $line = new LineLogin();
        $data['encrypter'] = $this->encrypter;
        // ส่ง 'coach' เป็น parameter เพื่อให้ callback รู้ว่าเป็น coach login
        $data['link'] = $line->getLink('coach');
        return view('Modules\Coach\Views\login', $data);
    }

    public function signin_with_account()
    {
        $input = $this->request->getPost();
        $user = $this->coachUserModel->verifyUser($input);

        if ($user) {
            $coach = $this->coachModel->getCoachByCoachUserId($user['id']);

            if (!$coach) {
                return redirect()->to('/coach')->with('error', 'บัญชีนี้ไม่ใช่บัญชีผู้ฝึกสอน');
            }

            $ses_data = [
                'coach_user_id' => $user['id'],
                'coach_id' => $coach['id'],
                'coach_data' => $coach,
                'user_type' => 'coach',
                'isLoggedIn' => true,
            ];
            session()->set($ses_data);

            return redirect()->to('/coach/dashboard')->with('success', 'เข้าสู่ระบบสำเร็จ');
        } else {
            return redirect()->to('/coach')->with('error', 'ไม่พบชื่อผู้ใช้งานหรือรหัสผ่านไม่ถูกต้อง');
        }
    }

    public function register()
    {
        return view('Modules\Coach\Views\register');
    }

    public function register_post()
    {
        $input = $this->request->getPost();

        // Use email as username since we removed the username field
        if (empty($input['username']) && !empty($input['email'])) {
            $input['username'] = $input['email'];
        }

        // Validation
        $validation = \Config\Services::validation();
        $validation->setRules([
            // Username validation: Removed is_unique to avoid double error messages (email check covers it for UI purposes)
            'username' => 'required|max_length[100]',
            'password' => 'required|min_length[8]',
            'confirm_password' => 'required|matches[password]',
            'first_name' => 'required|min_length[1]|max_length[100]',
            'last_name' => 'required|min_length[1]|max_length[100]',
            'email' => 'required|valid_email|is_unique[coach_users.email]',
        ], [
            'username' => [
                'required' => 'กรุณากรอกข้อมูลผู้ใช้',
                'max_length' => 'ชื่อผู้ใช้ต้องไม่เกิน 100 ตัวอักษร',
                'is_unique' => 'อีเมลนี้ถูกใช้งานแล้ว',
            ],
            'password' => [
                'required' => 'กรุณากรอกรหัสผ่าน',
                'min_length' => 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร',
            ],
            'confirm_password' => [
                'required' => 'กรุณายืนยันรหัสผ่าน',
                'matches' => 'รหัสผ่านไม่ตรงกัน',
            ],
            'first_name' => [
                'required' => 'กรุณากรอกชื่อ',
                'min_length' => 'ชื่อต้องมีความยาวอย่างน้อย 1 ตัวอักษร',
                'max_length' => 'ชื่อต้องไม่เกิน 100 ตัวอักษร',
            ],
            'last_name' => [
                'required' => 'กรุณากรอกนามสกุล',
                'min_length' => 'นามสกุลต้องมีความยาวอย่างน้อย 1 ตัวอักษร',
                'max_length' => 'นามสกุลต้องไม่เกิน 100 ตัวอักษร',
            ],
            'email' => [
                'required' => 'กรุณากรอกอีเมล',
                'valid_email' => 'รูปแบบอีเมลไม่ถูกต้อง',
                'is_unique' => 'อีเมลนี้ถูกใช้งานแล้วในระบบผู้ฝึกสอน',
            ],
        ]);

        if (!$validation->run($input)) {
            // Get errors as a list for cleaner display
            $errors = $validation->getErrors();
            $errorMessage = implode('<br>', $errors); // Use <br> for line breaks in HTML/SweetAlert
            return redirect()->back()->withInput()->with('error', 'กรุณาตรวจสอบข้อมูล:' . $errorMessage);
        }

        // Cross-check: Check if email exists in Club Users (Club users store email in 'username' column)
        $userModel = new \Modules\User\Models\UserModel();
        if ($userModel->checkUsername($input['email'])) {
             return redirect()->back()->withInput()->with('error', 'อีเมลนี้ถูกใช้งานแล้วในระบบสโมสร');
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Create coach user account
            $userData = [
                'username' => $input['username'],
                'password' => $input['password'],
                'email' => $input['email'] ?? null,
                'is_active' => 1
            ];

            $coachUserId = $this->coachUserModel->createCoachUser($userData);

            if (!$coachUserId) {
                throw new \Exception('ไม่สามารถสร้างบัญชีผู้ใช้ได้');
            }

            // Handle image upload
            $imagePath = null;
            if (!empty($input['image_path'])) {
                $imagePath = $this->saveBase64Image($input['image_path'], 'coach_' . $coachUserId);
            }

            // Create coach profile
            $coachData = [
                'coach_user_id' => $coachUserId,
                'first_name' => $input['first_name'],
                'last_name' => $input['last_name'],
                'nick_name' => $input['nick_name'] ?? null,
                'nation' => $input['nation'] ?? null,
                'id_card' => $input['id_card'] ?? null,
                'birth_date' => !empty($input['birth_date']) ? $input['birth_date'] : null,
                'height' => !empty($input['height']) ? $input['height'] : null,
                'weight' => !empty($input['weight']) ? $input['weight'] : null,
                'dominant_foot' => $input['dominant_foot'] ?? null,
                'image_path' => $imagePath,
                'team_id' => !empty($input['team_id']) ? $input['team_id'] : 0,
                'province_id' => !empty($input['province_id']) ? $input['province_id'] : 0,
                'amphure_id' => !empty($input['amphure_id']) ? $input['amphure_id'] : 0,
                'tambon_id' => !empty($input['tambon_id']) ? $input['tambon_id'] : 0,
                'is_active' => 1,
                'create_date' => date('Y-m-d H:i:s'),
                'update_date' => date('Y-m-d H:i:s')
            ];

            $coachId = $this->coachModel->createCoach($coachData);

            if (!$coachId) {
                $error = $this->coachModel->errors();
                $dbError = $this->coachModel->db->error();
                log_message('error', 'Coach profile creation failed. Model errors: ' . json_encode($error));
                log_message('error', 'DB error: ' . json_encode($dbError));
                log_message('error', 'Coach data: ' . json_encode($coachData));
                throw new \Exception('ไม่สามารถสร้างโปรไฟล์ผู้ฝึกสอนได้');
            }

            $db->transComplete();

            if ($db->transStatus() === FALSE) {
                throw new \Exception('เกิดข้อผิดพลาดในการบันทึกข้อมูล');
            }

            return redirect()->to('/coach')->with('success', 'สมัครสมาชิกสำเร็จ! กรุณาเข้าสู่ระบบ');

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Coach registration error: ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    private function saveBase64Image($base64Data, $filename)
    {
        // Remove data URL prefix
        $data = explode(',', $base64Data);
        if (count($data) !== 2) {
            return null;
        }

        $imageData = base64_decode($data[1]);
        if ($imageData === false) {
            return null;
        }

        // Create upload directory if it doesn't exist
        $uploadPath = FCPATH . 'public/uploads/coach_images/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Generate filename with timestamp
        $extension = 'jpg';
        $fileName = $filename . '_' . time() . '.' . $extension;
        $filePath = $uploadPath . $fileName;

        // Save file
        if (file_put_contents($filePath, $imageData)) {
            return 'public/uploads/coach_images/' . $fileName;
        }

        return null;
    }

    public function logout()
    {
        if (session()->get('frontend_impersonation') && session()->get('isAdminLoggedIn')) {
            session()->remove([
                'profile',
                'uid_line',
                'user_id',
                'team',
                'coach_user_id',
                'coach_id',
                'coach_data',
                'user_type',
                'isLoggedIn',
                'frontend_impersonation',
            ]);
            return redirect()->to(base_url('admin/return-to-admin'))->with('success', 'กลับสู่โหมดผู้ดูแลแล้ว');
        }

        if (isset($_SESSION['profile'])) {
            $profile = $_SESSION['profile'];
            $line = new LineLogin();
            $line->revoke($profile->access_token);
        }
        session_destroy();
        return redirect()->to(base_url('/'));
    }

    public function checkPdpaConsent()
    {
        $userId = session()->get('coach_user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'pdpa_accept' => null
            ]);
        }

        $db = \Config\Database::connect();

        $user = $db->table('coach_users')
                ->select('pdpa_accept')
                ->where('id', $userId)
                ->get()
                ->getRowArray();

        return $this->response->setJSON([
            'pdpa_accept' => $user['pdpa_accept'] ?? 0
        ]);
    }

    public function savePdpaConsent()
    {
        $userId = session()->get('coach_user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error'
            ]);
        }

        $db = \Config\Database::connect();

        $db->table('coach_users')
        ->where('id', $userId)
        ->update([
            'pdpa_accept' => 1,
            'pdpa_accept_datetime' => date('Y-m-d H:i:s')
        ]);

        return $this->response->setJSON([
            'status' => 'success'
        ]);
    }

}
