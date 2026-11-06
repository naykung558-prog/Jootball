<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="ti ti-user-plus text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.admin_manage_join_requests') ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.approve_or_reject_requests') ?></p>
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
                        <th class="py-3 text-start" width="25%"><?= lang('Common.requester') ?></th>
                        <th class="py-3 text-start" width="25%"><?= lang('Common.team_requested_to_join') ?></th>
                        <th class="py-3 text-center" width="15%"><?= lang('Common.request_date') ?></th>
                        <th class="py-3 pe-4 text-center" width="20%" style="border-radius: 0 var(--border-radius) 0 0;"><?= lang('Common.manage') ?></th>
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
    const langApproveRequest = '<?= lang("Common.approve_this_request") ?>';
    const langApproveRequestText = '<?= lang("Common.user_can_manage_team_after_approve") ?>';
    const langApproveBtn = '<?= lang("Common.approve") ?>';
    const langCancelBtn = '<?= lang("Common.cancel") ?>';
    const langRejectRequest = '<?= lang("Common.reject_this_request") ?>';
    const langRejectRequestText = '<?= lang("Common.request_will_be_rejected") ?>';
    const langRejectBtn = '<?= lang("Common.reject") ?>';
    const langErrorOccurred = '<?= lang("Common.error_occurred") ?>';
    const langCannotProcess = '<?= lang("Common.cannot_process") ?>';

    var table;
    $(document).ready(function() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('admin/team-requests/get-requests') ?>',
                method: "POST"
            },
            order: [[ 6, 'desc' ]],
            columnDefs: [{
                targets: [0, 4],
                orderable: false
            }, {
                targets: [5, 6, 7],
                visible: false
            }],
            columns: [{
                    data: '',
                    className: 'text-center'
                },
                {
                    data: 'user_info',
                    className: 'text-start',
                    orderData: [5]
                },
                {
                    data: 'team_info',
                    className: 'text-start',
                    orderData: [6]
                },
                {
                    data: 'request_date',
                    className: 'text-center',
                    orderData: [7]
                },
                {
                    data: 'action',
                    className: 'text-center'
                },
                {
                    data: 'user_name',
                    className: 'text-center'
                },
                {
                    data: 'team_name',
                    className: 'text-center'
                },
                {
                    data: 'created_at',
                    className: 'text-center'
                }
            ]
        });
    });

    function approveRequest(id) {
        Swal.fire({
            title: langApproveRequest,
            text: langApproveRequestText,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: langApproveBtn,
            cancelButtonText: langCancelBtn
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: '<?= base_url('admin/team-requests/approve') ?>/' + id,
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

    function rejectRequest(id) {
        Swal.fire({
            title: langRejectRequest,
            text: langRejectRequestText,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: langRejectBtn,
            cancelButtonText: langCancelBtn
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: '<?= base_url('admin/team-requests/reject') ?>/' + id,
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
