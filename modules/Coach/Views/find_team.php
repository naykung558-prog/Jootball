<?php $this->extend('template/coach') ?>
<?= $this->section('title') ?>
<?= lang('Common.find_team') ?> - GLO Football
<?= $this->endSection() ?>

<?= $this->section('style') ?>
<style>
.find-team-page {
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

.search-card {
    background-color: var(--coach-light);
    border: 1px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    box-shadow: var(--coach-shadow);
    overflow: hidden;
}

.search-card-header {
    background-color: var(--coach-primary);
    color: var(--coach-light);
    padding: 1.5rem 2rem;
    font-weight: 600;
}

.search-card-body {
    padding: 2rem;
}

.search-input-group {
    position: relative;
}

.search-input-group input {
    padding-right: 50px;
    border-radius: var(--coach-border-radius);
    border: 2px solid var(--coach-gray-200);
    padding: 1rem 1.25rem;
    font-size: 1rem;
}

.search-input-group input:focus {
    border-color: var(--coach-primary);
    box-shadow: 0 0 0 0.2rem rgba(0, 74, 35, 0.15);
}

.search-input-group .search-icon {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--coach-gray-500);
}

.team-results {
    margin-top: 2rem;
}

.team-item {
    background-color: var(--coach-light);
    border: 2px solid var(--coach-gray-200);
    border-radius: var(--coach-border-radius);
    padding: 1.25rem;
    margin-bottom: 1rem;
    transition: var(--coach-transition);
    cursor: pointer;
}

.team-item:hover {
    border-color: var(--coach-secondary);
    transform: translateY(-2px);
    box-shadow: var(--coach-shadow);
}

.team-item.selected {
    border-color: var(--coach-primary);
    background-color: rgba(0, 74, 35, 0.05);
}

.team-logo {
    width: 50px;
    height: 50px;
    border-radius: var(--coach-border-radius);
    object-fit: cover;
    border: 2px solid var(--coach-secondary);
}

.team-logo-placeholder {
    width: 50px;
    height: 50px;
    background-color: var(--coach-gray-100);
    border: 2px solid var(--coach-secondary);
    border-radius: var(--coach-border-radius);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--coach-primary);
}

.join-request-form {
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--coach-gray-200);
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
}

.empty-state {
    text-align: center;
    padding: 3rem 2rem;
    color: var(--coach-gray-500);
}

.empty-state i {
    font-size: 4rem;
    color: var(--coach-secondary);
    margin-bottom: 1rem;
    opacity: 0.7;
}

.loading-state {
    text-align: center;
    padding: 2rem;
    color: var(--coach-gray-500);
}

