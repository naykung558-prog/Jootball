<?php $this->extend('template/home') ?>
<?php $this->section('content'); ?>


<section id="result" class="result-section">
    <h1 class="mt-5" align="center"><?= lang('Common.statistics') ?></h1>


</section>

<section id="home" class="hero-section hero-khelo">
    <div class="hero-bg-wrapper">
        <div class="hero-bg-overlay"></div>
        <div class="hero-pattern">
            <div class="pattern-circle c1"></div>
            <div class="pattern-circle c2"></div>
            <div class="pattern-circle c3"></div>
        </div>
        <div class="hero-shape shape-left"></div>
        <div class="hero-shape shape-right"></div>
    </div>

    <div class="container-fluid px-0">

        <div class="card shadow-sm border-0 rounded-4">
            <div class="card-body">

            <!-- League -->
            <h6 class="text-center fw-semibold mb-4 text-white opacity-75" id="team-name">
                <?php echo session()->get('language')=='th'?$tour_info['name']:$tour_info['name_en'];?>
            </h6>

            <div class="tab-content mt-2">
                <div class="tab-pane fade show active" id="ranking">
                    <div id="rankingContainer"></div>
                </div>
            </div>

            <div class="stats-wrapper container">

                <div class="row g-4 mb-2">

                        <!-- Main Stats -->
                        <div class="row">

                            <div class="col-6 col-md-3">
                                <div class="stat-card stat-box">
                                    <div class="stat-label"><?= lang('Common.games') ?></div>
                                    <div class="stat-value" id="team-games">0</div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="stat-card stat-box">
                                    <div class="stat-label"><?= lang('Common.coaches') ?></div>
                                    <div class="stat-value" id="coach-count">0</div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="stat-card stat-box">
                                    <div class="stat-label"><?= lang('Common.teams') ?></div>
                                    <div class="stat-value" id="team-count">0</div>
                                </div>
                            </div>

                            <div class="col-6 col-md-3">
                                <div class="stat-card stat-box">
                                    <div class="stat-label"><?= lang('Tournament.for') ?></div>
                                    <div class="stat-value" id="team-for">0</div>
                                </div>
                            </div>

                        </div>

                </div>

            </div>

            <div class="row">
                <div class="col-md-6 col-12">
                    &nbsp;
                </div>
                <div class="col-md-6 col-12">
                    <button id="btn_player" type="button" class="btn btn-info" onclick="$('#playerModal').modal('show');">
                        <?= lang('Common.players') ?> (<span id="player-count" >-</span>)
                    </button>
                </div>
            </div>

        </div>


        <div class="modal fade" id="playerModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                    Players
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <div class="players-grid" id="playersGrid"></div>

                </div>

                <!--
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
                -->

                </div>
            </div>
        </div>

        <div class="modal fade" id="eventModal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">
                    Match Story
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">

                    <ul class="list-group list-group-flush">

                    </ul>

                </div>

                <!--
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                </div>
                -->

                </div>
            </div>
        </div>

    </div>

    <div class="scroll-down-indicator">
        <span class="scroll-line"></span>
        <span class="scroll-text"><?= lang('Common.scroll') ?></span>
    </div>
</section>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?= $this->include('Modules\Home\Views\partials\statistics_tour\styles') ?>

<?= $this->include('Modules\Home\Views\partials\statistics_tour\scripts') ?>
<?php $this->endSection(); ?>
