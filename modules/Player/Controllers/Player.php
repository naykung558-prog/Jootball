<?php

namespace Modules\Player\Controllers;

use App\Controllers\BaseController;
use Modules\User\Models\UserModel;
use Modules\Player\Models\PlayerModel;
use \Hermawan\DataTables\DataTable;
use Modules\Player\Models\ProvinceModel;
use Modules\Player\Models\DistrictModel;
use Modules\Player\Models\SubdistrictModel;
use Modules\Team\Models\TeamModel;

class Player extends BaseController
{
    protected $playerModel;
    protected $teamModel;
    protected $db;

    public function __construct()
    {
        $this->playerModel = new PlayerModel;
        $this->teamModel = new TeamModel;
        $this->db = \Config\Database::connect();
    }

   public function report_people($id = '')
    {
            $data['position'] = $this->playerModel->player_sub_position();
            $data['position_name_label'] = [];
            //die(print_r($data));

            // ดึงข้อมูลจังหวัด
            $provinceModel = new ProvinceModel();
            $data['provinces'] = $provinceModel->orderBy('province_name', 'ASC')->findAll();

            if ($id) {
                $id = $this->encrypter->decrypt(hex2bin($id)); // การถอดรหัส
                $data['player'] = $this->playerModel->get_Player($id);
                $position_name_label = $this->playerModel->player_sub_position(json_decode($data['player']['player_sub_position_id']));
                foreach($position_name_label as $kk=>$dd) {
                    $data['position_name_label'][] = $dd['name_en'] != '' ? $dd['name_en'] : $dd['name_th'];
                }
                // ดึงข้อมูลอำเภอ (amphure)
                if (!empty($data['player']['amphure_id'])) {
                    $districtModel = new DistrictModel();
                    $district = $districtModel->find($data['player']['amphure_id']);
                    if ($district) {
                        $data['player']['amphure_name'] = $district['district_name']; // เก็บชื่ออำเภอใน player
                    }
                }

                // หาชื่อจังหวัด
                if (!empty($data['player']['province_id']) && !empty($data['provinces'])) {
                    foreach ($data['provinces'] as $province) {
                        if ($province['province_id'] == $data['player']['province_id']) {
                            $data['player']['province_name'] = $province['province_name'];
                            break;
                        }
                    }
                }

                // ดึงข้อมูลตำบล (tambon)
                if (!empty($data['player']['amphure_id'])) {
                    $subdistrictModel = new SubdistrictModel();
                    $data['tambons'] = $subdistrictModel->where('district_id', $data['player']['amphure_id'])->findAll();

                    // ตรวจสอบว่าเลือกตำบลแล้วหรือไม่
                    if (!empty($data['player']['tambon_id'])) {
                        $tambon = $subdistrictModel->find($data['player']['tambon_id']);
                        if ($tambon) {
                            $data['player']['tambon_name'] = $tambon['subdistrict_name']; // เก็บชื่อตำบลใน player
                        }
                    }
                }

                // ตรวจสอบว่าเลือกตำบลแล้วหรือไม่
                if (!empty($data['player']['tambon_id'])) {
                    $tambon = $subdistrictModel->find($data['player']['tambon_id']);
                    if ($tambon) {
                        $data['player']['tambon_name'] = $tambon['subdistrict_name']; // เก็บชื่อตำบลใน player
                    }
                }
            } else {
                $data['player'] = [];
                $data['amphures'] = [];
                $data['tambons'] = [];
            }
            // echo"<pre>";
        return view("Modules\Player\Views\people.php" ,$data);
    }
public function manage($id = '')
{
    $data['position'] = $this->playerModel->player_sub_position();
    $data['positions'] = $this->playerModel->player_position();

    // ดึงข้อมูลจังหวัด
    $provinceModel = new ProvinceModel();
    $data['provinces'] = $provinceModel->orderBy('province_name', 'ASC')->findAll();

    // รับ academy_id จาก query string (กรณีมาจากหน้า Academy)
    $data['academy_id'] = (int) ($this->request->getGet('academy_id') ?? 0);

    if ($id) {
        $id = $this->encrypter->decrypt(hex2bin($id)); // การถอดรหัส
        $data['player'] = $this->playerModel->get_Player($id);
        $data['player_teams'] = $this->playerModel->get_PlayerTeams((int)$id);

        // ดึงข้อมูลอำเภอ (amphure)
        if (!empty($data['player']['amphure_id'])) {
            $districtModel = new DistrictModel();
            $district = $districtModel->find($data['player']['amphure_id']);
            if ($district) {
                $data['player']['amphure_name'] = $district['district_name']; // เก็บชื่ออำเภอใน player
            }
        }

        // ดึงข้อมูลตำบล (tambon)
        if (!empty($data['player']['amphure_id'])) {
            $subdistrictModel = new SubdistrictModel();
            $data['tambons'] = $subdistrictModel->where('district_id', $data['player']['amphure_id'])->findAll();

            // ตรวจสอบว่าเลือกตำบลแล้วหรือไม่
            if (!empty($data['player']['tambon_id'])) {
                $tambon = $subdistrictModel->find($data['player']['tambon_id']);
                if ($tambon) {
                    $data['player']['tambon_name'] = $tambon['subdistrict_name']; // เก็บชื่อตำบลใน player
                }
            }
        }
    } else {
        $data['player'] = [];
        $data['player_teams'] = [];
        $data['amphures'] = [];
        $data['tambons'] = [];
    }
    return view("Modules\Player\Views\manage.php", $data);
}

public function manage_by_team($teamId = '')
{
    $data['position'] = $this->playerModel->player_sub_position();
    $data['positions'] = $this->playerModel->player_position();

    // ดึงข้อมูลจังหวัด
    $provinceModel = new ProvinceModel();
    $data['provinces'] = $provinceModel->orderBy('province_name', 'ASC')->findAll();

    $data['player'] = [];
    $data['amphures'] = [];
    $data['tambons'] = [];
    $data['team_id'] = $teamId;

    return view("Modules\Player\Views\manage.php", $data);
}

public function manage_coach($id = '')
{

// ดึงข้อมูลจังหวัด
$provinceModel = new ProvinceModel();
$data['provinces'] = $provinceModel->orderBy('province_name', 'ASC')->findAll();

if ($id) {
$id = $this->encrypter->decrypt(hex2bin($id)); // การถอดรหัส
$data['player'] = $this->playerModel->get_Coachs($id);

// ดึงข้อมูลอำเภอ (amphure)
if (!empty($data['player']['amphure_id'])) {
$districtModel = new DistrictModel();
$district = $districtModel->find($data['player']['amphure_id']);
if ($district) {
$data['player']['amphure_name'] = $district['district_name']; // เก็บชื่ออำเภอใน player
}
}

// ดึงข้อมูลตำบล (tambon)
if (!empty($data['player']['amphure_id'])) {
$subdistrictModel = new SubdistrictModel();
$data['tambons'] = $subdistrictModel->where('district_id', $data['player']['amphure_id'])->findAll();

// ตรวจสอบว่าเลือกตำบลแล้วหรือไม่
if (!empty($data['player']['tambon_id'])) {
$tambon = $subdistrictModel->find($data['player']['tambon_id']);
if ($tambon) {
$data['player']['tambon_name'] = $tambon['subdistrict_name']; // เก็บชื่อตำบลใน player
}
}
}
} else {
$data['player'] = [];
$data['amphures'] = [];
$data['tambons'] = [];
}

// ดึง academy_id จาก coach's team หรือ user's academy
$data['academy_id'] = null;
$coachTeamId = !empty($data['player']['team_id']) ? (int)$data['player']['team_id'] : 0;
if ($coachTeamId > 0) {
    $db = db_connect();
    $at = $db->table('academy_teams')->where('team_id', $coachTeamId)->get()->getRowArray();
    if (!empty($at['academy_id'])) {
        $data['academy_id'] = $at['academy_id'];
    }
}
if (empty($data['academy_id']) && function_exists('getUserAcademyId')) {
    $data['academy_id'] = getUserAcademyId();
}

return view("Modules\Player\Views\manage_coach.php", $data);
}



/**
 * เช็คเลขบัตรประชาชนซ้ำในระบบแบบ real-time (ตอนพิมพ์ครบ 13 หลัก)
 * GET/POST: id_card, [exclude_id]  → { exists, player:{id,name} }
 */
public function check_id_card()
{
    $idCard    = $this->request->getGet('id_card') ?? $this->request->getPost('id_card') ?? '';
    $excludeId = $this->request->getGet('exclude_id') ?? $this->request->getPost('exclude_id') ?? null;
    $dup = $this->playerModel->findDuplicateIdCard((string)$idCard, $excludeId ? (int)$excludeId : null);
    if ($dup) {
        return $this->response->setJSON([
            'exists' => true,
            'player' => [
                'id'   => (int)$dup['id'],
                'name' => trim(($dup['first_name'] ?? '') . ' ' . ($dup['last_name'] ?? '')),
            ],
        ]);
    }
    return $this->response->setJSON(['exists' => false]);
}

public function save_player()
{
    $input = $this->request->getPost();

    // เพิ่มนักเตะใหม่ + เลขบัตรซ้ำในระบบ → เด้งถามเพิ่มจากระบบแทนการสร้างซ้ำ (เช็คก่อนอัปโหลดรูป)
    if (empty($input['id']) && !empty($input['id_card'])) {
        $dup = $this->playerModel->findDuplicateIdCard($input['id_card']);
        if ($dup) {
            return redirect()->back()->withInput()->with('duplicate_player', [
                'id'         => (int)$dup['id'],
                'name'       => trim(($dup['first_name'] ?? '') . ' ' . ($dup['last_name'] ?? '')),
                'team_id'    => (int)($input['team_id'] ?? 0) ?: (int)(session()->get('team.id') ?? 0),
                'academy_id' => (int)($input['academy_id'] ?? 0),
            ]);
        }
    }

    // Check if an image is provided in base64 format
    if (!empty($input['base64_image'])) {
        // Generate a random file name for the image
        $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);

        // Get the base64 string and clean it
        $img = $input['base64_image'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);  // Replace spaces with plus signs

        // Decode the base64 image data
        $data = base64_decode($img);

        // Check if the image is properly decoded
        if ($data === false) {
            // Handle error: Invalid base64 image data
            return redirect()->back()->with('error', 'Invalid image format');
        }

        // Set the file name and path for saving the image
        $set_name_img = $random_name . '.jpeg';  // Save the image as .jpeg
        $file_path = "public/uploads/players/" . $set_name_img;

        // Save the image to the server
        if (file_put_contents($file_path, $data) === false) {
            // Handle error: Failed to save the image
            return redirect()->back()->with('error', 'Failed to upload image');
        }

        // Save the image path in the input data
        $input['image_path'] = 'public/uploads/players/' . $set_name_img;
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
        $set_name_img_id = $random_name . '.' . $extension;
        file_put_contents($saveDir . $set_name_img_id, $data);
        $input['id_card_image'] = 'secure_uploads/id_cards/' . $set_name_img_id;
    }

