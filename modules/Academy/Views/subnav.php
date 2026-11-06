<?php
$url = uri_string();
$sub_nav_active = explode('/', $url);

// Academy ID param for building URLs
$academy_id_param = '';
if (!empty($academy) && isset($academy['id'])) {
    $academy_id_param = '/' . $academy['id'];
}

$is_coach = session()->get('user_type') === 'coach';

// Owner check (regular user only)
// Super academy operator is treated as virtual owner of every academy
$_is_owner = false;
if (!$is_coach && session()->get('is_super_academy')) {
    $_is_owner = true;
} elseif (!$is_coach && !empty($academy['id'])) {
    $_uid = session()->get('user_id');
    if ($_uid) {
        $_row = db_connect()->table('academy_members')
            ->select('role')
            ->where('academy_id', (int)$academy['id'])
            ->where('user_id', (int)$_uid)
            ->where('status', 'approved')
            ->get()->getRowArray();
        $_is_owner = (($_row['role'] ?? '') === 'owner');
    }
}

$seg1 = $sub_nav_active[0] ?? '';
$seg2 = $sub_nav_active[1] ?? '';

// Permission flags from controller
$perms = $perms ?? [];
$_can = function ($key) use ($perms, $_is_owner) {
    if ($_is_owner) return true;
    return !empty($perms[$key]);
};

// ===== Active state helpers =====
$isOverview = ($seg1 === 'academy' && in_array($seg2, ['info', 'manage', '']));
$teamsActive = ($seg1 === 'academy' && in_array($seg2, [
    'teams', 'team-create', 'team-info', 'team-player-manage', 'team-coach-manage'
]));

// อยู่ใน context ของทีม (เช่น team-info / team-player-manage) หรือไม่
$inTeamContext = !empty($team) && isset($team['id']);

$playersActive = ($seg1 === 'academy' && in_array($seg2, [
    'players', 'player-list', 'player-view', 'player-edit',
    'player-history', 'player-physical-history', 'report-player'
]));
$isTraining = ($seg1 === 'academy' && in_array($seg2, [
    'training-groups', 'training-group',
    'training-sessions', 'training-session', 'training-attendance',
    'training-topics', 'training-topic'
]));
$isEvaluation = ($seg1 === 'academy' && in_array($seg2, [
    'idp', 'reports', 'report-daily', 'report-monthly', 'report-group',
    'assessments', 'assessment', 'football-stats'
]));
$isManagement = (
    ($seg1 === 'auth' && $seg2 === 'tournament') ||
    ($seg1 === 'academy' && in_array($seg2, ['requests', 'coach-requests', 'permissions'])) ||
    ($seg1 === 'lineup')
);
$isGuide = ($seg1 === 'academy' && $seg2 === 'user-guide-pdf');

// ===== Visibility flags =====
$showTraining   = $_can('perm_view_training') || $_can('perm_manage_training_group')
                  || $_can('perm_create_training_session') || $_can('perm_create_training_topic');
$showEvaluation = $_can('perm_view_idp') || $_can('perm_manage_idp') || $_can('perm_view_reports');
$showApproveReq = !$is_coach && $_can('perm_approve_requests');
$showApproveCoachReq = !$is_coach && $_can('perm_approve_coach_requests');
$showLineup     = $_can('perm_create_edit_team') || $_can('perm_view_teams');
$showMgmt       = (!$is_coach) || $showApproveReq || $showApproveCoachReq || $_is_owner || $showLineup;

// ===== Pending request counts (สำหรับแสดง badge) =====
$pendingPlayerReqCount = 0;
$pendingCoachReqCount  = 0;
if (!empty($academy['id'])) {
    $_acid = (int)$academy['id'];
    $_db = db_connect();
    if ($showApproveReq) {
        $pendingPlayerReqCount = (int)$_db->table('academy_members')
            ->where('academy_id', $_acid)
            ->where('status', 'pending')
            ->where('role !=', 'coach')
            ->countAllResults();
    }
    if ($showApproveCoachReq && $_db->tableExists('coach_team_requests')) {
        $pendingCoachReqCount = (int)$_db->table('coach_team_requests ctr')
            ->join('academy_teams at', 'at.team_id = ctr.team_id', 'inner')
            ->where('at.academy_id', $_acid)
            ->where('ctr.status', 'pending')
            ->where('ctr.is_active', 1)
            ->countAllResults();
    }
}
$totalPendingReq = $pendingPlayerReqCount + $pendingCoachReqCount;
?>

