<?php $this->extend('template/coach') ?>
<?= $this->section('title') ?>
<?= lang('Common.my_join_requests') ?> - GLO Football
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
.my-requests-page {
    background-color: var(--coach-gray-100);
    min-height: calc(100vh - 200px);
    padding: 2rem 0;
}

.coach-page-header {
    background-color: var(--coach-light);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    padding: 1rem;
    margin-bottom: 2rem;
    box-shadow: var(--coach-shadow);
}

.coach-page-title {
    color: var(--coach-primary);
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.coach-page-subtitle {
    color: var(--coach-gray-600);
    margin-bottom: 0;
}

.requests-card {
    background-color: var(--coach-light);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    box-shadow: var(--coach-shadow);
    overflow: hidden;
    margin-bottom: 2rem;
}

.requests-card-header {
    background-color: var(--coach-primary);
    color: var(--coach-light);
    padding: 1.5rem 2rem;
    font-weight: 600;
}

.requests-card-body {
    padding: 0;
}

.request-item {
    padding: 1.5rem 2rem;
    border-bottom: 1px solid var(--coach-gray-200);
    transition: var(--coach-transition);
}

.request-item:last-child {
    border-bottom: none;
}

.request-item:hover {
    background-color: var(--coach-gray-50);
}

.team-logo {
    width: 60px;
    height: 60px;
    border-radius: var(--coach-border-radius);
    object-fit: cover;
    border: 2px solid var(--coach-secondary);
}

.team-logo-placeholder {
    width: 60px;
    height: 60px;
    background-color: var(--coach-gray-100);
    border: 2px solid var(--coach-secondary);
    border-radius: var(--coach-border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--coach-primary);
    font-size: 1.5rem;
}

.status-badge {
    padding: 0.375rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.8rem;
    font-weight: 600;
}

.status-pending {
    background-color: var(--coach-secondary);
    color: var(--coach-primary);
}

.status-approved {
    background-color: var(--coach-success);
    color: var(--coach-light);
}

.status-rejected {
    background-color: var(--coach-danger);
    color: var(--coach-light);
}

.role-badge {
    background-color: var(--coach-info);
    color: var(--coach-light);
    padding: 0.25rem 0.5rem;
    border-radius: 0.25rem;
    font-size: 0.75rem;
}

.coach-btn-primary {
    background-color: var(--coach-primary);
    border-color: var(--coach-primary);
    color: var(--coach-light);
    padding: 0.75rem 1.5rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    border: none;
}

.coach-btn-primary:hover {
    background-color: var(--coach-dark);
    border-color: var(--coach-dark);
    color: var(--coach-light);
}

.coach-btn-outline {
    background-color: transparent;
    border: 2px solid var(--coach-primary);
    color: var(--coach-primary);
    padding: 0.5rem 1rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    text-decoration: none;
}

.coach-btn-outline:hover {
    background-color: var(--coach-primary);
    color: var(--coach-light);
}

.coach-btn-danger {
    background-color: transparent;
    border: 2px solid var(--coach-danger);
    color: var(--coach-danger);
    padding: 0.5rem 1rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
}

.coach-btn-danger:hover {
    background-color: var(--coach-danger);
    color: var(--coach-light);
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--coach-gray-500);
}

.empty-state i {
    font-size: 4rem;
    color: var(--coach-secondary);
    margin-bottom: 1rem;
    opacity: 0.7;
}

.response-message {
    background-color: var(--coach-gray-100);
    border-radius: var(--coach-border-radius);
    padding: 0.75rem 1rem;
    margin-top: 0.75rem;
    font-size: 0.9rem;
}

