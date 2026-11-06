<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <div class="p-4">
        <?= view('Modules\Academy\Views\_football_stats_content', [
            'stats' => $stats,
            'academy' => $academy ?? null,
            'filterAction' => base_url('academy/football-stats/' . (int)($academy['id'] ?? 0))
        ]) ?>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<!-- Scripts are included in the partial -->
<?php $this->endSection(); ?>

