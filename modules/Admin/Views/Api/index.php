<?= $this->extend('template/admin_layout') ?>

<?= $this->section('content') ?>
<div class="card bg-info-subtle shadow-none position-relative overflow-hidden mb-4">
    <div class="card-body px-4 py-3">
        <div class="row align-items-center">
            <div class="col-9">
                <h4 class="fw-semibold mb-8"><?= lang('Common.admin_api_management') ?></h4>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a class="text-muted text-decoration-none" href="<?= base_url('admin/dashboard') ?>">Admin</a>
                        </li>
                        <li class="breadcrumb-item" aria-current="page"><?= lang('Common.admin_api_management') ?></li>
                    </ol>
                </nav>
            </div>
            <div class="col-3">
                <div class="text-center mb-n5">
                    <img src="<?= base_url('public/assets/images/breadcrumb/ChatBc.png') ?>" alt="" class="img-fluid mb-n4">
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Stats Cards -->
<div class="row mb-4">
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="round-48 d-flex align-items-center justify-content-center rounded bg-primary-subtle">
                        <i class="ti ti-api fs-6 text-primary"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-0 fw-semibold">25</h3>
                        <span class="text-muted fs-3"><?= lang('Common.api_total_endpoints') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="round-48 d-flex align-items-center justify-content-center rounded bg-success-subtle">
                        <i class="ti ti-lock fs-6 text-success"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-0 fw-semibold">4</h3>
                        <span class="text-muted fs-3"><?= lang('Common.api_protected') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="round-48 d-flex align-items-center justify-content-center rounded bg-warning-subtle">
                        <i class="ti ti-lock-open fs-6 text-warning"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-0 fw-semibold">20</h3>
                        <span class="text-muted fs-3"><?= lang('Common.api_public') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-lg-3">
        <div class="card border-0 shadow-sm">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="round-48 d-flex align-items-center justify-content-center rounded bg-info-subtle">
                        <i class="ti ti-versions fs-6 text-info"></i>
                    </div>
                    <div class="ms-3">
                        <h3 class="mb-0 fw-semibold">v1</h3>
                        <span class="text-muted fs-3"><?= lang('Common.api_version') ?></span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- API Tester -->
<div class="card mb-4">
    <div class="card-header bg-dark text-white d-flex align-items-center">
        <i class="ti ti-terminal-2 fs-5 me-2"></i>
        <h5 class="card-title mb-0 text-white"><?= lang('Common.api_tester') ?></h5>
    </div>
    <div class="card-body">
        <div class="row g-2 mb-3">
            <div class="col-auto">
                <select id="apiMethod" class="form-select fw-semibold" style="width:100px">
                    <option value="GET" selected>GET</option>
                    <option value="POST">POST</option>
                </select>
            </div>
            <div class="col">
                <div class="input-group">
                    <span class="input-group-text bg-light"><?= rtrim(base_url(), '/') ?>/</span>
                    <input type="text" id="apiUrl" class="form-control" placeholder="api/v1/matches?date=2025-01-01" value="api/v1/competitions">
                </div>
            </div>
            <div class="col-auto">
                <button class="btn btn-primary px-4" id="btnSend" onclick="sendApiRequest()">
                    <i class="ti ti-send me-1"></i> <?= lang('Common.api_send') ?>
                </button>
            </div>
        </div>
        <!-- Token & Body -->
        <div class="row g-2 mb-3">
            <div class="col-md-6">
                <label class="form-label fs-2 text-muted"><?= lang('Common.api_token') ?></label>
                <div class="input-group">
                    <input type="text" id="apiToken" class="form-control form-control-sm" placeholder="Bearer token..." readonly>
                    <button class="btn btn-sm btn-outline-success" onclick="autoLogin()" title="<?= lang('Common.api_auto_login') ?>">
                        <i class="ti ti-key"></i> <?= lang('Common.api_auto_login') ?>
                    </button>
                </div>
            </div>
            <div class="col-md-6" id="bodySection" style="display:none">
                <label class="form-label fs-2 text-muted"><?= lang('Common.api_request_body') ?></label>
                <textarea id="apiBody" class="form-control form-control-sm" rows="2" placeholder='{"username":"...","password":"..."}'></textarea>
            </div>
        </div>
        <!-- Response -->
        <div id="apiResponseSection" style="display:none">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <div>
                    <span class="badge fs-2 me-2" id="responseStatus"></span>
                    <span class="text-muted fs-2" id="responseTime"></span>
                </div>
                <button class="btn btn-sm btn-outline-secondary" onclick="copyResponse()">
                    <i class="ti ti-copy"></i> <?= lang('Common.copy') ?>
                </button>
            </div>
            <pre class="bg-dark text-white p-3 rounded-3" style="max-height:400px;overflow:auto"><code id="apiResponse"></code></pre>
        </div>
    </div>
