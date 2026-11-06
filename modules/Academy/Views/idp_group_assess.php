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
                        <i class="fas fa-clipboard-check me-2"></i><?= lang('Common.idp_batch_assess_title') ?>
                    </h4>
                    <p class="text-muted small mb-0">
                        <?= esc($academy['name'] ?? '') ?>
                        <?php if (!empty($group['name'])): ?>
                            &mdash; <strong><?= esc($group['name']) ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
                <div class="d-flex gap-2">
                    <a href="<?= base_url('academy/idp/group/report/' . $academy['id'] . '/' . ($group['id'] ?? '')) ?>"
                       class="btn btn-outline-info btn-sm rounded-pill">
                        <i class="fas fa-chart-bar me-1"></i><?= lang('Common.idp_group_report') ?>
                    </a>
                    <a href="<?= base_url('academy/idp/' . esc($academy['id'])) ?>"
                       class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($plans)): ?>
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
                        <label class="form-label fw-bold"><?= lang('Common.idp_template') ?></label>
                        <span class="d-block">
                            <span class="badge bg-info text-white" style="font-size: 0.85rem;">
                                <i class="fas fa-file-alt me-1"></i><?= esc(!empty($templateInfo) ? $templateInfo['title'] : '-') ?>
                            </span>
                        </span>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold"><?= lang('Common.idp_assessment_type') ?></label>
                        <select id="assessmentType" class="form-select">
                            <option value="adhoc"><?= lang('Common.idp_assessment_adhoc') ?></option>
                            <option value="baseline"><?= lang('Common.idp_assessment_baseline') ?></option>
                            <option value="midterm" selected><?= lang('Common.idp_assessment_midterm') ?></option>
                            <option value="final"><?= lang('Common.idp_assessment_final') ?></option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold"><?= lang('Common.idp_assessment_date') ?></label>
                        <input type="date" id="assessmentDate" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-md-2">
                        <?php if (!empty($coaches)): ?>
                            <label class="form-label fw-bold"><?= lang('Common.coach') ?></label>
                            <span class="d-block text-muted">
                                <?= esc($coaches[0]['first_name'] . ' ' . $coaches[0]['last_name']) ?>
                            </span>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-3"></div>
                </div>
            </div>
        </div>

        <!-- Assessment History Log (per-dimension) -->
        <?php if (!empty($assessmentHistory)): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-3">
            <div class="card-header bg-white border-0 py-2 px-4" data-bs-toggle="collapse" data-bs-target="#historyCollapse" style="cursor: pointer;">
                <div class="d-flex align-items-center justify-content-between">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-history me-1 text-warning"></i><?= lang('Common.idp_assessment_history') ?>
                        <span class="badge bg-warning text-dark ms-1"><?= count($assessmentHistory) ?></span>
                    </h6>
                    <i class="fas fa-chevron-down text-muted"></i>
                </div>
            </div>
            <div class="collapse" id="historyCollapse">
                <div class="card-body p-3 pt-0">
                    <div class="table-responsive">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th><?= lang('Common.idp_assessment_date') ?></th>
                                    <th><?= lang('Common.idp_dimensions') ?></th>
                                    <th><?= lang('Common.idp_assessment_type') ?></th>
                                    <th><?= lang('Common.players') ?></th>
                                    <th class="text-center"><?= lang('Common.action') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $typeLabels = [
                                    'baseline' => lang('Common.idp_assessment_baseline'),
                                    'midterm'  => lang('Common.idp_assessment_midterm'),
                                    'final'    => lang('Common.idp_assessment_final'),
                                    'adhoc'    => lang('Common.idp_assessment_adhoc'),
                                ];
                                foreach ($assessmentHistory as $hist):
                                    $typeLabel = isset($typeLabels[$hist['assessment_type']]) ? $typeLabels[$hist['assessment_type']] : $hist['assessment_type'];
                                    $dimColor = $hist['dimension_color'] ?? '#6c757d';
                                    $dimIcon = $hist['dimension_icon'] ?? 'fa-star';
                                ?>
                                <tr class="history-row" data-date="<?= esc($hist['assess_date']) ?>" data-type="<?= esc($hist['assessment_type']) ?>" data-dim-id="<?= esc($hist['dimension_id']) ?>">
                                    <td><?= esc($hist['assess_date']) ?></td>
                                    <td>
                                        <span class="badge" style="background: <?= esc($dimColor) ?>; font-size: 0.75rem;">
                                            <i class="fas <?= esc($dimIcon) ?> me-1"></i><?= esc($hist['dimension_name']) ?>
                                        </span>
                                    </td>
                                    <td><span class="badge bg-secondary"><?= esc($typeLabel) ?></span></td>
                                    <td><?= esc($hist['player_count']) ?></td>
                                    <td class="text-center">
                                        <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2"
                                                onclick="loadHistorySession('<?= esc($hist['assess_date']) ?>', '<?= esc($hist['assessment_type']) ?>', <?= esc($hist['dimension_id']) ?>)"
                                                style="font-size: 0.75rem;">
                                            <i class="fas fa-pencil-alt me-1"></i><?= lang('Common.edit') ?>
                                        </button>
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

        <!-- Dimension Selection -->
        <div class="mb-3">
            <div class="d-flex flex-wrap gap-2" id="dimensionCards">
                <?php foreach ($dimensions as $dim):
                    $color = $dim['color'] ?? '#0d6efd';
                    $icon = $dim['icon'] ?? 'fa-star';
                    $dimId = $dim['id'];
                    $indCount = isset($indicatorsByDimension[$dimId]['indicators']) ? count($indicatorsByDimension[$dimId]['indicators']) : 0;
                ?>
                <button type="button" class="btn btn-outline-secondary dimension-card d-flex align-items-center gap-2 px-3 py-2"
                        data-dim-id="<?= esc($dimId) ?>"
                        onclick="selectDimension(<?= esc($dimId) ?>)"
                        style="border-left: 4px solid <?= esc($color) ?>; transition: all 0.15s;">
                    <i class="fas <?= esc($icon) ?>" style="color: <?= esc($color) ?>; font-size: 1rem;"></i>
                    <span class="fw-bold" style="font-size: 0.8rem;"><?= esc($dim['name']) ?></span>
                    <span class="badge bg-light text-dark" style="font-size: 0.65rem;"><?= $indCount ?></span>
                    <i class="fas fa-check-circle text-success dimension-scored-icon" data-dim-id="<?= esc($dimId) ?>" style="font-size: 0.7rem; display: none;"></i>
                </button>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Indicator Scoring Tables (one per dimension, initially hidden) -->
        <?php foreach ($dimensions as $dim):
            $dimId = $dim['id'];
            $color = $dim['color'] ?? '#0d6efd';
            $icon = $dim['icon'] ?? 'fa-star';
            $indicators = isset($indicatorsByDimension[$dimId]['indicators']) ? $indicatorsByDimension[$dimId]['indicators'] : [];
        ?>
        <div class="scoring-table-wrapper" id="scoringTable_<?= esc($dimId) ?>" style="display: none;">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <i class="fas <?= esc($icon) ?> fa-lg me-2" style="color: <?= esc($color) ?>;"></i>
                            <h5 class="mb-0 fw-bold" style="color: <?= esc($color) ?>;"><?= esc($dim['name']) ?></h5>
                            <span class="badge bg-light text-dark ms-2"><?= count($indicators) ?> <?= lang('Common.idp_indicators_count') ?></span>
                        </div>
                        <?php if (!empty($perms['perm_manage_idp'])): ?>
                        <div class="text-end">
                            <button type="button" class="btn btn-success rounded-pill px-4 btnSaveDim">
                                <i class="fas fa-save me-1"></i><?= lang('Common.idp_save_dimension') ?>
                            </button>
                            <div class="mt-1">
                                <span class="modeIndicator" style="display:none; font-size:0.75rem; color:#6c757d;"></span>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($indicators)): ?>
                        <div class="text-center py-4 text-muted">
                            <i class="fas fa-info-circle me-1"></i> <?= lang('Common.no_data') ?>
                        </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-bordered table-hover mb-0 indicator-matrix" data-dim-id="<?= esc($dimId) ?>">
                            <thead class="table-light">
                                <tr>
                                    <th class="text-center" style="min-width: 200px; position: sticky; left: 0; background: #f8f9fa; z-index: 2;">
                                        <?= lang('Common.players') ?>
                                    </th>
                                    <?php foreach ($indicators as $ind):
                                        $mType = $ind['measurement_type'] ?? 'rating';
                                    ?>
                                    <th class="text-center" style="min-width: 110px;">
                                        <small class="fw-bold d-block" style="font-size: 0.75rem;"><?= esc($ind['name']) ?></small>
                                        <?php if ($mType === 'rating'): ?>
                                        <small class="text-muted" style="font-size: 0.6rem;">(1-<?= (int)($ind['max_value'] ?? 5) ?>)</small>
                                        <?php elseif (!empty($ind['unit'])): ?>
                                        <small class="text-muted" style="font-size: 0.6rem;">(<?= esc($ind['unit']) ?>)</small>
                                        <?php else: ?>
                                        <small class="text-muted" style="font-size: 0.6rem;">(<?= esc($ind['min_value'] ?? 0) ?>-<?= esc($ind['max_value'] ?? 10) ?>)</small>
                                        <?php endif; ?>
                                    </th>
                                    <?php endforeach; ?>
                                    <th class="text-center" style="min-width: 70px; background: #f0f0f0;">AVG</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($plans as $plan):
                                    $playerName = urlencode(($plan['player_name'] ?? ''));
                                    $imgPath = !empty($plan['player_image'])
                                        ? base_url($plan['player_image'])
                                        : 'https://ui-avatars.com/api/?name=' . $playerName . '&size=80&background=0d6efd&color=fff&bold=true&format=svg';
                                ?>
                                <tr data-plan-id="<?= esc($plan['id']) ?>" data-player-id="<?= esc($plan['player_id']) ?>">
                                    <td style="position: sticky; left: 0; background: #fff; z-index: 1;">
                                        <div class="d-flex align-items-center">
                                            <img src="<?= $imgPath ?>" class="rounded-circle me-2"
                                                 style="width: 32px; height: 32px; object-fit: cover;"
                                                 onerror="this.src='https://ui-avatars.com/api/?name=<?= $playerName ?>&size=80&background=6c757d&color=fff&format=svg'">
                                            <div>
                                                <span class="fw-bold" style="font-size: 0.85rem;">
                                                    <?php if (!empty($plan['shirt_number'])): ?>
                                                        <span class="badge bg-dark me-1" style="font-size: 0.6rem;">#<?= esc($plan['shirt_number']) ?></span>
                                                    <?php endif; ?>
                                                    <?= esc($plan['player_name']) ?>
                                                </span>
                                            </div>
                                        </div>
                                    </td>
                                    <?php foreach ($indicators as $ind):
                                        $mType = $ind['measurement_type'] ?? 'rating';
                                    ?>
                                    <td class="text-center p-1">
                                        <?php if ($mType === 'rating'): ?>
                                        <select class="form-select form-select-sm score-input text-center"
                                                data-dimension-id="<?= esc($dimId) ?>"
                                                data-indicator-id="<?= esc($ind['id']) ?>"
                                                data-type="rating"
                                                data-max="<?= esc($ind['max_value'] ?? 5) ?>"
                                                style="width: 100%; font-size: 0.8rem;">
                                            <option value="">-</option>
                                            <?php for ($s = 1; $s <= (int)($ind['max_value'] ?? 5); $s++): ?>
                                            <option value="<?= $s ?>"><?= $s ?></option>
                                            <?php endfor; ?>
                                        </select>
                                        <?php else: ?>
                                        <input type="number" class="form-control form-control-sm score-input text-center"
                                               data-dimension-id="<?= esc($dimId) ?>"
                                               data-indicator-id="<?= esc($ind['id']) ?>"
                                               data-type="<?= esc($mType) ?>"
                                               data-max="<?= esc($ind['max_value'] ?? 100) ?>"
                                               step="0.01"
                                               min="<?= esc($ind['min_value'] ?? 0) ?>"
                                               max="<?= esc($ind['max_value'] ?? 100) ?>"
                                               value=""
                                               placeholder="<?= esc(($ind['min_value'] ?? 0) . '-' . ($ind['max_value'] ?? 100)) ?>"
                                               style="width: 100%; font-size: 0.8rem;">
                                        <?php endif; ?>
                                    </td>
                                    <?php endforeach; ?>
                                    <td class="text-center fw-bold avg-cell" style="font-size: 0.85rem; background: #f9f9f9;">-</td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot class="table-light">
                                <tr>
                                    <td class="fw-bold text-center" style="position: sticky; left: 0; background: #f8f9fa;">AVG</td>
                                    <?php foreach ($indicators as $ind): ?>
                                    <td class="text-center fw-bold ind-avg" data-indicator-id="<?= esc($ind['id']) ?>">-</td>
                                    <?php endforeach; ?>
                                    <td class="text-center fw-bold overall-avg" style="background: #f0f0f0;">-</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

    <?php endif; ?>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<style>
