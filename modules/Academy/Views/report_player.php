<?php $this->extend('template/layout') ?>
<?php $this->section('content'); ?>

<?php
$p           = $player ?? [];
$academyId   = $academy_id ?? ($academy['id'] ?? 0);
$enrollment  = $enrollment ?? null;
$allAcademies= $all_academies ?? [];
$enrHistory  = $enrollment_history ?? [];
$attendance  = $attendance ?? ($stats['attendance'] ?? []);
$availYears  = $available_years ?? [];
$selYear     = $selected_year ?? 0;
$assessments = $assessments ?? ($stats['assessments'] ?? []);
$asmGroups   = $assessment_groups ?? [];
$physHistory = $physical_history ?? [];
$canEditAll      = $can_edit_all ?? false;
$canEditPhysical = $can_edit_physical ?? false;

$idpPlans          = $idp_plans ?? [];
$idpSummary        = $idp_summary ?? [];
$idpCompare        = $idp_compare ?? [];
$idpGoals          = $idp_goals ?? [];
$idpTemplates      = $idp_templates ?? [];
$selectedTemplateId= $selected_template_id ?? null;
$activePlan        = $idpSummary['active_plan'] ?? null;
$planSummary       = $idpSummary['plan_summary'] ?? [];
$planCount         = $idpSummary['plan_count'] ?? 0;
$selectedTplName   = '';
foreach ($idpTemplates as $tpl) {
    if ($tpl['template_id'] == $selectedTemplateId) { $selectedTplName = $tpl['title']; break; }
}

$thaiMonths = ['','ม.ค.','ก.พ.','มี.ค.','เม.ย.','พ.ค.','มิ.ย.','ก.ค.','ส.ค.','ก.ย.','ต.ค.','พ.ย.','ธ.ค.'];
function thaiDate($dateStr) {
    if (empty($dateStr)) return '-';
    $t = strtotime($dateStr);
    global $thaiMonths;
    return date('d/', $t) . @$thaiMonths[(int)date('m', $t)] . ' ' . (date('Y', $t) + 543);
}
function playerAge($birthDate) {
    if (empty($birthDate)) return '-';
    return floor((time() - strtotime($birthDate)) / 31536000) . ' ปี';
}
?>

