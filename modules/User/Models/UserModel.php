<?php

namespace Modules\User\Models;

use CodeIgniter\Model;
use \Hermawan\DataTables\DataTable;

class UserModel extends Model
{
    protected $table      = "user";
    protected $primaryKey = "id";
    protected $allowedFields = ['username', 'email', 'password', 'name', 'uid_line', 'profile_image', 'create_date'];

    function verifyUser($input)
    {
        $loginId = $input['username'];

        // Try username first
        $builder = $this->db->table($this->table);
        $user = $builder->select('*')->where('username', $loginId)->get()->getRowArray();

        // If not found by username, try email
        if (!$user) {
            $builder = $this->db->table($this->table);
            $user = $builder->select('*')->where('email', $loginId)->get()->getRowArray();
        }

        if ($user && password_verify($input['password'], $user['password'])) {
            return $user;
        }

        return null;
    }

    function checkUsername($username)
    {
        $builder = $this->db->table($this->table);
        $count = $builder->where('username', $username)->countAllResults();
        return $count > 0;
    }

    function check_uid_line($uid_line = '')
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->where("uid_line", $uid_line);
        $result = $builder->get()->getRowArray();
        return $result;
    }

    function saveUser($profile, $uid_line = null)
    { // สำหรับเชื่อมไลน์ ตอนสมัคร
        $builder = $this->db->table($this->table);
        $builder->set('name', $profile->name);
        $builder->set('uid_line', $uid_line ?? $profile->line_uid);
        $builder->set('create_date', date('Y-m-d H:i:s'));
        $builder->insert();
    }

    function editUser($input)
    {
        // Check which columns actually exist in the user table
        $tableFields = $this->db->getFieldNames($this->table);

        $builder = $this->db->table($this->table);
        if (!empty($input['username']) && in_array('username', $tableFields)) {
            $builder->set('username', $input['username']);
        }
        if (isset($input['email']) && in_array('email', $tableFields)) {
            $builder->set('email', $input['email']);
        }
        if (!empty($input['name']) && in_array('name', $tableFields)) {
            $builder->set('name', $input['name']);
        }
        if (!empty($input['password']) && in_array('password', $tableFields)) {
            $builder->set('password', password_hash($input['password'], PASSWORD_DEFAULT));
        }
        if (isset($input['profile_image']) && in_array('profile_image', $tableFields)) {
            $builder->set('profile_image', $input['profile_image']);
        }
        if (isset($input['uid_line']) && in_array('uid_line', $tableFields)) {
            $builder->set('uid_line', $input['uid_line']);
        }

        if (!empty($input['id'])) {
            $builder->where('id', $input['id']);
            $builder->update();
        } else {
            $builder->set('create_date', date('Y-m-d H:i:s'));
            $builder->insert();
        }
    }

    function getOneUser($id = '', $uid_line = '')
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        if ($id) {
            $builder->where('id', $id);
        }
        if ($uid_line) {
            $builder->where('uid_line', $uid_line);
        }
        $result = $builder->get()->getRowArray();
        return $result;
    }

    /**
     * Get a user by their LINE user ID
     * @param string $lineUserId The LINE user ID (uid_line)
     * @return array|null The user data or null if not found
     */
    function getOneUserByLineId($lineUserId)
    {
        $builder = $this->db->table($this->table);
        $builder->select('*');
        $builder->where('uid_line', $lineUserId);
        $result = $builder->get()->getRowArray();
        return $result;
    }
}
