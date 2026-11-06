<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<style>
    .stadium-img-clickable:hover {
        opacity: 0.7;
        transition: opacity 0.2s;
    }
</style>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-map-marker-alt text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                        <?php if (!empty($data['id'])): ?>
                            <?= lang('Common.edit') ?> <?= lang('Common.admin_stadium') ?>
                        <?php else: ?>
                            <?= lang('Common.add_stadium') ?>
                        <?php endif; ?>
                    </h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.manage_stadium_desc') ?></p>
                </div>
            </div>
            <a href="<?= base_url('admin/stadium') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <strong><i class="fas fa-exclamation-circle me-1"></i></strong>
                <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="<?= base_url('admin/stadium/save') ?>" method="POST" enctype="multipart/form-data" class="once-only">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (!empty($data['id'])) ? $data['id'] : '' ?>">

            <div class="row justify-content-center">
                <div class="col-md-10">

                    <!-- Stadium Information -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-info-circle me-2" style="color: var(--secondary-color);"></i>
                        <?= lang('Common.stadium_information') ?>
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold text-muted">
                                <?= lang('Common.stadium_name') ?> <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="name" id="name"
                                   value="<?= (!empty($data['name'])) ? $data['name'] : '' ?>"
                                   class="form-control" required
                                   placeholder="<?= lang('Common.enter_stadium_name') ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <div class="col-md-6">
                            <label for="name_en" class="form-label fw-bold text-muted">
                                <?= lang('Common.stadium_name_en') ?>
                            </label>
                            <input type="text" name="name_en" id="name_en"
                                   value="<?= (!empty($data['name_en'])) ? $data['name_en'] : '' ?>"
                                   class="form-control"
                                   placeholder="<?= lang('Common.enter_stadium_name_en') ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>

                        <div class="col-12">
                            <label for="address" class="form-label fw-bold text-muted">
                                <?= lang('Common.stadium_address') ?>
                            </label>
                            <textarea name="address" id="address" class="form-control" rows="3"
                                      placeholder="<?= lang('Common.enter_stadium_address') ?>"
                                      style="border-radius: 8px; padding: 10px 15px;"><?= (!empty($data['address'])) ? $data['address'] : '' ?></textarea>
                        </div>

                        <div class="col-md-6">
                            <label for="is_active" class="form-label fw-bold text-muted">
                                <?= lang('Common.status') ?>
                            </label>
                            <select name="is_active" id="is_active" class="form-select" style="border-radius: 8px; padding: 10px 15px;">
                                <option value="1" <?= (isset($data['is_active']) && $data['is_active'] == 1) ? 'selected' : '' ?>><?= lang('Common.active') ?></option>
                                <option value="0" <?= (isset($data['is_active']) && $data['is_active'] == 0) ? 'selected' : '' ?>><?= lang('Common.inactive') ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Map Section -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-map-marked-alt me-2" style="color: var(--secondary-color);"></i>
                        <?= lang('Common.stadium_coordinates') ?>
                    </h6>

                    <p class="text-muted small mb-3">
                        <i class="fas fa-info-circle me-1"></i>
                        <?= lang('Common.select_location_on_map') ?>
                    </p>

                    <div class="mb-3">
                        <div id="map" class="w-100" style="height: 400px; border-radius: var(--border-radius); border: 2px solid #e2e8f0; overflow: hidden;"></div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="latitude" class="form-label fw-bold text-muted">
                                <i class="fas fa-crosshairs me-1" style="color: var(--secondary-color);"></i>
                                <?= lang('Common.stadium_latitude') ?>
                            </label>
                            <input type="text" name="latitude" id="latitude"
                                   value="<?= (!empty($data['latitude'])) ? $data['latitude'] : '' ?>"
                                   class="form-control" readonly
                                   placeholder="<?= lang('Common.stadium_latitude') ?>"
                                   style="border-radius: 8px; padding: 10px 15px; background: #f8f9fa;">
                        </div>

                        <div class="col-md-6">
                            <label for="longitude" class="form-label fw-bold text-muted">
                                <i class="fas fa-crosshairs me-1" style="color: var(--secondary-color);"></i>
                                <?= lang('Common.stadium_longitude') ?>
                            </label>
                            <input type="text" name="longitude" id="longitude"
                                   value="<?= (!empty($data['longitude'])) ? $data['longitude'] : '' ?>"
                                   class="form-control" readonly
                                   placeholder="<?= lang('Common.stadium_longitude') ?>"
                                   style="border-radius: 8px; padding: 10px 15px; background: #f8f9fa;">
                        </div>
                    </div>

                    <!-- Image Section -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-images me-2" style="color: var(--secondary-color);"></i>
                        <?= lang('Common.stadium_images') ?>
                    </h6>

                    <?php if (!empty($images)): ?>
                    <div id="gallery" class="d-flex flex-wrap gap-3 mb-4">
                        <?php foreach ($images as $img): ?>
                        <div id="img-<?= $img['id'] ?>" style="width: 200px;">
                            <div class="position-relative overflow-hidden" style="border-radius: var(--border-radius); border: 3px solid <?= $img['is_cover'] ? 'var(--secondary-color)' : '#e9ecef' ?>; background: #e9ecef; transition: var(--transition);">
                                <?php if ($img['is_cover']): ?>
                                    <span class="position-absolute top-0 start-0 m-2 px-3 py-1 fw-bold" style="font-size: 11px; background: var(--gold-gradient); color: white; border-radius: 20px; box-shadow: var(--gold-shadow); z-index: 2; pointer-events: none;">
                                        <i class="fas fa-star me-1"></i><?= lang('Common.cover') ?>
                                    </span>
                                <?php endif; ?>
                                <img src="<?= base_url($img['image_path']) ?>" class="w-100 stadium-img <?= $img['is_cover'] ? '' : 'stadium-img-clickable' ?>" style="height: 160px; object-fit: cover; display: block; <?= $img['is_cover'] ? '' : 'cursor: pointer;' ?>" <?= $img['is_cover'] ? '' : 'onclick="setCover(' . $img['id'] . ')"' ?> title="<?= $img['is_cover'] ? '' : lang('Common.set_cover') ?>">
                                <button type="button" class="btn w-100 py-2 fw-bold" style="background: #f08080; color: white; border: none; border-radius: 0 0 calc(var(--border-radius) - 3px) calc(var(--border-radius) - 3px); font-size: 13px;" onclick="deleteImage(<?= $img['id'] ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <?php endif; ?>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">
                            <?= lang('Common.upload') ?>
                        </label>
                        <input type="file" name="images[]" accept="image/*" multiple class="form-control" style="border-radius: 8px; padding: 8px 15px;">
                        <div class="form-text small text-muted mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            <?= lang('Common.stadium_upload_hint') ?>
                        </div>
                    </div>

                    <!-- Submit Buttons -->
                    <div class="row mt-5 mb-3">
                        <div class="col-md-12 text-center">
                            <a href="<?= base_url('admin/stadium') ?>" class="btn btn-light rounded-pill px-5 py-2 fw-bold text-muted border me-2">
                                <i class="fas fa-times me-2"></i> <?= lang('Common.cancel') ?>
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold btn-submit shadow-sm" style="background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow);">
                                <i class="fas fa-save me-2"></i> <?= lang('Common.save_data') ?>
                            </button>
                        </div>
                    </div>

                </div>
            </div>
        </form>
    </div>
