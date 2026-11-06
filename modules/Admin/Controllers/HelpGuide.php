<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use Modules\Admin\Models\HelpGuideModel;

class HelpGuide extends BaseController
{
    protected $db;
    protected $helpGuideModel;

    public function __construct()
    {
        $this->db = db_connect();
        $this->helpGuideModel = new HelpGuideModel();
    }

    public function index()
    {
        $data = ['control' => ['admin', 'help-guide']];
        return view('Modules\Admin\Views\HelpGuide\index', $data);
    }

    public function ajax_list()
    {
        return $this->helpGuideModel->ajax_list();
    }

    public function manage($id = '')
    {
        $data = [
            'mode'    => ($id === '' ? 'add' : 'edit'),
            'control' => ['admin', 'help-guide'],
        ];
        if ($id !== '') {
            $data['data'] = $this->helpGuideModel->get_one($id);
            if (empty($data['data'])) {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
            }
        }
        return view('Modules\Admin\Views\HelpGuide\manage', $data);
    }

    public function save()
    {
        $input = $this->request->getPost();

        if (empty($input['title_th']) || empty($input['link'])) {
            session()->setFlashdata('error', lang('Common.fill_required_fields'));
            return redirect()->back()->withInput();
        }

        $input['sort_order'] = isset($input['sort_order']) ? (int)$input['sort_order'] : 0;
        $input['is_active']  = isset($input['is_active']) ? (int)$input['is_active'] : 1;

        $id = $this->helpGuideModel->save_item($input);
        return redirect()->to(base_url('admin/help-guide/manage/' . $id));
    }

    public function delete()
    {
        $input = $this->request->getPost();
        if (empty($input['id'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID not found']);
        }
        $this->helpGuideModel->delete_item($input['id']);
        return $this->response->setJSON(['status' => 'success']);
    }

    public function toggleStatus()
    {
        $input = $this->request->getPost();
        if (empty($input['id'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ID not found']);
        }
        $ok = $this->helpGuideModel->toggle_status($input['id'], (int)$input['is_active']);
        return $this->response->setJSON(['status' => $ok ? 'success' : 'error']);
    }
}
