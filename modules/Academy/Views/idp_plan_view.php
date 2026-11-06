<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Page Header -->
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 mt-3">
        <div>
            <h4 class="fw-bold text-dark mb-1">
                <i class="fas fa-file-alt me-2 text-primary"></i><?= esc($plan['title'] ?? lang('Common.idp_plan_view')) ?>
            </h4>
            <p class="text-muted mb-0">
                <i class="fas fa-user me-1"></i><?= esc($plan['player_name'] ?? '') ?>
                <?php if (!empty($plan['player_nickname'])): ?> (<?= esc($plan['player_nickname']) ?>)<?php endif; ?>
                <?php if (!empty($plan['coach_name'])): ?>
                    &nbsp;|&nbsp;<i class="fas fa-user-tie me-1"></i><?= esc($plan['coach_name']) ?>
                <?php endif; ?>
                &nbsp;|&nbsp;<i class="fas fa-calendar me-1"></i><?= esc($plan['start_date'] ?? '') ?> ~ <?= esc($plan['end_date'] ?? '') ?>
            </p>
        </div>
        <div class="d-flex gap-2 mt-2 mt-md-0">
            <?php
            $statusColors = ['draft' => 'secondary', 'active' => 'success', 'completed' => 'purple', 'cancelled' => 'danger'];
            $statusColor = $statusColors[$plan['status'] ?? 'draft'] ?? 'secondary';
            $statusKey = 'idp_status_' . ($plan['status'] ?? 'draft');
            ?>
            <span class="badge bg-<?= $statusColor ?> rounded-pill fs-6 px-3 py-2">
                <?= lang('Common.' . $statusKey) ?>
            </span>
            <?php if (!empty($perms['perm_manage_idp'])): ?>
                <a href="<?= base_url('academy/idp/plan/manage/' . $academy['id'] . '/' . $plan['player_id'] . '/' . $plan['id']) ?>"
                   class="btn btn-outline-primary btn-sm">
                    <i class="fas fa-edit me-1"></i><?= lang('Common.edit') ?>
                </a>
                <a href="<?= base_url('academy/idp/assessment/' . $academy['id'] . '/' . $plan['id']) ?>"
                   class="btn btn-outline-info btn-sm">
                    <i class="fas fa-star me-1"></i><?= lang('Common.idp_assessment_title') ?>
                </a>
                <button class="btn btn-outline-danger btn-sm" onclick="deletePlan()">
                    <i class="fas fa-trash me-1"></i><?= lang('Common.delete') ?>
                </button>
            <?php endif; ?>
            <a href="<?= base_url('academy/idp/' . $academy['id']) ?>" class="btn btn-outline-secondary btn-sm">
                <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
            </a>
        </div>
    </div>

    <div class="row g-4">
        <!-- LEFT: Radar Chart + Summary -->
        <div class="col-lg-5">
            <!-- Summary Stats -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <div class="row text-center g-3">
                        <div class="col-4">
                            <h3 class="fw-bold mb-0 text-primary"><?= $summary['goal_count'] ?? 0 ?></h3>
                            <small class="text-muted"><?= lang('Common.idp_goal_count') ?></small>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold mb-0 text-success"><?= $summary['achieved_count'] ?? 0 ?></h3>
                            <small class="text-muted"><?= lang('Common.idp_achieved_count') ?></small>
                        </div>
                        <div class="col-4">
                            <h3 class="fw-bold mb-0 text-warning"><?= $summary['avg_achievement'] ?? 0 ?>%</h3>
                            <small class="text-muted"><?= lang('Common.idp_avg_achievement') ?></small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Radar Chart -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-spider me-2"></i><?= lang('Common.idp_radar_chart') ?></h6>
                </div>
                <div class="card-body p-3">
                    <canvas id="radarChart" height="300"></canvas>
                    <?php if (empty($compareData) || array_sum(array_column($compareData, 'current_score')) == 0): ?>
                        <p class="text-center text-muted mt-2"><small><?= lang('Common.no_data') ?></small></p>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Overall Notes -->
            <?php if (!empty($plan['overall_notes'])): ?>
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body p-4">
                    <h6 class="fw-bold mb-2"><i class="fas fa-sticky-note me-2"></i><?= lang('Common.idp_plan_notes') ?></h6>
                    <p class="text-muted mb-0"><?= nl2br(esc($plan['overall_notes'])) ?></p>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- RIGHT: Goals + Feedback -->
        <div class="col-lg-7">
            <!-- Goals by Dimension -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-bullseye me-2"></i><?= lang('Common.idp_goals') ?></h6>
                    <?php if (!empty($perms['perm_manage_idp'])): ?>
                        <button class="btn btn-primary btn-sm" onclick="showGoalModal()">
                            <i class="fas fa-plus me-1"></i><?= lang('Common.idp_goal_add') ?>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($goals)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-bullseye fa-3x mb-3 opacity-25"></i>
                            <p><?= lang('Common.idp_goal_no_goals') ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($goals as $dimGroup): ?>
                            <div class="mb-4">
                                <h6 class="fw-bold mb-3" style="color: <?= esc($dimGroup['dimension_color']) ?>">
                                    <i class="fas <?= esc($dimGroup['dimension_icon']) ?> me-2"></i>
                                    <?= esc($dimGroup['dimension_name']) ?>
                                </h6>
                                <?php foreach ($dimGroup['goals'] as $goal): ?>
                                    <div class="border rounded-3 p-3 mb-2 bg-light">
                                        <div class="d-flex justify-content-between align-items-start">
                                            <div class="flex-grow-1">
                                                <div class="d-flex align-items-center mb-1">
                                                    <?php
                                                    $priorityColors = ['high' => 'danger', 'medium' => 'warning', 'low' => 'info'];
                                                    $pColor = $priorityColors[$goal['priority'] ?? 'medium'] ?? 'warning';
                                                    ?>
                                                    <span class="badge bg-<?= $pColor ?> me-2"><?= lang('Common.idp_goal_priority_' . ($goal['priority'] ?? 'medium')) ?></span>
                                                    <strong><?= esc($goal['title']) ?></strong>
                                                    <?php
                                                    $goalStatusColors = ['not_started' => 'secondary', 'in_progress' => 'primary', 'achieved' => 'success', 'partially_achieved' => 'warning', 'not_achieved' => 'danger'];
                                                    $gsColor = $goalStatusColors[$goal['status'] ?? 'not_started'] ?? 'secondary';
                                                    $gsKey = 'idp_goal_status_' . str_replace('_achieved', '', $goal['status'] ?? 'not_started');
                                                    if ($goal['status'] === 'partially_achieved') $gsKey = 'idp_goal_status_partially';
                                                    if ($goal['status'] === 'not_achieved') $gsKey = 'idp_goal_status_not_achieved';
                                                    if ($goal['status'] === 'not_started') $gsKey = 'idp_goal_status_not_started';
                                                    if ($goal['status'] === 'in_progress') $gsKey = 'idp_goal_status_in_progress';
                                                    if ($goal['status'] === 'achieved') $gsKey = 'idp_goal_status_achieved';
                                                    ?>
                                                    <span class="badge bg-<?= $gsColor ?> ms-2"><?= lang('Common.' . $gsKey) ?></span>
                                                </div>
                                                <?php if (!empty($goal['description'])): ?>
                                                    <small class="text-muted"><?= esc($goal['description']) ?></small><br>
                                                <?php endif; ?>
                                                <div class="mt-2">
                                                    <?php if (!empty($goal['baseline_value'])): ?>
                                                        <small class="me-3"><span class="text-muted"><?= lang('Common.idp_goal_baseline_value') ?>:</span> <strong><?= esc($goal['baseline_value']) ?></strong></small>
                                                    <?php endif; ?>
                                                    <?php if (!empty($goal['target_value'])): ?>
                                                        <small class="me-3"><span class="text-muted"><?= lang('Common.idp_goal_target_value') ?>:</span> <strong><?= esc($goal['target_value']) ?></strong></small>
                                                    <?php endif; ?>
                                                    <?php if (!empty($goal['current_value'])): ?>
                                                        <small><span class="text-muted"><?= lang('Common.idp_goal_current_value') ?>:</span> <strong class="text-primary"><?= esc($goal['current_value']) ?></strong></small>
                                                    <?php endif; ?>
                                                </div>
                                                <!-- Progress Bar -->
                                                <div class="mt-2">
                                                    <div class="progress" style="height: 8px;">
                                                        <?php $pct = (int)($goal['achievement_percent'] ?? 0); ?>
                                                        <div class="progress-bar bg-<?= $pct >= 100 ? 'success' : ($pct >= 50 ? 'primary' : 'warning') ?>"
                                                             style="width: <?= $pct ?>%"></div>
                                                    </div>
                                                    <small class="text-muted"><?= $pct ?>%</small>
                                                </div>
                                            </div>
                                            <?php if (!empty($perms['perm_manage_idp'])): ?>
                                            <div class="ms-2 d-flex gap-1">
                                                <button class="btn btn-sm btn-outline-success" onclick="showProgressModal(<?= $goal['id'] ?>, '<?= esc($goal['title']) ?>')" title="<?= lang('Common.idp_progress_add') ?>">
                                                    <i class="fas fa-chart-line"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-danger" onclick="deleteGoal(<?= $goal['id'] ?>)" title="<?= lang('Common.delete') ?>">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Feedback Timeline -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="mb-0 fw-bold"><i class="fas fa-comments me-2"></i><?= lang('Common.idp_feedback') ?></h6>
                    <?php if (!empty($perms['perm_manage_idp'])): ?>
                        <button class="btn btn-info btn-sm text-white" onclick="showFeedbackModal()">
                            <i class="fas fa-plus me-1"></i><?= lang('Common.idp_feedback_add') ?>
                        </button>
                    <?php endif; ?>
                </div>
                <div class="card-body p-4">
                    <?php if (empty($feedbacks)): ?>
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-comment-slash fa-3x mb-3 opacity-25"></i>
                            <p><?= lang('Common.idp_no_feedback') ?></p>
                        </div>
                    <?php else: ?>
                        <?php foreach ($feedbacks as $fb): ?>
                            <div class="border-start border-3 ps-3 mb-3" style="border-color: #0d6efd !important;">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <span class="badge bg-primary mb-1"><?= lang('Common.idp_feedback_type_' . str_replace('coach_', '', str_replace('player_', '', $fb['feedback_type'] ?? 'observation'))) ?></span>
                                        <small class="text-muted ms-2"><i class="fas fa-calendar me-1"></i><?= esc($fb['feedback_date']) ?></small>
                                        <?php if (!empty($fb['created_by_name'])): ?>
                                            <small class="text-muted ms-2"><i class="fas fa-user me-1"></i><?= esc($fb['created_by_name']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($perms['perm_manage_idp'])): ?>
                                        <button class="btn btn-sm btn-outline-danger" onclick="deleteFeedback(<?= $fb['id'] ?>)">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    <?php endif; ?>
                                </div>
                                <p class="mb-1 mt-1"><?= nl2br(esc($fb['content'])) ?></p>
                                <?php if (!empty($fb['strengths'])): ?>
                                    <small class="text-success"><i class="fas fa-thumbs-up me-1"></i><strong><?= lang('Common.idp_feedback_strengths') ?>:</strong> <?= esc($fb['strengths']) ?></small><br>
                                <?php endif; ?>
                                <?php if (!empty($fb['areas_to_improve'])): ?>
                                    <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i><strong><?= lang('Common.idp_feedback_areas_to_improve') ?>:</strong> <?= esc($fb['areas_to_improve']) ?></small><br>
                                <?php endif; ?>
                                <?php if (!empty($fb['action_items'])): ?>
                                    <small class="text-info"><i class="fas fa-tasks me-1"></i><strong><?= lang('Common.idp_feedback_action_items') ?>:</strong> <?= esc($fb['action_items']) ?></small>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Goal Modal -->
