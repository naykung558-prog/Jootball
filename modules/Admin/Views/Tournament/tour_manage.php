<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<style>
    /* Select2 Custom Styling */
    .select2-container .select2-selection--single {
        height: 46px !important; /* Match common input height */
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        display: flex !important;
        align-items: center !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 46px !important;
        padding-left: 15px !important;
        color: #212529 !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 44px !important;
        right: 10px !important;
    }

    .select2-dropdown {
        border-radius: 8px !important;
        border-color: #dee2e6 !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1);
    }

    /* Select2 Multiple Styling */
    .select2-container--default .select2-selection--multiple {
        border: 1px solid #dee2e6 !important;
        border-radius: 8px !important;
        padding: 5px 10px !important;
        min-height: 46px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice {
        background-color: #16478D !important;
        color: #fff !important;
        border: none !important;
        border-radius: 4px !important;
        padding: 3px 8px !important;
    }
    .select2-container--default .select2-selection--multiple .select2-selection__choice__remove {
        color: #fff !important;
        margin-right: 5px !important;
    }
</style>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">

        <?php echo view("Modules\Admin\Views\Tournament\subnav.php"); ?>

        <form id="tournament-form" action="<?= base_url('admin/tournament/save-tournament') ?>" method="POST"
            enctype="multipart/form-data" class="once-only" autocomplete="off">
            <div class="row justify-content-center mt-4 mb-4">

                <div class="col-md-12 text-center mb-4">
                    <h4 class="fw-bold" style="color: var(--primary-color);"><?= lang('Common.competition_info') ?></h4>
                    <p class="text-muted small"><?= lang('Common.fill_competition_details') ?></p>
                </div>

                <div class="col-md-12 text-center mb-4">
                    <div class="position-relative d-inline-block">
                        <?php if (!empty($data['logo'])) { ?>
                            <img id="personel_image_display" src="<?= base_url($data['logo']) ?>" alt="" class="img-fluid rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid white; box-shadow: var(--box-shadow);">
                        <?php } else { ?>
                            <img id="personel_image_display" src="<?= base_url('public/img/no-image.png') ?>" alt="" class="img-fluid rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover; border: 4px solid white; box-shadow: var(--box-shadow);">
                        <?php } ?>
                        <button type="button" class="btn btn-sm btn-primary position-absolute bottom-0 end-0 rounded-circle" style="width: 40px; height: 40px; padding: 0;" data-bs-toggle="modal" data-bs-target="#cropperModal">
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>

                    <input type="hidden" id="image_personel" name="base64_image">

                    <div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content" style="border-radius: var(--border-radius);">
                                <div class="modal-header border-0">
                                    <h5 class="modal-title fw-bold" id="cropperModalLabel"><?= lang('Common.upload_image') ?></h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <!-- Input อัปโหลดรูป -->
                                    <div class="row">
                                        <div class="col-lg-12">
                                            <input type="file" id="uploadImage" class="form-control mb-3" accept="image/*" />
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="cropper-container cropper-bg rounded overflow-hidden">
                                                <img id="cropImage" alt="Image to crop" style="max-width: 100%; display: none" />
                                            </div>
                                        </div>
                                        <div class="col-lg-6">
                                            <div class="preview-container rounded overflow-hidden border" id="preview" style="height: 200px;"></div>
                                        </div>
                                    </div>
                                </div>
                                <div class="modal-footer border-0">
                                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                                    <button type="button" id="cropButton" class="btn btn-primary rounded-pill px-4"><?= lang('Common.save') ?></button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hidden -->
            <input type="hidden" name="tour_id" value="<?= (!empty($data['tour_id'])) ? $data['tour_id'] : '' ?>">
            <input type="hidden" name="team_id" value="<?= session()->get('team.id') ?>">

            <div class="row justify-content-center">
                <div class="col-md-8">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="name" class="form-label fw-bold text-muted"><?= lang('Common.competition_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="name" id="name" value="<?= (!empty($data['name'])) ? $data['name'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" required>
                        </div>
                        <div class="col-md-6">
                            <label for="name_en" class="form-label fw-bold text-muted"><?= lang('Common.competition_name_en') ?></label>
                            <input type="text" name="name_en" id="name_en" value="<?= (!empty($data['name_en'])) ? $data['name_en'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-6">
                            <label for="generation" class="form-label fw-bold text-muted"><?= lang('Common.generation') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="generation" id="generation" value="<?= (!empty($data['generation'])) ? $data['generation'] : '' ?>"
                                class="form-control generation" style="border-radius: 8px; padding: 10px 15px;" required>
                        </div>
                        <div class="col-md-6">
                            <label for="player_count" class="form-label fw-bold text-muted"><?= lang('Common.player_submission_count') ?></label>
                            <input type="number" name="player_count" id="player_count" value="<?= (!empty($data['player_count'])) ? $data['player_count'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;" min="0">
                        </div>
                        <div class="col-md-6">
                            <label for="start_date" class="form-label fw-bold text-muted"><?= lang('Common.registration_start_date') ?></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                <input type="text" id="start_date" name="start_date" class="form-control border-start-0 date_picker" style="border-radius: 0 8px 8px 0; padding: 10px 15px;" value="<?= (!empty($data['start_date'])) ? date('d/m/Y', strtotime($data['start_date'])) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="end_date" class="form-label fw-bold text-muted"><?= lang('Common.registration_end_date') ?></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                <input type="text" id="end_date" name="end_date" class="form-control border-start-0 date_picker" style="border-radius: 0 8px 8px 0; padding: 10px 15px;" value="<?= (!empty($data['end_date'])) ? date('d/m/Y', strtotime($data['end_date'])) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="tournament_start_date" class="form-label fw-bold text-muted"><?= lang('Common.competition_start_date') ?></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                <input type="text" id="tournament_start_date" name="tournament_start_date" class="form-control border-start-0 date_picker" style="border-radius: 0 8px 8px 0; padding: 10px 15px;" value="<?= (!empty($data['tournament_start_date']) &&  $data['tournament_start_date'] != '0000-00-00') ? date('d/m/Y', strtotime($data['tournament_start_date'])) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label for="tournament_end_date" class="form-label fw-bold text-muted"><?= lang('Common.competition_end_date') ?></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;"><i class="fas fa-calendar-alt text-muted"></i></span>
                                <input type="text" id="tournament_end_date" name="tournament_end_date" class="form-control border-start-0 date_picker" style="border-radius: 0 8px 8px 0; padding: 10px 15px;" value="<?= (!empty($data['tournament_end_date']) && $data['tournament_end_date'] != '0000-00-00') ? date('d/m/Y', strtotime($data['tournament_end_date'])) : '' ?>">
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label for="detail" class="form-label fw-bold text-muted"><?= lang('Common.description') ?></label>
                            <textarea type="text" name="detail" id="detail" class="form-control detail" style="border-radius: 8px; padding: 10px 15px;" rows="4"><?= (!empty($data['detail'])) ? $data['detail'] : '' ?></textarea>
                        </div>
                         <div class="col-md-6 mt-3">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.usage_status') ?></label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" <?= (!empty($data['is_active']) && $data['is_active'] == 1) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2" for="is_active"><?= lang('Common.active') ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 mt-3">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.registration_status') ?></label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="is_registration_open" name="is_registration_open" value="1" <?= (!empty($data['is_registration_open']) && $data['is_registration_open'] == 1) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2" for="is_registration_open"><?= lang('Common.toggle_registration') ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.payment_system') ?></label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable_payment" name="enable_payment" value="1" <?= (!empty($data['enable_payment']) && $data['enable_payment'] == 1) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2" for="enable_payment"><?= lang('Common.enable_payment') ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.registration_documents') ?></label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="enable_document" name="enable_document" value="1" <?= (!empty($data['enable_document']) && $data['enable_document'] == 1) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2" for="enable_document"><?= lang('Common.enable_document_attachment') ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.duplicate_player_check') ?></label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="prevent_duplicate_player" name="prevent_duplicate_player" value="1" <?= (!empty($data['prevent_duplicate_player']) && $data['prevent_duplicate_player'] == 1) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2" for="prevent_duplicate_player"><?= lang('Common.enable_prevent_duplicate_player') ?></label>
                                </div>
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i><?= lang('Common.prevent_duplicate_player_hint') ?></small>
                        </div>
                        <div class="col-md-12 mt-3" id="duplicateCheckSection" style="display: none;">
                            <label class="form-label fw-bold text-muted"><i class="fas fa-link me-1"></i><?= lang('Common.cross_tournament_check') ?></label>
                            <select class="form-select" id="linked_tournaments" name="linked_tournaments[]" multiple>
                                <?php if (!empty($tournaments)) : ?>
                                    <?php foreach ($tournaments as $tour): ?>
                                        <?php
                                            // ไม่แสดงทัวร์นาเมนต์ตัวเอง
                                            if (!empty($data['tour_id']) && $tour['tour_id'] == $data['tour_id']) continue;
                                            $tourLabel = $tour['name'];
                                            if (!empty($tour['generation'])) {
                                                $tourLabel .= ' (' . $tour['generation'] . ')';
                                            }
                                            $isSelected = in_array($tour['tour_id'], $linked_tournament_ids ?? []);
                                        ?>
                                        <option value="<?= $tour['tour_id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= $tourLabel ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i><?= lang('Common.linked_tournaments_hint') ?></small>
                        </div>
                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-bold text-muted"><?= lang('Common.source_player_check') ?></label>
                            <div class="d-flex align-items-center">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" id="require_source_players" name="require_source_players" value="1" <?= (!empty($data['require_source_players']) && $data['require_source_players'] == 1) ? 'checked' : '' ?> style="width: 3em; height: 1.5em;">
                                    <label class="form-check-label ms-2" for="require_source_players"><?= lang('Common.enable_require_source_players') ?></label>
                                </div>
                            </div>
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i><?= lang('Common.require_source_players_hint') ?></small>
                        </div>
                        <div class="col-md-12 mt-3" id="sourceCheckSection" style="display: none;">
                            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                                <label class="form-label fw-bold text-muted mb-0"><i class="fas fa-link me-1"></i><?= lang('Common.select_source_tournaments') ?></label>
                                <button type="button" class="btn btn-sm btn-outline-primary rounded-pill" id="copyFromLinkedBtn">
                                    <i class="fas fa-copy me-1"></i><?= lang('Common.copy_from_duplicate_check') ?>
                                </button>
                            </div>
                            <select class="form-select" id="source_tournaments" name="source_tournaments[]" multiple>
                                <?php if (!empty($tournaments)) : ?>
                                    <?php foreach ($tournaments as $tour): ?>
                                        <?php
                                            // ไม่แสดงทัวร์นาเมนต์ตัวเอง
                                            if (!empty($data['tour_id']) && $tour['tour_id'] == $data['tour_id']) continue;
                                            $tourLabel = $tour['name'];
                                            if (!empty($tour['generation'])) {
                                                $tourLabel .= ' (' . $tour['generation'] . ')';
                                            }
                                            $isSelected = in_array($tour['tour_id'], $source_tournament_ids ?? []);
                                        ?>
                                        <option value="<?= $tour['tour_id'] ?>" <?= $isSelected ? 'selected' : '' ?>>
                                            <?= $tourLabel ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i><?= lang('Common.source_tournaments_hint') ?></small>
                        </div>
                        <div class="col-md-12 mt-3" id="documentSection" style="display: none;">
                            <label for="document_description" class="form-label fw-bold text-muted"><?= lang('Common.document_description_label') ?></label>
                            <textarea name="document_description" id="document_description" class="form-control" style="border-radius: 8px; padding: 10px 15px;" rows="3" placeholder="<?= lang('Common.document_description_placeholder') ?>"><?= (!empty($data['document_description'])) ? $data['document_description'] : '' ?></textarea>
                            <small class="text-muted"><i class="fas fa-info-circle me-1"></i><?= lang('Common.document_description_hint') ?></small>
                        </div>
                    <!-- PDF Upload Section -->
                        <div class="col-md-12 mt-4">
                            <label for="pdf_file" class="form-label fw-bold text-muted">
                                <i class="fas fa-file-pdf text-danger me-2"></i><?= lang('Common.pdf_detail_file') ?>
                            </label>
                            <?php if (!empty($data['pdf_path'])): ?>
                                <div class="alert alert-info d-flex align-items-center mb-3" role="alert">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <div class="flex-grow-1">
                                        <strong><?= lang('Common.current_file') ?>:</strong>
                                        <a href="<?= base_url($data['pdf_path']) ?>" target="_blank" class="alert-link">
                                            <?= basename($data['pdf_path']) ?>
                                        </a>
                                    </div>
                                    <a href="<?= base_url($data['pdf_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill">
                                        <i class="fas fa-eye me-1"></i><?= lang('Common.view_file') ?>
                                    </a>
                                </div>
                            <?php endif; ?>
                            <div class="input-group">
                                <input type="file" name="pdf_file" id="pdf_file" class="form-control" accept=".pdf" style="border-radius: 8px; padding: 10px 15px;">
                                <button class="btn btn-outline-secondary" type="button" onclick="document.getElementById('pdf_file').value = ''">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle me-1"></i><?= lang('Common.pdf_only_10mb') ?>
                            </small>
                        </div>

                        <div id="paymentSection" style="display: none;">
                        <div class="col-md-12 mt-4 text-center">
                            <hr class="my-4">
                            <h5 class="fw-bold" style="color: var(--primary-color);"><?= lang('Common.payment_information') ?></h5>
                        </div>

                        <div class="col-md-6">
                            <label for="bank_id" class="form-label fw-bold text-muted"><?= lang('Common.bank') ?></label>
                            <select name="bank_id" id="bank_name" class="form-select" style="border-radius: 8px; padding: 10px 15px;">
                                <option value=""><?= lang('Common.select_bank') ?></option>
                                <?php if (!empty($banks)) : ?>
                                    <?php foreach ($banks as $bank): ?>
                                        <?php
                                            // Create display string (e.g., "กสิกรไทย (KBANK)")
                                            $bankLabel = $bank['name'];
                                            if (!empty($bank['short_name'])) {
                                                $bankLabel .= " ({$bank['short_name']})";
                                            }
                                        ?>
                                        <option value="<?= $bank['id'] ?>" <?= (!empty($data['bank_id']) && $data['bank_id'] == $bank['id']) ? 'selected' : '' ?>>
                                            <?= $bankLabel ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="account_name" class="form-label fw-bold text-muted"><?= lang('Common.account_name') ?></label>
                            <input type="text" name="account_name" id="account_name" value="<?= (!empty($data['account_name'])) ? $data['account_name'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-6">
                            <label for="account_no" class="form-label fw-bold text-muted"><?= lang('Common.account_number') ?></label>
                            <input type="text" name="account_no" id="account_no" value="<?= (!empty($data['account_no'])) ? $data['account_no'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;"
                                inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>
                        <div class="col-md-6">
                            <label for="promptpay" class="form-label fw-bold text-muted"><?= lang('Common.promptpay') ?></label>
                            <input type="text" name="promptpay" id="promptpay" value="<?= (!empty($data['promptpay'])) ? $data['promptpay'] : '' ?>"
                                class="form-control" style="border-radius: 8px; padding: 10px 15px;"
                                inputmode="numeric" pattern="[0-9]*" oninput="this.value = this.value.replace(/[^0-9]/g, '')">
                        </div>

                        <div class="col-md-12">
                            <label for="qrcode" class="form-label fw-bold text-muted"><?= lang('Common.qrcode_payment') ?></label>
                            <?php if (!empty($data['qrcode'])): ?>
                                <div class="mb-2">
                                    <img src="<?= base_url($data['qrcode']) ?>" alt="QR Code" class="img-thumbnail" style="max-height: 200px;">
                                </div>
                            <?php endif; ?>
                            <input type="file" name="qrcode_file" id="qrcode" class="form-control" accept="image/*" style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        </div><!-- /paymentSection -->
                    </div>

                    <div class="row mt-5 mb-3">
                        <div class="col-md-12 text-center">
                            <a href="<?= base_url('admin/tournament') ?>" class="btn btn-light rounded-pill px-4 py-2 me-2 fw-bold text-muted border">
                                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold btn-submit shadow-sm">
                                <i class="fas fa-save me-2"></i> <?= lang('Common.save') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langSelectBank = '<?= lang("Common.select_bank") ?>';
    const langConfirmSave = '<?= lang("Common.confirm_save") ?>';
    const langConfirmSaveText = '<?= lang("Common.confirm_save_competition") ?>';
    const langConfirm = '<?= lang("Common.confirm") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';
    const langSuccess = '<?= lang("Common.success") ?>';
    const langSaveDataSuccess = '<?= lang("Common.save_data_success") ?>';
    const langErrorOccurred = '<?= lang("Common.error_occurred") ?>';
    const langCannotSave = '<?= lang("Common.cannot_save") ?>';
    const langCannotConnectServer = '<?= lang("Common.cannot_connect_server") ?>';

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
                cropImage.src = e.target.result; // แสดงภาพที่อัปโหลด
                cropImage.style.display = "block"; // แสดงภาพ
                if (cropper) {
                    cropper.destroy(); // รีเซ็ต Cropper ถ้ามีอยู่ก่อนแล้ว
                }
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1, // อัตราส่วน 1:1
                    viewMode: 1, // ห้ามครอบเกินพื้นที่
                    autoCropArea: 1, // ครอบพื้นที่ทั้งหมด
                    cropBoxResizable: false, // ไม่อนุญาตให้ปรับขนาด crop box
                    cropBoxMovable: true, // สามารถเลื่อน crop box ได้
                    preview: "#preview", // แสดงผลพรีวิวใน div
                });
            };
            reader.readAsDataURL(file);
        }
    });

    // เมื่อกดปุ่ม Crop
    cropButton.addEventListener("click", () => {
        if (cropper) {
            const canvas = cropper.getCroppedCanvas({
                width: 350,
                height: 350, // กำหนดขนาดภาพครอบ 350x350px
            });
            const croppedImageURL = canvas.toDataURL("image/png");
            // console.log(croppedImageURL);
            $('#image_personel').val(croppedImageURL);
            $('#personel_image_display').attr('src', croppedImageURL);

            const modal = bootstrap.Modal.getInstance(
                document.getElementById("cropperModal")
            );
            modal.hide();
        }
    });

    $(document).ready(function() {
        $('.date_picker').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
            language: 'th-en',
        });

        // Initialize Select2 for Bank Dropdown
        $('#bank_name').select2({
            theme: 'bootstrap-5', // Use bootstrap-5 theme if available, or remove if not
            width: '100%',
            placeholder: langSelectBank,
            allowClear: true
        });

        // Toggle ซ่อน/แสดง sections
        $('#enable_payment').on('change', function() {
            $('#paymentSection').toggle(this.checked);
        }).trigger('change');

        $('#enable_document').on('change', function() {
            $('#documentSection').toggle(this.checked);
        }).trigger('change');

        // Toggle ซ่อน/แสดง duplicateCheckSection ตาม prevent_duplicate_player
        $('#prevent_duplicate_player').on('change', function() {
            $('#duplicateCheckSection').toggle(this.checked);
        }).trigger('change');

        // Initialize Select2 for linked tournaments (multi-select)
        $('#linked_tournaments').select2({
            placeholder: '<?= lang('Common.select_linked_tournaments') ?>',
            allowClear: true,
            width: '100%'
        });

        // Toggle ซ่อน/แสดง sourceCheckSection ตาม require_source_players
        $('#require_source_players').on('change', function() {
            $('#sourceCheckSection').toggle(this.checked);
        }).trigger('change');

        // Initialize Select2 for source tournaments (multi-select)
        $('#source_tournaments').select2({
            placeholder: '<?= lang('Common.select_source_tournaments') ?>',
            allowClear: true,
            width: '100%'
        });

        // คัดลอกรายการที่เลือกใน "ตรวจสอบข้ามรายการแข่งขัน" มาใส่ "เลือกรายการแข่งขันต้นทาง"
        $('#copyFromLinkedBtn').on('click', function() {
            const linkedVals = $('#linked_tournaments').val() || [];
            if (linkedVals.length === 0) {
                Swal.fire({
                    icon: 'info',
                    title: '<?= lang('Common.copy_from_duplicate_check_empty') ?>',
                    timer: 2000,
                    showConfirmButton: false
                });
                return;
            }
            $('#source_tournaments').val(linkedVals).trigger('change');
        });

        $('#tournament-form').on('submit', function(e) {
            e.preventDefault();
            const form = $(this);
            const formData = new FormData(this);

            // Validate: เปิด require_source_players ต้องเลือก source tournaments อย่างน้อย 1 รายการ
            if ($('#require_source_players').is(':checked') && (($('#source_tournaments').val() || []).length === 0)) {
                Swal.fire({
                    icon: 'warning',
                    title: '<?= lang('Common.source_require_needs_source') ?>',
                    confirmButtonColor: '#3085d6'
                });
                return;
            }

            Swal.fire({
                title: langConfirmSave,
                text: langConfirmSaveText,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: langConfirm,
                cancelButtonText: langCancel
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: form.attr('action'),
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: langSuccess,
                                    text: response.msg || langSaveDataSuccess,
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.href = '<?= base_url("admin/tournament") ?>';
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: langErrorOccurred,
                                    text: response.msg || langCannotSave,
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: langErrorOccurred,
                                text: langCannotConnectServer,
                            });
                        }
                    });
                }
            });
        });
    });
</script>
<?php $this->endSection(); ?>