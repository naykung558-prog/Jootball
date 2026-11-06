<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-0 fw-bold text-primary">
                    <i class="fas fa-clipboard-list me-2"></i><?= lang('Common.training_session_detail') ?>
                </h4>
                <?php if (!empty($academy['name'])): ?>
                    <p class="text-muted small mb-0"><?= esc($academy['name']) ?></p>
                <?php endif; ?>
            </div>
            <?php if (!empty($perms['perm_save_attendance']) || !empty($perms['perm_create_training_session'])): ?>
            <div class="d-flex gap-2">
                <?php if (!empty($perms['perm_save_attendance'])): ?>
                <a href="<?= base_url('academy/training-attendance/' . esc($academy['id']) . '/' . esc($session['id'])) ?>"
                   class="btn btn-success rounded-pill px-4">
                    <i class="fas fa-user-check me-2"></i><?= lang('Common.training_take_attendance') ?>
                </a>
                <?php endif; ?>
                <?php if (!empty($perms['perm_create_training_session'])): ?>
                <a href="<?= base_url('academy/training-session/manage/' . esc($academy['id']) . '/' . esc($session['id'])) ?>"
                   class="btn btn-warning rounded-pill px-4">
                    <i class="fas fa-edit me-2"></i><?= lang('Common.edit') ?>
                </a>
                <button type="button" class="btn btn-outline-danger rounded-pill px-4" id="btnDeleteSession">
                    <i class="fas fa-trash me-2"></i><?= lang('Common.delete') ?>
                </button>
                <?php endif; ?>
            </div>
            <?php endif; ?>
        </div>

        <div class="card-body p-4">
            <!-- Session Info -->
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="card border-0 bg-light rounded-4 p-4 h-100">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-info-circle me-2"></i><?= lang('Common.training_session_info') ?>
                        </h6>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted fw-bold" width="40%"><?= lang('Common.training_title') ?></td>
                                <td><?= esc($session['title'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold"><?= lang('Common.training_date') ?></td>
                                <td><?= esc($session['training_date'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold"><?= lang('Common.training_time') ?></td>
                                <td><?= esc($session['start_time'] ?? '-') ?> - <?= esc($session['end_time'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold"><?= lang('Common.training_select_group') ?></td>
                                <td><?= esc($session['group_name'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold"><?= lang('Common.coach') ?></td>
                                <td><?= esc($session['coach_name'] ?? '-') ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold"><?= lang('Common.training_location') ?></td>
                                <td><?= esc($session['location'] ?? '-') ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card border-0 bg-light rounded-4 p-4 h-100">
                        <h6 class="fw-bold text-primary mb-3">
                            <i class="fas fa-tags me-2"></i><?= lang('Common.details') ?>
                        </h6>
                        <table class="table table-borderless mb-0">
                            <tr>
                                <td class="text-muted fw-bold" width="40%"><?= lang('Common.training_type') ?></td>
                                <td>
                                    <?php
                                    $typeClass = 'primary';
                                    if (($session['training_type'] ?? '') === 'special') $typeClass = 'warning';
                                    elseif (($session['training_type'] ?? '') === 'match') $typeClass = 'success';
                                    elseif (($session['training_type'] ?? '') === 'fitness') $typeClass = 'info';
                                    $typeKey = 'training_type_' . ($session['training_type'] ?? 'regular');
                                    ?>
                                    <span class="badge bg-<?= $typeClass ?> rounded-pill"><?= lang('Common.' . $typeKey) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold"><?= lang('Common.training_status') ?></td>
                                <td>
                                    <?php
                                    $statusClass = 'secondary';
                                    if (($session['status'] ?? '') === 'completed') $statusClass = 'success';
                                    elseif (($session['status'] ?? '') === 'cancelled') $statusClass = 'danger';
                                    $statusKey = 'training_status_' . ($session['status'] ?? 'planned');
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?> rounded-pill"><?= lang('Common.' . $statusKey) ?></span>
                                </td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold"><?= lang('Common.training_description') ?></td>
                                <td><?= nl2br(esc($session['description'] ?? '-')) ?></td>
                            </tr>
                            <tr>
                                <td class="text-muted fw-bold"><?= lang('Common.training_notes') ?></td>
                                <td><?= nl2br(esc($session['notes'] ?? '-')) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Attendance Summary Cards -->
            <h5 class="fw-bold text-dark mb-3">
                <i class="fas fa-chart-bar me-2"></i><?= lang('Common.training_attendance_summary') ?>
            </h5>

            <?php
            $totalPlayers = count($groupPlayers ?? []);
            $presentCount = 0;
            $absentCount = 0;
            $lateCount = 0;
            $excusedCount = 0;

            if (!empty($attendance)) {
                foreach ($attendance as $att) {
                    switch ($att['status'] ?? '') {
                        case 'present': $presentCount++; break;
                        case 'absent': $absentCount++; break;
                        case 'late': $lateCount++; break;
                        case 'excused': $excusedCount++; break;
                    }
                }
            }
            ?>

            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3 col-lg">
                    <div class="card border-0 rounded-4 text-center p-3" style="background:#dbeafe;">
                        <div class="fs-2 fw-bold" style="color:#1e40af;"><?= $totalPlayers ?></div>
                        <div class="small fw-semibold" style="color:#1e40af;"><?= lang('Common.attendance_total') ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg">
                    <div class="card border-0 rounded-4 text-center p-3" style="background:#dcfce7;">
                        <div class="fs-2 fw-bold" style="color:#166534;"><?= $presentCount ?></div>
                        <div class="small fw-semibold" style="color:#166534;"><?= lang('Common.attendance_present') ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg">
                    <div class="card border-0 rounded-4 text-center p-3" style="background:#fee2e2;">
                        <div class="fs-2 fw-bold" style="color:#991b1b;"><?= $absentCount ?></div>
                        <div class="small fw-semibold" style="color:#991b1b;"><?= lang('Common.attendance_absent') ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg">
                    <div class="card border-0 rounded-4 text-center p-3" style="background:#fef9c3;">
                        <div class="fs-2 fw-bold" style="color:#92400e;"><?= $lateCount ?></div>
                        <div class="small fw-semibold" style="color:#92400e;"><?= lang('Common.attendance_late') ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3 col-lg">
                    <div class="card border-0 rounded-4 text-center p-3" style="background:#e0f2fe;">
                        <div class="fs-2 fw-bold" style="color:#155e75;"><?= $excusedCount ?></div>
                        <div class="small fw-semibold" style="color:#155e75;"><?= lang('Common.attendance_excused') ?></div>
                    </div>
                </div>
            </div>

            <!-- Attendance Table -->
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-bold text-dark mb-0">
                        <i class="fas fa-list-alt me-2"></i><?= lang('Common.training_attendance') ?>
                    </h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="text-center py-3 text-secondary border-0" width="5%">#</th>
                                    <th class="py-3 text-secondary border-0"><?= lang('Common.full_name') ?></th>
                                    <th class="text-center py-3 text-secondary border-0" width="10%"><?= lang('Common.shirt_number') ?></th>
                                    <th class="text-center py-3 text-secondary border-0" width="15%"><?= lang('Common.status') ?></th>
                                    <th class="text-center py-3 text-secondary border-0" width="15%"><?= lang('Common.attendance_check_in_time') ?></th>
                                    <th class="py-3 text-secondary border-0" width="25%"><?= lang('Common.training_notes') ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($groupPlayers)): ?>
                                    <?php
                                    // Index attendance by player_id for quick lookup
                                    $attendanceMap = [];
                                    if (!empty($attendance)) {
                                        foreach ($attendance as $att) {
                                            $attendanceMap[$att['player_id']] = $att;
                                        }
                                    }
                                    ?>
                                    <?php foreach ($groupPlayers as $index => $player): ?>
                                    <?php $playerAttendance = $attendanceMap[$player['id']] ?? null; ?>
                                    <tr>
                                        <td class="text-center text-muted"><?= $index + 1 ?></td>
                                        <td class="fw-medium">
                                            <?= esc(($player['first_name'] ?? '') . ' ' . ($player['last_name'] ?? '')) ?>
                                        </td>
                                        <td class="text-center fw-bold text-primary"><?= esc($player['shirt_number'] ?? '-') ?></td>
                                        <td class="text-center">
                                            <?php if ($playerAttendance): ?>
                                                <?php
                                                $attStatus = $playerAttendance['status'] ?? '';
                                                $attBadge = 'secondary';
                                                if ($attStatus === 'present') $attBadge = 'success';
                                                elseif ($attStatus === 'absent') $attBadge = 'danger';
                                                elseif ($attStatus === 'late') $attBadge = 'warning';
                                                elseif ($attStatus === 'excused') $attBadge = 'info';
                                                ?>
                                                <span class="badge bg-<?= $attBadge ?> rounded-pill">
                                                    <?= lang('Common.attendance_' . $attStatus) ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-muted rounded-pill">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-center text-muted small">
                                            <?= esc($playerAttendance['check_in_time'] ?? '-') ?>
                                        </td>
                                        <td class="text-muted small">
                                            <?= esc($playerAttendance['notes'] ?? '-') ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center text-muted py-5">
                                            <i class="fas fa-users fa-2x mb-3 opacity-25 d-block"></i>
                                            <?= lang('Common.no_data') ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Bottom Action Buttons -->
            <div class="d-flex justify-content-between mt-4">
                <a href="<?= base_url('academy/training-sessions/' . esc($academy['id'])) ?>"
                   class="btn btn-light rounded-pill px-4">
                    <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                </a>
                <?php if (!empty($perms['perm_save_attendance']) || !empty($perms['perm_create_training_session'])): ?>
                <div class="d-flex gap-2">
                    <?php if (!empty($perms['perm_save_attendance'])): ?>
                    <a href="<?= base_url('academy/training-attendance/' . esc($academy['id']) . '/' . esc($session['id'])) ?>"
                       class="btn btn-success rounded-pill px-4">
                        <i class="fas fa-user-check me-1"></i><?= lang('Common.training_take_attendance') ?>
                    </a>
                    <?php endif; ?>
                    <?php if (!empty($perms['perm_create_training_session'])): ?>
                    <a href="<?= base_url('academy/training-session/manage/' . esc($academy['id']) . '/' . esc($session['id'])) ?>"
                       class="btn btn-warning rounded-pill px-4">
                        <i class="fas fa-edit me-1"></i><?= lang('Common.edit') ?>
                    </a>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" id="btnDeleteSessionBottom">
                        <i class="fas fa-trash me-1"></i><?= lang('Common.delete') ?>
                    </button>
                    <?php endif; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Attachments Card -->
    <?php if (!empty($attachments)): ?>
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-info text-white py-3 px-4">
            <h6 class="mb-0 text-white fw-bold">
                <i class="fas fa-paperclip me-2"></i>
                <?= lang('Common.training_attach_section_title') ?>
                <span class="badge bg-light text-dark ms-2"><?= count($attachments) ?></span>
            </h6>
        </div>
        <div class="card-body p-4">
            <?= $this->include('Modules\Academy\Views\_attachments_list', [
                'attachments' => $attachments,
                'mode'        => 'full',
                'canManage'   => false,
            ]) ?>
        </div>
    </div>
    <?php endif; ?>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?php if (!empty($attachments)): ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/css/lightgallery-bundle.min.css">
<script src="https://cdn.jsdelivr.net/npm/lightgallery@2.7.2/lightgallery.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const gallery = document.getElementById('attach-gallery');
    if (gallery && typeof lightGallery !== 'undefined') {
        lightGallery(gallery, {
            selector: '.attach-img-link',
            download: true,
            counter: true,
        });
    }
});
</script>
<?php endif; ?>
<?php if (!empty($perms['perm_create_training_session'])): ?>
<script>
    function doDeleteSession() {
        Swal.fire({
            title: "<?= lang('Common.confirm_delete_data') ?>",
            text: "<?= lang('Common.delete_irreversible') ?>",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#dc3545",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "<?= lang('Common.confirm_delete_btn') ?>",
            cancelButtonText: "<?= lang('Common.cancel') ?>",
            customClass: {
                confirmButton: 'btn btn-danger rounded-pill px-4',
                cancelButton: 'btn btn-secondary rounded-pill px-4'
            },
            buttonsStyling: false
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('academy/training-session/delete') ?>",
                    data: { id: "<?= esc($session['id']) ?>", academy_id: "<?= esc($academy['id']) ?>" },
                    dataType: 'json',
                    success: function(response) {
                        if (response.status === 'success') {
                            Swal.fire({
                                icon: "success",
                                title: "<?= lang('Common.success') ?>",
                                text: response.message || "<?= lang('Common.training_session_deleted') ?>",
                                showConfirmButton: false,
                                timer: 1500
                            }).then(function() {
                                window.location.href = "<?= base_url('academy/training-sessions/' . esc($academy['id'])) ?>";
                            });
                        } else {
                            Swal.fire({ icon: "error", title: "<?= lang('Common.error') ?>", text: response.message });
                        }
                    },
                    error: function() {
                        Swal.fire({ icon: "error", title: "<?= lang('Common.error') ?>", text: "<?= lang('Common.connection_error') ?>" });
                    }
                });
            }
        });
    }

    document.getElementById('btnDeleteSession').addEventListener('click', doDeleteSession);
    document.getElementById('btnDeleteSessionBottom').addEventListener('click', doDeleteSession);
</script>
<?php endif; ?>
<?php $this->endSection(); ?>
