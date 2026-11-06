<?php $this->extend('template/layout') ?>

<?php $this->section('css'); ?>
<style>
/* ---- FullCalendar overrides ---- */
.fc .fc-toolbar-title { font-size: 1.1rem; font-weight: 700; }
.fc .fc-button {
    border-radius: 20px !important;
    padding: 0.25rem 0.8rem !important;
    font-size: 0.82rem !important;
    box-shadow: none !important;
}
.fc .fc-button-primary {
    background-color: #16478d !important;
    border-color: #16478d !important;
}
.fc .fc-button-primary:not(:disabled).fc-button-active,
.fc .fc-button-primary:not(:disabled):active {
    background-color: #0d3166 !important;
    border-color: #0d3166 !important;
}
.fc .fc-today-button:disabled { opacity: 0.5; }
.fc-daygrid-day.fc-day-today { background: #eff6ff !important; }
.fc-event {
    cursor: pointer;
    border-radius: 5px !important;
    font-size: 0.78rem !important;
    padding: 1px 4px !important;
}
.fc-event-cancelled {
    text-decoration: line-through !important;
    opacity: 0.55 !important;
}
.fc-event-completed { opacity: 0.72 !important; }
/* ---- Legend ---- */
.legend-dot { width: 11px; height: 11px; border-radius: 50%; display: inline-block; flex-shrink: 0; }
/* ---- Offcanvas detail ---- */
#sessionOffcanvas .oc-header-bar {
    height: 4px;
    border-radius: 4px 4px 0 0;
    transition: background .3s;
}
#sessionOffcanvas .detail-row {
    display: flex;
    align-items: flex-start;
    gap: 10px;
    margin-bottom: 0.65rem;
    font-size: 0.9rem;
}
#sessionOffcanvas .detail-icon {
    width: 18px;
    color: #6c757d;
    flex-shrink: 0;
    padding-top: 2px;
    text-align: center;
}
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <!-- Header -->
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-calendar-alt me-2"></i><?= lang('Common.training_sessions') ?>
                </h4>
                <?php if (!empty($academy['name'])): ?>
                    <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
                <?php endif; ?>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <!-- Group filter -->
                <select id="filterGroup" class="form-select form-select-sm rounded-pill" style="width:auto;min-width:140px;">
                    <option value="">ทุกกลุ่ม</option>
                    <?php foreach ($groups as $g): ?>
                    <option value="<?= (int)$g['id'] ?>"><?= esc($g['name']) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php if (!empty($perms['perm_create_training_session'])): ?>
                <a href="<?= base_url('academy/training-session/manage/' . (int)$academy['id']) ?>"
                   class="btn btn-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-plus me-1"></i>สร้างแผนใหม่
                </a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Legend -->
        <div class="px-4 py-2 border-bottom d-flex flex-wrap gap-3 align-items-center">
            <small class="text-muted fw-semibold me-1">ประเภท:</small>
            <span class="d-flex align-items-center gap-1 small"><span class="legend-dot" style="background:#0d6efd;"></span>ซ้อมปกติ</span>
            <span class="d-flex align-items-center gap-1 small"><span class="legend-dot" style="background:#fd7e14;"></span>พิเศษ</span>
            <span class="d-flex align-items-center gap-1 small"><span class="legend-dot" style="background:#198754;"></span>แข่งขัน</span>
            <span class="d-flex align-items-center gap-1 small"><span class="legend-dot" style="background:#0dcaf0;"></span>ฟิตเนส</span>
            <span class="ms-2 d-flex align-items-center gap-1 small text-muted">
                <span class="legend-dot" style="background:#adb5bd;"></span>ยกเลิก
            </span>
        </div>

        <!-- Calendar -->
        <div class="card-body px-4 pb-4 pt-3">
            <div id="calendar"></div>
        </div>
    </div>
</div>

