<?php

namespace Modules\Admin\Controllers;

use App\Controllers\BaseController;
use \Hermawan\DataTables\DataTable;
use Modules\Admin\Models\IDPModel;

class IDP extends BaseController
{
    protected $idpModel;

    public function __construct()
    {
        $this->idpModel = new IDPModel();
    }

    // =====================================================
    // Templates
    // =====================================================

    /**
     * Templates list page
     */
    public function templates()
    {
        return view('Modules\Admin\Views\IDP\templates');
    }

    /**
     * DataTable AJAX source for templates
     */
    public function ajax_templates()
    {
        return $this->idpModel->getTemplatesDataTable();
    }

    /**
     * Template create/edit form
     */
    public function templateManage($id = '')
    {
        $data = [];
        if ($id) {
            $data['template'] = $this->idpModel->getTemplate($id);
            if (!$data['template']) {
                return redirect()->to('/admin/idp/templates')->with('error', lang('Common.not_found'));
            }
        }
        return view('Modules\Admin\Views\IDP\template_manage', $data);
    }

    /**
     * Template save (POST)
     */
    public function templateSave()
    {
        $input = $this->request->getPost();
        $this->idpModel->saveTemplate($input);
        return redirect()->to('/admin/idp/templates')->with('success', lang('Common.idp_admin_save_success'));
    }

    /**
     * Template delete (POST)
     */
    public function templateDelete()
    {
        $id = $this->request->getPost('id');
        $result = $this->idpModel->deleteTemplate($id);
        return $this->response->setJSON($result);
    }

    /**
     * Template detail page showing dimensions
     */
    public function templateDetail($id)
    {
        $data = [];
        $data['template'] = $this->idpModel->getTemplate($id);
        if (!$data['template']) {
            return redirect()->to('/admin/idp/templates')->with('error', lang('Common.not_found'));
        }
        $data['dimensions'] = $this->idpModel->getTemplateDimensions($id);
        return view('Modules\Admin\Views\IDP\template_detail', $data);
    }

    // =====================================================
    // Dimensions
    // =====================================================

    /**
     * Dimensions list page (optionally filtered by template)
     */
    public function dimensions($template_id = null)
    {
        $data = [];
        $data['template_id'] = $template_id;
        if ($template_id) {
            $data['template'] = $this->idpModel->getTemplate($template_id);
        }
        return view('Modules\Admin\Views\IDP\dimensions', $data);
    }

    /**
     * DataTable AJAX source for dimensions
     */
    public function ajax_dimensions()
    {
        $template_id = $this->request->getPost('template_id');
        return $this->idpModel->getDimensionsDataTable($template_id);
    }

    /**
     * Dimension create/edit form
     */
    public function dimensionManage($template_id, $id = '')
    {
        $data = [];
        $data['template_id'] = $template_id;
        $data['template'] = $this->idpModel->getTemplate($template_id);
        $data['templates'] = $this->idpModel->getTemplates();
        if ($id) {
            $data['dimension'] = $this->idpModel->getDimension($id);
            if (!$data['dimension']) {
                return redirect()->to('/admin/idp/dimensions/' . $template_id)->with('error', lang('Common.not_found'));
            }
        }
        return view('Modules\Admin\Views\IDP\dimension_manage', $data);
    }

    /**
     * Dimension save (POST)
     */
    public function dimensionSave()
    {
        $input = $this->request->getPost();
        $template_id = isset($input['template_id']) ? $input['template_id'] : '';
        $this->idpModel->saveDimension($input);
        return redirect()->to('/admin/idp/template/detail/' . $template_id)->with('success', lang('Common.idp_admin_save_success'));
    }

    /**
     * Dimension delete (POST)
     */
    public function dimensionDelete()
    {
        $id = $this->request->getPost('id');
        $result = $this->idpModel->deleteDimension($id);
        return $this->response->setJSON($result);
    }

    // =====================================================
    // Indicators
    // =====================================================

    /**
     * Indicators list page (optionally filtered by dimension)
     */
    public function indicators($dimension_id = null)
    {
        $data = [];
        $data['dimension_id'] = $dimension_id;
        if ($dimension_id) {
            $data['dimension'] = $this->idpModel->getDimension($dimension_id);
        }
        return view('Modules\Admin\Views\IDP\indicators', $data);
    }

    /**
     * DataTable AJAX source for indicators
     */
    public function ajax_indicators()
    {
        $dimension_id = $this->request->getPost('dimension_id');
        return $this->idpModel->getIndicatorsDataTable($dimension_id);
    }

    /**
     * Indicator create/edit form
     */
    public function indicatorManage($dimension_id, $id = '')
    {
        $data = [];
        $data['dimension_id'] = $dimension_id;
        $data['dimension'] = $this->idpModel->getDimension($dimension_id);
        $data['dimensions'] = $this->idpModel->getDimensions();
        if ($id) {
            $data['indicator'] = $this->idpModel->getIndicator($id);
            if (!$data['indicator']) {
                return redirect()->to('/admin/idp/indicators/' . $dimension_id)->with('error', lang('Common.not_found'));
            }
        }
        return view('Modules\Admin\Views\IDP\indicator_manage', $data);
    }

    /**
     * Indicator save (POST)
     */
    public function indicatorSave()
    {
        $input = $this->request->getPost();
        $dimension_id = isset($input['dimension_id']) ? $input['dimension_id'] : '';
        $this->idpModel->saveIndicator($input);

        // Get the dimension to find its template_id for redirect
        $dimension = $this->idpModel->getDimension($dimension_id);
        if ($dimension) {
            return redirect()->to('/admin/idp/indicators/' . $dimension_id)->with('success', lang('Common.idp_admin_save_success'));
        }
        return redirect()->to('/admin/idp/templates')->with('success', lang('Common.idp_admin_save_success'));
    }

    /**
     * Indicator delete (POST)
     */
    public function indicatorDelete()
    {
        $id = $this->request->getPost('id');
        $result = $this->idpModel->deleteIndicator($id);
        return $this->response->setJSON($result);
    }
}
