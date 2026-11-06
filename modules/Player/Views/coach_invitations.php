<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h4 class="card-title text-white mb-0">
                        <i class="ti ti-mail"></i> คำเชิญเข้าร่วมทีม
                    </h4>
                </div>
                <div class="card-body">
                    <?php if (empty($invitations)): ?>
                        <div class="text-center py-5">
                            <i class="ti ti-inbox" style="font-size: 4rem; color: #ccc;"></i>
                            <h5 class="mt-3 text-muted">ไม่มีคำเชิญใหม่</h5>
                            <p class="text-muted">คุณไม่มีคำเชิญเข้าร่วมทีมใดๆ ในขณะนี้</p>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($invitations as $invitation): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card border border-warning h-100">
                                        <div class="card-header bg-warning-subtle">
                                            <h6 class="card-title mb-0">
                                                <i class="ti ti-users"></i> คำเชิญจาก: <?= $invitation['team_name'] ?>
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="text-center mb-3">
                                                <?php if (!empty($invitation['team_logo'])): ?>
                                                    <img src="<?= base_url($invitation['team_logo']) ?>" alt="<?= $invitation['team_name'] ?>" class="rounded-circle" width="80" height="80">
                                                <?php else: ?>
                                                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                                        <i class="ti ti-users text-white" style="font-size: 2rem;"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <p class="text-center mb-3">
                                                <strong><?= $invitation['message'] ?></strong>
                                            </p>
                                            <div class="small text-muted mb-3">
                                                <div><i class="ti ti-user"></i> เชิญโดย: <?= $invitation['invited_by_name'] ?> <?= $invitation['invited_by_lastname'] ?></div>
                                                <div><i class="ti ti-calendar"></i> วันที่: <?= date('d/m/Y H:i', strtotime($invitation['created_at'])) ?> น.</div>
                                            </div>
                                        </div>
                                        <div class="card-footer">
                                            <div class="d-grid gap-2 d-md-block text-center">
                                                <button onclick="respondToInvitation(<?= $invitation['id'] ?>, 'accepted')"
                                                        class="btn btn-success btn-sm">
                                                    <i class="ti ti-check"></i> ยอมรับ
                                                </button>
                                                <button onclick="respondToInvitation(<?= $invitation['id'] ?>, 'rejected')"
                                                        class="btn btn-danger btn-sm">
                                                    <i class="ti ti-x"></i> ปฏิเสธ
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
function respondToInvitation(invitationId, response) {
    let title = response === 'accepted' ? 'ยอมรับคำเชิญ' : 'ปฏิเสธคำเชิญ';
    let text = response === 'accepted' ? 'คุณต้องการยอมรับคำเชิญเข้าร่วมทีมนี้หรือไม่?' : 'คุณต้องการปฏิเสธคำเชิญเข้าร่วมทีมนี้หรือไม่?';
    let confirmText = response === 'accepted' ? 'ยอมรับ' : 'ปฏิเสธ';

    Swal.fire({
        title: title,
        text: text,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: response === 'accepted' ? '#28a745' : '#dc3545',
        cancelButtonColor: '#6c757d',
        confirmButtonText: confirmText,
        cancelButtonText: 'ยกเลิก'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                method: "POST",
                url: "<?= base_url('player/respond-invitation') ?>",
                data: {
                    invitation_id: invitationId,
                    response: response
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: data.message,
                            showConfirmButton: false,
                            timer: 2000
                        }).then(() => {
                            window.location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: data.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'ไม่สามารถตอบรับคำเชิญได้'
                    });
                }
            });
        }
    });
}

<?php if (session()->getFlashdata('success')) : ?>
    Swal.fire({
        icon: 'success',
        title: 'สำเร็จ!',
        text: '<?= session()->getFlashdata('success') ?>',
        timer: 2500,
        showConfirmButton: false
    });
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    Swal.fire({
        icon: 'error',
        title: 'เกิดข้อผิดพลาด!',
        text: '<?= session()->getFlashdata('error') ?>'
    });
<?php endif; ?>
</script>
<?php $this->endSection(); ?>