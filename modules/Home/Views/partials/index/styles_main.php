<style>
/* Navbar Brand Logo Scale */
.navbar-brand img {
    transform: scale(1.4);
    transform-origin: center;
    transition: transform 0.3s ease;
}

.navbar-brand:hover img {
    transform: scale(1.5);
}

@media (max-width: 768px) {
    .navbar-brand img {
        transform: scale(1.2);
    }
}

@import url('https://fonts.googleapis.com/css2?family=Fira+Sans:wght@300;400;500;600;700;800;900&display=swap');

:root {
    /* Khelo Color Palette */
    --primary-blue: #214790;
    --primary-yellow: #FBC02D;
    --dark-blue: #0B1548;
    --light-blue: #3A5FD0;
    --white: #FFFFFF;
    --light-gray: #F5F5F5;
    --text-dark: #2C3E50;
    --text-light: #7F8C9F;
}

/* Hero Section */
.hero-khelo {
    position: relative;
    min-height: 100vh;
    display: flex;
    align-items: center;
    overflow: hidden;
    /* fallback: optimized JPG (older browsers) */
    background: linear-gradient(135deg, rgba(33,71,144,0.5) 0%, rgba(11,21,72,0.6) 50%, rgba(58,95,208,0.5) 100%),
                url('<?php echo base_url('public/img/bg_home-opt.jpg')?>');
    /* progressive: lightweight jpg where supported */
    background-image: linear-gradient(135deg, rgba(33,71,144,0.5) 0%, rgba(11,21,72,0.6) 50%, rgba(58,95,208,0.5) 100%),
                -webkit-image-set(url('<?php echo base_url('public/img/bg_home.jpg')?>') type('image/jpg'), url('<?php echo base_url('public/img/bg_home-opt.jpg')?>') type('image/jpeg'));
    background-image: linear-gradient(135deg, rgba(33,71,144,0.5) 0%, rgba(11,21,72,0.6) 50%, rgba(58,95,208,0.5) 100%),
                image-set(url('<?php echo base_url('public/img/bg_home.jpg')?>') type('image/jpg'), url('<?php echo base_url('public/img/bg_home-opt.jpg')?>') type('image/jpeg'));
    background-size: cover;
    background-position: center center;
    background-repeat: no-repeat;
    background-attachment: fixed;
    font-family: 'Kanit', sans-serif;
    padding: 120px 0 80px;
}

/* Background Elements */
.hero-bg-wrapper {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    z-index: 0;
}

.hero-bg-overlay {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, rgba(11,21,72,0.3) 0%, rgba(33,71,144,0.25) 50%, rgba(11,21,72,0.35) 100%);
}

.hero-pattern {
    position: absolute;
    width: 100%;
    height: 100%;
    overflow: hidden;
}

.pattern-circle {
    position: absolute;
    border-radius: 50%;
    opacity: 0.04;
}

.pattern-circle.c1 {
    width: 400px;
    height: 400px;
    background: var(--primary-blue);
    top: -100px;
    right: -100px;
    animation: patternFloat 20s ease-in-out infinite;
}

.pattern-circle.c2 {
    width: 300px;
    height: 300px;
    background: var(--primary-yellow);
    bottom: -80px;
    left: -80px;
    animation: patternFloat 25s ease-in-out infinite reverse;
}

.pattern-circle.c3 {
    width: 200px;
    height: 200px;
    background: var(--light-blue);
    top: 50%;
    left: 30%;
    animation: patternFloat 18s ease-in-out infinite;
}

.hero-shape {
    position: absolute;
    width: 300px;
    height: 300px;
    opacity: 0.05;
}

.shape-left {
    top: 20%;
    left: -150px;
    background: linear-gradient(135deg, var(--primary-blue), var(--light-blue));
    clip-path: polygon(50% 0%, 100% 50%, 50% 100%, 0% 50%);
    animation: rotate360 30s linear infinite;
}

.shape-right {
    bottom: 10%;
    right: -100px;
    background: linear-gradient(135deg, var(--primary-yellow), #FFD54F);
    border-radius: 50%;
    animation: pulse 4s ease-in-out infinite;
}

/* Hero Row */
.hero-row {
    position: relative;
    z-index: 2;
    min-height: 70vh;
}

/* Hero Content Wrapper */
.hero-content-wrapper {
    padding: 20px 0;
}

/* Hero Badge */
.hero-badge {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    background: linear-gradient(135deg, var(--primary-blue), var(--light-blue));
    color: var(--white);
    padding: 10px 20px;
    border-radius: 50px;
    font-size: 0.85rem;
    font-weight: 600;
    margin-bottom: 25px;
    box-shadow: 0 4px 15px rgba(33,71,144,0.2);
    transition: all 0.3s ease;
}

.hero-badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(33,71,144,0.3);
}

.badge-icon {
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary-yellow);
}

.badge-text {
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Main Title */
.hero-main-title {
    font-size: 3.8rem;
    font-weight: 800;
    line-height: 1.2;
    color: #FFFFFF;
    margin-bottom: 25px;
    font-family: 'Kanit', sans-serif;
    text-shadow: 0 4px 20px rgba(0,0,0,0.6), 0 2px 10px rgba(0,0,0,0.4);
}

.title-highlight {
    color: var(--primary-yellow);
    position: relative;
    display: inline-block;
}

.title-highlight::after {
    content: '';
    position: absolute;
    bottom: 5px;
    left: 0;
    width: 100%;
    height: 15px;
    background: var(--primary-yellow);
    opacity: 0.3;
    z-index: -1;
}

/* Description */
.hero-description {
    font-size: 1.1rem;
    line-height: 1.8;
    color: rgba(255,255,255,0.95);
    margin-bottom: 35px;
    max-width: 550px;
    font-weight: 400;
    text-shadow: 0 2px 8px rgba(0,0,0,0.5), 0 1px 3px rgba(0,0,0,0.3);
}

/* CTA Buttons */
.hero-cta-group {
    display: flex;
    flex-direction: column;
    gap: 14px;
    margin-bottom: 40px;
    width: 100%;
    max-width: 380px;
}

.hero-cta-group .cta-btn {
    width: 100%;
    flex: 0 0 auto;
    min-width: 0;
}

.cta-btn {
    display: inline-flex;
    align-items: center;
    justify-content: flex-start;
    gap: 14px;
    padding: clamp(14px, 1.6vw, 18px) clamp(18px, 2vw, 26px);
    border-radius: 12px;
    font-size: clamp(0.95rem, 1.3vw, 1.12rem);
    font-weight: 700;
    text-decoration: none;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    position: relative;
    overflow: hidden;
    font-family: 'Kanit', sans-serif;
    min-height: 62px;
    text-align: left;
    white-space: nowrap;
}

.cta-btn::before {
    content: '';
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255,255,255,0.3), transparent);
    transition: left 0.5s ease;
}

.cta-btn:hover::before {
    left: 100%;
}

.cta-primary {
    background: linear-gradient(135deg, var(--primary-blue) 0%, var(--light-blue) 100%);
    color: var(--white);
    box-shadow: 0 8px 20px rgba(33,71,144,0.25);
}

.cta-primary:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 30px rgba(33,71,144,0.35);
    color: var(--white);
}

.cta-secondary {
    background: var(--white);
    color: var(--primary-blue);
    border: 2px solid var(--primary-blue);
    box-shadow: 0 4px 15px rgba(33,71,144,0.1);
}

.cta-secondary:hover {
    background: var(--primary-blue);
    color: var(--white);
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(33,71,144,0.25);
}

.btn-logo-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    background: rgba(255,255,255,0.15);
    border-radius: 10px;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.btn-logo-icon img {
    width: 28px;
    height: 28px;
    filter: brightness(0) invert(1);
}

.cta-primary:hover .btn-logo-icon {
    background: rgba(255,255,255,0.25);
    transform: scale(1.05);
}

.btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    transition: transform 0.3s ease;
    font-size: 1.2rem;
    flex-shrink: 0;
}

.cta-btn:hover .btn-icon {
    transform: translateX(5px);
}

/* Stats Inline */
.hero-stats-inline {
    display: flex;
    align-items: center;
    gap: 30px;
    flex-wrap: wrap;
}

.stat-inline-item {
    text-align: left;
}

.stat-inline-number {
    font-size: 4.2rem;
    font-weight: 800;
    color: var(--primary-yellow);
    line-height: 1;
    margin-bottom: 8px;
    font-family: ds_digital;
    text-shadow: 0 3px 12px rgba(251,192,45,0.6), 0 2px 8px rgba(0,0,0,0.4);
}

