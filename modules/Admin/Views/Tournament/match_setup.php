<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<?= $this->include('Modules\Admin\Views\Tournament\partials\match_setup\styles') ?>

<div class="card border-0 shadow-sm" id="match-setup-root" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">

        <?php echo view("Modules\Admin\Views\Tournament\subnav.php"); ?>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--ms-gold); box-shadow: 0 4px 12px rgba(209, 158, 17, 0.3);">
                    <i class="ti ti-trophy text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--ms-navy);"><?= lang('Common.match_setup_title') ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.match_setup_desc') ?></p>
                </div>
            </div>
        </div>

        <div class="alert border-0 shadow-sm d-flex align-items-center justify-content-between mb-5" role="alert" style="border-radius: var(--border-radius); background: var(--ms-navy-soft); border-left: 4px solid var(--ms-navy) !important;">
            <div class="d-flex align-items-center">
                <div class="me-3" style="color: var(--ms-navy);">
                    <i class="ti ti-info-circle fs-3"></i>
                </div>
                <div>
                    <h5 class="alert-heading fw-bold mb-1" style="color: var(--ms-navy);"><?= lang('Common.competition_info') ?>: <?= esc(is_array($tournament) ? $tournament['name'] : $tournament->name) ?></h5>
                    <p class="mb-0 text-muted"><?= lang('Common.registered_teams_count') ?>: <strong class="text-dark"><?= $total_teams ?> <?= lang('Common.teams') ?></strong></p>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <button type="button" class="match-filter-btn" id="btn-open-filter" data-bs-toggle="offcanvas" data-bs-target="#matchFilterDrawer" aria-controls="matchFilterDrawer" style="display: none;">
                    <i class="ti ti-filter"></i>
                    <span><?= lang('Common.filter') ?></span>
                    <span class="filter-count" id="filter-active-count">0</span>
                </button>
                <div id="total-matches-badge" class="d-flex align-items-center gap-2 px-4 py-3 rounded-3 shadow-sm" style="background: var(--ms-navy); border-top: 3px solid var(--ms-gold); display: none;">
                    <i class="ti ti-ball-football text-white" style="font-size: 1.5rem;"></i>
                    <div class="text-white text-end">
                        <div class="fw-bold" style="font-size: 1.5rem; line-height: 1;"><span id="total-matches-count">0</span></div>
                        <div style="font-size: 0.75rem; opacity: 0.9;"><?= lang('Common.total_matches') ?></div>
                    </div>
                </div>
            </div>
        </div>

        <div id="stages-container" style="display: none;">
            <!-- Bulk Edit Toolbar -->
            <div id="bulk-edit-btn-container" class="mb-4">
                <div class="bulk-edit-toolbar">
                    <div class="bulk-edit-toolbar-head">
                        <div class="bulk-edit-toolbar-icon">
                            <i class="ti ti-checks"></i>
                        </div>
                        <div class="flex-grow-1">
                            <h6 class="bulk-edit-toolbar-title"><?= lang('Common.bulk_edit_matches_title') ?></h6>
                            <p class="bulk-edit-toolbar-sub"><?= lang('Common.bulk_edit_matches_hint') ?></p>
                        </div>
                    </div>
                    <div class="bulk-edit-toolbar-actions">
                        <button type="button" class="btn-bulk-edit btn-bulk-date" id="btn-open-bulk-date">
                            <i class="ti ti-calendar"></i><?= lang('Common.date') ?><span class="count-pill" id="bulk-date-count">0</span>
                        </button>
                        <button type="button" class="btn-bulk-edit btn-bulk-time" id="btn-open-bulk-time">
                            <i class="ti ti-clock"></i><?= lang('Common.time') ?><span class="count-pill" id="bulk-time-count">0</span>
                        </button>
                        <button type="button" class="btn-bulk-edit btn-bulk-stadium" id="btn-open-bulk-stadium">
                            <i class="ti ti-map-pin"></i><?= lang('Common.stadium') ?><span class="count-pill" id="bulk-stadium-count">0</span>
                        </button>
                        <button type="button" class="btn-bulk-edit btn-bulk-referee" id="btn-open-bulk-referee">
                            <i class="ti ti-user"></i><?= lang('Common.referee') ?><span class="count-pill" id="bulk-referee-count">0</span>
                        </button>
                        <span class="divider"></span>
                        <button type="button" class="btn-bulk-edit btn-bulk-all ms-auto" id="btn-open-bulk-all">
                            <i class="ti ti-edit"></i>แก้ไขทุกฟิลด์<span class="count-pill" id="bulk-all-count">0</span>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="btn-add-stage-wrapper" id="empty-state-wrapper">
            <button id="btn-add-new-stage"
                class="btn btn-lg rounded-pill px-5 py-3 shadow fw-bold text-white transition-hover"
                style="background: var(--ms-referee); border: none; box-shadow: 0 4px 12px rgba(209, 158, 15, 0.25);">
                <i class="ti ti-plus me-2"></i>
                <span><?= lang('Common.add_new_stage') ?></span>
            </button>
            <div class="mt-3">
                <button id="btn-import-match-setup"
                    class="btn btn-outline-primary rounded-pill px-4 py-2"
                    onclick="openImportModal()">
                    <i class="ti ti-download me-1"></i>
                    <?= lang('Common.import_match_setup') ?>
                </button>
            </div>
            <div class="mt-3 text-muted small">
                <i class="ti ti-bulb text-warning me-1"></i>
                <?= lang('Common.stage_example_hint') ?>
            </div>
        </div>

        <div class="btn-add-stage-wrapper mt-5" id="add-more-stages-wrapper" style="display: none;">
            <button id="btn-add-more-stage"
                class="btn btn-lg rounded-pill px-5 py-3 shadow fw-bold text-white transition-hover"
                style="background: var(--ms-referee); border: none; box-shadow: 0 4px 12px rgba(209, 158, 15, 0.25);">
                <i class="ti ti-plus me-2"></i>
                <span><?= lang('Common.add_new_stage') ?></span>
            </button>
            <div class="mt-3">
                <button class="btn btn-outline-primary rounded-pill px-4 py-2"
                    onclick="openImportModal()">
                    <i class="ti ti-download me-1"></i>
                    <?= lang('Common.import_match_setup') ?>
                </button>
            </div>
            <div class="mt-3 text-muted small">
                <i class="ti ti-bulb text-warning me-1"></i>
                <?= lang('Common.stage_example_hint') ?>
            </div>
        </div>

    </div>
