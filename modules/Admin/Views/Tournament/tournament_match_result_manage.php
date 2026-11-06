<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid">
    <!-- Scoreboard Card -->
    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius); background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%); color: white;">
        <div class="card-body p-4">
            <div class="row align-items-center justify-content-center">
                <!-- Time -->
                <div class="col-12 text-center mb-4">
                    <div class="d-inline-block px-4 py-2 rounded-pill" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(5px);">
                        <h2 id="time" class="mb-0 fw-bold font-monospace" style="letter-spacing: 2px;">00:00</h2>
                    </div>
                </div>

                <!-- Home Team -->
                <div class="col-4 text-center">
                    <h3 class="fw-bold mb-0"><?= $data['home_team_name'] ?></h3>
                </div>

                <!-- Score -->
                <div class="col-4 text-center">
                    <div class="d-flex justify-content-center align-items-center gap-3">
                        <div class="bg-white text-dark rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 70px;">
                            <h1 id="team1_score_text" class="mb-0 fw-bold display-5"><?= ($data['team1_score'] !== null && $data['team1_score'] !== '') ? $data['team1_score'] : '-' ?></h1>
                        </div>
                        <span class="display-6 fw-bold text-white-50">-</span>
                        <div class="bg-white text-dark rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 70px;">
                            <h1 id="team2_score_text" class="mb-0 fw-bold display-5"><?= ($data['team2_score'] !== null && $data['team2_score'] !== '') ? $data['team2_score'] : '-' ?></h1>
                        </div>
                    </div>
                </div>

                <!-- Away Team -->
                <div class="col-4 text-center">
                    <h3 class="fw-bold mb-0"><?= $data['away_team_name'] ?></h3>
                </div>

                <!-- Actions -->
                <div class="col-12 mt-4 pt-3 border-top border-white border-opacity-10">
                    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                        <div>
                            <button type="button" class="btn btn-success fw-bold shadow-sm" data-bs-toggle="modal" data-bs-target="#bs-fullscreen">
                                <i class="ti ti-users me-2"></i> <?= lang('Common.starter_and_substitute_list') ?>
                            </button>
                            <span class="text-white-50 ms-2 small d-none d-md-inline">
                                <i class="ti ti-info-circle me-1"></i> <?= lang('Common.please_fill_lineup_before_match') ?>
                            </span>
                        </div>
                        <button type="button" onclick="sendEndMatch('<?= $data['match_id'] ?>')" class="btn btn-light fw-bold shadow-sm text-primary">
                            <i class="ti ti-cloud-upload me-2"></i> <?= lang('Common.submit_summary') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <form action="<?= base_url('admin/tournament/save-player-to-match') ?>" method="POST" id="manage-real-players">
        <div class="modal fade" id="bs-fullscreen" tabindex="-1" aria-labelledby="bs-example-modal-lg" aria-hidden="true">
            <div class="modal-dialog modal-fullscreen">
                <div class="modal-content border-0">
                    <!-- Hidden -->
                    <input type="hidden" name="match_id" value="<?= (!empty($data['match_id'])) ? $data['match_id'] : '' ?>">
                    <input type="hidden" name="tour_id" value="<?= (!empty($data['tour_id'])) ? $data['tour_id'] : '' ?>">
                    <input type="hidden" name="team1_id" value="<?= (!empty($data['team1_id'])) ? $data['team1_id'] : '' ?>">
                    <input type="hidden" name="team2_id" value="<?= (!empty($data['team2_id'])) ? $data['team2_id'] : '' ?>">

                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title h4 fw-bold" id="exampleModalFullscreenLabel">
                            <i class="ti ti-users me-2"></i> <?= lang('Common.manage_player_lineup') ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <div class="container-fluid">
                            <div class="row g-4">
                                <div class="col-md-3">
                                    <!-- Nav tabs -->
                                    <div class="nav flex-column nav-pills bg-white rounded-3 shadow-sm p-3" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                        <a class="nav-link active fw-bold mb-2 p-3 border rounded-3" id="v-pills-home-tab" data-bs-toggle="pill" href="#v-pills-home" role="tab" aria-controls="v-pills-home" aria-selected="true">
                                            <i class="ti ti-shirt me-2"></i> <?= $data['home_team_name'] ?>
                                        </a>
                                        <a class="nav-link fw-bold p-3 border rounded-3" id="v-pills-away-tab" data-bs-toggle="pill" href="#v-pills-away" role="tab" aria-controls="v-pills-away" aria-selected="false">
                                            <i class="ti ti-shirt me-2"></i> <?= $data['away_team_name'] ?>
                                        </a>
                                    </div>
                                </div>
                                <div class="col-md-9">
                                    <div class="tab-content bg-white rounded-3 shadow-sm p-4" id="v-pills-tabContent">
                                        <div class="tab-pane fade show active" id="v-pills-home" role="tabpanel" aria-labelledby="v-pills-home-tab">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle data-tables">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center" width="10%"><?= lang('Common.starter') ?></th>
                                                            <th class="text-center" width="10%">
                                                                <div class="form-check d-flex justify-content-center align-items-center gap-2">
                                                                    <input class="form-check-input" type="checkbox" id="selectSubOnly">
                                                                    <label class="form-check-label small fw-bold" for="selectSubOnly"><?= lang('Common.substitute') ?></label>
                                                                </div>
                                                            </th>
                                                            <th class="text-center" width="10%"><?= lang('Common.shirt_number') ?></th>
                                                            <th><?= lang('Common.full_name') ?></th>
                                                            <th class="text-center" width="15%"><?= lang('Common.dob') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($players_home_list as $player) { ?>
                                                            <tr>
                                                                <td class="text-center">
                                                                    <div class="form-check d-flex justify-content-center">
                                                                        <input class="form-check-input primary check-light-primary border-2 border-primary real-check" type="checkbox" name="real_home_players[]" id="real_player_<?= $player['id'] ?>" value="<?= $player['id'] ?>" <?php foreach ($real_home_player as $sel) {
                                                                                                                                                                                                                                                                                                if ($sel['player_id'] == $player['id']) {
                                                                                                                                                                                                                                                                                                    echo "checked";
                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                            } ?>>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="form-check d-flex justify-content-center">
                                                                        <input class="form-check-input primary sub-check check-light-primary border-2 border-primary" type="checkbox" name="sub_home_players[]" id="sub_player_<?= $player['id'] ?>" value="<?= $player['id'] ?>" <?php foreach ($sub_home_player as $sel) {
                                                                                                                                                                                                                                                                                                if ($sel['player_id'] == $player['id']) {
                                                                                                                                                                                                                                                                                                    echo "checked";
                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                            } ?>>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center fw-bold text-primary"><?= $player['shirt_number'] ?></td>
                                                                <td class="fw-bold"><?= $player['first_name'] . ' ' . $player['last_name'] ?></td>
                                                                <td class="text-center text-muted small"><?= date('d/m/Y', strtotime('+543 years', strtotime($player['birth_date']))) ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>

                                        </div>
                                        <div class="tab-pane fade" id="v-pills-away" role="tabpanel" aria-labelledby="v-pills-away-tab">
                                            <div class="table-responsive">
                                                <table class="table table-hover align-middle data-tables">
                                                    <thead class="table-light">
                                                        <tr>
                                                            <th class="text-center" width="10%"><?= lang('Common.starter') ?></th>
                                                            <th class="text-center" width="10%"><?= lang('Common.substitute') ?></th>
                                                            <th class="text-center" width="10%"><?= lang('Common.shirt_number') ?></th>
                                                            <th><?= lang('Common.full_name') ?></th>
                                                            <th class="text-center" width="15%"><?= lang('Common.dob') ?></th>
                                                        </tr>
                                                    </thead>
                                                    <tbody>
                                                        <?php foreach ($players_away_list as $player) { ?>
                                                            <tr>
                                                                <td class="text-center">
                                                                    <div class="form-check d-flex justify-content-center">
                                                                        <input class="form-check-input primary check-light-primary border-2 border-primary" type="checkbox" name="real_away_players[]" id="real_player_<?= $player['id'] ?>" value="<?= $player['id'] ?>" <?php foreach ($real_away_player as $sel) {
                                                                                                                                                                                                                                                                                                if ($sel['player_id'] == $player['id']) {
                                                                                                                                                                                                                                                                                                    echo "checked";
                                                                                                                                                                                                                                                                                                }
                                                                                                                                                                                                                                                                                            } ?>>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center">
                                                                    <div class="form-check d-flex justify-content-center">
                                                                        <input class="form-check-input primary check-light-primary border-2 border-primary" type="checkbox" name="sub_away_players[]" id="sub_player_<?= $player['id'] ?>" value="<?= $player['id'] ?>" <?php foreach ($sub_away_player as $sel) {
                                                                                                                                                                                                                                                                                            if ($sel['player_id'] == $player['id']) {
                                                                                                                                                                                                                                                                                                echo "checked";
                                                                                                                                                                                                                                                                                            }
                                                                                                                                                                                                                                                                                        } ?>>
                                                                    </div>
                                                                </td>
                                                                <td class="text-center fw-bold text-primary"><?= $player['shirt_number'] ?></td>
                                                                <td class="fw-bold"><?= $player['first_name'] . ' ' . $player['last_name'] ?></td>
                                                                <td class="text-center text-muted small"><?= date('d/m/Y', strtotime('+543 years', strtotime($player['birth_date']))) ?></td>
                                                            </tr>
                                                        <?php } ?>
                                                    </tbody>
                                                </table>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer justify-content-center bg-light border-top-0 py-3">
                        <button type="submit" class="btn btn-success px-4 fw-bold shadow-sm"><i class="ti ti-check me-2"></i> <?= lang('Common.save') ?></button>
                        <button type="button" class="btn btn-outline-danger px-4 fw-bold" data-bs-dismiss="modal">
                            <?= lang('Common.cancel') ?>
                        </button>
                    </div>
                </div>
                <!-- /.modal-content -->
            </div>
            <!-- /.modal-dialog -->
        </div>
    </form>

    <div class="row g-4">
        <!-- Left Column: Controls & Pitch -->
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--border-radius);">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-primary"><i class="ti ti-device-gamepad-2 me-2"></i> <?= lang('Common.match_control') ?></h5>
                </div>
                <div class="card-body">
                    <div class="d-flex gap-2 mb-4">
                        <button type="button" onclick="updateMatchStatus(1 , '<?php echo $data['match_id'] ?>')" class="btn btn-primary flex-grow-1 shadow-sm">
                            <i class="ti ti-player-play me-2"></i> <?= lang('Common.start_match') ?>
                        </button>
                        <button type="button" onclick="updateMatchStatus(2 , '<?php echo $data['match_id'] ?>')" class="btn btn-warning text-white flex-grow-1 shadow-sm">
                            <i class="ti ti-player-pause me-2"></i> <?= lang('Common.half_time') ?>
                        </button>
                        <button type="button" onclick="updateMatchStatus(3 , '<?php echo $data['match_id'] ?>')" class="btn btn-danger flex-grow-1 shadow-sm">
                            <i class="ti ti-flag-check me-2"></i> <?= lang('Common.end_game') ?>
                        </button>
                    </div>

                    <style>
                        .pitch-container {
                            width: 100%;
                            overflow-x: auto;
                            display: flex;
                            justify-content: center;
                            background: #1a4d2e;
                            border-radius: 12px;
                            padding: 20px;
                            box-shadow: inset 0 0 20px rgba(0,0,0,0.3);
                        }
                        .pitch {
                            background-image: url('https://www.jqueryscript.net/demo/Soccer-Field-Players-Positions/img/soccerfield_green.png');
                            background-size: cover;
                            width: 520px;
                            height: 325px;
                            position: relative;
                            flex-shrink: 0;
                            border: 2px solid rgba(255,255,255,0.2);
                            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
                        }

                        .player {
                            position: absolute;
                            width: 24px;
                            height: 24px;
                            border-radius: 50%;
                            background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%);
                            cursor: pointer;
                            border: 2px solid white;
                            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
                            transition: transform 0.2s;
                            z-index: 10;
                        }

                        .player:hover {
                            transform: scale(1.2);
                            z-index: 20;
                        }

                        .player.away {
                            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%);
                        }

                        .player-label {
                            font-size: 10px;
                            color: white;
                            position: absolute;
                            bottom: -18px;
                            left: 50%;
                            transform: translateX(-50%);
                            width: 80px;
                            text-align: center;
                            text-shadow: 0 1px 2px rgba(0,0,0,0.8);
                            font-weight: 600;
                            background: rgba(0,0,0,0.5);
                            padding: 1px 4px;
                            border-radius: 4px;
                            pointer-events: none;
                        }
                    </style>

                    <div class="pitch-container mb-4">
                        <div class="pitch" id="pitch"></div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-primary mb-3 border-bottom pb-2"><?= $data['home_team_name'] ?> (<?= lang('Common.substitute') ?>)</h6>
                                    <div class="d-flex flex-wrap gap-2" style="max-height: 150px; overflow-y: auto;">
                                        <?php foreach ($sub_home_player as $player) { ?>
                                            <span class="badge bg-white text-dark border shadow-sm p-2">
                                                <?= $player['first_name'] . ' ' . $player['last_name'] ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                             <div class="card bg-light border-0">
                                <div class="card-body p-3">
                                    <h6 class="fw-bold text-danger mb-3 border-bottom pb-2 text-end"><?= $data['away_team_name'] ?> (<?= lang('Common.substitute') ?>)</h6>
                                    <div class="d-flex flex-wrap gap-2 justify-content-end" style="max-height: 150px; overflow-y: auto;">
                                        <?php foreach ($sub_away_player as $player) { ?>
                                            <span class="badge bg-white text-dark border shadow-sm p-2">
                                                <?= $player['first_name'] . ' ' . $player['last_name'] ?>
                                            </span>
                                        <?php } ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Events -->
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm h-100" style="border-radius: var(--border-radius);">
                <div class="card-header bg-white border-bottom py-3">
                    <h5 class="card-title mb-0 fw-bold text-secondary"><i class="ti ti-timeline-event me-2"></i> <?= lang('Common.record_event') ?></h5>
                </div>
                <div class="card-body d-flex flex-column">
                    <!-- Event Form Container -->
                    <div id="add-event-card" class="mb-3 p-3 bg-light rounded-3 border" style="min-height: 200px;">
                        <div class="text-center text-muted py-5">
                            <i class="ti ti-hand-click fs-1 mb-2"></i>
                            <p><?= lang('Common.click_player_to_add_event') ?></p>
                        </div>
                    </div>

                    <!-- Event Timeline/List -->
                    <div class="flex-grow-1 overflow-auto" style="max-height: 400px;">
                        <h6 class="fw-bold text-muted mb-3 small text-uppercase">Timeline</h6>
                        <div id="events-table">
                            <?php foreach ($events as $key => $row) { ?>
                                <div class="d-flex align-items-center mb-3 p-2 rounded-3 hover-bg-light border-bottom">
                                    <!-- Home Team Event -->
                                    <div class="col-5 text-end pe-3">
                                        <?php if ($row['team_id'] == $data['team1_id']) { ?>
                                            <div class="fw-bold text-truncate"><?= $row['pl1_first_name'] . ' ' . $row['pl1_last_name'] ?></div>
                                            <div class="small text-muted"><?= $row['pl2_first_name'] . ' ' . $row['pl2_last_name'] ?></div>
                                        <?php } ?>
                                    </div>

                                    <!-- Minute & Icon -->
                                    <div class="col-2 text-center position-relative">
                                        <div class="badge bg-dark rounded-pill mb-1"><?= $row['minute'] ?>'</div>
                                        <div class="fs-4"><?= $row['icon'] ?></div>
                                    </div>

                                    <!-- Away Team Event -->
                                    <div class="col-5 ps-3">
                                        <?php if ($row['team_id'] == $data['team2_id']) { ?>
                                            <div class="fw-bold text-truncate"><?= $row['pl1_first_name'] . ' ' . $row['pl1_last_name'] ?></div>
                                            <div class="small text-muted"><?= $row['pl2_first_name'] . ' ' . $row['pl2_last_name'] ?></div>
                                        <?php } ?>
                                    </div>

                                    <!-- Delete Action -->
                                    <button class="btn btn-link text-danger p-0 ms-2" onclick="deletedEvent('<?= $row['match_event_id'] ?>', '<?= $row['match_id'] ?>')">
                                        <i class="ti ti-trash"></i>
                                    </button>
                                </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langSaveSuccess = '<?= lang("Common.save_success") ?>';
    const langConfirmDeleteData = '<?= lang("Common.confirm_delete_data") ?>';
    const langOk = '<?= lang("Common.ok") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';
    const langConfirmSubmitData = '<?= lang("Common.confirm_submit_data") ?>';
    const langSubmitSuccess = '<?= lang("Common.submit_success") ?>';
    const langSubmitFailed = '<?= lang("Common.submit_failed") ?>';
    const langConnectionError = '<?= lang("Common.connection_error") ?>';

    let seconds = 0;
    let interval = null;

    const updateMatchStatus = (match_status, match_id) => {
        $.ajax({
            type: "post",
            url: "<?php echo base_url('admin/tournament/updateMatchStatus') ?>",
            data: {
                'match_status': match_status,
                'match_id': match_id
            },
            success: function(response) {
                Swal.fire({
                    icon: "success",
                    text: langSaveSuccess,
                });
            }
        });
    }
    document.getElementById('selectSubOnly').addEventListener('change', function() {
        const realChecks = document.querySelectorAll('.real-check');
        const subChecks = document.querySelectorAll('.sub-check');

        realChecks.forEach((realCheck, index) => {
            if (!realCheck.checked && !realCheck.disabled) {
                subChecks[index].checked = this.checked;
            } else {
                subChecks[index].checked = false;
            }
        });
    });

    function updateTime() {
        const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
        const secs = String(seconds % 60).padStart(2, '0');
        document.getElementById('time').textContent = `${mins}:${secs}`;
    }

    function startTimer() {
        if (interval) return; // ป้องกันการกดซ้ำ
        interval = setInterval(() => {
            seconds++;
            updateTime();

            // หยุดที่ 90 นาที (5400 วินาที) หากต้องการ
            if (seconds >= 5400) {
                stopTimer();
            }
        }, 1000);
    }

    function stopTimer() {
        clearInterval(interval);
        interval = null;
    }

    function resetTimer() {
        stopTimer();
        seconds = 0;
        updateTime();
    }

    function getCardEvent(tour_id, match_id, team_id, player_id) {
        $("#add-event-card").empty();
        $.ajax({
            method: "POST",
            url: "<?= base_url('admin/tournament/get-event-card') ?>",
            data: {
                tour_id: tour_id,
                match_id: match_id,
                team_id: team_id,
                player_id: player_id,
            },
            success: function(res) {
                const mins = String(Math.floor(seconds / 60)).padStart(2, '0');
                console.log(res);
                $("#add-event-card").append(res);
                $("#minute").val(parseInt(mins))
            }
        });
    }

    function saveEvent() {
        // console.log($('#save-event-form').serialize());
        $.ajax({
            method: "POST",
            url: "<?= base_url('admin/tournament/save-event') ?>",
            data: $('#save-event-form').serialize(),
            success: function(res) {
                // console.log(res);
                $('#events-table').empty();
                $('#events-table').append(res.table);

                $("#team1_score_text").text(res.score.team1_score !== null && res.score.team1_score !== '' ? res.score.team1_score : '-');
                $("#team2_score_text").text(res.score.team2_score !== null && res.score.team2_score !== '' ? res.score.team2_score : '-');

                $("#add-event-card").empty();
            }
        });
    }

    function deletedEvent(id, match_id) {
        Swal.fire({
            title: langConfirmDeleteData,
            text: "",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: langOk,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('admin/tournament/delete-event') ?>",
                    data: {
                        id: id,
                        match_id: match_id
                    },
                    success: function(res) {
                        // console.log(res);
                        $('#events-table').empty();
                        $('#events-table').append(res.table);

                        $("#team1_score_text").text(res.score.team1_score !== null && res.score.team1_score !== '' ? res.score.team1_score : '-');
                        $("#team2_score_text").text(res.score.team2_score !== null && res.score.team2_score !== '' ? res.score.team2_score : '-');
                    }
                });

                Swal.fire({
                    position: "center",
                    icon: "success",
                    title: "Your work has been saved",
                    showConfirmButton: false,
                    timer: 1500
                }).then((res) => {
                    // window.location.reload();
                });
            }
        });
    }

    function sendEndMatch(match_id) {
        Swal.fire({
            title: langConfirmSubmitData,
            text: "",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: langOk,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "GET",
                    url: "<?= base_url('api/end-match-result') ?>" + "/" + match_id,
                    success: function(res, textStatus, jqXHR) {
                        if (jqXHR.status === 200) {
                            Swal.fire({
                                position: "center",
                                icon: "success",
                                title: langSubmitSuccess,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        } else {
                            Swal.fire({
                                position: "center",
                                icon: "error",
                                title: langSubmitFailed,
                                showConfirmButton: false,
                                timer: 1500
                            });
                        }
                    },
                    error: function() {
                        Swal.fire({
                            position: "center",
                            icon: "error",
                            title: langConnectionError,
                            showConfirmButton: false,
                            timer: 1500
                        });
                    }
                });
            }
        });
    }

    $(document).ready(function() {
        // เรียกใช้ครั้งแรกเพื่อให้แสดง 00:00
        updateTime();

        // แก้ปัญหา modal-backdrop ติดค้าง
        $('#bs-fullscreen').on('hidden.bs.modal', function () {
            $('.modal-backdrop').remove();
            $('body').removeClass('modal-open');
            $('body').css('padding-right', '');
        });
    })
