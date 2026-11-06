<?php
$baseUrl = rtrim(base_url(), "/") . "/";
$matchId = $match['id'];
?>

<div class="d-flex align-items-center mb-3">
    <a href="<?= $baseUrl ?>admin/analytics/matches/detail/<?= $matchId ?>" class="btn btn-outline-secondary btn-sm me-3">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h5 class="text-gray-800 mb-0"><i class="fas fa-fire me-2 text-danger"></i>Heatmap &mdash; <?= esc($match['match_name']) ?></h5>
</div>

<div class="row">
    <!-- Player Selector -->
    <div class="col-md-3 mb-4">
        <div class="card shadow h-100">
            <div class="card-header py-3 d-flex justify-content-between align-items-center">
                <h6 class="m-0 font-weight-bold text-primary">เลือกผู้เล่น</h6>
            </div>
            <div class="card-body p-2 pb-0">
                <!-- Team Filter -->
                <div class="mb-2">
                    <div class="btn-group btn-group-sm w-100" role="group" id="teamFilter">
                        <button type="button" class="btn btn-outline-secondary team-btn active" data-team="all">ทั้งหมด</button>
                        <button type="button" class="btn btn-outline-primary team-btn" data-team="0">เหย้า</button>
                        <button type="button" class="btn btn-outline-danger team-btn" data-team="1">เยือน</button>
                    </div>
                </div>
            </div>
            <div class="list-group list-group-flush" id="playerList" style="max-height:480px;overflow-y:auto">
                <?php if (empty($players)): ?>
                    <div class="list-group-item text-center text-muted">ยังไม่มีข้อมูล</div>
                <?php else: ?>
                    <?php foreach ($players as $i => $p): ?>
                    <a href="#" class="list-group-item list-group-item-action player-item <?= $i === 0 ? 'active' : '' ?>"
                       data-player-id="<?= (int)$p['player_id'] ?>"
                       data-jersey="<?= (int)$p['jersey_num'] ?>"
                       data-team="<?= (int)$p['team_side'] ?>">
                        <div class="d-flex align-items-center">
                            <span class="badge <?= $p['team_side'] == 0 ? 'bg-primary' : 'bg-danger' ?> me-2" style="min-width:28px">
                                <?= (int)$p['jersey_num'] ?>
                            </span>
                            <div class="overflow-hidden">
                                <div class="small fw-bold text-truncate" style="max-width:140px">
                                    <?= $p['player_name'] ? esc($p['player_name'].' '.$p['player_surname']) : 'เบอร์ '.(int)$p['jersey_num'] ?>
                                </div>
                                <div class="text-muted" style="font-size:11px">
                                    <?= number_format((float)$p['total_distance'], 2) ?> กม.
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Heatmap Pitch -->
    <div class="col-md-9 mb-4">
        <div class="card shadow">
            <div class="card-header py-3 d-flex justify-content-between align-items-center flex-wrap" style="gap:8px">
                <h6 class="m-0 fw-bold text-danger" id="heatmapTitle">Heatmap</h6>
                <div class="d-flex align-items-center" style="gap:8px">
                    <select id="halfSelect" class="form-select form-select-sm" style="width:auto">
                        <option value="">ทั้งหมด</option>
                        <option value="1">ครึ่งที่ 1</option>
                        <option value="2">ครึ่งที่ 2</option>
                    </select>
                    <div id="heatLoading" class="spinner-border spinner-border-sm text-danger d-none" role="status"></div>
                </div>
            </div>
            <div class="card-body text-center p-3">
                <!-- SVG Pitch + Heatmap overlay -->
                <div style="position:relative; display:inline-block; max-width:100%; width:100%">
                    <svg id="pitchSvg" viewBox="0 0 800 518"
                         style="width:100%;max-width:800px;border-radius:4px;display:block;margin:0 auto"
                         xmlns="http://www.w3.org/2000/svg">
                        <!-- Background green -->
                        <rect width="800" height="518" fill="#1a7a1a"/>
                        <!-- Alternating stripe pattern -->
                        <rect x="0" y="0"     width="800" height="51.8" fill="#1d8a1d" opacity="0.35"/>
                        <rect x="0" y="103.6" width="800" height="51.8" fill="#1d8a1d" opacity="0.35"/>
                        <rect x="0" y="207.2" width="800" height="51.8" fill="#1d8a1d" opacity="0.35"/>
                        <rect x="0" y="310.8" width="800" height="51.8" fill="#1d8a1d" opacity="0.35"/>
                        <rect x="0" y="414.4" width="800" height="51.8" fill="#1d8a1d" opacity="0.35"/>
                        <!-- Field boundary -->
                        <rect x="10" y="10" width="780" height="498" fill="none" stroke="#ffffff" stroke-width="2.5"/>
                        <!-- Halfway line -->
                        <line x1="400" y1="10" x2="400" y2="508" stroke="#ffffff" stroke-width="2.5"/>
                        <!-- Centre circle r=9.15m → 9.15*(780/105)=67.9px -->
                        <circle cx="400" cy="259" r="68" fill="none" stroke="#ffffff" stroke-width="2.5"/>
                        <!-- Centre spot -->
                        <circle cx="400" cy="259" r="3.5" fill="#ffffff"/>
                        <!-- LEFT Penalty area: 40.32m wide × 16.5m tall → 299.2 × 121.7px -->
                        <!-- x: 10, width: 40.32*(780/105)=299.4, y: 10+(498-121.7)/2=198.2 -->
                        <rect x="10" y="197" width="299" height="124" fill="none" stroke="#ffffff" stroke-width="2.5"/>
                        <!-- LEFT Goal area: 18.32m × 5.5m → 136.1 × 40.8px -->
                        <rect x="10" y="239" width="136" height="40" fill="none" stroke="#ffffff" stroke-width="2.5"/>
                        <!-- LEFT Penalty spot: 11m → 81.7px from left goal line -->
                        <circle cx="92" cy="259" r="3.5" fill="#ffffff"/>
                        <!-- LEFT Penalty arc: r=9.15m=68px centred on penalty spot, only outside box -->
                        <path d="M 309,197 A 68,68 0 0,0 309,321" fill="none" stroke="#ffffff" stroke-width="2.5"/>
                        <!-- RIGHT Penalty area mirror -->
                        <rect x="491" y="197" width="299" height="124" fill="none" stroke="#ffffff" stroke-width="2.5"/>
                        <!-- RIGHT Goal area mirror -->
                        <rect x="654" y="239" width="136" height="40" fill="none" stroke="#ffffff" stroke-width="2.5"/>
                        <!-- RIGHT Penalty spot -->
                        <circle cx="708" cy="259" r="3.5" fill="#ffffff"/>
                        <!-- RIGHT Penalty arc -->
                        <path d="M 491,197 A 68,68 0 0,1 491,321" fill="none" stroke="#ffffff" stroke-width="2.5"/>
                        <!-- LEFT Goal posts -->
                        <rect x="0" y="247" width="10" height="24" fill="none" stroke="#ffffff" stroke-width="2"/>
                        <!-- RIGHT Goal posts -->
                        <rect x="790" y="247" width="10" height="24" fill="none" stroke="#ffffff" stroke-width="2"/>
                        <!-- Corner arcs r=1m=7.4px -->
                        <path d="M 10,17 A 7,7 0 0,0 17,10" fill="none" stroke="#ffffff" stroke-width="2"/>
                        <path d="M 783,10 A 7,7 0 0,0 790,17" fill="none" stroke="#ffffff" stroke-width="2"/>
                        <path d="M 10,501 A 7,7 0 0,1 17,508" fill="none" stroke="#ffffff" stroke-width="2"/>
                        <path d="M 783,508 A 7,7 0 0,1 790,501" fill="none" stroke="#ffffff" stroke-width="2"/>
                    </svg>
                    <!-- Heatmap canvas overlay — absolute over SVG -->
                    <canvas id="heatCanvas"
                        style="position:absolute;top:0;left:0;width:100%;height:100%;opacity:0.75;pointer-events:none;border-radius:4px">
                    </canvas>
                </div>

                <!-- Color Legend -->
                <div class="d-flex justify-content-center align-items-center mt-3" style="gap:8px">
                    <small class="text-muted fw-bold">น้อย</small>
                    <div style="width:200px;height:14px;border-radius:7px;
                                background:linear-gradient(to right,#0000ff,#00bfff,#00ff80,#ffff00,#ff6600,#ff0000);
                                border:1px solid #dee2e6"></div>
                    <small class="text-muted fw-bold">มาก</small>
                    <span class="ms-3 text-muted" id="pointCount" style="font-size:11px"></span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
var baseUrl   = '<?= $baseUrl ?>';
var matchId   = <?= (int)$matchId ?>;
var csrfName  = '<?= csrf_token() ?>';
var csrfToken = '<?= csrf_hash() ?>';

// SVG viewBox dimensions
var SVG_W = 800, SVG_H = 518;
// Pitch field boundary inside SVG
var FIELD_X = 10, FIELD_Y = 10, FIELD_W = 780, FIELD_H = 498;
// Real pitch dimensions in meters
var PITCH_M_W = 105, PITCH_M_H = 68;

var heatCanvas = document.getElementById('heatCanvas');
var pitchSvg   = document.getElementById('pitchSvg');

function syncCanvasSize() {
    var rect = pitchSvg.getBoundingClientRect();
    if (rect.width > 0 && rect.height > 0) {
        heatCanvas.width  = Math.round(rect.width);
        heatCanvas.height = Math.round(rect.height);
    }
}

// Convert meter coords → canvas pixel coords
function meterToCanvas(mx, my) {
    var svgX = FIELD_X + (mx / PITCH_M_W) * FIELD_W;
    var svgY = FIELD_Y + (my / PITCH_M_H) * FIELD_H;
    return {
        x: (svgX / SVG_W) * heatCanvas.width,
        y: (svgY / SVG_H) * heatCanvas.height
    };
}

var lastData = [];

function drawHeatmap(positions) {
    lastData = positions;
    syncCanvasSize();
    var ctx = heatCanvas.getContext('2d');
    ctx.clearRect(0, 0, heatCanvas.width, heatCanvas.height);

    if (!positions || positions.length === 0) {
        $('#pointCount').text('ไม่มีข้อมูล');
        return;
    }
    $('#pointCount').text(positions.length + ' จุด');

    var r = Math.max(14, heatCanvas.width * 0.030);

    positions.forEach(function(p) {
        var c = meterToCanvas(p.x, p.y);
        var v = p.v;
        var alpha = Math.min(v * 0.9, 0.88);

        var grad = ctx.createRadialGradient(c.x, c.y, 0, c.x, c.y, r);
        var core, mid;
        if (v < 0.25) {
            core = 'rgba(0,0,255,' + alpha + ')';
            mid  = 'rgba(0,50,180,' + (alpha * 0.35) + ')';
        } else if (v < 0.5) {
            core = 'rgba(0,191,255,' + alpha + ')';
            mid  = 'rgba(0,100,200,' + (alpha * 0.35) + ')';
        } else if (v < 0.75) {
            core = 'rgba(255,220,0,' + alpha + ')';
            mid  = 'rgba(255,140,0,' + (alpha * 0.35) + ')';
        } else {
            core = 'rgba(255,0,0,' + alpha + ')';
            mid  = 'rgba(200,0,0,' + (alpha * 0.35) + ')';
        }
        grad.addColorStop(0,   core);
        grad.addColorStop(0.55, mid);
        grad.addColorStop(1,   'rgba(0,0,0,0)');

        ctx.fillStyle = grad;
        ctx.beginPath();
        ctx.arc(c.x, c.y, r, 0, Math.PI * 2);
        ctx.fill();
    });
}

function loadHeatmap(playerId, jerseyNum, teamSide, half) {
    $('#heatLoading').removeClass('d-none');
    var body = { match_id: matchId, player_id: playerId, jersey_num: jerseyNum, team_side: teamSide, half: half || null };
    body[csrfName] = csrfToken;

    $.ajax({
        url: baseUrl + 'admin/analytics/analysis/positions',
        type: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        data: JSON.stringify(body),
        contentType: 'application/json',
        dataType: 'json',
        success: function(res) {
            $('#heatLoading').addClass('d-none');
            if (res.status === 'success' && res.data && res.data.length) {
                // aggregate into 0.5m grid
                var grid = {};
                res.data.forEach(function(pos) {
                    var gx = Math.round(parseFloat(pos.x) * 2) / 2;
                    var gy = Math.round(parseFloat(pos.y) * 2) / 2;
                    var key = gx + '_' + gy;
                    grid[key] = (grid[key] || 0) + 1;
                });
                var vals = Object.keys(grid).map(function(k){ return grid[k]; });
                var maxV = Math.max.apply(null, vals) || 1;
                var points = Object.keys(grid).map(function(k) {
                    var p = k.split('_');
                    return { x: parseFloat(p[0]), y: parseFloat(p[1]), v: grid[k] / maxV };
                });
                drawHeatmap(points);
            } else {
                drawHeatmap([]);
            }
        },
        error: function() {
            $('#heatLoading').addClass('d-none');
            drawHeatmap([]);
        }
    });
}

// Initial load
syncCanvasSize();
<?php if (!empty($players)): ?>
var firstPlayer = <?= json_encode($players[0]) ?>;
loadHeatmap(firstPlayer.player_id, firstPlayer.jersey_num, firstPlayer.team_side, null);
$('#heatmapTitle').text('Heatmap \u2014 ' + (firstPlayer.player_name ? firstPlayer.player_name + ' ' + firstPlayer.player_surname : 'เบอร์ ' + firstPlayer.jersey_num));
<?php endif; ?>

window.addEventListener('resize', function() {
    if (lastData.length) { drawHeatmap(lastData); }
});

// Click player item
$(document).on('click', '.player-item', function(e) {
    e.preventDefault();
    $('.player-item').removeClass('active');
    $(this).addClass('active');
    var playerId = parseInt($(this).data('player-id')) || 0;
    var jersey   = parseInt($(this).data('jersey'))    || 0;
    var team     = parseInt($(this).data('team'));
    var half     = $('#halfSelect').val() || null;
    var name     = $(this).find('.fw-bold').text().trim();
    $('#heatmapTitle').text('Heatmap \u2014 ' + name);
    loadHeatmap(playerId, jersey, team, half);
});

// Half selector
$('#halfSelect').on('change', function() {
    var active = $('.player-item.active');
    if (active.length) { active.trigger('click'); }
});

// Team filter buttons
$(document).on('click', '.team-btn', function() {
    $('.team-btn').removeClass('active');
    $(this).addClass('active');
    var tf = $(this).data('team');
    $('.player-item').each(function() {
        var show = (tf === 'all') || (String($(this).data('team')) === String(tf));
        $(this).toggle(show);
    });
});
</script>
