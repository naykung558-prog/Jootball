<?php

namespace Config;

use Config\Services;

// Create a new instance of our RouteCollection class.
$routes = Services::routes();

// Load the system's routing file first, so that the app and ENVIRONMENT
// can override as needed.
if (file_exists(SYSTEMPATH . 'Config/Routes.php')) {
    require SYSTEMPATH . 'Config/Routes.php';
}

$routes->setDefaultNamespace('Modules\Home\Controllers');
$routes->setDefaultController('Home');
$routes->setDefaultMethod('index');
$routes->setTranslateURIDashes(false);
$routes->set404Override();
$routes->setAutoRoute(true);

$routes->get('amphures/(:num)', '\Modules\Player\Controllers\Player::getAmphures/$1');
$routes->get('tambons/(:num)', '\Modules\Player\Controllers\Player::getTambons/$1');
$routes->post('maps-quota/check', '\App\Controllers\MapsQuota::check');
$routes->get('admin/maps-quota/log', '\App\Controllers\MapsQuota::log', ['filter' => 'adminGuard']);
$routes->get('admin/maps-quota/log/(:segment)', '\App\Controllers\MapsQuota::log/$1', ['filter' => 'adminGuard']);

$routes->group('/', ['namespace' => '\Modules\Home\Controllers'], function ($routes) {
    $routes->get('/', 'Home::index');
});

// Privacy Policy Route (public access)
$routes->get('privacy', '\App\Controllers\Home::privacy');

// Language Switcher Route
$routes->get('language/switch/(:segment)', '\App\Controllers\Language::switch/$1');

// LINE Callback - ต้องอยู่นอก group เพราะ redirect_uri ใน LINE Console ตั้งเป็น /line/callback
$routes->get('line/callback', '\Modules\Auth\Controllers\Auth::line_callback');

$routes->group('auth', ['namespace' => '\Modules\Auth\Controllers'], function ($routes) {
    $routes->get('/', 'Auth::index');
    $routes->get('logout', 'Auth::logout');
    $routes->get('login', 'Auth::index');
    $routes->post('login-with-account', 'Auth::login_with_account');
    $routes->get('register', 'Auth::register');
    $routes->post('register-post', 'Auth::register_post');

    // Forgot Password routes
    $routes->get('forgot-password', 'Auth::forgot_password');
    $routes->post('forgot-password-submit', 'Auth::forgot_password_submit');
    $routes->get('reset-password/(:any)', 'Auth::reset_password/$1');
    $routes->post('reset-password-submit', 'Auth::reset_password_submit');

    // Tournament routes - เข้าดูได้โดยไม่ต้อง login แต่สมัครต้อง login
    $routes->get('tournament/list', 'Auth::tournament_list');
    $routes->get('tournament/detail/(:any)', 'Auth::tournament_detail/$1');
    $routes->post('tournament/register', 'Auth::register_tournament', ['filter' => 'authGuard']);
    $routes->post('tournament/cancel', 'Auth::cancel_tournament_registration', ['filter' => 'authGuard']);
    $routes->post('tournament/get-team-details', 'Auth::ajax_get_team_details');

    $routes->get('pdpa/check-consent', 'Auth::checkPdpaConsent');
    $routes->post('pdpa/save-consent', 'Auth::savePdpaConsent');
});

$routes->group('team', ['namespace' => '\Modules\Team\Controllers'], function ($routes) {
    $routes->get('register', 'Team::register', ['filter' => 'authGuard']);
    $routes->get('manage', 'Team::manage', ['filter' => 'authGuard']);
    $routes->get('manage/(:num)', 'Team::manage/$1', ['filter' => 'authGuard']);
    $routes->post('post-register', 'Team::post_register', ['filter' => 'authGuard']);
    $routes->get('info', 'Team::index', ['filter' => 'authGuard']);
    $routes->get('info/(:num)', 'Team::index/$1', ['filter' => 'authGuard']);
    $routes->get('player-list', 'Team::player_list', ['filter' => 'authGuard']);
    $routes->get('player-list/(:num)', 'Team::player_list/$1', ['filter' => 'authGuard']);
    $routes->get('player-chart', 'Team::football_player_chart', ['filter' => 'authGuard']);
    $routes->get('player-chart/(:num)', 'Team::football_player_chart/$1', ['filter' => 'authGuard']);
    $routes->post('assign-player-position', 'Team::assign_player_position', ['filter' => 'authGuard']);
    $routes->post('update-player', 'Team::update_player', ['filter' => 'authGuard']);
    $routes->post('update-player-position', 'Team::update_player_position', ['filter' => 'authGuard']);
    $routes->post('remove-player-position', 'Team::remove_player_position', ['filter' => 'authGuard']);
    $routes->post('save-chart-selection', 'Team::save_chart_selection', ['filter' => 'authGuard']);
    $routes->post('save-coach-chart-selection', 'Team::save_coach_chart_selection', ['filter' => 'authGuard']);
    $routes->post('submit-player-list', 'Team::submitPlayerList', ['filter' => 'authGuard']);
    $routes->get('get-chart-selection/(:num)', 'Team::get_chart_selection/$1', ['filter' => 'authGuard']);
    $routes->get('get-old-chart-selection/(:num)/(:num)', 'Team::get_old_chart_selection/$1/$2', ['filter' => 'authGuard']);
    $routes->get('get-coach-chart-selection/(:num)', 'Team::get_coach_chart_selection/$1', ['filter' => 'authGuard']);
    $routes->get('get-submitted-players/(:num)', 'Team::getSubmittedPlayers/$1', ['filter' => 'authGuard']);
    $routes->get('get-player/(:num)', 'Team::get_player/$1', ['filter' => 'authGuard']);

    // Jersey — upload/remove เดิมยกเลิกแล้ว (เขียนฟิลด์ jersey_* ที่กำลังถูกลบ)
    // ชุดทีมแก้ที่ tournament_team ผ่านหน้า player-chart แทน
    $routes->get('get-jerseys/(:num)', 'Team::getJerseys/$1', ['filter' => 'authGuard']);

    $routes->get('coach-list', 'Team::coach_list', ['filter' => 'authGuard']);
    $routes->get('coach-list/(:num)', 'Team::coach_list/$1', ['filter' => 'authGuard']);

    $routes->get('user-manage', 'Team::manage_user', ['filter' => 'authGuard']);
    $routes->post('user-save', 'Team::save_user', ['filter' => 'authGuard']);

    // export
    $routes->get('player-document-list', 'Team::export_player_document');
    $routes->get('export-player-chart', 'Team::export_player_chart');
    $routes->get('export-player-card', 'Team::export_player_card');
    $routes->get('export-manager-card', 'Team::export_manager_card');

    // Join Team & Manager Requests
    $routes->get('join', 'Team::join_team_page', ['filter' => 'authGuard']);
    $routes->get('api/search', 'Team::api_search', ['filter' => 'authGuard']);
    $routes->post('api/join-request', 'Team::api_join_request', ['filter' => 'authGuard']);
    $routes->get('requests', 'Team::requests', ['filter' => 'authGuard']);
    $routes->get('requests/(:num)', 'Team::requests/$1', ['filter' => 'authGuard']);
    $routes->get('request/approve/(:num)', 'Team::approve_request/$1', ['filter' => 'authGuard']);
    $routes->get('request/reject/(:num)', 'Team::reject_request/$1', ['filter' => 'authGuard']);
    $routes->post('remove-manager/(:num)', 'Team::remove_manager/$1', ['filter' => 'authGuard']);

    // Coach Join Requests Management (Team Side)
    $routes->get('coach-requests', 'Team::coachRequests', ['filter' => 'authGuard']);
    $routes->get('coach-requests/(:num)', 'Team::coachRequests/$1', ['filter' => 'authGuard']);
    $routes->post('coach-request/approve/(:num)', 'Team::approveCoachRequest/$1', ['filter' => 'authGuard']);
    $routes->post('coach-request/reject/(:num)', 'Team::rejectCoachRequest/$1', ['filter' => 'authGuard']);

    // Notifications (เจ้าของทีม / regular user)
    $routes->get('notifications', 'Team::notifications', ['filter' => 'authGuard']);
    $routes->get('notifications/count', 'Team::notifications_count', ['filter' => 'authGuard']);
    $routes->post('notifications/mark-read', 'Team::notifications_mark_read', ['filter' => 'authGuard']);
    $routes->post('notifications/mark-all-read', 'Team::notifications_mark_all_read', ['filter' => 'authGuard']);

    // Sub-Team Management
    $routes->get('subteam/(:num)', 'Team::subteam_manage/$1', ['filter' => 'authGuard']);
    $routes->get('subteam/(:num)/(:num)', 'Team::subteam_manage/$1/$2', ['filter' => 'authGuard']);
    $routes->post('subteam-save', 'Team::save_subteam', ['filter' => 'authGuard']);
});

