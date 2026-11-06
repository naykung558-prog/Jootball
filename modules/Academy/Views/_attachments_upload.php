<?php
/**
 * Shared partial: upload input section for session manage form
 * Required vars: none (pure UI)
 */
?>
<div class="mb-3">
    <label class="form-label fw-semibold">
        <i class="fas fa-paperclip me-1"></i><?= lang('Common.training_attach_upload_new') ?>
    </label>
    <div class="attach-dropzone border rounded-3 p-3 text-center text-muted"
         id="attachDropzone"
         style="border-style:dashed !important; cursor:pointer;">
        <i class="fas fa-cloud-upload-alt fs-2"></i>
        <div><?= lang('Common.training_attach_drag_drop') ?></div>
        <small><?= lang('Common.training_attach_hint') ?></small>
    </div>
    <input type="file"
           name="attachments[]"
           id="attachInput"
           class="d-none"
           multiple
           accept=".jpg,.jpeg,.png,.pdf,.doc,.docx,.xls,.xlsx">
    <ul class="list-group mt-2" id="attachPreview"></ul>
</div>
