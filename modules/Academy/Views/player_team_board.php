<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<?php
$canManage = !empty($canManage);
$rosterCount    = count($roster ?? []);
$availableCount = count($available ?? []);
?>

<style>
/* ===== Player Team Board ===== */
.board-card {
    background: #fff;
    border: 1px solid #e5e9f0;
    border-radius: 14px;
    box-shadow: 0 2px 6px rgba(15,23,42,.04);
    display: flex; flex-direction: column;
    height: calc(100vh - 240px);
    min-height: 600px;
    overflow: hidden;
    font-family: 'Kanit', sans-serif;
}
.board-card-academy { border-top: 3px solid #0d6efd; }
.board-card-team    { border-top: 3px solid #10b981; }

.board-header {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; padding: 14px 16px;
    border-bottom: 1px solid #f0f3f8;
    background: linear-gradient(180deg, #fafbfc 0%, #ffffff 100%);
}
.board-icon {
    width: 38px; height: 38px;
    display: inline-flex; align-items: center; justify-content: center;
    border-radius: 10px;
    font-size: 1rem;
}
.board-title { font-weight: 600; color: #0f172a; font-size: .95rem; line-height: 1.2; }
.board-sub { font-size: .72rem; color: #64748b; margin-top: 3px; }
.board-count {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 28px; padding: 0 10px;
    border-radius: 999px;
    font-size: .85rem; font-weight: 700;
}
.board-count-academy { background: rgba(13,110,253,.12); color: #0d6efd; }
.board-count-team    { background: rgba(16,185,129,.15); color: #059669; }

.board-search {
    position: relative; padding: 10px 14px 0;
}
.board-search i {
    position: absolute; left: 26px; top: 19px;
    color: #94a3b8; font-size: .85rem; pointer-events: none;
}
.board-search input {
    border-radius: 10px;
    padding-left: 36px;
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    font-size: .85rem;
}
.board-search input:focus {
    background: #fff; border-color: #0d6efd; box-shadow: 0 0 0 3px rgba(13,110,253,.1);
}

.board-body {
    flex: 1 1 auto;
    padding: 12px;
    overflow-y: auto;
    min-height: 0;
    display: flex; flex-direction: column; gap: 8px;
}
.board-body.drag-over { background: rgba(13,110,253,.06); }
.board-card-team .board-body.drag-over { background: rgba(16,185,129,.08); }

.board-empty {
    text-align: center; padding: 40px 16px; color: #94a3b8;
}
.board-empty i { font-size: 2.5rem; opacity: .25; margin-bottom: 10px; display: block; }

/* Drop zone footer (ฝั่งทีม) — พื้นที่ว่างด้านล่างไว้ลากการ์ดมาวาง */
.drop-zone-footer {
    margin-top: auto;
    padding: 18px 14px;
    border: 2px dashed #cbd5e1;
    border-radius: 12px;
    background: #f8fafc;
    color: #64748b;
    text-align: center;
    font-size: .82rem;
    transition: border-color .15s, background .15s, color .15s;
    flex-shrink: 0;
}
.drop-zone-footer i {
    display: block; margin-bottom: 6px;
    font-size: 1.4rem; opacity: .6;
}
.board-card-team .board-body.drag-over .drop-zone-footer {
    border-color: #10b981;
    background: rgba(16,185,129,.08);
    color: #047857;
}
.drop-zone-footer.is-hidden { display: none; }

/* Player card */
.player-card {
    display: flex; align-items: center; gap: 10px;
    padding: 10px 12px;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    background: #fff;
    transition: border-color .15s, box-shadow .15s, transform .1s;
    cursor: grab;
}
.player-card:hover {
    border-color: #cbd5e1;
    box-shadow: 0 4px 10px -2px rgba(15,23,42,.08);
}
.player-card:active { cursor: grabbing; }
.player-card.dragging { opacity: .5; transform: scale(.98); }

.pc-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid #fff;
    box-shadow: 0 0 0 1px #e2e8f0;
    flex-shrink: 0;
}
.pc-info { flex-grow: 1; min-width: 0; }
.pc-name {
    font-weight: 600; font-size: .9rem; color: #0f172a;
    line-height: 1.2;
    white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
}
.pc-meta {
    display: flex; flex-wrap: wrap; gap: 4px; margin-top: 4px;
    font-size: .7rem;
}
.pc-meta .badge {
    font-weight: 500; padding: 3px 8px;
    background: #f1f5f9; color: #475569;
    border-radius: 999px;
}
.pc-meta .badge.shirt { background: #fef3c7; color: #b45309; font-weight: 700; }
.pc-meta .badge.position { background: #dbeafe; color: #1d4ed8; }
.pc-meta .badge.age { background: #ede9fe; color: #6d28d9; }
.pc-meta .badge.team-other {
    background: rgba(16,185,129,.12); color: #047857;
    font-size: .65rem;
}

.pc-action {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px;
    border-radius: 50%;
    border: 1px solid #e2e8f0;
    background: #fff;
    color: #475569;
    font-size: .8rem;
    cursor: pointer;
    transition: all .15s;
    flex-shrink: 0;
}
.pc-action.add:hover {
    background: #10b981; color: #fff; border-color: #10b981;
    transform: scale(1.08);
}
.pc-action.remove:hover {
    background: #ef4444; color: #fff; border-color: #ef4444;
    transform: scale(1.08);
}
.pc-action[disabled] { opacity: .4; cursor: not-allowed; }

/* นักเตะที่อยู่ในทีมแล้ว (แสดงฝั่ง Academy) — locked, ไม่สามารถเพิ่มซ้ำ */
.player-card.is-locked { background: #f8fafc; border-style: dashed; cursor: default; }
.player-card.is-locked:hover { box-shadow: none; }
.pc-in-team {
    display: inline-flex; align-items: center; justify-content: center;
    width: 32px; height: 32px;
    border-radius: 50%;
    background: rgba(16,185,129,.12);
    color: #059669;
    font-size: .8rem;
    flex-shrink: 0;
}

@media (max-width: 991.98px) {
    .board-card { height: auto; min-height: 480px; }
    .board-body { max-height: 55vh; }
}
</style>

<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <!-- Header -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-users me-2"></i>จัดการนักเตะของทีม
                </h4>
                <p class="text-muted small mb-0 mt-1">
                    ลากนักเตะจาก <span class="fw-semibold text-primary">Academy</span> มาวางที่ <span class="fw-semibold text-success">ทีม</span> หรือใช้ปุ่ม <i class="fas fa-arrow-right text-success"></i> / <i class="fas fa-arrow-left text-secondary"></i>
                </p>
            </div>
            <div class="d-flex gap-2">
                <a href="<?= base_url('academy/team-info/' . $academy['id'] . '/' . $team['id']) ?>"
                   class="btn btn-outline-secondary rounded-pill px-3">
                    <i class="fas fa-info-circle me-1"></i>ข้อมูลทีม
                </a>
                <?php if ($canManage): ?>
                <div class="dropdown">
                    <button type="button" class="btn btn-primary rounded-pill px-3 dropdown-toggle"
                            data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fas fa-plus-circle me-1"></i>เพิ่มนักเตะ
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 rounded-3 p-2"
                        style="min-width: 240px;">
                        <li>
                            <button type="button" class="dropdown-item rounded-2 py-2 px-3"
                                    data-bs-toggle="modal" data-bs-target="#addToAcademyModal">
                                <i class="fas fa-search me-2 text-success"></i>เพิ่มจากระบบ
                            </button>
                        </li>
                        <li>
                            <a href="<?= base_url('player/manage?academy_id=' . $academy['id']) ?>"
                               class="dropdown-item rounded-2 py-2 px-3">
                                <i class="fas fa-user-plus me-2 text-primary"></i>เพิ่มนักเตะใหม่
                            </a>
                        </li>
                    </ul>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-5">
        <!-- ===== ฝั่งซ้าย: Academy Players (available) ===== -->
        <div class="col-lg-6">
            <div class="board-card board-card-academy" data-zone="academy">
                <div class="board-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="board-icon" style="background:rgba(13,110,253,.15);color:#0d6efd;">
                            <i class="fas fa-school"></i>
                        </span>
                        <div>
                            <div class="board-title">นักเตะของ Academy</div>
                            <div class="board-sub">
                                <i class="fas fa-info-circle"></i> นักเตะที่ลงทะเบียนใน Academy
                            </div>
                        </div>
                    </div>
                    <span class="board-count board-count-academy" data-count="available"><?= $availableCount ?></span>
                </div>
                <div class="board-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchAvailable" class="form-control" placeholder="ค้นหาชื่อ / เลขเสื้อ / ตำแหน่ง...">
                </div>
                <div class="board-body" id="board-available" data-team-id="<?= (int)$team['id'] ?>" data-academy-id="<?= (int)$academy['id'] ?>">
                    <?php if (empty($available)): ?>
                        <div class="board-empty">
                            <i class="fas fa-user-friends"></i>
                            <p class="mb-1 fw-semibold">ยังไม่มีนักเตะใน Academy</p>
                            <small class="text-muted">กดปุ่ม "เพิ่มนักเตะ" ด้านบนเพื่อเริ่มต้น</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($available as $p): ?>
                            <?= view('Modules\\Academy\\Views\\_player_card', [
                                'p' => $p, 'side' => 'academy', 'canManage' => $canManage,
                            ]) ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- ===== ฝั่งขวา: Team Roster ===== -->
        <div class="col-lg-6">
            <div class="board-card board-card-team" data-zone="team">
                <div class="board-header">
                    <div class="d-flex align-items-center gap-2">
                        <span class="board-icon" style="background:rgba(16,185,129,.15);color:#059669;">
                            <i class="fas fa-shield-alt"></i>
                        </span>
                        <div>
                            <div class="board-title">นักเตะของทีม: <?= esc($team['name']) ?></div>
                            <div class="board-sub">
                                <i class="fas fa-info-circle"></i> สังกัดทีมนี้ (มีอยู่ในทีมอื่นได้พร้อมกัน)
                            </div>
                        </div>
                    </div>
                    <span class="board-count board-count-team" data-count="roster"><?= $rosterCount ?></span>
                </div>
                <div class="board-search">
                    <i class="fas fa-search"></i>
                    <input type="text" id="searchRoster" class="form-control" placeholder="ค้นหาในทีม...">
                </div>
                <div class="board-body" id="board-roster">
                    <?php if (empty($roster)): ?>
                        <div class="board-empty">
                            <i class="fas fa-shield-alt"></i>
                            <p class="mb-1 fw-semibold">ทีมนี้ยังไม่มีนักเตะ</p>
                            <small class="text-muted">ลากการ์ดจากฝั่งซ้าย หรือกดปุ่ม <i class="fas fa-arrow-right"></i> เพื่อเพิ่มนักเตะ</small>
                        </div>
                    <?php else: ?>
                        <?php foreach ($roster as $p): ?>
                            <?= view('Modules\\Academy\\Views\\_player_card', [
                                'p' => $p, 'side' => 'team', 'canManage' => $canManage,
                            ]) ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div class="drop-zone-footer<?= empty($roster) ? ' is-hidden' : '' ?>" id="dropZoneFooter">
                        <i class="fas fa-hand-point-down"></i>
                        ลากการ์ดมาวางในพื้นที่นี้เพื่อเพิ่มเข้าทีม
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ===== Modal: เพิ่มจากระบบ (ค้นหานักเตะเดิม) — ใช้ดีไซน์เดียวกับ player_list ===== -->
<?php if ($canManage): ?>
<div class="modal fade" id="addToAcademyModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 player-search-modal">
            <!-- Hero header -->
            <div class="psm-hero">
                <div class="psm-hero__icon"><i class="fas fa-user-plus"></i></div>
                <div class="psm-hero__body">
                    <h5 class="psm-hero__title mb-1"><?= lang('Common.add_player_from_system') ?></h5>
                    <p class="psm-hero__sub mb-0"><?= lang('Common.search_player_hint') ?></p>
                </div>
                <button type="button" class="btn-close btn-close-white psm-hero__close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Drag/drop hint (เฉพาะหน้านี้) -->
                <div class="alert alert-info border-0 d-flex align-items-start gap-2 mb-3 small" style="background:#eff6ff;color:#1e40af;">
                    <i class="fas fa-info-circle mt-1"></i>
                    <div>
                        <strong>นักเตะที่เพิ่มจะอยู่ฝั่ง Academy เท่านั้น</strong><br>
                        ใช้ปุ่ม <i class="fas fa-arrow-right text-success"></i> หรือลากการ์ดมาฝั่งทีม เพื่อใส่นักเตะลงทีม
                    </div>
                </div>

                <!-- Search input -->
                <div class="psm-search">
                    <i class="fa fa-search psm-search__icon"></i>
                    <input type="text" class="form-control psm-search__input" id="academySearchInput"
                           placeholder="<?= lang('Common.enter_id_or_name') ?>" autocomplete="off">
                    <button type="button" class="psm-search__clear d-none" id="academySearchClear" aria-label="Clear">
                        <i class="fa fa-times-circle"></i>
                    </button>
                    <button class="btn psm-search__submit" type="button" id="academySearchBtn">
                        <i class="fa fa-search me-1"></i> <?= lang('Common.search') ?>
                    </button>
                </div>

                <!-- Status legend -->
                <div class="psm-legend">
                    <span class="psm-legend__item"><span class="psm-dot psm-dot--success"></span><?= lang('Common.in_this_academy') ?></span>
                    <span class="psm-legend__item"><span class="psm-dot psm-dot--warning"></span><?= lang('Common.in_other_academy') ?></span>
                    <span class="psm-legend__item"><span class="psm-dot psm-dot--muted"></span><?= lang('Common.no_academy') ?></span>
                </div>

                <!-- Results -->
                <div id="academyResultsWrap" class="psm-results d-none">
                    <div class="psm-results__head">
                        <span class="psm-results__title"><i class="fa fa-users me-2"></i><?= lang('Common.search_results') ?></span>
                        <span class="psm-results__count" id="academyResultsCount"></span>
                    </div>
                    <div id="academySearchResults" class="psm-results__list">
                        <!-- Results will be populated here -->
                    </div>
                </div>

                <!-- Initial state -->
                <div id="academySearchHint" class="psm-empty">
                    <i class="fas fa-user-friends psm-empty__icon"></i>
                    <p class="psm-empty__title mb-1"><?= lang('Common.search_initial_title') ?></p>
                    <p class="psm-empty__sub mb-0"><?= lang('Common.search_initial_sub') ?></p>
                </div>

                <!-- No results -->
                <div id="academyNoResults" class="psm-empty d-none">
                    <i class="fas fa-search-minus psm-empty__icon"></i>
                    <p class="psm-empty__title mb-1"><?= lang('Common.no_player_found') ?></p>
                    <p class="psm-empty__sub mb-0"><?= lang('Common.no_player_hint') ?></p>
                </div>

                <!-- Loading -->
                <div id="academySearchLoading" class="psm-loading d-none">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3 mb-0"><?= lang('Common.searching_msg') ?></p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<style>
.player-search-modal .psm-hero {
    background: #16a34a;
    color: #fff;
    padding: 22px 26px;
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
    display: flex; align-items: center; gap: 14px;
    position: relative;
}
.player-search-modal .psm-hero__icon {
    width: 48px; height: 48px; border-radius: 12px;
    background: rgba(255,255,255,0.18);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}
.player-search-modal .psm-hero__title { font-weight: 700; font-size: 1.1rem; color: #fff; }
.player-search-modal .psm-hero__sub   { font-size: .82rem; opacity: .85; line-height: 1.45; }
.player-search-modal .psm-hero__close { position: absolute; top: 18px; right: 18px; opacity: .8; }
.player-search-modal .psm-hero__close:hover { opacity: 1; }

.player-search-modal .psm-search {
    position: relative;
    display: flex; align-items: center;
    background: #f6f9fc;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    padding: 0 6px 0 16px;
    transition: border-color .2s, box-shadow .2s;
    margin-bottom: 14px;
}
.player-search-modal .psm-search:focus-within {
    border-color: #16a34a;
    box-shadow: 0 0 0 4px rgba(22,163,74,.12);
}
.player-search-modal .psm-search__icon { color: #94a3b8; font-size: 1rem; flex-shrink: 0; }
.player-search-modal .psm-search__input {
    border: 0; background: transparent; font-size: .95rem;
    padding: 14px 12px; flex-grow: 1; outline: none; box-shadow: none !important;
}
.player-search-modal .psm-search__clear {
    background: transparent; border: 0; color: #94a3b8;
    padding: 8px; cursor: pointer; transition: color .2s;
}
.player-search-modal .psm-search__clear:hover { color: #ef4444; }
.player-search-modal .psm-search__submit {
    background: #16a34a; color: #fff;
    border: 0; border-radius: 10px;
    padding: 9px 18px; font-weight: 600;
    flex-shrink: 0;
}
.player-search-modal .psm-search__submit:hover { background: #15803d; color: #fff; }

.player-search-modal .psm-legend {
    display: flex; gap: 16px; flex-wrap: wrap;
    padding: 8px 4px 16px;
    font-size: .78rem; color: #64748b;
}
.player-search-modal .psm-legend__item { display: inline-flex; align-items: center; gap: 6px; }
.player-search-modal .psm-dot { width: 10px; height: 10px; border-radius: 50%; }
.player-search-modal .psm-dot--success { background: #16a34a; }
.player-search-modal .psm-dot--warning { background: #f59e0b; }
.player-search-modal .psm-dot--muted   { background: #94a3b8; }

.player-search-modal .psm-results {
    background: #fafbfc;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px;
}
.player-search-modal .psm-results__head {
    display: flex; align-items: center; justify-content: space-between;
    padding-bottom: 8px; margin-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
}
.player-search-modal .psm-results__title { font-weight: 600; color: #1f2937; font-size: .9rem; }
.player-search-modal .psm-results__count { font-size: .78rem; color: #64748b; }
.player-search-modal .psm-results__list .list-group-item {
    border-radius: 10px; margin-bottom: 6px; border-color: #e5e7eb;
    transition: all .15s;
}
.player-search-modal .psm-results__list .list-group-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15,23,42,.08);
    border-color: #16a34a40;
}

.player-search-modal .psm-avatar {
    width: 56px; height: 56px;
    border-radius: 50%;
    border: 1px solid #e5e7eb;
    object-fit: cover;
    flex-shrink: 0;
}
.player-search-modal .psm-avatar--initial {
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #16a34a 0%, #16478C 100%);
    color: #fff;
    font-weight: 700; font-size: 1.25rem;
    text-transform: uppercase;
    border: 0;
}

.player-search-modal .psm-teams {
    display: flex; flex-wrap: wrap; gap: 6px;
    margin-top: 4px;
}
.player-search-modal .psm-team {
    display: inline-flex; align-items: center;
    padding: 3px 10px; border-radius: 999px;
    background: #f1f5f9; border: 1px solid #e2e8f0;
    color: #475569; font-size: .76rem; font-weight: 500;
    line-height: 1.2;
    max-width: 100%;
}
.player-search-modal .psm-team__academy { color: #94a3b8; margin-left: 4px; font-weight: 400; }
.player-search-modal .psm-team--current {
    background: rgba(22,163,74,.10);
    border-color: rgba(22,163,74,.35);
    color: #15803d;
}
.player-search-modal .psm-team--current .psm-team__academy { color: #65a380; }

.player-search-modal .psm-empty,
.player-search-modal .psm-loading {
    text-align: center; padding: 36px 16px;
}
.player-search-modal .psm-empty__icon { font-size: 2.6rem; color: #cbd5e1; margin-bottom: 12px; display: block; }
.player-search-modal .psm-empty__title { font-weight: 600; color: #475569; font-size: .95rem; }
.player-search-modal .psm-empty__sub   { font-size: .82rem; color: #94a3b8; }
</style>
<?php endif; ?>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
(function() {
    const academyId = <?= (int)$academy['id'] ?>;
    const teamId    = <?= (int)$team['id'] ?>;
    const csrfToken = '<?= csrf_token() ?>';
    const csrfHash  = '<?= csrf_hash() ?>';
    const canManage = <?= $canManage ? 'true' : 'false' ?>;

    const boardAvailable = document.getElementById('board-available');
    const boardRoster    = document.getElementById('board-roster');
    const countAvail     = document.querySelector('[data-count="available"]');
    const countRoster    = document.querySelector('[data-count="roster"]');
    const searchAvail    = document.getElementById('searchAvailable');
    const searchRoster   = document.getElementById('searchRoster');

    // ----- Search filter -----
    function filterCards(input, container) {
        const q = (input.value || '').trim().toLowerCase();
        container.querySelectorAll('.player-card').forEach(card => {
            const text = (card.dataset.search || '').toLowerCase();
            card.style.display = !q || text.includes(q) ? '' : 'none';
        });
    }
    if (searchAvail)  searchAvail.addEventListener('input',  () => filterCards(searchAvail,  boardAvailable));
    if (searchRoster) searchRoster.addEventListener('input', () => filterCards(searchRoster, boardRoster));

    // ----- Helpers -----
    function findCard(container, playerId) {
        return container.querySelector('.player-card[data-player-id="'+playerId+'"]');
    }
    function lockAcademyCard(card) {
        // เปลี่ยนการ์ดฝั่ง Academy ให้เป็น state "อยู่ในทีมแล้ว"
        card.classList.add('is-locked');
        card.dataset.inTeam = '1';
        card.draggable = false;
        const action = card.querySelector('.pc-action');
        if (action) {
            const span = document.createElement('span');
            span.className = 'pc-in-team';
            span.title = 'นักเตะคนนี้อยู่ในทีมนี้แล้ว';
            span.innerHTML = '<i class="fas fa-check"></i>';
            action.replaceWith(span);
        }
    }
    function unlockAcademyCard(card) {
        // คืนสถานะให้กดเพิ่มเข้าทีมได้อีกครั้ง
        card.classList.remove('is-locked');
        card.dataset.inTeam = '0';
        card.draggable = true;
        const span = card.querySelector('.pc-in-team');
        if (span) {
            const btn = document.createElement('button');
            btn.type = 'button';
            btn.className = 'pc-action add';
            btn.title = 'เพิ่มเข้าทีม';
            btn.setAttribute('aria-label', 'เพิ่มเข้าทีม');
            btn.innerHTML = '<i class="fas fa-arrow-right"></i>';
            span.replaceWith(btn);
        }
    }
    function buildTeamCardFromAcademy(academyCard) {
        // โคลน card ฝั่ง Academy → ปรับเป็น team card (มีปุ่ม remove)
        const clone = academyCard.cloneNode(true);
        clone.classList.remove('is-locked', 'dragging');
        clone.dataset.side = 'team';
        clone.dataset.inTeam = '1';
        clone.draggable = true;
        // ลบ check icon ถ้ามี
        const span = clone.querySelector('.pc-in-team');
        if (span) span.remove();
        // ลบปุ่มเดิมถ้ามี
        const oldBtn = clone.querySelector('.pc-action');
        if (oldBtn) oldBtn.remove();
        // เพิ่มปุ่ม remove
        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'pc-action remove';
        btn.title = 'นำออกจากทีม';
        btn.setAttribute('aria-label', 'นำออกจากทีม');
        btn.innerHTML = '<i class="fas fa-arrow-left"></i>';
        clone.appendChild(btn);
        makeDraggable(clone);
        return clone;
    }

    // ----- Update counters -----
    function updateCounts() {
        countAvail.textContent  = boardAvailable.querySelectorAll('.player-card').length;
        countRoster.textContent = boardRoster.querySelectorAll('.player-card').length;
        toggleEmpty(boardAvailable, 'available');
        toggleEmpty(boardRoster, 'roster');
    }
    function toggleEmpty(container, type) {
        const hasCards = container.querySelectorAll('.player-card').length > 0;
        let empty = container.querySelector('.board-empty');
        if (!hasCards && !empty) {
            const html = type === 'roster'
                ? '<div class="board-empty"><i class="fas fa-shield-alt"></i><p class="mb-1 fw-semibold">ทีมนี้ยังไม่มีนักเตะ</p><small class="text-muted">ลากหรือกดปุ่ม <i class="fas fa-arrow-right"></i></small></div>'
                : '<div class="board-empty"><i class="fas fa-user-friends"></i><p class="mb-1 fw-semibold">ยังไม่มีนักเตะใน Academy</p></div>';
            const footer = container.querySelector('.drop-zone-footer');
            if (footer) {
                footer.insertAdjacentHTML('beforebegin', html);
            } else {
                container.insertAdjacentHTML('beforeend', html);
            }
        } else if (hasCards && empty) {
            empty.remove();
        }
        // toggle drop zone footer ฝั่งทีม: ซ่อนเมื่อยังไม่มีการ์ด (กันชนกับ empty state), แสดงเมื่อมี
        if (type === 'roster') {
            const footer = container.querySelector('.drop-zone-footer');
            if (footer) footer.classList.toggle('is-hidden', !hasCards);
        }
    }

    // ----- API: add player to team -----
    async function moveToTeam(playerId) {
        const academyCard = findCard(boardAvailable, playerId);
        if (!academyCard || academyCard.dataset.inTeam === '1') return;

        const body = new FormData();
        body.append('academy_id', academyId);
        body.append('team_id', teamId);
        body.append('player_id', playerId);
        body.append(csrfToken, csrfHash);
        try {
            const r = await fetch('<?= base_url('academy/team-add-player') ?>', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body });
            const d = await r.json();
            if (d.status === 'success') {
                // สร้าง card ฝั่งทีมก่อน lock card ฝั่ง academy
                const teamCard = buildTeamCardFromAcademy(academyCard);
                const footer = boardRoster.querySelector('.drop-zone-footer');
                if (footer) {
                    boardRoster.insertBefore(teamCard, footer);
                } else {
                    boardRoster.appendChild(teamCard);
                }
                lockAcademyCard(academyCard);
                updateCounts();
                toast('success', d.message);
            } else { toast('error', d.message); }
        } catch (e) { toast('error', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้'); }
    }

    // ----- API: remove player from team (Academy enrollment unchanged) -----
    async function moveToAcademy(playerId) {
        const teamCard = findCard(boardRoster, playerId);
        if (!teamCard) return;

        const body = new FormData();
        body.append('academy_id', academyId);
        body.append('team_id', teamId);
        body.append('player_id', playerId);
        body.append(csrfToken, csrfHash);
        try {
            const r = await fetch('<?= base_url('academy/team-remove-player') ?>', { method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body });
            const d = await r.json();
            if (d.status === 'success') {
                teamCard.remove();
                const academyCard = findCard(boardAvailable, playerId);
                if (academyCard) unlockAcademyCard(academyCard);
                updateCounts();
                toast('success', d.message);
            } else { toast('error', d.message); }
        } catch (e) { toast('error', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้'); }
    }

    // ----- Click action -----
    document.body.addEventListener('click', function(e) {
        const btn = e.target.closest('.pc-action');
        if (!btn || !canManage) return;
        const card = btn.closest('.player-card');
        if (!card) return;
        const playerId = card.dataset.playerId;
        if (btn.classList.contains('add'))    moveToTeam(playerId);
        else if (btn.classList.contains('remove')) moveToAcademy(playerId);
    });

    // ----- Drag & Drop -----
    function makeDraggable(card) {
        if (card.classList.contains('is-locked')) { card.draggable = false; return; }
        card.draggable = true;
        card.addEventListener('dragstart', e => {
            if (card.classList.contains('is-locked')) { e.preventDefault(); return; }
            e.dataTransfer.setData('text/plain', card.dataset.playerId + ':' + card.dataset.side);
            card.classList.add('dragging');
        });
        card.addEventListener('dragend', () => card.classList.remove('dragging'));
    }
    if (canManage) {
        document.querySelectorAll('.player-card').forEach(makeDraggable);
        [boardAvailable, boardRoster].forEach(zone => {
            zone.addEventListener('dragover', e => { e.preventDefault(); zone.classList.add('drag-over'); });
            zone.addEventListener('dragleave', () => zone.classList.remove('drag-over'));
            zone.addEventListener('drop', e => {
                e.preventDefault(); zone.classList.remove('drag-over');
                const payload = e.dataTransfer.getData('text/plain');
                if (!payload) return;
                const [id, fromSide] = payload.split(':');
                const toSide = (zone.id === 'board-roster') ? 'team' : 'academy';
                if (fromSide === toSide) return;
                if (toSide === 'team') moveToTeam(id);
                else moveToAcademy(id);
            });
        });
    }

    // ----- Toast helper -----
    function toast(type, msg) {
        if (typeof Swal === 'undefined') { alert(msg); return; }
        Swal.fire({
            toast: true, position: 'top-end',
            icon: type, title: msg,
            showConfirmButton: false, timer: 1800, timerProgressBar: true,
        });
    }

    // ===== "เพิ่มจากระบบ" Modal — search & enroll without team =====
    if (canManage) {
        const searchInput = document.getElementById('academySearchInput');
        const searchBtn   = document.getElementById('academySearchBtn');
        const searchClear = document.getElementById('academySearchClear');
        const resultsWrap = document.getElementById('academyResultsWrap');
        const resultsBox  = document.getElementById('academySearchResults');
        const resultsCount= document.getElementById('academyResultsCount');
        const loadingBox  = document.getElementById('academySearchLoading');
        const noResults   = document.getElementById('academyNoResults');
        const searchHint  = document.getElementById('academySearchHint');
        const modalEl     = document.getElementById('addToAcademyModal');

        function show(el)  { el && el.classList.remove('d-none'); }
        function hide(el)  { el && el.classList.add('d-none'); }
        function escHtml(s) {
            return String(s == null ? '' : s)
                .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
        }

        // toggle clear button + reset state when input cleared
        searchInput?.addEventListener('input', function() {
            const v = this.value;
            if (v.length === 0) {
                searchClear?.classList.add('d-none');
                hide(resultsWrap); hide(noResults); hide(loadingBox); show(searchHint);
            } else {
                searchClear?.classList.remove('d-none');
            }
        });
        searchClear?.addEventListener('click', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });
        // Reset state when opened
        modalEl?.addEventListener('shown.bs.modal', function() {
            searchInput.value = '';
            searchInput.dispatchEvent(new Event('input'));
            searchInput.focus();
        });

        async function searchAcademyPlayers() {
            const q = (searchInput.value || '').trim();
            if (q.length < 2) {
                toast('warning', 'กรุณาพิมพ์อย่างน้อย 2 ตัวอักษร');
                return;
            }
            hide(searchHint); hide(resultsWrap); hide(noResults); show(loadingBox);
            try {
                const r = await fetch('<?= base_url('academy/player-search/' . $academy['id']) ?>?q=' + encodeURIComponent(q));
                const d = await r.json();
                hide(loadingBox);
                if (d.status !== 'success' || !Array.isArray(d.data) || d.data.length === 0) {
                    show(noResults); return;
                }
                function avatarHtml(player) {
                    const hasImage = player.image_path && player.image_path.length > 0;
                    if (hasImage) {
                        return '<img src="' + escHtml(player.image_url) + '" class="psm-avatar psm-avatar--img" '
                             + 'onerror="this.parentNode.innerHTML=\'<div class=&quot;psm-avatar psm-avatar--initial&quot;>'
                             + escHtml((player.full_name || '?').trim().charAt(0).toUpperCase())
                             + '</div>\'">';
                    }
                    const initial = (player.full_name || '?').trim().charAt(0).toUpperCase();
                    return '<div class="psm-avatar psm-avatar--initial">' + escHtml(initial) + '</div>';
                }

                resultsBox.innerHTML = d.data.map(function(p) {
                    const birth = p.birth_date ? new Date(p.birth_date).toLocaleDateString('th-TH') : '-';

                    // status badge หลัก
                    let statusBadge = '';
                    if (p.in_current_academy) {
                        statusBadge = '<span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill">'
                            + '<i class="fas fa-check-circle me-1"></i><?= lang('Common.in_this_academy') ?>'
                            + (p.current_team_name ? ' · ' + escHtml(p.current_team_name) : '')
                            + '</span>';
                    } else if (p.other_academies && p.other_academies.length > 0) {
                        statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill">'
                            + '<i class="fas fa-school me-1"></i><?= lang('Common.in_other_academy') ?> (' + p.other_academies.length + ')'
                            + '</span>';
                    } else {
                        statusBadge = '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill">'
                            + '<i class="fas fa-user-plus me-1"></i><?= lang('Common.no_academy') ?>'
                            + '</span>';
                    }

                    // team chips จาก enrollments ทั้งหมด
                    let teamsHtml = '';
                    if (p.enrollments && p.enrollments.length > 0) {
                        const chips = p.enrollments.map(function(e) {
                            const isCurrent = parseInt(e.academy_id) === parseInt('<?= (int)($academy['id'] ?? 0) ?>');
                            const teamName = e.team_name || '<?= lang('Common.no_team_assigned') ?>';
                            const academyName = e.academy_name || '';
                            const cls = isCurrent ? 'psm-team psm-team--current' : 'psm-team';
                            return '<span class="' + cls + '" title="Academy: ' + escHtml(academyName) + '">'
                                + '<i class="fas fa-shield-alt me-1"></i>' + escHtml(teamName)
                                + '<span class="psm-team__academy">· ' + escHtml(academyName) + '</span>'
                                + '</span>';
                        }).join('');
                        teamsHtml = '<div class="psm-teams">' + chips + '</div>';
                    } else {
                        teamsHtml = '<div class="psm-teams"><span class="text-muted small"><i class="fas fa-info-circle me-1"></i><?= lang('Common.no_team_yet') ?></span></div>';
                    }

                    // ปุ่ม: ถ้าอยู่ใน academy นี้แล้ว → disable
                    let btnHtml = '';
                    if (p.in_current_academy) {
                        btnHtml = '<button type="button" class="btn btn-light btn-sm rounded-pill" disabled>'
                            + '<i class="fa fa-check me-1"></i><?= lang('Common.already_added') ?>'
                            + '</button>';
                    } else {
                        btnHtml = '<button type="button" class="btn btn-success btn-sm rounded-pill px-3" data-enroll="' + p.id + '">'
                            + '<i class="fas fa-plus me-1"></i>เพิ่มเข้า Academy'
                            + '</button>';
                    }

                    return '<div class="list-group-item list-group-item-action d-flex align-items-start p-3 gap-3">'
                        + '<div class="flex-shrink-0">' + avatarHtml(p) + '</div>'
                        + '<div class="flex-grow-1 min-w-0">'
                        +   '<div class="d-flex align-items-center gap-2 flex-wrap mb-1">'
                        +     '<h6 class="mb-0 fw-bold">' + escHtml(p.full_name) + '</h6>'
                        +     statusBadge
                        +   '</div>'
                        +   teamsHtml
                        +   '<small class="text-muted d-block mt-1"><i class="fas fa-birthday-cake me-1"></i> ' + birth + '</small>'
                        + '</div>'
                        + '<div class="flex-shrink-0">' + btnHtml + '</div>'
                        + '</div>';
                }).join('');
                resultsCount.textContent = d.data.length + ' คน';
                show(resultsWrap);
            } catch (e) {
                hide(loadingBox);
                show(noResults);
                toast('error', 'ค้นหาไม่สำเร็จ');
            }
        }

        searchBtn?.addEventListener('click', searchAcademyPlayers);
        searchInput?.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') { e.preventDefault(); searchAcademyPlayers(); }
        });

        resultsBox?.addEventListener('click', async function(e) {
            const btn = e.target.closest('[data-enroll]');
            if (!btn) return;
            const playerId = btn.dataset.enroll;
            btn.disabled = true; btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

            try {
                const r = await fetch('<?= base_url('academy/player-enroll/' . $academy['id']) ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ player_id: playerId, team_id: 0, shirt_number: '' })
                });
                const d = await r.json();
                if (d.status === 'success') {
                    btn.outerHTML = '<span class="badge bg-success rounded-pill px-3 py-2">เพิ่มแล้ว</span>';
                    toast('success', d.message || 'เพิ่มเข้า Academy สำเร็จ');
                    setTimeout(() => location.reload(), 800);
                } else {
                    btn.disabled = false;
                    btn.innerHTML = '<i class="fas fa-plus me-1"></i>เพิ่มเข้า Academy';
                    toast('error', d.message || 'ไม่สามารถเพิ่มได้');
                }
            } catch (err) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plus me-1"></i>เพิ่มเข้า Academy';
                toast('error', 'เชื่อมต่อเซิร์ฟเวอร์ไม่ได้');
            }
        });
    }

})();
</script>
<?php $this->endSection(); ?>
