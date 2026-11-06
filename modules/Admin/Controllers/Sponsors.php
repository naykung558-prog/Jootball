<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\SponsorsModel;

class Sponsors extends BaseController
{
    protected $db;
    protected $sponsorsModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->sponsorsModel = new SponsorsModel();
    }

    public function index()
    {
        $data['control'] = ['admin', 'sponsors'];
        return view('Modules\Admin\Views\Sponsors\index', $data);
    }

    public function ajax_sponsors()
    {
        return $this->sponsorsModel->get_Sponsors();
    }

    public function manage($id = '')
    {
        $data = ['mode' => ($id == '' ? 'add' : 'edit')];
        $data['control'] = ['admin', 'sponsors'];
        if ($id) {
            $data['data'] = $this->sponsorsModel->get_Sponsors($id);
        }
        return view('Modules\Admin\Views\Sponsors\manage', $data);
    }

    public function save()
    {
        $input = $this->request->getPost();
        $sponsorId = $this->sponsorsModel->saveSponsors($input);

        // Upload โลโก้ (ถ้ามี)
        $file = $this->request->getFile('logo');
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $saveDir = 'public/uploads/sponsors/';
            if (!is_dir(FCPATH . $saveDir)) {
                mkdir(FCPATH . $saveDir, 0755, true);
            }

            $newName = 'sponsor_' . $sponsorId . '_' . time() . '.' . $file->getExtension();
            $file->move(FCPATH . $saveDir, $newName);
            
            // อัพเดท path โลโก้ในฐานข้อมูล
            $this->db->table('sponsors')->update(['logo' => $saveDir . $newName], ['id' => $sponsorId]);
        }

        return redirect()->to(base_url('admin/sponsors/manage/' . $sponsorId));
    }

    public function delete()
    {
        $input = $this->request->getPost();
        $this->sponsorsModel->deleteSponsors($input['id']);
        return "success";
    }

    public function toggleStatus()
    {
        $input = $this->request->getPost();
        
        if (empty($input['id'])) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'ID not found'
            ]);
        }
        
        $result = $this->sponsorsModel->toggleStatus($input['id'], $input['is_active']);
        
        if ($result) {
            return $this->response->setJSON([
                'status' => 'success'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Cannot update status'
            ]);
        }
    }
}
