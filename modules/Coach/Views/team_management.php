<?php $this->extend('template/coach') ?>
<?= $this->section('title') ?>
จัดการสโมสร <?= $team['name'] ?> - GLO Football
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <div class="d-flex align-items-center">
            <?php if ($team['logo']): ?>
                <img src="<?= base_url($team['logo']) ?>" width="50" height="50" class="rounded-circle me-3">
            <?php endif; ?>
            <div>
                <h1 class="h3 mb-0 text-gray-800"><?= $team['name'] ?></h1>
                <p class="mb-0 text-muted"><?= $team['name_en'] ?></p>
            </div>
        </div>
        <a href="<?= base_url('coach/teams') ?>" class="btn btn-outline-primary">
            <i class="fas fa-arrow-left me-1"></i>กลับรายการสโมสร
        </a>
    </div>

    <!-- Team Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">จำนวนนักเตะ</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800"><?= count($players) ?></div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">นักเตะใหม่ (เดือนนี้)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $newPlayersCount = 0;
                                foreach ($players as $player) {
                                    if (date('Y-m', strtotime($player['create_date'])) == date('Y-m')) {
                                        $newPlayersCount++;
                                    }
                                }
                                echo $newPlayersCount;
                                ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-user-plus fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">อายุเฉลี่ย</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <?php
                                $totalAge = 0;
                                $validAges = 0;
                                foreach ($players as $player) {
                                    if ($player['birth_date']) {
                                        $age = date_diff(date_create($player['birth_date']), date_create('today'))->y;
                                        $totalAge += $age;
                                        $validAges++;
                                    }
                                }
                                echo $validAges > 0 ? round($totalAge / $validAges, 1) : 0;
                                ?> ปี
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-birthday-cake fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">สถานะทีม</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                <span class="badge bg-success">ใช้งาน</span>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">เมนูจัดการทีม</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('coach/team/' . $team['id'] . '/players') ?>" class="btn btn-primary w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <span>จัดการนักเตะ</span>
                                <small class="text-light"><?= count($players) ?> คน</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('coach/team/' . $team['id'] . '/training') ?>" class="btn btn-info w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-dumbbell fa-2x mb-2"></i>
                                <span>แผนการฝึกซ้อม</span>
                                <small class="text-light">จัดการการฝึก</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('coach/team/' . $team['id'] . '/matches') ?>" class="btn btn-success w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-futbol fa-2x mb-2"></i>
                                <span>การแข่งขัน</span>
                                <small class="text-light">ดูผลการแข่ง</small>
                            </a>
                        </div>
                        <div class="col-md-3 mb-3">
                            <a href="<?= base_url('coach/team/' . $team['id'] . '/reports') ?>" class="btn btn-warning w-100 h-100 d-flex flex-column align-items-center justify-content-center">
                                <i class="fas fa-chart-bar fa-2x mb-2"></i>
                                <span>รายงาน</span>
                                <small class="text-light">สถิติและข้อมูล</small>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Players -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">นักเตะล่าสุด</h6>
                    <a href="<?= base_url('coach/team/' . $team['id'] . '/players') ?>" class="btn btn-primary btn-sm">
                        ดูทั้งหมด
                    </a>
                </div>
                <div class="card-body">
                    <?php if (!empty($players)): ?>
                        <div class="table-responsive">
                            <table class="table table-bordered" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>เลขเสื้อ</th>
                                        <th>ชื่อ-นามสกุล</th>
                                        <th>ตำแหน่ง</th>
                                        <th>อายุ</th>
                                        <th>วันที่เพิ่ม</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    $recentPlayers = array_slice($players, -5); // Show last 5 players
                                    foreach ($recentPlayers as $player):
                                        $age = $player['birth_date'] ? date_diff(date_create($player['birth_date']), date_create('today'))->y : '-';
                                    ?>
                                    <tr>
                                        <td class="text-center">
                                            <span class="badge bg-primary"><?= $player['shirt_number'] ?: '-' ?></span>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <?php if ($player['image_path']): ?>
                                                    <img src="<?= base_url($player['image_path']) ?>" width="40" height="40" class="rounded-circle me-2">
                                                <?php else: ?>
                                                    <div class="rounded-circle bg-gray-300 me-2 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                                        <i class="fas fa-user text-gray-600"></i>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <strong><?= $player['first_name'] . ' ' . $player['last_name'] ?></strong>
                                                    <?php if ($player['nick_name']): ?>
                                                        <br><small class="text-muted">(<?= $player['nick_name'] ?>)</small>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?= $player['position_name'] ?: '-' ?></td>
                                        <td><?= $age ?></td>
                                        <td><?= date('d/m/Y', strtotime($player['create_date'])) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="fas fa-users fa-3x text-gray-300 mb-3"></i>
                            <p class="text-muted">ยังไม่มีนักเตะในทีม</p>
                            <a href="<?= base_url('coach/team/' . $team['id'] . '/players') ?>" class="btn btn-primary">
                                เพิ่มนักเตะ
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>