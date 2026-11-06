<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\IdCardModel;

class IdCard extends BaseController
{
    protected $db;
    protected $idCardModel;

    public function __construct()
    {
        helper('academy');
        $this->db = db_connect();
        $this->idCardModel = new IdCardModel();
    }

    public function index()
    {
        return view('Modules\Admin\Views\IdCard\index');
    }

    public function ajax_idcards()
    {
        return $this->idCardModel->get_IdCards();
    }

    public function manage($id = '')
    {
        $data = ['mode' => ($id == '' ? 'add' : 'edit')];
        if ($id) {
            $data['data'] = $this->idCardModel->get_IdCard($id);
            $cardType = $data['data']['card_type'] ?? 'player';
            $data['tournaments'] = $this->idCardModel->getTournamentOptions($cardType, $data['data']['tour_id'] ?? null);
            $data['IdCardsAll'] = $this->idCardModel->get_IdCardsAll($cardType);
        } else {
            $cardType = $this->request->getGet('card_type') ?? 'player';
            $data['card_type'] = $cardType;
            $data['tournaments'] = $this->idCardModel->getTournamentOptions($cardType);
        }
        
        return view('Modules\Admin\Views\IdCard\manage', $data);
    }

    public function save()
    {
        $input = $this->request->getPost();

        // Upload template image
        $file = $this->request->getFile('template_image_file');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $saveDir = 'public/uploads/idcards/' . date('Y') . '/' . date('m') . '/';
            if (!is_dir(FCPATH . $saveDir)) {
                mkdir(FCPATH . $saveDir, 0755, true);
            }

            // Delete old image if updating
            if (!empty($input['id'])) {
                $existing = $this->idCardModel->get_IdCard($input['id']);
                if ($existing && !empty($existing['template_image'])) {
                    $oldFile = FCPATH . $existing['template_image'];
                    if (file_exists($oldFile)) {
                        unlink($oldFile);
                    }
                }
            }

            $newName = $file->getRandomName();
            $file->move(FCPATH . $saveDir, $newName);
            $input['template_image'] = $saveDir . $newName;
        }

        $idCardId = $this->idCardModel->saveIdCard($input);

        return redirect()->to(base_url('admin/idcard/manage/' . $idCardId));
    }

    public function delete()
    {
        $input = $this->request->getPost();
        $this->idCardModel->deleteIdCard($input['id']);
        return "success";
    }

    public function preview($id)
    {
        // Security Check: System Admin OR Academy Owner/Staff
        $isAdmin = (bool) session()->get('isAdminLoggedIn');
        $isLoggedIn = (bool) session()->get('isLoggedIn');
        $userAcademyId = getUserAcademyId();

        if (!$isAdmin && !$isLoggedIn) {
            return redirect()->to(base_url('admin/login'))->with('error', 'กรุณาเข้าสู่ระบบก่อนใช้งาน');
        }

        if (!$isAdmin && !isSuperAdmin() && !$userAcademyId) {
            return redirect()->to(base_url())->with('error', 'Unauthorized access');
        }

        $idCard = $this->idCardModel->get_IdCard($id);
        if (!$idCard) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $isManager = ($idCard['card_type'] ?? 'player') === 'manager';

        // Sample data for preview
        if ($isManager) {
            $samplePlayers = [
                ['id' => 1, 'first_name' => 'ผู้จัดการทีม', 'last_name' => 'ตัวอย่าง', 'shirt_number' => '-', 'position_name' => 'Manager', 'image_path' => ''],
                ['id' => 2, 'first_name' => 'MANAGER', 'last_name' => 'SAMPLE', 'shirt_number' => '-', 'position_name' => 'Manager', 'image_path' => ''],
                ['id' => 3, 'first_name' => 'โค้ช', 'last_name' => 'ตัวอย่าง', 'shirt_number' => '-', 'position_name' => 'Coach', 'image_path' => ''],
                ['id' => 4, 'first_name' => 'COACH', 'last_name' => 'TEST', 'shirt_number' => '-', 'position_name' => 'Coach', 'image_path' => ''],
            ];
        } else {
            $samplePlayers = [
                ['id' => 1, 'first_name' => 'ชื่อตัวอย่าง', 'last_name' => 'นามสกุลตัวอย่าง', 'shirt_number' => '10', 'position_name' => 'กองกลาง', 'image_path' => ''],
                ['id' => 2, 'first_name' => 'SAMPLE', 'last_name' => 'PLAYER', 'shirt_number' => '7', 'position_name' => 'กองหน้า', 'image_path' => ''],
                ['id' => 3, 'first_name' => 'ทดสอบ', 'last_name' => 'ระบบ', 'shirt_number' => '99', 'position_name' => 'ผู้รักษาประตู', 'image_path' => ''],
                ['id' => 4, 'first_name' => 'TEST', 'last_name' => 'NAME', 'shirt_number' => '23', 'position_name' => 'กองหลัง', 'image_path' => ''],
            ];
        }

        $data = [
            'id_card_template' => $idCard,
            'team' => [
                'name' => 'ทีมตัวอย่าง SAMPLE TEAM',
                'logo' => 'public/img/jootball_logo.png'
            ],
            'tournament' => [
                'name' => $idCard['tournament_name'] ?? 'Tournament'
            ],
            'players' => $samplePlayers,
        ];

        $this->response->setHeader('Content-Type', 'application/pdf');
        $mpdf = new \Mpdf\Mpdf([
            'default_font' => 'garuda',
            'default_font_size' => 14,
            'mode' => 'utf-8',
            'format' => 'A4',
            'margin_top' => 0,
            'margin_left' => 0,
            'margin_right' => 0,
            'margin_header' => 0,
            'margin_footer' => 0,
            'orientation' => 'P',
        ]);

        // Extract variables for the template
        $id_card_template = $data['id_card_template'];
        $team = $data['team'];
        $tournament = $data['tournament'];
        $players = $data['players'];

        // Include template which uses $mpdf directly
        include ROOTPATH . 'modules/Team/Views/export/player_card_template.php';

        $mpdf->Output('preview_idcard.pdf', 'I');
    }
}
