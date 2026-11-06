<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="ti ti-user-star text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.admin_coach_manager') ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.manage_coach_and_staff_desc') ?></p>
                </div>
            </div>

            <div>
                <a href="<?= base_url('/admin/coach/coach-manage/') ?>" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                    <i class="ti ti-user-plus me-2"></i>
                    <?= lang('Common.add_coach_manager') ?>
                </a>
            </div>
        </div>
    </div>
</div>

<style>
/* ย่อขนาดให้พอดีกับจำนวนคอลัมน์ ไม่ให้แถวสูงเกินจำเป็น (ชุดเดียวกับหน้า admin/users) */
#table { font-size: .8125rem; }
#table thead th { font-size: .8125rem; font-weight: 600; white-space: nowrap; }
#table tbody td { padding-top: .5rem; padding-bottom: .5rem; vertical-align: middle; }
#table .badge { font-size: .6875rem; font-weight: 600; }
/* ชื่อทีมยาวมาก ตัดบรรทัดแทนที่จะดันคอลัมน์อื่น */
#table td:nth-child(4) { line-height: 1.35; word-break: break-word; }
</style>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="table" class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="4%" style="border-radius: var(--border-radius) 0 0 0;">#</th>
                        <th class="py-3 text-center" width="6%"><?= lang('Common.um_profile_image') ?></th>
                        <th class="py-3 text-start" width="28%"><?= lang('Common.full_name') ?></th>
                        <th class="py-3 text-start" width="34%"><?= lang('Common.teams') ?></th>
                        <th class="py-3 text-center" width="12%"><?= lang('Common.status') ?></th>
                        <th class="py-3 pe-4 text-center" width="16%" style="border-radius: 0 var(--border-radius) 0 0;"><?= lang('Common.action') ?></th>
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

    $(document).ready(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('admin/coach/get-coachs') ?>',
                method: "GET"
            },
            columnDefs: [
                { targets: [0, 1, 3, 5], orderable: false }
            ],
            columns: [{
                    data: '',
                    className: 'text-center'
                },
                {
                    data: 'profile',
                    className: 'text-center'
                },
                {
                    data: 'fullname',
                    className: 'text-start'
                },
                {
                    data: 'team_name',
                    className: 'text-start'
                },
                {
                    data: 'status_badge',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center'
                }
            ]
        });
    })


    function deleteCoach(tou_id) {
        Swal.fire({
            title: langConfirmDeleteTitle,
            text: langCannotRecoverData,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: langYesDelete,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('admin/coach/delete-coach') ?>",
                    data: {
                        id: tou_id
                    },
                    success: function() {
                        Swal.fire({
                            title: langDeleteSuccess,
                            text: langDataDeletedSuccess,
                            icon: "success",
                            timer: 1500,
                            showConfirmButton: false
                        }).then(() => {
                            $('#table').DataTable().ajax.reload();
                        });
                    },
                    error: function() {
                        Swal.fire(langError, langCannotDeleteData, "error");
                    }
                });
            }
        });
    }

    // เปิดจาก openTeamAccess() ใน partial ด้านล่าง แล้วรีเฟรชตารางเมื่อปิด
    $(document).on('hidden.bs.modal', '#accessGrantModal', function () {
        $('#table').DataTable().ajax.reload(null, false);
    });
</script>

<?= $this->include('Modules\Admin\Views\partials\access_grant_modal') ?>
<?= $this->include('Modules\Admin\Views\partials\list_popup') ?>
<?php $this->endSection() ?>