</div>

<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
<script>
    const langSaveSuccess = '<?= lang("Common.save_success") ?>';
    const langDeleteSuccess = '<?= lang("Common.delete_success") ?>';
    const langConfirmDeleteTitle = '<?= lang("Common.confirm_delete_title") ?>';
    const langYesDelete = '<?= lang("Common.yes_delete") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';
    const langError = '<?= lang("Common.error") ?>';
    const langSetCoverSuccess = '<?= lang("Common.save_success") ?>';
    const stadiumId = '<?= (!empty($data['id'])) ? $data['id'] : '' ?>';

    // Google Maps
    let map, marker;

    function initMap() {
        const defaultLat = <?= (!empty($data['latitude'])) ? $data['latitude'] : '13.7563' ?>;
        const defaultLng = <?= (!empty($data['longitude'])) ? $data['longitude'] : '100.5018' ?>;
        const defaultZoom = <?= (!empty($data['latitude'])) ? '15' : '6' ?>;

        const center = { lat: parseFloat(defaultLat), lng: parseFloat(defaultLng) };

        map = new google.maps.Map(document.getElementById('map'), {
            center: center,
            zoom: parseInt(defaultZoom),
            mapTypeControl: true,
            streetViewControl: false
        });

        marker = new google.maps.Marker({
            position: center,
            map: map,
            draggable: true,
            title: '<?= lang("Common.select_location_on_map") ?>'
        });

        <?php if (empty($data['latitude'])): ?>
        marker.setVisible(false);
        <?php endif; ?>

        map.addListener('click', function(e) {
            placeMarker(e.latLng);
        });

        marker.addListener('dragend', function(e) {
            updateLatLng(e.latLng);
        });
    }

    function placeMarker(location) {
        marker.setPosition(location);
        marker.setVisible(true);
        updateLatLng(location);
    }

    function updateLatLng(location) {
        document.getElementById('latitude').value = location.lat().toFixed(8);
        document.getElementById('longitude').value = location.lng().toFixed(8);
    }

    // Image Gallery Functions
    function deleteImage(imageId) {
        Swal.fire({
            title: langConfirmDeleteTitle,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#16478d',
            cancelButtonColor: '#d33',
            confirmButtonText: langYesDelete,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url("admin/stadium/delete-image") ?>',
                    method: 'POST',
                    data: {
                        image_id: imageId,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            $('#img-' + imageId).remove();
                            Swal.fire({ icon: 'success', title: langDeleteSuccess, timer: 1000, showConfirmButton: false });
                        }
                    }
                });
            }
        });
    }

    function setCover(imageId) {
        $.ajax({
            url: '<?= base_url("admin/stadium/set-cover-image") ?>',
            method: 'POST',
            data: {
                stadium_id: stadiumId,
                image_id: imageId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(res) {
                if (res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: langSetCoverSuccess,
                        timer: 1000,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                }
            }
        });
    }

    if (typeof loadGoogleMapsAPI === 'function') {
        loadGoogleMapsAPI(initMap);
    }
</script>

<?php $this->endSection() ?>
