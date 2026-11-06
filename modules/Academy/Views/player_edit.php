<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('academy/players/' . $academy_id) ?>"><?= lang('Common.players') ?></a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('academy/player-view/' . $academy_id . '/' . $player['id']) ?>"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></a></li>
                    <li class="breadcrumb-item active"><?= lang('Common.edit_information') ?></li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Player Header -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body py-3 px-4 d-flex align-items-center gap-3">
            <img id="profile_preview" src="<?= !empty($player['image_path']) ? base_url($player['image_path']) : base_url('public/img/img_profile.jpg') ?>"
                 class="rounded-circle border border-2 shadow-sm" style="width:64px;height:64px;object-fit:cover;">
            <div>
                <h5 class="fw-bold mb-0 text-primary"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></h5>
                <small class="text-muted"><?php if(!empty($player['nick_name'])): ?>"<?= esc($player['nick_name']) ?>"<?php endif; ?></small>
            </div>
            <div class="ms-auto d-flex gap-2">
                <button type="button" class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#profileImageModal">
                    <i class="fas fa-camera me-1"></i> <?= lang('Common.pe_change_photo') ?>
                </button>
                <a href="<?= base_url('academy/players/' . $academy_id) ?>" class="btn btn-light rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i> <?= lang('Common.back') ?>
                </a>
            </div>
        </div>
    </div>

    <!-- Modal: Profile Image Upload & Crop -->
    <div class="modal fade" id="profileImageModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="cropperModalLabel"><i class="fas fa-user-circle me-2"></i><?= lang('Common.pe_upload_crop_title') ?></h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="uploadImage" class="form-label"><?= lang('Common.pe_choose_image') ?></label>
                        <input type="file" id="uploadImage" class="form-control" accept="image/*" />
                    </div>
                    <div class="row">
                        <div class="col-md-7">
                            <div class="img-container" style="max-height: 400px;">
                                <img id="cropImage" style="max-width: 100%; display: none;" />
                            </div>
                        </div>
                        <div class="col-md-5 text-center">
                            <div class="fw-bold mb-2"><?= lang('Common.pe_image_preview_ratio') ?></div>
                            <div id="preview" class="border rounded-circle shadow-sm"
                                style="width: 200px; height: 200px; overflow: hidden; margin: auto;"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                    <button type="button" id="cropButton" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i> <?= lang('Common.pe_save_image') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <?php if (session()->getFlashdata('success')): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-4" role="alert">
            <i class="fas fa-check-circle me-2"></i><?= session()->getFlashdata('success') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-4" role="alert">
            <i class="fas fa-exclamation-circle me-2"></i><?= session()->getFlashdata('error') ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Section A: ข้อมูลส่วนตัว -->
    <?php if ($canEditAll): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="fw-bold mb-0 text-dark">
                <span class="badge bg-primary me-2">A</span><?= lang('Common.personal_information') ?>
                <small class="text-muted fw-normal ms-2" style="font-size:0.75rem;"><i class="fas fa-lock me-1"></i><?= lang('Common.pe_creator_superadmin_only') ?></small>
            </h5>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.name') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="sA_first_name" value="<?= esc($player['first_name']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.last_name') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="sA_last_name" value="<?= esc($player['last_name']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.nick_name') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="sA_nick_name" value="<?= esc($player['nick_name'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.birth_date') ?></label>
                    <input type="date" class="form-control bg-light border-0" id="sA_birth_date" value="<?= esc($player['birth_date'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.id_card_label') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="sA_id_card" value="<?= esc($player['id_card'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.nationality') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="sA_nation" value="<?= esc($player['nation'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.pe_gender') ?></label>
                    <div class="d-flex gap-3 mt-2">
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sA_sex" id="sA_sex_m" value="M" <?= (isset($player['sex']) && $player['sex'] === 'M') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="sA_sex_m">
                                <i class="fas fa-male text-primary me-1"></i><?= lang('Common.sex_m') ?>
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="sA_sex" id="sA_sex_w" value="W" <?= (isset($player['sex']) && $player['sex'] === 'W') ? 'checked' : '' ?>>
                            <label class="form-check-label" for="sA_sex_w">
                                <i class="fas fa-female text-danger me-1"></i><?= lang('Common.sex_w') ?>
                            </label>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="border rounded-4 p-3 bg-light-subtle">
                        <label class="form-label fw-semibold text-muted small d-block mb-3"><?= lang('Common.pe_contract') ?></label>
                        <div class="d-flex flex-wrap gap-3 mb-3">
                            <div class="form-check">
                                <input class="form-check-input contract-radio" type="radio" name="sA_contract" id="sA_contract_1" value="1" <?= ((string)($player['contract'] ?? '') === '1') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="sA_contract_1">Academy</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input contract-radio" type="radio" name="sA_contract" id="sA_contract_2" value="2" <?= ((string)($player['contract'] ?? '') === '2') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="sA_contract_2">Scholarships</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input contract-radio" type="radio" name="sA_contract" id="sA_contract_3" value="3" <?= ((string)($player['contract'] ?? '') === '3') ? 'checked' : '' ?>>
                                <label class="form-check-label" for="sA_contract_3">Soccer School</label>
                            </div>
                        </div>
                        <div id="contractFieldsGroup" style="<?= !empty($player['contract']) ? '' : 'display:none;' ?>">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.start_date') ?></label>
                                    <input type="date" class="form-control bg-white border-0" id="sA_contract_start" value="<?= esc($player['contract_start'] ?? '') ?>">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.end_date') ?></label>
                                    <input type="date" class="form-control bg-white border-0" id="sA_contract_end" value="<?= esc($player['contract_end'] ?? '') ?>">
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.note') ?></label>
                                    <textarea class="form-control bg-white border-0" id="sA_contract_note" rows="3"><?= esc($player['contract_note'] ?? '') ?></textarea>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.pe_id_card_photo') ?></label>
                    <div class="mb-3">
                        <?php if (!empty($player['id_card_image'])): ?>
                        <div class="card border-0 shadow-sm d-inline-block">
                            <div class="card-body p-2">
                                <img id="id_card_preview" src="<?= id_card_url($player['id_card_image']) ?>" alt="ID Card" class="rounded-3" style="max-width: 400px; max-height: 250px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="badge bg-success"><i class="fas fa-check-circle me-1"></i><?= lang('Common.pe_id_card_photo_exists') ?></span>
                        </div>
                        <?php else: ?>
                        <div class="text-center p-4 bg-light rounded-4 border" style="max-width: 400px;">
                            <i class="fas fa-id-card fa-4x text-muted mb-2 opacity-50"></i>
                            <p class="text-muted mb-0 fw-semibold"><?= lang('Common.pe_id_card_photo_none') ?></p>
                            <p class="text-muted small mb-0"><?= lang('Common.pe_upload_below') ?></p>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="row align-items-center g-2">
                        <div class="col-auto">
                            <input type="file" class="form-control" id="sA_id_card_image" accept="image/*" style="min-width: 300px;">
                        </div>
                        <div class="col">
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i><?= lang('Common.pe_file_support_hint') ?></small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-3">
                <!-- Section save buttons removed, replaced by single button at bottom -->
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Section B: ข้อมูลกายภาพ -->
    <?php if ($canEditPhysical || $canEditAll): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="fw-bold mb-0 text-dark">
                <span class="badge bg-success me-2">B</span><?= lang('Common.physical_info') ?>
                <small class="text-muted fw-normal ms-2" style="font-size:0.75rem;"><i class="fas fa-running me-1"></i><?= lang('Common.pe_creator_or_academy_coach') ?></small>
            </h5>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.height_cm') ?></label>
                    <input type="number" step="0.1" class="form-control bg-light border-0" id="sB_height" value="<?= esc($player['height'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.weight_kg') ?></label>
                    <input type="number" step="0.1" class="form-control bg-light border-0" id="sB_weight" value="<?= esc($player['weight'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small">BMI</label>
                    <input type="number" step="0.01" class="form-control bg-light border-0" id="sB_bmi" value="<?= esc($player['bmi'] ?? '') ?>" readonly>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.pe_dominant_foot') ?></label>
                    <select class="form-select bg-light border-0" id="sB_dominant_foot">
                        <option value=""><?= lang('Common.pe_select_option') ?></option>
                        <option value="left" <?= ($player['dominant_foot'] ?? '') === 'left' ? 'selected' : '' ?>><?= lang('Common.foot_left') ?></option>
                        <option value="right" <?= ($player['dominant_foot'] ?? '') === 'right' ? 'selected' : '' ?>><?= lang('Common.foot_right') ?></option>
                        <option value="both" <?= ($player['dominant_foot'] ?? '') === 'both' ? 'selected' : '' ?>><?= lang('Common.pe_foot_both') ?></option>
                    </select>
                </div>
            </div>
            <div class="mt-3 d-flex align-items-center gap-3">
                <a href="<?= base_url('academy/player-physical-history/' . $academy_id . '/' . $player['id']) ?>" class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-history me-1"></i> <?= lang('Common.pe_view_physical_history') ?>
                </a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Section C: เบอร์เสื้อ + ตำแหน่ง (per academy) -->
    <?php if ($canEditTeam || $canEditAll): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="fw-bold mb-0 text-dark">
                <span class="badge bg-warning text-dark me-2">C</span><?= lang('Common.pe_team_position') ?>
                <small class="text-muted fw-normal ms-2" style="font-size:0.75rem;"><i class="fas fa-shield-alt me-1"></i><?= lang('Common.pe_academy_specific') ?></small>
            </h5>
        </div>
        <div class="card-body px-4 pb-4">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.pe_shirt_number') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="sC_shirt_number" value="<?= esc($enrollment['shirt_number'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.pe_shirt_size') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="sC_shirt_size" value="<?= esc($enrollment['shirt_size'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.team') ?></label>
                    <select class="form-select bg-light border-0" id="sC_team_id" disabled>
                        <option value="0"><?= lang('Common.pe_no_team') ?></option>
                        <?php foreach ($teams as $t): ?>
                        <option value="<?= $t['id'] ?>" <?= ($enrollment['team_id'] ?? 0) == $t['id'] ? 'selected' : '' ?>>
                            <?= esc($t['name']) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted"><?= lang('Common.pe_change_team_hint') ?></small>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.pe_main_position') ?></label>
                    <?php
                    // ป้ายชื่อตำแหน่งตามภาษา (name_en เมื่อ EN, ไม่งั้น name_th)
                    $__peEn = function_exists('get_current_language') && get_current_language() === 'en';
                    $posLabel = fn($p) => esc($__peEn && !empty($p['name_en']) ? $p['name_en'] : ($p['name_th'] ?? ''));
                    ?>
                    <select class="form-select bg-light border-0" id="sC_position_id">
                        <option value="0"><?= lang('Common.pe_select_position') ?></option>
                        <?php foreach ($positions as $pos): ?>
                        <option value="<?= $pos['id'] ?>" <?= ($enrollment['position_id'] ?? 0) == $pos['id'] ? 'selected' : '' ?>>
                            <?= $posLabel($pos) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold text-muted small"><?= lang('Common.pe_sub_position') ?></label>
                    <select class="form-select bg-light border-0" id="sC_sub_position_ids" multiple>
                        <?php
                        $subIds = [];
                        if (!empty($enrollment['sub_position_ids'])) {
                            $subIds = json_decode($enrollment['sub_position_ids'], true) ?? [];
                        }
                        foreach ($positions as $pos):
                        ?>
                        <option value="<?= $pos['id'] ?>" <?= in_array($pos['id'], $subIds) ? 'selected' : '' ?>>
                            <?= $posLabel($pos) ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                    <small class="text-muted"><?= lang('Common.pe_ctrl_multiselect') ?></small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Unified Save Button -->
    <div class="card shadow-sm border-0 rounded-4 mb-5">
        <div class="card-body p-4 d-flex justify-content-between align-items-center">
            <div class="text-muted small">
                <i class="fas fa-info-circle me-1"></i> <?= lang('Common.pe_save_all_hint') ?>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('academy/players/' . $academy_id) ?>" class="btn btn-light rounded-pill px-4"><?= lang('Common.cancel') ?></a>
                <button class="btn btn-primary rounded-pill px-5 fw-bold" onclick="saveAll()">
                    <i class="fas fa-save me-2"></i> <?= lang('Common.pe_save_all') ?>
                </button>
            </div>
        </div>
    </div>

</div>

<div id="toastContainer" class="toast-container position-fixed bottom-0 end-0 p-3"></div>

<?php $this->endSection(); ?>

<?php $this->section('css'); ?>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.css">
<style>
    .img-container {
        width: 100%;
        min-height: 300px;
        background-color: #f7f7f7;
        display: flex;
        justify-content: center;
        align-items: center;
        overflow: hidden;
    }
</style>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/cropperjs/1.5.13/cropper.min.js"></script>
<script>
const academyId = <?= (int)$academy_id ?>;
const playerId  = <?= (int)$player['id'] ?>;
const saveUrl   = '<?= base_url('academy/player-save/' . $academy_id) ?>';
const base_url = '<?= base_url() ?>';

// --- Cropper.js Logic ---
const uploadImageInput = document.getElementById("uploadImage");
const cropImageElement = document.getElementById("cropImage");
const cropButton = document.getElementById("cropButton");
let cropper;

uploadImageInput?.addEventListener("change", (event) => {
    const file = event.target.files[0];
    if (!file) return;

    const reader = new FileReader();
    reader.onload = (e) => {
        cropImageElement.src = e.target.result;
        cropImageElement.style.display = "block";
        if (cropper) {
            cropper.destroy();
        }
        cropper = new Cropper(cropImageElement, {
            aspectRatio: 1,
            viewMode: 1,
            autoCropArea: 1,
            preview: "#preview",
        });
    };
    reader.readAsDataURL(file);
});

cropButton?.addEventListener("click", () => {
    if (!cropper) {
        Swal.fire({ icon: 'warning', title: '<?= lang('Common.warning') ?>', text: '<?= lang('Common.pe_crop_before_save') ?>' });
        return;
    }

    const canvas = cropper.getCroppedCanvas({
        width: 400,
        height: 400,
    });
    
    const croppedImageBase64 = canvas.toDataURL("image/jpeg", 0.9);
    uploadProfileImage(croppedImageBase64);
});

// Upload Profile Image (Base64)
function uploadProfileImage(base64Image) {
    const formData = new FormData();
    formData.append('player_id', playerId);
    formData.append('academy_id', academyId);
    formData.append('base64_image', base64Image); // Matches the field name used in common save logic if applicable

    // Use specific endpoint for image upload if it exists, or handle in common save
    fetch(base_url + '/academy/player-upload-image', {
        method: 'POST',
        body: formData
    })
    .then(res => res.json())
    .then(resp => {
        if (resp.status === 'success' && resp.image_path) {
            // Update profile preview
            document.getElementById('profile_preview').src = base_url + '/' + resp.image_path;

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('profileImageModal'));
            modal.hide();

            // Show success message
            Swal.fire({ icon: 'success', title: '<?= lang('Common.success_title') ?>', text: '<?= lang('Common.pe_photo_upload_success') ?>', timer: 1500, showConfirmButton: false });

            // Reset cropper
            if (cropper) {
                cropper.destroy();
                cropper = null;
            }
            cropImageElement.src = '';
            cropImageElement.style.display = 'none';
            uploadImageInput.value = '';
        } else {
            Swal.fire({ icon: 'error', title: '<?= lang('Common.error_occurred') ?>', text: resp.msg || '<?= lang('Common.pe_photo_upload_fail') ?>' });
        }
    })
    .catch(err => {
        console.error('Upload error:', err);
        Swal.fire({ icon: 'error', title: '<?= lang('Common.error_occurred') ?>', text: '<?= lang('Common.pe_photo_upload_fail') ?>' });
    });
}

// ID Card Image Preview
document.getElementById('sA_id_card_image')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            // Hide placeholder
            const placeholder = document.getElementById('id_card_placeholder');
            if (placeholder) {
                placeholder.style.display = 'none';
            }

            // Show or create preview image
            let previewContainer = document.getElementById('id_card_preview_container');
            if (!previewContainer) {
                // สร้างคอนเทนเนอร์ใหม่ถ้ายังไม่มี
                /*
                const html = `
                    <div id="id_card_preview_container" class="card border-0 shadow-sm d-inline-block mb-2">
                        <div class="card-body p-2">
                            <img id="id_card_preview" src="" alt="ID Card Preview" class="rounded-3" style="max-width: 400px; max-height: 250px; object-fit: contain;">
                        </div>
                    </div>
                    <div id="id_card_badge" class="mt-2 mb-3">
                        <span class="badge bg-primary"><i class="fas fa-image me-1"></i><?= lang('Common.pe_image_preview') ?></span>
                    </div>
                `;*/

                const html = `
                    <div id="id_card_badge" class="mt-2 mb-3">
                        <span class="badge bg-primary"><i class="fas fa-image me-1"></i><?= lang('Common.pe_image_preview') ?></span>
                    </div>
                `;

                const parent = document.getElementById('sA_id_card_image').closest('.col-md-12');
                parent.querySelector('.mb-3').insertAdjacentHTML('beforeend', html);
            }

            // ย้ายมาไว้ตรงนี้! ไม่ว่าจะสร้างใหม่หรือมีอยู่แล้ว ก็ต้องเอารูปไปใส่เสมอกัน
            document.getElementById('id_card_preview').src = e.target.result;
        };
        reader.readAsDataURL(file);
    }
});