<?php if (!empty($academy) && isset($academy['id'])) { ?>

<style>
/* ===== Academy Top Navigation (Compact + Dropdowns) ===== */
.academy-topnav {
    background: linear-gradient(180deg, #ffffff 0%, #fafbfc 100%);
    border: 1px solid #e9edf3;
    border-radius: 14px;
    padding: 6px;
    margin-bottom: 1.25rem;
    box-shadow: 0 1px 3px rgba(15,23,42,.04);
}
.academy-topnav,
.academy-topnav .topnav-link,
.academy-topnav .topnav-trigger,
.academy-topnav .topnav-dropdown-item span,
.academy-topnav .topnav-dropdown-header {
    font-family: 'Kanit', sans-serif;
}

.academy-topnav .topnav-list {
    display: flex; align-items: center; gap: 4px;
    flex-wrap: wrap;
    list-style: none; margin: 0; padding: 0;
}

.academy-topnav .topnav-link,
.academy-topnav .topnav-trigger {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 9px 14px;
    border-radius: 10px;
    font-size: .875rem; font-weight: 500;
    color: #475569;
    text-decoration: none;
    background: transparent;
    border: 1px solid transparent;
    cursor: pointer;
    transition: background .15s, color .15s, border-color .15s, transform .1s;
    white-space: nowrap;
}
.academy-topnav .topnav-link i,
.academy-topnav .topnav-trigger i.topnav-icon {
    font-size: .95rem; opacity: .75;
}
.academy-topnav .topnav-link:hover,
.academy-topnav .topnav-trigger:hover {
    background: #f1f5f9; color: #0d6efd;
}
.academy-topnav .topnav-link:hover i,
.academy-topnav .topnav-trigger:hover i.topnav-icon { opacity: 1; }

.academy-topnav .topnav-link.active,
.academy-topnav .topnav-trigger.active {
    background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
    color: #fff;
    border-color: rgba(13,110,253,.6);
    box-shadow: 0 4px 10px -2px rgba(13,110,253,.35);
}
.academy-topnav .topnav-link.active i,
.academy-topnav .topnav-trigger.active i.topnav-icon { opacity: 1; }

.academy-topnav .topnav-trigger .caret {
    font-size: .65rem; opacity: .65; margin-left: 2px;
    transition: transform .2s;
}
.academy-topnav .topnav-item.show .topnav-trigger .caret {
    transform: rotate(180deg);
}

/* Dropdown menu */
.academy-topnav .topnav-menu {
    min-width: 240px;
    padding: 6px;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    box-shadow: 0 12px 32px -8px rgba(15,23,42,.18);
    background: #fff;
}
.academy-topnav .topnav-dropdown-header {
    font-size: .68rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .5px;
    color: #94a3b8;
    padding: 6px 10px 4px;
}
.academy-topnav .topnav-dropdown-item {
    display: flex; align-items: center; gap: 10px;
    padding: 9px 10px;
    border-radius: 8px;
    color: #334155;
    text-decoration: none;
    font-size: .85rem;
    transition: background .15s, color .15s;
}
.academy-topnav .topnav-dropdown-item .item-icon {
    width: 28px; height: 28px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 7px;
    background: #f1f5f9;
    color: #0d6efd;
    font-size: .8rem;
    flex-shrink: 0;
}
.academy-topnav .topnav-dropdown-item:hover {
    background: #f8fafc;
    color: #0d6efd;
}
.academy-topnav .topnav-dropdown-item.active {
    background: rgba(13,110,253,.08);
    color: #0d6efd;
    font-weight: 600;
}
.academy-topnav .topnav-dropdown-item.active .item-icon {
    background: #0d6efd; color: #fff;
}
.academy-topnav .topnav-dropdown-divider {
    height: 1px; background: #eef2f7; margin: 4px 0;
}

/* Badge สำหรับนับคำขอที่รอดำเนินการ */
.academy-topnav .topnav-badge {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 18px; height: 18px;
    padding: 0 6px;
    margin-left: 4px;
    border-radius: 999px;
    background: #ef4444; color: #fff;
    font-size: .68rem; font-weight: 700; line-height: 1;
}
.academy-topnav .topnav-item-badge {
    margin-left: auto;
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 22px; height: 20px;
    padding: 0 7px;
    border-radius: 999px;
    background: #ef4444; color: #fff;
    font-size: .7rem; font-weight: 700; line-height: 1;
}
.academy-topnav .topnav-dropdown-item.active .topnav-item-badge {
    background: #fff; color: #ef4444;
}

/* Mobile toggle */
.academy-topnav-toggle {
    display: none;
    width: 100%;
    align-items: center; justify-content: space-between;
    padding: 10px 14px;
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    color: #334155;
    font-family: 'Kanit', sans-serif;
    font-size: .9rem;
    font-weight: 500;
}

@media (max-width: 991.98px) {
    .academy-topnav-toggle { display: inline-flex; }
    .academy-topnav { padding: 8px; }
    .academy-topnav .topnav-list {
        display: none; flex-direction: column; align-items: stretch; gap: 2px; padding-top: 8px;
    }
    .academy-topnav .topnav-list.show { display: flex; }
    .academy-topnav .topnav-link,
    .academy-topnav .topnav-trigger { width: 100%; justify-content: flex-start; }
    .academy-topnav .topnav-menu { width: 100%; }
}

/* ===== Context Indicator (บ่งบอกว่ากำลังจัดการ Academy หรือ Team) ===== */
.scope-banner {
    display: flex; align-items: center; gap: 14px;
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 1rem;
    font-family: 'Kanit', sans-serif;
    border: 1px solid;
}
.scope-banner-academy {
    background: linear-gradient(90deg, #eff6ff 0%, #dbeafe 100%);
    border-color: #bfdbfe;
    color: #1e40af;
}
.scope-banner-team {
    background: linear-gradient(90deg, #ecfdf5 0%, #d1fae5 100%);
    border-color: #a7f3d0;
    color: #065f46;
}
.scope-icon {
    width: 44px; height: 44px;
    display: flex; align-items: center; justify-content: center;
    border-radius: 10px;
    font-size: 1.15rem;
    flex-shrink: 0;
}
.scope-banner-academy .scope-icon { background: rgba(59,130,246,.15); color: #1d4ed8; }
.scope-banner-team .scope-icon { background: rgba(16,185,129,.15); color: #047857; }

.scope-meta { flex-grow: 1; min-width: 0; }
.scope-label {
    font-size: .68rem; font-weight: 600; text-transform: uppercase;
    letter-spacing: .5px; opacity: .7; line-height: 1;
}
.scope-title {
    font-size: 1rem; font-weight: 600; line-height: 1.3;
    margin-top: 2px; word-break: break-word;
}
.scope-sub {
    font-size: .78rem; opacity: .75; margin-top: 3px;
    display: inline-flex; align-items: center; gap: 6px;
}
.scope-back-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 6px 14px;
    border-radius: 999px;
    background: rgba(255,255,255,.7);
    color: inherit;
    text-decoration: none;
    font-size: .8rem; font-weight: 500;
    border: 1px solid rgba(0,0,0,.06);
    transition: background .15s, transform .1s;
    white-space: nowrap;
}
.scope-back-btn:hover {
    background: #fff; color: inherit;
    transform: translateX(-2px);
}
@media (max-width: 575.98px) {
    .scope-banner { flex-wrap: wrap; }
    .scope-back-btn { width: 100%; justify-content: center; }
}
</style>

<nav class="academy-topnav" aria-label="Academy navigation">
    <button class="academy-topnav-toggle" type="button" data-academy-toggle>
        <span><i class="fas fa-bars me-2"></i><?= lang('Common.subnav_menu') ?></span>
        <i class="fas fa-chevron-down"></i>
    </button>

    <ul class="topnav-list" data-academy-menu>

        <!-- 1. ภาพรวม (Overview / Academy Info) -->
        <?php if ($_can('perm_view_academy')): ?>
        <li class="topnav-item">
            <a class="topnav-link <?= $isOverview ? 'active' : '' ?>"
               href="<?= base_url('academy/info' . $academy_id_param) ?>">
                <i class="fas fa-school topnav-icon"></i>
                <span><?= lang('Common.subnav_academy_info') ?></span>
            </a>
        </li>
        <?php endif; ?>

        <!-- 2. นักเตะ (ทั้ง academy) -->
        <?php if ($_can('perm_view_players')): ?>
        <li class="topnav-item">
            <a class="topnav-link <?= $playersActive ? 'active' : '' ?>"
               href="<?= base_url('academy/players' . $academy_id_param) ?>">
                <i class="fas fa-running topnav-icon"></i>
                <span><?= lang('Common.players') ?></span>
            </a>
        </li>
        <?php endif; ?>

        <!-- 3. ทีม -->
        <?php if ($_can('perm_view_teams')): ?>
        <li class="topnav-item">
            <a class="topnav-link <?= $teamsActive ? 'active' : '' ?>"
               href="<?= base_url('academy/teams' . $academy_id_param) ?>">
                <i class="fas fa-shield-alt topnav-icon"></i>
                <span><?= lang('Common.teams') ?></span>
            </a>
        </li>
        <?php endif; ?>

        <!-- 4. แผงรูป — เมนูเดียวทั้งใน/นอก context ทีม
             กดแล้วเปิด modal ให้เลือกก่อนเสมอว่าจะดูแผงรูปของทีมไหนในรายการไหน -->
        <?php if ($_can('perm_view_players')): ?>
        <li class="topnav-item">
            <a class="topnav-link <?= ($seg1=='academy' && $seg2=='player-chart') ? 'active' : '' ?>"
               href="#" id="btnChartTournamentPicker"
               data-academy-id="<?= (int) $academy['id'] ?>"
               data-current-team-id="<?= (int) ($team['id'] ?? 0) ?>">
                <i class="fas fa-id-card topnav-icon"></i>
                <span><?= lang('Common.player_chart') ?></span>
            </a>
        </li>
        <?php endif; ?>

        <!-- 5. ฝึกซ้อม -->
        <?php if ($showTraining): ?>
        <li class="topnav-item dropdown">
            <button class="topnav-trigger <?= $isTraining ? 'active' : '' ?>"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-dumbbell topnav-icon"></i>
                <span><?= lang('Common.subnav_training') ?></span>
                <i class="fas fa-chevron-down caret"></i>
            </button>
            <ul class="dropdown-menu topnav-menu">
                <?php if ($_can('perm_view_training') || $_can('perm_manage_training_group')): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='academy' && in_array($seg2, ['training-groups','training-group'])) ? 'active' : '' ?>"
                       href="<?= base_url('academy/training-groups' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-layer-group"></i></span>
                        <span><?= lang('Common.training_groups') ?></span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($_can('perm_view_training') || $_can('perm_create_training_session') || $_can('perm_save_attendance')): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='academy' && in_array($seg2, ['training-sessions','training-session','training-attendance'])) ? 'active' : '' ?>"
                       href="<?= base_url('academy/training-sessions' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-clipboard-list"></i></span>
                        <span><?= lang('Common.training_sessions') ?></span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($_can('perm_view_training') || $_can('perm_create_training_topic')): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='academy' && in_array($seg2, ['training-topics','training-topic'])) ? 'active' : '' ?>"
                       href="<?= base_url('academy/training-topics' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-list-alt"></i></span>
                        <span><?= lang('Common.training_topics') ?></span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- 6. ประเมินและรายงาน -->
        <?php if ($showEvaluation): ?>
        <li class="topnav-item dropdown">
            <button class="topnav-trigger <?= $isEvaluation ? 'active' : '' ?>"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-chart-line topnav-icon"></i>
                <span><?= lang('Common.subnav_eval_reports') ?></span>
                <i class="fas fa-chevron-down caret"></i>
            </button>
            <ul class="dropdown-menu topnav-menu">
                <?php if ($_can('perm_view_idp') || $_can('perm_manage_idp')): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='academy' && $seg2=='idp') ? 'active' : '' ?>"
                       href="<?= base_url('academy/idp' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-seedling"></i></span>
                        <span><?= lang('Common.subnav_assessment') ?></span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($_can('perm_view_reports')): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='academy' && in_array($seg2, ['reports','report-daily','report-monthly','report-group'])) ? 'active' : '' ?>"
                       href="<?= base_url('academy/reports' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-chart-bar"></i></span>
                        <span><?= lang('Common.reports') ?></span>
                    </a>
                </li>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='academy' && $seg2=='football-stats') ? 'active' : '' ?>"
                       href="<?= base_url('academy/football-stats' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-futbol"></i></span>
                        <span><?= lang('Common.subnav_football_stats') ?></span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- 7. จัดการ -->
        <?php if ($showMgmt): ?>
        <li class="topnav-item dropdown">
            <button class="topnav-trigger <?= $isManagement ? 'active' : '' ?>"
                    type="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fas fa-cog topnav-icon"></i>
                <span><?= lang('Common.subnav_management') ?></span>
                <?php if ($totalPendingReq > 0): ?>
                    <span class="topnav-badge"><?= $totalPendingReq > 99 ? '99+' : $totalPendingReq ?></span>
                <?php endif; ?>
                <i class="fas fa-chevron-down caret"></i>
            </button>
            <ul class="dropdown-menu topnav-menu dropdown-menu-end">
                <?php if (!$is_coach): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='auth' && $seg2=='tournament') ? 'active' : '' ?>"
                       href="<?= base_url('auth/tournament/list') ?>" target="_blank">
                        <span class="item-icon"><i class="fas fa-trophy"></i></span>
                        <span><?= lang('Common.tournament_nav') ?></span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($showApproveReq || $showApproveCoachReq): ?>
                <li class="topnav-dropdown-divider"></li>
                <li class="topnav-dropdown-header"><?= lang('Common.subnav_join_requests_header') ?></li>
                <?php endif; ?>
                <?php if ($showApproveReq): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='academy' && $seg2=='requests') ? 'active' : '' ?>"
                       href="<?= base_url('academy/requests' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-user-plus"></i></span>
                        <span><?= lang('Common.player_join_requests') ?></span>
                        <?php if ($pendingPlayerReqCount > 0): ?>
                            <span class="topnav-item-badge"><?= $pendingPlayerReqCount > 99 ? '99+' : $pendingPlayerReqCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($showApproveCoachReq): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='academy' && $seg2=='coach-requests') ? 'active' : '' ?>"
                       href="<?= base_url('academy/coach-requests' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-user-tie"></i></span>
                        <span><?= lang('Common.coach_join_requests') ?></span>
                        <?php if ($pendingCoachReqCount > 0): ?>
                            <span class="topnav-item-badge"><?= $pendingCoachReqCount > 99 ? '99+' : $pendingCoachReqCount ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($showLineup || $_is_owner): ?>
                <li class="topnav-dropdown-divider"></li>
                <li class="topnav-dropdown-header"><?= lang('Common.subnav_tools') ?></li>
                <?php endif; ?>
                <?php if ($showLineup): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='lineup') ? 'active' : '' ?>"
                       href="<?= base_url('lineup' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-th"></i></span>
                        <span><?= lang('Common.lineup') ?></span>
                    </a>
                </li>
                <?php endif; ?>
                <?php if ($_is_owner): ?>
                <li>
                    <a class="topnav-dropdown-item <?= ($seg1=='academy' && $seg2=='permissions') ? 'active' : '' ?>"
                       href="<?= base_url('academy/permissions' . $academy_id_param) ?>">
                        <span class="item-icon"><i class="fas fa-shield-alt"></i></span>
                        <span><?= lang('Common.academy_permissions') ?></span>
                    </a>
                </li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- 8. คู่มือ -->
        <li class="topnav-item">
            <a class="topnav-link <?= $isGuide ? 'active' : '' ?>"
               href="<?= base_url('academy/user-guide-pdf') ?>" target="_blank">
                <i class="fas fa-book topnav-icon"></i>
                <span><?= lang('Common.user_guide') ?></span>
            </a>
        </li>

    </ul>
