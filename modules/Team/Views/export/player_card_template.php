<?php
/**
 * Player Card Template - renders via mPDF WriteFixedPosHTML
 * Variables: $id_card_template, $team, $tournament, $players, $mpdf
 */
ini_set('memory_limit', '1024M');
set_time_limit(600);

$tpl = (is_array($id_card_template)) ? $id_card_template : [];

if (!function_exists('resolvePublicPath_v2')) {
    function resolvePublicPath_v2($dbPath) {
        if (empty($dbPath)) return FCPATH . $dbPath;
        if (is_file($dbPath)) return $dbPath;
        $candidates = [
            FCPATH . $dbPath,
            FCPATH . 'public/' . $dbPath,
            WRITEPATH . $dbPath,
            WRITEPATH . 'secure_uploads/id_cards/' . basename($dbPath)
        ];
        if (strpos($dbPath, 'public/') === 0) {
            $candidates[] = FCPATH . substr($dbPath, 7);
        }
        foreach ($candidates as $path) {
            if (is_file($path)) return $path;
        }
        return $candidates[0];
    }
}

if (!function_exists('processImageForPdf_v2')) {
    function processImageForPdf_v2($imagePath, $size = 300) {
        try {
            if (!is_file($imagePath)) return $imagePath;
            $mime = @mime_content_type($imagePath);
            if (!$mime || strpos($mime, 'image') === false) return $imagePath;
            
            $cacheDir = WRITEPATH . 'cache/';
            if (!is_dir($cacheDir)) @mkdir($cacheDir, 0777, true);
            
            $tempPath = $cacheDir . 'pdf_card_' . md5($imagePath . $size) . '.jpg';
            if (is_file($tempPath) && (time() - filemtime($tempPath) < 86400)) {
                return $tempPath;
            }

            $contents = @file_get_contents($imagePath);
            if (!$contents) return $imagePath;
            $image = @imagecreatefromstring($contents);
            if (!$image) return $imagePath;

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

            $w = imagesx($image);
            $h = imagesy($image);
            $cropSize = min($w, $h);
            $x = (int)(($w - $cropSize) / 2);
            $y = (int)(($h - $cropSize) / 2);
            $cropped = imagecrop($image, ['x' => $x, 'y' => $y, 'width' => $cropSize, 'height' => $cropSize]);
            
            if ($cropped) {
                $resized = imagecreatetruecolor($size, $size);
                $white = imagecolorallocate($resized, 255, 255, 255);
                imagefill($resized, 0, 0, $white);
                imagecopyresampled($resized, $cropped, 0, 0, 0, 0, $size, $size, imagesx($cropped), imagesy($cropped));
                imagejpeg($resized, $tempPath, 85);
                imagedestroy($resized);
                imagedestroy($cropped);
            }
            imagedestroy($image);
            return is_file($tempPath) ? $tempPath : $imagePath;
        } catch (\Throwable $e) {
            return $imagePath;
        }
    }
}

$origCardW = floatval($tpl['card_width_mm'] ?? 90);
if ($origCardW <= 0) $origCardW = 90;
$origCardH = floatval($tpl['card_height_mm'] ?? 130);
if ($origCardH <= 0) $origCardH = 130;
$templateImagePath = !empty($tpl['template_image']) ? resolvePublicPath_v2($tpl['template_image']) : '';
$defaultImg = resolvePublicPath_v2('public/img/img_profile.jpg');
$hasCustomTemplate = !empty($templateImagePath) && is_file($templateImagePath);

// Determine card type
$cardType = $tpl['card_type'] ?? 'player';

// Tournament logo
$tourLogoPath = '';
if (!empty($tournament['logo'])) {
    $tourLogoPath = resolvePublicPath_v2($tournament['logo']);
    if (!is_file($tourLogoPath)) $tourLogoPath = '';
}

// A4 = 210 x 297 mm (margins = 0)
$pageW = 210;
$pageH = 297;
$pagePad = 10;
$cardGap = 0.4;

// Scale to fit 2x2
$maxCardW = ($pageW - $pagePad * 2 - $cardGap) / 2;
$maxCardH = ($pageH - $pagePad * 2 - $cardGap) / 2;
$scale = min($maxCardW / $origCardW, $maxCardH / $origCardH, 1.0);

$cardW = round($origCardW * $scale, 2);
$cardH = round($origCardH * $scale, 2);

// Center grid
$totalGridW = $cardW * 2 + $cardGap;
$totalGridH = $cardH * 2 + $cardGap;
$offsetX = ($pageW - $totalGridW) / 2;
$offsetY = ($pageH - $totalGridH) / 2;

$positions = [
    ['x' => $offsetX,                    'y' => $offsetY],
    ['x' => $offsetX + $cardW + $cardGap, 'y' => $offsetY],
    ['x' => $offsetX,                    'y' => $offsetY + $cardH + $cardGap],
    ['x' => $offsetX + $cardW + $cardGap, 'y' => $offsetY + $cardH + $cardGap],
];

