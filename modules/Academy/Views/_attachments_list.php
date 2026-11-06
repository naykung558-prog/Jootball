<?php
/**
 * Shared partial: render attachment list/gallery
 * Required vars:
 *   $attachments — array of rows (id, attach_name, attach_file, attach_file_type, attach_size)
 *   $mode        — 'full' (gallery+list, detail page) | 'compact' (list only)
 *   $canManage   — bool (show delete button?)
 */
$attachments = $attachments ?? [];
$mode        = $mode ?? 'full';
$perms       = $perms ?? [];
// Caller may pass $canManage explicitly, otherwise derive from $perms['perm_create_training_session']
$canManage   = $canManage ?? !empty($perms['perm_create_training_session']);

$images = array_filter($attachments, function ($a) {
    return in_array($a['attach_file_type'], ['image/jpeg', 'image/png', 'image/jpg']);
});
$docs = array_filter($attachments, function ($a) {
    return !in_array($a['attach_file_type'], ['image/jpeg', 'image/png', 'image/jpg']);
});

if (!function_exists('attach_icon_class')) {
    function attach_icon_class($mime) {
        if (strpos($mime, 'pdf') !== false) return 'fas fa-file-pdf text-danger';
        if (strpos($mime, 'word') !== false || strpos($mime, 'msword') !== false) return 'fas fa-file-word text-primary';
        if (strpos($mime, 'excel') !== false || strpos($mime, 'spreadsheet') !== false) return 'fas fa-file-excel text-success';
        if (strpos($mime, 'image') !== false) return 'fas fa-file-image text-info';
        return 'fas fa-file text-secondary';
    }
}
?>

<?php if (empty($attachments)): ?>
    <div class="text-muted small"><i class="fas fa-paperclip me-1"></i><?= lang('Common.training_attach_no_files') ?></div>
<?php else: ?>
    <?php if ($mode === 'full' && !empty($images)): ?>
    <div class="row g-2 mb-3" id="attach-gallery">
        <?php foreach ($images as $img): ?>
        <div class="col-6 col-md-3 attach-row" data-id="<?= esc($img['id']) ?>">
            <a href="<?= base_url('academy/training-session/attachment/download/' . $img['id']) ?>"
               data-src="<?= base_url('academy/training-session/attachment/download/' . $img['id']) ?>"
               class="d-block position-relative attach-img-link">
                <img src="<?= base_url('academy/training-session/attachment/download/' . $img['id']) ?>"
                     class="img-fluid rounded shadow-sm"
                     style="width:100%; height:120px; object-fit:cover;"
                     alt="<?= esc($img['attach_name']) ?>">
                <?php if ($canManage): ?>
                <button type="button"
                        class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 btn-delete-attach"
                        data-id="<?= esc($img['id']) ?>"
                        title="<?= lang('Common.delete') ?>">
                    <i class="fas fa-trash"></i>
                </button>
                <?php endif; ?>
            </a>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>

    <?php $listItems = ($mode === 'compact') ? $attachments : $docs; ?>
    <?php if (!empty($listItems)): ?>
    <ul class="list-group list-group-flush">
        <?php foreach ($listItems as $a): ?>
        <li class="list-group-item d-flex align-items-center justify-content-between attach-row" data-id="<?= esc($a['id']) ?>">
            <div class="d-flex align-items-center flex-grow-1">
                <i class="<?= attach_icon_class($a['attach_file_type']) ?> fs-4 me-2"></i>
                <div class="flex-grow-1">
                    <div class="fw-semibold small"><?= esc($a['attach_name']) ?></div>
                    <small class="text-muted"><?= esc($a['attach_size']) ?> MB</small>
                </div>
            </div>
            <div class="d-flex gap-1">
                <a href="<?= base_url('academy/training-session/attachment/download/' . $a['id']) ?>"
                   target="_blank"
                   class="btn btn-sm btn-outline-primary" title="<?= lang('Common.training_attach_preview') ?>">
                    <i class="fas fa-eye"></i>
                </a>
                <?php if ($canManage): ?>
                <button type="button"
                        class="btn btn-sm btn-outline-danger btn-delete-attach"
                        data-id="<?= esc($a['id']) ?>" title="<?= lang('Common.delete') ?>">
                    <i class="fas fa-trash"></i>
                </button>
                <?php endif; ?>
            </div>
        </li>
        <?php endforeach; ?>
    </ul>
    <?php endif; ?>
<?php endif; ?>
