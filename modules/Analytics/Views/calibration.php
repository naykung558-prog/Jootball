<?php
$baseUrl = rtrim(base_url(), "/") . "/";
$videoId  = $video['id'];
$matchId  = $match['id'];

// src_points ที่มีอยู่แล้ว (array of [x,y]) หรือ array ว่าง
$existingPoints = [];
if (!empty($existing['src_points'])) {
    $decoded = json_decode($existing['src_points'], true);
    if (is_array($decoded)) {
        $existingPoints = $decoded;
    }
}
$pitchW = isset($existing['pitch_w']) ? (int)$existing['pitch_w'] : 105;
$pitchH = isset($existing['pitch_h']) ? (int)$existing['pitch_h'] : 68;
?>

<!-- Header -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <a href="<?= $baseUrl ?>admin/analytics/matches/detail/<?= $matchId ?>"
           class="btn btn-outline-secondary btn-sm mb-2">
            <i class="fas fa-arrow-left mr-1"></i>กลับรายละเอียดแมตช์
        </a>
        <h4 class="text-gray-800 mb-1">
            <i class="fas fa-crosshairs mr-2"></i>Calibration สนาม
        </h4>
        <p class="text-muted small mb-0">
            <?= esc($match['match_name']) ?> &mdash;
            ครึ่งที่ <?= (int)$video['half'] ?>
        </p>
    </div>
    <div>
        <button id="btnAutoCal" class="btn btn-info mr-2">
            <i class="fas fa-magic mr-1"></i>Auto Calibrate
        </button>
        <button id="btnSave" class="btn btn-success">
            <i class="fas fa-save mr-1"></i>บันทึก Calibration
        </button>
    </div>
</div>

