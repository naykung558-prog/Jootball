<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<!-- Academy Info Card -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-start flex-wrap gap-3 mb-4">
            <div class="d-flex align-items-center">
                <div class="me-4">
                    <?php if (!empty($academy['logo'])): ?>
                        <img src="<?= base_url($academy['logo']) ?>" alt="<?= esc($academy['name']) ?>"
                            class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid white; box-shadow: var(--box-shadow);">
                    <?php else: ?>
                        <img src="<?= base_url('public/img/exam-logo.webp') ?>" alt="Academy Logo"
                            class="rounded-circle shadow-sm" style="width: 100px; height: 100px; object-fit: cover; border: 4px solid white; box-shadow: var(--box-shadow);">
                    <?php endif; ?>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= esc($academy['name']) ?></h4>
                    <?php if (!empty($academy['name_en'])): ?>
                        <p class="text-muted mb-1"><?= esc($academy['name_en']) ?></p>
                    <?php endif; ?>
                    <?php if (isset($academy['is_active']) && $academy['is_active'] == 1): ?>
                        <span class="badge bg-success px-3 py-2"><i class="fas fa-check-circle me-1"></i> <?= lang('Common.activate') ?></span>
                    <?php else: ?>
                        <span class="badge bg-secondary px-3 py-2"><i class="fas fa-times-circle me-1"></i> <?= lang('Common.deactivate') ?></span>
                    <?php endif; ?>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/academy/manage/' . $academy['id']) ?>" class="btn btn-primary rounded-pill px-4 py-2 fw-bold" style="background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                    <i class="fas fa-edit me-2"></i> <?= lang('Common.edit_btn') ?>
                </a>
                <a href="<?= base_url('admin/academy') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                    <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
                </a>
            </div>
        </div>

        <div class="row g-4">
            <!-- Contact Information -->
            <div class="col-md-6">
                <h6 class="fw-bold text-muted mb-3"><i class="fas fa-address-card me-2"></i><?= lang('Common.academy_contact_info') ?></h6>
                <table class="table table-borderless mb-0">
                    <tbody>
                        <?php if (!empty($academy['contact_name'])): ?>
                        <tr>
                            <td class="text-muted" style="width: 140px;"><i class="fas fa-user me-2"></i><?= lang('Common.contact_name_label') ?></td>
                            <td class="fw-bold"><?= esc($academy['contact_name']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($academy['contact_phone'])): ?>
                        <tr>
                            <td class="text-muted"><i class="fas fa-phone me-2"></i><?= lang('Common.mobile_contact') ?></td>
                            <td class="fw-bold"><?= esc($academy['contact_phone']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($academy['contact_email'])): ?>
                        <tr>
                            <td class="text-muted"><i class="fas fa-envelope me-2"></i><?= lang('Common.email') ?></td>
                            <td class="fw-bold"><?= esc($academy['contact_email']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($academy['contact_line'])): ?>
                        <tr>
                            <td class="text-muted"><i class="fab fa-line me-2"></i><?= lang('Common.line_id') ?></td>
                            <td class="fw-bold"><?= esc($academy['contact_line']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Location & Zone -->
            <div class="col-md-6">
                <h6 class="fw-bold text-muted mb-3"><i class="fas fa-map-marker-alt me-2"></i><?= lang('Common.field_location') ?></h6>
                <table class="table table-borderless mb-0">
                    <tbody>
                        <?php if (!empty($academy['address'])): ?>
                        <tr>
                            <td class="text-muted"><i class="fas fa-home me-2"></i><?= lang('Common.academy_address') ?></td>
                            <td class="fw-bold"><?= esc($academy['address']) ?></td>
                        </tr>
                        <?php endif; ?>
                        <?php if (!empty($academy['latitude']) && !empty($academy['longitude'])): ?>
                        <tr>
                            <td class="text-muted"><i class="fas fa-crosshairs me-2"></i><?= lang('Common.coordinates') ?></td>
                            <td class="fw-bold"><?= esc($academy['latitude']) ?>, <?= esc($academy['longitude']) ?></td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Description -->
            <?php if (!empty($academy['description'])): ?>
            <div class="col-md-12">
                <h6 class="fw-bold text-muted mb-3"><i class="fas fa-info-circle me-2"></i><?= lang('Common.academy_description_label') ?></h6>
                <div class="bg-light p-3" style="border-radius: var(--border-radius);">
                    <?= nl2br(esc($academy['description'])) ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Teams Table -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="color: var(--secondary-color);">
                <i class="fas fa-users me-2"></i><?= lang('Common.teams') ?>
                <span class="badge bg-primary ms-2" style="background: var(--gold-gradient) !important;"><?= isset($teams) ? count($teams) : 0 ?></span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-tables" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="5%">#</th>
                        <th class="py-3 text-center" width="10%"><?= lang('Common.academy_logo') ?></th>
                        <th class="py-3 text-start" width="30%"><?= lang('Common.academy_team_name') ?></th>
                        <th class="py-3 text-center" width="15%"><?= lang('Common.generation') ?></th>
                        <th class="py-3 text-center" width="15%"><?= lang('Common.academy_player_count') ?></th>
                        <th class="py-3 pe-4 text-center" width="15%"><?= lang('Common.academy_actions') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teams)): ?>
                        <?php foreach ($teams as $index => $team): ?>
                        <tr>
                            <td class="text-center ps-4"><?= $index + 1 ?></td>
                            <td class="text-center">
                                <?php if (!empty($team['logo'])): ?>
                                    <img src="<?= base_url($team['logo']) ?>" alt="<?= esc($team['name']) ?>"
                                        class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <img src="<?= base_url('public/img/exam-logo.webp') ?>" alt="Team Logo"
                                        class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php endif; ?>
                            </td>
                            <td class="text-start fw-bold"><?= esc($team['name']) ?></td>
                            <td class="text-center"><?= !empty($team['generation_name']) ? esc($team['generation_name']) : '-' ?></td>
                            <td class="text-center">
                                <span class="badge bg-info px-3 py-2"><?= isset($team['player_count']) ? $team['player_count'] : 0 ?></span>
                            </td>
                            <td class="text-center pe-4">
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <a href="<?= base_url('admin/team/manage/' . $team['id']) ?>" class="btn btn-sm btn-outline-warning rounded-pill px-3">
                                        <i class="fas fa-pencil-alt me-1"></i> <?= lang('Common.edit_btn') ?>
                                    </a>
                                    <a href="<?= base_url('detail/team/' . $team['id']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                        <i class="fas fa-eye me-1"></i> <?= lang('Common.view_details') ?>
                                    </a>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                <?= lang('Common.academy_no_teams_found') ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Members Table -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="fw-bold mb-0" style="color: var(--secondary-color);">
                <i class="fas fa-user-friends me-2"></i><?= lang('Common.members_label') ?>
                <span class="badge bg-primary ms-2" style="background: var(--gold-gradient) !important;"><?= isset($members) ? count($members) : 0 ?></span>
            </h5>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle data-tables" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="5%">#</th>
                        <th class="py-3 text-start" width="30%"><?= lang('Common.name') ?></th>
                        <th class="py-3 text-center" width="20%"><?= lang('Common.role') ?></th>
                        <th class="py-3 text-center" width="20%"><?= lang('Common.status') ?></th>
                        <th class="py-3 pe-4 text-center" width="25%"><?= lang('Common.joined_date') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($members)): ?>
                        <?php foreach ($members as $index => $member): ?>
                        <tr>
                            <td class="text-center ps-4"><?= $index + 1 ?></td>
                            <td class="text-start fw-bold">
                                <?php
                                    if (!empty($member['user_name'])) {
                                        echo esc($member['user_name']);
                                    } elseif (!empty($member['coach_first_name'])) {
                                        echo esc($member['coach_first_name'] . ' ' . ($member['coach_last_name'] ?? ''));
                                    } else {
                                        echo '-';
                                    }
                                ?>
                            </td>
                            <td class="text-center">
                                <?php
                                    $roleBadge = 'bg-secondary';
                                    $roleLabelMap = [
                                        'owner' => lang('Common.role_owner'),
                                        'admin' => lang('Common.role_admin'),
                                        'coach' => lang('Common.role_coach'),
                                        'viewer' => lang('Common.role_viewer')
                                    ];
                                    if (isset($member['role'])) {
                                        if ($member['role'] === 'owner') $roleBadge = 'bg-warning text-dark';
                                        elseif ($member['role'] === 'admin') $roleBadge = 'bg-primary';
                                        elseif ($member['role'] === 'coach') $roleBadge = 'bg-info';
                                        elseif ($member['role'] === 'viewer') $roleBadge = 'bg-secondary';
                                    }
                                ?>
                                <span class="badge <?= $roleBadge ?> px-3 py-2">
                                    <?= isset($member['role']) ? ($roleLabelMap[$member['role']] ?? ucfirst(esc($member['role']))) : lang('Common.role_viewer') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php
                                    $statusBadge = 'bg-secondary';
                                    $statusLabelMap = [
                                        'approved' => lang('Common.status_approved'),
                                        'pending' => lang('Common.status_pending_approval'),
                                        'rejected' => lang('Common.status_rejected')
                                    ];
                                    $statusText = lang('Common.status_pending_approval');
                                    if (isset($member['status'])) {
                                        if ($member['status'] === 'approved') $statusBadge = 'bg-success';
                                        elseif ($member['status'] === 'pending') $statusBadge = 'bg-warning text-dark';
                                        elseif ($member['status'] === 'rejected') $statusBadge = 'bg-danger';
                                        $statusText = $statusLabelMap[$member['status']] ?? $member['status'];
                                    }
                                ?>
                                <span class="badge <?= $statusBadge ?> px-3 py-2"><?= $statusText ?></span>
                            </td>
                            <td class="text-center pe-4">
                                <?= !empty($member['created_at']) ? date('d/m/Y', strtotime($member['created_at'])) : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-2x mb-2 d-block"></i>
                                <?= lang('Common.academy_no_members_found') ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>
