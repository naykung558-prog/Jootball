<!-- Football Stats Partial -->
<?php
$data = $stats ?? [];
$summary = $data['summary'] ?? [];
$s = $data['stats'] ?? [];
$selectedTourId = $data['selected_tour_id'] ?? null;

$defaultLogo = base_url('public/img/jootball_logo.png');
$defaultUser = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';

// Goal Minutes - handle both interval and per-minute data
$goalMinutesData = $s['goal_minutes'] ?? [];
$goalMinutesLabels = array_keys($goalMinutesData);
$goalMinutesValues = array_values($goalMinutesData);

$chartPayload = [
    'goalMinutes' => [
        'labels' => $goalMinutesLabels,
        'values' => $goalMinutesValues,
    ],
    'halfGoals' => [
        'labels' => ['ครึ่งแรก (1-45)', 'ครึ่งหลัง (46-90+)'],
        'values' => [ (float)($s['half_goals']['1st'] ?? 0), (float)($s['half_goals']['2nd'] ?? 0) ],
    ],
    'posGoals' => [
        'labels' => array_keys($s['position_goals'] ?? []),
        'values' => array_column($s['position_goals'] ?? [], 'count'),
    ],
    'homeAway' => [
        'labels' => ['เจ้าบ้านชนะ', 'เสมอ', 'ทีมเยือนชนะ'],
        'values' => [
            (float)($s['home_away']['home_win'] ?? 0),
            (float)($s['home_away']['draw'] ?? 0),
            (float)($s['home_away']['away_win'] ?? 0),
        ],
    ],
    'finalFourGroups' => [
        'labels' => array_map(fn($k) => 'กลุ่ม ' . $k, array_keys($s['final_four_groups'] ?? [])),
        'values' => array_values($s['final_four_groups'] ?? []),
    ],
    'finalFourGoals' => [
        'labels' => array_map(fn($row) => $row['name'] ?? '-', $s['final_four_goals'] ?? []),
        'gf' => array_map(fn($row) => (int)($row['gf'] ?? 0), $s['final_four_goals'] ?? []),
        'ga' => array_map(fn($row) => (int)($row['ga'] ?? 0), $s['final_four_goals'] ?? []),
    ],
];
?>

<link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700&family=Kanit:wght@200;400;600&display=swap" rel="stylesheet">
<style>
:root {
    --fs-panel: rgba(255, 255, 255, 0.98);
    --fs-panel-border: rgba(226, 232, 240, 0.8);
    --fs-accent-blue: #214790; /* Jootball Blue */
    --fs-accent-yellow: #FBC02D; /* Jootball Yellow */
    --fs-accent-green: #10b981;
    --fs-accent-red: #ef4444;
    --fs-accent-orange: #f59e0b;
    --fs-accent-indigo: #6366f1;
    --fs-accent-purple: #8b5cf6;
    --fs-accent-gold: #d97706;
    --fs-text-main: #1e293b;
    --fs-text-dim: #64748b;
    --fs-glass-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
}

