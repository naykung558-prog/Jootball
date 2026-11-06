<?php
$baseUrl = rtrim(base_url(), "/") . "/";
$matchId  = $match['id'];
$homeName = esc($match['home_team_name_real'] ?? $match['home_team_name']);
$awayName = esc($match['away_team_name_real'] ?? $match['away_team_name']);

// รวมสถิติทั้งแมตช์
$totalDist = 0;
$sumSpeed  = 0;
$maxSpeed  = 0;
$totalSprint = 0;
foreach ($playerStats as $ps) {
    $totalDist   += $ps['total_distance'];
    $sumSpeed    += $ps['avg_speed'];
    if ($ps['max_speed'] > $maxSpeed) {
        $maxSpeed = $ps['max_speed'];
    }
    $totalSprint += $ps['sprint_count'];
}
$avgSpeed = count($playerStats) > 0 ? $sumSpeed / count($playerStats) : 0;
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <a href="<?= $baseUrl ?>admin/analytics/matches/detail/<?= $matchId ?>" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left mr-1"></i>กลับรายละเอียดแมตช์
        </a>
        <h4 class="text-gray-800 mb-1">
            <i class="fas fa-file-alt mr-2"></i><?= esc($match['match_name']) ?>
        </h4>
        <p class="text-muted small mb-0">
            <i class="fas fa-calendar mr-1"></i><?= date('d/m/Y', strtotime($match['match_date'])) ?>
            <?php if (!empty($match['venue'])): ?>
                &nbsp;|&nbsp;<i class="fas fa-map-marker-alt mr-1"></i><?= esc($match['venue']) ?>
            <?php endif; ?>
        </p>
    </div>
    <div>
        <button onclick="window.print()" class="btn btn-secondary mr-2">
            <i class="fas fa-print mr-1"></i>Print
        </button>
        <a href="<?= $baseUrl ?>admin/analytics/report/export-pdf/<?= $matchId ?>"
           class="btn btn-danger" target="_blank">
            <i class="fas fa-file-pdf mr-1"></i>Export PDF
        </a>
    </div>
</div>

<!-- สกอร์ -->
<div class="card shadow mb-4">
    <div class="card-body text-center py-4">
        <div class="row align-items-center">
            <div class="col-5 text-right">
                <h5 class="font-weight-bold mb-0"><?= $homeName ?></h5>
                <span class="text-muted small">เหย้า</span>
            </div>
            <div class="col-2">
                <h2 class="font-weight-bold text-primary mb-0">
                    <?= (int)$match['home_score'] ?> - <?= (int)$match['away_score'] ?>
                </h2>
            </div>
            <div class="col-5 text-left">
                <h5 class="font-weight-bold mb-0"><?= $awayName ?></h5>
                <span class="text-muted small">เยือน</span>
            </div>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">ระยะทางรวม</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($totalDist, 2) ?> <small class="text-muted font-weight-normal">กม.</small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-route fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">ความเร็วเฉลี่ย</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($avgSpeed, 1) ?> <small class="text-muted font-weight-normal">กม./ชม.</small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-tachometer-alt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ความเร็วสูงสุด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($maxSpeed, 1) ?> <small class="text-muted font-weight-normal">กม./ชม.</small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-bolt fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Sprint รวม</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($totalSprint) ?> <small class="text-muted font-weight-normal">ครั้ง</small>
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-running fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Chart ระยะทางผู้เล่น -->
<?php if (!empty($playerStats)): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">ระยะทางแต่ละผู้เล่น (กม.)</h6>
    </div>
    <div class="card-body">
        <canvas id="distanceChart" style="max-height:320px"></canvas>
    </div>
</div>
<?php endif; ?>

