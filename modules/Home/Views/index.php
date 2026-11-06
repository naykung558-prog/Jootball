<?php $this->extend('template/home') ?>

<?php $this->section('content'); ?>

    <!-- Hero Section - Khelo Style -->
    <section id="home" class="hero-section hero-khelo">
        <!-- Hero Background with Pattern -->
        <div class="hero-bg-wrapper">
            <div class="hero-bg-overlay"></div>
            <div class="hero-pattern">
                <div class="pattern-circle c1"></div>
                <div class="pattern-circle c2"></div>
                <div class="pattern-circle c3"></div>
            </div>
            <div class="hero-shape shape-left"></div>
            <div class="hero-shape shape-right"></div>
        </div>

        <div class="container">
            <div class="row align-items-center hero-row">
                <!-- Left Content -->
                <div class="col-lg-6 col-md-12">
                    <div class="hero-content-wrapper text-center text-lg-start">
                        <!-- Badge -->
                        <div class="hero-badge mx-auto mx-lg-0" data-aos="fade-up">
                            <span class="badge-icon"><i class="fas fa-futbol"></i></span>
                            <span class="badge-text"><?= lang('Common.thai_youth_football') ?></span>
                        </div>

                        <!-- Main Title -->
                        <h1 class="hero-main-title" data-aos="fade-up" data-aos-delay="100">
                            <?= lang('Common.welcome_to') ?>
                            <span class="title-highlight">Jootball</span>
                        </h1>

                        <!-- Description -->
                        <p class="hero-description mx-auto mx-lg-0" data-aos="fade-up" data-aos-delay="200">
                            <?= lang('Common.hero_description') ?>
                        </p>

                        <div class="hero-main-image justify-content-center justify-content-lg-start" >
                            <div class="hero-cta-group justify-content-center justify-content-lg-start mx-auto mx-lg-0" data-aos="fade-up" data-aos-delay="300" style="z-index:9999;">
                                <?php if (!empty(session()->get('user_id'))) { ?>
                                    <a href="<?php echo getUserTeamAcademyUrl() ?: base_url('team/info'); ?>" class="cta-btn cta-primary">
                                        <span class="btn-logo-icon">
                                            <img src="<?php echo base_url('public/img/fa_logo.svg')?>" alt="FA Logo">
                                        </span>
                                        <span class="btn-text"><?= lang('Common.academy_dashboard') ?></span>
                                        <span class="btn-icon"><i class="fas fa-tachometer-alt"></i></span>
                                    </a>
                                <?php } else { ?>
                                    <a href="<?php echo base_url('auth');?>" class="cta-btn cta-primary">
                                        <span class="btn-logo-icon">
                                            <img src="<?php echo base_url('public/img/fa_logo.svg')?>" alt="FA Logo">
                                        </span>
                                        <span class="btn-text"><?= lang('Common.login_to_academy') ?></span>
                                        <span class="btn-icon"><i class="fas fa-arrow-right"></i></span>
                                    </a>
                                <?php } ?>
                                <?php if (!empty(session()->get('coach_id'))) { ?>
                                    <a href="<?php echo base_url('coach/dashboard');?>" class="cta-btn cta-secondary">
                                        <span class="btn-text"><?= lang('Common.coach_dashboard') ?></span>
                                        <span class="btn-icon"><i class="fas fa-tachometer-alt"></i></span>
                                    </a>
                                <?php } else { ?>
                                    <a href="<?php echo base_url('coach');?>" class="cta-btn cta-secondary">
                                        <span class="btn-text"><?= lang('Common.login_as_coach') ?></span>
                                        <span class="btn-icon"><i class="fas fa-user"></i></span>
                                    </a>
                                <?php } ?>
                            </div>

                            <div class="image-glow" style="z-index:0"></div>
                            <img src="<?php echo base_url('public/img/jootball_logo.png')?>" alt="Jootball" class="main-logo-img">


                        </div>
                    </div>
                </div>
