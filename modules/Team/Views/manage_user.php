<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-primary text-white p-4 border-0">
                    <div class="d-flex align-items-center">
                        <div>
                            <h4 class="mb-1 fw-bold text-white"><?= lang('Common.my_account_title') ?></h4>
                            <p class="mb-0 opacity-75 small"><?= lang('Common.account_desc') ?></p>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 p-md-5">

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-alert-circle fs-4 me-2"></i>
                                <div>
                                    <strong><?= lang('Common.error_message') ?></strong> <?= session()->getFlashdata('error'); ?>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-3 mb-4" role="alert">
                            <div class="d-flex align-items-center">
                                <i class="ti ti-check-circle fs-4 me-2"></i>
                                <div>
                                    <?= session()->getFlashdata('success'); ?>
                                </div>
                            </div>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <div class="row g-4">
                        <!-- Profile Picture & LINE Integration Section -->
                        <div class="col-md-5 order-md-2">
                            <!-- Profile Picture Section -->
                            <div class="card border-0 bg-light rounded-4 mb-3">
                                <div class="card-body text-center p-4">
                                    <h5 class="fw-bold mb-4 text-primary"><i class="fas fa-user-circle me-2"></i><?= lang('Common.profile_picture') ?></h5>

                                    <div class="position-relative d-inline-block mb-3">
                                        <?php
                                        $profileImageSrc = base_url('public/img/avatar.png');

                                        // ถ้ามี LINE profile ใช้รูป LINE
                                        if (session()->get('profile') && !empty(session()->get('profile')->picture)) {
                                            $profileImageSrc = session()->get('profile')->picture;
                                        }
                                        // ถ้าไม่มี LINE แต่มีรูปที่ upload มา ใช้รูปที่ upload
                                        elseif (!empty($user['profile_image']) && file_exists(FCPATH . $user['profile_image'])) {
                                            $profileImageSrc = base_url($user['profile_image']);
                                        }
                                        ?>
                                        <img id="profileImagePreview"
                                             src="<?= $profileImageSrc ?>"
                                             class="rounded-circle shadow-sm border border-4 border-white"
                                             alt="Profile Picture"
                                             style="width: 140px; height: 140px; object-fit: cover;"
                                             onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22140%22 height=%22140%22%3E%3Crect width=%22140%22 height=%22140%22 fill=%22%23f0f0f0%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22sans-serif%22 font-size=%2248%22 fill=%22%23999%22%3E👤%3C/text%3E%3C/svg%3E'">

                                        <?php if (!session()->get('profile')): ?>
                                        <!-- ปุ่มเปลี่ยนรูป (แสดงเฉพาะเมื่อไม่ได้ใช้ LINE) -->
                                        <label for="newProfileImage" class="btn btn-primary btn-sm rounded-circle position-absolute shadow-sm"
                                               style="bottom: 0; right: 0; width: 40px; height: 40px; padding: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;"
                                               title="<?= lang('Common.change_profile_picture') ?>">
                                            <i class="fas fa-camera"></i>
                                        </label>
                                        <input type="file" id="newProfileImage" name="new_profile_image" class="d-none" accept="image/*">
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!session()->get('profile')): ?>
                                    <p class="text-muted small mb-0">
                                        <i class="fas fa-info-circle me-1"></i><?= lang('Common.click_camera_to_change') ?>
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- LINE Integration Section -->
                            <div class="card border-0 bg-light rounded-4">
                                <div class="card-body text-center p-4">
                                    <h5 class="fw-bold mb-4 text-success"><i class="fab fa-line me-2 fs-4"></i><?= lang('Common.line_account') ?></h5>

                                    <?php if (!empty($user['uid_line'])): ?>
                                        <!-- LINE Connected (from DB) -->
                                        <?php if (session()->get('profile')): ?>
                                            <div class="position-relative mb-3">
                                                <img src="<?= session()->get('profile')->picture ?>" class="rounded-circle shadow-sm border border-3 border-success" alt="LINE Picture" style="width: 100px; height: 100px; object-fit: cover;"
                                                     onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                                <div class="bg-success-subtle rounded-circle shadow-sm d-none align-items-center justify-content-center mx-auto" style="width: 100px; height: 100px;">
                                                    <i class="fab fa-line text-success" style="font-size: 2.5rem;"></i>
                                                </div>
                                                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" style="width: 20px; height: 20px;"></span>
                                            </div>
                                            <h6 class="fw-bold mb-1"><?= session()->get('profile')->name ?></h6>
                                        <?php else: ?>
                                            <div class="position-relative d-inline-block mb-3">
                                                <div class="bg-success-subtle rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                                    <i class="fab fa-line text-success" style="font-size: 2.5rem;"></i>
                                                </div>
                                                <span class="position-absolute bottom-0 end-0 bg-success border border-2 border-white rounded-circle" style="width: 20px; height: 20px;"></span>
                                            </div>
                                        <?php endif; ?>
                                        <p class="text-muted small mb-3">
                                            <span class="badge bg-success-subtle text-success rounded-pill px-3">
                                                <i class="ti ti-check me-1"></i><?= lang('Common.line_connected') ?>
                                            </span>
                                        </p>
                                    <?php else: ?>
                                        <!-- LINE Not Connected -->
                                        <div class="bg-white rounded-circle p-3 mb-3 shadow-sm d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                            <i class="ti ti-link-off text-muted fs-1"></i>
                                        </div>
                                        <p class="text-muted small mb-3"><?= lang('Common.line_not_connected') ?></p>

                                        <?php if (!empty($line_sync_link)): ?>
                                            <a href="<?= $line_sync_link ?>" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                                                <i class="fab fa-line me-2"></i><?= lang('Common.sync_line_account') ?>
                                            </a>
                                            <p class="text-muted small mt-2 mb-0"><?= lang('Common.sync_line_desc') ?></p>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Form Section -->
                        <div class="col-md-7 order-md-1">
                            <form id="profileForm" action="<?= base_url('team/user-save') ?>" method="POST" enctype="multipart/form-data" class="h-100 d-flex flex-column">
                                <?= csrf_field() ?>
                                <input type="hidden" name="id" value="<?= session()->get('user_id') ?>">
                                <input type="hidden" name="profile_image_changed" id="profileImageChanged" value="0">

                                <div class="mb-4">
                                    <h5 class="fw-bold text-primary mb-3"><i class="ti ti-lock me-2"></i><?= lang('Common.username_password_title') ?></h5>
                                    <p class="text-muted small"><?= lang('Common.fill_account_info') ?></p>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="text" class="form-control bg-light border-0" id="username" name="username" placeholder="username123" value="<?= @$user['username'] ?>" required>
                                    <label for="username" class="text-muted"><i class="ti ti-user me-2"></i><?= lang('Common.username') ?></label>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="email" class="form-control bg-light border-0" id="email" name="email" placeholder="name@example.com" value="<?= @$user['email'] ?>">
                                    <label for="email" class="text-muted"><i class="ti ti-mail me-2"></i><?= lang('Common.email') ?></label>
                                </div>

                                <hr class="my-4">
                                <div class="mb-3">
                                    <h6 class="fw-bold text-primary mb-1"><i class="ti ti-key me-2"></i><?= lang('Common.change_password') ?></h6>
                                    <p class="text-muted small mb-0"><?= lang('Common.leave_blank_no_change') ?></p>
                                </div>

                                <div class="form-floating mb-3">
                                    <input type="password" class="form-control bg-light border-0 pe-5" id="password" name="password" placeholder="password">
                                    <label for="password" class="text-muted"><i class="ti ti-key me-2"></i><?= lang('Common.new_password') ?></label>
                                    <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary"
                                            id="togglePassword" style="z-index: 10; margin-right: 10px; text-decoration: none;">
                                        <i class="fas fa-eye" id="passwordIcon"></i>
                                    </button>
                                </div>
                                <div id="password-strength" class="mb-2" style="display:none;">
                                    <div class="progress" style="height: 5px;">
                                        <div id="strengthBar" class="progress-bar" role="progressbar" style="width: 0%"></div>
                                    </div>
                                    <small id="strengthText" class="text-muted"></small>
                                </div>

                                <div class="form-floating mb-4">
                                    <input type="password" class="form-control bg-light border-0" id="confirm_password" name="confirm_password" placeholder="Confirm Password">
                                    <label for="confirm_password" class="text-muted"><i class="ti ti-check-circle me-2"></i><?= lang('Common.confirm_password_label') ?></label>
                                    <div id="password-feedback" class="invalid-feedback mt-2 ms-1"></div>
                                </div>

                                <div class="mt-auto pt-3 border-top">
                                    <button type="button" id="submitBtn" class="btn btn-primary w-100 rounded-pill py-2 fw-bold shadow-sm hover-scale">
                                        <i class="ti ti-device-floppy me-2"></i><?= lang('Common.edit') ?>
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-scale {
        transition: transform 0.2s;
    }
    .hover-scale:hover {
        transform: scale(1.02);
    }
