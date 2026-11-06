<?php
/**
 * Debug: increase limits for PDF generation
 */
ini_set('memory_limit', '512M');
set_time_limit(300);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url('public/img/logo01.png') ?>" type="image/icon type">
    <title>ใบส่งชื่อนักกีฬา</title>
    <style>
        table,
        th,
        td {
            border-collapse: collapse;
        }
    </style>
</head>

<?php
$teamLogo = !empty($team['logo']) ? FCPATH . $team['logo'] : FCPATH . 'public/img/jootball_logo.png';
?>

<body>
    <div style="text-align:center; margin-bottom: 10px;">
        <img src="<?= $teamLogo ?>" width="80" alt="" style="margin-bottom: 5px;"><br>
        <h2 style="color: #1a3a6b; margin: 5px 0; font-size: 26px;">ใบส่งชื่อนักกีฬา</h2>
        <p style="font-size: 20px; margin: 5px 0;">
            ในการแข่งขันฟุตบอล <?= !empty($input['title']) ? $input['title'] : '........................................' ?>
            &nbsp;&nbsp;รุ่น <?= !empty($team['generation_name']) ? $team['generation_name'] : '..................' ?>
        </p>
        <p style="font-size: 18px; margin: 5px 0;">
            ชื่อทีม <?= (session()->get('team.name')) ? session()->get('team.name') : $team['name'] ?>
            &nbsp;&nbsp;เบอร์ผู้ฝึกสอน/โค้ช <?= (session()->get('team.coach_phone')) ? session()->get('team.coach_phone') : '........................' ?>
        </p>
        <p style="font-size: 18px; margin: 5px 0;">
            เสื้อสี.................................กางเกงสี..................................ถุงเท้าสี.................................
        </p>
    </div>
    <table width="100%" border="1" cellspacing="0" style="text-align:center; margin-bottom: 8px;">
        <thead>
            <tr>
                <th rowspan="3" width="6%">หมาย เลข</th>
                <th rowspan="3">ชื่อ - นามสกุล</th>
                <th colspan="21">การแข่งขันครั้งที่</th>
            </tr>
            <tr>
                <?php for ($i = 1; $i <= 7; $i++) { ?>
                    <th colspan="3"><?= $i ?></th>
                <?php } ?>
            </tr>
            <tr>
                <?php for ($i = 1; $i <= 7; $i++) { ?>
                    <th>P</th>
                    <th>S</th>
                    <th>N</th>
                <?php } ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $key => $player) { ?>
                <tr>
                    <td><?= $player['shirt_number'] ?></td>
                    <td><?= $player['first_name'] . ' ' . $player['last_name'] ?></td>
                    <?php for ($i = 1; $i <= 7; $i++) { ?>
                        <td></td>
                        <td></td>
                        <td></td>
                    <?php } ?>
                </tr>
            <?php } ?>
        </tbody>
    </table>

    <span>
        หมายเหตุ P = PLAYING ผู้เล่นตัวจริง S = SUBSTITUTION ผู้เล่นสำรอง N = NON PLAYING ผู้เล่นที่ลงเล่นไม่ได้
    </span>
    <pagebreak />
    <div style="text-align:center; margin-bottom: 10px;">
        <img src="<?= $teamLogo ?>" width="80" alt="" style="margin-bottom: 5px;"><br>
        <h2 style="color: #1a3a6b; margin: 5px 0; font-size: 26px;">สรุปผลการแข่งขัน</h2>
        <p style="font-size: 20px; margin: 5px 0;">
            ในการแข่งขันฟุตบอล <?= !empty($input['title']) ? $input['title'] : '........................................' ?>
            &nbsp;&nbsp;รุ่น <?= !empty($team['generation_name']) ? $team['generation_name'] : '..................' ?>
        </p>
        <p style="font-size: 18px; margin: 5px 0;">
            ชื่อทีม <?= (session()->get('team.name')) ? session()->get('team.name') : $team['name'] ?>
        </p>
    </div>
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
            <?php for ($i = 1; $i <= 7; $i++) { ?>
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
            <?php } ?>
        </tbody>
    </table>
</body>

</html>