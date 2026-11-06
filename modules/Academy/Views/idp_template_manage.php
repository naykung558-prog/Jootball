<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <h4 class="mb-0 fw-bold text-primary">
                <i class="fas fa-file-alt me-2"></i>
                <?= !empty($template) ? lang('Common.idp_template_edit') : lang('Common.idp_template_create') ?>
            </h4>
            <?php if (!empty($academy['name'])): ?>
                <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
            <?php endif; ?>
        </div>

        <div class="card-body p-4">
            <form action="<?= base_url('academy/idp/template/save') ?>" method="POST" autocomplete="off">
                <input type="hidden" name="academy_id" value="<?= esc($academy['id'] ?? '') ?>">
                <?php if (!empty($template['id'])): ?>
                    <input type="hidden" name="id" value="<?= esc($template['id']) ?>">
                <?php endif; ?>

                <div class="row justify-content-center">
                    <!-- Title -->
                    <div class="col-md-8">
                        <label class="form-label pt-3"><?= lang('Common.idp_template_title') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="title" class="form-control" required
                               value="<?= esc($template['title'] ?? '') ?>"
                               placeholder="<?= lang('Common.idp_template_title') ?>">
                    </div>

                    <!-- Description -->
                    <div class="col-md-8">
                        <label class="form-label pt-3"><?= lang('Common.idp_template_description') ?></label>
                        <textarea name="description" class="form-control" rows="4"
                                  placeholder="<?= lang('Common.idp_template_description') ?>"><?= esc($template['description'] ?? '') ?></textarea>
                    </div>

                    <!-- Is Active -->
                    <div class="col-md-8">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                <?= (empty($template) || !empty($template['is_active'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active"><?= lang('Common.active') ?></label>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-8 mt-4 d-flex gap-2">
                        <a href="<?= base_url('academy/idp/library/' . $academy['id']) ?>"
                           class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-save me-1"></i><?= lang('Common.save') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