</div>

<!-- Endpoints Table -->
<div class="card">
    <div class="card-header">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="card-title mb-0"><?= lang('Common.api_endpoints_list') ?></h5>
            <div class="d-flex gap-2">
                <select id="filterGroup" class="form-select form-select-sm" style="width:auto" onchange="filterEndpoints()">
                    <option value=""><?= lang('Common.all') ?></option>
                    <option value="auth">Auth</option>
                    <option value="match">Matches</option>
                    <option value="competition">Competitions</option>
                    <option value="team">Teams</option>
                    <option value="player">Players</option>
                    <option value="search">Search</option>
                    <option value="post">Posts</option>
                </select>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle" id="endpointsTable">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3" style="width:50px">#</th>
                        <th><?= lang('Common.api_method_col') ?></th>
                        <th><?= lang('Common.api_endpoint_col') ?></th>
                        <th><?= lang('Common.api_description_col') ?></th>
                        <th><?= lang('Common.api_auth_col') ?></th>
                        <th style="width:80px"><?= lang('Common.api_action_col') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $endpoints = [
                        // Auth (Public)
                        ['group' => 'auth', 'method' => 'POST', 'path' => 'api/v1/auth/login', 'desc' => lang('Common.api_desc_login'), 'auth' => false],
                        ['group' => 'auth', 'method' => 'POST', 'path' => 'api/v1/auth/login-line', 'desc' => lang('Common.api_desc_login_line'), 'auth' => false],
                        ['group' => 'auth', 'method' => 'POST', 'path' => 'api/v1/auth/refresh', 'desc' => lang('Common.api_desc_refresh'), 'auth' => false],
                        // Auth (Protected)
                        ['group' => 'auth', 'method' => 'GET', 'path' => 'api/v1/profile', 'desc' => lang('Common.api_desc_profile'), 'auth' => true],
                        // Matches (Public)
                        ['group' => 'match', 'method' => 'GET', 'path' => 'api/v1/matches', 'desc' => lang('Common.api_desc_matches'), 'auth' => false, 'params' => '?date=YYYY-MM-DD'],
                        ['group' => 'match', 'method' => 'GET', 'path' => 'api/v1/matches/{id}', 'desc' => lang('Common.api_desc_match_detail'), 'auth' => false],
                        ['group' => 'match', 'method' => 'GET', 'path' => 'api/v1/matches/{id}/h2h', 'desc' => lang('Common.api_desc_match_h2h'), 'auth' => false],
                        ['group' => 'match', 'method' => 'GET', 'path' => 'api/v1/matches/{id}/statistics', 'desc' => lang('Common.api_desc_match_stats'), 'auth' => false],
                        // Competitions (Public)
                        ['group' => 'competition', 'method' => 'GET', 'path' => 'api/v1/competitions', 'desc' => lang('Common.api_desc_competitions'), 'auth' => false],
                        ['group' => 'competition', 'method' => 'GET', 'path' => 'api/v1/competitions?status=past', 'desc' => lang('Common.api_desc_competitions_past'), 'auth' => false],
                        ['group' => 'competition', 'method' => 'GET', 'path' => 'api/v1/competitions/{id}/standings', 'desc' => lang('Common.api_desc_standings'), 'auth' => false],
                        ['group' => 'competition', 'method' => 'GET', 'path' => 'api/v1/competitions/{id}/top-stats', 'desc' => lang('Common.api_desc_top_stats'), 'auth' => false],
                        ['group' => 'competition', 'method' => 'GET', 'path' => 'api/v1/competitions/{id}/highlights', 'desc' => lang('Common.api_desc_highlights'), 'auth' => false],
                        ['group' => 'competition', 'method' => 'GET', 'path' => 'api/v1/competitions/{id}/winners', 'desc' => lang('Common.api_desc_winners'), 'auth' => false],
                        ['group' => 'competition', 'method' => 'GET', 'path' => 'api/v1/competitions/{id}/matches', 'desc' => lang('Common.api_desc_comp_matches'), 'auth' => false, 'params' => '?type=next|last'],
                        // Teams (Public)
                        ['group' => 'team', 'method' => 'GET', 'path' => 'api/v1/teams', 'desc' => lang('Common.api_desc_teams'), 'auth' => false],
                        ['group' => 'team', 'method' => 'GET', 'path' => 'api/v1/teams/{id}/matches', 'desc' => lang('Common.api_desc_team_matches'), 'auth' => false, 'params' => '?type=next|last'],
                        ['group' => 'team', 'method' => 'GET', 'path' => 'api/v1/teams/{id}/squads', 'desc' => lang('Common.api_desc_team_squads'), 'auth' => false],
                        ['group' => 'team', 'method' => 'GET', 'path' => 'api/v1/teams/{id}/standings', 'desc' => lang('Common.api_desc_team_standings'), 'auth' => false],
                        ['group' => 'team', 'method' => 'GET', 'path' => 'api/v1/teams/{id}/transfers', 'desc' => lang('Common.api_desc_team_transfers'), 'auth' => false],
                        // Players (Public)
                        ['group' => 'player', 'method' => 'GET', 'path' => 'api/v1/players/{id}', 'desc' => lang('Common.api_desc_player_detail'), 'auth' => false],
                        ['group' => 'player', 'method' => 'GET', 'path' => 'api/v1/players/{id}/idp', 'desc' => lang('Common.api_desc_player_idp'), 'auth' => false, 'params' => '?theme=&lang='],
                        // Search (Public)
                        ['group' => 'search', 'method' => 'GET', 'path' => 'api/v1/search', 'desc' => lang('Common.api_desc_search'), 'auth' => false, 'params' => '?query=&filter='],
                        // Posts
                        ['group' => 'post', 'method' => 'GET', 'path' => 'api/v1/posts', 'desc' => lang('Common.api_desc_posts'), 'auth' => true, 'params' => '?page=1'],
                        ['group' => 'post', 'method' => 'GET', 'path' => 'api/v1/posts/{id}', 'desc' => lang('Common.api_desc_post_detail'), 'auth' => true],
                    ];
                    foreach ($endpoints as $i => $ep):
                        $methodColor = $ep['method'] === 'GET' ? 'success' : ($ep['method'] === 'POST' ? 'warning' : 'info');
                    ?>
                    <tr data-group="<?= $ep['group'] ?>">
                        <td class="ps-3 text-muted"><?= $i + 1 ?></td>
                        <td>
                            <span class="badge bg-<?= $methodColor ?>-subtle text-<?= $methodColor ?> fw-semibold px-2 py-1"><?= $ep['method'] ?></span>
                        </td>
                        <td>
                            <code class="text-dark">/<?= $ep['path'] ?></code>
                            <?php if (!empty($ep['params'])): ?>
                                <br><small class="text-muted"><?= $ep['params'] ?></small>
                            <?php endif; ?>
                        </td>
                        <td class="fs-3"><?= $ep['desc'] ?></td>
                        <td>
                            <?php if ($ep['auth']): ?>
                                <span class="badge bg-success-subtle text-success"><i class="ti ti-lock fs-3"></i> JWT</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning"><i class="ti ti-lock-open fs-3"></i> Public</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="tryEndpoint('<?= $ep['method'] ?>', '<?= $ep['path'] ?>')" title="Try it">
                                <i class="ti ti-player-play"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Legacy APIs -->
