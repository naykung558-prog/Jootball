<?php
/**
 * Player Picker Card Component
 *
 * Variables (passed via view('...components/player_picker_card', $vars)):
 *   $p             array   - player row: id, first_name, last_name, nick_name, shirt_number, image_path, team_name
 *   $isSelected    bool    - whether this player is already selected (in team/group)
 *   $imgSrc        string  - resolved image URL
 *   $accentColor   string  - Bootstrap color: 'success' (green) | 'primary' (blue)
 *   $dataKey       string  - data-attribute key used by JS: 'in-team' | 'in-group'
 *   $showTeamBadge bool    - show team name badge below name
 *   $onClickFn     string  - (optional) JS function to call on click, default 'toggleItem'
 *   $canManage     bool    - (optional) if false, card is read-only (no click/pointer), default true
 */
$onClickFn  = $onClickFn ?? 'toggleItem';
$canManage  = $canManage ?? true;
?>
<div class="col-6 col-sm-4 col-md-3 col-lg-2 picker-item"
     data-id="<?= esc($p['id']) ?>"
     data-<?= esc($dataKey) ?>="<?= $isSelected ? '1' : '0' ?>"
     data-name="<?= esc(mb_strtolower(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? '') . ' ' . ($p['nick_name'] ?? ''))) ?>"
     <?= $canManage ? 'onclick="' . esc($onClickFn) . '(this)"' : '' ?>>
    <div class="card h-100 border-2 picker-card <?= $isSelected ? 'selected' : '' ?>"
         style="<?= $canManage ? 'cursor:pointer;' : '' ?> transition:all 0.15s; user-select:none;">
        <div class="card-body text-center p-2 position-relative">

            <!-- Checkmark (top-left) -->
            <div class="picker-check position-absolute" style="top:6px;left:6px;<?= $isSelected ? '' : 'display:none;' ?>">
                <i class="fas fa-check-circle text-<?= esc($accentColor) ?>" style="font-size:1rem;"></i>
            </div>

            <!-- Shirt number (top-right) -->
            <?php if (!empty($p['shirt_number'])): ?>
            <div class="position-absolute" style="top:4px;right:6px;">
                <span class="badge bg-<?= esc($accentColor) ?>" style="font-size:0.6rem;min-width:18px;"><?= esc($p['shirt_number']) ?></span>
            </div>
            <?php endif; ?>

            <!-- Avatar -->
            <img src="<?= esc($imgSrc) ?>" class="rounded-circle mb-1 border border-2"
                 style="width:56px;height:56px;object-fit:cover;"
                 onerror="this.src='<?= base_url('public/img/football-player.webp') ?>'">

            <!-- Name (first + last on same line) -->
            <div class="fw-bold text-dark lh-1 mt-1" style="font-size:0.78rem;">
                <?= esc(trim(($p['first_name'] ?? '') . ' ' . ($p['last_name'] ?? ''))) ?>
            </div>

            <!-- Nickname -->
            <?php if (!empty($p['nick_name'])): ?>
            <div class="text-muted lh-1" style="font-size:0.68rem;">(<?= esc($p['nick_name']) ?>)</div>
            <?php endif; ?>

            <!-- Team badge -->
            <?php if (!empty($showTeamBadge) && !empty($p['team_name'])): ?>
            <div class="mt-1">
                <span class="badge bg-light text-secondary border" style="font-size:0.62rem;"><?= esc($p['team_name']) ?></span>
            </div>
            <?php endif; ?>

        </div>
    </div>
</div>