<div class="col-lg-6 col-md-12">
        <div class="stats-surface cyber-bg">

            <div class="row text-center gy-3 mb-4">
                <div class="col-6 col-md-3" style="padding:3px">
                    <div class="cyber-stat-card highlight-card">
                        <div class="cyber-icon"><i class="fas fa-running"></i></div>
                        <div class="stat-label"><?= lang('Common.players') ?></div>
                        <div class="stat-number neon-gold stat-large"><?= number_format($players) ?></div>
                    </div>
                </div>

                <div class="col-6 col-md-3" style="padding:3px">
                    <div class="cyber-stat-card">
                        <div class="cyber-icon"><i class="fas fa-clipboard-list"></i></div>
                        <div class="stat-label"><?= lang('Common.coaches') ?></div>
                        <div class="stat-number neon-gold text-white"><?= number_format($staffs) ?></div>
                    </div>
                </div>

                <div class="col-6 col-md-3" style="padding:3px">
                    <div class="cyber-stat-card">
                        <div class="cyber-icon"><i class="fas fa-trophy"></i></div>
                        <div class="stat-label"><?= lang('Common.tournaments') ?></div>
                        <div class="stat-number neon-gold text-white"><?= number_format($tournaments) ?></div>
                    </div>
                </div>

                <div class="col-6 col-md-3" style="padding:3px">
                    <div class="cyber-stat-card">
                        <div class="cyber-icon"><i class="fas fa-users"></i></div>
                        <div class="stat-label"><?= lang('Common.registered_teams') ?></div>
                        <div class="stat-number neon-gold text-white"><?= number_format($teams) ?></div>
                    </div>
                </div>
            </div>

            <div class="row text-center">
                <div class="col-12">
                    <div class="p-3 cyber-summary-box">

                        <div class="mb-3 summary-header text-center">
                            <i class="fas fa-chart-bar me-2"></i> <?= lang('Common.header_timeline') ?>
                        </div>

                        <div class="row align-items-center g-3">
                        <?php
                        $total = $players;
                        $percent = 3;

                        // คํานวณ 3% แล้วปัดเป็นจำนวนเต็ม
                        $players_female = round(($total * $percent) / 100);

                        // นำยอดรวมมาลบผู้หญิง จะได้ยอดผู้ชายเป็นจำนวนเต็มพอดีเป๊ะ
                        $players_male = $total - $players_female;
                        ?>
                            <div class="col-12 col-md-5 mb-3 mb-md-0 stat-divider-col">
                                <div class="gender-container h-100 d-flex flex-column justify-content-center">
                                    <div class="d-flex justify-content-between align-items-center mb-2 px-1">
                                        <div class="gender-item">
                                            <div class="gender-label-m"><i class="fas fa-mars me-1"></i><?= lang('Common.sex_m') ?></div>
                                            <div class="stat-value"><?= number_format($players_male ?? 0) ?></div>
                                        </div>
                                        <div class="gender-item text-end">
                                            <div class="gender-label-w"><i class="fas fa-venus me-1"></i><?= lang('Common.sex_w') ?></div>
                                            <div class="stat-value"><?= number_format($players_female ?? 0) ?></div>
                                        </div>
                                    </div>

                                    <?php
                                        $m_width = ($total > 0) ? ($players_male / $total) * 100 : 50;
                                        $w_width = 100 - $m_width;
                                    ?>
                                    <div class="gender-bar-wrapper">
                                        <div class="gender-bar-m" style="width: <?= $m_width ?>%"></div>
                                        <div class="gender-bar-w" style="width: <?= $w_width ?>%"></div>
                                    </div>
                                    <div class="d-flex justify-content-between px-1" style="font-size: 0.65rem; color: rgba(255,255,255,0.4);">
                                        <span><?= round($m_width) ?>%</span>
                                        <span><?= round($w_width) ?>%</span>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12 col-md-7">
                                <div class="age-grid px-md-2">
                                    <div class="text-center age-item">
                                        <div class="age-label">0-10</div>
                                        <div class="stat-value"><?= number_format($age_0_10 ?? 0) ?></div>
                                    </div>
                                    <div class="text-center age-item">
                                        <div class="age-label">11-12</div>
                                        <div class="stat-value"><?= number_format($age_11_12 ?? 0) ?></div>
                                    </div>
                                    <div class="text-center age-item">
                                        <div class="age-label">13-14</div>
                                        <div class="stat-value"><?= number_format($age_13_14 ?? 0) ?></div>
                                    </div>
                                    <div class="text-center age-item">
                                        <div class="age-label">15-16</div>
                                        <div class="stat-value"><?= number_format($age_14_16 ?? 0) ?></div>
                                    </div>
                                    <div class="text-center age-item">
                                        <div class="age-label">16+</div>
                                        <div class="stat-value"><?= number_format($age_16_plus ?? 0) ?></div>
                                    </div>
                                    <div class="text-center age-item" style="display:none">
                                        <div class="age-label">N/A</div>
                                        <div class="stat-value"><?= number_format($age_na ?? 0) ?></div>
                                    </div>
                                </div>
                            </div>
                            </div>

                        </div> </div>
                </div>
            </div> </div>
