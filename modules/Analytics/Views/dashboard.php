<?php
$baseUrl = rtrim(base_url(), "/") . "/";
?>
<!-- Stat Cards -->
<div class="row mb-4">
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">แมตช์ทั้งหมด</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_matches ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-futbol fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">วิเคราะห์เสร็จ</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $completed ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-check-circle fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">กำลังประมวลผล</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $processing ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-spinner fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">ผู้เล่นที่ track แล้ว</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800"><?= $total_players ?></div>
                    </div>
                    <div class="col-auto"><i class="fas fa-running fa-2x text-gray-300"></i></div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Chart แมตช์ต่อเดือน -->
    <div class="col-xl-8 col-lg-7 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">แมตช์ต่อเดือน (12 เดือนล่าสุด)</h6>
                <a href="<?= $baseUrl ?>admin/analytics/matches/manage" class="btn btn-sm btn-primary">
                    <i class="fas fa-plus"></i> เพิ่มแมตช์
                </a>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" height="100"></canvas>
            </div>
        </div>
    </div>

    <!-- Top 5 ผู้เล่น -->
    <div class="col-xl-4 col-lg-5 mb-4">
        <div class="card shadow">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">Top 5 ผู้เล่น (ระยะทาง)</h6>
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php if (empty($top_distance)): ?>
                        <div class="list-group-item text-center text-muted">ยังไม่มีข้อมูล</div>
                    <?php else: ?>
                        <?php foreach ($top_distance as $i => $player): ?>
                        <div class="list-group-item d-flex align-items-center">
                            <span class="badge badge-primary mr-2"><?= $i + 1 ?></span>
                            <?php if ($player['image_url']): ?>
                                <img src="<?= base_url($player['image_url']) ?>" class="rounded-circle mr-2" width="36" height="36">
                            <?php else: ?>
                                <div class="rounded-circle bg-secondary mr-2 d-flex align-items-center justify-content-center" style="width:36px;height:36px">
                                    <i class="fas fa-user text-white fa-xs"></i>
                                </div>
                            <?php endif; ?>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold small"><?= esc($player['player_name']) ?> <?= esc($player['player_surname']) ?></div>
                                <div class="text-muted" style="font-size:11px"><?= number_format($player['total_dist'], 2) ?> กม.</div>
                            </div>
                            <span class="badge badge-light"><?= number_format($player['max_speed'], 1) ?> กม./ชม.</span>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- แมตช์ล่าสุด -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">แมตช์ล่าสุด</h6>
        <a href="<?= $baseUrl ?>admin/analytics/matches" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>แมตช์</th>
                        <th>วันที่</th>
                        <th>ผลการแข่ง</th>
                        <th>สถานะ</th>
                        <th>จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recent_matches)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">ยังไม่มีข้อมูลแมตช์</td></tr>
                    <?php else: ?>
                        <?php foreach ($recent_matches as $i => $m): ?>
                        <tr>
                            <td><?= $i + 1 ?></td>
                            <td><?= esc($m['match_name']) ?></td>
                            <td><?= date('d/m/Y', strtotime($m['match_date'])) ?></td>
                            <td>
                                <?= esc($m['home_team'] ?? $m['home_team_name']) ?>
                                <strong class="mx-1"><?= $m['home_score'] ?>-<?= $m['away_score'] ?></strong>
                                <?= esc($m['away_team'] ?? $m['away_team_name']) ?>
                            </td>
                            <td>
                                <?php
                                $statusMap = [
                                    'pending'    => ['warning','รอดำเนินการ'],
                                    'processing' => ['info','กำลังประมวลผล'],
                                    'completed'  => ['success','เสร็จสิ้น'],
                                    'error'      => ['danger','ผิดพลาด'],
                                ];
                                $s = isset($statusMap[$m['status']]) ? $statusMap[$m['status']] : ['secondary',$m['status']];
                                ?>
                                <span class="badge badge-<?= $s[0] ?>"><?= $s[1] ?></span>
                            </td>
                            <td>
                                <a href="<?= $baseUrl ?>admin/analytics/matches/detail/<?= $m['id'] ?>" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.4/dist/Chart.min.js"></script>
<script>
(function () {
    var monthlyData = <?= json_encode($monthly_matches ?? []) ?>;
    var labels  = monthlyData.map(function(d){ return d.month; });
    var counts  = monthlyData.map(function(d){ return parseInt(d.total); });

    var ctx = document.getElementById('monthlyChart').getContext('2d');
    new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'จำนวนแมตช์',
                data: counts,
                backgroundColor: 'rgba(78, 115, 223, 0.5)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1,
            }]
        },
        options: {
            responsive: true,
            scales: { yAxes: [{ ticks: { beginAtZero: true, stepSize: 1 } }] }
        }
    });
})();
</script>
