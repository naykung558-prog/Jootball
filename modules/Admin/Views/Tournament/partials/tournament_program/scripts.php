<script>
const tourId='<?= $tour_id ?>';
const language = '<?=session()->get('language')?>';

// Lang declarations
const langTba = '<?= lang("Common.tba") ?>';
const langUnknownDate = '<?= lang("Common.unknown_date") ?>';
const langGroup = '<?= lang("Common.group_label") ?>';
const langUnknownTeam = '<?= lang("Common.unknown_team") ?>';
const langPlayed = '<?= lang("Common.played") ?>';
const langWon = '<?= lang("Common.won") ?>';
const langDrawn = '<?= lang("Common.drawn") ?>';
const langLost = '<?= lang("Common.lost") ?>';
const langGf = '<?= lang("Common.goals_for") ?>';
const langGa = '<?= lang("Common.goals_against") ?>';
const langGd = '<?= lang("Common.goal_diff") ?>';
const langPts = '<?= lang("Common.points") ?>';
const langTeamLabel = '<?= lang("Common.teams") ?>';
const langSelectTeam = '<?= lang("Common.select_team") ?>';
const langSelectPlayer = '<?= lang("Common.select_player") ?>';
const langPlayersFull = '<?= lang("Common.players_full_alert") ?>';
const langClickPlayerOnField = '<?= lang("Common.click_player_on_field") ?>';
const langNumber = '<?= lang("Common.shirt_number") ?>';
const langSaveSuccess = '<?= lang("Common.save_success") ?>';
const langSaveError = '<?= lang("Common.error_occurred") ?>';
const langCannotSaveData = '<?= lang("Common.cannot_save") ?>';
//const DEFAULT_LOGO='https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg';
const DEFAULT_LOGO = '<?= base_url('public/img/jootball_logo.png') ?>';
const Stadium_logo = '<?= base_url('public/img/position_demo.png') ?>';
const NO_PROFILE = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
const round_match = '<?= lang('Tournament.round_match') ?>';

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



function teamRow(name,score,logo,pen){
 const src=logo?`<?=base_url()?>/${logo}`:DEFAULT_LOGO;
 //console.log(logo);
 return `<div class="team">
   <div class="team-left">
     <img src="${src}" class="team-logo" onerror="this.src='${DEFAULT_LOGO}'">
     <span>${name??langTba}</span>
   </div>
   <div class="score">${score??'-'} ${pen?'<b style="position:absolute;">(P)</b>':''}</div>
 </div>`;
}

