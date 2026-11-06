<!DOCTYPE html> 
<html lang="th"> 
<head> 
    <meta charset="UTF-8"> 
    <style> 
        /* ตั้งค่าพื้นฐานสำหรับ mPDF */
        body { 
            font-family: 'thsarabun', 'garuda', sans-serif;
            font-size: 16pt; 
            line-height: 1.3;
            color: #333; 
        } 

        /* 1. กำหนดชื่อ Header/Footer ให้ mPDF รู้จัก */
        @page {
            margin-top: 2.5cm;    /* เว้นที่ให้หัวกระดาษ */
            margin-bottom: 2.5cm; /* เว้นที่ให้ท้ายกระดาษ */
            header: html_myHeader;
            footer: html_myFooter;
        }

        .main-table { 
            width: 100%; 
            border-collapse: collapse; 
        } 
        .main-table th, .main-table td { 
            padding: 8px 5px; 
            text-align: center; 
            border: 1px solid #c5d4e3; 
        } 
        .main-table th { 
            background-color: #06346D; 
            color: white; 
            font-weight: bold; 
        } 
        .text-left { text-align: left !important; padding-left: 10px !important; } 
        tr:nth-child(even) { background-color: #f9fbfc; }

        /* สไตล์ตารางใน Header/Footer */
        .bar-table { width: 100%; border: none; font-size: 11pt; color: #555; }
        .bar-table td { border: none !important; }

        /* ปรับขนาดรูปเช็คถูก */
        .img-check { width: 15px; height: 15px; }
    </style> 
</head> 
<body> 

<htmlpageheader name="myHeader">
    <div style="border-bottom: 1px solid #06346D; padding-bottom: 5px;">
        <table class="bar-table">
            <tr>
                <td style="width: 15%;"><img src="<?=base_url('public/img/jootball_logo.png')?>" height="30"></td>
                <td style="width: 70%; text-align: center;">
                    <strong style="color: #06346D;">รายงานสถานะการสมัคร: U-13 GLO CUP (ภาคเหนือ จ.ลำพูน)</strong>
                </td>
                <td style="width: 15%; text-align: right;"><img src="https://jootball.com/public/img/jigsaw-logo-2020_0.png" height="30"></td>
            </tr>
        </table>
    </div>
</htmlpageheader>

<htmlpagefooter name="myFooter">
    <div style="border-top: 1px solid #06346D; padding-top: 5px;">
        <table class="bar-table">
            <tr>
                <td style="width: 80%; font-size: 9pt; line-height: 1.2;">
                    <strong>เอกสารฉบับนี้สร้างโดยแอปพลิเคชัน Jootball</strong> (ใช้งานฟรีสำหรับทุกทีม)<br>
                    สนใจใช้งานติดต่อ Line: @511lucuy หรือสแกน QR Code เพื่อใช้งาน
                </td>
                <td style="width: 20%; text-align: right;">
                    <img src="https://qr-official.line.me/gs/M_511ulcuy_BW.png?oat_content=qr" height="40">
                </td>
            </tr>
        </table>
    </div>
</htmlpagefooter>

<div class="container"> 
    <table class="main-table"> 
        <thead> 
            <tr> 
                <th width="8%">ลำดับ</th> 
                <th width="42%">ชื่อทีม</th> 
                <th width="12%">อนุมัติ</th> 
                <th width="13%">รอตรวจสอบ</th> 
                <th width="13%">รอเอกสาร</th> 
                <th width="12%">ไม่อนุมัติ</th> 
            </tr> 
        </thead> 
        <tbody> 
            <?php 
            $checkImg = 'https://cdn-icons-png.flaticon.com/128/1442/1442912.png'; 
            
            // 1. ประกาศตัวแปรนับไว้ที่นี่ (เริ่มที่ 0)
            $sum_app = 0; $sum_wait = 0; $sum_doc = 0; $sum_rej = 0; 
            ?>

            <?php foreach ($teams as $index => $team): ?>
            <tr> 
                <td><?= $index + 1 ?></td> 
                <td class="text-left"><strong><?= esc($team['team_name']) ?></strong></td> 
                
                <td>
                    <?php if($team['approved_status'] == '✓'): $sum_app++; ?>
                        <img src="<?= $checkImg ?>" class="img-check">
                    <?php else: echo '-'; endif; ?>
                </td>

                <td>
                    <?php if($team['waiting_status'] == '✓'): $sum_wait++; ?>
                        <img src="<?= $checkImg ?>" class="img-check">
                    <?php else: echo '-'; endif; ?>
                </td>

                <td>
                    <?php if($team['document_status'] == '✓'): $sum_doc++; ?>
                        <img src="<?= $checkImg ?>" class="img-check">
                    <?php else: echo '-'; endif; ?>
                </td>

                <td>
                    <?php if($team['rejected_status'] == '✓'): $sum_rej++; ?>
                        <img src="<?= $checkImg ?>" class="img-check">
                    <?php else: echo '-'; endif; ?>
                </td>
            </tr> 
            <?php endforeach; ?>
        </tbody>

        <tfoot>
            <tr style="background-color: #F3B71B; font-weight: bold; color: #06346D;">
                <td colspan="2" style="text-align: center;"><?= lang('Common.total') ?></td>
                <td><?= $sum_app ?></td>
                <td><?= $sum_wait ?></td>
                <td><?= $sum_doc ?></td>
                <td><?= $sum_rej ?></td>
            </tr>
        </tfoot>
    </table> 
</div> 

</body> 
</html>