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
<div class="card shadow-lg">
    <div class="card-body">
        <?php if (session()->get('team.id')) { ?>
            <?php echo view("Modules\Team\Views\subnav.php"); ?>
        <?php } ?>

        <form action="<?= base_url('team/post-register') ?>" id="regis-form" method="POST" enctype="multipart/form-data"
            class="once-only" autocomplete="off">
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

            <input type="hidden" name="id" value="<?= @$team['id'] ?>">

            <div class="row justify-content-center">
                <div class="col-md-5">
                    <label for="" class="pt-3"><?= lang('Common.team_name_thai') ?> <span class="text-danger">*</span> <span
                            class="remark-form-control"><?= lang('Common.team_name_thai_remark') ?></span></label>
                    <input type="text" id="" name="name" value="<?= (!empty($team)) ? $team['name'] : '' ?>"
                        class="form-control my-2">
                </div>
                <div class="col-md-5">
                    <label for="" class="pt-3"><?= lang('Common.team_name_english') ?> <span class="text-danger">*</span> <span
                            class="remark-form-control"><?= lang('Common.team_name_english_remark') ?></span></label>
                    <input type="text" id="" name="name_en" value="<?= (!empty($team)) ? $team['name_en'] : '' ?>"
                        class="form-control my-2">
                </div>
                <div class="col-md-5">
                    <label for="" class="pt-3"><?= lang('Common.organization_name') ?> <span class="text-danger">*</span> <span
                            class="remark-form-control"><?= lang('Common.organization_name_remark') ?></span></label>
                    <input type="text" id="" name="organize_name"
                        value="<?= (!empty($team)) ? $team['organize_name'] : '' ?>" class="form-control my-2">
                </div>
                <div class="col-md-5">
                    <label for="" class="pt-3"><?= lang('Common.head_coach_name_label') ?> <span class="text-danger">*</span> <span
                            class="remark-form-control"><?= lang('Common.head_coach_name_remark') ?></span></label>
                    <input type="text" id="" name="coach_name" value="<?= (!empty($team)) ? $team['coach_name'] : '' ?>"
                        class="form-control my-2">
                </div>
                <div class="col-md-5">
                    <label for="" class="pt-3"><?= lang('Common.mobile_coach') ?> <span class="text-danger">*</span> <span
                            class="remark-form-control"><?= lang('Common.mobile_coach_remark') ?></span></label>
                    <input type="text" id="" name="coach_phone"
                        value="<?= (!empty($team)) ? $team['coach_phone'] : '' ?>"
                        class="form-control my-2 phone-input">
                </div>
                <div class="col-md-5">
                    <label for="" class="pt-3"><?= lang('Common.contact_name_label') ?> <span class="text-danger">*</span> <span
                            class="remark-form-control"><?= lang('Common.contact_name_remark') ?></span></label>
                    <input type="text" id="" name="contact_name"
                        value="<?= (!empty($team)) ? $team['contact_name'] : '' ?>" class="form-control my-2">
                </div>
                <div class="col-md-5">
                    <label for="" class="pt-3"><?= lang('Common.mobile_contact') ?> <span class="text-danger">*</span> <span
                            class="remark-form-control"><?= lang('Common.mobile_contact_remark') ?></span></label>
                    <input type="text" id="" name="contact_phone"
                        value="<?= (!empty($team)) ? $team['contact_phone'] : '' ?>"
                        class="form-control my-2 phone-input">
                </div>
                <div class="col-md-5">
                    <label for="" class="pt-3"><?= lang('Common.email_contact') ?><span class="text-danger">*</span> <span
                            class="remark-form-control"><?= lang('Common.email_contact_remark') ?></span></label>
                    <input type="email" id="" name="contact_email"
                        value="<?= (!empty($team)) ? $team['contact_email'] : '' ?>" class="form-control my-2">
                </div>
                <div class="col-md-5">
                    <label for="" class="pt-3"><?= lang('Common.line_id_contact') ?> <span class="text-danger">*</span> <span
                            class="remark-form-control"><?= lang('Common.line_id_contact_remark') ?></span></label>
                    <input type="text" id="" name="contact_line"
                        value="<?= (!empty($team)) ? $team['contact_line'] : '' ?>" class="form-control my-2">
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

                <div class="col-md-5">
                    <label for="location_display" class="pt-3"><?= lang('Common.field_location') ?> <span
                            class="remark-form-control"><?= lang('Common.field_location_remark') ?></span></label>
                    <div class="input-group my-2">
                        <input type="text" id="location_display" class="form-control"
                            placeholder="<?= lang('Common.select_location_placeholder') ?>" readonly>
                        <button class="btn btn-outline-secondary" type="button" data-bs-toggle="modal"
                            data-bs-target="#mapModal">
                            <i class="fas fa-map-marker-alt"></i> &nbsp;<?= lang('Common.select_location_btn') ?>
                        </button>
                    </div>
                    <input type="hidden" id="latitude" name="latitude" value="<?= @$team['latitude'] ?>">
                    <input type="hidden" id="longitude" name="longitude" value="<?= @$team['longitude'] ?>">
                </div>
                <div class="col-md-12 text-center">
                    <button type="button" class="btn btn-primary my-2 btn-submit" id="pre-submit-btn"><?= lang('Common.submit_data') ?></button>
                </div>
            </div>
        </form>
    </div>
