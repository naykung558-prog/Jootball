<?php

namespace Modules\Admin\Controllers;

use App\Libraries\MatchScheduler;
use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\CoachModel;
use Modules\Player\Models\ProvinceModel;

class Coach extends BaseController
{
    protected $db;
    protected $coachModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->coachModel = new CoachModel();
    }

    public function index()
    {
        return view('Modules\Admin\Views\Coach\coach_index');
    }

    public function coach_manage($id = '')
    {
        $data = ['mode' => ($id == '' ? 'add' : 'edit')];
        if ($id) {
            $data['data'] = $this->coachModel->get_Coach($id);
        }
        $data['team_options'] = $this->coachModel->get_Teams();
        $provinceModel = new ProvinceModel();
        $data['provinces'] = $provinceModel->orderBy('province_name', 'ASC')->findAll();

        return view('Modules\Admin\Views\Coach\coach_manage.php', $data);
    }

    public function ajax_coachs()
    {
        $result = $this->coachModel->get_Coach();
        return $result;
    }

    public function save_coach()
    {
        $input = $this->request->getPost();

        if (!empty($input['base64_image'])) {
            $saveDir = 'public/uploads/coachs/';
            if (!is_dir($saveDir)) {
                mkdir($saveDir, 0755, true);
            }

            $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
            $img = $input['base64_image'];
            $img = str_replace('data:image/png;base64,', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $set_name_img = $random_name . '.jpeg';
            $file = "public/uploads/coachs/" . $set_name_img;
            file_put_contents($file, $data);
            $input['image_path'] = 'public/uploads/coachs/' . $set_name_img;
        }

        // Handle ID Card Image upload (base64)
        if (!empty($input['id_card_image_base64'])) {
            $saveDir = WRITEPATH . 'secure_uploads/id_cards/';
            if (!is_dir($saveDir)) {
                mkdir($saveDir, 0755, true);
            }

            $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
            $img = $input['id_card_image_base64'];
            // Support both PNG and JPEG base64
            $img = preg_replace('/^data:image\/\w+;base64,/', '', $img);
            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $set_name_img = $random_name . '.jpeg';
            $file = $saveDir . $set_name_img;
            file_put_contents($file, $data);
            $input['id_card_image'] = 'secure_uploads/id_cards/' . $set_name_img;
        } elseif (!empty($input['delete_id_card_image']) && $input['delete_id_card_image'] == '1') {
            $input['id_card_image'] = '';
        }

        $result = $this->coachModel->saveCoach($input);
        return redirect()->to(base_url('admin/coach'));
    }

    public function delete_coach()
    {
        $input = $this->request->getPost();
        $this->coachModel->deleteCoach($input['id']);
        return "success";
    }

    public function certs_manage($id = '')
    {
        //$data['position'] = $this->coachModel->player_position();
        $data = ['mode' => ($id == '' ? 'add' : 'edit')];
        if ($id) {
            $data['data'] = $this->coachModel->get_Certs($id);
        }
        $data['id']= $id;
        $data['cert_level'] = $this->coachModel->get_certLevel();
        //die(print_r($data));
        $data['organ'] = $this->coachModel->get_Organ();
        return view('Modules\Admin\Views\Coach\certs_manage.php', $data);
    }


    // tbl coach_team
    public function ajax_certs($coach_id = '')
    {
        $result = $this->coachModel->get_Certs($coach_id);
        return $result;
    }

    public function save_certs()
    {
        $input = $this->request->getPost();

        $saveDir = 'public/uploads/coachs/certs/';
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0755, true);
        }

        $certImages = [];
        $files = $this->request->getFiles();

        // Handle file uploads
        if (!empty($files['cert_image_file'])) {
            foreach ($files['cert_image_file'] as $index => $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($saveDir, $newName);
                    $certImages[$index] = $saveDir . $newName;
                }
            }
        }

        // Handle base64 images (from modal add)
        if (!empty($input['cert_image_base64'])) {
            foreach ($input['cert_image_base64'] as $index => $base64) {
                if (!empty($base64)) {
                    $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
                    $img = preg_replace('/^data:image\/\w+;base64,/', '', $base64);
                    $img = str_replace(' ', '+', $img);
                    $data = base64_decode($img);
                    $fileName = $random_name . '.jpeg';
                    file_put_contents($saveDir . $fileName, $data);
                    $certImages[$index] = $saveDir . $fileName;
                }
            }
        }

        $result = $this->coachModel->save_certs($input, $certImages);
        $coach_id = $input['coach_id'];
        return redirect()->to(base_url('admin/coach/certs-manage/' . $coach_id));
    }

    function delete_certs()
    {
        $input = $this->request->getPost();
        $result = $this->coachModel->delete_certs($input['id']);
        if ($result == "success") {
            $response = array(
                'status' => 'success',
                'msg' => 'ลบสำเร็จ'
            );
        } else {
            $response = array(
                'status' => 'error',
                'msg' => 'เกิดข้อผิดพลาด'
            );
        }
        return $this->response->setJson($response);
    }
    

} 
