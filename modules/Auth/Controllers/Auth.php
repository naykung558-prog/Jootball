<?php

namespace Modules\Auth\Controllers;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\Team\Models\TeamModel;
use Modules\Admin\Models\TournamentModel;
use Modules\Player\Models\PlayerModel;
use Modules\Log\Models\LogModel;
use App\Libraries\LineLogin;

class Auth extends BaseController
{
  protected $userModel;
  protected $teamModel;
  protected $playerModel;
  protected $tournamentModel;

  protected $logLoginModel;

  public function __construct()
  {
    $this->userModel = new UserModel();
    $this->teamModel = new TeamModel();
    $this->playerModel = new PlayerModel();
    $this->tournamentModel = new TournamentModel();
    $this->logLoginModel = new LogModel();
  }

  // public function line_callback()
  // {
  //   $line = new LineLogin();
  //   $callback = $this->request->getGet();
  //   $code = $callback['code'];
  //   $state = $callback['state'];
  //   $token = $line->token($code, $state);
  //   // echo "<pre>";

  //   if (property_exists($token, 'error'))
  //     return redirect()->to('/');

  //   if ($token->id_token) {
  //     $profile = $line->profileFormIdToken($token);
  //     // echo "<pre>" ;

  //     $resp = $line->verify($profile->access_token);
  //     $verify = json_decode($resp);
  //     if (!empty($verify->client_id)) {
  //       $checkuser = $this->userModel->check_uid_line($profile->line_uid);
  //       if (!empty($checkuser)) {
  //         $user = $this->userModel->getOneUser($checkuser['id']);
  //         $team = $this->teamModel->getUserteam($checkuser['id']);
  //       } else {
  //         // save user to database
  //         $this->userModel->saveUser($profile);
  //         $user = $this->userModel->getOneUser('', $profile->line_uid);
  //       }

  //       $ses_data = [
  //         'profile' => $profile,
  //         'uid_line' => $profile->line_uid,
  //         'user_id' => $user['id'],
  //         'team' => $team ?? null,
  //         'isLoggedIn' => true,
  //       ];
  //       session()->set($ses_data);
  //       return redirect()->to('/team/info');
  //     } else {
  //       return redirect()->to('/');
  //     }
  //   }
  // }

  public function line_callback()
  {
    $line = new LineLogin();
    $callback = $this->request->getGet();

    // --- Defensive Check 1: ตรวจสอบว่ามี code และ state ส่งมาหรือไม่ ---
    if (!isset($callback['code']) || !isset($callback['state'])) {
      log_message('error', 'LINE Callback: Missing code or state parameter.');
      return redirect()->to('/')->with('error', lang('Common.line_error_l01'));
    }

    $code = $callback['code'];
    $state = $callback['state'];

    // ตรวจสอบว่าเป็น coach login หรือ user login จาก state
    $stateParts = explode(':', $state, 2);
    $userType = (count($stateParts) === 2) ? $stateParts[0] : 'user';
    $isCoachLogin = ($userType === 'coach');
    $isSyncMode = ($userType === 'sync');

    try {
      // --- ขั้นตอนที่ 1: ขอ Access Token ---
      $token = $line->token($code, $state);

      // --- Defensive Check 2: ตรวจสอบ Error จากการขอ Token ---
      if (property_exists($token, 'error')) {
        log_message('error', 'LINE Token Error: ' . ($token->error_description ?? 'Unknown error'));
        $redirectUrl = $isSyncMode ? '/team/user-manage' : ($isCoachLogin ? '/coach' : '/');
        return redirect()->to($redirectUrl)->with('error', lang('Common.line_error_l02'));
      }

      // --- Defensive Check 3: ตรวจสอบว่ามี id_token มาหรือไม่ ---
      if (empty($token->id_token)) {
        log_message('error', 'LINE Callback: id_token not found in the response.');
        $redirectUrl = $isSyncMode ? '/team/user-manage' : ($isCoachLogin ? '/coach' : '/');
        return redirect()->to($redirectUrl)->with('error', lang('Common.line_error_l03'));
      }

      // --- ขั้นตอนที่ 2: ดึงข้อมูล Profile จาก id_token ---
      $profile = $line->profileFormIdToken($token);

      // --- Defensive Check 4: ตรวจสอบว่ามี line_uid (หรือ sub) หรือไม่ ---
      if (empty($profile->sub) && empty($profile->line_uid)) {
        log_message('error', 'LINE Profile Error: User ID (sub/line_uid) not found in profile data.');
        $redirectUrl = $isSyncMode ? '/team/user-manage' : ($isCoachLogin ? '/coach' : '/');
        return redirect()->to($redirectUrl)->with('error', lang('Common.line_error_l04'));
      }

      $lineUserId = $profile->line_uid ?? $profile->sub;

      // --- ขั้นตอนที่ 3: ตรวจสอบความถูกต้องของ Token (Verify) ---
      $resp = $line->verify($token->access_token);
      $verify = json_decode($resp);

      if (empty($verify->client_id)) {
        log_message('error', 'LINE Access Token Verification Failed.');
        $redirectUrl = $isSyncMode ? '/team/user-manage' : ($isCoachLogin ? '/coach' : '/');
        return redirect()->to($redirectUrl)->with('error', lang('Common.line_error_l05'));
      }

      // --- ขั้นตอนที่ 4: จัดการข้อมูลผู้ใช้ตามประเภท (Sync, Coach หรือ User) ---
      if ($isSyncMode) {
        return $this->handleLineSync($profile, $lineUserId);
      } elseif ($isCoachLogin) {
        return $this->handleCoachLogin($profile, $lineUserId);
      } else {
        return $this->handleUserLogin($profile, $lineUserId);
      }
    } catch (\Exception $e) {
      log_message('error', 'LINE Callback Exception: ' . $e->getMessage());
      $redirectUrl = $isSyncMode ? '/team/user-manage' : ($isCoachLogin ? '/coach' : '/');
      return redirect()->to($redirectUrl)->with('error', lang('Common.line_error_l99') . ' [' . $e->getMessage() . ']');
    }
  }

