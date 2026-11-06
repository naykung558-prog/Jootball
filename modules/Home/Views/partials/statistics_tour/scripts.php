<script>
    var tourId = '<?php echo $tour_id;?>';
    var team_id = 0;
    var base_url='<?php echo base_url();?>';
    const language = '<?=session()->get('language')?>';
    const group_label = '<?=lang('Tournament.group')?>';
    const team_label = '<?=lang('Tournament.team')?>';
    const minute_label = '<?=lang('Common.minute')?>';
    const played_label = '<?=lang('Tournament.played')?>';
    const league_table_label = '<?=lang('Tournament.league_table')?>';
    const won_label = '<?=lang('Tournament.won')?>';
    const drawn_label = '<?=lang('Tournament.drawn')?>';
    const lost_label = '<?=lang('Tournament.lost')?>';
    const for_label = '<?=lang('Tournament.for')?>';
    const against_label = '<?=lang('Tournament.against')?>';
    const goal_diff_label = '<?=lang('Tournament.goal_diff')?>';
    const points_label = '<?=lang('Tournament.points')?>';
    const unknown_team_label = '<?=lang('Tournament.unknown_team')?>';
    const unknown_date_label = '<?=lang('Tournament.unknown_date')?>';
    const tba_label = '<?=lang('Tournament.tba')?>';
    const tournament_results_label = 'Ranking';
    const winner_label = 'WINNER';
    const honorable_mentions_label = 'Honorable Mentions';
    const player_show_more_label = '<?=lang('Tournament.player_show_more')?>';
    const number_label = '<?=lang('Tournament.number')?>';
    const position_label = '<?=lang('Tournament.position')?>';
    //const DEFAULT_LOGO='https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg';
    const DEFAULT_LOGO = '<?= base_url('public/img/jootball_logo.png') ?>';
    const stadium_label = '<?=lang('Tournament.stadium')?>';
    const NO_PROFILE = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
    const round_match = '<?= lang('Tournament.round_match') ?>';

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

function tourTeams(){
    // alert(tourId);
    //TourList
    $.getJSON('<?= base_url('admin/tournament-pg/get-tour-teams') ?>',{tour_id:tourId},res=>{
        if(!res?.data)return;
            const team = res.data;

            //const $wrapper = $('#teamListWrapper');
            const $wrapper = $('#teamListWrapper').empty();

            $.each(team, function (i, t) {

                if(t.tour_id==tourId) {
                   //console.log(t.tour_id+':::'+tourId+':::'+team_id);
                    if(team_id==0) {
                        team_id = t.team_id;
                        $("#team-name").html(language=='th'?t.team_name:t.team_name_en);
                        //alert(team_id);
                        teamStatInfo();
                        getTourPlayer();
                    }

                    const logo = t.team_logo 
                    ? `<?= base_url() ?>/${t.team_logo}`
                    : 'https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg';

                    $wrapper.append(`
                    <div class="swiper-slide">
                        <div class="tournament-item" data-id="${t.team_id}" data-name="${language=='th'?t.team_name:t.team_name_en}" `+(team_id==t.team_id?'style="border:#FF3333 solid"':'')+`>
                        <img src="${logo}" class="tournament-logo"
                            onerror="this.src='https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg'">
                        <div class="tournament-name">${language=='th'?t.team_name:t.team_name_en}</div>
                        <!-- <div class="tournament-gen">-</div> -->
                        </div>
                    </div>
                    `);
                }
            });

            $(document)
            .off('click', '#teamList .tournament-item')
            .on('click', '#teamList .tournament-item', function () {

                $('#teamList .tournament-item').css('border',"");
                $(this).css('border','#FF3333 solid');

                team_id = $(this).data('id');
                $("#team-name").html($(this).data('name'));

                teamStatInfo();
                getTourPlayer();
            });


            // init swiper
            new Swiper('#teamList', {
            slidesPerView: 4.5,
            spaceBetween: 16,
            loop: false,/*
            autoplay: {
                delay: 2200,
                disableOnInteraction: false,
            },*/
            breakpoints: {
                576:  { slidesPerView: 6 },
                768:  { slidesPerView: 8 },
                1024: { slidesPerView: 10 },
                1400: { slidesPerView: 12 }
            }
            // ไม่มี pagination → ไม่มี dot
            });


    });
    //
}

