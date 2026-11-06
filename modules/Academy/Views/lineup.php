<?php $this->extend('template/layout') ?>

<?php $this->section('content') ?>

<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="container mt-4">
        <!-- Page Header -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                                <i class="fas fa-clipboard-list text-primary fa-lg"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark"><?= lang('Common.lineup') ?></h4>
                                <p class="mb-0 text-muted small">จัดการรายชื่อผู้เล่นและการแข่งขัน</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Team Selection -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-primary">
                            <i class="fas fa-users me-2"></i>เลือกทีม
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <?php if (!empty($teams)): ?>
                                <?php foreach ($teams as $team): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card team-card h-100 border-0 shadow-sm <?= isset($selected_team_id) && $selected_team_id == $team['id'] ? 'border-primary active' : '' ?>"
                                             style="cursor: pointer; transition: all 0.2s;"
                                             onclick="selectTeam(<?= $team['id'] ?>)">
                                            <div class="card-body text-center p-4">
                                                <div class="avatar-lg bg-primary bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px; overflow: hidden;">
                                                    <?php if (!empty($team['logo'])): ?>
                                                        <img src="<?= base_url($team['logo']) ?>"
                                                             alt="<?= esc($team['name']) ?>"
                                                             style="width: 100%; height: 100%; object-fit: cover;"
                                                             onerror="this.style.display='none'; this.parentElement.innerHTML='<i class=\'fas fa-users text-primary fa-lg\'></i>';">
                                                    <?php else: ?>
                                                        <i class="fas fa-users text-primary fa-lg"></i>
                                                    <?php endif; ?>
                                                </div>
                                                <h6 class="card-title fw-bold mb-2"><?= esc($team['name']) ?></h6>
                                                <?php if (isset($selected_team_id) && $selected_team_id == $team['id']): ?>
                                                    <div class="mt-2">
                                                        <span class="badge bg-primary"><i class="fas fa-check me-1"></i>กำลังดู</span>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-users-slash text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 text-muted">ไม่มีทีมใน Academy</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tournament Selection (show only when team is selected) -->
        <div class="row mb-4" id="tournamentSection" style="display: none;">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-success">
                            <i class="fas fa-trophy me-2"></i>เลือกทัวร์นาเมนต์
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-3">
                            <?php if (!empty($tournaments)): ?>
                                <?php foreach ($tournaments as $tournament): ?>
                                    <div class="col-md-6 col-lg-4">
                                        <div class="card tournament-card h-100 border-0 shadow-sm" 
                                             style="cursor: pointer; transition: all 0.2s;"
                                             onclick="selectTournament(<?= $tournament['tour_id'] ?>)">
                                            <div class="card-body text-center p-4">
                                                <?php if (!empty($tournament['logo'])): ?>
                                                    <img src="<?= base_url($tournament['logo']) ?>" 
                                                         alt="<?= esc($tournament['name']) ?>" 
                                                         class="rounded-circle mb-3" 
                                                         style="width: 80px; height: 80px; object-fit: cover;">
                                                <?php else: ?>
                                                    <div class="avatar-lg bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3" style="width: 80px; height: 80px;">
                                                        <i class="fas fa-trophy text-success fa-lg"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <h6 class="card-title fw-bold mb-2"><?= esc($tournament['name']) ?></h6>
                                                <p class="card-text text-muted small mb-2">
                                                    <?= esc($tournament['name_en']) ?>
                                                </p>
                                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                                    <span class="badge bg-success bg-opacity-10 text-success">
                                                        <i class="fas fa-calendar me-1"></i>
                                                        <?php 
                                                        $startDate = !empty($tournament['tournament_start_date']) ? $tournament['tournament_start_date'] : $tournament['start_date'];
                                                        $endDate = !empty($tournament['tournament_end_date']) ? $tournament['tournament_end_date'] : $tournament['end_date'];
                                                        echo $startDate ? date('d/m/Y', strtotime($startDate)) : '-';
                                                        ?>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12 text-center py-5">
                                    <i class="fas fa-trophy-off text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 text-muted">ไม่มีทัวร์นาเมนต์ที่เข้าร่วม</p>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Matches Section -->
        <div class="row" id="matchesSection" style="display: none;">
            <div class="col-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                        <h5 class="mb-0 fw-bold text-success">
                            <i class="fas fa-futbol me-2"></i>การแข่งขัน
                            <span id="selectedInfo" class="text-muted fs-6 ms-2"></span>
                        </h5>
                        <span class="badge bg-success" id="matchCountBadge">0 นัด</span>
                    </div>
                    <div class="card-body p-4">
                        <div id="matchesContainer"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.team-card:hover, .tournament-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 16px rgba(0,0,0,0.1) !important;
}
.team-card:hover .card-title, .tournament-card:hover .card-title {
    color: #0d6efd !important;
}
.team-card.active, .tournament-card.active {
    border: 2px solid #0d6efd !important;
    background-color: #e7f1ff !important;
}
.match-row:hover {
    background-color: #f8f9fa;
}
</style>

