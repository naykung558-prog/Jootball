<?php $this->extend('template/coach') ?>
<?= $this->section('title') ?>
<?= lang('Common.club_invitations_title') ?>
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
.coach-invitations-page {
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

.coach-invitations-card {
    background-color: var(--coach-light);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    box-shadow: var(--coach-shadow);
    overflow: hidden;
}

.coach-card-header {
    background-color: var(--coach-primary);
    color: var(--coach-light);
    padding: 1.5rem 2rem;
    border-bottom: none;
    font-weight: 600;
}

.coach-invitation-item {
    background-color: var(--coach-light);
    border: 2px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    padding: 2rem;
    margin-bottom: 1.5rem;
    transition: var(--coach-transition);
    position: relative;
}

.coach-invitation-item:hover {
    border-color: var(--coach-secondary);
    transform: translateY(-2px);
    box-shadow: var(--coach-shadow-lg);
}

.coach-team-logo {
    width: 80px;
    height: 80px;
    border-radius: var(--coach-border-radius);
    object-fit: cover;
    border: 3px solid var(--coach-secondary);
    box-shadow: var(--coach-shadow);
}

.coach-team-logo-placeholder {
    width: 80px;
    height: 80px;
    border-radius: var(--coach-border-radius);
    background-color: var(--coach-gray-100);
    border: 3px solid var(--coach-secondary);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--coach-primary);
}

.coach-team-name {
    color: var(--coach-primary);
    font-weight: 700;
    margin-bottom: 1rem;
}

.coach-invitation-info {
    color: var(--coach-gray-600);
    margin-bottom: 0.75rem;
}

.coach-invitation-info i {
    color: var(--coach-secondary);
    width: 20px;
}

.coach-btn-accept {
    background-color: var(--coach-success);
    border-color: var(--coach-success);
    color: var(--coach-light);
    padding: 0.75rem 1.5rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    border: 2px solid var(--coach-success);
}

.coach-btn-accept:hover {
    background-color: var(--coach-light);
    color: var(--coach-success);
    transform: translateY(-1px);
    box-shadow: var(--coach-shadow);
}

.coach-btn-reject {
    background-color: var(--coach-danger);
    border-color: var(--coach-danger);
    color: var(--coach-light);
    padding: 0.75rem 1.5rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    border: 2px solid var(--coach-danger);
}

.coach-btn-reject:hover {
    background-color: var(--coach-light);
    color: var(--coach-danger);
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
}

.coach-empty-state i {
    font-size: 4rem;
    color: var(--coach-secondary);
    margin-bottom: 2rem;
    opacity: 0.7;
}

.coach-empty-state h3 {
    color: var(--coach-primary);
    font-weight: 600;
    margin-bottom: 1rem;
}

.coach-empty-state .coach-btn-primary {
    background-color: var(--coach-primary);
    border-color: var(--coach-primary);
    color: var(--coach-light);
    padding: 0.75rem 2rem;
    border-radius: var(--coach-border-radius);
    font-weight: 600;
    transition: var(--coach-transition);
    text-decoration: none;
    display: inline-block;
    margin-top: 1rem;
}

.coach-empty-state .coach-btn-primary:hover {
    background-color: var(--coach-dark);
    border-color: var(--coach-dark);
    transform: translateY(-1px);
    box-shadow: var(--coach-shadow);
}

.coach-badge {
    background-color: var(--coach-secondary);
    color: var(--coach-primary);
    font-size: 0.875rem;
    font-weight: 600;
    padding: 0.375rem 0.75rem;
    border-radius: 1rem;
}

.coach-invitation-status {
    position: absolute;
    top: 1rem;
    right: 1rem;
    background-color: var(--coach-secondary);
    color: var(--coach-primary);
    padding: 0.25rem 0.75rem;
    border-radius: 1rem;
    font-size: 0.75rem;
    font-weight: 600;
}

