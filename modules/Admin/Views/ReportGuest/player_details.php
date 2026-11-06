<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="bg-primary p-4 text-white">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h5 class="text-white-50 text-uppercase fw-bold mb-1">Player Report</h5>
                            <h2 class="text-white fw-bold mb-0"><?= $player['first_name'] . ' ' . $player['last_name'] ?></h2>
                            <div class="mt-2">
                                <span class="badge bg-white text-primary rounded-pill me-2">
                                    <i class="ti ti-shirt me-1"></i> <?= $player['team_name'] ?>
                                </span>
                                <span class="badge bg-warning text-dark rounded-pill">
                                    <i class="ti ti-ball-football me-1"></i> <?= $player['position_name'] ?> (<?= $player['position_name_en'] ?>)
                                </span>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-center bg-white rounded-pill px-3 py-2 shadow-sm">
                            <div class="text-end lh-1">
                                <div class="fw-bold text-dark small">FIFA Talent</div>
                                <div class="text-muted small" style="font-size: 0.7rem;">Development Scheme</div>
                            </div>
                            <img src="<?= base_url('public/img/thailand_national_football_team_logo.png')?>" width="32" alt="Thailand Logo">
                            <img src="<?= base_url('public/img/fa_talent_id.png')?>" width="32" alt="FA Talent ID">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Left Column: Profile & Physical -->
    <div class="col-lg-4 mb-4">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-body text-center">
                <div class="mb-4 position-relative d-inline-block">
                    <?php
                    $imgSrc = (!empty($player['image_path']) && file_exists('./' . $player['image_path']))
                        ? base_url($player['image_path'])
                        : base_url("public/img/football-player.webp");
                    ?>
                    <img src="<?= $imgSrc ?>" class="rounded-circle img-thumbnail shadow-sm" style="width: 180px; height: 180px; object-fit: cover;" alt="Player Image">
                    <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle p-2" title="Active">
                        <span class="visually-hidden">Active</span>
                    </span>
                </div>

                <h5 class="fw-bold mb-1"><?= $player['first_name'] . ' ' . $player['last_name'] ?></h5>
                <p class="text-muted mb-4"><?= $player['position_name'] ?></p>

                <div class="row g-2 text-start">
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><?= lang('Common.dominant_foot') ?></small>
                            <div class="fw-bold"><i class="ti ti-shoe me-1 text-primary"></i> <?= $player['dominant_foot'] ?: '-' ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><?= lang('Common.birth_year') ?></small>
                            <div class="fw-bold"><i class="ti ti-calendar me-1 text-primary"></i> <?= $player['birth_date'] ?: '-' ?></div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><?= lang('Common.height') ?></small>
                            <div class="fw-bold"><i class="ti ti-ruler-2 me-1 text-primary"></i> <?= $player['height'] ?: '-' ?> cm</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-light rounded-3 h-100">
                            <small class="text-muted d-block mb-1"><?= lang('Common.weight') ?></small>
                            <div class="fw-bold"><i class="ti ti-scale me-1 text-primary"></i> <?= $player['weight'] ?: '-' ?> kg</div>
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="p-3 bg-light rounded-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted d-block mb-1">BMI</small>
                                    <div class="fw-bold"><?= $player['bmi'] ?: '-' ?></div>
                                </div>
                                <div class="text-end">
                                    <i class="ti ti-calculator fs-4 text-primary opacity-50"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Stats & Evaluation -->
    <div class="col-lg-8">
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-primary-subtle h-100">
                    <div class="card-body text-center p-3">
                        <i class="ti ti-stars fs-2 text-primary mb-2"></i>
                        <h6 class="text-muted mb-1"><?= lang('Common.average_score') ?></h6>
                        <h3 class="fw-bold text-primary mb-0"><?= ($player['average_score'] > 0) ? $player['average_score'] : 0 ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-danger-subtle h-100">
                    <div class="card-body text-center p-3">
                        <i class="ti ti-ball-football fs-2 text-danger mb-2"></i>
                        <h6 class="text-muted mb-1"><?= lang('Common.goals_label') ?></h6>
                        <h3 class="fw-bold text-danger mb-0"><?= $player['goals'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-info-subtle h-100">
                    <div class="card-body text-center p-3">
                        <i class="ti ti-assist fs-2 text-info mb-2"></i>
                        <h6 class="text-muted mb-1"><?= lang('Common.assists') ?></h6>
                        <h3 class="fw-bold text-info mb-0"><?= $player['assits'] ?></h3>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card shadow-sm border-0 bg-success-subtle h-100">
                    <div class="card-body text-center p-3">
                        <i class="ti ti-cake fs-2 text-success mb-2"></i>
                        <h6 class="text-muted mb-1"><?= lang('Common.age') ?></h6>
                        <h3 class="fw-bold text-success mb-0"><?= $player['age'] ?></h3>
                    </div>
                </div>
            </div>
        </div>

        <!-- Evaluation Section -->
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0"><i class="ti ti-clipboard-check me-2 text-primary"></i><?= lang('Common.evaluation') ?></h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-4 mb-md-0">
                        <div class="mb-4">
                            <h6 class="fw-bold text-primary mb-3"><?= lang('Common.talent_strengths') ?></h6>
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-primary">
                                <?= lang('Common.eval_talent_desc') ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h6 class="fw-bold text-info mb-3"><?= lang('Common.physical_mobility') ?></h6>
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-info">
                                <?= lang('Common.eval_physical_desc') ?>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="mb-4">
                            <h6 class="fw-bold text-warning mb-3"><?= lang('Common.play_intelligence') ?></h6>
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-warning">
                                <?= lang('Common.eval_intelligence_desc') ?>
                            </div>
                        </div>
                        <div class="mb-4">
                            <h6 class="fw-bold text-danger mb-3"><?= lang('Common.mental_commitment') ?></h6>
                            <div class="p-3 bg-light rounded-3 border-start border-4 border-danger">
                                <?= lang('Common.eval_mental_desc') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- History Table -->
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3">
                <h5 class="card-title fw-bold mb-0"><i class="ti ti-history me-2 text-primary"></i><?= lang('Common.evaluation_history') ?></h5>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light text-secondary small text-uppercase">
                        <tr>
                            <th class="text-center py-3"><?= lang('Common.evaluation_date') ?></th>
                            <th class="text-center py-3">Attendance 1st leg</th>
                            <th class="text-center py-3">Minutes Played</th>
                            <th class="text-center py-3">Attendance 2nd leg</th>
                            <th class="text-center py-3">Minutes Played</th>
                            <th class="text-center py-3"><?= lang('Common.evaluator') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="text-center text-muted">N/A</td>
                            <td class="text-center text-muted">N/A</td>
                            <td class="text-center text-muted">N/A</td>
                            <td class="text-center text-muted">N/A</td>
                            <td class="text-center text-muted">N/A</td>
                            <td class="text-center text-muted">N/A</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    let stateList = '<?= json_encode($state_list) ?>';
    // const state_label = Object.values(JSON.parse(stateList));

    $(document).ready(function() {
        // Init scripts if needed
    })
</script>
<?php $this->endSection(); ?>