<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-object-group text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.merge_teams_into_academy') ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.merge_select_teams') ?></p>
                </div>
            </div>
            <a href="<?= base_url('admin/academy') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>
    </div>
</div>

<!-- Step 1: Select Teams -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-users me-2"></i><?= lang('Common.merge_available_teams') ?>
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table id="teamsTable" class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="5%">
                            <input type="checkbox" class="form-check-input" id="selectAll">
                        </th>
                        <th class="py-3 text-center" width="8%"><?= lang('Common.academy_logo') ?></th>
                        <th class="py-3 text-start" width="25%"><?= lang('Common.team_name') ?></th>
                        <th class="py-3 text-center" width="15%"><?= lang('Common.player') ?></th>
                        <th class="py-3 text-center" width="15%"><?= lang('Common.coach') ?></th>
                        <th class="py-3 text-center" width="15%"><?= lang('Common.merge_tournament_count') ?></th>
                        <th class="py-3 text-center" width="10%"><?= lang('Common.detail') ?></th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
</div>

<!-- Selected Teams Summary -->
<div id="selectedSummary" class="alert alert-primary border-0 shadow-sm mb-4" style="border-radius: var(--border-radius); display: none;">
    <div class="d-flex align-items-center">
        <i class="fas fa-check-circle fs-4 me-3"></i>
        <div>
            <strong><?= lang('Common.merge_selected_teams') ?>: <span id="selectedCount">0</span> <?= lang('Common.teams') ?></strong>
            <div id="selectedTeamNames" class="mt-1 small"></div>
        </div>
    </div>
</div>

<!-- Step 2: Target Academy -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-header bg-white border-0 pt-4 px-4">
        <h5 class="fw-bold mb-0" style="color: var(--secondary-color);">
            <i class="fas fa-school me-2"></i><?= lang('Common.merge_target_academy') ?>
        </h5>
    </div>
    <div class="card-body px-4 pb-4">
        <div class="mb-3">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="target_type" id="targetNew" value="new" checked>
                <label class="form-check-label fw-bold" for="targetNew"><?= lang('Common.merge_create_new') ?></label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="target_type" id="targetExisting" value="existing">
                <label class="form-check-label fw-bold" for="targetExisting"><?= lang('Common.merge_use_existing') ?></label>
            </div>
            <div id="targetNewLockHint" class="text-warning small mt-1 d-none">
                <i class="fas fa-lock me-1"></i><?= lang('Common.merge_in_academy_existing_only') ?>
            </div>
        </div>

        <!-- New Academy Form -->
        <div id="newAcademyForm">
            <div class="row justify-content-center mb-3">
                <div class="col-md-12 text-center">
                    <img id="academyLogoPreview" src="<?= base_url('public/img/exam-logo.webp') ?>" alt="Academy Logo"
                        class="img-fluid rounded-circle shadow-sm" style="width: 110px; height: 110px; object-fit: cover; border: 4px solid white; box-shadow: var(--box-shadow);">
                    <input type="hidden" id="academyLogo" value="">
                    <div id="academySourceHint" class="text-muted small mt-2"></div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.academy_name_th') ?> <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" id="academyName" placeholder="<?= lang('Common.academy_name_th') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.academy_name_en') ?></label>
                    <input type="text" class="form-control" id="academyNameEn" placeholder="<?= lang('Common.academy_name_en') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.contact_name_label') ?></label>
                    <input type="text" class="form-control" id="academyContactName" placeholder="<?= lang('Common.contact_name_label') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.mobile_contact') ?></label>
                    <input type="text" class="form-control" id="academyContactPhone" placeholder="<?= lang('Common.mobile_contact') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.email_contact') ?></label>
                    <input type="email" class="form-control" id="academyContactEmail" placeholder="<?= lang('Common.email_contact') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.line_id') ?></label>
                    <input type="text" class="form-control" id="academyContactLine" placeholder="<?= lang('Common.line_id') ?>">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.field_location') ?> (Latitude)</label>
                    <input type="text" class="form-control" id="academyLatitude" placeholder="Latitude">
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.field_location') ?> (Longitude)</label>
                    <input type="text" class="form-control" id="academyLongitude" placeholder="Longitude">
                </div>
            </div>
        </div>

        <!-- Existing Academy Select -->
        <div id="existingAcademyForm" style="display: none;">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold"><?= lang('Common.merge_select_academy') ?></label>
                    <select class="form-select select2" id="academySelect" style="width: 100%;">
                        <option value=""><?= lang('Common.merge_select_academy') ?></option>
                        <?php if (!empty($academies)): ?>
                            <?php foreach ($academies as $ac): ?>
                                <option value="<?= $ac['id'] ?>"><?= !empty($ac['name']) ? $ac['name'] : $ac['name_en'] ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Preview Button -->
