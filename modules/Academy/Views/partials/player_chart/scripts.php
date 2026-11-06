<script>
    // Lang variables
    const langSelectAll = '<?= lang('Common.select_all') ?>';
    const langDeselectAll = '<?= lang('Common.deselect_all') ?>';
    const langNoJoinedTournaments = '<?= lang('Common.no_joined_tournaments_msg') ?>';
    const langNoTournamentAvailable = '<?= lang('Common.no_tournament_available') ?>';
    const langNoApprovedTournament = '<?= lang('Common.no_approved_tournament') ?>';
    const langGoToJoinRequests = '<?= lang('Common.go_to_join_requests') ?>';
    const langThisTeam = '<?= lang('Common.this_team') ?>';
    const langOk = '<?= lang('Common.ok_btn') ?>';
    const langPleaseSelectTournament = '<?= lang('Common.please_select_tournament') ?>';
    const langSelectTournamentFromList = '<?= lang('Common.select_tournament_from_list') ?>';
    const langPleaseSelectPlayers = '<?= lang('Common.please_select_players') ?>';
    const langSelectAtLeast1 = '<?= lang('Common.select_at_least_1') ?>';
    const langMaxCoachesSelect = '<?= lang('Common.max_coaches_select') ?>';
    const langDeselectCoachFirst = '<?= lang('Common.deselect_coach_first') ?>';
    const langLoadingMsg = '<?= lang('Common.loading_msg') ?>';
    const langNoPlayerData = '<?= lang('Common.no_player_data') ?>';
    const langPcNoneSelected = '<?= lang('Common.pc_none_selected') ?>';
    const langPcNoSearchResult = '<?= lang('Common.pc_no_search_result') ?>';
    const langPcEditShirt = '<?= lang('Common.pc_edit_shirt') ?>';
    const langPcNotSubmittedTitle = '<?= lang('Common.pc_not_submitted_title') ?>';
    const langPcNotSubmittedHint = '<?= lang('Common.pc_not_submitted_hint') ?>';
    const langPcChangedTitle = '<?= lang('Common.pc_changed_title') ?>';
    const langPcChangedHint = '<?= lang('Common.pc_changed_hint') ?>';
    const langSave = '<?= lang('Common.save') ?>';
    const langNoPlayerDataCard = '<?= lang('Common.no_player_data_card') ?>';
    const langErrorLoadingData = '<?= lang('Common.error_loading_data') ?>';
    const langSaveSuccess = '<?= lang('Common.save_success') ?>';
    const langSavePlayerSelection = '<?= lang('Common.save_player_selection') ?>';
    const langSaveCoachSelection = '<?= lang('Common.save_coach_selection') ?>';
    const langSaved = '<?= lang('Common.saved') ?>';
    const langErrorLabel = '<?= lang('Common.error_label') ?>';
    const langCannotSave = '<?= lang('Common.cannot_save') ?>';
    const langConnectionErrorServer = '<?= lang('Common.connection_error_server') ?>';
    const langNoTeamData = '<?= lang('Common.no_team_data') ?>';
    const langSelectTeamFirst = '<?= lang('Common.select_team_first') ?>';
    const langConfirmSubmitTitle = '<?= lang('Common.confirm_submit_title') ?>';
    const langSubmitIrreversible = '<?= lang('Common.submit_irreversible') ?>';
    const langConfirm = '<?= lang('Common.confirm') ?>';
    const langCancel = '<?= lang('Common.cancel') ?>';
    const langSuccess = '<?= lang('Common.success') ?>';
    const langError = '<?= lang('Common.error_title') ?>';
    const langSelectTournamentToSubmitList = '<?= lang('Common.select_tournament_to_submit_list') ?>';
    const langCannotConnectServer = '<?= lang('Common.cannot_connect_server') ?>';
    const langAlreadySubmittedEdit = '<?= lang('Common.already_submitted_edit') ?>';
    const langEditList = '<?= lang('Common.edit_list') ?>';
    const langConfirmSubmitBtn = '<?= lang('Common.confirm_submit_btn') ?>';
    const langSelectedCount = '<?= lang('Common.selected_count') ?>';
    const langMaxCount = '<?= lang('Common.max_count') ?>';
    const langOverLimit = '<?= lang('Common.over_limit') ?>';
    const langPlayerCountUnit = '<?= lang('Common.player_count_unit') ?>';
    const langAlreadySubmittedInfo = '<?= lang('Common.already_submitted_info') ?>';
    const langTournamentListLabel = '<?= lang('Common.tournament_list_label') ?>';
    const langPlayerCount = '<?= lang('Common.player_count') ?>';
    const langNoSelectedPlayer = '<?= lang('Common.no_selected_player') ?>';
    const langNoSubmittedPlayer = '<?= lang('Common.no_submitted_player') ?>';
    const langConnectionErrorShort = '<?= lang('Common.connection_error_short') ?>';
    const langClickToUpload = '<?= lang('Common.click_to_upload') ?>';
    const langUploading = '<?= lang('Common.uploading') ?>';
    const langUploadSuccess = '<?= lang('Common.upload_success') ?>';
    const langCannotUpload = '<?= lang('Common.cannot_upload') ?>';
    const langInvalidFileJersey = '<?= lang('Common.invalid_file_jersey') ?>';
    const langSelectImageFile = '<?= lang('Common.select_image_file') ?>';
    const langFileTooLargeJersey = '<?= lang('Common.file_too_large_jersey') ?>';
    const langFileMax5mb = '<?= lang('Common.file_max_5mb_short') ?>';
    const langConfirmDeleteJersey = '<?= lang('Common.confirm_delete_jersey_title') ?>';
    const langDeleteJerseyMsg = '<?= lang('Common.confirm_delete_jersey') ?>';
    const langDelete = '<?= lang('Common.delete') ?>';
    const langDeleteSuccess = '<?= lang('Common.delete_success') ?>';
    const langDuplicatePlayerTitle = '<?= lang('Common.duplicate_player_title') ?>';
    const langDuplicateClaimedBy = '<?= lang('Common.duplicate_claimed_by') ?>';
    const langDuplicateAcademy = '<?= lang('Common.duplicate_academy') ?>';
    const langDuplicateTournament = '<?= lang('Common.duplicate_tournament') ?>';
    const langDuplicateSubmittedAt = '<?= lang('Common.duplicate_submitted_at') ?>';
    const langSourceIneligibleBadge = '<?= lang('Common.source_ineligible_badge') ?>';
    const langSourceEligibilityBanner = '<?= lang('Common.source_eligibility_banner') ?>';
    const langSourceBannerTitle = '<?= lang('Common.source_banner_title') ?>';
    const langSourceBannerEligible = '<?= lang('Common.source_banner_eligible') ?>';
    const langSourceBannerPersonUnit = '<?= lang('Common.source_banner_person_unit') ?>';
    const langSourceBannerSourcesLabel = '<?= lang('Common.source_banner_sources_label') ?>';
    const langSourceBannerShowAll = '<?= lang('Common.source_banner_show_all') ?>';
    const langSourceBannerShowLess = '<?= lang('Common.source_banner_show_less') ?>';
    const langSourceCheckTitle = '<?= lang('Common.source_check_title') ?>';
    const langSourceCheckRequiredLabel = '<?= lang('Common.source_check_required_label') ?>';
    const langSourceCheckIneligibleLabel = '<?= lang('Common.source_check_ineligible_label') ?>';
    const langSourceNotEligibleTitle = '<?= lang('Common.source_player_not_eligible_title') ?>';
    const langSourceNotEligibleIntro = '<?= lang('Common.source_player_not_eligible_intro') ?>';
    const langSourcePrevSubmitted = '<?= lang('Common.source_prev_submitted_label') ?>';
    const langSourcePrevTournament = '<?= lang('Common.source_prev_tournament_label') ?>';
    const langSourcePrevTeam = '<?= lang('Common.source_prev_team_label') ?>';
    const langSourcePrevDate = '<?= lang('Common.source_prev_date_label') ?>';

    // State: source-tournament blacklist (require_source_players)
    // blockedDetails: map player_id -> string[] ประวัติ "เคยส่งรายการไหน โดยทีมไหน วันที่เท่าไหร่"
    let sourceEligibility = { enabled: false, blockedIds: new Set(), blockedDetails: {}, sourceNames: '', sourceList: [] };

    // State for selected players per team
    let selectionsByTeam = {};
    const joinedTournaments = <?= json_encode($joined_tournaments ?? []) ?>;
    const langPendingApproval = '<?= lang('Common.pending_approval') ?>';
    const langPendingPayment = '<?= lang('Common.pending_payment') ?>';
    const langEnded = '<?= lang('Common.ended') ?>';
    const langTourEndedReadonly = '<?= lang('Common.tournament_ended_cannot_submit') ?>';
    const langSelectTournamentOption = '<?= lang('Common.select_tournament_option') ?>';
    const langViewTournament = '<?= lang('Common.view') ?> - <?= lang('Common.tournament_label') ?>';
    // State for selected coaches per team
    let coachSelectionsByTeam = {};
    const MAX_COACHES = 5;
    let currentTeamId = '<?= $team['id'] ?? '' ?>';
    // เข้ามาจากเมนู "แผงรูป" บน subnav จะมี ?tour_id= ติดมาด้วย → เลือกทัวร์ให้เลย
    const urlTourId = (new URLSearchParams(window.location.search).get('tour_id') || '').replace(/\D/g, '');
    const ACADEMY_ID = '<?= (int)($academy['id'] ?? 0) ?>';
    function buildPlayersUrl(teamId) {
        // ดึงเฉพาะนักเตะที่อยู่ในทีมนี้ + enrolled ใน academy เดียวกัน
        return '<?= base_url('academy/get-team-players') ?>/' + ACADEMY_ID + '/' + (teamId || currentTeamId || 0);
    }

    // All players data for viewing in modal
    let allPlayersData = <?= json_encode($players ?? []) ?>;

    // Initialize selection set for current team if not exists
    if (currentTeamId) {
        selectionsByTeam[currentTeamId] = new Set();
        coachSelectionsByTeam[currentTeamId] = [];
        loadSavedSelection(currentTeamId); // Initial load for players
        loadSavedCoachSelection(currentTeamId); // Initial load for coaches
        // ชุดแข่ง (kit) โหลดตามทัวร์นาเมนต์ที่เลือก — ดู window.loadTournamentKit ใน player_chart.php
    }

    // Function to filter tournaments dropdown and history table by selected team
    // "จบแล้ว" = แอดมินปิด is_active แล้ว และเลยวันจบมาแล้ว
    // (end_date คือวันปิดรับสมัคร วันแข่งจริงอยู่ที่ tournament_end_date)
    function tourEffectiveEnd(t) {
        const pick = v => (v && String(v).indexOf('0000') !== 0) ? String(v) : '';
        return pick(t.tournament_end_date) || pick(t.end_date) || '';
    }

    function isTourEnded(t, today) {
        if (parseInt(t.is_active || 0) === 1) return false;
        const end = tourEffectiveEnd(t);
        return end !== '' && end < today;
    }

    // ทัวร์ที่กำลังดูอยู่จบไปแล้วหรือยัง — ใช้ตัดสินว่าจะล็อกหน้าเป็นอ่านอย่างเดียว
    function isCurrentTourEnded() {
        const opt = $('#tournament_filter').find('option:selected');
        if (!opt.length || !opt.val()) return false;
        if (String(opt.attr('data-ended')) === '1') return true;
        // เผื่อ option ที่ server render มาซึ่งไม่มี data-ended
        const t = joinedTournaments.find(x => String(x.tour_id) === String(opt.val())
                                           && x.team_id == currentTeamId);
        return t ? isTourEnded(t, new Date().toISOString().split('T')[0]) : false;
    }

    // จบแล้ว = ดูได้อย่างเดียว ติ๊กเลือก/ส่งรายชื่อไม่ได้ (ฝั่ง server กันไว้อีกชั้น)
    function applyReadOnlyForEndedTour() {
        const locked = isCurrentTourEnded();
        $('.player-checkbox').prop('disabled', locked);
        $('#selectAllBtn, #pcGoSubmit, #pcTopSubmitBtn').prop('disabled', locked)
            .toggleClass('disabled', locked);
        $('#pcEndedNotice').toggleClass('d-none', !locked);
        return locked;
    }

    function filterTournamentsByTeam(teamId, preselectTourId) {
        const today = new Date().toISOString().split('T')[0];

        // 1. Rebuild #submit_tournament_select
        let submitHtml = `<option value="">${langSelectTournamentOption}</option>`;
        joinedTournaments.forEach(t => {
            const regStatus = parseInt(t.register_status || 0);
            if (t.team_id == teamId && [0, 1, 3].includes(regStatus)) {
                let statusLabel = '';
                if (regStatus === 0) statusLabel = ` [${langPendingApproval}]`;
                else if (regStatus === 3) statusLabel = ` [${langPendingPayment}]`;

                // รายการที่จบแล้วห้ามส่ง/แก้รายชื่อ (ฝั่ง server ก็กันไว้อีกชั้น)
                const ended = isTourEnded(t, today);
                if (ended) statusLabel += ` [${langEnded}]`;

                submitHtml += `<option value="${t.tour_team_id || ''}"
                    data-tour-id="${t.tour_id || ''}"
                    data-status="${t.player_list_status || 0}"
                    data-player-count="${t.player_count || 0}"
                    data-team-id="${t.team_id || ''}"
                    data-register-status="${regStatus}"${ended ? ' disabled' : ''}>
                    ${t.name} (${t.generation || '-'}) ${statusLabel}
                </option>`;
            }
        });
        $('#submit_tournament_select').html(submitHtml).val('');
        $('#playerCountInfo').addClass('d-none');
        $('#submitStatusInfo').addClass('d-none');

        // 2. Rebuild #tournament_select
        let exportHtml = `<option value="">${langSelectTournamentOption}</option>`;
        joinedTournaments.forEach(t => {
            const regStatus = parseInt(t.register_status || 0);
            const endDate = t.tournament_end_date || t.end_date || null;
            const isEnded = endDate && endDate < today;

            if (t.team_id == teamId && [0, 1, 3].includes(regStatus)) {
                let statusLabel = '';
                if (regStatus === 0) statusLabel = ` [${langPendingApproval}]`;
                else if (regStatus === 3) statusLabel = ` [${langPendingPayment}]`;
                if (isEnded) statusLabel += ` [${langEnded}]`;

                exportHtml += `<option value="${t.tour_id}"
                    data-name="${t.name}"
                    data-gen="${t.generation || ''}"
                    data-team-id="${t.team_id || ''}"
                    data-tour-team-id="${t.tour_team_id || ''}"
                    data-register-status="${regStatus}"
                    data-player-list-status="${parseInt(t.player_list_status || 0)}">
                    ${t.name} (${t.generation || '-'}) ${statusLabel}
                </option>`;
            }
        });
        $('#tournament_select').html(exportHtml).val('');
        $('#generation_input').val('');

        // 3. Rebuild #tournament_filter
        let filterHtml = `<option value="">${langViewTournament}</option>`;
        joinedTournaments.forEach(t => {
            if (t.team_id == teamId) {
                // รายการที่จบแล้ว: แสดงไว้ให้เห็นแต่กดเลือกไม่ได้
                const ended = isTourEnded(t, today);
                filterHtml += `<option value="${t.tour_id}" data-team-id="${t.team_id || ''}" data-ended="${ended ? 1 : 0}"${ended ? ' disabled' : ''}>
                    ${t.name} (${t.generation || '-'})${ended ? ` [${langEnded}]` : ''}
                </option>`;
            }
        });
        // preselectTourId มีค่าเฉพาะตอนเข้ามาจากเมนู "แผงรูป" ที่เลือกทัวร์มาแล้ว
        const $filter  = $('#tournament_filter').html(filterHtml);
        const wantTour = preselectTourId ? String(preselectTourId) : '';
        // ต้องเช็คหลังใส่ option ชุดใหม่แล้วเท่านั้น ไม่งั้นไปเช็คกับของเดิมที่ server render มา
        const $want    = wantTour !== '' ? $filter.find('option[value="' + wantTour + '"]') : $();
        const hasWant  = $want.length > 0 && !$want.prop('disabled');
        $filter.val(hasWant ? wantTour : '');

        // ไม่ auto-select — ให้ผู้ใช้เลือกทัวร์นาเมนต์เองก่อน แล้วจึงแสดงรายชื่อนักเตะ
        if (!hasWant) hidePlayerSection();

        // Filter History Table by team
        let hasVisibleRows = false;
        $('.history-row').each(function() {
            let rowTeamId = $(this).data('team-id');
            if (rowTeamId == teamId) {
                $(this).show();
                hasVisibleRows = true;
            } else {
                $(this).hide();
            }
        });

        // Show/hide empty message
        $('#no-history-row').remove();
        if (!hasVisibleRows) {
            $('#history-table-body').append(
                '<tr id="no-history-row"><td colspan="7" class="text-center py-4 text-muted">' +
                '<i class="fa fa-info-circle me-2"></i>' + langNoJoinedTournaments + '</td></tr>'
            );
        }
    }

    // Function to check if team has tournaments and open submit modal
    function openSubmitModal() {
        if (isCurrentTourEnded()) {
            Swal.fire({ icon: 'info', title: langEnded, text: langTourEndedReadonly, confirmButtonColor: '#16478D' });
            return;
        }

        /*
        if(!$("#tournament_filter").val()) {
            Swal.fire({ icon: 'warning', title: langPleaseSelectTournament, text: "กรุณาเลือกทัวร์นาเม้นท์  ก่อนกดส่งรายชื่อ!", confirmButtonColor: '#16478D' });
            return;
        }
        */
        // ตรวจว่ามีทีมและนักเตะที่ถูกเลือก
        if (!currentTeamId) {
            Swal.fire({ icon: 'warning', title: langNoTeamData, text: langSelectTeamFirst, confirmButtonColor: '#16478D' });
            return;
        }
        const playerCnt = (selectionsByTeam[currentTeamId] || new Set()).size;
        if (playerCnt === 0) {
            Swal.fire({
                icon: 'warning',
                title: langPleaseSelectPlayers,
                text: langSelectAtLeast1,
                confirmButtonColor: '#16478D',
                confirmButtonText: langOk
            });
            return;
        }

        // นับ options ที่ไม่ถูก disabled และไม่ใช่ default option
        let availableOptions = $('#submit_tournament_select option').filter(function() {
            return $(this).val() !== '' && !$(this).prop('disabled');
        }).length;

        if (availableOptions === 0) {
            // Get current team name
            let teamName = $('#teamSelector option:selected').text() || langThisTeam;
            Swal.fire({
                icon: 'warning',
                title: langNoTournamentAvailable,
                html: '<b>' + teamName.trim() + '</b> ' + langNoApprovedTournament + '<br><br>' + langGoToJoinRequests,
                confirmButtonColor: '#16478D',
                confirmButtonText: langOk
            });
            return;
        }

        // Open modal
        let modal = new bootstrap.Modal(document.getElementById('submit-player-list-modal'));
        modal.show();

        // คำนวณข้อมูลใน modal ใหม่ทุกครั้งที่เปิด (จำนวนที่เลือก/สถานะ/การ์ดนักเตะถูกจำกัดสิทธิ์)
        // — selection อาจเปลี่ยนหลังจาก dropdown ใน modal ถูกตั้งค่าครั้งล่าสุด ทำให้การ์ดค้างค่าเก่า
        $('#submit_tournament_select').trigger('change');
    }

    // Function to check if team has tournaments and open export modal
    function openExportModal() {
        if (!currentTeamId) {
            Swal.fire({ icon: 'warning', title: langNoTeamData, text: langSelectTeamFirst, confirmButtonColor: '#16478D' });
            return;
        }

        let availableOptions = $('#tournament_select option').filter(function() {
            return $(this).val() !== '';
        }).length;

        if (availableOptions === 0) {
            let teamName = $('#teamSelector option:selected').text() || langThisTeam;
            Swal.fire({
                icon: 'warning',
                title: langNoTournamentAvailable,
                html: '<b>' + teamName.trim() + '</b> ' + langNoApprovedTournament + '<br><br>' + langGoToJoinRequests,
                confirmButtonColor: '#16478D',
                confirmButtonText: langOk
            });
            return;
        }

        // Open modal
        let modal = new bootstrap.Modal(document.getElementById('export-modal'));
        modal.show();
    }

    $(document).ready(function() {
        // Initial filter tournaments by selected team on page load
        if (currentTeamId) {
            filterTournamentsByTeam(currentTeamId, urlTourId);
        }

        // Initialize DataTable
        // ตารางถูกซ่อนไว้ ใช้เป็นแหล่งข้อมูลให้สองฝั่ง (ดู renderTransferPanes)
        // ดึงทีเดียวให้ครบ ไม่แบ่งหน้า ไม่งั้นคนที่อยู่หน้าถัดไปจะหายจากฝั่งซ้าย
        let table = $('#table').DataTable({
            pageLength: 1000,
            processing: true,
            serverSide: true,
            responsive: true,
            deferLoading: 0, // ยังไม่โหลดนักเตะจนกว่าจะเลือกทัวร์นาเมนต์
            language: dtLanguage,
            ajax: {
                url: buildPlayersUrl(),
                method: "POST",
                data: function(d) {
                    // academy_id และ team_id อยู่ใน URL แล้ว
                },
                error: function(xhr, status, err) {
                    console.error('DataTable AJAX error:', status, err);
                    Swal.fire({
                        icon: 'error',
                        title: typeof langErrorLabel !== 'undefined' ? langErrorLabel : 'ผิดพลาด',
                        text: typeof langErrorLoadingData !== 'undefined' ? langErrorLoadingData : 'ไม่สามารถโหลดข้อมูลได้',
                        confirmButtonColor: '#16478D'
                    });
                }
            }, // Remove default search input 'f' from dom
            dom: 't<"d-flex justify-content-between align-items-center flex-wrap px-4 py-3"ip>',
            order: [[3, 'asc']],
            columnDefs: [{
                    targets: [0, 1],
                    orderable: false
                },
                {
                    targets: 0,
                    render: function(data, type, row) {
                        // Check if this ID is in our selected set for current team
                        let currentSet = selectionsByTeam[currentTeamId] || new Set();
                        let checked = currentSet.has(String(row.id)) ? 'checked' : '';
                        return '<div class="form-check d-flex justify-content-center"><input type="checkbox" value="' + row.id + '" class="form-check-input player-checkbox" style="cursor:pointer;" ' + checked + '></div>';
                    }
                }
            ],
            columns: [{
                    data: 'action',
                    className: 'text-center ps-4'
                },
                {
                    data: null,
                    className: 'text-center text-muted fw-bold',
                    render: function(data, type, row, meta) {
                        return meta.row + meta.settings._iDisplayStart + 1;
                    }
                },
                {
                    data: 'image',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        return data;
                    }
                },
                {
                    data: 'shirt_number',
                    name: 'shirt_sort',
                    className: 'text-center fw-bold text-primary fs-5'
                },
                {
                    data: 'full_name',
                    name: 'first_name',
                    className: 'fw-medium'
                },
                {
                    data: 'nick_name',
                    className: 'text-muted'
                },
                {
                    data: 'birth_date',
                    className: 'text-center text-muted'
                },
                {
                    data: 'age',
                    className: 'text-center fw-bold',
                    orderable: false
                },
                {
                    data: 'player_sub_position',
                    className: 'text-center',
                    orderable: false
                }
            ],
            drawCallback: function(settings) {
                // Update Select All button state based on visible checkboxes
                updateSelectAllButtonState();
                applySourceEligibilityMarks();
            }
        });

        // เก็บข้อมูลนักเตะที่ตารางโหลดมา ไว้ให้สองฝั่งใช้วาด
        $('#table').on('xhr.dt', function(e, settings, json) {
            transferPlayers = (json && json.data) ? json.data : [];
            renderTransferPanes();
        });

        // Event listener for checkbox changes (delegated for dynamic content)
        $(document).on('change', '.player-checkbox', function() {
            // ทัวร์จบแล้วห้ามแก้รายชื่อ — handler อีกตัวด้านล่างจะคืนค่า checkbox ให้เอง
            if (isCurrentTourEnded()) return;
            let val = String($(this).val());
            if (!selectionsByTeam[currentTeamId]) {
                selectionsByTeam[currentTeamId] = new Set();
            }

            if (this.checked) {
                selectionsByTeam[currentTeamId].add(val);
            } else {
                selectionsByTeam[currentTeamId].delete(val);
            }
            updateSelectAllButtonState();
            saveSelection(true); // Auto-save
            // Sync step indicator
            const cnt = (selectionsByTeam[currentTeamId] || new Set()).size;
            $('#stepPlayerHint').text(cnt + ' ' + langPlayerCountUnit);
        });

        // Team Selector Change Event
        $('#teamSelector').on('change', function() {
            currentTeamId = $(this).val();
            // อัปเดต URL ของ DataTable ให้ตรงกับทีมใหม่ (แต่ยังไม่โหลดจนกว่าจะเลือกทัวร์)
            playersLoadedOnce = false;
            if ($.fn.DataTable.isDataTable('#table')) {
                $('#table').DataTable().ajax.url(buildPlayersUrl(currentTeamId));
            }
            // Filter tournaments dropdown to match selected team (เรียก hidePlayerSection ภายใน)
            filterTournamentsByTeam(currentTeamId);
            // Ensure set exists for players
            if (!selectionsByTeam[currentTeamId]) {
                selectionsByTeam[currentTeamId] = new Set();
                loadSavedSelection(currentTeamId); // Load when switching teams
            } else {
                reloadViews();
            }
            // Ensure array exists for coaches
            if (!coachSelectionsByTeam[currentTeamId]) {
                coachSelectionsByTeam[currentTeamId] = [];
                loadSavedCoachSelection(currentTeamId); // Load when switching teams
            }
            // Reload coaches for the new team
            loadCoachesForTeam(currentTeamId);
        });

        // Event listener for coach checkbox changes
        $(document).on('change', '.coach-checkbox', function() {
            let val = String($(this).val());
            if (!coachSelectionsByTeam[currentTeamId]) {
                coachSelectionsByTeam[currentTeamId] = [];
            }

            if (this.checked) {
                if (coachSelectionsByTeam[currentTeamId].length > MAX_COACHES) {
                    this.checked = false;
                    Swal.fire({
                        icon: 'warning',
                        title: langMaxCoachesSelect.replace('%d', MAX_COACHES),
                        text: langDeselectCoachFirst,
                        confirmButtonColor: '#16478D'
                    });
                    return;
                }
                if (!coachSelectionsByTeam[currentTeamId].includes(val)) {
                    coachSelectionsByTeam[currentTeamId].push(val);
                }
            } else {
                let idx = coachSelectionsByTeam[currentTeamId].indexOf(val);
                if (idx !== -1) {
                    coachSelectionsByTeam[currentTeamId].splice(idx, 1);
                }
            }
            updateSelectAllCoachesButtonState();
            reorderCoachRows();
            saveCoachSelection(true); // Auto-save
        });


        // Custom Search Logic
        let searchTimeout;
        $('#customSearch').on('keyup', function() {
            let val = $(this).val();
            clearTimeout(searchTimeout);

            searchTimeout = setTimeout(function() {
                table.search(val);

                if (currentViewMode === 'table') {
                    table.draw();
                } else {
                    loadPlayerCards();
                }
            }, 300); // Debounce search
        });

        $('#tournament_filter').on('change', function() {
            let tourId = $(this).val();
            // โหลด/ล้างชุดแข่งของทัวร์ที่เลือก (เก็บที่ tournament_team)
            if (window.loadTournamentKit) window.loadTournamentKit(tourId);
            if (tourId) {
                // เลือกทัวร์แล้ว → แสดงรายชื่อนักเตะ
                showPlayerSection();
                // โหลด blacklist เพื่อ mark นักเตะที่ถูกจำกัดสิทธิ์ (แถวแดง) ตาม config ทัวร์
                // จากนั้นดึงรายชื่อที่เคยส่งแผงรูปล่าสุดของทัวร์นี้มา pre-select เสมอ
                loadSourceEligibility(currentTeamId, tourId, function(enabled) {
                    loadOldSavedSelection(currentTeamId, tourId);
                });
                applyReadOnlyForEndedTour();
            } else {
                // กลับไปสถานะ "ยังไม่เลือกทัวร์"
                hidePlayerSection();
                loadSavedSelection(currentTeamId);
                sourceEligibility = { enabled: false, blockedIds: new Set(), blockedDetails: {}, sourceNames: '', sourceList: [] };
                applySourceEligibilityMarks();
            }

            // Sync dropdown ใน modal ส่งรายชื่อให้ตรงกับทัวร์ที่เลือกบนหน้า
            // (เดิม modal จำค่าเก่า → การ์ดเตือน/การส่งชี้ไปคนละทัวร์กับที่ดูอยู่)
            let matchOpt = $('#submit_tournament_select option').filter(function() {
                return String($(this).data('tour-id')) === String(tourId) && !$(this).prop('disabled');
            }).first();
            $('#submit_tournament_select').val(matchOpt.length ? matchOpt.val() : '').trigger('change');
        });

        // ค่าถูกเลือกไว้ตั้งแต่ filterTournamentsByTeam() แล้ว เหลือแค่ยิง change
        // หลังผูก handler เสร็จ เพื่อให้รายชื่อนักเตะ/ชุดแข่งถูกโหลดตามปกติ
        if (urlTourId) {
            if ($('#tournament_filter').val() === urlTourId) {
                $('#tournament_filter').trigger('change');
            } else {
                // ทีมนี้ไม่ได้ลงทัวร์ที่ส่งมา — ปล่อยให้ผู้ใช้เลือกเองตามปกติ
                console.warn('[player-chart] tour_id=' + urlTourId + ' ไม่มีในรายการของทีม ' + currentTeamId);
            }
        }
    });

    function fetchSourceEligibility(teamId, tourId, cb) {
        if (!teamId || !tourId) { cb(null); return; }
        $.ajax({
            url: '<?= base_url('academy/get-source-eligibility') ?>/' + teamId + '/' + tourId,
            type: 'GET',
            success: function(response) {
                cb(response && response.status === 'success' ? response : null);
            },
            error: function() {
                console.error("Failed to load source eligibility");
                cb(null);
            }
        });
    }

    function loadSourceEligibility(teamId, tourId, done) {
        fetchSourceEligibility(teamId, tourId, function(response) {
            if (response && response.enabled) {
                sourceEligibility.enabled = true;
                sourceEligibility.blockedIds = new Set((response.blocked_ids || []).map(String));
                sourceEligibility.blockedDetails = response.blocked_details || {};
                sourceEligibility.sourceList = (response.source_tournaments || [])
                    .map(t => t.name + (t.generation ? ' (' + t.generation + ')' : ''));
                sourceEligibility.sourceNames = sourceEligibility.sourceList.join(', ');
                // ไม่ auto-select ตาม eligibility แล้ว — ให้ loadOldSavedSelection ดึงรายชื่อที่เคยส่งล่าสุดมา pre-select
            } else {
                sourceEligibility = { enabled: false, blockedIds: new Set(), blockedDetails: {}, sourceNames: '', sourceList: [] };
            }
            applySourceEligibilityMarks();
            if (typeof done === 'function') done(sourceEligibility.enabled);
        });
    }

    function escHtml(s) {
        return String(s == null ? '' : s)
            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
    }

    const SOURCE_CHIPS_LIMIT = 4;

    function renderSourceEligibilityBanner() {
        const count = sourceEligibility.blockedIds.size;
        const list = sourceEligibility.sourceList || [];
        const hasBlocked = count > 0;
        const unit = langSourceBannerPersonUnit ? (' ' + langSourceBannerPersonUnit) : '';

        // chips ทัวร์ต้นทาง (จำกัดจำนวน + toggle ดูทั้งหมด)
        let chips = '';
        list.forEach((name, i) => {
            const hidden = i >= SOURCE_CHIPS_LIMIT ? ' src-chip--extra d-none' : '';
            chips += '<span class="src-chip' + hidden + '">' + escHtml(name) + '</span>';
        });
        let toggle = '';
        if (list.length > SOURCE_CHIPS_LIMIT) {
            toggle = '<button type="button" class="src-chip-toggle" onclick="toggleSourceChips(this)" '
                + 'data-more="' + escHtml(langSourceBannerShowAll) + ' (+' + (list.length - SOURCE_CHIPS_LIMIT) + ')" '
                + 'data-less="' + escHtml(langSourceBannerShowLess) + '">'
                + escHtml(langSourceBannerShowAll) + ' (+' + (list.length - SOURCE_CHIPS_LIMIT) + ')</button>';
        }

        const html =
            '<div class="src-elig">'
            +   '<div class="src-elig__icon"><i class="fa fa-shield-alt"></i></div>'
            +   '<div class="src-elig__body">'
            +     '<div class="src-elig__head">'
            +       '<span class="src-elig__title">' + escHtml(langSourceBannerTitle) + '</span>'
            +       '<span class="src-elig__count ' + (hasBlocked ? 'is-none' : 'is-ok') + '">'
            +         '<i class="fa ' + (hasBlocked ? 'fa-exclamation-circle' : 'fa-user-check') + ' me-1"></i>'
            +         escHtml(langSourceBannerEligible) + ' ' + count + unit
            +       '</span>'
            +     '</div>'
            +     '<div class="src-elig__sources">'
            +       '<span class="src-elig__sources-label">' + escHtml(langSourceBannerSourcesLabel) + ':</span>'
            +       '<span class="src-elig__chips">' + chips + toggle + '</span>'
            +     '</div>'
            +   '</div>'
            + '</div>';

        $('#sourceEligibilityBannerText').html(html);
    }

    function toggleSourceChips(btn) {
        const $wrap = $(btn).closest('.src-elig__chips');
        const $extra = $wrap.find('.src-chip--extra');
        const isHidden = $extra.hasClass('d-none');
        $extra.toggleClass('d-none', !isHidden);
        $(btn).text(isHidden ? $(btn).data('less') : $(btn).data('more'));
    }

    // รายการนักเตะที่ถูกจำกัดสิทธิ์แบบมีโครงสร้าง: ชื่อ + (รายการ / ทีมที่ส่ง / วันที่) ต่อประวัติ
    // playerItems: [{name, entries: [{tournament, team, date}]}]
    function renderBlockedPlayerRows(playerItems) {
        return (playerItems || []).map(function(it) {
            let html = '<div class="mb-2 p-2" style="background:#fff;border:1px solid #f1c8c8;border-radius:8px;">'
                + '<div class="fw-bold text-danger"><i class="fa fa-user me-1"></i>' + escHtml(it.name) + '</div>';
            (it.entries || []).forEach(function(e) {
                html += '<div class="small mt-1 ps-3" style="color:#454545;">'
                    + '<div><i class="fa fa-trophy me-1 text-muted"></i>' + escHtml(langSourcePrevTournament) + ': <strong>' + escHtml(e.tournament) + '</strong></div>'
                    + '<div class="ms-3">' + escHtml(langSourcePrevTeam) + ': <strong>' + escHtml(e.team) + '</strong>'
                    + (e.date ? ' <span class="text-muted">·</span> ' + escHtml(langSourcePrevDate) + ' ' + escHtml(e.date) : '')
                    + '</div>'
                    + '</div>';
            });
            return html + '</div>';
        }).join('');
    }

    // การ์ดเตือน (submit modal): นักเตะที่ถูกจำกัดสิทธิ์ + ประวัติว่าเคยส่งรายการไหน โดยทีมไหน วันที่เท่าไหร่
    function renderSourceCheckCard(playerItems) {
        return '<div class="src-check">'
            +   '<div class="src-check__head">'
            +     '<span class="src-check__icon"><i class="fa fa-shield-alt"></i></span>'
            +     '<span class="src-check__title">' + escHtml(langSourceCheckTitle) + '</span>'
            +     '<span class="src-check__count">' + (playerItems || []).length + ' ' + escHtml(langSourceBannerPersonUnit) + '</span>'
            +   '</div>'
            +   '<div class="src-check__block">'
            +     '<span class="src-check__label">' + escHtml(langSourceCheckIneligibleLabel) + ' (' + (playerItems || []).length + '):</span>'
            +     '<div class="mt-1">' + renderBlockedPlayerRows(playerItems) + '</div>'
            +   '</div>'
            + '</div>';
    }

    function applySourceEligibilityMarks() {
        if (sourceEligibility.enabled) {
            renderSourceEligibilityBanner();
            $('#sourceEligibilityBanner').removeClass('d-none');
        } else {
            $('#sourceEligibilityBanner').addClass('d-none');
        }

        // tooltip: เคยส่งรายการไหน โดยทีมไหน วันที่เท่าไหร่
        function blockedTooltip(playerId) {
            let entries = sourceEligibility.blockedDetails[String(playerId)] || [];
            if (!entries.length) return '';
            let lines = entries.map(e => e.tournament + ' — ' + langSourcePrevTeam + ' ' + e.team + (e.date ? ' (' + e.date + ')' : ''));
            return langSourcePrevSubmitted + ': ' + lines.join(' | ');
        }

        $('#table tbody tr').each(function() {
            let cb = $(this).find('.player-checkbox');
            if (!cb.length) return;
            let ineligible = sourceEligibility.enabled && sourceEligibility.blockedIds.has(String(cb.val()));
            $(this).toggleClass('player-row--ineligible', ineligible)
                .attr('title', ineligible ? blockedTooltip(cb.val()) : '');
        });

        $('#playerCardsContainer .player-card').each(function() {
            let cb = $(this).find('.player-checkbox');
            if (!cb.length) return;
            let ineligible = sourceEligibility.enabled && sourceEligibility.blockedIds.has(String(cb.val()));
            $(this).toggleClass('player-card--ineligible', ineligible)
                .attr('data-ineligible-label', langSourceIneligibleBadge)
                .attr('title', ineligible ? blockedTooltip(cb.val()) : '');
        });
    }

    function loadOldSavedSelection(teamId, tourId) {
        if (!teamId || !tourId) return;

        $.ajax({
            url: '<?= base_url('academy/get-old-chart-selection') ?>/' + teamId + '/' + tourId,
            type: 'GET',
            success: function(response) {
                let savedIds = (response.status === 'success' && Array.isArray(response.data)) ? response.data : [];
                // ทัวร์นี้ยังไม่เคยส่งรายชื่อ → fallback ไปแสดง draft ที่บันทึกไว้ (team_player_chart_selection)
                if (savedIds.length === 0) {
                    loadSavedSelection(teamId);
                    return;
                }
                let validPlayerIds = new Set(allPlayersData.map(p => String(p.id)));
                let filteredIds = savedIds.map(String).filter(id => validPlayerIds.has(id));
                selectionsByTeam[teamId] = new Set(filteredIds);
                reloadViews();
                syncStepIndicator();
                // Persist pre-select ลง DB (team_player_chart_selection) ให้ตรงกับที่แสดง
                // มิฉะนั้น submit จะดึง draft เก่า (backend ใช้ getPlayerChartSelection ไม่ใช่ค่าที่ติ๊กบนหน้า)
                if (teamId == currentTeamId) {
                    saveSelection(true);
                }
            },
            error: function() {
                console.error("Failed to load old saved selection");
                // fallback ไป draft ที่บันทึกไว้
                loadSavedSelection(teamId);
            }
        });
    }

    function updateGeneration() {
        let selectedOption = $("#tournament_select").find("option:selected");
        let generation = selectedOption.data("gen") || '';
        $("#generation_input").val(generation);
    }

    let currentViewMode = 'table';
    let playerDataCache = [];

    // State: ผู้ใช้ต้องเลือกทัวร์นาเมนต์ก่อนจึงจะแสดงรายชื่อนักเตะ
    let tournamentSelected = false;
    let playersLoadedOnce = false;

    function hidePlayerSection() {
        tournamentSelected = false;
        $('#tableView').hide();
        $('#cardView').hide();
        // ยังไม่เลือกทัวร์ → ซ่อนเครื่องมือทั้งหมด ไม่ให้กดอะไรได้
        $('#selectAllBtn, #pcTopSearchWrap, #pcTopSubmitBtn').hide();
        $('#selectTournamentPrompt').show();
    }

    function showPlayerSection() {
        tournamentSelected = true;
        $('#selectTournamentPrompt').hide();
        // switchViewMode ด้านล่างจะซ่อนตัวที่ซ้ำกับมุมมองสองฝั่งอีกที
        $('#selectAllBtn, #pcTopSearchWrap, #pcTopSubmitBtn').show();
        // โหลดข้อมูลนักเตะครั้งแรกเมื่อเลือกทัวร์
        if (!playersLoadedOnce && $.fn.DataTable.isDataTable('#table')) {
            playersLoadedOnce = true;
            $('#table').DataTable().ajax.reload();
        }
        switchViewMode(currentViewMode);
    }

    function switchViewMode(mode) {
        currentViewMode = mode;

        // ยังไม่เลือกทัวร์ → ไม่แสดงตาราง/การ์ด
        if (!tournamentSelected) {
            $('#tableView').hide();
            $('#cardView').hide();
            return;
        }

        if (mode === 'table') {
            $('#tableView').show();
            $('#cardView').hide();
            // สองฝั่งมีปุ่มเลือกทั้งหมด ช่องค้นหา และปุ่มส่งรายชื่อของตัวเองแล้ว
            // ซ่อนตัวที่อยู่ด้านบนไว้ ไม่งั้นซ้ำกันอย่างละสองที่
            $('#selectAllBtn, #pcTopSearchWrap, #pcTopSubmitBtn').hide();
            $('#viewTableBtn').css({
                'background-color': '#16478C',
                'border-color': '#16478C'
            });
            $('#viewTableBtn i').css('color', 'white');
            $('#viewCardBtn').css({
                'background-color': 'white',
                'border': '2px solid #16478C'
            });
            $('#viewCardBtn i').css('color', '#16478C');
            if ($.fn.DataTable.isDataTable('#table')) {
                $('#table').DataTable().ajax.reload();
            }
        } else if (mode === 'card') {
            $('#tableView').hide();
            $('#cardView').show();
            // มุมมองการ์ดไม่มีของพวกนี้ในตัว จึงต้องใช้ตัวที่อยู่ด้านบน
            $('#selectAllBtn, #pcTopSearchWrap, #pcTopSubmitBtn').show();
            $('#viewTableBtn').css({
                'background-color': 'white',
                'border': '2px solid #16478C'
            });
            $('#viewTableBtn i').css('color', '#16478C');
            $('#viewCardBtn').css({
                'background-color': '#16478C',
                'border-color': '#16478C'
            });
            $('#viewCardBtn i').css('color', 'white');
            loadPlayerCards();
        }
    }

    function loadPlayerCards() {
        // Show loading state
        $('#playerCardsContainer').html('<div class="col-12 text-center py-5"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">' + langLoadingMsg + '</span></div></div>');

        // Get DataTable instance
        let table = $('#table').DataTable();

        // Get current params from DataTable to respect filters/search
        let params = table.ajax.params();
        let requestData = {};

        if (params) {
            $.extend(true, requestData, params);
        }

        // Ensure search value is up to date from custom input
        if (!requestData.search) requestData.search = {};
        requestData.search.value = $('#customSearch').val();

        // Override pagination to show all (or many) in card view
        requestData.start = 0;
        requestData.length = 1000;

        // Make AJAX call
        $.ajax({
            url: buildPlayersUrl(),
            method: 'POST',
            data: requestData,
            dataType: 'json',
            success: function(response) {
                if (response && response.data) {
                    playerDataCache = response.data;
                    renderPlayerCards(playerDataCache);
                } else {
                    $('#playerCardsContainer').html('<div class="col-12 text-center py-5"><p class="text-muted">' + langNoPlayerData + '</p></div>');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error loading player data:', error);
                $('#playerCardsContainer').html('<div class="col-12 text-center py-5"><p class="text-danger">' + langErrorLoadingData + '</p></div>');
            }
        });
    }

    /* ===== สองฝั่ง: ยังไม่เลือก (ซ้าย) / เลือกแล้ว (ขวา) ===== */
    let transferPlayers = [];

    function pcEscape(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    // แถวหนึ่งคน — checkbox ใช้ class .player-checkbox เดิม จะได้เข้า handler ที่มีอยู่แล้ว
    // (อัปเดต selectionsByTeam + autosave) ไม่ต้องเขียนตรรกะการเลือกซ้ำ
    //
    // payload จาก getAcademyTeamPlayersDataTable ผสมกันสองแบบ:
    //   image, player_sub_position → HTML สำเร็จรูปจาก server (ใส่ตรง ๆ ห้าม escape ซ้ำ)
    //   shirt_number, full_name, nick_name → ข้อความล้วน (ต้อง escape)
    function pcItemHtml(p, checked) {
        const shirt = String(p.shirt_number ?? '').trim();

        return '<label class="pc-item">'
             + '<input type="checkbox" class="form-check-input player-checkbox" value="' + pcEscape(p.id) + '"'
             + (checked ? ' checked' : '') + '>'
             //  ป้ายเบอร์กดแก้ได้เลย — role=button + stopPropagation กัน label ไปติ๊ก checkbox
             + '<span class="pc-item__no pc-item__no--edit" role="button" tabindex="0"'
             +   ' data-edit-shirt="' + pcEscape(p.id) + '"'
             +   ' data-shirt="' + pcEscape(shirt) + '"'
             +   ' data-player-name="' + pcEscape(p.full_name || '') + '"'
             +   ' title="' + pcEscape(langPcEditShirt) + '">'
             +   (shirt !== '' ? pcEscape(shirt) : '<i class="ti ti-pencil"></i>')
             + '</span>'
             + (p.image || '')
             //  ใส่ทุกบรรทัดเสมอแม้ค่าว่าง เพื่อให้การ์ดทุกใบสูงเท่ากันทั้งสองฝั่ง
             + '<span class="pc-item__name">' + pcEscape(p.full_name || '') + '</span>'
             + '<span class="pc-item__nick">' + pcEscape(p.nick_name || '') + '</span>'
             + '<span class="pc-item__pos">' + (p.player_sub_position || '') + '</span>'
             + '</label>';
    }

    // ค้นหาจากชื่อ ชื่อเล่น หรือเบอร์เสื้อ
    function pcMatches(p, q) {
        if (q === '') return true;
        return (String(p.full_name || '') + ' ' + String(p.nick_name || '') + ' ' +
                String(p.shirt_number || '')).toLowerCase().includes(q);
    }

    function renderTransferPanes() {
        const selected = selectionsByTeam[currentTeamId] || new Set();
        const qLeft  = String($('#pcSearchAvailable').val() || '').trim().toLowerCase();
        const qRight = String($('#pcSearchSelected').val()  || '').trim().toLowerCase();

        const available = [];
        const chosen    = [];
        transferPlayers.forEach(function (p) {
            (selected.has(String(p.id)) ? chosen : available).push(p);
        });

        // ตัวเลขบนป้ายหัวนับจากทั้งหมด ไม่ใช่เฉพาะที่ผ่านการค้นหา
        $('#availableCount').text(available.length);
        $('#selectedCount').text(chosen.length);

        const availableShown = available.filter(p => pcMatches(p, qLeft));
        const chosenShown    = chosen.filter(p => pcMatches(p, qRight));

        $('#availableList').html(
            availableShown.length
                ? availableShown.map(p => pcItemHtml(p, false)).join('')
                : '<div class="pc-pane__empty">' + pcEscape(qLeft ? langPcNoSearchResult : langNoPlayerData) + '</div>'
        );

        $('#selectedList').html(
            chosenShown.length
                ? chosenShown.map(p => pcItemHtml(p, true)).join('')
                : '<div class="pc-pane__empty">'
                  + pcEscape(chosen.length && qRight ? langPcNoSearchResult : langPcNoneSelected)
                  + '</div>'
        );

        updateSubmitWarning();
        applySourceEligibilityMarks();
        applyReadOnlyForEndedTour();
    }

    // ทำเครื่องหมายว่าทัวร์นาเมนต์นี้ส่งรายชื่อแล้ว โดยไม่ต้องรีโหลดหน้า
    // ต้องแก้ทั้งสอง select เพราะคนละ dropdown กัน แต่สะท้อนสถานะเดียวกัน
    function markTournamentSubmitted(tourId) {
        if (!tourId) return;

        $('#tournament_filter option').filter(function () {
            return String($(this).val()) === String(tourId);
        }).attr('data-player-list-status', '1');

        $('#submit_tournament_select option').filter(function () {
            return String($(this).data('tour-id')) === String(tourId);
        }).attr('data-status', '1');

        // จำรายชื่อ ณ ตอนที่ส่ง ถ้าแก้เพิ่ม/ลดทีหลังจะได้เตือนให้ส่งใหม่
        submittedSnapshot[String(tourId)] = selectionKey();

        updateSubmitWarning();
        // ให้ modal คำนวณข้อความ/ปุ่มใหม่ตามสถานะที่เปลี่ยน
        $('#submit_tournament_select').trigger('change');
    }

    // รายชื่อที่ "ส่งไปแล้ว" ของแต่ละทัวร์ เก็บเป็น string เรียงแล้วเพื่อเทียบง่าย
    // ใช้ดูว่าหลังส่งไปแล้วมีการแก้รายชื่อหรือยัง
    const submittedSnapshot = {};

    function selectionKey() {
        return Array.from(selectionsByTeam[currentTeamId] || new Set())
            .map(String).sort().join(',');
    }

    // 3 สถานะ: ยังไม่ส่ง (เหลือง) / ส่งแล้ว (เขียว) / ส่งแล้วแต่แก้ทีหลัง (เหลือง คนละข้อความ)
    function updateSubmitWarning() {
        const opt       = $('#tournament_filter').find('option:selected');
        const tourId    = String(opt.val() || '');
        const submitted = parseInt(opt.attr('data-player-list-status') || '0', 10) === 1;
        const hasSelection = (selectionsByTeam[currentTeamId] || new Set()).size > 0;

        const $warn = $('#pcSubmitWarning');
        const $ok   = $('#pcSubmittedOk');

        // ยังไม่เลือกทัวร์ หรือยังไม่เลือกใครเลย → ไม่ต้องบอกอะไร
        if (!tourId || !hasSelection) {
            $warn.addClass('d-none');
            $ok.addClass('d-none');
            return;
        }

        if (!submitted) {
            $('#pcWarnTitle').text(langPcNotSubmittedTitle);
            $('#pcWarnHint').text(langPcNotSubmittedHint);
            $warn.removeClass('d-none');
            $ok.addClass('d-none');
            return;
        }

        // ส่งแล้ว — ครั้งแรกที่เห็นทัวร์นี้ ให้ยึดรายชื่อปัจจุบันเป็นตัวตั้งต้น
        // (เปิดหน้ามาใหม่หลังเคยส่งไปแล้ว จะได้ไม่ขึ้นว่า "มีการเปลี่ยนแปลง" ทั้งที่ยังไม่ได้แตะอะไร)
        if (submittedSnapshot[tourId] === undefined) {
            submittedSnapshot[tourId] = selectionKey();
        }

        if (submittedSnapshot[tourId] !== selectionKey()) {
            $('#pcWarnTitle').text(langPcChangedTitle);
            $('#pcWarnHint').text(langPcChangedHint);
            $warn.removeClass('d-none');
            $ok.addClass('d-none');
        } else {
            $warn.addClass('d-none');
            $ok.removeClass('d-none');
        }
    }

    // วาดใหม่ทุกครั้งที่ติ๊ก เพื่อให้คนที่เลือกย้ายไปฝั่งขวา (และติ๊กออกแล้วกลับมาซ้าย)
    //
    // ต้อง defer ด้วย setTimeout 0 เพราะ handler ที่อัปเดต selectionsByTeam ถูก bind
    // ไว้ใน $(document).ready() ซึ่งทำงาน "ทีหลัง" handler ตัวนี้ (bind ตอนสคริปต์รัน)
    // ถ้าวาดทันทีจะอ่าน selection ชุดเก่า → ป้ายสถานะช้าไปหนึ่งจังหวะ
    // (อาการเดิม: กดคนแรกไม่ขึ้นสีเหลือง ต้องกดคนที่สองถึงขึ้น)
    $(document).on('change', '.player-checkbox', function () {
        if (isCurrentTourEnded()) {
            // ทัวร์จบแล้ว แก้รายชื่อไม่ได้ — คืนค่าเดิมแล้วบอกเหตุผล
            $(this).prop('checked', !this.checked).prop('disabled', true);
            Swal.fire({ icon: 'info', title: langEnded, text: langTourEndedReadonly, confirmButtonColor: '#16478D' });
            return false;
        }
        setTimeout(renderTransferPanes, 0);
    });

    $(document).on('input', '#pcSearchAvailable, #pcSearchSelected', function () {
        renderTransferPanes();
    });

    $(document).on('click', '#pcSelectAllAvailable', function () {
        if (!selectionsByTeam[currentTeamId]) selectionsByTeam[currentTeamId] = new Set();
        // เลือกเฉพาะที่มองเห็นอยู่ (เคารพคำค้นหา)
        $('#availableList .player-checkbox').each(function () {
            selectionsByTeam[currentTeamId].add(String($(this).val()));
        });
        saveSelection(true);
        renderTransferPanes();
    });

    $(document).on('click', '#pcClearSelected', function () {
        if (!selectionsByTeam[currentTeamId]) return;
        selectionsByTeam[currentTeamId].clear();
        saveSelection(true);
        renderTransferPanes();
    });

    $(document).on('change', '#tournament_filter', function () {
        updateSubmitWarning();
    });

    // แก้เบอร์เสื้อจากการ์ดได้เลย ไม่ต้องออกไปหน้าจัดการนักเตะ
    $(document).on('click keydown', '[data-edit-shirt]', function (e) {
        if (e.type === 'keydown' && e.key !== 'Enter' && e.key !== ' ') return;
        // อยู่ใน <label> ถ้าปล่อยผ่านจะไปติ๊ก checkbox ของการ์ด
        e.preventDefault();
        e.stopPropagation();

        const $tag     = $(this);
        const playerId = $tag.data('edit-shirt');
        const current  = String($tag.attr('data-shirt') || '');

        Swal.fire({
            title: langPcEditShirt,
            text: $tag.attr('data-player-name') || '',
            input: 'text',
            inputValue: current,
            inputAttributes: { maxlength: 10, autocapitalize: 'off', inputmode: 'numeric' },
            showCancelButton: true,
            confirmButtonText: langSave,
            cancelButtonText: langCancel,
            confirmButtonColor: '#16478D'
        }).then(function (res) {
            if (!res.isConfirmed) return;

            const shirt = String(res.value ?? '').trim();
            if (shirt === current) return;   // ไม่เปลี่ยนก็ไม่ต้องยิง

            $.post('<?= base_url('academy/player-shirt') ?>/' + ACADEMY_ID + '/' + currentTeamId,
                { player_id: playerId, shirt_number: shirt },
                function (r) {
                    if (r && r.status === 'success') {
                        // อัปเดตข้อมูลในหน่วยความจำแล้ววาดใหม่ ไม่ต้องโหลดตารางทั้งชุด
                        const p = transferPlayers.find(x => String(x.id) === String(playerId));
                        if (p) p.shirt_number = shirt;
                        renderTransferPanes();

                        Swal.fire({
                            toast: true, position: 'top-end', icon: 'success',
                            title: r.message, showConfirmButton: false, timer: 2000
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: langErrorLabel,
                            text: (r && r.message) || langErrorLoadingData
                        });
                    }
                }, 'json')
             .fail(function () {
                Swal.fire({ icon: 'error', title: langErrorLabel, text: langErrorLoadingData });
             });
        });
    });

    function renderPlayerCards(players) {
        let container = $('#playerCardsContainer');
        container.empty();

        if (!players || players.length === 0) {
            container.html('<div class="col-12 text-center py-5"><p class="text-muted">' + langNoPlayerDataCard + '</p></div>');
            return;
        }

        const baseUrl = '<?= base_url() ?>';

        players.forEach(function(player) {
            let path = player.image_path || 'public/img/img_profile.jpg';
            // Ensure we don't have double slashes when joining
            let cleanBase = baseUrl.replace(/\/$/, "");
            let cleanPath = path.startsWith('/') ? path.substring(1) : path;
            let fullImgSrc = cleanBase + '/' + cleanPath;

            // If path is already a full URL (external image), use it as is
            if (path.startsWith('http://') || path.startsWith('https://')) {
                fullImgSrc = path;
            }

            let position = player.player_sub_position || '-';
            let shirtNumber = player.shirt_number || '-';

            // Check state
            let currentSet = selectionsByTeam[currentTeamId] || new Set();
            let checked = currentSet.has(String(player.id)) ? 'checked' : '';

            let cardHtml = `
                <div class="col-lg-3 col-md-4 col-sm-6 col-6">
                    <div class="player-card">
                        <input type="checkbox" value="${player.id}" class="player-checkbox player-card-checkbox" ${checked}>
                        <img src="${fullImgSrc}" alt="${player.full_name}">
                        <div class="player-position-badge">${position}</div>
                        <h5>${player.full_name}</h5>
                        <span class="player-shirt-number">${shirtNumber}</span>
                        <p class="text-muted">${player.nick_name || ''}</p>
                    </div>
                </div>
            `;
            container.append(cardHtml);
        });
        updateSelectAllButtonState();
        applySourceEligibilityMarks();
    }

    function selectAllCheckboxes() {
        let visibleCheckboxes = $(".player-checkbox");
        let allVisibleChecked = true;

        // Check if all visible are checked
        visibleCheckboxes.each(function() {
            if (!$(this).prop('checked')) {
                allVisibleChecked = false;
                return false; // break
            }
        });

        let newState = !allVisibleChecked;

        visibleCheckboxes.prop("checked", newState);

        // Update global set for current team
        if (!selectionsByTeam[currentTeamId]) {
            selectionsByTeam[currentTeamId] = new Set();
        }

        visibleCheckboxes.each(function() {
            let val = String($(this).val());
            if (newState) {
                selectionsByTeam[currentTeamId].add(val);
            } else {
                selectionsByTeam[currentTeamId].delete(val);
            }
        });

        updateSelectAllButtonState();
        saveSelection(true); // Auto-save
    }

    function updateSelectAllButtonState() {
        let visibleCheckboxes = $(".player-checkbox");

        if (visibleCheckboxes.length === 0) return;

        let checkedVisibleCount = 0;
        visibleCheckboxes.each(function() {
            if ($(this).prop('checked')) checkedVisibleCount++;
        });

        let allChecked = visibleCheckboxes.length > 0 && visibleCheckboxes.length === checkedVisibleCount;

        let btn = $("#selectAllBtn");
        if (allChecked) {
            btn.removeClass("btn-outline-secondary").addClass("btn-secondary text-white");
            btn.html('<i class="ti ti-check me-1"></i> ' + langDeselectAll);
        } else {
            btn.removeClass("btn-secondary text-white").addClass("btn-outline-secondary");
            btn.html('<i class="ti ti-check me-1"></i> ' + langSelectAll);
        }
    }

    function createPDF() {
        let tournamentSelect = $("#tournament_select");
        let selectedOption = tournamentSelect.find("option:selected");

        if (!selectedOption.val()) {
            Swal.fire({
                icon: 'warning',
                title: langPleaseSelectTournament,
                text: langSelectTournamentFromList,
                confirmButtonColor: '#16478D'
            });
            return;
        }

        // ตรวจว่าทัวร์นาเมนต์นี้มีรายชื่อที่ส่งแล้วหรือยัง
        let listStatus = parseInt(selectedOption.data('player-list-status') || 0, 10);
        if (listStatus !== 1) {
            Swal.fire({
                icon: 'warning',
                title: '<?= lang('Common.not_submitted') ?>',
                text: '<?= lang('Common.export_requires_submission') ?>',
                confirmButtonColor: '#16478D',
                confirmButtonText: langOk
            });
            return;
        }

        // ใช้ tour_team_id สำหรับโหลดรายชื่อจาก tournament_submitted_players
        let selectedTeamId = selectedOption.data("team-id") || currentTeamId;
        let selectedTourId = selectedOption.val() || '';
        let selectedTourTeamId = selectedOption.data("tour-team-id") || '';

        if (!selectedTourTeamId) {
            Swal.fire({
                icon: 'error',
                title: langErrorLabel,
                text: '<?= lang('Common.tour_team_not_found') ?>',
                confirmButtonColor: '#16478D'
            });
            return;
        }

        let url = "<?= base_url('academy/export-player-chart') ?>";
        url += "?id=" + selectedTeamId;
        url += "&tour_id=" + selectedTourId;
        url += "&tour_team_id=" + selectedTourTeamId;

        let title = selectedOption.data("name");
        let generate = $("#generation_input").val();
        if (title)    url += "&title=" + encodeURIComponent(title);
        if (generate) url += "&gen=" + encodeURIComponent(generate);

        // Add selected coaches to URL (โค้ชยังคงใช้จาก selection ปัจจุบัน)
        let currentCoachArr = coachSelectionsByTeam[currentTeamId] || [];
        let selectedCoaches = [...currentCoachArr];
        if (selectedCoaches.length > 0) {
            url += "&coaches=" + selectedCoaches.join(',');
        }

        window.open(url, '_blank');
    }

    function saveSelection(silent = false) {
        if (!currentTeamId) {
            if (!silent) {
                Swal.fire({
                    icon: 'error',
                    title: langNoTeamData,
                    text: langSelectTeamFirst,
                    confirmButtonColor: '#16478D'
                });
            }
            return;
        }

        let currentSet = selectionsByTeam[currentTeamId] || new Set();
        let selectedPlayers = Array.from(currentSet);

        // Always save, even if empty (to clear selection)

        $.ajax({
            url: '<?= base_url('academy/save-chart-selection') ?>',
            type: 'POST',
            data: {
                team_id: currentTeamId,
                player_ids: selectedPlayers,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    // silent = auto-save ตอนติ๊กเลือก ไม่ต้องเด้งอะไรเลย
                    // (เดิมเด้ง toast "บันทึกแล้ว" ทุกครั้งที่ติ๊ก กวนสายตาและไม่ได้สื่ออะไร)
                    // ถ้าบันทึกไม่สำเร็จยังเด้งเตือนเหมือนเดิม
                    if (!silent) {
                        Swal.fire({
                            icon: 'success',
                            title: langSaveSuccess,
                            text: langSavePlayerSelection,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: langErrorLabel,
                        text: response.message || langCannotSave,
                        confirmButtonColor: '#16478D'
                    });
                }

            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: langErrorLabel,
                    text: langConnectionErrorServer,
                    confirmButtonColor: '#16478D'
                });
            }
        });
    }

    function loadSavedSelection(teamId) {
        if (!teamId) return;

        $.ajax({
            url: '<?= base_url('academy/get-chart-selection') ?>/' + teamId,
            type: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    let savedIds = response.data; // Expecting array of IDs (strings or ints)
                    // Filter out stale IDs (players no longer in team)
                    let validPlayerIds = new Set(allPlayersData.map(p => String(p.id)));
                    let filteredIds = savedIds.map(String).filter(id => validPlayerIds.has(id));
                    selectionsByTeam[teamId] = new Set(filteredIds);

                    // Refresh view (เฉพาะเมื่อเลือกทัวร์แล้ว — กัน race กับการโหลดหน้า)
                    if (tournamentSelected) {
                        if ($.fn.DataTable.isDataTable('#table')) {
                            $('#table').DataTable().ajax.reload();
                        }
                        if (currentViewMode === 'card') {
                            loadPlayerCards();
                        }
                    }
                } else {
                    // No saved data or error, just keep empty set
                    reloadViews();
                }
                syncStepIndicator();
            },
            error: function() {
                console.error("Failed to load saved selection");
                reloadViews();
                syncStepIndicator();
            }
        });
    }

    // sync ตัวเลขใน step indicator + hero
    function syncStepIndicator() {
        try {
            const playerCnt = (selectionsByTeam[currentTeamId] || new Set()).size;
            $('#stepPlayerHint').text(playerCnt + ' ' + (typeof langPlayerCountUnit !== 'undefined' ? langPlayerCountUnit : ''));

            const coachCnt = (coachSelectionsByTeam[currentTeamId] || []).length;
            $('#stepCoachHint').text(coachCnt + '/' + MAX_COACHES);

            // step state: active = ขั้นที่มีค่าแล้ว (มีอย่างน้อย 1)
            const $steps = $('.pc-step');
            $steps.removeClass('pc-step--active pc-step--done');
            // step 1 (player)
            if (playerCnt > 0) $steps.eq(0).addClass('pc-step--done');
            // step 2 (coach)
            if (coachCnt > 0)  $steps.eq(1).addClass('pc-step--done');
            // step 3 (ชุดแข่ง) - เสร็จเมื่อเลือกสี หรืออัปโหลดรูปชุด อย่างน้อย 1 รายการ
            const jerseyDone = $('#kitForm .jk-color-input').filter(function(){ return $(this).val(); }).length > 0
                || $('#kitForm .jk-img-preview').filter(function(){ return $(this).attr('src'); }).length > 0;
            if (jerseyDone) $steps.eq(2).addClass('pc-step--done');
            // active = ขั้นถัดไปที่ยังไม่ done
            const idx = $steps.filter('.pc-step--done').length;
            const activeIdx = Math.min(idx, $steps.length - 1);
            $steps.eq(activeIdx).addClass('pc-step--active');
        } catch (e) { /* silent */ }
    }

    function reloadViews() {
        if (!tournamentSelected) return; // ยังไม่เลือกทัวร์ → ไม่โหลดนักเตะ
        if ($.fn.DataTable.isDataTable('#table')) {
            $('#table').DataTable().ajax.reload();
        }
        if (currentViewMode === 'card') {
            loadPlayerCards();
        }
    }

    // Submit Player List Modal Event Handler
    $('#submit_tournament_select').on('change', function() {
        let selectedOption = $(this).find('option:selected');
        let status = selectedOption.data('status');
        let maxPlayerCount = selectedOption.data('player-count');

        // Get currently selected player count
        let currentSet = selectionsByTeam[currentTeamId] || new Set();
        let selectedCount = currentSet.size;

        // Show player count info
        if (selectedOption.val() && maxPlayerCount > 0) {
            let infoClass = selectedCount > maxPlayerCount ? 'alert-danger' : 'alert-primary';
            let warningText = selectedCount > maxPlayerCount ? ' <strong>(' + langOverLimit + ')</strong>' : '';
            $('#playerCountInfo').removeClass('d-none alert-primary alert-danger').addClass(infoClass);
            $('#playerCountInfoText').html(`${langSelectedCount}: <strong>${selectedCount}</strong> ${langPlayerCountUnit} / ${langMaxCount}: <strong>${maxPlayerCount}</strong> ${langPlayerCountUnit}${warningText}`);
        } else if (selectedOption.val()) {
            $('#playerCountInfo').removeClass('d-none alert-danger').addClass('alert-primary');
            $('#playerCountInfoText').html(`${langSelectedCount}: <strong>${selectedCount}</strong> ${langPlayerCountUnit}`);
        } else {
            $('#playerCountInfo').addClass('d-none');
        }

        if (status == 1) {
            $('#submitStatusInfo').html('<i class="fa fa-info-circle me-2"></i>' + langAlreadySubmittedEdit).removeClass('d-none').removeClass('alert-info').addClass('alert-warning');
            $('#submitPlayerListBtn').html('<i class="fa fa-edit me-2"></i>' + langEditList);
        } else {
            $('#submitStatusInfo').addClass('d-none');
            $('#submitPlayerListBtn').html('<i class="fa fa-paper-plane me-2"></i>' + langConfirmSubmitBtn);
        }

        // Source-tournament blacklist: warn about selected players blocked for this tournament
        $('#sourceCheckInfo').addClass('d-none');
        let submitTourId = selectedOption.data('tour-id');
        if (selectedOption.val() && submitTourId) {
            fetchSourceEligibility(currentTeamId, submitTourId, function(response) {
                if (!response || !response.enabled) return;
                let blockedSet = new Set((response.blocked_ids || []).map(String));
                let blockedDetails = response.blocked_details || {};
                let ineligibleSelected = [...currentSet].filter(id => blockedSet.has(String(id)));
                if (ineligibleSelected.length === 0) return;
                let items = ineligibleSelected.map(function(id) {
                    let p = allPlayersData.find(pp => String(pp.id) === String(id));
                    let name = p ? (p.full_name || ((p.first_name || '') + ' ' + (p.last_name || '')).trim()) : ('#' + id);
                    return { name: name, entries: blockedDetails[String(id)] || [] };
                });
                $('#sourceCheckInfoText').html(renderSourceCheckCard(items));
                $('#sourceCheckInfo').removeClass('d-none');
            });
        }
    });

    function submitPlayerList() {
        let tourTeamId = $('#submit_tournament_select').val();

        if (!tourTeamId) {
            Swal.fire({
                icon: 'warning',
                title: langPleaseSelectTournament,
                text: langSelectTournamentToSubmitList,
                confirmButtonColor: '#16478D'
            });
            return;
        }

        Swal.fire({
            title: langConfirmSubmitTitle,
            text: langSubmitIrreversible,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langConfirm,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: '<?= base_url('academy/submit-player-list') ?>',
                    type: 'POST',
                    data: {
                        tour_team_id: tourTeamId,
                        team_id: currentTeamId,
                        <?= csrf_token() ?>: '<?= csrf_hash() ?>'
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            // อัปเดตสถานะในหน้าให้ตรงกับที่เพิ่งส่งไป
                            // ไม่งั้นกล่องเตือน "ยังไม่ได้ยืนยันการส่งรายชื่อ" ค้างอยู่จนกว่าจะรีเฟรช
                            // เพราะ updateSubmitWarning() อ่านจาก data-player-list-status ของ option
                            markTournamentSubmitted($('#submit_tournament_select')
                                .find('option:selected').data('tour-id'));

                            Swal.fire({
                                icon: 'success',
                                title: langSuccess,
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                        } else {
                            // Source-tournament blacklist: players who cannot be submitted (blocked by prior submission)
                            if (response.ineligible_players && response.ineligible_players.length > 0) {
                                let inelItems = response.ineligible_players.map(function(ip) {
                                    let displayName = ip.player_name;
                                    if (ip.nick_name) displayName += ' (' + ip.nick_name + ')';
                                    if (ip.shirt_number) displayName += ' - ' + ip.shirt_number;
                                    return { name: displayName, entries: ip.history || [] };
                                });

                                let inelHtml = '<div class="src-check text-start">'
                                    + '<div class="src-check__block">'
                                    +   '<span class="src-check__label">' + escHtml(langSourceCheckIneligibleLabel) + ' (' + inelItems.length + '):</span>'
                                    +   '<div class="mt-1" style="max-height:260px;overflow-y:auto;">' + renderBlockedPlayerRows(inelItems) + '</div>'
                                    + '</div>'
                                    + '</div>';

                                Swal.fire({
                                    icon: 'warning',
                                    title: langSourceNotEligibleTitle,
                                    html: inelHtml,
                                    confirmButtonColor: '#e53935',
                                    confirmButtonText: langOk
                                });
                                return;
                            }
                            // Multi-academy duplicate check: player already submitted via another team in same tournament
                            if (response.duplicate_players && response.duplicate_players.length > 0) {
                                let dupHtml = '<div class="text-start">';
                                dupHtml += '<p class="text-muted mb-2">นักเตะเหล่านี้ถูกส่งรายชื่อในทัวร์นาเมนต์เดียวกันผ่านทีมอื่นแล้ว กรุณาตรวจสอบรายชื่อก่อน submit ใหม่</p>';
                                dupHtml += '<ul class="list-unstyled mb-0">';
                                response.duplicate_players.forEach(function(dp) {
                                    let displayName = dp.player_name;
                                    if (dp.nick_name) displayName += ' (' + dp.nick_name + ')';
                                    dupHtml += '<li class="mb-3 p-2" style="background:#fdecea; border-radius:8px; border-left:4px solid #e53935;">';
                                    dupHtml += '<strong><i class="fa fa-user me-1 text-danger"></i>' + displayName + '</strong>';
                                    dupHtml += '<br><small class="text-muted ms-3"><i class="fa fa-shield-alt me-1"></i>ทีมที่ลงทะเบียนแล้ว: <strong>' + dp.team_name + '</strong></small>';
                                    if (dp.academy_name && dp.academy_name !== dp.team_name) {
                                        dupHtml += '<br><small class="text-muted ms-3"><i class="fa fa-school me-1"></i>' + langDuplicateAcademy + ': <strong>' + dp.academy_name + '</strong></small>';
                                    }
                                    dupHtml += '</li>';
                                });
                                dupHtml += '</ul></div>';

                                Swal.fire({
                                    icon: 'warning',
                                    title: '&#9888; ลงทะเบียนซ้ำ',
                                    html: dupHtml,
                                    confirmButtonColor: '#e53935',
                                    confirmButtonText: 'รับทราบ'
                                });
                            } else if (response.duplicates && response.duplicates.length > 0) {
                                let duplicateHtml = '<div class="text-start">';
                                duplicateHtml += '<ul class="list-unstyled mb-0">';
                                response.duplicates.forEach(function(dup) {
                                    duplicateHtml += '<li class="mb-3 p-2" style="background:#fff8e1; border-radius:8px; border-left:4px solid #f39c12;">';
                                    duplicateHtml += '<strong><i class="fa fa-user me-1"></i>' + dup.player_name + '</strong>';
                                    duplicateHtml += '<br><small class="text-muted ms-3"><i class="fa fa-shield-alt me-1"></i>' + langDuplicateClaimedBy + ': <strong>' + dup.claimed_by_team + '</strong></small>';
                                    if (dup.claimed_by_academy && dup.claimed_by_academy !== dup.claimed_by_team) {
                                        duplicateHtml += '<br><small class="text-muted ms-3"><i class="fa fa-school me-1"></i>' + langDuplicateAcademy + ': <strong>' + dup.claimed_by_academy + '</strong></small>';
                                    }
                                    if (dup.tournament_name) {
                                        duplicateHtml += '<br><small class="text-muted ms-3"><i class="fa fa-trophy me-1"></i>' + langDuplicateTournament + ': <strong>' + dup.tournament_name + '</strong></small>';
                                    }
                                    if (dup.submitted_at) {
                                        duplicateHtml += '<br><small class="text-muted ms-3"><i class="fa fa-clock me-1"></i>' + langDuplicateSubmittedAt + ': <strong>' + dup.submitted_at + '</strong></small>';
                                    }
                                    duplicateHtml += '</li>';
                                });
                                duplicateHtml += '</ul></div>';

                                Swal.fire({
                                    icon: 'warning',
                                    title: langDuplicatePlayerTitle,
                                    html: duplicateHtml,
                                    confirmButtonColor: '#16478D'
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: langError,
                                    text: response.message,
                                    confirmButtonColor: '#16478D'
                                });
                            }
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: langError,
                            text: langCannotConnectServer,
                            confirmButtonColor: '#16478D'
                        });
                    }
                });
            }
        });
    }

    function viewSubmittedPlayers(tourTeamId, tournamentName) {
        // Show modal
        var modal = new bootstrap.Modal(document.getElementById('view-players-modal'));
        modal.show();

        // Set tournament name
        document.getElementById('viewPlayersTournamentName').textContent = langTournamentListLabel + ': ' + tournamentName;

        // Show loading, hide content
        document.getElementById('viewPlayersLoading').style.display = 'block';
        document.getElementById('viewPlayersContent').classList.add('d-none');

        // Fetch submitted players with details for this tournament team
        $.ajax({
            url: '<?= base_url('academy/get-submitted-players') ?>/' + tourTeamId,
            type: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data && response.data.length > 0) {
                    let players = response.data;

                    let tbody = document.getElementById('viewPlayersTableBody');
                    tbody.innerHTML = '';

                    players.forEach((player, index) => {
                        let imgSrc = player.image_path || 'public/img/img_profile.jpg';
                        if (!imgSrc.startsWith('http')) {
                            imgSrc = '<?= base_url() ?>/' + imgSrc;
                        }

                        let fullName = (player.first_name || '') + ' ' + (player.last_name || '');

                        let row = `
                            <tr>
                                <td class="ps-3 text-center text-muted">${index + 1}</td>
                                <td class="text-center">
                                    <img src="${imgSrc}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.src='<?= base_url() ?>/public/img/img_profile.jpg'">
                                </td>
                                <td class="text-center fw-bold">${player.shirt_number || '-'}</td>
                                <td class="fw-medium">${fullName.trim() || '-'}</td>
                            </tr>
                        `;
                        tbody.innerHTML += row;
                    });
                    document.getElementById('viewPlayersCount').textContent = langPlayerCount + ': ' + players.length + ' ' + langPlayerCountUnit;

                    document.getElementById('viewPlayersLoading').style.display = 'none';
                    document.getElementById('viewPlayersContent').classList.remove('d-none');
                } else {
                    document.getElementById('viewPlayersTableBody').innerHTML = '<tr><td colspan="4" class="text-center py-4 text-muted">' + langNoSubmittedPlayer + '</td></tr>';
                    document.getElementById('viewPlayersCount').textContent = '';
                    document.getElementById('viewPlayersLoading').style.display = 'none';
                    document.getElementById('viewPlayersContent').classList.remove('d-none');
                }
            },
            error: function() {
                document.getElementById('viewPlayersTableBody').innerHTML = '<tr><td colspan="4" class="text-center py-4 text-danger">' + langConnectionErrorShort + '</td></tr>';
                document.getElementById('viewPlayersLoading').style.display = 'none';
                document.getElementById('viewPlayersContent').classList.remove('d-none');
            }
        });
    }

    // ========== Coach Selection Functions ==========

    function loadSavedCoachSelection(teamId) {
        if (!teamId) return;

        $.ajax({
            url: '<?= base_url('academy/get-coach-chart-selection') ?>/' + teamId,
            type: 'GET',
            success: function(response) {
                if (response.status === 'success' && response.data) {
                    let savedIds = response.data;
                    coachSelectionsByTeam[teamId] = savedIds.map(String);

                    // Update checkboxes
                    updateCoachCheckboxes();
                    updateSelectAllCoachesButtonState();
                    reorderCoachRows();
                }
                if (typeof syncStepIndicator === 'function') syncStepIndicator();
            },
            error: function() {
                console.error("Failed to load saved coach selection");
                if (typeof syncStepIndicator === 'function') syncStepIndicator();
            }
        });
    }

    function updateCoachCheckboxes() {
        let currentArr = coachSelectionsByTeam[currentTeamId] || [];
        $('.coach-checkbox').each(function() {
            let val = String($(this).val());
            $(this).prop('checked', currentArr.includes(val));
        });
    }

    function saveCoachSelection(silent = false) {
        if (!currentTeamId) {
            if (!silent) {
                Swal.fire({
                    icon: 'error',
                    title: langNoTeamData,
                    text: langSelectTeamFirst,
                    confirmButtonColor: '#16478D'
                });
            }
            return;
        }

        let currentArr = coachSelectionsByTeam[currentTeamId] || [];
        let selectedCoaches = [...currentArr];

        $.ajax({
            url: '<?= base_url('academy/save-coach-chart-selection') ?>',
            type: 'POST',
            data: {
                team_id: currentTeamId,
                coach_ids: selectedCoaches,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (!silent) {
                        Swal.fire({
                            icon: 'success',
                            title: langSaveSuccess,
                            text: langSaveCoachSelection,
                            timer: 1500,
                            showConfirmButton: false
                        });
                    }
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: langErrorLabel,
                        text: response.message || langCannotSave,
                        confirmButtonColor: '#16478D'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: langErrorLabel,
                    text: langConnectionErrorServer,
                    confirmButtonColor: '#16478D'
                });
            }
        });
    }

    function selectAllCoaches() {
        let visibleCheckboxes = $(".coach-checkbox");
        let allVisibleChecked = true;

        // Check if all visible are checked
        visibleCheckboxes.each(function() {
            if (!$(this).prop('checked')) {
                allVisibleChecked = false;
                return false; // break
            }
        });

        let newState = !allVisibleChecked;

        // Update global array for current team
        if (!coachSelectionsByTeam[currentTeamId]) {
            coachSelectionsByTeam[currentTeamId] = [];
        }

        if (newState) {
            // Select up to MAX_COACHES
            let count = 0;
            visibleCheckboxes.each(function() {
                let val = String($(this).val());
                if (count < MAX_COACHES) {
                    $(this).prop("checked", true);
                    if (!coachSelectionsByTeam[currentTeamId].includes(val)) {
                        coachSelectionsByTeam[currentTeamId].push(val);
                    }
                    count++;
                } else {
                    $(this).prop("checked", false);
                }
            });
            if (visibleCheckboxes.length > MAX_COACHES) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 2000,
                    timerProgressBar: false
                });
                Toast.fire({
                    icon: 'info',
                    title: langMaxCoachesSelect.replace('%d', MAX_COACHES)
                });
            }
        } else {
            // Deselect all
            visibleCheckboxes.prop("checked", false);
            coachSelectionsByTeam[currentTeamId] = [];
        }

        updateSelectAllCoachesButtonState();
        reorderCoachRows();
        saveCoachSelection(true); // Auto-save
    }

    function updateSelectAllCoachesButtonState() {
        let visibleCheckboxes = $(".coach-checkbox");

        if (visibleCheckboxes.length === 0) {
            // Hide button if no coaches
            $('#selectAllCoachesBtn').hide();
            $('#coachCountBadge').text('0/' + MAX_COACHES);
            return;
        } else {
            $('#selectAllCoachesBtn').show();
        }

        let checkedVisibleCount = 0;
        visibleCheckboxes.each(function() {
            if ($(this).prop('checked')) checkedVisibleCount++;
        });

        // Update count badge
        let badge = $('#coachCountBadge');
        badge.text(checkedVisibleCount + '/' + MAX_COACHES);
        if (checkedVisibleCount >= MAX_COACHES) {
            badge.removeClass('bg-secondary bg-success').addClass('bg-danger');
        } else if (checkedVisibleCount > 0) {
            badge.removeClass('bg-secondary bg-danger').addClass('bg-success');
        } else {
            badge.removeClass('bg-success bg-danger').addClass('bg-secondary');
        }
        // sync step indicator
        $('#stepCoachHint').text(checkedVisibleCount + '/' + MAX_COACHES);

        let allChecked = visibleCheckboxes.length > 0 && checkedVisibleCount >= Math.min(visibleCheckboxes.length, MAX_COACHES);

        // Update header checkbox
        $('#checkAllCoachesHeader').prop('checked', allChecked);

        // Update button state
        let btn = $("#selectAllCoachesBtn");
        if (allChecked) {
            btn.removeClass("btn-outline-secondary").addClass("btn-secondary text-white");
            btn.html('<i class="ti ti-check me-1"></i> ' + langDeselectAll);
        } else {
            btn.removeClass("btn-secondary text-white").addClass("btn-outline-secondary");
            btn.html('<i class="ti ti-check me-1"></i> ' + langSelectAll);
        }

        // Update row highlight
        $('.coach-row').each(function() {
            let checkbox = $(this).find('.coach-checkbox');
            if (checkbox.prop('checked')) {
                $(this).addClass('selected');
            } else {
                $(this).removeClass('selected');
            }
        });
    }

    function moveCoach(coachId, direction) {
        let arr = coachSelectionsByTeam[currentTeamId] || [];
        let id = String(coachId);
        let idx = arr.indexOf(id);
        if (idx === -1) return;

        if (direction === 'up' && idx > 0) {
            [arr[idx - 1], arr[idx]] = [arr[idx], arr[idx - 1]];
        } else if (direction === 'down' && idx < arr.length - 1) {
            [arr[idx], arr[idx + 1]] = [arr[idx + 1], arr[idx]];
        }
        coachSelectionsByTeam[currentTeamId] = arr;
        reorderCoachRows();
        saveCoachSelection(true);
    }

    function reorderCoachRows() {
        let arr = coachSelectionsByTeam[currentTeamId] || [];
        let tbody = $('#coachTable tbody');
        // ย้าย selected rows ขึ้นด้านบนตามลำดับ
        for (let i = arr.length - 1; i >= 0; i--) {
            let row = tbody.find('tr[data-coach-id="' + arr[i] + '"]');
            if (row.length) {
                tbody.prepend(row);
            }
        }
        updateCoachOrderButtons();
    }

    function updateCoachOrderButtons() {
        let arr = coachSelectionsByTeam[currentTeamId] || [];
        
        // Always show the action column if there are coaches
        $('.coach-order-col, .coach-order-cell').removeClass('d-none');

        // Manage buttons visibility
        $('.coach-row').each(function() {
            let row = $(this);
            let coachId = String(row.data('coach-id'));
            let btnGroup = row.find('.coach-order-cell .btn-group');
            let idx = arr.indexOf(coachId);

            // Always show the button group (it now contains the Edit button)
            btnGroup.removeClass('d-none');

            if (idx !== -1) {
                // Selected coach: show move buttons and handle their disabled state
                btnGroup.find('.btn-move-up, .btn-move-down').removeClass('d-none');
                btnGroup.find('.btn-move-up').prop('disabled', idx === 0);
                btnGroup.find('.btn-move-down').prop('disabled', idx === arr.length - 1);
            } else {
                // Not selected: hide move buttons
                btnGroup.find('.btn-move-up, .btn-move-down').addClass('d-none');
            }
        });
    }

    function loadCoachesForTeam(teamId) {
        // When team changes, we need to reload the coach cards
        // For now, just redirect to reload the page with the new team
        window.location.href = '<?= base_url('academy/player-chart/' . ($academy['id'] ?? '')) ?>/' + teamId;
    }

    // Initialize coach button state on page load
    $(document).ready(function() {
        updateSelectAllCoachesButtonState();

        // Allow clicking on row to toggle checkbox (ยกเว้นคอลัมน์ checkbox และคอลัมน์ปุ่มเรียงลำดับ)
        $(document).on('click', '.coach-row td:not(:first-child):not(.coach-order-cell)', function() {
            let row = $(this).closest('tr');
            let checkbox = row.find('.coach-checkbox');
            checkbox.prop('checked', !checkbox.prop('checked')).trigger('change');
        });

        // Init Bootstrap tooltips สำหรับปุ่ม Submit / Export
        try {
            document.querySelectorAll('[data-bs-toggle="tooltip"]').forEach(function(el) {
                new bootstrap.Tooltip(el);
            });
        } catch (e) { /* silent */ }
    });

    function editCoachRole(coachId, currentRole) {
        $('#edit_coach_id').val(coachId);
        
        let select = $('#coach_role_select');
        // Reset selection first
        select.val("");
        
        // Try matching by value (loose equality) or by text
        select.find('option').each(function() {
            if ($(this).val() == currentRole || $(this).text().trim() === String(currentRole).trim()) {
                $(this).prop('selected', true);
                return false;
            }
        });
        
        let modalEl = document.getElementById('edit-coach-role-modal');
        let modal = new bootstrap.Modal(modalEl);
        modal.show();
    }

    function saveCoachRole() {
        let coachId = $('#edit_coach_id').val();
        let role = $('#coach_role_select').val();

        if (!role) {
            Swal.fire({
                icon: 'warning',
                title: langError,
                text: '<?= lang('Common.please_select_role') ?>' || 'โปรดเลือกตำแหน่ง',
                confirmButtonColor: '#16478D'
            });
            return;
        }

        $.ajax({
            url: '<?= base_url('academy/update-coach-role') ?>',
            method: 'POST',
            data: {
                id: coachId,
                role_display_label: role,
                team_id: currentTeamId
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        icon: 'success',
                        title: langSuccess,
                        text: langSaveSuccess,
                        confirmButtonColor: '#16478D'
                    }).then(() => {
                        // Option 1: Reload page to reflect changes
                        location.reload();
                        
                        // Option 2: Update UI manually (if preferred)
                        /*
                        let row = $('tr[data-coach-id="' + coachId + '"]');
                        let roleName = $('#coach_role_select option:selected').text();
                        row.find('.badge.rounded-pill').text(roleName);
                        row.find('.btn-outline-warning').attr('onclick', "event.stopPropagation(); editCoachRole(" + coachId + ", '" + role + "')");
                        bootstrap.Modal.getInstance(document.getElementById('edit-coach-role-modal')).hide();
                        */
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: langError,
                        text: response.message || langCannotSave,
                        confirmButtonColor: '#16478D'
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: langError,
                    text: langConnectionErrorServer,
                    confirmButtonColor: '#16478D'
                });
            }
        });
    }
</script>
