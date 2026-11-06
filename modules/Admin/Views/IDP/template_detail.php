<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<!-- Template Info Header -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-clipboard-list text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.idp_admin_template_detail') ?></h4>
                    <p class="text-muted mb-0 small"><?= isset($template['title']) ? $template['title'] : '' ?></p>
                </div>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('admin/idp/template/manage/' . $template['id']) ?>" class="btn btn-warning text-dark rounded-pill px-4 py-2 fw-bold">
                    <i class="fas fa-pencil-alt me-2"></i> <?= lang('Common.edit') ?>
                </a>
                <a href="<?= base_url('admin/idp/templates') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                    <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Template Details Card -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="row">
            <div class="col-md-6">
                <p class="mb-2"><strong><?= lang('Common.idp_admin_title_label') ?>:</strong> <?= isset($template['title']) ? $template['title'] : '-' ?></p>
                <p class="mb-2"><strong><?= lang('Common.description') ?>:</strong> <?= isset($template['description']) ? $template['description'] : '-' ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-2">
                    <strong><?= lang('Common.idp_admin_owner_type') ?>:</strong>
                    <?php if (isset($template['owner_type']) && $template['owner_type'] === 'global'): ?>
                        <span class="badge bg-primary"><?= lang('Common.idp_admin_owner_global') ?></span>
                    <?php else: ?>
                        <span class="badge bg-info"><?= lang('Common.idp_admin_owner_academy') ?></span>
                    <?php endif; ?>
                </p>
                <p class="mb-2">
                    <strong><?= lang('Common.status') ?>:</strong>
                    <?php if (isset($template['is_active']) && $template['is_active'] == 1): ?>
                        <span class="badge bg-success"><?= lang('Common.active') ?></span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><?= lang('Common.inactive') ?></span>
                    <?php endif; ?>
                </p>
                <p class="mb-2">
                    <strong><?= lang('Common.idp_admin_is_public') ?>:</strong>
                    <?php if (isset($template['is_public']) && $template['is_public'] == 1): ?>
                        <span class="badge bg-success"><i class="fa fa-check"></i></span>
                    <?php else: ?>
                        <span class="badge bg-secondary"><i class="fa fa-times"></i></span>
                    <?php endif; ?>
                </p>
                <p class="mb-2"><strong><?= lang('Common.idp_admin_dimension_count') ?>:</strong> <?= isset($template['dimension_count']) ? $template['dimension_count'] : 0 ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Dimensions List -->
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-bold mb-0" style="color: var(--secondary-color);">
                <i class="fas fa-layer-group me-2"></i><?= lang('Common.idp_admin_dimensions') ?>
            </h5>
            <a href="<?= base_url('admin/idp/dimension/manage/' . $template['id']) ?>" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                <i class="fas fa-plus me-2"></i>
                <?= lang('Common.idp_admin_dimension_create') ?>
            </a>
        </div>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (!empty($dimensions)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead style="background: #f8f9fa; color: #06346D;">
                        <tr>
                            <th class="py-3 ps-4 text-center" width="5%">#</th>
                            <th class="py-3 text-center" width="8%"><?= lang('Common.idp_admin_icon') ?></th>
                            <th class="py-3 text-start" width="20%"><?= lang('Common.idp_admin_name_th') ?></th>
                            <th class="py-3 text-start" width="20%"><?= lang('Common.idp_admin_name_en') ?></th>
                            <th class="py-3 text-center" width="8%"><?= lang('Common.idp_admin_sort_order') ?></th>
                            <th class="py-3 text-center" width="10%"><?= lang('Common.idp_admin_indicator_count') ?></th>
                            <th class="py-3 text-center" width="8%"><?= lang('Common.status') ?></th>
                            <th class="py-3 pe-4 text-center" width="21%"><?= lang('Common.action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dimensions as $i => $dim): ?>
                            <tr>
                                <td class="text-center"><?= $i + 1 ?></td>
                                <td class="text-center">
                                    <span style="color: <?= !empty($dim['color']) ? $dim['color'] : '#6c757d' ?>; font-size: 1.5rem;">
                                        <i class="fa <?= !empty($dim['icon']) ? $dim['icon'] : 'fa-circle' ?>"></i>
                                    </span>
                                </td>
                                <td><?= isset($dim['name_th']) ? $dim['name_th'] : '-' ?></td>
                                <td><?= isset($dim['name_en']) ? $dim['name_en'] : '-' ?></td>
                                <td class="text-center"><?= isset($dim['sort_order']) ? $dim['sort_order'] : 0 ?></td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill"><?= isset($dim['indicator_count']) ? $dim['indicator_count'] : 0 ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if (isset($dim['is_active']) && $dim['is_active'] == 1): ?>
                                        <span class="badge bg-success"><?= lang('Common.active') ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary"><?= lang('Common.inactive') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('admin/idp/indicators/' . $dim['id']) ?>" class="btn btn-info btn-sm me-1">
                                        <i class="fa fa-list"></i> <?= lang('Common.idp_admin_view_indicators') ?>
                                    </a>
                                    <a href="<?= base_url('admin/idp/dimension/manage/' . $template['id'] . '/' . $dim['id']) ?>" class="btn btn-warning text-dark btn-sm me-1">
                                        <i class="fa fa-pencil"></i> <?= lang('Common.edit') ?>
                                    </a>
                                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteDimension(<?= $dim['id'] ?>)">
                                        <i class="fa fa-trash"></i> <?= lang('Common.delete') ?>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-layer-group fa-3x mb-3 opacity-50"></i>
                <p class="mb-0"><?= lang('Common.no_data') ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    function deleteDimension(id) {
        Swal.fire({
            title: '<?= lang('Common.idp_admin_confirm_delete_dimension') ?>',
            text: '<?= lang('Common.idp_admin_delete_warning') ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang('Common.confirm') ?>',
            cancelButtonText: '<?= lang('Common.cancel') ?>'
        }).then(function(result) {
            if (result.isConfirmed) {
                $.ajax({
                    method: 'POST',
                    url: '<?= base_url('admin/idp/dimension/delete') ?>',
                    data: { id: id },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: '<?= lang('Common.idp_admin_deleted_success') ?>',
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: '<?= lang('Common.error') ?>',
                                text: res.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: '<?= lang('Common.error') ?>',
                            text: '<?= lang('Common.save_failed') ?>'
                        });
                    }
                });
            }
        });
    }
</script>
<?php $this->endSection(); ?>
