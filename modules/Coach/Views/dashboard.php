<?php $this->extend('template/coach') ?>
<?= $this->section('title') ?>
<?= lang('Common.coach_dashboard') ?> - GLO Football
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
.coach-dashboard-page {
    background-color: var(--coach-gray-100);
    min-height: calc(100vh - 200px);
    padding: 2rem 0;
}

.coach-page-header {
    background-color: var(--coach-light);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    padding: 1rem;
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


.coach-welcome-content h2 {
    color: var(--coach-light);
    font-weight: 700;
    margin-bottom: 1rem;
}

.coach-welcome-badge {
    background-color: var(--coach-secondary);
    color: var(--coach-primary);
    padding: 0.5rem 1rem;
    border-radius: 2rem;
    font-weight: 600;
    display: inline-block;
    margin-bottom: 1rem;
}

.coach-profile-avatar {
    width: 80px;
    height: 80px;
    border-radius: var(--coach-border-radius);
    object-fit: cover;
    border: 3px solid var(--coach-secondary);
}

.coach-profile-placeholder {
    width: 80px;
    height: 80px;
    background-color: rgba(243, 184, 25, 0.2);
    border: 3px solid var(--coach-secondary);
    border-radius: var(--coach-border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--coach-light);
}

.coach-stats-card {
    background-color: var(--coach-light);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    padding: 1.25rem;
    margin-bottom: 1rem;
    box-shadow: var(--coach-shadow);
    transition: var(--coach-transition);
    position: relative;
    overflow: hidden;
}

.coach-stats-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--coach-shadow-lg);
}

.coach-stats-card::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    width: 4px;
    height: 100%;
}

.coach-stats-card.success::before { background-color: var(--coach-success); }
.coach-stats-card.warning::before { background-color: var(--coach-secondary); }
.coach-stats-card.info::before { background-color: var(--coach-info); }
.coach-stats-card.primary::before { background-color: var(--coach-primary); }

