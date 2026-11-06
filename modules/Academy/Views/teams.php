<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-layer-group me-2"></i><?= lang('Common.academy_teams') ?>
                </h4>
                <?php if (!empty($academy['name'])): ?>
                    <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($canCreateTeam)): ?>
            <div>
                <a href="<?= base_url('academy/team-create/' . esc($academy['id'])) ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus-circle me-2"></i><?= lang('Common.academy_add_team') ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="card-body p-4">
            <?php if (!empty($teams)): ?>
                <div class="row g-4">
                    <?php foreach ($teams as $team):
                        $teamName     = $team['name'] ?? '';
                        $teamNameEn   = $team['name_en'] ?? '';
                        $playerCount  = isset($team['player_count']) ? (int)$team['player_count'] : null;
                        $genName      = $team['generation_name'] ?? '';
                        $coachNames   = $team['coach_names'] ?? '';
                        $hasLogo      = !empty($team['logo']);
                        $logoSrc      = $hasLogo ? base_url($team['logo']) : '';
                        $initials     = mb_substr(trim($teamName) !== '' ? trim($teamName) : '?', 0, 1, 'UTF-8');
                        $teamUrl      = base_url('academy/team-info/' . esc($academy['id']) . '/' . esc($team['id']));
                    ?>
                    <div class="col-md-6 col-lg-4 col-xl-3">
                        <div class="academy-team-card position-relative h-100 d-flex flex-column">
                            <!-- Cover -->
                            <a href="<?= $teamUrl ?>" class="academy-team-card__cover text-decoration-none"></a>

                            <!-- Avatar -->
                            <div class="academy-team-card__avatar-wrap">
                                <?php if ($hasLogo): ?>
                                    <img src="<?= $logoSrc ?>"
                                         alt="<?= esc($teamName) ?>"
                                         class="academy-team-card__avatar"
                                         onerror="this.onerror=null;this.style.display='none';this.nextElementSibling.style.display='flex';">
                                    <div class="academy-team-card__avatar academy-team-card__avatar--fallback" style="display:none;">
                                        <?= esc($initials) ?>
                                    </div>
                                <?php else: ?>
                                    <div class="academy-team-card__avatar academy-team-card__avatar--fallback">
                                        <?= esc($initials) ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Body -->
                            <a href="<?= $teamUrl ?>" class="academy-team-card__body text-decoration-none flex-grow-1 d-flex flex-column">
                                <h6 class="academy-team-card__name"><?= esc($teamName) ?></h6>
                                <p class="academy-team-card__name-en">
                                    <?= !empty($teamNameEn) ? esc($teamNameEn) : '&nbsp;' ?>
                                </p>

                                <!-- Stats -->
                                <div class="academy-team-card__stats">
                                    <div class="academy-team-card__stat">
                                        <i class="fas fa-running"></i>
                                        <span class="academy-team-card__stat-value">
                                            <?= $playerCount !== null ? $playerCount : 0 ?>
                                        </span>
                                        <span class="academy-team-card__stat-label"><?= lang('Common.persons') ?></span>
                                    </div>
                                </div>

                            </a>

                            <!-- Footer -->
                            <!-- รวมปุ่มดูรายละเอียดกับแก้ไขเป็นปุ่มเดียว ชี้ไปหน้ารายละเอียด
                                 ซึ่งมีปุ่มแก้ไขข้อมูลอยู่แล้ว (Views/index.php) -->
                            <div class="academy-team-card__footer">
                                <a href="<?= $teamUrl ?>" class="btn btn-sm btn-light rounded-pill flex-grow-1" title="<?= lang('Common.manage_team') ?>">
                                    <i class="fas fa-users-cog me-1"></i><?= lang('Common.manage_team') ?>
                                </a>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-layer-group fa-4x text-muted opacity-25"></i>
                    </div>
                    <h5 class="fw-bold text-muted mb-2"><?= lang('Common.academy_no_teams') ?></h5>
                    <p class="text-muted small mb-4"><?= lang('Common.academy_no_teams_desc') ?></p>
                    <?php if (!empty($canCreateTeam)): ?>
                        <a href="<?= base_url('academy/team-create/' . esc($academy['id'])) ?>"
                           class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-plus-circle me-2"></i><?= lang('Common.academy_create_first_team') ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<style>
    .academy-team-card {
        background: #fff;
        border-radius: 1rem;
        box-shadow: 0 2px 8px rgba(22, 71, 141, 0.06);
        border: 1px solid #eef2f7;
        overflow: hidden;
        transition: transform 0.2s ease, box-shadow 0.2s ease;
    }
    .academy-team-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 28px rgba(22, 71, 141, 0.15);
    }

    .academy-team-card__cover {
        display: block;
        height: 80px;
        background: #16478d;
        position: relative;
    }
    .academy-team-card__gen-badge {
        position: absolute;
        top: 12px;
        right: 12px;
        background: rgba(255,255,255,0.92);
        color: #16478d;
        font-size: 0.72rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 999px;
        backdrop-filter: blur(4px);
        z-index: 2;
        max-width: calc(100% - 24px);
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .academy-team-card__avatar-wrap {
        position: relative;
        margin-top: -40px;
        display: flex;
        justify-content: center;
        z-index: 3;
    }
    .academy-team-card__avatar {
        width: 80px;
        height: 80px;
        border-radius: 50%;
        object-fit: cover;
        background: #fff;
        border: 4px solid #fff;
        box-shadow: 0 4px 12px rgba(22, 71, 141, 0.18);
    }
    .academy-team-card__avatar--fallback {
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        font-weight: 700;
        color: #fff;
        background: #16478d;
        text-transform: uppercase;
    }

    .academy-team-card__body {
        padding: 0.75rem 1rem 1rem;
        text-align: center;
        color: #2A3547;
    }
    .academy-team-card__name {
        font-weight: 700;
        font-size: 1rem;
        color: #16478d;
        margin: 0.5rem 0 0.15rem;
        line-height: 1.3;
        word-break: break-word;
    }
    .academy-team-card__name-en {
        font-size: 0.8rem;
        color: #7C8FAC;
        margin: 0 0 0.75rem;
        min-height: 1.2em;
    }

    .academy-team-card__stats {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
    }
    .academy-team-card__stat {
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
        background: #F6F9FC;
        border: 1px solid #EAEFF4;
        border-radius: 999px;
        padding: 5px 14px;
        font-size: 0.82rem;
        color: #2A3547;
    }
    .academy-team-card__stat i {
        color: #16478d;
        font-size: 0.78rem;
    }
    .academy-team-card__stat-value {
        font-weight: 700;
    }
    .academy-team-card__stat-label {
        color: #5A6A85;
        font-size: 0.78rem;
    }

    .academy-team-card__coach {
        font-size: 0.78rem;
        color: #2A3547;
        background: #F6F9FC;
        border-radius: 0.65rem;
        padding: 8px 10px;
        line-height: 1.35;
        min-height: 42px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
        gap: 4px;
    }
    .text-truncate-2 {
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .academy-team-card__footer {
        padding: 0.65rem 0.85rem 0.85rem;
        display: flex;
        gap: 0.4rem;
        border-top: 1px solid #f1f3f7;
        background: #fbfcfe;
    }
    .academy-team-card__footer .btn {
        font-size: 0.78rem;
        padding: 0.35rem 0.6rem;
    }
    .academy-team-card__footer .btn-light {
        background: #fff;
        border: 1px solid #DFE5EF;
        color: #16478d;
    }
    .academy-team-card__footer .btn-light:hover {
        background: #16478d;
        color: #fff;
        border-color: #16478d;
    }
</style>
<?php $this->endSection(); ?>
