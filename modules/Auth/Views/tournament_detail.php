<?php $this->extend('template/layout') ?>
<?php $this->section('content'); ?>
<style>
    /* Team Card Styling */
    .team-card {
        transition: all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1);
    }

    .team-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    /* Badge styling */
    .badge {
        font-size: 0.75rem;
        padding: 0.35rem 0.65rem;
        font-weight: 500;
    }
</style>
<div class="container-fluid p-0">
    <!-- Tournament Header -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4 bg-primary text-white overflow-hidden position-relative">
                <!-- Decorative background -->
                <div class="position-absolute top-0 end-0 opacity-10" style="transform: translate(30%, -30%);">
                    <i class="ti ti-trophy" style="font-size: 15rem; color: #f2b818;"></i>
                </div>

                <div class="card-body p-5 position-relative z-1">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div class="flex-grow-1">
                            <div class="mb-2">
                                <a href="<?= base_url('auth/tournament/list') ?>" class="btn btn-sm btn-light rounded-pill">
                                    <i class="ti ti-arrow-left me-1"></i><?= lang('Common.back') ?>
                                </a>
                            </div>
                            <h2 class="fw-bold mb-2 text-white"><?= esc($news['name']) ?></h2>
                            <?php if (!empty($news['generation'])): ?>
                                <span class="badge bg-white text-primary rounded-pill px-3 py-2">
                                    <i class="ti ti-trophy me-1"></i><?= esc($news['generation']) ?>
                                </span>
                            <?php endif; ?>
                            <?php if (!empty($news['start_date']) && !empty($news['end_date'])): ?>
                                <div class="mt-3 d-flex align-items-center text-white">
                                    <i class="ti ti-calendar-event me-2" style="font-size: 1.2rem;"></i>
                                    <span class="fw-bold me-2">วันรับสมัคร:</span>
                                    <span>
                                        <?php
                                        $start = new DateTime($news['start_date']);
                                        $end = new DateTime($news['end_date']);
                                        echo $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>

                            <?php if (!empty($news['tournament_start_date']) && !empty($news['tournament_end_date'])): ?>
                                <div class="mt-2 d-flex align-items-center text-white">
                                    <i class="ti ti-trophy me-2" style="font-size: 1.2rem;"></i>
                                    <span class="fw-bold me-2">วันแข่งขัน:</span>
                                    <span>
                                        <?php
                                        $start = new DateTime($news['tournament_start_date']);
                                        $end = new DateTime($news['tournament_end_date']);
                                        echo $start->format('d/m/Y') . ' - ' . $end->format('d/m/Y');
                                        ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                        </div>
                        <?php if (!empty($news['logo']) && file_exists('./' . $news['logo'])): ?>
                            <div class="text-end">
                                <img src="<?= base_url($news['logo']) ?>" class="img-fluid rounded-3 shadow" alt="<?= esc($news['name']) ?>" style="max-height: 150px; max-width: 150px; object-fit: contain; background: white; padding: 10px;">
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tournament Description -->
    <?php if (!empty($news['detail'])): ?>
        <div class="row mb-4">
            <div class="col-md-12">
                <div class="card shadow-sm border-0 rounded-4">
                    <div class="card-body p-4">
                        <h5 class="fw-bold mb-3">
                            <i class="ti ti-info-circle me-2 text-primary"></i><?= lang('Common.details') ?>
                        </h5>
                        <p class="text-muted mb-0"><?= nl2br(esc($news['detail'])) ?></p>
                        <?php if (!empty($news['pdf_path'])): ?>
                            <div class="mt-4">
                                <h6 class="fw-bold mb-3"><i class="ti ti-file-text me-2 text-primary"></i>เอกสารแนบ</h6>
                                <div class="ratio ratio-16x9 rounded-4 overflow-hidden border shadow-sm" style="min-height: 500px;">
                                    <iframe src="<?= base_url($news['pdf_path']) ?>" title="PDF Preview" allowfullscreen></iframe>
                                </div>
                                <div class="mt-2 text-end">
                                    <a href="<?= base_url($news['pdf_path']) ?>" target="_blank" class="btn btn-sm btn-outline-secondary rounded-pill">
                                        <i class="ti ti-external-link me-1"></i>เปิดในหน้าต่างใหม่
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Teams Participating Section -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-header bg-white border-0 p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <h4 class="fw-bold mb-0">
                            <i class="ti ti-users me-2 text-primary"></i><?= lang('Common.teams_participating') ?>
                            <?php if (!empty($teams_in_tournament)): ?>
                                <span class="badge bg-primary rounded-pill ms-2"><?= count($teams_in_tournament) ?> <?= lang('Common.teams') ?></span>
                            <?php endif; ?>
                        </h4>
                    </div>
                </div>
                <div class="card-body p-4">
                    <?php if (!empty($teams_in_tournament)): ?>
                        <div class="row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-5 g-4">
                            <?php foreach ($teams_in_tournament as $team): ?>
                                <div class="col">
                                    <?php
                                    // Check if team is approved
                                    // If register_status is not set, current view logic treats it as Approved (legacy or default)
                                    // If set, must be 1 (Approved)
                                    $isApproved = !isset($team['register_status']) || $team['register_status'] == 1;

                                    // Encrypt Tour ID for security
                                    $encryptedTourId = bin2hex($encrypter->encrypt($news['tour_id']));

                                    $onclickAttr = $isApproved ? 'onclick="showTeamDetails(' . $team['id'] . ', \'' . $encryptedTourId . '\')"' : '';
                                    $styleAttr = $isApproved ? 'cursor: pointer;' : 'cursor: default;';
                                    ?>
                                    <div class="card h-100 shadow-sm border-0 rounded-4 position-relative team-card" <?= $onclickAttr ?> style="<?= $styleAttr ?>">
                                        <!-- Status Badge -->
                                        <div class="position-absolute top-0 end-0 m-2 z-1">
                                            <div class="d-flex flex-column align-items-end gap-1">
                                                <?php if (isset($team['register_status'])): ?>
                                                    <?php if ($team['register_status'] == 1): ?>
                                                        <?php if (isset($team['player_list_status']) && $team['player_list_status'] == 1): ?>
                                                            <span class="badge bg-success rounded-pill shadow-sm">
                                                                <i class="ti ti-check me-1"></i>Approved (Ready Game)
                                                            </span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning rounded-pill shadow-sm">
                                                                <i class="ti ti-clock me-1"></i>Approved (List Pending)
                                                            </span>
                                                        <?php endif; ?>
                                                    <?php elseif ($team['register_status'] == 0 || $team['register_status'] == 3): ?>
                                                        <span class="badge bg-warning text-white rounded-pill shadow-sm">
                                                            <i class="ti ti-clock me-1"></i>Waiting
                                                        </span>
                                                    <?php elseif ($team['register_status'] == 2): ?>
                                                        <span class="badge bg-danger rounded-pill shadow-sm">
                                                            <i class="ti ti-x me-1"></i>Rejected
                                                        </span>
                                                    <?php endif; ?>
                                                <?php else: ?>
                                                    <span class="badge bg-success rounded-pill shadow-sm">
                                                        <i class="ti ti-check me-1"></i>Approved
                                                    </span>
                                                <?php endif; ?>
                                            </div>
                                        </div>

                                        <div class="card-body text-center p-4 d-flex flex-column align-items-center justify-content-center">
                                            <div class="mb-3 position-relative">
                                                <?php if (!empty($team['logo']) && file_exists('./' . $team['logo'])): ?>
                                                    <img src="<?= base_url($team['logo']) ?>" class="img-fluid rounded shadow-sm" alt="<?= esc($team['name']) ?>" style="width: 100px; height: 100px; object-fit: contain; background: #fff;">
                                                <?php else: ?>
                                                    <div class="bg-light rounded d-flex align-items-center justify-content-center shadow-sm" style="width: 100px; height: 100px;">
                                                        <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="Default" style="max-height: 50px; opacity: 0.5;">
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <h6 class="fw-bold text-dark mb-0 text-truncate w-100" title="<?= esc($team['name']) ?>">
                                                <?= esc($team['name']) ?>
                                            </h6>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <div class="mb-4">
                                <div class="bg-light rounded-circle d-inline-flex align-items-center justify-content-center" style="width: 100px; height: 100px;">
                                    <i class="ti ti-users-off text-secondary" style="font-size: 3rem;"></i>
                                </div>
                            </div>
                            <h5 class="fw-bold text-dark"><?= lang('Common.no_teams_yet') ?></h5>
                            <p class="text-muted"><?= lang('Common.be_first_team') ?></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    <div class="row my-2">
        <div class="col-12 text-center">
            <?php if ((!empty(session()->get('user_id')) && (!empty(session()->get('team.id')) || !empty($available_teams))) || session()->get('user_type') == 'coach') { ?>

                <!-- แสดงทีมที่เลือกจากหน้ารายการ (อ่านจาก sessionStorage ผ่าน JS) -->
                <?php if (!empty($available_teams)): ?>
                <div id="selectedTeamBanner" class="mb-3" style="display:none;">
                    <div class="d-inline-flex align-items-center gap-2 bg-light border border-success rounded-pill px-4 py-2 shadow-sm">
                        <img id="selectedTeamLogo" src="" class="rounded-circle border" style="width:36px;height:36px;object-fit:cover;">
                        <span class="fw-bold text-success" id="selectedTeamName"></span>
                    </div>
                </div>
                <?php endif; ?>

                <!-- แสดงปุ่มลงทะเบียนสำหรับแต่ละทีม -->
                <?php if (!empty($available_teams) && count($available_teams) > 1): ?>
                    <!-- มีหลายทีม: ใช้ทีมที่เลือกจากหน้ารายการ (sessionStorage) สมัครเลย -->
                    <div class="d-flex justify-content-center align-items-center gap-2 flex-wrap" id="multiTeamActions">
                        <!-- JS จะ render ปุ่มตามสถานะของทีมที่เลือก -->
                    </div>
                    <div class="mt-2">
                        <a href="<?= base_url('auth/tournament/list') ?>" class="btn btn-outline-secondary rounded-pill px-4">
                            <i class="ti ti-arrow-left me-1"></i><?= lang('Common.back') ?>
                        </a>
                    </div>
                <?php elseif (!empty($available_teams) && count($available_teams) == 1): ?>
                    <!-- มีทีมเดียว: ตรวจสอบสถานะการลงทะเบียน -->
                    <?php $singleTeam = $available_teams[0]; ?>
                    <?php if (!isset($singleTeam['tournament_register_status'])): ?>
                        <!-- ยังไม่ได้ลงทะเบียน -->
                        <button type="button" class="btn btn-primary rounded-pill px-4 py-2" onclick="registerTournament('<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>', <?= $singleTeam['id'] ?>)">
                            <i class="ti ti-plus me-2"></i><?= lang('Common.register_competition') ?>
                        </button>
                    <?php else: ?>
                        <!-- ลงทะเบียนแล้ว -->
                        <?php if ($singleTeam['tournament_register_status'] == 0): ?>
                            <button type="button" class="btn btn-warning rounded-pill px-4" disabled>
                                <i class="ti ti-clock me-2"></i><?= lang('Common.waiting_approval') ?>
                            </button>
                        <?php elseif ($singleTeam['tournament_register_status'] == 1): ?>
                            <?php if (isset($singleTeam['player_list_status']) && $singleTeam['player_list_status'] == 1): ?>
                                <button type="button" class="btn btn-success rounded-pill" disabled>
                                    <i class="ti ti-check me-2"></i>Approved (Ready Game)
                                </button>
                            <?php else: ?>
                                <button type="button" class="btn btn-warning rounded-pill" disabled>
                                    <i class="ti ti-clock me-2"></i>Approved (List Pending)
                                </button>
                            <?php endif; ?>
                        <?php elseif ($singleTeam['tournament_register_status'] == 3): ?>
                            <button type="button" class="btn btn-warning rounded-pill px-4" disabled>
                                <i class="ti ti-currency-baht me-2"></i>รอชำระเงิน
                            </button>
                        <?php elseif ($singleTeam['tournament_register_status'] == 2): ?>
                            <button type="button" class="btn btn-danger rounded-pill" disabled>
                                <i class="ti ti-x me-2"></i><?= lang('Common.registration_rejected') ?>
                            </button>
                        <?php endif; ?>

                        <?php if (in_array($singleTeam['tournament_register_status'], [0, 2, 3])): ?>
                            <button type="button" class="btn btn-outline-danger rounded-pill px-4" onclick="cancelTournamentRegistration('<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>', <?= $singleTeam['id'] ?>)">
                                <i class="ti ti-x me-2"></i>ยกเลิกการสมัคร
                            </button>
                        <?php endif; ?>

                        <!-- ปุ่มชำระเงิน/แนบเอกสาร - แสดงเฉพาะเมื่อรออนุมัติ(0) หรือ รอชำระเงิน(3) -->
                        <?php if (isset($singleTeam['tournament_register_status']) && in_array($singleTeam['tournament_register_status'], [0, 3])): ?>
                            <?php if (!empty($news['enable_payment'])): ?>
                                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="openPaymentModal('<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>', <?= $singleTeam['id'] ?>)">
                                    <i class="ti ti-receipt me-2"></i>ชำระเงินค่าสมัคร / แจ้งสลิป
                                </button>
                            <?php elseif (!empty($news['enable_document'])): ?>
                                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="openPaymentModal('<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>', <?= $singleTeam['id'] ?>)">
                                    <i class="ti ti-file-text me-2"></i>แนบเอกสารรับสมัคร
                                </button>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endif; ?>
                <?php else: ?>
                    <!-- ไม่มีทีม -->
                    <a href="<?= base_url('academy/register') ?>" class="btn btn-primary rounded-pill px-4 py-2">
                        <i class="ti ti-users-plus me-2"></i>สร้าง Academy ก่อนสมัคร
                    </a>
                <?php endif; ?>
            <?php } else { ?>
                <!-- ไม่ได้เข้าสู่ระบบ -->
                <a class="btn btn-primary rounded-pill" href="<?= base_url('auth') . '?return_url=' . urlencode(current_url()) ?>"><?= lang('Common.login') ?></a>
            <?php } ?>
        </div>
    </div>

    <!-- Team Selection Modal -->
    <?php if (!empty($available_teams) && count($available_teams) > 1): ?>
        <div class="modal fade" id="teamSelectModal" tabindex="-1" aria-labelledby="teamSelectModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content rounded-4 border-0 shadow-lg">
                    <div class="modal-header bg-primary text-white rounded-top-4">
                        <h5 class="modal-title fw-bold text-white" id="teamSelectModalLabel">
                            <i class="ti ti-users me-2"></i>เลือกทีมที่ต้องการสมัคร
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-4">
                        <p class="text-muted mb-4">กรุณาเลือกทีมที่ต้องการสมัครเข้าร่วมรายการ</p>

                        <div class="list-group">
                            <?php foreach ($available_teams as $avTeam): ?>
                                <?php
                                $isRegistered = isset($avTeam['tournament_register_status']);
                                $status = $avTeam['tournament_register_status'] ?? null;
                                $isSubTeam = !empty($avTeam['parent_team_id']);
                                ?>
                                <div class="list-group-item border rounded-3 mb-2 <?= $isRegistered ? 'bg-light' : '' ?>">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <?php
                                            $teamLogo = !empty($avTeam['logo']) ? base_url($avTeam['logo']) : base_url('public/img/exam-logo.webp');
                                            ?>
                                            <img src="<?= $teamLogo ?>" class="rounded-circle me-3 border" style="width: 50px; height: 50px; object-fit: cover;">
                                            <div>
                                                <h6 class="mb-0 fw-bold">
                                                    <?= esc($avTeam['name']) ?>
                                                </h6>
                                                <?php if (!empty($avTeam['name_en'])): ?>
                                                    <small class="text-muted"><?= esc($avTeam['name_en']) ?></small>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                        <div>
                                            <?php if (!$isRegistered): ?>
                                                <button type="button" class="btn btn-primary btn-sm rounded-pill" onclick="registerTournament('<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>', <?= $avTeam['id'] ?>)">
                                                    <i class="ti ti-plus me-1"></i>สมัคร
                                                </button>
                                            <?php else: ?>
                                                <div class="d-flex flex-column align-items-end gap-1">
                                                    <?php if ($status == 0): ?>
                                                        <span class="badge bg-warning text-white rounded-pill">รออนุมัติ</span>
                                                    <?php elseif ($status == 1): ?>
                                                        <?php if (isset($avTeam['player_list_status']) && $avTeam['player_list_status'] == 1): ?>
                                                            <span class="badge bg-success rounded-pill">Approved (Ready Game)</span>
                                                        <?php else: ?>
                                                            <span class="badge bg-warning rounded-pill">Approved (List Pending)</span>
                                                        <?php endif; ?>
                                                    <?php elseif ($status == 3): ?>
                                                        <span class="badge bg-secondary rounded-pill">รอชำระเงิน</span>
                                                    <?php elseif ($status == 2): ?>
                                                        <span class="badge bg-danger rounded-pill">ถูกปฏิเสธ</span>
                                                    <?php endif; ?>

                                                    <?php if (in_array($status, [0, 3])): ?>
                                                        <?php if (!empty($news['enable_payment'])): ?>
                                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-1" onclick="openPaymentModal('<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>', <?= $avTeam['id'] ?>)">
                                                                <i class="ti ti-receipt me-1"></i>ชำระเงิน
                                                            </button>
                                                        <?php elseif (!empty($news['enable_document'])): ?>
                                                            <button type="button" class="btn btn-outline-primary btn-sm rounded-pill mt-1" onclick="openPaymentModal('<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>', <?= $avTeam['id'] ?>)">
                                                                <i class="ti ti-file-text me-1"></i>แนบเอกสาร
                                                            </button>
                                                        <?php endif; ?>
                                                    <?php endif; ?>

                                                    <?php if (in_array($status, [0, 2, 3])): ?>
                                                        <button type="button" class="btn btn-outline-danger btn-sm rounded-pill mt-1" onclick="cancelTournamentRegistration('<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>', <?= $avTeam['id'] ?>)">
                                                            <i class="ti ti-x me-1"></i>ยกเลิก
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <!-- Payment/Register Modal (Now primarily for Payment) -->
    <div class="modal fade" id="paymentModal" tabindex="-1" aria-labelledby="paymentModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content rounded-4 border-0 shadow-lg">
                <div class="modal-header <?= !empty($news['enable_payment']) ? 'bg-warning text-dark' : 'bg-primary text-white' ?> rounded-top-4">
                    <h5 class="modal-title fw-bold text-white" id="paymentModalLabel">
                        <?php if (!empty($news['enable_payment'])): ?>
                            <i class="ti ti-receipt me-2"></i>แจ้งชำระเงิน / อัปเดตสลิป
                        <?php else: ?>
                            <i class="ti ti-file-text me-2"></i>แนบเอกสารรับสมัคร
                        <?php endif; ?>
                    </h5>
                    <button type="button" class="btn-close <?= empty($news['enable_payment']) ? 'btn-close-white' : '' ?>" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <!-- Form for submission -->
                <form id="paymentForm" enctype="multipart/form-data">
                    <input type="hidden" name="id" id="modalTourId" value="<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>">
                    <input type="hidden" name="team_id" id="modalTeamId" value="">

                    <div class="modal-body p-4">
                        <!-- แสดงชื่อทีมที่กำลังชำระเงิน -->
                        <div class="alert alert-info mb-3" id="paymentTeamInfo" style="display: none;">
                            <div class="d-flex align-items-center">
                                <img id="paymentTeamLogo" src="" class="rounded-circle me-2 border" style="width: 40px; height: 40px; object-fit: cover;">
                                <div>
                                    <strong id="paymentTeamName"></strong>
                                    <span class="badge bg-primary ms-2" id="paymentTeamBadge"></span>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($news['enable_payment'])): ?>
                        <p class="text-muted mb-4">
                            กรุณาแนบหลักฐานการชำระเงินเพื่อยืนยันสิทธิ์
                        </p>
                        <?php elseif (!empty($news['enable_document'])): ?>
                        <p class="text-muted mb-4">
                            กรุณาแนบเอกสารประกอบการสมัคร (*ขนาดไฟล์ไม่เกิน 10MB)
                        </p>
                        <?php endif; ?>

                        <?php if (!empty($news['enable_payment'])): ?>
                        <?php if (!empty($news['bank_id']) || !empty($news['account_no']) || !empty($news['promptpay']) || !empty($news['qrcode'])): ?>
                            <div class="alert alert-light border mb-4">
                                <h6 class="fw-bold mb-2 text-primary">ข้อมูลการชำระเงิน</h6>
                                <div class="row">
                                    <div class="col-<?= (!empty($news['qrcode'])) ? '7' : '12' ?>">
                                        <?php if (!empty($news['bank_name'])): ?>
                                            <p class="mb-1 small"><strong>ธนาคาร:</strong> <?= esc($news['bank_name']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($news['account_name'])): ?>
                                            <p class="mb-1 small"><strong>ชื่อบัญชี:</strong> <?= esc($news['account_name']) ?></p>
                                        <?php endif; ?>
                                        <?php if (!empty($news['account_no'])): ?>
                                            <p class="mb-1 small"><strong>เลขที่บัญชี:</strong> <span class="fw-bold text-dark"><?= esc($news['account_no']) ?></span></p>
                                        <?php endif; ?>
                                        <?php if (!empty($news['promptpay'])): ?>
                                            <p class="mb-1 small"><strong>พร้อมเพย์:</strong> <?= esc($news['promptpay']) ?></p>
                                        <?php endif; ?>
                                    </div>

                                    <?php if (!empty($news['qrcode'])): ?>
                                        <div class="col-5 text-center">
                                            <?php
                                            $qrSrc = $news['qrcode'];
                                            if (strpos($qrSrc, 'data:image') === false && strpos($qrSrc, 'http') === false) {
                                                $qrSrc = base_url($qrSrc);
                                            }
                                            ?>
                                            <img src="<?= $qrSrc ?>" class="img-fluid rounded border" style="max-height: 120px;" alt="Payment QR Code">
                                            <small class="d-block text-muted mt-1" style="font-size: 10px;">สแกนเพื่อชำระเงิน</small>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="mb-3">
                            <label for="payment_slip" class="form-label fw-bold">แนบสลิปการโอนเงิน</label>
                            <input class="form-control" type="file" id="payment_slip" name="payment_slip" accept="image/*" onchange="checkFileSize(this)">
                            <div class="form-text text-muted">รองรับไฟล์รูปภาพ JPG, PNG (ขนาดไม่เกิน 10MB)</div>
                        </div>

                        <!-- ส่วนแสดงประวัติสลิปของแต่ละทีม (โหลดจาก JS) -->
                        <div class="mb-3" id="slipHistoryContainer" style="display: none;">
                            <label class="form-label fw-bold">ประวัติการแนบสลิป</label>
                            <div class="row g-2 overflow-auto py-2 border rounded bg-white mb-2" style="max-height: 300px;" id="slipHistoryContent">
                            </div>
                            <div class="p-2 border rounded-3 bg-light-warning small">
                                <i class="ti ti-info-circle me-1 text-warning"></i> คุณสามารถอัปโหลดสลิปเพิ่มเติมเพื่อ<b>เพิ่ม</b>ข้อมูล ระบบจะบันทึกประวัติไว้ทั้งหมดครับ
                            </div>
                        </div>
                        <?php endif; ?>

                        <?php if (!empty($news['enable_document'])): ?>
                        <div class="mb-3">
                            <label class="form-label fw-bold">
                                <i class="ti ti-file-text me-1 text-primary"></i>เอกสารรับสมัคร
                            </label>
                            <?php if (!empty($news['document_description'])): ?>
                                <div class="alert alert-info small mb-2">
                                    <i class="ti ti-info-circle me-1"></i><?= nl2br(esc($news['document_description'])) ?>
                                </div>
                            <?php endif; ?>
                            <!-- แสดงเอกสารที่เคย upload ไปก่อนหน้า -->
                            <div id="currentDocumentContainer" class="mb-2" style="display: none;">
                                <div class="border rounded p-2 bg-light">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-file-check me-2 text-success fs-5"></i>
                                            <div>
                                                <span class="small fw-bold text-success">เอกสารที่แนบแล้ว</span>
                                                <br>
                                                <a id="currentDocumentLink" href="#" target="_blank" class="small text-primary">
                                                    <i class="ti ti-eye me-1"></i>ดูเอกสาร
                                                </a>
                                            </div>
                                        </div>
                                        <span id="currentDocumentPreview"></span>
                                    </div>
                                </div>
                                <div class="form-text text-warning small mt-1">
                                    <i class="ti ti-info-circle me-1"></i>หากอัปโหลดไฟล์ใหม่ จะเป็นการแทนที่เอกสารเดิม
                                </div>
                            </div>

                            <input class="form-control" type="file" id="registration_document" name="registration_document" accept="image/*,.pdf" onchange="checkFileSize(this)">
                            <div class="form-text text-muted">รองรับไฟล์รูปภาพ (JPG, PNG) หรือ PDF (ขนาดไม่เกิน 10MB)</div>
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                        <button type="submit" class="btn btn-primary rounded-pill px-4">
                            <?php if (!empty($news['enable_payment'])): ?>
                                <i class="ti ti-receipt me-2"></i>ส่งข้อมูลชำระเงิน
                            <?php else: ?>
                                <i class="ti ti-file-text me-2"></i>ส่งเอกสาร
                            <?php endif; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
