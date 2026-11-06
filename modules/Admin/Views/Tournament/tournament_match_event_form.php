<tr>
    <td>
        <input id="minute_<?= $index ?>" type="text" name="events[<?= $index ?>][minute]" class="form-control number-3-digit">
    </td>
    <td>
        <select id="team_id_<?= $index ?>" name="events[<?= $index ?>][team_id]" class="form-select" onchange="getPlayerOption(this.value, '<?= $index ?>', '<?= $data['tour_id'] ?>', '<?= $data['match_id'] ?>')" required>
            <option value=""> -- <?= lang('Common.select') ?> -- </option>
            <option value="<?= $data['team1_id'] ?>"><?= lang('Common.home_team') ?></option>
            <option value="<?= $data['team2_id'] ?>"><?= lang('Common.away_team') ?></option>
        </select>
    </td>
    <td>
        <select id="event_<?= $index ?>" name="events[<?= $index ?>][event_id]" class="form-select">
            <option value=""> -- <?= lang('Common.select') ?> -- </option>
            <?php foreach ($event_types as $type) { ?>
                <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
            <?php } ?>
        </select>
    </td>
    <td>
        <select id="player_1_id_<?= $index ?>" name="events[<?= $index ?>][player_1_id]" class="form-select">
            <option value=""> -- <?= lang('Common.select') ?> -- </option>
        </select>
    </td>
    <td>
        <select id="player_2_id_<?= $index ?>" name="events[<?= $index ?>][player_2_id]" class="form-select">
            <option value=""> -- <?= lang('Common.select') ?> -- </option>
        </select>
    </td>
    <td>
        <button type="button" class="btn btn-danger" onclick="this.parentElement.parentElement.remove()"><i class="ti ti-trash"></i></button>
    </td>
</tr>