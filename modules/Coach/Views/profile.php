<?php $this->extend('template/coach') ?>
<?= $this->section('title') ?>
<?= lang('Common.coach_profile_title') ?>
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
    .coach-profile-page {
        background-color: var(--coach-gray-100);
        min-height: calc(100vh - 200px);
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

    .coach-profile-card {
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

    .coach-btn-save {
        background-color: var(--coach-primary);
        border-color: var(--coach-primary);
        color: var(--coach-light);
        padding: 0.75rem 2rem;
        border-radius: var(--coach-border-radius);
        font-weight: 600;
        transition: var(--coach-transition);
    }

    .coach-btn-save:hover {
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

    /* Cert Card */
    .coach-cert-card {
        border: 1px solid var(--coach-gray-200);
        border-radius: var(--coach-border-radius);
        background: linear-gradient(135deg, #f8f9fc 0%, #ffffff 100%);
        padding: 1.25rem;
        position: relative;
        transition: var(--coach-transition);
    }

    .coach-cert-card:hover {
        border-color: var(--coach-secondary);
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
    }

    .coach-cert-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: linear-gradient(135deg, var(--coach-primary), #0a4a8f);
        color: var(--coach-light);
        font-weight: 700;
        font-size: 0.78rem;
        padding: 0.35rem 0.85rem;
        border-radius: 20px;
    }

    .coach-cert-badge.is-new {
        background: linear-gradient(135deg, #28a745, #20c997);
    }

    .coach-cert-delete-btn {
        border: none;
        background: transparent;
        color: #dc3545;
        font-size: 0.8rem;
        font-weight: 600;
        padding: 0.3rem 0.75rem;
        border-radius: 20px;
        transition: var(--coach-transition);
    }

    .coach-cert-delete-btn:hover {
        background: rgba(220, 53, 69, 0.1);
    }

    /* Cert Upload Button */
    .coach-cert-upload-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.4rem 1rem;
        border: 2px dashed var(--coach-gray-300);
        border-radius: var(--coach-border-radius);
        background: var(--coach-light);
        color: var(--coach-primary);
        font-size: 0.82rem;
        font-weight: 600;
        cursor: pointer;
        transition: var(--coach-transition);
    }

    .coach-cert-upload-btn:hover {
        border-color: var(--coach-secondary);
        background-color: rgba(243, 184, 25, 0.05);
    }

    .coach-cert-upload-btn.has-file {
        border-color: #28a745;
        border-style: solid;
        background-color: rgba(40, 167, 69, 0.05);
        color: #28a745;
    }

    .coach-cert-empty {
        text-align: center;
        padding: 3rem 1rem;
        color: var(--coach-gray-400);
    }

    .coach-cert-empty i {
        font-size: 3rem;
        opacity: 0.3;
        display: block;
        margin-bottom: 0.75rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .coach-profile-page {
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
<div class="coach-profile-page">
    <div class="container">
        <!-- Page Header -->
        <div class="coach-page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="coach-page-title h3">
                        <i class="fa fa-user me-3"></i>
                        <?= lang('Common.coach_profile') ?>
                    </h1>
                    <p class="coach-page-subtitle"><?= lang('Common.complete_profile_info') ?></p>
                </div>
                <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
                    <a href="<?= base_url('coach/dashboard') ?>" class="coach-btn-back">
                        <i class="fa fa-arrow-left me-2"></i><?= lang('Common.back') ?>
                    </a>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-lg-12 mx-auto">
                <div class="coach-profile-card">
                    <div class="coach-card-header">
                        <h6 class="m-0 text-white">
                            <i class="fa fa-edit me-3"></i>
                            <?= lang('Common.profile_information') ?>
                        </h6>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" action="<?= base_url('coach/profile/update') ?>" id="profileForm">
                            <!-- Profile Image -->
                            <div class="row mb-4">
                                <div class="col-12 text-center">
                                    <div class="profile-image-container mb-3">
                                        <?php
                                        // แสดงรูปภาพตามลำดับความสำคัญ: image_path ก่อน ถ้าไม่มีค่อยใช้ line_picture
                                        $displayImage = !empty($coach['image_path']) ? base_url($coach['image_path']) : (!empty($coach['line_picture']) ? base_url($coach['line_picture']) : '');
                                        ?>
                                        <?php if (!empty($displayImage)): ?>
                                            <img id="profileImage" src="<?= $displayImage ?>" class="coach-profile-image" onerror="this.onerror=null; this.src='<?= base_url('public/img/avatar.png') ?>'; this.style.objectFit='contain';">
                                        <?php else: ?>
                                            <div id="profileImage"
                                                class="coach-profile-image d-flex align-items-center justify-content-center bg-light mx-auto">
                                                <i class="fa fa-user fa-4x text-muted"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div>
                                        <label for="imageUpload" class="coach-btn-upload">
                                            <i class="fa fa-camera me-2"></i><?= lang('Common.change_picture') ?>
                                        </label>
                                        <input type="file" id="imageUpload" accept="image/*" style="display: none;">
                                        <input type="hidden" name="image_path" id="base64Image">
                                    </div>

                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control coach-form-control" id="first_name"
                                            name="first_name" value="<?= $coach['first_name'] ?>" placeholder="<?= lang('Common.first_name') ?>"
                                            required>
                                        <label for="first_name"><?= lang('Common.first_name') ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control coach-form-control" id="last_name"
                                            name="last_name" value="<?= $coach['last_name'] ?>" placeholder="<?= lang('Common.last_name') ?>"
                                            required>
                                        <label for="last_name"><?= lang('Common.last_name') ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control coach-form-control" id="nick_name"
                                            name="nick_name" value="<?= $coach['nick_name'] ?>" placeholder="<?= lang('Common.nick_name') ?>">
                                        <label for="nick_name"><?= lang('Common.nick_name') ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-control coach-form-control" id="nation" name="nation">
                                            <option value=""><?= lang('Common.select_nationality') ?></option>
                                            <option value="THAI" <?= $coach['nation'] == 'THAI' ? 'selected' : '' ?>>
                                                <?= lang('Common.thai') ?></option>
                                            <option value="OTHER" <?= $coach['nation'] == 'OTHER' ? 'selected' : '' ?>>
                                                <?= lang('Common.other') ?></option>
                                        </select>
                                        <label for="nation"><?= lang('Common.nationality') ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <select class="form-control coach-form-control" id="position" name="position">
                                            <option value=""><?= lang('Common.select_position') ?></option>
                                            <option value="team_manager" <?= ($coach['position'] ?? '') == 'team_manager' ? 'selected' : '' ?>><?= lang('Common.position_team_manager') ?></option>
                                            <option value="head_coach" <?= ($coach['position'] ?? '') == 'head_coach' ? 'selected' : '' ?>><?= lang('Common.position_head_coach') ?></option>
                                            <option value="coach" <?= ($coach['position'] ?? '') == 'coach' ? 'selected' : '' ?>><?= lang('Common.position_coach') ?></option>
                                            <option value="assistant_coach" <?= ($coach['position'] ?? '') == 'assistant_coach' ? 'selected' : '' ?>><?= lang('Common.position_assistant_coach') ?></option>
                                            <option value="team_staff" <?= ($coach['position'] ?? '') == 'team_staff' ? 'selected' : '' ?>><?= lang('Common.position_team_staff') ?></option>
                                        </select>
                                        <label for="position"><?= lang('Common.position_label') ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="text" class="form-control coach-form-control" id="id_card"
                                            name="id_card" value="<?= $coach['id_card'] ?>" placeholder="<?= lang('Common.id_card') ?>"
                                            maxlength="13" pattern="[0-9]{13}" title="<?= lang('Common.id_card_13_digits') ?>">
                                        <label for="id_card"><?= lang('Common.id_card') ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="date" class="form-control coach-form-control" id="birth_date"
                                            name="birth_date" value="<?= $coach['birth_date'] ?>" placeholder="<?= lang('Common.birth_date') ?>">
                                        <label for="birth_date"><?= lang('Common.birth_date') ?></label>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12 mb-3">
                                    <label class="form-label fw-bold" style="color: var(--coach-primary);"><?= lang('Common.id_card_image') ?></label>
                                    <input type="hidden" name="id_card_image_base64" id="idCardImageBase64">
                                    <input type="hidden" name="delete_id_card_image" id="deleteIdCardImage" value="0">
                                    <div class="d-flex align-items-center gap-3">
                                        <?php if (!empty($coach['id_card_image'])): ?>
                                        <div id="coachIdCardPreviewWrapper">
                                            <img id="coachIdCardPreview" src="<?= id_card_url($coach['id_card_image']) ?>"
                                                class="img-thumbnail" style="max-height: 120px; cursor: pointer;"
                                                onclick="viewCoachIdCard(this.src)" title="<?= lang('Common.view_id_card') ?>">
                                        </div>
                                        <?php else: ?>
                                        <div id="coachIdCardPreviewWrapper" style="display:none;">
                                            <img id="coachIdCardPreview" src="" class="img-thumbnail"
                                                style="max-height: 120px; cursor: pointer;"
                                                onclick="viewCoachIdCard(this.src)" title="<?= lang('Common.view_id_card') ?>">
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <label for="idCardFileInput" class="coach-btn-upload" style="cursor:pointer;">
                                                <i class="fa fa-upload me-1"></i><?= lang('Common.upload_id_card') ?>
                                            </label>
                                            <input type="file" id="idCardFileInput" accept="image/jpeg,image/png" style="display:none;">
                                            <br>
                                            <small class="text-muted"><?= lang('Common.id_card_image_hint') ?></small>
                                            <?php if (!empty($coach['id_card_image'])): ?>
                                            <br>
                                            <button type="button" class="btn btn-outline-danger btn-sm mt-1" id="btnDeleteCoachIdCard">
                                                <i class="fa fa-trash me-1"></i><?= lang('Common.delete_id_card_image') ?>
                                            </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control coach-form-control" id="height"
                                            name="height" value="<?= $coach['height'] ?>" placeholder="<?= lang('Common.height_cm') ?>"
                                            step="0.1">
                                        <label for="height"><?= lang('Common.height_cm') ?></label>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-floating mb-3">
                                        <input type="number" class="form-control coach-form-control" id="weight"
                                            name="weight" value="<?= $coach['weight'] ?>" placeholder="<?= lang('Common.weight_kg') ?>"
                                            step="0.1">
                                        <label for="weight"><?= lang('Common.weight_kg') ?></label>
                                    </div>
                                </div>
                                <!-- <div class="col-md-4">
                                    <div class="form-floating mb-3">
                                        <select class="form-control coach-form-control" id="dominant_foot" name="dominant_foot">
                                            <option value=""><?= lang('Common.cp_select_foot') ?></option>
                                            <option value="ขวา" <?= $coach['dominant_foot'] == 'ขวา' ? 'selected' : '' ?>><?= lang('Common.foot_right') ?></option>
                                            <option value="ซ้าย" <?= $coach['dominant_foot'] == 'ซ้าย' ? 'selected' : '' ?>><?= lang('Common.foot_left') ?></option>
                                            <option value="ทั้งสองเท้า" <?= $coach['dominant_foot'] == 'ทั้งสองเท้า' ? 'selected' : '' ?>><?= lang('Common.pe_foot_both') ?></option>
                                        </select>
                                        <label for="dominant_foot"><?= lang('Common.pe_dominant_foot') ?></label>
                                    </div>
                                </div> -->
                            </div>

                            <div class="text-end">
                                <button type="submit" class="coach-btn-save">
                                    <i class="fa fa-save me-2"></i><?= lang('Common.save') ?>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Certificates Section -->
        <div class="row mt-4">
            <div class="col-lg-12 mx-auto">
                <div class="coach-profile-card">
                    <div class="coach-card-header d-flex justify-content-between align-items-center">
                        <h6 class="m-0 text-white">
                            <i class="fa fa-certificate me-3"></i>
                            <?= lang('Common.certification_information') ?>
                        </h6>
                        <button type="button" class="btn btn-sm fw-bold px-3" id="addCertificate"
                            style="background: var(--coach-secondary); color: var(--coach-primary); border-radius: 20px;">
                            <i class="fa fa-plus me-1"></i> <?= lang('Common.add_new_certification') ?>
                        </button>
                    </div>
                    <div class="card-body p-4">
                        <form method="post" action="<?= base_url('coach/updateCertificates') ?>" id="certificatesForm" enctype="multipart/form-data">

                            <div id="certificatesContainer">
                                <?php if (empty($data)): ?>
                                <div class="coach-cert-empty" id="certEmptyState">
                                    <i class="fa fa-certificate"></i>
                                    <p class="mb-0"><?= lang('Common.no_data') ?></p>
                                </div>
                                <?php endif; ?>

                                <?php foreach ($data as $key => $cert): ?>
                                    <div class="coach-cert-card certificate-row mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                            <span class="coach-cert-badge">
                                                <i class="fa fa-certificate"></i> #<?= $key + 1 ?>
                                            </span>
                                            <button type="button" class="coach-cert-delete-btn remove-certificate">
                                                <i class="fa fa-trash me-1"></i> <?= lang('Common.delete_btn') ?>
                                            </button>
                                        </div>

                                        <input type="hidden" name="id[<?= $cert['id'] ?>]" value="<?= $cert['id'] ?>">
                                        <input type="hidden" name="existing_cert_image[<?= $cert['id'] ?>]" value="<?= $cert['cert_image'] ?? '' ?>">

                                        <div class="row g-3">
                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.association') ?></label>
                                                <select class="form-select coach-form-control"
                                                    name="organ_id[<?= $cert['id'] ?>]" required>
                                                    <option value=""><?= lang('Common.select_association') ?></option>
                                                    <?php foreach ($organ as $k => $d): ?>
                                                        <?php $selected = ($cert['organ_id'] == $d['id']) ? 'selected' : ''; ?>
                                                        <option <?= $selected ?> value="<?= $d['id'] ?>">
                                                            <?= $d['name'] ?> (<?= $d['label'] ?>)
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-6">
                                                <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.level') ?></label>
                                                <select class="form-select coach-form-control"
                                                    name="cert_level_id[<?= $cert['id'] ?>]" required>
                                                    <option value=""><?= lang('Common.select_level') ?></option>
                                                    <?php foreach ($cert_level as $k => $d): ?>
                                                        <?php $selected = ($cert['cert_level_id'] == $d['id']) ? 'selected' : ''; ?>
                                                        <option <?= $selected ?> value="<?= $d['id'] ?>">
                                                            <?= $d['name'] ?>
                                                        </option>
                                                    <?php endforeach; ?>
                                                </select>
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.license_code') ?></label>
                                                <input type="text" class="form-control coach-form-control"
                                                    name="license_code[<?= $cert['id'] ?>]"
                                                    value="<?= $cert['license_code'] ?? '' ?>"
                                                    placeholder="<?= lang('Common.license_code') ?>">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.completion_date') ?></label>
                                                <input type="date" class="form-control coach-form-control"
                                                    name="year[<?= $cert['id'] ?>]" value="<?= $cert['year'] ?? '' ?>">
                                            </div>

                                            <div class="col-md-4">
                                                <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.cert_image') ?></label>
                                                <div class="d-flex align-items-center gap-2">
                                                    <?php if (!empty($cert['cert_image'])): ?>
                                                        <img src="<?= base_url($cert['cert_image']) ?>" class="rounded border" style="width:40px;height:40px;object-fit:cover;cursor:pointer;" onclick="viewCertImage(this.src)" onerror="this.style.display='none'">
                                                    <?php endif; ?>
                                                    <label class="coach-cert-upload-btn flex-grow-1">
                                                        <input type="file" name="cert_image_file[<?= $cert['id'] ?>]" accept="image/jpeg,image/png,application/pdf" style="display:none;" onchange="onCertFileChange(this)">
                                                        <span><i class="fa fa-upload me-1"></i><?= lang('Common.upload') ?></span>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>

                            <div class="text-end mt-3">
                                <button type="submit" class="coach-btn-save">
                                    <i class="fa fa-save me-2"></i><?= lang('Common.save_changes') ?>
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
    let selectedImageData = null;
    let originalImageSrc = null;

    // --- ID Card Image Upload ---
    const langIdCardInvalidFileType = '<?= lang('Common.invalid_file_type') ?>';
    const langIdCardFileTooLarge = '<?= lang('Common.file_too_large') ?>';
    const langIdCardErrorOccurred = '<?= lang('Common.error_occurred') ?>';
    const langIdCardConfirmDelete = '<?= lang('Common.confirm_delete_id_card') ?>';
    const langIdCardDeleteBtn = '<?= lang('Common.delete_id_card_image') ?>';
    const langIdCardConfirm = '<?= lang('Common.confirm') ?>';
    const langIdCardCancel = '<?= lang('Common.cancel') ?>';
    const langIdCardView = '<?= lang('Common.view_id_card') ?>';

    document.getElementById('idCardFileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({ icon: 'error', title: langIdCardErrorOccurred, text: langIdCardInvalidFileType });
            this.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: langIdCardErrorOccurred, text: langIdCardFileTooLarge });
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('idCardImageBase64').value = ev.target.result;
            document.getElementById('coachIdCardPreview').src = ev.target.result;
            document.getElementById('coachIdCardPreviewWrapper').style.display = '';
            document.getElementById('deleteIdCardImage').value = '0';

            if (!document.getElementById('btnDeleteCoachIdCard')) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-danger btn-sm mt-1';
                btn.id = 'btnDeleteCoachIdCard';
                btn.innerHTML = '<i class="fa fa-trash me-1"></i>' + langIdCardDeleteBtn;
                btn.onclick = deleteCoachIdCard;
                document.getElementById('idCardFileInput').parentNode.querySelector('small').after(document.createElement('br'), btn);
            }
        };
        reader.readAsDataURL(file);
    });

    function viewCoachIdCard(src) {
        Swal.fire({
            title: langIdCardView,
            imageUrl: src,
            imageAlt: langIdCardView,
            showConfirmButton: true,
            confirmButtonText: langIdCardCancel,
            width: 'auto',
            imageWidth: '100%',
            imageHeight: 'auto'
        });
    }

    function deleteCoachIdCard() {
        Swal.fire({
            title: langIdCardDeleteBtn,
            text: langIdCardConfirmDelete,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langIdCardConfirm,
            cancelButtonText: langIdCardCancel
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('deleteIdCardImage').value = '1';
                document.getElementById('idCardImageBase64').value = '';
                document.getElementById('coachIdCardPreview').src = '';
                document.getElementById('coachIdCardPreviewWrapper').style.display = 'none';
                document.getElementById('idCardFileInput').value = '';
            }
        });
    }

    if (document.getElementById('btnDeleteCoachIdCard')) {
        document.getElementById('btnDeleteCoachIdCard').addEventListener('click', deleteCoachIdCard);
    }

    document.getElementById('imageUpload').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({
                    icon: 'error',
                    title: '<?= lang('Common.error_occurred') ?>',
                    text: '<?= lang('Common.file_size_exceed') ?>',
                    confirmButtonText: '<?= lang('Common.ok_button') ?>'
                });
                return;
            }

            // Validate file type
            const allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({
                    icon: 'error',
                    title: '<?= lang('Common.error_occurred') ?>',
                    text: '<?= lang('Common.invalid_file_type') ?>',
                    confirmButtonText: '<?= lang('Common.ok_button') ?>'
                });
                return;
            }

            const reader = new FileReader();
            reader.onload = function(e) {
                selectedImageData = e.target.result;

                // Store base64 data in hidden field for form submission
                document.getElementById('base64Image').value = selectedImageData;

                // Store original image src before changing
                const img = document.getElementById('profileImage');
                if (img.tagName === 'IMG') {
                    originalImageSrc = img.src;
                } else {
                    originalImageSrc = null; // No original image
                }

                // Show preview directly in profileImage
                if (img.tagName === 'IMG') {
                    img.src = selectedImageData;
                } else {
                    // Replace placeholder div with img for preview
                    const imgElement = document.createElement('img');
                    imgElement.id = 'profileImage';
                    imgElement.src = selectedImageData;
                    imgElement.className = 'coach-profile-image';
                    img.parentNode.replaceChild(imgElement, img);
                }

            };
            reader.readAsDataURL(file);
        }
    });


    // ID Card validation - only allow numbers and limit to 13 digits
    document.getElementById('id_card').addEventListener('input', function(e) {
        // Remove non-numeric characters
        this.value = this.value.replace(/[^0-9]/g, '');

        // Limit to 13 digits
        if (this.value.length > 13) {
            this.value = this.value.slice(0, 13);
        }
    });

    document.getElementById('id_card').addEventListener('blur', function() {
        const idCard = this.value;

        // Check if ID card is filled and validate length
        if (idCard && idCard.length !== 13) {
            this.setCustomValidity('<?= lang('Common.id_card_13_digits') ?>');
        } else {
            this.setCustomValidity('');
        }
    });

    // Show message function with SweetAlert
    function showMessage(message, type) {
        const icon = type === 'success' ? 'success' : 'error';
        const title = type === 'success' ? '<?= lang('Common.success_title') ?>' : '<?= lang('Common.error_occurred') ?>';

        Swal.fire({
            icon: icon,
            title: title,
            text: message,
            confirmButtonText: '<?= lang('Common.ok_button') ?>',
            timer: 3000,
            timerProgressBar: true
        });
    }

    // Calculate BMI automatically if BMI field exists
    if (document.getElementById('bmi')) {
        document.getElementById('height').addEventListener('input', calculateBMI);
        document.getElementById('weight').addEventListener('input', calculateBMI);

        function calculateBMI() {
            const height = parseFloat(document.getElementById('height').value);
            const weight = parseFloat(document.getElementById('weight').value);

            if (height && weight) {
                const heightInM = height / 100;
                const bmi = weight / (heightInM * heightInM);
                document.getElementById('bmi').value = bmi.toFixed(2);
            }
        }
    }

    // Show flash messages with SweetAlert
    document.addEventListener('DOMContentLoaded', function() {
        <?php if (session()->getFlashdata('success')): ?>
            Swal.fire({
                icon: 'success',
                title: '<?= lang('Common.success_title') ?>',
                text: '<?= session()->getFlashdata('success') ?>',
                confirmButtonText: '<?= lang('Common.ok_button') ?>',
                timer: 3000,
                timerProgressBar: true
            });
        <?php elseif (session()->getFlashdata('error')): ?>
            Swal.fire({
                icon: 'error',
                title: '<?= lang('Common.error_occurred') ?>',
                text: '<?= session()->getFlashdata('error') ?>',
                confirmButtonText: '<?= lang('Common.ok_button') ?>'
            });
        <?php endif; ?>
    });

    // Certificates management
    let certificateIndex = Date.now(); // Use timestamp for unique IDs

    // View cert image in SweetAlert
    function viewCertImage(src) {
        Swal.fire({
            title: '<?= lang('Common.cert_image') ?>',
            imageUrl: src,
            imageAlt: '<?= lang('Common.cert_image') ?>',
            showConfirmButton: true,
            confirmButtonText: '<?= lang('Common.close') ?>',
            width: 'auto',
            imageWidth: '100%',
            imageHeight: 'auto'
        });
    }

    // Handle cert file change - show preview and update button style
    function onCertFileChange(input) {
        const label = input.closest('.coach-cert-upload-btn');
        const wrapper = input.closest('.d-flex');

        if (input.files && input.files[0]) {
            label.classList.add('has-file');
            label.querySelector('span').innerHTML = '<i class="fa fa-check me-1"></i><?= lang('Common.file_selected') ?>';

            // Show preview if image
            const file = input.files[0];
            if (file.type.startsWith('image/')) {
                let previewImg = wrapper.querySelector('.cert-preview-img');
                if (!previewImg) {
                    previewImg = document.createElement('img');
                    previewImg.className = 'cert-preview-img rounded border';
                    previewImg.style.cssText = 'width:44px;height:44px;object-fit:cover;cursor:pointer;';
                    previewImg.onclick = function() { viewCertImage(this.src); };
                    wrapper.insertBefore(previewImg, wrapper.firstChild);
                }
                const reader = new FileReader();
                reader.onload = function(e) { previewImg.src = e.target.result; };
                reader.readAsDataURL(file);
            }
        } else {
            label.classList.remove('has-file');
            label.querySelector('span').innerHTML = '<i class="fa fa-upload me-1"></i><?= lang('Common.upload') ?>';
        }
    }

    // Add new certificate row
    document.getElementById('addCertificate').addEventListener('click', function() {
        const certificatesContainer = document.getElementById('certificatesContainer');
        const newId = certificateIndex++;

        // Remove empty state if exists
        const emptyState = document.getElementById('certEmptyState');
        if (emptyState) emptyState.remove();

        const certNum = certificatesContainer.querySelectorAll('.certificate-row').length + 1;
        const newCertificateRow = `
        <div class="coach-cert-card certificate-row mb-3">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <span class="coach-cert-badge is-new">
                    <i class="fa fa-certificate"></i> #${certNum} <small>(<?= lang('Common.new') ?>)</small>
                </span>
                <button type="button" class="coach-cert-delete-btn remove-certificate">
                    <i class="fa fa-trash me-1"></i> <?= lang('Common.delete_btn') ?>
                </button>
            </div>

            <input type="hidden" name="id[${newId}]" value="">

            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.association') ?></label>
                    <select class="form-select coach-form-control" name="organ_id[${newId}]" required>
                        <option value=""><?= lang('Common.select_association') ?></option>
                        <?php foreach ($organ as $k => $d): ?>
                            <option value="<?= $d['id'] ?>">
                                <?= $d['name'] ?> (<?= $d['label'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.level') ?></label>
                    <select class="form-select coach-form-control" name="cert_level_id[${newId}]" required>
                        <option value=""><?= lang('Common.select_level') ?></option>
                        <?php foreach ($cert_level as $k => $d): ?>
                            <option value="<?= $d['id'] ?>">
                                <?= $d['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.license_code') ?></label>
                    <input type="text" class="form-control coach-form-control"
                           name="license_code[${newId}]"
                           placeholder="<?= lang('Common.license_code') ?>">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.completion_date') ?></label>
                    <input type="date" class="form-control coach-form-control"
                           name="year[${newId}]">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-bold small" style="color: var(--coach-primary);"><?= lang('Common.cert_image') ?></label>
                    <div class="d-flex align-items-center gap-2">
                        <label class="coach-cert-upload-btn flex-grow-1">
                            <input type="file" name="cert_image_file[${newId}]" accept="image/jpeg,image/png,application/pdf" style="display:none;" onchange="onCertFileChange(this)">
                            <span><i class="fa fa-upload me-1"></i><?= lang('Common.upload') ?></span>
                        </label>
                    </div>
                </div>
            </div>
        </div>
    `;

        certificatesContainer.insertAdjacentHTML('beforeend', newCertificateRow);
    });

    // Remove certificate row + re-number badges
    document.addEventListener('click', function(e) {
        if (e.target.closest('.remove-certificate')) {
            e.target.closest('.certificate-row').remove();
            // Re-number badges
            document.querySelectorAll('#certificatesContainer .certificate-row').forEach(function(row, i) {
                const badge = row.querySelector('.coach-cert-badge');
                if (badge) {
                    const isNew = badge.classList.contains('is-new');
                    badge.innerHTML = '<i class="fa fa-certificate"></i> #' + (i + 1) + (isNew ? ' <small>(<?= lang("Common.new") ?>)</small>' : '');
                }
            });
            // Show empty state if no certs
            const container = document.getElementById('certificatesContainer');
            if (container.querySelectorAll('.certificate-row').length === 0) {
                container.innerHTML = '<div class="coach-cert-empty" id="certEmptyState"><i class="fa fa-certificate"></i><p class="mb-0"><?= lang("Common.no_data") ?></p></div>';
            }
        }
    });

    // Add SweetAlert confirmation to profile form
    document.getElementById('profileForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const idCard = document.getElementById('id_card').value;

        // Validate ID card if filled
        if (idCard && idCard.length !== 13) {
            Swal.fire({
                icon: 'error',
                title: '<?= lang('Common.error_occurred') ?>',
                text: '<?= lang('Common.id_card_13_digits') ?>',
                confirmButtonText: '<?= lang('Common.ok_button') ?>'
            });
            return;
        }

        Swal.fire({
            title: '<?= lang('Common.confirm_save') ?>',
            text: '<?= lang('Common.confirm_save_profile') ?>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang('Common.save') ?>',
            cancelButtonText: '<?= lang('Common.cancel') ?>',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: '<?= lang('Common.saving_profile') ?>',
                    html: '<?= lang('Common.please_wait_moment') ?>',
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

    // Add SweetAlert confirmation to certificates form
    document.getElementById('certificatesForm').addEventListener('submit', function(e) {
        e.preventDefault();

        Swal.fire({
            title: '<?= lang('Common.confirm_save') ?>',
            text: '<?= lang('Common.confirm_save_certifications') ?>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang('Common.save') ?>',
            cancelButtonText: '<?= lang('Common.cancel') ?>',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                Swal.fire({
                    title: '<?= lang('Common.saving_certifications') ?>',
                    html: '<?= lang('Common.please_wait_moment') ?>',
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
</script>
<?= $this->endSection() ?>