</div>

<!-- Team Details Modal -->
<div class="modal fade" id="teamDetailsModal" tabindex="-1" aria-labelledby="teamDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow-lg">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" id="teamDetailsModalLabel">
                    <i class="ti ti-shirt me-2 text-primary"></i>ข้อมูลทีม (Team Details)
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <div id="teamDetailsLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <div id="teamDetailsContent" style="display: none;">
                    <!-- Team Header Info -->
                    <div class="text-center mb-5">
                        <img id="detailTeamLogo" src="" class="rounded-circle shadow-sm border mb-3" style="width: 120px; height: 120px; object-fit: cover;">
                        <h4 class="fw-bold mb-1" id="detailTeamName"></h4>
                        <p class="text-muted mb-2" id="detailTeamNameEn"></p>
                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3" id="detailTeamId"></span>
                    </div>

                    <!-- Contact Info Section -->
                    <div class="card bg-light border-0 rounded-4 mb-4">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-3"><i class="ti ti-info-circle me-2"></i>ข้อมูลการติดต่อ</h6>
                            <div class="row g-4">
                                <div class="col-md-6">
                                    <div class="p-3 bg-white rounded-3 h-100 border-start border-4 border-primary shadow-sm" style="--bs-border-width: 5px;">
                                        <p class="small text-muted mb-1">หัวหน้าผู้ฝึกสอน</p>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ti ti-user-circle me-2 text-primary fs-5"></i>
                                            <span class="fw-bold" id="detailCoachName">-</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-phone me-2 text-success fs-5"></i>
                                            <span class="text-dark" id="detailCoachPhone">-</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="p-3 bg-white rounded-3 h-100 border-start border-4 border-info shadow-sm">
                                        <p class="small text-muted mb-1">ผู้ประสานงาน</p>
                                        <div class="d-flex align-items-center mb-2">
                                            <i class="ti ti-user me-2 text-info fs-5"></i>
                                            <span class="fw-bold" id="detailContactName">-</span>
                                        </div>
                                        <div class="d-flex align-items-center mb-1">
                                            <i class="ti ti-phone me-2 text-success fs-5"></i>
                                            <span class="text-dark" id="detailContactPhone">-</span>
                                        </div>
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-brand-line me-2 text-success fs-5"></i>
                                            <span class="text-dark" id="detailContactLine">-</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Player List Section -->
                    <div class="mb-3 d-flex align-items-center justify-content-between">
                        <h5 class="fw-bold m-0 p-2 text-white rounded-top px-4" style="background-color: #16478d; border-bottom-left-radius: 0 !important; border-bottom-right-radius: 0 !important; display: inline-block;">
                            <i class="ti ti-users me-2"></i>รายชื่อนักเตะ
                        </h5>
                    </div>


                    <div class="table-responsive rounded-3 border">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th class="py-3 ps-4 small text-uppercase" style="color: #16478d;">ลำดับ</th>
                                    <th class="py-3 small text-uppercase" style="color: #16478d;">รูป</th>
                                    <th class="py-3 small text-uppercase" style="color: #16478d;">หมายเลข</th>
                                    <th class="py-3 small text-uppercase" style="color: #16478d;">ชื่อ-นามสกุล</th>
                                    <th class="py-3 small text-uppercase" style="color: #16478d;">ชื่อเล่น</th>
                                    <th class="py-3 small text-uppercase" style="color: #16478d;">วัน/เดือน/ปีเกิด</th>
                                    <th class="py-3 pe-4 text-center small text-uppercase" style="color: #16478d;">อายุ</th>
                                </tr>
                            </thead>
                            <tbody id="detailPlayerTableBody">
                                <!-- Player Rows will be injected here -->
                            </tbody>
                        </table>
                    </div>

                    <!-- Jersey Section -->
                    <div id="jerseySection" style="display: none;" class="mt-4">
                        <div class="mb-3">
                            <h5 class="fw-bold m-0 p-2 text-white rounded-top px-4" style="background-color: #16478d; display: inline-block;">
                                <i class="ti ti-shirt me-2"></i>ชุดแข่งขัน
                            </h5>
                        </div>
                        <div class="row g-3" id="jerseyContainer">
                            <!-- Jersey images will be injected here -->
                        </div>
                    </div>

                </div>
            </div>
            <div class="modal-footer border-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

