<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-lg mt-5">
                <div class="card-body p-5">
                    <div class="text-center mb-4">
                        <h2 class="fw-bold">ตั้งรหัสผ่านใหม่</h2>
                        <p class="text-muted">สำหรับ: <strong><?= esc($username) ?></strong></p>
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

                    <form action="<?= base_url('auth/reset-password-submit') ?>" method="POST" id="resetForm">
                        <input type="hidden" name="token" value="<?= esc($token) ?>">

                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control pe-5" id="password" name="password"
                                   placeholder="รหัสผ่านใหม่" required minlength="8">
                            <label for="password">
                                <i class="fas fa-lock me-2"></i>รหัสผ่านใหม่
                            </label>
                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary"
                                    id="togglePassword" style="z-index: 10; margin-right: 10px; text-decoration: none;">
                                <i class="fas fa-eye" id="passwordIcon"></i>
                            </button>
                        </div>

                        <div class="form-floating mb-3 position-relative">
                            <input type="password" class="form-control pe-5" id="confirm_password" name="confirm_password"
                                   placeholder="ยืนยันรหัสผ่านใหม่" required minlength="8">
                            <label for="confirm_password">
                                <i class="fas fa-lock me-2"></i>ยืนยันรหัสผ่านใหม่
                            </label>
                            <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary"
                                    id="toggleConfirmPassword" style="z-index: 10; margin-right: 10px; text-decoration: none;">
                                <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                            </button>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <i class="fas fa-info-circle me-2"></i>
                                รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร ประกอบด้วย:
                                <ul class="mb-0 mt-2">
                                    <li>ตัวพิมพ์ใหญ่ (A-Z)</li>
                                    <li>ตัวพิมพ์เล็ก (a-z)</li>
                                    <li>ตัวเลข (0-9)</li>
                                </ul>
                            </small>
                        </div>

                        <div class="d-grid gap-2 mb-3">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-check me-2"></i>ยืนยันรหัสผ่านใหม่
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

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle Password Visibility
    const togglePasswordBtn = document.getElementById('togglePassword');
    const passwordField = document.getElementById('password');
    const passwordIcon = document.getElementById('passwordIcon');

    if (togglePasswordBtn && passwordField && passwordIcon) {
        togglePasswordBtn.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                passwordIcon.classList.remove('fa-eye');
                passwordIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                passwordIcon.classList.remove('fa-eye-slash');
                passwordIcon.classList.add('fa-eye');
            }
        });
    }

    // Toggle Confirm Password Visibility
    const toggleConfirmPasswordBtn = document.getElementById('toggleConfirmPassword');
    const confirmPasswordField = document.getElementById('confirm_password');
    const confirmPasswordIcon = document.getElementById('confirmPasswordIcon');

    if (toggleConfirmPasswordBtn && confirmPasswordField && confirmPasswordIcon) {
        toggleConfirmPasswordBtn.addEventListener('click', function() {
            if (confirmPasswordField.type === 'password') {
                confirmPasswordField.type = 'text';
                confirmPasswordIcon.classList.remove('fa-eye');
                confirmPasswordIcon.classList.add('fa-eye-slash');
            } else {
                confirmPasswordField.type = 'password';
                confirmPasswordIcon.classList.remove('fa-eye-slash');
                confirmPasswordIcon.classList.add('fa-eye');
            }
        });
    }

    // Form validation
    const form = document.getElementById('resetForm');
    if (form) {
        form.addEventListener('submit', function(e) {
            const password = passwordField.value;
            const confirmPassword = confirmPasswordField.value;

            if (password !== confirmPassword) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อผิดพลาด',
                    text: 'รหัสผ่านไม่ตรงกัน',
                    confirmButtonColor: '#16478C'
                });
                return false;
            }

            // Password strength validation
            if (password.length < 8 || !/[A-Z]/.test(password) || !/[a-z]/.test(password) || !/[0-9]/.test(password)) {
                e.preventDefault();
                Swal.fire({
                    icon: 'error',
                    title: 'รหัสผ่านไม่ปลอดภัย',
                    text: 'รหัสผ่านต้องมีความยาวอย่างน้อย 8 ตัวอักษร ประกอบด้วยตัวพิมพ์ใหญ่ ตัวพิมพ์เล็ก และตัวเลข',
                    confirmButtonColor: '#16478C'
                });
                return false;
            }
        });
    }
});
</script>

<?php $this->endSection(); ?>