</div>

            </div>
        </div>


        <!-- Scroll Down Indicator -->
        <div class="scroll-down-indicator">
            <span class="scroll-line"></span>
            <span class="scroll-text"><?= lang('Common.scroll') ?></span>
        </div>
    </section>

    <!-- Sponsors Section (ซ่อนไว้ใช้ในอนาคต) -->
    <!--
    <section class="sponsors-section py-5">
        <div class="container">
            <div class="sponsors-header text-center mb-5">
                <h2 class="sponsors-title fw-bold mb-3"><?= lang('Common.our_sponsors') ?></h2>
                <div class="sponsors-divider mx-auto" style="width: 80px; height: 3px; background: linear-gradient(90deg, transparent, #f3b81a, transparent);"></div>
            </div>

            <div class="row g-4 justify-content-center">
                <?php if (!empty($sponsors)): ?>
                    <?php foreach ($sponsors as $sponsor): ?>
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="sponsor-card text-center p-4 h-100" style="background: white; border-radius: 15px; box-shadow: 0 5px 20px rgba(0,0,0,0.1); transition: transform 0.3s;" onmouseover="this.style.transform='translateY(-5px)'" onmouseout="this.style.transform='translateY(0)'">
                            <div class="sponsor-logo-wrapper mb-3" style="height: 100px; display: flex; align-items: center; justify-content: center;">
                                <?php if (!empty($sponsor['logo'])): ?>
                                    <img src="<?= base_url($sponsor['logo']) ?>" alt="<?= $sponsor['name'] ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;" onerror="this.src='<?= base_url('public/img/jootball_logo.png') ?>'">
                                <?php else: ?>
                                    <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="<?= $sponsor['name'] ?>" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                <?php endif; ?>
                            </div>
                            <h5 class="sponsor-name fw-bold mb-2"><?= $sponsor['name'] ?></h5>
                            <?php if (!empty($sponsor['name_en'])): ?>
                                <p class="sponsor-name-en text-muted small mb-2"><?= $sponsor['name_en'] ?></p>
                            <?php endif; ?>
                            <?php if (!empty($sponsor['start']) && !empty($sponsor['end'])): ?>
                                <small class="text-muted">
                                    <i class="fas fa-calendar-alt me-1"></i>
                                    <?= date('d/m/Y', strtotime($sponsor['start'])) ?> - <?= date('d/m/Y', strtotime($sponsor['end'])) ?>
                                </small>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="col-12 text-center">
                        <p class="text-muted">ไม่มีข้อมูลผู้สนับสนุนในปัจจุบัน</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </section>
    -->

    <!-- Football Stats Section (loaded asynchronously so the page paints first) -->
    <section class="companies-section feature-section">
        <div class="container">
            <div id="football-stats-container"
                 data-url="<?= base_url('home/football_stats') ?>"
                 data-academy="<?= (int)($academy_id ?? 0) ?>"
                 data-tour="<?= (int)($tour_id ?? 0) ?>">
                <div class="text-center py-5" id="football-stats-loading">
                    <div class="spinner-border text-primary" role="status" style="width:3rem;height:3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <div class="mt-3 text-muted">Loading statistics...</div>
                </div>
            </div>
        </div>
    </section>

    <script>
    (function () {
        function hideSection(c) {
            var sec = c.closest ? c.closest('section') : null;
            if (sec) { sec.style.display = 'none'; } else { c.innerHTML = ''; }
        }

        function loadFootballStats() {
            var c = document.getElementById('football-stats-container');
            if (!c) return;
            var url = c.dataset.url + '?academy_id=' + (c.dataset.academy || 0) + '&tour_id=' + (c.dataset.tour || 0);

            // Never hang forever: abort the request after 25s.
            var controller = ('AbortController' in window) ? new AbortController() : null;
            var timer = setTimeout(function () { if (controller) controller.abort(); }, 25000);

            fetch(url, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' },
                signal: controller ? controller.signal : undefined
            })
                .then(function (r) {
                    if (!r.ok) throw new Error('HTTP ' + r.status);
                    return r.text();
                })
                .then(function (html) {
                    clearTimeout(timer);

                    // Content disabled from the backend (empty response) → hide quietly.
                    if (!html || !html.trim()) { hideSection(c); return; }

                    var tmp = document.createElement('div');
                    tmp.innerHTML = html;

                    // Detach scripts so we can run them in order after the markup is in the DOM.
                    var scripts = Array.prototype.slice.call(tmp.querySelectorAll('script'));
                    scripts.forEach(function (s) { s.parentNode.removeChild(s); });

                    c.innerHTML = '';
                    c.appendChild(tmp);

                    // Run external scripts (e.g. Chart.js) first, awaiting each, then inline ones.
                    (function run(i) {
                        if (i >= scripts.length) return;
                        var old = scripts[i];
                        var s = document.createElement('script');
                        if (old.src) {
                            s.src = old.src;
                            s.onload = s.onerror = function () { run(i + 1); };
                            document.body.appendChild(s);
                        } else {
                            s.textContent = old.textContent;
                            document.body.appendChild(s);
                            run(i + 1);
                        }
                    })(0);
                })
                .catch(function () {
                    clearTimeout(timer);
                    // On any failure/timeout, don't leave a stuck spinner — hide the section.
                    hideSection(c);
                });
        }

        // This inline script sits right after the container, so it already exists
        // in the DOM here — fire immediately (no DOMContentLoaded / requestIdleCallback
        // which could be delayed or never fire on an animation-heavy page).
        loadFootballStats();
    })();
    </script>


    <!-- Features Highlight Section -->
    <!-- Who We Serve Section (Redesigned) -->
    <section class="whois-modern py-5">
        <div class="container position-relative">
            <!-- Background Decorative Elements -->
            <div class="modern-deco deco-1"></div>
            <div class="modern-deco deco-2"></div>

            <div class="text-center mb-5 position-relative">
                <h2 class="modern-title"><?= lang('Common.why_choose_jootball') ?></h2>
                <div class="modern-divider mx-auto mb-3"></div>
                <p class="modern-description text-muted"><?= lang('Common.who_we_serve') ?></p>
            </div>

            <div class="row g-4 justify-content-center">
                <!-- Association -->
                <div class="col-6 col-md-3">
                    <div class="modern-card card-blue h-100">
                        <div class="card-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <div class="card-content">
                            <h5 class="card-title"><?= lang('Common.football_association') ?></h5>
                            <p class="card-text small opacity-75">เชื่อมต่อสมาคมและองค์กรกีฬาเข้าด้วยกัน</p>
                        </div>
                        <div class="card-glow"></div>
                    </div>
                </div>

                <!-- Academy -->
                <div class="col-6 col-md-3">
                    <a href="<?= base_url('home/sts_map');?>" class="modern-card card-green h-100 text-decoration-none">
                        <div class="card-icon">
                            <i class="fas fa-school"></i>
                        </div>
                        <div class="card-content">
                            <h5 class="card-title"><?= lang('Common.academy_training_center') ?></h5>
                            <p class="card-text small opacity-75">จัดการอะคาเดมี่และศูนย์ฝึกอย่างมืออาชีพ</p>
                        </div>
                        <div class="card-glow"></div>
                    </a>
                </div>

                <!-- Coach -->
                <div class="col-6 col-md-3">
                    <a href="<?= base_url('home/sts_coach');?>" class="modern-card card-orange h-100 text-decoration-none">
                        <div class="card-icon">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="card-content">
                            <h5 class="card-title"><?= lang('Common.coaches_title') ?></h5>
                            <p class="card-text small opacity-75">ยกระดับศักยภาพโค้ชด้วยข้อมูลสถิติ</p>
                        </div>
                        <div class="card-glow"></div>
                    </a>
                </div>

                <!-- Player -->
                <div class="col-6 col-md-3">
                    <a href="<?= base_url('home/sts_player_position');?>" class="modern-card card-purple h-100 text-decoration-none">
                        <div class="card-icon">
                            <i class="fas fa-running"></i>
                        </div>
                        <div class="card-content">
                            <h5 class="card-title"><?= lang('Common.youth') ?></h5>
                            <p class="card-text small opacity-75">ติดตามพัฒนาการนักเตะเยาวชนรายบุคคล</p>
                        </div>
                        <div class="card-glow"></div>
                    </a>
                </div>
            </div>
        </div>
    </section>



    <!-- Services Section -->
     <!--
    <section id="services" class="services-section">

        <div class="floating-footballs">
            <div class="football football-1"><i class="fas fa-futbol"></i></div>
            <div class="football football-2"><i class="fas fa-futbol"></i></div>
            <div class="football football-3"><i class="fas fa-futbol"></i></div>
        </div>
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="section-title"><?= lang('Common.our_services') ?></h2>
                <p class="section-description"><?= lang('Common.services_description') ?></p>
            </div>
            <div class="row g-4">
                <div class="col-lg-6">
                    <div class="service-card">
                        <div class="service-overlay"></div>
                        <div class="service-content">
                            <div class="service-number">01</div>
                            <div class="service-icon">
                                <div class="service-icon-circle service-icon-yellow">
                                    <i class="fas fa-user-graduate"></i>
                                </div>
                            </div>
                            <h3 class="service-title"><?= lang('Common.academy_database') ?></h3>
                            <p class="service-description">
                                <?= lang('Common.academy_description') ?>
                            </p>
                            <div class="service-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="service-card">
                        <div class="service-overlay"></div>
                        <div class="service-content">
                            <div class="service-number">02</div>
                            <div class="service-icon">
                                <div class="service-icon-circle service-icon-blue">
                                    <i class="fas fa-users"></i>
                                </div>
                            </div>
                            <h3 class="service-title"><?= lang('Common.coach_database') ?></h3>
                            <p class="service-description">
                                <?= lang('Common.coach_description') ?>
                            </p>
                            <div class="service-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="service-card">
                        <div class="service-overlay"></div>
                        <div class="service-content">
                            <div class="service-number">03</div>
                            <div class="service-icon">
                                <div class="service-icon-circle service-icon-yellow">
                                    <i class="fas fa-chalkboard-teacher"></i>
                                </div>
                            </div>
                            <h3 class="service-title"><?= lang('Common.player_database') ?></h3>
                            <p class="service-description">
                                <?= lang('Common.player_description') ?>
                            </p>
                            <div class="service-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="service-card">
                        <div class="service-overlay"></div>
                        <div class="service-content">
                            <div class="service-number">04</div>
                            <div class="service-icon">
                                <div class="service-icon-circle service-icon-blue">
                                    <i class="fas fa-campground"></i>
                                </div>
                            </div>
                            <h3 class="service-title"><?= lang('Common.other_support') ?></h3>
                            <p class="service-description">
                                <?= lang('Common.other_description') ?>
                            </p>
                            <div class="service-arrow">
                                <i class="fas fa-arrow-right"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    -->

    <!-- Recent Work Section -->
    <!-- <section id="portfolio" class="recent-work">
        <div class="decorative-elements">
            <div class="decoration decoration-1"></div>
            <div class="decoration decoration-2"></div>
            <div class="decoration decoration-3"></div>
        </div>
        <div class="container">
            <div class="text-center mb-5">
                <div class="work-badge">
                    <span>ผลงานของเรา</span>
                </div>
                <h2 class="section-title">ความสำเร็จที่โดดเด่น</h2>
                <p class="section-description">ผลงานที่เราภูมิใจและความสำเร็จในการพัฒนาฟุตบอลเยาวชน</p>
            </div>
            <div class="work-grid">
                <div class="work-item">
                    <div class="work-decoration">
                        <div class="work-decoration-dot"></div>
                    </div>
                    <div class="work-image">
                        <div class="icon-wrapper icon-wrapper-blue">
                            <i class="fas fa-futbol"></i>
                            <div class="icon-glow"></div>
                        </div>
                    </div>
                    <div class="work-content">
                        <h4 class="work-title">ชัมเปี้ยนคัพ 2024</h4>
                        <p class="work-category">การแข่งขัน</p>
                        <div class="work-divider"></div>
                    </div>
                </div>
                <div class="work-item">
                    <div class="work-decoration">
                        <div class="work-decoration-dot work-decoration-dot-blue"></div>
                    </div>
                    <div class="work-image">
                        <div class="icon-wrapper icon-wrapper-yellow">
                            <i class="fas fa-trophy"></i>
                            <div class="icon-glow"></div>
                        </div>
                    </div>
                    <div class="work-content">
                        <h4 class="work-title">นักเตะยอดเยี่ยม</h4>
                        <p class="work-category">รางวัล</p>
                        <div class="work-divider"></div>
                    </div>
                </div>
                <div class="work-item">
                    <div class="work-decoration">
                        <div class="work-decoration-dot"></div>
                    </div>
                    <div class="work-image">
                        <div class="icon-wrapper icon-wrapper-blue">
                            <i class="fas fa-users"></i>
                            <div class="icon-glow"></div>
                        </div>
                    </div>
                    <div class="work-content">
                        <h4 class="work-title">ค่ายเยาวชน</h4>
                        <p class="work-category">การพัฒนา</p>
                        <div class="work-divider"></div>
                    </div>
                </div>
            </div>
        </div>
    </section> -->

