<?php
/**
 * Debug: increase limits for PDF generation
 */
ini_set('memory_limit', '512M');
set_time_limit(300);
?>
<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>ใบส่งชื่อนักกีฬา</title>
    <style>
        * { margin: 0; padding: 0; }
        body { font-family: 'kanit', sans-serif; color: #1a1a2e; }
        table { border-collapse: collapse; }

        /* Header section */
        .page-header { text-align: center; margin-bottom: 15px; }
        .team-logo { width: 80px; height: 80px; border-radius: 50%; }
        .team-name { font-size: 22px; font-weight: bold; color: #16478C; margin: 8px 0 2px 0; }
        .tournament-info { font-size: 14px; color: #555; margin-bottom: 5px; }
        .tournament-info strong { color: #16478C; }

        /* Section title bar */
        .section-title {
            background: #16478C;
            color: #fff;
            padding: 6px 15px;
            font-size: 14px;
            font-weight: bold;
            border-radius: 4px 4px 0 0;
        }

        /* Data table */
        .data-table {
            width: 100%;
            border: 1px solid #dee2e6;
            margin-bottom: 20px;
            font-size: 13px;
        }
        .data-table thead th {
            background: #e8edf3;
            color: #16478C;
            padding: 7px 8px;
            text-align: center;
            font-weight: bold;
            border: 1px solid #dee2e6;
            font-size: 12px;
        }
        .data-table tbody td {
            padding: 5px 8px;
            text-align: center;
            border: 1px solid #dee2e6;
            vertical-align: middle;
        }
        .data-table tbody tr:nth-child(even) td {
            background: #f8f9fc;
        }

        /* Photo grid */
        .photo-card {
            text-align: center;
            padding: 10px 5px;
            vertical-align: top;
            border: 1px solid #dee2e6;
        }
        .photo-card img {
            width: 120px;
            height: 140px;
            object-fit: cover;
            border-radius: 6px;
            border: 2px solid #dee2e6;
        }
        .photo-card .player-number {
            background: #16478C;
            color: #fff;
            padding: 1px 10px;
            border-radius: 10px;
            font-size: 12px;
            font-weight: bold;
            display: inline-block;
            margin-top: 5px;
        }
        .photo-card .player-name {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a2e;
            margin-top: 3px;
        }
        .photo-card .player-pos {
            font-size: 10px;
            color: #888;
        }

        /* Coach card */
        .coach-card {
            text-align: center;
            padding: 12px 8px;
            vertical-align: top;
            border: 1px solid #dee2e6;
        }
        .coach-card img {
            width: 100px;
            height: 100px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #f39c12;
        }
        .coach-card .coach-name {
            font-size: 13px;
            font-weight: bold;
            color: #1a1a2e;
            margin-top: 6px;
        }
        .coach-card .coach-role {
            background: #f39c12;
            color: #fff;
            padding: 2px 12px;
            border-radius: 10px;
            font-size: 10px;
            font-weight: bold;
            display: inline-block;
            margin-top: 3px;
        }

        /* Jersey card */
        .jersey-card {
            text-align: center;
            padding: 15px 10px;
            vertical-align: top;
            border: 1px solid #dee2e6;
        }
        .jersey-card img {
            max-width: 200px;
            max-height: 240px;
            border-radius: 8px;
            border: 2px solid #dee2e6;
        }
        .jersey-label {
            font-size: 14px;
            font-weight: bold;
            margin-top: 8px;
        }

        /* ID Card section */
        .idcard-card {
            text-align: center;
            padding: 8px;
            vertical-align: top;
            border: 1px solid #dee2e6;
        }
        .idcard-card .idcard-name {
            font-size: 11px;
            font-weight: bold;
            color: #1a1a2e;
            margin-top: 4px;
        }
        .idcard-card .idcard-number {
            font-size: 10px;
            color: #555;
            margin-top: 1px;
        }

        .text-muted { color: #999; }
        .divider { border-top: 2px solid #16478C; margin: 15px 0; }
    </style>
</head>
<body>

<?php
    // Detect public directory path (รองรับทั้ง root index.php และ public/index.php)
    // ใช้ไฟล์จริงเป็นตัวตรวจสอบ เพราะ is_dir อาจไม่ตรงกับ FCPATH ที่ต่างกันในแต่ละ entry point
    $PUBLIC_PATH = FCPATH;
    $_testFile = 'img/img_profile.jpg';
    $_candidates = [FCPATH . 'public/', FCPATH, dirname(FCPATH) . '/public/'];
    foreach ($_candidates as $_candidate) {
        if (file_exists($_candidate . $_testFile)) {
            $PUBLIC_PATH = $_candidate;
            break;
        }
    }
    unset($_testFile, $_candidates, $_candidate);
    $GLOBALS['PUBLIC_PATH'] = $PUBLIC_PATH;

    // Helper: หา absolute path ของไฟล์จาก path ที่เก็บใน DB
    function resolvePublicPath($dbPath) {
        if (empty($dbPath)) return $GLOBALS['PUBLIC_PATH'] . $dbPath;

        $publicPath = $GLOBALS['PUBLIC_PATH'];
        $candidates = [];

        // 1) ตัด 'public/' prefix แล้วต่อกับ public path
        if (strpos($dbPath, 'public/') === 0) {
            $candidates[] = $publicPath . substr($dbPath, 7);
        }

        // 2) ใช้ path ตรงๆ ต่อกับ public path
        $candidates[] = $publicPath . $dbPath;

        // 3) ใช้ path ตรงๆ ต่อกับ FCPATH
        $candidates[] = FCPATH . $dbPath;

        // 4) ใช้ path ใน WRITEPATH
        $candidates[] = WRITEPATH . $dbPath;
        $candidates[] = WRITEPATH . 'secure_uploads/id_cards/' . basename($dbPath);

        // 5) path เป็น absolute อยู่แล้ว
        if ($dbPath[0] === '/') {
            $candidates[] = $dbPath;
        }

        foreach ($candidates as $path) {
            if (file_exists($path)) {
                return $path;
            }
        }

        log_message('warning', '[resolvePublicPath] File not found for: ' . $dbPath . ' | tried: ' . implode(', ', $candidates));
        return $candidates[0];
    }

    $teamName = $team['name'] ?? session()->get('team.name') ?? '';
    $teamLogo = !empty($team['logo']) ? resolvePublicPath($team['logo']) : $PUBLIC_PATH . 'img/jootball_logo.png';
    if (!file_exists($teamLogo)) {
        $teamLogo = $PUBLIC_PATH . 'img/jootball_logo.png';
    }

    /**
     * Download remote image and cache locally for PDF export.
     */
    function cacheRemoteImage($url, $cacheDir = null) {
        if (empty($url) || !filter_var($url, FILTER_VALIDATE_URL)) return null;
        if ($cacheDir === null) $cacheDir = WRITEPATH . 'cache/';
        $hash = md5($url);
        $cachedFile = $cacheDir . 'pdf_remote_' . $hash . '.jpg';
        if (file_exists($cachedFile) && (time() - filemtime($cachedFile)) < 86400) {
            return $cachedFile;
        }
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 5,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_USERAGENT => 'Mozilla/5.0',
        ]);
        $imageData = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        if ($httpCode === 200 && !empty($imageData)) {
            file_put_contents($cachedFile, $imageData);
            return $cachedFile;
        }
        return null;
    }

    /**
     * แก้ EXIF orientation + crop เป็นสี่เหลี่ยมจัตุรัส สำหรับ mPDF
     */
    function processImageForPdf($imagePath, $size = 300) {
        try {
            if (!file_exists($imagePath)) return $imagePath;

            $mime = @mime_content_type($imagePath);
            if (!$mime || strpos($mime, 'image') === false) return $imagePath;

            $contents = @file_get_contents($imagePath);
            if (!$contents) return $imagePath;

            $image = @imagecreatefromstring($contents);
            if (!$image) return $imagePath;

            // แก้ EXIF orientation (รูปกลับหัว/หมุน)
            if (function_exists('exif_read_data') && ($mime === 'image/jpeg' || $mime === 'image/tiff')) {
                $exif = @exif_read_data($imagePath);
                if ($exif && isset($exif['Orientation'])) {
                    switch ($exif['Orientation']) {
                        case 3: $image = imagerotate($image, 180, 0); break;
                        case 6: $image = imagerotate($image, -90, 0); break;
                        case 8: $image = imagerotate($image, 90, 0); break;
                    }
                }
            }

            // Crop ตรงกลางเป็นสี่เหลี่ยมจัตุรัส
            $w = imagesx($image);
            $h = imagesy($image);
            $cropSize = min($w, $h);
            $x = (int)(($w - $cropSize) / 2);
            $y = (int)(($h - $cropSize) / 2);

            $cropped = imagecrop($image, ['x' => $x, 'y' => $y, 'width' => $cropSize, 'height' => $cropSize]);
            if (!$cropped) { imagedestroy($image); return $imagePath; }

            // Resize ให้ขนาดเท่ากัน
            $resized = imagecreatetruecolor($size, $size);
            // เติมพื้นหลังสีขาว (สำหรับ PNG ที่มี transparency)
            $white = imagecolorallocate($resized, 255, 255, 255);
            imagefill($resized, 0, 0, $white);
            imagecopyresampled($resized, $cropped, 0, 0, 0, 0, $size, $size, imagesx($cropped), imagesy($cropped));

            // บันทึกเป็น temp file
            $tempPath = WRITEPATH . 'cache/pdf_img_' . md5($imagePath . $size) . '.jpg';
            imagejpeg($resized, $tempPath, 92);

            imagedestroy($image);
            imagedestroy($cropped);
            imagedestroy($resized);

            // ตรวจสอบว่าไฟล์ถูกสร้างจริง
            if (file_exists($tempPath) && filesize($tempPath) > 0) {
                return $tempPath;
            }
        } catch (\Throwable $e) {
            // log error แล้วใช้รูปเดิม
            log_message('error', '[processImageForPdf] ' . $e->getMessage());
        }

        return $imagePath;
    }

    /**
     * หารูปโค้ชสำหรับ PDF โดย fallback จากทุก record ที่ใช้ coach_user_id เดียวกัน
     */
    function resolveCoachImageForPdf($coach) {
        $default_img = $GLOBALS['PUBLIC_PATH'] . 'img/img_profile.jpg';
        $candidates = [];

        foreach (['image_path', 'line_picture'] as $field) {
            if (!empty($coach[$field])) {
                $candidates[] = $coach[$field];
            }
        }

        $coachUserId = (int) ($coach['coach_user_id'] ?? 0);
        if ($coachUserId > 0) {
            try {
                $db = \Config\Database::connect();
                $fallbackRows = $db->table('coach')
                    ->select('coach.image_path, coach_users.line_picture')
                    ->join('coach_users', 'coach_users.id = coach.coach_user_id', 'left')
                    ->where('coach.coach_user_id', $coachUserId)
                    ->where('coach.is_active', 1)
                    ->orderBy('coach.id', 'DESC')
                    ->get()
                    ->getResultArray();

                foreach ($fallbackRows as $row) {
                    foreach (['image_path', 'line_picture'] as $field) {
                        if (!empty($row[$field])) {
                            $candidates[] = $row[$field];
                        }
                    }
                }
            } catch (\Throwable $e) {
                log_message('error', '[resolveCoachImageForPdf] ' . $e->getMessage());
            }
        }

        $candidates = array_values(array_unique(array_filter($candidates)));
        foreach ($candidates as $candidate) {
            if (filter_var($candidate, FILTER_VALIDATE_URL)) {
                $cached = cacheRemoteImage($candidate);
                if ($cached && file_exists($cached)) {
                    return $cached;
                }
                continue;
            }

            $localPath = resolvePublicPath($candidate);
            if (file_exists($localPath)) {
                return $localPath;
            }
        }

        return $default_img;
    }
?>

<!-- ============================================================ -->
<!-- หน้า 1: รายชื่อนักเตะ + ผู้ฝึกสอน                              -->
<!-- ============================================================ -->

<div class="page-header">
    <img src="<?= $teamLogo ?>" class="team-logo" alt="">
    <div class="team-name"><?= esc($teamName) ?></div>
    <div class="tournament-info">
        <?php if (!empty($input['title'])) { ?>
            <strong>รายการ</strong> <?= $input['title'] ?>
        <?php } ?>
        <?php if (!empty($input['gen'])) { ?>
            &nbsp;&nbsp;<strong>รุ่น</strong> <?= $input['gen'] ?>
        <?php } ?>
        <?php if (!empty($team['zone_name'])) { ?>
            &nbsp;&nbsp;<strong>ภาค</strong> <?= $team['zone_name'] ?>
        <?php } ?>
    </div>
</div>

<!-- รายชื่อนักเตะ -->
<div class="section-title">รายชื่อนักเตะ (<?= count($players ?? []) ?> คน)</div>
<table class="data-table">
    <thead>
        <tr>
            <th width="6%">ลำดับ</th>
            <th width="12%">หมายเลข</th>
            <th width="35%">ชื่อ-นามสกุล</th>
            <th width="22%">ตำแหน่ง</th>
            <th width="25%">วัน/เดือน/ปีเกิด</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($players)) { ?>
            <?php $counter = 1; ?>
            <?php foreach ($players as $player) { ?>
                <tr>
                    <td><?= $counter++ ?></td>
                    <td style="font-weight: bold; font-size: 15px;"><?= !empty($player['shirt_number']) ? $player['shirt_number'] : '-' ?></td>
                    <td style="text-align: left; padding-left: 12px;"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></td>
                    <td style="font-size: 11px; color: #555;"><?= !empty($player['player_sub_position']) ? esc($player['player_sub_position']) : '-' ?></td>
                    <td><?= !empty($player['birth_date']) ? date('d/m/Y', strtotime('+543 years', strtotime($player['birth_date']))) : '-' ?></td>
                </tr>
            <?php } ?>
        <?php } else { ?>
            <tr>
                <td colspan="5" style="padding: 20px; color: #999;">ไม่มีข้อมูลนักเตะที่เลือก</td>
            </tr>
        <?php } ?>
    </tbody>
</table>

<!-- รายชื่อผู้ฝึกสอน -->
<?php if (!empty($selected_coaches)) { ?>
    <pagebreak />
    <div class="section-title">ผู้ฝึกสอน (<?= count($selected_coaches) ?> คน)</div>
    <table class="data-table">
        <thead>
            <tr>
                <th width="8%">ลำดับ</th>
                <th width="50%">ชื่อ-นามสกุล</th>
                <th width="42%">ตำแหน่ง</th>
            </tr>
        </thead>
        <tbody>
            <?php $coachCounter = 1; ?>
            <?php foreach ($selected_coaches as $coach) { ?>
                <?php
                $coach_name = trim(($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? ''));
                if (empty($coach_name)) $coach_name = 'ผู้ฝึกสอน';
                $role_name = $coach['role_name'] ?? 'ผู้ฝึกสอน';
                ?>
                <tr>
                    <td><?= $coachCounter++ ?></td>
                    <td style="text-align: left; padding-left: 12px;"><?= esc($coach_name) ?></td>
                    <td><?= esc($role_name) ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>


<?php
$hasJersey = !empty($jerseys['home_set_img']) || !empty($jerseys['away_set_img']) || !empty($jerseys['home_goal_set_img']);
$hasCoaches = !empty($selected_coaches);
?>

<!-- ============================================================ -->
<!-- หน้า 2: แผงรูปนักเตะ                                           -->
<!-- ============================================================ -->

<pagebreak />

<div class="page-header">
    <div class="team-name">แผงรูป <?= esc($teamName) ?></div>
    <div class="tournament-info">
        <?php if (!empty($input['title'])) { ?>
            <strong>รายการ</strong> <?= $input['title'] ?>
        <?php } ?>
        <?php if (!empty($input['gen'])) { ?>
            &nbsp;&nbsp;<strong>รุ่น</strong> <?= $input['gen'] ?>
        <?php } ?>
    </div>
</div>

<div class="section-title">รูปนักเตะ</div>
<table width="100%" style="border: 1px solid #dee2e6;">
    <?php if (!empty($players)) { ?>
        <?php foreach ($players as $key => $player) { ?>
            <?php if ($key % 4 == 0) { ?>
                <?php if ($key > 0) { ?></tr><?php } ?>
                <tr>
            <?php } ?>
            <?php
            $img_path = $PUBLIC_PATH . 'img/img_profile.jpg';
            if (!empty($player['image_path'])) {
                if (file_exists($player['image_path'])) {
                    // absolute path (cached remote image)
                    $img_path = $player['image_path'];
                } elseif (!filter_var($player['image_path'], FILTER_VALIDATE_URL)) {
                    // relative path (local upload) - ตัด public/ prefix ถ้ามี
                    $local_path = resolvePublicPath($player['image_path']);
                    if (file_exists($local_path)) {
                        $img_path = $local_path;
                    }
                }
                // ข้าม URL ที่ยังไม่ได้ cache (ใช้ default image แทน เพื่อความเร็ว)
            }
            // แก้ EXIF orientation + crop
            $img_path = processImageForPdf($img_path, 280);
            ?>
            <td class="photo-card" width="25%" style="background: #fafbfd;">
                <img src="<?= $img_path ?>" width="120" height="140" style="width: 120px; height: 140px; border-radius: 6px; border: 2px solid #dee2e6;" alt=""><br>
                <span class="player-number">#<?= !empty($player['shirt_number']) ? $player['shirt_number'] : '-' ?></span>
                <div class="player-name"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></div>
                <div class="player-pos"><?= !empty($player['player_sub_position']) ? esc($player['player_sub_position']) : '-' ?></div>
            </td>
        <?php } ?>
        <?php
        // Fill remaining cells in last row
        $remaining = 4 - (count($players) % 4);
        if ($remaining < 4) {
            for ($i = 0; $i < $remaining; $i++):
        ?>
            <td class="photo-card" style="background: #fafbfd;">&nbsp;</td>
        <?php
            endfor;
        }
        ?>
        </tr>
    <?php } else { ?>
        <tr><td colspan="4" style="text-align: center; padding: 30px; color: #999;">ไม่มีข้อมูลนักเตะ</td></tr>
    <?php } ?>
</table>

<!-- แผงรูปผู้ฝึกสอน (หน้าใหม่) -->
<?php if ($hasCoaches) {
    $coachTotal = count($selected_coaches);
    $coachesPerRow = min($coachTotal, 3);
    $colWidth = round(100 / $coachesPerRow);
    log_message('info', '[PDF Coach Photos] Rendering coach photo section: ' . $coachTotal . ' coaches');
?>
    <pagebreak />
    <div class="section-title">รูปผู้ฝึกสอน (<?= $coachTotal ?> คน)</div>
    <table width="100%" style="border: 1px solid #dee2e6;">
        <?php
        foreach ($selected_coaches as $cIdx => $coach) {
            $coach_img_path = resolveCoachImageForPdf($coach);

            // แก้ EXIF orientation + crop เป็นสี่เหลี่ยมจัตุรัส
            $coach_img_path = processImageForPdf($coach_img_path, 300);
            log_message('info', '[PDF Coach Photos] Coach #' . $cIdx . ' (' . ($coach['first_name'] ?? '') . '): img=' . $coach_img_path . ' exists=' . (file_exists($coach_img_path) ? 'yes' : 'no'));

            $coach_name = trim(($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? ''));
            if (empty($coach_name)) $coach_name = 'ผู้ฝึกสอน';
            $role_name = $coach['role_name'] ?? 'ผู้ฝึกสอน';

            if ($cIdx % $coachesPerRow == 0) {
                if ($cIdx > 0) { ?></tr><?php } ?>
                <tr>
            <?php } ?>
            <td class="coach-card" width="<?= $colWidth ?>%" style="background: #fafbfd;">
                <img src="<?= $coach_img_path ?>" width="100" height="100" style="width: 100px; height: 100px; border-radius: 50%; border: 3px solid #f39c12;" alt=""><br>
                <div class="coach-name"><?= esc($coach_name) ?></div>
                <div style="margin-top: 4px;">
                    <span class="coach-role"><?= esc($role_name) ?></span>
                </div>
            </td>
        <?php } ?>
        <?php
        if ($coachTotal > $coachesPerRow) {
            $remaining = $coachesPerRow - ($coachTotal % $coachesPerRow);
            if ($remaining < $coachesPerRow) {
                for ($i = 0; $i < $remaining; $i++):
        ?>
            <td style="background: #fafbfd; border: 1px solid #dee2e6;">&nbsp;</td>
        <?php
                endfor;
            }
        }
        ?>
        </tr>
    </table>
<?php } ?>

<!-- ============================================================ -->
<!-- หน้า: รูปบัตรประชาชนนักเตะ                                       -->
<!-- ============================================================ -->

<?php
// กรองเฉพาะนักเตะที่มีรูปบัตรประชาชน
$playersWithIdCard = array_filter($players ?? [], function($p) {
    return !empty($p['id_card_image']);
});
// กรองเฉพาะโค้ชที่มีรูปบัตรประชาชน
$coachesWithIdCard = array_filter($selected_coaches ?? [], function($c) {
    return !empty($c['id_card_image']);
});

if (!empty($playersWithIdCard) || !empty($coachesWithIdCard)) {
?>
<pagebreak />

<div class="page-header">
    <div class="team-name">สำเนาบัตรประชาชน <?= esc($teamName) ?></div>
    <div class="tournament-info">
        <?php if (!empty($input['title'])) { ?>
            <strong>รายการ</strong> <?= $input['title'] ?>
        <?php } ?>
        <?php if (!empty($input['gen'])) { ?>
            &nbsp;&nbsp;<strong>รุ่น</strong> <?= $input['gen'] ?>
        <?php } ?>
    </div>
</div>

<?php if (!empty($playersWithIdCard)) { ?>
<div class="section-title">บัตรประชาชนนักเตะ (<?= count($playersWithIdCard) ?> คน)</div>
<table width="100%" style="border: 1px solid #dee2e6;">
    <?php
    $idIdx = 0;
    foreach ($playersWithIdCard as $player) {
        $idcard_path = resolvePublicPath($player['id_card_image']);
        if (!file_exists($idcard_path)) {
            $idIdx++;
            continue;
        }

        if ($idIdx % 2 == 0) {
            if ($idIdx > 0) { ?></tr><?php } ?>
            <tr>
        <?php } ?>
        <td class="idcard-card" width="50%" style="background: #fafbfd;">
            <img src="<?= $idcard_path ?>" width="230" style="width: 230px; border-radius: 4px; border: 1px solid #ccc;" alt="">
            <div class="idcard-name"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></div>
            <div class="idcard-number"><?= !empty($player['id_card']) ? esc($player['id_card']) : '' ?></div>
        </td>
    <?php
        $idIdx++;
    }

    if ($idIdx % 2 != 0) {
    ?>
        <td class="idcard-card" style="background: #fafbfd;">&nbsp;</td>
    <?php } ?>
    </tr>
</table>
<?php } ?>

<?php if (!empty($coachesWithIdCard)) { ?>
<?php if (!empty($playersWithIdCard)) { ?><pagebreak /><?php } ?>
<div class="section-title">บัตรประชาชนผู้ฝึกสอน (<?= count($coachesWithIdCard) ?> คน)</div>
<table width="100%" style="border: 1px solid #dee2e6;">
    <?php
    $cIdIdx = 0;
    foreach ($coachesWithIdCard as $coach) {
        $idcard_path = resolvePublicPath($coach['id_card_image']);
        if (!file_exists($idcard_path)) {
            $cIdIdx++;
            continue;
        }

        $coach_name = trim(($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? ''));
        if (empty($coach_name)) $coach_name = 'ผู้ฝึกสอน';

        if ($cIdIdx % 2 == 0) {
            if ($cIdIdx > 0) { ?></tr><?php } ?>
            <tr>
        <?php } ?>
        <td class="idcard-card" width="50%" style="background: #fafbfd;">
            <img src="<?= $idcard_path ?>" width="230" style="width: 230px; border-radius: 4px; border: 1px solid #ccc;" alt="">
            <div class="idcard-name"><?= esc($coach_name) ?></div>
            <div class="idcard-number"><?= !empty($coach['id_card']) ? esc($coach['id_card']) : '' ?></div>
        </td>
    <?php
        $cIdIdx++;
    }

    if ($cIdIdx % 2 != 0) {
    ?>
        <td class="idcard-card" style="background: #fafbfd;">&nbsp;</td>
    <?php } ?>
    </tr>
</table>
<?php } ?>

<?php } ?>

<!-- ============================================================ -->
<!-- หน้าสุดท้าย: ชุดแข่งขัน                                          -->
<!-- ============================================================ -->

<?php
if ($hasJersey) {
    $jerseyLabels = ['home_set_img' => 'ชุดเหย้า (Home)', 'away_set_img' => 'ชุดเยือน (Away)', 'home_goal_set_img' => 'ชุดผู้รักษาประตู (GK)'];
    $jerseyColors = ['home_set_img' => '#16478C', 'away_set_img' => '#e74c3c', 'home_goal_set_img' => '#f39c12'];
?>
<pagebreak />

<div class="page-header">
    <div class="team-name"><?= esc($teamName) ?></div>
    <div class="tournament-info">
        <?php if (!empty($input['title'])) { ?>
            <strong>รายการ</strong> <?= $input['title'] ?>
        <?php } ?>
        <?php if (!empty($input['gen'])) { ?>
            &nbsp;&nbsp;<strong>รุ่น</strong> <?= $input['gen'] ?>
        <?php } ?>
    </div>
</div>

    <?php
    $activeJerseys = [];
    foreach (['home_set_img', 'away_set_img', 'home_goal_set_img'] as $jType) {
        if (!empty($jerseys[$jType])) {
            $jImg = resolvePublicPath($jerseys[$jType]);
            if (file_exists($jImg)) {
                $activeJerseys[$jType] = $jImg;
            }
        }
    }
    $jerseyCount = count($activeJerseys);
    ?>

    <div class="section-title">ชุดแข่งขัน</div>
    <table width="100%" style="border: 1px solid #dee2e6; margin-bottom: 15px;">
        <?php if ($jerseyCount <= 3) { ?>
            <!-- แสดงแนวนอน -->
            <tr>
                <?php foreach ($activeJerseys as $jType => $jImg) { ?>
                    <td style="width: <?= round(100 / $jerseyCount) ?>%; text-align: center; padding: 15px 10px; vertical-align: top; background: #fafbfd; border: 1px solid #dee2e6;">
                        <div style="background: <?= $jerseyColors[$jType] ?>; color: #fff; display: inline-block; padding: 2px 14px; border-radius: 12px; font-size: 11px; font-weight: bold; margin-bottom: 10px;">
                            <?= $jerseyLabels[$jType] ?>
                        </div><br>
                        <img src="<?= $jImg ?>" style="max-width: 160px; max-height: 200px; border-radius: 8px; border: 2px solid <?= $jerseyColors[$jType] ?>;" alt="">
                    </td>
                <?php } ?>
            </tr>
        <?php } ?>
    </table>

<?php } ?>

</body>
</html>
