<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <h4 class="card-title fw-bold mb-1">
            <i class="fas fa-envelope me-2"></i><?= lang('Common.email_smtp_settings') ?>
        </h4>
        <p class="text-muted mb-0"><?= lang('Common.email_smtp_desc') ?></p>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success'); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i>
        <ul class="mb-0">
            <?php foreach (session()->getFlashdata('errors') as $error): ?>
                <li><?= esc($error) ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-body">
                <form action="<?= base_url('admin/settings/save-email') ?>" method="POST" id="emailSettingsForm">

                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-server me-2"></i><?= lang('Common.smtp_configuration') ?>
                        </h5>

                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label for="email_smtp_host" class="form-label">SMTP Host</label>
                                <input type="text" class="form-control" id="email_smtp_host" name="email_smtp_host"
                                       value="<?= esc($settings['email_smtp_host'] ?? 'smtp.gmail.com') ?>" required>
                                <small class="text-muted"><?= lang('Common.smtp_host_hint') ?></small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="email_smtp_port" class="form-label">Port</label>
                                <input type="number" class="form-control" id="email_smtp_port" name="email_smtp_port"
                                       value="<?= esc($settings['email_smtp_port'] ?? '587') ?>" required>
                                <small class="text-muted"><?= lang('Common.smtp_port_hint') ?></small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="email_smtp_user" class="form-label">
                                <i class="fas fa-user me-1"></i>SMTP Username (Gmail Address)
                            </label>
                            <input type="email" class="form-control" id="email_smtp_user" name="email_smtp_user"
                                   value="<?= esc($settings['email_smtp_user'] ?? '') ?>"
                                   placeholder="example@gmail.com" required>
                        </div>

                        <div class="mb-3">
                            <label for="email_smtp_pass" class="form-label">
                                <i class="fas fa-lock me-1"></i>SMTP Password (App Password)
                            </label>
                            <div class="input-group">
                                <input type="password" class="form-control" id="email_smtp_pass" name="email_smtp_pass"
                                       placeholder="<?= !empty($settings['email_smtp_pass']) ? '••••••••••••••••' : lang('Common.enter_app_password') ?>">
                                <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i>
                                <?= lang('Common.must_create_app_password') ?>
                                <a href="https://myaccount.google.com/apppasswords" target="_blank"><?= lang('Common.click_here') ?></a>
                            </small>
                        </div>

                        <div class="mb-3">
                            <label for="email_smtp_crypto" class="form-label"><?= lang('Common.encryption') ?></label>
                            <select class="form-select" id="email_smtp_crypto" name="email_smtp_crypto" required>
                                <option value="tls" <?= ($settings['email_smtp_crypto'] ?? 'tls') == 'tls' ? 'selected' : '' ?>>TLS (<?= lang('Common.recommended') ?>)</option>
                                <option value="ssl" <?= ($settings['email_smtp_crypto'] ?? 'tls') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                            </select>
                        </div>
                    </div>

                    <hr>

                    <div class="mb-4">
                        <h5 class="text-primary mb-3">
                            <i class="fas fa-paper-plane me-2"></i><?= lang('Common.sender_info') ?>
                        </h5>

                        <div class="mb-3">
                            <label for="email_from_email" class="form-label">From Email</label>
                            <input type="email" class="form-control" id="email_from_email" name="email_from_email"
                                   value="<?= esc($settings['email_from_email'] ?? 'noreply@jootball.com') ?>" required>
                        </div>

                        <div class="mb-3">
                            <label for="email_from_name" class="form-label">From Name</label>
                            <input type="text" class="form-control" id="email_from_name" name="email_from_name"
                                   value="<?= esc($settings['email_from_name'] ?? 'Jootball Academy') ?>" required>
                        </div>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-2"></i><?= lang('Common.save_settings') ?>
                        </button>
                        <button type="button" class="btn btn-success" id="testEmailBtn">
                            <i class="fas fa-paper-plane me-2"></i><?= lang('Common.test_send_email') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card shadow-sm bg-light">
            <div class="card-body">
                <h5 class="card-title">
                    <i class="fas fa-info-circle me-2"></i><?= lang('Common.guidelines') ?>
                </h5>

                <div class="alert alert-warning">
                    <strong><?= lang('Common.important') ?></strong> <?= lang('Common.must_use_app_password') ?>
                </div>

                <h6 class="mt-3"><?= lang('Common.how_to_create_app_password') ?></h6>
                <ol class="small">
                    <li><?= lang('Common.step_open_google') ?> <a href="https://myaccount.google.com/" target="_blank">Google Account</a></li>
                    <li><?= lang('Common.step_enable_2step') ?></li>
                    <li><?= lang('Common.step_goto_security') ?></li>
                    <li><?= lang('Common.step_create_app_password') ?></li>
                    <li><?= lang('Common.step_copy_16_chars') ?></li>
                </ol>

                <div class="alert alert-info mt-3">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong><?= lang('Common.recommended_settings') ?></strong><br>
                    <small>
                        • Port: 587<br>
                        • Encryption: TLS<br>
                        • Host: smtp.gmail.com
                    </small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
