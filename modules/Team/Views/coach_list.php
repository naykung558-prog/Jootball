<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Team\Views\subnav.php"); ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary"><i class="ti ti-users me-2"></i><?= lang('Common.coach_list_title') ?></h4>
                <p class="text-muted small mb-0"><?= lang('Common.manage_coach_desc') ?></p>
            </div>
            <div class="d-flex gap-2">
                <button class="btn btn-primary rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#search-coach-modal">
                    <i class="ti ti-user-plus me-2"></i><?= lang('Common.add_coach') ?>
                </button>
            </div>
        </div>

        <div class="card-body p-0">
            <div class="table-responsive">
                <table id="table" class="table table-hover align-middle mb-0 text-nowrap" style="width:100%">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="text-center py-3 text-secondary border-0 rounded-start-2 ps-4" width="5%">#</th>
                            <th class="py-3 text-secondary border-0"><?= lang('Common.full_name') ?></th>
                            <th class="py-3 text-secondary border-0"><?= lang('Common.nickname') ?></th>
                            <th class="text-center py-3 text-secondary border-0" width="15%"><?= lang('Common.birth_date') ?></th>
                            <th class="text-center py-3 text-secondary border-0" width="15%"><?= lang('Common.status_label') ?></th>
                            <th class="text-center py-3 text-secondary border-0 rounded-end-2 pe-4" width="15%"><?= lang('Common.manage') ?></th>
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

<!-- Modal ค้นหาและเชิญผู้ฝึกสอน -->
<div class="modal fade" id="search-coach-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary"><i class="ti ti-search me-2"></i><?= lang('Common.search_coach') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="mb-4">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 py-2 ps-4" id="search_name" name="search_name" placeholder="<?= lang('Common.enter_name_to_search') ?>">
                        <button class="btn btn-primary px-4" type="button" onclick="searchCoaches()">
                            <i class="ti ti-search me-2"></i><?= lang('Common.search') ?>
                        </button>
                    </div>
                </div>

                <!-- ผลการค้นหา -->
                <div id="search-results" style="display: none;">
                    <h6 class="mb-3 fw-bold text-muted text-uppercase small"><?= lang('Common.search_results') ?></h6>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center py-2 text-secondary border-0" width="10%">#</th>
                                    <th class="py-2 text-secondary border-0"><?= lang('Common.full_name') ?></th>
                                    <th class="py-2 text-secondary border-0"><?= lang('Common.nickname') ?></th>
                                    <th class="text-center py-2 text-secondary border-0" width="20%"><?= lang('Common.birth_date') ?></th>
                                    <th class="text-center py-2 text-secondary border-0" width="20%"><?= lang('Common.manage') ?></th>
                                </tr>
                            </thead>
                            <tbody id="search-results-tbody">
                            </tbody>
                        </table>
                    </div>
                    <div id="no-results" class="alert alert-info mt-3 border-0 bg-info-subtle text-info rounded-3" style="display: none;">
                        <i class="ti ti-info-circle me-2"></i><?= lang('Common.no_coach_found') ?>
                    </div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.close') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Modal รายละเอียดผู้ฝึกสอน -->