<!-- ======= Offcanvas: Session Detail ======= -->
<div class="offcanvas offcanvas-end shadow-lg" tabindex="-1" id="sessionOffcanvas"
     style="width:340px;border-left:0;">
    <div id="ocHeaderBar" class="oc-header-bar" style="background:#16478d;"></div>
    <div class="offcanvas-header py-3 px-4 border-bottom">
        <h6 class="offcanvas-title fw-bold text-dark lh-base" id="ocTitle" style="max-width:260px;">—</h6>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
    </div>
    <div class="offcanvas-body px-4 py-3 d-flex flex-column">
        <!-- Details -->
        <div class="detail-row">
            <span class="detail-icon"><i class="fas fa-calendar-day"></i></span>
            <span id="ocDate" class="fw-semibold">—</span>
        </div>
        <div class="detail-row">
            <span class="detail-icon"><i class="fas fa-clock"></i></span>
            <span id="ocTime">—</span>
        </div>
        <div class="detail-row">
            <span class="detail-icon"><i class="fas fa-users"></i></span>
            <span id="ocGroup">—</span>
        </div>
        <div class="detail-row">
            <span class="detail-icon"><i class="fas fa-user-tie"></i></span>
            <span id="ocCoach" class="text-muted">—</span>
        </div>
        <div class="detail-row" id="ocLocationRow">
            <span class="detail-icon"><i class="fas fa-map-marker-alt"></i></span>
            <span id="ocLocation" class="text-muted">—</span>
        </div>
        <div class="d-flex gap-2 mb-3 flex-wrap">
            <span id="ocTypeBadge"></span>
            <span id="ocStatusBadge"></span>
            <span id="ocAttachBadge" style="display:none;">
                <a href="#" id="ocAttachLink" class="badge bg-info rounded-pill text-decoration-none">
                    <i class="fas fa-paperclip me-1"></i><span id="ocAttachCount">0</span> เอกสาร
                </a>
            </span>
        </div>
        <div class="detail-row" id="ocNotesRow" style="display:none;">
            <span class="detail-icon"><i class="fas fa-sticky-note"></i></span>
            <span id="ocNotes" class="text-muted small" style="white-space:pre-line;"></span>
        </div>

        <hr class="my-2">

        <!-- Action buttons -->
        <div class="d-grid gap-2 mt-auto">
            <a id="ocBtnView" href="#" class="btn btn-primary rounded-pill btn-sm">
                <i class="fas fa-eye me-1"></i>ดูรายละเอียด / เช็คชื่อ
            </a>
            <?php if (!empty($perms['perm_create_training_session'])): ?>
            <a id="ocBtnEdit" href="#" class="btn btn-outline-secondary rounded-pill btn-sm">
                <i class="fas fa-edit me-1"></i>แก้ไขแผน
            </a>
            <?php endif; ?>
            <?php if (!empty($perms['perm_duplicate_session'])): ?>
            <button id="ocBtnCopy" type="button" class="btn btn-outline-success rounded-pill btn-sm"
                    data-session-id="" data-session-date="">
                <i class="fas fa-copy me-1"></i>คัดลอกแผนนี้
            </button>
            <button id="ocBtnCopyDay" type="button" class="btn btn-success rounded-pill btn-sm"
                    data-session-date="">
                <i class="fas fa-calendar-plus me-1"></i>คัดลอกทุกแผนของวันนี้
            </button>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/locales-all.global.min.js"></script>
