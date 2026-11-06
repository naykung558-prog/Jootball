<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <h4 class="mb-0 fw-bold text-primary">
                <i class="fas fa-cubes me-2"></i>
                <?= !empty($dimension) ? lang('Common.idp_dimension_edit') : lang('Common.idp_dimension_create') ?>
            </h4>
            <p class="text-muted small mb-0">
                <?= esc($academy['name'] ?? '') ?> &mdash; <?= esc($template['title'] ?? '') ?>
            </p>
        </div>

        <div class="card-body p-4">
            <form action="<?= base_url('academy/idp/dimension/save') ?>" method="POST" autocomplete="off">
                <input type="hidden" name="academy_id" value="<?= esc($academy['id'] ?? '') ?>">
                <input type="hidden" name="template_id" value="<?= esc($template['id'] ?? '') ?>">
                <?php if (!empty($dimension['id'])): ?>
                    <input type="hidden" name="id" value="<?= esc($dimension['id']) ?>">
                <?php endif; ?>

                <div class="row justify-content-center">
                    <!-- Name Thai -->
                    <div class="col-md-5">
                        <label class="form-label pt-3"><?= lang('Common.idp_name_th') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name_th" class="form-control" required
                               value="<?= esc($dimension['name_th'] ?? '') ?>">
                    </div>

                    <!-- Name English -->
                    <div class="col-md-5">
                        <label class="form-label pt-3"><?= lang('Common.idp_name_en') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" required
                               value="<?= esc($dimension['name_en'] ?? '') ?>">
                    </div>

                    <!-- Description Thai -->
                    <div class="col-md-5">
                        <label class="form-label pt-3"><?= lang('Common.idp_description_th') ?></label>
                        <textarea name="description_th" class="form-control" rows="3"><?= esc($dimension['description_th'] ?? '') ?></textarea>
                    </div>

                    <!-- Description English -->
                    <div class="col-md-5">
                        <label class="form-label pt-3"><?= lang('Common.idp_description_en') ?></label>
                        <textarea name="description_en" class="form-control" rows="3"><?= esc($dimension['description_en'] ?? '') ?></textarea>
                    </div>

                    <!-- Icon -->
                    <div class="col-md-4">
                        <label class="form-label pt-3"><?= lang('Common.idp_icon') ?></label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas <?= esc($dimension['icon'] ?? 'fa-circle') ?>" id="icon-preview"></i></span>
                            <input type="text" name="icon" class="form-control" id="icon-input"
                                   value="<?= esc($dimension['icon'] ?? 'fa-circle') ?>"
                                   placeholder="fa-futbol">
                        </div>
                        <small class="form-text text-muted">FontAwesome class (e.g., fa-futbol, fa-brain)</small>
                    </div>

                    <!-- Color -->
                    <div class="col-md-3">
                        <label class="form-label pt-3"><?= lang('Common.idp_color') ?></label>
                        <input type="color" name="color" class="form-control form-control-color"
                               value="<?= esc($dimension['color'] ?? '#0d6efd') ?>" style="height: 40px;">
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-3">
                        <label class="form-label pt-3"><?= lang('Common.idp_sort_order') ?></label>
                        <input type="number" name="sort_order" class="form-control" min="0"
                               value="<?= esc($dimension['sort_order'] ?? '0') ?>">
                    </div>

                    <!-- Is Active -->
                    <div class="col-md-10">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                <?= (empty($dimension) || !empty($dimension['is_active'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active"><?= lang('Common.active') ?></label>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-10 mt-4 d-flex gap-2">
                        <a href="<?= base_url('academy/idp/template/detail/' . $academy['id'] . '/' . $template['id']) ?>"
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Live icon preview
    var iconInput = document.getElementById('icon-input');
    var iconPreview = document.getElementById('icon-preview');
    if (iconInput && iconPreview) {
        iconInput.addEventListener('input', function() {
            iconPreview.className = 'fas ' + this.value;
        });
    }
});
</script>
<?php $this->endSection(); ?>
