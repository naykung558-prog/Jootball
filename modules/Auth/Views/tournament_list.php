<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4 bg-primary text-white overflow-hidden position-relative">
                <div class="card-body p-5 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 ">
                        <div>
                            <h2 class="fw-bold mb-1 text-white"><?= lang('Common.football_tournament') ?></h2>
                            <p class="mb-0 opacity-75">เลือกรายการแข่งขันที่คุณต้องการเข้าร่วม</p>
                        </div>
                        <?php if (!empty($user['profile'])): ?>
                            <div class="text-end d-none d-md-block">
                                <small class="d-block opacity-75"><?= lang('Common.welcome_user') ?></small>
                                <h5 class="fw-bold mb-0 text-white"><?= esc($user['profile']->name ?? lang('Common.username')) ?></h5>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                <!-- Decorative background elements -->
                 <div class="position-absolute top-0 end-0 opacity-10" style="transform: translate(30%, -30%);">
                    <i class="ti ti-trophy" style="font-size: 15rem; color: #f2b818;"></i>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Search Bar -->
        <div class="col-md-12 mb-4">
            <form action="<?= base_url('auth/tournament/list') ?>" method="get" id="searchForm">
                <div class="input-group shadow-sm rounded-pill overflow-hidden bg-white">
                    <span class="input-group-text bg-white border-0 ps-4"><i class="ti ti-search text-muted"></i></span>
                    <input type="text" name="search" id="searchInput" class="form-control border-0 py-3" placeholder="<?= lang('Common.search_tournament') ?>" value="<?= esc($search_keyword ?? '') ?>">
                    <button class="btn btn-primary px-4 fw-bold" type="submit">ค้นหา</button>
                </div>
            </form>
        </div>

        <!-- Academy Team Selector (สำหรับ user ที่มี academy teams) -->
        <?php if (!empty($academyTeams)): ?>
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center gap-2">
                    <i class="ti ti-shield-check text-success fs-5"></i>
                    <span class="fw-bold text-dark">เลือกทีมที่ต้องการสมัครแข่งขัน</span>
                    <span class="ms-auto text-muted small" id="selectedTeamLabel">ยังไม่ได้เลือกทีม</span>
                </div>
                <div class="card-body px-4 pb-4 pt-2">
                    <div class="d-flex gap-3 flex-wrap" id="teamPickerRow">
                        <?php foreach ($academyTeams as $t):
                            $tLogoUrl = !empty($t['logo'])
                                ? (filter_var($t['logo'], FILTER_VALIDATE_URL) ? $t['logo'] : base_url($t['logo']))
                                : base_url('public/img/jootball_logo.png');
                        ?>
                        <div class="team-picker-item d-flex flex-column align-items-center gap-1"
                             data-team-id="<?= esc($t['id']) ?>"
                             data-team-name="<?= esc($t['name'] ?? $t['name_en'] ?? '') ?>"
                             onclick="selectAcademyTeam(this)"
                             style="cursor:pointer; min-width:80px; max-width:100px;">
                            <div class="team-picker-avatar rounded-circle border border-3 border-light overflow-hidden shadow-sm"
                                 style="width:62px;height:62px;transition:all .2s;">
                                <img src="<?= $tLogoUrl ?>"
                                     style="width:100%;height:100%;object-fit:cover;"
                                     onerror="this.src='<?= base_url('public/img/jootball_logo.png') ?>'">
                            </div>
                            <div class="text-center" style="font-size:0.72rem;font-weight:600;line-height:1.2;word-break:break-word;">
                                <?= esc($t['name'] ?? $t['name_en'] ?? '') ?>
                            </div>
                            <?php if (!empty($t['academy_name'])): ?>
                            <div class="text-muted text-center" style="font-size:0.62rem;line-height:1.1;">
                                <?= esc($t['academy_name']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Tournament Filter Tabs -->
        <?php if ((!empty($user['user_id']) && !empty($user['team'])) || session()->get('user_type') == 'coach' || !empty($academyTeams)): ?>
            <div class="col-md-12 mb-4">
                <ul class="nav nav-pills nav-fill bg-white p-2 rounded-4 shadow-sm" id="tournamentTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill fw-bold py-2" id="all-tab" data-bs-toggle="tab" data-bs-target="#all-tournaments" type="button" role="tab" aria-controls="all-tournaments" aria-selected="true">
                            <i class="ti ti-list me-2"></i><?= lang('Common.all_tournaments') ?? 'รายการแข่งขันทั้งหมด' ?>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill fw-bold py-2" id="joined-tab" data-bs-toggle="tab" data-bs-target="#joined-tournaments" type="button" role="tab" aria-controls="joined-tournaments" aria-selected="false">
                            <i class="ti ti-check-circle me-2"></i><?= lang('Common.joined_tournaments') ?? 'รายการที่เข้าร่วม' ?>
                        </button>
                    </li>
                </ul>
            </div>
        <?php endif; ?>

        <!-- Tab Content -->
        <div class="col-md-12">
            <div class="tab-content" id="tournamentTabsContent">
                <!-- All Tournaments Tab -->
                <div class="tab-pane fade show active" id="all-tournaments" role="tabpanel" aria-labelledby="all-tab">
                    <?php if (!empty($tournaments)): ?>
                        <div class="row g-3">
                            <?php foreach ($tournaments as $tournament):
                                $encId = bin2hex($encrypter->encrypt($tournament['tour_id']));
                                $detailUrl = base_url('auth/tournament/detail/' . $encId);
                            ?>
                                <div class="col-6 col-md-4 col-lg-3 col-xl-2-4">
                                    <div class="card shadow-sm border-0 h-100 rounded-3 transition-hover tournament-card"
                                         data-tour-id="<?= esc($tournament['tour_id']) ?>"
                                         data-enc-id="<?= esc($encId) ?>"
                                         data-detail-url="<?= esc($detailUrl) ?>"
                                         data-register-status="<?= isset($tournament['register_status']) ? (int)$tournament['register_status'] : 'null' ?>"
                                         style="cursor:pointer;"
                                         onclick="window.location.href='<?= $detailUrl ?>'">
                                        <div class="position-relative">
                                            <?php if (file_exists('./' . $tournament['logo']) && !empty($tournament['logo'])): ?>
                                                <img src="<?= base_url($tournament['logo']) ?>" class="card-img-top rounded-top-3" alt="<?= esc($tournament['name']) ?>" style="height: 140px; object-fit: contain;">
                                            <?php else: ?>
                                                <div class="bg-light rounded-top-3 d-flex align-items-center justify-content-center" style="height: 140px;">
                                                    <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="Default Logo" style="max-height: 70px; opacity: 0.5;">
                                                </div>
                                            <?php endif; ?>
                                            <div class="position-absolute top-0 end-0 m-2">
                                                <span class="badge bg-white text-primary shadow-sm rounded-pill px-2 py-1 small">
                                                    <?= esc($tournament['generation']) ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="card-body d-flex flex-column p-3">
                                            <h6 class="card-title fw-bold text-dark mb-2 line-clamp-2" style="min-height: 2.5rem;"><?= esc($tournament['name']) ?></h6>
                                            <p class="card-text text-muted small flex-grow-1 mb-2 card-detail-scroll" style="font-size: 0.8rem;">
                                                <?= esc($tournament['detail']) ?>
                                            </p>

                                            <!-- Tournament Details -->
                                            <div class="mb-3">
                                                <?php if (!empty($tournament['start_date']) && !empty($tournament['end_date'])): ?>
                                                    <div class="d-flex align-items-center text-muted mb-1" style="font-size: 0.75rem;">
                                                        <i class="ti ti-calendar-event me-1 text-warning" style="font-size: 0.9rem;"></i>
                                                        <span class="fw-bold me-1">รับสมัคร:</span>
                                                        <span>
                                                            <?php
                                                                $start = new DateTime($tournament['start_date']);
                                                                $end = new DateTime($tournament['end_date']);
                                                                echo $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
                                                            ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>

                                                <?php if (!empty($tournament['tournament_start_date']) && !empty($tournament['tournament_end_date'])): ?>
                                                    <div class="d-flex align-items-center text-muted mb-1" style="font-size: 0.75rem;">
                                                        <i class="ti ti-trophy me-1 text-success" style="font-size: 0.9rem;"></i>
                                                        <span class="fw-bold me-1">แข่งขัน:</span>
                                                        <span>
                                                            <?php
                                                                $start = new DateTime($tournament['tournament_start_date']);
                                                                $end = new DateTime($tournament['tournament_end_date']);
                                                                echo $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
                                                            ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>

                                            <!-- Action buttons — rendered by JS (updateCardButtons) -->
                                            <div class="mt-auto card-actions" data-tour-id="<?= esc($tournament['tour_id']) ?>"></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            <?= $pager->links('default', 'bootstrap_pagination') ?>
                        </div>
                    <?php else: ?>
                        <div class="card shadow-sm border-0 rounded-4">
                            <div class="card-body text-center py-5">
                                <div class="mb-4">
                                    <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                        <i class="ti ti-trophy text-secondary" style="font-size: 3rem;"></i>
                                    </div>
                                </div>
                                <?php if(!empty($search_keyword)): ?>
                                    <h4 class="fw-bold text-dark">ไม่พบรายการแข่งขันที่ค้นหา</h4>
                                    <p class="text-muted">ลองค้นหาด้วยคำใหม่อีกครั้ง</p>
                                    <a href="<?= base_url('auth/tournament/list') ?>" class="btn btn-outline-primary rounded-pill mt-2">
                                        <i class="ti ti-arrow-left me-1"></i> ดูรายการทั้งหมด
                                    </a>
                                <?php else: ?>
                                    <h4 class="fw-bold text-dark"><?= lang('Common.no_tournament_now') ?></h4>
                                    <p class="text-muted"><?= lang('Common.check_back_later') ?></p>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Joined Tournaments Tab (rendered per team, toggled by JS) -->
                <div class="tab-pane fade" id="joined-tournaments" role="tabpanel" aria-labelledby="joined-tab">
                    <div id="joinedContent">
                        <?php if (!empty($joinedTournaments) && !empty($joinedRegistrationsMap)): ?>
                            <?php foreach ($academyTeams as $t): ?>
                            <div class="joined-team-group d-none" data-team-id="<?= esc($t['id']) ?>">
                                <?php
                                    $teamJoinedTours = [];
                                    $teamRegMap = $joinedRegistrationsMap[$t['id']] ?? [];
                                    foreach ($joinedTournaments as $jt) {
                                        if (isset($teamRegMap[$jt['tour_id']]) && $teamRegMap[$jt['tour_id']] !== null) {
                                            $teamJoinedTours[] = $jt;
                                        }
                                    }
                                ?>
                                <?php if (!empty($teamJoinedTours)): ?>
                                <div class="row g-3">
                                    <?php foreach ($teamJoinedTours as $tournament):
                                        $encId = bin2hex($encrypter->encrypt($tournament['tour_id']));
                                        $detailUrl = base_url('auth/tournament/detail/' . $encId);
                                        $regStatus = $teamRegMap[$tournament['tour_id']];
                                    ?>
                                    <div class="col-6 col-md-4 col-lg-3 col-xl-2-4">
                                        <div class="card shadow-sm border-0 h-100 rounded-3 transition-hover"
                                             style="cursor:pointer;"
                                             onclick="window.location.href='<?= $detailUrl ?>'">
                                            <div class="position-relative">
                                                <?php if (file_exists('./' . $tournament['logo']) && !empty($tournament['logo'])): ?>
                                                    <img src="<?= base_url($tournament['logo']) ?>" class="card-img-top rounded-top-3" alt="<?= esc($tournament['name']) ?>" style="height: 140px; object-fit: contain;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded-top-3 d-flex align-items-center justify-content-center" style="height: 140px;">
                                                        <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="Default Logo" style="max-height: 70px; opacity: 0.5;">
                                                    </div>
                                                <?php endif; ?>
                                                <div class="position-absolute top-0 end-0 m-2">
                                                    <span class="badge bg-white text-primary shadow-sm rounded-pill px-2 py-1 small">
                                                        <?= esc($tournament['generation'] ?? '') ?>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="card-body d-flex flex-column p-3">
                                                <h6 class="card-title fw-bold text-dark mb-2 line-clamp-2" style="min-height: 2.5rem;"><?= esc($tournament['name']) ?></h6>
                                                <p class="card-text text-muted small flex-grow-1 mb-2 card-detail-scroll" style="font-size: 0.8rem;">
                                                    <?= esc($tournament['detail'] ?? '') ?>
                                                </p>
                                                <div class="mb-3">
                                                    <?php if (!empty($tournament['start_date']) && !empty($tournament['end_date'])): ?>
                                                        <div class="d-flex align-items-center text-muted mb-1" style="font-size: 0.75rem;">
                                                            <i class="ti ti-calendar-event me-1 text-warning" style="font-size: 0.9rem;"></i>
                                                            <span class="fw-bold me-1">รับสมัคร:</span>
                                                            <span><?= (new DateTime($tournament['start_date']))->format('d/m/Y') . ' - ' . (new DateTime($tournament['end_date']))->format('d/m/Y') ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                    <?php if (!empty($tournament['tournament_start_date']) && !empty($tournament['tournament_end_date'])): ?>
                                                        <div class="d-flex align-items-center text-muted mb-1" style="font-size: 0.75rem;">
                                                            <i class="ti ti-trophy me-1 text-success" style="font-size: 0.9rem;"></i>
                                                            <span class="fw-bold me-1">แข่งขัน:</span>
                                                            <span><?= (new DateTime($tournament['tournament_start_date']))->format('d/m/Y') . ' - ' . (new DateTime($tournament['tournament_end_date']))->format('d/m/Y') ?></span>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                                <div class="mt-auto">
                                                    <?php if ($regStatus === 0): ?>
                                                        <button class="btn btn-warning w-100 rounded-pill fw-bold text-white btn-sm" disabled>
                                                            <i class="ti ti-clock me-1"></i><span class="small">รอการอนุมัติ</span>
                                                        </button>
                                                    <?php elseif ($regStatus === 1): ?>
                                                        <a href="<?= $detailUrl ?>" class="btn btn-success w-100 rounded-pill fw-bold btn-sm" onclick="event.stopPropagation()">
                                                            <i class="ti ti-check me-1"></i><span class="small">ได้รับอนุมัติ</span>
                                                        </a>
                                                    <?php elseif ($regStatus === 2): ?>
                                                        <button class="btn btn-danger w-100 rounded-pill fw-bold btn-sm" disabled>
                                                            <i class="ti ti-x me-1"></i><span class="small">ถูกปฏิเสธ</span>
                                                        </button>
                                                    <?php elseif ($regStatus === 3): ?>
                                                        <a href="<?= $detailUrl ?>" class="btn btn-warning w-100 rounded-pill fw-bold text-white btn-sm" onclick="event.stopPropagation()">
                                                            <i class="ti ti-currency-baht me-1"></i><span class="small">รอชำระเงิน</span>
                                                        </a>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                                <?php else: ?>
                                <div class="text-center py-5 text-muted">
                                    <i class="ti ti-circle-off fs-1 opacity-25 d-block mb-3"></i>
                                    <p class="mb-0">ยังไม่มีรายการที่เข้าร่วม</p>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                            <div class="joined-no-team-msg text-center py-5 text-muted">
                                <i class="ti ti-shield-check fs-1 opacity-25 d-block mb-3"></i>
                                <p class="mb-0">เลือกทีมด้านบนเพื่อดูรายการที่เข้าร่วม</p>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-5 text-muted">
                                <i class="ti ti-shield-check fs-1 opacity-25 d-block mb-3"></i>
                                <p class="mb-0">เลือกทีมด้านบนเพื่อดูรายการที่เข้าร่วม</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <?php if (empty($user['user_id'])): ?>
            <div class="col-md-12 mt-4">
                <div class="alert alert-primary border-0 shadow-sm rounded-4 d-flex align-items-center">
                    <i class="ti ti-login fs-3 me-3"></i>
                    <div>
                        <h5 class="alert-heading fw-bold mb-1"><?= lang('Common.want_join_tournament') ?></h5>
                        <p class="mb-0">
                            <a href="<?= base_url('auth') ?>" class="alert-link fw-bold text-decoration-underline"><?= lang('Common.login') ?></a>
                            <?= lang('Common.login_to_register') ?>
                        </p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    /* 5 cards per row for extra large screens */
    @media (min-width: 1200px) {
        .col-xl-2-4 {
            flex: 0 0 auto;
            width: 20%;
        }
    }

    .transition-hover {
        transition: transform 0.2s ease-in-out, box-shadow 0.2s ease-in-out;
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .line-clamp-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    /* Compact card styling */
    .card-body.p-3 {
        padding: 0.75rem !important;
    }

    /* Ensure consistent card heights */
    .card.h-100 {
        min-height: 100%;
    }

    /* Scrollable detail text - max 3 lines */
    .card-detail-scroll {
        max-height: 3.6em; /* ~3 lines at 0.8rem font-size */
        line-height: 1.2em;
        overflow-y: auto;
        overflow-x: hidden;
        word-wrap: break-word;
    }

    /* Custom scrollbar styling */
    .card-detail-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .card-detail-scroll::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }

    .card-detail-scroll::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }

    .card-detail-scroll::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
