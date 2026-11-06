<?php
/**
 * Reusable player card for the team-roster board.
 * Vars (passed via $this->include): $p (player row), $side ('academy'|'team'), $canManage (bool)
 *
 * Defensive defaults — บาง CI4 version ไม่ extract var ทุกตัวให้ scope local
 */
$p         = $p         ?? [];
$side      = $side      ?? 'academy';
$canManage = $canManage ?? false;

$fullName = trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''));
$nickName = $p['nick_name'] ?? '';
$shirt    = $p['enrollment_shirt'] ?? $p['default_shirt'] ?? '';
$position = $p['position_name_th'] ?? $p['position_name_en'] ?? '';

$ageVal = null;
if (!empty($p['birth_date'])) {
    try {
        $birthDate = new \DateTime($p['birth_date']);
        $today = new \DateTime('today');
        $ageVal = (int) $birthDate->diff($today)->y;
    } catch (\Exception $e) { $ageVal = null; }
}

$img = !empty($p['image_path']) ? base_url($p['image_path']) : base_url('public/img/img_profile.jpg');
$searchStr = mb_strtolower($fullName . ' ' . $nickName . ' ' . $shirt . ' ' . $position);

$btnClass = $side === 'team' ? 'remove' : 'add';
$btnIcon  = $side === 'team' ? 'fa-arrow-left' : 'fa-arrow-right';
$btnTitle = $side === 'team' ? 'นำออกจากทีม' : 'เพิ่มเข้าทีม';

$otherTeams = $p['other_teams'] ?? [];

// flag: ฝั่ง Academy แต่ player อยู่ในทีมที่กำลังดูอยู่แล้ว → แสดงเป็น "อยู่ในทีมแล้ว"
$isInTeam = !empty($p['is_in_team']);
$inTeamLocked = $side === 'academy' && $isInTeam;
?>
<div class="player-card<?= $inTeamLocked ? ' is-locked' : '' ?>"
     data-player-id="<?= (int)$p['id'] ?>"
     data-side="<?= esc($side) ?>"
     data-in-team="<?= $isInTeam ? '1' : '0' ?>"
     data-search="<?= esc($searchStr) ?>">
    <img src="<?= $img ?>" class="pc-avatar" alt=""
         onerror="this.src='<?= base_url('public/img/img_profile.jpg') ?>'">
    <div class="pc-info">
        <div class="pc-name">
            <?= esc($fullName ?: '-') ?>
            <?php if ($nickName): ?>
                <span class="text-muted small"> (<?= esc($nickName) ?>)</span>
            <?php endif; ?>
        </div>
        <div class="pc-meta">
            <?php if ($shirt !== '' && $shirt !== null): ?>
                <span class="badge shirt"><i class="fas fa-tshirt me-1"></i><?= esc($shirt) ?></span>
            <?php endif; ?>
            <?php if ($position): ?>
                <span class="badge position"><?= esc($position) ?></span>
            <?php endif; ?>
            <?php if ($ageVal !== null): ?>
                <span class="badge age"><?= (int)$ageVal ?> ปี</span>
            <?php endif; ?>
            <?php foreach ($otherTeams as $t): ?>
                <span class="badge team-other" title="ทีมอื่นที่นักเตะสังกัด">
                    <i class="fas fa-shield-alt me-1"></i><?= esc($t['team_name']) ?>
                </span>
            <?php endforeach; ?>
        </div>
    </div>
    <?php if ($inTeamLocked): ?>
        <span class="pc-in-team" title="นักเตะคนนี้อยู่ในทีมนี้แล้ว">
            <i class="fas fa-check"></i>
        </span>
    <?php elseif (!empty($canManage)): ?>
        <button type="button"
                class="pc-action <?= $btnClass ?>"
                title="<?= esc($btnTitle) ?>"
                aria-label="<?= esc($btnTitle) ?>">
            <i class="fas <?= $btnIcon ?>"></i>
        </button>
    <?php endif; ?>
</div>
