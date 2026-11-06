<?php
$baseUrl = rtrim(base_url(), "/") . "/";
$matchId = $match['id'];

// Prepare data arrays for charts
$labels       = [];
$distances    = [];
$avgSpeeds    = [];
$maxSpeeds    = [];
$sprintCounts = [];
$zoneWalk     = [];
$zoneJog      = [];
$zoneRun      = [];
$zoneFast     = [];
$zoneSprint   = [];

foreach ($allStats as $s) {
    $name           = $s['player_name']
                      ? ($s['player_name'] . ' ' . $s['player_surname'])
                      : ('เบอร์ ' . $s['jersey_num']);
    $labels[]       = $name;
    $distances[]    = round((float)$s['total_distance'], 2);
    $avgSpeeds[]    = round((float)$s['avg_speed'], 1);
    $maxSpeeds[]    = round((float)$s['max_speed'], 1);
    $sprintCounts[] = (int)$s['sprint_count'];
    $zoneWalk[]     = round((float)$s['zone_walk'], 1);
    $zoneJog[]      = round((float)$s['zone_jog'], 1);
    $zoneRun[]      = round((float)$s['zone_run'], 1);
    $zoneFast[]     = round((float)$s['zone_fast'], 1);
    $zoneSprint[]   = round((float)$s['zone_sprint'], 1);
}

$maxSpeedPlayer = !empty($allStats) ? $allStats[0] : null;
$totalSprints   = !empty($allStats) ? array_sum(array_column($allStats, 'sprint_count')) : 0;
$avgSpeedAll    = !empty($allStats) ? array_sum(array_column($allStats, 'avg_speed')) / count($allStats) : 0;
$totalDist      = !empty($allStats) ? array_sum(array_column($allStats, 'total_distance')) : 0;
?>

<div class="d-flex align-items-center mb-3">
    <a href="<?= $baseUrl ?>admin/analytics/matches/detail/<?= $matchId ?>" class="btn btn-outline-secondary btn-sm me-3">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h5 class="text-gray-800 mb-0">
        <i class="fas fa-tachometer-alt me-2 text-info"></i>วิเคราะห์ความเร็ว &mdash; <?= esc($match['match_name']) ?>
    </h5>
</div>

<!-- Summary KPI cards -->
<?php if (!empty($allStats)): ?>
<div class="row mb-4">
    <div class="col-6 col-md-3 mb-3">
        <div class="card border-start border-info border-4 shadow-sm py-2">
            <div class="card-body py-2 px-3">
                <div class="text-xs fw-bold text-info text-uppercase mb-1">ความเร็วสูงสุด</div>
                <div class="h5 mb-0 fw-bold text-dark">
                    <?= number_format($maxSpeedPlayer['max_speed'], 1) ?> <small class="fs-6 fw-normal">กม./ชม.</small>
                </div>
                <small class="text-muted">
                    <?= $maxSpeedPlayer['player_name']
                        ? esc($maxSpeedPlayer['player_name'].' '.$maxSpeedPlayer['player_surname'])
                        : 'เบอร์ '.(int)$maxSpeedPlayer['jersey_num'] ?>
                </small>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card border-start border-success border-4 shadow-sm py-2">
            <div class="card-body py-2 px-3">
                <div class="text-xs fw-bold text-success text-uppercase mb-1">ความเร็วเฉลี่ยทีม</div>
                <div class="h5 mb-0 fw-bold text-dark">
                    <?= number_format($avgSpeedAll, 1) ?> <small class="fs-6 fw-normal">กม./ชม.</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card border-start border-warning border-4 shadow-sm py-2">
            <div class="card-body py-2 px-3">
                <div class="text-xs fw-bold text-warning text-uppercase mb-1">Sprint รวม</div>
                <div class="h5 mb-0 fw-bold text-dark">
                    <?= number_format($totalSprints) ?> <small class="fs-6 fw-normal">ครั้ง</small>
                </div>
            </div>
        </div>
    </div>
    <div class="col-6 col-md-3 mb-3">
        <div class="card border-start border-primary border-4 shadow-sm py-2">
            <div class="card-body py-2 px-3">
                <div class="text-xs fw-bold text-primary text-uppercase mb-1">ระยะทางรวมทีม</div>
                <div class="h5 mb-0 fw-bold text-dark">
                    <?= number_format($totalDist, 1) ?> <small class="fs-6 fw-normal">กม.</small>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Chart Row 1: Distance column + Speed column -->