<div class="text-center mb-4">
    <button type="button" class="btn btn-lg px-5 py-3 fw-bold" id="btnPreview" style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
        <i class="fas fa-eye me-2"></i><?= lang('Common.merge_preview') ?>
    </button>
</div>

<!-- Preview Modal (Micromodal) -->
<div class="micromodal" id="preview-modal" aria-hidden="true">
    <div class="micromodal-overlay" tabindex="-1" data-micromodal-close>
        <div class="micromodal-container modal-lg" role="dialog" aria-modal="true" aria-labelledby="preview-modal-title">
            <header class="micromodal-header">
                <h2 class="micromodal-title" id="preview-modal-title">
                    <i class="fas fa-search me-2"></i><?= lang('Common.merge_preview_title') ?>
                </h2>
                <button class="micromodal-close" aria-label="Close" data-micromodal-close>&times;</button>
            </header>
            <main class="micromodal-body" id="previewContent">
            </main>
            <footer class="micromodal-footer">
                <button type="button" class="btn btn-light rounded-pill px-4" data-micromodal-close>
                    <i class="fas fa-times me-1"></i> <?= lang('Common.cancel') ?>
                </button>
                <button type="button" class="btn btn-primary rounded-pill px-4 fw-bold" id="btnConfirmMerge">
                    <i class="fas fa-check me-1"></i><?= lang('Common.merge_confirm') ?>
                </button>
            </footer>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
var teamsTable;
var selectedTeams = {}; // { id: teamData, ... } เก็บทีมที่เลือกข้ามหน้า
var sourceTeamId = null;

const langMergeMinTeams = '<?= lang('Common.merge_select_min_teams') ?>';
const langMergeAcademyRequired = '<?= lang('Common.merge_academy_name_required') ?>';
const langMergeSelectAcademy = '<?= lang('Common.merge_select_academy') ?>';
const langMergeSuccess = '<?= lang('Common.merge_success') ?>';
const langMergeError = '<?= lang('Common.merge_error') ?>';
const langMergeConfirm = '<?= lang('Common.merge_confirm') ?>';
const langMergeAlreadyInAcademy = '<?= lang('Common.merge_team_already_in_academy') ?>';
const langPlayer = '<?= lang('Common.player') ?>';
const langCoach = '<?= lang('Common.coach') ?>';
const langMergeTournamentCount = '<?= lang('Common.merge_tournament_count') ?>';
const langTeamName = '<?= lang('Common.team_name') ?>';
const langConfirm = '<?= lang('Common.confirm') ?>';
const langCancel = '<?= lang('Common.cancel') ?>';
const langError = '<?= lang('Common.error') ?>';
const academyLogoFallback = '<?= base_url('public/img/exam-logo.webp') ?>';