</div>

<!-- สร้าง JSON สำหรับข้อมูล registration ของแต่ละทีม -->
<script>
    const teamRegistrations = <?= json_encode(array_map(function ($team) {
                                    return [
                                        'id' => (int)$team['id'],
                                        'name' => $team['name'],
                                        'logo' => !empty($team['logo']) ? base_url($team['logo']) : base_url('public/img/exam-logo.webp'),
                                        'is_subteam' => !empty($team['parent_team_id']),
                                        'payment_slip' => $team['payment_slip'] ?? null,
                                        'registration_document' => !empty($team['registration_document']) ? base_url($team['registration_document']) : null,
                                        'registration_document_ext' => !empty($team['registration_document']) ? strtolower(pathinfo($team['registration_document'], PATHINFO_EXTENSION)) : null,
                                        'register_status' => isset($team['tournament_register_status']) ? (int)$team['tournament_register_status'] : null,
                                        'player_list_status' => isset($team['player_list_status']) ? (int)$team['player_list_status'] : null
                                    ];
                                }, $available_teams ?? [])) ?>;
</script>


<?php $this->endSection(); ?>
<?php $this->section('scripts'); ?>
<script>
    var listBracket = <?php echo json_encode($listBracket) ?>;
    const HAS_PAYMENT = <?= $has_payment ? 'true' : 'false' ?>;
    const HAS_DOCUMENT = <?= $has_document ? 'true' : 'false' ?>;

    // Available teams data for selected team display
    var availableTeamsMap = {};
    <?php if (!empty($available_teams)): ?>
    <?php foreach ($available_teams as $at): ?>
    availableTeamsMap[<?= (int)$at['id'] ?>] = {
        name: <?= json_encode($at['name'] ?? $at['name_en'] ?? '') ?>,
        logo: <?= json_encode(!empty($at['logo']) ? base_url($at['logo']) : base_url('public/img/jootball_logo.png')) ?>
    };
    <?php endforeach; ?>
    <?php endif; ?>

    $(document).ready(function() {

        // Show selected team from sessionStorage + render multi-team buttons
        var _selectedTeamId = null;
        try {
            var savedTeamId = sessionStorage.getItem('selectedTeamId');
            if (savedTeamId && availableTeamsMap[parseInt(savedTeamId)]) {
                _selectedTeamId = parseInt(savedTeamId);
                var team = availableTeamsMap[_selectedTeamId];
                var banner = document.getElementById('selectedTeamBanner');
                if (banner) {
                    document.getElementById('selectedTeamName').textContent = team.name;
                    document.getElementById('selectedTeamLogo').src = team.logo;
                    document.getElementById('selectedTeamLogo').onerror = function() { this.src = '<?= base_url('public/img/jootball_logo.png') ?>'; };
                    banner.style.display = '';
                }
            }
        } catch(e) {}

        // Render multi-team action buttons based on selected team's status
        var multiDiv = document.getElementById('multiTeamActions');
        if (multiDiv && _selectedTeamId) {
            var teamData = teamRegistrations.find(function(t) { return t.id == _selectedTeamId; });
            var encId = '<?= bin2hex($encrypter->encrypt($news['tour_id'])) ?>';
            var status = teamData ? teamData.register_status : null;

            var html = '';
            if (status === null || status === undefined) {
                html = '<button type="button" class="btn btn-primary rounded-pill px-4 py-2" onclick="registerTournament(\'' + encId + '\', ' + _selectedTeamId + ')">'
                    + '<i class="ti ti-plus me-2"></i><?= lang('Common.register_competition') ?></button>';
            } else if (status === 0) {
                html = '<button type="button" class="btn btn-warning rounded-pill px-4" disabled>'
                    + '<i class="ti ti-clock me-2"></i><?= lang('Common.waiting_approval') ?></button>';
            } else if (status === 1) {
                html = '<button type="button" class="btn btn-success rounded-pill px-4" disabled>'
                    + '<i class="ti ti-check me-2"></i><?= lang('Common.joined_successfully') ?></button>';
            } else if (status === 3) {
                html = '<button type="button" class="btn btn-warning rounded-pill px-4" disabled>'
                    + '<i class="ti ti-currency-baht me-2"></i>รอชำระเงิน</button>';
            } else if (status === 2) {
                html = '<button type="button" class="btn btn-danger rounded-pill px-4" disabled>'
                    + '<i class="ti ti-x me-2"></i><?= lang('Common.registration_rejected') ?></button>';
            }

            // ปุ่มยกเลิกการสมัคร - แสดงเมื่อสถานะ รออนุมัติ(0), ถูกปฏิเสธ(2), รอชำระเงิน(3)
            if ([0, 2, 3].indexOf(status) !== -1) {
                html += ' <button type="button" class="btn btn-outline-danger rounded-pill px-4" onclick="cancelTournamentRegistration(\'' + encId + '\', ' + _selectedTeamId + ')">'
                    + '<i class="ti ti-x me-2"></i>ยกเลิกการสมัคร</button>';
            }

            // ปุ่มชำระเงิน/แนบเอกสาร - แสดงเมื่อสถานะ รออนุมัติ(0) หรือ รอชำระเงิน(3)
            if ([0, 3].indexOf(status) !== -1) {
                <?php if (!empty($news['enable_payment'])): ?>
                html += ' <button type="button" class="btn btn-primary rounded-pill px-4" onclick="openPaymentModal(\'' + encId + '\', ' + _selectedTeamId + ')">'
                    + '<i class="ti ti-receipt me-2"></i>ชำระเงินค่าสมัคร / แจ้งสลิป</button>';
                <?php elseif (!empty($news['enable_document'])): ?>
                html += ' <button type="button" class="btn btn-primary rounded-pill px-4" onclick="openPaymentModal(\'' + encId + '\', ' + _selectedTeamId + ')">'
                    + '<i class="ti ti-file-text me-2"></i>แนบเอกสารรับสมัคร</button>';
                <?php endif; ?>
            }

            multiDiv.innerHTML = html;
        } else if (multiDiv && !_selectedTeamId) {
            multiDiv.innerHTML = '<a href="<?= base_url('auth/tournament/list') ?>" class="btn btn-primary rounded-pill px-4 py-2">'
                + '<i class="ti ti-arrow-left me-2"></i>กลับไปเลือกทีม</a>';
        }

        // Bracket initialization (if needed)
        $('div#doubleElimination .demo').bracket({
            init: minimalData,
            teamWidth: 100,
            scoreWidth: 50,
            matchMargin: 60,
            roundMargin: 40,
        });
    });

    function parseResults(results) {
        return results.map(round =>
            round.map(match =>
                match.map(score => parseInt(score))
            )
        );
    }

    const minimalData = {
        teams: listBracket.teams,
        results: parseResults(listBracket.results)
    };

    function openPaymentModal(id, teamId) {
        document.getElementById('modalTourId').value = id;
        document.getElementById('modalTeamId').value = teamId || '';

        // หาข้อมูลทีมจาก teamRegistrations
        const teamData = teamRegistrations.find(t => t.id == teamId);

        // แสดงข้อมูลทีม
        const teamInfoEl = document.getElementById('paymentTeamInfo');
        if (teamData) {
            document.getElementById('paymentTeamLogo').src = teamData.logo;
            document.getElementById('paymentTeamName').textContent = teamData.name;
            document.getElementById('paymentTeamBadge').textContent = teamData.is_subteam ? 'ทีม' : 'ทีม';
            document.getElementById('paymentTeamBadge').className = teamData.is_subteam ? 'badge bg-secondary ms-2' : 'badge bg-primary ms-2';
            teamInfoEl.style.display = 'block';
        } else {
            teamInfoEl.style.display = 'none';
        }

        // แสดงประวัติสลิปของทีมนี้ (เฉพาะเมื่อเปิดระบบชำระเงิน)
        const slipContainer = document.getElementById('slipHistoryContainer');
        const slipContent = document.getElementById('slipHistoryContent');
        if (slipContent) slipContent.innerHTML = '';

        if (slipContainer && teamData && teamData.payment_slip) {
            let slips = [];
            try {
                slips = JSON.parse(teamData.payment_slip);
                if (!Array.isArray(slips)) {
                    slips = [teamData.payment_slip]; // ถ้าไม่ใช่ array ให้ใช้เป็น string เดียว
                }
            } catch (e) {
                slips = [teamData.payment_slip]; // ถ้า parse ไม่ได้ ให้ใช้เป็น string เดียว
            }

            if (slips.length > 0) {
                slips.forEach((slip, index) => {
                    let baseUrl = '<?= base_url() ?>';
                    if (!baseUrl.endsWith('/')) baseUrl += '/';
                    const slipUrl = slip.startsWith('http') ? slip : baseUrl + slip;
                    slipContent.innerHTML += `
                        <div class="col-6 mb-2">
                            <div class="text-center p-2 border rounded bg-light h-100">
                                <a href="${slipUrl}" target="_blank">
                                    <img src="${slipUrl}" class="img-fluid rounded shadow-sm mb-2" style="max-height: 120px;" alt="Slip ${index+1}">
                                </a>
                                <div class="small text-muted fw-bold">สลิปใบที่ ${index+1}</div>
                            </div>
                        </div>
                    `;
                });
                if (slipContainer) slipContainer.style.display = 'block';
            } else {
                if (slipContainer) slipContainer.style.display = 'none';
            }
        } else {
            if (slipContainer) slipContainer.style.display = 'none';
        }

        // แสดงเอกสารรับสมัครที่เคย upload ไปก่อนหน้า
        const docContainer = document.getElementById('currentDocumentContainer');
        const docLink = document.getElementById('currentDocumentLink');
        const docPreview = document.getElementById('currentDocumentPreview');
        const docInput = document.getElementById('registration_document');

        if (docContainer && teamData && teamData.registration_document) {
            docLink.href = teamData.registration_document;
            if (teamData.registration_document_ext === 'pdf') {
                docPreview.innerHTML = '<i class="ti ti-file-type-pdf text-danger fs-3"></i>';
            } else {
                docPreview.innerHTML = `<img src="${teamData.registration_document}" class="rounded border" style="width: 50px; height: 50px; object-fit: cover;">`;
            }
            docContainer.style.display = 'block';
            // ถ้ามีเอกสารเดิมแล้ว ไม่บังคับ upload ใหม่
            if (docInput) docInput.removeAttribute('required');
        } else {
            if (docContainer) docContainer.style.display = 'none';
        }

        // Reset file input
        if (docInput) docInput.value = '';

        var myModal = new bootstrap.Modal(document.getElementById('paymentModal'));
        myModal.show();
    }


    $(document).ready(function() {
        // Handle Payment Form Submit
        $('#paymentForm').on('submit', function(e) {
            e.preventDefault();

            // ตรวจสอบขนาดไฟล์ก่อนส่ง
            const maxSize = 10 * 1024 * 1024; // 10MB
            const fileInputs = this.querySelectorAll('input[type="file"]');
            for (const input of fileInputs) {
                if (input.files.length > 0 && input.files[0].size > maxSize) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'ไฟล์มีขนาดใหญ่เกินไป',
                        text: 'ไฟล์ต้องมีขนาดไม่เกิน 10MB (ไฟล์ที่เลือก: ' + (input.files[0].size / 1024 / 1024).toFixed(2) + 'MB)',
                        confirmButtonColor: '#0F2B55'
                    });
                    return;
                }
            }

            var formData = new FormData(this);

            Swal.fire({
                title: HAS_PAYMENT ? 'ยืนยันการแจ้งชำระเงิน?' : 'ยืนยันการส่งเอกสาร?',
                text: HAS_PAYMENT ? "ตรวจสอบความถูกต้องของสลิปแล้วใช่หรือไม่" : "ตรวจสอบเอกสารแล้วใช่หรือไม่",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'ยืนยัน',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: '<?= base_url('auth/tournament/register') ?>',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.status === 'success') {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สำเร็จ!',
                                    text: HAS_PAYMENT ? 'แจ้งชำระเงินเรียบร้อยแล้ว' : 'ส่งเอกสารเรียบร้อยแล้ว',
                                    showConfirmButton: false,
                                    timer: 1500
                                }).then(() => {
                                    window.location.reload();
                                });
                            } else {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'เกิดข้อผิดพลาด',
                                    text: response.msg
                                });
                            }
                        },
                        error: function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                            });
                        }
                    });
                }
            });
        });
    });

    function registerTournament(id, teamId) {
        Swal.fire({
            title: "<?= lang('Common.confirm_register') ?>",
            text: "<?= lang('Common.wait_approval') ?>",
            icon: "info",
            showCancelButton: true,
            confirmButtonColor: "#0F2B55",
            cancelButtonColor: "#d33",
            confirmButtonText: "<?= lang('Common.ok_button') ?>",
            cancelButtonText: "<?= lang('Common.cancel') ?>",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('auth/tournament/register') ?>",
                    data: {
                        id: id,
                        team_id: teamId
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            if (HAS_PAYMENT) {
                                // เปิดระบบชำระเงิน - ถามว่าจะชำระเลยไหม
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สมัครสำเร็จ!',
                                    text: 'ต้องการแจ้งชำระเงินเลยหรือไม่?',
                                    showCancelButton: true,
                                    confirmButtonText: 'ชำระเงิน',
                                    cancelButtonText: 'ไว้ภายหลัง',
                                    confirmButtonColor: '#ffc107',
                                    cancelButtonColor: '#6c757d'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        openPaymentModal(id, teamId);
                                        const paymentModalEl = document.getElementById('paymentModal');
                                        paymentModalEl.addEventListener('hidden.bs.modal', function(event) {
                                            window.location.reload();
                                        }, {
                                            once: true
                                        });
                                    } else {
                                        window.location.reload();
                                    }
                                });
                            } else if (HAS_DOCUMENT) {
                                // ปิดระบบชำระเงิน แต่เปิดแนบเอกสาร - ถามว่าจะแนบเลยไหม
                                Swal.fire({
                                    icon: 'success',
                                    title: 'สมัครสำเร็จ!',
                                    text: 'ต้องการแนบเอกสารรับสมัครเลยหรือไม่?',
                                    showCancelButton: true,
                                    confirmButtonText: 'แนบเอกสาร',
                                    cancelButtonText: 'ไว้ภายหลัง',
                                    confirmButtonColor: '#0F2B55',
                                    cancelButtonColor: '#6c757d'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        openPaymentModal(id, teamId);
                                        const paymentModalEl = document.getElementById('paymentModal');
                                        paymentModalEl.addEventListener('hidden.bs.modal', function(event) {
                                            window.location.reload();
                                        }, {
                                            once: true
                                        });
                                    } else {
                                        window.location.reload();
                                    }
                                });
                            } else {
                                // ปิดทั้งชำระเงินและเอกสาร
                                Swal.fire({
                                    position: "center",
                                    icon: res.status,
                                    title: res.msg,
                                    showConfirmButton: false,
                                    timer: 3000
                                }).then(() => {
                                    window.location.reload();
                                });
                            }
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'เกิดข้อผิดพลาด',
                                text: res.msg
                            });
                        }
                    }
                });
            }
        });
    }

    function cancelTournamentRegistration(tourId, teamId) {
        Swal.fire({
            title: "ยกเลิกการสมัคร?",
            text: "คุณต้องการยกเลิกการสมัครทัวร์นาเมนต์นี้หรือไม่?",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "ยืนยันยกเลิก",
            cancelButtonText: "ไม่ใช่",
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('auth/tournament/cancel') ?>",
                    data: {
                        id: tourId,
                        team_id: teamId
                    },
                    success: function(res) {
                        if (res.status === 'success') {
                            Swal.fire({
                                icon: 'success',
                                title: 'ยกเลิกสำเร็จ',
                                text: res.msg,
                                showConfirmButton: false,
                                timer: 2000
                            }).then(() => {
                                window.location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'ไม่สามารถยกเลิกได้',
                                text: res.msg
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'ไม่สามารถติดต่อเซิร์ฟเวอร์ได้'
                        });
                    }
                });
            }
        });
    }
