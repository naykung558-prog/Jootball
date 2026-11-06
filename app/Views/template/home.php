<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Jootball</title>
    <link rel="icon" href="<?= base_url('public/img/jootball_logo.png') ?>" type="image/icon type">
    <!-- Bootstrap CSS -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <!-- Google Fonts: Kanit for Thai -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

    <!-- Swiper -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css"/>
    <script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>

    <style>
        @font-face {
            font-family: 'ds_digital';
            src: url('<?php echo base_url('public/assets/font/DS-DIGI.TTF')?>') format('truetype') ;
        }

        @font-face {
            font-family: 'Viafont';
            src: url('<?php echo base_url('public/assets/font/Viafont.otf')?>');
            src: url('<?php echo base_url('public/assets/font/Viafont.ttf')?>') format('truetype') ;
        }

        :root {
            --primary-black: #1a1a1a;
            --primary-white: #ffffff;
            --primary-gray: #4a5568;
            --primary-yellow: #f3b71a;
            --primary-blue: #1e3a8a;
            --dark-gray: #374151;
            --light-gray: #e2e8f0;
            --dark-blue: #0f172a;
            /* World Cup 2026 spectrum palette */
            --wc-green: #3CAC3B;
            --wc-red: #E61D25;
            --wc-blue: #2A398D;
            --wc-gold: #F5A623;
            --wc-cyan: #19C3D6;
            --wc-purple: #7A3FB0;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background-color: var(--primary-white);
            color: var(--primary-black);
            overflow-x: hidden;
        }

        .navbar {
            background-color: transparent;
            backdrop-filter: none;
            padding: 1rem 0;
            transition: all 0.3s ease;
            border-bottom: 1px solid rgba(255, 255, 255, 0.2);
        }

        .navbar.scrolled {
            background-color: rgba(255, 255, 255, 0.98);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            padding: 0.75rem 0;
            border-bottom: 1px solid var(--light-gray);
        }

        .navbar-brand {
            font-weight: 800;
            font-size: 1.3rem;
            color: var(--primary-white) !important;
            letter-spacing: 1px;
            display: flex;
            align-items: center;
            gap: 8px;
            transition: all 0.3s ease;
        }

        .navbar.scrolled .navbar-brand {
            font-size: 1.2rem;
            color: var(--primary-black) !important;
        }

        .navbar-brand img {
            height: 45px;
            width: auto;
            transition: all 0.3s ease;
        }

        .navbar.scrolled .navbar-brand img {
            height: 40px;
        }

        /* Sponsor Badge - Small logo next to main logo */
        .sponsor-badge {
            display: inline-block;
            padding: 2px 6px;
            background: rgba(255, 255, 255, 0.9);
            border-left: 1px solid rgba(255, 255, 255, 0.5);
            margin-left: 3px;
            padding-left: 8px;
            max-height: 25px;
            max-width: 80px;
            overflow: hidden;
            vertical-align: middle;
            border-radius: 4px;
        }

        .navbar.scrolled .sponsor-badge {
            max-height: 20px;
            max-width: 60px;
            background: transparent;
            border-left: 1px solid #e2e8f0;
        }

        .sponsor-badge img {
            height: 16px;
            width: auto;
            max-width: 100%;
            object-fit: contain;
            display: block;
        }

        .navbar.scrolled .sponsor-badge img {
            height: 14px;
        }

        /* Responsive: Show sponsor badge on mobile */
        @media (max-width: 991px) {
            .sponsor-badge {
                display: inline-block !important;
                max-height: 20px;
                max-width: 60px;
            }
            .sponsor-badge img {
                height: 14px;
            }
            .navbar-brand {
                flex-wrap: nowrap;
            }
        }

        .navbar-nav .nav-link {
            color: #ffffff !important;
            font-weight: 500;
            margin: 0 0.5rem;
            padding: 0.5rem 0.75rem;
            font-size: 0.9rem;
            transition: all 0.3s ease;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .navbar-nav .nav-link:hover {
            color: var(--primary-yellow) !important;
        }

        .navbar-nav .nav-link i {
            font-size: 1rem;
            color: inherit !important;
        }

        .navbar.scrolled .navbar-nav .nav-link {
            color: var(--primary-black) !important;
        }

        .navbar.scrolled .navbar-nav .nav-link:hover {
            color: var(--primary-blue) !important;
        }

        /* Language Switcher */
        .navbar-nav .dropdown-toggle {
            color: #ffffff !important;
        }

        .navbar.scrolled .navbar-nav .dropdown-toggle {
            color: var(--primary-black) !important;
        }

        .navbar-nav .dropdown-menu {
            border-radius: 12px;
            padding: 0.5rem;
            min-width: 180px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.15);
            border: none;
            background: white;
        }

        .navbar-nav .dropdown-item {
            border-radius: 8px;
            padding: 0.6rem 1rem;
            transition: all 0.3s ease;
            font-weight: 500;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .navbar-nav .dropdown-item:hover {
            background-color: rgba(30, 58, 138, 0.1);
            color: var(--primary-blue);
        }

        .navbar-nav .dropdown-item.active {
            background-color: var(--primary-blue);
            color: white;
        }

        /* CTA Button */
        .navbar-nav .btn-primary {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, #f3b71a 100%);
            border: none;
            font-weight: 600;
            padding: 0.6rem 1.5rem;
            box-shadow: 0 4px 15px rgba(243, 183, 26, 0.3);
            transition: all 0.3s ease;
            border-radius: 50px;
            color: var(--primary-black) !important;
        }

        .navbar-nav .btn-primary:hover {
            background: linear-gradient(135deg, #f3b71a 0%, var(--primary-yellow) 100%);
            box-shadow: 0 6px 20px rgba(243, 183, 26, 0.4);
            transform: translateY(-2px);
        }

        .navbar.scrolled .navbar-nav .btn-primary {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #2c5282 100%);
            box-shadow: 0 4px 15px rgba(30, 58, 138, 0.3);
            color: white !important;
        }

        .navbar.scrolled .navbar-nav .btn-primary:hover {
            background: linear-gradient(135deg, #2c5282 0%, var(--primary-blue) 100%);
            box-shadow: 0 6px 20px rgba(30, 58, 138, 0.4);
        }

        @media (max-width: 991px) {
            .navbar-collapse {
                background: white;
                border-radius: 16px;
                padding: 1rem;
                margin-top: 1rem;
                box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            }

            .navbar-nav .nav-link,
            .navbar-nav .dropdown-toggle {
                padding: 0.75rem 1rem;
                margin: 0.25rem 0;
                justify-content: flex-start;
                color: #16478C !important;
            }

            .navbar-nav .nav-link:hover {
                color: var(--primary-blue) !important;
            }

            .navbar-nav .nav-item {
                margin: 0;
            }

            .navbar-nav .btn-primary {
                width: 100%;
                margin-top: 0.5rem;
                justify-content: center;
            }
        }

        .hero-section {
            min-height: 100vh;
            display: flex;
            align-items: center;
            position: relative;
            background: linear-gradient(135deg, var(--primary-white) 0%, var(--light-gray) 100%);
            overflow: hidden;
        }

        .hero-content {
            z-index: 2;
            animation: slideInLeft 1s ease-out;
        }

        .hero-title {
            color: var(--primary-blue);
            font-size: 4rem;
            font-weight: 900;
            line-height: 1.1;
            margin-bottom: 2rem;
            letter-spacing: -2px;
            position: relative;
        }

        .hero-title::after {
            content: '';
            position: absolute;
            width: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-blue), var(--primary-yellow));
            bottom: -10px;
            left: 0;
            animation: expandLine 2s ease-out 0.5s forwards;
        }

        .hero-logo {
            animation: fadeInRotate 1.5s ease-out 0.3s both;
        }

        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-50px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        @keyframes fadeInRotate {
            from {
                opacity: 0;
                transform: translateY(30px) rotate(-10deg);
            }
            to {
                opacity: 1;
                transform: translateY(0) rotate(0deg);
            }
        }

        @keyframes expandLine {
            to {
                width: 100%;
            }
        }

        .hero-subtitle {
            font-size: 1.2rem;
            color: var(--dark-gray);
            margin-bottom: 3rem;
            line-height: 1.6;
        }

        .btn-primary-yellow {
            background-color: var(--primary-yellow);
            border: none;
            padding: 1rem 2.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 5px;
            transition: all 0.3s ease;
            color: var(--primary-white);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-yellow::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary-yellow:hover::before {
            left: 100%;
        }

        .btn-primary-yellow:hover {
            background-color: #edaf12;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
            color: var(--primary-white);
        }

        .btn-primary-blue {
            background-color: var(--primary-blue);
            border: none;
            padding: 1rem 2.5rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            border-radius: 5px;
            transition: all 0.3s ease;
            color: var(--primary-white);
            position: relative;
            overflow: hidden;
        }

        .btn-primary-blue::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s ease;
        }

        .btn-primary-blue:hover::before {
            left: 100%;
        }

        .btn-primary-blue:hover {
            background-color: #08255c;
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(220, 53, 69, 0.3);
            color: var(--primary-white);
        }

        .player-avatar {
            width: 300px;
            height: 300px;
            border-radius: 50%;
            border: 5px solid var(--primary-blue);
            margin-bottom: 2rem;
            box-shadow: 0 20px 60px rgba(220, 53, 69, 0.3);
        }

        .companies-section {
            padding: 1.5rem 0;
            background-color: var(--light-gray);
            position: relative;
            overflow: hidden;
        }

        .companies-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(220, 53, 69, 0.1), transparent);
            animation: shimmer 3s infinite;
        }

        @keyframes shimmer {
            0% { left: -100%; }
            100% { left: 100%; }
        }

        .company-logo {
            opacity: 0.8;
            transition: all 0.3s ease;
            color: var(--dark-gray);
            transform: translateY(0);
        }

        .company-logo:hover {
            opacity: 1;
            color: var(--primary-yellow);
            transform: translateY(-5px);
        }

        .company-logo h5 {
            animation: fadeInUp 0.8s ease-out;
            animation-fill-mode: both;
        }

        .company-logo:nth-child(1) h5 { animation-delay: 0.1s; }
        .company-logo:nth-child(2) h5 { animation-delay: 0.2s; }
        .company-logo:nth-child(3) h5 { animation-delay: 0.3s; }
        .company-logo:nth-child(4) h5 { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .services-section {
            padding: 6rem 0;
            background: var(--primary-white);
        }

        .section-title {
            font-size: 3rem;
            font-weight: 800;
            text-align: center;
            margin-bottom: 4rem;
            letter-spacing: -1px;
            color: var(--primary-black);
        }

        .service-card {
            background: linear-gradient(135deg, var(--primary-white) 0%, #f8f9fa 100%);
            border-radius: 15px;
            padding: 2.5rem;
            height: 100%;
            transition: all 0.4s ease;
            border: 2px solid var(--light-gray);
            position: relative;
            overflow: hidden;
            color: var(--primary-black);
            transform: translateY(20px);
            opacity: 0;
        }

        .service-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(30, 58, 138, 0.05), transparent);
            transition: left 0.5s ease;
        }

        .service-card:hover::before {
            left: 100%;
        }

        .service-card:hover {
            transform: translateY(-10px);
            box-shadow: 0 20px 60px rgba(30, 58, 138, 0.15);
            border-color: var(--primary-blue);
        }

        .service-card.animate-in {
            transform: translateY(0);
            opacity: 1;
            animation: slideInCard 0.6s ease-out;
        }

        @keyframes slideInCard {
            from {
                transform: translateY(50px);
                opacity: 0;
            }
            to {
                transform: translateY(0);
                opacity: 1;
            }
        }

        .service-number {
            font-size: 1.5rem;
            font-weight: 800;
            color: var(--primary-white);
            background: var(--primary-yellow);
            padding: 0.5rem 1rem;
            border-radius: 5px;
            display: inline-block;
            margin-bottom: 1.5rem;
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 1rem;
            color: var(--primary-blue);
        }

        .service-description {
            color: var(--dark-gray);
            line-height: 1.6;
            margin-bottom: 2rem;
        }

        .service-arrow {
            position: absolute;
            top: 2rem;
            right: 2rem;
            color: var(--primary-yellow);
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
            background-color: var(--light-gray);
        }

        .work-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(350px, 1fr));
            gap: 2rem;
            margin-top: 3rem;
        }

        .work-item {
            background: var(--primary-white);
            border-radius: 15px;
            overflow: hidden;
            transition: all 0.4s ease;
            border: 2px solid var(--light-gray);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transform: translateY(0);
        }

        .work-item:hover {
            transform: translateY(-10px) scale(1.02);
            box-shadow: 0 20px 40px rgba(30, 58, 138, 0.15);
            border-color: var(--primary-blue);
        }

        .work-image {
            height: 250px;
            background: linear-gradient(45deg, var(--light-gray), var(--primary-blue));
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }

        .work-image::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(45deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transform: translateX(-100%) rotate(45deg);
            transition: transform 0.6s ease;
        }

        .work-item:hover .work-image::before {
            transform: translateX(100%) rotate(45deg);
        }

        .work-image i {
            font-size: 4rem;
            color: var(--primary-white);
            transition: all 0.3s ease;
            z-index: 2;
        }

        .work-item:hover .work-image i {
            transform: scale(1.2) rotate(360deg);
        }

        .work-content {
            padding: 2rem;
            color: var(--primary-black);
        }

        .work-title {
            font-size: 1.3rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
            color: var(--primary-blue);
        }

        .work-category {
            color: var(--primary-yellow);
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
            border: 2px solid rgba(220, 53, 69, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }

        .circle-1 {
            width: 200px;
            height: 200px;
            top: 20%;
            right: 10%;
            animation-delay: 0s;
            border-color: rgba(30, 58, 138, 0.2);
            animation: float 8s ease-in-out infinite, pulse 4s ease-in-out infinite;
        }

        .circle-2 {
            width: 100px;
            height: 100px;
            bottom: 30%;
            left: 15%;
            animation-delay: 2s;
            border-color: rgba(220, 53, 69, 0.2);
            animation: float 6s ease-in-out infinite reverse, pulse 3s ease-in-out infinite 1s;
        }

        .circle-3 {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 20%;
            animation-delay: 4s;
            border-color: rgba(30, 58, 138, 0.15);
            animation: float 10s ease-in-out infinite, pulse 5s ease-in-out infinite 2s;
        }

        @keyframes pulse {
            0%, 100% {
                opacity: 0.3;
                transform: scale(1);
            }
            50% {
                opacity: 0.8;
                transform: scale(1.1);
            }
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
            .navbar {
                padding: 0.5rem 0;
            }

            .navbar.scrolled {
                padding: 0.25rem 0;
            }

            .navbar-brand img {
                height: 40px !important;
            }

            .navbar.scrolled .navbar-brand img {
                height: 35px !important;
            }

            .navbar-toggler {
                border: none;
                padding: 0.5rem;
                font-size: 1.2rem;
                margin-right: 0;
                filter: invert(1);
            }

            .navbar.scrolled .navbar-toggler {
                filter: none;
            }

            .navbar-toggler:focus {
                box-shadow: none;
            }

            .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
                padding: 1rem;
                margin-top: 0.5rem;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            }

            .navbar-nav .nav-link {
                margin: 0.25rem 0;
                padding: 0.75rem 1rem;
                font-size: 1rem;
                color: var(--primary-black) !important;
            }

            .navbar.scrolled .navbar-collapse {
                background: rgba(255, 255, 255, 0.98);
            }

            .navbar.scrolled .navbar-nav .nav-link {
                color: var(--primary-black) !important;
            }

            .navbar.scrolled .navbar-nav .nav-link:hover {
                color: var(--primary-blue) !important;
            }

            .navbar.scrolled .navbar-nav .dropdown-toggle {
                color: var(--primary-black) !important;
            }

            .hero-section {
                padding-top: 70px;
                min-height: 100vh;
            }

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

            .hero-logo img {
                height: 180px !important;
                max-width: 180px;
            }

            .container-stat {
                padding-top: 60px !important;
            }

            .feature-highlight {
                margin-bottom: 1rem;
            }

            .service-card {
                margin-bottom: 1.5rem;
            }
        }

        @media (max-width: 576px) {
            .navbar {
                padding: 0.25rem 0;
            }

            .navbar.scrolled {
                padding: 0.2rem 0;
            }

            .navbar-brand {
                font-size: 1.1rem;
            }

            .navbar-brand img {
                height: 35px !important;
            }

            .navbar.scrolled .navbar-brand img {
                height: 30px !important;
            }

            .sponsor-badge {
                display: none;
            }

            .navbar-toggler {
                padding: 0.4rem;
                font-size: 1.1rem;
            }

            .navbar.scrolled .navbar-nav .nav-link {
                color: var(--primary-black) !important;
            }

            .navbar.scrolled .navbar-nav .nav-link:hover {
                color: var(--primary-blue) !important;
            }

            .navbar.scrolled .navbar-nav .dropdown-toggle {
                color: var(--primary-black) !important;
            }

            .hero-title {
                font-size: 2rem;
                line-height: 1.2;
            }

            .hero-logo img {
                height: 150px !important;
                max-width: 150px;
            }

            .container-stat {
                padding-top: 50px !important;
            }

            .section-title {
                font-size: 1.8rem;
            }

            .service-card {
                padding: 2rem 1.5rem;
            }

            .work-item {
                padding: 2rem 1.5rem;
            }
        }

        .scroll-indicator {
            position: absolute;
            bottom: 2rem;
            left: 50%;
            transform: translateX(-50%);
            color: var(--primary-black);
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

        /* Footer Styles Redesigned */
        .footer-section {
            background: #0B1548;
            color: #ffffff;
            position: relative;
            padding-top: 80px;
            overflow: hidden;
            border-top: 5px solid #FBC02D;
        }

        .footer-section::before {
            content: '';
            position: absolute;
            top: -100px;
            left: -100px;
            width: 300px;
            height: 300px;
            background: rgba(33, 71, 144, 0.2);
            filter: blur(100px);
            border-radius: 50%;
            pointer-events: none;
        }

        .footer-section::after {
            content: '';
            position: absolute;
            bottom: -50px;
            right: -50px;
            width: 250px;
            height: 250px;
            background: rgba(251, 192, 45, 0.1);
            filter: blur(80px);
            border-radius: 50%;
            pointer-events: none;
        }

        .footer-brand-logo {
            font-family: 'Orbitron', sans-serif;
            font-weight: 900;
            font-size: 2.5rem;
            color: #ffffff;
            letter-spacing: 2px;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            display: inline-block;
        }

        .footer-brand-logo span {
            color: #FBC02D;
        }

        .footer-tagline {
            color: rgba(255, 255, 255, 0.6);
            font-size: 1rem;
            max-width: 400px;
            line-height: 1.6;
        }

        .footer-heading {
            font-weight: 800;
            font-size: 1.15rem;
            color: #ffffff;
            margin-bottom: 2rem;
            position: relative;
            padding-bottom: 10px;
        }

        .footer-heading::after {
            content: '';
            position: absolute;
            left: 0;
            bottom: 0;
            width: 30px;
            height: 3px;
            background: #FBC02D;
            border-radius: 2px;
        }

        .social-links {
            display: flex;
            gap: 1rem;
            justify-content: flex-start;
        }

        .social-link {
            width: 42px;
            height: 42px;
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.2rem;
            transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
            text-decoration: none;
        }

        .social-link:hover {
            background: #FBC02D;
            color: #0B1548;
            transform: translateY(-5px) rotate(10deg);
            border-color: #FBC02D;
            box-shadow: 0 10px 20px rgba(251, 192, 45, 0.3);
        }

        .footer-contact-item {
            display: flex;
            align-items: flex-start;
            gap: 15px;
            margin-bottom: 1.25rem;
            color: rgba(255, 255, 255, 0.7);
            transition: color 0.3s ease;
        }

        .footer-contact-item i {
            color: #FBC02D;
            margin-top: 4px;
            font-size: 1.1rem;
        }

        .footer-contact-item:hover {
            color: #ffffff;
        }

        .partner-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 20px;
            padding: 25px;
            transition: all 0.4s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
        }

        .partner-card:hover {
            background: rgba(255, 255, 255, 0.06);
            transform: translateY(-8px);
            border-color: rgba(251, 192, 45, 0.3);
        }

        .partner-logo-box {
            background: #e9ecef; /* Neutral light gray to support both dark and white logos */
            padding: 12px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 15px;
            width: fit-content;
            box-shadow: inset 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
        }

        .partner-logo-box img {
            max-width: 100%;
            height: auto;
            max-height: 40px;
            object-fit: contain;
        }

        .partner-info .title {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.8rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 5px;
        }

        .partner-info .name {
            color: #ffffff;
            font-weight: 700;
            font-size: 1rem;
        }

        .footer-divider {
            border-color: rgba(255, 255, 255, 0.1);
            margin: 0;
        }

        .copyright-bar {
            background: #060D2D;
            padding: 25px 0;
        }

        .copyright-text {
            color: rgba(255, 255, 255, 0.4);
            font-size: 0.9rem;
        }

        .footer-bottom-links {
            display: flex;
            gap: 20px;
            justify-content: flex-end;
        }

        .footer-bottom-link {
            color: rgba(255, 255, 255, 0.4);
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s ease;
        }

        .footer-bottom-link:hover {
            color: #FBC02D;
        }

        @media (max-width: 992px) {
            .footer-bottom-links {
                justify-content: center;
                margin-top: 15px;
            }
            .footer-heading {
                margin-top: 2rem;
            }
            .social-links {
                justify-content: center;
            }
            .footer-brand {
                text-align: center;
            }
            .footer-tagline {
                margin: 0 auto 1.5rem;
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
            border: 1px solid var(--light-gray);
            padding: 0.5rem 0;
        }

        .language-dropdown .dropdown-item {
            padding: 0.75rem 1.25rem;
            transition: all 0.3s ease;
            border-radius: 8px;
            margin: 0.25rem 0.5rem;
        }

        .language-dropdown .dropdown-item:hover {
            background: linear-gradient(135deg, var(--primary-blue) 0%, #1e40af 100%);
            color: var(--primary-white);
            transform: translateX(5px);
        }

        .language-dropdown .dropdown-item.active {
            background: linear-gradient(135deg, var(--primary-yellow) 0%, #edaf12 100%);
            color: var(--primary-white);
            font-weight: 600;
        }

        .language-dropdown .dropdown-item.active:hover {
            background: linear-gradient(135deg, #edaf12 0%, var(--primary-yellow) 100%);
        }

        #languageDropdown {
            background: rgba(255, 255, 255, 0.1);
            padding: 0.5rem 1rem;
            border-radius: 50px;
            transition: all 0.3s ease;
        }

        #languageDropdown:hover {
            background: rgba(255, 255, 255, 0.2);
            transform: translateY(-2px);
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
	            margin-right: -5px;
	            font-size: 0.95rem;
	            font-weight: 500;
	            opacity: 1;
	            transform: translateX(0);
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
	                display: block;
	                padding: 8px 28px 8px 16px;
	                margin-right: -8px;
	                font-size: 0.82rem;
	            }
	        }
    </style>
</head>
<body>
<?= $this->include('partials/preloader') ?>
<?= $this->include('partials/announcement') ?>


    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg fixed-top">
        <div class="container">
            <a class="navbar-brand" href="<?= base_url() ?>">
                <img src="<?= base_url('public/img/jootball_logo.png') ?>" height="55px" alt="Jootball">
                <?php if (!empty($navbar_sponsor)): ?>
                <div class="sponsor-badge">
                    <img src="<?= base_url($navbar_sponsor['logo']) ?>" alt="<?= $navbar_sponsor['name'] ?>" style="width: 100%; height: 100%; object-fit: contain;">
                </div>
                <?php endif; ?>
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto align-items-center">
                    <li class="nav-item">
                        <a class="nav-link" href="#home">
                            <?= lang('Common.home') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="https://dev.jigsawinnovation.com/certificate_service/">
                            <?= lang('Common.certificate') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('auth/tournament/list') ?>">
                            <?= lang('Common.tournament') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#result">
                            <?= lang('Common.result') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('home/statistics') ?>">
                            <?= lang('Common.statistics') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('admin') ?>">
                            <?= lang('Common.for_staff') ?>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= base_url('privacy') ?>">
                            <?= lang('Common.privacy_policy') ?>
                        </a>
                    </li>

                    <!-- Language Switcher -->
                    <li class="nav-item dropdown ms-2">
                        <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="languageDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false" style="gap: 0.5rem;">
                            <?php
                            helper('language');
                            $currentLang = get_current_language();
                            if ($currentLang == 'th') {
                                echo '<span class="flag-icon" style="font-size: 1.2rem;">🇹🇭</span><span>ไทย</span>';
                            } else {
                                echo '<span class="flag-icon" style="font-size: 1.2rem;">🇬🇧</span><span>English</span>';
                            }
                            ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end language-dropdown shadow-lg border-0" aria-labelledby="languageDropdown">
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
                    <!--
                    <?php if (!empty(session()->get('user_id'))): ?>
                    <li class="nav-item ms-3">
                        <a href="<?= base_url('team/info') ?>" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-user-circle me-1"></i> <?= lang('Common.academy_dashboard') ?>
                        </a>
                    </li>
                    <?php else: ?>
                    <li class="nav-item ms-3">
                        <a href="<?= base_url('auth') ?>" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-sign-in-alt me-1"></i> <?= lang('Common.login') ?>
                        </a>
                    </li>
                    <?php endif; ?>
                    -->
                </ul>
            </div>
        </div>
    </nav>



    <?php $this->renderSection('content'); ?>

    <footer class="footer-section">
        <div class="container pb-5">
            <div class="row g-4">
                <!-- Brand & About -->
                <div class="col-lg-4 col-md-12 footer-brand">
                    <a href="<?= base_url() ?>" class="footer-brand-logo text-decoration-none">
                        JOOT<span>BALL</span>
                    </a>
                    <p class="footer-tagline">
                        แพลตฟอร์มบริหารจัดการข้อมูลสถิติฟุตบอลระดับมืออาชีพ ยกระดับศักยภาพนักเตะและทีมงานด้วยเทคโนโลยี AI และ Data Analytics
                    </p>
                    <div class="social-links mt-4">
                        <a href="https://www.facebook.com/JigsawInnovation" class="social-link" target="_blank"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="social-link"><i class="fab fa-line"></i></a>
                    </div>
                </div>

                <!-- Quick Links -->
                <div class="col-lg-2 col-md-4 col-6">
                    <h5 class="footer-heading">เมนูหลัก</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2"><a href="#home" class="footer-bottom-link text-decoration-none">หน้าแรก</a></li>
                        <li class="mb-2"><a href="#services" class="footer-bottom-link text-decoration-none">บริการของเรา</a></li>
                        <li class="mb-2"><a href="#works" class="footer-bottom-link text-decoration-none">ผลงาน</a></li>
                        <li class="mb-2"><a href="<?= base_url('admin') ?>" class="footer-bottom-link text-decoration-none"><?= lang('Common.for_staff') ?></a></li>
                    </ul>
                </div>

                <!-- Contact -->
                <div class="col-lg-3 col-md-8 col-12">
                    <h5 class="footer-heading">ติดต่อเรา</h5>
                    <div class="footer-contact-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>Jigsaw Innovation Co., Ltd.<br>Bangkok, Thailand</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-envelope"></i>
                        <span>jigsawinnovation@gmail.com</span>
                    </div>
                    <div class="footer-contact-item">
                        <i class="fas fa-phone"></i>
                        <span>086-322-3809</span>
                    </div>
                </div>

                <!-- Partners -->
                <div class="col-lg-3 col-md-12">
                    <h5 class="footer-heading">พันธมิตรของเรา</h5>
                    <div class="row g-3">
                        <div class="col-12">
                            <a href="#" class="text-decoration-none">
                                <div class="partner-card">
                                    <div class="partner-logo-box">
                                        <img src="<?= base_url('public/img/fa_logo.svg') ?>" height="35">
                                    </div>
                                    <div class="partner-info">
                                        <div class="title"><?= lang('Common.partner_with') ?></div>
                                        <div class="name"><?= lang('Common.fa_thailand_full') ?></div>
                                    </div>
                                </div>
                            </a>
                        </div>
                        <div class="col-12">
                            <a href="https://jigsawinnovation.com" target="_blank" class="text-decoration-none">
                                <div class="partner-card">
                                    <div class="partner-logo-box">
                                        <img src="<?= base_url('public/img/logo-white.png') ?>" height="35" style="filter: brightness(0) saturate(100%) invert(20%) sepia(85%) saturate(1915%) hue-rotate(218deg) brightness(91%) contrast(92%);">
                                    </div>
                                    <div class="partner-info">
                                        <div class="title"><?= lang('Common.developed_by') ?></div>
                                        <div class="name">JIGSAW INNOVATION</div>
                                    </div>
                                </div>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Copyright Bar -->
        <div class="copyright-bar">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-md-6 text-center text-md-start">
                        <p class="copyright-text mb-0">
                            © <?= date('Y') ?> Jigsaw Innovation. <?= lang('Common.all_rights_reserved') ?>
                        </p>
                    </div>
                    <div class="col-md-6">
                        <div class="footer-bottom-links">
                            <a href="<?= base_url('privacy') ?>" class="footer-bottom-link"><?= lang('Common.privacy_policy') ?></a>
                            <a href="#" class="footer-bottom-link"><?= lang('Common.terms_of_service') ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </footer>


    <!-- jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Bootstrap JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>

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

            // Navbar scroll effect
            $(window).scroll(function() {
                if ($(this).scrollTop() > 50) {
                    $('.navbar').addClass('scrolled');
                } else {
                    $('.navbar').removeClass('scrolled');
                }
            });

            // Add animation on scroll for service cards
            $(window).scroll(function() {
                $('.service-card').each(function(index) {
                    var elementTop = $(this).offset().top;
                    var elementBottom = elementTop + $(this).outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();

                    if (elementBottom > viewportTop && elementTop < viewportBottom) {
                        setTimeout(() => {
                            $(this).addClass('animate-in');
                        }, index * 200); // Stagger animation
                    }
                });
            });

            // Add staggered animation for work items
            $(window).scroll(function() {
                $('.work-item').each(function(index) {
                    var elementTop = $(this).offset().top;
                    var elementBottom = elementTop + $(this).outerHeight();
                    var viewportTop = $(window).scrollTop();
                    var viewportBottom = viewportTop + $(window).height();

                    if (elementBottom > viewportTop && elementTop < viewportBottom) {
                        setTimeout(() => {
                            $(this).addClass('animate-work');
                        }, index * 150);
                    }
                });
            });

            // Add floating animation to hero elements
            setInterval(function() {
                $('.hero-logo img').css('transform', 'translateY(' + Math.sin(Date.now() / 1000) * 10 + 'px)');
            }, 50);

            // Add continuous animation to service numbers
            $('.service-number').each(function(index) {
                $(this).css('animation-delay', (index * 0.2) + 's');
                $(this).addClass('number-pulse');
            });

            // Add mouse parallax effect
            $(document).mousemove(function(e) {
                var mouseX = e.pageX / window.innerWidth - 0.5;
                var mouseY = e.pageY / window.innerHeight - 0.5;

                $('.floating-circle').each(function(index) {
                    var speed = (index + 1) * 0.5;
                    $(this).css('transform',
                        'translate(' + (mouseX * speed * 20) + 'px, ' + (mouseY * speed * 20) + 'px)'
                    );
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
            $('.btn-primary-yellow').on('click', function(e) {
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

            .animate-work {
                animation: slideInWork 0.6s ease-out forwards;
            }

            @keyframes slideInWork {
                from {
                    opacity: 0;
                    transform: translateY(50px) scale(0.9);
                }
                to {
                    opacity: 1;
                    transform: translateY(0) scale(1);
                }
            }

            .number-pulse {
                animation: numberPulse 2s ease-in-out infinite;
            }

            @keyframes numberPulse {
                0%, 100% {
                    transform: scale(1);
                }
                50% {
                    transform: scale(1.05);
                }
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

            /* Additional floating animations */
            .hero-logo img {
                transition: transform 0.1s ease-out;
            }

            .navbar {
                animation: slideDown 0.5s ease-out;
            }

            @keyframes slideDown {
                from {
                    transform: translateY(-100%);
                }
                to {
                    transform: translateY(0);
                }
            }

            /* Continuous background animation */
            .services-section {
                position: relative;
            }

            .services-section::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: radial-gradient(circle at 20% 20%, rgba(30, 58, 138, 0.05) 0%, transparent 50%),
                           radial-gradient(circle at 80% 80%, rgba(220, 53, 69, 0.05) 0%, transparent 50%);
                animation: backgroundShift 10s ease-in-out infinite;
            }

            @keyframes backgroundShift {
                0%, 100% {
                    opacity: 0.3;
                }
                50% {
                    opacity: 0.7;
                }
            }

            /* Nav Tabs Styles */
            .nav-tabs {
                border-bottom: 2px solid var(--primary-blue);
            }

            .nav-tabs .nav-link {
                color: var(--primary-blue) !important;
                border: none !important;
                border-bottom: 3px solid transparent !important;
                font-weight: 600;
                transition: all 0.3s ease;
            }

            .nav-tabs .nav-link:hover {
                border-color: var(--primary-yellow) !important;
                color: var(--primary-black) !important;
            }

            .nav-tabs .nav-link.active {
                color: var(--primary-blue) !important;
                border-bottom: 3px solid var(--primary-yellow) !important;
                font-weight: 700;
            }

            /* Keep navbar-nav white only on desktop */
            @media (min-width: 992px) {
                .navbar-nav.ms-auto .nav-link {
                    color: #ffffff !important;
                }

                .navbar-nav.ms-auto .nav-link:hover {
                    color: var(--primary-yellow) !important;
                }
            }
        `).appendTo('head');

    </script>
    <a href="https://www.tiktok.com/@jootball8?_r=1&_t=ZS-95uPWPKB7RY" target="_blank" class="tiktok-floating-btn" title="สื่อผู้ช่วยสอน">
        <div class="tiktok-icon-wrapper">
            <i class="fa-brands fa-tiktok"></i>
        </div>
        <div class="tiktok-text-label">สื่อผู้ช่วยสอน</div>
    </a>

    <!-- ==================== ปุ่มกากบาทปิด FAB ==================== -->
    <style>
    /* FAB ทั้งสองปุ่มเป็น position:fixed อยู่แล้ว จึงเป็น containing block ให้ลูกที่ absolute ได้เลย */
    .fab-dismiss {
        position: absolute;
        top: -5px;
        right: -5px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #1a1a1a;
        color: #fff;
        border: 2px solid #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11px;
        line-height: 1;
        cursor: pointer;
        box-shadow: 0 2px 8px rgba(0, 0, 0, .35);
        /* .tiktok-icon-wrapper เป็น z-index:2 ในกล่องเดียวกัน ต้องสูงกว่า */
        z-index: 3;
        transition: transform .2s ease, background .2s ease;
    }
    .fab-dismiss:hover  { background: #d32f2f; transform: scale(1.15); }
    .fab-dismiss:active { transform: scale(.95); }
    .fab-dismiss:focus-visible { outline: 2px solid #fff; outline-offset: 2px; }

    @media (max-width: 576px) {
        /* นิ้วกดง่ายขึ้นบนจอเล็ก */
        .fab-dismiss { width: 26px; height: 26px; font-size: 12px; }
    }
    </style>

    <script>
    (function () {
        var FABS = [
            { sel: '.tiktok-floating-btn', key: 'jb_fab_tiktok_hidden' },
            { sel: '.help-guide-fab',      key: 'jb_fab_help_hidden'   }
        ];

        // localStorage ใช้ไม่ได้ในโหมดส่วนตัวของบางเบราว์เซอร์ จึงหุ้ม try ไว้
        var store = {
            hidden: function (k) { try { return localStorage.getItem(k) === '1'; } catch (e) { return false; } },
            hide:   function (k) { try { localStorage.setItem(k, '1'); } catch (e) {} }
        };

        FABS.forEach(function (fab) {
            var el = document.querySelector(fab.sel);
            if (!el) return;   // หน้านี้ไม่มีปุ่มนั้น

            if (store.hidden(fab.key)) {
                el.style.display = 'none';
                return;
            }

            // ใช้ <span> ไม่ใช่ <button> เพราะตัว FAB เองเป็น <a>/<button> อยู่แล้ว
            // ซ้อน interactive element ซ้อนกันเป็น HTML ที่ไม่ถูกต้อง
            var x = document.createElement('span');
            x.className = 'fab-dismiss';
            x.setAttribute('role', 'button');
            x.setAttribute('tabindex', '0');
            x.setAttribute('aria-label', '<?= lang('Common.close') ?>');
            x.setAttribute('title', '<?= lang('Common.close') ?>');
            x.innerHTML = '<i class="fas fa-times"></i>';

            function close(e) {
                // กันไม่ให้เปิดลิงก์ TikTok หรือเปิด offcanvas ของปุ่มช่วยเหลือ
                e.preventDefault();
                e.stopPropagation();
                el.style.display = 'none';
                store.hide(fab.key);
            }

            x.addEventListener('click', close);
            x.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') close(e);
            });

            el.appendChild(x);
        });
    })();
    </script>

    <?php $this->renderSection('scripts'); ?>
</body>
</html>