<div class="card mt-4">
    <div class="card-header">
        <h5 class="card-title mb-0"><?= lang('Common.api_legacy_endpoints') ?></h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-3" style="width:50px">#</th>
                        <th><?= lang('Common.api_method_col') ?></th>
                        <th><?= lang('Common.api_endpoint_col') ?></th>
                        <th><?= lang('Common.api_description_col') ?></th>
                        <th><?= lang('Common.api_auth_col') ?></th>
                        <th style="width:80px"><?= lang('Common.api_action_col') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $legacyEndpoints = [
                        ['method' => 'POST', 'path' => 'api/token', 'desc' => lang('Common.api_legacy_get_token'), 'auth' => false],
                        ['method' => 'GET', 'path' => 'api/actions', 'desc' => lang('Common.api_legacy_get_events'), 'auth' => true],
                        ['method' => 'GET', 'path' => 'api/match-result/{match_id}', 'desc' => lang('Common.api_legacy_match_result'), 'auth' => true],
                        ['method' => 'GET', 'path' => 'api/player-state/{player_id}', 'desc' => lang('Common.api_legacy_player_state'), 'auth' => true],
                        ['method' => 'GET', 'path' => 'api/end-match-result/{match_id}', 'desc' => lang('Common.api_legacy_end_match'), 'auth' => false],
                        ['method' => 'GET', 'path' => 'api/tournament/players/{tour_id}', 'desc' => lang('Common.api_get_players'), 'auth' => false],
                        ['method' => 'GET', 'path' => 'api/tournaments', 'desc' => lang('Common.api_get_tournaments'), 'auth' => false],
                        ['method' => 'GET', 'path' => 'api/all-data', 'desc' => lang('Common.api_get_all_data'), 'auth' => false],
                        ['method' => 'GET', 'path' => 'api/all-data/{ids}', 'desc' => lang('Common.api_legacy_all_data_filter'), 'auth' => false],
                    ];
                    foreach ($legacyEndpoints as $i => $ep):
                        $methodColor = $ep['method'] === 'GET' ? 'success' : 'warning';
                    ?>
                    <tr>
                        <td class="ps-3 text-muted"><?= $i + 1 ?></td>
                        <td><span class="badge bg-<?= $methodColor ?>-subtle text-<?= $methodColor ?> fw-semibold px-2 py-1"><?= $ep['method'] ?></span></td>
                        <td><code class="text-dark">/<?= $ep['path'] ?></code></td>
                        <td class="fs-3"><?= $ep['desc'] ?></td>
                        <td>
                            <?php if ($ep['auth']): ?>
                                <span class="badge bg-success-subtle text-success"><i class="ti ti-lock fs-3"></i> JWT</span>
                            <?php else: ?>
                                <span class="badge bg-warning-subtle text-warning"><i class="ti ti-lock-open fs-3"></i> Public</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <button class="btn btn-sm btn-outline-primary" onclick="tryEndpoint('<?= $ep['method'] ?>', '<?= $ep['path'] ?>')" title="Try it">
                                <i class="ti ti-player-play"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Clone from Production -->
