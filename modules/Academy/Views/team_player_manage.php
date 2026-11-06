<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-bold" style="color:#198754;">
                        <i class="fas fa-users-cog me-2"></i><?= lang('Common.team_player_manager') ?>
                    </h4>
                    <p class="text-muted small mb-0">
                        <?= esc($academy['name'] ?? '') ?>
                        <?php if (!empty($team['name'])): ?>
                            &mdash; <strong><?= esc($team['name']) ?></strong>
                        <?php endif; ?>
                    </p>
                </div>
                <a href="<?= base_url('academy/team-info/' . esc($academy['id']) . '/' . esc($team['id'])) ?>"
                   class="btn btn-outline-secondary rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                </a>
            </div>
        </div>

        <div class="card-body p-4">

            <!-- Filter bar -->
            <div class="d-flex gap-2 mb-3 flex-wrap align-items-center">
                <button type="button" class="btn btn-sm btn-success picker-filter-btn active" data-filter="all"
                        onclick="filterPicker(this,'all')"><?= lang('Common.all') ?></button>
                <button type="button" class="btn btn-sm btn-outline-success picker-filter-btn" data-filter="in"
                        onclick="filterPicker(this,'in')">
                    <i class="fas fa-check me-1"></i><?= lang('Common.team_player_in_team') ?>
                    <span class="badge bg-success ms-1" id="cntInTeam">0</span>
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary picker-filter-btn" data-filter="out"
                        onclick="filterPicker(this,'out')">
                    <?= lang('Common.team_player_other_team') ?>
                </button>
                <input type="text" id="pickerSearch" class="form-control form-control-sm ms-auto"
                       style="max-width:220px;" placeholder="<?= lang('Common.search_name') ?>..."
                       oninput="filterPicker(null,'search')">
            </div>

            <!-- Player picker grid -->
            <?php if (empty($allPlayers)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-users fa-3x mb-3 opacity-25"></i>
                <p><?= lang('Common.academy_no_players') ?></p>
            </div>
            <?php else: ?>
            <div class="row g-2" id="playerPickerGrid">
                <?php foreach ($allPlayers as $p):
                    $inTeam = ((int)$p['team_id'] === (int)$team['id']);
                    $imgSrc = !empty($p['image_path'])
                        ? base_url($p['image_path'])
                        : base_url('public/img/football-player.webp');
                    echo view('Modules\Academy\Views\components\player_picker_card', [
                        'p'             => $p,
                        'isSelected'    => $inTeam,
                        'imgSrc'        => $imgSrc,
                        'accentColor'   => 'success',
                        'dataKey'       => 'in-team',
                        'showTeamBadge' => !$inTeam,
                    ]);
                endforeach; ?>
            </div>

            <!-- Save bar -->
            <div class="mt-4 pt-3 border-top d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div class="text-muted small" id="pickerSummary"></div>
                <button type="button" class="btn btn-success rounded-pill px-5" id="btnSave">
                    <i class="fas fa-save me-2"></i><?= lang('Common.team_player_save_btn') ?>
                </button>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<style>
    .picker-card { border-color: #dee2e6 !important; }
    .picker-card.selected { border-color: #198754 !important; background: rgba(25,135,84,0.08); }
    .picker-card:hover { border-color: #198754 !important; box-shadow: 0 2px 8px rgba(25,135,84,0.2) !important; }
</style>
<script>
var pickerState    = {};
var pickerOriginal = new Set();
var SAVE_URL = '<?= base_url('academy/team-player/save/' . esc($academy['id']) . '/' . esc($team['id'])) ?>';

(function init() {
    document.querySelectorAll('.picker-item').forEach(function(el) {
        var id     = parseInt(el.getAttribute('data-id'));
        var inTeam = el.getAttribute('data-in-team') === '1';
        pickerState[id] = inTeam;
        if (inTeam) pickerOriginal.add(id);
    });
    updateSummary();
})();

function toggleItem(el) {
    var id = parseInt(el.getAttribute('data-id'));
    pickerState[id] = !pickerState[id];
    var card  = el.querySelector('.picker-card');
    var check = el.querySelector('.picker-check');
    if (pickerState[id]) { card.classList.add('selected');    check.style.display = ''; }
    else                 { card.classList.remove('selected'); check.style.display = 'none'; }
    updateSummary();
}

function updateSummary() {
    var sel = 0, add = 0, rem = 0;
    Object.keys(pickerState).forEach(function(k) {
        var id = parseInt(k);
        if (pickerState[id])  sel++;
        if (pickerState[id]  && !pickerOriginal.has(id)) add++;
        if (!pickerState[id] && pickerOriginal.has(id))  rem++;
    });
    document.getElementById('cntInTeam').textContent = sel;
    var parts = ['<?= lang("Common.team_player_in_team") ?>: ' + sel];
    if (add > 0) parts.push('+ ' + add + ' <?= lang("Common.person_unit") ?>');
    if (rem > 0) parts.push('- ' + rem + ' <?= lang("Common.person_unit") ?>');
    document.getElementById('pickerSummary').textContent = parts.join('  |  ');
}

var _filter = 'all';
function filterPicker(btn, type) {
    if (type !== 'search') {
        _filter = type;
        document.querySelectorAll('.picker-filter-btn').forEach(function(b) {
            b.classList.remove('active','btn-success','btn-outline-success','btn-outline-secondary');
            b.classList.add(b.getAttribute('data-filter') === 'in' ? 'btn-outline-success' : 'btn-outline-secondary');
        });
        if (btn) { btn.classList.add('active'); btn.classList.remove('btn-outline-success','btn-outline-secondary'); btn.classList.add('btn-success'); }
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

document.getElementById('btnSave').addEventListener('click', function() {
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
</script>
<?php $this->endSection(); ?>
