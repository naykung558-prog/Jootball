<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-users me-2"></i><?= lang('Common.academy_members') ?>
                </h4>
                <p class="text-muted small mb-0">
                    <?= esc($academy['name'] ?? '') ?>
                </p>
            </div>
            <?php if (!empty($perms['perm_manage_members'])): ?>
            <div>
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addMemberModal">
                    <i class="fas fa-user-plus me-2"></i><?= lang('Common.academy_add_member') ?>
                </button>
            </div>
            <?php endif; ?>
        </div>

        <div class="card-body p-0">

            <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success alert-dismissible fade show mx-4 mt-3 border-0 shadow-sm rounded-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-check-circle me-2"></i>
                    <div><?= session()->getFlashdata('success') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show mx-4 mt-3 border-0 shadow-sm rounded-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>

            <?php if (empty($members)): ?>
            <div class="text-center py-5">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <p class="text-muted mb-0"><?= lang('Common.academy_no_members') ?></p>
            </div>
            <?php else: ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 ps-4" width="5%">#</th>
                            <th class="border-0"><?= lang('Common.academy_member') ?></th>
                            <th class="border-0" width="15%"><?= lang('Common.role') ?></th>
                            <th class="border-0" width="15%"><?= lang('Common.status') ?></th>
                            <?php if (!empty($perms['perm_manage_members'])): ?>
                            <th class="border-0 text-end pe-4" width="20%"><?= lang('Common.manage') ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $index => $member): ?>
                        <?php
                            // Determine display name and image
                            $isCoach = !empty($member['coach_user_id']);
                            if ($isCoach) {
                                $displayName = trim(esc($member['coach_first_name'] ?? '') . ' ' . esc($member['coach_last_name'] ?? ''));
                                if (empty(trim($displayName))) {
                                    $displayName = esc($member['coach_username'] ?? '');
                                }
                                $displayUsername = esc($member['coach_username'] ?? '');
                                $displayImage   = $member['coach_image'] ?? '';
                            } else {
                                $displayName     = esc($member['user_name'] ?? '');
                                $displayUsername = esc($member['username'] ?? '');
                                $displayImage    = $member['profile_image'] ?? '';
                            }

                            // Role badge colour
                            $roleMap = ['owner' => 'bg-primary', 'admin' => 'bg-info', 'coach' => 'bg-success', 'viewer' => 'bg-secondary'];
                            $roleBadgeClass = $roleMap[$member['role'] ?? ''] ?? 'bg-secondary';
                            $roleLabelMap = ['owner' => lang('Common.role_owner'), 'admin' => lang('Common.role_admin'), 'coach' => lang('Common.role_coach'), 'viewer' => lang('Common.role_viewer')];
                            $roleLabel = $roleLabelMap[$member['role'] ?? ''] ?? esc($member['role'] ?? '-');

                            // Status badge colour
                            $statusMap = ['approved' => 'bg-success', 'pending' => 'bg-warning text-dark', 'rejected' => 'bg-danger'];
                            $statusBadgeClass = $statusMap[$member['status'] ?? ''] ?? 'bg-secondary';
                            $statusLabelMap = ['approved' => lang('Common.status_approved'), 'pending' => lang('Common.status_pending_approval'), 'rejected' => lang('Common.status_rejected')];
                            $statusLabel = $statusLabelMap[$member['status'] ?? ''] ?? esc($member['status'] ?? '-');

                            $firstLetter = strtoupper(substr(strip_tags($displayName), 0, 1) ?: 'U');
                        ?>
                        <tr>
                            <td class="ps-4 text-muted fw-bold"><?= $index + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <?php if (!empty($displayImage) && file_exists(FCPATH . $displayImage)): ?>
                                        <img src="<?= base_url(esc($displayImage)) ?>"
                                             class="rounded-circle me-3 shadow-sm"
                                             style="width: 42px; height: 42px; object-fit: cover;"
                                             alt="">
                                    <?php else: ?>
                                        <div class="rounded-circle me-3 d-flex align-items-center justify-content-center bg-primary text-white fw-bold shadow-sm flex-shrink-0"
                                             style="width: 42px; height: 42px; font-size: 1rem;">
                                            <?= $firstLetter ?>
                                        </div>
                                    <?php endif; ?>
                                    <div>
                                        <p class="mb-0 fw-bold text-dark"><?= $displayName ?></p>
                                        <?php if (!empty($displayUsername)): ?>
                                        <small class="text-muted">
                                            <i class="fas fa-user me-1"></i><?= $displayUsername ?>
                                        </small>
                                        <?php endif; ?>
                                        <?php if ($isCoach): ?>
                                        <br><small class="badge bg-light text-secondary border">
                                            <i class="fas fa-whistle me-1"></i><?= lang('Common.role_coach') ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge <?= $roleBadgeClass ?> rounded-pill px-3">
                                    <?= $roleLabel ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge <?= $statusBadgeClass ?> rounded-pill px-3">
                                    <?= $statusLabel ?>
                                </span>
                            </td>
                            <?php if (!empty($perms['perm_manage_members'])): ?>
                            <td class="text-end pe-4">
                                <?php if (($member['role'] ?? '') !== 'owner'): ?>
                                <div class="d-flex gap-2 justify-content-end align-items-center">
                                    <!-- Change Role dropdown -->
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary rounded-pill dropdown-toggle"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false"
                                                title="<?= lang('Common.academy_change_role') ?>">
                                            <i class="fas fa-user-tag me-1"></i><?= lang('Common.role') ?>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                            <li>
                                                <h6 class="dropdown-header text-muted small"><?= lang('Common.academy_select_role') ?></h6>
                                            </li>
                                            <?php foreach (['admin' => lang('Common.role_admin'), 'coach' => lang('Common.role_coach'), 'viewer' => lang('Common.role_viewer')] as $roleVal => $roleText): ?>
                                            <li>
                                                <button class="dropdown-item btn-change-role
                                                               <?= ($member['role'] ?? '') === $roleVal ? 'active' : '' ?>"
                                                        type="button"
                                                        data-id="<?= esc($member['id']) ?>"
                                                        data-role="<?= $roleVal ?>"
                                                        data-name="<?= $displayName ?>">
                                                    <?php if (($member['role'] ?? '') === $roleVal): ?>
                                                    <i class="fas fa-check me-2 text-success"></i>
                                                    <?php endif; ?>
                                                    <?= $roleText ?>
                                                </button>
                                            </li>
                                            <?php endforeach; ?>
                                        </ul>
                                    </div>
                                    <!-- Remove button -->
                                    <button class="btn btn-sm btn-outline-danger rounded-pill btn-remove-member"
                                            type="button"
                                            data-id="<?= esc($member['id']) ?>"
                                            data-name="<?= $displayName ?>"
                                            title="<?= lang('Common.remove_from_list') ?>">
                                        <i class="fas fa-user-minus me-1"></i><?= lang('Common.remove_from_list') ?>
                                    </button>
                                </div>
                                <?php else: ?>
                                <span class="text-muted small">-</span>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>

