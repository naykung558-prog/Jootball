<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>

<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <?php if (empty($academy)): ?>
        <!-- ============================================================ -->
        <!-- Section 2: No Academy State                                   -->
        <!-- ============================================================ -->
        <div class="row justify-content-center g-4 mt-3 mb-5">
            <!-- No Academy Card -->
            <div class="<?= !empty($userRequests) ? 'col-lg-4' : 'col-lg-6' ?>">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden h-100">
                    <div class="card-body text-center p-5 d-flex flex-column justify-content-center">
                        <div class="mb-4">
                            <i class="fas fa-school fa-5x text-primary opacity-50"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-3">
                            <?= lang('Common.academy_no_academy') ?>
                        </h2>
                        <p class="text-muted mb-4">
                            <?= lang('Common.academy_no_academy_desc') ?>
                        </p>
                        <div class="d-grid gap-2 col-md-10 mx-auto">
                            <a href="<?= base_url('academy/register') ?>" class="btn btn-primary btn-lg rounded-pill py-3">
                                <i class="fas fa-plus-circle me-2"></i>
                                <?= lang('Common.academy_create_my') ?>
                            </a>
                            <a href="<?= base_url('academy/join') ?>" class="btn btn-outline-secondary btn-lg rounded-pill py-3">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                <?= lang('Common.academy_join') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($userRequests)): ?>
            <!-- Pending Join Requests -->
            <div class="col-lg-8">
                <div class="card shadow border-0 rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                        <div class="avatar-sm bg-soft-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-history text-primary font-20"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">
                                <?= lang('Common.academy_join_history') ?>
                            </h5>
                            <p class="mb-0 text-muted small">
                                <?= lang('Common.academy_join_history_desc') ?>
                            </p>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 border-0 text-uppercase text-muted small fw-bold">
                                            <?= lang('Common.academy_info') ?>
                                        </th>
                                        <th class="py-3 border-0 text-uppercase text-muted small fw-bold"><?= lang('Common.status') ?></th>
                                        <th class="py-3 border-0 text-uppercase text-muted small fw-bold"><?= lang('Common.request_date') ?></th>
                                        <th class="py-3 border-0 text-end pe-4 text-uppercase text-muted small fw-bold"><?= lang('Common.message') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userRequests as $req): ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <?php if (!empty($req['logo'])): ?>
                                                    <img src="<?= base_url($req['logo']) ?>"
                                                         class="rounded-circle avatar-sm me-3 shadow-sm border border-2 border-white"
                                                         alt="academy-logo"
                                                         style="width: 42px; height: 42px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-gradient-primary rounded-circle text-white shadow-sm font-18 fw-bold">
                                                            <?= strtoupper(substr($req['academy_name'] ?? 'A', 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark font-15"><?= esc($req['academy_name']) ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <?php
                                            $statusBadge = 'bg-soft-warning text-warning';
                                            $statusIcon  = 'fa-clock';
                                            $statusText  = lang('Common.waiting_approval');

                                            if ($req['status'] === 'approved') {
                                                $statusBadge = 'bg-soft-success text-success';
                                                $statusIcon  = 'fa-check-circle';
                                                $statusText  = lang('Common.approved');
                                            } elseif ($req['status'] === 'rejected') {
                                                $statusBadge = 'bg-soft-danger text-danger';
                                                $statusIcon  = 'fa-times-circle';
                                                $statusText  = lang('Common.rejected');
                                            }
                                            ?>
                                            <span class="badge <?= $statusBadge ?> rounded-pill font-13 fw-medium px-3 py-1 d-inline-flex align-items-center border-0">
                                                <i class="fas <?= $statusIcon ?> me-1"></i> <?= $statusText ?>
                                            </span>
                                        </td>
                                        <td class="text-muted py-3">
                                            <span class="d-flex align-items-center">
                                                <i class="far fa-calendar-alt me-2 text-primary opacity-50"></i>
                                                <?= date('d/m/Y', strtotime($req['created_at'])) ?>
                                                <span class="ms-2 small text-muted"><?= date('H:i', strtotime($req['created_at'])) ?></span>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 py-3">
                                            <?php if ($req['status'] === 'rejected'): ?>
                                                <span class="text-danger small"><i class="fas fa-exclamation-circle me-1"></i>
                                                    <?= lang('Common.academy_request_rejected') ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

    <?php elseif (empty($team)): ?>
        <!-- ============================================================ -->
        <!-- Section 3: Academy Dashboard (no team selected)              -->
        <!-- ============================================================ -->

        <!-- Row 1: Academy Profile Card + Academy Info -->
        <div class="row g-4 mb-4">
            <!-- Academy Profile Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100 rounded-4 overflow-hidden">
                    <div class="card-body text-center p-5 bg-light position-relative">
                        <div class="mb-4">
                            <?php if (!empty($academy['logo'])): ?>
                                <img src="<?= base_url($academy['logo']) ?>" alt="Academy Logo"
                                     class="img-fluid rounded-circle shadow-sm border border-4 border-white"
                                     style="width: 180px; height: 180px; object-fit: cover;">
                            <?php else: ?>
                                <img src="<?= base_url('public/img/exam-logo.webp') ?>" alt="Default Logo"
                                     class="img-fluid rounded-circle shadow-sm border border-4 border-white"
                                     style="width: 180px; height: 180px; object-fit: cover;">
                            <?php endif; ?>
                        </div>

                        <h3 class="fw-bold text-dark mb-1"><?= esc($academy['name']) ?></h3>
                        <?php if (!empty($academy['name_en'])): ?>
                            <p class="text-muted mb-2"><?= esc($academy['name_en']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($academy['description'])): ?>
                            <p class="text-muted small mb-3"><?= esc($academy['description']) ?></p>
                        <?php endif; ?>

                        <?php if (!empty($perms['perm_manage_academy_info'])): ?>
                        <div class="d-grid gap-2 mt-4">
                            <a href="<?= base_url('academy/manage/' . $academy['id']) ?>" class="btn btn-primary rounded-pill py-2">
                                <i class="fa fa-edit me-2"></i> <?= lang('Common.edit_information') ?>
                            </a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Academy Info Card -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fa fa-info-circle me-2"></i>
                            <?= lang('Common.academy_information') ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.coordinator_name') ?></label>
                                    <div class="fw-medium"><?= !empty($academy['contact_name']) ? esc($academy['contact_name']) : '-' ?></div>
                                    <?php if (!empty($academy['contact_phone'])): ?>
                                        <div class="small text-muted mt-1"><i class="fa fa-phone me-1"></i> <?= esc($academy['contact_phone']) ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.email') ?></label>
                                    <div class="fw-medium">
                                        <?php if (!empty($academy['contact_email'])): ?>
                                            <i class="fa fa-envelope me-1 text-muted"></i> <?= esc($academy['contact_email']) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.line_id') ?></label>
                                    <div class="fw-medium text-success">
                                        <?php if (!empty($academy['contact_line'])): ?>
                                            <i class="fab fa-line me-1"></i> <?= esc($academy['contact_line']) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.location') ?></label>
                                    <div class="fw-medium">
                                        <?php
                                        $locationParts = [];
                                        if (!empty($academy['province_name'])) $locationParts[] = esc($academy['province_name']);
                                        echo !empty($locationParts) ? implode(', ', $locationParts) : '-';
                                        ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Map Card (hidden to save Google Maps API quota) -->
                <?php /* if (!empty($academy['latitude']) && !empty($academy['longitude'])): ?>
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fa fa-map-marker-alt me-2"></i><?= lang('Common.location') ?>
                        </h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="map-canvas" style="height: 350px; width: 100%;"></div>
                    </div>
                </div>
                <?php endif; */ ?>
            </div>
        </div>

        <!-- Row 2: Stats Cards -->
        <div class="row g-4 mb-4">
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 text-center p-4">
                    <div class="mb-3">
                        <span class="avatar-lg bg-soft-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-shield-alt fa-2x text-primary"></i>
                        </span>
                    </div>
                    <h2 class="fw-bold text-dark mb-1"><?= isset($stats['team_count']) ? (int)$stats['team_count'] : 0 ?></h2>
                    <p class="text-muted mb-0">
                        <?= lang('Common.academy_teams_label') ?>
                    </p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 text-center p-4">
                    <div class="mb-3">
                        <span class="avatar-lg bg-soft-success rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-running fa-2x text-success"></i>
                        </span>
                    </div>
                    <h2 class="fw-bold text-dark mb-1"><?= isset($stats['player_count']) ? (int)$stats['player_count'] : 0 ?></h2>
                    <p class="text-muted mb-0"><?= lang('Common.players') ?></p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card shadow-sm border-0 rounded-4 text-center p-4">
                    <div class="mb-3">
                        <span class="avatar-lg bg-soft-warning rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                            <i class="fas fa-chalkboard-teacher fa-2x text-warning"></i>
                        </span>
                    </div>
                    <h2 class="fw-bold text-dark mb-1"><?= isset($stats['coach_count']) ? (int)$stats['coach_count'] : 0 ?></h2>
                    <p class="text-muted mb-0"><?= lang('Common.coaches') ?></p>
                </div>
            </div>
        </div>


    <?php else: ?>
        <!-- ============================================================ -->
        <!-- Section 4: Team View within Academy                          -->
        <!-- ============================================================ -->

        <!-- Team Profile Row -->
        <div class="row g-4 mb-4">
            <!-- Team Profile Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100 rounded-4 overflow-hidden">
                    <div class="card-body text-center p-5 bg-light position-relative">
                        <!-- Back to Academy link -->
                        <?php if (!empty($academy)): ?>
                        <div class="text-start mb-3">
                            <a href="<?= base_url('academy/info/' . $academy['id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                                <i class="fas fa-arrow-left me-1"></i>
                                <?= lang('Common.back_to_academy') ?>
                            </a>
                        </div>
                        <?php endif; ?>

                        <div class="mb-4">
                            <?php if (!empty($team['logo'])): ?>
                                <img src="<?= base_url($team['logo']) ?>" alt="Team Logo"
                                     class="img-fluid rounded-circle shadow-sm border border-4 border-white"
                                     style="width: 180px; height: 180px; object-fit: cover;">
                            <?php else: ?>
                                <img src="<?= base_url('public/img/exam-logo.webp') ?>" alt="Default Logo"
                                     class="img-fluid rounded-circle shadow-sm border border-4 border-white"
                                     style="width: 180px; height: 180px; object-fit: cover;">
                            <?php endif; ?>
                        </div>

                        <h3 class="fw-bold text-dark mb-1"><?= esc($team['name']) ?></h3>
                        <?php if (!empty($team['name_en'])): ?>
                            <p class="text-muted mb-2"><?= esc($team['name_en']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($perms['perm_manage_academy_info']) || !empty($perms['perm_create_edit_team'])): ?>
                        <div class="d-grid gap-2 mt-4">
                            <?php if (!empty($perms['perm_manage_academy_info'])): ?>
                            <a href="<?= base_url('academy/team-create/' . ($academy['id'] ?? ''). '/' . ($team['id'] ?? '')) ?>" class="btn btn-primary rounded-pill py-2">
                                <i class="fa fa-edit me-2"></i> <?= lang('Common.edit_information') ?>
                            </a>
                            <?php endif; ?>
                            <?php if (!empty($perms['perm_create_edit_team'])): ?>
                            <a href="<?= base_url('academy/player-list/' . ($academy['id'] ?? '') . '/' . ($team['id'] ?? '')) ?>" class="btn btn-outline-primary rounded-pill py-2">
                                <i class="fas fa-users-cog me-2"></i><?= lang('Common.team_player_manager') ?>
                            </a>
                            <a href="<?= base_url('academy/team-coach/manage/' . ($academy['id'] ?? '') . '/' . ($team['id'] ?? '')) ?>" class="btn btn-outline-info rounded-pill py-2">
                                <i class="fas fa-chalkboard-teacher me-2"></i><?= lang('Common.team_coach_manager') ?>
                            </a>
                            <a href="<?= base_url('academy/player-chart/' . ($academy['id'] ?? '') . '/' . ($team['id'] ?? '')) ?>" class="btn btn-outline-success rounded-pill py-2">
                                <i class="fas fa-images me-2"></i><?= lang('Common.player_chart') ?>
                            </a>
                            <a href="<?= base_url('academy/team-jersey/' . ($academy['id'] ?? '') . '/' . ($team['id'] ?? '')) ?>" class="btn btn-outline-warning rounded-pill py-2">
                                <i class="fas fa-tshirt me-2"></i><?= lang('Common.jersey_manager') ?>
                            </a>
                            <?php endif; ?>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Team Info + Map -->
            <div class="col-lg-8">
                <div class="card shadow-sm border-0 rounded-4 mb-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fa fa-info-circle me-2"></i><?= lang('Common.team_information') ?>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.organization_name') ?></label>
                                    <div class="fw-medium"><?= !empty($team['organize_name']) ? esc($team['organize_name']) : '-' ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.head_coach_name') ?></label>
                                    <div class="fw-medium">
                                        <?= !empty($team['coach_name']) ? esc($team['coach_name']) : '-' ?>
                                        <?php if (!empty($team['coach_phone'])): ?>
                                            <div class="small text-muted mt-1"><i class="fa fa-phone me-1"></i> <?= esc($team['coach_phone']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.coordinator_name') ?></label>
                                    <div class="fw-medium">
                                        <?= !empty($team['contact_name']) ? esc($team['contact_name']) : '-' ?>
                                        <?php if (!empty($team['contact_phone'])): ?>
                                            <div class="small text-muted mt-1"><i class="fa fa-phone me-1"></i> <?= esc($team['contact_phone']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="p-3 bg-light rounded-3 h-100">
                                    <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.line_id') ?></label>
                                    <div class="fw-medium text-success">
                                        <?php if (!empty($team['contact_line'])): ?>
                                            <i class="fab fa-line me-1"></i> <?= esc($team['contact_line']) ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <?php /* Map Card - Team (hidden to save Google Maps API quota)
                <?php if (!empty($team['latitude']) && !empty($team['longitude'])): ?>
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-primary"><i class="fa fa-map-marker-alt me-2"></i><?= lang('Common.location') ?></h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="map-canvas" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
                <?php endif; ?>
                */ ?>
            </div>
        </div>

        <!-- Players DataTable with Pagination -->
        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-users me-2"></i><?= lang('Common.player_list') ?>
                        </h5>
                        <a href="<?= base_url('academy/player-list/' . ($academy['id'] ?? '') . '/' . $team['id']) ?>"
                           class="btn btn-outline-primary btn-sm rounded-pill">
                            <i class="fas fa-list me-1"></i><?= lang('Common.view_all') ?>
                        </a>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="playerTable" class="table table-hover align-middle mb-0 text-nowrap" style="width:100%">
                                <thead class="bg-primary text-white">
                                    <tr>
                                        <th class="text-center py-3 border-0 ps-4" width="5%">#</th>
                                        <th class="text-center py-3 border-0" width="8%"><?= lang('Common.image') ?></th>
                                        <th class="text-center py-3 border-0" width="8%"><?= lang('Common.shirt_number') ?></th>
                                        <th class="py-3 border-0"><?= lang('Common.full_name') ?></th>
                                        <th class="py-3 border-0"><?= lang('Common.nickname') ?></th>
                                        <th class="text-center py-3 border-0" width="12%"><?= lang('Common.birth_date') ?></th>
                                        <th class="text-center py-3 border-0 pe-4" width="10%"><?= lang('Common.position') ?></th>
                                    </tr>
                                </thead>
                                <tbody></tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Coaches List Table -->
        <?php if (!empty($coaches)): ?>
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-users me-2"></i><?= lang('Common.coach_list') ?></h5>
                    </div>
                    <div class="card-body px-4 pb-4 pt-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width:50px;">#</th>
                                        <th><?= lang('Common.name') ?></th>
                                        <th><?= lang('Common.position') ?></th>
                                        <th><?= lang('Common.phone') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($coaches as $i => $coach): ?>
                                    <?php
                                        // Check approval_status

                                        $coachName = trim(($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? ''));
                                        $coachImg = '';
                                        if (!empty($coach['line_picture'])) {
                                            $coachImg = (strpos($coach['line_picture'], 'http') === 0) ? $coach['line_picture'] : base_url($coach['line_picture']);
                                        } elseif (!empty($coach['image_path'])) {
                                            $coachImg = (strpos($coach['image_path'], 'http') === 0) ? $coach['image_path'] : base_url($coach['image_path']);
                                        }
                                        $role = !empty($coach['role']) ? $coach['role'] : 'coach';
                                        $roleKey = 'Common.coach_role_' . $role;
                                        $roleLabel = lang($roleKey);
                                        if ($roleLabel === $roleKey) {
                                            $roleLabel = ucfirst(str_replace('_', ' ', $role));
                                        }
                                    ?>
                                    <tr>
                                        <td class="text-muted"><?= $i + 1 ?></td>
                                        <td>
                                            <div class="d-flex align-items-center gap-2">
                                                <?php if (!empty($coachImg)): ?>
                                                    <img src="<?= $coachImg ?>" class="rounded-circle border" style="width:36px;height:36px;object-fit:cover;">
                                                <?php else: ?>
                                                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($coachName) ?>&size=36&background=0d6efd&color=fff&bold=true&format=svg" class="rounded-circle" style="width:36px;height:36px;">
                                                <?php endif; ?>
                                                <span class="fw-medium"><?= esc($coachName) ?></span>
                                            </div>
                                        </td>
                                        <td><span class="badge bg-info bg-opacity-10 text-info"><?= $roleLabel ?></span></td>
                                        <td class="text-muted"><?= !empty($coach['phone']) ? esc($coach['phone']) : '-' ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tournament History Table -->
        <div class="row g-4 mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-primary"><i class="ti ti-trophy me-2"></i><?= lang('Common.tournament_history') ?></h5>
                    </div>
                    <div class="card-body px-4 pb-4 pt-0">
                        <?php if (!empty($tournaments)): ?>
                            <?php $langField = (function_exists('get_current_language') && get_current_language() === 'en') ? 'name_en' : 'name'; ?>
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width:50px;">#</th>
                                            <th><?= lang('Common.tournament') ?></th>
                                            <th><?= lang('Common.date') ?></th>
                                            <th class="text-center"><?= lang('Common.matches_played') ?></th>
                                            <th class="text-center"><?= lang('Common.status') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($tournaments as $i => $tour): ?>
                                        <tr>
                                            <td class="text-muted"><?= $i + 1 ?></td>
                                            <td>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if (!empty($tour['logo'])): ?>
                                                        <img src="<?= base_url($tour['logo']) ?>" class="rounded" style="width:36px;height:36px;object-fit:cover;">
                                                    <?php else: ?>
                                                        <img src="<?= base_url('public/img/jootball_logo.png') ?>" class="rounded" style="width:36px;height:36px;object-fit:cover;">
                                                    <?php endif; ?>
                                                    <span class="fw-medium"><?= esc(!empty($tour[$langField]) ? $tour[$langField] : $tour['name']) ?></span>
                                                </div>
                                            </td>
                                            <td class="text-muted" style="font-size:0.88rem;">
                                                <?php
                                                    $start = !empty($tour['start_date']) ? date('d/m/Y', strtotime($tour['start_date'])) : '-';
                                                    $end = !empty($tour['end_date']) ? date('d/m/Y', strtotime($tour['end_date'])) : '-';
                                                    echo $start . ' - ' . $end;
                                                ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ((int)$tour['match_count'] > 0): ?>
                                                    <span class="badge bg-primary bg-opacity-10 text-primary"><?= (int)$tour['match_count'] ?> <?= lang('Common.matches_unit') ?></span>
                                                <?php else: ?>
                                                    <span class="text-muted">-</span>
                                                <?php endif; ?>
                                            </td>
                                            <td class="text-center">
                                                <?php if ((int)$tour['register_status'] === 0): ?>
                                                    <span class="badge bg-warning text-dark"><?= lang('Common.waiting_approval') ?></span>
                                                <?php else: ?>
                                                    <span class="badge bg-success"><?= lang('Common.approved') ?></span>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <div class="text-center text-muted py-4">
                                <i class="ti ti-trophy-off" style="font-size:2.5rem;"></i>
                                <p class="mt-2 mb-0"><?= lang('Common.no_tournament_history') ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (false): // player manager moved to team_player_manage.php ?>
        <div class="row g-4 mb-5">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4"
                         data-bs-toggle="collapse" data-bs-target="#playerManagerCollapse"
                         style="cursor:pointer;">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="mb-0 fw-bold" style="color:#198754;">
                                <i class="fas fa-user-cog me-2"></i><?= lang('Common.team_player_manager') ?>
                                <span class="badge bg-success rounded-pill ms-2" id="pickerSelectedCount">0</span>
                            </h5>
                            <i class="fas fa-chevron-down text-muted"></i>
                        </div>
                    </div>
                    <div class="collapse" id="playerManagerCollapse">
                        <div class="card-body p-4">

                            <!-- Filter bar -->
                            <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
                                <button type="button" class="btn btn-sm btn-success picker-filter-btn active" data-filter="all"
                                        onclick="filterPicker(this,'all')"><?= lang('Common.all') ?></button>
                                <button type="button" class="btn btn-sm btn-outline-success picker-filter-btn" data-filter="in"
                                        onclick="filterPicker(this,'in')"><?= lang('Common.team_player_in_team') ?></button>
                                <button type="button" class="btn btn-sm btn-outline-secondary picker-filter-btn" data-filter="out"
                                        onclick="filterPicker(this,'out')"><?= lang('Common.team_player_other_team') ?></button>
                                <input type="text" id="pickerSearch" class="form-control form-control-sm ms-auto"
                                       style="max-width:200px;" placeholder="<?= lang('Common.search_name') ?>..."
                                       oninput="filterPicker(null,'search')">
                            </div>

                            <!-- Player picker grid -->
                            <div class="row g-2" id="playerPickerGrid">
                                <?php foreach ($allPlayers as $p):
                                    $inTeam     = ((int)$p['team_id'] === (int)$team['id']);
                                    $playerName = urlencode(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''));
                                    $imgSrc     = !empty($p['image_path'])
                                        ? base_url($p['image_path'])
                                        : 'https://ui-avatars.com/api/?name=' . $playerName . '&size=80&background=0d6efd&color=fff&bold=true&format=svg';
                                ?>
                                <div class="col-6 col-md-4 col-lg-3 col-xl-2 picker-item"
                                     data-player-id="<?= esc($p['id']) ?>"
                                     data-in-team="<?= $inTeam ? '1' : '0' ?>"
                                     data-player-name="<?= esc(mb_strtolower(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''))) ?>"
                                     onclick="togglePlayer(this)">
                                    <div class="card h-100 border-2 picker-card <?= $inTeam ? 'selected' : '' ?>"
                                         style="cursor:pointer; transition:all 0.15s; user-select:none;">
                                        <div class="card-body text-center p-2 position-relative">
                                            <div class="picker-check position-absolute" style="top:6px;left:6px;<?= $inTeam ? '' : 'display:none;' ?>">
                                                <i class="fas fa-check-circle text-success" style="font-size:1rem;"></i>
                                            </div>
                                            <?php if (!empty($p['shirt_number'])): ?>
                                            <div class="position-absolute" style="top:4px;right:6px;">
                                                <span class="badge bg-secondary" style="font-size:0.6rem;min-width:18px;"><?= esc($p['shirt_number']) ?></span>
                                            </div>
                                            <?php endif; ?>
                                            <img src="<?= $imgSrc ?>"
                                                 class="rounded-circle mb-1 border border-2"
                                                 style="width:52px;height:52px;object-fit:cover;"
                                                 onerror="this.src='https://ui-avatars.com/api/?name=P&size=80&background=6c757d&color=fff&format=svg'">
                                            <div class="fw-bold text-dark lh-1 mt-1" style="font-size:0.78rem;"><?= esc($p['first_name'] ?? '') ?></div>
                                            <div class="text-muted lh-1" style="font-size:0.72rem;"><?= esc($p['last_name'] ?? '') ?></div>
                                            <?php if (!$inTeam && !empty($p['team_name'])): ?>
                                            <div class="mt-1">
                                                <span class="badge bg-light text-secondary border" style="font-size:0.6rem;"><?= esc($p['team_name']) ?></span>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>

                            <!-- Save bar -->
                            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <div class="text-muted small" id="pickerSummary"></div>
                                <button type="button" class="btn btn-success rounded-pill px-5" id="btnSaveTeamPlayers">
                                    <i class="fas fa-save me-2"></i><?= lang('Common.team_player_save_btn') ?>
                                </button>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

    <?php endif; ?>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    <?php if (!empty($team['id'])): ?>
    $('#playerTable').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        pageLength: 10,
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "<?= lang('Common.all') ?? 'All' ?>"]],
        language: typeof dtLanguage !== 'undefined' ? dtLanguage : {},
        ajax: {
            url: '<?= base_url('academy/get-team-players/' . ($academy['id'] ?? '') . '/' . $team['id']) ?>',
            method: "POST"
        },
        dom: '<"d-flex justify-content-between align-items-center flex-wrap px-4 py-3"lf>t<"d-flex justify-content-between align-items-center flex-wrap px-4 py-3"ip>',
        order: [[2, 'asc']],
        columnDefs: [
            { targets: 0, orderable: false },
            { targets: 1, orderable: false },
            { targets: -1, orderable: false }
        ],
        columns: [
            { data: '', className: 'text-center text-muted fw-bold ps-4', name: 'numbering' },
            { data: 'image', className: 'text-center', orderable: false, name: 'image' },
            {
                data: 'shirt_number',
                className: 'text-center fw-bold text-primary fs-5 dt-type-numeric',
                name: 'player.shirt_number',
                render: function(data, type) {
                    if (type === 'sort' || type === 'type') return parseInt(data) || 0;
                    return data;
                }
            },
            { data: 'full_name', name: 'player.first_name', className: 'fw-medium' },
            { data: 'nick_name', name: 'player.nick_name', className: 'text-muted' },
            { data: 'birth_date', name: 'player.birth_date', className: 'text-center text-muted' },
            { data: 'player_sub_position', name: 'player_sub_position', className: 'text-center', orderable: false }
        ]
    });
    <?php endif; ?>
});
</script>
<?php $this->endSection(); ?>
