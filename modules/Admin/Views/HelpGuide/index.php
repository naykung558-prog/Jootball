<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3"
                     style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-book-open text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                        <?= lang('Common.help_guide_manager') ?>
                    </h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.help_guide_manager_desc') ?></p>
                </div>
            </div>

            <div>
                <a href="<?= base_url('admin/help-guide/manage') ?>"
                   class="btn btn-primary px-4 py-2 fw-bold"
                   style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                    <i class="ti ti-plus me-2"></i>
                    <?= lang('Common.add_help_guide') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="table-responsive">
            <table id="table" class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="5%">#</th>
                        <th class="py-3 text-start" width="25%"><?= lang('Common.title') ?> (TH)</th>
                        <th class="py-3 text-start" width="22%"><?= lang('Common.title') ?> (EN)</th>
                        <th class="py-3 text-start" width="25%"><?= lang('Common.link') ?></th>
                        <th class="py-3 text-center" width="8%"><?= lang('Common.sort_order') ?></th>
                        <th class="py-3 text-center" width="8%"><?= lang('Common.status') ?></th>
                        <th class="py-3 pe-4 text-center" width="12%"><?= lang('Common.action') ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>
<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
<script>
    const langConfirmDeleteTitle = '<?= lang("Common.confirm_delete_title") ?>';
    const langCannotRecoverData  = '<?= lang("Common.cannot_recover_data") ?>';
    const langYesDelete          = '<?= lang("Common.yes_delete") ?>';
    const langCancel             = '<?= lang("Common.cancel") ?>';
    const langDeleteSuccess      = '<?= lang("Common.delete_success") ?>';
    const langDataDeletedSuccess = '<?= lang("Common.data_deleted_success") ?>';
    const langError              = '<?= lang("Common.error") ?>';
    const langCannotDeleteData   = '<?= lang("Common.delete_error") ?>';
    const langStatusUpdated      = '<?= lang("Common.status_updated") ?>';
    const langCannotUpdateStatus = '<?= lang("Common.status_update_error") ?>';

    var table;
    $(document).ready(function () {
        table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('admin/help-guide/ajax-list') ?>',
                method: 'GET'
            },
            order: [[4, 'asc']],
            columnDefs: [{ targets: [0, 5, 6], orderable: false }],
            columns: [
                { data: '',             className: 'text-center' },
                { data: 'title_th',     className: 'text-start' },
                { data: 'title_en',     className: 'text-start' },
                { data: 'link',         className: 'text-start' },
                { data: 'sort_order',   className: 'text-center' },
                {
                    data: 'is_active_raw',
                    className: 'text-center',
                    render: function (data, type, row) {
                        if (type === 'display') {
                            var checked = (data == '1' || data == 1) ? 'checked' : '';
                            return '<div class="form-check form-switch d-flex justify-content-center">'
                                 + '<input class="form-check-input" type="checkbox" role="switch" '
                                 + checked + ' onchange="toggleStatus(' + row.id + ', this)"></div>';
                        }
                        return data;
                    }
                },
                { data: 'action', className: 'text-center' }
            ]
        });
    });

    function toggleStatus(id, element) {
        var isActive = element.checked ? '1' : '0';
        $.ajax({
            method: 'POST',
            url: '<?= base_url('admin/help-guide/toggle-status') ?>',
            data: { id: id, is_active: isActive },
            dataType: 'json',
            success: function (response) {
                if (response.status === 'success') {
                    Swal.fire({ icon: 'success', title: langStatusUpdated, showConfirmButton: false, timer: 1500 });
                } else {
                    element.checked = !element.checked;
                    Swal.fire({ icon: 'error', title: langError, text: response.message || langCannotUpdateStatus });
                }
            },
            error: function () {
                element.checked = !element.checked;
                Swal.fire({ icon: 'error', title: langError, text: langCannotUpdateStatus });
            }
        });
    }

    function deleteHelpGuide(id) {
        Swal.fire({
            title: langConfirmDeleteTitle,
            text: langCannotRecoverData,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langYesDelete,
            cancelButtonText: langCancel,
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: 'POST',
                    url: '<?= base_url('admin/help-guide/delete') ?>',
                    data: { id: id },
                    dataType: 'json',
                    success: function () {
                        Swal.fire({
                            icon: 'success',
                            title: langDeleteSuccess,
                            text: langDataDeletedSuccess,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => table.ajax.reload());
                    },
                    error: function () {
                        Swal.fire({ icon: 'error', title: langError, text: langCannotDeleteData });
                    }
                });
            }
        });
    }
</script>
<?php $this->endSection() ?>
