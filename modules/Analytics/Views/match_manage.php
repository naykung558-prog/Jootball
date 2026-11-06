<?php
$baseUrl = rtrim(base_url(), "/") . "/";
$isEdit  = !empty($match);
$matchId = $isEdit ? $match['id'] : 0;
?>

<div class="d-flex align-items-center mb-4">
    <a href="<?= $baseUrl ?>admin/analytics/matches" class="btn btn-outline-secondary btn-sm mr-3">
        <i class="fas fa-arrow-left"></i>
    </a>
    <h4 class="text-gray-800 mb-0"><?= $isEdit ? 'แก้ไขแมตช์' : 'เพิ่มแมตช์ใหม่' ?></h4>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">ข้อมูลแมตช์</h6>
            </div>
            <div class="card-body">
                <form action="<?= $baseUrl ?>admin/analytics/matches/save" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <?php if ($isEdit): ?>
                        <input type="hidden" name="id" value="<?= $matchId ?>">
                    <?php endif; ?>

                    <div class="form-group">
                        <label class="font-weight-bold">ชื่อแมตช์ <span class="text-danger">*</span></label>
                        <input type="text" name="match_name" class="form-control"
                               value="<?= $isEdit ? esc($match['match_name']) : '' ?>" required
                               placeholder="เช่น นัดซ้อม ทีม A vs ทีม B">
                    </div>

                    <div class="form-row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">วันที่แข่ง <span class="text-danger">*</span></label>
                                <input type="date" name="match_date" class="form-control" required
                                       value="<?= $isEdit ? $match['match_date'] : date('Y-m-d') ?>">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label class="font-weight-bold">สนาม</label>
                                <input type="text" name="venue" class="form-control"
                                       value="<?= $isEdit ? esc($match['venue']) : '' ?>"
                                       placeholder="ชื่อสนาม">
                            </div>
                        </div>
                    </div>

                    <!-- ทีมเหย้า -->
                    <div class="form-row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">ทีมเหย้า</label>
                                <select name="home_team_id" class="form-control">
                                    <option value="">-- ไม่ระบุ --</option>
                                    <?php foreach ($teams as $t): ?>
                                        <option value="<?= $t['id'] ?>"
                                            <?= ($isEdit && $match['home_team_id'] == $t['id']) ? 'selected' : '' ?>>
                                            <?= esc($t['team_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">หรือพิมพ์ชื่อด้านล่าง</small>
                                <input type="text" name="home_team_name" class="form-control mt-1"
                                       value="<?= $isEdit ? esc($match['home_team_name']) : '' ?>"
                                       placeholder="ชื่อทีมเหย้า (กรณีไม่มีในระบบ)">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="font-weight-bold">ทีมเยือน</label>
                                <select name="away_team_id" class="form-control">
                                    <option value="">-- ไม่ระบุ --</option>
                                    <?php foreach ($teams as $t): ?>
                                        <option value="<?= $t['id'] ?>"
                                            <?= ($isEdit && $match['away_team_id'] == $t['id']) ? 'selected' : '' ?>>
                                            <?= esc($t['team_name']) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <small class="text-muted">หรือพิมพ์ชื่อด้านล่าง</small>
                                <input type="text" name="away_team_name" class="form-control mt-1"
                                       value="<?= $isEdit ? esc($match['away_team_name']) : '' ?>"
                                       placeholder="ชื่อทีมเยือน (กรณีไม่มีในระบบ)">
                            </div>
                        </div>
                    </div>

                    <!-- สกอร์ -->
                    <div class="form-row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">สกอร์เหย้า</label>
                                <input type="number" name="home_score" class="form-control" min="0"
                                       value="<?= $isEdit ? $match['home_score'] : 0 ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label class="font-weight-bold">สกอร์เยือน</label>
                                <input type="number" name="away_score" class="form-control" min="0"
                                       value="<?= $isEdit ? $match['away_score'] : 0 ?>">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="font-weight-bold">หมายเหตุ</label>
                        <textarea name="description" class="form-control" rows="3"><?= $isEdit ? esc($match['description']) : '' ?></textarea>
                    </div>

                    <!-- อัปโหลดวิดีโอ -->
                    <hr>
                    <h6 class="font-weight-bold mb-3">อัปโหลดวิดีโอ</h6>

                    <?php foreach ([1, 2] as $half): ?>
                    <?php
                    $existingVideo = null;
                    if (!empty($videos)) {
                        foreach ($videos as $v) {
                            if ($v['half'] == $half) { $existingVideo = $v; break; }
                        }
                    }
                    ?>
                    <div class="card border mb-3">
                        <div class="card-header py-2 d-flex justify-content-between align-items-center">
                            <span class="font-weight-bold">ครึ่งที่ <?= $half ?></span>
                            <?php if ($existingVideo): ?>
                                <span class="badge badge-<?= $existingVideo['status'] === 'completed' ? 'success' : ($existingVideo['status'] === 'error' ? 'danger' : 'warning') ?>">
                                    <?= $existingVideo['status'] ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        <div class="card-body py-2">
                            <?php if ($existingVideo): ?>
                                <div class="alert alert-light py-2 mb-2">
                                    <small>
                                        <i class="fas fa-video mr-1"></i>
                                        <?= basename($existingVideo['original_path']) ?>
                                        (<?= $existingVideo['file_size'] ? number_format($existingVideo['file_size'] / 1048576, 1) . ' MB' : '-' ?>)
                                    </small>
                                    <?php if ($matchId): ?>
                                    <button type="button" class="btn btn-xs btn-outline-danger ml-2 btn-del-video"
                                            data-id="<?= $existingVideo['id'] ?>">
                                        <i class="fas fa-trash"></i> ลบ
                                    </button>
                                    <?php endif; ?>
                                </div>
                                <small class="text-muted">อัปโหลดใหม่เพื่อแทนที่:</small>
                            <?php endif; ?>
                            <input type="file" name="video_half<?= $half ?>" class="form-control-file mt-1"
                                   accept="video/*">
                            <small class="text-muted">รองรับ .mp4, .avi, .mov (สูงสุด 500 MB)</small>
                        </div>
                    </div>
                    <?php endforeach; ?>

                    <div class="d-flex justify-content-between">
                        <a href="<?= $baseUrl ?>admin/analytics/matches" class="btn btn-secondary">
                            <i class="fas fa-times mr-1"></i>ยกเลิก
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save mr-1"></i><?= $isEdit ? 'บันทึกการแก้ไข' : 'สร้างแมตช์' ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
$(document).on('click', '.btn-del-video', function () {
    var btn = $(this);
    var id  = btn.data('id');
    Swal.fire({
        title: 'ลบวิดีโอ?',
        text: 'ไฟล์วิดีโอและข้อมูลที่เกี่ยวข้องจะถูกลบ',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e74a3b',
        confirmButtonText: 'ลบ',
        cancelButtonText: 'ยกเลิก',
    }).then(function (r) {
        if (r.isConfirmed) {
            $.ajax({
                url: '<?= $baseUrl ?>admin/analytics/matches/video/delete/' + id,
                type: 'POST',
                headers: { 'X-Requested-With': 'XMLHttpRequest', 'X-CSRF-TOKEN': '<?= csrf_hash() ?>' },
                success: function (res) {
                    if (res.status === 'success') {
                        btn.closest('.alert').remove();
                        Swal.fire('ลบแล้ว!', '', 'success');
                    }
                }
            });
        }
    });
});
</script>
