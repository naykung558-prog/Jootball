<?php

namespace App\Traits;

/**
 * Shared jersey upload/remove/get logic (DRY) สำหรับ Team & Academy controllers.
 *
 * เดิม method ทั้ง 3 (uploadJersey/removeJersey/getJerseys) ถูก clone ซ้ำใน 2 controller
 * ต่างกันแค่ model (teamModel/academyModel) + ข้อความ (Team hardcode / Academy lang()).
 * Trait นี้เก็บ algorithm กลาง โดยให้แต่ละ controller ส่ง variant ผ่าน 2 hook:
 *   - jerseyEntityModel()  → คืน model ของตัวเอง
 *   - jerseyMessages()     → คืน array ข้อความของตัวเอง
 * พฤติกรรม/ผลลัพธ์เหมือนเดิม 100% by construction (controller ส่ง model+ข้อความเดิมเป๊ะ)
 */
trait JerseyManagementTrait
{
    /** @return object model ที่มี getJerseys()/removeJersey()/saveJersey() */
    abstract protected function jerseyEntityModel();

    /** @return array<string,string> keys: auth_required, data_incomplete, kit_type_invalid, image_read_error, kit_uploaded, kit_deleted */
    abstract protected function jerseyMessages(): array;

    public function getJerseys($teamId = null)
    {
        $msg = $this->jerseyMessages();
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON(['status' => 'error', 'message' => $msg['auth_required']])->setStatusCode(401);
        }

        if (!$teamId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Team ID required']);
        }

        $jerseys = $this->jerseyEntityModel()->getJerseys($teamId);

        return $this->response->setJSON(['status' => 'success', 'data' => $jerseys]);
    }

    // (ยกเลิกแล้ว) removeJersey()/uploadJersey() เดิมเขียนฟิลด์ jersey_home/away/gk ที่กำลังถูกลบ
    // ชุดทีมย้ายไปแก้ที่ tournament_team ผ่าน Academy::tournamentJersey / saveTournamentJersey
}