.dimension-card.active { background: #e8f0fe !important; border-color: #0d6efd !important; color: #0d6efd; box-shadow: 0 2px 8px rgba(13,110,253,0.15); }
</style>
<script>
var currentDimId = null;
var currentMode = null; // 'new' or 'edit'

var HISTORY_URL = '<?= base_url("academy/idp/group/assess/history") ?>';
var SAVE_URL = '<?= base_url("academy/idp/group/assess-indicators/save") ?>';
var GROUP_ID = '<?= esc($group_id) ?>';
var ACADEMY_ID = '<?= esc($academy["id"]) ?>';
var TEMPLATE_ID = '<?= esc($template_id ?? "") ?>';

var TYPE_LABELS = {
    'baseline': '<?= lang("Common.idp_assessment_baseline") ?>',
    'midterm': '<?= lang("Common.idp_assessment_midterm") ?>',
    'final': '<?= lang("Common.idp_assessment_final") ?>',
    'adhoc': '<?= lang("Common.idp_assessment_adhoc") ?>'
};

var DIMENSION_NAMES = {
    <?php foreach ($dimensions as $dim): ?>
    '<?= esc($dim["id"]) ?>': '<?= esc($dim["name"]) ?>',
    <?php endforeach; ?>
};

// ========== Dimension Selection ==========
function selectDimension(dimId) {
    document.querySelectorAll('.dimension-card').forEach(function(card) {
        card.classList.remove('active');
    });
    var activeCard = document.querySelector('.dimension-card[data-dim-id="' + dimId + '"]');
    if (activeCard) activeCard.classList.add('active');

    document.querySelectorAll('.scoring-table-wrapper').forEach(function(w) {
        w.style.display = 'none';
    });
    var table = document.getElementById('scoringTable_' + dimId);
    if (table) table.style.display = 'block';

    currentDimId = dimId;
    recalcAverages(dimId);
    // Auto-check for existing data when switching dimension
    checkAndLoadSession();
}

function getInputValue(inp) {
    var val = inp.value;
    if (val === '' || val === null || val === undefined) return null;
    return parseFloat(val);
}

function recalcAverages(dimId) {
    var table = document.querySelector('#scoringTable_' + dimId + ' .indicator-matrix');
    if (!table) return;

    var rows = table.querySelectorAll('tbody tr');
    var indTotals = {};
    var indCounts = {};

    rows.forEach(function(row) {
        var inputs = row.querySelectorAll('.score-input');
        var filledCount = 0;

        inputs.forEach(function(inp) {
            var val = getInputValue(inp);
            var indId = inp.getAttribute('data-indicator-id');
            if (val !== null) {
                filledCount++;
                if (!indTotals[indId]) { indTotals[indId] = 0; indCounts[indId] = 0; }
                indTotals[indId] += val;
                indCounts[indId]++;
            }
        });

        var avgCell = row.querySelector('.avg-cell');
        if (filledCount > 0) {
            avgCell.textContent = filledCount + '/' + inputs.length;
            avgCell.style.color = (filledCount === inputs.length) ? '#198754' : '#fd7e14';
        } else {
            avgCell.textContent = '-';
            avgCell.style.color = '';
        }
    });

    table.querySelectorAll('.ind-avg').forEach(function(cell) {
        var indId = cell.getAttribute('data-indicator-id');
        if (indTotals[indId] && indCounts[indId] > 0) {
            cell.textContent = (indTotals[indId] / indCounts[indId]).toFixed(1);
        } else {
            cell.textContent = '-';
        }
    });

    var overallAvg = table.querySelector('.overall-avg');
    if (overallAvg) {
        var totalFilled = 0;
        for (var k in indCounts) { totalFilled += indCounts[k]; }
        var totalInputs = table.querySelectorAll('tbody .score-input').length;
        overallAvg.textContent = (totalFilled > 0) ? totalFilled + '/' + totalInputs : '-';
    }
}

// Listen for score changes
document.querySelectorAll('.score-input').forEach(function(inp) {
    inp.addEventListener('change', function() { recalcAverages(this.getAttribute('data-dimension-id')); });
    inp.addEventListener('input', function() { recalcAverages(this.getAttribute('data-dimension-id')); });
});

// ========== Session Load (AJAX) — per dimension ==========
function checkAndLoadSession() {
    var date = document.getElementById('assessmentDate').value;
    var type = document.getElementById('assessmentType').value;
    if (!date || !type || !currentDimId) {
        setMode(null);
        return;
    }

    var url = HISTORY_URL
        + '?group_id=' + GROUP_ID
        + '&academy_id=' + ACADEMY_ID
        + '&template_id=' + TEMPLATE_ID
        + '&date=' + encodeURIComponent(date)
        + '&type=' + encodeURIComponent(type)
        + '&dimension_id=' + currentDimId;

    fetch(url)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status !== 'success') { clearCurrentDimScores(); setMode('new'); return; }

            var scores = data.scores;
            var hasAny = false;
            for (var pid in scores) { for (var iid in scores[pid]) { hasAny = true; break; } if (hasAny) break; }

            if (hasAny) {
                populateScores(scores);
                setMode('edit');
            } else {
                clearCurrentDimScores();
                setMode('new');
            }
            highlightHistoryRow(date, type, currentDimId);
            recalcAverages(currentDimId);
            updateDimensionScoredIcons();
        })
        .catch(function() {
            clearCurrentDimScores();
            setMode('new');
        });
}

