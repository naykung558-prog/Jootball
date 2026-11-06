<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="row mb-3">
        <div class="col-12 d-flex justify-content-between align-items-center">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= base_url('academy/players/' . $academy_id) ?>"><?= lang('Common.players') ?></a></li>
                    <li class="breadcrumb-item active"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></li>
                </ol>
            </nav>
            <div class="d-flex gap-2">
                <?php if ($canEditAll || $canEditPhysical || $canEditTeam): ?>
                <a href="<?= base_url('academy/player-edit/' . $academy_id . '/' . $player['id']) ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-edit me-1"></i> <?= lang('Common.pv_edit') ?>
                </a>
                <?php endif; ?>
                <?php if (!empty($perms['perm_enroll_player'])): ?>
                <button class="btn btn-outline-danger rounded-pill px-4" onclick="unenrollPlayer(<?= $player['id'] ?>, '<?= esc($player['first_name'] . ' ' . $player['last_name']) ?>')">
                    <i class="fas fa-user-minus me-1"></i> <?= lang('Common.pv_remove_from_academy') ?>
                </button>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Left: Player Profile -->
        <div class="col-md-4">
            <div class="card shadow-sm border-0 rounded-4 text-center p-4">
                <img src="<?= !empty($player['image_path']) ? base_url($player['image_path']) : base_url('public/img/img_profile.jpg') ?>"
                     class="rounded-circle mx-auto mb-3 border border-3 shadow" style="width:120px;height:120px;object-fit:cover;">
                <h4 class="fw-bold text-primary mb-0"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></h4>
                <?php if (!empty($player['nick_name'])): ?>
                <p class="text-muted mb-0">"<?= esc($player['nick_name']) ?>"</p>
                <?php endif; ?>

                <?php if (!empty($enrollment)): ?>
                <div class="mt-3">
                    <span class="badge bg-primary fs-6 px-3 py-2 rounded-pill">
                        <i class="fas fa-tshirt me-1"></i>
                        #<?= esc($enrollment['shirt_number'] ?: '-') ?>
                    </span>
                </div>
                <?php endif; ?>

                <hr>
                <!-- Physical Info -->
                <div class="row text-start g-2">
                    <?php if (!empty($player['birth_date'])): ?>
                    <div class="col-6">
                        <small class="text-muted d-block"><?= lang('Common.birth_date') ?></small>
                        <span class="fw-semibold"><?= date('d/m/', strtotime($player['birth_date'])) . (date('Y', strtotime($player['birth_date'])) + 543) ?></span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted d-block"><?= lang('Common.pv_age') ?></small>
                        <span class="fw-semibold"><?= floor((time() - strtotime($player['birth_date'])) / 31536000) ?> <?= lang('Common.pv_years') ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($player['height'])): ?>
                    <div class="col-6">
                        <small class="text-muted d-block"><?= lang('Common.pv_height') ?></small>
                        <span class="fw-semibold"><?= esc($player['height']) ?> <?= lang('Common.pv_cm') ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($player['weight'])): ?>
                    <div class="col-6">
                        <small class="text-muted d-block"><?= lang('Common.pv_weight') ?></small>
                        <span class="fw-semibold"><?= esc($player['weight']) ?> <?= lang('Common.pv_kg') ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($player['dominant_foot'])): ?>
                    <div class="col-6">
                        <small class="text-muted d-block"><?= lang('Common.pe_dominant_foot') ?></small>
                        <span class="fw-semibold">
                            <?= ['left' => lang('Common.foot_left'), 'right' => lang('Common.foot_right'), 'both' => lang('Common.pe_foot_both')][$player['dominant_foot']] ?? $player['dominant_foot'] ?>
                        </span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Links -->
                <div class="d-grid gap-2 mt-3">
                    <a href="<?= base_url('academy/report-player/' . $academy_id . '/' . $player['id']) ?>" class="btn btn-outline-success btn-sm rounded-pill">
                        <i class="fas fa-chart-line me-1"></i> <?= lang('Common.pv_player_report') ?>
                    </a>
                    <a href="<?= base_url('academy/player-history/' . $academy_id . '/' . $player['id']) ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="fas fa-history me-1"></i> <?= lang('Common.pv_academy_history') ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Right: Details -->
        <div class="col-md-8">
            <!-- Enrollment Info (this academy) -->
            <?php if (!empty($enrollment)): ?>
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-shield-alt me-2 text-primary"></i><?= lang('Common.pv_academy_info_here') ?></h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <small class="text-muted d-block"><?= lang('Common.team') ?></small>
                            <span class="fw-semibold"><?= esc($enrollment['team_name'] ?? lang('Common.pv_no_team')) ?></span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block"><?= lang('Common.pe_shirt_number') ?></small>
                            <span class="fw-bold text-primary fs-5"><?= esc($enrollment['shirt_number'] ?: '-') ?></span>
                        </div>
                        <div class="col-md-4">
                            <small class="text-muted d-block"><?= lang('Common.pv_joined_date') ?></small>
                            <span class="fw-semibold"><?= date('d/m/', strtotime($enrollment['enrolled_at'])) . (date('Y', strtotime($enrollment['enrolled_at'])) + 543) ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- All Academies -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fas fa-school me-2 text-success"></i><?= lang('Common.pv_academies') ?></h6>
                    <span class="badge bg-success rounded-pill"><?= count($academies) ?> Academy</span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($academies)): ?>
                    <div class="text-center py-4 text-muted">
                        <i class="fas fa-info-circle me-2"></i><?= lang('Common.pv_no_academy') ?>
                    </div>
                    <?php else: ?>
                    <div class="list-group list-group-flush rounded-4">
                        <?php foreach ($academies as $acad): ?>
                        <?php $isCurrent = (int)$acad['academy_id'] === (int)$academy_id; ?>
                        <div class="list-group-item d-flex align-items-center gap-3 py-3 px-4 <?= $isCurrent ? 'bg-primary' : '' ?>">
                            <?php if (!empty($acad['logo'])): ?>
                            <img src="<?= base_url($acad['logo']) ?>" class="rounded" style="width:40px;height:40px;object-fit:cover;">
                            <?php else: ?>
                            <div class="<?= $isCurrent ? 'bg-white text-primary' : 'bg-primary text-white' ?> rounded d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0;">
                                <i class="fas fa-school"></i>
                            </div>
                            <?php endif; ?>
                            <div class="flex-grow-1">
                                <div class="fw-semibold <?= $isCurrent ? 'text-white' : 'text-dark' ?>">
                                    <?= esc($acad['academy_name']) ?>
                                    <?php if ($isCurrent): ?>
                                    <span class="badge bg-white text-primary ms-1 rounded-pill" style="font-size:0.65rem;"><?= lang('Common.pv_this_academy') ?></span>
                                    <?php endif; ?>
                                </div>
                                <small class="<?= $isCurrent ? 'text-white' : 'text-muted' ?>" style="<?= $isCurrent ? 'opacity:0.85' : '' ?>">
                                    <?= lang('Common.scope_team_prefix') ?> <?= esc($acad['team_name'] ?? lang('Common.pv_no_team')) ?> | <?= lang('Common.pv_number_prefix') ?>: <?= esc($acad['shirt_number'] ?: '-') ?>
                                </small>
                            </div>
                            <small class="<?= $isCurrent ? 'text-white' : 'text-muted' ?>" style="<?= $isCurrent ? 'opacity:0.85' : '' ?>">
                                <?= lang('Common.pv_joined') ?> <?= date('d/m/', strtotime($acad['enrolled_at'])) . (date('Y', strtotime($acad['enrolled_at'])) + 543) ?>
                            </small>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Personal Info (visible section) -->
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-id-card me-2 text-info"></i><?= lang('Common.personal_information') ?></h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <?php if (!empty($player['id_card'])): ?>
                        <div class="col-md-6">
                            <small class="text-muted d-block"><?= lang('Common.id_card_label') ?></small>
                            <span class="fw-semibold"><?= esc($player['id_card']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($player['nation'])): ?>
                        <div class="col-md-6">
                            <small class="text-muted d-block"><?= lang('Common.nationality') ?></small>
                            <span class="fw-semibold"><?= esc($player['nation']) ?></span>
                        </div>
                        <?php endif; ?>
                        <?php if (!empty($player['province_name'])): ?>
                        <div class="col-md-6">
                            <small class="text-muted d-block"><?= lang('Common.pv_province') ?></small>
                            <span class="fw-semibold"><?= esc($player['province_name']) ?></span>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
const academyId = <?= (int)$academy_id ?>;

function unenrollPlayer(playerId, playerName) {
    Swal.fire({
        title: '<?= lang('Common.pv_remove_confirm_title') ?>',
        html: `<?= lang('Common.pv_remove_confirm_prefix') ?> <strong>${playerName}</strong> <?= lang('Common.pv_remove_confirm_suffix') ?>`,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<?= lang('Common.pv_remove_btn') ?>',
        cancelButtonText: '<?= lang('Common.cancel') ?>'
    }).then(result => {
        if (result.isConfirmed) {
            fetch('<?= base_url('academy/player-unenroll/' . $academy_id) ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ player_id: playerId })
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.status === 'success') {
                    Swal.fire({ icon: 'success', title: '<?= lang('Common.success_title') ?>', text: resp.message, timer: 1500, showConfirmButton: false })
                        .then(() => location.href = '<?= base_url('academy/players/' . $academy_id) ?>');
                } else {
                    Swal.fire({ icon: 'error', title: '<?= lang('Common.error_occurred') ?>', text: resp.message });
                }
            });
        }
    });
}
</script>
<?php $this->endSection(); ?>
