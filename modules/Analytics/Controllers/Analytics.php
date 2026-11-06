<?php

namespace Modules\Analytics\Controllers;

use App\Controllers\BaseController;
use Modules\Analytics\Models\AnalyticsModel;

class Analytics extends BaseController
{
    protected $analyticsModel;

    public function __construct()
    {
        $this->analyticsModel = new AnalyticsModel();
    }

    /**
     * Dashboard - ภาพรวมระบบ Analytics
     */
    public function index()
    {
        $data = [
            'title'          => 'Football Analytics Dashboard',
            'total_matches'  => $this->analyticsModel->getTotalMatches(),
            'completed'      => $this->analyticsModel->getCompletedMatches(),
            'processing'     => $this->analyticsModel->getProcessingMatches(),
            'total_players'  => $this->analyticsModel->getTotalTrackedPlayers(),
            'top_distance'   => $this->analyticsModel->getTopPlayersByDistance(5),
            'monthly_matches'=> $this->analyticsModel->getMatchesPerMonth(),
            'recent_matches' => $this->analyticsModel->getRecentMatches(5),
        ];

        return view('template/admin_layout', [
            'title'   => $data['title'],
            'content' => view('Modules\Analytics\Views\dashboard', $data),
        ]);
    }
}