function renderMatches(data){
  if (!Array.isArray(data) || !data.length) {
    matchContainer.innerHTML = '-';
    return;
  }

  // 🔥 แก้ปัญหาเรื่องโดนจัดเรียง: ทำการ Clone ข้อมูลออกมาแล้วเรียงลำดับให้ถูกต้อง (ไม่ใช้ d.reverse() ตรงๆ ที่จะไปทำลายข้อมูลเดิม)
  // โดยเรียงตามโครงสร้างธรรมชาติ: รอบการแข่งขัน และ วันที่/เวลา
  const sortedMatches = [...data].sort((a, b) => {
    // 1. เรียงตามรอบการแข่งขัน (ถ้ามี stage_id หรือ round)
    const stageA = parseInt(a.stage_id || a.state_id || 0);
    const stageB = parseInt(b.stage_id || b.state_id || 0);
    if (stageA !== stageB) return stageA - stageB;

    // 2. เรียงตามวันที่ (เก่าไปใหม่)
    const dateA = a.date || '';
    const dateB = b.date || '';
    if (dateA !== dateB) return dateA.localeCompare(dateB);

    // 3. เรียงตามเวลา (เช้าไปเย็น)
    const timeA = a.time || '';
    const timeB = b.time || '';
    return timeA.localeCompare(timeB);
  });

  let h = '';
  let lastKey = '';
  let c = 0;

  // วนลูปผ่านตัวแปร sortedMatches ที่ถูกจัดกลุ่มตามธรรมชาติอย่างถูกต้องแล้ว
  sortedMatches.forEach((m, i) => {

    // ✅ key = รอบ + วัน เท่านั้น
    const key = `${m.match_round}_${m.date || m.state_id}`;

    // ===== ขึ้นหัวใหม่เมื่อเปลี่ยนวัน / รอบ =====
    if (key !== lastKey) {
      if (lastKey !== '') h += '</div>';

      h += `
        <div class="day-header">
          ${m.match_round} · ${formatThaiDate(m.date)}
        </div>
        <div class="row g-0">
      `;

      lastKey = key;
      c = 0;
    }

    // ===== render match =====
    h += `
      <div style="position:relative" class="col-6 match-cell ${c % 2 ? '' : 'left'}">
        ${m.group_id ? `<div class="group">${langGroup} ${m.group_id}</div>` : '<div class="group">&nbsp;</div>'}
        <div class="match-info" style="position:absolute; right:5px; top:5px;font-size:13px">
          <div>${m.date ? formatThaiDate(m.date) : langUnknownDate}</div>
          <div>${m.time?.slice(0, 5) ?? ''}</div>
        </div>
        ${teamRow(language=='th'?m.team1_name:m.team1_name_en, m.team1_score, m.team1_logo, ((m.team1_id==m.winner_team_id || m.result==1) && m.is_penalty_used==1))}
        ${teamRow(language=='th'?m.team2_name:m.team2_name_en, m.team2_score, m.team2_logo, ((m.team2_id==m.winner_team_id || m.result==2) && m.is_penalty_used==1))}
        <div class="row">
            <div class="col-6" style="font-size: 13px;">Stadium ${language=='th' ? (m.stadium ?? '-') : (m.stadium_en ?? m.stadium ?? '-')}</div>
            <div class="col-3">
                <div class="match-info">
                    <div class="match-btn" onclick="match_sto(this)" data-event='${JSON.stringify(m.event ?? [])}' style="cursor:pointer">MATCH STORY</div>
                </div>
            </div>
            <div class="col-3">
                <div class="match-info">
                    <div class="match-btn btn-danger" onclick="deleteMatch(${m.match_id})" style="cursor:pointer">DELETE</div>
                </div>
            </div>
        </div>
      </div>
    `;

    c++;
  });

  // ✅ ปิด row เฉพาะตอนที่มีการเปิด
  if (lastKey !== '') {
    h += '</div>';
  }
  
  matchContainer.innerHTML = h == '' ? '-' : h;
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

// Delete match function
function deleteMatch(matchId) {
  if (!confirm('คุณแน่ใจว่าต้องการลบแมตช์นี้?')) {
    return;
  }
  
  $.ajax({
    url: '<?= base_url('admin/tournament/delete-match') ?>',
    type: 'POST',
    data: { match_id: matchId },
    dataType: 'json',
    success: function(response) {
      if (response.status === 'success') {
        alert('ลบแมตช์สำเร็จ');
        // Reload matches
        if (typeof loadMatches === 'function') {
          loadMatches();
        } else {
          location.reload();
        }
      } else {
        alert('เกิดข้อผิดพลาด: ' + (response.message || 'ไม่สามารถลบแมตช์ได้'));
      }
    },
    error: function(xhr, status, error) {
      alert('เกิดข้อผิดพลาดในการลบแมตช์: ' + error);
    }
  });
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
          <span class="badge bg-secondary">${v.minute ?? '-'}</span>
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
  const order = [];

  // Group by match_round
  data.forEach(m => {
    const r = m.match_round || 'Round';
    if (!map[r]) {
      map[r] = [];
      order.push(r);
    }
    map[r].push(m);
  });

  // ==========================================
  // 🔥 แก้ไขตรงนี้: ใช้การ Sort ลำดับโครงสร้างทัวร์นาเมนต์ที่แท้จริงแทนการใช้ .reverse()
  // ==========================================
  order.sort((a, b) => {
    const matchA = map[a][0];
    const matchB = map[b][0];

    // 1. เรียงตาม stage_id หรือ state_id (รอบแรกๆ ID จะน้อยกว่ารอบลึกๆ เสมอ)
    const stageA = parseInt(matchA.stage_id || matchA.state_id || 0);
    const stageB = parseInt(matchB.stage_id || matchB.state_id || 0);
    if (stageA !== stageB) return stageA - stageB;

    // 2. ถ้าหากดึง stage มาไม่ได้ ให้เปรียบเทียบตามวันที่แข่งของแมตช์แรกในรอบนั้น (เก่าไปใหม่)
    const dateA = matchA.date || '';
    const dateB = matchB.date || '';
    return dateA.localeCompare(dateB);
  });
  // ==========================================

  // Create Nav Tabs
  let navHtml = '<ul class="nav nav-pills" id="bracketRoundTabs" role="tablist">';
  let contentHtml = '<div class="tab-content" id="bracketRoundTabsContent">';
  const roundDayCounters = {};

  order.forEach((r, idx) => {
    const activeClass = idx === 0 ? 'active' : '';
    const showActiveClass = idx === 0 ? 'show active' : '';
    const tabId = `bracket-round-${idx}`;
    const isKnockout = idx > 0 || map[r].length <= 8; 

    navHtml += `
      <li class="nav-item" role="presentation">
        <button class="nav-link ${activeClass} px-4 fw-bold" id="${tabId}-tab" data-bs-toggle="pill" data-bs-target="#${tabId}" type="button" role="tab">${r}</button>
      </li>
    `;

    contentHtml += `<div class="tab-pane fade ${showActiveClass}" id="${tabId}" role="tabpanel">`;

    // --- แยกแมตช์ธรรมดา กับ แมตช์ที่เป็น final_match ออกจากกัน ---
    const currentMatches = map[r];
    const normalMatches = currentMatches.filter(m => parseInt(m.final_match) !== 1);
    const finalMatches = currentMatches.filter(m => parseInt(m.final_match) === 1);

    // 1. เรนเดอร์กลุ่มแมตช์ธรรมดา
    if (idx === 0) {
      // --- รอบแรก: จัดแสดงผลเป็นตาราง แบ่งตามสนาม (Stadium) ---
      const stadiumGroups = {};
      normalMatches.forEach(m => {
        const std = language === 'th' ? (m.stadium ?? '-') : (m.stadium_en ?? m.stadium ?? '-');
        if (!stadiumGroups[std]) stadiumGroups[std] = [];
        stadiumGroups[std].push(m);
      });

      const stadiums = Object.keys(stadiumGroups).sort();
      contentHtml += '<div class="row g-2">'; // ลดช่องว่างระหว่างคอลัมน์
      
      stadiums.forEach(std => {
        contentHtml += `
          <div class="col-lg-6">
            <div class="stadium-header p-2 mb-0 text-white shadow-sm" style="background: #16478D; border-radius: 8px 8px 0 0;">
              <h6 class="fw-bold mb-0 text-center text-white">
                <i class="ti ti-map-pin me-2"></i>${language === 'th' ? 'สนาม' : 'Stadium'}: ${std}
              </h6>
            </div>
            <div class="table-responsive shadow-sm" style="border-radius: 0 0 8px 8px;">
              <table class="table table-sm table-bordered align-middle mb-0 text-center" style="font-size: 0.75rem; border: 1px solid #dee2e6; table-layout: fixed;">
                <thead style="background: #e7f1ff; color: #16478D;">
                  <tr>
                    <th style="width: 5%;">No</th>
                    <th style="width: 10%;">${language === 'th' ? 'เวลา' : 'Time'}</th>
                    <th style="width: 10%;">Group</th>
                    <th style="width: 30%;">${language === 'th' ? 'ทีม 1' : 'Team 1'}</th>
                    <th style="width: 10%;">Score</th>
                    <th style="width: 35%;">${language === 'th' ? 'ทีม 2' : 'Team 2'}</th>
                  </tr>
                </thead>
                <tbody>
        `;

        // จัดกลุ่มตามวันที่ภายในสนาม
        const dateGroups = {};
        stadiumGroups[std].forEach(m => {
          const d = m.date || 'TBD';
          if (!dateGroups[d]) dateGroups[d] = [];
          dateGroups[d].push(m);
        });

        Object.keys(dateGroups).sort().forEach(date => {
          // --- หัวข้อวันที่ (ขึ้นกลุ่มวันที่ใหม่) ---
          contentHtml += `
            <tr style="background: #f1f3f5;">
              <td colspan="6" class="text-start ps-3 py-1 fw-bold" style="color: #495057; font-size: 0.75rem;">
                <i class="ti ti-calendar me-1"></i> ${date !== 'TBD' ? formatThaiDate(date) : langUnknownDate}
              </td>
            </tr>
          `;

          const matches = dateGroups[date].sort((a, b) => (a.time || '').localeCompare(b.time || ''));
          matches.forEach((m, mIdx) => {
            const seqKey = `${r}_${m.date || langUnknownDate}`;
            const seq = (roundDayCounters[seqKey] || 0) + 1;
            roundDayCounters[seqKey] = seq;

            contentHtml += `
              <tr style="background: #fff;">
                <td>${seq}</td>
                <td class="fw-bold" style="color: #1e5bb5;">${m.time?.slice(0, 5) ?? '-'}</td>
                <td><span class="badge bg-light text-dark border-0 p-1" style="font-size: 0.65rem;">${m.group_id ?? '-'}</span></td>
                <td class="text-start">
                  <div class="d-flex align-items-center">
                    <img src="${m.team1_logo ? `<?=base_url()?>/${m.team1_logo}` : DEFAULT_LOGO}" class="me-1" style="width: 18px; height: 18px; object-fit: cover;" onerror="this.src='${DEFAULT_LOGO}'">
                    <span class="text-truncate">${language === 'th' ? m.team1_name : m.team1_name_en}</span>
                  </div>
                </td>
                <td class="fw-bold" style="background: #f8fafc; border-left: 2px solid #e7f1ff; border-right: 2px solid #e7f1ff;">
                  ${m.team1_score ?? '-'} - ${m.team2_score ?? '-'}
                </td>
                <td class="text-start">
                  <div class="d-flex align-items-center">
                    <img src="${m.team2_logo ? `<?=base_url()?>/${m.team2_logo}` : DEFAULT_LOGO}" class="me-1" style="width: 18px; height: 18px; object-fit: cover;" onerror="this.src='${DEFAULT_LOGO}'">
                    <span class="text-truncate">${language === 'th' ? m.team2_name : m.team2_name_en}</span>
                  </div>
                </td>
              </tr>
            `;
          });
        });

        contentHtml += `
                </tbody>
              </table>
            </div>
          </div>
        `;
      });
      contentHtml += '</div>';

    } else if (isKnockout && idx < order.length - 1 && normalMatches.length > 0) {
      contentHtml += '<div class="bracket-pair-container">';
      normalMatches.forEach(m => {
        const seqKey = `${r}_${m.date || langUnknownDate}`;
        const seq = (roundDayCounters[seqKey] || 0) + 1;
        roundDayCounters[seqKey] = seq;

        contentHtml += `
          <div class="bracket-pair">
            <div class="bracket-match-item shadow-sm">
              <div class="bracket-seq">${round_match} ${seq}</div>
              <div class="bracket-connector-v"></div>
              <div class="bracket-connector-h"></div>
              ${bracketTeam(language == 'th' ? m.team1_name : m.team1_name_en, m.team1_score, m.team1_logo, getBracketTeamClass(m, 1))}
              ${bracketTeam(language == 'th' ? m.team2_name : m.team2_name_en, m.team2_score, m.team2_logo, getBracketTeamClass(m, 2))}
              <div class="mt-1" style="font-size: 10px; color: #999;">
                <div>${m.time?.slice(0, 5) ?? ''}</div>
                <div>Stadium ${language=='th' ? (m.stadium ?? '-') : (m.stadium_en ?? m.stadium ?? '-')}</div>
              </div>
            </div>
          </div>
        `;
      });
      contentHtml += '</div>';

    } else if (normalMatches.length > 0) {
      contentHtml += '<div class="row g-3 justify-content-center mb-3">';
      normalMatches.forEach(m => {
        const seqKey = `${r}_${m.date || langUnknownDate}`;
        const seq = (roundDayCounters[seqKey] || 0) + 1;
        roundDayCounters[seqKey] = seq;

        contentHtml += `
          <div class="col-md-6 col-lg-4">
            <div class="bracket-match h-100 mb-0 shadow-sm border">
              <div class="bracket-seq">${round_match} ${seq}</div>
              ${bracketTeam(language == 'th' ? m.team1_name : m.team1_name_en, m.team1_score, m.team1_logo, getBracketTeamClass(m, 1))}
              ${bracketTeam(language == 'th' ? m.team2_name : m.team2_name_en, m.team2_score, m.team2_logo, getBracketTeamClass(m, 2))}
              <div class="mt-2" style="font-size: 11px; color: #888;">
                <div>${m.date ? formatThaiDate(m.date) : ''} ${m.time?.slice(0, 5) ?? ''}</div>
                <div>Stadium ${language=='th' ? (m.stadium ?? '-') : (m.stadium_en ?? m.stadium ?? '-')}</div>
              </div>
            </div>
          </div>
        `;
      });
      contentHtml += '</div>';
    }

    // 2. เรนเดอร์กลุ่ม final_match = 1 (แมตช์ชิง)
    if (finalMatches.length > 0) {
      contentHtml += '<div class="row g-3 justify-content-center mt-2">';
      finalMatches.forEach(m => {
        const seqKey = `${r}_${m.date || langUnknownDate}`;
        const seq = (roundDayCounters[seqKey] || 0) + 1;
        roundDayCounters[seqKey] = seq;

        contentHtml += `
          <div class="col-md-6 col-lg-4">
            <div class="bracket-match h-100 mb-0 shadow-sm border" style="border-top: 3px solid #ffc107 !important;">
              <div class="bracket-seq bg-warning text-dark">${round_match} ${seq}</div>
              ${bracketTeam(language == 'th' ? m.team1_name : m.team1_name_en, m.team1_score, m.team1_logo, getBracketTeamClass(m, 1))}
              ${bracketTeam(language == 'th' ? m.team2_name : m.team2_name_en, m.team2_score, m.team2_logo, getBracketTeamClass(m, 2))}
              <div class="mt-2" style="font-size: 11px; color: #888;">
                <div>${m.date ? formatThaiDate(m.date) : ''} ${m.time?.slice(0, 5) ?? ''}</div>
                <div>Stadium ${language=='th' ? (m.stadium ?? '-') : (m.stadium_en ?? m.stadium ?? '-')}</div>
              </div>
            </div>
          </div>
        `;
      });
      contentHtml += '</div>';
    }

    contentHtml += '</div>'; // ปิด tab-pane
  });

  navHtml += '</ul>';
  contentHtml += '</div>';

  bracketContainer.innerHTML = navHtml + contentHtml;
}

function getBracketWinnerSide(match){
  if (Number(match?.winner_team_id) && String(match.winner_team_id) === String(match.team1_id)) return 1;
  if (Number(match?.winner_team_id) && String(match.winner_team_id) === String(match.team2_id)) return 2;
  if (Number(match?.result) === 1) return 1;
  if (Number(match?.result) === 2) return 2;
  return 0;
}

function getBracketTeamClass(match, side){
  const winnerSide = getBracketWinnerSide(match);
  if (!winnerSide) return '';
  return winnerSide === side ? 'is-winner' : 'is-loser';
}

function bracketTeam(name,score,logo,statusClass = ''){
 const src=logo?`<?=base_url()?>/${logo}`:DEFAULT_LOGO;
 return `<div class="bracket-team ${statusClass}">
   <div class="bracket-left">
     <img src="${src}" class="bracket-logo" onerror="this.src='${DEFAULT_LOGO}'">
     <span>${name??langTba}</span>
   </div>
   <strong>${score??'-'}</strong>
 </div>`;
}

function drawBracketLines(){
 const svg=bracketLines;svg.innerHTML='';
 const cols=document.querySelectorAll('.bracket-col');
 const p=svg.getBoundingClientRect();
 for(let i=0;i<cols.length-1;i++){
  const L=cols[i].querySelectorAll('.bracket-match');
  const R=cols[i+1].querySelectorAll('.bracket-match');
  L.forEach((m,j)=>{
   if(!R[j])return;
   const a=m.getBoundingClientRect(),b=R[j].getBoundingClientRect();
   const x1=a.right-p.left,y1=a.top+a.height/2-p.top;
   const x2=b.left-p.left,y2=b.top+b.height/2-p.top;
   const path=document.createElementNS('http://www.w3.org/2000/svg','path');
   path.setAttribute('d',`M${x1},${y1} C${x1+30},${y1} ${x2-30},${y2} ${x2},${y2}`);
   //path.setAttribute('stroke','#adb5bd');
   //path.setAttribute('stroke-width','2');
   path.setAttribute('fill','none');
   svg.appendChild(path);
  });
 }
}

function renderRanking(data){

// Filter เฉพาะรอบที่เป็น League (league_single, league_double, league)
// ห้ามคัดด้วย group_id — รอบลีกที่ตั้งกลุ่มเดียว (group_count = 1) ไม่ได้เขียน group_id
// ลงแถวแมตช์เลย ถ้าคัดทิ้งตารางคะแนนจะหายทั้งรอบ (tour 116, 105, 104, 102)
data = data.filter(d => d.format && d.format.toLowerCase().includes('league'));

//console.log('Ranking data:', data);

  const groups = {};

  data.forEach(m => {
    const t1 = parseInt(m.team1_id, 10) ?? 0;
    const t2 = parseInt(m.team2_id, 10) ?? 0;

    if (!groups[m.group_id]) groups[m.group_id] = {};

    // ===== init ทีม =====
    // Normalize ชื่อทีม: trim และแปลงเป็นตัวพิมพ์เล็กเพื่อเปรียบเทียบ
    const name1 = m.team1_name?.trim() || 'Unknown';
    const name2 = m.team2_name?.trim() || 'Unknown';

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

      // H2H - ใช้ name ที่ normalize แล้ว (ตัวพิมพ์เล็ก) เป็น key
      const h2hA = a.h2h[b.name?.toLowerCase()];
      const h2hB = b.h2h[a.name?.toLowerCase()];
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
      <div class="group-title">${langGroup} ${g=='null'?'-':g}</div>
      <table class="ranking-table">
        <thead>
          <tr>
            <th>#</th><th width="40%">${langTeamLabel}</th><th>${langPlayed}</th>
            <th>${langWon}</th><th>${langDrawn}</th><th>${langLost}</th>
            <th>${langGf}</th><th>${langGa}</th><th>${langGd}</th><th>${langPts}</th>
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
    name: name ?? langUnknownTeam,
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
  // ใช้ name ที่ normalize แล้ว (ตัวพิมพ์เล็ก) เป็น key ใน h2h
  const bKey = B.name?.toLowerCase();
  if (!A.h2h[bKey]) A.h2h[bKey]={pts:0,gf:0,ga:0,gd:0};
  const h=A.h2h[bKey];
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

function formatThaiDate(d){
 if(!d)return'';
 const m=['ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
 const x=new Date(d);
 return `${x.getDate()} ${m[x.getMonth()]} ${x.getFullYear()+543}`;
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
        const teamName = p.team_name || 'N/A';

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
        players.forEach(p=>$('#playerSelect').append(`<option value="${p.id}">${shirtTag(p.number)}${p.name} ${p.name_en}</option>`));
      //

      loadMock();
    }
  );
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

  // 1. เรียงตามตัวอักษรปกติ
  $('#filterStadium').find('option:not(:first)').remove();
  Array.from(stadiums).sort().forEach(s => $('#filterStadium').append(`<option value="${s}">${s}</option>`));

  // 2. 🔥 แก้ไขส่วนนี้: เรียงลำดับวันที่จากน้อยไปมาก (เก่าไปใหม่) โดยแปลงเป็น Object วันที่เพื่อเปรียบเทียบ
  $('#filterDate').find('option:not(:first)').remove();
  Array.from(dates)
    .sort((a, b) => new Date(a) - new Date(b)) // เปรียบเทียบค่า Timestamp ของวัน
    .forEach(d => $('#filterDate').append(`<option value="${d}">${formatThaiDate(d)}</option>`));

  // 3. เรียงรอบการแข่งขันปกติ
  $('#filterRound').find('option:not(:first)').remove();
  Array.from(rounds).sort().forEach(r => $('#filterRound').append(`<option value="${r}">${r}</option>`));
}

function sortMatchesByDate(data) {
  if (!Array.isArray(data)) return [];
  return [...data].sort((a, b) => {
    const dateA = a.date || '9999-12-31';
    const dateB = b.date || '9999-12-31';
    return dateA.localeCompare(dateB);
  });
}

function applyMatchFilters() {
  if (!bracketData) return;

  const stadium = $('#filterStadium').val();
  const date = $('#filterDate').val();
  const round = $('#filterRound').val();

  let filtered = bracketData.filter(m => {
    const std = language === 'th' ? (m.stadium ?? '-') : (m.stadium_en ?? m.stadium ?? '-');
    const matchStadium = stadium === "" || std === stadium;
    const matchDate = date === "" || m.date === date;
    const matchRound = round === "" || m.match_round === round;
    return matchStadium && matchDate && matchRound;
  });

  filtered = sortMatchesByDate(filtered);
  renderMatches(filtered);
}

function teamStatInfo() {

    $.getJSON('<?= base_url('admin/tournament-pg/get-team-statistics') ?>',{tour_id:tourId},res=>{
        if(!res?.data)return;
        //console.log(res.data);
        $("#sum-math").html(res.data.match_played_pre);
        $("#sum-team").html(res.data.team_count);
        $("#sum-coach").html(res.data.coach_count);
        $("#sum-player").html(res.data.player_count);
        $("#sum-card").html(res.data.sum_card);
        $("#sum-best-player").html(res.data.sum_best_player);
    });
}

var bracketData = null;

$(function(){

    //main match
  $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-match-for-prog-rank-result') ?>',{tour_id:tourId},res=>{
    if(!res?.data)return;

    bracketData = sortMatchesByDate(res.data);
    getTourPlayer();
    populateFilters(bracketData);

    //sortData(res.data);      // <<< น้อย → มาก
    renderMatches(bracketData);
    //sortMatchCellByGroup(); // ยกเลิกการเรียงลำดับภายในกลุ่ม
    renderRanking(res.data);
    teamStatInfo();
    
    // Render bracket ถ้า tab map กำลังแสดงอยู่
    if($('button[data-bs-target="#map"]').hasClass('active')) {
      renderBracket(bracketData);
      setTimeout(drawBracketLines, 50);
    }
  });

  // Event Listeners for Filters
  $('#filterStadium, #filterDate, #filterRound').on('change', applyMatchFilters);

  // Render bracket when tab is clicked
  $('button[data-bs-target="#map"]').on('shown.bs.tab', function () {
    if(bracketData) {
      renderBracket(bracketData);
      setTimeout(drawBracketLines, 50);
    }
  });

  // Render players when tab is clicked
  $('button[data-bs-target="#player"]').on('shown.bs.tab', function () {
    if(bracketData) {
      getTourPlayer();
    }
  });

  window.addEventListener('resize',drawBracketLines);
  //
});


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
let mockLineup=[];

/* ===== TEAM / PLAYER LIST ===== */
// เบอร์เสื้อนำหน้าชื่อ — คนที่ยังไม่มีเบอร์ปล่อยว่างไว้ ไม่ต้องโชว์ # เปล่า ๆ
function shirtTag(n){
  const s = (n === null || n === undefined) ? '' : String(n).trim();
  return s === '' ? '' : `#${s} `;
}

//teams.forEach(t=>$('#teamSelect').append(`<option value="${t.id}">${t.name}</option>`));

function refreshPlayerSelect(teamId){
  $('#playerSelect').prop('disabled',!teamId)
    .html(`<option value="">-- ${langSelectPlayer} --</option>`);
  players.filter(p=>p.team==teamId)
    // เรียงตามเบอร์เสื้อ คนที่ยังไม่มีเบอร์ไปอยู่ท้าย
    .sort((a,b)=>{
      const na=parseInt(a.number,10), nb=parseInt(b.number,10);
      if(isNaN(na)&&isNaN(nb)) return 0;
      if(isNaN(na)) return 1;
      if(isNaN(nb)) return -1;
      return na-nb;
    })
    .forEach(p=>{
      $('#playerSelect').append(
        `<option value="${p.id}" ${placed.has(p.id)?'disabled':''}>${shirtTag(p.number)}${p.name} (${p.sub==null?'N/A':p.sub})</option>`
      );
    });
}

$('#teamSelect').on('change',function(){ refreshPlayerSelect(this.value); });
$('#playerSelect').on('change',function(){
  const p=players.find(x=>x.id==this.value);
  if(p) $('#playerPos').val(p.pos);
});

/* ===== DRAG ===== */
function makeDrag(el){ el.draggable({revert:'invalid',stack:'.player'}); }
$('.zone').droppable({accept:'.player',drop:(e,ui)=>$(e.target).append(ui.draggable.css({top:0,left:0}))});

/* ===== INFO ===== */
function showInfo(p){
  const team=teams.find(t=>t.id==p.team);
  $('#playerInfo').html(`
    <div class="player-info-card">
      <img class="photo" src="${p.photo}" onerror="this.src='${NO_PROFILE}'" >
      <div>
        <div class="name">${p.name}</div>
        ${team?`<div class="team"><img src="${team.logo}"  onerror="this.src='${DEFAULT_LOGO}'" width="22"> ${team.name}</div>`:''}
        <div class="meta">${p.sub} • ${langNumber} ${p.number}</div>
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
      <span class="remove">✖</span>
    </div>
  `);
  el.on('click',()=>{$('.player').removeClass('active');el.addClass('active');showInfo(p);});
  el.find('.remove').on('click',e=>{
    e.stopPropagation();
    placed.delete(p.id);
    el.remove();
    refreshPlayerSelect($('#teamSelect').val());
  });
  makeDrag(el);
  return el;
}

/* ===== ADD (จำกัดจำนวนผู้เล่น) ===== */
$('#addPlayer').on('click',()=>{
  const max=+$('#maxPlayers').val();
  if(placed.size>=max){
    alert(langPlayersFull);
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
          `<div class="empty-info">${langClickPlayerOnField}</div>`
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


$('#saveTeam').on('click',()=>{
  mockLineup=[];

  $('.zone').each(function(){
    const zone = $(this).data('zone'); // ✅ FIX
    if(!zone) return;

    $(this).find('.player').each(function(){
      const name=$(this).find('strong').text();
      const p=players.find(x=>x.name===name);
      if(p){
        mockLineup.push({ id:p.id, zone });
      }
    });
  });

  console.log(mockLineup);

  const formData = new FormData();
  formData.append('num', $("#maxPlayers").val());
  formData.append('tour_id', tourId);
  formData.append('tournament_mvp_players', JSON.stringify(mockLineup));

  fetch('<?= base_url('admin/tournament/save-tournament-mvp') ?>', {
          method: 'POST',
          body: formData
      })
      .then(response => response.json())
      .then(data => {
      console.log(data);
          if (data.status === 'success') {
              Swal.fire({
                  icon: 'success',
                  title: langSaveSuccess,
                  text: data.msg,
                  timer: 1500,
                  showConfirmButton: false
              });
          } else {
              Swal.fire({
                  icon: 'error',
                  title: langSaveError,
                  text: data.msg
              });
          }
      })
      .catch(error => {
          console.error('Error:', error);
          Swal.fire({
              icon: 'error',
              title: langSaveError,
              text: langCannotSaveData
          });
      });


});

$('#loadMock').on('click',loadMock);

$(tourTeams);
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
        $('#teamSelect').empty().append(`<option value="">-- ${langSelectTeam} --</option>`);

        teams.forEach(t => {
            //console.log(t);
            $('#teamSelect').append(
                `<option value="${t.id}">
                    ${language === 'th' ? t.team_name : t.team_name_en}
                </option>`
            );
        });
        //console.log(teams);
    });
    //
}

/* ===== Tournament Ranking ===== */
var rankingTeams = [];
var rankingLoaded = false;

function setRankingTeamsFromMatches(matches) {
  const teamMap = {};

  (matches || []).forEach(match => {
    if (match.teams_json) {
      try {
        const stageTeams = JSON.parse(match.teams_json);
        if (Array.isArray(stageTeams)) {
          stageTeams.forEach(stageTeam => {
            const stageTeamId = parseInt(stageTeam.id, 10);
            const isAutoTeam = String(stageTeam.id || '').startsWith('auto_');

            if (stageTeamId > 0 && !isAutoTeam && !teamMap[stageTeamId]) {
              teamMap[stageTeamId] = {
                id: String(stageTeamId),
                name: stageTeam.name || '-',
                logo: stageTeam.logo || ''
              };
            }
          });
        }
      } catch (e) {
        console.warn('Invalid teams_json for ranking:', e);
      }
    }

    [
      {
        id: match.team1_id,
        name: language === 'th' ? match.team1_name : (match.team1_name_en || match.team1_name),
        logo: match.team1_logo
      },
      {
        id: match.team2_id,
        name: language === 'th' ? match.team2_name : (match.team2_name_en || match.team2_name),
        logo: match.team2_logo
      }
    ].forEach(team => {
      const teamId = parseInt(team.id, 10);
      if (teamId > 0 && !teamMap[teamId]) {
        teamMap[teamId] = {
          id: String(teamId),
          name: team.name || '-',
          logo: team.logo || ''
        };
      } else if (teamId > 0 && team.logo && !teamMap[teamId].logo) {
        teamMap[teamId].logo = team.logo;
      }
    });
  });

  rankingTeams = Object.values(teamMap).sort((a, b) => a.name.localeCompare(b.name));
}

function rankingTeamLogoUrl(logo) {
  if (!logo) return DEFAULT_LOGO;
  if (/^https?:\/\//i.test(logo)) return logo;
  return `<?= rtrim(base_url(), '/') ?>/${String(logo).replace(/^\/+/, '')}`;
}

function formatRankingTeamOption(option) {
  if (!option.id) return option.text;

  const logo = $(option.element).data('logo') || '';
  const src = rankingTeamLogoUrl(logo);
  return $(`
    <span class="ranking-team-option">
      <img src="${src}" onerror="this.src='${DEFAULT_LOGO}'" alt="">
      <span>${option.text}</span>
    </span>
  `);
}

function initRankingSelect2() {
  if (typeof $.fn.select2 !== 'function') return;

  $('.ranking-team-select').each(function() {
    const $select = $(this);
    if ($select.data('select2')) {
      $select.select2('destroy');
    }

    $select.select2({
      width: '100%',
      dropdownParent: $('#finalRanking'),
      templateResult: formatRankingTeamOption,
      templateSelection: formatRankingTeamOption
    });
  });
}

function loadRankingTeams(callback) {
  if (rankingTeams.length) {
    if (typeof callback === 'function') callback();
    return;
  }

  if (Array.isArray(bracketData) && bracketData.length) {
    setRankingTeamsFromMatches(bracketData);
    if (typeof callback === 'function') callback();
    return;
  }

  $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-match-for-prog-rank-result') ?>', { tour_id: tourId }, res => {
    setRankingTeamsFromMatches(res?.data || []);
    if (typeof callback === 'function') callback();
  });
}

function selectedRankingTeams() {
  const selected = {};
  $('.ranking-team-select').each(function() {
    const val = $(this).val();
    if (val) selected[val] = String($(this).data('rank'));
  });
  return selected;
}

function updateRankingTeamOptions() {
  const selected = selectedRankingTeams();

  $('.ranking-team-select').each(function() {
    const $select = $(this);
    const currentVal = $select.val() || '';
    const currentRank = String($select.data('rank'));
    let html = `<option value="">-- ${langSelectTeam} --</option>`;

    rankingTeams.forEach(team => {
      const disabled = selected[team.id] && selected[team.id] !== currentRank ? 'disabled' : '';
      const selectedAttr = team.id === currentVal ? 'selected' : '';
      html += `<option value="${team.id}" data-logo="${team.logo || ''}" ${selectedAttr} ${disabled}>${team.name}</option>`;
    });

    $select.html(html);
  });

  initRankingSelect2();
}

function generateRankingRows(num) {
  const count = Math.max(1, Math.min(32, parseInt(num, 10) || 4));
  const currentValues = {};

  $('#rankingRowsContainer .ranking-team-select').each(function() {
    currentValues[$(this).data('rank')] = $(this).val();
  });

  let html = '';
  for (let i = 1; i <= count; i++) {
    html += `
      <div class="final-ranking-row">
        <div class="final-ranking-no">Rank ${i}</div>
        <div class="final-ranking-team flex-grow-1">
          <select class="form-select ranking-team-select" data-rank="${i}">
            <option value="">-- ${langSelectTeam} --</option>
          </select>
        </div>
      </div>
    `;
  }

  $('#numRanking').val(count);
  $('#rankingRowsContainer').html(html);
  updateRankingTeamOptions();

  $('.ranking-team-select').each(function() {
    const oldValue = currentValues[$(this).data('rank')] || '';
    if (oldValue) $(this).val(oldValue);
  });

  updateRankingTeamOptions();
}

function loadTournamentRanking() {
  loadRankingTeams(() => {
    $.getJSON('<?= base_url('admin/tournament/get-tournament-ranking') ?>', { tour_id: tourId }, res => {
      if (res?.status !== 'success') {
        Swal.fire({ icon: 'error', title: langSaveError, text: res?.msg || langCannotSaveData });
        return;
      }

      const data = Array.isArray(res.data) ? res.data : [];
      const maxSavedRank = data.length ? Math.max(...data.map(r => parseInt(r.rank_no, 10) || 0)) : 0;
      const configuredRows = parseInt(res.number_ranking || $('#numRanking').val(), 10) || 4;
      generateRankingRows(Math.max(configuredRows, maxSavedRank, 1));

      data.forEach(r => {
        $(`.ranking-team-select[data-rank="${parseInt(r.rank_no, 10)}"]`).val(String(r.team_id));
      });

      updateRankingTeamOptions();
      rankingLoaded = true;
    });
  });
}

$('#updateRankingRows').on('click', function() {
  generateRankingRows($('#numRanking').val());
});

$('#numRanking').on('change', function() {
  generateRankingRows($(this).val());
});

$(document).on('change', '.ranking-team-select', updateRankingTeamOptions);

$('button[data-bs-target="#finalRanking"]').on('shown.bs.tab', function() {
  if (!rankingLoaded) {
    loadTournamentRanking();
  }
});

$('#saveRanking').on('click', function() {
  const rankings = [];
  const selectedTeams = new Set();
  let hasDuplicate = false;

  $('.ranking-team-select').each(function() {
    const teamId = $(this).val();
    const rankNo = parseInt($(this).data('rank'), 10);

    if (!teamId) return;
    if (selectedTeams.has(teamId)) hasDuplicate = true;
    selectedTeams.add(teamId);
    rankings.push({ team_id: teamId, rank_no: rankNo });
  });

  if (hasDuplicate) {
    Swal.fire({
      icon: 'error',
      title: 'ข้อมูลซ้ำ',
      text: 'ไม่สามารถระบุทีมเดียวกันในหลายลำดับได้'
    });
    return;
  }

  const formData = new FormData();
  formData.append('tour_id', tourId);
  formData.append('number_ranking', $('#numRanking').val());
  formData.append('rankings', JSON.stringify(rankings));

  fetch('<?= base_url('admin/tournament/save-tournament-ranking') ?>', {
    method: 'POST',
    body: formData
  })
    .then(response => response.json())
    .then(data => {
      if (data.status === 'success') {
        rankingLoaded = false;
        Swal.fire({
          icon: 'success',
          title: langSaveSuccess,
          text: data.msg,
          timer: 1500,
          showConfirmButton: false
        });
      } else {
        Swal.fire({ icon: 'error', title: langSaveError, text: data.msg });
      }
    })
    .catch(error => {
      console.error('Error:', error);
      Swal.fire({ icon: 'error', title: langSaveError, text: langCannotSaveData });
    });
});
/* ===== END Tournament Ranking ===== */
/* ===== END LineUP MVP ===== */

</script>
