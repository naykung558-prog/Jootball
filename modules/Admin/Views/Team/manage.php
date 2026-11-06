<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.manage_team_data') ?></h4>
                <p class="text-muted mb-0 small"><?= lang('Common.add_or_edit_team_data') ?></p>
            </div>
            <a href="<?= base_url('admin/team') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="ti ti-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>

        <form action="<?= base_url('admin/team/save') ?>" id="regis-form" method="POST" enctype="multipart/form-data"
            class="once-only" autocomplete="off">

            <div class="row justify-content-center mb-5">
                <div class="col-md-12 text-center">
                    <div class="position-relative d-inline-block">
                        <?php if (!empty($team['logo'])) { ?>
                            <img id="personel_image_display" src="<?= base_url($team['logo']) ?>" alt=""
                                class="img-fluid rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid white; box-shadow: var(--box-shadow);">
                        <?php } else { ?>
                            <img id="personel_image_display" src="<?= base_url('public/img/exam-logo.webp') ?>" alt=""
                                class="img-fluid rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid white; box-shadow: var(--box-shadow);">
                        <?php } ?>
                        <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" style="width: 40px; height: 40px; padding: 0; background: var(--gold-gradient); border: none;" data-bs-toggle="modal" data-bs-target="#cropperModal">
                            <i class="ti ti-camera"></i>
                        </button>
                    </div>

                    <input type="hidden" id="image_personel" name="base64_image">

                    <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel"
                        aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content" style="border-radius: var(--border-radius);">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold" id="cropperModalLabel"><?= lang('Common.upload_image') ?></h5>
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
                                            <div class="cropper-container cropper-bg rounded overflow-hidden">
                                                <img id="cropImage" alt="Image to crop"
                                                    style="max-width: 100%; display: none" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="preview-container rounded overflow-hidden border" id="preview" style="height: 200px;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                                        <?= lang('Common.cancel') ?>
                                    </button>
                                    <button type="button" id="cropButton" class="btn btn-primary rounded-pill px-4">
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
                <div class="col-md-10">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold text-muted"><?= lang('Common.team_name_thai') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="<?= (!empty($team)) ? $team['name'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.team_name_thai_remark') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label for="name_en" class="form-label fw-bold text-muted"><?= lang('Common.team_name_english') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="name_en" name="name_en" value="<?= (!empty($team)) ? $team['name_en'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.team_name_english_remark') ?></div>
                        </div>

                        <div class="col-md-12">
                            <label for="organize_name" class="form-label fw-bold text-muted"><?= lang('Common.organization_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="organize_name" name="organize_name"
                                value="<?= (!empty($team)) ? $team['organize_name'] : '' ?>" class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.organization_name_remark') ?></div>
                        </div>

                        <div class="col-md-6">
                            <label for="coach_name" class="form-label fw-bold text-muted"><?= lang('Common.head_coach_name_label') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="coach_name" name="coach_name" value="<?= (!empty($team)) ? $team['coach_name'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.head_coach_name_remark') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label for="coach_phone" class="form-label fw-bold text-muted"><?= lang('Common.mobile_coach') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="coach_phone" name="coach_phone"
                                value="<?= (!empty($team)) ? $team['coach_phone'] : '' ?>"
                                class="form-control phone-input" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.mobile_coach_remark') ?></div>
                        </div>

                        <div class="col-md-6">
                            <label for="contact_name" class="form-label fw-bold text-muted"><?= lang('Common.contact_name_label') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="contact_name" name="contact_name"
                                value="<?= (!empty($team)) ? $team['contact_name'] : '' ?>" class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.contact_name_remark') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label fw-bold text-muted"><?= lang('Common.mobile_contact') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="contact_phone" name="contact_phone"
                                value="<?= (!empty($team)) ? $team['contact_phone'] : '' ?>"
                                class="form-control phone-input" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.mobile_contact_remark') ?></div>
                        </div>

                        <div class="col-md-6">
                            <label for="contact_email" class="form-label fw-bold text-muted"><?= lang('Common.email_contact') ?><span class="text-danger">*</span></label>
                            <input type="email" id="contact_email" name="contact_email"
                                value="<?= (!empty($team)) ? $team['contact_email'] : '' ?>" class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.email_contact_remark') ?></div>
                        </div>
                        <div class="col-md-6">
                            <label for="contact_line" class="form-label fw-bold text-muted"><?= lang('Common.line_id_contact') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="contact_line" name="contact_line"
                                value="<?= (!empty($team)) ? $team['contact_line'] : '' ?>" class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.line_id_contact_remark') ?></div>
                        </div>

                        <div class="col-md-12">
                            <label for="location_display" class="form-label fw-bold text-muted"><?= lang('Common.field_location') ?></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="ti ti-map-pin text-danger"></i></span>
                                <input type="text" id="location_display" class="form-control border-start-0"
                                    placeholder="<?= lang('Common.select_location_placeholder') ?>" readonly style="border-radius: 0 8px 8px 0; padding: 10px 15px;">
                                <button class="btn btn-outline-secondary ms-2 rounded" type="button" data-bs-toggle="modal"
                                    data-bs-target="#mapModal" style="border-radius: 8px !important;">
                                    <?= lang('Common.select_location_btn') ?>
                                </button>
                            </div>
                            <input type="hidden" id="latitude" name="latitude" value="<?= @$team['latitude'] ?>">
                            <input type="hidden" id="longitude" name="longitude" value="<?= @$team['longitude'] ?>">
                        </div>
                    </div>

                    <div class="row mt-5 mb-3">
                        <div class="col-md-12 text-center">
                            <button type="button" class="btn btn-primary rounded-pill px-5 py-2 fw-bold btn-submit shadow-sm" id="pre-submit-btn" style="background: var(--gold-gradient); border: none;">
                                <i class="ti ti-device-floppy me-2"></i> <?= lang('Common.save_data') ?>
                            </button>
                        </div>
                    </div>
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

<div class="modal fade" id="mapModal" tabindex="-1" aria-labelledby="mapModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="mapModalLabel"><?= lang('Common.specify_map_location') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="map-canvas" style="height: 500px; width: 100%; position: relative;"></div>
                <p class="text-muted mt-2 text-center"><?= lang('Common.drag_marker') ?></p>
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
    const langConfirmSaveData = '<?= lang("Common.confirm_save_data") ?>';
    const langConfirmSaveText = '<?= lang("Common.confirm_save_text") ?>';
    const langConfirmBtn = '<?= lang("Common.confirm") ?>';
    const langCancelBtn = '<?= lang("Common.cancel") ?>';
    const langGeolocationError = '<?= lang("Common.geolocation_error") ?>';
    const langBrowserNoGeolocation = '<?= lang("Common.browser_no_geolocation") ?>';

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

            const modal = bootstrap.Modal.getInstance(document.getElementById("cropperModal"));
            modal.hide();
        }
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

    // --- Document Ready Logic ---
    $(document).ready(function() {
        $("#pre-submit-btn").click(function() {
            Swal.fire({
                title: langConfirmSaveData,
                text: langConfirmSaveText,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#16478d",
                cancelButtonColor: "#d33",
                confirmButtonText: langConfirmBtn,
                cancelButtonText: langCancelBtn
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
                        alert(langGeolocationError);
                    }
                );
            } else {
                alert(langBrowserNoGeolocation);
            }
        });
    });
</script>
<?php $this->endSection(); ?>