</nav>

<!-- ===== Scope Banner: บ่งบอกว่ากำลังจัดการ Academy หรือ Team ===== -->
<?php if ($inTeamContext): ?>
    <div class="scope-banner scope-banner-team" role="status">
        <div class="scope-icon"><i class="fas fa-shield-alt"></i></div>
        <div class="scope-meta">
            <div class="scope-label"><?= lang('Common.scope_managing_team') ?></div>
            <div class="scope-title">
                <i class="fas fa-users me-1 opacity-75" style="font-size:.85rem;"></i>
                <?= lang('Common.scope_team_prefix') ?> <?= esc($team['name'] ?? '-') ?>
                <?php if (!empty($team['generation_name'])): ?>
                    <span class="badge bg-success bg-opacity-25 text-success rounded-pill ms-1" style="font-size:.7rem;font-weight:500;">
                        <?= esc($team['generation_name']) ?>
                    </span>
                <?php endif; ?>
            </div>
            <div class="scope-sub">
                <i class="fas fa-school"></i> <?= lang('Common.scope_within_academy') ?> <?= esc($academy['name'] ?? '-') ?>
            </div>
        </div>
        <a class="scope-back-btn" href="<?= base_url('academy/info/' . $academy['id']) ?>" title="<?= lang('Common.scope_back_title') ?>">
            <i class="fas fa-arrow-left"></i> <?= lang('Common.scope_back_academy') ?>
        </a>
    </div>
