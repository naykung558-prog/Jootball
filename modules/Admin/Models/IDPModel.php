<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class IDPModel extends Model
{
    protected $table      = "idp_templates";
    protected $primaryKey = "id";
    protected $allowedFields = [];

    // =====================================================
    // Templates
    // =====================================================

    /**
     * Get list of templates with optional filters
     */
    public function getTemplates($filters = [])
    {
        $builder = $this->db->table('idp_templates');
        $builder->select('idp_templates.*, (SELECT COUNT(*) FROM idp_dimensions WHERE idp_dimensions.template_id = idp_templates.id) as dimension_count');

        if (!empty($filters['owner_type'])) {
            $builder->where('idp_templates.owner_type', $filters['owner_type']);
        }
        if (isset($filters['is_active'])) {
            $builder->where('idp_templates.is_active', $filters['is_active']);
        }
        if (!empty($filters['search'])) {
            $builder->groupStart();
            $builder->like('idp_templates.title', $filters['search']);
            $builder->orLike('idp_templates.description', $filters['search']);
            $builder->groupEnd();
        }

        $builder->orderBy('idp_templates.created_at', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get single template by ID
     */
    public function getTemplate($id)
    {
        $builder = $this->db->table('idp_templates');
        $builder->select('idp_templates.*, (SELECT COUNT(*) FROM idp_dimensions WHERE idp_dimensions.template_id = idp_templates.id) as dimension_count');
        $builder->where('idp_templates.id', $id);
        return $builder->get()->getRowArray();
    }

    /**
     * Insert or update a template
     */
    public function saveTemplate($data)
    {
        $builder = $this->db->table('idp_templates');

        $fields = ['title', 'description', 'owner_type', 'is_public', 'is_active'];
        $saveData = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $saveData[$field] = $data[$field];
            }
        }

        // Handle checkboxes defaulting to 0
        if (!isset($data['is_public'])) {
            $saveData['is_public'] = 0;
        }
        if (!isset($data['is_active'])) {
            $saveData['is_active'] = 0;
        }

        if (!empty($data['id'])) {
            // Update
            $saveData['updated_at'] = date('Y-m-d H:i:s');
            $builder->where('id', $data['id']);
            $builder->update($saveData);
            return $data['id'];
        } else {
            // Insert
            $saveData['created_by'] = session()->get('member_id') ? session()->get('member_id') : 0;
            $saveData['created_at'] = date('Y-m-d H:i:s');
            $saveData['updated_at'] = date('Y-m-d H:i:s');
            $builder->insert($saveData);
            return $this->db->insertID();
        }
    }

    /**
     * Delete a template
     */
    public function deleteTemplate($id)
    {
        $template = $this->getTemplate($id);
        if (!$template) {
            return ['success' => false, 'message' => 'Template not found'];
        }

        // Check if template has dimensions
        $dimCount = $this->db->table('idp_dimensions')->where('template_id', $id)->countAllResults();
        if ($dimCount > 0) {
            return ['success' => false, 'message' => 'Cannot delete template with existing dimensions. Remove dimensions first.'];
        }

        $this->db->table('idp_templates')->where('id', $id)->delete();
        return ['success' => true, 'message' => 'Template deleted successfully'];
    }

    /**
     * Get dimensions for a specific template
     */
    public function getTemplateDimensions($template_id)
    {
        $builder = $this->db->table('idp_dimensions');
        $builder->select('idp_dimensions.*, (SELECT COUNT(*) FROM idp_indicators WHERE idp_indicators.dimension_id = idp_dimensions.id) as indicator_count');
        $builder->where('idp_dimensions.template_id', $template_id);
        $builder->orderBy('idp_dimensions.sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * DataTable source for templates
     */
    public function getTemplatesDataTable()
    {
        $builder = $this->db->table('idp_templates');
        $builder->select('idp_templates.*');

        return DataTable::of($builder)
            ->add('dimension_count', function ($row) {
                return $this->db->table('idp_dimensions')->where('template_id', $row->id)->countAllResults();
            })
            ->add('owner_type_label', function ($row) {
                if ($row->owner_type === 'global') {
                    $label = lang('Common.idp_admin_owner_global');
                    return '<span class="badge bg-primary">' . $label . '</span>';
                } else {
                    $label = lang('Common.idp_admin_owner_academy');
                    return '<span class="badge bg-info">' . $label . '</span>';
                }
            })
            ->add('status', function ($row) {
                if ($row->is_active == 1) {
                    $label = lang('Common.active');
                    return '<span class="badge bg-success">' . $label . '</span>';
                } else {
                    $label = lang('Common.inactive');
                    return '<span class="badge bg-secondary">' . $label . '</span>';
                }
            })
            ->add('action', function ($row) {
                $detail_url = base_url('admin/idp/template/detail/' . $row->id);
                $edit_url = base_url('admin/idp/template/manage/' . $row->id);
                $langDetail = lang('Common.idp_admin_template_detail');
                $langEdit = lang('Common.edit');
                $langDelete = lang('Common.delete');
                $rowId = $row->id;
                $tools = '<a href="' . $detail_url . '" class="btn btn-info btn-sm me-1"><i class="fa fa-eye"></i> ' . $langDetail . '</a>'
                    . '<a href="' . $edit_url . '" class="btn btn-warning text-dark btn-sm me-1"><i class="fa fa-pencil"></i> ' . $langEdit . '</a>'
                    . '<button type="button" class="btn btn-danger btn-sm" onclick="deleteTemplate(' . $rowId . ')"><i class="fa fa-trash"></i> ' . $langDelete . '</button>';
                return $tools;
            })
            ->addNumbering()
            ->setSearchableColumns(['title', 'description'])
            ->toJson(true);
    }

    // =====================================================
    // Dimensions
    // =====================================================

    /**
     * Get list of dimensions with optional filters
     */
    public function getDimensions($filters = [])
    {
        $builder = $this->db->table('idp_dimensions');
        $builder->select('idp_dimensions.*, idp_templates.title as template_title, (SELECT COUNT(*) FROM idp_indicators WHERE idp_indicators.dimension_id = idp_dimensions.id) as indicator_count');
        $builder->join('idp_templates', 'idp_templates.id = idp_dimensions.template_id', 'left');

        if (!empty($filters['template_id'])) {
            $builder->where('idp_dimensions.template_id', $filters['template_id']);
        }
        if (isset($filters['is_active'])) {
            $builder->where('idp_dimensions.is_active', $filters['is_active']);
        }

        $builder->orderBy('idp_dimensions.sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get single dimension by ID
     */
    public function getDimension($id)
    {
        $builder = $this->db->table('idp_dimensions');
        $builder->select('idp_dimensions.*, idp_templates.title as template_title, (SELECT COUNT(*) FROM idp_indicators WHERE idp_indicators.dimension_id = idp_dimensions.id) as indicator_count');
        $builder->join('idp_templates', 'idp_templates.id = idp_dimensions.template_id', 'left');
        $builder->where('idp_dimensions.id', $id);
        return $builder->get()->getRowArray();
    }

    /**
     * Insert or update a dimension
     */
    public function saveDimension($data)
    {
        $builder = $this->db->table('idp_dimensions');

        $fields = ['template_id', 'name_th', 'name_en', 'description_th', 'description_en', 'icon', 'color', 'sort_order', 'is_active', 'owner_type', 'owner_id', 'is_public'];
        $saveData = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $saveData[$field] = $data[$field];
            }
        }

        // Handle checkboxes defaulting to 0
        if (!isset($data['is_active'])) {
            $saveData['is_active'] = 0;
        }
        if (!isset($data['is_public'])) {
            $saveData['is_public'] = 0;
        }

        if (!empty($data['id'])) {
            // Update
            $builder->where('id', $data['id']);
            $builder->update($saveData);
            return $data['id'];
        } else {
            // Insert
            $builder->insert($saveData);
            return $this->db->insertID();
        }
    }

    /**
     * Delete a dimension
     */
    public function deleteDimension($id)
    {
        $dimension = $this->getDimension($id);
        if (!$dimension) {
            return ['success' => false, 'message' => 'Dimension not found'];
        }

        // Check if dimension has indicators
        $indCount = $this->db->table('idp_indicators')->where('dimension_id', $id)->countAllResults();
        if ($indCount > 0) {
            return ['success' => false, 'message' => 'Cannot delete dimension with existing indicators. Remove indicators first.'];
        }

        $this->db->table('idp_dimensions')->where('id', $id)->delete();
        return ['success' => true, 'message' => 'Dimension deleted successfully'];
    }

    /**
     * Get indicators for a specific dimension
     */
    public function getDimensionIndicators($dimension_id)
    {
        $builder = $this->db->table('idp_indicators');
        $builder->select('idp_indicators.*');
        $builder->where('idp_indicators.dimension_id', $dimension_id);
        $builder->orderBy('idp_indicators.sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * DataTable source for dimensions
     */
    public function getDimensionsDataTable($template_id = null)
    {
        $builder = $this->db->table('idp_dimensions');
        $builder->select('idp_dimensions.*, idp_templates.title as template_title');
        $builder->join('idp_templates', 'idp_templates.id = idp_dimensions.template_id', 'left');

        if ($template_id) {
            $builder->where('idp_dimensions.template_id', $template_id);
        }

        return DataTable::of($builder)
            ->add('indicator_count', function ($row) {
                return $this->db->table('idp_indicators')->where('dimension_id', $row->id)->countAllResults();
            })
            ->add('icon_preview', function ($row) {
                $icon = !empty($row->icon) ? $row->icon : 'fa-circle';
                $color = !empty($row->color) ? $row->color : '#6c757d';
                return '<span style="color: ' . $color . '; font-size: 1.5rem;"><i class="fa ' . $icon . '"></i></span>';
            })
            ->add('name_display', function ($row) {
                $lang = function_exists('get_current_language') ? get_current_language() : 'th';
                if ($lang === 'en' && !empty($row->name_en)) {
                    return $row->name_en;
                }
                return $row->name_th;
            })
            ->add('status', function ($row) {
                if ($row->is_active == 1) {
                    $label = lang('Common.active');
                    return '<span class="badge bg-success">' . $label . '</span>';
                } else {
                    $label = lang('Common.inactive');
                    return '<span class="badge bg-secondary">' . $label . '</span>';
                }
            })
            ->add('action', function ($row) {
                $indicators_url = base_url('admin/idp/indicators/' . $row->id);
                $edit_url = base_url('admin/idp/dimension/manage/' . $row->template_id . '/' . $row->id);
                $langIndicators = lang('Common.idp_admin_view_indicators');
                $langEdit = lang('Common.edit');
                $langDelete = lang('Common.delete');
                $rowId = $row->id;
                $tools = '<a href="' . $indicators_url . '" class="btn btn-info btn-sm me-1"><i class="fa fa-list"></i> ' . $langIndicators . '</a>'
                    . '<a href="' . $edit_url . '" class="btn btn-warning text-dark btn-sm me-1"><i class="fa fa-pencil"></i> ' . $langEdit . '</a>'
                    . '<button type="button" class="btn btn-danger btn-sm" onclick="deleteDimension(' . $rowId . ')"><i class="fa fa-trash"></i> ' . $langDelete . '</button>';
                return $tools;
            })
            ->addNumbering()
            ->setSearchableColumns(['name_th', 'name_en'])
            ->toJson(true);
    }

    // =====================================================
    // Indicators
    // =====================================================

    /**
     * Get list of indicators with optional filters
     */
    public function getIndicators($filters = [])
    {
        $builder = $this->db->table('idp_indicators');
        $builder->select('idp_indicators.*, idp_dimensions.name_th as dimension_name_th, idp_dimensions.name_en as dimension_name_en');
        $builder->join('idp_dimensions', 'idp_dimensions.id = idp_indicators.dimension_id', 'left');

        if (!empty($filters['dimension_id'])) {
            $builder->where('idp_indicators.dimension_id', $filters['dimension_id']);
        }
        if (isset($filters['is_active'])) {
            $builder->where('idp_indicators.is_active', $filters['is_active']);
        }

        $builder->orderBy('idp_indicators.sort_order', 'ASC');
        return $builder->get()->getResultArray();
    }

    /**
     * Get single indicator by ID
     */
    public function getIndicator($id)
    {
        $builder = $this->db->table('idp_indicators');
        $builder->select('idp_indicators.*, idp_dimensions.name_th as dimension_name_th, idp_dimensions.name_en as dimension_name_en, idp_dimensions.template_id');
        $builder->join('idp_dimensions', 'idp_dimensions.id = idp_indicators.dimension_id', 'left');
        $builder->where('idp_indicators.id', $id);
        return $builder->get()->getRowArray();
    }

    /**
     * Insert or update an indicator
     */
    public function saveIndicator($data)
    {
        $builder = $this->db->table('idp_indicators');

        $fields = ['dimension_id', 'name_th', 'name_en', 'description_th', 'description_en', 'measurement_type', 'unit', 'min_value', 'max_value', 'is_lower_better', 'sort_order', 'is_active', 'owner_type', 'owner_id'];
        $saveData = [];
        foreach ($fields as $field) {
            if (isset($data[$field])) {
                $saveData[$field] = $data[$field];
            }
        }

        // Handle checkboxes defaulting to 0
        if (!isset($data['is_lower_better'])) {
            $saveData['is_lower_better'] = 0;
        }
        if (!isset($data['is_active'])) {
            $saveData['is_active'] = 0;
        }

        // Handle nullable numeric fields
        if (isset($data['min_value']) && $data['min_value'] === '') {
            $saveData['min_value'] = null;
        }
        if (isset($data['max_value']) && $data['max_value'] === '') {
            $saveData['max_value'] = null;
        }

        if (!empty($data['id'])) {
            // Update
            $builder->where('id', $data['id']);
            $builder->update($saveData);
            return $data['id'];
        } else {
            // Insert
            $builder->insert($saveData);
            return $this->db->insertID();
        }
    }

    /**
     * Delete an indicator
     */
    public function deleteIndicator($id)
    {
        $indicator = $this->getIndicator($id);
        if (!$indicator) {
            return ['success' => false, 'message' => 'Indicator not found'];
        }

        $this->db->table('idp_indicators')->where('id', $id)->delete();
        return ['success' => true, 'message' => 'Indicator deleted successfully'];
    }

    /**
     * DataTable source for indicators
     */
    public function getIndicatorsDataTable($dimension_id = null)
    {
        $builder = $this->db->table('idp_indicators');
        $builder->select('idp_indicators.*, idp_dimensions.name_th as dimension_name_th, idp_dimensions.name_en as dimension_name_en, idp_dimensions.template_id');
        $builder->join('idp_dimensions', 'idp_dimensions.id = idp_indicators.dimension_id', 'left');

        if ($dimension_id) {
            $builder->where('idp_indicators.dimension_id', $dimension_id);
        }

        return DataTable::of($builder)
            ->add('name_display', function ($row) {
                $lang = function_exists('get_current_language') ? get_current_language() : 'th';
                if ($lang === 'en' && !empty($row->name_en)) {
                    return $row->name_en;
                }
                return $row->name_th;
            })
            ->add('dimension_name', function ($row) {
                $lang = function_exists('get_current_language') ? get_current_language() : 'th';
                if ($lang === 'en' && !empty($row->dimension_name_en)) {
                    return $row->dimension_name_en;
                }
                return $row->dimension_name_th;
            })
            ->add('measurement_label', function ($row) {
                $type = $row->measurement_type;
                $labels = [
                    'score' => 'Score',
                    'time' => 'Time',
                    'distance' => 'Distance',
                    'percentage' => 'Percentage',
                    'rating' => 'Rating',
                ];
                if (isset($labels[$type])) {
                    return $labels[$type];
                }
                return $type;
            })
            ->add('range_display', function ($row) {
                $min = isset($row->min_value) ? $row->min_value : '-';
                $max = isset($row->max_value) ? $row->max_value : '-';
                return $min . ' - ' . $max;
            })
            ->add('status', function ($row) {
                if ($row->is_active == 1) {
                    $label = lang('Common.active');
                    return '<span class="badge bg-success">' . $label . '</span>';
                } else {
                    $label = lang('Common.inactive');
                    return '<span class="badge bg-secondary">' . $label . '</span>';
                }
            })
            ->add('action', function ($row) {
                $edit_url = base_url('admin/idp/indicator/manage/' . $row->dimension_id . '/' . $row->id);
                $langEdit = lang('Common.edit');
                $langDelete = lang('Common.delete');
                $rowId = $row->id;
                $tools = '<a href="' . $edit_url . '" class="btn btn-warning text-dark btn-sm me-1"><i class="fa fa-pencil"></i> ' . $langEdit . '</a>'
                    . '<button type="button" class="btn btn-danger btn-sm" onclick="deleteIndicator(' . $rowId . ')"><i class="fa fa-trash"></i> ' . $langDelete . '</button>';
                return $tools;
            })
            ->addNumbering()
            ->setSearchableColumns(['idp_indicators.name_th', 'idp_indicators.name_en'])
            ->toJson(true);
    }
}
