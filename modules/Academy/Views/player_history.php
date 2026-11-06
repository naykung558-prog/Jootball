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
                    <li class="breadcrumb-item active">ประวัติ Academy</li>
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
                <small class="text-muted"><i class="fas fa-history me-1"></i>ประวัติการเข้า-ออก Academy ทั้งหมด</small>
            </div>
            <div class="ms-auto">
                <a href="<?= base_url('academy/player-view/' . $academy_id . '/' . $player['id']) ?>" class="btn btn-light rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i> กลับ
                </a>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="card shadow-sm border-0 rounded-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0"><i class="fas fa-history me-2 text-primary"></i>ประวัติการเข้า-ออก Academy</h6>
            <span class="badge bg-primary rounded-pill"><?= count($history) ?> รายการ</span>
        </div>
        <div class="card-body px-4 pb-4">
            <?php if (empty($history)): ?>
            <div class="text-center py-5 text-muted">
                <i class="fas fa-inbox fa-3x mb-3 opacity-25"></i>
                <p>ยังไม่มีประวัติ</p>
            </div>
            <?php else: ?>
            <div class="timeline">
                <?php foreach ($history as $h): ?>
                <div class="card border-0 rounded-4 mb-3 <?= $h['is_active'] ? 'bg-success bg-opacity-10 border-success border border-opacity-25' : 'bg-light' ?>">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <!-- Academy Logo -->
                            <?php if (!empty($h['logo'])): ?>
                            <img src="<?= base_url($h['logo']) ?>" class="rounded" style="width:48px;height:48px;object-fit:cover;flex-shrink:0;">
                            <?php else: ?>
                            <div class="bg-primary text-white rounded d-flex align-items-center justify-content-center" style="width:48px;height:48px;flex-shrink:0;">
                                <i class="fas fa-school"></i>
                            </div>
                            <?php endif; ?>

                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-bold"><?= esc($h['academy_name']) ?></span>
                                    <?php if ($h['is_active']): ?>
                                    <span class="badge bg-success rounded-pill" style="font-size:0.65rem;">ยังอยู่</span>
                                    <?php else: ?>
                                    <span class="badge bg-secondary rounded-pill" style="font-size:0.65rem;">ออกแล้ว</span>
                                    <?php endif; ?>
                                    <?php if ((int)$h['academy_id'] === (int)$academy_id): ?>
                                    <span class="badge bg-primary rounded-pill" style="font-size:0.65rem;">Academy นี้</span>
                                    <?php endif; ?>
                                </div>
                                <div class="row g-2">
                                    <div class="col-auto">
                                        <small class="text-muted">ทีม:</small>
                                        <small class="fw-semibold ms-1"><?= esc($h['team_name'] ?? '-') ?></small>
                                    </div>
                                    <div class="col-auto">
                                        <small class="text-muted">เบอร์:</small>
                                        <small class="fw-bold text-primary ms-1"><?= esc($h['shirt_number'] ?: '-') ?></small>
                                    </div>
                                    <?php if (!empty($h['enrolled_by_name'])): ?>
                                    <div class="col-auto">
                                        <small class="text-muted">เพิ่มโดย:</small>
                                        <small class="ms-1"><?= esc($h['enrolled_by_name']) ?></small>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Dates -->
                            <div class="text-end" style="flex-shrink:0;">
                                <div>
                                    <small class="text-muted d-block">วันที่เข้า</small>
                                    <small class="fw-semibold text-success">
                                        <i class="fas fa-sign-in-alt me-1"></i>
                                        <?= date('d/m/', strtotime($h['enrolled_at'])) . (date('Y', strtotime($h['enrolled_at'])) + 543) ?>
                                    </small>
                                </div>
                                <?php if (!empty($h['left_at'])): ?>
                                <div class="mt-1">
                                    <small class="text-muted d-block">วันที่ออก</small>
                                    <small class="fw-semibold text-danger">
                                        <i class="fas fa-sign-out-alt me-1"></i>
                                        <?= date('d/m/', strtotime($h['left_at'])) . (date('Y', strtotime($h['left_at'])) + 543) ?>
                                    </small>
                                </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>

</div>
<?php $this->endSection(); ?>
