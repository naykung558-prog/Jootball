<?= $this->extend('\App\Views\template\admin_layout') ?>

<?= $this->section('content') ?>
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-body p-4">

        <div class="d-flex justify-content-between align-items-center mb-4 border-bottom pb-3">
            <div>
                <h4 class="card-title fw-bold mb-1"><?= $title ?></h4>
                <p class="text-muted mb-0"><?= lang('Common.set_details_and_permissions') ?></p>
            </div>
            <div class="d-flex gap-2">
                <?php if (isset($role) && $role && $role['name'] == 'admin'): ?>
                    <div class="dropdown">
                        <button class="btn btn-outline-success dropdown-toggle shadow-sm" type="button" data-bs-toggle="dropdown">
                            <i class="ti ti-report-analytics me-2"></i> ReportGuest Access
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                            <li><h6 class="dropdown-header text-uppercase small fw-bold">Admin Role Access</h6></li>
                            <li><a class="dropdown-item" href="<?= base_url('admin/report-guest/report-players') ?>" target="_blank">
                                <i class="ti ti-chart-line me-2 text-primary"></i><?= lang('Common.player_report') ?>
                            </a></li>
                            <li><a class="dropdown-item" href="#" onclick="promptPlayerIdFromManage()">
                                <i class="ti ti-search me-2 text-info"></i><?= lang('Common.search_player_by_id') ?>
                            </a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><small class="dropdown-item-text text-muted">
                                <i class="ti ti-info-circle me-1"></i>
                                <?= lang('Common.admin_role_reportguest_only') ?>
                            </small></li>
                        </ul>
                    </div>
                <?php endif; ?>
                <a href="<?= base_url('admin/role') ?>" class="btn btn-light border shadow-sm">
                    <i class="ti ti-arrow-left me-2"></i> <?= lang('Common.back') ?>
                </a>
            </div>
        </div>

        <?php if (session()->getFlashdata('errors')): ?>
            <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex">
                    <i class="ti ti-alert-circle fs-4 me-2 mt-1"></i>
                    <ul class="mb-0 ps-3">
                        <?php foreach (session()->getFlashdata('errors') as $error): ?>
                            <li><?= $error ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <form action="<?= base_url('admin/role/save') ?>" method="post" class="once-only" autocomplete="off">
            <?= csrf_field() ?>

            <?php if ($role): ?>
                <input type="hidden" name="id" value="<?= $role['id'] ?>">
            <?php endif; ?>

            <div class="row g-4">
                <div class="col-12">
                    <h5 class="fw-bold text-primary mb-3"><i class="ti ti-info-circle me-2"></i><?= lang('Common.general_information') ?></h5>
                </div>

                <div class="col-md-6">
                    <label for="name" class="form-label fw-bold"><?= lang('Common.role_name') ?> (System Name) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-code"></i></span>
                        <input type="text" class="form-control" id="name" name="name"
                               value="<?= old('name', $role['name'] ?? '') ?>" required placeholder="<?= lang('Common.role_name_placeholder') ?>">
                    </div>
                    <div class="form-text text-muted small"><i class="ti ti-help me-1"></i><?= lang('Common.role_name_hint') ?></div>
                </div>

                <div class="col-md-6">
                    <label for="display_name" class="form-label fw-bold"><?= lang('Common.display_name') ?> (Display Name) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-tag"></i></span>
                        <input type="text" class="form-control" id="display_name" name="display_name"
                               value="<?= old('display_name', $role['display_name'] ?? '') ?>" required placeholder="<?= lang('Common.display_name_placeholder') ?>">
                    </div>
                </div>

                <div class="col-md-8">
                    <label for="description" class="form-label fw-bold"><?= lang('Common.description') ?></label>
                    <textarea class="form-control" id="description" name="description" rows="3" placeholder="<?= lang('Common.description_placeholder') ?>"><?= old('description', $role['description'] ?? '') ?></textarea>
                </div>

                <div class="col-md-4">
                    <label for="status" class="form-label fw-bold"><?= lang('Common.status') ?></label>
                    <select class="form-select" id="status" name="status">
                        <option value="1" <?= old('status', $role['status'] ?? 1) == 1 ? 'selected' : '' ?>><?= lang('Common.active') ?></option>
                        <option value="0" <?= old('status', $role['status'] ?? 1) == 0 ? 'selected' : '' ?>><?= lang('Common.inactive') ?></option>
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <h5 class="fw-bold text-primary mb-3 border-bottom pb-2"><i class="ti ti-lock-access me-2"></i><?= lang('Common.assign_permissions') ?></h5>
                </div>

                <div class="col-12">
                    <div class="row g-3">
                        <?php if (!empty($permissions)): ?>
                            <?php foreach ($permissions as $module => $modulePermissions): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="card h-100 border shadow-sm hover-shadow transition-all <?= ($module == 'report_guest') ? 'border-success' : '' ?>">
                                        <div class="card-header py-2 <?= ($module == 'report_guest') ? 'bg-success text-white' : 'bg-light' ?>">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <h6 class="fw-bold mb-0 text-uppercase small">
                                                    <?= ucfirst($module) ?>
                                                    <?php if ($module == 'report_guest'): ?>
                                                        <i class="ti ti-star-filled ms-1 text-warning"></i>
                                                    <?php endif; ?>
                                                </h6>
                                                <div class="form-check form-switch m-0">
                                                    <input class="form-check-input select-all-module" type="checkbox" role="switch" data-module="<?= $module ?>" title="<?= lang('Common.select_all') ?>">
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card-body p-3">
                                            <?php foreach ($modulePermissions as $permission): ?>
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input permission-checkbox module-<?= $module ?>" type="checkbox"
                                                           name="permissions[]" value="<?= $permission['id'] ?>"
                                                           id="permission_<?= $permission['id'] ?>"
                                                           <?= in_array($permission['id'], $rolePermissions ?? []) ? 'checked' : '' ?>>
                                                    <label class="form-check-label user-select-none" for="permission_<?= $permission['id'] ?>">
                                                        <?= esc($permission['display_name']) ?>
                                                        <?php if ($permission['name'] == 'report_guest.view'): ?>
                                                            <span class="badge bg-success-subtle text-success ms-1" style="font-size: 0.65rem;">ADMIN</span>
                                                        <?php endif; ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="row justify-content-center mt-5">
                <div class="col-md-12 text-center">
                    <a href="<?= base_url('admin/role') ?>" class="btn btn-light border btn-lg px-4 me-2 shadow-sm rounded-pill">
                        <i class="ti ti-arrow-left me-2"></i> <?= lang('Common.cancel') ?>
                    </a>
                    <button type="submit" class="btn btn-primary btn-lg px-5 shadow-sm rounded-pill btn-submit">
                        <i class="ti ti-device-floppy me-2"></i> <?= lang('Common.save_data') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-2px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const langSearchPlayer = '<?= lang("Common.search_player") ?>';
