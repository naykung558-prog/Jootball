<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-clipboard-list me-2"></i><?= lang('Common.assessments') ?>
                </h4>
                <?php if (!empty($academy['name'])): ?>
                    <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($perms['perm_create_assessment'])): ?>
            <div>
                <a href="<?= base_url('academy/assessment/manage/' . esc($academy['id'])) ?>"
                   class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus-circle me-2"></i><?= lang('Common.assessment_create') ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <!-- Filter Bar -->
        <div class="card-body border-top bg-light px-4 py-3">
            <form method="GET" action="<?= base_url('academy/assessments/' . esc($academy['id'])) ?>" class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold mb-1"><?= lang('Common.assessment_type') ?></label>
                    <select name="type" class="form-select form-select-sm">
                        <option value=""><?= lang('Common.view') ?> - <?= lang('Common.assessment_type') ?></option>
                        <option value="fitness" <?= (($filters['type'] ?? '') === 'fitness') ? 'selected' : '' ?>>
                            <?= lang('Common.assessment_type_fitness') ?>
                        </option>
                        <option value="skill" <?= (($filters['type'] ?? '') === 'skill') ? 'selected' : '' ?>>
                            <?= lang('Common.assessment_type_skill') ?>
                        </option>
                        <option value="match" <?= (($filters['type'] ?? '') === 'match') ? 'selected' : '' ?>>
                            <?= lang('Common.assessment_type_match') ?>
                        </option>
                        <option value="custom" <?= (($filters['type'] ?? '') === 'custom') ? 'selected' : '' ?>>
                            <?= lang('Common.assessment_type_custom') ?>
                        </option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted fw-bold mb-1"><?= lang('Common.training_groups') ?></label>
                    <select name="group_id" class="form-select form-select-sm">
                        <option value=""><?= lang('Common.view') ?> - <?= lang('Common.training_groups') ?></option>
                        <?php if (!empty($groups)): ?>
                            <?php foreach ($groups as $group): ?>
                                <option value="<?= esc($group['id']) ?>"
                                    <?= (($filters['group_id'] ?? '') == $group['id']) ? 'selected' : '' ?>>
                                    <?= esc($group['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                        <i class="fas fa-filter me-1"></i><?= lang('Common.search') ?>
                    </button>
                    <a href="<?= base_url('academy/assessments/' . esc($academy['id'])) ?>"
                       class="btn btn-sm btn-outline-secondary rounded-pill px-3">
                        <i class="fas fa-times me-1"></i><?= lang('Common.cancel') ?>
                    </a>
                </div>
            </form>
        </div>

        <!-- Assessment Cards -->
        <div class="card-body p-4">
            <?php if (!empty($assessments)): ?>
                <div class="row g-3">
                    <?php foreach ($assessments as $assessment): ?>
                    <div class="col-md-6 col-lg-4">
                        <a href="<?= base_url('academy/assessment/detail/' . esc($academy['id']) . '/' . esc($assessment['id'])) ?>"
                           class="text-decoration-none">
                            <div class="card h-100 border shadow-sm rounded-4 overflow-hidden hover-shadow">
                                <div class="card-body p-4">
                                    <!-- Type Badge -->
                                    <div class="mb-3">
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
                                        <span class="badge <?= $typeBadgeClass ?> rounded-pill px-3 py-1">
                                            <?= $typeLabel ?>
                                        </span>
                                    </div>

                                    <!-- Assessment Name -->
                                    <h6 class="fw-bold text-dark mb-2"><?= esc($assessment['name']) ?></h6>

                                    <!-- Date -->
                                    <div class="d-flex align-items-center text-muted small mb-2">
                                        <i class="far fa-calendar-alt me-2"></i>
                                        <?= !empty($assessment['assessment_date']) ? date('d/m/Y', strtotime($assessment['assessment_date'])) : '-' ?>
                                    </div>

                                    <!-- Group Name -->
                                    <?php if (!empty($assessment['group_name'])): ?>
                                    <div class="d-flex align-items-center text-muted small mb-2">
                                        <i class="fas fa-users me-2"></i>
                                        <?= esc($assessment['group_name']) ?>
                                    </div>
                                    <?php endif; ?>

                                    <!-- Creator -->
                                    <?php if (!empty($assessment['creator_name'])): ?>
                                    <div class="d-flex align-items-center text-muted small">
                                        <i class="fas fa-user me-2"></i>
                                        <?= esc($assessment['creator_name']) ?>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </a>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-clipboard-list fa-4x text-muted opacity-25"></i>
                    </div>
                    <h5 class="fw-bold text-muted mb-2"><?= lang('Common.assessment_no_assessments') ?></h5>
                    <p class="text-muted small mb-4"><?= lang('Common.no_data') ?></p>
                    <?php if (!empty($perms['perm_create_assessment'])): ?>
                        <a href="<?= base_url('academy/assessment/manage/' . esc($academy['id'])) ?>"
                           class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-plus-circle me-2"></i><?= lang('Common.assessment_create') ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>
