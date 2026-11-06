<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="card mb-2">
    <div class="px-4 py-3 border-bottom">

        <div class="d-flex justify-content-lg-between align-items-center flex-wrap justify-content-center">
            <div class="col-12 col-md-12 col-lg-9 text-center text-md-center text-lg-start">
                <h4 class="card-title mb-3 mb-lg-0">จัดการผู้ใช้งาน</h4>
            </div>

            <div class="col-12 col-md-12 col-lg-3 text-center text-md-center text-lg-end">
                <a href="<?= base_url("user/manage") ?>" class="btn btn-primary btn-sm">
                    <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-user-plus" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFF" fill="none" stroke-linecap="round" stroke-linejoin="round">
                        <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                        <path d="M8 7a4 4 0 1 0 8 0a4 4 0 0 0 -8 0" />
                        <path d="M16 19h6" />
                        <path d="M19 16v6" />
                        <path d="M6 21v-2a4 4 0 0 1 4 -4h4" />
                    </svg>
                    เพิ่มผู้ใช้งาน
                </a>
            </div>
        </div>
    </div>
</div>
<!-- <> -->
<div class="card rounded-3 w-100 position-relative overflow-hidden">
    <div class="card-body">
        <div class="table-responsive mb-4">
            <table id="table" class="table table-striped text-nowrap mb-0 align-middle table-bordered rounded text-dark" style="background-color: #FBF5F2;">
                <thead class=" fs-4" style="background-color: #444; color: white;">
                    <tr>
                        <th width="10%">ลำดับ</th>
                        <th width="30%">
                            ชื่อ
                        </th>
                        <th width="35%">
                            สาขา
                        </th>
                        <th width="15%">
                            สถานะ
                        </th>
                        <th>
                            เครื่องมือ
                        </th>
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
    $('#table').DataTable({
        processing: true,
        serverSide: true,
        language: dtLanguage,
        ajax: {
            url: '<?= base_url('user/users') ?>',
            method: "POST"
        },
        columnDefs: [{
                targets: 0,
                orderable: false
            },
            {
                targets: -1,
                orderable: false
            }
        ],
        columns: [{
                data: '',
                className: 'text-center'
            },
            {
                data: 'fullname',
                name: 'firstname'
            },
            {
                data: 'branch',
                name: 'branch_id',
            },
            {
                data: 'is_active'
            },
            {
                data: 'action',
                className: 'text-center'
            }
        ]
    });

    function deleteUser(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "You won't be able to revert this!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('user/delete') ?>",
                    data: {
                        id: id
                    },
                });

                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Your work has been saved",
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    window.location.reload();
                });
            }
        });
    }
</script>
<?php $this->endSection() ?>