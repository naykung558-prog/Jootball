<?php $this->extend('template/tournament') ?>

<?php $this->section('content'); ?>
<style>
    :root {
        --primary-color: #06346D;
        --secondary-color: #F3B71B;
        --navy-dark: #042441;
        --navy-gradient: linear-gradient(135deg, #06346D 0%, #042441 100%);
        --gold-gradient: linear-gradient(135deg, #F3B71B 0%, #d19e0f 100%);
        --text-dark: #1a202c;
        --text-gray: #64748b;
        --bg-light: #f8fafc;
        --success-green: #059669;
        --danger-red: #dc3545;
        --card-shadow: 0 10px 40px rgba(6, 52, 109, 0.12);
        --border-radius: 16px;
    }

    .page-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Page Header Card */
    .page-header-card {
        background: var(--navy-gradient);
        border-radius: var(--border-radius);
        padding: 30px 35px;
        margin-bottom: 25px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .page-header-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -5%;
        width: 250px;
        height: 250px;
        background: var(--gold-gradient);
        opacity: 0.1;
        border-radius: 50%;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 1;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 18px;
    }

    .header-icon {
        width: 55px;
        height: 55px;
        background: var(--gold-gradient);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px rgba(243, 183, 27, 0.35);
    }

    .header-icon i {
        font-size: 24px;
        color: var(--primary-color);
    }

    .header-title {
        color: #ffffff;
        font-size: 1.4rem;
        font-weight: 700;
        margin: 0;
    }

    .header-badge {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        color: #ffffff;
        padding: 10px 22px;
        border-radius: 25px;
        font-weight: 600;
        font-size: 0.95rem;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .header-badge i {
        color: var(--secondary-color);
        margin-right: 8px;
    }

    /* Search Section */
    .search-card {
        background: #ffffff;
        border-radius: var(--border-radius);
        padding: 20px 25px;
        margin-bottom: 25px;
        box-shadow: 0 4px 20px rgba(6, 52, 109, 0.08);
        border: 1px solid rgba(6, 52, 109, 0.08);
    }

    .search-wrapper {
        position: relative;
        max-width: 400px;
    }

    .search-input {
        width: 100%;
        background: var(--bg-light);
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 14px 20px 14px 50px;
        font-size: 1rem;
        font-weight: 500;
        color: var(--text-dark);
        transition: all 0.3s ease;
    }

    .search-input:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 4px rgba(243, 183, 27, 0.15);
        outline: none;
        background: #ffffff;
    }

    .search-input::placeholder {
        color: #94a3b8;
    }

    .search-icon {
        position: absolute;
        left: 18px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
        transition: color 0.3s ease;
    }

    .search-input:focus+.search-icon {
        color: var(--primary-color);
    }

    /* Status Filter */
    .filter-toolbar {
        display: flex;
        gap: 12px;
        align-items: center;
        flex-wrap: wrap;
    }

    .filter-toolbar .search-wrapper {
        flex: 1 1 280px;
    }

    .status-filter {
        flex: 0 0 auto;
        min-width: 200px;
        padding: 14px 18px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background: #f8fafc;
        font-size: 0.95rem;
        color: var(--text-dark);
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .status-filter:focus {
        border-color: var(--secondary-color);
        box-shadow: 0 0 0 4px rgba(243, 183, 27, 0.15);
        outline: none;
        background: #ffffff;
    }

    /* Status Badge */
    .status-badge {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border-radius: 999px;
        font-size: 0.8rem;
        font-weight: 600;
        white-space: nowrap;
    }

    .status-badge.s-pending  { background: #fef3c7; color: #92400e; }
    .status-badge.s-approved { background: #d1fae5; color: #065f46; }
    .status-badge.s-rejected { background: #fee2e2; color: #991b1b; }
    .status-badge.s-payment  { background: #fef3c7; color: #92400e; }

    /* Teams Table Card */
    .table-card {
        background: #ffffff;
        border-radius: var(--border-radius);
        overflow: visible;
        box-shadow: 0 4px 20px rgba(6, 52, 109, 0.08);
        border: 1px solid rgba(6, 52, 109, 0.08);
    }

    .table-header {
        background: var(--primary-color);
        color: #ffffff;
        padding: 18px 25px;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        display: grid;
        grid-template-columns: 100px 1fr 140px 190px 130px 120px;
        gap: 15px;
        align-items: center;
    }

    .teams-table {
        width: 100%;
    }

    .team-row {
        display: grid;
        grid-template-columns: 100px 1fr 140px 190px 130px 120px;
        gap: 15px;
        align-items: center;
        padding: 18px 25px;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        position: relative;
    }

    .team-row:last-child {
        border-bottom: none;
    }

    .team-row:hover {
        background: linear-gradient(90deg, rgba(6, 52, 109, 0.03) 0%, rgba(243, 183, 27, 0.03) 100%);
    }

    /* Team Logo */
    .team-logo-wrapper {
        display: flex;
        justify-content: center;
    }

    .team-logo {
        width: 55px;
        height: 55px;
        border-radius: 12px;
        object-fit: cover;
        border: 3px solid #f1f5f9;
        background: #ffffff;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transition: all 0.3s ease;
    }

    .team-logo:hover {
        transform: scale(1.08);
        box-shadow: 0 6px 20px rgba(6, 52, 109, 0.15);
        border-color: var(--secondary-color);
    }

    /* Team Info */
    .team-info {
        display: flex;
        flex-direction: column;
        gap: 4px;
    }

    .team-name {
        font-weight: 700;
        color: var(--text-dark);
        font-size: 1.05rem;
        text-decoration: none;
        transition: color 0.3s ease;
    }

    .team-name:hover {
        color: var(--primary-color);
    }

    .team-name-sub {
        font-size: 0.85rem;
        color: var(--text-gray);
        font-style: italic;
    }

    /* Contact Info */
    .contact-item {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .contact-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 14px;
    }

    .contact-icon.phone {
        background: rgba(5, 150, 105, 0.1);
        color: var(--success-green);
    }

    .contact-icon.email {
        background: rgba(6, 52, 109, 0.1);
        color: var(--primary-color);
    }

    .contact-text {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .contact-text.phone {
        color: var(--success-green);
    }

    .contact-text.email {
        color: var(--primary-color);
    }

    .no-data {
        color: #cbd5e1;
        font-size: 0.9rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: var(--text-gray);
    }

    .empty-state-icon {
        width: 80px;
        height: 80px;
        background: rgba(6, 52, 109, 0.08);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
    }

    .empty-state-icon i {
        font-size: 32px;
        color: var(--primary-color);
    }

    .empty-state h3 {
        color: var(--text-dark);
        margin-bottom: 8px;
    }

    /* Search Stats & No Results */
    .search-stats {
        padding: 15px 25px;
        background: var(--bg-light);
        color: var(--text-gray);
        font-size: 0.9rem;
        border-top: 1px solid #f1f5f9;
    }

    .no-results {
        text-align: center;
        padding: 50px 20px;
        color: var(--text-gray);
    }

    .no-results i {
        font-size: 3rem;
        color: #cbd5e1;
        margin-bottom: 15px;
    }

    /* Highlight */
    .highlight {
        background: rgba(243, 183, 27, 0.3);
        padding: 0 2px;
        border-radius: 3px;
    }

    /* Export Button */
    .export-actions {
        display: flex;
        justify-content: center;
        position: relative;
        z-index: 10;
    }

    .btn-export {
        background: var(--navy-gradient);
        color: #ffffff;
        border: none;
        padding: 8px 16px;
        border-radius: 8px;
        font-size: 0.85rem;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
        gap: 6px;
    }

    .btn-export:hover {
        background: linear-gradient(135deg, #0a4d7a 0%, #06346D 100%);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(6, 52, 109, 0.25);
    }

    .btn-export::after {
        margin-left: 4px;
    }

    .export-actions .dropdown {
        position: relative;
    }

    .export-actions .dropdown-menu {
        border: 1px solid rgba(6, 52, 109, 0.15);
        border-radius: 10px;
        box-shadow: 0 8px 25px rgba(6, 52, 109, 0.2);
        padding: 8px 0;
        min-width: 200px;
        z-index: 9999 !important;
        position: absolute !important;
        background: #ffffff;
    }

    .export-actions .dropdown-menu.show {
        display: block !important;
    }

    .export-actions .dropdown-item {
        padding: 10px 16px;
        font-size: 0.9rem;
        font-weight: 500;
        color: var(--text-dark);
        transition: all 0.2s ease;
    }

    .export-actions .dropdown-item:hover {
        background: linear-gradient(90deg, rgba(6, 52, 109, 0.08) 0%, rgba(243, 183, 27, 0.08) 100%);
        transform: translateX(5px);
    }

    .export-actions .dropdown-item i {
        width: 20px;
        text-align: center;
    }

    /* Fix for dropdown visibility - row with open dropdown gets higher z-index */
    .team-row:has(.dropdown-menu.show) {
        z-index: 100;
    }

    /* Animations */
    .fade-up {
        opacity: 0;
        transform: translateY(20px);
        animation: fadeUp 0.5s ease forwards;
    }

    @keyframes fadeUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    /* Responsive */
    @media (max-width: 992px) {
        .table-header {
            display: none;
        }

        .team-row {
            grid-template-columns: 70px 1fr;
            gap: 15px;
        }

        .team-row>*:nth-child(3),
        .team-row>*:nth-child(4),
        .team-row>*:nth-child(5),
        .team-row>*:nth-child(6) {
            grid-column: 2;
            padding-left: 0;
        }

        .contact-item {
            margin-top: 8px;
        }

        .export-actions {
            margin-top: 12px;
            justify-content: flex-start;
        }
    }

    @media (max-width: 576px) {
        .header-content {
            flex-direction: column;
            gap: 15px;
            text-align: center;
        }

        .header-left {
            flex-direction: column;
        }

        .page-header-card {
            padding: 25px 20px;
        }

        .team-row {
            grid-template-columns: 1fr;
            text-align: center;
        }

        .team-logo-wrapper {
            justify-content: center;
        }

        .team-info {
            align-items: center;
        }

        .contact-item {
            justify-content: center;
        }
    }
</style>

<div class="page-container">
    <!-- Page Header -->
    <div class="page-header-card fade-up">
        <div class="header-content">
            <div class="header-left">
                <div class="header-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h1 class="header-title"><?= lang('Tournament.team_list') ?? 'รายชื่อทีมที่เข้าร่วมการแข่งขัน' ?></h1>
            </div>
            <?php if (!empty($team)): ?>
                <div class="header-badge">
                    <i class="fas fa-flag-checkered"></i>
                    <span id="teamsCount"><?= count($team) ?></span> <?= lang('Common.teams') ?? 'ทีม' ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-card fade-up" style="animation-delay: 0.1s;">
        <div class="filter-toolbar">
            <div class="search-wrapper">
                <input type="text" id="teamSearch" class="search-input" placeholder="<?= lang('Tournament.search_team') ?? 'ค้นหาทีม...' ?>">
                <i class="fas fa-search search-icon"></i>
            </div>
            <select id="statusFilter" class="status-filter" aria-label="<?= lang('Common.filter_by_status') ?>">
                <option value=""><?= lang('Common.all_status') ?></option>
                <option value="1"><?= lang('Common.approved') ?></option>
                <option value="0"><?= lang('Common.pending_approval') ?></option>
                <option value="3"><?= lang('Common.waiting_payment') ?></option>
                <option value="2"><?= lang('Common.not_approved') ?></option>
            </select>
        </div>
    </div>

    <!-- Teams Table -->
    <div class="table-card fade-up" style="animation-delay: 0.2s;">
        <?php if (empty($team)): ?>
            <div class="empty-state">
                <div class="empty-state-icon">
                    <i class="fas fa-users"></i>
                </div>
                <h3><?= lang('Tournament.no_teams') ?? 'ยังไม่มีทีมลงทะเบียน' ?></h3>
                <p><?= lang('Tournament.waiting_teams') ?? 'รอทีมเข้าร่วมการแข่งขัน' ?></p>
            </div>
        <?php else: ?>
            <!-- Table Header -->
            <div class="table-header">
                <div><?= lang('Common.logo') ?? 'โลโก้' ?></div>
                <div><?= lang('Common.team_name') ?? 'ชื่อทีม' ?></div>
                <div><?= lang('Common.phone') ?? 'เบอร์โทร' ?></div>
                <div><?= lang('Common.email') ?? 'อีเมล' ?></div>
                <div><?= lang('Common.status') ?? 'สถานะ' ?></div>
                <div><?= lang('Common.action') ?? 'จัดการ' ?></div>
            </div>

            <!-- Teams List -->
            <div class="teams-table" id="teamsTableBody">
                <?php
                $logodefault = base_url('public/img/jootball_logo.png');
                $statusMap = [
                    0 => ['label' => lang('Common.pending_approval'), 'class' => 's-pending',  'icon' => 'fa-hourglass-half'],
                    1 => ['label' => lang('Common.approved'),         'class' => 's-approved', 'icon' => 'fa-circle-check'],
                    2 => ['label' => lang('Common.not_approved'),     'class' => 's-rejected', 'icon' => 'fa-circle-xmark'],
                    3 => ['label' => lang('Common.waiting_payment'),  'class' => 's-payment',  'icon' => 'fa-money-bill-wave'],
                ];
                foreach ($team as $key => $value):
                    $rs = isset($value['register_status']) ? (int)$value['register_status'] : 0;
                    $st = $statusMap[$rs] ?? $statusMap[0];
                ?>
                    <div class="team-row" data-status="<?= $rs ?>">
                        <div class="team-logo-wrapper">
                            <img src="<?= !empty($value['logo']) ? base_url(esc($value['logo'])) : $logodefault ?>"
                                alt="<?= esc($value['name'] ?? 'Team Logo') ?>"
                                class="team-logo"
                                onerror="this.src='<?= $logodefault ?>'">
                        </div>
                        <div class="team-info">
                            <a href="<?= base_url('detail/team/' . $value['id']) ?>" target="_blank" class="team-name">
                                <?= esc($value['name']) ?>
                            </a>
                            <?php if (!empty($value['name_en'])): ?>
                                <div class="team-name-sub"><?= esc($value['name_en']) ?></div>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if (!empty($value['contact_phone'])): ?>
                                <div class="contact-item">
                                    <div class="contact-icon phone">
                                        <i class="fas fa-phone"></i>
                                    </div>
                                    <span class="contact-text phone"><?= esc($value['contact_phone']) ?></span>
                                </div>
                            <?php else: ?>
                                <span class="no-data">-</span>
                            <?php endif; ?>
                        </div>
                        <div>
                            <?php if (!empty($value['contact_email'])): ?>
                                <div class="contact-item">
                                    <div class="contact-icon email">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <span class="contact-text email"><?= esc($value['contact_email']) ?></span>
                                </div>
                            <?php else: ?>
                                <span class="no-data">-</span>
                            <?php endif; ?>
                        </div>
                        <!-- Status -->
                        <div>
                            <span class="status-badge <?= $st['class'] ?>">
                                <i class="fas <?= $st['icon'] ?>"></i>
                                <?= esc($st['label']) ?>
                            </span>
                        </div>
                        <!-- Export Dropdown (แสดงเฉพาะทีมที่ยืนยันส่งรายชื่อแล้ว) -->
                        <div class="export-actions">
                            <?php if (!empty($value['player_list_status']) && intval($value['player_list_status']) === 1): ?>
                            <div class="dropdown">
                                <button class="btn-export dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-download"></i> <?= lang('Tournament.export') ?? 'Export' ?>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li>
                                        <a class="dropdown-item" href="<?= base_url('team/export-player-chart?id=' . $value['id'] . '&tour_id=' . $tour_id) ?>" target="_blank">
                                            <i class="fas fa-image text-primary me-2"></i> <?= lang('Tournament.export_player_chart') ?? 'Player Chart' ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= base_url('team/export-player-card?id=' . $value['id'] . '&tour_id=' . $tour_id) ?>" target="_blank">
                                            <i class="fas fa-id-card text-success me-2"></i> <?= lang('Tournament.export_player_card') ?? 'Player Card' ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= base_url('team/export-manager-card?id=' . $value['id'] . '&tour_id=' . $tour_id) ?>" target="_blank">
                                            <i class="fas fa-user-tie text-info me-2"></i> <?= lang('Tournament.export_manager_card') ?>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="<?= base_url('team/player-document-list?id=' . $value['id'] . '&tour_id=' . $tour_id) ?>" target="_blank">
                                            <i class="fas fa-list-alt text-warning me-2"></i> <?= lang('Tournament.export_player_list') ?? 'Player List' ?>
                                        </a>
                                    </li>
                                </ul>
                            </div>
                            <?php else: ?>
                            <span class="badge bg-secondary"><i class="fas fa-clock me-1"></i><?= lang('Common.not_submitted_yet') ?? 'ยังไม่ส่งรายชื่อ' ?></span>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>

            <!-- Search Stats -->
            <div class="search-stats" id="searchStats" style="display: none;">
                <?= lang('Common.showing') ?? 'แสดงผล' ?> <span id="visibleTeams">0</span> <?= lang('Common.from') ?? 'จาก' ?> <span id="totalTeams"><?= count($team) ?></span> <?= lang('Common.teams') ?? 'ทีม' ?>
            </div>

            <!-- No Results -->
            <div class="no-results" id="noResults" style="display: none;">
                <i class="fas fa-search"></i>
                <h3><?= lang('Tournament.no_search_results') ?? 'ไม่พบทีมที่ค้นหา' ?></h3>
                <p><?= lang('Tournament.try_different_search') ?? 'ลองเปลี่ยนคำค้นหาหรือตรวจสอบการสะกดอีกครั้ง' ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php $this->endSection(); ?>
<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        const searchInput = $('#teamSearch');
        const statusFilter = $('#statusFilter');
        const teamRows = $('.team-row');
        const teamsCountBadge = $('#teamsCount');
        const searchStats = $('#searchStats');
        const noResults = $('#noResults');
        const totalTeams = teamRows.length;

        function clearHighlights() {
            teamRows.find('.team-name, .team-name-sub, .contact-text').each(function() {
                $(this).html($(this).text());
            });
        }

        function applyFilters() {
            const searchTerm = (searchInput.val() || '').toLowerCase().trim();
            const statusVal = statusFilter.val();
            let visibleCount = 0;

            clearHighlights();

            teamRows.each(function() {
                const row = $(this);
                const matchStatus = statusVal === '' || String(row.data('status')) === statusVal;

                let matchSearch = true;
                if (searchTerm !== '') {
                    const teamName = row.find('.team-name').text().toLowerCase();
                    const teamNameSub = row.find('.team-name-sub').text().toLowerCase();
                    const phone = row.find('.contact-text.phone').text().toLowerCase();
                    const email = row.find('.contact-text.email').text().toLowerCase();
                    matchSearch = teamName.includes(searchTerm) ||
                                  teamNameSub.includes(searchTerm) ||
                                  phone.includes(searchTerm) ||
                                  email.includes(searchTerm);
                }

                if (matchStatus && matchSearch) {
                    row.show();
                    visibleCount++;
                    if (searchTerm !== '') {
                        highlightText(row.find('.team-name'), searchTerm);
                        highlightText(row.find('.team-name-sub'), searchTerm);
                    }
                } else {
                    row.hide();
                }
            });

            const isFiltered = searchTerm !== '' || statusVal !== '';
            if (!isFiltered) {
                searchStats.hide();
                noResults.hide();
            } else if (visibleCount === 0) {
                noResults.show();
                searchStats.hide();
            } else {
                noResults.hide();
                searchStats.show();
                $('#visibleTeams').text(visibleCount);
            }
            teamsCountBadge.text(visibleCount);
        }

        function highlightText(element, searchTerm) {
            if (element.length === 0) return;
            const text = element.text();
            const regex = new RegExp('(' + escapeRegExp(searchTerm) + ')', 'gi');
            element.html(text.replace(regex, '<span class="highlight">$1</span>'));
        }

        function escapeRegExp(string) {
            return string.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        }

        searchInput.on('input', applyFilters);
        statusFilter.on('change', applyFilters);

        searchInput.on('keydown', function(e) {
            if (e.key === 'Escape') {
                $(this).val('');
                applyFilters();
            }
        });
    });
</script>
<?php $this->endSection(); ?>