</style>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {

        const form = $('#profileForm');
        const passwordInput = $('#password');
        const confirmPasswordInput = $('#confirm_password');
        const passwordFeedback = $('#password-feedback');
        const submitButton = $('#submitBtn');
        const strengthBar = $('#strengthBar');
        const strengthText = $('#strengthText');
        const strengthContainer = $('#password-strength');

        // Lang constants
        const langPwdWeak = '<?= lang('Common.password_weak') ?>';
        const langPwdMedium = '<?= lang('Common.password_medium') ?>';
        const langPwdStrong = '<?= lang('Common.password_strong') ?>';
        const langPwdRequirements = '<?= lang('Common.password_requirements') ?>';

        // --- Profile Image Upload ---
        $('#newProfileImage').on('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!validTypes.includes(file.type)) {
                    Swal.fire({ icon: 'error', title: '<?= lang('Common.invalid_file') ?>', text: '<?= lang('Common.select_image_only') ?>', confirmButtonColor: '#dc3545' });
                    $(this).val('');
                    return;
                }
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({ icon: 'error', title: '<?= lang('Common.file_too_large') ?>', text: '<?= lang('Common.file_max_5mb') ?>', confirmButtonColor: '#dc3545' });
                    $(this).val('');
                    return;
                }
                const reader = new FileReader();
                reader.onload = function(e) {
                    $('#profileImagePreview').attr('src', e.target.result);
                    $('#profileImageChanged').val('1');
                    Swal.fire({ icon: 'success', title: '<?= lang('Common.image_selected') ?>', text: '<?= lang('Common.press_save_to_update') ?>', confirmButtonColor: '#16478C', timer: 2000, timerProgressBar: true });
                };
                reader.readAsDataURL(file);
            }
        });

        // --- Toggle Password Visibility ---
        $('#togglePassword').on('click', function() {
            if (passwordInput.attr('type') === 'password') {
                passwordInput.attr('type', 'text');
                $('#passwordIcon').removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordInput.attr('type', 'password');
                $('#passwordIcon').removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // --- Password Strength Check ---
        function checkPasswordStrength(password) {
            let score = 0;
            if (password.length >= 8) score++;
            if (/[A-Z]/.test(password)) score++;
            if (/[a-z]/.test(password)) score++;
            if (/[0-9]/.test(password)) score++;
            if (/[^A-Za-z0-9]/.test(password)) score++;
            return score;
        }

        passwordInput.on('keyup', function() {
            const pwd = $(this).val();
            if (pwd.length === 0) {
                strengthContainer.hide();
                return;
            }
            strengthContainer.show();
            const score = checkPasswordStrength(pwd);

            if (score <= 2) {
                strengthBar.css('width', '33%').removeClass().addClass('progress-bar bg-danger');
                strengthText.text(langPwdWeak).removeClass().addClass('text-danger small');
            } else if (score <= 3) {
                strengthBar.css('width', '66%').removeClass().addClass('progress-bar bg-warning');
                strengthText.text(langPwdMedium).removeClass().addClass('text-warning small');
            } else {
                strengthBar.css('width', '100%').removeClass().addClass('progress-bar bg-success');
                strengthText.text(langPwdStrong).removeClass().addClass('text-success small');
            }
        });

        // --- Validate Passwords Match ---
        function validatePasswords() {
            const password = passwordInput.val();
            const confirmPassword = confirmPasswordInput.val();

            if (password === '' && confirmPassword === '') {
                confirmPasswordInput.removeClass('is-invalid is-valid');
                passwordFeedback.hide();
                return true;
            }

            if (password === confirmPassword) {
                confirmPasswordInput.removeClass('is-invalid').addClass('is-valid');
                passwordFeedback.removeClass('invalid-feedback').addClass('valid-feedback');
                passwordFeedback.html('<i class="ti ti-check me-1"></i> <?= lang('Common.password_match') ?>').show();
                return true;
            } else {
                confirmPasswordInput.removeClass('is-valid').addClass('is-invalid');
                passwordFeedback.removeClass('valid-feedback').addClass('invalid-feedback');
                passwordFeedback.html('<i class="ti ti-alert-circle me-1"></i> <?= lang('Common.password_not_match') ?>').show();
                return false;
            }
        }

        confirmPasswordInput.on('keyup', validatePasswords);
        passwordInput.on('keyup', validatePasswords);

        // --- Validate Password Strength (like registration) ---
        function validatePasswordStrength(password) {
            if (password.length < 8) return false;
            if (!/[A-Z]/.test(password)) return false;
            if (!/[a-z]/.test(password)) return false;
            if (!/[0-9]/.test(password)) return false;
            return true;
        }

        // --- Submit ---
        submitButton.on('click', function(event) {
            const password = passwordInput.val();

            // If password is filled, validate strength
            if (password.length > 0) {
                if (!validatePasswordStrength(password)) {
                    Swal.fire({
                        icon: 'error',
                        title: '<?= lang('Common.data_incorrect') ?>',
                        text: langPwdRequirements,
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: '<?= lang('Common.ok_btn') ?>'
                    });
                    event.preventDefault();
                    return;
                }

                if (!validatePasswords()) {
                    Swal.fire({
                        icon: 'error',
                        title: '<?= lang('Common.data_incorrect') ?>',
                        text: '<?= lang('Common.password_mismatch_warning') ?>',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: '<?= lang('Common.ok_btn') ?>'
                    });
                    event.preventDefault();
                    return;
                }
            }

            form.submit();
        });

    });
</script>
<?php $this->endSection(); ?>