<!-- ตารางสถิติผู้เล่น -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">สถิติรายผู้เล่น</h6>
        <span class="text-muted small"><?= count($playerStats) ?> คน</span>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="40">#</th>
                        <th>ชื่อผู้เล่น</th>
                        <th width="60" class="text-center">เบอร์</th>
                        <th width="130" class="text-center">ระยะทาง (กม.)</th>
                        <th width="130" class="text-center">เฉลี่ย (กม./ชม.)</th>
                        <th width="130" class="text-center">สูงสุด (กม./ชม.)</th>
                        <th width="80" class="text-center">Sprint</th>
                        <th width="80" class="text-center">Zone1%</th>
                        <th width="80" class="text-center">Zone2%</th>
                        <th width="80" class="text-center">Zone3%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playerStats as $i => $ps): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?php if (!empty($ps['player_name'])): ?>
                                <a href="<?= $baseUrl ?>admin/analytics/report/player/<?= $ps['player_id'] ?>">
                                    <?= esc($ps['player_name']) ?> <?= esc($ps['player_surname']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">เบอร์ <?= (int)$ps['jersey_num'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= (int)$ps['jersey_num'] ?></td>
                        <td class="text-center"><?= number_format($ps['total_distance'], 2) ?></td>
                        <td class="text-center"><?= number_format($ps['avg_speed'], 1) ?></td>
                        <td class="text-center"><?= number_format($ps['max_speed'], 1) ?></td>
                        <td class="text-center"><?= (int)$ps['sprint_count'] ?></td>
                        <td class="text-center">
                            <?= isset($ps['zone1_pct']) ? number_format($ps['zone1_pct'], 1) . '%' : '-' ?>
                        </td>
                        <td class="text-center">
                            <?= isset($ps['zone2_pct']) ? number_format($ps['zone2_pct'], 1) . '%' : '-' ?>
                        </td>
                        <td class="text-center">
                            <?= isset($ps['zone3_pct']) ? number_format($ps['zone3_pct'], 1) . '%' : '-' ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Speed Zone Breakdown -->
<?php if (!empty($playerStats)): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-success">Speed Zone Breakdown (%)</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-sm mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>ผู้เล่น</th>
                        <th class="text-center" style="background:#e8f5e9">เดิน (&lt;7)</th>
                        <th class="text-center" style="background:#fff9c4">วิ่งช้า (7-14)</th>
                        <th class="text-center" style="background:#ffe0b2">วิ่ง (14-21)</th>
                        <th class="text-center" style="background:#ffccbc">วิ่งเร็ว (21-25)</th>
                        <th class="text-center" style="background:#ffcdd2">Sprint (&gt;25)</th>
                        <th class="text-center">Sprint ระยะทาง</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playerStats as $ps): ?>
                    <tr>
                        <td>
                            <?php if (!empty($ps['player_name'])): ?>
                                <?= esc(trim($ps['player_name'] . ' ' . $ps['player_surname'])) ?>
                            <?php else: ?>
                                <span class="text-muted">เบอร์ <?= (int)$ps['jersey_num'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center"><?= number_format($ps['zone_walk'] ?? 0, 1) ?>%</td>
                        <td class="text-center"><?= number_format($ps['zone_jog'] ?? 0, 1) ?>%</td>
                        <td class="text-center"><?= number_format($ps['zone_run'] ?? 0, 1) ?>%</td>
                        <td class="text-center"><?= number_format($ps['zone_fast'] ?? 0, 1) ?>%</td>
                        <td class="text-center font-weight-bold text-danger"><?= number_format($ps['zone_sprint'] ?? 0, 1) ?>%</td>
                        <td class="text-center"><?= number_format($ps['sprint_distance'] ?? 0, 2) ?> กม.</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
<?php if (!empty($playerStats)): ?>
(function () {
    var labels  = [];
    var values  = [];
    var colors  = [];
    var palette = [
        'rgba(78,115,223,0.8)','rgba(28,200,138,0.8)','rgba(246,194,62,0.8)',
        'rgba(231,74,59,0.8)','rgba(54,185,204,0.8)','rgba(133,135,150,0.8)',
        'rgba(105,0,132,0.8)','rgba(0,128,0,0.8)','rgba(255,128,0,0.8)','rgba(0,0,128,0.8)',
    ];

    <?php foreach ($playerStats as $i => $ps): ?>
    <?php
    $label = !empty($ps['player_name'])
        ? trim($ps['player_name'] . ' ' . $ps['player_surname'])
        : 'เบอร์ ' . $ps['jersey_num'];
    ?>
    labels.push(<?= json_encode($label) ?>);
    values.push(<?= number_format($ps['total_distance'], 3, '.', '') ?>);
    colors.push(palette[<?= $i ?> % palette.length]);
    <?php endforeach; ?>

    var ctx = document.getElementById('distanceChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'ระยะทาง (กม.)',
                data: values,
                backgroundColor: colors,
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            legend: { display: false },
            scales: {
                yAxes: [{
                    ticks: { beginAtZero: true },
                    scaleLabel: { display: true, labelString: 'กม.' }
                }]
            },
            tooltips: {
                callbacks: {
                    label: function (item) {
                        return item.yLabel.toFixed(2) + ' กม.';
                    }
                }
            }
        }
    });
}());
<?php endif; ?>
</script>
