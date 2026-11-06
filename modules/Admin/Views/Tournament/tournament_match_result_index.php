<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <?php echo view("Modules\Admin\Views\Tournament\subnav.php"); ?>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="fas fa-trophy text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.competition_results') ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.manage_results_and_match_details') ?></p>
                </div>
            </div>

            <!-- <div class="d-flex gap-2">
                 <button id="btn-view-show" type="button" class="btn btn-light border rounded-pill px-3 py-2 fw-bold text-muted" onclick="showItemTournament()">
                    <i class="fas fa-sitemap me-2"></i> มุมมองทัวร์นาเมนต์
                </button>
            </div> -->
        </div>

        <div class="card border-0 shadow-sm mb-4" style="background-color: #f8f9fa; border-radius: var(--border-radius);">
            <div class="card-body p-3">
                <form id="filter_match" action="<?= base_url('admin/tournament/tournament-match-result/' . $tour_id) ?>" method="GET">
                    <div class="row align-items-center">
                        <div class="col-md-4">
                            <label for="round_name" class="form-label fw-bold text-muted small mb-1"><?= lang('Common.match_round') ?></label>
                            <?php if (!empty($round_name)) { ?>
                                <select name="round_name" id="round_name" class="form-select border-0 shadow-sm" onchange="$('#filter_match').submit()" style="border-radius: 8px;">
                                    <option value="">-- <?= lang('Common.all') ?> --</option>
                                    <?php foreach ($round_name as $round) { ?>
                                        <option value="<?= $round['match_round'] ?>" <?= (!empty($_GET['round_name']) && $_GET['round_name'] == $round['match_round']) ? 'selected' : '' ?>><?= $round['match_round'] ?></option>
                                    <?php } ?>
                                </select>
                            <?php } ?>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="tournament_list">
            <?php if (!empty($allMatches)): ?>
                <h5 class="fw-bold mb-3 ps-2 border-start border-4 border-primary" style="color: var(--secondary-color);"><?= !empty($_GET['round_name']) ? $_GET['round_name'] : lang('Common.all_matches') ?></h5>
                <div class="card border-0 shadow-sm" style="border-radius: 12px; overflow: hidden;">
                    <?php foreach ($allMatches as $index => $match): ?>
                        <?php
                            $team1Name = esc($match['home_team_name'] ?? '');
                            $team2Name = esc($match['away_team_name'] ?? '');

                            // TBD Fallback Logic
                            if (empty($team1Name) || $team1Name === 'TBD') {
                                $team1Name = lang('Common.team_number') . ' ' . ($index * 2 + 1);
                            }
                            if (empty($team2Name) || $team2Name === 'TBD') {
                                $team2Name = lang('Common.team_number') . ' ' . ($index * 2 + 2);
                            }

                            // Fallback Logo Logic
                            $defaultLogo = base_url('/public/img/jootball_logo.png');
                            $team1Logo = (!empty($match['home_team_logo']) && file_exists('./' . $match['home_team_logo'])) ? base_url($match['home_team_logo']) : $defaultLogo;
                            $team2Logo = (!empty($match['away_team_logo']) && file_exists('./' . $match['away_team_logo'])) ? base_url($match['away_team_logo']) : $defaultLogo;

                            // Status Logic
                            // 3 = Finished? Based on previous code logic.
                            // If status == 3, Green. Else Blue (Upcoming) or Red (Live - if we want to keep that distinction)
                            // User request implies "Finished" (Green) vs "Upcoming" (Blue) logic.
                            // Assuming status 3 is Finished.
                            $status = ($match['match_status'] == 3) ? 'FINISHED' : 'UPCOMING';
                            // If user explicitly wants strict check like match_setup, we might need to check scores too,
                            // but usually status 3 is the definitive DB flag.
                            $statusText = ($status == 'FINISHED') ? lang('Common.match_finished') : lang('Common.match_upcoming');
                            $statusBg = ($status == 'FINISHED') ? '#13DEB9' : '#16478D';

                            $matchDate = ($match['date'] && $match['date'] != '0000-00-00') ? date('d M', strtotime($match['date'])) : lang('Common.to_be_determined');
                            $matchTime = ($match['time'] && $match['time'] != '00:00:00') ? date('H:i', strtotime($match['time'])) : '-';

                            $score1 = ($match['team1_score'] !== null && $match['team1_score'] !== '') ? $match['team1_score'] : '-';
                            $score2 = ($match['team2_score'] !== null && $match['team2_score'] !== '') ? $match['team2_score'] : '-';
                            $scoreText = ($status == 'FINISHED') ? $score1 . ' - ' . $score2 : ($matchDate . '<br><span style="opacity:0.8">' . $matchTime . '</span>');
                        ?>

                        <div style="margin-bottom: 12px;">
                            <div class="d-flex align-items-center justify-content-between p-4 rounded-lg" style="gap: 15px; background: #f8fafb; border-left: 4px solid #16478D; transition: all 0.3s ease; border-radius: 8px;">

                                <!-- Team 1 -->
                                <div class="text-center" style="flex: 0 0 auto; min-width: 90px;">
                                    <div style="position: relative; width: 48px; height: 48px; margin: 0 auto 8px; border-radius: 8px; overflow: hidden; border: 2px solid #16478D; background: white;">
                                        <img src="<?= $team1Logo ?>" alt="<?= $team1Name ?>" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                    <div class="small fw-bold text-truncate" style="color: #16478D; font-size: 0.85rem; line-height: 1.2; max-width: 100px;"><?= $team1Name ?></div>
                                </div>

                                <!-- Status Badge & Center Info -->
                                <div class="text-center" style="flex: 0 0 auto; min-width: 120px;">
                                    <span class="badge rounded-pill text-white small mb-2" style="background-color: <?= $statusBg ?>; display: inline-block; padding: 8px 14px; font-weight: 600; font-size: 0.75rem; letter-spacing: 0.5px;">
                                        <?= $statusText ?>
                                    </span>
                                    <div class="small" style="color: #16478D; font-weight: 500; font-size: 0.85rem; line-height: 1.4;">
                                        <?= $scoreText ?>
                                    </div>
                                </div>

                                <!-- Team 2 -->
                                <div class="text-center" style="flex: 0 0 auto; min-width: 90px;">
                                    <div style="position: relative; width: 48px; height: 48px; margin: 0 auto 8px; border-radius: 8px; overflow: hidden; border: 2px solid #16478D; background: white;">
                                        <img src="<?= $team2Logo ?>" alt="<?= $team2Name ?>" class="w-100 h-100" style="object-fit: cover;">
                                    </div>
                                    <div class="small fw-bold text-truncate" style="color: #16478D; font-size: 0.85rem; line-height: 1.2; max-width: 100px;"><?= $team2Name ?></div>
                                </div>

                                <!-- Actions Dropdown -->
                                <div class="dropdown" style="flex: 0 0 auto;">
                                    <button class="btn btn-light btn-sm rounded-circle shadow-sm" type="button" data-bs-toggle="dropdown" style="color: #16478D;">
                                        <i class="ti ti-settings"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow">
                                        <li><a class="dropdown-item py-2" href="<?= base_url('/admin/tournament/tournament-match-result-manage/' . $match['tour_id'] . '/' . $match['match_id']) ?>"><i class="fas fa-edit me-2 text-warning"></i> <?= lang('Common.edit_match_result') ?></a></li>
                                        <li><a class="dropdown-item py-2" href="<?= base_url('/admin/tournament/tournament-match-detail-manage/' . $match['tour_id'] . '/' . $match['match_id']) ?>"><i class="fas fa-info-circle me-2 text-info"></i> <?= lang('Common.details') ?></a></li>
                                        <li><hr class="dropdown-divider"></li>
                                        <li onclick="deletedMatch('<?= $match['match_id'] ?>')"><span class="dropdown-item py-2 text-danger cursor-pointer"><i class="fas fa-trash-alt me-2"></i> <?= lang('Common.delete_match') ?></span></li>
                                    </ul>
                                </div>

                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="text-center py-5">
                    <div class="mb-3">
                        <i class="fas fa-inbox text-muted" style="font-size: 3rem; opacity: 0.3;"></i>
                    </div>
                    <h5 class="text-muted fw-bold"><?= lang('Common.no_match_results') ?></h5>
                    <p class="text-muted small"><?= lang('Common.try_other_filter_or_no_matches') ?></p>
                </div>
            <?php endif; ?>
        </div>

        <div id="tournament_doubleElimination" style="display: none;">
            <div id="doubleElimination" class="overflow-auto py-4">
                <div class="demo"></div>
            </div>
        </div>
    </div>
