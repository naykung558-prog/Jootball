<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-list-alt me-2"></i><?= esc($title) ?>
                </h4>
                <?php if (!empty($academy['name'])): ?>
                    <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($perms['perm_create_training_topic'])): ?>
            <div>
                <a href="<?= base_url('academy/training-topic/manage/' . esc($academy['id'])) ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus-circle me-2"></i><?= lang('Common.training_topic_create') ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="card-body p-4">
            <?php if (!empty($topics)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-primary text-white">
                            <tr>
                                <th class="text-center py-2 border-0" width="5%">#</th>
                                <th class="py-2 border-0"><?= lang('Common.training_topic_name') ?></th>
                                <th class="py-2 border-0 d-none d-md-table-cell"><?= lang('Common.training_topic_description') ?></th>
                                <?php if (!empty($perms['perm_create_training_topic']) || !empty($perms['perm_delete_topic'])): ?>
                                <th class="text-center py-2 border-0" width="15%"><?= lang('Common.manage') ?></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($topics as $index => $topic): ?>
                            <tr id="topic-row-<?= (int)$topic['id'] ?>">
                                <td class="text-center text-muted fw-bold"><?= $index + 1 ?></td>
                                <td class="fw-medium"><?= esc($topic['name']) ?></td>
                                <td class="text-muted small d-none d-md-table-cell">
                                    <?= esc(mb_strimwidth($topic['description'] ?? '', 0, 80, '...')) ?>
                                </td>
                                <?php if (!empty($perms['perm_create_training_topic']) || !empty($perms['perm_delete_topic'])): ?>
                                <td class="text-center">
                                    <div class="d-flex gap-1 justify-content-center">
                                        <?php if (!empty($perms['perm_create_training_topic'])): ?>
                                        <a href="<?= base_url('academy/training-topic/manage/' . esc($academy['id']) . '/' . esc($topic['id'])) ?>"
                                           class="btn btn-outline-primary btn-sm rounded-pill px-3">
                                            <i class="fas fa-edit me-1"></i><?= lang('Common.edit') ?>
                                        </a>
                                        <?php endif; ?>
                                        <?php if (!empty($perms['perm_delete_topic'])): ?>
                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                                onclick="deleteTopic(<?= (int)$topic['id'] ?>, '<?= esc($topic['name'], 'js') ?>')">
                                            <i class="fas fa-trash me-1"></i><?= lang('Common.delete') ?>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-list-alt fa-4x text-muted opacity-25"></i>
                    </div>
                    <h5 class="fw-bold text-muted mb-2"><?= lang('Common.training_no_topics') ?></h5>
                    <p class="text-muted small mb-4"><?= lang('Common.training_no_topics_desc') ?></p>
                    <?php if (!empty($perms['perm_create_training_topic'])): ?>
                        <a href="<?= base_url('academy/training-topic/manage/' . esc($academy['id'])) ?>"
                           class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-plus-circle me-2"></i><?= lang('Common.training_topic_create') ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langSuccess = '<?= lang('Common.success') ?>';
    const langError   = '<?= lang('Common.error') ?>';
    const langCancel  = '<?= lang('Common.cancel') ?>';
    const langConfirm = '<?= lang('Common.confirm') ?>';

    function deleteTopic(topicId, topicName) {
        Swal.fire({
            title: '<?= lang('Common.confirm_delete_data') ?>',
            text: topicName,
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
                        topic_id: topicId,
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
                            document.getElementById('topic-row-' + topicId).remove();
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
    }

    <?php if (session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: langSuccess,
        text: '<?= session()->getFlashdata('success') ?>',
        timer: 2000,
        showConfirmButton: false
    });
    <?php endif; ?>
</script>
<?php $this->endSection(); ?>
