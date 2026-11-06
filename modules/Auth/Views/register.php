<?= $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<style>
    /* Style สำหรับข้อความ Feedback การจับคู่รหัสผ่าน */
    #password-match-feedback {
        font-size: 0.875em;
        font-weight: 500;
    }

    .password-valid {
        color: #198754;
        /* สีเขียวของ Bootstrap */
    }

    .password-invalid {
        color: #dc3545;
        /* สีแดงของ Bootstrap */
    }
</style>
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-7 col-lg-6">
            <div class="card shadow-lg p-4 p-md-5">
                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show text-center" role="alert">
                        <strong><?= lang('Common.error_message') ?></strong> <?= session()->getFlashdata('error'); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success text-center" role="alert">
                        <?= session()->getFlashdata('success'); ?>
                    </div>
                <?php endif; ?>

                <form id="registerForm" action="<?= base_url('auth/register-post') ?>" method="POST" enctype="multipart/form-data" class="needs-validation" novalidate>
                    <?= csrf_field() ?>
                    <div class="text-center mb-4">
                        <h2 class="h3 fw-bold"><?= lang('Common.register_btn') ?></h2>
                        <p class="text-muted"><?= lang('Common.create_account_desc') ?></p>
                    </div>

                    <!-- Profile Image Upload -->
                    <div class="text-center mb-4">
                        <div class="position-relative d-inline-block">
                            <img id="imagePreview" src="<?= base_url('public/img/avatar.png') ?>"
                                 class="rounded-circle border border-3 border-light shadow"
                                 style="width: 120px; height: 120px; object-fit: cover; background-color: #f8f9fa;"
                                 onerror="this.src='data:image/svg+xml,%3Csvg xmlns=%22http://www.w3.org/2000/svg%22 width=%22120%22 height=%22120%22%3E%3Crect width=%22120%22 height=%22120%22 fill=%22%23e9ecef%22/%3E%3Ctext x=%2250%25%22 y=%2250%25%22 dominant-baseline=%22middle%22 text-anchor=%22middle%22 font-family=%22Arial%22 font-size=%2240%22 fill=%22%236c757d%22%3E%F0%9F%91%A4%3C/text%3E%3C/svg%3E'">
                            <label for="profileImage" class="btn btn-primary btn-sm rounded-circle position-absolute"
                                   style="bottom: 5px; right: 5px; width: 35px; height: 35px; padding: 0; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="profileImage" name="profile_image" class="d-none" accept="image/*">
                        </div>
                        <div class="small text-muted mt-2">
                            <?= service('request')->getLocale() === 'th' ? 'คลิกที่ไอคอนกล้องเพื่ออัปโหลดรูปโปรไฟล์' : 'Click camera icon to upload profile picture' ?>
                        </div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="text" class="form-control" id="name" name="name" placeholder="<?= lang('Common.fullname') ?>" value="<?= old('name') ?>" required>
                        <label for="name"><?= lang('Common.fullname') ?></label>
                        <div class="invalid-feedback"><?= service('request')->getLocale() === 'th' ? 'กรุณากรอกชื่อ-นามสกุล' : 'Please enter your full name' ?></div>
                    </div>

                    <div class="form-floating mb-3">
                        <input type="email" class="form-control" id="email" name="email" placeholder="<?= lang('Common.email_placeholder') ?>" value="<?= old('email') ?>" required>
                        <label for="email"><?= lang('Common.email') ?></label>
                        <div class="invalid-feedback"><?= lang('Common.enter_email') ?></div>
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control pe-5" id="password" name="password" placeholder="<?= lang('Common.password') ?>" required>
                        <label for="password"><?= lang('Common.password') ?></label>
                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary"
                                id="togglePassword" style="z-index: 10; margin-right: 10px; text-decoration: none;">
                            <i class="fas fa-eye" id="passwordIcon"></i>
                        </button>
                        <div class="invalid-feedback"><?= lang('Common.input_missing') ?></div>
                        <!-- Password Requirements Indicator -->
                        <div id="password-requirements" class="small mt-2" style="display: none;">
                            <div id="req-length" class="text-muted">
                                <i class="fas fa-circle" style="font-size: 6px;"></i> <?= service('request')->getLocale() === 'th' ? 'ความยาวอย่างน้อย 8 ตัวอักษร' : 'At least 8 characters' ?>
                            </div>
                            <div id="req-uppercase" class="text-muted">
                                <i class="fas fa-circle" style="font-size: 6px;"></i> <?= service('request')->getLocale() === 'th' ? 'มีตัวพิมพ์ใหญ่ (A-Z)' : 'Contains uppercase letter (A-Z)' ?>
                            </div>
                            <div id="req-lowercase" class="text-muted">
                                <i class="fas fa-circle" style="font-size: 6px;"></i> <?= service('request')->getLocale() === 'th' ? 'มีตัวพิมพ์เล็ก (a-z)' : 'Contains lowercase letter (a-z)' ?>
                            </div>
                            <div id="req-number" class="text-muted">
                                <i class="fas fa-circle" style="font-size: 6px;"></i> <?= service('request')->getLocale() === 'th' ? 'มีตัวเลข (0-9)' : 'Contains number (0-9)' ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-floating mb-3 position-relative">
                        <input type="password" class="form-control pe-5" id="confirm_password" name="confirm_password" placeholder="<?= lang('Common.confirm_password') ?>" required>
                        <label for="confirm_password"><?= lang('Common.confirm_password') ?></label>
                        <button type="button" class="btn btn-link position-absolute end-0 top-50 translate-middle-y text-secondary"
                                id="toggleConfirmPassword" style="z-index: 10; margin-right: 10px; text-decoration: none;">
                            <i class="fas fa-eye" id="confirmPasswordIcon"></i>
                        </button>
                        <!-- ส่วนแสดงผล Feedback ของรหัสผ่าน -->
                        <div id="password-match-feedback" class="mt-2"></div>
                    </div>

                    <div class="d-grid">
                        <button type="submit" id="submitBtn" class="btn btn-primary btn-lg">
                            <?= lang('Common.register_btn') ?>
                        </button>
                    </div>

                     <div class="text-center mt-3">
                        <a href="<?= base_url('auth') ?>" class="text-decoration-none"><?= lang('Common.login') ?></a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>

<script>
    $(document).ready(function() {

        // --- เลือก Elements ---
        const registerForm = $('#registerForm');
        const passwordInput = $('#password');
        const confirmPasswordInput = $('#confirm_password');
        const feedbackDiv = $('#password-match-feedback');
        const requirementsDiv = $('#password-requirements');
        const profileImageInput = $('#profileImage');
        const imagePreview = $('#imagePreview');

        // --- Toggle Password Visibility ---
        $('#togglePassword').on('click', function() {
            const passwordField = $('#password');
            const passwordIcon = $('#passwordIcon');

            if (passwordField.attr('type') === 'password') {
                passwordField.attr('type', 'text');
                passwordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                passwordField.attr('type', 'password');
                passwordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        $('#toggleConfirmPassword').on('click', function() {
            const confirmPasswordField = $('#confirm_password');
            const confirmPasswordIcon = $('#confirmPasswordIcon');

            if (confirmPasswordField.attr('type') === 'password') {
                confirmPasswordField.attr('type', 'text');
                confirmPasswordIcon.removeClass('fa-eye').addClass('fa-eye-slash');
            } else {
                confirmPasswordField.attr('type', 'password');
                confirmPasswordIcon.removeClass('fa-eye-slash').addClass('fa-eye');
            }
        });

        // --- Image Preview Function ---
        profileImageInput.on('change', function(e) {
            const file = e.target.files[0];

            if (file) {
                // ตรวจสอบประเภทไฟล์
                if (!file.type.match('image.*')) {
                    Swal.fire({
                        icon: 'error',
                        title: '<?= service('request')->getLocale() === 'th' ? 'ไฟล์ไม่ถูกต้อง' : 'Invalid File' ?>',
                        text: '<?= service('request')->getLocale() === 'th' ? 'กรุณาเลือกไฟล์รูปภาพเท่านั้น' : 'Please select an image file only' ?>'
                    });
                    $(this).val('');
                    return;
                }

                // ตรวจสอบขนาดไฟล์ (ไม่เกิน 5MB)
                if (file.size > 5 * 1024 * 1024) {
                    Swal.fire({
                        icon: 'error',
                        title: '<?= service('request')->getLocale() === 'th' ? 'ไฟล์ใหญ่เกินไป' : 'File Too Large' ?>',
                        text: '<?= service('request')->getLocale() === 'th' ? 'กรุณาเลือกรูปภาพที่มีขนาดไม่เกิน 5MB' : 'Please select an image smaller than 5MB' ?>'
                    });
                    $(this).val('');
                    return;
                }

                // แสดงตัวอย่างรูปภาพ
                const reader = new FileReader();
                reader.onload = function(event) {
                    imagePreview.attr('src', event.target.result);
                };
                reader.readAsDataURL(file);
            }
        });

        // --- ฟังก์ชันตรวจสอบความแข็งแรงของรหัสผ่าน ---
        function validatePasswordStrength(password) {
            const requirements = {
                length: password.length >= 8,
                uppercase: /[A-Z]/.test(password),
                lowercase: /[a-z]/.test(password),
                number: /[0-9]/.test(password)
            };

            // อัพเดทสถานะของแต่ละข้อกำหนด
            $('#req-length').toggleClass('text-success', requirements.length)
                .toggleClass('text-muted', !requirements.length)
                .find('i').toggleClass('fa-check-circle', requirements.length)
                .toggleClass('fa-circle', !requirements.length);

            $('#req-uppercase').toggleClass('text-success', requirements.uppercase)
                .toggleClass('text-muted', !requirements.uppercase)
                .find('i').toggleClass('fa-check-circle', requirements.uppercase)
                .toggleClass('fa-circle', !requirements.uppercase);

            $('#req-lowercase').toggleClass('text-success', requirements.lowercase)
                .toggleClass('text-muted', !requirements.lowercase)
                .find('i').toggleClass('fa-check-circle', requirements.lowercase)
                .toggleClass('fa-circle', !requirements.lowercase);

            $('#req-number').toggleClass('text-success', requirements.number)
                .toggleClass('text-muted', !requirements.number)
                .find('i').toggleClass('fa-check-circle', requirements.number)
                .toggleClass('fa-circle', !requirements.number);

            // ส่งค่ากลับว่าผ่านเงื่อนไขทั้งหมดหรือไม่
            return requirements.length && requirements.uppercase && requirements.lowercase && requirements.number;
        }

        // --- เมื่อเริ่มพิมพ์รหัสผ่าน แสดงข้อกำหนด ---
        passwordInput.on('focus', function() {
            requirementsDiv.slideDown(200);
        });

        // --- ตรวจสอบรหัสผ่านแบบ real-time ---
        passwordInput.on('input', function() {
            const password = $(this).val();

            if (password === '') {
                requirementsDiv.slideUp(200);
                passwordInput.removeClass('is-valid is-invalid');
                return;
            }

            const isValid = validatePasswordStrength(password);

            if (isValid) {
                passwordInput.removeClass('is-invalid').addClass('is-valid');
            } else {
                passwordInput.removeClass('is-valid').addClass('is-invalid');
            }

            // ตรวจสอบความตรงกันด้วย
            checkPasswordMatch();
        });

        // --- ฟังก์ชันหลักสำหรับตรวจสอบความตรงกันของรหัสผ่าน ---
        function checkPasswordMatch() {
            const password = passwordInput.val();
            const confirmPassword = confirmPasswordInput.val();

            // ถ้ายังไม่ได้กรอกช่อง "ยืนยันรหัสผ่าน" ให้เคลียร์ feedback
            if (confirmPassword === '') {
                feedbackDiv.empty();
                confirmPasswordInput.removeClass('is-invalid is-valid');
                return;
            }

            // --- เปรียบเทียบรหัสผ่าน ---
            if (password === confirmPassword) {
                // กรณีรหัสผ่านตรงกัน
                confirmPasswordInput.removeClass('is-invalid').addClass('is-valid');
                feedbackDiv.html('<i class="fas fa-check-circle"></i> <?= service('request')->getLocale() === 'th' ? 'รหัสผ่านตรงกัน' : 'Passwords match' ?>')
                    .removeClass('password-invalid')
                    .addClass('password-valid');
                return true;
            } else {
                // กรณีรหัสผ่านไม่ตรงกัน
                confirmPasswordInput.removeClass('is-valid').addClass('is-invalid');
                feedbackDiv.html('<i class="fas fa-times-circle"></i> <?= service('request')->getLocale() === 'th' ? 'รหัสผ่านไม่ตรงกัน!' : 'Passwords do not match!' ?>')
                    .removeClass('password-valid')
                    .addClass('password-invalid');
                return false;
            }
        }

        // --- ผูก Event การพิมพ์ ---
        // ใช้ 'input' event จะตอบสนองเร็วกว่า 'keyup' (รวมถึงการ paste ด้วย)
        confirmPasswordInput.on('input', checkPasswordMatch);

        // --- ผูก Event การ Submit ของฟอร์ม ---
        registerForm.on('submit', function(event) {

            // หยุดการส่งฟอร์มตามปกติก่อน เพื่อตรวจสอบ
            event.preventDefault();

            const password = passwordInput.val();
            const isPasswordValid = validatePasswordStrength(password);
            const passwordsMatch = checkPasswordMatch();

            // ตรวจสอบ Validation ของ HTML5 ทั้งหมด (เช่นช่องที่ required)
            // ถ้า form ไม่ valid, Bootstrap จะแสดง error ให้เอง
            if (this.checkValidity() === false) {
                event.stopPropagation();
                // ทำให้ Bootstrap แสดง feedback ของ validation
                $(this).addClass('was-validated');
            } else if (!isPasswordValid) {
                // ถ้ารหัสผ่านไม่ตรงตามเงื่อนไข
                Swal.fire({
                    icon: 'error',
                    title: '<?= service('request')->getLocale() === 'th' ? 'รหัสผ่านไม่ปลอดภัย' : 'Weak Password' ?>',
                    html: '<?= lang('Common.password_requirements') ?>'
                });
            } else if (!passwordsMatch) {
                // ถ้า validation อื่นๆ ผ่าน แต่รหัสผ่านไม่ตรงกัน
                Swal.fire({
                    icon: 'error',
                    title: '<?= service('request')->getLocale() === 'th' ? 'รหัสผ่านไม่ตรงกัน' : 'Passwords Do Not Match' ?>',
                    text: '<?= service('request')->getLocale() === 'th' ? 'กรุณาตรวจสอบรหัสผ่านและยืนยันรหัสผ่านอีกครั้ง' : 'Please check your password and confirm password again' ?>'
                });
            } else {
                // แสดง Confirmation Dialog ก่อนสมัคร
                Swal.fire({
                    title: '<?= service('request')->getLocale() === 'th' ? 'ยืนยันการสมัครสมาชิก' : 'Confirm Registration' ?>',
                    html: '<?= service('request')->getLocale() === 'th' ? 'คุณต้องการสมัครสมาชิกด้วยข้อมูลนี้หรือไม่?' : 'Do you want to register with this information?' ?>',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#16478C',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<?= service('request')->getLocale() === 'th' ? 'ยืนยัน' : 'Confirm' ?>',
                    cancelButtonText: '<?= service('request')->getLocale() === 'th' ? 'ยกเลิก' : 'Cancel' ?>'
                }).then((result) => {
                    if (result.isConfirmed) {
                        // แสดง Loading
                        Swal.fire({
                            title: '<?= service('request')->getLocale() === 'th' ? 'กำลังสมัครสมาชิก...' : 'Registering...' ?>',
                            html: '<?= service('request')->getLocale() === 'th' ? 'กรุณารอสักครู่' : 'Please wait' ?>',
                            allowOutsideClick: false,
                            allowEscapeKey: false,
                            didOpen: () => {
                                Swal.showLoading();
                            }
                        });

                        // Submit ฟอร์ม
                        registerForm[0].submit();
                    }
                });
            }
        });

    });
</script>
<?php $this->endSection(); ?>