$(document).ready(function() {
    // DataTable
    teamsTable = $('#teamsTable').DataTable({
        processing: true,
        serverSide: true,
        language: dtLanguage,
        ajax: {
            url: '<?= base_url('admin/academy/ajax-available-teams') ?>',
            method: 'POST'
        },
        ordering: false,
        columns: [
            { data: 'checkbox', className: 'text-center' },
            { data: 'image_logo', className: 'text-center' },
            { data: 'team_name', className: 'text-start' },
            { data: 'player_count', className: 'text-center' },
            { data: 'coach_count', className: 'text-center' },
            { data: 'tournament_count', className: 'text-center' },
            { data: 'action', className: 'text-center' }
        ],
        drawCallback: function() {
            // Restore checkbox ที่เลือกไว้หลัง DataTable redraw (ค้นหา/เปลี่ยนหน้า)
            $('.team-checkbox').each(function() {
                var id = $(this).val();
                if (selectedTeams[id]) {
                    $(this).prop('checked', true);
                }
            });
            updateSelectAllState();
        }
    });

    // Select All checkbox
    $('#selectAll').on('change', function() {
        var checked = $(this).prop('checked');
        $('.team-checkbox:not(:disabled)').each(function() {
            $(this).prop('checked', checked);
            var team = getTeamDataFromCheckbox($(this));
            if (checked) {
                selectedTeams[team.id] = team;
            } else {
                delete selectedTeams[team.id];
            }
        });
        updateSelectedSummary();
        applyDefaultSourceTeam();
    });

    // Track individual checkbox changes
    $('#teamsTable').on('change', '.team-checkbox', function() {
        var team = getTeamDataFromCheckbox($(this));
        if ($(this).prop('checked')) {
            selectedTeams[team.id] = team;
        } else {
            delete selectedTeams[team.id];
            if (sourceTeamId === team.id) {
                sourceTeamId = null;
                $('#academySourceHint').text('');
            }
        }
        updateSelectAllState();
        updateSelectedSummary();
        applyDefaultSourceTeam();
    });

    // Target type toggle
    $('input[name="target_type"]').on('change', function() {
        if ($(this).val() === 'new') {
            $('#newAcademyForm').show();
            $('#existingAcademyForm').hide();
        } else {
            $('#newAcademyForm').hide();
            $('#existingAcademyForm').show();
        }
    });

    // Select2 for existing academy
    $('#academySelect').select2({
        placeholder: langMergeSelectAcademy,
        allowClear: true
    });

    // Preview button
    $('#btnPreview').on('click', function() {
        var selectedTeamIds = Object.keys(selectedTeams);

        if (selectedTeamIds.length === 0) {
            Swal.fire({ icon: 'warning', title: langMergeMinTeams });
            return;
        }

        var targetType = $('input[name="target_type"]:checked').val();

        if (targetType === 'new' && !$('#academyName').val().trim()) {
            Swal.fire({ icon: 'warning', title: langMergeAcademyRequired });
            return;
        }

        if (targetType === 'existing' && !$('#academySelect').val()) {
            Swal.fire({ icon: 'warning', title: langMergeSelectAcademy });
            return;
        }

        $.ajax({
            url: '<?= base_url('admin/academy/merge-preview') ?>',
            method: 'POST',
            data: {
                team_ids: selectedTeamIds,
                target_type: targetType,
                academy_id: targetType === 'existing' ? $('#academySelect').val() : null,
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(res) {
                if (res.status) {
                    renderPreview(res.data, targetType);
                    MicroModal.show('preview-modal');
                } else {
                    Swal.fire({ icon: 'error', title: langError, text: res.message });
                }
            },
            error: function() {
                Swal.fire({ icon: 'error', title: langError, text: langMergeError });
            }
        });
    });

    // Confirm Merge
    $('#btnConfirmMerge').on('click', function() {
        var targetType = $('input[name="target_type"]:checked').val();
        var btn = $(this);
        btn.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('admin/academy/merge-execute') ?>',
            method: 'POST',
            data: {
                team_ids: Object.keys(selectedTeams),
                target_type: targetType,
                academy_id: targetType === 'existing' ? $('#academySelect').val() : null,
                name: $('#academyName').val(),
                name_en: $('#academyNameEn').val(),
                logo: $('#academyLogo').val(),
                contact_name: $('#academyContactName').val(),
                contact_phone: $('#academyContactPhone').val(),
                contact_email: $('#academyContactEmail').val(),
                contact_line: $('#academyContactLine').val(),
                latitude: $('#academyLatitude').val(),
                longitude: $('#academyLongitude').val(),
                <?= csrf_token() ?>: '<?= csrf_hash() ?>'
            },
            success: function(res) {
                MicroModal.close('preview-modal');
                btn.prop('disabled', false);
                if (res.status) {
                    Swal.fire({
                        icon: 'success',
                        title: langMergeSuccess,
                        showConfirmButton: true,
                        confirmButtonColor: '#16478d'
                    }).then(function() {
                        window.location.href = '<?= base_url('admin/academy') ?>';
                    });
                } else {
                    Swal.fire({ icon: 'error', title: langError, text: res.message });
                }
            },
            error: function() {
                btn.prop('disabled', false);
                Swal.fire({ icon: 'error', title: langError, text: langMergeError });
            }
        });
    });
});

