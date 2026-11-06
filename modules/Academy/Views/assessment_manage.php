<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-clipboard-list me-2"></i>
                    <?= !empty($assessment) ? lang('Common.assessment_edit') : lang('Common.assessment_create') ?>
                </h4>
                <?php if (!empty($academy['name'])): ?>
                    <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('academy/assessments/' . esc($academy['id'])) ?>"
               class="btn btn-outline-secondary btn-sm rounded-pill">
                <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
            </a>
        </div>

        <div class="card-body p-4">
            <form action="<?= base_url('academy/assessment/save') ?>" method="POST" id="assessmentForm" autocomplete="off">
                <input type="hidden" name="academy_id" value="<?= esc($academy['id']) ?>">
                <input type="hidden" name="id" value="<?= esc($assessment['id'] ?? '') ?>">

                <!-- Basic Information -->
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <?= lang('Common.assessment_name') ?> <span class="text-danger">*</span>
                        </label>
                        <input type="text" name="name" class="form-control"
                               value="<?= esc($assessment['name'] ?? '') ?>"
                               placeholder="<?= lang('Common.assessment_name') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <?= lang('Common.assessment_date') ?> <span class="text-danger">*</span>
                        </label>
                        <input type="date" name="assessment_date" class="form-control"
                               value="<?= esc($assessment['assessment_date'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">
                            <?= lang('Common.assessment_type') ?> <span class="text-danger">*</span>
                        </label>
                        <select name="assessment_type" class="form-select" required>
                            <option value="fitness" <?= (($assessment['assessment_type'] ?? '') === 'fitness') ? 'selected' : '' ?>>
                                <?= lang('Common.assessment_type_fitness') ?>
                            </option>
                            <option value="skill" <?= (($assessment['assessment_type'] ?? '') === 'skill') ? 'selected' : '' ?>>
                                <?= lang('Common.assessment_type_skill') ?>
                            </option>
                            <option value="match" <?= (($assessment['assessment_type'] ?? '') === 'match') ? 'selected' : '' ?>>
                                <?= lang('Common.assessment_type_match') ?>
                            </option>
                            <option value="custom" <?= (($assessment['assessment_type'] ?? '') === 'custom') ? 'selected' : '' ?>>
                                <?= lang('Common.assessment_type_custom') ?>
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.training_groups') ?></label>
                        <select name="group_id" class="form-select">
                            <option value="">-- <?= lang('Common.training_select_group') ?> --</option>
                            <?php if (!empty($groups)): ?>
                                <?php foreach ($groups as $group): ?>
                                    <option value="<?= esc($group['id']) ?>"
                                        <?= (($assessment['group_id'] ?? '') == $group['id']) ? 'selected' : '' ?>>
                                        <?= esc($group['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <label class="form-label fw-bold"><?= lang('Common.description') ?></label>
                        <textarea name="description" class="form-control" rows="3"
                                  placeholder="<?= lang('Common.description') ?>"><?= esc($assessment['description'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Criteria Section -->
                <hr class="my-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary mb-0">
                        <i class="fas fa-list-ol me-2"></i><?= lang('Common.assessment_criteria') ?>
                    </h5>
                    <button type="button" class="btn btn-success btn-sm rounded-pill px-3" id="addCriteriaBtn">
                        <i class="fas fa-plus me-1"></i><?= lang('Common.assessment_add_criteria') ?>
                    </button>
                </div>

                <div id="criteriaContainer">
                    <?php if (!empty($criteria)): ?>
                        <?php foreach ($criteria as $index => $crit): ?>
                        <div class="criteria-row card border shadow-sm rounded-3 mb-3 p-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted">
                                        <?= lang('Common.assessment_criteria_name') ?> <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="criteria_name[]" class="form-control form-control-sm"
                                           value="<?= esc($crit['criteria_name'] ?? $crit['name'] ?? '') ?>"
                                           placeholder="<?= lang('Common.assessment_criteria_name') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted"><?= lang('Common.description') ?></label>
                                    <input type="text" name="criteria_description[]" class="form-control form-control-sm"
                                           value="<?= esc($crit['criteria_description'] ?? $crit['description'] ?? '') ?>"
                                           placeholder="<?= lang('Common.description') ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold text-muted">
                                        <?= lang('Common.assessment_max_score') ?> <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="criteria_max_score[]" class="form-control form-control-sm"
                                           value="<?= esc($crit['max_score'] ?? 10) ?>"
                                           min="1" max="1000" placeholder="10" required>
                                </div>
                                <div class="col-md-1 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-criteria-btn"
                                            title="<?= lang('Common.delete') ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <!-- Default empty row -->
                        <div class="criteria-row card border shadow-sm rounded-3 mb-3 p-3">
                            <div class="row g-2 align-items-end">
                                <div class="col-md-5">
                                    <label class="form-label small fw-bold text-muted">
                                        <?= lang('Common.assessment_criteria_name') ?> <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" name="criteria_name[]" class="form-control form-control-sm"
                                           placeholder="<?= lang('Common.assessment_criteria_name') ?>" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label small fw-bold text-muted"><?= lang('Common.description') ?></label>
                                    <input type="text" name="criteria_description[]" class="form-control form-control-sm"
                                           placeholder="<?= lang('Common.description') ?>">
                                </div>
                                <div class="col-md-2">
                                    <label class="form-label small fw-bold text-muted">
                                        <?= lang('Common.assessment_max_score') ?> <span class="text-danger">*</span>
                                    </label>
                                    <input type="number" name="criteria_max_score[]" class="form-control form-control-sm"
                                           value="10" min="1" max="1000" placeholder="10" required>
                                </div>
                                <div class="col-md-1 text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-criteria-btn"
                                            title="<?= lang('Common.delete') ?>">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- No Criteria Message -->
                <div id="noCriteriaMsg" class="text-center py-4 text-muted d-none">
                    <i class="fas fa-list-ol fa-2x mb-2 opacity-50"></i>
                    <p class="mb-0"><?= lang('Common.assessment_no_criteria') ?></p>
                </div>

                <!-- Submit -->
                <div class="text-center mt-4">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2" id="submitBtn">
                        <i class="fas fa-save me-2"></i><?= lang('Common.save') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Criteria Row Template (hidden) -->
<template id="criteriaRowTemplate">
    <div class="criteria-row card border shadow-sm rounded-3 mb-3 p-3">
        <div class="row g-2 align-items-end">
            <div class="col-md-5">
                <label class="form-label small fw-bold text-muted">
                    <?= lang('Common.assessment_criteria_name') ?> <span class="text-danger">*</span>
                </label>
                <input type="text" name="criteria_name[]" class="form-control form-control-sm"
                       placeholder="<?= lang('Common.assessment_criteria_name') ?>" required>
            </div>
            <div class="col-md-4">
                <label class="form-label small fw-bold text-muted"><?= lang('Common.description') ?></label>
                <input type="text" name="criteria_description[]" class="form-control form-control-sm"
                       placeholder="<?= lang('Common.description') ?>">
            </div>
            <div class="col-md-2">
                <label class="form-label small fw-bold text-muted">
                    <?= lang('Common.assessment_max_score') ?> <span class="text-danger">*</span>
                </label>
                <input type="number" name="criteria_max_score[]" class="form-control form-control-sm"
                       value="10" min="1" max="1000" placeholder="10" required>
            </div>
            <div class="col-md-1 text-center">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle remove-criteria-btn"
                        title="<?= lang('Common.delete') ?>">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
    </div>
</template>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const container = document.getElementById('criteriaContainer');
        const addBtn = document.getElementById('addCriteriaBtn');
        const template = document.getElementById('criteriaRowTemplate');
        const noCriteriaMsg = document.getElementById('noCriteriaMsg');

        function toggleNoCriteriaMsg() {
            var rows = container.querySelectorAll('.criteria-row');
            if (rows.length === 0) {
                noCriteriaMsg.classList.remove('d-none');
            } else {
                noCriteriaMsg.classList.add('d-none');
            }
        }

        // Add criteria row
        addBtn.addEventListener('click', function() {
            var clone = template.content.cloneNode(true);
            container.appendChild(clone);
            toggleNoCriteriaMsg();

            // Focus on the new criteria name input
            var newRows = container.querySelectorAll('.criteria-row');
            var lastRow = newRows[newRows.length - 1];
            var nameInput = lastRow.querySelector('input[name="criteria_name[]"]');
            if (nameInput) nameInput.focus();
        });

        // Remove criteria row (event delegation)
        container.addEventListener('click', function(e) {
            var btn = e.target.closest('.remove-criteria-btn');
            if (btn) {
                var row = btn.closest('.criteria-row');
                if (row) {
                    row.remove();
                    toggleNoCriteriaMsg();
                }
            }
        });

        toggleNoCriteriaMsg();
    });
</script>
<?php $this->endSection(); ?>
