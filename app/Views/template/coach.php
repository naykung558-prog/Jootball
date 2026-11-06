<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url('public/img/jootball_logo.png') ?>" type="image/icon type">
    <title><?= $this->renderSection('title') ?></title>
    <link rel="stylesheet" href="<?php echo base_url('public/css/styles.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/myStyle.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/js/DatepickerThai/css/datepicker.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/dataTables.dataTables.min.css') ?>">
    <!-- Font Awesome 6 for modern icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
    <!-- Keep Font Awesome 4.7 for backward compatibility -->
    <link rel="stylesheet" href="<?php echo base_url('public/css/font-awesome_4.7/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/jqueryBracket/jquery.bracket.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/select2.min.css') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link
        href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/css/kanit.css') ?>">

    <link rel="stylesheet" href="<?php echo base_url('public/css/cropper.min.css') ?>" />

    <!-- Coach Theme Styles -->
    <style>
        :root {
            --coach-primary: #0B1548;    /* Navy Blue */
            --coach-secondary: #F3B819;  /* Gold */
            --coach-dark: #000000;       /* Black */
            --coach-light: #ffffff;      /* White */
            --coach-gray-100: #f8f9fa;
            --coach-gray-200: #e9ecef;
            --coach-gray-300: #dee2e6;
            --coach-gray-400: #ced4da;
            --coach-gray-500: #adb5bd;
            --coach-gray-600: #6c757d;
            --coach-gray-700: #495057;
            --coach-gray-800: #343a40;
            --coach-success: #28a745;
            --coach-danger: #dc3545;
            --coach-info: #17a2b8;
            --coach-shadow: 0 2px 10px rgba(11, 21, 72, 0.1);
            --coach-shadow-lg: 0 4px 20px rgba(11, 21, 72, 0.15);
            --coach-border-radius: 8px;
            --coach-transition: all 0.3s ease;
        }

        /* Header Styles */
        .coach-header {
            box-shadow: var(--coach-shadow);
        }

        .coach-navbar {
            background-color: var(--coach-primary) !important;
            padding: 1rem 0;
        }

        .coach-logo {
            transition: var(--coach-transition);
        }

        .coach-logo:hover {
            transform: scale(1.05);
        }

        .coach-toggler {
            border: 2px solid var(--coach-secondary);
            background: transparent;
            padding: 8px 12px;
            border-radius: var(--coach-border-radius);
        }

        .coach-toggler i {
            color: var(--coach-secondary);
            font-size: 1.2rem;
        }

        .coach-toggler:hover {
            background-color: var(--coach-secondary);
        }

        .coach-toggler:hover i {
            color: var(--coach-primary);
        }

        /* User Menu */
        .coach-user-menu {
            color: var(--coach-light) !important;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: var(--coach-border-radius);
            transition: var(--coach-transition);
        }

        .coach-user-menu:hover {
            background-color: rgba(243, 184, 25, 0.1);
            color: var(--coach-secondary) !important;
        }

        .coach-profile-img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            border: 2px solid var(--coach-secondary);
            object-fit: cover;
        }

        .coach-profile-placeholder {
            width: 40px;
            height: 40px;
            background-color: var(--coach-secondary);
            color: var(--coach-primary);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
        }

        .coach-dropdown {
            background-color: var(--coach-light);
            border: 1px solid var(--coach-gray-300);
            border-radius: var(--coach-border-radius);
            box-shadow: var(--coach-shadow-lg);
            min-width: 200px;
        }

        .coach-dropdown .dropdown-item {
            color: var(--coach-gray-700);
            padding: 12px 20px;
            transition: var(--coach-transition);
        }

        .coach-dropdown .dropdown-item:hover {
            background-color: var(--coach-gray-100);
            color: var(--coach-primary);
        }

        .coach-login-btn {
            background-color: var(--coach-secondary);
            color: var(--coach-primary) !important;
            border: 2px solid var(--coach-secondary);
            padding: 10px 20px;
            border-radius: var(--coach-border-radius);
            font-weight: 600;
            transition: var(--coach-transition);
            text-decoration: none;
        }

        .coach-login-btn:hover {
            background-color: transparent;
            color: var(--coach-secondary) !important;
            border-color: var(--coach-secondary);
        }

        /* Sub Navigation */
        .coach-subnav {
            background-color: var(--coach-light);
            border-bottom: 1px solid var(--coach-gray-200);
            padding: 1rem 0;
        }

        .coach-nav-menu {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin: 0;
            padding: 0;
            list-style: none;
            flex-wrap: wrap;
        }

        .coach-nav-menu .nav-link {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 12px 20px;
            color: var(--coach-gray-600);
            text-decoration: none;
            border-radius: var(--coach-border-radius);
            transition: var(--coach-transition);
            position: relative;
            min-width: 100px;
            text-align: center;
        }

        .coach-nav-menu .nav-link i {
            font-size: 1.5rem;
            margin-bottom: 6px;
        }

        .coach-nav-menu .nav-link span {
            font-size: 0.9rem;
            font-weight: 500;
        }

        .coach-nav-menu .nav-link:hover {
            color: var(--coach-primary);
            background-color: var(--coach-gray-100);
            transform: translateY(-2px);
        }

        .coach-nav-menu .nav-link.active {
            color: var(--coach-light);
            background-color: var(--coach-primary);
            box-shadow: var(--coach-shadow);
        }

        .coach-nav-menu .nav-link.active:hover {
            background-color: var(--coach-primary);
            color: var(--coach-light);
        }



        /* Alert Styles */
        .coach-alert-success {
            background-color: rgba(40, 167, 69, 0.1);
            border: 1px solid var(--coach-success);
            color: var(--coach-success);
            border-radius: var(--coach-border-radius);
            padding: 16px;
        }

        .coach-alert-error {
            background-color: rgba(220, 53, 69, 0.1);
            border: 1px solid var(--coach-danger);
            color: var(--coach-danger);
            border-radius: var(--coach-border-radius);
            padding: 16px;
        }

        .coach-alert-info {
            background-color: rgba(11, 21, 72, 0.1);
            border: 1px solid var(--coach-primary);
            color: var(--coach-primary);
            border-radius: var(--coach-border-radius);
            padding: 16px;
        }

        .coach-alert-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.2rem;
            flex-shrink: 0;
        }

        .coach-alert-success .coach-alert-icon {
            background-color: var(--coach-success);
            color: var(--coach-light);
        }

        .coach-alert-error .coach-alert-icon {
            background-color: var(--coach-danger);
            color: var(--coach-light);
        }

        .coach-alert-info .coach-alert-icon {
            background-color: var(--coach-primary);
            color: var(--coach-light);
        }

        .coach-alert-close {
            color: inherit;
            opacity: 0.7;
            transition: var(--coach-transition);
        }

        .coach-alert-close:hover {
            opacity: 1;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .coach-nav-menu {
                gap: 0.5rem;
            }

            .coach-nav-menu .nav-link {
                min-width: 80px;
                padding: 10px 12px;
            }

            .coach-nav-menu .nav-link i {
                font-size: 1.3rem;
                margin-bottom: 4px;
            }

            .coach-nav-menu .nav-link span {
                font-size: 0.8rem;
            }
        }

        @media (max-width: 576px) {
            .coach-subnav {
                padding: 0.5rem 0;
            }

            .coach-nav-menu {
                justify-content: space-around;
                gap: 0.25rem;
            }

            .coach-nav-menu .nav-link {
                min-width: 70px;
                padding: 8px 10px;
            }
            .coach-notification-dropdown {
                min-width: 320px;
                max-width: 350px;
                left: -200px !important;
            }

            .coach-nav-menu .nav-link span {
                font-size: 0.75rem;
            }
        }

        /* Body and Content */
        body.kanit-regular {
            background-color: var(--coach-gray-100);
        }

        /* Utility Classes */
        .coach-btn-primary {
            background-color: var(--coach-primary);
            border-color: var(--coach-primary);
            color: var(--coach-light);
        }

        .coach-btn-primary:hover {
            background-color: var(--coach-dark);
            border-color: var(--coach-dark);
            color: var(--coach-light);
        }

        .coach-btn-secondary {
            background-color: var(--coach-secondary);
            border-color: var(--coach-secondary);
            color: var(--coach-primary);
        }

        .coach-btn-secondary:hover {
            background-color: var(--coach-dark);
            border-color: var(--coach-dark);
            color: var(--coach-light);
        }

        .coach-text-primary {
            color: var(--coach-primary) !important;
        }

        .coach-text-secondary {
            color: var(--coach-secondary) !important;
        }

        .coach-bg-primary {
            background-color: var(--coach-primary) !important;
        }

        .coach-bg-secondary {
            background-color: var(--coach-secondary) !important;
        }

        /* Notification Styles */
        .coach-notification-bell {
            position: relative;
            padding: 8px 12px;
            border-radius: var(--coach-border-radius);
            transition: var(--coach-transition);
        }

        .coach-notification-bell:hover {
            background-color: rgba(243, 184, 25, 0.1);
        }

        .coach-notification-dropdown {
            background-color: var(--coach-light);
            border: 1px solid var(--coach-gray-300);
            border-radius: var(--coach-border-radius);
            box-shadow: var(--coach-shadow-lg);
            min-width: 420px;
            max-width: 500px;
            white-space: normal;
        }

        .coach-notification-item {
            padding: 12px 16px;
            border-bottom: 1px solid var(--coach-gray-200);
            transition: var(--coach-transition);
            cursor: pointer;
        }

        .coach-notification-item:hover {
            background-color: var(--coach-gray-100);
        }

        .coach-notification-item.unread {
            background-color: rgba(243, 184, 25, 0.05);
            border-left: 3px solid var(--coach-secondary);
        }

        .coach-notification-title {
            font-weight: 600;
            color: var(--coach-primary);
            font-size: 0.9rem;
            margin-bottom: 4px;
        }

        .coach-notification-message {
            color: var(--coach-gray-600);
            font-size: 0.85rem;
            margin-bottom: 6px;
            line-height: 1.4;
            word-wrap: break-word;
            overflow-wrap: break-word;
            max-width: 100%;
        }

        .coach-notification-time {
            color: var(--coach-gray-500);
            font-size: 0.75rem;
        }

        .coach-notification-icon {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }

        .coach-notification-icon.invitation {
            background-color: rgba(243, 184, 25, 0.1);
            color: var(--coach-secondary);
        }

        .coach-notification-icon.response {
            background-color: rgba(11, 21, 72, 0.1);
            color: var(--coach-primary);
        }

        /* Language Switcher Styles */
        #languageDropdown {
            background: rgba(243, 184, 25, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        #languageDropdown:hover {
            background: rgba(243, 184, 25, 0.2);
            transform: translateY(-2px);
        }

        .coach-dropdown {
            min-width: 200px;
            border-radius: 10px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            border: 1px solid var(--coach-gray-300);
            padding: 0.5rem 0;
        }

        .coach-dropdown .dropdown-item {
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0.25rem 0.5rem;
        }

        .coach-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, var(--coach-primary) 0%, #1e40af 100%);
            color: var(--coach-light);
            transform: translateX(5px);
        }

        .coach-dropdown .dropdown-item.active {
            background: linear-gradient(135deg, var(--coach-secondary) 0%, #edaf12 100%);
            color: var(--coach-primary);
            font-weight: 600;
        }

        .coach-dropdown .dropdown-item.active:hover {
            background: linear-gradient(135deg, #edaf12 0%, var(--coach-secondary) 100%);
        }

        .admin-impersonation-bar {
            background: #fff3cd;
            border-bottom: 1px solid #f2d98d;
            color: #6b5200;
            padding: 10px 16px;
            font-size: 14px;
            text-align: center;
        }

        .admin-impersonation-bar a {
            color: var(--coach-primary);
            font-weight: 700;
            text-decoration: none;
        }

        /* TikTok Floating Button */
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
    </style>
    <?= $this->renderSection('style') ?>
</head>

<?php
$url = uri_string();
$control = explode('/', $url);
?>

<body class="kanit-regular">
<?= $this->include('partials/preloader') ?>
<?= $this->include('partials/announcement') ?>

    <a style="display:none" href="https://www.tiktok.com/@jootball8?_r=1&_t=ZS-95uPWPKB7RY" target="_blank" class="tiktok-floating-btn" title="สื่อผู้ช่วยสอน">
        <div class="tiktok-icon-wrapper">
            <i class="fa-brands fa-tiktok"></i>
        </div>
        <div class="tiktok-text-label">สื่อผู้ช่วยสอน</div>
    </a>

    <?php $frontendImpersonation = session()->get('frontend_impersonation'); ?>
    <?php if (!empty($frontendImpersonation) && session()->get('isAdminLoggedIn')): ?>
        <div class="admin-impersonation-bar">
            กำลังทดสอบหน้าบ้านในฐานะ <?= esc($frontendImpersonation['type'] ?? 'coach') ?>:
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

    <header class="coach-header">
        <!-- Main Navigation Bar -->
        <nav class="navbar navbar-expand-lg px-3 kanit-medium coach-navbar">
            <div class="container">
                <!-- Logo -->
                <a class="navbar-brand" href="<?= base_url('/') ?>">
                    <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="GLO Football" height="60px" class="coach-logo">
                </a>

                <!-- Mobile Toggle Button -->
                <button class="navbar-toggler coach-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="fas fa-bars"></i>
                </button>

                <!-- Navbar Items -->
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto align-items-center">
                        <?php if (!empty(session()->get('coach_id'))): ?>
                        <!-- Language Switcher -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="gap: 0.5rem;">
                                <?php
                                helper('language');
                                $currentLang = get_current_language();
                                if ($currentLang == 'th') {
                                    echo '<span style="font-size: 1.2rem;">🇹🇭</span><span class="d-none d-md-inline" style="color: var(--coach-light);">ไทย</span>';
                                } else {
                                    echo '<span style="font-size: 1.2rem;">🇬🇧</span><span class="d-none d-md-inline" style="color: var(--coach-light);">English</span>';
                                }
                                ?>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end coach-dropdown" aria-labelledby="languageDropdown">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center <?= $currentLang == 'th' ? 'active' : '' ?>" href="<?= lang_url('th') ?>" style="gap: 0.75rem;">
                                        <span style="font-size: 1.5rem;">🇹🇭</span>
                                        <span style="color: var(--coach-primary);">ภาษาไทย</span>
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item d-flex align-items-center <?= $currentLang == 'en' ? 'active' : '' ?>" href="<?= lang_url('en') ?>" style="gap: 0.75rem;">
                                        <span style="font-size: 1.5rem;">🇬🇧</span>
                                        <span style="color: var(--coach-primary);">English</span>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Notification Bell -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link coach-notification-bell" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false" id="notificationDropdown">
                                <i class="fas fa-bell" style="font-size: 1.2rem; color: var(--coach-secondary);"></i>
                                <span class="badge bg-danger rounded-pill position-absolute top-0 start-100 translate-middle" id="notificationBadge" style="font-size: 0.7rem; display: none;">0</span>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end coach-notification-dropdown" aria-labelledby="notificationDropdown" style="width: max-content;">
                                <div class="dropdown-header d-flex justify-content-between align-items-center">
                                    <span><?= lang('Common.notifications') ?></span>
                                    <button class="btn btn-sm btn-outline-primary" id="markAllReadBtn" style="display: none;">
                                        <i class="fas fa-check"></i> <?= lang('Common.mark_all_as_read') ?>
                                    </button>
                                </div>
                                <div class="dropdown-divider"></div>
                                <div id="notificationList" style="max-height: 400px; overflow-y: auto;">
                                    <div class="text-center py-3 text-muted">
                                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                        <p class="mb-0"><?= lang('Common.no_notifications') ?></p>
                                    </div>
                                </div>
                                <div class="dropdown-divider"></div>
                                <div class="dropdown-item-text text-center">
                                    <a href="<?= base_url('coach/invitations') ?>" class="btn btn-sm btn-primary">
                                        <?= lang('Common.view_all_invitations') ?>
                                    </a>
                                </div>
                            </div>
                        </li>

                        <li class="nav-item">
                            <a class="nav-link coach-user-menu d-flex align-items-center" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php
                                // แสดงรูปภาพตามลำดับความสำคัญ: image_path ก่อน ถ้าไม่มีค่อยใช้ line_picture
                                $coachData = session()->get('coach_data');
                                $displayImage = !empty($coachData['image_path']) ? base_url($coachData['image_path']) : (!empty($coachData['line_picture']) ? base_url($coachData['line_picture']) : '');
                                ?>
                                <?php if (!empty($displayImage)): ?>
                                <img src="<?= $displayImage ?>" alt="Profile" class="coach-profile-img me-2">
                                <?php else: ?>
                                <div class="coach-profile-placeholder me-2">
                                    <i class="fas fa-user"></i>
                                </div>
                                <?php endif; ?>
                                <span class="d-none d-md-inline"><?= $coachData['first_name']?></span>
                            </a>

                                                            <li><a class="dropdown-item text-danger"
                                                                    href="<?= base_url('coach/logout') ?>"><i
                                                                        class="fas fa-sign-out-alt me-2"></i><?= lang('Common.logout_button') ?></a>
                                                            </li>

                        </li>
                        <?php else: ?>
                        <!-- <li class="nav-item">
                            <a class="btn coach-login-btn" href="<?= base_url('coach') ?>">
                                <i class="fas fa-sign-in-alt me-2"></i>เข้าสู่ระบบ
                            </a>
                        </li> -->
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </nav>

        <!-- Sub Navigation -->
        <?php if (!empty(session()->get('coach_id'))): ?>
        <nav class="coach-subnav">
            <div class="container">
                <ul class="nav coach-nav-menu">
                    <li class="nav-item">
                        <a class="nav-link <?= ($control[1] == 'dashboard' || empty($control[1])) ? "active" : "" ?>"
                           href="<?= base_url('coach/dashboard') ?>">
                            <i class="fas fa-tachometer-alt"></i>
                            <span><?= lang('Common.dashboard_nav') ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($control[1] == 'profile') ? "active" : "" ?>"
                           href="<?= base_url('coach/profile') ?>">
                            <i class="fas fa-user-edit"></i>
                            <span><?= lang('Common.profile_nav') ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($control[1] == 'teams') ? "active" : "" ?>"
                           href="<?= base_url('coach/teams') ?>">
                            <i class="fas fa-shield-alt"></i>
                            <span><?= lang('Common.clubs_nav') ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($control[1] == 'find-team' || $control[1] == 'my-requests') ? "active" : "" ?>"
                           href="<?= base_url('coach/find-team') ?>">
                            <i class="fas fa-search"></i>
                            <span><?= lang('Common.find_team_nav') ?></span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link <?= ($control[1] == 'invitations') ? "active" : "" ?>"
                           href="<?= base_url('coach/invitations') ?>">
                            <i class="fas fa-envelope"></i>
                            <span><?= lang('Common.invitations_nav') ?></span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
        <?php endif; ?>
    </header>

    <!-- Flash Messages -->
    <?php if (session()->getFlashdata('success')): ?>
    <div class="container mt-3">
        <div class="alert coach-alert-success alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="coach-alert-icon me-3">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="flex-grow-1">
                    <strong><?= lang('Common.success_message') ?></strong>
                    <div><?= session()->getFlashdata('success') ?></div>
                </div>
                <button type="button" class="btn-close coach-alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
    <div class="container mt-3">
        <div class="alert coach-alert-error alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="coach-alert-icon me-3">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <div class="flex-grow-1">
                    <strong><?= lang('Common.error_message_alert') ?></strong>
                    <div><?= session()->getFlashdata('error') ?></div>
                </div>
                <button type="button" class="btn-close coach-alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('info')): ?>
    <div class="container mt-3">
        <div class="alert coach-alert-info alert-dismissible fade show" role="alert">
            <div class="d-flex align-items-center">
                <div class="coach-alert-icon me-3">
                    <i class="fas fa-info-circle"></i>
                </div>
                <div class="flex-grow-1">
                    <strong><?= lang('Common.info_message') ?></strong>
                    <div><?= session()->getFlashdata('info') ?></div>
                </div>
                <button type="button" class="btn-close coach-alert-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <div class="container-fluid p-0">
        <?= $this->renderSection('content') ?>
    </div>

    <script src="<?php echo base_url('public/js/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/jquery.mask.min.js') ?>"></script>

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
            "lengthMenu": "แสดง _MENU_ รายการ",
            "search": "ค้นหา:",
            "zeroRecords": "ไม่มีข้อมูล",
            "emptyTable": "ไม่มีข้อมูลในตาราง",
            "info": "รายการที่ _START_ ถึง _END_ จาก _TOTAL_ รายการ",
            "infoFiltered": "(กรองข้อมูลจากทั้งหมด _MAX_ แถว)",
            "infoEmpty": "ไม่มีข้อมูล",
            "paginate": {
                "first": "หน้าแรก",
                "last": "หน้าสุดท้าย",
                "next": "ถัดไป",
                "previous": "ก่อนหน้า"
            },
        };

        $(document).ready(function () {
            $('.phone-input').mask('000-000-0000');
            $('.shirt_number').mask('00', {
                translation: {
                    '0': {
                        pattern: /[0-9]/,
                        optional: true
                    }
                }
            });

            $('.once-only').submit(function () {
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

            // Initialize notifications
            <?php if (!empty(session()->get('coach_id'))): ?>
            initializeNotifications();
            <?php endif; ?>
        });

        // Notification System
        function initializeNotifications() {
            loadNotifications();

            // Poll for new notifications every 30 seconds
            setInterval(loadNotifications, 30000);

            // Mark all as read button
            $('#markAllReadBtn').click(function() {
                markAllNotificationsAsRead();
            });
        }

        function loadNotifications() {
            $.ajax({
                url: '<?= base_url('coach/notifications') ?>',
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        updateNotificationUI(response.data.notifications, response.data.unread_count);
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to load notifications:', error);
                }
            });
        }

        function updateNotificationUI(notifications, unreadCount) {
            const badge = $('#notificationBadge');
            const list = $('#notificationList');
            const markAllBtn = $('#markAllReadBtn');

            // Update badge
            if (unreadCount > 0) {
                badge.text(unreadCount).show();
                markAllBtn.show();
            } else {
                badge.hide();
                markAllBtn.hide();
            }

            // Update notification list
            if (notifications.length === 0) {
                list.html(`
                    <div class="text-center py-3 text-muted">
                        <i class="fas fa-bell-slash fa-2x mb-2"></i>
                        <p class="mb-0"><?= lang('Common.no_notifications') ?></p>
                    </div>
                `);
            } else {
                let html = '';
                notifications.forEach(function(notification) {
                    const isUnread = notification.is_read == 0;
                    const isInvite = (notification.type === 'coach_invitation' || notification.type === 'coach_invitation_pending');
                    const iconClass = isInvite ? 'invitation' : 'response';
                    const iconName = isInvite ? 'fa-envelope' : 'fa-reply';
                    const timeAgo = formatTimeAgo(notification.created_at);
                    const idAttr = String(notification.id).replace(/'/g, '');

                    html += `
                        <div class="coach-notification-item ${isUnread ? 'unread' : ''}"
                             data-id="${idAttr}"
                             onclick="handleNotificationClick('${idAttr}', '${notification.type}')">
                            <div class="d-flex">
                                <div class="coach-notification-icon ${iconClass}">
                                    <i class="fas ${iconName}"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="coach-notification-title">${notification.title}</div>
                                    <div class="coach-notification-message">${notification.message}</div>
                                    <div class="coach-notification-time">${timeAgo}</div>
                                </div>
                                ${isUnread ? '<div class="ms-2"><i class="fas fa-circle text-primary" style="font-size: 0.5rem;"></i></div>' : ''}
                            </div>
                        </div>
                    `;
                });
                list.html(html);
            }
        }

        function handleNotificationClick(notificationId, type) {
            // Virtual pending invitation: ไปหน้าคำเชิญตรงๆ ไม่ต้อง mark-as-read (ยัง pending อยู่)
            if (type === 'coach_invitation_pending') {
                window.location.href = '<?= base_url('coach/invitations') ?>';
                return;
            }
            // Mark as read first สำหรับ notification ปกติ
            markNotificationAsRead(notificationId);

            // Navigate based on notification type
            if (type === 'coach_invitation') {
                window.location.href = '<?= base_url('coach/invitations') ?>';
            }
        }

        function markNotificationAsRead(notificationId) {
            // ป้องกัน virtual id (เช่น "inv-xxx") ไม่ให้ยิง API
            if (typeof notificationId === 'string' && notificationId.indexOf('-') !== -1) return;
            $.ajax({
                url: '<?= base_url('coach/notifications/mark-read') ?>',
                type: 'POST',
                data: { notification_id: notificationId },
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Reload notifications to update UI
                        loadNotifications();
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to mark notification as read:', error);
                }
            });
        }

        function markAllNotificationsAsRead() {
            $.ajax({
                url: '<?= base_url('coach/notifications/mark-all-read') ?>',
                type: 'POST',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Reload notifications to update UI
                        loadNotifications();

                        // Show success message
                        Swal.fire({
                            title: '<?= lang('Common.success_message') ?>',
                            text: '<?= lang('Common.all_notifications_read') ?>',
                            icon: 'success',
                            confirmButtonColor: '#28a745',
                            confirmButtonText: '<?= lang('Common.ok') ?>',
                            timer: 2000,
                            customClass: {
                                popup: 'rounded-3',
                                confirmButton: 'rounded-pill px-4'
                            }
                        });
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Failed to mark all notifications as read:', error);
                    Swal.fire({
                        title: '<?= lang('Common.error_message_alert') ?>',
                        text: '<?= lang('Common.unable_to_mark_read') ?>',
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: '<?= lang('Common.try_again') ?>',
                        customClass: {
                            popup: 'rounded-3',
                            confirmButton: 'rounded-pill px-4'
                        }
                    });
                }
            });
        }

        function formatTimeAgo(dateString) {
            const now = new Date();
            const date = new Date(dateString);
            const diffMs = now - date;
            const diffMins = Math.floor(diffMs / 60000);
            const diffHours = Math.floor(diffMs / 3600000);
            const diffDays = Math.floor(diffMs / 86400000);

            if (diffMins < 1) return '<?= lang('Common.just_now') ?>';
            if (diffMins < 60) return `${diffMins} <?= lang('Common.minutes_ago') ?>`;
            if (diffHours < 24) return `${diffHours} <?= lang('Common.hours_ago') ?>`;
            if (diffDays < 7) return `${diffDays} <?= lang('Common.days_ago') ?>`;

            return date.toLocaleDateString('th-TH', {
                year: 'numeric',
                month: 'short',
                day: 'numeric'
            });
        }

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
            url: '<?= base_url('/coach/pdpa/check-consent') ?>',   // 🔥 route เช็คสถานะ
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
                url: '<?= base_url('/coach/pdpa/save-consent') ?>',   // 🔥 route บันทึก
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

    <?= $this->renderSection('scripts') ?>
</body>

</html>
