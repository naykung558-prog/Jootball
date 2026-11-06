<?php $this->extend('template/layout') ?>
<?php $this->section('content'); ?>

<?= $this->include('Modules\Academy\Views\subnav') ?>

<div class="container-fluid mt-3">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h4 class="mb-1"><i class="fas fa-users me-2"></i><?= lang('Common.report_group') ?></h4>
            <h5 class="text-muted mb-0"><?= esc($group['name']) ?></h5>
        </div>
        <a href="<?= base_url('academy/reports/' . $academy['id']) ?>" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
        </a>
    </div>

    <!-- Month/Year Selector -->
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-body">
            <form method="get" class="row g-2 align-items-end">
                <div class="col-auto">
                    <label class="form-label small fw-bold"><?= lang('Common.report_select_month') ?></label>
                    <select name="month" class="form-select form-select-sm">
                        <?php for ($m = 1; $m <= 12; $m++): ?>
                        <option value="<?= $m ?>" <?= ($month == $m) ? 'selected' : '' ?>><?= $m ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <label class="form-label small fw-bold"><?= lang('Common.report_select_year') ?></label>
                    <select name="year" class="form-select form-select-sm">
                        <?php for ($y = date('Y') - 2; $y <= date('Y') + 1; $y++): ?>
                        <option value="<?= $y ?>" <?= ($year == $y) ? 'selected' : '' ?>><?= $y ?></option>
                        <?php endfor; ?>
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="fas fa-filter me-1"></i><?= lang('Common.filter') ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Group Info + Stats -->
    <div class="row mb-4">
        <!-- Group Info -->
        <div class="col-md-4">
            <div class="card shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="fas fa-info-circle me-2 text-primary"></i><?= lang('Common.training_group_info') ?></h6>
                    <?php if (!empty($group['age_range'])): ?>
                    <p class="mb-2"><i class="fas fa-birthday-cake me-2 text-muted"></i><?= lang('Common.training_group_age_range') ?>: <strong><?= esc($group['age_range']) ?></strong></p>
                    <?php endif; ?>
                    <?php if (!empty($group['schedule_info'])): ?>
                    <p class="mb-2"><i class="fas fa-calendar-alt me-2 text-muted"></i><?= lang('Common.training_group_schedule') ?>: <strong><?= esc($group['schedule_info']) ?></strong></p>
                    <?php endif; ?>
                    <p class="mb-2"><i class="fas fa-users me-2 text-muted"></i><?= lang('Common.players') ?>: <strong><?= count($players) ?></strong> / <?= esc($group['max_players']) ?></p>
                    <p class="mb-0"><i class="fas fa-user-tie me-2 text-muted"></i><?= lang('Common.training_group_coaches') ?>: <strong><?= count($coaches) ?></strong></p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="col-md-8">
            <div class="row">
                <div class="col-6 col-lg-3 mb-3">
                    <div class="card shadow-sm rounded-4 text-center border-0" style="background: #e8f5e9;">
                        <div class="card-body py-3">
                            <h3 class="fw-bold text-success mb-1"><?= $stats['session_count'] ?></h3>
                            <small class="text-muted"><?= lang('Common.report_total_sessions') ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-3">
                    <div class="card shadow-sm rounded-4 text-center border-0" style="background: #e3f2fd;">
                        <div class="card-body py-3">
                            <h3 class="fw-bold text-primary mb-1"><?= $stats['total_records'] ?></h3>
                            <small class="text-muted"><?= lang('Common.attendance_total') ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-3">
                    <div class="card shadow-sm rounded-4 text-center border-0" style="background: #fff3e0;">
                        <div class="card-body py-3">
                            <h3 class="fw-bold text-warning mb-1"><?= $stats['present_records'] ?></h3>
                            <small class="text-muted"><?= lang('Common.attendance_present') ?></small>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3 mb-3">
                    <div class="card shadow-sm rounded-4 text-center border-0" style="background: <?= $stats['attendance_rate'] >= 80 ? '#e8f5e9' : ($stats['attendance_rate'] >= 50 ? '#fff3e0' : '#ffebee') ?>;">
                        <div class="card-body py-3">
                            <h3 class="fw-bold mb-1" style="color: <?= $stats['attendance_rate'] >= 80 ? '#2e7d32' : ($stats['attendance_rate'] >= 50 ? '#e65100' : '#c62828') ?>;"><?= $stats['attendance_rate'] ?>%</h3>
                            <small class="text-muted"><?= lang('Common.attendance_rate') ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Coaches -->
    <?php if (!empty($coaches)): ?>
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white rounded-top-4">
            <h6 class="mb-0 fw-bold"><i class="fas fa-user-tie me-2"></i><?= lang('Common.training_group_coaches') ?></h6>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($coaches as $coach): ?>
                <div class="col-md-4 col-lg-3 mb-3">
                    <div class="d-flex align-items-center p-2 border rounded-3">
                        <?php if (!empty($coach['image_path'])): ?>
                            <img src="<?= base_url($coach['image_path']) ?>" class="rounded-circle me-2" width="40" height="40" style="object-fit:cover;">
                        <?php else: ?>
                            <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center me-2" style="width:40px;height:40px;font-size:16px;">
                                <?= mb_substr($coach['first_name'] ?? '?', 0, 1) ?>
                            </div>
                        <?php endif; ?>
                        <div>
                            <div class="fw-bold small"><?= esc($coach['first_name'] . ' ' . $coach['last_name']) ?></div>
                            <span class="badge bg-<?= $coach['role'] === 'head' ? 'warning' : 'info' ?> text-dark" style="font-size:0.7em;">
                                <?= $coach['role'] === 'head' ? lang('Common.training_head_coach') : lang('Common.training_assistant_coach') ?>
                            </span>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Sessions List -->
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white rounded-top-4">
            <h6 class="mb-0 fw-bold"><i class="fas fa-clipboard-list me-2"></i><?= lang('Common.training_sessions') ?></h6>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($sessions)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th><?= lang('Common.training_date') ?></th>
                            <th><?= lang('Common.training_time') ?></th>
                            <th><?= lang('Common.training_title') ?></th>
                            <th><?= lang('Common.training_type') ?></th>
                            <th><?= lang('Common.training_status') ?></th>
                            <th><?= lang('Common.training_notes') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($sessions as $i => $session): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($session['training_date']) ?></td>
                            <td>
                                <?php if (!empty($session['start_time'])): ?>
                                    <?= substr($session['start_time'], 0, 5) ?>
                                    <?php if (!empty($session['end_time'])): ?>
                                        - <?= substr($session['end_time'], 0, 5) ?>
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= esc($session['title'] ?? '-') ?>
                                <?php $ac = $attachmentCounts[$session['id']] ?? 0; ?>
                                <?php if ($ac > 0): ?>
                                    <span class="badge bg-info rounded-pill ms-1" title="<?= $ac ?> ไฟล์"><i class="fas fa-paperclip"></i> <?= $ac ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $typeColors = ['regular' => 'primary', 'special' => 'warning', 'match' => 'success', 'fitness' => 'info'];
                                    $typeColor = $typeColors[$session['training_type']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $typeColor ?>"><?= lang('Common.training_type_' . $session['training_type']) ?></span>
                            </td>
                            <td>
                                <?php
                                    $statusColors = ['planned' => 'secondary', 'completed' => 'success', 'cancelled' => 'danger'];
                                    $statusColor = $statusColors[$session['status']] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusColor ?>"><?= lang('Common.training_status_' . $session['status']) ?></span>
                            </td>
                            <td><small class="text-muted"><?= esc(mb_substr($session['notes'] ?? '', 0, 50)) ?><?= mb_strlen($session['notes'] ?? '') > 50 ? '...' : '' ?></small></td>
                            <td>
                                <a href="<?= base_url('academy/training-session/detail/' . $academy['id'] . '/' . $session['id']) ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center py-4 text-muted">
                <i class="fas fa-calendar-times fa-2x mb-2"></i>
                <p><?= lang('Common.report_no_data') ?></p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Players in Group -->
    <?php if (!empty($players)): ?>
    <div class="card shadow-sm rounded-4 mb-4">
        <div class="card-header bg-white rounded-top-4">
            <h6 class="mb-0 fw-bold"><i class="fas fa-running me-2"></i><?= lang('Common.training_group_players') ?> (<?= count($players) ?>)</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th></th>
                            <th><?= lang('Common.first_name') ?></th>
                            <th><?= lang('Common.team') ?></th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $i => $player): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td>
                                <?php if (!empty($player['shirt_number'])): ?>
                                    <span class="badge bg-dark">#<?= esc($player['shirt_number']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= base_url('academy/report-player/' . $academy['id'] . '/' . $player['player_id']) ?>" class="text-decoration-none">
                                    <?= esc($player['first_name'] . ' ' . $player['last_name']) ?>
                                </a>
                            </td>
                            <td><small class="text-muted"><?= esc($player['team_name'] ?? '-') ?></small></td>
                            <td>
                                <a href="<?= base_url('academy/report-player/' . $academy['id'] . '/' . $player['player_id']) ?>" class="btn btn-sm btn-outline-info">
                                    <i class="fas fa-chart-line"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<?php $this->endSection(); ?>
