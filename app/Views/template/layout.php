<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="<?= csrf_token() ?>" content="<?= csrf_hash() ?>">
    <script>
    /* เติม X-Requested-With ให้ fetch() same-origin อัตโนมัติ — กัน CI4 เก็บ AJAX เป็น _ci_previous_url
       ซึ่งทำให้ redirect()->back() เด้งไปหน้า JSON (แก้ทั้งคลาสบั๊กในที่เดียว) */
    (function () {
        if (!window.fetch || window.__fetchXrwPatched) return;
        window.__fetchXrwPatched = true;
        var _fetch = window.fetch;
        window.fetch = function (input, init) {
            init = init || {};
            try {
                var url = (typeof input === 'string') ? input : (input && input.url) || '';
                var u = new URL(url, window.location.href);
                if (u.origin === window.location.origin) {
                    var h = new Headers(init.headers || {});
                    if (!h.has('X-Requested-With')) h.set('X-Requested-With', 'XMLHttpRequest');
                    init.headers = h;
                }
            } catch (e) {}
            return _fetch.call(this, input, init);
        };
    })();
    </script>
    <link rel="icon" href="<?= base_url('public/img/jootball_logo.png') ?>" type="image/icon type">
    <title>Jootball</title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/styles.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/myStyle.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/js/DatepickerThai/css/datepicker.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/dataTables.dataTables.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/font-awesome_4.7/css/font-awesome.min.css') ?>">
    <!-- Font Awesome 6 for modern icons (fas, far, fab) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" integrity="sha512-DTOQO9RWCH3ppGqcWaEA1BIZOC6xxalwEsw9c2QQeAIftl+Vegovlnee1c9QX4TctnWMn13TZye+giMm8e2LwA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="<?php echo base_url('public/css/jqueryBracket/jquery.bracket.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/select2.min.css') ?>">

    <!-- Google Fonts: Kanit for Thai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/css/kanit.css') ?>">

    <link rel="stylesheet" href="<?php echo base_url('public/css/cropper.min.css') ?>" />

    <style>
        /* Language Switcher Styles */
        :root {
            --bs-primary: #16478C;
            --bs-primary-rgb: 22, 71, 140;
            --bs-warning: #F2B81B;
            --bs-warning-rgb: 242, 184, 27;
        }

        .btn-primary {
            --bs-btn-bg: #16478C;
            --bs-btn-border-color: #16478C;
            --bs-btn-hover-bg: #123870;
            --bs-btn-hover-border-color: #103366;
        }

        .btn-warning {
            --bs-btn-bg: #F2B81B;
            --bs-btn-border-color: #F2B81B;
            --bs-btn-hover-bg: #d9a418;
            --bs-btn-hover-border-color: #cc9a17;
            color: #000; /* Ensure text is readable on yellow */
        }

        .text-primary {
            color: #16478C !important;
        }

        .bg-primary {
            background-color: #16478C !important;
        }

        .flag-icon {
            font-size: 1.2rem;
            line-height: 1;
        }

        .language-dropdown {
            min-width: 200px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid #e2e8f0;
            padding: 0.5rem 0;
        }

        .language-dropdown .dropdown-item {
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0.25rem 0.5rem;
        }

        .language-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, #16478C 0%, #123870 100%);
            color: #ffffff;
            transform: translateX(5px);
        }

        .language-dropdown .dropdown-item.active {
            background: linear-gradient(135deg, #F2B81B 0%, #d9a418 100%);
            color: #000000;
            font-weight: 600;
        }

        .language-dropdown .dropdown-item.active:hover {
            background: linear-gradient(135deg, #d9a418 0%, #F2B81B 100%);
        }

        #languageDropdown {
            color: white;
            background: rgb(255 255 255 / 10%);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        #languageDropdown:hover {
            background: rgba(22, 71, 140, 0.2);
            transform: translateY(-2px);
            border-color: #16478C;
        }

        /* Force white text on primary background headers */
        thead.bg-primary th,
        thead.bg-primary td,
        .bg-primary {
            color: #ffffff !important;
        }

        /* Ensure DataTables sort icons and other elements in header are also white */
        thead.bg-primary th *,
        thead.bg-primary td * {
            color: #ffffff !important;
        }

        /* Notification Dropdown Styles */
        .notification-bell {
            position: relative;
            padding: 8px 12px;
            transition: all 0.3s ease;
        }

        .notification-bell:hover {
            opacity: 0.8;
        }

        .notification-dropdown {
            min-width: 420px;
            max-width: 500px;
            padding: 0;
            border: none;
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border-radius: 0.5rem;
        }

        .notification-header {
            padding: 0.75rem 1rem;
            background-color: #f8f9fa;
            border-bottom: 1px solid #e9ecef;
            font-weight: 600;
            border-top-left-radius: 0.5rem;
            border-top-right-radius: 0.5rem;
        }

        .notification-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .notification-item {
            display: flex;
            align-items: start;
            padding: 0.75rem 1rem;
            border-bottom: 1px solid #f0f0f0;
            text-decoration: none;
            color: #333;
            transition: background-color 0.2s;
        }

        .notification-item:hover {
            background-color: #f8f9fa;
            color: #333;
        }

        .notification-item.is-read {
            opacity: 0.65;
        }
        .notification-item:not(.is-read) {
            background-color: rgba(22, 71, 141, 0.04);
        }

        .notification-item:last-child {
            border-bottom: none;
        }

        .notification-icon {
            flex-shrink: 0;
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background-color: #e9ecef;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 0.75rem;
            overflow: hidden;
        }

        .notification-icon img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .notification-content {
            flex-grow: 1;
        }

        .notification-title {
            font-size: 0.9rem;
            font-weight: 600;
            margin-bottom: 0.1rem;
        }

        .notification-desc {
            font-size: 0.85rem;
            color: #6c757d;
            margin-bottom: 0.2rem;
        }

        .notification-time {
            font-size: 0.75rem;
            color: #adb5bd;
        }

        .notification-footer {
            padding: 0.5rem;
            text-align: center;
            background-color: #f8f9fa;
            border-top: 1px solid #e9ecef;
            border-bottom-left-radius: 0.5rem;
            border-bottom-right-radius: 0.5rem;
        }

        .notification-footer a {
            font-size: 0.9rem;
            color: #16478C;
            text-decoration: none;
            font-weight: 500;
        }

        .notification-footer a:hover {
            text-decoration: underline;
        }

        .admin-impersonation-bar {
            background: #fff3cd;
            border-bottom: 1px solid #f2d98d;
            color: #6b5200;
            padding: 10px 16px;
            font-size: 14px;
        }

        .admin-impersonation-bar a {
            color: #16478C;
            font-weight: 700;
            text-decoration: none;
        }

        /* TikTok Premium Floating Pulse */
        .tiktok-floating-btn {
            position: fixed;
            top: 150px;
            right: 20px;
            z-index: 10000;
            display: flex;
            align-items: center;
            flex-direction: row-reverse;
            text-decoration: none !important;
            transition: all 0.4s cubic-bezier(0.23, 1, 0.32, 1);
        }

        .tiktok-icon-wrapper {
            width: 65px;
            height: 65px;
            background: #000;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            z-index: 2;
            box-shadow: 0 0 0 0 rgba(0, 242, 234, 0.7), 0 0 0 0 rgba(255, 0, 80, 0.7);
            animation: tiktok-pulse 2.5s infinite;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .tiktok-icon-wrapper i {
            color: #fff;
            font-size: 1.6rem;
            filter: drop-shadow(2px 2px 0px #ff0050) drop-shadow(-2px -2px 0px #00f2ea);
        }

        .tiktok-text-label {
            background: #000;
            color: #fff;
            padding: 10px 35px 10px 25px;
            border-radius: 30px 0 0 30px;
            margin-right: -25px;
            font-size: 0.95rem;
            font-weight: 500;
            opacity: 0;
            transform: translateX(20px);
            transition: all 0.4s ease;
            white-space: nowrap;
            pointer-events: none;
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-right: none;
            font-family: 'Kanit', sans-serif;
        }

        .tiktok-floating-btn:hover .tiktok-text-label {
            opacity: 1;
            transform: translateX(0);
            margin-right: -5px;
        }

        .tiktok-floating-btn:hover .tiktok-icon-wrapper {
            transform: scale(1.1) rotate(10deg);
            animation: none;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.5);
        }

        @keyframes tiktok-pulse {
            0% { box-shadow: 0 0 0 0 rgba(0, 242, 234, 0.6), 0 0 0 0 rgba(255, 0, 80, 0.6); }
            70% { box-shadow: 0 0 0 15px rgba(0, 242, 234, 0), 0 0 0 10px rgba(255, 0, 80, 0); }
            100% { box-shadow: 0 0 0 0 rgba(0, 242, 234, 0), 0 0 0 0 rgba(255, 0, 80, 0); }
        }

        @media (max-width: 768px) {
            .tiktok-floating-btn {
                top: auto;
                bottom: 100px;
                right: 15px;
            }
            .tiktok-icon-wrapper {
                width: 48px;
                height: 48px;
            }
            .tiktok-text-label {
                display: none;
            }
        }
    
    /* DELETE_ME_START */
    @media (max-width: 1024px) {
        .modal-dialog {
            display: flex;
            align-items: center;
            min-height: calc(100% - 3.5rem);
            margin: 1.75rem auto !important;
            max-width: 95% !important;
        }
        .modal-content {
            max-height: 90vh !important;
            overflow: hidden !important;
            display: flex;
            flex-direction: column !important;
            border-radius: 12px !important;
        }
        .modal-body {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
    }

    /* --- Global Modal Fix (v3) --- */
    @media (max-width: 1024px) {
        /* ทำงานเฉพาะตอนที่ Modal ถูกสั่งแสดงผลแล้วเท่านั้น เพื่อไม่ให้ขวางจังหวะกด */
        .modal.show .modal-dialog {
            display: flex !important;
            align-items: center !important;
            min-height: calc(100% - 3.5rem) !important;
            margin: 1.75rem auto !important;
            max-width: 95% !important;
        }
        .modal-content {
            max-height: 85vh !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
        }
        .modal-body {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            flex: 1 1 auto !important;
        }
    }
</style>
</head>
<?php $this->renderSection('style'); ?>

<?php
$url = uri_string();
$control = explode('/', $url);
?>

<body class="kanit-regular">
<?= $this->include('partials/preloader') ?>
<?= $this->include('partials/announcement') ?>

    <?php $frontendImpersonation = session()->get('frontend_impersonation'); ?>
    <?php if (!empty($frontendImpersonation) && session()->get('isAdminLoggedIn')): ?>
        <div class="admin-impersonation-bar text-center">
            กำลังทดสอบหน้าบ้านในฐานะ <?= esc($frontendImpersonation['type'] ?? 'user') ?>:
            <strong><?= esc($frontendImpersonation['label'] ?? '-') ?></strong>
            | <a href="<?= base_url('admin/return-to-admin') ?>">กลับสู่หลังบ้าน</a>
        </div>
    <?php endif; ?>

    <!-- MODAL PDPA -->
    <div class="modal fade"
        id="pdpaModal"
        tabindex="-1"
        data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
            <div class="modal-content" style="background:#fff;border:none;border-radius:12px;">

                <!-- ✅ modal-body ครอบทั้งหมด -->
                <div class="modal-body" style="max-height:65vh; overflow-y:auto;">

                    <?php if(session()->get('language')=='th'){?>

                        <div class="pdpa-content" style="padding:30px; line-height:1.8; font-size:16px; color:#333;">
                            <h2 style="text-align:center; font-weight:700; margin-bottom:25px;">
                                ประกาศการคุ้มครองข้อมูลส่วนบุคคล (PDPA)
                            </h2>

                            <p>
                                <strong>Jootball</strong> ตระหนักถึงความสำคัญของข้อมูลส่วนบุคคลและข้อมูลอื่นอันเกี่ยวกับท่าน
                                (รวมเรียกว่า “ข้อมูล”) เพื่อให้ท่านสามารถเชื่อมั่นได้ว่า Jootball มีความโปร่งใสและความรับผิดชอบ
                                ในการเก็บรวบรวม ใช้ หรือเปิดเผยข้อมูลของท่าน ตามพระราชบัญญัติคุ้มครองข้อมูลส่วนบุคคล พ.ศ. 2562
                                (“กฎหมายคุ้มครองข้อมูลส่วนบุคคล”) รวมถึงกฎหมายอื่นที่เกี่ยวข้อง
                            </p>

                            <hr>

                            <h4 style="font-weight:600;">ข้อมูลส่วนบุคคลคืออะไร</h4>
                            <p>
                                ข้อมูลส่วนบุคคล หมายถึง ข้อมูลเกี่ยวกับบุคคลซึ่งทำให้สามารถระบุตัวบุคคลนั้นได้
                                ไม่ว่าทางตรงหรือทางอ้อม แต่ไม่รวมถึงข้อมูลของผู้ถึงแก่กรรมโดยเฉพาะ
                            </p>

                            <hr>

                            <h4 style="font-weight:600;">Jootball เก็บข้อมูลของคุณประเภทใดบ้าง</h4>
                            <p>ข้อมูลที่อาจมีการเก็บรวบรวม ได้แก่</p>

                            <ol>
                                <li>
                                    <strong>ข้อมูลส่วนบุคคลทั่วไป</strong> เช่น คำนำหน้าชื่อ ชื่อ นามสกุล
                                    เลขบัตรประชาชน สัญชาติ เลขหนังสือเดินทาง ข้อมูลทะเบียนบ้าน
                                    หมายเลขใบอนุญาตต่าง ๆ เป็นต้น
                                </li>
                                <li>
                                    <strong>ข้อมูลส่วนบุคคลที่มีความละเอียดอ่อน</strong> เช่น เชื้อชาติ ศาสนา
                                    ความพิการ ความเห็นทางการเมือง ประวัติอาชญากรรม
                                    ข้อมูลชีวภาพ (ภาพใบหน้า) ข้อมูลสุขภาพ เป็นต้น
                                </li>
                            </ol>

                            <hr>

                            <h4 style="font-weight:600;">Jootball เก็บข้อมูลผ่านช่องทางใด</h4>
                            <ol>
                                <li>จากเจ้าของข้อมูลโดยตรงผ่านช่องทางการให้บริการ</li>
                                <li>ผ่านการเข้าใช้งานเว็บไซต์</li>
                                <li>จากแหล่งข้อมูลอื่น เช่น การเชื่อมโยงบริการดิจิทัลของหน่วยงานรัฐ</li>
                            </ol>

                            <hr>

                            <h4 style="font-weight:600;">วัตถุประสงค์ในการเก็บข้อมูล</h4>
                            <ol>
                                <li>เพื่อดำเนินภารกิจเพื่อประโยชน์สาธารณะหรือการใช้อำนาจรัฐ</li>
                                <li>เพื่อปฏิบัติตามกฎหมายที่เกี่ยวข้อง</li>
                                <li>เพื่อประโยชน์โดยชอบด้วยกฎหมายของ Jootball</li>
                                <li>เพื่อปฏิบัติตามสัญญาหรือดำเนินการก่อนเข้าทำสัญญา</li>
                            </ol>

                            <hr>

                            <h4 style="font-weight:600;">การเปิดเผยข้อมูล</h4>
                            <ol>
                                <li>หน่วยงานของรัฐตามกฎหมาย</li>
                                <li>สำนักงานคณะกรรมการคุ้มครองข้อมูลส่วนบุคคล</li>
                                <li>ผู้ให้บริการที่ประมวลผลข้อมูลแทน Jootball</li>
                                <li>หน่วยงานที่มีอำนาจตามกฎหมายอื่น</li>
                            </ol>

                            <hr>

                            <h4 style="font-weight:600;">สิทธิของเจ้าของข้อมูล</h4>
                            <ul>
                                <li>สิทธิขอเข้าถึงข้อมูล</li>
                                <li>สิทธิขอแก้ไขข้อมูลให้ถูกต้อง</li>
                                <li>สิทธิขอลบหรือทำลายข้อมูล</li>
                                <li>สิทธิขอระงับการใช้ข้อมูล</li>
                                <li>สิทธิคัดค้านการประมวลผลข้อมูล</li>
                                <li>สิทธิถอนความยินยอม</li>
                            </ul>

                            <hr>

                            <h4 style="font-weight:600;">ข้อมูลเพิ่มเติม</h4>
                            <p>
                                ท่านสามารถอ่านรายละเอียดฉบับเต็มได้ที่<br>
                                <a href="#" style="color:#007bff; font-weight:600;">
                                    ประกาศการคุ้มครองข้อมูลส่วนบุคคลฉบับเต็มของ Jootball
                                </a>
                            </p>

                            <hr>

                            <h4 style="font-weight:600;">ติดต่อสอบถาม</h4>
                            <p>
                                หากมีข้อสงสัยเกี่ยวกับนโยบายนี้ สามารถติดต่อได้ที่
                            </p>

                            <p>
                                📧 อีเมล: <a href="mailto:jootball@gmail.com">jootball@gmail.com</a><br>
                                📞 โทรศัพท์: 086-586-2599
                            </p>

                            <!-- ตัดเนื้อหาไว้เหมือนเดิม ใส่ต่อได้เลย -->

                        </div>

                    <?php } else if(session()->get('language')=='en'){?>

                        <div class="privacy-content" style="padding:30px; line-height:1.8; font-size:16px; color:#333;">
                            <h2 style="text-align:center; font-weight:700; margin-bottom:25px;">
                                Privacy Notice
                            </h2>

                            <p>
                                <strong>Jootball</strong> recognizes the importance of personal data protection and is committed to ensuring
                                transparency and accountability in the collection, use, and disclosure of personal data in accordance with
                                the Personal Data Protection Act B.E. 2562 (2019) (“PDPA”) and other applicable laws.
                            </p>

                            <p>
                                This Privacy Notice (“Notice”) explains how Jootball collects, uses, discloses, and processes personal data,
                                including the roles of its officers, employees, and authorized persons acting on its behalf.
                            </p>

                            <hr>

                            <h4><strong>1. Definition of Personal Data</strong></h4>
                            <p>
                                “Personal Data” means any information relating to an individual which enables the identification of such
                                individual, whether directly or indirectly, but excludes information relating specifically to deceased persons.
                            </p>

                            <hr>

                            <h4><strong>2. Categories of Personal Data Collected</strong></h4>
                            <p>
                                Depending on the services requested and the nature of the relationship with Jootball,
                                we may collect the following categories of personal data:
                            </p>

                            <h5><strong>2.1 General Personal Data</strong></h5>
                            <ul>
                                <li>Title, first name, last name, middle name, nickname</li>
                                <li>Signature</li>
                                <li>National identification number</li>
                                <li>Nationality</li>
                                <li>Driver’s license number</li>
                                <li>Passport number</li>
                                <li>Household registration information</li>
                                <li>Business registration number</li>
                                <li>Professional license number</li>
                                <li>Insured person number</li>
                                <li>Social security number</li>
                                <li>Other information necessary for service provision</li>
                            </ul>

                            <h5><strong>2.2 Sensitive Personal Data</strong></h5>
                            <p>
                                Where necessary and permitted by law, Jootball may collect sensitive personal data, including:
                            </p>
                            <ul>
                                <li>Racial or ethnic data</li>
                                <li>Religious information</li>
                                <li>Disability information</li>
                                <li>Political opinions</li>
                                <li>Criminal records</li>
                                <li>Biometric data (e.g., facial recognition data)</li>
                                <li>Genetic data</li>
                                <li>Health information</li>
                            </ul>
                            <p>
                                Sensitive Personal Data will be collected only where strictly necessary and with explicit consent
                                or other lawful basis under the PDPA.
                            </p>

                            <hr>

                            <h4><strong>3. Sources of Personal Data</strong></h4>
                            <ul>
                                <li>Directly from you through our service channels</li>
                                <li>Through your use of our website</li>
                                <li>From third parties or external sources, including government digital service linkages, where legally permitted</li>
                            </ul>

                            <hr>

                            <h4><strong>4. Purposes and Legal Bases for Processing</strong></h4>

                            <h5><strong>4.1 Public Interest or Official Authority</strong></h5>
                            <p>
                                To perform tasks carried out in the public interest or in the exercise of official authority vested in
                                Jootball under applicable laws, regulations, orders, or Cabinet resolutions.
                            </p>

                            <h5><strong>4.2 Legal Obligation</strong></h5>
                            <p>
                                To comply with legal obligations to which Jootball is subject.
                            </p>

                            <h5><strong>4.3 Legitimate Interests</strong></h5>
                            <p>
                                For the legitimate interests of Jootball or third parties, provided such interests are not overridden
                                by the fundamental rights of the data subject (e.g., user account registration and system access).
                            </p>

                            <h5><strong>4.4 Contractual Necessity</strong></h5>
                            <p>
                                For the performance of a contract to which you are a party,
                                or to take steps at your request prior to entering into a contract.
                            </p>

                            <p>
                                Where required by law, Jootball will obtain your explicit consent before or at the time of collection.
                            </p>

                            <hr>

                            <h4><strong>5. Disclosure of Personal Data</strong></h4>
                            <ul>
                                <li>Government agencies authorized under the PDPA</li>
                                <li>Personal data regulatory authorities (e.g., the Office of the Personal Data Protection Committee)</li>
                                <li>Data processors or service providers engaged by Jootball</li>
                                <li>Other legally authorized entities for audit or compliance purposes</li>
                            </ul>
                            <p>
                                All disclosures will be made on a need-to-know basis and subject to appropriate safeguards.
                            </p>

                            <hr>

                            <h4><strong>6. Your Rights as a Data Subject</strong></h4>
                            <ul>
                                <li>Right of access</li>
                                <li>Right to rectification</li>
                                <li>Right to erasure</li>
                                <li>Right to restriction of processing</li>
                                <li>Right to object</li>
                                <li>Right to withdraw consent</li>
                                <li>Right to data portability</li>
                            </ul>

                            <p>
                                Requests to exercise these rights will be handled in accordance with applicable law.
                            </p>

                            <hr>

                            <h4><strong>7. Additional Information</strong></h4>
                            <p>
                                You may review the full version of Jootball’s Privacy Notice at:
                            </p>
                            <p>
                                <a href="#" style="color:#007bff; font-weight:600;">
                                    Jootball Full Privacy Notice — Click here
                                </a>
                            </p>

                            <hr>

                            <h4><strong>8. Contact Information</strong></h4>
                            <p>
                                If you have any questions, concerns, or requests regarding this Notice or the processing of your personal data,
                                please contact:
                            </p>

                            <p>
                                📧 Email: <a href="mailto:jootball@gmail.com">jootball@gmail.com</a><br>
                                📞 Telephone: 086-586-2599
                            </p>

                        </div>

                    <?php }?>

                </div>
                <!-- ✅ ปิด modal-body ตรงนี้ถูกต้อง -->

                <div class="modal-footer text-center" style="border-top:0;flex-direction:column;">

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" checked
                            id="pdpaDontShow" style="width:18px;height:18px;">
                        <label class="form-check-label" for="pdpaDontShow"
                            style="padding-left:5px;font-size:18px;">
                            <?php if(session()->get('language')=='th'){?>
                                อ่านแล้ว และไม่ต้องแสดงอีก
                            <?php } else if(session()->get('language')=='en'){?>
                                I have read this and do not show again
                            <?php }?>
                        </label>
                    </div>

                    <div>
                        <button type="button" id="btnPdpaOk"
                            class="btn btn-success btn-lg w-100">
                            OK
                        </button>
                    </div>

                </div>

            </div>
        </div>
    </div>
    <!-- END MODAL PDPA -->

    <header>
        <nav class="navbar navbar-expand-lg px-3 kanit-medium bg-secondary">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="<?= base_url('/') ?>">
                    <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="" height="70px">
                </a>
                <!-- Navbar Items -->
                <div class="row">
                    <div class="col-md-12">
                        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                            <ul class="navbar-nav align-items-center">
                                <!-- Language Switcher -->
                                <li class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="gap: 0.5rem;">
                                        <?php
                                        helper('language');
                                        $currentLang = get_current_language();
                                        if ($currentLang == 'th') {
                                            echo '<span class="flag-icon">🇹🇭</span><span>ไทย</span>';
                                        } else {
                                            echo '<span class="flag-icon">🇬🇧</span><span>EN</span>';
                                        }
                                        ?>
                                    </a>
                                    <ul class="dropdown-menu dropdown-menu-end language-dropdown" aria-labelledby="languageDropdown">
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center <?= $currentLang == 'th' ? 'active' : '' ?>" href="<?= lang_url('th') ?>" style="gap: 0.75rem;">
                                                <span class="flag-icon" style="font-size: 1.5rem;">🇹🇭</span>
                                                <span>ภาษาไทย</span>
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item d-flex align-items-center <?= $currentLang == 'en' ? 'active' : '' ?>" href="<?= lang_url('en') ?>" style="gap: 0.75rem;">
                                                <span class="flag-icon" style="font-size: 1.5rem;">🇬🇧</span>
                                                <span>English</span>
                                            </a>
                                        </li>
                                    </ul>
                                </li>
                                <?php
                                $userIdForNotif = session()->get('user_id');
                                if (!empty($userIdForNotif) && session()->get('user_type') !== 'coach') {
                                    $_db = db_connect();
                                    $aggregatedRequests = [];

                                    // 1. ทีมที่ user เป็นเจ้าของ
                                    $ownedTeamIds = array_column(
                                        $_db->table('team')->select('id')->where('create_by', $userIdForNotif)->get()->getResultArray(),
                                        'id'
                                    );
                                    $ownedTeamIds = array_map('intval', $ownedTeamIds);

                                    // 2. academy ที่ user เป็น owner/admin
                                    if (session()->get('is_super_academy')) {
                                        // Super academy operator → scope notification ตาม current academy เท่านั้น
                                        $cur = (int)session()->get('current_academy_id');
                                        $ownedAcademyIds = $cur > 0 ? [$cur] : [];
                                    } else {
                                        $ownedAcademyIds = array_column(
                                            $_db->table('academy_members')
                                                ->select('academy_id')
                                                ->where('user_id', $userIdForNotif)
                                                ->whereIn('role', ['owner', 'admin'])
                                                ->where('status', 'approved')
                                                ->get()->getResultArray(),
                                            'academy_id'
                                        );
                                        $ownedAcademyIds = array_map('intval', $ownedAcademyIds);
                                    }

                                    // ----- player join requests (team_managers) -----
                                    if (!empty($ownedTeamIds)) {
                                        $rows = $_db->table('team_managers tm')
                                            ->select('tm.id, tm.team_id, tm.created_at, u.name, u.profile_image, t.name AS team_name')
                                            ->join('user u', 'u.id = tm.user_id', 'left')
                                            ->join('team t', 't.id = tm.team_id', 'left')
                                            ->whereIn('tm.team_id', $ownedTeamIds)
                                            ->where('tm.status', 'pending')
                                            ->orderBy('tm.created_at', 'DESC')
                                            ->get()->getResultArray();
                                        foreach ($rows as $r) {
                                            $aggregatedRequests[] = [
                                                'kind'     => 'team_player',
                                                'title'    => lang('Common.team_join_request'),
                                                'desc'     => ($r['name'] ?? '-') . ' ' . lang('Common.requested_to_join') . ' ' . ($r['team_name'] ?? ''),
                                                'image'    => !empty($r['profile_image']) ? base_url($r['profile_image']) : base_url('public/img/avatar.png'),
                                                'time'     => $r['created_at'],
                                                'href'     => base_url('team/requests/' . (int)$r['team_id']),
                                            ];
                                        }
                                    }

                                    // ----- coach join requests (coach_team_requests) จากทีมที่ owner หรือ academy -----
                                    if ($_db->tableExists('coach_team_requests')) {
                                        $coachReqTeamIds = $ownedTeamIds;
                                        if (!empty($ownedAcademyIds)) {
                                            $atRows = $_db->table('academy_teams')
                                                ->select('team_id')
                                                ->whereIn('academy_id', $ownedAcademyIds)
                                                ->get()->getResultArray();
                                            foreach ($atRows as $row) {
                                                $coachReqTeamIds[] = (int)$row['team_id'];
                                            }
                                            $coachReqTeamIds = array_values(array_unique($coachReqTeamIds));
                                        }
                                        if (!empty($coachReqTeamIds)) {
                                            $rows = $_db->table('coach_team_requests ctr')
                                                ->select('ctr.id, ctr.team_id, ctr.requested_at, c.first_name, c.last_name, c.image_path, t.name AS team_name')
                                                ->join('coach c', 'c.id = ctr.coach_id', 'left')
                                                ->join('team t', 't.id = ctr.team_id', 'left')
                                                ->whereIn('ctr.team_id', $coachReqTeamIds)
                                                ->where('ctr.status', 'pending')
                                                ->where('ctr.is_active', 1)
                                                ->orderBy('ctr.requested_at', 'DESC')
                                                ->get()->getResultArray();
                                            foreach ($rows as $r) {
                                                $coachName = trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: lang('Common.coach');
                                                $aggregatedRequests[] = [
                                                    'kind'     => 'team_coach',
                                                    'title'    => lang('Common.coach_join_requests'),
                                                    'desc'     => $coachName . ' ' . lang('Common.requested_to_join') . ' ' . ($r['team_name'] ?? ''),
                                                    'image'    => !empty($r['image_path']) ? base_url($r['image_path']) : base_url('public/img/avatar.png'),
                                                    'time'     => $r['requested_at'],
                                                    'href'     => base_url('team/coach-requests/' . (int)$r['team_id']),
                                                ];
                                            }
                                        }
                                    }

                                    // ----- academy member requests (academy_members status=pending) -----
                                    if (!empty($ownedAcademyIds)) {
                                        $rows = $_db->table('academy_members am')
                                            ->select('am.id, am.academy_id, am.created_at, am.role,
                                                      u.name AS user_name, u.profile_image,
                                                      c.first_name, c.last_name, c.image_path,
                                                      a.name AS academy_name')
                                            ->join('user u', 'u.id = am.user_id', 'left')
                                            ->join('coach c', 'c.coach_user_id = am.coach_user_id', 'left')
                                            ->join('academy a', 'a.id = am.academy_id', 'left')
                                            ->whereIn('am.academy_id', $ownedAcademyIds)
                                            ->where('am.status', 'pending')
                                            ->orderBy('am.created_at', 'DESC')
                                            ->get()->getResultArray();
                                        foreach ($rows as $r) {
                                            $name = $r['user_name'] ?: trim(($r['first_name'] ?? '') . ' ' . ($r['last_name'] ?? '')) ?: '-';
                                            $img  = $r['profile_image'] ?: $r['image_path'] ?: '';
                                            $aggregatedRequests[] = [
                                                'kind'     => 'academy_member',
                                                'title'    => $r['role'] === 'coach' ? lang('Common.coach_join_requests') : lang('Common.player_join_requests'),
                                                'desc'     => $name . ' ' . lang('Common.requested_to_join') . ' ' . ($r['academy_name'] ?? ''),
                                                'image'    => $img ? base_url($img) : base_url('public/img/avatar.png'),
                                                'time'     => $r['created_at'],
                                                'href'     => $r['role'] === 'coach'
                                                                ? base_url('academy/coach-requests/' . (int)$r['academy_id'])
                                                                : base_url('academy/requests/' . (int)$r['academy_id']),
                                            ];
                                        }
                                    }

                                    // sort newest first
                                    usort($aggregatedRequests, function($a, $b) {
                                        return strtotime($b['time'] ?? '0') <=> strtotime($a['time'] ?? '0');
                                    });
                                    $pendingCount = count($aggregatedRequests);

                                    try {
                                        $notifModel = new \Modules\Coach\Models\NotificationModel();
                                        $userNotifs = $notifModel->getAllByUser($userIdForNotif, 10);
                                        $userUnread = (int)$notifModel->getUnreadCountByUser($userIdForNotif);
                                    } catch (\Throwable $e) {
                                        $userNotifs = [];
                                        $userUnread = 0;
                                    }
                                    $totalCount = $pendingCount + $userUnread;
                                ?>
                                    <li class="nav-item dropdown me-2">
                                        <a class="nav-link notification-bell text-white position-relative" href="#" id="teamNotificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fa-solid fa-bell" style="font-size: 1.5rem;"></i>
                                            <span id="teamNotifBadge" class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem; transform: translate(-50%, 50%) !important; <?= $totalCount > 0 ? '' : 'display:none;' ?>">
                                                <?= $totalCount > 99 ? '99+' : $totalCount ?>
                                                <span class="visually-hidden">unread notifications</span>
                                            </span>
                                        </a>
                                        <div class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="teamNotificationDropdown">
                                            <div class="notification-header border-bottom d-flex justify-content-between align-items-center">
                                                <span><?= lang('Common.notifications') ?></span>
                                                <a href="#" id="teamNotifMarkAllRead" class="small text-decoration-none" style="font-size: 0.78rem;"><?= lang('Common.notification_mark_all_read') ?></a>
                                            </div>
                                            <div class="notification-list" id="teamNotifList">
                                                <?php
                                                $hasItems = $pendingCount > 0 || count($userNotifs) > 0;
                                                foreach ($aggregatedRequests as $req) {
                                                    $reqTime = !empty($req['time']) ? date('d/m/Y H:i', strtotime($req['time'])) : '';
                                                ?>
                                                    <a href="<?= esc($req['href']) ?>" class="notification-item">
                                                        <div class="notification-icon">
                                                            <img src="<?= esc($req['image']) ?>" alt="User" onerror="this.src='<?= base_url('public/img/avatar.png') ?>'">
                                                        </div>
                                                        <div class="notification-content">
                                                            <div class="notification-title"><?= esc($req['title']) ?></div>
                                                            <div class="notification-desc"><?= esc($req['desc']) ?></div>
                                                            <?php if ($reqTime): ?>
                                                            <div class="notification-time"><i class="far fa-clock me-1"></i><?= $reqTime ?></div>
                                                            <?php endif; ?>
                                                        </div>
                                                    </a>
                                                <?php }
                                                foreach ($userNotifs as $notif) {
                                                    $isRead = !empty($notif['is_read']);
                                                    $time = date('d/m/Y H:i', strtotime($notif['created_at']));
                                                ?>
                                                    <a href="#" class="notification-item team-notif-item <?= $isRead ? 'is-read' : '' ?>" data-notif-id="<?= (int)$notif['id'] ?>">
                                                        <div class="notification-icon">
                                                            <i class="fa-solid fa-bell text-primary"></i>
                                                        </div>
                                                        <div class="notification-content">
                                                            <div class="notification-title"><?= esc($notif['title']) ?></div>
                                                            <div class="notification-desc"><?= esc($notif['message']) ?></div>
                                                            <div class="notification-time">
                                                                <i class="far fa-clock me-1"></i><?= $time ?>
                                                            </div>
                                                        </div>
                                                    </a>
                                                <?php }
                                                if (!$hasItems) { ?>
                                                    <div class="text-center py-4 text-muted">
                                                        <i class="far fa-bell-slash fa-2x mb-2"></i>
                                                        <p class="mb-0 small"><?= lang('Common.notification_no_items') ?></p>
                                                    </div>
                                                <?php } ?>
                                            </div>
                                            <div class="notification-footer">
                                                <a href="<?= base_url('team/requests') ?>"><?= lang('Common.notification_view_all') ?></a>
                                            </div>
                                        </div>
                                    </li>
                                    <script>
                                        (function() {
                                            const bellBadge = document.getElementById('teamNotifBadge');
                                            const list      = document.getElementById('teamNotifList');
                                            const markAll   = document.getElementById('teamNotifMarkAllRead');
                                            if (!bellBadge) return;

                                            const COUNT_URL    = '<?= base_url('team/notifications/count') ?>';
                                            const MARK_URL     = '<?= base_url('team/notifications/mark-read') ?>';
                                            const MARK_ALL_URL = '<?= base_url('team/notifications/mark-all-read') ?>';

                                            function setBadge(n) {
                                                if (!bellBadge) return;
                                                if (n > 0) {
                                                    bellBadge.textContent = n > 99 ? '99+' : n;
                                                    bellBadge.style.display = '';
                                                } else {
                                                    bellBadge.style.display = 'none';
                                                }
                                            }

                                            async function refreshCount() {
                                                try {
                                                    const r = await fetch(COUNT_URL, { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                                    if (!r.ok) return;
                                                    const j = await r.json();
                                                    if (j && j.status === 'success') setBadge(parseInt(j.count || 0, 10));
                                                } catch (e) { /* silent */ }
                                            }

                                            // Mark single notification as read on click
                                            if (list) {
                                                list.addEventListener('click', async function(ev) {
                                                    const item = ev.target.closest('.team-notif-item');
                                                    if (!item) return;
                                                    const id = item.getAttribute('data-notif-id');
                                                    if (!id || item.classList.contains('is-read')) return;
                                                    try {
                                                        const fd = new FormData();
                                                        fd.append('notification_id', id);
                                                        await fetch(MARK_URL, { method: 'POST', body: fd, credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                                        item.classList.add('is-read');
                                                        refreshCount();
                                                    } catch (e) { /* silent */ }
                                                });
                                            }

                                            // Mark all as read
                                            if (markAll) {
                                                markAll.addEventListener('click', async function(ev) {
                                                    ev.preventDefault();
                                                    ev.stopPropagation();
                                                    try {
                                                        await fetch(MARK_ALL_URL, { method: 'POST', credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                                                        document.querySelectorAll('#teamNotifList .team-notif-item').forEach(el => el.classList.add('is-read'));
                                                        refreshCount();
                                                    } catch (e) { /* silent */ }
                                                });
                                            }

                                            // Poll every 30 seconds
                                            setInterval(refreshCount, 30000);
                                        })();
                                    </script>
                                <?php } ?>
                                <?php if (session()->get('is_super_academy')): ?>
                                    <?php
                                        $_curName = (string)session()->get('current_academy_name');
                                        if ($_curName !== '') $_curName = mb_strimwidth($_curName, 0, 20, '...');
                                    ?>
                                    <li class="nav-item me-2">
                                        <a href="<?= base_url('academy/select') ?>"
                                           class="btn btn-warning btn-sm d-flex align-items-center"
                                           style="white-space:nowrap">
                                            🔄 เปลี่ยน <?= $_curName !== '' ? '['.esc($_curName).']' : 'Academy' ?>
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (!empty(session()->get('user_id')) || !empty(session()->get('coach_id'))) { ?>
                                    <li class="nav-item">
                                        <a class="nav-link text-white" href="<?= base_url('auth/logout') ?>">
                                            <img src="<?= @session()->get('profile')->picture ?>" alt="" class="img-fluid rounded-circle mx-2" style="width: 50px;">
                                            <?= lang('Common.logout') ?></a>
                                    </li>
                                <?php } else { ?>
                                    <!-- Login buttons removed from desktop view -->
                                <?php } ?>
                            </ul>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
                            <ul class="navbar-nav align-items-center">
                            </ul>
                        </div>
                    </div>
                </div>

                <div class="row d-lg-none d-sm-none d-md-block">
                    <div class="col-md-12">
                        <?php if (!empty(session()->get('user_id')) || !empty(session()->get('coach_id'))) { ?>
                            <a class="nav-link text-white" href="<?= base_url('auth/logout') ?>">
                                <img src="<?= @session()->get('profile')->picture ?>" alt="" class="img-fluid rounded-circle mx-2" style="width: 50px;">
                                ออกจากระบบ</a>
                        <?php } else { ?>
                                <a class="btn btn-primary rounded-pill mx-1" href="<?= base_url('/') ?>">เข้าสู่ระบบ</a>
                            <a class="btn btn-success rounded-pill mx-1" href="<?= isset($link) ? $link : '#' ?>">Line Login</a>
                        <?php } ?>
                    </div>
                </div>

            </div>
        </nav>
        <?php if ((!empty(session()->get('user_id')) || !empty(session()->get('coach_id'))) && session()->get('user_type') !== 'coach') { ?>
            <?php
                // Phase 8: กำหนด URL หลักตาม Academy หรือ Team
                $_homeUrl = getUserTeamAcademyUrl() ?: base_url('team/info');
                $_isAcademyUser = (getUserAcademyId() !== null);
                $_academyId = getUserAcademyId();
                $_myTeamUrl = $_academyId ? base_url('academy/teams/' . $_academyId) : base_url('team/info');
            ?>
            <div class="container kanit-medium">
                <ul class="nav justify-content-evenly mt-3 px-4 py-2 border border-3 rounded-pill">
                    <li class="nav-item">
                        <a class="nav-link <?= ($control[0] == "" || ($_isAcademyUser && $control[0] == 'academy' && ($control[1] ?? '') == 'info')) ? "active-nav" : "" ?>" href="<?= $_homeUrl ?>"><?= lang('Common.home') ?></a>
                    </li>
                    <?php if (empty(session()->get('team.id')) && !$_isAcademyUser) { ?>
                        <!-- <li class="nav-item">
                            <a class="nav-link <?= ($control[0] == "team") ? "active-nav" : "" ?>" href="<?= base_url('team/register') ?>"><?= lang('Common.create_team') ?></a>
                        </li> -->
                    <?php } else {  ?>
                        <li class="nav-item">
                            <?php
                                $myTeamActive = false;
                                if ($control[0] == 'academy' && in_array($control[1] ?? '', ['teams', 'team-info', 'team-player', 'team-coach', 'player-list', 'player-chart', 'coaches'])) {
                                    $myTeamActive = true;
                                } elseif (($control[0] == 'team' || $control[0] == 'player') && in_array($control[1] ?? '', ['info', 'player-list', 'coach-list'])) {
                                    $myTeamActive = true;
                                }
                            ?>
                            <a class="nav-link <?= $myTeamActive ? 'active-nav' : '' ?>" href="<?= $_myTeamUrl ?>"><?= lang('Common.my_team') ?></a>
                        </li>
                    <?php } ?>
                    <?php if (session()->get('user_type') !== 'coach') { ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($control[0] == "auth" && ($control[1] == "tournament" || $control[2] == "tournament")) ? "active-nav" : "" ?>" href="<?= base_url('auth/tournament/list') ?>"><?= lang('Common.tournament') ?></a>
                    </li>
                    <?php } ?>
                    <li class="nav-item">
                        <a class="nav-link <?= ($control[0] == "team" && $control[1] == "user-manage") ? "active-nav" : "" ?>" href="<?= base_url('team/user-manage') ?>"><?= lang('Common.my_account') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('privacy') ?>"><?= lang('Common.privacy_policy') ?></a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#"><?= lang('Common.match_schedule') ?></a>
                    </li> -->
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#"><?= lang('Common.score_table') ?></a>
                    </li> -->
                    <!-- <li class="nav-item">
                        <a class="nav-link" href="#">Scout</a>
                    </li> -->
                </ul>
            </div>
        <?php } ?>
    </header>

    <div class="container pt-3">
        <?php $this->renderSection('content'); ?>
    </div>

    <script src="<?php echo base_url('public/js/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/jquery.mask.min.js') ?>"></script>

    <script src="<?php echo base_url('public/js/popper.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/popper.min.js') ?>"></script>
    <script src="<?php echo base_url("public/js/DatepickerThai/bootstrap-datepicker.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/DatepickerThai/bootstrap-datepicker-thai.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/DatepickerThai/bootstrap-datepicker.th.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/dataTables.min.js"); ?>"></script>
    <script src="<?php echo base_url('public/js/select2.min.js') ?>"></script>
    <script src="<?php echo base_url("public/js/sweetalert2@11.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/index.global.min.js"); ?>"></script>
    <script src="<?php echo base_url('public/js/cropper.min.js') ?>"></script>
    <script src="<?= base_url('public/js/jqueryBracket/jquery.bracket.min.js') ?>"></script>
    <script>
        let dtLanguage = {
            "lengthMenu": "<?= lang('Common.dt_length_menu') ?>",
            "search": "<?= lang('Common.dt_search') ?>",
            "zeroRecords": "<?= lang('Common.dt_zero_records') ?>",
            "emptyTable": "<?= lang('Common.dt_empty_table') ?>",
            "info": "<?= lang('Common.dt_info') ?>",
            "infoFiltered": "<?= lang('Common.dt_info_filtered') ?>",
            "infoEmpty": "<?= lang('Common.dt_info_empty') ?>",
            "paginate": {
                "first": "<?= lang('Common.dt_first') ?>",
                "last": "<?= lang('Common.dt_last') ?>",
                "next": "<?= lang('Common.dt_next') ?>",
                "previous": "<?= lang('Common.dt_previous') ?>"
            },
        };

        $(document).ready(function() {
            $('.phone-input').mask('000-000-0000');
            $('.shirt_number').mask('00', {
                translation: {
                    '0': {
                        pattern: /[0-9]/,
                        optional: true
                    }
                }
            });

            $('.once-only').submit(function() {
                $(".btn-submit").attr("disabled", true);
                setTimeout(() => {
                    $(".btn-submit").attr("disabled", false);
                }, 1000);
            });

            $('.date-picker').datepicker({
                format: "dd/mm/yyyy",
                autoclose: true,
                language: 'th-en',
            });

            $('.select2').select2();

            // Sweet Alert for flash messages
            <?php if (session()->has('success')): ?>
            Swal.fire({
                icon: 'success',
                title: '<?= lang('Common.success') ?>',
                text: '<?= session('success') ?>',
                confirmButtonText: '<?= lang('Common.confirm') ?>',
                confirmButtonColor: '#28a745'
            });
            <?php endif; ?>

            <?php if (session()->has('error')): ?>
            Swal.fire({
                icon: 'error',
                title: '<?= lang('Common.error') ?>',
                text: '<?= session('error') ?>',
                confirmButtonText: '<?= lang('Common.confirm') ?>',
                confirmButtonColor: '#dc3545'
            });
            <?php endif; ?>
        });
    </script>

    <script>
    $(function () {

        const modalEl = document.getElementById('pdpaModal');
        const pdpaModal = new bootstrap.Modal(modalEl, {
            backdrop: 'static',
            keyboard: false
        });

        // 🔎 เช็คสถานะจาก server ก่อน
        $.ajax({
            url: '<?= base_url('/auth/pdpa/check-consent') ?>',   // 🔥 route เช็คสถานะ
            type: 'GET',
            dataType: 'json',
            success: function (res) {

                // ถ้ายังไม่ยอมรับ
                if (res.pdpa_accept == 0) {
                    pdpaModal.show();
                }

            },
            error: function () {
                console.log('ไม่สามารถตรวจสอบ PDPA ได้');
            }
        });

        // ✅ กดปุ่ม OK
        $('#btnPdpaOk').on('click', function () {

            let $btn = $(this);
            $btn.prop('disabled', true); // กันกดรัว

            $.ajax({
                url: '<?= base_url('/auth/pdpa/save-consent') ?>',   // 🔥 route บันทึก
                type: 'POST',
                data: {
                    accept: 1
                },
                success: function () {
                    pdpaModal.hide();
                },
                complete: function () {
                    $btn.prop('disabled', false);
                },
                error: function () {
                    alert('เกิดข้อผิดพลาด กรุณาลองใหม่');
                }
            });

        });

    });
    </script>

    <a style="display:none" href="https://www.tiktok.com/@jootball8?_r=1&_t=ZS-95uPWPKB7RY" target="_blank" class="tiktok-floating-btn" title="สื่อผู้ช่วยสอน">
        <div class="tiktok-icon-wrapper">
            <i class="fa-brands fa-tiktok"></i>
        </div>
        <div class="tiktok-text-label">สื่อผู้ช่วยสอน</div>
    </a>

    <?= $this->include('partials/google_maps') ?>
    <?php $this->renderSection('scripts'); ?>
</body>

</html>
