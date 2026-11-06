<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;

class UserRoleModel extends Model
{
    protected $table = 'user_roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['member_id', 'role_id', 'assigned_by', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get user roles with role details
     */
    public function getUserRoles($memberId)
    {
        return $this->db->table($this->table)
            ->select('user_roles.*, roles.name as role_name, roles.display_name as role_display_name, roles.description as role_description')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.member_id', $memberId)
            ->where('roles.status', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * Assign role to user
     */
    public function assignRole($memberId, $roleId, $assignedBy = null)
    {
        // Check if already assigned
        $existing = $this->where('member_id', $memberId)
            ->where('role_id', $roleId)
            ->first();
        
        if ($existing) {
            return ['status' => false, 'message' => 'บทบาทนี้ได้ถูกกำหนดให้ผู้ใช้แล้ว'];
        }
        
        $data = [
            'member_id' => $memberId,
            'role_id' => $roleId,
            'assigned_by' => $assignedBy,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->insert($data);
        
        if ($result) {
            return ['status' => true, 'message' => 'กำหนดบทบาทสำเร็จ'];
        }
        
        return ['status' => false, 'message' => 'เกิดข้อผิดพลาดในการกำหนดบทบาท'];
    }

    /**
     * Remove role from user
     */
    public function removeRole($memberId, $roleId)
    {
        $result = $this->where('member_id', $memberId)
            ->where('role_id', $roleId)
            ->delete();
        
        if ($result) {
            return ['status' => true, 'message' => 'ลบบทบาทสำเร็จ'];
        }
        
        return ['status' => false, 'message' => 'เกิดข้อผิดพลาดในการลบบทบาท'];
    }

    /**
     * Update user roles (remove all and assign new ones)
     */
    public function updateUserRoles($memberId, $roleIds, $assignedBy = null)
    {
        // Start transaction
        $this->db->transStart();
        
        try {
            // Remove existing roles
            $this->where('member_id', $memberId)->delete();
            
            // Assign new roles
            if (!empty($roleIds)) {
                $data = [];
                foreach ($roleIds as $roleId) {
                    $data[] = [
                        'member_id' => $memberId,
                        'role_id' => $roleId,
                        'assigned_by' => $assignedBy,
                        'created_at' => date('Y-m-d H:i:s')
                    ];
                }
                $this->insertBatch($data);
            }
            
            $this->db->transComplete();
            
            if ($this->db->transStatus() === false) {
                return ['status' => false, 'message' => 'เกิดข้อผิดพลาดในการอัปเดตบทบาท'];
            }
            
            return ['status' => true, 'message' => 'อัปเดตบทบาทสำเร็จ'];
            
        } catch (\Exception $e) {
            $this->db->transRollback();
            return ['status' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()];
        }
    }

    /**
     * Check if user has role
     */
    public function userHasRole($memberId, $roleName)
    {
        $result = $this->db->table($this->table)
            ->select('user_roles.id')
            ->join('roles', 'roles.id = user_roles.role_id')
            ->where('user_roles.member_id', $memberId)
            ->where('roles.name', $roleName)
            ->where('roles.status', 1)
            ->get()
            ->getRowArray();
        
        return !empty($result);
    }

    /**
     * Get users with their roles
     */
    public function getUsersWithRoles()
    {
        return $this->db->table('member')
            ->select('member.id, member.username, GROUP_CONCAT(roles.display_name) as roles, MAX(user_roles.created_at) as assigned_at')
            ->join('user_roles', 'user_roles.member_id = member.id', 'left')
            ->join('roles', 'roles.id = user_roles.role_id', 'left')
            ->where('member.access_login', 1)
            ->groupBy('member.id')
            ->get()
            ->getResultArray();
    }
}