<div class="container-fluid p-0">
    <?= $this->include('Modules\Academy\Views\subnav') ?>

    <!-- Player Profile Card (compact single-line) -->
    <div class="card shadow-sm border-0 rounded-4 mb-3">
        <div class="card-body py-2 px-3 d-flex align-items-center gap-3 flex-wrap">
            <img src="<?= !empty($p['image_path']) ? base_url($p['image_path']) : base_url('public/img/img_profile.jpg') ?>"
                 class="rounded-circle border border-2 flex-shrink-0" style="width:44px;height:44px;object-fit:cover;">
            <div class="d-flex align-items-center gap-2 flex-wrap flex-grow-1">
                <span class="fw-bold text-primary fs-6"><?= esc(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '')) ?></span>
                <?php if (!empty($p['nick_name'])): ?>
                <span class="text-muted small">"<?= esc($p['nick_name']) ?>"</span>
                <?php endif; ?>
                <?php if (!empty($enrollment['shirt_number'])): ?>
                <span class="badge bg-primary rounded-pill px-2"><i class="fas fa-tshirt me-1"></i>#<?= esc($enrollment['shirt_number']) ?></span>
                <?php endif; ?>
                <?php if (!empty($enrollment['team_name'])): ?>
                <span class="badge bg-light text-dark border"><i class="fas fa-shield-alt me-1 text-primary"></i><?= esc($enrollment['team_name']) ?></span>
                <?php endif; ?>
                <span class="badge bg-light text-dark border"><i class="fas fa-school me-1 text-success"></i><?= esc($academy['name'] ?? '') ?></span>
            </div>
            <a href="<?= base_url('academy/players/' . $academyId) ?>" class="btn btn-outline-secondary btn-sm rounded-pill ms-auto flex-shrink-0">
                <i class="fas fa-arrow-left me-1"></i> กลับ
            </a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="mb-0">
        <ul class="nav nav-pills gap-2 p-2 rounded-3" id="reportTabs" role="tablist"
            style="background:#f1f5f9; border:1px solid #e2e8f0;">
            <li class="nav-item" role="presentation">
                <button class="nav-link active fw-semibold px-4 py-2 rounded-3"
                        id="tab-profile" data-bs-toggle="tab" data-bs-target="#pane-profile" type="button"
                        style="border:1.5px solid transparent;">
                    <i class="fas fa-user me-2"></i>ข้อมูลส่วนตัว
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold px-4 py-2 rounded-3"
                        id="tab-attendance" data-bs-toggle="tab" data-bs-target="#pane-attendance" type="button"
                        style="border:1.5px solid transparent;">
                    <i class="fas fa-running me-2"></i>การฝึกซ้อม
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold px-4 py-2 rounded-3"
                        id="tab-idp" data-bs-toggle="tab" data-bs-target="#pane-idp" type="button"
                        style="border:1.5px solid transparent;">
                    <i class="fas fa-seedling me-2"></i>IDP
                    <?php if ($planCount > 0): ?><span class="badge rounded-pill ms-1" style="background:#7c3aed;"><?= $planCount ?></span><?php endif; ?>
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link fw-semibold px-4 py-2 rounded-3"
                        id="tab-history" data-bs-toggle="tab" data-bs-target="#pane-history" type="button"
                        style="border:1.5px solid transparent;">
                    <i class="fas fa-exchange-alt me-2"></i>ประวัติ Academy
                    <?php if (count($enrHistory) > 0): ?><span class="badge bg-secondary rounded-pill ms-1"><?= count($enrHistory) ?></span><?php endif; ?>
                </button>
            </li>
        </ul>
    </div>

    <div class="tab-content bg-white rounded-4 shadow-sm border mt-3 p-4" id="reportTabContent">

        <!-- ========== TAB 1: ข้อมูลส่วนตัว ========== -->
        <div class="tab-pane fade show active" id="pane-profile" role="tabpanel">

            <!-- Section Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-id-card me-2 text-primary"></i>ข้อมูลส่วนตัว</h5>
                <?php if ($canEditAll): ?>
                <a href="<?= base_url('academy/player-edit/'.$academyId.'/'.$p['id']) ?>" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="fas fa-edit me-1"></i> แก้ไขข้อมูล
                </a>
                <?php endif; ?>
            </div>

            <!-- Info Grid -->
            <?php
            $footMap  = ['left'=>'ซ้าย','right'=>'ขวา','both'=>'สองเท้า'];
            $bmiVal   = !empty($p['bmi']) ? (float)$p['bmi'] : null;
            $bmiColor = $bmiVal ? ($bmiVal < 18.5 ? '#0ea5e9' : ($bmiVal < 25 ? '#22c55e' : ($bmiVal < 30 ? '#f59e0b' : '#ef4444'))) : '#6b7280';
            $infoItems = [
                ['icon'=>'fas fa-birthday-cake', 'color'=>'#8b5cf6', 'bg'=>'#f5f3ff', 'label'=>'วันเกิด',          'value'=> !empty($p['birth_date']) ? thaiDate($p['birth_date']) : null],
                ['icon'=>'fas fa-user-clock',    'color'=>'#3b82f6', 'bg'=>'#eff6ff', 'label'=>'อายุ',             'value'=> !empty($p['birth_date']) ? playerAge($p['birth_date']) : null],
                ['icon'=>'fas fa-id-card',       'color'=>'#6b7280', 'bg'=>'#f9fafb', 'label'=>'เลขบัตรประชาชน', 'value'=> !empty($p['id_card']) ? esc($p['id_card']) : '-'],
                ['icon'=>'fas fa-flag',          'color'=>'#10b981', 'bg'=>'#ecfdf5', 'label'=>'สัญชาติ',         'value'=> !empty($p['nation']) ? esc($p['nation']) : '-'],
                ['icon'=>'fas fa-ruler-vertical','color'=>'#16478D', 'bg'=>'#eff6ff', 'label'=>'ส่วนสูง',         'value'=> !empty($p['height']) ? esc($p['height']).' ซม.' : '-'],
                ['icon'=>'fas fa-weight',        'color'=>'#f59e0b', 'bg'=>'#fffbeb', 'label'=>'น้ำหนัก',         'value'=> !empty($p['weight']) ? esc($p['weight']).' กก.' : '-'],
                ['icon'=>'fas fa-heartbeat',     'color'=>$bmiColor, 'bg'=>'#fff0f0', 'label'=>'BMI',             'value'=> $bmiVal ? number_format($bmiVal,1) : '-'],
                ['icon'=>'fas fa-shoe-prints',   'color'=>'#f97316', 'bg'=>'#fff7ed', 'label'=>'เท้าถนัด',        'value'=> !empty($p['dominant_foot']) ? ($footMap[$p['dominant_foot']] ?? esc($p['dominant_foot'])) : '-'],
            ];
            ?>
            <div class="row g-3 mb-5">
                <?php foreach ($infoItems as $item):
                    $isEmpty = ($item['value'] === '-' || $item['value'] === null);
                ?>
                <div class="col-6 col-sm-4 col-lg-3">
                    <div class="d-flex align-items-center gap-3 p-3 rounded-4 h-100"
                         style="background:<?= $isEmpty ? '#f8fafc' : $item['bg'] ?>; border:1px solid <?= $isEmpty ? '#e2e8f0' : $item['bg'] ?>;">
                        <div class="d-flex align-items-center justify-content-center rounded-3 flex-shrink-0"
                             style="width:40px;height:40px;background:<?= $isEmpty ? '#e2e8f020' : $item['color'].'20' ?>;">
                            <i class="<?= $item['icon'] ?>" style="color:<?= $isEmpty ? '#cbd5e1' : $item['color'] ?>;font-size:1rem;"></i>
                        </div>
                        <div class="min-w-0">
                            <div class="mb-1" style="font-size:0.72rem;letter-spacing:.03em;text-transform:uppercase;font-weight:600;color:<?= $isEmpty ? '#94a3b8' : '#64748b' ?>;"><?= $item['label'] ?></div>
                            <div style="font-size:0.95rem;line-height:1.2;font-weight:<?= $isEmpty ? '400' : '700' ?>;color:<?= $isEmpty ? '#94a3b8' : '#1e293b' ?>;"><?= $item['value'] ?? '-' ?></div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Physical History -->
            <div class="mt-2">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold mb-0 text-dark"><i class="fas fa-chart-line me-2 text-success"></i>ประวัติกายภาพ</h5>
                    <div class="d-flex gap-2">
                        <?php if ($canEditAll || $canEditPhysical): ?>
                        <button class="btn btn-success btn-sm rounded-pill" onclick="openAddPhysicalModal()">
                            <i class="fas fa-plus me-1"></i> เพิ่มข้อมูลกายภาพ
                        </button>
                        <?php endif; ?>
                        <a href="<?= base_url('academy/player-physical-history/'.$academyId.'/'.$p['id']) ?>" class="btn btn-outline-success btn-sm rounded-pill">
                            ดูทั้งหมด <i class="fas fa-arrow-right ms-1"></i>
                        </a>
                    </div>
                </div>
                <?php if (!empty($physHistory)): ?>
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr><th class="ps-3 py-2 small text-muted">วันที่</th><th class="py-2 small text-muted text-center">ส่วนสูง</th><th class="py-2 small text-muted text-center">น้ำหนัก</th><th class="py-2 small text-muted text-center">BMI</th><th class="py-2 small text-muted">บันทึกโดย</th></tr>
                        </thead>
                        <tbody>
                            <?php foreach (array_slice($physHistory,0,5) as $ph): ?>
                            <tr>
                                <td class="ps-3 py-2 small"><?= thaiDate($ph['recorded_at']??'') ?></td>
                                <td class="py-2 text-center small fw-bold"><?= $ph['height'] ? number_format($ph['height'],1).' ซม.' : '-' ?></td>
                                <td class="py-2 text-center small fw-bold"><?= $ph['weight'] ? number_format($ph['weight'],1).' กก.' : '-' ?></td>
                                <td class="py-2 text-center"><?php if ($ph['bmi']): $b=(float)$ph['bmi']; ?><span class="badge rounded-pill <?= $b<18.5?'bg-info':($b<25?'bg-success':($b<30?'bg-warning':'bg-danger')) ?>"><?= number_format($b,1) ?></span><?php else: ?>-<?php endif; ?></td>
                                <td class="py-2 small text-muted"><?= esc($ph['recorded_by_name']??'-') ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                <?php else: ?>
                <div class="text-center py-4 text-muted"><i class="fas fa-heartbeat fa-2x mb-2 opacity-25"></i><p class="small">ยังไม่มีประวัติกายภาพ</p></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- ========== TAB 2: การเข้าร่วม ========== -->
        <div class="tab-pane fade" id="pane-attendance" role="tabpanel">

            <!-- Year Filter -->
            <div class="d-flex align-items-center gap-3 mb-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-clipboard-check me-2 text-primary"></i>สรุปการเข้าร่วมฝึกซ้อม</h5>
                <div class="ms-auto d-flex align-items-center gap-2">
                    <label class="small text-muted fw-semibold">ปี:</label>
                    <select class="form-select form-select-sm" style="width:120px;" onchange="filterByYear(this.value)">
                        <option value="0" <?= $selYear == 0 ? 'selected' : '' ?>>ทั้งหมด</option>
                        <?php foreach ($availYears as $yr): ?>
                        <option value="<?= $yr ?>" <?= $selYear == $yr ? 'selected' : '' ?>><?= $yr + 543 ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <!-- Stat Cards -->
            <?php $atTotal = (int)($attendance['total'] ?? 0); $atRate = (float)($attendance['attendance_rate'] ?? 0); ?>
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-3 bg-light rounded-3 text-center h-100">
                        <div class="small text-muted fw-bold text-uppercase mb-1">ทั้งหมด</div>
                        <div class="fs-3 fw-bold text-dark"><?= $atTotal ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-3 rounded-3 text-center h-100" style="background:#ecfdf5;">
                        <div class="small text-muted fw-bold text-uppercase mb-1">มาเรียน</div>
                        <div class="fs-3 fw-bold text-success"><?= (int)($attendance['present'] ?? 0) ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-3 rounded-3 text-center h-100" style="background:#fffbeb;">
                        <div class="small text-muted fw-bold text-uppercase mb-1">มาสาย</div>
                        <div class="fs-3 fw-bold text-warning"><?= (int)($attendance['late'] ?? 0) ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-3 rounded-3 text-center h-100" style="background:#fef2f2;">
                        <div class="small text-muted fw-bold text-uppercase mb-1">ขาด</div>
                        <div class="fs-3 fw-bold text-danger"><?= (int)($attendance['absent'] ?? 0) ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-3 bg-light rounded-3 text-center h-100">
                        <div class="small text-muted fw-bold text-uppercase mb-1">ลา</div>
                        <div class="fs-3 fw-bold text-secondary"><?= (int)($attendance['excused'] ?? 0) ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-4 col-xl-2">
                    <div class="p-3 rounded-3 text-center h-100" style="background:#eff6ff;">
                        <div class="small text-muted fw-bold text-uppercase mb-1">อัตราเข้าร่วม</div>
                        <div class="fs-3 fw-bold text-primary"><?= number_format($atRate, 1) ?>%</div>
                    </div>
                </div>
            </div>

            <!-- Progress Bar -->
            <?php if ($atTotal > 0): ?>
            <div class="mb-2 d-flex justify-content-between"><span class="small fw-bold text-muted">อัตราการเข้าร่วม</span>
                <span class="small fw-bold <?= $atRate >= 80 ? 'text-success' : ($atRate >= 50 ? 'text-warning' : 'text-danger') ?>"><?= number_format($atRate,1) ?>%</span>
            </div>
            <div class="progress mb-4" style="height:14px; border-radius:8px;">
                <div class="progress-bar <?= $atRate >= 80 ? 'bg-success' : ($atRate >= 50 ? 'bg-warning' : 'bg-danger') ?>" style="width:<?= $atRate ?>%"></div>
            </div>
            <!-- Donut Chart -->
            <div class="row justify-content-center">
                <div class="col-12 col-md-5">
                    <canvas id="attendanceDonut" height="200"></canvas>
                </div>
            </div>
            <?php else: ?>
            <div class="text-center py-5 text-muted"><i class="fas fa-calendar-times fa-3x mb-3 opacity-25"></i><p>ยังไม่มีข้อมูลการเข้าร่วม<?= $selYear > 0 ? ' ปี ' . ($selYear + 543) : '' ?></p></div>
            <?php endif; ?>
        </div>

        <!-- ========== TAB 2: การทดสอบ ========== -->
        <div class="tab-pane fade" id="pane-assessment" role="tabpanel">
            <h5 class="fw-bold mb-4"><i class="fas fa-star-half-alt me-2 text-warning"></i>ประวัติการทดสอบ</h5>

            <?php if (!empty($asmGroups)): ?>
            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <?php
                $totalAsm = count($assessments);
                $scores = array_filter(array_map(fn($a) => (!empty($a['score']) && !empty($a['max_score'])) ? ($a['score']/$a['max_score']*100) : null, $assessments));
                $avgScore = count($scores) > 0 ? round(array_sum($scores)/count($scores), 1) : null;
                ?>
                <div class="col-6 col-md-3">
                    <div class="p-3 bg-light rounded-3 text-center">
                        <div class="small text-muted fw-bold text-uppercase mb-1">จำนวนครั้ง</div>
                        <div class="fs-3 fw-bold text-dark"><?= $totalAsm ?></div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center" style="background:#eff6ff;">
                        <div class="small text-muted fw-bold text-uppercase mb-1">ประเภท</div>
                        <div class="fs-3 fw-bold text-primary"><?= count($asmGroups) ?></div>
                    </div>
                </div>
                <?php if ($avgScore !== null): ?>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center" style="background:#ecfdf5;">
                        <div class="small text-muted fw-bold text-uppercase mb-1">คะแนนเฉลี่ย</div>
                        <div class="fs-3 fw-bold text-success"><?= $avgScore ?>%</div>
                    </div>
                </div>
                <?php endif; ?>
                <?php if (!empty($assessments)): ?>
                <div class="col-6 col-md-3">
                    <div class="p-3 rounded-3 text-center" style="background:#fff7ed;">
                        <div class="small text-muted fw-bold text-uppercase mb-1">ล่าสุด</div>
                        <div class="fw-bold text-warning small"><?= thaiDate($assessments[0]['date'] ?? '') ?></div>
                    </div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Grouped Summary (Collapsible) -->
            <div class="accordion" id="asmAccordion">
                <?php foreach ($asmGroups as $gi => $grp): ?>
                <div class="accordion-item border rounded-3 mb-2" style="border-color:#e5e7eb !important;">
                    <h2 class="accordion-header">
                        <button class="accordion-button <?= $gi > 0 ? 'collapsed' : '' ?> rounded-3 fw-semibold" type="button"
                                data-bs-toggle="collapse" data-bs-target="#asmGroup<?= $gi ?>">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <span><?= esc($grp['name']) ?></span>
                                <span class="badge bg-primary rounded-pill ms-1"><?= $grp['count'] ?> ครั้ง</span>
                                <?php if ($grp['avg'] > 0): ?>
                                <span class="badge <?= $grp['avg'] >= 70 ? 'bg-success' : ($grp['avg'] >= 50 ? 'bg-warning' : 'bg-danger') ?> rounded-pill">เฉลี่ย <?= $grp['avg'] ?>%</span>
                                <?php endif; ?>
                                <span class="text-muted small ms-auto me-3">ล่าสุด: <?= thaiDate($grp['items'][0]['date'] ?? '') ?></span>
                            </div>
                        </button>
                    </h2>
                    <div id="asmGroup<?= $gi ?>" class="accordion-collapse collapse <?= $gi == 0 ? 'show' : '' ?>">
                        <div class="accordion-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th class="ps-4 py-2 small text-muted">วันที่</th>
                                            <th class="py-2 small text-muted">เกณฑ์</th>
                                            <th class="py-2 small text-muted text-center">คะแนน</th>
                                            <th class="py-2 small text-muted text-center">เกรด</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($grp['items'] as $a): ?>
                                        <tr>
                                            <td class="ps-4 py-2 small"><?= thaiDate($a['date'] ?? '') ?></td>
                                            <td class="py-2 small text-muted"><?= esc($a['criteria'] ?? '-') ?></td>
                                            <td class="py-2 text-center small fw-bold">
                                                <?= esc($a['score'] ?? '-') ?>
                                                <?php if (!empty($a['max_score'])): ?><span class="text-muted fw-normal">/<?= esc($a['max_score']) ?></span><?php endif; ?>
                                            </td>
                                            <td class="py-2 text-center">
                                                <?php if (!empty($a['grade'])):
                                                    $g = strtoupper($a['grade']);
                                                    $gc = in_array($g,['A','A+','A-']) ? 'bg-success' : (in_array($g,['B','B+','B-']) ? 'bg-primary' : (in_array($g,['C','C+','C-']) ? 'bg-warning text-dark' : 'bg-danger'));
                                                ?>
                                                <span class="badge <?= $gc ?> rounded-pill"><?= esc($a['grade']) ?></span>
                                                <?php else: ?><span class="text-muted small">-</span><?php endif; ?>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

            <?php else: ?>
            <div class="text-center py-5 text-muted"><i class="fas fa-clipboard fa-3x mb-3 opacity-25"></i><p>ยังไม่มีประวัติการทดสอบ</p></div>
            <?php endif; ?>
        </div>

        <!-- ========== TAB 3: IDP ========== -->
        <div class="tab-pane fade" id="pane-idp" role="tabpanel">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="fw-bold mb-0"><i class="fas fa-seedling me-2" style="color:#7c3aed;"></i>Individual Development Plan</h5>
                <?php if ($planCount > 0): ?>
                <div class="d-flex gap-2">
                    <?php if (!empty($ai_analyzed_this_month)): ?>
                    <button class="btn btn-secondary btn-sm fw-bold" disabled><i class="fas fa-lock me-1"></i>วิเคราะห์แล้ว</button>
                    <?php else: ?>
                    <button id="btnAIAnalyze" class="btn btn-warning btn-sm fw-bold" onclick="runAIAnalysis()"><i class="fas fa-robot me-1"></i>AI วิเคราะห์</button>
                    <?php endif; ?>
                    <button class="btn btn-outline-secondary btn-sm" onclick="loadAIHistory()"><i class="fas fa-history me-1"></i>ประวัติ AI</button>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($planCount > 0): ?>
            <!-- Overview Cards -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3"><div class="p-3 rounded-3 text-center h-100" style="background:#f3e8ff;"><div class="small text-muted fw-bold text-uppercase mb-1">แผนทั้งหมด</div><div class="fs-3 fw-bold" style="color:#7c3aed;"><?= $planCount ?></div></div></div>
                <div class="col-6 col-md-3"><div class="p-3 rounded-3 text-center h-100" style="background:#ecfdf5;"><div class="small text-muted fw-bold text-uppercase mb-1">แผนใช้งาน</div><div class="fs-3 fw-bold text-success"><?= $activePlan ? 1 : 0 ?></div></div></div>
                <div class="col-6 col-md-3"><div class="p-3 rounded-3 text-center h-100" style="background:#fff7ed;"><div class="small text-muted fw-bold text-uppercase mb-1">ความก้าวหน้าเฉลี่ย</div><div class="fs-3 fw-bold text-warning"><?= number_format((float)($planSummary['avg_achievement'] ?? 0),1) ?>%</div></div></div>
                <div class="col-6 col-md-3"><div class="p-3 rounded-3 text-center h-100" style="background:#eff6ff;"><div class="small text-muted fw-bold text-uppercase mb-1">เป้าหมายรวม</div><div class="fs-3 fw-bold text-primary"><?= (int)($planSummary['goal_count'] ?? 0) ?></div></div></div>
            </div>

            <!-- Template selector -->
            <?php if (count($idpTemplates) > 1): ?>
            <div class="mb-4">
                <select id="idpTemplateSelect" class="form-select form-select-sm" style="max-width:320px;" onchange="if(this.value) loadIDPData(this.value)">
                    <?php foreach ($idpTemplates as $tpl): ?>
                    <option value="<?= esc($tpl['template_id']) ?>" <?= $tpl['template_id'] == $selectedTemplateId ? 'selected' : '' ?>><?= esc($tpl['title']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <!-- Radar Chart -->
            <div id="idpRadarWrap" style="<?= empty($idpCompare) ? 'display:none;' : '' ?>">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-chart-pie me-2" style="color:#7c3aed;"></i>เปรียบเทียบรายด้าน
                    <span id="idpTemplateBadge" class="badge rounded-pill ms-2" style="background:#7c3aed;font-size:0.7em;<?= empty($selectedTplName)?'display:none;':''?>"><?= esc($selectedTplName) ?></span>
                </h6>
                <div class="row justify-content-center mb-4"><div class="col-12 col-md-7 col-lg-5"><canvas id="idpRadarChart" height="300"></canvas></div></div>
                <div class="d-flex justify-content-center gap-4 mb-4">
                    <span class="d-flex align-items-center gap-1 small text-muted"><span style="display:inline-block;width:12px;height:12px;background:rgba(156,163,175,0.5);border-radius:50%;"></span> Baseline</span>
                    <span class="d-flex align-items-center gap-1 small text-muted"><span style="display:inline-block;width:12px;height:12px;background:rgba(124,58,237,0.7);border-radius:50%;"></span> ปัจจุบัน</span>
                </div>
            </div>

            <!-- Goals Table -->
            <div id="idpGoalsWrap" style="<?= empty($idpGoals) ? 'display:none;' : '' ?>">
                <h6 class="fw-bold text-dark mb-3"><i class="fas fa-bullseye me-2 text-danger"></i>เป้าหมาย IDP</h6>
                <div class="table-responsive mb-4">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light">
                            <tr>
                                <th class="ps-4 py-2 small text-muted">ด้าน</th>
                                <th class="py-2 small text-muted">เป้าหมาย</th>
                                <th class="py-2 small text-muted text-center">เริ่มต้น</th>
                                <th class="py-2 small text-muted text-center">เป้า</th>
                                <th class="py-2 small text-muted text-center">ปัจจุบัน</th>
                                <th class="py-2 small text-muted text-center pe-4" style="min-width:130px;">ความก้าวหน้า</th>
                            </tr>
                        </thead>
                        <tbody id="idpGoalsTbody">
                            <?php foreach ($idpGoals as $dimGroup): foreach ($dimGroup['goals'] as $goal):
                                $pct = min(100, max(0, (float)($goal['achievement_percent'] ?? 0)));
                                $bc  = $pct >= 80 ? 'bg-success' : ($pct >= 50 ? 'bg-warning' : 'bg-danger'); ?>
                            <tr>
                                <td class="ps-4 py-2"><span class="badge rounded-pill px-2 py-1" style="background:<?= esc($dimGroup['dimension_color']??'#6b7280') ?>;color:#fff;"><i class="<?= esc($dimGroup['dimension_icon']??'fas fa-circle') ?> me-1"></i><?= esc($dimGroup['dimension_name']??'-') ?></span></td>
                                <td class="py-2"><div class="fw-medium"><?= esc($goal['title']??'-') ?></div><?php if (!empty($goal['indicator_name'])): ?><small class="text-muted"><?= esc($goal['indicator_name']) ?></small><?php endif; ?></td>
                                <td class="py-2 text-center text-muted"><?= number_format((float)($goal['baseline_value']??0),1) ?></td>
                                <td class="py-2 text-center fw-bold"><?= number_format((float)($goal['target_value']??0),1) ?></td>
                                <td class="py-2 text-center"><span class="fw-bold" style="color:<?= esc($dimGroup['dimension_color']??'#6b7280') ?>;"><?= number_format((float)($goal['current_value']??0),1) ?></span></td>
                                <td class="py-2 pe-4"><div class="d-flex align-items-center gap-2"><div class="progress flex-grow-1" style="height:8px;"><div class="progress-bar <?= $bc ?>" style="width:<?= $pct ?>%"></div></div><span class="small fw-bold" style="min-width:36px;"><?= round($pct) ?>%</span></div></td>
                            </tr>
                            <?php endforeach; endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Plans History -->
            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-history me-2" style="color:#7c3aed;"></i>ประวัติแผน IDP</h6>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th class="ps-4 py-2 small text-muted">ชื่อแผน</th>
                            <th class="py-2 small text-muted">ซีซัน</th>
                            <th class="py-2 small text-muted">ระยะเวลา</th>
                            <th class="py-2 small text-muted">โค้ช</th>
                            <th class="py-2 small text-muted text-center pe-4">สถานะ</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($idpPlans as $plan):
                            $sc = $plan['status']==='active'?'bg-success':($plan['status']==='completed'?'bg-primary':($plan['status']==='cancelled'?'bg-danger':'bg-secondary'));
                            $sl = lang('Common.idp_status_'.$plan['status']); if ($sl==='Common.idp_status_'.$plan['status']) $sl=ucfirst($plan['status']); ?>
                        <tr>
                            <td class="ps-4 py-2 fw-medium"><?= esc($plan['title']??'-') ?></td>
                            <td class="py-2 text-muted small"><?= esc($plan['season']??'-') ?></td>
                            <td class="py-2 text-muted small"><?= thaiDate($plan['start_date']??'') ?> – <?= thaiDate($plan['end_date']??'') ?></td>
                            <td class="py-2 text-muted small"><?= esc($plan['coach_name']??'-') ?></td>
                            <td class="py-2 text-center pe-4"><span class="badge <?= $sc ?> rounded-pill"><?= $sl ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <?php else: ?>
            <div class="text-center py-5"><i class="fas fa-seedling fa-3x mb-3" style="color:#d8b4fe;"></i><h6 class="text-muted">ยังไม่มีแผน IDP</h6></div>
            <?php endif; ?>
        </div>

        <!-- ========== TAB 5: ประวัติ Academy ========== -->
        <div class="tab-pane fade" id="pane-history" role="tabpanel">
            <h5 class="fw-bold mb-4"><i class="fas fa-exchange-alt me-2 text-secondary"></i>ประวัติการเข้า-ออก Academy
                <span class="badge bg-secondary rounded-pill ms-2"><?= count($enrHistory) ?></span>
            </h5>

            <?php if (!empty($enrHistory)): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($enrHistory as $h): ?>
                <div class="card border-0 rounded-3 mb-3 <?= $h['is_active'] ? 'border-start border-success border-3' : 'border-start border-secondary border-3' ?>" style="border-left-width:4px !important; background:<?= $h['is_active']?'#f0fdf4':'#f9fafb' ?>;">
                    <div class="card-body py-3 px-4">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-<?= $h['is_active']?'success':'secondary' ?> text-white rounded d-flex align-items-center justify-content-center" style="width:44px;height:44px;flex-shrink:0;"><i class="fas fa-school"></i></div>
                            <div class="flex-grow-1">
                                <div class="d-flex align-items-center gap-2 mb-1">
                                    <span class="fw-bold"><?= esc($h['academy_name']??'-') ?></span>
                                    <?php if ($h['is_active']): ?><span class="badge bg-success rounded-pill" style="font-size:0.6rem;">ยังอยู่</span><?php else: ?><span class="badge bg-secondary rounded-pill" style="font-size:0.6rem;">ออกแล้ว</span><?php endif; ?>
                                    <?php if ((int)($h['academy_id']??0) === (int)$academyId): ?><span class="badge bg-primary rounded-pill" style="font-size:0.6rem;">Academy นี้</span><?php endif; ?>
                                </div>
                                <div class="d-flex flex-wrap gap-3">
                                    <small class="text-muted">ทีม: <span class="fw-semibold text-dark"><?= esc($h['team_name']??'-') ?></span></small>
                                    <small class="text-muted">เบอร์: <span class="fw-bold text-primary"><?= esc($h['shirt_number']??'-') ?></span></small>
                                    <?php if (!empty($h['enrolled_by_name'])): ?><small class="text-muted">เพิ่มโดย: <?= esc($h['enrolled_by_name']) ?></small><?php endif; ?>
                                </div>
                            </div>
                            <div class="text-end" style="flex-shrink:0;">
                                <div><small class="text-muted">เข้า</small><br><small class="fw-semibold text-success"><i class="fas fa-sign-in-alt me-1"></i><?= thaiDate($h['enrolled_at']??'') ?></small></div>
                                <?php if (!empty($h['left_at'])): ?>
                                <div class="mt-1"><small class="text-muted">ออก</small><br><small class="fw-semibold text-danger"><i class="fas fa-sign-out-alt me-1"></i><?= thaiDate($h['left_at']) ?></small></div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5 text-muted"><i class="fas fa-inbox fa-3x mb-3 opacity-25"></i><p>ยังไม่มีประวัติ</p></div>
            <?php endif; ?>
        </div>

    </div><!-- /tab-content -->
</div>

<!-- Modal: เพิ่มข้อมูลกายภาพ -->
<div class="modal fade" id="addPhysicalModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0 shadow">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fas fa-heartbeat me-2 text-success"></i>เพิ่มข้อมูลกายภาพ</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body pt-3">
                <div class="row g-3">
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-muted">ส่วนสูง (ซม.)</label>
                        <input type="number" step="0.1" class="form-control bg-light border-0 rounded-3" id="ph_height" placeholder="เช่น 160.5">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-muted">น้ำหนัก (กก.)</label>
                        <input type="number" step="0.1" class="form-control bg-light border-0 rounded-3" id="ph_weight" placeholder="เช่น 55.0">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-muted">BMI</label>
                        <input type="number" step="0.01" class="form-control bg-light border-0 rounded-3" id="ph_bmi" readonly placeholder="คำนวณอัตโนมัติ">
                    </div>
                    <div class="col-6">
                        <label class="form-label fw-semibold small text-muted">เท้าถนัด</label>
                        <select class="form-select bg-light border-0 rounded-3" id="ph_dominant_foot">
                            <option value="">-- เลือก --</option>
                            <option value="left" <?= ($p['dominant_foot']??'')==='left'?'selected':'' ?>>ซ้าย</option>
                            <option value="right" <?= ($p['dominant_foot']??'')==='right'?'selected':'' ?>>ขวา</option>
                            <option value="both" <?= ($p['dominant_foot']??'')==='both'?'selected':'' ?>>สองเท้า</option>
                        </select>
                    </div>
                </div>
                <div id="physicalSaveMsg" class="mt-3" style="display:none;"></div>
            </div>
            <div class="modal-footer border-0 pt-0">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success rounded-pill px-4 fw-semibold" onclick="savePhysicalFromModal()">
                    <i class="fas fa-save me-1"></i> บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Modals (AI) -->
<div class="modal fade" id="aiResultModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#f59e0b,#f97316);">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-robot me-2"></i><?= lang('Common.idp_ai_result') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <div id="aiResultLoading" class="text-center py-5" style="display:none;"><div class="spinner-border text-warning mb-3" style="width:3rem;height:3rem;" role="status"></div><h6 class="text-muted"><?= lang('Common.idp_ai_analyzing') ?></h6></div>
                <div id="aiResultContent" style="display:none;"></div>
                <div id="aiResultError" class="text-center py-5" style="display:none;"><i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i><h6 class="text-danger" id="aiResultErrorMsg"></h6></div>
                <div id="aiResultMeta" class="mt-3 pt-3 border-top small text-muted" style="display:none;"><span id="aiMetaModel"></span> &nbsp; <span id="aiMetaTokens"></span> &nbsp; <span id="aiMetaTime"></span></div>
            </div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.idp_ai_close') ?></button></div>
        </div>
    </div>
</div>
<div class="modal fade" id="aiHistoryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header" style="background:linear-gradient(135deg,#7c3aed,#a855f7);">
                <h5 class="modal-title text-white fw-bold"><i class="fas fa-history me-2"></i><?= lang('Common.idp_ai_history_title') ?></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4"><div id="aiHistoryLoading" class="text-center py-4" style="display:none;"><div class="spinner-border text-primary" role="status"></div></div><div id="aiHistoryList"></div></div>
            <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal"><?= lang('Common.idp_ai_close') ?></button></div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>
<?php $this->section('scripts'); ?>
<style>
/* Tab Pill Styles */
#reportTabs .nav-link {
    color: #475569;
    background: #ffffff;
    border: 1.5px solid #cbd5e1 !important;
    transition: all .18s ease;
}
#reportTabs .nav-link:hover:not(.active) {
    background: #e8f0fe;
    border-color: #93c5fd !important;
    color: #1d4ed8;
}
#reportTabs .nav-link.active {
    background: #16478D;
    color: #ffffff !important;
    border-color: #16478D !important;
    box-shadow: 0 2px 8px rgba(22,71,141,.25);
}
#reportTabs .nav-link.active .badge {
    background: rgba(255,255,255,.25) !important;
    color: #fff;
}
</style>
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Year filter
function filterByYear(val) {
    var url = new URL(window.location.href);
    if (val == '0') { url.searchParams.delete('year'); } else { url.searchParams.set('year', val); }
    window.location.href = url.toString();
}

