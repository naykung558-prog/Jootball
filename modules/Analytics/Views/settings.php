<?php $baseUrl = rtrim(base_url(), "/") . "/"; ?>

<div class="d-flex align-items-center mb-4">
    <h4 class="text-gray-800 mb-0"><i class="fas fa-cog mr-2"></i>ตั้งค่า Football Analytics</h4>
</div>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show">
        <?= session()->getFlashdata('success') ?>
        <button type="button" class="close" data-dismiss="alert">&times;</button>
    </div>
<?php endif; ?>

<!-- Python API Status -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">สถานะ Python CV Backend</h6>
    </div>
    <div class="card-body d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center" id="statusDisplay">
            <?php if ($pythonOnline): ?>
                <span class="badge badge-success badge-pill px-3 py-2 mr-3" id="statusBadge">
                    <i class="fas fa-circle mr-1" style="font-size:10px"></i>Online
                </span>
                <span class="text-success" id="statusText">Python backend พร้อมใช้งาน</span>
            <?php else: ?>
                <span class="badge badge-danger badge-pill px-3 py-2 mr-3" id="statusBadge">
                    <i class="fas fa-circle mr-1" style="font-size:10px"></i>Offline
                </span>
                <span class="text-danger" id="statusText">ไม่สามารถเชื่อมต่อ Python backend ได้ — ตรวจสอบ URL และ service</span>
            <?php endif; ?>
        </div>
        <button id="btnTestConn" class="btn btn-outline-info btn-sm">
            <i class="fas fa-plug mr-1"></i>Test Connection
        </button>
    </div>

</div>

<form action="<?= $baseUrl ?>admin/analytics/settings/save" method="POST">
    <?= csrf_field() ?>

    <div class="row">
        <!-- Python API -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">Python API Connection</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Python API URL</label>
                        <input type="url" name="python_api_url" class="form-control"
                               value="<?= esc($settings['python_api_url'] ?? 'http://localhost:8000') ?>">
                        <small class="text-muted">URL ของ FastAPI backend</small>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">Timeout (วินาที)</label>
                        <input type="number" name="python_api_timeout" class="form-control" min="5" max="300"
                               value="<?= esc($settings['python_api_timeout'] ?? 30) ?>">
                    </div>
                </div>
            </div>
        </div>

        <!-- CV Thresholds -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">CV Thresholds</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Confidence Threshold</label>
                        <input type="number" name="confidence_threshold" class="form-control" step="0.01" min="0.1" max="1.0"
                               value="<?= esc($settings['confidence_threshold'] ?? 0.5) ?>">
                        <small class="text-muted">ค่าขั้นต่ำ confidence สำหรับ YOLO detection (0.1-1.0)</small>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">IOU Threshold</label>
                        <input type="number" name="iou_threshold" class="form-control" step="0.01" min="0.1" max="1.0"
                               value="<?= esc($settings['iou_threshold'] ?? 0.45) ?>">
                        <small class="text-muted">IOU threshold สำหรับ NMS (0.1-1.0)</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Speed Zones -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Speed Zones</h6>
                </div>
                <div class="card-body">
                    <div class="form-group">
                        <label class="font-weight-bold">Sprint Threshold (กม./ชม.)</label>
                        <input type="number" name="sprint_threshold" class="form-control" min="15" max="40"
                               value="<?= esc($settings['sprint_threshold'] ?? 25) ?>">
                        <small class="text-muted">ความเร็วขั้นต่ำที่นับเป็น sprint</small>
                    </div>
                    <div class="alert alert-light py-2 small">
                        <strong>Speed Zones:</strong><br>
                        เดิน: &lt;7 | วิ่งช้า: 7-14 | วิ่ง: 14-21 | วิ่งเร็ว: 21-25 | Sprint: &gt;25 กม./ชม.
                    </div>
                </div>
            </div>
        </div>

        <!-- Pitch Dimensions -->
        <div class="col-md-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">ขนาดสนาม (เมตร)</h6>
                </div>
                <div class="card-body">
                    <div class="form-row">
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">ความกว้าง</label>
                                <input type="number" name="pitch_width" class="form-control" min="90" max="120"
                                       value="<?= esc($settings['pitch_width'] ?? 105) ?>">
                            </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label class="font-weight-bold">ความสูง (ลึก)</label>
                                <input type="number" name="pitch_height" class="form-control" min="45" max="90"
                                       value="<?= esc($settings['pitch_height'] ?? 68) ?>">
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="font-weight-bold">ขนาดไฟล์สูงสุด (MB)</label>
                        <input type="number" name="max_upload_size" class="form-control" min="100" max="2000"
                               value="<?= esc($settings['max_upload_size'] ?? 500) ?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-right">
        <button type="submit" class="btn btn-primary px-4">
            <i class="fas fa-save mr-1"></i>บันทึกการตั้งค่า
        </button>
    </div>
</form>

<script>
(function () {
    var baseUrl   = '<?= base_url() ?>';
    var csrfToken = '<?= csrf_hash() ?>';

    document.getElementById('btnTestConn').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>กำลังทดสอบ...';

        $.ajax({
            url: baseUrl + 'admin/analytics/settings/test-connection',
            type: 'POST',
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': csrfToken },
            success: function (res) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plug mr-1"></i>Test Connection';

                var badge = document.getElementById('statusBadge');
                var text  = document.getElementById('statusText');

                if (res.online) {
                    badge.className = 'badge badge-success badge-pill px-3 py-2 mr-3';
                    badge.innerHTML = '<i class="fas fa-circle mr-1" style="font-size:10px"></i>Online';
                    text.className  = 'text-success';
                    text.textContent = res.message;
                } else {
                    badge.className = 'badge badge-danger badge-pill px-3 py-2 mr-3';
                    badge.innerHTML = '<i class="fas fa-circle mr-1" style="font-size:10px"></i>Offline';
                    text.className  = 'text-danger';
                    text.textContent = res.message;
                }
            },
            error: function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-plug mr-1"></i>Test Connection';
                Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อ server ได้', 'error');
            }
        });
    });
}());
</script>
