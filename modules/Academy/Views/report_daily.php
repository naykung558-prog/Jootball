<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>

<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-calendar-day me-2 text-primary"></i><?= lang('Common.report_daily') ?>
            </h4>
            <p class="text-muted mb-0"><?= esc($academy['name'] ?? '') ?></p>
        </div>
        <a href="<?= base_url('academy/reports/' . ($academy['id'] ?? '')) ?>" class="btn btn-outline-secondary btn-sm rounded-pill mt-2 mt-md-0">
            <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
        </a>
    </div>

    <!-- Date Picker -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body py-3 px-4">
            <div class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small fw-bold text-muted mb-1"><?= lang('Common.report_select_date') ?></label>
                    <input type="date"
                           id="report-date-picker"
                           class="form-control form-control-sm"
                           value="<?= esc($date) ?>"
                           max="<?= date('Y-m-d') ?>">
                </div>
            </div>
        </div>
    </div>

    <!-- Sessions List -->
    <?php if (!empty($sessions)): ?>
        <div class="row g-3 mb-5">
            <?php foreach ($sessions as $session): ?>
                <div class="col-12">
                    <div class="card shadow-sm border-0 rounded-4">
                        <div class="card-header bg-white border-0 py-3 px-4 d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center">
                            <div class="d-flex align-items-center">
                                <span class="d-inline-flex align-items-center justify-content-center bg-soft-primary rounded-circle me-3" style="width: 42px; height: 42px;">
                                    <i class="fas fa-futbol text-primary"></i>
                                </span>
                                <div>
                                    <h6 class="mb-0 fw-bold text-dark"><?= esc($session['group_name'] ?? '-') ?></h6>
                                    <small class="text-muted">
                                        <i class="far fa-clock me-1"></i>
                                        <?= esc($session['start_time'] ?? '') ?> - <?= esc($session['end_time'] ?? '') ?>
                                    </small>
                                </div>
                            </div>
                            <div class="mt-2 mt-md-0">
                                <?php
                                $statusClass = 'bg-soft-primary text-primary';
                                $statusIcon = 'fa-clock';
                                $statusText = lang('Common.training_status_planned');

                                if (($session['status'] ?? '') === 'completed') {
                                    $statusClass = 'bg-soft-success text-success';
                                    $statusIcon = 'fa-check-circle';
                                    $statusText = lang('Common.training_status_completed');
                                } elseif (($session['status'] ?? '') === 'cancelled') {
                                    $statusClass = 'bg-soft-danger text-danger';
                                    $statusIcon = 'fa-times-circle';
                                    $statusText = lang('Common.training_status_cancelled');
                                }
                                ?>
                                <span class="badge <?= $statusClass ?> rounded-pill px-3 py-1 fw-medium">
                                    <i class="fas <?= $statusIcon ?> me-1"></i><?= $statusText ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body px-4 py-3">
                            <div class="row g-3">
                                <!-- Coach -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="p-3 bg-light rounded-3">
                                        <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.report_coach') ?></label>
                                        <div class="fw-medium">
                                            <?= esc(trim(($session['coach_first_name'] ?? '') . ' ' . ($session['coach_last_name'] ?? ''))) ?: '-' ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- Training Date -->
                                <div class="col-md-6 col-lg-3">
                                    <div class="p-3 bg-light rounded-3">
                                        <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.training_date') ?></label>
                                        <div class="fw-medium">
                                            <?= !empty($session['training_date']) ? date('d/m/Y', strtotime($session['training_date'])) : '-' ?>
                                        </div>
                                    </div>
                                </div>
                                <!-- Attendance Stats -->
                                <div class="col-md-12 col-lg-6">
                                    <div class="p-3 bg-light rounded-3">
                                        <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.training_attendance') ?></label>
                                        <div class="d-flex flex-wrap gap-2 mt-1">
                                            <span class="badge bg-success rounded-pill px-3 py-1">
                                                <i class="fas fa-check me-1"></i><?= lang('Common.report_present') ?>: <?= (int)($session['present_count'] ?? 0) ?>
                                            </span>
                                            <span class="badge bg-danger rounded-pill px-3 py-1">
                                                <i class="fas fa-times me-1"></i><?= lang('Common.report_absent') ?>: <?= (int)($session['absent_count'] ?? 0) ?>
                                            </span>
                                            <span class="badge bg-warning text-dark rounded-pill px-3 py-1">
                                                <i class="fas fa-clock me-1"></i><?= lang('Common.report_late') ?>: <?= (int)($session['late_count'] ?? 0) ?>
                                            </span>
                                            <span class="badge bg-secondary rounded-pill px-3 py-1">
                                                <i class="fas fa-users me-1"></i><?= lang('Common.attendance_total') ?>: <?= (int)($session['total_players'] ?? 0) ?>
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Notes -->
                            <?php if (!empty($session['notes'])): ?>
                                <div class="mt-3 p-3 bg-light rounded-3">
                                    <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.training_notes') ?></label>
                                    <div class="text-dark"><?= nl2br(esc($session['notes'])) ?></div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <!-- Empty State -->
        <div class="card shadow-sm border-0 rounded-4 mb-5">
            <div class="card-body text-center py-5">
                <i class="fas fa-calendar-times fa-3x text-muted mb-3 opacity-50"></i>
                <h6 class="text-muted"><?= lang('Common.report_no_sessions') ?></h6>
            </div>
        </div>
    <?php endif; ?>

</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    document.getElementById('report-date-picker').addEventListener('change', function() {
        const selectedDate = this.value;
        if (selectedDate) {
            window.location.href = '<?= base_url('academy/report-daily/' . ($academy['id'] ?? '')) ?>?date=' + selectedDate;
        }
    });
</script>
<?php $this->endSection(); ?>
