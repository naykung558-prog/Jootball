<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <!-- Group Form -->
    <div class="card shadow-lg mt-3">
        <div class="card-header bg-primary text-white">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-1 text-white">
                        <i class="fas fa-layer-group me-2"></i>
                        <?= !empty($group['id']) ? lang('Common.training_group_edit') : lang('Common.training_group_create') ?>
                    </h5>
                    <small class="opacity-75"><?= esc($academy['name']) ?></small>
                </div>
                <a href="<?= base_url('academy/training-groups/' . esc($academy['id'])) ?>" class="btn btn-light btn-sm">
                    <i class="fas fa-arrow-left me-1"></i> <?= lang('Common.back') ?>
                </a>
            </div>
        </div>
        <div class="card-body">
            <form action="<?= base_url('academy/training-group/save') ?>" method="POST" id="group-form" class="once-only" autocomplete="off">
                <input type="hidden" name="academy_id" value="<?= esc($academy['id']) ?>">
                <?php if (!empty($group['id'])): ?>
                    <input type="hidden" name="id" value="<?= esc($group['id']) ?>">
                <?php endif; ?>

                <div class="row justify-content-center">
                    <div class="col-md-5">
                        <label class="pt-3"><?= lang('Common.training_group_name') ?> <span class="text-danger">*</span></label>
                        <input type="text" name="name" value="<?= esc($group['name'] ?? '') ?>" class="form-control my-2" required>
                    </div>
                    <div class="col-md-5">
                        <label class="pt-3"><?= lang('Common.training_group_age_range') ?></label>
                        <input type="text" name="age_range" value="<?= esc($group['age_range'] ?? '') ?>" class="form-control my-2" placeholder="U12, U15, ...">
                    </div>
                    <div class="col-md-10">
                        <label class="pt-3"><?= lang('Common.training_group_description') ?></label>
                        <textarea name="description" class="form-control my-2" rows="3"><?= esc($group['description'] ?? '') ?></textarea>
                    </div>
                    <div class="col-md-10">
                        <label class="pt-3"><?= lang('Common.training_group_schedule') ?></label>
                        <textarea name="schedule_info" class="form-control my-2" rows="2"><?= esc($group['schedule_info'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-12 text-center mt-4">
                        <button type="button" class="btn btn-primary my-2 btn-submit px-5" id="pre-submit-btn">
                            <i class="fas fa-save me-2"></i><?= lang('Common.submit_data') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if (!empty($group['id'])): ?>
    <!-- Coaches in Group -->
    <div class="card shadow-sm border-0 rounded-4 mt-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-chalkboard-teacher me-2"></i><?= lang('Common.training_group_coaches') ?>
            </h5>
        </div>
        <div class="card-body p-4">
            <!-- Existing Coaches List -->
            <div class="table-responsive mb-4">
                <table class="table table-hover align-middle mb-0" id="coaches-table">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center py-2 border-0" width="5%">#</th>
                            <th class="py-2 border-0"><?= lang('Common.full_name') ?></th>
                            <th class="text-center py-2 border-0" width="20%"><?= lang('Common.training_coach_role') ?></th>
                            <?php if (!empty($perms['perm_manage_training_group'])): ?>
                            <th class="text-center py-2 border-0" width="15%"><?= lang('Common.manage') ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($coaches)): ?>
                            <?php foreach ($coaches as $index => $coach): ?>
                            <tr id="coach-row-<?= (int)$coach['id'] ?>">
                                <td class="text-center text-muted fw-bold"><?= $index + 1 ?></td>
                                <td class="fw-medium"><?= esc($coach['name']) ?></td>
                                <td class="text-center">
                                    <?php if (($coach['role'] ?? '') === 'head'): ?>
                                        <span class="badge bg-warning text-dark rounded-pill px-3"><?= lang('Common.training_head_coach') ?></span>
                                    <?php else: ?>
                                        <span class="badge bg-info text-dark rounded-pill px-3"><?= lang('Common.training_assistant_coach') ?></span>
                                    <?php endif; ?>
                                </td>
                                <?php if (!empty($perms['perm_manage_training_group'])): ?>
                                <td class="text-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3"
                                            onclick="removeCoach(<?= (int)$coach['coach_id'] ?>)">
                                        <i class="fas fa-times me-1"></i><?= lang('Common.delete') ?>
                                    </button>
                                </td>
                                <?php endif; ?>
                            </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr id="no-coaches-row">
                                <td colspan="<?= !empty($perms['perm_manage_training_group']) ? 4 : 3 ?>" class="text-center py-4 text-muted"><?= lang('Common.no_data') ?></td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Add Coach Form -->
            <?php if (!empty($perms['perm_manage_training_group']) && !empty($availableCoaches)): ?>
            <div class="border rounded-4 p-3 bg-light">
                <h6 class="fw-bold mb-3"><i class="fas fa-plus-circle me-2 text-primary"></i><?= lang('Common.training_assign_coach') ?></h6>
                <div class="row g-2 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label small text-muted"><?= lang('Common.training_select_coach') ?></label>
                        <select id="add-coach-select" class="form-select">
                            <option value=""><?= lang('Common.training_select_coach') ?></option>
                            <?php foreach ($availableCoaches as $ac): ?>
                                <option value="<?= (int)$ac['id'] ?>"><?= esc($ac['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small text-muted"><?= lang('Common.training_coach_role') ?></label>
                        <select id="add-coach-role" class="form-select">
                            <option value="head"><?= lang('Common.training_head_coach') ?></option>
                            <option value="assistant" selected><?= lang('Common.training_assistant_coach') ?></option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="button" class="btn btn-primary rounded-pill px-4 w-100" onclick="assignCoach()">
                            <i class="fas fa-plus me-1"></i><?= lang('Common.add') ?>
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Players in Group — Inline Picker -->
    <div class="card shadow-sm border-0 rounded-4 mt-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center gap-2">
            <h5 class="mb-0 fw-bold text-primary flex-shrink-0">
                <i class="fas fa-users me-2"></i><?= lang('Common.training_group_players') ?>
                <span class="badge bg-primary ms-2" id="cntInGroup"><?= count($players) ?></span>
            </h5>
            <?php if (!empty($perms['perm_manage_training_group']) && !empty($allPlayers)): ?>
            <div class="d-flex gap-2 align-items-center flex-nowrap">
                <div class="btn-group btn-group-sm" role="group">
                    <button type="button" class="btn btn-primary picker-filter-btn active" data-filter="all"
                            onclick="filterPlayerPicker(this,'all')"><?= lang('Common.all') ?></button>
                    <button type="button" class="btn btn-outline-primary picker-filter-btn" data-filter="in"
                            onclick="filterPlayerPicker(this,'in')"><?= lang('Common.training_group_in_group') ?></button>
                    <button type="button" class="btn btn-outline-secondary picker-filter-btn" data-filter="out"
                            onclick="filterPlayerPicker(this,'out')"><?= lang('Common.training_group_other_group') ?></button>
                </div>
                <input type="text" id="playerPickerSearch" class="form-control form-control-sm rounded-pill"
                       style="max-width:180px;" placeholder="<?= lang('Common.search_name') ?>..."
                       oninput="filterPlayerPicker(null,'search')">
            </div>
            <?php endif; ?>
        </div>
        <div class="card-body p-3">
            <?php if (empty($allPlayers)): ?>
            <div class="text-center py-4 text-muted">
                <i class="fas fa-users fa-2x mb-2 opacity-25"></i>
                <p class="mb-0"><?= lang('Common.academy_no_players') ?></p>
            </div>
            <?php else: ?>

            <!-- Picker summary -->
            <div class="text-muted small mb-2 px-1" id="playerPickerSummary"></div>

            <!-- Picker grid -->
            <div class="row g-2" id="playerPickerGrid">
                <?php foreach ($allPlayers as $p):
                    $inGroup = !empty($p['in_group']);
                    $imgSrc  = !empty($p['image_path'])
                        ? base_url($p['image_path'])
                        : base_url('public/img/football-player.webp');
                    echo view('Modules\Academy\Views\components\player_picker_card', [
                        'p'             => $p,
                        'isSelected'    => $inGroup,
                        'imgSrc'        => $imgSrc,
                        'accentColor'   => 'primary',
                        'dataKey'       => 'in-group',
                        'showTeamBadge' => true,
                        'onClickFn'     => 'togglePlayerItem',
                        'canManage'     => !empty($perms['perm_manage_training_group']),
                    ]);
                endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Bottom Action Buttons -->
    <?php if (!empty($perms['perm_manage_training_group'])): ?>
    <div class="d-flex justify-content-center gap-3 mb-5 flex-wrap">
        <?php if (!empty($allPlayers)): ?>
        <button type="button" class="btn btn-success rounded-pill px-5" id="btnSavePlayers">
            <i class="fas fa-save me-2"></i><?= lang('Common.team_player_save_btn') ?>
        </button>
        <?php endif; ?>
        <button type="button" class="btn btn-outline-danger rounded-pill px-5" onclick="deleteGroup()">
            <i class="fas fa-trash me-2"></i><?= lang('Common.training_delete_group') ?>
        </button>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<style>
    .picker-card { border-color: #dee2e6 !important; }
    .picker-card.selected { border-color: #0d6efd !important; background: rgba(13,110,253,0.07); }
    .picker-card:hover { border-color: #0d6efd !important; box-shadow: 0 2px 8px rgba(13,110,253,0.18) !important; }
</style>
<script>
    // Language variables for JS
    const langSuccess = '<?= lang('Common.success') ?>';
    const langError = '<?= lang('Common.error') ?>';
    const langCancel = '<?= lang('Common.cancel') ?>';
    const langConfirm = '<?= lang('Common.confirm') ?>';
    const langNoData = '<?= lang('Common.no_data') ?>';
    const langConfirmSaveData = '<?= lang('Common.confirm_save_data') ?>';
    const langConfirmSaveText = '<?= lang('Common.confirm_save_text') ?>';

    // Form submit with confirmation
    $(document).ready(function() {
        $("#pre-submit-btn").click(function() {
            Swal.fire({
                title: langConfirmSaveData,
                text: langConfirmSaveText,
                icon: "question",
                showCancelButton: true,
                confirmButtonColor: "#16478d",
                cancelButtonColor: "#d33",
                confirmButtonText: langConfirm,
                cancelButtonText: langCancel
            }).then((result) => {
                if (result.isConfirmed) {
                    $('#group-form').submit();
                }
            });
        });
    });

    <?php if (!empty($group['id'])): ?>
    // AJAX: Assign Coach
    function assignCoach() {
        const coachId = document.getElementById('add-coach-select').value;
        const role = document.getElementById('add-coach-role').value;

        if (!coachId) {
            Swal.fire({
                icon: 'warning',
                title: '<?= lang('Common.training_select_coach') ?>',
                confirmButtonColor: '#16478D'
            });
            return;
        }

        fetch('<?= base_url('academy/training-group/assign-coach') ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            body: JSON.stringify({
                group_id: <?= (int)$group['id'] ?>,
                academy_id: <?= (int)$academy['id'] ?>,
                coach_id: coachId,
                role: role,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: langSuccess,
                    text: data.message,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    location.reload();
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
        .catch(() => {
            Swal.fire({
                icon: 'error',
                title: langError,
                confirmButtonColor: '#dc3545'
            });
        });
    }

    // AJAX: Remove Coach
    function removeCoach(coachId) {
        Swal.fire({
            title: '<?= lang('Common.training_confirm_remove_coach') ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langConfirm,
            cancelButtonText: langCancel,
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('academy/training-group/remove-coach') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({
                        group_id: <?= (int)$group['id'] ?>,
                        academy_id: <?= (int)$academy['id'] ?>,
                        coach_id: coachId,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: langSuccess,
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            location.reload();
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
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: langError,
                        confirmButtonColor: '#dc3545'
                    });
                });
            }
        });
    }

    // ===================== Player Picker =====================
    var playerPickerState    = {};
    var playerPickerOriginal = new Set();
    var PLAYER_SAVE_URL = '<?= base_url('academy/training-group/player-save/' . esc($academy['id']) . '/' . esc($group['id'])) ?>';

    (function initPlayerPicker() {
        document.querySelectorAll('.picker-item').forEach(function(el) {
            var id      = parseInt(el.getAttribute('data-id'));
            var inGroup = el.getAttribute('data-in-group') === '1';
            playerPickerState[id] = inGroup;
            if (inGroup) playerPickerOriginal.add(id);
        });
        updatePlayerSummary();
    })();

    function togglePlayerItem(el) {
        var id = parseInt(el.getAttribute('data-id'));
        playerPickerState[id] = !playerPickerState[id];
        var card  = el.querySelector('.picker-card');
        var check = el.querySelector('.picker-check');
        if (playerPickerState[id]) { card.classList.add('selected');    check.style.display = ''; }
        else                       { card.classList.remove('selected'); check.style.display = 'none'; }
        updatePlayerSummary();
    }

    function updatePlayerSummary() {
        var sel = 0, add = 0, rem = 0;
        Object.keys(playerPickerState).forEach(function(k) {
            var id = parseInt(k);
            if (playerPickerState[id])  sel++;
            if (playerPickerState[id]  && !playerPickerOriginal.has(id)) add++;
            if (!playerPickerState[id] && playerPickerOriginal.has(id))  rem++;
        });
        var cntEl = document.getElementById('cntInGroup');
        var sumEl = document.getElementById('playerPickerSummary');
        if (cntEl) cntEl.textContent = sel;
        var parts = ['<?= lang("Common.training_group_in_group") ?>: ' + sel];
        if (add > 0) parts.push('+ ' + add + ' <?= lang("Common.person_unit") ?>');
        if (rem > 0) parts.push('- ' + rem + ' <?= lang("Common.person_unit") ?>');
        if (sumEl) sumEl.textContent = parts.join('  |  ');
    }

    var _playerFilter = 'all';
    function filterPlayerPicker(btn, type) {
        if (type !== 'search') {
            _playerFilter = type;
            document.querySelectorAll('.picker-filter-btn').forEach(function(b) {
                b.classList.remove('active','btn-primary','btn-outline-primary','btn-outline-secondary');
                var f = b.getAttribute('data-filter');
                b.classList.add(f === 'in' ? 'btn-outline-primary' : (f === 'out' ? 'btn-outline-secondary' : 'btn-outline-secondary'));
            });
            if (btn) {
                btn.classList.add('active');
                btn.classList.remove('btn-outline-primary','btn-outline-secondary');
                btn.classList.add('btn-primary');
            }
        }
        var q = (document.getElementById('playerPickerSearch').value || '').toLowerCase();
        document.querySelectorAll('.picker-item').forEach(function(el) {
            var inGroup = el.getAttribute('data-in-group') === '1';
            var name    = el.getAttribute('data-name') || '';
            var show    = true;
            if (_playerFilter === 'in')  show = inGroup;
            if (_playerFilter === 'out') show = !inGroup;
            if (q && name.indexOf(q) === -1) show = false;
            el.style.display = show ? '' : 'none';
        });
    }

    var btnSavePlayers = document.getElementById('btnSavePlayers');
    if (btnSavePlayers) {
        btnSavePlayers.addEventListener('click', function() {
            var add = [], rem = [];
            Object.keys(playerPickerState).forEach(function(k) {
                var id = parseInt(k);
                if (playerPickerState[id]  && !playerPickerOriginal.has(id)) add.push(id);
                if (!playerPickerState[id] && playerPickerOriginal.has(id))  rem.push(id);
            });
            if (add.length === 0 && rem.length === 0) {
                Swal.fire({ icon: 'info', title: '<?= lang("Common.no_changes") ?>', showConfirmButton: false, timer: 1300 });
                return;
            }
            var msg = [];
            if (add.length > 0) msg.push('<?= lang("Common.add") ?> ' + add.length + ' <?= lang("Common.person_unit") ?>');
            if (rem.length > 0) msg.push('<?= lang("Common.delete") ?> ' + rem.length + ' <?= lang("Common.person_unit") ?>');
            Swal.fire({
                title: '<?= lang("Common.confirm_save_data") ?>',
                text: msg.join(', '),
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#0d6efd',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<?= lang("Common.confirm") ?>',
                cancelButtonText: '<?= lang("Common.cancel") ?>'
            }).then(function(result) {
                if (!result.isConfirmed) return;
                doSavePlayers(add, rem, false);
            });
        });
    }

    function doSavePlayers(add, rem, force) {
        fetch(PLAYER_SAVE_URL, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ add: add, remove: rem, force: force })
        })
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status === 'success') {
                Swal.fire({ icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 })
                    .then(function() { location.reload(); });
            } else if (data.status === 'warning' && data.conflicts) {
                // แสดงชื่อนักเตะที่อยู่กลุ่มอื่นอยู่แล้ว
                var rows = data.conflicts.map(function(c) {
                    var name = c.player_name || '';
                    if (c.nick_name) name += ' (' + c.nick_name + ')';
                    return '<li class="text-start"><strong>' + name + '</strong><br>'
                         + '<span class="text-muted small"><i class="fas fa-layer-group me-1"></i>' + c.group_names + '</span></li>';
                });
                Swal.fire({
                    title: '<?= lang("Common.training_player_in_other_group_title") ?>',
                    html: '<p class="mb-2"><?= lang("Common.training_player_in_other_group_desc") ?></p>'
                        + '<ul class="list-unstyled mb-0 text-start" style="max-height:200px;overflow-y:auto;">' + rows.join('') + '</ul>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: '<i class="fas fa-check me-1"></i><?= lang("Common.training_confirm_add_anyway") ?>',
                    cancelButtonText: '<?= lang("Common.cancel") ?>',
                    reverseButtons: true
                }).then(function(result) {
                    if (result.isConfirmed) {
                        doSavePlayers(add, rem, true);
                    }
                });
            } else {
                Swal.fire({ icon: 'error', title: langError, text: data.message });
            }
        })
        .catch(function() {
            Swal.fire({ icon: 'error', title: langError, text: '<?= lang("Common.connection_error") ?>' });
        });
    }
    // ===================== /Player Picker =====================

    // Delete Group
    function deleteGroup() {
        Swal.fire({
            title: '<?= lang('Common.training_confirm_delete_group') ?>',
            text: '<?= lang('Common.training_delete_group_warning') ?>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang('Common.training_delete_group') ?>',
            cancelButtonText: langCancel,
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('<?= base_url('academy/training-group/delete') ?>', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                    },
                    body: JSON.stringify({
                        academy_id: <?= (int)$academy['id'] ?>,
                        group_id: <?= (int)($group['id'] ?? 0) ?>,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: langSuccess,
                            text: data.message,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.href = '<?= base_url('academy/training-groups/' . esc($academy['id'])) ?>';
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
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: langError,
                        text: langError,
                        confirmButtonColor: '#dc3545'
                    });
                });
            }
        });
    }
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')): ?>
    Swal.fire({
        icon: 'success',
        title: langSuccess,
        text: '<?= session()->getFlashdata('success') ?>',
        timer: 2500,
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
