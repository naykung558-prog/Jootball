<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <form action="<?= base_url('admin/tournament/save-tournament-team') ?>" method="POST"
            enctype="multipart/form-data" class="once-only" autocomplete="off">

            <!-- Hidden -->
            <input type="hidden" name="tour_id" value="<?= (!empty($tour_id)) ? $tour_id : '' ?>">

            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-3">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                        <i class="ti ti-users-plus text-white fs-4"></i>
                    </div>
                    <div>
                        <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.select_participating_teams') ?></h4>
                        <p class="text-muted mb-0 small"><?= lang('Common.please_select_teams_to_join') ?></p>
                    </div>
                </div>
                <a href="<?= base_url('admin/tournament/tournament-team/' . $tour_id) ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border shadow-sm">
                    <i class="ti ti-arrow-left me-2"></i> <?= lang('Common.back') ?>
                </a>
            </div>

            <!-- Search Field -->
            <div class="card border-0 shadow-sm mb-4" style="background-color: #f8f9fa; border-radius: var(--border-radius);">
                <div class="card-body p-3">
                    <div class="row align-items-center g-3">
                        <div class="col-md-7">
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white border-end-0" style="border-radius: 8px 0 0 8px;">
                                    <i class="ti ti-search text-muted"></i>
                                </span>
                                <input type="text" id="teamSearch" class="form-control border-start-0" placeholder="<?= lang('Common.search_team_name') ?>" style="border-radius: 0 8px 8px 0;">
                            </div>
                        </div>
                        <div class="col-md-5 text-md-end">
                            <div class="text-muted fw-bold small">
                                <i class="ti ti-info-circle me-1"></i>
                                <?= lang('Common.found') ?> <span id="teamCount" class="text-primary fs-5">0</span> / <span id="teamTotal">0</span> <?= lang('Common.teams') ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Teams Grid -->
            <div class="row g-3" id="teamContainer">
                <?php foreach ($team_options as $key => $team) { ?>
                    <div class="col-xl-2 col-lg-3 col-md-4 col-sm-6 team-item" data-team-name="<?= strtolower($team['name']) ?>">
                        <label class="w-100 h-100 cursor-pointer">
                            <input type="checkbox" class="d-none team-checkbox" name="teams[]" value="<?= $team['id'] ?>"
                                <?php foreach ($team_options_selected as $sel) {
                                    if ($sel['team_id'] == $team['id']) {
                                        echo "checked";
                                    }
                                } ?>>
                            <div class="card h-100 border team-card-content transition-hover" style="border-radius: 12px;">
                                <div class="card-body p-3 text-center d-flex flex-column align-items-center justify-content-center">
                                    <div class="position-relative mb-3">
                                        <div class="team-logo-wrapper d-flex align-items-center justify-content-center bg-light rounded p-2 shadow-sm" style="width: 80px; height: 80px;">
                                            <?php if ($team['logo'] && file_exists('./' . $team['logo'])) { ?>
                                                <img src="<?= base_url($team['logo']) ?>" alt="<?= $team['name'] ?>" class="img-fluid" style="max-height: 100%; max-width: 100%;">
                                            <?php } else { ?>
                                                <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="Logo" class="img-fluid" style="max-height: 100%; max-width: 100%;">
                                            <?php } ?>
                                        </div>
                                        <div class="check-indicator position-absolute top-0 end-0 translate-middle-y me-n2 mt-n2 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 24px; height: 24px; opacity: 0; transform: scale(0); transition: all 0.2s;">
                                            <i class="ti ti-check" style="font-size: 14px;"></i>
                                        </div>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-0 text-nowrap w-100 small" title="<?= $team['name'] ?>"><?= $team['name'] ?></h6>
                                </div>
                            </div>
                        </label>
                    </div>
                <?php } ?>
            </div>

            <!-- No Results Message -->
            <div id="noResults" class="text-center py-5 d-none">
                <div class="mb-3">
                    <i class="ti ti-search text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                </div>
                <h5 class="text-muted fw-bold"><?= lang('Common.no_teams_match_search') ?></h5>
                <p class="text-muted small"><?= lang('Common.try_search_other_name') ?></p>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation" class="mt-5">
                <ul class="pagination justify-content-center" id="pagination">
                </ul>
            </nav>

            <div class="row justify-content-center mt-4 mb-2">
                <div class="col-md-12 text-center">
                    <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold btn-submit shadow-sm" style="background: var(--gold-gradient); border: none;">
                        <i class="ti ti-device-floppy me-2"></i> <?= lang('Common.save') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<style>
    .cursor-pointer { cursor: pointer; }
    .transition-hover { transition: all 0.2s ease; }
    .team-checkbox:checked + .team-card-content {
        border-color: var(--bs-primary) !important;
        background-color: rgba(var(--bs-primary-rgb), 0.05);
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }
    .team-checkbox:checked + .team-card-content .check-indicator {
        opacity: 1;
        transform: scale(1);
    }
    .team-item:hover .team-card-content {
        transform: translateY(-5px);
        box-shadow: 0 5px 15px rgba(0,0,0,0.08);
    }
    .pagination .page-link {
        color: var(--bs-primary);
        border-radius: 50%;
        margin: 0 3px;
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: none;
        font-weight: bold;
    }
    .pagination .page-item.active .page-link {
        background-color: var(--bs-primary);
        color: white;
    }
    .pagination .page-item.disabled .page-link {
        color: #6c757d;
        background-color: transparent;
    }
    .pagination .page-link:hover:not(.active) {
        background-color: #e9ecef;
    }
