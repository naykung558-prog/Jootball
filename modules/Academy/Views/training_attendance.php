<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Session Info Header -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <h4 class="mb-1 fw-bold text-primary">
                        <i class="fas fa-clipboard-check me-2"></i><?= lang('Common.training_attendance') ?>
                    </h4>
                    <?php if (!empty($session)): ?>
                        <p class="text-muted small mb-0">
                            <?= esc($session['title'] ?? lang('Common.training_session_detail')) ?>
                        </p>
                    <?php endif; ?>
                </div>
                <a href="<?= base_url('academy/training-session/detail/' . esc($academy['id']) . '/' . esc($session['id'])) ?>"
                   class="btn btn-outline-secondary btn-sm rounded-pill">
                    <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                </a>
            </div>
        </div>

        <!-- Session Details -->
        <?php if (!empty($session)): ?>
        <div class="card-body border-top px-4 py-3">
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="far fa-calendar-alt text-primary me-2"></i>
                        <div>
                            <small class="text-muted d-block"><?= lang('Common.training_date') ?></small>
                            <span class="fw-medium"><?= date('d/m/Y', strtotime($session['training_date'])) ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-users text-success me-2"></i>
                        <div>
                            <small class="text-muted d-block"><?= lang('Common.training_groups') ?></small>
                            <span class="fw-medium"><?= esc($session['group_name'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="far fa-clock text-warning me-2"></i>
                        <div>
                            <small class="text-muted d-block"><?= lang('Common.training_time') ?></small>
                            <span class="fw-medium">
                                <?= !empty($session['start_time']) ? date('H:i', strtotime($session['start_time'])) : '-' ?>
                                <?php if (!empty($session['end_time'])): ?>
                                    - <?= date('H:i', strtotime($session['end_time'])) ?>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tag text-info me-2"></i>
                        <div>
                            <small class="text-muted d-block"><?= lang('Common.training_type') ?></small>
                            <span class="fw-medium"><?= esc($session['training_type'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <!-- Attachments Accordion -->
    <?php if (!empty($attachments)): ?>
    <div class="accordion mb-3" id="attachAccordion">
        <div class="accordion-item border-0 shadow-sm rounded-4 overflow-hidden">
            <h2 class="accordion-header">
                <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#attachAccordionBody">
                    <i class="fas fa-paperclip me-2"></i>
                    <?= lang('Common.training_attach_section_title') ?>
                    <span class="badge bg-info ms-2"><?= count($attachments) ?></span>
                </button>
            </h2>
            <div id="attachAccordionBody" class="accordion-collapse collapse">
                <div class="accordion-body">
                    <?= $this->include('Modules\Academy\Views\_attachments_list', [
                        'attachments' => $attachments,
                        'mode'        => 'compact',
                        'canManage'   => false,
                    ]) ?>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Attendance Form -->
    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <h5 class="mb-0 fw-bold text-primary">
                <i class="fas fa-user-check me-2"></i><?= lang('Common.training_attendance') ?>
                <?php if (!empty($groupPlayers)): ?>
                    <span class="badge bg-primary rounded-pill ms-2"><?= count($groupPlayers) ?></span>
                <?php endif; ?>
            </h5>
            <?php if (!empty($groupPlayers) && !empty($perms['perm_save_attendance'])):
                // ใช้เวลาจากข้อมูลเดิม หรือเวลาปัจจุบัน
                $sharedTime = '';
                if (!empty($attendance)) {
                    $sharedTime = $attendance[0]['check_in_time'] ?? '';
                    if ($sharedTime) $sharedTime = substr($sharedTime, 0, 5); // HH:MM
                }
                if (empty($sharedTime)) $sharedTime = date('H:i');
            ?>
            <div class="d-flex align-items-center gap-2">
                <i class="far fa-clock text-muted"></i>
                <label for="sharedCheckInTime" class="mb-0 text-muted small fw-semibold text-nowrap">เวลาเช็คอิน</label>
                <input type="time" id="sharedCheckInTime" class="form-control form-control-sm"
                       style="width:120px;" value="<?= $sharedTime ?>">
            </div>
            <?php endif; ?>
        </div>
        <div class="card-body p-0">
            <?php if (!empty($groupPlayers)): ?>
                <?php
                // Build lookup from existing attendance data
                $attendanceMap = [];
                if (!empty($attendance)) {
                    foreach ($attendance as $att) {
                        $attendanceMap[$att['player_id']] = $att;
                    }
                }
                ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0 text-muted small fw-bold text-uppercase" width="5%">#</th>
                                <th class="py-3 border-0 text-muted small fw-bold text-uppercase" width="35%"><?= lang('Common.full_name') ?></th>
                                <th class="py-3 border-0 text-muted small fw-bold text-uppercase text-center" width="40%"><?= lang('Common.status') ?></th>
                                <th class="py-3 border-0 text-muted small fw-bold text-uppercase pe-4" width="20%"><?= lang('Common.training_notes') ?></th>
                            </tr>
                        </thead>
                        <tbody id="attendanceBody">
                            <?php foreach ($groupPlayers as $index => $player):
                                $existing = $attendanceMap[$player['id']] ?? null;
                                $currentStatus = $existing['status'] ?? 'present';
                                $currentTime = $existing['check_in_time'] ?? '';
                                $currentNotes = $existing['notes'] ?? '';
                            ?>
                            <tr>
                                <td class="ps-4 text-muted fw-bold"><?= $index + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="<?= !empty($player['image_path']) ? base_url($player['image_path']) : base_url('public/img/img_profile.jpg') ?>"
                                             class="rounded-circle me-3 border"
                                             style="width: 40px; height: 40px; object-fit: cover;"
                                             alt="player">
                                        <div>
                                            <div class="fw-bold">
                                                <?php if (!empty($player['shirt_number'])): ?>
                                                    <span class="badge bg-primary me-1"><?= esc($player['shirt_number']) ?></span>
                                                <?php endif; ?>
                                                <?= esc($player['first_name'] ?? $player['name'] ?? '') ?>
                                                <?= esc($player['last_name'] ?? '') ?>
                                                <?php if (!empty($player['nick_name'])): ?>
                                                    <span class="text-muted fw-normal">(<?= esc($player['nick_name']) ?>)</span>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <input type="hidden" name="attendance[<?= $index ?>][player_id]" value="<?= esc($player['id']) ?>">
                                </td>
                                <td class="text-center">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <input type="radio" class="btn-check" name="attendance[<?= $index ?>][status]"
                                               id="present_<?= $player['id'] ?>" value="present"
                                               <?= ($currentStatus === 'present') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-success" for="present_<?= $player['id'] ?>">
                                            <?= lang('Common.attendance_present') ?>
                                        </label>

                                        <input type="radio" class="btn-check" name="attendance[<?= $index ?>][status]"
                                               id="absent_<?= $player['id'] ?>" value="absent"
                                               <?= ($currentStatus === 'absent') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-danger" for="absent_<?= $player['id'] ?>">
                                            <?= lang('Common.attendance_absent') ?>
                                        </label>

                                        <input type="radio" class="btn-check" name="attendance[<?= $index ?>][status]"
                                               id="late_<?= $player['id'] ?>" value="late"
                                               <?= ($currentStatus === 'late') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-warning" for="late_<?= $player['id'] ?>">
                                            <?= lang('Common.attendance_late') ?>
                                        </label>

                                        <input type="radio" class="btn-check" name="attendance[<?= $index ?>][status]"
                                               id="excused_<?= $player['id'] ?>" value="excused"
                                               <?= ($currentStatus === 'excused') ? 'checked' : '' ?>>
                                        <label class="btn btn-outline-secondary" for="excused_<?= $player['id'] ?>">
                                            <?= lang('Common.attendance_excused') ?>
                                        </label>
                                    </div>
                                </td>
                                <td class="pe-4">
                                    <input type="text" class="form-control form-control-sm"
                                           name="attendance[<?= $index ?>][notes]"
                                           value="<?= esc($currentNotes) ?>"
                                           placeholder="<?= lang('Common.training_notes') ?>">
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Save Button -->
                <?php if (!empty($perms['perm_save_attendance'])): ?>
                <div class="card-footer bg-white border-top text-center py-4">
                    <button type="button" class="btn btn-primary rounded-pill px-5 py-2" id="saveAttendanceBtn">
                        <i class="fas fa-save me-2"></i><?= lang('Common.save') ?>
                    </button>
                </div>
                <?php endif; ?>

            <?php else: ?>
                <!-- Empty State -->
                <div class="text-center py-5">
                    <div class="mb-4">
                        <i class="fas fa-user-check fa-4x text-muted opacity-25"></i>
                    </div>
                    <h5 class="fw-bold text-muted mb-2"><?= lang('Common.no_data') ?></h5>
                    <p class="text-muted small mb-0"><?= lang('Common.training_group_players') ?></p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const saveBtn = document.getElementById('saveAttendanceBtn');
        if (!saveBtn) return;

        saveBtn.addEventListener('click', function() {
            const rows = document.querySelectorAll('#attendanceBody tr');
            const attendanceData = [];
            const sharedTime = document.getElementById('sharedCheckInTime') ? document.getElementById('sharedCheckInTime').value : '';

            rows.forEach(function(row, index) {
                const playerIdInput = row.querySelector('input[name="attendance[' + index + '][player_id]"]');
                const statusInput = row.querySelector('input[name="attendance[' + index + '][status]"]:checked');
                const notesInput = row.querySelector('input[name="attendance[' + index + '][notes]"]');

                if (playerIdInput) {
                    attendanceData.push({
                        player_id: playerIdInput.value,
                        status: statusInput ? statusInput.value : 'present',
                        check_in_time: sharedTime,
                        notes: notesInput ? notesInput.value : ''
                    });
                }
            });

            saveBtn.disabled = true;
            saveBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span><?= lang('Common.loading') ?>';

            fetch('<?= base_url('academy/training-attendance/save') ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                body: JSON.stringify({
                    academy_id: '<?= esc($academy['id']) ?>',
                    session_id: '<?= esc($session['id']) ?>',
                    attendance: attendanceData
                })
            })
            .then(function(response) { return response.json(); })
            .then(function(data) {
                if (data.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '<?= lang('Common.success') ?>',
                        text: '<?= lang('Common.training_attendance_saved') ?>',
                        showConfirmButton: false,
                        timer: 1500
                    }).then(function() {
                        window.location.href = '<?= base_url('academy/training-session/detail/' . esc($academy['id']) . '/' . esc($session['id'])) ?>';
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: '<?= lang('Common.error') ?>',
                        text: data.message || '<?= lang('Common.error') ?>'
                    });
                }
            })
            .catch(function() {
                Swal.fire({
                    icon: 'error',
                    title: '<?= lang('Common.error') ?>',
                    text: '<?= lang('Common.error') ?>'
                });
            })
            .finally(function() {
                saveBtn.disabled = false;
                saveBtn.innerHTML = '<i class="fas fa-save me-2"></i><?= lang('Common.save') ?>';
            });
        });
    });
</script>
<?php $this->endSection(); ?>
