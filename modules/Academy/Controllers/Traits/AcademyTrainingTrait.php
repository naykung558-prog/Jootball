<?php

namespace Modules\Academy\Controllers\Traits;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\Academy\Models\AcademyModel;
use Modules\Player\Models\PlayerModel;
use \Hermawan\DataTables\DataTable;
use App\Libraries\Date_thai;
use Modules\Academy\Controllers\Traits\AcademyAiTrait;
use Modules\Academy\Controllers\Traits\AcademyPlayerTrait;
use Modules\Academy\Controllers\Traits\AcademyIdpTrait;
use Modules\Academy\Controllers\Traits\AcademyReportTrait;
use Modules\Academy\Controllers\Traits\AcademyAssessmentTrait;

trait AcademyTrainingTrait
{
    // =====================================================
    // Training Groups
    // =====================================================

    /**
     * รายการกลุ่มฝึกซ้อม
     */
    public function trainingGroups($academy_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['groups'] = $this->academyModel->getTrainingGroups($academy_id);
        $data['canManage'] = $this->hasPermission($academy_id, 'perm_manage_training_group');
        $data['title'] = lang('Common.training_groups');

        return view('Modules\Academy\Views\training_groups', $data);
    }

    /**
     * ฟอร์มสร้าง/แก้ไขกลุ่มฝึกซ้อม
     */
    public function trainingGroupManage($academy_id, $group_id = null)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_manage_training_group');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['group'] = $group_id ? $this->academyModel->getTrainingGroup($group_id) : null;
        $data['coaches'] = $group_id ? $this->academyModel->getGroupCoaches($group_id) : [];
        $data['players'] = $group_id ? $this->academyModel->getGroupPlayers($group_id) : [];
        $data['availableCoaches'] = $this->academyModel->getAvailableCoaches($academy_id);
        $data['availablePlayers'] = $this->academyModel->getAvailablePlayers($academy_id);
        $data['allPlayers'] = $group_id ? $this->academyModel->getAllPlayersForGroupPicker($academy_id, $group_id) : [];
        $data['canManage'] = true;
        $data['title'] = $group_id ? lang('Common.training_group_edit') : lang('Common.training_group_create');

