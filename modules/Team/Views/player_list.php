<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Team\Views\subnav.php"); ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary"><i class="fa fa-users me-2"></i><?= lang('Common.player_list') ?></h4>
                <p class="text-muted small mb-0"><?= lang('Common.manage_player_desc') ?></p>
            </div>
            <div class="d-flex gap-2">
                <?php
                $manage_url = base_url('player/manage');
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
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success"><i class="fa fa-search me-2"></i><?= lang('Common.add_player_from_system') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="alert alert-info border-0 shadow-sm mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <?= lang('Common.search_player_hint') ?>
                </div>

                <div class="input-group mb-4">
                    <input type="text" class="form-control bg-light border-0" id="searchPlayerInput" placeholder="<?= lang('Common.enter_id_or_name') ?>" autocomplete="off">
                    <button class="btn btn-success" type="button" id="searchPlayerBtn">
                        <i class="fa fa-search me-1"></i> <?= lang('Common.search') ?>
                    </button>
                </div>

                <div id="playerSearchResults" class="d-none">
                    <h6 class="text-muted mb-3"><i class="fa fa-users me-2"></i><?= lang('Common.search_results') ?></h6>
                    <div id="playerResultsList" class="list-group">
                        <!-- Results will be populated here -->
                    </div>
                </div>

                <div id="noPlayerResults" class="text-center py-5 d-none">
                    <i class="fa fa-sad-tear fa-3x text-muted mb-3"></i>
                    <p class="text-muted"><?= lang('Common.no_player_found') ?></p>
                </div>

                <div id="playerSearchLoading" class="text-center py-5 d-none">
                    <div class="spinner-border text-success" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="text-muted mt-2"><?= lang('Common.searching_msg') ?></p>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.close') ?></button>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('player/get-players') ?>',
                method: "POST",
                <?php if (isset($team['id'])): ?>
                data: function(d) {
                    d.team_id = '<?= $team['id'] ?>';
                }
                <?php endif; ?>
            },
            dom: '<"d-flex justify-content-between align-items-center flex-wrap px-4 py-3"f>t<"d-flex justify-content-between align-items-center flex-wrap px-4 py-3"ip>',
            order: [[2, 'asc']],
            columnDefs: [{
                    targets: 0,
                    orderable: false
                },
                {
                    targets: -1,
                    orderable: false
                }
            ],
            columns: [{
                    data: '',
                    className: 'text-center text-muted fw-bold ps-4'
                },
                {
                    data: 'image',
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        // Assuming data is the image URL or HTML tag. If it's just URL, wrap it.
                        // If the backend returns HTML <img> tag, we might need to parse or style it via CSS targeting.
                        // For now, keeping as is, but adding a class wrapper if needed via createdCell if I could.
                        return data;
                    }
                },
                {
                    data: 'shirt_number',
                    className: 'text-center fw-bold text-primary fs-5'
                },
                {
                    data: 'full_name',
                    name: 'first_name',
                    className: 'fw-medium'
                },
                {
                    data: 'nick_name',
                    className: 'text-muted'
                },
                {
                    data: 'birth_date',
                    className: 'text-center text-muted',
                },
                {
                    data: 'player_sub_position',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'action',
                    className: 'text-center pe-4'
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
        let url = "<?= base_url('team/player-document-list') ?>";
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
        $('#playerSearchResults').addClass('d-none');
        $('#noPlayerResults').addClass('d-none');
        $('#playerSearchLoading').removeClass('d-none');

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
                Swal.fire({
                    icon: 'error',
                    title: langError,
                    text: langSearchError
                });
            }
        });
    }

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
                            Swal.fire({
                                icon: 'success',
                                title: langSuccess,
                                text: response.message,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#selectPlayerModal').modal('hide');
                                $('#searchPlayerInput').val('');
                                $('#playerSearchResults').addClass('d-none');
                                $('#table').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: langError,
                                text: response.message
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: langError,
                            text: langCannotAddPlayer
                        });
                    }
                });
            }
        });
    }
</script>
<?php $this->endSection(); ?>