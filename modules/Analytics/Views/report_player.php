<?php
$baseUrl = rtrim(base_url(), "/") . "/";
$playerName = esc(trim($player['player_name'] . ' ' . $player['player_surname']));
$matchCount = count($allStats);
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div class="d-flex align-items-center">
        <a href="<?= $baseUrl ?>admin/analytics/players" class="btn btn-outline-secondary btn-sm mr-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <button onclick="window.print()" class="btn btn-secondary btn-sm mr-3">
            <i class="fas fa-print mr-1"></i>Print
        </button>
        <?php if (!empty($player['image_url'])): ?>
            <img src="<?= esc($player['image_url']) ?>" alt="<?= $playerName ?>"
                 class="rounded-circle mr-3" style="width:64px;height:64px;object-fit:cover;">
        <?php else: ?>
            <div class="rounded-circle bg-primary d-flex align-items-center justify-content-center mr-3"
                 style="width:64px;height:64px;min-width:64px;">
                <i class="fas fa-user fa-2x text-white"></i>
            </div>
        <?php endif; ?>
        <div>
            <h4 class="text-gray-800 mb-1"><?= $playerName ?></h4>
            <p class="text-muted small mb-0">
                <?php if (!empty($player['position'])): ?>
                    <span class="badge badge-light"><?= esc($player['position']) ?></span>
                <?php endif; ?>
                <?php if (!empty($player['team_name'])): ?>
                    &nbsp;<i class="fas fa-shield-alt mr-1"></i><?= esc($player['team_name']) ?>
                <?php endif; ?>
            </p>
        </div>
    </div>
</div>

<!-- Stat Cards -->
<div class="row mb-4">
    <div class="col-xl col-md-4 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">จำนวนแมตช์</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $matchCount ?> <small class="text-muted font-weight-normal">แมตช์</small>
                        </div>
                    </div>
                    <div class="col-auto"><i class="fas fa-futbol fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">ระยะทางรวม</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($totalStat['total_distance'] ?? 0, 2) ?>
                            <small class="text-muted font-weight-normal">กม.</small>
                        </div>
                    </div>
                    <div class="col-auto"><i class="fas fa-route fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-4 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">เฉลี่ยต่อแมตช์</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= $matchCount > 0
                                ? number_format(($totalStat['total_distance'] ?? 0) / $matchCount, 2)
                                : '0.00' ?>
                            <small class="text-muted font-weight-normal">กม.</small>
                        </div>
                    </div>
                    <div class="col-auto"><i class="fas fa-chart-line fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">ความเร็วสูงสุด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($totalStat['max_speed'] ?? 0, 1) ?>
                            <small class="text-muted font-weight-normal">กม./ชม.</small>
                        </div>
                    </div>
                    <div class="col-auto"><i class="fas fa-bolt fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">Sprint รวม</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            <?= number_format($totalStat['total_sprints'] ?? 0) ?>
                            <small class="text-muted font-weight-normal">ครั้ง</small>
                        </div>
                    </div>
                    <div class="col-auto"><i class="fas fa-running fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Line Chart -->
<?php if (!empty($allStats)): ?>
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">ประสิทธิภาพรายแมตช์</h6>
    </div>
    <div class="card-body">
        <canvas id="playerChart" style="max-height:320px"></canvas>
    </div>
</div>
<?php endif; ?>

<!-- ตาราง Match History -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">ประวัติแมตช์</h6>
    </div>
    <div class="card-body p-0">
        <?php if (empty($allStats)): ?>
            <div class="p-4 text-center text-muted">ยังไม่มีข้อมูลแมตช์</div>
        <?php else: ?>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th width="110">วันที่</th>
                        <th>ชื่อแมตช์</th>
                        <th width="130" class="text-center">ระยะทาง (กม.)</th>
                        <th width="130" class="text-center">เฉลี่ย (กม./ชม.)</th>
                        <th width="130" class="text-center">สูงสุด (กม./ชม.)</th>
                        <th width="80" class="text-center">Sprint</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($allStats as $stat): ?>
                    <tr>
                        <td><?= date('d/m/Y', strtotime($stat['match_date'])) ?></td>
                        <td>
                            <a href="<?= $baseUrl ?>admin/analytics/report/match/<?= (int)$stat['match_id'] ?>">
                                <?= esc($stat['match_name']) ?>
                            </a>
                        </td>
                        <td class="text-center"><?= number_format($stat['total_distance'], 2) ?></td>
                        <td class="text-center"><?= number_format($stat['avg_speed'], 1) ?></td>
                        <td class="text-center"><?= number_format($stat['max_speed'], 1) ?></td>
                        <td class="text-center"><?= (int)$stat['sprint_count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
<?php if (!empty($allStats)): ?>
(function () {
    var labels   = [];
    var distData = [];
    var speedData = [];

    <?php foreach ($allStats as $stat): ?>
    labels.push(<?= json_encode(date('d/m/y', strtotime($stat['match_date']))) ?>);
    distData.push(<?= number_format($stat['total_distance'], 3, '.', '') ?>);
    speedData.push(<?= number_format($stat['avg_speed'], 2, '.', '') ?>);
    <?php endforeach; ?>

    var ctx = document.getElementById('playerChart').getContext('2d');
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'ระยะทาง (กม.)',
                    data: distData,
                    borderColor: 'rgba(78,115,223,1)',
                    backgroundColor: 'rgba(78,115,223,0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: true,
                    yAxisID: 'y-dist',
                },
                {
                    label: 'ความเร็วเฉลี่ย (กม./ชม.)',
                    data: speedData,
                    borderColor: 'rgba(28,200,138,1)',
                    backgroundColor: 'rgba(28,200,138,0.1)',
                    borderWidth: 2,
                    pointRadius: 4,
                    fill: false,
                    yAxisID: 'y-speed',
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            scales: {
                xAxes: [{
                    gridLines: { display: false }
                }],
                yAxes: [
                    {
                        id: 'y-dist',
                        position: 'left',
                        scaleLabel: { display: true, labelString: 'กม.' },
                        ticks: { beginAtZero: true }
                    },
                    {
                        id: 'y-speed',
                        position: 'right',
                        scaleLabel: { display: true, labelString: 'กม./ชม.' },
                        ticks: { beginAtZero: true },
                        gridLines: { drawOnChartArea: false }
                    }
                ]
            },
            tooltips: { mode: 'index', intersect: false }
        }
    });
}());
<?php endif; ?>
</script>
