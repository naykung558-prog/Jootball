<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Assessment Info Header -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-clipboard-list me-2"></i><?= esc($assessment['name'] ?? lang('Common.assessments')) ?>
                    </h4>
                    <?php if (!empty($academy['name'])): ?>
                        <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <?php if (!empty($perms['perm_create_assessment'])): ?>
                        <a href="<?= base_url('academy/assessment/manage/' . esc($academy['id']) . '/' . esc($assessment['id'])) ?>"
                           class="btn btn-outline-warning btn-sm rounded-pill">
                            <i class="fas fa-edit me-1"></i><?= lang('Common.edit') ?>
                        </a>
                    <?php endif; ?>
                    <a href="<?= base_url('academy/assessments/' . esc($academy['id'])) ?>"
                       class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Assessment Details -->
        <?php if (!empty($assessment)): ?>
        <div class="card-body border-top px-4 py-3">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="far fa-calendar-alt text-primary me-2"></i>
                        <div>
                            <small class="text-muted d-block"><?= lang('Common.assessment_date') ?></small>
                            <span class="fw-medium">
                                <?= !empty($assessment['assessment_date']) ? date('d/m/Y', strtotime($assessment['assessment_date'])) : '-' ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tag text-info me-2"></i>
                        <div>
                            <small class="text-muted d-block"><?= lang('Common.assessment_type') ?></small>
                            <?php
                            $typeBadgeClass = 'bg-secondary';
                            $typeLabel = esc($assessment['assessment_type'] ?? '');
                            switch ($assessment['assessment_type'] ?? '') {
                                case 'fitness':
                                    $typeBadgeClass = 'bg-success';
                                    $typeLabel = lang('Common.assessment_type_fitness');
                                    break;
                                case 'skill':
                                    $typeBadgeClass = 'bg-primary';
                                    $typeLabel = lang('Common.assessment_type_skill');
                                    break;
                                case 'match':
                                    $typeBadgeClass = 'bg-warning text-dark';
                                    $typeLabel = lang('Common.assessment_type_match');
                                    break;
                                case 'custom':
                                    $typeBadgeClass = 'bg-info';
                                    $typeLabel = lang('Common.assessment_type_custom');
                                    break;
                            }
                            ?>
                            <span class="badge <?= $typeBadgeClass ?> rounded-pill"><?= $typeLabel ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users text-success me-2"></i>
                        <div>
                            <small class="text-muted d-block"><?= lang('Common.training_groups') ?></small>
                            <span class="fw-medium"><?= esc($assessment['group_name'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-user text-warning me-2"></i>
                        <div>
                            <small class="text-muted d-block"><?= lang('Common.coach_staff') ?></small>
                            <span class="fw-medium"><?= esc($assessment['creator_name'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (!empty($assessment['description'])): ?>
            <div class="mt-3 p-3 bg-light rounded-3">
                <small class="text-muted d-block mb-1 fw-bold"><?= lang('Common.description') ?></small>
                <span><?= esc($assessment['description']) ?></span>
            </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Scoring Matrix -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-table me-2"></i>
                <?= !empty($perms['perm_save_assessment_result']) ? lang('Common.assessment_enter_results') : lang('Common.assessment_score') ?>
            </h5>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($criteria) && !empty($players)): ?>
                <?php
                // Build lookup for existing results: results[player_id][criteria_id] = score
                $resultsMap = [];
                if (!empty($results)) {
                    foreach ($results as $r) {
                        $resultsMap[$r['player_id']][$r['criteria_id']] = $r['score'];
                    }
                }
                ?>
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0" id="scoringTable">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0 text-muted small fw-bold text-uppercase" style="min-width: 200px;">
                                    <?= lang('Common.full_name') ?>
                                </th>
                                <?php foreach ($criteria as $crit): ?>
                                <th class="py-3 border-0 text-muted small fw-bold text-uppercase text-center" style="min-width: 120px;">
                                    <div><?= esc($crit['criteria_name'] ?? $crit['name'] ?? '') ?></div>
                                    <small class="fw-normal text-muted">(<?= lang('Common.assessment_max_score') ?>: <?= (int)($crit['max_score'] ?? 0) ?>)</small>
                                </th>
                                <?php endforeach; ?>
                                <th class="py-3 border-0 text-muted small fw-bold text-uppercase text-center pe-4" style="min-width: 100px;">
                                    <?= lang('Common.attendance_total') ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $player):
                                $playerTotal = 0;
                                $playerMaxTotal = 0;
                            ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <img src="<?= !empty($player['image_path']) ? base_url($player['image_path']) : base_url('public/img/img_profile.jpg') ?>"
                                             class="rounded-circle me-2 border"
                                             style="width: 32px; height: 32px; object-fit: cover;"
                                             alt="player">
                                        <div>
                                            <span class="fw-bold small">
                                                <?php if (!empty($player['shirt_number'])): ?>
                                                    <span class="badge bg-primary me-1"><?= esc($player['shirt_number']) ?></span>
                                                <?php endif; ?>
                                                <?= esc($player['first_name'] ?? $player['name'] ?? '') ?>
                                                <?= esc($player['last_name'] ?? '') ?>
                                            </span>
                                        </div>
                                    </div>
                                </td>
                                <?php foreach ($criteria as $crit):
                                    $critId = $crit['id'];
                                    $maxScore = (int)($crit['max_score'] ?? 0);
                                    $existingScore = $resultsMap[$player['id']][$critId] ?? '';
                                    $playerMaxTotal += $maxScore;
                                    if ($existingScore !== '') {
                                        $playerTotal += (float)$existingScore;
                                    }
                                ?>
                                <td class="text-center">
                                    <?php if (!empty($perms['perm_save_assessment_result'])): ?>
                                        <input type="number"
                                               class="form-control form-control-sm text-center score-input"
                                               name="results[<?= esc($player['id']) ?>][<?= esc($critId) ?>]"
                                               data-player-id="<?= esc($player['id']) ?>"
                                               data-criteria-id="<?= esc($critId) ?>"
                                               data-max-score="<?= $maxScore ?>"
                                               value="<?= esc($existingScore) ?>"
                                               min="0" max="<?= $maxScore ?>"
                                               step="0.1"
                                               style="max-width: 80px; margin: 0 auto;">
                                    <?php else: ?>
                                        <?php if ($existingScore !== ''): ?>
                                            <span class="fw-bold"><?= esc($existingScore) ?></span>
                                            <small class="text-muted">/ <?= $maxScore ?></small>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </td>
                                <?php endforeach; ?>
                                <td class="text-center pe-4">
                                    <span class="fw-bold player-total" data-player-id="<?= esc($player['id']) ?>">
                                        <?= $playerTotal > 0 ? number_format($playerTotal, 1) : '-' ?>
                                    </span>
                                    <small class="text-muted">/ <?= $playerMaxTotal ?></small>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <!-- Summary: Average per criteria -->
                        <tfoot class="bg-light">
                            <tr>
                                <td class="ps-4 fw-bold text-muted"><?= lang('Common.average_score') ?></td>
                                <?php
                                $grandTotal = 0;
                                $grandMax = 0;
                                foreach ($criteria as $crit):
                                    $critId = $crit['id'];
                                    $maxScore = (int)($crit['max_score'] ?? 0);
                                    $critScores = [];
                                    foreach ($players as $p) {
                                        if (isset($resultsMap[$p['id']][$critId]) && $resultsMap[$p['id']][$critId] !== '') {
                                            $critScores[] = (float)$resultsMap[$p['id']][$critId];
                                        }
                                    }
                                    $critAvg = count($critScores) > 0 ? array_sum($critScores) / count($critScores) : 0;
                                    $grandTotal += $critAvg;
                                    $grandMax += $maxScore;
                                ?>
                                <td class="text-center">
                                    <span class="fw-bold criteria-avg" data-criteria-id="<?= esc($critId) ?>">
                                        <?= count($critScores) > 0 ? number_format($critAvg, 1) : '-' ?>
                                    </span>
                                </td>
                                <?php endforeach; ?>
                                <td class="text-center pe-4">
                                    <span class="fw-bold" id="grandAvg">
                                        <?= $grandTotal > 0 ? number_format($grandTotal, 1) : '-' ?>
                                    </span>
                                    <small class="text-muted">/ <?= $grandMax ?></small>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Save Button (save_assessment_result only) -->
                <?php if (!empty($perms['perm_save_assessment_result'])): ?>
                <div class="card-footer bg-white border-top text-center py-4">
                    <button type="button" class="btn btn-primary rounded-pill px-5 py-2" id="saveResultsBtn">
                        <i class="fas fa-save me-2"></i><?= lang('Common.save') ?>
                    </button>
                </div>
                <?php endif; ?>

            <?php elseif (empty($criteria)): ?>
                <!-- No Criteria -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-list-ol fa-4x text-muted opacity-25"></i>
                    </div>
                    <h5 class="fw-bold text-muted mb-2"><?= lang('Common.assessment_no_criteria') ?></h5>
                    <?php if (!empty($perms['perm_create_assessment'])): ?>
                        <a href="<?= base_url('academy/assessment/manage/' . esc($academy['id']) . '/' . esc($assessment['id'])) ?>"
                           class="btn btn-primary rounded-pill px-4 mt-2">
                            <i class="fas fa-edit me-2"></i><?= lang('Common.assessment_edit') ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php else: ?>
                <!-- No Players -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-users fa-4x text-muted opacity-25"></i>
                    </div>
                    <h5 class="fw-bold text-muted mb-2"><?= lang('Common.no_data') ?></h5>
                    <p class="text-muted small"><?= lang('Common.training_group_players') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Summary Cards: Average per Player -->
    <?php if (!empty($criteria) && !empty($players) && !empty($results)): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-chart-bar me-2"></i><?= lang('Common.average_score') ?>
            </h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <?php
                $maxTotalScore = 0;
                foreach ($criteria as $c) {
                    $maxTotalScore += (int)($c['max_score'] ?? 0);
                }

                foreach ($players as $player):
                    $pTotal = 0;
                    $pCount = 0;
                    foreach ($criteria as $c) {
                        if (isset($resultsMap[$player['id']][$c['id']]) && $resultsMap[$player['id']][$c['id']] !== '') {
                            $pTotal += (float)$resultsMap[$player['id']][$c['id']];
                            $pCount++;
                        }
                    }
                    $pPercent = $maxTotalScore > 0 ? ($pTotal / $maxTotalScore) * 100 : 0;
                    $barColor = $pPercent >= 80 ? 'bg-success' : ($pPercent >= 50 ? 'bg-warning' : 'bg-danger');
                ?>
                <div class="col-md-6 col-lg-4">
                    <div class="d-flex align-items-center mb-1">
                        <img src="<?= !empty($player['image_path']) ? base_url($player['image_path']) : base_url('public/img/img_profile.jpg') ?>"
                             class="rounded-circle me-2 border"
                             style="width: 28px; height: 28px; object-fit: cover;"
                             alt="player">
                        <span class="fw-medium small flex-grow-1">
                            <?= esc($player['first_name'] ?? $player['name'] ?? '') ?>
                            <?= esc($player['last_name'] ?? '') ?>
                        </span>
                        <span class="fw-bold small"><?= number_format($pTotal, 1) ?>/<?= $maxTotalScore ?></span>
                    </div>
                    <div class="progress" style="height: 8px;">
                        <div class="progress-bar <?= $barColor ?>" role="progressbar"
                             style="width: <?= number_format($pPercent, 1) ?>%"
                             aria-valuenow="<?= number_format($pPercent, 1) ?>"
                             aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?php if (!empty($perms['perm_save_assessment_result']) && !empty($criteria) && !empty($players)): ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Validate score inputs on change
        var scoreInputs = document.querySelectorAll('.score-input');
        scoreInputs.forEach(function(input) {
            input.addEventListener('change', function() {
                var max = parseFloat(this.dataset.maxScore) || 0;
                var val = parseFloat(this.value);
                if (isNaN(val) || val < 0) {
                    this.value = '';
                } else if (val > max) {
                    this.value = max;
                }
                updatePlayerTotal(this.dataset.playerId);
            });
        });

        function updatePlayerTotal(playerId) {
            var inputs = document.querySelectorAll('.score-input[data-player-id="' + playerId + '"]');
            var total = 0;
            var hasValue = false;
            inputs.forEach(function(inp) {
                var v = parseFloat(inp.value);
                if (!isNaN(v)) {
                    total += v;
                    hasValue = true;
                }
            });
            var totalEl = document.querySelector('.player-total[data-player-id="' + playerId + '"]');
            if (totalEl) {
                totalEl.textContent = hasValue ? total.toFixed(1) : '-';
            }
        }

        // Save results via AJAX
        var saveBtn = document.getElementById('saveResultsBtn');
        if (saveBtn) {
            saveBtn.addEventListener('click', function() {
                var resultsData = [];
                scoreInputs.forEach(function(input) {
                    var val = input.value.trim();
                    if (val !== '') {
                        resultsData.push({
                            criteria_id: input.dataset.criteriaId,
                            player_id: input.dataset.playerId,
                            score: parseFloat(val)
                        });
                    }
                });

                saveBtn.disabled = true;
                saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span><?= lang('Common.loading') ?>';

                fetch('<?= base_url('academy/assessment/save-results') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({
                        academy_id: '<?= esc($academy['id']) ?>',
                        assessment_id: '<?= esc($assessment['id']) ?>',
                        results: resultsData
                    })
                })
                .then(function(response) { return response.json(); })
                .then(function(data) {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '<?= lang('Common.success') ?>',
                            text: '<?= lang('Common.assessment_results_saved') ?>',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '<?= lang('Common.error') ?>',
                            text: data.message || '<?= lang('Common.error') ?>'
                        });
                    }
                })
                .catch(function() {
                    Swal.fire({
                        icon: 'error',
                        title: '<?= lang('Common.error') ?>',
                        text: '<?= lang('Common.error') ?>'
                    });
                })
                .finally(function() {
                    saveBtn.disabled = false;
                    saveBtn.innerHTML = '<i class="fas fa-save me-2"></i><?= lang('Common.save') ?>';
                });
            });
        }
    });
</script>
<?php endif; ?>
<?php $this->endSection(); ?>
