<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="card shadow-sm">
    <div class="card-body">

        <form action="<?= base_url('player/save-coach') ?>" method="POST" enctype="multipart/form-data" class="mt-4"
            autocomplete="off">

            <input type="hidden" name="id" value="<?= (!empty($player['id'])) ? $player['id'] : '' ?>">
            <input type="hidden" name="team_id" value="<?= session()->get('team.id') ?>">
            <input type="hidden" id="image_personel" name="base64_image">

            <div class="text-center mb-5">
                <h2 class="text-primary fw-bold">ข้อมูลผู้ฝึกสอน</h2>
                <hr class="w-50 mx-auto">
            </div>

            <div class="row justify-content-center mb-4">
                <div class="col-md-8 text-center">
                    <?php if (!empty($player['image_path'])) : ?>
                    <img id="personel_image_display" src="<?= base_url($player['image_path']) ?>" alt="Player Profile"
                        class="img-fluid rounded-circle shadow-sm"
                        style="width: 175px; height: 175px; object-fit: cover; border: 4px solid #dee2e6;">
                    <?php else : ?>
                    <img id="personel_image_display" src="<?= base_url('public/img/img_profile.jpg') ?>"
                        alt="Default Profile" class="img-fluid rounded-circle"
                        style="width: 175px; height: 175px; border: 4px solid #dee2e6;">
                    <?php endif; ?>
                    <div class="mt-3">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                            data-bs-target="#cropperModal">
                            <i class="fas fa-upload me-2"></i>อัพโหลดรูปภาพ
                        </button>
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row g-3 justify-content-center">

                    <div class="col-md-10">
                        <fieldset class="border rounded-3 p-3 pt-2">
                            <legend class="float-none w-auto px-3 h6 text-primary">ข้อมูลส่วนตัว</legend>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="first_name" class="form-label">ขื่อจริง <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="first_name" id="first_name"
                                        value="<?= old('first_name', $player['first_name'] ?? '') ?>"
                                        class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label for="last_name" class="form-label">นามสกุล <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="last_name" id="last_name"
                                        value="<?= old('last_name', $player['last_name'] ?? '') ?>" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-4">
                                    <label for="nick_name" class="form-label">ชื่อเล่น <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="nick_name" id="nick_name"
                                        value="<?= old('nick_name', $player['nick_name'] ?? '') ?>" class="form-control"
                                        required>
                                </div>

                                <div class="col-md-3">
                                    <label for="nation" class="form-label">สัญชาติ <span
                                            class="text-danger">*</span></label>
                                    <select name="nation" id="nation" class="form-select" required>
                                        <option value="THAI"
                                            <?= old('nation', $player['nation'] ?? '') == 'THAI' ? 'selected' : '' ?>>
                                            ไทย</option>
                                        <option value="OTHER"
                                            <?= old('nation', $player['nation'] ?? '') == 'OTHER' ? 'selected' : '' ?>>
                                            ต่างชาติ</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label for="id_card" class="form-label">เลขประจำตัว/พาสปอร์ต <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="id_card" id="id_card"
                                        value="<?= old('id_card', $player['id_card'] ?? '') ?>" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-5">
                                    <label for="birth_date" class="form-label">วัน/เดือน/ปีเกิด <span
                                            class="text-danger">*</span></label>
                                    <input type="text" name="birth_date" id="birth_date"
                                        value="<?= (!empty($player['birth_date'])) ? date('d/m/Y', strtotime($player['birth_date'])) : '' ?>"
                                        class="form-control date-picker" required>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label">เพศ</label>
                                    <div class="d-flex gap-3 mt-2">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sex" id="sex_m" value="M" <?= (old('sex', $player['sex'] ?? '') === 'M') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="sex_m">
                                                <i class="fas fa-male text-primary me-1"></i>ชาย
                                            </label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="sex" id="sex_w" value="W" <?= (old('sex', $player['sex'] ?? '') === 'W') ? 'checked' : '' ?>>
                                            <label class="form-check-label" for="sex_w">
                                                <i class="fas fa-female text-danger me-1"></i>หญิง
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-md-10">
                        <fieldset class="border rounded-3 p-3 pt-2">
                            <legend class="float-none w-auto px-3 h6 text-primary">ข้อมูลกายภาพ</legend>
                            <div class="row g-3">
                                <div class="col-md-3">
                                    <label for="height" class="form-label">ส่วนสูง (ซม.) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.1" name="height" id="height"
                                        value="<?= old('height', $player['height'] ?? '') ?>" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <label for="weight" class="form-label">น้ำหนัก (กก.) <span
                                            class="text-danger">*</span></label>
                                    <input type="number" step="0.1" name="weight" id="weight"
                                        value="<?= old('weight', $player['weight'] ?? '') ?>" class="form-control"
                                        required>
                                </div>
                                <div class="col-md-3">
                                    <label for="bmi" class="form-label">BMI</label>
                                    <input type="text" name="bmi" id="bmi"
                                        value="<?= old('bmi', $player['bmi'] ?? '') ?>" class="form-control" readonly>
                                </div>
                            </div>
                        </fieldset>
                    </div>

                    <div class="col-md-10">
                        <fieldset class="border rounded-3 p-3 pt-2">
                            <legend class="float-none w-auto px-3 h6 text-primary">ภูมิลำเนา</legend>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="province" class="form-label">จังหวัด <span
                                            class="text-danger">*</span></label>
                                    <select name="province_id" id="province" class="form-select" required>
                                        <option value="">-- เลือกจังหวัด --</option>
                                        <?php foreach ($provinces as $province) : ?>
                                        <option value="<?= $province['province_id'] ?>"
                                            <?= (old('province_id', $player['province_id'] ?? '') == $province['province_id']) ? 'selected' : '' ?>>
                                            <?= $province['province_name'] ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="amphure" class="form-label">อำเภอ/เขต <span
                                            class="text-danger">*</span></label>
                                    <select name="amphure_id" id="amphure" class="form-select" disabled required>
                                        <option value="">-- เลือกอำเภอ/เขต --</option>
                                        <?php if (!empty($player['amphure_id'])): ?>
                                        <option value="<?= $player['amphure_id'] ?>" selected>
                                            <?= $player['amphure_name'] ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                                <div class="col-md-4">
                                    <label for="tambon" class="form-label">ตำบล/แขวง <span
                                            class="text-danger">*</span></label>
                                    <select name="tambon_id" id="tambon" class="form-select" disabled required>
                                        <option value="">-- เลือกตำบล/แขวง --</option>
                                        <?php if (!empty($player['tambon_id'])): ?>
                                        <option value="<?= $player['tambon_id'] ?>" selected>
                                            <?= $player['tambon_name'] ?></option>
                                        <?php endif; ?>
                                    </select>
                                </div>

                            </div>
                        </fieldset>
                    </div>


                </div>

                <div class="row mt-5">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary btn-lg mx-2"><i class="fa fa-save me-2"></i>
                            บันทึกข้อมูล</button>
                        <a href="<?= !empty($academy_id) ? base_url('academy/coaches/' . $academy_id) : base_url('team/player-list') ?>" class="btn btn-secondary btn-lg mx-2"><i
                                class="fa fa-arrow-left me-2"></i> กลับสู่หน้าหลัก</a>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropperModalLabel">อัพโหลดและตัดรูปภาพ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="uploadImage" class="form-label">เลือกไฟล์รูปภาพ:</label>
                    <input type="file" id="uploadImage" class="form-control" accept="image/*" />
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <div class="img-container" style="max-height: 400px;">
                            <img id="cropImage" style="max-width: 100%; display: none;" />
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="fw-bold mb-2">ตัวอย่าง:</div>
                        <div id="preview" class="border"
                            style="width: 250px; height: 250px; overflow: hidden; margin: auto;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" id="cropButton" class="btn btn-primary">บันทึกรูปภาพ</button>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>


