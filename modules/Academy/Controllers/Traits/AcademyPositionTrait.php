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
use Modules\Academy\Controllers\Traits\AcademyTrainingTrait;
use App\Traits\JerseyManagementTrait;
use App\Traits\PlayerAjaxTrait;
use App\Traits\ChartSelectionTrait;
use Modules\Academy\Controllers\Traits\AcademyRequestsTrait;
use Modules\Academy\Controllers\Traits\AcademyExportTrait;
use Modules\Academy\Controllers\Traits\AcademySubmitTrait;

trait AcademyPositionTrait
{
    /**
     * True if the player is actively enrolled in the team (or its parent team).
     * Uses academy_player_enrollments — NOT the legacy player.team_id, which only
     * reflects the player's PRIMARY team and wrongly rejects multi-team players
     * when editing positions in a non-primary team.
     */
    private function playerBelongsToTeam(int $playerId, int $teamId): bool
    {
        $teamInfo = $this->db->table('team')->select('parent_team_id')->where('id', $teamId)->get()->getRowArray();
        $teamIds  = [$teamId];
        if ($teamInfo && !empty($teamInfo['parent_team_id'])) {
            $teamIds[] = (int) $teamInfo['parent_team_id'];
        }
        $enrolled = $this->db->table('academy_player_enrollments')
            ->where('player_id', $playerId)
            ->whereIn('team_id', $teamIds)
            ->where('is_active', 1)
            ->countAllResults() > 0;
        if ($enrolled) {
            return true;
        }
        // legacy fallback: player.team_id
        $p = $this->db->table('player')->select('team_id')->where('id', $playerId)->get()->getRowArray();
        return $p && in_array((int) $p['team_id'], array_map('intval', $teamIds), true);
    }