$totalPlayers = is_array($players) ? count($players) : 0;
$cardIndex = 0;

// Default card colors
$headerColor = ($cardType === 'manager') ? '#0f3460' : '#1a1a2e';
$accentColor = ($cardType === 'manager') ? '#e94560' : '#0f3460';
$cardLabel = ($cardType === 'manager') ? 'STAFF' : 'PLAYER';

if (!empty($players)):
    foreach ($players as $player):
        $slot = $cardIndex % 4;
        $px = $positions[$slot]['x'];
        $py = $positions[$slot]['y'];

        // 1) Background
        if ($hasCustomTemplate) {
            // Custom template image
            $mpdf->WriteFixedPosHTML(
                '<img src="' . $templateImagePath . '" style="width:' . $cardW . 'mm;height:' . $cardH . 'mm;" />',
                $px, $py, $cardW, $cardH, 'hidden'
            );
        } else {
            // Default card design
            $headerH = round(22 * $scale, 2);
            $photoAreaTop = round(25 * $scale, 2);
            $photoAreaH = round(40 * $scale, 2);

            // Card border + white background
            $mpdf->WriteFixedPosHTML(
                '<div style="width:' . $cardW . 'mm;height:' . $cardH . 'mm;border:0.5mm solid ' . $headerColor . ';background:#ffffff;border-radius:2mm;"></div>',
                $px, $py, $cardW, $cardH, 'hidden'
            );

            // Header bar with gradient effect
            $mpdf->WriteFixedPosHTML(
                '<div style="width:' . $cardW . 'mm;height:' . $headerH . 'mm;background:' . $headerColor . ';border-radius:1.5mm 1.5mm 0 0;"></div>',
                $px, $py, $cardW, $headerH, 'hidden'
            );

            // Tournament name in header
            $tourName = esc($tournament['name'] ?? 'TOURNAMENT');
            $tourNameFs = max(5, round(8 * $scale));
            $mpdf->WriteFixedPosHTML(
                '<table width="100%" style="table-layout: fixed; border:none;"><tr><td align="center" style="font-family:garuda;font-size:' . $tourNameFs . 'pt;color:#ffffff;font-weight:bold;line-height:1.0;word-break:break-all;">' . $tourName . '</td></tr></table>',
                $px + 1 * $scale, $py + 2 * $scale, $cardW - 2 * $scale, 18 * $scale, 'hidden'
            );

            // Card type label in header
            $labelFs = max(5, round(7 * $scale));
            $mpdf->WriteFixedPosHTML(
                '<div style="font-family:garuda;font-size:' . $labelFs . 'pt;color:' . $accentColor . ';background:#ffffff;text-align:center;font-weight:bold;border-radius:1mm;padding:0.5mm 2mm;">' . $cardLabel . '</div>',
                $px + ($cardW / 2 - 10 * $scale), $py + $headerH - 4 * $scale, 20 * $scale, 5 * $scale, 'hidden'
            );

            // Tournament logo in header (small, top-left)
            if (!empty($tourLogoPath)) {
                $logoSize = round(8 * $scale, 2);
                $mpdf->WriteFixedPosHTML(
                    '<img src="' . $tourLogoPath . '" style="width:' . $logoSize . 'mm;height:' . $logoSize . 'mm;" />',
                    $px + 2 * $scale, $py + 10 * $scale, $logoSize, $logoSize, 'hidden'
                );
            }

            // Photo frame border
            $frameW = round(28 * $scale, 2);
            $frameH = round(33 * $scale, 2);
            $frameX = $px + ($cardW - $frameW) / 2;
            $frameY = $py + $photoAreaTop + 2 * $scale;
            $mpdf->WriteFixedPosHTML(
                '<div style="width:' . $frameW . 'mm;height:' . $frameH . 'mm;border:0.4mm solid ' . $accentColor . ';border-radius:1mm;background:#f8f9fa;"></div>',
                $frameX, $frameY, $frameW, $frameH, 'hidden'
            );

            // Bottom accent line
            $lineY = $py + $cardH - 8 * $scale;
            $mpdf->WriteFixedPosHTML(
                '<div style="width:' . ($cardW - 10 * $scale) . 'mm;height:0.6mm;background:' . $accentColor . ';"></div>',
                $px + 5 * $scale, $lineY, $cardW - 10 * $scale, 1, 'hidden'
            );
        }

        // 2) Player photo
        $playerImgPath = (!empty($player['image_path']) && $player['image_path'] != '')
            ? resolvePublicPath_v2($player['image_path'])
            : $defaultImg;
        
        $playerImg = processImageForPdf_v2($playerImgPath, 300);
        
        if (is_file($playerImg)) {
            $photoW = round(floatval($tpl['photo_width'] ?? 25) * $scale, 2);
            $photoH = round(floatval($tpl['photo_height'] ?? 30) * $scale, 2);
            $mpdf->WriteFixedPosHTML(
                '<img src="' . $playerImg . '" style="width:' . $photoW . 'mm;height:' . $photoH . 'mm;" />',
                $px + floatval($tpl['photo_x'] ?? 10) * $scale,
                $py + floatval($tpl['photo_y'] ?? 30) * $scale,
                $photoW, $photoH, 'hidden'
            );
        }

        // 3) Shirt number
        if ($cardType !== 'manager') {
            $shirtNumber = esc($player['shirt_number'] ?? '-');
            $numberFs = max(6, round(intval($tpl['number_font_size'] ?? 24) * $scale));
            $numberClr = esc($tpl['number_color'] ?? '#FFFFFF');
            if ($hasCustomTemplate) {
                $mpdf->WriteFixedPosHTML(
                    '<div style="font-family:garuda;font-size:' . $numberFs . 'pt;color:' . $numberClr . ';font-weight:bold;">' . $shirtNumber . '</div>',
                    $px + floatval($tpl['number_x'] ?? 10) * $scale,
                    $py + floatval($tpl['number_y'] ?? 20) * $scale,
                    30 * $scale, 16 * $scale, 'hidden'
                );
            } else {
                $mpdf->WriteFixedPosHTML(
                    '<div style="font-family:garuda;font-size:' . $numberFs . 'pt;color:#ffffff;font-weight:bold;text-align:right;">' . $shirtNumber . '</div>',
                    $px + $cardW - 18 * $scale,
                    $py + 9 * $scale,
                    14 * $scale, 16 * $scale, 'hidden'
                );
            }
        }

        // 4) Player name
        $playerName = esc(trim(($player['first_name'] ?? '') . ' ' . ($player['last_name'] ?? '')));
        $nameFs = max(6, round(intval($tpl['name_font_size'] ?? 16) * $scale));
        $nameClr = esc($tpl['name_color'] ?? '#000000');
        if ($hasCustomTemplate) {
            $mpdf->WriteFixedPosHTML(
                '<table width="100%" style="table-layout: fixed; border:none;"><tr><td style="font-family:garuda;font-size:' . $nameFs . 'pt;color:' . $nameClr . ';font-weight:bold;line-height:1.1;word-break:break-all;">' . $playerName . '</td></tr></table>',
                $px + floatval($tpl['name_x'] ?? 10) * $scale,
                $py + floatval($tpl['name_y'] ?? 100) * $scale,
                $cardW - floatval($tpl['name_x'] ?? 10) * $scale - 2 * $scale, 20 * $scale, 'hidden'
            );
        } else {
            $mpdf->WriteFixedPosHTML(
                '<table width="100%" style="table-layout: fixed; border:none;"><tr><td align="center" style="font-family:garuda;font-size:' . $nameFs . 'pt;color:' . $nameClr . ';font-weight:bold;line-height:1.1;word-break:break-all;">' . $playerName . '</td></tr></table>',
                $px + 1 * $scale,
                $py + floatval($tpl['name_y'] ?? 65) * $scale,
                $cardW - 2 * $scale, 15 * $scale, 'hidden'
            );
        }

        // 5) Team name
        $teamName = esc($team['name'] ?? 'TEAM');
        $teamFs = max(6, round(intval($tpl['team_font_size'] ?? 14) * $scale));
        $teamClr = esc($tpl['team_color'] ?? '#000000');
        if ($hasCustomTemplate) {
            $mpdf->WriteFixedPosHTML(
                '<table width="100%" style="table-layout: fixed; border:none;"><tr><td style="font-family:garuda;font-size:' . $teamFs . 'pt;color:' . $teamClr . ';font-weight:bold;line-height:1.1;word-break:break-all;">' . $teamName . '</td></tr></table>',
                $px + floatval($tpl['team_x'] ?? 10) * $scale,
                $py + floatval($tpl['team_y'] ?? 110) * $scale,
                $cardW - floatval($tpl['team_x'] ?? 10) * $scale - 2 * $scale, 20 * $scale, 'hidden'
            );
        } else {
            $mpdf->WriteFixedPosHTML(
                '<table width="100%" style="table-layout: fixed; border:none;"><tr><td align="center" style="font-family:garuda;font-size:' . $teamFs . 'pt;color:' . $teamClr . ';font-weight:bold;line-height:1.1;word-break:break-all;">' . $teamName . '</td></tr></table>',
                $px + 1 * $scale,
                $py + floatval($tpl['team_y'] ?? 72) * $scale,
                $cardW - 2 * $scale, 20 * $scale, 'hidden'
            );
        }

        $cardIndex++;

        if ($cardIndex % 4 == 0 && $cardIndex < $totalPlayers) {
            $mpdf->AddPage();
        }
    endforeach;
endif;
