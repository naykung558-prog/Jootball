<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="card-title fw-bold mb-1"><?= lang('Common.match_list_scout') ?></h4>
                <p class="text-muted mb-0"><?= lang('Common.search_match_to_view_players') ?></p>
            </div>
        </div>

        <form id="searcMatchForm" action="" method="GET" autocomplete="off">
            <div class="row align-items-end">
                <div class="col-md-4">
                    <label for="match_date" class="form-label fw-bold"><?= lang('Common.match_date') ?></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light"><i class="ti ti-calendar"></i></span>
                        <input type="text" class="form-control date-picker" name="match_date" id="match_date"
                            value="<?= (!empty($search['match_date'])) ? date('d/m/Y', strtotime($search['match_date'])) : date('d/m/Y', strtotime(date('Y-m-d'))) ?>"
                            placeholder="<?= lang('Common.date_placeholder') ?>">
                    </div>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100 shadow-sm">
                        <i class="ti ti-search me-2"></i> <?= lang('Common.search') ?>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<div class="row" id="tournament_list">
    <?php if (!empty($matches)) { ?>
        <?php foreach ($matches as $key => $match) { ?>
            <div class="col-md-6 col-xl-4 mb-4">
                <div class="card shadow-sm h-100 border-0 rounded-3 hover-shadow transition-all">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <span class="badge bg-light text-dark border">
                                <i class="ti ti-calendar me-1"></i> <?= date('d/m/Y', strtotime('+543 years', strtotime($match['date']))) ?>
                            </span>
                            <span class="badge bg-light text-dark border">
                                <i class="ti ti-clock me-1"></i> <?= date('H:i', strtotime($match['time'])) ?> <?= lang('Common.time_suffix') ?>
                            </span>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="text-center" style="width: 40%;">
                                <?php if (file_exists('./' . $match['home_team_logo'])) { ?>
                                    <img class="img-fluid mb-2 rounded-circle shadow-sm" src="<?= base_url($match['home_team_logo']) ?>" alt="" style="width: 64px; height: 64px; object-fit: cover;">
                                <?php } else { ?>
                                    <img class="img-fluid mb-2 rounded-circle shadow-sm" src="<?= base_url('/public/img/jootball_logo.png') ?>" alt="" style="width: 64px; height: 64px; object-fit: cover;">
                                <?php } ?>
                                <div class="fw-bold text-truncate" title="<?= $match['home_team_name'] ?>"><?= ($match['home_team_name']) ? $match['home_team_name'] : '????' ?></div>
                            </div>

                            <div class="text-center" style="width: 20%;">
                                <?php if ($match['result'] == 0) { ?>
                                    <div class="fw-bold fs-5 text-muted">VS</div>
                                <?php } else { ?>
                                    <div class="fw-bold fs-4 text-primary"><?= $match['team1_score'] ?> - <?= $match['team2_score'] ?></div>
                                    <small class="text-muted"><?= lang('Common.match_finished') ?></small>
                                <?php } ?>
                            </div>

                            <div class="text-center" style="width: 40%;">
                                <?php if (file_exists('./' . $match['away_team_logo'])) { ?>
                                    <img class="img-fluid mb-2 rounded-circle shadow-sm" src="<?= base_url($match['away_team_logo']) ?>" alt="" style="width: 64px; height: 64px; object-fit: cover;">
                                <?php } else { ?>
                                    <img class="img-fluid mb-2 rounded-circle shadow-sm" src="<?= base_url('/public/img/jootball_logo.png') ?>" alt="" style="width: 64px; height: 64px; object-fit: cover;">
                                <?php } ?>
                                <div class="fw-bold text-truncate" title="<?= $match['away_team_name'] ?>"><?= ($match['away_team_name']) ? $match['away_team_name'] : '????' ?></div>
                            </div>
                        </div>

                        <div class="text-center mb-3">
                            <small class="text-muted"><i class="ti ti-map-pin me-1"></i> <?= lang('Common.stadium') ?> <?= $match['stadium'] ?></small>
                        </div>

                        <div class="d-grid">
                            <a href="<?= base_url('admin/scout/players-state-list/' . $match['tour_id'] . '/' . $match['match_id']) ?>" class="btn btn-outline-primary rounded-pill">
                                <i class="ti ti-users me-2"></i> <?= lang('Common.player_list') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php } ?>
    <?php } else { ?>
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3 text-center py-5">
                <div class="card-body">
                    <i class="ti ti-calendar-off fs-1 text-muted mb-3 d-block opacity-50"></i>
                    <h4 class="text-muted"><?= lang('Common.no_matches_on_date') ?></h4>
                    <p class="text-muted mb-0"><?= lang('Common.select_other_date') ?></p>
                </div>
            </div>
        </div>
    <?php } ?>
</div>

<style>
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 .5rem 1rem rgba(0,0,0,.15)!important;
    }
    .transition-all {
        transition: all 0.3s ease;
    }
</style>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    $(document).ready(function() {
        $('.date-picker').datepicker({
            format: "dd/mm/yyyy",
            autoclose: true,
            language: 'th-en',
            todayHighlight: true
        });
    });
</script>
<?php $this->endSection(); ?>