<script>
// Store data
let selectedTeamId = null;
let selectedTeamName = null;
let selectedTournamentId = null;

function selectTeam(teamId) {
    // Update active state on team cards
    document.querySelectorAll('.team-card').forEach(card => {
        card.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    selectedTeamId = teamId;
    
    // Get team name
    const teamCard = event.currentTarget;
    selectedTeamName = teamCard.querySelector('.card-title').textContent.trim();
    
    // Show tournament section
    document.getElementById('tournamentSection').style.display = 'block';
    
    // Hide matches section until tournament is selected
    document.getElementById('matchesSection').style.display = 'none';
    
    // Load tournaments via AJAX
    loadTournaments(teamId);
}

function loadTournaments(teamId) {
    fetch('<?= base_url('lineup/get-tournaments') ?>?team_id=' + teamId)
        .then(response => response.json())
        .then(data => {
            console.log('Tournaments:', data);
            if (data.status === 'success' && data.data.length > 0) {
                renderTournaments(data.data);
            } else {
                renderNoTournaments();
            }
        })
        .catch(error => {
            console.error('Error loading tournaments:', error);
            renderNoTournaments();
        });
}

function renderTournaments(tournaments) {
    const container = document.querySelector('#tournamentSection .card-body .row.g-3');
    let html = '';
    
    tournaments.forEach(tournament => {
        const startDate = tournament.tournament_start_date || tournament.start_date;
        const endDate = tournament.tournament_end_date || tournament.end_date;
        
        // Format dates to Thai format
        const formatDate = (dateStr) => {
            if (!dateStr) return '-';
            const date = new Date(dateStr);
            return date.toLocaleDateString('th-TH', {
                day: '2-digit',
                month: 'short',
                year: 'numeric'
            });
        };
        
        html += `
            <div class="col-md-6 col-lg-4">
                <div class="card tournament-card h-100 border-0 shadow-sm" 
                     style="cursor: pointer; transition: all 0.2s;"
                     onclick="selectTournament(${tournament.tour_id})">
                    <div class="card-body text-center p-4">
                        <div class="tournament-logo-container mb-3" style="width: 100%; display: flex; justify-content: center;">
                            ${tournament.logo_url ? 
                                `<img src="${tournament.logo_url}" alt="${tournament.name}" 
                                      class="rounded-circle shadow-sm" 
                                      style="width: 90px; height: 90px; object-fit: contain; background: #f8f9fa; padding: 5px;"
                                      onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                <div class="avatar-lg bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 90px; height: 90px; display: none; background: #f8f9fa;">
                                    <i class="fas fa-trophy text-success fa-2x"></i>
                                </div>` :
                                `<div class="avatar-lg bg-success bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center" 
                                     style="width: 90px; height: 90px; background: #f8f9fa;">
                                    <i class="fas fa-trophy text-success fa-2x"></i>
                                </div>`
                            }
                        </div>
                        <h6 class="card-title fw-bold mb-2" style="font-size: 0.9rem; line-height: 1.4; min-height: 2.8rem; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">${tournament.name}</h6>
                        ${tournament.name_en ? `<p class="card-text text-muted small mb-2" style="font-size: 0.7rem;">${tournament.name_en}</p>` : ''}
                        <div class="d-flex justify-content-center gap-2 flex-wrap mb-2">
                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2" style="font-size: 0.7rem;">
                                <i class="fas fa-calendar-alt me-1"></i>
                                เริ่ม: ${formatDate(startDate)}
                            </span>
                        </div>
                        ${endDate ? `
                        <div class="d-flex justify-content-center gap-2 flex-wrap">
                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2" style="font-size: 0.7rem;">
                                <i class="fas fa-flag-checkered me-1"></i>
                                สิ้นสุด: ${formatDate(endDate)}
                            </span>
                        </div>
                        ` : ''}
                    </div>
                </div>
            </div>
        `;
    });
    
    container.innerHTML = html;
}

function renderNoTournaments() {
    const container = document.querySelector('#tournamentSection .card-body .row.g-3');
    container.innerHTML = `
        <div class="col-12 text-center py-5">
            <i class="fas fa-trophy-off text-muted" style="font-size: 3rem;"></i>
            <p class="mt-3 text-muted">ไม่มีทัวร์นาเมนต์ที่ทีมนี้เข้าร่วม</p>
        </div>
    `;
}

function selectTournament(tournamentId) {
    // Update active state on tournament cards
    document.querySelectorAll('.tournament-card').forEach(card => {
        card.classList.remove('active');
    });
    event.currentTarget.classList.add('active');
    
    selectedTournamentId = tournamentId;
    
    // Load matches via AJAX
    loadMatches(selectedTeamId, tournamentId);
}

function loadMatches(teamId, tournamentId) {
    let url = '<?= base_url('lineup/get-matches') ?>?team_id=' + teamId;
    if (tournamentId) {
        url += '&tournament_id=' + tournamentId;
    }
    
    fetch(url)
        .then(response => response.json())
        .then(data => {
            console.log('Matches:', data);
            if (data.status === 'success' && data.data.length > 0) {
                renderMatches(data.data);
            } else {
                renderNoMatches();
            }
        })
        .catch(error => {
            console.error('Error loading matches:', error);
            renderNoMatches();
        });
}

function renderMatches(matches) {
    // Show matches section
    document.getElementById('matchesSection').style.display = 'block';
    
    // Update count
    document.getElementById('matchCountBadge').textContent = matches.length + ' นัด';
    
    // Update info text
    const infoText = matches.length > 0 ? `(${matches[0].tournament_name})` : '(ไม่มีการแข่งขัน)';
    document.getElementById('selectedInfo').textContent = infoText;

    const container = document.getElementById('matchesContainer');
    
    if (matches.length === 0) {
        renderNoMatches();
        return;
    }

    // Build matches table
    let html = `
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="py-3">เวลา</th>
                        <th class="py-3">ทัวร์นาเมนต์</th>
                        <th class="py-3 text-center">ทีมเหย้า</th>
                        <th class="py-3 text-center"></th>
                        <th class="py-3 text-center">ทีมเยือน</th>
                        <th class="py-3">สนาม</th>
                        <th class="py-3 text-end">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
    `;

    matches.forEach(match => {
        const isHome = match.team1_id == selectedTeamId;
        const isAway = match.team2_id == selectedTeamId;
        const team1DisplayName = isHome ? selectedTeamName : match.team1_name;
        const team2DisplayName = isAway ? selectedTeamName : match.team2_name;
        
        html += `
            <tr class="match-row">
                <td class="py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="fas fa-clock text-muted"></i>
                        <span class="fw-bold">${match.match_time}</span>
                    </div>
                </td>
                <td class="py-3">
                    <span class="text-muted small">${match.tournament_name}</span>
                    <br>
                    <span class="badge bg-secondary">รอบ ${match.round}</span>
                </td>
                <td class="py-3 text-end">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <span class="fw-bold">${team1DisplayName}</span>
                        ${match.team1_logo ? `<img src="${match.team1_logo}" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">` : ''}
                    </div>
                </td>
                <td class="py-3 text-center">
                    <span class="badge bg-dark">VS</span>
                </td>
                <td class="py-3 text-start">
                    <div class="d-flex align-items-center gap-2">
                        ${match.team2_logo ? `<img src="${match.team2_logo}" class="rounded-circle" style="width: 30px; height: 30px; object-fit: cover;">` : ''}
                        <span class="fw-bold">${team2DisplayName}</span>
                    </div>
                </td>
                <td class="py-3">
                    <i class="fas fa-map-marker-alt text-danger me-1"></i>
                    <span class="text-muted small">${match.venue}</span>
                </td>
                <td class="py-3 text-end">
                    <a href="<?= base_url('lineup/form') ?>?team_id=${match.team1_id}&tournament_id=${match.tour_id}&match_id=${match.match_id}"
                       class="btn btn-sm btn-primary">
                        <i class="fas fa-clipboard-list me-1"></i>
                        จัดการรายชื่อ
                    </a>
                </td>
            </tr>
        `;
    });

    html += `
                </tbody>
            </table>
        </div>
    `;

    container.innerHTML = html;
}

function renderNoMatches() {
    document.getElementById('matchesSection').style.display = 'block';
    const container = document.getElementById('matchesContainer');
    container.innerHTML = `
        <div class="text-center py-5">
            <i class="fas fa-calendar-check text-muted" style="font-size: 3rem;"></i>
            <p class="mt-3 text-muted">ไม่มีการแข่งขัน</p>
        </div>
    `;
    document.getElementById('matchCountBadge').textContent = '0 นัด';
    document.getElementById('selectedInfo').textContent = '(ไม่มีการแข่งขัน)';
}
</script>

<?php $this->endSection() ?>