    // Handle ID card image deletion
    if (!empty($input['delete_id_card_image']) && $input['delete_id_card_image'] === '1') {
        $input['id_card_image'] = '';
    }

    // Call the model function to save player data in the database
    try {
        $savedPlayerId = $this->playerModel->savePlayer($input);
    } catch (\RuntimeException $e) {
        return redirect()->back()->withInput()->with('error', $e->getMessage());
    }

    $mainPositionId = !empty($input['player_position_id']) ? (int)$input['player_position_id'] : null;
    $subPositionIds = !empty($input['player_sub_position_id']) && is_array($input['player_sub_position_id'])
        ? array_values(array_filter($input['player_sub_position_id'], fn($v) => $v !== '' && $v !== null))
        : [];

    // Hook: ถ้าเป็นนักเตะใหม่ → บันทึก creator + enrollment
    if (empty($input['id']) && $savedPlayerId) {
        $academyModel = new \Modules\Academy\Models\AcademyModel();
        $currentUserId = session()->get('user_id');

        // บันทึกผู้สร้าง
        if ($currentUserId) {
            $academyModel->setPlayerCreator((int)$savedPlayerId, (int)$currentUserId);
        }

        $directAcademyId = !empty($input['academy_id']) ? (int)$input['academy_id'] : 0;

        if ($directAcademyId > 0) {
            // มาจากหน้า Academy → enroll เข้า Academy แบบไม่ผูกทีม (team_id = 0)
            $academyModel->enrollPlayerToAcademy(
                $directAcademyId,
                (int)$savedPlayerId,
                0,
                $input['shirt_number'] ?? '',
                $currentUserId ? (int)$currentUserId : 0
            );
        } else {
            // Enrollment: หา academy_id จาก team_id
            $enrollTeamId = !empty($input['team_id']) ? (int)$input['team_id'] : (int)session()->get('team.id');
            if ($enrollTeamId > 0) {
                $atRow = db_connect()->table('academy_teams')
                    ->select('academy_id')
                    ->where('team_id', $enrollTeamId)
                    ->limit(1)
                    ->get()->getRowArray();

                if ($atRow) {
                    $academyModel->enrollPlayerToAcademy(
                        (int)$atRow['academy_id'],
                        (int)$savedPlayerId,
                        $enrollTeamId,
                        $input['shirt_number'] ?? '',
                        $currentUserId ? (int)$currentUserId : 0
                    );
                }
            }
        }
    }

