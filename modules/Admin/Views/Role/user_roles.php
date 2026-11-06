<?= $this->extend('\App\Views\template\admin_layout') ?>

<?= $this->section('content') ?>
<style>
    .avatar-initial {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        font-weight: 600;
        margin-right: 15px;
        font-size: 16px;
    }
    .badge-soft {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 500;
        font-size: 0.85rem;
    }
    .badge-soft-primary {
        background-color: rgba(93, 135, 255, 0.1);
        color: #5D87FF;
    }
    .btn-action {
        width: 32px;
        height: 32px;
        padding: 0;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 8px;
        transition: all 0.2s;
    }
    .btn-action:hover {
        transform: translateY(-2px);
    }
    .table thead th {
        font-weight: 600;
        color: #5a6a85;
        background-color: #f6f9fc;
        text-transform: uppercase;
        font-size: 0.75rem;
        letter-spacing: 0.5px;
        padding: 16px 24px;
        border-bottom: none;
    }
    .table tbody td {
        padding: 16px 24px;
        border-bottom: 1px solid #eaeef4;
    }
    .table tbody tr:last-child td {
        border-bottom: none;
    }

</style>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="card-body p-0">
                <div class="p-4 d-flex justify-content-between align-items-center bg-white border-bottom">
                    <div>
                        <h5 class="card-title fw-bold mb-1"><?= $title ?></h5>
                        <p class="text-muted small mb-0"><?= lang('Common.manage_users_and_access') ?></p>
                    </div>
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm" data-bs-toggle="modal" data-bs-target="#userModal" onclick="resetUserForm()">
                        <i class="ti ti-plus me-2 fs-4"></i> <?= lang('Common.add_user') ?>
                    </button>
                </div>



                <div class="px-4 pt-3">
                    <?php if (session()->getFlashdata('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show rounded-3 shadow-sm border-0 bg-success bg-opacity-10 text-success" role="alert">
                            <i class="ti ti-check-circle me-2"></i> <?= session()->getFlashdata('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->getFlashdata('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show rounded-3 shadow-sm border-0 bg-danger bg-opacity-10 text-danger" role="alert">
                            <i class="ti ti-alert-circle me-2"></i> <?= session()->getFlashdata('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="table-responsive">
                    <table class="table align-middle mb-0" id="userRolesTable">
                        <thead>
                            <tr>
                                <th><?= lang('Common.user') ?></th>
                                <th><?= lang('Common.role') ?></th>
                                <th><?= lang('Common.assigned_date') ?></th>
                                <th class="text-end"><?= lang('Common.manage') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <?php
                                        // Generate random color for avatar based on char code
                                        $colors = ['#5D87FF', '#13DEB9', '#FA896B', '#FFAE1F', '#49BEFF'];
                                        $firstChar = mb_substr($user['username'], 0, 1, 'UTF-8');
                                        $colorIndex = ord(strtoupper($firstChar)) % count($colors);
                                        $avatarColor = $colors[$colorIndex];
                                    ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-initial shadow-sm" style="background-color: <?= $avatarColor ?>; color: #fff;">
                                                    <?= strtoupper($firstChar) ?>
                                                </div>
                                                <div>
                                                    <h6 class="fw-bold mb-0 text-dark"><?= esc($user['username']) ?></h6>
                                                    <div class="d-flex align-items-center mt-1">
                                                        <span class="badge border border-light text-muted fw-normal px-2 py-1 rounded-1" style="font-size: 10px; background-color: #f8f9fa;">
                                                            ID: <?= $user['id'] ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <?php if (!empty($user['roles'])): ?>
                                                <?php
                                                $roles = explode(',', $user['roles']);
                                                foreach ($roles as $role): ?>
                                                    <span class="badge badge-soft badge-soft-primary me-1 mb-1"><?= esc(trim($role)) ?></span>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <span class="text-muted fst-italic small"><?= lang('Common.no_role') ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($user['assigned_at']): ?>
                                                <div class="d-flex align-items-center text-muted">
                                                    <i class="ti ti-calendar me-2 fs-4"></i>
                                                    <?= date('d/m/Y', strtotime($user['assigned_at'])) ?>
                                                </div>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-end">
                                            <div class="d-inline-flex gap-2">
                                                <?php if (hasPermission('user.edit')): ?>
                                                    <a href="<?= base_url('admin/role/assign-user-roles/' . $user['id']) ?>"
                                                       class="btn btn-action btn-light text-primary" data-bs-toggle="tooltip" title="<?= lang('Common.manage_permissions') ?>">
                                                        <i class="ti ti-settings fs-5"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-action btn-light text-warning" onclick="editUser(<?= $user['id'] ?>)" data-bs-toggle="tooltip" title="<?= lang('Common.edit_data') ?>">
                                                        <i class="ti ti-edit fs-5"></i>
                                                    </button>
                                                    <button type="button" class="btn btn-action btn-light text-danger" onclick="deleteUser(<?= $user['id'] ?>)" data-bs-toggle="tooltip" title="<?= lang('Common.delete_user') ?>">
                                                        <i class="ti ti-trash fs-5"></i>
                                                    </button>
                                                <?php endif; ?>

                                                <?php if (!empty($user['roles']) && strpos($user['roles'], 'Admin') !== false): ?>
                                                    <div class="dropdown">
                                                        <button class="btn btn-action btn-light text-success" type="button" data-bs-toggle="dropdown" title="<?= lang('Common.more_menu') ?>">
                                                            <i class="ti ti-settings fs-5"></i>
                                                        </button>
                                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 rounded-3">
                                                            <li><a class="dropdown-item d-flex align-items-center py-2" href="<?= base_url('admin/report-guest/report-players') ?>" target="_blank">
                                                                <i class="ti ti-chart-line me-2 text-success"></i> <?= lang('Common.player_report') ?>
                                                            </a></li>
                                                            <li><hr class="dropdown-divider"></li>
                                                            <li><small class="dropdown-header text-uppercase"><?= lang('Common.player_details') ?></small></li>
                                                            <li><a class="dropdown-item d-flex align-items-center py-2" href="#" onclick="promptPlayerId()">
                                                                <i class="ti ti-search me-2 text-primary"></i> <?= lang('Common.search_by_id') ?>
                                                            </a></li>
                                                        </ul>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <div class="empty-state">
                                            <div class="mb-3">
                                                <i class="ti ti-users text-muted opacity-25" style="font-size: 4rem;"></i>
                                            </div>
                                            <h6 class="text-muted fw-bold"><?= lang('Common.no_users_found') ?></h6>
                                            <p class="text-muted small"><?= lang('Common.start_by_adding_first_user') ?></p>
                                        </div>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Modal -->
<div class="modal fade" id="userModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="modalTitle"><?= lang('Common.add_user') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="userForm">
                <div class="modal-body pt-3 pb-3">
                    <?= csrf_field() ?>
                    <input type="hidden" id="userId" name="id">

                    <div class="mb-3">
                        <label for="username" class="form-label fw-bold small text-muted mb-1"><?= lang('Common.username') ?> <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="username" name="username" required minlength="3">
                    </div>

                    <div class="mb-3">
                        <label for="password" class="form-label fw-bold small text-muted mb-1"><?= lang('Common.password') ?> <span class="text-danger" id="passwordReq">*</span></label>
                        <input type="password" class="form-control" id="password" name="password" minlength="4">
                        <small class="text-muted" id="passwordHelp" style="display:none;"><?= lang('Common.leave_blank_no_change') ?></small>
                    </div>

                    <div class="mb-3">
                        <label for="confirm_password" class="form-label fw-bold small text-muted mb-1"><?= lang('Common.confirm_password') ?> <span class="text-danger" id="confirmPasswordReq">*</span></label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="access_login" name="access_login" value="1" checked>
                        <label class="form-check-label" for="access_login"><?= lang('Common.enable_login') ?></label>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0 pb-4 pe-4">
                    <button type="button" class="btn btn-dark px-4 rounded-3" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                    <button type="submit" class="btn btn-primary px-4 rounded-3"><?= lang('Common.save') ?></button>
                </div>
            </form>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const langEnterPlayerId = '<?= lang("Common.enter_player_id") ?>';
const langExampleId = '<?= lang("Common.example_id") ?>';
const langViewDetails = '<?= lang("Common.view_details") ?>';
const langCancel = '<?= lang("Common.cancel") ?>';
const langPleaseEnterPlayerId = '<?= lang("Common.please_enter_player_id") ?>';
const langPlayerIdMustBePositive = '<?= lang("Common.player_id_must_be_positive") ?>';
const langAddUser = '<?= lang("Common.add_user") ?>';
const langEditUser = '<?= lang("Common.edit_user") ?>';
const langErrorFetchingData = '<?= lang("Common.error_fetching_data") ?>';
const langConfirmDeleteTitle = '<?= lang("Common.confirm_delete_title") ?>';
const langConfirmDeleteUserText = '<?= lang("Common.confirm_delete_user_text") ?>';
const langDelete = '<?= lang("Common.delete") ?>';
const langErrorDeletingData = '<?= lang("Common.error_deleting_data") ?>';
const langPasswordNotMatch = '<?= lang("Common.password_not_match") ?>';
const langErrorOccurred = '<?= lang("Common.error_occurred") ?>';
const langErrorSavingData = '<?= lang("Common.error_saving_data") ?>';

function promptPlayerId() {
    Swal.fire({
        title: langEnterPlayerId,
        input: 'number',
        inputLabel: 'Player ID',
        inputPlaceholder: langExampleId,
        showCancelButton: true,
        confirmButtonText: langViewDetails,
        cancelButtonText: langCancel,
        inputValidator: (value) => {
            if (!value) {
                return langPleaseEnterPlayerId
            }
            if (value <= 0) {
                return langPlayerIdMustBePositive
            }
        }
    }).then((result) => {
        if (result.isConfirmed) {
            const playerId = result.value;
            const url = '<?= base_url('admin/report-guest/player-details/') ?>' + playerId;
            window.open(url, '_blank');
        }
    })
}

function resetUserForm() {
    $('#userForm')[0].reset();
    $('#userId').val('');
    $('#modalTitle').text(langAddUser);
    $('#passwordHelp').hide();
    $('#password').prop('required', true);
    $('#confirm_password').prop('required', true);
}

function editUser(id) {
    $.ajax({
        url: '<?= base_url('admin/role/get-user') ?>/' + id,
        type: 'GET',
        dataType: 'json',
        success: function(response) {
            if (response.status) {
                const user = response.data;
                $('#userId').val(user.id);
                $('#username').val(user.username);
                $('#access_login').prop('checked', user.access_login == 1);

                $('#modalTitle').text(langEditUser);
                $('#passwordHelp').show();
                $('#password').prop('required', false);
                $('#confirm_password').prop('required', false);

                $('#userModal').modal('show');
            } else {
                Swal.fire('Error', response.message, 'error');
            }
        },
        error: function() {
            Swal.fire('Error', langErrorFetchingData, 'error');
        }
    });
}

function deleteUser(id) {
    Swal.fire({
        title: langConfirmDeleteTitle,
        text: langConfirmDeleteUserText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: langDelete,
        cancelButtonText: langCancel
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: '<?= base_url('admin/role/delete-user') ?>',
                type: 'POST',
                data: { id: id, <?= csrf_token() ?>: '<?= csrf_hash() ?>' },
                dataType: 'json',
                success: function(response) {
                    if (response.status) {
                        Swal.fire('Deleted!', response.message, 'success').then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire('Error', response.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', langErrorDeletingData, 'error');
                }
            });
        }
    });
}

$(document).ready(function() {
    // Move modal to body to obtain correct loop context
    $('#userModal').appendTo("body");

    // Use delegated event since the form is inside the moved modal
    $(document).on('submit', '#userForm', function(e) {
        e.preventDefault();

        const password = $('#password').val();
        const confirmPassword = $('#confirm_password').val();

        if (password && password !== confirmPassword) {
            Swal.fire('Error', langPasswordNotMatch, 'error');
            return;
        }

        const formData = $(this).serialize();

        $.ajax({
            url: '<?= base_url('admin/role/save-user') ?>',
            type: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.status) {
                    $('#userModal').modal('hide');
                    Swal.fire('Success', response.message, 'success').then(() => {
                        location.reload();
                    });
                } else {
                    let msg = response.message || langErrorOccurred;
                    if (response.errors) {
                        msg = Object.values(response.errors).join('<br>');
                    }
                    Swal.fire('Error', msg, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', langErrorSavingData, 'error');
            }
        });
    });
});
</script>
<?= $this->endSection() ?>