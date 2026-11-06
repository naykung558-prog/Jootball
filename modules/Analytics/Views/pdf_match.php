<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="utf-8">
<title><?= htmlspecialchars($match['match_name'], ENT_QUOTES, 'UTF-8') ?> - รายงานสถิติ</title>
<style>
* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}
body {
    font-family: 'thsarabun', 'THSarabunNew', 'Garuda', sans-serif;
    font-size: 13px;
    color: #222;
    background: #fff;
}
.page-header {
    text-align: center;
    padding: 18px 0 12px;
    border-bottom: 2px solid #1a3c6e;
    margin-bottom: 18px;
}
.page-header h1 {
    font-size: 20px;
    font-weight: bold;
    color: #1a3c6e;
    margin-bottom: 4px;
}
.page-header p {
    font-size: 12px;
    color: #555;
    margin: 2px 0;
}
.score-box {
    width: 100%;
    border: 1px solid #ccc;
    border-radius: 6px;
    margin-bottom: 18px;
    background: #f7f9fc;
}
.score-box table {
    width: 100%;
    border-collapse: collapse;
}
.score-box td {
    padding: 12px 8px;
    vertical-align: middle;
}
.team-name {
    font-size: 15px;
    font-weight: bold;
    color: #1a3c6e;
}
.team-label {
    font-size: 11px;
    color: #777;
}
.score-cell {
    text-align: center;
    font-size: 24px;
    font-weight: bold;
    color: #1a3c6e;
    padding: 12px 20px;
    border-left: 1px solid #ddd;
    border-right: 1px solid #ddd;
}
.section-title {
    font-size: 14px;
    font-weight: bold;
    color: #1a3c6e;
    border-bottom: 1px solid #1a3c6e;
    padding-bottom: 4px;
    margin-bottom: 10px;
}
.stats-table {
    width: 100%;
    border-collapse: collapse;
    font-size: 12px;
}
.stats-table th {
    background-color: #1a3c6e;
    color: #fff;
    padding: 7px 6px;
    text-align: center;
    font-weight: bold;
    border: 1px solid #1a3c6e;
}
.stats-table th.text-left {
    text-align: left;
}
.stats-table td {
    padding: 6px 6px;
    border: 1px solid #d0d5dd;
    text-align: center;
    vertical-align: middle;
}
.stats-table td.text-left {
    text-align: left;
}
.stats-table tbody tr:nth-child(even) {
    background-color: #f0f4ff;
}
.stats-table tbody tr:nth-child(odd) {
    background-color: #fff;
}
.footer {
    margin-top: 24px;
    border-top: 1px solid #ccc;
    padding-top: 8px;
    font-size: 10px;
    color: #888;
    text-align: right;
}
</style>
</head>
<body>

<!-- Header -->
<div class="page-header">
    <h1>รายงานสถิติแมตช์ฟุตบอล</h1>
    <p><?= htmlspecialchars($match['match_name'], ENT_QUOTES, 'UTF-8') ?></p>
    <p>วันที่: <?= date('d/m/Y', strtotime($match['match_date'])) ?>
        <?php if (!empty($match['venue'])): ?>
            &nbsp;|&nbsp; สนาม: <?= htmlspecialchars($match['venue'], ENT_QUOTES, 'UTF-8') ?>
        <?php endif; ?>
    </p>
</div>

<!-- สกอร์ -->
<div class="score-box">
    <table>
        <tr>
            <td style="text-align:right; width:40%">
                <span class="team-name">
                    <?= htmlspecialchars($match['home_team_name_real'] ?? $match['home_team_name'], ENT_QUOTES, 'UTF-8') ?>
                </span><br>
                <span class="team-label">เหย้า</span>
            </td>
            <td class="score-cell">
                <?= (int)$match['home_score'] ?> - <?= (int)$match['away_score'] ?>
            </td>
            <td style="text-align:left; width:40%">
                <span class="team-name">
                    <?= htmlspecialchars($match['away_team_name_real'] ?? $match['away_team_name'], ENT_QUOTES, 'UTF-8') ?>
                </span><br>
                <span class="team-label">เยือน</span>
            </td>
        </tr>
    </table>
</div>

<!-- ตารางสถิติผู้เล่น -->
<div class="section-title">สถิติรายผู้เล่น</div>

<?php if (empty($playerStats)): ?>
    <p style="color:#888; font-style:italic;">ไม่มีข้อมูลสถิติผู้เล่น</p>
<?php else: ?>
<table class="stats-table">
    <thead>
        <tr>
            <th style="width:30px">#</th>
            <th class="text-left">ชื่อผู้เล่น</th>
            <th style="width:50px">เบอร์</th>
            <th style="width:90px">ระยะทาง (กม.)</th>
            <th style="width:90px">เฉลี่ย (กม./ชม.)</th>
            <th style="width:90px">สูงสุด (กม./ชม.)</th>
            <th style="width:60px">Sprint</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($playerStats as $i => $ps): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td class="text-left">
                <?php if (!empty($ps['player_name'])): ?>
                    <?= htmlspecialchars(trim($ps['player_name'] . ' ' . $ps['player_surname']), ENT_QUOTES, 'UTF-8') ?>
                <?php else: ?>
                    เบอร์ <?= (int)$ps['jersey_num'] ?>
                <?php endif; ?>
            </td>
            <td><?= (int)$ps['jersey_num'] ?></td>
            <td><?= number_format($ps['total_distance'], 2) ?></td>
            <td><?= number_format($ps['avg_speed'], 1) ?></td>
            <td><?= number_format($ps['max_speed'], 1) ?></td>
            <td><?= (int)$ps['sprint_count'] ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- Speed Zone Summary -->
<?php if (!empty($playerStats)): ?>
<div class="section-title" style="margin-top:18px">Speed Zone Breakdown (%)</div>
<table class="stats-table">
    <thead>
        <tr>
            <th class="text-left">ผู้เล่น</th>
            <th style="width:70px">เดิน</th>
            <th style="width:70px">วิ่งช้า</th>
            <th style="width:70px">วิ่ง</th>
            <th style="width:70px">วิ่งเร็ว</th>
            <th style="width:70px">Sprint</th>
            <th style="width:80px">Sprint (กม.)</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($playerStats as $ps): ?>
        <tr>
            <td class="text-left">
                <?php if (!empty($ps['player_name'])): ?>
                    <?= htmlspecialchars(trim($ps['player_name'] . ' ' . $ps['player_surname']), ENT_QUOTES, 'UTF-8') ?>
                <?php else: ?>
                    เบอร์ <?= (int)$ps['jersey_num'] ?>
                <?php endif; ?>
            </td>
            <td><?= number_format($ps['zone_walk'] ?? 0, 1) ?>%</td>
            <td><?= number_format($ps['zone_jog'] ?? 0, 1) ?>%</td>
            <td><?= number_format($ps['zone_run'] ?? 0, 1) ?>%</td>
            <td><?= number_format($ps['zone_fast'] ?? 0, 1) ?>%</td>
            <td><strong><?= number_format($ps['zone_sprint'] ?? 0, 1) ?>%</strong></td>
            <td><?= number_format($ps['sprint_distance'] ?? 0, 2) ?></td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<!-- Footer -->
<div class="footer">
    พิมพ์เมื่อ <?= date('d/m/Y H:i') ?> &nbsp;|&nbsp; Football Analytics - Jootball
</div>

</body>
</html>