<section id="result" class="result-section">
    <h1 align="center"><?= lang('Common.result') ?></h1>

    <div class="swiper tournament-swiper">
        <div class="swiper-wrapper" id="tournamentWrapper"></div>
        <div class="swiper-pagination"></div>
    </div>

    <ul class="nav nav-tabs mt-2">
        <li class="nav-item">
            <button type="button" onclick="/*location.reload();*//* getTour();*/" class="nav-link active"
                    data-bs-toggle="tab"
                    data-bs-target="#home0">
                <?= lang('Tournament.competition') ?>
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#ranking">
                <?= lang('Tournament.standings') ?>
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#map">
                <?= lang('Tournament.match_bracket') ?>
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#player">
                <?= lang('Tournament.players') ?>
            </button>
        </li>
        <li class="nav-item">
            <button type="button" class="nav-link"
                    data-bs-toggle="tab"
                    data-bs-target="#summary">
                <?= lang('Tournament.summary') ?>
            </button>
        </li>
    </ul>

    <div class="tab-content mt-2">

        <div class="tab-pane fade show active" id="home0">
            <!-- Match Filters -->
            <div class="row g-2 mb-4 p-3 bg-light rounded shadow-sm border" style="color:black">
              <div class="col-md-4">
                <label class="form-label fw-bold text-primary small mb-1"><i class="fas fa-map-marker-alt me-1"></i><?= lang('Common.stadium') ?></label>
                <select id="filterStadium" class="form-select form-select-sm shadow-none border-secondary-subtle">
                  <option value="">-- All Stadiums --</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold text-primary small mb-1"><i class="fas fa-calendar-alt me-1"></i><?= lang('Common.date') ?></label>
                <select id="filterDate" class="form-select form-select-sm shadow-none border-secondary-subtle">
                  <option value="">-- All Dates --</option>
                </select>
              </div>
              <div class="col-md-4">
                <label class="form-label fw-bold text-primary small mb-1"><i class="fas fa-trophy me-1"></i><?= lang('Tournament.round_match') ?></label>
                <select id="filterRound" class="form-select form-select-sm shadow-none border-secondary-subtle">
                  <option value="">-- All Rounds --</option>
                </select>
              </div>
            </div>
            <div id="matchContainer"></div>
        </div>

        <div class="tab-pane fade" id="map">
            <div id="bracketWrapper">
                <svg id="bracketLines"></svg>
                <div id="bracketContainer"></div>
            </div>
        </div>

        <div class="tab-pane fade" id="player">
            <div class="players-grid" id="playersGrid"></div>
        </div>

        <div class="tab-pane fade" id="ranking">
            <div id="rankingContainer"></div>
        </div>

        <div class="tab-pane fade" id="summary">
            <div id="summaryContainer" class="pt-4 pb-4">

            <div class="row g-4">
                <!-- Games -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="sum-card-modern">
                        <div class="sum-icon-wrapper bg-blue-soft">
                            <i class="fas fa-futbol"></i>
                        </div>
                        <div class="sum-label"><?= lang('Common.games') ?></div>
                        <div class="sum-number" id="sum-math">-</div>
                    </div>
                </div>

                <!-- Teams -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="sum-card-modern">
                        <div class="sum-icon-wrapper bg-gold-soft">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="sum-label"><?= lang('Common.registered_teams') ?></div>
                        <div class="sum-number" id="sum-team">-</div>
                    </div>
                </div>

                <!-- Players -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="sum-card-modern">
                        <div class="sum-icon-wrapper bg-green-soft">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="sum-label"><?= lang('Common.players') ?></div>
                        <div class="sum-number" id="sum-player">-</div>
                    </div>
                </div>

                <!-- Coaches -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="sum-card-modern">
                        <div class="sum-icon-wrapper bg-purple-soft">
                            <i class="fas fa-user-tie"></i>
                        </div>
                        <div class="sum-label"><?= lang('Common.coaches') ?></div>
                        <div class="sum-number" id="sum-coach">-</div>
                    </div>
                </div>

                <!-- Cards -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="sum-card-modern">
                        <div class="sum-icon-wrapper bg-red-soft">
                            <i class="fas fa-copy"></i>
                        </div>
                        <div class="sum-label"><?= lang('Tournament.sum_card') ?></div>
                        <div class="sum-number" id="sum-card">-</div>
                    </div>
                </div>

                <!-- Best Player -->
                <div class="col-6 col-md-4 col-lg-2">
                    <div class="sum-card-modern">
                        <div class="sum-icon-wrapper bg-indigo-soft">
                            <i class="fas fa-trophy"></i>
                        </div>
                        <div class="sum-label"><?= lang('Tournament.sum_best_player') ?></div>
                        <div class="sum-number" id="sum-best-player">-</div>
                    </div>
                </div>
            </div>

            </div>
        </div>
        <!-- END tab summary -->

    </div>
