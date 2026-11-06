<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center">
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-layer-group me-2"></i><?= esc($title ?? lang('Common.idp_create_group_plan')) ?>
                </h4>
                <a href="<?= base_url('academy/idp/' . esc($academy['id'])) ?>"
                   class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                </a>
            </div>
            <?php if (!empty($academy['name'])): ?>
                <p class="text-muted small mb-0 mt-1"><?= esc($academy['name']) ?></p>
            <?php endif; ?>
        </div>

        <div class="card-body p-4">
            <form action="<?= base_url('academy/idp/group/save') ?>" method="POST" id="groupForm" autocomplete="off">
                <input type="hidden" name="academy_id" value="<?= esc($academy['id'] ?? '') ?>">
                <?php $ed = isset($editData) ? $editData : null; ?>
                <?php if ($ed && !empty($ed['id'])): ?>
                    <input type="hidden" name="group_plan_id" value="<?= esc($ed['id']) ?>">
                <?php endif; ?>

                <div class="row justify-content-center">
                    <!-- Group Plan Info Section -->
                    <div class="col-md-10">
                        <div class="card border rounded-3 mb-4">
                            <div class="card-header bg-light py-2 px-3">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-info-circle me-1 text-primary"></i> <?= lang('Common.idp_group_plan_name') ?></h6>
                            </div>
                            <div class="card-body p-3">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label"><?= lang('Common.idp_group_plan_name') ?> <span class="text-danger">*</span></label>
                                        <input type="text" name="name" class="form-control" required
                                               placeholder="<?= lang('Common.idp_group_plan_name_placeholder') ?>"
                                               value="<?= esc($ed ? ($ed['name'] ?? ($ed['title'] ?? '')) : '') ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label"><?= lang('Common.idp_select_template') ?> <span class="text-danger">*</span></label>
                                        <select name="template_id" id="templateSelect" class="form-select" required>
                                            <?php if (!empty($templates)): ?>
                                                <?php foreach ($templates as $idx => $tpl): ?>
                                                <option value="<?= esc($tpl['id']) ?>"
                                                    <?= ($ed && isset($ed['template_id']) && $ed['template_id'] == $tpl['id']) ? 'selected' : ((!$ed && $idx === 0) ? 'selected' : '') ?>>
                                                    <?= esc($tpl['title']) ?>
                                                    <?php if ($tpl['owner_type'] === 'global'): ?> (<?= lang('Common.idp_global_badge') ?>)<?php endif; ?>
                                                </option>
                                                <?php endforeach; ?>
                                            <?php else: ?>
                                                <option value=""><?= lang('Common.no_data') ?></option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label"><?= lang('Common.idp_select_coach') ?></label>
                                        <select name="coach_id" id="coachSelect" class="form-select">
                                            <option value=""><?= lang('Common.idp_select_coach') ?></option>
                                            <?php if (!empty($coaches)): ?>
                                                <?php foreach ($coaches as $coach): ?>
                                                <option value="<?= esc($coach['id']) ?>"
                                                    <?= ($ed && isset($ed['coach_id']) && (int)$ed['coach_id'] === (int)$coach['id']) ? 'selected' : '' ?>>
                                                    <?= esc($coach['name'] ?? ($coach['first_name'] . ' ' . $coach['last_name'])) ?>
                                                </option>
                                                <?php endforeach; ?>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label"><?= lang('Common.idp_plan_season') ?></label>
                                        <input type="text" name="season" class="form-control" placeholder="2025/26"
                                               value="<?= esc($ed ? ($ed['season'] ?? '') : '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label"><?= lang('Common.idp_plan_start_date') ?></label>
                                        <input type="date" name="start_date" class="form-control"
                                               value="<?= esc($ed ? ($ed['start_date'] ?? date('Y-m-d')) : date('Y-m-d')) ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label"><?= lang('Common.idp_plan_end_date') ?></label>
                                        <input type="date" name="end_date" class="form-control"
                                               value="<?= esc($ed ? ($ed['end_date'] ?? '') : '') ?>">
                                    </div>
                                    <div class="col-md-2">
                                        <label class="form-label"><?= lang('Common.idp_plan_status') ?></label>
                                        <select name="status" class="form-select">
                                            <option value="active" <?= ($ed && isset($ed['status']) && $ed['status'] === 'active') ? 'selected' : '' ?>><?= lang('Common.idp_status_active') ?></option>
                                            <option value="draft" <?= ($ed && isset($ed['status']) && $ed['status'] === 'draft') ? 'selected' : '' ?>><?= lang('Common.idp_status_draft') ?></option>
                                            <option value="completed" <?= ($ed && isset($ed['status']) && $ed['status'] === 'completed') ? 'selected' : '' ?>><?= lang('Common.idp_status_completed') ?></option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Select Players Section -->
                    <div class="col-md-10">
                        <div class="card border rounded-3">
                            <div class="card-header bg-light py-2 px-3 d-flex justify-content-between align-items-center">
                                <h6 class="mb-0 fw-bold"><i class="fas fa-users me-1 text-success"></i> <?= lang('Common.idp_select_players') ?></h6>
                                <div class="d-flex align-items-center gap-2">
                                    <!-- Training group helper dropdown -->
                                    <select id="groupHelper" class="form-select form-select-sm" style="width: auto; min-width: 200px;">
                                        <option value=""><?= lang('Common.idp_select_players_help') ?></option>
                                        <option value="0"><?= lang('Common.idp_all_players') ?></option>
                                        <?php if (!empty($groups)): ?>
                                            <?php foreach ($groups as $group): ?>
                                            <option value="<?= esc($group['id']) ?>">
                                                <?= esc($group['name']) ?>
                                                <?php if (!empty($group['age_range'])): ?> (<?= esc($group['age_range']) ?>)<?php endif; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </select>
                                    <button type="button" class="btn btn-sm btn-outline-primary" id="btnSelectAll"><?= lang('Common.idp_select_all') ?></button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" id="btnDeselectAll"><?= lang('Common.idp_deselect_all') ?></button>
                                </div>
                            </div>
                            <div class="card-body p-3" id="playerListContainer">
                                <div class="text-center text-muted py-4" id="playerPlaceholder">
                                    <i class="fas fa-hand-pointer fa-2x mb-2 opacity-25"></i>
                                    <p><?= lang('Common.idp_select_players_help') ?></p>
                                </div>
                                <div class="row g-2" id="playerCheckboxes" style="display: none;"></div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="col-md-10 text-center pt-4">
                        <a href="<?= base_url('academy/idp/' . esc($academy['id'])) ?>"
                           class="btn btn-light rounded-pill px-4 me-2">
                            <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                        </a>
                        <button type="button" class="btn btn-primary rounded-pill px-4" id="btnSubmit">
                            <i class="fas fa-save me-1"></i><?= lang('Common.save') ?>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