// Attendance Donut
document.addEventListener('DOMContentLoaded', function() {
    var ctx = document.getElementById('attendanceDonut');
    if (ctx) {
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['มาเรียน','มาสาย','ขาด','ลา'],
                datasets: [{ data: [<?= (int)($attendance['present']??0) ?>,<?= (int)($attendance['late']??0) ?>,<?= (int)($attendance['absent']??0) ?>,<?= (int)($attendance['excused']??0) ?>],
                    backgroundColor: ['#22c55e','#f59e0b','#ef4444','#94a3b8'], borderWidth: 2, borderColor: '#fff' }]
            },
            options: { cutout: '65%', plugins: { legend: { position: 'bottom', labels: { font: { size: 12 } } } }, responsive: true, maintainAspectRatio: false }
        });
    }

    // IDP Radar
    var initialData = <?= json_encode($idpCompare ?? []) ?>;
    var initialPeerMax = <?= json_encode($idp_peer_max ?? (object)[]) ?>;
    if (initialData && initialData.length > 0) renderRadarChart(initialData, initialPeerMax);
});

// IDP
window.idpChart = null;
var idpAjaxUrl = '<?= base_url('academy/idp-radar-data/' . ($academy['id'] ?? '') . '/' . ($player['id'] ?? '')) ?>';

