<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Header -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-chart-bar me-2"></i><?= lang('Common.idp_group_report') ?>
                    </h4>
                    <p class="text-muted small mb-0">
                        <?= esc($academy['name'] ?? '') ?>
                        <?php if (!empty($group['name'])): ?>
                            &mdash; <strong><?= esc($group['name']) ?></strong>
                        <?php endif; ?>
                        <?php if (!empty($group['age_group'])): ?>
                            <span class="badge bg-light text-dark ms-1"><?= esc($group['age_group']) ?></span>
                        <?php endif; ?>
                    </p>
                    <!-- Plan details -->
                    <?php if (!empty($planInfo)): ?>
                    <div class="mt-2 d-flex align-items-center gap-3 flex-wrap">
                        <?php if (!empty($planInfo['template_title'])): ?>
                        <span class="badge bg-info text-dark">
                            <i class="fas fa-file-alt me-1"></i><?= esc($planInfo['template_title']) ?>
                        </span>
                        <?php endif; ?>
                        <?php if (!empty($planInfo['coach_name'])): ?>
                        <small class="text-muted">
                            <i class="fas fa-user-tie me-1"></i><?= esc($planInfo['coach_name']) ?>
                        </small>
                        <?php endif; ?>
                        <?php if (!empty($planInfo['player_count'])): ?>
                        <small class="text-muted">
                            <i class="fas fa-running me-1"></i><?= $planInfo['player_count'] ?> <?= lang('Common.players') ?>
                        </small>
                        <?php endif; ?>
                        <?php if (!empty($planInfo['created_at'])): ?>
                        <small class="text-muted">
                            <i class="fas fa-calendar-plus me-1"></i><?= date('d/m/Y', strtotime($planInfo['created_at'])) ?>
                        </small>
                        <?php endif; ?>
                    </div>
                    <?php endif; ?>
                </div>
                <div class="d-flex align-items-center gap-2 flex-wrap">
                    <a href="<?= base_url('academy/idp/group/assess/' . $academy['id'] . '/' . $group['id']) ?>"
                       class="btn btn-outline-success btn-sm rounded-pill">
                        <i class="fas fa-clipboard-check me-1"></i><?= lang('Common.idp_group_assess') ?>
                    </a>
                    <a href="<?= base_url('academy/idp/' . $academy['id']) ?>"
                       class="btn btn-outline-secondary btn-sm rounded-pill">
                        <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php if (empty($comparisonData)): ?>
        <!-- No data -->
        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body text-center py-5">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3 opacity-25"></i>
                <p class="text-muted"><?= lang('Common.no_data') ?></p>
            </div>
        </div>
    <?php else: ?>

    <!-- Section 0: Assessment Summary Cards -->
    <?php if (!empty($summaryData) && $summaryData['total_sessions'] > 0): ?>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center py-3">
                    <div class="mb-1"><i class="fas fa-clipboard-list fa-2x text-primary opacity-75"></i></div>
                    <h3 class="mb-0 fw-bold text-primary"><?= $summaryData['total_sessions'] ?></h3>
                    <small class="text-muted"><?= lang('Common.idp_report_total_sessions') ?></small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center py-3">
                    <div class="mb-1"><i class="fas fa-calendar-alt fa-2x text-info opacity-75"></i></div>
                    <h6 class="mb-0 fw-bold text-info">
                        <?php if ($summaryData['date_from'] && $summaryData['date_to']): ?>
                            <?= date('d M', strtotime($summaryData['date_from'])) ?>
                            - <?= date('d M Y', strtotime($summaryData['date_to'])) ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </h6>
                    <small class="text-muted"><?= lang('Common.idp_report_date_range') ?></small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center py-3">
                    <div class="mb-1"><i class="fas fa-layer-group fa-2x text-success opacity-75"></i></div>
                    <h3 class="mb-0 fw-bold text-success"><?= $summaryData['dims_covered'] ?>/<?= count($dimensions) ?></h3>
                    <small class="text-muted"><?= lang('Common.idp_report_dims_covered') ?></small>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center py-3">
                    <div class="mb-1"><i class="fas fa-users fa-2x text-warning opacity-75"></i></div>
                    <h3 class="mb-0 fw-bold text-warning"><?= $summaryData['players_assessed'] ?></h3>
                    <small class="text-muted"><?= lang('Common.idp_report_players_assessed') ?></small>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Section 1: Charts Side-by-Side (Trend + Radar) -->
    <div class="row g-4 mb-4">
        <!-- Trend Line Chart -->
        <div class="col-lg-6">
            <?php if (!empty($trendData['dates']) && count($trendData['dates']) >= 2): ?>
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-success"></i><?= lang('Common.idp_group_trends') ?></h5>
                    <small class="text-muted"><?= lang('Common.idp_normalized_score') ?></small>
                </div>
                <div class="card-body p-3 d-flex align-items-center">
                    <div style="width: 100%;">
                        <canvas id="trendLineChart"></canvas>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-chart-line me-2 text-success"></i><?= lang('Common.idp_group_trends') ?></h5>
                </div>
                <div class="card-body text-center py-5 d-flex align-items-center justify-content-center">
                    <div>
                        <i class="fas fa-chart-line fa-2x text-muted opacity-25 mb-2"></i>
                        <p class="text-muted small mb-0"><?= lang('Common.idp_group_trends_need_more') ?></p>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <!-- Radar Chart -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold"><i class="fas fa-spider me-2 text-primary"></i><?= lang('Common.idp_group_radar') ?></h5>
                    <small class="text-muted"><?= lang('Common.idp_normalized_score') ?></small>
                </div>
                <div class="card-body p-3 d-flex align-items-center">
                    <div style="width: 100%;">
                        <canvas id="groupRadarChart"></canvas>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Dimension Comparison Table -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold"><i class="fas fa-table me-2 text-info"></i><?= lang('Common.idp_group_comparison') ?></h5>
            <small class="text-muted"><?= lang('Common.idp_normalized_score') ?></small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="text-center" style="min-width: 200px; position: sticky; left: 0; background: #f8f9fa; z-index: 2;">
                                <?= lang('Common.players') ?>
                            </th>
                            <?php foreach ($dimensions as $dim):
                                $color = $dim['color'] ?? '#0d6efd';
                                $icon  = $dim['icon'] ?? 'fa-star';
                                $dimId = (int)$dim['id'];
                                $lastAssessed = isset($summaryData['dim_last_assessed'][$dimId]) ? $summaryData['dim_last_assessed'][$dimId] : null;
                            ?>
                            <th class="text-center" style="min-width: 120px;">
                                <i class="fas <?= esc($icon) ?>" style="color: <?= esc($color) ?>;"></i>
                                <br>
                                <small style="font-size: 0.7rem; color: <?= esc($color) ?>; white-space: nowrap;"><?= esc($dim['name']) ?></small>
                                <?php if ($lastAssessed): ?>
                                <br><small class="text-muted" style="font-size: 0.55rem;"><?= date('d/m', strtotime($lastAssessed)) ?></small>
                                <?php endif; ?>
                            </th>
                            <?php endforeach; ?>
                            <th class="text-center" style="min-width: 70px;"><?= lang('Common.idp_avg_score') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comparisonData as $player):
                            $playerName = urlencode($player['player_name'] ?? '');
                            $imgPath = !empty($player['player_image'])
                                ? base_url($player['player_image'])
                                : 'https://ui-avatars.com/api/?name=' . $playerName . '&size=80&background=0d6efd&color=fff&bold=true&format=svg';
                            $rowTotal = 0;
                            $rowCount = 0;
                        ?>
                        <tr>
                            <td style="position: sticky; left: 0; background: #fff; z-index: 1;">
                                <div class="d-flex align-items-center">
                                    <img src="<?= $imgPath ?>" class="rounded-circle me-2"
                                         style="width: 32px; height: 32px; object-fit: cover;"
                                         onerror="this.src='https://ui-avatars.com/api/?name=<?= $playerName ?>&size=80&background=6c757d&color=fff&format=svg'">
                                    <div>
                                        <span class="fw-bold" style="font-size: 0.85rem;">
                                            <?php if (!empty($player['shirt_number'])): ?>
                                                <span class="badge bg-dark me-1" style="font-size: 0.6rem;">#<?= esc($player['shirt_number']) ?></span>
                                            <?php endif; ?>
                                            <?= esc($player['player_name']) ?>
                                        </span>
                                    </div>
                                </div>
                            </td>
                            <?php foreach ($player['dimensions'] as $dimScore):
                                $score = (float)$dimScore['score'];
                                if ($score > 0) { $rowTotal += $score; $rowCount++; }
                                $cellClass = '';
                                if ($score >= 4) $cellClass = 'score-4';
                                elseif ($score >= 3) $cellClass = 'score-3';
                                elseif ($score >= 2) $cellClass = 'score-2';
                                elseif ($score > 0) $cellClass = 'score-1';
                                $cellDimId = (int)$dimScore['dimension_id'];
                                $cellPlanId = (int)($player['plan_id'] ?? 0);
                                $cellDimName = '';
                                $cellDimColor = '#0d6efd';
                                $cellDimIcon = 'fa-star';
                                foreach ($dimensions as $d) {
                                    if ((int)$d['id'] === $cellDimId) {
                                        $cellDimName = $d['name'];
                                        $cellDimColor = $d['color'] ?? '#0d6efd';
                                        $cellDimIcon = $d['icon'] ?? 'fa-star';
                                        break;
                                    }
                                }
                            ?>
                            <td class="text-center <?= $cellClass ?>" style="font-size: 0.9rem; font-weight: 600;">
                                <?php if ($score > 0 && $cellPlanId > 0): ?>
                                    <button type="button" class="score-cell-btn js-open-dim-detail"
                                            data-plan-id="<?= $cellPlanId ?>"
                                            data-dimension-id="<?= $cellDimId ?>"
                                            data-player-name="<?= esc($player['player_name'] ?? '', 'attr') ?>"
                                            data-player-image="<?= esc($player['player_image'] ?? '', 'attr') ?>"
                                            data-shirt-number="<?= esc($player['shirt_number'] ?? '', 'attr') ?>"
                                            data-dimension-name="<?= esc($cellDimName, 'attr') ?>"
                                            data-dimension-color="<?= esc($cellDimColor, 'attr') ?>"
                                            data-dimension-icon="<?= esc($cellDimIcon, 'attr') ?>"
                                            title="คลิกเพื่อดูรายละเอียดการประเมิน">
                                        <?= number_format($score, 1) ?>
                                    </button>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <?php endforeach; ?>
                            <?php $avgScore = $rowCount > 0 ? $rowTotal / $rowCount : 0; ?>
                            <td class="text-center fw-bold" style="font-size: 0.9rem;">
                                <?= $avgScore > 0 ? number_format($avgScore, 1) : '-' ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <?php
                        // คำนวณค่ามากสุดของกลุ่มต่อ dimension จาก $comparisonData
                        $dimMaxes = []; // [dimension_id => max]
                        foreach ($dimensions as $dim) {
                            $dimId = (int)$dim['id'];
                            $max = 0;
                            foreach ($comparisonData as $player) {
                                foreach ($player['dimensions'] as $ds) {
                                    if ((int)$ds['dimension_id'] === $dimId) {
                                        $s = (float)$ds['score'];
                                        if ($s > $max) $max = $s;
                                    }
                                }
                            }
                            $dimMaxes[$dimId] = $max;
                        }
                        $overallMax = !empty($dimMaxes) ? max($dimMaxes) : 0;
                        ?>
                        <tr style="border-top: 2px solid #adb5bd;">
                            <td class="fw-bold" style="position: sticky; left: 0; background: #f8f9fa; z-index: 2;">
                                <i class="fas fa-trophy me-2 text-secondary"></i><?= lang('Common.idp_group_max') ?>
                            </td>
                            <?php foreach ($dimensions as $dim):
                                $dimId = (int)$dim['id'];
                                $max = $dimMaxes[$dimId] ?? 0;
                                $footClass = '';
                                if ($max >= 4) $footClass = 'score-4';
                                elseif ($max >= 3) $footClass = 'score-3';
                                elseif ($max >= 2) $footClass = 'score-2';
                                elseif ($max > 0) $footClass = 'score-1';
                            ?>
                            <td class="text-center fw-bold <?= $footClass ?>" style="font-size: 0.95rem;">
                                <?= $max > 0 ? number_format($max, 1) : '-' ?>
                            </td>
                            <?php endforeach; ?>
                            <td class="text-center fw-bold" style="font-size: 0.95rem; background: #e9ecef;">
                                <?= $overallMax > 0 ? number_format($overallMax, 1) : '-' ?>
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Section 3: Top Performers per Dimension -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold"><i class="fas fa-trophy me-2 text-warning"></i><?= lang('Common.idp_top_performers') ?></h5>
        </div>
        <div class="card-body p-4">
            <div class="row g-3">
                <?php foreach ($topPerformers as $dimId => $perfData):
                    $dim = $perfData['dimension'];
                    $players = $perfData['players'];
                    if (empty($players)) continue;
                ?>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="card border rounded-3 h-100">
                        <div class="card-header py-2 px-3" style="background: <?= esc($dim['color']) ?>15;">
                            <h6 class="mb-0 fw-bold" style="color: <?= esc($dim['color']) ?>;">
                                <i class="fas <?= esc($dim['icon'] ?? 'fa-star') ?> me-1"></i>
                                <?= esc($dim['name']) ?>
                            </h6>
                        </div>
                        <div class="card-body p-2">
                            <?php foreach ($players as $rank => $p):
                                $pName = urlencode($p['player_name'] ?? '');
                                $pImg = !empty($p['player_image'])
                                    ? base_url($p['player_image'])
                                    : 'https://ui-avatars.com/api/?name=' . $pName . '&size=80&background=0d6efd&color=fff&bold=true&format=svg';
                                $medals = ['text-warning', 'text-secondary', 'text-danger'];
                                $medalColor = isset($medals[$rank]) ? $medals[$rank] : 'text-muted';
                            ?>
                            <div class="d-flex align-items-center py-1 px-2 <?= $rank > 0 ? 'border-top' : '' ?>">
                                <span class="fw-bold me-2 <?= $medalColor ?>" style="font-size: 1.1rem;">
                                    <?= $rank + 1 ?>
                                </span>
                                <img src="<?= $pImg ?>" class="rounded-circle me-2"
                                     style="width: 28px; height: 28px; object-fit: cover;"
                                     onerror="this.src='https://ui-avatars.com/api/?name=<?= $pName ?>&size=80&background=6c757d&color=fff&format=svg'">
                                <div class="flex-grow-1">
                                    <small class="fw-bold">
                                        <?php if (!empty($p['shirt_number'])): ?>
                                            <span class="badge bg-dark me-1" style="font-size: 0.55rem;">#<?= esc($p['shirt_number']) ?></span>
                                        <?php endif; ?>
                                        <?= esc($p['player_name']) ?>
                                    </small>
                                </div>
                                <span class="badge rounded-pill" style="background: <?= esc($dim['color']) ?>; color: #fff;">
                                    <?= number_format($p['score'], 1) ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- Section 4: Indicator Heatmap (Normalized 0-5) -->
    <?php if (!empty($heatmapData['plans']) && !empty($heatmapData['dimensions'])): ?>
    <?php
        // Check if there are any scores at all
        $hasAnyScores = false;
        foreach ($heatmapData['scores'] as $planScores) {
            if (!empty($planScores)) { $hasAnyScores = true; break; }
        }

        // Helper: measurement type short label for tooltip
        $mtypeLabels = [
            'rating'     => 'Rating 1-5',
            'percentage' => '0-100%',
            'score'      => 'Score',
            'time'       => 'Time',
            'distance'   => 'Distance',
        ];
    ?>
    <?php if ($hasAnyScores): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold"><i class="fas fa-th me-2 text-success"></i><?= lang('Common.idp_indicator_heatmap') ?></h5>
            <small class="text-muted"><?= lang('Common.idp_heatmap_desc') ?></small>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-bordered table-sm mb-0" id="heatmapTable">
                    <thead>
                        <!-- Dimension header row -->
                        <tr class="table-light">
                            <th rowspan="2" class="text-center align-middle" style="min-width: 180px; position: sticky; left: 0; background: #f8f9fa; z-index: 3;">
                                <?= lang('Common.players') ?>
                            </th>
                            <?php foreach ($heatmapData['dimensions'] as $dim):
                                $indCount = count($dim['indicators']);
                                if ($indCount === 0) continue;
                            ?>
                            <th colspan="<?= $indCount ?>" class="text-center" style="background: <?= esc($dim['color'] ?? '#0d6efd') ?>15; color: <?= esc($dim['color'] ?? '#0d6efd') ?>; font-size: 0.75rem;">
                                <i class="fas <?= esc($dim['icon'] ?? 'fa-star') ?> me-1"></i>
                                <?= esc(mb_substr($dim['name'], 0, 10)) ?>
                            </th>
                            <?php endforeach; ?>
                        </tr>
                        <!-- Indicator header row with measurement type -->
                        <tr class="table-light">
                            <?php foreach ($heatmapData['dimensions'] as $dim): ?>
                                <?php foreach ($dim['indicators'] as $ind):
                                    $mtype = isset($ind['measurement_type']) ? $ind['measurement_type'] : 'rating';
                                    $mtypeLabel = isset($mtypeLabels[$mtype]) ? $mtypeLabels[$mtype] : $mtype;
                                    $maxV = isset($ind['max_value']) ? $ind['max_value'] : '';
                                    $unit = isset($ind['unit']) ? $ind['unit'] : '';
                                    // Build full tooltip: "indicator name (Rating 1-5)" or "indicator name (0-100%)"
                                    $tooltip = esc($ind['name']) . ' (' . $mtypeLabel;
                                    if ($mtype !== 'rating' && $mtype !== 'percentage' && $maxV) {
                                        $tooltip .= ' 0-' . $maxV;
                                    }
                                    if ($unit) {
                                        $tooltip .= ' ' . $unit;
                                    }
                                    $tooltip .= ')';
                                ?>
                                <th class="text-center" style="min-width: 60px; font-size: 0.6rem; writing-mode: vertical-rl; text-orientation: mixed; height: 90px; padding: 4px 2px;" title="<?= $tooltip ?>">
                                    <?= esc(mb_substr($ind['name'], 0, 12)) ?>
                                    <br><span class="text-muted" style="font-size: 0.5rem;"><?= esc($mtypeLabel) ?></span>
                                </th>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($heatmapData['plans'] as $plan):
                            $planId = (int)$plan['id'];
                            $pName = urlencode($plan['player_name'] ?? '');
                            $pImg = !empty($plan['player_image'])
                                ? base_url($plan['player_image'])
                                : 'https://ui-avatars.com/api/?name=' . $pName . '&size=80&background=0d6efd&color=fff&bold=true&format=svg';
                        ?>
                        <tr>
                            <td style="position: sticky; left: 0; background: #fff; z-index: 1;">
                                <div class="d-flex align-items-center">
                                    <img src="<?= $pImg ?>" class="rounded-circle me-1"
                                         style="width: 24px; height: 24px; object-fit: cover;"
                                         onerror="this.src='https://ui-avatars.com/api/?name=<?= $pName ?>&size=80&background=6c757d&color=fff&format=svg'">
                                    <small class="fw-bold" style="font-size: 0.75rem;">
                                        <?php if (!empty($plan['shirt_number'])): ?>
                                            <span class="badge bg-dark" style="font-size: 0.5rem;">#<?= esc($plan['shirt_number']) ?></span>
                                        <?php endif; ?>
                                        <?= esc($plan['player_name']) ?>
                                    </small>
                                </div>
                            </td>
                            <?php foreach ($heatmapData['dimensions'] as $dim): ?>
                                <?php foreach ($dim['indicators'] as $ind):
                                    $indId = (int)$ind['id'];
                                    $rawScore = isset($heatmapData['scores'][$planId][$indId])
                                        ? (float)$heatmapData['scores'][$planId][$indId]
                                        : 0;

                                    // Normalize raw score to 0-5 for color coding
                                    $normScore = 0;
                                    if ($rawScore > 0) {
                                        $mtype = isset($ind['measurement_type']) ? $ind['measurement_type'] : 'rating';
                                        $maxV = (float)(isset($ind['max_value']) ? $ind['max_value'] : 5);
                                        $minV = (float)(isset($ind['min_value']) ? $ind['min_value'] : 0);
                                        $isLower = !empty($ind['is_lower_better']);

                                        if ($mtype === 'rating') {
                                            $normScore = $rawScore;
                                        } elseif ($mtype === 'percentage') {
                                            $normScore = ($rawScore / 100.0) * 5.0;
                                        } else {
                                            $range = $maxV - $minV;
                                            if ($range > 0) {
                                                $ratio = ($rawScore - $minV) / $range;
                                                if ($isLower) { $ratio = 1.0 - $ratio; }
                                                $normScore = $ratio * 5.0;
                                            } else {
                                                $normScore = $rawScore;
                                            }
                                        }
                                        $normScore = max(0, min(5, $normScore));
                                    }

                                    // Color coding based on normalized score
                                    $scoreClass = '';
                                    if ($normScore >= 4) $scoreClass = 'hm-5';
                                    elseif ($normScore >= 3) $scoreClass = 'hm-4';
                                    elseif ($normScore >= 2) $scoreClass = 'hm-3';
                                    elseif ($normScore >= 1) $scoreClass = 'hm-2';
                                    elseif ($normScore > 0) $scoreClass = 'hm-1';
                                ?>
                                <td class="text-center <?= $scoreClass ?>" style="font-size: 0.75rem; padding: 4px 2px;"
                                    title="<?= $rawScore > 0 ? number_format($normScore, 1) . '/5' : '' ?>">
                                    <?= $rawScore > 0 ? number_format($normScore, 1) : '' ?>
                                </td>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Legend -->
        <div class="card-footer bg-white border-0 py-2 px-4">
            <div class="d-flex align-items-center gap-3 flex-wrap">
                <small class="text-muted fw-bold"><?= lang('Common.idp_normalized_score') ?>:</small>
                <span class="d-inline-flex align-items-center gap-1"><span class="d-inline-block rounded" style="width:14px;height:14px;background:#f8d7da;"></span><small>0-1 <?= lang('Common.idp_score_low') ?></small></span>
                <span class="d-inline-flex align-items-center gap-1"><span class="d-inline-block rounded" style="width:14px;height:14px;background:#fff3cd;"></span><small>1-2</small></span>
                <span class="d-inline-flex align-items-center gap-1"><span class="d-inline-block rounded" style="width:14px;height:14px;background:#d1ecf1;"></span><small>2-3 <?= lang('Common.idp_score_medium') ?></small></span>
                <span class="d-inline-flex align-items-center gap-1"><span class="d-inline-block rounded" style="width:14px;height:14px;background:#d4edda;"></span><small>3-4 <?= lang('Common.idp_score_high') ?></small></span>
                <span class="d-inline-flex align-items-center gap-1"><span class="d-inline-block rounded" style="width:14px;height:14px;background:#198754;"></span><small>4-5 <?= lang('Common.idp_score_excellent') ?></small></span>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>

    <?php endif; ?><!-- end: if comparisonData not empty -->
