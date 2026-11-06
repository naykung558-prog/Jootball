<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-chart-line me-2 text-primary"></i><?= lang('Common.idp_title') ?>
            </h4>
            <p class="text-muted mb-0"><?= esc($academy['name'] ?? '') ?></p>
        </div>
        <?php if (!empty($perms['perm_manage_idp_template']) || !empty($perms['perm_manage_idp'])): ?>
        <div class="mt-2 mt-md-0">
            <?php if (!empty($perms['perm_manage_idp_template'])): ?>
            <a href="<?= base_url('academy/idp/library/' . $academy['id']) ?>"
               class="btn btn-outline-primary rounded-pill px-4 me-2">
                <i class="fas fa-book me-1"></i><?= lang('Common.idp_library_title') ?>
            </a>
            <?php endif; ?>
            <?php if (!empty($perms['perm_manage_idp'])): ?>
            <a href="<?= base_url('academy/idp/group/create/' . $academy['id']) ?>"
               class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-layer-group me-1"></i><?= lang('Common.idp_create_group_plan') ?>
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(13,110,253,0.1);">
                            <i class="fas fa-file-alt fa-lg text-primary"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold mb-0"><?= $overview['total_plans'] ?? 0 ?></h3>
                    <small class="text-muted"><?= lang('Common.idp_total_plans') ?></small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(40,167,69,0.1);">
                            <i class="fas fa-play-circle fa-lg text-success"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold mb-0 text-success"><?= $overview['active_plans'] ?? 0 ?></h3>
                    <small class="text-muted"><?= lang('Common.idp_active_plans') ?></small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(111,66,193,0.1);">
                            <i class="fas fa-check-circle fa-lg text-purple"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold mb-0" style="color: #6f42c1;"><?= $overview['completed_plans'] ?? 0 ?></h3>
                    <small class="text-muted"><?= lang('Common.idp_completed_plans') ?></small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle" style="width: 50px; height: 50px; background: rgba(253,126,20,0.1);">
                            <i class="fas fa-users fa-lg text-warning"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold mb-0 text-warning"><?= $overview['players_with_idp'] ?? 0 ?></h3>
                    <small class="text-muted"><?= lang('Common.idp_players_with_idp') ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- IDP Group Plans V2 Section -->
    <?php if (!empty($idpGroupPlans)): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold"><i class="fas fa-layer-group me-2 text-primary"></i><?= lang('Common.idp_group_plans') ?></h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <?php foreach ($idpGroupPlans as $gp):
                    $gpId = (int)$gp['id'];
                    $statusColor = 'success';
                    $statusIcon = 'play-circle';
                    if ($gp['status'] === 'draft') { $statusColor = 'secondary'; $statusIcon = 'edit'; }
                    elseif ($gp['status'] === 'completed') { $statusColor = 'info'; $statusIcon = 'check-circle'; }
                    elseif ($gp['status'] === 'cancelled') { $statusColor = 'danger'; $statusIcon = 'times-circle'; }
                ?>
                <div class="col-12 col-md-6" id="groupPlanCard_<?= $gpId ?>">
                    <div class="card border rounded-3 h-100 hover-shadow">
                        <div class="card-body p-3">
                            <!-- Group Plan Name + Action Buttons -->
                            <div class="d-flex align-items-start justify-content-between mb-2">
                                <div>
                                    <h6 class="fw-bold mb-0">
                                        <i class="fas fa-clipboard-list me-1 text-primary"></i>
                                        <?= esc($gp['name']) ?>
                                    </h6>
                                    <span class="badge bg-<?= $statusColor ?> rounded-pill mt-1" style="font-size: 0.7rem;">
                                        <i class="fas fa-<?= $statusIcon ?> me-1"></i><?= lang('Common.idp_status_' . $gp['status']) ?>
                                    </span>
                                </div>
                                <div class="d-flex align-items-center gap-1">
                                    <?php if (!empty($perms['perm_manage_idp'])): ?>
                                    <a href="<?= base_url('academy/idp/group/edit/' . $academy['id'] . '/' . $gpId) ?>"
                                       class="btn btn-sm btn-outline-primary" title="<?= lang('Common.edit') ?>">
                                        <i class="fas fa-edit" style="font-size: 0.8rem;"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger"
                                            onclick="deleteGroupPlan(<?= $gpId ?>, '<?= esc($gp['name'], 'js') ?>', <?= (int)($gp['player_count'] ?? 0) ?>)"
                                            title="<?= lang('Common.delete') ?>">
                                        <i class="fas fa-trash-alt" style="font-size: 0.8rem;"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Template + Player Count + Coach -->
                            <div class="mb-2 py-2 px-2 bg-light rounded">
                                <div class="d-flex align-items-center mb-1">
                                    <span class="badge bg-white text-dark border me-2" style="font-size: 0.75rem;">
                                        <i class="fas fa-file-alt me-1 text-info"></i><?= esc($gp['template_title'] ?? '-') ?>
                                    </span>
                                    <small class="text-muted"><i class="fas fa-users me-1"></i><?= (int)($gp['player_count'] ?? 0) ?> <?= lang('Common.players') ?></small>
                                </div>

                                <?php if (!empty($gp['coach_name'])): ?>
                                <div class="mb-1">
                                    <small class="text-muted">
                                        <i class="fas fa-chalkboard-teacher me-1"></i><?= esc($gp['coach_name']) ?>
                                    </small>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($gp['start_date']) || !empty($gp['end_date'])): ?>
                                <div class="mb-1">
                                    <small class="text-muted">
                                        <i class="fas fa-calendar-alt me-1"></i>
                                        <?= !empty($gp['start_date']) ? date('M Y', strtotime($gp['start_date'])) : '?' ?>
                                        -
                                        <?= !empty($gp['end_date']) ? date('M Y', strtotime($gp['end_date'])) : '?' ?>
                                    </small>
                                </div>
                                <?php endif; ?>

                                <?php if (!empty($gp['season'])): ?>
                                <div class="mb-1">
                                    <small class="text-muted"><i class="fas fa-trophy me-1"></i><?= esc($gp['season']) ?></small>
                                </div>
                                <?php endif; ?>

                                <!-- Action buttons -->
                                <div class="d-flex gap-1 mt-2">
                                    <a href="<?= base_url('academy/idp/group/assess/' . $academy['id'] . '/' . $gpId) ?>?template_id=<?= (int)$gp['template_id'] ?>"
                                       class="btn btn-sm btn-outline-success flex-fill">
                                        <i class="fas fa-clipboard-check me-1"></i><?= lang('Common.idp_group_assess') ?>
                                    </a>
                                    <a href="<?= base_url('academy/idp/group/report/' . $academy['id'] . '/' . $gpId) ?>?template_id=<?= (int)$gp['template_id'] ?>"
                                       class="btn btn-sm btn-outline-info flex-fill">
                                        <i class="fas fa-chart-bar me-1"></i><?= lang('Common.idp_group_report') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Player List with IDP Status -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold"><i class="fas fa-users me-2"></i><?= lang('Common.idp_individual_plans') ?></h5>
        </div>
        <div class="card-body p-4">
            <?php if (empty($players)): ?>
                <div class="text-center text-muted py-5">
                    <i class="fas fa-user-slash fa-3x mb-3 opacity-25"></i>
                    <p><?= lang('Common.no_data') ?></p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($players as $player): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border rounded-3 h-100 hover-shadow">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <!-- Player Image -->
                                    <div class="me-3">
                                        <?php
                                        $playerName = urlencode(($player['first_name'] ?? '') . '+' . ($player['last_name'] ?? ''));
                                        $imgPath = !empty($player['image_path'])
                                            ? base_url($player['image_path'])
                                            : 'https://ui-avatars.com/api/?name=' . $playerName . '&size=110&background=0d6efd&color=fff&bold=true&format=svg';
                                        ?>
                                        <img src="<?= $imgPath ?>" alt="" class="rounded-circle" style="width: 55px; height: 55px; object-fit: cover; border: 2px solid #e0e0e0;" onerror="this.src='https://ui-avatars.com/api/?name=<?= $playerName ?>&size=110&background=6c757d&color=fff&format=svg'">
                                    </div>
                                    <!-- Player Info -->
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1 fw-bold">
                                            <?php if (!empty($player['shirt_number'])): ?>
                                                <span class="badge bg-dark me-1">#<?= esc($player['shirt_number']) ?></span>
                                            <?php endif; ?>
                                            <?= esc(($player['first_name'] ?? '') . ' ' . ($player['last_name'] ?? '')) ?>
                                        </h6>
                                        <?php if (!empty($player['nick_name'])): ?>
                                            <small class="text-muted">"<?= esc($player['nick_name']) ?>"</small>
                                        <?php endif; ?>
                                        <?php if (!empty($player['position_name'])): ?>
                                            <br><small class="text-muted"><i class="fas fa-running me-1"></i><?= esc($player['position_name']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <!-- IDP Status Badge -->
                                    <div class="text-end">
                                        <?php
                                        $planCount = (int)($player['plan_count'] ?? 0);
                                        $latestStatus = $player['latest_plan_status'] ?? '';
                                        $activePlanId = $player['active_plan_id'] ?? null;
                                        if ($activePlanId): ?>
                                            <span class="badge bg-success rounded-pill mb-1">
                                                <i class="fas fa-check-circle me-1"></i><?= lang('Common.idp_has_active_plan') ?>
                                            </span>
                                            <br>
                                            <a href="<?= base_url('academy/idp/plan/view/' . $academy['id'] . '/' . $activePlanId) ?>"
                                               class="btn btn-sm btn-outline-primary mt-1">
                                                <i class="fas fa-eye me-1"></i><?= lang('Common.idp_view_plan') ?>
                                            </a>
                                        <?php elseif ($planCount > 0): ?>
                                            <span class="badge bg-secondary rounded-pill mb-1"><?= $planCount ?> <?= lang('Common.idp_total_plans') ?></span>
                                            <br>
                                            <a href="<?= base_url('academy/idp/report/' . $academy['id'] . '/' . $player['id']) ?>"
                                               class="btn btn-sm btn-outline-info mt-1">
                                                <i class="fas fa-chart-bar me-1"></i><?= lang('Common.idp_view_report') ?>
                                            </a>
                                        <?php else: ?>
                                            <span class="badge bg-light text-muted rounded-pill mb-1">
                                                <i class="fas fa-minus-circle me-1"></i><?= lang('Common.idp_no_active_plan') ?>
                                            </span>
                                        <?php endif; ?>

                                        <?php if (!empty($perms['perm_manage_idp'])): ?>
                                            <br>
                                            <a href="<?= base_url('academy/idp/plan/manage/' . $academy['id'] . '/' . $player['id']) ?>"
                                               class="btn btn-sm btn-primary mt-1">
                                                <i class="fas fa-plus me-1"></i><?= lang('Common.idp_plan_create') ?>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.hover-shadow { transition: box-shadow 0.2s ease; }
.hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important; }
</style>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
var academyId = <?= (int)$academy['id'] ?>;

// V2: Delete group plan (idp_group_plans)
function deleteGroupPlan(groupPlanId, planName, playerCount) {
    Swal.fire({
        title: "<?= lang('Common.confirm_delete') ?>",
        html: '<strong>' + planName + '</strong><br><small>' + playerCount + ' <?= lang('Common.players') ?></small>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#6c757d',
        confirmButtonText: "<?= lang('Common.delete') ?>",
        cancelButtonText: "<?= lang('Common.cancel') ?>"
    }).then(function(result) {
        if (!result.isConfirmed) return;

        fetch('<?= base_url('academy/idp/group/plan/delete') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({
                academy_id: academyId,
                group_plan_id: groupPlanId
            })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: data.message,
                    timer: 1500,
                    showConfirmButton: false
                }).then(function() {
                    window.location.reload();
                });
            } else {
                Swal.fire({ icon: 'error', title: data.message || '<?= lang('Common.delete_failed') ?>' });
            }
        })
        .catch(function() {
            Swal.fire({ icon: 'error', title: '<?= lang('Common.delete_failed') ?>' });
        });
    });
}

</script>
<?php $this->endSection(); ?>