function getTeamDataFromCheckbox($checkbox) {
    var $row = $checkbox.closest('tr');
    return {
        id: $checkbox.val(),
        name: $checkbox.attr('data-name') || $row.find('td:eq(2)').text().trim(),
        name_en: $checkbox.attr('data-name-en') || '',
        logo: $checkbox.attr('data-logo') || '',
        logo_url: $checkbox.attr('data-logo-url') || '',
        contact_name: $checkbox.attr('data-contact-name') || '',
        contact_phone: $checkbox.attr('data-contact-phone') || '',
        contact_email: $checkbox.attr('data-contact-email') || '',
        contact_line: $checkbox.attr('data-contact-line') || '',
        latitude: $checkbox.attr('data-latitude') || '',
        longitude: $checkbox.attr('data-longitude') || '',
        in_academy: $checkbox.attr('data-in-academy') === '1'
    };
}

// ถ้ามีทีมที่อยู่ใน academy อยู่แล้วถูกเลือก → บังคับ target = "อคาเดมีที่มีอยู่" (ห้ามสร้างใหม่)
function enforceTargetRestriction() {
    var hasInAcademy = Object.keys(selectedTeams).some(function(id) {
        return selectedTeams[id] && selectedTeams[id].in_academy;
    });
    var $newRadio = $('#targetNew');
    if (hasInAcademy) {
        if ($newRadio.prop('checked')) {
            $('#targetExisting').prop('checked', true).trigger('change');
        }
        $newRadio.prop('disabled', true);
        $('#targetNewLockHint').removeClass('d-none');
    } else {
        $newRadio.prop('disabled', false);
        $('#targetNewLockHint').addClass('d-none');
    }
}

function applyDefaultSourceTeam() {
    if (sourceTeamId && selectedTeams[sourceTeamId]) return;

    var ids = Object.keys(selectedTeams);
    if (!ids.length) {
        sourceTeamId = null;
        $('#academySourceHint').text('');
        return;
    }

    if (!$('#academyName').val().trim()) {
        useTeamAsAcademySource(ids[0]);
    }
}

function updateSelectedSummary() {
    enforceTargetRestriction();
    var ids = Object.keys(selectedTeams);

    if (ids.length > 0) {
        $('#selectedCount').text(ids.length);
        var badges = ids.map(function(id) {
            var team = selectedTeams[id];
            var isSource = sourceTeamId === id;
            var badgeClass = isSource ? 'bg-primary text-white border-primary' : 'bg-white text-primary border-primary';
            var removeClass = isSource ? 'text-white' : 'text-danger';
            return '<span class="badge border me-1 mb-1 px-2 py-1 ' + badgeClass + '" style="cursor:pointer;" onclick="useTeamAsAcademySource(\'' + id + '\')">' +
                '<i class="fas fa-futbol me-1"></i>' + escapeHtml(team.name || team.name_en || '') +
                (isSource ? ' <i class="fas fa-check ms-1"></i>' : '') +
                ' <i class="fas fa-times ms-1 ' + removeClass + '" style="cursor:pointer;" onclick="event.stopPropagation(); removeSelectedTeam(\'' + id + '\')"></i>' +
                '</span>';
        }).join('');
        $('#selectedTeamNames').html(badges);
        $('#selectedSummary').slideDown(200);
    } else {
        $('#selectedSummary').slideUp(200);
    }
}

function updateSelectAllState() {
    var total = $('.team-checkbox:not(:disabled)').length;
    var checked = $('.team-checkbox:not(:disabled):checked').length;
    $('#selectAll').prop('checked', total > 0 && total === checked);
}

function removeSelectedTeam(id) {
    delete selectedTeams[id];
    $('.team-checkbox[value="' + id + '"]').prop('checked', false);
    if (sourceTeamId === id) {
        sourceTeamId = null;
        $('#academySourceHint').text('');
    }
    updateSelectAllState();
    updateSelectedSummary();
    applyDefaultSourceTeam();
}

function useTeamAsAcademySource(id) {
    var team = selectedTeams[id];
    if (!team) return;

    sourceTeamId = id;
    $('#academyName').val(team.name || '');
    $('#academyNameEn').val(team.name_en || '');
    $('#academyLogo').val(team.logo || '');
    $('#academyLogoPreview').attr('src', team.logo_url || academyLogoFallback);
    $('#academyContactName').val(team.contact_name || '');
    $('#academyContactPhone').val(team.contact_phone || '');
    $('#academyContactEmail').val(team.contact_email || '');
    $('#academyContactLine').val(team.contact_line || '');
    $('#academyLatitude').val(team.latitude || '');
    $('#academyLongitude').val(team.longitude || '');
    $('#academySourceHint').text('ใช้ข้อมูลตั้งต้นจาก: ' + (team.name || team.name_en || ''));
    updateSelectedSummary();
}

