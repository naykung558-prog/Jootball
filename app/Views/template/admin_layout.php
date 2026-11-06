<!doctype html>
<html lang="<?= get_current_language() ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
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
    <title>JOOTBALL</title>
    <link rel="icon" href="<?= base_url('public/img/jootball_logo.png') ?>" type="image/icon type">

    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/styles.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/myStyles.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/js/DatepickerThai/css/datepicker.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/dataTables.dataTables.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/font-awesome_4.7/css/font-awesome.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/select2.min.css') ?>">


    <link rel="stylesheet" href="<?php echo base_url('public/css/jqueryBracket/jquery.bracket.min.css') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/css/kanit.css') ?>">

    <link rel="stylesheet" href="<?php echo base_url('public/css/cropper.min.css') ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('public/assets/css/admin_theme.css') ?>">

    <link rel="stylesheet" href="<?php echo base_url('public/css/jquery-ui.css') ?>">
    <script src="<?php echo base_url('public/js/jquery-3.5.1.min.js') ?>"></script>
    <style>
        /* Responsive Offcanvas for Bootstrap 5.0 */
        @media (min-width: 1200px) {

            /* xl breakpoint matching d-xl-none usage */
            .left-sidebar.offcanvas {
                visibility: visible !important;
                transform: none !important;
                position: fixed !important;
                top: 0;
                bottom: 0;
                width: 270px;
                /* Enforce standard width */
                transition: transform 0.3s ease, visibility 0.3s ease;
                z-index: 1050;
                /* Ensure on top */
            }

            .offcanvas-backdrop {
                display: none !important;
            }

            /* Desktop Toggle States */
            .left-sidebar.sidebar-closed {
                transform: translateX(-100%) !important;
                visibility: hidden !important;
            }

            .body-wrapper {
                transition: margin-left 0.3s ease;
                margin-left: 270px;
                /* Enforce default margin matching sidebar */
                width: auto;
                /* Allow auto expansion */
                min-height: 100vh;
                background-color: #F2F6FA;
                /* Match the light blue theme background */
            }

            .body-wrapper.sidebar-closed {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        /* Global Background Fix */
        body {
            background-color: #F2F6FA;
        }

        .page-wrapper {
            min-height: 100vh;
            background-color: #F2F6FA;
        }

        /* Ensure normal styles for mobile */
        @media (max-width: 1199px) {
            .body-wrapper {
                margin-left: 0 !important;
                /* Mobile always full width */
            }
        }

        /* Sidebar Toggle Button Styling */
        #headerCollapse {
            background: white;
            color: var(--primary-color);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            border: 2px solid rgba(243, 183, 27, 0.2);
            transition: all 0.3s ease;
        }

        #headerCollapse:hover {
            box-shadow: 0 6px 15px rgba(243, 183, 27, 0.3);
            transform: translateY(-2px);
            border-color: var(--secondary-color);
            background: #fff;
        }

        /* Robust Layout Fixes for Desktop Toggle */
        @media (min-width: 1200px) {
            #main-wrapper.sidebar-closed .left-sidebar {
                transform: translateX(-100%) !important;
                visibility: hidden !important;
            }

            #main-wrapper.sidebar-closed .body-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }

            #main-wrapper.sidebar-closed .app-header {
                width: 100% !important;
                left: 0 !important;
            }

            #main-wrapper.sidebar-closed .body-wrapper>.container-fluid,
            #main-wrapper.sidebar-closed .body-wrapper>.container-lg,
            #main-wrapper.sidebar-closed .body-wrapper>.container-md,
            #main-wrapper.sidebar-closed .body-wrapper>.container-sm,
            #main-wrapper.sidebar-closed .body-wrapper>.container-xl,
            #main-wrapper.sidebar-closed .body-wrapper>.container-xxl {
                max-width: 100% !important;
                padding-left: 30px !important;
                padding-right: 30px !important;
            }

            /* Full-width content for all admin pages — override theme's 1200px cap */
            #main-wrapper[data-layout="vertical"][data-sidebartype="full"]:not(.sidebar-closed) .body-wrapper {
                margin-left: 270px !important;
            }
            .body-wrapper>.container-fluid,
            .body-wrapper>.container-lg,
            .body-wrapper>.container-md,
            .body-wrapper>.container-sm,
            .body-wrapper>.container-xl,
            .body-wrapper>.container-xxl {
                max-width: 100% !important;
                margin-left: 0 !important;
                margin-right: 0 !important;
                padding-left: 24px !important;
                padding-right: 24px !important;
            }
        }

        @media (min-width: 1400px) {
            .body-wrapper>.container-fluid,
            .body-wrapper>.container-lg,
            .body-wrapper>.container-md,
            .body-wrapper>.container-sm,
            .body-wrapper>.container-xl,
            .body-wrapper>.container-xxl {
                padding-left: 32px !important;
                padding-right: 32px !important;
            }
        }

        /* Mobile Sidebar Fixes */
        @media (max-width: 1199px) {
            .left-sidebar.offcanvas {
                position: fixed !important;
                z-index: 1050 !important;
                width: 270px !important;
                height: 100% !important;
                top: 0 !important;
                left: 0 !important;
                transform: translateX(-100%);
                transition: transform 0.3s ease-in-out;
                visibility: hidden;
            }

            .left-sidebar.offcanvas.show {
                transform: translateX(0) !important;
                visibility: visible !important;
            }

            .offcanvas-backdrop {
                display: block !important;
                z-index: 1040 !important;
            }
        }

        /* Admin Language Switcher */
        .admin-lang-dropdown .nav-link {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid var(--secondary-color, #F3B71B);
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0;
            color: #fff;
        }

        .admin-lang-dropdown .nav-link:hover {
            background: var(--secondary-color, #F3B71B);
            color: #1e3a5f;
            box-shadow: 0 4px 15px rgba(243, 183, 27, 0.4);
        }

        .admin-lang-dropdown .nav-link .flag-icon {
            font-size: 1.1rem;
            line-height: 1;
        }

        .admin-lang-dropdown .nav-link .lang-text {
            display: none;
        }

        .admin-lang-dropdown .nav-link.dropdown-toggle::after {
            display: none;
        }

        .admin-lang-dropdown .dropdown-menu {
            min-width: 200px;
            border-radius: 10px;
            border: 1px solid #e2e8f0;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            padding: 0.5rem 0;
        }

        .admin-lang-dropdown .dropdown-item {
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0.25rem 0.5rem;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .admin-lang-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: #fff;
            transform: translateX(5px);
        }

        .admin-lang-dropdown .dropdown-item.active {
            background: linear-gradient(135deg, #f3b71a 0%, #edaf12 100%);
            color: #fff;
            font-weight: 600;
        }

        .admin-lang-dropdown .dropdown-item.active:hover {
            background: linear-gradient(135deg, #edaf12 0%, #f3b71a 100%);
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

<body class="modern-admin">
<?= $this->include('partials/preloader') ?>
<?= $this->include('partials/announcement') ?>

    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <!-- Sidebar Start -->
        <aside class="left-sidebar offcanvas offcanvas-start" tabindex="-1" id="mobileSidebar" aria-labelledby="mobileSidebarLabel">
            <!-- Sidebar scroll-->
            <div>
                <div class="brand-logo text-center">
                    <a href="<?= base_url('admin/dashboard') ?>" class="text-nowrap logo-img">
                        <img src="<?= base_url('public/img/jootball_logo.png') ?>" width="80" alt="JOOTBALL" />
                    </a>
                    <div class="close-btn d-xl-none d-block cursor-pointer" id="sidebarDismissBtn" aria-label="Close">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <i class="fas fa-home nav-small-cap-icon"></i>
                            <span class="hide-menu"><?= lang('Common.admin_main_menu') ?></span>
                        </li>

                        <?php if (hasPermission('dashboard.view')): ?>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "dashboard") ? "active" : "" ?>" href="<?= base_url('admin/dashboard') ?>" aria-expanded="false">
                                <i class="fas fa-tachometer-alt"></i>
                                <span class="hide-menu"><?= lang('Common.admin_dashboard') ?></span>
                            </a>
                        </li>
                        <?php endif; ?>

                        <?php if (isSuperAdmin() || hasAnyPermission(['tournament.view', 'team.view', 'team.requests', 'team.verify', 'academy.view', 'idp.view', 'stadium.view', 'idcard.view', 'settings.view'])): ?>
                            <li class="nav-small-cap">
                                <i class="fas fa-cog nav-small-cap-icon"></i>
                                <span class="hide-menu"><?= lang('Common.admin_system_management') ?></span>
                            </li>

                            <?php if (hasPermission('tournament.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "tournament") ? "active" : "" ?>" href="<?= base_url('admin/tournament'); ?>" aria-expanded="false">
                                        <i class="fas fa-trophy"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_tournament') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('team.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "team") ? "active" : "" ?>" href="<?= base_url('admin/team'); ?>" aria-expanded="false">
                                        <i class="fas fa-users"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_team') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (hasPermission('team.requests')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "team-requests") ? "active" : "" ?>" href="<?= base_url('admin/team-requests'); ?>" aria-expanded="false">
                                        <i class="fas fa-user-plus"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_manage_join_requests') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('team.verify')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "team-verify") ? "active" : "" ?>" href="<?= base_url('admin/team-verify'); ?>" aria-expanded="false">
                                        <i class="fas fa-search"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_verify_team') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('academy.view')): ?>
                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "academy") ? "active" : "" ?>" href="<?= base_url('admin/academy'); ?>" aria-expanded="false">
                                    <i class="fas fa-school"></i>
                                    <span class="hide-menu"><?= lang('Common.admin_academy') ?></span>
                                </a>
                            </li>
                            <?php endif; ?>

                            <?php if (hasPermission('stadium.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "stadium") ? "active" : "" ?>" href="<?= base_url('admin/stadium'); ?>" aria-expanded="false">
                                        <i class="fas fa-map-marker-alt"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_stadium') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('idcard.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "idcard") ? "active" : "" ?>" href="<?= base_url('admin/idcard'); ?>" aria-expanded="false">
                                        <i class="fas fa-id-card"></i>
                                        <span class="hide-menu">จัดการ ID CARD</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('idp.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "idp") ? "active" : "" ?>" href="<?= base_url('admin/idp'); ?>" aria-expanded="false">
                                        <i class="fas fa-clipboard-list"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_idp') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('settings.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "settings") ? "active" : "" ?>" href="<?= base_url('admin/settings/email'); ?>" aria-expanded="false">
                                        <i class="fas fa-cog"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_email_settings') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (isSuperAdmin() || hasAnyPermission(['report.view', 'report.guest', 'scout.view'])): ?>
                            <li class="nav-small-cap">
                                <i class="fas fa-chart-bar nav-small-cap-icon"></i>
                                <span class="hide-menu"><?= lang('Common.admin_reports') ?></span>
                            </li>

                            <?php if (hasPermission('report.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "report") ? "active" : "" ?>" href="<?= base_url('admin/report/report-players'); ?>" aria-expanded="false">
                                        <i class="fas fa-chart-line"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_player_report') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('report.guest')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "report-guest") ? "active" : "" ?>" href="<?= base_url('admin/report-guest/report-players'); ?>" aria-expanded="false">
                                        <i class="fas fa-eye"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_guest_report') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('scout.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "scout") ? "active" : "" ?>" href="<?= base_url('admin/scout'); ?>" aria-expanded="false">
                                        <i class="fas fa-binoculars"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_scout') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (isSuperAdmin()): ?>
                            <li class="nav-small-cap">
                                <i class="fas fa-video nav-small-cap-icon"></i>
                                <span class="hide-menu">Football Analytics</span>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && isset($control[1]) && $control[1] == "analytics") ? "active" : "" ?>"
                                   href="<?= base_url('admin/analytics') ?>" aria-expanded="false">
                                    <i class="fas fa-chart-area"></i>
                                    <span class="hide-menu">Analytics Dashboard</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && isset($control[1]) && $control[1] == "analytics" && isset($control[2]) && $control[2] == "matches") ? "active" : "" ?>"
                                   href="<?= base_url('admin/analytics/matches') ?>" aria-expanded="false">
                                    <i class="fas fa-futbol"></i>
                                    <span class="hide-menu">จัดการแมตช์</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && isset($control[1]) && $control[1] == "analytics" && isset($control[2]) && $control[2] == "players") ? "active" : "" ?>"
                                   href="<?= base_url('admin/analytics/players') ?>" aria-expanded="false">
                                    <i class="fas fa-running"></i>
                                    <span class="hide-menu">สถิติผู้เล่น</span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && isset($control[1]) && $control[1] == "analytics" && isset($control[2]) && $control[2] == "settings") ? "active" : "" ?>"
                                   href="<?= base_url('admin/analytics/settings') ?>" aria-expanded="false">
                                    <i class="fas fa-cog"></i>
                                    <span class="hide-menu">ตั้งค่า Analytics</span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "report") ? "active" : "" ?>" href="<?= base_url('admin/report/report-logs'); ?>" aria-expanded="false">
                                    <i class="fas fa-chart-line"></i>
                                    <span class="hide-menu"><?= lang('Common.admin_login_report') ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "sponsors") ? "active" : "" ?>" href="<?= base_url('admin/sponsors'); ?>" aria-expanded="false">
                                    <i class="fas fa-handshake"></i>
                                    <span class="hide-menu"><?= lang('Common.sponsors_manager') ?></span>
                                </a>
                            </li>

                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && isset($control[1]) && $control[1] == "help-guide") ? "active" : "" ?>" href="<?= base_url('admin/help-guide'); ?>" aria-expanded="false">
                                    <i class="fas fa-book-open"></i>
                                    <span class="hide-menu"><?= lang('Common.help_guide_manager') ?></span>
                                </a>
                            </li>

                        <?php endif; ?>

                        <?php if (isSuperAdmin() || hasAnyPermission(['role.view', 'user.view'])): ?>
                            <li class="nav-small-cap">
                                <i class="fas fa-shield-alt nav-small-cap-icon"></i>
                                <span class="hide-menu"><?= lang('Common.admin_access_control') ?></span>
                            </li>

                            <?php if (hasPermission('user.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "users") ? "active" : "" ?>" href="<?= base_url('admin/users') ?>" aria-expanded="false">
                                        <i class="fas fa-users-cog"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_user_manager') ?></span>
                                    </a>
                                </li>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "player-merge") ? "active" : "" ?>" href="<?= base_url('admin/player-merge') ?>" aria-expanded="false">
                                        <i class="fas fa-user-friends"></i>
                                        <span class="hide-menu">รวมนักเตะซ้ำ</span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('role.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "role" && (!isset($control[2]) || $control[2] == '')) ? "active" : "" ?>" href="<?= base_url('admin/role') ?>" aria-expanded="false">
                                        <i class="fas fa-user-cog"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_manage_roles') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>

                            <?php if (hasPermission('user.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "role" && isset($control[2]) && $control[2] == "user-roles") ? "active" : "" ?>" href="<?= base_url('admin/role/user-roles') ?>" aria-expanded="false">
                                        <i class="fas fa-user-shield"></i>
                                        <span class="hide-menu"><?= lang('Common.admin_assign_permissions') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                            <?php if (hasPermission('coach.view')): ?>
                                <li class="sidebar-item">
                                    <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "coach") ? "active" : "" ?>"
                                        href="<?= base_url('admin/coach'); ?>" aria-expanded="false">
                                        <span>
                                            <i class="ti ti-users"></i>
                                        </span>
                                        <span class="hide-menu"><?= lang('Common.admin_coach_manager') ?></span>
                                    </a>
                                </li>
                            <?php endif; ?>
                        <?php endif; ?>

                        <?php if (isSuperAdmin()): ?>
                            <li class="nav-small-cap">
                                <i class="fas fa-code nav-small-cap-icon"></i>
                                <span class="hide-menu"><?= lang('Common.admin_developer') ?></span>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "api-manager") ? "active" : "" ?>" href="<?= base_url('admin/api-manager') ?>" aria-expanded="false">
                                    <i class="fas fa-code"></i>
                                    <span class="hide-menu"><?= lang('Common.admin_api_management') ?></span>
                                </a>
                            </li>
                            <li class="sidebar-item">
                                <a class="sidebar-link <?= ($control[0] == "admin" && $control[1] == "maps-quota") ? "active" : "" ?>" href="<?= base_url('admin/maps-quota/log') ?>" aria-expanded="false">
                                    <i class="ti ti-map-pin"></i>
                                    <span class="hide-menu"><?= lang('Common.admin_maps_quota_log') ?></span>
                                </a>
                            </li>
                        <?php endif; ?>

                        <li class="nav-small-cap">
                            <i class="fas fa-sign-out-alt nav-small-cap-icon"></i>
                            <span class="hide-menu"><?= lang('Common.admin_system_menu') ?></span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link logout-btn" href="<?= base_url('admin/logout') ?>" aria-expanded="false">
                                <i class="fas fa-power-off"></i>
                                <span class="hide-menu"><?= lang('Common.admin_logout') ?></span>
                            </a>
                        </li>
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->
        </aside>
        <!--  Sidebar End -->
        <!--  Main wrapper -->
        <div class="body-wrapper">
            <!--  Header Start -->
            <header class="app-header">
                <nav class="navbar navbar-light px-3">
                    <ul class="navbar-nav">
                        <li class="nav-item d-block">
                            <a class="nav-link nav-icon-hover" id="headerCollapse" href="javascript:void(0)">
                                <i class="ti ti-menu-2"></i>
                            </a>
                        </li>
                    </ul>
                    <div class="d-flex align-items-center justify-content-between flex-grow-1 px-2">
                        <a href="<?= base_url('/') ?>" class="text-nowrap logo-img d-flex align-items-center gap-2 text-decoration-none">
                            <i class="ti ti-home fs-6"></i>
                            <span class="d-none d-md-block"><?= lang('Common.admin_home') ?></span>
                        </a>
                        <ul class="navbar-nav flex-row ms-auto align-items-center justify-content-end">
                            <!-- Language Switcher -->
                            <li class="nav-item dropdown admin-lang-dropdown me-2">
                                <a class="nav-link dropdown-toggle" href="javascript:void(0)" id="adminLanguageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <?php if (get_current_language() === 'th'): ?>
                                        <span class="flag-icon">🇹🇭</span>
                                    <?php else: ?>
                                        <span class="flag-icon">🇬🇧</span>
                                    <?php endif; ?>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminLanguageDropdown">
                                    <li>
                                        <a class="dropdown-item <?= get_current_language() === 'th' ? 'active' : '' ?>" href="<?= lang_url('th') ?>">
                                            <span style="font-size: 1.5rem;">🇹🇭</span>
                                            <span>ภาษาไทย</span>
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item <?= get_current_language() === 'en' ? 'active' : '' ?>" href="<?= lang_url('en') ?>">
                                            <span style="font-size: 1.5rem;">🇬🇧</span>
                                            <span>English</span>
                                        </a>
                                    </li>
                                </ul>
                            </li>
                            <li class="nav-item dropdown">
                                <a class="nav-link nav-icon-hover position-relative" href="javascript:void(0)" id="drop2" data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    <div class="profile-avatar">
                                        <img src="<?= base_url('public/img/img_profile.jpg') ?>" alt="Profile" width="40" height="40" class="rounded-circle border border-3" style="border-color: var(--secondary-color) !important;">
                                    </div>
                                </a>
                                <div class="dropdown-menu dropdown-menu-end shadow-lg" aria-labelledby="drop2">
                                    <div class="dropdown-header text-center py-3 border-bottom">
                                        <img src="<?= base_url('public/img/img_profile.jpg') ?>" alt="Profile" width="60" height="60" class="rounded-circle mb-2">
                                        <h6 class="mb-0"><?= lang('Common.admin_administrator') ?></h6>
                                        <small class="text-muted">Administrator</small>
                                    </div>
                                    <div class="dropdown-body p-2">
                                        <a href="javascript:void(0)" class="dropdown-item d-flex align-items-center gap-3 py-2">
                                            <i class="fas fa-user-circle" style="color: var(--primary-color);"></i>
                                            <span><?= lang('Common.admin_my_profile') ?></span>
                                        </a>
                                        <a href="javascript:void(0)" class="dropdown-item d-flex align-items-center gap-3 py-2">
                                            <i class="fas fa-cog" style="color: var(--secondary-color);"></i>
                                            <span><?= lang('Common.admin_settings') ?></span>
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a href="<?= base_url('admin/logout') ?>" class="dropdown-item d-flex align-items-center gap-3 py-2 text-danger">
                                            <i class="fas fa-sign-out-alt"></i>
                                            <span><?= lang('Common.admin_logout') ?></span>
                                        </a>
                                    </div>
                                </div>
                            </li>
                        </ul>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            <div class="container-fluid">
                <?php $this->renderSection('content'); ?>
                <?php if (!empty($content)) echo $content; ?>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('public/js/jquery-ui.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/jquery.mask.min.js') ?>"></script>
    <!-- Micromodal.js - Lightweight accessible modal library -->
    <script src="https://unpkg.com/micromodal/dist/micromodal.min.js"></script>

    <script src="<?php echo base_url('public/js/popper.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/popper.min.js') ?>"></script>
    <script src="<?php echo base_url("public/js/DatepickerThai/bootstrap-datepicker.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/DatepickerThai/bootstrap-datepicker-thai.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/DatepickerThai/bootstrap-datepicker.th.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/dataTables.min.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/sweetalert2@11.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/index.global.min.js"); ?>"></script>
    <script src="<?php echo base_url('public/js/cropper.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/select2.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/highcharts/highcharts.js') ?>"></script>
    <script src="<?php echo base_url('public/js/highcharts/modules/exporting.js') ?> "></script>
    <script src="<?php echo base_url('public/js/highcharts/modules/export-data.js') ?> "></script>
    <script src="<?php echo base_url('public/js/highcharts/modules/accessibility.js') ?> "></script>

    <script src="<?= base_url('public/assets/js/sidebarmenu.js') ?>"></script>
    <script src="<?= base_url('public/js/jqueryBracket/jquery.bracket.min.js') ?>"></script>

    <script src="<?php echo base_url('public/js/cropper.min.js') ?>"></script>
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

        Highcharts.setOptions({
            lang: {
                thousandsSep: ',',
                viewFullscreen: '<?= lang('Common.hc_view_fullscreen') ?>',
                printChart: '<?= lang('Common.hc_print_chart') ?>',
                downloadCSV: '<?= lang('Common.hc_download_csv') ?>',
                downloadJPEG: '<?= lang('Common.hc_download_jpeg') ?>',
                downloadPDF: '<?= lang('Common.hc_download_pdf') ?>',
                downloadPNG: '<?= lang('Common.hc_download_png') ?>',
                downloadXLS: '<?= lang('Common.hc_download_xls') ?>',
                downloadSVG: '<?= lang('Common.hc_download_svg') ?>',
                viewData: '<?= lang('Common.hc_view_data') ?>',
                hideData: '<?= lang('Common.hc_hide_data') ?>',
            },
            credits: false,
        });

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
            $('.number-3-digit').mask('000', {
                translation: {
                    '0': {
                        pattern: /[0-9]/,
                        optional: true
                    }
                }
            });
            $('.numberic').mask('#,##0', {
                reverse: true
            })

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

            $('.data-tables').DataTable({
                language: dtLanguage,
                processing: false,
                serverSide: false,
                pageLength: 50,
            })

            $('.select2').select2();
        });

        // Sidebar Toggle Logic
        $(document).ready(function() {
            var mobileSidebarEl = document.getElementById('mobileSidebar');
            var bsOffcanvas = null;

            function getOffcanvas() {
                if (!bsOffcanvas && mobileSidebarEl) {
                    bsOffcanvas = new bootstrap.Offcanvas(mobileSidebarEl);
                }
                return bsOffcanvas;
            }

            // Desktop Toggle / Mobile Open
            $('#headerCollapse').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                if (window.innerWidth >= 1200) {
                    // Desktop: Toggle Visibility
                    $('#main-wrapper').toggleClass('sidebar-closed');
                } else {
                    // Mobile: Open Offcanvas
                    var oc = getOffcanvas();
                    if (oc) oc.show();
                }
            });

            // Mobile Close Button
            $('#sidebarDismissBtn').on('click', function(e) {
                if (window.innerWidth < 1200) {
                    var oc = getOffcanvas();
                    if (oc) oc.hide();
                }
            });
        });
    </script>
    <script>
        // Global MicroModal init
        MicroModal.init({
            disableScroll: false,
            awaitOpenAnimation: false,
            awaitCloseAnimation: false,
            onShow: function(modal) {
                // เลื่อนหน้าจอไปยังตำแหน่ง modal
                var container = modal.querySelector('.micromodal-container');
                if (container) {
                    setTimeout(function() {
                        container.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 50);
                }
            }
        });
    </script>
    <a  style="display:none" href="https://www.tiktok.com/@jootball8?_r=1&_t=ZS-95uPWPKB7RY" target="_blank" class="tiktok-floating-btn" title="สื่อผู้ช่วยสอน">
        <div class="tiktok-icon-wrapper">
            <i class="fa-brands fa-tiktok"></i>
        </div>
        <div class="tiktok-text-label">สื่อผู้ช่วยสอน</div>
    </a>

    <?= $this->include('partials/google_maps') ?>
    <?php $this->renderSection('scripts'); ?>
</body>

</html>