<?php $cloneSafe = empty($cloneEnvErrors ?? []); ?>
<div class="card mt-4 border-<?= $cloneSafe ? 'danger' : 'dark' ?>">
    <div class="card-header bg-<?= $cloneSafe ? 'danger' : 'dark' ?> text-white d-flex align-items-center">
        <i class="ti ti-database-import fs-5 me-2"></i>
        <h5 class="card-title mb-0 text-white">Clone Data from Production</h5>
        <span class="badge bg-white text-<?= $cloneSafe ? 'danger' : 'dark' ?> ms-2"><?= $cloneSafe ? 'DANGER ZONE' : 'DISABLED — UNSAFE ENV' ?></span>
    </div>
    <div class="card-body">
        <!-- Environment Safety Check -->
        <div class="alert alert-<?= $cloneSafe ? 'success' : 'danger' ?> mb-3">
            <div class="d-flex align-items-start">
                <i class="ti ti-<?= $cloneSafe ? 'shield-check' : 'shield-x' ?> fs-5 me-2 mt-1"></i>
                <div class="flex-grow-1">
                    <strong><?= $cloneSafe ? '✓ ปลอดภัย — environment ผ่านทุก guard' : '✗ Environment ไม่ปลอดภัย — ปุ่มถูกปิด' ?></strong>
                    <div class="row mt-2 fs-3">
                        <div class="col-md-6"><strong>DB:</strong> <code><?= esc($cloneCurrentDb ?? '?') ?></code></div>
                        <div class="col-md-6"><strong>Host:</strong> <code><?= esc($cloneCurrentHost ?? '?') ?></code></div>
                        <div class="col-12 mt-1"><strong>Path:</strong> <code class="word-break-all"><?= esc($cloneCurrentPath ?? '?') ?></code></div>
                    </div>
                    <?php if (!$cloneSafe): ?>
                        <ul class="mb-0 mt-2 small">
                            <?php foreach (($cloneEnvErrors ?? []) as $err): ?>
                                <li><?= esc($err) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="alert alert-warning mb-3">
            <i class="ti ti-alert-triangle me-1"></i>
            <strong>คำเตือน:</strong> การ clone จะ <u>ทับข้อมูล dev (jootball_dev) ทั้งหมด</u> ด้วยข้อมูลจาก prod (jootball)
            <ul class="mb-0 mt-2">
                <li>DB: copy ทุก table จาก prod → dev (ยกเว้น 5 tables ด้านล่าง)</li>
                <li>Files: mirror โฟลเดอร์ <code>public/uploads/</code> (rsync --delete)</li>
                <li>เวลาที่ใช้: ขึ้นกับขนาด data — ปกติ 30 วิ - 2 นาที</li>
            </ul>
        </div>

        <div class="row g-3">
            <div class="col-md-6">
                <h6 class="fw-semibold mb-2"><i class="ti ti-arrow-right me-1"></i> Source (Production)</h6>
                <ul class="list-unstyled fs-3 mb-0">
                    <li><strong>DB:</strong> <code>jootball</code> @ <dev-server></dev></li>
                    <li><strong>Files:</strong> <code>/var/www/html/jootball.com/public/uploads/</code></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="fw-semibold mb-2"><i class="ti ti-arrow-left me-1"></i> Destination (Dev)</h6>
                <ul class="list-unstyled fs-3 mb-0">
                    <li><strong>DB:</strong> <code>jootball_dev</code> @ <dev-server></dev></li>
                    <li><strong>Files:</strong> <code>/var/www/html/dev.jigsawinnovation.com/jootball/public/uploads/</code></li>
                </ul>
            </div>
        </div>

        <hr>

        <div class="mb-3">
            <strong class="fs-3">Tables ที่ exclude (ไม่ทับของ dev):</strong>
            <div class="mt-1">
                <code class="me-1">admin_remember_tokens</code>
                <code class="me-1">password_reset_tokens</code>
                <code class="me-1">coach_invitations</code>
                <code class="me-1">tournament_duplicate_check_link</code>
                <code class="me-1">temp_tour_match</code>
            </div>
        </div>

        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#cloneProdModal" <?= $cloneSafe ? '' : 'disabled' ?>>
            <i class="ti ti-database-import me-1"></i> Clone from Production
        </button>
        <?php if (!$cloneSafe): ?>
            <span class="text-danger ms-2 fs-3"><i class="ti ti-lock"></i> ปุ่มถูก disable เพราะ environment ไม่ปลอดภัย (ดูด้านบน)</span>
        <?php endif; ?>
    </div>
