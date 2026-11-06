<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-map-marker-alt text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.admin_stadium') ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.manage_stadium_desc') ?></p>
                </div>
            </div>

            <div>
                <a href="<?= base_url('admin/stadium/manage') ?>" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                    <i class="ti ti-plus me-2"></i>
                    <?= lang('Common.add_stadium') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="table" class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="5%" style="border-radius: var(--border-radius) 0 0 0;">#</th>
                        <th class="py-3 text-center" width="10%"><?= lang('Common.image') ?></th>
                        <th class="py-3 text-start" width="25%"><?= lang('Common.stadium_name') ?></th>
                        <th class="py-3 text-start" width="30%"><?= lang('Common.stadium_address') ?></th>
                        <th class="py-3 text-center" width="10%"><?= lang('Common.status') ?></th>
                        <th class="py-3 pe-4 text-center" width="20%" style="border-radius: 0 var(--border-radius) 0 0;"><?= lang('Common.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
<script>
    const langConfirmDeleteTitle = '<?= lang("Common.confirm_delete_title") ?>';
    const langCannotRecoverData = '<?= lang("Common.cannot_recover_data") ?>';
    const langYesDelete = '<?= lang("Common.yes_delete") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';
    const langDeleteSuccess = '<?= lang("Common.delete_success") ?>';
    const langDataDeletedSuccess = '<?= lang("Common.data_deleted_success") ?>';
    const langError = '<?= lang("Common.error") ?>';
    const langCannotDeleteData = '<?= lang("Common.delete_error") ?>';

    var table;
    $(document).ready(function() {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('admin/stadium/get-stadiums') ?>',
                method: "GET"
            },
            columnDefs: [{
                targets: [0, 1, 4, 5],
                orderable: false
            }],
            columns: [{
                    data: '',
                    className: 'text-center'
                },
                {
                    data: 'cover_image',
                    className: 'text-center'
                },
                {
                    data: 'name',
                    className: 'text-start fw-bold'
                },
                {
                    data: 'address',
                    className: 'text-start text-muted'
                },
                {
                    data: 'is_active',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center'
                }
            ],
        });
    });

    function deleteStadium(id) {
        Swal.fire({
            title: langConfirmDeleteTitle,
            text: langCannotRecoverData,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#16478d",
            cancelButtonColor: "#d33",
            confirmButtonText: langYesDelete,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('admin/stadium/delete') ?>",
                    data: {
                        id: id,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    success: function() {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: langDeleteSuccess,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        table.ajax.reload();
                    },
                    error: function() {
                        Swal.fire(langError, langCannotDeleteData, "error");
                    }
                });
            }
        });
    }
</script>
<?php $this->endSection() ?>
