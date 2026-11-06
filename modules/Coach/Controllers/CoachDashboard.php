<?php

namespace Modules\Coach\Controllers;

use App\Controllers\BaseController;
use Modules\Coach\Models\CoachModel;
use Modules\Coach\Models\NotificationModel;

class CoachDashboard extends BaseController
{
    protected $coachModel;
    protected $notificationModel;

    public function __construct()
    {
        $this->coachModel = new CoachModel();
        $this->notificationModel = new NotificationModel();
    }

    public function index()
    {
        $session = session();
        $coachId = $session->get('coach_id');

        $data['coach'] = $session->get('coach_data');
        $data['teams'] = $this->coachModel->getCoachTeams($coachId);
        $data['invitations'] = $this->coachModel->getCoachInvitations($coachId);
        $data['certifications'] = $this->coachModel->getCertifications($coachId);
        // print_r($data);
        // die();
        return view('Modules\Coach\Views\dashboard', $data);

    }

    public function profile()
    {
        $session = session();
        $coachId = $session->get('coach_id');
        $data['coach'] = $this->coachModel->getCoachById($coachId);
        $data['data'] = $this->coachModel->getCoachCertificates($coachId);
        $data['organ'] = $this->coachModel->getOrganizations();
        $data['cert_level'] = $this->coachModel->getCertificationLevels();

        return view('Modules\Coach\Views\profile', $data);
    }

    public function updateProfile()
    {
        $session = session();
        $coachId = $session->get('coach_id');
        $input = $this->request->getPost();

        // Handle image upload if provided
        if (!empty($input['image_path']) && strpos($input['image_path'], 'data:image/') === 0) {
            $saveDir = 'public/uploads/coaches/';
            if (!is_dir($saveDir)) {
                mkdir($saveDir, 0755, true);
            }

            $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
            $img = $input['image_path'];

            // Determine file extension from base64 data
            $extension = 'jpg';
            if (strpos($img, 'data:image/png') !== false) {
                $extension = 'png';
                $img = str_replace('data:image/png;base64,', '', $img);
            } elseif (strpos($img, 'data:image/jpeg') !== false) {
                $extension = 'jpg';
                $img = str_replace('data:image/jpeg;base64,', '', $img);
            } elseif (strpos($img, 'data:image/jpg') !== false) {
                $extension = 'jpg';
                $img = str_replace('data:image/jpg;base64,', '', $img);
            } elseif (strpos($img, 'data:image/gif') !== false) {
                $extension = 'gif';
                $img = str_replace('data:image/gif;base64,', '', $img);
            } elseif (strpos($img, 'data:image/webp') !== false) {
                $extension = 'webp';
                $img = str_replace('data:image/webp;base64,', '', $img);
            } else {
                // Fallback - remove any data:image prefix
                $img = preg_replace('#^data:image/[^;]+;base64,#', '', $img);
            }

            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $set_name_img = $random_name . '.' . $extension;
            $file = $saveDir . $set_name_img;
            file_put_contents($file, $data);
            $input['image_path'] = $saveDir . $set_name_img;
        }

        // Handle ID card image upload
        if (!empty($input['id_card_image_base64']) && strpos($input['id_card_image_base64'], 'data:image/') === 0) {
            $saveDir = WRITEPATH . 'secure_uploads/id_cards/';
            if (!is_dir($saveDir)) {
                mkdir($saveDir, 0755, true);
            }

            $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);
            $img = $input['id_card_image_base64'];

            $extension = 'jpg';
            if (strpos($img, 'data:image/png') !== false) {
                $extension = 'png';
                $img = str_replace('data:image/png;base64,', '', $img);
            } elseif (strpos($img, 'data:image/jpeg') !== false || strpos($img, 'data:image/jpg') !== false) {
                $extension = 'jpg';
                $img = preg_replace('#^data:image/jpe?g;base64,#', '', $img);
            } else {
                $img = preg_replace('#^data:image/[^;]+;base64,#', '', $img);
            }

            $img = str_replace(' ', '+', $img);
            $data = base64_decode($img);
            $set_name_img = $random_name . '.' . $extension;
            file_put_contents($saveDir . $set_name_img, $data);
            $input['id_card_image'] = 'secure_uploads/id_cards/' . $set_name_img;
        }

        // Handle ID card image deletion
        if (!empty($input['delete_id_card_image']) && $input['delete_id_card_image'] === '1') {
            $input['id_card_image'] = '';
        }

        // Handle birth date formatting
        if (!empty($input['birth_date'])) {
            $formatDate = str_replace('/', '-', $input['birth_date']);
            $input['birth_date'] = date('Y-m-d', strtotime($formatDate));
        }

        // Clean up non-DB fields before passing to model
        unset($input['id_card_image_base64'], $input['delete_id_card_image']);

        $result = $this->coachModel->updateCoach($coachId, $input);

