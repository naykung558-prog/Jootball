<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<style>
:root {
    --tour-radius: 16px;
    --tour-radius-sm: 10px;
    --tour-border: #eef1f6;
    --tour-muted: #6b7585;
    --tour-text: #1f2a44;
    --tour-row-hover: #f6f9ff;
}

/* Full-width override is now applied globally in admin_layout.php */

/* ===== Header / Hero card ===== */
.tour-hero {
    background: linear-gradient(135deg, #0b1e44 0%, #173a7a 55%, #2453b5 100%);
    border-radius: var(--tour-radius);
    color: #fff;
    padding: 28px 32px;
    box-shadow: 0 12px 32px rgba(11, 30, 68, .18);
    position: relative;
    overflow: hidden;
}
.tour-hero::before,
.tour-hero::after {
    content: "";
    position: absolute;
    border-radius: 50%;
    background: rgba(255, 255, 255, .08);
    pointer-events: none;
}
.tour-hero::before { width: 220px; height: 220px; right: -60px; top: -80px; }
.tour-hero::after  { width: 140px; height: 140px; right: 120px; bottom: -70px; background: rgba(255,255,255,.05); }

.tour-hero .hero-icon {
    width: 56px; height: 56px;
    background: rgba(255, 255, 255, .15);
    border: 1px solid rgba(255, 255, 255, .25);
    border-radius: 16px;
    display: inline-flex; align-items: center; justify-content: center;
    backdrop-filter: blur(6px);
}
.tour-hero .hero-icon i { font-size: 1.6rem; }
.tour-hero h4 { font-weight: 700; letter-spacing: .2px; margin: 0; }
.tour-hero .hero-sub { opacity: .8; font-size: .9rem; margin-top: 2px; }

.btn-tour-create {
    background: #ffffff;
    color: #0b1e44 !important;
    font-weight: 700;
    border: none;
    border-radius: 999px;
    padding: 10px 22px;
    box-shadow: 0 8px 18px rgba(0, 0, 0, .18);
    transition: transform .15s ease, box-shadow .15s ease;
    white-space: nowrap;
}
.btn-tour-create:hover {
    transform: translateY(-1px);
    box-shadow: 0 12px 24px rgba(0, 0, 0, .25);
    background: #f8fafc;
}
.btn-tour-create i { color: #2453b5; }

/* ===== Data card / Table ===== */
.tour-card {
    background: #fff;
    border-radius: var(--tour-radius);
    border: 1px solid var(--tour-border);
    box-shadow: 0 6px 22px rgba(15, 23, 42, .04);
    /* overflow visible so dropdowns can escape the card */
    overflow: visible;
}
.tour-card .table-responsive { overflow: visible; }
@media (max-width: 768px) {
    /* mobile keeps horizontal scroll but dropdown uses position:fixed via data-bs-strategy */
    .tour-card .table-responsive { overflow-x: auto; overflow-y: visible; }
}
/* Make sure dropdown menu floats above sticky/zindex layers */
.tour-action-menu { z-index: 1080 !important; }

#table.dataTable {
    border-collapse: separate !important;
    border-spacing: 0;
}
#table thead th {
    background: #f7f9fc;
    color: #4a5568;
    font-weight: 600;
    font-size: .82rem;
    letter-spacing: .3px;
    text-transform: uppercase;
    border-bottom: 1px solid var(--tour-border) !important;
    padding: 14px 10px;
    white-space: nowrap;
}
#table tbody td {
    padding: 14px 10px;
    border-bottom: 1px solid var(--tour-border);
    color: var(--tour-text);
    font-size: .92rem;
    vertical-align: middle;
}
#table tbody tr:hover td { background: var(--tour-row-hover); }
#table tbody tr:last-child td { border-bottom: 0; }

/* index numbering chip */
#table tbody td:first-child {
    font-weight: 600;
    color: var(--tour-muted);
}

/* ===== Logo cell ===== */
.tour-logo-wrap {
    width: 52px; height: 52px;
    border-radius: 14px;
    margin: 0 auto;
    display: inline-flex; align-items: center; justify-content: center;
    background: #f1f4fa;
    border: 1px solid var(--tour-border);
    overflow: hidden;
    box-shadow: 0 2px 6px rgba(15, 23, 42, .05);
}
.tour-logo-img {
    width: 100%; height: 100%;
    object-fit: cover;
    display: block;
}
.tour-logo-img.is-fallback {
    object-fit: contain;
    padding: 8px;
    opacity: .55;
}

