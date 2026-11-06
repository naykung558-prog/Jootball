<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Header -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-list-ul me-2"></i><?= lang('Common.idp_indicator_assessment_title') ?>
                    </h4>
                    <p class="text-muted small mb-0">
                        <?= esc($academy['name'] ?? '') ?>
                        <?php if (!empty($group['name'])): ?>
                            &mdash; <strong><?= esc($group['name']) ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <?php if (!empty($group['id'])): ?>
                    <a href="<?= base_url('academy/idp/group/assess/' . esc($academy['id']) . '/' . esc($group['id'])) ?>"
                       class="btn btn-outline-primary btn-sm rounded-pill">
                        <i class="fas fa-th me-1"></i><?= lang('Common.idp_assess_dimension_level') ?>
                    </a>
                    <?php endif; ?>
                    <a href="<?= base_url('academy/idp/' . esc($academy['id'])) ?>"
                       class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($plans)): ?>
        <!-- No Plans -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-clipboard fa-3x text-muted mb-3 opacity-25"></i>
                <p class="text-muted"><?= lang('Common.idp_group_no_plans') ?></p>
                <?php if (!empty($perms['perm_manage_idp'])): ?>
                    <a href="<?= base_url('academy/idp/group/create/' . esc($academy['id'])) ?>"
                       class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-plus me-1"></i><?= lang('Common.idp_group_create') ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    <?php else: ?>
        <!-- Assessment Controls -->
        <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div class="card-body p-3">
                <div class="row g-3 align-items-end">
                    <div class="col-md-3">
                        <label class="form-label fw-bold"><?= lang('Common.idp_assessment_type') ?></label>
                        <select id="assessmentType" class="form-select">
                            <option value="adhoc"><?= lang('Common.idp_assessment_adhoc') ?></option>
                            <option value="baseline"><?= lang('Common.idp_assessment_baseline') ?></option>
                            <option value="midterm"><?= lang('Common.idp_assessment_midterm') ?></option>
                            <option value="final"><?= lang('Common.idp_assessment_final') ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold"><?= lang('Common.idp_assessment_date') ?></label>
                        <input type="date" id="assessmentDate" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-3">
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" id="btnExpandAll">
                                <i class="fas fa-expand-alt me-1"></i><?= lang('Common.idp_expand_all') ?>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm rounded-pill" id="btnCollapseAll">
                                <i class="fas fa-compress-alt me-1"></i><?= lang('Common.idp_collapse_all') ?>
                            </button>
                        </div>
                    </div>
                    <div class="col-md-3 text-end">
                        <?php if (!empty($perms['perm_manage_idp'])): ?>
                            <button type="button" class="btn btn-success rounded-pill px-4" id="btnSaveAll">
                                <i class="fas fa-save me-1"></i><?= lang('Common.idp_bulk_save') ?>
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Overall Summary -->
        <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div class="card-body p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <span class="fw-bold text-muted"><?= lang('Common.idp_overall_avg') ?>:</span>
                    <span class="fw-bold fs-5" id="globalOverallAvg">-</span>
                </div>
            </div>
        </div>

        <!-- Dimension Accordions -->
        <div class="accordion" id="dimensionAccordion">
            <?php
            $dimIndex = 0;
            foreach ($indicatorsByDimension as $dimId => $dimData):
                $dim = $dimData['dimension'];
                $indicators = $dimData['indicators'];
                if (empty($indicators)) continue;
                $color = $dim['color'] ?? '#0d6efd';
                $icon  = $dim['icon'] ?? 'fa-star';
                $dimIndex++;
            ?>
            <div class="card shadow-sm border-0 rounded-4 mb-3">
                <div class="card-header bg-white border-0 p-0">
                    <button class="btn w-100 text-start p-3 d-flex align-items-center justify-content-between collapsed"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#dimCollapse<?= esc($dimId) ?>"
                            aria-expanded="<?= ($dimIndex <= 1) ? 'true' : 'false' ?>">
                        <div class="d-flex align-items-center">
                            <span class="d-inline-flex align-items-center justify-content-center rounded-circle me-2"
                                  style="width: 36px; height: 36px; background: <?= esc($color) ?>15;">
                                <i class="fas <?= esc($icon) ?>" style="color: <?= esc($color) ?>; font-size: 1rem;"></i>
                            </span>
                            <div>
                                <span class="fw-bold" style="color: <?= esc($color) ?>;"><?= esc($dim['name']) ?></span>
                                <small class="text-muted ms-2">(<?= count($indicators) ?> <?= lang('Common.idp_indicator_scores') ?>)</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge rounded-pill dim-overall-avg" data-dim-id="<?= esc($dimId) ?>"
                                  style="background: <?= esc($color) ?>; font-size: 0.8rem;">-</span>
                            <i class="fas fa-chevron-down text-muted accordion-chevron" style="transition: transform 0.2s;"></i>
                        </div>
                    </button>
                </div>
                <div id="dimCollapse<?= esc($dimId) ?>"
                     class="collapse <?= ($dimIndex <= 1) ? 'show' : '' ?>"
                     data-bs-parent="#dimensionAccordion">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-bordered table-hover mb-0 indicator-table" data-dim-id="<?= esc($dimId) ?>">
                                <thead class="table-light">
                                    <tr>
                                        <th class="text-center" style="min-width: 180px; position: sticky; left: 0; background: #f8f9fa; z-index: 2;">
                                            <?= lang('Common.players') ?>
                                        </th>
                                        <?php foreach ($indicators as $ind): ?>
                                        <th class="text-center" style="min-width: 80px; font-size: 0.7rem;">
                                            <span title="<?= esc($ind['name']) ?>"><?= esc(mb_substr($ind['name'], 0, 10)) ?></span>
                                            <?php if (!empty($ind['unit'])): ?>
                                            <br><small class="text-muted">(<?= esc($ind['unit']) ?>)</small>
                                            <?php endif; ?>
                                        </th>
                                        <?php endforeach; ?>
                                        <th class="text-center" style="min-width: 60px; font-size: 0.75rem;">
                                            <?= lang('Common.idp_dimension_avg') ?>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($plans as $plan):
                                        $playerName = urlencode(($plan['player_name'] ?? ''));
                                        $imgPath = !empty($plan['player_image'])
                                            ? base_url($plan['player_image'])
                                            : 'https://ui-avatars.com/api/?name=' . $playerName . '&size=80&background=0d6efd&color=fff&bold=true&format=svg';
                                        $planScores = isset($existingScores[$plan['id']]) ? $existingScores[$plan['id']] : [];
                                    ?>
                                    <tr data-plan-id="<?= esc($plan['id']) ?>" data-player-id="<?= esc($plan['player_id']) ?>">
                                        <td style="position: sticky; left: 0; background: #fff; z-index: 1;">
                                            <div class="d-flex align-items-center">
                                                <img src="<?= $imgPath ?>" class="rounded-circle me-2"
                                                     style="width: 28px; height: 28px; object-fit: cover;"
                                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= $playerName ?>&size=80&background=6c757d&color=fff&format=svg'">
                                                <div>
                                                    <span class="fw-bold" style="font-size: 0.8rem;">
                                                        <?php if (!empty($plan['shirt_number'])): ?>
                                                            <span class="badge bg-dark me-1" style="font-size: 0.55rem;">#<?= esc($plan['shirt_number']) ?></span>
                                                        <?php endif; ?>
                                                        <?= esc($plan['player_name']) ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </td>
                                        <?php foreach ($indicators as $ind):
                                            $existingScore = '';
                                            if (isset($planScores[$ind['id']])) {
                                                $existingScore = $planScores[$ind['id']]['score'];
                                            }
                                        ?>
                                        <td class="text-center p-1">
                                            <select class="form-select form-select-sm score-input text-center"
                                                    data-dim-id="<?= esc($dimId) ?>"
                                                    data-ind-id="<?= esc($ind['id']) ?>"
                                                    style="width: 60px; margin: 0 auto; font-size: 0.75rem; padding: 2px 4px;">
                                                <option value="">-</option>
                                                <option value="1" <?= ($existingScore != '' && (float)$existingScore == 1) ? 'selected' : '' ?>>1</option>
                                                <option value="2" <?= ($existingScore != '' && (float)$existingScore == 2) ? 'selected' : '' ?>>2</option>
                                                <option value="3" <?= ($existingScore != '' && (float)$existingScore == 3) ? 'selected' : '' ?>>3</option>
                                                <option value="4" <?= ($existingScore != '' && (float)$existingScore == 4) ? 'selected' : '' ?>>4</option>
                                                <option value="5" <?= ($existingScore != '' && (float)$existingScore == 5) ? 'selected' : '' ?>>5</option>
                                            </select>
                                        </td>
                                        <?php endforeach; ?>
                                        <td class="text-center fw-bold row-dim-avg" style="font-size: 0.8rem;">-</td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td class="fw-bold text-center" style="position: sticky; left: 0; background: #f8f9fa; font-size: 0.75rem;">
                                            <?= lang('Common.idp_indicator_avg') ?>
                                        </td>
                                        <?php foreach ($indicators as $ind): ?>
                                        <td class="text-center fw-bold col-ind-avg" data-ind-id="<?= esc($ind['id']) ?>" style="font-size: 0.8rem;">-</td>
                                        <?php endforeach; ?>
                                        <td class="text-center fw-bold dim-total-avg" data-dim-id="<?= esc($dimId) ?>" style="font-size: 0.85rem;">-</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>