  private function handleUserLogin($profile, $lineUserId)
  {
    $checkuser = $this->userModel->check_uid_line($lineUserId);

    if (!empty($checkuser)) {
      // ผู้ใช้เก่า: ดึงข้อมูลและสร้าง session
      $user = $this->userModel->getOneUser($checkuser['id']);
      $team = $this->teamModel->getUserteam($checkuser['id']);

      // Download LINE profile image ถ้ายังไม่มี
      if (empty($user['profile_image']) && !empty($profile->picture)) {
        $localPath = $this->downloadLineProfileImage($profile->picture, 'user_' . $lineUserId);
        if ($localPath) {
          $this->userModel->editUser(['id' => $user['id'], 'profile_image' => $localPath]);
          $user['profile_image'] = $localPath;
        }
      }
    } else {
      // ผู้ใช้ใหม่: สร้างบัญชี
      $this->userModel->saveUser($profile, $lineUserId);
      $user = $this->userModel->getOneUserByLineId($lineUserId);
      $team = null;

      // Download LINE profile image สำหรับผู้ใช้ใหม่
      if (!empty($user) && !empty($profile->picture)) {
        $localPath = $this->downloadLineProfileImage($profile->picture, 'user_' . $lineUserId);
        if ($localPath) {
          $this->userModel->editUser(['id' => $user['id'], 'profile_image' => $localPath]);
          $user['profile_image'] = $localPath;
        }
      }
    }

    // --- Defensive Check: ตรวจสอบว่าได้ข้อมูล user จาก DB หรือไม่ ---
    if (empty($user)) {

      $this->logLoginModel->saveLoginLog(
        array(
          'post_data' => json_encode($profile, JSON_UNESCAPED_UNICODE),
          'response_data' => 'error',
          'ip_address' => @$this->request->getIPAddress(),
          'user_id' => null,
          'type' => 'line'
        )
      );

      log_message('critical', 'User login successful via LINE but failed to retrieve/create user record in DB. LINE UID: ' . $lineUserId);
      return redirect()->to('/')->with('error', lang('Common.line_error_l06'));
    }

    // Clear Coach Session Data if exists
    session()->remove(['coach_user_id', 'coach_id', 'coach_data', 'user_type']);

    // สร้าง Session
    $ses_data = [
      'profile'    => $profile,
      'uid_line'   => $lineUserId,
      'user_id'    => $user['id'],
      'team'       => $team,
      'isLoggedIn' => true,
    ];
    session()->set($ses_data);


    $this->logLoginModel->saveLoginLog(
      array(
        'post_data' => json_encode($profile, JSON_UNESCAPED_UNICODE),
        'response_data' => 'success',
        'ip_address' => @$this->request->getIPAddress(),
        'user_id' => $user['id'],
        'type' => 'line'
      )
    );

    // Check for return_url
    if (session()->has('return_url')) {
      $returnUrl = session()->get('return_url');
      session()->remove('return_url');
      return redirect()->to($returnUrl)->with('success', lang('Common.login_success'));
    }

    // Phase 8: ถ้า user มี Academy → redirect ไป Academy dashboard แทน
    $academyUrl = getUserTeamAcademyUrl();
    if ($academyUrl) {
      return redirect()->to($academyUrl)->with('success', lang('Common.login_success'))->with('info', lang('Common.team_redirected_to_academy'));
    }

    return redirect()->to('/academy/info')->with('success', lang('Common.login_success'));
  }

  private function handleLineSync($profile, $lineUserId)
  {
    // ต้อง login อยู่แล้วถึงจะ sync ได้
    $userId = session()->get('user_id');
    if (empty($userId)) {
      return redirect()->to('/auth')->with('error', lang('Common.please_login_first'));
    }

    // ตรวจสอบว่า LINE UID นี้ถูกใช้กับบัญชีอื่นแล้วหรือไม่
    $existingUser = $this->userModel->check_uid_line($lineUserId);
    if (!empty($existingUser) && $existingUser['id'] != $userId) {
      return redirect()->to('/team/user-manage')->with('error', lang('Common.line_already_linked'));
    }

    // อัพเดต uid_line ให้ user ปัจจุบัน
    $updateData = ['id' => $userId, 'uid_line' => $lineUserId];

    // ถ้ายังไม่มีรูปโปรไฟล์ ใช้รูปจาก LINE
    $currentUser = $this->userModel->getOneUser($userId);
    if (empty($currentUser['profile_image']) && !empty($profile->picture)) {
      $localPath = $this->downloadLineProfileImage($profile->picture, 'user_' . $lineUserId);
      if ($localPath) {
        $updateData['profile_image'] = $localPath;
      }
    }

    // ถ้ายังไม่มีชื่อ ใช้ชื่อจาก LINE
    if (empty($currentUser['name']) && !empty($profile->name)) {
      $updateData['name'] = $profile->name;
    }

    $this->userModel->editUser($updateData);

    // อัพเดต session
    session()->set('uid_line', $lineUserId);
    session()->set('profile', $profile);

    return redirect()->to('/team/user-manage')->with('success', lang('Common.line_sync_success'));
  }

  private function handleCoachLogin($profile, $lineUserId)
  {
    $coachUserModel = new \Modules\Coach\Models\CoachUserModel();
    $coachModel = new \Modules\Coach\Models\CoachModel();

    // Check if coach user exists
    $coachUser = $coachUserModel->checkUidLine($lineUserId);
    $isNewUser = empty($coachUser);

    if (!empty($coachUser)) {
      // ตรวจสอบว่ามีรูปใน line_picture แล้วหรือยัง ถ้ามีแล้วไม่ต้อง download อีก
      $localPicturePath = null;
      if (empty($coachUser['line_picture']) && !empty($profile->picture)) {
        $localPicturePath = $this->downloadLineProfileImage($profile->picture, 'coach_' . $lineUserId);
      }

      // อัปเดตข้อมูล LINE ให้เป็นปัจจุบัน
      $updateData = [
        'line_name' => $profile->name ?? $coachUser['line_name'],
      ];

      // อัปเดต line_picture เฉพาะเมื่อมีการ download รูปใหม่
      if ($localPicturePath) {
        $updateData['line_picture'] = $localPicturePath;
      }

      $coachUserModel->updateCoachUser($coachUser['id'], $updateData);
      $coachUserModel->updateLastLogin($coachUser['id']);

      $user = $coachUserModel->getOneUser($coachUser['id']);
      $coach = $coachModel->getCoachByCoachUserId($coachUser['id']);

      // ถ้าไม่มีข้อมูลผู้ฝึกสอน ให้สร้างข้อมูลผู้ฝึกสอนเบื้องต้น
      if (!$coach) {
        $coachData = [
          'coach_user_id' => $user['id'],
          'first_name' => $profile->name ?? lang('Common.not_specified'),
          'last_name' => '',
          'image_path' => null,
          'team_id' => 0,
          'province_id' => 0,
          'amphure_id' => 0,
          'tambon_id' => 0,
          'is_active' => 1,
          'create_date' => date('Y-m-d H:i:s'),
          'update_date' => date('Y-m-d H:i:s')
        ];
        $coachId = $coachModel->insert($coachData);
        $coach = $coachModel->find($coachId);
      }
    } else {
      // ดาวน์โหลดรูปจาก LINE มาเก็บไว้ที่ server สำหรับผู้ใช้ใหม่
      $localPicturePath = null;
      if (!empty($profile->picture)) {
        $localPicturePath = $this->downloadLineProfileImage($profile->picture, 'coach_' . $lineUserId);
      }

      // สร้างบัญชีผู้ใช้ใหม่สำหรับ LINE login
      $coachUserModel->saveUser($profile, $lineUserId);
      $user = $coachUserModel->getOneUserByLineId($lineUserId);
      $coachUserModel->updateLastLogin($user['id']);

      // อัปเดต line_picture ด้วย local path
      if ($localPicturePath) {
        $coachUserModel->updateCoachUser($user['id'], ['line_picture' => $localPicturePath]);
      }

      // ตรวจสอบว่ามีข้อมูลผู้ฝึกสอนหรือไม่
      $coach = $coachModel->getCoachByCoachUserId($user['id']);

      // ถ้าไม่มีข้อมูลผู้ฝึกสอน ให้สร้างข้อมูลผู้ฝึกสอนเบื้องต้น
      if (!$coach) {
        $coachData = [
          'coach_user_id' => $user['id'],
          'first_name' => $profile->name ?? lang('Common.not_specified'),
          'last_name' => '',
          'image_path' => null,
          'team_id' => 0,
          'province_id' => 0,
          'amphure_id' => 0,
          'tambon_id' => 0,
          'is_active' => 1,
          'create_date' => date('Y-m-d H:i:s'),
          'update_date' => date('Y-m-d H:i:s')
        ];
        $coachId = $coachModel->insert($coachData);
        $coach = $coachModel->find($coachId);
      }
    }

    // Refresh coach data เพื่อให้มี line_picture
    $coach = $coachModel->getCoachById($coach['id']);

    // Clear Academy Session Data if exists
    session()->remove(['user_id', 'team']);

    // Create session for coach
    $ses_data = [
      'profile' => $profile,
      'uid_line' => $lineUserId,
      'coach_user_id' => $user['id'],
      'coach_id' => $coach['id'],
      'coach_data' => $coach,
      'user_type' => 'coach',
      'isLoggedIn' => true,
    ];
    session()->set($ses_data);

    $successMessage = $isNewUser ? lang('Common.coach_register_success') : lang('Common.login_success');
    $redirectUrl = $isNewUser ? '/coach' : '/coach/dashboard';
    return redirect()->to($redirectUrl)->with('success', $successMessage);
  }