</style>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<style>
    .team-picker-item .team-picker-avatar {
        border-color: #dee2e6 !important;
        transition: all .2s;
    }
    .team-picker-item.selected .team-picker-avatar {
        border-color: #198754 !important;
        box-shadow: 0 0 0 3px rgba(25,135,84,.25);
    }
    .team-picker-item.selected > div:first-child + div {
        color: #198754 !important;
        font-weight: 700;
    }
    .team-picker-item:hover .team-picker-avatar {
        border-color: #198754 !important;
    }
</style>
<script>
/* ===== Registration Map from PHP ===== */
var registrationsMap = <?= json_encode($registrationsMap ?? []) ?>;
/* registrationsMap[teamId][tourId] = status (int) | null */

var selectedTeamId   = null;
var isLoggedIn       = <?= !empty($user['user_id']) ? 'true' : 'false' ?>;
var hasAcademyTeams  = <?= !empty($academyTeams) ? 'true' : 'false' ?>;
var REGISTER_URL     = '<?= base_url('auth/tournament/register') ?>';
var AUTH_URL         = '<?= base_url('auth') . '?return_url=' . urlencode(current_url()) ?>';

/* ===== Team Selector ===== */
function selectAcademyTeam(el) {
    // Deselect all
    document.querySelectorAll('.team-picker-item').forEach(function(item) {
        item.classList.remove('selected');
    });
    el.classList.add('selected');

    selectedTeamId = parseInt(el.getAttribute('data-team-id'));
    // Remember selected team across page reloads
    try { sessionStorage.setItem('selectedTeamId', selectedTeamId); } catch(e) {}
    var teamName   = el.getAttribute('data-team-name') || '';
    document.getElementById('selectedTeamLabel').textContent = 'ทีม: ' + teamName;

    // Update all card action buttons
    document.querySelectorAll('.card-actions').forEach(function(actDiv) {
        var tourId  = parseInt(actDiv.getAttribute('data-tour-id'));
        var cardEl  = actDiv.closest('[data-enc-id]');
        var encId   = cardEl ? cardEl.getAttribute('data-enc-id') : '';
        var detailUrl = cardEl ? cardEl.getAttribute('data-detail-url') : '';

        var teamMap = registrationsMap[selectedTeamId] || {};
        var status  = (tourId in teamMap) ? teamMap[tourId] : null;

        actDiv.innerHTML = renderActionButtons(status, encId, detailUrl);
    });

    // Refresh joined tab
    refreshJoinedTab();
}