function setMode(mode) {
    currentMode = mode;
    // หา modeIndicator ใน scoringTable ที่กำลัง active อยู่
    var el = null;
    if (currentDimId) {
        var wrapper = document.getElementById('scoringTable_' + currentDimId);
        if (wrapper) el = wrapper.querySelector('.modeIndicator');
    }
    if (!el) return;
    if (mode === 'edit') {
        var rawDate = document.getElementById('assessmentDate').value; // YYYY-MM-DD
        var parts = rawDate.split('-');
        var displayDate = (parts.length === 3) ? parts[2] + '/' + parts[1] + '/' + parts[0] : rawDate;
        var dimName = DIMENSION_NAMES[currentDimId] || '';
        el.textContent = '<?= lang("Common.edit_mode") ?>: ' + dimName + ' (' + displayDate + ')';
        el.style.display = 'inline';
    } else if (mode === 'new') {
        el.textContent = '<?= lang("Common.idp_new_session") ?>';
        el.style.display = 'inline';
    } else {
        el.style.display = 'none';
    }
}

function populateScores(scores) {
    if (!currentDimId) return;
    var table = document.querySelector('#scoringTable_' + currentDimId + ' .indicator-matrix');
    if (!table) return;
    table.querySelectorAll('tbody tr').forEach(function(row) {
        var planId = row.getAttribute('data-plan-id');
        row.querySelectorAll('.score-input').forEach(function(inp) {
            var indId = inp.getAttribute('data-indicator-id');
            var val = '';
            if (scores[planId] && scores[planId][indId]) {
                val = scores[planId][indId].score;
            }
            if (val !== '' && val !== null && val !== undefined) {
                // Select (rating) inputs: option values are integers ("1","2","3")
                // but DB stores float ("3.00") — convert to integer string to match
                if (inp.tagName === 'SELECT') {
                    inp.value = String(Math.round(parseFloat(val)));
                } else {
                    inp.value = val;
                }
            } else {
                inp.value = '';
            }
            inp.disabled = false;
        });
    });
}