/* Base Panel */
.fs-panel {
    background: var(--fs-panel);
    border: 1px solid var(--fs-panel-border);
    border-radius: 20px;
    padding: 24px;
    box-shadow: var(--fs-glass-shadow);
    height: 100%;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

/* Theme Varients */
.theme-red { --current-accent: var(--fs-accent-red); }
.theme-blue { --current-accent: var(--fs-accent-blue); }
.theme-green { --current-accent: var(--fs-accent-green); }
.theme-orange { --current-accent: var(--fs-accent-orange); }
.theme-indigo { --current-accent: var(--fs-accent-indigo); }
.theme-purple { --current-accent: var(--fs-accent-purple); }
.theme-gold { --current-accent: var(--fs-accent-gold); }
.theme-yellow { --current-accent: var(--fs-accent-yellow); }

.fs-panel:hover {
    transform: translateY(-4px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
    border-color: var(--current-accent, var(--fs-accent-blue));
}

.fs-panel-title {
    font-size: 0.95rem;
    font-weight: 700;
    color: var(--current-accent, var(--fs-accent-blue));
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    line-height: 1.4;
    min-height: 2.8rem;
}
.fs-panel-title i { opacity: 0.8; }

/* Header */
.fs-topbar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    background: linear-gradient(135deg, var(--fs-accent-blue) 0%, #0B1548 100%);
    padding: 25px 35px;
    border-radius: 24px;
    color: #fff;
    box-shadow: 0 10px 15px -3px rgba(11, 21, 72, 0.3);
    flex-wrap: wrap;
    gap: 15px;
    border: 2px solid var(--fs-accent-yellow);
}
.fs-title { flex: 1; min-width: 250px; }
.fs-title h4 { 
    font-family: 'Kanit', sans-serif; 
    font-size: 1.75rem; 
    margin: 0; 
    font-weight: 800; 
    letter-spacing: 0.5px;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
    color: #ffffff !important;
}
.fs-title .platform-label {
    color: var(--fs-accent-yellow);
    font-family: 'Orbitron', sans-serif;
    font-weight: 700;
}

.fs-filter select {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #fff;
    padding: 10px 20px;
    border-radius: 12px;
    backdrop-filter: blur(10px);
    font-weight: 500;
    transition: all 0.2s;
}
.fs-filter select:hover { background: rgba(255, 255, 255, 0.2); }
.fs-filter select option { color: #000; }

/* KPI Grid */
.fs-kpi-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 24px;
    margin-bottom: 30px;
}
.fs-kpi-card { text-align: center; position: relative; overflow: hidden; border-bottom: 4px solid transparent; }
.fs-kpi-card:nth-child(1) { border-bottom-color: var(--fs-accent-blue); }
.fs-kpi-card:nth-child(2) { border-bottom-color: var(--fs-accent-green); }
.fs-kpi-card:nth-child(3) { border-bottom-color: var(--fs-accent-indigo); }
.fs-kpi-card:nth-child(4) { border-bottom-color: var(--fs-accent-orange); }

.fs-kpi-value { font-family: 'Orbitron', sans-serif; font-size: 2.5rem; font-weight: 700; margin: 5px 0; color: #1e293b; }
.fs-kpi-label { color: var(--fs-text-dim); font-size: 0.85rem; font-weight: 600; text-transform: uppercase; letter-spacing: 1px; }

/* Main Grid */
.fs-main-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 24px;
}
.span-2 { grid-column: span 2; }
.span-3 { grid-column: span 3; }

/* Heatmap Pitch Styling */
.defense-heatmap-container {
    position: relative;
    width: 100%;
    height: 300px;
    background: linear-gradient(180deg, #064e3b 0%, #14532d 100%);
    border-radius: 16px;
    overflow: hidden;
    box-shadow: inset 0 0 50px rgba(0,0,0,0.3);
}
.pitch-overlay { position: absolute; inset: 15px; border: 2px solid rgba(255, 255, 255, 0.3); pointer-events: none; }
.pitch-center { position: absolute; top: 50%; left: 50%; transform: translate(-50%, -50%); width: 80px; height: 80px; border: 2px solid rgba(255, 255, 255, 0.3); border-radius: 50%; }
.pitch-center::after { content: ''; position: absolute; top: 50%; left: 0; right: 0; height: 2px; background: rgba(255, 255, 255, 0.3); transform: translateY(-50%); }
.pitch-goal { position: absolute; right: 0; top: 50%; transform: translateY(-50%); width: 40px; height: 100px; border: 2px solid rgba(255, 255, 255, 0.3); border-right: 0; }

.heatmap-glow {
    position: absolute;
    border-radius: 50%;
    pointer-events: none;
    mix-blend-mode: screen;
    filter: blur(25px);
}

/* Charts & Lists */
.fs-canvas-wrap { height: 260px; position: relative; }
.player-list, .team-list { max-height: 280px; overflow-y: auto; padding-right: 5px; }

/* Custom Scrollbar */
.player-list::-webkit-scrollbar, .team-list::-webkit-scrollbar { width: 4px; }
.player-list::-webkit-scrollbar-thumb, .team-list::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 10px; }

.player-row, .team-list-item {
    display: flex; align-items: center; gap: 12px; padding: 12px;
    border-radius: 12px; transition: background 0.2s;
}
.player-row:hover, .team-list-item:hover { background: #f8fafc; }
.player-img { width: 40px; height: 40px; border-radius: 10px; object-fit: cover; background: #f1f5f9; border: 1px solid #e2e8f0; }
.badge-count { background: #f1f5f9; color: #1e293b; padding: 4px 10px; border-radius: 8px; font-weight: 700; font-size: 0.85rem; }

@media (max-width: 1400px) { .fs-main-grid { grid-template-columns: repeat(2, 1fr); } .span-3 { grid-column: span 2; } }
@media (max-width: 992px) { .fs-kpi-grid { grid-template-columns: repeat(2, 1fr); } .fs-main-grid { grid-template-columns: 1fr; } .span-2, .span-3 { grid-column: span 1; } }
@media (max-width: 576px) { .fs-kpi-grid { grid-template-columns: 1fr; } }
</style>

<div class="football-stats-page">
    <!-- Dashboard Header -->
    <div class="fs-topbar">
        <div class="fs-title">
            <p class="mb-1 text-uppercase opacity-75 platform-label" style="font-size: 0.75rem; letter-spacing: 3px;">Analytics Platform</p>
            <h4>สถิติการแข่งขันฟุตบอล</h4>
        </div>
        <form class="fs-filter" method="get" action="<?= $filterAction ?? base_url('') ?>">
            <?php if(isset($academy['id'])): ?>
                <input type="hidden" name="academy_id" value="<?= (int)$academy['id'] ?>">
            <?php endif; ?>
            <select name="tour_id" class="form-select" onchange="this.form.submit()">
                <option value="">ทุกทัวร์นาเมนต์</option>
                <?php foreach (($data['tournaments'] ?? []) as $tour): ?>
                    <option value="<?= (int)$tour['tour_id'] ?>" <?= ((int)$selectedTourId === (int)$tour['tour_id']) ? 'selected' : '' ?>>
                        <?= esc($tour['name'] ?: 'Tournament #' . $tour['tour_id']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </form>
    </div>

    <?php if (empty($summary['match_count'])): ?>
        <div class="fs-panel text-center py-5">
            <div class="py-5">
                <i class="fas fa-chart-pie fa-4x mb-4 text-light opacity-50"></i>
                <h5 class="text-muted">ไม่พบข้อมูลสถิติที่ประมวลผลแล้ว</h5>
                <p class="text-muted small">โปรดเลือกทัวร์นาเมนต์ที่มีการบันทึกข้อมูลการแข่งขัน</p>
            </div>
        </div>
    <?php else: ?>
        
        <!-- KPI Summary Row -->
        <div class="fs-kpi-grid">
            <div class="fs-panel fs-kpi-card">
                <div class="fs-kpi-label">แมตช์ทั้งหมด</div>
                <div class="fs-kpi-value"><?= number_format($summary['match_count']) ?></div>
                <div class="badge bg-soft-primary text-primary rounded-pill px-3">COMPLETED</div>
            </div>
            <div class="fs-panel fs-kpi-card">
                <div class="fs-kpi-label">ประตูรวม</div>
                <div class="fs-kpi-value text-success"><?= number_format($summary['goal_count']) ?></div>
                <div class="badge bg-soft-success text-success rounded-pill px-3">TOTAL GOALS</div>
            </div>
            <div class="fs-panel fs-kpi-card">
                <div class="fs-kpi-label">ชนะ 2 นัดแรกแล้วเข้า 8 ทีม</div>
                <div class="fs-kpi-value" style="color: #6366f1;"><?= (int)($s['two_wins']['count'] ?? 0) ?>/<?= (int)($s['two_wins']['total'] ?? 0) ?></div>
                <div class="badge bg-soft-indigo text-indigo rounded-pill px-3">TOP FORM</div>
            </div>
            <div class="fs-panel fs-kpi-card">
                <div class="fs-kpi-label">แพ้รอบแรกแต่เข้า 8 ทีม</div>
                <div class="fs-kpi-value text-warning"><?= (int)($s['comeback']['count'] ?? 0) ?></div>
                <div class="badge bg-soft-warning text-warning rounded-pill px-3">COMEBACK</div>
            </div>
        </div>

        <!-- Unified Analytical Grid (3 Columns Symmetrical) -->
        <div class="fs-main-grid">
            
            <!-- Row 1: Time & Position Analytics -->
            <div class="fs-panel span-3 theme-blue">
                <div class="fs-panel-title"><i class="fas fa-clock"></i> ช่วงเวลาที่ทำประตูได้บ่อยที่สุด (นาที)</div>
                <div class="fs-canvas-wrap"><canvas id="goalMinutesChart"></canvas></div>
            </div>

            <!-- Row 2: Heatmap & Team Power -->
            <div class="fs-panel span-2 theme-orange">
                <div class="fs-panel-title"><i class="fas fa-bullseye"></i> แผนภูมิความหนาแน่นการทำประตูตามตำแหน่ง (HEATMAP)</div>
                <div class="row g-4 h-100">
                    <div class="col-md-7">
                        <div class="defense-heatmap-container" id="scorerHeatmap">
                            <div class="pitch-overlay"></div>
                            <div class="pitch-center"></div>
                            <div class="pitch-goal"></div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <h6 class="mb-3 text-muted fw-bold small">อันดับตามตำแหน่ง</h6>
                        <div class="player-list">
                            <?php 
                            $posGoalsRaw = $s['position_goals'] ?? [];
                            uasort($posGoalsRaw, fn($a, $b) => $b['count'] <=> $a['count']);
                            foreach ($posGoalsRaw as $code => $pdata): 
                            ?>
                                <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom border-light">
                                    <div class="d-flex flex-column">
                                        <span class="fw-bold text-dark" style="font-size: 0.85rem;"><?= esc($code) ?></span>
                                        <small class="text-muted" style="font-size: 0.7rem;"><?= esc($pdata['name_th']) ?></small>
                                    </div>
                                    <span class="badge-count text-danger" style="background: rgba(239, 68, 68, 0.1); color: var(--fs-accent-red);"><?= $pdata['count'] ?></span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="fs-panel theme-green">
                <div class="fs-panel-title">
                    <i class="fas fa-balance-scale"></i> อัตราการชนะ: ทีมเหย้า VS ทีมเยือน
                </div>
                <div class="fs-canvas-wrap"><canvas id="homeAwayChart"></canvas></div>
            </div>

            <!-- Row 3: Player & Team Leaders -->
            <div class="fs-panel theme-gold">
                <div class="fs-panel-title"><i class="fas fa-trophy"></i> รายชื่อผู้ทำประตู</div>
                <div class="player-list">
                    <?php if (empty($s['top_scorers'])): ?>
                        <div class="text-center py-5"><i class="fas fa-user-slash mb-2 opacity-25"></i><br>ไม่มีข้อมูลผู้ทำประตู</div>
                    <?php else: ?>
                        <?php foreach (($s['top_scorers'] ?? []) as $idx => $p): ?>
                            <div class="player-row">
                                <span class="fw-bold text-muted small" style="width: 20px;"><?= $idx+1 ?></span>
                                <img src="<?= $p['photo'] ? base_url($p['photo']) : $defaultUser ?>" class="player-img" onerror="this.src='<?= $defaultUser ?>'">
                                <div class="flex-grow-1">
                                    <div style="font-size: 0.85rem; font-weight: 600;"><?= esc($p['name']) ?></div>
                                </div>
                                <span class="badge-count" style="background: rgba(217, 119, 6, 0.1); color: var(--fs-accent-gold);"><?= $p['goals'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="fs-panel theme-purple">
                <div class="fs-panel-title"><i class="fas fa-exchange-alt"></i> ทีมที่เปลี่ยนตัวมากที่สุด (รอบแรก)</div>
                <div class="team-list">
                    <?php if (empty($s['most_subs'])): ?>
                        <div class="text-center py-5"><i class="fas fa-info-circle mb-2 opacity-25"></i><br>ไม่มีข้อมูลการเปลี่ยนตัว</div>
                    <?php else: ?>
                        <?php foreach (($s['most_subs'] ?? []) as $idx => $t): ?>
                            <div class="team-list-item">
                                <span class="fw-bold text-muted small" style="width: 20px;"><?= $idx+1 ?></span>
                                <img src="<?= $t['logo'] ? base_url($t['logo']) : $defaultLogo ?>" class="player-img" style="width: 32px; height: 32px; border-radius: 8px;" onerror="this.src='<?= $defaultLogo ?>'">
                                <span style="font-size: 0.85rem; font-weight: 500;"><?= esc($t['name']) ?></span>
                                <span class="ms-auto badge-count" style="background: rgba(139, 92, 246, 0.1); color: var(--fs-accent-purple);"><?= $t['subs'] ?></span>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="fs-panel theme-indigo">
                <div class="fs-panel-title"><i class="fas fa-layer-group"></i> กลุ่มที่ทีมเข้ารอบ 8 ทีมสุดท้ายสังกัด</div>
                <div class="fs-canvas-wrap"><canvas id="finalFourGroupChart"></canvas></div>
            </div>

            <!-- Row 4: Final 8 Insights -->
            <div class="fs-panel theme-blue">
                <div class="fs-panel-title"><i class="fas fa-shield-virus"></i> สถิติยิง/เสียประตู (รอบตัดเชือก)</div>
                <div class="fs-canvas-wrap"><canvas id="finalFourGoalsChart"></canvas></div>
            </div>

            <div class="fs-panel theme-green">
                <div class="fs-panel-title"><i class="fas fa-route"></i> ทีมชนะ 2 นัดแรกที่เข้า 8 ทีม</div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach(($s['two_wins']['teams'] ?? []) as $t): ?>
                        <div class="text-center p-2 rounded-3" style="width: 75px; background: rgba(16, 185, 129, 0.05); border: 1px solid rgba(16, 185, 129, 0.1);">
                            <img src="<?= $t['logo'] ? base_url($t['logo']) : $defaultLogo ?>" style="width: 32px; height: 32px; object-fit: contain;" onerror="this.src='<?= $defaultLogo ?>'">
                            <div class="mt-1 fw-bold text-success" style="font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($t['team_name']) ?></div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($s['two_wins']['teams'])): ?> <p class="text-muted small w-100 text-center py-3">- ไม่มีข้อมูล -</p> <?php endif; ?>
                </div>
            </div>

            <div class="fs-panel theme-orange">
                <div class="fs-panel-title"><i class="fas fa-fire-alt"></i> ทีมที่เคยแพ้แต่เข้า 8 ทีมสุดท้าย</div>
                <div class="d-flex flex-wrap gap-2">
                    <?php foreach(($s['comeback']['teams'] ?? []) as $t): ?>
                        <div class="text-center p-2 rounded-3" style="width: 75px; background: rgba(245, 158, 11, 0.05); border: 1px solid rgba(245, 158, 11, 0.1);">
                            <img src="<?= $t['logo'] ? base_url($t['logo']) : $defaultLogo ?>" style="width: 32px; height: 32px; object-fit: contain;" onerror="this.src='<?= $defaultLogo ?>'">
                            <div class="mt-1 fw-bold text-warning" style="font-size: 0.65rem; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= esc($t['team_name']) ?></div>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($s['comeback']['teams'])): ?> <p class="text-muted small w-100 text-center py-3">- ไม่มีข้อมูล -</p> <?php endif; ?>
                </div>
            </div>

        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
    const ds = <?= json_encode($chartPayload, JSON_UNESCAPED_UNICODE) ?>;
    Chart.defaults.color = '#64748b';
    Chart.defaults.borderColor = 'rgba(0, 0, 0, 0.05)';
    Chart.defaults.font.family = "'Kanit', sans-serif";

    function mkChart(id, config) {
        const ctx = document.getElementById(id);
        if (!ctx) return null;
        
        // Destroy existing chart if it exists to avoid duplication errors
        const existingChart = Chart.getChart(ctx);
        if (existingChart) existingChart.destroy();
        
        return new Chart(ctx, config);
    }

    // 1. Goal Minutes (Line Theme for detailed data, Bar for intervals)
    const isDetailed = ds.goalMinutes.labels.length > 10;
    mkChart('goalMinutesChart', {
        type: isDetailed ? 'line' : 'bar',
        data: {
            labels: ds.goalMinutes.labels,
            datasets: [{ 
                label: 'จำนวนประตู', 
                data: ds.goalMinutes.values, 
                backgroundColor: isDetailed ? 'rgba(33, 71, 144, 0.1)' : '#214790',
                borderColor: '#214790',
                borderWidth: 2,
                fill: isDetailed,
                stepped: false, // Normal line
                tension: 0, // Sharp straight lines (Signal style)
                pointRadius: isDetailed ? 2 : 4,
                pointBackgroundColor: '#214790',
                borderRadius: 6 
            }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            scales: { 
                y: { 
                    beginAtZero: true, 
                    ticks: { stepSize: 1, precision: 0 } 
                },
                x: {
                    ticks: {
                        maxRotation: 0,
                        autoSkip: true,
                        maxTicksLimit: 15
                    }
                }
            }, 
            plugins: { 
                legend: { display: false },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return 'ประตูที่นาที ' + context.label + ': ' + context.raw + ' ลูก';
                        }
                    }
                }
            } 
        }
    });

    // 2. Half Goals
    mkChart('halfGoalsChart', {
        type: 'doughnut',
        data: {
            labels: ds.halfGoals.labels,
            datasets: [{ data: ds.halfGoals.values, backgroundColor: ['#38bdf8', '#214790'], borderWidth: 4, borderColor: '#fff' }]
        },
        options: { responsive: true, maintainAspectRatio: false, cutout: '75%', plugins: { legend: { position: 'bottom', labels: { boxWidth: 10, padding: 15 } } } }
    });

    // 4. Home Away
    mkChart('homeAwayChart', {
        type: 'bar',
        data: {
            labels: ['โอกาสชนะ (%)'],
            datasets: [
                { label: 'เหย้า', data: [ds.homeAway.values[0]], backgroundColor: '#10b981' },
                { label: 'เสมอ', data: [ds.homeAway.values[1]], backgroundColor: '#f59e0b' },
                { label: 'เยือน', data: [ds.homeAway.values[2]], backgroundColor: '#214790' }
            ]
        },
        options: {
            indexAxis: 'y', responsive: true, maintainAspectRatio: false,
            scales: { x: { stacked: true, max: 100 }, y: { stacked: true, display: false } },
            plugins: { legend: { position: 'bottom', labels: { boxWidth: 12 } } }
        }
    });

    // 9. Groups
    mkChart('finalFourGroupChart', {
        type: 'bar',
        data: {
            labels: ds.finalFourGroups.labels,
            datasets: [{ label: 'ทีม', data: ds.finalFourGroups.values, backgroundColor: '#6366f1', borderRadius: 6 }]
        },
        options: { 
            responsive: true, 
            maintainAspectRatio: false, 
            scales: { 
                y: { 
                    beginAtZero: true, 
                    suggestedMax: 4, 
                    ticks: { stepSize: 1, precision: 0 } 
                } 
            }, 
            plugins: { legend: { display: false } } 
        }
    });

    // 10. Goals Semi
    mkChart('finalFourGoalsChart', {
        type: 'bar',
        data: {
            labels: ds.finalFourGoals.labels,
            datasets: [
                { label: 'ยิง', data: ds.finalFourGoals.gf, backgroundColor: '#10b981', borderRadius: 4 },
                { label: 'เสีย', data: ds.finalFourGoals.ga, backgroundColor: '#ef4444', borderRadius: 4 }
            ]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom', labels: { boxWidth: 10 } } } }
    });

    function initScorerHeatmap() {
        const container = document.getElementById('scorerHeatmap');
        if (!container) return;

        const posMap = {
            'ST': { x: 88, y: 50 }, 'CF': { x: 82, y: 50 }, 'LS': { x: 84, y: 35 }, 'RS': { x: 84, y: 65 },
            'LW': { x: 78, y: 20 }, 'RW': { x: 78, y: 80 }, 'Attacker': { x: 82, y: 50 },
            'CAM': { x: 68, y: 50 }, 'LAM': { x: 68, y: 30 }, 'RAM': { x: 68, y: 70 },
            'CM': { x: 55, y: 50 }, 'LCM': { x: 55, y: 35 }, 'RCM': { x: 55, y: 65 },
            'LM': { x: 55, y: 15 }, 'RM': { x: 55, y: 85 },
            'CDM': { x: 42, y: 50 }, 'LDM': { x: 42, y: 35 }, 'RDM': { x: 42, y: 65 },
            'Midfielder': { x: 55, y: 50 },
            'CB': { x: 25, y: 50 }, 'LCB': { x: 25, y: 35 }, 'RCB': { x: 25, y: 65 },
            'LB': { x: 28, y: 15 }, 'RB': { x: 28, y: 85 },
            'GK': { x: 5, y: 50 }, 'Goalkeeper': { x: 5, y: 50 }
        };

        // Clear existing heatmap glows
        const existingGlows = container.querySelectorAll('.heatmap-glow');
        existingGlows.forEach(g => g.remove());

        ds.posGoals.labels.forEach((code, i) => {
            const count = ds.posGoals.values[i];
            const config = posMap[code] || { x: 50, y: 50 };
            if (count > 0) {
                const glow = document.createElement('div');
                glow.className = 'heatmap-glow';
                const size = Math.min(250, 100 + (count * 15));
                glow.style.width = size + 'px'; glow.style.height = size + 'px';
                glow.style.background = `radial-gradient(circle, rgba(239, 68, 68, 0.95) 0%, rgba(245, 158, 11, 0.6) 45%, rgba(251, 192, 45, 0.3) 75%, transparent 100%)`;
                glow.style.left = (config.x + (Math.random() * 6 - 3)) + '%';
                glow.style.top = (config.y + (Math.random() * 20 - 10)) + '%';
                glow.style.transform = 'translate(-50%, -50%)';
                glow.style.opacity = Math.min(1.0, 0.75 + (count * 0.05));
                container.appendChild(glow);
            }
        });
    }
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initScorerHeatmap);
    } else {
        initScorerHeatmap();
    }
})();
</script>
