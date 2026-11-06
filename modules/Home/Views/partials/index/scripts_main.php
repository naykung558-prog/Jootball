<script>
/* รายงานผล */
var tourId=33;
const language = '<?=session()->get('language')?>';
const group_label = '<?=lang('Tournament.group')?>';
const team_label = '<?=lang('Tournament.team')?>';
const played_label = '<?=lang('Tournament.played')?>';
const minute_label = '<?=lang('Common.minute')?>';
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
const player_show_more_label = '<?=lang('Tournament.player_show_more')?>';
const number_label = '<?=lang('Tournament.number')?>';
const position_label = '<?=lang('Tournament.position')?>';
//const DEFAULT_LOGO='https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg';
const DEFAULT_LOGO = '<?= base_url('public/img/jootball_logo.png') ?>';
const stadium_label = '<?=lang('Tournament.stadium')?>';
const NO_PROFILE = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';

/* ===== SORT น้อย → มาก =====
   state_id → stage_id → match_round → date → time
*/
function sortData(d){
  if (!Array.isArray(d) || d.length < 2) return;

  // 1. กลับลำดับบน ↔ ล่าง
  d.reverse();
  //console.log(d);

  /*
  // 2. สลับตัวบนสุด ไปอยู่ลำดับที่ 2
  const first = d.shift();   // ดึงตัวแรกออก
  d.splice(1, 0, first);     // ใส่กลับไปที่ index 1
  */

}
function sortMatchCellByGroup() {
  $('#matchContainer .row.g-0').each(function () {
    const $row = $(this);

    const cells = $row.children('.match-cell').get();

    // เก็บ index เดิมไว้ เพื่อคงลำดับเมื่อ group เท่ากัน
    cells.forEach((el, i) => el._idx = i);

    cells.sort((a, b) => {
      const ga = +($(a).data('group') ?? 0);
      const gb = +($(b).data('group') ?? 0);

      if (ga !== gb) return ga - gb;      // น้อย → มาก
      return a._idx - b._idx;              // เสมอ = ลำดับเดิม
    });

    $(cells).appendTo($row);
  });
}
function teamRow(team_id,name,score,logo,pen){
 const src=logo?`<?=base_url()?>/${logo}`:DEFAULT_LOGO;
 return `<div class="team" onclick="teamClick(${tourId},${team_id})">
   <div class="team-left">
     <img src="${src}" class="team-logo" onerror="this.src='${DEFAULT_LOGO}'">
     <span>${name??tba_label}</span>
   </div>
   <div class="score">${score??'-'} ${pen?'<b style="position:absolute">(P)</b>':''}</div>
 </div>`;
}
function renderMatches(data){
  if (!Array.isArray(data) || !data.length) {
    matchContainer.innerHTML = '-';
    return;
  }

  // Grouping logic: Round -> Stadium -> Date
  const roundGroups = {};
  const roundOrder = [];

  data.forEach(m => {
    const r = m.match_round || 'Round';
    if (!roundGroups[r]) {
      roundGroups[r] = {};
      roundOrder.push(r);
    }
    
    const std = language === 'th' ? (m.stadium ?? '-') : (m.stadium_en ?? m.stadium ?? '-');
    if (!roundGroups[r][std]) roundGroups[r][std] = {};
    
    const d = m.date || 'TBD';
    if (!roundGroups[r][std][d]) roundGroups[r][std][d] = [];
    
    roundGroups[r][std][d].push(m);
  });

  let h = '';
  const roundDayCounters = {};

  roundOrder.forEach((r, roundIndex) => {
    const showGroupColumn = roundIndex === 0;
    const tableColspan = showGroupColumn ? 7 : 6;

    h += `<div class="round-section mb-5">
            <h4 class="round-big-label p-3 mb-3 text-white fw-bold shadow-sm" style="background: linear-gradient(135deg, #0B1548 0%, #214790 100%); border-radius: 8px;">
              <i class="fas fa-trophy me-2 text-warning"></i>${r}
            </h4>
            <div class="row g-3">`;

    const stadiums = Object.keys(roundGroups[r]).sort();
    const colClass = stadiums.length > 1 ? 'col-lg-6' : 'col-lg-12';
    stadiums.forEach(std => {
      h += `
        <div class="${colClass}">
          <div class="stadium-header p-2 mb-0 text-white" style="background: #16478D; border-radius: 8px 8px 0 0;">
            <h6 class="fw-bold mb-0 text-center text-white" style="font-size: 0.8rem;">
              <i class="fas fa-map-marker-alt me-2"></i>${language === 'th' ? 'สนาม' : 'Stadium'}: ${std}
            </h6>
          </div>
          <div class="table-responsive shadow-sm mb-4" style="border-radius: 0 0 8px 8px;">
            <table class="table table-sm table-bordered align-middle mb-0 text-center" style="font-size: 0.72rem; border: 1px solid #dee2e6;">
              <thead style="background: #e7f1ff; color: #16478D;">
                <tr>
                  <th style="width: 5%;">No</th>
                  <th style="width: 10%;">${language === 'th' ? 'เวลา' : 'Time'}</th>
                  ${showGroupColumn ? '<th style="width: 10%;">Group</th>' : ''}
                  <th style="width: ${showGroupColumn ? '25%' : '30%'};">${language === 'th' ? 'ทีม 1' : 'Team 1'}</th>
                  <th style="width: 10%;">Score</th>
                  <th style="width: ${showGroupColumn ? '25%' : '30%'};">${language === 'th' ? 'ทีม 2' : 'Team 2'}</th>
                  <th style="width: 15%;">Action</th>
                </tr>
              </thead>
              <tbody>
      `;

      const dates = Object.keys(roundGroups[r][std]).sort();
      dates.forEach(date => {
        h += `
          <tr style="background: #f8f9fa;">
            <td colspan="${tableColspan}" class="text-start ps-2 py-1 fw-bold" style="color: #495057; font-size: 0.72rem;">
              <i class="fas fa-calendar-alt me-2 text-primary"></i> ${date !== 'TBD' ? formatThaiDate(date) : unknown_date_label}
            </td>
          </tr>
        `;

        const matches = roundGroups[r][std][date].sort((a, b) => (a.time || '').localeCompare(b.time || ''));
        matches.forEach(m => {
          const seqKey = `${r}_${date}`;
          const seq = (roundDayCounters[seqKey] || 0) + 1;
          roundDayCounters[seqKey] = seq;

          const t1_logo = m.team1_logo ? `<?=base_url()?>/${m.team1_logo}` : DEFAULT_LOGO;
          const t2_logo = m.team2_logo ? `<?=base_url()?>/${m.team2_logo}` : DEFAULT_LOGO;

          // ชื่อทีม: กันค่า null/ว่าง ไม่ให้แสดงคำว่า null (อังกฤษไม่มี name_en ให้ใช้ชื่อไทยแทน)
          const t1_name = (language === 'th' ? m.team1_name : (m.team1_name_en || m.team1_name)) || tba_label;
          const t2_name = (language === 'th' ? m.team2_name : (m.team2_name_en || m.team2_name)) || tba_label;

          h += `
            <tr style="background: #fff;">
              <td>${seq}</td>
              <td class="fw-bold text-primary">${m.time?.slice(0, 5) ?? '-'}</td>
              ${showGroupColumn ? `<td><span class="badge bg-light text-dark border p-1" style="font-size: 0.62rem;">${m.group_id ?? '-'}</span></td>` : ''}
              <td class="text-start" onclick="teamClick(${tourId},${m.team1_id})" style="cursor:pointer">
                <div class="d-flex align-items-center" style="min-width:0;">
                  <img src="${t1_logo}" class="me-1" style="width: 18px; height: 18px; object-fit: contain; background: #f8f9fa; flex: 0 0 auto;" onerror="this.src='${DEFAULT_LOGO}'">
                  <span class="text-truncate">${t1_name}</span>
                </div>
              </td>
              <td class="fw-bold" style="background: #f0f7ff; font-size: 0.82rem;">
                ${m.team1_score ?? '-'} - ${m.team2_score ?? '-'}
              </td>
              <td class="text-start" onclick="teamClick(${tourId},${m.team2_id})" style="cursor:pointer">
                <div class="d-flex align-items-center" style="min-width:0;">
                  <img src="${t2_logo}" class="me-1" style="width: 18px; height: 18px; object-fit: contain; background: #f8f9fa; flex: 0 0 auto;" onerror="this.src='${DEFAULT_LOGO}'">
                  <span class="text-truncate">${t2_name}</span>
                </div>
              </td>
              <td>
                <button class="btn btn-sm btn-dark py-0 px-2" style="font-size: 0.62rem;" onclick="match_sto(this)" data-event='${JSON.stringify(m.event ?? [])}'>
                  Story
                </button>
              </td>
            </tr>
          `;
        });
      });

      h += `</tbody></table></div></div>`;
    });

    h += `</div></div>`;
  });

  matchContainer.innerHTML = h;
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

function match_sto(node) {
  const data = $(node).data('event');
  let html = '';

  if (!data || !data.length) return;

  const leftTeamId = data[0].team_id; // ทีมซ้าย

  $.each(data, function (i, v) {
    const isLeft = v.team_id == leftTeamId;
    const teamName = language === 'th' ? v.team_name : v.team_name_en;
    const logo = v.logo ? `<?= base_url() ?>/${v.logo}` : DEFAULT_LOGO;

    const meta = getEventMeta(v.event_name_en, v.event_id);
    html += `
      <li class="list-group-item d-flex align-items-center justify-content-between">

        <!-- LEFT -->
        <div class="d-flex align-items-center" style="min-width:40%">
          ${isLeft ? `
            <img src="${logo}" width="28" class="me-2" onerror="this.src='${DEFAULT_LOGO}'">
            <span>
              <strong>${teamName}</strong><br>
              ${v.p1_first_name ?? ''} ${v.p1_last_name ?? ''}
            </span>
          ` : ''}
        </div>

        <!-- CENTER -->
        <div class="text-center">
          <i class="fa-solid ${meta.icon} ${meta.color}" title="${meta.title}"></i>
          ${meta.label ? `<div class="small ${meta.color} fw-bold">${meta.label}</div>` : ''}
          <span class="badge bg-secondary">${minute_label} ${v.minute ?? '-'}</span>
        </div>

        <!-- RIGHT -->
        <div class="d-flex align-items-center justify-content-end text-end" style="min-width:40%">
          ${!isLeft ? `
            <span class="me-2">
              <strong>${teamName}</strong><br>
              ${v.p1_first_name ?? ''} ${v.p1_last_name ?? ''}
            </span>
            <img src="${logo}" width="28" onerror="this.src='${DEFAULT_LOGO}'">
          ` : ''}
        </div>

      </li>
    `;
  });

  $('#eventModal .list-group').html(html);
  $('#eventModal').modal('show');
}

function renderBracket(data){
  if (!Array.isArray(data) || !data.length) {
    bracketContainer.innerHTML = '';
    return;
  }

  const map = {};
  const title = {};
  const order = [];

  // ✅ แบ่งคอลัมน์ด้วย stage_id ไม่ใช่ match_round
  // match_round เป็นข้อความอิสระที่ค้างเป็นชื่อเก่าได้เมื่อมีการเปลี่ยนชื่อ stage
  // ทำให้ stage เดียวถูกแตกเป็นหลายคอลัมน์ (คอลัมน์ที่โผล่มาเองแบบ "จะแจ้งภายหลัง")
  // ส่วน stage_id คือคีย์จริงที่แถวผูกอยู่ ไม่มีวันเพี้ยน
  data.forEach(m => {
    const k = (m.stage_id ?? '') !== '' ? String(m.stage_id) : ('r:' + (m.match_round || ''));

    if (!map[k]) {
      map[k] = [];
      title[k] = m.stage_name || m.match_round || '';
      order.push(k);   // ← ลำดับนี้มาจาก API ตรง ๆ (เรียงตาม order_num แล้ว)
    }

    map[k].push(m);
  });

  // ✅ ตัดรอบแรกออกไป (ถ้ามีมากกว่า 1 รอบ)
  if (order.length > 1) {
    const firstRound = order.shift();
    delete map[firstRound];
  }

  // ===== render =====
  let h = '<div class="d-flex" style="gap: 100px;">';

  order.forEach(k => {
    h += `
      <div class="bracket-col">
        <div class="bracket-title">${title[k]}</div>
    `;

    map[k].forEach(m => {
      const winnerId = m.winner_team_id;
      h += `
        <div class="bracket-match" data-winner-id="${winnerId}" data-final="${m.final_match ?? 0}">
          ${bracketTeam(m.team1_name, m.team1_score, m.team1_logo, m.team1_id, winnerId && winnerId == m.team1_id)}
          ${bracketTeam(m.team2_name, m.team2_score, m.team2_logo, m.team2_id, winnerId && winnerId == m.team2_id)}
        </div>
      `;
    });

    h += '</div>';
  });

  h += '</div>';

  bracketContainer.innerHTML = h;

  // วาดเส้นหลัง DOM พร้อม
  setTimeout(drawBracketLines, 50);
}

function bracketTeam(name,score,logo,teamId,isWinner){
 const src=logo?`<?=base_url()?>/${logo}`:DEFAULT_LOGO;
 return `<div class="bracket-team ${isWinner?'is-winner':''}" data-team-id="${teamId}">
   <div class="bracket-left">
     <img src="${src}" class="bracket-logo" onerror="this.src='${DEFAULT_LOGO}'">
     <span>${name??tba_label}</span>
   </div>
   <strong>${score??'-'}</strong>
 </div>`;
}

function drawBracketLines(){
  const svg=bracketLines; 
  svg.innerHTML='';
  const cols=document.querySelectorAll('.bracket-col');
  if(!cols.length) return;

  const p=svg.getBoundingClientRect();
  
  for(let i=0; i<cols.length-1; i++){
    const L = cols[i].querySelectorAll('.bracket-match');
    const nextCol = cols[i+1];
    
    L.forEach(m => {
      const winnerId = m.getAttribute('data-winner-id');
      const isFinal = m.getAttribute('data-final') == '1';
      
      // รอบชิงอันดับไม่ต้องโยงเส้นไปต่อ
      if (isFinal) return;
      
      const a_match = m.getBoundingClientRect();
      const x1 = a_match.right - p.left;
      const x2_target_col = nextCol.getBoundingClientRect().left - p.left;
      const x_mid = x1 + (x2_target_col - x1) / 2;

      if (winnerId && winnerId !== 'null') {
        // มีผู้ชนะ -> โยงไปหาทีมเดียวกันในรอบถัดไป
        const winningTeamRow = m.querySelector(`.bracket-team[data-team-id="${winnerId}"]`);
        const targetTeamRow = nextCol.querySelector(`.bracket-team[data-team-id="${winnerId}"]`);
        
        if (winningTeamRow && targetTeamRow) {
          const a = winningTeamRow.getBoundingClientRect();
          const b = targetTeamRow.getBoundingClientRect();
          
          const y1 = a.top + (a.height / 2) - p.top;
          const x2 = b.left - p.left;
          const y2 = b.top + (b.height / 2) - p.top;
          
          // ใช้ S-Curve สีเทาเพื่อแก้ปัญหาเส้นทับกัน (Overlapping) และดูสะอาดตาขึ้น
          const path = document.createElementNS('http://www.w3.org/2000/svg','path');
          path.setAttribute('d', `M${x1},${y1} C${x_mid},${y1} ${x_mid},${y2} ${x2},${y2}`);
          path.setAttribute('stroke', '#adb5bd');
          path.setAttribute('stroke-width', '2');
          path.setAttribute('fill', 'none');
          svg.appendChild(path);
        }
      } else {
        // ยังไม่มีผู้ชนะ -> โยงเส้นประไปไว้ตรงกลางระหว่างรอบ
        const y_match_mid = a_match.top + (a_match.height / 2) - p.top;
        
        const path = document.createElementNS('http://www.w3.org/2000/svg','path');
        path.setAttribute('d', `M${x1},${y_match_mid} L${x_mid},${y_match_mid}`);
        path.setAttribute('stroke', '#adb5bd'); 
        path.setAttribute('stroke-width', '2');
        path.setAttribute('stroke-dasharray', '5,5'); 
        path.setAttribute('fill', 'none');
        svg.appendChild(path);
      }
    });
  }
}
function renderRanking(data){
    const firstStageId = Number(data?.[0]?.stage_id ?? null);
    data = data.filter(d => Number(d.stage_id) === firstStageId);
    console.log('Ranking data:', data);

    const groups = {};

    data.forEach(m => {
        const t1 = parseInt(m.team1_id, 10) ?? 0;
        const t2 = parseInt(m.team2_id, 10) ?? 0;

        if (!groups[m.group_id]) groups[m.group_id] = {};

        // ===== init ทีม =====
        // ใช้ชื่อทีมเป็น key เพื่อ group ทีมที่มีชื่อเดียวกันเป็นทีมเดียว
        // Normalize ชื่อ: trim และแปลงเป็นตัวพิมพ์เล็กเพื่อเปรียบเทียบ
        const name1 = (language=='th'?m.team1_name:m.team1_name_en)?.trim() || 'Unknown';
        const name2 = (language=='th'?m.team2_name:m.team2_name_en)?.trim() || 'Unknown';

        // ถ้ามี team_id ให้ใช้ id เป็น key (เพื่อแยกทีมที่ชื่อเดียวกันแต่คนละทีม)
        // ถ้าไม่มี id ให้ใช้ชื่อทีมเป็น key (เพื่อ group ทีมที่ชื่อเดียวกัน)
        const key1 = (t1 > 0 && m.team1_id) ? String(t1) : name1.toLowerCase();
        const key2 = (t2 > 0 && m.team2_id) ? String(t2) : name2.toLowerCase();

        initTeam(groups[m.group_id], key1, name1, m.team1_logo, m.team1_slot);

        // มี team2 หรือไม่? (เช็คทั้ง id และ name)
        if (t2 > 0 || m.team2_name) {
            initTeam(groups[m.group_id], key2, name2, m.team2_logo, m.team2_slot);
        }

        const s1 = parseInt(m.team1_score, 10);
        const s2 = parseInt(m.team2_score, 10);
        if (isNaN(s1) || isNaN(s2)) return;

        const A = groups[m.group_id][key1];
        
        // มี team2 หรือไม่? (เช็คทั้ง id และ name)
        if (t2 > 0 || m.team2_name) {
            const B = groups[m.group_id][key2];
            A.play++; B.play++;
            A.gf += s1; A.ga += s2;
            B.gf += s2; B.ga += s1;
            A.gd = A.gf - A.ga;
            B.gd = B.gf - B.ga;

            if (s1 > s2) {
            A.pts += 3; A.win++; B.lose++;
            } else if (s1 < s2) {
            B.pts += 3; B.win++; A.lose++;
            } else {
            A.pts++; B.pts++; A.draw++; B.draw++;
            }

            // ===== H2H =====
            saveH2H(A, B, s1, s2);
            saveH2H(B, A, s2, s1);
        }
    });

    // ===== render ตาราง =====
    let html = '';

    Object.keys(groups).sort().forEach(g => {
        const teams = Object.values(groups[g]);

        teams.sort((a,b)=>{
        if (b.pts !== a.pts) return b.pts - a.pts;
        if (b.gd !== a.gd) return b.gd - a.gd;
        if (b.gf !== a.gf) return b.gf - a.gf;

        // H2H
        const h2hA = a.h2h[b.name];
        const h2hB = b.h2h[a.name];
        if (h2hA && h2hB) {
            if (h2hB.pts !== h2hA.pts) return h2hB.pts - h2hA.pts;
            if (h2hB.gd !== h2hA.gd) return h2hB.gd - h2hA.gd;
        }

        // เสมอกันทุกเกณฑ์ (ยังไม่แข่ง / ยังไม่เลือกทีม) — Array.sort เป็น stable
        // ถ้าคืน 0 เฉย ๆ จะได้ลำดับที่ทีมถูกพบในตารางแมตช์ คือลำดับคู่แข่งของ
        // round-robin ไม่ใช่ลำดับที่แอดมินจัดไว้ จึงออกมาเป็น A4 A5 A1 A2 A3
        // เรียงตาม slot ใน teams_json = ลำดับช่องที่แอดมินจัดไว้จริง
        // (ใช้ชื่อไม่ได้ เพราะชื่อจริงหรือป้ายที่ตั้งเองไม่ได้เรียงตามช่อง)
        if (a.slot !== null && b.slot !== null && a.slot !== b.slot) return a.slot - b.slot;

        // แถวเก่าที่ยังไม่มี slot — numeric:true ให้ A2 มาก่อน A10
        return String(a.name).localeCompare(String(b.name), undefined, { numeric: true, sensitivity: 'base' });
        });

        html += `
        <div class="group-title">${group_label} ${g=='null'?'-':g}</div>
        <table class="ranking-table">
            <thead>
            <tr>
                <th>#</th><th width="40%">${team_label}</th><th>${played_label}</th>
                <th>${won_label}</th><th>${drawn_label}</th><th>${lost_label}</th>
                <th>${for_label}</th><th>${against_label}</th><th>${goal_diff_label}</th><th>${points_label}</th>
            </tr>
            </thead>
            <tbody>
        `;

        teams.forEach((t,i)=>{
            //console.log(t);
                const team1_logo = t.logo
            ? `<?= base_url() ?>/${t.logo}`
            : DEFAULT_LOGO;

        const highlight = i < 2 ? 'top-rank' : '';
        html += `
            <tr class="${highlight}">
            <td>${i+1}</td>
            <td class="text-start">
                <div class="rank-team">
                <img style="background: #eee;" src="${team1_logo}" onerror="this.src='${DEFAULT_LOGO}'">
                ${t.name}
                </div>
            </td>
            <td>${t.play}</td>
            <td>${t.win}</td>
            <td>${t.draw}</td>
            <td>${t.lose}</td>
            <td>${t.gf}</td>
            <td>${t.ga}</td>
            <td>${t.gd}</td>
            <td><strong>${t.pts}</strong></td>
            </tr>
        `;
        });

        html += '</tbody></table>';
    });

    $('#rankingContainer').html(html);
}
// ===== helpers =====
function initTeam(group,id,name,logo,slot){
  if (group[id]) return;
  group[id] = {
    id,
    name: name ?? unknown_team_label,
    logo: logo ? `${logo}` : DEFAULT_LOGO,
    // ตำแหน่งใน teams_json — ใช้เป็นเกณฑ์ตัดสินสุดท้ายตอนคะแนนเท่ากันหมด
    slot: (slot === null || slot === undefined || slot === '') ? null : parseInt(slot, 10),
    play:0, win:0, draw:0, lose:0,
    gf:0, ga:0, pts:0,
    get gd(){ return this.gf - this.ga; },
    h2h:{}
  };
}
function saveH2H(A,B,sa,sb){
  if (!A.h2h[B.name]) A.h2h[B.name]={pts:0,gf:0,ga:0,gd:0};
  const h=A.h2h[B.name];
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

function populateFilters(data) {
  const stadiums = new Set();
  const dates = new Set();
  const rounds = new Set();

  data.forEach(m => {
    const std = language === 'th' ? (m.stadium ?? '-') : (m.stadium_en ?? m.stadium ?? '-');
    if (std && std !== '-') stadiums.add(std);
    if (m.date) dates.add(m.date);
    if (m.match_round) rounds.add(m.match_round);
  });

  // Reset values
  $('#filterStadium').val("");
  $('#filterDate').val("");
  $('#filterRound').val("");

  // 1. Stadiums
  $('#filterStadium').find('option:not(:first)').remove();
  Array.from(stadiums).sort().forEach(s => $('#filterStadium').append(`<option value="${s}">${s}</option>`));

  // 2. Dates
  $('#filterDate').find('option:not(:first)').remove();
  Array.from(dates)
    .sort((a, b) => new Date(a) - new Date(b))
    .forEach(d => $('#filterDate').append(`<option value="${d}">${formatThaiDate(d)}</option>`));

  // 3. Rounds
  $('#filterRound').find('option:not(:first)').remove();
  Array.from(rounds).forEach(r => $('#filterRound').append(`<option value="${r}">${r}</option>`));
}

function sortMatchesByDate(data) {
  if (!Array.isArray(data)) return [];
  return [...data].sort((a, b) => {
    const dateA = a.date || '9999-12-31';
    const dateB = b.date || '9999-12-31';
    if (dateA !== dateB) return dateA.localeCompare(dateB);
    const timeA = a.time || '23:59';
    const timeB = b.time || '23:59';
    return timeA.localeCompare(timeB);
  });
}

function applyMatchFilters() {
  if (!window.bracketData) return;

  const stadium = $('#filterStadium').val();
  const date = $('#filterDate').val();
  const round = $('#filterRound').val();

  let filtered = window.bracketData.filter(m => {
    const std = language === 'th' ? (m.stadium ?? '-') : (m.stadium_en ?? m.stadium ?? '-');
    const matchStadium = stadium === "" || std === stadium;
    const matchDate = date === "" || m.date === date;
    const matchRound = round === "" || m.match_round === round;
    return matchStadium && matchDate && matchRound;
  });

  renderMatches(filtered);
}

function getTourRank() {
    $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-match-for-prog-rank-result') ?>',{tour_id:tourId},res=>{
        if(!res?.data)return;
        tourTeams();
        getTourPlayer();
        teamStatInfo();

        //sortData(res.data);      // <<< น้อย → มาก
        
        // Match Filters
        window.bracketData = res.data;
        populateFilters(window.bracketData);
        renderMatches(window.bracketData);
        
        //sortMatchCellByGroup();
        renderRanking(res.data);

        // Render bracket ทันทีเมื่อเปลี่ยน tour (ถ้า tab map กำลังแสดงอยู่)
        if($('.nav-link[data-bs-target="#map"]').hasClass('active')) {
            renderBracket(res.data);
            setTimeout(drawBracketLines, 50);
        }
    });
    window.addEventListener('resize',drawBracketLines);
}
function getTourPlayer() {
    //player
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

            const card = `
                <div class="player-card" onclick="window.location.replace('${p.link_report_people}')">

                <img class="player-photo"
                    src="${playerPhoto}"
                    onerror="this.src='${NO_PROFILE}'"
                    alt="${fullName}">

                <div class="player-name">${fullName}</div>
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

            //
                players = res.data.map(player => ({
                    ...player,
                    id: player.player_id,
                    team: player.tour_team_id,
                    photo: player.image_path,
                    name: player.first_name+' '+player.last_name,
                    number: player.shirt_number,
                    pos: player.pos,
                    sub: player.position_name_en,
                }));
                players.forEach(p=>$('#playerSelect').append(`<option value="${p.id}">${p.name} ${p.name_en}</option>`));
            //

            loadMock();


        }
    );
    //
}
function teamStatInfo() {

    $.getJSON('<?= base_url('admin/tournament-pg/get-team-statistics') ?>',{tour_id:tourId},res=>{
        if(!res?.data)return;
        console.log(res.data);
        $("#sum-math").html(res.data.match_played_pre);
        $("#sum-team").html(res.data.team_count);
        $("#sum-coach").html(res.data.coach_count);
        $("#sum-player").html(res.data.player_count);
        $("#sum-card").html(res.data.sum_card);
        $("#sum-best-player").html(res.data.sum_best_player);
    });
}
$(function(){
    //TourList
    getTour();
    //

    // Event Listeners for Filters
    $('#filterStadium, #filterDate, #filterRound').on('change', applyMatchFilters);

    //main match
    //getTourRank();
    //

    //get Player in tour
    //getTourPlayer();
    //

});

function getTour() {
    $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-list') ?>',{},res=>{
        if(!res?.data || res.data.length === 0){
            $('.result-section').hide();
            $('.services-mvp').hide();
            return;
        }
        const tournaments = res.data;

            const $wrapper = $('#tournamentWrapper');

            $.each(tournaments, function (i, t) {
                if(i==0) {
                    tourId = t.tour_id;
                    getTourRank();

                    $("#tour-name").html(language=='th'?t.name:t.name_en);
                }

                const logo = t.logo
                ? `<?= base_url() ?>/${t.logo}`
                : 'https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg';

                $wrapper.append(`
                <div class="swiper-slide">
                    <div class="tournament-item" data-id="${t.tour_id}" `+(tourId==t.tour_id?'style="border:#FF3333 solid"':'')+`>
                    <img src="${logo}" class="tournament-logo"
                        onerror="this.src='https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg'">
                    <div class="tournament-name">${t.name}</div>
                    <div class="tournament-gen">${t.generation ?? ''}</div>
                    </div>
                </div>
                `);
            });

            // click item
            $(document).on('click', '.tournament-item', function () {

                // ล้าง border ตัวอื่นก่อน
                $('.tournament-item').css('border',"");

                // ใส่ border ให้ตัวที่กด
                $(this).css('border','#FF3333 solid');

                tourId = $(this).data('id');
                $("#tour-name").html($(this).text());
                getTourRank();
                //getTourPlayer();
            });


            // init swiper
            new Swiper('.tournament-swiper', {
                slidesPerView: 2.5,
                spaceBetween: 12,
                loop: false,
                /*
                autoplay: {
                delay: 2200,
                disableOnInteraction: false,
                },
                */
                breakpoints: {
                576: { slidesPerView: 3.5 },
                768: { slidesPerView: 5 },
                1024:{ slidesPerView: 7 }
                },
                pagination: {
                el: '.swiper-pagination',
                clickable: true,
                },
            });
    });
}

 /* ===== LineUP MVP ===== */
var teams=[
/*
 {id:1,name:'Barcelona',logo:'https://upload.wikimedia.org/wikipedia/en/4/47/FC_Barcelona_%28crest%29.svg'},
 {id:2,name:'Real Madrid',logo:'https://upload.wikimedia.org/wikipedia/en/5/56/Real_Madrid_CF.svg'},
 {id:3,name:'Man City',logo:'https://upload.wikimedia.org/wikipedia/en/e/eb/Manchester_City_FC_badge.svg'}
*/
];

var players=[
/*
 {id:1,team:1,name:'Messi',pos:'Attacker',sub:'CF',number:10,photo:'https://ui-avatars.com/api/?name=Messi'},
 {id:2,team:1,name:'Xavi',pos:'Midfielder',sub:'CM',number:6,photo:'https://ui-avatars.com/api/?name=Xavi'},
 {id:3,team:2,name:'Benzema',pos:'Attacker',sub:'CF',number:9,photo:'https://ui-avatars.com/api/?name=Benzema'},
 {id:33,team:2,name:'Cannavaro',pos:'Defender',sub:'CB',number:5,photo:'https://ui-avatars.com/api/?name=Cannavaro'},
 {id:4,team:2,name:'Modric',pos:'Midfielder',sub:'CM',number:10,photo:'https://ui-avatars.com/api/?name=Modric'},
 {id:5,team:3,name:'Haaland',pos:'Attacker',sub:'ST',number:9,photo:'https://ui-avatars.com/api/?name=Haaland'},
 {id:6,team:3,name:'Ederson',pos:'Goalkeeper',sub:'GK',number:31,photo:'https://ui-avatars.com/api/?name=Ederson'},
 {id:99,team:1,name:'Xavi',pos:'manager',sub:'Manager',number:'-',photo:'https://ui-avatars.com/api/?name=Xavi+Coach'}
 */
];

var map={Goalkeeper:'gk',Defender:'df',Midfielder:'mf',Attacker:'fw'};
var placed=new Set();

/* ===== mock lineup ===== */
let mockLineup=[
  /*
 {id:6,zone:'gk'},
 {id:33,zone:'df'},
 {id:2,zone:'mf'},
 {id:4,zone:'mf'},
 {id:1,zone:'fw'},
 {id:5,zone:'fw'},
 {id:99,zone:'manager'}
 */
  /*{id:13695,zone:'fw'}*/
];

/* ===== TEAM / PLAYER LIST ===== */
//teams.forEach(t=>$('#teamSelect').append(`<option value="${t.id}">${t.name}</option>`));

function refreshPlayerSelect(teamId){
  $('#playerSelect').prop('disabled',!teamId)
    .html('<option value="">-- เลือกนักเตะ --</option>');
  players.filter(p=>p.team==teamId).forEach(p=>{
    $('#playerSelect').append(
      `<option value="${p.id}" ${placed.has(p.id)?'disabled':''}>${p.name} (${p.sub})</option>`
    );
  });
}

$('#teamSelect').on('change',function(){ refreshPlayerSelect(this.value); });
$('#playerSelect').on('change',function(){
  const p=players.find(x=>x.id==this.value);
  if(p) $('#playerPos').val(p.pos);
});

/* ===== DRAG ===== */
//function makeDrag(el){ el.draggable({revert:'invalid',stack:'.player'}); }
//$('.zone').droppable({accept:'.player',drop:(e,ui)=>$(e.target).append(ui.draggable.css({top:0,left:0}))});

/* ===== INFO ===== */
function showInfo(p){
    console.log(p);
  //const team=teams.find(t=>t.id==p.team);
  //console.log(team);
  $('#playerInfo').html(`
    <div class="player-info-card">
      <img class="photo" src="${p.photo}" onerror="this.src='${NO_PROFILE}'" >
      <div>
        <div class="name">${p.name}</div>
        <div class="team"><img src="${p.team_logo}"  onerror="this.src='${DEFAULT_LOGO}'" width="22"> ${language === 'th' ? p.team_name : p.team_name_en}</div>
        <div class="meta">`+(position_label)+` • ${p.sub==null?'N/A':p.sub} </div>
        <div class="meta">`+(number_label)+` • ${p.number}</div>
      </div>
    </div>
  `);
}

function createPlayer(p){
  placed.add(p.id);
  const team=teams.find(t=>t.id==p.team);
  const el=$(`
    <div class="player">
      <img class="photo" src="${p.photo}" onerror="this.src='${NO_PROFILE}'">
      <div><strong>${p.name}</strong><div class="pos">${p.sub==null?'N/A':p.sub}</div></div>
      ${team?`<img class="logo" onerror="this.src='${DEFAULT_LOGO}'" src="${team.logo}">`:''}
      <!-- <span class="remove">✖</span>-->
    </div>
  `);
  el.on('click',()=>{$('.player').removeClass('active');el.addClass('active');showInfo(p);});
  el.find('.remove').on('click',e=>{
    e.stopPropagation();
    placed.delete(p.id);
    el.remove();
    refreshPlayerSelect($('#teamSelect').val());
  });
  //makeDrag(el);
  return el;
}

/* ===== ADD (จำกัดจำนวนผู้เล่น) ===== */
$('#addPlayer').on('click',()=>{
  const max=+$('#maxPlayers').val();
  if(placed.size>=max){
    alert('จำนวนนักเตะครบตามที่กำหนดแล้ว');
    return;
  }
  const pid=+$('#playerSelect').val();
  if(!pid || placed.has(pid)) return;
  const p=players.find(x=>x.id==pid);
  const zone=$('#playerPos').val();
  zone==='manager'
    ? $('.zone.manager').empty().append(createPlayer(p))
    : $('.zone.'+map[zone]).append(createPlayer(p));
  refreshPlayerSelect($('#teamSelect').val());
});

/* ===== LOAD / SAVE ===== */
function loadMock(){
  $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-mvp') ?>',{tour_id:tourId},res=>{
      if(!res?.data || res.data.length === 0){
        // แสดง mockup 4-4-2 เมื่อไม่มีข้อมูล
        show442Mockup();
        return;
      }
        mockLineup = res.data;
        console.log(res.num);
        $("#maxPlayers").val(res.num);
        console.log(mockLineup);

        //
        $('.zone').empty();
        placed.clear();

        $('#playerInfo').html(
          '<div class="empty-info">'+player_show_more_label+'</div>'
        );
          console.log(players);
        mockLineup.forEach(m=>{
          const p = players.find(x => Number(x.player_id) === Number(m.player_id));

          if (!p) {
            console.warn('ไม่พบ player:', m);
            return; // <<< สำคัญ ห้ามลบ
          }

          m.zone === 'manager'
            ? $('.zone.manager').append(createPlayer(p))
            : $('.zone.' + m.zone).append(createPlayer(p));
        });

        refreshPlayerSelect($('#teamSelect').val());
        //

      });
      //
}

$('#loadMock').on('click',loadMock);

/* ===== 4-4-2 MOCKUP ===== */
function show442Mockup(){
  $('.zone').empty();
  placed.clear();

  $('#playerInfo').html(
    '<div class="empty-info">'+player_show_more_label+'</div>'
  );

  // Mockup data สำหรับ 11 คน (4-4-2)
  const mockup11 = [
    { zone: 'gk', name: 'ผู้รักษาประตู', pos: 'GK' },
    { zone: 'df', name: 'กองหลัง 1', pos: 'DF' },
    { zone: 'df', name: 'กองหลัง 2', pos: 'DF' },
    { zone: 'df', name: 'กองหลัง 3', pos: 'DF' },
    { zone: 'df', name: 'กองหลัง 4', pos: 'DF' },
    { zone: 'mf', name: 'กองกลาง 1', pos: 'MF' },
    { zone: 'mf', name: 'กองกลาง 2', pos: 'MF' },
    { zone: 'mf', name: 'กองกลาง 3', pos: 'MF' },
    { zone: 'mf', name: 'กองกลาง 4', pos: 'MF' },
    { zone: 'fw', name: 'กองหน้า 1', pos: 'FW' },
    { zone: 'fw', name: 'กองหน้า 2', pos: 'FW' }
  ];

  mockup11.forEach(m => {
    const el = $(`
      <div class="player mockup-player">
        <div class="mockup-avatar">
          <i class="fas fa-user"></i>
        </div>
        <div><strong>${m.name}</strong><div class="pos">${m.pos}</div></div>
      </div>
    `);
    $('.zone.' + m.zone).append(el);
  });

  // เพิ่ม manager
  $('.zone.manager').append(`
    <div class="player mockup-player">
      <div class="mockup-avatar">
        <i class="fas fa-user-tie"></i>
      </div>
      <div><strong>ผู้จัดการทีม</strong><div class="pos">MGR</div></div>
    </div>
  `);
}

//$(tourTeams);
//$(loadMock);

function tourTeams(){
    // alert(tourId);
    //TourList
    $.getJSON('<?= base_url('admin/tournament-pg/get-tour-teams') ?>',{tour_id:tourId},res=>{
        if(!res?.data)return;
        //console.log();
        //teams = res.data;
        teams = res.data.map(team => ({
            ...team,
            id: team.tour_team_id,
            name: language === 'th' ? team.team_name : team.team_name_en
        }));
        $('#teamSelect').empty().append('<option value="">-- เลือกทีม --</option>');

        teams.forEach(t => {
            //console.log(t);
            $('#teamSelect').append(
                `<option value="${t.id}">
                    ${language === 'th' ? t.team_name : t.team_name_en}
                </option>`
            );
        });
        //console.log(teams);

        //getTourPlayer();
    });
    //
}
/* ===== END LineUP MVP ===== */

function teamClick(tour_id,team_id) {
    window.location.replace("<?php echo base_url();?>/home/team_profile/?tour_id="+tour_id+"&team_id="+team_id);
}

/* END Script รายงานผล */

$(document).ready(function() {
    animateCounter();
    initializeButtonEffects();
});

// Statistics Counter Animation - Khelo Style
function animateCounter() {
    // Animate inline stats
    $('.stat-inline-number, .card-number').each(function() {
        var $this = $(this);
        var countTo = parseInt($this.attr('data-count'));

        if (!countTo || isNaN(countTo)) return;

        var index = $(this).index();
        var delay = index * 150;

        setTimeout(function() {
            $({ countNum: 0 }).animate({
                countNum: countTo
            }, {
                duration: 2000,
                easing: 'swing',
                step: function() {
                    $this.text(Math.floor(this.countNum).toLocaleString('th-TH'));
                },
                complete: function() {
                    $this.text(countTo.toLocaleString('th-TH'));
                }
            });
        }, delay);
    });
}

// Scroll to next section
$('.scroll-down-indicator').on('click', function() {
    $('html, body').animate({
        scrollTop: $('.feature-section').offset().top - 80
    }, 800);
});

// Add AOS (Animate On Scroll) effect manually if not loaded
if (typeof AOS === 'undefined') {
    $('[data-aos]').css('opacity', '0');

    $(window).on('scroll load', function() {
        $('[data-aos]').each(function() {
            var elementTop = $(this).offset().top;
            var elementBottom = elementTop + $(this).outerHeight();
            var viewportTop = $(window).scrollTop();
            var viewportBottom = viewportTop + $(window).height();

            if (elementBottom > viewportTop && elementTop < viewportBottom) {
                $(this).css({
                    'opacity': '1',
                    'transform': 'translateY(0)',
                    'transition': 'all 0.6s ease'
                });
            }
        });
    }).trigger('scroll');
}

// Initialize button effects
function initializeButtonEffects() {
    // Add entrance animation for action buttons
    $('.action-btn').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(20px)'
        }).delay(300 + (index * 150)).animate({
            'opacity': '1'
        }, {
            duration: 600,
            complete: function() {
                $(this).css('transform', 'translateY(0)');
            }
        });
    });

    // Parallax effect for logo on scroll
    if ($(window).width() > 768) {
        $(window).on('scroll', function() {
            var scrolled = $(window).scrollTop();
            $('.hero-logo-img').css('transform', 'translateY(' + (scrolled * 0.3) + 'px)');
        });
    }
}

// Add touch feedback for mobile
if ('ontouchstart' in window) {
    $('.action-btn').on('touchstart', function() {
        $(this).css('transform', 'scale(0.98)');
    }).on('touchend', function() {
        var $this = $(this);
        setTimeout(() => {
            $this.css('transform', '');
        }, 150);
    });
}

// Intersection Observer for scroll animations
if ('IntersectionObserver' in window) {
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -100px 0px'
    };

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('animate-in');
            }
        });
    }, observerOptions);

    // Observe stat cards
    document.querySelectorAll('.stat-card').forEach(card => {
        observer.observe(card);
    });
}

// Smooth scrolling for navigation links
$('a[href^="#"]').on('click', function(event) {
    var target = $(this.getAttribute('href'));
    if( target.length ) {
        event.preventDefault();
        $('html, body').stop().animate({
            scrollTop: target.offset().top - 80
        }, 1000);
    }
});

// Add scroll animations
$(window).scroll(function() {
    $('.feature-highlight, .service-card, .work-item, .modern-card').each(function(index) {
        var elementTop = $(this).offset().top;
        var elementBottom = elementTop + $(this).outerHeight();
        var viewportTop = $(window).scrollTop();
        var viewportBottom = viewportTop + $(window).height();

        if (elementBottom > viewportTop && elementTop < viewportBottom) {
            setTimeout(() => {
                $(this).addClass('animate-in');
            }, index * 100);
        }
    });
});

// Add CSS for scroll animations
$('<style>').prop('type', 'text/css').html(`
    .animate-in {
        animation: slideInUp 0.6s ease-out forwards;
    }

    @keyframes slideInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .feature-highlight,
    .service-card,
    .work-item,
    .modern-card {
        opacity: 0;
        transform: translateY(30px);
        transition: all 0.6s ease;
    }

    .animate-in.feature-highlight,
    .animate-in.service-card,
    .animate-in.work-item,
    .animate-in.modern-card {
        opacity: 1;
        transform: translateY(0);
    }

    /* Premium Sponsors Section Styles */
    .sponsors-section {
        padding: 80px 0;
        background: linear-gradient(135deg, #0a1f35 0%, #1a3a5c 50%, #0a1f35 100%);
        position: relative;
        overflow: hidden;
    }

    .sponsors-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: url('<?= base_url('public/img/pattern-overlay.png') ?>') repeat;
        opacity: 0.05;
        pointer-events: none;
    }

    .sponsors-header {
        position: relative;
        z-index: 1;
    }

    .sponsors-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 80px;
        height: 80px;
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        border-radius: 50%;
        margin-bottom: 20px;
        box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3);
        animation: pulse-gold 2s ease-in-out infinite;
    }

    .sponsors-badge i {
        font-size: 32px;
        color: #0a1f35;
    }

    @keyframes pulse-gold {
        0%, 100% { transform: scale(1); box-shadow: 0 10px 30px rgba(255, 215, 0, 0.3); }
        50% { transform: scale(1.05); box-shadow: 0 15px 40px rgba(255, 215, 0, 0.5); }
    }

    .sponsors-title {
        font-size: 2.5rem;
        font-weight: 700;
        color: #ffffff;
        margin-bottom: 15px;
        text-transform: uppercase;
        letter-spacing: 2px;
        background: linear-gradient(135deg, #ffd700, #ffed4e, #ffffff);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .sponsors-subtitle {
        font-size: 1.1rem;
        color: rgba(255, 255, 255, 0.7);
        max-width: 600px;
        margin: 0 auto 20px;
    }

    .sponsors-divider {
        width: 100px;
        height: 3px;
        background: linear-gradient(90deg, transparent, #ffd700, transparent);
        margin: 20px auto;
    }

    .sponsors-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
        gap: 30px;
        margin-bottom: 50px;
        position: relative;
        z-index: 1;
    }

    .sponsor-item {
        display: flex;
        justify-content: center;
    }

    .sponsor-card {
        background: rgba(255, 255, 255, 0.05);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.1);
        border-radius: 20px;
        padding: 30px;
        text-align: center;
        transition: all 0.4s ease;
        position: relative;
        overflow: hidden;
        width: 100%;
        max-width: 350px;
    }

    .sponsor-card::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        opacity: 0;
        transition: opacity 0.4s ease;
    }

    .sponsor-card:hover::before {
        opacity: 1;
    }

    .sponsor-card:hover {
        transform: translateY(-10px);
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
    }

    .sponsor-platinum .sponsor-card {
        border-color: rgba(229, 228, 226, 0.6);
        box-shadow: 0 0 30px rgba(229, 228, 226, 0.2);
    }

    .sponsor-gold .sponsor-card {
        border-color: rgba(255, 215, 0, 0.5);
        box-shadow: 0 0 20px rgba(255, 215, 0, 0.15);
    }

    .sponsor-silver .sponsor-card {
        border-color: rgba(192, 192, 192, 0.5);
        box-shadow: 0 0 20px rgba(192, 192, 192, 0.15);
    }

    .sponsor-badge-wrapper {
        margin-bottom: 15px;
    }

    .sponsor-tier-badge {
        display: inline-block;
        padding: 6px 20px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 700;
        letter-spacing: 1.5px;
        text-transform: uppercase;
    }

    .sponsor-tier-badge.platinum {
        background: linear-gradient(135deg, #e5e4e2, #ffffff);
        color: #0a1f35;
        box-shadow: 0 5px 15px rgba(229, 228, 226, 0.4);
    }

    .sponsor-tier-badge.gold {
        background: linear-gradient(135deg, #ffd700, #ffed4e);
        color: #0a1f35;
        box-shadow: 0 5px 15px rgba(255, 215, 0, 0.4);
    }

    .sponsor-tier-badge.silver {
        background: linear-gradient(135deg, #c0c0c0, #e8e8e8);
        color: #0a1f35;
        box-shadow: 0 5px 15px rgba(192, 192, 192, 0.4);
    }

    .sponsor-logo-wrapper {
        position: relative;
        width: 150px;
        height: 150px;
        margin: 0 auto 20px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .sponsor-glow {
        position: absolute;
        width: 120px;
        height: 120px;
        background: radial-gradient(circle, rgba(255,255,255,0.2) 0%, transparent 70%);
        border-radius: 50%;
        animation: glow-pulse 3s ease-in-out infinite;
    }

    @keyframes glow-pulse {
        0%, 100% { opacity: 0.5; transform: scale(1); }
        50% { opacity: 1; transform: scale(1.1); }
    }

    .sponsor-logo {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: drop-shadow(0 5px 15px rgba(0,0,0,0.3));
        transition: transform 0.4s ease;
    }

    .sponsor-card:hover .sponsor-logo {
        transform: scale(1.1);
    }

    .sponsor-info {
        position: relative;
        z-index: 1;
    }

    .sponsor-name {
        font-size: 1.1rem;
        font-weight: 600;
        color: #ffffff;
        margin-bottom: 8px;
    }

    .sponsor-desc {
        font-size: 0.9rem;
        color: rgba(255, 255, 255, 0.6);
        margin: 0;
    }

    /* Partners Strip */
    .partners-strip {
        position: relative;
        z-index: 1;
        padding: 30px 0;
        background: rgba(0, 0, 0, 0.2);
        border-radius: 15px;
        overflow: hidden;
    }

    .partners-wrapper {
        overflow: hidden;
    }

    .partners-scroll {
        display: flex;
        gap: 50px;
        animation: scroll-partners 30s linear infinite;
        width: max-content;
    }

    @keyframes scroll-partners {
        0% { transform: translateX(0); }
        100% { transform: translateX(-50%); }
    }

    .partners-scroll:hover {
        animation-play-state: paused;
    }

    .partner-logo-item {
        width: 120px;
        height: 80px;
        display: flex;
        align-items: center;
        justify-content: center;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 10px;
        padding: 15px;
        transition: all 0.3s ease;
    }

    .partner-logo-item:hover {
        background: rgba(255, 255, 255, 0.1);
        transform: scale(1.05);
    }

    .partner-logo-item img {
        max-width: 100%;
        max-height: 100%;
        object-fit: contain;
        filter: grayscale(50%) brightness(1.2);
        transition: all 0.3s ease;
    }

    .partner-logo-item:hover img {
        filter: grayscale(0%) brightness(1);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sponsors-title {
            font-size: 1.8rem;
        }

        .sponsors-grid {
            grid-template-columns: 1fr;
        }

        .sponsor-card {
            max-width: 100%;
        }

        .partners-scroll {
            animation-duration: 20s;
        }

        .partner-logo-item {
            width: 100px;
            height: 60px;
        }
    }
`).appendTo('head');
</script>
