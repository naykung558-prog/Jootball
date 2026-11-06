<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                    <?php if (!empty($academy)): ?>
                        <?= lang('Common.academy_edit') ?>
                    <?php else: ?>
                        <?= lang('Common.academy_add_new') ?>
                    <?php endif; ?>
                </h4>
                <p class="text-muted mb-0 small"><?= lang('Common.academy_fill_details') ?></p>
            </div>
            <a href="<?= base_url('admin/academy') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <strong><i class="fas fa-exclamation-circle me-1"></i></strong> <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <i class="fas fa-check-circle me-1"></i> <?= session()->getFlashdata('success'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/academy/save') ?>" id="academy-form" method="POST" enctype="multipart/form-data"
            class="once-only" autocomplete="off">

            <!-- Logo Upload Section -->
            <div class="row justify-content-center mb-5">
                <div class="col-md-12 text-center">
                    <div class="position-relative d-inline-block">
                        <?php if (!empty($academy['logo'])): ?>
                            <img id="logo_preview" src="<?= base_url($academy['logo']) ?>" alt="Academy Logo"
                                class="img-fluid rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid white; box-shadow: var(--box-shadow);">
                        <?php else: ?>
                            <img id="logo_preview" src="<?= base_url('public/img/exam-logo.webp') ?>" alt="Academy Logo"
                                class="img-fluid rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid white; box-shadow: var(--box-shadow);">
                        <?php endif; ?>
                        <label for="logo_file" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" style="width: 40px; height: 40px; padding: 0; background: var(--gold-gradient); border: none; cursor: pointer; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-camera"></i>
                        </label>
                    </div>
                    <input type="file" id="logo_file" name="logo" accept="image/*" class="d-none">
                    <p class="text-muted small mt-2"><?= lang('Common.academy_click_upload') ?></p>
                </div>
            </div>

            <input type="hidden" name="id" value="<?= isset($academy['id']) ? $academy['id'] : '' ?>">

            <div class="row justify-content-center">
                <div class="col-md-10">
                    <div class="row g-3">
                        <!-- Academy Name (Thai) -->
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold text-muted"><?= lang('Common.academy_name_th') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="name" name="name" value="<?= isset($academy['name']) ? $academy['name'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" required>
                            <div class="form-text small text-muted"><?= lang('Common.academy_enter_name_th') ?></div>
                        </div>

                        <!-- Academy Name (English) -->
                        <div class="col-md-6">
                            <label for="name_en" class="form-label fw-bold text-muted"><?= lang('Common.academy_name_en') ?></label>
                            <input type="text" id="name_en" name="name_en" value="<?= isset($academy['name_en']) ? $academy['name_en'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                            <div class="form-text small text-muted"><?= lang('Common.academy_enter_name_en') ?></div>
                        </div>

                        <!-- Description -->
                        <div class="col-md-12">
                            <label for="description" class="form-label fw-bold text-muted"><?= lang('Common.academy_description_label') ?></label>
                            <textarea id="description" name="description" rows="4"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;"><?= isset($academy['description']) ? $academy['description'] : '' ?></textarea>
                            <div class="form-text small text-muted"><?= lang('Common.academy_brief_desc') ?></div>
                        </div>

                        <!-- Contact Name -->
                        <div class="col-md-6">
                            <label for="contact_name" class="form-label fw-bold text-muted"><?= lang('Common.contact_name_label') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="contact_name" name="contact_name"
                                value="<?= isset($academy['contact_name']) ? $academy['contact_name'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" required>
                        </div>

                        <!-- Contact Phone -->
                        <div class="col-md-6">
                            <label for="contact_phone" class="form-label fw-bold text-muted"><?= lang('Common.mobile_contact') ?> <span class="text-danger">*</span></label>
                            <input type="text" id="contact_phone" name="contact_phone"
                                value="<?= isset($academy['contact_phone']) ? $academy['contact_phone'] : '' ?>"
                                class="form-control phone-input" style="border-radius: 8px; padding: 10px 15px;" required>
                        </div>

                        <!-- Contact Email -->
                        <div class="col-md-6">
                            <label for="contact_email" class="form-label fw-bold text-muted"><?= lang('Common.email_contact') ?></label>
                            <input type="email" id="contact_email" name="contact_email"
                                value="<?= isset($academy['contact_email']) ? $academy['contact_email'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <!-- Contact LINE -->
                        <div class="col-md-6">
                            <label for="contact_line" class="form-label fw-bold text-muted"><?= lang('Common.line_id') ?></label>
                            <input type="text" id="contact_line" name="contact_line"
                                value="<?= isset($academy['contact_line']) ? $academy['contact_line'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <!-- Address -->
                        <div class="col-md-12">
                            <label for="address" class="form-label fw-bold text-muted"><?= lang('Common.academy_address') ?></label>
                            <textarea id="address" name="address" rows="3"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;"><?= isset($academy['address']) ? $academy['address'] : '' ?></textarea>
                        </div>

                        <!-- Location (Map Picker) -->
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
                            <input type="hidden" id="latitude" name="latitude" value="<?= @$academy['latitude'] ?>">
                            <input type="hidden" id="longitude" name="longitude" value="<?= @$academy['longitude'] ?>">
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row mt-5 mb-3">
                        <div class="col-md-12 text-center">
                            <a href="<?= base_url('admin/academy') ?>" class="btn btn-light rounded-pill px-5 py-2 fw-bold text-muted border me-2">
                                <i class="fas fa-times me-2"></i> <?= lang('Common.cancel') ?>
                            </a>
                            <button type="button" class="btn btn-primary rounded-pill px-5 py-2 fw-bold btn-submit shadow-sm" id="pre-submit-btn" style="background: var(--gold-gradient); border: none;">
                                <i class="fas fa-save me-2"></i> <?= lang('Common.save') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Ensure Google Places autocomplete dropdown shows above the Bootstrap modal -->
<style>
    .pac-container {
        z-index: 1060 !important;
    }
</style>

<!-- Map Modal -->
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
                <button type="button" class="btn btn-secondary" id="getCurrentLocationBtn"><i class="fas fa-crosshairs me-1"></i><?= lang('Common.current_location_btn') ?></button>
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
    const langGeolocationError = '<?= lang("Common.geolocation_error") ?>';
    const langBrowserNoGeolocation = '<?= lang("Common.browser_no_geolocation") ?>';

    // Logo file preview
    document.getElementById('logo_file').addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            var reader = new FileReader();
            reader.onload = function(event) {
                document.getElementById('logo_preview').src = event.target.result;
            };
            reader.readAsDataURL(file);
        }
    });

    // --- Google Map Logic ---
    let map;
    let marker;
    const mapModalElement = document.getElementById('mapModal');

    function initMap() {
        const initialLat = parseFloat($('#latitude').val()) || 18.7967;
        const initialLng = parseFloat($('#longitude').val()) || 98.9854;
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

    // --- Document Ready Logic ---
    $(document).ready(function() {
        $('#pre-submit-btn').click(function() {
            Swal.fire({
                title: '<?= lang('Common.confirm_save_data') ?>',
                text: '<?= lang('Common.academy_save_confirm') ?>',
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16478d',
                cancelButtonColor: '#d33',
                confirmButtonText: '<?= lang('Common.confirm') ?>',
                cancelButtonText: '<?= lang('Common.cancel') ?>'
            }).then(function(result) {
                if (result.isConfirmed) {
                    $('#academy-form').submit();
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
