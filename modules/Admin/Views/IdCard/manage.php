<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<style>
    #preview-container {
        position: sticky;
        top: 20px;
    }
    #preview-card {
        position: relative;
        overflow: hidden;
        background: #e9ecef;
        border: 2px solid #dee2e6;
        border-radius: 8px;
        margin: 0 auto;
        user-select: none;
    }
    #preview-card img.preview-bg {
        width: 100%;
        height: 100%;
        object-fit: fill;
        display: block;
        pointer-events: none;
    }
    #preview-card .pv-draggable {
        position: absolute;
        cursor: move;
        transition: box-shadow 0.15s;
    }
    #preview-card .pv-draggable:hover,
    #preview-card .pv-draggable.dragging {
        box-shadow: 0 0 0 2px #F3B71B, 0 4px 12px rgba(0,0,0,0.3);
        z-index: 100 !important;
    }
    #preview-card .pv-photo {
        background: rgba(255,255,255,0.7);
        border: 2px dashed #F3B71B;
        display: flex;
        align-items: center;
        justify-content: center;
        overflow: hidden;
    }
    #preview-card .pv-photo i {
        font-size: 24px;
        color: #999;
        pointer-events: none;
    }
    #preview-card .resize-handle {
        position: absolute;
        right: -4px;
        bottom: -4px;
        width: 14px;
        height: 14px;
        background: #F3B71B;
        border: 2px solid #fff;
        border-radius: 2px;
        cursor: nwse-resize;
        z-index: 10;
    }
    #preview-card .font-resize-handle {
        position: absolute;
        right: -4px;
        bottom: -4px;
        width: 14px;
        height: 14px;
        background: #3498db;
        border: 2px solid #fff;
        border-radius: 50%;
        cursor: ns-resize;
        z-index: 10;
    }
    #preview-card .pv-text {
        white-space: nowrap;
        font-weight: bold;
        text-shadow: 0 1px 3px rgba(0,0,0,0.3);
        padding: 2px 6px;
        border-radius: 3px;
    }
    #preview-card .pv-text:hover,
    #preview-card .pv-text.dragging {
        background: rgba(0,0,0,0.15);
    }
    .legend-list {
        list-style: none;
        padding: 0;
        margin: 10px 0 0;
        font-size: 13px;
    }
    .legend-list li {
        display: inline-block;
        margin-right: 14px;
        margin-bottom: 4px;
    }
    .legend-dot {
        display: inline-block;
        width: 10px;
        height: 10px;
        border-radius: 2px;
        margin-right: 4px;
        vertical-align: middle;
    }
    .pv-label {
        position: absolute;
        top: -18px;
        left: 0;
        font-size: 10px;
        font-weight: bold;
        padding: 1px 5px;
        border-radius: 3px 3px 0 0;
        color: #fff;
        white-space: nowrap;
        pointer-events: none;
        display: none;
    }
    .pv-draggable:hover .pv-label,
    .pv-draggable.dragging .pv-label {
        display: block;
    }