</script>
<script>
    function showTeamDetails(teamId, tourId) {
        var modal = new bootstrap.Modal(document.getElementById('teamDetailsModal'));
        modal.show();


        // Reset and Show Loading
        document.getElementById('teamDetailsLoading').style.display = 'block';
        document.getElementById('teamDetailsContent').style.display = 'none';

        // Fetch Data
        $.ajax({
            url: '<?= base_url('auth/tournament/get-team-details') ?>',
            type: 'POST',
            data: {
                team_id: teamId,
                tour_id: tourId
            },
            success: function(response) {
                if (response.status === 'success') {
                    const team = response.team;
                    const players = response.players;

                    // Populate Team Info
                    document.getElementById('detailTeamName').textContent = team.name || '-';
                    document.getElementById('detailTeamNameEn').textContent = team.name_en ? '(' + team.name_en + ')' : '';
                    document.getElementById('detailTeamId').textContent = team.name_en || team.name; // User wanted "test-11111" in Pill, assuming it's name or maybe username? Using name/name_en for now. Image shows "test-11111"

                    // Logo
                    const logoUrl = team.logo ? '<?= base_url() ?>/' + team.logo : '<?= base_url('public/img/exam-logo.webp') ?>';
                    document.getElementById('detailTeamLogo').src = logoUrl;


                    // Contact Info
                    document.getElementById('detailCoachName').textContent = team.coach_name || '-';
                    document.getElementById('detailCoachPhone').textContent = team.coach_phone || '-';
                    document.getElementById('detailContactName').textContent = team.contact_name || '-';
                    document.getElementById('detailContactPhone').textContent = team.contact_phone || '-';
                    document.getElementById('detailContactLine').textContent = team.contact_line || '-';


                    // Populate Player Table
                    const tbody = document.getElementById('detailPlayerTableBody');
                    tbody.innerHTML = '';

                    if (players.length > 0) {
                        players.forEach((p, index) => {
                            const row = `
                                 <tr>
                                     <td class="ps-4 text-muted">${index + 1}</td>
                                     <td>
                                        <img src="${p.image_url}" class="rounded-circle border" style="width: 40px; height: 40px; object-fit: cover;">
                                     </td>
                                     <td class="fw-bold text-primary">${p.shirt_number || '-'}</td>
                                     <td>
                                         <div class="fw-bold">${p.full_name}</div>
                                     </td>
                                     <td>${p.nick_name || '-'}</td>
                                     <td>${p.birth_date_formatted}</td>
                                     <td class="text-center fw-bold">${p.age}</td>
                                 </tr>
                             `;
                            tbody.innerHTML += row;
                        });

                    } else {
                        tbody.innerHTML = '<tr><td colspan="7" class="text-center py-4 text-muted">ไม่มีข้อมูลนักเตะ</td></tr>';

                    }

                    // Populate Jerseys
                    const jerseys = response.jerseys || [];
                    const jerseySection = document.getElementById('jerseySection');
                    const jerseyContainer = document.getElementById('jerseyContainer');
                    jerseyContainer.innerHTML = '';

                    if (jerseys.length > 0) {
                        jerseySection.style.display = 'block';
                        const colClass = jerseys.length === 1 ? 'col-6 mx-auto' : (jerseys.length === 2 ? 'col-6' : 'col-4');
                        jerseys.forEach(j => {
                            jerseyContainer.innerHTML += `
                                <div class="${colClass} text-center">
                                    <div class="border rounded-3 p-3 bg-light h-100">
                                        <img src="${j.url}" class="img-fluid rounded" style="max-height: 200px; object-fit: contain;" alt="${j.label}">
                                        <div class="mt-2 fw-bold small" style="color: #16478d;">${j.label}</div>
                                    </div>
                                </div>
                            `;
                        });
                    } else {
                        jerseySection.style.display = 'none';
                    }

                    // Hide Loading, Show Content
                    document.getElementById('teamDetailsLoading').style.display = 'none';
                    document.getElementById('teamDetailsContent').style.display = 'block';

                } else {
                    // Error handling
                    alert('ไม่สามารถดึงข้อมูลได้: ' + (response.msg || 'Unknown error'));
                    modal.hide();
                }
            },
            error: function() {
                alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                modal.hide();
            }

        });
    }

    function checkFileSize(input) {
        const maxSize = 10 * 1024 * 1024; // 10MB

        if (input.files.length > 0) {
            const file = input.files[0];

            if (file.size > maxSize) {
                Swal.fire({
                    icon: 'warning',
                    title: 'ไฟล์มีขนาดใหญ่เกินไป',
                    text: 'ไฟล์ต้องมีขนาดไม่เกิน 10MB (ไฟล์ที่เลือก: ' + (file.size / 1024 / 1024).toFixed(2) + 'MB)',
                    confirmButtonColor: '#0F2B55'
                });
                input.value = ""; // เคลียร์ไฟล์ที่เลือก
            }
        }
    }
</script>
<?php $this->endSection(); ?>
```