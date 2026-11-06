<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="card shadow-lg mt-3">
        <div class="card-header bg-primary text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1 text-white">
                        <i class="fas fa-list-alt me-2"></i>
                        <?= !empty($topic['id']) ? lang('Common.training_topic_edit') : lang('Common.training_topic_create') ?>
                    </h5>
                    <small class="opacity-75"><?= esc($academy['name']) ?></small>
                </div>
                <a href="<?= base_url('academy/training-topics/' . esc($academy['id'])) ?>" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> <?= lang('Common.back') ?>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="<?= base_url('academy/training-topic/save') ?>" method="POST" id="topic-form" autocomplete="off">
                <input type="hidden" name="academy_id" value="<?= esc($academy['id']) ?>">
                <?php if (!empty($topic['id'])): ?>
                    <input type="hidden" name="id" value="<?= esc($topic['id']) ?>">
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-md-10">
                        <label class="pt-3"><?= lang('Common.training_topic_name') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="<?= esc($topic['name'] ?? '') ?>"
                               class="form-control my-2" required>
                    </div>
                    <div class="col-md-10">
                        <label class="pt-3"><?= lang('Common.training_topic_description') ?></label>
                        <textarea name="description" class="form-control my-2" rows="3"><?= esc($topic['description'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-10 text-center mt-4">
                        <button type="button" class="btn btn-primary rounded-pill px-5" id="pre-submit-btn">
                            <i class="fas fa-save me-2"></i><?= lang('Common.submit_data') ?>
                        </button>
                        <?php if (!empty($topic['id'])): ?>
                        <button type="button" class="btn btn-outline-danger rounded-pill px-5 ms-2" id="btn-delete-topic">
                            <i class="fas fa-trash me-2"></i><?= lang('Common.delete') ?>
                        </button>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langSuccess       = '<?= lang('Common.success') ?>';
    const langError         = '<?= lang('Common.error') ?>';
    const langCancel        = '<?= lang('Common.cancel') ?>';
    const langConfirm       = '<?= lang('Common.confirm') ?>';
    const langConfirmSave   = '<?= lang('Common.confirm_save_data') ?>';
    const langConfirmSaveText = '<?= lang('Common.confirm_save_text') ?>';

    $('#pre-submit-btn').click(function() {
        Swal.fire({
            title: langConfirmSave,
            text: langConfirmSaveText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16478d',
            cancelButtonColor: '#d33',
            confirmButtonText: langConfirm,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $('#topic-form').submit();
            }
        });
    });

    <?php if (!empty($topic['id'])): ?>
    document.getElementById('btn-delete-topic').addEventListener('click', function() {
        Swal.fire({
            title: '<?= lang('Common.confirm_delete_data') ?>',
            text: '<?= esc($topic['name'] ?? '', 'js') ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang('Common.confirm_delete_btn') ?>',
            cancelButtonText: langCancel,
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('academy/training-topic/delete') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({
                        academy_id: <?= (int)$academy['id'] ?>,
                        topic_id: <?= (int)$topic['id'] ?>,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    })
                })
                .then(r => r.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: langSuccess,
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '<?= base_url('academy/training-topics/' . esc($academy['id'])) ?>';
                        });
                    } else {
                        Swal.fire({ icon: 'error', title: langError, text: data.message });
                    }
                })
                .catch(() => {
                    Swal.fire({ icon: 'error', title: langError, confirmButtonColor: '#dc3545' });
                });
            }
        });
    });
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: langSuccess,
        text: '<?= session()->getFlashdata('success') ?>',
        timer: 2000,
        showConfirmButton: false
    });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: langError,
        text: '<?= session()->getFlashdata('error') ?>',
        confirmButtonColor: '#dc3545'
    });
    <?php endif; ?>
</script>
<?php $this->endSection(); ?>
