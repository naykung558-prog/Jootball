<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">

        <?php echo view("Modules\Admin\Views\Coach\subnav.php"); ?>

        <form action="<?= base_url('admin/coach/save-coach') ?>" method="POST"
            enctype="multipart/form-data" class="once-only" autocomplete="off">

            <div class="row justify-content-center mb-5">
                <div class="col-md-4 text-center">
                    <div class="position-relative d-inline-block">
                        <?php if (!empty($data['image_path']) && file_exists(base_url($data['image_path']))) { ?>
                            <img id="personel_image_display" src="<?= base_url($data['image_path']) ?>" alt="" class="img-fluid border-4 border-white shadow rounded-circle" style="width: 180px; height: 180px; object-fit: cover;">
                        <?php } else { ?>
                            <img id="personel_image_display" src="<?= base_url('public/img/no-image.png') ?>" alt="" class="img-fluid border-4 border-white shadow rounded-circle" style="width: 180px; height: 180px; object-fit: cover;">
                        <?php } ?>
                        <button type="button" class="btn btn-primary btn-icon rounded-circle position-absolute bottom-0 end-0 mb-2 me-2 shadow-sm" data-bs-toggle="modal" data-bs-target="#cropperModal" title="<?= lang('Common.upload_image') ?>">
                            <i class="ti ti-camera fs-4"></i>
                        </button>
                    </div>
                    <input type="hidden" id="image_path" name="base64_image">
                </div>
            </div>

            <!-- Image Cropper Modal -->
            <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
                <div class="modal-dialog modal-lg modal-dialog-centered">
                    <div class="modal-content border-0 shadow-lg">
                        <div class="modal-header bg-light">
                            <h5 class="modal-title fw-bold" id="cropperModalLabel"><i class="ti ti-photo-edit me-2"></i><?= lang('Common.upload_and_crop_image') ?></h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <div class="input-group">
                                        <input type="file" id="uploadImage" class="form-control" accept="image/*" />
                                        <label class="input-group-text" for="uploadImage"><i class="ti ti-upload"></i></label>
                                    </div>
                                </div>
                                <div class="col-lg-8">
                                    <div class="cropper-container bg-dark rounded overflow-hidden d-flex align-items-center justify-content-center" style="height: 400px;">
                                        <img id="cropImage" style="max-width: 100%; max-height: 100%; display: none;" />
                                        <span class="text-white-50" id="placeholder-text"><?= lang('Common.select_image_to_crop') ?></span>
                                    </div>
                                </div>
                                <div class="col-lg-4">
                                    <div class="preview-container border rounded overflow-hidden mx-auto" id="preview" style="width: 200px; height: 200px; background: #f8f9fa;"></div>
                                    <div class="text-center mt-2 text-muted small"><?= lang('Common.image_preview') ?></div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer bg-light">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                            <button type="button" id="cropButton" class="btn btn-primary px-4"><i class="ti ti-check me-2"></i><?= lang('Common.confirm_image') ?></button>
                        </div>
                    </div>
                </div>
            </div>

            <input type="hidden" name="id" value="<?= (!empty($data['id'])) ? $data['id'] : '' ?>">

            <div class="row g-3">
                <div class="col-12">
                    <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="ti ti-id me-2"></i><?= lang('Common.personal_information') ?></h5>
                </div>

                <div class="col-md-4">
                    <label for="first_name" class="form-label fw-bold"><?= lang('Common.first_name') ?> <span class="text-danger">*</span></label>
                    <input type="text" name="first_name" id="first_name" value="<?= (!empty($data['first_name'])) ? $data['first_name'] : '' ?>" class="form-control" required placeholder="<?= lang('Common.enter_first_name') ?>">
                </div>
                <div class="col-md-4">
                    <label for="last_name" class="form-label fw-bold"><?= lang('Common.last_name') ?></label>
                    <input type="text" name="last_name" id="last_name" value="<?= (!empty($data['last_name'])) ? $data['last_name'] : '' ?>" class="form-control" placeholder="<?= lang('Common.enter_last_name') ?>">
                </div>
                <div class="col-md-4">
                    <label for="nick_name" class="form-label fw-bold"><?= lang('Common.nick_name') ?></label>
                    <input type="text" name="nick_name" id="nick_name" value="<?= (!empty($data['nick_name'])) ? $data['nick_name'] : '' ?>" class="form-control" required placeholder="<?= lang('Common.enter_nickname') ?>">
                </div>

                <div class="col-md-4">
                    <label for="nation" class="form-label fw-bold"><?= lang('Common.nationality') ?></label>
                    <select name="nation" id="nation" class="form-select">
                        <option value=""><?= lang('Common.select_nationality') ?></option>
                        <option value="THAI" <?= (isset($data['nation']) && $data['nation'] == 'THAI') ? 'selected' : '' ?>><?= lang('Common.thai') ?></option>
                        <option value="OTHER" <?= (isset($data['nation']) && $data['nation'] == 'OTHER') ? 'selected' : '' ?>><?= lang('Common.other') ?></option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="position" class="form-label fw-bold"><?= lang('Common.position_label') ?></label>
                    <select name="position" id="position" class="form-select">
                        <option value=""><?= lang('Common.select_position') ?></option>
                        <option value="team_manager" <?= (isset($data['position']) && $data['position'] == 'team_manager') ? 'selected' : '' ?>><?= lang('Common.position_team_manager') ?></option>
                        <option value="head_coach" <?= (isset($data['position']) && $data['position'] == 'head_coach') ? 'selected' : '' ?>><?= lang('Common.position_head_coach') ?></option>
                        <option value="coach" <?= (isset($data['position']) && $data['position'] == 'coach') ? 'selected' : '' ?>><?= lang('Common.position_coach') ?></option>
                        <option value="assistant_coach" <?= (isset($data['position']) && $data['position'] == 'assistant_coach') ? 'selected' : '' ?>><?= lang('Common.position_assistant_coach') ?></option>
                        <option value="team_staff" <?= (isset($data['position']) && $data['position'] == 'team_staff') ? 'selected' : '' ?>><?= lang('Common.position_team_staff') ?></option>
                    </select>
                </div>

                <div class="col-md-4">
                    <label for="id_card" class="form-label fw-bold"><?= lang('Common.id_card_label') ?></label>
                    <input type="text" name="id_card" id="id_card" value="<?= (!empty($data['id_card'])) ? $data['id_card'] : '' ?>" class="form-control" placeholder="<?= lang('Common.thirteen_digits') ?>" maxlength="13">
                </div>
                <div class="col-md-4">
                    <label for="birth_date" class="form-label fw-bold"><?= lang('Common.birth_date') ?></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="ti ti-calendar"></i></span>
                        <input type="text" id="birth_date" name="birth_date" class="form-control date_picker" value="<?= (!empty($data['birth_date'])) ? date('d/m/Y', strtotime($data['birth_date'])) : '' ?>" placeholder="<?= lang('Common.date_placeholder') ?>">
                    </div>
                </div>

                <!-- ID Card Image -->
                <div class="col-md-4">
                    <label class="form-label fw-bold"><?= lang('Common.id_card_image') ?></label>
                    <input type="hidden" name="id_card_image_base64" id="idCardImageBase64">
                    <input type="hidden" name="delete_id_card_image" id="deleteIdCardImage" value="0">
                    <div class="d-flex align-items-center gap-3">
                        <?php if (!empty($data['id_card_image'])): ?>
                        <div id="idCardPreviewWrapper">
                            <img id="idCardPreview" src="<?= id_card_url($data['id_card_image']) ?>"
                                class="img-thumbnail" style="max-height: 80px; cursor: pointer;"
                                onclick="viewIdCard(this.src)" title="<?= lang('Common.view_id_card') ?>">
                        </div>
                        <?php else: ?>
                        <div id="idCardPreviewWrapper" style="display:none;">
                            <img id="idCardPreview" src="" class="img-thumbnail"
                                style="max-height: 80px; cursor: pointer;"
                                onclick="viewIdCard(this.src)" title="<?= lang('Common.view_id_card') ?>">
                        </div>
                        <?php endif; ?>
                        <div>
                            <label for="idCardFileInput" class="btn btn-outline-primary btn-sm" style="cursor:pointer;">
                                <i class="ti ti-upload me-1"></i><?= lang('Common.upload_id_card') ?>
                            </label>
                            <input type="file" id="idCardFileInput" accept="image/jpeg,image/png" style="display:none;">
                            <br><small class="text-muted"><?= lang('Common.id_card_image_hint') ?></small>
                            <?php if (!empty($data['id_card_image'])): ?>
                            <br>
                            <button type="button" class="btn btn-outline-danger btn-sm mt-1" id="btnDeleteIdCard">
                                <i class="ti ti-trash me-1"></i><?= lang('Common.delete_id_card_image') ?>
                            </button>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

                <div class="col-12 mt-4">
                    <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="ti ti-shirt-sport me-2"></i><?= lang('Common.physical_and_team_info') ?></h5>
                </div>

                <div class="col-md-4">
                    <label for="team_id" class="form-label fw-bold"><?= lang('Common.belongs_to_team') ?></label>
                    <select name="team_id" id="team_id" class="form-select select2" required>
                        <option value="">-- <?= lang('Common.select_team') ?> --</option>
                        <?php foreach ($team_options as $option) { ?>
                            <option value="<?= $option['id'] ?>" <?= (isset($data['team_id']) && $data['team_id'] == $option['id']) ? 'selected' : '' ?>><?= $option['name'] ?></option>
                        <?php } ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="dominant_foot" class="form-label fw-bold"><?= lang('Common.dominant_foot') ?> <span class="text-danger">*</span></label>
                    <select name="dominant_foot" id="dominant_foot" class="form-select" required>
                        <option value="">-- <?= lang('Common.select') ?> --</option>
                        <option value="ขวา" <?= (isset($data['dominant_foot']) && $data['dominant_foot'] == 'ขวา') ? 'selected' : '' ?>><?= lang('Common.right_foot') ?></option>
                        <option value="ซ้าย" <?= (isset($data['dominant_foot']) && $data['dominant_foot'] == 'ซ้าย') ? 'selected' : '' ?>><?= lang('Common.left_foot') ?></option>
                        <option value="ทั้งสองเท้า" <?= (isset($data['dominant_foot']) && $data['dominant_foot'] == 'ทั้งสองเท้า') ? 'selected' : '' ?>><?= lang('Common.both_feet') ?></option>
                    </select>
                </div>
                <div class="col-md-4">
                     <!-- Spacer -->
                </div>

                <div class="col-md-4">
                    <label for="height" class="form-label fw-bold"><?= lang('Common.height_cm') ?> <span class="text-danger">*</span></label>
                    <input type="number" name="height" id="height" value="<?= (!empty($data['height'])) ? $data['height'] : '' ?>" class="form-control" required min="0">
                </div>
                <div class="col-md-4">
                    <label for="weight" class="form-label fw-bold"><?= lang('Common.weight_kg') ?> <span class="text-danger">*</span></label>
                    <input type="number" name="weight" id="weight" value="<?= (!empty($data['weight'])) ? $data['weight'] : '' ?>" class="form-control" required min="0">
                </div>
                <div class="col-md-4">
                    <label for="bmi" class="form-label fw-bold">BMI <span class="text-danger">*</span></label>
                    <input type="text" name="bmi" id="bmi" value="<?= (!empty($data['bmi'])) ? $data['bmi'] : '' ?>" class="form-control bg-light" readonly required>
                </div>

                <div class="col-12 mt-4">
                    <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="ti ti-map-pin me-2"></i><?= lang('Common.address') ?></h5>
                </div>

                <div class="col-md-4">
                    <label for="province" class="form-label fw-bold"><?= lang('Common.province') ?> <span class="text-danger">*</span></label>
                    <select name="province_id" id="province" class="form-select select2" required>
                        <option value=""><?= lang('Common.select_province') ?></option>
                        <?php foreach ($provinces as $province) : ?>
                        <option value="<?= $province['province_id'] ?>"
                            <?= (old('province_id', $data['province_id'] ?? '') == $province['province_id']) ? 'selected' : '' ?>>
                            <?= $province['province_name'] ?>
                        </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="amphure" class="form-label fw-bold"><?= lang('Common.district') ?> <span class="text-danger">*</span></label>
                    <select name="amphure_id" id="amphure" class="form-select select2" disabled required>
                        <option value=""><?= lang('Common.select_district') ?></option>
                        <?php if (!empty($data['amphure_id'])): ?>
                        <option value="<?= $data['amphure_id'] ?>" selected>
                            <?= $data['amphure_name'] ?></option>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label for="tambon" class="form-label fw-bold"><?= lang('Common.subdistrict') ?> <span class="text-danger">*</span></label>
                    <select name="tambon_id" id="tambon" class="form-select select2" disabled required>
                        <option value=""><?= lang('Common.select_subdistrict') ?></option>
                        <?php if (!empty($data['tambon_id'])): ?>
                        <option value="<?= $data['tambon_id'] ?>" selected>
                            <?= $data['tambon_name'] ?></option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-md-12 text-center">
                    <a href="<?= base_url('admin/coach') ?>" class="btn btn-light border btn-lg px-4 me-2 shadow-sm rounded-pill">
                        <i class="ti ti-arrow-left me-2"></i> <?= lang('Common.back') ?>
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill btn-submit">
                        <i class="ti ti-device-floppy me-2"></i> <?= lang('Common.save_data') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langSelectDistrict = '<?= lang("Common.select_district") ?>';
    const langSelectSubdistrict = '<?= lang("Common.select_subdistrict") ?>';
    const langLoading = '<?= lang("Common.loading") ?>';
    const langLoadFailed = '<?= lang("Common.load_failed") ?>';

    const uploadImage = document.getElementById("uploadImage");
    const cropImage = document.getElementById("cropImage");
    const cropButton = document.getElementById("cropButton");
    const preview = document.getElementById("preview");
    const placeholderText = document.getElementById("placeholder-text");
    let cropper;

    uploadImage.addEventListener("change", (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                cropImage.src = e.target.result;
                cropImage.style.display = "block";
                if(placeholderText) placeholderText.style.display = "none";

                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    cropBoxResizable: false,
                    cropBoxMovable: true,
                    preview: "#preview",
                });
            };
            reader.readAsDataURL(file);
        }
    });

    cropButton.addEventListener("click", () => {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 350,
                height: 350,
            });
            const croppedImageURL = canvas.toDataURL("image/png");
            $('#image_path').val(croppedImageURL);
            $('#personel_image_display').attr('src', croppedImageURL);

            const modal = bootstrap.Modal.getInstance(document.getElementById("cropperModal"));
            modal.hide();
        }
    });

    $(document).ready(function() {
        $('.date_picker').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
            language: 'th-en',
        });

        // Auto Calculate BMI
        function calculateBMI() {
            const height = parseFloat($('#height').val()) / 100; // convert cm to m
            const weight = parseFloat($('#weight').val());

            if (height > 0 && weight > 0) {
                const bmi = (weight / (height * height)).toFixed(2);
                $('#bmi').val(bmi);
            } else {
                $('#bmi').val('');
            }
        }

        $('#height, #weight').on('input', calculateBMI);
    });


        // --- Address Dropdown Logic ---
        $('#province').on('change', function () {

            const provinceId = $(this).val();
            const amphureSelect = $('#amphure');
            const tambonSelect = $('#tambon');

            amphureSelect.html('<option value="">' + langSelectDistrict + '</option>').prop('disabled', true);
            tambonSelect.html('<option value="">' + langSelectSubdistrict + '</option>').prop('disabled', true);

            if (provinceId) {
                amphureSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
                $.get('<?= site_url('amphures/') ?>' + provinceId, function (districts) {
                    amphureSelect.html('<option value="">' + langSelectDistrict + '</option>');
                    districts.forEach(district => {
                        amphureSelect.append(`<option value="${district.district_id}">${district.district_name}</option>`);
                    });

                    // For edit form: if an old amphure exists, select it and trigger change
                    const oldAmphureId = "<?= $data['amphure_id'] ?? '' ?>";
                    if(oldAmphureId) {
                        amphureSelect.val(oldAmphureId).trigger('change');
                    }
                }).fail(function () {
                    amphureSelect.html('<option value="">' + langLoadFailed + '</option>');
                });
            }
        });

        $('#amphure').on('change', function () {
            const amphureId = $(this).val();
            const tambonSelect = $('#tambon');

            tambonSelect.html('<option value="">' + langSelectSubdistrict + '</option>').prop('disabled', true);

            if (amphureId) {
                tambonSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
                $.get('<?= site_url('tambons/') ?>' + amphureId, function (subdistricts) {
                    tambonSelect.html('<option value="">' + langSelectSubdistrict + '</option>');
                    subdistricts.forEach(subdistrict => {
                        tambonSelect.append(`<option value="${subdistrict.subdistrict_id}">${subdistrict.subdistrict_name}</option>`);
                    });

                    // For edit form: if an old tambon exists, select it
                     const oldTambonId = "<?= $data['tambon_id'] ?? '' ?>";
                    if(oldTambonId) {
                        tambonSelect.val(oldTambonId);
                    }
                }).fail(function () {
                    tambonSelect.html('<option value="">' + langLoadFailed + '</option>');
                });
            }
        });

        // --- ID Card number validation ---
        $('#id_card').on('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
            if (this.value.length > 13) {
                this.value = this.value.slice(0, 13);
            }
        });

        // --- ID Card Image Upload ---
        $('#idCardFileInput').on('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;

            const allowedTypes = ['image/jpeg', 'image/png'];
            if (!allowedTypes.includes(file.type)) {
                Swal.fire({ icon: 'error', title: '<?= lang("Common.error") ?>', text: '<?= lang("Common.invalid_file_type") ?>' });
                this.value = '';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                Swal.fire({ icon: 'error', title: '<?= lang("Common.error") ?>', text: '<?= lang("Common.file_too_large") ?>' });
                this.value = '';
                return;
            }

            const reader = new FileReader();
            reader.onload = function(ev) {
                $('#idCardImageBase64').val(ev.target.result);
                $('#idCardPreview').attr('src', ev.target.result);
                $('#idCardPreviewWrapper').show();
                $('#deleteIdCardImage').val('0');

                if (!$('#btnDeleteIdCard').length) {
                    const btn = $('<br><button type="button" class="btn btn-outline-danger btn-sm mt-1" id="btnDeleteIdCard"><i class="ti ti-trash me-1"></i><?= lang("Common.delete_id_card_image") ?></button>');
                    btn.on('click', deleteIdCard);
                    $('#idCardFileInput').closest('div').find('small').after(btn);
                }
            };
            reader.readAsDataURL(file);
        });

        // View ID Card Image
        window.viewIdCard = function(src) {
            Swal.fire({
                title: '<?= lang("Common.view_id_card") ?>',
                imageUrl: src,
                imageAlt: '<?= lang("Common.view_id_card") ?>',
                showConfirmButton: true,
                confirmButtonText: '<?= lang("Common.cancel") ?>',
                width: 'auto',
                imageWidth: '100%',
                imageHeight: 'auto'
            });
        };

        // Delete ID Card Image
        function deleteIdCard() {
            Swal.fire({
                title: '<?= lang("Common.delete_id_card_image") ?>',
                text: '<?= lang("Common.confirm_delete_id_card") ?>',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<?= lang("Common.confirm") ?>',
                cancelButtonText: '<?= lang("Common.cancel") ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#deleteIdCardImage').val('1');
                    $('#idCardImageBase64').val('');
                    $('#idCardPreview').attr('src', '');
                    $('#idCardPreviewWrapper').hide();
                    $('#idCardFileInput').val('');
                }
            });
        }

        $(document).on('click', '#btnDeleteIdCard', deleteIdCard);

</script>
<?php $this->endSection(); ?>
