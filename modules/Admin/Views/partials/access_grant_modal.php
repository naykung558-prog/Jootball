<?php
/**
 * Modal ให้สิทธิ์แบบกดครั้งเดียว — ใช้ร่วมกันระหว่าง admin/users กับ admin/coach
 *
 *   openAcademyAccess(userId, name)  → ให้ user เป็นผู้จัดการ academy
 *   openTeamAccess(coachId, name)    → ให้ coach ดูแลสโมสร
 *
 * ฝั่ง server เขียนข้อมูลครบทุกตารางให้เองใน AccessGrantModel
 */
?>
<div class="modal fade" id="accessGrantModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 16px; overflow: hidden;">
            <!-- d-block ทับ flex ของ modal-header เพื่อให้จัดกลางได้ ส่วนปุ่มปิดลอยมุมขวา
                 text-white ต้องใส่ที่ตัว heading เอง เพราะ styles.css กำหนด
                 color: var(--bs-heading-color) ให้ h1-h6 ซึ่งทับสีที่สืบทอดมาจาก parent -->
            <div class="modal-header border-0 d-block text-center position-relative py-4"
                 style="background: #06346D;">
                <h4 class="modal-title fw-bold mb-1 text-white" id="agTitle"></h4>
                <div class="fs-6 text-white opacity-75" id="agSubject"></div>
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3"
                        data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body p-4">
                <!-- สิทธิ์ปัจจุบัน -->
                <h6 class="fw-bold text-secondary mb-2">
                    <i class="ti ti-list-check me-1"></i><?= lang('Common.ag_current_access') ?>
                </h6>
                <div id="agCurrentList" class="mb-4"></div>

                <!-- เพิ่มใหม่ -->
                <h6 class="fw-bold text-secondary mb-2">
                    <i class="ti ti-plus me-1"></i><span id="agAddLabel"></span>
                </h6>

                <div class="row g-2 mb-2">
                    <div class="col-md-8">
                        <input type="text" id="agSearchInput" class="form-control" autocomplete="off">
                    </div>
                    <div class="col-md-4" id="agRoleWrap">
                        <!-- ใช้คีย์ role_* ชุดเดียวกับที่ตารางใช้ จะได้ไม่เรียกคนละชื่อ -->
                        <select id="agRoleSelect" class="form-select">
                            <option value="admin"><?= lang('Common.role_admin') ?></option>
                            <option value="owner"><?= lang('Common.role_owner') ?></option>
                            <option value="viewer"><?= lang('Common.role_viewer') ?></option>
                        </select>
                    </div>
                </div>

                <div id="agSearchResult" style="max-height: 320px; overflow-y: auto;"></div>
            </div>
        </div>
    </div>
</div>