<style>
.accordion-chevron { transition: transform 0.3s ease; }
.btn:not(.collapsed) .accordion-chevron { transform: rotate(180deg); }
.score-input:focus { box-shadow: 0 0 0 2px rgba(13,110,253,.25); }
.indicator-table th { vertical-align: middle; }
.indicator-table td { vertical-align: middle; }
</style>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
/**
 * Recalculate all averages across all dimension tables
 */
function recalcAllAverages() {
    var dimAvgs = {};
    var dimAvgCount = 0;
    var dimAvgTotal = 0;

    // Process each dimension table
    document.querySelectorAll('.indicator-table').forEach(function(table) {
        var dimId = table.getAttribute('data-dim-id');
        var rows = table.querySelectorAll('tbody tr');
        var indTotals = {};
        var indCounts = {};
        var dimRowAvgTotal = 0;
        var dimRowAvgCount = 0;

        // Per row: calculate row average for this dimension
        rows.forEach(function(row) {
            var inputs = row.querySelectorAll('.score-input');
            var rowTotal = 0;
            var rowCount = 0;

            inputs.forEach(function(inp) {
                var val = parseFloat(inp.value);
                var indId = inp.getAttribute('data-ind-id');
                if (val > 0) {
                    rowTotal += val;
                    rowCount++;
                    // Column (indicator) totals
                    if (!indTotals[indId]) { indTotals[indId] = 0; indCounts[indId] = 0; }
                    indTotals[indId] += val;
                    indCounts[indId]++;
                }
            });

            var avgCell = row.querySelector('.row-dim-avg');
            if (rowCount > 0) {
                var avg = (rowTotal / rowCount).toFixed(1);
                avgCell.textContent = avg;
                colorCell(avgCell, parseFloat(avg));
                dimRowAvgTotal += parseFloat(avg);
                dimRowAvgCount++;
            } else {
                avgCell.textContent = '-';
                avgCell.style.color = '';
            }
        });

        // Per column (indicator): calculate column average
        table.querySelectorAll('.col-ind-avg').forEach(function(cell) {
            var indId = cell.getAttribute('data-ind-id');
            if (indTotals[indId] && indCounts[indId] > 0) {
                var colAvg = (indTotals[indId] / indCounts[indId]).toFixed(1);
                cell.textContent = colAvg;
                colorCell(cell, parseFloat(colAvg));
            } else {
                cell.textContent = '-';
                cell.style.color = '';
            }
        });

        // Dimension total average
        var dimTotalAvgCell = table.querySelector('.dim-total-avg');
        if (dimRowAvgCount > 0) {
            var dimAvg = (dimRowAvgTotal / dimRowAvgCount).toFixed(1);
            dimTotalAvgCell.textContent = dimAvg;
            colorCell(dimTotalAvgCell, parseFloat(dimAvg));
            dimAvgs[dimId] = parseFloat(dimAvg);
        } else {
            dimTotalAvgCell.textContent = '-';
            dimTotalAvgCell.style.color = '';
        }

        // Update badge on accordion header
        var badge = document.querySelector('.dim-overall-avg[data-dim-id="' + dimId + '"]');
        if (badge) {
            if (dimRowAvgCount > 0) {
                badge.textContent = (dimRowAvgTotal / dimRowAvgCount).toFixed(1);
            } else {
                badge.textContent = '-';
            }
        }
    });

    // Global overall average
    var overallTotal = 0;
    var overallCount = 0;
    for (var key in dimAvgs) {
        if (dimAvgs.hasOwnProperty(key)) {
            overallTotal += dimAvgs[key];
            overallCount++;
        }
    }
    var globalEl = document.getElementById('globalOverallAvg');
    if (overallCount > 0) {
        var gAvg = (overallTotal / overallCount).toFixed(1);
        globalEl.textContent = gAvg;
        colorCell(globalEl, parseFloat(gAvg));
    } else {
        globalEl.textContent = '-';
        globalEl.style.color = '';
    }
}