/* ===== Render buttons based on registration status ===== */
function renderActionButtons(status, encId, detailUrl) {
    detailUrl = detailUrl || '#';

    if (!isLoggedIn) {
        return '<a href="' + AUTH_URL + '" class="btn btn-primary w-100 rounded-pill fw-bold mb-2 btn-sm" onclick="event.stopPropagation()">'
             + '<span class="small">สมัครเข้าร่วม</span> <i class="ti ti-login ms-1"></i></a>'
             + '<a href="' + detailUrl + '" class="btn btn-outline-secondary w-100 rounded-pill fw-bold btn-sm" onclick="event.stopPropagation()">'
             + '<i class="ti ti-file-description me-1"></i><span class="small">รายละเอียด</span></a>';
    }

    if (!hasAcademyTeams || !selectedTeamId) {
        // ไม่มี academy team หรือยังไม่เลือกทีม → แสดงแค่ปุ่ม detail
        return '<a href="' + detailUrl + '" class="btn btn-outline-secondary w-100 rounded-pill fw-bold btn-sm" onclick="event.stopPropagation()">'
             + '<i class="ti ti-file-description me-1"></i><span class="small">รายละเอียด</span></a>';
    }

    // status: null = ยังไม่สมัคร, 0 = รอ, 1 = อนุมัติ, 2 = ปฏิเสธ, 3 = รอชำระเงิน
    if (status === null || status === undefined) {
        return '<button class="btn btn-primary w-100 rounded-pill fw-bold mb-2 btn-sm" onclick="event.stopPropagation(); registerTournament(\'' + encId + '\')">'
             + '<i class="ti ti-plus me-1"></i><span class="small">สมัครแข่งขัน</span></button>'
             + '<a href="' + detailUrl + '" class="btn btn-outline-secondary w-100 rounded-pill fw-bold btn-sm" onclick="event.stopPropagation()">'
             + '<i class="ti ti-file-description me-1"></i><span class="small">รายละเอียด</span></a>';
    }
    if (status === 3) {
        return '<button class="btn btn-warning w-100 rounded-pill fw-bold text-white mb-2 btn-sm" onclick="event.stopPropagation(); window.location.href=\'' + detailUrl + '\'">'
             + '<i class="ti ti-currency-baht me-1"></i><span class="small">รอชำระเงิน</span></button>'
             + '<a href="' + detailUrl + '" class="btn btn-outline-secondary w-100 rounded-pill fw-bold btn-sm" onclick="event.stopPropagation()">'
             + '<i class="ti ti-file-description me-1"></i><span class="small">รายละเอียด</span></a>';
    }
    if (status === 0) {
        return '<button class="btn btn-warning w-100 rounded-pill fw-bold text-white mb-2 btn-sm" disabled>'
             + '<i class="ti ti-clock me-1"></i><span class="small">รอการอนุมัติ</span></button>'
             + '<a href="' + detailUrl + '" class="btn btn-outline-secondary w-100 rounded-pill fw-bold btn-sm" onclick="event.stopPropagation()">'
             + '<i class="ti ti-file-description me-1"></i><span class="small">รายละเอียด</span></a>';
    }
    if (status === 1) {
        return '<a href="' + detailUrl + '" class="btn btn-success w-100 rounded-pill fw-bold btn-sm" onclick="event.stopPropagation()">'
             + '<i class="ti ti-check me-1"></i><span class="small">ได้รับอนุมัติ</span></a>';
    }
    if (status === 2) {
        return '<button class="btn btn-danger w-100 rounded-pill fw-bold btn-sm" disabled>'
             + '<i class="ti ti-x me-1"></i><span class="small">ถูกปฏิเสธ</span></button>';
    }
    return '<a href="' + detailUrl + '" class="btn btn-outline-secondary w-100 rounded-pill fw-bold btn-sm" onclick="event.stopPropagation()">'
         + '<i class="ti ti-file-description me-1"></i><span class="small">รายละเอียด</span></a>';
}

