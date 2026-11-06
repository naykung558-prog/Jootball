<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
                     style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-book-open text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                        <?php if (!empty($data['id'])): ?>
                            <?= lang('Common.edit') ?> <?= lang('Common.help_guide_item') ?>
                        <?php else: ?>
                            <?= lang('Common.add_help_guide') ?>
                        <?php endif; ?>
                    </h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.help_guide_manager_desc') ?></p>
                </div>
            </div>
            <a href="<?= base_url('admin/help-guide') ?>"
               class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <strong><i class="fas fa-exclamation-circle me-1"></i></strong>
                <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/help-guide/save') ?>" method="POST" class="once-only">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= !empty($data['id']) ? $data['id'] : '' ?>">

            <div class="row justify-content-center">
                <div class="col-md-10">

                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                        <?= lang('Common.help_guide_information') ?>
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">
                                <?= lang('Common.title') ?> (TH) <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="title_th"
                                   value="<?= !empty($data['title_th']) ? esc($data['title_th']) : '' ?>"
                                   class="form-control" required
                                   placeholder="<?= lang('Common.enter_title_th') ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">
                                <?= lang('Common.title') ?> (EN)
                            </label>
                            <input type="text" name="title_en"
                                   value="<?= !empty($data['title_en']) ? esc($data['title_en']) : '' ?>"
                                   class="form-control"
                                   placeholder="<?= lang('Common.enter_title_en') ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <div class="col-md-12">
                            <label class="form-label fw-bold text-muted">
                                <?= lang('Common.link') ?> <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="link"
                                   value="<?= !empty($data['link']) ? esc($data['link']) : '' ?>"
                                   class="form-control" required
                                   placeholder="https://example.com  <?= lang('Common.or') ?>  /auth"
                                   style="border-radius: 8px; padding: 10px 15px;">
                            <small class="text-muted"><?= lang('Common.help_guide_link_hint') ?></small>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">
                                <?= lang('Common.sort_order') ?>
                            </label>
                            <input type="number" name="sort_order" min="0"
                                   value="<?= isset($data['sort_order']) ? (int)$data['sort_order'] : 0 ?>"
                                   class="form-control"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">
                                <?= lang('Common.status') ?>
                            </label>
                            <select name="is_active" class="form-select" style="border-radius: 8px; padding: 10px 15px;">
                                <option value="1" <?= (!isset($data['is_active']) || $data['is_active'] == 1) ? 'selected' : '' ?>><?= lang('Common.active') ?></option>
                                <option value="0" <?= (isset($data['is_active']) && $data['is_active'] == 0) ? 'selected' : '' ?>><?= lang('Common.inactive') ?></option>
                            </select>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/help-guide') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                            <i class="fas fa-times me-2"></i> <?= lang('Common.cancel') ?>
                        </a>
                        <button type="submit" class="btn btn-primary rounded-pill px-4 py-2 fw-bold"
                                style="background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                            <i class="fas fa-save me-2"></i> <?= lang('Common.save') ?>
                        </button>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection() ?>

