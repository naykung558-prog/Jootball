<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-file-medical me-2 text-primary"></i>
                <?= !empty($plan) ? lang('Common.idp_plan_edit') : lang('Common.idp_plan_create') ?>
            </h4>
            <p class="text-muted mb-0">
                <i class="fas fa-user me-1"></i><?= esc($player['first_name'] . ' ' . $player['last_name']) ?>
                <?php if (!empty($player['nick_name'])): ?> (<?= esc($player['nick_name']) ?>)<?php endif; ?>
            </p>
        </div>
        <a href="<?= base_url('academy/idp/' . $academy['id']) ?>" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
            <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
        </a>
    </div>

    <!-- Plan Form -->
    <form method="POST" action="<?= base_url('academy/idp/plan/save') ?>">
        <input type="hidden" name="id" value="<?= $plan['id'] ?? '' ?>">
        <input type="hidden" name="academy_id" value="<?= $academy['id'] ?>">
        <input type="hidden" name="player_id" value="<?= $player['id'] ?>">

        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-white border-0 py-3 px-4">
                <h5 class="mb-0 fw-bold"><i class="fas fa-info-circle me-2"></i><?= lang('Common.idp_plan_view') ?></h5>
            </div>
            <div class="card-body p-4">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-bold"><?= lang('Common.idp_plan_title') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control"
                               value="<?= esc($plan['title'] ?? '') ?>"
                               placeholder="<?= lang('Common.idp_plan_title') ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_plan_season') ?></label>
                        <input type="text" name="season" class="form-control"
                               value="<?= esc($plan['season'] ?? date('Y') . '-' . (date('Y')+1)) ?>"
                               placeholder="2025-26">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_plan_start_date') ?> <span class="text-danger">*</span></label>
                        <input type="date" name="start_date" class="form-control"
                               value="<?= esc($plan['start_date'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_plan_end_date') ?> <span class="text-danger">*</span></label>
                        <input type="date" name="end_date" class="form-control"
                               value="<?= esc($plan['end_date'] ?? date('Y-m-d', strtotime('+6 months'))) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_plan_status') ?></label>
                        <select name="status" class="form-select">
                            <option value="draft" <?= (($plan['status'] ?? 'draft') === 'draft') ? 'selected' : '' ?>><?= lang('Common.idp_status_draft') ?></option>
                            <option value="active" <?= (($plan['status'] ?? '') === 'active') ? 'selected' : '' ?>><?= lang('Common.idp_status_active') ?></option>
                            <option value="completed" <?= (($plan['status'] ?? '') === 'completed') ? 'selected' : '' ?>><?= lang('Common.idp_status_completed') ?></option>
                            <option value="cancelled" <?= (($plan['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>><?= lang('Common.idp_status_cancelled') ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.idp_plan_coach') ?></label>
                        <select name="coach_id" class="form-select">
                            <option value="">-- <?= lang('Common.idp_plan_coach') ?> --</option>
                            <?php foreach ($coaches ?? [] as $coach): ?>
                                <option value="<?= $coach['id'] ?>" <?= (($plan['coach_id'] ?? '') == $coach['id']) ? 'selected' : '' ?>>
                                    <?= esc($coach['first_name'] . ' ' . $coach['last_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.idp_template') ?></label>
                        <select name="template_id" class="form-select">
                            <option value=""><?= lang('Common.idp_template_default') ?></option>
                            <?php if (!empty($templates)): ?>
                                <?php foreach ($templates as $tpl): ?>
                                <option value="<?= esc($tpl['id']) ?>" <?= (isset($plan['template_id']) && $plan['template_id'] == $tpl['id']) ? 'selected' : '' ?>>
                                    <?= esc($tpl['title']) ?>
                                    <?php if ($tpl['owner_type'] === 'global'): ?> (<?= lang('Common.idp_global_badge') ?>)<?php endif; ?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold"><?= lang('Common.idp_plan_notes') ?></label>
                        <textarea name="overall_notes" class="form-control" rows="3"
                                  placeholder="<?= lang('Common.idp_plan_notes') ?>"><?= esc($plan['overall_notes'] ?? '') ?></textarea>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit -->
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="<?= base_url('academy/idp/' . $academy['id']) ?>" class="btn btn-secondary">
                <i class="fas fa-times me-1"></i><?= lang('Common.cancel') ?>
            </a>
            <button type="submit" class="btn btn-primary">
                <i class="fas fa-save me-1"></i><?= lang('Common.save') ?>
            </button>
        </div>
    </form>
</div>
<?php $this->endSection(); ?>
