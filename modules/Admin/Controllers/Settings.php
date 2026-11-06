<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\SettingsModel;

class Settings extends BaseController
{
    protected $settingsModel;

    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
    }

    /**
     * แสดงหน้าตั้งค่า Email
     */
    public function email_settings()
    {
        $data['settings'] = $this->settingsModel->getSettingsByGroup('email');
        return view('Modules\Admin\Views\Settings\email_settings', $data);
    }

    /**
     * บันทึกการตั้งค่า Email
     */
    public function save_email_settings()
    {
        $input = $this->request->getPost();

        // Validation
        $rules = [
            'email_smtp_host' => 'required',
            'email_smtp_user' => 'required|valid_email',
            'email_smtp_port' => 'required|numeric',
            'email_smtp_crypto' => 'required|in_list[tls,ssl]',
            'email_from_email' => 'required|valid_email',
            'email_from_name' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                           ->withInput()
                           ->with('errors', $this->validator->getErrors());
        }

        // บันทึกการตั้งค่า
        $settingsToSave = [
            'email_smtp_host' => $input['email_smtp_host'],
            'email_smtp_user' => $input['email_smtp_user'],
            'email_smtp_port' => $input['email_smtp_port'],
            'email_smtp_crypto' => $input['email_smtp_crypto'],
            'email_from_email' => $input['email_from_email'],
            'email_from_name' => $input['email_from_name']
        ];

        // บันทึก password เฉพาะเมื่อมีการกรอกใหม่
        if (!empty($input['email_smtp_pass'])) {
            $settingsToSave['email_smtp_pass'] = $input['email_smtp_pass'];
        }

        $this->settingsModel->saveSettings($settingsToSave, 'email');

        return redirect()->back()->with('success', 'บันทึกการตั้งค่าเรียบร้อยแล้ว');
    }

    /**
     * ทดสอบส่งอีเมล
     */
    public function test_email()
    {
        $testEmail = $this->request->getPost('test_email');

        if (empty($testEmail) || !filter_var($testEmail, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'กรุณากรอกอีเมลที่ถูกต้อง'
            ]);
        }

        try {
            // โหลดการตั้งค่าจาก database
            $settings = $this->settingsModel->getSettingsByGroup('email');

            // ตั้งค่า Email service
            $email = \Config\Services::email();
            $email->initialize([
                'protocol' => 'smtp',
                'SMTPHost' => $settings['email_smtp_host'] ?? 'smtp.gmail.com',
                'SMTPUser' => $settings['email_smtp_user'] ?? '',
                'SMTPPass' => $settings['email_smtp_pass'] ?? '',
                'SMTPPort' => $settings['email_smtp_port'] ?? 587,
                'SMTPCrypto' => $settings['email_smtp_crypto'] ?? 'tls',
                'mailType' => 'html',
                'charset' => 'UTF-8',
                'SMTPTimeout' => 60
            ]);

            $email->setFrom(
                $settings['email_from_email'] ?? 'noreply@jootball.com',
                $settings['email_from_name'] ?? 'Jootball Academy'
            );
            $email->setTo($testEmail);
            $email->setSubject('ทดสอบการส่งอีเมล - Jootball Academy');
            $email->setMessage($this->getTestEmailTemplate());

            if ($email->send()) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'ส่งอีเมลทดสอบสำเร็จ กรุณาตรวจสอบที่ ' . $testEmail
                ]);
            } else {
                log_message('error', 'Test email failed: ' . $email->printDebugger(['headers']));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่สามารถส่งอีเมลได้ กรุณาตรวจสอบการตั้งค่า'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Test email exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Template สำหรับอีเมลทดสอบ
     */
    private function getTestEmailTemplate()
    {
        return '
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #16478C; color: white; padding: 20px; text-align: center; }
        .content { background-color: #f9f9f9; padding: 30px; }
        .success { color: #28a745; font-size: 18px; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>✅ ทดสอบส่งอีเมลสำเร็จ!</h1>
        </div>
        <div class="content">
            <p class="success">การตั้งค่าอีเมลของคุณทำงานได้ถูกต้อง</p>
            <p>นี่คืออีเมลทดสอบจากระบบ Jootball Academy</p>
            <p>หากคุณเห็นข้อความนี้ แสดงว่าการตั้งค่า SMTP ทำงานได้อย่างสมบูรณ์</p>
            <hr>
            <p><small>อีเมลนี้ส่งจากระบบอัตโนมัติ กรุณาอย่าตอบกลับ</small></p>
        </div>
    </div>
</body>
</html>
        ';
    }
}
