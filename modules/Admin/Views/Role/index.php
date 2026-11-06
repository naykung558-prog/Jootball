<?= $this->extend('\App\Views\template\admin_layout') ?>

<?= $this->section('content') ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--navy-gradient); box-shadow: var(--box-shadow);">
                    <i class="fas fa-user-cog text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= $title ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.manage_roles_and_permissions') ?></p>
                </div>
            </div>

            <?php if (hasPermission('role.create')): ?>
                <div>
                    <a href="<?= base_url('admin/role/manage') ?>" class="btn btn-primary px-4 py-2 fw-bold" style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                        <i class="ti ti-plus me-2"></i>
                        <?= lang('Common.add_new_role') ?>
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert" style="border-radius: var(--border-radius);">
        <i class="ti ti-check-circle me-2 fs-4"></i>
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert" style="border-radius: var(--border-radius);">
        <i class="ti ti-alert-circle me-2 fs-4"></i>
        <?= session()->getFlashdata('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="rolesTable" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-start" width="15%" style="border-radius: var(--border-radius) 0 0 0;"><?= lang('Common.role_name') ?></th>
                        <th class="py-3 text-start" width="20%"><?= lang('Common.display_name') ?></th>
                        <th class="py-3 text-start" width="25%"><?= lang('Common.description') ?></th>
                        <th class="py-3 text-center" width="15%"><?= lang('Common.permission_count') ?></th>
                        <th class="py-3 text-center" width="10%"><?= lang('Common.status') ?></th>
                        <th class="py-3 pe-4 text-center" width="15%" style="border-radius: 0 var(--border-radius) 0 0;"><?= lang('Common.action') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td class="ps-4">
                                    <span class="badge bg-light text-dark border fw-bold px-3 py-2"><?= esc($role['name']) ?></span>
                                </td>
                                <td>
                                    <div class="fw-bold" style="color: var(--secondary-color);"><?= esc($role['display_name']) ?></div>
                                </td>
                                <td>
                                    <div class="text-muted small"><?= esc($role['description']) ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(6, 52, 109, 0.1); color: var(--secondary-color);">
                                        <?= $role['permission_count'] ?? 0 ?> <?= lang('Common.permissions') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($role['status'] == 1): ?>
                                        <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2"><?= lang('Common.active') ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-danger-subtle text-danger rounded-pill px-3 py-2"><?= lang('Common.inactive') ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-2">
                                        <?php if (hasPermission('role.edit')): ?>
                                            <a href="<?= base_url('admin/role/manage/' . $role['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3" title="<?= lang('Common.edit') ?>">
                                                <i class="ti ti-pencil me-1"></i> <?= lang('Common.edit') ?>
                                            </a>
                                        <?php endif; ?>

                                        <?php if ($role['name'] == 'admin'): ?>
                                            <div class="dropdown">
                                                <button class="btn btn-outline-success btn-sm rounded-pill px-3 dropdown-toggle" type="button" data-bs-toggle="dropdown" title="ReportGuest">
                                                    <i class="ti ti-report-analytics me-1"></i> Report
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                    <li><h6 class="dropdown-header text-uppercase small fw-bold">ReportGuest Access</h6></li>
                                                    <li><a class="dropdown-item" href="<?= base_url('admin/report-guest/report-players') ?>" target="_blank">
                                                        <i class="ti ti-chart-line me-2 text-primary"></i><?= lang('Common.player_report') ?>
                                                    </a></li>
                                                    <li><a class="dropdown-item" href="#" onclick="promptPlayerIdFromIndex()">
                                                        <i class="ti ti-search me-2 text-info"></i><?= lang('Common.search_player_by_id') ?>
                                                    </a></li>
                                                </ul>
                                            </div>
                                        <?php endif; ?>

                                        <?php if (hasPermission('role.delete') && !in_array($role['name'], ['super_admin', 'admin'])): ?>
                                            <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3" onclick="deleteRole(<?= $role['id'] ?>)" title="<?= lang('Common.delete') ?>">
                                                <i class="ti ti-trash me-1"></i> <?= lang('Common.delete') ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="ti ti-database-off fs-1 d-block mb-3 opacity-50"></i>
                                    <span class="fs-5"><?= lang('Common.no_roles_found') ?></span>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const langConfirmDeleteTitle = '<?= lang("Common.confirm_delete_title") ?>';
const langConfirmDeleteRoleText = '<?= lang("Common.confirm_delete_role_text") ?>';
const langYesDelete = '<?= lang("Common.yes_delete") ?>';
const langCancel = '<?= lang("Common.cancel") ?>';
const langSuccess = '<?= lang("Common.success") ?>';
const langError = '<?= lang("Common.error") ?>';
const langCannotConnectServer = '<?= lang("Common.cannot_connect_server") ?>';
const langSearchPlayer = '<?= lang("Common.search_player") ?>';
const langEnterPlayerId = '<?= lang("Common.enter_player_id") ?>';
const langExampleId = '<?= lang("Common.example_id") ?>';
const langSearch = '<?= lang("Common.search") ?>';
const langPleaseEnterPlayerId = '<?= lang("Common.please_enter_player_id") ?>';
const langPlayerIdMustBePositive = '<?= lang("Common.player_id_must_be_positive") ?>';

function deleteRole(id) {
    Swal.fire({
        title: langConfirmDeleteTitle,
        text: langConfirmDeleteRoleText,
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: langYesDelete,
        cancelButtonText: langCancel
    }).then((result) => {
        if (result.isConfirmed) {
            $.post('<?= base_url('admin/role/delete') ?>', {id: id})
            .done(function(response) {
                if (response.status) {
                    Swal.fire({
                        title: langSuccess,
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire(langError, response.message, 'error');
                }
            })
            .fail(function() {
                Swal.fire(langError, langCannotConnectServer, 'error');
            });
        }
    })
}

function promptPlayerIdFromIndex() {
    Swal.fire({
        title: langSearchPlayer,
        input: 'number',
        inputLabel: langEnterPlayerId,
        inputPlaceholder: langExampleId,
        showCancelButton: true,
        confirmButtonText: langSearch,
        cancelButtonText: langCancel,
        confirmButtonColor: '#0d6efd',
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
</script>
<?= $this->endSection() ?>
