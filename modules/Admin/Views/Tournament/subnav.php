<?php
$url = uri_string();
$sub_nav_active = explode('/', $url);
$tour_id = isset($sub_nav_active[3]) ? $sub_nav_active[3] : '';
?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: 16px; overflow: hidden; background: #fff;">
    <div class="card-body p-0">
        <div class="d-flex justify-content-around align-items-center bg-white p-3">
<?php if(!in_array('reporter',session()->get('user_roles'))){ ?>
            <!-- Tab 1: Info -->
            <a href="<?= base_url('admin/tournament/tournament-manage/' . $tour_id) ?>"
               class="d-flex align-items-center text-decoration-none px-4 py-2 position-relative subnav-link <?= ($sub_nav_active[0] == 'admin' && ($sub_nav_active[1] =='' || $sub_nav_active[2] == 'tournament-manage') ) ? 'active' : '' ?>"
               style="transition: all 0.3s ease;">
                <i class="ti ti-trophy fs-4 me-2"></i>
                <span class="fw-bold" style="font-size: 1rem;"><?= lang('Common.competition_info') ?></span>
                <?php if ($sub_nav_active[0] == 'admin' && ($sub_nav_active[1] =='' || $sub_nav_active[2] == 'tournament-manage') ): ?>
                    <div class="active-indicator"></div>
                <?php endif; ?>
            </a>

            <!-- Tab 2: Teams -->
            <a href="<?= base_url('admin/tournament/tournament-team/' . $tour_id) ?>"
               class="d-flex align-items-center text-decoration-none px-4 py-2 position-relative subnav-link <?= ($sub_nav_active[2] == 'tournament-team' || $sub_nav_active[2] == 'tournament-team-manage') ? 'active' : '' ?>"
               onclick="<?php if(@$mode=='add'){?> alert('<?= lang('Common.please_save_competition_first') ?>'); return false;<?php }?>"
               style="transition: all 0.3s ease;">
                <i class="ti ti-users fs-4 me-2"></i>
                <span class="fw-bold" style="font-size: 1rem;"><?= lang('Common.participating_teams') ?></span>
                <?php if ($sub_nav_active[2] == 'tournament-team' || $sub_nav_active[2] == 'tournament-team-manage'): ?>
                    <div class="active-indicator"></div>
                <?php endif; ?>
            </a>

            <!-- Tab 3: Match & Setup -->
            <a href="<?= base_url('admin/tournament/match-setup/' . $tour_id) ?>"
               class="d-flex align-items-center text-decoration-none px-4 py-2 position-relative subnav-link <?= ($sub_nav_active[2] == 'match-setup' || $sub_nav_active[2] == 'tournament-match-result-manage' || $sub_nav_active[2] == 'match-setup') ? 'active' : '' ?>"
               onclick="<?php if(@$mode=='add'){?> alert('<?= lang('Common.please_save_competition_first') ?>'); return false;<?php }?>"
               style="transition: all 0.3s ease;">
                <i class="ti ti-scoreboard fs-4 me-2"></i>
                <span class="fw-bold" style="font-size: 1rem;"><?= lang('Common.match_and_results') ?></span>
                <?php if ($sub_nav_active[2] == 'match-setup' || $sub_nav_active[2] == 'tournament-match-result-manage' || $sub_nav_active[2] == 'match-setup'): ?>
                    <div class="active-indicator"></div>
                <?php endif; ?>
            </a>
<?php }?>
            <!-- Tab 4: Score Management -->
            <a href="<?= base_url('admin/tournament/score-management/' . $tour_id) ?>"
               class="d-flex align-items-center text-decoration-none px-4 py-2 position-relative subnav-link <?= ($sub_nav_active[2] == 'score-management') ? 'active' : '' ?>"
               onclick="<?php if(@$mode=='add'){?> alert('<?= lang('Common.please_save_competition_first') ?>'); return false;<?php }?>"
               style="transition: all 0.3s ease;">
                <i class="ti ti-calculator fs-4 me-2"></i>
                <span class="fw-bold" style="font-size: 1rem;"><?= lang('Common.score_management') ?></span>
                <?php if ($sub_nav_active[2] == 'score-management'): ?>
                    <div class="active-indicator"></div>
                <?php endif; ?>
            </a>

<?php if(!in_array('reporter',session()->get('user_roles'))){ ?>
            <!-- Tab 5: Result -->
            <a href="<?= base_url('admin/tournament/tournament-program/' . $tour_id) ?>"
               class="d-flex align-items-center text-decoration-none px-4 py-2 position-relative subnav-link <?= ($sub_nav_active[2] == 'tournament-program') ? 'active' : '' ?>"
               onclick="<?php if(@$mode=='add'){?> alert('<?= lang('Common.please_save_competition_first') ?>'); return false;<?php }?>"
               style="transition: all 0.3s ease;">
                <i class="ti ti-report fs-4 me-2"></i>
                <span class="fw-bold" style="font-size: 1rem;"><?= lang('Common.report_results') ?></span>
                <?php if ($sub_nav_active[2] == 'tournament-program'): ?>
                    <div class="active-indicator"></div>
                <?php endif; ?>
            </a>
<?php }?>

        </div>
    </div>
</div>

<style>
    /* Default State */
    .subnav-link {
        color: #98a6ad !important;
        opacity: 0.8;
    }

    .subnav-link:hover {
        color: #16478D !important;
        opacity: 1;
        background-color: rgba(22, 71, 141, 0.03);
        border-radius: 8px;
    }

    /* Active State */
    .subnav-link.active {
        color: #16478D !important;
        opacity: 1;
    }

    /* Active Indicator Line */
    .active-indicator {
        position: absolute;
        bottom: -16px; /* Matches padding of container */
        left: 0;
        width: 100%;
        height: 4px;
        background: #16478D;
        border-top-left-radius: 4px;
        border-top-right-radius: 4px;
        box-shadow: 0 -2px 6px rgba(22, 71, 141, 0.2);
    }
</style>