<div class="row">
    <!-- Canvas คลิกเลือก 4 จุด -->
    <div class="col-lg-8 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">
                    คลิกเลือก 4 มุมสนาม (TL → TR → BR → BL)
                </h6>
                <button id="btnClear" class="btn btn-outline-danger btn-sm">
                    <i class="fas fa-trash mr-1"></i>ล้างจุด
                </button>
            </div>
            <div class="card-body p-2 text-center" style="background:#111; position:relative;">
                <?php if (!empty($framePath)): ?>
                    <div style="position:relative; display:inline-block; max-width:100%;">
                        <img id="frameImg" src="<?= esc($framePath) ?>" alt="frame"
                             style="max-width:100%; display:block;" draggable="false">
                        <canvas id="calCanvas"
                                style="position:absolute; top:0; left:0; cursor:crosshair;"></canvas>
                    </div>
                <?php else: ?>
                    <div class="py-5 text-white">
                        <i class="fas fa-image fa-3x mb-3 d-block opacity-50"></i>
                        <p class="mb-0">ยังไม่มี frame ภาพจากวิดีโอ<br>
                        <small class="text-muted">ระบบจะดึง frame อัตโนมัติหลังประมวลผล</small></p>
                    </div>
                    <canvas id="calCanvas" width="640" height="360"
                            style="display:block; margin:0 auto; cursor:crosshair; background:#1a1a1a;"></canvas>
                <?php endif; ?>
            </div>
            <div class="card-footer">
                <small class="text-muted">
                    <i class="fas fa-info-circle mr-1"></i>
                    คลิกบนภาพเพื่อเลือกจุดมุมสนาม ตามลำดับ:
                    <span class="badge badge-primary">1</span> บนซ้าย
                    <span class="badge badge-success">2</span> บนขวา
                    <span class="badge badge-warning">3</span> ล่างขวา
                    <span class="badge badge-danger">4</span> ล่างซ้าย
                </small>
            </div>
        </div>
    </div>

    <!-- Panel ขวา -->
    <div class="col-lg-4 mb-4">
        <!-- จุดที่เลือก -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">จุดที่เลือก (src_points)</h6>
            </div>
            <div class="card-body p-0">
                <table class="table table-sm table-bordered mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th width="30">#</th>
                            <th>ตำแหน่ง</th>
                            <th>X</th>
                            <th>Y</th>
                        </tr>
                    </thead>
                    <tbody id="pointsTable">
                        <tr id="ptRow_0">
                            <td><span class="badge badge-primary">1</span></td>
                            <td class="small">บนซ้าย (TL)</td>
                            <td id="ptX_0">-</td>
                            <td id="ptY_0">-</td>
                        </tr>
                        <tr id="ptRow_1">
                            <td><span class="badge badge-success">2</span></td>
                            <td class="small">บนขวา (TR)</td>
                            <td id="ptX_1">-</td>
                            <td id="ptY_1">-</td>
                        </tr>
                        <tr id="ptRow_2">
                            <td><span class="badge badge-warning">3</span></td>
                            <td class="small">ล่างขวา (BR)</td>
                            <td id="ptX_2">-</td>
                            <td id="ptY_2">-</td>
                        </tr>
                        <tr id="ptRow_3">
                            <td><span class="badge badge-danger">4</span></td>
                            <td class="small">ล่างซ้าย (BL)</td>
                            <td id="ptX_3">-</td>
                            <td id="ptY_3">-</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Pitch Reference Diagram -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-success">แผนผังสนาม (จุด dst_points)</h6>
            </div>
            <div class="card-body p-2 text-center" style="background:#1a7a1a; border-radius:4px;">
                <svg viewBox="0 0 210 136" width="100%" style="max-width:280px; display:block; margin:0 auto;">
                    <!-- Pitch outline -->
                    <rect x="2" y="2" width="206" height="132" fill="none" stroke="white" stroke-width="1.5"/>
                    <!-- Centre line -->
                    <line x1="105" y1="2" x2="105" y2="134" stroke="white" stroke-width="1"/>
                    <!-- Centre circle -->
                    <circle cx="105" cy="68" r="18.3" fill="none" stroke="white" stroke-width="1"/>
                    <circle cx="105" cy="68" r="1.5" fill="white"/>
                    <!-- Left penalty area -->
                    <rect x="2" y="27.4" width="33" height="81.2" fill="none" stroke="white" stroke-width="1"/>
                    <!-- Right penalty area -->
                    <rect x="175" y="27.4" width="33" height="81.2" fill="none" stroke="white" stroke-width="1"/>
                    <!-- Left 6-yard -->
                    <rect x="2" y="49.3" width="11" height="37.4" fill="none" stroke="white" stroke-width="0.8"/>
                    <!-- Right 6-yard -->
                    <rect x="197" y="49.3" width="11" height="37.4" fill="none" stroke="white" stroke-width="0.8"/>
                    <!-- Corner markers (dst_points TL,TR,BR,BL = 0,0 / 105,0 / 105,68 / 0,68) -->
                    <circle cx="2" cy="2" r="4" fill="#4e73df" stroke="white" stroke-width="1"/>
                    <text x="6" y="10" fill="white" font-size="6" font-weight="bold">1 TL (0,0)</text>
                    <circle cx="208" cy="2" r="4" fill="#1cc88a" stroke="white" stroke-width="1"/>
                    <text x="150" y="10" fill="white" font-size="6" font-weight="bold">2 TR (105,0)</text>
                    <circle cx="208" cy="134" r="4" fill="#f6c23e" stroke="white" stroke-width="1"/>
                    <text x="144" y="132" fill="white" font-size="6" font-weight="bold">3 BR (105,68)</text>
                    <circle cx="2" cy="134" r="4" fill="#e74a3b" stroke="white" stroke-width="1"/>
                    <text x="6" y="132" fill="white" font-size="6" font-weight="bold">4 BL (0,68)</text>
                </svg>
                <p class="text-white small mt-1 mb-0" style="font-size:10px;">
                    ตัวเลขคือตำแหน่งบนสนามจริง (เมตร)
                </p>
            </div>
        </div>

        <!-- ขนาดสนาม -->
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ขนาดสนาม (เมตร)</h6>
            </div>
            <div class="card-body">
                <div class="form-group mb-3">
                    <label class="font-weight-bold small">ความกว้าง (ด้านยาว)</label>
                    <input type="number" id="pitchWidth" class="form-control form-control-sm"
                           value="<?= $pitchW ?>" min="90" max="120">
                </div>
                <div class="form-group mb-0">
                    <label class="font-weight-bold small">ความสูง (ด้านกว้าง)</label>
                    <input type="number" id="pitchHeight" class="form-control form-control-sm"
                           value="<?= $pitchH ?>" min="45" max="90">
                </div>
            </div>
        </div>

        <!-- สถานะ calibration ปัจจุบัน -->
        <?php if (!empty($existing)): ?>
        <div class="card shadow border-left-success">
            <div class="card-body py-3">
                <p class="small font-weight-bold text-success mb-1">
                    <i class="fas fa-check-circle mr-1"></i>มีข้อมูล Calibration อยู่แล้ว
                </p>
                <p class="small text-muted mb-0">
                    อัปเดตล่าสุด:
                    <?= !empty($existing['updated_at'])
                        ? date('d/m/Y H:i', strtotime($existing['updated_at']))
                        : date('d/m/Y H:i', strtotime($existing['created_at'])) ?>
                </p>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<script>
