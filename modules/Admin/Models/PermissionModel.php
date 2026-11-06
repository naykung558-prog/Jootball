<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;

class PermissionModel extends Model
{
    protected $table = 'permissions';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name', 'display_name', 'description', 'module', 'status', 'created_at', 'updated_at'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Get all active permissions
     */
    public function getActivePermissions()
    {
        return $this->where('status', 1)->findAll();
    }

    /**
     * Get permissions grouped by module
     */
    public function getPermissionsByModule()
    {
        $permissions = $this->where('status', 1)
            ->orderBy('module', 'ASC')
            ->orderBy('name', 'ASC')
            ->findAll();
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission['module']][] = $permission;
        }
        
        return $grouped;
    }

    /**
     * Get user permissions by member ID
     */
    public function getUserPermissions($memberId)
    {
        return $this->db->table($this->table)
            ->select('permissions.name, permissions.display_name, permissions.module')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
            ->join('roles', 'roles.id = role_permissions.role_id')
            ->join('user_roles', 'user_roles.role_id = roles.id')
            ->where('user_roles.member_id', $memberId)
            ->where('permissions.status', 1)
            ->where('roles.status', 1)
            ->groupBy('permissions.id')
            ->get()
            ->getResultArray();
    }

    /**
     * Check if user has specific permission
     */
    public function userHasPermission($memberId, $permissionName)
    {
        $result = $this->db->table($this->table)
            ->select('permissions.id')
            ->join('role_permissions', 'role_permissions.permission_id = permissions.id')
            ->join('roles', 'roles.id = role_permissions.role_id')
            ->join('user_roles', 'user_roles.role_id = roles.id')
            ->where('user_roles.member_id', $memberId)
            ->where('permissions.name', $permissionName)
            ->where('permissions.status', 1)
            ->where('roles.status', 1)
            ->get()
            ->getRowArray();
        
        return !empty($result);
    }

    /**
     * Get all permissions for a specific user organized by module
     */
    public function getUserPermissionsByModule($memberId)
    {
        $permissions = $this->getUserPermissions($memberId);
        
        $grouped = [];
        foreach ($permissions as $permission) {
            $grouped[$permission['module']][] = $permission;
        }
        
        return $grouped;
    }
}