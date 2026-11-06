<?php $this->extend('template/home') ?>
<?php $this->section('content'); ?>

<div class="tp-page">
    <!-- Team Header Banner -->
    <div class="tp-header">
        <div class="container">
            <a href="javascript:history.back()" class="tp-back-btn">
                <i class="fas fa-arrow-left"></i> <?= lang('Common.back') ?>
            </a>
            <div class="tp-header-content">
                <img id="team-logo" src="https://cdn-icons-png.flaticon.com/512/53/53283.png"
                     onerror="this.src='https://cdn-icons-png.flaticon.com/512/53/53283.png'"
                     class="tp-team-logo" alt="Team Logo">
                <div class="tp-team-info">
                    <h2 id="team-name" class="tp-team-name">-</h2>
                    <div class="tp-team-meta">
                        <span id="team-organize_name" class="tp-org-name">-</span>
                        <span class="tp-meta-divider">|</span>
                        <span><i class="fas fa-user-tie"></i> <?= lang('Common.coach') ?> : <span id="team-coach">-</span></span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="container py-4">
        <div class="row g-4">

            <!-- Players Section -->
            <div class="col-lg-8 col-md-12">
                <div class="tp-card">
                    <div class="tp-card-header">
                        <h5 class="tp-card-title">
                            <i class="fas fa-users"></i> <?= lang('Common.player_list') ?>
                        </h5>
                        <span id="playerCount" class="tp-badge">0</span>
                    </div>
                    <div class="tp-card-body">
                        <div id="playersGrid" class="row g-3">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Match History Section -->
            <div class="col-lg-4 col-md-12">
                <div class="tp-card">
                    <div class="tp-card-header">
                        <h5 class="tp-card-title">
                            <i class="fas fa-calendar-alt"></i> <?= lang('Tournament.history') ?>
                        </h5>
                    </div>
                    <div id="his" class="tp-card-body tp-match-list">
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<style>
/* ===== Navbar Styling for Map Page ===== */
.navbar .navbar-brand,
.navbar .navbar-brand:hover,
.navbar .nav-link,
.navbar .nav-link:hover,
.navbar .nav-link:focus,
.navbar .dropdown-toggle,
.navbar .dropdown-toggle:hover,
.navbar .navbar-nav .nav-link,
.navbar .navbar-nav.ms-auto .nav-link{
    color: #000000 !important;
}

.navbar .nav-link:hover,
.navbar .dropdown-toggle:hover,
.navbar .navbar-nav .nav-link:hover,
.navbar .navbar-nav.ms-auto .nav-link:hover{
    color: #f3b71a !important;
}

.navbar{
    background-color: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.1) !important;
}

.navbar .sponsor-badge{
    background: rgba(0, 0, 0, 0.05);
    border-left: 1px solid rgba(0, 0, 0, 0.1);
}
/* ===== End Navbar ===== */

/* ===== Team Profile Page ===== */
.tp-page {
    background: #f8f9fa;
    min-height: 100vh;
    padding-top: 70px;
}