.stat-inline-label {
    font-size: 0.95rem;
    color: rgba(255,255,255,0.95);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
    text-shadow: 0 2px 6px rgba(0,0,0,0.5);
}

.stat-divider {
    width: 2px;
    height: 40px;
    background: linear-gradient(to bottom, transparent, var(--primary-yellow), transparent);
}

.stat-divider-col {
    border-right: 1px dashed rgba(0, 243, 255, 0.2);
}

@media (max-width: 767.98px) {
    .stat-divider-col {
        border-right: none;
        border-bottom: 1px dashed rgba(0, 243, 255, 0.2);
        padding-bottom: 1.5rem;
        margin-bottom: 1rem !important;
    }

    .cyber-summary-box {
        padding: 1rem !important;
    }

    .age-item {
        flex: 1 1 calc(33.333% - 1rem);
        min-width: 60px;
    }
}

/* Hero Visual (Right Side) */
.hero-visual {
    position: relative;
    padding: 40px;
}

.hero-main-image {
    position: relative;
    z-index: 2;
    display: flex;
    flex-direction: row; /* Revert to horizontal */
    align-items: center;
    justify-content: flex-start;
    gap: 30px;
}

/* Tablet & Small Desktop (iPad Landscape etc.) */
@media (max-width: 1200px) {
    .hero-main-image {
        gap: 15px;
    }
    .main-logo-img {
        max-width: 200px; /* Shrink logo to fit */
    }
    .hero-cta-group {
        max-width: 320px; /* Narrower CTA group */
    }
    .hero-cta-group .cta-btn {
        min-width: 100%; /* Stack buttons inside group to save width */
    }
}

@media (max-width: 992px) {
    .hero-main-image {
        flex-direction: column;
        align-items: center;
        text-align: center;
    }
}

.image-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(251,192,45,0.15) 0%, transparent 70%);
    animation: pulseGlow 3s ease-in-out infinite;
    z-index: 0;
}

.main-logo-img {
    position: relative;
    z-index: 1;
    max-width: 320px;
    height: auto;
    filter: drop-shadow(0 15px 35px rgba(33,71,144,0.2));
    animation: floatHero 6s ease-in-out infinite;
}

/* Floating Cards */
.floating-card {
    position: absolute;
    background: var(--white);
    border-radius: 16px;
    padding: 18px 22px;
    box-shadow: 0 10px 40px rgba(33,71,144,0.15);
    display: flex;
    align-items: center;
    gap: 15px;
    z-index: 3;
    animation: cardFloat 4s ease-in-out infinite;
    transition: all 0.3s ease;
}

.floating-card:hover {
    transform: translateY(-8px) scale(1.05);
    box-shadow: 0 15px 50px rgba(33,71,144,0.25);
}

.floating-card .card-icon {
    width: 60px;
    height: 60px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.8rem;
    flex-shrink: 0;
}

.card-1 {
    top: 15%;
    left: -10%;
    animation-delay: 0s;
}

