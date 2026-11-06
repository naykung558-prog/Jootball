<?php

namespace Modules\Coach\Models;

use CodeIgniter\Model;

class NotificationModel extends Model
{
    protected $table = "notifications";
    protected $primaryKey = "id";
    protected $allowedFields = ['coach_id', 'user_id', 'type', 'title', 'message', 'data', 'is_read'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    /**
     * Create a notification for invitation
     */
    public function createInvitationNotification($coachId, $teamName, $invitationId)
    {
        $data = [
            'coach_id' => $coachId, // coach.id from coach table
            'type' => 'coach_invitation',
            'title' => 'คำเชิญจากสโมสร',
            'message' => "คุณได้รับคำเชิญจากสโมสร {$teamName} ให้เข้าร่วมในฐานะผู้ฝึกสอน",
            'data' => json_encode([
                'invitation_id' => $invitationId,
                'team_name' => $teamName,
                'action_url' => '/coach/invitations'
            ]),
            'is_read' => 0
        ];

        return $this->insert($data);
    }

    /**
     * Get unread notifications for a coach
     */
    public function getUnreadNotifications($coachId, $limit = 10)
    {
        return $this->where('coach_id', $coachId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Get all notifications for a coach with pagination
     */
    public function getAllNotifications($coachId, $limit = 20, $offset = 0)
    {
        return $this->where('coach_id', $coachId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $coachId = null)
    {
        $builder = $this->where('id', $notificationId);

        if ($coachId) {
            $builder->where('coach_id', $coachId);
        }

        return $builder->set('is_read', 1)
                      ->update();
    }

    /**
     * Mark all notifications as read for a coach
     */
    public function markAllAsRead($coachId)
    {
        return $this->where('coach_id', $coachId)
                    ->where('is_read', 0)
                    ->set('is_read', 1)
                    ->update();
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount($coachId)
    {
        return $this->where('coach_id', $coachId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    /**
     * Delete old notifications (older than X days)
     */
    public function deleteOldNotifications($days = 30)
    {
        $cutoffDate = date('Y-m-d H:i:s', strtotime("-{$days} days"));

        return $this->where('created_at <', $cutoffDate)
                    ->where('is_read', 1)
                    ->delete();
    }

    /**
     * Create a general notification
     */
    public function createNotification($coachId, $type, $title, $message, $data = null)
    {
        $notificationData = [
            'coach_id' => $coachId,
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'data' => $data ? json_encode($data) : null,
            'is_read' => 0
        ];

        return $this->insert($notificationData);
    }

    /**
     * Get notifications by type
     */
    public function getNotificationsByType($coachId, $type, $limit = 10)
    {
        return $this->where('coach_id', $coachId)
                    ->where('type', $type)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    /**
     * Mark invitation-related notification as read when invitation is responded
     */
    public function markInvitationNotificationRead($coachId, $invitationId)
    {
        return $this->where('coach_id', $coachId)
                    ->where('type', 'coach_invitation')
                    ->like('data', '"invitation_id":' . $invitationId)
                    ->set('is_read', 1)
                    ->update();
    }

    /**
     * Get notification by invitation ID
     */
    public function getNotificationByInvitation($coachId, $invitationId)
    {
        return $this->where('coach_id', $coachId)
                    ->where('type', 'coach_invitation')
                    ->like('data', '"invitation_id":' . $invitationId)
                    ->first();
    }

    // ============================================================
    // User notifications (เจ้าของทีม / regular user)
    // ============================================================

    public function createUserNotification($userId, $type, $title, $message, $data = null)
    {
        return $this->insert([
            'user_id'  => $userId,
            'type'     => $type,
            'title'    => $title,
            'message'  => $message,
            'data'     => $data ? json_encode($data, JSON_UNESCAPED_UNICODE) : null,
            'is_read'  => 0,
        ]);
    }

    public function getUnreadByUser($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit)
                    ->findAll();
    }

    public function getAllByUser($userId, $limit = 20, $offset = 0)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('created_at', 'DESC')
                    ->limit($limit, $offset)
                    ->findAll();
    }

    public function getUnreadCountByUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->countAllResults();
    }

    public function markAsReadByUser($notificationId, $userId)
    {
        return $this->where('id', $notificationId)
                    ->where('user_id', $userId)
                    ->set('is_read', 1)
                    ->update();
    }

    public function markAllReadByUser($userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_read', 0)
                    ->set('is_read', 1)
                    ->update();
    }
}