var academyId = '<?= esc($academy['id']) ?>';
var editPlayerIds = <?= (!empty($editData['player_ids'])) ? json_encode(array_map('intval', $editData['player_ids'])) : '[]' ?>;

// When training group helper changes, load players via AJAX
document.getElementById('groupHelper').addEventListener('change', function() {
    var groupId = this.value;
    var container = document.getElementById('playerCheckboxes');
    var placeholder = document.getElementById('playerPlaceholder');

    if (!groupId) {
        container.style.display = 'none';
        placeholder.style.display = 'block';
        return;
    }

    // Fetch players
    var fetchUrl = '<?= base_url('academy/idp/group/players') ?>/' + groupId;
    if (groupId == '0') {
        fetchUrl += '?academy_id=' + academyId;
    }
    fetch(fetchUrl)
        .then(function(r) { return r.json(); })
        .then(function(data) {
            if (data.status !== 'success') {
                container.innerHTML = '<div class="text-danger text-center py-3">' + (data.message || 'Error') + '</div>';
                container.style.display = 'block';
                placeholder.style.display = 'none';
                return;
            }

            // Auto-select coach from group if empty
            if (data.coaches && data.coaches.length > 0) {
                var coachSelect = document.getElementById('coachSelect');
                if (!coachSelect.value) {
                    coachSelect.value = data.coaches[0].coach_id || data.coaches[0].id || '';
                }
            }

            // Build player checkboxes
            var html = '';
            if (!data.players || data.players.length === 0) {
                html = '<div class="col-12 text-center text-muted py-3"><i class="fas fa-user-slash me-2"></i><?= lang('Common.no_data') ?></div>';
            } else {
                for (var i = 0; i < data.players.length; i++) {
                    var p = data.players[i];
                    var fullName = (p.first_name || '') + ' ' + (p.last_name || '');
                    var nameEnc = encodeURIComponent((p.first_name || '') + '+' + (p.last_name || ''));
                    var imgSrc = p.image_path
                        ? '<?= base_url() ?>/' + p.image_path
                        : 'https://ui-avatars.com/api/?name=' + nameEnc + '&size=80&background=0d6efd&color=fff&bold=true&format=svg';
                    var hasActivePlan = !!p.active_plan_id;
                    var badgeHtml = '';
                    if (hasActivePlan) {
                        badgeHtml = '<span class="badge bg-warning text-dark ms-1" style="font-size: 0.65rem;"><i class="fas fa-exclamation-circle me-1"></i><?= lang('Common.idp_player_has_active_plan') ?></span>';
                    }

                    html += '<div class="col-12 col-md-6 col-lg-4">'
                        + '<label class="d-flex align-items-center p-2 border rounded-3 hover-shadow' + (hasActivePlan ? ' bg-light' : '') + '" style="cursor:pointer;">'
                        + '<input type="checkbox" name="player_ids[]" value="' + p.id + '" class="form-check-input me-2 player-cb"'
                        + (hasActivePlan ? ' data-has-plan="1"' : '') + '>'
                        + '<img src="' + imgSrc + '" class="rounded-circle me-2" style="width:35px;height:35px;object-fit:cover;" onerror="this.src=\'https://ui-avatars.com/api/?name=' + nameEnc + '&size=80&background=6c757d&color=fff&format=svg\'">'
                        + '<div class="flex-grow-1">'
                        + '<span class="fw-bold">';
                    if (p.shirt_number) {
                        html += '<span class="badge bg-dark me-1" style="font-size:0.65rem;">#' + p.shirt_number + '</span>';
                    }
                    html += fullName + '</span>'
                        + badgeHtml;
                    if (p.position_name) {
                        html += '<br><small class="text-muted">' + p.position_name + '</small>';
                    }
                    html += '</div></label></div>';
                }
            }
            container.innerHTML = html;
            container.style.display = 'flex';
            placeholder.style.display = 'none';

            // In edit mode, pre-check existing players
            if (editPlayerIds.length > 0) {
                document.querySelectorAll('.player-cb').forEach(function(cb) {
                    if (editPlayerIds.indexOf(parseInt(cb.value)) !== -1) {
                        cb.checked = true;
                    }
                });
            }
        })
        .catch(function(err) {
            container.innerHTML = '<div class="text-danger text-center py-3">Error loading players</div>';
            container.style.display = 'block';
            placeholder.style.display = 'none';
        });
});

