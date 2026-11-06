<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary"><i class="fa fa-users me-2"></i><?= lang('Common.player_list') ?></h4>
                <p class="text-muted small mb-0"><?= lang('Common.manage_player_desc') ?></p>
            </div>
            <div class="d-flex gap-2">
                <?php
                $_academyIdForLink = $academy['id'] ?? ($academy_id ?? null);
                $manage_url = base_url('player/manage' . ($_academyIdForLink ? '?academy_id=' . (int)$_academyIdForLink : ''));
                if (session()->get('user_type') === 'coach' && isset($team['id'])) {
                    $manage_url = base_url('player/manage_team/' . $team['id']);
                }
                ?>
                <div class="dropdown">
                    <button class="btn btn-primary rounded-pill px-4 dropdown-toggle" type="button" id="addPlayerDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa fa-plus-circle me-2"></i><?= lang('Common.add_player') ?>
                    </button>
                    <ul class="dropdown-menu" aria-labelledby="addPlayerDropdown">
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#selectPlayerModal">
                                <i class="fa fa-search me-2 text-success"></i><?= lang('Common.add_from_system') ?>
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="<?= $manage_url ?>">
                                <i class="fa fa-user-plus me-2 text-primary"></i><?= lang('Common.add_new_player') ?>
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="table" class="table table-hover align-middle mb-0 text-nowrap" style="width:100%">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center py-3 text-secondary border-0 rounded-start-2 ps-4" width="5%">#</th>
                            <th class="text-center py-3 text-secondary border-0" width="10%"><?= lang('Common.image') ?></th>
                            <th class="text-center py-3 text-secondary border-0" width="10%"><?= lang('Common.shirt_number') ?></th>
                            <th class="py-3 text-secondary border-0"><?= lang('Common.full_name') ?></th>
                            <th class="py-3 text-secondary border-0"><?= lang('Common.nickname') ?></th>
                            <th class="text-center py-3 text-secondary border-0" width="15%"><?= lang('Common.birth_date') ?></th>
                            <th class="text-center py-3 text-secondary border-0" width="10%"><?= lang('Common.position') ?></th>
                            <th class="text-center py-3 text-secondary border-0 rounded-end-2 pe-4" width="10%"><?= lang('Common.manage') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- DataTables will populate this -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Export Modal -->
<div class="modal fade" id="export-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary"><i class="fa fa-file-pdf me-2"></i><?= lang('Common.create_document') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="mb-3">
                    <label for="tournament_name" class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.tournament_name_label') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="tournament_name" name="tournament_name" placeholder="<?= lang('Common.enter_tournament_name') ?>">
                </div>
                <div class="mb-3">
                    <label for="gen" class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.age_group') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="gen" name="gen" placeholder="<?= lang('Common.enter_age_group') ?>">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="createPDF()">
                    <i class="fa fa-print me-2"></i><?= lang('Common.create_document_btn') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Select Player from System Modal -->
