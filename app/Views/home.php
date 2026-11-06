<?=$this->extend('templates/main');?>
<?=$this->section("style")?>
<?=$this->endSection()?>

<?=$this->section('content');?>
<?php

echo 'Index Account. <br>';

echo $aa;

?>
<?=$this->endSection();?>

<?=$this->section("scripts")?>
<script src="<?= base_url('public/js/script.js') ?>"></script>
<?=$this->endSection()?>