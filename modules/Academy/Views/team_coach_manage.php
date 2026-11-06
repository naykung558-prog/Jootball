<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\\Academy\\Views\\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-bold text-success">
                        <i class="fas fa-chalkboard-teacher me-2"></i><?= lang('Common.team_coach_manager') ?>
                    </h4>
                    <p class="text-muted small mb-0">
                        <?= esc($academy['name'] ?? '') ?>
                        <?php if (!empty($team['name'])): ?>
                            &mdash; <strong class="text-dark"><?= esc($team['name']) ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
                <a href="<?= base_url('academy/team-info/' . esc($academy['id']) . '/' . esc($team['id'])) ?>"
                   class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                </a>
            </div>
        </div>

        <div class="card-body p-4 pb-0">

            <!-- Filter Bar -->
            <div class="coach-picker-filter d-flex gap-2 mb-4 flex-wrap align-items-center">
                <button type="button" class="picker-chip active" data-filter="all" onclick="filterPicker(this,'all')">
                    <i class="fas fa-th-large me-1"></i><?= lang('Common.all') ?>
                    <span class="picker-chip__count" id="cntAll">0</span>
                </button>
                <button type="button" class="picker-chip" data-filter="in" onclick="filterPicker(this,'in')">
                    <i class="fas fa-check me-1"></i><?= lang('Common.team_coach_in_team') ?>
                    <span class="picker-chip__count picker-chip__count--success" id="cntInTeam">0</span>
                </button>
                <button type="button" class="picker-chip" data-filter="out" onclick="filterPicker(this,'out')">
                    <i class="fas fa-users me-1"></i><?= lang('Common.team_coach_other_team') ?>
                    <span class="picker-chip__count" id="cntOther">0</span>
                </button>

                <div class="ms-auto position-relative" style="max-width:280px;">
                    <i class="fas fa-search text-muted position-absolute" style="top:50%; left:14px; transform:translateY(-50%); font-size:0.85rem;"></i>
                    <input type="text" id="pickerSearch" class="form-control form-control-sm rounded-pill ps-5"
                           placeholder="<?= lang('Common.search_name') ?>..."
                           oninput="filterPicker(null,'search')"
                           style="height:38px;">
                </div>
            </div>

            <?php if (empty($allCoaches)): ?>
                <!-- Empty State -->
                <div class="text-center py-5 my-4">
                    <i class="fas fa-chalkboard-teacher fa-4x text-muted opacity-25 mb-3"></i>
                    <h5 class="text-muted fw-bold mb-2"><?= lang('Common.academy_no_coaches') ?></h5>
                    <p class="text-muted small"><?= lang('Common.academy_no_players') ?></p>
                </div>
            <?php else: ?>

                <!-- Coach Picker Grid -->
                <div class="row g-3 pb-5" id="coachPickerGrid">
                    <?php foreach ($allCoaches as $c):
                        $inTeam     = !empty($c['in_team']);
                        $isPending  = !empty($c['is_pending']);
                        $hasImage   = !empty($c['image_path']);
                        $imgSrc     = $hasImage ? base_url($c['image_path']) : '';
                        $firstName  = $c['first_name'] ?? '';
                        $lastName   = $c['last_name'] ?? '';
                        $nickName   = $c['nick_name'] ?? '';
                        $position   = $c['position'] ?? '';
                        $otherTeams = !empty($c['other_teams']) ? array_filter(array_map('trim', explode(',', $c['other_teams']))) : [];
                        $initials   = mb_substr(trim($firstName) !== '' ? trim($firstName) : '?', 0, 1, 'UTF-8');
                    ?>
                    <div class="col-6 col-sm-4 col-md-3 col-lg-2 picker-item"
                         data-id="<?= esc($c['id']) ?>"
                         data-in-team="<?= $inTeam ? '1' : '0' ?>"
                         data-name="<?= esc(mb_strtolower($firstName . ' ' . $lastName . ' ' . $nickName)) ?>"
                         onclick="toggleItem(this)">
                        <div class="coach-pick-card <?= $inTeam ? 'is-selected' : '' ?>">
                            <!-- Status indicator (top-left) -->
                            <span class="coach-pick-card__check <?= $inTeam ? '' : 'd-none' ?>">
                                <i class="fas fa-check"></i>
                            </span>

                            <!-- Position badge (top-right) -->
                            <?php if (!empty($position)): ?>
                                <span class="coach-pick-card__position"><?= esc($position) ?></span>
                            <?php endif; ?>

                            <!-- Avatar -->
                            <div class="coach-pick-card__avatar-wrap">
                                <?php if ($hasImage): ?>
                                    <img src="<?= $imgSrc ?>"
                                         alt="<?= esc($firstName) ?>"
                                         class="coach-pick-card__avatar"
                                         onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="coach-pick-card__avatar coach-pick-card__avatar--fallback" style="display:none;">
                                        <?= esc($initials) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="coach-pick-card__avatar coach-pick-card__avatar--fallback">
                                        <?= esc($initials) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Name -->
                            <div class="coach-pick-card__name">
                                <?= esc($firstName) ?> <?= esc($lastName) ?>
                            </div>
                            <?php if (!empty($nickName)): ?>
                                <div class="coach-pick-card__nickname">(<?= esc($nickName) ?>)</div>
                            <?php endif; ?>
                            <?php if ($isPending && !$inTeam): ?>
                                <div class="mt-1">
                                    <span class="badge bg-warning-subtle text-warning rounded-pill px-2" style="font-size:.68rem;">
                                        <i class="fas fa-clock me-1"></i><?= lang('Common.status_pending_approval') ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <!-- Other teams badges -->
                            <?php if (!empty($otherTeams)): ?>
                                <div class="coach-pick-card__teams">
                                    <?php foreach (array_slice($otherTeams, 0, 2) as $ot): ?>
                                        <span class="coach-pick-card__team-badge"><?= esc($ot) ?></span>
                                    <?php endforeach; ?>
                                    <?php if (count($otherTeams) > 2): ?>
                                        <span class="coach-pick-card__team-badge coach-pick-card__team-badge--more">+<?= count($otherTeams) - 2 ?></span>
                                    <?php endif; ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php endif; ?>
        </div>
    </div>