function colorCell(el, val) {
    if (val >= 4) el.style.color = '#198754';
    else if (val >= 3) el.style.color = '#0d6efd';
    else if (val >= 2) el.style.color = '#fd7e14';
    else el.style.color = '#dc3545';
}

// Listen for score changes
document.querySelectorAll('.score-input').forEach(function(inp) {
    inp.addEventListener('change', recalcAllAverages);
});

// Initial calculation
recalcAllAverages();

// Expand All
document.getElementById('btnExpandAll').addEventListener('click', function() {
    document.querySelectorAll('#dimensionAccordion .collapse').forEach(function(el) {
        if (!el.classList.contains('show')) {
            var bsCollapse = new bootstrap.Collapse(el, { toggle: false });
            bsCollapse.show();
        }
    });
});

// Collapse All
document.getElementById('btnCollapseAll').addEventListener('click', function() {
    document.querySelectorAll('#dimensionAccordion .collapse.show').forEach(function(el) {
        var bsCollapse = new bootstrap.Collapse(el, { toggle: false });
        bsCollapse.hide();
    });
});

// Save All
var btnSaveAll = document.getElementById('btnSaveAll');
if (btnSaveAll) {
    btnSaveAll.addEventListener('click', function() {
        var results = [];
        var hasScores = false;

        // Collect all scores grouped by plan (player)
        var planMap = {};
        document.querySelectorAll('.indicator-table tbody tr').forEach(function(row) {
            var planId = parseInt(row.getAttribute('data-plan-id'));
            var playerId = parseInt(row.getAttribute('data-player-id'));

            if (!planMap[planId]) {
                planMap[planId] = { plan_id: planId, player_id: playerId, scores: [] };
            }

            row.querySelectorAll('.score-input').forEach(function(inp) {
                var val = inp.value;
                if (val !== '' && val !== null) {
                    planMap[planId].scores.push({
                        dimension_id: parseInt(inp.getAttribute('data-dim-id')),
                        indicator_id: parseInt(inp.getAttribute('data-ind-id')),
                        score: parseFloat(val)
                    });
                    hasScores = true;
                }
            });
        });

        // Convert map to array
        for (var pid in planMap) {
            if (planMap.hasOwnProperty(pid) && planMap[pid].scores.length > 0) {
                results.push(planMap[pid]);
            }
        }

        if (!hasScores) {
            Swal.fire({ icon: 'warning', title: '<?= lang('Common.fill_required') ?>' });
            return;
        }

        Swal.fire({
            title: "<?= lang('Common.confirm_save_data') ?>",
            text: results.length + " <?= lang('Common.players') ?>",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#16478d",
            cancelButtonColor: "#d33",
            confirmButtonText: "<?= lang('Common.confirm') ?>",
            cancelButtonText: "<?= lang('Common.cancel') ?>"
        }).then(function(result) {
            if (result.isConfirmed) {
                var payload = {
                    assessment_type: document.getElementById('assessmentType').value,
                    assessment_date: document.getElementById('assessmentDate').value,
                    results: results
                };

                fetch('<?= base_url('academy/idp/group/assess-indicators/save') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(payload)
                })
                .then(function(r) { return r.json(); })
                .then(function(data) {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: '<?= lang('Common.success') ?>',
                            text: data.message || '<?= lang('Common.idp_scores_saved') ?>',
                            showConfirmButton: false,
                            timer: 1500
                        }).then(function() {
                            location.reload();
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: '<?= lang('Common.error') ?>', text: data.message });
                    }
                })
                .catch(function(err) {
                    Swal.fire({ icon: 'error', title: '<?= lang('Common.error') ?>', text: '<?= lang('Common.connection_error') ?>' });
                });
            }
        });
    });
}
</script>
<?php $this->endSection(); ?>