  private function downloadLineProfileImage($lineImageUrl, $prefix = 'line')
  {
    try {
      if (empty($lineImageUrl)) {
        log_message('debug', 'LINE Profile Image: No URL provided');
        return null;
      }

      // สร้างโฟลเดอร์สำหรับเก็บรูป LINE profile ใน public
      $uploadDir = FCPATH . 'public/uploads/line_profiles/';
      if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
      }

      // ใช้ cURL แทน file_get_contents เพื่อรองรับ SSL ได้ดีกว่า
      $ch = curl_init();
      curl_setopt($ch, CURLOPT_URL, $lineImageUrl);
      curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
      curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
      curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
      curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
      curl_setopt($ch, CURLOPT_TIMEOUT, 30);
      curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; GLO Football/1.0)');

      $imageContent = curl_exec($ch);
      $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      $curlError = curl_error($ch);
      curl_close($ch);

      if ($imageContent === false || $httpCode !== 200) {
        log_message('error', 'Failed to download LINE profile image. HTTP Code: ' . $httpCode . ', Error: ' . $curlError . ', URL: ' . $lineImageUrl);
        return null;
      }

      // สร้างชื่อไฟล์
      $fileName = $prefix . '_' . time() . '.jpg';
      $filePath = $uploadDir . $fileName;

      // บันทึกไฟล์
      if (file_put_contents($filePath, $imageContent)) {
        log_message('debug', 'LINE Profile Image saved: ' . $filePath);
        return 'public/uploads/line_profiles/' . $fileName;
      }