</div>

<!-- Modal: Import Match Setup -->
<div class="modal fade" id="importMatchSetupModal" tabindex="-1" aria-hidden="true" style="z-index:1060;">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <div class="modal-header text-white" style="background: var(--ms-navy); padding: 1.25rem 1.75rem;">
                <h5 class="modal-title fw-bold text-white">
                    <i class="ti ti-download me-2"></i><?= lang('Common.import_match_setup_title') ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.select_source_tournament') ?></label>
                    <input type="text" id="import-search-input" class="form-control rounded-pill" placeholder="<?= lang('Common.search_tournament') ?>">
                </div>
                <div id="import-tournament-list" style="max-height: 400px; overflow-y: auto;">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<template id="inline-edit-form-template">
    <div class="inline-edit-form" style="margin-top: 20px; margin-bottom: 20px;">
        <div class="card border-0" style="border-radius: 16px; overflow: hidden; box-shadow: 0 4px 24px rgba(0,0,0,0.08);">
            <div class="card-header text-white" style="background: var(--ms-navy); padding: 1.25rem 1.75rem;">
                <div class="d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold text-white" style="font-size: 1.1rem;"><i class="ti ti-edit me-2"></i><?= lang('Common.edit_match_details') ?></h6>
                    <button type="button" class="btn btn-sm btn-close btn-close-white cancel-inline-edit"></button>
                </div>
            </div>
            <div class="card-body p-4" style="background: #f8fafc;">
                <form class="edit-match-form">
                    <input type="hidden" class="match-id-field" name="match_id">
                    <input type="hidden" class="tour-id-field" name="tour_id" value="<?= $tour_id ?>">

                    <input type="hidden" name="team1_id" class="team1-select">
                    <input type="hidden" name="team2_id" class="team2-select">

                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2" style="color: #475569; font-size: 0.875rem;"><?= lang('Common.date') ?></label>
                            <input type="text" class="form-control date-input date_picker" name="date" placeholder="<?= lang('Common.date_placeholder') ?>" style="border-radius: 10px; border: 1px solid #e2e8f0; padding: 0.625rem 1rem;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2" style="color: #475569; font-size: 0.875rem;"><?= lang('Common.time') ?></label>
                            <input type="text" class="form-control time-input time" name="time" placeholder="HH:MM" style="border-radius: 10px; border: 1px solid #e2e8f0; padding: 0.625rem 1rem;">
                        </div>
                    </div>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2" style="color: #475569; font-size: 0.875rem;"><?= lang('Common.stadium') ?></label>
                            <div class="d-flex gap-2 align-items-start stadium-field-group">
                                <select class="form-select stadium-input stadium-select2" name="stadium">
                                    <option value=""><?= lang('Common.select_stadium') ?></option>
                                    <?php if (!empty($stadiums)): ?>
                                        <?php foreach ($stadiums as $s): ?>
                                        <option value="<?= esc($s['name']) ?>"><?= esc($s['name']) ?><?= !empty($s['name_en']) ? ' (' . esc($s['name_en']) . ')' : '' ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <button type="button" class="btn bulk-add-stadium-btn btn-add-stadium" title="<?= lang('Common.add_new_stadium') ?>">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold mb-2" style="color: #475569; font-size: 0.875rem;"><?= lang('Common.referee') ?></label>
                            <input type="text" class="form-control referee-input" name="referee" placeholder="<?= lang('Common.enter_referee_name') ?>" style="border-radius: 10px; border: 1px solid #e2e8f0; padding: 0.625rem 1rem;">
                        </div>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mt-4 pt-3 border-top">
                        <button type="button" class="btn btn-light border cancel-inline-edit px-3" style="border-radius: 8px; color: #64748b; font-size: 0.9rem;">
                            <i class="ti ti-x me-1"></i><?= lang('Common.cancel') ?>
                        </button>
                        <button type="button" class="btn btn-primary save-inline-edit text-white shadow-sm px-3" style="background: var(--ms-navy); border: none; border-radius: 8px; font-weight: 600; font-size: 0.9rem;">
                            <i class="ti ti-device-floppy me-1"></i><?= lang('Common.save') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<template id="stage-template">
    <div class="stage-card fade-in" id="stage-{id}">
        <div class="stage-header" data-bs-toggle="collapse" data-bs-target="#stage-content-{id}" aria-expanded="true" aria-controls="stage-content-{id}">
            <div class="d-flex align-items-center">
                <i class="ti ti-chevron-down me-3 text-white"></i>
                <span class="stage-number">{id}</span>
                <span class="stage-title text-white fw-bold" id="stage-title-{id}" style="font-size: 1.1rem; margin-left: 15px;">
                    <?= lang('Common.stage_number') ?> {id}
                </span>
                <span class="stage-match-count badge bg-white text-dark ms-3" id="stage-match-count-{id}" style="font-size: 0.85rem; padding: 5px 12px; border-radius: 20px; display: none;">
                    <i class="ti ti-ball-football me-1"></i><span class="match-count-number">0</span> &nbsp;<?= lang('Common.matches') ?>
                </span>
            </div>
            <button class="btn btn-sm btn-outline-light border-0 text-white opacity-75 hover-opacity-100" onclick="event.stopPropagation(); removeStage({id})" title="<?= lang('Common.delete_this_stage') ?>">
                <i class="ti ti-trash" style="font-size: 1.2rem;"></i>
            </button>
        </div>

        <div id="stage-content-{id}" class="collapse show">
            <div class="card-body p-4">

                <div class="settings-area">

                    <div class="row g-4 mb-4">
                        <div class="col-md-8">
                            <label class="form-label fw-bold small text-primary"><?= lang('Common.stage_name') ?></label>
                            <input type="text" class="form-control border stage-name-input shadow-sm" id="stage-name-input-{id}" placeholder="<?= lang('Common.stage_name_placeholder') ?>" value="" style="border-color: #DFE5EF;">
                        </div>
                        <div class="col-md-4 d-flex align-items-end pb-2">
                            <div class="form-check form-switch">
                                <input class="form-check-input final-match-checkbox" type="checkbox" id="final-match-{id}">
                                <label class="form-check-label fw-bold small text-primary" for="final-match-{id}">
                                    Final Match
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="row g-4 align-items-end mb-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold small text-primary"><?= lang('Common.format') ?></label>
                            <select class="form-select border stage-format shadow-sm" id="format-{id}" style="border-color: #DFE5EF;">
                                <option value="">-- <?= lang('Common.select') ?> --</option>
                                <option value="knockout"><?= lang('Common.format_knockout') ?></option>
                                <option value="league_single"><?= lang('Common.format_league_single') ?></option>
                                <option value="league_double"><?= lang('Common.format_league_double') ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-primary"><?= lang('Common.team_count') ?></label>
                            <input type="number" class="form-control border stage-teams-count shadow-sm" id="teams-count-{id}" value="" min="2" max="<?= $total_teams ?>" style="border-color: #DFE5EF;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold small text-primary"><?= lang('Common.group_count') ?></label>
                            <input type="number" class="form-control border stage-groups-count shadow-sm" id="groups-count-{id}" value="" min="1" max="8" style="border-color: #DFE5EF;">
                        </div>
                        <div class="col-md-2">
                            <button class="btn w-100 fw-bold shadow-sm text-white rounded-pill" onclick="generateStage({id})" style="background: var(--ms-navy); border: none;">
                                <i class="ti ti-wand me-1"></i> <?= lang('Common.generate') ?>
                            </button>
                        </div>
                    </div>

                    <div class="mt-4">
                        <label class="form-label fw-bold small d-flex align-items-center gap-2 text-primary">
                            <i class="ti ti-users"></i> <?= lang('Common.team_list') ?>
                        </label>
                        <div class="p-4 rounded-3 border stage-team-inputs bg-light-subtle" id="team-inputs-{id}" style="border-color: #DFE5EF;">

                        </div>
                    </div>

                    <div id="stage-matches-{id}" class="mt-0" style="display: none;"></div>
                </div>

                <hr class="my-4 text-muted opacity-25">

                <div id="result-area-{id}" style="display: none;"></div>
            </div>
        </div>
    </div>