</div>

<style>
    .hover-bg-light:hover { background-color: #f8f9fa; }
    .transition-hover { transition: background-color 0.2s ease; }
    .cursor-pointer { cursor: pointer; }
</style>

<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
<script>
    var listBracket = <?php echo json_encode($listBracket) ?>;
    var showTournament = true;
    // console.log(listBracket);

    function parseResults(results) {
        return results.map(round =>
            round.map(match =>
                match.map(score => parseInt(score))
            )
        );
    }

    const minimalData = {
        teams: listBracket.teams,
        results: parseResults(listBracket.results)
    };

    $(document).ready(function() {
        $('div#doubleElimination .demo').bracket({
            init: minimalData,
            teamWidth: 100,
            scoreWidth: 50,
            matchMargin: 60,
            roundMargin: 40,
        });
        showItemTournament();
    });

    const showItemTournament = () => {
        if (showTournament) {
            $('#tournament_list').css('display', 'block');
            $('#tournament_doubleElimination').css('display', 'none');
            showTournament = false;
            $('#btn-view-show').empty();
            $('#btn-view-show').html(`<i class="ti ti-tournament" style="font-size: 16px;"></i>`);
        } else {
            showTournament = true;
            $('#tournament_list').css('display', 'none');
            $('#tournament_doubleElimination').css('display', 'block');
            $('#btn-view-show').empty();
            $('#btn-view-show').html(`<i class="ti ti-list" style="font-size: 16px;"></i>`);
        }
    }
    $(".item").click(function() {
        $("#listDetail").modal("show");
    })

    const langConfirmDeleteMatch = '<?= lang("Common.confirm_delete_match") ?>';
    const langDeleteMatchWarning = '<?= lang("Common.delete_match_warning") ?>';
    const langConfirm = '<?= lang("Common.confirm") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';

    function deletedMatch(id) {
        Swal.fire({
            title: langConfirmDeleteMatch,
            text: langDeleteMatchWarning,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#3085d6",
            cancelButtonColor: "#d33",
            confirmButtonText: langConfirm,
            cancelButtonText: langCancel,
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('admin/tournament/delete-tournament-match-result') ?>",
                    data: {
                        id: id
                    },
                    success: function(res) {
                        Swal.fire({
                            position: "center",
                            icon: res.status,
                            title: res.msg,
                            showConfirmButton: false,
                            timer: 1500
                        }).then(() => {
                            window.location.reload();
                        });
                    }
                });
            }
        });
    }
</script>
<?php $this->endSection() ?>