function renderRadarChart(compareData, peerMax) {
    var ctx = document.getElementById('idpRadarChart');
    if (!ctx) return;
    if (window.idpChart) { window.idpChart.destroy(); window.idpChart = null; }
    if (!compareData || compareData.length === 0) { document.getElementById('idpRadarWrap').style.display='none'; return; }
    peerMax = peerMax || {};
    var labels=[], initial=[], current=[], peer=[], peerHasData=false, initialHasData=false;
    compareData.forEach(function(d){
        labels.push(d.dimension_name);
        var bs = Number(d.baseline_score) || 0;
        if (bs > 0) initialHasData = true;
        initial.push(bs);
        current.push(d.current_score);
        var pm = peerMax[d.dimension_id] != null ? Number(peerMax[d.dimension_id]) : 0;
        if (pm > 0) peerHasData = true;
        peer.push(pm);
    });
    document.getElementById('idpRadarWrap').style.display='';
    var datasets = [];
    if (peerHasData) {
        datasets.push({ label:'Baseline', data:peer, backgroundColor:'rgba(108,117,125,0.22)', borderColor:'rgba(108,117,125,0.85)', borderWidth:2, borderDash:[5,5], pointRadius:3, pointBackgroundColor:'rgba(108,117,125,0.85)' });
    }
    if (initialHasData) {
        datasets.push({ label:'เริ่มต้น', data:initial, backgroundColor:'transparent', borderColor:'rgba(156,163,175,0.8)', borderWidth:2, pointRadius:3, fill:false });
    }
    datasets.push({ label:'ปัจจุบัน', data:current, backgroundColor:'transparent', borderColor:'rgba(124,58,237,0.9)', borderWidth:2, pointRadius:4, fill:false });
    window.idpChart = new Chart(ctx, { type:'radar', data:{ labels:labels, datasets:datasets }, options:{ responsive:true, maintainAspectRatio:false, scales:{ r:{ suggestedMin:0, suggestedMax:5, ticks:{ stepSize:1, backdropColor:'transparent', font:{size:10} }, pointLabels:{ font:{size:11,weight:'600'}, color:'#374151' }, grid:{color:'#e5e7eb'} } }, plugins:{legend:{display:true, position:'bottom', labels:{font:{size:10}, padding:8}}} } });
}

