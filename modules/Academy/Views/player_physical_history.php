<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="row mb-3">
        <div class="col-12">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= base_url('academy/players/' . $academy_id) ?>">นักเตะ</a></li>
                    <li class="breadcrumb-item"><a href="<?= base_url('academy/player-view/' . $academy_id . '/' . $player['id']) ?>"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></a></li>
                    <li class="breadcrumb-item active">ประวัติกายภาพ</li>
                </ol>
            </nav>
        </div>
    </div>

    <!-- Player Header -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-body py-3 px-4 d-flex align-items-center gap-3">
            <img src="<?= !empty($player['image_path']) ? base_url($player['image_path']) : base_url('public/img/img_profile.jpg') ?>"
                 class="rounded-circle border border-2 shadow-sm" style="width:56px;height:56px;object-fit:cover;">
            <div>
                <h5 class="fw-bold mb-0 text-primary"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></h5>
                <small class="text-muted"><i class="fas fa-heartbeat me-1"></i>ประวัติข้อมูลกายภาพ</small>
            </div>
            <div class="ms-auto d-flex gap-2">
                <a href="<?= base_url('academy/player-edit/' . $academy_id . '/' . $player['id']) ?>" class="btn btn-light rounded-pill px-3">
                    <i class="fas fa-edit me-1"></i> แก้ไขข้อมูล
                </a>
                <a href="<?= base_url('academy/player-view/' . $academy_id . '/' . $player['id']) ?>" class="btn btn-light rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i> กลับ
                </a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Chart -->
        <?php if (count($history) >= 2): ?>
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold mb-0"><i class="fas fa-chart-line me-2 text-primary"></i>กราฟพัฒนาการ</h6>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="physicalChart" style="height:280px;"></div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- History Table -->
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-success"></i>ประวัติการวัด</h6>
                    <span class="badge bg-success rounded-pill"><?= count($history) ?> รายการ</span>
                </div>
                <div class="card-body p-0">
                    <?php if (empty($history)): ?>
                    <div class="text-center py-5 text-muted">
                        <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                        <p>ยังไม่มีประวัติข้อมูลกายภาพ</p>
                    </div>
                    <?php else: ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th class="px-4 py-3">#</th>
                                    <th class="py-3">วันที่วัด</th>
                                    <th class="py-3 text-center">ส่วนสูง (ซม.)</th>
                                    <th class="py-3 text-center">น้ำหนัก (กก.)</th>
                                    <th class="py-3 text-center">BMI</th>
                                    <th class="py-3 text-center">เท้าถนัด</th>
                                    <th class="py-3">บันทึกโดย</th>
                                    <th class="py-3">Academy</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($history as $i => $h): ?>
                                <?php
                                    $prev = $history[$i + 1] ?? null;
                                    $heightDiff = ($prev && $h['height'] && $prev['height']) ? round($h['height'] - $prev['height'], 1) : null;
                                    $weightDiff = ($prev && $h['weight'] && $prev['weight']) ? round($h['weight'] - $prev['weight'], 1) : null;
                                ?>
                                <tr>
                                    <td class="px-4 text-muted"><?= $i + 1 ?></td>
                                    <td>
                                        <span class="fw-semibold">
                                            <?php
                                                $d = strtotime($h['recorded_at']);
                                                echo date('d/m/', $d) . (date('Y', $d) + 543) . date(' H:i', $d);
                                            ?>
                                        </span>
                                        <?php if ($i === 0): ?>
                                        <span class="badge bg-primary rounded-pill ms-1" style="font-size:0.6rem;">ล่าสุด</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($h['height']): ?>
                                        <span class="fw-bold"><?= number_format($h['height'], 1) ?></span>
                                        <?php if ($heightDiff !== null): ?>
                                        <small class="ms-1 <?= $heightDiff > 0 ? 'text-success' : ($heightDiff < 0 ? 'text-danger' : 'text-muted') ?>">
                                            <?= $heightDiff > 0 ? '+' : '' ?><?= $heightDiff ?>
                                        </small>
                                        <?php endif; ?>
                                        <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($h['weight']): ?>
                                        <span class="fw-bold"><?= number_format($h['weight'], 1) ?></span>
                                        <?php if ($weightDiff !== null): ?>
                                        <small class="ms-1 <?= $weightDiff > 0 ? 'text-warning' : ($weightDiff < 0 ? 'text-success' : 'text-muted') ?>">
                                            <?= $weightDiff > 0 ? '+' : '' ?><?= $weightDiff ?>
                                        </small>
                                        <?php endif; ?>
                                        <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($h['bmi']): ?>
                                        <?php $bmi = (float)$h['bmi']; ?>
                                        <span class="badge rounded-pill <?= $bmi < 18.5 ? 'bg-info' : ($bmi < 25 ? 'bg-success' : ($bmi < 30 ? 'bg-warning' : 'bg-danger')) ?>">
                                            <?= number_format($bmi, 1) ?>
                                        </span>
                                        <?php else: ?><span class="text-muted">-</span><?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php $foot = ['left' => 'ซ้าย', 'right' => 'ขวา', 'both' => 'สองเท้า'];
                                        echo esc($foot[$h['dominant_foot'] ?? ''] ?? ($h['dominant_foot'] ?: '-')); ?>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= esc($h['recorded_by_name'] ?? '-') ?></small>
                                    </td>
                                    <td>
                                        <small class="text-muted"><?= esc($h['academy_name'] ?? '-') ?></small>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?php if (count($history) >= 2): ?>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script>
(function() {
    const raw = <?= json_encode(array_reverse($history)) ?>;
    const categories = raw.map(h => {
        const d = new Date(h.recorded_at);
        const day = String(d.getDate()).padStart(2,'0');
        const mon = String(d.getMonth()+1).padStart(2,'0');
        const yr  = d.getFullYear() + 543;
        return day+'/'+mon+'/'+yr;
    });

    Highcharts.chart('physicalChart', {
        chart: { type: 'line', style: { fontFamily: 'inherit' } },
        title: { text: null },
        xAxis: { categories, crosshair: true },
        yAxis: [
            { title: { text: 'ส่วนสูง (ซม.) / น้ำหนัก (กก.)' }, min: 0 },
            { title: { text: 'BMI' }, opposite: true, min: 0 }
        ],
        tooltip: { shared: true },
        legend: { enabled: true },
        credits: { enabled: false },
        series: [
            {
                name: 'ส่วนสูง (ซม.)',
                data: raw.map(h => h.height ? parseFloat(h.height) : null),
                color: '#16478D',
                marker: { enabled: true }
            },
            {
                name: 'น้ำหนัก (กก.)',
                data: raw.map(h => h.weight ? parseFloat(h.weight) : null),
                color: '#28a745',
                marker: { enabled: true }
            },
            {
                name: 'BMI',
                data: raw.map(h => h.bmi ? parseFloat(h.bmi) : null),
                color: '#fd7e14',
                yAxis: 1,
                dashStyle: 'ShortDash',
                marker: { enabled: true }
            }
        ]
    });
})();
</script>
<?php endif; ?>
<?php $this->endSection(); ?>
