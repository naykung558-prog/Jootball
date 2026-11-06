<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg mt-5">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">ลืมรหัสผ่าน</h2>
                        <p class="text-muted">กรุณากรอก Username หรือ Email ของคุณ</p>
                    </div>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('error'); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <?= session()->getFlashdata('success'); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <form action="<?= base_url('auth/forgot-password-submit') ?>" method="POST">
                        <div class="form-floating mb-3">
                            <input type="text" class="form-control" id="username" name="username"
                                   placeholder="Username หรือ Email" required autofocus>
                            <label for="username">
                                <i class="fas fa-user me-2"></i>Username หรือ Email
                            </label>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i>ขอรีเซ็ตรหัสผ่าน
                            </button>
                        </div>

                        <div class="text-center">
                            <a href="<?= base_url('auth') ?>" class="text-decoration-none">
                                <i class="fas fa-arrow-left me-2"></i>กลับไปหน้าเข้าสู่ระบบ
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
