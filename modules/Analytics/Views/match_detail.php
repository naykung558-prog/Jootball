<?php
$baseUrl = rtrim(base_url(), "/") . "/";
$matchId   = $match['id'];
$statusMap = [
    'pending'    => ['warning', 'รอดำเนินการ'],
    'processing' => ['info',    'กำลังประมวลผล'],
    'completed'  => ['success', 'เสร็จสิ้น'],
    'error'      => ['danger',  'ผิดพลาด'],
];
$ms = isset($statusMap[$match['status']]) ? $statusMap[$match['status']] : ['secondary', $match['status']];
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-start mb-4">
    <div>
        <a href="<?= $baseUrl ?>admin/analytics/matches" class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left"></i> กลับ
        </a>
        <h4 class="text-gray-800 mb-1"><?= esc($match['match_name']) ?></h4>
        <span class="text-muted small">
            <i class="fas fa-calendar mr-1"></i><?= date('d/m/Y', strtotime($match['match_date'])) ?>
            <?php if ($match['venue']): ?>
                &nbsp;|&nbsp;<i class="fas fa-map-marker-alt mr-1"></i><?= esc($match['venue']) ?>
            <?php endif; ?>
        </span>
    </div>
    <div class="text-right">
        <span class="badge badge-<?= $ms[0] ?> badge-pill px-3 py-2"><?= $ms[1] ?></span>
        <div class="mt-2">
            <a href="<?= $baseUrl ?>admin/analytics/matches/manage/<?= $matchId ?>" class="btn btn-sm btn-warning">
                <i class="fas fa-edit mr-1"></i>แก้ไข
            </a>
            <?php if ($match['status'] === 'completed'): ?>
            <a href="<?= $baseUrl ?>admin/analytics/report/match/<?= $matchId ?>" class="btn btn-sm btn-success">
                <i class="fas fa-file-alt mr-1"></i>รายงาน
            </a>
            <a href="<?= $baseUrl ?>admin/analytics/report/export-pdf/<?= $matchId ?>" class="btn btn-sm btn-danger" target="_blank">
                <i class="fas fa-file-pdf mr-1"></i>PDF
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- สกอร์ -->
<div class="card shadow mb-4">
    <div class="card-body text-center py-4">
        <div class="row align-items-center">
            <div class="col-5 text-right">
                <h5 class="font-weight-bold"><?= esc($match['home_team_name_real'] ?? $match['home_team_name']) ?></h5>
                <span class="text-muted small">เหย้า</span>
            </div>
            <div class="col-2">
                <h2 class="font-weight-bold text-primary">
                    <?= $match['home_score'] ?> - <?= $match['away_score'] ?>
                </h2>
            </div>
            <div class="col-5 text-left">
                <h5 class="font-weight-bold"><?= esc($match['away_team_name_real'] ?? $match['away_team_name']) ?></h5>
                <span class="text-muted small">เยือน</span>
            </div>
        </div>
    </div>
</div>

<!-- วิดีโอ -->
<div class="row">
    <?php foreach ($videos as $video): ?>
    <?php
    $vs     = isset($statusMap[$video['status']]) ? $statusMap[$video['status']] : ['secondary', $video['status']];
    $canProc = in_array($video['status'], ['uploaded', 'error']);
    ?>
    <div class="col-md-6 mb-4">
        <div class="card shadow h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <span class="font-weight-bold">ครึ่งที่ <?= $video['half'] ?></span>
                <span class="badge badge-<?= $vs[0] ?>" id="statusBadge_<?= $video['id'] ?>"><?= $vs[1] ?></span>
            </div>
            <div class="card-body">
                <p class="small text-muted mb-2">
                    <i class="fas fa-file-video mr-1"></i><?= basename($video['original_path']) ?>
                    <?php if ($video['file_size']): ?>
                        (<?= number_format($video['file_size'] / 1048576, 1) ?> MB)
                    <?php endif; ?>
                </p>

                <!-- Progress bar -->
                <div id="progressWrap_<?= $video['id'] ?>" class="<?= $canProc ? 'd-none' : '' ?> mb-2">
                    <div class="progress" style="height:20px">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-info"
                             id="progressBar_<?= $video['id'] ?>" style="width:0%">0%</div>
                    </div>
                    <small class="text-muted" id="progressMsg_<?= $video['id'] ?>"></small>
                </div>

                <?php if ($video['status'] === 'error' && $video['error_message']): ?>
                    <div class="alert alert-danger py-1 small"><?= esc($video['error_message']) ?></div>
                <?php endif; ?>

                <div class="d-flex mt-2">
                    <?php if ($canProc): ?>
                    <button class="btn btn-primary btn-sm btn-process mr-2"
                            data-video-id="<?= $video['id'] ?>">
                        <i class="fas fa-play mr-1"></i>เริ่มวิเคราะห์
                    </button>
                    <?php elseif ($video['status'] === 'completed'): ?>
                    <a href="<?= $baseUrl ?>admin/analytics/analysis/heatmap/<?= $matchId ?>?half=<?= $video['half'] ?>"
                       class="btn btn-success btn-sm mr-1">
                        <i class="fas fa-fire"></i> Heatmap
                    </a>
                    <a href="<?= $baseUrl ?>admin/analytics/analysis/speed/<?= $matchId ?>?half=<?= $video['half'] ?>"
                       class="btn btn-info btn-sm">
                        <i class="fas fa-tachometer-alt"></i> Speed
                    </a>
                    <?php endif; ?>
                    <button class="btn btn-outline-secondary btn-sm btn-del-video ml-auto"
                            data-id="<?= $video['id'] ?>">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>

    <?php if (empty($videos)): ?>
    <div class="col-12">
        <div class="alert alert-warning">
            ยังไม่มีวิดีโอ
            <a href="<?= $baseUrl ?>admin/analytics/matches/manage/<?= $matchId ?>" class="alert-link">คลิกเพื่ออัปโหลด</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<!-- สถิติผู้เล่น -->