function renderGoalsTable(goals) {
    var $tbody = document.getElementById('idpGoalsTbody');
    if (!$tbody) return;
    if (!goals || goals.length === 0) { document.getElementById('idpGoalsWrap').style.display='none'; return; }
    var html='';
    goals.forEach(function(dg) {
        var color=dg.dimension_color||'#6b7280', icon=dg.dimension_icon||'fas fa-circle', name=dg.dimension_name||'-';
        (dg.goals||[]).forEach(function(g) {
            var pct=Math.min(100,Math.max(0,parseFloat(g.achievement_percent||0)));
            var bc=pct>=80?'bg-success':(pct>=50?'bg-warning':'bg-danger');
            html+='<tr><td class="ps-4 py-2"><span class="badge rounded-pill px-2 py-1" style="background:'+color+';color:#fff;"><i class="'+icon+' me-1"></i>'+name+'</span></td>'
                +'<td class="py-2"><div class="fw-medium">'+escHtml(g.title||'-')+'</div>'+(g.indicator_name?'<small class="text-muted">'+escHtml(g.indicator_name)+'</small>':'')+'</td>'
                +'<td class="py-2 text-center text-muted">'+parseFloat(g.baseline_value||0).toFixed(1)+'</td>'
                +'<td class="py-2 text-center fw-bold">'+parseFloat(g.target_value||0).toFixed(1)+'</td>'
                +'<td class="py-2 text-center"><span class="fw-bold" style="color:'+color+';">'+parseFloat(g.current_value||0).toFixed(1)+'</span></td>'
                +'<td class="py-2 pe-4"><div class="d-flex align-items-center gap-2"><div class="progress flex-grow-1" style="height:8px;"><div class="progress-bar '+bc+'" style="width:'+pct+'%"></div></div><span class="small fw-bold" style="min-width:36px;">'+Math.round(pct)+'%</span></div></td></tr>';
        });
    });
    $tbody.innerHTML=html;
    document.getElementById('idpGoalsWrap').style.display='';
}

