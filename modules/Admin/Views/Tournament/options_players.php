<?php foreach ($players as $row) { ?>
    <option value="<?= $row['id'] ?>"> (<?= $row['shirt_number'] ?>) <?= $row['first_name'] . ' ' . $row['last_name'] ?></option>
<?php } ?>