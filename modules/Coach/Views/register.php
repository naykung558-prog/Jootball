<?php $this->extend('template/coach') ?>
<?= $this->section('title') ?>
<?= lang('Common.register_coach_title') ?> - GLO Football
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
    .coach-register-page {
        background-color: var(--coach-gray-100);
        min-height: 100vh;
        padding: 2rem 0;
    }

    .coach-page-header {
        background-color: var(--coach-light);
        border: 1px solid var(--coach-gray-200);
        border-radius: var(--coach-border-radius);
        padding: 2rem;
        margin-bottom: 2rem;
        box-shadow: var(--coach-shadow);
    }

    .coach-page-title {
        color: var(--coach-primary);
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .coach-page-subtitle {
        color: var(--coach-gray-600);
        margin-bottom: 0;
    }

    .coach-register-card {
        background-color: var(--coach-light);
        border: 1px solid var(--coach-gray-200);
        border-radius: var(--coach-border-radius);
        box-shadow: var(--coach-shadow);
        overflow: hidden;
    }

    .coach-card-header {
        background-color: var(--coach-primary);
        color: var(--coach-light);
        padding: 1.5rem 2rem;
        font-weight: 600;
    }

    .coach-profile-image {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        border: 4px solid var(--coach-secondary);
    }

    .coach-btn-upload {
        background-color: var(--coach-secondary);
        color: var(--coach-primary);
        padding: 0.5rem 1rem;
        border-radius: var(--coach-border-radius);
        font-weight: 600;
        border: none;
        transition: var(--coach-transition);
        cursor: pointer;
    }

    .coach-btn-upload:hover {
        background-color: var(--coach-primary);
        color: var(--coach-light);
        transform: translateY(-1px);
        box-shadow: var(--coach-shadow);
    }

    .coach-form-control {
        border: 2px solid var(--coach-gray-200);
        border-radius: var(--coach-border-radius);
        transition: var(--coach-transition);
    }

    .coach-form-control:focus {
        border-color: var(--coach-secondary);
        box-shadow: 0 0 0 0.2rem rgba(243, 184, 25, 0.25);
    }

    .coach-btn-register {
        background-color: var(--coach-primary);
        border-color: var(--coach-primary);
        color: var(--coach-light);
        padding: 0.75rem 2rem;
        border-radius: var(--coach-border-radius);
        font-weight: 600;
        transition: var(--coach-transition);
    }

    .coach-btn-register:hover {
        background-color: var(--coach-dark);
        border-color: var(--coach-dark);
        color: var(--coach-light);
        transform: translateY(-1px);
        box-shadow: var(--coach-shadow);
    }

    .coach-btn-back {
        background-color: transparent;
        border: 2px solid var(--coach-primary);
        color: var(--coach-primary);
        padding: 0.75rem 1.5rem;
        border-radius: var(--coach-border-radius);
        font-weight: 600;
        transition: var(--coach-transition);
        text-decoration: none;
    }

    .coach-btn-back:hover {
        background-color: var(--coach-primary);
        color: var(--coach-light);
        transform: translateY(-1px);
        box-shadow: var(--coach-shadow);
    }

    .required-field {
        color: var(--coach-danger);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .coach-register-page {
            padding: 1rem 0;
        }

        .coach-page-header {
            padding: 1.5rem 1rem;
            margin-bottom: 1.5rem;
        }

        .coach-profile-image {
            width: 120px;
            height: 120px;
        }
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="coach-register-page">
    <div class="container">
        <!-- Page Header -->
        <div class="coach-page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="coach-page-title h3">
                        <i class="fa fa-user-plus me-3"></i>
                        <?= lang('Common.register_coach_title') ?>
                    </h1>
                    <p class="coach-page-subtitle"><?= lang('Common.fill_info_to_register') ?></p>
                </div>
                <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
                    <a href="<?= base_url('coach') ?>" class="coach-btn-back">
                        <i class="fa fa-arrow-left me-2"></i><?= lang('Common.back_to_login_page') ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="coach-register-card">
                    <div class="coach-card-header">
                        <h6 class="m-0 text-white">
                            <i class="fa fa-edit me-3"></i>
                            <?= lang('Common.coach_personal_info') ?>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" action="<?= base_url('coach/register-post') ?>" id="registerForm">
                            <!-- Login Credentials Section -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fa fa-key me-2"></i><?= lang('Common.login_credentials') ?>
                                    </h6>
                                </div>
                                <div class="col-12">
                                    <div class="form-floating mb-3">
                                        <input type="email" class="form-control coach-form-control" id="email"
                                            name="email" placeholder="<?= lang('Common.email') ?>" required>
                                        <label for="email"><?= lang('Common.email') ?> <span class="required-field">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 position-relative">
                                        <input type="password" class="form-control coach-form-control pe-5" id="password"
                                            name="password" placeholder="<?= lang('Common.password') ?>" required>
                                        <label for="password"><?= lang('Common.password') ?> <span class="required-field">*</span></label>
                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary"
                                            id="togglePassword" style="z-index: 10; margin-right: 10px; text-decoration: none;">
                                            <i class="fas fa-eye" id="passwordIcon"></i>
                                        </button>
                                        <!-- Password Requirements Indicator -->
                                        <div id="password-requirements" class="small mt-2" style="display: none;">
                                            <div id="req-length" class="text-muted">
                                                <i class="fas fa-circle" style="font-size: 6px;"></i> <?= lang('Common.password_min_length') ?>
                                            </div>
                                            <div id="req-uppercase" class="text-muted">
                                                <i class="fas fa-circle" style="font-size: 6px;"></i> <?= lang('Common.password_uppercase') ?>
                                            </div>
                                            <div id="req-lowercase" class="text-muted">
                                                <i class="fas fa-circle" style="font-size: 6px;"></i> <?= lang('Common.password_lowercase') ?>
                                            </div>
                                            <div id="req-number" class="text-muted">
                                                <i class="fas fa-circle" style="font-size: 6px;"></i> <?= lang('Common.password_number') ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3 position-relative">
                                        <input type="password" class="form-control coach-form-control pe-5"
                                            id="confirm_password" name="confirm_password" placeholder="<?= lang('Common.confirm_password') ?>"
                                            required>
                                        <label for="confirm_password"><?= lang('Common.confirm_password') ?> <span class="required-field">*</span></label>
                                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary"
                                            id="toggleConfirmPassword" style="z-index: 10; margin-right: 10px; text-decoration: none;">
                                            <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                                        </button>
                                        <div id="password-match-feedback" class="mt-2 text-start"></div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Profile Image -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fa fa-camera me-2"></i><?= lang('Common.profile_image') ?>
                                    </h6>
                                </div>
                                <div class="col-12 text-center">
                                    <div class="profile-image-container mb-3">
                                        <div id="profileImage"
                                            class="coach-profile-image d-flex align-items-center justify-content-center bg-light mx-auto">
                                            <i class="fa fa-user fa-4x text-muted"></i>
                                        </div>
                                    </div>
                                    <div>
                                        <label for="imageUpload" class="coach-btn-upload">
                                            <i class="fa fa-camera me-2"></i><?= lang('Common.select_photo') ?>
                                        </label>
                                        <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                                        <input type="hidden" name="image_path" id="base64Image">
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">

                            <!-- Personal Information -->
                            <div class="row mb-4">
                                <div class="col-12">
                                    <h6 class="text-primary mb-3">
                                        <i class="fa fa-user me-2"></i><?= lang('Common.personal_info') ?>
                                    </h6>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control coach-form-control" id="first_name"
                                            name="first_name" placeholder="<?= lang('Common.first_name') ?>" required>
                                        <label for="first_name"><?= lang('Common.first_name') ?> <span class="required-field">*</span></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control coach-form-control" id="last_name"
                                            name="last_name" placeholder="<?= lang('Common.last_name') ?>" required>
                                        <label for="last_name"><?= lang('Common.last_name') ?> <span class="required-field">*</span></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control coach-form-control" id="nick_name"
                                            name="nick_name" placeholder="<?= lang('Common.nick_name') ?>">
                                        <label for="nick_name"><?= lang('Common.nick_name') ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-control coach-form-control" id="nation" name="nation">
                                            <option value=""><?= lang('Common.select_nationality') ?></option>
                                            <option value="THAI"><?= lang('Common.thai') ?></option>
                                            <option value="OTHER"><?= lang('Common.foreign_nationality') ?></option>
                                        </select>
                                        <label for="nation"><?= lang('Common.nationality') ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control coach-form-control" id="id_card"
                                            name="id_card" placeholder="<?= lang('Common.id_card_number') ?>" maxlength="13"
                                            pattern="[0-9]{13}" title="<?= lang('Common.id_card_13_digits') ?>">
                                        <label for="id_card"><?= lang('Common.id_card_number') ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control coach-form-control" id="birth_date"
                                            name="birth_date" placeholder="<?= lang('Common.birth_date') ?>">
                                        <label for="birth_date"><?= lang('Common.birth_date') ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control coach-form-control" id="height"
                                            name="height" placeholder="<?= lang('Common.height_cm') ?>" step="0.1">
                                        <label for="height"><?= lang('Common.height_cm') ?></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control coach-form-control" id="weight"
                                            name="weight" placeholder="<?= lang('Common.weight_kg') ?>" step="0.1">
                                        <label for="weight"><?= lang('Common.weight_kg') ?></label>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <select class="form-control coach-form-control" id="dominant_foot"
                                            name="dominant_foot">
                                            <option value=""><?= lang('Common.select_dominant_foot') ?></option>
                                            <option value="ขวา"><?= lang('Common.foot_right') ?></option>
                                            <option value="ซ้าย"><?= lang('Common.foot_left') ?></option>
                                            <option value="ทั้งสองเท้า"><?= lang('Common.foot_both') ?></option>
                                        </select>
                                        <label for="dominant_foot"><?= lang('Common.dominant_foot') ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="text-end">
                                <button type="submit" class="coach-btn-register">
                                    <i class="fa fa-user-plus me-2"></i><?= lang('Common.register_member') ?>
                                </button>
                            </div>
                        </form>
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
    const langOkBtn = '<?= lang('Common.ok_btn') ?>';
    const langCancel = '<?= lang('Common.cancel') ?>';
    const langSuccess = '<?= lang('Common.success') ?>';
    const langErrorOccurred = '<?= lang('Common.error_occurred') ?>';
    const langFileTooLarge = '<?= lang('Common.file_too_large') ?>';
    const langFileMax5mb = '<?= lang('Common.file_max_5mb') ?>';
    const langInvalidFileType = '<?= lang('Common.invalid_file_type') ?>';
    const langImageFilesOnly = '<?= lang('Common.image_files_only') ?>';
    const langIdCardMust13 = '<?= lang('Common.id_card_must_13') ?>';
    const langPasswordNotSecure = '<?= lang('Common.password_not_secure') ?>';
    const langPasswordRequirementsText = '<?= lang('Common.password_requirements_text') ?>';
    const langPasswordNotMatch = '<?= lang('Common.password_not_match') ?>';
    const langCheckPasswordAgain = '<?= lang('Common.check_password_again') ?>';
    const langIdCardInvalid = '<?= lang('Common.id_card_invalid') ?>';
    const langIdCardMust13Only = '<?= lang('Common.id_card_must_13_only') ?>';
    const langConfirmRegistration = '<?= lang('Common.confirm_registration') ?>';
    const langWantToRegisterCoach = '<?= lang('Common.want_to_register_coach') ?>';
    const langRegisterMember = '<?= lang('Common.register_member') ?>';
    const langRegistering = '<?= lang('Common.registering') ?>';
    const langPleaseWait = '<?= lang('Common.please_wait') ?>';
    const langPasswordMatch = '<?= lang('Common.password_match') ?>';
    const langPasswordNotMatchWarning = '<?= lang('Common.password_not_match_warning') ?>';

    let selectedImageData = null;

    document.getElementById('imageUpload').addEventListener('change', function (e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: langFileTooLarge,
                    text: langFileMax5mb,
                    confirmButtonText: langOkBtn
                });
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: langInvalidFileType,
                    text: langImageFilesOnly,
                    confirmButtonText: langOkBtn
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function (e) {
                selectedImageData = e.target.result;
                document.getElementById('base64Image').value = selectedImageData;

                // Show preview
                const img = document.getElementById('profileImage');
                const imgElement = document.createElement('img');
                imgElement.id = 'profileImage';
                imgElement.src = selectedImageData;
                imgElement.className = 'coach-profile-image';
                img.parentNode.replaceChild(imgElement, img);
            };
            reader.readAsDataURL(file);
        }
    });

    // ID Card validation - only allow numbers and limit to 13 digits
    document.getElementById('id_card').addEventListener('input', function (e) {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');

        // Limit to 13 digits
        if (this.value.length > 13) {
            this.value = this.value.slice(0, 13);
        }
    });

    document.getElementById('id_card').addEventListener('blur', function () {
        const idCard = this.value;

        // Check if ID card is filled and validate length
        if (idCard && idCard.length !== 13) {
            this.setCustomValidity(langIdCardMust13);
        } else {
            this.setCustomValidity('');
        }
    });

    // --- Toggle Password Visibility ---
    document.getElementById('togglePassword').addEventListener('click', function() {
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('passwordIcon');
        if (passwordInput.type === 'password') {
            passwordInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            passwordInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    document.getElementById('toggleConfirmPassword').addEventListener('click', function() {
        const confirmInput = document.getElementById('confirm_password');
        const icon = document.getElementById('confirmPasswordIcon');
        if (confirmInput.type === 'password') {
            confirmInput.type = 'text';
            icon.classList.remove('fa-eye');
            icon.classList.add('fa-eye-slash');
        } else {
            confirmInput.type = 'password';
            icon.classList.remove('fa-eye-slash');
            icon.classList.add('fa-eye');
        }
    });

    // Password Strength Validation
    function validatePasswordStrength(password) {
        const requirements = {
            length: password.length >= 8,
            uppercase: /[A-Z]/.test(password),
            lowercase: /[a-z]/.test(password),
            number: /[0-9]/.test(password)
        };

        const updateReq = (id, isValid) => {
            const el = document.getElementById(id);
            if (isValid) {
                el.classList.add('text-success');
                el.classList.remove('text-muted');
                el.querySelector('i').classList.remove('fa-circle');
                el.querySelector('i').classList.add('fa-check-circle');
            } else {
                el.classList.remove('text-success');
                el.classList.add('text-muted');
                el.querySelector('i').classList.add('fa-circle');
                el.querySelector('i').classList.remove('fa-check-circle');
            }
        };

        updateReq('req-length', requirements.length);
        updateReq('req-uppercase', requirements.uppercase);
        updateReq('req-lowercase', requirements.lowercase);
        updateReq('req-number', requirements.number);

        return requirements.length && requirements.uppercase && requirements.lowercase && requirements.number;
    }

    const passwordInput = document.getElementById('password');
    const requirementsDiv = document.getElementById('password-requirements');

    passwordInput.addEventListener('focus', () => requirementsDiv.style.display = 'block');

    passwordInput.addEventListener('input', function() {
        const password = this.value;
        if (password === '') {
            requirementsDiv.style.display = 'none';
            return;
        }
        validatePasswordStrength(password);
        checkPasswordMatch();
    });

    // Password Match Validation
    function checkPasswordMatch() {
        const password = document.getElementById('password').value;
        const confirmPassword = document.getElementById('confirm_password').value;
        const feedbackDiv = document.getElementById('password-match-feedback');
        const confirmInput = document.getElementById('confirm_password');

        if (confirmPassword === '') {
            feedbackDiv.innerHTML = '';
            confirmInput.classList.remove('is-valid', 'is-invalid');
            return false;
        }

        if (password === confirmPassword) {
            confirmInput.classList.remove('is-invalid');
            confirmInput.classList.add('is-valid');
            feedbackDiv.innerHTML = '<span class="text-success"><i class="fas fa-check-circle"></i> ' + langPasswordMatch + '</span>';
            return true;
        } else {
            confirmInput.classList.remove('is-valid');
            confirmInput.classList.add('is-invalid');
            feedbackDiv.innerHTML = '<span class="text-danger"><i class="fas fa-times-circle"></i> ' + langPasswordNotMatchWarning + '</span>';
            return false;
        }
    }

    document.getElementById('confirm_password').addEventListener('input', checkPasswordMatch);

    // Form submission with validation
    document.getElementById('registerForm').addEventListener('submit', function (e) {
        e.preventDefault();

        const password = document.getElementById('password').value;
        const idCard = document.getElementById('id_card').value;

        if (!validatePasswordStrength(password)) {
             Swal.fire({
                icon: 'error',
                title: langPasswordNotSecure,
                text: langPasswordRequirementsText,
                confirmButtonText: langOkBtn
            });
            return;
        }

        if (!checkPasswordMatch()) {
             Swal.fire({
                icon: 'error',
                title: langPasswordNotMatch,
                text: langCheckPasswordAgain,
                confirmButtonText: langOkBtn
            });
            return;
        }

        // Validate ID card if filled
        if (idCard && idCard.length !== 13) {
            Swal.fire({
                icon: 'error',
                title: langIdCardInvalid,
                text: langIdCardMust13Only,
                confirmButtonText: langOkBtn
            });
            return;
        }

        Swal.fire({
            title: langConfirmRegistration,
            text: langWantToRegisterCoach,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langRegisterMember,
            cancelButtonText: langCancel,
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                Swal.fire({
                    title: langRegistering,
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

                this.submit();
            }
        });
    });

    // Show flash messages
    document.addEventListener('DOMContentLoaded', function () {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: langSuccess,
                text: '<?= session()->getFlashdata('success') ?>',
                confirmButtonText: langOkBtn,
                timer: 3000,
                timerProgressBar: true
            });
        <?php elseif(session()->getFlashdata('error')): ?>
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
