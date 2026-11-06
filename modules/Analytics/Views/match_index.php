<?php $baseUrl = rtrim(base_url(), "/") . "/"; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="text-gray-800 mb-0"><i class="fas fa-futbol mr-2"></i>จัดการแมตช์</h4>
    <a href="<?= $baseUrl ?>admin/analytics/matches/manage" class="btn btn-primary">
        <i class="fas fa-plus mr-1"></i>เพิ่มแมตช์ใหม่
    </a>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show">
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<div class="card shadow mb-4">
    <div class="card-body">
        <div class="table-responsive">
            <table id="matchTable" class="table table-bordered" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>ชื่อแมตช์</th>
                        <th width="110">วันที่</th>
                        <th>คู่แข่ง / สกอร์</th>
                        <th width="80">วิดีโอ</th>
                        <th width="130">สถานะ</th>
                        <th width="120">จัดการ</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
$(document).ready(function () {
    var table = $('#matchTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= $baseUrl ?>admin/analytics/matches',
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
        },
        columns: [
            { data: 0, orderable: false },
            { data: 1 },
            { data: 2 },
            { data: 3 },
            { data: 4, orderable: false },
            { data: 5, orderable: false },
            { data: 6, orderable: false },
        ],
        language: dtLanguage,
        order: [[2, 'desc']],
        pageLength: 25,
    });

    // ลบแมตช์
    $(document).on('click', '.btn-delete', function () {
        var id = $(this).data('id');
        Swal.fire({
            title: 'ยืนยันการลบ?',
            text: 'ข้อมูลแมตช์และวิดีโอทั้งหมดจะถูกลบ ไม่สามารถกู้คืนได้',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#e74a3b',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ลบเลย',
            cancelButtonText: 'ยกเลิก',
        }).then(function (result) {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= $baseUrl ?>admin/analytics/matches/delete/' + id,
                    type: 'POST',
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
                    success: function (res) {
                        if (res.status === 'success') {
                            Swal.fire('ลบแล้ว!', res.message, 'success');
                            table.ajax.reload();
                        } else {
                            Swal.fire('ผิดพลาด', res.message, 'error');
                        }
                    },
                    error: function () {
                        Swal.fire('ผิดพลาด', 'ไม่สามารถลบได้', 'error');
                    }
                });
            }
        });
    });
});
</script>
