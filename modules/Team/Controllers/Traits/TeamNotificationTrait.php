<?php

namespace Modules\Team\Controllers\Traits;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\Team\Models\TeamModel;
use Modules\Player\Models\PlayerModel;
use \Hermawan\DataTables\DataTable;
use App\Libraries\Date_thai;
use App\Traits\JerseyManagementTrait;
use App\Traits\PlayerAjaxTrait;
use App\Traits\ChartSelectionTrait;

trait TeamNotificationTrait
{
    // ============================================================
    // Notifications สำหรับเจ้าของทีม / regular user
    // ============================================================

    private function _notificationModel()
    {
        return new \Modules\Coach\Models\NotificationModel();
    }

    public function notifications()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized'])->setStatusCode(401);
        }

        $limit  = (int)($this->request->getGet('limit') ?? 10);
        $offset = (int)($this->request->getGet('offset') ?? 0);

        $model = $this->_notificationModel();
        $items = $model->getAllByUser($userId, $limit, $offset);

        // ผนวก pending team join requests (ทั้งผู้เล่นและโค้ช) เข้าไปด้วย
        $teamId = session()->get('team.id');
        $pending = [];
        if ($teamId) {
            $pending = $this->teamModel->getPendingRequests($teamId);
        }

        return $this->response->setJSON([
            'status' => 'success',
            'data' => [
                'notifications' => $items,
                'unread_count'  => $model->getUnreadCountByUser($userId),
                'pending_requests' => $pending,
                'pending_count'  => count($pending),
            ],
        ]);
    }

    public function notifications_count()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'count' => 0]);
        }
        $model = $this->_notificationModel();
        $unread = (int)$model->getUnreadCountByUser($userId);
        $pendingCount = $this->_countPendingRequestsForUser($userId);
        return $this->response->setJSON([
            'status' => 'success',
            'count' => $unread + $pendingCount,
            'unread_count' => $unread,
            'pending_count' => $pendingCount,
        ]);
    }

    private function _countPendingRequestsForUser($userId)
    {
        $db = db_connect();
        $count = 0;

        $ownedTeamIds = array_map('intval', array_column(
            $db->table('team')->select('id')->where('create_by', $userId)->get()->getResultArray(),
            'id'
        ));
        $ownedAcademyIds = array_map('intval', array_column(
            $db->table('academy_members')->select('academy_id')
                ->where('user_id', $userId)
                ->whereIn('role', ['owner', 'admin'])
                ->where('status', 'approved')
                ->get()->getResultArray(),
            'academy_id'
        ));

        if (!empty($ownedTeamIds)) {
            $count += (int)$db->table('team_managers')
                ->whereIn('team_id', $ownedTeamIds)
                ->where('status', 'pending')
                ->countAllResults();
        }

        if ($db->tableExists('coach_team_requests')) {
            $coachReqTeamIds = $ownedTeamIds;
            if (!empty($ownedAcademyIds)) {
                $atRows = $db->table('academy_teams')->select('team_id')
                    ->whereIn('academy_id', $ownedAcademyIds)->get()->getResultArray();
                foreach ($atRows as $r) $coachReqTeamIds[] = (int)$r['team_id'];
                $coachReqTeamIds = array_values(array_unique($coachReqTeamIds));
            }
            if (!empty($coachReqTeamIds)) {
                $count += (int)$db->table('coach_team_requests')
                    ->whereIn('team_id', $coachReqTeamIds)
                    ->where('status', 'pending')
                    ->where('is_active', 1)
                    ->countAllResults();
            }
        }

        if (!empty($ownedAcademyIds)) {
            $count += (int)$db->table('academy_members')
                ->whereIn('academy_id', $ownedAcademyIds)
                ->where('status', 'pending')
                ->countAllResults();
        }

        return $count;
    }

    public function notifications_mark_read()
    {
        $userId = session()->get('user_id');
        $notificationId = $this->request->getPost('notification_id');
        if (!$userId || !$notificationId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Invalid request']);
        }
        $ok = $this->_notificationModel()->markAsReadByUser($notificationId, $userId);
        return $this->response->setJSON(['status' => $ok ? 'success' : 'error']);
    }

    public function notifications_mark_all_read()
    {
        $userId = session()->get('user_id');
        if (!$userId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Unauthorized']);
        }
        $ok = $this->_notificationModel()->markAllReadByUser($userId);
        return $this->response->setJSON(['status' => $ok ? 'success' : 'error']);
    }
}