@media (max-width: 768px) {
    .find-team-page {
        padding: 1rem 0;
    }
    .search-card-body {
        padding: 1.5rem;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="find-team-page">
    <div class="container">
        <!-- Page Header -->
        <div class="coach-page-header">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <h1 class="coach-page-title h3">
                        <i class="fa fa-search me-3"></i>
                        <?= lang('Common.find_team') ?>
                    </h1>
                    <p class="coach-page-subtitle"><?= lang('Common.search_team_to_join') ?></p>
                </div>
                <div class="col-md-4 text-md-end">
                    <a href="<?= base_url('coach/my-requests') ?>" class="coach-btn-outline">
                        <i class="fa fa-list me-2"></i>
                        <?= lang('Common.my_join_requests') ?>
                        <?php if ($pendingRequestsCount > 0): ?>
                        <span class="badge bg-warning text-dark ms-2"><?= $pendingRequestsCount ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Search Card -->
        <div class="search-card">
            <div class="search-card-header">
                <h5 class="m-0 d-flex align-items-center text-white">
                    <i class="fa fa-futbol me-3"></i>
                    <?= lang('Common.search_team') ?>
                </h5>
            </div>
            <div class="search-card-body">
                <!-- Search Input -->
                <div class="search-input-group">
                    <input type="text" id="teamSearch" class="form-control"
                           placeholder="<?= lang('Common.enter_team_name') ?>"
                           autocomplete="off">
                    <i class="fa fa-search search-icon"></i>
                </div>

                <!-- Team Results -->
                <div class="team-results" id="teamResults">
                    <div class="empty-state" id="emptyState">
                        <i class="fa fa-search"></i>
                        <h5><?= lang('Common.enter_search_keyword') ?></h5>
                        <p class="text-muted"><?= lang('Common.type_at_least_2_chars') ?></p>
                    </div>
                    <div class="loading-state d-none" id="loadingState">
                        <i class="fa fa-spinner fa-spin fa-2x"></i>
                        <p class="mt-2"><?= lang('Common.searching') ?>...</p>
                    </div>
                    <div id="teamList"></div>
                </div>

                <!-- Join Request Form (hidden until team is selected) -->
                <div class="join-request-form d-none" id="joinRequestForm">
                    <h5 class="mb-3"><?= lang('Common.send_join_request') ?></h5>

                    <input type="hidden" id="selectedTeamId" value="">

                    <div class="mb-3">
                        <label class="form-label"><?= lang('Common.selected_team') ?></label>
                        <div id="selectedTeamInfo" class="p-3 border rounded bg-light"></div>
                    </div>

                    <div class="mb-3">
                        <label for="roleRequested" class="form-label"><?= lang('Common.role_requested') ?></label>
                        <select class="form-select" id="roleRequested">
                            <option value="assistant_coach"><?= lang('Common.role_assistant_coach') ?></option>
                            <option value="head_coach"><?= lang('Common.role_head_coach') ?></option>
                            <option value="trainer"><?= lang('Common.role_trainer') ?></option>
                            <option value="scout"><?= lang('Common.role_scout') ?></option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="requestMessage" class="form-label"><?= lang('Common.message_optional') ?></label>
                        <textarea class="form-control" id="requestMessage" rows="3"
                                  placeholder="<?= lang('Common.introduce_yourself') ?>"></textarea>
                    </div>

                    <div class="d-flex gap-2">
                        <button type="button" class="coach-btn-primary" id="submitRequest">
                            <i class="fa fa-paper-plane me-2"></i>
                            <?= lang('Common.send_request') ?>
                        </button>
                        <button type="button" class="btn btn-secondary" id="cancelSelection">
                            <?= lang('Common.cancel') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let searchTimeout;
let selectedTeam = null;

// Search functionality
$('#teamSearch').on('input', function() {
    const keyword = $(this).val().trim();

    clearTimeout(searchTimeout);

    if (keyword.length < 2) {
        $('#emptyState').removeClass('d-none');
        $('#teamList').html('');
        $('#loadingState').addClass('d-none');
        return;
    }

    $('#emptyState').addClass('d-none');
    $('#loadingState').removeClass('d-none');
    $('#teamList').html('');

    searchTimeout = setTimeout(function() {
        searchTeams(keyword);
    }, 300);
});

function searchTeams(keyword) {
    $.ajax({
        url: '<?= base_url('coach/search-teams') ?>',
        method: 'GET',
        data: { keyword: keyword },
        dataType: 'json',
        success: function(teams) {
            $('#loadingState').addClass('d-none');
            if (teams.status === 'error') {
                $('#teamList').html('<div class="alert alert-danger">' + (teams.message || '<?= lang('Common.error_searching') ?>') + '</div>');
                return;
            }
            displayTeams(Array.isArray(teams) ? teams : []);
        },
        error: function(xhr) {
            $('#loadingState').addClass('d-none');
            if (xhr.status === 401) {
                window.location.href = '<?= base_url('coach') ?>';
                return;
            }
            $('#teamList').html('<div class="alert alert-danger"><?= lang('Common.error_searching') ?></div>');
        }
    });
}

function displayTeams(teams) {
    if (teams.length === 0) {
        $('#teamList').html(`
            <div class="empty-state">
                <i class="fa fa-meh"></i>
                <h5><?= lang('Common.no_teams_found') ?></h5>
                <p class="text-muted"><?= lang('Common.try_different_keyword') ?></p>
            </div>
        `);
        return;
    }

    let html = '';
    teams.forEach(function(team) {
        const logo = team.logo
            ? `<img src="<?= base_url() ?>/${team.logo}" class="team-logo" alt="${team.name}">`
            : `<div class="team-logo-placeholder"><i class="fa fa-shield"></i></div>`;

        html += `
            <div class="team-item" data-team-id="${team.id}" data-team-name="${team.name}" data-team-logo="${team.logo || ''}">
                <div class="d-flex align-items-center">
                    <div class="me-3">${logo}</div>
                    <div class="flex-grow-1">
                        <h6 class="mb-1 fw-bold">${team.name}</h6>
                        <small class="text-muted">${team.organize_name || ''}</small>
                    </div>
                    <i class="fa fa-chevron-right text-muted"></i>
                </div>
            </div>
        `;
    });
    $('#teamList').html(html);
}

// Team selection
$(document).on('click', '.team-item', function() {
    $('.team-item').removeClass('selected');
    $(this).addClass('selected');

    selectedTeam = {
        id: $(this).data('team-id'),
        name: $(this).data('team-name'),
        logo: $(this).data('team-logo')
    };

    showJoinRequestForm();
});

function showJoinRequestForm() {
    $('#selectedTeamId').val(selectedTeam.id);

    const logo = selectedTeam.logo
        ? `<img src="<?= base_url() ?>/${selectedTeam.logo}" class="team-logo me-3" alt="${selectedTeam.name}">`
        : `<div class="team-logo-placeholder me-3"><i class="fa fa-shield"></i></div>`;

    $('#selectedTeamInfo').html(`
        <div class="d-flex align-items-center">
            ${logo}
            <div>
                <h6 class="mb-0 fw-bold">${selectedTeam.name}</h6>
            </div>
        </div>
    `);

    $('#joinRequestForm').removeClass('d-none');

    // Scroll to form
    $('html, body').animate({
        scrollTop: $('#joinRequestForm').offset().top - 100
    }, 300);
}

// Cancel selection
$('#cancelSelection').on('click', function() {
    selectedTeam = null;
    $('.team-item').removeClass('selected');
    $('#joinRequestForm').addClass('d-none');
    $('#requestMessage').val('');
});

// Submit request
$('#submitRequest').on('click', function() {
    if (!selectedTeam) {
        Swal.fire({
            icon: 'warning',
            title: '<?= lang('Common.warning') ?>',
            text: '<?= lang('Common.please_select_team') ?>'
        });
        return;
    }

    Swal.fire({
        title: '<?= lang('Common.confirm_send_request') ?>',
        text: '<?= lang('Common.confirm_send_request_text') ?>'.replace('%s', selectedTeam.name),
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#004a23',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<?= lang('Common.send_request') ?>',
        cancelButtonText: '<?= lang('Common.cancel') ?>'
    }).then((result) => {
        if (result.isConfirmed) {
            submitJoinRequest();
        }
    });
});

function submitJoinRequest() {
    Swal.fire({
        title: '<?= lang('Common.processing') ?>',
        html: '<?= lang('Common.please_wait_moment') ?>',
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
        }
    });

    $.ajax({
        url: '<?= base_url('coach/submit-join-request') ?>',
        method: 'POST',
        data: {
            team_id: selectedTeam.id,
            message: $('#requestMessage').val(),
            role_requested: $('#roleRequested').val()
        },
        dataType: 'json',
        success: function(response) {
            if (response.status === 'success') {
                Swal.fire({
                    icon: 'success',
                    title: '<?= lang('Common.success_title') ?>',
                    text: response.message,
                    confirmButtonColor: '#004a23'
                }).then(() => {
                    window.location.href = '<?= base_url('coach/my-requests') ?>';
                });
            } else {
                Swal.fire({
                    icon: 'error',
                    title: '<?= lang('Common.error_occurred') ?>',
                    text: response.message,
                    confirmButtonColor: '#dc3545'
                });
            }
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: '<?= lang('Common.error_occurred') ?>',
                text: '<?= lang('Common.connection_error') ?>',
                confirmButtonColor: '#dc3545'
            });
        }
    });
}
</script>
<?= $this->endSection() ?>
