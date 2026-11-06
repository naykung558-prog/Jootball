<?php $this->extend('template/layout') ?>
<?php $encrypter = \Config\Services::encrypter(); ?>
<?php $this->section('content'); ?>

<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <?php
    $myRoleKey  = $myRole ?? 'viewer';
    $roleColors = ['owner' => 'primary', 'admin' => 'info', 'coach' => 'success', 'viewer' => 'secondary'];
    $roleIcons  = ['owner' => 'crown', 'admin' => 'user-shield', 'coach' => 'chalkboard-teacher', 'viewer' => 'eye'];
    $roleDescs  = [
        'owner'  => lang('Common.role_owner_desc'),
        'admin'  => lang('Common.role_admin_desc'),
        'coach'  => lang('Common.role_coach_desc'),
        'viewer' => lang('Common.role_viewer_desc'),
    ];
    $roleLabelMap = [
        'owner'  => lang('Common.role_owner'),
        'admin'  => lang('Common.role_admin'),
        'coach'  => lang('Common.role_coach'),
        'viewer' => lang('Common.role_viewer'),
    ];
    $myColor = $roleColors[$myRoleKey] ?? 'secondary';
    $myIcon  = $roleIcons[$myRoleKey]  ?? 'eye';

    // เฉพาะ Super Academy Operator (academy.admin@jootball.com) เท่านั้นที่เลือก Owner ได้
    $isSuperAcademy = $isSuperAcademy ?? false;
    $roleOptions = ['admin' => lang('Common.role_admin'), 'coach' => lang('Common.role_coach'), 'viewer' => lang('Common.role_viewer')];
    if ($isSuperAcademy) {
        $roleOptions = ['owner' => lang('Common.role_owner')] + $roleOptions;
    }
    ?>

    <!-- Header -->
    <div class="card shadow-sm border-0 rounded-4 mb-4" style="background: linear-gradient(135deg, #16478d 0%, #1e5fa8 100%);">
        <div class="card-body py-4 px-4">
            <div class="d-flex flex-wrap align-items-center gap-3">
                <!-- Icon -->
                <div class="flex-shrink-0 rounded-3 d-flex align-items-center justify-content-center"
                     style="width:54px;height:54px;background:rgba(255,255,255,0.18);">
                    <i class="fas fa-shield-alt text-white" style="font-size:1.5rem;"></i>
                </div>
                <!-- Title -->
                <div class="flex-grow-1">
                    <h4 class="fw-bold mb-0 text-white"><?= lang('Common.academy_permissions') ?></h4>
                    <p class="mb-0 text-white" style="opacity:.75;font-size:.85rem;">
                        <i class="fas fa-school me-1"></i><?= esc($academy['name'] ?? '') ?>
                    </p>
                </div>
                <!-- Role badge -->
                <?php
                $roleBgMap = [
                    'owner'  => 'rgba(255,215,0,0.25)',
                    'admin'  => 'rgba(13,202,240,0.25)',
                    'coach'  => 'rgba(25,135,84,0.25)',
                    'viewer' => 'rgba(255,255,255,0.15)',
                ];
                $roleTextMap = [
                    'owner'  => '#FFD700',
                    'admin'  => '#0dcaf0',
                    'coach'  => '#5ce65c',
                    'viewer' => 'rgba(255,255,255,0.8)',
                ];
                ?>
                <div class="flex-shrink-0 d-flex align-items-center gap-2 px-3 py-2 rounded-3"
                     style="background:<?= $roleBgMap[$myRoleKey] ?? 'rgba(255,255,255,0.15)' ?>;border:1px solid rgba(255,255,255,0.2);">
                    <i class="fas fa-<?= $myIcon ?>" style="font-size:1.1rem;color:<?= $roleTextMap[$myRoleKey] ?? '#fff' ?>;"></i>
                    <div>
                        <div style="font-size:.7rem;color:rgba(255,255,255,.7);font-weight:600;line-height:1.2;letter-spacing:.3px;">
                            <?= lang('Common.academy_perm_your_role') ?>
                        </div>
                        <div class="fw-bold text-white" style="font-size:.95rem;line-height:1.3;">
                            <?= $roleLabelMap[$myRoleKey] ?? $myRoleKey ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <div class="perm-tabs-wrap mb-4">
        <ul class="nav perm-tabs gap-1" id="permTabs" role="tablist">
            <?php if ($canManage): ?>
            <li class="nav-item" role="presentation">
                <button class="perm-tab-btn active" id="tab-members-btn"
                        data-bs-toggle="pill" data-bs-target="#tab-members" type="button">
                    <span class="perm-tab-icon" style="background:#ECF2FF;">
                        <i class="fas fa-users" style="color:#16478d;"></i>
                    </span>
                    <span class="perm-tab-label"><?= lang('Common.members_label') ?></span>
                    <span class="perm-tab-count" style="background:#16478d;color:#fff;"><?= count($members) ?></span>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="perm-tab-btn" id="tab-coaches-btn"
                        data-bs-toggle="pill" data-bs-target="#tab-coaches" type="button">
                    <span class="perm-tab-icon" style="background:#e8f8f0;">
                        <i class="fas fa-chalkboard-teacher" style="color:#198754;"></i>
                    </span>
                    <span class="perm-tab-label"><?= lang('Common.coach_staff') ?></span>
                    <span class="perm-tab-count" style="background:#198754;color:#fff;"><?= count($coaches) ?></span>
                </button>
            </li>
            <?php endif; ?>
            <li class="nav-item" role="presentation">
                <button class="perm-tab-btn <?= !$canManage ? 'active' : '' ?>" id="tab-guide-btn"
                        data-bs-toggle="pill" data-bs-target="#tab-guide" type="button">
                    <span class="perm-tab-icon" style="background:#fff3cd;">
                        <i class="fas fa-book-open" style="color:#b45309;"></i>
                    </span>
                    <span class="perm-tab-label"><?= lang('Common.academy_permissions_guide') ?></span>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content">

        <?php if ($canManage): ?>
        <!-- ============================================================ -->
        <!-- TAB 1: สมาชิก -->
        <!-- ============================================================ -->
        <div class="tab-pane fade show active" id="tab-members" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-primary">
                            <i class="fas fa-users me-2"></i><?= lang('Common.academy_members') ?>
                        </h5>
                        <p class="small text-muted mb-0"><?= lang('Common.academy_permissions_desc') ?></p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="text" id="memberFilterSearch" class="form-control rounded-pill"
                               style="max-width:200px;" placeholder="<?= lang('Common.search_name') ?>..."
                               oninput="filterMembers()">
                        <button class="btn btn-primary rounded-pill px-4 text-nowrap" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                            <i class="fas fa-user-plus me-2"></i><?= lang('Common.academy_add_member') ?>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <!-- Note: coaches managed in coaches tab -->
                    <div class="px-4 pt-3 pb-2">
                        <div class="d-flex align-items-center gap-2 rounded-3 px-3 py-2"
                             style="background:#f0f7ff;border:1px solid #d0e4ff;">
                            <i class="fas fa-info-circle text-primary" style="font-size:.9rem;"></i>
                            <span class="small text-primary">
                                รายการนี้แสดงเฉพาะ <strong>สมาชิกทั่วไป</strong> เท่านั้น —
                                ผู้ฝึกสอนจะแสดงในแท็บ <strong>ผู้ฝึกสอน</strong>
                            </span>
                        </div>
                    </div>
                    <?php if (empty($members)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0"><?= lang('Common.academy_no_members') ?></p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 ps-4" width="4%">#</th>
                                    <th class="border-0"><?= lang('Common.academy_member') ?></th>
                                    <th class="border-0" width="16%"><?= lang('Common.role') ?></th>
                                    <th class="border-0" width="14%"><?= lang('Common.status') ?></th>
                                    <th class="border-0 text-end pe-4" width="22%"><?= lang('Common.manage') ?></th>
                                </tr>
                            </thead>
                            <tbody id="memberTbody">
                                <?php foreach ($members as $idx => $member): ?>
                                <?php
                                    $isCoach = !empty($member['coach_user_id']);
                                    if ($isCoach) {
                                        $displayName = trim(esc($member['coach_first_name'] ?? '') . ' ' . esc($member['coach_last_name'] ?? ''));
                                        if (empty(trim($displayName))) $displayName = esc($member['coach_username'] ?? '');
                                        $displayUsername = esc($member['coach_username'] ?? '');
                                        $displayImg      = $member['coach_image'] ?? '';
                                    } else {
                                        $displayName     = esc($member['user_name'] ?? '');
                                        $displayUsername = esc($member['username'] ?? '');
                                        $displayImg      = $member['profile_image'] ?? '';
                                    }
                                    $memberRole     = $member['role'] ?? 'viewer';
                                    $roleBadgeClass = $roleColors[$memberRole] ?? 'secondary';
                                    $roleLabel      = $roleLabelMap[$memberRole] ?? $memberRole;
                                    $statusMap      = [
                                        'approved' => ['cls' => 'bg-success',              'label' => lang('Common.status_approved')],
                                        'pending'  => ['cls' => 'bg-warning text-dark',    'label' => lang('Common.status_pending_approval')],
                                        'rejected' => ['cls' => 'bg-danger',               'label' => lang('Common.status_rejected')],
                                    ];
                                    $statusInfo  = $statusMap[$member['status'] ?? ''] ?? ['cls' => 'bg-secondary', 'label' => $member['status'] ?? '-'];
                                    $firstLetter = strtoupper(substr(strip_tags($displayName), 0, 1) ?: 'U');
                                ?>
                                <tr class="member-row"
                                    data-name="<?= esc(mb_strtolower($displayName . ' ' . $displayUsername)) ?>">
                                    <td class="ps-4 text-muted"><?= $idx + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <?php if (!empty($displayImg) && file_exists(FCPATH . $displayImg)): ?>
                                                <img src="<?= base_url(esc($displayImg)) ?>"
                                                     class="rounded-circle flex-shrink-0 shadow-sm"
                                                     style="width:40px;height:40px;object-fit:cover;" alt="">
                                            <?php else: ?>
                                                <div class="rounded-circle bg-<?= $roleBadgeClass ?> text-white d-flex align-items-center justify-content-center fw-bold flex-shrink-0"
                                                     style="width:40px;height:40px;font-size:.85rem;">
                                                    <?= $firstLetter ?>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <p class="mb-0 fw-semibold"><?= $displayName ?></p>
                                                <?php if (!empty($displayUsername)): ?>
                                                <small class="text-muted">@<?= $displayUsername ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $roleBadgeClass ?> rounded-pill px-3">
                                            <i class="fas fa-<?= $roleIcons[$memberRole] ?? 'eye' ?> me-1"></i><?= $roleLabel ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $statusInfo['cls'] ?> rounded-pill px-3">
                                            <?= $statusInfo['label'] ?>
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <?php if ($memberRole !== 'owner' || $isSuperAcademy): ?>
                                        <div class="d-flex gap-2 justify-content-end">
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-user-tag me-1"></i><?= lang('Common.role') ?>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width:180px;">
                                                    <li><h6 class="dropdown-header text-muted small"><?= lang('Common.academy_select_role') ?></h6></li>
                                                    <?php foreach ($roleOptions as $rv => $rt):
                                                        $rvColor = $roleColors[$rv] ?? 'secondary';
                                                        $rvIcon  = $roleIcons[$rv]  ?? 'eye';
                                                    ?>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center gap-2 btn-change-role <?= $memberRole === $rv ? 'active' : '' ?>"
                                                                type="button"
                                                                data-id="<?= esc($member['id']) ?>"
                                                                data-role="<?= $rv ?>"
                                                                data-name="<?= esc($displayName) ?>">
                                                            <span class="badge bg-<?= $rvColor ?> rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                                  style="width:22px;height:22px;">
                                                                <i class="fas fa-<?= $rvIcon ?>" style="font-size:.55rem;"></i>
                                                            </span>
                                                            <span class="flex-grow-1"><?= $rt ?></span>
                                                            <?php if ($memberRole === $rv): ?><i class="fas fa-check text-success ms-auto"></i><?php endif; ?>
                                                        </button>
                                                    </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            <?php if ($memberRole !== 'owner'): ?>
                                            <button class="btn btn-sm btn-outline-danger rounded-pill btn-remove-member"
                                                    data-id="<?= esc($member['id']) ?>"
                                                    data-name="<?= esc($displayName) ?>">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                        <?php else: ?>
                                        <span class="text-muted small">–</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="noMemberResult" class="text-center py-4 text-muted d-none">
                        <i class="fas fa-search fa-2x mb-2 opacity-25"></i>
                        <p class="mb-0"><?= lang('Common.no_data_found') ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ============================================================ -->
        <!-- TAB 2: ผู้ฝึกสอน -->
        <!-- ============================================================ -->
        <div class="tab-pane fade" id="tab-coaches" role="tabpanel">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h5 class="fw-bold mb-0 text-success">
                            <i class="fas fa-chalkboard-teacher me-2"></i><?= lang('Common.coach_staff') ?>
                        </h5>
                        <p class="small text-muted mb-0">
                            <?= lang('Common.coach_staff') ?> <?= count($coaches) ?> <?= lang('Common.persons') ?>
                        </p>
                    </div>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="text" id="coachSearch" class="form-control rounded-pill"
                               style="max-width:200px;" placeholder="<?= lang('Common.search_name') ?>..."
                               oninput="filterCoaches()">
                        <button class="btn btn-success rounded-pill px-4 text-nowrap"
                                data-bs-toggle="modal" data-bs-target="#inviteCoachModal">
                            <i class="fas fa-user-plus me-2"></i><?= lang('Common.add_coach') ?>
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($coaches)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-chalkboard-teacher fa-3x mb-3 opacity-25"></i>
                        <p class="mb-0"><?= lang('Common.academy_no_coaches') ?></p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="coachTable">
                            <thead class="bg-light">
                                <tr>
                                    <th class="border-0 ps-4" width="4%">#</th>
                                    <th class="border-0"><?= lang('Common.full_name') ?></th>
                                    <th class="border-0" width="16%"><?= lang('Common.role') ?></th>
                                    <th class="border-0" width="14%"><?= lang('Common.status') ?></th>
                                    <th class="border-0 text-end pe-4" width="22%"><?= lang('Common.manage') ?></th>
                                </tr>
                            </thead>
                            <tbody id="coachTbody">
                                <?php foreach ($coaches as $ci => $c):
                                    $imgSrc = !empty($c['image_path'])
                                        ? base_url($c['image_path'])
                                        : (!empty($c['line_picture']) ? $c['line_picture'] : base_url('public/img/football-player.webp'));
                                    $teamBadges = !empty($c['all_team_names'])
                                        ? array_filter(array_map('trim', explode(',', $c['all_team_names'])))
                                        : (!empty($c['team_name']) ? [$c['team_name']] : []);
                                    $teamIdsStr = !empty($c['team_ids_str']) ? $c['team_ids_str'] : (string)($c['team_id'] ?? '');
                                    try { $encId = bin2hex($encrypter->encrypt($c['id'])); }
                                    catch (\Exception $e) { $encId = ''; }
                                    $cMemberId  = $c['member_id']  ?? null;
                                    $cRole      = $c['member_role'] ?? null;
                                    $cRoleColor = $roleColors[$cRole] ?? 'secondary';
                                    $cRoleLabel = $roleLabelMap[$cRole] ?? '–';
                                    $cFirstLetter = strtoupper(substr($c['first_name'] ?? 'C', 0, 1));
                                ?>
                                <tr class="coach-row"
                                    data-name="<?= esc(mb_strtolower(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '') . ' ' . ($c['nick_name'] ?? ''))) ?>">
                                    <td class="ps-4 text-muted"><?= $ci + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= $imgSrc ?>" class="rounded-circle flex-shrink-0 shadow-sm"
                                                 style="width:40px;height:40px;object-fit:cover;"
                                                 onerror="this.src='<?= base_url('public/img/football-player.webp') ?>'">
                                            <div>
                                                <p class="mb-0 fw-semibold"><?= esc(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')) ?></p>
                                                <div class="d-flex flex-wrap gap-1 mt-1">
                                                    <?php if (!empty($c['nick_name'])): ?>
                                                    <small class="text-muted me-1"><?= esc($c['nick_name']) ?></small>
                                                    <?php endif; ?>
                                                    <?php foreach ($teamBadges as $badge): ?>
                                                    <span class="badge rounded-pill px-2 bg-success bg-opacity-10 text-success" style="font-size:.7rem;">
                                                        <i class="fas fa-shield-alt me-1"></i><?= esc($badge) ?>
                                                    </span>
                                                    <?php endforeach; ?>
                                                    <?php if (!empty($c['cert_level_name'])): ?>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-2" style="font-size:.7rem;">
                                                        <i class="fas fa-certificate me-1"></i><?= esc($c['cert_level_name']) ?>
                                                    </span>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <?php if ($cMemberId && $cRole): ?>
                                        <span class="badge bg-<?= $cRoleColor ?> rounded-pill px-3">
                                            <i class="fas fa-<?= $roleIcons[$cRole] ?? 'eye' ?> me-1"></i><?= $cRoleLabel ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="text-muted small">–</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($c['coach_user_id'])): ?>
                                        <span class="badge bg-success rounded-pill px-3">
                                            <i class="fas fa-circle me-1" style="font-size:.45rem;vertical-align:middle;"></i><?= lang('Common.has_account') ?>
                                        </span>
                                        <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3">
                                            <i class="fas fa-circle me-1" style="font-size:.45rem;vertical-align:middle;"></i><?= lang('Common.no_account') ?>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-end pe-4">
                                        <div class="d-flex gap-2 justify-content-end">
                                            <?php if ($cMemberId): ?>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown">
                                                    <i class="fas fa-user-tag me-1"></i><?= lang('Common.role') ?>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="min-width:180px;">
                                                    <li><h6 class="dropdown-header text-muted small"><?= lang('Common.academy_select_role') ?></h6></li>
                                                    <?php foreach ($roleOptions as $rv => $rt):
                                                        $rvColor = $roleColors[$rv] ?? 'secondary';
                                                        $rvIcon  = $roleIcons[$rv]  ?? 'eye';
                                                    ?>
                                                    <li>
                                                        <button class="dropdown-item d-flex align-items-center gap-2 btn-change-role <?= $cRole === $rv ? 'active' : '' ?>"
                                                                type="button"
                                                                data-id="<?= esc($cMemberId) ?>"
                                                                data-role="<?= $rv ?>"
                                                                data-name="<?= esc(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')) ?>">
                                                            <span class="badge bg-<?= $rvColor ?> rounded-circle d-inline-flex align-items-center justify-content-center flex-shrink-0"
                                                                  style="width:22px;height:22px;">
                                                                <i class="fas fa-<?= $rvIcon ?>" style="font-size:.55rem;"></i>
                                                            </span>
                                                            <span class="flex-grow-1"><?= $rt ?></span>
                                                            <?php if ($cRole === $rv): ?><i class="fas fa-check text-success ms-auto"></i><?php endif; ?>
                                                        </button>
                                                    </li>
                                                    <?php endforeach; ?>
                                                </ul>
                                            </div>
                                            <?php endif; ?>
                                            <?php if (!empty($encId)): ?>
                                            <a href="<?= base_url('player/manage_coach/' . $encId) ?>"
                                               class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                                <i class="fas fa-edit me-1"></i><?= lang('Common.edit_btn') ?>
                                            </a>
                                            <?php endif; ?>
                                            <?php if ($cMemberId): ?>
                                            <button class="btn btn-sm btn-outline-danger rounded-pill btn-remove-coach"
                                                    data-id="<?= esc($cMemberId) ?>"
                                                    data-name="<?= esc(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')) ?>">
                                                <i class="fas fa-user-minus"></i>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div id="noCoachResult" class="text-center py-4 text-muted d-none">
                        <i class="fas fa-search fa-2x mb-2 opacity-25"></i>
                        <p class="mb-0"><?= lang('Common.no_data_found') ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Pending Invitations -->
            <?php if (!empty($pending_invitations)): ?>
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0 text-warning">
                        <i class="fas fa-clock me-2"></i>คำเชิญที่รอการตอบรับ
                        <span class="badge bg-warning-subtle text-warning rounded-pill ms-2"><?= count($pending_invitations) ?></span>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0" width="4%">#</th>
                                    <th class="border-0">ชื่อ-นามสกุล</th>
                                    <th class="border-0" width="18%">ทีม</th>
                                    <th class="text-center border-0" width="14%">สถานะ</th>
                                    <th class="text-center border-0" width="14%">วันที่เชิญ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($pending_invitations as $pi => $inv):
                                    $invImg = !empty($inv['image_path'])
                                        ? base_url($inv['image_path'])
                                        : (!empty($inv['line_picture'])
                                            ? (filter_var($inv['line_picture'], FILTER_VALIDATE_URL) ? $inv['line_picture'] : base_url($inv['line_picture']))
                                            : base_url('public/img/football-player.webp'));
                                ?>
                                <tr>
                                    <td class="ps-4 text-muted"><?= $pi + 1 ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <img src="<?= $invImg ?>" class="rounded-circle border flex-shrink-0"
                                                 style="width:38px;height:38px;object-fit:cover;"
                                                 onerror="this.src='<?= base_url('public/img/football-player.webp') ?>'">
                                            <div>
                                                <div class="fw-semibold"><?= esc(($inv['first_name'] ?? '') . ' ' . ($inv['last_name'] ?? '')) ?></div>
                                                <?php if (!empty($inv['nick_name'])): ?><small class="text-muted"><?= esc($inv['nick_name']) ?></small><?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge rounded-pill px-2 py-1 bg-success bg-opacity-10 text-success" style="font-size:.75rem;">
                                            <i class="fas fa-shield-alt me-1"></i><?= esc($inv['team_name'] ?? '-') ?>
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-warning-subtle text-warning rounded-pill px-2" style="font-size:.72rem;">
                                            <i class="fas fa-hourglass-half me-1"></i>รอการตอบรับ
                                        </span>
                                    </td>
                                    <td class="text-center text-muted small">
                                        <?= !empty($inv['created_at']) ? date('d/m/Y', strtotime($inv['created_at'])) : '-' ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- ============================================================ -->
        <!-- TAB 3: คู่มือสิทธิ์ -->
        <!-- ============================================================ -->
        <div class="tab-pane fade <?= !$canManage ? 'show active' : '' ?>" id="tab-guide" role="tabpanel">
            <!-- Role Cards -->
            <div class="row g-3 mb-4">
                <?php
                $roles = [
                    ['key' => 'owner',  'label' => lang('Common.role_owner'),  'color' => 'primary', 'icon' => 'crown'],
                    ['key' => 'admin',  'label' => lang('Common.role_admin'),  'color' => 'info',    'icon' => 'user-shield'],
                    ['key' => 'coach',  'label' => lang('Common.role_coach'),  'color' => 'success', 'icon' => 'chalkboard-teacher'],
                    ['key' => 'viewer', 'label' => lang('Common.role_viewer'), 'color' => 'secondary','icon' => 'eye'],
                ];
                foreach ($roles as $r):
                ?>
                <div class="col-6 col-md-3">
                    <div class="card h-100 border-0 rounded-4 <?= ($myRoleKey === $r['key']) ? 'border border-' . $r['color'] . ' border-2 shadow-sm' : 'bg-light' ?>">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                <span class="badge bg-<?= $r['color'] ?> rounded-pill px-3 py-2">
                                    <i class="fas fa-<?= $r['icon'] ?> me-1"></i><?= $r['label'] ?>
                                </span>
                                <?php if ($myRoleKey === $r['key']): ?>
                                <span class="badge bg-warning text-dark rounded-pill small">
                                    <i class="fas fa-star me-1"></i>คุณ
                                </span>
                                <?php endif; ?>
                            </div>
                            <p class="small text-muted mb-0" style="line-height:1.55;"><?= $roleDescs[$r['key']] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Permission Matrix Table -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body p-0">
                    <?php
                    $sections = [
                        ['label' => lang('Common.academy_perm_manage_section'), 'icon' => 'cogs',    'color' => 'danger',
                         'items' => [
                            ['key' => 'perm_manage_academy_info',    'owner' => true,  'admin' => true,  'coach' => false, 'viewer' => false],
                            ['key' => 'perm_create_edit_team',       'owner' => true,  'admin' => true,  'coach' => false, 'viewer' => false],
                            ['key' => 'perm_manage_members',         'owner' => true,  'admin' => true,  'coach' => false, 'viewer' => false],
                            ['key' => 'perm_approve_requests',       'owner' => true,  'admin' => true,  'coach' => false, 'viewer' => false],
                            ['key' => 'perm_approve_coach_requests', 'owner' => true,  'admin' => true,  'coach' => false, 'viewer' => false],
                            ['key' => 'perm_create_assessment',      'owner' => true,  'admin' => true,  'coach' => false, 'viewer' => false],
                            ['key' => 'perm_save_assessment_result', 'owner' => true,  'admin' => true,  'coach' => false, 'viewer' => false],
                            ['key' => 'perm_duplicate_session',      'owner' => true,  'admin' => true,  'coach' => false, 'viewer' => false],
                            ['key' => 'perm_delete_topic',           'owner' => true,  'admin' => true,  'coach' => false, 'viewer' => false],
                        ]],
                        ['label' => lang('Common.academy_perm_train_section'),  'icon' => 'dumbbell', 'color' => 'success',
                         'items' => [
                            ['key' => 'perm_create_training_session', 'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => false],
                            ['key' => 'perm_save_attendance',         'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => false],
                            ['key' => 'perm_manage_training_group',   'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => false],
                            ['key' => 'perm_create_training_topic',   'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => false],
                            ['key' => 'perm_manage_idp',              'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => false],
                            ['key' => 'perm_manage_idp_template',     'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => false],
                            ['key' => 'perm_enroll_player',           'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => false],
                        ]],
                        ['label' => lang('Common.academy_perm_view_section'),   'icon' => 'eye',      'color' => 'info',
                         'items' => [
                            ['key' => 'perm_view_academy',     'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => true],
                            ['key' => 'perm_view_teams',       'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => true],
                            ['key' => 'perm_view_players',     'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => true],
                            ['key' => 'perm_view_coaches',     'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => true],
                            ['key' => 'perm_view_reports',     'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => true],
                            ['key' => 'perm_view_training',    'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => true],
                            ['key' => 'perm_view_assessments', 'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => true],
                            ['key' => 'perm_view_idp',         'owner' => true, 'admin' => true, 'coach' => true, 'viewer' => true],
                        ]],
                    ];
                    ?>
                    <?php $isOwner = ($myRoleKey === 'owner'); ?>
                    <?php if ($isOwner): ?>
                    <div class="px-4 py-3 border-bottom d-flex align-items-center gap-2"
                         style="background:#f0f7ff;">
                        <i class="fas fa-edit text-primary"></i>
                        <span class="small text-primary fw-semibold">คลิกที่ ✓ / ✗ เพื่อแก้ไขสิทธิ์ของแต่ละบทบาทได้ (เฉพาะ Owner เท่านั้น)</span>
                    </div>
                    <?php endif; ?>
                    <div class="table-responsive rounded-4">
                        <table class="table table-hover align-middle mb-0 perm-table">
                            <thead class="bg-light">
                                <tr>
                                    <th class="ps-4 border-0" style="width:40%;">
                                        <span class="text-muted fw-semibold" style="font-size:.8rem;"><?= lang('Common.academy_permissions_guide') ?></span>
                                    </th>
                                    <?php foreach ($roles as $r): ?>
                                    <th class="text-center border-0" style="width:15%;">
                                        <span class="badge bg-<?= $r['color'] ?> rounded-pill px-3 py-2" style="font-size:.8rem;">
                                            <i class="fas fa-<?= $r['icon'] ?> me-1"></i><?= $r['label'] ?>
                                        </span>
                                    </th>
                                    <?php endforeach; ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($sections as $section): ?>
                                <tr class="table-light">
                                    <td colspan="5" class="ps-4 py-2 border-0">
                                        <span class="fw-bold text-<?= $section['color'] ?> text-uppercase"
                                              style="font-size:.75rem;letter-spacing:.5px;">
                                            <i class="fas fa-<?= $section['icon'] ?> me-2"></i><?= $section['label'] ?>
                                        </span>
                                    </td>
                                </tr>
                                <?php foreach ($section['items'] as $item): ?>
                                <?php
                                    // Merge default + custom overrides
                                    $effectivePerms = [];
                                    foreach (['owner','admin','coach','viewer'] as $rk) {
                                        $effectivePerms[$rk] = isset($customPerms[$item['key']][$rk])
                                            ? $customPerms[$item['key']][$rk]
                                            : $item[$rk];
                                    }
                                    $rowHighlight = ($effectivePerms[$myRoleKey] ?? false) ? 'perm-row-mine' : '';
                                ?>
                                <tr class="<?= $rowHighlight ?>">
                                    <td class="ps-4 py-3" style="font-size:.875rem;"><?= lang('Common.' . $item['key']) ?></td>
                                    <?php foreach ($roles as $r): ?>
                                    <td class="text-center py-3">
                                        <?php if ($r['key'] === 'owner'): ?>
                                            <!-- Owner always full access, not editable -->
                                            <i class="fas fa-check-circle text-success fs-5"></i>
                                        <?php elseif ($isOwner): ?>
                                            <!-- Editable toggle for owner user -->
                                            <?php $val = $effectivePerms[$r['key']] ?? false; ?>
                                            <button class="btn-perm-toggle border-0 bg-transparent p-0 perm-toggle-clickable"
                                                    type="button"
                                                    title="<?= $val ? 'คลิกเพื่อปิดสิทธิ์' : 'คลิกเพื่อเปิดสิทธิ์' ?>"
                                                    data-key="<?= esc($item['key']) ?>"
                                                    data-role="<?= esc($r['key']) ?>"
                                                    data-allowed="<?= $val ? '1' : '0' ?>">
                                                <i class="fas <?= $val ? 'fa-check-circle text-success' : 'fa-times-circle text-danger opacity-25' ?> fs-5 perm-icon"></i>
                                            </button>
                                        <?php else: ?>
                                            <!-- Read-only for non-owner -->
                                            <?php $val = $effectivePerms[$r['key']] ?? false; ?>
                                            <?php if ($val): ?>
                                            <i class="fas fa-check-circle text-success fs-5"></i>
                                            <?php else: ?>
                                            <i class="fas fa-times-circle text-danger opacity-25 fs-5"></i>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </td>
                                    <?php endforeach; ?>
                                </tr>
                                <?php endforeach; ?>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="d-flex flex-wrap gap-3 px-4 py-3 border-top">
                        <span class="small text-muted"><i class="fas fa-check-circle text-success me-1"></i><?= lang('Common.academy_perm_can') ?></span>
                        <span class="small text-muted"><i class="fas fa-times-circle text-danger opacity-50 me-1"></i><?= lang('Common.academy_perm_cannot') ?></span>
                        <span class="small text-muted"><span class="perm-dot me-1"></span><?= lang('Common.academy_perm_your_role') ?></span>
                        <?php if ($isOwner): ?>
                        <span class="small text-primary ms-auto"><i class="fas fa-mouse-pointer me-1"></i>คลิกเพื่อสลับสิทธิ์</span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

    </div><!-- /tab-content -->
</div>

<!-- ============================================================ -->
<!-- Add Member Modal -->
<!-- ============================================================ -->
<?php if ($canManage): ?>
<div class="modal fade" id="addMemberModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary">
                    <i class="fas fa-user-plus me-2"></i><?= lang('Common.academy_add_member') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="addMemberForm" action="<?= base_url('academy/member-save') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="academy_id" value="<?= esc($academy['id'] ?? '') ?>">
                <div class="modal-body pt-4">
                    <div id="addMemberAlert" class="alert d-none mb-3" role="alert"></div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('Common.academy_search_user') ?></label>
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0" id="memberSearch"
                                   placeholder="<?= lang('Common.academy_search_user_placeholder') ?>" autocomplete="off">
                            <button class="btn btn-primary" type="button" id="btnSearchMember">
                                <i class="fas fa-search me-1"></i><?= lang('Common.search') ?>
                            </button>
                        </div>
                        <div id="searchResultsContainer" style="display:none;" class="mt-2">
                            <div class="list-group shadow-sm rounded-3" id="searchResultsList"></div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('Common.academy_selected_user') ?></label>
                        <div id="selectedUserDisplay" class="bg-light rounded-3 p-3 text-muted small">
                            <i class="fas fa-user me-2"></i><?= lang('Common.academy_no_user_selected') ?>
                        </div>
                        <input type="hidden" name="user_id" id="selectedUserId">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold"><?= lang('Common.role') ?></label>
                        <select class="form-select bg-light border-0" id="memberRole" name="role" required>
                            <option value="" disabled selected>-- <?= lang('Common.academy_select_role') ?> --</option>
                            <option value="admin"><?= lang('Common.role_admin') ?></option>
                            <option value="coach"><?= lang('Common.role_coach') ?></option>
                            <option value="viewer"><?= lang('Common.role_viewer') ?></option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer border-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                    <button type="submit" id="btnAddMember" class="btn btn-primary rounded-pill px-4" disabled>
                        <i class="fas fa-user-plus me-2"></i><?= lang('Common.academy_add_member') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Invite Coach Modal -->
<div class="modal fade" id="inviteCoachModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-success text-white rounded-top-4 py-3">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>ค้นหาและเชิญผู้ฝึกสอน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div class="input-group mb-3">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="inviteCoachSearch" class="form-control border-start-0"
                           placeholder="พิมพ์ชื่อผู้ฝึกสอน (อย่างน้อย 2 ตัวอักษร)..." autocomplete="off">
                </div>
                <?php if (!empty($available_teams) && count($available_teams) > 1): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">เลือกทีมที่จะเชิญเข้า</label>
                    <select id="inviteTeamSelect" class="form-select form-select-sm rounded-pill">
                        <?php foreach ($available_teams as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php elseif (!empty($available_teams)): ?>
                <input type="hidden" id="inviteTeamSelect" value="<?= $available_teams[0]['id'] ?>">
                <?php endif; ?>
                <div id="inviteSearchResults" style="max-height:400px;overflow-y:auto;">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-search fa-2x mb-2 opacity-25"></i>
                        <p class="mb-0 small">พิมพ์ชื่อเพื่อค้นหาผู้ฝึกสอนในระบบ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<style>
/* ── Global font consistency: ใช้ฟอนต์ระบบหลัก (Kanit) ── */
/* หมายเหตุ: ไม่ใส่ !important และไม่ใช้ universal selector
   เพราะจะไป override FontAwesome / Bootstrap Icons ทำให้ icon ไม่ render */
.perm-table td, .perm-table th, .perm-tab-btn,
.swal2-popup, .swal2-title, .swal2-html-container, .swal2-confirm, .swal2-cancel {
    font-family: 'Kanit', sans-serif;
}
.perm-table td, .perm-table th { font-size: .875rem; vertical-align: middle; }
.perm-table thead th { font-size: .8rem; }

/* ── Permission table ── */
.perm-row-mine td:first-child { border-left: 3px solid #16478d; }
.perm-dot { display:inline-block;width:10px;height:10px;border-left:3px solid #16478d;vertical-align:middle; }

/* ── Toggle button ── */
.perm-toggle-clickable {
    cursor: pointer; transition: transform .15s, opacity .15s;
    border-radius: 50%; outline: none;
}
.perm-toggle-clickable:hover { transform: scale(1.25); opacity: .85; }
.perm-toggle-clickable:hover .perm-icon { filter: drop-shadow(0 0 4px currentColor); }

/* ── Custom Tab pills ── */
.perm-tabs-wrap { background:#f8f9fc; border-radius:14px; padding:8px 10px; display:inline-flex; }
.perm-tabs { flex-wrap:wrap; gap:4px !important; }
.perm-tab-btn {
    display: flex; align-items: center; gap: 8px;
    padding: 7px 14px 7px 8px;
    border: none; background: transparent;
    border-radius: 10px; cursor: pointer;
    font-size: .875rem; font-weight: 600; color: #495057;
    transition: background .18s, color .18s;
    white-space: nowrap;
}
.perm-tab-btn:hover { background: #e9ecef; color: #16478d; }
.perm-tab-btn.active { background: #fff; color: #16478d; box-shadow: 0 2px 8px rgba(22,71,141,.12); }
.perm-tab-icon {
    width: 30px; height: 30px; border-radius: 8px;
    display: inline-flex; align-items: center; justify-content: center;
    font-size: .85rem; flex-shrink: 0;
}
.perm-tab-count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 22px; height: 22px; border-radius: 11px;
    font-size: .7rem; font-weight: 700; padding: 0 6px; line-height: 1;
}
@media (max-width: 575.98px) {
    .perm-tabs-wrap { width: 100%; }
    .perm-tab-btn { flex: 1; justify-content: center; }
}
</style>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
var academyId = <?= (int)($academy['id'] ?? 0) ?>;

// Fix dropdown clipped by table-responsive overflow
document.querySelectorAll('.table-responsive .dropdown-toggle').forEach(function(el) {
    new bootstrap.Dropdown(el, {
        popperConfig: function(defaultBsConfig) {
            return Object.assign({}, defaultBsConfig, { strategy: 'fixed' });
        }
    });
});

// ---- Permission Toggle (Owner only) ----
var roleLabels = {
    'owner':  '<?= lang('Common.role_owner') ?>',
    'admin':  '<?= lang('Common.role_admin') ?>',
    'coach':  '<?= lang('Common.role_coach') ?>',
    'viewer': '<?= lang('Common.role_viewer') ?>'
};

document.querySelectorAll('.perm-toggle-clickable').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var key     = this.dataset.key;
        var role    = this.dataset.role;
        var current = this.dataset.allowed === '1';
        var newVal  = !current;
        var icon    = this.querySelector('.perm-icon');
        var self    = this;
        var permLabel = this.closest('tr').querySelector('td:first-child').textContent.trim();
        var roleLabel = roleLabels[role] || role;

        // Optimistic UI update
        self.dataset.allowed = newVal ? '1' : '0';
        icon.className = 'fas perm-icon fs-5 ' + (newVal ? 'fa-check-circle text-success' : 'fa-times-circle text-danger opacity-25');
        self.title = newVal ? 'คลิกเพื่อปิดสิทธิ์' : 'คลิกเพื่อเปิดสิทธิ์';

        var fd = new FormData();
        fd.append('academy_id', academyId);
        fd.append('permission_key', key);
        fd.append('role', role);
        fd.append('allowed', newVal ? '1' : '0');
        fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('academy/permission-toggle') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: fd
        })
        .then(function(r) { return r.json(); })
        .then(function(d) {
            if (d.status) {
                var toastIcon  = newVal ? 'success' : 'warning';
                var toastTitle = newVal
                    ? '<b>' + roleLabel + '</b> ได้รับสิทธิ์ <b>' + permLabel + '</b> แล้ว'
                    : '<b>' + roleLabel + '</b> ถูกถอนสิทธิ์ <b>' + permLabel + '</b> แล้ว';
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: toastIcon,
                    html: toastTitle,
                    showConfirmButton: false,
                    timer: 2500,
                    timerProgressBar: true
                });
            } else {
                // Revert on failure
                self.dataset.allowed = current ? '1' : '0';
                icon.className = 'fas perm-icon fs-5 ' + (current ? 'fa-check-circle text-success' : 'fa-times-circle text-danger opacity-25');
                Swal.fire({ icon: 'error', text: d.message || 'เกิดข้อผิดพลาด', timer: 2000, showConfirmButton: false });
            }
        })
        .catch(function() {
            self.dataset.allowed = current ? '1' : '0';
            icon.className = 'fas perm-icon fs-5 ' + (current ? 'fa-check-circle text-success' : 'fa-times-circle text-danger opacity-25');
            Swal.fire({ icon: 'error', text: 'เกิดข้อผิดพลาดในการเชื่อมต่อ', timer: 2000, showConfirmButton: false });
        });
    });
});

// ---- Role change ----
document.querySelectorAll('.btn-change-role').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id = this.dataset.id, role = this.dataset.role, name = this.dataset.name;
        Swal.fire({
            title: '<?= lang('Common.academy_change_role') ?>',
            html: '<?= lang('Common.academy_change_role_msg') ?>'.replace('%s', name).replace('%s', roleLabels[role] || role),
            icon: 'question', showCancelButton: true,
            confirmButtonColor: '#16478C', cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang('Common.confirm') ?>', cancelButtonText: '<?= lang('Common.cancel') ?>'
        }).then(function(r) {
            if (!r.isConfirmed) return;
            var fd = new FormData();
            fd.append('member_id', id);
            fd.append('role', role);
            fd.append('academy_id', '<?= esc($academy['id'] ?? '') ?>');
            fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            fetch('<?= base_url('academy/member-save') ?>', { method: 'POST', headers: {'X-Requested-With':'XMLHttpRequest'}, body: fd })
            .then(r => r.json()).then(function(d) {
                if (d.status) {
                    Swal.fire({
                        icon: 'success',
                        title: '<?= lang('Common.academy_role_updated') ?>',
                        text: d.message || '',
                        timer: 1800,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '<?= lang('Common.error_title') ?>',
                        text: d.message || '<?= lang('Common.cannot_save') ?>',
                        confirmButtonColor: '#dc3545'
                    });
                }
            }).catch(function() {
                Swal.fire({
                    icon: 'error',
                    title: '<?= lang('Common.error_title') ?>',
                    text: '<?= lang('Common.connection_error_short') ?>',
                    confirmButtonColor: '#dc3545'
                });
            });
        });
    });
});

// ---- Remove member ----
document.querySelectorAll('.btn-remove-member').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id = this.dataset.id, name = this.dataset.name;
        Swal.fire({
            title: '<?= lang('Common.academy_remove_member') ?>',
            html: '<?= lang('Common.academy_remove_member_msg') ?>'.replace('%s', name),
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang('Common.academy_yes_remove') ?>', cancelButtonText: '<?= lang('Common.cancel') ?>'
        }).then(function(r) {
            if (!r.isConfirmed) return;
            var fd = new FormData(); fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            fetch('<?= base_url('academy/member-remove') ?>/' + id, { method: 'POST', headers: {'X-Requested-With':'XMLHttpRequest'}, body: fd })
            .then(r => r.json()).then(function(d) {
                if (d.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'นำออกสำเร็จ',
                        text: d.message || ('นำ ' + name + ' ออกจาก Academy เรียบร้อยแล้ว'),
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถนำออกได้',
                        text: d.message || 'เกิดข้อผิดพลาด',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                    confirmButtonColor: '#dc3545'
                });
            });
        });
    });
});

// ---- Remove coach from academy ----
document.querySelectorAll('.btn-remove-coach').forEach(function(btn) {
    btn.addEventListener('click', function() {
        var id = this.dataset.id, name = this.dataset.name;
        Swal.fire({
            title: 'นำผู้ฝึกสอนออก',
            html: 'ต้องการนำ <b>' + name + '</b> ออกจาก Academy นี้ใช่หรือไม่?',
            icon: 'warning', showCancelButton: true,
            confirmButtonColor: '#dc3545', cancelButtonColor: '#6c757d',
            confirmButtonText: 'นำออก', cancelButtonText: '<?= lang('Common.cancel') ?>'
        }).then(function(r) {
            if (!r.isConfirmed) return;
            var fd = new FormData(); fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
            fetch('<?= base_url('academy/member-remove') ?>/' + id, { method: 'POST', headers: {'X-Requested-With':'XMLHttpRequest'}, body: fd })
            .then(r => r.json()).then(function(d) {
                if (d.status) {
                    Swal.fire({
                        icon: 'success',
                        title: 'นำออกสำเร็จ',
                        text: d.message || ('นำ ' + name + ' ออกจาก Academy เรียบร้อยแล้ว'),
                        timer: 2000,
                        showConfirmButton: false
                    }).then(() => location.reload());
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'ไม่สามารถนำออกได้',
                        text: d.message || 'เกิดข้อผิดพลาด',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(function(err) {
                Swal.fire({
                    icon: 'error',
                    title: 'เกิดข้อผิดพลาด',
                    text: 'ไม่สามารถเชื่อมต่อกับเซิร์ฟเวอร์ได้',
                    confirmButtonColor: '#dc3545'
                });
            });
        });
    });
});

// ---- Add Member Modal ----
<?php if ($canManage): ?>
var memberSearch = document.getElementById('memberSearch');
var btnSearch    = document.getElementById('btnSearchMember');
var resContainer = document.getElementById('searchResultsContainer');
var resList      = document.getElementById('searchResultsList');
var selDisplay   = document.getElementById('selectedUserDisplay');
var selId        = document.getElementById('selectedUserId');
var btnAdd       = document.getElementById('btnAddMember');
var addAlert     = document.getElementById('addMemberAlert');
var searchTimer  = null;

function doSearch() {
    var q = memberSearch.value.trim();
    if (!q) { resContainer.style.display = 'none'; return; }
    resList.innerHTML = '<div class="list-group-item text-center text-muted py-3"><i class="fas fa-spinner fa-spin me-2"></i><?= lang('Common.academy_searching') ?></div>';
    resContainer.style.display = 'block';
    var fd = new FormData();
    fd.append('query', q); fd.append('academy_id', '<?= esc($academy['id'] ?? '') ?>');
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');
    fetch('<?= base_url('academy/member-search') ?>', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd })
    .then(r => r.json()).then(function(d) {
        if (!d.status || !d.results || !d.results.length) {
            resList.innerHTML = '<div class="list-group-item text-center text-muted py-3"><?= lang('Common.academy_user_not_found') ?></div>'; return;
        }
        resList.innerHTML = d.results.map(function(u) {
            return '<button type="button" class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-2 px-3" onclick="selectMemberUser(\'' + u.id + '\',\'' + u.display_name.replace(/'/g,"\\'") + '\')">'
                + '<i class="fas fa-user text-secondary"></i><span class="fw-semibold">' + u.display_name + '</span>'
                + (u.username ? '<small class="text-muted ms-auto">@' + u.username + '</small>' : '') + '</button>';
        }).join('');
    });
}
btnSearch.addEventListener('click', doSearch);
memberSearch.addEventListener('keydown', function(e) { if (e.key === 'Enter') { e.preventDefault(); doSearch(); } });
memberSearch.addEventListener('input', function() {
    clearTimeout(searchTimer);
    if (!this.value.trim()) { resContainer.style.display = 'none'; return; }
    searchTimer = setTimeout(doSearch, 400);
});
document.addEventListener('click', function(e) {
    if (!resContainer.contains(e.target) && e.target !== memberSearch && e.target !== btnSearch)
        resContainer.style.display = 'none';
});
document.getElementById('addMemberForm').addEventListener('submit', function(e) {
    if (!selId.value) { e.preventDefault(); addAlert.className='alert alert-warning mb-3'; addAlert.textContent='<?= lang('Common.academy_please_select_user') ?>'; addAlert.classList.remove('d-none'); }
});
document.getElementById('addMemberModal').addEventListener('hidden.bs.modal', function() {
    memberSearch.value = ''; selId.value = ''; btnAdd.disabled = true;
    selDisplay.innerHTML = '<i class="fas fa-user me-2"></i><?= lang('Common.academy_no_user_selected') ?>';
    document.getElementById('memberRole').value = '';
    resContainer.style.display = 'none'; resList.innerHTML = '';
    addAlert.classList.add('d-none');
});

// ---- Invite Coach ----
document.getElementById('inviteCoachSearch').addEventListener('input', function() {
    clearTimeout(searchTimer);
    var kw = this.value.trim();
    if (kw.length < 2) { document.getElementById('inviteSearchResults').innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-search fa-2x mb-2 opacity-25"></i><p class="mb-0 small">พิมพ์ชื่อเพื่อค้นหา</p></div>'; return; }
    searchTimer = setTimeout(function() { searchCoaches(kw); }, 400);
});

function searchCoaches(kw) {
    var el = document.getElementById('inviteSearchResults');
    el.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-success"></div></div>';
    var fd = new FormData(); fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>'); fd.append('keyword', kw); fd.append('academy_id', academyId);
    fetch('<?= base_url('academy/coach-search') ?>', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd })
    .then(r => r.json()).then(function(res) {
        if (!res.data || !res.data.length) { el.innerHTML='<div class="text-center text-muted py-4"><p class="small mb-0">ไม่พบผู้ฝึกสอน</p></div>'; return; }
        el.innerHTML = '<div class="list-group list-group-flush">' + res.data.map(function(c) {
            var nm = (c.first_name||'') + ' ' + (c.last_name||'') + (c.nick_name ? ' (' + c.nick_name + ')' : '');
            var action = c.is_pending
                ? '<span class="badge bg-warning-subtle text-warning rounded-pill px-3 py-2"><i class="fas fa-clock me-1"></i>รอตอบรับ</span>'
                : '<button class="btn btn-success btn-sm rounded-pill px-3" onclick="inviteCoach(' + c.id + ',this)"><i class="fas fa-paper-plane me-1"></i>เชิญ</button>';
            return '<div class="list-group-item d-flex align-items-center justify-content-between py-3 px-3"><div class="d-flex align-items-center gap-3"><img src="' + c.image_url + '" class="rounded-circle border" style="width:42px;height:42px;object-fit:cover;" onerror="this.src=\'<?= base_url("public/img/football-player.webp") ?>\'"><span class="fw-semibold">' + nm + '</span></div>' + action + '</div>';
        }).join('') + '</div>';
    });
}

function inviteCoach(id, btn) {
    var tel = document.getElementById('inviteTeamSelect');
    var tid = tel ? tel.value : '';
    if (!tid) { Swal.fire({icon:'warning', title:'กรุณาเลือกทีม', timer:2000, showConfirmButton:false}); return; }
    btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';
    var fd = new FormData(); fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>'); fd.append('coach_id', id); fd.append('team_id', tid);
    fetch('<?= base_url('academy/coach-invite') ?>', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body:fd })
    .then(r => r.json()).then(function(res) {
        if (res.status === 'success') { btn.outerHTML='<span class="badge bg-success rounded-pill px-3 py-2">ส่งแล้ว</span>'; Swal.fire({icon:'success', title:res.message, timer:1500, showConfirmButton:false}).then(() => location.reload()); }
        else { btn.disabled=false; btn.innerHTML='<i class="fas fa-paper-plane me-1"></i>เชิญ'; Swal.fire({icon:'error', title:res.message, timer:2500, showConfirmButton:false}); }
    });
}
<?php endif; ?>

function filterMembers() {
    var q = (document.getElementById('memberFilterSearch')?.value || '').toLowerCase();
    var rows = document.querySelectorAll('.member-row');
    var shown = 0;
    rows.forEach(function(row) {
        var visible = !q || (row.getAttribute('data-name') || '').indexOf(q) > -1;
        row.style.display = visible ? '' : 'none';
        if (visible) shown++;
    });
    var no = document.getElementById('noMemberResult');
    if (no) no.classList.toggle('d-none', shown > 0);
}

function filterCoaches() {
    var q = (document.getElementById('coachSearch')?.value||'').toLowerCase();
    var rows = document.querySelectorAll('.coach-row');
    var shown = 0;
    rows.forEach(function(row) {
        var visible = !q || (row.getAttribute('data-name')||'').indexOf(q) > -1;
        row.style.display = visible ? '' : 'none';
        if (visible) shown++;
    });
    var no = document.getElementById('noCoachResult');
    if (no) no.classList.toggle('d-none', shown > 0);
}

function selectMemberUser(uid, name) {
    document.getElementById('selectedUserId').value = uid;
    document.getElementById('selectedUserDisplay').innerHTML = '<i class="fas fa-user-check me-2 text-success"></i><span class="fw-semibold text-dark">' + name + '</span>';
    document.getElementById('searchResultsContainer').style.display = 'none';
    document.getElementById('searchResultsList').innerHTML = '';
    document.getElementById('memberSearch').value = '';
    document.getElementById('btnAddMember').disabled = false;
    document.getElementById('addMemberAlert').classList.add('d-none');
}
</script>
<?php $this->endSection(); ?>
