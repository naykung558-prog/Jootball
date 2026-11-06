<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--navy-gradient); box-shadow: var(--box-shadow);">
                <i class="ti ti-users-group text-white fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.um_title') ?></h4>
                <p class="text-muted mb-0 small"><?= lang('Common.um_subtitle') ?></p>
            </div>
        </div>
    </div>
</div>

<style>
/* ย่อขนาดให้พอดีกับ 8 คอลัมน์ ไม่ให้แถวสูงเกินจำเป็น */
#usersTable { font-size: .8125rem; }
#usersTable thead th { font-size: .8125rem; font-weight: 600; white-space: nowrap; }
#usersTable tbody td { padding-top: .5rem; padding-bottom: .5rem; vertical-align: middle; }
#usersTable .badge { font-size: .6875rem; font-weight: 600; }
/* ชื่อ academy ยาวมาก ตัดบรรทัดแทนที่จะดันคอลัมน์อื่น */
#usersTable td:nth-child(6) { line-height: 1.35; word-break: break-word; }
#usersTable td:nth-child(6) .small { font-size: .75rem; }
</style>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="usersTable" class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="4%"><?= lang('Common.order_number') ?></th>
                        <th class="py-3 text-center" width="6%"><?= lang('Common.um_profile_image') ?></th>
                        <th class="py-3 text-start" width="21%"><?= lang('Common.um_name') ?></th>
                        <th class="py-3 text-center" width="12%"><?= lang('Common.um_login_type') ?></th>
                        <th class="py-3 text-center" width="5%"><?= lang('Common.um_teams') ?></th>
                        <th class="py-3 text-start" width="28%"><?= lang('Common.um_academies') ?></th>
                        <th class="py-3 text-center" width="10%"><?= lang('Common.um_registered_date') ?></th>
                        <th class="py-3 text-center" width="14%"><?= lang('Common.tools') ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langConfirmDelete = '<?= lang("Common.confirm_delete") ?>';
    const langConfirmDeleteText = '<?= lang("Common.confirm_delete_press_confirm") ?>';
    const langConfirmBtn = '<?= lang("Common.confirm") ?>';
    const langCancelBtn = '<?= lang("Common.cancel") ?>';
    const langDeleteSuccess = '<?= lang("Common.delete_data_success") ?>';
    const langError = '<?= lang("Common.error_occurred_msg") ?>';

    let usersTable;

    $(document).ready(function () {
        usersTable = $('#usersTable').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url("admin/users/get-users") ?>',
                method: "POST"
            },
            ordering: false,
            columns: [
                { data: '', className: 'text-center' },
                { data: 'profile', className: 'text-center' },
                { data: 'display_name', className: 'text-start' },
                { data: 'login_type', className: 'text-center' },
                { data: 'teams', className: 'text-center' },
                { data: 'academies', className: 'text-start' },
                { data: 'registered_date', className: 'text-center' },
                { data: 'action', className: 'text-center' }
            ]
        });
    });

    // เปิดจาก openAcademyAccess() ใน partial ด้านล่าง แล้วรีเฟรชตารางเมื่อปิด
    $(document).on('hidden.bs.modal', '#accessGrantModal', function () {
        usersTable.ajax.reload(null, false);
    });

    // popup รายการทั้งหมด (ค้นหาได้) อยู่ใน partials/list_popup ที่ include ท้ายไฟล์

    function deleteUser(id) {
        Swal.fire({
            title: langConfirmDelete,
            text: langConfirmDeleteText,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: langConfirmBtn,
            cancelButtonText: langCancelBtn
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: '<?= base_url("admin/users/delete-user") ?>',
                    data: { id: id },
                    dataType: 'json',
                    success: function (res) {
                        if (res.status) {
                            Swal.fire({ icon: 'success', title: langDeleteSuccess, showConfirmButton: false, timer: 1500 });
                            usersTable.ajax.reload();
                        } else {
                            Swal.fire({ icon: 'error', title: langError, text: res.message });
                        }
                    }
                });
            }
        });
    }
</script>

<?= $this->include('Modules\Admin\Views\partials\access_grant_modal') ?>
<?= $this->include('Modules\Admin\Views\partials\list_popup') ?>
<?php $this->endSection(); ?>