function loadIDPData(templateId) {
    var sel = document.getElementById('idpTemplateSelect');
    if (sel) sel.disabled=true;
    fetch(idpAjaxUrl+'?template_id='+templateId)
        .then(r=>r.json()).then(res=>{
            if (res.status==='success') {
                renderRadarChart(res.data.compare, res.data.peer_max || {}); renderGoalsTable(res.data.goals);
                var badge=document.getElementById('idpTemplateBadge');
                if (badge) { badge.textContent=res.data.template_name||''; badge.style.display=res.data.template_name?'':'none'; }
            }
        }).finally(()=>{ if(sel) sel.disabled=false; });
}

// AI
var aiAnalyzeUrl='<?= base_url("academy/idp/ai-analyze") ?>';
var aiHistoryUrl='<?= base_url("academy/idp/ai-history/".($academy['id']??'')."/".($player['id']??'')) ?>';
var aiDetailUrl ='<?= base_url("academy/idp/ai-detail") ?>';
var aiPlayerId  =<?= (int)($player['id']??0) ?>;
var aiAcademyId =<?= (int)($academy['id']??0) ?>;

function lockAIButton() {
    var btn = document.getElementById('btnAIAnalyze');
    if (btn) {
        btn.disabled = true;
        btn.className = 'btn btn-secondary btn-sm fw-bold';
        btn.innerHTML = '<i class="fas fa-lock me-1"></i>วิเคราะห์แล้ว';
        btn.onclick = null;
    }
}

