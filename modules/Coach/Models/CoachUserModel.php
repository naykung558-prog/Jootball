<?php

namespace Modules\Coach\Models;

use CodeIgniter\Model;

class CoachUserModel extends Model
{
    protected $table = "coach_users";
    protected $primaryKey = "id";
    protected $allowedFields = ['username', 'password', 'email', 'uid_line', 'line_name', 'line_picture', 'is_active', 'last_login', 'created_at', 'updated_at'];
    protected $useTimestamps = false;

    public function verifyUser($credentials)
    {
        $builder = $this->db->table($this->table);

        if (isset($credentials['email']) && isset($credentials['password'])) {
            // Login with email/password
            $builder->where('email', $credentials['email']);
            $builder->where('is_active', 1);
            $user = $builder->get()->getRowArray();

            if ($user && password_verify($credentials['password'], $user['password'])) {
                // Update last login
                $this->updateLastLogin($user['id']);
                return $user;
            }
        }

        return false;
    }

    public function checkUidLine($uid_line)
    {
        $builder = $this->db->table($this->table);
        $builder->where('uid_line', $uid_line);
        $builder->where('is_active', 1);
        return $builder->get()->getRowArray();
    }

    public function getOneUser($id = '', $uid_line = '')
    {
        $builder = $this->db->table($this->table);

        if ($id) {
            $builder->where('id', $id);
        } elseif ($uid_line) {
            $builder->where('uid_line', $uid_line);
        }

        $builder->where('is_active', 1);
        return $builder->get()->getRowArray();
    }

    public function saveUser($profile, $lineUserId = null)
    {
        $data = [
            'uid_line' => $lineUserId ?: ($profile->line_uid ?? $profile->sub),
            'line_name' => $profile->name ?? '',
            'line_picture' => '', // Will be updated after download with local path
            'email' => $profile->email ?? '',
            'username' => 'coach_' . ($lineUserId ?: ($profile->line_uid ?? $profile->sub)),
            'is_active' => 1,
            'created_at' => date('Y-m-d H:i:s')
        ];

        return $this->insert($data);
    }

    public function getOneUserByLineId($lineUserId)
    {
        $builder = $this->db->table($this->table);
        $builder->where('uid_line', $lineUserId);
        $builder->where('is_active', 1);
        return $builder->get()->getRowArray();
    }

    public function updateLastLogin($userId)
    {
        $builder = $this->db->table($this->table);
        $builder->where('id', $userId);
        $builder->set('last_login', date('Y-m-d H:i:s'));
        return $builder->update();
    }

    public function createCoachUser($data)
    {
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $data['created_at'] = date('Y-m-d H:i:s');
        $data['updated_at'] = date('Y-m-d H:i:s');

        // Use query builder for better error handling
        try {
            $builder = $this->db->table($this->table);
            $result = $builder->insert($data);

            if (!$result) {
                $error = $this->db->error();
                log_message('error', 'Failed to create coach user: ' . json_encode($error));
                log_message('error', 'Data: ' . json_encode($data));
                return false;
            }

            return $this->db->insertID();

        } catch (\Exception $e) {
            log_message('error', 'Exception creating coach user: ' . $e->getMessage());
            log_message('error', 'Data: ' . json_encode($data));
            return false;
        }
    }

    public function updateCoachUser($id, $data)
    {
        // Hash password if provided
        if (!empty($data['password'])) {
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
        }

        $data['updated_at'] = date('Y-m-d H:i:s');
        $builder = $this->db->table($this->table);
        $builder->where('id', $id);
        return $builder->update($data);
    }

    public function getUserRoles($userId)
    {
        $builder = $this->db->table('coach_user_roles');
        $builder->select('coach_roles.name, coach_roles.display_name, coach_roles.description');
        $builder->join('coach_roles', 'coach_user_roles.role_id = coach_roles.id');
        $builder->where('coach_user_roles.coach_user_id', $userId);
        $builder->where('coach_roles.is_active', 1);
        return $builder->get()->getResultArray();
    }

    public function getUserPermissions($userId)
    {
        $builder = $this->db->table('coach_user_roles');
        $builder->select('coach_permissions.name, coach_permissions.display_name, coach_permissions.module');
        $builder->join('coach_roles', 'coach_user_roles.role_id = coach_roles.id');
        $builder->join('coach_role_permissions', 'coach_roles.id = coach_role_permissions.role_id');
        $builder->join('coach_permissions', 'coach_role_permissions.permission_id = coach_permissions.id');
        $builder->where('coach_user_roles.coach_user_id', $userId);
        $builder->where('coach_roles.is_active', 1);
        $builder->where('coach_permissions.is_active', 1);
        return $builder->get()->getResultArray();
    }

    public function assignRole($userId, $roleId, $assignedBy = null)
    {
        $data = [
            'coach_user_id' => $userId,
            'role_id' => $roleId,
            'assigned_by' => $assignedBy,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $builder = $this->db->table('coach_user_roles');
        return $builder->insert($data);
    }

    public function removeRole($userId, $roleId)
    {
        $builder = $this->db->table('coach_user_roles');
        $builder->where('coach_user_id', $userId);
        $builder->where('role_id', $roleId);
        return $builder->delete();
    }

    public function hasPermission($userId, $permission)
    {
        $permissions = $this->getUserPermissions($userId);
        foreach ($permissions as $perm) {
            if ($perm['name'] === $permission) {
                return true;
            }
        }
        return false;
    }
}