        if ($result) {
            // Update session data
            $updatedCoach = $this->coachModel->getCoachById($coachId);
            $session->set('coach_data', $updatedCoach);

            return redirect()->to('/coach/profile')->with('success', lang('Common.update_profile_success'));
        } else {
            return redirect()->to('/coach/profile')->with('error', lang('Common.update_profile_error'));
        }
    }

    public function updateCertificates()
    {
        $session = session();
        $coachId = $session->get('coach_id');
        $input = $this->request->getPost();

        // Add coach_id to input (same as admin logic)
        $input['coach_id'] = $coachId;

        // Handle cert image file uploads
        $certImages = [];
        $files = $this->request->getFiles();
        if (!empty($files['cert_image_file'])) {
            $saveDir = 'public/uploads/cert_images/';
            if (!is_dir($saveDir)) {
                mkdir($saveDir, 0755, true);
            }

            foreach ($files['cert_image_file'] as $index => $file) {
                if ($file->isValid() && !$file->hasMoved()) {
                    $newName = $file->getRandomName();
                    $file->move($saveDir, $newName);
                    $certImages[$index] = $saveDir . $newName;
                }
            }
        }

        $result = $this->coachModel->saveCoachCertificates($input, $certImages);

        if ($result) {
            return redirect()->to('/coach/profile')->with('success', lang('Common.update_cert_success'));
        } else {
            return redirect()->to('/coach/profile')->with('error', lang('Common.update_cert_error'));
        }
    }

    public function invitations()
    {
        $session = session();
        $coachId = $session->get('coach_id');
        $data['invitations'] = $this->coachModel->getCoachInvitations($coachId);

        return view('Modules\Coach\Views\invitations', $data);
    }

    public function respondToInvitation()
    {
        $session = session();
        $coachId = $session->get('coach_id');
        $input = $this->request->getPost();

        $invitationId = $input['invitation_id'];
        $response = $input['response']; // 'accepted' or 'rejected'

        $result = $this->coachModel->respondToInvitation($invitationId, $response, $coachId);

        if ($result) {
            $message = $response === 'accepted' ? lang('Common.invitation_accepted_success') : lang('Common.invitation_rejected_success');

            // Update coach data in session if accepted
            if ($response === 'accepted') {
                $updatedCoach = $this->coachModel->getCoachById($coachId);
                $session->set('coach_data', $updatedCoach);
            }

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.invitation_response_error')
            ]);
        }
    }

    public function teams()
    {
        $session = session();
        $coachId = $session->get('coach_id');
        $data['teams'] = $this->coachModel->getCoachTeams($coachId);

        return view('Modules\Coach\Views\teams', $data);
    }

    /**
     * Get notifications for the current coach
     */
    public function getNotifications()
    {
        try {
            $session = session();
            $coachId = $session->get('coach_id');

            if (!$coachId) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Unauthorized'
                ]);
            }

            $limit = (int)($this->request->getGet('limit') ?? 10);
            $offset = (int)($this->request->getGet('offset') ?? 0);

            $notifications = $this->notificationModel->getAllNotifications($coachId, $limit, $offset);
            $unreadCount = (int)$this->notificationModel->getUnreadCount($coachId);

            // เพิ่ม pending coach_invitations (คำเชิญที่ยังไม่ตอบ) เป็น virtual notification
            $db = db_connect();
            $pendingInvites = [];
            if ($db->tableExists('coach_invitations')) {
                $pendingInvites = $db->table('coach_invitations ci')
                    ->select('ci.id, ci.team_id, ci.message, ci.created_at, t.name AS team_name')
                    ->join('team t', 't.id = ci.team_id', 'left')
                    ->where('ci.coach_id', $coachId)
                    ->where('ci.status', 'pending')
                    ->where('ci.is_active', 1)
                    ->orderBy('ci.created_at', 'DESC')
                    ->get()->getResultArray();
            }
            $virtualInvites = [];
            foreach ($pendingInvites as $inv) {
                $virtualInvites[] = [
                    'id'         => 'inv-' . (int)$inv['id'],
                    'type'       => 'coach_invitation_pending',
                    'title'      => lang('Common.coach_invitation_title'),
                    'message'    => lang('Common.coach_invitation_received') . ' ' . ($inv['team_name'] ?? ''),
                    'is_read'    => 0,
                    'created_at' => $inv['created_at'],
                    'invitation_id' => (int)$inv['id'],
                ];
            }

            // นำคำเชิญใหม่ไว้บนสุด รวมกับ notifications เดิม
            $combined = array_merge($virtualInvites, $notifications);
            $totalUnread = $unreadCount + count($virtualInvites);

            return $this->response->setJSON([
                'status' => 'success',
                'data' => [
                    'notifications' => $combined,
                    'unread_count'  => $totalUnread,
                ]
            ]);
        } catch (\Exception $e) {
            log_message('error', 'getNotifications error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    /**
     * Get unread notifications count
     */
    public function getUnreadCount()
    {
        $session = session();
        $coachId = $session->get('coach_id');

        if (!$coachId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $unreadCount = (int)$this->notificationModel->getUnreadCount($coachId);

        $db = db_connect();
        $pendingInvites = 0;
        if ($db->tableExists('coach_invitations')) {
            $pendingInvites = (int)$db->table('coach_invitations')
                ->where('coach_id', $coachId)
                ->where('status', 'pending')
                ->where('is_active', 1)
                ->countAllResults();
        }

        return $this->response->setJSON([
            'status' => 'success',
            'count' => $unreadCount + $pendingInvites,
        ]);
    }

    /**
     * Mark notification as read
     */
    public function markNotificationAsRead()
    {
        $session = session();
        $coachId = $session->get('coach_id');
        $notificationId = $this->request->getPost('notification_id');

        if (!$coachId || !$notificationId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Invalid request'
            ]);
        }

        $result = $this->notificationModel->markAsRead($notificationId, $coachId);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'Notification marked as read'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to mark notification as read'
            ]);
        }
    }

    /**
     * Mark all notifications as read
     */
    public function markAllNotificationsAsRead()
    {
        $session = session();
        $coachId = $session->get('coach_id');

        if (!$coachId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Unauthorized'
            ]);
        }

        $result = $this->notificationModel->markAllAsRead($coachId);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'All notifications marked as read'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'Failed to mark notifications as read'
            ]);
        }
    }

    /**
     * แสดงรายละเอียดสโมสร/ทีมที่ผู้ฝึกสอนดูแล
     */
    public function clubDetails($teamId)
    {
        $session = session();
        $coachId = $session->get('coach_id');

        // ตรวจสอบว่าผู้ฝึกสอนมีสิทธิ์เข้าดูทีมนี้หรือไม่
        if (!$this->coachModel->canAccessTeam($coachId, $teamId)) {
            return redirect()->to('/coach/dashboard')->with('error', lang('Common.no_access_club'));
        }

        // ดึงข้อมูลทีม
        $data['team'] = $this->coachModel->getTeamDetails($teamId);

        if (!$data['team']) {
            return redirect()->to('/coach/dashboard')->with('error', lang('Common.club_not_found'));
        }

        // ดึงข้อมูลผู้เล่นในทีม
        $data['players'] = $this->coachModel->getTeamPlayers($teamId);

        // ดึงข้อมูลผู้ฝึกสอนในทีม
        $data['coaches'] = $this->coachModel->getTeamCoaches($teamId);

        // ข้อมูลผู้ฝึกสอนปัจจุบัน
        $data['current_coach'] = $session->get('coach_data');

        return view('Modules\Coach\Views\club_details', $data);
    }

    // =====================================================
    // Coach Team Join Request Methods
    // =====================================================

    /**
     * หน้าค้นหาทีมเพื่อขอเข้าร่วม
     */
    public function findTeam()
    {
        $session = session();
        $coachId = $session->get('coach_id');

        $data['coach'] = $session->get('coach_data');
        $data['pendingRequestsCount'] = $this->coachModel->getPendingRequestsCount($coachId);

        return view('Modules\Coach\Views\find_team', $data);
    }

    /**
     * AJAX endpoint สำหรับค้นหาทีม
     */
    public function searchTeams()
    {
        try {
            $session = session();
            $coachId = $session->get('coach_id');
            $keyword = $this->request->getGet('keyword');

            if (empty($keyword) || strlen($keyword) < 2) {
                return $this->response->setJSON([]);
            }

            $teams = $this->coachModel->searchTeamsForJoinRequest($coachId, $keyword);
            return $this->response->setJSON($teams);
        } catch (\Exception $e) {
            log_message('error', 'searchTeams error: ' . $e->getMessage());
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()])->setStatusCode(500);
        }
    }

    /**
     * ส่งคำขอเข้าร่วมทีม
     */
    public function submitJoinRequest()
    {
        $session = session();
        $coachId = $session->get('coach_id');

        $teamId = $this->request->getPost('team_id');
        $message = $this->request->getPost('message');
        $role = $this->request->getPost('role_requested') ?? 'assistant_coach';

        if (!$teamId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.please_select_team')
            ]);
        }

        // Check if already has relationship
        $relationship = $this->coachModel->hasExistingRelationship($coachId, $teamId);
        if ($relationship['exists']) {
            $errorMsg = $relationship['type'] === 'member'
                ? lang('Common.already_team_member')
                : lang('Common.already_sent_request');
            return $this->response->setJSON([
                'status' => 'error',
                'message' => $errorMsg
            ]);
        }

        $result = $this->coachModel->createTeamJoinRequest($coachId, $teamId, $message, $role);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => lang('Common.request_sent_successfully')
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.error_sending_request')
            ]);
        }
    }

    /**
     * หน้าดูประวัติคำขอของ coach
     */
    public function myRequests()
    {
        $session = session();
        $coachId = $session->get('coach_id');

        $data['coach'] = $session->get('coach_data');
        $data['requests'] = $this->coachModel->getCoachJoinRequests($coachId);

        return view('Modules\Coach\Views\my_requests', $data);
    }

    /**
     * ยกเลิกคำขอเข้าร่วมทีม
     */
    public function cancelRequest()
    {
        $session = session();
        $coachId = $session->get('coach_id');
        $requestId = $this->request->getPost('request_id');

        if (!$requestId) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.invalid_request')
            ]);
        }

        $result = $this->coachModel->cancelJoinRequest($requestId, $coachId);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => lang('Common.request_cancelled')
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => lang('Common.error_cancelling_request')
            ]);
        }
    }
}