<?php else: ?>
    <div class="scope-banner scope-banner-academy" role="status">
        <div class="scope-icon"><i class="fas fa-school"></i></div>
        <div class="scope-meta">
            <div class="scope-label"><?= lang('Common.scope_managing_academy') ?></div>
            <div class="scope-title">
                <i class="fas fa-graduation-cap me-1 opacity-75" style="font-size:.85rem;"></i>
                Academy: <?= esc($academy['name'] ?? '-') ?>
            </div>
            <div class="scope-sub">
                <i class="fas fa-info-circle"></i> <?= lang('Common.scope_academy_hint') ?>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- ===== Modal เลือกทัวร์นาเมนต์ก่อนเข้าแผงรูป (ใช้กับเมนู "แผงรูป" ตอนอยู่นอก context ทีม) ===== -->
<?php if ($_can('perm_view_players')): ?>
<div class="modal fade" id="chartTournamentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-primary d-block text-center">
                <h4 class="modal-title fw-bold mb-0 text-white"><?= lang('Common.chart_picker_title') ?></h4>
                <div class="small text-white opacity-75 mt-1"><?= lang('Common.chart_picker_hint') ?></div>
            </div>
            <div class="modal-body">
                <div class="position-relative mb-3">
                    <input type="text" class="form-control ps-5 rounded-pill" id="chartTourSearch"
                           placeholder="<?= lang('Common.search_placeholder') ?>" autocomplete="off">
                    <i class="fas fa-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                </div>
                <div id="chartTourList">
                    <div class="text-center text-muted py-4">
                        <div class="spinner-border spinner-border-sm me-2"></div><?= lang('Common.loading') ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.close') ?></button>
            </div>
        </div>
    </div>