function teamStatInfo() {
    $.getJSON('<?= base_url('admin/tournament-pg/get-team-statistics') ?>',{tour_id:tourId},res=>{
        if(!res?.data)return;
        //console.log(res.data);
        $("#team-games").html(res.data.match_played??'0');
        $("#team-for").html(res.data.goal_for??'0');
        $("#coach-count").html(res.data.coach_count??'0');
        $("#team-count").html(res.data.team_count??'0');
        $("#player-count").html(res.data.player_count??'0');
        
        //$("#team-drawn").html(res.data.draw_count??'0');
        //$("#team-won").html(res.data.win_match??'0');
        //$("#team-win_home").html(res.data.win_home_count??'0');
        //$("#team-win_away").html(res.data.win_away_count??'0');
        //$("#team-lost").html(res.data.match_lost??'0');
    });
}

function getTourPlayer() {
    //player
    const NO_PROFILE = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
    const NO_TEAM    = DEFAULT_LOGO;

    $.getJSON(
        '<?= base_url('admin/tournament-pg/get-tournament-players') ?>',
        { tour_id: tourId },
        res => {

            if (!res || res.status !== 'success' || !Array.isArray(res.data)) return;

            const $grid = $('#playersGrid');
            $grid.empty();

            res.data.forEach(p => {

            // ===== player =====
            const firstName = p.first_name?.trim() || 'N/A';
            const lastName  = p.last_name?.trim()  || '';
            const fullName  = (firstName + ' ' + lastName).trim() || 'N/A';

            const position = p.position_name_th || p.position_name_en || 'N/A';

            const playerPhoto = p.image_path
                ? `<?= base_url() ?>/${p.image_path}`
                : NO_PROFILE;

            // ===== team =====
            const teamName = language=='th'?p.team_name:p.team_name_en;

            const teamLogo = p.team_logo
                ? `<?= base_url() ?>/${p.team_logo}`
                : NO_TEAM;

            const shirt_number = p.shirt_number;

            const card = `
                <div class="player-card" onclick="window.location.replace('${p.link_report_people}')">

                <img class="player-photo"
                    src="${playerPhoto}"
                    onerror="this.src='${NO_PROFILE}'"
                    alt="${fullName}">

                <div class="player-name">${fullName} N.${shirt_number}</div>
                <div class="player-position">${position}</div>

                <div class="team-info mt-2">
                    <img class="team-logo"
                        src="${teamLogo}"
                        onerror="this.src='${NO_TEAM}'"
                        alt="${teamName}">
                    <span class="team-name">${teamName}</span>
                </div>

                </div>
            `;

            $grid.append(card);
            });
        }
    );
    //
}

function getTourRank() {
    $.when(
        $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-match-for-prog-rank-result') ?>',{tour_id:tourId}),
        $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-final-ranking') ?>',{tour_id:tourId})
    ).done((matchRes, finalRankingRes) => {
        const matches = matchRes?.[0]?.data || [];
        const finalRanking = finalRankingRes?.[0]?.status === 'success' ? finalRankingRes[0] : null;
        renderRanking(matches, finalRanking);
    }).fail(() => {
        $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-match-for-prog-rank-result') ?>',{tour_id:tourId},res=>{
            if(!res?.data)return;
            renderRanking(res.data, null);
        });
    });
    window.addEventListener('resize',drawBracketLines);
}

