<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= lang('Common.access_denied') ?> - JOOTBALL</title>
    <link rel="icon" href="<?= base_url('public/img/jootball_logo.png') ?>" type="image/icon type">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/styles.css') ?>">
</head>

<body>
    <!--  Body Wrapper -->
    <div class="page-wrapper" id="main-wrapper" data-layout="vertical" data-navbarbg="skin6" data-sidebartype="full"
        data-sidebar-position="fixed" data-header-position="fixed">
        <div
            class="position-relative overflow-hidden radial-gradient min-vh-100 d-flex align-items-center justify-content-center">
            <div class="d-flex align-items-center justify-content-center w-100">
                <div class="row justify-content-center w-100">
                    <div class="col-md-8 col-lg-6 col-xxl-4">
                        <div class="card mb-0">
                            <div class="card-body">
                                <div class="text-center">
                                    <img src="<?= base_url('public/img/jootball_logo.png') ?>" width="80" alt="JOOTBALL" class="mb-4" />
                                </div>

                                <div class="text-center mb-4">
                                    <div class="mb-3">
                                        <i class="ti ti-lock-access" style="font-size: 4rem; color: #dc3545;"></i>
                                    </div>
                                    <h2 class="fw-bolder text-danger"><?= lang('Common.access_denied') ?></h2>
                                    <p class="mb-4 text-muted"><?= lang('Common.access_denied_desc') ?></p>
                                </div>

                                <?php if (session()->getFlashdata('error')): ?>
                                    <div class="alert alert-danger" role="alert">
                                        <div class="d-flex align-items-center">
                                            <i class="ti ti-alert-circle me-2"></i>
                                            <?= session()->getFlashdata('error') ?>
                                        </div>
                                    </div>
                                <?php endif; ?>

                                <div class="d-flex align-items-center justify-content-between mb-4">
                                    <div class="row w-100">
                                        <div class="col-6">
                                            <a href="<?= base_url('admin/dashboard') ?>" class="btn btn-primary w-100 py-8 fs-4 mb-4 rounded-2">
                                                <i class="ti ti-home me-2"></i>
                                                <?= lang('Common.back_to_home') ?>
                                            </a>
                                        </div>
                                        <div class="col-6">
                                            <a href="<?= base_url('admin/logout') ?>" class="btn btn-outline-secondary w-100 py-8 fs-4 mb-4 rounded-2">
                                                <i class="ti ti-logout me-2"></i>
                                                <?= lang('Common.logout') ?>
                                            </a>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-center">
                                    <small class="text-muted">
                                        <?= lang('Common.access_denied_contact_admin') ?><br>
                                        <?= lang('Common.current_user') ?>: <strong><?= session()->get('username') ?></strong>
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="<?php echo base_url('public/js/bootstrap.bundle.min.js') ?>"></script>
</body>

</html>