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
    <?php echo view("Modules\Team\Views\subnav.php"); ?>

    <div class="card shadow-lg mt-3">
        <div class="card-header bg-primary text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1 text-white">
                        <i class="fas fa-users me-2"></i>
                        <?= !empty($team['id']) ? lang('Common.edit_sub_team') : lang('Common.add_sub_team') ?>
                    </h5>
                    <small class="opacity-75"><?= lang('Common.belongs_to_club') ?>: <?= esc($parent_team['name']) ?></small>
                </div>
                <a href="<?= base_url('team/info') ?>" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> <?= lang('Common.back') ?>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="<?= base_url('team/subteam-save') ?>" id="subteam-form" method="POST" enctype="multipart/form-data"
                class="once-only" autocomplete="off">

                <input type="hidden" name="id" value="<?= @$team['id'] ?>">
                <input type="hidden" name="parent_team_id" value="<?= $parent_team['id'] ?>">

                <div class="row justify-content-center mb-3">
                    <div class="col-md-5 text-center">
                        <div>
                            <?php if (!empty($team['logo'])) { ?>
                                <img id="personel_image_display" src="<?= base_url($team['logo']) ?>" alt=""
                                    class="img-fluid border border-4 rounded-circle my-2" style="max-width: 175px;">
                            <?php } else { ?>
                                <img id="personel_image_display" src="<?= base_url('public/img/exam-logo.webp') ?>" alt=""
                                    class="img-fluid border border-4 rounded-circle my-2" style="max-width: 175px;">
                            <?php } ?>
                        </div>

                        <input type="hidden" id="image_personel" name="base64_image">

                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cropperModal">
                            <?= lang('Common.upload_image') ?>
                        </button>

                        <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel"
                            aria-hidden="true">
                            <div class="modal-dialog modal-lg">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="cropperModalLabel"><?= lang('Common.upload_image') ?></h5>
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
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
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
                    <!-- ข้อมูลทีม -->
                    <div class="col-md-5">
                        <label for="" class="pt-3"><?= lang('Common.team_name_thai') ?> <span class="text-danger">*</span> <span
                                class="remark-form-control"><?= lang('Common.sub_team_name_remark') ?></span></label>
                        <input type="text" id="" name="name" value="<?= (!empty($team)) ? $team['name'] : '' ?>"
                            class="form-control my-2" required>
                    </div>
                    <div class="col-md-5">
                        <label for="" class="pt-3"><?= lang('Common.team_name_english') ?> <span
                                class="remark-form-control"><?= lang('Common.sub_team_name_en_remark') ?></span></label>
                        <input type="text" id="" name="name_en" value="<?= (!empty($team)) ? $team['name_en'] : '' ?>"
                            class="form-control my-2">
                    </div>
                    <div class="col-md-5">
                        <label for="country_id" class="pt-3"><?= lang('Common.country') ?> <span class="remark-form-control"><?= lang('Common.country_remark') ?></span></label>
                        <select name="country_id" id="country_id" class="form-control my-2 select2">
                            <option value=""><?= lang('Common.select_country') ?></option>
                            <?php foreach ($countries as $country) : ?>
                            <option value="<?= $country['country_id'] ?>"
                                <?= (($team['country_id'] ?? '') == $country['country_id']) ? 'selected' : '' ?>>
                                <?= $country['country_name_th'] ?> (<?= $country['country_name_en'] ?>)
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-5"></div>
                    <div id="address-fieldset" class="col-md-10" style="<?= (($team['country_id'] ?? '') == 1 || empty($team['country_id'])) ? '' : 'display:none;' ?>">
                        <div class="row">
                            <div class="col-md-4">
                                <label for="province_id" class="pt-3"><?= lang('Common.province') ?> <span class="remark-form-control"><?= lang('Common.province_remark') ?></span></label>
                                <select name="province_id" id="province_id" class="form-control my-2 select2">
                                    <option value=""><?= lang('Common.select_province') ?></option>
                                    <?php foreach ($provinces as $province) : ?>
                                    <option value="<?= $province['province_id'] ?>"
                                        <?= (($team['province_id'] ?? '') == $province['province_id']) ? 'selected' : '' ?>>
                                        <?= $province['province_name'] ?>
                                    </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="amphure_id" class="pt-3"><?= lang('Common.district') ?> <span class="remark-form-control"><?= lang('Common.district_remark') ?></span></label>
                                <select name="amphure_id" id="amphure_id" class="form-control my-2 select2" disabled>
                                    <option value=""><?= lang('Common.select_district') ?></option>
                                    <?php if (!empty($team['amphure_id'])): ?>
                                    <option value="<?= $team['amphure_id'] ?>" selected><?= $team['amphure_name'] ?? $team['amphure_id'] ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label for="tambon_id" class="pt-3"><?= lang('Common.subdistrict') ?> <span class="remark-form-control"><?= lang('Common.subdistrict_remark') ?></span></label>
                                <select name="tambon_id" id="tambon_id" class="form-control my-2 select2" disabled>
                                    <option value=""><?= lang('Common.select_subdistrict') ?></option>
                                    <?php if (!empty($team['tambon_id'])): ?>
                                    <option value="<?= $team['tambon_id'] ?>" selected><?= $team['tambon_name'] ?? $team['tambon_id'] ?></option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>

                    <!-- ข้อมูลติดต่อ -->
                    <div class="col-md-5">
                        <label for="" class="pt-3"><?= lang('Common.head_coach_name_label') ?> <span
                                class="remark-form-control"><?= lang('Common.head_coach_name_remark') ?></span></label>
                        <input type="text" id="" name="coach_name" value="<?= (!empty($team)) ? $team['coach_name'] : '' ?>"
                            class="form-control my-2">
                    </div>
                    <div class="col-md-5">
                        <label for="" class="pt-3"><?= lang('Common.mobile_coach') ?> <span
                                class="remark-form-control"><?= lang('Common.mobile_coach_remark') ?></span></label>
                        <input type="text" id="" name="coach_phone"
                            value="<?= (!empty($team)) ? $team['coach_phone'] : '' ?>"
                            class="form-control my-2 phone-input">
                    </div>
                    <div class="col-md-5">
                        <label for="" class="pt-3"><?= lang('Common.contact_name_label') ?> <span
                                class="remark-form-control"><?= lang('Common.contact_name_remark') ?></span></label>
                        <input type="text" id="" name="contact_name"
                            value="<?= (!empty($team)) ? $team['contact_name'] : '' ?>" class="form-control my-2">
                    </div>
                    <div class="col-md-5">
                        <label for="" class="pt-3"><?= lang('Common.mobile_contact') ?> <span
                                class="remark-form-control"><?= lang('Common.mobile_contact_remark') ?></span></label>
                        <input type="text" id="" name="contact_phone"
                            value="<?= (!empty($team)) ? $team['contact_phone'] : '' ?>"
                            class="form-control my-2 phone-input">
                    </div>
                    <div class="col-md-5">
                        <label for="" class="pt-3"><?= lang('Common.email_contact') ?> <span
                                class="remark-form-control"><?= lang('Common.email_contact_remark') ?></span></label>
                        <input type="email" id="" name="contact_email"
                            value="<?= (!empty($team)) ? $team['contact_email'] : '' ?>" class="form-control my-2">
                    </div>
                    <div class="col-md-5">
                        <label for="" class="pt-3"><?= lang('Common.line_id_contact') ?> <span
                                class="remark-form-control"><?= lang('Common.line_id_contact_remark') ?></span></label>
                        <input type="text" id="" name="contact_line"
                            value="<?= (!empty($team)) ? $team['contact_line'] : '' ?>" class="form-control my-2">
                    </div>

                    <div class="col-md-12 text-center mt-4">
                        <button type="button" class="btn btn-primary my-2 btn-submit px-5" id="pre-submit-btn">
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

    $('#country_id').on('change', function() {
        toggleAddressFieldset();
    });

    $('#province_id').on('change', function() {
        const provinceId = $(this).val();
        const amphureSelect = $('#amphure_id');
        const tambonSelect = $('#tambon_id');

        amphureSelect.html('<option value="">' + langSelectDistrict + '</option>').prop('disabled', true).trigger('change.select2');
        tambonSelect.html('<option value="">' + langSelectSubdistrict + '</option>').prop('disabled', true).trigger('change.select2');

        if (provinceId) {
            amphureSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
            $.get('<?= site_url('amphures') ?>/' + provinceId, function(data) {
                amphureSelect.empty().append('<option value="">' + langSelectDistrict + '</option>');
                data.forEach(function(item) {
                    amphureSelect.append('<option value="' + item.district_id + '">' + item.district_name + '</option>');
                });
                amphureSelect.trigger('change.select2');
                const oldAmphureId = "<?= $team['amphure_id'] ?? '' ?>";
                if (oldAmphureId && amphureSelect.find('option[value="' + oldAmphureId + '"]').length) {
                    amphureSelect.val(oldAmphureId).trigger('change').trigger('change.select2');
                }
            }).fail(function() {
                amphureSelect.html('<option value="">' + langLoadFailed + '</option>').trigger('change.select2');
            });
        }
    });

    $('#amphure_id').on('change', function() {
        const amphureId = $(this).val();
        const tambonSelect = $('#tambon_id');

        tambonSelect.html('<option value="">' + langSelectSubdistrict + '</option>').prop('disabled', true).trigger('change.select2');

        if (amphureId) {
            tambonSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
            $.get('<?= site_url('tambons') ?>/' + amphureId, function(data) {
                tambonSelect.empty().append('<option value="">' + langSelectSubdistrict + '</option>');
                data.forEach(function(item) {
                    tambonSelect.append('<option value="' + item.subdistrict_id + '">' + item.subdistrict_name + '</option>');
                });
                tambonSelect.trigger('change.select2');
                const oldTambonId = "<?= $team['tambon_id'] ?? '' ?>";
                if (oldTambonId && tambonSelect.find('option[value="' + oldTambonId + '"]').length) {
                    tambonSelect.val(oldTambonId).trigger('change.select2');
                }
            }).fail(function() {
                tambonSelect.html('<option value="">' + langLoadFailed + '</option>').trigger('change.select2');
            });
        }
    });

    // --- Document Ready Logic ---
    $(document).ready(function() {
        // Load cascading address on edit form
        if ($('#province_id').val()) {
            $('#province_id').trigger('change');
        }

        $("#pre-submit-btn").click(function() {
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
                    $('#subteam-form').submit();
                }
            });
        });
    });
</script>
<?php $this->endSection(); ?>