// Lineup Module (Independent - not under academy group)
$routes->group('lineup', ['namespace' => '\Modules\Academy\Controllers'], function ($routes) {
    $routes->get('', 'Lineup::index', ['filter' => 'authGuard']);
    $routes->get('get-tournaments', 'Lineup::getTournaments', ['filter' => 'authGuard']);
    $routes->get('get-matches', 'Lineup::getMatches', ['filter' => 'authGuard']);
    $routes->get('form', 'Lineup::form', ['filter' => 'authGuard']);
    $routes->post('save', 'Lineup::save', ['filter' => 'authGuard']);
    $routes->get('load', 'Lineup::load', ['filter' => 'authGuard']);
    $routes->get('(:num)', 'Lineup::index/$1', ['filter' => 'authGuard']);
});

// Academy Module (1 Academy = many Teams)
$routes->group('academy', ['namespace' => '\Modules\Academy\Controllers'], function ($routes) {
    // Super academy switcher (Phase 1: super user เลือก academy ที่จะเข้าใช้งาน)
    $routes->get('select',         'Academy::selectAcademy',     ['filter' => 'superAcademyGuard']);
    $routes->get('select-search',  'Academy::selectSearch',      ['filter' => 'superAcademyGuard']);
    // CI4 4.1.1 รองรับ filter เดียว/route → CSRF check ทำใน switchAcademy() เอง (state-changing endpoint)
    $routes->post('switch',        'Academy::switchAcademy',     ['filter' => 'superAcademyGuard']);

    // Academy CRUD
    $routes->get('register', 'Academy::register', ['filter' => 'authGuard']);
    $routes->post('post-register', 'Academy::post_register', ['filter' => 'authGuard']);
    $routes->get('info', 'Academy::index', ['filter' => 'authGuard']);
    $routes->get('info/(:num)', 'Academy::index/$1', ['filter' => 'authGuard']);
    $routes->get('manage', 'Academy::manage', ['filter' => 'authGuard']);
    $routes->get('manage/(:num)', 'Academy::manage/$1', ['filter' => 'authGuard']);

    // Team Management within Academy
    $routes->get('teams/(:num)', 'Academy::teams/$1', ['filter' => 'authGuard']);
    $routes->get('team-create/(:num)', 'Academy::teamCreate/$1', ['filter' => 'authGuard']);
    $routes->get('team-create/(:num)/(:num)', 'Academy::teamCreate/$1/$2', ['filter' => 'authGuard']);
    $routes->post('team-save', 'Academy::teamSave', ['filter' => 'authGuard']);
    $routes->get('team-info/(:num)/(:num)', 'Academy::teamInfo/$1/$2', ['filter' => 'authGuard']);

    $routes->get('team-player/manage/(:num)/(:num)', 'Academy::teamPlayerManage/$1/$2', ['filter' => 'authGuard']);
    $routes->post('team-player/save/(:num)/(:num)', 'Academy::teamPlayerSave/$1/$2', ['filter' => 'authGuard']);
    $routes->get('team-coach/manage/(:num)/(:num)', 'Academy::teamCoachManage/$1/$2', ['filter' => 'authGuard']);
    $routes->post('team-coach/save/(:num)/(:num)', 'Academy::teamCoachSave/$1/$2', ['filter' => 'authGuard']);
    $routes->get('team-jersey/(:num)/(:num)', 'Academy::teamJersey/$1/$2', ['filter' => 'authGuard']);
    $routes->post('team-jersey/save/(:num)/(:num)', 'Academy::saveTeamJersey/$1/$2', ['filter' => 'authGuard']);
    // ชุดทีมระดับทัวร์นาเมนต์ (tournament_team) — ใช้ในหน้า player-chart
    $routes->get('tournament-jersey/(:num)/(:num)', 'Academy::tournamentJersey/$1/$2', ['filter' => 'authGuard']);
    $routes->post('tournament-jersey/save/(:num)/(:num)', 'Academy::saveTournamentJersey/$1/$2', ['filter' => 'authGuard']);
    $routes->post('team-remove', 'Academy::teamRemove', ['filter' => 'authGuard']);

    // Team Roster Board (drag-drop / click-to-move)
    $routes->post('team-add-player', 'Academy::teamAddPlayer', ['filter' => 'authGuard']);
    $routes->post('team-remove-player', 'Academy::teamRemovePlayer', ['filter' => 'authGuard']);

    // Player views (team-level within academy)
    $routes->get('player-list/(:num)/(:num)', 'Academy::player_list/$1/$2', ['filter' => 'authGuard']);
    $routes->get('player-chart/(:num)', 'Academy::football_player_chart/$1', ['filter' => 'authGuard']);
    $routes->get('player-chart/(:num)/(:num)', 'Academy::football_player_chart/$1/$2', ['filter' => 'authGuard']);
    // รายการทัวร์นาเมนต์ที่ยัง active ของ academy — ใช้กับเมนู "แผงรูป" บน subnav
    $routes->get('chart-tournaments/(:num)', 'Academy::chartTournaments/$1', ['filter' => 'authGuard']);

    // Academy-wide views
    $routes->get('players/(:num)', 'Academy::players/$1', ['filter' => 'authGuard']);
    $routes->get('coaches/(:num)', 'Academy::coaches/$1', ['filter' => 'authGuard']);
    $routes->get('coach-list/(:num)/(:num)', 'Academy::coach_list/$1/$2', ['filter' => 'authGuard']);
    $routes->get('football-stats/(:num)', 'Academy::footballStatsDashboard/$1', ['filter' => 'authGuard']);

    // Academy Members Management
    $routes->get('members/(:num)', 'Academy::members/$1', ['filter' => 'authGuard']);
    $routes->post('member-save', 'Academy::memberSave', ['filter' => 'authGuard']);
    $routes->post('member-remove/(:num)', 'Academy::memberRemove/$1', ['filter' => 'authGuard']);

    // Permission Guide & Toggle
    $routes->get('permissions/(:num)', 'Academy::permissions/$1', ['filter' => 'authGuard']);
    $routes->post('permission-toggle', 'Academy::permissionToggle', ['filter' => 'authGuard']);

    // Player Position/Formation APIs (team-level, JSON)
    $routes->post('assign-player-position', 'Academy::assign_player_position', ['filter' => 'authGuard']);
    $routes->post('update-player', 'Academy::update_player', ['filter' => 'authGuard']);
    $routes->post('update-player-position', 'Academy::update_player_position', ['filter' => 'authGuard']);
    $routes->post('remove-player-position', 'Academy::remove_player_position', ['filter' => 'authGuard']);
    $routes->get('get-player/(:num)', 'Academy::get_player/$1', ['filter' => 'authGuard']);

    // Chart Selection APIs
    $routes->post('save-chart-selection', 'Academy::save_chart_selection', ['filter' => 'authGuard']);
    $routes->get('get-chart-selection/(:num)', 'Academy::get_chart_selection/$1', ['filter' => 'authGuard']);
    $routes->get('get-old-chart-selection/(:num)/(:num)', 'Academy::get_old_chart_selection/$1/$2', ['filter' => 'authGuard']);
    $routes->post('save-coach-chart-selection', 'Academy::save_coach_chart_selection', ['filter' => 'authGuard']);
    $routes->get('get-coach-chart-selection/(:num)', 'Academy::get_coach_chart_selection/$1', ['filter' => 'authGuard']);

    // Tournament Player Submission
    $routes->post('submit-player-list', 'Academy::submitPlayerList', ['filter' => 'authGuard']);
    $routes->get('get-submitted-players/(:num)', 'Academy::getSubmittedPlayers/$1', ['filter' => 'authGuard']);
    $routes->get('get-source-eligibility/(:num)/(:num)', 'Academy::getSourceEligibility/$1/$2', ['filter' => 'authGuard']);

    // Academy-wide player DataTable
    $routes->post('get-academy-players/(:num)', 'Academy::getAcademyPlayersDataTable/$1', ['filter' => 'authGuard']);
    // Players in a specific team within academy
    $routes->post('get-team-players/(:num)/(:num)', 'Academy::getAcademyTeamPlayersDataTable/$1/$2', ['filter' => 'authGuard']);

    // Jersey — upload/remove เดิมยกเลิกแล้ว (เขียนฟิลด์ jersey_* ที่กำลังถูกลบ)
    // ชุดทีมแก้ที่ tournament_team ผ่านหน้า player-chart แทน
    $routes->post('update-coach-role', 'Academy::updateCoachRole', ['filter' => 'authGuard']);
    $routes->get('get-jerseys/(:num)', 'Academy::getJerseys/$1', ['filter' => 'authGuard']);

    // Export (auth needed for PDPA compliance)
    $routes->get('player-document-list', 'Academy::export_player_document', ['filter' => 'authGuard']);
    $routes->get('export-player-chart', 'Academy::export_player_chart', ['filter' => 'authGuard']);
    $routes->get('export-player-card', 'Academy::export_player_card', ['filter' => 'authGuard']);
    $routes->get('user-guide-pdf', 'Academy::exportUserGuidePdf');

    // User Profile Management
    $routes->get('user-manage', 'Academy::manage_user', ['filter' => 'authGuard']);
    $routes->post('user-save', 'Academy::save_user', ['filter' => 'authGuard']);

    // Join Academy & Member Requests
    $routes->get('join', 'Academy::join_team_page', ['filter' => 'authGuard']);
    $routes->get('api/search', 'Academy::api_search', ['filter' => 'authGuard']);
    $routes->post('api/join-request', 'Academy::api_join_request', ['filter' => 'authGuard']);
    $routes->get('requests', 'Academy::requests', ['filter' => 'authGuard']);
    $routes->get('requests/(:num)', 'Academy::requests/$1', ['filter' => 'authGuard']);
    $routes->get('request/approve/(:num)', 'Academy::approve_request/$1', ['filter' => 'authGuard']);
    $routes->get('request/reject/(:num)', 'Academy::reject_request/$1', ['filter' => 'authGuard']);
    $routes->post('remove-manager/(:num)', 'Academy::remove_manager/$1', ['filter' => 'authGuard']);
    $routes->post('member-search', 'Academy::memberSearch', ['filter' => 'authGuard']);

    // Coach Search & Invite
    $routes->post('coach-search', 'Academy::coachSearch', ['filter' => 'authGuard']);
    $routes->post('coach-invite', 'Academy::coachInvite', ['filter' => 'authGuard']);

    // Coach Join Requests Management (team-level)
    $routes->get('coach-requests/(:num)', 'Academy::coachRequests/$1', ['filter' => 'authGuard']);
    $routes->get('coach-requests/(:num)/(:num)', 'Academy::coachRequests/$1/$2', ['filter' => 'authGuard']);
    $routes->post('coach-request/approve/(:num)', 'Academy::approveCoachRequest/$1', ['filter' => 'authGuard']);
    $routes->post('coach-request/reject/(:num)', 'Academy::rejectCoachRequest/$1', ['filter' => 'authGuard']);

    // Training Groups
    $routes->get('training-groups/(:num)', 'Academy::trainingGroups/$1', ['filter' => 'authGuard']);
    $routes->get('training-group/manage/(:num)', 'Academy::trainingGroupManage/$1', ['filter' => 'authGuard']);
    $routes->get('training-group/manage/(:num)/(:num)', 'Academy::trainingGroupManage/$1/$2', ['filter' => 'authGuard']);
    $routes->post('training-group/save', 'Academy::trainingGroupSave', ['filter' => 'authGuard']);
    $routes->post('training-group/delete', 'Academy::trainingGroupDelete', ['filter' => 'authGuard']);
    $routes->post('training-group/assign-coach', 'Academy::assignGroupCoach', ['filter' => 'authGuard']);
    $routes->post('training-group/remove-coach', 'Academy::removeGroupCoach', ['filter' => 'authGuard']);
    $routes->post('training-group/assign-player', 'Academy::assignGroupPlayer', ['filter' => 'authGuard']);
    $routes->post('training-group/remove-player', 'Academy::removeGroupPlayer', ['filter' => 'authGuard']);
    $routes->get('training-group/player-manage/(:num)/(:num)', 'Academy::trainingGroupPlayerManage/$1/$2', ['filter' => 'authGuard']);
    $routes->post('training-group/player-save/(:num)/(:num)', 'Academy::trainingGroupPlayerSave/$1/$2', ['filter' => 'authGuard']);

    // Training Sessions
    $routes->get('training-sessions/(:num)', 'Academy::trainingSessions/$1', ['filter' => 'authGuard']);
    $routes->get('training-session/manage/(:num)', 'Academy::trainingSessionManage/$1', ['filter' => 'authGuard']);
    $routes->get('training-session/manage/(:num)/(:num)', 'Academy::trainingSessionManage/$1/$2', ['filter' => 'authGuard']);
    $routes->post('training-session/save', 'Academy::trainingSessionSave', ['filter' => 'authGuard']);
    $routes->post('training-session/delete', 'Academy::trainingSessionDelete', ['filter' => 'authGuard']);
    $routes->get('training-session/events/(:num)', 'Academy::trainingSessionEvents/$1', ['filter' => 'authGuard']);
    $routes->post('training-session/duplicate', 'Academy::trainingSessionDuplicate', ['filter' => 'authGuard']);
    $routes->post('training-session/duplicate-day', 'Academy::trainingSessionDuplicateDay', ['filter' => 'authGuard']);
    $routes->get('training-session/detail/(:num)/(:num)', 'Academy::trainingSessionDetail/$1/$2', ['filter' => 'authGuard']);
    $routes->post('training-session/attachment/delete', 'Academy::deleteSessionAttachment', ['filter' => 'authGuard']);
    $routes->get('training-session/attachment/download/(:num)', 'Academy::downloadSessionAttachment/$1', ['filter' => 'authGuard']);
    $routes->get('training-attendance/(:num)/(:num)', 'Academy::trainingAttendance/$1/$2', ['filter' => 'authGuard']);
    $routes->post('training-attendance/save', 'Academy::saveAttendance', ['filter' => 'authGuard']);

    // Training Topics
    $routes->get('training-topics/(:num)', 'Academy::trainingTopics/$1', ['filter' => 'authGuard']);
    $routes->get('training-topic/manage/(:num)', 'Academy::trainingTopicManage/$1', ['filter' => 'authGuard']);
    $routes->get('training-topic/manage/(:num)/(:num)', 'Academy::trainingTopicManage/$1/$2', ['filter' => 'authGuard']);
    $routes->post('training-topic/save', 'Academy::trainingTopicSave', ['filter' => 'authGuard']);
    $routes->post('training-topic/delete', 'Academy::trainingTopicDelete', ['filter' => 'authGuard']);

    // Assessments
    $routes->get('assessments/(:num)', 'Academy::assessments/$1', ['filter' => 'authGuard']);
    $routes->get('assessment/manage/(:num)', 'Academy::assessmentManage/$1', ['filter' => 'authGuard']);
    $routes->get('assessment/manage/(:num)/(:num)', 'Academy::assessmentManage/$1/$2', ['filter' => 'authGuard']);
    $routes->post('assessment/save', 'Academy::assessmentSave', ['filter' => 'authGuard']);
    $routes->post('assessment/delete', 'Academy::assessmentDelete', ['filter' => 'authGuard']);
    $routes->get('assessment/detail/(:num)/(:num)', 'Academy::assessmentDetail/$1/$2', ['filter' => 'authGuard']);
    $routes->post('assessment/save-results', 'Academy::saveAssessmentResults', ['filter' => 'authGuard']);

    // Reports
    $routes->get('reports/(:num)', 'Academy::reports/$1', ['filter' => 'authGuard']);
    $routes->get('report-daily/(:num)', 'Academy::reportDaily/$1', ['filter' => 'authGuard']);
    $routes->get('report-monthly/(:num)', 'Academy::reportMonthly/$1', ['filter' => 'authGuard']);
    $routes->get('report-player/(:num)/(:num)', 'Academy::reportPlayer/$1/$2', ['filter' => 'authGuard']);
    $routes->get('idp-radar-data/(:num)/(:num)', 'Academy::getIDPRadarData/$1/$2', ['filter' => 'authGuard']);
    $routes->get('report-group/(:num)/(:num)', 'Academy::reportGroup/$1/$2', ['filter' => 'authGuard']);

    // IDP (Individual Development Plan)
    $routes->get('idp/(:num)', 'Academy::idpOverview/$1', ['filter' => 'authGuard']);
    $routes->get('idp/plan/view/(:num)/(:num)', 'Academy::idpPlanView/$1/$2', ['filter' => 'authGuard']);
    $routes->get('idp/plan/manage/(:num)/(:num)', 'Academy::idpPlanManage/$1/$2', ['filter' => 'authGuard']);
    $routes->get('idp/plan/manage/(:num)/(:num)/(:num)', 'Academy::idpPlanManage/$1/$2/$3', ['filter' => 'authGuard']);
    $routes->post('idp/plan/save', 'Academy::idpPlanSave', ['filter' => 'authGuard']);
    $routes->post('idp/plan/delete', 'Academy::idpPlanDelete', ['filter' => 'authGuard']);
    $routes->post('idp/goal/save', 'Academy::idpGoalSave', ['filter' => 'authGuard']);
    $routes->post('idp/goal/delete', 'Academy::idpGoalDelete', ['filter' => 'authGuard']);
    $routes->post('idp/progress/save', 'Academy::idpProgressSave', ['filter' => 'authGuard']);
    $routes->get('idp/assessment/(:num)/(:num)', 'Academy::idpAssessmentView/$1/$2', ['filter' => 'authGuard']);
    $routes->post('idp/assessment/save', 'Academy::idpAssessmentSave', ['filter' => 'authGuard']);
    $routes->post('idp/feedback/save', 'Academy::idpFeedbackSave', ['filter' => 'authGuard']);
    $routes->post('idp/feedback/delete', 'Academy::idpFeedbackDelete', ['filter' => 'authGuard']);
    $routes->get('idp/report/(:num)/(:num)', 'Academy::idpReport/$1/$2', ['filter' => 'authGuard']);

    // IDP Group (Batch)
    $routes->get('idp/group/create/(:num)', 'Academy::idpGroupCreate/$1', ['filter' => 'authGuard']);
    $routes->get('idp/group/edit/(:num)/(:num)', 'Academy::idpGroupCreate/$1/$2', ['filter' => 'authGuard']);
    $routes->post('idp/group/save', 'Academy::idpGroupSave', ['filter' => 'authGuard']);
    $routes->post('idp/group/plan/delete', 'Academy::idpGroupPlanDelete', ['filter' => 'authGuard']);
    $routes->get('idp/group/assess/history', 'Academy::idpGroupAssessHistory', ['filter' => 'authGuard']);
    $routes->get('idp/group/assess/(:num)/(:num)', 'Academy::idpGroupAssess/$1/$2', ['filter' => 'authGuard']);
    $routes->get('idp/group/players/(:num)', 'Academy::getGroupPlayersJSON/$1', ['filter' => 'authGuard']);
    $routes->get('idp/dimension-detail', 'Academy::idpDimensionDetail', ['filter' => 'authGuard']);
    $routes->get('idp/group/report/(:num)/(:num)', 'Academy::idpGroupReport/$1/$2', ['filter' => 'authGuard']);

    // IDP Group Indicator-Level Assessment
    $routes->get('idp/group/assess-indicators/(:num)/(:num)', 'Academy::idpGroupAssessIndicators/$1/$2', ['filter' => 'authGuard']);
    $routes->post('idp/group/assess-indicators/save', 'Academy::idpGroupAssessIndicatorsSave', ['filter' => 'authGuard']);

    // IDP Library (Template Management)
    $routes->get('idp/library/(:num)', 'Academy::idpLibrary/$1', ['filter' => 'authGuard']);
    $routes->get('idp/template/manage/(:num)', 'Academy::idpTemplateManage/$1', ['filter' => 'authGuard']);
    $routes->get('idp/template/manage/(:num)/(:num)', 'Academy::idpTemplateManage/$1/$2', ['filter' => 'authGuard']);
    $routes->post('idp/template/save', 'Academy::idpTemplateSave', ['filter' => 'authGuard']);
    $routes->post('idp/template/delete', 'Academy::idpTemplateDelete', ['filter' => 'authGuard']);
    $routes->post('idp/template/clone', 'Academy::idpTemplateClone', ['filter' => 'authGuard']);
    $routes->get('idp/template/detail/(:num)/(:num)', 'Academy::idpTemplateDetail/$1/$2', ['filter' => 'authGuard']);
    $routes->get('idp/dimension/manage/(:num)/(:num)', 'Academy::idpDimensionManage/$1/$2', ['filter' => 'authGuard']);
    $routes->get('idp/dimension/manage/(:num)/(:num)/(:num)', 'Academy::idpDimensionManage/$1/$2/$3', ['filter' => 'authGuard']);
    $routes->post('idp/dimension/save', 'Academy::idpDimensionSave', ['filter' => 'authGuard']);
    $routes->post('idp/dimension/delete', 'Academy::idpDimensionDelete', ['filter' => 'authGuard']);
    $routes->get('idp/indicator/manage/(:num)/(:num)', 'Academy::idpIndicatorManage/$1/$2', ['filter' => 'authGuard']);
    $routes->get('idp/indicator/manage/(:num)/(:num)/(:num)', 'Academy::idpIndicatorManage/$1/$2/$3', ['filter' => 'authGuard']);
    $routes->post('idp/indicator/save', 'Academy::idpIndicatorSave', ['filter' => 'authGuard']);
    $routes->post('idp/indicator/delete', 'Academy::idpIndicatorDelete', ['filter' => 'authGuard']);

    // IDP AI Analysis
    $routes->post('idp/ai-analyze', 'Academy::idpAIAnalyze', ['filter' => 'authGuard']);
    $routes->get('idp/ai-history/(:num)/(:num)', 'Academy::getAIAnalysisHistory/$1/$2', ['filter' => 'authGuard']);
    $routes->get('idp/ai-detail/(:num)', 'Academy::getAIAnalysisDetail/$1', ['filter' => 'authGuard']);

    // Multi-Academy Player Enrollment
    $routes->get('player-search/(:num)', 'Academy::playerSearch/$1', ['filter' => 'authGuard']);
    $routes->post('player-enroll/(:num)', 'Academy::playerEnroll/$1', ['filter' => 'authGuard']);
    $routes->post('player-unenroll/(:num)', 'Academy::playerUnenroll/$1', ['filter' => 'authGuard']);
    $routes->get('player-edit/(:num)/(:num)', 'Academy::playerEdit/$1/$2', ['filter' => 'authGuard']);
    $routes->post('player-save/(:num)', 'Academy::playerSave/$1', ['filter' => 'authGuard']);
    $routes->post('player-upload-image', 'Academy::playerUploadImage', ['filter' => 'authGuard']);
    $routes->post('player-shirt/(:num)/(:num)', 'Academy::playerShirtUpdate/$1/$2', ['filter' => 'authGuard']);
    $routes->get('player-view/(:num)/(:num)', 'Academy::playerView/$1/$2', ['filter' => 'authGuard']);
    $routes->get('player-history/(:num)/(:num)', 'Academy::playerHistory/$1/$2', ['filter' => 'authGuard']);
    $routes->get('player-physical-history/(:num)/(:num)', 'Academy::playerPhysicalHistory/$1/$2', ['filter' => 'authGuard']);
});

