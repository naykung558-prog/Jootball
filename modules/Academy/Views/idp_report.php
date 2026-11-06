<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-chart-line me-2 text-primary"></i><?= lang('Common.idp_report_title') ?>
            </h4>
            <p class="text-muted mb-0">
                <i class="fas fa-user me-1"></i><?= esc(($player['first_name'] ?? '') . ' ' . ($player['last_name'] ?? '')) ?>
                <?php if (!empty($player['nick_name'])): ?> (<?= esc($player['nick_name']) ?>)<?php endif; ?>
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <?php if (!empty($activePlan)): ?>
                <a href="<?= base_url('academy/idp/plan/view/' . $academy['id'] . '/' . $activePlan['id']) ?>"
                   class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-file-alt me-1"></i><?= lang('Common.idp_view_plan') ?>
                </a>
            <?php endif; ?>
            <a href="<?= base_url('academy/idp/' . $academy['id']) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold mb-0 text-primary"><?= $idpSummary['plan_count'] ?? 0 ?></h3>
                    <small class="text-muted"><?= lang('Common.idp_total_plans') ?></small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold mb-0 text-success"><?= ($idpSummary['attendance']['attendance_rate'] ?? 0) ?>%</h3>
                    <small class="text-muted"><?= lang('Common.report_attendance_summary') ?></small>
                </div>
            </div>
        </div>
        <?php if (!empty($idpSummary['plan_summary'])): ?>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold mb-0 text-warning"><?= $idpSummary['plan_summary']['avg_achievement'] ?? 0 ?>%</h3>
                    <small class="text-muted"><?= lang('Common.idp_avg_achievement') ?></small>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-3">
                    <h3 class="fw-bold mb-0" style="color: #6f42c1;"><?= $idpSummary['plan_summary']['goal_count'] ?? 0 ?></h3>
                    <small class="text-muted"><?= lang('Common.idp_goal_count') ?></small>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <div class="row g-4">
        <!-- Radar Chart: Baseline vs Current -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="mb-0 fw-bold">
                        <i class="fas fa-spider me-2"></i><?= lang('Common.idp_report_baseline_vs_current') ?>
                    </h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="compareRadarChart" height="300"></canvas>
                    <?php if (empty($compareData)): ?>
                        <p class="text-center text-muted mt-2"><small><?= lang('Common.no_data') ?></small></p>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Goals Progress -->
        <div class="col-lg-6">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-bullseye me-2"></i><?= lang('Common.idp_goals') ?></h6>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($goals)): ?>
                        <p class="text-center text-muted"><?= lang('Common.idp_goal_no_goals') ?></p>
                    <?php else: ?>
                        <?php foreach ($goals as $dimGroup): ?>
                            <h6 class="fw-bold mb-2" style="color: <?= esc($dimGroup['dimension_color']) ?>">
                                <i class="fas <?= esc($dimGroup['dimension_icon']) ?> me-1"></i>
                                <?= esc($dimGroup['dimension_name']) ?>
                            </h6>
                            <?php foreach ($dimGroup['goals'] as $goal): ?>
                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small class="fw-bold"><?= esc($goal['title']) ?></small>
                                        <small class="text-muted"><?= $goal['achievement_percent'] ?? 0 ?>%</small>
                                    </div>
                                    <div class="progress" style="height: 6px;">
                                        <?php $pct = (int)($goal['achievement_percent'] ?? 0); ?>
                                        <div class="progress-bar bg-<?= $pct >= 100 ? 'success' : ($pct >= 50 ? 'primary' : 'warning') ?>"
                                             style="width: <?= $pct ?>%"></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                            <hr class="my-2">
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Plans History -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h6 class="mb-0 fw-bold"><i class="fas fa-history me-2"></i><?= lang('Common.idp_total_plans') ?></h6>
        </div>
        <div class="card-body p-4">
            <?php if (empty($plans)): ?>
                <p class="text-center text-muted"><?= lang('Common.idp_no_plans') ?></p>
            <?php else: ?>
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-light">
                            <tr>
                                <th><?= lang('Common.idp_plan_title') ?></th>
                                <th><?= lang('Common.idp_plan_season') ?></th>
                                <th><?= lang('Common.idp_plan_start_date') ?></th>
                                <th><?= lang('Common.idp_plan_end_date') ?></th>
                                <th><?= lang('Common.idp_plan_coach') ?></th>
                                <th><?= lang('Common.idp_plan_status') ?></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($plans as $p): ?>
                            <tr>
                                <td class="fw-bold"><?= esc($p['title'] ?? '-') ?></td>
                                <td><?= esc($p['season'] ?? '-') ?></td>
                                <td><?= esc($p['start_date'] ?? '-') ?></td>
                                <td><?= esc($p['end_date'] ?? '-') ?></td>
                                <td><?= esc($p['coach_name'] ?? '-') ?></td>
                                <td>
                                    <?php
                                    $sColors = ['draft' => 'secondary', 'active' => 'success', 'completed' => 'info', 'cancelled' => 'danger'];
                                    $sColor = $sColors[$p['status'] ?? 'draft'] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $sColor ?>"><?= lang('Common.idp_status_' . ($p['status'] ?? 'draft')) ?></span>
                                </td>
                                <td>
                                    <a href="<?= base_url('academy/idp/plan/view/' . $academy['id'] . '/' . $p['id']) ?>"
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Feedback Timeline -->
    <?php if (!empty($feedbacks)): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h6 class="mb-0 fw-bold"><i class="fas fa-comments me-2"></i><?= lang('Common.idp_feedback') ?></h6>
        </div>
        <div class="card-body p-4">
            <?php foreach ($feedbacks as $fb): ?>
                <div class="border-start border-3 ps-3 mb-3" style="border-color: #0d6efd !important;">
                    <span class="badge bg-primary mb-1"><?= lang('Common.idp_feedback_type_' . str_replace('coach_', '', str_replace('player_', '', $fb['feedback_type'] ?? 'observation'))) ?></span>
                    <small class="text-muted ms-2"><?= esc($fb['feedback_date']) ?></small>
                    <p class="mb-1 mt-1"><?= nl2br(esc($fb['content'])) ?></p>
                    <?php if (!empty($fb['strengths'])): ?>
                        <small class="text-success"><strong><?= lang('Common.idp_feedback_strengths') ?>:</strong> <?= esc($fb['strengths']) ?></small><br>
                    <?php endif; ?>
                    <?php if (!empty($fb['areas_to_improve'])): ?>
                        <small class="text-warning"><strong><?= lang('Common.idp_feedback_areas_to_improve') ?>:</strong> <?= esc($fb['areas_to_improve']) ?></small>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <!-- Indicator Detail Breakdown -->
    <?php if (!empty($indicatorScores)): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h6 class="mb-0 fw-bold"><i class="fas fa-list-ul me-2"></i><?= lang('Common.idp_indicator_detail') ?></h6>
        </div>
        <div class="card-body p-4">
            <?php foreach ($indicatorScores as $dimId => $dimData): ?>
            <div class="card border rounded-3 mb-3">
                <div class="card-header py-2 px-3" style="background: <?= esc($dimData['dimension']['color']) ?>15;">
                    <h6 class="mb-0 fw-bold" style="color: <?= esc($dimData['dimension']['color']) ?>;">
                        <i class="fas <?= esc($dimData['dimension']['icon']) ?> me-2"></i><?= esc($dimData['dimension']['name']) ?>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <tbody>
                            <?php foreach ($dimData['indicators'] as $ind):
                                $maxVal = $ind['max_value'] > 0 ? $ind['max_value'] : 5;
                                $pctVal = ($ind['score'] / $maxVal) * 100;
                                if ($pctVal > 100) $pctVal = 100;
                                $barColor = $dimData['dimension']['color'];
                            ?>
                            <tr>
                                <td style="width: 40%;" class="ps-3"><?= esc($ind['name']) ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="progress flex-grow-1" style="height: 20px;">
                                            <div class="progress-bar" role="progressbar"
                                                 style="width: <?= $pctVal ?>%; background-color: <?= esc($barColor) ?>;"
                                                 aria-valuenow="<?= $ind['score'] ?>" aria-valuemin="0" aria-valuemax="<?= $maxVal ?>">
                                                <?= number_format($ind['score'], 1) ?>/<?= number_format($maxVal, 0) ?>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php elseif (!empty($activePlan)): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body text-center py-4">
            <i class="fas fa-chart-bar fa-2x text-muted mb-2 opacity-25"></i>
            <p class="text-muted mb-0"><?= lang('Common.idp_no_indicator_data') ?></p>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const compareData = <?= json_encode($compareData ?? []) ?>;
if (compareData.length > 0) {
    const labels = compareData.map(d => d.dimension_name);
    const baselineScores = compareData.map(d => d.baseline_score);
    const currentScores = compareData.map(d => d.current_score);

    new Chart(document.getElementById('compareRadarChart'), {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '<?= lang('Common.idp_assessment_baseline') ?>',
                    data: baselineScores,
                    borderColor: 'rgba(108,117,125,0.8)',
                    backgroundColor: 'rgba(108,117,125,0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 3,
                },
                {
                    label: '<?= lang('Common.idp_goal_current_value') ?>',
                    data: currentScores,
                    borderColor: 'rgba(40,167,69,0.8)',
                    backgroundColor: 'rgba(40,167,69,0.15)',
                    borderWidth: 2,
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 5,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}
</script>
<?php $this->endSection(); ?>