      log_message('error', 'Failed to save LINE profile image to: ' . $filePath);
      return null;
    } catch (\Exception $e) {
      log_message('error', 'Failed to download LINE profile image: ' . $e->getMessage());
      return null;
    }
  }

  public function index()
  {
    $line = new LineLogin();
    $data['encrypter'] = $this->encrypter;
    $data['link'] = $line->getLink();
    $data['tournament_news'] = $this->tournamentModel->getTournamentNews();

    // Check for return_url and save to session
    $returnUrl = $this->request->getGet('return_url');
    if ($returnUrl) {
      session()->set('return_url', $returnUrl);
    }

    return view('Modules\Auth\Views\login', $data);
    // return redirect()->to($data['link']);
  }


  public function logout()
  {
    if (session()->get('frontend_impersonation') && session()->get('isAdminLoggedIn')) {
      session()->remove([
        'profile',
        'uid_line',
        'user_id',
        'team',
        'coach_user_id',
        'coach_id',
        'coach_data',
        'user_type',
        'isLoggedIn',
        'frontend_impersonation',
      ]);
      return redirect()->to(base_url('admin/return-to-admin'))->with('success', 'กลับสู่โหมดผู้ดูแลแล้ว');
    }

    if (isset($_SESSION['profile'])) {
      $profile = $_SESSION['profile'];
      $line = new LineLogin();
      $line->revoke($profile->access_token);
    }
    session_destroy();
    return redirect()->to(base_url('/'));
  }

  // Tournament
  public function tournament_list()
  {
    // if (!session()->get('isLoggedIn')) {
    //   return redirect()->to('/auth?return_url=' . urlencode(current_url()));
    // }

    $line = new LineLogin();
    $data['link'] = $line->getLink();
    $data['encrypter'] = $this->encrypter;

    // Search and Pagination
    $keyword = $this->request->getVar('search') ?? '';
    $page = (int) ($this->request->getVar('page') ?? 1);
    $result = $this->tournamentModel->getTournamentList($keyword, 25, $page);

    // Deduplicate by tour_id (safeguard against LEFT JOIN duplicates from tbl_bank)
    $uniqueTours = [];
    foreach ($result['tournaments'] as $t) {
      $uniqueTours[$t['tour_id']] = $t;
    }
    $data['tournaments'] = array_values($uniqueTours);
    $data['pager'] = $result['pager'];
    $data['search_keyword'] = $keyword;

    // เพิ่มข้อมูลสถานะการลงทะเบียนของทีมสำหรับแต่ละทัวร์นาเมนต์
    $userType = session()->get('user_type');

    // Academy teams + registration map (regular user only)
    $data['academyTeams']     = [];
    $data['registrationsMap'] = []; // [team_id][tour_id] => status|null

    $user_id = session()->get('user_id');
    if (!empty($user_id) && $userType !== 'coach') {
      $academyModel  = new \Modules\Academy\Models\AcademyModel();
      $userAcademies = $academyModel->getAcademyByUser($user_id);

      $allTeams = [];
      foreach ($userAcademies as $academy) {
        $teams = $academyModel->getAcademyTeams($academy['id']);
        foreach ($teams as $team) {
          $team['academy_name'] = $academy['name'];
          $team['academy_logo'] = $academy['logo'];
          $allTeams[$team['id']] = $team;
        }
      }
      $data['academyTeams'] = array_values($allTeams);

      if (!empty($allTeams)) {
        $teamIds = array_keys($allTeams);
        $db = db_connect();

        // Get ALL registrations for all academy teams (not limited to current page)
        $allRegs = $db->table('tournament_team')
          ->select('team_id, tour_id, register_status')
          ->whereIn('team_id', $teamIds)
          ->get()
          ->getResultArray();

        // Build registrationsMap for current page tournaments (for action buttons)
        if (!empty($data['tournaments'])) {
          $tourIds = array_column($data['tournaments'], 'tour_id');
          foreach ($teamIds as $tid) {
            foreach ($tourIds as $trid) {
              $data['registrationsMap'][$tid][$trid] = null;
            }
          }
        }

        // Also build a full map for all registrations
        $allRegistrationsMap = [];
        foreach ($allRegs as $reg) {
          $tid = (int)$reg['team_id'];
          $trid = (int)$reg['tour_id'];
          $status = (int)$reg['register_status'];

          // For current page buttons
          if (isset($data['registrationsMap'][$tid][$trid])) {
            $data['registrationsMap'][$tid][$trid] = $status;
          }

          // For joined tab (all registrations)
          $allRegistrationsMap[$tid][$trid] = $status;
        }

        // Get tournament details for all registered tournaments
        $registeredTourIds = array_unique(array_column($allRegs, 'tour_id'));
        $data['joinedTournaments'] = [];
        if (!empty($registeredTourIds)) {
          $data['joinedTournaments'] = $db->table('tournament')
            ->select('tournament.*, tbl_bank.name as bank_name, tbl_bank.short_name as bank_short_name, tbl_bank.logo as bank_icon')
            ->join('tbl_bank', 'tournament.bank_id = tbl_bank.id', 'LEFT')
            ->whereIn('tournament.tour_id', $registeredTourIds)
            ->orderBy('tournament.tour_id', 'DESC')
            ->get()
            ->getResultArray();
        }

        // Build joined registrations map per team
        $data['joinedRegistrationsMap'] = $allRegistrationsMap;
      }
    }

    // Session-based register_status (only if NO academy teams — for users with personal team or coaches)
    $team_id = session()->get('team.id');
    if (empty($data['academyTeams']) && !empty($team_id)) {
      foreach ($data['tournaments'] as &$tournament) {
        $registration = $this->tournamentModel->getTeamRegistration($tournament['tour_id'], $team_id);
        $tournament['register_status'] = $registration['register_status'] ?? null;
      }
    }
    // If no specific team context, but user is a coach, check all managed teams
    else if ($userType === 'coach') {
      $coachId = session()->get('coach_id');
      if (!empty($coachId)) {
        $coachModel = new \Modules\Coach\Models\CoachModel();
        $managedTeams = $coachModel->getCoachTeams($coachId);

        if (!empty($managedTeams)) {
          foreach ($data['tournaments'] as &$tournament) {
            $hasPendingPayment = false;
            $isRegistered = false;
            $status = null;

            foreach ($managedTeams as $mTeam) {
              $reg = $this->tournamentModel->getTeamRegistration($tournament['tour_id'], $mTeam['id']);
              if (!empty($reg)) {
                $isRegistered = true;
                // Check priority: Waiting Payment (3) > Waiting Approval (0) > Approved (1) > Rejected (2)
                $s = $reg['register_status'];

                // If any team is waiting for payment, show that status
                if ($s == 3) {
                  $status = 3;
                  $hasPendingPayment = true;
                  break;
                }

                // If we don't have a status yet or current is Approved/Rejected, overwrite with Waiting Approval
                if (is_null($status) || ($status == 1 || $status == 2)) {
                  $status = $s;
                }
              }
            }

            if ($isRegistered) {
              $tournament['register_status'] = $status;
            }
          }
        }
      }
    }

    $data['user'] = [
      'user_id' => session()->get('user_id') ?? session()->get('coach_user_id'),
      'team' => session()->get('team'),
      'profile' => session()->get('profile')
    ];
    return view('Modules\Auth\Views\tournament_list', $data);
  }

  public function tournament_detail($id)
  {
    $line = new LineLogin();
    $data['encrypter'] = $this->encrypter;
    $data['link'] = $line->getLink();

    try {
      $tour_id = $this->encrypter->decrypt(hex2bin($id));
    } catch (\Exception $e) {
      throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
    }
    $data['news'] = $this->tournamentModel->getTournamentNews($tour_id);
    $data['teams_in_tournament'] = $this->tournamentModel->get_Tournament_Team($tour_id, false);
    $data['listBracket'] = $this->tournamentModel->getBracketData($tour_id);

    // เพิ่มข้อมูลสถานะการลงทะเบียนของทีมปัจจุบัน
    // เพิ่มข้อมูลสถานะการลงทะเบียนของทีมปัจจุบัน
    $team_id = session()->get('team.id');

    // Prepare available teams
    $availableTeams = [];

    // If user is a coach, fetch all their managed teams
    if (session()->get('user_type') === 'coach') {
      $coachModel = new \Modules\Coach\Models\CoachModel();
      $coachTeams = $coachModel->getCoachTeams(session()->get('coach_id'));
      if (!empty($coachTeams)) {
        $availableTeams = $coachTeams;
      }
    }

    // Load academy teams for regular users
    $user_id = session()->get('user_id');
    $userType = session()->get('user_type');
    if (!empty($user_id) && $userType !== 'coach') {
      $academyModel  = new \Modules\Academy\Models\AcademyModel();
      $userAcademies = $academyModel->getAcademyByUser($user_id);
      foreach ($userAcademies as $academy) {
        $teams = $academyModel->getAcademyTeams($academy['id']);
        foreach ($teams as $t) {
          $availableTeams[] = $t;
        }
      }
    }

    if (!empty($team_id)) {
      $registration = $this->tournamentModel->getTeamRegistration($tour_id, $team_id);
      $data['registration'] = $registration;
      $data['register_status'] = $registration['register_status'] ?? null;

      // Logic for Main/Sub teams relationship (existing logic)
      // Only run this if we don't have teams yet (or if we want to merge, but for coach we prioritize their managed teams)
      // Actually, let's merge if it's a coach who ALSO has a team session?
      // Usually coach session team.id is one of their managed teams.

      if (empty($availableTeams)) {
        $mainTeam = $this->teamModel->get_Teams($team_id);

        if ($mainTeam) {
          // ถ้าเป็นทีมหลัก ให้ดึงทีมรองทั้งหมด
          if (empty($mainTeam['parent_team_id'])) {
            $availableTeams[] = $mainTeam;
            $subTeams = $this->teamModel->getSubTeams($team_id);
            if (!empty($subTeams)) {
              $availableTeams = array_merge($availableTeams, $subTeams);
            }
          } else {
            // ถ้าเป็นทีมรอง ให้ดึงทีมหลักและทีมรองอื่นๆ ด้วย
            $parentTeam = $this->teamModel->get_Teams($mainTeam['parent_team_id']);
            if ($parentTeam) {
              $availableTeams[] = $parentTeam;
              $subTeams = $this->teamModel->getSubTeams($parentTeam['id']);
              if (!empty($subTeams)) {
                $availableTeams = array_merge($availableTeams, $subTeams);
              }
            } else {
              $availableTeams[] = $mainTeam;
            }
          }
        }
      }

      // Remove duplicates based on ID
      $uniqueTeams = [];
      foreach ($availableTeams as $t) {
        $uniqueTeams[$t['id']] = $t;
      }
      $availableTeams = array_values($uniqueTeams);

      // เพิ่มสถานะการลงทะเบียนและข้อมูลสลิปสำหรับแต่ละทีม
      foreach ($availableTeams as &$team) {
        $teamReg = $this->tournamentModel->getTeamRegistration($tour_id, $team['id']);
        $team['tournament_register_status'] = $teamReg['register_status'] ?? null;
        $team['player_list_status'] = $teamReg['player_list_status'] ?? null;
        $team['payment_slip'] = $teamReg['payment_slip'] ?? null;
        $team['registration_document'] = $teamReg['registration_document'] ?? null;
      }

      $data['available_teams'] = $availableTeams;
    } else {
      // If no team_id in session, but we have availableTeams (from coach), we should still process them
      if (!empty($availableTeams)) {
        $uniqueTeams = [];
        foreach ($availableTeams as $t) {
          $uniqueTeams[$t['id']] = $t;
        }
        $availableTeams = array_values($uniqueTeams);

        foreach ($availableTeams as &$team) {
          $teamReg = $this->tournamentModel->getTeamRegistration($tour_id, $team['id']);
          $team['tournament_register_status'] = $teamReg['register_status'] ?? null;
          $team['player_list_status'] = $teamReg['player_list_status'] ?? null;
          $team['payment_slip'] = $teamReg['payment_slip'] ?? null;
          $team['registration_document'] = $teamReg['registration_document'] ?? null;
        }
        $data['available_teams'] = $availableTeams;
        $data['registration'] = []; // Default
        $data['register_status'] = null;
      } else {
        $data['registration'] = [];
        $data['register_status'] = null;
        $data['available_teams'] = [];
      }
    }

    $data['has_payment'] = !empty($data['news']['enable_payment']) &&
        (!empty($data['news']['bank_id']) || !empty($data['news']['account_no']) || !empty($data['news']['promptpay']) || !empty($data['news']['qrcode']));

    $data['has_document'] = !empty($data['news']['enable_document']);

    return view('Modules\Auth\Views\tournament_detail', $data);
  }

  public function register_tournament()
  {
    try {
      $input = $this->request->getPost();

      if (empty($input['id'])) {
        throw new \Exception('Invalid Tournament ID');
      }

      $input['tour_id'] = $this->encrypter->decrypt(hex2bin($input['id']));

      // ใช้ team_id ที่เลือกจากฟอร์ม หรือ fallback ไปยัง session
      $selectedTeamId = !empty($input['team_id']) ? $input['team_id'] : session()->get('team.id');

      $input['teams'] = array(
        'id' => $selectedTeamId,
      );

      // ดึงข้อมูล tournament เพื่อเช็ค enable_payment
      $tournament = $this->tournamentModel->get_Tournament($input['tour_id']);

      $maxFileSize = 10 * 1024 * 1024; // 10MB

      // Handle payment slip upload
      $file = $this->request->getFile('payment_slip');
      if (!empty($tournament['enable_payment'])) {
        // ระบบชำระเงินเปิด - logic เดิม
        if ($file && $file->isValid() && !$file->hasMoved()) {
          if ($file->getSize() > $maxFileSize) {
            return $this->response->setJSON(['status' => 'error', 'msg' => lang('Common.slip_file_size_limit')]);
          }
          $newName = $file->getRandomName();
          $uploadPath = FCPATH . 'public/uploads/slips/';
          if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
          }
          $file->move($uploadPath, $newName);
          $input['payment_slip'] = 'public/uploads/slips/' . $newName;
          $input['register_status'] = 0;
        } else {
          $input['register_status'] = 3;
        }
      } else {
        // ระบบชำระเงินปิด - ไม่ต้องส่ง slip
        $input['register_status'] = 0;
      }

      // Handle registration document upload
      $docFile = $this->request->getFile('registration_document');
      if ($docFile && $docFile->isValid() && !$docFile->hasMoved()) {
        if ($docFile->getSize() > $maxFileSize) {
          return $this->response->setJSON(['status' => 'error', 'msg' => lang('Common.doc_file_size_limit')]);
        }

        // ลบไฟล์เอกสารเดิม (ถ้ามี)
        $existingReg = $this->tournamentModel->getTeamRegistration($input['tour_id'], $selectedTeamId);
        if (!empty($existingReg['registration_document'])) {
          $oldFilePath = FCPATH . $existingReg['registration_document'];
          if (file_exists($oldFilePath)) {
            unlink($oldFilePath);
          }
        }

        $newName = $docFile->getRandomName();
        $uploadPath = FCPATH . 'public/uploads/documents/';
        if (!is_dir($uploadPath)) {
          mkdir($uploadPath, 0777, true);
        }
        $docFile->move($uploadPath, $newName);
        $input['registration_document'] = 'public/uploads/documents/' . $newName;
      }

      $result = $this->tournamentModel->save_tournament_teams($input);
      if ($result == "success") {
        $result = [
          'status' => 'success',
          'msg' => lang('Common.tournament_register_success')
        ];
      } else {
        $result = [
          'status' => 'error',
          'msg' => lang('Common.tournament_register_error')
        ];
      }

      return $this->response->setJSON($result);
    } catch (\Exception $e) {
      log_message('error', '[Register Tournament Error] ' . $e->getMessage());
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'msg' => 'Internal Server Error: ' . $e->getMessage()
      ]);
    }
  }

  public function cancel_tournament_registration()
  {
    try {
      $input = $this->request->getPost();

      if (empty($input['id'])) {
        return $this->response->setJSON(['status' => 'error', 'msg' => 'Invalid Tournament ID']);
      }

      $tour_id = $this->encrypter->decrypt(hex2bin($input['id']));
      $team_id = $input['team_id'] ?? null;

      if (empty($team_id)) {
        return $this->response->setJSON(['status' => 'error', 'msg' => 'Invalid Team ID']);
      }

      // เช็คสถานะการลงทะเบียน
      $teamReg = $this->tournamentModel->getTeamRegistration($tour_id, $team_id);

      if (empty($teamReg)) {
        return $this->response->setJSON(['status' => 'error', 'msg' => lang('Common.registration_not_found')]);
      }

      // ถ้าอนุมัติแล้ว (status 1) ไม่สามารถยกเลิกได้
      if ($teamReg['register_status'] == 1) {
        return $this->response->setJSON(['status' => 'error', 'msg' => lang('Common.cannot_cancel_approved')]);
      }

      // ลบไฟล์สลิปที่อัปโหลดไว้ (ถ้ามี)
      if (!empty($teamReg['payment_slip'])) {
        $slipPath = FCPATH . $teamReg['payment_slip'];
        if (file_exists($slipPath)) {
          unlink($slipPath);
        }
      }

      // ลบไฟล์เอกสาร (ถ้ามี)
      if (!empty($teamReg['registration_document'])) {
        $docPath = FCPATH . $teamReg['registration_document'];
        if (file_exists($docPath)) {
          unlink($docPath);
        }
      }

      // ลบข้อมูลการสมัคร
      $this->tournamentModel->delete_tournament_team($teamReg['tour_team_id']);

      return $this->response->setJSON(['status' => 'success', 'msg' => lang('Common.cancel_registration_success')]);
    } catch (\Exception $e) {
      log_message('error', '[Cancel Tournament Error] ' . $e->getMessage());
      return $this->response->setStatusCode(500)->setJSON([
        'status' => 'error',
        'msg' => lang('Common.generic_error') . ': ' . $e->getMessage()
      ]);
    }
  }

  public function ajax_get_team_details()
  {
    $team_id = $this->request->getPost('team_id');
    $tour_id_encrypted = $this->request->getPost('tour_id');

    if (!$team_id) {
      return $this->response->setJSON(['status' => 'error', 'msg' => 'Team ID is required']);
    }

    $team = $this->teamModel->get_Teams($team_id);

    // Default: No players if filtering is required but fail
    $players = [];

    if ($tour_id_encrypted) {
      try {
        $tour_id = $this->encrypter->decrypt(hex2bin($tour_id_encrypted));

        // Get registration to find tour_team_id
        $registration = $this->tournamentModel->getTeamRegistration($tour_id, $team_id);

        if (!empty($registration) && !empty($registration['tour_team_id'])) {
          // Get submitted player IDs
          $submittedPlayerIds = $this->tournamentModel->getSubmittedPlayers($registration['tour_team_id']);

          if (!empty($submittedPlayerIds)) {
            // Fetch all team players first (or optimize to fetch only specific IDs if Model supports it)
            // Current Model get_PlayerList gets all active players. We filter PHP side for simplicity with existing Model.
            $allPlayers = $this->playerModel->get_PlayerList($team_id);

            // Filter
            $players = array_filter($allPlayers, function ($p) use ($submittedPlayerIds) {
              return in_array($p['id'], $submittedPlayerIds);
            });

            // Re-index array
            $players = array_values($players);
          } else {
            // Submitted list is empty -> Return empty players (Requirement: Show only submitted)
            $players = [];
          }
        }
      } catch (\Exception $e) {
        // Decryption error or other issue
        return $this->response->setJSON(['status' => 'error', 'msg' => 'Invalid Tournament ID']);
      }
    } else {
      // Fallback: If no tour_id provided (legacy behavior or error), maybe show all?
      // But requirement is specific for "Team Details" in tournament context.
      // Let's keep existing behavior if tour_id is missing, OR return empty?
      // Given the specific request is for "Team Details" modal which now sends tour_id,
      // we can default to showing all if tour_id is NOT sent (backward compatibility)
      // OR strict. Let's stick to strict if tour_id is expected.
      // However, to be safe, if no tour_id, we fetch all (original behavior).
      $players = $this->playerModel->get_PlayerList($team_id);
    }

    // Process players to calculate age and format name
    foreach ($players as &$p) {
      // Calculate Age
      $p['age'] = '-';
      if (!empty($p['birth_date']) && $p['birth_date'] != '0000-00-00') {
        try {
          $dob = new \DateTime($p['birth_date']);
          $now = new \DateTime();
          $diff = $now->diff($dob);
          $p['age'] = $diff->y;
        } catch (\Exception $e) {
          $p['age'] = '-';
        }

        // Format Date
        $timestamp = strtotime($p['birth_date']);
        $months = [
          1 => lang('Common.month_1'),
          2 => lang('Common.month_2'),
          3 => lang('Common.month_3'),
          4 => lang('Common.month_4'),
          5 => lang('Common.month_5'),
          6 => lang('Common.month_6'),
          7 => lang('Common.month_7'),
          8 => lang('Common.month_8'),
          9 => lang('Common.month_9'),
          10 => lang('Common.month_10'),
          11 => lang('Common.month_11'),
          12 => lang('Common.month_12'),
        ];
        $month = date('n', $timestamp);
        $isThai = service('request')->getLocale() === 'th';
        $year = $isThai ? (date('Y', $timestamp) + 543) : date('Y', $timestamp);
        $p['birth_date_formatted'] = date('j', $timestamp) . ' ' . $months[$month] . ' ' . $year;
      } else {
        $p['birth_date_formatted'] = '-';
      }

      $p['full_name'] = $p['first_name'] . ' ' . $p['last_name'];
      $p['image_url'] = !empty($p['image_path']) ? base_url($p['image_path']) : base_url('public/img/avatar.png');
    }

    // Get jersey data
    $jerseys = $this->teamModel->getJerseys($team_id);
    $jerseyData = [];
    $jerseyLabels = ['home_set_img' => lang('Common.jersey_home'), 'away_set_img' => lang('Common.jersey_away'), 'home_goal_set_img' => lang('Common.jersey_gk')];
    foreach (['home_set_img', 'away_set_img', 'home_goal_set_img'] as $jType) {
        if (!empty($jerseys[$jType])) {
            $jerseyData[] = [
                'type' => $jType,
                'label' => $jerseyLabels[$jType],
                'url' => base_url($jerseys[$jType])
            ];
        }
    }

    return $this->response->setJSON(['status' => 'success', 'team' => $team, 'players' => $players, 'jerseys' => $jerseyData]);
  }

  public function login_with_account()
  {
    $input = $this->request->getPost();

    // Map login_id (email or username) to username for verification
    if (isset($input['login_id'])) {
      $input['username'] = $input['login_id'];
    } elseif (isset($input['email'])) {
      $input['username'] = $input['email'];
    }

    $user = $this->userModel->verifyUser($input);

    if ($user) {
      $team = $this->teamModel->getUserteam($user['id']);
      // Regenerate session ID to prevent session fixation attacks (critical for super users)
      session()->regenerate(true);
      $ses_data = [
        // 'profile'    => $profile,
        // 'uid_line'   => $lineUserId,
        'user_id'           => $user['id'],
        'team'              => $team,
        'isLoggedIn'        => true,
        'is_super_academy'  => !empty($user['is_super_academy']) ? 1 : 0,
      ];
      session()->set($ses_data);
    } else {
      $this->logLoginModel->saveLoginLog(
        array(
          'post_data' => json_encode($this->request->getPost(), JSON_UNESCAPED_UNICODE),
          'response_data' => 'error',
          'ip_address' => @$this->request->getIPAddress(),
          'user_id' => null,
          'type' => 'web'
        )
      );

      return redirect()->to('/auth')->with('error', lang('Common.invalid_credentials'));
    }
    $this->logLoginModel->saveLoginLog(
      array(
        'post_data' => json_encode($this->request->getPost(), JSON_UNESCAPED_UNICODE),
        'response_data' => 'success',
        'ip_address' => @$this->request->getIPAddress(),
        'user_id' => $user['id'],
        'type' => 'web'
      )
    );

    // Check for return_url
    if (session()->has('return_url')) {
      $returnUrl = session()->get('return_url');
      session()->remove('return_url');
      return redirect()->to($returnUrl)->with('success', lang('Common.login_success'));
    }

    // Super academy operator → ไปหน้าเลือก academy ก่อน (ไม่ต้องผ่าน team/academy detection)
    if (session()->get('is_super_academy')) {
      session()->remove('current_academy_id');    // reset ทุก login เพื่อไม่ให้ติด academy เดิม
      session()->remove('current_academy_name');
      return redirect()->to('/academy/select')->with('success', lang('Common.login_success'));
    }

    // Phase 8: ถ้า user มี Academy → redirect ไป Academy dashboard แทน
    $academyUrl = getUserTeamAcademyUrl();
    if ($academyUrl) {
      return redirect()->to($academyUrl)->with('success', lang('Common.login_success'))->with('info', lang('Common.team_redirected_to_academy'));
    }

    return redirect()->to('/team/info')->with('success', lang('Common.login_success'));
  }

  public function register()
  {
    $line = new LineLogin();
    $data['encrypter'] = $this->encrypter;
    $data['link'] = $line->getLink();
    return view('Modules\Auth\Views\register', $data);
  }

  public function register_post()
  {
    $input = $this->request->getPost();

    // Basic Validation
    if (empty($input['email']) || empty($input['password']) || empty($input['confirm_password'])) {
      return redirect()->back()->withInput()->with('error', lang('Common.input_missing'));
    }

    if (!filter_var($input['email'], FILTER_VALIDATE_EMAIL)) {
      return redirect()->back()->withInput()->with('error', lang('Common.invalid_email'));
    }

    if ($input['password'] !== $input['confirm_password']) {
      return redirect()->back()->withInput()->with('error', lang('Common.password_not_match'));
    }

    // Password Strength Validation
    // At least 8 chars, 1 uppercase, 1 lowercase, 1 number
    if (strlen($input['password']) < 8 || !preg_match('/[A-Z]/', $input['password']) || !preg_match('/[a-z]/', $input['password']) || !preg_match('/[0-9]/', $input['password'])) {
      return redirect()->back()->withInput()->with('error', lang('Common.password_requirements'));
    }

    // Map email to username for legacy compatibility
    $username = $input['email'];

    // Check if username/email already exists
    if ($this->userModel->checkUsername($username)) {
      return redirect()->back()->withInput()->with('error', lang('Common.email_taken'));
    }

    // Cross-check: Check if email exists in Coach Users
    $coachUserModel = new \Modules\Coach\Models\CoachUserModel();
    $existingCoach = $coachUserModel->where('email', $username)->orWhere('username', $username)->first();
    if ($existingCoach) {
      return redirect()->back()->withInput()->with('error', lang('Common.email_taken_coach'));
    }

    // Handle Profile Image Upload
    $profileImagePath = null;
    $file = $this->request->getFile('profile_image');

    if ($file && $file->isValid() && !$file->hasMoved()) {
      // Validate file type
      $validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
      if (!in_array($file->getMimeType(), $validTypes)) {
        return redirect()->back()->withInput()->with('error', lang('Common.upload_image_only'));
      }

      // Validate file size (max 5MB)
      if ($file->getSize() > 5 * 1024 * 1024) {
        return redirect()->back()->withInput()->with('error', lang('Common.file_size_limit_5mb'));
      }

      // Create upload directory if not exists
      $uploadPath = FCPATH . 'public/uploads/profiles/';
      if (!is_dir($uploadPath)) {
        mkdir($uploadPath, 0755, true);
      }

      // Generate unique filename
      $newName = 'profile_' . time() . '_' . uniqid() . '.' . $file->getExtension();

      // Move file to upload directory
      if ($file->move($uploadPath, $newName)) {
        $profileImagePath = 'public/uploads/profiles/' . $newName;
      } else {
        log_message('error', 'Failed to move uploaded file: ' . $file->getErrorString());
      }
    }

    // Save user (map email to username)
    $input['username'] = $username;
    $input['profile_image'] = $profileImagePath;
    $this->userModel->editUser($input);

    return redirect()->to('/auth')->with('success', lang('Common.register_success'));
  }

  /**
   * แสดงฟอร์มลืมรหัสผ่าน
   */
  public function forgot_password()
  {
    return view('Modules\Auth\Views\forgot_password');
  }

  /**
   * ประมวลผลการขอรีเซ็ตรหัสผ่าน
   */
  public function forgot_password_submit()
  {
    $input = $this->request->getPost();
    $usernameOrEmail = $input['username'] ?? '';

    if (empty($usernameOrEmail)) {
      return redirect()->back()->with('error', lang('Common.please_enter_username_or_email'));
    }

    // ค้นหา user จาก username หรือ email
    $user = $this->userModel->where('username', $usernameOrEmail)
      ->orWhere('username', $usernameOrEmail) // email is stored as username
      ->first();

    if (!$user) {
      // เพื่อความปลอดภัย ไม่บอกว่าหา user ไม่เจอ แต่แสดงข้อความเหมือนกันเพื่อป้องกัน user enumeration
      return redirect()->back()->with('success', lang('Common.forgot_password_sent'));
    }

    // สร้าง reset token
    $passwordResetModel = new \Modules\Auth\Models\PasswordResetModel();
    $token = $passwordResetModel->createToken($user['id']);

    // สร้างลิงก์รีเซ็ตรหัสผ่าน
    $resetLink = base_url('auth/reset-password/' . $token);

    // ส่งอีเมล
    try {
      // โหลดการตั้งค่า Email จาก database
      $settingsModel = new \Modules\Admin\Models\SettingsModel();
      $emailSettings = $settingsModel->getSettingsByGroup('email');

      // ตรวจสอบว่ามีการตั้งค่าหรือไม่
      if (empty($emailSettings['email_smtp_user']) || empty($emailSettings['email_smtp_pass'])) {
        log_message('error', 'Email settings not configured properly');
        return redirect()->back()->with('error', lang('Common.email_not_configured'));
      }

      $email = \Config\Services::email();

      // ตั้งค่า Email จาก database
      $email->initialize([
        'protocol' => 'smtp',
        'SMTPHost' => $emailSettings['email_smtp_host'] ?? 'smtp.gmail.com',
        'SMTPUser' => $emailSettings['email_smtp_user'],
        'SMTPPass' => $emailSettings['email_smtp_pass'],
        'SMTPPort' => intval($emailSettings['email_smtp_port'] ?? 587),
        'SMTPCrypto' => $emailSettings['email_smtp_crypto'] ?? 'tls',
        'mailType' => 'html',
        'charset' => 'UTF-8',
        'SMTPTimeout' => 60
      ]);

      $email->setFrom(
        $emailSettings['email_from_email'] ?? 'noreply@jootball.com',
        $emailSettings['email_from_name'] ?? 'Jootball Academy'
      );
      $email->setTo($user['username']); // username is email
      $email->setSubject(lang('Common.reset_password_subject'));

      // สร้าง HTML email template
      $message = $this->getPasswordResetEmailTemplate($user['name'] ?? $user['username'], $resetLink);
      $email->setMessage($message);

      if ($email->send()) {
        return redirect()->back()->with('success', lang('Common.reset_link_sent'));
      } else {
        // Log error for debugging
        log_message('error', 'Failed to send password reset email: ' . $email->printDebugger(['headers']));
        return redirect()->back()->with('error', lang('Common.email_send_failed'));
      }
    } catch (\Exception $e) {
      log_message('error', 'Exception sending password reset email: ' . $e->getMessage());
      return redirect()->back()->with('error', lang('Common.email_send_error'));
    }
  }

  /**
   * สร้าง HTML template สำหรับอีเมลรีเซ็ตรหัสผ่าน
   */
  private function getPasswordResetEmailTemplate($userName, $resetLink)
  {
    return '
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background-color: #16478C; color: white; padding: 20px; text-align: center; border-radius: 5px 5px 0 0; }
        .content { background-color: #f9f9f9; padding: 30px; border: 1px solid #ddd; border-top: none; }
        .button { display: inline-block; padding: 12px 30px; background-color: #16478C; color: white; text-decoration: none; border-radius: 5px; margin: 20px 0; }
        .button:hover { background-color: #0f3366; }
        .footer { text-align: center; padding: 20px; color: #666; font-size: 12px; }
        .warning { background-color: #fff3cd; border: 1px solid #ffc107; padding: 15px; border-radius: 5px; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🔐 รีเซ็ตรหัสผ่าน</h1>
            <p>Jootball Academy</p>
        </div>
        <div class="content">
            <h2>สวัสดีครับ, ' . htmlspecialchars($userName) . '</h2>
            <p>เราได้รับคำขอรีเซ็ตรหัสผ่านสำหรับบัญชีของคุณ หากคุณไม่ได้ทำการขอรีเซ็ตรหัสผ่าน กรุณาเพิกเฉยต่ออีเมลนี้</p>

            <p>คลิกปุ่มด้านล่างเพื่อตั้งรหัสผ่านใหม่:</p>

            <div style="text-align: center;">
                <a href="' . $resetLink . '" class="button text-white">รีเซ็ตรหัสผ่าน</a>
            </div>

            <p>หรือคัดลอกลิงก์ด้านล่างไปวางในเบราว์เซอร์:</p>
            <p style="background-color: #f0f0f0; padding: 10px; border-radius: 3px; word-break: break-all; font-size: 12px;">
                ' . $resetLink . '
            </p>

            <div class="warning">
                <strong>⚠️ สำคัญ:</strong>
                <ul style="margin: 10px 0;">
                    <li>ลิงก์นี้จะหมดอายุใน <strong>1 ชั่วโมง</strong></li>
                    <li>ลิงก์สามารถใช้ได้เพียง <strong>ครั้งเดียว</strong></li>
                    <li>อย่าแชร์ลิงก์นี้กับผู้อื่น</li>
                </ul>
            </div>

            <p>หากคุณมีคำถาม กรุณาติดต่อทีมงาน Jootball Academy</p>
        </div>
        <div class="footer">
            <p>อีเมลนี้ส่งโดยอัตโนมัติ กรุณาอย่าตอบกลับ</p>
            <p>&copy; ' . date('Y') . ' Jootball Academy. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
    ';
  }

  /**
   * แสดงฟอร์มรีเซ็ตรหัสผ่าน
   */
  public function reset_password($token = '')
  {
    if (empty($token)) {
      return redirect()->to('/auth')->with('error', lang('Common.token_invalid'));
    }

    // ตรวจสอบ token
    $passwordResetModel = new \Modules\Auth\Models\PasswordResetModel();
    $resetToken = $passwordResetModel->verifyToken($token);

    if (!$resetToken) {
      return redirect()->to('/auth')->with('error', lang('Common.token_expired'));
    }

    // ดึงข้อมูลผู้ใช้
    $user = $this->userModel->find($resetToken['user_id']);

    $data = [
      'token' => $token,
      'username' => $user['username']
    ];

    return view('Modules\Auth\Views\reset_password', $data);
  }

  /**
   * ประมวลผลการรีเซ็ตรหัสผ่าน
   */
  public function reset_password_submit()
  {
    $input = $this->request->getPost();
    $token = $input['token'] ?? '';
    $password = $input['password'] ?? '';
    $confirmPassword = $input['confirm_password'] ?? '';

    // Validation
    if (empty($token) || empty($password) || empty($confirmPassword)) {
      return redirect()->back()->with('error', lang('Common.please_fill_complete'));
    }

    if ($password !== $confirmPassword) {
      return redirect()->back()->with('error', lang('Common.password_not_match'));
    }

    // Password Strength Validation
    if (strlen($password) < 8 || !preg_match('/[A-Z]/', $password) || !preg_match('/[a-z]/', $password) || !preg_match('/[0-9]/', $password)) {
      return redirect()->back()->with('error', lang('Common.password_requirements'));
    }

    // ตรวจสอบ token
    $passwordResetModel = new \Modules\Auth\Models\PasswordResetModel();
    $resetToken = $passwordResetModel->verifyToken($token);

    if (!$resetToken) {
      return redirect()->to('/auth')->with('error', lang('Common.token_expired'));
    }

    // อัพเดทรหัสผ่านใหม่
    $this->userModel->update($resetToken['user_id'], [
      'password' => password_hash($password, PASSWORD_DEFAULT)
    ]);

    // ทำเครื่องหมายว่า token ถูกใช้แล้ว
    $passwordResetModel->markAsUsed($token);

    return redirect()->to('/auth')->with('success', lang('Common.reset_password_success'));
  }

    public function checkPdpaConsent()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'pdpa_accept' => null
            ]);
        }

        $db = \Config\Database::connect();

        $user = $db->table('user')
                ->select('pdpa_accept')
                ->where('id', $userId)
                ->get()
                ->getRowArray();

        return $this->response->setJSON([
            'pdpa_accept' => $user['pdpa_accept'] ?? 0
        ]);
    }

    public function savePdpaConsent()
    {
        $userId = session()->get('user_id');

        if (!$userId) {
            return $this->response->setJSON([
                'status' => 'error'
            ]);
        }

        $db = \Config\Database::connect();

        $db->table('user')
        ->where('id', $userId)
        ->update([
            'pdpa_accept' => 1,
            'pdpa_accept_datetime' => date('Y-m-d H:i:s')
        ]);

    return $this->response->setJSON([
            'status' => 'success'
        ]);
    }

}