</div>

<!-- Clone Confirmation Modal -->
<div class="modal fade" id="cloneProdModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="ti ti-alert-triangle me-1"></i> ยืนยันการ Clone</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="cloneIdleSection">
                    <p class="mb-3">คุณกำลังจะ <strong class="text-danger">ทับข้อมูล dev ทั้งหมด</strong> ด้วยข้อมูลจาก prod</p>
                    <p class="mb-2">พิมพ์ <code class="bg-dark text-white px-2 py-1 rounded">CLONE</code> เพื่อยืนยัน:</p>
                    <input type="text" id="cloneConfirmInput" class="form-control" placeholder="พิมพ์ CLONE ตรงนี้" autocomplete="off">
                </div>
                <div id="cloneRunningSection" style="display:none">
                    <div class="text-center py-4">
                        <div class="spinner-border text-danger mb-3" style="width:3rem;height:3rem"></div>
                        <h5 class="mb-1">กำลัง Clone จาก Production...</h5>
                        <p class="text-muted mb-0 fs-3">อย่าปิดหน้านี้ — ใช้เวลาประมาณ 30 วิ - 2 นาที</p>
                    </div>
                </div>
                <div id="cloneResultSection" style="display:none"></div>
            </div>
            <div class="modal-footer" id="cloneIdleFooter">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="cloneConfirmBtn" disabled onclick="executeClone()">
                    <i class="ti ti-database-import me-1"></i> Clone เลย
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
const baseUrl = '<?= rtrim(base_url(), '/') ?>/';
let currentToken = '';

// Toggle body section for POST
document.getElementById('apiMethod').addEventListener('change', function() {
    document.getElementById('bodySection').style.display = this.value === 'POST' ? 'block' : 'none';
});

