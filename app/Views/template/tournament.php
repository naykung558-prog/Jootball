<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>JOOTBALL - <?php echo $data['name'] ?></title>
    <link rel="icon" href="<?= base_url('public/img/jootball_logo.png') ?>" type="image/icon type">

    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/styles.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/myStyles.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/js/DatepickerThai/css/datepicker.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/dataTables.dataTables.min.css') ?>">
    <link rel="stylesheet" href="<?php echo base_url('public/css/font-awesome_4.7/css/font-awesome.min.css') ?>">

    <link rel="stylesheet" href="<?php echo base_url('public/css/jqueryBracket/jquery.bracket.min.css') ?>">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= base_url('public/css/kanit.css') ?>">

    <link rel="stylesheet" href="<?php echo base_url('public/css/cropper.min.css') ?>" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <link rel="stylesheet" href="<?= base_url('public/assets/css/admin_theme.css') ?>">
    <style>
        /* Responsive Offcanvas for Bootstrap 5.0 */
        @media (min-width: 1200px) {
            .left-sidebar.offcanvas {
                visibility: visible !important;
                transform: none !important;
                position: fixed !important;
                top: 0;
                bottom: 0;
                width: 270px;
                transition: transform 0.3s ease, visibility 0.3s ease;
                z-index: 1050;
            }

            .offcanvas-backdrop {
                display: none !important;
            }

            #main-wrapper.sidebar-closed .left-sidebar {
                transform: translateX(-100%) !important;
                visibility: hidden !important;
            }

            .body-wrapper {
                transition: margin-left 0.3s ease;
                margin-left: 270px;
                width: auto;
                min-height: 100vh;
                background-color: #F2F6FA;
            }

            #main-wrapper.sidebar-closed .body-wrapper {
                margin-left: 0 !important;
                width: 100% !important;
            }
        }

        body {
            background-color: #F2F6FA;
        }

        .page-wrapper {
            min-height: 100vh;
            background-color: #F2F6FA;
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

        /* Header fixed positioning - Navy Blue Theme (matching sidebar from admin_theme.css) */
        .app-header {
            position: fixed;
            top: 0;
            right: 0;
            left: 270px;
            z-index: 1040;
            background: #053269;
            height: 70px;
            border-bottom: 3px solid var(--secondary-color, #F3B71B);
            box-shadow: 0 4px 20px rgba(6, 52, 109, 0.1);
            border-radius: 0;
        }

        .app-header .navbar {
            height: 100%;
            padding: 0 20px;
        }

        /* Header toggle button - White circle */
        .app-header #headerCollapse {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #ffffff;
            border: none;
            color: #1e3a5f;
            transition: all 0.3s ease;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.15);
        }

        .app-header #headerCollapse:hover {
            background: var(--secondary-color, #F3B71B);
            color: #1e3a5f;
            transform: scale(1.08);
            box-shadow: 0 4px 15px rgba(243, 183, 27, 0.4);
        }

        .app-header #headerCollapse i {
            font-size: 1.2rem;
            font-weight: bold;
        }

        /* Header tournament name */
        .header-tournament-name {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0;
            text-decoration: none;
            transition: all 0.3s ease;
        }

        .header-tournament-name:hover {
            opacity: 0.85;
        }

        .header-tournament-name .trophy-icon {
            width: 28px;
            height: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--secondary-color, #F3B71B);
            font-size: 1.2rem;
        }

        .header-tournament-name .tournament-title {
            font-weight: 500;
            color: #ffffff;
            font-size: 1rem;
            letter-spacing: 0.3px;
        }

        /* Language switcher in header - Gold border circle */
        .header-lang-switcher {
            width: 42px;
            height: 42px;
            border-radius: 50%;
            border: 3px solid var(--secondary-color, #F3B71B);
            background: rgba(255, 255, 255, 0.1);
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 500;
            padding: 0;
        }

        .header-lang-switcher:hover {
            background: var(--secondary-color, #F3B71B);
            color: #1e3a5f;
            box-shadow: 0 4px 15px rgba(243, 183, 27, 0.4);
        }

        .header-lang-switcher .flag-icon {
            font-size: 1.3rem;
        }

        .header-lang-switcher .lang-text {
            display: none;
        }

        /* Hide dropdown arrow */
        .header-lang-switcher.dropdown-toggle::after {
            display: none;
        }

        /* Content spacing below fixed header */
        .body-wrapper>.container-fluid {
            padding-top: 100px;
            padding-bottom: 30px;
        }

        @media (max-width: 1199px) {
            .body-wrapper {
                margin-left: 0 !important;
            }

            .app-header {
                left: 0 !important;
            }
        }

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

            #main-wrapper.sidebar-closed .body-wrapper>.container-fluid {
                max-width: 100% !important;
                padding-left: 30px !important;
                padding-right: 30px !important;
            }
        }

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

        /* Language Switcher Styles */
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
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 100%);
            color: #ffffff;
            transform: translateX(5px);
        }

        .language-dropdown .dropdown-item.active {
            background: linear-gradient(135deg, #f3b71a 0%, #edaf12 100%);
            color: #ffffff;
            font-weight: 600;
        }

        .language-dropdown .dropdown-item.active:hover {
            background: linear-gradient(135deg, #edaf12 0%, #f3b71a 100%);
        }


        /* Tournament Logo in Sidebar */
        .tournament-logo-sidebar {
            width: 80px;
            height: 80px;
            object-fit: contain;
            border-radius: 10px;
            background: white;
            padding: 5px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
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
                    <a href="<?= base_url('tournament/index/' . $data['tour_id']) ?>" class="text-nowrap logo-img">
                        <img src="<?= base_url($data['logo']) ?>" class="tournament-logo-sidebar my-2" alt="<?= $data['name'] ?>" />
                    </a>
                    <div class="close-btn d-xl-none d-block cursor-pointer" id="sidebarDismissBtn" aria-label="Close">
                        <i class="ti ti-x fs-8"></i>
                    </div>
                </div>
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav scroll-sidebar" data-simplebar="">
                    <ul id="sidebarnav">
                        <li class="nav-small-cap">
                            <i class="fas fa-trophy nav-small-cap-icon"></i>
                            <span class="hide-menu"><?= lang('Common.tournament') ?></span>
                        </li>

                        <li class="sidebar-item">
                            <a class="sidebar-link <?= (isset($control[1]) && $control[1] == "index") ? "active" : "" ?>" href="<?= base_url('tournament/index/' . $data['tour_id']) ?>" aria-expanded="false">
                                <i class="fas fa-tachometer-alt"></i>
                                <span class="hide-menu"><?= lang('Tournament.dashboard') ?></span>
                            </a>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link <?= (isset($control[1]) && $control[1] == "team") ? "active" : "" ?>" href="<?= base_url('tournament/team/' . $data['tour_id']) ?>" aria-expanded="false">
                                <i class="fas fa-users"></i>
                                <span class="hide-menu"><?= lang('Tournament.team_list') ?></span>
                            </a>
                        </li>
                        <!-- <li class="sidebar-item">
                            <a class="sidebar-link <?= (isset($control[1]) && $control[1] == "result") ? "active" : "" ?>" href="<?= base_url('tournament/result/' . $data['tour_id']) ?>" aria-expanded="false">
                                <i class="fas fa-list-alt"></i>
                                <span class="hide-menu"><?= lang('Tournament.results') ?></span>
                            </a>
                        </li> -->

                        <li class="nav-small-cap">
                            <i class="fas fa-sign-out-alt nav-small-cap-icon"></i>
                            <span class="hide-menu"><?= lang('Common.menu') ?></span>
                        </li>
                        <li class="sidebar-item">
                            <a class="sidebar-link logout-btn" href="<?= base_url('admin/logout') ?>" aria-expanded="false">
                                <i class="fas fa-power-off"></i>
                                <span class="hide-menu"><?= lang('Common.logout') ?></span>
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
                <nav class="navbar navbar-light">
                    <div class="d-flex align-items-center w-100">
                        <!-- Toggle Button -->
                        <a class="nav-link" id="headerCollapse" href="javascript:void(0)">
                            <i class="ti ti-menu-2"></i>
                        </a>

                        <!-- Tournament Name -->
                        <a href="<?= base_url('tournament/index/' . $data['tour_id']) ?>" class="header-tournament-name ms-3">
                            <span class="trophy-icon">
                                <i class="ti ti-trophy"></i>
                            </span>
                            <span class="tournament-title d-none d-md-block"><?= $data['name'] ?></span>
                        </a>

                        <!-- Language Switcher -->
                        <div class="ms-auto dropdown">
                            <?php
                            helper('language');
                            $currentLang = get_current_language();
                            ?>
                            <a class="header-lang-switcher dropdown-toggle" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php if ($currentLang == 'th'): ?>
                                    <span class="flag-icon">🇹🇭</span>
                                <?php else: ?>
                                    <span class="flag-icon">🇬🇧</span>
                                <?php endif; ?>
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
                        </div>
                    </div>
                </nav>
            </header>
            <!--  Header End -->
            <div class="container-fluid">
                <?php $this->renderSection('content'); ?>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('public/js/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/bootstrap.bundle.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/jquery.mask.min.js') ?>"></script>

    <script src="<?php echo base_url('public/js/popper.min.js') ?>"></script>
    <script src="<?php echo base_url("public/js/DatepickerThai/bootstrap-datepicker.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/DatepickerThai/bootstrap-datepicker-thai.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/DatepickerThai/bootstrap-datepicker.th.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/dataTables.min.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/sweetalert2@11.js"); ?>"></script>
    <script src="<?php echo base_url("public/js/index.global.min.js"); ?>"></script>
    <script src="<?php echo base_url('public/js/cropper.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/highcharts/highcharts.js') ?>"></script>
    <script src="<?php echo base_url('public/js/highcharts/modules/exporting.js') ?> "></script>
    <script src="<?php echo base_url('public/js/highcharts/modules/export-data.js') ?> "></script>
    <script src="<?php echo base_url('public/js/highcharts/modules/accessibility.js') ?> "></script>

    <script src="<?= base_url('public/assets/js/sidebarmenu.js') ?>"></script>
    <script src="<?= base_url('public/js/app.min.js') ?>"></script>
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

        Highcharts.setOptions({
            lang: {
                thousandsSep: ',',
                viewFullscreen: 'แสดงแผนภูมิเต็มจอ',
                printChart: 'พิมพ์แผนภูมิ',
                downloadCSV: 'ดาวน์โหลดไฟล์ .CSV',
                downloadJPEG: 'ดาวน์โหลดไฟล์ .JPEG',
                downloadPDF: 'ดาวน์โหลดไฟล์ .PDF',
                downloadPNG: 'ดาวน์โหลดไฟล์ .PNG',
                downloadXLS: 'ดาวน์โหลดไฟล์ .XLS',
                downloadSVG: 'ดาวน์โหลดไฟล์ .SVG',
                viewData: 'แสดงตารางข้อมูล',
                hideData: 'ซ่อนตารางข้อมูล',
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
    <a style="display:none" href="https://www.tiktok.com/@jootball8?_r=1&_t=ZS-95uPWPKB7RY" target="_blank" class="tiktok-floating-btn" title="สื่อผู้ช่วยสอน">
        <div class="tiktok-icon-wrapper">
            <i class="fa-brands fa-tiktok"></i>
        </div>
        <div class="tiktok-text-label">สื่อผู้ช่วยสอน</div>
    </a>

    <?php $this->renderSection('scripts'); ?>
</body>

</html>