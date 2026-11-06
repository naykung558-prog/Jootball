<?php $this->extend('template/layout') ?>

<?php $this->section('style'); ?>
<style>
    .select2-container--default .select2-selection--single {
        height: calc(1.5em + 0.75rem + 2px);
        padding: 0.375rem 0.75rem;
        font-size: 1rem;
        border: 1px solid #ced4da;
        border-radius: 0.375rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 1.5;
        padding-left: 0;
        color: #212529;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: calc(1.5em + 0.75rem);
        right: 5px;
    }
    .select2-container--default.select2-container--disabled .select2-selection--single {
        background-color: #e9ecef;
    }
    .select2-container {
        margin-top: 0.5rem !important;
        margin-bottom: 0.5rem !important;
    }
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item">
                <a href="<?= base_url('academy/teams/' . $academy['id']) ?>"><?= esc($academy['name']) ?></a>
            </li>
            <li class="breadcrumb-item">
                <a href="<?= base_url('academy/teams/' . $academy['id']) ?>"><?= lang('Common.teams') ?></a>
            </li>
            <li class="breadcrumb-item active" aria-current="page"><?= !empty($team) ? lang('Common.edit') : lang('Common.add_sub_team') ?></li>
        </ol>
    </nav>

    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1 text-white">
                        <i class="fas <?= !empty($team) ? 'fa-edit' : 'fa-plus-circle' ?> me-2"></i>
                        <?= !empty($team) ? lang('Common.edit') . ' ' . esc($team['name'] ?? '') : lang('Common.add_sub_team') ?>
                    </h5>
                    <small class="opacity-75"><?= lang('Common.belongs_to_club') ?>: <?= esc($academy['name']) ?></small>
                </div>
                <a href="<?= base_url('academy/teams/' . $academy['id']) ?>" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> <?= lang('Common.back') ?>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="<?= base_url('academy/team-save') ?>" id="team-create-form" method="POST"
                enctype="multipart/form-data" class="once-only" autocomplete="off">

                <input type="hidden" name="academy_id" value="<?= $academy['id'] ?>">
                <?php if (!empty($team)): ?>
                    <input type="hidden" name="team_id" value="<?= $team['id'] ?>">
                <?php endif; ?>

                <!-- Logo Upload -->
                <div class="row justify-content-center mb-3">
                    <div class="col-md-5 text-center">
                        <div>
                            <?php
                                $logoSrc = base_url('public/img/exam-logo.webp');
                                if (!empty($team['logo'])) {
                                    $logoSrc = base_url($team['logo']);
                                }
                            ?>
                            <img id="personel_image_display" src="<?= $logoSrc ?>" alt=""
                                class="img-fluid border border-4 rounded-circle my-2" style="max-width: 175px;">
                        </div>

                        <input type="hidden" id="image_personel" name="base64_image">

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#cropperModal">
                            <?= lang('Common.upload_image') ?>
                        </button>

                        <!-- Cropper Modal -->
                        <div class="modal fade" id="cropperModal" tabindex="-1"
                            aria-labelledby="cropperModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cropperModalLabel">
                                            <?= lang('Common.upload_image') ?>
                                        </h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                            aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-lg-12">
                                                <input type="file" id="uploadImage" class="form-control mb-3"
                                                    accept="image/*" />
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="cropper-container cropper-bg">
                                                    <img id="cropImage" alt="Image to crop"
                                                        style="max-width: 100%; display: none" />
                                                </div>
                                            </div>
                                            <div class="col-lg-6">
                                                <div class="preview-container" id="preview"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary"
                                            data-bs-dismiss="modal">
                                            <?= lang('Common.cancel') ?>
                                        </button>
                                        <button type="button" id="cropButton" class="btn btn-primary">
                                            <?= lang('Common.save') ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row justify-content-center">
                    <!-- Team Name (Thai) -->
                    <div class="col-md-5">
                        <label class="pt-3">
                            <?= lang('Common.team_name_thai') ?>
                            <span class="text-danger">*</span>
                            <span class="remark-form-control"><?= lang('Common.sub_team_name_remark') ?></span>
                        </label>
                        <input type="text" name="name" class="form-control my-2" value="<?= esc($team['name'] ?? '') ?>" required>
                    </div>

                    <!-- Team Name (English) -->
                    <div class="col-md-5">
                        <label class="pt-3">
                            <?= lang('Common.team_name_english') ?>
                            <span class="remark-form-control"><?= lang('Common.sub_team_name_en_remark') ?></span>
                        </label>
                        <input type="text" name="name_en" class="form-control my-2" value="<?= esc($team['name_en'] ?? '') ?>">
                    </div>

                    <!-- Generation / Age Group -->
                    <div class="col-md-5">
                        <label class="pt-3"><?= lang('Common.generation') ?></label>
                        <input type="text" name="generation_name" class="form-control my-2"
                            placeholder="<?= lang('Common.generation_placeholder') ?>" value="<?= esc($team['generation_name'] ?? '') ?>">
                    </div>

                    <!-- Zone -->
                    <div class="col-md-5">
                        <label class="pt-3"><?= lang('Common.zone') ?></label>
                        <select name="zone_id" id="zone_id" class="form-control my-2 select2">
                            <option value="">-- <?= lang('Common.zone') ?> --</option>
                            <?php foreach ($zone as $z) : ?>
                            <option value="<?= $z['id'] ?>" <?= ($team['zone_id'] ?? '') == $z['id'] ? 'selected' : '' ?>><?= esc($z['description']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <!-- Country -->
                    <div class="col-md-5">
                        <label for="country_id" class="pt-3">
                            <?= lang('Common.country') ?>
                            <span class="remark-form-control"><?= lang('Common.country_remark') ?></span>
                        </label>
                        <select name="country_id" id="country_id" class="form-control my-2 select2">
                            <option value=""><?= lang('Common.select_country') ?></option>
                            <?php foreach ($countries as $country) : ?>
                            <option value="<?= $country['country_id'] ?>" <?= ($team['country_id'] ?? '') == $country['country_id'] ? 'selected' : '' ?>>
                                <?= $country['country_name_th'] ?> (<?= $country['country_name_en'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-5"></div>

                    <!-- Address Fieldset (Thailand only) -->
                    <div id="address-fieldset" class="col-md-10">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="province_id" class="pt-3">
                                    <?= lang('Common.province') ?>
                                    <span class="remark-form-control"><?= lang('Common.province_remark') ?></span>
                                </label>
                                <select name="province_id" id="province_id" class="form-control my-2 select2">
                                    <option value=""><?= lang('Common.select_province') ?></option>
                                    <?php foreach ($provinces as $province) : ?>
                                    <option value="<?= $province['province_id'] ?>" <?= ($team['province_id'] ?? '') == $province['province_id'] ? 'selected' : '' ?>>
                                        <?= $province['province_name'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="amphure_id" class="pt-3">
                                    <?= lang('Common.district') ?>
                                    <span class="remark-form-control"><?= lang('Common.district_remark') ?></span>
                                </label>
                                <select name="amphure_id" id="amphure_id" class="form-control my-2 select2" disabled>
                                    <option value=""><?= lang('Common.select_district') ?></option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tambon_id" class="pt-3">
                                    <?= lang('Common.subdistrict') ?>
                                    <span class="remark-form-control"><?= lang('Common.subdistrict_remark') ?></span>
                                </label>
                                <select name="tambon_id" id="tambon_id" class="form-control my-2 select2" disabled>
                                    <option value=""><?= lang('Common.select_subdistrict') ?></option>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information -->
                    <div class="col-md-5">
                        <label class="pt-3">
                            <?= lang('Common.contact_name_label') ?>
                            <span class="remark-form-control"><?= lang('Common.contact_name_remark') ?></span>
                        </label>
                        <input type="text" name="contact_name" class="form-control my-2" value="<?= esc($team['contact_name'] ?? '') ?>">
                    </div>

                    <div class="col-md-5">
                        <label class="pt-3">
                            <?= lang('Common.mobile_contact') ?>
                            <span class="remark-form-control"><?= lang('Common.mobile_contact_remark') ?></span>
                        </label>
                        <input type="text" name="contact_phone" class="form-control my-2 phone-input" value="<?= esc($team['contact_phone'] ?? '') ?>">
                    </div>

                    <div class="col-md-5">
                        <label class="pt-3">
                            <?= lang('Common.email_contact') ?>
                            <span class="remark-form-control"><?= lang('Common.email_contact_remark') ?></span>
                        </label>
                        <input type="email" name="contact_email" class="form-control my-2" value="<?= esc($team['contact_email'] ?? '') ?>">
                    </div>

                    <div class="col-md-5">
                        <label class="pt-3">
                            <?= lang('Common.line_id_contact') ?>
                            <span class="remark-form-control"><?= lang('Common.line_id_contact_remark') ?></span>
                        </label>
                        <input type="text" name="contact_line" class="form-control my-2" value="<?= esc($team['contact_line'] ?? '') ?>">
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-12 text-center mt-4">
                        <a href="<?= base_url('academy/teams/' . $academy['id']) ?>"
                            class="btn btn-outline-secondary me-2 px-4">
                            <i class="fas fa-times me-2"></i><?= lang('Common.cancel') ?>
                        </a>
                        <button type="button" class="btn btn-primary px-5" id="pre-submit-btn">
                            <i class="fas fa-save me-2"></i><?= lang('Common.submit_data') ?>
                        </button>
                    </div>
                </div>

            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    // --- Cropper.js Logic ---
    const uploadImage = document.getElementById("uploadImage");
    const cropImage = document.getElementById("cropImage");
    const cropButton = document.getElementById("cropButton");
    const preview = document.getElementById("preview");
    let cropper;

    uploadImage.addEventListener("change", (event) => {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = (e) => {
            cropImage.src = e.target.result;
            cropImage.style.display = "block";
            if (cropper) {
                cropper.destroy();
            }
            cropper = new Cropper(cropImage, {
                aspectRatio: 1,
                viewMode: 1,
                autoCropArea: 1,
                preview: "#preview",
            });
        };
        reader.readAsDataURL(file);
    });

    cropButton.addEventListener("click", () => {
        if (!cropper) return;

        const canvas = cropper.getCroppedCanvas({
            width: 350,
            height: 350,
        });
        const croppedImageURL = canvas.toDataURL("image/png");
        $('#image_personel').val(croppedImageURL);
        $('#personel_image_display').attr('src', croppedImageURL).css({
            'width': '175px',
            'height': '175px',
            'object-fit': 'cover',
            'border': '4px solid #dee2e6'
        });

        const modal = bootstrap.Modal.getInstance(document.getElementById("cropperModal"));
        modal.hide();
    });

    // --- Country / Address Logic ---
    const THAILAND_ID = '1';
    const langSelectDistrict = '<?= lang('Common.select_district') ?>';
    const langSelectSubdistrict = '<?= lang('Common.select_subdistrict') ?>';
    const langLoading = '<?= lang('Common.loading') ?>';
    const langLoadFailed = '<?= lang('Common.load_failed') ?>';

    function toggleAddressFieldset() {
        const countryVal = $('#country_id').val();
        if (countryVal === THAILAND_ID || countryVal === '') {
            $('#address-fieldset').show();
        } else {
            $('#address-fieldset').hide();
            $('#province_id').val('').trigger('change.select2');
            $('#amphure_id').html('<option value="">' + langSelectDistrict + '</option>').prop('disabled', true).trigger('change.select2');
            $('#tambon_id').html('<option value="">' + langSelectSubdistrict + '</option>').prop('disabled', true).trigger('change.select2');
        }
    }

    $('#country_id').on('change', function () {
        toggleAddressFieldset();
    });

    $('#province_id').on('change', function () {
        const provinceId = $(this).val();
        const amphureSelect = $('#amphure_id');
        const tambonSelect = $('#tambon_id');

        amphureSelect.html('<option value="">' + langSelectDistrict + '</option>').prop('disabled', true).trigger('change.select2');
        tambonSelect.html('<option value="">' + langSelectSubdistrict + '</option>').prop('disabled', true).trigger('change.select2');

        if (provinceId) {
            amphureSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
            $.get('<?= site_url('amphures') ?>/' + provinceId, function (data) {
                amphureSelect.empty().append('<option value="">' + langSelectDistrict + '</option>');
                data.forEach(function (item) {
                    amphureSelect.append('<option value="' + item.district_id + '">' + item.district_name + '</option>');
                });
                amphureSelect.trigger('change.select2');
            }).fail(function () {
                amphureSelect.html('<option value="">' + langLoadFailed + '</option>').trigger('change.select2');
            });
        }
    });

    $('#amphure_id').on('change', function () {
        const amphureId = $(this).val();
        const tambonSelect = $('#tambon_id');

        tambonSelect.html('<option value="">' + langSelectSubdistrict + '</option>').prop('disabled', true).trigger('change.select2');

        if (amphureId) {
            tambonSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
            $.get('<?= site_url('tambons') ?>/' + amphureId, function (data) {
                tambonSelect.empty().append('<option value="">' + langSelectSubdistrict + '</option>');
                data.forEach(function (item) {
                    tambonSelect.append('<option value="' + item.subdistrict_id + '">' + item.subdistrict_name + '</option>');
                });
                tambonSelect.trigger('change.select2');
            }).fail(function () {
                tambonSelect.html('<option value="">' + langLoadFailed + '</option>').trigger('change.select2');
            });
        }
    });

    // --- Document Ready Logic ---
    $(document).ready(function () {
        // Initialize Select2
        $('.select2').select2();

        // Edit mode: pre-load amphure and tambon
        <?php if (!empty($team['province_id'])): ?>
        (function() {
            const provinceId = '<?= $team['province_id'] ?>';
            const editAmphureId = '<?= $team['amphure_id'] ?? '' ?>';
            const editTambonId = '<?= $team['tambon_id'] ?? '' ?>';
            const amphureSelect = $('#amphure_id');
            const tambonSelect = $('#tambon_id');

            if (provinceId) {
                amphureSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
                $.get('<?= site_url('amphures') ?>/' + provinceId, function (data) {
                    amphureSelect.empty().append('<option value="">' + langSelectDistrict + '</option>');
                    data.forEach(function (item) {
                        const selected = (item.district_id == editAmphureId) ? ' selected' : '';
                        amphureSelect.append('<option value="' + item.district_id + '"' + selected + '>' + item.district_name + '</option>');
                    });
                    amphureSelect.trigger('change.select2');

                    // Load tambon if amphure is selected
                    if (editAmphureId) {
                        tambonSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
                        $.get('<?= site_url('tambons') ?>/' + editAmphureId, function (data2) {
                            tambonSelect.empty().append('<option value="">' + langSelectSubdistrict + '</option>');
                            data2.forEach(function (item) {
                                const selected = (item.subdistrict_id == editTambonId) ? ' selected' : '';
                                tambonSelect.append('<option value="' + item.subdistrict_id + '"' + selected + '>' + item.subdistrict_name + '</option>');
                            });
                            tambonSelect.trigger('change.select2');
                        });
                    }
                });
            }
        })();
        <?php endif; ?>

        $("#pre-submit-btn").click(function () {
            Swal.fire({
                title: "<?= lang('Common.confirm_save_data') ?>",
                text: "<?= lang('Common.confirm_save_text') ?>",
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#16478d",
                cancelButtonColor: "#d33",
                confirmButtonText: "<?= lang('Common.confirm') ?>",
                cancelButtonText: "<?= lang('Common.cancel') ?>"
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#team-create-form').submit();
                }
            });
        });
    });
</script>
<?php $this->endSection(); ?>