</section>

    <section id="mvp" class="services-mvp">
    <div class="container mt-4 mb-5">
        <h3 class="text-center fw-semibold mb-4 text-black opacity-75" id="tour-name">
                Most Valuable Player (MVP)
        </h3>

      <div class="row g-3 justify-content-center">
        <!-- LEFT -->

        <div class="col-md-5">
          <div class="card">
            <div class="card-header fw-bold"><?= lang('Tournament.stadium') ?></div>
            <div class="card-body">
              <div id="footballField">
                <div class="penalty-top"></div>
                <div class="penalty-bottom"></div>
                <div class="zone fw" data-zone="fw"></div>
                <div class="zone mf" data-zone="mf"></div>
                <div class="zone df" data-zone="df"></div>
                <div class="zone gk" data-zone="gk"></div>
              </div>
              <div class="manager-zone">
                <div class="zone manager" data-zone="manager"></div>
              </div>
            </div>
          </div>
        </div>

        <!-- LEFT -->
        <div class="col-md-5">
          <div class="card">
            <div class="card-header fw-bold"><?= lang('Tournament.player_info') ?></div>
            <div id="playerInfo">
              <div class="empty-info"><?= lang('Tournament.player_show_more') ?></div>
            </div>
          </div>
      </div>
    </div>
    </section>