$routes->group('player', ['namespace' => '\Modules\Player\Controllers', 'filter' => 'authGuard'], function ($routes) {
    $routes->get('manage', 'Player::manage');
    $routes->get('manage/(:any)', 'Player::manage/$1');
    $routes->get('manage_team/(:num)', 'Player::manage_by_team/$1');
    $routes->post('get-players', 'Player::ajax_players');
    $routes->post('save-player', 'Player::save_player');
    $routes->get('check-id-card', 'Player::check_id_card');
    $routes->post('delete', 'Player::delete_player');

    // Search and assign players (multi-team support)
    $routes->post('search-available-players', 'Player::search_available_players');
    $routes->post('assign-player-to-team', 'Player::assign_player_to_team');
    $routes->post('add-player-to-team', 'Player::add_player_to_team');
    $routes->post('remove-player-from-team', 'Player::remove_player_from_team');
    $routes->post('get-player-teams', 'Player::get_player_teams');

    $routes->get('manage_coach', 'Player::manage_coach');
    $routes->get('manage_coach/(:any)', 'Player::manage_coach/$1');
    $routes->post('get-coachs', 'Player::ajax_coachs');
    $routes->post('get-all-coachs', 'Player::ajax_all_coachs');
    $routes->post('search-coaches', 'Player::search_coaches');
    $routes->post('invite-coach', 'Player::invite_coach');
    $routes->post('cancel-invitation', 'Player::cancel_invitation');
    $routes->get('my-invitations', 'Player::my_invitations');
    $routes->post('respond-invitation', 'Player::respond_invitation');
    $routes->post('save-coach', 'Player::save_coach');
    $routes->post('delete-coach', 'Player::delete_coach');
    $routes->post('remove-coach-from-club', 'Player::remove_coach_from_club');
    $routes->post('get-coach-profile', 'Player::getCoachProfile');
});