function runAIAnalysis(){
    var modal=new bootstrap.Modal(document.getElementById('aiResultModal')); modal.show();
    document.getElementById('aiResultLoading').style.display='';
    document.getElementById('aiResultContent').style.display='none';
    document.getElementById('aiResultError').style.display='none';
    document.getElementById('aiResultMeta').style.display='none';
    $.ajax({ url:aiAnalyzeUrl, type:'POST', dataType:'json', timeout:120000, data:{player_id:aiPlayerId,academy_id:aiAcademyId},
        success:function(res){
            document.getElementById('aiResultLoading').style.display='none';
            if(res.status==='success'){
                document.getElementById('aiResultContent').innerHTML=simpleMarkdownToHtml(res.data.result_text);
                document.getElementById('aiResultContent').style.display='';
                document.getElementById('aiMetaModel').textContent=res.data.model_used||'';
                document.getElementById('aiMetaTokens').textContent=(res.data.tokens_input||0)+' in / '+(res.data.tokens_output||0)+' out';
                document.getElementById('aiMetaTime').textContent=((res.data.processing_time_ms||0)/1000).toFixed(1)+'s';
                document.getElementById('aiResultMeta').style.display='';
                lockAIButton(); // ล็อคปุ่มทันทีหลังวิเคราะห์สำเร็จ
            } else {
                document.getElementById('aiResultErrorMsg').textContent=res.message||'Error';
                document.getElementById('aiResultError').style.display='';
            }
        },
        error:function(){ document.getElementById('aiResultLoading').style.display='none'; document.getElementById('aiResultErrorMsg').textContent='เกิดข้อผิดพลาด'; document.getElementById('aiResultError').style.display=''; }
    });
}