// Auto login to get token - try multiple accounts
const autoLoginAccounts = [
    {username: 'mediaglo', password: 'mediaglo'},
    {username: 'coach1', password: 'password'},
    {username: 'scout1', password: 'scout1'},
];

async function autoLogin() {
    const btn = event.target.closest('button');
    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin"></i>';

    for (const account of autoLoginAccounts) {
        try {
            const r = await fetch(baseUrl + 'api/v1/auth/login', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify(account)
            });
            const data = await r.json();
            if (data.status === 'success' && data.data && data.data.accessToken) {
                currentToken = data.data.accessToken;
                const userType = data.data.user?.type || '';
                document.getElementById('apiToken').value = currentToken.substring(0, 50) + '...';
                Swal.fire({icon: 'success', title: 'Token received!', text: account.username + ' (' + userType + ')', toast: true, position: 'top-end', showConfirmButton: false, timer: 2000});
                btn.disabled = false;
                btn.innerHTML = '<i class="ti ti-key"></i> <?= lang('Common.api_auto_login') ?>';
                return;
            }
        } catch(e) { /* try next */ }
    }

    Swal.fire({icon: 'error', title: 'Login failed', text: 'No valid account found', toast: true, position: 'top-end', showConfirmButton: false, timer: 3000});
    btn.disabled = false;
    btn.innerHTML = '<i class="ti ti-key"></i> <?= lang('Common.api_auto_login') ?>';
}

// Send API request
function sendApiRequest() {
    const method = document.getElementById('apiMethod').value;
    const url = document.getElementById('apiUrl').value.replace(/^\/+/, '');
    const body = document.getElementById('apiBody')?.value || '';
    const btn = document.getElementById('btnSend');

    btn.disabled = true;
    btn.innerHTML = '<i class="ti ti-loader ti-spin me-1"></i> Sending...';

    const headers = {'Content-Type': 'application/json'};
    if (currentToken) {
        headers['Authorization'] = 'Bearer ' + currentToken;
    }

    const options = {method: method, headers: headers};
    if (method === 'POST' && body) {
        options.body = body;
    }

    const startTime = performance.now();

    fetch(baseUrl + url, options)
    .then(r => {
        const elapsed = Math.round(performance.now() - startTime);
        const statusEl = document.getElementById('responseStatus');
        statusEl.textContent = r.status + ' ' + r.statusText;
        statusEl.className = 'badge fs-2 me-2 ' + (r.ok ? 'bg-success' : 'bg-danger');
        document.getElementById('responseTime').textContent = elapsed + 'ms';
        return r.text();
    })
    .then(text => {
        try {
            const json = JSON.parse(text);
            document.getElementById('apiResponse').textContent = JSON.stringify(json, null, 2);
        } catch(e) {
            document.getElementById('apiResponse').textContent = text;
        }
        document.getElementById('apiResponseSection').style.display = 'block';
    })
    .catch(err => {
        document.getElementById('responseStatus').textContent = 'Error';
        document.getElementById('responseStatus').className = 'badge fs-2 me-2 bg-danger';
        document.getElementById('responseTime').textContent = '';
        document.getElementById('apiResponse').textContent = err.message;
        document.getElementById('apiResponseSection').style.display = 'block';
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = '<i class="ti ti-send me-1"></i> <?= lang('Common.api_send') ?>';
    });
}

// Try endpoint from table
function tryEndpoint(method, path) {
    document.getElementById('apiMethod').value = method;
    document.getElementById('apiUrl').value = path.replace(/^\/+/, '');
    document.getElementById('bodySection').style.display = method === 'POST' ? 'block' : 'none';

    // Replace {id} placeholder with prompt
    if (path.includes('{id}')) {
        const id = prompt('Enter ID:', '1');
        if (!id) return;
        document.getElementById('apiUrl').value = path.replace('{id}', id).replace(/^\/+/, '');
    }

    // Scroll to tester
    document.querySelector('.card-header.bg-dark').scrollIntoView({behavior: 'smooth', block: 'start'});

    // Auto send for GET
    if (method === 'GET') {
        setTimeout(() => sendApiRequest(), 300);
    }
}

// Filter endpoints by group
function filterEndpoints() {
    const group = document.getElementById('filterGroup').value;
    const rows = document.querySelectorAll('#endpointsTable tbody tr');
    rows.forEach(row => {
        row.style.display = (!group || row.dataset.group === group) ? '' : 'none';
    });
}

