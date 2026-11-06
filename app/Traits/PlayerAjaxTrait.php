<?php

namespace App\Traits;

/**
 * Shared player-AJAX logic (DRY) สำหรับ Team & Academy controllers.
 *
 * get_player() เดิม clone ซ้ำใน 2 controller ต่างกันแค่ข้อความ (Team hardcode / Academy lang()).
 * ทั้งคู่ใช้ $this->playerModel (มีใน 2 controller) → ไม่ต้อง inject model, inject แค่ messages
 * พฤติกรรม/ผลลัพธ์เหมือนเดิม 100% by construction.
 */
trait PlayerAjaxTrait
{
    /** @return array<string,string> keys: auth_required, player_not_found, team_not_found, player_not_in_team */
    abstract protected function playerAjaxMessages(): array;

    public function get_player($playerId = null)
    {
        $msg = $this->playerAjaxMessages();
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $msg['auth_required']
            ])->setStatusCode(401);
        }

        try {
            if (!$playerId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $msg['player_not_found']
                ])->setStatusCode(400);
            }

            $teamId = session()->get('team.id');
            if (!$teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $msg['team_not_found']
                ])->setStatusCode(400);
            }

            // Get player data
            $playerData = $this->playerModel->get_Player($playerId);

            if (!$playerData || $playerData['team_id'] != $teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => $msg['player_not_in_team']
                ])->setStatusCode(403);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $playerData
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