    // Sync ตำแหน่งหลัก/รอง ไปยัง academy_player_enrollments หลังสร้าง enrollment แล้ว
    // เพื่อให้หน้าเพิ่มนักเตะใหม่จาก Academy เก็บตำแหน่งเหมือนหน้าแก้ไข
    if ($savedPlayerId) {
        db_connect()->table('academy_player_enrollments')
            ->where('player_id', (int)$savedPlayerId)
            ->where('is_active', 1)
            ->update([
                'position_id'      => $mainPositionId,
                'sub_position_ids' => !empty($subPositionIds) ? json_encode($subPositionIds) : null,
            ]);
    }

    // ถ้ามี academy_id (มาจากหน้า Academy) → กลับไปหน้า Academy players
    if (!empty($input['academy_id'])) {
        return redirect()->to(base_url('academy/players/' . (int)$input['academy_id']))
            ->with('success', 'บันทึกข้อมูลสำเร็จ');
    }

    // Fallback: ถ้าเป็นการแก้ไขนักเตะที่อยู่ใน academy → กลับไปหน้า academy players
    $playerIdForLookup = !empty($input['id']) ? (int)$input['id'] : (int)$savedPlayerId;
    if ($playerIdForLookup > 0) {
        $enrollment = db_connect()->table('academy_player_enrollments')
            ->select('academy_id')
            ->where('player_id', $playerIdForLookup)
            ->where('is_active', 1)
            ->orderBy('id', 'DESC')
            ->limit(1)
            ->get()->getRowArray();

        if (!empty($enrollment['academy_id'])) {
            return redirect()->to(base_url('academy/players/' . (int)$enrollment['academy_id']))
                ->with('success', 'บันทึกข้อมูลสำเร็จ');
        }
    }

