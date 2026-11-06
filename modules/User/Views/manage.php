<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<form action="<?= base_url('user/save') ?>" method="POST" autocomplete="off" enctype="multipart/form-data">
    <!-- Hidden -->
    <input type="hidden" name="id" value="<?= @$data['id'] ?>">
    <!--  -->
    <div class="card">
        <div class="px-4 py-3 border-bottom">
            <h4 class="card-title mb-0">จัดการผู้ใช้งาน</h4>
        </div>
        <div class="card-body">
            <div class="row align-items-center mt-4 justify-content-center">
                <div class="col-lg-4 order-lg-1 order-2">

                </div>
                <div class="col-lg-4 col-12 col-md-4 mt-3">
                    <div class="mt-n5">
                        <div class="d-flex align-items-center justify-content-center mb-2">
                            <div class="d-flex align-items-center justify-content-center round-110">
                                <div class="border border-4 border-white d-flex align-items-center justify-content-center rounded-circle overflow-hidden round-100">
                                    <?php
                                    if (!empty($data['user_img'])) {
                                    ?>
                                        <img id="course_img" src="<?php echo base_url('public/files/user_img/' . $data['user_img']) ?>" alt="modernize-img" class="" style="height: 200px; width: 200px;">
                                    <?php
                                    } else {
                                    ?>
                                        <img id="course_img" src="<?php echo base_url('public/img/profile_2.png') ?>" alt="modernize-img" class="" style="height: 200px; width: 200px;">
                                    <?php
                                    }
                                    ?>

                                </div>
                            </div>
                        </div>
                        <!-- <div class="text-center mb-5">
                            <input type="file" name="user_img" id="" class="form-control">
                        </div> -->
                    </div>
                </div>
                <div class="col-lg-12 mb-3">
                    <div class="mt-3">
                        <div onclick="ClickInput()" style="border-radius: 0.375rem; border: 1px solid #6366F1; background-color: #F9FAFB; padding: 1rem; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); width: 9rem;" class="mx-auto">
                            <label for="upload" style="display: flex; flex-direction: column; align-items: center; gap: 0.5rem; cursor: pointer;">
                                <svg xmlns="http://www.w3.org/2000/svg" style="height: 2.5rem; width: 2.5rem; fill: white; stroke: #6366F1;" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 13h6m-3-3v6m5 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <span style="color: #4B5563; font-weight: 500;">อัพโหลดรูป</span>
                            </label>
                            <input id="signature_image" name="user_img" type="file" class="" style="display: none;" />
                        </div>
                    </div>
                </div>

            </div>
            <div class="mb-4 row align-items-center justify-content-md-center">
                <label for="" class="form-label col-12 col-md-2 col-lg-1 col-form-label text-start text-md-end text-lg-end">ชื่อ-สกุล</label>
                <div class="col-6 col-md-5 col-lg-2">
                    <input type="text" class="form-control" id="" name="firstname" value="<?= (@$data) ? @$data['firstname'] : '' ?>" placeholder="">
                </div>
                <div class="col-6 col-md-5 col-lg-3">
                    <input type="text" class="form-control" id="" name="lastname" value="<?= (@$data) ? @$data['lastname'] : '' ?>" placeholder="">
                </div>
            </div>
            <div class="mb-4 row align-items-start justify-content-md-start justify-content-lg-center">
                <label for="position_id" class="form-label col-12 col-md-2 col-lg-1 col-form-label text-start text-md-end text-lg-end">ประเภทผู้ใช้งาน</label>
                <div class="col-sm-5">
                    <select name="position_id" id="position_id" class="form-select">
                        <option value="">เลือก</option>
                        <option value="1" <?= (@$data && @$data['position_id'] == 1) ? 'selected' : '' ?>>เจ้าของร้าน</option>
                        <option value="2" <?= (@$data && @$data['position_id'] == 2) ? 'selected' : '' ?>>ผู้จัดการ</option>
                    </select>
                </div>
            </div>
            <div class="mb-4 row align-items-center justify-content-md-start justify-content-lg-center">
                <label for="branch_id" class="form-label col-12 col-md-2 col-lg-1 col-form-label text-start text-md-end text-lg-end">สาขาร้าน</label>
                <div class="col-sm-5">
                    <select name="branch_id" id="branch_id" class="form-select">
                        <option value="">เลือก</option>
                        <?php foreach ($branchs as $val) { ?>
                            <option value="<?= $val['id'] ?>" <?= (@$data && @$data['branch_id'] == $val['id']) ? 'selected' : '' ?>><?= $val['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
            </div>
            <div class="mb-4 row align-items-center justify-content-md-start justify-content-lg-center">
                <label for="is_active" class="form-label col-12 col-md-2 col-lg-1 col-form-label text-start text-md-end text-lg-end">สถานะ</label>
                <div class="col-sm-5">
                    <select name="is_active" id="is_active" class="form-select">
                        <option value="1" <?= (@$data && @$data['is_active'] == 1) ? 'selected' : '' ?>>ใช้งาน</option>
                        <option value="0" <?= (@$data && @$data['is_active'] == 0) ? 'selected' : '' ?>>ระงับการใช้งาน</option>
                    </select>
                </div>
            </div>
            <!-- <hr> -->
            <div class="mb-4 row align-items-center justify-content-md-start justify-content-lg-center">
                <label for="username" class="form-label col-12 col-md-2 col-lg-1 col-form-label text-start text-md-end text-lg-end">Username</label>
                <div class="col-sm-5">
                    <input type="text" class="form-control" id="username" name="username" value="<?= (@$data && @$data['username']) ? @$data['username'] : '' ?>">
                </div>
            </div>
            <div class="mb-4 row align-items-center justify-content-md-start justify-content-lg-center">
                <label for="password" class="form-label col-12 col-md-2 col-lg-1 col-form-label text-start text-md-end text-lg-end">Password</label>
                <div class="col-sm-5">
                    <input type="password" class="form-control" id="password" name="password" placeholder="กรอกรหัสผ่านใหม่">
                </div>
            </div>
            <div class="mb-4 row align-items-center justify-content-md-start justify-content-lg-center">
                <label for="confirm_password" class="form-label col-12 col-md-2 col-lg-1 col-form-label text-start text-md-end text-lg-end">Confirm Password</label>
                <div class="col-sm-5">
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" placeholder="ยืนยันรหัสผ่าน">
                </div>
            </div>
            <div class="mb-4 row align-items-center">
                <div class="col-sm-12 text-center">
                    <button type="submit" class="btn btn-primary">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFF" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
                            <path d="M12 14m-2 0a2 2 0 1 0 4 0a2 2 0 1 0 -4 0" />
                            <path d="M14 4l0 4l-6 0l0 -4" />
                        </svg>
                        บันทึก
                    </button>
                    <a class="btn btn-dark-light" href="<?= base_url("user") ?>">
                        <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-arrow-back-up" width="20" height="20" viewBox="0 0 24 24" stroke-width="1.5" stroke="#FFF" fill="none" stroke-linecap="round" stroke-linejoin="round">
                            <path stroke="none" d="M0 0h24v24H0z" fill="none" />
                            <path d="M9 14l-4 -4l4 -4" />
                            <path d="M5 10h11a4 4 0 1 1 0 8h-1" />
                        </svg>
                        ย้อนกลับ
                    </a>
                </div>
            </div>
        </div>
    </div>
</form>
<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>

<script>
    const ClickInput = () => {
        document.getElementById('signature_image').click()
    }
    $('#signature_image').on('change', function(event) {
        var output = document.getElementById('course_img');
        output.src = URL.createObjectURL(event.target.files[0]);
        output.onload = function() {
            URL.revokeObjectURL(output.src) // free memory
        }
    })
</script>
<?php $this->endSection() ?>