</template>

<!-- Template for stages will be inserted into stages-container -->

<!-- Modal: Bulk Edit Date -->
<div class="modal fade" id="bulkEditDateModal" tabindex="-1" aria-labelledby="bulkEditDateModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header border-0 text-white" style="background: var(--ms-date); padding: 1.5rem;">
                <h5 class="modal-title fw-bold text-white" style="font-size: 1.2rem; color: #ffffff !important;">
                    <i class="ti ti-calendar me-2 text-white"></i><?= lang('Common.bulk_edit_date') ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary mb-2"><?= lang('Common.bulk_edit_date_label') ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;"><i class="ti ti-calendar text-primary"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 ps-0 date_picker" id="bulk-date-input" placeholder="DD/MM/YYYY" readonly style="border-radius: 0 12px 12px 0; padding: 0.75rem;">
                    </div>
                </div>
                <div class="alert alert-info border-0 rounded-3 mb-0 py-2">
                    <i class="ti ti-info-circle me-1"></i> <?= sprintf(lang('Common.bulk_edit_selected_matches_notice'), '<span class="fw-bold selected-matches-display-count">0</span>') ?>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn w-100 py-3 fw-bold text-white" id="btn-save-bulk-date" style="border-radius: 12px; background: var(--ms-date); border: none;"><?= lang('Common.save_date') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Bulk Edit Time -->
