<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-book me-2 text-primary"></i><?= lang('Common.idp_library_title') ?>
            </h4>
            <p class="text-muted mb-0"><?= lang('Common.idp_library_desc') ?> &mdash; <?= esc($academy['name'] ?? '') ?></p>
        </div>
        <?php if (!empty($perms['perm_manage_idp_template'])): ?>
        <div class="mt-2 mt-md-0">
            <a href="<?= base_url('academy/idp/' . $academy['id']) ?>"
               class="btn btn-outline-secondary rounded-pill px-3 me-2">
                <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
            </a>
            <a href="<?= base_url('academy/idp/template/manage/' . $academy['id']) ?>"
               class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-1"></i><?= lang('Common.idp_template_create') ?>
            </a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Global Templates -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-globe me-2 text-info"></i><?= lang('Common.idp_template_global') ?>
            </h5>
        </div>
        <div class="card-body p-4">
            <?php if (empty($templates['global'])): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                    <p><?= lang('Common.idp_no_templates') ?></p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($templates['global'] as $tpl): ?>
                    <div class="col-12 col-md-6 col-lg-4">
                        <div class="card border rounded-3 h-100 hover-shadow">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= esc($tpl['title']) ?></h6>
                                        <?php if (!empty($tpl['description'])): ?>
                                            <small class="text-muted"><?= esc(mb_substr($tpl['description'], 0, 80)) ?><?= mb_strlen($tpl['description']) > 80 ? '...' : '' ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <span class="badge bg-info rounded-pill"><?= lang('Common.idp_global_badge') ?></span>
                                </div>
                                <div class="d-flex gap-3 mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-cubes me-1"></i><?= (int)$tpl['dimension_count'] ?> <?= lang('Common.idp_dimension_count') ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-ruler me-1"></i><?= (int)$tpl['indicator_count'] ?> <?= lang('Common.idp_indicator_count') ?>
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('academy/idp/template/detail/' . $academy['id'] . '/' . $tpl['id']) ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i><?= lang('Common.view') ?>
                                    </a>
                                    <?php if (!empty($perms['perm_manage_idp_template'])): ?>
                                    <button type="button" class="btn btn-sm btn-outline-success btn-clone"
                                            data-template-id="<?= $tpl['id'] ?>"
                                            data-template-title="<?= esc($tpl['title']) ?>">
                                        <i class="fas fa-copy me-1"></i><?= lang('Common.idp_template_clone') ?>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Academy Templates -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold">
                <i class="fas fa-building me-2 text-primary"></i><?= lang('Common.idp_template_academy') ?>
            </h5>
        </div>
        <div class="card-body p-4">
            <?php if (empty($templates['academy'])): ?>
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                    <p><?= lang('Common.idp_no_templates') ?></p>
                </div>
            <?php else: ?>
                <div class="row g-3">
                    <?php foreach ($templates['academy'] as $tpl): ?>
                    <div class="col-12 col-md-6 col-lg-4" id="template-card-<?= $tpl['id'] ?>">
                        <div class="card border rounded-3 h-100 hover-shadow">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-start justify-content-between mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-1"><?= esc($tpl['title']) ?></h6>
                                        <?php if (!empty($tpl['description'])): ?>
                                            <small class="text-muted"><?= esc(mb_substr($tpl['description'], 0, 80)) ?><?= mb_strlen($tpl['description']) > 80 ? '...' : '' ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <span class="badge bg-primary rounded-pill"><?= lang('Common.idp_academy_badge') ?></span>
                                        <?php if (!(int)$tpl['is_active']): ?>
                                            <span class="badge bg-secondary rounded-pill"><?= lang('Common.inactive') ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div class="d-flex gap-3 mb-3">
                                    <small class="text-muted">
                                        <i class="fas fa-cubes me-1"></i><?= (int)$tpl['dimension_count'] ?> <?= lang('Common.idp_dimension_count') ?>
                                    </small>
                                    <small class="text-muted">
                                        <i class="fas fa-ruler me-1"></i><?= (int)$tpl['indicator_count'] ?> <?= lang('Common.idp_indicator_count') ?>
                                    </small>
                                </div>
                                <div class="d-flex gap-2">
                                    <a href="<?= base_url('academy/idp/template/detail/' . $academy['id'] . '/' . $tpl['id']) ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye me-1"></i><?= lang('Common.idp_template_detail') ?>
                                    </a>
                                    <?php if (!empty($perms['perm_manage_idp_template'])): ?>
                                    <a href="<?= base_url('academy/idp/template/manage/' . $academy['id'] . '/' . $tpl['id']) ?>"
                                       class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-edit me-1"></i><?= lang('Common.edit') ?>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-template"
                                            data-template-id="<?= $tpl['id'] ?>">
                                        <i class="fas fa-trash me-1"></i><?= lang('Common.delete') ?>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.hover-shadow { transition: box-shadow 0.2s ease; }
.hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var academyId = '<?= $academy['id'] ?>';

    // Clone template
    document.querySelectorAll('.btn-clone').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var templateId = this.getAttribute('data-template-id');
            var templateTitle = this.getAttribute('data-template-title');

            Swal.fire({
                title: '<?= lang('Common.idp_template_clone') ?>',
                input: 'text',
                inputLabel: '<?= lang('Common.idp_template_clone_title') ?>',
                inputValue: templateTitle + ' (Copy)',
                showCancelButton: true,
                confirmButtonText: '<?= lang('Common.confirm') ?>',
                cancelButtonText: '<?= lang('Common.cancel') ?>',
                inputValidator: function(value) {
                    if (!value) {
                        return '<?= lang('Common.fill_required') ?>';
                    }
                }
            }).then(function(result) {
                if (result.isConfirmed) {
                    fetch('<?= base_url('academy/idp/template/clone') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            academy_id: academyId,
                            template_id: templateId,
                            title: result.value
                        })
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: '<?= lang('Common.success') ?>',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            }).then(function() {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire('<?= lang('Common.error') ?>', data.message, 'error');
                        }
                    });
                }
            });
        });
    });

    // Delete template
    document.querySelectorAll('.btn-delete-template').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var templateId = this.getAttribute('data-template-id');

            Swal.fire({
                title: '<?= lang('Common.idp_template_delete_confirm') ?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: '<?= lang('Common.delete') ?>',
                cancelButtonText: '<?= lang('Common.cancel') ?>'
            }).then(function(result) {
                if (result.isConfirmed) {
                    fetch('<?= base_url('academy/idp/template/delete') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            academy_id: academyId,
                            template_id: templateId
                        })
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.status === 'success') {
                            var card = document.getElementById('template-card-' + templateId);
                            if (card) card.remove();
                            Swal.fire({
                                icon: 'success',
                                title: '<?= lang('Common.success') ?>',
                                text: data.message,
                                timer: 1500,
                                showConfirmButton: false
                            });
                        } else {
                            Swal.fire('<?= lang('Common.error') ?>', data.message, 'error');
                        }
                    });
                }
            });
        });
    });
});
</script>
<?php $this->endSection(); ?>