<div class="row mb-4">
    <div class="col-lg-7 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-primary">
                    <i class="fas fa-road me-1"></i>ระยะทางรวมแต่ละผู้เล่น (กม.)
                </h6>
            </div>
            <div class="card-body">
                <div id="distanceChart" style="min-height:300px"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-5 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3">
                <h6 class="m-0 fw-bold text-info">
                    <i class="fas fa-bolt me-1"></i>ความเร็ว (กม./ชม.)
                </h6>
            </div>
            <div class="card-body">
                <div id="speedChart" style="min-height:300px"></div>
            </div>
        </div>
    </div>
</div>

<!-- Chart Row 2: Stacked speed zones -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 fw-bold text-warning">
            <i class="fas fa-layer-group me-1"></i>Speed Zone Distribution ต่อผู้เล่น (%)
        </h6>
        <div>
            <span class="badge bg-secondary me-1" style="font-size:11px">เดิน</span>
            <span class="badge bg-info me-1" style="font-size:11px">Jog</span>
            <span class="badge bg-success me-1" style="font-size:11px">Run</span>
            <span class="badge bg-warning text-dark me-1" style="font-size:11px">Fast</span>
            <span class="badge bg-danger" style="font-size:11px">Sprint</span>
        </div>
    </div>
    <div class="card-body">
        <div id="zoneStackedChart" style="min-height:340px"></div>
    </div>
</div>

<!-- Player Cards -->
<?php if (!empty($allStats)): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-dark"><i class="fas fa-id-card me-1"></i>Player Cards</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <?php foreach ($allStats as $s):
                $pname = $s['player_name']
                         ? esc($s['player_name'].' '.$s['player_surname'])
                         : 'เบอร์ '.(int)$s['jersey_num'];
                $teamClass = ((int)$s['team_side'] === 0) ? 'border-primary' : 'border-danger';
                $teamLabel = ((int)$s['team_side'] === 0) ? 'bg-primary' : 'bg-danger';
            ?>
            <div class="col-6 col-md-4 col-lg-3 mb-3">
                <div class="card border-2 <?= $teamClass ?> shadow-sm h-100">
                    <div class="card-body p-2 text-center">
                        <span class="badge <?= $teamLabel ?> mb-1" style="font-size:10px">
                            #<?= (int)$s['jersey_num'] ?>
                            <?= (int)$s['team_side'] === 0 ? 'เหย้า' : 'เยือน' ?>
                        </span>
                        <div class="fw-bold small mb-2 text-truncate" title="<?= $pname ?>"><?= $pname ?></div>
                        <div class="row text-center g-0">
                            <div class="col-4">
                                <div class="text-info fw-bold"><?= number_format((float)$s['max_speed'], 1) ?></div>
                                <div style="font-size:9px" class="text-muted">Max km/h</div>
                            </div>
                            <div class="col-4">
                                <div class="text-success fw-bold"><?= number_format((float)$s['avg_speed'], 1) ?></div>
                                <div style="font-size:9px" class="text-muted">Avg km/h</div>
                            </div>
                            <div class="col-4">
                                <div class="text-danger fw-bold"><?= (int)$s['sprint_count'] ?></div>
                                <div style="font-size:9px" class="text-muted">Sprint</div>
                            </div>
                        </div>
                        <div class="mt-2">
                            <div class="text-primary fw-bold" style="font-size:12px"><?= number_format((float)$s['total_distance'], 2) ?> กม.</div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Detail Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary"><i class="fas fa-table me-1"></i>ตารางสถิติรายละเอียด</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover table-sm mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th class="text-center">#</th>
                        <th>ผู้เล่น</th>
                        <th class="text-center">ทีม</th>
                        <th class="text-center">ระยะทาง (กม.)</th>
                        <th class="text-center">เฉลี่ย (กม./ชม.)</th>
                        <th class="text-center">สูงสุด (กม./ชม.)</th>
                        <th class="text-center">Sprint</th>
                        <th class="text-center">Walk%</th>
                        <th class="text-center">Jog%</th>
                        <th class="text-center">Run%</th>
                        <th class="text-center">Fast%</th>
                        <th class="text-center">Sprint%</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allStats as $i => $s): ?>
                    <tr>
                        <td class="text-center"><?= $i + 1 ?></td>
                        <td><?= $s['player_name'] ? esc($s['player_name'].' '.$s['player_surname']) : 'เบอร์ '.(int)$s['jersey_num'] ?></td>
                        <td class="text-center">
                            <span class="badge <?= (int)$s['team_side'] === 0 ? 'bg-primary' : 'bg-danger' ?>">
                                <?= (int)$s['team_side'] === 0 ? 'เหย้า' : 'เยือน' ?>
                            </span>
                        </td>
                        <td class="text-center"><?= number_format((float)$s['total_distance'], 2) ?></td>
                        <td class="text-center"><?= number_format((float)$s['avg_speed'], 1) ?></td>
                        <td class="text-center fw-bold"><?= number_format((float)$s['max_speed'], 1) ?></td>
                        <td class="text-center"><?= (int)$s['sprint_count'] ?></td>
                        <td class="text-center"><?= number_format((float)$s['zone_walk'], 1) ?>%</td>
                        <td class="text-center"><?= number_format((float)$s['zone_jog'], 1) ?>%</td>
                        <td class="text-center"><?= number_format((float)$s['zone_run'], 1) ?>%</td>
                        <td class="text-center"><?= number_format((float)$s['zone_fast'], 1) ?>%</td>
                        <td class="text-center"><?= number_format((float)$s['zone_sprint'], 1) ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
