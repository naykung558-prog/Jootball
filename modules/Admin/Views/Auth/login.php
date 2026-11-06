<!doctype html>
<html lang="<?= get_current_language() ?>">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= lang('Common.login') ?> - JOOTBALL Admin</title>
    <link rel="icon" href="<?= base_url('public/img/jootball_logo.png') ?>" type="image/icon type">
    <link rel="stylesheet" href="<?php echo base_url('public/assets/css/styles.css') ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/tabler-icons.min.css">
    <style>
        body {
            background: #f0f2f5;
        }
        .login-card {
            border-radius: 16px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
            border: none;
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #06346D 0%, #042441 100%);
            padding: 2rem;
            text-align: center;
            color: white;
        }
        .login-body {
            padding: 2.5rem;
            background: white;
        }
        .form-control {
            border-radius: 8px;
            padding: 0.75rem 1rem;
            border: 1px solid #e2e8f0;
        }
        .form-control:focus {
            border-color: #06346D;
            box-shadow: 0 0 0 3px rgba(6, 52, 109, 0.1);
        }
        .btn-primary {
            background: #06346D;
            border: none;
            padding: 0.75rem;
            border-radius: 8px;
            font-weight: 600;
            transition: all 0.2s;
        }
        .btn-primary:hover {
            background: #042441;
            transform: translateY(-1px);
        }
        .input-group-text {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-right: none;
            color: #64748b;
        }
        .form-control {
            border-left: none;
        }
        .form-check-input:checked {
            background-color: #06346D;
            border-color: #06346D;
        }
    </style>
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
                        <div class="card login-card mb-0">
                            <div class="login-header">
                                <a href="" class="text-nowrap logo-img text-center d-block mb-3 w-100">
                                    <img src="<?= base_url('public/img/jootball_logo.png') ?>" width="100" alt="JOOTBALL" class="bg-white rounded-circle p-2 shadow-sm">
                                </a>
                                <h4 class="fw-bold mb-1 text-white">JOOTBALL ADMIN</h4>
                                <p class="mb-0 text-white-50"><?= lang('Common.football_management_system') ?></p>
                            </div>
                            <div class="login-body">
                                <div class="row">
                                    <div class="col-12">
                                        <?php if (session()->getFlashdata('msg')) { ?>
                                            <div class="alert alert-danger d-flex align-items-center" role="alert">
                                                <i class="ti ti-alert-circle me-2 fs-4"></i>
                                                <div><?= session()->getFlashdata('msg') ?></div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <form action="<?= base_url('admin/sign-in') ?>" method="POST">
                                    <div class="mb-3">
                                        <label for="username" class="form-label fw-semibold text-dark"><?= lang('Common.username') ?></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ti ti-user"></i></span>
                                            <input type="text" class="form-control" id="username" name="username" placeholder="<?= lang('Common.enter_username') ?>" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="mb-4">
                                        <label for="password" class="form-label fw-semibold text-dark"><?= lang('Common.password') ?></label>
                                        <div class="input-group">
                                            <span class="input-group-text"><i class="ti ti-lock"></i></span>
                                            <input type="password" class="form-control" id="password" name="password" placeholder="<?= lang('Common.enter_password') ?>" autocomplete="off" required>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="remember_me" name="remember_me" value="1">
                                            <label class="form-check-label text-dark" for="remember_me">
                                                <i class="ti ti-clock me-1"></i><?= lang('Common.remember_me') ?>
                                            </label>
                                        </div>
                                    </div>
                                    <button type="submit" class="btn btn-primary w-100 fs-4 mb-4">
                                        <i class="ti ti-login me-2"></i><?= lang('Common.login') ?>
                                    </button>
                                </form>
                                <div class="text-center pt-2 border-top">
                                    <a href="<?= base_url('admin/login') ?>?lang=th" class="btn btn-sm <?= get_current_language() === 'th' ? 'btn-dark' : 'btn-outline-secondary' ?> rounded-pill px-3 me-1">
                                        🇹🇭 ไทย
                                    </a>
                                    <a href="<?= base_url('admin/login') ?>?lang=en" class="btn btn-sm <?= get_current_language() === 'en' ? 'btn-dark' : 'btn-outline-secondary' ?> rounded-pill px-3">
                                        🇬🇧 EN
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php if (!empty($dev_members)): ?>
                        <div class="card mt-3 border-warning">
                            <div class="card-header bg-warning text-dark py-2 px-3 d-flex align-items-center">
                                <i class="ti ti-bug me-2"></i>
                                <strong>Dev Quick Login</strong>
                                <span class="badge bg-dark ms-auto">localhost only</span>
                            </div>
                            <div class="card-body p-2">
                                <?php foreach ($dev_members as $m): ?>
                                <button type="button" class="btn btn-sm btn-outline-dark w-100 text-start mb-1 dev-login-btn"
                                    data-username="<?= esc($m['username']) ?>"
                                    data-password="<?= esc($m['password']) ?>">
                                    <i class="ti ti-user-circle me-1"></i>
                                    <strong><?= esc($m['username']) ?></strong>
                                    <span class="text-muted ms-2" style="font-size:11px"><?= esc($m['password']) ?></span>
                                </button>
                                <?php endforeach; ?>
                            </div>
                        </div>
                        <?php endif; ?>
                        <div class="text-center mt-3 text-muted small">
                            &copy; <?= date('Y') ?> JOOTBALL. All rights reserved.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script src="<?php echo base_url('public/js/jquery-3.5.1.min.js') ?>"></script>
    <script src="<?php echo base_url('public/js/bootstrap.bundle.min.js') ?>"></script>
    <?php if (!empty($dev_members)): ?>
    <script>
    document.querySelectorAll('.dev-login-btn').forEach(function(btn) {
        btn.addEventListener('click', function() {
            document.getElementById('username').value = this.dataset.username;
            document.getElementById('password').value = this.dataset.password;
        });
    });
    </script>
    <?php endif; ?>
</body>

</html>