<script>
(function () {
    const AG = {
        mode: null,      // 'academy' | 'team'
        subjectId: null, // user_id | coach_id
        timer: null,
        urls: {
            searchAcademy: '<?= base_url('admin/access/search-academy') ?>',
            searchTeam:    '<?= base_url('admin/access/search-team') ?>',
            userAcademies: '<?= base_url('admin/access/user-academies') ?>',
            coachTeams:    '<?= base_url('admin/access/coach-teams') ?>',
            grantAcademy:  '<?= base_url('admin/access/grant-academy') ?>',
            revokeAcademy: '<?= base_url('admin/access/revoke-academy') ?>',
            grantTeam:     '<?= base_url('admin/access/grant-team') ?>',
            revokeTeam:    '<?= base_url('admin/access/revoke-team') ?>',
        },
        lang: {
            academyTitle:  '<?= lang('Common.ag_academy_access_title') ?>',
            teamTitle:     '<?= lang('Common.ag_team_access_title') ?>',
            addAcademy:    '<?= lang('Common.ag_add_academy') ?>',
            addTeam:       '<?= lang('Common.ag_add_team') ?>',
            searchAcademy: '<?= lang('Common.ag_search_academy') ?>',
            searchTeam:    '<?= lang('Common.ag_search_team') ?>',
            noAccess:      '<?= lang('Common.ag_no_access') ?>',
            noResult:      '<?= lang('Common.ag_no_result') ?>',
            searching:     '<?= lang('Common.ag_searching') ?>',
            grant:         '<?= lang('Common.ag_grant_btn') ?>',
            revoke:        '<?= lang('Common.ag_revoke_btn') ?>',
            confirmTitle:  '<?= lang('Common.ag_confirm_revoke') ?>',
            confirmText:   '<?= lang('Common.ag_confirm_revoke_text') ?>',
            confirmBtn:    '<?= lang('Common.confirm') ?>',
            cancelBtn:     '<?= lang('Common.cancel') ?>',
            error:         '<?= lang('Common.ag_error') ?>',
            noAcademyWarn: '<?= lang('Common.ag_no_academy_warning') ?>',
            notVisible:    '<?= lang('Common.ag_not_visible_to_coach') ?>',
            notVisibleHint: '<?= lang('Common.ag_not_visible_hint') ?>',
            fix:           '<?= lang('Common.ag_fix_btn') ?>',
            noAcademy:     '<?= lang('Common.ag_no_academy_label') ?>',
            noAffiliation: '<?= lang('Common.ag_no_academy_affiliation') ?>',
            noTeam:        '<?= lang('Common.ag_no_team') ?>',
            teamUnit:      '<?= lang('Common.ag_team_unit') ?>',
            roleOwner:     '<?= lang('Common.role_owner') ?>',
            roleAdmin:     '<?= lang('Common.role_admin') ?>',
            roleCoach:     '<?= lang('Common.role_coach') ?>',
            roleViewer:    '<?= lang('Common.role_viewer') ?>',
        }
    };

    function esc(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    function toast(icon, title) {
        Swal.fire({ toast: true, position: 'top-end', icon, title, showConfirmButton: false, timer: 2200 });
    }

    function roleLabel(role) {
        return {
            owner:  AG.lang.roleOwner,
            admin:  AG.lang.roleAdmin,
            coach:  AG.lang.roleCoach,
            viewer: AG.lang.roleViewer
        }[role] || role;
    }

    // สีเดียวกับคอลัมน์ "Academy ที่ดูแล" ในตาราง (UserManagerModel)
    function roleClass(role) {
        return {
            owner:  'bg-danger-subtle text-danger',
            admin:  'bg-primary-subtle text-primary',
            coach:  'bg-info-subtle text-info',
            viewer: 'bg-secondary-subtle text-secondary'
        }[role] || 'bg-secondary-subtle text-secondary';
    }

    // ข้อมูลเก่าบางแถวไม่มีชื่อ จะได้ไม่กลายเป็นแถวว่างที่กดเลือกไม่ถูกว่าอันไหน
    function nameOr(name, id) {
        const n = String(name ?? '').trim();
        return n !== '' ? n : '#' + id;
    }

    /**
     * แถวหนึ่งรายการ: ชื่อหลักตัวหนา + คำอธิบายบรรทัดล่างเสมอ
     * ต้องมีบรรทัดล่างทุกกรณี ไม่งั้นชื่อสองอันจะไปติดกันจนอ่านเป็นคำเดียว
     */
    function rowHtml(title, subtitle, subtitleMuted, btnHtml) {
        const cls = subtitleMuted ? 'text-muted' : 'text-warning-emphasis';
        return '<div class="list-group-item d-flex justify-content-between align-items-center gap-2">'
             + '<div class="min-w-0">'
             +   '<div class="fw-semibold text-break">' + esc(title) + '</div>'
             +   '<small class="' + cls + ' d-block text-break">' + subtitle + '</small>'
             + '</div>'
             + btnHtml
             + '</div>';
    }

    // คำอธิบายของสโมสร → บอกว่าอยู่ Academy ไหน
    function teamSubtitle(academyName) {
        const a = String(academyName ?? '').trim();
        return a !== ''
            ? '<i class="ti ti-school me-1"></i>' + esc(a)
            : '<i class="ti ti-alert-circle me-1"></i>' + esc(AG.lang.noAffiliation);
    }

    // คำอธิบายของ Academy → บอกว่ามีกี่ทีม
    function academySubtitle(teamCount) {
        const n = parseInt(teamCount, 10) || 0;
        return n > 0
            ? '<i class="ti ti-shield me-1"></i>' + n + ' ' + esc(AG.lang.teamUnit)
            : '<i class="ti ti-alert-circle me-1"></i>' + esc(AG.lang.noTeam);
    }

    function open(mode, subjectId, name) {
        AG.mode = mode;
        AG.subjectId = subjectId;

        const isAcademy = mode === 'academy';
        $('#agTitle').text(isAcademy ? AG.lang.academyTitle : AG.lang.teamTitle);
        $('#agSubject').text(name || '');
        $('#agAddLabel').text(isAcademy ? AG.lang.addAcademy : AG.lang.addTeam);
        $('#agSearchInput').val('').attr('placeholder', isAcademy ? AG.lang.searchAcademy : AG.lang.searchTeam);
        $('#agRoleWrap').toggle(isAcademy);   // โหมดโค้ชไม่ต้องเลือก role
        $('#agSearchResult').html('');

        loadCurrent();
        search('');   // โชว์รายการตั้งต้นให้เลือกได้เลยโดยไม่ต้องพิมพ์
        new bootstrap.Modal(document.getElementById('accessGrantModal')).show();
    }

    function loadCurrent() {
        const url = (AG.mode === 'academy' ? AG.urls.userAcademies : AG.urls.coachTeams) + '/' + AG.subjectId;
        $('#agCurrentList').html('<div class="text-muted small">' + AG.lang.searching + '</div>');

        $.getJSON(url, function (res) {
            const rows = (res && res.data) || [];
            if (!rows.length) {
                $('#agCurrentList').html('<div class="text-muted small fst-italic">' + AG.lang.noAccess + '</div>');
                return;
            }

            let html = '<div class="list-group">';
            rows.forEach(function (r) {
                let id, title, subtitle, muted = true, needFix = false;

                if (AG.mode === 'academy') {
                    id = r.academy_id;
                    title = nameOr(r.academy_name, id);
                    subtitle = academySubtitle(r.team_count);
                    muted = (parseInt(r.team_count, 10) || 0) > 0;
                    subtitle += ' <span class="badge ' + roleClass(r.role) + ' ms-1">'
                              + esc(roleLabel(r.role)) + '</span>';
                    if (r.status !== 'approved') {
                        subtitle += '<span class="badge bg-warning-subtle text-warning ms-1">'
                                  + esc(r.status) + '</span>';
                    }
                } else {
                    id = r.team_id;
                    title = nameOr(r.team_name, id);
                    subtitle = teamSubtitle(r.academy_name);
                    muted = String(r.academy_name ?? '').trim() !== '';

                    // ขาดแถวใน coach_invitations — แอดมินเห็นชื่อในทีม แต่โค้ชล็อกอินแล้วไม่เห็นสโมสรนี้
                    // อธิบายให้ครบว่าคืออะไรและต้องทำอะไร ไม่ใช่แค่ป้ายสั้น ๆ
                    if (!r.visible_to_coach) {
                        subtitle += '<div class="alert alert-warning py-1 px-2 mt-1 mb-0 small">'
                                  +   '<i class="ti ti-eye-off me-1"></i>'
                                  +   '<strong>' + esc(AG.lang.notVisible) + '</strong><br>'
                                  +   esc(AG.lang.notVisibleHint)
                                  + '</div>';
                        muted = false;
                        needFix = true;
                    }
                }

                // ปุ่มซ่อม = ให้สิทธิ์ซ้ำ ซึ่งจะเติมแถวที่ขาดให้ครบเอง
                const fixBtn = needFix
                    ? '<button class="btn btn-warning btn-sm flex-shrink-0 text-dark" data-ag-fix="' + id + '">'
                      + '<i class="ti ti-tool"></i> ' + AG.lang.fix + '</button>'
                    : '';

                html += rowHtml(title, subtitle, muted,
                    '<div class="d-inline-flex flex-nowrap gap-1 flex-shrink-0">'
                    + fixBtn
                    + '<button class="btn btn-outline-danger btn-sm" data-ag-revoke="' + id + '">'
                    + '<i class="ti ti-x"></i> ' + AG.lang.revoke + '</button>'
                    + '</div>');
            });
            $('#agCurrentList').html(html + '</div>');
        });
    }

    function search(q) {
        const url = AG.mode === 'academy' ? AG.urls.searchAcademy : AG.urls.searchTeam;
        $('#agSearchResult').html('<div class="text-muted small">' + AG.lang.searching + '</div>');

        $.getJSON(url, { q: q }, function (res) {
            const rows = (res && res.data) || [];
            if (!rows.length) {
                $('#agSearchResult').html('<div class="text-muted small fst-italic">' + AG.lang.noResult + '</div>');
                return;
            }

            const isTeam = AG.mode === 'team';
            let html = '<div class="list-group">';
            rows.forEach(function (r) {
                const subtitle = isTeam ? teamSubtitle(r.academy_name) : academySubtitle(r.team_count);
                const muted = isTeam
                    ? String(r.academy_name ?? '').trim() !== ''
                    : (parseInt(r.team_count, 10) || 0) > 0;

                html += rowHtml(nameOr(r.name, r.id), subtitle, muted,
                    '<button class="btn btn-success btn-sm flex-shrink-0" data-ag-grant="' + r.id + '">'
                    + '<i class="ti ti-check"></i> ' + AG.lang.grant + '</button>');
            });
            $('#agSearchResult').html(html + '</div>');
        });
    }

    $('#agSearchInput').on('input', function () {
        clearTimeout(AG.timer);
        const q = this.value.trim();
        AG.timer = setTimeout(function () { search(q); }, 300);
    });

    $(document).on('click', '[data-ag-grant]', function () {
        const targetId = $(this).data('ag-grant');
        const isAcademy = AG.mode === 'academy';

        $.post(isAcademy ? AG.urls.grantAcademy : AG.urls.grantTeam,
            isAcademy
                ? { user_id: AG.subjectId, academy_id: targetId, role: $('#agRoleSelect').val() }
                : { coach_id: AG.subjectId, team_id: targetId },
            function (res) {
                if (res && res.status) {
                    toast('success', res.message);
                    if (res.no_academy) toast('warning', AG.lang.noAcademyWarn);
                    loadCurrent();
                } else {
                    Swal.fire({ icon: 'error', title: AG.lang.error, text: (res && res.message) || '' });
                }
            }, 'json');
    });

    // ซ่อม = ให้สิทธิ์ทีมเดิมซ้ำ ซึ่ง grantTeamToCoach เติมแถวที่ขาดให้ครบเอง (idempotent)
    $(document).on('click', '[data-ag-fix]', function () {
        $.post(AG.urls.grantTeam,
            { coach_id: AG.subjectId, team_id: $(this).data('ag-fix') },
            function (res) {
                if (res && res.status) {
                    toast('success', res.message);
                    loadCurrent();
                } else {
                    Swal.fire({ icon: 'error', title: AG.lang.error, text: (res && res.message) || '' });
                }
            }, 'json');
    });

    $(document).on('click', '[data-ag-revoke]', function () {
        const targetId = $(this).data('ag-revoke');
        const isAcademy = AG.mode === 'academy';

        Swal.fire({
            title: AG.lang.confirmTitle,
            text: AG.lang.confirmText,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: AG.lang.confirmBtn,
            cancelButtonText: AG.lang.cancelBtn
        }).then(function (r) {
            if (!r.isConfirmed) return;

            $.post(isAcademy ? AG.urls.revokeAcademy : AG.urls.revokeTeam,
                isAcademy
                    ? { user_id: AG.subjectId, academy_id: targetId }
                    : { coach_id: AG.subjectId, team_id: targetId },
                function (res) {
                    if (res && res.status) {
                        toast('success', res.message);
                        loadCurrent();
                    } else {
                        Swal.fire({ icon: 'error', title: AG.lang.error, text: (res && res.message) || '' });
                    }
                }, 'json');
        });
    });

    // เรียกจากปุ่มในตาราง (render มาจากฝั่ง DataTable)
    window.openAcademyAccess = function (userId, name) { open('academy', userId, name); };
    window.openTeamAccess    = function (coachId, name) { open('team', coachId, name); };
})();
</script>
