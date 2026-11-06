<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                    <?php if (!empty($template)): ?>
                        <?= lang('Common.idp_admin_template_edit') ?>
                    <?php else: ?>
                        <?= lang('Common.idp_admin_template_create') ?>
                    <?php endif; ?>
                </h4>
                <p class="text-muted mb-0 small"><?= lang('Common.idp_admin_template_fill_details') ?></p>
            </div>
            <a href="<?= base_url('admin/idp/templates') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <strong><i class="fas fa-exclamation-circle me-1"></i></strong> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/idp/template/save') ?>" method="POST" class="once-only" autocomplete="off">
            <input type="hidden" name="id" value="<?= isset($template['id']) ? $template['id'] : '' ?>">

            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="row g-3">
                        <!-- Title -->
                        <div class="col-md-12">
                            <label for="title" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_title_label') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" value="<?= isset($template['title']) ? $template['title'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" required>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="description" class="form-label fw-bold text-muted"><?= lang('Common.description') ?></label>
                            <textarea id="description" name="description" rows="4"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;"><?= isset($template['description']) ? $template['description'] : '' ?></textarea>
                        </div>

                        <!-- Owner Type -->
                        <div class="col-md-6">
                            <label for="owner_type" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_owner_type') ?></label>
                            <select id="owner_type" name="owner_type" class="form-select" style="border-radius: 8px; padding: 10px 15px;">
                                <option value="global" <?= (isset($template['owner_type']) && $template['owner_type'] === 'global') ? 'selected' : '' ?>><?= lang('Common.idp_admin_owner_global') ?></option>
                                <option value="academy" <?= (isset($template['owner_type']) && $template['owner_type'] === 'academy') ? 'selected' : '' ?>><?= lang('Common.idp_admin_owner_academy') ?></option>
                            </select>
                        </div>

                        <!-- Checkboxes -->
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_is_public') ?></label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1"
                                    <?= (isset($template['is_public']) && $template['is_public'] == 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_public"><?= lang('Common.idp_admin_is_public') ?></label>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.status') ?></label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                    <?= (!isset($template['is_active']) || $template['is_active'] == 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_active"><?= lang('Common.active') ?></label>
                            </div>
                        </div>

                        <!-- Submit -->
                        <div class="col-md-12 text-center mt-4">
                            <button type="submit" class="btn btn-primary px-5 py-2 fw-bold btn-submit" style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                                <i class="fas fa-save me-2"></i> <?= lang('Common.save') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>
