<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<!-- Nav tabs -->
<ul class="nav nav-pills flex-column flex-sm-row mt-4" role="tablist">
    <li class="nav-item flex-sm-fill text-sm-center">
        <a class="nav-link active" data-bs-toggle="tab" href="#navpill-11" role="tab">
            <span><?= $data['home_team_name'] ?></span>
        </a>
    </li>
    <li class="nav-item flex-sm-fill text-sm-center">
        <a class="nav-link" data-bs-toggle="tab" href="#navpill-22" role="tab">
            <span><?= $data['away_team_name'] ?></span>
        </a>
    </li>
</ul>
<!-- Tab panes -->
<div class="tab-content mt-2">
    <div class="tab-pane active p-3" id="navpill-11" role="tabpanel">
        <div class="row">
            <?php foreach ($players_home as $key => $player) { ?>
            <div class="col-md-3 text-center">
                <div class="card shadow shadow-lg">
                    <div class="card-body">
                        <?php if (file_exists('./' . $player['image_path'])) { ?>
                        <?php if ($player['image_path'] != '') { ?>
                        <img src="<?= base_url($player['image_path']) ?>"
                            class="rounded-circle border border-2 border-primary img-square" alt=""
                            style="max-width: 80px; min-width: auto;">
                        <?php } else { ?>
                        <img src="<?= base_url('public/img/img_profile.jpg') ?>"
                            class="rounded-circle border border-2 border-primary img-square" alt=""
                            style="max-width: 80px;">
                        <?php }  ?>
                        <?php } else { ?>
                        <img src="<?= base_url('public/img/img_profile.jpg') ?>"
                            class="rounded-circle border border-2 border-primary img-square" alt=""
                            style="max-width: 80px;">
                        <?php } ?>

                        <p class="mt-3"><strong><?= $player['shirt_number'] ?>
                                <?= $player['first_name'] . ' ' . $player['last_name'] ?></strong></p>
                        <p><?= $player['position_name_th'] ?></p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-dark me-1 mb-1 text-white px-4 fs-4 btn-sm" data-bs-toggle="modal"
                            data-bs-target="#home-player-state-<?= $player['id'] ?>"
                            onclick="getStatePlayer('<?= $player['id'] ?>', '<?= $data['tour_id'] ?>', '<?= $data['match_id'] ?>')">
                            <?= lang('Common.record_stats') ?>
                        </button>
                    </div>

                    <div id="home-player-state-<?= $player['id'] ?>" class="modal fade" tabindex="-1"
                        aria-labelledby="home-player-state-<?= $player['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header text-center">
                                    <h4 class="modal-title" id="myModalLabel">
                                        <?= lang('Common.record_player_stats') ?>
                                    </h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row justify-content-center">
                                        <div class="col-12 mb-3">
                                            <?php if (file_exists('./' . $player['image_path'])) { ?>
                                            <?php if ($player['image_path'] != '') { ?>
                                            <img src="<?= base_url($player['image_path']) ?>"
                                                class="rounded-circle border border-2 border-primary img-square my-2"
                                                alt="" style="max-width: 100px; min-width: auto;">
                                            <?php } else { ?>
                                            <img src="<?= base_url('public/img/img_profile.jpg') ?>"
                                                class="rounded-circle border border-2 border-primary img-square my-2"
                                                alt="" style="max-width: 100px;">
                                            <?php }  ?>
                                            <?php } else { ?>
                                            <img src="<?= base_url('public/img/img_profile.jpg') ?>"
                                                class="rounded-circle border border-2 border-primary img-square my-2"
                                                alt="" style="max-width: 100px;">
                                            <?php } ?>
                                            <h5>
                                                <strong><?= lang('Common.player_info') ?> : </strong>(<?= $player['shirt_number'] ?>)
                                                <?= $player['first_name'] . ' ' . $player['last_name'] ?>
                                            </h5>
                                        </div>
                                        <?php foreach ($state_list as $st_key => $state) { ?>
                                        <div class="col-7 text-center">
                                            <label for="" class="form-label"><?= $state ?></label>
                                            <div class="input-group mb-3">
                                                <button class="btn bg-danger-subtle text-danger" type="button"
                                                    onclick="minusValueState(`<?= $st_key ?>`,`<?= $player['id'] ?>`,`<?= $data['tour_id'] ?>`,`<?= $data['match_id'] ?>`)">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-minus  me-1 fs-4"></i>
                                                    </div>
                                                </button>
                                                <input type="text" class="form-control text-center"
                                                    id="state_<?= $st_key ?>_<?= $player['id'] ?>" name="" value="">
                                                <button class="btn bg-success-subtle text-success" type="button"
                                                    onclick="addValueState(`<?= $st_key ?>`,`<?= $player['id'] ?>`,`<?= $data['tour_id'] ?>`,`<?= $data['match_id'] ?>`)">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-plus me-1 fs-4"></i>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="modal-footer">

                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                </div>
                <div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
    <div class="tab-pane p-3" id="navpill-22" role="tabpanel">
        <div class="row">
            <?php foreach ($players_away as $key => $player) { ?>
            <div class="col-md-3 text-center">
                <div class="card shadow shadow-lg">
                    <div class="card-body">
                        <?php if (file_exists('./' . $player['image_path'])) { ?>
                        <?php if ($player['image_path'] != '') { ?>
                        <img src="<?= base_url($player['image_path']) ?>"
                            class="rounded-circle border border-2 border-primary img-square" alt=""
                            style="max-width: 80px; min-width: auto;">
                        <?php } else { ?>
                        <img src="<?= base_url('public/img/img_profile.jpg') ?>"
                            class="rounded-circle border border-2 border-primary img-square" alt=""
                            style="max-width: 80px;">
                        <?php }  ?>
                        <?php } else { ?>
                        <img src="<?= base_url('public/img/img_profile.jpg') ?>"
                            class="rounded-circle border border-2 border-primary img-square" alt=""
                            style="max-width: 80px;">
                        <?php } ?>

                        <p class="mt-3"><strong><?= $player['shirt_number'] ?>
                                <?= $player['first_name'] . ' ' . $player['last_name'] ?></strong></p>
                        <p><?= $player['position_name_th'] ?></p>
                    </div>
                    <div class="card-footer">
                        <button class="btn btn-dark me-1 mb-1 text-white px-4 fs-4 btn-sm" data-bs-toggle="modal"
                            data-bs-target="#home-player-state-<?= $player['id'] ?>"
                            onclick="getStatePlayer('<?= $player['id'] ?>', '<?= $data['tour_id'] ?>', '<?= $data['match_id'] ?>')">
                            <?= lang('Common.record_stats') ?>
                        </button>
                    </div>

                    <div id="home-player-state-<?= $player['id'] ?>" class="modal fade" tabindex="-1"
                        aria-labelledby="home-player-state-<?= $player['id'] ?>" aria-hidden="true">
                        <div class="modal-dialog modal-dialog-scrollable modal-lg modal-dialog-centered">
                            <div class="modal-content">
                                <div class="modal-header text-center">
                                    <h4 class="modal-title" id="myModalLabel">
                                        <?= lang('Common.record_player_stats') ?>
                                    </h4>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <div class="row justify-content-center">
                                        <div class="col-12 mb-3">
                                            <?php if (file_exists('./' . $player['image_path'])) { ?>
                                            <?php if ($player['image_path'] != '') { ?>
                                            <img src="<?= base_url($player['image_path']) ?>"
                                                class="rounded-circle border border-2 border-primary img-square my-2"
                                                alt="" style="max-width: 100px; min-width: auto;">
                                            <?php } else { ?>
                                            <img src="<?= base_url('public/img/img_profile.jpg') ?>"
                                                class="rounded-circle border border-2 border-primary img-square my-2"
                                                alt="" style="max-width: 100px;">
                                            <?php }  ?>
                                            <?php } else { ?>
                                            <img src="<?= base_url('public/img/img_profile.jpg') ?>"
                                                class="rounded-circle border border-2 border-primary img-square my-2"
                                                alt="" style="max-width: 100px;">
                                            <?php } ?>
                                            <h5>
                                                <strong><?= lang('Common.player_info') ?> : </strong>(<?= $player['shirt_number'] ?>)
                                                <?= $player['first_name'] . ' ' . $player['last_name'] ?>
                                            </h5>
                                        </div>
                                        <?php foreach ($state_list as $st_key => $state) { ?>
                                        <div class="col-7 text-center">
                                            <label for="" class="form-label"><?= $state ?></label>
                                            <div class="input-group mb-3">
                                                <button class="btn bg-danger-subtle text-danger" type="button"
                                                    onclick="minusValueState(`<?= $st_key ?>`,`<?= $player['id'] ?>`,`<?= $data['tour_id'] ?>`,`<?= $data['match_id'] ?>`)">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-minus  me-1 fs-4"></i>
                                                    </div>
                                                </button>
                                                <input type="text" class="form-control text-center"
                                                    id="state_<?= $st_key ?>_<?= $player['id'] ?>" name="" value="">
                                                <button class="btn bg-success-subtle text-success" type="button"
                                                    onclick="addValueState(`<?= $st_key ?>`,`<?= $player['id'] ?>`,`<?= $data['tour_id'] ?>`,`<?= $data['match_id'] ?>`)">
                                                    <div class="d-flex align-items-center">
                                                        <i class="ti ti-plus me-1 fs-4"></i>
                                                    </div>
                                                </button>
                                            </div>
                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>
                                <div class="modal-footer">

                                </div>
                            </div>
                            <!-- /.modal-content -->
                        </div>
                        <!-- /.modal-dialog -->
                    </div>
                </div>
                <div>
                </div>
            </div>
            <?php } ?>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    let stateList = < ? = json_encode($state_list) ? > ;

    function addValueState(state_key, player_id, tour_id, match_id) {
        let value = $(`#state_${state_key}_${player_id}`).val();

        if (value == '') {
            value = 1;
        } else {
            value++;
        }
        $(`#state_${state_key}_${player_id}`).val(value);
        saveStatePlayer(player_id, tour_id, match_id, state_key, value);
    }

    function minusValueState(state_key, player_id, tour_id, match_id) {
        let value = $(`#state_${state_key}_${player_id}`).val();

        if (value == '' || value <= 0) {
            value = 0;
        } else {
            value--;
        }
        $(`#state_${state_key}_${player_id}`).val(value);
        saveStatePlayer(player_id, tour_id, match_id, state_key, value);
    }

    function getStatePlayer(player_id, tour_id, match_id) {
        console.log(player_id, tour_id, match_id);
        $.ajax({
            method: "POST",
            url: "<?= base_url('admin/scout/get-player-state') ?>",
            data: {
                player_id: player_id,
                tour_id: tour_id,
                match_id: match_id
            },
            success: function (res) {
                // console.log(res);
                if (res) {
                    for (const [key, value] of Object.entries(stateList)) {
                        // console.log(key, value);
                        $(`#state_${key}_${player_id}`).val(res[key])
                    }
                } else {
                    // console.log(stateList);
                    for (const [key, value] of Object.entries(stateList)) {
                        // console.log(key, value);
                        $(`#state_${key}_${player_id}`).val(0)
                    }
                }
            }
        })
    }

    function saveStatePlayer(player_id, tour_id, match_id, stateKey, value) {
        console.log(stateKey, value);
        $.ajax({
            method: "POST",
            url: "<?= base_url('admin/scout/save-player-state') ?>",
            data: {
                player_id: player_id,
                tour_id: tour_id,
                match_id: match_id,
                state_key: stateKey,
                value: value
            },
            success: function (res) {

            }
        })
    }
</script>
<?php $this->endSection(); ?>