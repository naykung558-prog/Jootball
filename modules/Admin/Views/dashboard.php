<?= $this->extend('template/admin_layout') ?>

<?= $this->section('content') ?>
                <!--  Row 1 -->
                <div class="row">
                    <div class="col-lg-12 d-flex align-items-strech">
                        <div class="card w-100 border-0 shadow-lg" style="border-radius: var(--border-radius); background: linear-gradient(135deg, white 0%, #f8faff 100%);">
                            <div class="card-body p-4">
                                <div class="d-sm-flex d-block align-items-center justify-content-between mb-4">
                                    <div class="mb-3 mb-sm-0">
                                        <h3 class="card-title fw-bold text-primary mb-2"><?= $title ?></h3>
                                        <p class="card-subtitle mb-0 text-muted"><?= lang('Common.welcome_to_tournament_system') ?></p>
                                    </div>
                                </div>

                                <div class="row mt-5">
                                    <div class="col-12">
                                        <div class="d-flex align-items-center justify-content-between mb-4">
                                            <h4 class="fw-bold" style="color: var(--primary-color); border-left: 4px solid var(--secondary-color); padding-left: 15px;"><?= lang('Common.admin_main_menu') ?></h4>
                                        </div>
                                        <div class="row">
                                            <?php if (isSuperAdmin()): ?>
                                                <div class="col-md-4 mb-3">
                                                    <a href="<?= base_url('admin/tournament') ?>" class="btn btn-outline-primary w-100 py-3 fw-bold" style="border-radius: var(--border-radius); transition: var(--transition);">
                                                        <i class="fas fa-trophy me-2"></i>
                                                        <?= lang('Common.manage_tournament') ?>
                                                    </a>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <a href="<?= base_url('admin/team') ?>" class="btn w-100 py-3 fw-bold" style="border: 2px solid var(--secondary-color); color: var(--secondary-color); background: transparent; border-radius: var(--border-radius); transition: var(--transition);" onmouseover="this.style.background='var(--gold-gradient)'; this.style.color='white'; this.style.transform='translateY(-2px)'; this.style.boxShadow='var(--gold-shadow)'" onmouseout="this.style.background='transparent'; this.style.color='var(--secondary-color)'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fas fa-users me-2"></i>
                                                        <?= lang('Common.manage_team') ?>
                                                    </a>
                                                </div>
                                                <div class="col-md-4 mb-3">
                                                    <a href="<?= base_url('admin/report/report-players') ?>" class="btn w-100 py-3 fw-bold" style="border: 2px solid #17a2b8; color: #17a2b8; background: transparent; border-radius: var(--border-radius); transition: var(--transition);" onmouseover="this.style.background='linear-gradient(135deg, #17a2b8 0%, #138496 100%)'; this.style.color='white'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(23, 162, 184, 0.3)'" onmouseout="this.style.background='transparent'; this.style.color='#17a2b8'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fas fa-chart-line me-2"></i>
                                                        <?= lang('Common.admin_player_report') ?>
                                                    </a>
                                                </div>
                                            <?php endif; ?>

                                            <?php if (hasRole('admin') && !isSuperAdmin()): ?>
                                                <div class="col-md-6 mb-3">
                                                    <a href="<?= base_url('admin/report-guest/report-players') ?>" class="btn w-100 py-3 fw-bold" style="border: 2px solid #28a745; color: #28a745; background: transparent; border-radius: var(--border-radius); transition: var(--transition);" onmouseover="this.style.background='linear-gradient(135deg, #28a745 0%, #1e7e34 100%)'; this.style.color='white'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(40, 167, 69, 0.3)'" onmouseout="this.style.background='transparent'; this.style.color='#28a745'; this.style.transform='translateY(0)'; this.style.boxShadow='none'">
                                                        <i class="fas fa-eye me-2"></i>
                                                        <?= lang('Common.admin_guest_report') ?>
                                                    </a>
                                                </div>
                                                <div class="col-md-6 mb-3">
                                                    <button type="button" class="btn w-100 py-3 fw-bold" style="border: 2px solid #17a2b8; color: #17a2b8; background: transparent; border-radius: var(--border-radius); transition: var(--transition);" onmouseover="this.style.background='linear-gradient(135deg, #17a2b8 0%, #138496 100%)'; this.style.color='white'; this.style.transform='translateY(-2px)'; this.style.boxShadow='0 4px 15px rgba(23, 162, 184, 0.3)'" onmouseout="this.style.background='transparent'; this.style.color='#17a2b8'; this.style.transform='translateY(0)'; this.style.boxShadow='none'" onclick="promptPlayerIdDashboard()">
                                                        <i class="fas fa-search me-2"></i>
                                                        <?= lang('Common.search_player_details') ?>
                                                    </button>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>

                                <!-- Data Tables and Charts Section -->
                                <?php if (hasRole('admin') || isSuperAdmin()): ?>

                                <!-- Player Statistics Chart - Top Section -->
                                <?php if (!empty($player_positions)): ?>
                                <div class="row mt-5">
                                    <div class="col-12 mb-4">
                                        <div class="card border-0 shadow-sm" style="border-radius: var(--border-radius); background: linear-gradient(135deg, white 0%, #f8faff 100%);">
                                            <div class="card-header border-0 pb-0" style="background: transparent;">
                                                <h5 class="fw-bold" style="color: var(--primary-color);">
                                                    <i class="fas fa-chart-column me-2" style="color: var(--secondary-color);"></i>
                                                    <?= lang('Common.player_position_stats') ?>
                                                </h5>
                                            </div>
                                            <div class="card-body pt-2">
                                                <div id="positionChart" style="height: 350px;"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php endif; ?>

                                <!-- Players Table - Bottom Section -->
                                <div class="row mt-4">
                                    <div class="col-12 mb-4">
                                        <div class="card border-0 shadow-sm" style="border-radius: var(--border-radius); background: linear-gradient(135deg, white 0%, #f8faff 100%);">
                                            <div class="card-header border-0 pb-0" style="background: transparent;">
                                                <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
                                                    <h5 class="fw-bold mb-0" style="color: var(--primary-color);">
                                                        <i class="fas fa-users me-2" style="color: var(--secondary-color);"></i>
                                                        <?= lang('Common.player_list') ?>
                                                    </h5>
                                                    <div class="d-flex flex-column flex-sm-row gap-2">
                                                        <select id="player_position_id" class="form-select form-select-sm" style="width: auto; min-width: 160px; border-radius: var(--border-radius);">
                                                            <option value=""><?= lang('Common.all_positions') ?></option>
                                                            <?php if (isset($player_positions) && is_array($player_positions)): ?>
                                                                <?php foreach ($player_positions as $position): ?>
                                                                    <?php if (is_object($position) && isset($position->id) && isset($position->name_th)): ?>
                                                                        <option value="<?= $position->id ?>"><?= $position->name_th ?></option>
                                                                    <?php elseif (is_array($position) && isset($position['id']) && isset($position['name_th'])): ?>
                                                                        <option value="<?= $position['id'] ?>"><?= $position['name_th'] ?></option>
                                                                    <?php endif; ?>
                                                                <?php endforeach; ?>
                                                            <?php endif; ?>
                                                        </select>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="card-body pt-2">
                                                <div class="table-responsive">
                                                    <table id="playersTable" class="table table-hover align-middle" style="border-radius: var(--border-radius);">
                                                        <thead style="background: var(--navy-gradient); color: white;">
                                                            <tr>
                                                                <th style="border-radius: var(--border-radius) 0 0 var(--border-radius);"><?= lang('Common.players') ?></th>
                                                                <th><?= lang('Common.teams') ?></th>
                                                                <th><?= lang('Common.position') ?></th>
                                                                <th style="border-radius: 0 var(--border-radius) var(--border-radius) 0;"><?= lang('Common.age') ?></th>
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
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <?php if (hasRole('admin') || isSuperAdmin()): ?>
    <script>
    const langPlayerPosition = '<?= lang("Common.player_position") ?>';
    const langCountPerson = '<?= lang("Common.count_person") ?>';
    const langPersonUnit = '<?= lang("Common.person_unit") ?>';
    const langPlayerCount = '<?= lang("Common.player_count_label") ?>';
    const langNoStatsData = '<?= lang("Common.no_stats_data") ?>';
    var player_position_value = '<?= json_encode($player_positions ?? []) ?>';
    var positions = [];
    var position_counts = [];

    try {
        if (player_position_value && player_position_value !== '[]' && player_position_value !== 'null') {
            var positionsData = JSON.parse(player_position_value);
            if (Array.isArray(positionsData) && positionsData.length > 0) {
                positionsData.forEach(item => {
                    if (item && (item.name_th || item['name_th']) && (item.count_value || item['count_value'])) {
                        positions.push(item.name_th || item['name_th']);
                        position_counts.push(Number(item.count_value || item['count_value'] || 0));
                    }
                });
            }
        }
    } catch (e) {
        console.log('Error parsing player positions data:', e);
        positions = [];
        position_counts = [];
    }

    $(document).ready(function() {
        // DataTable for Players - only initialize if table exists
        var playersTable = null;
        if ($('#playersTable').length > 0) {
            try {
                playersTable = $('#playersTable').DataTable({
                    processing: true,
                    serverSide: true,
                    responsive: true,
                    pageLength: 10,
                    lengthMenu: [10, 25, 50, 100],
                    language: dtLanguage,
                    ajax: {
                        url: '<?= base_url('admin/report/get-players') ?>',
                        method: "POST",
                        data: function(d) {
                            d.player_position_id = $('#player_position_id').val() || '';
                        },
                        error: function(xhr, error, thrown) {
                            console.log('DataTable Error:', error, thrown);
                        }
                    },
                    columns: [{
                            name: 'first_name',
                            data: 'player_detail',
                            className: 'ps-0 text-start'
                        },
                        {
                            data: 'team_name',
                            className: 'text-center'
                        },
                        {
                            data: 'position',
                            className: 'text-center'
                        },
                        {
                            data: 'age',
                            className: 'text-center',
                        },
                    ]
                });
            } catch (e) {
                console.log('Error initializing DataTable:', e);
            }
        }

        // Custom search - only if table exists
        $("#searchInput").keyup(function(){
            if (playersTable) {
                playersTable.search($(this).val()).draw();
            }
        });

        // Position filter - only if table exists
        $("#player_position_id").change(function(){
            if (playersTable) {
                playersTable.draw();
            }
        });

        // Position Chart - only if data exists and chart container exists
        if ($('#positionChart').length > 0 && positions.length > 0 && position_counts.length > 0) {
            try {
                Highcharts.chart('positionChart', {
                    chart: {
                        type: 'column',
                        backgroundColor: 'transparent',
                    },
                    title: {
                        text: null
                    },
                    xAxis: {
                        categories: positions,
                        crosshair: true,
                        accessibility: {
                            description: langPlayerPosition
                        }
                    },
                    yAxis: {
                        min: 0,
                        title: {
                            text: langCountPerson,
                            style: {
                                color: '#06346D'
                            }
                        }
                    },
                    tooltip: {
                        valueSuffix: ' ' + langPersonUnit
                    },
                    plotOptions: {
                        column: {
                            pointPadding: 0.2,
                            borderWidth: 0,
                            borderRadius: 4
                        }
                    },
                    colors: ['#06346D'],
                    series: [{
                        name: langPlayerCount,
                        data: position_counts,
                        color: "#06346D",
                    }]
                });
            } catch (e) {
                console.log('Error creating chart:', e);
                // Hide chart container if error
                $('#positionChart').parent().hide();
            }
        } else if ($('#positionChart').length > 0) {
            // Show no data message
            $('#positionChart').html('<div class="text-center py-5 text-muted"><i class="fas fa-chart-column fa-3x mb-3"></i><br>' + langNoStatsData + '</div>');
        }
    });
    </script>
    <?php endif; ?>

    <?php if (hasRole('admin') && !isSuperAdmin()): ?>
    <script>
    const langSearchPlayerData = '<?= lang("Common.search_player_data") ?>';
    const langEnterPlayerIdPlaceholder = '<?= lang("Common.enter_player_id_placeholder") ?>';
    const langSearchBtn = '<?= lang("Common.search") ?>';
    const langCancelBtn = '<?= lang("Common.cancel") ?>';
    const langPleaseEnterPlayerId = '<?= lang("Common.please_enter_player_id") ?>';
    const langPlayerIdMustBePositive = '<?= lang("Common.player_id_must_be_positive") ?>';
    function promptPlayerIdDashboard() {
        Swal.fire({
            title: langSearchPlayerData,
            input: 'number',
            inputLabel: 'Player ID',
            inputPlaceholder: langEnterPlayerIdPlaceholder,
            showCancelButton: true,
            confirmButtonText: langSearchBtn,
            cancelButtonText: langCancelBtn,
            inputValidator: (value) => {
                if (!value) {
                    return langPleaseEnterPlayerId
                }
                if (value <= 0) {
                    return langPlayerIdMustBePositive
                }
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const playerId = result.value;
                const url = '<?= base_url('admin/report-guest/player-details/') ?>' + playerId;
                window.open(url, '_blank');
            }
        })
    }
    </script>
    <?php endif; ?>
<?= $this->endSection() ?>