<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card shadow-lg">
    <div class="card-body">

        <?php echo view("Modules\Admin\Views\Tournament\subnav.php"); ?>

        <div class="row justify-content-center mt-4">
            <div class="col-md-12">
                <h4 class="mb-4">
                    <i class="ti ti-tournament"></i> <?= lang('Common.assign_teams_for_matches') ?>
                </h4>

                <!-- ข้อมูลการแข่งขัน -->
                <div class="alert alert-info mb-4">
                    <h6 class="mb-2"><?= lang('Common.match_info') ?>: <?= $tournament['name'] ?></h6>
                    <p class="mb-0">
                        <strong><?= lang('Common.number_of_teams') ?>:</strong> <?= count($teams) ?> <?= lang('Common.teams') ?> |
                        <strong><?= lang('Common.number_of_matches') ?>:</strong> <?= count($all_matches) ?> <?= lang('Common.matches_unit') ?>
                    </p>
                </div>

                <!-- Group Display - แสดงเป็น Group Card คล้ายตัวอย่าง -->
                <div class="groups-container mb-4">
                    <div class="row">
                        <?php foreach ($grouped_matches as $group_name => $matches): ?>
                            <div class="col-md-6 col-lg-3 mb-4">
                                <!-- Group Card -->
                                <div class="group-card" style="background: linear-gradient(135deg, #f5f5f5 0%, #e8e8e8 100%); border-radius: 12px; padding: 20px; min-height: auto; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">

                                    <!-- Group Title -->
                                    <div class="group-title mb-4">
                                        <h3 style="font-weight: 900; font-size: 36px; color: #001a4d; margin: 0;">
                                            GROUP <span><?= strtoupper($group_name) ?></span>
                                        </h3>
                                    </div>

                                    <!-- Teams List in Group -->
                                    <div class="teams-list mb-4">
                                        <?php
                                        // ดึงทีมที่อยู่ในสายนี้จากแมตช์ (ทีมที่ได้เลือกไปแล้ว)
                                        $teams_in_group = [];
                                        foreach ($matches as $match) {
                                            if ($match['team1_id']) {
                                                $teams_in_group[$match['team1_id']] = $teams[$match['team1_id']] ?? null;
                                            }
                                            if ($match['team2_id']) {
                                                $teams_in_group[$match['team2_id']] = $teams[$match['team2_id']] ?? null;
                                            }
                                        }
                                        ?>

                                        <!-- แสดงรายชื่อทีมที่อยู่ในกลุ่มนี้เท่านั้น -->
                                        <?php if (!empty($teams_in_group)): ?>
                                            <p style="font-size: 13px; color: #666; margin-bottom: 10px; font-weight: 500;">
                                                <i class="ti ti-users"></i> <?= lang('Common.teams_in_group') ?> (<?= count(array_filter($teams_in_group)) ?> <?= lang('Common.teams') ?>)
                                            </p>
                                            <?php foreach ($teams_in_group as $team_id => $team): ?>
                                                <?php if ($team): ?>
                                                    <div class="team-item mb-2">
                                                        <div class="team-badge" style="display: inline-block; width: 100%; padding: 10px 15px; background-color: #e8f4f8; border-radius: 20px; border: 2px solid #0d6efd; font-weight: 500; cursor: default; text-align: center;">
                                                            <?php if (!empty($team['logo'])): ?>
                                                                <img src="<?= base_url($team['logo']) ?>" alt="logo" style="height: 20px; margin-right: 8px; vertical-align: middle;">
                                                            <?php endif; ?>
                                                            <span><?= $team['name'] ?></span>
                                                        </div>
                                                    </div>
                                                <?php endif; ?>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <p style="color: #999; text-align: center; font-size: 13px; padding: 15px 0;">
                                                <i class="ti ti-inbox"></i> <?= lang('Common.no_teams_in_group') ?>
                                            </p>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Separator -->
                                    <hr style="margin: 15px 0; border: 1px solid #ddd;">

                                    <!-- Matches in Group -->
                                    <div class="matches-list">
                                        <h6 style="font-weight: 600; color: #333; margin-bottom: 10px;">
                                            <i class="ti ti-ball-football"></i> <?= lang('Common.matches_label') ?> (<?= count($matches) ?> <?= lang('Common.games_unit') ?>)
                                        </h6>

                                        <?php foreach ($matches as $idx => $match): ?>
                                            <div class="match-row mb-3" data-match-id="<?= $match['match_id'] ?>">
                                                <!-- Team 1 Select -->
                                                <div class="team-select-item mb-2">
                                                    <select class="form-select team-select"
                                                            name="team1_id"
                                                            data-match-id="<?= $match['match_id'] ?>"
                                                            data-team-type="team1"
                                                            style="border-radius: 20px; padding: 8px 12px; font-weight: 500; font-size: 12px; border: 2px solid #ccc; background-color: white;">
                                                        <option value="0">-- <?= lang('Common.select_team') ?> --</option>
                                                        <?php foreach ($teams as $team_id => $team): ?>
                                                            <option value="<?= $team_id ?>"
                                                                    <?= ($match['team1_id'] == $team_id) ? 'selected' : '' ?>>
                                                                <?= $team['name'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- VS Badge -->
                                                <div class="text-center mb-2" style="font-size: 11px; font-weight: bold; color: #999;">
                                                    VS
                                                </div>

                                                <!-- Team 2 Select -->
                                                <div class="team-select-item">
                                                    <select class="form-select team-select"
                                                            name="team2_id"
                                                            data-match-id="<?= $match['match_id'] ?>"
                                                            data-team-type="team2"
                                                            style="border-radius: 20px; padding: 8px 12px; font-weight: 500; font-size: 12px; border: 2px solid #ccc; background-color: white;">
                                                        <option value="0">-- <?= lang('Common.select_team') ?> --</option>
                                                        <?php foreach ($teams as $team_id => $team): ?>
                                                            <option value="<?= $team_id ?>"
                                                                    <?= ($match['team2_id'] == $team_id) ? 'selected' : '' ?>>
                                                                <?= $team['name'] ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>

                                                <!-- Round Info -->
                                                <div style="text-align: center; margin-top: 6px; font-size: 10px; color: #999;">
                                                    <?= $match['match_round'] ?? '-' ?>
                                                </div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="row mt-4">
                    <div class="col-md-12 text-end">
                        <a href="<?= base_url('admin/tournament') ?>" class="btn btn-secondary btn-lg">
                            <i class="ti ti-arrow-left"></i> <?= lang('Common.back') ?>
                        </a>
                        <button type="button" class="btn btn-success btn-lg" id="saveAllBtn" style="background: linear-gradient(135deg, #28a745 0%, #20c997 100%); border: none;">
                            <i class="ti ti-device-floppy"></i> <?= lang('Common.save_all') ?>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langError = '<?= lang("Common.error") ?>';
    const langSelectTeamsComplete = '<?= lang("Common.select_teams_complete") ?>';
    const langNoMatchToSave = '<?= lang("Common.no_match_to_save") ?>';
    const langConfirmSave = '<?= lang("Common.confirm_save") ?>';
    const langWillSaveMatches = '<?= lang("Common.will_save_matches") ?>';
    const langConfirm = '<?= lang("Common.confirm") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';
    const langSaving = '<?= lang("Common.saving") ?>';
    const langSuccess = '<?= lang("Common.success") ?>';
    const langSaveError = '<?= lang("Common.save_error") ?>';

$(document).ready(function() {
    // บันทึกทีมทั้งหมด
    $('#saveAllBtn').on('click', function() {
        const assignments = [];
        let isValid = true;

        // เก็บข้อมูลการจัดทีม
        $('[data-match-id]').each(function() {
            const matchId = $(this).data('match-id');
            const team1Id = $(this).find('select[data-team-type="team1"]').val();
            const team2Id = $(this).find('select[data-team-type="team2"]').val();

            // ตรวจสอบว่าเลือกทีมแล้ว
            if (team1Id === '0' || team2Id === '0') {
                isValid = false;
                $(this).css('border', '2px solid red');
                return;
            } else {
                $(this).css('border', '');
            }

            // ตรวจสอบว่าทีมซ้ำกันหรือไม่
            if (team1Id === team2Id) {
                isValid = false;
                $(this).css('border', '2px solid red');
                return;
            } else {
                $(this).css('border', '');
            }

            assignments.push({
                match_id: matchId,
                team1_id: team1Id,
                team2_id: team2Id
            });
        });

        if (!isValid) {
            Swal.fire(langError, langSelectTeamsComplete, 'error');
            return;
        }

        if (assignments.length === 0) {
            Swal.fire(langError, langNoMatchToSave, 'error');
            return;
        }

        // ยืนยันการบันทึก
        Swal.fire({
            title: langConfirmSave,
            html: `<p>${langWillSaveMatches.replace('%s', assignments.length)}</p>`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: langConfirm,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                saveAssignments(assignments);
            }
        });
    });

    function saveAssignments(assignments) {
        $.ajax({
            url: '<?= base_url('admin/tournament/bulk-assign-teams') ?>',
            type: 'POST',
            dataType: 'json',
            data: {
                assignments: JSON.stringify(assignments)
            },
            beforeSend: function() {
                Swal.fire({
                    title: langSaving,
                    allowOutsideClick: false,
                    didOpen: () => {
                        Swal.showLoading();
                    }
                });
            },
            success: function(response) {
                if (response.status === 'success') {
                    Swal.fire({
                        title: langSuccess,
                        text: response.msg,
                        icon: 'success'
                    }).then(() => {
                        window.location.href = '<?= base_url('admin/tournament') ?>';
                    });
                } else {
                    Swal.fire(langError, response.msg, 'error');
                }
            },
            error: function(xhr) {
                let errorMsg = langSaveError;
                if (xhr.responseJSON && xhr.responseJSON.msg) {
                    errorMsg = xhr.responseJSON.msg;
                }
                Swal.fire(langError, errorMsg, 'error');
            }
        });
    }

    // ไฮไลต์เมื่อเลือกทีม
    $(document).on('change', '.team-select', function() {
        const matchRow = $(this).closest('[data-match-id]');
        const team1 = matchRow.find('select[data-team-type="team1"]').val();
        const team2 = matchRow.find('select[data-team-type="team2"]').val();

        // เปลี่ยนสีพื้นหลังของ select ตามสถานะ
        matchRow.find('.team-select').css('background-color', 'white').css('border-color', '#ccc');

        if (team1 === '0' || team2 === '0') {
            matchRow.find('.team-select').css('background-color', '#fff3cd').css('border-color', '#ffc107');
        } else if (team1 === team2 && team1 !== '0') {
            matchRow.find('.team-select').css('background-color', '#f8d7da').css('border-color', '#dc3545');
        } else if (team1 !== '0' && team2 !== '0') {
            matchRow.find('.team-select').css('background-color', '#d4edda').css('border-color', '#28a745');
        }
    });

    // ตรวจสอบแบบเรียลไทม์เมื่อโหลดหน้า
    $('.team-select').trigger('change');
});
</script>
<?php $this->endSection(); ?>
