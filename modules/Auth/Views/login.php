<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container">
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow shadow-lg">
                <div class="card-body">
                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                            <strong><?= lang('Common.error_message') ?></strong> <?= session()->getFlashdata('error'); ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (empty(session()->get('user_id'))) { ?>
                        <div class="row justify-content-center text-center">
                            <div class="col-md-7 my-3">
                                <h2><?= lang('Common.welcome_to') ?> Jootball</h2>
                                <h4><?= lang('Common.login_with_line') ?></h4>
                            </div>
                        </div>

                        <!-- ปุ่ม Line Login หลัก -->
                        <div class="row justify-content-center">
                            <div class="col-md-7">
                                <div class="d-grid mb-4">
                                    <a href="<?= $link ?>" class="btn btn-success btn-lg">
                                        <i class="fab fa-line me-2"></i><?= lang('Common.login_with_line') ?>
                                    </a>
                                </div>

                                <!-- ปุ่มซ่อน - แสดงฟอร์ม Username/Password -->
                                <div class="text-center mb-3">
                                    <button type="button" class="btn btn-link text-muted" id="toggleLoginForm">
                                        <small><?= lang('Common.login_with_email') ?></small>
                                    </button>
                                </div>
                                <div class="text-center">
                                    <span class="text-muted small"><?= lang('Common.no_account_yet') ?></span>
                                    <a href="<?= base_url('auth/register') ?>" class="text-decoration-none small"><?= lang('Common.register_btn') ?></a>
                                </div>

                                <!-- ฟอร์มเข้าสู่ระบบด้วย Username/Password (ซ่อนไว้) -->
                                <div id="loginFormContainer" style="display: none;">
                                    <form action="<?= base_url('auth/login-with-account') ?>" method="POST">
                                        <div class="card border-primary mb-4">
                                            <div class="card-body">
                                                <div class="form-floating mb-3">
                                                    <input type="text" class="form-control" id="login_id" name="login_id" placeholder="<?= lang('Common.email_or_username') ?>" required>
                                                    <label for="login_id"><?= lang('Common.email_or_username') ?></label>
                                                </div>

                                                <div class="form-floating mb-3 position-relative">
                                                    <input type="password" class="form-control pe-5" id="password" name="password" placeholder="<?= lang('Common.password') ?>" required>
                                                    <label for="password"><?= lang('Common.password') ?></label>
                                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary"
                                                            id="togglePassword" style="z-index: 10; margin-right: 10px; text-decoration: none;">
                                                        <i class="fas fa-eye" id="passwordIcon"></i>
                                                    </button>
                                                </div>

                                                <div class="d-grid">
                                                    <button type="submit" class="btn btn-primary btn-lg"><?= lang('Common.login') ?></button>
                                                </div>

                                                <div class="text-center mt-3">
                                                    <a href="<?= base_url('auth/forgot-password') ?>" class="text-decoration-none small">
                                                        <i class="fas fa-key me-1"></i><?= lang('Common.forgot_password') ?>
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for success message
    <?php if (session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: '<?= lang('Common.success_title') ?>',
            html: '<?= session()->getFlashdata('success'); ?>',
            confirmButtonColor: '#16478C',
            confirmButtonText: '<?= lang('Common.ok_button') ?>'
        });
    <?php endif; ?>

    const toggleButton = document.getElementById('toggleLoginForm');
    const formContainer = document.getElementById('loginFormContainer');

    if (toggleButton && formContainer) {
        toggleButton.addEventListener('click', function() {
            if (formContainer.style.display === 'none') {
                formContainer.style.display = 'block';
                toggleButton.innerHTML = '<small><?= lang('Common.hide_login_form') ?></small>';
            } else {
                formContainer.style.display = 'none';
                toggleButton.innerHTML = '<small><?= lang('Common.login_with_email') ?></small>';
            }
        });
    }

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
});
</script>

<?php $this->endSection(); ?>