var HC_labels      = <?= json_encode($labels) ?>;
var HC_distances   = <?= json_encode($distances) ?>;
var HC_avgSpeeds   = <?= json_encode($avgSpeeds) ?>;
var HC_maxSpeeds   = <?= json_encode($maxSpeeds) ?>;
var HC_zoneWalk    = <?= json_encode($zoneWalk) ?>;
var HC_zoneJog     = <?= json_encode($zoneJog) ?>;
var HC_zoneRun     = <?= json_encode($zoneRun) ?>;
var HC_zoneFast    = <?= json_encode($zoneFast) ?>;
var HC_zoneSprint  = <?= json_encode($zoneSprint) ?>;

// 1. Distance Column Chart
Highcharts.chart('distanceChart', {
    chart: { type: 'column', style: { fontFamily: 'Kanit, sans-serif' } },
    title: { text: null },
    xAxis: { categories: HC_labels, labels: { rotation: -35, style: { fontSize: '11px' } } },
    yAxis: { min: 0, title: { text: 'กิโลเมตร' } },
    legend: { enabled: false },
    tooltip: { valueSuffix: ' กม.' },
    plotOptions: {
        column: {
            colorByPoint: true,
            dataLabels: { enabled: true, format: '{point.y:.2f}', style: { fontSize: '10px', fontWeight: 'normal' } }
        }
    },
    series: [{ name: 'ระยะทาง', data: HC_distances }],
    credits: { enabled: false }
});

// 2. Speed Column Chart (avg + max grouped)
Highcharts.chart('speedChart', {
    chart: { type: 'column', style: { fontFamily: 'Kanit, sans-serif' } },
    title: { text: null },
    xAxis: { categories: HC_labels, labels: { rotation: -35, style: { fontSize: '11px' } } },
    yAxis: { min: 0, title: { text: 'กม./ชม.' } },
    tooltip: { valueSuffix: ' กม./ชม.' },
    plotOptions: {
        column: { grouping: true, borderWidth: 0 }
    },
    series: [
        { name: 'เฉลี่ย', data: HC_avgSpeeds, color: '#36a2eb' },
        { name: 'สูงสุด',  data: HC_maxSpeeds, color: '#ff6384' }
    ],
    credits: { enabled: false }
});

// 3. Stacked Bar — Speed Zones per player
Highcharts.chart('zoneStackedChart', {
    chart: { type: 'bar', style: { fontFamily: 'Kanit, sans-serif' } },
    title: { text: null },
    xAxis: { categories: HC_labels, labels: { style: { fontSize: '11px' } } },
    yAxis: {
        min: 0,
        max: 100,
        title: { text: 'เปอร์เซ็นต์ (%)' },
        labels: { format: '{value}%' }
    },
    legend: {
        reversed: true,
        itemStyle: { fontFamily: 'Kanit, sans-serif', fontSize: '12px' }
    },
    tooltip: {
        formatter: function() {
            return '<b>' + this.x + '</b><br/>' + this.series.name + ': <b>' + this.y + '%</b>';
        }
    },
    plotOptions: {
        series: { stacking: 'normal', borderWidth: 0 }
    },
    series: [
        { name: 'Sprint', data: HC_zoneSprint, color: '#ff6384' },
        { name: 'Fast',   data: HC_zoneFast,   color: '#ff9f40' },
        { name: 'Run',    data: HC_zoneRun,    color: '#4bc0c0' },
        { name: 'Jog',    data: HC_zoneJog,    color: '#36a2eb' },
        { name: 'Walk',   data: HC_zoneWalk,   color: '#9e9e9e' }
    ],
    credits: { enabled: false }
});
</script>