$routes->group('player_ex', ['namespace' => '\Modules\Player\Controllers'], function ($routes) {
    $routes->get('report_people/(:any)', 'Player::report_people/$1');
});

$routes->group('admin', ['namespace' => '\Modules\Admin\Controllers'], function ($routes) {
    //General
    $routes->get('/', 'AdminAuth::login');
    $routes->get('login', 'AdminAuth::login');
    $routes->post('sign-in', 'AdminAuth::signin');
    $routes->get('logout', 'AdminAuth::logout');


    $routes->group('coach', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:coach.view'], function ($routes) {
        $routes->get('/', 'Coach::index');

        //coach
        $routes->get('coach-manage', 'Coach::coach_manage');
        $routes->get('coach-manage/(:any)', 'Coach::coach_manage/$1');
        $routes->get('get-coachs', 'Coach::ajax_coachs'); //AJAX
        $routes->get('impersonate/(:num)', 'UserManager::impersonateCoach/$1');
        $routes->post('save-coach', 'Coach::save_coach');
        $routes->post('delete-coach', 'Coach::delete_coach');
        //

        //cert
        $routes->get('certs-manage/(:any)', 'Coach::certs_manage/$1');
        $routes->get('get-certs/(:any)', 'Coach::ajax_certs/$1'); //AJAX
        $routes->post('save-certs', 'Coach::save_certs');
        $routes->post('delete-certs', 'Coach::delete_certs');
        //


    });

    $routes->group('dashboard', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:dashboard.view'], function ($routes) {
        $routes->get('/', 'Dashboard::index');
        $routes->post('get-players', 'Dashboard::getPlayersAjax');
    });

    $routes->group('tournament', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:tournament.view'], function ($routes) {
        $routes->get('/', 'Tournament::index');

        //Tour manage
        $routes->get('tournament-manage', 'Tournament::tour_manage');
        $routes->get('tournament-manage/(:any)', 'Tournament::tour_manage/$1');
        $routes->get('get-tournaments', 'Tournament::ajax_tournaments'); //AJAX
        $routes->post('save-tournament', 'Tournament::save_tournament', ['filter' => 'permissionGuard:tournament.create,tournament.edit']);
        $routes->post('delete-tournament', 'Tournament::delete_tournament', ['filter' => 'permissionGuard:tournament.delete']);
        $routes->post('toggle-tournament-field', 'Tournament::toggle_tournament_field', ['filter' => 'permissionGuard:tournament.edit']);
        $routes->get('pending-chart-players/(:num)', 'Tournament::pending_chart_players/$1'); //AJAX
        $routes->post('delete-pending-chart-player', 'Tournament::delete_pending_chart_player', ['filter' => 'permissionGuard:tournament.delete']);
        $routes->get('clone/(:num)', 'Tournament::clone_tournament/$1');
        //

        //Team in Tour
        $routes->get('tournament-team/(:any)', 'Tournament::tournament_team/$1');
        $routes->get('tournament-team-manage/(:any)', 'Tournament::tournament_team_manage/$1');
        $routes->get('get-tournament-teams/(:any)', 'Tournament::ajax_tournament_teams/$1'); //AJAX
        $routes->post('save-tournament-team', 'Tournament::save_tournament_team', ['filter' => 'permissionGuard:tournament.edit']);
        $routes->post('delete-tournament-team', 'Tournament::delete_tournament_team', ['filter' => 'permissionGuard:tournament.delete']);
        $routes->post('change-register-status', 'Tournament::change_register_status');
        $routes->get('export_players/(:any)', 'Tournament::export_players/$1');
        $routes->get('export-players-csv/(:num)', 'Tournament::export_players_csv/$1');
        $routes->get('export-registration/(:any)', 'Tournament::export_registration/$1');
        $routes->get('export-registration-status/(:any)', 'Tournament::export_registration_status/$1');
        //

        //Ranking
        $routes->get('ranking/(:any)', 'Tournament::ranking/$1');
        $routes->post('update-stage-final-match','Tournament::update_stage_final_match');
        //

        //Tournament Match Result
        $routes->get('tournament-match-result/(:any)', 'Tournament::tournament_match_result/$1');
        $routes->get('tournament-match-result-manage/(:any)/(:any)', 'Tournament::tournament_match_result_manage/$1/$2');
        $routes->get('tournament-match-detail-manage/(:any)/(:any)', 'Tournament::tournament_match_detail_manage/$1/$2');
        $routes->get('get-tournament-match-results', 'Tournament::ajax_tournament_match_result'); //AJAX
        $routes->post('save-tournament-match-result', 'Tournament::save_tournament_match_result', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post('swap-match-teams', 'Tournament::swap_match_teams', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post('save-tournament-match-result-multi', 'Tournament::save_tournament_match_result_multi', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post('delete-tournament-match-result', 'Tournament::delete_tournament_match_result', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post("create-match-event-form", "Tournament::create_match_event_form");
        $routes->post('create-player-options', "Tournament::create_player_option");

        $routes->post("save-player-to-match", "PlayerState::savePlayerToMatch");

        $routes->post("manage-match-status", "PlayerState::savePlayerToMatch");

        //
        $routes->post("create-schedule-matches", "Tournament::create_match_tournament");
        //

        $routes->post("get-event-card", "Tournament::getInputEventCard");
        $routes->post('save-event', "Tournament::saveMatchEvent");
        $routes->post("delete-event", "Tournament::deleteEvent");
        $routes->post('updateMatchStatus', 'Tournament::updateMatchStatus');

        // Google Ai studio
        $routes->post('generate-matchs', 'Tournament::generate');

        // Match Setup - ตั้งค่าการสร้างแมตช์
        $routes->get('match-setup/(:any)', 'Tournament::match_setup/$1');
        $routes->get('get-tournament-stages/(:any)', 'Tournament::get_tournament_stages/$1');
        $routes->post('generate-matches', 'Tournament::generate_matches', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post('generate-matches-with-rounds', 'Tournament::generate_matches_with_rounds', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post('generate-matches-with-teams', 'Tournament::generate_matches_with_teams', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post('delete-match-round', 'Tournament::delete_match_round', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->get('assign-matches-by-group/(:any)', 'Tournament::assign_matches_by_group/$1');
        $routes->post('bulk-assign-teams', 'Tournament::bulk_assign_teams');
        $routes->get('get-match-details/(:any)/(:any)', 'Tournament::get_match_details/$1/$2');
        $routes->post('delete-match/(:num)', 'Tournament::delete_match/$1');

        // Tournament Stage Management
        $routes->get('get-tournament-stages/(:num)', 'Tournament::get_tournament_stages/$1');
        $routes->post('save-tournament-stage', 'Tournament::save_tournament_stage');
        $routes->post('update-stage-name', 'Tournament::update_stage_name');
        $routes->post('delete-tournament-stage/(:num)', 'Tournament::delete_tournament_stage/$1');

        // Import match-setup from another tournament
        $routes->get('get-tournaments-with-stages', 'Tournament::get_tournaments_with_stages');
        $routes->post('import-match-setup', 'Tournament::import_match_setup');
        $routes->post('update-stage-teams', 'Tournament::update_stage_teams');

        // Match Management - จัดการแมตช์แบบ single-page (รองรับการสร้างรอบต่อเนื่อง)

        // Score Management - จัดการคะแนน
        $routes->get('score-management/(:any)', 'Tournament::score_management/$1');
        $routes->post('save-match-score', 'Tournament::save_match_score', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post('end-match-with-score', 'Tournament::end_match_with_score', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->get('get-matches-for-scoring/(:any)', 'Tournament::get_matches_for_scoring/$1');

        // Score Management v2 - Lineup & Events
        $routes->get('get-match-lineup/(:num)', 'Tournament::get_match_lineup/$1');
        $routes->post('save-match-lineup', 'Tournament::save_match_lineup', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->get('get-match-events/(:num)', 'Tournament::get_match_events/$1');
        $routes->post('add-match-event', 'Tournament::add_match_event', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post('add-match-substitutions', 'Tournament::add_match_substitutions', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->post('remove-match-event', 'Tournament::remove_match_event', ['filter' => 'permissionGuard:tournament.manage_matches']);
        $routes->get('get-team-players/(:num)/(:num)', 'Tournament::get_team_players/$1/$2');
        $routes->get('get-match-players/(:num)/(:num)', 'Tournament::get_match_players/$1/$2');
        $routes->get('export-match-result/(:num)', 'Tournament::export_match_result/$1');

        //Tournament Program
        $routes->get('tournament-program/(:any)', 'Tournament::program/$1');
        $routes->post('delete-match', 'Tournament::delete_match', ['filter' => 'permissionGuard:tournament.edit']);
        $routes->post('save-tournament-mvp', 'Tournament::save_tournament_mvp', ['filter' => 'permissionGuard:tournament.edit']);
        $routes->get('get-tournament-ranking', 'Tournament::get_tournament_ranking');
        $routes->post('save-tournament-ranking', 'Tournament::save_tournament_ranking', ['filter' => 'permissionGuard:tournament.edit']);
    });


    //Tournament Program (Front)
    $routes->group('tournament-pg', ['namespace' => '\Modules\Admin\Controllers'], function ($routes) {
        $routes->get('get-tournament-players', 'Tournament::ajax_tournament_players'); //AJAX
        $routes->get('get-tournament-match-for-prog-rank-result', 'Tournament::ajax_tournament_match_for_prog_rank_result'); //AJAX
        $routes->get('get-tournament-list', 'Tournament::ajax_tournament_list'); //AJAX
        $routes->get('get-team-statistics', 'Tournament::ajax_team_statistics'); //AJAX
        $routes->get('get-tour-teams', 'Tournament::ajax_tour_teams'); //AJAX
        $routes->get('get-tournament-mvp', 'Tournament::ajax_tournament_mvp'); //AJAX
        $routes->get('get-tournament-final-ranking', 'Tournament::get_tournament_ranking'); //AJAX
        $routes->get('get-dashboard-statistic', 'Tournament::ajax_dashboard_statistic'); //AJAX
        $routes->get('get-map-statistic', 'Tournament::ajax_map_statistic'); //AJAX
    });
    //

    $routes->group('team', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:team.view'], function ($routes) {
        $routes->get('/', 'Team::index');
        $routes->post('get-teams', 'Team::ajax_teams');
        $routes->get('manage', 'Team::manage');
        $routes->get('manage/(:num)', 'Team::manage/$1');
        $routes->post('save', 'Team::saveTeam', ['filter' => 'permissionGuard:team.create,team.edit']);
        $routes->post('delete-team', 'Team::deletedTeam', ['filter' => 'permissionGuard:team.delete']);
    });

    // Team Requests Management
    $routes->group('team-requests', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:team.requests'], function ($routes) {
        $routes->get('/', 'Team::teamRequests');
        $routes->post('get-requests', 'Team::ajax_team_requests');
        $routes->post('approve/(:num)', 'Team::approveRequest/$1');
        $routes->post('reject/(:num)', 'Team::rejectRequest/$1');
    });

    // Team Verify Management (ตรวจสอบทีม - ลบทีมถาวร)
    $routes->group('team-verify', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:team.verify'], function ($routes) {
        $routes->get('/', 'Team::teamVerify');
        $routes->post('get-teams', 'Team::ajax_teams_verify');
        $routes->get('manage/(:num)', 'Team::manageVerify/$1');
        $routes->post('permanent-delete', 'Team::permanentDeleteTeam');
    });

    // Stadium Management
    $routes->group('stadium', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:stadium.view'], function ($routes) {
        $routes->get('/', 'Stadium::index');
        $routes->get('manage', 'Stadium::manage', ['filter' => 'permissionGuard:stadium.create,stadium.edit']);
        $routes->get('manage/(:any)', 'Stadium::manage/$1', ['filter' => 'permissionGuard:stadium.edit']);
        $routes->get('get-stadiums', 'Stadium::ajax_stadiums');
        $routes->post('save', 'Stadium::save', ['filter' => 'permissionGuard:stadium.create,stadium.edit']);
        $routes->post('delete', 'Stadium::delete', ['filter' => 'permissionGuard:stadium.delete']);
        $routes->post('upload-image', 'Stadium::upload_image', ['filter' => 'permissionGuard:stadium.edit']);
        $routes->post('delete-image', 'Stadium::delete_image', ['filter' => 'permissionGuard:stadium.edit']);
        $routes->post('set-cover-image', 'Stadium::set_cover_image', ['filter' => 'permissionGuard:stadium.edit']);
        $routes->post('quick-add', 'Stadium::quick_add', ['filter' => 'permissionGuard:stadium.create']);
    });

    // ID Card Template Management
    $routes->group('idcard', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:idcard.view'], function ($routes) {
        $routes->get('/', 'IdCard::index');
        $routes->get('manage', 'IdCard::manage');
        $routes->get('manage/(:num)', 'IdCard::manage/$1');
        $routes->get('get-idcards', 'IdCard::ajax_idcards');
        $routes->post('save', 'IdCard::save');
        $routes->post('delete', 'IdCard::delete');
        $routes->get('preview/(:num)', 'IdCard::preview/$1');
    });

    // Help Guide Management
    $routes->group('help-guide', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'adminGuard'], function ($routes) {
        $routes->get('/', 'HelpGuide::index');
        $routes->get('manage', 'HelpGuide::manage');
        $routes->get('manage/(:any)', 'HelpGuide::manage/$1');
        $routes->get('ajax-list', 'HelpGuide::ajax_list');
        $routes->post('save', 'HelpGuide::save');
        $routes->post('delete', 'HelpGuide::delete');
        $routes->post('toggle-status', 'HelpGuide::toggleStatus');
    });

    // Sponsors Management
    $routes->group('sponsors', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:sponsors.view'], function ($routes) {
        $routes->get('/', 'Sponsors::index');
        $routes->get('manage', 'Sponsors::manage', ['filter' => 'permissionGuard:sponsors.create,sponsors.edit']);
        $routes->get('manage/(:any)', 'Sponsors::manage/$1', ['filter' => 'permissionGuard:sponsors.edit']);
        $routes->get('ajax-sponsors', 'Sponsors::ajax_sponsors');
        $routes->post('save', 'Sponsors::save', ['filter' => 'permissionGuard:sponsors.create,sponsors.edit']);
        $routes->post('delete', 'Sponsors::delete', ['filter' => 'permissionGuard:sponsors.delete']);
        $routes->post('toggle-status', 'Sponsors::toggleStatus', ['filter' => 'permissionGuard:sponsors.edit']);
    });

    // IDP Management
    $routes->group('idp', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:idp.view'], function ($routes) {
        // Templates
        $routes->get('/', 'IDP::templates');
        $routes->get('templates', 'IDP::templates');
        $routes->post('templates/data', 'IDP::ajax_templates');
        $routes->get('template/manage', 'IDP::templateManage');
        $routes->get('template/manage/(:num)', 'IDP::templateManage/$1');
        $routes->post('template/save', 'IDP::templateSave');
        $routes->post('template/delete', 'IDP::templateDelete');
        $routes->get('template/detail/(:num)', 'IDP::templateDetail/$1');

        // Dimensions
        $routes->get('dimensions', 'IDP::dimensions');
        $routes->get('dimensions/(:num)', 'IDP::dimensions/$1');
        $routes->post('dimensions/data', 'IDP::ajax_dimensions');
        $routes->get('dimension/manage/(:num)', 'IDP::dimensionManage/$1');
        $routes->get('dimension/manage/(:num)/(:num)', 'IDP::dimensionManage/$1/$2');
        $routes->post('dimension/save', 'IDP::dimensionSave');
        $routes->post('dimension/delete', 'IDP::dimensionDelete');

        // Indicators
        $routes->get('indicators', 'IDP::indicators');
        $routes->get('indicators/(:num)', 'IDP::indicators/$1');
        $routes->post('indicators/data', 'IDP::ajax_indicators');
        $routes->get('indicator/manage/(:num)', 'IDP::indicatorManage/$1');
        $routes->get('indicator/manage/(:num)/(:num)', 'IDP::indicatorManage/$1/$2');
        $routes->post('indicator/save', 'IDP::indicatorSave');
        $routes->post('indicator/delete', 'IDP::indicatorDelete');
    });

    // Academy Management
    $routes->group('academy', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:academy.view'], function ($routes) {
        $routes->get('/', 'Academy::index');
        $routes->post('get-academies', 'Academy::ajax_academies');
        $routes->get('manage', 'Academy::manage');
        $routes->get('manage/(:num)', 'Academy::manage/$1');
        $routes->post('save', 'Academy::save');
        $routes->get('detail/(:num)', 'Academy::detail/$1');
        $routes->post('toggle-active', 'Academy::toggleActive');
        $routes->post('delete', 'Academy::deleteAcademy');
        // Merge Teams into Academy
        $routes->get('merge-teams', 'Academy::mergeTeams');
        $routes->post('ajax-available-teams', 'Academy::ajaxAvailableTeams');
        $routes->post('ajax-team-detail', 'Academy::ajaxTeamDetail');
        $routes->post('ajax-academies-list', 'Academy::ajaxAcademiesList');
        $routes->post('merge-preview', 'Academy::mergePreview');
        $routes->post('merge-execute', 'Academy::mergeExecute');
    });

    $routes->group('scout', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:scout.view'], function ($routes) {
        $routes->get('/', 'Scout::index');
        $routes->get('players-state-list/(:any)/(:any)', 'Scout::player_state_list/$1/$2');

        $routes->post('get-player-state', 'Scout::getPlayerState');
        $routes->post('save-player-state', 'Scout::savePlayerState');
    });

    $routes->group('report', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:report.view'], function ($routes) {
        $routes->get('report-players', 'Report::report_players');
        $routes->post('get-players', 'Report::ajax_get_players');
        $routes->get('player-details/(:any)', 'Report::report_details_player/$1');
        $routes->get('report-logs', 'Report::report_logs');
        $routes->post('get-logs', 'Report::ajax_get_logs');
    });


    $routes->group('report-guest', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:report.guest'], function ($routes) {
        $routes->get('report-players', 'ReportGuest::report_players');
        $routes->post('get-players', 'ReportGuest::ajax_get_players');
        $routes->get('player-details/(:any)', 'ReportGuest::report_details_player/$1');
    });

    // Access Control
    $routes->get('access-denied', 'AccessControl::accessDenied');

    // API Manager
    $routes->group('api-manager', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'adminGuard'], function ($routes) {
        $routes->get('/', 'ApiManager::index');
        $routes->post('clone-prod', 'ApiManager::cloneFromProd');
    });

    // User Management
    $routes->group('users', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:user.view'], function ($routes) {
        $routes->get('/', 'UserManager::index');
        $routes->post('get-users', 'UserManager::ajax_users');
        $routes->post('get-members', 'UserManager::ajax_members');
        $routes->get('get-user/(:num)', 'UserManager::getUser/$1');
        $routes->get('get-member/(:num)', 'UserManager::getMember/$1');
        $routes->get('impersonate/user/(:num)', 'UserManager::impersonateUser/$1');
        $routes->post('save-user', 'UserManager::saveUser', ['filter' => 'permissionGuard:user.edit']);
        $routes->post('save-member', 'UserManager::saveMember', ['filter' => 'permissionGuard:user.edit']);
        $routes->post('delete-user', 'UserManager::deleteUser', ['filter' => 'permissionGuard:user.edit']);
        $routes->post('delete-member', 'UserManager::deleteMember', ['filter' => 'permissionGuard:user.edit']);
    });

    $routes->get('return-to-admin', 'UserManager::returnToAdmin', ['filter' => 'adminGuard']);

    // ให้สิทธิ์แบบกดครั้งเดียว (ใช้จากหน้า admin/users และ admin/coach)
    $routes->group('access', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'adminGuard'], function ($routes) {
        $routes->get('search-academy', 'AccessGrant::searchAcademy');
        $routes->get('search-team', 'AccessGrant::searchTeam');
        $routes->get('user-academies/(:num)', 'AccessGrant::userAcademies/$1');
        $routes->get('coach-teams/(:num)', 'AccessGrant::coachTeams/$1');
        $routes->post('grant-academy', 'AccessGrant::grantAcademy');
        $routes->post('revoke-academy', 'AccessGrant::revokeAcademy');
        $routes->post('grant-team', 'AccessGrant::grantTeam');
        $routes->post('revoke-team', 'AccessGrant::revokeTeam');
    });

    // Player Merge (dedup review)
    $routes->group('player-merge', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'adminGuard'], function ($routes) {
        $routes->get('/', 'PlayerMerge::index');
        $routes->get('compare', 'PlayerMerge::compare');
        $routes->post('do-merge', 'PlayerMerge::doMerge');
    });

    // Role Management
    $routes->group('role', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:role.view'], function ($routes) {
        $routes->get('/', 'RoleManager::index');
        $routes->get('manage', 'RoleManager::manage', ['filter' => 'permissionGuard:role.create']);
        $routes->get('manage/(:num)', 'RoleManager::manage/$1', ['filter' => 'permissionGuard:role.edit']);
        $routes->post('save', 'RoleManager::save', ['filter' => 'permissionGuard:role.create,role.edit']);
        $routes->post('delete', 'RoleManager::delete', ['filter' => 'permissionGuard:role.delete']);
        $routes->post('get-roles', 'RoleManager::getRoles');

        // User Role Management
        $routes->get('user-roles', 'RoleManager::userRoles');
        $routes->get('assign-user-roles/(:num)', 'RoleManager::assignUserRoles/$1', ['filter' => 'permissionGuard:user.edit']);
        $routes->post('save-user-roles', 'RoleManager::saveUserRoles', ['filter' => 'permissionGuard:user.edit']);

        // User CRUD
        $routes->post('save-user', 'RoleManager::saveUser', ['filter' => 'permissionGuard:user.edit']);
        $routes->post('delete-user', 'RoleManager::deleteUser', ['filter' => 'permissionGuard:user.edit']); // Using user.edit for now as safe default if user.delete isn't defined
        $routes->get('get-user/(:num)', 'RoleManager::getUser/$1', ['filter' => 'permissionGuard:user.edit']);
    });

    // Settings routes
    $routes->group('settings', ['namespace' => '\Modules\Admin\Controllers', 'filter' => 'permissionGuard:settings.view'], function ($routes) {
        $routes->get('email', 'Settings::email_settings');
        $routes->post('save-email', 'Settings::save_email_settings');
        $routes->post('test-email', 'Settings::test_email');
    });
});