<div class="modal fade" id="coach-profile-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 bg-primary text-white rounded-top-4">
                <h5 class="modal-title text-white"><i class="ti ti-user-circle me-2"></i><?= lang('Common.coach_detail') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div class="row g-4">
                    <!-- Profile Image Section -->
                    <div class="col-md-3 text-center">
                        <div id="coach-image-container" class="mb-3">
                            <div class="coach-profile-placeholder bg-light d-flex align-items-center justify-content-center mx-auto rounded-circle shadow-sm" style="width: 150px; height: 150px;">
                                <i class="ti ti-user text-secondary" style="font-size: 4rem;"></i>
                            </div>
                        </div>
                    </div>

                    <!-- Coach Information -->
                    <div class="col-md-9">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.full_name') ?></label>
                                <p id="coach-fullname" class="h6 fw-bold mb-0 text-dark">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.nickname') ?></label>
                                <p id="coach-nickname" class="h6 fw-bold mb-0 text-dark">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.nationality') ?></label>
                                <p id="coach-nation" class="h6 fw-bold mb-0 text-dark">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.birth_date') ?></label>
                                <p id="coach-birthdate" class="h6 fw-bold mb-0 text-dark">-</p>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.id_card_label') ?></label>
                                <p id="coach-idcard" class="h6 fw-bold mb-0 text-dark">-</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.height_label') ?></label>
                                <p id="coach-height" class="h6 fw-bold mb-0 text-dark">-</p>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.weight_label') ?></label>
                                <p id="coach-weight" class="h6 fw-bold mb-0 text-dark">-</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Certificates Section -->
                <div class="mt-5">
                    <h6 class="mb-3 fw-bold text-primary"><i class="ti ti-certificate me-2"></i><?= lang('Common.certificates_title') ?></h6>
                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th class="text-center py-2 border-0" width="5%">#</th>
                                    <th class="py-2 border-0"><?= lang('Common.association_label') ?></th>
                                    <th class="py-2 border-0"><?= lang('Common.level_label') ?></th>
                                    <th class="py-2 border-0"><?= lang('Common.license_code') ?></th>
                                    <th class="text-center py-2 border-0"><?= lang('Common.year_completed') ?></th>
                                </tr>
                            </thead>
                            <tbody id="coach-certificates-tbody">
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted"><?= lang('Common.no_certificate_data') ?></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
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
    // Language variables for JS
    const langError = '<?= lang('Common.error') ?>';
    const langSuccess = '<?= lang('Common.success') ?>';
    const langCancel = '<?= lang('Common.cancel') ?>';
    const langPleaseEnterInfo = '<?= lang('Common.please_fill_info') ?>';
    const langNoCoachFound = '<?= lang('Common.no_coach_found') ?>';
    const langCannotSearch = '<?= lang('Common.cannot_search') ?>';
    const langConfirmInvite = '<?= lang('Common.confirm_invite_coach') ?>';
    const langInviteDesc = '<?= lang('Common.invite_coach_desc') ?>';
    const langInviteJoin = '<?= lang('Common.invite_join') ?>';
    const langInviteSuccess = '<?= lang('Common.invite_success') ?>';
    const langCannotInvite = '<?= lang('Common.cannot_invite_coach') ?>';
    const langConfirmCancelInvite = '<?= lang('Common.confirm_cancel_invite') ?>';
    const langCancelInviteDesc = '<?= lang('Common.cancel_invite_desc') ?>';
    const langCancelInviteBtn = '<?= lang('Common.cancel_invite_btn') ?>';
    const langDontCancel = '<?= lang('Common.dont_cancel') ?>';
    const langCancelInviteSuccess = '<?= lang('Common.cancel_invite_success') ?>';
    const langCannotCancelInvite = '<?= lang('Common.cannot_cancel_invite') ?>';
    const langConfirmDeleteCoach = '<?= lang('Common.confirm_delete_coach') ?>';
    const langDeleteCoachDesc = '<?= lang('Common.delete_coach_desc') ?>';
    const langRemoveFromTeam = '<?= lang('Common.remove_from_team') ?>';
    const langDeleteCoachSuccess = '<?= lang('Common.delete_coach_success') ?>';
    const langCoachNoLongerMember = '<?= lang('Common.coach_no_longer_member') ?>';
    const langCannotRemoveCoach = '<?= lang('Common.cannot_remove_coach') ?>';
    const langConfirmRemoveClub = '<?= lang('Common.confirm_remove_coach_club') ?>';
    const langRemoveClubDesc = '<?= lang('Common.remove_coach_club_desc') ?>';
    const langRemoveCoachBtn = '<?= lang('Common.remove_coach_btn') ?>';
    const langDontRemove = '<?= lang('Common.dont_remove') ?>';
    const langRemoveSuccess = '<?= lang('Common.remove_coach_success') ?>';
    const langCoachNotified = '<?= lang('Common.coach_notified') ?>';
    const langCannotRemoveClub = '<?= lang('Common.cannot_remove_coach_club') ?>';
    const langAlreadyMember = '<?= lang('Common.already_member') ?>';
    const langInvitationSent = '<?= lang('Common.invitation_sent') ?>';
    const langInvite = '<?= lang('Common.invite') ?>';
    const langLoadingData = '<?= lang('Common.loading_data') ?>';
    const langCannotLoadCoach = '<?= lang('Common.cannot_load_coach') ?>';
    const langCmUnit = '<?= lang('Common.cm_unit') ?>';
    const langKgUnit = '<?= lang('Common.kg_unit') ?>';
    const langNoCertData = '<?= lang('Common.no_certificate_data') ?>';
    const langSearching = '<?= lang('Common.searching_msg') ?>';

    $(document).ready(function () {
        var customLanguage = Object.assign({}, dtLanguage, {
            search: "<?= lang('Common.search_data_label') ?>"
        });

        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            language: customLanguage,
            ajax: {
                url: '<?= base_url('player/get-coachs') ?>',
                method: "POST",
                data: function(d) {
                    <?php if (isset($team['id'])): ?>
                    d.team_id = '<?= $team['id'] ?>';
                    <?php endif; ?>
                }
            },
            dom: '<"d-flex justify-content-between align-items-center flex-wrap px-4 py-3"f>t<"d-flex justify-content-between align-items-center flex-wrap px-4 py-3"ip>',
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
                    data: 'status_badge',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'action',
                    className: 'text-center pe-4'
                }
            ]
        });
    });

    // ฟังก์ชันค้นหาผู้ฝึกสอนจากระบบ
    function searchCoaches() {
        const searchName = $('#search_name').val().trim();

        // ตรวจสอบว่ากรอกข้อมูล
        if (!searchName) {
            Swal.fire({
                icon: 'warning',
                title: langPleaseEnterInfo,
                text: langPleaseEnterInfo,
                confirmButtonColor: '#16478D'
            });
            return;
        }

        // ส่งคำขอค้นหาไปยัง server
        $.ajax({
            method: "POST",
            url: "<?= base_url('player/search-coaches') ?>",
            data: {
                search_name: searchName,
                <?= csrf_token()?> : '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            beforeSend: function () {
                // แสดง loading
                $('#search-results-tbody').html(
                    '<tr><td colspan="5" class="text-center py-4 text-muted"><i class="ti ti-loader animate-spin me-2"></i> ' + langSearching + '</td></tr>'
                    );
                $('#search-results').show();
                $('#no-results').hide();
            },
            success: function (response) {
                if (response.status === 'success' && response.data.length > 0) {
                    let html = '';
                    response.data.forEach((coach, index) => {
                        html += '<tr>';
                        html += '<td class="text-center text-muted fw-bold">' + (index + 1) + '</td>';
                        html += '<td class="fw-medium">' + coach.full_name + '</td>';
                        html += '<td class="text-muted">' + (coach.nick_name || '-') + '</td>';
                        html += '<td class="text-center text-muted">' + coach.birth_date + '</td>';
                        html += '<td class="text-center">';

                        // แสดงปุ่มตามสถานะ
                        if (coach.is_member) {
                            html += '<span class="badge bg-success-subtle text-success rounded-pill px-3">' + langAlreadyMember + '</span>';
                        } else if (coach.is_invited) {
                            html += '<span class="badge bg-warning-subtle text-warning rounded-pill px-3">' + langInvitationSent + '</span>';
                        } else {
                            html +=
                                '<button class="btn btn-sm btn-primary rounded-pill px-3" onclick="inviteCoachFromSearch(' +
                                coach.id + ')">';
                            html += '<i class="ti ti-send me-1"></i> ' + langInvite;
                            html += '</button>';
                        }

                        html += '</td>';
                        html += '</tr>';
                    });
                    $('#search-results-tbody').html(html);
                    $('#no-results').hide();
                } else {
                    $('#search-results-tbody').html('');
                    $('#no-results').show();
                }
            },
            error: function () {
                Swal.fire({
                    icon: 'error',
                    title: langError,
                    text: langCannotSearch,
                    confirmButtonColor: '#dc3545'
                });
                $('#search-results').hide();
            }
        });
    }

    // ฟังก์ชันเชิญผู้ฝึกสอนจากหน้าค้นหา
    function inviteCoachFromSearch(id) {
        Swal.fire({
            title: langConfirmInvite,
            text: langInviteDesc,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#16478D",
            cancelButtonColor: "#6c757d",
            confirmButtonText: langInviteJoin,
            cancelButtonText: langCancel,
            customClass: {
                confirmButton: 'btn btn-primary rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('player/invite-coach') ?>",
                    data: {
                        id: id,
                        <?= csrf_token()?> : '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: "success",
                                title: langInviteSuccess,
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                $('#table').DataTable().ajax.reload();
                                searchCoaches();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: langError,
                                text: response.message,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: langError,
                            text: langCannotInvite,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    }

    // Reset form on modal close
    $('#search-coach-modal').on('hidden.bs.modal', function () {
        $('#search_name').val('');
        $('#search-results').hide();
        $('#search-results-tbody').html('');
        $('#no-results').hide();
    });

    function inviteCoach(id) {
        Swal.fire({
            title: langConfirmInvite,
            text: langInviteDesc,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#16478D",
            cancelButtonColor: "#6c757d",
            confirmButtonText: langInviteJoin,
            cancelButtonText: langCancel,
            customClass: {
                confirmButton: 'btn btn-primary rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('player/invite-coach') ?>",
                    data: {
                        id: id,
                        <?= csrf_token() ?> : '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: "success",
                                title: langInviteSuccess,
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                $('#table').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: langError,
                                text: response.message,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: langError,
                            text: langCannotInvite,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    }

    function cancelInvitation(id) {
        Swal.fire({
            title: langConfirmCancelInvite,
            text: langCancelInviteDesc,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: langCancelInviteBtn,
            cancelButtonText: langDontCancel,
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('player/cancel-invitation') ?>",
                    data: {
                        id: id,
                        <?= csrf_token() ?> : '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: "success",
                                title: langCancelInviteSuccess,
                                text: response.message,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                $('#table').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: langError,
                                text: response.message,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: langError,
                            text: langCannotCancelInvite,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    }

    function deleteCoach(id) {
        Swal.fire({
            title: langConfirmDeleteCoach,
            text: langDeleteCoachDesc,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: langRemoveFromTeam,
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
                    url: "<?= base_url('player/delete-coach') ?>",
                    data: {
                        id: id,
                        <?= csrf_token() ?> : '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: "success",
                                title: langDeleteCoachSuccess,
                                text: langCoachNoLongerMember,
                                showConfirmButton: false,
                                timer: 1500
                            }).then(() => {
                                $('#table').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: langError,
                                text: response.message || langCannotRemoveCoach,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: langError,
                            text: langCannotRemoveCoach,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    }

    function removeCoachFromClub(id) {
        Swal.fire({
            title: langConfirmRemoveClub,
            text: langRemoveClubDesc,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: langRemoveCoachBtn,
            cancelButtonText: langDontRemove,
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('player/remove-coach-from-club') ?>",
                    data: {
                        id: id,
                        <?= csrf_token() ?> : '<?= csrf_hash() ?>'
                    },
                    dataType: 'json',
                    success: function (response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: "success",
                                title: langRemoveSuccess,
                                text: langCoachNotified,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                $('#table').DataTable().ajax.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: "error",
                                title: langError,
                                text: response.message || langCannotRemoveClub,
                                confirmButtonColor: '#dc3545'
                            });
                        }
                    },
                    error: function () {
                        Swal.fire({
                            icon: "error",
                            title: langError,
                            text: langCannotRemoveClub,
                            confirmButtonColor: '#dc3545'
                        });
                    }
                });
            }
        });
    }

    // ฟังก์ชันแสดงรายละเอียดผู้ฝึกสอนใน popup
    function viewCoachProfile(coachId) {
        $.ajax({
            method: "POST",
            url: "<?= base_url('player/get-coach-profile') ?>",
            data: {
                coach_id: coachId,
                <?= csrf_token() ?> : '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            beforeSend: function () {
                Swal.fire({
                    title: langLoadingData,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function (response) {
                Swal.close();

                if (response.status === 'success') {
                    const coach = response.data;

                    // แสดงรูปภาพ
                    if (coach.display_image) {
                        $('#coach-image-container').html(
                            '<img src="' + coach.display_image +
                            '" class="img-fluid rounded-circle shadow-sm" style="width: 150px; height: 150px; object-fit: cover;">'
                        );
                    } else {
                        $('#coach-image-container').html(
                            '<div class="coach-profile-placeholder bg-light d-flex align-items-center justify-content-center mx-auto rounded-circle shadow-sm" style="width: 150px; height: 150px;">' +
                            '<i class="ti ti-user text-secondary" style="font-size: 4rem;"></i>' +
                            '</div>'
                        );
                    }

                    // แสดงข้อมูลผู้ฝึกสอน
                    $('#coach-fullname').text(coach.full_name || '-');
                    $('#coach-nickname').text(coach.nick_name || '-');
                    $('#coach-nation').text(coach.nation || '-');
                    $('#coach-birthdate').text(coach.birth_date || '-');
                    $('#coach-idcard').text(coach.id_card || '-');
                    $('#coach-height').text(coach.height ? coach.height + ' ' + langCmUnit : '-');
                    $('#coach-weight').text(coach.weight ? coach.weight + ' ' + langKgUnit : '-');

                    // แสดงข้อมูลหลักสูตร
                    if (coach.certificates && coach.certificates.length > 0) {
                        let certsHtml = '';
                        coach.certificates.forEach((cert, index) => {
                            certsHtml += '<tr>';
                            certsHtml += '<td class="text-center text-muted fw-bold">' + (index + 1) + '</td>';
                            certsHtml += '<td>' + (cert.organ_name ? cert.organ_name + ' (' + cert
                                .organ_label + ')' : '-') + '</td>';
                            certsHtml += '<td>' + (cert.cert_level_name || '-') + '</td>';
                            certsHtml += '<td>' + (cert.license_code || '-') + '</td>';
                            certsHtml += '<td class="text-center">' + (cert.year || '-') + '</td>';
                            certsHtml += '</tr>';
                        });
                        $('#coach-certificates-tbody').html(certsHtml);
                    } else {
                        $('#coach-certificates-tbody').html(
                            '<tr><td colspan="5" class="text-center py-4 text-muted">' + langNoCertData + '</td></tr>'
                        );
                    }

                    // แสดง modal
                    $('#coach-profile-modal').modal('show');
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: langError,
                        text: response.message || langCannotLoadCoach,
                        confirmButtonColor: '#dc3545'
                    });
                }
            },
            error: function () {
                Swal.close();
                Swal.fire({
                    icon: 'error',
                    title: langError,
                    text: langCannotLoadCoach,
                    confirmButtonColor: '#dc3545'
                });
            }
        });
    }

    function createPDF() {
        let url = "<?= base_url('team/player-document-list') ?>";
        <?php
        if (session()->get('user_type') === 'coach' && isset($team['id'])): ?>
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
    <?php if (session()->getFlashdata('success')): ?>
        Swal.fire({
            icon: 'success',
            title: langSuccess,
            text: '<?= session()->getFlashdata('success') ?>',
            timer: 2500,
            showConfirmButton: false
        });
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')): ?>
        Swal.fire({
            icon: 'error',
            title: langError,
            text: '<?= session()->getFlashdata('error') ?>',
            confirmButtonColor: '#dc3545'
        });
    <?php endif; ?>
</script>
<?php $this->endSection(); ?>