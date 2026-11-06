<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-file-alt me-2 text-primary"></i><?= esc($template['title'] ?? '') ?>
            </h4>
            <?php if (!empty($template['description'])): ?>
                <p class="text-muted mb-0"><?= esc($template['description']) ?></p>
            <?php endif; ?>
            <div class="mt-1">
                <?php if ($template['owner_type'] === 'global'): ?>
                    <span class="badge bg-info rounded-pill"><?= lang('Common.idp_global_badge') ?></span>
                <?php else: ?>
                    <span class="badge bg-primary rounded-pill"><?= lang('Common.idp_academy_badge') ?></span>
                <?php endif; ?>
                <?php if ((int)$template['is_active']): ?>
                    <span class="badge bg-success rounded-pill"><?= lang('Common.active') ?></span>
                <?php else: ?>
                    <span class="badge bg-secondary rounded-pill"><?= lang('Common.inactive') ?></span>
                <?php endif; ?>
            </div>
        </div>
        <div class="mt-2 mt-md-0 d-flex gap-2">
            <a href="<?= base_url('academy/idp/library/' . $academy['id']) ?>"
               class="btn btn-outline-secondary rounded-pill px-3">
                <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
            </a>
            <?php if (!empty($perms['perm_manage_idp_template']) && !empty($isOwner)): ?>
            <a href="<?= base_url('academy/idp/dimension/manage/' . $academy['id'] . '/' . $template['id']) ?>"
               class="btn btn-primary rounded-pill px-4">
                <i class="fas fa-plus me-1"></i><?= lang('Common.idp_dimension_create') ?>
            </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Dimensions List -->
    <?php if (empty($dimensions)): ?>
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body text-center text-muted py-5">
                <i class="fas fa-cubes fa-3x mb-3 opacity-25"></i>
                <p><?= lang('Common.no_data') ?></p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($dimensions as $dim): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-3" id="dimension-card-<?= $dim['id'] ?>">
            <div class="card-header bg-white border-0 py-3 px-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <span class="d-inline-flex align-items-center justify-content-center rounded-circle me-3"
                              style="width: 40px; height: 40px; background: <?= esc($dim['color'] ?? '#0d6efd') ?>20;">
                            <i class="fas <?= esc($dim['icon'] ?? 'fa-circle') ?>" style="color: <?= esc($dim['color'] ?? '#0d6efd') ?>;"></i>
                        </span>
                        <div>
                            <h6 class="fw-bold mb-0"><?= esc($dim['name']) ?></h6>
                            <small class="text-muted">
                                <?= esc($dim['name_th']) ?> / <?= esc($dim['name_en']) ?>
                                &mdash; <?= (int)$dim['indicator_count'] ?> <?= lang('Common.idp_indicator_count') ?>
                            </small>
                        </div>
                    </div>
                    <?php if (!empty($perms['perm_manage_idp_template']) && !empty($isOwner)): ?>
                    <div class="d-flex gap-2">
                        <a href="<?= base_url('academy/idp/indicator/manage/' . $academy['id'] . '/' . $dim['id']) ?>"
                           class="btn btn-sm btn-outline-success" title="<?= lang('Common.idp_indicator_create') ?>">
                            <i class="fas fa-plus me-1"></i><?= lang('Common.idp_indicator_create') ?>
                        </a>
                        <a href="<?= base_url('academy/idp/dimension/manage/' . $academy['id'] . '/' . $template['id'] . '/' . $dim['id']) ?>"
                           class="btn btn-sm btn-outline-warning" title="<?= lang('Common.edit') ?>">
                            <i class="fas fa-edit"></i>
                        </a>
                        <button type="button" class="btn btn-sm btn-outline-danger btn-delete-dimension"
                                data-dimension-id="<?= $dim['id'] ?>" title="<?= lang('Common.delete') ?>">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if (!empty($dim['indicators'])): ?>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4" style="width: 40px;">#</th>
                                <th><?= lang('Common.idp_name_th') ?></th>
                                <th><?= lang('Common.idp_name_en') ?></th>
                                <th><?= lang('Common.idp_measurement_type') ?></th>
                                <th><?= lang('Common.idp_unit') ?></th>
                                <th class="text-center"><?= lang('Common.idp_min_value') ?></th>
                                <th class="text-center"><?= lang('Common.idp_max_value') ?></th>
                                <?php if (!empty($perms['perm_manage_idp_template']) && !empty($isOwner)): ?>
                                <th class="text-end pe-4"><?= lang('Common.action') ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($dim['indicators'] as $idx => $ind): ?>
                            <tr id="indicator-row-<?= $ind['id'] ?>">
                                <td class="ps-4"><?= $idx + 1 ?></td>
                                <td><?= esc($ind['name_th']) ?></td>
                                <td><?= esc($ind['name_en']) ?></td>
                                <td><span class="badge bg-light text-dark"><?= esc($ind['measurement_type']) ?></span></td>
                                <td><?= esc($ind['unit'] ?? '-') ?></td>
                                <td class="text-center"><?= esc($ind['min_value']) ?></td>
                                <td class="text-center"><?= esc($ind['max_value']) ?></td>
                                <?php if (!empty($perms['perm_manage_idp_template']) && !empty($isOwner)): ?>
                                <td class="text-end pe-4">
                                    <a href="<?= base_url('academy/idp/indicator/manage/' . $academy['id'] . '/' . $dim['id'] . '/' . $ind['id']) ?>"
                                       class="btn btn-sm btn-outline-warning" title="<?= lang('Common.edit') ?>">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-delete-indicator"
                                            data-indicator-id="<?= $ind['id'] ?>" title="<?= lang('Common.delete') ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<style>
.hover-shadow { transition: box-shadow 0.2s ease; }
.hover-shadow:hover { box-shadow: 0 .5rem 1rem rgba(0,0,0,.1) !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var academyId = '<?= $academy['id'] ?>';

    // Delete dimension
    document.querySelectorAll('.btn-delete-dimension').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var dimensionId = this.getAttribute('data-dimension-id');

            Swal.fire({
                title: '<?= lang('Common.idp_dimension_delete_confirm') ?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: '<?= lang('Common.delete') ?>',
                cancelButtonText: '<?= lang('Common.cancel') ?>'
            }).then(function(result) {
                if (result.isConfirmed) {
                    fetch('<?= base_url('academy/idp/dimension/delete') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            academy_id: academyId,
                            dimension_id: dimensionId
                        })
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.status === 'success') {
                            var card = document.getElementById('dimension-card-' + dimensionId);
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

    // Delete indicator
    document.querySelectorAll('.btn-delete-indicator').forEach(function(btn) {
        btn.addEventListener('click', function() {
            var indicatorId = this.getAttribute('data-indicator-id');

            Swal.fire({
                title: '<?= lang('Common.idp_indicator_delete_confirm') ?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                confirmButtonText: '<?= lang('Common.delete') ?>',
                cancelButtonText: '<?= lang('Common.cancel') ?>'
            }).then(function(result) {
                if (result.isConfirmed) {
                    fetch('<?= base_url('academy/idp/indicator/delete') ?>', {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({
                            academy_id: academyId,
                            indicator_id: indicatorId
                        })
                    })
                    .then(function(res) { return res.json(); })
                    .then(function(data) {
                        if (data.status === 'success') {
                            var row = document.getElementById('indicator-row-' + indicatorId);
                            if (row) row.remove();
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