    // Redirect to the player list page after saving
    // Phase 8: ตรวจสอบว่าทีมอยู่ใน Academy → redirect ไป Academy module
    $redirectTeamId = !empty($input['team_id']) ? $input['team_id'] : session()->get('team.id');
    if ($redirectTeamId) {
        $academyUrl = getAcademyUrlForTeam($redirectTeamId, 'player-list');
        if ($academyUrl) {
            return redirect()->to($academyUrl)->with('success', 'บันทึกข้อมูลสำเร็จ');
        }
    }

    // ถ้าเป็นผู้ฝึกสอนให้ redirect พร้อม team_id
    if (session()->get('user_type') === 'coach' && !empty($input['team_id'])) {
        return redirect()->to(base_url('team/player-list/' . $input['team_id']))->with('success', 'บันทึกข้อมูลสำเร็จ');
    }

    return redirect()->to(base_url('team/player-list'))->with('success', 'บันทึกข้อมูลสำเร็จ');
}
public function save_coach()
{
    $input = $this->request->getPost();

    // Check if an image is provided in base64 format
    if (!empty($input['base64_image'])) {
        // Generate a random file name for the image
        $random_name = substr(number_format(time() * rand(), 0, '', ''), 0, 10);

        // Get the base64 string and clean it
        $img = $input['base64_image'];
        $img = str_replace('data:image/png;base64,', '', $img);
        $img = str_replace(' ', '+', $img);  // Replace spaces with plus signs

        // Decode the base64 image data
        $data = base64_decode($img);

        // Check if the image is properly decoded
        if ($data === false) {
            // Handle error: Invalid base64 image data
            return redirect()->back()->with('error', 'Invalid image format');
        }

        // Set the file name and path for saving the image
        $set_name_img = $random_name . '.jpeg';  // Save the image as .jpeg
        $file_path = "public/uploads/players/" . $set_name_img;

        // Save the image to the server
        if (file_put_contents($file_path, $data) === false) {
            // Handle error: Failed to save the image
            return redirect()->back()->with('error', 'Failed to upload image');
        }

        // Save the image path in the input data
        $input['image_path'] = 'public/uploads/players/' . $set_name_img;
    }

    // Call the model function to save player data in the database
    $result = $this->playerModel->saveCoach($input);

    // Redirect to the player list page after saving
    return redirect()->to(base_url('team/coach-list'))->with('success', 'บันทึกข้อมูลสำเร็จ');
}


    public function ajax_players()
    {
        $teamId = $this->request->getPost('team_id');

        // ถ้าไม่มี teamId จาก POST ให้ใช้จาก session
        if (!$teamId) {
            $teamId = session()->get('team.id');
        }

        // Debug
        log_message('debug', 'ajax_players - teamId: ' . $teamId);

        return $this->playerModel->get_Player('', $teamId);
    }
    public function ajax_coachs()
    {
        $teamId = $this->request->getPost('team_id');
        $result = $this->playerModel->get_Coachs('', $teamId);
        return $result;
    }

    public function ajax_all_coachs()
    {
        $result = $this->playerModel->get_AllCoachs();
        return $result;
    }

    public function search_coaches()
    {
        try {
            $input = $this->request->getPost();

            // Log input data
            log_message('debug', 'Search coaches input: ' . json_encode($input));

            // ตรวจสอบว่ากรอกข้อมูล
            if (empty($input['search_name'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'กรุณากรอกข้อมูลเพื่อค้นหา'
                ]);
            }

            // ตรวจสอบ session
            $teamData = session()->get("team");
            log_message('debug', 'Team data from session: ' . json_encode($teamData));

            $teamId = session()->get("team.id") ?? (is_array($teamData) ? $teamData['id'] : null);

            if (!$teamId) {
                log_message('error', 'Search coaches: No team ID in session. Full session: ' . json_encode(session()->get()));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'กรุณาเข้าสู่ระบบใหม่อีกครั้ง'
                ]);
            }

            log_message('debug', 'Searching coaches with name: "' . $input['search_name'] . '" for team: ' . $teamId);

            // ค้นหาผู้ฝึกสอนจากระบบ
            $result = $this->playerModel->searchCoaches($teamId, $input['search_name']);

            log_message('debug', 'Search result count: ' . count($result) . ', data: ' . json_encode($result));

            if ($result && count($result) > 0) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $result
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลผู้ฝึกสอนที่ค้นหา',
                    'data' => []
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Search coaches exception: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function invite_coach()
    {
        try {
            $input = $this->request->getPost();

            // ตรวจสอบว่ามี coach_id หรือไม่
            if (empty($input['id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลผู้ฝึกสอน'
                ]);
            }

            // ตรวจสอบ session
            $teamId = session()->get("team.id") ?? session()->get("team")['id'] ?? null;
            if (!$teamId) {
                log_message('error', 'Invite coach: No team in session. Session data: ' . json_encode(session()->get()));
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'กรุณาเข้าสู่ระบบใหม่อีกครั้ง'
                ]);
            }

            $result = $this->playerModel->inviteCoach($input['id']);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'ส่งคำเชิญให้ผู้ฝึกสอนเรียบร้อย รอการยืนยัน'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'เกิดข้อผิดพลาดในการส่งคำเชิญ หรือได้ส่งคำเชิญแล้ว'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Invite coach exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function cancel_invitation()
    {
        $input = $this->request->getPost();
        $result = $this->playerModel->cancelInvitation($input['id']);

        if ($result) {
            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'ยกเลิกคำเชิญเรียบร้อย'
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการยกเลิกคำเชิญ'
            ]);
        }
    }

    public function my_invitations()
    {
        // Get coach associated with current user
        $coach = $this->db->table("coach")
            ->where('coach_user_id', session()->get('user_id'))
            ->where('is_active', 1)
            ->get()->getRowArray();

        if (!$coach) {
            return redirect()->to('/')->with('error', 'คุณไม่ใช่ผู้ฝึกสอนในระบบ');
        }

        $invitations = $this->playerModel->getCoachInvitations($coach['id']);
        $data['invitations'] = $invitations;
        $data['coach'] = $coach;

        return view('Modules\Player\Views\coach_invitations.php', $data);
    }

    public function respond_invitation()
    {
        $input = $this->request->getPost();

        // Get coach associated with current user
        $coach = $this->db->table("coach")
            ->where('coach_user_id', session()->get('user_id'))
            ->where('is_active', 1)
            ->get()->getRowArray();

        if (!$coach) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'คุณไม่ใช่ผู้ฝึกสอนในระบบ'
            ]);
        }

        $result = $this->playerModel->respondToInvitation(
            $input['invitation_id'],
            $input['response'],
            $coach['id']
        );

        if ($result) {
            $message = ($input['response'] === 'accepted') ?
                'ยอมรับคำเชิญเรียบร้อย คุณเป็นสมาชิกในทีมแล้ว' :
                'ปฏิเสธคำเชิญเรียบร้อย';

            return $this->response->setJSON([
                'status' => 'success',
                'message' => $message
            ]);
        } else {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาดในการตอบรับคำเชิญ'
            ]);
        }
    }

    public function delete_player()
    {
        try {
            $input = $this->request->getPost();

            if (empty($input['id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลนักเตะ'
                ]);
            }

            $this->playerModel->deletePlayer($input['id']);

            return $this->response->setJSON([
                'status' => 'success',
                'message' => 'ลบข้อมูลนักเตะเรียบร้อยแล้ว'
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }
    public function delete_coach()
    {
        $input = $this->request->getPost();

        try {
            $result = $this->playerModel->deleteCoach($input['id']);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'ลบผู้ฝึกสอนออกจากทีมสำเร็จ ผู้ฝึกสอนไม่เป็นสมาชิกของทีมนี้แล้ว'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่สามารถนำผู้ฝึกสอนออกจากทีมได้'
                ]);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    public function remove_coach_from_club()
    {
        $input = $this->request->getPost();
        $coachId = $input['id'];

        try {
            // ดึงข้อมูลโค๊ชและทีม
            $coachData = $this->playerModel->getCoachById($coachId);
            if (!$coachData) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลผู้ฝึกสอน'
                ]);
            }

            // ดึงข้อมูลทีมปัจจุบัน
            $teamData = $this->teamModel->getUserteam(session()->get('user_id'));
            if (!$teamData) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลทีม'
                ]);
            }

            // ยกเลิกผู้ฝึกสอนออกจากสโมสร
            $result = $this->playerModel->removeCoachFromClub($coachId, $teamData['id']);

            if ($result) {
                // ส่งการแจ้งเตือนให้ผู้ฝึกสอน
                $this->sendNotificationToCoach($coachData, $teamData, 'removed');

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'ยกเลิกผู้ฝึกสอนออกจากสโมสรสำเร็จ และส่งการแจ้งเตือนแล้ว'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่สามารถยกเลิกผู้ฝึกสอนได้'
                ]);
            }

        } catch (\Exception $e) {
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    private function sendNotificationToCoach($coachData, $teamData, $action = 'removed')
    {
        try {
            $db = \Config\Database::connect();

            // สร้างข้อความแจ้งเตือน
            $message = '';
            if ($action === 'removed') {
                $message = "คุณได้ถูกยกเลิกออกจากสโมสร {$teamData['name']} แล้ว";
            }

            // บันทึกการแจ้งเตือนลงฐานข้อมูล
            $notificationData = [
                'coach_id' => $coachData['coach_user_id'],
                'title' => 'การยกเลิกจากสโมสร',
                'message' => $message,
                'type' => 'team_removal',
                'is_read' => 0,
                'created_at' => date('Y-m-d H:i:s')
            ];

            $db->table('notifications')->insert($notificationData);

            return true;
        } catch (\Exception $e) {
            log_message('error', 'Failed to send notification to coach: ' . $e->getMessage());
            return false;
        }
    }
     // --- API สำหรับดึงข้อมูลอำเภอและตำบล (เพิ่มส่วนนี้เข้ามา) ---

    /**
     * ดึงข้อมูลอำเภอตาม ID ของจังหวัด
     * @param int $provinceId
     * @return \CodeIgniter\HTTP\Response
     */
    public function getAmphures($provinceId = null)
    {
        if (!$this->request->isAJAX() || $provinceId === null) {
            return $this->response->setStatusCode(403);
        }

        $districtModel = new DistrictModel();
        // ค้นหาอำเภอทั้งหมดที่อยู่ในจังหวัดที่ระบุ
        $districts = $districtModel->where('province_id', $provinceId)
                                   ->orderBy('district_name', 'ASC')
                                   ->findAll();

        return $this->response->setJSON($districts);
    }

    /**
     * ดึงข้อมูลตำบลตาม ID ของอำเภอ
     * @param int $districtId
     * @return \CodeIgniter\HTTP\Response
     */
    public function getTambons($districtId = null)
    {
        if (!$this->request->isAJAX() || $districtId === null) {
            return $this->response->setStatusCode(403);
        }

        $subdistrictModel = new SubdistrictModel();
        // ค้นหาตำบลทั้งหมดที่อยู่ในอำเภอที่ระบุ
        $subdistricts = $subdistrictModel->where('district_id', $districtId)
                                         ->orderBy('subdistrict_name', 'ASC')
                                         ->findAll();

        return $this->response->setJSON($subdistricts);
    }

    /**
     * ดึงข้อมูลผู้ฝึกสอนเพื่อแสดงใน popup
     */
    public function getCoachProfile()
    {
        try {
            $input = $this->request->getPost();

            // ตรวจสอบว่ามี coach_id หรือไม่
            if (empty($input['coach_id'])) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลผู้ฝึกสอน'
                ]);
            }

            $coachId = $input['coach_id'];

            // ดึงข้อมูลผู้ฝึกสอนพร้อมข้อมูลหลักสูตร
            $coach = $this->db->table('coach')
                ->select('coach.*,
                         CONCAT(coach.first_name, " ", coach.last_name) as full_name,
                         coach.nick_name,
                         coach.birth_date,
                         coach.id_card,
                         coach.nation,
                         coach.height,
                         coach.weight,
                         coach.image_path,
                         coach_users.line_picture')
                ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
                ->where('coach.id', $coachId)
                ->where('coach.is_active', 1)
                ->get()
                ->getRowArray();

            if (!$coach) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลผู้ฝึกสอน'
                ]);
            }

            // ดึงข้อมูลหลักสูตร (Certificates)
            $certificates = $this->db->table('coach_certs')
                ->select('coach_certs.*,
                         tbl_fifa_organizations.name as organ_name,
                         tbl_fifa_organizations.label as organ_label,
                         tbl_certs_level.name as cert_level_name')
                ->join('tbl_fifa_organizations', 'tbl_fifa_organizations.id = coach_certs.organ_id', 'left')
                ->join('tbl_certs_level', 'tbl_certs_level.id = coach_certs.cert_level_id', 'left')
                ->where('coach_certs.coach_id', $coachId)
                ->get()
                ->getResultArray();

            $coach['certificates'] = $certificates;

            // แสดงรูปภาพตามลำดับความสำคัญ: image_path ก่อน ถ้าไม่มีค่อยใช้ line_picture
            $displayImage = !empty($coach['image_path']) ? base_url($coach['image_path']) : (!empty($coach['line_picture']) ? base_url($coach['line_picture']) : '');
            $coach['display_image'] = $displayImage;

            return $this->response->setJSON([
                'status' => 'success',
                'data' => $coach
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Get coach profile exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Search players by ID card or name (รองรับ multi-team แล้ว)
     */
    public function search_available_players()
    {
        try {
            $search = $this->request->getPost('search');
            $targetTeamId = (int)($this->request->getPost('team_id') ?: session()->get('team.id'));

            if (empty($search) || strlen($search) < 2) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'กรุณากรอกข้อมูลอย่างน้อย 2 ตัวอักษร',
                    'data' => []
                ]);
            }

            // ค้นหานักเตะ - ไม่จำกัด team_id=0 เพราะรองรับหลายทีมแล้ว
            // บัตร ปชช. ใน DB เก็บปนกัน (มีขีด/ช่องว่าง/เลขล้วน) → normalize ทั้งสองฝั่งตอนค้นบัตร
            $idSearch = str_replace(['-', ' '], '', $search);
            $builder = $this->db->table('player')
                ->select('player.id, player.first_name, player.last_name, player.nick_name, player.id_card, player.image_path,
                          CONCAT(player.first_name, " ", player.last_name) as full_name')
                ->groupStart()
                    ->like('player.id_card', $search)
                    ->orLike('player.first_name', $search)
                    ->orLike('player.last_name', $search)
                    ->orLike('CONCAT(player.first_name, " ", player.last_name)', $search);
            if ($idSearch !== '') {
                $builder->orLike(new \CodeIgniter\Database\RawSql("REPLACE(REPLACE(player.id_card, '-', ''), ' ', '')"), $idSearch);
            }
            $players = $builder->groupEnd()
                ->limit(20)
                ->get()
                ->getResultArray();

            if ($players && count($players) > 0) {
                foreach ($players as &$player) {
                    $player['image_url'] = !empty($player['image_path'])
                        ? base_url($player['image_path'])
                        : base_url('public/img/img_profile.jpg');

                    // ตรวจว่าอยู่ในทีมนี้แล้วหรือยัง
                    $inTeam = $targetTeamId ? $this->db->table('academy_player_enrollments')
                        ->where('player_id', $player['id'])
                        ->where('team_id', $targetTeamId)
                        ->where('is_active', 1)
                        ->countAllResults() > 0 : false;
                    $player['already_in_team'] = $inTeam;
                }

                return $this->response->setJSON([
                    'status' => 'success',
                    'data' => $players
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบนักเตะที่ค้นหา',
                    'data' => []
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Search available players exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * Assign player to team (backward compat - ใช้ player_teams แล้ว)
     */
    public function assign_player_to_team()
    {
        try {
            $playerId = (int)$this->request->getPost('player_id');
            $teamId   = (int)($this->request->getPost('team_id') ?: session()->get('team.id'));

            if (empty($playerId) || empty($teamId)) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบข้อมูลนักเตะหรือทีม'
                ]);
            }

            $player = $this->db->table('player')
                ->where('id', $playerId)
                ->where('is_active', 1)
                ->get()->getRowArray();

            if (!$player) {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'ไม่พบนักเตะในระบบ'
                ]);
            }

            $result = $this->playerModel->addPlayerToTeam($playerId, $teamId);

            if ($result) {
                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'เพิ่มนักเตะ ' . $player['first_name'] . ' ' . $player['last_name'] . ' เข้าทีมเรียบร้อยแล้ว'
                ]);
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'นักเตะอยู่ในทีมนี้แล้ว'
                ]);
            }

        } catch (\Exception $e) {
            log_message('error', 'Assign player to team exception: ' . $e->getMessage());
            return $this->response->setJSON([
                'status' => 'error',
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ]);
        }
    }

    /**
     * เพิ่มนักเตะเข้าทีม (multi-team)
     */
    public function add_player_to_team()
    {
        try {
            $playerId = (int)$this->request->getPost('player_id');
            $teamId   = (int)$this->request->getPost('team_id');

            if (!$playerId || !$teamId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
            }

            $result = $this->playerModel->addPlayerToTeam($playerId, $teamId);

            if ($result) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'เพิ่มทีมให้นักเตะเรียบร้อย']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'นักเตะอยู่ในทีมนี้แล้ว']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * ลบนักเตะออกจากทีม (multi-team)
     */
    public function remove_player_from_team()
    {
        try {
            $playerId = (int)$this->request->getPost('player_id');
            $teamId   = (int)$this->request->getPost('team_id');

            if (!$playerId || !$teamId) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ข้อมูลไม่ครบ']);
            }

            $result = $this->playerModel->removePlayerFromTeam($playerId, $teamId);

            if ($result) {
                return $this->response->setJSON(['status' => 'success', 'message' => 'ลบนักเตะออกจากทีมเรียบร้อย']);
            } else {
                return $this->response->setJSON(['status' => 'error', 'message' => 'ไม่พบข้อมูลการสังกัด']);
            }
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
        }
    }

    /**
     * AJAX: ดึงทีมทั้งหมดของนักเตะ
     */
    public function get_player_teams()
    {
        try {
            $playerId = (int)$this->request->getPost('player_id');
            if (!$playerId) {
                return $this->response->setJSON(['status' => 'error', 'data' => []]);
            }
            $teams = $this->playerModel->get_PlayerTeams($playerId);
            return $this->response->setJSON(['status' => 'success', 'data' => $teams]);
        } catch (\Exception $e) {
            return $this->response->setJSON(['status' => 'error', 'message' => $e->getMessage()]);
        }
    }
}