</div>

<style>
/* Comparison table cell colors — use !important to override Bootstrap .table td */
.table td.score-1 { background-color: #f8d7da !important; color: #212529 !important; font-weight: 600; }
.table td.score-2 { background-color: #fff3cd !important; color: #212529 !important; font-weight: 600; }
.table td.score-3 { background-color: #d1ecf1 !important; color: #212529 !important; font-weight: 600; }
.table td.score-4 { background-color: #d4edda !important; color: #212529 !important; font-weight: 600; }
.score-cell-btn {
    background: none;
    border: none;
    padding: 0;
    font: inherit;
    color: inherit;
    cursor: pointer;
    width: 100%;
    text-align: center;
    text-decoration: none;
}
.score-cell-btn:hover {
    text-decoration: underline;
    filter: brightness(0.9);
}
.score-cell-btn:focus {
    outline: 2px solid #0d6efd;
    outline-offset: -2px;
}
#dimensionDetailModal .indicator-table th,
#dimensionDetailModal .indicator-table td {
    font-size: 0.85rem;
    vertical-align: middle;
}
#dimensionDetailModal .modal-header {
    background: linear-gradient(90deg, var(--dim-color, #0d6efd) 0%, rgba(255,255,255,0.3) 100%);
}

/* Heatmap cell colors (normalized 0-5) */
.table td.hm-1 { background-color: #f8d7da !important; color: #212529 !important; font-weight: 600; }
.table td.hm-2 { background-color: #fff3cd !important; color: #212529 !important; font-weight: 600; }
.table td.hm-3 { background-color: #d1ecf1 !important; color: #212529 !important; font-weight: 600; }
.table td.hm-4 { background-color: #d4edda !important; color: #212529 !important; font-weight: 600; }
.table td.hm-5 { background-color: #198754 !important; color: #fff !important; font-weight: 600; }
</style>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
// ============================
// Trend Line Chart
// ============================
var trendData = <?= json_encode($trendData ?? ['dates' => [], 'dimensions' => []]) ?>;

if (trendData.dates && trendData.dates.length >= 2 && trendData.dimensions && trendData.dimensions.length > 0) {
    var trendLabels = [];
    for (var i = 0; i < trendData.dates.length; i++) {
        var parts = trendData.dates[i].split('-');
        trendLabels.push(parts[2] + '/' + parts[1]);
    }

    var trendDatasets = [];
    for (var d = 0; d < trendData.dimensions.length; d++) {
        var dim = trendData.dimensions[d];
        var hasData = false;
        for (var s = 0; s < dim.scores.length; s++) {
            if (dim.scores[s] !== null) { hasData = true; break; }
        }
        if (!hasData) continue;

        trendDatasets.push({
            label: dim.name,
            data: dim.scores,
            borderColor: dim.color || '#0d6efd',
            backgroundColor: (dim.color || '#0d6efd') + '20',
            borderWidth: 2,
            pointRadius: 3,
            pointHoverRadius: 5,
            fill: false,
            tension: 0.3,
            spanGaps: true
        });
    }

    if (trendDatasets.length > 0) {
        new Chart(document.getElementById('trendLineChart'), {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: trendDatasets
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                aspectRatio: 1.4,
                interaction: {
                    mode: 'index',
                    intersect: false
                },
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 10,
                            usePointStyle: true,
                            font: { size: 10 }
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(ctx) {
                                if (ctx.parsed.y === null) return ctx.dataset.label + ': -';
                                return ctx.dataset.label + ': ' + ctx.parsed.y.toFixed(2);
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 5,
                        ticks: { stepSize: 1, font: { size: 10 } }
                    },
                    x: {
                        ticks: { font: { size: 10 } }
                    }
                }
            }
        });
    }
}

// ============================
// Group Radar Chart
// ============================
var comparisonData = <?= json_encode($comparisonData ?? []) ?>;
var dimensionLabels = <?= json_encode(array_map(function($d) { return $d['name']; }, $dimensions ?? [])) ?>;

if (comparisonData.length > 0 && dimensionLabels.length > 0) {
    function generateColors(count) {
        var colors = [];
        for (var i = 0; i < count; i++) {
            var hue = Math.round((360 / count) * i);
            colors.push({
                border: 'hsla(' + hue + ', 70%, 50%, 0.85)',
                bg: 'hsla(' + hue + ', 70%, 50%, 0.08)'
            });
        }
        return colors;
    }

    var colors = generateColors(comparisonData.length);
    var datasets = [];

    // Group max (baseline) per dimension
    var groupMax = [];
    for (var d = 0; d < dimensionLabels.length; d++) {
        var mx = 0;
        for (var p = 0; p < comparisonData.length; p++) {
            var dsAtD = comparisonData[p].dimensions[d];
            var sc = dsAtD ? (Number(dsAtD.score) || 0) : 0;
            if (sc > mx) mx = sc;
        }
        groupMax.push(mx);
    }

    // Baseline dataset — filled grey, drawn behind player lines
    datasets.push({
        label: '<?= lang('Common.idp_group_max') ?>',
        data: groupMax,
        borderColor: 'rgba(108, 117, 125, 0.85)',
        backgroundColor: 'rgba(108, 117, 125, 0.22)',
        borderWidth: 2,
        borderDash: [5, 5],
        pointRadius: 3,
        pointBackgroundColor: 'rgba(108, 117, 125, 0.85)',
        fill: true
    });

    for (var i = 0; i < comparisonData.length; i++) {
        var plan = comparisonData[i];
        var scores = [];
        for (var j = 0; j < plan.dimensions.length; j++) {
            scores.push(plan.dimensions[j].score);
        }
        var label = plan.player_name;
        if (plan.shirt_number) {
            label = '#' + plan.shirt_number + ' ' + label;
        }
        datasets.push({
            label: label,
            data: scores,
            borderColor: colors[i].border,
            backgroundColor: 'transparent',
            borderWidth: 2,
            pointRadius: 3,
            fill: false
        });
    }

    new Chart(document.getElementById('groupRadarChart'), {
        type: 'radar',
        data: {
            labels: dimensionLabels,
            datasets: datasets
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            aspectRatio: 1.1,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: {
                        padding: 10,
                        usePointStyle: true,
                        font: { size: 10 }
                    }
                }
            },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 5,
                    ticks: { stepSize: 1, font: { size: 9 } },
                    pointLabels: { font: { size: 10 } }
                }
            }
        }
    });
}

// ============================
// Dimension Detail Modal
// ============================
document.addEventListener('DOMContentLoaded', function() {
    var modalEl = document.getElementById('dimensionDetailModal');
    if (!modalEl) return;
    var modal = new bootstrap.Modal(modalEl);
    var loading = document.getElementById('dimDetailLoading');
    var content = document.getElementById('dimDetailContent');
    var errBox = document.getElementById('dimDetailError');
    var planLink = document.getElementById('dimDetailPlanLink');

    function setHeader(btn) {
        var name = btn.getAttribute('data-player-name') || '';
        var shirt = btn.getAttribute('data-shirt-number') || '';
        var img = btn.getAttribute('data-player-image') || '';
        var dimName = btn.getAttribute('data-dimension-name') || '';
        var dimColor = btn.getAttribute('data-dimension-color') || '#0d6efd';
        var dimIcon = btn.getAttribute('data-dimension-icon') || 'fa-star';

        var headerName = (shirt ? '#' + shirt + ' ' : '') + name;
        document.getElementById('dimDetailPlayerName').textContent = headerName;
        document.getElementById('dimDetailDimName').textContent = dimName;
        document.getElementById('dimDetailIcon').className = 'fas ' + dimIcon + ' me-1';

        var imgEl = document.getElementById('dimDetailPlayerImg');
        var fallback = 'https://ui-avatars.com/api/?name=' + encodeURIComponent(name)
                     + '&size=80&background=0d6efd&color=fff&bold=true&format=svg';
        imgEl.src = img ? (img.indexOf('http') === 0 ? img : '<?= base_url() ?>/' + img) : fallback;
        imgEl.onerror = function() { this.src = fallback; };

        modalEl.style.setProperty('--dim-color', dimColor);
    }

    function renderAssessment(label, info) {
        if (!info) return '';
        if (info.is_dimension_level) {
            return '<div class="mb-3"><h6 class="fw-bold">' + label + '</h6>'
                + '<div class="text-muted small mb-2"><i class="fas fa-calendar me-1"></i>'
                + (info.assessed_at || '-') + '</div>'
                + '<div class="alert alert-info mb-0 py-2 small">'
                + 'การประเมินระดับมิติ (ไม่มี indicator breakdown) — คะแนน: '
                + '<strong>' + Number(info.normalized_score || 0).toFixed(2) + '</strong> / 5</div></div>';
        }
        var rows = '';
        (info.indicators || []).forEach(function(ind, idx) {
            rows += '<tr>'
                + '<td class="text-center text-muted small">' + (idx+1) + '</td>'
                + '<td>' + escapeHtml(ind.name || '') + '</td>'
                + '<td class="text-center">' + Number(ind.score).toFixed(2) + '</td>'
                + '<td class="text-center text-muted small">'
                  + Number(ind.min_value).toFixed(0) + ' – ' + Number(ind.max_value).toFixed(0)
                  + (ind.is_lower_better ? ' <i class="fas fa-arrow-down text-success" title="lower is better"></i>' : '')
                + '</td>'
                + '<td class="text-center fw-bold">' + Number(ind.normalized).toFixed(2) + '</td>'
                + '<td class="small text-muted">' + escapeHtml(ind.notes || '') + '</td>'
                + '</tr>';
        });
        return '<div class="mb-3">'
            + '<h6 class="fw-bold">' + label + '</h6>'
            + '<div class="text-muted small mb-2"><i class="fas fa-calendar me-1"></i>'
            + (info.assessed_at || '-') + '</div>'
            + '<div class="table-responsive"><table class="table table-bordered table-sm indicator-table mb-0">'
            + '<thead class="table-light"><tr>'
            + '<th class="text-center" style="width: 40px;">#</th>'
            + '<th>Indicator</th>'
            + '<th class="text-center" style="width: 80px;">คะแนน</th>'
            + '<th class="text-center" style="width: 100px;">ช่วง</th>'
            + '<th class="text-center" style="width: 100px;">Normalized</th>'
            + '<th>หมายเหตุ</th>'
            + '</tr></thead><tbody>' + rows + '</tbody>'
            + '<tfoot class="table-light"><tr>'
            + '<td colspan="4" class="text-end fw-bold">คะแนนรวม Normalized</td>'
            + '<td class="text-center fw-bold">' + Number(info.normalized_score || 0).toFixed(2) + '</td>'
            + '<td></td></tr></tfoot></table></div></div>';
    }

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, function(c) {
            return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
        });
    }

    function loadDetail(planId, dimensionId) {
        loading.classList.remove('d-none');
        content.classList.add('d-none');
        errBox.classList.add('d-none');
        planLink.classList.add('d-none');

        var url = '<?= base_url('academy/idp/dimension-detail') ?>'
                + '?plan_id=' + encodeURIComponent(planId)
                + '&dimension_id=' + encodeURIComponent(dimensionId);

        fetch(url, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function(r) { return r.json().then(function(j) { return { ok: r.ok, body: j }; }); })
            .then(function(res) {
                loading.classList.add('d-none');
                if (!res.ok || !res.body || res.body.ok !== true) {
                    var msg = (res.body && res.body.error) ? res.body.error : 'โหลดข้อมูลไม่สำเร็จ';
                    errBox.textContent = msg;
                    errBox.classList.remove('d-none');
                    return;
                }
                var data = res.body;
                var html = '';
                if (!data.latest && !data.baseline) {
                    html = '<div class="alert alert-warning mb-0">ยังไม่มีข้อมูลการประเมินใน dimension นี้</div>';
                } else {
                    html += renderAssessment('การประเมินล่าสุด', data.latest);
                    if (data.baseline) {
                        html += renderAssessment('Baseline', data.baseline);
                    }
                }
                content.innerHTML = html;
                content.classList.remove('d-none');

                if (data.plan && data.plan.academy_id && data.plan.id) {
                    planLink.href = '<?= base_url('academy/idp/plan/view') ?>/'
                                  + data.plan.academy_id + '/' + data.plan.id;
                    planLink.classList.remove('d-none');
                }
            })
            .catch(function(e) {
                loading.classList.add('d-none');
                errBox.textContent = 'เครือข่ายมีปัญหา: ' + (e.message || e);
                errBox.classList.remove('d-none');
            });
    }

    document.querySelectorAll('.js-open-dim-detail').forEach(function(btn) {
        btn.addEventListener('click', function(ev) {
            ev.preventDefault();
            var planId = btn.getAttribute('data-plan-id');
            var dimId = btn.getAttribute('data-dimension-id');
            if (!planId || !dimId) return;
            setHeader(btn);
            modal.show();
            loadDetail(planId, dimId);
        });
    });
});

</script>

<!-- Dimension Detail Modal -->
<div class="modal fade" id="dimensionDetailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div class="d-flex align-items-center gap-3 w-100">
                    <img id="dimDetailPlayerImg" src="" alt="" class="rounded-circle"
                         style="width: 48px; height: 48px; object-fit: cover; border: 2px solid #fff;">
                    <div class="flex-grow-1">
                        <h5 class="modal-title mb-0" id="dimDetailPlayerName">—</h5>
                        <small class="text-dark">
                            <i id="dimDetailIcon" class="fas fa-star me-1"></i>
                            <span id="dimDetailDimName">—</span>
                        </small>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
            </div>
            <div class="modal-body" id="dimDetailBody">
                <div class="text-center py-5" id="dimDetailLoading">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="dimDetailContent" class="d-none"></div>
                <div id="dimDetailError" class="alert alert-danger d-none mb-0"></div>
            </div>
            <div class="modal-footer">
                <a id="dimDetailPlanLink" href="#" class="btn btn-outline-primary btn-sm d-none">
                    <i class="fas fa-external-link-alt me-1"></i>ดูแผน IDP เต็ม
                </a>
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>