// ============================================================
// Football Analytics - Admin only
// ============================================================
$routes->group('admin/analytics', ['namespace' => '\Modules\Analytics\Controllers', 'filter' => 'adminGuard'], function ($routes) {
    // Dashboard
    $routes->get('/', 'Analytics::index');
    $routes->get('dashboard', 'Analytics::index');

    // Match Management
    $routes->match(['get', 'post'], 'matches', 'MatchAnalytics::index');
    $routes->get('matches/manage', 'MatchAnalytics::manage');
    $routes->get('matches/manage/(:num)', 'MatchAnalytics::manage/$1');
    $routes->post('matches/save', 'MatchAnalytics::save');
    $routes->get('matches/detail/(:num)', 'MatchAnalytics::detail/$1');
    $routes->post('matches/process/(:num)', 'MatchAnalytics::process/$1');
    $routes->get('matches/check-status/(:num)', 'MatchAnalytics::checkStatus/$1');
    $routes->post('matches/delete/(:num)', 'MatchAnalytics::delete/$1');
    $routes->post('matches/video/delete/(:num)', 'MatchAnalytics::deleteVideo/$1');
    $routes->post('matches/map-player', 'MatchAnalytics::mapPlayer');

    // Player Analytics
    $routes->get('players', 'PlayerAnalytics::stats');
    $routes->post('players/data', 'PlayerAnalytics::ajaxData');
    $routes->get('players/stats-json/(:num)', 'PlayerAnalytics::statsJson/$1');
    $routes->get('players/detail-json/(:num)', 'PlayerAnalytics::detailJson/$1');

    // Analysis Views
    $routes->get('analysis/heatmap/(:num)', 'AnalysisView::heatmap/$1');
    $routes->get('analysis/speed/(:num)', 'AnalysisView::speed/$1');
    $routes->get('analysis/compare', 'AnalysisView::compare');
    $routes->post('analysis/positions', 'AnalysisView::ajaxPositions');

    // Reports
    $routes->get('report/match/(:num)', 'ReportAnalytics::matchReport/$1');
    $routes->get('report/player/(:num)', 'ReportAnalytics::playerReport/$1');
    $routes->get('report/export-pdf/(:num)', 'ReportAnalytics::exportPdf/$1');

    // Settings
    $routes->get('settings', 'SettingAnalytics::index');
    $routes->post('settings/save', 'SettingAnalytics::save');
    $routes->post('settings/test-connection', 'SettingAnalytics::testConnection');
    $routes->get('settings/calibration/(:num)', 'SettingAnalytics::calibration/$1');
    $routes->post('settings/auto-calibrate/(:num)', 'SettingAnalytics::autoCalibrate/$1');
    $routes->post('settings/save-calibration', 'SettingAnalytics::saveCalibration');
});