<?php if (!empty($perms['perm_manage_members'])): ?>
<!-- Add Member Modal -->
<div class="modal fade" id="addMemberModal" tabindex="-1" aria-labelledby="addMemberModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary" id="addMemberModalLabel">
                    <i class="fas fa-user-plus me-2"></i><?= lang('Common.academy_add_member') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addMemberForm" action="<?= base_url('academy/member-save') ?>" method="POST">
                <?= csrf_field() ?>
                <input type="hidden" name="academy_id" value="<?= esc($academy['id'] ?? '') ?>">
                <div class="modal-body pt-4">

                    <div id="addMemberAlert" class="alert d-none mb-3" role="alert"></div>

                    <div class="mb-3">
                        <label for="memberSearch" class="form-label fw-semibold text-dark">
                            <?= lang('Common.academy_search_user') ?>
                            <small class="text-muted fw-normal">(<?= lang('Common.academy_search_user_hint') ?>)</small>
                        </label>
                        <div class="input-group">
                            <input type="text"
                                   class="form-control bg-light border-0"
                                   id="memberSearch"
                                   placeholder="<?= lang('Common.academy_search_user_placeholder') ?>"
                                   autocomplete="off">
                            <button class="btn btn-primary" type="button" id="btnSearchMember">
                                <i class="fas fa-search me-1"></i><?= lang('Common.search') ?>
                            </button>
                        </div>
                        <div id="searchResultsContainer" class="mt-2" style="display: none;">
                            <div class="list-group shadow-sm rounded-3" id="searchResultsList"></div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold text-dark"><?= lang('Common.academy_selected_user') ?></label>
                        <div id="selectedUserDisplay" class="bg-light rounded-3 p-3 text-muted small">
                            <i class="fas fa-user me-2"></i><?= lang('Common.academy_no_user_selected') ?>
                        </div>
                        <input type="hidden" name="user_id" id="selectedUserId">
                    </div>

                    <div class="mb-3">
                        <label for="memberRole" class="form-label fw-semibold text-dark"><?= lang('Common.role') ?></label>
                        <select class="form-select bg-light border-0" id="memberRole" name="role" required>
                            <option value="" disabled selected>-- <?= lang('Common.academy_select_role') ?> --</option>
                            <option value="admin"><?= lang('Common.role_admin') ?></option>
                            <option value="coach"><?= lang('Common.role_coach') ?></option>
                            <option value="viewer"><?= lang('Common.role_viewer') ?></option>
                        </select>
                    </div>

                </div>
                <div class="modal-footer border-0 pt-0 pb-4">
                    <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">
                        <?= lang('Common.cancel') ?>
                    </button>
                    <button type="submit" id="btnAddMember" class="btn btn-primary rounded-pill px-4" disabled>
                        <i class="fas fa-user-plus me-2"></i><?= lang('Common.academy_add_member') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