<div class="modal fade" id="selectPlayerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content border-0 shadow-lg rounded-4 player-search-modal">
            <!-- Hero header -->
            <div class="psm-hero">
                <div class="psm-hero__icon"><i class="fas fa-user-plus"></i></div>
                <div class="psm-hero__body">
                    <h5 class="psm-hero__title mb-1"><?= lang('Common.add_player_from_system') ?></h5>
                    <p class="psm-hero__sub mb-0"><?= lang('Common.search_player_hint') ?></p>
                </div>
                <button type="button" class="btn-close btn-close-white psm-hero__close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- Search input -->
                <div class="psm-search">
                    <i class="fa fa-search psm-search__icon"></i>
                    <input type="text" class="form-control psm-search__input" id="searchPlayerInput"
                           placeholder="<?= lang('Common.enter_id_or_name') ?>" autocomplete="off">
                    <button type="button" class="psm-search__clear d-none" id="searchPlayerClear" aria-label="Clear">
                        <i class="fa fa-times-circle"></i>
                    </button>
                    <button class="btn psm-search__submit" type="button" id="searchPlayerBtn">
                        <i class="fa fa-search me-1"></i> <?= lang('Common.search') ?>
                    </button>
                </div>

                <!-- Status legend (info) -->
                <div class="psm-legend">
                    <span class="psm-legend__item"><span class="psm-dot psm-dot--success"></span><?= lang('Common.in_this_academy') ?></span>
                    <span class="psm-legend__item"><span class="psm-dot psm-dot--warning"></span><?= lang('Common.in_other_academy') ?></span>
                    <span class="psm-legend__item"><span class="psm-dot psm-dot--muted"></span><?= lang('Common.no_academy') ?></span>
                </div>

                <!-- Results -->
                <div id="playerSearchResults" class="psm-results d-none">
                    <div class="psm-results__head">
                        <span class="psm-results__title"><i class="fa fa-users me-2"></i><?= lang('Common.search_results') ?></span>
                        <span class="psm-results__count" id="playerResultsCount"></span>
                    </div>
                    <div id="playerResultsList" class="psm-results__list">
                        <!-- Results will be populated here -->
                    </div>
                </div>

                <!-- Initial state (when no search yet) -->
                <div id="playerSearchInitial" class="psm-empty">
                    <i class="fas fa-user-friends psm-empty__icon"></i>
                    <p class="psm-empty__title mb-1"><?= lang('Common.search_initial_title') ?></p>
                    <p class="psm-empty__sub mb-0"><?= lang('Common.search_initial_sub') ?></p>
                </div>

                <!-- No results -->
                <div id="noPlayerResults" class="psm-empty d-none">
                    <i class="fas fa-search-minus psm-empty__icon"></i>
                    <p class="psm-empty__title mb-1"><?= lang('Common.no_player_found') ?></p>
                    <p class="psm-empty__sub mb-0"><?= lang('Common.no_player_hint') ?></p>
                </div>

                <!-- Loading -->
                <div id="playerSearchLoading" class="psm-loading d-none">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-3 mb-0"><?= lang('Common.searching_msg') ?></p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-3 px-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.close') ?></button>
            </div>
        </div>
    </div>
</div>

