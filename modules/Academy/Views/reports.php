<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>

<div class="container-fluid p-0 reports-page">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <?php
    $monthNames = explode(',', lang('Common.report_month_names'));
    $monthLabel = $monthNames[(int)$month - 1] ?? $month;

    $totalSessions     = (int)($monthlySummary['total_sessions'] ?? 0);
    $completedSessions = (int)($monthlySummary['completed_sessions'] ?? 0);
    $cancelledSessions = (int)($monthlySummary['cancelled_sessions'] ?? 0);
    $attendanceRate    = (float)($monthlySummary['attendance_rate'] ?? 0);
    $completedPct      = $totalSessions > 0 ? min(100, round(($completedSessions / $totalSessions) * 100)) : 0;

    $asSummary           = $assessmentSummary ?? [];
    $totalAssessments    = (int)($asSummary['total_assessments'] ?? 0);
    $totalPlayersTested  = (int)($asSummary['total_players_tested'] ?? 0);
    $avgScore            = (float)($asSummary['avg_score'] ?? 0);
    $avgScoreColor       = $avgScore >= 8 ? '#16a34a' : ($avgScore >= 6 ? '#f59e0b' : '#ef4444');

    $firstGroupId = '';
    if (!empty($groups)) {
        $firstGroup = reset($groups);
        $firstGroupId = $firstGroup['id'] ?? '';
    }
    ?>

    <!-- ===== Hero Header ===== -->
    <div class="reports-hero">
        <div class="reports-hero__body">
            <div class="reports-hero__crumb">
                <i class="fas fa-chart-line me-2"></i><?= lang('Common.reports') ?>
            </div>
            <h2 class="reports-hero__title"><?= lang('Common.report_overview_for') ?> <?= esc($academy['name'] ?? '') ?></h2>
            <div class="reports-hero__period">
                <i class="far fa-calendar-alt me-1"></i>
                <?= esc($monthLabel) ?> <?= esc((string)$year) ?>
            </div>
        </div>
        <form method="get" action="<?= base_url('academy/reports/' . ($academy['id'] ?? '')) ?>" class="reports-hero__filter">
            <div class="period-pill">
                <label class="period-pill__label"><i class="fas fa-calendar-alt"></i></label>
                <select name="month" class="period-pill__select" onchange="this.form.submit()">
                    <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= ((int)$month === $m) ? 'selected' : '' ?>>
                            <?= $monthNames[$m - 1] ?? $m ?>
                        </option>
                    <?php endfor; ?>
                </select>
                <span class="period-pill__divider"></span>
                <select name="year" class="period-pill__select" onchange="this.form.submit()">
                    <?php for ($y = (int)$year - 2; $y <= (int)$year + 1; $y++): ?>
                        <option value="<?= $y ?>" <?= ((int)$year === $y) ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>

    <!-- ===== KPI Row ===== -->
    <div class="kpi-grid">
        <!-- Sessions -->
        <div class="kpi-card kpi-card--blue">
            <div class="kpi-card__icon"><i class="fas fa-calendar-check"></i></div>
            <div class="kpi-card__body">
                <div class="kpi-card__label"><?= lang('Common.report_total_sessions') ?></div>
                <div class="kpi-card__value"><?= $totalSessions ?></div>
                <div class="kpi-card__progress">
                    <div class="kpi-card__progress-bar" style="width: <?= $completedPct ?>%;"></div>
                </div>
                <div class="kpi-card__hint">
                    <i class="fas fa-check-circle"></i> <?= $completedSessions ?> <?= lang('Common.report_completed_sessions') ?>
                </div>
            </div>
        </div>

        <!-- Cancelled -->
        <div class="kpi-card kpi-card--red">
            <div class="kpi-card__icon"><i class="fas fa-times-circle"></i></div>
            <div class="kpi-card__body">
                <div class="kpi-card__label"><?= lang('Common.report_cancelled_sessions') ?></div>
                <div class="kpi-card__value"><?= $cancelledSessions ?></div>
                <div class="kpi-card__hint">&nbsp;</div>
            </div>
        </div>

        <!-- Attendance -->
        <div class="kpi-card kpi-card--green">
            <div class="kpi-card__icon"><i class="fas fa-user-check"></i></div>
            <div class="kpi-card__body">
                <div class="kpi-card__label"><?= lang('Common.report_attendance_rate') ?></div>
                <div class="kpi-card__value"><?= number_format($attendanceRate, 1) ?>%</div>
                <div class="kpi-card__progress">
                    <div class="kpi-card__progress-bar" style="width: <?= min(100, $attendanceRate) ?>%;"></div>
                </div>
                <div class="kpi-card__hint">&nbsp;</div>
            </div>
        </div>

        <!-- Avg score -->
        <div class="kpi-card kpi-card--amber">
            <div class="kpi-card__icon"><i class="fas fa-star"></i></div>
            <div class="kpi-card__body">
                <div class="kpi-card__label"><?= lang('Common.report_overall_avg') ?></div>
                <div class="kpi-card__value">
                    <span style="color: <?= $avgScoreColor ?>;"><?= number_format($avgScore, 1) ?></span>
                    <span class="kpi-card__suffix">/10</span>
                </div>
                <div class="kpi-card__hint">
                    <i class="fas fa-clipboard-check"></i> <?= $totalAssessments ?> <?= lang('Common.report_total_assessments') ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ===== Quick Links ===== -->
    <div class="quick-links">
        <a href="<?= base_url('academy/report-daily/' . ($academy['id'] ?? '') . '?date=' . date('Y-m-d')) ?>"
           class="quick-link quick-link--blue">
            <span class="quick-link__icon"><i class="fas fa-calendar-day"></i></span>
            <div class="quick-link__body">
                <div class="quick-link__title"><?= lang('Common.report_daily_report') ?></div>
                <div class="quick-link__sub"><?= lang('Common.report_daily') ?></div>
            </div>
            <i class="fas fa-arrow-right quick-link__arrow"></i>
        </a>

        <a href="<?= base_url('academy/report-monthly/' . ($academy['id'] ?? '') . '?month=' . $month . '&year=' . $year) ?>"
           class="quick-link quick-link--green">
            <span class="quick-link__icon"><i class="fas fa-calendar-alt"></i></span>
            <div class="quick-link__body">
                <div class="quick-link__title"><?= lang('Common.report_monthly_report') ?></div>
                <div class="quick-link__sub"><?= lang('Common.report_monthly') ?></div>
            </div>
            <i class="fas fa-arrow-right quick-link__arrow"></i>
        </a>

        <a href="<?= !empty($firstGroupId) ? base_url('academy/report-group/' . ($academy['id'] ?? '') . '/' . $firstGroupId) : '#' ?>"
           class="quick-link quick-link--amber <?= empty($firstGroupId) ? 'is-disabled' : '' ?>">
            <span class="quick-link__icon"><i class="fas fa-users"></i></span>
            <div class="quick-link__body">
                <div class="quick-link__title"><?= lang('Common.report_group_reports') ?></div>
                <div class="quick-link__sub"><?= lang('Common.report_group') ?></div>
            </div>
            <i class="fas fa-arrow-right quick-link__arrow"></i>
        </a>
    </div>

    <!-- ===== Section: Training ===== -->
    <section class="report-section">
        <div class="report-section__header">
            <span class="report-section__bar" style="background: #2563eb;"></span>
            <div class="report-section__heading">
                <h5><i class="fas fa-clipboard-list me-2" style="color:#2563eb;"></i><?= lang('Common.report_section_training') ?></h5>
                <p><?= lang('Common.report_section_training_desc') ?></p>
            </div>
        </div>
        <div class="report-section__body">
            <?php if (!empty($monthlySummary['group_stats'])): ?>
                <div class="group-grid">
                    <?php foreach ($monthlySummary['group_stats'] as $gs):
                        $gName = $gs['group_name'] ?? '-';
                        $gInitial = mb_substr(trim($gName) !== '' ? trim($gName) : '?', 0, 1, 'UTF-8');
                    ?>
                    <div class="group-card">
                        <div class="group-card__head">
                            <div class="group-card__avatar"><?= esc($gInitial) ?></div>
                            <div class="group-card__name"><?= esc($gName) ?></div>
                        </div>
                        <div class="group-card__metric">
                            <div class="group-card__metric-value"><?= (int)($gs['session_count'] ?? 0) ?></div>
                            <div class="group-card__metric-label"><?= lang('Common.report_total_sessions') ?></div>
                        </div>
                        <?php if (!empty($gs['group_id'])): ?>
                        <a href="<?= base_url('academy/report-group/' . ($academy['id'] ?? '') . '/' . $gs['group_id']) ?>" class="group-card__link">
                            <i class="fas fa-eye me-1"></i><?= lang('Common.report_view_detail') ?>
                        </a>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="empty-state">
                    <i class="fas fa-chart-pie"></i>
                    <p><?= lang('Common.report_no_data') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- ===== Section: Assessments ===== -->
    <section class="report-section">
        <div class="report-section__header">
            <span class="report-section__bar" style="background: #16a34a;"></span>
            <div class="report-section__heading">
                <h5><i class="fas fa-tasks me-2" style="color:#16a34a;"></i><?= lang('Common.report_section_assessment') ?></h5>
                <p><?= lang('Common.report_section_assessment_desc') ?></p>
            </div>
            <a href="<?= base_url('academy/assessments/' . ($academy['id'] ?? '')) ?>" class="report-section__action">
                <i class="fas fa-list me-1"></i><?= lang('Common.assessments') ?>
            </a>
        </div>
        <div class="report-section__body">

            <!-- Stat tiles for assessment -->
            <div class="assess-stats">
                <div class="assess-stat">
                    <div class="assess-stat__icon" style="background:rgba(22,163,74,.12);color:#16a34a;"><i class="fas fa-file-alt"></i></div>
                    <div>
                        <div class="assess-stat__value"><?= $totalAssessments ?></div>
                        <div class="assess-stat__label"><?= lang('Common.report_total_assessments') ?></div>
                    </div>
                </div>
                <div class="assess-stat">
                    <div class="assess-stat__icon" style="background:rgba(2,132,199,.12);color:#0284c7;"><i class="fas fa-user-check"></i></div>
                    <div>
                        <div class="assess-stat__value"><?= $totalPlayersTested ?></div>
                        <div class="assess-stat__label"><?= lang('Common.report_players_tested') ?></div>
                    </div>
                </div>
                <div class="assess-stat">
                    <div class="assess-stat__icon" style="background:rgba(245,158,11,.12);color:#f59e0b;"><i class="fas fa-star"></i></div>
                    <div>
                        <div class="assess-stat__value">
                            <span style="color: <?= $avgScoreColor ?>;"><?= number_format($avgScore, 1) ?></span>
                            <span class="assess-stat__suffix">/10</span>
                        </div>
                        <div class="assess-stat__label"><?= lang('Common.report_overall_avg') ?></div>
                    </div>
                </div>
            </div>

            <?php if (!empty($asSummary['recent_assessments'])): ?>
            <div class="subsection-title">
                <i class="far fa-clock me-1 text-muted"></i><?= lang('Common.report_recent_assessments') ?>
            </div>
            <div class="assess-table-wrap">
                <table class="assess-table">
                    <thead>
                        <tr>
                            <th style="width: 40%"><?= lang('Common.assessments') ?></th>
                            <th><?= lang('Common.report_group_name') ?></th>
                            <th class="text-center"><?= lang('Common.report_players_tested') ?></th>
                            <th class="text-center"><?= lang('Common.report_avg_score') ?></th>
                            <th class="text-end"><?= lang('Common.action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($asSummary['recent_assessments'] as $ra):
                            $aType = $ra['assessment_type'] ?? '';
                            $typeKey = 'report_assessment_type_' . ($aType ?: 'custom');
                            $typeLabel = lang('Common.' . $typeKey);
                            if ($aType === 'skill')        { $typeBadgeColor = 'primary'; }
                            elseif ($aType === 'fitness')  { $typeBadgeColor = 'success'; }
                            elseif ($aType === 'match')    { $typeBadgeColor = 'warning'; }
                            else                            { $typeBadgeColor = 'secondary'; }
                            $raAvg = (float)($ra['avg_score'] ?? 0);
                            $raAvgColor = $raAvg >= 8 ? '#16a34a' : ($raAvg >= 6 ? '#f59e0b' : '#ef4444');
                        ?>
                        <tr>
                            <td>
                                <div class="cell-title"><?= esc($ra['name'] ?? '') ?></div>
                                <div class="cell-meta">
                                    <span class="badge bg-<?= $typeBadgeColor ?> bg-opacity-10 text-<?= $typeBadgeColor ?> rounded-pill"><?= $typeLabel ?></span>
                                    <span class="ms-1"><?= date('d/m/Y', strtotime($ra['assessment_date'])) ?></span>
                                </div>
                            </td>
                            <td><span class="text-muted small"><?= esc($ra['group_name'] ?? '-') ?></span></td>
                            <td class="text-center">
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill"><?= (int)($ra['player_count'] ?? 0) ?></span>
                            </td>
                            <td class="text-center">
                                <span class="score-pill" style="color: <?= $raAvgColor ?>; border-color: <?= $raAvgColor ?>33;">
                                    <?= number_format($raAvg, 1) ?><span class="score-pill__suffix">/10</span>
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="<?= base_url('academy/assessment/detail/' . ($academy['id'] ?? '') . '/' . $ra['id']) ?>" class="btn-detail">
                                    <i class="fas fa-eye me-1"></i><?= lang('Common.report_view_detail') ?>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php endif; ?>

            <?php if (!empty($asSummary['group_avg'])): ?>
            <div class="subsection-title mt-4">
                <i class="fas fa-chart-bar me-1 text-muted"></i><?= lang('Common.report_group_avg_scores') ?>
            </div>
            <div class="group-bars">
                <?php foreach ($asSummary['group_avg'] as $ga):
                    $gaAvg = (float)($ga['avg_score'] ?? 0);
                    $gaPercent = min(($gaAvg / 10) * 100, 100);
                    $gaColor = $gaAvg >= 8 ? '#16a34a' : ($gaAvg >= 6 ? '#f59e0b' : '#ef4444');
                ?>
                <div class="group-bar">
                    <div class="group-bar__head">
                        <span class="group-bar__name"><?= esc($ga['group_name'] ?? '-') ?></span>
                        <span class="group-bar__count"><?= (int)($ga['assessment_count'] ?? 0) ?> <?= lang('Common.report_assessment_times') ?></span>
                    </div>
                    <div class="group-bar__track">
                        <div class="group-bar__fill" style="width: <?= $gaPercent ?>%; background: <?= $gaColor ?>;"></div>
                    </div>
                    <div class="group-bar__foot">
                        <span class="group-bar__score" style="color: <?= $gaColor ?>;"><?= number_format($gaAvg, 1) ?></span>
                        <span class="group-bar__suffix">/10</span>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-clipboard-check"></i>
                <p><?= lang('Common.report_no_assessments') ?></p>
            </div>
            <?php endif; ?>
        </div>
    </section>

</div>

<style>
.reports-page {
    --rp-primary: #16478d;
    --rp-bg: #F6F9FC;
    --rp-text: #1f2937;
    --rp-muted: #6b7280;
    --rp-border: #e5e7eb;
    color: var(--rp-text);
}

/* Hero */
.reports-hero {
    background: linear-gradient(135deg, #16478d 0%, #1d5cb1 100%);
    color: #fff;
    padding: 28px 28px 24px;
    border-radius: 16px;
    margin: 1rem 0 1.25rem;
    box-shadow: 0 8px 24px rgba(22, 71, 141, 0.18);
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 1rem;
}
.reports-hero__crumb {
    display: inline-flex; align-items: center;
    background: rgba(255,255,255,0.16); padding: 5px 12px;
    border-radius: 999px; font-size: .8rem; font-weight: 500;
    backdrop-filter: blur(4px);
    margin-bottom: .65rem;
}
.reports-hero__title {
    font-weight: 700; font-size: 1.45rem; margin: 0; line-height: 1.3;
    color: #fff;
}
.reports-hero__period {
    margin-top: .5rem; font-size: .9rem; opacity: .85;
}
.reports-hero__filter { margin: 0; }
.period-pill {
    display: inline-flex; align-items: center;
    background: rgba(255,255,255,0.14);
    border: 1px solid rgba(255,255,255,0.22);
    border-radius: 999px;
    padding: 5px 8px;
    backdrop-filter: blur(6px);
}
.period-pill__label { color: #fff; padding: 0 8px; font-size: .9rem; }
.period-pill__select {
    background: transparent; border: 0; color: #fff;
    font-size: .9rem; font-weight: 600;
    padding: 6px 8px; outline: none; cursor: pointer;
}
.period-pill__select option { color: #1f2937; }
.period-pill__divider { width: 1px; height: 20px; background: rgba(255,255,255,0.3); margin: 0 4px; }

/* KPI Cards */
.kpi-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.kpi-card {
    background: #fff;
    border-radius: 14px;
    padding: 18px;
    display: flex; gap: 14px; align-items: flex-start;
    border: 1px solid var(--rp-border);
    box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
    transition: transform .2s ease, box-shadow .2s ease;
}
.kpi-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, .08);
}
.kpi-card__icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.kpi-card__body { flex: 1; min-width: 0; }
.kpi-card__label {
    font-size: .78rem; font-weight: 600; color: var(--rp-muted);
    text-transform: uppercase; letter-spacing: .03em;
    margin-bottom: 2px;
}
.kpi-card__value {
    font-size: 1.85rem; font-weight: 700; color: var(--rp-text);
    line-height: 1.1;
}
.kpi-card__suffix { font-size: 1rem; font-weight: 500; color: var(--rp-muted); }
.kpi-card__progress {
    height: 6px; border-radius: 999px; background: #f1f5f9;
    margin: 8px 0 6px;
    overflow: hidden;
}
.kpi-card__progress-bar { height: 100%; border-radius: 999px; }
.kpi-card__hint { font-size: .78rem; color: var(--rp-muted); }
.kpi-card--blue  .kpi-card__icon { background: rgba(37,99,235,.12); color: #2563eb; }
.kpi-card--blue  .kpi-card__progress-bar { background: #2563eb; }
.kpi-card--red   .kpi-card__icon { background: rgba(239,68,68,.12); color: #ef4444; }
.kpi-card--red   .kpi-card__value { color: #ef4444; }
.kpi-card--green .kpi-card__icon { background: rgba(22,163,74,.12); color: #16a34a; }
.kpi-card--green .kpi-card__progress-bar { background: #16a34a; }
.kpi-card--amber .kpi-card__icon { background: rgba(245,158,11,.12); color: #f59e0b; }

/* Quick links */
.quick-links {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
    gap: 1rem;
    margin-bottom: 1.5rem;
}
.quick-link {
    display: flex; align-items: center; gap: 14px;
    padding: 16px;
    border-radius: 14px;
    border: 1px solid var(--rp-border);
    background: #fff;
    text-decoration: none;
    transition: all .2s ease;
    color: var(--rp-text);
}
.quick-link:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 24px rgba(15, 23, 42, .08);
    color: var(--rp-text);
}
.quick-link.is-disabled { opacity: .5; pointer-events: none; }
.quick-link__icon {
    width: 44px; height: 44px;
    border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.quick-link__body { flex: 1; min-width: 0; }
.quick-link__title { font-weight: 700; font-size: .95rem; }
.quick-link__sub   { font-size: .78rem; color: var(--rp-muted); margin-top: 2px; }
.quick-link__arrow { color: var(--rp-muted); transition: transform .2s ease; }
.quick-link:hover .quick-link__arrow { transform: translateX(4px); }
.quick-link--blue  .quick-link__icon { background: rgba(37,99,235,.12);  color: #2563eb; }
.quick-link--green .quick-link__icon { background: rgba(22,163,74,.12);  color: #16a34a; }
.quick-link--amber .quick-link__icon { background: rgba(245,158,11,.12); color: #f59e0b; }

/* Section */
.report-section {
    background: #fff;
    border-radius: 14px;
    border: 1px solid var(--rp-border);
    box-shadow: 0 2px 8px rgba(15, 23, 42, .04);
    margin-bottom: 1.25rem;
    overflow: hidden;
}
.report-section__header {
    display: flex; align-items: center; gap: 12px;
    padding: 16px 20px;
    border-bottom: 1px solid var(--rp-border);
    background: #fafbfc;
}
.report-section__bar {
    width: 4px; align-self: stretch; border-radius: 999px;
}
.report-section__heading { flex: 1; min-width: 0; }
.report-section__heading h5 { margin: 0; font-weight: 700; font-size: 1rem; }
.report-section__heading p  { margin: 2px 0 0; font-size: .8rem; color: var(--rp-muted); }
.report-section__action {
    display: inline-flex; align-items: center;
    padding: 6px 14px; border-radius: 999px;
    background: #fff; border: 1px solid var(--rp-border);
    color: var(--rp-text); font-size: .82rem; font-weight: 500;
    text-decoration: none; transition: all .2s ease;
}
.report-section__action:hover {
    background: #16478d; color: #fff; border-color: #16478d;
}
.report-section__body { padding: 20px; }

/* Group cards (training) */
.group-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
    gap: 1rem;
}
.group-card {
    background: #fafbfc;
    border: 1px solid var(--rp-border);
    border-radius: 12px;
    padding: 14px;
    transition: all .2s ease;
}
.group-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(15, 23, 42, .08);
    border-color: #2563eb33;
}
.group-card__head {
    display: flex; align-items: center; gap: 10px;
    margin-bottom: 12px;
}
.group-card__avatar {
    width: 36px; height: 36px; border-radius: 10px;
    background: linear-gradient(135deg, #2563eb, #1d4ed8);
    color: #fff; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    text-transform: uppercase;
    flex-shrink: 0;
}
.group-card__name {
    font-weight: 600; font-size: .9rem;
    overflow: hidden; text-overflow: ellipsis; white-space: nowrap;
}
.group-card__metric {
    background: #fff; border-radius: 10px; padding: 10px;
    margin-bottom: 10px; text-align: center;
    border: 1px solid var(--rp-border);
}
.group-card__metric-value { font-size: 1.5rem; font-weight: 700; color: #2563eb; line-height: 1.1; }
.group-card__metric-label { font-size: .72rem; color: var(--rp-muted); margin-top: 2px; }
.group-card__link {
    display: block; width: 100%; text-align: center;
    padding: 8px; border-radius: 8px;
    background: #fff; border: 1px solid var(--rp-border);
    color: #2563eb; font-size: .82rem; font-weight: 500;
    text-decoration: none; transition: all .2s ease;
}
.group-card__link:hover { background: #2563eb; color: #fff; border-color: #2563eb; }

/* Assessment stats */
.assess-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 1.25rem;
}
.assess-stat {
    display: flex; gap: 12px; align-items: center;
    padding: 14px;
    background: #fafbfc;
    border: 1px solid var(--rp-border);
    border-radius: 12px;
}
.assess-stat__icon {
    width: 44px; height: 44px; border-radius: 12px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
}
.assess-stat__value { font-size: 1.5rem; font-weight: 700; line-height: 1.1; }
.assess-stat__label { font-size: .8rem; color: var(--rp-muted); margin-top: 2px; }
.assess-stat__suffix { font-size: .9rem; font-weight: 500; color: var(--rp-muted); }

.subsection-title {
    font-size: .85rem; font-weight: 600; color: #475569;
    margin-bottom: .85rem; padding-bottom: .5rem;
    border-bottom: 1px dashed var(--rp-border);
}

/* Assessment table */
.assess-table-wrap { overflow-x: auto; }
.assess-table {
    width: 100%; border-collapse: separate; border-spacing: 0;
    font-size: .9rem;
}
.assess-table thead th {
    text-align: left; padding: 10px 12px;
    font-size: .72rem; font-weight: 700; text-transform: uppercase;
    color: var(--rp-muted); letter-spacing: .03em;
    border-bottom: 1px solid var(--rp-border);
    background: #fafbfc;
}
.assess-table tbody td {
    padding: 12px;
    border-bottom: 1px solid var(--rp-border);
    vertical-align: middle;
}
.assess-table tbody tr:hover td { background: #fafbfc; }
.cell-title { font-weight: 600; color: var(--rp-text); margin-bottom: 3px; }
.cell-meta  { font-size: .75rem; color: var(--rp-muted); }
.score-pill {
    display: inline-flex; align-items: baseline; gap: 2px;
    padding: 4px 10px; border-radius: 999px;
    border: 1px solid; background: #fff;
    font-weight: 700; font-size: .9rem;
}
.score-pill__suffix { font-size: .7rem; color: var(--rp-muted); font-weight: 500; }
.btn-detail {
    display: inline-flex; align-items: center;
    padding: 5px 12px; border-radius: 999px;
    background: #16478d; color: #fff;
    font-size: .78rem; font-weight: 500; text-decoration: none;
    transition: all .2s ease;
}
.btn-detail:hover { background: #1d5cb1; color: #fff; transform: translateY(-1px); }

/* Group bar charts */
.group-bars {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
    gap: 1rem;
}
.group-bar {
    background: #fafbfc;
    border: 1px solid var(--rp-border);
    border-radius: 12px;
    padding: 14px;
}
.group-bar__head {
    display: flex; justify-content: space-between; align-items: center;
    margin-bottom: 10px;
}
.group-bar__name { font-weight: 600; font-size: .88rem; }
.group-bar__count { font-size: .72rem; color: var(--rp-muted); }
.group-bar__track {
    height: 10px; background: #e5e7eb; border-radius: 999px; overflow: hidden;
}
.group-bar__fill { height: 100%; border-radius: 999px; transition: width .4s ease; }
.group-bar__foot { text-align: right; margin-top: 6px; }
.group-bar__score { font-weight: 700; font-size: 1rem; }
.group-bar__suffix { color: var(--rp-muted); font-size: .8rem; }

/* Empty state */
.empty-state { text-align: center; padding: 32px 16px; color: var(--rp-muted); }
.empty-state i { font-size: 2.2rem; opacity: .35; margin-bottom: .5rem; display: block; }
.empty-state p { margin: 0; font-size: .85rem; }

@media (max-width: 768px) {
    .reports-hero { padding: 22px 18px; border-radius: 12px; }
    .reports-hero__title { font-size: 1.2rem; }
    .kpi-card { padding: 14px; }
    .kpi-card__value { font-size: 1.5rem; }
    .report-section__body { padding: 14px; }
}
</style>

<?php $this->endSection(); ?>
