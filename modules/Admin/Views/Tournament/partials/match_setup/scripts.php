<script>
    // Language variables
    const langSwapTeams = '<?= lang("Common.swap_teams") ?>';
    const langSwapConfirmText = '<?= lang("Common.swap_teams_confirm") ?>';
    const langStageNumber = '<?= lang("Common.stage_number") ?>';
    const langMatches = '<?= lang("Common.matches") ?>';
    const langCannotLoadStages = '<?= lang("Common.cannot_load_stages") ?>';
    const langConfirmDelete = '<?= lang("Common.confirm_delete_title") ?>';
    const langConfirmDeleteStageText = '<?= lang("Common.confirm_delete_stage_text") ?>';
    const langDeleteAllRelatedMatches = '<?= lang("Common.delete_all_related_matches") ?>';
    const langDelete = '<?= lang("Common.delete") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';
    const langDeleteSuccess = '<?= lang("Common.delete_success") ?>';
    const langStageAndMatchesDeleted = '<?= lang("Common.stage_and_matches_deleted") ?>';
    const langErrorTitle = '<?= lang("Common.error_title") ?>';
    const langStageIdInvalid = '<?= lang("Common.stage_id_invalid") ?>';
    const langCannotDeleteStage = '<?= lang("Common.cannot_delete_stage") ?>';
    const langDeletingData = '<?= lang("Common.deleting_data") ?>';
    const langErrorDeletingData = '<?= lang("Common.error_deleting_data") ?>';
    const langSelectTeam = '<?= lang("Common.select_team") ?>';
    const langSaving = '<?= lang("Common.saving") ?>';
    const langSaved = '<?= lang("Common.saved") ?>';
    const langSaveFailed = '<?= lang("Common.save_failed") ?>';
    const langErrorOccurred = '<?= lang("Common.error_occurred") ?>';
    const langMatchesCreatedPreview = '<?= lang("Common.matches_created_preview") ?>';
    const langMatchesCreated = '<?= lang("Common.matches_created") ?>';
    const langGroupUnspecified = '<?= lang("Common.group_unspecified") ?>';
    const langGroup = '<?= lang("Common.group") ?>';
    const langLane = '<?= lang("Common.lane") ?>';
    const langMatchPair = '<?= lang("Common.match_pair") ?>';
    const langWaitingMatch = '<?= lang("Common.waiting_match") ?>';
    const langMatchInProgress = '<?= lang("Common.match_in_progress") ?>';
    const langMatchFinished = '<?= lang("Common.match_finished") ?>';
    const langWaitingSchedule = '<?= lang("Common.waiting_schedule") ?>';
    const langSaveToEdit = '<?= lang("Common.save_to_edit") ?>';
    const langEditDetails = '<?= lang("Common.edit_match_details") ?>';
    const langDeleteMatch = '<?= lang("Common.delete_match") ?>';
    const langTeamNotInSystem = '<?= lang("Common.team_not_in_system") ?>';
    const langKnockoutNoGroups = '<?= lang("Common.knockout_no_groups") ?>';
    const langPleaseSelectFormat = '<?= lang("Common.please_select_format") ?>';
    const langPleaseSpecifyTeamCount = '<?= lang("Common.please_specify_team_count") ?>';
    const langGenerating = '<?= lang("Common.generating") ?>';
    const langMatchesCreatedSuccess = '<?= lang("Common.matches_created_success") ?>';
    const langTotalMatchesCreated = '<?= lang("Common.total_matches_created") ?>';
    const langClickEditToModify = '<?= lang("Common.click_edit_to_modify") ?>';
    const langOk = '<?= lang("Common.ok") ?>';
    const langCannotGenerateMatches = '<?= lang("Common.cannot_generate_matches") ?>';
    const langErrorGeneratingMatches = '<?= lang("Common.error_generating_matches") ?>';
    const langCannotSaveStageData = '<?= lang("Common.cannot_save_stage_data") ?>';
    const langErrorSavingData = '<?= lang("Common.error_saving_data") ?>';
    const langEditSlotLabel = '<?= lang("Common.edit_slot_label") ?>';
    const langPleaseEnterSlotName = '<?= lang("Common.please_enter_slot_name") ?>';
    const langSave = '<?= lang("Common.save") ?>';
    const langDate = '<?= lang("Common.date") ?>';
    const langTime = '<?= lang("Common.time") ?>';
    const langStadium = '<?= lang("Common.stadium") ?>';
    const langReferee = '<?= lang("Common.referee") ?>';
    const langBulkEdit = '<?= lang("Common.bulk_edit") ?>' !== 'Common.bulk_edit' ? '<?= lang("Common.bulk_edit") ?>' : 'แก้ไขแบบกลุ่ม';
    const langBulkEditDate = '<?= lang("Common.bulk_edit_date") ?>' !== 'Common.bulk_edit_date' ? '<?= lang("Common.bulk_edit_date") ?>' : 'แก้ไขวันที่แข่งแบบกลุ่ม';
    const langBulkEditTime = '<?= lang("Common.bulk_edit_time") ?>' !== 'Common.bulk_edit_time' ? '<?= lang("Common.bulk_edit_time") ?>' : 'แก้ไขเวลาแข่งแบบกลุ่ม';
    const langBulkEditStadium = '<?= lang("Common.bulk_edit_stadium") ?>' !== 'Common.bulk_edit_stadium' ? '<?= lang("Common.bulk_edit_stadium") ?>' : 'แก้ไขสนามแข่งแบบกลุ่ม';
    const langBulkEditReferee = '<?= lang("Common.bulk_edit_referee") ?>' !== 'Common.bulk_edit_referee' ? '<?= lang("Common.bulk_edit_referee") ?>' : 'แก้ไขผู้ตัดสินแบบกลุ่ม';
    const langCannotLoadMatchResults = '<?= lang("Common.cannot_load_match_results") ?>';
    const langMatchNotFound = '<?= lang("Common.match_not_found") ?>';
    const langCannotLoadMatchData = '<?= lang("Common.cannot_load_match_data") ?>';
    const langErrorLoadingMatchData = '<?= lang("Common.error_loading_match_data") ?>';
    const langFormNotFound = '<?= lang("Common.form_not_found") ?>';
    const langSavingData = '<?= lang("Common.saving_data") ?>';
    const langSuccess = '<?= lang("Common.success") ?>';
    const langMatchSaved = '<?= lang("Common.match_saved") ?>';
    const langCannotSaveData = '<?= lang("Common.cannot_save_data") ?>';
    const langErrorSavingDataServer = '<?= lang("Common.error_saving_data") ?>';
    const langCannotConnectServer = '<?= lang("Common.cannot_connect_server") ?>';
    const langServerError = '<?= lang("Common.server_error") ?>';
    const langConfirmDeleteMatch = '<?= lang("Common.confirm_delete_match") ?>';
    const langDeleteMatchText = '<?= lang("Common.delete_match_text") ?>';
    const langMatchDeleted = '<?= lang("Common.match_deleted") ?>';
    const langCannotDeleteMatch = '<?= lang("Common.cannot_delete_match") ?>';
    const langCannotConnectServerShort = '<?= lang("Common.cannot_connect_server") ?>';
    const langPleaseFilAllRequired = '<?= lang("Common.please_fill_all_required") ?>';
    const langHomeAwayMustDiffer = '<?= lang("Common.home_away_must_differ") ?>';
    const langDateFormatInvalid = '<?= lang("Common.date_format_invalid") ?>';
    const langDateValueInvalid = '<?= lang("Common.date_value_invalid") ?>';
    const langDateNotExist = '<?= lang("Common.date_not_exist") ?>';
    const langTryAgain = '<?= lang("Common.try_again") ?>';
    const langNoApprovedTeams = '<?= lang("Common.no_approved_teams") ?>';
    const langCanCreateMatchesButAddTeamsLater = '<?= lang("Common.can_create_matches_add_teams_later") ?>';
    const langGoToTeamsPage = '<?= lang("Common.go_to_teams_page") ?>';
    const langHomeTeam = '<?= lang("Common.home_team") ?>';
    const langAwayTeam = '<?= lang("Common.away_team") ?>';
    const langRoundNum = '<?= lang("Common.round_num") ?>';
    const langClickToEditSlot = '<?= lang("Common.click_to_edit_slot") ?>';

    // Import match-setup language variables
    const langImportMatchSetup = '<?= lang("Common.import_match_setup") ?>';
    const langSelectSourceTournament = '<?= lang("Common.select_source_tournament") ?>';
    const langNoTournamentFound = '<?= lang("Common.no_tournament_found") ?>';
    const langStagesCount = '<?= lang("Common.stages_count") ?>';
    const langMatchesCount = '<?= lang("Common.matches_count") ?>';
    const langConfirmImport = '<?= lang("Common.confirm_import") ?>';
    const langConfirmImportText = '<?= lang("Common.confirm_import_text") ?>';
    const langYesImport = '<?= lang("Common.yes_import") ?>';
    const langImportSuccess = '<?= lang("Common.import_success") ?>';
    const langImportError = '<?= lang("Common.import_error") ?>';
    const langSelectMatchForBulkEdit = '<?= lang("Common.select_match_for_bulk_edit") ?>';
    const langSelectShort = '<?= lang("Common.select") ?>';
    const langSelectAll = '<?= lang("Common.select_all") ?>';
    const langDeselectAll = '<?= lang("Common.deselect_all") ?>';
    const langPleaseEnterInformation = '<?= lang("Common.please_enter_information") ?>';
    const langConfirmBulkEditTitle = '<?= lang("Common.confirm_bulk_edit_title") ?>';
    const langConfirmSaveNow = '<?= lang("Common.confirm_save_now") ?>';
    const langSavingInformation = '<?= lang("Common.saving_information") ?>';
    const langSaveCompletedWithErrors = '<?= lang("Common.save_completed_with_errors") ?>';
    const langSaveSummary = '<?= lang("Common.save_summary") ?>';
    const langSaveSummaryErrorsSuffix = '<?= lang("Common.save_summary_errors_suffix") ?>';
    const langConfirmBulkEditTextTemplate = '<?= lang("Common.confirm_bulk_edit_text") ?>';

    const stages = {};
    let stageCounter = 0;
    let stagesLoaded = false;
    const tourId = '<?= $tour_id ?>';
    let allMatches = {};
    const maxTeams = <?= $total_teams ?>;
    const tournamentType = '<?= is_array($tournament) ? ($tournament['tournament_type'] ?? 'knockout') : ($tournament->tournament_type ?? 'knockout') ?>';

    let activePollingTimeouts = [];
    let isPageActive = true;

    function cancelAllPolling() {
        activePollingTimeouts.forEach(timeoutId => clearTimeout(timeoutId));
        activePollingTimeouts = [];
        isPageActive = false;
    }

    window.addEventListener('beforeunload', cancelAllPolling);

    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Debounce function สำหรับ auto-save
    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    // Save stage name to database
    function saveStageNameToDB(stageId, stageName) {
        if (!stageId) return;

        $.ajax({
            url: '<?= base_url('/admin/tournament/update-stage-name') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                stage_id: stageId,
                stage_name: stageName
            },
            success: function(response) {
                if (response.status === 'success') {
                    // Show subtle success indicator (optional)
                }
            },
            error: function() {
                console.error('Failed to save stage name');
            }
        });
    }

    // Save final match status to database
    function saveStageFinalMatchToDB(stageId, isFinal) {
        if (!stageId) return;

        const stageKey = Object.keys(stages).find(key => stages[key].stage_id == stageId);
        const stageCard = document.getElementById(`stage-${stageKey}`);
        let savingIndicator = null;

        if (stageCard) {
            const existing = stageCard.querySelector(`#saving-indicator-final-${stageId}`);
            if (existing) existing.remove();
            
            savingIndicator = document.createElement('div');
            savingIndicator.id = `saving-indicator-final-${stageId}`;
            savingIndicator.className = 'position-absolute top-0 end-0 m-2 badge bg-warning text-dark';
            savingIndicator.style.zIndex = '10';
            savingIndicator.innerHTML = '<i class="ti ti-loader spin"></i> ' + langSaving;
            
            const cardBody = stageCard.querySelector('.card-body');
            cardBody.style.position = 'relative';
            cardBody.appendChild(savingIndicator);
        }

        $.ajax({
            url: '<?= base_url('/admin/tournament/update-stage-final-match') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                stage_id: stageId,
                final_match: isFinal ? 1 : 0
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (savingIndicator) {
                        savingIndicator.className = 'position-absolute top-0 end-0 m-2 badge bg-success text-white';
                        savingIndicator.innerHTML = '<i class="ti ti-check"></i> ' + langSaved;
                        setTimeout(() => {
                            savingIndicator.style.opacity = '0';
                            setTimeout(() => savingIndicator.remove(), 500);
                        }, 1000);
                    }
                }
            },
            error: function() {
                if (savingIndicator) {
                    savingIndicator.className = 'position-absolute top-0 end-0 m-2 badge bg-danger text-white';
                    savingIndicator.innerHTML = '<i class="ti ti-x"></i> Error';
                    setTimeout(() => savingIndicator.remove(), 2000);
                }
                console.error('Failed to save final match status');
            }
        });
    }

    // Update stage match count in header
    function updateStageMatchCount(id, count) {
        const matchCountElement = document.getElementById(`stage-match-count-${id}`);
        if (matchCountElement) {
            const countNumber = matchCountElement.querySelector('.match-count-number');
            if (countNumber) {
                countNumber.textContent = count;
            }
            if (count > 0) {
                matchCountElement.style.display = 'inline-flex';
            } else {
                matchCountElement.style.display = 'none';
            }
        }
    }

    // Update total match count in page header
    function updateTotalMatchCount(totalCount) {
        const totalBadge = document.getElementById('total-matches-badge');
        const totalCountElement = document.getElementById('total-matches-count');
        if (totalBadge && totalCountElement) {
            totalCountElement.textContent = totalCount;
            if (totalCount > 0) {
                totalBadge.style.display = 'flex';
                totalBadge.style.setProperty('display', 'flex', 'important');
            } else {
                totalBadge.style.display = 'none';
                totalBadge.style.setProperty('none', 'important');
            }
        }
    }

    // Attach stage name input listeners
    function attachStageNameListeners(id) {
        const stageNameInput = document.querySelector(`#stage-${id} .stage-name-input`);
        const stageTitle = document.getElementById(`stage-title-${id}`);
        const finalMatchCheckbox = document.getElementById(`final-match-${id}`);

        if (!stageNameInput || !stageTitle) return;

        // Update title immediately on input
        stageNameInput.addEventListener('input', function() {
            const newName = this.value || `${langStageNumber} ${id}`;
            stageTitle.textContent = newName;
        });

        // Debounced save to database
        const debouncedSave = debounce(function(stageId, stageName) {
            saveStageNameToDB(stageId, stageName);
        }, 500);

        stageNameInput.addEventListener('input', function() {
            const stage = stages[id];
            if (stage && stage.stage_id) {
                debouncedSave(stage.stage_id, this.value || `${langStageNumber} ${id}`);
            }
        });

        if (finalMatchCheckbox) {
            finalMatchCheckbox.addEventListener('change', function() {
                const stage = stages[id];
                if (stage) {
                    stage.final_match = this.checked ? 1 : 0;
                    if (stage.stage_id) {
                        saveStageFinalMatchToDB(stage.stage_id, this.checked);
                    }
                }
            });
        }
    }

    const teamOptions = <?= json_encode(array_map(function ($team) {
                            return [
                                'id' => $team['id'],
                                'name' => $team['name'],
                                'logo' => $team['logo'] ?? ''
                            ];
                        }, $registered_teams ?? [])) ?>;

    function loadSavedStages() {

        if (stagesLoaded) {
            return Promise.resolve();
        }

        return new Promise((resolve) => {
            const ajaxUrl = '<?= base_url('/admin/tournament/get-tournament-stages') ?>/' + tourId;

            $.ajax({
                url: ajaxUrl,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success' && response.data.length > 0) {
                        const stagesContainer = document.getElementById('stages-container');
                        const emptyStateWrapper = document.getElementById('empty-state-wrapper');
                        const addMoreWrapper = document.getElementById('add-more-stages-wrapper');
                        if (stagesContainer) {
                            stagesContainer.style.display = 'block';
                        }
                        if (emptyStateWrapper) {
                            emptyStateWrapper.style.display = 'none';
                        }
                        if (addMoreWrapper) {
                            addMoreWrapper.style.display = 'block';
                        }
                        response.data.forEach((stageData, index) => {
                            addLoadedStageWithData(stageData, false);
                        });
                    } else {}
                    stagesLoaded = true;
                    resolve();
                },
                error: function(xhr) {
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 4000,
                        timerProgressBar: true
                    });
                    Toast.fire({
                        icon: 'error',
                        title: langCannotLoadStages
                    });
                    reject(new Error('Failed to load stages'));
                }
            });
        });
    }

    function addLoadedStageWithData(stageData, isExpanded = true) {
        stageCounter++;
        const id = stageCounter;

        stages[id] = {
            id: id,
            stage_id: stageData.stage_id || stageData.id || null,
            teams: stageData.teams_json ? JSON.parse(stageData.teams_json) : [],
            format: stageData.format || '',
            team_count: stageData.team_count || '',
            group_count: stageData.group_count || '',
            final_match: stageData.final_match || 0,
            config: {
                type: 'knockout',
                count: 4,
                groups: 1
            },
            data: null
        };

        const template = document.getElementById('stage-template').innerHTML;
        const html = template.replace(/{id}/g, id);
        document.getElementById('stages-container').insertAdjacentHTML('beforeend', html);

        const stageElement = document.getElementById(`stage-${id}`);

        // Handle collapse state based on isExpanded
        const stageHeader = stageElement.querySelector('.stage-header');
        const stageContent = document.getElementById(`stage-content-${id}`);

        if (isExpanded) {
            stageContent.classList.add('show');
            stageHeader.classList.remove('collapsed');
            stageHeader.setAttribute('aria-expanded', 'true');
        } else {
            stageContent.classList.remove('show');
            stageHeader.classList.add('collapsed');
            stageHeader.setAttribute('aria-expanded', 'false');
        }

        if (stageData.stage_id) {
            stageElement.dataset.dbStageId = stageData.stage_id;

        } else {

        }

        const stageNameValue = stageData.stage_name || `${langStageNumber} ${id}`;
        stageElement.querySelector('.stage-name-input').value = stageNameValue;

        // Update stage title in header to match the name
        const stageTitle = document.getElementById(`stage-title-${id}`);
        if (stageTitle) {
            stageTitle.textContent = stageNameValue;
        }

        // Set final match checkbox
        const finalMatchCheckbox = document.getElementById(`final-match-${id}`);
        if (finalMatchCheckbox) {
            finalMatchCheckbox.checked = parseInt(stages[id].final_match) === 1;
        }

        document.getElementById(`format-${id}`).value = stages[id].format;
        document.getElementById(`teams-count-${id}`).value = stages[id].team_count;
        document.getElementById(`groups-count-${id}`).value = stages[id].group_count;

        handleFormatChange(id);

        updateTeamInputs(id);

        const teamsCountInput = document.getElementById(`teams-count-${id}`);
        const groupsCountInput = document.getElementById(`groups-count-${id}`);
        const formatSelect = document.getElementById(`format-${id}`);

        const handleTeamsCountChange = () => {
            updateTeamInputs(id);
            const stage = stages[id];
            if (stage.stage_id) {
                updateStageTeamsInDB(stage.stage_id, stage.teams);
            }
        };

        const handleGroupsCountChange = () => {
            updateTeamInputs(id);
            const stage = stages[id];
            if (stage.stage_id) {
                updateStageTeamsInDB(stage.stage_id, stage.teams);
            }
        };

        const handleFormatSelectChange = () => handleFormatChange(id);

        teamsCountInput.removeEventListener('change', handleTeamsCountChange);
        groupsCountInput.removeEventListener('change', handleGroupsCountChange);
        formatSelect.removeEventListener('change', handleFormatSelectChange);

        teamsCountInput.addEventListener('change', handleTeamsCountChange);
        groupsCountInput.addEventListener('change', handleGroupsCountChange);
        formatSelect.addEventListener('change', handleFormatSelectChange);

        // Attach stage name input listeners for auto-update title
        attachStageNameListeners(id);

        if (stageData.stage_id) {
            updateGeneratedMatchesWithNewTeamNames(stageData.stage_id);
        }
    }

    function addNewStage() {
        stageCounter++;
        const id = stageCounter;

        stages[id] = {
            id: id,
            stage_id: null,
            teams: [],
            format: '',
            team_count: '',
            group_count: '',
            final_match: 0,
            config: {
                type: 'knockout',
                count: 4,
                groups: 1
            },
            data: null
        };
        const template = document.getElementById('stage-template').innerHTML;
        const html = template.replace(/{id}/g, id);
        document.getElementById('stages-container').insertAdjacentHTML('beforeend', html);

        document.querySelector(`#stage-${id} .stage-name-input`).value = `${langStageNumber} ${id}`;
        document.getElementById(`format-${id}`).value = stages[id].format;
        document.getElementById(`teams-count-${id}`).value = stages[id].team_count;
        document.getElementById(`groups-count-${id}`).value = stages[id].group_count;

        handleFormatChange(id);

        updateTeamInputs(id);

        const teamsCountInput = document.getElementById(`teams-count-${id}`);
        const groupsCountInput = document.getElementById(`groups-count-${id}`);
        const formatSelect = document.getElementById(`format-${id}`);

        const handleTeamsCountChange = () => {
            const teamCount = parseInt(teamsCountInput.value);

            if (teamCount) {
                stages[id].team_count = teamCount;

                updateTeamInputs(id);

                const stage = stages[id];
                if (stage.stage_id) {
                    updateStageTeamsInDB(stage.stage_id, stage.teams);
                }
                generatePreviewMatches(id);
            }
        };

        const handleGroupsCountChange = () => {
            const groupCount = parseInt(groupsCountInput.value) || 1;

            stages[id].group_count = groupCount;

            updateTeamInputs(id);

            const stage = stages[id];
            if (stage.stage_id) {
                updateStageTeamsInDB(stage.stage_id, stage.teams);
            }
            generatePreviewMatches(id);
        };

        const handleFormatSelectChange = () => {
            const selectedFormat = formatSelect.value;

            stages[id].format = selectedFormat;
            handleFormatChange(id);
            generatePreviewMatches(id);
        };

        teamsCountInput.removeEventListener('change', handleTeamsCountChange);
        groupsCountInput.removeEventListener('change', handleGroupsCountChange);
        formatSelect.removeEventListener('change', handleFormatSelectChange);

        teamsCountInput.addEventListener('change', handleTeamsCountChange);
        groupsCountInput.addEventListener('change', handleGroupsCountChange);
        formatSelect.addEventListener('change', handleFormatSelectChange);

        // Attach stage name input listeners for auto-update title
        attachStageNameListeners(id);

        setTimeout(() => document.getElementById(`stage-${id}`).scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        }), 100);
    }

    function removeStage(id) {
        const stageNameElement = document.querySelector(`#stage-${id} .stage-name-input`);
        const stageName = stageNameElement?.value || `${langStageNumber} ${id}`;
        let stageId = stages[id]?.stage_id;

        if (!stageId) {
            const stageElement = document.getElementById(`stage-${id}`);
            if (stageElement) {
                if (stageElement.dataset.dbStageId) {
                    stageId = stageElement.dataset.dbStageId;
                    stages[id].stage_id = stageId;
                }
            }
        }

        Swal.fire({
            title: langConfirmDelete,
            text: langConfirmDeleteStageText.replace('%s', stageName) + '\n' + langDeleteAllRelatedMatches,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#a82e2e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langDelete,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                const completeStageRemoval = () => {
                    const stageElement = document.getElementById(`stage-${id}`);
                    if (stageElement) {
                        stageElement.remove();
                    }

                    if (stages[id]) {
                        delete stages[id];
                    }

                    stageCounter = Object.keys(stages).length > 0 ?
                        Math.max(...Object.keys(stages).map(Number)) :
                        0;

                    Swal.fire({
                        title: langDeleteSuccess,
                        text: langStageAndMatchesDeleted,
                        icon: 'success',
                        confirmButtonColor: '#16478D'
                    });
                };

                if (stageId && /^\d+$/.test(String(stageId))) {

                    Swal.fire({
                        title: langDeletingData,
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });

                    $.ajax({
                        url: '<?= base_url('admin/tournament/delete-tournament-stage') ?>/' + stageId,
                        type: 'POST',
                        dataType: 'json',
                        success: function(response) {

                            if (response && response.status === 'success') {
                                completeStageRemoval();
                            } else {
                                const errorMsg = response && response.msg ? response.msg : langCannotDeleteStage;
                                Swal.fire({
                                    title: langErrorTitle,
                                    text: errorMsg,
                                    icon: 'error',
                                    confirmButtonColor: '#a82e2e'
                                });
                            }
                        },
                        error: function(xhr, status, error) {
                            let errorMsg = langErrorDeletingData;

                            try {
                                if (xhr.responseJSON && xhr.responseJSON.msg) {
                                    errorMsg = xhr.responseJSON.msg;
                                } else if (xhr.responseText) {
                                    const jsonResponse = JSON.parse(xhr.responseText);
                                    if (jsonResponse && jsonResponse.msg) {
                                        errorMsg = jsonResponse.msg;
                                    }
                                }
                            } catch (e) {
                                errorMsg = `HTTP ${xhr.status}: ${error || 'Unknown error'}`;
                            }

                            Swal.fire({
                                title: langErrorTitle,
                                text: errorMsg,
                                icon: 'error',
                                confirmButtonColor: '#a82e2e'
                            });
                        }
                    });
                } else {
                    completeStageRemoval();
                }
            }
        });
    }

    function updateTeamInputs(id) {
        const count = parseInt(document.getElementById(`teams-count-${id}`).value) || 2;
        const groups = parseInt(document.getElementById(`groups-count-${id}`).value) || 1;
        const container = document.getElementById(`team-inputs-${id}`);

        const oldTeams = stages[id].teams;

        $(`#team-inputs-${id} .team-select`).each(function() {
            if ($(this).data('select2')) {
                $(this).select2('destroy');
            }
        });

        stages[id].teams = [];

        container.innerHTML = '';

        let html = '<div style="display: grid; grid-template-columns: repeat(' + groups + ', minmax(120px, 1fr)); gap: 20px;">';

        for (let i = 0; i < count; i++) {
            const groupIdx = i % groups;
            const groupChar = String.fromCharCode(65 + groupIdx);
            const existingTeamId = oldTeams[i] ? oldTeams[i].id : '';
            const mockTeamNumber = i + 1;

            const positionInGroup = Math.floor(i / groups) + 1;
            const teamNumber = `${groupChar}${positionInGroup}`;

            let defaultTeamId = '';
            let defaultTeamName = '';

            if (existingTeamId) {
                defaultTeamId = existingTeamId;

                const foundTeam = teamOptions.find(t => t.id == defaultTeamId);
                if (foundTeam) {
                    defaultTeamName = foundTeam.name;
                } else {
                    defaultTeamId = '';
                    defaultTeamName = '';
                }
            } else {
                const existingSelect = document.querySelector(`select[data-stage="${id}"][data-team="${i}"]`);
                if (existingSelect && existingSelect.value) {
                    const val = existingSelect.value;
                    const foundTeam = teamOptions.find(t => t.id == val);
                    if (foundTeam) {
                        defaultTeamId = val;
                        defaultTeamName = foundTeam.name;
                    }
                }
            }

            if (!defaultTeamId) {
                defaultTeamId = 'auto_' + i;
                // Check if old team had a custom label, if so use it as name
                if (oldTeams[i] && oldTeams[i].custom_slot_label) {
                    defaultTeamName = oldTeams[i].custom_slot_label;
                } else {
                    defaultTeamName = `${groupChar}${positionInGroup}`;
                }
            }

            const actualTeamId = defaultTeamId;
            stages[id].teams.push({
                id: actualTeamId,
                name: defaultTeamName,
                group: groupChar
            });

            let teamOptionsHtml = '<option value="">' + langSelectTeam + '</option>';

            if (teamOptions.length > 0) {
                teamOptions.forEach(team => {
                    const isSelected = (defaultTeamId && !String(defaultTeamId).startsWith('auto_') && team.id == defaultTeamId) ? 'selected' : '';
                    teamOptionsHtml += `<option value="${team.id}" ${isSelected}>${team.name}</option>`;
                });

                teamOptionsHtml += '<option value="" disabled>─────────────</option>';
            }

            html += `
            <div style="display: flex; flex-direction: column;">
                <label class="form-label small fw-bold mb-2" style="color: var(--ms-navy); width: 100%; display: flex; align-items: center; justify-content: space-between;">
                    <div style="display: flex; align-items: center; cursor: pointer;" onclick="editSlotLabel(${id}, ${i})" title="${langClickToEditSlot}">
                        <span class="badge me-2" style="background-color: var(--ms-navy); color: #fff; transition: all 0.2s;">
                            ${oldTeams[i] && oldTeams[i].custom_slot_label ? oldTeams[i].custom_slot_label : teamNumber}
                            <i class="ti ti-pencil ms-1" style="font-size: 0.7em; opacity: 0.7;"></i>
                        </span>
                    </div>
                </label>
                <select class="form-select form-select-sm border team-select" data-stage="${id}" data-team="${i}" style="border-color: #DFE5EF; font-family: 'Kanit', sans-serif;" onchange="updateTeamSelect(${id}, ${i}, this.value)">
                    ${teamOptionsHtml}
                </select>
            </div>
        `;
        }
        html += '</div>';
        container.innerHTML = html;

        // Persist custom labels to new array structure if not already there
        for (let i = 0; i < count; i++) {
            if (stages[id].teams[i] && oldTeams[i] && oldTeams[i].custom_slot_label) {
                stages[id].teams[i].custom_slot_label = oldTeams[i].custom_slot_label;
            }
        }

        $(`#team-inputs-${id} .team-select`).select2({
            placeholder: langSelectTeam,
            allowClear: true,
            width: '100%'
        });

        $(`#team-inputs-${id} .team-select`).each(function() {
            if ($(this).val()) {
                const stageId = $(this).data('stage');
                const teamIdx = $(this).data('team');
                const val = $(this).val();
                updateTeamSelect(stageId, teamIdx, val);
            }
        });
    }

    $(document).on('change', '.team-select', function() {
        const stageId = $(this).data('stage');
        const teamIdx = $(this).data('team');
        const val = $(this).val();
        updateTeamSelect(stageId, teamIdx, val);
    });

    function updateTeamSelect(stageId, teamIdx, val) {
        const stage = stages[stageId];

        if (stage && stage.teams && stage.teams[teamIdx] && stage.teams[teamIdx].id === val) {
            return;
        }

        if (val && !teamOptions.find(t => t.id == val)) {
            Swal.fire(langErrorTitle, langTeamNotInSystem, 'error');

            const previousId = stage.teams[teamIdx].id;
            const select = $(`select[data-stage="${stageId}"][data-team="${teamIdx}"]`);
            if (select.length) {
                select.val(previousId || '').trigger('change.select2');
            }
            return;
        }

        const selectEl = $(`select[data-stage="${stageId}"][data-team="${teamIdx}"]`);
        if (selectEl.length > 0 && String(selectEl.val()) !== String(val)) {
            selectEl.val(val).trigger('change');
        }



        stage.teams[teamIdx].id = val;

        let teamName = '';
        let teamId = '';

        if (val) {
            teamName = teamOptions.find(t => t.id == val)?.name || '';
            teamId = val;

            const groups = parseInt(document.getElementById(`groups-count-${stageId}`).value) || 1;
            const groupIdx = teamIdx % groups;
            stage.teams[teamIdx].group = String.fromCharCode(65 + groupIdx);
        } else {

            const groups = parseInt(document.getElementById(`groups-count-${stageId}`).value) || 1;
            const groupIdx = teamIdx % groups;
            const groupChar = String.fromCharCode(65 + groupIdx);

            stage.teams[teamIdx].group = groupChar;
            const positionInGroup = Math.floor(teamIdx / groups) + 1;

            let placeholderName = `${groupChar}${positionInGroup}`;
            if (stage.teams[teamIdx].custom_slot_label) {
                placeholderName = stage.teams[teamIdx].custom_slot_label;
            }

            teamName = placeholderName;
            teamId = 'auto_' + teamIdx;
            stage.teams[teamIdx].id = teamId;
        }

        stage.teams[teamIdx].name = teamName;

        updateTeamLogoInUI(stageId, teamIdx, teamId);

        // ถ้า stage นี้มีแมตช์ที่บันทึกใน DB แล้ว ให้บันทึกทีมแล้วรีโหลดข้อมูลจริงจาก DB
        // (ป้องกันพรีวิว client-side ไปทับข้อมูลวันที่/เวลา/สนาม/ผู้ตัดสินที่บันทึกไว้แล้ว)
        if (stage.stage_id && stageHasSavedMatches(stageId)) {
            updateStageTeamsInDB(stage.stage_id, stage.teams, { reloadMatches: true });
            return;
        }

        // Show client-side preview (uses same algorithm as server)
        displayMatchPreviewForStage(stageId);

        if (stage.stage_id) {
            updateStageTeamsInDB(stage.stage_id, stage.teams);
        }
    }

    // เช็คว่า stage นี้มีแมตช์ที่ถูกบันทึกไว้ใน DB แล้วหรือยัง (เทียบจากชื่อ stage กับ match_round)
    function stageHasSavedMatches(stageId) {
        const stageName = document.querySelector(`#stage-${stageId} .stage-name-input`)?.value;
        if (!stageName) return false;
        return Object.values(allMatches).some(m => (m.match_round || '') === stageName);
    }

    function updateTeamLogoInUI(stageId, teamIdx, teamId) {
        const defaultLogo = '<?= base_url('public/img/jootball_logo.png') ?>';
        let logoUrl = defaultLogo;

        if (teamId && !String(teamId).startsWith('auto_')) {
            const teamData = teamOptions.find(t => t.id == teamId);
            if (teamData && teamData.logo) {
                if (teamData.logo.startsWith('http://') || teamData.logo.startsWith('https://')) {
                    logoUrl = teamData.logo;
                } else {
                    const baseUrl = '<?= rtrim(base_url(), '/') ?>/';
                    logoUrl = baseUrl + teamData.logo.replace(/^\/+/, '');
                }
            }
        }

        const matchContainer = document.querySelector(`#stage-matches-${stageId}`);
        if (matchContainer) {
            const logoImg = matchContainer.querySelector(`img[data-team-index="${teamIdx}"]`);
            if (logoImg) {
                logoImg.src = logoUrl;
            }
        }
    }

    function updateStageTeamsInDB(stageId, teams, options = {}) {
        const savingIndicator = document.createElement('div');
        savingIndicator.id = `saving-indicator-${stageId}`;
        savingIndicator.className = 'position-absolute top-0 end-0 m-2 badge bg-warning text-dark';
        savingIndicator.style.transition = 'opacity 0.5s';
        savingIndicator.innerHTML = '<i class="ti ti-loader spin"></i> ' + langSaving;

        const stageCard = document.getElementById(`stage-${Object.keys(stages).find(key => stages[key].stage_id == stageId)}`);
        if (stageCard) {
            const existing = stageCard.querySelector(`#saving-indicator-${stageId}`);
            if (existing) existing.remove();
            stageCard.querySelector('.card-body').style.position = 'relative';
            stageCard.querySelector('.card-body').appendChild(savingIndicator);
        }

        $.ajax({
            url: '<?= base_url('admin/tournament/update-stage-teams') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                stage_id: stageId,
                teams_json: JSON.stringify(teams)
            },
            success: function(response) {
                if (response.status === 'success') {
                    if (savingIndicator) {
                        savingIndicator.className = 'position-absolute top-0 end-0 m-2 badge bg-success text-white';
                        savingIndicator.innerHTML = '<i class="ti ti-check"></i> ' + langSaved;
                        setTimeout(() => {
                            savingIndicator.style.opacity = '0';
                            setTimeout(() => savingIndicator.remove(), 500);
                        }, 2000);
                    }
                    // รีโหลดการ์ดแมตช์จาก DB เพื่อให้แสดงทีมใหม่พร้อมวันที่/เวลา/สนาม/ผู้ตัดสินที่บันทึกไว้
                    if (options.reloadMatches) {
                        loadMatchResults({ noScroll: true });
                    }
                } else {
                    if (savingIndicator) {
                        savingIndicator.className = 'position-absolute top-0 end-0 m-2 badge bg-danger text-white';
                        savingIndicator.innerHTML = '<i class="ti ti-alert-circle"></i> ' + langSaveFailed;
                    }
                }
            },
            error: function() {
                if (savingIndicator) {
                    savingIndicator.className = 'position-absolute top-0 end-0 m-2 badge bg-danger text-white';
                    savingIndicator.innerHTML = '<i class="ti ti-alert-circle"></i> ' + langErrorOccurred;
                }
            }
        });
    }

    function updateGeneratedMatchesWithNewTeamNames(stageId) {
        if (!stageId || !stages[stageId]) {
            return;
        }

        const stage = stages[stageId];
        const matchContainer = document.querySelector(`#stage-matches-${stageId}`);

        if (!matchContainer || !matchContainer.innerHTML) {
            return;
        }

        const formatElement = document.getElementById(`format-${stageId}`);
        if (!formatElement) {
            return;
        }

        const format = formatElement.value || 'knockout';
        const numGroups = parseInt(document.getElementById(`groups-count-${stageId}`)?.value) || 1;

        if (!stage.teams || !Array.isArray(stage.teams)) {
            return;
        }

        let matchPairs = [];

        const teamCount = stage.team_count || stage.teams.length;
        const teamsWithIndex = stage.teams.slice(0, teamCount).map((t, i) => ({
            ...t,
            _originalIndex: i
        }));

        if (format === 'knockout') {
            matchPairs = generateKnockoutPreview(teamsWithIndex);
        } else if (format === 'league_single') {
            matchPairs = generateLeaguePreview(teamsWithIndex);
        } else if (format === 'league_double') {
            matchPairs = generateLeagueDoublePreview(teamsWithIndex);
        }

        let html = `
        <div class="stage-matches-container" style="border-top: 2px solid #16478D; padding-top: 1.5rem;">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h6 class="fw-bold mb-0" style="color: #16478D; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                    <i class="ti ti-check-circle" style="font-size: 1.1rem;"></i>
                    ${langMatchesCreatedPreview}
                </h6>
                <span class="badge match-created-badge" style="background-color: #13DEB9; font-size: 0.75rem; padding: 6px 12px; font-weight: 600;">
                    ${matchPairs.length} ${langMatches}
                </span>
            </div>
    `;

        const groupedMatches = {};
        matchPairs
            .sort((a, b) => {
                const gA = String(a.group_id || '');
                const gB = String(b.group_id || '');
                if (gA !== gB) return gA.localeCompare(gB);
                return (a.round_num || 0) - (b.round_num || 0);
            })
            .forEach(match => {
            const group = match.group_id || langGroupUnspecified;
            if (!groupedMatches[group]) {
                groupedMatches[group] = [];
            }
            groupedMatches[group].push(match);
        });

        let globalMatchIndex = 0;

        Object.keys(groupedMatches).sort().forEach(groupName => {
            const matches = groupedMatches[groupName];

            const isLetter = /^[A-Z]$/.test(groupName);
            const groupText = isLetter ? `${langLane} ${groupName}` : `${groupName}`;

            let groupBadge = '';
            if (groupName !== langGroupUnspecified && format !== 'knockout') {
                groupBadge = `<span class="badge" style="background-color: #16478D; color: #fff; font-weight: 600; padding: 5px 11px; font-size: 0.78rem;">📍 ${groupText}</span>`;
            }

            matches.forEach((match, idx) => {
                globalMatchIndex++;
                const defaultLogo = '<?= base_url('public/img/jootball_logo.png') ?>';

                const team1 = match.team1;
                const team2 = match.team2;

                const team1Name = match.team1.name || '';
                const team2Name = match.team2.name || '';

                const team1Data = teamOptions.find(t => t.id == team1.id);
                const team2Data = teamOptions.find(t => t.id == team2.id);

                const baseUrl = '<?= rtrim(base_url(), '/') ?>/';
                const resolveLogoPath = (logoPath) => {
                    if (!logoPath) return defaultLogo;
                    if (logoPath.startsWith('http://') || logoPath.startsWith('https://')) return logoPath;
                    return baseUrl + logoPath.replace(/^\/+/, '');
                };

                const team1Logo = resolveLogoPath(team1Data && team1Data.logo ? team1Data.logo : (team1.logo || ''));
                const team2Logo = resolveLogoPath(team2Data && team2Data.logo ? team2Data.logo : (team2.logo || ''));
                const escapedTeam1Name = escapeHtml(team1Name);
                const escapedTeam2Name = escapeHtml(team2Name);

                const statusColor = '#D19E11';
                const statusText = langWaitingMatch;

                const index1 = (team1._originalIndex !== undefined) ? team1._originalIndex : -1;
                const index2 = (team2._originalIndex !== undefined) ? team2._originalIndex : -1;

                html += `
                <div class="match-card-block" style="margin-bottom: 16px;" data-stage-id="${stageId}" data-group="${groupName === langGroupUnspecified ? '' : groupName}" data-team1="${escapedTeam1Name}" data-team2="${escapedTeam2Name}" data-status="0" data-stadium="" data-referee="" data-date="">
                    <div class="match-meta-row">
                        ${groupBadge}
                        <span class="badge" style="background-color: #e3edfa; color: #16478D; font-weight: 700; padding: 5px 11px; font-size: 0.78rem; border: 1px solid #c5d6ee;">${langMatchPair} ${globalMatchIndex}</span>
                        <label class="match-select-pill" for="chk${match.match_id}" title="${langSelectMatchForBulkEdit}">
                            <input type="checkbox" value="1" class="form-check-input" id="chk${match.match_id}">
                            <span>${langSelectShort}</span>
                        </label>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-4 rounded-3 match-row-wrapper is-selectable" data-match-id="${match.match_id}" style="gap: 20px; background: #ffffff; box-shadow: 0 4px 18px rgba(22, 71, 141, 0.06); border: 1px solid #eef1f6; border-left: 4px solid #D19E11; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <span class="selected-indicator"><i class="ti ti-check"></i></span>
                        <div class="text-center" style="flex: 1; min-width: 0;">
                            <div style="position: relative; width: 64px; height: 64px; margin: 0 auto 12px; border-radius: 12px; padding: 4px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <img src="${team1Logo}" alt="${escapedTeam1Name}" class="w-100 h-100" style="object-fit: contain;" data-team-index="${index1}" onerror="this.onerror=null;this.src='${defaultLogo}';">
                            </div>
                            <div class="fw-bold text-truncate" style="color: #16478D; font-size: 0.95rem; margin-bottom: 4px;">${escapedTeam1Name}</div>
                        </div>

                        <div class="text-center d-flex flex-column align-items-center justify-content-center" style="flex: 0 0 140px;">
                            <div class="badge rounded-pill mb-2 px-3 py-2" style="background-color: ${statusColor}15; color: ${statusColor}; font-weight: 700; font-size: 0.75rem; border: 1px solid ${statusColor}30;">
                                <i class="ti ti-clock me-1"></i> ${statusText}
                            </div>

                            <div class="d-flex flex-column align-items-center">
                                <span style="font-size: 1.5rem; font-weight: 800; color: #16478D; line-height: 1;">VS</span>
                                <span class="small text-muted mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    ${langWaitingSchedule}<br>
                                    <span style="opacity: 0.8;">-</span>
                                </span>
                            </div>
                        </div>

                        <div class="text-center" style="flex: 1; min-width: 0;">
                            <div style="position: relative; width: 64px; height: 64px; margin: 0 auto 12px; border-radius: 12px; padding: 4px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <img src="${team2Logo}" alt="${escapedTeam2Name}" class="w-100 h-100" style="object-fit: contain;" data-team-index="${index2}" onerror="this.onerror=null;this.src='${defaultLogo}';">
                            </div>
                            <div class="fw-bold text-truncate" style="color: #16478D; font-size: 0.95rem; margin-bottom: 4px;">${escapedTeam2Name}</div>
                        </div>

                        <div style="flex: 0 0 auto; border-left: 1px solid #eee; padding-left: 15px;" class="dropdown">
                            <button class="btn btn-light btn-icon rounded-circle" type="button" data-bs-toggle="dropdown" style="color: #7C8FAC; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-settings"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><span class="dropdown-item py-2 text-muted" style="cursor: default;"><i class="ti ti-info-circle me-2"></i> ${langSaveToEdit}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            });
        });

        html += `</div>`;
        matchContainer.innerHTML = html;
    }

    function handleFormatChange(id) {
        const format = document.getElementById(`format-${id}`).value;
        const groupsCountInput = document.getElementById(`groups-count-${id}`);
        const teamCountInput = document.getElementById(`teams-count-${id}`);

        if (format === 'knockout') {
            groupsCountInput.value = 1;
            groupsCountInput.disabled = true;
            groupsCountInput.title = langKnockoutNoGroups;

            if (stages[id]) {
                stages[id].format = format;
            }
        } else {
            groupsCountInput.disabled = false;
            groupsCountInput.title = '';
        }

        if (teamCountInput.value) {
            updateTeamInputs(id);
        }
    }

    function generateStage(id) {
        const format = document.getElementById(`format-${id}`).value;
        const teamCountInput = document.getElementById(`teams-count-${id}`);
        const groupCountInput = document.getElementById(`groups-count-${id}`);
        const stageNameInput = document.getElementById(`stage-name-input-${id}`).value || `${langStageNumber} ${id}`;

        if (!format) {
            Swal.fire(langErrorTitle, langPleaseSelectFormat, 'warning');
            return;
        }

        if (!teamCountInput.value) {
            Swal.fire(langErrorTitle, langPleaseSpecifyTeamCount, 'warning');
            return;
        }

        const teamCount = parseInt(teamCountInput.value);
        const groupCount = parseInt(groupCountInput.value) || 1;

        const stageTitle = document.getElementById(`stage-title-${id}`);
        if (stageTitle && stageNameInput) {
            stageTitle.textContent = stageNameInput;
        }

        const generateBtn = event.target.closest('button');

        const stageId = stages[id].stage_id;

        const originalBtnHTML = generateBtn.innerHTML;
        generateBtn.disabled = true;
        generateBtn.innerHTML = '<i class="ti ti-loader spin"></i> ' + langGenerating;

        $.ajax({
            url: '<?= base_url('/admin/tournament/save-tournament-stage') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                stage_id: stageId || '',
                tour_id: tourId,
                stage_name: stageNameInput,
                format: format,
                team_count: teamCount,
                group_count: groupCount,
                order_num: id,
                final_match: document.getElementById(`final-match-${id}`).checked ? 1 : 0
            },
            success: function(response) {
                if (response.status === 'success') {

                    stages[id].stage_id = response.stage_id;

                    const stageElement = document.getElementById(`stage-${id}`);
                    if (stageElement) {
                        stageElement.dataset.dbStageId = response.stage_id;
                    }

                    const roundsData = [];
                    const stage = stages[id];
                    const format = document.getElementById(`format-${id}`).value;

                    roundsData.push({
                        name: document.querySelector(`#stage-${id} .stage-name-input`).value,
                        type: format,
                        groups: parseInt(document.querySelector(`#groups-count-${id}`).value) || 1
                    });

                    let teamsData = [];
                    const teamCountFromUI = parseInt(document.getElementById(`teams-count-${id}`).value) || 2;
                    const groupsCountFromUI = parseInt(document.getElementById(`groups-count-${id}`).value) || 1;

                    const existingTeamsMap = {};
                    if (stage.teams && Array.isArray(stage.teams)) {
                        stage.teams.forEach((t, idx) => {
                            if (t && t.id && !String(t.id).startsWith('auto_')) {
                                existingTeamsMap[idx] = t;
                            }
                        });
                    }

                    for (let i = 0; i < teamCountFromUI; i++) {
                        const groupChar = String.fromCharCode(65 + (i % groupsCountFromUI));

                        if (existingTeamsMap[i]) {
                            const existingId = existingTeamsMap[i].id;
                            const isAuto = String(existingId).startsWith('auto_') || existingId == 0 || existingId == '0';
                            teamsData.push({
                                id: isAuto ? (-1 - i) : existingId,
                                group: existingTeamsMap[i].group || groupChar
                            });
                        } else {
                            teamsData.push({
                                id: -1 - i,
                                group: groupChar
                            });
                        }
                    }

                    $.ajax({
                        url: '<?= base_url('admin/tournament/generate-matches-with-teams') ?>',
                        type: 'POST',
                        dataType: 'json',
                        data: {
                            tour_id: tourId,
                            stage_id: stage.stage_id,
                            rounds: JSON.stringify(roundsData),
                            teamsData: JSON.stringify(teamsData)
                        },
                        beforeSend: function() {

                        },
                        success: function(response) {

                            if (response.status === 'success') {

                                const processingDelay = response.processing_time || 500;
                                const maxRetries = 5;
                                let retryCount = 0;

                                const pollForMatches = () => {
                                    if (!isPageActive) {
                                        return;
                                    }

                                    const timeoutId = setTimeout(() => {
                                        const index = activePollingTimeouts.indexOf(timeoutId);
                                        if (index > -1) {
                                            activePollingTimeouts.splice(index, 1);
                                        }

                                        if (!isPageActive) {
                                            return;
                                        }

                                        $.ajax({
                                            url: '<?= base_url('admin/tournament/get-tournament-match-results') ?>',
                                            type: 'GET',
                                            dataType: 'json',
                                            data: {
                                                tour_id: tourId
                                            },
                                            success: function(pollResponse) {
                                                if (!isPageActive) return;

                                                if (pollResponse && pollResponse.data && pollResponse.data.length > 0) {
                                                    loadMatchResults();
                                                } else if (retryCount < maxRetries) {
                                                    retryCount++;
                                                    pollForMatches();
                                                } else {
                                                    loadMatchResults();
                                                }
                                            },
                                            error: function() {
                                                if (!isPageActive) return;

                                                if (retryCount < maxRetries) {
                                                    retryCount++;
                                                    pollForMatches();
                                                } else {
                                                    loadMatchResults();
                                                }
                                            }
                                        });
                                    }, processingDelay);

                                    activePollingTimeouts.push(timeoutId);
                                };

                                pollForMatches();

                                // Update match count in header
                                updateStageMatchCount(id, response.total_matches || 0);

                                Swal.fire({
                                    title: langMatchesCreatedSuccess,
                                    html: `
                                    <div style="text-align: left; margin: 15px 0;">
                                        <p><strong>${response.msg}</strong></p>
                                        <div style="background-color: #ECF2FF; padding: 12px; border-radius: 8px; margin-top: 12px; color: #16478D;">
                                            <i class="ti ti-check-circle" style="color: #13DEB9; margin-right: 8px;"></i>
                                            <strong>${langTotalMatchesCreated}: ${response.total_matches} ${langMatches}</strong>
                                        </div>
                                        <p style="font-size: 0.9rem; color: #7C8FAC; margin-top: 12px; margin-bottom: 0;">
                                            <i class="ti ti-info-circle" style="margin-right: 6px;"></i>
                                            ${langClickEditToModify}
                                        </p>
                                    </div>
                                `,
                                    icon: 'success',
                                    confirmButtonText: langOk,
                                    confirmButtonColor: '#16478D',
                                    allowOutsideClick: false
                                });

                                generateBtn.disabled = false;
                                generateBtn.innerHTML = originalBtnHTML;
                            } else {
                                generateBtn.disabled = false;
                                generateBtn.innerHTML = originalBtnHTML;
                                Swal.fire(langErrorTitle, response.msg || langCannotGenerateMatches, 'error');
                            }
                        },
                        error: function(xhr) {

                            generateBtn.disabled = false;
                            generateBtn.innerHTML = originalBtnHTML;

                            let errorMsg = langErrorGeneratingMatches;
                            if (xhr.responseJSON && xhr.responseJSON.msg) {
                                errorMsg = xhr.responseJSON.msg;
                            }

                            Swal.fire(langErrorTitle, errorMsg, 'error');
                        }
                    });
                } else {
                    generateBtn.disabled = false;
                    generateBtn.innerHTML = originalBtnHTML;
                    Swal.fire(langErrorTitle, response.msg || langCannotSaveStageData, 'error');
                }
            },
            error: function(xhr) {
                generateBtn.disabled = false;
                generateBtn.innerHTML = originalBtnHTML;
                Swal.fire(langErrorTitle, langErrorSavingData, 'error');
            }
        });
    }

    function editSlotLabel(stageId, teamIndex) {
        const stage = stages[stageId];
        if (!stage || !stage.teams || !stage.teams[teamIndex]) {
            return;
        }

        const currentLabel = stage.teams[teamIndex].custom_slot_label || '';

        // Calculate default label for reference
        const groups = parseInt(document.getElementById(`groups-count-${stageId}`).value) || 1;
        const groupIdx = teamIndex % groups;
        const groupChar = String.fromCharCode(65 + groupIdx);
        const positionInGroup = Math.floor(teamIndex / groups) + 1;
        const defaultLabel = `${groupChar}${positionInGroup}`;

        Swal.fire({
            title: langEditSlotLabel,
            input: 'text',
            inputValue: currentLabel || defaultLabel,
            inputPlaceholder: defaultLabel,
            showCancelButton: true,
            confirmButtonText: langSave,
            cancelButtonText: langCancel,
            confirmButtonColor: '#16478D',
            inputValidator: (value) => {
                if (!value) {
                    return langPleaseEnterSlotName;
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Update local state
                stage.teams[teamIndex].custom_slot_label = result.value;

                // If currently using a placeholder name (starts with auto_ or empty), update the name as well
                if (!stage.teams[teamIndex].id || String(stage.teams[teamIndex].id).startsWith('auto_')) {
                    stage.teams[teamIndex].name = result.value;
                    if (!stage.teams[teamIndex].id) {
                        stage.teams[teamIndex].id = 'auto_' + teamIndex;
                    }
                }

                // Re-render inputs to show new label
                updateTeamInputs(stageId);

                // Update preview matches logic to reflect name change
                updateGeneratedMatchesWithNewTeamNames(stageId);

                // Refresh Saved/Created Matches view to reflect name change (no scroll)
                loadMatchResults({ noScroll: true });

                // Save to DB
                if (stage.stage_id) {
                    updateStageTeamsInDB(stage.stage_id, stage.teams);
                }
            }
        });
    }

    function generatePreviewMatches(stageId) {
        displayMatchPreviewForStage(stageId);
    }

    function displayMatchPreviewForStage(stageId) {
        const stage = stages[stageId];
        if (!stage || !stage.teams || stage.teams.length < 2) {
            return;
        }

        const format = document.getElementById(`format-${stageId}`).value;
        const groupCount = parseInt(document.getElementById(`groups-count-${stageId}`).value) || 1;

        let matchPairs = [];

        const teamCount = stage.team_count || stage.teams.length;
        const teamsWithIndex = stage.teams.slice(0, teamCount).map((t, i) => ({
            ...t,
            _originalIndex: i
        }));

        if (format === 'knockout') {
            matchPairs = generateKnockoutPreview(teamsWithIndex);
        } else if (format === 'league_single') {
            matchPairs = generateLeaguePreview(teamsWithIndex);
        } else if (format === 'league_double') {
            matchPairs = generateLeagueDoublePreview(teamsWithIndex);
        }

        displayMatchPreviewHTML(stageId, matchPairs);
    }

    // ... (in generateLeaguePreview) ...
    // ... (in generateLeaguePreview) ...
    function generateKnockoutPreview(teams) {
        const matches = [];
        for (let i = 0; i < teams.length; i += 2) {
            if (i + 1 < teams.length) {
                matches.push({
                    team1: teams[i],
                    team2: teams[i + 1],
                    group_id: teams[i].group || 'A',
                    round_num: 1
                });
            }
        }
        return matches;
    }

    function generateLeaguePreview(teams) {
        const matches = [];

        // Calculate total groups based on standard logic (or passed parameter if available)
        // We infer group count from the highest group assigned or default logic
        // But to be safe for preview, we should trust the input UI's group count if accessible or derive from teams

        // Better approach: Re-assign groups based on index if 'group' property is missing or suspect
        // However, teams strictly come from UI where group is assigned.
        // Let's rely on the passed 'teams' array but ensure we group by what's actually there.

        // Group teams by their group
        const groupedTeams = {};
        teams.forEach((team, index) => {
            let group = team.group;

            // Fallback: If group is missing, calculate it (Same logic as updateTeamInputs)
            if (!group) {
                // Try to find the stage ID from context or DOM to get group count
                // Since we don't have stageId here easily without changing signature, we rely on the object.
                // If manual group assignment failed, we default to A.
                group = 'A';
            }

            if (!groupedTeams[group]) {
                groupedTeams[group] = [];
            }
            groupedTeams[group].push(team);
        });

        // Generate Round Robin for each group
        Object.keys(groupedTeams).sort().forEach(group => {
            let T = groupedTeams[group];
            let N = T.length;

            if (N < 2) return;

            if (N % 2 !== 0) {
                // ODD teams: Dummy at position 0 (fixed), counter-clockwise rotation
                // This ensures team[0] plays in round 1 (not bye first)
                // Example 3 teams [A1,A2,A3]: produces A1-A2, A2-A3, A3-A1
                let scheduleTeams = [{ id: 'dummy', name: 'Bye' }, ...T];
                const numTeams = scheduleTeams.length;
                const numRounds = numTeams - 1;
                const halfSize = numTeams / 2;

                for (let r = 0; r < numRounds; r++) {
                    for (let i = 0; i < halfSize; i++) {
                        const teamA = scheduleTeams[i];
                        const teamB = scheduleTeams[numTeams - 1 - i];

                        if (teamA.id !== 'dummy' && teamB.id !== 'dummy') {
                            matches.push({
                                team1: teamA,
                                team2: teamB,
                                group_id: group,
                                round_num: r + 1
                            });
                        }
                    }
                    // Counter-clockwise rotation: remove position 1, append to end
                    const removed = scheduleTeams.splice(1, 1)[0];
                    scheduleTeams.push(removed);
                }
            } else {
                // EVEN teams: Standard Berger Table with home/away swap
                let currentTeams = [...T];
                const R = N - 1;
                const M = N / 2;

                for (let r = 0; r < R; r++) {
                    const swapHomeAway = (r % 2 !== 0);

                    for (let i = 0; i < M; i++) {
                        const teamA = currentTeams[i];
                        const teamB = currentTeams[N - 1 - i];

                        matches.push({
                            team1: swapHomeAway ? teamB : teamA,
                            team2: swapHomeAway ? teamA : teamB,
                            group_id: group,
                            round_num: r + 1
                        });
                    }

                    // Standard rotation: pop last, insert at position 1
                    const lastElement = currentTeams.pop();
                    currentTeams.splice(1, 0, lastElement);
                }
            }
        });

        return matches;
    }

    function generateLeagueDoublePreview(teams) {
        // Reuse Single Round logic to ensure consistency
        const singleMatches = generateLeaguePreview(teams);
        const leg2Matches = [];
        const maxRoundsByGroup = {};

        // Find max round per group to determine offset
        singleMatches.forEach(m => {
            const g = m.group_id;
            if (!maxRoundsByGroup[g] || m.round_num > maxRoundsByGroup[g]) {
                maxRoundsByGroup[g] = m.round_num;
            }
        });

        // Generate Leg 2 by reversing Leg 1 (Single) matches
        singleMatches.forEach(m => {
            const R = maxRoundsByGroup[m.group_id] || 0;
            leg2Matches.push({
                team1: {
                    ...m.team2
                },
                team2: {
                    ...m.team1
                },
                group_id: m.group_id,
                round_num: m.round_num + R
            });
        });

        return [...singleMatches, ...leg2Matches];
    }

    function displayMatchPreviewHTML(stageId, matchPairs) {
        const container = document.getElementById(`stage-matches-${stageId}`);
        if (!container) return;

        if (matchPairs.length === 0) {
            container.innerHTML = '';
            container.style.display = 'none';
            return;
        }

        let html = `
        <div class="stage-matches-container" style="border-top: 2px solid #16478D; padding-top: 1.5rem; margin-top: 1.5rem;">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <h6 class="fw-bold mb-0" style="color: #16478D; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                    <i class="ti ti-check-circle" style="font-size: 1.1rem;"></i>
                    ${langMatchesCreatedPreview}
                </h6>
                <span class="badge match-created-badge" style="background-color: #13DEB9; font-size: 0.75rem; padding: 6px 12px; font-weight: 600;">
                    ${matchPairs.length} ${langMatches}
                </span>
            </div>
    `;

        const groupedMatches = {};
        matchPairs
            .sort((a, b) => {
                const gA = String(a.group_id || '');
                const gB = String(b.group_id || '');
                if (gA !== gB) return gA.localeCompare(gB);
                return (a.round_num || 0) - (b.round_num || 0);
            })
            .forEach(match => {
            const group = match.group_id || langGroupUnspecified;
            if (!groupedMatches[group]) {
                groupedMatches[group] = [];
            }
            groupedMatches[group].push(match);
        });

        let globalMatchIndex = 0;

        Object.keys(groupedMatches).sort().forEach(groupName => {
            const matches = groupedMatches[groupName];

            const isLetter = /^[A-Z]$/.test(groupName);
            const groupText = isLetter ? `${langLane} ${groupName}` : `${groupName}`;

            let groupBadge = '';
            // Get format from stages object or DOM to determine if we should show badge
            const stageFormat = stages[stageId] ? stages[stageId].format : (document.getElementById(`format-${stageId}`)?.value || 'knockout');

            if (groupName !== langGroupUnspecified && stageFormat !== 'knockout') {
                groupBadge = `<span class="badge" style="background-color: #16478D; color: #fff; font-weight: 600; padding: 5px 11px; font-size: 0.78rem;">📍 ${groupText}</span>`;
            }

            matches.forEach((match, idx) => {
                globalMatchIndex++;
                const defaultLogo = '<?= base_url('public/img/jootball_logo.png') ?>';

                const team1Data = teamOptions.find(t => t.id == match.team1.id);
                const team2Data = teamOptions.find(t => t.id == match.team2.id);

                const baseUrl = '<?= rtrim(base_url(), '/') ?>/';
                const resolveLogoPath = (logoPath) => {
                    if (!logoPath) return defaultLogo;
                    if (logoPath.startsWith('http://') || logoPath.startsWith('https://')) return logoPath;
                    return baseUrl + logoPath.replace(/^\/+/, '');
                };

                const team1Logo = resolveLogoPath(team1Data && team1Data.logo ? team1Data.logo : (match.team1.logo || ''));
                const team2Logo = resolveLogoPath(team2Data && team2Data.logo ? team2Data.logo : (match.team2.logo || ''));

                const team1Name = escapeHtml(match.team1.name || '');
                const team2Name = escapeHtml(match.team2.name || '');

                const statusColor = '#D19E11';
                const statusText = langWaitingMatch;

                let roundBadge = '';

                const index1 = (match.team1._originalIndex !== undefined) ? match.team1._originalIndex : -1;
                const index2 = (match.team2._originalIndex !== undefined) ? match.team2._originalIndex : -1;

                html += `
                <div class="match-card-block" style="margin-bottom: 16px;" data-stage-id="${stageId}" data-group="${groupName === langGroupUnspecified ? '' : groupName}" data-team1="${team1Name}" data-team2="${team2Name}" data-status="0" data-stadium="" data-referee="" data-date="">
                    <div class="match-meta-row">
                        ${groupBadge}
                        <span class="badge" style="background-color: #e3edfa; color: #16478D; font-weight: 700; padding: 5px 11px; font-size: 0.78rem; border: 1px solid #c5d6ee;">${langMatchPair} ${globalMatchIndex} ${roundBadge}</span>
                        <label class="match-select-pill" for="chk${match.match_id}" title="${langSelectMatchForBulkEdit}">
                            <input type="checkbox" value="1" class="form-check-input" id="chk${match.match_id}">
                            <span>${langSelectShort}</span>
                        </label>
                    </div>
                    <div class="d-flex align-items-center justify-content-between p-4 rounded-3 match-row-wrapper is-selectable" data-match-id="${match.match_id}" style="gap: 20px; background: #ffffff; box-shadow: 0 4px 18px rgba(22, 71, 141, 0.06); border: 1px solid #eef1f6; border-left: 4px solid #D19E11; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                        <span class="selected-indicator"><i class="ti ti-check"></i></span>
                        <div class="text-center" style="flex: 1; min-width: 0;">
                            <div style="position: relative; width: 64px; height: 64px; margin: 0 auto 12px; border-radius: 12px; padding: 4px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <img src="${team1Logo}" alt="${team1Name}" class="w-100 h-100" style="object-fit: contain;" data-team-index="${index1}" onerror="this.onerror=null;this.src='${defaultLogo}';">
                            </div>
                            <div class="fw-bold text-truncate" style="color: #16478D; font-size: 0.95rem; margin-bottom: 4px;">${team1Name}</div>
                        </div>

                        <div class="text-center d-flex flex-column align-items-center justify-content-center" style="flex: 0 0 140px;">
                            <div class="badge rounded-pill mb-2 px-3 py-2" style="background-color: ${statusColor}15; color: ${statusColor}; font-weight: 700; font-size: 0.75rem; border: 1px solid ${statusColor}30;">
                                <i class="ti ti-clock me-1"></i> ${statusText}
                            </div>

                            <div class="d-flex flex-column align-items-center">
                                <span style="font-size: 1.5rem; font-weight: 800; color: #16478D; line-height: 1;">VS</span>
                                <span class="small text-muted mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                    ${langWaitingSchedule}<br>
                                    <span style="opacity: 0.8;">-</span>
                                </span>
                            </div>
                        </div>

                        <div class="text-center" style="flex: 1; min-width: 0;">
                            <div style="position: relative; width: 64px; height: 64px; margin: 0 auto 12px; border-radius: 12px; padding: 4px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                <img src="${team2Logo}" alt="${team2Name}" class="w-100 h-100" style="object-fit: contain;" data-team-index="${index2}" onerror="this.onerror=null;this.src='${defaultLogo}';">
                            </div>
                            <div class="fw-bold text-truncate" style="color: #16478D; font-size: 0.95rem; margin-bottom: 4px;">${team2Name}</div>
                        </div>

                        <div style="flex: 0 0 auto; border-left: 1px solid #eee; padding-left: 15px;" class="dropdown">
                            <button class="btn btn-light btn-icon rounded-circle" type="button" data-bs-toggle="dropdown" style="color: #7C8FAC; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                <i class="ti ti-settings"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                <li><span class="dropdown-item py-2 text-muted" style="cursor: default;"><i class="ti ti-info-circle me-2"></i> ${langSaveToEdit}</span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            });
        });

        html += `</div>`;

        container.innerHTML = html;
        container.style.display = 'block';
    }

    // Helper to get Round Robin pair using Sliding Pointer + Greedy H/A Balancing (as per document)
    function getRoundRobinPair(nTeams, matchIndex) {
        if (nTeams < 2) return [1, 2];
        let n = nTeams;
        let pairs = [];
        let seen = new Set();
        
        // 1. Generate all unique pairings using distance-based sliding
        for (let d = 1; d <= Math.floor(n / 2); d++) {
            let tempMatches = [];
            for (let i = 1; i <= n; i++) {
                let t1 = i;
                let t2 = (i + d - 1) % n + 1;
                
                let key = [t1, t2].sort((a,b)=>a-b).join('-');
                if (!seen.has(key)) {
                    tempMatches.push([t1, t2]);
                    seen.add(key);
                }
            }
            
            // 2. Reorder distance matches to get sequential pairs first (1-2, 3-4...)
            let reordered = [];
            for (let i = 0; i < tempMatches.length; i += 2) reordered.push(tempMatches[i]);
            for (let i = 1; i < tempMatches.length; i += 2) reordered.push(tempMatches[i]);
            pairs.push(...reordered);
        }
        
        // 3. Assign Home/Away using Greedy Balance Logic (from document Step 4)
        let hCount = {};
        let aCount = {};
        for (let i = 1; i <= n; i++) { hCount[i] = 0; aCount[i] = 0; }
        
        let finalMatches = [];
        for (let i = 0; i < pairs.length; i++) {
            let [u, v] = pairs[i];
            let home, away;
            
            // Priority 1: Team with fewer Home matches so far
            if (hCount[u] < hCount[v]) {
                home = u; away = v;
            } else if (hCount[u] > hCount[v]) {
                home = v; away = u;
            } else {
                // Priority 2: Tie-break using Away count (team needing Away more becomes Away)
                if (aCount[u] < aCount[v]) {
                    home = v; away = u;
                } else {
                    // Priority 3: Final tie-break (lower ID as Home)
                    home = u; away = v;
                }
            }
            
            finalMatches.push([home, away]);
            hCount[home]++;
            aCount[away]++;
        }
        
        return finalMatches[matchIndex - 1] || [0, 0];
    }

    function loadMatchResults(options = {}) {

        $.ajax({
            url: '<?= base_url('admin/tournament/get-tournament-match-results') ?>',
            type: 'GET',
            dataType: 'json',
            data: {
                tour_id: tourId
            },
            beforeSend: function() {

            },
            success: function(response) {
                if (response && response.data && response.data.length > 0) {
                    // Store matches for reference
                    allMatches = {};
                    response.data.forEach(m => {
                        allMatches[m.match_id] = m;
                    });

                    const matchesByRound = {};
                    response.data.forEach(match => {
                        const round = match.match_round || langGroupUnspecified;
                        if (!matchesByRound[round]) {
                            matchesByRound[round] = [];
                        }
                        matchesByRound[round].push(match);
                    });

                    Object.keys(stages).forEach(stageId => {
                        const stageName = document.querySelector(`#stage-${stageId} .stage-name-input`)?.value;
                        if (matchesByRound[stageName]) {

                            updateTeamInputs(stageId);
                        }
                    });

                    let displayedAnyMatches = false;
                    const displayedRounds = new Set();
                    let totalDisplayedMatches = 0;

                    Object.keys(stages).forEach(stageId => {
                        const matchContainer = document.querySelector(`#stage-matches-${stageId}`);
                        if (matchContainer) {
                            matchContainer.innerHTML = '';
                            matchContainer.style.display = 'none';
                        }
                    });

                    Object.keys(stages).forEach(stageId => {
                        const stageName = document.querySelector(`#stage-${stageId} .stage-name-input`)?.value;

                        if (stageName && matchesByRound[stageName]) {
                            displayedAnyMatches = true;
                            totalDisplayedMatches += matchesByRound[stageName].length;

                            // Update match count in header
                            updateStageMatchCount(stageId, matchesByRound[stageName].length);

                            let html = `
                            <div class="stage-matches-container" style="border-top: 2px solid #16478D; padding-top: 1.5rem;">
                                <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-2">
                                    <h6 class="fw-bold mb-0" style="color: #16478D; font-size: 0.95rem; display: flex; align-items: center; gap: 8px;">
                                        <i class="ti ti-check-circle" style="font-size: 1.1rem;"></i>
                                        ${langMatchesCreated}
                                    </h6>
                                    <div class="d-flex align-items-center gap-2">
                                        <button type="button" class="btn btn-sm btn-select-all-stage" data-stage-id="${stageId}" style="background:#fff; border:1px solid #16478D; color:#16478D; border-radius:20px; font-size:0.78rem; font-weight:600; padding:5px 12px; display:inline-flex; align-items:center; gap:6px;">
                                            <i class="ti ti-checkbox"></i>
                                            <span class="select-all-label">${langSelectAll}</span>
                                        </button>
                                        <span class="badge match-created-badge" style="background-color: #13DEB9; font-size: 0.75rem; padding: 6px 12px; font-weight: 600;">
                                            ${matchesByRound[stageName].length} ${langMatches}
                                        </span>
                                    </div>
                                </div>
                        `;

                            let matchIndex = 0;
                            const stage = stages[stageId];
                            const groups = parseInt(document.getElementById(`groups-count-${stageId}`)?.value) || 1;

                            // คำนวณลำดับแมตช์ในแต่ละสาย (group) ตามลำดับ match_id เดิมไว้ก่อน
                            // เพื่อให้การเดาชื่อทีม placeholder (round-robin/knockout) ยังถูกต้อง
                            // แม้ลำดับการแสดงผลจะเปลี่ยนไปเรียงตามวันที่/เวลาแล้วก็ตาม
                            const groupIndexByMatchId = {};
                            const _groupSeqCounts = {};
                            [...matchesByRound[stageName]]
                                .sort((a, b) => {
                                    const gA = a.group_id || 'A';
                                    const gB = b.group_id || 'A';
                                    if (gA !== gB) return gA.localeCompare(gB);
                                    return parseInt(a.match_id) - parseInt(b.match_id);
                                })
                                .forEach((m) => {
                                    const g = m.group_id || 'A';
                                    _groupSeqCounts[g] = (_groupSeqCounts[g] || 0) + 1;
                                    groupIndexByMatchId[m.match_id] = _groupSeqCounts[g];
                                });

                            matchesByRound[stageName]
                                .sort((a, b) => {
                                    // เรียงตามวันที่ → เวลา น้อยไปมาก; คู่ที่ยังไม่กำหนดวัน/เวลา ไปอยู่ท้ายสุด
                                    const dA = (a.date && a.date !== '0000-00-00') ? a.date : '';
                                    const dB = (b.date && b.date !== '0000-00-00') ? b.date : '';
                                    if (dA !== dB) {
                                        if (!dA) return 1;
                                        if (!dB) return -1;
                                        return dA.localeCompare(dB);
                                    }
                                    const tA = a.time || '';
                                    const tB = b.time || '';
                                    if (tA !== tB) {
                                        if (!tA) return 1;
                                        if (!tB) return -1;
                                        return tA.localeCompare(tB);
                                    }
                                    return parseInt(a.match_id) - parseInt(b.match_id);
                                })
                                .forEach((match) => {
                                matchIndex++;

                                const groupLabel = match.group_id || 'A';
                                const currentGroupMatchIndex = groupIndexByMatchId[match.match_id] || 1;

                                const team1IsPlaceholder = !match.team1_id || parseInt(match.team1_id) <= 0;
                                const team2IsPlaceholder = !match.team2_id || parseInt(match.team2_id) <= 0;

                                let team1Name = (!team1IsPlaceholder && match.team1_name) ? escapeHtml(match.team1_name.trim()) : '';
                                let team2Name = (!team2IsPlaceholder && match.team2_name) ? escapeHtml(match.team2_name.trim()) : '';

                                // Slot each side belongs to, straight from the row. A match
                                // with no teams picked yet stores 0 on both id columns, so
                                // this is the only thing that says which fixture it is.
                                // Everything below is fallback for rows saved before the
                                // slot columns existed.
                                const slotLabel = (slot) => {
                                    if (slot === null || slot === undefined || slot === '') return '';
                                    const t = stage.teams && stage.teams[parseInt(slot)];
                                    if (!t) return '';
                                    return escapeHtml(t.custom_slot_label || t.name || '');
                                };

                                if (team1IsPlaceholder) team1Name = slotLabel(match.team1_slot) || team1Name;
                                if (team2IsPlaceholder) team2Name = slotLabel(match.team2_slot) || team2Name;

                                // Resolve placeholder names from Negative IDs (preserved identity)
                                if (parseInt(match.team1_id) < 0) {
                                    const idx1 = -1 - parseInt(match.team1_id);
                                    if (stage.teams && stage.teams[idx1]) {
                                        if (stage.teams[idx1].custom_slot_label) {
                                            team1Name = escapeHtml(stage.teams[idx1].custom_slot_label);
                                        } else if (!team1Name) {
                                            team1Name = escapeHtml(stage.teams[idx1].name || '');
                                        }
                                    }
                                }
                                if (parseInt(match.team2_id) < 0) {
                                    const idx2 = -1 - parseInt(match.team2_id);
                                    if (stage.teams && stage.teams[idx2]) {
                                        if (stage.teams[idx2].custom_slot_label) {
                                            team2Name = escapeHtml(stage.teams[idx2].custom_slot_label);
                                        } else if (!team2Name) {
                                            team2Name = escapeHtml(stage.teams[idx2].name || '');
                                        }
                                    }
                                }

                                const formatElement = document.getElementById(`format-${stageId}`);
                                const format = formatElement ? formatElement.value : 'knockout';
                                const isLeage = format.startsWith('league');

                                if (!team1Name || team1Name === '0' || team1Name === 'null') {
                                    const isLetter = /^[A-Z]$/.test(groupLabel);
                                    const groupChar = isLetter ? groupLabel : 'A';

                                    let pos1;
                                    if (isLeage) {
                                        const totalTeamsVal = parseInt(document.getElementById(`teams-count-${stageId}`)?.value) || 0;
                                        const totalGroupsVal = parseInt(document.getElementById(`groups-count-${stageId}`)?.value) || 1;
                                        const teamsPerGroup = Math.ceil(totalTeamsVal / totalGroupsVal);

                                        const pair = getRoundRobinPair(teamsPerGroup, currentGroupMatchIndex);
                                        pos1 = pair[0];
                                    } else {
                                        pos1 = (currentGroupMatchIndex * 2) - 1;
                                    }

                                    if (stage.teams) {
                                        let groupIdx = 0;
                                        if (isLetter) {
                                            groupIdx = groupLabel.charCodeAt(0) - 65;
                                        }
                                        const totalGroups = parseInt(document.getElementById(`groups-count-${stageId}`)?.value) || 1;
                                        if (groupIdx >= 0 && groupIdx < totalGroups) {
                                            const calculatedIdx = (pos1 - 1) * totalGroups + groupIdx;
                                            if (stage.teams[calculatedIdx]) {
                                                // Check custom label first
                                                if (stage.teams[calculatedIdx].custom_slot_label) {
                                                    team1Name = stage.teams[calculatedIdx].custom_slot_label;
                                                } else if (stage.teams[calculatedIdx].name) {
                                                    team1Name = stage.teams[calculatedIdx].name;
                                                }
                                            }
                                        }
                                    }

                                    if (!team1Name || team1Name === '0' || team1Name === 'null') {
                                        team1Name = groupChar + pos1;
                                    }
                                }

                                if (!team2Name || team2Name === '0' || team2Name === 'null') {
                                    const isLetter = /^[A-Z]$/.test(groupLabel);
                                    const groupChar = isLetter ? groupLabel : 'A';

                                    let pos2;
                                    if (isLeage) {
                                        const totalTeamsVal = parseInt(document.getElementById(`teams-count-${stageId}`)?.value) || 0;
                                        const totalGroupsVal = parseInt(document.getElementById(`groups-count-${stageId}`)?.value) || 1;
                                        const teamsPerGroup = Math.ceil(totalTeamsVal / totalGroupsVal);

                                        const pair = getRoundRobinPair(teamsPerGroup, currentGroupMatchIndex);
                                        pos2 = pair[1];
                                    } else {
                                        pos2 = (currentGroupMatchIndex * 2);
                                    }

                                    if (stage.teams) {
                                        let groupIdx = 0;
                                        if (isLetter) {
                                            groupIdx = groupLabel.charCodeAt(0) - 65;
                                        }
                                        const totalGroups = parseInt(document.getElementById(`groups-count-${stageId}`)?.value) || 1;
                                        if (groupIdx >= 0 && groupIdx < totalGroups) {
                                            const calculatedIdx = (pos2 - 1) * totalGroups + groupIdx;
                                            if (stage.teams[calculatedIdx]) {
                                                // Check custom label first
                                                if (stage.teams[calculatedIdx].custom_slot_label) {
                                                    team2Name = stage.teams[calculatedIdx].custom_slot_label;
                                                } else if (stage.teams[calculatedIdx].name) {
                                                    team2Name = stage.teams[calculatedIdx].name;
                                                }
                                            }
                                        }
                                    }

                                    if (!team2Name || team2Name === '0' || team2Name === 'null') {
                                        team2Name = groupChar + pos2;
                                    }
                                }


                                const defaultLogo = '<?= base_url('public/img/jootball_logo.png') ?>';
                                const baseUrl = '<?= rtrim(base_url(), '/') ?>/';

                                const resolveTeamLogo = (logoPath, teamId) => {
                                    let src = logoPath;
                                    if (!src && teamId) {
                                        const td = teamOptions.find(t => t.id == teamId);
                                        if (td && td.logo) src = td.logo;
                                    }
                                    if (!src) return defaultLogo;
                                    if (src.startsWith('http://') || src.startsWith('https://')) return src;
                                    return baseUrl + src.replace(/^\/+/, '');
                                };

                                const team1Logo = resolveTeamLogo(match.team1_logo, match.team1_id);
                                const team2Logo = resolveTeamLogo(match.team2_logo, match.team2_id);
                                const score1Raw = match.team1_score;
                                const score2Raw = match.team2_score;

                                const isScore1Empty = score1Raw === null || score1Raw === undefined || score1Raw === '';
                                const isScore2Empty = score2Raw === null || score2Raw === undefined || score2Raw === '';

                                const score1 = !isScore1Empty ? score1Raw : '-';
                                const score2 = !isScore2Empty ? score2Raw : '-';

                                let matchDate = '-';
                                if (match.date && match.date !== '0000-00-00') {
                                    const parts = match.date.split('-');
                                    if (parts.length === 3) {
                                        matchDate = `${parts[2]}-${parts[1]}-${parts[0]}`;
                                    } else {
                                        matchDate = match.date;
                                    }
                                }
                                const matchTime = match.time || '-';

                                // Use match_status from database
                                // 0 = รอแข่งขัน, 1 = กำลังแข่งขัน, 3 = จบการแข่งขัน
                                const matchStatus = parseInt(match.match_status) || 0;
                                let statusText = langWaitingMatch;
                                let statusColor = '#D19E11';

                                if (matchStatus === 1) {
                                    statusText = langMatchInProgress;
                                    statusColor = '#dc3545';
                                } else if (matchStatus === 3) {
                                    statusText = langMatchFinished;
                                    statusColor = '#13DEB9';
                                }

                                const hasScores = matchStatus === 3 && !isScore1Empty && !isScore2Empty;

                                let groupDisplay = '';
                                if (groupLabel && format !== 'knockout') {

                                    const isLetter = /^[A-Z]$/.test(groupLabel);
                                    const groupText = isLetter ? `${langLane} ${groupLabel}` : `${langGroup} ${groupLabel}`;
                                    groupDisplay = `<span class="badge" style="background-color: #16478D; color: #fff; font-weight: 600; padding: 5px 11px; font-size: 0.78rem;">📍 ${groupText}</span>`;
                                }

                                const escapedStadium = escapeHtml(match.stadium || '');
                                const escapedReferee = escapeHtml(match.referee || '');
                                html += `
                                <div class="match-card-block" style="margin-bottom: 16px;" data-stage-id="${stageId}" data-group="${groupLabel || ''}" data-team1="${team1Name}" data-team2="${team2Name}" data-status="${matchStatus}" data-stadium="${escapedStadium}" data-referee="${escapedReferee}" data-date="${match.date || ''}">
                                    <div class="match-meta-row">
                                        ${groupDisplay}
                                        <span class="badge" style="background-color: #e3edfa; color: #16478D; font-weight: 700; padding: 5px 11px; font-size: 0.78rem; border: 1px solid #c5d6ee;">${langMatchPair} ${matchIndex}</span>
                                        <label class="match-select-pill" for="chk${match.match_id}" title="${langSelectMatchForBulkEdit}">
                                            <input type="checkbox" value="1" class="form-check-input" id="chk${match.match_id}">
                                            <span>${langSelectShort}</span>
                                        </label>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-between p-4 rounded-3 match-row-wrapper is-selectable" data-match-id="${match.match_id}" style="gap: 20px; background: #ffffff; box-shadow: 0 4px 18px rgba(22, 71, 141, 0.06); border: 1px solid #eef1f6; border-left: 4px solid #D19E11; transition: transform 0.2s ease, box-shadow 0.2s ease;">
                                        <span class="selected-indicator"><i class="ti ti-check"></i></span>
                                        <div class="text-center" style="flex: 1; min-width: 0;">
                                            <div style="position: relative; width: 64px; height: 64px; margin: 0 auto 12px; border-radius: 12px; padding: 4px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                                <img src="${team1Logo}" alt="${team1Name}" class="w-100 h-100" style="object-fit: contain;" onerror="this.onerror=null;this.src='${defaultLogo}';">
                                            </div>
                                            <div class="fw-bold text-truncate" style="color: #16478D; font-size: 0.95rem; margin-bottom: 4px;">${team1Name}</div>
                                        </div>

                                        <div class="text-center d-flex flex-column align-items-center justify-content-center" style="flex: 0 0 140px;">
                                            <div class="badge rounded-pill mb-2 px-3 py-2" style="background-color: ${statusColor}15; color: ${statusColor}; font-weight: 700; font-size: 0.75rem; border: 1px solid ${statusColor}30;">
                                                <i class="ti ti-clock me-1"></i> ${statusText}
                                            </div>

                                            <div class="d-flex flex-column align-items-center">
                                                <span style="font-size: 1.5rem; font-weight: 800; color: #16478D; line-height: 1;">
                                                    ${hasScores ? `${score1} : ${score2}` : 'VS'}
                                                </span>
                                                <span class="small text-muted mt-1" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                                                    ${matchDate !== '-' ? matchDate : langWaitingSchedule}<br>
                                                    <span style="opacity: 0.8;">${matchTime !== '-' ? matchTime : ''}</span>
                                                </span>
                                                ${escapedStadium ? `<span class="small text-muted mt-1 d-inline-flex align-items-center" style="font-size: 0.72rem; opacity: 0.9; gap: 3px;"><i class="ti ti-map-pin"></i>${escapedStadium}</span>` : ''}
                                                <button type="button" class="btn btn-sm swap-teams-btn mt-2"
                                                    data-match-id="${match.match_id}"
                                                    title="${langSwapTeams}"
                                                    aria-label="${langSwapTeams}"
                                                    style="border:1px solid #c5d6ee; background:#f2f7ff; color:#16478D; border-radius:50%; width:30px; height:30px; padding:0; display:inline-flex; align-items:center; justify-content:center; flex:0 0 auto;">
                                                    <i class="ti ti-arrows-exchange"></i>
                                                </button>
                                            </div>
                                        </div>

                                        <div class="text-center" style="flex: 1; min-width: 0;">
                                            <div style="position: relative; width: 64px; height: 64px; margin: 0 auto 12px; border-radius: 12px; padding: 4px; background: #fff; box-shadow: 0 2px 8px rgba(0,0,0,0.08);">
                                                <img src="${team2Logo}" alt="${team2Name}" class="w-100 h-100" style="object-fit: contain;" onerror="this.onerror=null;this.src='${defaultLogo}';">
                                            </div>
                                            <div class="fw-bold text-truncate" style="color: #16478D; font-size: 0.95rem; margin-bottom: 4px;">${team2Name}</div>
                                        </div>

                                        <div style="flex: 0 0 auto; border-left: 1px solid #eee; padding-left: 15px;" class="dropdown" data-stage-id="${stageId}">
                                            <button class="btn btn-light btn-icon rounded-circle" type="button" data-bs-toggle="dropdown" style="color: #7C8FAC; width: 32px; height: 32px; display: flex; align-items: center; justify-content: center;">
                                                <i class="ti ti-settings"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0">
                                                <li>
                                                    <a class="dropdown-item py-2"
                                                       href="javascript:void(0)"
                                                       onclick="editMatchInline(this, '${tourId}', '${match.match_id}', '${stageId}')">
                                                        <i class="ti ti-edit me-2"></i> ${langEditDetails}
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li><span class="dropdown-item text-danger py-2" onclick="deleteMatch(this, '${match.match_id}', tourId)" style="cursor: pointer; font-weight: 600;"><i class="ti ti-trash me-2"></i> ${langDeleteMatch}</span></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            `;
                            });

                            html += `
                            </div>
                        `;

                            const matchContainer = document.querySelector(`#stage-matches-${stageId}`);
                            if (matchContainer) {
                                matchContainer.innerHTML = html;
                                matchContainer.style.display = 'block';
                            }
                        }
                    });

                    // Update total match count from displayed matches
                    updateTotalMatchCount(totalDisplayedMatches);

                    const firstStageWithMatches = Object.keys(stages).find(sid => {
                        const stageNameInput = document.querySelector(`#stage-${sid} .stage-name-input`);
                        const stageName = stageNameInput?.value;
                        return stageName && matchesByRound[stageName];
                    });

                    if (firstStageWithMatches && !options.noScroll) {

                        setTimeout(() => {
                            const stageMatchesElement = document.querySelector(`#stage-matches-${firstStageWithMatches}`);
                            if (stageMatchesElement) {
                                stageMatchesElement.scrollIntoView({
                                    behavior: 'smooth',
                                    block: 'center'
                                });

                                stageMatchesElement.style.backgroundColor = 'rgba(19, 222, 185, 0.1)';
                                setTimeout(() => {
                                    stageMatchesElement.style.backgroundColor = 'transparent';
                                    stageMatchesElement.style.transition = 'background-color 0.5s ease-out';
                                }, 100);
                            }
                        }, 300);
                    }
                }
            },
            error: function(xhr) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 4000,
                    timerProgressBar: true
                });
                Toast.fire({
                    icon: 'error',
                    title: langCannotLoadMatchResults
                });
            }
        });
    }

    function editMatchInline(clickedElement, tourId, matchId, stageId) {
        // Close any existing inline edit forms and remove their datepickers safely
        $('.inline-edit-form').each(function() {
            const $existingForm = $(this);
            const $existingDateField = $existingForm.find('.date-input');
            try {
                if ($existingDateField.length && $existingDateField.data('datepicker')) {
                    $existingDateField.datepicker('remove');
                }
            } catch (e) {
                console.log('Datepicker cleanup warning:', e);
            }
        });
        $('.inline-edit-form').slideUp(300, function() {
            $(this).remove();
        });

        const $matchCard = $(clickedElement).closest('.dropdown').parent();
        const $matchWrapper = $matchCard.parent();

        if (!$matchWrapper.length) {
            Swal.fire(langErrorTitle, langMatchNotFound, 'error');
            return;
        }

        $.ajax({
            url: '<?= base_url('/admin/tournament/get-match-details') ?>/' + tourId + '/' + matchId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response && response.data) {
                    const matchData = response.data;

                    const template = document.getElementById('inline-edit-form-template');
                    const $editForm = $(template.content.cloneNode(true)).find('.inline-edit-form');

                    // Extract team names from the match card DOM for consistent display
                    const teamNames = $matchCard.find('.fw-bold.text-truncate');
                    const team1Name = $(teamNames[0]).text().trim() || langHomeTeam;
                    const team2Name = $(teamNames[1]).text().trim() || langAwayTeam;

                    $editForm.find('.team1-label').text(team1Name);
                    $editForm.find('.team2-label').text(team2Name);

                    let teamOptionsHtml = '<option value="">' + langSelectTeam + '</option>';
                    if (stageId && stages[stageId]) {
                        const stageTeamIds = stages[stageId].teams
                            .filter(t => t.id && !String(t.id).startsWith('auto_'))
                            .map(t => t.id);
                        teamOptions.forEach(team => {
                            if (stageTeamIds.includes(team.id)) {
                                teamOptionsHtml += `<option value="${team.id}">${escapeHtml(team.name)}</option>`;
                            }
                        });
                    } else {
                        teamOptions.forEach(team => {
                            teamOptionsHtml += `<option value="${team.id}">${escapeHtml(team.name)}</option>`;
                        });
                    }

                    $editForm.find('.match-id-field').val(matchId);
                    $editForm.find('.tour-id-field').val(tourId);
                    $editForm.find('.team1-select').html(teamOptionsHtml).val(matchData.team1_id || '');
                    $editForm.find('.team2-select').html(teamOptionsHtml).val(matchData.team2_id || '');

                    if (matchData.date) {
                        const dateParts = matchData.date.split('-');
                        const formattedDate = dateParts[2] + '/' + dateParts[1] + '/' + dateParts[0];
                        $editForm.find('.date-input').val(formattedDate);
                    }

                    $editForm.find('.time-input').val(matchData.time || '');
                    $editForm.find('.stadium-input').val(matchData.stadium || '');
                    $editForm.find('.referee-input').val(matchData.referee || '');
                    $editForm.find('.group-select').val(matchData.group_id || '');

                    $matchWrapper.after($editForm);

                    const stageFormat = stages[stageId] ? stages[stageId].format : 'knockout';
                    if (stageFormat === 'knockout') {
                        $editForm.find('.group-field-wrapper').hide();
                    }


                    $editForm.hide().slideDown(400, function() {
                        // Initialize datepicker after form is visible
                        const $dateInput = $editForm.find('.date-input');
                        try {
                            if ($dateInput.data('datepicker')) {
                                $dateInput.datepicker('remove');
                            }
                            $dateInput.datepicker({
                                format: "dd/mm/yyyy",
                                autoclose: true,
                                language: 'th-en',
                            });
                        } catch (e) {
                            console.error('Datepicker initialization error:', e);
                        }

                        // Initialize Select2 for stadium dropdown
                        const $stadiumEl = $editForm.find('.stadium-select2');
                        if ($stadiumEl.length) {
                            initStadiumSelect2($stadiumEl);
                        }
                    });

                    const $timeInput = $editForm.find('.time-input');
                    if (typeof $timeInput.mask === 'function') {
                        $timeInput.mask('00:00');
                    }

                    $editForm.find('.cancel-inline-edit').on('click', function() {
                        // Remove datepicker before removing form
                        const $dateField = $editForm.find('.date-input');
                        try {
                            if ($dateField.length && $dateField.data('datepicker')) {
                                $dateField.datepicker('remove');
                            }
                        } catch (e) {
                            console.log('Datepicker cleanup warning:', e);
                        }
                        $editForm.slideUp(300, function() {
                            $(this).remove();
                        });
                    });

                    $editForm.find('.save-inline-edit').on('click', function() {
                        saveInlineEditMatch($editForm, 'save');
                    });

                } else {
                    Swal.fire(langErrorTitle, langCannotLoadMatchData, 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = langErrorLoadingMatchData;
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                }
                Swal.fire(langErrorTitle, errorMsg, 'error');
            }
        });
    }

    function saveInlineEditMatch($form, actionType = 'save') {
        const $formElement = $form.find('form');

        if ($formElement.length === 0) {
            Swal.fire(langErrorTitle, langFormNotFound, 'error');
            console.error('Form element not found in:', $form);
            return;
        }

        // Convert date format from dd/mm/yyyy to yyyy-mm-dd
        const dateInput = $formElement.find('.date-input').val();
        let formattedDate = '';
        if (dateInput) {
            const dateParts = dateInput.split('/');
            if (dateParts.length === 3) {
                // Convert from dd/mm/yyyy to yyyy-mm-dd
                formattedDate = dateParts[2] + '-' + dateParts[1] + '-' + dateParts[0];
            }
        }

        // Get form data as object
        const formDataObj = {};
        formDataObj['action_type'] = actionType;
        $formElement.serializeArray().forEach(item => {
            if (item.name === 'date' && formattedDate) {
                formDataObj[item.name] = formattedDate;
            } else {
                formDataObj[item.name] = item.value;
            }
        });

        console.log('Form data being sent:', formDataObj);

        $.ajax({
            url: '<?= base_url('/admin/tournament/save-tournament-match-result') ?>',
            type: 'POST',
            data: formDataObj,
            dataType: 'json',
            beforeSend: function() {
                Swal.fire({
                    title: langSavingData,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire(langSuccess, langMatchSaved, 'success').then(() => {
                        // Remove datepicker before removing form
                        const $dateField = $form.find('.date-input');
                        try {
                            if ($dateField.length && $dateField.data('datepicker')) {
                                $dateField.datepicker('remove');
                            }
                        } catch (e) {
                            console.log('Datepicker cleanup warning:', e);
                        }
                        $form.slideUp(300, function() {
                            $(this).remove();
                        });
                        loadMatchResults({ noScroll: true });
                    });
                } else {
                    Swal.fire(langErrorTitle, response.msg || langCannotSaveData, 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = langErrorSavingData;
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                } else if (xhr.status === 0) {
                    errorMsg = langCannotConnectServer;
                } else if (xhr.status >= 500) {
                    errorMsg = langServerError + ' (Error ' + xhr.status + ')';
                }
                Swal.fire(langErrorTitle, errorMsg, 'error');
            }
        });
    }

    function deleteMatch(element, matchId, tourId) {
        Swal.fire({
            title: langConfirmDeleteMatch,
            text: langDeleteMatchText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#a82e2e',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langDelete,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {

                $.ajax({
                    url: '<?= base_url('/admin/tournament/delete-match') ?>/' + matchId,
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        tour_id: tourId,
                        match_id: matchId
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire(langDeleteSuccess, langMatchDeleted, 'success').then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire(langErrorTitle, response.msg || langCannotDeleteMatch, 'error');
                        }
                    },
                    error: function(xhr) {
                        Swal.fire(langErrorTitle, langCannotConnectServer, 'error');
                    }
                });
            }
        });
    }

    // Swap home/away (left<->right) of a saved match.
    $(document).on('click', '.swap-teams-btn', function() {
        const matchId = $(this).data('match-id');
        if (!matchId) return;
        Swal.fire({
            title: langSwapTeams,
            text: langSwapConfirmText,
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#16478D',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langSwapTeams,
            cancelButtonText: langCancel
        }).then((result) => {
            if (!result.isConfirmed) return;
            $.ajax({
                url: '<?= base_url('/admin/tournament/swap-match-teams') ?>',
                type: 'POST',
                dataType: 'json',
                data: { match_id: matchId },
                success: function(response) {
                    if (response && response.status === 'success') {
                        loadMatchResults({ noScroll: true });
                    } else {
                        Swal.fire(langErrorTitle, (response && response.msg) || langCannotConnectServer, 'error');
                    }
                },
                error: function() {
                    Swal.fire(langErrorTitle, langCannotConnectServer, 'error');
                }
            });
        });
    });

    function populateTeamSelects() {
        let teamOptionsHtml = '<option value="">' + langSelectTeam + '</option>';

        if (teamOptions.length > 0) {
            teamOptions.forEach(team => {
                teamOptionsHtml += `<option value="${team.id}">${team.name}</option>`;
            });
        }

        $('#modalTeam1Id').html(teamOptionsHtml);
        $('#modalTeam2Id').html(teamOptionsHtml);
    }

    function populateTeamSelectsFromStage(stage) {
        let teamOptionsHtml = '<option value="">' + langSelectTeam + '</option>';

        if (stage && stage.teams && stage.teams.length > 0) {

            const stageTeamIds = stage.teams
                .filter(t => t.id && !String(t.id).startsWith('auto_'))
                .map(t => t.id);

            teamOptions.forEach(team => {
                if (stageTeamIds.includes(team.id)) {
                    teamOptionsHtml += `<option value="${team.id}">${team.name}</option>`;
                }
            });
        }

        if (teamOptionsHtml === '<option value="">' + langSelectTeam + '</option>' && teamOptions.length > 0) {
            teamOptionsHtml = '<option value="">' + langSelectTeam + '</option>';
            teamOptions.forEach(team => {
                teamOptionsHtml += `<option value="${team.id}">${team.name}</option>`;
            });
        }

        $('#modalTeam1Id').html(teamOptionsHtml);
        $('#modalTeam2Id').html(teamOptionsHtml);
    }

    function saveMatchChanges() {

        const team1Id = $('#modalTeam1Id').val();
        const team2Id = $('#modalTeam2Id').val();
        const dateVal = $('#modalDate').val();
        const timeVal = $('#modalTime').val();

        if (!team1Id || !team2Id || !dateVal || !timeVal) {
            Swal.fire(langErrorTitle, langPleaseFilAllRequired, 'error');
            return;
        }

        if (team1Id === team2Id) {
            Swal.fire(langErrorTitle, langHomeAwayMustDiffer, 'error');
            return;
        }

        const dateInput = $('#modalDate').val();
        const dateParts = dateInput.split('/');

        if (dateParts.length !== 3 || !dateParts[0] || !dateParts[1] || !dateParts[2]) {
            Swal.fire(langErrorTitle, langDateFormatInvalid, 'error');
            return;
        }

        const day = parseInt(dateParts[0]);
        const month = parseInt(dateParts[1]);
        const year = parseInt(dateParts[2]);

        if (isNaN(day) || isNaN(month) || isNaN(year) || day < 1 || day > 31 || month < 1 || month > 12 || year < 2000) {
            Swal.fire(langErrorTitle, langDateValueInvalid, 'error');
            return;
        }

        const testDate = new Date(year, month - 1, day);
        if (testDate.getFullYear() !== year || testDate.getMonth() !== (month - 1) || testDate.getDate() !== day) {
            Swal.fire(langErrorTitle, langDateNotExist.replace('%s', `${day}/${month}/${year}`), 'error');
            return;
        }

        const formattedDate = dateParts[2].padStart(4, '0') + '-' +
            dateParts[1].padStart(2, '0') + '-' +
            dateParts[0].padStart(2, '0');

        const matchData = {
            match_id: $('#matchIdField').val(),
            tour_id: $('#tourIdField').val(),
            team1_id: team1Id,
            team2_id: team2Id,
            date: formattedDate,
            time: timeVal,
            stadium: $('#modalStadium').length ? $('#modalStadium').val() : '',
            referee: $('#modalReferee').length ? $('#modalReferee').val() : '',
            group_id: $('#modalGroupId').length ? $('#modalGroupId').val() : ''
        };

        $.ajax({
            url: '<?= base_url('/admin/tournament/save-tournament-match-result') ?>',
            type: 'POST',
            dataType: 'json',
            data: matchData,
            beforeSend: function() {
                Swal.fire({
                    title: langSavingData,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire(langSuccess, langMatchSaved, 'success').then(() => {

                        const modalElement = document.getElementById('editMatchModal');
                        if (modalElement) {
                            const modalInstance = bootstrap.Modal.getInstance(modalElement);
                            if (modalInstance) {
                                modalInstance.hide();
                            }
                        }

                        loadMatchResults({ noScroll: true });
                    });
                } else {
                    Swal.fire(langErrorTitle, response.msg || langCannotSaveData, 'error');
                }
            },
            error: function(xhr) {

                let errorMsg = langErrorSavingData;
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                } else if (xhr.status === 0) {
                    errorMsg = langCannotConnectServer;
                } else if (xhr.status >= 500) {
                    errorMsg = langServerError + ' (Error ' + xhr.status + ')';
                }

                Swal.fire({
                    title: langErrorTitle,
                    text: errorMsg,
                    icon: 'error',
                    showCancelButton: true,
                    confirmButtonText: langTryAgain,
                    cancelButtonText: langCancel,
                    confirmButtonColor: '#16478D'
                }).then((result) => {
                    if (result.isConfirmed) {

                        saveMatchChanges();
                    }
                });
            }
        });
    }

    $(document).ready(async function() {

        if (teamOptions.length === 0) {
            Swal.fire({
                title: langNoApprovedTeams,
                html: '<p>' + langCanCreateMatchesButAddTeamsLater + '<br><small class="text-muted">' + langGoToTeamsPage + '</small></p>',
                icon: 'info',
                confirmButtonText: langOk
            });
        }

        const btnAddStage = document.getElementById('btn-add-new-stage');
        const btnAddMoreStages = document.getElementById('btn-add-more-stage');

        if (btnAddStage) {
            $(btnAddStage).on('click', function() {
                $('#stages-container').show();
                $('#empty-state-wrapper').hide();
                $('#add-more-stages-wrapper').show();

                if (stageCounter === 0) {
                    addNewStage();
                }
            });
        }

        if (btnAddMoreStages) {
            $(btnAddMoreStages).on('click', function() {
                addNewStage();
            });
        }

        await loadSavedStages();

        loadMatchResults();
    });

    // ==================== Import Match Setup ====================
    let importTournaments = [];

    function openImportModal() {
        const modalEl = document.getElementById('importMatchSetupModal');
        // ย้าย modal ไป body เพื่อไม่ให้ CSS ของ parent container ทำให้ตำแหน่งผิด
        if (modalEl.parentElement !== document.body) {
            document.body.appendChild(modalEl);
        }
        // ปิด backdrop ของ Bootstrap แล้วสร้างเอง
        const modal = new bootstrap.Modal(modalEl, { backdrop: false });

        // สร้าง backdrop manual
        let backdrop = document.getElementById('import-modal-backdrop');
        if (!backdrop) {
            backdrop = document.createElement('div');
            backdrop.id = 'import-modal-backdrop';
            backdrop.style.cssText = 'position:fixed;top:0;left:0;width:100%;height:100%;background:rgba(0,0,0,0.5);z-index:1055;';
            backdrop.addEventListener('click', function() {
                modal.hide();
            });
        }
        document.body.appendChild(backdrop);

        modal.show();
        loadTournamentsForImport();
    }

    // ลบ backdrop ตอนปิด modal
    document.getElementById('importMatchSetupModal').addEventListener('hidden.bs.modal', function () {
        const backdrop = document.getElementById('import-modal-backdrop');
        if (backdrop) backdrop.remove();
        document.body.classList.remove('modal-open');
        document.body.style.removeProperty('overflow');
        document.body.style.removeProperty('padding-right');
    });

    function loadTournamentsForImport() {
        const listEl = document.getElementById('import-tournament-list');
        listEl.innerHTML = '<div class="text-center py-4"><div class="spinner-border text-primary" role="status"></div></div>';

        $.ajax({
            url: '<?= base_url("admin/tournament/get-tournaments-with-stages") ?>',
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    importTournaments = response.data.filter(t => String(t.tour_id) !== String(tourId));
                    renderImportList(importTournaments);
                } else {
                    listEl.innerHTML = '<div class="text-center py-4 text-muted">' + langNoTournamentFound + '</div>';
                }
            },
            error: function() {
                listEl.innerHTML = '<div class="text-center py-4 text-danger">' + langServerError + '</div>';
            }
        });
    }

    function renderImportList(tournaments) {
        const listEl = document.getElementById('import-tournament-list');

        if (tournaments.length === 0) {
            listEl.innerHTML = '<div class="text-center py-4 text-muted">' + langNoTournamentFound + '</div>';
            return;
        }

        let html = '';
        tournaments.forEach(function(tour) {
            const tourFallback = '<?= base_url('public/img/jootball_logo.png') ?>';
            const logoUrl = tour.logo ? '<?= base_url() ?>/' + tour.logo : '';
            const logoHtml = logoUrl
                ? '<img src="' + logoUrl + '" class="rounded-circle me-3" style="width:40px;height:40px;object-fit:cover;" onerror="this.onerror=null;this.src=\'' + tourFallback + '\';">'
                : '<div class="rounded-circle me-3 bg-primary d-flex align-items-center justify-content-center" style="width:40px;height:40px;"><i class="ti ti-trophy text-white"></i></div>';

            html += '<div class="import-tour-item d-flex align-items-center p-3 border rounded-3 mb-2" style="cursor:pointer;transition:all 0.2s;" ' +
                'onmouseover="this.style.backgroundColor=\'#f0f7ff\';this.style.borderColor=\'#4e8cff\'" ' +
                'onmouseout="this.style.backgroundColor=\'\';this.style.borderColor=\'\'" ' +
                'onclick="confirmImport(' + tour.tour_id + ', \'' + escapeHtml(tour.name) + '\')">' +
                logoHtml +
                '<div class="flex-grow-1">' +
                '<div class="fw-bold">' + escapeHtml(tour.name) + '</div>' +
                '<div class="small text-muted">' +
                '<span class="me-3"><i class="ti ti-layers-intersect me-1"></i>' + langStagesCount + ': ' + tour.stages_count + '</span>' +
                '<span><i class="ti ti-ball-football me-1"></i>' + langMatchesCount + ': ' + tour.matches_count + '</span>' +
                '</div>' +
                '</div>' +
                '<i class="ti ti-chevron-right text-muted"></i>' +
                '</div>';
        });

        listEl.innerHTML = html;
    }

    // Search filter
    $(document).on('input', '#import-search-input', function() {
        const keyword = $(this).val().toLowerCase();
        const filtered = importTournaments.filter(function(t) {
            return (t.name && t.name.toLowerCase().indexOf(keyword) !== -1) ||
                   (t.name_en && t.name_en.toLowerCase().indexOf(keyword) !== -1);
        });
        renderImportList(filtered);
    });

    function confirmImport(sourceTourId, sourceName) {
        // ปิด modal ก่อน
        bootstrap.Modal.getInstance(document.getElementById('importMatchSetupModal')).hide();

        Swal.fire({
            title: langConfirmImport,
            html: langConfirmImportText + '<br><br><strong>' + sourceName + '</strong>',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#16478D',
            cancelButtonColor: '#d33',
            confirmButtonText: langYesImport,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                doImport(sourceTourId);
            }
        });
    }

    function doImport(sourceTourId) {
        Swal.fire({
            title: langSaving,
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });

        $.ajax({
            url: '<?= base_url("admin/tournament/import-match-setup") ?>',
            type: 'POST',
            data: {
                source_tour_id: sourceTourId,
                target_tour_id: tourId,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: langImportSuccess,
                        text: langStagesCount + ': ' + response.stages_imported + ' | ' + langMatchesCount + ': ' + response.matches_imported,
                        confirmButtonColor: '#16478D'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: langImportError,
                        text: response.msg
                    });
                }
            },
            error: function() {
                Swal.fire({
                    icon: 'error',
                    title: langImportError,
                    text: langServerError
                });
            }
        });
    }

    // ===== Stadium Select2 + Quick Add =====
    const langSelectStadium = '<?= lang("Common.select_stadium") ?>';
    const langAddNewStadium = '<?= lang("Common.add_new_stadium") ?>';
    const langNewStadiumName = '<?= lang("Common.new_stadium_name") ?>';
    const langStadiumNameRequired = '<?= lang("Common.stadium_name_required") ?>';
    const langStadiumNameDuplicate = '<?= lang("Common.stadium_name_duplicate") ?>';
    const langStadiumAddedSuccess = '<?= lang("Common.stadium_added_success") ?>';

    // Stadium options data (สำหรับ clone ให้ inline form)
    const stadiumOptions = <?= json_encode($stadiums ?? []) ?>;

    function initStadiumSelect2($el) {
        if ($el.hasClass('select2-hidden-accessible')) {
            $el.select2('destroy');
        }
        $el.select2({
            placeholder: langSelectStadium,
            allowClear: true,
            width: '100%'
        });
    }


    // ปุ่ม "เพิ่มสนามใหม่"
    $(document).on('click', '.btn-add-stadium', function() {
        const $btn = $(this);
        const $select = $btn.closest('.d-flex').find('.stadium-select2');

        Swal.fire({
            title: langAddNewStadium,
            input: 'text',
            inputLabel: langNewStadiumName,
            inputPlaceholder: langNewStadiumName,
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-check me-1"></i> ' + langSave,
            cancelButtonText: langCancel,
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return langStadiumNameRequired;
                }
            },
            showLoaderOnConfirm: true,
            preConfirm: (name) => {
                return $.ajax({
                    url: '<?= base_url("admin/stadium/quick-add") ?>',
                    method: 'POST',
                    data: { name: name.trim() },
                    dataType: 'json'
                }).then(function(res) {
                    if (res.status === 'duplicate') {
                        Swal.showValidationMessage(langStadiumNameDuplicate);
                        return false;
                    }
                    if (res.status === 'error') {
                        Swal.showValidationMessage(res.msg);
                        return false;
                    }
                    return res;
                }).catch(function() {
                    Swal.showValidationMessage(langErrorTitle);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.status === 'success') {
                const newName = result.value.stadium.name;

                // เพิ่ม option ใหม่ในทุก stadium select ที่มีอยู่
                $('.stadium-select2').each(function() {
                    if ($(this).find('option[value="' + newName + '"]').length === 0) {
                        $(this).append(new Option(newName, newName, false, false));
                    }
                });

                // อัปเดต stadiumOptions array
                stadiumOptions.push({ id: result.value.stadium.id, name: newName, name_en: '' });

                // เลือกสนามใหม่ใน select ที่กดปุ่ม
                $select.val(newName).trigger('change');

                Swal.fire({
                    icon: 'success',
                    title: langStadiumAddedSuccess,
                    timer: 1200,
                    showConfirmButton: false
                });
            }
        });
    });

    // ==================== Match Filter Logic ====================
    const matchFilter = {
        search: '',
        stages: new Set(),
        groups: new Set(),
        statuses: new Set(),
        dateFrom: '',
        dateTo: '',
    };

    function populateFilterOptions() {
        // Stage chips — from rendered stage cards
        const $stageChips = $('#filter-stage-chips').empty();
        const stageCards = $('.stage-card');
        if (stageCards.length === 0) {
            $stageChips.append('<div class="text-muted small fst-italic">-</div>');
        } else {
            stageCards.each(function(idx) {
                const sid = this.id.replace(/^stage-/, '');
                const sname = $(this).find('.stage-name-input').val() || `${langStageNumber} ${sid}`;
                const isActive = matchFilter.stages.has(sid);
                const safeName = $('<div>').text(sname).html();
                $stageChips.append(
                    `<span class="filter-chip chip-stage ${isActive ? 'is-active' : ''}" data-stage-id="${sid}" title="${safeName}">` +
                    `<span class="chip-stage-num">${idx + 1}</span>` +
                    `<span class="chip-stage-label">${safeName}</span>` +
                    `</span>`
                );
            });
        }

        // Group chips — distinct values from rendered match cards (square grid)
        const $groupChips = $('#filter-group-chips').empty();
        const groups = new Set();
        $('.match-card-block').each(function() {
            const g = $(this).data('group');
            if (g) groups.add(String(g));
        });
        if (groups.size === 0) {
            $groupChips.empty().append('<div class="text-muted small fst-italic" style="grid-column: 1/-1;">-</div>');
        } else {
            Array.from(groups).sort().forEach(g => {
                const isActive = matchFilter.groups.has(g);
                const safe = $('<div>').text(g).html();
                $groupChips.append(`<span class="filter-chip-square ${isActive ? 'is-active' : ''}" data-group="${safe}">${safe}</span>`);
            });
        }
    }

    function updateFilterCountBadge() {
        let count = 0;
        if (matchFilter.search) count++;
        if (matchFilter.stages.size) count++;
        if (matchFilter.groups.size) count++;
        if (matchFilter.statuses.size) count++;
        if (matchFilter.dateFrom || matchFilter.dateTo) count++;
        const $btn = $('#btn-open-filter');
        $btn.find('#filter-active-count').text(count);
        $btn.toggleClass('has-active', count > 0);
    }

    function dateToISO(d) {
        // Convert dd/mm/yyyy or dd-mm-yyyy → yyyy-mm-dd; or pass through if already ISO
        if (!d) return '';
        if (/^\d{4}-\d{2}-\d{2}$/.test(d)) return d;
        const parts = d.split(/[\/\-]/);
        if (parts.length === 3) {
            // Assume dd/mm/yyyy
            return `${parts[2]}-${parts[1].padStart(2, '0')}-${parts[0].padStart(2, '0')}`;
        }
        return '';
    }

    let _filterApplyingFlag = false;
    function applyMatchFilters() {
        if (_filterApplyingFlag) return;
        _filterApplyingFlag = true;
        const searchLower = (matchFilter.search || '').trim().toLowerCase();
        const fromISO = dateToISO(matchFilter.dateFrom);
        const toISO = dateToISO(matchFilter.dateTo);

        // Iterate match cards
        $('.match-card-block').each(function() {
            const $c = $(this);
            const stageId = String($c.data('stage-id') || '');
            const group = String($c.data('group') || '');
            const status = String($c.data('status') || '0');
            const team1 = String($c.data('team1') || '').toLowerCase();
            const team2 = String($c.data('team2') || '').toLowerCase();
            const stadium = String($c.data('stadium') || '').toLowerCase();
            const referee = String($c.data('referee') || '').toLowerCase();
            const date = String($c.data('date') || '');

            let visible = true;

            if (matchFilter.stages.size && !matchFilter.stages.has(stageId)) visible = false;
            if (visible && matchFilter.groups.size && !matchFilter.groups.has(group)) visible = false;
            if (visible && matchFilter.statuses.size && !matchFilter.statuses.has(status)) visible = false;
            if (visible && searchLower) {
                const hay = `${team1} ${team2} ${stadium} ${referee}`;
                if (!hay.includes(searchLower)) visible = false;
            }
            if (visible && fromISO && date && date < fromISO) visible = false;
            if (visible && toISO && date && date > toISO) visible = false;
            // If a date filter is set but match has no date → hide
            if (visible && (fromISO || toISO) && !date) visible = false;

            $c.toggle(visible);
        });

        // Hide entire stage cards that aren't in selected stages (when stage filter active)
        $('.stage-card').each(function() {
            const sid = this.id.replace(/^stage-/, '');
            const shouldShow = matchFilter.stages.size === 0 || matchFilter.stages.has(sid);
            $(this).toggle(shouldShow);
        });

        // Show empty message per stage only when filter is active AND all blocks are filter-hidden
        const anyFilterActive = !!searchLower || matchFilter.stages.size > 0 || matchFilter.groups.size > 0 || matchFilter.statuses.size > 0 || !!fromISO || !!toISO;
        $('.stage-card:visible').each(function() {
            const $stage = $(this);
            $stage.find('.filter-empty-msg').remove();
            if (!anyFilterActive) return;
            const $blocks = $stage.find('.match-card-block');
            if ($blocks.length === 0) return;
            // Count blocks hidden by our inline style (set via $.toggle(false))
            const filteredOut = $blocks.filter(function() { return this.style.display === 'none'; }).length;
            if (filteredOut === $blocks.length) {
                const $container = $blocks.first().parent();
                $container.append(`<div class="filter-empty-msg"><i class="ti ti-filter-off"></i>${langFilterNoMatchesInStage}</div>`);
            }
        });
        // Also clean up empty-msg in hidden stages
        $('.stage-card:hidden .filter-empty-msg').remove();

        updateFilterCountBadge();
        // Release flag on next frame so observer doesn't immediately re-fire
        requestAnimationFrame(() => { _filterApplyingFlag = false; });
    }

    function resetMatchFilters() {
        matchFilter.search = '';
        matchFilter.stages.clear();
        matchFilter.groups.clear();
        matchFilter.statuses.clear();
        matchFilter.dateFrom = '';
        matchFilter.dateTo = '';
        $('#filter-search').val('');
        $('#filter-date-from').val('');
        $('#filter-date-to').val('');
        $('#matchFilterDrawer .filter-chip, #matchFilterDrawer .filter-chip-square').removeClass('is-active');
        applyMatchFilters();
    }

    // Show filter button when any matches are rendered
    function refreshFilterButtonVisibility() {
        const hasAny = $('.match-card-block').length > 0;
        $('#btn-open-filter').toggle(hasAny);
    }

    // Event handlers — drawer open populates options + toggles backdrop body class
    $('#matchFilterDrawer').on('show.bs.offcanvas', function() {
        document.body.classList.add('filter-drawer-open');
        populateFilterOptions();
        $('#filter-search').val(matchFilter.search);
        $('#filter-date-from').val(matchFilter.dateFrom);
        $('#filter-date-to').val(matchFilter.dateTo);
        // Status chips
        $('#matchFilterDrawer .filter-chip[data-status]').each(function() {
            $(this).toggleClass('is-active', matchFilter.statuses.has(String($(this).data('status'))));
        });
    }).on('shown.bs.offcanvas', function() {
        // Init datepicker on date filter inputs (same format as inline edit form)
        ['#filter-date-from', '#filter-date-to'].forEach(sel => {
            const $el = $(sel);
            if ($el.data('datepicker')) $el.datepicker('destroy');
            $el.datepicker({
                format: 'dd/mm/yyyy',
                autoclose: true,
                todayHighlight: true,
                language: 'th',
                orientation: 'bottom auto',
                forceParse: false // don't reparse/blank the field on blur when switching from<->to
            });
        });

        // The two pickers can blank each other when switching fields. Remember each
        // chosen value and restore it if a field gets cleared while the other opens.
        var restoreDates = function () {
            var $f = $('#filter-date-from'), $t = $('#filter-date-to');
            if (matchFilter.dateFrom && !$f.val()) $f.val(matchFilter.dateFrom);
            if (matchFilter.dateTo && !$t.val()) $t.val(matchFilter.dateTo);
        };
        $('#filter-date-from').off('changeDate.keep').on('changeDate.keep', function () { matchFilter.dateFrom = $(this).val() || ''; });
        $('#filter-date-to').off('changeDate.keep').on('changeDate.keep', function () { matchFilter.dateTo = $(this).val() || ''; });
        $('#filter-date-from, #filter-date-to')
            .off('show.keep hide.keep')
            .on('show.keep hide.keep', function () { setTimeout(restoreDates, 0); });
    }).on('hidden.bs.offcanvas', function() {
        document.body.classList.remove('filter-drawer-open');
        ['#filter-date-from', '#filter-date-to'].forEach(sel => {
            const $el = $(sel);
            if ($el.data('datepicker')) $el.datepicker('hide');
        });
    });

    // Chip toggling
    $(document).on('click', '#filter-stage-chips .filter-chip', function() {
        const sid = String($(this).data('stage-id'));
        if (matchFilter.stages.has(sid)) matchFilter.stages.delete(sid);
        else matchFilter.stages.add(sid);
        $(this).toggleClass('is-active');
    });
    $(document).on('click', '#filter-group-chips .filter-chip-square', function() {
        const g = String($(this).data('group'));
        if (matchFilter.groups.has(g)) matchFilter.groups.delete(g);
        else matchFilter.groups.add(g);
        $(this).toggleClass('is-active');
    });
    $(document).on('click', '#matchFilterDrawer .filter-chip[data-status]', function() {
        const s = String($(this).data('status'));
        if (matchFilter.statuses.has(s)) matchFilter.statuses.delete(s);
        else matchFilter.statuses.add(s);
        $(this).toggleClass('is-active');
    });

    // Apply / Reset
    $(document).on('click', '#btn-filter-apply', function() {
        matchFilter.search = $('#filter-search').val() || '';
        matchFilter.dateFrom = $('#filter-date-from').val() || '';
        matchFilter.dateTo = $('#filter-date-to').val() || '';
        applyMatchFilters();
    });
    $(document).on('click', '#btn-filter-reset', resetMatchFilters);

    // Throttled refresh on DOM changes; ignore our own filter-empty-msg writes
    let _refreshTimer = null;
    function scheduleRefresh() {
        if (_refreshTimer) clearTimeout(_refreshTimer);
        _refreshTimer = setTimeout(() => {
            refreshFilterButtonVisibility();
            if (matchFilter.search || matchFilter.stages.size || matchFilter.groups.size || matchFilter.statuses.size || matchFilter.dateFrom || matchFilter.dateTo) {
                applyMatchFilters();
            }
        }, 250);
    }
    const filterObserver = new MutationObserver((mutations) => {
        if (_filterApplyingFlag) return;
        const hasRealChange = mutations.some(m =>
            Array.from(m.addedNodes).some(n => n.nodeType === 1 && !n.classList?.contains('filter-empty-msg'))
            || Array.from(m.removedNodes).some(n => n.nodeType === 1 && !n.classList?.contains('filter-empty-msg'))
        );
        if (!hasRealChange) return;
        scheduleRefresh();
    });
    $(document).ready(function() {
        const stagesContainer = document.getElementById('stages-container');
        if (stagesContainer) {
            filterObserver.observe(stagesContainer, { childList: true, subtree: true });
        }
        refreshFilterButtonVisibility();
    });

    const langFilterNoMatchesInStage = '<?= lang("Common.filter_no_matches_in_stage") ?>';

    // ==================== Bulk Edit Logic ====================
    $(document).on('change', '.form-check-input[id^="chk"]', function() {
        const $chk = $(this);
        const matchId = this.id.replace(/^chk/, '');
        const isChecked = $chk.is(':checked');
        $chk.closest('.match-select-pill').toggleClass('is-checked', isChecked);
        $(`.match-row-wrapper[data-match-id="${matchId}"]`).toggleClass('is-selected', isChecked);
        updateFloatingPanel();
        syncSelectAllButton($chk.closest('.stage-matches-container'));
    });

    // Select all / deselect all matches within a stage
    $(document).on('click', '.btn-select-all-stage', function() {
        const $btn = $(this);
        const $container = $btn.closest('.stage-matches-container');
        const $checkboxes = $container.find('.form-check-input[id^="chk"]');
        if ($checkboxes.length === 0) return;
        const shouldCheck = $checkboxes.filter(':not(:checked)').length > 0;
        $checkboxes.each(function() {
            if ($(this).prop('checked') !== shouldCheck) {
                $(this).prop('checked', shouldCheck).trigger('change');
            }
        });
        syncSelectAllButton($container);
    });

    function syncSelectAllButton($container) {
        if (!$container || $container.length === 0) return;
        const $btn = $container.find('.btn-select-all-stage');
        if ($btn.length === 0) return;
        const $checkboxes = $container.find('.form-check-input[id^="chk"]');
        const allChecked = $checkboxes.length > 0 && $checkboxes.filter(':not(:checked)').length === 0;
        $btn.find('.select-all-label').text(allChecked ? langDeselectAll : langSelectAll);
        $btn.find('i').attr('class', allChecked ? 'ti ti-square' : 'ti ti-checkbox');
    }

    // Click anywhere on the match card to toggle the checkbox (except dropdown/buttons/inline-edit form)
    $(document).on('click', '.match-row-wrapper.is-selectable', function(e) {
        if ($(e.target).closest('.dropdown, button, a, .inline-edit-form, input, select, textarea, .form-check-input, .match-select-pill').length) {
            return;
        }
        const matchId = $(this).data('match-id');
        const $chk = $(`#chk${matchId}`);
        if ($chk.length) {
            $chk.prop('checked', !$chk.prop('checked')).trigger('change');
        }
    });

    function updateFloatingPanel() {
        const selectedCheckboxes = $('.form-check-input[id^="chk"]:checked');
        const count = selectedCheckboxes.length;

        if (count > 0) {
            $('#bulk-date-count').text(count);
            $('#bulk-time-count').text(count);
            $('#bulk-stadium-count').text(count);
            $('#bulk-referee-count').text(count);
            $('#bulk-all-count').text(count);
            $('.selected-matches-display-count').text(count);
            $('#bulk-edit-btn-container').addClass('active');
        } else {
            $('#bulk-date-count').text(0);
            $('#bulk-time-count').text(0);
            $('#bulk-stadium-count').text(0);
            $('#bulk-referee-count').text(0);
            $('#bulk-all-count').text(0);
            $('#bulk-edit-btn-container').removeClass('active');

            // ปิด Modal หากเปิดอยู่แต่ไม่มีการเลือก checkbox แล้ว
            ['bulkEditDateModal', 'bulkEditTimeModal', 'bulkEditStadiumModal', 'bulkEditRefereeModal', 'bulkEditAllModal'].forEach(id => {
                const modalEl = document.getElementById(id);
                if (modalEl) {
                    try {
                        const modalInstance = bootstrap.Modal.getInstance(modalEl);
                        if (modalInstance) modalInstance.hide();
                    } catch (e) {}
                }
            });
        }
    }

    // Open Modals (single-field, เดิม)
    $(document).on('click', '.btn-bulk-date', function() {
        const modalEl = document.getElementById('bulkEditDateModal');
        document.body.appendChild(modalEl);
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
    $(document).on('click', '.btn-bulk-time', function() {
        const modalEl = document.getElementById('bulkEditTimeModal');
        document.body.appendChild(modalEl);
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
    $(document).on('click', '.btn-bulk-stadium', function() {
        const modalEl = document.getElementById('bulkEditStadiumModal');
        document.body.appendChild(modalEl);
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });
    $(document).on('click', '.btn-bulk-referee', function() {
        const modalEl = document.getElementById('bulkEditRefereeModal');
        document.body.appendChild(modalEl);
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });

    // Open Combined Modal (ใหม่)
    $(document).on('click', '.btn-bulk-all', function() {
        const modalEl = document.getElementById('bulkEditAllModal');
        document.body.appendChild(modalEl);
        bootstrap.Modal.getOrCreateInstance(modalEl).show();
    });

    // Initialize components on modal show (เดิม)
    $(document).on('shown.bs.modal', '#bulkEditDateModal', function () {
        const $input = $('#bulk-date-input');
        if ($input.data('datepicker')) $input.datepicker('destroy');
        $input.datepicker({ format: 'dd/mm/yyyy', autoclose: true, todayHighlight: true, language: 'th', container: '#bulkEditDateModal' });
    });
    $(document).on('shown.bs.modal', '#bulkEditStadiumModal', function () {
        $('#bulk-stadium-input').select2({ placeholder: langSelectStadium, allowClear: true, width: '100%', dropdownParent: $('#bulkEditStadiumModal') });
    });
    $(document).on('shown.bs.modal', '#bulkEditTimeModal', function () {
        const $input = $('#bulk-time-input');
        if (typeof $input.mask === 'function') $input.mask('00:00');
    });

    // Initialize components on combined modal show (ใหม่)
    $(document).on('shown.bs.modal', '#bulkEditAllModal', function () {
        const $date = $('#bulk-all-date');
        if ($date.data('datepicker')) $date.datepicker('destroy');
        $date.datepicker({ format: 'dd/mm/yyyy', autoclose: true, todayHighlight: true, language: 'th', container: '#bulkEditAllModal' });

        const $stadium = $('#bulk-all-stadium');
        if ($stadium.hasClass('select2-hidden-accessible')) $stadium.select2('destroy');
        $stadium.select2({ placeholder: langSelectStadium, allowClear: true, width: '100%', dropdownParent: $('#bulkEditAllModal') });

        const $time = $('#bulk-all-time');
        if (typeof $time.mask === 'function') $time.mask('00:00');
    });

    // Generic Bulk Save Function (single-field, เดิม)
    async function saveBulkMatches(field, value, modalId) {
        if (!value) {
            Swal.fire({ icon: 'warning', title: langPleaseEnterInformation, confirmButtonColor: '#16478D' });
            return;
        }

        const selectedCheckboxes = $('.form-check-input[id^="chk"]:checked');
        const count = selectedCheckboxes.length;

        const result = await Swal.fire({
            title: langConfirmBulkEditTitle,
            text: langConfirmBulkEditTextTemplate.replace('%s', count),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: langConfirmSaveNow,
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#16478D',
            cancelButtonColor: '#d33'
        });

        if (result.isConfirmed) {
            const modalEl = document.getElementById(modalId);
            const modalInstance = bootstrap.Modal.getInstance(modalEl);
            if (modalInstance) modalInstance.hide();

            Swal.fire({ title: langSavingInformation, allowOutsideClick: false, didOpen: () => Swal.showLoading() });

            let formattedValue = value;
            if (field === 'date') {
                const parts = value.split('/');
                if (parts.length === 3) formattedValue = parts[2] + '-' + parts[1] + '-' + parts[0];
            }

            const matchIds = [];
            selectedCheckboxes.each(function() { matchIds.push(this.id.replace('chk', '')); });
            let successCount = 0;
            let errorCount = 0;

            try {
                const response = await $.ajax({
                    url: '<?= base_url('/admin/tournament/save-tournament-match-result-multi') ?>',
                    type: 'POST',
                    dataType: 'json',
                    data: {
                        tour_id: tourId,
                        field: field,
                        value: formattedValue,
                        match_ids: matchIds
                    }
                });

                if (!response || response.status === 'error') {
                    throw new Error(response?.msg || 'Bulk save failed');
                }

                successCount = Number(response.success_count || 0);
                errorCount = Number(response.error_count || 0);
            } catch (err) {
                errorCount = matchIds.length;
            }

            Swal.close();
            const errorSuffix = errorCount > 0 ? langSaveSummaryErrorsSuffix.replace('%s', errorCount) : '';
            Swal.fire({
                icon: errorCount === 0 ? 'success' : 'warning',
                title: errorCount === 0 ? langSaved : langSaveCompletedWithErrors,
                text: langSaveSummary.replace('%s', successCount).replace('%s', errorSuffix),
                confirmButtonColor: '#16478D'
            }).then(() => {
                $('.form-check-input[id^="chk"]').prop('checked', false);
                updateFloatingPanel();
                loadMatchResults({ noScroll: true });
            });
        }
    }

    // Save Handlers (single-field, เดิม)
    $(document).on('click', '#btn-save-bulk-date', () => saveBulkMatches('date', $('#bulk-date-input').val(), 'bulkEditDateModal'));
    $(document).on('click', '#btn-save-bulk-time', () => saveBulkMatches('time', $('#bulk-time-input').val(), 'bulkEditTimeModal'));
    $(document).on('click', '#btn-save-bulk-stadium', () => saveBulkMatches('stadium', $('#bulk-stadium-input').val(), 'bulkEditStadiumModal'));
    $(document).on('click', '#btn-save-bulk-referee', () => saveBulkMatches('referee', $('#bulk-referee-input').val(), 'bulkEditRefereeModal'));

    // Combined Bulk Save (Pattern B: send one POST with match_id array of objects per match)
    async function saveBulkAll() {
        const dateVal    = ($('#bulk-all-date').val()    || '').trim();
        const timeVal    = ($('#bulk-all-time').val()    || '').trim();
        const stadiumVal = ($('#bulk-all-stadium').val() || '').trim();
        const refereeVal = ($('#bulk-all-referee').val() || '').trim();

        // อย่างน้อย 1 field ต้องกรอก
        if (!dateVal && !timeVal && !stadiumVal && !refereeVal) {
            Swal.fire({ icon: 'warning', title: langPleaseEnterInformation, confirmButtonColor: '#16478D' });
            return;
        }

        const selectedCheckboxes = $('.form-check-input[id^="chk"]:checked');
        const count = selectedCheckboxes.length;
        if (count === 0) return;

        const result = await Swal.fire({
            title: langConfirmBulkEditTitle,
            text: langConfirmBulkEditTextTemplate.replace('%s', count),
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: langConfirmSaveNow,
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#16478D',
            cancelButtonColor: '#d33'
        });
        if (!result.isConfirmed) return;

        const modalEl = document.getElementById('bulkEditAllModal');
        const modalInstance = bootstrap.Modal.getInstance(modalEl);
        if (modalInstance) modalInstance.hide();

        Swal.fire({ title: langSavingInformation, allowOutsideClick: false, didOpen: () => Swal.showLoading() });

        // แปลง date dd/mm/yyyy → yyyy-mm-dd
        let formattedDate = '';
        if (dateVal) {
            const parts = dateVal.split('/');
            if (parts.length === 3) formattedDate = parts[2] + '-' + parts[1] + '-' + parts[0];
        }

        // สร้าง payload Pattern B: match_id เป็น array ของ object ที่มีเฉพาะ field ที่กรอก
        const matchArr = [];
        selectedCheckboxes.each(function() {
            const item = { match_id: parseInt(this.id.replace('chk', ''), 10) };
            if (formattedDate) item.date    = formattedDate;
            if (timeVal)       item.time    = timeVal;
            if (stadiumVal)    item.stadium = stadiumVal;
            if (refereeVal)    item.referee = refereeVal;
            matchArr.push(item);
        });

        let successCount = 0;
        let errorCount = matchArr.length;

        try {
            const response = await $.ajax({
                url: '<?= base_url('/admin/tournament/save-tournament-match-result-multi') ?>',
                type: 'POST',
                dataType: 'json',
                data: { tour_id: tourId, match_id: matchArr }
            });

            if (!response || response.status === 'error') {
                throw new Error(response?.msg || 'Bulk save failed');
            }

            successCount = Number(response.success_count || 0);
            errorCount = Number(response.error_count || 0);
        } catch (err) {
            // errorCount เริ่มต้น = matchArr.length
        }

        Swal.close();
        const errorSuffix = errorCount > 0 ? langSaveSummaryErrorsSuffix.replace('%s', errorCount) : '';
        Swal.fire({
            icon: errorCount === 0 ? 'success' : 'warning',
            title: errorCount === 0 ? langSaved : langSaveCompletedWithErrors,
            text: langSaveSummary.replace('%s', successCount).replace('%s', errorSuffix),
            confirmButtonColor: '#16478D'
        }).then(() => {
            $('.form-check-input[id^="chk"]').prop('checked', false);
            // reset form
            $('#bulk-all-date, #bulk-all-time, #bulk-all-referee').val('');
            $('#bulk-all-stadium').val('').trigger('change');
            updateFloatingPanel();
            loadMatchResults({ noScroll: true });
        });
    }

    // Save Handler
    $(document).on('click', '#btn-save-bulk-all', saveBulkAll);
</script>