/* Responsive Design */
@media (max-width: 768px) {
    .coach-invitations-page {
        padding: 1rem 0;
    }

    .coach-page-header {
        padding: 1.5rem 1rem;
        margin-bottom: 1.5rem;
    }

    .coach-invitation-item {
        padding: 1.5rem 1rem;
    }

    .coach-team-logo,
    .coach-team-logo-placeholder {
        width: 60px;
        height: 60px;
    }

    .coach-btn-accept,
    .coach-btn-reject,
    .coach-btn-back {
        width: 100%;
        margin-bottom: 0.5rem;
    }
}

@media (max-width: 576px) {
    .coach-page-header {
        text-align: center;
    }

    .coach-invitation-item .d-flex {
        flex-direction: column;
        text-align: center;
    }

    .coach-invitation-item .me-4 {
        margin-right: 0 !important;
        margin-bottom: 1rem;
        display: flex;
        justify-content: center;
    }
}

/* Animation */
.coach-invitation-item {
    opacity: 0;
    transform: translateY(20px);
    transition: opacity 0.6s ease, transform 0.6s ease;
}

.coach-invitation-item.animate-in {
    opacity: 1;
    transform: translateY(0);
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="coach-invitations-page">
    <div class="container">
        <!-- Page Header -->
        <div class="coach-page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="coach-page-title h3">
                        <i class="fas fa-envelope-open me-3"></i>
                        <?= lang('Common.club_invitations') ?>
                    </h1>
                    <p class="coach-page-subtitle"><?= lang('Common.manage_club_invitations') ?></p>
                </div>
                <div class="col-md-4 text-md-end text-center mt-3 mt-md-0">
                    <a href="<?= base_url('coach/dashboard') ?>" class="coach-btn-back">
                        <i class="fas fa-arrow-left me-2"></i><?= lang('Common.back') ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Invitations List -->
        <div class="row">
            <div class="col-12">
                <div class="coach-invitations-card">
                    <div class="coach-card-header">
                        <h5 class="m-0 d-flex align-items-center text-white">
                            <i class="fas fa-handshake me-3"></i>
                            <?= lang('Common.pending_invitations') ?>
                            <span class="coach-badge ms-3"><?= count($invitations ?? []) ?></span>
                        </h5>
                    </div>
                    <div class="card-body p-4">
                        <?php if (!empty($invitations)): ?>
                            <div class="row">
                                <?php foreach ($invitations as $index => $invitation): ?>
                                <div class="col-lg-6 mb-4">
                                    <div class="coach-invitation-item" style="animation-delay: <?= $index * 0.1 ?>s;">
                                        <div class="coach-invitation-status">
                                            <?php
                                            $statusText = lang('Common.status_new');
                                            $daysSinceCreated = floor((time() - strtotime($invitation['created_at'])) / (24 * 60 * 60));
                                            if ($daysSinceCreated == 0) {
                                                $statusText = lang('Common.status_today');
                                            } else if ($daysSinceCreated == 1) {
                                                $statusText = lang('Common.status_yesterday');
                                            } else if ($daysSinceCreated <= 7) {
                                                $statusText = $daysSinceCreated . ' ' . lang('Common.days_ago');
                                            } else if ($daysSinceCreated <= 30) {
                                                $statusText = floor($daysSinceCreated / 7) . ' ' . lang('Common.weeks_ago');
                                            } else {
                                                $statusText = lang('Common.status_old');
                                            }
                                            echo $statusText;
                                            ?>
                                        </div>
                                        <div class="d-flex align-items-start">
                                            <div class="me-4">
                                                <?php if (!empty($invitation['logo'])): ?>
                                                    <img src="<?= base_url($invitation['logo']) ?>" class="coach-team-logo" alt="<?= $invitation['team_name'] ?>">
                                                <?php else: ?>
                                                    <div class="coach-team-logo-placeholder">
                                                        <i class="fas fa-futbol fa-2x"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="flex-grow-1">
                                                <h4 class="coach-team-name"><?= $invitation['team_name'] ?></h4>
                                                <div class="coach-invitation-info">
                                                    <i class="fas fa-calendar-alt me-2"></i>
                                                    <?= lang('Common.invited_date') ?>: <?= date('d/m/Y H:i', strtotime($invitation['created_at'])) ?>
                                                </div>
                                                <div class="coach-invitation-info">
                                                    <i class="fas fa-user-tie me-2"></i>
                                                    <?= lang('Common.position_coach_info') ?>
                                                </div>
                                                <?php if (!empty($invitation['message'])): ?>
                                                <div class="coach-invitation-info">
                                                    <i class="fas fa-comment me-2"></i>
                                                    <?= lang('Common.message') ?>: <?= htmlspecialchars($invitation['message']) ?>
                                                </div>
                                                <?php endif; ?>

                                                <div class="d-flex flex-column flex-sm-row gap-2">
                                                    <button type="button" class="btn coach-btn-accept"
                                                            onclick="respondToInvitation(<?= $invitation['id'] ?>, 'accepted')">
                                                        <i class="fas fa-check me-2"></i><?= lang('Common.accept') ?>
                                                    </button>
                                                    <button type="button" class="btn coach-btn-reject"
                                                            onclick="respondToInvitation(<?= $invitation['id'] ?>, 'rejected')">
                                                        <i class="fas fa-times me-2"></i><?= lang('Common.reject') ?>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="coach-empty-state">
                                <i class="fas fa-inbox"></i>
                                <h3><?= lang('Common.no_invitations') ?></h3>
                                <p class="mb-4"><?= lang('Common.no_invitations_description') ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
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
        reverseButtons: true,
        customClass: {
            popup: 'rounded-3',
            confirmButton: 'rounded-pill px-4',
            cancelButton: 'rounded-pill px-4'
        }
    }).then((result) => {
        if (result.isConfirmed) {
            // Show loading
            Swal.fire({
                title: '<?= lang('Common.processing') ?>',
                html: '<?= lang('Common.please_wait_moment') ?>',
                timerProgressBar: true,
                allowOutsideClick: false,
                allowEscapeKey: false,
                allowEnterKey: false,
                showConfirmButton: false,
                customClass: {
                    popup: 'rounded-3'
                },
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
                        confirmButtonColor: '#28a745',
                        confirmButtonText: '<?= lang('Common.ok_button') ?>',
                        customClass: {
                            popup: 'rounded-3',
                            confirmButton: 'rounded-pill px-4'
                        }
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        title: '<?= lang('Common.error_occurred') ?>',
                        text: data.message || '<?= lang('Common.unexpected_error') ?>',
                        icon: 'error',
                        confirmButtonColor: '#dc3545',
                        confirmButtonText: '<?= lang('Common.retry') ?>',
                        customClass: {
                            popup: 'rounded-3',
                            confirmButton: 'rounded-pill px-4'
                        }
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    title: '<?= lang('Common.error_occurred') ?>',
                    text: '<?= lang('Common.connection_error') ?>',
                    icon: 'error',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: '<?= lang('Common.retry') ?>',
                    customClass: {
                        popup: 'rounded-3',
                        confirmButton: 'rounded-pill px-4'
                    }
                });
            });
        }
    });
}

