<?php

namespace App\Traits;

/**
 * Shared player/coach chart-selection logic (DRY) สำหรับ Team & Academy controllers.
 *
 * 4 method นี้ clone ซ้ำใน 2 controller ต่างกันแค่ chart model (teamModel/academyModel) —
 * ข้อความเหมือนกันทุกตัว (English hardcode) จึง inject แค่ model hook เดียว
 * (save_coach_chart_selection ไม่รวมที่นี่ เพราะ Team มี logic sub-team validate ต่างจาก Academy)
 *
 * พฤติกรรม/ผลลัพธ์เหมือนเดิม 100% by construction.
 */
trait ChartSelectionTrait
{
    /** @return object model ที่มี save/get PlayerChartSelection, getPlayerOldChartSelection, getCoachChartSelection */
    abstract protected function chartModel();

    public function save_chart_selection()
    {
        $teamId = $this->request->getPost('team_id');
        $playerIds = $this->request->getPost('player_ids');

        if (!$teamId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Team ID required']);
        }

        // Ensure player_ids is array
        if (!is_array($playerIds)) {
            $playerIds = [];
        }

        // Filter out invalid or inactive players
        $activePlayers = $this->playerModel->get_PlayerList($teamId);
        $activePlayerIds = array_column($activePlayers, 'id');

        $playerIds = array_filter($playerIds, function ($id) use ($activePlayerIds) {
            return in_array($id, $activePlayerIds);
        });

        $playerIds = array_values($playerIds);

        $result = $this->chartModel()->savePlayerChartSelection($teamId, $playerIds);

        if ($result) {
            return $this->response->setJSON(['status' => 'success']);
        } else {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Failed to save']);
        }
    }

    public function get_chart_selection($teamId = null)
    {
        if (!$teamId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Team ID required']);
        }

        $selection = $this->chartModel()->getPlayerChartSelection($teamId);

        return $this->response->setJSON(['status' => 'success', 'data' => $selection]);
    }

    public function get_old_chart_selection($teamId = null, $tournamentId = null)
    {
        if (!$teamId && !$tournamentId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Team ID and Tournament ID required']);
        }

        $selection = $this->chartModel()->getPlayerOldChartSelection($teamId, $tournamentId);

        return $this->response->setJSON(['status' => 'success', 'data' => $selection]);
    }

    public function get_coach_chart_selection($teamId = null)
    {
        if (!$teamId) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Team ID required']);
        }

        $selection = $this->chartModel()->getCoachChartSelection($teamId);

        return $this->response->setJSON(['status' => 'success', 'data' => $selection]);
    }
}
