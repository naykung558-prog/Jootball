<?php
/**
 * Debug: increase limits for PDF generation
 */
ini_set('memory_limit', '512M');
set_time_limit(300);

// สี theme น้ำเงินเหมือนกันทุกรุ่น
$colorPrimary = '#1a3a6b';
$colorSecondary = '#2c5aa0';
$colorText = '#1a3a6b';
$colorLink = '#0056b3';

// สีตาม generation — ดึงจากชื่อทัวร์ก่อน ถ้าไม่มีค่อยใช้ generation_name ของทีม
if (!empty($input['title']) && preg_match('/U-?(\d+)/i', $input['title'], $genMatch)) {
    $genName = 'U' . $genMatch[1];
} else {
    $genName = !empty($team['generation_name']) ? $team['generation_name'] : '';
}
if (stripos($genName, 'U15') !== false) {
    $colorBannerBg = '#F5B800';
    $colorBannerText = '#5B2D0E';
    $colorBadgeGen = '#F5B800';
    $colorBadgeGenText = '#5B2D0E';
} else {
    // Default: U13
    $colorBannerBg = '#1400FF';
    $colorBannerText = '#ffffff';
    $colorBadgeGen = '#1400FF';
    $colorBadgeGenText = '#ffffff';
}
?>
<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <title>Starting List</title>
    <style>
        body {
            font-family: garuda, sans-serif;
            font-size: 14px;
            color: <?= $colorText ?>;
        }

        table {
            border-collapse: collapse;
        }

        .header-banner {
            background: linear-gradient(to right, <?= $colorPrimary ?>, <?= $colorSecondary ?>);
            color: #ffffff;
            text-align: center;
            padding: 6px 0;
            font-size: 20px;
            font-weight: bold;
            letter-spacing: 3px;
            margin-bottom: 6px;
            font-style: italic;
        }

        .info-table td {
            padding: 1px 4px;
            font-size: 14px;
            vertical-align: top;
        }

        .info-label {
            font-weight: bold;
            color: <?= $colorText ?>;
            white-space: nowrap;
        }

        .info-value {
            color: <?= $colorLink ?>;
            font-weight: bold;
        }

        .badge-age {
            background-color: #FFD700;
            color: <?= $colorText ?>;
            padding: 2px 10px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 11px;
            display: inline-block;
        }

        .badge-gen {
            background-color: <?= $colorBadgeGen ?>;
            color: <?= $colorBadgeGenText ?>;
            padding: 3px 12px;
            border-radius: 15px;
            font-weight: bold;
            font-size: 16px;
            display: inline-block;
        }

        .main-table {
            width: 100%;
            font-size: 10px;
        }

        .main-table th {
            background-color: #d9d9d9;
            color: #333333;
            border: 1px solid #999;
            padding: 2px 1px;
            text-align: center;
            font-weight: bold;
        }

        .main-table td {
            border: 1px solid #aaa;
            padding: 2px 2px;
            text-align: center;
            height: 16px;
        }

        .th-round {
            background-color: #bfbfbf;
            color: #333333;
            border: 1px solid #999;
        }

        .th-psyr-p, .th-psyr-s {
            color: #1a3a6b;
            font-weight: bold;
        }

        .th-psyr-y {
            color: #DAA520;
            font-weight: bold;
        }

        .th-psyr-r {
            color: #cc0000;
            font-weight: bold;
        }

        .staff-table td {
            padding: 1px 4px;
            font-size: 14px;
        }
    </style>
</head>

<?php
$teamLogo = !empty($team['logo']) ? FCPATH . $team['logo'] : FCPATH . 'public/img/jootball_logo.png';
$tournamentLogo = !empty($tournament['logo']) ? FCPATH . $tournament['logo'] : '';
$tournamentName = !empty($input['title']) ? $input['title'] : '........................................';
// ดึง Age Group จากชื่อทัวร์นาเมนต์ก่อน ถ้าไม่มีค่อยใช้ generation_name ของทีม
if (!empty($input['title']) && preg_match('/U-?(\d+)/i', $input['title'], $ageMatch)) {
    $generationName = 'U' . $ageMatch[1];
} else {
    $generationName = !empty($team['generation_name']) ? $team['generation_name'] : '..................';
}
$teamName = !empty($team['name']) ? $team['name'] : (session()->get('team.name') ?: '..................');

