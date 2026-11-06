<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-header bg-white border-bottom py-3">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title fw-bold mb-0 text-dark">
                <i class="ti ti-certificate me-2 text-primary"></i> <?= lang('Common.certificates_title') ?>
            </h5>
            <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold shadow-sm" id="bt_add" onclick="$('#formMock')[0].reset(); $('#certImagePreviewModal').attr('src','').parent().hide(); $('#createModal').modal('show');">
                <i class="ti ti-plus me-2"></i> <?= lang('Common.add_item') ?>
            </button>
        </div>
    </div>
    <div class="card-body p-4">

        <form action="<?= base_url('admin/coach/save-certs') ?>" method="POST" enctype="multipart/form-data" class="once-only" autocomplete="off">
            <input type="hidden" name="coach_id" value="<?= (!empty($id)) ? $id : '' ?>">

            <?php if (empty($data)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="ti ti-certificate-off" style="font-size: 3rem; opacity: 0.3;"></i>
                    <p class="mt-2 mb-0"><?= lang('Common.no_data') ?></p>
                </div>
            <?php endif; ?>

            <?php foreach ($data as $key => $cert) { ?>
                <div class="cert-card mb-3">
                    <div class="border rounded-3 p-3" style="background: #fafbfd;">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-primary-subtle text-primary fw-bold px-3 py-2" style="border-radius: 20px;">
                                <i class="ti ti-certificate me-1"></i> #<?= $key + 1 ?>
                            </span>
                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="delCert(this)">
                                <i class="ti ti-trash me-1"></i> <?= lang('Common.delete_btn') ?>
                            </button>
                        </div>

                        <input type="hidden" name="id[<?= $cert['id'] ?>]" value="<?= $cert['id'] ?>">
                        <input type="hidden" name="existing_cert_image[<?= $cert['id'] ?>]" value="<?= $cert['cert_image'] ?? '' ?>">

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small"><?= lang('Common.association_label') ?></label>
                                <select name="organ_id[<?= $cert['id'] ?>]" class="form-select">
                                    <?php foreach ($organ as $k => $d) {
                                        $selected = ($cert['organ_id'] == $d['id']) ? 'selected' : '';
                                    ?>
                                        <option <?= $selected ?> value="<?= $d['id'] ?>"><?= $d['name'] ?> (<?= $d['label'] ?>)</option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small"><?= lang('Common.level_label') ?></label>
                                <select name="cert_level_id[<?= $cert['id'] ?>]" class="form-select">
                                    <?php foreach ($cert_level as $k => $d) {
                                        $selected = ($cert['cert_level_id'] == $d['id']) ? 'selected' : '';
                                    ?>
                                        <option <?= $selected ?> value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                    <?php } ?>
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small"><?= lang('Common.license_code') ?></label>
                                <input type="text" name="license_code[<?= $cert['id'] ?>]" class="form-control" value="<?= $cert['license_code'] ?>">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold text-muted small"><?= lang('Common.completion_date') ?></label>
                                <input type="date" name="year[<?= $cert['id'] ?>]" class="form-control" value="<?= $cert['year'] ?>">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold text-muted small"><?= lang('Common.cert_image') ?></label>
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($cert['cert_image'])): ?>
                                        <img src="<?= base_url($cert['cert_image']) ?>" class="rounded border" style="width: 44px; height: 44px; object-fit: cover; cursor: pointer;" onclick="viewCertImage(this.src)" onerror="this.style.display='none'">
                                    <?php endif; ?>
                                    <div class="flex-grow-1">
                                        <label class="cert-upload-btn w-100">
                                            <input type="file" name="cert_image_file[<?= $cert['id'] ?>]" accept="image/jpeg,image/png,application/pdf" style="display:none;" onchange="previewCertFile(this)">
                                            <span><i class="ti ti-upload me-1"></i><?= lang('Common.upload') ?></span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

            <div class="new_content"></div>

            <div class="row justify-content-center mt-5">
                <div class="col-md-12 text-center">
                    <a href="<?= base_url('admin/coach/coach-manage/' . $id) ?>" class="btn btn-light border rounded-pill px-4 fw-bold me-2">
                        <i class="ti ti-arrow-left me-2"></i> <?= lang('Common.back') ?>
                    </a>
                    <button type="submit" class="btn btn-primary rounded-pill px-5 fw-bold btn-submit shadow-sm">
                        <i class="ti ti-device-floppy me-2"></i> <?= lang('Common.save') ?>
                    </button>
                </div>
            </div>
        </form>

        <!-- Modal -->
        <div class="modal fade" id="createModal" tabindex="-1" aria-labelledby="createModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                    <div class="modal-header" style="background: var(--navy-gradient); color: white; border-radius: 16px 16px 0 0;">
                        <h5 class="modal-title fw-bold text-white" id="createModalLabel">
                            <i class="ti ti-plus me-2"></i><?= lang('Common.create_new_item') ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <form id="formMock" name="formMock">
                            <div class="row g-3 mockup">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted"><?= lang('Common.association_label') ?></label>
                                    <input type="hidden" name="id[]" class="id" value="">
                                    <select name="organ_id[]" class="form-select organ_id">
                                        <?php foreach ($organ as $k => $d) { ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?> (<?= $d['label'] ?>)</option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold text-muted"><?= lang('Common.level_label') ?></label>
                                    <select name="cert_level_id[]" class="form-select cert_level_id">
                                        <?php foreach ($cert_level as $k => $d) { ?>
                                            <option value="<?= $d['id'] ?>"><?= $d['name'] ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-muted"><?= lang('Common.license_code') ?></label>
                                    <input type="text" class="form-control license_code" name="license_code[]" value="">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-muted"><?= lang('Common.completion_date') ?></label>
                                    <input type="date" class="form-control year" name="year[]" value="">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label fw-bold text-muted"><?= lang('Common.cert_image') ?></label>
                                    <label class="cert-upload-btn w-100">
                                        <input type="file" class="cert_image_input" accept="image/jpeg,image/png,application/pdf" style="display:none;" onchange="previewCertFile(this)">
                                        <span><i class="ti ti-upload me-1"></i><?= lang('Common.upload') ?></span>
                                    </label>
                                    <div class="mt-2 text-center" style="display:none;">
                                        <img id="certImagePreviewModal" src="" class="rounded border" style="max-height: 60px;">
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light" style="border-radius: 0 0 16px 16px;">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                        <button id="bt_save_add" type="button" data-bs-dismiss="modal" class="btn btn-primary rounded-pill px-4 fw-bold">
                            <i class="ti ti-check me-1"></i><?= lang('Common.create_new_item') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<style>
    .cert-upload-btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 6px 16px;
        border: 1px dashed #bdc3cb;
        border-radius: 8px;
        background: #f8fafc;
        cursor: pointer;
        transition: all 0.2s ease;
        text-align: center;
    }
    .cert-upload-btn:hover {
        border-color: var(--primary-color);
        background: #eef3f9;
        color: var(--primary-color);
    }
    .cert-upload-btn span {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--primary-color);
    }
    .cert-upload-btn.has-file {
        border-color: var(--success-color);
        background: rgba(40, 167, 69, 0.05);
    }
    .cert-upload-btn.has-file span {
        color: var(--success-color);
    }
</style>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    let newCertIndex = 0;

    function previewCertFile(input) {
        const label = $(input).closest('.cert-upload-btn, label');
        if (input.files && input.files[0]) {
            const file = input.files[0];
            label.addClass('has-file');
            label.find('span').html('<i class="ti ti-check me-1"></i>' + file.name.substring(0, 20) + (file.name.length > 20 ? '...' : ''));

            // Preview in modal if applicable
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    const previewContainer = $(input).closest('.col-md-4, .col-md-3').find('div[style*="display:none"], div:has(img)');
                    if (previewContainer.length) {
                        previewContainer.show().find('img').attr('src', e.target.result);
                    }
                };
                reader.readAsDataURL(file);
            }
        }
    }

    $("#bt_save_add").click(function() {
        const idx = 'new_' + newCertIndex++;
        const modalFileInput = $('#createModal .cert_image_input')[0];
        const files = modalFileInput ? modalFileInput.files : [];

        let certImageHtml = '';
        if (files.length > 0 && files[0].type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const base64 = e.target.result;
                const targetCard = $(".new_content .cert-card").last();
                targetCard.find('.cert-image-slot').html(
                    '<div class="d-flex align-items-center gap-2">' +
                    '<img src="' + base64 + '" class="rounded border" style="width:44px;height:44px;object-fit:cover;cursor:pointer;" onclick="viewCertImage(this.src)">' +
                    '<input type="hidden" name="cert_image_base64[' + idx + ']" value="' + base64 + '">' +
                    '<label class="cert-upload-btn has-file flex-grow-1"><input type="file" name="cert_image_file[' + idx + ']" accept="image/jpeg,image/png,application/pdf" style="display:none;" onchange="previewCertFile(this)"><span><i class="ti ti-check me-1"></i><?= lang("Common.upload") ?></span></label>' +
                    '</div>'
                );
            };
            reader.readAsDataURL(files[0]);
            certImageHtml = '<div class="cert-image-slot"><div class="d-flex align-items-center gap-2"><label class="cert-upload-btn flex-grow-1"><input type="file" name="cert_image_file[' + idx + ']" accept="image/jpeg,image/png,application/pdf" style="display:none;" onchange="previewCertFile(this)"><span><i class="ti ti-upload me-1"></i><?= lang("Common.upload") ?></span></label></div></div>';
        } else {
            certImageHtml = '<div class="cert-image-slot"><div class="d-flex align-items-center gap-2"><label class="cert-upload-btn flex-grow-1"><input type="file" name="cert_image_file[' + idx + ']" accept="image/jpeg,image/png,application/pdf" style="display:none;" onchange="previewCertFile(this)"><span><i class="ti ti-upload me-1"></i><?= lang("Common.upload") ?></span></label></div></div>';
        }

        const certNum = $('.cert-card').length + 1;
        const cardHtml = `
        <div class="cert-card mb-3">
            <div class="border rounded-3 p-3" style="background: #fafbfd;">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <span class="badge bg-success-subtle text-success fw-bold px-3 py-2" style="border-radius: 20px;">
                        <i class="ti ti-certificate me-1"></i> #${certNum} <small>(<?= lang('Common.new') ?>)</small>
                    </span>
                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="delCert(this)">
                        <i class="ti ti-trash me-1"></i> <?= lang('Common.delete_btn') ?>
                    </button>
                </div>
                <input type="hidden" name="id[${idx}]" value="">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small"><?= lang('Common.association_label') ?></label>
                        <select name="organ_id[${idx}]" class="form-select organ_id">
                            ${$('#createModal .organ_id').html()}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small"><?= lang('Common.level_label') ?></label>
                        <select name="cert_level_id[${idx}]" class="form-select cert_level_id">
                            ${$('#createModal .cert_level_id').html()}
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small"><?= lang('Common.license_code') ?></label>
                        <input type="text" name="license_code[${idx}]" class="form-control license_code" value="">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-bold text-muted small"><?= lang('Common.completion_date') ?></label>
                        <input type="date" name="year[${idx}]" class="form-control year" value="">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label fw-bold text-muted small"><?= lang('Common.cert_image') ?></label>
                        ${certImageHtml}
                    </div>
                </div>
            </div>
        </div>`;

        $(cardHtml).appendTo(".new_content");

        // Set values
        const lastCard = $(".new_content .cert-card").last();
        lastCard.find('.organ_id').val($('#createModal .organ_id').val());
        lastCard.find('.cert_level_id').val($('#createModal .cert_level_id').val());
        lastCard.find('.license_code').val($('#createModal .license_code').val());
        lastCard.find('.year').val($('#createModal .year').val());
    });

    function delCert(node) {
        $(node).closest('.cert-card').remove();
        // Re-number badges
        $('.cert-card').each(function(i) {
            $(this).find('.badge:first').html('<i class="ti ti-certificate me-1"></i> #' + (i + 1));
        });
    }

    function viewCertImage(src) {
        Swal.fire({
            title: '<?= lang("Common.cert_image") ?>',
            imageUrl: src,
            imageAlt: '<?= lang("Common.cert_image") ?>',
            showConfirmButton: true,
            confirmButtonText: '<?= lang("Common.close") ?>',
            width: 'auto',
            imageWidth: '100%',
            imageHeight: 'auto'
        });
    }
</script>
<?php $this->endSection(); ?>