(function () {
    var baseUrl   = '<?= $baseUrl ?>';
    var videoId   = <?= (int)$videoId ?>;
    var csrfToken = '<?= csrf_hash() ?>';

    var pointColors = ['#4e73df', '#1cc88a', '#f6c23e', '#e74a3b'];
    var points      = [];  // [{x, y}, ...]

    // โหลดจุดที่มีอยู่แล้ว
    var existingPts = <?= json_encode($existingPoints) ?>;
    if (existingPts && existingPts.length) {
        points = existingPts.slice(0, 4);
    }

    // ---- Canvas setup ----
    var canvas = document.getElementById('calCanvas');
    var ctx    = canvas.getContext('2d');
    var img    = document.getElementById('frameImg');

    function resizeCanvas() {
        if (img) {
            canvas.width  = img.offsetWidth;
            canvas.height = img.offsetHeight;
        }
        drawAll();
    }

    function drawAll() {
        ctx.clearRect(0, 0, canvas.width, canvas.height);

        // วาดเส้นเชื่อมจุด
        if (points.length >= 2) {
            ctx.beginPath();
            ctx.strokeStyle = 'rgba(255,255,255,0.5)';
            ctx.lineWidth   = 1.5;
            ctx.setLineDash([5, 4]);
            ctx.moveTo(points[0].x, points[0].y);
            for (var i = 1; i < points.length; i++) {
                ctx.lineTo(points[i].x, points[i].y);
            }
            if (points.length === 4) {
                ctx.lineTo(points[0].x, points[0].y);
            }
            ctx.stroke();
            ctx.setLineDash([]);
        }

        // วาดจุด
        for (var j = 0; j < points.length; j++) {
            ctx.beginPath();
            ctx.arc(points[j].x, points[j].y, 7, 0, Math.PI * 2);
            ctx.fillStyle   = pointColors[j];
            ctx.fill();
            ctx.strokeStyle = '#fff';
            ctx.lineWidth   = 2;
            ctx.stroke();

            // เลข
            ctx.fillStyle  = '#fff';
            ctx.font       = 'bold 10px Arial';
            ctx.textAlign  = 'center';
            ctx.textBaseline = 'middle';
            ctx.fillText(j + 1, points[j].x, points[j].y);
        }
    }

    function updateTable() {
        for (var k = 0; k < 4; k++) {
            if (points[k]) {
                document.getElementById('ptX_' + k).textContent = Math.round(points[k].x);
                document.getElementById('ptY_' + k).textContent = Math.round(points[k].y);
            } else {
                document.getElementById('ptX_' + k).textContent = '-';
                document.getElementById('ptY_' + k).textContent = '-';
            }
        }
    }

    // คลิก canvas
    canvas.addEventListener('click', function (e) {
        if (points.length >= 4) {
            Swal.fire({
                title: 'ครบ 4 จุดแล้ว',
                text: 'ต้องการล้างจุดเดิมและเริ่มใหม่ไหม?',
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'ล้างแล้วเลือกใหม่',
                cancelButtonText: 'ยกเลิก',
            }).then(function (r) {
                if (r.isConfirmed) {
                    points = [];
                    addPoint(e);
                }
            });
            return;
        }
        addPoint(e);
    });

    function addPoint(e) {
        var rect = canvas.getBoundingClientRect();
        var x    = e.clientX - rect.left;
        var y    = e.clientY - rect.top;
        points.push({ x: x, y: y });
        drawAll();
        updateTable();
    }

    // ล้างจุด
    document.getElementById('btnClear').addEventListener('click', function () {
        Swal.fire({
            title: 'ล้างจุดทั้งหมด?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'ล้าง',
            cancelButtonText: 'ยกเลิก',
        }).then(function (r) {
            if (r.isConfirmed) {
                points = [];
                drawAll();
                updateTable();
            }
        });
    });

    // Auto Calibrate
    document.getElementById('btnAutoCal').addEventListener('click', function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-1"></i>กำลัง Auto Calibrate...';

        $.ajax({
            url: baseUrl + 'admin/analytics/settings/auto-calibrate/' + videoId,
            type: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken,
            },
            success: function (res) {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Auto Calibrate';

                if (res.status === 'success' && res.src_points && res.src_points.length === 4) {
                    // แปลงพิกัดให้ตรงกับขนาด canvas ปัจจุบัน
                    var scaleX = canvas.width  / (res.frame_width  || canvas.width);
                    var scaleY = canvas.height / (res.frame_height || canvas.height);
                    points = [];
                    for (var i = 0; i < 4; i++) {
                        points.push({
                            x: res.src_points[i][0] * scaleX,
                            y: res.src_points[i][1] * scaleY,
                        });
                    }
                    drawAll();
                    updateTable();
                    Swal.fire('Auto Calibrate สำเร็จ', 'ตรวจสอบจุดแล้วกด "บันทึก"', 'success');
                } else {
                    Swal.fire('ไม่สามารถ Auto Calibrate', res.message || 'ลองใหม่อีกครั้ง', 'error');
                }
            },
            error: function () {
                btn.disabled = false;
                btn.innerHTML = '<i class="fas fa-magic mr-1"></i>Auto Calibrate';
                Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อ server ได้', 'error');
            }
        });
    });

    // Save Calibration
    document.getElementById('btnSave').addEventListener('click', function () {
        if (points.length < 4) {
            Swal.fire('ยังไม่ครบ', 'กรุณาเลือก 4 จุดมุมสนามให้ครบ', 'warning');
            return;
        }

        var pitchW = parseInt(document.getElementById('pitchWidth').value)  || 105;
        var pitchH = parseInt(document.getElementById('pitchHeight').value) || 68;

        // แปลงพิกัดกลับเป็น pixel บน frame ต้นฉบับ
        var rawPoints = [];
        for (var i = 0; i < 4; i++) {
            rawPoints.push([Math.round(points[i].x), Math.round(points[i].y)]);
        }

        Swal.fire({
            title: 'ยืนยันบันทึก Calibration?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'บันทึก',
            cancelButtonText: 'ยกเลิก',
        }).then(function (r) {
            if (!r.isConfirmed) { return; }

            $.ajax({
                url: baseUrl + 'admin/analytics/settings/save-calibration',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    video_id:     videoId,
                    src_points:   rawPoints,
                    pitch_width:  pitchW,
                    pitch_height: pitchH,
                }),
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                },
                success: function (res) {
                    if (res.status === 'success') {
                        Swal.fire('บันทึกสำเร็จ', 'Calibration ถูกบันทึกแล้ว', 'success');
                    } else {
                        Swal.fire('ผิดพลาด', res.message || 'ไม่สามารถบันทึกได้', 'error');
                    }
                },
                error: function () {
                    Swal.fire('ผิดพลาด', 'ไม่สามารถเชื่อมต่อ server ได้', 'error');
                }
            });
        });
    });

    // Init
    if (img) {
        if (img.complete) {
            resizeCanvas();
        } else {
            img.addEventListener('load', resizeCanvas);
        }
        window.addEventListener('resize', resizeCanvas);
    } else {
        drawAll();
        updateTable();
    }

    // โหลดจุดเดิม (ถ้ามี)
    if (points.length) {
        drawAll();
        updateTable();
    }
}());
</script>