/* ===== Joined tab: show/hide pre-rendered team groups ===== */
function refreshJoinedTab() {
    // Hide all team groups and the no-team message
    document.querySelectorAll('.joined-team-group').forEach(function(g) {
        g.classList.add('d-none');
    });
    var noTeamMsg = document.querySelector('.joined-no-team-msg');

    if (!selectedTeamId) {
        if (noTeamMsg) noTeamMsg.classList.remove('d-none');
        return;
    }

    // Show the group for the selected team
    var group = document.querySelector('.joined-team-group[data-team-id="' + selectedTeamId + '"]');
    if (group) {
        group.classList.remove('d-none');
        if (noTeamMsg) noTeamMsg.classList.add('d-none');
    } else {
        if (noTeamMsg) noTeamMsg.classList.remove('d-none');
    }
}

/* ===== Register Tournament ===== */
function registerTournament(encId) {
    if (!selectedTeamId) {
        Swal.fire({ icon: 'warning', title: 'กรุณาเลือกทีมก่อน', text: 'เลือกทีมที่ต้องการสมัครจากด้านบน', confirmButtonColor: '#198754' });
        return;
    }
    Swal.fire({
        title: '<?= lang('Common.confirm_register') ?>',
        text: '<?= lang('Common.wait_approval') ?>',
        icon: 'info',
        showCancelButton: true,
        confirmButtonColor: '#0F2B55',
        cancelButtonColor: '#d33',
        confirmButtonText: '<?= lang('Common.ok_button') ?>',
        cancelButtonText: '<?= lang('Common.cancel') ?>'
    }).then(function(result) {
        if (!result.isConfirmed) return;
        $.ajax({
            method: 'POST',
            url: REGISTER_URL,
            data: { id: encId, team_id: selectedTeamId },
            success: function(res) {
                Swal.fire({ icon: res.status, title: res.msg, showConfirmButton: false, timer: 3000 })
                    .then(function() { window.location.reload(); });
            }
        });
    });
}