// Copy response
function copyResponse() {
    const text = document.getElementById('apiResponse').textContent;
    navigator.clipboard.writeText(text).then(() => {
        Swal.fire({icon: 'success', title: 'Copied!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500});
    });
}

// Copy endpoint URL
function copyEndpoint(url) {
    navigator.clipboard.writeText(url).then(() => {
        Swal.fire({icon: 'success', title: 'Copied!', toast: true, position: 'top-end', showConfirmButton: false, timer: 1500});
    });
}

// ====== Clone from Production ======
$(document).ready(function() {
    // Move modal to <body> to escape container CSS transforms (same pattern as userModal)
    $('#cloneProdModal').appendTo('body');

    const input = document.getElementById('cloneConfirmInput');
    const btn   = document.getElementById('cloneConfirmBtn');
    if (!input || !btn) return;

    input.addEventListener('input', function() {
        btn.disabled = (this.value.trim() !== 'CLONE');
    });

    // reset modal state on open
    const modalEl = document.getElementById('cloneProdModal');
    modalEl.addEventListener('show.bs.modal', function() {
        input.value = '';
        btn.disabled = true;
        document.getElementById('cloneIdleSection').style.display = 'block';
        document.getElementById('cloneIdleFooter').style.display = 'flex';
        document.getElementById('cloneRunningSection').style.display = 'none';
        document.getElementById('cloneResultSection').style.display = 'none';
        document.getElementById('cloneResultSection').innerHTML = '';
    });
});

function executeClone() {
    const input = document.getElementById('cloneConfirmInput');
    if (input.value.trim() !== 'CLONE') return;

    document.getElementById('cloneIdleSection').style.display = 'none';
    document.getElementById('cloneIdleFooter').style.display = 'none';
    document.getElementById('cloneRunningSection').style.display = 'block';

    const fd = new FormData();
    fd.append('confirm', 'CLONE');
    fd.append('<?= csrf_token() ?>', '<?= csrf_hash() ?>');

    fetch(baseUrl + 'admin/api-manager/clone-prod', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        body: fd
    })
    .then(r => r.json().then(d => ({status: r.status, data: d})))
    .then(({status, data}) => {
        document.getElementById('cloneRunningSection').style.display = 'none';
        renderCloneResult(status, data);
    })
    .catch(err => {
        document.getElementById('cloneRunningSection').style.display = 'none';
        renderCloneResult(0, {success: false, error: err.message});
    });
}

function renderCloneResult(httpStatus, data) {
    const sec = document.getElementById('cloneResultSection');
    const ok  = data.success === true;
    const titleClass = ok ? 'alert-success' : 'alert-danger';
    const titleIcon  = ok ? 'ti-circle-check' : 'ti-circle-x';
    const titleText  = ok ? 'Clone สำเร็จ!' : 'Clone ล้มเหลว';

    let html = `<div class="alert ${titleClass} mb-3"><i class="ti ${titleIcon} me-1"></i> <strong>${titleText}</strong></div>`;

    if (ok) {
        html += `<div class="row g-3">`;
        html += `<div class="col-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="mb-2"><i class="ti ti-database me-1"></i> Database</h6>
                        <div class="fs-3"><strong>${data.database.dump_human}</strong> dumped</div>
                        <div class="fs-3 text-muted">${data.database.elapsed_sec}s</div>
                    </div>
                 </div>`;
        html += `<div class="col-6">
                    <div class="border rounded p-3 h-100">
                        <h6 class="mb-2"><i class="ti ti-folder me-1"></i> Files</h6>
                        <div class="fs-3"><strong>${data.files.size_human}</strong> synced</div>
                        <div class="fs-3 text-muted">${data.files.elapsed_sec}s</div>
                    </div>
                 </div>`;
        html += `</div>`;
        html += `<div class="text-center text-muted fs-3 mt-3">รวมเวลา: ${data.elapsed_sec} วินาที</div>`;
    } else {
        html += `<pre class="bg-dark text-white p-3 rounded fs-3" style="max-height:300px;overflow:auto">${JSON.stringify(data, null, 2)}</pre>`;
    }

    html += `<div class="text-end mt-3">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
             </div>`;

    sec.innerHTML = html;
    sec.style.display = 'block';
}
</script>
<?= $this->endSection() ?>
