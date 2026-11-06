<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-star me-2 text-warning"></i><?= lang('Common.idp_assessment_title') ?>
            </h4>
            <p class="text-muted mb-0">
                <?= esc($plan['player_name'] ?? '') ?> — <?= esc($plan['title'] ?? '') ?>
            </p>
        </div>
        <a href="<?= base_url('academy/idp/plan/view/' . $academy['id'] . '/' . $plan['id']) ?>" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
        </a>
    </div>

    <!-- Assessment Form -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body p-4">
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold"><?= lang('Common.idp_assessment_type') ?></label>
                    <select id="assessment_type" class="form-select">
                        <option value="baseline"><?= lang('Common.idp_assessment_baseline') ?></option>
                        <option value="midterm"><?= lang('Common.idp_assessment_midterm') ?></option>
                        <option value="final"><?= lang('Common.idp_assessment_final') ?></option>
                        <option value="adhoc" selected><?= lang('Common.idp_assessment_adhoc') ?></option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold"><?= lang('Common.idp_assessment_date') ?></label>
                    <input type="date" id="assessment_date" class="form-control" value="<?= date('Y-m-d') ?>">
                </div>
            </div>

            <!-- Accordion: 7 Dimensions -->
            <div class="accordion" id="dimensionAccordion">
                <?php foreach ($dimensionsWithIndicators as $i => $dim): ?>
                <div class="accordion-item border rounded-3 mb-2">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $i > 0 ? 'collapsed' : '' ?>" type="button"
                                data-bs-toggle="collapse" data-bs-target="#dim_<?= $dim['id'] ?>">
                            <span class="me-2" style="color: <?= esc($dim['color']) ?>">
                                <i class="fas <?= esc($dim['icon']) ?> fa-lg"></i>
                            </span>
                            <strong><?= esc($dim['name']) ?></strong>
                        </button>
                    </h2>
                    <div id="dim_<?= $dim['id'] ?>" class="accordion-collapse collapse <?= $i === 0 ? 'show' : '' ?>"
                         data-bs-parent="#dimensionAccordion">
                        <div class="accordion-body">
                            <!-- Dimension-level Score -->
                            <div class="row g-3 mb-3 pb-3 border-bottom">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-primary">
                                        <?= lang('Common.idp_assessment_rating') ?> (<?= esc($dim['name']) ?>)
                                    </label>
                                    <div class="d-flex align-items-center gap-2">
                                        <?php for ($s = 1; $s <= 5; $s++): ?>
                                            <button type="button" class="btn btn-outline-warning btn-sm star-btn"
                                                    data-dim="<?= $dim['id'] ?>" data-score="<?= $s ?>"
                                                    onclick="setDimensionRating(<?= $dim['id'] ?>, <?= $s ?>)">
                                                <i class="fas fa-star"></i> <?= $s ?>
                                            </button>
                                        <?php endfor; ?>
                                        <input type="hidden" id="dim_score_<?= $dim['id'] ?>" value="">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold"><?= lang('Common.idp_progress_notes') ?></label>
                                    <input type="text" id="dim_notes_<?= $dim['id'] ?>" class="form-control form-control-sm" placeholder="<?= lang('Common.idp_progress_notes') ?>">
                                </div>
                            </div>

                            <!-- Indicator-level Scores -->
                            <?php if (!empty($dim['indicators'])): ?>
                                <small class="text-muted fw-bold mb-2 d-block"><?= lang('Common.idp_indicators') ?>:</small>
                                <?php foreach ($dim['indicators'] as $ind): ?>
                                    <div class="row g-2 mb-2 align-items-center">
                                        <div class="col-md-4">
                                            <small class="fw-bold"><?= esc($ind['name']) ?></small>
                                            <?php if (!empty($ind['unit'])): ?>
                                                <small class="text-muted">(<?= esc($ind['unit']) ?>)</small>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?php if ($ind['measurement_type'] === 'rating'): ?>
                                                <select class="form-select form-select-sm ind-input"
                                                        data-dim="<?= $dim['id'] ?>" data-ind="<?= $ind['id'] ?>" data-type="rating">
                                                    <option value="">--</option>
                                                    <?php for ($r = 1; $r <= 5; $r++): ?>
                                                        <option value="<?= $r ?>"><?= $r ?> / 5</option>
                                                    <?php endfor; ?>
                                                </select>
                                            <?php else: ?>
                                                <input type="number" class="form-control form-control-sm ind-input"
                                                       data-dim="<?= $dim['id'] ?>" data-ind="<?= $ind['id'] ?>" data-type="score"
                                                       step="0.01"
                                                       min="<?= $ind['min_value'] ?>" max="<?= $ind['max_value'] ?>"
                                                       placeholder="<?= $ind['min_value'] ?> - <?= $ind['max_value'] ?>">
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-md-5">
                                            <input type="text" class="form-control form-control-sm ind-notes"
                                                   data-ind="<?= $ind['id'] ?>"
                                                   placeholder="<?= lang('Common.idp_progress_notes') ?>">
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Save Button -->
            <?php if (!empty($perms['perm_manage_idp'])): ?>
            <div class="d-flex justify-content-end mt-4">
                <button class="btn btn-primary btn-lg" onclick="saveAllAssessments()">
                    <i class="fas fa-save me-2"></i><?= lang('Common.save') ?>
                </button>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const BASE_URL = '<?= base_url() ?>';
const PLAN_ID = <?= $plan['id'] ?>;

function setDimensionRating(dimId, score) {
    document.getElementById('dim_score_' + dimId).value = score;
    // Highlight buttons
    document.querySelectorAll(`.star-btn[data-dim="${dimId}"]`).forEach(btn => {
        const s = parseInt(btn.dataset.score);
        if (s <= score) {
            btn.classList.remove('btn-outline-warning');
            btn.classList.add('btn-warning', 'text-white');
        } else {
            btn.classList.remove('btn-warning', 'text-white');
            btn.classList.add('btn-outline-warning');
        }
    });
}

function saveAllAssessments() {
    const assessmentType = document.getElementById('assessment_type').value;
    const assessmentDate = document.getElementById('assessment_date').value;
    const scores = [];

    // Dimension-level scores
    document.querySelectorAll('[id^="dim_score_"]').forEach(input => {
        if (input.value) {
            const dimId = input.id.replace('dim_score_', '');
            const notes = document.getElementById('dim_notes_' + dimId)?.value || '';
            scores.push({
                dimension_id: dimId,
                indicator_id: null,
                score: null,
                rating: parseInt(input.value),
                notes: notes
            });
        }
    });

    // Indicator-level scores
    document.querySelectorAll('.ind-input').forEach(input => {
        if (input.value) {
            const dimId = input.dataset.dim;
            const indId = input.dataset.ind;
            const type = input.dataset.type;
            const notesInput = document.querySelector(`.ind-notes[data-ind="${indId}"]`);
            scores.push({
                dimension_id: dimId,
                indicator_id: indId,
                score: type === 'score' ? parseFloat(input.value) : null,
                rating: type === 'rating' ? parseInt(input.value) : null,
                notes: notesInput?.value || ''
            });
        }
    });

    if (scores.length === 0) {
        Swal.fire('', '<?= lang('Common.no_data') ?>', 'warning');
        return;
    }

    fetch(BASE_URL + '/academy/idp/assessment/save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({
            plan_id: PLAN_ID,
            assessment_type: assessmentType,
            assessment_date: assessmentDate,
            scores: scores
        })
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire({icon: 'success', title: res.message, timer: 1500, showConfirmButton: false})
            .then(() => history.back());
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
}
</script>
<?php $this->endSection(); ?>
