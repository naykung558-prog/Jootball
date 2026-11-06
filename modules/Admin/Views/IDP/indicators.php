<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-chart-bar text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                        <?= lang('Common.idp_admin_indicators') ?>
                        <?php if (!empty($dimension)): ?>
                            <?php
                                $lang = function_exists('get_current_language') ? get_current_language() : 'th';
                                $dimName = ($lang === 'en' && !empty($dimension['name_en'])) ? $dimension['name_en'] : $dimension['name_th'];
                            ?>
                            - <?= $dimName ?>
                        <?php endif; ?>
                    </h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.idp_admin_indicators_desc') ?></p>
                    <?php if (!empty($dimension) && !empty($dimension['template_title'])): ?>
                        <p class="text-muted mb-0 small">
                            <?= lang('Common.idp_admin_templates') ?>: <strong><?= $dimension['template_title'] ?></strong>
                        </p>
                    <?php endif; ?>
                </div>
            </div>

            <div class="d-flex gap-2">
                <?php if (!empty($dimension_id)): ?>
                    <a href="<?= base_url('admin/idp/indicator/manage/' . $dimension_id) ?>" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                        <i class="fas fa-plus me-2"></i>
                        <?= lang('Common.idp_admin_indicator_create') ?>
                    </a>
                <?php endif; ?>
                <?php if (!empty($dimension) && !empty($dimension['template_id'])): ?>
                    <a href="<?= base_url('admin/idp/template/detail/' . $dimension['template_id']) ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                        <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
                    </a>
                <?php else: ?>
                    <a href="<?= base_url('admin/idp/templates') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                        <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.idp_admin_all_templates') ?>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-0">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert" style="border-radius: var(--border-radius);">
                <strong><i class="fas fa-exclamation-circle me-1"></i></strong> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show m-3" role="alert" style="border-radius: var(--border-radius);">
                <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="table" class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="5%" style="border-radius: var(--border-radius) 0 0 0;">#</th>
                        <th class="py-3 text-start" width="18%"><?= lang('Common.idp_admin_name_th') ?></th>
                        <th class="py-3 text-start" width="18%"><?= lang('Common.idp_admin_name_en') ?></th>
                        <th class="py-3 text-start" width="12%"><?= lang('Common.idp_admin_dimensions') ?></th>
                        <th class="py-3 text-center" width="10%"><?= lang('Common.idp_admin_measurement_type') ?></th>
                        <th class="py-3 text-center" width="7%"><?= lang('Common.idp_admin_unit') ?></th>
                        <th class="py-3 text-center" width="8%"><?= lang('Common.idp_admin_min_value') ?> / <?= lang('Common.idp_admin_max_value') ?></th>
                        <th class="py-3 text-center" width="6%"><?= lang('Common.status') ?></th>
                        <th class="py-3 pe-4 text-center" width="16%" style="border-radius: 0 var(--border-radius) 0 0;"><?= lang('Common.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    var table;
    $(document).ready(function() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('admin/idp/indicators/data') ?>',
                method: "POST",
                data: function(d) {
                    d.dimension_id = '<?= isset($dimension_id) ? $dimension_id : '' ?>';
                }
            },
            order: [[ 0, 'asc' ]],
            columnDefs: [{
                targets: [0, 4, 5, 6, 7, 8],
                orderable: false
            }],
            columns: [
                {
                    data: '',
                    className: 'text-center'
                },
                {
                    data: 'name_th',
                    className: 'text-start'
                },
                {
                    data: 'name_en',
                    className: 'text-start'
                },
                {
                    data: 'dimension_name',
                    className: 'text-start'
                },
                {
                    data: 'measurement_label',
                    className: 'text-center'
                },
                {
                    data: 'unit',
                    className: 'text-center'
                },
                {
                    data: 'range_display',
                    className: 'text-center'
                },
                {
                    data: 'status',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center'
                }
            ]
        });
    });

    function deleteIndicator(id) {
        Swal.fire({
            title: '<?= lang('Common.idp_admin_confirm_delete_indicator') ?>',
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
                    url: '<?= base_url('admin/idp/indicator/delete') ?>',
                    data: { id: id },
                    success: function(res) {
                        if (res.success) {
                            Swal.fire({
                                position: 'center',
                                icon: 'success',
                                title: '<?= lang('Common.idp_admin_deleted_success') ?>',
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload();
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
