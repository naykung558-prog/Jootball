<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;

class RoleModel extends Model
{
    protected $table = 'roles';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'display_name', 'description', 'status', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all active roles
     */
    public function getActiveRoles()
    {
        return $this->where('status', 1)->findAll();
    }

    /**
     * Get role with permissions
     */
    public function getRoleWithPermissions($roleId)
    {
        return $this->db->table($this->table)
            ->select('roles.*, permissions.name as permission_name, permissions.display_name as permission_display_name, permissions.module')
            ->join('role_permissions', 'role_permissions.role_id = roles.id', 'left')
            ->join('permissions', 'permissions.id = role_permissions.permission_id', 'left')
            ->where('roles.id', $roleId)
            ->where('roles.status', 1)
            ->get()
            ->getResultArray();
    }

    /**
     * Get all roles with permission counts
     */
    public function getRolesWithPermissionCounts()
    {
        return $this->db->table($this->table)
            ->select('roles.*, COUNT(role_permissions.permission_id) as permission_count')
            ->join('role_permissions', 'role_permissions.role_id = roles.id', 'left')
            ->where('roles.status', 1)
            ->groupBy('roles.id')
            ->get()
            ->getResultArray();
    }

    /**
     * Assign permissions to role
     */
    public function assignPermissions($roleId, $permissionIds)
    {
        // Delete existing permissions
        $this->db->table('role_permissions')->where('role_id', $roleId)->delete();
        
        // Insert new permissions
        $data = [];
        foreach ($permissionIds as $permissionId) {
            $data[] = [
                'role_id' => $roleId,
                'permission_id' => $permissionId,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        
        if (!empty($data)) {
            return $this->db->table('role_permissions')->insertBatch($data);
        }
        
        return true;
    }

    /**
     * Get role permissions
     */
    public function getRolePermissions($roleId)
    {
        return $this->db->table('role_permissions')
            ->select('permissions.*')
            ->join('permissions', 'permissions.id = role_permissions.permission_id')
            ->where('role_permissions.role_id', $roleId)
            ->where('permissions.status', 1)
            ->get()
            ->getResultArray();
    }
}