@media (max-width: 768px) {
    .my-requests-page {
        padding: 1rem 0;
    }
    .request-item {
        padding: 1rem;
    }
    .team-logo, .team-logo-placeholder {
        width: 50px;
        height: 50px;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="my-requests-page">
    <div class="container">
        <!-- Page Header -->
        <div class="coach-page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="coach-page-title h3">
                        <i class="fa fa-list me-3"></i>
                        <?= lang('Common.my_join_requests') ?>
                    </h1>
                    <p class="coach-page-subtitle"><?= lang('Common.view_your_requests') ?></p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="<?= base_url('coach/find-team') ?>" class="coach-btn-primary">
                        <i class="fa fa-search me-2"></i>
                        <?= lang('Common.find_team') ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Requests List -->
        <div class="requests-card">
            <div class="requests-card-header">
                <h5 class="m-0 d-flex align-items-center text-white">
                    <i class="fa fa-paper-plane me-3"></i>
                    <?= lang('Common.request_history') ?>
                    <span class="badge bg-warning text-dark ms-3"><?= count($requests) ?></span>
                </h5>
            </div>
            <div class="requests-card-body">
                <?php if (empty($requests)): ?>
                <div class="empty-state">
                    <i class="fa fa-inbox"></i>
                    <h5><?= lang('Common.no_requests_yet') ?></h5>
                    <p class="text-muted mb-4"><?= lang('Common.no_requests_description') ?></p>
                    <a href="<?= base_url('coach/find-team') ?>" class="coach-btn-primary">
                        <i class="fa fa-search me-2"></i>
                        <?= lang('Common.find_team_now') ?>
                    </a>
                </div>
                <?php else: ?>
                <?php foreach ($requests as $request): ?>
                <div class="request-item" data-request-id="<?= $request['id'] ?>">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex align-items-center">
                                <div class="me-3">
                                    <?php if ($request['team_logo']): ?>
                                    <img src="<?= base_url($request['team_logo']) ?>" class="team-logo" alt="<?= $request['team_name'] ?>">
                                    <?php else: ?>
                                    <div class="team-logo-placeholder"><i class="fa fa-shield"></i></div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h6 class="mb-1 fw-bold"><?= esc($request['team_name']) ?></h6>
                                    <div class="mb-1">
                                        <span class="role-badge">
                                            <?= lang('Common.role_' . $request['role_requested']) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted">
                                        <i class="fa fa-clock me-1"></i>
                                        <?= lang('Common.requested_at') ?>: <?= date('d/m/Y H:i', strtotime($request['requested_at'])) ?>
                                    </small>
                                    <?php if ($request['message']): ?>
                                    <p class="mb-0 mt-2 text-muted small">
                                        <i class="fa fa-comment me-1"></i>
                                        "<?= esc($request['message']) ?>"
                                    </p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <?php if ($request['status'] === 'pending'): ?>
                            <span class="status-badge status-pending mb-2 d-inline-block">
                                <i class="fa fa-clock me-1"></i>
                                <?= lang('Common.status_pending') ?>
                            </span>
                            <br>
                            <button type="button" class="btn coach-btn-danger btn-sm cancel-request-btn" data-id="<?= $request['id'] ?>">
                                <i class="fa fa-times me-1"></i>
                                <?= lang('Common.cancel_request') ?>
                            </button>
                            <?php elseif ($request['status'] === 'approved'): ?>
                            <span class="status-badge status-approved">
                                <i class="fa fa-check me-1"></i>
                                <?= lang('Common.status_approved') ?>
                            </span>
                            <?php if ($request['responded_at']): ?>
                            <br>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($request['responded_at'])) ?>
                            </small>
                            <?php endif; ?>
                            <?php if ($request['response_message']): ?>
                            <div class="response-message text-start mt-2">
                                <i class="fa fa-reply me-1"></i>
                                <?= esc($request['response_message']) ?>
                            </div>
                            <?php endif; ?>
                            <?php else: ?>
                            <span class="status-badge status-rejected">
                                <i class="fa fa-times me-1"></i>
                                <?= lang('Common.status_rejected') ?>
                            </span>
                            <?php if ($request['responded_at']): ?>
                            <br>
                            <small class="text-muted">
                                <?= date('d/m/Y H:i', strtotime($request['responded_at'])) ?>
                            </small>
                            <?php endif; ?>
                            <?php if ($request['response_message']): ?>
                            <div class="response-message text-start mt-2">
                                <i class="fa fa-reply me-1"></i>
                                <?= esc($request['response_message']) ?>
                            </div>
                            <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$('.cancel-request-btn').on('click', function() {
    const requestId = $(this).data('id');
    const requestItem = $(this).closest('.request-item');

    Swal.fire({
        title: '<?= lang('Common.confirm_cancel_request') ?>',
        text: '<?= lang('Common.confirm_cancel_request_text') ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<?= lang('Common.yes_cancel') ?>',
        cancelButtonText: '<?= lang('Common.no_keep') ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            cancelRequest(requestId, requestItem);
        }
    });
});

function cancelRequest(requestId, requestItem) {
    Swal.fire({
        title: '<?= lang('Common.processing') ?>',
        html: '<?= lang('Common.please_wait_moment') ?>',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '<?= base_url('coach/cancel-request') ?>',
        method: 'POST',
        data: { request_id: requestId },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '<?= lang('Common.success_title') ?>',
                    text: response.message,
                    confirmButtonColor: '#004a23'
                }).then(() => {
                    requestItem.fadeOut(300, function() {
                        $(this).remove();
                        // Check if no more requests
                        if ($('.request-item').length === 0) {
                            location.reload();
                        }
                    });
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '<?= lang('Common.error_occurred') ?>',
                    text: response.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: '<?= lang('Common.error_occurred') ?>',
                text: '<?= lang('Common.connection_error') ?>',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