/* ===== Tournament name cell ===== */
#table tbody td:nth-child(3) a {
    color: var(--tour-text);
    font-weight: 600;
    text-decoration: none;
    border-bottom: 1px dashed transparent;
    transition: color .15s ease, border-color .15s ease;
}
#table tbody td:nth-child(3) a:hover {
    color: #2453b5;
    border-bottom-color: #2453b5;
}

/* ===== Generation badge ===== */
.gen-badge {
    display: inline-block;
    background: #eef3ff;
    color: #2453b5;
    border-radius: 999px;
    padding: 4px 12px;
    font-size: .8rem;
    font-weight: 600;
}

/* ===== Toggle switch ===== */
.jt-switch {
    position: relative;
    display: inline-block;
    width: 42px; height: 24px;
    background: #d3d8e0;
    border-radius: 24px;
    transition: background .25s ease;
    flex-shrink: 0;
    box-shadow: inset 0 1px 2px rgba(0, 0, 0, .08);
}
.jt-switch.on { background: #28a745; }
.jt-switch-knob {
    position: absolute;
    top: 2px; left: 2px;
    width: 20px; height: 20px;
    background: #fff;
    border-radius: 50%;
    transition: left .25s ease, transform .15s ease;
    box-shadow: 0 1px 3px rgba(0, 0, 0, .25);
}
.jt-switch.on .jt-switch-knob { left: 20px; }
.jt-switch:hover .jt-switch-knob { transform: scale(1.05); }

/* ===== Action dropdown button ===== */
.tour-action-btn {
    width: 36px; height: 36px;
    display: inline-flex; align-items: center; justify-content: center;
    background: #f3f5fa;
    border: 1px solid var(--tour-border);
    color: #4a5568;
    border-radius: 10px;
    transition: background .15s ease, color .15s ease, border-color .15s ease;
}
.tour-action-btn:hover,
.tour-action-btn[aria-expanded="true"] {
    background: #2453b5;
    color: #fff !important;
    border-color: #2453b5;
}
.dropdown-menu.tour-action-menu {
    border-radius: 12px;
    padding: 6px;
    border: 1px solid var(--tour-border);
    box-shadow: 0 14px 30px rgba(15, 23, 42, .12);
    min-width: 240px;
}
.tour-action-menu .dropdown-header {
    color: var(--tour-muted);
    font-size: .72rem;
    text-transform: uppercase;
    letter-spacing: .5px;
    padding: 6px 12px 4px;
}
.tour-action-menu .dropdown-item {
    padding: 8px 12px;
    border-radius: 8px;
    font-size: .88rem;
    color: var(--tour-text);
    transition: background .12s ease;
}
.tour-action-menu .dropdown-item:hover { background: #eef3ff; color: #2453b5; }
.tour-action-menu .dropdown-item.text-danger:hover { background: #fdecec; color: #c83232 !important; }
.tour-action-menu .dropdown-item i { width: 18px; }

.pending-chart-table th,
.pending-chart-table td {
    font-size: .86rem;
    vertical-align: middle;
}
.pending-chart-empty {
    color: var(--tour-muted);
    padding: 28px 12px;
    text-align: center;
}

/* ===== DataTables UI tweaks ===== */
.dataTables_wrapper { padding: 14px 20px 18px; }
.dataTables_wrapper .dataTables_length select,
.dataTables_wrapper .dataTables_filter input {
    border: 1px solid var(--tour-border);
    border-radius: 10px;
    padding: 6px 12px;
    box-shadow: none;
}
.dataTables_wrapper .dataTables_filter input:focus,
.dataTables_wrapper .dataTables_length select:focus {
    border-color: #2453b5;
    outline: none;
    box-shadow: 0 0 0 3px rgba(36, 83, 181, .12);
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 8px !important;
    border: 1px solid transparent !important;
    margin: 0 2px;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: #2453b5 !important;
    color: #fff !important;
    border-color: #2453b5 !important;
}
.dataTables_wrapper .dataTables_info { color: var(--tour-muted); }

/* ===== Responsive ===== */
@media (max-width: 768px) {
    .tour-hero { padding: 20px; }
    .tour-hero h4 { font-size: 1.1rem; }
    .btn-tour-create { padding: 8px 18px; font-size: .85rem; }
    #table thead th, #table tbody td { padding: 12px 8px; font-size: .85rem; }
    .tour-logo-wrap { width: 44px; height: 44px; border-radius: 12px; }
}
</style>

<div class="tour-hero mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 position-relative" style="z-index: 1;">
        <div class="d-flex align-items-center gap-3">
            <div class="hero-icon">
                <i class="ti ti-trophy text-white"></i>
            </div>
            <div>
                <h4 class="text-white"><?= lang('Common.competitions') ?></h4>
                <div class="hero-sub"><?= lang('Common.manage_all_competitions') ?></div>
            </div>
        </div>
        <a href="<?= base_url('admin/tournament/tournament-manage/') ?>" class="btn btn-tour-create">
            <i class="ti ti-plus me-2"></i><?= lang('Common.create_competition') ?>
        </a>
    </div>
</div>

<div class="tour-card">
    <div class="table-responsive">
        <table id="table" class="table table-hover align-middle mb-0" style="width: 100%;">
            <thead>
                <tr>
                    <th class="text-center" width="5%"><?= lang('Common.order_number') ?></th>
                    <th class="text-center" width="9%"><?= lang('Common.logo') ?></th>
                    <th class="text-start" width="34%"><?= lang('Common.competition_name') ?></th>
                    <th class="text-center" width="12%"><?= lang('Common.generation') ?></th>
                    <th class="text-center" width="13%"><?= lang('Common.status') ?></th>
                    <th class="text-center" width="13%"><?= lang('Common.registration_status') ?></th>
                    <th class="text-center" width="9%"><?= lang('Common.tools') ?></th>
                    <th class="d-none"></th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
</div>

<div class="modal fade" id="pendingChartPlayersModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-0">นักเตะที่ค้างในแผงรูป</h5>
                    <small class="text-muted">แสดงเฉพาะรายการที่ทัวร์นาเมนต์ยังไม่เริ่ม และนักเตะไม่ได้อยู่ในทีมปัจจุบันแล้ว</small>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="pendingChartPlayersLoading" class="pending-chart-empty">กำลังโหลดข้อมูล...</div>
                <div class="table-responsive d-none" id="pendingChartPlayersTableWrap">
                    <table class="table table-hover pending-chart-table mb-0">
                        <thead>
                            <tr>
                                <th width="8%" class="text-center">ID</th>
                                <th width="12%" class="text-center">ไอดีนักเตะ</th>
                                <th>ชื่อ-นามสกุล</th>
                                <th width="10%" class="text-center">เบอร์</th>
                                <th>ทีม</th>
                                <th width="14%" class="text-center">ไอดีทีม</th>
                                <th width="16%" class="text-center">วันเริ่ม</th>
                                <th width="10%" class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody id="pendingChartPlayersBody"></tbody>
                    </table>
                </div>
                <div id="pendingChartPlayersEmpty" class="pending-chart-empty d-none">ไม่พบรายการนักเตะที่ค้างในแผงรูป</div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
<script>
    const langConfirmDelete = '<?= lang("Common.confirm_delete_question") ?>';
    const langCannotRecover = '<?= lang("Common.cannot_recover_data") ?>';
    const langConfirmDeleteBtn = '<?= lang("Common.confirm_delete_now") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';
    const langDeleteSuccess = '<?= lang("Common.delete_data_success") ?>';
    const langErrorOccurred = '<?= lang("Common.error_occurred") ?>';
    const langCannotDeleteData = '<?= lang("Common.cannot_delete_data") ?>';
    const langConfirmClone = '<?= lang("Common.confirm_clone") ?>';
    const langConfirmCloneText = '<?= lang("Common.confirm_clone_text") ?>';
    const langConfirmCloneBtn = '<?= lang("Common.yes_clone") ?>';
    const langStatusActive = '<?= lang("Common.status_active") ?>';
    const langStatusSuspended = '<?= lang("Common.status_suspended") ?>';
    const langRegistrationOpen = '<?= lang("Common.registration_open") ?>';
    const langRegistrationClosed = '<?= lang("Common.registration_closed") ?>';
    const langUpdateSuccess = '<?= lang("Common.save_success") ?>';
    const toggleUrl = '<?= base_url("admin/tournament/toggle-tournament-field") ?>';
    const pendingChartPlayersUrl = '<?= base_url("admin/tournament/pending-chart-players") ?>';
    const deletePendingChartPlayerUrl = '<?= base_url("admin/tournament/delete-pending-chart-player") ?>';
    const csrfTokenName = '<?= csrf_token() ?>';
    const csrfTokenValue = '<?= csrf_hash() ?>';

    const fieldConfig = {
        is_active: {
            on:  { label: langStatusActive, color: '#28a745' },
            off: { label: langStatusSuspended, color: '#dc3545' }
        },
        is_registration_open: {
            on:  { label: langRegistrationOpen, color: '#0dcaf0' },
            off: { label: langRegistrationClosed, color: '#6c757d' }
        }
    };

    function toggleField(el) {
        const $el = $(el);
        const id = $el.data('id');
        const field = $el.data('field');
        const newValue = el.checked ? 1 : 0;
        const $wrap = $el.closest('label');
        const $track = $wrap.find('.jt-switch');
        const $label = $wrap.find('.toggle-label');

        $wrap.css('opacity', '.5').css('pointer-events', 'none');

        $.ajax({
            method: 'POST',
            url: toggleUrl,
            data: {
                id: id,
                field: field,
                value: newValue,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(res) {
                if (res.status === 'success') {
                    const cfg = fieldConfig[field];
                    const state = newValue === 1 ? cfg.on : cfg.off;
                    $track.toggleClass('on', newValue === 1);
                    if (field === 'is_active') {
                        $track.css('background', newValue ? '#28a745' : '#ccc');
                    }
                    $label.text(state.label).css('color', state.color);

                    Swal.mixin({
                        toast: true, position: 'top-end',
                        showConfirmButton: false, timer: 1500
                    }).fire({ icon: 'success', title: langUpdateSuccess });
                } else {
                    el.checked = !el.checked;
                    Swal.fire({ icon: 'warning', title: res.msg || langErrorOccurred });
                }
            },
            error: function() {
                el.checked = !el.checked;
                Swal.fire({ icon: 'error', title: langErrorOccurred });
            },
            complete: function() {
                $wrap.css('opacity', '1').css('pointer-events', 'auto');
            }
        });
    }

    $(document).ready(function() {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('admin/tournament/get-tournaments') ?>',
                method: "GET"
            },
            order: [[ 7, 'desc' ]],
            columnDefs: [{
                targets: [0, 1, 6],
                orderable: false
            }, {
                targets: 7,
                visible: false
            }],
            columns: [{
                    data: '',
                    className: 'text-center'
                },
                {
                    data: 'logo',
                    className: 'text-center'
                },
                {
                    data: 'name',
                    className: 'text-start'
                },
                {
                    data: 'generation',
                    className: 'text-center'
                },
                {
                    data: 'is_active',
                    className: 'text-center'
                },
                {
                    data: 'is_registration_open',
                    className: 'text-center'
                },
                {
                    data: 'action',
                    className: 'text-center'
                },
                {
                    data: 'tour_id',
                    className: 'd-none'
                }
            ]

        });
    })


    function deleteTournament(tou_id) {
        Swal.fire({
            title: langConfirmDelete,
            text: langCannotRecover,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: langConfirmDeleteBtn,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('admin/tournament/delete-tournament') ?>",
                    data: {
                        id: tou_id
                    },
                    success: function(response) {
                        Swal.fire({
                            position: "center",
                            icon: "success",
                            title: langDeleteSuccess,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    },
                    error: function() {
                        Swal.fire({
                            icon: "error",
                            title: langErrorOccurred,
                            text: langCannotDeleteData,
                        });
                    }
                });
            }
        });
    }

    function confirmClone(url) {
        Swal.fire({
            title: langConfirmClone,
            text: langConfirmCloneText,
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#6c757d", // secondary color matches button
            cancelButtonColor: "#d33",
            confirmButtonText: langConfirmCloneBtn,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = url;
            }
        });
    }

    function escapeHtml(value) {
        return String(value ?? '').replace(/[&<>"']/g, function(match) {
            return {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            }[match];
        });
    }

    function formatDateTime(value) {
        if (!value) return '-';
        const normalized = String(value).replace(' ', 'T');
        const date = new Date(normalized);
        if (Number.isNaN(date.getTime())) return escapeHtml(value);
        return date.toLocaleString('th-TH', {
            year: 'numeric',
            month: '2-digit',
            day: '2-digit',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function setPendingChartPlayersState(state) {
        $('#pendingChartPlayersLoading').toggleClass('d-none', state !== 'loading');
        $('#pendingChartPlayersTableWrap').toggleClass('d-none', state !== 'table');
        $('#pendingChartPlayersEmpty').toggleClass('d-none', state !== 'empty');
    }

    function openPendingChartPlayers(tourId) {
        const modalEl = document.getElementById('pendingChartPlayersModal');
        const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
        $('#pendingChartPlayersBody').empty();
        setPendingChartPlayersState('loading');
        modal.show();

        $.ajax({
            method: 'GET',
            url: pendingChartPlayersUrl + '/' + tourId,
            success: function(res) {
                if (res.status !== 'success') {
                    setPendingChartPlayersState('empty');
                    Swal.fire({ icon: 'warning', title: res.msg || langErrorOccurred });
                    return;
                }

                const rows = res.data || [];
                if (!rows.length) {
                    setPendingChartPlayersState('empty');
                    return;
                }

                const html = rows.map(function(row) {
                    const fullName = [row.first_name, row.last_name].filter(Boolean).join(' ') || '-';
                    return `
                        <tr data-submitted-id="${escapeHtml(row.submitted_id)}">
                            <td class="text-center">${escapeHtml(row.submitted_id)}</td>
                            <td class="text-center">${escapeHtml(row.player_id)}</td>
                            <td>${escapeHtml(fullName)}</td>
                            <td class="text-center">${escapeHtml(row.shirt_number || '-')}</td>
                            <td>${escapeHtml(row.team_name || '-')}</td>
                            <td class="text-center">${escapeHtml(row.team_id || '-')}</td>
                            <td class="text-center">${formatDateTime(row.tournament_start_date)}</td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-danger" onclick="deletePendingChartPlayer(${Number(row.submitted_id) || 0})">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </td>
                        </tr>
                    `;
                }).join('');

                $('#pendingChartPlayersBody').html(html);
                setPendingChartPlayersState('table');
            },
            error: function() {
                setPendingChartPlayersState('empty');
                Swal.fire({ icon: 'error', title: langErrorOccurred });
            }
        });
    }

    function deletePendingChartPlayer(submittedId) {
        Swal.fire({
            title: langConfirmDelete,
            text: 'ลบรายการนี้ออกจาก tournament_submitted_players หรือไม่',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: langConfirmDeleteBtn,
            cancelButtonText: langCancel
        }).then((result) => {
            if (!result.isConfirmed) return;

            const postData = {
                submitted_id: submittedId
            };
            postData[csrfTokenName] = csrfTokenValue;

            $.ajax({
                method: 'POST',
                url: deletePendingChartPlayerUrl,
                data: postData,
                success: function(res) {
                    if (res.status === 'success') {
                        const $row = $('#pendingChartPlayersBody tr[data-submitted-id="' + submittedId + '"]');
                        $row.remove();
                        if (!$('#pendingChartPlayersBody tr').length) {
                            setPendingChartPlayersState('empty');
                        }
                        Swal.fire({
                            position: 'center',
                            icon: 'success',
                            title: res.msg || langDeleteSuccess,
                            showConfirmButton: false,
                            timer: 1200
                        });
                    } else {
                        Swal.fire({ icon: 'warning', title: res.msg || langErrorOccurred });
                    }
                },
                error: function() {
                    Swal.fire({ icon: 'error', title: langErrorOccurred });
                }
            });
        });
    }
</script>
<?php $this->endSection() ?>
