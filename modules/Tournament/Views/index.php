<?php $this->extend('template/tournament') ?>

<?php $this->section('content'); ?>
<style>
    :root {
        --primary-color: #06346D;
        --secondary-color: #F3B71B;
        --navy-dark: #042441;
        --navy-gradient: linear-gradient(135deg, #06346D 0%, #042441 100%);
        --gold-gradient: linear-gradient(135deg, #F3B71B 0%, #d19e0f 100%);
        --text-dark: #1a202c;
        --text-gray: #64748b;
        --bg-light: #f8fafc;
        --card-shadow: 0 10px 40px rgba(6, 52, 109, 0.15);
        --border-radius: 16px;
    }

    .dashboard-container {
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    /* Tournament Header Card */
    .tournament-header-card {
        background: var(--navy-gradient);
        border-radius: var(--border-radius);
        padding: 35px 40px;
        margin-bottom: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: var(--card-shadow);
    }

    .tournament-header-card::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -10%;
        width: 300px;
        height: 300px;
        background: var(--gold-gradient);
        opacity: 0.1;
        border-radius: 50%;
    }

    .tournament-header-card::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 10%;
        width: 200px;
        height: 200px;
        background: rgba(255, 255, 255, 0.05);
        border-radius: 50%;
    }

    .header-content {
        display: flex;
        align-items: center;
        justify-content: space-between;
        position: relative;
        z-index: 1;
    }

    .header-info {
        display: flex;
        align-items: center;
        gap: 20px;
    }

    .header-icon {
        width: 70px;
        height: 70px;
        background: var(--gold-gradient);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 25px rgba(243, 183, 27, 0.4);
    }

    .header-icon i {
        font-size: 28px;
        color: var(--primary-color);
    }

    .header-text h2 {
        color: #ffffff;
        font-size: 1.5rem;
        font-weight: 700;
        margin: 0 0 8px 0;
    }

    .header-text .date-range {
        color: rgba(255, 255, 255, 0.85);
        font-size: 1.1rem;
        font-weight: 500;
        display: flex;
        align-items: center;
        gap: 10px;
    }

    .header-text .date-range i {
        color: var(--secondary-color);
    }

    .header-badge {
        background: var(--gold-gradient);
        color: var(--primary-color);
        padding: 10px 25px;
        border-radius: 25px;
        font-weight: 700;
        font-size: 0.9rem;
        text-transform: uppercase;
        letter-spacing: 1px;
        box-shadow: 0 4px 15px rgba(243, 183, 27, 0.3);
    }

    /* Stats Grid */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 25px;
        margin-top: 10px;
    }

    @media (max-width: 992px) {
        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 576px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .header-content {
            flex-direction: column;
            text-align: center;
            gap: 20px;
        }

        .header-info {
            flex-direction: column;
        }

        .tournament-header-card {
            padding: 25px 20px;
        }
    }

    /* Stat Card */
    .stat-card {
        background: #ffffff;
        border-radius: var(--border-radius);
        padding: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 4px 20px rgba(6, 52, 109, 0.08);
        border: 1px solid rgba(6, 52, 109, 0.08);
        transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .stat-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(6, 52, 109, 0.15);
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: var(--navy-gradient);
    }

    .stat-card-inner {
        display: flex;
        align-items: flex-start;
        justify-content: space-between;
    }

    .stat-info h3 {
        color: var(--text-gray);
        font-size: 0.95rem;
        font-weight: 600;
        margin: 0 0 12px 0;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stat-info .stat-number {
        color: var(--primary-color);
        font-size: 3rem;
        font-weight: 800;
        line-height: 1;
        margin: 0;
    }

    .stat-icon {
        width: 60px;
        height: 60px;
        background: var(--navy-gradient);
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 6px 20px rgba(6, 52, 109, 0.25);
    }

    .stat-icon i {
        font-size: 24px;
        color: #ffffff;
    }

    /* Different accent colors for top border */
    .stat-card.teams::before {
        background: linear-gradient(90deg, #059669 0%, #10b981 100%);
    }

    .stat-card.players::before {
        background: linear-gradient(90deg, #dc2626 0%, #f87171 100%);
    }

    .stat-card.staffs::before {
        background: linear-gradient(90deg, #7c3aed 0%, #a78bfa 100%);
    }

    .stat-card.teams .stat-icon {
        background: linear-gradient(135deg, #059669 0%, #10b981 100%);
    }

    .stat-card.players .stat-icon {
        background: linear-gradient(135deg, #dc2626 0%, #f87171 100%);
    }

    .stat-card.staffs .stat-icon {
        background: linear-gradient(135deg, #7c3aed 0%, #a78bfa 100%);
    }

    /* Animations */
    .fade-up {
        opacity: 0;
        transform: translateY(30px);
        animation: fadeUp 0.6s ease forwards;
    }

    .fade-up:nth-child(1) {
        animation-delay: 0.1s;
    }

    .fade-up:nth-child(2) {
        animation-delay: 0.2s;
    }

    .fade-up:nth-child(3) {
        animation-delay: 0.3s;
    }

    @keyframes fadeUp {
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .pulse-icon {
        animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }
    }
</style>

<div class="dashboard-container">
    <!-- Tournament Header -->
    <div class="tournament-header-card fade-up">
        <div class="header-content">
            <div class="header-info">
                <div class="header-icon pulse-icon">
                    <i class="fas fa-trophy"></i>
                </div>
                <div class="header-text">
                    <h2><?= esc($data['name']) ?></h2>
                    <div class="date-range">
                        <i class="fas fa-calendar-alt"></i>
                        <span><?= $Mydate->date_conv($data['start_date'], 0) ?> - <?= $Mydate->date_conv($data['end_date'], 0) ?></span>
                    </div>
                </div>
            </div>
            <div class="header-badge">
                <i class="fas fa-futbol me-2"></i><?= lang('Tournament.dashboard') ?? 'Dashboard' ?>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <!-- Teams Card -->
        <div class="stat-card teams fade-up">
            <div class="stat-card-inner">
                <div class="stat-info">
                    <h3><?= lang('Common.teams') ?></h3>
                    <p class="stat-number" data-count="<?= $count_teams_approved ?>" data-total="<?= $count_teams ?>"><span class="count-value"><?= number_format($count_teams_approved) ?></span> / <?= number_format($count_teams) ?></p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <!-- Players Card -->
        <div class="stat-card players fade-up">
            <div class="stat-card-inner">
                <div class="stat-info">
                    <h3><?= lang('Common.players') ?></h3>
                    <p class="stat-number" data-count="<?= $count_players ?>"><?= number_format($count_players) ?></p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-running"></i>
                </div>
            </div>
        </div>

        <!-- Staffs Card -->
        <div class="stat-card staffs fade-up">
            <div class="stat-card-inner">
                <div class="stat-info">
                    <h3><?= lang('Common.staffs') ?></h3>
                    <p class="stat-number" data-count="<?= $count_coachs ?>"><?= number_format($count_coachs) ?></p>
                </div>
                <div class="stat-icon">
                    <i class="fas fa-user-tie"></i>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Counter animation for stat numbers
        const counters = document.querySelectorAll('.stat-number');

        counters.forEach(counter => {
            const hasTotal = counter.getAttribute('data-total') !== null;
            const target = parseInt(counter.getAttribute('data-count')) || parseInt(counter.textContent.replace(/[^0-9]/g, ''));
            const total = hasTotal ? parseInt(counter.getAttribute('data-total')) : null;
            const duration = 1500; // 1.5 seconds
            const increment = target / (duration / 16); // 60fps
            let current = 0;

            const updateCounter = () => {
                current += increment;
                if (current < target) {
                    const val = new Intl.NumberFormat().format(Math.floor(current));
                    counter.innerHTML = hasTotal ? val + ' / ' + new Intl.NumberFormat().format(total) : val;
                    requestAnimationFrame(updateCounter);
                } else {
                    const val = new Intl.NumberFormat().format(target);
                    counter.innerHTML = hasTotal ? val + ' / ' + new Intl.NumberFormat().format(total) : val;
                }
            };

            // Start animation when card is visible
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        updateCounter();
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5
            });

            observer.observe(counter);
        });

        // Hover effects for stat cards
        const statCards = document.querySelectorAll('.stat-card');
        statCards.forEach(card => {
            card.addEventListener('mouseenter', function() {
                const icon = this.querySelector('.stat-icon');
                if (icon) {
                    icon.style.transform = 'scale(1.1) rotate(5deg)';
                    icon.style.transition = 'transform 0.3s ease';
                }
            });

            card.addEventListener('mouseleave', function() {
                const icon = this.querySelector('.stat-icon');
                if (icon) {
                    icon.style.transform = 'scale(1) rotate(0deg)';
                }
            });
        });
    });
</script>
<?php $this->endSection(); ?>