.coach-stats-icon {
    width: 50px;
    height: 50px;
    border-radius: var(--coach-border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 0.75rem;
    color: var(--coach-light);
}

.coach-stats-icon.success { background-color: var(--coach-success); }
.coach-stats-icon.warning { background-color: var(--coach-secondary); }
.coach-stats-icon.info { background-color: var(--coach-info); }
.coach-stats-icon.primary { background-color: var(--coach-primary); }

.coach-content-card {
    background-color: var(--coach-light);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    box-shadow: var(--coach-shadow);
    margin-bottom: 2rem;
    overflow: hidden;
}

.coach-content-header {
    background-color: var(--coach-primary);
    color: var(--coach-light);
    padding: 1.5rem 2rem;
    font-weight: 600;
}

.coach-team-item {
    background-color: var(--coach-light);
    border: 2px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    padding: 1.5rem;
    margin-bottom: 1.5rem;
    transition: var(--coach-transition);
}

.coach-team-item:hover {
    border-color: var(--coach-secondary);
    transform: translateY(-2px);
    box-shadow: var(--coach-shadow);
}

.coach-team-logo {
    width: 60px;
    height: 60px;
    border-radius: var(--coach-border-radius);
    object-fit: cover;
    border: 2px solid var(--coach-secondary);
}

.coach-team-logo-placeholder {
    width: 60px;
    height: 60px;
    background-color: var(--coach-gray-100);
    border: 2px solid var(--coach-secondary);
    border-radius: var(--coach-border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--coach-primary);
}

.coach-invitation-item {
    background-color: var(--coach-light);
    border: 2px solid var(--coach-secondary);
    border-radius: var(--coach-border-radius);
    padding: 1.5rem;
    margin-bottom: 1rem;
    color: var(--coach-primary);
}

.coach-quick-action {
    background-color: var(--coach-light);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    padding: 1rem 1.5rem;
    margin-bottom: 0.75rem;
    transition: var(--coach-transition);
    text-decoration: none;
    color: var(--coach-gray-700);
    display: flex;
    align-items: center;
}

.coach-quick-action:hover {
    background-color: var(--coach-gray-100);
    border-color: var(--coach-secondary);
    color: var(--coach-primary);
    text-decoration: none;
    transform: translateX(5px);
}

.coach-cert-item {
    background-color: var(--coach-gray-100);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    padding: 1rem;
    margin-bottom: 1rem;
}

.coach-empty-state {
    text-align: center;
    padding: 4rem 2rem;
    color: var(--coach-gray-500);
}

.coach-empty-state i {
    font-size: 4rem;
    color: var(--coach-secondary);
    margin-bottom: 2rem;
    opacity: 0.7;
}

.coach-empty-state h5 {
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
    border: none;
}

.coach-btn-primary:hover {
    background-color: var(--coach-dark);
    border-color: var(--coach-dark);
    color: var(--coach-light);
    transform: translateY(-1px);
    box-shadow: var(--coach-shadow);
}

.coach-btn-success {
    background-color: var(--coach-success);
    border-color: var(--coach-success);
    color: var(--coach-light);
    padding: 0.5rem 1rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    border: none;
}

.coach-btn-danger {
    background-color: var(--coach-danger);
    border-color: var(--coach-danger);
    color: var(--coach-light);
    padding: 0.5rem 1rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    border: none;
}

.coach-btn-outline {
    background-color: transparent;
    border: 2px solid var(--coach-primary);
    color: var(--coach-primary);
    padding: 0.75rem 1.5rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    text-decoration: none;
}

.coach-btn-outline:hover {
    background-color: var(--coach-primary);
    color: var(--coach-light);
    transform: translateY(-1px);
    box-shadow: var(--coach-shadow);
}

.coach-badge {
    background-color: var(--coach-secondary);
    color: var(--coach-primary);
    padding: 0.375rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.875rem;
    font-weight: 600;
}

.coach-badge-success {
    background-color: var(--coach-success);
    color: var(--coach-light);
}

/* Responsive Design */
@media (max-width: 768px) {
    .coach-dashboard-page {
        padding: 1rem 0;
    }

    .coach-page-header {
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
    }


    .coach-stats-card {
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .coach-content-card {
        margin-bottom: 1.5rem;
    }

    .coach-team-item {
        padding: 1rem;
    }

    .coach-profile-avatar,
    .coach-profile-placeholder {
        width: 60px;
        height: 60px;
    }

    .coach-team-logo,
    .coach-team-logo-placeholder {
        width: 50px;
        height: 50px;
    }
}

@media (max-width: 576px) {
    .coach-page-title {
        font-size: 1.5rem;
    }

    .coach-stats-icon {
        width: 45px;
        height: 45px;
        font-size: 1.25rem;
    }

    .coach-quick-action {
        padding: 0.75rem 1rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="coach-dashboard-page">
    <div class="container">
        <!-- Page Header -->
        <div class="coach-page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="coach-page-title h3">
                        <i class="fa fa-tachometer me-3"></i>
                        <?= lang('Common.coach_dashboard') ?>
                    </h1>
                    <p class="coach-page-subtitle"><?= lang('Common.manage_clubs_track_progress') ?></p>
                </div>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="row">
            <div class="col-xl-4 col-md-6 mb-3">
                <div class="coach-stats-card success">
                    <div class="text-center">
                        <div class="coach-stats-icon success mx-auto">
                            <i class="fa fa-futbol"></i>
                        </div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; margin-bottom: 0.5rem;"><?= lang('Common.clubs_managed') ?></div>
                        <div class="h4 mb-0 fw-bold" style="color: var(--coach-primary);"><?= count($teams) ?></div>
                        <div class="text-muted" style="font-size: 0.8rem; margin-top: 0.5rem;">
                            <i class="fa fa-arrow-up me-1" style="color: var(--coach-success);"></i>
                            <?= lang('Common.clubs_under_care') ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-3">
                <div class="coach-stats-card warning">
                    <div class="text-center">
                        <div class="coach-stats-icon warning mx-auto">
                            <i class="fa fa-envelope"></i>
                        </div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; margin-bottom: 0.5rem;"><?= lang('Common.pending_invitations') ?></div>
                        <div class="h4 mb-0 fw-bold" style="color: var(--coach-primary);"><?= count($invitations) ?></div>
                        <div class="text-muted" style="font-size: 0.8rem; margin-top: 0.5rem;">
                            <?php if (count($invitations) > 0): ?>
                                <i class="fa fa-clock me-1" style="color: var(--coach-secondary);"></i>
                                <?= lang('Common.awaiting_response') ?>
                            <?php else: ?>
                                <i class="fa fa-check me-1" style="color: var(--coach-success);"></i>
                                <?= lang('Common.no_pending_invitations') ?>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-4 col-md-6 mb-3">
                <div class="coach-stats-card info">
                    <div class="text-center">
                        <div class="coach-stats-icon info mx-auto">
                            <i class="fa fa-certificate"></i>
                        </div>
                        <div class="text-muted text-uppercase fw-bold" style="font-size: 0.75rem; margin-bottom: 0.5rem;"><?= lang('Common.certifications') ?></div>
                        <div class="h4 mb-0 fw-bold" style="color: var(--coach-primary);"><?= count($certifications) ?></div>
                        <div class="text-muted" style="font-size: 0.8rem; margin-top: 0.5rem;">
                            <i class="fa fa-trophy me-1" style="color: var(--coach-info);"></i>
                            <?= lang('Common.certifications_received') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Teams and Certifications -->
        <div class="row">
            <!-- Teams Section -->
            <div class="col-lg-8">
                <div class="coach-content-card">
                    <div class="coach-content-header">
                        <h5 class="m-0 d-flex align-items-center text-white">
                            <i class="fa fa-shield me-3"></i>
                            <?= lang('Common.clubs_managed') ?>
                            <span class="coach-badge ms-3"><?= count($teams) ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($teams)): ?>
                            <div class="row">
                                <?php foreach ($teams as $team): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="coach-team-item">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <?php if ($team['logo']): ?>
                                                    <img src="<?= base_url($team['logo']) ?>" class="coach-team-logo">
                                                <?php else: ?>
                                                    <img src="<?= base_url('public/img/exam-logo.webp') ?>" class="coach-team-logo">
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-4 fw-bold" style="color: var(--coach-primary);"><?= $team['name'] ?></h6>
                                                <?php $coachTeamUrl = getAcademyUrlForTeam($team['id'], 'info') ?: base_url('team/info/' . $team['id']); ?>
                                                <a target="_blank" href="<?= $coachTeamUrl ?>" class="coach-btn-primary btn-sm">
                                                    <i class="fa fa-cogs me-1"></i><?= lang('Common.manage_club') ?>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="coach-empty-state">
                                <i class="fa fa-futbol"></i>
                                <h5 class="mt-3 mb-2"><?= lang('Common.no_clubs_managed') ?></h5>
                                <p class="text-muted mb-4"><?= lang('Common.not_assigned_clubs') ?></p>
                                <a href="<?= base_url('coach/find-team') ?>" class="coach-btn-primary">
                                    <i class="fa fa-search me-2"></i>
                                    <?= lang('Common.find_team_to_join') ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Certifications Section -->
            <div class="col-lg-4">
                <div class="coach-content-card">
                    <div class="coach-content-header">
                        <h5 class="m-0 d-flex align-items-center text-white">
                            <i class="fa fa-certificate me-3"></i>
                            <?= lang('Common.certifications_awarded') ?>
                            <span class="coach-badge ms-3"><?= count($certifications) ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($certifications)): ?>
                            <?php foreach ($certifications as $cert): ?>
                                <div class="coach-cert-item">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fa fa-certificate" style="color: var(--coach-info); font-size: 1.5rem;"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1 fw-bold" style="color: var(--coach-primary); font-size: 0.9rem;">
                                                <?= isset($cert['cert_level_name']) ? $cert['cert_level_name'] : lang('Common.not_specified') ?>
                                            </h6>
                                            <p class="mb-1 text-muted small">
                                                <i class="fa fa-building me-1"></i>
                                                <?= isset($cert['organ_name']) ? $cert['organ_name'] : lang('Common.organization_not_specified') ?>
                                            </p>
                                            <?php if (isset($cert['issue_date'])): ?>
                                                <p class="mb-0 text-muted small">
                                                    <i class="fa fa-calendar me-1"></i>
                                                    <?= lang('Common.issued_date') ?>: <?= date('d/m/Y', strtotime($cert['issue_date'])) ?>
                                                </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="text-center py-5">
                                <h6 style="color: var(--coach-primary); font-weight: 600;"><?= lang('Common.no_certifications_data') ?></h6>
                                <p class="text-muted small mb-3"><?= lang('Common.no_certifications_added') ?></p>
                                <a href="<?= base_url('coach/profile#certifications') ?>" class="coach-btn-outline btn-sm">
                                    <i class="fa fa-plus me-1"></i><?= lang('Common.add_certification') ?>
                                </a>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
</div>

<?= $this->section('scripts') ?>
<script>
// Show flash messages with SweetAlert - Run after page loads
$(document).ready(function() {
    <?php if (session()->getFlashdata('success')): ?>
        const successMessage = '<?= addslashes(session()->getFlashdata('success')) ?>';
        const isNewUser = successMessage.includes('<?= lang('Common.register_btn') ?>');

        // ซ่อน Bootstrap alert ถ้ามี
        $('.coach-alert-success').hide();

        Swal.fire({
            icon: 'success',
            title: '<?= lang('Common.success_title') ?>',
            text: successMessage,
            confirmButtonText: '<?= lang('Common.ok_button') ?>',
            confirmButtonColor: '#28a745',
            timer: isNewUser ? undefined : 3000,
            timerProgressBar: !isNewUser,
            allowOutsideClick: false
        }).then((result) => {
            // ถ้าเป็นผู้ใช้ใหม่ แสดง modal แนะนำให้แก้ไขโปรไฟล์
            if (isNewUser) {
                Swal.fire({
                    icon: 'info',
                    title: '<?= lang('Common.welcome_coach_system') ?>',
                    html: '<p class="mb-3"><?= lang('Common.registration_success') ?></p>' +
                          '<p class="text-muted"><?= lang('Common.recommend_complete_profile') ?></p>',
                    confirmButtonText: '<?= lang('Common.edit_profile_now') ?>',
                    confirmButtonColor: '#007bff',
                    showCancelButton: true,
                    cancelButtonText: '<?= lang('Common.view_later') ?>',
                    cancelButtonColor: '#6c757d',
                    reverseButtons: true,
                    allowOutsideClick: false
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = '<?= base_url('coach/profile') ?>';
                    }
                });
            }
        });
    <?php elseif (session()->getFlashdata('error')): ?>
        // ซ่อน Bootstrap alert ถ้ามี
        $('.coach-alert-error').hide();

        Swal.fire({
            icon: 'error',
            title: '<?= lang('Common.error_occurred') ?>',
            text: '<?= addslashes(session()->getFlashdata('error')) ?>',
            confirmButtonText: '<?= lang('Common.ok_button') ?>',
            confirmButtonColor: '#dc3545'
        });
    <?php endif; ?>
});

// Enhanced invitation response with SweetAlert2
function respondToInvitation(invitationId, response) {
    const isAccepted = response === 'accepted';
    const title = isAccepted ? '<?= lang('Common.confirm_accept_invitation') ?>' : '<?= lang('Common.confirm_reject_invitation') ?>';
    const text = isAccepted ? '<?= lang('Common.want_accept_club_invitation') ?>' : '<?= lang('Common.want_reject_club_invitation') ?>';
    const icon = isAccepted ? 'question' : 'warning';
    const confirmButtonText = isAccepted ? '<?= lang('Common.accept') ?>' : '<?= lang('Common.reject') ?>';
    const confirmButtonColor = isAccepted ? '#28a745' : '#dc3545';

    Swal.fire({
        title: title,
        text: text,
        icon: icon,
        showCancelButton: true,
        confirmButtonColor: confirmButtonColor,
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmButtonText,
        cancelButtonText: '<?= lang('Common.cancel') ?>',
        reverseButtons: true
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '<?= lang('Common.processing') ?>',
                html: '<?= lang('Common.please_wait_moment') ?>',
                timerProgressBar: true,
                didOpen: () => {
                    Swal.showLoading();
                }
            });

            fetch('<?= base_url('coach/respond-invitation') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'invitation_id=' + invitationId + '&response=' + response
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    Swal.fire({
                        title: '<?= lang('Common.success_title') ?>',
                        text: data.message,
                        icon: 'success',
                        confirmButtonColor: '#28a745'
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '<?= lang('Common.error_occurred') ?>',
                        text: data.message,
                        icon: 'error',
                        confirmButtonColor: '#dc3545'
                    });
                }
            })
            .catch(error => {
                Swal.fire({
                    title: '<?= lang('Common.error_occurred') ?>',
                    text: '<?= lang('Common.connection_error') ?>',
                    icon: 'error',
                    confirmButtonColor: '#dc3545'
                });
            });
        }
    });
}

// Smooth scroll animation for cards
document.addEventListener('DOMContentLoaded', function() {
    // Add smooth hover effects
    const cards = document.querySelectorAll('.stats-card, .team-item, .invitation-item');
    cards.forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        });
    });

    // Add loading animation to quick actions
    const quickActions = document.querySelectorAll('.quick-action');
    quickActions.forEach(action => {
        action.addEventListener('click', function(e) {
            // Don't prevent default, just add visual feedback
            this.style.transform = 'translateX(10px)';
            setTimeout(() => {
                this.style.transform = '';
            }, 200);
        });
    });

    // Progressive enhancement for modern browsers
    if ('IntersectionObserver' in window) {
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, {
            threshold: 0.1
        });

        // Observe cards for scroll animations
        document.querySelectorAll('.stats-card, .content-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(20px)';
            card.style.transition = 'opacity 0.6s ease, transform 0.6s ease';
            observer.observe(card);
        });
    }
});
</script>
<?= $this->endSection() ?>