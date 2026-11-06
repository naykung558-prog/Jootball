<?php $this->extend('template/layout') ?>

<?php $this->section('style'); ?>
<style>
    .bar-chart-row {
        display: flex;
        align-items: center;
        margin-bottom: 0.5rem;
    }
    .bar-chart-label {
        min-width: 140px;
        font-size: 0.85rem;
        font-weight: 500;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
    .bar-chart-bar {
        flex-grow: 1;
        background-color: #e9ecef;
        border-radius: 4px;
        height: 24px;
        position: relative;
        overflow: hidden;
    }
    .bar-chart-fill {
        height: 100%;
        border-radius: 4px;
        transition: width 0.6s ease;
        min-width: 2px;
    }
    .bar-chart-value {
        min-width: 60px;
        text-align: right;
        font-size: 0.85rem;
        font-weight: 600;
        padding-left: 0.5rem;
    }
    /* Filter selects: กันช่องแคบเกินไป (padding ลูกศรจัดการที่ global .form-select แล้ว) */
    .report-filter .form-select { min-width: 130px; }
    .report-filter select[name="year"] { min-width: 95px; }
    .report-filter select[name="group_id"] { min-width: 200px; max-width: 260px; }
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>

<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-calendar-alt me-2 text-primary"></i><?= lang('Common.report_monthly') ?>
            </h4>
            <p class="text-muted mb-0"><?= esc($academy['name'] ?? '') ?></p>
        </div>
        <a href="<?= base_url('academy/reports/' . ($academy['id'] ?? '')) ?>" class="btn btn-outline-secondary btn-sm rounded-pill mt-2 mt-md-0">
            <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
        </a>
    </div>

    <!-- Month/Year Selector -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body py-3 px-4">
            <form method="get" action="<?= base_url('academy/report-monthly/' . ($academy['id'] ?? '')) ?>" class="row g-2 align-items-end report-filter">
                <div class="col-auto">
                    <label class="form-label small fw-bold text-muted mb-1"><?= lang('Common.report_select_month') ?></label>
                    <select name="month" class="form-select form-select-sm">
                        <?php
                        $monthNames = explode(',', lang('Common.report_month_names'));
                        for ($m = 1; $m <= 12; $m++):
                        ?>
                            <option value="<?= $m ?>" <?= ((int)$month === $m) ? 'selected' : '' ?>>
                                <?= $monthNames[$m - 1] ?? $m ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small fw-bold text-muted mb-1"><?= lang('Common.report_select_year') ?></label>
                    <select name="year" class="form-select form-select-sm">
                        <?php for ($y = (int)$year - 2; $y <= (int)$year + 1; $y++): ?>
                            <option value="<?= $y ?>" <?= ((int)$year === $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small fw-bold text-muted mb-1"><?= lang('Common.report_filter_group') ?></label>
                    <select name="group_id" class="form-select form-select-sm">
                        <option value="0"><?= lang('Common.report_all_groups') ?></option>
                        <?php foreach (($allGroups ?? []) as $g): ?>
                            <option value="<?= (int)$g['id'] ?>" <?= ((int)($selectedGroupId ?? 0) === (int)$g['id']) ? 'selected' : '' ?>>
                                <?= esc($g['name'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-primary btn-sm">
                        <i class="fas fa-search me-1"></i><?= lang('Common.search') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Overview Stat Cards -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-soft-primary rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fas fa-calendar-check fa-lg text-primary"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold text-dark mb-1"><?= (int)($summary['total_sessions'] ?? 0) ?></h3>
                    <p class="text-muted small mb-0"><?= lang('Common.report_total_sessions') ?></p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-soft-success rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fas fa-check-circle fa-lg text-success"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold text-success mb-1"><?= (int)($summary['completed_sessions'] ?? 0) ?></h3>
                    <p class="text-muted small mb-0"><?= lang('Common.report_completed_sessions') ?></p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-soft-danger rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fas fa-times-circle fa-lg text-danger"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold text-danger mb-1"><?= (int)($summary['cancelled_sessions'] ?? 0) ?></h3>
                    <p class="text-muted small mb-0"><?= lang('Common.report_cancelled_sessions') ?></p>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3">
            <div class="card shadow-sm border-0 rounded-4 h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-2">
                        <span class="d-inline-flex align-items-center justify-content-center bg-soft-warning rounded-circle" style="width: 50px; height: 50px;">
                            <i class="fas fa-percentage fa-lg text-warning"></i>
                        </span>
                    </div>
                    <h3 class="fw-bold text-warning mb-1"><?= number_format((float)($summary['attendance_rate'] ?? 0), 1) ?>%</h3>
                    <p class="text-muted small mb-0"><?= lang('Common.report_attendance_rate') ?></p>
                </div>
            </div>
        </div>
    </div>

    <!-- Group Details Table with Bar Chart -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-layer-group me-2"></i><?= lang('Common.report_group_performance') ?>
            </h5>
        </div>
        <div class="card-body p-4">
            <?php if (!empty($groups)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-4">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-3 py-3 border-0 text-uppercase text-muted small fw-bold"><?= lang('Common.report_group_name') ?></th>
                                <th class="py-3 border-0 text-uppercase text-muted small fw-bold text-center"><?= lang('Common.report_session_count') ?></th>
                                <th class="py-3 border-0 text-uppercase text-muted small fw-bold text-center"><?= lang('Common.report_attendance_rate') ?></th>
                                <th class="py-3 border-0 text-uppercase text-muted small fw-bold text-end pe-3"><?= lang('Common.action') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($groups as $group): ?>
                                <?php
                                $gStats = $group['stats'] ?? [];
                                $gSessionCount = (int)($gStats['session_count'] ?? 0);
                                $gAttendanceRate = (float)($gStats['attendance_rate'] ?? 0);
                                ?>
                                <tr>
                                    <td class="ps-3 py-3">
                                        <div class="d-flex align-items-center">
                                            <span class="d-inline-flex align-items-center justify-content-center bg-soft-primary rounded-circle me-2" style="width: 36px; height: 36px;">
                                                <i class="fas fa-users text-primary small"></i>
                                            </span>
                                            <div class="fw-medium"><?= esc($group['name'] ?? '-') ?></div>
                                        </div>
                                    </td>
                                    <td class="py-3 text-center">
                                        <span class="badge bg-soft-primary text-primary rounded-pill px-3 py-1">
                                            <?= $gSessionCount ?>
                                        </span>
                                    </td>
                                    <td class="py-3 text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="progress flex-grow-1" style="height: 8px; max-width: 120px;">
                                                <div class="progress-bar <?= $gAttendanceRate >= 80 ? 'bg-success' : ($gAttendanceRate >= 50 ? 'bg-warning' : 'bg-danger') ?>"
                                                     role="progressbar"
                                                     style="width: <?= $gAttendanceRate ?>%"
                                                     aria-valuenow="<?= $gAttendanceRate ?>"
                                                     aria-valuemin="0"
                                                     aria-valuemax="100"></div>
                                            </div>
                                            <span class="small fw-medium"><?= number_format($gAttendanceRate, 1) ?>%</span>
                                        </div>
                                    </td>
                                    <td class="py-3 text-end pe-3">
                                        <a href="<?= base_url('academy/report-group/' . ($academy['id'] ?? '') . '/' . ($group['id'] ?? '')) ?>"
                                           class="btn btn-outline-primary btn-sm rounded-pill">
                                            <i class="fas fa-eye me-1"></i><?= lang('Common.report_view_detail') ?>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- CSS Bar Chart: Attendance Rate by Group -->
                <div class="mt-3">
                    <h6 class="fw-bold text-muted text-uppercase mb-3">
                        <i class="fas fa-chart-bar me-1"></i><?= lang('Common.report_attendance_rate') ?>
                    </h6>
                    <?php foreach ($groups as $group): ?>
                        <?php
                        $gStats = $group['stats'] ?? [];
                        $gRate = (float)($gStats['attendance_rate'] ?? 0);
                        $barColor = $gRate >= 80 ? '#198754' : ($gRate >= 50 ? '#ffc107' : '#dc3545');
                        ?>
                        <div class="bar-chart-row">
                            <div class="bar-chart-label text-dark"><?= esc($group['name'] ?? '-') ?></div>
                            <div class="bar-chart-bar mx-2">
                                <div class="bar-chart-fill" style="width: <?= $gRate ?>%; background-color: <?= $barColor ?>;"></div>
                            </div>
                            <div class="bar-chart-value" style="color: <?= $barColor ?>;"><?= number_format($gRate, 1) ?>%</div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <i class="fas fa-chart-pie fa-3x text-muted mb-3 opacity-50"></i>
                    <h6 class="text-muted"><?= lang('Common.report_no_data') ?></h6>
                </div>
            <?php endif; ?>
        </div>
    </div>

</div>

<?php $this->endSection(); ?>