// สีชุด (hex) จาก tournament_team ของทัวร์นาเมนต์นี้ — render เป็นช่องสี ถ้าว่างเป็นจุดไข่ปลา
$jersey = $jersey ?? [];
$swatch = function ($hex) {
    $hex = trim((string)($hex ?? ''));
    if ($hex === '') return '<span style="letter-spacing:2px;">..........</span>';
    return '<span style="display:inline-block; width:28px; height:11px; border:1px solid #333; background:' . htmlspecialchars($hex) . '; vertical-align:middle;"></span>';
};
?>

<body>
    <!-- Banner STARTING LIST -->
    <div style="background-color: <?= $colorBannerBg ?>; text-align:center; padding: 6px 0; margin-bottom: 2px;">
        <span style="font-size: 18px; font-weight: bold; color: <?= $colorBannerText ?>; letter-spacing: 3px; font-style: italic;">STARTING LIST</span>
    </div>

    <!-- Logo and Tournament Title (พื้นหลังน้ำเงิน) -->
    <table width="100%" style="background-color: <?= $colorPrimary ?>; margin-bottom: 6px;" cellpadding="0" cellspacing="0">
        <tr>
            <td width="20%" style="text-align:left; vertical-align:middle; padding: 5px 10px;">
                <img src="<?= FCPATH . 'public/img/LOGO GLO-01_0.png' ?>" width="50" alt="" style="margin-right: 3px;">
                <img src="<?= FCPATH . 'public/img/LOGO TDF-01_0.png' ?>" width="50" alt="">
            </td>
            <td width="60%" style="text-align:center; vertical-align:middle; padding: 5px 0;">
                <span style="font-size: 18px; font-weight: bold; color: #ffffff;"><?= $tournamentName ?></span>
            </td>
            <td width="20%" style="text-align:right; vertical-align:middle; padding: 5px 10px;">
                <?php if ($tournamentLogo): ?>
                    <img src="<?= $tournamentLogo ?>" width="55" alt="">
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Team Info Section -->
    <table class="info-table" width="100%" style="margin-bottom: 2px;">
        <tr>
            <td class="info-label" width="22%">ROUND COMPETITIONS :</td>
            <td class="info-value" width="43%">Thailand Championship Group Stage 32 Teams &amp; Knockout</td>
            <td width="13%" style="text-align:right;">
                <span class="badge-age">Age Group</span>
            </td>
            <td width="7%" style="text-align:center;">
                <span class="badge-gen"><?= $generationName ?></span>
            </td>
            <td width="15%" rowspan="3" style="text-align:center; vertical-align:middle;">
                <img src="<?= $teamLogo ?>" width="55" alt=""><br>
                <span style="font-size: 9px; color: #1a3a6b; font-weight: bold;">Logo Team<br><?= $teamName ?></span>
            </td>
        </tr>
        <tr>
            <td class="info-label">ZONE</td>
            <td class="info-value">: ................................................................</td>
            <td colspan="2"></td>
        </tr>
        <tr>
            <td class="info-label">TEAM</td>
            <td class="info-value">: <?= $teamName ?></td>
            <td colspan="2"></td>
        </tr>
    </table>

    <!-- Jersey Color Info -->
    <table class="info-table" width="100%" style="margin-bottom: 2px; font-size: 13px;">
        <tr>
            <td class="info-label" width="22%">JERSEY COLOR HOME : PLAYER</td>
            <td width="30%">เสื้อ <?= $swatch($jersey['home_shirt'] ?? '') ?> กางเกง <?= $swatch($jersey['home_trousers'] ?? '') ?> ถุงเท้า <?= $swatch($jersey['home_socks'] ?? '') ?></td>
            <td class="info-label" width="20%">JERSEY COLOR AWAY : เสื้อ <?= $swatch($jersey['away_shirt'] ?? '') ?></td>
            <td width="28%">กางเกง <?= $swatch($jersey['away_trousers'] ?? '') ?> ถุงเท้า <?= $swatch($jersey['away_socks'] ?? '') ?></td>
        </tr>
        <tr>
            <td class="info-label" style="padding-left: 110px;">GK</td>
            <td>เสื้อ <?= $swatch($jersey['home_goal_shirt'] ?? '') ?> กางเกง <?= $swatch($jersey['home_goal_trousers'] ?? '') ?> ถุงเท้า <?= $swatch($jersey['home_goal_socks'] ?? '') ?></td>
            <td style="padding-left: 95px;">เสื้อ <?= $swatch($jersey['away_goal_shirt'] ?? '') ?></td>
            <td>กางเกง <?= $swatch($jersey['away_goal_trousers'] ?? '') ?> ถุงเท้า <?= $swatch($jersey['away_goal_socks'] ?? '') ?></td>
        </tr>
    </table>

    <table class="info-table" width="100%" style="margin-bottom: 6px;">
        <tr>
            <td class="info-label" width="8%">VENUE</td>
            <td class="info-value">: ..............................................................................................................</td>
        </tr>
    </table>

    <!-- Main Player Table -->
    <table class="main-table">
        <thead>
            <!-- Row 1: Round headers -->
            <tr>
                <th rowspan="3" width="5%">No.JSY</th>
                <th rowspan="3" width="21%">NAME</th>
                <th colspan="12" class="th-round">GROUP STAGE 32 TEAMS</th>
                <th colspan="4" class="th-round">ROUND OF 16</th>
                <th colspan="4" class="th-round">Quarter-Finals</th>
                <th colspan="4" class="th-round">Semi-Finals</th>
                <th colspan="4" class="th-round">Final &amp; 3rd Place</th>
            </tr>
            <!-- Row 2: Match Day headers (MD1-MD7) -->
            <tr>
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <th colspan="4">MD<?= $i ?></th>
                <?php endfor; ?>
            </tr>
            <!-- Row 3: P/S/Y/R per match day -->
            <tr>
                <?php for ($i = 1; $i <= 7; $i++): ?>
                    <th width="2%" style="background-color: #ffffff; color: #333;">P</th>
                    <th width="2%" style="background-color: #ffffff; color: #333;">S</th>
                    <th width="2%" style="background-color: #FFEB3B; color: #333;">Y</th>
                    <th width="2%" style="background-color: #E60000; color: #fff;">R</th>
                <?php endfor; ?>
            </tr>
        </thead>
        <tbody>
            <?php
            $count = 0;
            foreach ($players as $key => $player):
                $count++;
            ?>
                <tr>
                    <td style="font-weight: bold; color: #cc0000;"><?= $player['shirt_number'] ?></td>
                    <td style="text-align: left; padding-left: 4px; font-size: 21px; font-weight: bold;"><?= $player['first_name'] . ' ' . $player['last_name'] ?></td>
                    <?php for ($i = 1; $i <= 28; $i++): ?><td></td><?php endfor; ?>
                </tr>
            <?php endforeach; ?>

            <!-- Extra empty rows -->
            <?php for ($i = $count + 1; $i <= 18; $i++): ?>
                <tr>
                    <td>&nbsp;</td>
                    <td></td>
                    <?php for ($j = 1; $j <= 28; $j++): ?><td></td><?php endfor; ?>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>

    <!-- แถวเพิ่มเติม (สำหรับผู้เล่นสำรอง/เพิ่มชื่อ) — โครงคอลัมน์เดียวกับตารางหลัก -->
    <table class="main-table" style="margin-top: 4px;">
        <?php for ($i = 1; $i <= 3; $i++): ?>
            <tr>
                <td width="5%">&nbsp;</td>
                <td width="21%"></td>
                <?php for ($j = 1; $j <= 28; $j++): ?><td width="2%"></td><?php endfor; ?>
            </tr>
        <?php endfor; ?>
    </table>

    <!-- Legend and Staff Section -->
    <table width="100%" style="margin-top: 8px;">
        <tr>
            <td width="55%" style="vertical-align: top; font-size: 13px;">
                <p style="margin: 3px 0;">
                    <strong>P = Player</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong>S = Substitute</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong style="color: #DAA520;">Y = Yellow Card</strong> &nbsp;&nbsp;&nbsp;&nbsp;
                    <strong style="color: #cc0000;">R = Red Card</strong>
                </p>
                <p style="margin: 5px 0; font-size: 13px;">
                    <strong>NoJSY 1</strong> = หมายเลขเสื้อของทีมที่เข้าร่วมแข่งขันรอบแบ่งกลุ่ม 48 ทีม
                </p>
                <p style="margin: 5px 0; font-size: 13px;">
                    <strong>NoJSY 2</strong> = หมายเลขเสื้อที่ผ่านเข้ารอบการแข่งขันมอบให้ในการแข่งขันรอบน็อคเอาท์ 16 ทีม เป็นต้นไป
                </p>
            </td>
            <td width="45%" style="vertical-align: top;">
                <table class="staff-table" width="100%">
                    <tr>
                        <td style="font-weight: bold;">ผู้จัดการทีม</td>
                        <td>Tel. _________________________</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">หัวหน้าผู้ฝึกสอน</td>
                        <td>Tel. _________________________</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">ผู้ช่วยผู้ฝึกสอน</td>
                        <td>Tel. _________________________</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">ผู้ช่วยผู้ฝึกสอน</td>
                        <td>Tel. _________________________</td>
                    </tr>
                    <tr>
                        <td style="font-weight: bold;">ผู้ประสานงาน</td>
                        <td>Tel. _________________________</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
    <pagebreak />

    <!-- หน้า 2: สรุปผลการแข่งขัน -->

    <!-- Banner สรุปผลการแข่งขัน -->
    <div style="background-color: <?= $colorBannerBg ?>; text-align:center; padding: 6px 0; margin-bottom: 2px;">
        <span style="font-family: garuda; font-size: 18px; font-weight: bold; color: <?= $colorBannerText ?>;">สรุปผลการแข่งขัน</span>
    </div>

    <!-- Logo and Tournament Title (พื้นหลังน้ำเงิน) -->
    <table width="100%" style="background-color: <?= $colorPrimary ?>; margin-bottom: 6px;" cellpadding="0" cellspacing="0">
        <tr>
            <td width="20%" style="text-align:left; vertical-align:middle; padding: 5px 10px;">
                <img src="<?= FCPATH . 'public/img/LOGO GLO-01_0.png' ?>" width="50" alt="" style="margin-right: 3px;">
                <img src="<?= FCPATH . 'public/img/LOGO TDF-01_0.png' ?>" width="50" alt="">
            </td>
            <td width="60%" style="text-align:center; vertical-align:middle; padding: 5px 0;">
                <span style="font-size: 18px; font-weight: bold; color: #ffffff;"><?= $tournamentName ?></span>
            </td>
            <td width="20%" style="text-align:right; vertical-align:middle; padding: 5px 10px;">
                <?php if ($tournamentLogo): ?>
                    <img src="<?= $tournamentLogo ?>" width="55" alt="">
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <!-- Team Info -->
    <table class="info-table" width="100%" style="margin-bottom: 8px;">
        <tr>
            <td class="info-label" width="10%">TEAM</td>
            <td class="info-value" width="40%">: <?= $teamName ?></td>
            <td width="15%" style="text-align:right;">
                <span class="badge-age">Age Group</span>
            </td>
            <td width="10%" style="text-align:center;">
                <span class="badge-gen"><?= $generationName ?></span>
            </td>
            <td width="25%"></td>
        </tr>
    </table>
    <table width="100%" border="1" cellspacing="0" style="text-align:center; margin-top: 8px;">
        <thead>
            <tr>
                <th width="5%">ครั้ง</th>
                <th>ทีม</th>
                <th width="5%">ชนะ</th>
                <th width="5%">เสมอ</th>
                <th width="5%">แพ้</th>
                <th>ทีม</th>
                <th width="10%">ผล</th>
                <th>บันทึกใบเหลือง/ใบแดง</th>
            </tr>
        </thead>
        <tbody>
            <?php for ($i = 1; $i <= 7; $i++): ?>
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>:</td>
                    <td></td>
                </tr>
            <?php endfor; ?>
        </tbody>
    </table>
</body>

</html>