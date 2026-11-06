<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: linear-gradient(135deg, #dc3545, #c82333); box-shadow: 0 4px 15px rgba(220, 53, 69, 0.3);">
                    <i class="ti ti-search text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.admin_verify_team') ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.verify_and_delete_team_permanent') ?></p>
                </div>
            </div>
        </div>
        <div class="mt-3">
            <div class="alert alert-danger border-0 shadow-sm d-flex align-items-center" role="alert" style="border-radius: var(--border-radius);">
                <i class="ti ti-alert-triangle me-2 fs-5"></i>
                <div>
                    <strong><?= lang('Common.warning') ?>:</strong> <?= lang('Common.permanent_delete_warning') ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-0">
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show m-3" role="alert" style="border-radius: var(--border-radius);">
                <strong><?= lang('Common.error_message') ?></strong> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success m-3" role="alert" style="border-radius: var(--border-radius);">
                <?= session()->getFlashdata('success'); ?>
            </div>
        <?php endif; ?>

        <div class="table-responsive">
            <table id="table" class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="5%" style="border-radius: var(--border-radius) 0 0 0;"><?= lang('Common.order_number') ?></th>
                        <th class="py-3 text-center" width="15%"><?= lang('Common.team_emblem') ?></th>
                        <th class="py-3 text-start" width="50%"><?= lang('Common.team_name') ?></th>
                        <th class="py-3 pe-4 text-center" width="20%" style="border-radius: 0 var(--border-radius) 0 0;"><?= lang('Common.tools') ?></th>
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
    const langPermanentDeleteTeam = '<?= lang("Common.permanent_delete_team") ?>';
    const langConfirmPermanentDelete = '<?= lang("Common.confirm_permanent_delete_text") ?>';
    const langPlayerCoachDataKept = '<?= lang("Common.player_coach_data_kept") ?>';
    const langPermanentDeleteBtn = '<?= lang("Common.permanent_delete_btn") ?>';
    const langCancelBtn = '<?= lang("Common.cancel") ?>';
    const langErrorOccurred = '<?= lang("Common.error_occurred") ?>';
    const langCannotProcess = '<?= lang("Common.cannot_process") ?>';

    var table;
    $(document).ready(function() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('admin/team-verify/get-teams') ?>',
                method: "POST"
            },
            order: [[ 4, 'desc' ]],
            columnDefs: [{
                targets: [0, 1, 3],
                orderable: false
            }, {
                targets: [4, 5],
                visible: false
            }],
            columns: [{
                    data: '',
                    className: 'text-center'
                },
                {
                    data: 'image_logo',
                    className: 'text-center'
                },
                {
                    data: 'team_name',
                    className: 'text-start',
                    orderData: [5]
                },
                {
                    data: 'action',
                    className: 'text-center'
                },
                {
                    data: 'id',
                    className: 'text-center'
                },
                {
                    data: 'name',
                    className: 'text-center'
                }
            ]
        });
    });

    function permanentDeleteTeam(team_id, team_name) {
        Swal.fire({
            title: langPermanentDeleteTeam,
            html: `<div class="text-start">
                <p>${langConfirmPermanentDelete.replace('%s', team_name)}</p>
                <div class="alert alert-warning py-2 px-3">
                    <small><i class="fas fa-info-circle me-1"></i> ${langPlayerCoachDataKept}</small>
                </div>
            </div>`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: langPermanentDeleteBtn,
            cancelButtonText: langCancelBtn
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: '<?= base_url('admin/team-verify/permanent-delete') ?>',
                    data: {
                        team_id: team_id
                    },
                    success: function(res) {
                        if (res.status) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: res.message,
                                showConfirmButton: false,
                                timer: 1500
                            });
                            table.ajax.reload();
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: langErrorOccurred,
                                text: res.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: langErrorOccurred,
                            text: langCannotProcess
                        });
                    }
                });
            }
        });
    }
</script>
<?php $this->endSection(); ?>
