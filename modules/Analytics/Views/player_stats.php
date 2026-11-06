<?php $baseUrl = rtrim(base_url(), "/") . "/"; ?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="text-gray-800 mb-0"><i class="fas fa-running mr-2"></i>สถิติผู้เล่น</h5>
    <a href="<?= $baseUrl ?>admin/analytics/analysis/compare" class="btn btn-outline-primary btn-sm">
        <i class="fas fa-balance-scale mr-1"></i>เปรียบเทียบผู้เล่น
    </a>
</div>

<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">รายการผู้เล่น</h6>
        <div>
            <label class="mb-0 mr-2 font-weight-bold small">กรองตามทีม:</label>
            <select id="filterTeam" class="form-control form-control-sm d-inline-block w-auto">
                <option value="">ทุกทีม</option>
                <?php foreach ($teams as $t): ?>
                    <option value="<?= $t['id'] ?>"><?= esc($t['team_name']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="playerTable" class="table table-bordered" width="100%">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>ผู้เล่น</th>
                        <th width="100">แมตช์</th>
                        <th width="130">ระยะทางรวม</th>
                        <th width="130">เฉลี่ย (กม./ชม.)</th>
                        <th width="130">สูงสุด (กม./ชม.)</th>
                        <th width="80">Sprint</th>
                        <th width="80" class="text-center">จัดการ</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<script>
var baseUrl = '<?= $baseUrl ?>';

var table = $('#playerTable').DataTable({
    processing: true,
    serverSide: true,
    ajax: {
        url: baseUrl + 'admin/analytics/players/data',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        data: function (d) {
            d.team_id = $('#filterTeam').val();
        }
    },
    columns: [
        { data: 0, orderable: false },
        { data: 1 },
        { data: 2, orderable: false },
        { data: 3 },
        { data: 4 },
        { data: 5 },
        { data: 6 },
        { data: 7, orderable: false, className: 'text-center' },
    ],
    language: {
        url: baseUrl + 'assets/vendor/datatables/lang/Thai.json',
        processing: '<div class="spinner-border text-primary spinner-border-sm"></div> กำลังโหลด...',
    },
    order: [[3, 'desc']],
    pageLength: 25,
});

$('#filterTeam').on('change', function () {
    table.ajax.reload();
});

// Modal — คลิกปุ่มดูสถิติ
$(document).on('click', '.btn-player-detail', function () {
    var playerId = $(this).data('id');
    $('#modalPlayerBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i></div>');
    $('#modalPlayerDetail').modal('show');

    $.ajax({
        url: baseUrl + 'admin/analytics/players/detail-json/' + playerId,
        type: 'GET',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        success: function (res) {
            if (res.status !== 'success') {
                $('#modalPlayerBody').html('<div class="alert alert-danger">ไม่พบข้อมูล</div>');
                return;
            }
            var d = res.data;
            var p = d.player;
            var s = d.stats;

            var imgHtml = p.image_url
                ? '<img src="' + baseUrl + p.image_url + '" class="rounded-circle" width="80" height="80" style="object-fit:cover">'
                : '<div class="rounded-circle bg-secondary d-flex align-items-center justify-content-center mx-auto" style="width:80px;height:80px"><i class="fas fa-user fa-2x text-white"></i></div>';

            var recentHtml = '';
            if (d.recent_matches && d.recent_matches.length > 0) {
                recentHtml = '<h6 class="font-weight-bold mt-3 mb-2">5 แมตช์ล่าสุด</h6>'
                    + '<div class="table-responsive"><table class="table table-sm table-bordered mb-0">'
                    + '<thead class="bg-light"><tr><th>แมตช์</th><th>วันที่</th><th>ระยะ(กม.)</th><th>เฉลี่ย</th><th>สูงสุด</th><th>Sprint</th></tr></thead><tbody>';
                $.each(d.recent_matches, function (i, m) {
                    recentHtml += '<tr>'
                        + '<td>' + (m.match_name || '-') + '</td>'
                        + '<td>' + (m.match_date ? m.match_date.substring(0, 10) : '-') + '</td>'
                        + '<td class="text-center">' + parseFloat(m.total_distance || 0).toFixed(2) + '</td>'
                        + '<td class="text-center">' + parseFloat(m.avg_speed || 0).toFixed(1) + '</td>'
                        + '<td class="text-center">' + parseFloat(m.max_speed || 0).toFixed(1) + '</td>'
                        + '<td class="text-center">' + (m.sprint_count || 0) + '</td>'
                        + '</tr>';
                });
                recentHtml += '</tbody></table></div>';
            }

            $('#modalPlayerBody').html(
                '<div class="row align-items-center mb-3">'
                    + '<div class="col-auto text-center">' + imgHtml + '</div>'
                    + '<div class="col">'
                        + '<h5 class="font-weight-bold mb-1">' + esc(p.player_name || '') + ' ' + esc(p.player_surname || '') + '</h5>'
                        + '<div class="text-muted small"><i class="fas fa-tshirt mr-1"></i>เบอร์ ' + (p.player_number || '-')
                        + ' &nbsp;|&nbsp;<i class="fas fa-shield-alt mr-1"></i>' + esc(p.team_name || '-') + '</div>'
                        + '<a href="' + baseUrl + 'admin/analytics/report/player/' + p.id + '" class="btn btn-sm btn-outline-primary mt-2">'
                        + '<i class="fas fa-chart-bar mr-1"></i>ดูรายงานเต็ม</a>'
                    + '</div>'
                + '</div>'
                + '<div class="row text-center mb-2">'
                    + '<div class="col-6 col-md-3 mb-2"><div class="card border-primary"><div class="card-body py-2">'
                    + '<div class="h5 mb-0 text-primary font-weight-bold">' + parseFloat(s && s.total_distance ? s.total_distance : 0).toFixed(2) + '</div>'
                    + '<div class="small text-muted">ระยะทางรวม (กม.)</div></div></div></div>'
                    + '<div class="col-6 col-md-3 mb-2"><div class="card border-info"><div class="card-body py-2">'
                    + '<div class="h5 mb-0 text-info font-weight-bold">' + parseFloat(s && s.avg_speed ? s.avg_speed : 0).toFixed(1) + '</div>'
                    + '<div class="small text-muted">เฉลี่ย (กม./ชม.)</div></div></div></div>'
                    + '<div class="col-6 col-md-3 mb-2"><div class="card border-warning"><div class="card-body py-2">'
                    + '<div class="h5 mb-0 text-warning font-weight-bold">' + parseFloat(s && s.max_speed ? s.max_speed : 0).toFixed(1) + '</div>'
                    + '<div class="small text-muted">สูงสุด (กม./ชม.)</div></div></div></div>'
                    + '<div class="col-6 col-md-3 mb-2"><div class="card border-success"><div class="card-body py-2">'
                    + '<div class="h5 mb-0 text-success font-weight-bold">' + (s && s.match_count ? s.match_count : 0) + '</div>'
                    + '<div class="small text-muted">จำนวนแมตช์</div></div></div></div>'
                + '</div>'
                + recentHtml
            );
        },
        error: function () {
            $('#modalPlayerBody').html('<div class="alert alert-danger">เกิดข้อผิดพลาด กรุณาลองใหม่</div>');
        }
    });
});

function esc(str) {
    var d = document.createElement('div');
    d.appendChild(document.createTextNode(str || ''));
    return d.innerHTML;
}
</script>

<!-- Modal สถิติผู้เล่น -->
<div class="modal fade" id="modalPlayerDetail" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white py-3">
                <h5 class="modal-title mb-0"><i class="fas fa-user mr-2"></i>สถิติผู้เล่น</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body" id="modalPlayerBody">
                <div class="text-center py-4">
                    <i class="fas fa-spinner fa-spin fa-2x text-primary"></i>
                </div>
            </div>
        </div>
    </div>
</div>