</style>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <!-- Header Section -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-id-card text-white fs-4"></i>
                </div>
                <div>
                    <?php $currentCardType = $data['card_type'] ?? $card_type ?? 'player'; ?>
                    <?php $cardTypeLabel = ($currentCardType === 'manager') ? 'Manager' : 'Player'; ?>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">
                        <?php if (!empty($data['id'])): ?>
                            แก้ไข <?= $cardTypeLabel ?> Card Template
                        <?php else: ?>
                            เพิ่ม <?= $cardTypeLabel ?> Card Template
                        <?php endif; ?>
                    </h4>
                    <p class="text-muted mb-0 small">กำหนดเทมเพลตและตำแหน่ง overlay ข้อมูล<?= ($currentCardType === 'manager') ? 'ผู้จัดการทีม' : 'นักเตะ' ?></p>
                </div>
            </div>
            <a href="<?= base_url('admin/idcard') ?>" class="btn btn-light rounded-pill px-4 py-2 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i> <?= lang('Common.back') ?>
            </a>
        </div>

        <!-- Alert Messages -->
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert" style="border-radius: var(--border-radius);">
                <strong><i class="fas fa-exclamation-circle me-1"></i></strong>
                <?= session()->getFlashdata('error'); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Form -->
        <form action="<?= base_url('admin/idcard/save') ?>" method="POST" enctype="multipart/form-data" class="once-only">
            <?= csrf_field() ?>
            <input type="hidden" name="id" value="<?= (!empty($data['id'])) ? $data['id'] : '' ?>">
            <input type="hidden" name="card_type" value="<?= $data['card_type'] ?? $card_type ?? 'player' ?>">

            <div class="row">
                <!-- LEFT: Form Fields -->
                <div class="col-lg-7">

                    <!-- Tournament Selection -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-trophy me-2" style="color: var(--secondary-color);"></i>
                        เลือกทัวร์นาเมนต์
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="tour_id" class="form-label fw-bold text-muted">
                                ทัวร์นาเมนต์ <span class="text-danger">*</span>
                            </label>
                            <?php if (!empty($data['id'])){ ?>
                                <input type="hidden" name="tour_id" value="<?= $data['tour_id'] ?>">
                            <!--
                                <input type="text" class="form-control" value="<?= esc($data['tournament_name'] ?? '') ?>" readonly style="border-radius: 8px; padding: 10px 15px; background: #f8f9fa;">
                            -->
                                <select name="IdCardsAll" id="IdCardsAll" onchange="window.location.replace('<?php echo base_url('')?>'+'/admin/idcard/manage/'+$(this).val())" class="form-select" required style="border-radius: 8px; padding: 10px 15px; width: 100%;">
                                    <option value="">-- เลือกทัวร์นาเมนต์ --</option>
                                    <?php if (!empty($IdCardsAll)){ ?>
                                        <?php foreach ($IdCardsAll as $t){ ?>
                                            <option  <?php if($t['id']==$data['id']){ echo 'selected'; } ?> value="<?= $t['id'] ?>"><?= esc($t['tournament_name']) ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            <?php }else{ ?>
                                <select name="tour_id" id="tour_id" class="form-select select2" required style="border-radius: 8px; padding: 10px 15px; width: 100%;">
                                    <option value="">-- เลือกทัวร์นาเมนต์ --</option>
                                    <?php if (!empty($tournaments)){ ?>
                                        <?php foreach ($tournaments as $t){ ?>
                                            <option value="<?= $t['tour_id'] ?>"><?= esc($t['name']) ?></option>
                                        <?php } ?>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                        </div>

                        <div class="col-md-6">
                            <label for="is_active" class="form-label fw-bold text-muted">
                                <?= lang('Common.status') ?>
                            </label>
                            <select name="is_active" id="is_active" class="form-select" style="border-radius: 8px; padding: 10px 15px;">
                                <option value="1" <?= (isset($data['is_active']) && $data['is_active'] == 1) ? 'selected' : '' ?>><?= lang('Common.active') ?></option>
                                <option value="0" <?= (isset($data['is_active']) && $data['is_active'] == 0) ? 'selected' : '' ?>><?= lang('Common.inactive') ?></option>
                            </select>
                        </div>
                    </div>

                    <!-- Template Image Upload -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-image me-2" style="color: var(--secondary-color);"></i>
                        รูปเทมเพลต ID Card
                    </h6>

                    <div class="mb-4">
                        <label class="form-label fw-bold text-muted">
                            <?= (!empty($data['template_image'])) ? 'เปลี่ยนรูปเทมเพลต' : 'อัปโหลดรูปเทมเพลต' ?> <?= empty($data['id']) ? '<span class="text-danger">*</span>' : '' ?>
                        </label>
                        <input type="file" name="template_image_file" id="template_image_file" accept="image/*" class="form-control" <?= empty($data['id']) ? 'required' : '' ?> style="border-radius: 8px; padding: 8px 15px;">
                        <div class="form-text small text-muted mt-1">
                            <i class="fas fa-info-circle me-1"></i>
                            แนะนำขนาดรูป 1063x1535px (90x130mm ที่ 300 DPI) รองรับ JPG, PNG
                        </div>
                    </div>

                    <!-- Card Dimensions -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-ruler-combined me-2" style="color: var(--secondary-color);"></i>
                        ขนาดการ์ด (มิลลิเมตร)
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">ความกว้าง (mm)</label>
                            <input type="number" step="0.01" name="card_width_mm" class="form-control pv-input"
                                   value="<?= $data['card_width_mm'] ?? '90.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-muted">ความสูง (mm)</label>
                            <input type="number" step="0.01" name="card_height_mm" class="form-control pv-input"
                                   value="<?= $data['card_height_mm'] ?? '130.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                    </div>

                    <!-- Photo Position -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-user-circle me-2" style="color: var(--secondary-color);"></i>
                        ตำแหน่งรูปนักเตะ
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">X (mm)</label>
                            <input type="number" step="0.01" name="photo_x" class="form-control pv-input"
                                   value="<?= $data['photo_x'] ?? '10.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">Y (mm)</label>
                            <input type="number" step="0.01" name="photo_y" class="form-control pv-input"
                                   value="<?= $data['photo_y'] ?? '30.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">กว้าง (mm)</label>
                            <input type="number" step="0.01" name="photo_width" class="form-control pv-input"
                                   value="<?= $data['photo_width'] ?? '25.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">สูง (mm)</label>
                            <input type="number" step="0.01" name="photo_height" class="form-control pv-input"
                                   value="<?= $data['photo_height'] ?? '30.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                    </div>

                    <!-- Name Position -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-font me-2" style="color: var(--secondary-color);"></i>
                        ตำแหน่งชื่อนักเตะ
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">X (mm)</label>
                            <input type="number" step="0.01" name="name_x" class="form-control pv-input"
                                   value="<?= $data['name_x'] ?? '10.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">Y (mm)</label>
                            <input type="number" step="0.01" name="name_y" class="form-control pv-input"
                                   value="<?= $data['name_y'] ?? '100.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">ขนาดตัวอักษร (pt)</label>
                            <input type="number" name="name_font_size" class="form-control pv-input"
                                   value="<?= $data['name_font_size'] ?? '16' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">สีตัวอักษร</label>
                            <input type="color" name="name_color" class="form-control form-control-color w-100 pv-input"
                                   value="<?= $data['name_color'] ?? '#000000' ?>"
                                   style="border-radius: 8px; height: 46px;">
                        </div>
                    </div>

                    <!-- Team Position -->
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-users me-2" style="color: var(--secondary-color);"></i>
                        ตำแหน่งชื่อทีม
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">X (mm)</label>
                            <input type="number" step="0.01" name="team_x" class="form-control pv-input"
                                   value="<?= $data['team_x'] ?? '10.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">Y (mm)</label>
                            <input type="number" step="0.01" name="team_y" class="form-control pv-input"
                                   value="<?= $data['team_y'] ?? '110.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">ขนาดตัวอักษร (pt)</label>
                            <input type="number" name="team_font_size" class="form-control pv-input"
                                   value="<?= $data['team_font_size'] ?? '14' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">สีตัวอักษร</label>
                            <input type="color" name="team_color" class="form-control form-control-color w-100 pv-input"
                                   value="<?= $data['team_color'] ?? '#000000' ?>"
                                   style="border-radius: 8px; height: 46px;">
                        </div>
                    </div>

                    <!-- Number Position (Player only) -->
                    <?php if ($currentCardType !== 'manager'): ?>
                    <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                        <i class="fas fa-hashtag me-2" style="color: var(--secondary-color);"></i>
                        ตำแหน่งเบอร์เสื้อ
                    </h6>

                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">X (mm)</label>
                            <input type="number" step="0.01" name="number_x" class="form-control pv-input"
                                   value="<?= $data['number_x'] ?? '10.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">Y (mm)</label>
                            <input type="number" step="0.01" name="number_y" class="form-control pv-input"
                                   value="<?= $data['number_y'] ?? '20.00' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">ขนาดตัวอักษร (pt)</label>
                            <input type="number" name="number_font_size" class="form-control pv-input"
                                   value="<?= $data['number_font_size'] ?? '24' ?>"
                                   style="border-radius: 8px; padding: 10px 15px;">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold text-muted">สีตัวอักษร</label>
                            <input type="color" name="number_color" class="form-control form-control-color w-100 pv-input"
                                   value="<?= $data['number_color'] ?? '#FFFFFF' ?>"
                                   style="border-radius: 8px; height: 46px;">
                        </div>
                    </div>
                    <?php endif; ?>

                    <!-- Submit Buttons -->
                    <div class="row mt-5 mb-3">
                        <div class="col-md-12 text-center">
                            <a href="<?= base_url('admin/idcard') ?>" class="btn btn-light rounded-pill px-5 py-2 fw-bold text-muted border me-2">
                                <i class="fas fa-times me-2"></i> <?= lang('Common.cancel') ?>
                            </a>
                            <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold btn-submit shadow-sm" style="background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow);">
                                <i class="fas fa-save me-2"></i> <?= lang('Common.save_data') ?>
                            </button>
                            <?php if (!empty($data['id'])): ?>
                                <a href="<?= base_url('admin/idcard/preview/' . $data['id']) ?>" target="_blank" class="btn btn-info rounded-pill px-5 py-2 fw-bold ms-2 text-white">
                                    <i class="fas fa-eye me-2"></i> Preview PDF
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>

                <!-- RIGHT: Live Preview -->
                <div class="col-lg-5">
                    <div id="preview-container">
                        <h6 class="fw-bold text-muted text-uppercase mb-3" style="letter-spacing: 0.5px;">
                            <i class="fas fa-eye me-2" style="color: var(--secondary-color);"></i>
                            Live Preview
                        </h6>
                        <div class="card border-0 shadow-sm p-3" style="border-radius: var(--border-radius); background: #f8f9fa;">
                            <div id="preview-placeholder" class="text-center py-5 text-muted" <?= !empty($data['template_image']) ? 'style="display:none"' : '' ?>>
                                <i class="fas fa-cloud-upload-alt fa-3x mb-3 d-block" style="color: #ccc;"></i>
                                <p class="mb-0">อัปโหลดรูปเทมเพลตเพื่อดู Preview</p>
                            </div>
                            <div id="preview-card" <?= empty($data['template_image']) ? 'style="display:none"' : '' ?>>
                                <img class="preview-bg" id="preview-bg-img"
                                     src="<?= !empty($data['template_image']) ? base_url($data['template_image']) : '' ?>"
                                     alt="Template">
                                <!-- Photo overlay (draggable + resizable) -->
                                <div class="pv-photo pv-draggable" id="pv-photo"
                                     data-x="photo_x" data-y="photo_y" data-w="photo_width" data-h="photo_height">
                                    <span class="pv-label" style="background:#F3B71B;">รูปนักเตะ</span>
                                    <i class="fas fa-user"></i>
                                    <div class="resize-handle" id="pv-photo-resize"></div>
                                </div>
                                <!-- Number overlay (draggable + font resizable) - Player only -->
                                <?php if ($currentCardType !== 'manager'): ?>
                                <div class="pv-text pv-draggable" id="pv-number"
                                     data-x="number_x" data-y="number_y" data-fs="number_font_size">
                                    <span class="pv-label" style="background:#e74c3c;">เบอร์เสื้อ</span>
                                    <span class="pv-text-content">99</span>
                                    <div class="font-resize-handle"></div>
                                </div>
                                <?php endif; ?>
                                <!-- Name overlay (draggable + font resizable) -->
                                <div class="pv-text pv-draggable" id="pv-name"
                                     data-x="name_x" data-y="name_y" data-fs="name_font_size">
                                    <span class="pv-label" style="background:#3498db;">ชื่อ</span>
                                    <span class="pv-text-content">ชื่อ นามสกุล</span>
                                    <div class="font-resize-handle"></div>
                                </div>
                                <!-- Team overlay (draggable + font resizable) -->
                                <div class="pv-text pv-draggable" id="pv-team"
                                     data-x="team_x" data-y="team_y" data-fs="team_font_size">
                                    <span class="pv-label" style="background:#2ecc71;">ทีม</span>
                                    <span class="pv-text-content">ชื่อทีม</span>
                                    <div class="font-resize-handle"></div>
                                </div>
                            </div>
                            <ul class="legend-list">
                                <li><span class="legend-dot" style="background: #F3B71B; border: 1px solid #ccc;"></span> รูปนักเตะ</li>
                                <li><span class="legend-dot" style="background: #e74c3c;"></span> เบอร์เสื้อ</li>
                                <li><span class="legend-dot" style="background: #3498db;"></span> ชื่อนักเตะ</li>
                                <li><span class="legend-dot" style="background: #2ecc71;"></span> ชื่อทีม</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
<script>
    // Preview scale: convert mm to px relative to preview container width
    const PREVIEW_WIDTH = 350;
    let bgLoaded = <?= !empty($data['template_image']) ? 'true' : 'false' ?>;
    let currentScale = 1;

    function getVal(name) {
        const el = document.querySelector('[name="' + name + '"]');
        return el ? parseFloat(el.value) || 0 : 0;
    }
    function setVal(name, val) {
        const el = document.querySelector('[name="' + name + '"]');
        if (el) el.value = parseFloat(val).toFixed(2);
    }
    function getColor(name) {
        const el = document.querySelector('[name="' + name + '"]');
        return el ? el.value : '#000000';
    }

    function updatePreview() {
        if (!bgLoaded) return;

        const cardW = getVal('card_width_mm') || 90;
        const cardH = getVal('card_height_mm') || 130;
        currentScale = PREVIEW_WIDTH / cardW;
        const previewH = cardH * currentScale;

        const card = document.getElementById('preview-card');
        card.style.width = PREVIEW_WIDTH + 'px';
        card.style.height = previewH + 'px';
        card.style.display = 'block';
        document.getElementById('preview-placeholder').style.display = 'none';

        // Photo
        const pvPhoto = document.getElementById('pv-photo');
        pvPhoto.style.left = (getVal('photo_x') * currentScale) + 'px';
        pvPhoto.style.top = (getVal('photo_y') * currentScale) + 'px';
        pvPhoto.style.width = (getVal('photo_width') * currentScale) + 'px';
        pvPhoto.style.height = (getVal('photo_height') * currentScale) + 'px';

        // Number (player only)
        const pvNumber = document.getElementById('pv-number');
        if (pvNumber) {
            pvNumber.style.left = (getVal('number_x') * currentScale) + 'px';
            pvNumber.style.top = (getVal('number_y') * currentScale) + 'px';
            pvNumber.style.fontSize = (getVal('number_font_size') * currentScale * 0.35) + 'px';
            pvNumber.style.color = getColor('number_color');
        }

        // Name
        const pvName = document.getElementById('pv-name');
        pvName.style.left = (getVal('name_x') * currentScale) + 'px';
        pvName.style.top = (getVal('name_y') * currentScale) + 'px';
        pvName.style.fontSize = (getVal('name_font_size') * currentScale * 0.35) + 'px';
        pvName.style.color = getColor('name_color');

        // Team
        const pvTeam = document.getElementById('pv-team');
        pvTeam.style.left = (getVal('team_x') * currentScale) + 'px';
        pvTeam.style.top = (getVal('team_y') * currentScale) + 'px';
        pvTeam.style.fontSize = (getVal('team_font_size') * currentScale * 0.35) + 'px';
        pvTeam.style.color = getColor('team_color');
    }

    // ===================== DRAG, RESIZE & FONT RESIZE =====================
    (function() {
        let dragEl = null, dragType = null;
        let startMouseX, startMouseY, startElLeft, startElTop, startElW, startElH, startFontSize;

        function onMouseDown(e) {
            e.preventDefault();
            e.stopPropagation();

            const isResize = e.target.classList.contains('resize-handle');
            const isFontResize = e.target.classList.contains('font-resize-handle');
            const el = e.target.closest('.pv-draggable');
            if (!el) return;

            dragEl = el;
            dragType = isFontResize ? 'font-resize' : (isResize ? 'resize' : 'move');
            dragEl.classList.add('dragging');

            const rect = dragEl.getBoundingClientRect();
            startMouseX = e.clientX;
            startMouseY = e.clientY;
            startElLeft = parseFloat(dragEl.style.left) || 0;
            startElTop = parseFloat(dragEl.style.top) || 0;
            startElW = rect.width;
            startElH = rect.height;
            startFontSize = parseFloat(dragEl.style.fontSize) || 14;

            document.addEventListener('mousemove', onMouseMove);
            document.addEventListener('mouseup', onMouseUp);
        }

        function onMouseMove(e) {
            if (!dragEl) return;
            e.preventDefault();

            const dx = e.clientX - startMouseX;
            const dy = e.clientY - startMouseY;

            if (dragType === 'move') {
                const newLeft = Math.max(0, startElLeft + dx);
                const newTop = Math.max(0, startElTop + dy);
                dragEl.style.left = newLeft + 'px';
                dragEl.style.top = newTop + 'px';

                const xField = dragEl.dataset.x;
                const yField = dragEl.dataset.y;
                if (xField) setVal(xField, newLeft / currentScale);
                if (yField) setVal(yField, newTop / currentScale);

            } else if (dragType === 'resize') {
                const newW = Math.max(20, startElW + dx);
                const newH = Math.max(20, startElH + dy);
                dragEl.style.width = newW + 'px';
                dragEl.style.height = newH + 'px';

                const wField = dragEl.dataset.w;
                const hField = dragEl.dataset.h;
                if (wField) setVal(wField, newW / currentScale);
                if (hField) setVal(hField, newH / currentScale);

            } else if (dragType === 'font-resize') {
                // Drag down = bigger, drag up = smaller
                const newFontPx = Math.max(6, startFontSize - dy * 0.3);
                dragEl.style.fontSize = newFontPx + 'px';

                // Convert preview px font size back to pt
                const fsField = dragEl.dataset.fs;
                if (fsField) {
                    const ptVal = newFontPx / (currentScale * 0.35);
                    setVal(fsField, Math.round(ptVal));
                }
            }
        }

        function onMouseUp(e) {
            if (dragEl) {
                dragEl.classList.remove('dragging');
                dragEl = null;
                dragType = null;
            }
            document.removeEventListener('mousemove', onMouseMove);
            document.removeEventListener('mouseup', onMouseUp);
        }

        // Attach to preview card
        document.getElementById('preview-card').addEventListener('mousedown', onMouseDown);

        // Scroll wheel on text elements to adjust font size
        document.getElementById('preview-card').addEventListener('wheel', function(e) {
            const el = e.target.closest('.pv-text.pv-draggable');
            if (!el || !el.dataset.fs) return;
            e.preventDefault();

            const fsField = el.dataset.fs;
            const currentPt = getVal(fsField);
            const delta = e.deltaY > 0 ? -1 : 1;
            const newPt = Math.max(6, Math.min(72, currentPt + delta));
            setVal(fsField, newPt);
            updatePreview();
        }, { passive: false });
    })();

    // ===================== FILE UPLOAD PREVIEW =====================
    document.getElementById('template_image_file').addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            document.getElementById('preview-bg-img').src = ev.target.result;
            bgLoaded = true;
            updatePreview();
        };
        reader.readAsDataURL(file);
    });

    // ===================== INPUT LISTENERS =====================
    document.querySelectorAll('.pv-input').forEach(function(el) {
        el.addEventListener('input', updatePreview);
        el.addEventListener('change', updatePreview);
    });

    // Initial render
    $(document).ready(function() {
        updatePreview();
    });
</script>
<?php $this->endSection() ?>