</div>

<?php if (!empty($allCoaches)): ?>
<!-- Sticky Save Bar -->
<div class="coach-picker-savebar">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 py-2">
            <div class="coach-picker-savebar__summary">
                <span class="coach-picker-savebar__label"><?= lang('Common.team_coach_in_team') ?>:</span>
                <strong class="coach-picker-savebar__count" id="summarySelected">0</strong>
                <span class="coach-picker-savebar__diff text-success ms-3 d-none" id="summaryAdd">
                    <i class="fas fa-plus-circle"></i> <span></span> <?= lang('Common.person_unit') ?>
                </span>
                <span class="coach-picker-savebar__diff text-danger ms-2 d-none" id="summaryRemove">
                    <i class="fas fa-minus-circle"></i> <span></span> <?= lang('Common.person_unit') ?>
                </span>
            </div>
            <button type="button" class="btn btn-success rounded-pill px-5" id="btnSave">
                <i class="fas fa-save me-2"></i><?= lang('Common.team_player_save_btn') ?>
            </button>
        </div>
    </div>
</div>
<?php endif; ?>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<style>
    /* === Filter Chips === */
    .picker-chip {
        background: #fff;
        border: 1.5px solid #DFE5EF;
        color: #5A6A85;
        font-size: 0.85rem;
        font-weight: 500;
        padding: 6px 16px;
        border-radius: 999px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        transition: all 0.15s ease;
        cursor: pointer;
    }
    .picker-chip:hover {
        border-color: #198754;
        color: #198754;
    }
    .picker-chip.active {
        background: #198754;
        border-color: #198754;
        color: #fff;
    }
    .picker-chip__count {
        background: rgba(0,0,0,0.08);
        color: inherit;
        font-size: 0.72rem;
        font-weight: 700;
        padding: 1px 8px;
        border-radius: 999px;
        margin-left: 4px;
        min-width: 22px;
        text-align: center;
    }
    .picker-chip.active .picker-chip__count {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }
    .picker-chip__count--success {
        background: rgba(25, 135, 84, 0.15);
        color: #198754;
    }
    .picker-chip.active .picker-chip__count--success {
        background: rgba(255,255,255,0.25);
        color: #fff;
    }

    /* === Coach Picker Card === */
    .coach-pick-card {
        position: relative;
        background: #fff;
        border: 2px solid #EAEFF4;
        border-radius: 1rem;
        padding: 16px 12px 12px;
        text-align: center;
        cursor: pointer;
        user-select: none;
        transition: transform 0.15s ease, box-shadow 0.15s ease, border-color 0.15s ease;
        height: 100%;
    }
    .coach-pick-card:hover {
        border-color: #198754;
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(25, 135, 84, 0.12);
    }
    .coach-pick-card.is-selected {
        border-color: #198754;
        background: #f0fbf6;
        box-shadow: 0 4px 12px rgba(25, 135, 84, 0.15);
    }

    .coach-pick-card__check {
        position: absolute;
        top: 8px;
        left: 8px;
        width: 26px;
        height: 26px;
        border-radius: 50%;
        background: #198754;
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.7rem;
        box-shadow: 0 2px 6px rgba(25, 135, 84, 0.35);
        z-index: 2;
    }

    .coach-pick-card__position {
        position: absolute;
        top: 8px;
        right: 8px;
        background: #2A3547;
        color: #fff;
        font-size: 0.62rem;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 999px;
        z-index: 2;
    }

    .coach-pick-card__avatar-wrap {
        display: flex;
        justify-content: center;
        margin-bottom: 8px;
    }
    .coach-pick-card__avatar {
        width: 64px;
        height: 64px;
        border-radius: 50%;
        object-fit: cover;
        border: 3px solid #fff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        background: #fff;
    }
    .coach-pick-card.is-selected .coach-pick-card__avatar {
        box-shadow: 0 2px 8px rgba(25, 135, 84, 0.25);
    }
    .coach-pick-card__avatar--fallback {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.6rem;
        font-weight: 700;
        color: #fff;
        background: #198754;
        text-transform: uppercase;
    }

    .coach-pick-card__name {
        font-weight: 700;
        font-size: 0.85rem;
        color: #2A3547;
        line-height: 1.25;
        margin-bottom: 2px;
        word-break: break-word;
    }
    .coach-pick-card__nickname {
        font-size: 0.72rem;
        color: #7C8FAC;
        line-height: 1.2;
        margin-bottom: 6px;
    }

    .coach-pick-card__teams {
        display: flex;
        flex-wrap: wrap;
        gap: 3px;
        justify-content: center;
        margin-top: 6px;
    }
    .coach-pick-card__team-badge {
        background: #F6F9FC;
        color: #5A6A85;
        border: 1px solid #EAEFF4;
        font-size: 0.6rem;
        font-weight: 500;
        padding: 2px 7px;
        border-radius: 999px;
        max-width: 100%;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .coach-pick-card__team-badge--more {
        background: #198754;
        color: #fff;
        border-color: #198754;
    }

    /* === Sticky Save Bar === */
    .coach-picker-savebar {
        position: sticky;
        bottom: 0;
        left: 0;
        right: 0;
        background: #fff;
        border-top: 1px solid #EAEFF4;
        box-shadow: 0 -4px 16px rgba(0,0,0,0.06);
        padding: 8px 24px;
        z-index: 100;
        margin-top: -1rem;
    }
    .coach-picker-savebar__label {
        color: #5A6A85;
        font-size: 0.9rem;
    }
    .coach-picker-savebar__count {
        font-size: 1.25rem;
        color: #198754;
    }
    .coach-picker-savebar__diff {
        font-size: 0.85rem;
        font-weight: 600;
    }

    /* Empty filter state */
    .picker-empty-state {
        padding: 3rem 1rem;
        text-align: center;
        color: #7C8FAC;
        grid-column: 1 / -1;
    }
</style>
<script>
var pickerState    = {};
var pickerOriginal = new Set();
var SAVE_URL = '<?= base_url('academy/team-coach/save/' . esc($academy['id']) . '/' . esc($team['id'])) ?>';

(function init() {
    document.querySelectorAll('.picker-item').forEach(function(el) {
        var id     = parseInt(el.getAttribute('data-id'));
        var inTeam = el.getAttribute('data-in-team') === '1';
        pickerState[id] = inTeam;
        if (inTeam) pickerOriginal.add(id);
    });
    updateCounts();
    updateSummary();
})();

function toggleItem(el) {
    var id = parseInt(el.getAttribute('data-id'));
    pickerState[id] = !pickerState[id];
    var card  = el.querySelector('.coach-pick-card');
    var check = el.querySelector('.coach-pick-card__check');
    if (pickerState[id]) {
        card.classList.add('is-selected');
        check.classList.remove('d-none');
    } else {
        card.classList.remove('is-selected');
        check.classList.add('d-none');
    }
    updateSummary();
}

function updateCounts() {
    var total = 0, inTeam = 0, other = 0;
    document.querySelectorAll('.picker-item').forEach(function(el) {
        total++;
        if (el.getAttribute('data-in-team') === '1') inTeam++;
        else other++;
    });
    var elAll = document.getElementById('cntAll');
    var elIn  = document.getElementById('cntInTeam');
    var elOut = document.getElementById('cntOther');
    if (elAll) elAll.textContent = total;
    if (elIn)  elIn.textContent  = inTeam;
    if (elOut) elOut.textContent = other;
}

function updateSummary() {
    var sel = 0, add = 0, rem = 0;
    Object.keys(pickerState).forEach(function(k) {
        var id = parseInt(k);
        if (pickerState[id])  sel++;
        if (pickerState[id]  && !pickerOriginal.has(id)) add++;
        if (!pickerState[id] && pickerOriginal.has(id))  rem++;
    });
    var selEl = document.getElementById('summarySelected');
    if (selEl) selEl.textContent = sel;

    var addEl = document.getElementById('summaryAdd');
    var remEl = document.getElementById('summaryRemove');
    if (addEl) {
        if (add > 0) {
            addEl.classList.remove('d-none');
            addEl.querySelector('span').textContent = add;
        } else {
            addEl.classList.add('d-none');
        }
    }
    if (remEl) {
        if (rem > 0) {
            remEl.classList.remove('d-none');
            remEl.querySelector('span').textContent = rem;
        } else {
            remEl.classList.add('d-none');
        }
    }
}

var _filter = 'all';
function filterPicker(btn, type) {
    if (type !== 'search') {
        _filter = type;
        document.querySelectorAll('.picker-chip').forEach(function(b) { b.classList.remove('active'); });
        if (btn) btn.classList.add('active');
    }
    var q = (document.getElementById('pickerSearch').value || '').toLowerCase();
    document.querySelectorAll('.picker-item').forEach(function(el) {
        var inTeam = el.getAttribute('data-in-team') === '1';
        var name   = el.getAttribute('data-name') || '';
        var show   = true;
        if (_filter === 'in')  show = inTeam;
        if (_filter === 'out') show = !inTeam;
        if (q && name.indexOf(q) === -1) show = false;
        el.style.display = show ? '' : 'none';
    });
}

var btnSave = document.getElementById('btnSave');
if (btnSave) {
    btnSave.addEventListener('click', function() {
        var add = [], rem = [];
        Object.keys(pickerState).forEach(function(k) {
            var id = parseInt(k);
            if (pickerState[id]  && !pickerOriginal.has(id)) add.push(id);
            if (!pickerState[id] && pickerOriginal.has(id))  rem.push(id);
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
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang("Common.confirm") ?>',
            cancelButtonText: '<?= lang("Common.cancel") ?>'
        }).then(function(result) {
            if (!result.isConfirmed) return;
            fetch(SAVE_URL, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ add: add, remove: rem })
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    Swal.fire({ icon: 'success', title: data.message, showConfirmButton: false, timer: 1500 })
                        .then(function() { location.reload(); });
                } else {
                    Swal.fire({ icon: 'error', title: '<?= lang("Common.error") ?>', text: data.message });
                }
            })
            .catch(function() {
                Swal.fire({ icon: 'error', title: '<?= lang("Common.error") ?>', text: '<?= lang("Common.connection_error") ?>' });
            });
        });
    });
}
</script>
<?php $this->endSection(); ?>