        return view('Modules\Academy\Views\training_group_manage', $data);
    }

    /**
     * บันทึกกลุ่มฝึกซ้อม
     */
    public function trainingGroupSave()
    {
        $academy_id = $this->request->getPost('academy_id');
        if (!$this->hasPermission($academy_id, 'perm_manage_training_group')) {
            return redirect()->back()->with('error', lang('Common.academy_no_permission'));
        }

        $data = [
            'id'            => $this->request->getPost('id') ?: null,
            'academy_id'    => $academy_id,
            'name'          => $this->request->getPost('name'),
            'description'   => $this->request->getPost('description'),
            'age_range'     => $this->request->getPost('age_range'),
            'max_players'   => $this->request->getPost('max_players') ?: 30,
            'schedule_info' => $this->request->getPost('schedule_info'),
        ];

        $groupId = $this->academyModel->saveTrainingGroup($data);

        if ($groupId) {
            return redirect()->to('/academy/training-group/manage/' . $academy_id . '/' . $groupId)
                ->with('success', lang('Common.training_group_saved'));
        }

        return redirect()->back()->with('error', lang('Common.save_failed'));
    }

    /**
     * ลบกลุ่มฝึกซ้อม
     */
    public function trainingGroupDelete()
    {
        $academy_id = $this->getInput('academy_id');
        $group_id = $this->getInput('group_id');

        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_training_group');
        if ($err) return $err;

        $this->academyModel->deleteTrainingGroup($group_id);
        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.training_group_deleted')]);
    }

    /**
     * เพิ่มโค้ชเข้ากลุ่ม (AJAX)
     */
    public function assignGroupCoach()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_training_group');
        if ($err) return $err;

        $result = $this->academyModel->assignCoachToGroup(
            $this->getInput('group_id'),
            $this->getInput('coach_id'),
            $this->getInput('role') ?: 'assistant'
        );

        // Convert model response format to view format
        return $this->response->setJSON([
            'status' => ($result['success'] ?? false) ? 'success' : 'error',
            'message' => $result['message'] ?? ''
        ]);
    }

    /**
     * ลบโค้ชออกจากกลุ่ม (AJAX)
     */
    public function removeGroupCoach()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_training_group');
        if ($err) return $err;

        $this->academyModel->removeCoachFromGroup(
            $this->getInput('group_id'),
            $this->getInput('coach_id')
        );

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.training_coach_removed')]);
    }

    /**
     * เพิ่มนักเตะเข้ากลุ่ม (AJAX)
     */
    public function assignGroupPlayer()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_training_group');
        if ($err) return $err;

        $result = $this->academyModel->assignPlayerToGroup(
            $this->getInput('group_id'),
            $this->getInput('player_id')
        );

        // Convert model response format to view format
        return $this->response->setJSON([
            'status' => ($result['success'] ?? false) ? 'success' : 'error',
            'message' => $result['message'] ?? ''
        ]);
    }

    /**
     * ลบนักเตะออกจากกลุ่ม (AJAX)
     */
    public function removeGroupPlayer()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_training_group');
        if ($err) return $err;

        $this->academyModel->removePlayerFromGroup(
            $this->getInput('group_id'),
            $this->getInput('player_id')
        );

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.training_player_removed')]);
    }

    /**
     * หน้า Picker เลือกนักเตะเข้า/ออกกลุ่มฝึกซ้อมแบบ multi-select
     */
    public function trainingGroupPlayerManage($academy_id, $group_id)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_manage_training_group');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $group = $this->academyModel->getTrainingGroup($group_id);
        if (!$group) {
            return redirect()->to('/academy/training-groups/' . $academy_id)->with('error', lang('Common.academy_not_found'));
        }

        $data['group'] = $group;
        $data['allPlayers'] = $this->academyModel->getAllPlayersForGroupPicker($academy_id, $group_id);
        $data['canManage']  = true;

        return view('Modules\Academy\Views\training_group_player_manage', $data);
    }

    /**
     * บันทึกการเลือกนักเตะเข้า/ออกกลุ่ม (AJAX JSON)
     */
    public function trainingGroupPlayerSave($academy_id, $group_id)
    {
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_manage_training_group');
        if ($err) return $err;

        $json  = $this->request->getJSON(true);
        $add   = isset($json['add'])    ? array_map('intval', $json['add'])    : [];
        $rem   = isset($json['remove']) ? array_map('intval', $json['remove']) : [];
        $force = !empty($json['force']);

        if (empty($add) && empty($rem)) {
            return $this->response->setJSON(['status' => 'info', 'message' => lang('Common.no_changes')]);
        }

        // ตรวจสอบว่ามีนักเตะที่อยู่ในกลุ่มอื่นอยู่แล้วหรือไม่ (เฉพาะตอนไม่ force)
        if (!$force && !empty($add)) {
            $conflicts = $this->academyModel->checkPlayersInOtherGroups($group_id, $add);
            if (!empty($conflicts)) {
                return $this->response->setJSON([
                    'status'    => 'warning',
                    'conflicts' => $conflicts,
                ]);
            }
        }

        $result = $this->academyModel->updateGroupPlayers($group_id, $add, $rem);

        if ($result['success']) {
            return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.save_success')]);
        }

        return $this->response->setJSON([
            'status'  => 'error',
            'message' => implode(', ', $result['errors']),
        ]);
    }

    // =====================================================
    // Training Sessions
    // =====================================================

    /**
     * รายการ sessions ของ academy
     */
    public function trainingSessions($academy_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $filters = [
            'group_id' => $this->request->getGet('group_id'),
            'month'    => $this->request->getGet('month') ?: date('m'),
            'year'     => $this->request->getGet('year') ?: date('Y'),
            'status'   => $this->request->getGet('status'),
        ];

        $data['sessions'] = $this->academyModel->getAcademyTrainingSessions($academy_id, $filters);
        $sessionIds = array_column($data['sessions'], 'id');
        $data['attachmentCounts'] = $this->academyModel->getAttachmentsForSessions($sessionIds);
        $data['groups'] = $this->academyModel->getTrainingGroups($academy_id);
        $data['filters'] = $filters;
        $data['canManage'] = $this->hasPermission($academy_id, 'perm_create_training_session');
        $data['title'] = lang('Common.training_sessions');

        return view('Modules\Academy\Views\training_sessions', $data);
    }

    /**
     * ฟอร์มสร้าง/แก้ไข session
     */
    public function trainingSessionManage($academy_id, $session_id = null)
    {
        $academy_id = (int)$academy_id;
        if (!$this->canViewAcademy($academy_id)) {
            return redirect()->to(base_url('academy/join'))->with('error', lang('Common.academy_no_permission'));
        }
        if (!$this->hasPermission($academy_id, 'perm_create_training_session')) {
            return redirect()->to(base_url('academy/info/' . $academy_id))->with('error', lang('Common.academy_no_permission'));
        }
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['session'] = $session_id ? $this->academyModel->getTrainingSession($session_id) : null;
        $data['attachments'] = $session_id ? $this->academyModel->getSessionAttachments((int)$session_id) : [];
        $data['groups'] = $this->academyModel->getTrainingGroups($academy_id);
        $data['topics'] = $this->academyModel->getTrainingTopics($academy_id);
        $data['canManage'] = true;
        $data['title'] = $session_id ? lang('Common.training_session_edit') : lang('Common.training_session_create');

        return view('Modules\Academy\Views\training_session_manage', $data);
    }

    /**
     * บันทึก session + แนบเอกสาร (ทำใน transaction)
     */
    public function trainingSessionSave()
    {
        $academy_id = $this->request->getPost('academy_id');
        if (!$this->hasPermission($academy_id, 'perm_create_training_session')) {
            return redirect()->back()->with('error', lang('Common.academy_no_permission'));
        }

        $coach_id = $this->request->getPost('coach_id');
        if (empty($coach_id) && session()->get('user_type') === 'coach') {
            $coachData = session()->get('coach_data');
            $coach_id = $coachData['id'] ?? null;
        }

        $group_id      = $this->request->getPost('group_id');
        $training_date = $this->request->getPost('training_date');
        $title         = $this->request->getPost('title');

        if (empty($group_id) || empty($training_date) || empty($title)) {
            return redirect()->back()->with('error', lang('Common.fill_required'))->withInput();
        }

        $group = $this->academyModel->getTrainingGroup($group_id);
        if (!$group || (int)$group['academy_id'] !== (int)$academy_id) {
            return redirect()->back()->with('error', lang('Common.fill_required'))->withInput();
        }

        $data = [
            'id'            => $this->request->getPost('id') ?: null,
            'academy_id'    => $academy_id,
            'group_id'      => $group_id,
            'coach_id'      => $coach_id,
            'training_date' => $training_date,
            'start_time'    => $this->request->getPost('start_time'),
            'end_time'      => $this->request->getPost('end_time'),
            'title'         => $title,
            'description'   => $this->request->getPost('description'),
            'location'      => $this->request->getPost('location'),
            'training_type' => $this->request->getPost('training_type') ?: 'regular',
            'status'        => $this->request->getPost('status') ?: 'planned',
            'notes'         => $this->request->getPost('notes'),
        ];

        // ---------- Pre-transaction: validate uploads ----------
        $uploadedFiles = $this->request->getFiles();
        $rawFiles = $uploadedFiles['attachments'] ?? [];
        if (!is_array($rawFiles)) $rawFiles = [$rawFiles];
        $validFiles = array_filter($rawFiles, function ($f) {
            return $f instanceof \CodeIgniter\HTTP\Files\UploadedFile
                && $f->isValid()
                && $f->getError() === UPLOAD_ERR_OK;
        });

        // Capture trusted metadata BEFORE move (getName changes post-move in CI4 4.7)
        $fileMeta = [];
        foreach ($validFiles as $f) {
            // Trusted MIME via finfo (not browser-supplied)
            $trustedMime = $f->getMimeType();
            if (!in_array($trustedMime, self::TRAINING_ATTACH_ALLOWED_MIME, true)) {
                return redirect()->back()
                    ->with('error', lang('Common.training_attach_error_mime', ['name' => $f->getClientName()]))
                    ->withInput();
            }
            $size = $f->getSize();
            if ($size > self::TRAINING_ATTACH_MAX_SIZE_MB * 1024 * 1024) {
                return redirect()->back()
                    ->with('error', lang('Common.training_attach_error_size', ['name' => $f->getClientName()]))
                    ->withInput();
            }
            // Safe extension: prefer finfo-guessed, fallback to sanitized client ext, then 'bin'
            $ext = $f->guessExtension();
            if (empty($ext)) {
                $ext = preg_replace('/[^a-zA-Z0-9]/', '', (string)$f->getClientExtension());
            }
            if (empty($ext)) $ext = 'bin';

            $fileMeta[] = [
                'file'          => $f,
                'original_name' => $f->getClientName(),
                'trusted_mime'  => $trustedMime,
                'ext'           => $ext,
                'size'          => $size,
            ];
        }

        // ---------- Transaction: count check + session save + file move + insert ----------
        $this->db->transBegin();

        // Count check INSIDE transaction to prevent race condition (concurrent submits)
        $existingCount = !empty($data['id'])
            ? $this->academyModel->countSessionAttachments((int)$data['id'])
            : 0;
        if (count($fileMeta) + $existingCount > self::TRAINING_ATTACH_MAX_FILES) {
            $this->db->transRollback();
            return redirect()->back()->with('error', lang('Common.training_attach_error_count'))->withInput();
        }

        $sessionId = $this->academyModel->saveTrainingSession($data);
        if (!$sessionId) {
            $this->db->transRollback();
            return redirect()->back()->with('error', lang('Common.save_failed'));
        }

        $movedPaths = [];
        if (!empty($fileMeta)) {
            $uploadSubPath  = 'uploads/training_sessions/' . date('Y') . '/' . date('m') . '/';
            $uploadFullPath = FCPATH . $uploadSubPath;
            if (!is_dir($uploadFullPath) && !@mkdir($uploadFullPath, 0755, true)) {
                log_message('error', 'Failed to create upload dir: ' . $uploadFullPath);
                $this->db->transRollback();
                return redirect()->back()->with('error', lang('Common.save_failed'));
            }

            $mainModel = new \App\Models\Main_Model();
            $insertRows = [];
            foreach ($fileMeta as $meta) {
                $f = $meta['file'];
                $newName = $mainModel->generateUniqueFilename($meta['ext']);
                try {
                    $moved = $f->move($uploadFullPath, $newName);
                    if ($moved === false) {
                        throw new \RuntimeException('UploadedFile::move returned false');
                    }
                } catch (\Exception $e) {
                    log_message('error', 'Attach move failed: ' . $e->getMessage());
                    foreach ($movedPaths as $p) { if (is_file($p)) @unlink($p); }
                    $this->db->transRollback();
                    return redirect()->back()->with('error', lang('Common.save_failed'));
                }
                $movedPaths[] = $uploadFullPath . $newName;
                $insertRows[] = [
                    'attach_name'      => $meta['original_name'],
                    'attach_file'      => $uploadSubPath . $newName,
                    'attach_file_type' => $meta['trusted_mime'],
                    'attach_size'      => round($meta['size'] / (1024 * 1024), 2),
                ];
            }

            // Determine uploader identity
            $created_by = null;
            $created_by_type = 'user';
            if (session()->get('user_type') === 'coach') {
                $coachData = session()->get('coach_data');
                $created_by = $coachData['coach_user_id'] ?? null;
                $created_by_type = 'coach';
            } else {
                $created_by = session()->get('user_id') ?: session()->get('member_id');
                $created_by_type = 'user';
            }

            $this->academyModel->insertSessionAttachments(
                (int)$sessionId,
                (int)$academy_id,
                $insertRows,
                $created_by ? (int)$created_by : null,
                $created_by_type
            );
        }

        if ($this->db->transStatus() === false) {
            foreach ($movedPaths as $p) { if (is_file($p)) @unlink($p); }
            $this->db->transRollback();
            return redirect()->back()->with('error', lang('Common.save_failed'));
        }
        $this->db->transCommit();

        return redirect()->to('/academy/training-session/detail/' . $academy_id . '/' . $sessionId)
            ->with('success', lang('Common.training_session_saved'));
    }

    /**
     * รายละเอียด session + attendance
     */
    public function trainingSessionDetail($academy_id, $session_id)
    {
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $session = $this->academyModel->getTrainingSession($session_id);
        if (!$session) {
            return redirect()->to('/academy/training-sessions/' . $academy_id)->with('error', lang('Common.academy_not_found'));
        }

        $data['session'] = $session;
        $data['attendance'] = $this->academyModel->getSessionAttendance($session_id);
        $data['groupPlayers'] = $this->academyModel->getGroupPlayers($session['group_id']);
        $data['attachments'] = $this->academyModel->getSessionAttachments((int)$session_id);
        $data['canManage'] = $this->hasPermission($academy_id, 'perm_create_training_session');
        $data['title'] = lang('Common.training_session_detail');

        return view('Modules\Academy\Views\training_session_detail', $data);
    }

    /**
     * หน้าเช็คชื่อ (Attendance)
     */
    public function trainingAttendance($academy_id, $session_id)
    {
        $academy_id = (int)$academy_id;
        if (!$this->canViewAcademy($academy_id)) {
            return redirect()->to(base_url('academy/join'))->with('error', lang('Common.academy_no_permission'));
        }
        if (!$this->hasPermission($academy_id, 'perm_save_attendance')) {
            return redirect()->to(base_url('academy/info/' . $academy_id))->with('error', lang('Common.academy_no_permission'));
        }
        $access = $this->ensureAcademyAccess($academy_id);
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $session = $this->academyModel->getTrainingSession($session_id);
        if (!$session) {
            return redirect()->to('/academy/training-sessions/' . $academy_id)->with('error', lang('Common.academy_not_found'));
        }

        $data['session'] = $session;
        $data['attendance'] = $this->academyModel->getSessionAttendance($session_id);
        $data['groupPlayers'] = $this->academyModel->getGroupPlayers($session['group_id']);
        $data['attachments'] = $this->academyModel->getSessionAttachments((int)$session_id);
        $data['canManage'] = true;
        $data['title'] = lang('Common.training_attendance');

        return view('Modules\Academy\Views\training_attendance', $data);
    }

    /**
     * บันทึก attendance (AJAX)
     */
    public function saveAttendance()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_save_attendance');
        if ($err) return $err;

        $session_id = $this->getInput('session_id');
        $attendanceData = $this->getInput('attendance') ?: [];

        // ดึง coach_id ของ current user
        $recorded_by = null;
        if (session()->get('user_type') === 'coach') {
            $coachData = session()->get('coach_data');
            $recorded_by = $coachData['id'] ?? null;
        }

        $this->academyModel->saveAttendance($session_id, $attendanceData, $recorded_by);

        // อัพเดต session status เป็น completed ถ้ายังเป็น planned
        $session = $this->academyModel->getTrainingSession($session_id);
        if ($session && $session['status'] === 'planned') {
            $this->academyModel->saveTrainingSession([
                'id' => $session_id,
                'status' => 'completed'
            ]);
        }

        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.training_attendance_saved')]);
    }

    /**
     * ลบ session
     */
    public function trainingSessionDelete()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_create_training_session');
        if ($err) return $err;

        $this->academyModel->deleteTrainingSession($this->getInput('session_id') ?: $this->getInput('id'));
        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.training_session_deleted')]);
    }

    /**
     * AJAX: Soft delete an attachment
     */
    public function deleteSessionAttachment()
    {
        $attachment_id = (int) $this->getInput('attachment_id');
        if (!$attachment_id) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.invalid_request')]);
        }

        $attach = $this->academyModel->getAttachmentById($attachment_id);
        if (!$attach) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.not_found')]);
        }

        if (!$this->hasPermission((int)$attach['academy_id'], 'perm_create_training_session')) {
            return $this->response->setJSON(['status' => 'error', 'message' => lang('Common.academy_no_permission')]);
        }

        $this->academyModel->deleteSessionAttachment($attachment_id);

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => lang('Common.training_attach_deleted'),
        ]);
    }

    /**
     * Download or preview an attachment
     */
    public function downloadSessionAttachment($attachment_id)
    {
        $attach = $this->academyModel->getAttachmentById((int)$attachment_id);
        if (!$attach || !$attach['is_active']) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        if (!$this->canViewAcademy((int)$attach['academy_id'])) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $filePath = FCPATH . ltrim($attach['attach_file'], '/');
        if (!is_file($filePath)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
        }

        $inlineTypes = ['application/pdf', 'image/jpeg', 'image/png', 'image/jpg'];
        $disposition = in_array($attach['attach_file_type'], $inlineTypes) ? 'inline' : 'attachment';

        return $this->response
            ->setHeader('Content-Type', $attach['attach_file_type'])
            ->setHeader('Content-Disposition', $disposition . '; filename="' . $attach['attach_name'] . '"')
            ->setBody(file_get_contents($filePath));
    }

    /**
     * AJAX: ดึง events สำหรับ FullCalendar
     * GET /academy/training-session/events/{academy_id}?start=&end=&group_id=
     */
    public function trainingSessionEvents($academy_id)
    {
        if (!$this->canViewAcademy($academy_id)) {
            return $this->response->setJSON([]);
        }

        $start    = substr($this->request->getGet('start') ?? date('Y-m-01'), 0, 10);
        $end      = substr($this->request->getGet('end')   ?? date('Y-m-t'),  0, 10);
        $group_id = $this->request->getGet('group_id');

        $builder = $this->db->table('academy_training_sessions s')
            ->select("s.id, s.title, s.training_date, s.start_time, s.end_time,
                      s.training_type, s.status, s.location, s.notes,
                      g.name as group_name, g.id as group_id,
                      CONCAT(c.first_name, ' ', c.last_name) as coach_name", false)
            ->join('academy_training_groups g', 'g.id = s.group_id', 'left')
            ->join('coach c', 'c.id = s.coach_id', 'left')
            ->where('s.academy_id', (int)$academy_id)
            ->where('s.training_date >=', $start)
            ->where('s.training_date <=', $end);

        if (!empty($group_id)) {
            $builder->where('s.group_id', (int)$group_id);
        }

        $sessions = $builder->orderBy('s.training_date', 'ASC')
            ->orderBy('s.start_time', 'ASC')
            ->get()->getResultArray();

        $typeColors = [
            'regular' => '#0d6efd',
            'special' => '#fd7e14',
            'match'   => '#198754',
            'fitness' => '#0dcaf0',
        ];

        $sessionIds = array_column($sessions, 'id');
        $attachmentCounts = $this->academyModel->getAttachmentsForSessions($sessionIds);

        $events = [];
        foreach ($sessions as $s) {
            $color = $typeColors[$s['training_type'] ?? 'regular'] ?? '#6c757d';
            if (($s['status'] ?? '') === 'cancelled') {
                $color = '#adb5bd';
            }

            $events[] = [
                'id'              => (int)$s['id'],
                'title'           => $s['group_name'] ?? '-',
                'start'           => $s['training_date'] . 'T' . ($s['start_time'] ?: '00:00:00'),
                'end'             => $s['training_date'] . 'T' . ($s['end_time']   ?: '23:59:00'),
                'backgroundColor' => $color,
                'borderColor'     => $color,
                'extendedProps'   => [
                    'session_id'       => (int)$s['id'],
                    'session_title'    => $s['title'],
                    'group_name'       => $s['group_name'] ?? '-',
                    'group_id'         => $s['group_id'],
                    'coach_name'       => trim($s['coach_name'] ?? '') ?: '-',
                    'location'         => $s['location'] ?? '',
                    'training_type'    => $s['training_type'] ?? 'regular',
                    'status'           => $s['status'] ?? 'planned',
                    'notes'            => $s['notes'] ?? '',
                    'training_date'    => $s['training_date'],
                    'start_time'       => substr($s['start_time'] ?? '00:00', 0, 5),
                    'end_time'         => substr($s['end_time']   ?? '00:00', 0, 5),
                    'attachment_count' => (int)($attachmentCounts[(int)$s['id']] ?? 0),
                ],
            ];
        }

        return $this->response->setJSON($events);
    }

    /**
     * Duplicate a training session to a new date
     * POST /academy/training-session/duplicate
     */
    public function trainingSessionDuplicate()
    {
        $input      = $this->getInput();
        $academy_id = $input['academy_id'] ?? 0;
        $session_id = (int)($input['session_id'] ?? 0);
        $new_date   = $input['new_date'] ?? '';

        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_duplicate_session');
        if ($err) return $err;

        if (!$session_id || !$new_date) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
        }

        $parsed = date_create($new_date);
        if (!$parsed) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'วันที่ไม่ถูกต้อง']);
        }

        $src = $this->db->table('academy_training_sessions')
            ->where('id', $session_id)
            ->where('academy_id', $academy_id)
            ->get()->getRowArray();

        if (!$src) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบแผนการฝึกที่ต้องการคัดลอก']);
        }

        $newData = [
            'academy_id'    => $src['academy_id'],
            'group_id'      => $src['group_id'],
            'coach_id'      => $src['coach_id'],
            'training_date' => date('Y-m-d', strtotime($new_date)),
            'start_time'    => $src['start_time'],
            'end_time'      => $src['end_time'],
            'title'         => $src['title'],
            'description'   => $src['description'],
            'location'      => $src['location'],
            'training_type' => $src['training_type'],
            'status'        => 'planned',
            'notes'         => '',
        ];

        $newId = $this->academyModel->saveTrainingSession($newData);

        if (!$newId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่สามารถบันทึกได้']);
        }

        return $this->response->setJSON([
            'status'       => 'success',
            'message'      => 'คัดลอกแผนการฝึกสำเร็จ',
            'new_id'       => $newId,
            'redirect_url' => base_url('academy/training-session/detail/' . $academy_id . '/' . $newId),
        ]);
    }

    /**
     * Duplicate ALL sessions of a given day to a new date
     * POST /academy/training-session/duplicate-day
     */
    public function trainingSessionDuplicateDay()
    {
        $input       = $this->getInput();
        $academy_id  = $input['academy_id']  ?? 0;
        $source_date = $input['source_date'] ?? '';
        $new_date    = $input['new_date']    ?? '';

        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_duplicate_session');
        if ($err) return $err;

        if (!$source_date || !$new_date) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
        }

        $srcParsed = date_create($source_date);
        $newParsed = date_create($new_date);
        if (!$srcParsed || !$newParsed) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'วันที่ไม่ถูกต้อง']);
        }

        $sessions = $this->db->table('academy_training_sessions')
            ->where('academy_id', (int)$academy_id)
            ->where('training_date', date('Y-m-d', strtotime($source_date)))
            ->get()->getResultArray();

        if (empty($sessions)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบแผนการฝึกในวันนั้น']);
        }

        $targetDate = date('Y-m-d', strtotime($new_date));
        $count = 0;
        foreach ($sessions as $src) {
            $newData = [
                'academy_id'    => $src['academy_id'],
                'group_id'      => $src['group_id'],
                'coach_id'      => $src['coach_id'],
                'training_date' => $targetDate,
                'start_time'    => $src['start_time'],
                'end_time'      => $src['end_time'],
                'title'         => $src['title'],
                'description'   => $src['description'],
                'location'      => $src['location'],
                'training_type' => $src['training_type'],
                'status'        => 'planned',
                'notes'         => '',
            ];
            $this->academyModel->saveTrainingSession($newData);
            $count++;
        }

        return $this->response->setJSON([
            'status'  => 'success',
            'message' => "คัดลอก {$count} แผนสำเร็จ",
            'count'   => $count,
        ]);
    }

    // =====================================================
    // Training Topics
    // =====================================================

    public function trainingTopics($academy_id)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_create_training_topic');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['topics'] = $this->academyModel->getTrainingTopics($academy_id);
        $data['title'] = lang('Common.training_topics');

        return view('Modules\Academy\Views\training_topics', $data);
    }

    public function trainingTopicManage($academy_id, $topic_id = null)
    {
        $access = $this->ensurePermAccess($academy_id, 'perm_create_training_topic');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];
        $data = $access['data'];

        $data['topic'] = $topic_id ? $this->academyModel->getTrainingTopic($topic_id) : null;
        $data['title'] = $topic_id ? lang('Common.training_topic_edit') : lang('Common.training_topic_create');

        return view('Modules\Academy\Views\training_topic_manage', $data);
    }

    public function trainingTopicSave()
    {
        $academy_id = $this->request->getPost('academy_id');
        $access = $this->ensurePermAccess($academy_id, 'perm_create_training_topic');
        if ($access['redirect']) return $access['redirect'];
        $academy_id = $access['academy_id'];

        $name = trim($this->request->getPost('name') ?? '');
        if (empty($name)) {
            return redirect()->back()->with('error', lang('Common.fill_required'))->withInput();
        }

        $data = [
            'id'          => $this->request->getPost('id') ?: null,
            'academy_id'  => $academy_id,
            'name'        => $name,
            'description' => $this->request->getPost('description'),
            'sort_order'  => (int)($this->request->getPost('sort_order') ?? 0),
            'is_active'   => 1,
        ];

        $this->academyModel->saveTrainingTopic($data);

        return redirect()->to('/academy/training-topics/' . $academy_id)
            ->with('success', lang('Common.training_topic_saved'));
    }

    public function trainingTopicDelete()
    {
        $academy_id = $this->getInput('academy_id');
        $err = $this->ensureAjaxPermAccess($academy_id, 'perm_delete_topic');
        if ($err) return $err;

        $topic_id = $this->getInput('topic_id');
        $this->academyModel->deleteTrainingTopic($topic_id);
        return $this->response->setJSON(['status' => 'success', 'message' => lang('Common.training_topic_deleted')]);
    }
}
