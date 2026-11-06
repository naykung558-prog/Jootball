<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="ti ti-users text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.all_teams') ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.manage_team_data_in_system') ?></p>
                </div>
            </div>

            <div>
                <a href="<?= base_url('admin/team/manage') ?>" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                    <i class="ti ti-plus me-2"></i>
                    <?= lang('Common.add_team') ?>
                </a>
            </div>
        </div>
        <div class="mt-3">
            <div class="alert alert-light border-0 shadow-sm d-flex align-items-center" role="alert" style="border-radius: var(--border-radius); background: rgba(220, 53, 69, 0.05); color: var(--danger-color);">
                <i class="ti ti-alert-circle me-2 fs-5"></i>
                <div>
                    <strong><?= lang('Common.note') ?>:</strong> <?= lang('Common.admin_add_team_note') ?>
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
                        <th class="py-3 text-center" width="8%"><?= lang('Common.team_logo') ?></th>
                        <th class="py-3 text-start" width="30%"><?= lang('Common.team_name') ?></th>
                        <th class="py-3 text-start" width="25%">Academy</th>
                        <th class="py-3 pe-4 text-center" style="border-radius: 0 var(--border-radius) 0 0;"><?= lang('Common.tools') ?></th>
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
    const langConfirmDeleteTeam = '<?= lang("Common.confirm_delete_team") ?>';
    const langConfirmDeleteText = '<?= lang("Common.confirm_delete_press_confirm") ?>';
    const langConfirmBtn = '<?= lang("Common.confirm") ?>';
    const langCancelBtn = '<?= lang("Common.cancel") ?>';
    const langDeleteSuccess = '<?= lang("Common.delete_data_success") ?>';

    var table;
    $(document).ready(function() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('admin/team/get-teams') ?>',
                method: "POST"
            },
            order: [[ 5, 'desc' ]],
            columnDefs: [{
                targets: [0, 1, 3, 4],
                orderable: false
            }, {
                targets: [5, 6],
                visible: false
            }],
            columns: [
                { data: '',              className: 'text-center' },
                { data: 'image_logo',    className: 'text-center' },
                { data: 'team_name',     className: 'text-start', orderData: [6] },
                { data: 'academy_display', className: 'text-start' },
                { data: 'action',        className: 'text-center' },
                { data: 'id',            className: 'text-center' },
                { data: 'name',          className: 'text-center' }
            ]

        });
    })

    function deletedTeam(team_id) {
        Swal.fire({
            title: langConfirmDeleteTeam,
            text: langConfirmDeleteText,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#16478d",
            cancelButtonColor: "#d33",
            confirmButtonText: langConfirmBtn,
            cancelButtonText: langCancelBtn
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: '<?= base_url('admin/team/delete-team') ?>',
                    data: {
                        team_id: team_id
                    },
                    success: function(res) {
                        console.log(res);
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: langDeleteSuccess,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload();
                    }
                });
            }
        });
    }
</script>
<?php $this->endSection(); ?>