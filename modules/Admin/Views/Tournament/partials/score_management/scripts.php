<script>
    const langSaveSuccess = '<?= lang("Common.save_success") ?>';
    const langError = '<?= lang("Common.error_occurred") ?>';
    const langCannotSave = '<?= lang("Common.cannot_save") ?>';
    const langPleaseEnterScore = '<?= lang("Common.please_enter_score") ?>';
    const langPleaseEnterBothScores = '<?= lang("Common.please_enter_both_scores") ?>';
    const langScoreTied = '<?= lang("Common.score_tied") ?>';
    const langEndAsDraw = '<?= lang("Common.end_as_draw") ?>';
    const langEndDrawOrPenalty = '<?= lang("Common.end_draw_or_penalty") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';
    const langPleaseSelectWinner = '<?= lang("Common.please_select_winner") ?>';
    const langPenaltyTiedSelectWinner = '<?= lang("Common.penalty_tied_select_winner") ?>';
    const langConfirmEndMatch = '<?= lang("Common.confirm_end_match") ?>';
    const langCannotEditAfterEnd = '<?= lang("Common.cannot_edit_after_end") ?>';
    const langConfirm = '<?= lang("Common.confirm") ?>';
    const langEndMatchSuccess = '<?= lang("Common.end_match_success") ?>';
    const langOverLimit = '<?= lang("Common.over_limit") ?>';
    const langMax11Starters = '<?= lang("Common.max_11_starters") ?>';
    const langLoading = '<?= lang("Common.loading") ?>';
    const langNoPlayerFound = '<?= lang("Common.no_player_found") ?>';
    const langErrorLoadingData = '<?= lang("Common.error_loading_data") ?>';
    const langSubPlayerOut = '<?= lang("Common.sub_player_out") ?>';
    const langSubPlayerIn = '<?= lang("Common.sub_player_in") ?>';
    const langSubRowIncomplete = '<?= lang("Common.sub_row_incomplete") ?>';
    const langSubNoRow = '<?= lang("Common.sub_no_row") ?>';
    const langSubSavedCount = '<?= lang("Common.sub_saved_count") ?>';
    const langSubNoPlayers = '<?= lang("Common.sub_no_players") ?>';
    const langSubNoSubstitutes = '<?= lang("Common.sub_no_substitutes") ?>';
    const langStarterGroup = '<?= lang("Common.starter") ?>';
    const langSubstituteGroup = '<?= lang("Common.substitute") ?>';
    const langNotInLineup = '<?= lang("Common.player_not_in_lineup") ?>';
    const langSubPickHint = '<?= lang("Common.sub_pick_hint") ?>';
    const langSubPickedIn = '<?= lang("Common.sub_picked_in") ?>';
    const langSubPickedOut = '<?= lang("Common.sub_picked_out") ?>';
    const langMinute = '<?= lang("Common.minute") ?>';
    const langSubLineupNotSet = '<?= lang("Common.sub_lineup_not_set") ?>';
    const langSubSelectTeamFirst = '<?= lang("Common.sub_select_team_first") ?>';
    const langSubLoadFailed = '<?= lang("Common.sub_load_failed") ?>';
    const langNoLineupSelected = '<?= lang("Common.no_lineup_selected") ?>';
    const langNoEvents = '<?= lang("Common.no_events") ?>';
    const langNotSpecified = '<?= lang("Common.not_specified") ?>';
    const langStarter = '<?= lang("Common.starter") ?>';
    const langSubstitute = '<?= lang("Common.substitute") ?>';
    const langNumber = '<?= lang("Common.number_abbr") ?>';
    const langFullName = '<?= lang("Common.full_name") ?>';
    const langPosition = '<?= lang("Common.position") ?>';
    const langSubstituteColon = '<?= lang("Common.substitute") ?>:';
    const langAssist = '<?= lang("Common.assist") ?>';
    const langAddEventSuccess = '<?= lang("Common.add_event_success") ?>';
    const langConfirmDeleteEvent = '<?= lang("Common.confirm_delete_event") ?>';
    const langDeleteBtn = '<?= lang("Common.delete") ?>';
    const langDeleteEventSuccess = '<?= lang("Common.delete_event_success") ?>';
    const langConfirmDelete = '<?= lang("Common.confirm_delete") ?>';
    const langDeleteConfirm = '<?= lang("Common.confirm_delete_event_question") ?>';
    const langPleaseEnterInfo = '<?= lang("Common.please_enter_info") ?>';
    const langSelectEventAndPlayer = '<?= lang("Common.select_event_and_player") ?>';
    const langSubstituteIn = '<?= lang("Common.substitute_player_in") ?>';
    const langAssistOptional = '<?= lang("Common.assist_optional") ?>';
    const langSelectTeam = '<?= lang("Common.select_team") ?>';
    const langSelectPlayer = '<?= lang("Common.select_player") ?>';
    const langNoLineup = '<?= lang("Common.no_lineup_yet") ?>';
    const langErrorOccurred = '<?= lang("Common.error_occurred") ?>';
    const langTeam = '<?= lang("Common.team") ?>';

    const tourId = '<?= $tour_id ?>';
    let currentLineupData = {};
    let currentEventTypes = [];

    // ==========================================
    // Score Management Functions (Existing)
    // ==========================================

    function checkTieStatus(matchId) {
        const card = document.querySelector(`[data-match-id="${matchId}"]`);
        const team1Score = card.querySelector('.team1-score').value;
        const team2Score = card.querySelector('.team2-score').value;
        const penaltySection = document.getElementById(`penalty-${matchId}`);
        const winnerSection = document.getElementById(`winner-select-${matchId}`);

        if (team1Score !== '' && team2Score !== '' && team1Score === team2Score) {
            penaltySection.classList.add('show');
        } else {
            penaltySection.classList.remove('show');
            if (winnerSection) winnerSection.classList.remove('show');
        }
    }

    function checkPenaltyTieStatus(matchId) {
        const card = document.querySelector(`[data-match-id="${matchId}"]`);
        const team1Penalty = card.querySelector('.team1-penalty').value;
        const team2Penalty = card.querySelector('.team2-penalty').value;
        const winnerSection = document.getElementById(`winner-select-${matchId}`);

        if (team1Penalty !== '' && team2Penalty !== '' && team1Penalty === team2Penalty) {
            winnerSection.classList.add('show');
        } else {
            winnerSection.classList.remove('show');
        }
    }

    function saveMatchScore(matchId) {
        const card = document.querySelector(`[data-match-id="${matchId}"]`);
        const team1Score = card.querySelector('.team1-score').value;
        const team2Score = card.querySelector('.team2-score').value;
        const team1Penalty = card.querySelector('.team1-penalty')?.value || '';
        const team2Penalty = card.querySelector('.team2-penalty')?.value || '';
        const winnerId = card.querySelector('.winner-select')?.value || '';

        const formData = new FormData();
        formData.append('match_id', matchId);
        formData.append('team1_score', team1Score);
        formData.append('team2_score', team2Score);
        formData.append('team1_penalty', team1Penalty);
        formData.append('team2_penalty', team2Penalty);
        formData.append('winner_team_id', winnerId);

        fetch('<?= base_url('admin/tournament/save-match-score') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: langSaveSuccess,
                        text: data.msg,
                        timer: 1500,
                        showConfirmButton: false
                    });
                } else {
                    showEventModalAlert(data.msg || langError);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: langError,
                    text: langCannotSave
                });
            });
    }

    function endMatchWithScore(matchId) {
        const card = document.querySelector(`[data-match-id="${matchId}"]`);
        const team1Score = card.querySelector('.team1-score').value;
        const team2Score = card.querySelector('.team2-score').value;
        const team1Penalty = card.querySelector('.team1-penalty')?.value || '';
        const team2Penalty = card.querySelector('.team2-penalty')?.value || '';
        const winnerId = card.querySelector('.winner-select')?.value || '';

        if (team1Score === '' || team2Score === '') {
            Swal.fire({
                icon: 'warning',
                title: langPleaseEnterScore,
                text: langPleaseEnterBothScores
            });
            return;
        }

        if (team1Score === team2Score) {
            if (team1Penalty === '' || team2Penalty === '') {
                Swal.fire({
                    icon: 'question',
                    title: langScoreTied,
                    text: langEndDrawOrPenalty,
                    showCancelButton: true,
                    confirmButtonText: langEndAsDraw,
                    cancelButtonText: langCancel
                }).then((result) => {
                    if (result.isConfirmed) {
                        submitEndMatch(matchId, team1Score, team2Score, team1Penalty, team2Penalty, winnerId);
                    }
                });
                return;
            }

            if (team1Penalty === team2Penalty && winnerId === '') {
                Swal.fire({
                    icon: 'warning',
                    title: langPleaseSelectWinner,
                    text: langPenaltyTiedSelectWinner
                });
                return;
            }
        }

        Swal.fire({
            icon: 'question',
            title: langConfirmEndMatch,
            text: langCannotEditAfterEnd,
            showCancelButton: true,
            confirmButtonText: langConfirm,
            cancelButtonText: langCancel,
            confirmButtonColor: '#28a745'
        }).then((result) => {
            if (result.isConfirmed) {
                submitEndMatch(matchId, team1Score, team2Score, team1Penalty, team2Penalty, winnerId);
            }
        });
    }

    function submitEndMatch(matchId, team1Score, team2Score, team1Penalty, team2Penalty, winnerId) {
        const formData = new FormData();
        formData.append('match_id', matchId);
        formData.append('team1_score', team1Score);
        formData.append('team2_score', team2Score);
        formData.append('team1_penalty', team1Penalty);
        formData.append('team2_penalty', team2Penalty);
        formData.append('winner_team_id', winnerId);

        fetch('<?= base_url('admin/tournament/end-match-with-score') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: langEndMatchSuccess,
                        text: data.msg,
                        timer: 1500,
                        showConfirmButton: false
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: langError,
                        text: data.msg
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: langError,
                    text: langCannotSave
                });
            });
    }

    function refreshMatches() {
        // รีเฟรชโดยคงค่า filter ไว้
        const dateFilter = document.getElementById('dateFilter');
        const filterDate = dateFilter ? dateFilter.value : '';
        const baseUrl = '<?= base_url('admin/tournament/score-management/' . $tour_id) ?>';
        window.location.href = filterDate ? `${baseUrl}?date=${filterDate}` : baseUrl;
    }

    function filterByDate(date) {
        const baseUrl = '<?= base_url('admin/tournament/score-management/' . $tour_id) ?>';
        window.location.href = date ? `${baseUrl}?date=${date}` : baseUrl;
    }

    // ==========================================
    // Edit Mode Functions for Ended Matches
    // ==========================================

    function enableEditMode(matchId) {
        const card = document.querySelector(`.match-card[data-match-id="${matchId}"]`);
        if (!card) return;

        // เก็บค่าเดิมไว้เผื่อยกเลิก
        const team1Score = card.querySelector('.team1-score');
        const team2Score = card.querySelector('.team2-score');
        const team1Penalty = card.querySelector('.team1-penalty');
        const team2Penalty = card.querySelector('.team2-penalty');

        card.dataset.originalTeam1Score = team1Score ? team1Score.value : '';
        card.dataset.originalTeam2Score = team2Score ? team2Score.value : '';
        card.dataset.originalTeam1Penalty = team1Penalty ? team1Penalty.value : '';
        card.dataset.originalTeam2Penalty = team2Penalty ? team2Penalty.value : '';

        // เปิดให้แก้ไข score inputs
        if (team1Score) {
            team1Score.removeAttribute('readonly');
            team1Score.classList.remove('score-readonly');
        }
        if (team2Score) {
            team2Score.removeAttribute('readonly');
            team2Score.classList.remove('score-readonly');
        }
        if (team1Penalty) {
            team1Penalty.removeAttribute('readonly');
            team1Penalty.classList.remove('score-readonly');
        }
        if (team2Penalty) {
            team2Penalty.removeAttribute('readonly');
            team2Penalty.classList.remove('score-readonly');
        }

        // แสดงปุ่มแก้ไข lineup และ events
        card.querySelectorAll('.btn-open-lineup-single, .btn-add-event-single').forEach(btn => {
            btn.classList.remove('d-none');
            btn.style.display = '';
        });

        // แสดง penalty section ถ้าคะแนนเสมอ
        checkTieStatus(matchId);

        // สลับแสดง action buttons
        document.getElementById(`action-view-${matchId}`).classList.add('d-none');
        document.getElementById(`action-edit-${matchId}`).classList.remove('d-none');

        // เพิ่ม class แสดงว่าอยู่ในโหมดแก้ไข
        card.classList.add('editing-mode');
        card.classList.remove('match-ended');
    }

    function cancelEditMode(matchId) {
        const card = document.querySelector(`.match-card[data-match-id="${matchId}"]`);
        if (!card) return;

        // คืนค่าเดิม
        const team1Score = card.querySelector('.team1-score');
        const team2Score = card.querySelector('.team2-score');
        const team1Penalty = card.querySelector('.team1-penalty');
        const team2Penalty = card.querySelector('.team2-penalty');

        if (team1Score) {
            team1Score.value = card.dataset.originalTeam1Score || '';
            team1Score.setAttribute('readonly', 'readonly');
            team1Score.classList.add('score-readonly');
        }
        if (team2Score) {
            team2Score.value = card.dataset.originalTeam2Score || '';
            team2Score.setAttribute('readonly', 'readonly');
            team2Score.classList.add('score-readonly');
        }
        if (team1Penalty) {
            team1Penalty.value = card.dataset.originalTeam1Penalty || '';
            team1Penalty.setAttribute('readonly', 'readonly');
            team1Penalty.classList.add('score-readonly');
        }
        if (team2Penalty) {
            team2Penalty.value = card.dataset.originalTeam2Penalty || '';
            team2Penalty.setAttribute('readonly', 'readonly');
            team2Penalty.classList.add('score-readonly');
        }

        // ซ่อนปุ่มแก้ไข lineup และ events
        card.querySelectorAll('.btn-open-lineup-single, .btn-add-event-single').forEach(btn => {
            btn.classList.add('d-none');
        });

        // สลับแสดง action buttons
        document.getElementById(`action-view-${matchId}`).classList.remove('d-none');
        document.getElementById(`action-edit-${matchId}`).classList.add('d-none');

        // ลบ class โหมดแก้ไข
        card.classList.remove('editing-mode');
        card.classList.add('match-ended');
    }

    // ==========================================
    // Lineup Management Functions
    // ==========================================

    // Store current button data for modal
    let currentLineupButton = null;
    let currentEventButton = null;

    async function loadLineupModal(matchId, team1Id, team2Id) {
        // Show loading
        document.getElementById('lineup-team1-players').innerHTML = '<tr><td colspan="5" class="text-center text-muted">${langLoading}</td></tr>';
        document.getElementById('lineup-team2-players').innerHTML = '<tr><td colspan="5" class="text-center text-muted">${langLoading}</td></tr>';

        try {
            // Fetch existing lineup
            const lineupResponse = await fetch(`<?= base_url('admin/tournament/get-match-lineup') ?>/${matchId}`);
            const lineupData = await lineupResponse.json();

            // Fetch available players for both teams
            const team1PlayersResponse = await fetch(`<?= base_url('admin/tournament/get-team-players') ?>/${tourId}/${team1Id}`);
            const team1PlayersData = await team1PlayersResponse.json();

            const team2PlayersResponse = await fetch(`<?= base_url('admin/tournament/get-team-players') ?>/${tourId}/${team2Id}`);
            const team2PlayersData = await team2PlayersResponse.json();

            // Store current lineup data
            currentLineupData = lineupData.data || {};

            // Render player tables
            renderPlayerTable('lineup-team1-players', team1PlayersData.data || [], currentLineupData.team1);
            renderPlayerTable('lineup-team2-players', team2PlayersData.data || [], currentLineupData.team2);

            // Update counts after rendering
            updateLineupCounts();

        } catch (error) {
            console.error('Error loading lineup:', error);
            document.getElementById('lineup-team1-players').innerHTML = '<tr><td colspan="5" class="text-center text-danger">${langErrorLoadingData}</td></tr>';
            document.getElementById('lineup-team2-players').innerHTML = '<tr><td colspan="5" class="text-center text-danger">${langErrorLoadingData}</td></tr>';
        }
    }

    function renderPlayerTable(tableBodyId, players, teamLineup) {
        const tbody = document.getElementById(tableBodyId);

        if (players.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">${langNoPlayerFound}</td></tr>';
            return;
        }

        const starterIds = (teamLineup?.starters || []).map(p => p.player_id);
        const subIds = (teamLineup?.substitutes || []).map(p => p.player_id);

        let html = '';
        players.forEach(player => {
            const isStarter = starterIds.includes(String(player.id));
            const isSub = subIds.includes(String(player.id));
            const rowClass = isStarter ? 'table-success' : (isSub ? 'table-warning' : '');

            html += `
            <tr class="${rowClass}" data-player-id="${player.id}">
                <td class="text-center">
                    <input type="checkbox" class="form-check-input starter-checkbox"
                           data-player-id="${player.id}"
                           ${isStarter ? 'checked' : ''}
                           onchange="handleLineupCheckbox(this, 'starter')">
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input sub-checkbox"
                           data-player-id="${player.id}"
                           ${isSub ? 'checked' : ''}
                           onchange="handleLineupCheckbox(this, 'sub')">
                </td>
                <td class="text-center fw-bold">${player.shirt_number || '-'}</td>
                <td>${player.first_name} ${player.last_name}</td>
                <td><small class="text-muted">${player.position_name || '-'}</small></td>
            </tr>
        `;
        });

        tbody.innerHTML = html;
    }

    function handleLineupCheckbox(checkbox, type) {
        const row = checkbox.closest('tr');
        const starterCheckbox = row.querySelector('.starter-checkbox');
        const subCheckbox = row.querySelector('.sub-checkbox');
        const tbody = row.closest('tbody');

        // Check max 11 starters limit
        if (type === 'starter' && checkbox.checked) {
            const currentStarters = tbody.querySelectorAll('.starter-checkbox:checked').length;
            if (currentStarters > 11) {
                checkbox.checked = false;
                Swal.fire({
                    icon: 'warning',
                    title: langOverLimit,
                    text: langMax11Starters,
                    confirmButtonColor: '#06346D'
                });
                return;
            }
            subCheckbox.checked = false;
        } else if (type === 'sub' && checkbox.checked) {
            starterCheckbox.checked = false;
        }

        // Update row styling
        row.classList.remove('table-success', 'table-warning');
        if (starterCheckbox.checked) {
            row.classList.add('table-success');
        } else if (subCheckbox.checked) {
            row.classList.add('table-warning');
        }

        // Update counts
        updateLineupCounts();
    }

    function updateLineupCounts() {
        // Team 1 counts
        const team1Starters = $('#lineup-team1-players .starter-checkbox:checked').length;
        const team1Subs = $('#lineup-team1-players .sub-checkbox:checked').length;
        $('#team1-starter-count').text(team1Starters);
        $('#team1-sub-count').text(team1Subs);

        // Team 2 counts
        const team2Starters = $('#lineup-team2-players .starter-checkbox:checked').length;
        const team2Subs = $('#lineup-team2-players .sub-checkbox:checked').length;
        $('#team2-starter-count').text(team2Starters);
        $('#team2-sub-count').text(team2Subs);
    }

    function saveTeamLineup(teamNum) {
        const matchId = document.getElementById('lineup-match-id').value;
        const teamId = document.getElementById(`lineup-team${teamNum}-id`).value;
        const tbody = document.getElementById(`lineup-team${teamNum}-players`);

        const starters = [];
        const substitutes = [];

        tbody.querySelectorAll('.starter-checkbox:checked').forEach(cb => {
            starters.push(cb.dataset.playerId);
        });

        tbody.querySelectorAll('.sub-checkbox:checked').forEach(cb => {
            substitutes.push(cb.dataset.playerId);
        });

        const formData = new FormData();
        formData.append('match_id', matchId);
        formData.append('team_id', teamId);
        formData.append('starters', JSON.stringify(starters));
        formData.append('substitutes', JSON.stringify(substitutes));

        fetch('<?= base_url('admin/tournament/save-match-lineup') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: langSaveSuccess,
                        text: data.msg,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // Reload lineup display
                    loadLineupDisplay(matchId);
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: langError,
                        text: data.msg
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: langError,
                    text: langCannotSave
                });
            });
    }

    function loadLineupDisplay(matchId) {
        const contentDiv = document.getElementById(`lineup-content-${matchId}`);
        contentDiv.innerHTML = '<div class="text-center text-muted small py-2"><i class="ti ti-loader ti-spin me-1"></i><?= lang('Common.loading') ?></div>';

        fetch(`<?= base_url('admin/tournament/get-match-lineup') ?>/${matchId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderLineupDisplay(matchId, data.data);
                } else {
                    contentDiv.innerHTML = '<div class="text-center text-muted small py-2">${langNoLineupSelected}</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                contentDiv.innerHTML = '<div class="text-center text-danger small py-2">${langErrorOccurred}</div>';
            });
    }

    function renderLineupDisplay(matchId, data) {
        const contentDiv = document.getElementById(`lineup-content-${matchId}`);

        const team1 = data.team1 || {};
        const team2 = data.team2 || {};
        const team1Starters = team1.starters || [];
        const team1Subs = team1.substitutes || [];
        const team2Starters = team2.starters || [];
        const team2Subs = team2.substitutes || [];

        if (team1Starters.length === 0 && team1Subs.length === 0 && team2Starters.length === 0 && team2Subs.length === 0) {
            contentDiv.innerHTML = '<div class="text-center text-muted small py-2">${langNoLineupSelected}</div>';
            return;
        }

        let html = '<div class="row">';

        // Team 1
        html += '<div class="col-md-6">';
        html += `<div class="lineup-team-name">${team1.team_name || langTeam + ' 1'} (${team1Starters.length})</div>`;
        html += '<div class="lineup-players">';
        team1Starters.forEach(p => {
            html += `<span class="player-badge starter"><span class="player-number">#${p.shirt_number || '-'}</span>${p.first_name}</span>`;
        });
        html += '</div>';
        if (team1Subs.length > 0) {
            html += '<div class="mt-2"><small class="text-muted">${langSubstituteColon}</small></div>';
            html += '<div class="lineup-players">';
            team1Subs.forEach(p => {
                html += `<span class="player-badge substitute"><span class="player-number">#${p.shirt_number || '-'}</span>${p.first_name}</span>`;
            });
            html += '</div>';
        }
        html += '</div>';

        // Team 2
        html += '<div class="col-md-6">';
        html += `<div class="lineup-team-name">${team2.team_name || langTeam + ' 2'} (${team2Starters.length})</div>`;
        html += '<div class="lineup-players">';
        team2Starters.forEach(p => {
            html += `<span class="player-badge starter"><span class="player-number">#${p.shirt_number || '-'}</span>${p.first_name}</span>`;
        });
        html += '</div>';
        if (team2Subs.length > 0) {
            html += '<div class="mt-2"><small class="text-muted">${langSubstituteColon}</small></div>';
            html += '<div class="lineup-players">';
            team2Subs.forEach(p => {
                html += `<span class="player-badge substitute"><span class="player-number">#${p.shirt_number || '-'}</span>${p.first_name}</span>`;
            });
            html += '</div>';
        }
        html += '</div>';

        html += '</div>';
        contentDiv.innerHTML = html;
    }

    // ==========================================
    // Events Management Functions
    // ==========================================

    // เก็บข้อมูลผู้เล่นสำหรับใช้ใน event modal
    let currentEventPlayers = {
        starters: [],
        substitutes: [],
        all: []
    };

    function loadEventPlayers() {
        const matchId = document.getElementById('event-match-id').value;
        const teamId = document.getElementById('event-team-select').value;

        if (!teamId) {
            document.getElementById('event-player1-select').innerHTML = '<option value="">${langSelectPlayer}</option>';
            document.getElementById('event-player2-select').innerHTML = '<option value="">${langSelectPlayer}</option>';
            currentEventPlayers = { starters: [], substitutes: [], all: [], unassigned: [] };
            return;
        }

        // First try to get players from lineup
        // ยิงพร้อมกัน 2 เส้น ไม่ต่อคิวกัน เพราะ request บนเครื่อง dev ช้าอยู่แล้ว
        //   lineup = คนที่ถูกจัดตัวไว้จริง (มี is_substitute)
        //   squad  = รายชื่อทั้งทีมในทัวร์นี้ ใช้หาคนที่ "ยังไม่ได้เลือก"
        Promise.all([
            fetch(`<?= base_url('admin/tournament/get-match-players') ?>/${matchId}/${teamId}`)
                .then(r => r.json()).then(d => d.data || []).catch(() => []),
            fetch(`<?= base_url('admin/tournament/get-team-players') ?>/${tourId}/${teamId}`)
                .then(r => r.json()).then(d => d.data || []).catch(() => [])
        ])
            .then(([lineup, squad]) => {
                // แยกผู้เล่นตัวจริงและตัวสำรอง
                currentEventPlayers.starters = lineup.filter(p => p.is_substitute != 1);
                currentEventPlayers.substitutes = lineup.filter(p => p.is_substitute == 1);

                // คนที่อยู่ในทีมแต่ยังไม่ถูกจัดตัวลงแมตช์นี้
                const inLineup = new Set(lineup.map(p => String(p.id)));
                currentEventPlayers.unassigned = squad.filter(p => !inLineup.has(String(p.id)));

                // all = ทุกคนที่เลือกได้ในเหตุการณ์ทั่วไป (ไม่ใช่เปลี่ยนตัว)
                const players = lineup.concat(currentEventPlayers.unassigned);
                currentEventPlayers.all = players;

                // options ของ player1 — รวมตัวสำรองด้วย แยกกลุ่มให้เห็นว่าใครเป็นใคร
                document.getElementById('event-player1-select').innerHTML =
                    playerOptionsGrouped(langSelectPlayer);

                // อัปเดต player2 dropdown ตามประเภทเหตุการณ์ที่เลือก
                updatePlayer2Options();
                syncSubRowsAfterLoad();
                if (!lineup.length) showEventModalAlert(langSubLineupNotSet);
            })
            .catch(error => {
                console.error('Error loading players:', error);
            });
    }

    // ตัวสำรองที่ถูกเปลี่ยนลงสนามแล้วก็ทำประตู/โดนใบเหลืองได้ จึงต้องมีให้เลือกด้วย
    // แต่ต้องแยกกลุ่มให้เห็นชัดว่าใครเป็นตัวจริง ใครเป็นตัวสำรอง
    function playerOptionsGrouped(placeholder) {
        let html = `<option value="">-- ${placeholder} --</option>`;

        const group = (label, list) => {
            if (!list || !list.length) return '';
            let g = `<optgroup label="${label} (${list.length})">`;
            list.forEach(p => {
                g += `<option value="${p.id}">#${p.shirt_number || '-'} ${p.first_name} ${p.last_name}</option>`;
            });
            return g + '</optgroup>';
        };

        html += group(langStarterGroup, currentEventPlayers.starters);
        html += group(langSubstituteGroup, currentEventPlayers.substitutes);
        // คนที่ยังไม่ได้ถูกจัดตัว ยังเลือกได้ แต่ต้องบอกให้ชัดว่าคนละสถานะกัน
        html += group(langNotInLineup, currentEventPlayers.unassigned);
        return html;
    }

    function updatePlayer2Options() {
        const eventType = document.getElementById('event-type-select').value;
        let options = '<option value="">${langSelectPlayer}</option>';

        if (eventType === '2') {
            // เปลี่ยนตัว - แสดงเฉพาะตัวสำรอง
            currentEventPlayers.substitutes.forEach(p => {
                options += `<option value="${p.id}">#${p.shirt_number || '-'} ${p.first_name} ${p.last_name}</option>`;
            });
        } else {
            // กรณีอื่น (เช่น แอสซิสต์) - แสดงตัวจริงทั้งหมด
            currentEventPlayers.starters.forEach(p => {
                options += `<option value="${p.id}">#${p.shirt_number || '-'} ${p.first_name} ${p.last_name}</option>`;
            });
        }

        document.getElementById('event-player2-select').innerHTML = options;
    }

    function togglePlayer2Field() {
        const eventType = document.getElementById('event-type-select').value;
        const player2Container = document.getElementById('event-player2-container');
        const player2Label = document.getElementById('event-player2-label');

        // เปลี่ยนตัว = ทำได้ทีละหลายคู่ จึงสลับไปใช้ตารางแทนช่องเลือกเดี่ยว
        try {
            clearEventModalAlert();
            setSubMultiMode(eventType === '2');
        } catch (e) {
            console.error('setSubMultiMode error:', e);
        }

        if (eventType === '2') {
            player2Container.style.display = 'none';
            return;
        }

        if (eventType === '1') { // Goal - show assist option
            player2Container.style.display = 'block';
            player2Label.textContent = langAssistOptional;
            // อัปเดต dropdown ให้แสดงตัวจริง
            updatePlayer2Options();
        } else {
            player2Container.style.display = 'none';
        }
    }

    // รีเซ็ตเฉพาะส่วนที่เพิ่มเข้ามาใหม่ ห่อ try/catch ไว้เพราะถูกเรียกจาก handler เปิด modal
    // ถ้าตรงนี้โยน error ขึ้นมา handler จะหยุดกลางคัน แล้ว dropdown ผู้เล่นจะว่างทุกประเภทเหตุการณ์
    function resetEventModalExtras() {
        try {
            setSubMultiMode(false);
            clearEventModalAlert();
        } catch (e) {
            console.error('resetEventModalExtras error:', e);
        }
    }

    // แจ้งเตือนในตัว modal แทนการเด้ง Swal ทับ — ไม่ให้มี popup ซ้อน popup
    function showEventModalAlert(msg) {
        const box = document.getElementById('event-modal-alert');
        if (!box) return;
        const txt = document.getElementById('event-modal-alert-text');
        if (txt) txt.textContent = msg;
        box.classList.remove('d-none');
    }

    function clearEventModalAlert() {
        const box = document.getElementById('event-modal-alert');
        if (box) box.classList.add('d-none');
    }

    // ===== โหมดเปลี่ยนตัว — สองฝั่งจิ้มจับคู่ =====
    // ฝั่งซ้าย = ตัวสำรอง / ฝั่งขวา = ตัวจริงในสนาม
    // คลิกฝั่งไหนก่อนก็ได้ พอครบทั้งคู่จะถูกเพิ่มลงรายการด้านล่างอัตโนมัติ
    let subPairs   = [];    // [{ inId, outId, minute }]
    let pendingIn  = null;  // ตัวสำรองที่เพิ่งคลิก
    let pendingOut = null;  // ตัวจริงที่เพิ่งคลิก

    // ยึดตามที่จัดตัวไว้จริงเท่านั้น ไม่เดา ไม่ดึงคนอื่นในทีมมาเติม
    function subOutList() { return currentEventPlayers.starters; }
    function subInList()  { return currentEventPlayers.substitutes; }

    function playerLabel(p) {
        return `#${p.shirt_number || '-'} ${p.first_name} ${p.last_name}`;
    }

    function findPlayer(id) {
        return currentEventPlayers.all.find(p => String(p.id) === String(id)) || null;
    }

    function playerName(id) {
        const p = findPlayer(id);
        return p ? playerLabel(p) : String(id);
    }

    // คนที่ถูกจับคู่ไปแล้ว ห้ามจับซ้ำ
    function pairedIds() {
        const used = new Set();
        subPairs.forEach(x => { used.add(String(x.inId)); used.add(String(x.outId)); });
        return used;
    }

    function subCardHtml(p, side, used, picked) {
        const cls = 'sub-card' + (used ? ' is-used' : '') + (picked ? ' is-picked' : '');
        return `<button type="button" class="${cls}" data-sub-side="${side}" data-sub-id="${p.id}"${used ? ' disabled' : ''}>
                    <span class="sub-card__no">${p.shirt_number || '-'}</span>
                    <span class="sub-card__name">${p.first_name} ${p.last_name}</span>
                </button>`;
    }

    function renderSubPanes() {
        const bench = document.getElementById('sub-bench-list');
        const pitch = document.getElementById('sub-pitch-list');
        if (!bench || !pitch) return;

        const used = pairedIds();
        const inList  = subInList();
        const outList = subOutList();

        bench.innerHTML = inList.length
            ? inList.map(p => subCardHtml(p, 'in', used.has(String(p.id)), String(p.id) === String(pendingIn))).join('')
            : `<div class="text-muted small p-2">${langSubNoSubstitutes}</div>`;

        pitch.innerHTML = outList.length
            ? outList.map(p => subCardHtml(p, 'out', used.has(String(p.id)), String(p.id) === String(pendingOut))).join('')
            : `<div class="text-muted small p-2">${langSubLineupNotSet}</div>`;

        const bc = document.getElementById('sub-bench-count');
        const pc = document.getElementById('sub-pitch-count');
        if (bc) bc.textContent = inList.length;
        if (pc) pc.textContent = outList.length;

        // บอกสถานะว่ากำลังรอคลิกอีกฝั่ง
        const hint = document.getElementById('sub-hint');
        if (hint) {
            let msg = langSubPickHint;
            if (pendingIn)  msg = langSubPickedIn;
            if (pendingOut) msg = langSubPickedOut;
            hint.innerHTML = '<i class="ti ti-hand-click me-1"></i>' + msg;
        }
    }

    function renderSubPairs() {
        const box = document.getElementById('sub-pair-list');
        if (!box) return;

        box.innerHTML = subPairs.map((x, i) => `
            <div class="sub-pair" data-pair-index="${i}">
                <span class="sub-pair__no">${i + 1}</span>
                <span class="sub-pair__side text-success fw-semibold">
                    <i class="ti ti-arrow-down-right me-1"></i>${playerName(x.outId)}
                </span>
                <span class="sub-pair__side text-primary fw-semibold">
                    <i class="ti ti-arrow-up-right me-1"></i>${playerName(x.inId)}
                </span>
                <input type="number" class="form-control form-control-sm sub-pair__minute sub-pair-minute"
                       min="1" max="120" placeholder="${langMinute}" value="${x.minute || ''}">
                <button type="button" class="btn btn-sm btn-outline-danger sub-pair-remove" title="${langCancel}">
                    <i class="ti ti-trash"></i>
                </button>
            </div>`).join('');

        const cnt = document.getElementById('sub-pair-count');
        if (cnt) cnt.textContent = subPairs.length;
        const empty = document.getElementById('sub-multi-empty');
        if (empty) empty.style.display = subPairs.length ? 'none' : '';
    }

    function renderSubMode() {
        renderSubPanes();
        renderSubPairs();
    }

    // เรียกหลังโหลดรายชื่อเสร็จ ให้สองฝั่งอัปเดตตามข้อมูลใหม่
    function syncSubRowsAfterLoad() {
        try {
            const multi = document.getElementById('sub-multi-container');
            if (!multi || multi.style.display === 'none') return;
            clearEventModalAlert();
            renderSubMode();
        } catch (e) {
            console.error('syncSubRowsAfterLoad error:', e);
        }
    }

    function resetSubRows() {
        subPairs = [];
        pendingIn = null;
        pendingOut = null;
        renderSubMode();
    }

    function setSubMultiMode(on) {
        const multi = document.getElementById('sub-multi-container');
        if (!multi) return;   // เช็คก่อนแตะ element อื่น ไม่ให้ error ไปล้มโค้ดเดิมที่เรียกเรา

        const p1El    = document.getElementById('event-player1-select');
        const p1Group = p1El ? p1El.closest('.mb-3') : null;
        const minute  = document.getElementById('event-single-minute-container');
        const box     = document.getElementById('event-modal-container');

        multi.style.display = on ? 'block' : 'none';
        if (p1Group) p1Group.style.display = on ? 'none' : '';
        if (minute)  minute.style.display  = on ? 'none' : '';   // นาทีย้ายไปอยู่ในแต่ละคู่
        if (box) {
            box.classList.toggle('modal-sm', !on);
            box.classList.toggle('modal-lg', on);
        }

        if (on) renderSubMode();
        else { resetSubRows(); clearEventModalAlert(); }
    }

    // คลิกการ์ดฝั่งใดฝั่งหนึ่ง — ครบคู่เมื่อไหร่ก็เพิ่มลงรายการ
    function pickSubCard(side, id) {
        if (pairedIds().has(String(id))) return;

        if (side === 'in')  pendingIn  = (String(pendingIn)  === String(id)) ? null : id;
        if (side === 'out') pendingOut = (String(pendingOut) === String(id)) ? null : id;

        if (pendingIn && pendingOut) {
            subPairs.push({ inId: pendingIn, outId: pendingOut, minute: '' });
            pendingIn = null;
            pendingOut = null;
            clearEventModalAlert();
        }
        renderSubMode();
    }

    function collectSubRows() {
        const subs = subPairs.map(x => ({
            player_out: x.outId,
            player_in: x.inId,
            minute: x.minute || ''
        }));
        return { subs, incomplete: false };
    }

    function saveSubstitutions() {
        const matchId = document.getElementById('event-match-id').value;
        const teamId  = currentEventTeamId || document.getElementById('event-team-select').value;

        if (!teamId) { showEventModalAlert(langSubSelectTeamFirst); return; }

        const { subs } = collectSubRows();
        if (!subs.length) { showEventModalAlert(langSubNoRow); return; }
        clearEventModalAlert();

        const formData = new FormData();
        formData.append('match_id', matchId);
        formData.append('team_id', teamId);
        formData.append('subs', JSON.stringify(subs));

        fetch('<?= base_url('admin/tournament/add-match-substitutions') ?>', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.status === 'success') {
                    MicroModal.close('event-modal');
                    Swal.fire({
                        icon: 'success',
                        title: langSubSavedCount.replace('%d', data.saved ?? subs.length),
                        timer: 1500,
                        showConfirmButton: false
                    });
                    loadAllTeamData(matchId);
                    resetSubRows();
                    setSubMultiMode(false);
                    currentEventTeamId = null;
                } else {
                    showEventModalAlert(data.msg || langError);
                }
            })
            .catch(err => {
                console.error(err);
                showEventModalAlert(langError);
            });
    }

    $(document).on('click', '.sub-card', function () {
        if (this.disabled) return;
        pickSubCard(this.dataset.subSide, this.dataset.subId);
    });

    $(document).on('click', '.sub-pair-remove', function () {
        const i = parseInt($(this).closest('.sub-pair').data('pair-index'), 10);
        if (!isNaN(i)) { subPairs.splice(i, 1); renderSubMode(); }
    });

    $(document).on('input', '.sub-pair-minute', function () {
        const i = parseInt($(this).closest('.sub-pair').data('pair-index'), 10);
        if (!isNaN(i) && subPairs[i]) subPairs[i].minute = this.value;
    });

    function saveEvent() {
        // เปลี่ยนตัวใช้เส้นทางบันทึกหลายคู่แทน
        if (document.getElementById('event-type-select').value === '2') {
            saveSubstitutions();
            return;
        }

        const matchId = document.getElementById('event-match-id').value;
        // ใช้ currentEventTeamId ถ้ามี (จากปุ่มทีมเดียว) หรือใช้จาก select
        const teamId = currentEventTeamId || document.getElementById('event-team-select').value;
        const eventId = document.getElementById('event-type-select').value;
        const player1Id = document.getElementById('event-player1-select').value;
        const player2Id = document.getElementById('event-player2-select').value;
        const minute = document.getElementById('event-minute').value;

        if (!teamId || !eventId || !player1Id) {
            showEventModalAlert(langSelectEventAndPlayer);
            return;
        }
        clearEventModalAlert();

        const formData = new FormData();
        formData.append('match_id', matchId);
        formData.append('team_id', teamId);
        formData.append('event_id', eventId);
        formData.append('player_1_id', player1Id);
        formData.append('player_2_id', player2Id);
        formData.append('minute', minute);

        fetch('<?= base_url('admin/tournament/add-match-event') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: langAddEventSuccess,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    MicroModal.close('event-modal');

                    // Refresh ข้อมูลทีมทั้งหมด (ทั้ง lineup และ events)
                    loadAllTeamData(matchId);

                    // Reset stored team data
                    currentEventTeamId = null;
                    currentEventTeamName = null;
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: langError,
                        text: data.msg
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: langError,
                    text: langCannotSave
                });
            });
    }

    function deleteEvent(eventId, matchId) {
        Swal.fire({
            icon: 'question',
            title: langConfirmDeleteEvent,
            showCancelButton: true,
            confirmButtonText: langDeleteBtn,
            cancelButtonText: langCancel,
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('event_id', eventId);

                fetch('<?= base_url('admin/tournament/remove-match-event') ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: langDeleteEventSuccess,
                                timer: 1500,
                                showConfirmButton: false
                            });
                            loadEventsDisplay(matchId);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: langError,
                                text: data.msg
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
    }

    function loadEventsDisplay(matchId) {
        const contentDiv = document.getElementById(`events-content-${matchId}`);
        contentDiv.innerHTML = '<div class="text-center text-muted small py-2"><i class="ti ti-loader ti-spin me-1"></i><?= lang('Common.loading') ?></div>';

        fetch(`<?= base_url('admin/tournament/get-match-events') ?>/${matchId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderEventsDisplay(matchId, data.data.events);
                } else {
                    contentDiv.innerHTML = '<div class="no-events">${langNoEvents}</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                contentDiv.innerHTML = '<div class="text-center text-danger small py-2">${langErrorOccurred}</div>';
            });
    }

    function renderEventsDisplay(matchId, events) {
        const contentDiv = document.getElementById(`events-content-${matchId}`);
        const card = document.querySelector(`[data-match-id="${matchId}"]`);
        const isEnded = card.classList.contains('match-ended');

        if (!events || events.length === 0) {
            contentDiv.innerHTML = '<div class="no-events">${langNoEvents}</div>';
            return;
        }

        // Sort by minute
        events.sort((a, b) => parseInt(a.minute || 0) - parseInt(b.minute || 0));

        let html = '';
        events.forEach(event => {
            const playerName = event.pl1_first_name ? `${event.pl1_first_name} ${event.pl1_last_name || ''}` : langNotSpecified;
            const player2Name = event.pl2_first_name ? `${event.pl2_first_name} ${event.pl2_last_name || ''}` : '';

            let details = playerName;
            if (event.event_id == 2 && player2Name) { // Substitution
                details = `${playerName} → ${player2Name}`;
            } else if (event.event_id == 1 && player2Name) { // Goal with assist
                details = `${playerName} (${langAssist}: ${player2Name})`;
            }

            const deleteBtn = !isEnded ? `<span class="event-delete" onclick="deleteEvent(${event.match_event_id}, ${matchId})"><i class="ti ti-x"></i></span>` : '';

            html += `
            <div class="event-item">
                <span class="event-minute">${event.minute || '-'}'</span>
                <span class="event-icon">${event.icon || ''}</span>
                <span class="event-details">${details}</span>
                ${deleteBtn}
            </div>
        `;
        });

        contentDiv.innerHTML = html;
    }

    // ==========================================
    // Team-specific Lineup & Events Functions (New UI)
    // ==========================================

    function loadTeamLineupDisplay(matchId, teamId, teamNum) {
        const contentDiv = document.getElementById(`lineup-team${teamNum}-content-${matchId}`);
        if (!contentDiv) return;

        contentDiv.innerHTML = '<div class="text-center text-muted small py-2"><i class="ti ti-loader ti-spin me-1"></i><?= lang('Common.loading') ?></div>';

        fetch(`<?= base_url('admin/tournament/get-match-players') ?>/${matchId}/${teamId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    renderTeamLineup(contentDiv, data.data || []);
                } else {
                    contentDiv.innerHTML = '<div class="text-center text-muted small py-2">${langNoLineup}</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                contentDiv.innerHTML = '<div class="text-center text-danger small py-2">${langErrorOccurred}</div>';
            });
    }

    function renderTeamLineup(contentDiv, players) {
        if (!players || players.length === 0) {
            contentDiv.innerHTML = '<div class="text-center text-muted small py-2">${langNoLineup}</div>';
            return;
        }

        const starters = players.filter(p => p.is_substitute != 1);
        const subs = players.filter(p => p.is_substitute == 1);

        let html = `
            <table class="table table-sm lineup-table mb-0">
                <thead>
                    <tr>
                        <th width="50" class="text-center">${langNumber}</th>
                        <th>${langFullName}</th>
                        <th width="100">${langPosition}</th>
                    </tr>
                </thead>
                <tbody>`;

        // Starters section
        if (starters.length > 0) {
            html += `<tr class="lineup-section-header"><td colspan="3"><strong>${langStarter} (${starters.length})</strong></td></tr>`;
            starters.forEach(p => {
                html += `
                    <tr class="lineup-starter-row">
                        <td class="text-center fw-bold">${p.shirt_number || '-'}</td>
                        <td>${p.first_name} ${p.last_name || ''}</td>
                        <td class="text-muted small">${p.position_name || '-'}</td>
                    </tr>`;
            });
        }

        // Substitutes section
        if (subs.length > 0) {
            html += `<tr class="lineup-section-header sub-header"><td colspan="3"><strong>${langSubstitute} (${subs.length})</strong></td></tr>`;
            subs.forEach(p => {
                html += `
                    <tr class="lineup-sub-row">
                        <td class="text-center fw-bold">${p.shirt_number || '-'}</td>
                        <td>${p.first_name} ${p.last_name || ''}</td>
                        <td class="text-muted small">${p.position_name || '-'}</td>
                    </tr>`;
            });
        }

        html += `</tbody></table>`;
        contentDiv.innerHTML = html;
    }

    function loadTeamEventsDisplay(matchId, teamId, teamNum) {
        const contentDiv = document.getElementById(`events-team${teamNum}-content-${matchId}`);
        if (!contentDiv) return;

        contentDiv.innerHTML = '<div class="text-center text-muted small py-2"><i class="ti ti-loader ti-spin me-1"></i><?= lang('Common.loading') ?></div>';

        fetch(`<?= base_url('admin/tournament/get-match-events') ?>/${matchId}`)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    // Filter events by team
                    const teamEvents = (data.data.events || []).filter(e => e.team_id == teamId);
                    renderTeamEvents(contentDiv, teamEvents, matchId);
                } else {
                    contentDiv.innerHTML = '<div class="no-team-events">${langNoEvents}</div>';
                }
            })
            .catch(error => {
                console.error('Error:', error);
                contentDiv.innerHTML = '<div class="text-center text-danger small py-2">${langErrorOccurred}</div>';
            });
    }

    function renderTeamEvents(contentDiv, events, matchId) {
        const card = document.querySelector(`[data-match-id="${matchId}"]`);
        const isEnded = card ? card.classList.contains('match-ended') : false;

        if (!events || events.length === 0) {
            contentDiv.innerHTML = '<div class="no-team-events">${langNoEvents}</div>';
            return;
        }

        // Sort by minute
        events.sort((a, b) => parseInt(a.minute || 0) - parseInt(b.minute || 0));

        let html = '';
        events.forEach(event => {
            const playerName = event.pl1_first_name ? `${event.pl1_first_name} ${event.pl1_last_name || ''}` : langNotSpecified;
            const player2Name = event.pl2_first_name ? `${event.pl2_first_name} ${event.pl2_last_name || ''}` : '';

            let details = playerName;
            if (event.event_id == 2 && player2Name) {
                details = `${playerName} → ${player2Name}`;
            } else if (event.event_id == 1 && player2Name) {
                details = `${playerName} <small class="text-muted">(${player2Name})</small>`;
            }

            const deleteBtn = !isEnded ? `<span class="team-event-delete" onclick="deleteEventAndRefreshTeam(${event.match_event_id}, ${matchId}, ${event.team_id})"><i class="ti ti-x"></i></span>` : '';

            html += `
            <div class="team-event-item">
                <span class="team-event-minute">${event.minute || '-'}'</span>
                <span class="team-event-icon">${event.icon || ''}</span>
                <span class="team-event-details">${details}</span>
                ${deleteBtn}
            </div>
        `;
        });

        contentDiv.innerHTML = html;
    }

    function deleteEventAndRefreshTeam(eventId, matchId, teamId) {
        Swal.fire({
            icon: 'question',
            title: langConfirmDelete,
            text: langDeleteConfirm,
            showCancelButton: true,
            confirmButtonText: langDeleteBtn,
            cancelButtonText: langCancel,
            confirmButtonColor: '#dc3545'
        }).then((result) => {
            if (result.isConfirmed) {
                const formData = new FormData();
                formData.append('event_id', eventId);

                fetch('<?= base_url('admin/tournament/remove-match-event') ?>', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: langDeleteEventSuccess,
                                timer: 1000,
                                showConfirmButton: false
                            });
                            // Refresh both teams
                            loadAllTeamData(matchId);
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: langError,
                                text: data.msg
                            });
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                    });
            }
        });
    }

    function loadAllTeamData(matchId) {
        // Get team IDs from the card's data attributes
        const card = document.querySelector(`.match-card[data-match-id="${matchId}"]`);
        if (!card) return;

        // Use data attributes directly from card (works for both ended and non-ended matches)
        const team1Id = card.dataset.team1Id;
        const team2Id = card.dataset.team2Id;

        if (team1Id) {
            loadTeamLineupDisplay(matchId, team1Id, 1);
            loadTeamEventsDisplay(matchId, team1Id, 1);
        }
        if (team2Id) {
            loadTeamLineupDisplay(matchId, team2Id, 2);
            loadTeamEventsDisplay(matchId, team2Id, 2);
        }
    }

    // Store current team for event modal
    let currentEventTeamId = null;
    let currentEventTeamName = null;
    let currentEventMatchId = null;

    // ==========================================
    // Initialize on Page Load (Using jQuery)
    // ==========================================

    $(document).ready(function() {
        // Move modals to body level to fix positioning issues caused by parent transforms
        $(".micromodal").appendTo("body");
        // Initialize collapse toggle icons
        $('.round-header').on('click', function() {
            $(this).toggleClass('collapsed');
        });

        // Load team data on page load for each match card
        $('.match-card').each(function() {
            const matchId = $(this).data('match-id');
            if (matchId) {
                loadAllTeamData(matchId);
            }
        });

        function resetModalPosition(modalId) {
            $('#' + modalId).find('.micromodal-container').attr('style', '');
        }

        // Button click handlers to open modals
        $(document).on('click', '.btn-open-lineup', function(e) {
            e.preventDefault();
            e.stopPropagation();

            const $btn = $(this);
            const matchId = $btn.data('match-id');
            const team1Id = $btn.data('team1-id');
            const team2Id = $btn.data('team2-id');
            const team1Name = $btn.data('team1-name');
            const team2Name = $btn.data('team2-name');

            console.log('Match ID:', matchId, 'Team1:', team1Id, 'Team2:', team2Id);

            $('#lineup-match-id').val(matchId);
            $('#lineup-team1-id').val(team1Id);
            $('#lineup-team2-id').val(team2Id);
            $('#lineup-team1-name').text(team1Name);
            $('#lineup-team2-name').text(team2Name);
            $('#lineup-team1-name-btn').text(team1Name);
            $('#lineup-team2-name-btn').text(team2Name);

            // Hide single team pane, show tabs for both teams
            $('#lineup-single-team-pane').hide();
            $('#lineupTeamTabs').show();
            $('#lineup-team1-tab').removeClass('force-hide').addClass('show active');
            $('#lineup-team2-tab').removeClass('force-hide show active');

            // Load existing lineup
            loadLineupModal(matchId, team1Id, team2Id);

            // Position and open modal
            resetModalPosition('lineup-modal');
            MicroModal.show('lineup-modal');
        });

        $(document).on('click', '.btn-open-event', function(e) {
            e.preventDefault();
            e.stopPropagation();
            console.log('Event button clicked - manual trigger');

            const $btn = $(this);
            const matchId = $btn.data('match-id');
            const team1Id = $btn.data('team1-id');
            const team2Id = $btn.data('team2-id');
            const team1Name = $btn.data('team1-name');
            const team2Name = $btn.data('team2-name');

            console.log('Match ID:', matchId);

            $('#event-match-id').val(matchId);
            $('#event-team-select').html(`
                <option value="">${langSelectTeam}</option>
                <option value="${team1Id}">${team1Name}</option>
                <option value="${team2Id}">${team2Name}</option>
            `);
            $('#event-type-select').val('');
            $('#event-player1-select').html('<option value="">${langSelectPlayer}</option>');
            $('#event-player2-select').html('<option value="">${langSelectPlayer}</option>');
            $('#event-minute').val('');
            $('#event-player2-container').hide();
            resetEventModalExtras(); // เปิด modal ใหม่ = เริ่มจากโหมดเดี่ยวเสมอ
            $('#event-team-container').show(); // Show team selector

            // Clear stored team data
            currentEventTeamId = null;
            currentEventTeamName = null;
            currentEventMatchId = matchId;

            // Position and open modal
            resetModalPosition('event-modal');
            MicroModal.show('event-modal');
        });

        // New handler for single team event button
        $(document).on('click', '.btn-add-event-single', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            const $btn = $(this);
            const matchId = $btn.data('match-id');
            const teamId = $btn.data('team-id');
            const teamName = $btn.data('team-name');

            console.log('Add event for team:', teamId, teamName);

            // Store team data
            currentEventTeamId = teamId;
            currentEventTeamName = teamName;
            currentEventMatchId = matchId;

            // Set up modal with pre-selected team
            $('#event-match-id').val(matchId);
            $('#event-team-select').val(teamId);
            $('#event-team-container').hide(); // Hide team selector since we know the team
            $('#event-modal-team-display').text(teamName);
            $('#event-modal-team-display-container').show();

            $('#event-type-select').val('');
            $('#event-player1-select').html('<option value="">${langSelectPlayer}</option>');
            $('#event-player2-select').html('<option value="">${langSelectPlayer}</option>');
            $('#event-minute').val('');
            $('#event-player2-container').hide();

            // Reset player data
            currentEventPlayers = { starters: [], substitutes: [], all: [], unassigned: [] };

            // Load players for this team — ต้องยิงก่อน ไม่ให้อะไรมาขวาง
            loadEventPlayersForTeam(matchId, teamId);

            resetEventModalExtras(); // เปิด modal ใหม่ = เริ่มจากโหมดเดี่ยวเสมอ

            // Position and open modal
            resetModalPosition('event-modal');
            MicroModal.show('event-modal');
        });

        // New handler for single team lineup button
        $(document).on('click', '.btn-open-lineup-single', function(e) {
            e.preventDefault();
            e.stopPropagation();
            e.stopImmediatePropagation();

            const $btn = $(this);
            const matchId = $btn.data('match-id');
            const teamId = $btn.data('team-id');
            const teamName = $btn.data('team-name');

            console.log('Open lineup for team:', teamId, teamName);

            // Set up lineup modal for single team
            $('#lineup-match-id').val(matchId);
            $('#lineup-single-team-id').val(teamId);
            $('#lineup-single-team-name').text(teamName);

            // Hide tabs and old tab panes completely
            $('#lineupTeamTabs').hide();
            $('#lineup-team1-tab').removeClass('show active').addClass('force-hide');
            $('#lineup-team2-tab').removeClass('show active').addClass('force-hide');

            // Show single team pane
            $('#lineup-single-team-pane').show();

            // Load players for this team
            loadLineupModalSingleTeam(matchId, teamId);

            // Position and open modal
            resetModalPosition('lineup-modal');
            MicroModal.show('lineup-modal');
        });
    });

    // Load players for event modal (single team)
    function loadEventPlayersForTeam(matchId, teamId) {
        // ยิงพร้อมกัน 2 เส้น ไม่ต่อคิวกัน เพราะ request บนเครื่อง dev ช้าอยู่แล้ว
        //   lineup = คนที่ถูกจัดตัวไว้จริง (มี is_substitute)
        //   squad  = รายชื่อทั้งทีมในทัวร์นี้ ใช้หาคนที่ "ยังไม่ได้เลือก"
        Promise.all([
            fetch(`<?= base_url('admin/tournament/get-match-players') ?>/${matchId}/${teamId}`)
                .then(r => r.json()).then(d => d.data || []).catch(() => []),
            fetch(`<?= base_url('admin/tournament/get-team-players') ?>/${tourId}/${teamId}`)
                .then(r => r.json()).then(d => d.data || []).catch(() => [])
        ])
            .then(([lineup, squad]) => {
                // แยกผู้เล่นตัวจริงและตัวสำรอง
                currentEventPlayers.starters = lineup.filter(p => p.is_substitute != 1);
                currentEventPlayers.substitutes = lineup.filter(p => p.is_substitute == 1);

                // คนที่อยู่ในทีมแต่ยังไม่ถูกจัดตัวลงแมตช์นี้
                const inLineup = new Set(lineup.map(p => String(p.id)));
                currentEventPlayers.unassigned = squad.filter(p => !inLineup.has(String(p.id)));

                // all = ทุกคนที่เลือกได้ในเหตุการณ์ทั่วไป (ไม่ใช่เปลี่ยนตัว)
                const players = lineup.concat(currentEventPlayers.unassigned);
                currentEventPlayers.all = players;

                // options ของ player1 — รวมตัวสำรองด้วย แยกกลุ่มให้เห็นว่าใครเป็นใคร
                document.getElementById('event-player1-select').innerHTML =
                    playerOptionsGrouped(langSelectPlayer);

                // อัปเดต player2 dropdown
                updatePlayer2Options();
                syncSubRowsAfterLoad();
                if (!lineup.length) showEventModalAlert(langSubLineupNotSet);
            })
            .catch(error => {
                console.error('Error loading players:', error);
            });
    }

    // Load lineup modal for single team
    async function loadLineupModalSingleTeam(matchId, teamId) {
        const tbody = document.getElementById('lineup-single-team-players');
        tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">${langLoading}</td></tr>';

        try {
            // Fetch available players
            const playersResponse = await fetch(`<?= base_url('admin/tournament/get-team-players') ?>/${tourId}/${teamId}`);
            const playersData = await playersResponse.json();

            // Fetch existing lineup
            const lineupResponse = await fetch(`<?= base_url('admin/tournament/get-match-players') ?>/${matchId}/${teamId}`);
            const lineupData = await lineupResponse.json();

            // Get starter and sub IDs
            const existingPlayers = lineupData.data || [];
            const starterIds = existingPlayers.filter(p => p.is_substitute != 1).map(p => String(p.id));
            const subIds = existingPlayers.filter(p => p.is_substitute == 1).map(p => String(p.id));

            // Render player table
            renderPlayerTableSingle(tbody, playersData.data || [], starterIds, subIds);

        } catch (error) {
            console.error('Error loading lineup:', error);
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-danger">${langErrorOccurred}</td></tr>';
        }
    }

    function renderPlayerTableSingle(tbody, players, starterIds, subIds) {
        if (players.length === 0) {
            tbody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">${langNoPlayerFound}</td></tr>';
            return;
        }

        let html = '';
        players.forEach(player => {
            const isStarter = starterIds.includes(String(player.id));
            const isSub = subIds.includes(String(player.id));
            const rowClass = isStarter ? 'table-success' : (isSub ? 'table-warning' : '');

            html += `
            <tr class="${rowClass}" data-player-id="${player.id}">
                <td class="text-center">
                    <input type="checkbox" class="form-check-input starter-checkbox"
                           data-player-id="${player.id}"
                           ${isStarter ? 'checked' : ''}
                           onchange="handleLineupCheckboxSingle(this, 'starter')">
                </td>
                <td class="text-center">
                    <input type="checkbox" class="form-check-input sub-checkbox"
                           data-player-id="${player.id}"
                           ${isSub ? 'checked' : ''}
                           onchange="handleLineupCheckboxSingle(this, 'sub')">
                </td>
                <td class="text-center fw-bold">${player.shirt_number || '-'}</td>
                <td>${player.first_name} ${player.last_name}</td>
                <td><small class="text-muted">${player.position_name || '-'}</small></td>
            </tr>
        `;
        });

        tbody.innerHTML = html;
        updateLineupCountsSingle();
    }

    function handleLineupCheckboxSingle(checkbox, type) {
        const row = checkbox.closest('tr');
        const starterCb = row.querySelector('.starter-checkbox');
        const subCb = row.querySelector('.sub-checkbox');

        if (type === 'starter' && checkbox.checked) {
            subCb.checked = false;
            row.classList.remove('table-warning');
            row.classList.add('table-success');
        } else if (type === 'sub' && checkbox.checked) {
            starterCb.checked = false;
            row.classList.remove('table-success');
            row.classList.add('table-warning');
        } else {
            row.classList.remove('table-success', 'table-warning');
        }

        updateLineupCountsSingle();
    }

    function updateLineupCountsSingle() {
        const starters = $('#lineup-single-team-players .starter-checkbox:checked').length;
        const subs = $('#lineup-single-team-players .sub-checkbox:checked').length;
        $('#lineup-single-starter-count').text(starters);
        $('#lineup-single-sub-count').text(subs);
    }

    function saveLineupSingleTeam() {
        const matchId = $('#lineup-match-id').val();
        const teamId = $('#lineup-single-team-id').val();

        const starters = [];
        const substitutes = [];

        $('#lineup-single-team-players .starter-checkbox:checked').each(function() {
            starters.push($(this).data('player-id'));
        });

        $('#lineup-single-team-players .sub-checkbox:checked').each(function() {
            substitutes.push($(this).data('player-id'));
        });

        const formData = new FormData();
        formData.append('match_id', matchId);
        formData.append('team_id', teamId);
        formData.append('starters', JSON.stringify(starters));
        formData.append('substitutes', JSON.stringify(substitutes));

        fetch('<?= base_url('admin/tournament/save-match-lineup') ?>', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: langSaveSuccess,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    MicroModal.close('lineup-modal');

                    // Reload team data
                    const card = document.querySelector(`[data-match-id="${matchId}"]`);
                    if (card) {
                        loadAllTeamData(matchId);
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: langError,
                        text: data.msg
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: langError,
                    text: langCannotSave
                });
            });
    }

    // ==========================================
    // Initialize: Load all lineup and events data on page load
    // ==========================================
    document.addEventListener('DOMContentLoaded', function() {
        // Find all match cards and load their data
        const matchCards = document.querySelectorAll('.match-card[data-match-id]');
        matchCards.forEach(card => {
            const matchId = card.dataset.matchId;
            if (matchId) {
                loadAllTeamData(matchId);
            }
        });
    });
</script>
