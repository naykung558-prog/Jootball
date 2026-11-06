<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                    <?php if (!empty($dimension)): ?>
                        <?= lang('Common.idp_admin_dimension_edit') ?>
                    <?php else: ?>
                        <?= lang('Common.idp_admin_dimension_create') ?>
                    <?php endif; ?>
                </h4>
                <p class="text-muted mb-0 small"><?= lang('Common.idp_admin_dimension_fill_details') ?></p>
                <?php if (!empty($template)): ?>
                    <p class="text-muted mb-0 small">
                        <?= lang('Common.idp_admin_templates') ?>: <strong><?= isset($template['title']) ? $template['title'] : '' ?></strong>
                    </p>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('admin/idp/template/detail/' . $template_id) ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <strong><i class="fas fa-exclamation-circle me-1"></i></strong> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/idp/dimension/save') ?>" method="POST" class="once-only" autocomplete="off">
            <input type="hidden" name="id" value="<?= isset($dimension['id']) ? $dimension['id'] : '' ?>">
            <input type="hidden" name="template_id" value="<?= $template_id ?>">

            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="row g-3">
                        <!-- Template (read-only display) -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_template_select') ?></label>
                            <select name="template_id_display" class="form-select" style="border-radius: 8px; padding: 10px 15px;" disabled>
                                <?php if (!empty($templates)): ?>
                                    <?php foreach ($templates as $t): ?>
                                        <option value="<?= $t['id'] ?>" <?= ($t['id'] == $template_id) ? 'selected' : '' ?>><?= $t['title'] ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Name TH -->
                        <div class="col-md-6">
                            <label for="name_th" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_name_th') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="name_th" name="name_th" value="<?= isset($dimension['name_th']) ? $dimension['name_th'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" required>
                        </div>

                        <!-- Name EN -->
                        <div class="col-md-6">
                            <label for="name_en" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_name_en') ?></label>
                            <input type="text" id="name_en" name="name_en" value="<?= isset($dimension['name_en']) ? $dimension['name_en'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <!-- Description TH -->
                        <div class="col-md-6">
                            <label for="description_th" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_description_th') ?></label>
                            <textarea id="description_th" name="description_th" rows="3"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;"><?= isset($dimension['description_th']) ? $dimension['description_th'] : '' ?></textarea>
                        </div>

                        <!-- Description EN -->
                        <div class="col-md-6">
                            <label for="description_en" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_description_en') ?></label>
                            <textarea id="description_en" name="description_en" rows="3"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;"><?= isset($dimension['description_en']) ? $dimension['description_en'] : '' ?></textarea>
                        </div>

                        <!-- Icon -->
                        <div class="col-md-4">
                            <label for="icon" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_icon') ?></label>
                            <div class="input-group">
                                <input type="text" id="icon" name="icon" value="<?= isset($dimension['icon']) ? $dimension['icon'] : '' ?>"
                                    class="form-control" style="border-radius: 8px 0 0 8px; padding: 10px 15px;" placeholder="fa-futbol">
                                <span class="input-group-text" id="icon-preview" style="border-radius: 0 8px 8px 0; width: 50px; justify-content: center;">
                                    <i class="fa <?= isset($dimension['icon']) ? $dimension['icon'] : 'fa-circle' ?>" style="font-size: 1.3rem;"></i>
                                </span>
                            </div>
                            <div class="form-text small text-muted"><?= lang('Common.idp_admin_icon_preview') ?> (Font Awesome 4.x)</div>
                        </div>

                        <!-- Color -->
                        <div class="col-md-4">
                            <label for="color" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_color') ?></label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" id="color" name="color" value="<?= isset($dimension['color']) ? $dimension['color'] : '#3498db' ?>"
                                    class="form-control form-control-color" style="width: 60px; height: 45px;">
                                <input type="text" id="color_text" value="<?= isset($dimension['color']) ? $dimension['color'] : '#3498db' ?>"
                                    class="form-control" style="border-radius: 8px; padding: 10px 15px;" readonly>
                            </div>
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-4">
                            <label for="sort_order" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_sort_order') ?></label>
                            <input type="number" id="sort_order" name="sort_order" value="<?= isset($dimension['sort_order']) ? $dimension['sort_order'] : 0 ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" min="0">
                        </div>

                        <!-- Owner Type -->
                        <div class="col-md-4">
                            <label for="owner_type" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_owner_type') ?></label>
                            <select id="owner_type" name="owner_type" class="form-select" style="border-radius: 8px; padding: 10px 15px;">
                                <option value="global" <?= (isset($dimension['owner_type']) && $dimension['owner_type'] === 'global') ? 'selected' : '' ?>><?= lang('Common.idp_admin_owner_global') ?></option>
                                <option value="academy" <?= (isset($dimension['owner_type']) && $dimension['owner_type'] === 'academy') ? 'selected' : '' ?>><?= lang('Common.idp_admin_owner_academy') ?></option>
                            </select>
                        </div>

                        <!-- Is Public -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_is_public') ?></label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_public" name="is_public" value="1"
                                    <?= (isset($dimension['is_public']) && $dimension['is_public'] == 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_public"><?= lang('Common.idp_admin_is_public') ?></label>
                            </div>
                        </div>

                        <!-- Is Active -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.status') ?></label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                    <?= (!isset($dimension['is_active']) || $dimension['is_active'] == 1) ? 'checked' : '' ?>>
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

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        // Icon preview
        $('#icon').on('input', function() {
            var iconClass = $(this).val() || 'fa-circle';
            $('#icon-preview i').attr('class', 'fa ' + iconClass);
        });

        // Color sync
        $('#color').on('input', function() {
            $('#color_text').val($(this).val());
            $('#icon-preview i').css('color', $(this).val());
        });
    });
</script>
<?php $this->endSection(); ?>