function escapeHtml(value) {
    return $('<div>').text(value || '').html();
}

function renderPreview(data, targetType) {
    var html = '';

    // Academy target info
    if (targetType === 'new') {
        html += '<div class="p-3 mb-3" style="background: linear-gradient(135deg, #e8f4fd 0%, #f0f7ff 100%); border-radius: 12px; border-left: 4px solid #06346D;">';
        html += '<div class="d-flex align-items-center">';
        html += '<img src="' + escapeHtml($('#academyLogoPreview').attr('src') || academyLogoFallback) + '" class="rounded-circle me-3" style="width:40px;height:40px;object-fit:cover;flex-shrink:0;">';
        html += '<div><strong style="color:#06346D;"><?= lang('Common.merge_create_new') ?></strong>';
        html += '<div class="text-muted small mt-1">' + escapeHtml($('#academyName').val() || '');
        if ($('#academyNameEn').val()) html += ' (' + escapeHtml($('#academyNameEn').val()) + ')';
        html += '</div></div></div></div>';
    } else if (data.academy) {
        html += '<div class="p-3 mb-3" style="background: linear-gradient(135deg, #e8f4fd 0%, #f0f7ff 100%); border-radius: 12px; border-left: 4px solid #06346D;">';
        html += '<div class="d-flex align-items-center">';
        html += '<div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width:40px;height:40px;background:linear-gradient(135deg,#06346D,#042441);flex-shrink:0;"><i class="fas fa-school text-white"></i></div>';
        html += '<div><strong style="color:#06346D;"><?= lang('Common.merge_use_existing') ?></strong>';
        html += '<div class="text-muted small mt-1">' + escapeHtml(data.academy.name || data.academy.name_en);
        html += ' <span class="badge text-white ms-1" style="background:#06346D;">' + data.academy.current_teams + ' <?= lang('Common.teams') ?></span>';
        html += '</div></div></div></div>';
    }

    // Teams table
    html += '<div class="table-responsive">';
    html += '<table class="table table-hover align-middle mb-0" style="border-radius: 12px; overflow: hidden;">';
    html += '<thead style="background: #f0f4f8; color: #06346D;"><tr>';
    html += '<th class="py-2 ps-3" width="5%">#</th>';
    html += '<th class="py-2">' + langTeamName + '</th>';
    html += '<th class="py-2 text-center" width="12%">' + langPlayer + '</th>';
    html += '<th class="py-2 text-center" width="12%">' + langCoach + '</th>';
    html += '<th class="py-2 text-center" width="18%">' + langMergeTournamentCount + '</th>';
    html += '<th class="py-2 text-center pe-3" width="15%"><?= lang('Common.status') ?></th>';
    html += '</tr></thead><tbody>';

    data.teams.forEach(function(team, i) {
        html += '<tr>';
        html += '<td class="ps-3">' + (i + 1) + '</td>';
        html += '<td class="fw-bold">' + escapeHtml(team.name || team.name_en) + '</td>';
        html += '<td class="text-center"><span class="badge bg-light text-dark border">' + team.player_count + '</span></td>';
        html += '<td class="text-center"><span class="badge bg-light text-dark border">' + team.coach_count + '</span></td>';
        html += '<td class="text-center"><span class="badge bg-light text-dark border">' + team.tournament_count + '</span></td>';
        html += '<td class="text-center pe-3">';
        if (team.already_in_academy) {
            html += '<span class="badge bg-warning text-dark px-2 py-1"><i class="fas fa-exclamation-triangle me-1"></i>' + langMergeAlreadyInAcademy + '</span>';
        } else {
            html += '<span class="badge px-3 py-1" style="background: #28a745;"><i class="fas fa-check me-1"></i>OK</span>';
        }
        html += '</td></tr>';
    });

    html += '</tbody></table></div>';

    $('#previewContent').html(html);
}

</script>
<?php $this->endSection(); ?>
