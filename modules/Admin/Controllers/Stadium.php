<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\StadiumModel;

class Stadium extends BaseController
{
    protected $db;
    protected $stadiumModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->stadiumModel = new StadiumModel();
    }

    public function index()
    {
        return view('Modules\Admin\Views\Stadium\index');
    }

    public function ajax_stadiums()
    {
        return $this->stadiumModel->get_Stadium();
    }

    public function manage($id = '')
    {
        $data = ['mode' => ($id == '' ? 'add' : 'edit')];
        if ($id) {
            $data['data'] = $this->stadiumModel->get_Stadium($id);
            $data['images'] = $this->stadiumModel->getImages($id);
        }
        return view('Modules\Admin\Views\Stadium\manage', $data);
    }

    public function save()
    {
        $input = $this->request->getPost();
        $stadiumId = $this->stadiumModel->saveStadium($input);

        // Upload รูปภาพ (ถ้ามี)
        $files = $this->request->getFileMultiple('images');
        if ($files) {
            $saveDir = 'public/uploads/stadiums/' . date('Y') . '/' . date('m') . '/';
            if (!is_dir(FCPATH . $saveDir)) {
                mkdir(FCPATH . $saveDir, 0755, true);
            }

            foreach ($files as $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move(FCPATH . $saveDir, $newName);
                    $this->stadiumModel->saveImage($stadiumId, $saveDir . $newName);
                }
            }
        }

        return redirect()->to(base_url('admin/stadium/manage/' . $stadiumId));
    }

    public function delete()
    {
        $input = $this->request->getPost();
        $this->stadiumModel->deleteStadium($input['id']);
        return "success";
    }

    public function upload_image()
    {
        $stadiumId = $this->request->getPost('stadium_id');
        $file = $this->request->getFile('image');

        if (!$file || !$file->isValid()) {
            return $this->response->setJSON(['status' => 'error', 'msg' => lang('Common.error')]);
        }

        $saveDir = 'public/uploads/stadiums/' . date('Y') . '/' . date('m') . '/';
        if (!is_dir($saveDir)) {
            mkdir($saveDir, 0755, true);
        }

        $newName = $file->getRandomName();
        $file->move(FCPATH . $saveDir, $newName);
        $imagePath = $saveDir . $newName;

        $imageId = $this->stadiumModel->saveImage($stadiumId, $imagePath);

        return $this->response->setJSON([
            'status' => 'success',
            'msg' => lang('Common.save_success'),
            'image' => [
                'id' => $imageId,
                'path' => $imagePath,
                'url' => base_url($imagePath)
            ]
        ]);
    }

    public function delete_image()
    {
        $imageId = $this->request->getPost('image_id');
        $this->stadiumModel->deleteImage($imageId);
        return $this->response->setJSON(['status' => 'success', 'msg' => lang('Common.delete_success')]);
    }

    public function set_cover_image()
    {
        $stadiumId = $this->request->getPost('stadium_id');
        $imageId = $this->request->getPost('image_id');
        $this->stadiumModel->setCoverImage($stadiumId, $imageId);
        return $this->response->setJSON(['status' => 'success', 'msg' => lang('Common.save_success')]);
    }

    /**
     * AJAX: เพิ่มสนามใหม่แบบ quick add (จากหน้า match setup)
     * เช็คชื่อซ้ำก่อน insert
     */
    public function quick_add()
    {
        $name = trim($this->request->getPost('name'));

        if (empty($name)) {
            return $this->response->setJSON([
                'status' => 'error',
                'msg' => lang('Common.stadium_name_required')
            ]);
        }

        // เช็คชื่อซ้ำ
        $duplicate = $this->stadiumModel->checkDuplicateName($name);
        if ($duplicate) {
            return $this->response->setJSON([
                'status' => 'duplicate',
                'msg' => lang('Common.stadium_name_duplicate'),
                'stadium' => $duplicate
            ]);
        }

        // Insert สนามใหม่
        $id = $this->stadiumModel->saveStadium([
            'name' => $name,
            'is_active' => 1
        ]);

        return $this->response->setJSON([
            'status' => 'success',
            'msg' => lang('Common.save_success'),
            'stadium' => [
                'id' => $id,
                'name' => $name
            ]
        ]);
    }
}
