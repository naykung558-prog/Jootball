<?php $this->extend('template/layout') ?>
<?php $encrypter = \Config\Services::encrypter(); ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <!-- Row 1: Title + Search/Filter -->
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
                <div>
                    <h4 class="mb-1 fw-bold" style="color:#198754;">
                        <i class="fas fa-chalkboard-teacher me-2"></i><?= lang('Common.coach_staff') ?>
                    </h4>
                    <?php if (!empty($academy['name'])): ?>
                        <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2 align-items-center">
                    <button type="button" class="btn btn-success btn-sm rounded-pill" style="min-width:150px;" data-bs-toggle="modal" data-bs-target="#inviteCoachModal">
                        <i class="fas fa-plus me-1"></i><?= lang('Common.add_coach') ?>
                    </button>
                    <input type="text" id="coachSearch" class="form-control form-control-sm rounded-pill"
                           style="max-width:200px;" placeholder="<?= lang('Common.search_name') ?>..."
                           oninput="filterCoaches()">
                    <select id="teamFilter" class="form-select form-select-sm rounded-pill" style="max-width:160px;" onchange="filterCoaches()">
                        <option value=""><?= lang('Common.all') ?></option>
                        <?php
                        // รวบรวม team options จากทุกทีมที่โค้ชแต่ละคนสังกัด (รองรับ multi-team)
                        $teamOptions = [];
                        foreach ($coaches as $c) {
                            // primary team
                            if (!empty($c['team_name']) && !isset($teamOptions[$c['team_id']])) {
                                $teamOptions[$c['team_id']] = $c['team_name'];
                            }
                        }
                        foreach ($teamOptions as $tid => $tname): ?>
                        <option value="<?= esc($tid) ?>"><?= esc($tname) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <!-- Row 2: Summary badges -->
            <?php if (!empty($coaches)):
                // นับจำนวนโค้ชต่อทีม (รองรับ multi-team)
                $byTeam = [];
                foreach ($coaches as $c) {
                    $names = !empty($c['all_team_names'])
                        ? array_filter(array_map('trim', explode(',', $c['all_team_names'])))
                        : (empty($c['team_name']) ? [] : [$c['team_name']]);
                    foreach ($names as $tname) {
                        $byTeam[$tname] = ($byTeam[$tname] ?? 0) + 1;
                    }
                }
            ?>
            <div class="d-flex gap-2 flex-wrap align-items-center">
                <span class="badge bg-success-subtle text-success rounded-pill px-3 py-2">
                    <i class="fas fa-chalkboard-teacher me-1"></i>
                    <?= lang('Common.coach_staff') ?>: <strong id="coachCount"><?= count($coaches) ?></strong> <?= lang('Common.persons') ?>
                </span>
                <?php foreach ($byTeam as $tname => $cnt): ?>
                <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                    <?= esc($tname) ?>: <?= $cnt ?>
                </span>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="card-body p-0">
            <?php if (empty($coaches)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fas fa-chalkboard-teacher fa-3x mb-3 opacity-25"></i>
                    <p class="mb-0"><?= lang('Common.academy_no_coaches') ?></p>
                </div>
            <?php else: ?>

            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0" id="coachTable">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3" style="width:40px;">#</th>
                            <th class="py-3"><?= lang('Common.full_name') ?></th>
                            <th class="py-3"><?= lang('Common.team') ?></th>
                            <th class="text-center py-3" style="">License</th>
                            <th class="text-center py-3" style="width:110px;"><?= lang('Common.status_label') ?></th>
                            <?php if (!empty($perms['perm_manage_members'])): ?>
                            <th class="text-center py-3 pe-4" style="width:80px;"><?= lang('Common.manage') ?></th>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody id="coachTbody">
                        <?php foreach ($coaches as $i => $c):
                            $imgSrc = !empty($c['image_path'])
                                ? base_url($c['image_path'])
                                : (!empty($c['line_picture'])
                                    ? $c['line_picture']
                                    : base_url('public/img/football-player.webp'));
                            $hasAccount  = !empty($c['coach_user_id']);
                            $teamName    = $c['team_name'] ?? null;
                            $teamId      = (int)($c['team_id'] ?? 0);
                            // รองรับ multi-team: team_ids_str = "1,2,3"
                            $teamIdsStr  = !empty($c['team_ids_str']) ? $c['team_ids_str'] : (string)$teamId;
                            $allTeamNames = !empty($c['all_team_names']) ? $c['all_team_names'] : $teamName;
                            $teamBadges  = $allTeamNames ? array_filter(array_map('trim', explode(',', $allTeamNames))) : [];
                            try {
                                $encId = bin2hex($encrypter->encrypt($c['id']));
                            } catch (\Exception $e) {
                                $encId = '';
                            }
                        ?>
                        <tr class="coach-row"
                            data-name="<?= esc(mb_strtolower(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '') . ' ' . ($c['nick_name'] ?? ''))) ?>"
                            data-teams="<?= esc($teamIdsStr) ?>"
                            data-team="<?= esc($teamId) ?>">
                            <td class="ps-4 text-muted" style="font-size:0.85rem;"><?= $i + 1 ?></td>
                            
                            <!-- Photo + Name -->
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $imgSrc ?>"
                                         class="rounded-circle border"
                                         style="width:44px;height:44px;object-fit:cover;flex-shrink:0;"
                                         onerror="this.src='<?= base_url('public/img/football-player.webp') ?>'">
                                    <div>
                                        <div class="fw-semibold text-dark" style="font-size:0.9rem;">
                                            <?= esc(($c['first_name'] ?? '') . ' ' . ($c['last_name'] ?? '')) ?>
                                        </div>
                                        <?php if (!empty($c['nick_name'])): ?>
                                        <div class="text-muted" style="font-size:0.78rem;"><?= esc($c['nick_name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>

                            <!-- Team (รองรับ multi-team badges) -->
                            <td>
                                <?php if (!empty($teamBadges)): ?>
                                <div class="d-flex flex-wrap gap-1">
                                    <?php foreach ($teamBadges as $badge): ?>
                                    <span class="badge rounded-pill px-2 py-1" style="background:rgba(25,135,84,0.12);color:#198754;font-size:0.75rem;">
                                        <i class="fas fa-shield-alt me-1"></i><?= esc($badge) ?>
                                    </span>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <span class="text-muted" style="font-size:0.78rem;">–</span>
                                <?php endif; ?>
                            </td>

                            <!-- Certification Level -->
                            <td class="text-center">
                                <?php if (!empty($c['cert_level_name'])): ?>
                                <span class="badge bg-primary rounded-pill px-2" style="font-size:0.72rem;">
                                    <i class="fas fa-certificate me-1"></i><?= esc($c['cert_level_name']) ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted" style="font-size:0.72rem;">-</span>
                                <?php endif; ?>
                            </td>

                            <!-- Account -->
                            <td class="text-center">
                                <?php if ($hasAccount): ?>
                                <span class="badge bg-success-subtle text-success rounded-pill px-2" style="font-size:0.72rem;">
                                    <i class="fas fa-circle me-1" style="font-size:0.45rem;vertical-align:middle;"></i><?= lang('Common.has_account') ?>
                                </span>
                                <?php else: ?>
                                <span class="badge bg-light text-muted border rounded-pill px-2" style="font-size:0.72rem;">
                                    <i class="fas fa-circle me-1" style="font-size:0.45rem;vertical-align:middle;"></i><?= lang('Common.no_account') ?>
                                </span>
                                <?php endif; ?>
                            </td>

                            <!-- Actions -->
                            <?php if (!empty($perms['perm_manage_members'])): ?>
                            <td class="text-center pe-4">
                                <?php if (!empty($encId)): ?>
                                <a href="<?= base_url('player/manage_coach/' . $encId) ?>"
                                   class="btn btn-outline-primary btn-sm rounded-pill px-3 text-nowrap"
                                   title="<?= lang('Common.edit_btn') ?>">
                                    <i class="fas fa-edit me-1"></i><?= lang('Common.edit_btn') ?>
                                </a>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- No results message -->
            <div id="noCoachResult" class="text-center py-4 text-muted d-none">
                <i class="fas fa-search fa-2x mb-2 opacity-25"></i>
                <p class="mb-0"><?= lang('Common.no_data_found') ?></p>
            </div>

            <?php endif; ?>
        </div>
    </div>

    <!-- Pending Invitations -->
    <?php if (!empty($pending_invitations)): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold" style="color:#f39c12;">
                <i class="fas fa-clock me-2"></i>คำเชิญที่รอการตอบรับ
                <span class="badge bg-warning-subtle text-warning rounded-pill ms-2"><?= count($pending_invitations) ?></span>
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light border-bottom">
                        <tr>
                            <th class="ps-4 py-3" style="width:40px;">#</th>
                            <th class="py-3">ชื่อ-นามสกุล</th>
                            <th class="py-3">ทีม</th>
                            <th class="text-center py-3">License</th>
                            <th class="text-center py-3">สถานะ</th>
                            <th class="text-center py-3" style="width:120px;">วันที่เชิญ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pending_invitations as $pi => $inv):
                            $invImg = !empty($inv['image_path'])
                                ? base_url($inv['image_path'])
                                : (!empty($inv['line_picture'])
                                    ? (filter_var($inv['line_picture'], FILTER_VALIDATE_URL) ? $inv['line_picture'] : base_url($inv['line_picture']))
                                    : base_url('public/img/football-player.webp'));
                        ?>
                        <tr>
                            <td class="ps-4 text-muted" style="font-size:0.85rem;"><?= $pi + 1 ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-3">
                                    <img src="<?= $invImg ?>" class="rounded-circle border"
                                         style="width:44px;height:44px;object-fit:cover;flex-shrink:0;"
                                         onerror="this.src='<?= base_url('public/img/football-player.webp') ?>'">
                                    <div>
                                        <div class="fw-semibold text-dark" style="font-size:0.9rem;">
                                            <?= esc(($inv['first_name'] ?? '') . ' ' . ($inv['last_name'] ?? '')) ?>
                                        </div>
                                        <?php if (!empty($inv['nick_name'])): ?>
                                        <div class="text-muted" style="font-size:0.78rem;"><?= esc($inv['nick_name']) ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge rounded-pill px-2 py-1" style="background:rgba(25,135,84,0.12);color:#198754;font-size:0.75rem;">
                                    <i class="fas fa-shield-alt me-1"></i><?= esc($inv['team_name'] ?? '-') ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($inv['cert_level_name'])): ?>
                                <span class="badge bg-primary rounded-pill px-2" style="font-size:0.72rem;">
                                    <i class="fas fa-certificate me-1"></i><?= esc($inv['cert_level_name']) ?>
                                </span>
                                <?php else: ?>
                                <span class="text-muted" style="font-size:0.72rem;">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-warning-subtle text-warning rounded-pill px-2" style="font-size:0.72rem;">
                                    <i class="fas fa-hourglass-half me-1"></i>รอการตอบรับ
                                </span>
                            </td>
                            <td class="text-center text-muted" style="font-size:0.78rem;">
                                <?= !empty($inv['created_at']) ? date('d/m/Y', strtotime($inv['created_at'])) : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Invite Coach Modal -->
<div class="modal fade" id="inviteCoachModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header bg-success text-white rounded-top-4 py-3">
                <h5 class="modal-title"><i class="fas fa-user-plus me-2"></i>ค้นหาและเชิญผู้ฝึกสอน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <!-- Search -->
                <div class="input-group mb-3">
                    <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" id="inviteCoachSearch" class="form-control border-start-0"
                           placeholder="พิมพ์ชื่อผู้ฝึกสอน (อย่างน้อย 2 ตัวอักษร)..." autocomplete="off">
                </div>

                <!-- Team select -->
                <?php if (!empty($available_teams) && count($available_teams) > 1): ?>
                <div class="mb-3">
                    <label class="form-label fw-semibold small text-muted">เลือกทีมที่จะเชิญเข้า</label>
                    <select id="inviteTeamSelect" class="form-select form-select-sm rounded-pill">
                        <?php foreach ($available_teams as $t): ?>
                        <option value="<?= $t['id'] ?>"><?= esc($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <?php elseif (!empty($available_teams)): ?>
                <input type="hidden" id="inviteTeamSelect" value="<?= $available_teams[0]['id'] ?>">
                <?php endif; ?>

                <!-- Results -->
                <div id="inviteSearchResults" class="mt-2" style="max-height:400px; overflow-y:auto;">
                    <div class="text-center text-muted py-4">
                        <i class="fas fa-search fa-2x mb-2 opacity-25"></i>
                        <p class="mb-0 small">พิมพ์ชื่อเพื่อค้นหาผู้ฝึกสอนในระบบ</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
var academyId = <?= (int)($academy['id'] ?? 0) ?>;
var searchTimer = null;

document.getElementById('inviteCoachSearch').addEventListener('input', function() {
    clearTimeout(searchTimer);
    var keyword = this.value.trim();
    if (keyword.length < 2) {
        document.getElementById('inviteSearchResults').innerHTML =
            '<div class="text-center text-muted py-4"><i class="fas fa-search fa-2x mb-2 opacity-25"></i><p class="mb-0 small">พิมพ์ชื่อเพื่อค้นหาผู้ฝึกสอนในระบบ</p></div>';
        return;
    }
    searchTimer = setTimeout(function() { searchCoaches(keyword); }, 400);
});

function searchCoaches(keyword) {
    var resultsEl = document.getElementById('inviteSearchResults');
    resultsEl.innerHTML = '<div class="text-center py-3"><div class="spinner-border spinner-border-sm text-success"></div> กำลังค้นหา...</div>';

    fetch('<?= base_url('academy/coach-search') ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: '<?= csrf_token() ?>=<?= csrf_hash() ?>&keyword=' + encodeURIComponent(keyword) + '&academy_id=' + academyId
    })
    .then(r => r.json())
    .then(res => {
        if (!res.data || res.data.length === 0) {
            resultsEl.innerHTML = '<div class="text-center text-muted py-4"><i class="fas fa-user-slash fa-2x mb-2 opacity-25"></i><p class="mb-0 small">ไม่พบผู้ฝึกสอนที่ตรงกับคำค้นหา</p></div>';
            return;
        }
        var html = '<div class="list-group list-group-flush">';
        res.data.forEach(function(c) {
            var name = (c.first_name || '') + ' ' + (c.last_name || '');
            var nick = c.nick_name ? ' (' + c.nick_name + ')' : '';
            html += '<div class="list-group-item d-flex align-items-center justify-content-between py-3 px-3">' +
                '<div class="d-flex align-items-center gap-3">' +
                    '<img src="' + c.image_url + '" class="rounded-circle border" style="width:44px;height:44px;object-fit:cover;" onerror="this.src=\'' + '<?= base_url("public/img/football-player.webp") ?>' + '\'">' +
                    '<div><div class="fw-semibold" style="font-size:0.9rem;">' + name + nick + '</div></div>' +
                '</div>' +
                '<button class="btn btn-success btn-sm rounded-pill px-3" onclick="inviteCoach(' + c.id + ', this)">' +
                    '<i class="fas fa-paper-plane me-1"></i>เชิญ' +
                '</button>' +
            '</div>';
        });
        html += '</div>';
        resultsEl.innerHTML = html;
    })
    .catch(function() {
        resultsEl.innerHTML = '<div class="text-center text-danger py-3">เกิดข้อผิดพลาด</div>';
    });
}

function inviteCoach(coachId, btn) {
    var teamEl = document.getElementById('inviteTeamSelect');
    var teamId = teamEl ? teamEl.value : '';
    if (!teamId) {
        Swal.fire({icon: 'warning', title: 'กรุณาเลือกทีม', timer: 2000, showConfirmButton: false});
        return;
    }
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch('<?= base_url('academy/coach-invite') ?>', {
        method: 'POST',
        headers: {'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest'},
        body: '<?= csrf_token() ?>=<?= csrf_hash() ?>&coach_id=' + coachId + '&team_id=' + teamId
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            btn.outerHTML = '<span class="badge bg-success rounded-pill px-3 py-2"><i class="fas fa-check me-1"></i>ส่งแล้ว</span>';
            Swal.fire({icon: 'success', title: res.message, timer: 1500, showConfirmButton: false}).then(function() {
                location.reload();
            });
        } else {
            btn.disabled = false;
            btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>เชิญ';
            Swal.fire({icon: 'error', title: res.message, timer: 2500, showConfirmButton: false});
        }
    })
    .catch(function() {
        btn.disabled = false;
        btn.innerHTML = '<i class="fas fa-paper-plane me-1"></i>เชิญ';
        Swal.fire({icon: 'error', title: 'เกิดข้อผิดพลาด', timer: 2000, showConfirmButton: false});
    });
}

function filterCoaches() {
    var q    = (document.getElementById('coachSearch').value || '').toLowerCase();
    var team = document.getElementById('teamFilter').value;
    var rows = document.querySelectorAll('.coach-row');
    var shown = 0;
    rows.forEach(function(row) {
        var name    = row.getAttribute('data-name') || '';
        var teamsStr = row.getAttribute('data-teams') || row.getAttribute('data-team') || '';
        var visible = true;
        if (q && name.indexOf(q) === -1) visible = false;
        // รองรับ multi-team: data-teams = "1,2,3"
        if (team) {
            var teamIds = teamsStr.split(',').map(function(t) { return t.trim(); });
            if (teamIds.indexOf(team) === -1) visible = false;
        }
        row.style.display = visible ? '' : 'none';
        if (visible) shown++;
    });
    var noResult = document.getElementById('noCoachResult');
    var countEl  = document.getElementById('coachCount');
    if (noResult) noResult.classList.toggle('d-none', shown > 0);
    if (countEl)  countEl.textContent = shown;
}
</script>
<?php $this->endSection(); ?>