function clearCurrentDimScores() {
    if (!currentDimId) return;
    var table = document.querySelector('#scoringTable_' + currentDimId + ' .indicator-matrix');
    if (!table) return;
    table.querySelectorAll('.score-input').forEach(function(inp) {
        inp.value = '';
        inp.disabled = false;
    });
    recalcAverages(currentDimId);
}

function clearAllScores() {
    document.querySelectorAll('.score-input').forEach(function(inp) {
        inp.value = '';
        inp.disabled = false;
    });
    document.querySelectorAll('.dimension-card').forEach(function(card) {
        var dimId = card.getAttribute('data-dim-id');
        if (dimId) recalcAverages(dimId);
    });
    updateDimensionScoredIcons();
}

function updateDimensionScoredIcons() {
    document.querySelectorAll('.dimension-scored-icon').forEach(function(icon) {
        var dimId = icon.getAttribute('data-dim-id');
        var table = document.querySelector('#scoringTable_' + dimId + ' .indicator-matrix');
        if (!table) { icon.style.display = 'none'; return; }
        var hasAny = false;
        table.querySelectorAll('tbody .score-input').forEach(function(inp) {
            if (getInputValue(inp) !== null) hasAny = true;
        });
        icon.style.display = hasAny ? 'inline' : 'none';
    });
}