<div class="modal fade" id="bulkEditTimeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header border-0 text-white" style="background: var(--ms-time); padding: 1.5rem;">
                <h5 class="modal-title fw-bold text-white" style="font-size: 1.2rem;">
                    <i class="ti ti-clock me-2"></i><?= lang('Common.bulk_edit_time') ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary mb-2"><?= lang('Common.bulk_edit_time_label') ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;"><i class="ti ti-clock text-info"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 ps-0 time" id="bulk-time-input" placeholder="00:00" style="border-radius: 0 12px 12px 0; padding: 0.75rem;">
                    </div>
                </div>
                <div class="alert alert-info border-0 rounded-3 mb-0 py-2">
                    <i class="ti ti-info-circle me-1"></i> <?= sprintf(lang('Common.bulk_edit_selected_matches_notice'), '<span class="fw-bold selected-matches-display-count">0</span>') ?>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn w-100 py-3 fw-bold text-white" id="btn-save-bulk-time" style="border-radius: 12px; background: var(--ms-time); border: none;"><?= lang('Common.save_time') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Bulk Edit Stadium -->
<div class="modal fade" id="bulkEditStadiumModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header border-0 text-white" style="background: var(--ms-stadium); padding: 1.5rem;">
                <h5 class="modal-title fw-bold text-white" style="font-size: 1.2rem;">
                    <i class="ti ti-map-pin me-2"></i><?= lang('Common.bulk_edit_stadium') ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary mb-2"><?= lang('Common.bulk_edit_stadium_label') ?></label>
                    <div class="d-flex gap-2 bulk-stadium-group">
                        <select class="form-select stadium-select2" id="bulk-stadium-input" style="border-radius: 12px;">
                            <option value=""><?= lang('Common.select_stadium') ?></option>
                            <?php if (!empty($stadiums)): ?>
                                <?php foreach ($stadiums as $s): ?>
                                <option value="<?= esc($s['name']) ?>"><?= esc($s['name']) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <button type="button" class="btn bulk-add-stadium-btn btn-add-stadium" title="<?= lang('Common.add_new_stadium') ?>"><i class="ti ti-plus"></i></button>
                    </div>
                </div>
                <div class="alert alert-info border-0 rounded-3 mb-0 py-2">
                    <i class="ti ti-info-circle me-1"></i> <?= sprintf(lang('Common.bulk_edit_selected_matches_notice'), '<span class="fw-bold selected-matches-display-count">0</span>') ?>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn w-100 py-3 fw-bold text-white" id="btn-save-bulk-stadium" style="border-radius: 12px; background: var(--ms-stadium); border: none;"><?= lang('Common.save_stadium') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Bulk Edit Referee -->
