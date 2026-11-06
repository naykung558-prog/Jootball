<?php $this->extend('template/admin_layout') ?>
<?php $this->section('content'); ?>

<?= $this->include('Modules\Admin\Views\Tournament\partials\tournament_program\styles') ?>

<div class="card border-0 shadow-sm">
<div class="card-body p-4">

<?= view("Modules\Admin\Views\Tournament\subnav.php"); ?>

<ul class="nav nav-tabs">
  <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#home"><?= lang('Common.competition') ?></button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#ranking"><?= lang('Common.score_table') ?></button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#map"><?= lang('Common.match_bracket') ?></button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#player"><?= lang('Common.players') ?></button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#summary"><?= lang('Common.match_summary') ?></button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#lineupMVP"><?= lang('Common.best_players') ?></button></li>
  <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#finalRanking">Ranking</button></li>
</ul>

<div class="tab-content mt-2">

  <div class="tab-pane fade show active" id="home">
    <!-- Match Filters -->
    <div class="row g-2 mb-4 p-3 bg-light rounded shadow-sm border">
      <div class="col-md-4">
        <label class="form-label fw-bold text-primary small mb-1"><i class="ti ti-map-pin me-1"></i><?= lang('Common.stadium') ?></label>
        <select id="filterStadium" class="form-select form-select-sm shadow-none border-secondary-subtle">
          <option value="">-- All Stadiums --</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold text-primary small mb-1"><i class="ti ti-calendar me-1"></i><?= lang('Common.date') ?></label>
        <select id="filterDate" class="form-select form-select-sm shadow-none border-secondary-subtle">
          <option value="">-- All Dates --</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label fw-bold text-primary small mb-1"><i class="ti ti- tournament me-1"></i><?= lang('Tournament.round_match') ?></label>
        <select id="filterRound" class="form-select form-select-sm shadow-none border-secondary-subtle">
          <option value="">-- All Rounds --</option>
        </select>
      </div>
    </div>
    <div id="matchContainer"></div>
  </div>

  <div class="tab-pane fade" id="ranking">
    <div id="rankingContainer"></div>
  </div>

  <div class="tab-pane fade" id="map">
    <div id="bracketWrapper">
      <svg id="bracketLines"></svg>
      <div id="bracketContainer"></div>
    </div>
  </div>

  <div class="tab-pane fade" id="player">
    <div class="players-grid" id="playersGrid"></div>
  </div>

  <div class="tab-pane fade" id="summary">
    <div id="summaryContainer" class="pt-4 px-2">

      <div class="row g-4">
        <!-- Games -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card blue">
                <div class="stat-icon-wrapper grad-blue">
                    <i class="fa-solid fa-futbol"></i>
                </div>
                <div class="stat-value" id="sum-math">-</div>
                <div class="stat-label"><?= lang('Common.games') ?></div>
            </div>
        </div>

        <!-- Registered Teams -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card purple">
                <div class="stat-icon-wrapper grad-purple">
                    <i class="fa-solid fa-users"></i>
                </div>
                <div class="stat-value" id="sum-team">-</div>
                <div class="stat-label"><?= lang('Common.registered_teams') ?></div>
            </div>
        </div>

        <!-- Players -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card green">
                <div class="stat-icon-wrapper grad-green">
                    <i class="fa-solid fa-user-check"></i>
                </div>
                <div class="stat-value" id="sum-player">-</div>
                <div class="stat-label"><?= lang('Common.players') ?></div>
            </div>
        </div>

        <!-- Coaches -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card cyan">
                <div class="stat-icon-wrapper grad-cyan">
                    <i class="fa-solid fa-user-tie"></i>
                </div>
                <div class="stat-value" id="sum-coach">-</div>
                <div class="stat-label"><?= lang('Common.coaches') ?></div>
            </div>
        </div>

        <!-- Sum Card -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card orange">
                <div class="stat-icon-wrapper grad-orange">
                    <i class="fa-solid fa-square"></i>
                </div>
                <div class="stat-value" id="sum-card">-</div>
                <div class="stat-label"><?= lang('Tournament.sum_card') ?></div>
            </div>
        </div>

        <!-- Best Player -->
        <div class="col-6 col-md-4 col-xl-2">
            <div class="stat-card red">
                <div class="stat-icon-wrapper grad-red">
                    <i class="fa-solid fa-award"></i>
                </div>
                <div class="stat-value" id="sum-best-player">-</div>
                <div class="stat-label"><?= lang('Tournament.sum_best_player') ?></div>
            </div>
        </div>
      </div>

    </div>
  </div>
  <!-- END tab summary -->

  <div class="tab-pane fade" id="lineupMVP">

    <div class="container-fluid mt-4">
      <div class="row g-3">
        <!-- LEFT -->
        <div class="col-md-5">
          <div class="card mb-3">
            <div class="card-header fw-bold"><?= lang('Common.add_player') ?></div>
            <div class="card-body">
              <!-- ✅ เพิ่มใหม่ -->
              <label><?= lang('Common.number_of_players') ?></label>
              <input type="number" id="maxPlayers" class="form-control mb-2" value="7" min="1" max="11">

              <label><?= lang('Common.teams') ?></label>
              <select id="teamSelect" class="form-select mb-2">
                <option value="">-- <?= lang('Common.select_team') ?> --</option>
              </select>

              <label><?= lang('Common.player_list') ?></label>
              <select id="playerSelect" class="form-select mb-2" disabled>
                <option value="">-- <?= lang('Common.select_player') ?> --</option>
              </select>

              <label><?= lang('Common.zone') ?></label>
              <select id="playerPos" class="form-select mb-3">
                <option value="Goalkeeper">Goalkeeper</option>
                <option value="Defender">Defender</option>
                <option value="Midfielder">Midfielder</option>
                <option value="Attacker">Attacker</option>
                <option value="manager">Manager</option>
              </select>

              <button id="addPlayer" class="btn btn-success w-100"><?= lang('Common.add') ?></button>
              <button id="saveTeam" class="btn btn-warning w-100 mt-2"><?= lang('Common.save_latest_team') ?></button>
              <button id="loadMock" style="display:none" class="btn btn-outline-primary w-100 mt-2"><?= lang('Common.load_saved_team') ?></button>
            </div>
          </div>

          <div class="card">
            <div class="card-header fw-bold"><?= lang('Common.player_detail') ?></div>
            <div id="playerInfo">
              <div class="empty-info"><?= lang('Common.click_player_on_field') ?></div>
            </div>
          </div>
        </div>

        <!-- RIGHT -->
        <div class="col-md-7">
          <div class="card">
            <div class="card-header fw-bold"><?= lang('Common.field') ?></div>
            <div class="card-body">
              <div id="footballField">
                <div class="penalty-top"></div>
                <div class="penalty-bottom"></div>
                <div class="zone fw" data-zone="fw"></div>
                <div class="zone mf" data-zone="mf"></div>
                <div class="zone df" data-zone="df"></div>
                <div class="zone gk" data-zone="gk"></div>
              </div>
              <div class="manager-zone">
                <div class="zone manager" data-zone="manager"></div>
              </div>
            </div>
          </div>
        </div>

      </div>
    </div>
    <!-- END container mt-4 -->

  </div>

  <div class="tab-pane fade" id="finalRanking">
    <div class="container-fluid mt-4">
      <div class="card">
        <div class="card-header fw-bold d-flex justify-content-between align-items-center">
          <span>Tournament Ranking</span>
          <button id="saveRanking" class="btn btn-primary px-4 py-2">Save Ranking</button>
        </div>
        <div class="card-body">
          <div class="row mb-4">
            <div class="col-md-4">
              <label class="fw-bold mb-2">Number of Rankings</label>
              <div class="input-group">
                <input type="number" id="numRanking" class="form-control" value="<?= $tournament['number_ranking'] ?? 4 ?>" min="1" max="32">
                <button id="updateRankingRows" class="btn btn-outline-secondary">Update Ranking</button>
              </div>
            </div>
          </div>
          
          <div id="rankingRowsContainer">
            <!-- Ranking rows will be generated here -->
          </div>
        </div>
      </div>
    </div>
  </div>

</div>
</div>

<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
<?= $this->include('Modules\Admin\Views\Tournament\partials\tournament_program\scripts') ?>


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


<?php $this->endSection() ?>