.card-1 .card-icon {
    background: linear-gradient(135deg, #4A90E2, #5FA3EF);
    color: var(--white);
}

.card-2 {
    top: 50%;
    right: -8%;
    animation-delay: 1s;
}

.card-2 .card-icon {
    background: linear-gradient(135deg, var(--primary-yellow), #FFD54F);
    color: var(--dark-blue);
}

.card-3 {
    bottom: -10%;
    left: 5%;
    animation-delay: 2s;
}

.card-3 .card-icon {
    background: linear-gradient(135deg, #28a745, #34ce57);
    color: var(--white);
}

.floating-card .card-info {
    text-align: left;
}

.floating-card .card-number {
    font-size: 2.2rem;
    font-weight: 800;
    color: var(--dark-blue);
    line-height: 1;
    margin-bottom: 5px;
    font-family: ds_digital;
}

.floating-card .card-label {
    font-size: 0.85rem;
    color: var(--text-light);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.8px;
}

/* Decorative Circles */
.deco-circle {
    position: absolute;
    border-radius: 50%;
    z-index: 0;
}

.deco-1 {
    width: 150px;
    height: 150px;
    background: linear-gradient(135deg, rgba(33,71,144,0.1), rgba(58,95,208,0.1));
    top: 10%;
    right: 15%;
    animation: rotate360 20s linear infinite;
}

.deco-2 {
    width: 100px;
    height: 100px;
    background: linear-gradient(135deg, rgba(251,192,45,0.15), rgba(255,213,79,0.1));
    bottom: 20%;
    right: 10%;
    animation: rotate360 15s linear infinite reverse;
}

.deco-3 {
    width: 80px;
    height: 80px;
    background: linear-gradient(135deg, rgba(40,167,69,0.1), rgba(52,206,87,0.1));
    top: 40%;
    left: 10%;
    animation: pulse 3s ease-in-out infinite;
}

/* Scroll Down Indicator */
.scroll-down-indicator {
    position: absolute;
    bottom: 40px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 10px;
    cursor: pointer;
    z-index: 10;
}

.scroll-line {
    width: 2px;
    height: 50px;
    background: linear-gradient(to bottom, var(--primary-yellow), transparent);
    animation: scrollLineMove 2s ease-in-out infinite;
}

.scroll-text {
    font-size: 0.75rem;
    color: var(--text-light);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1.5px;
    writing-mode: vertical-rl;
    transform: rotate(180deg);
}

.scroll-down-indicator:hover .scroll-line {
    background: linear-gradient(to bottom, var(--primary-blue), transparent);
}

.stat-card {
    background: rgba(255,255,255,0.08);
    backdrop-filter: blur(20px);
    border-radius: 16px;
    padding: 1.2rem;
    border: 1px solid rgba(255,255,255,0.15);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    position: relative;
    overflow: hidden;
    height: 100%;
}

.stat-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 3px;
    background: linear-gradient(90deg, transparent, #F3B81A, transparent);
    opacity: 0;
    transition: opacity 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-8px) scale(1.03);
    background: rgba(255,255,255,0.12);
    border-color: rgba(243,184,26,0.4);
    box-shadow:
        0 20px 40px rgba(0,0,0,0.3),
        0 0 20px rgba(243,184,26,0.2);
}

.stat-card:hover::before {
    opacity: 1;
}

.stat-icon-wrapper {
    width: 45px;
    height: 45px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 0.8rem;
    transition: all 0.3s ease;
}

.stat-teams .stat-icon-wrapper {
    background: rgba(30,144,255,0.2);
}

.stat-teams .stat-icon-wrapper i {
    color: #1E90FF;
    font-size: 1.4rem;
}

.stat-players .stat-icon-wrapper {
    background: rgba(40,167,69,0.2);
}

.stat-players .stat-icon-wrapper i {
    color: #28a745;
    font-size: 1.4rem;
}

.stat-staffs .stat-icon-wrapper {
    background: rgba(23,162,184,0.2);
}

.stat-staffs .stat-icon-wrapper i {
    color: #17a2b8;
    font-size: 1.4rem;
}

.stat-tournaments .stat-icon-wrapper {
    background: rgba(253,126,20,0.2);
}

.stat-tournaments .stat-icon-wrapper i {
    color: #fd7e14;
    font-size: 1.4rem;
}

.stat-card:hover .stat-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.stat-content {
    text-align: left;
}

.stat-label {
    font-size: clamp(0.8rem, 2vw, 0.9rem);
    color: rgba(255,255,255,0.7);
    font-weight: 500;
    margin: 0;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Action Buttons */
.hero-actions {
    margin-top: 2rem;
}

.action-btn {
    text-decoration: none;
    display: block;
    width: 100%;
    position: relative;
    border-radius: 18px;
    overflow: hidden;
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}

.btn-background {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    transition: all 0.4s ease;
    z-index: 0;
}

.btn-primary-action .btn-background {
    background: linear-gradient(135deg, #1E3EA2 0%, #2B4CB8 50%, #3A5FD0 100%);
}

.btn-secondary-action .btn-background {
    background: linear-gradient(135deg, #F3B81A 0%, #E0A800 50%, #FFD700 100%);
}

.btn-content-wrapper {
    position: relative;
    z-index: 1;
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px 24px;
    min-height: 95px;
}

.btn-icon-box {
    width: 55px;
    height: 55px;
    border-radius: 14px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    transition: all 0.3s ease;
}

.btn-primary-action .btn-icon-box {
    background: rgba(255,255,255,0.15);
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
}

.btn-primary-action .btn-icon-box img {
    width: 38px;
    height: 38px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

.btn-secondary-action .btn-icon-box {
    background: rgba(11,21,72,0.15);
}

.btn-secondary-action .btn-icon-box i {
    font-size: 1.8rem;
    color: #0B1548;
}

.btn-text-box {
    flex: 1;
    text-align: left;
}

.btn-main-text {
    display: block;
    font-size: 1.1rem;
    font-weight: 700;
    line-height: 1.3;
    margin-bottom: 4px;
}

.btn-primary-action .btn-main-text {
    color: #ffffff;
}

.btn-secondary-action .btn-main-text {
    color: #0B1548;
}

.btn-sub-text {
    display: block;
    font-size: 0.85rem;
    font-weight: 600;
    line-height: 1.2;
}

.btn-primary-action .btn-sub-text {
    color: #F3B81A;
}

.btn-secondary-action .btn-sub-text {
    color: rgba(11,21,72,0.7);
}

.btn-arrow-icon {
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
    flex-shrink: 0;
}

.btn-arrow-icon i {
    font-size: 1.2rem;
}

.btn-primary-action .btn-arrow-icon i {
    color: #F3B81A;
}

.btn-secondary-action .btn-arrow-icon i {
    color: #0B1548;
}

/* Button Hover Effects */
.action-btn:hover {
    transform: translateY(-6px);
    box-shadow: 0 15px 35px rgba(0,0,0,0.4);
}

.btn-primary-action:hover .btn-background {
    background: linear-gradient(135deg, #2B4CB8 0%, #3A5FD0 50%, #4A6FE8 100%);
}

.btn-secondary-action:hover .btn-background {
    background: linear-gradient(135deg, #FFD700 0%, #F3B81A 50%, #E0A800 100%);
}

.action-btn:hover .btn-icon-box {
    transform: scale(1.08) rotate(-3deg);
}

.action-btn:hover .btn-arrow-icon {
    opacity: 1;
    transform: translateX(0);
}

/* Hero Logo */
.hero-logo-container {
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 2rem;
}

.logo-glow {
    position: absolute;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 350px;
    height: 350px;
    background: radial-gradient(circle, rgba(243,184,26,0.2) 0%, transparent 70%);
    border-radius: 50%;
    animation: pulseGlow 3s ease-in-out infinite;
}

.hero-logo-img {
    position: relative;
    z-index: 2;
    max-width: 280px;
    height: auto;
    filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));
    animation: floatLogo 4s ease-in-out infinite;
    transition: transform 0.3s ease;
}

.hero-logo-img:hover {
    transform: scale(1.05);
}

/* Scroll Indicator */
.scroll-indicator {
    position: absolute;
    bottom: 30px;
    left: 50%;
    transform: translateX(-50%);
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
    z-index: 10;
    cursor: pointer;
    transition: all 0.3s ease;
}

.scroll-text {
    font-size: 0.8rem;
    color: rgba(255,255,255,0.6);
    text-transform: uppercase;
    letter-spacing: 2px;
    font-weight: 600;
}

.scroll-arrow {
    width: 30px;
    height: 30px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    backdrop-filter: blur(10px);
    display: flex;
    align-items: center;
    justify-content: center;
    animation: scrollBounce 2s ease-in-out infinite;
}

.scroll-arrow i {
    color: #F3B81A;
    font-size: 1rem;
}

.scroll-indicator:hover {
    transform: translateX(-50%) translateY(-5px);
}

.scroll-indicator:hover .scroll-arrow {
    background: rgba(243,184,26,0.2);
}

/* ============================================
   ANIMATIONS - KHELO STYLE
   ============================================ */

@keyframes patternFloat {
    0%, 100% {
        transform: translateY(0) translateX(0) rotate(0deg);
    }
    50% {
        transform: translateY(-30px) translateX(20px) rotate(180deg);
    }
}

@keyframes rotate360 {
    from {
        transform: rotate(0deg);
    }
    to {
        transform: rotate(360deg);
    }
}

@keyframes pulse {
    0%, 100% {
        opacity: 0.5;
        transform: scale(1);
    }
    50% {
        opacity: 0.8;
        transform: scale(1.1);
    }
}

@keyframes pulseGlow {
    0%, 100% {
        opacity: 0.3;
        transform: translate(-50%, -50%) scale(1);
    }
    50% {
        opacity: 0.6;
        transform: translate(-50%, -50%) scale(1.15);
    }
}

@keyframes floatHero {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-20px);
    }
}

@keyframes cardFloat {
    0%, 100% {
        transform: translateY(0);
    }
    50% {
        transform: translateY(-15px);
    }
}

@keyframes scrollLineMove {
    0% {
        height: 0;
    }
    50% {
        height: 50px;
    }
    100% {
        height: 0;
    }
}

/* ============================================
   OLD BUTTON STYLES (TO BE REMOVED)
   ============================================ */

.btn-academy {
    padding: 20px 30px;
    font-size: 1.1rem;
    font-weight: 700;
    border-radius: 20px;
    border: none;
    background: linear-gradient(135deg, #1E3EA2 0%, #2B4CB8 50%, #3A5FD0 100%);
    color: white;
    box-shadow: 0 10px 30px rgba(30,62,162,0.4);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    text-transform: none;
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 110px;
    width: 100%;
    position: relative;
    overflow: hidden;
}

.btn-glow {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
    opacity: 0;
    transition: all 0.3s ease;
    border-radius: 20px;
}

.btn-icon-wrapper {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 55px;
    height: 55px;
    background: rgba(255,255,255,0.15);
    border-radius: 15px;
    flex-shrink: 0;
    backdrop-filter: blur(10px);
    border: 1px solid rgba(255,255,255,0.2);
    position: relative;
    z-index: 2;
}

.btn-icon-wrapper img {
    width: 40px;
    height: 40px;
    filter: drop-shadow(0 2px 4px rgba(0,0,0,0.2));
}

.btn-text {
    text-align: left;
    line-height: 1.2;
    position: relative;
    z-index: 2;
    flex: 1 1 auto;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.btn-title {
    font-size: 1.05rem;
    margin-bottom: 5px;
    opacity: 0.98;
    font-weight: 700;
}

.btn-subtitle {
    font-size: 0.85rem;
    font-weight: 600;
    color: #F3B81A;
    text-shadow: 0 1px 2px rgba(0,0,0,0.2);
}

.btn-arrow {
    margin-left: auto;
    opacity: 0;
    transform: translateX(-10px);
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
}

.btn-arrow i {
    font-size: 1.2rem;
    color: #F3B81A;
}

.btn-login {
    padding: 20px 30px;
    font-size: 1.1rem;
    font-weight: 700;
    border-radius: 20px;
    border: none;
    background: linear-gradient(135deg, #F3B81A 0%, #E0A800 50%, #CC9900 100%);
    color: #0B1548;
    box-shadow: 0 10px 30px rgba(243,184,26,0.4);
    transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    text-transform: none;
    position: relative;
    overflow: hidden;
    min-height: 110px;
    width: 100%;
    display: flex;
    align-items: center;
    gap: 12px;
}

.btn-glow-login {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: linear-gradient(45deg, rgba(255,255,255,0.3) 0%, rgba(255,255,255,0) 100%);
    opacity: 0;
    transition: all 0.3s ease;
    border-radius: 20px;
}

.btn-login .btn-icon {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 55px;
    height: 55px;
    background: rgba(11,21,72,0.15);
    border-radius: 15px;
    flex-shrink: 0;
    position: relative;
    z-index: 2;
}

.btn-login .btn-icon i {
    font-size: 1.8rem;
    color: #0B1548;
}

.btn-text-login {
    text-align: left;
    line-height: 1.2;
    position: relative;
    z-index: 2;
    flex: 1;
}

.btn-title-login {
    font-size: 1.05rem;
    margin-bottom: 5px;
    font-weight: 700;
    color: #0B1548;
}

.btn-subtitle-login {
    font-size: 0.85rem;
    font-weight: 600;
    color: rgba(11,21,72,0.7);
}

.btn-arrow-login {
    opacity: 0;
    transform: translateX(-5px);
    transition: all 0.3s ease;
    position: relative;
    z-index: 2;
    flex-shrink: 0;
}

.btn-arrow-login i {
    font-size: 1.2rem;
    color: #0B1548;
}

/* Hover Effects */
.btn-academy-link:hover .btn-academy {
    transform: translateY(-8px) scale(1.03);
    box-shadow: 0 20px 50px rgba(30,62,162,0.6);
    background: linear-gradient(135deg, #2B4CB8 0%, #3A5FD0 50%, #4A6FE8 100%);
}

.btn-academy-link:hover .btn-glow {
    opacity: 1;
}

.btn-academy-link:hover .btn-arrow {
    opacity: 1;
    transform: translateX(5px);
}

.btn-login-link:hover .btn-login {
    transform: translateY(-6px) scale(1.06);
    box-shadow: 0 15px 40px rgba(243,184,26,0.7);
    background: linear-gradient(135deg, #FFD700 0%, #F3B81A 50%, #E0A800 100%);
}

.btn-login-link:hover .btn-glow-login {
    opacity: 1;
}

.btn-login-link:hover .btn-arrow-login {
    opacity: 1;
    transform: translateX(3px);
}

.hero-logo-img {
    transition: transform 0.3s ease;
}

.hero-logo-img:hover {
    transform: scale(1.05);
}

/* Feature Section Styles */
/* Whois Modern Section */
.whois-modern {
    background-color: #ffffff;
    position: relative;
    overflow: hidden;
}

.modern-title {
    font-size: 2.8rem;
    font-weight: 900;
    color: #0B1548;
    letter-spacing: -1px;
    margin-bottom: 0.5rem;
}

.modern-divider {
    width: 80px;
    height: 5px;
    background: linear-gradient(90deg, #214790, #FBC02D);
    border-radius: 10px;
}

.modern-description {
    font-size: 1.15rem;
    max-width: 700px;
    margin: 0 auto;
}

.modern-card {
    background: #ffffff;
    border: 1px solid rgba(33, 71, 144, 0.08);
    border-radius: 30px;
    padding: 40px 30px;
    transition: all 0.5s cubic-bezier(0.23, 1, 0.32, 1);
    position: relative;
    overflow: hidden;
    display: flex;
    flex-direction: column;
    align-items: center;
    text-align: center;
    box-shadow: 0 15px 35px rgba(33, 71, 144, 0.05);
    z-index: 1;
}

.modern-card:hover {
    transform: translateY(-15px);
    box-shadow: 0 30px 60px rgba(33, 71, 144, 0.12);
    border-color: var(--accent-color);
}

.card-icon {
    width: 90px;
    height: 90px;
    border-radius: 25px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 38px;
    margin-bottom: 30px;
    background: var(--bg-soft);
    color: var(--accent-color);
    transition: all 0.5s ease;
    box-shadow: inset 0 2px 10px rgba(255, 255, 255, 0.5);
}

.modern-card:hover .card-icon {
    background: var(--accent-color);
    color: #ffffff;
    transform: rotate(-8deg) scale(1.15);
}

.card-title {
    font-weight: 800;
    font-size: 1.4rem;
    color: #0B1548;
    margin-bottom: 15px;
}

.card-text {
    color: #64748b;
    line-height: 1.6;
}

/* Card Themes */
.card-blue {
    --accent-color: #214790;
    --bg-soft: rgba(33, 71, 144, 0.08);
}
.card-green {
    --accent-color: #10b981;
    --bg-soft: rgba(16, 185, 129, 0.08);
}
.card-orange {
    --accent-color: #f59e0b;
    --bg-soft: rgba(245, 158, 11, 0.08);
}
.card-purple {
    --accent-color: #8b5cf6;
    --bg-soft: rgba(139, 92, 246, 0.08);
}

.card-glow {
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, var(--bg-soft) 0%, transparent 70%);
    opacity: 0;
    transition: opacity 0.4s ease;
    pointer-events: none;
    z-index: -1;
}

.modern-card:hover .card-glow {
    opacity: 1;
}

/* Decorative Backgrounds */
.modern-deco {
    position: absolute;
    border-radius: 50%;
    filter: blur(80px);
    z-index: 0;
    opacity: 0.4;
}

.deco-1 {
    width: 300px;
    height: 300px;
    background: rgba(33, 71, 144, 0.1);
    top: -100px;
    left: -100px;
}

.deco-2 {
    width: 250px;
    height: 250px;
    background: rgba(255, 214, 0, 0.1);
    bottom: -50px;
    right: -50px;
}

@media (max-width: 768px) {
    .modern-title { font-size: 2rem; }
    .modern-card { padding: 25px 15px; }
    .card-icon { width: 60px; height: 60px; font-size: 24px; }
}

.feature-section {
    background:
        linear-gradient(115deg, rgba(243,184,26,0.14) 0 18%, transparent 18% 100%),
        radial-gradient(circle at 88% 16%, rgba(22,71,140,0.12), transparent 30%),
        linear-gradient(180deg, #ffffff 0%, #edf4fb 100%);
    padding: 80px 0;
    position: relative;
    overflow: hidden;
}

@media(max-width:576px){
    .feature-section {
        padding: 50px 0;
    }
}

/* Services Section Enhanced */
.services-section {
    background: #f8f9ff;
    padding: 100px 0;
    position: relative;
    overflow: hidden;
}

.floating-footballs {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.football {
    position: absolute;
    opacity: 0.15;
}

.football-1 {
    top: 10%;
    left: 5%;
    animation: floatFootball 8s ease-in-out infinite;
}

.football-2 {
    top: 70%;
    right: 8%;
    animation: floatFootball 10s ease-in-out infinite reverse;
}

.football-3 {
    top: 40%;
    left: 80%;
    animation: floatFootball 12s ease-in-out infinite;
}

.football i {
    font-size: 2rem;
    color: #F3B81A;
}

.section-description {
    font-size: 1.2rem;
    color: rgba(11,21,72,0.8);
    max-width: 700px;
    margin: 0 auto;
}

.service-card {
    background: #ffffff;
    border-radius: 20px;
    padding: 2.5rem;
    color: #0B1548;
    position: relative;
    overflow: hidden;
    box-shadow: 0 15px 35px rgba(11,21,72,0.3);
    transition: all 0.4s ease;
    border: 2px solid rgba(243,184,26,0.2);
    height: 100%;
}

.service-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(243,184,26,0.05);
    opacity: 0;
    transition: all 0.4s ease;
    transform: scale(0.8);
}

.service-content {
    position: relative;
    z-index: 2;
    height: 100%;
    display: flex;
    flex-direction: column;
}

.service-number {
    font-size: 4rem;
    font-weight: 900;
    color: rgba(11,21,72,0.8);
    position: absolute;
    top: -10px;
    right: 20px;
    transition: all 0.3s ease;
}

.service-icon {
    margin-bottom: 1.5rem;
    transform: translateY(0);
    transition: all 0.3s ease;
}

.service-icon-circle {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 10px 25px rgba(11,21,72,0.3);
}

.service-icon-yellow {
    background: #F3B81A;
}

.service-icon-yellow i {
    font-size: 2rem;
    color: #0B1548;
}

.service-icon-blue {
    background: #0B1548;
}

.service-icon-blue i {
    font-size: 2rem;
    color: #F3B81A;
}

.service-title {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 1rem;
    color: #0B1548;
    transition: all 0.3s ease;
}

.service-description {
    font-size: 1rem;
    line-height: 1.6;
    color: rgba(11,21,72,0.8);
    transition: all 0.3s ease;
    flex-grow: 1;
}

.service-arrow {
    margin-top: 1.5rem;
    opacity: 0;
    transform: translateX(-20px);
    transition: all 0.3s ease;
}

.service-arrow i {
    color: #F3B81A;
    font-size: 1.2rem;
}

.service-card:hover {
    transform: translateY(-10px) scale(1.02);
    box-shadow: 0 25px 50px rgba(11,21,72,0.15);
    border-color: #F3B81A;
}

.service-card:hover .service-overlay {
    opacity: 1;
    transform: scale(1);
}

.service-card:hover .service-arrow {
    opacity: 1;
    transform: translateX(8px);
}

.service-card:hover .service-icon {
    transform: translateY(-5px);
}

/* Recent Work Section Enhanced */
.recent-work {
    background: #f8fbff;
    padding: 120px 0;
    position: relative;
    overflow: hidden;
}

.decorative-elements {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: 1;
}

.decoration {
    position: absolute;
    border-radius: 50%;
    animation: floatSlow 12s ease-in-out infinite;
}

.decoration-1 {
    top: 10%;
    left: -5%;
    width: 200px;
    height: 200px;
    background: rgba(243,184,26,0.03);
}

.decoration-2 {
    top: 70%;
    right: -5%;
    width: 150px;
    height: 150px;
    background: rgba(11,21,72,0.03);
    animation: floatSlow 10s ease-in-out infinite reverse;
}

.decoration-3 {
    top: 40%;
    left: 50%;
    width: 100px;
    height: 100px;
    background: rgba(243,184,26,0.02);
    animation: floatSlow 15s ease-in-out infinite;
}

.work-badge {
    display: inline-block;
    background: rgba(243,184,26,0.1);
    padding: 8px 24px;
    border-radius: 50px;
    margin-bottom: 1rem;
}

.work-badge span {
    color: #0B1548;
    font-size: 0.9rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.work-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
    gap: 2.5rem;
    margin-top: 4rem;
    padding: 0 1rem;
}

.work-item {
    background: #ffffff;
    border-radius: 24px;
    padding: 3rem 2rem;
    text-align: center;
    transition: all 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    box-shadow: 0 8px 25px rgba(11,21,72,0.08);
    border: 1px solid rgba(243,184,26,0.15);
    position: relative;
}

.work-decoration {
    position: absolute;
    top: -10px;
    right: -10px;
    width: 40px;
    height: 40px;
    background: rgba(243,184,26,0.1);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.work-decoration-dot {
    width: 20px;
    height: 20px;
    background: #F3B81A;
    border-radius: 50%;
}

.work-decoration-dot-blue {
    background: #0B1548;
}

.work-image {
    margin-bottom: 2rem;
    position: relative;
}

.icon-wrapper {
    width: 90px;
    height: 90px;
    border-radius: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin: 0 auto;
    box-shadow: 0 12px 24px rgba(11,21,72,0.15);
    transition: all 0.4s ease;
    position: relative;
}

.icon-wrapper-blue {
    background: #0B1548;
}

.icon-wrapper-blue i {
    font-size: 2.8rem;
    color: #F3B81A;
}

.icon-wrapper-yellow {
    background: #F3B81A;
}

.icon-wrapper-yellow i {
    font-size: 2.8rem;
    color: #0B1548;
}

.icon-glow {
    position: absolute;
    inset: -3px;
    background: rgba(243,184,26,0.2);
    border-radius: 23px;
    opacity: 0;
    transition: all 0.3s ease;
}

.work-content {
    position: relative;
    z-index: 2;
}

.work-title {
    font-size: 1.5rem;
    font-weight: 700;
    color: #0B1548;
    margin-bottom: 0.8rem;
    line-height: 1.3;
}

.work-category {
    color: rgba(11,21,72,0.6);
    font-weight: 500;
    font-size: 0.9rem;
    text-transform: uppercase;
    letter-spacing: 2px;
    margin-bottom: 1rem;
}

.work-divider {
    width: 60px;
    height: 3px;
    background: #F3B81A;
    margin: 0 auto;
    border-radius: 2px;
}

.work-item:hover {
    transform: translateY(-15px) scale(1.02);
    box-shadow: 0 20px 40px rgba(11,21,72,0.12);
    border-color: #F3B81A;
}

.work-item:hover .icon-wrapper {
    transform: scale(1.1) rotate(-5deg);
    box-shadow: 0 16px 32px rgba(11,21,72,0.2);
}

.work-item:hover .icon-glow {
    opacity: 1;
}

/* Animations */
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

@keyframes floatFootball {
    0%, 100% {
        transform: translateY(0px) rotate(0deg);
    }
    25% {
        transform: translateY(-20px) rotate(90deg);
    }
    50% {
        transform: translateY(-10px) rotate(180deg);
    }
    75% {
        transform: translateY(-30px) rotate(270deg);
    }
}

@keyframes floatSlow {
    0%, 100% {
        transform: translateY(0px) translateX(0px);
    }
    33% {
        transform: translateY(-10px) translateX(5px);
    }
    66% {
        transform: translateY(5px) translateX(-5px);
    }
}

/* ============================================
   RESPONSIVE DESIGN - KHELO STYLE
   ============================================ */

/* Tablet (≤ 992px) */
@media (max-width: 992px) {
    .hero-khelo {
        padding: 100px 0 60px;
    }

    .hero-main-title {
        font-size: 3.2rem;
    }

    .hero-description {
        font-size: 1rem;
        max-width: 100%;
    }

    .hero-visual {
        padding: 30px;
        margin-top: 40px;
    }

    .main-logo-img {
        max-width: 280px;
    }

    .floating-card {
        padding: 15px 18px;
    }

    .floating-card .card-icon {
        width: 45px;
        height: 45px;
        font-size: 1.3rem;
    }

    .floating-card .card-number {
        font-size: 1.5rem;
    }
}

/* Mobile (≤ 768px) */
@media (max-width: 768px) {
    .hero-khelo {
        padding: 80px 0 50px;
        min-height: auto;
    }

    .hero-row {
        flex-direction: column;
    }

    /* Content Section แสดงก่อน (ด้านบน) */
    .hero-content-wrapper {
        order: 1;
        text-align: center;
        padding: 0;
        margin-bottom: 40px;
        display: flex;
        flex-direction: column;
    }

    /* Logo Section แสดงหลัง (ด้านล่าง) */
    .hero-visual {
        order: 2;
        padding: 30px 15px;
        margin-top: 0;
        margin-bottom: 0;
    }

    .hero-badge {
        margin-left: auto;
        margin-right: auto;
        order: 1;
    }

    .hero-main-title {
        font-size: 2.5rem;
        margin-bottom: 20px;
        order: 2;
    }

    .hero-description {
        font-size: 0.95rem;
        margin-left: auto;
        margin-right: auto;
        margin-bottom: 30px;
        order: 3;
    }

    .hero-stats-inline {
        display: flex;
        order: 4;
        justify-content: center;
        gap: 30px;
        margin-bottom: 30px;
    }

    .hero-cta-group {
        justify-content: center;
        gap: 12px;
        margin-bottom: 35px;
        order: 5;
    }

    .cta-btn {
        padding: 16px 30px;
        font-size: 1rem;
        width: 100%;
        max-width: 350px;
    }

    .btn-logo-icon {
        width: 38px;
        height: 38px;
    }

    .btn-logo-icon img {
        width: 26px;
        height: 26px;
    }

    /* Logo และ Cards เรียงแนวตั้ง */
    .hero-main-image {
        flex-direction: column;
        align-items: center;
        gap: 15px;
    }

    .main-logo-img {
        max-width: 200px;
        order: 1;
        margin-bottom: 25px;
    }

    .image-glow {
        width: 280px;
        height: 280px;
    }

    /* Floating Cards เป็น Static และกว้างขึ้น */
    .floating-card {
        position: static !important;
        width: 100%;
        max-width: 100%;
        margin: 0 auto 12px auto;
        padding: 18px 20px;
        animation: none !important;
        transform: none !important;
        box-shadow: 0 8px 25px rgba(33,71,144,0.2);
    }

    .card-1 {
        order: 2;
    }

    .card-2 {
        order: 3;
    }

    .card-3 {
        order: 4;
    }

    .floating-card .card-icon {
        width: 55px;
        height: 55px;
        font-size: 1.5rem;
    }

    .floating-card .card-number {
        font-size: 2rem;
    }

    .floating-card .card-label {
        font-size: 0.8rem;
    }

    .scroll-down-indicator {
        display: none;
    }

    .scroll-line {
        height: 40px;
    }

    .deco-circle {
        display: none;
    }
}

/* Small Mobile (≤ 576px) */
@media (max-width: 576px) {
    .hero-khelo {
        padding: 70px 0 40px;
    }

    .hero-row {
        flex-direction: column;
    }

    /* Content Section แสดงก่อน (ด้านบน) */
    .hero-content-wrapper {
        order: 1;
        text-align: center;
        padding: 0;
        margin-bottom: 35px;
    }

    /* Logo Section แสดงหลัง (ด้านล่าง) */
    .hero-visual {
        order: 2;
        padding: 20px 10px;
        margin-top: 0;
        margin-bottom: 0;
    }

    .hero-badge {
        font-size: 0.8rem;
        padding: 8px 16px;
    }

    .hero-main-title {
        font-size: 2rem;
    }

    .title-highlight::after {
        height: 10px;
    }

    .hero-description {
        font-size: 0.9rem;
    }

    .cta-btn {
        width: 100%;
        justify-content: center;
        padding: 14px 28px;
        font-size: 0.95rem;
        max-width: 100%;
    }

    .btn-logo-icon {
        width: 36px;
        height: 36px;
    }

    .btn-logo-icon img {
        width: 24px;
        height: 24px;
    }

    .btn-icon {
        font-size: 1rem;
    }

    .hero-stats-inline {
        display: none;
    }

    /* Logo และ Cards เรียงแนวตั้ง */
    .hero-main-image {
        flex-direction: column;
        align-items: center;
        gap: 12px;
    }

    .main-logo-img {
        max-width: 180px;
        order: 1;
        margin-bottom: 20px;
    }

    .image-glow {
        width: 250px;
        height: 250px;
    }

    /* Floating Cards กว้างเต็มที่ */
    .floating-card {
        position: static !important;
        width: 100%;
        max-width: 100%;
        margin: 0 auto 10px auto;
        padding: 16px 18px;
        animation: none !important;
        transform: none !important;
        box-shadow: 0 6px 20px rgba(33,71,144,0.2);
    }

    /* เรียงลำดับ: Logo -> ทีม -> ทัวร์นาเมนต์ -> นักเตะ */
    .main-logo-img {
        order: 1;
    }

    .card-1 {
        order: 2;
    }

    .card-2 {
        order: 3;
    }

    .card-3 {
        order: 4;
    }

    .floating-card .card-icon {
        width: 52px;
        height: 52px;
        font-size: 1.4rem;
    }

    .floating-card .card-number {
        font-size: 1.9rem;
    }

    .floating-card .card-label {
        font-size: 0.8rem;
    }

    .pattern-circle,
    .hero-shape,
    .deco-circle {
        display: none;
    }

    .scroll-down-indicator {
        display: none;
    }
}

/* Old styles - to be removed */
@media (max-width: 768px) {
    .hero-title-home {
        font-size: 2.2rem;
        line-height: 1.2;
    }

    .hero-subtitle-home {
        font-size: 1.5rem;
    }

    .hero-logo-img {
        height: 150px !important;
        max-width: 150px;
    }

    .container-stat {
        padding-top: 50px !important;
    }

    .feature-title {
        font-size: 1.8rem;
    }

    .section-title {
        font-size: 2rem;
    }

    .btn-academy {
        min-height: 85px !important;
        padding: 15px 20px !important;
        font-size: 1.05rem !important;
        gap: 8px !important;
        border-radius: 18px !important;
    }

    .btn-icon-wrapper {
        width: 50px !important;
        height: 50px !important;
        border-radius: 15px !important;
    }

    .btn-icon-wrapper img {
        height: 40px !important;
    }

    .btn-title {
        font-size: 1rem !important;
        line-height: 1.2 !important;
    }

    .btn-subtitle {
        font-size: 0.95rem !important;
    }

    .btn-academy,
    .btn-login {
        min-height: 90px !important;
        padding: 15px 18px !important;
        font-size: 0.9rem !important;
        gap: 8px !important;
    }

    .btn-icon-wrapper,
    .btn-login .btn-icon {
        width: 45px !important;
        height: 45px !important;
        border-radius: 10px !important;
    }

    .btn-icon-wrapper img {
        width: 30px !important;
        height: 30px !important;
    }

    .btn-login .btn-icon i {
        font-size: 1.3rem !important;
    }

    .btn-title,
    .btn-title-login {
        font-size: 0.85rem !important;
        line-height: 1.3 !important;
    }

    .btn-subtitle,
    .btn-subtitle-login {
        font-size: 0.7rem !important;
    }

    .btn-arrow,
    .btn-arrow-login {
        display: none !important;
    }
}

/* Performance optimizations for mobile */
@media (max-width: 768px) {
    .btn-academy-link:hover .btn-academy,
    .btn-login-link:hover .btn-login {
        animation: none !important;
        transform: translateY(-3px) scale(1.01) !important;
    }

    .btn-academy:active,
    .btn-login:active {
        transform: scale(0.98) !important;
        transition: all 0.1s ease !important;
    }
}

/* Reduced motion support */
@media (prefers-reduced-motion: reduce) {
    .hero-content,
    .floating-footballs,
    .decorative-elements {
        animation: none !important;
    }

    .btn-academy-link:hover .btn-academy {
        transform: translateY(-2px) scale(1.01);
    }

    .btn-login-link:hover .btn-login {
        transform: translateY(-1px) scale(1.02);
    }
}

/*
รายงานผล
*/
.day-header{font-size:1.2rem;font-weight:700;margin:20px 0 10px;padding:8px 12px;background:#f1f3f5;border-radius:6px;}
.match-cell{border:1px solid #e5e5e5;padding:12px;}
.match-cell.left{border-right:0;}
.group{font-weight:700;margin-bottom:6px;}
.match-cell .team{display:flex;justify-content:space-between;align-items:center;margin:4px 0; font-size: 14px;}
.team .team-left{display:flex;align-items:center;gap:8px;}
.team .team-logo,.team-info .team-logo{width:28px;height:28px;object-fit:contain;background:#eee;}
.score{font-weight:700;min-width:30px;text-align:center;}
.match-info{text-align:right;}
.match-btn{font-size:.75rem;background:#0d6efd;color:#fff;padding:2px 6px;border-radius:4px;display:inline-block;margin-top:4px}

#bracketWrapper{position:relative;overflow-x:auto;padding-bottom:20px;}
#bracketLines{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;}
.bracket-col{min-width:260px;}
.bracket-title{font-weight:700;text-align:center;margin-bottom:12px;}
.bracket-match{border:1px solid #e5e5e5;border-radius:8px;padding:10px;margin-bottom:16px;background:#fff;}
.bracket-team{display:flex;justify-content:space-between;align-items:center;margin:4px 0;}
.bracket-left{display:flex;align-items:center;gap:8px; font-size: 14px;}
.bracket-logo{width:26px;height:26px;object-fit:contain;background:#eee;}
.bracket-team.is-winner {
  color: #198754;
  font-weight: 700;
}
.bracket-team.is-winner img {
  border: 1.5px solid #198754;
}

.players-grid{
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 16px;
}

.player-card{
  display: flex;
  flex-direction: column;
}

.player-card{
  background: #fff;
  border-radius: 10px;
  padding: 12px;
  text-align: center;
  box-shadow: 0 2px 6px rgba(0,0,0,.08);
}

.player-photo{
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
  background: #f1f1f1;
  border-radius: 8px;
}

.player-name{
  font-weight: 400;
  margin-top: 8px;
  font-size: 13px;
}

.player-position{
  font-size: 13px;
  color: #666;
}

.team-info {
  font-size: 13px
}


.group-title{font-weight:700;margin:5px 0 8px;font-size:15px;}
.ranking-table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;}
.ranking-table thead th{background:#f8f9fa;font-weight:600;font-size:13px;padding:10px;border-bottom:1px solid #dee2e6;text-align:center;}
.ranking-table tbody td{padding:10px;font-size:13px;border-bottom:1px solid #eee;text-align:center;}
.rank-team{display:flex;align-items:center;gap:8px;text-align:left;}
.rank-team img{width:20px;height:20px;object-fit:contain;}
.rank-pos{font-weight:600;}
.match-btn {background-color:#303236}
/* ===== highlight อันดับ 1–2 (ไม่ทำให้ตารางล้น) ===== */
#rankingContainer {
  padding: 10px;
}
.ranking-table tbody tr.top-rank td:first-child{
  position: relative;
  padding-left: 16px; /* เผื่อที่ให้แถบ */
}

.ranking-table tbody tr.top-rank td:first-child::before{
  content:'';
  position:absolute;
  left:4px;
  top:50%;
  transform:translateY(-50%);
  width:6px;
  height:70%;
  /*background:#0d6efd;*/
  border-radius:4px;
}
.team-name {
  font-size: 12px;
}

.result-section {
    background: #FFF !important;
    padding: 30px 80px;
    position: relative;
    overflow: hidden;
}

.nav-item .nav-link{
    color: #1e3a8a !important;
}
.nav-item .nav-link.active {
    color: #f3b81a !important;
    font-weight: bold !important;
}

/* TourList */
.tournament-item {
  height: 125px;
  margin: 2px;
  background: #fff;
  border-radius: 12px;
  padding: 10px 8px;
  text-align: center;
  cursor: pointer;
  box-shadow: 0 2px 8px rgba(0,0,0,.08);
  transition: all .2s;
}
.tournament-item img {
    background: #eee;
}

.tournament-item:hover {
  transform: translateY(-4px);
}

.tournament-logo {
  width: 48px;
  height: 48px;
  object-fit: contain;
  margin-bottom: 6px;
}

.tournament-name {
  font-size: 12px;
  font-weight: 600;
  line-height: 1.2;
}

.tournament-gen {
  font-size: 11px;
  color: #777;
}

/* ===== LineUP MVP ===== */
/* ===== FIELD ===== */
/* ===== REAL GRASS FIELD ===== */
#footballField{
    position:relative;
    width:100%;
    aspect-ratio: 2 / 3;
    border-radius:16px;
    overflow:hidden;
    border:4px solid #ffffff;
    box-shadow:
        inset 0 0 60px rgba(0,0,0,.45),
        0 25px 45px rgba(0,0,0,.25);

    /* หญ้าแบบ realistic */
    background:
        /* เงาแสงตกกระทบ */
        radial-gradient(circle at 30% 20%, rgba(255,255,255,.15), transparent 40%),
        radial-gradient(circle at 70% 80%, rgba(0,0,0,.15), transparent 50%),

        /* ลายตัดหญ้า */
        repeating-linear-gradient(
            0deg,
            #3ea043 0px,
            #3ea043 40px,
            #2f8f37 40px,
            #2f8f37 80px
        ),

        /* ฐานสีหญ้า */
        linear-gradient(
            180deg,
            #46b04d 0%,
            #2f8f37 50%,
            #26752d 100%
        );
}

/* เส้นกลาง */
#footballField::before{
    content:"";
    position:absolute;
    top:50%;
    left:0;
    width:100%;
    height:3px;
    background:#fff;
    transform:translateY(-50%);
}

/* วงกลมกลาง */
#footballField::after{
    content:"";
    position:absolute;
    top:50%;
    left:50%;
    width:30%;
    aspect-ratio:1/1;
    border:3px solid #fff;
    border-radius:50%;
    transform:translate(-50%,-50%);
}
/* เขตโทษ */
.penalty-top,.penalty-bottom{
  position:absolute;left:50%;
  width:240px;height:120px;
  border:2px solid #fff;
  transform:translateX(-50%);
}
.penalty-top{top:0}
.penalty-bottom{bottom:0}

/* ===== ZONES ===== */
.zone{position:absolute;width:100%;display:flex;justify-content:center;gap:5px;min-height:100px;z-index:2;flex-wrap: wrap;align-content: flex-start;}
.zone.fw{top:60px}
.zone.mf{top:200px}
.zone.df{top:400px}
.zone.gk{bottom:50px}
.manager-zone{margin-top:12px;display:flex;justify-content:center}
.zone.manager{min-height:60px}

/* ===== PLAYER ===== */
.player{
  scale: 0.7;
  background:#fff;border-radius:20px;
  padding:6px 12px 6px 6px;
  font-size:11px;min-width:85px;
  box-shadow:0 2px 5px rgba(0,0,0,.25);
  cursor:move;position:relative;
  display:flex;align-items:center;gap:4px;
}
.player img.photo{width:32px;height:32px;border-radius:50%;object-fit:cover}
.player img.logo{
  width:18px;height:18px;
  position:absolute;left:4px;bottom:-8px;
  background:#fff;border-radius:50%
}
.player .pos{font-size:10px;font-weight:bold;color:#198754}
.player .remove{
  position:absolute;right:6px;top:2px;
  font-size:12px;color:#dc3545;cursor:pointer
}
.player.active{outline:2px solid #0d6efd}
.zone.manager .player{background:#fff3cd;border:1px solid #ffe69c}

/* ===== MOCKUP PLAYER STYLES ===== */
.mockup-player{
  background:#f8f9fa;border:1px dashed #dee2e6;
  opacity:0.7;cursor:default;
  scale: 0.85;
}
.mockup-player:hover{
  background:#e9ecef;opacity:1;
}
.mockup-avatar{
  width:40px;height:40px;border-radius:50%;
  background:#6c757d;color:#fff;
  display:flex;align-items:center;justify-content:center;
  font-size:18px;
}

/* ===== PLAYER INFO ===== */
#playerInfo{
  background:linear-gradient(135deg,#ffffff,#f1f3f5);
  border-radius:16px;padding:16px;min-height:150px;
  box-shadow:0 6px 14px rgba(0,0,0,.15);
}
.player-info-card{display:flex;gap:16px;align-items:center}
.player-info-card img.photo{
  width:96px;height:96px;border-radius:50%;
  border:3px solid #0d6efd;object-fit:cover
}
.player-info-card .name{font-size:1.2rem;font-weight:700}
.player-info-card .team{display:flex;align-items:center;gap:6px;font-size:.95rem}
.player-info-card .meta{font-size:.9rem;color:#555}
.empty-info{text-align:center;color:#999;padding:30px 0}

/* ===== END LineUP MVP ===== */

/* END รายงานผล */

/* เพิ่มเติม header หน้าแรก*/

.stats-surface {
    background: #ffffff36;
    padding: 36px 20px;
    box-shadow: 0 12px 40px rgba(0,0,0,.08);
}

/* ช่องแต่ละสถิติ */
.stat-item1 {
    padding: 10px 6px;
    text-align: center;
}

.stat-label {
    font-size: clamp(0.9rem, 1.2vw, 1.15rem);
    font-weight: 400;
    color: #fff;
    margin-bottom: 4px;
    white-space: nowrap;
}


/* ตัวเลข */
.stat-number {
/* เริ่มต้นที่ 1.2rem บนมือถือ และขยายได้ถึง 2.5rem บนจอใหญ่ */
    font-size: clamp(1.2rem, 5vw, 2rem);
    line-height: 1.2;
    overflow-wrap: break-word; /* ป้องกันเลขยาวเกินแล้วทะลุ Card */
    white-space: nowrap;      /* ถ้าไม่อยากให้เลขขึ้นบรรทัดใหม่ */
}

/* Summary Modern Styles */
.sum-card-modern {
    background: #ffffff;
    border-radius: 20px;
    padding: 1.5rem;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
    text-align: center;
    border: 1px solid rgba(0, 0, 0, 0.05);
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.05), 0 8px 10px -6px rgba(0, 0, 0, 0.05);
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    height: 100%;
    position: relative;
    overflow: hidden;
}

.sum-card-modern:hover {
    transform: translateY(-8px);
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -6px rgba(0, 0, 0, 0.04);
    border-color: rgba(33, 71, 144, 0.2);
}

.sum-card-modern::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 4px;
    background: transparent;
    transition: background 0.3s ease;
}

.sum-card-modern:hover::before {
    background: linear-gradient(90deg, #214790, #FBC02D);
}

.sum-icon-wrapper {
    width: 60px;
    height: 60px;
    border-radius: 16px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 1.25rem;
    font-size: 1.5rem;
    transition: all 0.3s ease;
}

.sum-card-modern:hover .sum-icon-wrapper {
    transform: scale(1.1) rotate(5deg);
}

.sum-label {
    font-size: 0.9rem;
    font-weight: 700;
    color: #64748b;
    margin-bottom: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    font-family: 'Kanit', sans-serif;
}

.sum-number {
    font-size: 2.25rem;
    font-weight: 800;
    color: #0B1548;
    line-height: 1;
    font-family: 'Kanit', sans-serif;
}

/* Color Variants */
.bg-blue-soft { background-color: #eff6ff; color: #214790; }
.bg-gold-soft { background-color: #fffbeb; color: #d97706; }
.bg-green-soft { background-color: #f0fdf4; color: #16a34a; }
.bg-purple-soft { background-color: #faf5ff; color: #7c3aed; }
.bg-red-soft { background-color: #fef2f2; color: #dc2626; }
.bg-indigo-soft { background-color: #eef2ff; color: #4f46e5; }

/* เส้นแบ่งแบบ Material */
@media (min-width: 768px) {
    .stat-item1 {
        border-right: 1px solid rgba(0,0,0,.08);
    }
    .row > div:nth-child(4n) .stat-item1 {
        border-right: none;
    }
}
@media (max-width: 1190px) {
    .stat-number {
        font-size: 1.2rem;
    }
}

@media (max-width: 1366px) {

    .result-section {
        padding: 0px;
    }
}

.tournament-swiper {
    position: relative;
    padding-bottom: 40px;
}

.tournament-swiper .swiper-pagination {
    bottom: 0 !important;
}

/* ============================================
   HOME STATS CARDS
   ============================================ */

.cyber-bg {
    background-color: transparent !important;
    padding: 0.25rem 0 1rem;
    box-shadow: none !important;
}

.cyber-stat-card {
    --stat-accent: #f3b81a;
    --stat-accent-2: #20e37c;
    --stat-accent-soft: rgba(243, 184, 26, 0.25);
    --stat-accent-border: rgba(243, 184, 26, 0.6);
    position: relative;
    min-height: 145px;
    width: 100%;
    margin: 0 auto;
    padding: 20px 10px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
    overflow: hidden;
    isolation: isolate;
    background:
        linear-gradient(145deg, rgba(255,255,255,0.12), rgba(255,255,255,0.02) 40%, rgba(0,0,0,0.3)),
        radial-gradient(circle at 50% 0%, var(--stat-accent-soft), transparent 60%),
        linear-gradient(155deg, #0a192f 0%, #0d2137 50%, #050c1a 100%) !important;
    border: 1px solid rgba(255,255,255,0.15) !important;
    border-radius: 16px !important;
    backdrop-filter: blur(12px);
    box-shadow:
        0 15px 35px rgba(0, 0, 0, 0.4),
        inset 0 1px 1px rgba(255,255,255,0.1);
    transition: all 0.4s cubic-bezier(0.165, 0.84, 0.44, 1);
}

.cyber-stat-card::before {
    content: "";
    position: absolute;
    top: 0;
    left: 50%;
    transform: translateX(-50%);
    width: 60%;
    height: 3px;
    background: linear-gradient(90deg, transparent, var(--stat-accent), var(--stat-accent-2), transparent);
    border-radius: 0 0 10px 10px;
    opacity: 0.8;
    z-index: 1;
}

.cyber-stat-card::after {
    content: "";
    position: absolute;
    width: 100px;
    height: 100px;
    right: -30px;
    bottom: -30px;
    background: radial-gradient(circle, var(--stat-accent-soft) 0%, transparent 70%);
    opacity: 0.4;
    z-index: -1;
    filter: blur(20px);
}

.cyber-stat-card:hover {
    transform: translateY(-8px);
    border-color: var(--stat-accent-border) !important;
    box-shadow:
        0 25px 50px rgba(0, 0, 0, 0.5),
        0 0 20px var(--stat-accent-soft);
}

.cyber-icon {
    width: 48px;
    height: 48px;
    display: flex;
    align-items: center;
    justify-content: center;
    margin-bottom: 12px;
    border-radius: 14px;
    color: #07121f;
    font-size: 1.25rem;
    background: linear-gradient(135deg, #ffffff 0%, var(--stat-accent) 40%, var(--stat-accent-2) 100%);
    border: 1px solid rgba(255,255,255,0.8);
    box-shadow: 0 8px 16px rgba(0,0,0,0.3);
    transition: transform 0.3s ease;
    z-index: 2;
}

.cyber-stat-card:hover .cyber-icon {
    transform: scale(1.1) rotate(5deg);
}

.cyber-stat-card ,
.cyber-stat-card .stat-number.neon-gold {
    font-family: 'Kanit', sans-serif !important;
    color: #ffffff !important;
    z-index: 2;
}

.cyber-stat-card .stat-number.neon-gold {
    order: 2;
    font-size: clamp(1.8rem, 2.8vw, 2.5rem);
    font-weight: 800;
    line-height: 1;
    color: #ffffff !important;
    text-shadow: 0 0 15px var(--stat-accent-soft);
    margin: 5px 0;
}

.cyber-stat-card .stat-label {
    order: 3;
    font-size: 0.75rem !important;
    color: rgba(255,255,255,0.7) !important;
    font-weight: 500 !important;
    letter-spacing: 0.5px;
    text-transform: uppercase;
}

.row.text-center.gy-3.mb-4 > .col-6:nth-child(1) .cyber-stat-card {
    --stat-accent: #f3b81a;
    --stat-accent-2: #20e37c;
    --stat-accent-soft: rgba(243, 184, 26, 0.25);
}

.row.text-center.gy-3.mb-4 > .col-6:nth-child(2) .cyber-stat-card {
    --stat-accent: #35c5ff;
    --stat-accent-2: #20e37c;
    --stat-accent-soft: rgba(53, 197, 255, 0.25);
}

.row.text-center.gy-3.mb-4 > .col-6:nth-child(3) .cyber-stat-card {
    --stat-accent: #ff9f2f;
    --stat-accent-2: #f3b81a;
    --stat-accent-soft: rgba(255, 159, 47, 0.25);
}

.row.text-center.gy-3.mb-4 > .col-6:nth-child(4) .cyber-stat-card {
    --stat-accent: #c084fc;
    --stat-accent-2: #35c5ff;
    --stat-accent-soft: rgba(192, 132, 252, 0.25);
}

.cyber-stat-card.highlight-card {
    border-width: 1.5px !important;
    border-color: rgba(243, 184, 26, 0.4) !important;
    animation: cyberPulse 3s infinite ease-in-out;
}

@keyframes cyberPulse {
    0%, 100% { box-shadow: 0 15px 35px rgba(0, 0, 0, 0.4), inset 0 1px 1px rgba(255,255,255,0.1); }
    50% { box-shadow: 0 15px 45px rgba(243, 184, 26, 0.2), inset 0 1px 1px rgba(255,255,255,0.2); }
}

.cyber-summary-box {
    background: rgba(10, 25, 47, 0.7) !important;
    border: 1px solid rgba(0, 243, 255, 0.3) !important;
    border-radius: 20px !important;
    backdrop-filter: blur(12px);
    box-shadow: 0 0 30px rgba(0, 243, 255, 0.1), inset 0 0 20px rgba(0, 243, 255, 0.05) !important;
    position: relative;
    overflow: hidden;
    padding: 1.5rem !important;
}

.summary-header {
    color: #00f3ff;
    font-size: 1rem;
    font-weight: 700;
    letter-spacing: 2px;
    text-transform: uppercase;
    text-shadow: 0 0 15px rgba(0, 243, 255, 0.5);
    margin-bottom: 1.5rem !important;
}

.gender-container {
    background: rgba(255, 255, 255, 0.03);
    border-radius: 15px;
    padding: 1rem;
    border: 1px solid rgba(255, 255, 255, 0.05);
}

.gender-item {
    transition: all 0.3s ease;
}

.gender-label-m {
    color: #00f3ff;
    font-size: 0.85rem;
    font-weight: 600;
    text-shadow: 0 0 10px rgba(0, 243, 255, 0.4);
}

.gender-label-w {
    color: #ff66cc;
    font-size: 0.85rem;
    font-weight: 600;
    text-shadow: 0 0 10px rgba(255, 102, 204, 0.4);
}

.gender-bar-wrapper {
    height: 6px;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 10px;
    margin: 10px 0;
    overflow: hidden;
    width: 100%;
    display: flex;
}

.gender-bar-m { background: #00f3ff; box-shadow: 0 0 10px rgba(0, 243, 255, 0.5); }
.gender-bar-w { background: #ff66cc; box-shadow: 0 0 10px rgba(255, 102, 204, 0.5); }

.age-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 12px;
}

.age-item {
    background: rgba(255, 255, 255, 0.03);
    border: 1px solid rgba(255, 255, 255, 0.05);
    padding: 10px 5px;
    border-radius: 12px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
}

.age-item:hover {
    background: rgba(0, 243, 255, 0.08);
    border-color: rgba(0, 243, 255, 0.3);
    transform: translateY(-3px);
    box-shadow: 0 5px 15px rgba(0, 243, 255, 0.1);
}

.age-label {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    margin-bottom: 4px;
}

.stat-value {
    color: #ffffff;
    font-size: 1.2rem;
    font-weight: 800;
    font-family: 'Kanit', sans-serif;
    text-shadow: 0 2px 4px rgba(0,0,0,0.3);
}

.neon-gold {
    color: #ffcc00 !important;
    text-shadow: 0 0 15px rgba(255, 204, 0, 0.5);
}

@media (max-width: 575.98px) {
    .cyber-stat-card {
        min-height: 135px;
        padding: 15px 8px;
    }

    .cyber-icon {
        width: 40px;
        height: 40px;
        font-size: 1.1rem;
        margin-bottom: 8px;
    }

    .cyber-stat-card .stat-number.neon-gold {
        font-size: 1.6rem;
    }
}
/* END เพิ่มเติม header หน้าแรก*/
</style>