document.addEventListener('DOMContentLoaded', function () {

    // ---- Role change via AJAX ----
    document.querySelectorAll('.btn-change-role').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const memberId = this.dataset.id;
            const newRole  = this.dataset.role;
            const name     = this.dataset.name;

            Swal.fire({
                title: '<?= lang('Common.academy_change_role') ?>',
                html: '<?= lang('Common.academy_change_role_msg') ?>'.replace('%s', name).replace('%s', newRole),
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#16478C',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<?= lang('Common.confirm') ?>',
                cancelButtonText: '<?= lang('Common.cancel') ?>'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                const formData = new FormData();
                formData.append('id', memberId);
                formData.append('role', newRole);
                formData.append('_action', 'change_role');
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                fetch('<?= base_url('academy/member-save') ?>', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: '<?= lang('Common.success') ?>',
                            text: data.message || '<?= lang('Common.academy_role_changed') ?>',
                            confirmButtonColor: '#198754',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '<?= lang('Common.error') ?>',
                            text: data.message || '<?= lang('Common.academy_role_change_failed') ?>',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(function () {
                    Swal.fire({
                        icon: 'error',
                        title: '<?= lang('Common.error') ?>',
                        text: '<?= lang('Common.academy_server_error') ?>',
                        confirmButtonColor: '#dc3545'
                    });
                });
            });
        });
    });

    // ---- Remove member ----
    document.querySelectorAll('.btn-remove-member').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const memberId = this.dataset.id;
            const name     = this.dataset.name;

            Swal.fire({
                title: '<?= lang('Common.academy_remove_member') ?>',
                html: '<?= lang('Common.academy_remove_member_msg') ?>'.replace('%s', name),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<?= lang('Common.academy_yes_remove') ?>',
                cancelButtonText: '<?= lang('Common.cancel') ?>'
            }).then(function (result) {
                if (!result.isConfirmed) return;

                const formData = new FormData();
                formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

                fetch('<?= base_url('academy/member-remove') ?>/' + memberId, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: formData
                })
                .then(function (res) { return res.json(); })
                .then(function (data) {
                    if (data.status) {
                        Swal.fire({
                            icon: 'success',
                            title: '<?= lang('Common.success') ?>',
                            text: data.message || '<?= lang('Common.academy_member_removed') ?>',
                            confirmButtonColor: '#198754',
                            timer: 2000,
                            showConfirmButton: false
                        }).then(function () {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: '<?= lang('Common.error') ?>',
                            text: data.message || '<?= lang('Common.academy_member_remove_failed') ?>',
                            confirmButtonColor: '#dc3545'
                        });
                    }
                })
                .catch(function () {
                    Swal.fire({
                        icon: 'error',
                        title: '<?= lang('Common.error') ?>',
                        text: '<?= lang('Common.academy_server_error') ?>',
                        confirmButtonColor: '#dc3545'
                    });
                });
            });
        });
    });

    <?php if (!empty($perms['perm_manage_members'])): ?>

    // ---- Add Member Modal ----
    const searchInput      = document.getElementById('memberSearch');
    const btnSearch        = document.getElementById('btnSearchMember');
    const resultsContainer = document.getElementById('searchResultsContainer');
    const resultsList      = document.getElementById('searchResultsList');
    const selectedDisplay  = document.getElementById('selectedUserDisplay');
    const selectedUserId   = document.getElementById('selectedUserId');
    const btnAddMember     = document.getElementById('btnAddMember');
    const addMemberAlert   = document.getElementById('addMemberAlert');
    const addMemberForm    = document.getElementById('addMemberForm');

    let searchTimeout = null;

    function showAlert(type, message) {
        addMemberAlert.className = 'alert alert-' + type + ' mb-3';
        addMemberAlert.textContent = message;
        addMemberAlert.classList.remove('d-none');
    }

    function hideAlert() {
        addMemberAlert.classList.add('d-none');
    }

    function selectUser(userId, displayText) {
        selectedUserId.value = userId;
        selectedDisplay.innerHTML =
            '<i class="fas fa-user-check me-2 text-success"></i>' +
            '<span class="fw-semibold text-dark">' + displayText + '</span>';
        resultsContainer.style.display = 'none';
        resultsList.innerHTML = '';
        searchInput.value = '';
        btnAddMember.disabled = false;
        hideAlert();
    }

    function performSearch() {
        const query = searchInput.value.trim();
        if (!query) {
            resultsContainer.style.display = 'none';
            return;
        }

        resultsList.innerHTML =
            '<div class="list-group-item text-center text-muted py-3">' +
            '<i class="fas fa-spinner fa-spin me-2"></i><?= lang('Common.academy_searching') ?></div>';
        resultsContainer.style.display = 'block';

        const formData = new FormData();
        formData.append('query', query);
        formData.append('academy_id', '<?= esc($academy['id'] ?? '') ?>');
        formData.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

        fetch('<?= base_url('academy/member-search') ?>', {
            method: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            body: formData
        })
        .then(function (res) { return res.json(); })
        .then(function (data) {
            if (!data.status || !data.results || data.results.length === 0) {
                resultsList.innerHTML =
                    '<div class="list-group-item text-center text-muted py-3">' +
                    '<i class="fas fa-user-slash me-2"></i><?= lang('Common.academy_user_not_found') ?></div>';
                return;
            }

            let html = '';
            data.results.forEach(function (user) {
                html +=
                    '<button type="button" class="list-group-item list-group-item-action py-2 px-3 d-flex align-items-center gap-2" ' +
                    'onclick="selectMemberUser(\'' + user.id + '\', \'' + user.display_name.replace(/'/g, "\\'") + '\')">' +
                    '<i class="fas fa-user text-secondary"></i>' +
                    '<span class="fw-semibold">' + user.display_name + '</span>' +
                    (user.username ? '<small class="text-muted ms-auto">@' + user.username + '</small>' : '') +
                    '</button>';
            });
            resultsList.innerHTML = html;
        })
        .catch(function () {
            resultsList.innerHTML =
                '<div class="list-group-item text-center text-danger py-3">' +
                '<i class="fas fa-exclamation-circle me-2"></i><?= lang('Common.academy_error_try_again') ?></div>';
        });
    }

    btnSearch.addEventListener('click', performSearch);

    searchInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            performSearch();
        }
    });

    searchInput.addEventListener('input', function () {
        clearTimeout(searchTimeout);
        if (!this.value.trim()) {
            resultsContainer.style.display = 'none';
            return;
        }
        searchTimeout = setTimeout(performSearch, 400);
    });

    // Close search results when clicking outside
    document.addEventListener('click', function (e) {
        if (!resultsContainer.contains(e.target) && e.target !== searchInput && e.target !== btnSearch) {
            resultsContainer.style.display = 'none';
        }
    });

    addMemberForm.addEventListener('submit', function (e) {
        if (!selectedUserId.value) {
            e.preventDefault();
            showAlert('warning', '<?= lang('Common.academy_please_select_user') ?>');
            return;
        }
        const role = document.getElementById('memberRole').value;
        if (!role) {
            e.preventDefault();
            showAlert('warning', '<?= lang('Common.academy_please_select_role') ?>');
            return;
        }
    });

    // Reset modal state on close
    document.getElementById('addMemberModal').addEventListener('hidden.bs.modal', function () {
        searchInput.value       = '';
        selectedUserId.value    = '';
        selectedDisplay.innerHTML =
            '<i class="fas fa-user me-2"></i><?= lang('Common.academy_no_user_selected') ?>';
        document.getElementById('memberRole').value = '';
        btnAddMember.disabled   = true;
        resultsContainer.style.display = 'none';
        resultsList.innerHTML   = '';
        hideAlert();
    });

    <?php endif; ?>

    // Flash messages via SweetAlert2
    <?php if (session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: '<?= lang('Common.success') ?>',
        text: '<?= addslashes(session()->getFlashdata('success')) ?>',
        timer: 2500,
        showConfirmButton: false
    });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    Swal.fire({
        icon: 'error',
        title: '<?= lang('Common.error') ?>',
        text: '<?= addslashes(session()->getFlashdata('error')) ?>',
        confirmButtonColor: '#dc3545'
    });
    <?php endif; ?>
});

// Global function called by dynamically-generated list-group buttons
function selectMemberUser(userId, displayName) {
    document.getElementById('selectedUserId').value = userId;
    document.getElementById('selectedUserDisplay').innerHTML =
        '<i class="fas fa-user-check me-2 text-success"></i>' +
        '<span class="fw-semibold text-dark">' + displayName + '</span>';
    document.getElementById('searchResultsContainer').style.display = 'none';
    document.getElementById('searchResultsList').innerHTML = '';
    document.getElementById('memberSearch').value = '';
    document.getElementById('btnAddMember').disabled = false;
    document.getElementById('addMemberAlert').classList.add('d-none');
}
</script>
<?php $this->endSection(); ?>
