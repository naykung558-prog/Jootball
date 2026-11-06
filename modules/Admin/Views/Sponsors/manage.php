<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-handshake text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                        <?php if (!empty($data['id'])): ?>
                            <?= lang('Common.edit') ?> <?= lang('Common.sponsor') ?>
                        <?php else: ?>
                            <?= lang('Common.add_sponsor') ?>
                        <?php endif; ?>
                    </h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.manage_sponsors_desc') ?></p>
                </div>
            </div>
            <a href="<?= base_url('admin/sponsors') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <strong><i class="fas fa-exclamation-circle me-1"></i></strong>
                <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="<?= base_url('admin/sponsors/save') ?>" method="POST" enctype="multipart/form-data" class="once-only">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (!empty($data['id'])) ? $data['id'] : '' ?>">

            <div class="row justify-content-center">
                <div class="col-md-10">

                    <!-- Sponsor Information -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                        <?= lang('Common.sponsor_information') ?>
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold text-muted">
                                <?= lang('Common.sponsor_name') ?> <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   value="<?= (!empty($data['name'])) ? $data['name'] : '' ?>"
                                   class="form-control" required
                                   placeholder="<?= lang('Common.enter_sponsor_name') ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <div class="col-md-6">
                            <label for="name_en" class="form-label fw-bold text-muted">
                                <?= lang('Common.sponsor_name_en') ?>
                            </label>
                            <input type="text" name="name_en" id="name_en"
                                   value="<?= (!empty($data['name_en'])) ? $data['name_en'] : '' ?>"
                                   class="form-control"
                                   placeholder="<?= lang('Common.enter_sponsor_name_en') ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <div class="col-md-6">
                            <label for="start" class="form-label fw-bold text-muted">
                                <?= lang('Common.start_date') ?>
                            </label>
                            <input type="datetime-local" name="start" id="start"
                                   value="<?= (!empty($data['start'])) ? date('Y-m-d\TH:i', strtotime($data['start'])) : '' ?>"
                                   class="form-control"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <div class="col-md-6">
                            <label for="end" class="form-label fw-bold text-muted">
                                <?= lang('Common.end_date') ?>
                            </label>
                            <input type="datetime-local" name="end" id="end"
                                   value="<?= (!empty($data['end'])) ? date('Y-m-d\TH:i', strtotime($data['end'])) : '' ?>"
                                   class="form-control"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <div class="col-md-6">
                            <label for="is_active" class="form-label fw-bold text-muted">
                                <?= lang('Common.status') ?>
                            </label>
                            <select name="is_active" id="is_active" class="form-select" style="border-radius: 8px; padding: 10px 15px;">
                                <option value="1" <?= (isset($data['is_active']) && $data['is_active'] == 1) ? 'selected' : '' ?>><?= lang('Common.active') ?></option>
                                <option value="0" <?= (isset($data['is_active']) && $data['is_active'] == 0) ? 'selected' : '' ?>><?= lang('Common.inactive') ?></option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label for="logo" class="form-label fw-bold text-muted">
                                <?= lang('Common.sponsor_logo') ?>
                            </label>
                            <input type="file" name="logo" id="logo" accept="image/*"
                                   class="form-control"
                                   style="border-radius: 8px; padding: 10px 15px;">
                            <?php if (!empty($data['logo'])): ?>
                                <div class="mt-2">
                                    <img src="<?= base_url($data['logo']) ?>" alt="Logo" style="max-width: 150px; height: auto; border-radius: 8px;">
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/sponsors') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                            <i class="fas fa-times me-2"></i> <?= lang('Common.cancel') ?>
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold" style="background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                            <i class="fas fa-save me-2"></i> <?= lang('Common.save') ?>
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection() ?>
