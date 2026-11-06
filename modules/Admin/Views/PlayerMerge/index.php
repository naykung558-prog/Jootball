<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('success')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <?= esc(session()->getFlashdata('error')) ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <div class="d-flex align-items-center">
            <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--navy-gradient); box-shadow: var(--box-shadow);">
                <i class="ti ti-users text-white fs-4"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-1" style="color: var(--secondary-color);">รวมนักเตะซ้ำ</h4>
                <p class="text-muted mb-0 small">กลุ่มที่ระบบรวมอัตโนมัติไม่ได้ (เลขบัตร checksum ไม่ผ่าน หรือ ชื่อ+วันเกิดซ้ำ) — ต้องตรวจสอบก่อนรวม</p>
            </div>
        </div>
    </div>
</div>

<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-3">
        <div class="table-responsive">
            <table id="dupGroupsTable" class="table table-hover align-middle mb-0" style="width: 100%;">
                <thead style="background: #f8f9fa; color: #06346D;">
                    <tr>
                        <th class="py-3 ps-4 text-center" width="5%">#</th>
                        <th class="py-3 text-center" width="14%">ประเภทที่ซ้ำ</th>
                        <th class="py-3 text-start" width="22%">คีย์</th>
                        <th class="py-3 text-start">ตัวอย่างนักเตะ</th>
                        <th class="py-3 text-center" width="9%">จำนวนซ้ำ</th>
                        <th class="py-3 text-center" width="12%">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($groups as $i => $g): ?>
                        <tr>
                            <td class="text-center"><?= $i + 1 ?></td>
                            <td class="text-center">
                                <?php if ($g['type'] === 'id_card'): ?>
                                    <span class="badge bg-warning text-dark">เลขบัตรซ้ำ</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark">ชื่อ+วันเกิดซ้ำ</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-start"><code><?= esc($g['label']) ?></code><br><small class="text-muted"><?= esc($g['sample']) ?></small></td>
                            <td class="text-start"><?= esc($g['type'] === 'id_card' ? $g['sample'] : $g['label']) ?></td>
                            <td class="text-center"><span class="badge bg-secondary"><?= (int) $g['member_count'] ?></span></td>
                            <td class="text-center">
                                <a class="btn btn-sm btn-primary"
                                   href="<?= base_url('admin/player-merge/compare?type=' . urlencode($g['type']) . '&key=' . urlencode($g['key'])) ?>">
                                    <i class="ti ti-git-merge"></i> ตรวจสอบ
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function () {
        $('#dupGroupsTable').DataTable({
            order: [[4, 'desc']],
            pageLength: 25,
            language: {
                search: 'ค้นหา:',
                lengthMenu: 'แสดง _MENU_ รายการ',
                info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ กลุ่ม',
                infoEmpty: 'ไม่มีรายการซ้ำ',
                zeroRecords: 'ไม่พบรายการ',
                paginate: { next: 'ถัดไป', previous: 'ก่อนหน้า' }
            }
        });
    });
</script>
<?php $this->endSection(); ?>
