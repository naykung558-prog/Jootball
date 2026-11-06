<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<?= $this->include('Modules\Admin\Views\Tournament\partials\score_management\styles') ?>

<!-- Subnav -->
<?= $this->include('Modules\Admin\Views\Tournament\subnav') ?>

<div class="container-fluid py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold">
                <i class="ti ti-calculator me-2"></i>
                <?= lang('Common.score_management') ?>
            </h4>
            <p class="text-muted mb-0">
                <?= esc($tournament['name'] ?? lang('Common.tournament')) ?>
                <?php if (!empty($tournament['generation'])): ?>
                    - <?= lang('Common.generation') ?> <?= esc($tournament['generation']) ?>
                <?php endif; ?>
            </p>
        </div>
        <div class="d-flex align-items-center gap-3">
            <!-- Date Filter -->
            <?php if (!empty($match_dates)): ?>
                <div class="d-flex align-items-center gap-2">
                    <label class="form-label mb-0 text-muted small">
                        <i class="ti ti-calendar me-1"></i><?= lang('Common.match_date') ?>:
                    </label>
                    <select class="form-select form-select-sm" id="dateFilter" onchange="filterByDate(this.value)" style="width: auto; min-width: 150px;">
                        <option value=""><?= lang('Common.all') ?></option>
                        <?php foreach ($match_dates as $date): ?>
                            <?php
                            $dateObj = new DateTime($date);
                            $thaiMonths = ['', 'ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                            $day = $dateObj->format('j');
                            $month = $thaiMonths[(int)$dateObj->format('n')];
                            $year = (int)$dateObj->format('Y') + 543;
                            $thaiDate = "$day $month $year";
                            $isToday = ($date === date('Y-m-d'));
                            ?>
                            <option value="<?= esc($date) ?>" <?= ($filter_date === $date) ? 'selected' : '' ?>>
                                <?= $thaiDate ?><?= $isToday ? ' (' . lang('Common.today') . ')' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            <?php endif; ?>
            <button class="btn btn-outline-primary" onclick="refreshMatches()">
                <i class="ti ti-refresh me-2"></i><?= lang('Common.refresh') ?>
            </button>
        </div>
    </div>

    <!-- Matches by Round -->
    <div id="matches-container">
        <?php if (empty($matches_by_round)): ?>
            <div class="empty-state">
                <i class="ti ti-trophy-off"></i>
                <?php if (!empty($filter_date)): ?>
                    <?php
                    $filterDateObj = new DateTime($filter_date);
                    $thaiMonths = ['', 'มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
                    $filterDay = $filterDateObj->format('j');
                    $filterMonth = $thaiMonths[(int)$filterDateObj->format('n')];
                    $filterYear = (int)$filterDateObj->format('Y') + 543;
                    $filterThaiDate = "$filterDay $filterMonth $filterYear";
                    ?>
                    <h5><?= lang('Common.no_match_on_date') ?> <?= $filterThaiDate ?></h5>
                    <p><?= lang('Common.select_other_date_or_all') ?></p>
                    <button class="btn btn-primary mt-3" onclick="filterByDate('')">
                        <i class="ti ti-list me-2"></i><?= lang('Common.view_all_matches') ?>
                    </button>
                <?php else: ?>
                    <h5><?= lang('Common.no_match_ready_for_scoring') ?></h5>
                    <p><?= lang('Common.shows_only_matches_with_teams') ?></p>
                    <p class="text-muted small"><?= lang('Common.set_match_date_today_hint') ?></p>
                    <a href="<?= base_url('admin/tournament/match-setup/' . $tour_id) ?>" class="btn btn-primary mt-3">
                        <i class="ti ti-settings me-2"></i><?= lang('Common.manage_matches') ?>
                    </a>
                <?php endif; ?>
            </div>
        <?php else: ?>
            <?php foreach ($matches_by_round as $round): ?>
                <div class="round-section">
                    <div class="round-header collapsed" data-bs-toggle="collapse" data-bs-target="#round-<?= md5($round['round_name']) ?>">
                        <div class="d-flex align-items-center">
                            <i class="ti ti-tournament fs-4 me-2"></i>
                            <span class="fw-bold"><?= esc($round['round_name']) ?></span>
                            <span class="badge bg-light text-dark ms-3"><?= count($round['matches']) ?> <?= lang('Common.match') ?></span>
                        </div>
                        <i class="ti ti-chevron-down fs-4"></i>
                    </div>

                    <div class="collapse" id="round-<?= md5($round['round_name']) ?>">
                        <?php foreach ($round['matches'] as $match): ?>
                            <?php
                            $isEnded = ($match['match_status'] == 3);
                            $team1Score = $match['team1_score'] ?? '';
                            $team2Score = $match['team2_score'] ?? '';
                            $team1Penalty = $match['team1_penalty'] ?? '';
                            $team2Penalty = $match['team2_penalty'] ?? '';
                            $winnerId = $match['winner_team_id'] ?? '';
                            $isTied = ($team1Score !== '' && $team2Score !== '' && $team1Score == $team2Score);
                            $isPenaltyTied = ($team1Penalty !== '' && $team2Penalty !== '' && $team1Penalty == $team2Penalty);
                            ?>
                            <div class="match-card <?= $isEnded ? 'match-ended' : '' ?>"
                                data-match-id="<?= $match['match_id'] ?>"
                                data-tour-id="<?= $tour_id ?>"
                                data-team1-id="<?= $match['team1_id'] ?>"
                                data-team2-id="<?= $match['team2_id'] ?>"
                                data-team1-name="<?= esc($match['team1_name'] ?? lang('Common.team') . ' 1') ?>"
                                data-team2-name="<?= esc($match['team2_name'] ?? lang('Common.team') . ' 2') ?>">
                                <div class="d-flex justify-content-between align-items-start mb-3">
                                    <div>
                                        <?php if (!empty($match['date'])): ?>
                                            <small class="text-muted">
                                                <i class="ti ti-calendar me-1"></i>
                                                <?= date('d/m/Y', strtotime($match['date'])) ?>
                                                <?php if (!empty($match['time'])): ?>
                                                    <i class="ti ti-clock ms-2 me-1"></i><?= $match['time'] ?>
                                                <?php endif; ?>
                                            </small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ($isEnded): ?>
                                        <span class="match-status-badge status-ended">
                                            <i class="ti ti-check me-1"></i><?= lang('Common.match_ended') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="match-status-badge status-live">
                                            <i class="ti ti-live-photo me-1"></i><?= lang('Common.match_in_progress') ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Teams and Score -->
                                <div class="d-flex align-items-center justify-content-between">
                                    <!-- Team 1 -->
                                    <div class="team-display">
                                        <?php
                                        $logo1 = (!empty($match['team1_logo']) && file_exists('./' . $match['team1_logo']))
                                            ? base_url($match['team1_logo'])
                                            : base_url('public/img/jootball_logo.png');
                                        ?>
                                        <img src="<?= $logo1 ?>" alt="" class="team-logo">
                                        <span class="team-name <?= ($winnerId == $match['team1_id'] && $isEnded) ? 'winner' : '' ?>">
                                            <?= esc($match['team1_name'] ?? lang('Common.team') . ' 1') ?>
                                            <?php if ($winnerId == $match['team1_id'] && $isEnded): ?>
                                                <i class="ti ti-trophy text-warning ms-1"></i>
                                            <?php endif; ?>
                                        </span>
                                    </div>

                                    <!-- Score Inputs -->
                                    <div class="score-input-wrapper">
                                        <input type="number"
                                            class="score-input team1-score <?= $isEnded ? 'score-readonly' : '' ?>"
                                            min="0"
                                            max="99"
                                            value="<?= $team1Score ?>"
                                            data-match-id="<?= $match['match_id'] ?>"
                                            <?= $isEnded ? 'readonly' : '' ?>
                                            onchange="checkTieStatus(<?= $match['match_id'] ?>)">
                                        <span class="vs-badge">-</span>
                                        <input type="number"
                                            class="score-input team2-score <?= $isEnded ? 'score-readonly' : '' ?>"
                                            min="0"
                                            max="99"
                                            value="<?= $team2Score ?>"
                                            data-match-id="<?= $match['match_id'] ?>"
                                            <?= $isEnded ? 'readonly' : '' ?>
                                            onchange="checkTieStatus(<?= $match['match_id'] ?>)">
                                    </div>

                                    <!-- Team 2 -->
                                    <div class="team-display justify-content-end">
                                        <span class="team-name text-end <?= ($winnerId == $match['team2_id'] && $isEnded) ? 'winner' : '' ?>">
                                            <?php if ($winnerId == $match['team2_id'] && $isEnded): ?>
                                                <i class="ti ti-trophy text-warning me-1"></i>
                                            <?php endif; ?>
                                            <?= esc($match['team2_name'] ?? lang('Common.team') . ' 2') ?>
                                        </span>
                                        <?php
                                        $logo2 = (!empty($match['team2_logo']) && file_exists('./' . $match['team2_logo']))
                                            ? base_url($match['team2_logo'])
                                            : base_url('public/img/jootball_logo.png');
                                        ?>
                                        <img src="<?= $logo2 ?>" alt="" class="team-logo">
                                    </div>
                                </div>

                                <!-- Team Management Section - แบ่ง 2 ทีม -->
                                <div class="row g-3 mt-2">
                                    <!-- Team 1 -->
                                    <div class="col-md-6">
                                        <div class="team-management-card team1-card">
                                            <div class="team-management-header">
                                                <?php
                                                $logo1 = (!empty($match['team1_logo']) && file_exists('./' . $match['team1_logo']))
                                                    ? base_url($match['team1_logo'])
                                                    : base_url('public/img/jootball_logo.png');
                                                ?>
                                                <img src="<?= $logo1 ?>" alt="" class="team-mini-logo">
                                                <span class="team-management-name"><?= esc($match['team1_name'] ?? lang('Common.team') . ' 1') ?></span>
                                            </div>

                                            <!-- Team 1 Lineup -->
                                            <div class="team-lineup-section">
                                                <div class="team-section-header">
                                                    <span><i class="ti ti-list-check me-1"></i><?= lang('Common.match_lineup') ?></span>
                                                    <button type="button" class="btn btn-xs btn-outline-primary btn-open-lineup-single <?= $isEnded ? 'd-none' : '' ?>"
                                                        data-match-id="<?= $match['match_id'] ?>"
                                                        data-team-id="<?= $match['team1_id'] ?>"
                                                        data-team-name="<?= esc($match['team1_name'] ?? lang('Common.team') . ' 1') ?>">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                </div>
                                                <div class="team-lineup-content" id="lineup-team1-content-<?= $match['match_id'] ?>">
                                                    <div class="text-center text-muted small py-2">
                                                        <i class="ti ti-loader ti-spin me-1"></i><?= lang('Common.loading') ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Team 1 Events -->
                                            <div class="team-events-section">
                                                <div class="team-section-header">
                                                    <span><i class="ti ti-ball-football me-1"></i><?= lang('Common.event') ?></span>
                                                    <button type="button" class="btn btn-xs btn-outline-success btn-add-event-single <?= $isEnded ? 'd-none' : '' ?>"
                                                        data-match-id="<?= $match['match_id'] ?>"
                                                        data-team-id="<?= $match['team1_id'] ?>"
                                                        data-team-name="<?= esc($match['team1_name'] ?? lang('Common.team') . ' 1') ?>">
                                                        <i class="ti ti-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="team-events-content" id="events-team1-content-<?= $match['match_id'] ?>">
                                                    <div class="text-center text-muted small py-2">
                                                        <i class="ti ti-loader ti-spin me-1"></i><?= lang('Common.loading') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Team 2 -->
                                    <div class="col-md-6">
                                        <div class="team-management-card team2-card">
                                            <div class="team-management-header">
                                                <?php
                                                $logo2 = (!empty($match['team2_logo']) && file_exists('./' . $match['team2_logo']))
                                                    ? base_url($match['team2_logo'])
                                                    : base_url('public/img/jootball_logo.png');
                                                ?>
                                                <img src="<?= $logo2 ?>" alt="" class="team-mini-logo">
                                                <span class="team-management-name"><?= esc($match['team2_name'] ?? lang('Common.team') . ' 2') ?></span>
                                            </div>

                                            <!-- Team 2 Lineup -->
                                            <div class="team-lineup-section">
                                                <div class="team-section-header">
                                                    <span><i class="ti ti-list-check me-1"></i><?= lang('Common.match_lineup') ?></span>
                                                    <button type="button" class="btn btn-xs btn-outline-primary btn-open-lineup-single <?= $isEnded ? 'd-none' : '' ?>"
                                                        data-match-id="<?= $match['match_id'] ?>"
                                                        data-team-id="<?= $match['team2_id'] ?>"
                                                        data-team-name="<?= esc($match['team2_name'] ?? lang('Common.team') . ' 2') ?>">
                                                        <i class="ti ti-edit"></i>
                                                    </button>
                                                </div>
                                                <div class="team-lineup-content" id="lineup-team2-content-<?= $match['match_id'] ?>">
                                                    <div class="text-center text-muted small py-2">
                                                        <i class="ti ti-loader ti-spin me-1"></i><?= lang('Common.loading') ?>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Team 2 Events -->
                                            <div class="team-events-section">
                                                <div class="team-section-header">
                                                    <span><i class="ti ti-ball-football me-1"></i><?= lang('Common.event') ?></span>
                                                    <button type="button" class="btn btn-xs btn-outline-success btn-add-event-single <?= $isEnded ? 'd-none' : '' ?>"
                                                        data-match-id="<?= $match['match_id'] ?>"
                                                        data-team-id="<?= $match['team2_id'] ?>"
                                                        data-team-name="<?= esc($match['team2_name'] ?? lang('Common.team') . ' 2') ?>">
                                                        <i class="ti ti-plus"></i>
                                                    </button>
                                                </div>
                                                <div class="team-events-content" id="events-team2-content-<?= $match['match_id'] ?>">
                                                    <div class="text-center text-muted small py-2">
                                                        <i class="ti ti-loader ti-spin me-1"></i><?= lang('Common.loading') ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Penalty Section (shown when tied or has penalty data) -->
                                <?php $showPenalty = ($isTied && !$isEnded) || ($isEnded && ($team1Penalty !== '' || $team2Penalty !== '')); ?>
                                <div class="penalty-section <?= $showPenalty ? 'show' : '' ?>" id="penalty-<?= $match['match_id'] ?>">
                                    <div class="penalty-label">
                                        <i class="ti ti-target me-1"></i>
                                        <?= lang('Common.penalty_shootout') ?> <?= $isEnded ? '' : '(' . lang('Common.if_any') . ')' ?>
                                    </div>
                                    <div class="d-flex align-items-center justify-content-center gap-3">
                                        <div class="text-center">
                                            <small class="text-muted d-block mb-1"><?= esc($match['team1_name'] ?? lang('Common.team') . ' 1') ?></small>
                                            <input type="number"
                                                class="penalty-input team1-penalty <?= $isEnded ? 'score-readonly' : '' ?>"
                                                min="0"
                                                max="99"
                                                value="<?= $team1Penalty ?>"
                                                data-match-id="<?= $match['match_id'] ?>"
                                                <?= $isEnded ? 'readonly' : '' ?>
                                                onchange="checkPenaltyTieStatus(<?= $match['match_id'] ?>)">
                                        </div>
                                        <span class="text-muted fw-bold">-</span>
                                        <div class="text-center">
                                            <small class="text-muted d-block mb-1"><?= esc($match['team2_name'] ?? lang('Common.team') . ' 2') ?></small>
                                            <input type="number"
                                                class="penalty-input team2-penalty <?= $isEnded ? 'score-readonly' : '' ?>"
                                                min="0"
                                                max="99"
                                                value="<?= $team2Penalty ?>"
                                                data-match-id="<?= $match['match_id'] ?>"
                                                <?= $isEnded ? 'readonly' : '' ?>
                                                onchange="checkPenaltyTieStatus(<?= $match['match_id'] ?>)">
                                        </div>
                                    </div>
                                </div>

                                <!-- Winner Selection (shown when penalty also tied) -->
                                <?php if (!$isEnded): ?>
                                    <div class="winner-select-section <?= ($isTied && $isPenaltyTied) ? 'show' : '' ?>" id="winner-select-<?= $match['match_id'] ?>">
                                        <div class="winner-label">
                                            <i class="ti ti-crown me-1"></i>
                                            <?= lang('Common.select_winner_penalty_tie') ?>
                                        </div>
                                        <select class="form-select winner-select" data-match-id="<?= $match['match_id'] ?>">
                                            <option value="">-- <?= lang('Common.select_winning_team') ?> --</option>
                                            <option value="<?= $match['team1_id'] ?>" <?= ($winnerId == $match['team1_id']) ? 'selected' : '' ?>>
                                                <?= esc($match['team1_name'] ?? lang('Common.team') . ' 1') ?>
                                            </option>
                                            <option value="<?= $match['team2_id'] ?>" <?= ($winnerId == $match['team2_id']) ? 'selected' : '' ?>>
                                                <?= esc($match['team2_name'] ?? lang('Common.team') . ' 2') ?>
                                            </option>
                                        </select>
                                    </div>
                                <?php endif; ?>

                                <!-- Action Buttons -->
                                <?php if (!$isEnded): ?>
                                    <div class="action-buttons">
                                        <button class="btn btn-save-score" onclick="saveMatchScore(<?= $match['match_id'] ?>)">
                                            <i class="ti ti-device-floppy me-1"></i>
                                            <?= lang('Common.save') ?>
                                        </button>
                                        <button class="btn btn-end-match" onclick="endMatchWithScore(<?= $match['match_id'] ?>)">
                                            <i class="ti ti-flag-check me-1"></i>
                                            <?= lang('Common.match_ended') ?>
                                        </button>
                                    </div>
                                <?php else: ?>
                                    <!-- แสดงเมื่อยังไม่ได้เข้าโหมดแก้ไข -->
                                    <div class="action-buttons action-view-mode" id="action-view-<?= $match['match_id'] ?>">
                                        <span class="text-success fw-semibold">
                                            <i class="ti ti-circle-check me-1"></i>
                                            <?= lang('Common.result_saved') ?>
                                            <?php if (!empty($match['is_penalty_used'])): ?>
                                                <span class="text-muted">(<?= lang('Common.penalty_shootout') ?>)</span>
                                            <?php endif; ?>
                                        </span>
                                        <button class="btn btn-outline-warning btn-edit-ended ms-2" onclick="enableEditMode(<?= $match['match_id'] ?>)">
                                            <i class="ti ti-pencil me-1"></i>
                                            <?= lang('Common.edit') ?>
                                        </button>
                                        <a href="<?= base_url('admin/tournament/export-match-result/' . $match['match_id']) ?>" class="btn btn-export-result ms-2" target="_blank">
                                            <i class="ti ti-file-export me-1"></i>
                                            Export PDF
                                        </a>
                                    </div>
                                    <!-- แสดงเมื่อเข้าโหมดแก้ไข -->
                                    <div class="action-buttons action-edit-mode d-none" id="action-edit-<?= $match['match_id'] ?>">
                                        <button class="btn btn-secondary" onclick="cancelEditMode(<?= $match['match_id'] ?>)">
                                            <i class="ti ti-x me-1"></i>
                                            <?= lang('Common.cancel') ?>
                                        </button>
                                        <button class="btn btn-save-score" onclick="saveMatchScore(<?= $match['match_id'] ?>)">
                                            <i class="ti ti-device-floppy me-1"></i>
                                            <?= lang('Common.save') ?>
                                        </button>
                                        <button class="btn btn-end-match" onclick="endMatchWithScore(<?= $match['match_id'] ?>)">
                                            <i class="ti ti-flag-check me-1"></i>
                                            <?= lang('Common.save_and_end') ?>
                                        </button>
                                    </div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<!-- Lineup Selection Modal (Micromodal) -->
<div class="micromodal" id="lineup-modal" aria-hidden="true">
    <div class="micromodal-overlay" tabindex="-1" data-micromodal-close>
        <div class="micromodal-container modal-lg" role="dialog" aria-modal="true" aria-labelledby="lineup-modal-title">
            <header class="micromodal-header">
                <h2 class="micromodal-title" id="lineup-modal-title">
                    <i class="ti ti-list-check me-2"></i><?= lang('Common.select_match_lineup') ?>
                </h2>
                <button class="micromodal-close" aria-label="Close" data-micromodal-close>&times;</button>
            </header>
            <main class="micromodal-body">
                <input type="hidden" id="lineup-match-id">

                <!-- Team Tabs -->
                <ul class="nav nav-tabs" id="lineupTeamTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="team1-tab" data-bs-toggle="tab" data-bs-target="#lineup-team1-tab" type="button" role="tab">
                            <i class="ti ti-users me-1"></i><span id="lineup-team1-name"><?= lang('Common.team') ?> 1</span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="team2-tab" data-bs-toggle="tab" data-bs-target="#lineup-team2-tab" type="button" role="tab">
                            <i class="ti ti-users me-1"></i><span id="lineup-team2-name"><?= lang('Common.team') ?> 2</span>
                        </button>
                    </li>
                </ul>

                <div class="tab-content">
                    <!-- Team 1 -->
                    <div class="tab-pane fade show active" id="lineup-team1-tab" role="tabpanel">
                        <input type="hidden" id="lineup-team1-id">
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                            <div>
                                <span class="badge bg-success me-2"><?= lang('Common.starter') ?>: <span id="team1-starter-count">0</span>/11</span>
                                <span class="badge bg-warning text-dark"><?= lang('Common.substitute') ?>: <span id="team1-sub-count">0</span></span>
                            </div>
                            <small class="text-muted"><?= lang('Common.max_11_starters') ?></small>
                        </div>
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-hover player-select-table" id="lineup-team1-table">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th width="70" class="text-center"><?= lang('Common.starter') ?></th>
                                        <th width="70" class="text-center"><?= lang('Common.substitute') ?></th>
                                        <th width="70" class="text-center"><?= lang('Common.shirt_number') ?></th>
                                        <th><?= lang('Common.full_name') ?></th>
                                        <th width="120"><?= lang('Common.position') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="lineup-team1-players">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4"><?= lang('Common.loading') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary" onclick="saveTeamLineup(1)">
                                <i class="ti ti-device-floppy me-1"></i><?= lang('Common.save_lineup') ?> <span id="lineup-team1-name-btn"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Team 2 -->
                    <div class="tab-pane fade" id="lineup-team2-tab" role="tabpanel">
                        <input type="hidden" id="lineup-team2-id">
                        <div class="d-flex justify-content-between align-items-center mb-3 mt-3">
                            <div>
                                <span class="badge bg-success me-2"><?= lang('Common.starter') ?>: <span id="team2-starter-count">0</span>/11</span>
                                <span class="badge bg-warning text-dark"><?= lang('Common.substitute') ?>: <span id="team2-sub-count">0</span></span>
                            </div>
                            <small class="text-muted"><?= lang('Common.max_11_starters') ?></small>
                        </div>
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-hover player-select-table" id="lineup-team2-table">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th width="70" class="text-center"><?= lang('Common.starter') ?></th>
                                        <th width="70" class="text-center"><?= lang('Common.substitute') ?></th>
                                        <th width="70" class="text-center"><?= lang('Common.shirt_number') ?></th>
                                        <th><?= lang('Common.full_name') ?></th>
                                        <th width="120"><?= lang('Common.position') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="lineup-team2-players">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4"><?= lang('Common.loading') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary" onclick="saveTeamLineup(2)">
                                <i class="ti ti-device-floppy me-1"></i><?= lang('Common.save_lineup') ?> <span id="lineup-team2-name-btn"></span>
                            </button>
                        </div>
                    </div>

                    <!-- Single Team Pane (for new UI) -->
                    <div id="lineup-single-team-pane" style="display: none;">
                        <input type="hidden" id="lineup-single-team-id">
                        <h5 class="mb-3"><i class="ti ti-users me-2"></i><span id="lineup-single-team-name"><?= lang('Common.team') ?></span></h5>
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <div>
                                <span class="badge bg-success me-2"><?= lang('Common.starter') ?>: <span id="lineup-single-starter-count">0</span>/11</span>
                                <span class="badge bg-warning text-dark"><?= lang('Common.substitute') ?>: <span id="lineup-single-sub-count">0</span></span>
                            </div>
                            <small class="text-muted"><?= lang('Common.max_11_starters') ?></small>
                        </div>
                        <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                            <table class="table table-hover player-select-table">
                                <thead class="sticky-top bg-white">
                                    <tr>
                                        <th width="70" class="text-center"><?= lang('Common.starter') ?></th>
                                        <th width="70" class="text-center"><?= lang('Common.substitute') ?></th>
                                        <th width="70" class="text-center"><?= lang('Common.shirt_number') ?></th>
                                        <th><?= lang('Common.full_name') ?></th>
                                        <th width="120"><?= lang('Common.position') ?></th>
                                    </tr>
                                </thead>
                                <tbody id="lineup-single-team-players">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4"><?= lang('Common.loading') ?></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3 d-flex justify-content-end">
                            <button class="btn btn-primary" onclick="saveLineupSingleTeam()">
                                <i class="ti ti-device-floppy me-1"></i><?= lang('Common.save_lineup') ?>
                            </button>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<!-- Add Event Modal (Micromodal) -->
<div class="micromodal" id="event-modal" aria-hidden="true">
    <div class="micromodal-overlay" tabindex="-1" data-micromodal-close>
        <div class="micromodal-container modal-sm" id="event-modal-container" role="dialog" aria-modal="true" aria-labelledby="event-modal-title">
            <header class="micromodal-header">
                <h2 class="micromodal-title" id="event-modal-title">
                    <i class="ti ti-ball-football me-2"></i><?= lang('Common.add_event') ?>
                </h2>
                <button class="micromodal-close" aria-label="Close" data-micromodal-close>&times;</button>
            </header>
            <main class="micromodal-body">
                <input type="hidden" id="event-match-id">

                <!-- แจ้งเตือนในตัว modal เอง ไม่เด้ง popup ซ้อน popup -->
                <div class="alert alert-warning d-none py-2 px-3 mb-3" id="event-modal-alert" role="alert">
                    <i class="ti ti-alert-triangle me-1"></i><span id="event-modal-alert-text"></span>
                </div>

                <!-- Team Selector (for old UI) -->
                <div class="mb-3" id="event-team-container">
                    <label class="form-label"><?= lang('Common.team') ?></label>
                    <select class="form-select" id="event-team-select" onchange="loadEventPlayers()">
                        <option value="">-- <?= lang('Common.select_team') ?> --</option>
                    </select>
                </div>

                <!-- Team Display (for new UI - single team) -->
                <div class="mb-3" id="event-modal-team-display-container" style="display: none;">
                    <label class="form-label"><?= lang('Common.team') ?></label>
                    <div class="form-control bg-light" id="event-modal-team-display" style="pointer-events: none;"></div>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= lang('Common.event_type') ?></label>
                    <select class="form-select" id="event-type-select" onchange="togglePlayer2Field()">
                        <option value="">-- <?= lang('Common.select_type') ?> --</option>
                        <option value="1"><?= lang('Common.goal_scored') ?></option>
                        <option value="5"><?= lang('Common.own_goal') ?></option>
                        <option value="3"><?= lang('Common.yellow_card') ?></option>
                        <option value="4"><?= lang('Common.red_card') ?></option>
                        <option value="2"><?= lang('Common.substitution') ?></option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label"><?= lang('Common.player') ?></label>
                    <select class="form-select" id="event-player1-select">
                        <option value="">-- <?= lang('Common.select_player') ?> --</option>
                    </select>
                </div>

                <div class="mb-3" id="event-player2-container" style="display: none;">
                    <label class="form-label" id="event-player2-label"><?= lang('Common.substitute_player') ?></label>
                    <select class="form-select" id="event-player2-select">
                        <option value="">-- <?= lang('Common.select_player') ?> --</option>
                    </select>
                </div>

                <div class="mb-3" id="event-single-minute-container">
                    <label class="form-label"><?= lang('Common.minute') ?></label>
                    <input type="number" class="form-control" id="event-minute" min="1" max="120" placeholder="<?= lang('Common.example_minute') ?>">
                </div>

                <!-- โหมดเปลี่ยนตัว — สองฝั่งจิ้มจับคู่ ไม่ต้องค้นใน dropdown -->
                <div id="sub-multi-container" style="display: none;">
                    <div class="sub-hint mb-2" id="sub-hint">
                        <i class="ti ti-hand-click me-1"></i><?= lang('Common.sub_pick_hint') ?>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="sub-pane sub-pane--bench">
                                <div class="sub-pane__head">
                                    <span><i class="ti ti-users me-1"></i><?= lang('Common.substitute') ?></span>
                                    <span class="badge bg-warning text-dark" id="sub-bench-count">0</span>
                                </div>
                                <div class="sub-pane__body" id="sub-bench-list"></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="sub-pane sub-pane--pitch">
                                <div class="sub-pane__head">
                                    <span><i class="ti ti-run me-1"></i><?= lang('Common.starter') ?></span>
                                    <span class="badge bg-success" id="sub-pitch-count">0</span>
                                </div>
                                <div class="sub-pane__body" id="sub-pitch-list"></div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="form-label fw-bold mb-0">
                                <i class="ti ti-transfer me-1"></i><?= lang('Common.sub_pairs_title') ?>
                                <span class="badge bg-primary rounded-pill ms-1" id="sub-pair-count">0</span>
                            </label>
                        </div>
                        <div id="sub-pair-list"></div>
                        <div class="text-muted small" id="sub-multi-empty">
                            <i class="ti ti-info-circle me-1"></i><?= lang('Common.sub_no_row') ?>
                        </div>
                    </div>
                </div>

            </main>
            <footer class="micromodal-footer">
                <button type="button" class="btn btn-secondary" data-micromodal-close><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-success" onclick="saveEvent()">
                    <i class="ti ti-plus me-1"></i><?= lang('Common.add_event') ?>
                </button>
            </footer>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?= $this->include('Modules\Admin\Views\Tournament\partials\score_management\scripts') ?>
<?php $this->endSection(); ?>
