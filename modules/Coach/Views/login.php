<?php $this->extend('template/coach') ?>

<?= $this->section('title') ?>
<?= lang('Common.login_as_coach') ?> - GLO Football
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded-lg mt-5">
                <div class="card-header">
                    <h3 class="text-center font-weight-light my-4"><?= lang('Common.login_as_coach') ?></h3>
                </div>
                <div class="card-body">
                    <!-- LINE Login Button -->
                    <div class="text-center mb-4">
                        <a href="<?= $link ?>" class="btn btn-success btn-lg w-100">
                            <i class="fab fa-line me-2"></i>
                            <?= lang('Common.login_with_line') ?>
                        </a>
                    </div>

                    <hr class="my-4">

                    <!-- Toggle Button for Username/Password Form -->
                    <div class="text-center mb-3">
                        <button type="button" class="btn btn-outline-secondary" id="toggleFormBtn">
                            <i class="fas fa-user me-2"></i>
                            <?= lang('Common.login_with_account') ?>
                        </button>
                    </div>

                    <!-- Username/Password Login Form (Hidden by default) -->
                    <div id="loginFormContainer" style="display: none;">
                        <p class="text-center text-muted mb-3"><?= lang('Common.please_enter_login_info') ?></p>
                        <form method="post" action="<?= base_url('coach/signin') ?>" id="loginForm">
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputEmail" name="email" type="email" placeholder="<?= lang('Common.email') ?>" required />
                            <label for="inputEmail"><?= lang('Common.email') ?></label>
                        </div>
                        <div class="form-floating mb-3">
                            <input class="form-control" id="inputPassword" name="password" type="password" placeholder="<?= lang('Common.password') ?>" required />
                            <label for="inputPassword"><?= lang('Common.password') ?></label>
                        </div>
                        <div class="d-flex align-items-center justify-content-between mt-4 mb-0">
                            <a class="small" href="<?= base_url('/') ?>"><?= lang('Common.back_to_home') ?></a>
                            <button type="submit" class="btn btn-primary"><?= lang('Common.login') ?></button>
                        </div>
                    </form>
                    </div>
                </div>
                <div class="card-footer text-center py-3">
                    <div class="small">
                        <p class="text-muted"><?= lang('Common.coach_only_message') ?></p>
                        <p class="mt-2">
                            <?= lang('Common.no_account_yet') ?>
                            <a href="<?= base_url('coach/register') ?>" class="text-primary fw-bold"><?= lang('Common.register_coach') ?></a>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
// --- Language Constants ---
const langLoginWithAccount = '<?= lang('Common.login_with_account') ?>';
const langHideLoginForm = '<?= lang('Common.hide_login_form_coach') ?>';
const langIncompleteData = '<?= lang('Common.incomplete_data') ?>';
const langPleaseEnterEmailPassword = '<?= lang('Common.please_enter_email_password') ?>';
const langOkBtn = '<?= lang('Common.ok_btn') ?>';
const langConfirmLogin = '<?= lang('Common.confirm_login') ?>';
const langWantToLogin = '<?= lang('Common.want_to_login') ?>';
const langLogin = '<?= lang('Common.login') ?>';
const langCancel = '<?= lang('Common.cancel') ?>';
const langLoggingIn = '<?= lang('Common.logging_in') ?>';
const langPleaseWait = '<?= lang('Common.please_wait') ?>';
const langSuccess = '<?= lang('Common.success') ?>';
const langErrorOccurred = '<?= lang('Common.error_occurred') ?>';

// Toggle Username/Password Login Form
document.getElementById('toggleFormBtn').addEventListener('click', function() {
    const formContainer = document.getElementById('loginFormContainer');
    const btn = this;

    if (formContainer.style.display === 'none') {
        formContainer.style.display = 'block';
        btn.innerHTML = '<i class="fas fa-eye-slash me-2"></i>' + langHideLoginForm;
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-secondary');
    } else {
        formContainer.style.display = 'none';
        btn.innerHTML = '<i class="fas fa-user me-2"></i>' + langLoginWithAccount;
        btn.classList.remove('btn-secondary');
        btn.classList.add('btn-outline-secondary');
    }
});

// Add SweetAlert confirmation to login form
document.getElementById('loginForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const email = document.getElementById('inputEmail').value;
    const password = document.getElementById('inputPassword').value;

    if (!email || !password) {
        Swal.fire({
            title: langIncompleteData,
            text: langPleaseEnterEmailPassword,
            icon: 'warning',
            confirmButtonText: langOkBtn,
            confirmButtonColor: '#ffc107'
        });
        return;
    }

    Swal.fire({
        title: langConfirmLogin,
        text: langWantToLogin,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#007bff',
        cancelButtonColor: '#6c757d',
        confirmButtonText: langLogin,
        cancelButtonText: langCancel,
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: langLoggingIn,
                html: langPleaseWait,
                timerProgressBar: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            // Submit the form
            this.submit();
        }
    });
});

// Show flash messages with SweetAlert
document.addEventListener('DOMContentLoaded', function() {
    <?php if (session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: langSuccess,
            text: '<?= session()->getFlashdata('success') ?>',
            confirmButtonText: langOkBtn,
            timer: 3000,
            timerProgressBar: true
        });
    <?php elseif (session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: langErrorOccurred,
            text: '<?= session()->getFlashdata('error') ?>',
            confirmButtonText: langOkBtn
        });
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
