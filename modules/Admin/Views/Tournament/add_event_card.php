<form id="save-event-form">
    <input type="hidden" name="player_1_id" value="<?= @$data['player_id'] ?>">
    <input type="hidden" name="team_id" value="<?= @$data['team_id'] ?>">
    <input type="hidden" name="tour_id" value="<?= @$data['tour_id'] ?>">
    <input type="hidden" name="match_id" value="<?= @$data['match_id'] ?>">

    <style>
        :root {
            --primary-color: #16478D;
            --secondary-color: #13DEB9;
            --card-shadow: 0 2px 12px rgba(0, 0, 0, 0.08);
        }

        .event-form-container {
            background: linear-gradient(135deg, #f5f7fa 0%, #ffffff 100%);
            border-radius: 12px;
            padding: 2rem;
        }

        .player-header {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            display: flex;
            align-items: center;
            gap: 1.5rem;
            border-left: 4px solid var(--primary-color);
        }

        .player-avatar {
            position: relative;
            width: 100px;
            height: 100px;
            flex-shrink: 0;
        }

        .player-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 12px;
            border: 3px solid var(--primary-color);
            object-fit: cover;
            box-shadow: 0 4px 12px rgba(22, 71, 141, 0.2);
        }

        .player-shirt-badge {
            position: absolute;
            bottom: -8px;
            right: -8px;
            width: 45px;
            height: 45px;
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%);
            color: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: 1.1rem;
            border: 3px solid white;
            box-shadow: 0 4px 12px rgba(22, 71, 141, 0.3);
        }

        .player-info h5 {
            color: var(--primary-color);
            font-weight: 700;
            margin: 0 0 0.5rem 0;
            font-size: 1.3rem;
        }

        .player-info .badge {
            background-color: #f0f4ff;
            color: var(--primary-color);
            border: 1px solid var(--primary-color);
            font-size: 0.85rem;
            padding: 6px 12px;
            font-weight: 600;
        }

        .minute-input-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
            border: 2px solid #f1f5f9;
        }

        .minute-input-section .input-group-text {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%);
            color: white !important;
            border: none;
            font-weight: 700;
        }

        .minute-input-section input {
            border: 1px solid #e9ecef;
            font-size: 1.2rem;
            font-weight: 700;
        }

        .minute-input-section input:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.1);
        }

        .event-types-section {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: var(--card-shadow);
            margin-bottom: 2rem;
        }

        .event-types-title {
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1.5rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .event-types-title i {
            font-size: 1.3rem;
        }

        .event-option {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 12px;
            padding: 1.5rem !important;
            transition: all 0.3s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .event-option::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            opacity: 0;
            transition: opacity 0.3s ease;
            z-index: -1;
        }

        .event-option:hover {
            border-color: var(--primary-color);
            box-shadow: 0 8px 24px rgba(22, 71, 141, 0.15);
            transform: translateY(-4px);
        }

        .event-option:hover::before {
            opacity: 0.05;
        }

        .event-option span {
            transition: all 0.3s ease;
        }

        .event-option .event-icon {
            font-size: 2.5rem;
            margin-bottom: 0.75rem;
            display: block;
        }

        .event-option .event-name {
            font-size: 0.95rem;
            font-weight: 700;
            color: var(--primary-color);
        }

        .btn-check:checked+.event-option {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 12px 30px rgba(22, 71, 141, 0.3);
        }

        .btn-check:checked+.event-option .event-icon,
        .btn-check:checked+.event-option .event-name {
            color: white;
        }
    </style>

    <div class="event-form-container">
        <!-- Player Header -->
        <div class="player-header">
            <div class="player-avatar">
                <?php if (!empty($player['image_path'])) { ?>
                <img src="<?= base_url($player['image_path']) ?>"
                    alt="<?= $player['first_name'] . ' ' . $player['last_name'] ?>"
                    onerror="this.src='<?= base_url('public/img/img_profile.jpg') ?>'">
                <?php } else { ?>
                <img src="<?= base_url('public/img/img_profile.jpg') ?>" alt="">
                <?php } ?>
                <div class="player-shirt-badge"><?= $player['shirt_number'] ?? '#' ?></div>
            </div>
            <div class="player-info">
                <h5><?= $player['first_name'] . ' ' . $player['last_name'] ?></h5>
                <span class="badge">
                    <i class="ti ti-jersey me-1"></i>
                    <?= $player['position_name'] ?>
                </span>
            </div>
        </div>

        <!-- Minute Input -->
        <div class="minute-input-section">
            <label class="form-label fw-bold text-primary mb-3" style="color: var(--primary-color);">
                <i class="ti ti-clock me-2"></i> <?= lang('Common.event_minute') ?>
            </label>
            <div class="input-group">
                <span class="input-group-text"><i class="ti ti-clock me-2"></i> <?= lang('Common.minute') ?></span>
                <input type="number" id="minute" name="minute" class="form-control text-center" placeholder="00"
                    required min="0" max="150">
                <span class="input-group-text" style="background: transparent; border-left: none;"><?= lang('Common.minute') ?></span>
            </div>
        </div>

        <!-- Event Types Selection -->
        <div class="event-types-section">
            <h6 class="event-types-title">
                <i class="ti ti-soccer"></i> <?= lang('Common.select_event_type') ?>
            </h6>
            <div class="row g-3">
                <?php foreach ($event_types as $type) { ?>
                <div class="col-6 col-md-4">
                    <input type="radio" class="btn-check" name="event_id" id="options_<?= $type['id'] ?>"
                        autocomplete="off" value="<?= $type['id'] ?>" required>
                    <label
                        class="btn btn-outline-light text-dark w-100 h-100 d-flex flex-column align-items-center justify-content-center event-option"
                        for="options_<?= $type['id'] ?>">
                        <span class="event-icon"><?= $type['icon'] ?></span>
                        <span class="event-name"><?= $type['name'] ?></span>
                    </label>
                </div>
                <?php } ?>
            </div>
        </div>
    </div>

    <!-- Next Button -->
    <div class="mt-4">
        <button type="button" class="btn w-100 py-3 fw-bold shadow-sm text-white rounded-pill"
            style="background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%); border: none; font-size: 1.05rem;"
            data-bs-toggle="modal" data-bs-target="#second_player_modal">
            <?= lang('Common.next') ?> <i class="ti ti-arrow-right ms-2"></i>
        </button>
    </div>

    <!-- Modal for Secondary Player Selection -->
    <div class="modal fade" id="second_player_modal" data-bs-keyboard="false" tabindex="-1"
        aria-labelledby="second_player_modal" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-lg">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px;">
                <div class="modal-header text-white"
                    style="background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%); border-radius: 16px 16px 0 0;">
                    <h5 class="modal-title fw-bold" id="myLargeModalLabel">
                        <i class="ti ti-user-plus me-2"></i> <?= lang('Common.select_related_player') ?>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body p-4">
                    <!-- Playing Field Players -->
                    <div class="mb-4">
                        <div
                            style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 2px solid #f1f5f9;">
                            <i class="ti ti-users" style="color: var(--primary-color); font-size: 1.3rem;"></i>
                            <h6 class="fw-bold mb-0" style="color: var(--primary-color);"><?= lang('Common.playing_field_players') ?></h6>
                        </div>
                        <div class="row g-3">
                            <?php foreach ($real_player as $player) { ?>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="player_2_id"
                                    id="options_real_<?= $player['player_id'] ?>" autocomplete="off"
                                    value="<?= $player['player_id'] ?>">
                                <label
                                    class="btn btn-outline-primary w-100 text-truncate p-3 small fw-600 player-option-label"
                                    for="options_real_<?= $player['player_id'] ?>"
                                    style="border: 2px solid #e9ecef; border-radius: 12px; transition: all 0.3s ease;">
                                    <i class="ti ti-user me-1"></i>
                                    <?= $player['first_name'] . ' ' . $player['last_name'] ?>
                                </label>
                            </div>
                            <?php } ?>
                        </div>
                    </div>

                    <!-- Reserve Players -->
                    <div>
                        <div
                            style="display: flex; align-items: center; gap: 0.5rem; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 2px solid #f1f5f9;">
                            <i class="ti ti-user-plus" style="color: var(--primary-color); font-size: 1.3rem;"></i>
                            <h6 class="fw-bold mb-0" style="color: var(--primary-color);"><?= lang('Common.reserve_players') ?></h6>
                        </div>
                        <div class="row g-3">
                            <?php foreach ($sub_player as $player) { ?>
                            <div class="col-6 col-md-4">
                                <input type="radio" class="btn-check" name="player_2_id"
                                    id="options_sub_<?= $player['player_id'] ?>" autocomplete="off"
                                    value="<?= $player['player_id'] ?>">
                                <label
                                    class="btn btn-outline-primary w-100 text-truncate p-3 small fw-600 player-option-label"
                                    for="options_sub_<?= $player['player_id'] ?>"
                                    style="border: 2px solid #e9ecef; border-radius: 12px; transition: all 0.3s ease;">
                                    <i class="ti ti-user-check me-1"></i>
                                    <?= $player['first_name'] . ' ' . $player['last_name'] ?>
                                </label>
                            </div>
                            <?php } ?>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-light" style="border-radius: 0 0 16px 16px;">
                    <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">
                        <i class="ti ti-x me-1"></i> <?= lang('Common.cancel') ?>
                    </button>
                    <button type="button" class="btn text-white fw-bold px-4 rounded-pill"
                        style="background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%); border: none;"
                        id="save-event-btn" data-bs-dismiss="modal" onclick="saveEvent()">
                        <i class="ti ti-check me-1"></i> <?= lang('Common.save') ?>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <style>
        .player-option-label:hover {
            border-color: var(--primary-color) !important;
            box-shadow: 0 4px 12px rgba(22, 71, 141, 0.15);
            transform: translateY(-2px);
        }

        #second_player_modal .btn-check:checked+.player-option-label {
            background: linear-gradient(135deg, var(--primary-color) 0%, #1a5aad 100%);
            color: white;
            border-color: var(--primary-color);
            box-shadow: 0 8px 20px rgba(22, 71, 141, 0.3);
        }

        #second_player_modal .btn-check:checked+.player-option-label i {
            color: white;
        }
    </style>
</form>