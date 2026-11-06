<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="card shadow-sm">
    <div class="card-body">
        <?php echo view("Modules\Team\Views\subnav.php"); ?>

        <form action="<?= base_url('player/save-player') ?>" method="POST" enctype="multipart/form-data" class="mt-4" autocomplete="off">

            <input type="hidden" name="id" value="<?= (!empty($player['id'])) ? $player['id'] : '' ?>">
            <?php
            $_academyId = isset($academy_id) ? (int)$academy_id : 0;
            $_teamIdValue = $_academyId > 0
                ? '' 
                : ((!empty($player['team_id'])) ? $player['team_id'] : (isset($team_id) ? $team_id : session()->get('team.id')));
            ?>
            <input type="hidden" name="team_id" value="<?= $_teamIdValue ?>">
            <input type="hidden" name="academy_id" value="<?= $_academyId ?>">
            <input type="hidden" id="image_personel" name="base64_image">

            <!-- Player Header Card -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-body py-3 px-4 d-flex align-items-center gap-3">
                    <?php if (!empty($player['image_path'])) : ?>
                    <img id="personel_image_display" src="<?= base_url($player['image_path']) ?>" alt="Player Profile"
                        class="rounded-circle border border-2 shadow-sm" style="width: 64px; height: 64px; object-fit: cover;">
                    <?php else : ?>
                    <img id="personel_image_display" src="<?= base_url('public/img/img_profile.jpg') ?>" alt="Default Profile" 
                        class="rounded-circle border border-2 shadow-sm" style="width: 64px; height: 64px; object-fit: cover;">
                    <?php endif; ?>
                    
                    <div>
                        <h5 class="fw-bold mb-0 text-primary"><?= lang('Common.player_data') ?></h5>
                        <small class="text-muted">
                            <?= !empty($player['first_name']) ? esc($player['first_name'] . ' ' . $player['last_name']) : 'เพิ่มนักเตะใหม่' ?>
                            <?= !empty($player['nick_name']) ? '("'.esc($player['nick_name']).'")' : '' ?>
                        </small>
                    </div>
                    <div class="ms-auto">
                        <button type="button" class="btn btn-outline-primary rounded-pill px-3" data-bs-toggle="modal" data-bs-target="#cropperModal">
                            <i class="fas fa-camera me-1"></i> <?= lang('Common.upload_image') ?>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Section A: ข้อมูลส่วนตัว -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <span class="badge bg-primary me-2">A</span><?= lang('Common.personal_info') ?>
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="first_name" class="form-label fw-semibold text-muted small"><?= lang('Common.first_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="first_name" id="first_name" value="<?= old('first_name', $player['first_name'] ?? '') ?>" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="last_name" class="form-label fw-semibold text-muted small"><?= lang('Common.last_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="last_name" id="last_name" value="<?= old('last_name', $player['last_name'] ?? '') ?>" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="nick_name" class="form-label fw-semibold text-muted small"><?= lang('Common.nick_name') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="nick_name" id="nick_name" value="<?= old('nick_name', $player['nick_name'] ?? '') ?>" class="form-control bg-light border-0" required>
                        </div>

                        <div class="col-md-4">
                            <label for="nation" class="form-label fw-semibold text-muted small"><?= lang('Common.nationality') ?> <span class="text-danger">*</span></label>
                            <select name="nation" id="nation" class="form-select bg-light border-0" required>
                                <option value="THAI" <?= old('nation', $player['nation'] ?? 'THAI') == 'THAI' ? 'selected' : '' ?>><?= lang('Common.thai') ?></option>
                                <option value="OTHER" <?= old('nation', $player['nation'] ?? '') == 'OTHER' ? 'selected' : '' ?>><?= lang('Common.foreign_nationality') ?></option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="id_card" class="form-label fw-semibold text-muted small"><?= lang('Common.id_card_passport') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="id_card" id="id_card" value="<?= old('id_card', $player['id_card'] ?? '') ?>" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-4">
                            <label for="birth_date" class="form-label fw-semibold text-muted small"><?= lang('Common.birth_date_full') ?> <span class="text-danger">*</span></label>
                            <input type="text" name="birth_date" id="birth_date" value="<?= (!empty($player['birth_date'])) ? date('d/m/Y', strtotime($player['birth_date'])) : '' ?>" class="form-control date-picker bg-light border-0" required>
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label fw-semibold text-muted small">เพศ</label>
                            <div class="d-flex gap-3 mt-1">
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sex" id="sex_m" value="M" <?= (old('sex', $player['sex'] ?? '') === 'M') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="sex_m">
                                        <i class="fas fa-male text-primary me-1"></i>ชาย
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="sex" id="sex_w" value="W" <?= (old('sex', $player['sex'] ?? '') === 'W') ? 'checked' : '' ?>>
                                    <label class="form-check-label" for="sex_w">
                                        <i class="fas fa-female text-danger me-1"></i>หญิง
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-12 mt-3">
                            <label class="form-label fw-semibold text-muted small"><?= lang('Common.id_card_image') ?></label>
                            <input type="hidden" name="id_card_image_base64" id="id_card_image_base64">
                            <input type="hidden" name="delete_id_card_image" id="delete_id_card_image" value="0">
                            
                            <div class="mb-3">
                                <?php if (!empty($player['id_card_image'])): ?>
                                <div id="idCardPreviewWrapper" class="card border-0 shadow-sm d-inline-block">
                                    <div class="card-body p-2">
                                        <img id="idCardPreview" src="<?= id_card_url($player['id_card_image']) ?>" class="rounded-3" style="max-height: 150px; cursor: pointer;" onclick="viewIdCardFull(this.src)" title="<?= lang('Common.view_id_card') ?>">
                                    </div>
                                </div>
                                <?php else: ?>
                                <div id="idCardPreviewWrapper" class="text-center p-4 bg-light rounded-4 border d-inline-block" style="min-width: 250px; display:none !important;">
                                    <img id="idCardPreview" src="" class="rounded-3" style="max-height: 150px; cursor: pointer;" onclick="viewIdCardFull(this.src)">
                                </div>
                                <div id="idCardPlaceholder" class="text-center p-4 bg-light rounded-4 border" style="max-width: 350px;">
                                    <i class="fas fa-id-card fa-3x text-muted mb-2 opacity-50"></i>
                                    <p class="text-muted small mb-0 fw-semibold">ยังไม่มีรูปถ่ายบัตรประชาชน</p>
                                </div>
                                <?php endif; ?>
                            </div>

                            <div class="row align-items-center g-2">
                                <div class="col-auto">
                                    <label for="idCardFileInput" class="btn btn-outline-primary rounded-pill btn-sm" style="cursor:pointer;">
                                        <i class="fas fa-upload me-1"></i><?= lang('Common.upload_id_card') ?>
                                    </label>
                                    <input type="file" id="idCardFileInput" accept="image/jpeg,image/png" style="display:none;">
                                </div>
                                <?php if (!empty($player['id_card_image'])): ?>
                                <div class="col-auto">
                                    <button type="button" class="btn btn-outline-danger rounded-pill btn-sm" id="btnDeleteIdCard">
                                        <i class="fas fa-trash me-1"></i><?= lang('Common.delete_id_card_image') ?>
                                    </button>
                                </div>
                                <?php endif; ?>
                                <div class="col-12">
                                    <small class="text-muted"><i class="fas fa-info-circle me-1"></i><?= lang('Common.id_card_image_hint') ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section B: ข้อมูลกายภาพ -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <span class="badge bg-success me-2">B</span><?= lang('Common.physical_info') ?>
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <label for="dominant_foot" class="form-label fw-semibold text-muted small"><?= lang('Common.dominant_foot') ?> <span class="text-danger">*</span></label>
                            <select name="dominant_foot" id="dominant_foot" class="form-select bg-light border-0" required>
                                <option value=""><?= lang('Common.select_option') ?></option>
                                <option value="right" <?= old('dominant_foot', $player['dominant_foot'] ?? '') == 'right' ? 'selected' : '' ?>><?= lang('Common.foot_right') ?></option>
                                <option value="left" <?= old('dominant_foot', $player['dominant_foot'] ?? '') == 'left' ? 'selected' : '' ?>><?= lang('Common.foot_left') ?></option>
                                <option value="both" <?= old('dominant_foot', $player['dominant_foot'] ?? '') == 'both' ? 'selected' : '' ?>><?= lang('Common.foot_both') ?></option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="height" class="form-label fw-semibold text-muted small"><?= lang('Common.height_cm') ?> <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="height" id="height" value="<?= old('height', $player['height'] ?? '') ?>" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-3">
                            <label for="weight" class="form-label fw-semibold text-muted small"><?= lang('Common.weight_kg') ?> <span class="text-danger">*</span></label>
                            <input type="number" step="0.1" name="weight" id="weight" value="<?= old('weight', $player['weight'] ?? '') ?>" class="form-control bg-light border-0" required>
                        </div>
                        <div class="col-md-3">
                            <label for="bmi" class="form-label fw-semibold text-muted small">BMI</label>
                            <input type="text" name="bmi" id="bmi" value="<?= old('bmi', $player['bmi'] ?? '') ?>" class="form-control bg-light border-0" readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section Domicile: ภูมิลำเนา -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <span class="badge bg-info text-white me-2"><i class="fas fa-map-marker-alt"></i></span><?= lang('Common.domicile') ?>
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="province" class="form-label fw-semibold text-muted small"><?= lang('Common.province') ?></label>
                            <select name="province_id" id="province" class="form-select bg-light border-0 select2">
                                <option value=""><?= lang('Common.select_province') ?></option>
                                <?php foreach ($provinces as $province) : ?>
                                <option value="<?= $province['province_id'] ?>" <?= (old('province_id', $player['province_id'] ?? '') == $province['province_id']) ? 'selected' : '' ?>>
                                    <?= $province['province_name'] ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="amphure" class="form-label fw-semibold text-muted small"><?= lang('Common.district') ?></label>
                            <select name="amphure_id" id="amphure" class="form-select bg-light border-0 select2" disabled>
                                <option value=""><?= lang('Common.select_district') ?></option>
                                <?php if (!empty($player['amphure_id'])): ?>
                                <option value="<?= $player['amphure_id'] ?>" selected><?= $player['amphure_name'] ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="tambon" class="form-label fw-semibold text-muted small"><?= lang('Common.subdistrict') ?></label>
                            <select name="tambon_id" id="tambon" class="form-select bg-light border-0 select2" disabled>
                                <option value=""><?= lang('Common.select_subdistrict') ?></option>
                                <?php if (!empty($player['tambon_id'])): ?>
                                <option value="<?= $player['tambon_id'] ?>" selected><?= $player['tambon_name'] ?></option>
                                <?php endif; ?>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section: ทีมที่สังกัดในปัจจุบัน (กรณีมี ID Player แล้ว) -->
            <?php if (!empty($player['id'])): ?>
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <span class="badge bg-secondary me-2"><i class="fas fa-users"></i></span>ทีมที่สังกัด
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div id="player-teams-list" class="mb-3">
                        <?php if (!empty($player_teams)): ?>
                            <?php foreach ($player_teams as $pt): ?>
                            <div class="d-flex align-items-center justify-content-between py-2 border-bottom" id="pt-row-<?= $pt['team_id'] ?>">
                                <div class="d-flex align-items-center gap-2">
                                    <?php if (!empty($pt['team_logo'])): ?>
                                    <img src="<?= base_url($pt['team_logo']) ?>" style="width:32px;height:32px;object-fit:cover;" class="rounded-circle border">
                                    <?php else: ?>
                                    <div class="rounded-circle border bg-light d-flex align-items-center justify-content-center" style="width:32px;height:32px;"><i class="fas fa-shield-alt text-secondary" style="font-size:14px;"></i></div>
                                    <?php endif; ?>
                                    <div>
                                        <span class="fw-semibold"><?= esc($pt['team_name']) ?></span>
                                        <?php if ($pt['is_primary']): ?>
                                        <span class="badge bg-primary ms-1" style="font-size:10px;">ทีมหลัก</span>
                                        <?php endif; ?>
                                        <?php if (!empty($pt['academy_name'])): ?>
                                        <small class="text-muted ms-1">/ <?= esc($pt['academy_name']) ?></small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-outline-danger btn-sm rounded-circle" onclick="removePlayerFromTeam(<?= $player['id'] ?>, <?= $pt['team_id'] ?>, '<?= esc($pt['team_name']) ?>')">
                                    <i class="fas fa-times"></i>
                                </button>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <p class="text-muted small mb-2">ยังไม่มีทีมที่สังกัด</p>
                        <?php endif; ?>
                    </div>
                    <button type="button" class="btn btn-outline-primary rounded-pill btn-sm" data-bs-toggle="modal" data-bs-target="#addTeamModal">
                        <i class="fas fa-plus me-1"></i>เพิ่มทีม
                    </button>
                </div>
            </div>
            <?php endif; ?>

            <!-- Section C: ข้อมูลทีมและตำแหน่ง -->
            <div class="card shadow-sm border-0 rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h5 class="fw-bold mb-0 text-dark">
                        <span class="badge bg-warning text-dark me-2">C</span><?= lang('Common.team_data') ?>
                    </h5>
                </div>
                <div class="card-body px-4 pb-4">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label for="player_position_id" class="form-label fw-semibold text-muted small">ตำแหน่งหลัก</label>
                            <select name="player_position_id" id="player_position_id" class="form-select bg-light border-0 select2">
                                <option value="">-- เลือกตำแหน่ง --</option>
                                <?php
                                $current_main_position = old('player_position_id', $player['player_position_id'] ?? '');
                                $positions_list = $positions ?? ($position ?? []);
                                foreach ($positions_list as $pos): ?>
                                <option value="<?= $pos['id'] ?>" <?= (string)$current_main_position === (string)$pos['id'] ? 'selected' : '' ?>>
                                    <?= esc($pos['name_th']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label for="player_sub_position_id" class="form-label fw-semibold text-muted small">ตำแหน่งรอง</label>
                            <select name="player_sub_position_id[]" id="player_sub_position_id" class="form-select bg-light border-0 select2" multiple>
                                <?php
                                $selected_positions = [];
                                $raw_data = old('player_sub_position_id', $player['player_sub_position_id'] ?? []);

                                if (is_string($raw_data) && $raw_data !== '') {
                                    $decoded = json_decode($raw_data, true);
                                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                                        $selected_positions = $decoded;
                                    } else {
                                        $selected_positions = array_filter(explode(',', $raw_data));
                                    }
                                } elseif (is_array($raw_data)) {
                                    $selected_positions = $raw_data;
                                }

                                $sub_positions_list = $position ?? ($positions ?? []);
                                foreach ($sub_positions_list as $pos) { ?>
                                <option value="<?= $pos['id'] ?>" <?= in_array((string)$pos['id'], array_map('strval', $selected_positions), true) ? 'selected' : '' ?>>
                                    <?= esc($pos['name_th']) ?>
                                </option>
                                <?php } ?>
                            </select>
                            <small class="text-muted d-block mt-1">กด Ctrl/Cmd เพื่อเลือกหลายตำแหน่ง</small>
                        </div>

                        <div class="col-md-4">
                            <label for="shirt_number" class="form-label fw-semibold text-muted small"><?= lang('Common.shirt_number_label') ?> <span class="text-danger" id="label-required-shirt">*</span></label>
                            <input type="text" name="shirt_number" id="shirt_number" value="<?= old('shirt_number', $player['shirt_number'] ?? '') ?>" class="form-control shirt_number bg-light border-0" required>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="row mt-4 mb-5">
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary rounded-pill btn-lg px-5 mx-2 shadow-sm">
                        <i class="fa fa-save me-2"></i> <?= lang('Common.save_data') ?>
                    </button>
                    <?php
                    $backUrl = base_url('team/player-list');
                    $backTeamId = (!empty($player['team_id'])) ? $player['team_id'] : (isset($team_id) ? $team_id : session()->get('team.id'));
                    if ($backTeamId) {
                        $academyBackUrl = getAcademyUrlForTeam($backTeamId, 'player-list');
                        if ($academyBackUrl) {
                            $backUrl = $academyBackUrl;
                        } elseif (session()->get('user_type') === 'coach') {
                            $backUrl = base_url('team/player-list/' . $backTeamId);
                        }
                    }
                    ?>
                    <a href="<?= $backUrl ?>" class="btn btn-light rounded-pill btn-lg px-4 mx-2 border">
                        <i class="fa fa-arrow-left me-2"></i> <?= lang('Common.back_to_main') ?>
                    </a>
                </div>
            </div>
        </form>

    </div>
</div>

<script>
    // Convert empty address fields to null before form submission
    document.querySelector('form').addEventListener('submit', function (e) {
        const provinceId = document.querySelector('input[name="province_id"]');
        const amphureId = document.querySelector('input[name="amphure_id"]');
        const tambonId = document.querySelector('input[name="tambon_id"]');

        // If fields are empty, send empty string (which PHP will handle)
        // or you can add hidden fields to send null
        if (document.querySelector('select[name="province_id"]').value === '') {
            document.querySelector('select[name="province_id"]').value = '';
        }
    });
</script>

<!-- Modal เพิ่มทีมให้นักเตะ -->
<?php if (!empty($player['id'])): ?>
<div class="modal fade" id="addTeamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="fas fa-plus me-2"></i>เพิ่มทีมให้นักเตะ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label">ค้นหาทีม</label>
                    <div class="input-group">
                        <input type="text" id="searchTeamInput" class="form-control" placeholder="พิมพ์ชื่อทีม...">
                        <button class="btn btn-primary" type="button" onclick="searchTeams()">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                <div id="searchTeamResults"></div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="modal fade" id="cropperModal" tabindex="-1" aria-labelledby="cropperModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="cropperModalLabel"><?= lang('Common.upload_and_crop_title') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label for="uploadImage" class="form-label"><?= lang('Common.choose_image_file') ?></label>
                    <input type="file" id="uploadImage" class="form-control" accept="image/*" />
                </div>
                <div class="row">
                    <div class="col-md-7">
                        <div class="img-container" style="max-height: 400px;">
                            <img id="cropImage" style="max-width: 100%; display: none;" />
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="fw-bold mb-2"><?= lang('Common.preview_label') ?></div>
                        <div id="preview" class="border"
                            style="width: 250px; height: 250px; overflow: hidden; margin: auto;"></div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" id="cropButton" class="btn btn-primary"><?= lang('Common.save_image') ?></button>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>


<?php $this->section('css'); ?>
<style>
    fieldset {
        background-color: #f8f9fa;
    }

    legend {
        font-size: 1rem;
        font-weight: 600;
    }
</style>
<?php $this->endSection(); ?>


<?php $this->section('scripts'); ?>
<script>
    // --- Language Constants ---
    const langSelectDistrict = '<?= lang('Common.select_district') ?>';
    const langSelectSubdistrict = '<?= lang('Common.select_subdistrict') ?>';
    const langLoading = '<?= lang('Common.loading') ?>';
    const langLoadFailed = '<?= lang('Common.load_failed') ?>';
    const langInvalidFileType = '<?= lang('Common.invalid_file_type') ?>';
    const langFileTooLarge = '<?= lang('Common.file_too_large') ?>';
    const langErrorOccurred = '<?= lang('Common.error_occurred') ?>';
    const langConfirmDeleteIdCard = '<?= lang('Common.confirm_delete_id_card') ?>';
    const langDeleteIdCardImage = '<?= lang('Common.delete_id_card_image') ?>';
    const langConfirm = '<?= lang('Common.confirm') ?>';
    const langCancel = '<?= lang('Common.cancel') ?>';
    const langViewIdCard = '<?= lang('Common.view_id_card') ?>';

    // --- ID Card Image Upload ---
    document.getElementById('idCardFileInput').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png'];
        if (!allowedTypes.includes(file.type)) {
            Swal.fire({ icon: 'error', title: langErrorOccurred, text: langInvalidFileType });
            this.value = '';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            Swal.fire({ icon: 'error', title: langErrorOccurred, text: langFileTooLarge });
            this.value = '';
            return;
        }

        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('id_card_image_base64').value = ev.target.result;
            document.getElementById('idCardPreview').src = ev.target.result;
            document.getElementById('idCardPreviewWrapper').style.display = '';
            document.getElementById('delete_id_card_image').value = '0';

            // Add delete button if not exists
            if (!document.getElementById('btnDeleteIdCard')) {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'btn btn-outline-danger btn-sm mt-1';
                btn.id = 'btnDeleteIdCard';
                btn.innerHTML = '<i class="fas fa-trash me-1"></i>' + langDeleteIdCardImage;
                btn.onclick = deleteIdCardImage;
                document.getElementById('idCardFileInput').parentNode.querySelector('small').after(document.createElement('br'), btn);
            }
        };
        reader.readAsDataURL(file);
    });

    function viewIdCardFull(src) {
        Swal.fire({
            title: langViewIdCard,
            imageUrl: src,
            imageAlt: langViewIdCard,
            showConfirmButton: true,
            confirmButtonText: langCancel,
            width: 'auto',
            imageWidth: '100%',
            imageHeight: 'auto'
        });
    }

    function deleteIdCardImage() {
        Swal.fire({
            title: langDeleteIdCardImage,
            text: langConfirmDeleteIdCard,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: langConfirm,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete_id_card_image').value = '1';
                document.getElementById('id_card_image_base64').value = '';
                document.getElementById('idCardPreview').src = '';
                document.getElementById('idCardPreviewWrapper').style.display = 'none';
                document.getElementById('idCardFileInput').value = '';
            }
        });
    }

    // Bind delete button if exists
    if (document.getElementById('btnDeleteIdCard')) {
        document.getElementById('btnDeleteIdCard').addEventListener('click', deleteIdCardImage);
    }

    // ค่าบริบท (ใช้ทั้งเพิ่มใหม่/แก้ไข)
    const PLAYER_TEAM_ID    = <?= (int)($_teamIdValue ?: 0) ?>;
    const PLAYER_ACADEMY_ID = <?= (int)$_academyId ?>;
    const PLAYER_EDIT_ID    = <?= !empty($player['id']) ? (int)$player['id'] : 0 ?>;

    // เพิ่มนักเตะเดิมจากระบบ (ใช้ทั้ง real-time เช็คบัตร และตอน submit ซ้ำ)
    function promptAddPlayerFromSystem(dupId, dupName) {
        Swal.fire({
            title: '<?= lang('Common.confirm_add_player') ?>',
            html: '<?= lang('Common.want_add_player') ?>'.replace('%s', dupName),
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#28a745',
            cancelButtonColor: '#6c757d',
            confirmButtonText: '<?= lang('Common.add_player') ?>',
            cancelButtonText: '<?= lang('Common.cancel') ?>'
        }).then(function (r) {
            if (!r.isConfirmed) return;
            const headers = { 'X-Requested-With': 'XMLHttpRequest' };
            // สำเร็จ → แสดง popup แจ้งผล (ข้อความจาก response เหมือน flow หลัก) แล้วค่อยไปหน้ารายการ
            const done = function (res, redirectUrl) {
                if (res && res.status === 'success') {
                    Swal.fire({
                        icon: 'success',
                        title: '<?= lang('Common.success') ?>',
                        text: (res.message || ''),
                        timer: 1500,
                        showConfirmButton: false
                    }).then(function () { window.location.href = redirectUrl; });
                } else {
                    Swal.fire('เกิดข้อผิดพลาด', (res && res.message) ? res.message : 'ไม่สามารถเพิ่มนักเตะได้', 'error');
                }
            };
            if (PLAYER_ACADEMY_ID > 0) {
                // ใช้ endpoint เดียวกับ flow หลัก (academy/player-enroll, team_id:0) + popup แจ้งสำเร็จ
                fetch('<?= base_url('academy/player-enroll') ?>/' + PLAYER_ACADEMY_ID, {
                    method: 'POST',
                    headers: Object.assign({ 'Content-Type': 'application/json' }, headers),
                    body: JSON.stringify({ player_id: dupId, team_id: 0, shirt_number: '' })
                }).then(function (r) { return r.json(); })
                  .then(function (res) { done(res, '<?= base_url('academy/players') ?>/' + PLAYER_ACADEMY_ID); })
                  .catch(function () { Swal.fire('เกิดข้อผิดพลาด', 'เชื่อมต่อไม่สำเร็จ', 'error'); });
            } else if (PLAYER_TEAM_ID > 0) {
                fetch('<?= base_url('player/add-player-to-team') ?>', {
                    method: 'POST',
                    headers: Object.assign({ 'Content-Type': 'application/x-www-form-urlencoded' }, headers),
                    body: 'player_id=' + dupId + '&team_id=' + PLAYER_TEAM_ID
                }).then(function (r) { return r.json(); })
                  .then(function (res) { done(res, '<?= base_url('team/player-list') ?>/' + PLAYER_TEAM_ID); })
                  .catch(function () { Swal.fire('เกิดข้อผิดพลาด', 'เชื่อมต่อไม่สำเร็จ', 'error'); });
            } else {
                Swal.fire('', 'ไม่พบทีม/Academy ปลายทางสำหรับเพิ่มนักเตะ', 'info');
            }
        });
    }

    // real-time: พิมพ์เลขบัตรครบ 13 หลัก → เช็คซ้ำในระบบทันที
    // ฟัง keyup ด้วย เพราะ mask plugin เซ็ตค่าผ่าน .val() (ไม่ fire input ตอนพิมพ์ทีละตัว)
    let __lastCheckedIdCard = '';
    function checkIdCardDuplicate(rawVal) {
        const digits = (rawVal || '').replace(/\D/g, '');
        if (digits.length !== 13) {
            __lastCheckedIdCard = ''; // ยังไม่ครบ 13 หลัก → reset ให้เช็คใหม่ได้เมื่อพิมพ์ครบอีกครั้ง
            return;
        }
        if (digits === __lastCheckedIdCard) return; // เพิ่งเช็คเลขนี้ไป (keyup ยิงถี่) ไม่ยิงซ้ำ
        __lastCheckedIdCard = digits;
        fetch('<?= base_url('player/check-id-card') ?>?id_card=' + digits + '&exclude_id=' + PLAYER_EDIT_ID, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) { return r.json(); })
          .then(function (res) {
              if (res && res.exists && res.player) {
                  promptAddPlayerFromSystem(res.player.id, res.player.name);
              }
          }).catch(function () {});
    }
    $(document).on('input change keyup', '#id_card', function () {
        checkIdCardDuplicate($(this).val());
    });
    // paste: อ่านค่าหลัง mask ประมวลผลเสร็จ
    $(document).on('paste', '#id_card', function () {
        const el = this;
        setTimeout(function () { checkIdCardDuplicate($(el).val()); }, 50);
    });

    // --- Multi-Team Management ---
    <?php if (!empty($player['id'])): ?>
    const currentPlayerId = <?= $player['id'] ?>;

    function searchTeams() {
        const q = document.getElementById('searchTeamInput').value.trim();
        if (q.length < 2) {
            Swal.fire({ icon: 'warning', title: 'กรุณากรอกชื่อทีมอย่างน้อย 2 ตัวอักษร', timer: 1500, showConfirmButton: false });
            return;
        }
        const resultsDiv = document.getElementById('searchTeamResults');
        resultsDiv.innerHTML = '<div class="text-center py-3"><i class="fas fa-spinner fa-spin"></i> กำลังค้นหา...</div>';

        fetch('<?= base_url('team/api/search') ?>?keyword=' + encodeURIComponent(q))
        .then(r => r.json())
        .then(teams => {
            if (Array.isArray(teams) && teams.length > 0) {
                let html = '<div class="list-group">';
                teams.forEach(team => {
                    html += `<div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <span class="fw-semibold">${team.name}</span>
                        </div>
                        <button class="btn btn-success btn-sm" onclick="addToTeam(${team.id}, '${team.name.replace(/'/g, "\\'")}')">
                            <i class="fas fa-plus me-1"></i>เพิ่ม
                        </button>
                    </div>`;
                });
                html += '</div>';
                resultsDiv.innerHTML = html;
            } else {
                resultsDiv.innerHTML = '<div class="alert alert-warning">ไม่พบทีมที่ค้นหา</div>';
            }
        })
        .catch(() => {
            resultsDiv.innerHTML = '<div class="alert alert-danger">เกิดข้อผิดพลาดในการค้นหา</div>';
        });
    }

    function addToTeam(teamId, teamName) {
        fetch('<?= base_url('player/add-player-to-team') ?>', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'player_id=' + currentPlayerId + '&team_id=' + teamId
        })
        .then(r => r.json())
        .then(res => {
            if (res.status === 'success') {
                Swal.fire({ icon: 'success', title: 'เพิ่มทีมสำเร็จ', timer: 1500, showConfirmButton: false })
                    .then(() => location.reload());
            } else {
                Swal.fire({ icon: 'warning', title: res.message, timer: 2000, showConfirmButton: false });
            }
        });
    }

    function removePlayerFromTeam(playerId, teamId, teamName) {
        Swal.fire({
            title: 'ลบออกจากทีม?',
            text: 'ต้องการลบนักเตะออกจากทีม "' + teamName + '" หรือไม่?',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'ลบออก',
            cancelButtonText: 'ยกเลิก'
        }).then(result => {
            if (result.isConfirmed) {
                fetch('<?= base_url('player/remove-player-from-team') ?>', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'player_id=' + playerId + '&team_id=' + teamId
                })
                .then(r => r.json())
                .then(res => {
                    if (res.status === 'success') {
                        document.getElementById('pt-row-' + teamId)?.remove();
                        Swal.fire({ icon: 'success', title: 'ลบออกจากทีมแล้ว', timer: 1500, showConfirmButton: false });
                    } else {
                        Swal.fire({ icon: 'error', title: res.message });
                    }
                });
            }
        });
    }

    // Enter key ใน search input
    document.getElementById('searchTeamInput')?.addEventListener('keyup', function(e) {
        if (e.key === 'Enter') searchTeams();
    });
    <?php endif; ?>

    // --- Function Definitions ---

    function checkMaskIDCard() {
        let value = $('#nation').val();
        if (value == 'THAI') {
            $('#id_card').mask('0-0000-00000-00-0');
        } else {
            $('#id_card').unmask();
        }
    }

    function calculateBMI() {
        const height = parseFloat($('#height').val());
        const weight = parseFloat($('#weight').val());

        if (height > 0 && weight > 0) {
            const heightInMeters = height / 100;
            const bmi = weight / (heightInMeters * heightInMeters);
            $('#bmi').val(bmi.toFixed(2));
        } else {
            $('#bmi').val('');
        }
    }

    function checkShirtNumberRequired(positionIds) {
        if (!Array.isArray(positionIds) || positionIds.length === 0) {
            $("#label-required-shirt").addClass("text-danger").text("*");
            $("#shirt_number").prop("required", true);
            return;
        }
        const optionalPositions = ['5', '6', '7'];
        const isShirtOptional = positionIds.every(id => optionalPositions.includes(id));

        if (isShirtOptional) {
            $("#label-required-shirt").removeClass("text-danger").text("");
            $("#shirt_number").prop("required", false);
        } else {
            $("#label-required-shirt").addClass("text-danger").text("*");
            $("#shirt_number").prop("required", true);
        }
    }

    /**
     * Toggles the address fields based on the selected nation.
     * If nation is 'OTHER', disables address fields.
     * If nation is 'THAI', enables them.
     * Note: Address fields are not required (optional).
     */
    function toggleAddressFields() {
        const nation = $('#nation').val();
        const provinceSelect = $('#province');
        const amphureSelect = $('#amphure');
        const tambonSelect = $('#tambon');
        const addressFields = [provinceSelect, amphureSelect, tambonSelect];
        const fieldset = provinceSelect.closest('fieldset');

        if (nation === 'OTHER') {
            // Disable and clear value
            addressFields.forEach(field => {
                field.prop('disabled', true).val('');
            });
            // Reset dependent dropdowns to their initial state
            amphureSelect.html('<option value="">' + langSelectDistrict + '</option>');
            tambonSelect.html('<option value="">' + langSelectSubdistrict + '</option>');
            fieldset.css('opacity', '0.65');
        } else { // nation === 'THAI'
            // Enable province (amphure/tambon will be enabled later by their own logic)
            provinceSelect.prop('disabled', false);
            fieldset.css('opacity', '1');
        }
    }


    // --- Main Logic on Document Ready ---
    $(document).ready(function () {
        // --- Event Handlers ---
        $('#nation').on('change', function () {
            checkMaskIDCard();
            $('#id_card').val('');
            toggleAddressFields();
        });

        $('#height, #weight').on('input', calculateBMI);

        $("#player_sub_position_id").on('change', function () {
            checkShirtNumberRequired($(this).val());
        });

        // --- Address Dropdown Logic ---
        $('#province').on('change', function () {
            const provinceId = $(this).val();
            const amphureSelect = $('#amphure');
            const tambonSelect = $('#tambon');

            amphureSelect.html('<option value="">' + langSelectDistrict + '</option>').prop('disabled', true);
            tambonSelect.html('<option value="">' + langSelectSubdistrict + '</option>').prop('disabled', true);

            if (provinceId) {
                amphureSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
                $.get('<?= site_url('amphures') ?>/' + provinceId,
                    function (districts) {
                        amphureSelect.empty().append('<option value="">' + langSelectDistrict + '</option>');
                        districts.forEach(district => {
                            amphureSelect.append(
                                `<option value="${district.district_id}">${district.district_name}</option>`
                            );
                        });

                        // For edit form: if an old amphure exists, select it
                        const oldAmphureId = "<?= $player['amphure_id'] ?? '' ?>";
                        if (oldAmphureId) {
                            amphureSelect.val(oldAmphureId).trigger('change');
                        } else {
                            amphureSelect.trigger('change.select2');
                        }
                    }).fail(function () {
                    amphureSelect.html('<option value="">' + langLoadFailed + '</option>');
                });
            }
        });

        $('#amphure').on('change', function () {
            const amphureId = $(this).val();
            const tambonSelect = $('#tambon');

            tambonSelect.empty().append('<option value="">' + langSelectSubdistrict + '</option>').prop('disabled', true).trigger('change.select2');

            if (amphureId) {
                tambonSelect.prop('disabled', false).html('<option value="">' + langLoading + '</option>');
                $.get('<?= site_url('tambons') ?>/' + amphureId,
                    function (subdistricts) {
                        tambonSelect.empty().append('<option value="">' + langSelectSubdistrict + '</option>');
                        subdistricts.forEach(subdistrict => {
                            tambonSelect.append(
                                `<option value="${subdistrict.subdistrict_id}">${subdistrict.subdistrict_name}</option>`
                            );
                        });

                        // For edit form: if an old tambon exists, select it
                        const oldTambonId = "<?= $player['tambon_id'] ?? '' ?>";
                        if (oldTambonId) {
                            tambonSelect.val(oldTambonId);
                        }
                        tambonSelect.trigger('change.select2'); // Notify Select2 of changes
                    }).fail(function () {
                    tambonSelect.html('<option value="">' + langLoadFailed + '</option>');
                });
            }
        });

        // --- Initial Calls on Page Load ---
        checkMaskIDCard();
        calculateBMI();
        checkShirtNumberRequired($("#player_sub_position_id").val());
        toggleAddressFields(); // Set initial state for address fields

        // If a province is pre-selected (on edit form), trigger change to load amphures
        if ($('#province').val() && $('#nation').val() === 'THAI') {
            $('#province').trigger('change');
        }

        // --- Cropper.js Logic ---
        const uploadImage = document.getElementById("uploadImage");
        const cropImage = document.getElementById("cropImage");
        const cropButton = document.getElementById("cropButton");
        const preview = document.getElementById("preview");
        let cropper;

        uploadImage.addEventListener("change", (event) => {
            const file = event.target.files[0];
            if (!file) return;

            const reader = new FileReader();
            reader.onload = (e) => {
                cropImage.src = e.target.result;
                cropImage.style.display = "block";
                if (cropper) {
                    cropper.destroy();
                }
                cropper = new Cropper(cropImage, {
                    aspectRatio: 1,
                    viewMode: 1,
                    autoCropArea: 1,
                    preview: "#preview",
                });
            };
            reader.readAsDataURL(file);
        });

        cropButton.addEventListener("click", () => {
            if (!cropper) return;

            const canvas = cropper.getCroppedCanvas({
                width: 350,
                height: 350,
            });
            const croppedImageURL = canvas.toDataURL("image/png");
            $('#image_personel').val(croppedImageURL);
            $('#personel_image_display').attr('src', croppedImageURL).css({
                'width': '175px',
                'height': '175px',
                'object-fit': 'cover',
                'border': '4px solid #dee2e6'
            });

            const modal = bootstrap.Modal.getInstance(document.getElementById("cropperModal"));
            modal.hide();
        });
    });

    // เลขบัตรซ้ำในระบบตอน submit (fallback) → เด้งถามด้วยฟังก์ชันเดียวกับ real-time
    <?php $__dup = session()->getFlashdata('duplicate_player'); if (!empty($__dup)): ?>
    promptAddPlayerFromSystem(<?= (int)$__dup['id'] ?>, '<?= esc($__dup['name'], 'js') ?>');
    <?php endif; ?>
</script>
<?php $this->endSection(); ?>