const langEnterPlayerId = '<?= lang("Common.enter_player_id") ?>';
const langExampleId = '<?= lang("Common.example_id") ?>';
const langSearch = '<?= lang("Common.search") ?>';
const langCancel = '<?= lang("Common.cancel") ?>';
const langPleaseEnterPlayerId = '<?= lang("Common.please_enter_player_id") ?>';
const langPlayerIdMustBePositive = '<?= lang("Common.player_id_must_be_positive") ?>';

$(document).ready(function() {
    // Convert name to lowercase with underscores
    $('#name').on('input', function() {
        let value = $(this).val().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9_]/g, '');
        $(this).val(value);
    });

    // Select All Logic
    $('.select-all-module').on('change', function() {
        const module = $(this).data('module');
        const isChecked = $(this).is(':checked');
        $(`.module-${module}`).prop('checked', isChecked);
    });

    // Update Select All state when individual checkboxes change
    $('.permission-checkbox').on('change', function() {
        const moduleClass = $(this).attr('class').split(' ').find(c => c.startsWith('module-'));
        const module = moduleClass.replace('module-', '');
        const allChecked = $(`.${moduleClass}:checked`).length === $(`.${moduleClass}`).length;
        $(`.select-all-module[data-module="${module}"]`).prop('checked', allChecked);
    });

    // Initial check for Select All state
    $('.select-all-module').each(function() {
        const module = $(this).data('module');
        const allChecked = $(`.module-${module}:checked`).length === $(`.module-${module}`).length && $(`.module-${module}`).length > 0;
        $(this).prop('checked', allChecked);
    });
});

function promptPlayerIdFromManage() {
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