<?php $baseUrl = rtrim(base_url(), "/") . "/"; ?>

<div class="d-flex align-items-center mb-4">
    <h5 class="text-gray-800 mb-0">
        <i class="fas fa-balance-scale me-2"></i>เปรียบเทียบผู้เล่น
    </h5>
</div>

<!-- Selection Form -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 fw-bold text-primary">เลือกผู้เล่นที่ต้องการเปรียบเทียบ</h6>
    </div>
    <div class="card-body">
        <div class="row align-items-end g-2">
            <div class="col-md-5">
                <label class="fw-bold mb-1">ผู้เล่นคนที่ 1</label>
                <select id="player1" class="form-select">
                    <option value="">-- เลือกผู้เล่น --</option>
                    <?php foreach ($playersWithStats as $p): ?>
                        <option value="<?= (int)$p['id'] ?>">
                            <?= esc($p['player_name'] . ' ' . $p['player_surname']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 text-center pb-1">
                <span class="fw-bold text-muted fs-5">vs</span>
            </div>
            <div class="col-md-5">
                <label class="fw-bold mb-1">ผู้เล่นคนที่ 2</label>
                <select id="player2" class="form-select">
                    <option value="">-- เลือกผู้เล่น --</option>
                    <?php foreach ($playersWithStats as $p): ?>
                        <option value="<?= (int)$p['id'] ?>">
                            <?= esc($p['player_name'] . ' ' . $p['player_surname']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1 d-grid">
                <button id="btnCompare" class="btn btn-primary">
                    <i class="fas fa-search me-1"></i>เปรียบ
                </button>
            </div>
        </div>
        <div class="row mt-2 g-2">
            <div class="col-md-5">
                <label class="fw-bold mb-1 text-muted small">กรองตาม Match (ไม่บังคับ — ใช้เฉพาะ stats-json ที่รองรับ)</label>
                <select id="matchFilter" class="form-select form-select-sm">
                    <option value="">-- สถิติทุก Match รวมกัน --</option>
                    <?php if (!empty($matches)): ?>
                        <?php foreach ($matches as $m): ?>
                        <option value="<?= (int)$m['id'] ?>"><?= esc($m['match_name']) ?> (<?= date('d/m/Y', strtotime($m['match_date'])) ?>)</option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
        </div>
    </div>
</div>

<!-- Loading spinner -->
<div id="compareLoading" class="d-none text-center py-5">
    <div class="spinner-border text-primary" style="width:3rem;height:3rem"></div>
    <p class="mt-3 text-muted">กำลังโหลดข้อมูล...</p>
</div>

<!-- Compare Result (hidden until loaded) -->
<div id="compareResult" class="d-none">

    <!-- Side-by-side Stat Cards -->
    <div class="row mb-4" id="statCards"></div>

    <!-- Charts Row 1: Radar + Speed Zone Bar -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-primary">
                        <i class="fas fa-chart-radar me-1"></i>Radar / Spider Chart
                    </h6>
                </div>
                <div class="card-body">
                    <div id="radarChart" style="min-height:340px"></div>
                </div>
            </div>
        </div>
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 fw-bold text-warning">
                        <i class="fas fa-layer-group me-1"></i>Speed Zone Comparison (%)
                    </h6>
                </div>
                <div class="card-body">
                    <div id="zoneBarChart" style="min-height:340px"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Comparison Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 fw-bold text-success"><i class="fas fa-table me-1"></i>ตารางสถิติเปรียบเทียบ</h6>
        </div>
        <div class="card-body p-0">
            <table class="table table-bordered mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>สถิติ</th>
                        <th class="text-center" id="p1Header" style="color:#2196f3">ผู้เล่น 1</th>
                        <th class="text-center" id="p2Header" style="color:#f44336">ผู้เล่น 2</th>
                    </tr>
                </thead>
                <tbody id="compareTable"></tbody>
            </table>
        </div>
    </div>
</div>

<script>
var baseUrl   = '<?= $baseUrl ?>';
var hcRadar   = null;
var hcZone    = null;
var COLOR1    = '#2196f3';
var COLOR2    = '#f44336';

$('#btnCompare').on('click', function() {
    var p1 = $('#player1').val();
    var p2 = $('#player2').val();

    if (!p1 || !p2) {
        Swal.fire('แจ้งเตือน', 'กรุณาเลือกผู้เล่นทั้ง 2 คน', 'warning');
        return;
    }
    if (p1 === p2) {
        Swal.fire('แจ้งเตือน', 'กรุณาเลือกผู้เล่นคนละคน', 'warning');
        return;
    }

    $('#compareResult').addClass('d-none');
    $('#compareLoading').removeClass('d-none');

    var name1 = $('#player1 option:selected').text().trim();
    var name2 = $('#player2 option:selected').text().trim();

    Promise.all([
        $.ajax({
            url: baseUrl + 'admin/analytics/players/stats-json/' + p1,
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataType: 'json'
        }),
        $.ajax({
            url: baseUrl + 'admin/analytics/players/stats-json/' + p2,
            type: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' },
            dataType: 'json'
        })
    ]).then(function(results) {
        $('#compareLoading').addClass('d-none');
        var stat1 = (results[0] && results[0].data) ? results[0].data : {};
        var stat2 = (results[1] && results[1].data) ? results[1].data : {};
        renderCompare(stat1, stat2, name1, name2);
        $('#compareResult').removeClass('d-none');
    }).catch(function() {
        $('#compareLoading').addClass('d-none');
        Swal.fire('ผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้', 'error');
    });
});

function fv(v, decimals) {
    return parseFloat(v || 0).toFixed(decimals || 0);
}
function fi(v) { return parseInt(v || 0); }

function renderCompare(s1, s2, name1, name2) {
    $('#p1Header').text(name1);
    $('#p2Header').text(name2);

    // --- Side-by-side Stat Cards ---
    var d1 = parseFloat(s1.total_distance || 0),  d2 = parseFloat(s2.total_distance || 0);
    var a1 = parseFloat(s1.avg_speed || 0),        a2 = parseFloat(s2.avg_speed || 0);
    var m1 = parseFloat(s1.max_speed || 0),        m2 = parseFloat(s2.max_speed || 0);
    var sp1 = parseInt(s1.total_sprints || 0),     sp2 = parseInt(s2.total_sprints || 0);
    var mc1 = parseInt(s1.match_count || 0),       mc2 = parseInt(s2.match_count || 0);

    function cardBg(v1, v2) {
        if (v1 > v2) return 'bg-success bg-opacity-10 border-success';
        if (v1 < v2) return 'bg-danger  bg-opacity-10 border-danger';
        return 'bg-light border-secondary';
    }

    var cardHtml = '<div class="col-12"><div class="row g-3 justify-content-center">';
    var metrics = [
        { label: 'ระยะทางรวม', v1: d1.toFixed(2),  v2: d2.toFixed(2),  rv1: d1,  rv2: d2,  unit: 'กม.' },
        { label: 'ความเร็วเฉลี่ย', v1: a1.toFixed(1), v2: a2.toFixed(1), rv1: a1, rv2: a2, unit: 'กม./ชม.' },
        { label: 'ความเร็วสูงสุด', v1: m1.toFixed(1), v2: m2.toFixed(1), rv1: m1, rv2: m2, unit: 'กม./ชม.' },
        { label: 'Sprint',     v1: sp1,            v2: sp2,            rv1: sp1, rv2: sp2, unit: 'ครั้ง' },
        { label: 'จำนวนแมตช์', v1: mc1,           v2: mc2,            rv1: mc1, rv2: mc2, unit: 'แมตช์' }
    ];

    metrics.forEach(function(m) {
        var bg1 = cardBg(m.rv1, m.rv2);
        var bg2 = cardBg(m.rv2, m.rv1);
        cardHtml += '<div class="col-6 col-md-2">';
        cardHtml +=   '<div class="card border text-center p-2 ' + bg1 + ' h-100">';
        cardHtml +=     '<div class="small text-muted mb-1">' + m.label + '</div>';
        cardHtml +=     '<div class="fw-bold fs-5" style="color:' + COLOR1 + '">' + m.v1 + '</div>';
        cardHtml +=     '<div class="text-muted" style="font-size:11px">' + m.unit + '</div>';
        cardHtml +=     '<div class="mt-1" style="font-size:10px;color:#666">' + name1 + '</div>';
        cardHtml +=   '</div></div>';
        cardHtml += '<div class="col-6 col-md-2">';
        cardHtml +=   '<div class="card border text-center p-2 ' + bg2 + ' h-100">';
        cardHtml +=     '<div class="small text-muted mb-1">' + m.label + '</div>';
        cardHtml +=     '<div class="fw-bold fs-5" style="color:' + COLOR2 + '">' + m.v2 + '</div>';
        cardHtml +=     '<div class="text-muted" style="font-size:11px">' + m.unit + '</div>';
        cardHtml +=     '<div class="mt-1" style="font-size:10px;color:#666">' + name2 + '</div>';
        cardHtml +=   '</div></div>';
    });
    cardHtml += '</div></div>';
    $('#statCards').html(cardHtml);

    // --- Comparison Table ---
    var rows = [
        ['ระยะทางรวม (กม.)',      d1.toFixed(2),  d2.toFixed(2),  d1,  d2],
        ['ความเร็วเฉลี่ย (กม./ชม.)', a1.toFixed(1), a2.toFixed(1), a1, a2],
        ['ความเร็วสูงสุด (กม./ชม.)', m1.toFixed(1), m2.toFixed(1), m1, m2],
        ['Sprint (ครั้ง)',        sp1,            sp2,            sp1, sp2],
        ['จำนวนแมตช์',            mc1,            mc2,            mc1, mc2]
    ];
    var tbHtml = '';
    rows.forEach(function(r) {
        var rv1 = r[3], rv2 = r[4];
        var c1 = rv1 > rv2 ? 'table-success' : (rv1 < rv2 ? 'table-danger' : '');
        var c2 = rv2 > rv1 ? 'table-success' : (rv2 < rv1 ? 'table-danger' : '');
        var icon1 = rv1 > rv2 ? ' <i class="fas fa-arrow-up text-success small"></i>' : (rv1 < rv2 ? ' <i class="fas fa-arrow-down text-danger small"></i>' : '');
        var icon2 = rv2 > rv1 ? ' <i class="fas fa-arrow-up text-success small"></i>' : (rv2 < rv1 ? ' <i class="fas fa-arrow-down text-danger small"></i>' : '');
        tbHtml += '<tr><td class="fw-bold">' + r[0] + '</td>';
        tbHtml +=   '<td class="text-center ' + c1 + '">' + r[1] + icon1 + '</td>';
        tbHtml +=   '<td class="text-center ' + c2 + '">' + r[2] + icon2 + '</td></tr>';
    });
    $('#compareTable').html(tbHtml);

    // --- Radar Chart (Highcharts) ---
    // Normalize values for radar (percentage of combined max)
    function normalizeRadar(v1, v2) {
        var mx = Math.max(v1, v2, 0.001);
        return [Math.round(v1/mx*100), Math.round(v2/mx*100)];
    }
    var nDist  = normalizeRadar(d1, d2);
    var nAvg   = normalizeRadar(a1, a2);
    var nMax   = normalizeRadar(m1, m2);
    var nSpr   = normalizeRadar(sp1, sp2);
    var nMat   = normalizeRadar(mc1, mc2);

    if (hcRadar) { hcRadar.destroy(); }
    hcRadar = Highcharts.chart('radarChart', {
        chart: {
            polar: true,
            type: 'line',
            style: { fontFamily: 'Kanit, sans-serif' }
        },
        title: { text: null },
        pane: { size: '80%' },
        xAxis: {
            categories: ['ระยะทาง', 'เฉลี่ยสปีด', 'MaxSpeed', 'Sprint', 'แมตช์'],
            tickmarkPlacement: 'on',
            lineWidth: 0
        },
        yAxis: {
            gridLineInterpolation: 'polygon',
            lineWidth: 0,
            min: 0,
            max: 100,
            labels: { enabled: false }
        },
        tooltip: {
            shared: true,
            pointFormat: '<span style="color:{series.color}">{series.name}: <b>{point.y}%</b><br/>'
        },
        legend: {
            align: 'center',
            verticalAlign: 'bottom',
            itemStyle: { fontFamily: 'Kanit, sans-serif', fontSize: '12px' }
        },
        series: [
            {
                name: name1,
                data: [nDist[0], nAvg[0], nMax[0], nSpr[0], nMat[0]],
                pointPlacement: 'on',
                color: COLOR1
            },
            {
                name: name2,
                data: [nDist[1], nAvg[1], nMax[1], nSpr[1], nMat[1]],
                pointPlacement: 'on',
                color: COLOR2
            }
        ],
        credits: { enabled: false }
    });

    // --- Speed Zone Grouped Bar Chart (Highcharts) ---
    var zoneLabels = ['Walk', 'Jog', 'Run', 'Fast', 'Sprint'];
    var zColors    = ['#9e9e9e', '#36a2eb', '#4bc0c0', '#ff9f40', '#ff6384'];

    // build zone data arrays
    function zoneVal(s, key) { return Math.round(parseFloat(s[key] || 0) * 10) / 10; }
    var zoneSeries = [
        { name: 'Walk',   y1: zoneVal(s1,'zone_walk'),   y2: zoneVal(s2,'zone_walk'),   color: '#9e9e9e' },
        { name: 'Jog',    y1: zoneVal(s1,'zone_jog'),    y2: zoneVal(s2,'zone_jog'),    color: '#36a2eb' },
        { name: 'Run',    y1: zoneVal(s1,'zone_run'),    y2: zoneVal(s2,'zone_run'),    color: '#4bc0c0' },
        { name: 'Fast',   y1: zoneVal(s1,'zone_fast'),   y2: zoneVal(s2,'zone_fast'),   color: '#ff9f40' },
        { name: 'Sprint', y1: zoneVal(s1,'zone_sprint'), y2: zoneVal(s2,'zone_sprint'), color: '#ff6384' }
    ];

    // Two series per zone as grouped bars
    var zoneSer1Data = zoneSeries.map(function(z){ return { y: z.y1, color: z.color }; });
    var zoneSer2Data = zoneSeries.map(function(z){ return { y: z.y2, color: z.color, opacity: 0.5 }; });

    if (hcZone) { hcZone.destroy(); }
    hcZone = Highcharts.chart('zoneBarChart', {
        chart: { type: 'bar', style: { fontFamily: 'Kanit, sans-serif' } },
        title: { text: null },
        xAxis: {
            categories: zoneLabels,
            labels: { style: { fontSize: '12px' } }
        },
        yAxis: {
            min: 0,
            title: { text: 'เปอร์เซ็นต์ (%)' },
            labels: { format: '{value}%' }
        },
        tooltip: {
            formatter: function() {
                return '<b>' + this.x + '</b><br/>' + this.series.name + ': <b>' + this.y + '%</b>';
            }
        },
        legend: {
            itemStyle: { fontFamily: 'Kanit, sans-serif', fontSize: '12px' }
        },
        plotOptions: {
            bar: { grouping: true, borderWidth: 0, dataLabels: { enabled: true, format: '{point.y:.1f}%', style: { fontSize: '10px', fontWeight: 'normal' } } }
        },
        series: [
            { name: name1, data: zoneSer1Data, color: COLOR1 },
            { name: name2, data: zoneSer2Data, color: COLOR2 }
        ],
        credits: { enabled: false }
    });
}
</script>
