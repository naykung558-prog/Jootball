<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<style>
    /* Styling for Filter Buttons */
    .btn-filter {
        background-color: #fff;
        border: 1px solid #dee2e6;
        color: #636e72;
        transition: all 0.2s;
    }
    .btn-filter.active {
        background-color: #0d6efd;
        border-color: #0d6efd;
        color: #fff;
        box-shadow: 0 4px 10px rgba(13, 110, 253, 0.3);
    }

    /* Card & Table Customization */
    .stat-icon { width: 50px; height: 50px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 1.4rem; }
    .table thead th { background-color: #f8f9fa; color: #636e72; font-weight: 600; font-size: 0.85rem; border: none; }
    .badge-method { font-weight: 600; padding: 4px 8px; border-radius: 6px; font-size: 0.7rem; }
</style>

<div class="container-fluid py-4 px-lg-4">
    <div class="row align-items-center mb-4">
        <div class="col-md-7">
            <h4 class="fw-bold text-dark mb-1">รายงานการเข้าใช้งาน (Logs Login)</h4>
            <p class="text-muted small mb-0">ข้อมูลประจำวันที่ <span id="currentDateThai" class="fw-bold text-primary">...</span></p>
        </div>
        <div class="col-md-5 text-md-end">
            <div class="btn-group shadow-sm" id="timeFilter">
                <button type="button" class="btn btn-sm btn-filter" data-range="day">รายวัน</button>
                <button type="button" class="btn btn-sm btn-filter" data-range="week">รายสัปดาห์</button>
                <button type="button" class="btn btn-sm btn-filter active" data-range="month">รายเดือน</button>
            </div>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-primary-subtle text-primary me-3"><i class="ti ti-door-open"></i></div>
                    <div>
                        <small class="text-muted d-block">จำนวน Log ทั้งหมด</small>
                        <h4 class="fw-bold mb-0"><?= number_format($total_logs) ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-success-subtle text-success me-3"><i class="ti ti-users"></i></div>
                    <div>
                        <small class="text-muted d-block">ผู้ใช้งานไม่ซ้ำหน้า</small>
                        <h4 class="fw-bold mb-0"><?= number_format($unique_users) ?></h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-info-subtle text-info me-3"><i class="ti ti-device-laptop"></i></div>
                    <div>
                        <small class="text-muted d-block">ผ่านหน้าเว็บ</small>
                        <h4 class="fw-bold mb-0"><?= $web_percent ?>%</h4>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm p-3 border-start border-danger border-4">
                <div class="d-flex align-items-center">
                    <div class="stat-icon bg-danger-subtle text-danger me-3"><i class="ti ti-shield-alert"></i></div>
                    <div>
                        <small class="text-muted d-block">พบความผิดปกติ</small>
                        <h4 class="fw-bold mb-0 text-danger"><?= number_format($anomaly_count) ?></h4>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row g-4 mb-4">
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm p-4 h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h6 class="fw-bold mb-0">แนวโน้มการเข้าใช้งาน (<span id="chartLabel">รายเดือน</span>)</h6>
                    <i class="ti ti-settings text-muted"></i>
                </div>
                <div style="height: 300px;"><canvas id="mainChart"></canvas></div>
            </div>
        </div>
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm p-4 h-100 text-center">
                <h6 class="fw-bold mb-4">ช่องทางการใช้งาน</h6>
                <div style="height: 200px; margin-bottom: 20px;"><canvas id="doughnutChart"></canvas></div>
                <div class="text-start mt-3">
                    <small class="text-muted">แยกตามประเภทการเชื่อมต่อ</small>
                    <div class="d-flex justify-content-between mt-2"><span>Web Browser</span><span class="fw-bold text-primary"><?= $web_percent ?>%</span></div>
                    <div class="d-flex justify-content-between"><span>API / Mobile</span><span class="fw-bold text-info"><?= 100 - $web_percent ?>%</span></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Logs Table -->
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white border-bottom py-3 d-flex justify-content-between align-items-center">
            <h6 class="fw-bold mb-0">รายการกิจกรรมล่าสุด</h6>
            <button class="btn btn-sm btn-light border" onclick="reloadTable()">
                <i class="ti ti-refresh me-1"></i> รีเฟรช
            </button>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive p-3">
                <table class="table table-hover align-middle mb-0" id="logTable" style="width:100%">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>ผู้ใช้งาน</th>
                            <th>IP Address</th>
                            <th>Method</th>
                            <th>URL</th>
                            <th>วันเวลา</th>
                            <th>จัดการ</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Log Detail Modal -->
<div class="modal fade" id="logDetailModal" tabindex="-1" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg">
            <div class="modal-header bg-light py-3">
                <h5 class="modal-title fw-bold fs-5"><i class="ti ti-file-description me-2"></i>รายละเอียดกิจกรรม (Log)</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <div class="col-md-6 border-end">
                        <label class="fw-bold small text-muted text-uppercase letter-spacing-1">ข้อมูลผู้ใช้งาน:</label>
                        <p id="detailUser" class="mb-0 fw-bold fs-6 mt-1 text-dark">-</p>
                    </div>
                    <div class="col-md-6">
                        <label class="fw-bold small text-muted text-uppercase letter-spacing-1">URL ปลายทาง:</label>
                        <p id="detailUrl" class="mb-0 fw-bold fs-6 mt-1 text-primary break-all">-</p>
                    </div>
                    <div class="col-12 mt-4 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold small text-muted text-uppercase">Post Data (JSON):</label>
                            <span class="badge bg-light text-dark border">Data Content</span>
                        </div>
                        <pre id="detailPostData" class="bg-dark text-light p-3 rounded-3 mt-1 shadow-inner" style="max-height: 250px; overflow-y: auto; font-family: 'Consolas', monospace; font-size: 0.8rem; line-height: 1.5;"></pre>
                    </div>
                    <div class="col-12 mt-4 pt-2 border-top">
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <label class="fw-bold small text-muted text-uppercase">Response Data:</label>
                            <span class="badge bg-light text-dark border">Output Response</span>
                        </div>
                        <pre id="detailResponseData" class="bg-dark text-light p-3 rounded-3 mt-1 shadow-inner" style="max-height: 250px; overflow-y: auto; font-family: 'Consolas', monospace; font-size: 0.8rem; line-height: 1.5;"></pre>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-top bg-light py-3">
                <button type="button" class="btn btn-secondary px-4 fw-bold" data-bs-dismiss="modal">ปิดหน้าต่าง</button>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<!-- Use local datatable scripts if available, or CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    let logTable;

    $(document).ready(function() {
        // --- Thai Date Initialization ---
        const thaiMonths = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"];
        const d = new Date();
        const thaiDate = `${d.getDate()} ${thaiMonths[d.getMonth()]} ${d.getFullYear() + 543}`;
        $('#currentDateThai').text(thaiDate);

        // --- Filter Buttons Logic ---
        $('.btn-filter').on('click', function() {
            $('.btn-filter').removeClass('active');
            $(this).addClass('active');
            const range = $(this).data('range');

            // อัปเดตข้อความในกราฟ
            const labelMap = { 'day': 'รายวัน', 'week': 'รายสัปดาห์', 'month': 'รายเดือน' };
            $('#chartLabel').text(labelMap[range]);

            // เปลี่ยนชุดข้อมูลกราฟตามช่วงเวลา
            if (range === 'day') {
                myChart.data.labels = ['08:00', '10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'];
                myChart.data.datasets[0].data = [15, 45, 120, 80, 60, 150, 200, 50];
            } else if (range === 'week') {
                myChart.data.labels = ['จ.', 'อ.', 'พ.', 'พฤ.', 'ศ.', 'ส.', 'อา.'];
                myChart.data.datasets[0].data = [800, 950, 1100, 1050, 1300, 1800, 1600];
            } else if (range === 'month') {
                myChart.data.labels = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.'];
                myChart.data.datasets[0].data = [420, 580, 1100, 890, 950, 1400, 1250, 1500];
            }

            // สั่งให้กราฟวาดใหม่
            myChart.update();

            // รีเฟรชตาราง
            reloadTable();
        });

        // --- Charts Initialization ---
        const webPercent = <?= $web_percent ?>;
        const apiPercent = 100 - webPercent;

        // Line Chart
        const mainCtx = document.getElementById('mainChart').getContext('2d');
        let gradient = mainCtx.createLinearGradient(0, 0, 0, 400);
        gradient.addColorStop(0, 'rgba(13, 110, 253, 0.4)');
        gradient.addColorStop(1, 'rgba(13, 110, 253, 0)');

        const myChart = new Chart(mainCtx, {
            type: 'line',
            data: {
                labels: ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.'],
                datasets: [{
                    label: 'จำนวนครั้ง',
                    data: [420, 580, 1100, 890, 950, 1400, 1250, 1500],
                    borderColor: '#0d6efd',
                    borderWidth: 3,
                    tension: 0.4,
                    fill: true,
                    backgroundColor: gradient,
                    pointBackgroundColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(0,0,0,0.05)' } },
                    x: { grid: { display: false } }
                }
            }
        });

        // Doughnut Chart
        const doughnutCtx = document.getElementById('doughnutChart').getContext('2d');
        new Chart(doughnutCtx, {
            type: 'doughnut',
            data: {
                labels: ['Web', 'API/Mobile'],
                datasets: [{
                    data: [webPercent, apiPercent],
                    backgroundColor: ['#0d6efd', '#0dcaf0'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, padding: 20 } } }
            }
        });

        // --- DataTable Initialization ---
        logTable = $('#logTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= base_url('admin/report/get-logs') ?>",
                type: 'POST'
            },
            columns: [
                { data: 'no', name: 'no', orderable: false, searchable: false, className: 'text-muted' },
                { data: 'user_display', name: 'member.username', className: 'fw-bold' },
                { data: 'ip_address', name: 'logs_login.ip_address' },
                {
                    data: 'method',
                    name: 'logs_login.method',
                    render: function(data) {
                        const cls = data === 'POST' ? 'bg-primary-subtle text-primary' : 'bg-success-subtle text-success';
                        return `<span class="badge-method ${cls}">${data}</span>`;
                    }
                },
                { data: 'url', name: 'logs_login.url' },
                { data: 'created_at', name: 'logs_login.created_at' },
                { data: 'action', orderable: false, searchable: false, className: 'text-center' }
            ],
            order: [[5, 'desc']],
            language: {
                url: "//cdn.datatables.net/plug-ins/1.13.7/i18n/th.json"
            }
        });

        // View Details Event
        $(document).on('click', '.btn-view-log', function() {
            const id = $(this).data('id');
            const row = logTable.row($(this).closest('tr')).data();

            $('#detailUser').text(row.user_display);
            $('#detailUrl').text(row.url);

            // Format JSON for better reading
            try {
                const postData = JSON.parse(row.post_data);
                $('#detailPostData').text(JSON.stringify(postData, null, 2));
            } catch(e) {
                $('#detailPostData').text(row.post_data || '-');
            }

            try {
                const responseData = JSON.parse(row.response_data);
                $('#detailResponseData').text(JSON.stringify(responseData, null, 2));
            } catch(e) {
                $('#detailResponseData').text(row.response_data || '-');
            }

            $('#logDetailModal').modal('show');
        });
    });

    function reloadTable() {
        logTable.ajax.reload();
    }
</script>
<?php $this->endSection(); ?>