<?php $this->section('css'); ?>
<style>
    fieldset {
        background-color: #f8f9fa;
    }

    legend {
        font-size: 1rem;
        font-weight: 600;
    }
</style>
<?php $this->endSection(); ?>


<?php $this->section('scripts'); ?>
<script>
    // --- Function Definitions ---
    function checkMaskIDCard() {
        let value = $('#nation').val();
        if (value == 'THAI') {
            $('#id_card').mask('0-0000-00000-00-0');
        } else {
            $('#id_card').unmask();
        }
    }

    function calculateBMI() {
        const height = parseFloat($('#height').val());
        const weight = parseFloat($('#weight').val());

        if (height > 0 && weight > 0) {
            const heightInMeters = height / 100;
            const bmi = weight / (heightInMeters * heightInMeters);
            $('#bmi').val(bmi.toFixed(2));
        } else {
            $('#bmi').val('');
        }
    }

    // --- Main Logic on Document Ready ---
    $(document).ready(function () {
        // --- Event Handlers (onchange, oninput, etc.) ---
        $('#nation').on('change', function () {
            checkMaskIDCard();
            $('#id_card').val('');
        });

        $('#height, #weight').on('input', calculateBMI);

        // --- Address Dropdown Logic ---
        $('#province').on('change', function () {
            const provinceId = $(this).val();
            const amphureSelect = $('#amphure');
            const tambonSelect = $('#tambon');

            // ลบข้อมูลเก่าทิ้ง
            amphureSelect.empty().append('<option value="">-- เลือกอำเภอ/เขต --</option>')
                .prop('disabled', true);
            tambonSelect.empty().append('<option value="">-- เลือกตำบล/แขวง --</option>')
                .prop('disabled', true);

            if (provinceId) {
                // โหลดข้อมูลอำเภอ
                amphureSelect.prop('disabled', false).append(
                    '<option value="">กำลังโหลด...</option>');
                $.get('<?= site_url('amphures/') ?>' + provinceId,
                    function (districts) {
                        amphureSelect.empty().append(
                            '<option value="">-- เลือกอำเภอ/เขต --</option>');
                        $.each(districts, function (index, district) {
                            amphureSelect.append('<option value="' + district
                                .district_id + '">' + district
                                .district_name + '</option>');
                        });

                        // หากมีข้อมูลอำเภอที่เลือกไว้ก่อนหน้านี้
                        <?php if (!empty($player['amphure_id'])): ?>
                            amphureSelect.val("<?= $player['amphure_id'] ?>");
                            $('#amphure').trigger('change');
                        <?php endif; ?>
                    }).fail(function () {
                    amphureSelect.empty().append(
                        '<option value="">-- โหลดข้อมูลไม่สำเร็จ --</option>');
                });
            }
        });

        // เมื่อเลือกอำเภอ
        $('#amphure').on('change', function () {
            const amphureId = $(this).val();
            const tambonSelect = $('#tambon');

            tambonSelect.empty().append('<option value="">-- เลือกตำบล/แขวง --</option>')
                .prop('disabled', true);

            if (amphureId) {
                tambonSelect.prop('disabled', false).append(
                    '<option value="">กำลังโหลด...</option>');
                $.get('<?= site_url('tambons/') ?>' + amphureId,
                    function (subdistricts) {
                        tambonSelect.empty().append(
                            '<option value="">-- เลือกตำบล/แขวง --</option>');
                        $.each(subdistricts, function (index, subdistrict) {
                            tambonSelect.append('<option value="' + subdistrict
                                .subdistrict_id + '">' + subdistrict
                                .subdistrict_name + '</option>');
                        });

                        // หากมีข้อมูลตำบลที่เลือกไว้ก่อนหน้านี้
                        <?php if (!empty($player['tambon_id'])): ?>
                            tambonSelect.val("<?= $player['tambon_id'] ?>");
                        <?php endif; ?>
                    }).fail(function () {
                    tambonSelect.empty().append(
                        '<option value="">-- โหลดข้อมูลไม่สำเร็จ --</option>');
                });
            }
        });

        // หากมีข้อมูลจังหวัดที่เลือกไว้ก่อนหน้านี้ ให้ trigger change เพื่อโหลดอำเภอ
        <?php if (!empty($player['province_id'])): ?>
            $('#province').trigger('change');
        <?php endif; ?>

        // --- Initial Calls on Page Load ---
        checkMaskIDCard();
        calculateBMI();

        // --- Cropper.js Logic ---
        const uploadImage = document.getElementById("uploadImage");
        const cropImage = document.getElementById("cropImage");
        const cropButton = document.getElementById("cropButton");
        const preview = document.getElementById("preview");
        let cropper;

        uploadImage.addEventListener("change", (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    cropImage.src = e.target.result;
                    cropImage.style.display = "block";
                    
                    // Wait for image to load before initializing cropper
                    cropImage.onload = function() {
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
                $('#image_personel').val(croppedImageURL);
                $('#personel_image_display').attr('src', croppedImageURL).css({
                    'width': '175px',
                    'height': '175px',
                    'object-fit': 'cover',
                    'border': '4px solid #dee2e6'
                });

                // Correctly hide the modal for BS5
                const modalEl = document.getElementById("cropperModal");
                const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                modal.hide();
            } else {
                alert('กรุณาเลือกรูปภาพและปรับขนาดก่อนบันทึก');
            }
        });
    });
</script>
<?php $this->endSection(); ?>