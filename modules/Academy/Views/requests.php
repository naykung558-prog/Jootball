<?php $this->extend('template/layout') ?>

<?= $this->section('content') ?>
<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card shadow-sm border-0">
                    <div class="card-body">
                        <?php echo view("Modules\Academy\Views\subnav.php"); ?>

                        <div class="mt-4">
                            <div class="d-flex justify-content-between align-items-center mb-4">
                                <h4 class="header-title text-dark"><i class="fas fa-users-cog me-2"></i> <?= lang('Common.requests_and_managers') ?></h4>
                            </div>

                            <?php if (session()->getFlashdata('success')) : ?>
                                <div class="alert alert-success alert-dismissible fade show" role="alert">
                                    <i class="mdi mdi-check-all me-2"></i> <?= session()->getFlashdata('success') ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>
                            <?php if (session()->getFlashdata('error')) : ?>
                                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                    <i class="mdi mdi-block-helper me-2"></i> <?= session()->getFlashdata('error') ?>
                                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                                </div>
                            <?php endif; ?>

                            <?php if(empty($requests) && empty($members)): ?>
                                <div class="text-center py-5">
                                    <i class="fas fa-user-friends fa-3x text-muted mb-3"></i>
                                    <p class="text-muted"><?= lang('Common.no_requests_or_managers') ?></p>
                                </div>
                            <?php endif; ?>

                            <?php if(!empty($requests)): ?>
                            <div class="card border-primary border mb-4">
                                <div class="card-header bg-soft-primary">
                                    <h5 class="card-title text-primary mb-0"><i class="fas fa-user-clock me-2"></i> <?= lang('Common.pending_requests_title') ?></h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-centered table-nowrap mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3 border-0"><?= lang('Common.user') ?></th>
                                                    <th class="border-0"><?= lang('Common.request_date') ?></th>
                                                    <th class="border-0 text-end pe-3"><?= lang('Common.manage') ?></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($requests as $req): ?>
                                                <?php
                                                    $reqName = $req['user_name'] ?? (($req['coach_first_name'] ?? '') . ' ' . ($req['coach_last_name'] ?? ''));
                                                    $reqUsername = $req['username'] ?? $req['coach_username'] ?? '';
                                                    $reqImage = $req['profile_image'] ?? $req['coach_image'] ?? '';
                                                ?>
                                                <tr>
                                                    <td class="ps-3">
                                                        <div class="d-flex align-items-center">
                                                            <?php if(!empty($reqImage)): ?>
                                                                <img src="<?= base_url($reqImage) ?>" class="rounded-circle avatar-sm me-3 shadow-sm" alt="user-image" style="width: 40px; height: 40px; object-fit:cover;">
                                                            <?php else: ?>
                                                                <div class="avatar-sm me-3">
                                                                    <span class="avatar-title bg-primary rounded-circle text-white shadow-sm font-16">
                                                                        <?= strtoupper(substr(trim($reqName) ?: 'U', 0, 1)) ?>
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <h5 class="m-0 font-15 fw-bold text-dark"><?= esc($reqName) ?></h5>
                                                                <?php if(!empty($reqUsername)): ?>
                                                                <p class="mb-0 text-muted font-13"><i class="far fa-id-card me-1"></i> <?= esc($reqUsername) ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><i class="far fa-calendar-alt me-1 text-muted"></i> <?= date('d/m/Y H:i', strtotime($req['created_at'])) ?></td>
                                                    <td class="text-end pe-3">
                                                        <a href="<?= base_url('academy/request/approve/' . $req['id']) ?>" class="btn btn-success btn-sm rounded-pill px-3 btn-approve">
                                                            <i class="fas fa-check me-1"></i> <?= lang('Common.approve_btn') ?>
                                                        </a>
                                                        <a href="<?= base_url('academy/request/reject/' . $req['id']) ?>" class="btn btn-light btn-sm rounded-pill px-3 text-danger btn-reject">
                                                            <i class="fas fa-times me-1"></i> <?= lang('Common.reject_btn') ?>
                                                        </a>
                                                    </td>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>

                            <?php if(!empty($members)): ?>
                            <?php
                                $isOwner = false;
                                foreach ($members as $m) {
                                    if ($m['role'] === 'owner' && $m['user_id'] == session()->get('user_id')) {
                                        $isOwner = true;
                                        break;
                                    }
                                }
                                $roleMap = ['owner' => 'bg-primary', 'admin' => 'bg-info', 'coach' => 'bg-success', 'viewer' => 'bg-secondary'];
                            ?>
                            <div class="card border mb-0">
                                <div class="card-header bg-light">
                                    <h5 class="card-title text-success mb-0"><i class="fas fa-user-check me-2"></i> <?= lang('Common.current_managers') ?></h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive">
                                        <table class="table table-hover table-centered table-nowrap mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th class="ps-3 border-0"><?= lang('Common.user') ?></th>
                                                    <th class="border-0"><?= lang('Common.role') ?></th>
                                                    <th class="border-0"><?= lang('Common.join_date') ?></th>
                                                    <?php if($isOwner): ?>
                                                        <th class="border-0 text-end pe-3"><?= lang('Common.manage') ?></th>
                                                    <?php endif; ?>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach($members as $member): ?>
                                                <?php
                                                    $memberName = $member['user_name'] ?? ($member['coach_first_name'] . ' ' . ($member['coach_last_name'] ?? ''));
                                                    $memberUsername = $member['username'] ?? $member['coach_username'] ?? '';
                                                    $memberImage = $member['profile_image'] ?? $member['coach_image'] ?? '';
                                                    $roleBadge = $roleMap[$member['role'] ?? ''] ?? 'bg-secondary';
                                                ?>
                                                <tr>
                                                    <td class="ps-3">
                                                        <div class="d-flex align-items-center">
                                                            <?php if(!empty($memberImage)): ?>
                                                                <img src="<?= base_url($memberImage) ?>" class="rounded-circle avatar-sm me-3 shadow-sm" alt="user-image" style="width: 40px; height: 40px; object-fit:cover;">
                                                            <?php else: ?>
                                                                <div class="avatar-sm me-3">
                                                                    <span class="avatar-title bg-success rounded-circle text-white shadow-sm font-16">
                                                                        <?= strtoupper(substr($memberName ?? 'M', 0, 1)) ?>
                                                                    </span>
                                                                </div>
                                                            <?php endif; ?>
                                                            <div>
                                                                <h5 class="m-0 font-15 fw-bold text-dark"><?= esc($memberName) ?></h5>
                                                                <?php if(!empty($memberUsername)): ?>
                                                                <p class="mb-0 text-muted font-13"><i class="far fa-user-circle me-1"></i> <?= esc($memberUsername) ?></p>
                                                                <?php endif; ?>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td><span class="badge <?= $roleBadge ?> font-12 rounded-pill px-2"><?= ucfirst($member['role'] ?? 'viewer') ?></span></td>
                                                    <td><i class="far fa-calendar-check me-1 text-muted"></i> <?= isset($member['created_at']) ? date('d/m/Y', strtotime($member['created_at'])) : '-' ?></td>
                                                    <?php if($isOwner && ($member['role'] ?? '') !== 'owner'): ?>
                                                        <td class="text-end pe-3">
                                                            <button class="btn btn-outline-danger btn-sm rounded-pill px-3 btn-remove-manager" data-id="<?= $member['id'] ?>" data-name="<?= esc($memberName) ?>">
                                                                <i class="fas fa-trash-alt me-1"></i> <?= lang('Common.delete') ?>
                                                            </button>
                                                        </td>
                                                    <?php elseif($isOwner && ($member['role'] ?? '') === 'owner'): ?>
                                                        <td class="text-end pe-3">
                                                            <span class="text-muted">-</span>
                                                        </td>
                                                    <?php endif; ?>
                                                </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
