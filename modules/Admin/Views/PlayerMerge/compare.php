<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<?php
// default keep = member with active+primary enrollment, else most child history, else lowest id
$keepDefault = null;
foreach ($members as $m) {
    if (!$keepDefault) { $keepDefault = $m; continue; }
    $a = [$m['has_primary'] ? 1 : 0, (int) $m['child_total'], -(int) $m['id']];
    $b = [$keepDefault['has_primary'] ? 1 : 0, (int) $keepDefault['child_total'], -(int) $keepDefault['id']];
    if ($a > $b) { $keepDefault = $m; }
}
$keepDefaultId = (int) ($keepDefault['id'] ?? 0);
?>

<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4 d-flex align-items-center justify-content-between">
        <div class="d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--navy-gradient);">
                <i class="ti ti-git-merge text-white fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">ตรวจสอบและรวมนักเตะ</h4>
                <p class="text-muted mb-0 small">เลือกนักเตะที่จะ <strong>เก็บไว้</strong> (canonical) — รายการที่ติ๊ก "รวม" จะถูกยุบเข้าตัวที่เก็บไว้และซ่อน (soft-delete)</p>
            </div>
        </div>
        <a href="<?= base_url('admin/player-merge') ?>" class="btn btn-outline-secondary"><i class="ti ti-arrow-left"></i> กลับ</a>
    </div>
</div>

<div class="alert alert-warning"><i class="ti ti-alert-triangle"></i> การรวมนักเตะย้อนกลับอัตโนมัติไม่ได้ — โปรดตรวจสอบให้แน่ใจว่าเป็นคนเดียวกัน</div>

<form method="post" action="<?= base_url('admin/player-merge/do-merge') ?>" onsubmit="return confirmMerge();">
    <div class="card border-0 shadow-sm">
        <div class="card-body p-3">
            <div class="table-responsive">
                <table class="table table-bordered align-middle mb-0">
                    <thead style="background: #f8f9fa; color: #06346D;">
                        <tr>
                            <th class="text-center" width="8%">เก็บไว้</th>
                            <th class="text-center" width="8%">รวม</th>
                            <th class="text-center">id</th>
                            <th>ชื่อ - นามสกุล</th>
                            <th>เลขบัตร</th>
                            <th class="text-center">วันเกิด</th>
                            <th>ทีม</th>
                            <th class="text-center">ทีมหลัก</th>
                            <th class="text-center">จำนวนข้อมูล</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($members as $m): ?>
                            <?php $mid = (int) $m['id']; ?>
                            <tr>
                                <td class="text-center">
                                    <input class="form-check-input keep-radio" type="radio" name="keep_id" value="<?= $mid ?>" <?= $mid === $keepDefaultId ? 'checked' : '' ?>>
                                </td>
                                <td class="text-center">
                                    <input class="form-check-input drop-check" type="checkbox" name="drop_ids[]" value="<?= $mid ?>" data-id="<?= $mid ?>" <?= $mid === $keepDefaultId ? '' : 'checked' ?>>
                                </td>
                                <td class="text-center"><?= $mid ?></td>
                                <td><?= esc(trim($m['first_name'] . ' ' . $m['last_name'])) ?><?= !empty($m['nick_name']) ? ' (' . esc($m['nick_name']) . ')' : '' ?></td>
                                <td><code><?= esc($m['id_card'] ?? '-') ?></code></td>
                                <td class="text-center"><?= esc($m['birth_date'] ?? '-') ?></td>
                                <td><?= esc($m['team_names'] ?: '-') ?></td>
                                <td class="text-center"><?= $m['has_primary'] ? '<span class="badge bg-success">มี</span>' : '<span class="text-muted">-</span>' ?></td>
                                <td class="text-center"><span class="badge bg-secondary"><?= (int) $m['child_total'] ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-danger"><i class="ti ti-git-merge"></i> รวมนักเตะที่เลือก</button>
            </div>
        </div>
    </div>
</form>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    // keep-row's "merge" checkbox must never be checked; toggling keep unchecks its own merge box
    function syncRows() {
        var keepId = $('input[name="keep_id"]:checked').val();
        $('.drop-check').each(function () {
            if ($(this).data('id') == keepId) {
                $(this).prop('checked', false).prop('disabled', true);
            } else {
                $(this).prop('disabled', false);
            }
        });
    }
    $(document).ready(function () {
        $('.keep-radio').on('change', syncRows);
        syncRows();
    });
    function confirmMerge() {
        var keepId = $('input[name="keep_id"]:checked').val();
        var drops = $('.drop-check:checked').not(':disabled').length;
        if (!keepId) { alert('กรุณาเลือกนักเตะที่จะเก็บไว้'); return false; }
        if (drops === 0) { alert('กรุณาเลือกอย่างน้อยหนึ่งรายการที่จะรวม'); return false; }
        return confirm('ยืนยันรวม ' + drops + ' รายการเข้าสู่ id ' + keepId + ' ?\nการกระทำนี้ย้อนกลับอัตโนมัติไม่ได้');
    }
</script>
<?php $this->endSection(); ?>