// BMI auto-calc
document.getElementById('sB_height')?.addEventListener('input', calcBMI);
document.getElementById('sB_weight')?.addEventListener('input', calcBMI);
function calcBMI() {
    const h = parseFloat(document.getElementById('sB_height')?.value) / 100;
    const w = parseFloat(document.getElementById('sB_weight')?.value);
    const bmiEl = document.getElementById('sB_bmi');
    if (bmiEl && h > 0 && w > 0) {
        bmiEl.value = (w / (h * h)).toFixed(2);
    }
}

document.querySelectorAll('.contract-radio').forEach(el => {
    el.addEventListener('change', toggleContractFields);
});

function toggleContractFields() {
    const contractRadio = document.querySelector('input[name="sA_contract"]:checked');
    const group = document.getElementById('contractFieldsGroup');
    if (!group) return;

    const hasContract = !!contractRadio;
    group.style.display = hasContract ? '' : 'none';

    if (!hasContract) {
        const startEl = document.getElementById('sA_contract_start');
        const endEl = document.getElementById('sA_contract_end');
        const noteEl = document.getElementById('sA_contract_note');
        if (startEl) startEl.value = '';
        if (endEl) endEl.value = '';
        if (noteEl) noteEl.value = '';
    }
}

function saveAll() {
    let formData = new FormData();
    formData.append('player_id', playerId);
    formData.append('section', 'ALL');

    // Section A Data
    if (document.getElementById('sA_first_name')) {
        formData.append('first_name', document.getElementById('sA_first_name').value);
        formData.append('last_name', document.getElementById('sA_last_name').value);
        formData.append('nick_name', document.getElementById('sA_nick_name').value);
        formData.append('birth_date', document.getElementById('sA_birth_date').value);
        formData.append('id_card', document.getElementById('sA_id_card').value);
        formData.append('nation', document.getElementById('sA_nation').value);

        const sexRadio = document.querySelector('input[name="sA_sex"]:checked');
        formData.append('sex', sexRadio ? sexRadio.value : '');

        const contractRadio = document.querySelector('input[name="sA_contract"]:checked');
        formData.append('contract', contractRadio ? contractRadio.value : '');
        formData.append('contract_start', document.getElementById('sA_contract_start').value);
        formData.append('contract_end', document.getElementById('sA_contract_end').value);
        formData.append('contract_note', document.getElementById('sA_contract_note').value);

        const idCardFile = document.getElementById('sA_id_card_image');
        if (idCardFile && idCardFile.files[0]) {
            formData.append('id_card_image', idCardFile.files[0]);
        }
    }

    // Section B Data
    if (document.getElementById('sB_height')) {
        formData.append('height', document.getElementById('sB_height').value);
        formData.append('weight', document.getElementById('sB_weight').value);
        formData.append('bmi', document.getElementById('sB_bmi').value);
        formData.append('dominant_foot', document.getElementById('sB_dominant_foot').value);
    }

    // Section C Data
    if (document.getElementById('sC_shirt_number')) {
        formData.append('shirt_number', document.getElementById('sC_shirt_number').value);
        formData.append('shirt_size', document.getElementById('sC_shirt_size').value);
        formData.append('position_id', document.getElementById('sC_position_id').value);

        const subSel = document.getElementById('sC_sub_position_ids');
        if (subSel) {
            const selectedSubs = Array.from(subSel.selectedOptions).map(o => o.value);
            selectedSubs.forEach(sub => formData.append('sub_position_ids[]', sub));
        }
    }

    fetch(saveUrl, {
        method: 'POST',
        body: formData
    })
    .then(r => r.json())
    .then(resp => {
        showToast(resp.status === 'success', resp.message);
        if (resp.status === 'success') {
            setTimeout(() => {
                window.location.replace(resp.redirect_url || '<?= base_url('academy/players/' . $academy_id) ?>');
            }, 1000);
        }
    })
    .catch(() => showToast(false, '<?= lang('Common.error_occurred') ?>'));
}

function showToast(success, message) {
    const id = 'toast_' + Date.now();
    const cls = success ? 'bg-success' : 'bg-danger';
    const html = `
        <div id="${id}" class="toast align-items-center text-white ${cls} border-0 rounded-4 shadow" role="alert">
            <div class="d-flex">
                <div class="toast-body fw-semibold"><i class="fas fa-${success ? 'check' : 'times'}-circle me-2"></i>${message}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
            </div>
        </div>`;
    document.getElementById('toastContainer').insertAdjacentHTML('beforeend', html);
    const toastEl = document.getElementById(id);
    new bootstrap.Toast(toastEl, { delay: 3000 }).show();
}
</script>
<?php $this->endSection(); ?>