const langConfirmApprove = '<?= lang('Common.confirm_approve_title') ?>';
const langConfirmApproveUser = '<?= lang('Common.confirm_approve_user') ?>';
const langYesApprove = '<?= lang('Common.yes_approve') ?>';
const langCancel = '<?= lang('Common.cancel') ?>';
const langConfirmReject = '<?= lang('Common.confirm_reject_title') ?>';
const langConfirmRejectUser = '<?= lang('Common.confirm_reject_user') ?>';
const langYesReject = '<?= lang('Common.yes_reject') ?>';
const langConfirmDelete = '<?= lang('Common.confirm_delete_title') ?>';
const langYesRemove = '<?= lang('Common.yes_remove') ?>';
const langSuccess = '<?= lang('Common.success') ?>';
const langError = '<?= lang('Common.error_title') ?>';
const langConnectionError = '<?= lang('Common.connection_error_msg') ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Approve Action
    document.querySelectorAll('.btn-approve').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');

            Swal.fire({
                title: langConfirmApprove,
                text: langConfirmApproveUser,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#198754',
                cancelButtonColor: '#6c757d',
                confirmButtonText: langYesApprove,
                cancelButtonText: langCancel
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    // Reject Action
    document.querySelectorAll('.btn-reject').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('href');

            Swal.fire({
                title: langConfirmReject,
                text: langConfirmRejectUser,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: langYesReject,
                cancelButtonText: langCancel
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = url;
                }
            });
        });
    });

    // Remove Manager Action
    document.querySelectorAll('.btn-remove-manager').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const url = '<?= base_url('academy/member-remove') ?>/' + id;

            Swal.fire({
                title: langConfirmDelete,
                text: '<?= lang('Common.confirm_remove_manager') ?>'.replace('%s', name),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: langYesRemove,
                cancelButtonText: langCancel
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Content-Type': 'application/json',
                            '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                        }
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status) {
                            Swal.fire({
                                icon: 'success',
                                title: langSuccess,
                                text: data.message,
                                confirmButtonColor: '#198754'
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: langError,
                                text: data.message,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        Swal.fire({
                            icon: 'error',
                            title: langError,
                            text: langConnectionError,
                            confirmButtonColor: '#dc3545'
                        });
                    });
                }
            });
        });
    });
});
</script>
<?= $this->endSection() ?>
