<?php

if (!function_exists('hasPermission')) {
    /**
     * Check if current user has specific permission
     * 
     * @param string $permission
     * @return bool
     */
    function hasPermission($permission)
    {
        // Super admin bypasses all permission checks
        if (session()->get('is_super_admin')) {
            return true;
        }
        
        $userPermissions = session()->get('user_permissions') ?? [];
        return in_array($permission, $userPermissions);
    }
}

if (!function_exists('hasRole')) {
    /**
     * Check if current user has specific role
     * 
     * @param string $role
     * @return bool
     */
    function hasRole($role)
    {
        $userRoles = session()->get('user_roles') ?? [];
        return in_array($role, $userRoles);
    }
}

if (!function_exists('isSuperAdmin')) {
    /**
     * Check if current user is super admin
     * 
     * @return bool
     */
    function isSuperAdmin()
    {
        return session()->get('is_super_admin') ?? false;
    }
}

if (!function_exists('hasAnyPermission')) {
    /**
     * Check if current user has any of the specified permissions
     * 
     * @param array $permissions
     * @return bool
     */
    function hasAnyPermission($permissions)
    {
        // Super admin bypasses all permission checks
        if (session()->get('is_super_admin')) {
            return true;
        }
        
        $userPermissions = session()->get('user_permissions') ?? [];
        
        foreach ($permissions as $permission) {
            if (in_array($permission, $userPermissions)) {
                return true;
            }
        }
        
        return false;
    }
}

if (!function_exists('hasAllPermissions')) {
    /**
     * Check if current user has all of the specified permissions
     * 
     * @param array $permissions
     * @return bool
     */
    function hasAllPermissions($permissions)
    {
        // Super admin bypasses all permission checks
        if (session()->get('is_super_admin')) {
            return true;
        }
        
        $userPermissions = session()->get('user_permissions') ?? [];
        
        foreach ($permissions as $permission) {
            if (!in_array($permission, $userPermissions)) {
                return false;
            }
        }
        
        return true;
    }
}

if (!function_exists('canAccess')) {
    /**
     * Check if current user can access a specific module/action
     * 
     * @param string $module
     * @param string $action (view, create, edit, delete)
     * @return bool
     */
    function canAccess($module, $action = 'view')
    {
        return hasPermission("{$module}.{$action}");
    }
}