<div class="modal fade" id="bulkEditRefereeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 24px; border: none; overflow: hidden; box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);">
            <div class="modal-header border-0 text-white" style="background: var(--ms-referee); padding: 1.5rem;">
                <h5 class="modal-title fw-bold text-white" style="font-size: 1.2rem;">
                    <i class="ti ti-user me-2"></i><?= lang('Common.bulk_edit_referee') ?>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="mb-4">
                    <label class="form-label fw-bold text-secondary mb-2"><?= lang('Common.bulk_edit_referee_label') ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0" style="border-radius: 12px 0 0 12px;"><i class="ti ti-user text-warning"></i></span>
                        <input type="text" class="form-control bg-light border-start-0 ps-0" id="bulk-referee-input" placeholder="<?= lang('Common.bulk_edit_referee_label') ?>" style="border-radius: 0 12px 12px 0; padding: 0.75rem;">
                    </div>
                </div>
                <div class="alert alert-info border-0 rounded-3 mb-0 py-2">
                    <i class="ti ti-info-circle me-1"></i> <?= sprintf(lang('Common.bulk_edit_selected_matches_notice'), '<span class="fw-bold selected-matches-display-count">0</span>') ?>
                </div>
            </div>
            <div class="modal-footer border-0 p-4 pt-0">
                <button type="button" class="btn w-100 py-3 fw-bold text-white" id="btn-save-bulk-referee" style="border-radius: 12px; background: var(--ms-referee); border: none;"><?= lang('Common.save_referee') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Bulk Edit All (Date / Time / Stadium / Referee) -->
