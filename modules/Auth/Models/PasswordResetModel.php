<?php

namespace Modules\Auth\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table = 'password_reset_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['user_id', 'token', 'expires_at', 'used', 'created_at'];

    /**
     * สร้าง token ใหม่สำหรับรีเซ็ตรหัสผ่าน
     */
    public function createToken($userId)
    {
        // ลบ token เก่าที่ยังไม่ได้ใช้ของ user นี้
        $this->where('user_id', $userId)
             ->where('used', 0)
             ->delete();

        // สร้าง token ใหม่
        $token = bin2hex(random_bytes(32));
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // ใช้ query builder แทน insert() เพื่อหลีกเลี่ยง error
        $db = \Config\Database::connect();
        $builder = $db->table($this->table);

        $data = [
            'user_id' => $userId,
            'token' => $token,
            'expires_at' => $expiresAt,
            'used' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];

        $result = $builder->insert($data);

        if ($result) {
            return $token;
        }

        return false;
    }

    /**
     * ตรวจสอบ token ว่ายังใช้งานได้หรือไม่
     */
    public function verifyToken($token)
    {
        $resetToken = $this->where('token', $token)
                           ->where('used', 0)
                           ->where('expires_at >', date('Y-m-d H:i:s'))
                           ->first();

        return $resetToken;
    }

    /**
     * ทำเครื่องหมายว่า token ถูกใช้แล้ว
     */
    public function markAsUsed($token)
    {
        return $this->where('token', $token)
                    ->set(['used' => 1])
                    ->update();
    }

    /**
     * ลบ token ที่หมดอายุและใช้แล้ว
     */
    public function cleanupExpiredTokens()
    {
        $this->where('expires_at <', date('Y-m-d H:i:s'))
             ->orWhere('used', 1)
             ->delete();
    }
}