function loadAIHistory(){
    var modal=new bootstrap.Modal(document.getElementById('aiHistoryModal')); modal.show();
    document.getElementById('aiHistoryLoading').style.display='';
    document.getElementById('aiHistoryList').innerHTML='';
    $.ajax({ url:aiHistoryUrl, type:'GET', dataType:'json',
        success:function(res){
            document.getElementById('aiHistoryLoading').style.display='none';
            if(res.status==='success'&&res.data&&res.data.length>0){
                var html='<div class="d-flex flex-column gap-2">';
                res.data.forEach(function(item, idx){
                    var s=(item.result_summary||'').substring(0,160)+(item.result_summary&&item.result_summary.length>160?'...':'');
                    html+='<div onclick="viewAIDetail('+item.id+')" style="cursor:pointer;border:1.5px solid #e2e8f0;border-radius:12px;padding:14px 16px;background:#fff;transition:border-color .15s,background .15s;" '
                        +'onmouseover="this.style.borderColor=\'#a855f7\';this.style.background=\'#faf5ff\'" '
                        +'onmouseout="this.style.borderColor=\'#e2e8f0\';this.style.background=\'#fff\'">'
                        +'<div class="d-flex justify-content-between align-items-start gap-3">'
                        +'<div class="flex-grow-1">'
                        +'<div class="fw-bold mb-1" style="color:#1e293b;font-size:.92rem;">'
                        +'<i class="fas fa-robot me-2" style="color:#f59e0b;"></i>'+escHtml(item.plan_title||'N/A')+'</div>'
                        +'<p class="mb-0 text-muted" style="font-size:.82rem;line-height:1.5;">'+escHtml(s)+'</p>'
                        +'</div>'
                        +'<div class="text-end flex-shrink-0">'
                        +'<small class="text-muted d-block" style="font-size:.75rem;white-space:nowrap;">'+escHtml(item.created_at||'')+'</small>'
                        +'<span class="badge rounded-pill mt-1" style="background:#f3e8ff;color:#7c3aed;font-size:.7rem;">ดูรายละเอียด →</span>'
                        +'</div>'
                        +'</div>'
                        +'</div>';
                });
                html+='</div>';
                document.getElementById('aiHistoryList').innerHTML=html;
            } else {
                document.getElementById('aiHistoryList').innerHTML='<div class="text-center py-5 text-muted"><i class="fas fa-history fa-2x mb-2 opacity-25"></i><p>ยังไม่มีประวัติการวิเคราะห์</p></div>';
            }
        },
        error:function(){ document.getElementById('aiHistoryLoading').style.display='none'; document.getElementById('aiHistoryList').innerHTML='<div class="text-center py-4 text-danger">เกิดข้อผิดพลาด</div>'; }
    });
}

function viewAIDetail(id){
    var hm=bootstrap.Modal.getInstance(document.getElementById('aiHistoryModal')); if(hm) hm.hide();
    runAIAnalysis.detailId=id;
    var modal=new bootstrap.Modal(document.getElementById('aiResultModal')); modal.show();
    document.getElementById('aiResultLoading').style.display='';
    document.getElementById('aiResultContent').style.display='none';
    $.ajax({ url:aiDetailUrl+'/'+id, type:'GET', dataType:'json',
        success:function(res){ document.getElementById('aiResultLoading').style.display='none'; if(res.status==='success'&&res.data){ document.getElementById('aiResultContent').innerHTML=simpleMarkdownToHtml(res.data.result_text||''); document.getElementById('aiResultContent').style.display=''; }else{ document.getElementById('aiResultErrorMsg').textContent=res.message||'Error'; document.getElementById('aiResultError').style.display=''; } },
        error:function(){ document.getElementById('aiResultLoading').style.display='none'; }
    });
}

function simpleMarkdownToHtml(md){ if(!md) return ''; var h=escHtml(md); h=h.replace(/^### (.+)$/gm,'<h5 class="mt-3 mb-2 fw-bold">$1</h5>'); h=h.replace(/^## (.+)$/gm,'<h4 class="mt-4 mb-2 fw-bold" style="color:#7c3aed;">$1</h4>'); h=h.replace(/^# (.+)$/gm,'<h3 class="mt-4 mb-2 fw-bold">$1</h3>'); h=h.replace(/\*\*(.+?)\*\*/g,'<strong>$1</strong>'); h=h.replace(/\*(.+?)\*/g,'<em>$1</em>'); h=h.replace(/^- (.+)$/gm,'<li>$1</li>'); h=h.replace(/(<li>.*<\/li>\n?)+/g,'<ul class="mb-2">$&</ul>'); h=h.replace(/\n\n/g,'</p><p>'); h=h.replace(/\n/g,'<br>'); return '<div class="ai-result-content"><p>'+h+'</p></div>'; }
function escHtml(t){ var d=document.createElement('div'); d.appendChild(document.createTextNode(t)); return d.innerHTML; }

// Physical Modal
var physicalSaveUrl = '<?= base_url('academy/player-save/'.$academyId) ?>';
var physicalPlayerId = <?= (int)($p['id']??0) ?>;

function openAddPhysicalModal() {
    document.getElementById('ph_height').value = '<?= esc($p['height']??'') ?>';
    document.getElementById('ph_weight').value = '<?= esc($p['weight']??'') ?>';
    document.getElementById('ph_bmi').value    = '<?= esc($p['bmi']??'') ?>';
    document.getElementById('physicalSaveMsg').style.display = 'none';
    new bootstrap.Modal(document.getElementById('addPhysicalModal')).show();
}

document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('ph_height')?.addEventListener('input', calcPhBMI);
    document.getElementById('ph_weight')?.addEventListener('input', calcPhBMI);
});
function calcPhBMI() {
    var h = parseFloat(document.getElementById('ph_height').value) / 100;
    var w = parseFloat(document.getElementById('ph_weight').value);
    var bmiEl = document.getElementById('ph_bmi');
    if (h > 0 && w > 0) { bmiEl.value = (w / (h * h)).toFixed(2); } else { bmiEl.value = ''; }
}

function savePhysicalFromModal() {
    var data = {
        player_id: physicalPlayerId,
        section: 'B',
        height:        document.getElementById('ph_height').value || '',
        weight:        document.getElementById('ph_weight').value || '',
        bmi:           document.getElementById('ph_bmi').value || '',
        dominant_foot: document.getElementById('ph_dominant_foot').value || '',
    };
    fetch(physicalSaveUrl, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    })
    .then(r => r.json())
    .then(resp => {
        var msgEl = document.getElementById('physicalSaveMsg');
        msgEl.style.display = '';
        msgEl.className = 'alert ' + (resp.status === 'success' ? 'alert-success' : 'alert-danger') + ' rounded-3 py-2';
        msgEl.textContent = resp.message || (resp.status === 'success' ? 'บันทึกสำเร็จ' : 'เกิดข้อผิดพลาด');
        if (resp.status === 'success') {
            setTimeout(function() { location.reload(); }, 1200);
        }
    })
    .catch(function() {
        var msgEl = document.getElementById('physicalSaveMsg');
        msgEl.style.display = '';
        msgEl.className = 'alert alert-danger rounded-3 py-2';
        msgEl.textContent = 'เกิดข้อผิดพลาด';
    });
}
</script>
<?php $this->endSection(); ?>
