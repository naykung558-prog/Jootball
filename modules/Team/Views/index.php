<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>

<div class="container-fluid p-0">
    <?php echo view("Modules\Team\Views\subnav.php"); ?>

    <?php if (empty($team)): ?>
        <div class="row justify-content-center g-4 mt-3 mb-5">
            <!-- No Team Yet - Show Create Team Card -->
            <div class="<?= !empty($userRequests) ? 'col-lg-4' : 'col-lg-6' ?>">
                <div class="card shadow-lg border-0 rounded-4 overflow-hidden h-100">
                    <div class="card-body text-center p-5 d-flex flex-column justify-content-center">
                        <div class="mb-4">
                            <i class="fas fa-users fa-5x text-primary opacity-50"></i>
                        </div>
                        <h2 class="fw-bold text-dark mb-3"><?= service('request')->getLocale() === 'th' ? 'คุณยังไม่มีทีม' : 'You Don\'t Have a Team Yet' ?></h2>
                        <p class="text-muted mb-4">
                            <?= service('request')->getLocale() === 'th'
                                ? 'สร้างทีมของคุณเพื่อเริ่มต้นการจัดการนักเตะและเข้าร่วมทัวร์นาเมนต์'
                                : 'Create your team to start managing players and join tournaments' ?>
                        </p>
                        <div class="d-grid gap-2 col-md-10 mx-auto">
                            <a href="<?= base_url('academy/register') ?>" class="btn btn-primary btn-lg rounded-pill py-3">
                                <i class="fas fa-plus-circle me-2"></i>
                                <?= service('request')->getLocale() === 'th' ? 'สร้าง Academy ของฉัน' : 'Create My Academy' ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($userRequests)): ?>
            <!-- User Join Requests -->
            <div class="col-lg-8">
                <div class="card shadow border-0 rounded-4 h-100">
                    <div class="card-header bg-white border-0 py-4 px-4 d-flex align-items-center">
                        <div class="avatar-sm bg-soft-primary rounded-circle me-3 d-flex align-items-center justify-content-center">
                            <i class="fas fa-history text-primary font-20"></i>
                        </div>
                        <div>
                            <h5 class="mb-1 fw-bold text-dark">ประวัติการขอเข้าร่วมทีม</h5>
                            <p class="mb-0 text-muted small">รายการคำขอเข้าร่วมทีมทั้งหมดของคุณ</p>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4 py-3 border-0 text-uppercase text-muted small fw-bold">ทีม</th>
                                        <th class="py-3 border-0 text-uppercase text-muted small fw-bold">สถานะ</th>
                                        <th class="py-3 border-0 text-uppercase text-muted small fw-bold">วันที่ส่งคำขอ</th>
                                        <th class="py-3 border-0 text-end pe-4 text-uppercase text-muted small fw-bold">หมายเหตุ</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($userRequests as $req): ?>
                                    <tr>
                                        <td class="ps-4 py-3">
                                            <div class="d-flex align-items-center">
                                                <?php if(!empty($req['team_logo'])): ?>
                                                    <img src="<?= base_url($req['team_logo']) ?>" class="rounded-circle avatar-sm me-3 shadow-sm border border-2 border-white" alt="team-logo" style="width: 42px; height: 42px; object-fit:cover;">
                                                <?php else: ?>
                                                    <div class="avatar-sm me-3">
                                                        <span class="avatar-title bg-gradient-primary rounded-circle text-white shadow-sm font-18 fw-bold">
                                                            <?= strtoupper(substr($req['team_name'] ?? 'T', 0, 1)) ?>
                                                        </span>
                                                    </div>
                                                <?php endif; ?>
                                                <div>
                                                    <h6 class="mb-0 fw-bold text-dark font-15"><?= esc($req['team_name']) ?></h6>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3">
                                            <?php
                                            $statusBadge = 'bg-soft-warning text-warning';
                                            $statusIcon = 'fa-clock';
                                            $statusText = 'รอการอนุมัติ';

                                            if ($req['status'] === 'approved') {
                                                $statusBadge = 'bg-soft-success text-success';
                                                $statusIcon = 'fa-check-circle';
                                                $statusText = 'อนุมัติแล้ว';
                                            } elseif ($req['status'] === 'rejected') {
                                                $statusBadge = 'bg-soft-danger text-danger';
                                                $statusIcon = 'fa-times-circle';
                                                $statusText = 'ถูกปฏิเสธ';
                                            }
                                            ?>
                                            <span class="badge <?= $statusBadge ?> rounded-pill font-13 fw-medium px-3 py-1 d-inline-flex align-items-center border-0">
                                                <i class="fas <?= $statusIcon ?> me-1"></i> <?= $statusText ?>
                                            </span>
                                        </td>
                                        <td class="text-muted py-3">
                                            <span class="d-flex align-items-center">
                                                <i class="far fa-calendar-alt me-2 text-primary opacity-50"></i>
                                                <?= date('d/m/Y', strtotime($req['created_at'])) ?>
                                                <span class="ms-2 small text-muted"><?= date('H:i', strtotime($req['created_at'])) ?></span>
                                            </span>
                                        </td>
                                        <td class="text-end pe-4 py-3">
                                            <?php if($req['status'] === 'rejected'): ?>
                                                <span class="text-danger small"><i class="fas fa-exclamation-circle me-1"></i> คำขอของคุณไม่ผ่านการอนุมัติ</span>
                                            <?php else: ?>
                                                <span class="text-muted">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>

    <?php else: ?>

        <!-- Team Exists - Show Team Info -->
        <div class="row g-4 mb-5">
            <!-- Team Profile Card -->
            <div class="col-lg-4">
                <div class="card shadow-sm border-0 h-100 rounded-4 overflow-hidden">
                    <div class="card-body text-center p-5 bg-light position-relative">
                        <div class="mb-4">
                            <?php if (!empty($team['logo'])) { ?>
                                <img src="<?= base_url($team['logo']) ?>" alt="Team Logo"
                                    class="img-fluid rounded-circle shadow-sm border border-4 border-white"
                                    style="width: 180px; height: 180px; object-fit: cover;">
                            <?php } else { ?>
                                <img src="<?= base_url('public/img/exam-logo.webp') ?>" alt="Default Logo"
                                    class="img-fluid rounded-circle shadow-sm border border-4 border-white"
                                    style="width: 180px; height: 180px; object-fit: cover;">
                            <?php } ?>
                        </div>

                        <h3 class="fw-bold text-dark mb-1"><?= @$team['name'] ?></h3>
                        <?php if (!empty($team['name_en'])): ?>
                            <p class="text-muted mb-3"><?= $team['name_en'] ?></p>
                        <?php endif; ?>

                        <div class="d-grid gap-2 mt-4">
                                <a href="<?= base_url('team/manage/'. $team['id']) ?>" class="btn btn-primary rounded-pill py-2">
                                    <i class="fa fa-edit me-2"></i> <?= lang('Common.edit_information') ?>
                                </a>
                        </div>
                    </div>
                </div>
            </div>

        <!-- Team Details & Map -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="mb-0 fw-bold text-primary"><i class="fa fa-info-circle me-2"></i><?= lang('Common.team_information') ?></h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.organization_name') ?></label>
                                <div class="fw-medium"><?= !empty($team['organize_name']) ? $team['organize_name'] : '-' ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.head_coach_name') ?></label>
                                <div class="fw-medium">
                                    <?= !empty($team['coach_name']) ? $team['coach_name'] : '-' ?>
                                    <?php if(!empty($team['coach_phone'])): ?>
                                        <div class="small text-muted mt-1"><i class="fa fa-phone me-1"></i> <?= $team['coach_phone'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.coordinator_name') ?></label>
                                <div class="fw-medium">
                                    <?= !empty($team['contact_name']) ? $team['contact_name'] : '-' ?>
                                    <?php if(!empty($team['contact_phone'])): ?>
                                        <div class="small text-muted mt-1"><i class="fa fa-phone me-1"></i> <?= $team['contact_phone'] ?></div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="small text-muted text-uppercase fw-bold mb-1"><?= lang('Common.line_id') ?></label>
                                <div class="fw-medium text-success">
                                    <?php if(!empty($team['contact_line'])): ?>
                                        <i class="fab fa-line me-1"></i> <?= $team['contact_line'] ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <?php if (!empty($team['latitude']) && !empty($team['longitude'])): ?>
                <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
                    <div class="card-header bg-white border-0 py-3 px-4">
                        <h5 class="mb-0 fw-bold text-primary"><i class="fa fa-map-marker-alt me-2"></i><?= lang('Common.location') ?></h5>
                    </div>
                    <div class="card-body p-0">
                        <div id="map-canvas" style="height: 400px; width: 100%;"></div>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Sub-Teams Section - แสดงเฉพาะทีมหลัก (ไม่มี parent_team_id) -->
    <?php if (!empty($team) && empty($team['parent_team_id'])): ?>
    <div class="row g-4 mb-5">
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex align-items-center justify-content-between">
                    <h5 class="mb-0 fw-bold text-primary">
                        <i class="fas fa-sitemap me-2"></i><?= lang('Common.sub_teams_in_club') ?>
                    </h5>
                    <a href="<?= base_url('team/subteam/' . $team['id']) ?>" class="btn btn-primary btn-sm rounded-pill">
                        <i class="fas fa-plus me-1"></i> <?= lang('Common.create_team') ?>
                    </a>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($subteams)): ?>
                        <div class="row g-3">
                            <?php foreach ($subteams as $subteam): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 border shadow-sm hover-shadow">
                                    <div class="card-body">
                                        <div class="d-flex align-items-center mb-3">
                                            <?php
                                                $subLogo = !empty($subteam['logo']) ? base_url($subteam['logo']) : base_url('public/img/exam-logo.webp');
                                            ?>
                                            <img src="<?= $subLogo ?>" alt="" class="rounded-circle me-3 border"
                                                style="width: 60px; height: 60px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-1 fw-bold"><?= esc($subteam['name']) ?></h6>
                                                <?php if (!empty($subteam['name_en'])): ?>
                                                    <small class="text-muted"><?= esc($subteam['name_en']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <?php if (!empty($subteam['coach_name'])): ?>
                                        <p class="mb-2 small">
                                            <i class="fas fa-user-tie text-muted me-2"></i>
                                            <?= esc($subteam['coach_name']) ?>
                                            <?php if (!empty($subteam['coach_phone'])): ?>
                                                <span class="text-muted ms-2"><?= $subteam['coach_phone'] ?></span>
                                            <?php endif; ?>
                                        </p>
                                        <?php endif; ?>

                                        <div class="d-flex gap-2 mt-3">
                                            <a href="<?= base_url('team/info/' . $subteam['id']) ?>" class="btn btn-outline-primary btn-sm flex-fill">
                                                <i class="fas fa-eye me-1"></i> <?= lang('Common.view_details') ?>
                                            </a>
                                            <a href="<?= base_url('team/subteam/' . $team['id'] . '/' . $subteam['id']) ?>" class="btn btn-outline-warning btn-sm">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-users fa-3x text-muted mb-3 opacity-50"></i>
                            <h6 class="text-muted"><?= lang('Common.no_sub_teams') ?></h6>
                            <p class="text-muted small mb-3"><?= lang('Common.create_sub_team_desc') ?></p>
                            <a href="<?= base_url('team/subteam/' . $team['id']) ?>" class="btn btn-primary rounded-pill">
                                <i class="fas fa-plus me-2"></i> <?= lang('Common.create_first_team') ?>
                            </a>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?php if (!empty($team)): ?>
<script>
    function initMap() {
        const lat = parseFloat('<?= @$team['latitude'] ?>');
        const lng = parseFloat('<?= @$team['longitude'] ?>');

        if (!isNaN(lat) && !isNaN(lng)) {
            const teamLocation = {
                lat: lat,
                lng: lng
            };

            const map = new google.maps.Map(document.getElementById("map-canvas"), {
                zoom: 16,
                center: teamLocation,
                gestureHandling: 'cooperative',
                zoomControl: true,
                streetViewControl: false,
                mapTypeControl: false,
                fullscreenControl: true,
                styles: [
                    {
                        "featureType": "poi",
                        "elementType": "labels",
                        "stylers": [{ "visibility": "off" }]
                    }
                ]
            });

            const marker = new google.maps.Marker({
                position: teamLocation,
                map: map,
                title: '<?= esc(@$team['name']) ?>',
                animation: google.maps.Animation.DROP
            });
        }
    }

    // Load map if coordinates exist
    <?php if (!empty($team['latitude']) && !empty($team['longitude'])): ?>
        if (typeof loadGoogleMapsAPI === 'function') {
            loadGoogleMapsAPI(initMap);
        }
    <?php endif; ?>
</script>
<?php endif; ?>
<?php $this->endSection(); ?>