const langTestSendEmail = '<?= lang("Common.test_send_email") ?>';
const langEnterTestEmail = '<?= lang("Common.enter_test_email") ?>';
const langSendTestEmail = '<?= lang("Common.send_test_email") ?>';
const langCancel = '<?= lang("Common.cancel") ?>';
const langPleaseEnterEmail = '<?= lang("Common.enter_email") ?>';
const langInvalidEmailFormat = '<?= lang("Common.invalid_email") ?>';
const langSendingEmail = '<?= lang("Common.sending_email") ?>';
const langPleaseWait = '<?= lang("Common.please_wait_moment") ?>';
const langEmailSentSuccess = '<?= lang("Common.email_sent_success") ?>';
const langCannotSendEmail = '<?= lang("Common.cannot_send_email") ?>';
const langErrorOccurred = '<?= lang("Common.error_occurred") ?>';
const langCannotConnectServer = '<?= lang("Common.cannot_connect_server") ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordField = document.getElementById('email_smtp_pass');
    const toggleIcon = document.getElementById('toggleIcon');

    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            if (passwordField.type === 'password') {
                passwordField.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-slash');
            } else {
                passwordField.type = 'password';
                toggleIcon.classList.remove('fa-eye-slash');
                toggleIcon.classList.add('fa-eye');
            }
        });
    }

    // Test Email
    const testEmailBtn = document.getElementById('testEmailBtn');
    if (testEmailBtn) {
        testEmailBtn.addEventListener('click', async function() {
            const result = await Swal.fire({
                title: langTestSendEmail,
                input: 'email',
                inputLabel: langEnterTestEmail,
                inputPlaceholder: 'example@gmail.com',
                showCancelButton: true,
                confirmButtonText: langSendTestEmail,
                cancelButtonText: langCancel,
                confirmButtonColor: '#28a745',
                inputValidator: (value) => {
                    if (!value) {
                        return langPleaseEnterEmail
                    }
                    if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)) {
                        return langInvalidEmailFormat
                    }
                }
            });

            if (result.isConfirmed) {
                Swal.fire({
                    title: langSendingEmail,
                    text: langPleaseWait,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });

                try {
                    const response = await fetch('<?= base_url('admin/settings/test-email') ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded',
                        },
                        body: 'test_email=' + encodeURIComponent(result.value)
                    });

                    const data = await response.json();

                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: langEmailSentSuccess,
                            text: data.message,
                            confirmButtonColor: '#16478C'
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: langCannotSendEmail,
                            text: data.message,
                            confirmButtonColor: '#16478C'
                        });
                    }
                } catch (error) {
                    Swal.fire({
                        icon: 'error',
                        title: langErrorOccurred,
                        text: langCannotConnectServer,
                        confirmButtonColor: '#16478C'
                    });
                }
            }
        });
    }
});
</script>

<?php $this->endSection(); ?>