// Select all / Deselect all
document.getElementById('btnSelectAll').addEventListener('click', function() {
    document.querySelectorAll('.player-cb').forEach(function(cb) { cb.checked = true; });
});
document.getElementById('btnDeselectAll').addEventListener('click', function() {
    document.querySelectorAll('.player-cb').forEach(function(cb) { cb.checked = false; });
});

// Submit with confirmation
document.getElementById('btnSubmit').addEventListener('click', function() {
    var checkedCount = document.querySelectorAll('.player-cb:checked').length;
    if (checkedCount === 0) {
        Swal.fire({ icon: 'warning', title: '<?= lang('Common.idp_select_players') ?>', text: '<?= lang('Common.fill_required') ?>' });
        return;
    }

    var nameField = document.querySelector('input[name="name"]');
    if (!nameField.value.trim()) {
        Swal.fire({ icon: 'warning', title: '<?= lang('Common.idp_group_plan_name') ?>', text: '<?= lang('Common.fill_required') ?>' });
        nameField.focus();
        return;
    }

    Swal.fire({
        title: "<?= lang('Common.confirm_save_data') ?>",
        text: checkedCount + " <?= lang('Common.players') ?>",
        icon: "question",
        showCancelButton: true,
        confirmButtonColor: "#16478d",
        cancelButtonColor: "#d33",
        confirmButtonText: "<?= lang('Common.confirm') ?>",
        cancelButtonText: "<?= lang('Common.cancel') ?>"
    }).then(function(result) {
        if (result.isConfirmed) {
            document.getElementById('groupForm').submit();
        }
    });
});

// Edit mode: auto-load players on page load
<?php if (!empty($editData)): ?>
(function() {
    var groupHelper = document.getElementById('groupHelper');
    // In edit mode, default to show all players so we can pre-select them
    groupHelper.value = '0';
    groupHelper.dispatchEvent(new Event('change'));
})();
<?php endif; ?>
</script>
<?php $this->endSection(); ?>
