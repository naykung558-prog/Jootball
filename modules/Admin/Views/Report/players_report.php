<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card shadow-sm border-0 overflow-hidden">
            <div class="card-body p-0">
                <div class="bg-primary p-4 text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h4 class="text-white fw-bold mb-1">Football Player Analytics</h4>
                            <p class="text-white-50 mb-0"><?= lang('Common.player_analytics_desc') ?></p>
                        </div>
                        <div class="d-none d-md-block">
                            <i class="ti ti-chart-bar fs-1 text-white opacity-50"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm h-100 bg-primary-subtle">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-primary fw-semibold"><?= lang('Common.total_players') ?></p>
                        <h2 class="fw-bold text-primary mb-0"><?= $count_players ?></h2>
                    </div>
                    <div class="bg-white rounded-circle p-3 text-primary shadow-sm">
                        <i class="ti ti-users fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3 mb-md-0">
        <div class="card border-0 shadow-sm h-100 bg-warning-subtle">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-warning-emphasis fw-semibold"><?= lang('Common.popular_position') ?></p>
                        <h2 class="fw-bold text-warning-emphasis mb-0"><?= $popular_position['name_th'] ?></h2>
                    </div>
                    <div class="bg-white rounded-circle p-3 text-warning shadow-sm">
                        <i class="ti ti-trophy fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100 bg-danger-subtle">
            <div class="card-body p-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div>
                        <p class="mb-1 text-danger fw-semibold"><?= lang('Common.average_age') ?></p>
                        <h2 class="fw-bold text-danger mb-0"><?= $average_age ?> <span class="fs-6 fw-normal text-muted"><?= lang('Common.years_unit') ?></span></h2>
                    </div>
                    <div class="bg-white rounded-circle p-3 text-danger shadow-sm">
                        <i class="ti ti-cake fs-3"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-lg-6 mb-4 mb-lg-0">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold mb-0"><i class="ti ti-chart-pie me-2 text-primary"></i><?= lang('Common.players_by_position') ?></h5>
            </div>
            <div class="card-body">
                <div id="position_value_chart" style="height: 350px;"></div>
            </div>
        </div>
    </div>
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-3">
                <h5 class="card-title fw-bold mb-0"><i class="ti ti-star me-2 text-warning"></i><?= lang('Common.top5_best_avg_players') ?></h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3"><?= lang('Common.name') ?></th>
                                <th class="text-center py-3"><?= lang('Common.matches_count') ?></th>
                                <th class="text-center py-3 pe-4"><?= lang('Common.average_score') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($most_best_avg_player as $player) { ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="me-3">
                                                <?php
                                                $img = '';
                                                if (!empty($player['image_path'])) {
                                                    if (file_exists('./' . $player['image_path'])) {
                                                        $img = '<img src="' . base_url($player['image_path']) . '" class="rounded-circle shadow-sm" width="40" height="40" style="object-fit: cover;">';
                                                    } else {
                                                        $img = '<img src="' . base_url("public/img/football-player.webp") . '" class="rounded-circle shadow-sm" width="40" height="40" style="object-fit: cover;">';
                                                    }
                                                } else {
                                                    $img = '<img src="' . base_url("public/img/football-player.webp") . '" class="rounded-circle shadow-sm" width="40" height="40" style="object-fit: cover;">';
                                                }
                                                echo $img;
                                                ?>
                                            </div>
                                            <div>
                                                <a href="<?= base_url('admin/report/player-details/' . $player['id']) ?>" target="_blank" class="text-decoration-none text-dark fw-bold">
                                                    <?= $player['first_name'] . ' ' . $player['last_name'] ?>
                                                </a>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-light text-dark border"><?= $player['matches_played'] ?></span></td>
                                    <td class="text-center pe-4"><span class="badge bg-success"><?= $player['average_score_scaled'] ?></span></td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                <h5 class="card-title fw-bold mb-0"><i class="ti ti-list me-2 text-primary"></i><?= lang('Common.all_players_list') ?></h5>
                <div style="min-width: 250px;">
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="ti ti-filter"></i></span>
                        <select id="player_position_id" class="form-select border-start-0">
                            <option value=""><?= lang('Common.all_positions') ?></option>
                            <?php foreach ($player_position as $position) { ?>
                                <option value="<?= $position['id'] ?>"><?= $position['name_th'] ?></option>
                            <?php } ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table id="table" class="table table-hover align-middle mb-0" style="width: 100%;">
                        <thead class="bg-light">
                            <tr>
                                <th class="py-3 ps-4"><?= lang('Common.player') ?></th>
                                <th class="py-3 text-center"><?= lang('Common.team_label') ?></th>
                                <th class="py-3 text-center"><?= lang('Common.position') ?></th>
                                <th class="py-3 text-center pe-4"><?= lang('Common.age') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langYearsUnit = '<?= lang("Common.years_unit") ?>';
    const langPlayerPositions = '<?= lang("Common.player_positions") ?>';
    const langCountPersons = '<?= lang("Common.count_persons") ?>';
    const langPersonUnit = '<?= lang("Common.person_unit") ?>';
    const langPlayerCountLabel = '<?= lang("Common.player_count_label") ?>';

    var player_position_value = '<?= json_encode($player_position_value) ?>';
    // console.log(player_position_value);
    var positions = [];
    var position_counts = [];

    if(player_position_value) {
        try {
            JSON.parse(player_position_value).forEach(item => {
                positions.push(item.name_th);
                position_counts.push(Number(item.count_value));
            });
        } catch(e) {
            console.error("Error parsing position data", e);
        }
    }

    $(document).ready(function() {
        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            language: dtLanguage,
            dom: '<"d-flex justify-content-between align-items-center mx-3 mt-3"f>rt<"d-flex justify-content-between align-items-center mx-3 mb-3"ip>',
            ajax: {
                url: '<?= base_url('admin/report/get-players') ?>',
                method: "POST",
                data: function(d) {
                    d.player_position_id = $('#player_position_id').val();
                }
            },
            columns: [{
                    name: 'first_name',
                    data: 'player_detail',
                    className: 'ps-4 text-start',
                    render: function(data, type, row) {
                        return '<div class="fw-semibold">' + data + '</div>';
                    }
                },
                {
                    data: 'team_name',
                    className: 'text-center',
                    render: function(data) {
                        return '<span class="badge bg-light text-dark border">' + data + '</span>';
                    }
                },
                {
                    data: 'position',
                    className: 'text-center',
                    render: function(data) {
                        return '<span class="badge bg-primary-subtle text-primary">' + data + '</span>';
                    }
                },
                {
                    data: 'age',
                    className: 'text-center pe-4',
                    render: function(data) {
                        return data + ' ' + langYearsUnit;
                    }
                },
            ]
        });

        $('#player_position_id').change(function(event) {
            table.ajax.reload();
        });

        Highcharts.chart('position_value_chart', {
            chart: {
                type: 'column',
                align: 'left',
                style: {
                    fontFamily: 'inherit'
                }
            },
            title: {
                text: ''
            },
            xAxis: {
                categories: positions,
                crosshair: true,
                accessibility: {
                    description: langPlayerPositions
                }
            },
            yAxis: {
                min: 0,
                title: {
                    text: langCountPersons
                }
            },
            tooltip: {
                valueSuffix: ' ' + langPersonUnit,
                borderRadius: 10,
                shadow: false,
                borderWidth: 0,
                backgroundColor: 'rgba(255, 255, 255, 0.9)',
                style: {
                    fontSize: '14px'
                }
            },
            plotOptions: {
                column: {
                    pointPadding: 0.2,
                    borderWidth: 0,
                    borderRadius: 5
                }
            },
            series: [{
                name: langPlayerCountLabel,
                data: position_counts,
                color: "#5D87FF",
                showInLegend: false
            }],
            credits: {
                enabled: false
            }
        });
    });
</script>
<?php $this->endSection(); ?>