function renderRanking(data, finalRanking){
    const finalRankingData = normalizeFinalRanking(finalRanking);

    if ((!data || !data.length) && !finalRankingData.length) {
        $('#rankingContainer').html('');
        return;
    }

    const stages = {};
    const stageKeys = [];
    (data || []).forEach(m=>{
        const stageKey = m.stage_id || `stage_${stageKeys.length + 1}`;
        if(!stages[stageKey]) {
            stages[stageKey] = [];
            stageKeys.push(stageKey);
        }
        stages[stageKey].push(m);
    });

    let html = '';

    // ================= TAB HEADERS =================
    html += `<ul class="nav nav-tabs mb-3" role="tablist">`;

    // 1. ตารางคะแนน Tab (Always first)
    html += `
    <li class="nav-item">
        <button class="nav-link active"
                data-bs-toggle="tab"
                data-bs-target="#stage_ranking"
                type="button">
            ${league_table_label}
        </button>
    </li>
    `;

    // 2. Stage Match Tabs
    stageKeys.forEach((stageId,index)=>{
        const stageName = stages[stageId][0]?.match_round || `รอบที่ ${index+1}`;
        html += `
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#stage_${stageId}"
                    type="button">
                ${stageName}
            </button>
        </li>
        `;
    });

    if (finalRankingData.length) {
        html += `
        <li class="nav-item">
            <button class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#stage_final_results"
                    type="button">
                ${tournament_results_label}
            </button>
        </li>
        `;
    }

    html += `</ul>`;
    html += `<div class="tab-content">`;

    // ================= TAB CONTENT 1: ${league_table_label} (Standings from First Stage) =================
    html += `<div class="tab-pane fade show active" id="stage_ranking">`;
    html += `<div class="row g-4">`;

    const firstStageMatches = data.filter(m => m.format === 'league_single' && m.group_id);
    const groups = {};

    firstStageMatches.forEach(m=>{
        const t1 = parseInt(m.team1_id,10) || 0;
        const t2 = parseInt(m.team2_id,10) || 0;

        if(!groups[m.group_id]) groups[m.group_id]={};

        const name1 = getMatchTeamName(m, 1);
        const name2 = getMatchTeamName(m, 2);
        const key1 = t1 > 0 ? String(t1) : name1.toLowerCase();
        const key2 = t2 > 0 ? String(t2) : name2.toLowerCase();

        initTeam(groups[m.group_id], key1, name1, m.team1_logo);
        if (t2 > 0 || m.team2_name || m.team2_name_en) {
            initTeam(groups[m.group_id], key2, name2, m.team2_logo);
        }

        const s1=parseInt(m.team1_score);
        const s2=parseInt(m.team2_score);
        if(isNaN(s1)||isNaN(s2)) return;

        const A=groups[m.group_id][key1];
        const B=groups[m.group_id][key2];
        if (!A || !B) return;

        A.play++; B.play++;
        A.gf+=s1; A.ga+=s2;
        B.gf+=s2; B.ga+=s1;
        A.gd=A.gf-A.ga;
        B.gd=B.gf-B.ga;

        if(s1>s2){A.pts+=3;A.win++;B.lose++;}
        else if(s1<s2){B.pts+=3;B.win++;A.lose++;}
        else{A.pts++;B.pts++;A.draw++;B.draw++;}

        saveH2H(A,B,s1,s2);
        saveH2H(B,A,s2,s1);
    });

    Object.keys(groups).sort().forEach(g=>{
        const teams = Object.values(groups[g]);
        teams.sort((a,b)=>{
            if (b.pts!==a.pts) return b.pts-a.pts;
            if (b.gd!==a.gd) return b.gd-a.gd;
            if (b.gf!==a.gf) return b.gf-a.gf;
            return 0;
        });

        html += `
        <div class="col-12 col-sm-6 col-lg-6">
            <div class="bg-white rounded-4 p-3 h-100 shadow-sm">
                <div class="group-title mb-2 fw-bold">
                    ${group_label} ${g=='null'?'-':g}
                </div>
                <div class="table-responsive">
                    <table class="ranking-table">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th width="40%">${team_label}</th>
                                <th>${played_label}</th>
                                <th>${won_label}</th>
                                <th>${drawn_label}</th>
                                <th>${lost_label}</th>
                                <th>${for_label}</th>
                                <th>${against_label}</th>
                                <th>${goal_diff_label}</th>
                                <th>${points_label}</th>
                            </tr>
                        </thead>
                        <tbody>
        `;

        teams.forEach((t,i)=>{
            const logo = t.logo ? `<?= base_url() ?>/${t.logo}` : DEFAULT_LOGO;
            const highlight = (i < 2) ? 'top-rank' : '';
            html += `
            <tr class="${highlight}">
                <td>${i+1}</td>
                <td class="text-start">
                    <div class="rank-team" ${getTeamClickAttr(t.id)}>
                        <img style="background:#eee;" src="${logo}" onerror="this.src='${DEFAULT_LOGO}'">
                        ${t.name}
                    </div>
                </td>
                <td>${t.play || 0}</td>
                <td>${t.win || 0}</td>
                <td>${t.draw || 0}</td>
                <td>${t.lose || 0}</td>
                <td>${t.gf || 0}</td>
                <td>${t.ga || 0}</td>
                <td>${t.gd || 0}</td>
                <td><strong>${t.pts || 0}</strong></td>
            </tr>
            `;
        });

        html += `
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        `;
    });
    html += `</div></div>`;

    // ================= TAB CONTENT 2+: Matches for each Stage =================
    stageKeys.forEach((stageId,index)=>{
        html += `<div class="tab-pane fade" id="stage_${stageId}">`;
        const matches = stages[stageId];
        const matchDayCounters = {};
        html += `<div class="row g-4">`;

        matches.forEach(m => {
            const seqKey = `${stageId}_${m.date || unknown_date_label}`;
            const seq = (matchDayCounters[seqKey] || 0) + 1;
            matchDayCounters[seqKey] = seq;

            const team1 = getMatchTeamName(m, 1);
            const team2 = getMatchTeamName(m, 2);

            const logo1 = m.team1_logo ? `<?= base_url() ?>/${m.team1_logo}` : DEFAULT_LOGO;
            const logo2 = m.team2_logo ? `<?= base_url() ?>/${m.team2_logo}` : DEFAULT_LOGO;

            const s1 = parseInt(m.team1_score);
            const s2 = parseInt(m.team2_score);

            const finished = !isNaN(s1) && !isNaN(s2);
            const score = finished ? `${s1} - ${s2}` : '-';

            const team1Penalty = (
                (parseInt(m.team1_id) === parseInt(m.winner_team_id) || m.result==1) &&
                parseInt(m.is_penalty_used) === 1
            );

            const team2Penalty = (
                (parseInt(m.team2_id) === parseInt(m.winner_team_id) || m.result==2) &&
                parseInt(m.is_penalty_used) === 1
            );

            const team1Name = `${team1}${team1Penalty ? ` <span class='text-danger'>(P)</span>` : ''}`;
            const team2Name = `${team2}${team2Penalty ? ` <span class='text-danger'>(P)</span>` : ''}`;

            html += `
            <div class="col-12 col-md-6 col-lg-4">
                <div class="match-card bg-white rounded-4 p-3 shadow-sm h-100 d-flex flex-column">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="match-seq">${round_match} ${seq}</span>
                        <span class="match-date">${m.date ? formatThaiDate(m.date) : unknown_date_label} ${m.time ? m.time.slice(0, 5) : ''}</span>
                    </div>
                    ${m.group_id ? `<div class="text-muted small fw-bold mb-2">${group_label} ${m.group_id}</div>` : ''}
                    <!-- ทีม + สกอร์ -->
                    <div class="match-row">
                        <div class="team-block text-center" ${getTeamClickAttr(m.team1_id)}>
                            <img src="${logo1}" height="55"
                                style="background:#f1f3f5;padding:6px;border-radius:12px;"
                                onerror="this.src='${DEFAULT_LOGO}'">
                            <div class="mt-2 small fw-bold">${team1Name}</div>
                        </div>
                        <div class="score-block text-center fw-bold fs-5">
                            ${score}
                        </div>
                        <div class="team-block text-center" ${getTeamClickAttr(m.team2_id)}>
                            <img src="${logo2}" height="55"
                                style="background:#f1f3f5;padding:6px;border-radius:12px;"
                                onerror="this.src='${DEFAULT_LOGO}'">
                            <div class="mt-2 small fw-bold">${team2Name}</div>
                        </div>
                    </div>
                    <!-- Footer -->
                    <div class="mt-auto pt-3 border-top d-flex justify-content-between align-items-center">
                        <div style="font-size:13px;">
                            ${stadium_label} ${m.stadium ?? '-'}
                        </div>
                        <div class="match-btn"
                            onclick="match_sto(\'${team1Name}\',\'${team2Name}\',this)" 
                            data-event='${JSON.stringify(m.event ?? [])}'
                            style="cursor:pointer">
                            MATCH STORY
                        </div>
                    </div>
                </div>
            </div>
            `;
        });
        html += `</div></div>`;
    });

    if (finalRankingData.length) {
        html += `
        <div class="tab-pane fade" id="stage_final_results">
            ${renderFinalRanking(finalRankingData)}
        </div>
        `;
    }

    html += `</div>`;
    $('#rankingContainer').html(html);
}
function drawBracketLines(){

 const svg = document.getElementById('bracketLines');
 if(!svg) return;

 svg.innerHTML = '';

 const cols = document.querySelectorAll('.bracket-col');
 const p = svg.getBoundingClientRect();

 for(let i=0;i<cols.length-1;i++){

  const L = cols[i].querySelectorAll('.bracket-match');
  const R = cols[i+1].querySelectorAll('.bracket-match');

  L.forEach((m,j)=>{

   if(!R[j]) return;

   const a = m.getBoundingClientRect();
   const b = R[j].getBoundingClientRect();

   const x1 = a.right - p.left;
   const y1 = a.top + a.height/2 - p.top;
   const x2 = b.left - p.left;
   const y2 = b.top + b.height/2 - p.top;

   const path = document.createElementNS('http://www.w3.org/2000/svg','path');

   path.setAttribute(
     'd',
     `M${x1},${y1} C${x1+30},${y1} ${x2-30},${y2} ${x2},${y2}`
   );

   path.setAttribute('fill','none');
   path.setAttribute('stroke','#adb5bd');
   path.setAttribute('stroke-width','2');

   svg.appendChild(path);
  });
 }
}
// ===== helpers =====
function getMatchTeamName(match, side) {
  const th = match[`team${side}_name`];
  const en = match[`team${side}_name_en`];
  return (language === 'th' ? (th || en) : (en || th)) || tba_label;
}

