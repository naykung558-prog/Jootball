<?php $this->extend('template/admin_layout') ?>

<?php $this->section('style'); ?>
<style>
    .stat-card { border-radius: 12px; border: none; }
    .stat-card .card-body { padding: 1.25rem; }
    .stat-number { font-size: 2rem; font-weight: 700; }
    .badge-loaded { background-color: #28a745; }
    .badge-warning { background-color: #ffc107; color: #333; }
    .badge-blocked { background-color: #dc3545; }
    .progress { height: 24px; border-radius: 12px; }
    .log-table td { font-size: 0.85rem; vertical-align: middle; }
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-bold mb-1"><i class="ti ti-map-pin me-2"></i>Google Maps API Usage Log</h4>
            <p class="text-muted mb-0">Daily Limit: <strong><?= number_format($limit) ?></strong> loads/day</p>
        </div>
        <div>
            <select class="form-select" id="dateSelector" style="width: 200px;">
                <?php foreach ($availableDates as $d): ?>
                    <option value="<?= $d ?>" <?= $d === $date ? 'selected' : '' ?>><?= $d ?><?= $d === date('Y-m-d') ? ' (Today)' : '' ?></option>
                <?php endforeach; ?>
                <?php if (empty($availableDates)): ?>
                    <option value="<?= $date ?>" selected><?= $date ?> (No data)</option>
                <?php endif; ?>
            </select>
        </div>
    </div>

    <!-- Progress Bar -->
    <?php
        $percent = $limit > 0 ? min(100, round(($todayCount / $limit) * 100)) : 0;
        $barColor = $percent >= 100 ? 'bg-danger' : ($percent >= 80 ? 'bg-warning' : 'bg-success');
    ?>
    <div class="card stat-card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between mb-2">
                <span class="fw-bold">Usage: <?= number_format($todayCount) ?> / <?= number_format($limit) ?></span>
                <span class="fw-bold"><?= $percent ?>%</span>
            </div>
            <div class="progress">
                <div class="progress-bar <?= $barColor ?>" style="width: <?= $percent ?>%"></div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card stat-card shadow-sm border-start border-4 border-primary">
                <div class="card-body text-center">
                    <div class="text-muted small">Total Requests</div>
                    <div class="stat-number text-primary"><?= number_format($stats['total']) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm border-start border-4 border-success">
                <div class="card-body text-center">
                    <div class="text-muted small">Loaded</div>
                    <div class="stat-number text-success"><?= number_format($stats['loaded']) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm border-start border-4 border-warning">
                <div class="card-body text-center">
                    <div class="text-muted small">Warning (80%+)</div>
                    <div class="stat-number text-warning"><?= number_format($stats['warning']) ?></div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card stat-card shadow-sm border-start border-4 border-danger">
                <div class="card-body text-center">
                    <div class="text-muted small">Blocked</div>
                    <div class="stat-number text-danger"><?= number_format($stats['blocked']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mb-4">
        <!-- Top Pages -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold"><i class="ti ti-file me-2"></i>Top Pages</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>Page</th><th class="text-end" style="width:80px">Count</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($pageStats)): ?>
                                <tr><td colspan="2" class="text-center text-muted py-3">No data</td></tr>
                            <?php else: ?>
                                <?php foreach (array_slice($pageStats, 0, 10) as $page => $cnt): ?>
                                    <tr>
                                        <td class="text-truncate" style="max-width: 300px;" title="<?= esc($page) ?>"><?= esc($page) ?></td>
                                        <td class="text-end fw-bold"><?= $cnt ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Top IPs -->
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold"><i class="ti ti-world me-2"></i>Top IPs</div>
                <div class="card-body p-0">
                    <table class="table table-sm mb-0">
                        <thead class="table-light">
                            <tr><th>IP Address</th><th class="text-end" style="width:80px">Count</th></tr>
                        </thead>
                        <tbody>
                            <?php if (empty($ipStats)): ?>
                                <tr><td colspan="2" class="text-center text-muted py-3">No data</td></tr>
                            <?php else: ?>
                                <?php foreach (array_slice($ipStats, 0, 10) as $ip => $cnt): ?>
                                    <tr>
                                        <td><?= esc($ip) ?></td>
                                        <td class="text-end fw-bold"><?= $cnt ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Log Detail Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white d-flex justify-content-between align-items-center">
            <span class="fw-bold"><i class="ti ti-list me-2"></i>Request Log (<?= $date ?>)</span>
            <span class="text-muted small"><?= count($logs) ?> records</span>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-sm log-table mb-0">
                    <thead class="table-light">
                        <tr>
                            <th style="width:160px">Time</th>
                            <th style="width:90px">Status</th>
                            <th style="width:80px">Count</th>
                            <th>User</th>
                            <th>IP</th>
                            <th>Page</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($logs)): ?>
                            <tr><td colspan="6" class="text-center text-muted py-4">No log data for this date</td></tr>
                        <?php else: ?>
                            <?php foreach ($logs as $l): ?>
                                <tr>
                                    <td class="text-nowrap"><?= esc($l['time']) ?></td>
                                    <td>
                                        <?php if ($l['status'] === 'LOADED'): ?>
                                            <span class="badge badge-loaded">LOADED</span>
                                        <?php elseif ($l['status'] === 'WARNING'): ?>
                                            <span class="badge badge-warning">WARNING</span>
                                        <?php else: ?>
                                            <span class="badge badge-blocked">BLOCKED</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?= esc($l['count']) ?>/<?= esc($l['limit']) ?></td>
                                    <td><?= esc($l['user_id']) ?> <small class="text-muted">(<?= esc($l['user_type']) ?>)</small></td>
                                    <td><?= esc($l['ip']) ?></td>
                                    <td class="text-truncate" style="max-width: 250px;" title="<?= esc($l['page']) ?>"><?= esc($l['page']) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    $('#dateSelector').on('change', function() {
        window.location.href = '<?= base_url("admin/maps-quota/log") ?>/' + $(this).val();
    });
</script>
<?php $this->endSection(); ?>