// ========== History (per-dimension) ==========
function loadHistorySession(date, type, dimId) {
    document.getElementById('assessmentDate').value = date;
    document.getElementById('assessmentType').value = type;
    // Clear all dimensions since date/type context changed
    clearAllScores();
    // Switch to the target dimension (triggers checkAndLoadSession via selectDimension)
    selectDimension(dimId);
}

function highlightHistoryRow(date, type, dimId) {
    document.querySelectorAll('.history-row').forEach(function(r) { r.classList.remove('table-active'); });
    var dimStr = String(dimId);
    document.querySelectorAll('.history-row').forEach(function(r) {
        if (r.getAttribute('data-date') === date && r.getAttribute('data-type') === type && r.getAttribute('data-dim-id') === dimStr) {
            r.classList.add('table-active');
        }
    });
}

// Date/type change: clear all scores then re-check current dimension
document.getElementById('assessmentDate').addEventListener('change', function() {
    clearAllScores();
    checkAndLoadSession();
});
document.getElementById('assessmentType').addEventListener('change', function() {
    clearAllScores();
    checkAndLoadSession();
});

// ========== Save (per-dimension) ==========
document.querySelectorAll('.btnSaveDim').forEach(function(btnSaveAll) {
    btnSaveAll.addEventListener('click', function() {
        if (!currentDimId) {
            Swal.fire({ icon: 'warning', title: '<?= lang("Common.idp_select_dimension") ?>' });
            return;
        }

        var table = document.querySelector('#scoringTable_' + currentDimId + ' .indicator-matrix');
        if (!table) return;

        var rows = table.querySelectorAll('tbody tr');
        var results = [];
        var hasScores = false;

        rows.forEach(function(row) {
            var planId = parseInt(row.getAttribute('data-plan-id'));
            var playerId = parseInt(row.getAttribute('data-player-id'));
            var inputs = row.querySelectorAll('.score-input');
            var scores = [];

            inputs.forEach(function(inp) {
                var val = getInputValue(inp);
                if (val !== null) {
                    scores.push({
                        dimension_id: parseInt(inp.getAttribute('data-dimension-id')),
                        indicator_id: parseInt(inp.getAttribute('data-indicator-id')),
                        score: val
                    });
                    hasScores = true;
                }
            });

            if (scores.length > 0) {
                results.push({ plan_id: planId, player_id: playerId, scores: scores });
            }
        });

        if (!hasScores) {
            Swal.fire({ icon: 'warning', title: '<?= lang("Common.fill_required") ?>' });
            return;
        }

        var dimName = DIMENSION_NAMES[currentDimId] || '';
        var confirmText = dimName + ' — ' + results.length + ' <?= lang("Common.players") ?>';
        if (currentMode === 'edit') {
            confirmText += ' (<?= lang("Common.idp_overwrite_existing") ?>)';
        }

        Swal.fire({
            title: '<?= lang("Common.confirm_save_data") ?>',
            text: confirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16478d',
            cancelButtonColor: '#d33',
            confirmButtonText: '<?= lang("Common.confirm") ?>',
            cancelButtonText: '<?= lang("Common.cancel") ?>'
        }).then(function(result) {
            if (!result.isConfirmed) return;

            var payload = {
                assessment_type: document.getElementById('assessmentType').value,
                assessment_date: document.getElementById('assessmentDate').value,
                dimension_id: currentDimId,
                results: results
            };

            fetch(SAVE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '<?= lang("Common.success") ?>',
                        text: data.message || '<?= lang("Common.idp_scores_saved") ?>',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: '<?= lang("Common.error") ?>', text: data.message });
                }
            })
            .catch(function() {
                Swal.fire({ icon: 'error', title: '<?= lang("Common.error") ?>', text: '<?= lang("Common.connection_error") ?>' });
            });
        });
    });
});
</script>
<?php $this->endSection(); ?>