<script>
(function () {
    const academyId  = <?= (int)$academy['id'] ?>;
    const eventsUrl  = '<?= base_url('academy/training-session/events/' . (int)$academy['id']) ?>';
    const dupUrl     = '<?= base_url('academy/training-session/duplicate') ?>';
    const dupDayUrl  = '<?= base_url('academy/training-session/duplicate-day') ?>';
    const detailBase = '<?= base_url('academy/training-session/detail/' . (int)$academy['id']) . '/' ?>';
    const editBase   = '<?= base_url('academy/training-session/manage/' . (int)$academy['id']) . '/' ?>';

    // ---- Helpers ----
    const thMonths  = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
    const thDays    = ['อาทิตย์','จันทร์','อังคาร','พุธ','พฤหัสบดี','ศุกร์','เสาร์'];
    const typeLabel = { regular:'ซ้อมปกติ', special:'พิเศษ', match:'แข่งขัน', fitness:'ฟิตเนส' };
    const typeColor = { regular:'primary',  special:'warning', match:'success',  fitness:'info' };
    const statusLabel = { planned:'วางแผน', completed:'เสร็จแล้ว', cancelled:'ยกเลิก' };
    const statusColor = { planned:'secondary', completed:'success', cancelled:'danger' };

    function formatThaiDate(dateStr) {
        var d = new Date(dateStr + 'T00:00:00');
        return thDays[d.getDay()] + ' ' + d.getDate() + ' ' + thMonths[d.getMonth() + 1] + ' ' + (d.getFullYear() + 543);
    }

    // ---- FullCalendar ----
    var calendar = new FullCalendar.Calendar(document.getElementById('calendar'), {
        locale: 'th',
        initialView: 'dayGridMonth',
        firstDay: 1,
        height: 'auto',
        headerToolbar: {
            left:   'prev,next today',
            center: 'title',
            right:  'dayGridMonth,timeGridWeek,timeGridDay'
        },
        buttonText: {
            today: 'วันนี้',
            month: 'เดือน',
            week:  'สัปดาห์',
            day:   'วัน'
        },
        eventTimeFormat: { hour: '2-digit', minute: '2-digit', hour12: false },
        events: function (info, successCb, failureCb) {
            var gid = document.getElementById('filterGroup').value;
            var url = eventsUrl + '?start=' + info.startStr + '&end=' + info.endStr;
            if (gid) url += '&group_id=' + gid;
            fetch(url)
                .then(function (r) { return r.json(); })
                .then(successCb)
                .catch(failureCb);
        },
        eventDidMount: function (info) {
            var status = info.event.extendedProps.status;
            if (status === 'cancelled') {
                info.el.classList.add('fc-event-cancelled');
            } else if (status === 'completed') {
                info.el.classList.add('fc-event-completed');
            }
        },
        eventClick: function (info) {
            showDetail(info.event);
        },
        dateClick: function (info) {
            <?php if (!empty($perms['perm_duplicate_session'])): ?>
            var dayEvents = calendar.getEvents().filter(function (e) {
                return e.startStr.startsWith(info.dateStr);
            });
            if (dayEvents.length > 0) {
                showCopyDayPopup(info.dateStr, dayEvents.length);
            }
            <?php endif; ?>
        },
        noEventsContent: 'ไม่มีแผนการฝึกซ้อมในช่วงนี้'
    });

    calendar.render();

    // Group filter → re-fetch
    document.getElementById('filterGroup').addEventListener('change', function () {
        calendar.refetchEvents();
    });

    // ---- Offcanvas: show session detail ----
    function showDetail(event) {
        var p   = event.extendedProps;
        var sid = p.session_id || event.id;

        document.getElementById('ocTitle').textContent = p.session_title || event.title;
        document.getElementById('ocDate').textContent  = formatThaiDate(p.training_date);
        document.getElementById('ocTime').textContent  = p.start_time + ' – ' + p.end_time + ' น.';
        document.getElementById('ocGroup').textContent = p.group_name || '-';
        document.getElementById('ocCoach').textContent = p.coach_name || '-';
        document.getElementById('ocLocation').textContent = p.location || 'ไม่ระบุสถานที่';

        // Header bar color
        document.getElementById('ocHeaderBar').style.background = event.backgroundColor || '#16478d';

        // Type badge
        var tType = p.training_type || 'regular';
        document.getElementById('ocTypeBadge').innerHTML =
            '<span class="badge bg-' + (typeColor[tType] || 'secondary') + ' bg-opacity-10 ' +
            'text-' + (typeColor[tType] || 'secondary') + ' rounded-pill border border-' +
            (typeColor[tType] || 'secondary') + ' border-opacity-25">' +
            (typeLabel[tType] || tType) + '</span>';

        // Status badge
        var tStatus = p.status || 'planned';
        document.getElementById('ocStatusBadge').innerHTML =
            '<span class="badge bg-' + (statusColor[tStatus] || 'secondary') + ' rounded-pill">' +
            (statusLabel[tStatus] || tStatus) + '</span>';

        // Attachment badge
        var attachBadge = document.getElementById('ocAttachBadge');
        var attachCount = parseInt(p.attachment_count || 0, 10);
        if (attachCount > 0) {
            document.getElementById('ocAttachCount').textContent = attachCount;
            document.getElementById('ocAttachLink').href = detailBase + sid;
            attachBadge.style.display = '';
        } else {
            attachBadge.style.display = 'none';
        }

        // Notes
        var notesRow = document.getElementById('ocNotesRow');
        if (p.notes && p.notes.trim()) {
            document.getElementById('ocNotes').textContent = p.notes;
            notesRow.style.display = '';
        } else {
            notesRow.style.display = 'none';
        }

        // Buttons
        document.getElementById('ocBtnView').href = detailBase + sid;
        var btnEdit = document.getElementById('ocBtnEdit');
        if (btnEdit) btnEdit.href = editBase + sid;
        var btnCopy = document.getElementById('ocBtnCopy');
        if (btnCopy) {
            btnCopy.dataset.sessionId   = sid;
            btnCopy.dataset.sessionDate = p.training_date;
        }
        var btnCopyDay = document.getElementById('ocBtnCopyDay');
        if (btnCopyDay) {
            btnCopyDay.dataset.sessionDate = p.training_date;
        }

        // Show offcanvas
        bootstrap.Offcanvas.getOrCreateInstance(document.getElementById('sessionOffcanvas')).show();
    }

    // ---- Copy button in offcanvas ----
    var btnCopy = document.getElementById('ocBtnCopy');
    if (btnCopy) {
        btnCopy.addEventListener('click', function () {
            var sessionId = this.dataset.sessionId;
            var srcDate   = this.dataset.sessionDate;

            bootstrap.Offcanvas.getInstance(document.getElementById('sessionOffcanvas'))?.hide();

            Swal.fire({
                title: 'คัดลอกแผนการฝึก',
                input: 'date',
                inputValue: srcDate,
                inputAttributes: { required: true },
                showCancelButton: true,
                confirmButtonText: '<i class="fas fa-copy me-1"></i> คัดลอก',
                cancelButtonText: 'ยกเลิก',
                confirmButtonColor: '#16478d',
                cancelButtonColor: '#6c757d',
                showLoaderOnConfirm: true,
                preConfirm: function (newDate) {
                    if (!newDate) {
                        Swal.showValidationMessage('กรุณาเลือกวันที่');
                        return false;
                    }
                    return fetch(dupUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json' },
                        body: JSON.stringify({ session_id: sessionId, new_date: newDate, academy_id: academyId })
                    })
                    .then(function (r) { return r.json(); })
                    .then(function (res) {
                        if (res.status !== 'success') {
                            Swal.showValidationMessage(res.message || 'ไม่สามารถคัดลอกได้');
                            return false;
                        }
                        return res;
                    })
                    .catch(function () {
                        Swal.showValidationMessage('ไม่สามารถเชื่อมต่อได้');
                        return false;
                    });
                },
                allowOutsideClick: function () { return !Swal.isLoading(); }
            }).then(function (result) {
                if (result.isConfirmed && result.value) {
                    calendar.refetchEvents();
                    Swal.fire({
                        icon: 'success',
                        title: 'คัดลอกสำเร็จ',
                        showConfirmButton: true,
                        confirmButtonText: 'ดูแผนใหม่',
                        showCancelButton: true,
                        cancelButtonText: 'อยู่หน้านี้',
                        confirmButtonColor: '#16478d',
                        timer: 5000
                    }).then(function (r) {
                        if (r.isConfirmed) {
                            window.location.href = result.value.redirect_url;
                        }
                    });
                }
            });
        });
    }

    // ---- Copy-day popup (shared) ----
    function showCopyDayPopup(srcDate, count) {
        var label = count !== undefined
            ? 'วันที่ <strong>' + formatThaiDate(srcDate) + '</strong> มี <strong>' + count + ' แผน</strong>'
            : 'วันที่ <strong>' + formatThaiDate(srcDate) + '</strong>';

        Swal.fire({
            title: 'คัดลอกทุกแผนของวัน',
            html: '<p class="text-muted mb-3" style="font-size:.9rem;">' + label + ' จะถูกคัดลอกไปยังวันที่เลือก</p>',
            input: 'date',
            inputLabel: 'เลือกวันที่ปลายทาง',
            showCancelButton: true,
            confirmButtonText: '<i class="fas fa-calendar-plus me-1"></i> คัดลอกทั้งหมด',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#198754',
            cancelButtonColor: '#6c757d',
            showLoaderOnConfirm: true,
            preConfirm: function (newDate) {
                if (!newDate) {
                    Swal.showValidationMessage('กรุณาเลือกวันที่ปลายทาง');
                    return false;
                }
                if (newDate === srcDate) {
                    Swal.showValidationMessage('วันที่ปลายทางต้องไม่ซ้ำกับวันต้นทาง');
                    return false;
                }
                return fetch(dupDayUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ source_date: srcDate, new_date: newDate, academy_id: academyId })
                })
                .then(function (r) { return r.json(); })
                .then(function (res) {
                    if (res.status !== 'success') {
                        Swal.showValidationMessage(res.message || 'ไม่สามารถคัดลอกได้');
                        return false;
                    }
                    return res;
                })
                .catch(function () {
                    Swal.showValidationMessage('ไม่สามารถเชื่อมต่อได้');
                    return false;
                });
            },
            allowOutsideClick: function () { return !Swal.isLoading(); }
        }).then(function (result) {
            if (result.isConfirmed && result.value) {
                calendar.refetchEvents();
                Swal.fire({
                    icon: 'success',
                    title: 'คัดลอกสำเร็จ',
                    html: 'คัดลอก <strong>' + result.value.count + ' แผน</strong> เรียบร้อย',
                    confirmButtonText: 'ตกลง',
                    confirmButtonColor: '#16478d',
                    timer: 4000
                });
            }
        });
    }

    // ---- ocBtnCopyDay handler ----
    var btnCopyDay = document.getElementById('ocBtnCopyDay');
    if (btnCopyDay) {
        btnCopyDay.addEventListener('click', function () {
            var srcDate = this.dataset.sessionDate;
            var dayEvents = calendar.getEvents().filter(function (e) {
                return e.startStr.startsWith(srcDate);
            });
            bootstrap.Offcanvas.getInstance(document.getElementById('sessionOffcanvas'))?.hide();
            showCopyDayPopup(srcDate, dayEvents.length);
        });
    }

})();
</script>
<?php $this->endSection(); ?>