<?php if (!empty($playerStats)): ?>
<div class="card shadow mb-4">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">สถิติผู้เล่น</h6>
        <a href="<?= $baseUrl ?>admin/analytics/analysis/heatmap/<?= $matchId ?>" class="btn btn-sm btn-outline-primary">
            ดู Heatmap
        </a>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0">
                <thead class="bg-light">
                    <tr>
                        <th>#</th>
                        <th>ผู้เล่น</th>
                        <th>เบอร์</th>
                        <th>ระยะทาง (กม.)</th>
                        <th>เฉลี่ย (กม./ชม.)</th>
                        <th>สูงสุด (กม./ชม.)</th>
                        <th>Sprint</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($playerStats as $i => $ps): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?php if ($ps['player_name']): ?>
                                <a href="<?= $baseUrl ?>admin/analytics/report/player/<?= $ps['player_id'] ?>">
                                    <?= esc($ps['player_name']) ?> <?= esc($ps['player_surname']) ?>
                                </a>
                            <?php else: ?>
                                <span class="text-muted">เบอร์ <?= $ps['jersey_num'] ?></span>
                            <?php endif; ?>
                        </td>
                        <td><?= $ps['jersey_num'] ?></td>
                        <td class="text-center"><?= number_format($ps['total_distance'], 2) ?></td>
                        <td class="text-center"><?= number_format($ps['avg_speed'], 1) ?></td>
                        <td class="text-center"><?= number_format($ps['max_speed'], 1) ?></td>
                        <td class="text-center"><?= $ps['sprint_count'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<script>
var baseUrl   = '<?= $baseUrl ?>';
var csrfToken = '<?= csrf_hash() ?>';

// Process video
$(document).on('click', '.btn-process', function () {
    var btn     = $(this);
    var videoId = btn.data('video-id');

    btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin mr-1"></i>กำลังส่ง...');

    $.ajax({
        url: baseUrl + 'admin/analytics/matches/process/' + videoId,
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
        success: function (res) {
            if (res.status === 'success') {
                $('#statusBadge_' + videoId).removeClass().addClass('badge badge-info').text('กำลังประมวลผล');
                $('#progressWrap_' + videoId).removeClass('d-none');
                btn.remove();
                startPolling(videoId);
            } else {
                btn.prop('disabled', false).html('<i class="fas fa-play mr-1"></i>เริ่มวิเคราะห์');
                Swal.fire('ผิดพลาด', res.message, 'error');
            }
        },
        error: function () {
            btn.prop('disabled', false).html('<i class="fas fa-play mr-1"></i>เริ่มวิเคราะห์');
            Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อ server ได้', 'error');
        }
    });
});

// Polling task status
function startPolling(videoId) {
    var timer = setInterval(function () {
        $.ajax({
            url: baseUrl + 'admin/analytics/matches/check-status/' + videoId,
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            success: function (res) {
                var pct = res.progress || 0;
                var bar = $('#progressBar_' + videoId);
                bar.css('width', pct + '%').text(pct + '%');
                if (res.message) {
                    $('#progressMsg_' + videoId).text(res.message);
                }

                if (res.status === 'success') {
                    clearInterval(timer);
                    $('#statusBadge_' + videoId).removeClass().addClass('badge badge-success').text('เสร็จสิ้น');
                    bar.removeClass('progress-bar-animated').addClass('bg-success').css('width','100%').text('100%');
                    setTimeout(function(){ location.reload(); }, 1500);
                } else if (res.status === 'failure') {
                    clearInterval(timer);
                    $('#statusBadge_' + videoId).removeClass().addClass('badge badge-danger').text('ผิดพลาด');
                    bar.removeClass('progress-bar-animated').addClass('bg-danger');
                }
            }
        });
    }, 3000);
}

// Auto-poll กรณี processing อยู่แล้ว
<?php foreach ($videos as $video): ?>
<?php if (in_array($video['status'], ['queued', 'processing'])): ?>
$('#progressWrap_<?= $video['id'] ?>').removeClass('d-none');
startPolling(<?= $video['id'] ?>);
<?php endif; ?>
<?php endforeach; ?>

// Delete video
$(document).on('click', '.btn-del-video', function () {
    var btn = $(this);
    var id  = btn.data('id');
    Swal.fire({
        title: 'ลบวิดีโอ?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก',
    }).then(function (r) {
        if (r.isConfirmed) {
            $.ajax({
                url: baseUrl + 'admin/analytics/matches/video/delete/' + id,
                type: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
                success: function (res) {
                    if (res.status === 'success') {
                        location.reload();
                    }
                }
            });
        }
    });
});
</script>