<div class="modal fade bulk-edit-all-modal" id="bulkEditAllModal" tabindex="-1" aria-labelledby="bulkEditAllModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content bulk-edit-all-content">
            <div class="modal-header bulk-edit-all-header">
                <div class="d-flex align-items-center gap-3">
                    <div class="bulk-edit-all-icon">
                        <i class="ti ti-edit"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-white mb-0">แก้ไขข้อมูลแมตช์ที่เลือกพร้อมกัน</h5>
                        <small class="text-white-50">กรอกเฉพาะฟิลด์ที่ต้องการอัปเดต</small>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form id="bulk-edit-all-form">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <label class="bulk-edit-label" for="bulk-all-date"><?= lang('Common.date') ?></label>
                            <input type="text" class="form-control bulk-edit-input date_picker" id="bulk-all-date" placeholder="<?= lang('Common.date_placeholder') ?>" readonly>
                        </div>
                        <div class="col-md-6">
                            <label class="bulk-edit-label" for="bulk-all-time">
                                <i class="ti ti-clock"></i><?= lang('Common.time') ?>
                            </label>
                            <input type="text" class="form-control bulk-edit-input time" id="bulk-all-time" placeholder="HH:MM">
                        </div>
                        <div class="col-md-6">
                            <label class="bulk-edit-label" for="bulk-all-stadium">
                                <i class="ti ti-map-pin"></i><?= lang('Common.stadium') ?>
                            </label>
                            <div class="d-flex gap-2 bulk-stadium-group">
                                <select class="form-select bulk-edit-input stadium-select2" id="bulk-all-stadium">
                                    <option value=""><?= lang('Common.select_stadium') ?></option>
                                    <?php if (!empty($stadiums)): ?>
                                        <?php foreach ($stadiums as $s): ?>
                                        <option value="<?= esc($s['name']) ?>"><?= esc($s['name']) ?></option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                                <button type="button" class="btn bulk-add-stadium-btn btn-add-stadium" title="<?= lang('Common.add_new_stadium') ?>">
                                    <i class="ti ti-plus"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="bulk-edit-label" for="bulk-all-referee">
                                <i class="ti ti-user"></i><?= lang('Common.referee') ?>
                            </label>
                            <input type="text" class="form-control bulk-edit-input" id="bulk-all-referee" placeholder="<?= lang('Common.enter_referee_name') ?>">
                        </div>
                    </div>
                </form>
                <div class="bulk-edit-notice mt-4">
                    <div class="bulk-edit-notice-icon">
                        <i class="ti ti-info-circle"></i>
                    </div>
                    <div class="flex-grow-1">
                        <div class="bulk-edit-notice-title">
                            <?= sprintf(lang('Common.bulk_edit_selected_matches_notice'), '<span class="fw-bold selected-matches-display-count">0</span>') ?>
                        </div>
                        <div class="bulk-edit-notice-sub">เว้นว่างฟิลด์ที่ไม่ต้องการแก้ไข — ระบบจะอัปเดตเฉพาะข้อมูลที่กรอกเท่านั้น</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer bulk-edit-footer">
                <button type="button" class="btn bulk-edit-cancel-btn" data-bs-dismiss="modal">
                    <i class="ti ti-x me-1"></i><?= lang('Common.cancel') ?>
                </button>
                <button type="button" class="btn bulk-edit-save-btn" id="btn-save-bulk-all">
                    <i class="ti ti-device-floppy me-1"></i><?= lang('Common.save') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Drawer (offcanvas) -->