// Initialize animations and interactions
document.addEventListener('DOMContentLoaded', function() {
    // Animate invitation items on load
    const invitationItems = document.querySelectorAll('.coach-invitation-item');

    if (invitationItems.length > 0) {
        // Progressive enhancement for modern browsers
        if ('IntersectionObserver' in window) {
            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('animate-in');
                        observer.unobserve(entry.target); // Stop observing once animated
                    }
                });
            }, {
                threshold: 0.1,
                rootMargin: '50px'
            });

            // Observe all invitation items
            invitationItems.forEach((item, index) => {
                observer.observe(item);
            });
        } else {
            // Fallback for older browsers
            invitationItems.forEach((item, index) => {
                setTimeout(() => {
                    item.classList.add('animate-in');
                }, index * 100);
            });
        }
    } else {
        // Animate empty state if no invitations
        const emptyState = document.querySelector('.coach-empty-state');
        if (emptyState) {
            setTimeout(() => {
                emptyState.style.opacity = '1';
                emptyState.style.transform = 'translateY(0)';
            }, 300);
        }
    }

    // Add hover effects enhancement
    invitationItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.borderColor = 'var(--coach-secondary)';
        });

        item.addEventListener('mouseleave', function() {
            this.style.borderColor = 'var(--coach-gray-200)';
        });
    });
});
</script>
<?= $this->endSection() ?>