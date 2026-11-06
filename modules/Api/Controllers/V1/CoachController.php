<?php

namespace Modules\Api\Controllers\V1;

use Modules\Api\Controllers\BaseApiController;
use Modules\Coach\Models\CoachModel;

class CoachController extends BaseApiController
{
    protected $coachModel;
    protected $db;

    public function __construct()
    {
        $this->coachModel = new CoachModel();
        $this->db = \Config\Database::connect();
    }

    /**
     * GET /api/v1/teams/:id/coaches
     * Schema: ISquadCoach[]
     */
    public function teamCoaches($teamId = null)
    {
        $team = $this->db->table('team')->where('id', $teamId)->get()->getRowArray();
        if (!$team) {
            return $this->notFoundResponse('Team not found');
        }

        $coaches = $this->coachModel->getTeamCoaches($teamId);

        return $this->successResponse(array_map(function ($coach) {
            return $this->formatCoachSummary($coach);
        }, $coaches));
    }

    /**
     * GET /api/v1/coaches/:id
     * Coach detail with certifications and teams.
     */
    public function show($coachId = null)
    {
        $coach = $this->coachModel->getCoachById($coachId);
        if (!$coach) {
            return $this->notFoundResponse('Coach not found');
        }

        $data = $this->formatCoachDetail($coach);
        $data['licenses'] = array_map(function ($cert) {
            return $this->formatCertification($cert);
        }, $this->coachModel->getCertifications($coachId));
        $data['teams'] = array_map(function ($team) {
            return [
                'id' => (int) $team['id'],
                'name' => $team['name'] ?? null,
                'logo' => $this->imageUrl($team['logo'] ?? null),
            ];
        }, $this->coachModel->getCoachTeams($coachId));

        return $this->successResponse($data);
    }

    private function formatCoachSummary(array $coach): array
    {
        $fullName = trim(($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? ''));

        return [
            'id' => isset($coach['id']) ? (int) $coach['id'] : null,
            'name' => $fullName ?: ($coach['nick_name'] ?? null),
            'shortName' => $coach['nick_name'] ?? null,
            'photo' => $this->imageUrl($coach['image_path'] ?? null),
            'role' => $coach['position'] ?? null,
            'position' => $coach['position'] ?? null,
            'team_id' => $coach['team_id'] ?? null,
            'coach_user_id' => $coach['coach_user_id'] ?? null,
        ];
    }

    private function formatCoachDetail(array $coach): array
    {
        $data = $this->formatCoachSummary($coach);

        return array_merge($data, [
            'firstName' => $coach['first_name'] ?? null,
            'lastName' => $coach['last_name'] ?? null,
            'birthDate' => $coach['birth_date'] ?? null,
            'nationality' => $coach['nation'] ?? null,
            'height' => $coach['height'] ?? null,
            'weight' => $coach['weight'] ?? null,
            'dominantFoot' => $coach['dominant_foot'] ?? null,
            'province' => $coach['province_name'] ?? null,
            'district' => $coach['amphure_name'] ?? null,
            'subdistrict' => $coach['tambon_name'] ?? null,
            'email' => $coach['email'] ?? null,
        ]);
    }

    private function formatCertification(array $cert): array
    {
        return [
            'id' => isset($cert['id']) ? (int) $cert['id'] : null,
            'name' => $cert['level_name'] ?? null,
            'organization' => $cert['organ_name'] ?? null,
            'licenseCode' => $cert['license_code'] ?? null,
            'year' => $cert['year'] ?? null,
            'image' => $this->imageUrl($cert['cert_image'] ?? null),
        ];
    }
}