</style>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        const itemsPerPage = 30; // 30 teams per page (6 columns x 5 rows)
        let currentPage = 1;
        let filteredItems = [];
        const allItems = $('.team-item');
        const totalTeams = allItems.length;

        // Initialize pagination
        function initPagination() {
            filteredItems = allItems.toArray();
            updateTeamCounts();
            renderPagination();
            displayPage(1);
        }

        // Render pagination buttons
        function renderPagination() {
            const totalPages = Math.ceil(filteredItems.length / itemsPerPage);
            const paginationHtml = [];

            // Only show pagination if there's more than one page
            if (totalPages <= 1) {
                $('#pagination').html('');
                return;
            }

            // Previous button
            paginationHtml.push(
                `<li class="page-item ${currentPage === 1 ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="prev">
                        <i class="ti ti-chevron-left"></i>
                    </a>
                </li>`
            );

            // Show first page
            paginationHtml.push(
                `<li class="page-item ${currentPage === 1 ? 'active' : ''}">
                    <a class="page-link" href="#" data-page="1">1</a>
                </li>`
            );

            // Show ellipsis and middle pages if needed
            if (totalPages > 5) {
                if (currentPage > 3) {
                    paginationHtml.push(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }

                const startPage = Math.max(2, currentPage - 1);
                const endPage = Math.min(totalPages - 1, currentPage + 1);

                for (let i = startPage; i <= endPage; i++) {
                    paginationHtml.push(
                        `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`
                    );
                }

                if (currentPage < totalPages - 2) {
                    paginationHtml.push(`<li class="page-item disabled"><span class="page-link">...</span></li>`);
                }
            } else {
                // Show all page numbers if 5 or fewer
                for (let i = 2; i <= totalPages; i++) {
                    paginationHtml.push(
                        `<li class="page-item ${i === currentPage ? 'active' : ''}">
                            <a class="page-link" href="#" data-page="${i}">${i}</a>
                        </li>`
                    );
                }
            }

            // Show last page
            if (totalPages > 1) {
                paginationHtml.push(
                    `<li class="page-item ${currentPage === totalPages ? 'active' : ''}">
                        <a class="page-link" href="#" data-page="${totalPages}">${totalPages}</a>
                    </li>`
                );
            }

            // Next button
            paginationHtml.push(
                `<li class="page-item ${currentPage === totalPages ? 'disabled' : ''}">
                    <a class="page-link" href="#" data-page="next">
                        <i class="ti ti-chevron-right"></i>
                    </a>
                </li>`
            );

            $('#pagination').html(paginationHtml.join(''));

            // Bind pagination click events
            $('#pagination a').click(function(e) {
                e.preventDefault();
                const page = $(this).data('page');
                const totalPages = Math.ceil(filteredItems.length / itemsPerPage);

                if (page === 'prev' && currentPage > 1) {
                    currentPage--;
                } else if (page === 'next' && currentPage < totalPages) {
                    currentPage++;
                } else if (!isNaN(page)) {
                    currentPage = parseInt(page);
                }

                displayPage(currentPage);
                renderPagination();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            });
        }

        // Display teams for current page
        function displayPage(page) {
            const startIdx = (page - 1) * itemsPerPage;
            const endIdx = startIdx + itemsPerPage;

            $('.team-item').hide();
            filteredItems.slice(startIdx, endIdx).forEach(item => {
                $(item).show();
            });

            // Show/hide no results message
            if (filteredItems.length === 0) {
                $('#noResults').removeClass('d-none');
            } else {
                $('#noResults').addClass('d-none');
            }
        }

        // Update team counts
        function updateTeamCounts() {
            $('#teamCount').text(filteredItems.length);
            $('#teamTotal').text(totalTeams);
        }

        // Search functionality
        $('#teamSearch').on('keyup', function() {
            const searchText = $(this).val().toLowerCase().trim();

            if (searchText === '') {
                filteredItems = allItems.toArray();
            } else {
                filteredItems = allItems.filter(function() {
                    // Use attr() + String() so numeric team names aren't coerced to Number
                    // by jQuery .data() (which would break .includes()).
                    const teamName = String($(this).attr('data-team-name') || '');
                    return teamName.includes(searchText);
                }).toArray();
            }

            currentPage = 1;
            updateTeamCounts();
            renderPagination();
            displayPage(1);
        });

        // Pressing Enter in the search box should only search, never submit/save the form.
        $('#teamSearch').on('keydown', function(e) {
            if (e.key === 'Enter' || e.keyCode === 13) {
                e.preventDefault();
                $(this).trigger('keyup'); // re-run the live filter
            }
        });

        // Guard the save: block empty selection, and prevent double submission
        // (which creates duplicate tournament_team rows).
        var isSubmitting = false;
        $('form.once-only').on('submit', function(e) {
            if ($('.team-checkbox:checked').length === 0) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var msg = '<?= lang('Common.please_select_teams_to_join') ?>';
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'warning', title: '<?= lang('Common.warning') ?>', text: msg });
                } else {
                    alert(msg);
                }
                return false;
            }

            // Already submitting (e.g. double click or repeated Enter) → ignore.
            if (isSubmitting) {
                e.preventDefault();
                e.stopImmediatePropagation();
                return false;
            }
            isSubmitting = true;
            $('.btn-submit').attr('disabled', true);
        });

        // Initialize on page load
        initPagination();
    });
</script>
<?php $this->endSection(); ?>