/* --- Header Banner --- */
.tp-header {
    background: linear-gradient(135deg, var(--primary-blue, #1e3a8a) 0%, #2c5282 60%, #3b82f6 100%);
    padding: 2rem 0;
    position: relative;
    overflow: hidden;
}
.tp-header::before {
    content: '';
    position: absolute;
    top: -50%;
    right: -10%;
    width: 400px;
    height: 400px;
    border-radius: 50%;
    background: rgba(255,255,255,0.05);
    pointer-events: none;
}
.tp-header::after {
    content: '';
    position: absolute;
    bottom: -30%;
    left: -5%;
    width: 300px;
    height: 300px;
    border-radius: 50%;
    background: rgba(243,183,26,0.1);
    pointer-events: none;
}

.tp-back-btn {
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 0.85rem;
    margin-bottom: 1rem;
    transition: color 0.2s;
}
.tp-back-btn:hover {
    color: #fff;
}

.tp-header-content {
    display: flex;
    align-items: center;
    gap: 1.5rem;
}

.tp-team-logo {
    width: 90px;
    height: 90px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid rgba(255,255,255,0.3);
    background: #fff;
    flex-shrink: 0;
}

.tp-team-name {
    color: #fff;
    font-weight: 700;
    font-size: 1.6rem;
    margin-bottom: 0.25rem;
}

.tp-team-meta {
    color: rgba(255,255,255,0.75);
    font-size: 0.9rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
    flex-wrap: wrap;
}
.tp-team-meta i {
    font-size: 0.8rem;
}
.tp-meta-divider {
    opacity: 0.4;
}

/* --- Cards --- */
.tp-card {
    background: #fff;
    border-radius: 12px;
    border: 1px solid #e2e8f0;
    box-shadow: 0 1px 3px rgba(0,0,0,0.06);
    overflow: hidden;
}

.tp-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 1rem 1.25rem;
    border-bottom: 1px solid #e2e8f0;
    background: #fafbfc;
}

.tp-card-title {
    margin: 0;
    font-weight: 600;
    font-size: 1rem;
    color: var(--primary-blue, #1e3a8a);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.tp-card-title i {
    font-size: 0.9rem;
    opacity: 0.7;
}

.tp-badge {
    background: var(--primary-blue, #1e3a8a);
    color: #fff;
    font-size: 0.75rem;
    font-weight: 600;
    padding: 0.2rem 0.6rem;
    border-radius: 20px;
    min-width: 28px;
    text-align: center;
}

.tp-card-body {
    padding: 1.25rem;
}

/* --- Player Cards --- */
.tp-player-card {
    background: #fff;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    padding: 1rem;
    text-align: center;
    transition: all 0.25s ease;
    cursor: pointer;
    height: 100%;
}
.tp-player-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 8px 20px rgba(30,58,138,0.1);
    border-color: var(--primary-blue, #1e3a8a);
}

.tp-player-img {
    width: 64px;
    height: 64px;
    border-radius: 50%;
    object-fit: cover;
    margin-bottom: 0.5rem;
    border: 2px solid #e2e8f0;
    transition: border-color 0.2s;
}
.tp-player-card:hover .tp-player-img {
    border-color: var(--primary-blue, #1e3a8a);
}

.tp-player-name {
    font-weight: 600;
    font-size: 0.8rem;
    color: #1a202c;
    margin-bottom: 0.15rem;
    line-height: 1.3;
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.tp-player-number {
    font-weight: 700;
    font-size: 0.75rem;
    color: var(--primary-blue, #1e3a8a);
    margin-bottom: 0.15rem;
}

.tp-player-position {
    font-size: 0.7rem;
    color: #718096;
    background: #f1f5f9;
    display: inline-block;
    padding: 0.1rem 0.5rem;
    border-radius: 10px;
}

/* --- Match List --- */
.tp-match-list {
    max-height: 70vh;
    overflow-y: auto;
}
.tp-match-list::-webkit-scrollbar {
    width: 4px;
}
.tp-match-list::-webkit-scrollbar-thumb {
    background: #cbd5e0;
    border-radius: 4px;
}

.tp-day-header {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--primary-blue, #1e3a8a);
    padding: 0.5rem 0;
    margin-top: 0.75rem;
    border-bottom: 1px solid #e2e8f0;
}
.tp-day-header:first-child {
    margin-top: 0;
}

.tp-match-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 8px;
    padding: 0.75rem;
    margin-top: 0.5rem;
    transition: all 0.2s ease;
}
.tp-match-card:hover {
    border-color: #cbd5e0;
    box-shadow: 0 2px 8px rgba(0,0,0,0.06);
}

.tp-match-date {
    font-size: 0.7rem;
    color: #a0aec0;
    margin-bottom: 0.35rem;
}

.tp-match-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.15rem 0;
    font-size: 0.85rem;
    font-weight: 500;
    color: #2d3748;
}
.tp-match-row .tp-team-link {
    flex: 1;
    cursor: pointer;
    transition: color 0.2s;
}
.tp-match-row .tp-team-link:hover {
    color: var(--primary-blue, #1e3a8a);
}
.tp-match-score {
    width: 30px;
    text-align: center;
    font-weight: 700;
    color: var(--primary-blue, #1e3a8a);
}

.tp-penalty-tag {
    font-size: 0.65rem;
    background: var(--primary-yellow, #f3b71a);
    color: #fff;
    padding: 0 0.3rem;
    border-radius: 3px;
    font-weight: 700;
    margin-left: 0.3rem;
}

/* --- Responsive --- */
@media (max-width: 768px) {
    .tp-page {
        padding-top: 55px;
    }
    .tp-header {
        padding: 1.25rem 0;
    }
    .tp-team-logo {
        width: 60px;
        height: 60px;
    }
    .tp-team-name {
        font-size: 1.2rem;
    }
    .tp-team-meta {
        font-size: 0.8rem;
    }
    .tp-meta-divider {
        display: none;
    }
    .tp-team-meta {
        flex-direction: column;
        gap: 0.2rem;
    }
}
</style>
<script>
    var tourId = '<?php echo $tour_id;?>';
    var team_id = '<?php echo $team_id;?>';
    var base_url='<?php echo base_url();?>';
    const language = '<?=session()->get('language')?>';
    const unknown_date_label = '<?=lang('Tournament.unknown_date')?>';
    const DEFAULT_LOGO='https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg';
    $(function(){
        getTourRank();
        teamStatInfo();
        getTourPlayer();
    });

    $(function () {
        $('.navbar-nav a[href^="#"]').each(function () {
            const hash = $(this).attr('href');
            $(this).attr('href', base_url + hash);
        });
    });

    function formatThaiDate(d){
        if (!d) return '';
        const x = new Date(d);

        const day = x.getDate();
        const month = String(x.getMonth() + 1).padStart(2, '0');
        const year = x.getFullYear() + 543;

        return `${day}/${month}/${year}`;
    }

    function getTourRank() {
        $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-match-for-prog-rank-result') ?>',{tour_id:tourId},res=>{
            if(!res?.data)return;
            renderMatches(res.data);
        });
    }

    function renderMatches(data){
        let h = '';
        let lastKey = '';

        // Show only the matches this team (team_id from the query string) actually played in.
        const tid = String(team_id || '');
        if (tid) {
            data = data.filter(m => String(m.team1_id) === tid || String(m.team2_id) === tid);
        }

        data.forEach((m, i) => {
            const key = `${m.match_round}_${m.state_id}`;

            if (key !== lastKey) {
                h += `
                    <div class="tp-day-header">
                        ${m.match_round} &middot; ${formatThaiDate(m.date)}
                    </div>
                `;
                lastKey = key;
            }

            const team1 = language=='th'?m.team1_name:m.team1_name_en;
            const team2 = language=='th'?m.team2_name:m.team2_name_en;

            const score1 = m.team1_score ?? 0;
            const score2 = m.team2_score ?? 0;

            const dateText = m.date ? formatThaiDate(m.date) : unknown_date_label;
            const timeText = m.time?.slice(0,5) ?? '';

            const pen1 = ( (m.team1_id==m.winner_team_id || m.result==1) && m.is_penalty_used==1);
            const pen2 = ( (m.team2_id==m.winner_team_id || m.result==2) && m.is_penalty_used==1);

            h += `
                <div class="tp-match-card">
                    <div class="tp-match-date">
                        <i class="far fa-clock"></i> ${dateText} ${timeText ? '- ' + timeText : ''}
                    </div>
                    <div class="tp-match-row">
                        <span class="tp-team-link" onclick="teamClick(${tourId},${m.team1_id})">${team1 ?? 'Team 1'}${pen1?'<span class="tp-penalty-tag">P</span>':''}</span>
                        <span class="tp-match-score">${score1}</span>
                    </div>
                    <div class="tp-match-row">
                        <span class="tp-team-link" onclick="teamClick(${tourId},${m.team2_id})">${team2 ?? 'Team 2'}${pen2?'<span class="tp-penalty-tag">P</span>':''}</span>
                        <span class="tp-match-score">${score2}</span>
                    </div>
                </div>
            `;
        });

        $('#his').append(h);
    }

    function teamStatInfo() {
        $.getJSON('<?= base_url('admin/tournament-pg/get-team-statistics') ?>',{team_id:team_id},res=>{
            if(!res?.data)return;

            $("#team-name").html(language=='TH'?res.data.team_info.name:res.data.team_info.name_en);
            $("#team-organize_name").html(res.data.team_info.organize_name);
            $("#team-coach").html(res.data.team_info.coach_name);
        });
    }

    function getTourPlayer() {
        const NO_PROFILE = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
        const NO_TEAM    = DEFAULT_LOGO;

        $.getJSON(
            '<?= base_url('admin/tournament-pg/get-tournament-players') ?>',
            { team_id: team_id },
            res => {

                if (!res || res.status !== 'success' || !Array.isArray(res.data)) return;

                const $grid = $('#playersGrid');
                $grid.empty();

                $('#playerCount').text(res.data.length);

                res.data.forEach(p => {

                const firstName = p.first_name?.trim() || 'N/A';
                const lastName  = p.last_name?.trim()  || '';
                const fullName  = (firstName + ' ' + lastName).trim() || 'N/A';

                const position = p.position_name_th || p.position_name_en || 'N/A';

                const playerPhoto = p.image_path
                    ? `<?= base_url() ?>/${p.image_path}`
                    : NO_PROFILE;

                const shirt_number = p.shirt_number;

                const card_info = `
                    <div class="col-xl-3 col-lg-4 col-md-3 col-6">
                        <div class="tp-player-card" onclick="window.location.replace('${p.link_report_people}')">
                            <img src="${playerPhoto}" onerror="this.src='${NO_PROFILE}'" class="tp-player-img" alt="${fullName}">
                            <div class="tp-player-number">#${shirt_number}</div>
                            <div class="tp-player-name">${fullName}</div>
                            <div class="tp-player-position">${position}</div>
                        </div>
                    </div>`;

                $grid.append(card_info);
                });
            }
        );
    }

    function teamClick(tour_id,team_id) {
        window.location.replace("?tour_id="+tour_id+"&team_id="+team_id);
    }

</script>
<?php $this->endSection(); ?>