<div class="modal fade" id="goalModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-bullseye me-2"></i><?= lang('Common.idp_goal_add') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.idp_dimension') ?> <span class="text-danger">*</span></label>
                        <select id="goal_dimension_id" class="form-select" onchange="loadIndicators(this.value)">
                            <option value=""><?= lang('Common.idp_select_dimension') ?></option>
                            <?php foreach ($dimensions as $dim): ?>
                                <option value="<?= $dim['id'] ?>"><?= esc($dim['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.idp_indicator') ?></label>
                        <select id="goal_indicator_id" class="form-select">
                            <option value=""><?= lang('Common.idp_select_indicator') ?></option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold"><?= lang('Common.idp_goal_title') ?> <span class="text-danger">*</span></label>
                        <input type="text" id="goal_title" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold"><?= lang('Common.idp_goal_description') ?></label>
                        <textarea id="goal_description" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_goal_baseline_value') ?></label>
                        <input type="text" id="goal_baseline" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_goal_target_value') ?></label>
                        <input type="text" id="goal_target" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_goal_target_date') ?></label>
                        <input type="date" id="goal_target_date" class="form-control" value="<?= $plan['end_date'] ?? '' ?>">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_goal_type') ?></label>
                        <select id="goal_type" class="form-select">
                            <option value="short_term"><?= lang('Common.idp_goal_type_short') ?></option>
                            <option value="medium_term"><?= lang('Common.idp_goal_type_medium') ?></option>
                            <option value="long_term"><?= lang('Common.idp_goal_type_long') ?></option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_goal_priority') ?></label>
                        <select id="goal_priority" class="form-select">
                            <option value="high"><?= lang('Common.idp_goal_priority_high') ?></option>
                            <option value="medium" selected><?= lang('Common.idp_goal_priority_medium') ?></option>
                            <option value="low"><?= lang('Common.idp_goal_priority_low') ?></option>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_goal_training_recommendation') ?></label>
                        <input type="text" id="goal_recommendation" class="form-control">
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button class="btn btn-primary" onclick="saveGoal()"><i class="fas fa-save me-1"></i><?= lang('Common.save') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Progress Modal -->
<div class="modal fade" id="progressModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-chart-line me-2"></i><?= lang('Common.idp_progress_add') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="progress_goal_id">
                <p class="text-muted mb-3" id="progress_goal_title"></p>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.idp_progress_value') ?></label>
                        <input type="text" id="progress_value" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.idp_progress_percent') ?> (%)</label>
                        <input type="number" id="progress_percent" class="form-control" min="0" max="100">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.idp_progress_date') ?></label>
                        <input type="date" id="progress_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold"><?= lang('Common.idp_progress_notes') ?></label>
                        <textarea id="progress_notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button class="btn btn-success" onclick="saveProgress()"><i class="fas fa-save me-1"></i><?= lang('Common.save') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Feedback Modal -->
<div class="modal fade" id="feedbackModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-comments me-2"></i><?= lang('Common.idp_feedback_add') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.idp_feedback_type') ?></label>
                        <select id="feedback_type" class="form-select">
                            <option value="coach_observation"><?= lang('Common.idp_feedback_type_observation') ?></option>
                            <option value="coach_recommendation"><?= lang('Common.idp_feedback_type_recommendation') ?></option>
                            <option value="player_reflection"><?= lang('Common.idp_feedback_type_reflection') ?></option>
                            <option value="review_meeting"><?= lang('Common.idp_feedback_type_review') ?></option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold"><?= lang('Common.idp_feedback_date') ?></label>
                        <input type="date" id="feedback_date" class="form-control" value="<?= date('Y-m-d') ?>">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold"><?= lang('Common.idp_feedback_content') ?> <span class="text-danger">*</span></label>
                        <textarea id="feedback_content" class="form-control" rows="3" required></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_feedback_strengths') ?></label>
                        <textarea id="feedback_strengths" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_feedback_areas_to_improve') ?></label>
                        <textarea id="feedback_improve" class="form-control" rows="2"></textarea>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold"><?= lang('Common.idp_feedback_action_items') ?></label>
                        <textarea id="feedback_actions" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button class="btn btn-info text-white" onclick="saveFeedback()"><i class="fas fa-save me-1"></i><?= lang('Common.save') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script>
const BASE_URL = '<?= base_url() ?>';
const PLAN_ID = <?= $plan['id'] ?>;
const ACADEMY_ID = <?= $academy['id'] ?>;

// Indicators data for dynamic dropdown
const indicatorsData = <?= json_encode($dimensions) ?>;
// Indicators grouped by dimension (passed from controller)
<?php
$indicatorsByDim = [];
foreach ($allIndicators ?? [] as $dimData) {
    $indicatorsByDim[$dimData['id']] = $dimData['indicators'] ?? [];
}
?>
const allIndicators = <?= json_encode($indicatorsByDim) ?>;

// Radar Chart
const compareData = <?= json_encode($compareData ?? []) ?>;
if (compareData.length > 0) {
    const labels = compareData.map(d => d.dimension_name);
    const baselineScores = compareData.map(d => d.baseline_score);
    const currentScores = compareData.map(d => d.current_score);
    const colors = compareData.map(d => d.color);

    new Chart(document.getElementById('radarChart'), {
        type: 'radar',
        data: {
            labels: labels,
            datasets: [
                {
                    label: '<?= lang('Common.idp_assessment_baseline') ?>',
                    data: baselineScores,
                    borderColor: 'rgba(108,117,125,0.8)',
                    backgroundColor: 'rgba(108,117,125,0.1)',
                    borderWidth: 2,
                    borderDash: [5, 5],
                    pointRadius: 3,
                },
                {
                    label: '<?= lang('Common.idp_goal_current_value') ?>',
                    data: currentScores,
                    borderColor: 'rgba(13,110,253,0.8)',
                    backgroundColor: 'rgba(13,110,253,0.15)',
                    borderWidth: 2,
                    pointRadius: 4,
                }
            ]
        },
        options: {
            responsive: true,
            plugins: { legend: { position: 'bottom' } },
            scales: {
                r: {
                    beginAtZero: true,
                    max: 5,
                    ticks: { stepSize: 1 }
                }
            }
        }
    });
}

// --- Modal Functions ---
function showGoalModal() {
    document.getElementById('goal_title').value = '';
    document.getElementById('goal_description').value = '';
    document.getElementById('goal_baseline').value = '';
    document.getElementById('goal_target').value = '';
    document.getElementById('goal_recommendation').value = '';
    new bootstrap.Modal(document.getElementById('goalModal')).show();
}

function loadIndicators(dimId) {
    const select = document.getElementById('goal_indicator_id');
    select.innerHTML = '<option value=""><?= lang('Common.idp_select_indicator') ?></option>';
    if (dimId && allIndicators[dimId]) {
        allIndicators[dimId].forEach(ind => {
            select.innerHTML += `<option value="${ind.id}">${ind.name}</option>`;
        });
    }
}

function saveGoal() {
    const data = {
        plan_id: PLAN_ID,
        dimension_id: document.getElementById('goal_dimension_id').value,
        indicator_id: document.getElementById('goal_indicator_id').value,
        title: document.getElementById('goal_title').value,
        description: document.getElementById('goal_description').value,
        baseline_value: document.getElementById('goal_baseline').value,
        target_value: document.getElementById('goal_target').value,
        target_date: document.getElementById('goal_target_date').value,
        goal_type: document.getElementById('goal_type').value,
        priority: document.getElementById('goal_priority').value,
        training_recommendation: document.getElementById('goal_recommendation').value,
    };
    if (!data.dimension_id || !data.title) {
        Swal.fire('Error', '<?= lang('Common.idp_select_dimension') ?>', 'warning');
        return;
    }
    fetch(BASE_URL + '/academy/idp/goal/save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire({icon: 'success', title: res.message, timer: 1500, showConfirmButton: false})
            .then(() => location.reload());
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
}

function deleteGoal(goalId) {
    Swal.fire({
        title: '<?= lang('Common.idp_confirm_delete_goal') ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: '<?= lang('Common.delete') ?>',
        cancelButtonText: '<?= lang('Common.cancel') ?>'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(BASE_URL + '/academy/idp/goal/delete', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({goal_id: goalId})
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') location.reload();
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}

function showProgressModal(goalId, goalTitle) {
    document.getElementById('progress_goal_id').value = goalId;
    document.getElementById('progress_goal_title').textContent = goalTitle;
    document.getElementById('progress_value').value = '';
    document.getElementById('progress_percent').value = '';
    document.getElementById('progress_notes').value = '';
    new bootstrap.Modal(document.getElementById('progressModal')).show();
}

function saveProgress() {
    const value = document.getElementById('progress_value').value.trim();
    const percent = document.getElementById('progress_percent').value.trim();
    const recordedDate = document.getElementById('progress_date').value;

    // Validate: at least value or percent must be filled
    if (!value && !percent) {
        Swal.fire('<?= lang('Common.warning') ?? "Warning" ?>', '<?= lang('Common.idp_progress_fill_required') ?? "Please fill in value or percentage" ?>', 'warning');
        return;
    }

    const data = {
        goal_id: document.getElementById('progress_goal_id').value,
        value: value || null,
        achievement_percent: percent ? parseInt(percent) : null,
        recorded_date: recordedDate || '<?= date('Y-m-d') ?>',
        notes: document.getElementById('progress_notes').value.trim() || null,
    };
    fetch(BASE_URL + '/academy/idp/progress/save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(function(r) {
        if (!r.ok) throw new Error('HTTP ' + r.status);
        return r.json();
    })
    .then(function(res) {
        if (res.status === 'success') {
            Swal.fire({icon: 'success', title: res.message, timer: 1500, showConfirmButton: false})
            .then(function() { location.reload(); });
        } else {
            Swal.fire('Error', res.message || '<?= lang('Common.save_failed') ?>', 'error');
        }
    })
    .catch(function(err) {
        console.error('saveProgress error:', err);
        Swal.fire('Error', '<?= lang('Common.save_failed') ?> (' + err.message + ')', 'error');
    });
}

function showFeedbackModal() {
    document.getElementById('feedback_content').value = '';
    document.getElementById('feedback_strengths').value = '';
    document.getElementById('feedback_improve').value = '';
    document.getElementById('feedback_actions').value = '';
    new bootstrap.Modal(document.getElementById('feedbackModal')).show();
}

function saveFeedback() {
    const data = {
        plan_id: PLAN_ID,
        feedback_type: document.getElementById('feedback_type').value,
        feedback_date: document.getElementById('feedback_date').value,
        content: document.getElementById('feedback_content').value,
        strengths: document.getElementById('feedback_strengths').value,
        areas_to_improve: document.getElementById('feedback_improve').value,
        action_items: document.getElementById('feedback_actions').value,
    };
    if (!data.content) {
        Swal.fire('Error', '<?= lang('Common.idp_feedback_content') ?>', 'warning');
        return;
    }
    fetch(BASE_URL + '/academy/idp/feedback/save', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(res => {
        if (res.status === 'success') {
            Swal.fire({icon: 'success', title: res.message, timer: 1500, showConfirmButton: false})
            .then(() => location.reload());
        } else {
            Swal.fire('Error', res.message, 'error');
        }
    });
}

function deleteFeedback(id) {
    Swal.fire({
        title: '<?= lang('Common.idp_confirm_delete_feedback') ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: '<?= lang('Common.delete') ?>',
        cancelButtonText: '<?= lang('Common.cancel') ?>'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(BASE_URL + '/academy/idp/feedback/delete', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({feedback_id: id})
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') location.reload();
                else Swal.fire('Error', res.message, 'error');
            });
        }
    });
}

function deletePlan() {
    Swal.fire({
        title: '<?= lang('Common.idp_confirm_delete_plan') ?>',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#dc3545',
        confirmButtonText: '<?= lang('Common.delete') ?>',
        cancelButtonText: '<?= lang('Common.cancel') ?>'
    }).then(result => {
        if (result.isConfirmed) {
            fetch(BASE_URL + '/academy/idp/plan/delete', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({plan_id: PLAN_ID, academy_id: ACADEMY_ID})
            })
            .then(r => r.json())
            .then(res => {
                if (res.status === 'success') {
                    window.location.href = BASE_URL + '/academy/idp/' + ACADEMY_ID;
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            });
        }
    });
}
</script>

<style>
.bg-purple { background-color: #6f42c1 !important; }
</style>
<?php $this->endSection(); ?>
