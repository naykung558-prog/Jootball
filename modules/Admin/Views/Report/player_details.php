<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="bg-primary p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="text-white-50 text-uppercase fw-bold mb-1">Player Profile</h5>
                            <h2 class="text-white fw-bold mb-0"><?= $player['first_name'] . ' ' . $player['last_name'] ?></h2>
                            <div class="mt-2">
                                <span class="badge bg-white text-primary rounded-pill me-2">
                                    <i class="ti ti-shirt me-1"></i> <?= $player['team_name'] ?>
                                </span>
                                <span class="badge bg-warning text-dark rounded-pill">
                                    <i class="ti ti-ball-football me-1"></i> <?= $player['position_name'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="d-flex gap-3 align-items-center">
                            <a href="javascript:window.close();" class="btn btn-light btn-sm rounded-pill px-3 shadow-sm">
                                <i class="ti ti-x me-1"></i> <?= lang('Common.close_window') ?>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-4 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body text-center p-4">
                <div class="mb-4 position-relative d-inline-block">
                    <?php
                    $imgSrc = (!empty($player['image_path']) && file_exists('./' . $player['image_path']))
                        ? base_url($player['image_path'])
                        : base_url("public/img/football-player.webp");
                    ?>
                    <img src="<?= $imgSrc ?>" class="rounded-circle img-thumbnail shadow-sm" style="width: 180px; height: 180px; object-fit: cover;" alt="Player Image">
                    <span class="position-absolute bottom-0 end-0 bg-primary text-white rounded-circle d-flex align-items-center justify-content-center border border-white" style="width: 45px; height: 45px;">
                        <span class="fw-bold small"><?= $player['age'] ?></span>
                    </span>
                </div>

                <h4 class="fw-bold mb-1"><?= $player['first_name'] . ' ' . $player['last_name'] ?></h4>
                <p class="text-muted mb-4"><?= $player['team_name'] ?></p>

                <div class="row g-2 text-start">
                    <div class="col-6">
                        <div class="p-3 bg-primary-subtle rounded-3 h-100 text-center">
                            <h3 class="fw-bold text-primary mb-0"><?= ($player['average_score'] > 0) ? $player['average_score'] : 0 ?></h3>
                            <small class="text-muted d-block"><?= lang('Common.average_score') ?></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-success-subtle rounded-3 h-100 text-center">
                            <h3 class="fw-bold text-success mb-0"><?= $player['goals'] ?></h3>
                            <small class="text-muted d-block"><?= lang('Common.goals_label') ?></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-info-subtle rounded-3 h-100 text-center">
                            <h3 class="fw-bold text-info mb-0"><?= $player['assits'] ?></h3>
                            <small class="text-muted d-block"><?= lang('Common.assists') ?></small>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="p-3 bg-warning-subtle rounded-3 h-100 text-center">
                            <h3 class="fw-bold text-warning-emphasis mb-0"><?= $player['matches_played'] ?? 0 ?></h3>
                            <small class="text-muted d-block"><?= lang('Common.matches') ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-8 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold mb-0"><i class="ti ti-chart-bar me-2 text-primary"></i><?= lang('Common.overall_play_stats') ?></h5>
            </div>
            <div class="card-body p-4">
                <div id="state-chart" style="height: 400px;"></div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langNumberOfTimes = '<?= lang("Common.number_of_times") ?>';
    const langTimesUnit = '<?= lang("Common.times_unit") ?>';
    const langStatistics = '<?= lang("Common.statistics") ?>';

    let stateList = '<?= json_encode($state_list) ?>';
    const state_label = Object.values(JSON.parse(stateList));

    $(document).ready(function() {
        Highcharts.chart('state-chart', {
            chart: {
                type: 'bar',
                style: {
                    fontFamily: 'inherit'
                }
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: state_label,
                title: {
                    text: null
                },
                gridLineWidth: 1,
                lineWidth: 0
            },
            yAxis: {
                min: 0,
                title: {
                    text: langNumberOfTimes,
                    align: 'high'
                },
                labels: {
                    overflow: 'justify'
                },
                gridLineWidth: 0
            },
            tooltip: {
                valueSuffix: ' ' + langTimesUnit,
                borderRadius: 10,
                shadow: false,
                borderWidth: 0,
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                style: {
                    fontSize: '14px'
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 5,
                    dataLabels: {
                        enabled: true
                    },
                    groupPadding: 0.1
                }
            },
            credits: {
                enabled: false
            },
            series: [{
                name: langStatistics,
                data: [
                    <?= $player['states']['shots'] ?>,
                    <?= $player['states']['shots_on_target'] ?>,
                    <?= $player['states']['total_passes'] ?>,
                    <?= $player['states']['success_passed'] ?>,
                    <?= $player['states']['tackles'] ?>,
                    <?= $player['states']['interceptions'] ?>,
                    <?= $player['states']['fouls_committed'] ?>,
                    <?= $player['states']['offside'] ?>,
                    <?= $player['states']['saves'] ?>,
                ],
                color: '#5d87ff',
                showInLegend: false
            }]
        });
    })
</script>
<?php $this->endSection(); ?>