</div>
<div class="modal fade" id="regisDocument" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1"
    aria-labelledby="regisDocumentLabel">
    <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
        </div>
    </div>
</div>

<!-- Ensure Google Places autocomplete dropdown shows above the Bootstrap modal -->
<style>
    .pac-container {
        z-index: 1060 !important;
    }
</style>

<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel"><?= lang('Common.specify_map_location') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map-canvas" style="height: 450px; width: 100%; position: relative;"></div>
                <div class="input-group mt-3">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="map-search-input" class="form-control border-start-0"
                        placeholder="<?= lang('Common.search_location_placeholder') ?>" autocomplete="off">
                </div>
                <p class="text-muted mt-2 mb-0 text-center small"><?= lang('Common.drag_marker') ?></p>
            </div>
            <div class="modal-footer justify-content-between">
                <button type="button" class="btn btn-secondary" id="getCurrentLocationBtn"></i>&nbsp;<?= lang('Common.current_location_btn') ?></button>

                <div>
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                    <button type="button" class="btn btn-primary" id="saveLocationBtn"><?= lang('Common.save_location_btn') ?></button>
                </div>
            </div>
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


    // --- Google Map Logic ---
    let map;
    let marker;
    const mapModalElement = document.getElementById('mapModal');

    function initMap() {
        const initialLat = parseFloat($('#latitude').val()) || 18.7967; // Default to Chiang Mai
        const initialLng = parseFloat($('#longitude').val()) || 98.9854; // Default to Chiang Mai
        const initialPosition = {
            lat: initialLat,
            lng: initialLng
        };

        map = new google.maps.Map(document.getElementById("map-canvas"), {
            zoom: 15,
            center: initialPosition,
            mapTypeControl: false,
            streetViewControl: false
        });

        marker = new google.maps.Marker({
            position: initialPosition,
            map: map,
            draggable: true,
        });

        google.maps.event.addListener(marker, 'dragend', function(event) {
            updateLocationFields(event.latLng);
        });

        // --- Places search ---
        const searchInput = document.getElementById('map-search-input');
        if (searchInput && google.maps.places) {
            const autocomplete = new google.maps.places.Autocomplete(searchInput, {
                fields: ['geometry', 'name', 'formatted_address'],
            });
            autocomplete.bindTo('bounds', map);
            autocomplete.addListener('place_changed', function() {
                const place = autocomplete.getPlace();
                if (!place.geometry || !place.geometry.location) {
                    return;
                }
                if (place.geometry.viewport) {
                    map.fitBounds(place.geometry.viewport);
                } else {
                    map.setCenter(place.geometry.location);
                    map.setZoom(17);
                }
                marker.setPosition(place.geometry.location);
                updateLocationFields(place.geometry.location);
            });
            // Prevent Enter in the search box from submitting the form
            searchInput.addEventListener('keydown', function(e) {
                if (e.key === 'Enter') e.preventDefault();
            });
        }

        if ($('#latitude').val() && $('#longitude').val()) {
            $('#location_display').val(`Lat: ${initialLat.toFixed(6)}, Lng: ${initialLng.toFixed(6)}`);
        }
    }

    function updateLocationFields(latLng) {
        const lat = latLng.lat();
        const lng = latLng.lng();
        $('#latitude').val(lat);
        $('#longitude').val(lng);
        $('#location_display').val(`Lat: ${lat.toFixed(6)}, Lng: ${lng.toFixed(6)}`);
    }

    if (mapModalElement) {
        mapModalElement.addEventListener('shown.bs.modal', function() {
            if (typeof loadGoogleMapsAPI === 'function') {
                loadGoogleMapsAPI(function() {
                    if (!map) initMap();
                    google.maps.event.trigger(map, "resize");
                    map.setCenter(marker.getPosition());
                });
            }
        });
    }

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
                    $('#regis-form').submit();
                }
            });
        });

        $('#saveLocationBtn').click(function() {
            const modal = bootstrap.Modal.getInstance(mapModalElement);
            modal.hide();
        });

        $('#getCurrentLocationBtn').click(function() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    (position) => {
                        const pos = {
                            lat: position.coords.latitude,
                            lng: position.coords.longitude,
                        };
                        const newLatLng = new google.maps.LatLng(pos.lat, pos.lng);

                        marker.setPosition(newLatLng);
                        map.setCenter(pos);
                        updateLocationFields(newLatLng);
                    },
                    () => {
                        alert("<?= lang('Common.geolocation_error') ?>");
                    }
                );
            } else {
                alert("<?= lang('Common.browser_no_geolocation') ?>");
            }
        });
    });
</script>
<?php $this->endSection(); ?>