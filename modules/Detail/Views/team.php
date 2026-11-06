<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<div class="container-fluid px-4 py-4">
    <div class="card border-0 shadow-sm rounded-3 overflow-hidden">
        <?php if (!empty($academy)):
            $academyLogoUrl = !empty($academy['logo'])
                ? (filter_var($academy['logo'], FILTER_VALIDATE_URL) ? $academy['logo'] : base_url($academy['logo']))
                : base_url('public/img/jootball_logo.png');
            $academyDisplayName = !empty($academy['name']) ? $academy['name'] : ($academy['name_en'] ?? '');
        ?>
        <a href="<?= base_url('admin/academy/detail/' . esc($academy['id'])) ?>"
           class="d-flex align-items-center gap-3 px-4 py-3 text-decoration-none"
           style="background: linear-gradient(135deg, #1a6b3c 0%, #28a745 60%, #34c759 100%);">
            <img src="<?= $academyLogoUrl ?>" class="rounded-circle border border-white shadow"
                 style="width:52px;height:52px;object-fit:cover;flex-shrink:0;"
                 onerror="this.src='<?= base_url('public/img/jootball_logo.png') ?>'">
            <div>
                <div class="text-white opacity-75 small fw-semibold" style="letter-spacing:.5px;">
                    <i class="ti ti-school me-1"></i>สังกัด Academy
                </div>
                <div class="text-white fw-bold fs-5 lh-sm"><?= esc($academyDisplayName) ?></div>
            </div>
            <i class="ti ti-chevron-right text-white ms-auto opacity-75 fs-5"></i>
        </a>
        <?php endif; ?>
        <div class="card-header bg-white border-bottom py-3">
            <h5 class="card-title fw-bold mb-0 text-dark"><i class="ti ti-shirt me-2 text-primary"></i>ข้อมูลทีม (Team Details)</h5>
        </div>
        <div class="card-body p-4">

            <div class="row g-4 mb-5">
                <div class="col-md-4 text-center border-end-md">
                    <div class="mb-3">
                        <?php if (!empty($team['logo'])) { ?>
                            <img src="<?= base_url($team['logo']) ?>" alt="Team Logo"
                                class="img-fluid rounded-circle shadow-sm p-1 bg-white border" style="width: 200px; height: 200px; object-fit: cover;">
                        <?php } else { ?>
                            <img src="<?= base_url('public/img/exam-logo.webp') ?>" alt="Default Logo"
                                class="img-fluid rounded-circle shadow-sm p-1 bg-white border" style="width: 200px; height: 200px; object-fit: cover;">
                        <?php } ?>
                    </div>
                    <h4 class="fw-bold text-dark mb-1"><?= @$team['name'] ?></h4>
                    <?php if (!empty($team['name_en'])): ?>
                        <p class="text-muted mb-2">(<?= $team['name_en'] ?>)</p>
                    <?php endif; ?>
                    <span class="badge bg-primary-subtle text-primary rounded-pill px-3 py-2">
                        <i class="ti ti-building me-1"></i> <?= @$team['organize_name'] ?>
                    </span>
                </div>
                <div class="col-md-8">
                    <h5 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="ti ti-info-circle me-2 text-info"></i>ข้อมูลการติดต่อ</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="small text-muted fw-bold mb-1">หัวหน้าผู้ฝึกสอน</label>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-user-circle fs-5 me-2 text-secondary"></i>
                                    <span class="fw-semibold"><?= @$team['coach_name'] ?></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-phone fs-5 me-2 text-secondary"></i>
                                    <span><?= @$team['coach_phone'] ?></span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-light rounded-3 h-100">
                                <label class="small text-muted fw-bold mb-1">ผู้ประสานงาน</label>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-user fs-5 me-2 text-secondary"></i>
                                    <span class="fw-semibold"><?= @$team['contact_name'] ?></span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ti ti-phone fs-5 me-2 text-secondary"></i>
                                    <span><?= @$team['contact_phone'] ?></span>
                                </div>
                                <div class="d-flex align-items-center">
                                    <i class="ti ti-brand-line fs-5 me-2 text-success"></i>
                                    <span><?= @$team['contact_line'] ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($team['latitude']) && !empty($team['longitude'])): ?>
                        <h5 class="fw-bold text-dark border-bottom pb-2 mb-3 mt-4"><i class="ti ti-map-pin me-2 text-danger"></i>ที่ตั้งทีม</h5>
                        <div id="map-canvas" class="rounded-3 shadow-sm border" style="height: 300px; width: 100%;"></div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="p-1 rounded-3 mb-4" style="background:#f0f4f8;">
                <ul class="nav nav-pills nav-fill gap-1" id="teamTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active fw-bold py-2 px-3" id="players-tab" data-bs-toggle="tab" data-bs-target="#players" type="button" role="tab" aria-controls="players" aria-selected="true"
                            style="border-radius:8px; transition:all .2s;">
                            <i class="ti ti-users me-2"></i>รายชื่อนักเตะ
                            <span class="badge ms-1 <?= !empty($player) ? 'bg-primary' : 'bg-secondary' ?>"><?= count($player ?? []) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-2 px-3" id="coaches-tab" data-bs-toggle="tab" data-bs-target="#coaches" type="button" role="tab" aria-controls="coaches" aria-selected="false"
                            style="border-radius:8px; transition:all .2s;">
                            <i class="ti ti-whistle me-2"></i>รายชื่อผู้ฝึกสอน
                            <span class="badge ms-1 <?= !empty($coach) ? 'bg-primary' : 'bg-secondary' ?>"><?= count($coach ?? []) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link fw-bold py-2 px-3" id="tournaments-tab" data-bs-toggle="tab" data-bs-target="#tournaments" type="button" role="tab" aria-controls="tournaments" aria-selected="false"
                            style="border-radius:8px; transition:all .2s;">
                            <i class="ti ti-trophy me-2"></i>ประวัติการแข่งขัน
                            <span class="badge ms-1 <?= !empty($tournaments) ? 'bg-warning text-dark' : 'bg-secondary' ?>"><?= count($tournaments ?? []) ?></span>
                        </button>
                    </li>
                </ul>
            </div>

            <div class="tab-content" id="teamTabsContent">
                <div class="tab-pane fade show active" id="players" role="tabpanel" aria-labelledby="players-tab">
                    <?php
                    // Define position colors for badges
                    $positionColors = [
                        4 => ['bg' => 'bg-warning-subtle', 'text' => 'text-warning'],      // Goalkeeper
                        3 => ['bg' => 'bg-info-subtle', 'text' => 'text-info'],            // Defender
                        2 => ['bg' => 'bg-success-subtle', 'text' => 'text-success'],      // Midfielder
                        1 => ['bg' => 'bg-danger-subtle', 'text' => 'text-danger'],        // Attacker
                    ];
                    $defaultColor = ['bg' => 'bg-secondary-subtle', 'text' => 'text-secondary'];
                    ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="playerTable" style="width:100%">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-3 rounded-start">ลำดับ</th>
                                    <th class="py-3">รูป</th>
                                    <th class="py-3">หมายเลข</th>
                                    <th class="py-3">ชื่อ-นามสกุล</th>
                                    <th class="py-3">ชื่อเล่น</th>
                                    <th class="py-3">ตำแหน่ง</th>
                                    <th class="py-3">วัน/เดือน/ปีเกิด</th>
                                    <th class="py-3 rounded-end">อายุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $index = 0;
                                foreach ($playersByPosition as $positionId => $positionData):
                                    $color = $positionColors[$positionId] ?? $defaultColor;
                                    foreach ($positionData['players'] as $p):
                                        $index++;
                                        $birthDate = new DateTime($p['birth_date']);
                                        $today = new DateTime();
                                        $age = $today->diff($birthDate)->y;

                                        $birthDateObj = new DateTime($p['birth_date']);
                                        $day = $birthDateObj->format('d');
                                        $month = $birthDateObj->format('m');
                                        $year = $birthDateObj->format('Y') + 543;
                                ?>
                                <tr>
                                    <td class="ps-3 text-muted"><?= $index ?></td>
                                    <td>
                                        <?php if (!empty($p['image_path']) && file_exists(FCPATH . $p['image_path'])): ?>
                                            <img src="<?= base_url($p['image_path']) ?>" class="rounded-circle border shadow-sm" width="40" height="40" style="object-fit: cover;">
                                        <?php else: ?>
                                            <img src="<?= base_url('public/img/football-player.webp') ?>" class="rounded-circle border shadow-sm" width="40" height="40" style="object-fit: cover;">
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-dark rounded-pill"><?= esc($p['shirt_number']) ?></span></td>
                                    <td class="fw-semibold"><?= esc($p['first_name'] . ' ' . $p['last_name']) ?></td>
                                    <td class="text-muted"><?= esc($p['nick_name']) ?></td>
                                    <td>
                                        <?php if (!empty($p['sub_position_display'])): ?>
                                            <span class="badge <?= $color['bg'] ?> <?= $color['text'] ?> rounded-pill"><?= esc($p['sub_position_display']) ?></span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td data-sort="<?= $p['birth_date'] ?>"><i class="ti ti-calendar me-1 text-muted"></i><?= $day . '/' . $month . '/' . $year ?></td>
                                    <td data-sort="<?= $age ?>"><span class="badge bg-info-subtle text-info-emphasis rounded-pill"><?= $age ?> ปี</span></td>
                                </tr>
                                <?php
                                    endforeach;
                                endforeach;
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <div class="tab-pane fade" id="coaches" role="tabpanel" aria-labelledby="coaches-tab">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="coachTable" style="width:100%">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-3 rounded-start">ลำดับ</th>
                                    <th class="py-3">รูป</th>
                                    <th class="py-3">ชื่อ-นามสกุล</th>
                                    <th class="py-3">ชื่อเล่น</th>
                                    <th class="py-3">วัน/เดือน/ปีเกิด</th>
                                    <th class="py-3 rounded-end">อายุ</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($coach as $key => $p) {
                                    $birthDate = new DateTime($p['birth_date']);
                                    $today = new DateTime();
                                    $age = $today->diff($birthDate)->y;

                                    $birthDateObj = new DateTime($p['birth_date']);
                                    $day = $birthDateObj->format('d');
                                    $month = $birthDateObj->format('m');
                                    $year = $birthDateObj->format('Y') + 543;
                                ?>
                                <tr>
                                    <td class="ps-3 text-muted"><?php echo $key + 1; ?></td>
                                    <td>
                                        <img src="<?php echo base_url($p['image_path'])?>" class="rounded-circle border shadow-sm" width="40" height="40" style="object-fit: cover;">
                                    </td>
                                    <td class="fw-semibold"><?php echo $p['first_name'].' '.$p['last_name']?></td>
                                    <td class="text-muted"><?php echo $p['nick_name']?></td>
                                    <td data-sort="<?php echo $p['birth_date']; ?>"><i class="ti ti-calendar me-1 text-muted"></i><?php echo $day.'/'.$month.'/'.$year; ?></td>
                                    <td data-sort="<?php echo $age; ?>"><span class="badge bg-info-subtle text-info-emphasis rounded-pill"><?php echo $age; ?> ปี</span></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tab: ประวัติการแข่งขัน -->
                <div class="tab-pane fade" id="tournaments" role="tabpanel" aria-labelledby="tournaments-tab">
                    <?php if (!empty($tournaments)): ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" id="tournamentTable" style="width:100%">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-3 rounded-start" width="5%">ลำดับ</th>
                                    <th class="py-3 text-center" width="8%">โลโก้</th>
                                    <th class="py-3" width="47%">ชื่อการแข่งขัน</th>
                                    <th class="py-3 text-center" width="25%">วันที่แข่ง</th>
                                    <th class="py-3 text-center" width="15%">นัดที่ลง</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($tournaments as $i => $tour):
                                    $logoUrl = !empty($tour['logo'])
                                        ? base_url($tour['logo'])
                                        : base_url('public/img/jootball_logo.png');
                                    $tourName = !empty($tour['name']) ? $tour['name'] : ($tour['name_en'] ?? '-');
                                    $startDate = !empty($tour['start_date']) ? date('d/m/', strtotime($tour['start_date'])) . (date('Y', strtotime($tour['start_date'])) + 543) : '-';
                                    $endDate   = !empty($tour['end_date'])   ? date('d/m/', strtotime($tour['end_date']))   . (date('Y', strtotime($tour['end_date']))   + 543) : '-';
                                ?>
                                <tr>
                                    <td class="ps-3 text-muted"><?= $i + 1 ?></td>
                                    <td class="text-center">
                                        <img src="<?= $logoUrl ?>" class="rounded-circle border shadow-sm"
                                             width="40" height="40" style="object-fit:cover;"
                                             onerror="this.src='<?= base_url('public/img/jootball_logo.png') ?>'">
                                    </td>
                                    <td class="fw-semibold"><?= esc($tourName) ?></td>
                                    <td class="text-center">
                                        <small class="text-muted">
                                            <i class="ti ti-calendar me-1"></i><?= $startDate ?>
                                            <?php if ($startDate !== $endDate): ?> – <?= $endDate ?><?php endif; ?>
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3">
                                            <?= (int)$tour['match_count'] ?> นัด
                                        </span>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-5 text-muted">
                        <i class="ti ti-trophy-off fs-1 opacity-25 d-block mb-3"></i>
                        <p class="mb-0">ยังไม่มีประวัติการแข่งขัน</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>

        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    function initMap() {
        const lat = parseFloat('<?= @$team["latitude"] ?>');
        const lng = parseFloat('<?= @$team["longitude"] ?>');

        if (!isNaN(lat) && !isNaN(lng)) {
            const teamLocation = {
                lat: lat,
                lng: lng
            };

            const map = new google.maps.Map(document.getElementById("map-canvas"), {
                zoom: 16,
                center: teamLocation,
                gestureHandling: 'none',
                zoomControl: false,
                streetViewControl: false,
                mapTypeControl: false,
                fullscreenControl: false
            });

            const marker = new google.maps.Marker({
                position: teamLocation,
                map: map,
                title: '<?= esc(@$team["name"]) ?>'
            });
        }
    }
    <?php if (!empty($team['latitude']) && !empty($team['longitude'])): ?>
    if (typeof loadGoogleMapsAPI === 'function') {
        loadGoogleMapsAPI(initMap);
    }
    <?php endif; ?>

    // Initialize DataTables for Player and Coach tables
    $(document).ready(function() {
        let dtLanguage = {
            "lengthMenu": "แสดง _MENU_ รายการ",
            "search": "ค้นหา:",
            "zeroRecords": "ไม่มีข้อมูล",
            "emptyTable": "ไม่มีข้อมูลในตาราง",
            "info": "รายการที่ _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "infoFiltered": "(กรองข้อมูลจากทั้งหมด _MAX_ แถว)",
            "infoEmpty": "ไม่มีข้อมูล",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
        };

        // Player Table
        $('#playerTable').DataTable({
            language: dtLanguage,
            pageLength: 25,
            order: [[0, 'asc']], // เรียงตามลำดับ
            columnDefs: [
                { orderable: false, targets: [1] }, // ไม่ให้เรียงลำดับคอลัมน์รูป
                { orderData: [6], targets: [6] }, // ใช้ data-sort attribute สำหรับวันเกิด
                { orderData: [7], targets: [7] }  // ใช้ data-sort attribute สำหรับอายุ
            ]
        });

        // Coach Table
        $('#coachTable').DataTable({
            language: dtLanguage,
            pageLength: 10,
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [1] },
                { orderData: [4], targets: [4] },
                { orderData: [5], targets: [5] }
            ]
        });

        // Tournament History Table
        $('#tournamentTable').DataTable({
            language: dtLanguage,
            pageLength: 25,
            order: [[0, 'asc']],
            columnDefs: [
                { orderable: false, targets: [1] }
            ]
        });
    });
</script>

<?php $this->endSection(); ?>