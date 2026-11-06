<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<style>
    :root {
        --primary-color: #16478D;
        --secondary-color: #13DEB9;
        --card-shadow: 0 2px 12px rgba(0,0,0,0.08);
        --border-radius: 12px;
    }

    .ranking-container {
        background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
        border-radius: var(--border-radius);
        padding: 2rem;
    }

    .ranking-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 2rem;
        padding-bottom: 1.5rem;
        border-bottom: 2px solid #e9ecef;
    }

    .ranking-header .icon-box {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%);
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: var(--card-shadow);
    }

    .ranking-header .icon-box i {
        color: white;
        font-size: 1.5rem;
    }

    .ranking-header h4 {
        color: var(--primary-color);
        font-weight: 700;
        margin: 0;
    }

    .ranking-cards {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.5rem;
    }

    .team-card {
        background: white;
        border-radius: var(--border-radius);
        padding: 1.5rem;
        box-shadow: var(--card-shadow);
        transition: all 0.3s ease;
        border: 1px solid #f1f5f9;
        position: relative;
        overflow: hidden;
    }

    .team-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 12px 30px rgba(0,0,0,0.12);
        border-color: var(--primary-color);
    }

    .team-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, var(--primary-color) 0%, var(--secondary-color) 100%);
    }

    .rank-number {
        position: absolute;
        top: 1rem;
        right: 1rem;
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        font-size: 1.1rem;
        box-shadow: 0 4px 12px rgba(22, 71, 141, 0.3);
    }

    .team-header {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .team-logo {
        width: 60px;
        height: 60px;
        border-radius: 10px;
        overflow: hidden;
        border: 2px solid var(--primary-color);
        flex-shrink: 0;
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .team-logo img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .team-info h6 {
        color: var(--primary-color);
        font-weight: 700;
        margin: 0 0 0.25rem 0;
        font-size: 1.05rem;
    }

    .team-info .badge {
        background-color: #f0f4ff;
        color: var(--primary-color);
        border: 1px solid var(--primary-color);
        font-size: 0.75rem;
        padding: 4px 8px;
    }

    .stats-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .stat-item {
        text-align: center;
    }

    .stat-label {
        font-size: 0.75rem;
        color: #7C8FAC;
        font-weight: 600;
        text-transform: uppercase;
        margin-bottom: 0.5rem;
    }

    .stat-value {
        font-size: 1.3rem;
        font-weight: 700;
        color: var(--primary-color);
    }

    .stat-value.wins {
        color: #10b981;
    }

    .stat-value.draws {
        color: #f59e0b;
    }

    .stat-value.losses {
        color: #ef4444;
    }

    .points-section {
        background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%);
        color: white;
        padding: 1rem;
        border-radius: 10px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1rem;
    }

    .points-label {
        font-size: 0.9rem;
        opacity: 0.9;
    }

    .points-value {
        font-size: 1.8rem;
        font-weight: 700;
    }

    .timeline-badges {
        display: flex;
        gap: 0.5rem;
        justify-content: center;
    }

    .timeline-badge {
        width: 28px;
        height: 28px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.8rem;
        font-weight: 700;
        color: white;
    }

    .timeline-badge.win {
        background-color: #10b981;
    }

    .timeline-badge.draw {
        background-color: #f59e0b;
    }

    .timeline-badge.loss {
        background-color: #ef4444;
    }

    @media (max-width: 768px) {
        .ranking-cards {
            grid-template-columns: 1fr;
        }

        .stats-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">

        <div class="ranking-container">
            <div class="ranking-header">
                <div class="icon-box">
                    <i class="ti ti-trophy"></i>
                </div>
                <div>
                    <h4><?= lang('Common.standings') ?? 'ตารางคะแนน' ?></h4>
                    <p class="text-muted mb-0 small">
                        <i class="ti ti-info-circle me-1"></i>
                        <?= lang('Common.total_teams') ?>: <strong><?= count($data) ?> <?= lang('Common.teams') ?></strong>
                    </p>
                </div>
            </div>

            <div class="ranking-cards">
                <?php
                $rank = 1;
                foreach ($data as $value) {
                ?>
                    <div class="team-card">
                        <div class="rank-number"><?= $rank ?></div>

                        <div class="team-header">
                            <div class="team-logo">
                                <img src="<?= base_url($value['logo'] ?? 'public/img/jootball_logo.png') ?>" alt="<?= $value['name'] ?>" onerror="this.src='<?= base_url('public/img/jootball_logo.png') ?>'">
                            </div>
                            <div class="team-info">
                                <h6><?= $value['name'] ?></h6>
                                <span class="badge">
                                    <i class="ti ti-users-group me-1"></i>
                                    <?= $value['match'] ?> <?= lang('Common.matches_unit') ?>
                                </span>
                            </div>
                        </div>

                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-label"><?= lang('Common.win') ?></div>
                                <div class="stat-value wins"><?= $value['W'] ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label"><?= lang('Common.draw') ?></div>
                                <div class="stat-value draws"><?= $value['D'] ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label"><?= lang('Common.loss') ?></div>
                                <div class="stat-value losses"><?= $value['L'] ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-label"><?= lang('Common.goal_difference') ?></div>
                                <div class="stat-value"><?= $value['diff'] ?></div>
                            </div>
                        </div>

                        <div class="points-section">
                            <span class="points-label"><?= lang('Common.total_points') ?></span>
                            <span class="points-value"><?= $value['points'] ?></span>
                        </div>

                        <div style="text-align: center; padding: 0.5rem 0; border-top: 1px solid #f1f5f9; padding-top: 1rem;">
                            <span class="small text-muted" style="display: block; margin-bottom: 0.5rem;"><?= lang('Common.last_5_matches') ?></span>
                            <div class="timeline-badges">
                                <?php
                                if (!empty($value['last_timeline'])) {
                                    echo $value['last_timeline'];
                                } else {
                                    echo '<span class="text-muted small">-</span>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                <?php
                    $rank++;
                }
                ?>
            </div>
        </div>

    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $.ajax({
            method: "GET",
            url: "<?= base_url('admin/' . @$data['tour_id'] . @$data['tour_id']) ?>",
        });
    });
</script>
<?php $this->endSection(); ?>