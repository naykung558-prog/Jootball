<?php foreach ($events as $row) { ?>
    <div class="row align-items-center my-3 position-relative event-row">
        <!-- Home Team Event -->
        <div class="col-5 text-end">
            <?php if ($row['team_id'] == $data['team1_id']) { ?>
                <div class="event-card p-2 rounded shadow-sm bg-white border-start border-4 border-primary">
                    <div class="d-flex align-items-center justify-content-end gap-2">
                        <div>
                            <h6 class="fw-bold mb-0 text-dark small"><?= $row['pl1_first_name'] . ' ' . $row['pl1_last_name'] ?></h6>
                            <?php if (!empty($row['pl2_first_name'])) { ?>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                    <i class="ti ti-arrow-right text-success"></i> <?= $row['pl2_first_name'] . ' ' . $row['pl2_last_name'] ?>
                                </small>
                            <?php } ?>
                        </div>
                        <span class="fs-4"><?= $row['icon'] ?></span>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Minute -->
        <div class="col-2 text-center position-relative">
            <div class="badge rounded-pill bg-light text-dark border shadow-sm py-2 px-3 fw-bold" style="font-size: 0.9rem; z-index: 2; position: relative;">
                <?= $row['minute'] ?>'
            </div>
            <!-- Vertical Line -->
            <div class="position-absolute top-0 bottom-0 start-50 translate-middle-x bg-light" style="width: 2px; z-index: 1;"></div>
        </div>

        <!-- Away Team Event -->
        <div class="col-5 text-start">
            <?php if ($row['team_id'] == $data['team2_id']) { ?>
                <div class="event-card p-2 rounded shadow-sm bg-white border-end border-4 border-danger">
                    <div class="d-flex align-items-center justify-content-start gap-2">
                        <span class="fs-4"><?= $row['icon'] ?></span>
                        <div>
                            <h6 class="fw-bold mb-0 text-dark small"><?= $row['pl1_first_name'] . ' ' . $row['pl1_last_name'] ?></h6>
                            <?php if (!empty($row['pl2_first_name'])) { ?>
                                <small class="text-muted d-block" style="font-size: 0.75rem;">
                                    <i class="ti ti-arrow-right text-success"></i> <?= $row['pl2_first_name'] . ' ' . $row['pl2_last_name'] ?>
                                </small>
                            <?php } ?>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>

        <!-- Delete Action (Absolute positioned or integrated) -->
        <div class="position-absolute end-0 top-50 translate-middle-y" style="width: auto; right: 10px;">
            <button class="btn btn-sm btn-link text-danger p-0 opacity-50 hover-opacity-100" onclick="deletedEvent('<?= $row['match_event_id'] ?>', '<?= $row['match_id'] ?>')" title="<?= lang('Common.delete_event') ?>">
                <i class="ti ti-trash fs-5"></i>
            </button>
        </div>
    </div>
<?php } ?>

<style>
    .event-row:hover .btn-link {
        opacity: 1 !important;
    }
    .hover-opacity-100:hover {
        opacity: 1 !important;
    }
</style>