</div>
<script>
(function() {
    var trigger = document.getElementById('btnChartTournamentPicker');
    if (!trigger) return;

    var modalEl  = document.getElementById('chartTournamentModal');
    var listEl   = document.getElementById('chartTourList');
    var searchEl = document.getElementById('chartTourSearch');
    var academyId = trigger.getAttribute('data-academy-id');
    var curTeamId = trigger.getAttribute('data-current-team-id') || '';
    var loaded    = false;
    // base_url() ตัด / ท้ายทิ้ง จึงต้องต่อ / เองทุกครั้ง
    var BASE      = <?= json_encode(rtrim(base_url(), '/') . '/') ?>;
    var modal     = null;

    // bootstrap.bundle ถูกโหลดท้ายหน้า (หลัง subnav) จึงต้องสร้าง Modal ตอนกดเท่านั้น
    function getModal() {
        if (!modal) modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        return modal;
    }

    var LANG = {
        empty:   <?= json_encode(lang('Common.chart_picker_empty')) ?>,
        error:   <?= json_encode(lang('Common.error_occurred')) ?>,
        gen:     <?= json_encode(lang('Common.generation')) ?>,
        pendA:   <?= json_encode(lang('Common.pending_approval')) ?>,
        pendP:   <?= json_encode(lang('Common.pending_payment')) ?>,
        curTeam: <?= json_encode(lang('Common.chart_picker_current_team')) ?>,
        upcoming:<?= json_encode(lang('Common.chart_picker_upcoming')) ?>,
        regDate: <?= json_encode(lang('Common.registration_status')) ?>,
        matchDate:<?= json_encode(lang('Common.competition')) ?>
    };
    var LOCALE = <?= json_encode(service('request')->getLocale() === 'th' ? 'th-TH' : 'en-GB') ?>;

    function esc(v) {
        return String(v === null || v === undefined ? '' : v)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#039;');
    }

    // logo เก็บเป็น path สัมพัทธ์ เช่น public/uploads/logo-team/x.jpeg (เผื่อ URL เต็มด้วย)
    // ไฟล์หาย/โหลดไม่ขึ้น ก็ตกไปใช้โลโก้กลางเหมือนหน้า select_academy ไม่ใช้ตัวย่อ
    var DEFAULT_LOGO = BASE + 'public/img/jootball_logo.png';

    function logoUrl(path) {
        if (!path) return DEFAULT_LOGO;
        return /^https?:\/\//i.test(path) ? path : BASE + String(path).replace(/^\/+/, '');
    }

    function logoHtml(path, name, size, rounded) {
        var style = 'width:' + size + 'px;height:' + size + 'px;flex:0 0 ' + size + 'px;object-fit:cover;'
                  + (rounded ? 'border-radius:50%;' : 'border-radius:.4rem;');
        return '<img src="' + esc(logoUrl(path)) + '" alt="' + esc(name || '') + '"'
             + ' class="border bg-white" style="' + style + '"'
             + ' onerror="this.onerror=null;this.src=\'' + esc(DEFAULT_LOGO) + '\';">';
    }

    // '0000-00-00' / null / '' ถือว่ายังไม่ได้กำหนด
    function fmtDate(d) {
        if (!d || String(d).indexOf('0000') === 0) return '';
        var p = String(d).split('-');
        if (p.length !== 3) return '';
        var dt = new Date(+p[0], +p[1] - 1, +p[2]);
        if (isNaN(dt.getTime())) return '';
        return dt.toLocaleDateString(LOCALE, { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function dateRange(from, to) {
        var a = fmtDate(from), b = fmtDate(to);
        if (a && b) return a === b ? a : a + ' - ' + b;
        return a || b || '';
    }

    function dateLine(icon, label, from, to) {
        var range = dateRange(from, to);
        if (!range) return '';
        return '<span class="d-inline-flex align-items-center gap-1 me-3">'
             + '<i class="fas ' + icon + ' text-muted"></i>'
             + '<span class="text-muted">' + esc(label) + ':</span>'
             + '<span>' + esc(range) + '</span></span>';
    }

    function statusBadge(st) {
        if (st === 0) return '<span class="badge bg-warning text-dark ms-2">' + esc(LANG.pendA) + '</span>';
        if (st === 3) return '<span class="badge bg-info text-dark ms-2">' + esc(LANG.pendP) + '</span>';
        return '';
    }

    // ลิสต์เรียงกันไปตรง ๆ ไม่แยกกลุ่มทีม — ชื่อทีมอยู่ต่อท้ายแต่ละรายการแข่งขัน
    // ลำดับมาจาก SQL แล้ว: รายการที่ยังไม่ถึงเวลาแข่งก่อน แล้วไล่ tour ใหม่สุด ตามด้วยชื่อทีม
    function render(rows) {
        if (!rows.length) {
            listEl.innerHTML = '<div class="text-center text-muted py-4">' + esc(LANG.empty) + '</div>';
            return;
        }

        var html = '<div class="list-group list-group-flush">';
        rows.forEach(function(r) {
            // ค้นหาเทียบกับทั้งชื่อรายการ ชื่อทีม และรุ่น
            var hay = ((r.tour_name || '') + ' ' + (r.team_name || '') + ' ' + (r.generation || '')).toLowerCase();
            // ส่ง tour_id ไปด้วย หน้าแผงรูปจะเลือกทัวร์ใน dropdown ให้เลย
            var url = BASE + 'academy/player-chart/' + academyId + '/' + r.team_id
                    + '?tour_id=' + encodeURIComponent(r.tour_id);
            var isCur = String(r.team_id) === curTeamId;

            html += '<a href="' + esc(url) + '" class="list-group-item list-group-item-action py-3 px-2" data-hay="' + esc(hay) + '">'
                  + '<div class="d-flex align-items-center gap-3">'
                  + logoHtml(r.tour_logo, r.tour_name, 44, false)
                  + '<div class="flex-grow-1 min-w-0">'
                  + '<div class="fw-semibold text-truncate">' + esc(r.tour_name) + statusBadge(parseInt(r.register_status, 10))
                  + (parseInt(r.is_upcoming, 10) === 1
                      ? '<span class="badge bg-primary bg-opacity-10 text-primary ms-2">' + esc(LANG.upcoming) + '</span>' : '') + '</div>'
                  + '<div class="d-flex align-items-center gap-2 mt-1 min-w-0">'
                  + logoHtml(r.team_logo, r.team_name, 20, true)
                  + '<span class="small fw-semibold text-truncate">' + esc(r.team_name) + '</span>'
                  + (isCur ? '<span class="badge bg-success bg-opacity-10 text-success">' + esc(LANG.curTeam) + '</span>' : '')
                  + (r.generation ? '<span class="small text-muted text-truncate">' + esc(LANG.gen) + ' ' + esc(r.generation) + '</span>' : '')
                  + '</div>'
                  + '<div class="small mt-1 d-flex flex-wrap">'
                  + dateLine('fa-pen-to-square', LANG.regDate, r.reg_start_date, r.reg_end_date)
                  + dateLine('fa-futbol', LANG.matchDate, r.tournament_start_date, r.tournament_end_date)
                  + '</div>'
                  + '</div>'
                  + '<i class="fas fa-chevron-right text-muted"></i>'
                  + '</div></a>';
        });
        listEl.innerHTML = html + '</div>';
    }

    function load() {
        fetch(BASE + 'academy/chart-tournaments/' + academyId, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(function(res) { return res.json(); })
        .then(function(res) {
            if (res.status !== 'success') throw new Error(res.message || LANG.error);
            loaded = true;
            render(res.data || []);
        })
        .catch(function(e) {
            listEl.innerHTML = '<div class="text-center text-danger py-4">' + esc(e.message || LANG.error) + '</div>';
        });
    }

    trigger.addEventListener('click', function(e) {
        e.preventDefault();
        getModal().show();
        if (!loaded) load();
    });

    searchEl.addEventListener('input', function() {
        var q = this.value.trim().toLowerCase();
        listEl.querySelectorAll('[data-hay]').forEach(function(el) {
            el.style.display = (!q || el.getAttribute('data-hay').indexOf(q) !== -1) ? '' : 'none';
        });

    });

    modalEl.addEventListener('shown.bs.modal', function() { searchEl.focus(); });
})();
</script>
<?php endif; ?>

<script>
(function() {
    var toggleBtn = document.querySelector('[data-academy-toggle]');
    var menu = document.querySelector('[data-academy-menu]');
    if (toggleBtn && menu) {
        toggleBtn.addEventListener('click', function() {
            menu.classList.toggle('show');
        });
    }
})();
</script>

<?php } ?>
