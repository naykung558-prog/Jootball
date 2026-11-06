<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<style>
    /* Select2 - Stadium dropdown theme */
    .select2-container--default .select2-selection--single {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        height: 38px;
        padding: 0.1rem 0.25rem;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 28px;
        color: #212529;
        padding-left: 0.5rem;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px;
        right: 6px;
    }
    .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #6c757d;
    }
    .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 4px;
        color: #6c757d;
    }
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .select2-dropdown {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        margin-top: 2px;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #dee2e6;
        border-radius: 0.375rem;
        padding: 0.4rem 0.6rem;
    }
    .select2-container--default .select2-results__option--highlighted[aria-selected] {
        background-color: #1e3c72;
    }
</style>
<?php echo view("Modules\Admin\Views\Tournament\subnav.php"); ?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold mb-0 text-dark"><i class="ti ti-edit me-2 text-primary"></i> <?= lang('Common.match_detail') ?></h5>
            </div>
            <div class="card-body p-4">
                <form action="<?= base_url('admin/tournament/save-tournament-match-result') ?>" method="POST"
                    enctype="multipart/form-data" class="once-only" autocomplete="off">

                    <!-- Hidden -->
                    <input type="hidden" name="match_id" value="<?= (!empty($data['match_id'])) ? $data['match_id'] : '' ?>">
                    <input type="hidden" name="tour_id" value="<?= (!empty($data['tour_id'])) ? $data['tour_id'] : '' ?>">
                    <input type="hidden" name="action_type" id="action_type" value="save">

                    <div class="row g-4">
                        <!-- Teams Section -->
                        <div class="col-12">
                            <h6 class="fw-bold text-muted border-bottom pb-2 mb-3"><?= lang('Common.team_info') ?></h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="team1_id" class="form-label fw-bold"><?= lang('Common.home_team') ?> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-home"></i></span>
                                        <select name="team1_id" id="team1_id" class="form-select select2 border-start-0" required>
                                            <option value=""><?= lang('Common.select_home_team') ?></option>
                                            <?php foreach ($match_options as $option) { ?>
                                                <option value="<?= $option['id'] ?>" <?= ($data['team1_id'] && $data['team1_id'] == $option['id']) ? 'selected' : '' ?>><?= $option['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="team2_id" class="form-label fw-bold"><?= lang('Common.away_team') ?> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-plane"></i></span>
                                        <select name="team2_id" id="team2_id" class="form-select select2 border-start-0" required>
                                            <option value=""><?= lang('Common.select_away_team') ?></option>
                                            <?php foreach ($match_options as $option) { ?>
                                                <option value="<?= $option['id'] ?>" <?= ($data['team2_id'] && $data['team2_id'] == $option['id']) ? 'selected' : '' ?>><?= $option['name'] ?></option>
                                            <?php } ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Match Info Section -->
                        <div class="col-12">
                            <h6 class="fw-bold text-muted border-bottom pb-2 mb-3"><?= lang('Common.match_info') ?></h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="date" class="form-label fw-bold"><?= lang('Common.match_date') ?> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-calendar"></i></span>
                                        <input type="text" class="form-control date_picker border-start-0" id="date" name="date" value="<?= ($data['date']) ? date('d/m/Y', strtotime($data['date'])) : '' ?>" placeholder="<?= lang('Common.date_placeholder') ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="time" class="form-label fw-bold"><?= lang('Common.time') ?> <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-clock"></i></span>
                                        <input type="text" class="form-control time border-start-0" id="time" name="time" value="<?= ($data['time']) ? $data['time'] : '' ?>" placeholder="00:00">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="stadium" class="form-label fw-bold"><?= lang('Common.stadium') ?></label>
                                    <div class="d-flex gap-2 align-items-start">
                                        <div class="flex-fill">
                                            <select class="form-select stadium-select2" id="stadium" name="stadium">
                                                <option value=""><?= lang('Common.select_stadium') ?></option>
                                                <?php if (!empty($stadiums)): ?>
                                                    <?php foreach ($stadiums as $s): ?>
                                                    <option value="<?= esc($s['name']) ?>" <?= (isset($data['stadium']) && $data['stadium'] == $s['name']) ? 'selected' : '' ?>><?= esc($s['name']) ?><?= !empty($s['name_en']) ? ' (' . esc($s['name_en']) . ')' : '' ?></option>
                                                    <?php endforeach; ?>
                                                <?php endif; ?>
                                            </select>
                                        </div>
                                        <button type="button" class="btn btn-outline-success btn-sm btn-add-stadium-detail" title="<?= lang('Common.add_new_stadium') ?>" style="border-radius:8px; height:38px;">
                                            <i class="ti ti-plus"></i>
                                        </button>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <label for="referee" class="form-label fw-bold"><?= lang('Common.referee') ?></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-whistle"></i></span>
                                        <input type="text" class="form-control border-start-0" id="referee" name="referee" value="<?= ($data['referee']) ? $data['referee'] : '' ?>" placeholder="<?= lang('Common.enter_referee_name') ?>">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Score & Group Section -->
                        <div class="col-12">
                            <h6 class="fw-bold text-muted border-bottom pb-2 mb-3"><?= lang('Common.result_and_group') ?></h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="team1_score" class="form-label fw-bold"><?= lang('Common.home_team_score') ?></label>
                                    <input type="number" class="form-control text-center fw-bold text-primary" id="team1_score" name="team1_score" value="<?= ($data['team1_score']) ? $data['team1_score'] : '' ?>" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="team2_score" class="form-label fw-bold"><?= lang('Common.away_team_score') ?></label>
                                    <input type="number" class="form-control text-center fw-bold text-danger" id="team2_score" name="team2_score" value="<?= ($data['team2_score']) ? $data['team2_score'] : '' ?>" min="0">
                                </div>
                                <div class="col-md-4">
                                    <label for="group_id" class="form-label fw-bold"><?= lang('Common.player_group') ?></label>
                                    <select name="group_id" id="group_id" class="form-select" required>
                                        <option value=""><?= lang('Common.select_group') ?></option>
                                        <?php
                                        $groups = range('A', 'Z');
                                        foreach ($groups as $group) { ?>
                                            <option value="<?= $group ?>" <?= ($data['group_id'] == $group) ? 'selected' : '' ?>><?= $group ?></option>
                                        <?php } ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-center gap-3 mt-5">
                        <a href="<?= base_url('admin/tournament/tournament-match-result/' . $tour_id) ?>" class="btn btn-light border px-4 fw-bold"><i class="ti ti-arrow-left me-2"></i> <?= lang('Common.back') ?></a>
                        <button type="button" class="btn btn-primary px-5 fw-bold btn-submit shadow-sm" onclick="submitForm('save')"><i class="ti ti-device-floppy me-2"></i> <?= lang('Common.save_data') ?></button>
                        <button type="button" class="btn btn-success px-5 fw-bold btn-submit shadow-sm" onclick="submitForm('end_match')"><i class="ti ti-check me-2"></i> <?= lang('Common.confirm_end_match') ?></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('.date_picker').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
            language: 'th-en',
        });
        $('.time').mask('00:00');

        // เก็บ option ทั้งหมดไว้
        var allTeam1Options = $('#team1_id option').clone();
        var allTeam2Options = $('#team2_id option').clone();

        // ฟังก์ชันสำหรับ filter ทีม
        function filterTeamOptions() {
            var selectedTeam1 = $('#team1_id').val();
            var selectedTeam2 = $('#team2_id').val();

            // Reset options
            $('#team1_id').html(allTeam1Options.clone());
            $('#team2_id').html(allTeam2Options.clone());

            // ถ้าเลือกทีมเหย้าแล้ว ให้ซ่อนทีมนั้นออกจากตัวเลือกทีมเยือน
            if (selectedTeam1) {
                $('#team2_id option[value="' + selectedTeam1 + '"]').remove();
            }

            // ถ้าเลือกทีมเยือนแล้ว ให้ซ่อนทีมนั้นออกจากตัวเลือกทีมเหย้า
            if (selectedTeam2) {
                $('#team1_id option[value="' + selectedTeam2 + '"]').remove();
            }

            // Set ค่ากลับไป
            $('#team1_id').val(selectedTeam1);
            $('#team2_id').val(selectedTeam2);

            // Trigger select2 refresh ถ้ามีใช้ select2
            if ($('#team1_id').hasClass('select2')) {
                $('#team1_id').trigger('change.select2');
                $('#team2_id').trigger('change.select2');
            }
        }

        // เมื่อเลือกทีมเหย้า
        $('#team1_id').on('change', function() {
            filterTeamOptions();
        });

        // เมื่อเลือกทีมเยือน
        $('#team2_id').on('change', function() {
            filterTeamOptions();
        });

        // Run เมื่อโหลดหน้าครั้งแรก (กรณีแก้ไข)
        filterTeamOptions();
    });

    // Stadium Select2
    const langSelectStadium = '<?= lang("Common.select_stadium") ?>';
    const langAddNewStadium = '<?= lang("Common.add_new_stadium") ?>';
    const langNewStadiumName = '<?= lang("Common.new_stadium_name") ?>';
    const langStadiumNameRequired = '<?= lang("Common.stadium_name_required") ?>';
    const langStadiumNameDuplicate = '<?= lang("Common.stadium_name_duplicate") ?>';
    const langStadiumAddedSuccess = '<?= lang("Common.stadium_added_success") ?>';
    const langSave = '<?= lang("Common.save") ?>';
    const langError = '<?= lang("Common.error") ?>';

    $(document).ready(function() {
        $('.stadium-select2').select2({
            placeholder: langSelectStadium,
            allowClear: true,
            width: '100%'
        });
    });

    $('.btn-add-stadium-detail').on('click', function() {
        const $select = $('.stadium-select2');
        Swal.fire({
            title: langAddNewStadium,
            input: 'text',
            inputLabel: langNewStadiumName,
            inputPlaceholder: langNewStadiumName,
            showCancelButton: true,
            confirmButtonText: '<i class="ti ti-check me-1"></i> ' + langSave,
            cancelButtonText: langCancel,
            inputValidator: (value) => {
                if (!value || !value.trim()) {
                    return langStadiumNameRequired;
                }
            },
            showLoaderOnConfirm: true,
            preConfirm: (name) => {
                return $.ajax({
                    url: '<?= base_url("admin/stadium/quick-add") ?>',
                    method: 'POST',
                    data: { name: name.trim() },
                    dataType: 'json'
                }).then(function(res) {
                    if (res.status === 'duplicate') {
                        Swal.showValidationMessage(langStadiumNameDuplicate);
                        return false;
                    }
                    if (res.status === 'error') {
                        Swal.showValidationMessage(res.msg);
                        return false;
                    }
                    return res;
                }).catch(function() {
                    Swal.showValidationMessage(langError);
                });
            },
            allowOutsideClick: () => !Swal.isLoading()
        }).then((result) => {
            if (result.isConfirmed && result.value && result.value.status === 'success') {
                const newName = result.value.stadium.name;
                if ($select.find('option[value="' + newName + '"]').length === 0) {
                    $select.append(new Option(newName, newName, true, true));
                } else {
                    $select.val(newName);
                }
                $select.trigger('change');
                Swal.fire({ icon: 'success', title: langStadiumAddedSuccess, timer: 1200, showConfirmButton: false });
            }
        });
    });

    const langConfirmEndMatch = '<?= lang("Common.confirm_end_match") ?>';
    const langEndMatchWarning = '<?= lang("Common.end_match_warning") ?>';
    const langConfirm = '<?= lang("Common.confirm") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';

    function submitForm(action) {
        $('#action_type').val(action);

        if(action == 'end_match'){
            Swal.fire({
                title: langConfirmEndMatch,
                text: langEndMatchWarning,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#28a745',
                cancelButtonColor: '#d33',
                confirmButtonText: langConfirm,
                cancelButtonText: langCancel
            }).then((result) => {
                if (result.isConfirmed) {
                    $('form').submit();
                }
            })
        } else {
            $('form').submit();
        }
    }
</script>
<?php $this->endSection() ?>