    /**
     * Assign players to a specific position
     * POST /academy/assign-player-position
     *
     * @return JSON response with success status
     */
    public function assign_player_position()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Common.academy_auth_required')
            ])->setStatusCode(401);
        }

        try {
            $input = $this->request->getJSON();

            if (!$input) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_invalid_json')
                ])->setStatusCode(400);
            }

            $positionId = $input->position_id ?? null;
            $playersData = $input->players ?? [];

            if (!$positionId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_position_not_found')
                ])->setStatusCode(400);
            }

            // Parse players data
            $players = is_string($playersData) ? json_decode($playersData, true) : (is_object($playersData) ? (array)$playersData : $playersData);

            if (empty($players) || !is_array($players)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_player_not_found')
                ])->setStatusCode(400);
            }

            // Get team_id from session or input
            $teamId = $input->team_id ?? session()->get('team.id');
            if (!$teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_team_not_found')
                ])->setStatusCode(400);
            }

            // Verify team belongs to user's academy
            $academy = $this->academyModel->getAcademyByTeamId($teamId);
            if ($academy && !$this->canViewAcademy($academy['id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_no_team_access')
                ])->setStatusCode(403);
            }

            // Start transaction
            $this->db->transBegin();

            // Update each player's position
            foreach ($players as $player) {
                $playerObj = is_object($player) ? $player : (object)$player;
                $playerId = $playerObj->id ?? null;

                if (!$playerId) {
                    continue;
                }

                // Verify player belongs to this team
                $playerData = $this->playerModel->get_Player($playerId);
                if (!$playerData || !$this->playerBelongsToTeam((int)$playerId, (int)$teamId)) {
                    throw new \Exception(lang('Common.academy_player_not_in_team'));
                }

                $this->db->table('player')
                    ->where('id', $playerId)
                    ->update(['player_position_id' => $positionId]);
            }

            if ($this->db->transStatus() === false) {
                $this->db->transRollback();
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_save_error')
                ])->setStatusCode(500);
            }

            $this->db->transCommit();

            return $this->response->setJSON([
                'success' => true,
                'message' => lang('Common.academy_player_position_added')
            ]);
        } catch (\Exception $e) {
            $this->db->transRollback();
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update player data (name, last name, shirt number)
     * POST /academy/update-player
     *
     * @return JSON response with success status
     */
    public function update_player()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Common.academy_auth_required')
            ])->setStatusCode(401);
        }

        try {
            $input = $this->request->getJSON();

            if (!$input) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_invalid_json')
                ])->setStatusCode(400);
            }

            $playerId = $input->player_id ?? null;
            $firstName = $input->first_name ?? null;
            $lastName = $input->last_name ?? null;
            $shirtNumber = $input->shirt_number ?? null;

            if (!$playerId || !$firstName) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_data_incomplete')
                ])->setStatusCode(400);
            }

            $teamId = $input->team_id ?? session()->get('team.id');
            if (!$teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_team_not_found')
                ])->setStatusCode(400);
            }

            // Verify team belongs to user's academy
            $academy = $this->academyModel->getAcademyByTeamId($teamId);
            if ($academy && !$this->canViewAcademy($academy['id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_no_team_access')
                ])->setStatusCode(403);
            }

            // Verify player belongs to this team
            $playerData = $this->playerModel->get_Player($playerId);
            if (!$playerData || !$this->playerBelongsToTeam((int)$playerId, (int)$teamId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_player_not_in_team')
                ])->setStatusCode(403);
            }

            // Prepare update data
            $updateData = [
                'first_name' => $firstName,
                'last_name' => $lastName ?? ''
            ];

            if ($shirtNumber !== null && $shirtNumber !== '') {
                $updateData['shirt_number'] = $shirtNumber;
            }

            $this->db->table('player')
                ->where('id', $playerId)
                ->update($updateData);

            return $this->response->setJSON([
                'success' => true,
                'message' => lang('Common.academy_player_updated')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Update player position
     * POST /academy/update-player-position
     *
     * @return JSON response with success status
     */
    public function update_player_position()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Common.academy_auth_required')
            ])->setStatusCode(401);
        }

        try {
            $input = $this->request->getJSON();

            if (!$input) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_invalid_json')
                ])->setStatusCode(400);
            }

            $playerId = $input->player_id ?? null;
            $positionId = $input->position_id ?? null;

            if (!$playerId || !$positionId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_data_incomplete')
                ])->setStatusCode(400);
            }

            $teamId = $input->team_id ?? session()->get('team.id');
            if (!$teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_team_not_found')
                ])->setStatusCode(400);
            }

            // Verify team belongs to user's academy
            $academy = $this->academyModel->getAcademyByTeamId($teamId);
            if ($academy && !$this->canViewAcademy($academy['id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_no_team_access')
                ])->setStatusCode(403);
            }

            // Verify player belongs to this team
            $playerData = $this->playerModel->get_Player($playerId);
            if (!$playerData || !$this->playerBelongsToTeam((int)$playerId, (int)$teamId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_player_not_in_team')
                ])->setStatusCode(403);
            }

            $this->db->table('player')
                ->where('id', $playerId)
                ->update(['player_position_id' => $positionId]);

            return $this->response->setJSON([
                'success' => true,
                'message' => lang('Common.academy_player_position_updated')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }

    /**
     * Remove player from position
     * POST /academy/remove-player-position
     *
     * @return JSON response with success status
     */
    public function remove_player_position()
    {
        if (!session()->get('isLoggedIn')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => lang('Common.academy_auth_required')
            ])->setStatusCode(401);
        }

        try {
            $input = $this->request->getJSON();

            if (!$input) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_invalid_json')
                ])->setStatusCode(400);
            }

            $playerId = $input->player_id ?? null;

            if (!$playerId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_player_not_found')
                ])->setStatusCode(400);
            }

            $teamId = $input->team_id ?? session()->get('team.id');
            if (!$teamId) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_team_not_found')
                ])->setStatusCode(400);
            }

            // Verify team belongs to user's academy
            $academy = $this->academyModel->getAcademyByTeamId($teamId);
            if ($academy && !$this->canViewAcademy($academy['id'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_no_team_access')
                ])->setStatusCode(403);
            }

            // Verify player belongs to this team
            $playerData = $this->playerModel->get_Player($playerId);
            if (!$playerData || !$this->playerBelongsToTeam((int)$playerId, (int)$teamId)) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => lang('Common.academy_player_not_in_team')
                ])->setStatusCode(403);
            }

            $this->db->table('player')
                ->where('id', $playerId)
                ->update(['player_position_id' => null]);

            return $this->response->setJSON([
                'success' => true,
                'message' => lang('Common.academy_player_position_removed')
            ]);
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => $e->getMessage()
            ])->setStatusCode(500);
        }
    }
}