<style>
.player-search-modal .psm-hero {
    background: #16a34a;
    color: #fff;
    padding: 22px 26px;
    border-top-left-radius: 1rem;
    border-top-right-radius: 1rem;
    display: flex; align-items: center; gap: 14px;
    position: relative;
}
.player-search-modal .psm-hero__icon {
    width: 48px; height: 48px; border-radius: 12px;
    background: rgba(255,255,255,0.18);
    display: flex; align-items: center; justify-content: center;
    font-size: 1.4rem; flex-shrink: 0;
}
.player-search-modal .psm-hero__title { font-weight: 700; font-size: 1.1rem; color: #fff; }
.player-search-modal .psm-hero__sub   { font-size: .82rem; opacity: .85; line-height: 1.45; }
.player-search-modal .psm-hero__close { position: absolute; top: 18px; right: 18px; opacity: .8; }
.player-search-modal .psm-hero__close:hover { opacity: 1; }

.player-search-modal .psm-search {
    position: relative;
    display: flex; align-items: center;
    background: #f6f9fc;
    border: 2px solid #e5e7eb;
    border-radius: 14px;
    padding: 0 6px 0 16px;
    transition: border-color .2s, box-shadow .2s;
    margin-bottom: 14px;
}
.player-search-modal .psm-search:focus-within {
    border-color: #16a34a;
    box-shadow: 0 0 0 4px rgba(22,163,74,.12);
}
.player-search-modal .psm-search__icon { color: #94a3b8; font-size: 1rem; flex-shrink: 0; }
.player-search-modal .psm-search__input {
    border: 0; background: transparent; font-size: .95rem;
    padding: 14px 12px; flex-grow: 1; outline: none; box-shadow: none !important;
}
.player-search-modal .psm-search__input:focus { background: transparent; }
.player-search-modal .psm-search__clear {
    background: transparent; border: 0; color: #94a3b8;
    padding: 8px; cursor: pointer; transition: color .2s;
}
.player-search-modal .psm-search__clear:hover { color: #ef4444; }
.player-search-modal .psm-search__submit {
    background: #16a34a; color: #fff;
    border: 0; border-radius: 10px;
    padding: 9px 18px; font-weight: 600;
    transition: background .2s;
    flex-shrink: 0;
}
.player-search-modal .psm-search__submit:hover { background: #15803d; color: #fff; }

.player-search-modal .psm-legend {
    display: flex; gap: 16px; flex-wrap: wrap;
    padding: 8px 4px 16px;
    font-size: .78rem; color: #64748b;
}
.player-search-modal .psm-legend__item { display: inline-flex; align-items: center; gap: 6px; }
.player-search-modal .psm-dot { width: 10px; height: 10px; border-radius: 50%; }
.player-search-modal .psm-dot--success { background: #16a34a; }
.player-search-modal .psm-dot--warning { background: #f59e0b; }
.player-search-modal .psm-dot--muted   { background: #94a3b8; }

.player-search-modal .psm-results {
    background: #fafbfc;
    border: 1px solid #e5e7eb;
    border-radius: 12px;
    padding: 12px;
}
.player-search-modal .psm-results__head {
    display: flex; align-items: center; justify-content: space-between;
    padding-bottom: 8px; margin-bottom: 8px;
    border-bottom: 1px solid #e5e7eb;
}
.player-search-modal .psm-results__title { font-weight: 600; color: #1f2937; font-size: .9rem; }
.player-search-modal .psm-results__count { font-size: .78rem; color: #64748b; }
.player-search-modal .psm-results__list .list-group-item {
    border-radius: 10px; margin-bottom: 6px; border-color: #e5e7eb;
    transition: all .15s;
}
.player-search-modal .psm-results__list .list-group-item:hover {
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(15,23,42,.08);
    border-color: #16a34a40;
}

.player-search-modal .psm-avatar {
    width: 56px; height: 56px;
    border-radius: 50%;
    border: 1px solid #e5e7eb;
    object-fit: cover;
    flex-shrink: 0;
}
.player-search-modal .psm-avatar--initial {
    display: flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, #16a34a 0%, #16478C 100%);
    color: #fff;
    font-weight: 700; font-size: 1.25rem;
    text-transform: uppercase;
    border: 0;
}

.player-search-modal .psm-teams {
    display: flex; flex-wrap: wrap; gap: 6px;
    margin-top: 4px;
}
.player-search-modal .psm-team {
    display: inline-flex; align-items: center;
    padding: 3px 10px; border-radius: 999px;
    background: #f1f5f9; border: 1px solid #e2e8f0;
    color: #475569; font-size: .76rem; font-weight: 500;
    line-height: 1.2;
    max-width: 100%;
}
.player-search-modal .psm-team__academy { color: #94a3b8; margin-left: 4px; font-weight: 400; }
.player-search-modal .psm-team--current {
    background: rgba(22,163,74,.10);
    border-color: rgba(22,163,74,.35);
    color: #15803d;
}
.player-search-modal .psm-team--current .psm-team__academy { color: #65a380; }

.player-search-modal .psm-empty,
.player-search-modal .psm-loading {
    text-align: center; padding: 36px 16px;
}
.player-search-modal .psm-empty__icon { font-size: 2.6rem; color: #cbd5e1; margin-bottom: 12px; display: block; }
.player-search-modal .psm-empty__title { font-weight: 600; color: #475569; font-size: .95rem; }
.player-search-modal .psm-empty__sub   { font-size: .82rem; color: #94a3b8; }
</style>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 50,
            lengthMenu: [[25, 50, 100, -1], [25, 50, 100, "All"]],
            language: dtLanguage,
            ajax: {
                <?php if (isset($team['id'])): ?>
                url: '<?= base_url('player/get-players') ?>',
                method: "POST",
                data: function(d) {
                    d.team_id = '<?= $team['id'] ?>';
                }
                <?php elseif (isset($academy['id'])): ?>
                url: '<?= base_url('academy/get-academy-players/' . $academy['id']) ?>',
                method: "POST"
                <?php else: ?>
                url: '<?= base_url('player/get-players') ?>',
                method: "POST"
                <?php endif; ?>
            },
            dom: '<"d-flex justify-content-between align-items-center flex-wrap px-4 py-3"lf>t<"d-flex justify-content-between align-items-center flex-wrap px-4 py-3"ip>',
            order: [[2, 'asc']],
            columnDefs: [{
                    targets: 0,
                    orderable: false
                },
                {
                    targets: 1,
                    orderable: false
                },
                {
                    targets: -1,
                    orderable: false
                }
            ],
            columns: [{
                    data: '',
                    className: 'text-center text-muted fw-bold ps-4',
                    name: 'numbering'
                },
                {
                    data: 'image',
                    className: 'text-center',
                    orderable: false,
                    name: 'image'
                },
                {
                    data: 'shirt_number',
                    className: 'text-center fw-bold text-primary fs-5 dt-type-numeric',
                    <?php if (isset($academy['id']) && !isset($team['id'])): ?>
                    name: 'academy_player_enrollments.shirt_number',
                    <?php else: ?>
                    name: 'player.shirt_number',
                    <?php endif; ?>
                    render: function(data, type, row) {
                        if (type === 'sort' || type === 'type') {
                            return parseInt(data) || 0;
                        }
                        return data;
                    }
                },
                {
                    data: 'full_name',
                    name: 'player.first_name',
                    className: 'fw-medium'
                },
                {
                    data: 'nick_name',
                    name: 'player.nick_name',
                    className: 'text-muted'
                },
                {
                    data: 'birth_date',
                    name: 'player.birth_date',
                    className: 'text-center text-muted',
                },
                {
                    data: 'player_sub_position',
                    name: 'player_sub_position',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'action',
                    name: 'action',
                    className: 'text-center pe-4',
                    orderable: false
                }
            ],
            drawCallback: function(settings) {
                // Optional: Add custom styling to pagination buttons here if not handled by BS5 integration
            }
        });
    });

    const langConfirmDeleteData = '<?= lang('Common.confirm_delete_data') ?>';
    const langDeleteIrreversible = '<?= lang('Common.delete_irreversible') ?>';
    const langConfirmDeleteBtn = '<?= lang('Common.confirm_delete_btn') ?>';
    const langCancel = '<?= lang('Common.cancel') ?>';
    const langSuccess = '<?= lang('Common.success') ?>';
    const langError = '<?= lang('Common.error') ?>';
    const langDeleteError = '<?= lang('Common.delete_error') ?>';
    const langPleaseEnterInfo = '<?= lang('Common.please_enter_info') ?>';
    const langEnterAtLeast2 = '<?= lang('Common.enter_at_least_2_chars') ?>';
    const langOk = '<?= lang('Common.ok_btn') ?>';
    const langSearchError = '<?= lang('Common.search_error') ?>';
    const langConfirmAddPlayer = '<?= lang('Common.confirm_add_player') ?>';
    const langAddToTeam = '<?= lang('Common.add_to_team') ?>';
    const langAddPlayer = '<?= lang('Common.add_player') ?>';
    const langCannotAddPlayer = '<?= lang('Common.cannot_add_player') ?>';

    function deletePlayer(id) {
        Swal.fire({
            title: langConfirmDeleteData,
            text: langDeleteIrreversible,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: langConfirmDeleteBtn,
            cancelButtonText: langCancel,
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('player/delete') ?>",
                    data: {
                        id: id
                    },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: "success",
                                title: langSuccess,
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#table').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: langError,
                                text: response.message,
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: langError,
                            text: langDeleteError,
                        });
                    }
                });
            }
        });
    }

    function createPDF() {
        let url = "<?= base_url('academy/player-document-list') ?>";
        <?php if (session()->get('user_type') === 'coach' && isset($team['id'])): ?>
        url += "?id=<?= $team['id'] ?>";
        <?php endif; ?>

        let title = $("#tournament_name").val();
        let generate = $("#gen").val();

        if (url.includes('?')) {
            url += "&title=" + title + "&gen=" + generate;
        } else {
            url += "?title=" + title + "&gen=" + generate;
        }

        window.open(url, '_blank');
    }

    // Search players from system (team_id = 0)
    $('#searchPlayerBtn').on('click', function() {
        searchAvailablePlayers();
    });

    $('#searchPlayerInput').on('keypress', function(e) {
        if (e.which === 13) {
            searchAvailablePlayers();
        }
    });

    // toggle clear button + reset state when input cleared
    $('#searchPlayerInput').on('input', function() {
        const v = $(this).val();
        $('#searchPlayerClear').toggleClass('d-none', v.length === 0);
        if (v.length === 0) {
            $('#playerSearchResults').addClass('d-none');
            $('#noPlayerResults').addClass('d-none');
            $('#playerSearchLoading').addClass('d-none');
            $('#playerSearchInitial').removeClass('d-none');
        }
    });
    $('#searchPlayerClear').on('click', function() {
        $('#searchPlayerInput').val('').trigger('input').focus();
    });

    // Reset modal state when opened
    $('#selectPlayerModal').on('shown.bs.modal', function() {
        $('#searchPlayerInput').val('').trigger('input').focus();
    });

    function searchAvailablePlayers() {
        const search = $('#searchPlayerInput').val().trim();

        if (search.length < 2) {
            Swal.fire({
                icon: 'warning',
                title: langPleaseEnterInfo,
                text: langEnterAtLeast2,
                confirmButtonText: langOk
            });
            return;
        }

        // Show loading
        $('#playerSearchInitial').addClass('d-none');
        $('#playerSearchResults').addClass('d-none');
        $('#noPlayerResults').addClass('d-none');
        $('#playerSearchLoading').removeClass('d-none');

        <?php if (isset($academy['id'])): ?>
        // Academy context: ใช้ endpoint ใหม่ที่ยกเว้น player ที่อยู่ใน Academy นี้แล้ว
        $.ajax({
            method: 'GET',
            url: '<?= base_url('academy/player-search/' . ($academy['id'] ?? '')) ?>',
            data: { q: search },
            success: function(response) {
                $('#playerSearchLoading').addClass('d-none');

                if (response.status === 'success' && response.data.length > 0) {
                    $('#playerSearchResults').removeClass('d-none');
                    $('#noPlayerResults').addClass('d-none');

                    function escHtml(s) {
                        return String(s == null ? '' : s)
                            .replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
                            .replace(/"/g, '&quot;').replace(/'/g, '&#39;');
                    }

                    function avatarHtml(player) {
                        const hasImage = player.image_path && player.image_path.length > 0;
                        if (hasImage) {
                            return '<img src="' + escHtml(player.image_url) + '" class="psm-avatar psm-avatar--img" '
                                 + 'onerror="this.parentNode.innerHTML=\'<div class=&quot;psm-avatar psm-avatar--initial&quot;>'
                                 + escHtml((player.full_name || '?').trim().charAt(0).toUpperCase())
                                 + '</div>\'">';
                        }
                        const initial = (player.full_name || '?').trim().charAt(0).toUpperCase();
                        return '<div class="psm-avatar psm-avatar--initial">' + escHtml(initial) + '</div>';
                    }

                    let html = '';
                    response.data.forEach(function(player) {
                        const birthDate = player.birth_date ? new Date(player.birth_date).toLocaleDateString('th-TH') : '-';
                        const safeName = (player.full_name || '').replace(/'/g, "\\'");

                        // status badge หลัก (1 อัน)
                        let statusBadge = '';
                        if (player.in_current_academy) {
                            statusBadge = '<span class="badge bg-success bg-opacity-10 text-success border border-success rounded-pill">'
                                + '<i class="fas fa-check-circle me-1"></i><?= lang('Common.in_this_academy') ?>'
                                + (player.current_team_name ? ' · ' + escHtml(player.current_team_name) : '')
                                + '</span>';
                        } else if (player.other_academies && player.other_academies.length > 0) {
                            statusBadge = '<span class="badge bg-warning bg-opacity-10 text-warning border border-warning rounded-pill">'
                                + '<i class="fas fa-school me-1"></i><?= lang('Common.in_other_academy') ?> (' + player.other_academies.length + ')'
                                + '</span>';
                        } else {
                            statusBadge = '<span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary rounded-pill">'
                                + '<i class="fas fa-user-plus me-1"></i><?= lang('Common.no_academy') ?>'
                                + '</span>';
                        }

                        // รายการทีมที่นักเตะสังกัดอยู่ (ทุก enrollment)
                        let teamsHtml = '';
                        if (player.enrollments && player.enrollments.length > 0) {
                            const chips = player.enrollments.map(function(e) {
                                const isCurrent = parseInt(e.academy_id) === parseInt('<?= (int)($academy['id'] ?? 0) ?>');
                                const teamName = e.team_name || '<?= lang('Common.no_team_assigned') ?>';
                                const academyName = e.academy_name || '';
                                const cls = isCurrent ? 'psm-team psm-team--current' : 'psm-team';
                                return '<span class="' + cls + '" title="Academy: ' + escHtml(academyName) + '">'
                                    + '<i class="fas fa-shield-alt me-1"></i>' + escHtml(teamName)
                                    + '<span class="psm-team__academy">· ' + escHtml(academyName) + '</span>'
                                    + '</span>';
                            }).join('');
                            teamsHtml = '<div class="psm-teams">' + chips + '</div>';
                        } else {
                            teamsHtml = '<div class="psm-teams"><span class="text-muted small"><i class="fas fa-info-circle me-1"></i><?= lang('Common.no_team_yet') ?></span></div>';
                        }

                        // ปุ่ม: ถ้าอยู่ใน academy นี้แล้ว → disable
                        let btnHtml = '';
                        if (player.in_current_academy) {
                            btnHtml = '<button type="button" class="btn btn-light btn-sm rounded-pill" disabled>'
                                + '<i class="fa fa-check me-1"></i><?= lang('Common.already_added') ?>'
                                + '</button>';
                        } else {
                            btnHtml = `<button type="button" class="btn btn-success btn-sm rounded-pill" onclick="showEnrollModal(${player.id}, '${safeName}')">`
                                + '<i class="fa fa-plus me-1"></i> ' + langAddPlayer
                                + '</button>';
                        }

                        html += `
                            <div class="list-group-item list-group-item-action d-flex align-items-start p-3 gap-3">
                                <div class="flex-shrink-0">${avatarHtml(player)}</div>
                                <div class="flex-grow-1 min-w-0">
                                    <div class="d-flex align-items-center gap-2 flex-wrap mb-1">
                                        <h6 class="mb-0 fw-bold">${escHtml(player.full_name)}</h6>
                                        ${statusBadge}
                                    </div>
                                    ${teamsHtml}
                                    <small class="text-muted d-block mt-1">
                                        <i class="fas fa-birthday-cake me-1"></i> ${birthDate}
                                    </small>
                                </div>
                                <div class="flex-shrink-0">${btnHtml}</div>
                            </div>
                        `;
                    });
                    $('#playerResultsList').html(html);
                    $('#playerResultsCount').text(response.data.length + ' ' + (typeof langPlayerCountUnit !== 'undefined' ? langPlayerCountUnit : ''));
                } else {
                    $('#playerSearchResults').addClass('d-none');
                    $('#noPlayerResults').removeClass('d-none');
                }
            },
            error: function() {
                $('#playerSearchLoading').addClass('d-none');
                $('#noPlayerResults').removeClass('d-none');
                Swal.fire({ icon: 'error', title: langError, text: langSearchError });
            }
        });
        <?php else: ?>
        // Team context: ใช้ endpoint เดิม
        $.ajax({
            method: 'POST',
            url: '<?= base_url('player/search-available-players') ?>',
            data: { search: search },
            success: function(response) {
                $('#playerSearchLoading').addClass('d-none');

                if (response.status === 'success' && response.data.length > 0) {
                    $('#playerSearchResults').removeClass('d-none');
                    $('#noPlayerResults').addClass('d-none');

                    let html = '';
                    response.data.forEach(function(player) {
                        const birthDate = player.birth_date ? new Date(player.birth_date).toLocaleDateString('th-TH') : '-';
                        html += `
                            <div class="list-group-item list-group-item-action d-flex align-items-center p-3">
                                <img src="${player.image_url}" class="rounded-circle me-3 border" style="width: 60px; height: 60px; object-fit: cover;">
                                <div class="flex-grow-1">
                                    <h6 class="mb-1 fw-bold">${player.full_name}</h6>
                                    <small class="text-muted">
                                        <i class="fas fa-id-card me-1"></i> ${player.id_card || '-'}
                                        <span class="mx-2">|</span>
                                        <i class="fas fa-birthday-cake me-1"></i> ${birthDate}
                                    </small>
                                </div>
                                <button type="button" class="btn btn-success btn-sm rounded-pill" onclick="assignPlayerToTeam(${player.id}, '${player.full_name}')">
                                    <i class="fa fa-plus me-1"></i> ${langAddToTeam}
                                </button>
                            </div>
                        `;
                    });
                    $('#playerResultsList').html(html);
                } else {
                    $('#playerSearchResults').addClass('d-none');
                    $('#noPlayerResults').removeClass('d-none');
                }
            },
            error: function() {
                $('#playerSearchLoading').addClass('d-none');
                Swal.fire({ icon: 'error', title: langError, text: langSearchError });
            }
        });
        <?php endif; ?>
    }

    <?php if (isset($academy['id'])): ?>
    // Academy Enrollment Flow: SweetAlert ยืนยัน → enroll ตรงเข้า academy ทันที
    function showEnrollModal(playerId, playerName) {
        Swal.fire({
            title: langConfirmAddPlayer,
            html: '<?= lang('Common.want_add_player') ?>'.replace('%s', playerName),
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langAddPlayer,
            cancelButtonText: langCancel
        }).then((result) => {
            if (!result.isConfirmed) return;

            fetch('<?= base_url('academy/player-enroll/' . $academy['id']) ?>', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ player_id: playerId, team_id: 0, shirt_number: '' })
            })
            .then(r => r.json())
            .then(resp => {
                if (resp.status === 'success') {
                    Swal.fire({ icon: 'success', title: langSuccess, text: resp.message, timer: 1500, showConfirmButton: false })
                        .then(() => {
                            $('#selectPlayerModal').modal('hide');
                            $('#searchPlayerInput').val('');
                            $('#playerSearchResults').addClass('d-none');
                            $('#table').DataTable().ajax.reload();
                        });
                } else {
                    Swal.fire({ icon: 'error', title: langError, text: resp.message });
                }
            })
            .catch(() => {
                Swal.fire({ icon: 'error', title: langError, text: langCannotAddPlayer });
            });
        });
    }

    // Unenroll player from academy (called from DataTable action button)
    function unenrollPlayer(playerId, playerName) {
        Swal.fire({
            title: 'ถอนนักเตะออก?',
            html: `ต้องการถอน <strong>${playerName}</strong> ออกจาก Academy นี้ใช่หรือไม่?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ถอนออก',
            cancelButtonText: langCancel
        }).then(result => {
            if (result.isConfirmed) {
                fetch('<?= base_url('academy/player-unenroll/' . $academy['id']) ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ player_id: playerId })
                })
                .then(r => r.json())
                .then(resp => {
                    if (resp.status === 'success') {
                        Swal.fire({ icon: 'success', title: langSuccess, text: resp.message, timer: 1500, showConfirmButton: false })
                            .then(() => $('#table').DataTable().ajax.reload());
                    } else {
                        Swal.fire({ icon: 'error', title: langError, text: resp.message });
                    }
                });
            }
        });
    }
    <?php else: ?>
    function assignPlayerToTeam(playerId, playerName) {
        Swal.fire({
            title: langConfirmAddPlayer,
            html: '<?= lang('Common.want_add_player') ?>'.replace('%s', playerName),
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langAddToTeam,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: 'POST',
                    url: '<?= base_url('player/assign-player-to-team') ?>',
                    data: {
                        player_id: playerId
                        <?php if (session()->get('user_type') === 'coach' && isset($team['id'])): ?>
                        , team_id: '<?= $team['id'] ?>'
                        <?php endif; ?>
                    },
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({ icon: 'success', title: langSuccess, text: response.message, showConfirmButton: false, timer: 1500 })
                                .then(() => {
                                    $('#selectPlayerModal').modal('hide');
                                    $('#searchPlayerInput').val('');
                                    $('#playerSearchResults').addClass('d-none');
                                    $('#table').DataTable().ajax.reload();
                                });
                        } else {
                            Swal.fire({ icon: 'error', title: langError, text: response.message });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: 'error', title: langError, text: langCannotAddPlayer });
                    }
                });
            }
        });
    }

    <?php endif; ?>
</script>

<?php $this->endSection(); ?>