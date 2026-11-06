<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>FOOTBALL PRO - นักเตะมืออาชีพ</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Kanit for Thai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <style>
        :root {
            --primary-black: #1a1a1a;
            --primary-white: #ffffff;
            --primary-gray: #6c757d;
            --primary-orange: #ff4500;
            --dark-gray: #2d2d2d;
            --light-gray: #f8f9fa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--primary-black);
            color: var(--primary-white);
            overflow-x: hidden;
        }

        .navbar {
            background-color: rgba(26, 26, 26, 0.95);
            backdrop-filter: blur(10px);
            padding: 0.2rem 0;
            transition: all 0.3s ease;
        }

        .navbar-scrolled {
            background-color: rgba(255, 255, 255, 0.98) !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.5rem;
            color: var(--primary-white) !important;
            letter-spacing: 2px;
        }

        .navbar-scrolled .navbar-brand {
            color: var(--primary-black) !important;
        }

        .navbar-nav .nav-link {
            color: var(--primary-white) !important;
            font-weight: 500;
            margin: 0 1rem;
            transition: color 0.3s ease;
        }

        .navbar-scrolled .navbar-nav .nav-link {
            color: var(--primary-black) !important;
        }

        .navbar-scrolled .navbar-nav .nav-link:hover {
            color: var(--primary-orange) !important;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-orange) !important;
        }

        @media (max-width: 768px) {
            .navbar-collapse {
                background: rgba(26, 26, 26, 0.98);
                border-radius: 8px;
                padding: 1rem;
                margin-top: 0.5rem;
            }

            .navbar-scrolled .navbar-collapse {
                background: rgba(255, 255, 255, 0.98) !important;
            }

            .navbar-scrolled .navbar-nav .nav-link {
                color: var(--primary-black) !important;
            }

            .navbar-scrolled .navbar-nav .nav-link:hover {
                color: var(--primary-orange) !important;
            }
        }

        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            background: linear-gradient(135deg, var(--primary-black) 0%, var(--dark-gray) 100%);
        }

        .hero-content {
            z-index: 2;
        }

        .hero-title {
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 2rem;
            letter-spacing: -2px;
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--primary-gray);
            margin-bottom: 3rem;
            line-height: 1.6;
        }

        .btn-primary-orange {
            background-color: var(--primary-orange);
            border: none;
            padding: 1rem 2.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }

        .btn-primary-orange:hover {
            background-color: #e63e00;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(255, 69, 0, 0.3);
        }

        .player-avatar {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            border: 5px solid var(--primary-orange);
            margin-bottom: 2rem;
            box-shadow: 0 20px 60px rgba(255, 69, 0, 0.3);
        }

        .companies-section {
            padding: 1.5rem 0;
            background-color: var(--dark-gray);
        }

        .company-logo {
            opacity: 0.6;
            transition: opacity 0.3s ease;
            filter: grayscale(100%);
        }

        .company-logo:hover {
            opacity: 1;
            filter: grayscale(0%);
        }

        .services-section {
            padding: 6rem 0;
        }

        .section-title {
            font-size: 3rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 4rem;
            letter-spacing: -1px;
        }

        .service-card {
            background: linear-gradient(135deg, var(--dark-gray) 0%, #2a2a2a 100%);
            border-radius: 15px;
            padding: 2.5rem;
            height: 100%;
            transition: all 0.3s ease;
            border: 1px solid #404040;
            position: relative;
            overflow: hidden;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
        }

        .service-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-orange);
            background: rgba(255, 69, 0, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--primary-white);
        }

        .service-description {
            color: var(--primary-gray);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .service-arrow {
            position: absolute;
            top: 2rem;
            right: 2rem;
            color: var(--primary-orange);
            font-size: 1.2rem;
            opacity: 0;
            transition: all 0.3s ease;
        }

        .service-card:hover .service-arrow {
            opacity: 1;
            transform: translate(5px, -5px);
        }

        .recent-work {
            padding: 6rem 0;
            background-color: var(--dark-gray);
        }

        .work-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .work-item {
            background: var(--primary-black);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.3s ease;
            border: 1px solid #404040;
        }

        .work-item:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.4);
        }

        .work-image {
            height: 250px;
            background: linear-gradient(45deg, var(--primary-orange), #ff6b35);
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .work-image i {
            font-size: 4rem;
            color: var(--primary-white);
        }

        .work-content {
            padding: 2rem;
        }

        .work-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }

        .work-category {
            color: var(--primary-orange);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-size: 0.9rem;
        }

        .floating-elements {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            pointer-events: none;
            overflow: hidden;
        }

        .floating-circle {
            position: absolute;
            border: 2px solid rgba(255, 69, 0, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .circle-1 {
            width: 200px;
            height: 200px;
            top: 20%;
            right: 10%;
            animation-delay: 0s;
        }

        .circle-2 {
            width: 100px;
            height: 100px;
            bottom: 30%;
            left: 15%;
            animation-delay: 2s;
        }

        .circle-3 {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 20%;
            animation-delay: 4s;
        }

        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }

            .section-title {
                font-size: 2rem;
            }

            .player-avatar {
                width: 200px;
                height: 200px;
            }

            .work-grid {
                grid-template-columns: 1fr;
            }
        }

        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            color: var(--primary-white);
            animation: bounce 2s infinite;
        }

        @keyframes bounce {
            0%, 20%, 50%, 80%, 100% {
                transform: translateX(-50%) translateY(0);
            }
            40% {
                transform: translateX(-50%) translateY(-10px);
            }
            60% {
                transform: translateX(-50%) translateY(-5px);
            }
        }

        /* Footer Styles */
        .footer-section {
            background: linear-gradient(135deg, var(--primary-black) 0%, #0f0f0f 100%);
            border-top: 1px solid #404040;
            color: var(--primary-white);
        }

        .footer-brand h4 {
            color: var(--primary-white);
            font-weight: 800;
            letter-spacing: 2px;
        }

        .footer-tagline {
            color: var(--primary-gray);
            font-size: 0.9rem;
        }

        .social-links {
            display: flex;
            justify-content: center;
            justify-content: end;
            gap: 1rem;
        }

        .social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: var(--dark-gray);
            color: var(--primary-white);
            border-radius: 50%;
            text-decoration: none;
            transition: all 0.3s ease;
            border: 2px solid transparent;
        }

        .social-link:hover {
            background: var(--primary-orange);
            color: var(--primary-white);
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(255, 69, 0, 0.3);
        }

        .contact-info {
            color: var(--primary-gray);
            font-size: 0.9rem;
        }

        .contact-info i {
            color: var(--primary-orange);
        }

        .footer-divider {
            border-color: #404040;
            margin: 0;
        }

        .credit-text {
            color: var(--primary-gray);
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .developer-link,
        .partner-link {
            text-decoration: none;
            color: inherit;
            transition: all 0.3s ease;
        }

        .developer-link:hover,
        .partner-link:hover {
            transform: translateY(-2px);
        }

        .jigsaw-logo,
        .fa-logo {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1rem;
            background: var(--dark-gray);
            border-radius: 10px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .jigsaw-logo:hover,
        .fa-logo:hover {
            border-color: var(--primary-orange);
            box-shadow: 0 5px 20px rgba(255, 69, 0, 0.2);
        }

        .jigsaw-icon,
        .fa-icon {
            width: 50px;
            height: 50px;
            background: var(--primary-orange);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-white);
            font-size: 1.5rem;
        }

        .jigsaw-text,
        .fa-text {
            display: flex;
            flex-direction: column;
        }

        .company-name,
        .fa-name {
            font-weight: 800;
            font-size: 1.1rem;
            color: var(--primary-white);
            letter-spacing: 1px;
        }

        .company-sub,
        .fa-sub {
            font-size: 0.8rem;
            color: var(--primary-gray);
            letter-spacing: 0.5px;
        }

        .copyright-text {
            color: var(--primary-gray);
            font-size: 0.9rem;
        }

        .footer-link {
            color: var(--primary-gray);
            text-decoration: none;
            transition: color 0.3s ease;
        }

        .footer-link:hover {
            color: var(--primary-orange);
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

        @media (max-width: 768px) {
            .social-links {
                justify-content: center;
            }

            .jigsaw-logo,
            .fa-logo {
                justify-content: center;
                margin-bottom: 1rem;
            }

            .footer-brand {
                text-align: center;
                margin-bottom: 2rem;
            }
        }
    </style>
</head>
<?php $this->renderSection('style'); ?>
<?php
$url = uri_string();
$control = explode('/', $url);
?>
<body>
<?= $this->include('partials/preloader') ?>
<?= $this->include('partials/announcement') ?>

    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="#"><img src="<?php echo base_url('public/img/jootball_logo.png')?>" height="55px"></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">หน้าแรก</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#services">บริการ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#portfolio">ผลงาน</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#about">เกี่ยวกับ</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#contact">ติดต่อ</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>


    <?php $this->renderSection('content'); ?>


    <!-- Footer Section -->
    <footer class="footer-section">
        <div class="container">
            <div class="row align-items-center py-4">
                <div class="col-lg-6 text-center text-lg-start mb-3 mb-lg-0">
                    <div class="footer-brand">
                        <h4 class="mb-0">Jootball</h4>
                        <p class="footer-tagline mb-0">นักเตะมืออาชีพ</p>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="footer-links text-center text-lg-end">
                        <div class="social-links mb-3">
                            <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                        </div>
                        <div class="contact-info">
                            <p class="mb-1"><i class="fas fa-envelope me-2"></i>contact@footballpro.com</p>
                            <p class="mb-0"><i class="fas fa-phone me-2"></i>+66 (0) 2-123-4567</p>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">

            <!-- Developer & Partner Credits -->
            <div class="row align-items-center py-4">
                <div class="col-lg-6 text-center text-lg-start mb-3 mb-lg-0">
                    <div class="developer-credit">
                        <p class="credit-text mb-2">พัฒนาโดย</p>
                        <div class="developer-logo">
                            <a href="https://jigsawinnovation.com" target="_blank" class="developer-link">
                                <div class="jigsaw-logo">
                                    <div class="jigsaw-icon">
                                        <i class="fas fa-puzzle-piece"></i>
                                    </div>
                                    <div class="jigsaw-text">
                                        <span class="company-name">JIGSAW</span>
                                        <span class="company-sub">INNOVATION</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 text-center text-lg-end">
                    <div class="partner-credit">
                        <p class="credit-text mb-2">ร่วมมือกับ</p>
                        <div class="partner-logo">
                            <a href="#" class="partner-link">
                                <div class="fa-logo">
                                    <div class="fa-icon">
                                        <i class="fas fa-futbol"></i>
                                    </div>
                                    <div class="fa-text">
                                        <span class="fa-name">สมาคมฟุตบอล</span>
                                        <span class="fa-sub">แห่งประเทศไทย</span>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <hr class="footer-divider">

            <!-- Copyright -->
            <div class="row">
                <div class="col-12 text-center py-3">
                    <p class="copyright-text mb-0">
                        © 2024 Football Pro. สงวนลิขสิทธิ์. |
                        <a href="<?= base_url('privacy') ?>" class="footer-link">นโยบายความเป็นส่วนตัว</a> |
                        <a href="#" class="footer-link">เงื่อนไขการใช้งาน</a>
                    </p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <script>
        $(document).ready(function() {
            // Smooth scrolling for navigation links
            $('a[href^="#"]').on('click', function(event) {
                var target = $(this.getAttribute('href'));
                if( target.length ) {
                    event.preventDefault();
                    $('html, body').stop().animate({
                        scrollTop: target.offset().top - 80
                    }, 1000);
                }
            });

            // Navbar background change on scroll
            $(window).scroll(function() {
                if ($(this).scrollTop() > 100) {
                    $('.navbar').addClass('navbar-scrolled');
                } else {
                    $('.navbar').removeClass('navbar-scrolled');
                }
            });

            // Add animation on scroll for service cards
            $(window).scroll(function() {
                $('.service-card').each(function() {
                    var elementTop = $(this).offset().top;
                    var elementBottom = elementTop + $(this).outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();

                    if (elementBottom > viewportTop && elementTop < viewportBottom) {
                        $(this).addClass('animate-in');
                    }
                });
            });

            // Add hover effects for work items
            $('.work-item').hover(
                function() {
                    $(this).find('.work-image i').addClass('fa-spin');
                },
                function() {
                    $(this).find('.work-image i').removeClass('fa-spin');
                }
            );

            // Add click animation for buttons
            $('.btn-primary-orange').on('click', function(e) {
                let ripple = $('<span class="ripple"></span>');
                $(this).append(ripple);

                setTimeout(() => {
                    ripple.remove();
                }, 600);
            });

            // Parallax effect for floating elements
            $(window).scroll(function() {
                var scrolled = $(window).scrollTop();
                var parallax = scrolled * 0.5;

                $('.floating-circle').css('transform', 'translateY(' + parallax + 'px)');
            });

            // Add loading animation
            $(window).on('load', function() {
                $('.hero-content').addClass('fade-in');
                $('.player-avatar').addClass('fade-in-right');
            });

            // Mobile menu close on link click
            $('.navbar-nav .nav-link').on('click', function() {
                if ($(window).width() < 992) {
                    $('.navbar-collapse').collapse('hide');
                }
            });

            // Add hover animation for footer logos
            $('.developer-link, .partner-link').hover(
                function() {
                    $(this).find('.jigsaw-icon i, .fa-icon i').addClass('fa-spin');
                },
                function() {
                    $(this).find('.jigsaw-icon i, .fa-icon i').removeClass('fa-spin');
                }
            );

            // Footer logo click tracking
            $('.developer-link').on('click', function() {
                // Analytics tracking can be added here
                console.log('Jigsaw Innovation link clicked');
            });

            $('.partner-link').on('click', function() {
                // Analytics tracking can be added here
                console.log('Football Association link clicked');
            });
        });

        // Additional CSS for animations
        $('<style>').prop('type', 'text/css').html(`
            .animate-in {
                transform: translateY(0) !important;
                opacity: 1 !important;
            }

            .service-card {
                transform: translateY(50px);
                opacity: 0;
                transition: all 0.8s ease;
            }

            .fade-in {
                animation: fadeIn 1s ease-in-out;
            }

            .fade-in-right {
                animation: fadeInRight 1s ease-in-out;
            }

            @keyframes fadeIn {
                from { opacity: 0; transform: translateY(30px); }
                to { opacity: 1; transform: translateY(0); }
            }

            @keyframes fadeInRight {
                from { opacity: 0; transform: translateX(50px); }
                to { opacity: 1; transform: translateX(0); }
            }

            .ripple {
                position: absolute;
                border-radius: 50%;
                background: rgba(255, 255, 255, 0.3);
                animation: ripple-animation 0.6s linear;
                pointer-events: none;
            }

            @keyframes ripple-animation {
                to {
                    transform: scale(4);
                    opacity: 0;
                }
            }
        `).appendTo('head');
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
