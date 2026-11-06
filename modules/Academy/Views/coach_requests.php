<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>

<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="container py-4">
        <!-- Page Header -->
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h4 class="fw-bold text-primary mb-1">
                            <i class="fa fa-user-tie me-2"></i>
                            <?= lang('Common.coach_join_requests') ?>
                        </h4>
                        <p class="text-muted mb-0"><?= lang('Common.manage_coach_requests_desc') ?></p>
                    </div>
                    <div class="col-md-4 text-md-end">
                        <?php if (count($pendingRequests) > 0): ?>
                        <span class="badge bg-warning text-dark fs-6">
                            <?= count($pendingRequests) ?> <?= lang('Common.pending_requests') ?>
                        </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Requests -->
        <?php if (!empty($pendingRequests)): ?>
        <div class="card shadow-sm border-0 rounded-4 mb-4">
            <div class="card-header bg-warning text-dark py-3">
                <h5 class="m-0 d-flex align-items-center">
                    <i class="fa fa-clock me-2"></i>
                    <?= lang('Common.pending_requests') ?>
                    <span class="badge bg-dark ms-2"><?= count($pendingRequests) ?></span>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php foreach ($pendingRequests as $request): ?>
                <div class="request-item p-4 border-bottom" data-request-id="<?= $request['id'] ?>">
                    <div class="row align-items-center">
                        <div class="col-md-7">
                            <div class="d-flex align-items-start">
                                <div class="me-3">
                                    <?php
                                    $coachImage = $request['coach_image'] ?? $request['coach_line_picture'] ?? null;
                                    if ($coachImage): ?>
                                    <img src="<?= base_url($coachImage) ?>" class="rounded-circle" width="60" height="60" style="object-fit: cover;" alt="">
                                    <?php else: ?>
                                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                        <i class="fa fa-user fa-2x text-muted"></i>
                                    </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-1">
                                        <?= esc($request['coach_first_name'] ?? '') ?> <?= esc($request['coach_last_name'] ?? '') ?>
                                        <?php if ($request['coach_nick_name']): ?>
                                        <small class="text-muted">(<?= esc($request['coach_nick_name']) ?>)</small>
                                        <?php endif; ?>
                                    </h5>
                                    <div class="mb-2">
                                        <span class="badge bg-info">
                                            <?= lang('Common.role_' . $request['role_requested']) ?>
                                        </span>
                                    </div>
                                    <small class="text-muted d-block">
                                        <i class="fa fa-calendar me-1"></i>
                                        <?= lang('Common.requested_at') ?>: <?= date('d/m/Y H:i', strtotime($request['requested_at'])) ?>
                                    </small>
                                    <?php if ($request['message']): ?>
                                    <div class="bg-light rounded p-2 mt-2">
                                        <small class="text-muted">
                                            <i class="fa fa-comment me-1"></i>
                                            <?= lang('Common.message') ?>:
                                        </small>
                                        <p class="mb-0 mt-1">"<?= esc($request['message']) ?>"</p>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end mt-3 mt-md-0">
                            <div class="d-flex flex-wrap gap-2 justify-content-md-end">
                                <button type="button" class="btn btn-success approve-btn" data-id="<?= $request['id'] ?>">
                                    <i class="fa fa-check me-1"></i>
                                    <?= lang('Common.approve') ?>
                                </button>
                                <button type="button" class="btn btn-outline-danger reject-btn" data-id="<?= $request['id'] ?>">
                                    <i class="fa fa-times me-1"></i>
                                    <?= lang('Common.reject') ?>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <!-- All Requests History -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-header bg-primary text-white py-3">
                <h5 class="m-0 d-flex align-items-center text-white">
                    <i class="fa fa-history me-2"></i>
                    <?= lang('Common.request_history') ?>
                    <span class="badge bg-light text-dark ms-2"><?= count($allRequests) ?></span>
                </h5>
            </div>
            <div class="card-body p-0">
                <?php if (empty($allRequests)): ?>
                <div class="text-center py-5">
                    <i class="fa fa-inbox fa-4x text-muted mb-3"></i>
                    <h5 class="text-muted"><?= lang('Common.no_requests_yet') ?></h5>
                    <p class="text-muted"><?= lang('Common.no_coach_requests_description') ?></p>
                </div>
                <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="border-0 ps-4"><?= lang('Common.coach') ?></th>
                                <th class="border-0"><?= lang('Common.role') ?></th>
                                <th class="border-0"><?= lang('Common.requested_at') ?></th>
                                <th class="border-0"><?= lang('Common.status') ?></th>
                                <th class="border-0"><?= lang('Common.responded_at') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($allRequests as $request): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex align-items-center">
                                        <?php
                                        $coachImage = $request['coach_image'] ?? $request['coach_line_picture'] ?? null;
                                        if ($coachImage): ?>
                                        <img src="<?= base_url($coachImage) ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit: cover;" alt="">
                                        <?php else: ?>
                                        <div class="bg-light rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 40px; height: 40px;">
                                            <i class="fa fa-user text-muted"></i>
                                        </div>
                                        <?php endif; ?>
                                        <div>
                                            <span class="fw-bold"><?= esc($request['coach_first_name'] ?? '') ?> <?= esc($request['coach_last_name'] ?? '') ?></span>
                                            <?php if ($request['coach_nick_name']): ?>
                                            <br><small class="text-muted">(<?= esc($request['coach_nick_name']) ?>)</small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge bg-info">
                                        <?= lang('Common.role_' . $request['role_requested']) ?>
                                    </span>
                                </td>
                                <td>
                                    <small><?= date('d/m/Y H:i', strtotime($request['requested_at'])) ?></small>
                                </td>
                                <td>
                                    <?php if ($request['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark">
                                        <i class="fa fa-clock me-1"></i>
                                        <?= lang('Common.status_pending') ?>
                                    </span>
                                    <?php elseif ($request['status'] === 'approved'): ?>
                                    <span class="badge bg-success">
                                        <i class="fa fa-check me-1"></i>
                                        <?= lang('Common.status_approved') ?>
                                    </span>
                                    <?php else: ?>
                                    <span class="badge bg-danger">
                                        <i class="fa fa-times me-1"></i>
                                        <?= lang('Common.status_rejected') ?>
                                    </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($request['responded_at']): ?>
                                    <small><?= date('d/m/Y H:i', strtotime($request['responded_at'])) ?></small>
                                    <?php if ($request['responder_name']): ?>
                                    <br><small class="text-muted"><?= lang('Common.by') ?> <?= esc($request['responder_name']) ?></small>
                                    <?php endif; ?>
                                    <?php else: ?>
                                    <small class="text-muted">-</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Response Modal -->
<div class="modal fade" id="responseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="responseModalTitle"></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="responseRequestId">
                <input type="hidden" id="responseAction">
                <div class="mb-3">
                    <label for="responseMessage" class="form-label"><?= lang('Common.response_message_optional') ?></label>
                    <textarea class="form-control" id="responseMessage" rows="3" placeholder="<?= lang('Common.add_message_to_coach') ?>"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn" id="confirmResponseBtn"></button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?php $this->section('scripts'); ?>
<script>
$(document).ready(function() {
    const responseModal = new bootstrap.Modal(document.getElementById('responseModal'));

    // Approve button click - ส่งคำขออนุมัติโดยตรง
    $(document).on('click', '.approve-btn', function() {
        const requestId = $(this).data('id');

        Swal.fire({
            title: '<?= lang('Common.confirm_approve') ?>',
            text: '<?= lang('Common.confirm_approve_text') ?>',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang('Common.approve') ?>',
            cancelButtonText: '<?= lang('Common.cancel') ?>'
        }).then((result) => {
            if (result.isConfirmed) {
                processRequest(requestId, 'approve', '');
            }
        });
    });

    // Reject button click - ยังใช้ modal เพื่อให้ใส่เหตุผล
    $(document).on('click', '.reject-btn', function() {
        const requestId = $(this).data('id');
        $('#responseRequestId').val(requestId);
        $('#responseModalTitle').text('<?= lang('Common.confirm_reject') ?>');
        $('#confirmResponseBtn').removeClass('btn-success').addClass('btn-danger').text('<?= lang('Common.reject') ?>');
        $('#responseMessage').val('');
        responseModal.show();
    });

    // Confirm reject
    $(document).on('click', '#confirmResponseBtn', function() {
        const requestId = $('#responseRequestId').val();
        const message = $('#responseMessage').val();
        responseModal.hide();
        processRequest(requestId, 'reject', message);
    });

    // Process request function
    function processRequest(requestId, action, message) {
        Swal.fire({
            title: '<?= lang('Common.processing') ?>',
            html: '<?= lang('Common.please_wait_moment') ?>',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        const url = action === 'approve'
            ? '<?= base_url('academy/coach-request/approve') ?>/' + requestId
            : '<?= base_url('academy/coach-request/reject') ?>/' + requestId;

        $.ajax({
            url: url,
            method: 'POST',
            data: {
                message: message,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '<?= lang('Common.success_title') ?>',
                        text: response.message,
                        confirmButtonColor: '#004a23'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    let errorText = response.message;
                    if (response.debug) {
                        errorText += '\n\nDebug: ' + JSON.stringify(response.debug);
                    }
                    Swal.fire({
                        icon: 'error',
                        title: '<?= lang('Common.error_occurred') ?>',
                        text: errorText,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function(xhr, status, error) {
                let errorMessage = '<?= lang('Common.connection_error') ?>';
                if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                } else if (xhr.responseText) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.message) {
                            errorMessage = response.message;
                        }
                    } catch (e) {
                        errorMessage = 'Error: ' + status + ' - ' + error;
                    }
                }
                Swal.fire({
                    icon: 'error',
                    title: '<?= lang('Common.error_occurred') ?>',
                    text: errorMessage,
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
