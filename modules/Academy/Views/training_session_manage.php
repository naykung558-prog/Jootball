<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-primary text-white py-3 px-4">
            <div class="d-flex align-items-center justify-content-between">
                <div>
                    <h5 class="mb-0 text-white fw-bold">
                        <i class="fas fa-clipboard-list me-2"></i>
                        <?= !empty($session) ? lang('Common.training_session_edit') : lang('Common.training_session_create') ?>
                    </h5>
                    <?php if (!empty($academy['name'])): ?>
                        <small class="opacity-75"><?= esc($academy['name']) ?></small>
                    <?php endif; ?>
                </div>
                <a href="<?= base_url('academy/training-sessions/' . esc($academy['id'])) ?>"
                   class="btn btn-light btn-sm rounded-pill px-3">
                    <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                </a>
            </div>
        </div>

        <div class="card-body p-4">
            <form action="<?= base_url('academy/training-session/save') ?>" method="POST" id="sessionForm" autocomplete="off" enctype="multipart/form-data">
                <input type="hidden" name="academy_id" value="<?= esc($academy['id'] ?? '') ?>">
                <?php if (!empty($session['id'])): ?>
                    <input type="hidden" name="id" value="<?= esc($session['id']) ?>">
                <?php endif; ?>

                <!-- Section 1: กลุ่ม + วันที่ + เวลา + ประเภท -->
                <div class="row g-3 mb-2">
                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><?= lang('Common.training_select_group') ?> <span class="text-danger">*</span></label>
                        <select name="group_id" class="form-select" required>
                            <option value=""><?= lang('Common.training_select_group') ?></option>
                            <?php if (!empty($groups)): ?>
                                <?php foreach ($groups as $group): ?>
                                <option value="<?= esc($group['id']) ?>"
                                    <?= (($session['group_id'] ?? '') == $group['id']) ? 'selected' : '' ?>>
                                    <?= esc($group['name']) ?>
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label fw-semibold"><?= lang('Common.training_date') ?> <span class="text-danger">*</span></label>
                        <input type="date" name="training_date" class="form-control"
                               value="<?= esc($session['training_date'] ?? date('Y-m-d')) ?>" required>
                    </div>
                    <div class="col-4 col-md-2">
                        <label class="form-label fw-semibold"><?= lang('Common.training_start_time') ?> <span class="text-danger">*</span></label>
                        <input type="time" name="start_time" class="form-control"
                               value="<?= esc($session['start_time'] ?? '') ?>" required>
                    </div>
                    <div class="col-4 col-md-2">
                        <label class="form-label fw-semibold"><?= lang('Common.training_end_time') ?> <span class="text-danger">*</span></label>
                        <input type="time" name="end_time" class="form-control"
                               value="<?= esc($session['end_time'] ?? '') ?>" required>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label fw-semibold"><?= lang('Common.training_type') ?> <span class="text-danger">*</span></label>
                        <select name="training_type" class="form-select" required>
                            <option value="regular" <?= (($session['training_type'] ?? 'regular') === 'regular') ? 'selected' : '' ?>><?= lang('Common.training_type_regular') ?></option>
                            <option value="special" <?= (($session['training_type'] ?? '') === 'special') ? 'selected' : '' ?>><?= lang('Common.training_type_special') ?></option>
                            <option value="match"   <?= (($session['training_type'] ?? '') === 'match')   ? 'selected' : '' ?>><?= lang('Common.training_type_match') ?></option>
                            <option value="fitness" <?= (($session['training_type'] ?? '') === 'fitness') ? 'selected' : '' ?>><?= lang('Common.training_type_fitness') ?></option>
                        </select>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Section 2: หัวข้อการฝึก + รายละเอียด -->
                <?php
                    // parse selected titles (comma-separated)
                    $selectedTitles = !empty($session['title'])
                        ? array_map('trim', explode(',', $session['title']))
                        : [];
                ?>
                <div class="row g-3">
                    <div class="col-12">
                        <label class="form-label fw-semibold"><?= lang('Common.training_title') ?> <span class="text-danger">*</span></label>
                        <!-- hidden field ส่งค่าจริง -->
                        <input type="hidden" name="title" id="titleHidden" value="<?= esc($session['title'] ?? '') ?>" required>

                        <?php if (!empty($topics)): ?>
                        <div class="topic-pills d-flex flex-wrap gap-2 mt-1 mb-1" id="topicPills">
                            <?php foreach ($topics as $t): ?>
                            <?php $checked = in_array($t['name'], $selectedTitles); ?>
                            <button type="button"
                                    class="btn btn-sm rounded-pill topic-pill <?= $checked ? 'btn-primary' : 'btn-outline-secondary' ?>"
                                    data-name="<?= esc($t['name']) ?>"
                                    data-desc="<?= esc($t['description'] ?? '') ?>">
                                <?= esc($t['name']) ?>
                            </button>
                            <?php endforeach; ?>
                        </div>
                        <div class="d-flex align-items-center gap-2 mt-1">
                            <small class="text-muted" id="topicCount">
                                <?= count($selectedTitles) > 0 ? 'เลือก ' . count($selectedTitles) . ' หัวข้อ' : 'ยังไม่ได้เลือก' ?>
                            </small>
                            <span class="text-muted">·</span>
                            <a href="<?= base_url('academy/training-topics/' . esc($academy['id'])) ?>" class="text-muted small" target="_blank">
                                <i class="fas fa-external-link-alt me-1"></i>จัดการหัวข้อ
                            </a>
                        </div>
                        <?php else: ?>
                        <input type="text" name="title" class="form-control"
                               value="<?= esc($session['title'] ?? '') ?>" required>
                        <small class="text-muted">
                            ยังไม่มีหัวข้อการฝึก —
                            <a href="<?= base_url('academy/training-topics/' . esc($academy['id'])) ?>">เพิ่มหัวข้อการฝึก</a>
                        </small>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-12">
                        <label class="form-label fw-semibold"><?= lang('Common.training_description') ?></label>
                        <textarea name="description" id="descriptionArea" rows="2" class="form-control"
                                  placeholder="รายละเอียดจะถูกดึงมาอัตโนมัติเมื่อเลือกหัวข้อ..."><?= esc($session['description'] ?? '') ?></textarea>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><?= lang('Common.training_location') ?></label>
                        <input type="text" name="location" class="form-control"
                               value="<?= esc($session['location'] ?? '') ?>"
                               placeholder="สนามฝึก...">
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><?= lang('Common.training_status') ?> <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="planned"   <?= (($session['status'] ?? 'planned') === 'planned')   ? 'selected' : '' ?>><?= lang('Common.training_status_planned') ?></option>
                            <option value="completed" <?= (($session['status'] ?? '') === 'completed') ? 'selected' : '' ?>><?= lang('Common.training_status_completed') ?></option>
                            <option value="cancelled" <?= (($session['status'] ?? '') === 'cancelled') ? 'selected' : '' ?>><?= lang('Common.training_status_cancelled') ?></option>
                        </select>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label fw-semibold"><?= lang('Common.training_notes') ?></label>
                        <textarea name="notes" rows="3" class="form-control"
                                  placeholder="สรุปผล / หมายเหตุ..."><?= esc($session['notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <hr class="my-3">

                <!-- Section: Attachments -->
                <div class="mb-3">
                    <label class="form-label fw-semibold">
                        <i class="fas fa-paperclip me-1"></i><?= lang('Common.training_attach_section_title') ?>
                    </label>

                    <?php if (!empty($session['id'])): ?>
                        <div id="existingAttachments">
                            <?= $this->include('Modules\Academy\Views\_attachments_list', [
                                'attachments' => $attachments ?? [],
                                'mode'        => 'compact',
                                'canManage'   => true,
                            ]) ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?= $this->include('Modules\Academy\Views\_attachments_upload') ?>

                <!-- Action Buttons -->
                <div class="d-flex justify-content-center gap-2 mt-4 pt-2 border-top">
                    <a href="<?= base_url('academy/training-sessions/' . esc($academy['id'])) ?>"
                       class="btn btn-light rounded-pill px-4">
                        <i class="fas fa-arrow-left me-1"></i><?= lang('Common.back') ?>
                    </a>
                    <button type="button" class="btn btn-primary rounded-pill px-5" id="btnSubmitSession">
                        <i class="fas fa-save me-1"></i><?= lang('Common.save') ?>
                    </button>
                    <?php if (!empty($session['id'])): ?>
                    <button type="button" class="btn btn-outline-danger rounded-pill px-4" id="btnDeleteSession">
                        <i class="fas fa-trash me-1"></i><?= lang('Common.delete') ?>
                    </button>
                    <?php endif; ?>
                </div>
            </form>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    // Multi-select topic pills
    <?php if (!empty($topics)): ?>
    (function() {
        var titleHidden = document.getElementById('titleHidden');
        var descArea    = document.getElementById('descriptionArea');
        var topicCount  = document.getElementById('topicCount');

        function syncPills() {
            var selected = [];
            var descs    = [];
            document.querySelectorAll('.topic-pill').forEach(function(btn) {
                if (btn.classList.contains('btn-primary')) {
                    selected.push(btn.dataset.name);
                    if (btn.dataset.desc) descs.push(btn.dataset.desc);
                }
            });
            titleHidden.value = selected.join(', ');
            if (descs.length > 0) {
                descArea.value = descs.join('\n');
            }
            topicCount.textContent = selected.length > 0
                ? 'เลือก ' + selected.length + ' หัวข้อ'
                : 'ยังไม่ได้เลือก';
        }

        document.querySelectorAll('.topic-pill').forEach(function(btn) {
            btn.addEventListener('click', function() {
                this.classList.toggle('btn-primary');
                this.classList.toggle('btn-outline-secondary');
                syncPills();
            });
        });
    })();
    <?php endif; ?>

    // Submit with confirmation
    document.getElementById('btnSubmitSession').addEventListener('click', function() {
        var form = document.getElementById('sessionForm');
        <?php if (!empty($topics)): ?>
        // ตรวจสอบว่าเลือกหัวข้ออย่างน้อย 1 อัน
        var titleVal = document.getElementById('titleHidden').value;
        if (!titleVal.trim()) {
            Swal.fire({ icon: 'warning', title: 'กรุณาเลือกหัวข้อการฝึก', text: 'เลือกอย่างน้อย 1 หัวข้อ', confirmButtonColor: '#16478d' });
            return;
        }
        <?php endif; ?>
        if (!form.checkValidity()) {
            form.reportValidity();
            return;
        }
        Swal.fire({
            title: "<?= lang('Common.confirm_save_data') ?>",
            text: "<?= lang('Common.confirm_save_text') ?>",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#16478d",
            cancelButtonColor: "#d33",
            confirmButtonText: "<?= lang('Common.confirm') ?>",
            cancelButtonText: "<?= lang('Common.cancel') ?>"
        }).then((result) => {
            if (result.isConfirmed) {
                form.submit();
            }
        });
    });

    <?php if (!empty($session['id'])): ?>
    // Delete with confirmation
    document.getElementById('btnDeleteSession').addEventListener('click', function() {
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
    });
    <?php endif; ?>

    // ============ Attachments: dropzone + preview + delete ============
    (function() {
        const ALLOWED_EXT = ['jpg','jpeg','png','pdf','doc','docx','xls','xlsx'];
        const MAX_SIZE_MB = 20;
        const MAX_FILES = 10;

        const dropzone   = document.getElementById('attachDropzone');
        const fileInput  = document.getElementById('attachInput');
        const preview    = document.getElementById('attachPreview');
        const existingEl = document.getElementById('existingAttachments');

        if (!dropzone || !fileInput || !preview) return;

        let selectedFiles = [];

        function renderPreview() {
            preview.innerHTML = '';
            selectedFiles.forEach((f, idx) => {
                const li = document.createElement('li');
                li.className = 'list-group-item d-flex justify-content-between align-items-center';
                li.innerHTML = '<span class="small"><i class="fas fa-file me-1"></i>' +
                    f.name + ' <small class="text-muted">(' + (f.size/1024/1024).toFixed(2) + ' MB)</small></span>' +
                    '<button type="button" class="btn btn-sm btn-outline-danger"><i class="fas fa-times"></i></button>';
                li.querySelector('button').addEventListener('click', () => {
                    selectedFiles.splice(idx, 1);
                    syncInput();
                    renderPreview();
                });
                preview.appendChild(li);
            });
        }

        function syncInput() {
            const dt = new DataTransfer();
            selectedFiles.forEach(f => dt.items.add(f));
            fileInput.files = dt.files;
        }

        function existingCount() {
            return existingEl ? existingEl.querySelectorAll('.attach-row').length : 0;
        }

        function addFiles(files) {
            const arr = Array.from(files);
            for (const f of arr) {
                const ext = f.name.split('.').pop().toLowerCase();
                if (!ALLOWED_EXT.includes(ext)) {
                    Swal.fire({icon:'error', title: 'ประเภทไฟล์ไม่รองรับ: ' + f.name});
                    continue;
                }
                if (f.size > MAX_SIZE_MB * 1024 * 1024) {
                    Swal.fire({icon:'error', title: 'ไฟล์ ' + f.name + ' มีขนาดเกิน ' + MAX_SIZE_MB + ' MB'});
                    continue;
                }
                if (selectedFiles.length + existingCount() >= MAX_FILES) {
                    Swal.fire({icon:'warning', title: '<?= lang('Common.training_attach_error_count') ?>'});
                    break;
                }
                selectedFiles.push(f);
            }
            syncInput();
            renderPreview();
        }

        dropzone.addEventListener('click', () => fileInput.click());
        fileInput.addEventListener('change', e => addFiles(e.target.files));
        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('bg-light'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('bg-light'));
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            dropzone.classList.remove('bg-light');
            addFiles(e.dataTransfer.files);
        });

        // Delete existing attachments (AJAX)
        if (existingEl) {
            existingEl.addEventListener('click', function(e) {
                const btn = e.target.closest('.btn-delete-attach');
                if (!btn) return;
                e.preventDefault();
                const id = btn.dataset.id;
                Swal.fire({
                    title: '<?= lang('Common.training_attach_delete_confirm') ?>',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: '<?= lang('Common.confirm_delete_btn') ?>',
                    cancelButtonText: '<?= lang('Common.cancel') ?>'
                }).then(r => {
                    if (!r.isConfirmed) return;
                    fetch('<?= base_url('academy/training-session/attachment/delete') ?>', {
                        method: 'POST',
                        headers: {'Content-Type':'application/x-www-form-urlencoded'},
                        body: 'attachment_id=' + encodeURIComponent(id)
                    }).then(res => res.json()).then(data => {
                        if (data.status === 'success') {
                            btn.closest('.attach-row').remove();
                        } else {
                            Swal.fire({icon:'error', text: data.message});
                        }
                    }).catch(() => {
                        Swal.fire({icon:'error', text: '<?= lang('Common.connection_error') ?>'});
                    });
                });
            });
        }
    })();
</script>
<?php $this->endSection(); ?>