<div class="offcanvas offcanvas-end filter-drawer" tabindex="-1" id="matchFilterDrawer" aria-labelledby="matchFilterDrawerLabel" data-bs-backdrop="true" data-bs-scroll="false">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="matchFilterDrawerLabel">
            <i class="ti ti-filter"></i><span class="text-white"><?= lang('Common.filter_matches') ?></span>
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <!-- Search -->
        <div class="filter-section">
            <div class="filter-label">
                <i class="ti ti-search"></i><?= lang('Common.search') ?>
            </div>
            <div class="filter-search-wrap">
                <i class="ti ti-search"></i>
                <input type="text" class="filter-search-input" id="filter-search" placeholder="<?= lang('Common.filter_search_placeholder') ?>">
            </div>
        </div>

        <!-- Date range -->
        <div class="filter-section">
            <div class="filter-label">
                <i class="ti ti-calendar"></i><?= lang('Common.filter_date_range') ?>
            </div>
            <div class="filter-date-row-stack">
                <div class="filter-date-field">
                    <label class="filter-date-label"><?= lang('Common.filter_from_date') ?></label>
                    <div class="filter-date-col">
                        <i class="ti ti-calendar-event"></i>
                        <input type="text" class="form-control date_picker" id="filter-date-from" placeholder="<?= lang('Common.date_placeholder') ?>" autocomplete="off">
                    </div>
                </div>
                <div class="filter-date-field">
                    <label class="filter-date-label"><?= lang('Common.filter_to_date') ?></label>
                    <div class="filter-date-col">
                        <i class="ti ti-calendar-event"></i>
                        <input type="text" class="form-control date_picker" id="filter-date-to" placeholder="<?= lang('Common.date_placeholder') ?>" autocomplete="off">
                    </div>
                </div>
            </div>
        </div>

        <!-- Stages -->
        <div class="filter-section">
            <div class="filter-label">
                <i class="ti ti-stack-2"></i><?= lang('Common.filter_stages') ?>
            </div>
            <div class="filter-chips" id="filter-stage-chips">
                <div class="text-muted small fst-italic">-</div>
            </div>
        </div>

        <!-- Groups -->
        <div class="filter-section">
            <div class="filter-label">
                <i class="ti ti-map-pin"></i><?= lang('Common.filter_groups') ?>
            </div>
            <div class="filter-chips-grid" id="filter-group-chips">
                <div class="text-muted small fst-italic">-</div>
            </div>
        </div>

        <!-- Status -->
        <div class="filter-section">
            <div class="filter-label">
                <i class="ti ti-flag"></i><?= lang('Common.filter_status') ?>
            </div>
            <div class="filter-chips">
                <span class="filter-chip chip-status-pending" data-status="0"><i class="ti ti-clock"></i><?= lang('Common.filter_status_pending') ?></span>
                <span class="filter-chip chip-status-live" data-status="1"><i class="ti ti-player-play"></i><?= lang('Common.filter_status_live') ?></span>
                <span class="filter-chip chip-status-done" data-status="3"><i class="ti ti-flag-check"></i><?= lang('Common.filter_status_done') ?></span>
            </div>
        </div>
    </div>
    <div class="filter-drawer-footer">
        <button type="button" class="btn-filter-reset" id="btn-filter-reset">
            <i class="ti ti-refresh"></i><?= lang('Common.reset_filter') ?>
        </button>
        <button type="button" class="btn-filter-apply" id="btn-filter-apply" data-bs-dismiss="offcanvas">
            <i class="ti ti-check"></i><?= lang('Common.apply_filter') ?>
        </button>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?= $this->include('Modules\Admin\Views\Tournament\partials\match_setup\scripts') ?>
<?php $this->endSection(); ?>
