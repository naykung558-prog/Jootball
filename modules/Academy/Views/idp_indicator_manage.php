<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <h4 class="mb-0 fw-bold text-primary">
                <i class="fas fa-ruler me-2"></i>
                <?= !empty($indicator) ? lang('Common.idp_indicator_edit') : lang('Common.idp_indicator_create') ?>
            </h4>
            <p class="text-muted small mb-0">
                <?= esc($academy['name'] ?? '') ?>
                &mdash; <?= esc($template['title'] ?? '') ?>
                &mdash; <i class="fas <?= esc($dimension['icon'] ?? 'fa-circle') ?>" style="color: <?= esc($dimension['color'] ?? '#0d6efd') ?>;"></i>
                <?php
                    $lang = function_exists('get_current_language') ? get_current_language() : 'th';
                    $dimName = ($lang === 'en' && !empty($dimension['name_en'])) ? $dimension['name_en'] : ($dimension['name_th'] ?? '');
                ?>
                <?= esc($dimName) ?>
            </p>
        </div>

        <div class="card-body p-4">
            <form action="<?= base_url('academy/idp/indicator/save') ?>" method="POST" autocomplete="off">
                <input type="hidden" name="academy_id" value="<?= esc($academy['id'] ?? '') ?>">
                <input type="hidden" name="dimension_id" value="<?= esc($dimension['id'] ?? '') ?>">
                <?php if (!empty($indicator['id'])): ?>
                    <input type="hidden" name="id" value="<?= esc($indicator['id']) ?>">
                <?php endif; ?>

                <div class="row justify-content-center">
                    <!-- Name Thai -->
                    <div class="col-md-5">
                        <label class="form-label pt-3"><?= lang('Common.idp_name_th') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name_th" class="form-control" required
                               value="<?= esc($indicator['name_th'] ?? '') ?>">
                    </div>

                    <!-- Name English -->
                    <div class="col-md-5">
                        <label class="form-label pt-3"><?= lang('Common.idp_name_en') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name_en" class="form-control" required
                               value="<?= esc($indicator['name_en'] ?? '') ?>">
                    </div>

                    <!-- Description Thai -->
                    <div class="col-md-5">
                        <label class="form-label pt-3"><?= lang('Common.idp_description_th') ?></label>
                        <textarea name="description_th" class="form-control" rows="3"><?= esc($indicator['description_th'] ?? '') ?></textarea>
                    </div>

                    <!-- Description English -->
                    <div class="col-md-5">
                        <label class="form-label pt-3"><?= lang('Common.idp_description_en') ?></label>
                        <textarea name="description_en" class="form-control" rows="3"><?= esc($indicator['description_en'] ?? '') ?></textarea>
                    </div>

                    <!-- Measurement Type -->
                    <div class="col-md-4">
                        <label class="form-label pt-3"><?= lang('Common.idp_measurement_type') ?> <span class="text-danger">*</span></label>
                        <select name="measurement_type" class="form-select" required>
                            <?php
                            $measureTypes = [
                                'score'      => lang('Common.idp_measurement_score'),
                                'time'       => lang('Common.idp_measurement_time'),
                                'distance'   => lang('Common.idp_measurement_distance'),
                                'percentage' => lang('Common.idp_measurement_percentage'),
                                'rating'     => lang('Common.idp_measurement_rating'),
                            ];
                            $currentType = $indicator['measurement_type'] ?? 'score';
                            foreach ($measureTypes as $val => $label):
                            ?>
                            <option value="<?= $val ?>" <?= $currentType === $val ? 'selected' : '' ?>><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Unit -->
                    <div class="col-md-3">
                        <label class="form-label pt-3"><?= lang('Common.idp_unit') ?></label>
                        <input type="text" name="unit" class="form-control"
                               value="<?= esc($indicator['unit'] ?? '') ?>"
                               placeholder="e.g., sec, m, %">
                    </div>

                    <!-- Is Lower Better -->
                    <div class="col-md-3">
                        <div class="form-check form-switch mt-5">
                            <input class="form-check-input" type="checkbox" name="is_lower_better" value="1" id="is_lower_better"
                                <?= !empty($indicator['is_lower_better']) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_lower_better"><?= lang('Common.idp_is_lower_better') ?></label>
                        </div>
                    </div>

                    <!-- Min Value -->
                    <div class="col-md-3">
                        <label class="form-label pt-3"><?= lang('Common.idp_min_value') ?></label>
                        <input type="number" name="min_value" class="form-control" step="0.01"
                               value="<?= esc($indicator['min_value'] ?? '0') ?>">
                    </div>

                    <!-- Max Value -->
                    <div class="col-md-3">
                        <label class="form-label pt-3"><?= lang('Common.idp_max_value') ?></label>
                        <input type="number" name="max_value" class="form-control" step="0.01"
                               value="<?= esc($indicator['max_value'] ?? '100') ?>">
                    </div>

                    <!-- Sort Order -->
                    <div class="col-md-2">
                        <label class="form-label pt-3"><?= lang('Common.idp_sort_order') ?></label>
                        <input type="number" name="sort_order" class="form-control" min="0"
                               value="<?= esc($indicator['sort_order'] ?? '0') ?>">
                    </div>

                    <!-- Is Active -->
                    <div class="col-md-2">
                        <div class="form-check form-switch mt-5">
                            <input class="form-check-input" type="checkbox" name="is_active" value="1" id="is_active"
                                <?= (empty($indicator) || !empty($indicator['is_active'])) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active"><?= lang('Common.active') ?></label>
                        </div>
                    </div>

                    <!-- Buttons -->
                    <div class="col-md-10 mt-4 d-flex gap-2">
                        <?php
                        $backUrl = base_url('academy/idp/template/detail/' . $academy['id'] . '/' . ($template['id'] ?? ''));
                        ?>
                        <a href="<?= $backUrl ?>"
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
