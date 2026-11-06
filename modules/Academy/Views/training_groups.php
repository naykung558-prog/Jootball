<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-layer-group me-2"></i><?= esc($title) ?>
                </h4>
                <?php if (!empty($academy['name'])): ?>
                    <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($perms['perm_manage_training_group'])): ?>
            <div>
                <a href="<?= base_url('academy/training-group/manage/' . esc($academy['id'])) ?>" class="btn btn-primary rounded-pill px-4">
                    <i class="fas fa-plus-circle me-2"></i><?= lang('Common.training_group_create') ?>
                </a>
            </div>
            <?php endif; ?>
        </div>

        <div class="card-body p-4">
            <?php if (!empty($groups)): ?>
                <div class="row g-3">
                    <?php foreach ($groups as $group): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card h-100 border shadow-sm rounded-4 overflow-hidden hover-shadow">
                            <div class="card-body p-4">
                                <!-- Group Name & Age Range -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h5 class="fw-bold text-dark mb-0"><?= esc($group['name']) ?></h5>
                                    <?php if (!empty($group['age_range'])): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-1">
                                            <?= esc($group['age_range']) ?>
                                        </span>
                                    <?php endif; ?>
                                </div>

                                <!-- Description -->
                                <?php if (!empty($group['description'])): ?>
                                    <p class="text-muted small mb-3"><?= esc($group['description']) ?></p>
                                <?php endif; ?>

                                <!-- Player Count -->
                                <div class="d-flex align-items-center mb-2">
                                    <i class="fas fa-users text-primary me-2"></i>
                                    <span class="text-muted small">
                                        <?= lang('Common.players') ?>:
                                        <strong><?= isset($group['player_count']) ? (int)$group['player_count'] : 0 ?></strong>
                                        <?php if (!empty($group['max_players'])): ?>
                                            / <?= (int)$group['max_players'] ?>
                                        <?php endif; ?>
                                        <?= lang('Common.persons') ?>
                                    </span>
                                </div>

                                <!-- Coach Count & Names -->
                                <div class="d-flex align-items-start mb-2">
                                    <i class="fas fa-chalkboard-teacher text-primary me-2 mt-1"></i>
                                    <div>
                                        <span class="text-muted small">
                                            <?= lang('Common.coaches') ?>:
                                            <strong><?= isset($group['coach_count']) ? (int)$group['coach_count'] : 0 ?></strong>
                                        </span>
                                        <?php if (!empty($group['coaches'])): ?>
                                            <div class="mt-1">
                                                <?php foreach ($group['coaches'] as $coach): ?>
                                                    <span class="badge bg-light text-dark border me-1 mb-1">
                                                        <?= esc($coach['name']) ?>
                                                        <?php if (!empty($coach['role']) && $coach['role'] === 'head'): ?>
                                                            <span class="badge bg-warning text-dark ms-1"><?= lang('Common.training_head_coach') ?></span>
                                                        <?php elseif (!empty($coach['role']) && $coach['role'] === 'assistant'): ?>
                                                            <span class="badge bg-info text-dark ms-1"><?= lang('Common.training_assistant_coach') ?></span>
                                                        <?php endif; ?>
                                                    </span>
                                                <?php endforeach; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <!-- Schedule Info -->
                                <?php if (!empty($group['schedule_info'])): ?>
                                    <div class="d-flex align-items-start mb-0">
                                        <i class="fas fa-calendar-alt text-primary me-2 mt-1"></i>
                                        <span class="text-muted small"><?= esc($group['schedule_info']) ?></span>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Action Buttons -->
                            <?php if (!empty($perms['perm_manage_training_group'])): ?>
                            <div class="card-footer bg-white border-top py-2 px-3">
                                <div class="d-flex gap-2 justify-content-end">
                                    <a href="<?= base_url('academy/training-group/manage/' . esc($academy['id']) . '/' . esc($group['id'])) ?>"
                                       class="btn btn-outline-primary btn-sm rounded-pill px-3"
                                       title="<?= lang('Common.training_manage_group') ?>">
                                        <i class="fas fa-cog me-1"></i><?= lang('Common.training_manage_group') ?>
                                    </a>
                                    <a href="<?= base_url('academy/training-sessions/' . esc($academy['id']) . '?group_id=' . esc($group['id'])) ?>"
                                       class="btn btn-outline-success btn-sm rounded-pill px-3"
                                       title="<?= lang('Common.training_view_sessions') ?>">
                                        <i class="fas fa-clipboard-list me-1"></i><?= lang('Common.training_view_sessions') ?>
                                    </a>
                                </div>
                            </div>
                            <?php endif; ?>
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
                    <h5 class="fw-bold text-muted mb-2"><?= lang('Common.training_no_groups') ?></h5>
                    <p class="text-muted small mb-4"><?= lang('Common.training_no_groups_desc') ?></p>
                    <?php if (!empty($perms['perm_manage_training_group'])): ?>
                        <a href="<?= base_url('academy/training-group/manage/' . esc($academy['id'])) ?>"
                           class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-plus-circle me-2"></i><?= lang('Common.training_create_first_group') ?>
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>