$routes->group('tournament', ['namespace' => '\Modules\Tournament\Controllers', 'filter' => 'adminGuard'], function ($routes) {
    $routes->get('index/(:any)', 'Tournament::index/$1');
    $routes->get('team/(:any)', 'Tournament::team/$1');
    $routes->get('result/(:any)', 'Tournament::result/$1');
    $routes->get('group/(:any)', 'Tournament::group/$1');
    $routes->get('update_tour', 'Tournament::update_tour');
});

$routes->group('detail', ['namespace' => '\Modules\Detail\Controllers', 'filter' => 'adminGuard'], function ($routes) {
    $routes->get('team/(:any)', 'Detail::team/$1');
});

// Coach Routes
$routes->group('coach', ['namespace' => '\Modules\Coach\Controllers'], function ($routes) {
    $routes->get('/', 'CoachAuth::index');
    $routes->get('login', 'CoachAuth::index');
    $routes->post('signin', 'CoachAuth::signin_with_account');
    $routes->get('logout', 'CoachAuth::logout');
    $routes->get('register', 'CoachAuth::register');
    $routes->post('register-post', 'CoachAuth::register_post');

    // Protected coach routes
    $routes->get('dashboard', 'CoachDashboard::index', ['filter' => 'coachGuard']);
    $routes->get('profile', 'CoachDashboard::profile', ['filter' => 'coachGuard']);
    $routes->post('profile/update', 'CoachDashboard::updateProfile', ['filter' => 'coachGuard']);
    $routes->post('updateCertificates', 'CoachDashboard::updateCertificates', ['filter' => 'coachGuard']);
    $routes->get('invitations', 'CoachDashboard::invitations', ['filter' => 'coachGuard']);
    $routes->post('respond-invitation', 'CoachDashboard::respondToInvitation', ['filter' => 'coachGuard']);
    $routes->get('teams', 'CoachDashboard::teams', ['filter' => 'coachGuard']);

    // Notification routes
    $routes->get('notifications', 'CoachDashboard::getNotifications', ['filter' => 'coachGuard']);
    $routes->get('notifications/count', 'CoachDashboard::getUnreadCount', ['filter' => 'coachGuard']);
    $routes->post('notifications/mark-read', 'CoachDashboard::markNotificationAsRead', ['filter' => 'coachGuard']);
    $routes->post('notifications/mark-all-read', 'CoachDashboard::markAllNotificationsAsRead', ['filter' => 'coachGuard']);

    // Coach - Join Team Request routes
    $routes->get('find-team', 'CoachDashboard::findTeam', ['filter' => 'coachGuard']);
    $routes->get('search-teams', 'CoachDashboard::searchTeams', ['filter' => 'coachGuard']);
    $routes->post('submit-join-request', 'CoachDashboard::submitJoinRequest', ['filter' => 'coachGuard']);
    $routes->get('my-requests', 'CoachDashboard::myRequests', ['filter' => 'coachGuard']);
    $routes->post('cancel-request', 'CoachDashboard::cancelRequest', ['filter' => 'coachGuard']);

    // Team management routes
    $routes->get('team/(:num)', 'TeamManagement::index/$1', ['filter' => 'coachGuard']);
    $routes->get('team/(:num)/players', 'TeamManagement::players/$1', ['filter' => 'coachGuard']);
    $routes->post('team/(:num)/players/add', 'TeamManagement::addPlayer/$1', ['filter' => 'coachGuard']);
    $routes->post('team/(:num)/players/(:num)/edit', 'TeamManagement::editPlayer/$1/$2', ['filter' => 'coachGuard']);
    $routes->post('team/(:num)/players/(:num)/remove', 'TeamManagement::removePlayer/$1/$2', ['filter' => 'coachGuard']);

    $routes->get('pdpa/check-consent', 'CoachAuth::checkPdpaConsent');
    $routes->post('pdpa/save-consent', 'CoachAuth::savePdpaConsent');
});

