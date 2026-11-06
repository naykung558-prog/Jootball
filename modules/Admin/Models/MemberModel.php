<?php

namespace Modules\Admin\Models;

use CodeIgniter\Model;

class MemberModel extends Model
{
    protected $table      = "member";
    protected $primaryKey = "id";
    protected $allowedFields = ['username', 'password', 'access_login'];

    function verifyUser($username, $password, $mode='website')
    {
        $builder = $this->db->table($this->table);
        $builder->select("*")->where("username", $username);
        if($mode == 'website') {
            $builder->where("access_login", 1);
        }
        $user = $builder->get()->getRowArray();

        if ($user) {
            // Check if password matches hash
            if (password_verify($password, $user['password'])) {
                return $user;
            }
            // Fallback for legacy plain text passwords
            if ($user['password'] === $password) {
                // Optional: Auto-migrate to hash
                // $this->update($user['id'], ['password' => password_hash($password, PASSWORD_DEFAULT)]);
                return $user;
            }
        }
        return false;
    }

    /**
     * สร้าง Remember Me token
     * @return string|false คืน "selector:validator" สำหรับเก็บใน cookie
     */
    public function createRememberToken($memberId)
    {
        $selector = bin2hex(random_bytes(12));
        $validator = bin2hex(random_bytes(33));
        $hashedValidator = hash('sha256', $validator);
        $expiresAt = date('Y-m-d H:i:s', time() + (30 * 24 * 60 * 60));

        $result = $this->db->table('admin_remember_tokens')->insert([
            'member_id'        => $memberId,
            'selector'         => $selector,
            'hashed_validator' => $hashedValidator,
            'expires_at'       => $expiresAt,
        ]);

        return $result ? $selector . ':' . $validator : false;
    }

    /**
     * ตรวจสอบ Remember Me token
     * @param string $token รูปแบบ "selector:validator"
     * @return array|false คืนข้อมูล member หรือ false
     */
    public function validateRememberToken($token)
    {
        $parts = explode(':', $token);
        if (count($parts) !== 2) {
            return false;
        }

        [$selector, $validator] = $parts;

        $tokenRow = $this->db->table('admin_remember_tokens')
            ->select('admin_remember_tokens.*, member.id as member_id, member.username, member.access_login')
            ->join('member', 'member.id = admin_remember_tokens.member_id')
            ->where('admin_remember_tokens.selector', $selector)
            ->where('admin_remember_tokens.expires_at >', date('Y-m-d H:i:s'))
            ->where('member.access_login', 1)
            ->get()
            ->getRowArray();

        if (!$tokenRow) {
            return false;
        }

        $hashedValidator = hash('sha256', $validator);
        if (!hash_equals($tokenRow['hashed_validator'], $hashedValidator)) {
            $this->deleteRememberTokenBySelector($selector);
            return false;
        }

        return [
            'id'       => $tokenRow['member_id'],
            'username' => $tokenRow['username'],
            'selector' => $selector,
        ];
    }

    /**
     * ลบ token ตาม selector
     */
    public function deleteRememberTokenBySelector($selector)
    {
        $this->db->table('admin_remember_tokens')
            ->where('selector', $selector)
            ->delete();
    }

    /**
     * ลบ token ทั้งหมดของ member
     */
    public function deleteRememberTokensByMember($memberId)
    {
        $this->db->table('admin_remember_tokens')
            ->where('member_id', $memberId)
            ->delete();
    }
}