<div class="modal fade" id="eventModal" tabindex="-1">
  <div class="modal-dialog modal-dialog-centered modal-lg">
    <div class="modal-content">

      <div class="modal-header">
        <h5 class="modal-title">
          Match Story
        </h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body">

        <ul class="list-group list-group-flush">

        </ul>

      </div>

      <!--
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
      </div>
      -->

    </div>
  </div>
</div>

<!-- ==================== FLOATING HELP GUIDE BUTTON ==================== -->
<button type="button"
        class="help-guide-fab"
        data-bs-toggle="offcanvas"
        data-bs-target="#helpGuideOffcanvas"
        aria-controls="helpGuideOffcanvas"
        aria-label="<?= lang('Common.help_guide_button') ?>">
    <span class="fab-pulse"></span>
    <span class="fab-pulse fab-pulse-2"></span>
    <span class="fab-icon"><i class="fas fa-question"></i></span>
    <span class="fab-label"><?= lang('Common.help_guide_button') ?></span>
</button>

<!-- ==================== HELP GUIDE OFFCANVAS PANEL ==================== -->
<div class="offcanvas offcanvas-end help-guide-offcanvas" tabindex="-1" id="helpGuideOffcanvas" aria-labelledby="helpGuideOffcanvasLabel">
    <div class="offcanvas-header help-guide-header">
        <div>
            <h5 class="offcanvas-title fw-bold mb-1" id="helpGuideOffcanvasLabel">
                <i class="fas fa-book-open me-2"></i><?= lang('Common.help_guide_title') ?>
            </h5>
            <small class="opacity-75"><?= lang('Common.help_guide_subtitle') ?></small>
        </div>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas" aria-label="<?= lang('Common.help_guide_close') ?>"></button>
    </div>
    <div class="offcanvas-body help-guide-body">
        <?php
        $currentLang = function_exists('get_current_language') ? get_current_language() : 'th';
        $guides = isset($help_guides) && is_array($help_guides) ? $help_guides : [];
        ?>

        <?php if (empty($guides)): ?>
            <div class="text-center text-muted py-5">
                <i class="fas fa-book-open fa-2x mb-3 opacity-50"></i>
                <p class="mb-0"><?= lang('Common.no_data_found') ?></p>
            </div>
        <?php else: ?>
            <ol class="help-guide-list list-unstyled m-0">
                <?php foreach ($guides as $i => $g):
                    $idx   = $i + 1;
                    $title = ($currentLang === 'en' && !empty($g['title_en'])) ? $g['title_en'] : $g['title_th'];
                    $href  = $g['link'];
                    $isExternal = preg_match('#^(https?:)?//#i', $href) === 1;
                    $resolved   = $isExternal ? $href : base_url(ltrim($href, '/'));
                ?>
                <li class="help-guide-list-item">
                    <a href="<?= esc($resolved) ?>"
                       <?= $isExternal ? 'target="_blank" rel="noopener"' : '' ?>
                       class="guide-card">
                        <span class="guide-step-num"><?= $idx ?></span>
                        <span class="guide-step-title"><?= esc($title) ?></span>
                        <span class="guide-step-arrow">
                            <i class="fas <?= $isExternal ? 'fa-external-link-alt' : 'fa-arrow-right' ?>"></i>
                        </span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ol>
        <?php endif; ?>
    </div>