#----------------------------------------------------------------------

// Legacy API
$routes->group('api', ['namespace' => '\Modules\Api\Controllers'], function ($routes) {
    $routes->post('token', 'Api::getToken');
    $routes->get('actions', 'Api::getEvent', ['filter' => 'apiGuard']);
    $routes->get('match-result/(:any)', 'Api::getMatchResult/$1', ['filter' => 'apiGuard']);
    $routes->get('player-state/(:any)', 'Api::getPlayerState/$1', ['filter' => 'apiGuard']);
    $routes->get('end-match-result/(:any)', 'Api::postEndMatch/$1');
    $routes->get('tournament/players/(:num)', 'Api::getTournamentPlayers/$1');
    $routes->get('tournaments', 'Api::getTournaments');
    $routes->get('all-data', 'Api::getAllData');
    $routes->get('all-data/(:any)', 'Api::getAllData/$1');
});

// Mobile API v1
$routes->post('api/v1/auth/login', '\Modules\Api\Controllers\V1\AuthController::login');
$routes->post('api/v1/auth/login-line', '\Modules\Api\Controllers\V1\AuthController::loginLine');
$routes->post('api/v1/auth/refresh', '\Modules\Api\Controllers\V1\AuthController::refresh');

// Public API v1 (no token required)
$routes->group('api/v1', ['namespace' => '\Modules\Api\Controllers\V1'], function ($routes) {
    // Matches
    $routes->get('matches', 'MatchController::index');                        // matches list by date
    $routes->get('matches/(:num)', 'MatchController::show/$1');               // match details
    $routes->get('matches/(:num)/h2h', 'MatchController::h2h/$1');            // head to head
    $routes->get('matches/(:num)/statistics', 'MatchController::statistics/$1'); // match statistics

    // Competitions (Tournaments)
    $routes->get('competitions', 'CompetitionController::index');              // countries/competitions list
    $routes->get('competitions/(:num)/standings', 'CompetitionController::standings/$1');   // standings table
    $routes->get('competitions/(:num)/top-stats', 'CompetitionController::topStats/$1');    // top scorers, cards
    $routes->get('competitions/(:num)/highlights', 'CompetitionController::highlights/$1'); // recent results
    $routes->get('competitions/(:num)/winners', 'CompetitionController::winners/$1');       // past winners
    $routes->get('competitions/(:num)/matches', 'CompetitionController::matches/$1');       // league matches by round

    // Teams
    $routes->get('teams', 'TeamController::index');                           // all teams list
    $routes->get('teams/(:num)/matches', 'TeamController::matches/$1');       // team matches (next/last)
    $routes->get('teams/(:num)/squads', 'TeamController::squads/$1');         // team squad/roster
    $routes->get('teams/(:num)/coaches', 'CoachController::teamCoaches/$1');  // team coaches
    $routes->get('teams/(:num)/standings', 'TeamController::standings/$1');   // team standings in competitions
    $routes->get('teams/(:num)/transfers', 'TeamController::transfers/$1');   // team transfers

    // Players
    $routes->get('players/(:num)', 'PlayerController::show/$1');              // player detail + stats

    // Coaches
    $routes->get('coaches/(:num)', 'CoachController::show/$1');               // coach detail + licenses
    $routes->get('players/(:num)/idp', 'PlayerController::idp/$1');           // player IDP radar chart

    // Search
    $routes->get('search', 'SearchController::index');                        // search competitions/teams/players
});

