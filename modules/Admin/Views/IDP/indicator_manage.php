<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                    <?php if (!empty($indicator)): ?>
                        <?= lang('Common.idp_admin_indicator_edit') ?>
                    <?php else: ?>
                        <?= lang('Common.idp_admin_indicator_create') ?>
                    <?php endif; ?>
                </h4>
                <p class="text-muted mb-0 small"><?= lang('Common.idp_admin_indicator_fill_details') ?></p>
                <?php if (!empty($dimension)): ?>
                    <?php
                        $lang = function_exists('get_current_language') ? get_current_language() : 'th';
                        $dimName = ($lang === 'en' && !empty($dimension['name_en'])) ? $dimension['name_en'] : $dimension['name_th'];
                    ?>
                    <p class="text-muted mb-0 small">
                        <?= lang('Common.idp_admin_dimensions') ?>: <strong><?= $dimName ?></strong>
                    </p>
                <?php endif; ?>
            </div>
            <a href="<?= base_url('admin/idp/indicators/' . $dimension_id) ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <strong><i class="fas fa-exclamation-circle me-1"></i></strong> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/idp/indicator/save') ?>" method="POST" class="once-only" autocomplete="off">
            <input type="hidden" name="id" value="<?= isset($indicator['id']) ? $indicator['id'] : '' ?>">
            <input type="hidden" name="dimension_id" value="<?= $dimension_id ?>">

            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="row g-3">
                        <!-- Dimension (read-only display) -->
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_dimension_select') ?></label>
                            <select name="dimension_id_display" class="form-select" style="border-radius: 8px; padding: 10px 15px;" disabled>
                                <?php if (!empty($dimensions)): ?>
                                    <?php foreach ($dimensions as $d): ?>
                                        <?php
                                            $dName = ($lang === 'en' && !empty($d['name_en'])) ? $d['name_en'] : $d['name_th'];
                                        ?>
                                        <option value="<?= $d['id'] ?>" <?= ($d['id'] == $dimension_id) ? 'selected' : '' ?>><?= $dName ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Name TH -->
                        <div class="col-md-6">
                            <label for="name_th" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_name_th') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="name_th" name="name_th" value="<?= isset($indicator['name_th']) ? $indicator['name_th'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" required>
                        </div>

                        <!-- Name EN -->
                        <div class="col-md-6">
                            <label for="name_en" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_name_en') ?></label>
                            <input type="text" id="name_en" name="name_en" value="<?= isset($indicator['name_en']) ? $indicator['name_en'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <!-- Description TH -->
                        <div class="col-md-6">
                            <label for="description_th" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_description_th') ?></label>
                            <textarea id="description_th" name="description_th" rows="3"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;"><?= isset($indicator['description_th']) ? $indicator['description_th'] : '' ?></textarea>
                        </div>

                        <!-- Description EN -->
                        <div class="col-md-6">
                            <label for="description_en" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_description_en') ?></label>
                            <textarea id="description_en" name="description_en" rows="3"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;"><?= isset($indicator['description_en']) ? $indicator['description_en'] : '' ?></textarea>
                        </div>

                        <!-- Measurement Type -->
                        <div class="col-md-4">
                            <label for="measurement_type" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_measurement_type') ?> <span class="text-danger">*</span></label>
                            <select id="measurement_type" name="measurement_type" class="form-select" style="border-radius: 8px; padding: 10px 15px;" required>
                                <option value="score" <?= (isset($indicator['measurement_type']) && $indicator['measurement_type'] === 'score') ? 'selected' : '' ?>>Score</option>
                                <option value="time" <?= (isset($indicator['measurement_type']) && $indicator['measurement_type'] === 'time') ? 'selected' : '' ?>>Time</option>
                                <option value="distance" <?= (isset($indicator['measurement_type']) && $indicator['measurement_type'] === 'distance') ? 'selected' : '' ?>>Distance</option>
                                <option value="percentage" <?= (isset($indicator['measurement_type']) && $indicator['measurement_type'] === 'percentage') ? 'selected' : '' ?>>Percentage</option>
                                <option value="rating" <?= (isset($indicator['measurement_type']) && $indicator['measurement_type'] === 'rating') ? 'selected' : '' ?>>Rating</option>
                            </select>
                        </div>

                        <!-- Unit -->
                        <div class="col-md-4">
                            <label for="unit" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_unit') ?></label>
                            <input type="text" id="unit" name="unit" value="<?= isset($indicator['unit']) ? $indicator['unit'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" placeholder="e.g. sec, m, %">
                        </div>

                        <!-- Sort Order -->
                        <div class="col-md-4">
                            <label for="sort_order" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_sort_order') ?></label>
                            <input type="number" id="sort_order" name="sort_order" value="<?= isset($indicator['sort_order']) ? $indicator['sort_order'] : 0 ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" min="0">
                        </div>

                        <!-- Min Value -->
                        <div class="col-md-4">
                            <label for="min_value" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_min_value') ?></label>
                            <input type="number" id="min_value" name="min_value" value="<?= isset($indicator['min_value']) ? $indicator['min_value'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" step="any">
                        </div>

                        <!-- Max Value -->
                        <div class="col-md-4">
                            <label for="max_value" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_max_value') ?></label>
                            <input type="number" id="max_value" name="max_value" value="<?= isset($indicator['max_value']) ? $indicator['max_value'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" step="any">
                        </div>

                        <!-- Is Lower Better -->
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_is_lower_better') ?></label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_lower_better" name="is_lower_better" value="1"
                                    <?= (isset($indicator['is_lower_better']) && $indicator['is_lower_better'] == 1) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_lower_better"><?= lang('Common.idp_admin_is_lower_better') ?></label>
                            </div>
                        </div>

                        <!-- Owner Type -->
                        <div class="col-md-6">
                            <label for="owner_type" class="form-label fw-bold text-muted"><?= lang('Common.idp_admin_owner_type') ?></label>
                            <select id="owner_type" name="owner_type" class="form-select" style="border-radius: 8px; padding: 10px 15px;">
                                <option value="global" <?= (isset($indicator['owner_type']) && $indicator['owner_type'] === 'global') ? 'selected' : '' ?>><?= lang('Common.idp_admin_owner_global') ?></option>
                                <option value="academy" <?= (isset($indicator['owner_type']) && $indicator['owner_type'] === 'academy') ? 'selected' : '' ?>><?= lang('Common.idp_admin_owner_academy') ?></option>
                            </select>
                        </div>

                        <!-- Is Active -->
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.status') ?></label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1"
                                    <?= (!isset($indicator['is_active']) || $indicator['is_active'] == 1) ? 'checked' : '' ?>>
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