</script>
<script>
    const allPlayersHome = [
        <?php foreach ($real_home_player as $player) { ?> {
                shirt_number: '<?= $player['shirt_number'] ?>',
                name: '<?= $player['first_name'] ?>',
                positionId: <?= $player['player_position_id'] ?>,
                playerId: '<?= $player['player_id'] ?>',
                tourId: '<?= $data['tour_id'] ?>',
                matchId: '<?= $data['match_id'] ?>',
                teamId: <?= $data['team1_id'] ?>
            },
        <?php } ?>
    ];


    const allPlayersAway = [
        <?php foreach ($real_away_player as $player) { ?> {
                shirt_number: '<?= $player['shirt_number'] ?>',
                name: '<?= $player['first_name'] ?>',
                positionId: <?= $player['player_position_id'] ?>,
                playerId: '<?= $player['player_id'] ?>',
                tourId: '<?= $data['tour_id'] ?>',
                matchId: '<?= $data['match_id'] ?>',
                teamId: <?= $data['team2_id'] ?>

            },
        <?php } ?>
    ];

    // กำหนดตำแหน่งของผู้เล่นในทีมเหย้า
    const teamStateHome = {
        goalkeeper: [],
        defender: [],
        midfielder: [],
        forward: []
    };

    // กำหนดตำแหน่งของผู้เล่นในทีมเยือน
    const teamStateAway = {
        goalkeeper: [],
        defender: [],
        midfielder: [],
        forward: []
    };

    // คำนวณจำนวนผู้เล่นในแต่ละตำแหน่งจากข้อมูลของทีมเหย้า
    allPlayersHome.forEach(player => {
        switch (player.positionId) {
            case 1: // forward
                teamStateHome.forward.push(player);
                break;
            case 2: // midfielder
                teamStateHome.midfielder.push(player);
                break;
            case 3: // defender
                teamStateHome.defender.push(player);
                break;
            case 4: // goalkeeper
                teamStateHome.goalkeeper.push(player);
                break;
        }
    });

    // คำนวณจำนวนผู้เล่นในแต่ละตำแหน่งจากข้อมูลของทีมเยือน
    allPlayersAway.forEach(player => {
        switch (player.positionId) {
            case 1: // forward
                teamStateAway.forward.push(player);
                break;
            case 2: // midfielder
                teamStateAway.midfielder.push(player);
                break;
            case 3: // defender
                teamStateAway.defender.push(player);
                break;
            case 4: // goalkeeper
                teamStateAway.goalkeeper.push(player);
                break;
        }
    });

    // ฟังก์ชันการแสดงผลผู้เล่นในสนาม
    function renderPlayers() {
        const pitch = document.getElementById('pitch');
        pitch.innerHTML = '';

        // กำหนดตำแหน่งเริ่มต้น Y สำหรับแต่ละตำแหน่งในทีมเหย้า
        const numDefendersHome = teamStateHome.defender.length;
        const numMidfieldersHome = teamStateHome.midfielder.length;
        const numForwardsHome = teamStateHome.forward.length;

        // กำหนดค่า startY ตามจำนวนผู้เล่นในแต่ละตำแหน่ง
        let startYDefenderHome = 110;
        if (numDefendersHome === 3) startYDefenderHome = 110;
        else if (numDefendersHome === 4) startYDefenderHome = 85;
        else if (numDefendersHome === 5) startYDefenderHome = 65;
        else if (numDefendersHome === 2) startYDefenderHome = 130;
        else if (numMidfieldersHome === 1) startYDefenderHome = 155;


        let startYMidfielderHome = 110;
        if (numMidfieldersHome === 3) startYMidfielderHome = 110;
        else if (numMidfieldersHome === 4) startYMidfielderHome = 85;
        else if (numMidfieldersHome === 5) startYMidfielderHome = 65;
        else if (numMidfieldersHome === 2) startYMidfielderHome = 130;
        else if (numMidfieldersHome === 1) startYMidfielderHome = 155;

        let startYForwardHome = 110;
        if (numForwardsHome === 3) startYForwardHome = 110;
        else if (numForwardsHome === 4) startYForwardHome = 85;
        else if (numMidfieldersHome === 5) startYMidfielderHome = 65;
        else if (numForwardsHome === 2) startYForwardHome = 130;
        else if (numForwardsHome === 1) startYForwardHome = 155;

        const startYHome = {
            goalkeeper: 158,
            defender: startYDefenderHome,
            midfielder: startYMidfielderHome,
            forward: startYForwardHome
        };

        const stepYHome = {
            goalkeeper: 0,
            defender: 45,
            midfielder: 45,
            forward: 45
        };

        const posXHome = {
            goalkeeper: 40,
            defender: 100,
            midfielder: 160,
            forward: 220
        };

        // กำหนดตำแหน่งเริ่มต้น Y สำหรับแต่ละตำแหน่งในทีมเยือน
        const numDefendersAway = teamStateAway.defender.length;
        const numMidfieldersAway = teamStateAway.midfielder.length;
        const numForwardsAway = teamStateAway.forward.length;

        let startYDefenderAway = 110;
        if (numDefendersAway === 3) startYDefenderAway = 110;
        else if (numDefendersAway === 4) startYDefenderAway = 85;
        else if (numDefendersAway === 5) startYDefenderAway = 65;
        else if (numDefendersAway === 2) startYDefenderAway = 130;
        else if (numDefendersAway === 1) startYDefenderAway = 155;

        let startYMidfielderAway = 110;
        if (numMidfieldersAway === 3) startYMidfielderAway = 120;
        else if (numMidfieldersAway === 4) startYMidfielderAway = 85;
        else if (numMidfieldersAway === 5) startYMidfielderAway = 65;
        else if (numMidfieldersAway === 2) startYMidfielderAway = 130;
        else if (numMidfieldersAway === 1) startYMidfielderAway = 155;


        let startYForwardAway = 110;
        if (numForwardsAway === 3) startYForwardAway = 120;
        else if (numForwardsAway === 4) startYForwardAway = 85;
        else if (numForwardsAway === 5) startYForwardAway = 65;
        else if (numForwardsAway === 2) startYForwardAway = 130;
        else if (numForwardsAway === 1) startYForwardAway = 155;

        const startYAway = {
            goalkeeper: 158,
            defender: startYDefenderAway,
            midfielder: startYMidfielderAway,
            forward: startYForwardAway
        };

        const stepYAway = {
            goalkeeper: 0,
            defender: 45,
            midfielder: 45,
            forward: 45
        };

        const posXAway = {
            goalkeeper: 465,
            defender: 400,
            midfielder: 350,
            forward: 285
        };

        // การแสดงผลของทีมเหย้า
        Object.keys(teamStateHome).forEach(position => {
            let y = startYHome[position];
            teamStateHome[position].forEach(player => {
                const playerElement = document.createElement('div');
                playerElement.className = 'player';
                playerElement.style.left = `${posXHome[position]}px`;
                playerElement.style.top = `${y}px`;
                playerElement.dataset.position = position;
                playerElement.dataset.name = player.name;
                playerElement.dataset.playerId = player.playerId;

                // เพิ่มเบอร์เสื้อเข้าไปที่ชื่อ
                const label = document.createElement('div');
                label.className = 'player-label';
                label.innerText = `${player.name} (${player.shirt_number})`; // แสดงชื่อและเบอร์เสื้อ
                playerElement.appendChild(label);

                // Inline styles removed, handled by CSS .player-label

                playerElement.setAttribute('onclick',
                    `getCardEvent('${player.tourId}', '${player.matchId}', ${player.teamId}, '${player.playerId}')`
                );

                pitch.appendChild(playerElement);
                y += stepYHome[position];
            });
        });

        // การแสดงผลของทีมเยือน
        Object.keys(teamStateAway).forEach(position => {
            let y = startYAway[position];
            teamStateAway[position].forEach(player => {
                const playerElement = document.createElement('div');
                playerElement.className = 'player';
                playerElement.style.left = `${posXAway[position]}px`;
                playerElement.style.top = `${y}px`;
                playerElement.dataset.position = position;
                playerElement.dataset.name = player.name;
                playerElement.dataset.playerId = player.playerId;

                // เพิ่มเบอร์เสื้อเข้าไปที่ชื่อ
                const label = document.createElement('div');
                label.className = 'player-label';
                label.innerText = `${player.name} ( ${player.shirt_number} )`; // แสดงชื่อและเบอร์เสื้อ
                playerElement.appendChild(label);

                // Inline styles removed, handled by CSS .player-label
                playerElement.classList.add('away');

                playerElement.setAttribute('onclick',
                    `getCardEvent('${player.tourId}', '${player.matchId}', ${player.teamId}, '${player.playerId}')`
                );

                pitch.appendChild(playerElement);
                y += stepYAway[position];
            });
        });
    }

    renderPlayers();
</script>

<?php $this->endSection(); ?>