</div>

<style>
/* ==================== Floating Help Guide ==================== */
.tiktok-floating-btn {
    top: 96px !important;
    right: 24px !important;
    bottom: auto !important;
    z-index: 1049 !important;
}

.help-guide-fab {
    position: fixed;
    right: 24px;
    bottom: 24px;
    z-index: 1049;
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 22px 14px 18px;
    border: none;
    border-radius: 999px;
    background: linear-gradient(135deg, #FFD24C 0%, #F77F00 100%);
    color: #1a1a1a;
    font-weight: 700;
    font-size: 15px;
    box-shadow: 0 10px 30px rgba(247, 127, 0, .45), 0 4px 12px rgba(0, 0, 0, .15);
    cursor: pointer;
    transition: transform .25s ease, box-shadow .25s ease;
}
.help-guide-fab:hover {
    transform: translateY(-3px) scale(1.03);
    box-shadow: 0 14px 36px rgba(247, 127, 0, .55), 0 6px 18px rgba(0, 0, 0, .18);
}
.help-guide-fab:active { transform: translateY(-1px) scale(.99); }

.help-guide-fab .fab-icon {
    width: 34px; height: 34px;
    border-radius: 50%;
    background: #1a1a1a;
    color: #FFD24C;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    flex-shrink: 0;
}

.help-guide-fab .fab-label { white-space: nowrap; }

.help-guide-fab .fab-pulse {
    position: absolute;
    inset: 0;
    border-radius: inherit;
    background: rgba(255, 210, 76, .55);
    z-index: -1;
    animation: fabPulse 2s ease-out infinite;
}
.help-guide-fab .fab-pulse-2 { animation-delay: 1s; }
@keyframes fabPulse {
    0%   { transform: scale(1);   opacity: .7; }
    100% { transform: scale(1.5); opacity: 0; }
}

@media (max-width: 576px) {
    .tiktok-floating-btn {
        top: 82px !important;
        right: 16px !important;
        bottom: auto !important;
    }

    .help-guide-fab {
        right: 16px;
        bottom: 16px;
        padding: 12px;
        gap: 0;
    }
    .help-guide-fab .fab-label { display: none; }
}

/* ==================== Help Guide Offcanvas ==================== */
.help-guide-offcanvas {
    width: min(440px, 96vw);
    border-left: 4px solid #FFD24C;
    box-shadow: -10px 0 40px rgba(0,0,0,.18);
}
.help-guide-header {
    background: linear-gradient(135deg, #1d3557 0%, #0a1929 100%);
    color: #fff;
    padding: 20px 24px;
}
.help-guide-header .offcanvas-title { color: #FFD24C; }
.help-guide-body { padding: 18px 18px 32px; background: #f7f9fc; }

.help-guide-list { display: flex; flex-direction: column; gap: 12px; }
.help-guide-list-item { list-style: none; }

.help-guide-list .guide-card {
    display: flex;
    align-items: center;
    gap: 12px;
    width: 100%;
    padding: 14px 16px;
    background: #fff;
    border: 1px solid rgba(0,0,0,.06);
    border-radius: 14px;
    box-shadow: 0 2px 8px rgba(0,0,0,.04);
    color: #1a1a1a;
    font-weight: 600;
    text-decoration: none;
    transition: transform .2s ease, box-shadow .2s ease, background .2s ease;
}
.help-guide-list .guide-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 22px rgba(247, 127, 0, .18);
    background: #fffbe9;
    color: #1a1a1a;
}
.help-guide-list .guide-card:focus-visible {
    outline: 3px solid rgba(247, 127, 0, .35);
    outline-offset: 2px;
}

.help-guide-list .guide-step-num {
    width: 30px; height: 30px;
    border-radius: 50%;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    background: linear-gradient(135deg, #FFD24C 0%, #F77F00 100%);
    color: #1a1a1a;
    font-weight: 800;
    font-size: 14px;
    flex-shrink: 0;
    box-shadow: 0 2px 6px rgba(247, 127, 0, .45);
}
.help-guide-list .guide-step-title {
    flex: 1;
    font-size: 15px;
    line-height: 1.35;
}
.help-guide-list .guide-step-arrow {
    color: #F77F00;
    font-size: 13px;
    transition: transform .2s ease;
    flex-shrink: 0;
}
.help-guide-list .guide-card:hover .guide-step-arrow { transform: translateX(3px); }
</style>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>

<script>
$(document).ready(function() {
    // Re-render content when switching tabs
    $(document).on('shown.bs.tab', 'button[data-bs-toggle="tab"]', function (e) {
        const target = $(e.target).data('bs-target');

        if (target === '#home' && window.bracketData) {
            renderMatches(window.bracketData);
            sortMatchCellByGroup();
        }
        if (target === '#ranking' && window.bracketData) {
            renderRanking(window.bracketData);
        }
        if (target === '#map' && window.bracketData) {
            renderBracket(window.bracketData);
            setTimeout(drawBracketLines, 50);
        }
        if (target === '#player' && window.bracketData) {
            getTourPlayer();
        }
    });
});
</script>



<?= $this->include('Modules\Home\Views\partials\index\styles_main') ?>


<?= $this->include('Modules\Home\Views\partials\index\scripts_main') ?>
<?php $this->endSection(); ?>