/* ===== Init: auto-select saved team or first team ===== */
document.addEventListener('DOMContentLoaded', function() {
    var items = document.querySelectorAll('.team-picker-item');
    if (items.length > 0) {
        // Try to restore previously selected team from sessionStorage
        var savedTeamId = null;
        try { savedTeamId = sessionStorage.getItem('selectedTeamId'); } catch(e) {}
        var targetEl = null;

        if (savedTeamId) {
            // Find the saved team in the picker
            items.forEach(function(el) {
                if (el.getAttribute('data-team-id') === savedTeamId) {
                    targetEl = el;
                }
            });
        }

        // Fallback: auto-select first team if only 1, or if saved team not found
        if (!targetEl && items.length === 1) {
            targetEl = items[0];
        }

        if (targetEl) {
            selectAcademyTeam(targetEl);
        }
    } else if (isLoggedIn && !hasAcademyTeams) {
        // No academy teams: render detail-only buttons
        document.querySelectorAll('.card-actions').forEach(function(actDiv) {
            var cardEl   = actDiv.closest('[data-enc-id]');
            var detailUrl = cardEl ? cardEl.getAttribute('data-detail-url') : '#';
            actDiv.innerHTML = renderActionButtons(undefined, '', detailUrl);
        });
    }
});
</script>
<?php $this->endSection(); ?>