function escapeHtml(value) {
  return String(value ?? '')
    .replace(/&/g, '&amp;')
    .replace(/</g, '&lt;')
    .replace(/>/g, '&gt;')
    .replace(/"/g, '&quot;')
    .replace(/'/g, '&#039;');
}

function logoUrl(logo) {
  if (!logo) return DEFAULT_LOGO;
  if (/^https?:\/\//i.test(logo)) return logo;
  return `<?= rtrim(base_url(), '/') ?>/${String(logo).replace(/^\/+/, '')}`;
}

function normalizeFinalRanking(finalRanking) {
  const rows = Array.isArray(finalRanking?.data) ? finalRanking.data : [];
  const maxRows = parseInt(finalRanking?.number_ranking, 10) || rows.length;

  return rows
    .filter(row => Number(row.rank_no) > 0 && Number(row.rank_no) <= maxRows)
    .sort((a, b) => Number(a.rank_no) - Number(b.rank_no));
}

function finalRankingTeamName(row) {
  return (language === 'th' ? (row.team_name || row.team_name_en) : (row.team_name_en || row.team_name)) || unknown_team_label;
}

function renderPodiumCard(row, rank) {
  if (!row) return '<div class="podium-column"></div>';

  const name = escapeHtml(finalRankingTeamName(row));
  const logo = logoUrl(row.team_logo);
  const badge = rank === 1
    ? `<span class="badge rounded-pill bg-warning text-dark mb-2 px-3 fw-bold">${winner_label}</span>`
    : rank === 2
      ? `<span class="badge rounded-pill bg-secondary bg-opacity-10 text-secondary border border-secondary mb-2">2nd Place</span>`
      : `<span class="badge rounded-pill mb-2" style="background-color:rgba(205,127,50,.1);color:#cd7f32;border:1px solid #cd7f32;">3rd Place</span>`;
  const crown = rank === 1 ? `<span class="crown-icon">🏆</span>` : '';

  return `
    <div class="podium-column">
      <div class="podium-card rank-${rank}">
        <div class="avatar-wrap">
          ${crown}
          <div class="avatar-img">
            <img src="${logo}" onerror="this.src='${DEFAULT_LOGO}'" alt="${name}">
          </div>
        </div>
        ${badge}
        <div class="name-label" ${rank === 1 ? 'style="font-size:1.3rem;"' : ''}>${name}</div>
      </div>
    </div>
  `;
}

function renderFinalRanking(rows) {
  const byRank = {};
  rows.forEach(row => byRank[Number(row.rank_no)] = row);
  const mentions = rows.filter(row => Number(row.rank_no) > 3);

  let html = `
    <div class="tour-results text-center">
      <h2 class="header-title">${tournament_results_label}</h2>
      <div class="podium-wrapper">
        ${renderPodiumCard(byRank[2], 2)}
        ${renderPodiumCard(byRank[1], 1)}
        ${renderPodiumCard(byRank[3], 3)}
      </div>
  `;

  if (mentions.length) {
    html += `
      <div class="list-container mt-5">
        <p class="section-subtitle">${honorable_mentions_label}</p>
    `;

    mentions.forEach(row => {
      const rankNo = Number(row.rank_no);
      const name = escapeHtml(finalRankingTeamName(row));
      const logo = logoUrl(row.team_logo);

      html += `
        <div class="result-list-item">
          <div class="rank-num">${rankNo}</div>
          <img class="result-team-logo" src="${logo}" onerror="this.src='${DEFAULT_LOGO}'" alt="${name}">
          <div class="text-start">
            <span class="mention-badge">อันดับ ${rankNo}</span>
            <div class="fw-bold h5 mb-0">${name}</div>
          </div>
          <div class="score-val">Rank ${rankNo}</div>
        </div>
      `;
    });

    html += `</div>`;
  }

  html += `</div>`;
  return html;
}

function getTeamClickAttr(teamId) {
  const id = parseInt(teamId, 10);
  return id > 0 ? `onclick="teamClick(${tourId},${id})" style="cursor:pointer"` : '';
}

function initTeam(group,id,name,logo){
  if (group[id]) return;
  group[id] = {
    id,
    name: name ?? unknown_team_label,
    logo: logo ? `${logo}` : DEFAULT_LOGO,
    play:0, win:0, draw:0, lose:0,
    gf:0, ga:0, pts:0,
    get gd(){ return this.gf - this.ga; },
    h2h:{}
  };
}
function saveH2H(A,B,sa,sb){
  if (!A.h2h[B.id]) A.h2h[B.id]={pts:0,gf:0,ga:0,gd:0};
  const h=A.h2h[B.id];
  h.gf+=sa; h.ga+=sb; h.gd=h.gf-h.ga;
  if (sa>sb) h.pts+=3;
  else if (sa===sb) h.pts+=1;
}
function addTeam(g,name,logo,s,c){
 if(!name)return;
 if(!g[name])g[name]={name,logo:logo?`<?=base_url()?>${logo}`:DEFAULT_LOGO,play:0,gf:0,ga:0,gd:0,pts:0};
 const t=g[name];
 t.play++;t.gf+=+s||0;t.ga+=+c||0;t.gd=t.gf-t.ga;
 if(s>c)t.pts+=3;else if(s==c)t.pts+=1;
}
/*
function formatThaiDate(d){
 if(!d)return'';
 const m=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
 const x=new Date(d);
 return `${x.getDate()} ${m[x.getMonth()]} ${x.getFullYear()+543}`;
}
*/
function formatThaiDate(d){
  if (!d) return '';
  const x = new Date(d);

  const day = x.getDate();
  const month = String(x.getMonth() + 1).padStart(2, '0');
  const year = x.getFullYear() + 543;

  return `${day}/${month}/${year}`;
}

function teamClick(tour_id,team_id) {
    window.location.replace("<?php echo base_url();?>/home/team_profile/?tour_id="+tour_id+"&team_id="+team_id);
}



function normalizeEventName(event, eventId) {
  const eventById = {
    1: 'goal',
    2: 'substitution',
    3: 'yellow card',
    4: 'red card',
    5: 'own goal'
  };

  const normalized = String(event ?? '').trim().toLowerCase().replace(/[_-]+/g, ' ');
  if (normalized) {
    if (['goal', 'ทำประตู'].includes(normalized)) return 'goal';
    if (['substitution', 'subsitation', 'substitution player', 'เปลี่ยนตัวผู้เล่น'].includes(normalized)) return 'substitution';
    if (['yellow card', 'yellowcard', 'ใบเหลือง'].includes(normalized)) return 'yellow card';
    if (['red card', 'redcard', 'ใบแดง'].includes(normalized)) return 'red card';
    if (['own goal', 'owngoal', 'ทำประตูตัวเอง'].includes(normalized)) return 'own goal';
  }

  return eventById[Number(eventId)] ?? normalized;
}

function getEventMeta(event, eventId) {
  switch (normalizeEventName(event, eventId)) {
    case 'goal':
      return { icon: 'fa-futbol', color: 'text-success', label: 'GOAL', title: 'Goal' };

    case 'own goal':
      return { icon: 'fa-futbol', color: 'text-danger', label: 'OG', title: 'Own goal' };

    case 'substitution':
      return { icon: 'fa-right-left', color: 'text-primary', label: 'SUB', title: 'Substitution' };

    case 'yellow card':
      return { icon: 'fa-square', color: 'text-warning', label: 'YC', title: 'Yellow card' };

    case 'red card':
      return { icon: 'fa-square', color: 'text-danger', label: 'RC', title: 'Red card' };

    default:
      return { icon: 'fa-circle-info', color: 'text-secondary', label: '', title: 'Event' };
  }
}

function match_sto(team1Name,team2Name,node) {
  const data = $(node).data('event');
  if (!data || !data.length) return;
  //console.log(data);

  let html = '';

  const leftTeamId = data[0].team_id;

    const leftTeam = data.find(v => v.team_id == leftTeamId) || {};
    const rightTeam = data.find(v => v.team_id != leftTeamId) || {};

  const leftTeamName = team1Name;

  const rightTeamName = team2Name;

  const leftLogo = leftTeam && leftTeam.logo
    ? `<?= base_url() ?>/${leftTeam.logo}`
    : DEFAULT_LOGO;

  const rightLogo = rightTeam && rightTeam.logo
    ? `<?= base_url() ?>/${rightTeam.logo}`
    : DEFAULT_LOGO;

  /* ===== HEADER ===== */
  html += `
    <li class="list-group-item bg-light fw-bold d-flex justify-content-between align-items-center text-center">

      <div class="d-flex align-items-center" style="min-width:40%">
        <img src="${leftLogo}" width="30" class="m-2"
             onerror="this.src='${DEFAULT_LOGO}'">
        ${leftTeamName ?? '-'}
      </div>

      <div style="width:20%"></div>

      <div class="d-flex align-items-center justify-content-end" style="min-width:40%">
        <img src="${rightLogo}" width="30" class="m-2"
             onerror="this.src='${DEFAULT_LOGO}'">
        ${rightTeamName ?? '-'}
      </div>

    </li>
  `;

  /* ===== EVENTS ===== */
  $.each(data, function (i, v) {

    const isLeft = v.team_id == leftTeamId;
    const meta = getEventMeta(v.event_name_en, v.event_id);

    const firstName = v.p1_first_name || '';
    const lastName = v.p1_last_name || '';
    const minute = v.minute || '-';

    const playerPhoto1 = v.p1_image_path
                ? `<?= base_url() ?>/${v.p1_image_path}`
                : NO_PROFILE;

    const playerPhoto2 = v.p2_image_path
                ? `<?= base_url() ?>/${v.p2_image_path}`
                : NO_PROFILE;


    html += `
      <li class="list-group-item d-flex align-items-center justify-content-between">

        <!-- LEFT -->
        <div style="min-width:40%">
          ${isLeft ? `<img src="${playerPhoto1}" width="30" class="m-2"
             onerror="this.src='${NO_PROFILE}'">`+`${firstName} ${lastName}` : ''}
        </div>

        <!-- CENTER -->
        <div class="text-center">
          <i class="fa-solid ${meta.icon} ${meta.color}" title="${meta.title}"></i>
          ${meta.label ? `<div class="small ${meta.color} fw-bold">${meta.label}</div>` : ''}
          <span class="badge bg-secondary">${minute_label} ${minute}</span>
        </div>

        <!-- RIGHT -->
        <div class="text-end" style="min-width:40%">
          ${!isLeft ? `${firstName} ${lastName}`+`<img src="${playerPhoto2}" width="30" class="m-2"
             onerror="this.src='${NO_PROFILE}'">` : ''}
        </div>

      </li>
    `;
  });

  $('#eventModal .list-group').html(html);
  $('#eventModal').modal('show');
}
</script>
