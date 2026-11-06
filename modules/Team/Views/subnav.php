<?php
$url = uri_string();
$sub_nav_active = explode('/', $url);

// สำหรับผู้ฝึกสอนที่เข้ามาดูทีม ต้องใช้ team ID ใน URL
$team_id_param = '';
if (!empty($team) && isset($team['id'])) {
    $team_id_param = '/' . $team['id'];
}

// ===== Pending request counts (สำหรับแสดง badge) =====
$_team_player_req = 0;
$_team_coach_req  = 0;
if (!empty($team) && isset($team['id']) && session()->get('user_type') !== 'coach') {
    $_tid = (int)$team['id'];
    $_dbsn = db_connect();
    $_team_player_req = (int)$_dbsn->table('team_managers')
        ->where('team_id', $_tid)
        ->where('status', 'pending')
        ->countAllResults();
    if ($_dbsn->tableExists('coach_team_requests')) {
        $_team_coach_req = (int)$_dbsn->table('coach_team_requests')
            ->where('team_id', $_tid)
            ->where('status', 'pending')
            ->where('is_active', 1)
            ->countAllResults();
    }
}
?>
<style>
.nav-pills .nav-link .req-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px;
    padding: 0 6px; margin-left: 6px;
    border-radius: 999px;
    background: #ef4444; color: #fff !important;
    font-size: .68rem; font-weight: 700; line-height: 1;
}
.nav-pills .nav-link.active .req-badge {
    background: #fff; color: #ef4444 !important;
}
</style>
<ul class="nav nav-pills nav-justified">
    <?php if (!empty(session()->get('user_id')) || (session()->get('user_type') === 'coach' && !empty($team) && isset($team['id']))) { ?>
    <li class="nav-item">
        <a class="nav-link <?= ($sub_nav_active[0] == 'team' && ($sub_nav_active[1] == 'info' || $sub_nav_active[1] == 'manage')) ? 'active' : '' ?>"
            aria-current="page" href="<?= base_url('team/info' . $team_id_param) ?>"><?= lang('Common.team_info') ?></a>
    </li>
    <?php } ?>
    <?php if (!empty(session()->get('team')) || (session()->get('user_type') === 'coach' && !empty($team) && isset($team['id']))) { ?>
    <li class="nav-item">
        <a class="nav-link <?= (($sub_nav_active[0] == 'team' && $sub_nav_active[1] == 'player-list') || ($sub_nav_active[0] == 'player' && $sub_nav_active[1] == 'manage')) ? 'active' : '' ?>"
            href="<?= base_url('team/player-list' . $team_id_param) ?>"><?= lang('Common.players') ?></a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= (($sub_nav_active[0] == 'team' && $sub_nav_active[1] == 'player-chart')) ? 'active' : '' ?>"
            href="<?= base_url('team/player-chart' . $team_id_param) ?>"><?= lang('Common.player_chart') ?></a>
    </li>

    <?php if (session()->get('user_type') === 'coach') { ?>
    <li class="nav-item">
        <a class="nav-link <?= (($sub_nav_active[0] == 'auth' && $sub_nav_active[1] == 'tournament')) ? 'active' : '' ?>"
            href="<?= base_url('auth/tournament/list') ?>" target="_blank"><?= lang('Common.tournament_nav') ?></a>
    </li>
    <?php } ?>
    <?php if (session()->get('user_type') !== 'coach') { ?>
    <li class="nav-item">
        <a class="nav-link <?= (($sub_nav_active[0] == 'team' && ($sub_nav_active[1] == 'coach-list') || ($sub_nav_active[0] == 'player' && $sub_nav_active[1] == 'manage_coach'))) ? 'active' : '' ?>"
            href="<?= base_url('team/coach-list' . $team_id_param) ?>"><?= lang('Common.coach_staff') ?></a>
    </li>
    <?php } ?>

    <!-- Management Menu -->
    <?php if (session()->get('user_type') !== 'coach') { ?>
    <li class="nav-item">
        <a class="nav-link <?= ($sub_nav_active[0] == 'team' && $sub_nav_active[1] == 'requests') ? 'active' : '' ?>"
           href="<?= base_url('team/requests' . $team_id_param) ?>">
            <?= lang('Common.player_join_requests') ?>
            <?php if ($_team_player_req > 0): ?><span class="req-badge"><?= $_team_player_req > 99 ? '99+' : $_team_player_req ?></span><?php endif; ?>
        </a>
    </li>
    <li class="nav-item">
        <a class="nav-link <?= ($sub_nav_active[0] == 'team' && $sub_nav_active[1] == 'coach-requests') ? 'active' : '' ?>"
           href="<?= base_url('team/coach-requests' . $team_id_param) ?>">
            <?= lang('Common.coach_join_requests') ?>
            <?php if ($_team_coach_req > 0): ?><span class="req-badge"><?= $_team_coach_req > 99 ? '99+' : $_team_coach_req ?></span><?php endif; ?>
        </a>
    </li>
    <?php } ?>

    <?php } ?>
</ul>