// Protected API v1 (requires JWT token)
$routes->group('api/v1', ['namespace' => '\Modules\Api\Controllers\V1', 'filter' => 'apiGuard'], function ($routes) {
    // Auth / Profile
    $routes->get('profile', 'AuthController::profile');

    // Posts / News
    $routes->get('posts', 'PostController::index');                           // posts list
    $routes->get('posts/(:num)', 'PostController::show/$1');                  // post detail

    // Push Notifications
    $routes->post('notification/devices', 'NotificationController::registerDevice');
    $routes->delete('notification/devices', 'NotificationController::deleteDevice');
    $routes->delete('notification/devices/(:any)', 'NotificationController::deleteDevice/$1');
    $routes->get('notification/preferences', 'NotificationController::preferences');
    $routes->put('notification/preferences', 'NotificationController::updatePreferences');
    $routes->post('notification/favorites/sync', 'NotificationController::syncFavorites');

});

#----------------------------------------------------------------------

// We get a performance increase by specifying the default
// route since we don't have to scan directories.
// Dashborad

$routes->get('secure-file/id-card/(:segment)', '\App\Controllers\SecureFile::idCard/$1', ['filter' => 'authGuard']);

/*
 * --------------------------------------------------------------------
 * Additional Routing
 * --------------------------------------------------------------------
 *
 * There will often be times that you need additional routing and you
 * need it to be able to override any defaults in this file. Environment
 * based routes is one such time. require() additional route files here
 * to make that happen.
 *
 * You will have access to the $routes object within that file without
 * needing to reload it.
 */
if (file_exists(APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php')) {
    require APPPATH . 'Config/' . ENVIRONMENT . '/Routes.php';
}
