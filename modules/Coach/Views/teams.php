<?php $this->extend('template/coach') ?>
<?= $this->section('title') ?>
<?= lang('Common.my_clubs_title') ?>
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
.coach-teams-page {
    background-color: var(--coach-gray-100);
    min-height: calc(100vh - 200px);
    padding: 2rem 0;
}

.coach-page-header {
    background-color: var(--coach-light);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    padding: 2rem;
    margin-bottom: 2rem;
    box-shadow: var(--coach-shadow);
}

.coach-page-title {
    color: var(--coach-primary);
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.coach-page-subtitle {
    color: var(--coach-gray-600);
    margin-bottom: 0;
}

.coach-team-card {
    background-color: var(--coach-light);
    border: 2px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    box-shadow: var(--coach-shadow);
    margin-bottom: 2rem;
    transition: var(--coach-transition);
    overflow: hidden;
}

.coach-team-card:hover {
    border-color: var(--coach-secondary);
    transform: translateY(-3px);
    box-shadow: var(--coach-shadow-lg);
}

.coach-team-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
    background-color: var(--coach-primary);
}

.coach-team-logo {
    width: 80px;
    height: 80px;
    border-radius: var(--coach-border-radius);
    object-fit: cover;
    border: 3px solid var(--coach-secondary);
}

.coach-team-logo-placeholder {
    width: 80px;
    height: 80px;
    background-color: var(--coach-gray-100);
    border: 3px solid var(--coach-secondary);
    border-radius: var(--coach-border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--coach-primary);
}

.coach-team-name {
    color: var(--coach-primary);
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.coach-team-info {
    color: var(--coach-gray-600);
    margin-bottom: 0.5rem;
}

.coach-team-info i {
    color: var(--coach-secondary);
    width: 20px;
}

.coach-btn-manage {
    background-color: var(--coach-primary);
    border-color: var(--coach-primary);
    color: var(--coach-light);
    padding: 0.5rem 1rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    text-decoration: none;
    margin-right: 0.5rem;
    margin-bottom: 0.5rem;
    display: inline-block;
}

.coach-btn-manage:hover {
    background-color: var(--coach-dark);
    color: var(--coach-light);
    transform: translateY(-1px);
    box-shadow: var(--coach-shadow);
}

.coach-btn-players {
    background-color: var(--coach-info);
    border-color: var(--coach-info);
    color: var(--coach-light);
    padding: 0.5rem 1rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    text-decoration: none;
    margin-bottom: 0.5rem;
    display: inline-block;
}

.coach-btn-players:hover {
    background-color: var(--coach-primary);
    color: var(--coach-light);
    transform: translateY(-1px);
    box-shadow: var(--coach-shadow);
}

.coach-btn-back {
    background-color: transparent;
    border: 2px solid var(--coach-primary);
    color: var(--coach-primary);
    padding: 0.75rem 1.5rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    text-decoration: none;
}

.coach-btn-back:hover {
    background-color: var(--coach-primary);
    color: var(--coach-light);
    transform: translateY(-1px);
    box-shadow: var(--coach-shadow);
}

.coach-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--coach-gray-500);
    background-color: var(--coach-light);
    border-radius: var(--coach-border-radius);
    box-shadow: var(--coach-shadow);
}

.coach-empty-state i {
    font-size: 4rem;
    color: var(--coach-secondary);
    margin-bottom: 2rem;
    opacity: 0.7;
}

.coach-empty-state h4 {
    color: var(--coach-primary);
    font-weight: 600;
    margin-bottom: 1rem;
}

.coach-btn-primary {
    background-color: var(--coach-primary);
    border-color: var(--coach-primary);
    color: var(--coach-light);
    padding: 0.75rem 1.5rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    text-decoration: none;
    display: inline-block;
}

.coach-btn-primary:hover {
    background-color: var(--coach-dark);
    border-color: var(--coach-dark);
    color: var(--coach-light);
    transform: translateY(-1px);
    box-shadow: var(--coach-shadow);
}

/* Responsive Design */
@media (max-width: 768px) {
    .coach-teams-page {
        padding: 1rem 0;
    }

    .coach-page-header {
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
    }

    .coach-team-card {
        margin-bottom: 1.5rem;
    }

    .coach-team-logo,
    .coach-team-logo-placeholder {
        width: 60px;
        height: 60px;
    }

    .coach-btn-manage,
    .coach-btn-players {
        width: 100%;
        margin-right: 0;
        margin-bottom: 0.5rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="coach-teams-page">
    <div class="container">
        <!-- Page Header -->
        <div class="coach-page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="coach-page-title h3">
                        <i class="fa fa-shield me-3"></i>
                        <?= lang('Common.my_clubs') ?>
                    </h1>
                    <p class="coach-page-subtitle"><?= lang('Common.view_manage_clubs') ?></p>
                </div>
                <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
                    <a href="<?= base_url('coach/dashboard') ?>" class="coach-btn-back">
                        <i class="fa fa-arrow-left me-2"></i><?= lang('Common.back') ?>
                    </a>
                </div>
            </div>
        </div>

        <?php if (!empty($teams)): ?>
            <div class="row">
                <?php foreach ($teams as $team): ?>
                    <div class="col-lg-6 col-xl-4 mb-4">
                        <div class="coach-team-card position-relative">
                            <div class="card-body p-4">
                                <div class="d-flex align-items-start mb-3">
                                    <div class="me-3">
                                        <?php if ($team['logo']): ?>
                                            <img src="<?= base_url($team['logo']) ?>" class="coach-team-logo">
                                        <?php else: ?>
                                            <img src="<?= base_url('public/img/exam-logo.webp') ?>" class="coach-team-logo">
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <div class="text-xs text-uppercase fw-bold text-muted mb-1"><?= lang('Common.organization') ?></div>
                                        <h6 class="coach-team-name"><?= $team['name'] ?></h6>
                                        <?php if ($team['name_en']): ?>
                                            <div class="text-muted small"><?= $team['name_en'] ?></div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="team-info mb-3">
                                    <?php if ($team['organize_name']): ?>
                                        <div class="coach-team-info">
                                            <i class="fa fa-building me-2"></i>
                                            <strong><?= lang('Common.owner_organization') ?>:</strong> <?= $team['organize_name'] ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($team['contact_name']): ?>
                                        <div class="coach-team-info">
                                            <i class="fa fa-user me-2"></i>
                                            <strong><?= lang('Common.contact_person') ?>:</strong> <?= $team['contact_name'] ?>
                                        </div>
                                    <?php endif; ?>

                                    <?php if ($team['contact_phone']): ?>
                                        <div class="coach-team-info">
                                            <i class="fa fa-phone me-2"></i>
                                            <strong><?= lang('Common.phone') ?>:</strong> <?= $team['contact_phone'] ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mt-3">
                                    <?php $coachTeamUrl = getAcademyUrlForTeam($team['id'], 'info') ?: base_url('team/info/' . $team['id']); ?>
                                    <a target="_blank" href="<?= $coachTeamUrl ?>"
                                        class="coach-btn-manage">
                                        <i class="fa fa-cogs me-1"></i><?= lang('Common.manage_club') ?>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="row">
                <div class="col-12">
                    <div class="coach-empty-state">
                        <i class="fa fa-futbol"></i>
                        <h4><?= lang('Common.no_clubs_managed') ?></h4>
                        <p class="mb-4"><?= lang('Common.not_assigned_clubs') ?></p>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>
<?= $this->endSection() ?>