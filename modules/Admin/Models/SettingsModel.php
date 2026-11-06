<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table = 'system_settings';
    protected $primaryKey = 'id';
    protected $allowedFields = ['setting_key', 'setting_value', 'setting_group', 'description', 'updated_at'];

    /**
     * ดึงค่าการตั้งค่าตาม key
     */
    public function getSetting($key, $default = null)
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    /**
     * ดึงการตั้งค่าทั้งหมดในกลุ่ม
     */
    public function getSettingsByGroup($group)
    {
        $settings = $this->where('setting_group', $group)->findAll();

        $result = [];
        foreach ($settings as $setting) {
            $result[$setting['setting_key']] = $setting['setting_value'];
        }

        return $result;
    }

    /**
     * บันทึกหรืออัพเดทการตั้งค่า
     */
    public function saveSetting($key, $value, $group = 'general', $description = null)
    {
        $existing = $this->where('setting_key', $key)->first();

        if ($existing) {
            // อัพเดทค่าเดิม
            return $this->update($existing['id'], [
                'setting_value' => $value,
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            // สร้างใหม่
            return $this->insert([
                'setting_key' => $key,
                'setting_value' => $value,
                'setting_group' => $group,
                'description' => $description
            ]);
        }
    }

    /**
     * บันทึกหลายค่าพร้อมกัน
     */
    public function saveSettings($data, $group = null)
    {
        foreach ($data as $key => $value) {
            $this->saveSetting($key, $value, $group ?? 'general');
        }

        return true;
    }

    /**
     * ลบการตั้งค่า
     */
    public function deleteSetting($key)
    {
        return $this->where('setting_key', $key)->delete();
    }

    /**
     * ดึงการตั้งค่าทั้งหมด
     */
    public function getAllSettings()
    {
        $settings = $this->findAll();

        $result = [];
        foreach ($settings as $setting) {
            if (!isset($result[$setting['setting_group']])) {
                $result[$setting['setting_group']] = [];
            }
            $result[$setting['setting_group']][$setting['setting_key']] = $setting['setting_value'];
        }

        return $result;
    }
}
