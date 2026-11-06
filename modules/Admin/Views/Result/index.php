<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>

<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="card-title fw-bold mb-1"><?= lang('Common.standings') ?></h4>
                <p class="text-muted mb-0"><?= lang('Common.standings_description') ?></p>
            </div>
            <div class="d-none d-md-block">
                <i class="ti ti-trophy fs-1 text-warning opacity-50"></i>
            </div>
        </div>
    </div>
</div>

<div class="container-fluid p-0">
    <?php
    class TournamentStandings {
        private $teamNames = [];
        private $standings = [];
        private $matches = [];

        public function __construct($matches) {
            $this->matches = $matches;
            $this->buildTeamNames($matches);
            $this->calculateStandings($matches);
        }

        private function buildTeamNames($matches) {
            foreach ($matches as $match) {
                $this->teamNames[$match['team1_id']] = $match['team1_name'];
                $this->teamNames[$match['team2_id']] = $match['team2_name'];
            }
        }

        private function initializeTeam($teamId, $group) {
            if (!isset($this->standings[$teamId])) {
                $this->standings[$teamId] = [
                    'PL' => 0,
                    'W' => 0,
                    'D' => 0,
                    'L' => 0,
                    'G' => 0,
                    'GA' => 0,
                    'GD' => 0,
                    'Pts' => 0,
                    'group' => $group ?: 'No Group'
                ];
            }
        }

        private function updateTeamStats($teamId, $group, $scored, $conceded, $win, $draw, $loss) {
            $this->initializeTeam($teamId, $group);

            $this->standings[$teamId]['PL']++;
            $this->standings[$teamId]['W'] += $win;
            $this->standings[$teamId]['D'] += $draw;
            $this->standings[$teamId]['L'] += $loss;
            $this->standings[$teamId]['G'] += $scored;
            $this->standings[$teamId]['GA'] += $conceded;
            $this->standings[$teamId]['GD'] = $this->standings[$teamId]['G'] - $this->standings[$teamId]['GA'];
            $this->standings[$teamId]['Pts'] += ($win * 3) + ($draw * 1);
        }

        private function calculateStandings($matches) {
            foreach ($matches as $match) {
                $team1Id = $match['team1_id'];
                $team2Id = $match['team2_id'];
                $team1Score = (int)$match['team1_score'];
                $team2Score = (int)$match['team2_score'];
                $group = $match['group_id'] ?: 'No Group';

                if ($team1Score > $team2Score) {
                    $this->updateTeamStats($team1Id, $group, $team1Score, $team2Score, 1, 0, 0);
                    $this->updateTeamStats($team2Id, $group, $team2Score, $team1Score, 0, 0, 1);
                } elseif ($team1Score < $team2Score) {
                    $this->updateTeamStats($team1Id, $group, $team1Score, $team2Score, 0, 0, 1);
                    $this->updateTeamStats($team2Id, $group, $team2Score, $team1Score, 1, 0, 0);
                } else {
                    $this->updateTeamStats($team1Id, $group, $team1Score, $team2Score, 0, 1, 0);
                    $this->updateTeamStats($team2Id, $group, $team2Score, $team1Score, 0, 1, 0);
                }
            }
        }

        private function sortTeamsByRank($a, $b) {
            if ($a['Pts'] !== $b['Pts']) {
                return $b['Pts'] - $a['Pts'];
            }
            if ($a['GD'] !== $b['GD']) {
                return $b['GD'] - $a['GD'];
            }
            return $b['G'] - $a['G'];
        }

        public function getGroupedStandings() {
            $groups = [];
            foreach ($this->standings as $teamId => $data) {
                $groups[$data['group']][$teamId] = $data;
            }

            foreach ($groups as &$teams) {
                uasort($teams, [$this, 'sortTeamsByRank']);
            }
            unset($teams);

            return $groups;
        }

        private function getTeamLogo($teamName) {
            $logoPath = "/uploads/logo-team/";
            $logoFile = strtolower(str_replace(' ', '_', $teamName)) . '.png';

            if (file_exists(FCPATH . $logoPath . $logoFile)) {
                return "<img src='{$logoPath}{$logoFile}' class='rounded-circle border' style='width: 32px; height: 32px; object-fit: cover;' alt='{$teamName}'>";
            }

            $initials = '';
            $words = explode(' ', $teamName);
            foreach ($words as $word) {
                if (!empty($word)) {
                    $initials .= strtoupper(substr($word, 0, 1));
                    if (strlen($initials) >= 2) break;
                }
            }

            return "<div class='rounded-circle bg-light border d-flex align-items-center justify-content-center fw-bold text-primary' style='width: 32px; height: 32px; font-size: 12px;'>{$initials}</div>";
        }

        public function renderGroupTable($group, $teams) {
            $groupTitle = ($group === 'No Group') ? lang('Common.teams_without_group') : lang('Common.group') . ' ' . $group;

            // Get group metadata for filtering
            $groupMatches = [];
            foreach ($this->matches as $match) {
                if (($match['group_id'] ?: 'No Group') == $group) {
                    $groupMatches[] = $match;
                    break; // We just need one match to get the metadata
                }
            }

            $roundName = !empty($groupMatches) ? $groupMatches[0]['round_name'] ?? '' : '';
            $categoryName = !empty($groupMatches) ? $groupMatches[0]['category_name'] ?? '' : '';
            $regionName = !empty($groupMatches) ? $groupMatches[0]['region_name'] ?? '' : '';

            echo "<div class='col-lg-6 mb-4 standings-container' data-round='{$roundName}' data-category='{$categoryName}' data-region='{$regionName}' data-group='{$group}'>";
            echo "<div class='card shadow-sm h-100 border-0 rounded-3 overflow-hidden'>";
            echo "<div class='card-header bg-primary text-white py-3'>";
            echo "<div class='d-flex justify-content-between align-items-center'>";
            echo "<h5 class='mb-0 fw-bold'><i class='ti ti-users-group me-2'></i>{$groupTitle}</h5>";
            echo "<span class='badge bg-white text-primary rounded-pill px-3'>{$roundName}</span>";
            echo "</div>";
            echo "</div>";
            echo "<div class='table-responsive'>";
            echo "<table class='table table-hover align-middle mb-0'>";
            echo "<thead class='bg-light text-secondary small text-uppercase'>";
            echo "<tr>";
            echo "<th class='ps-3' style='width: 45%;'>" . lang('Common.team') . "</th>";
            echo "<th class='text-center' title='Played'>PL</th>";
            echo "<th class='text-center' title='Won'>W</th>";
            echo "<th class='text-center' title='Drawn'>D</th>";
            echo "<th class='text-center' title='Lost'>L</th>";
            echo "<th class='text-center' title='Goal Difference'>GD</th>";
            echo "<th class='text-center fw-bold text-primary' title='Points'>Pts</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            $rank = 1;
            foreach ($teams as $teamId => $stats) {
                $rankClass = $this->getRankClass($rank);
                $gdClass = $this->getGDClass($stats['GD']);
                $teamName = esc($this->teamNames[$teamId]);
                $teamLogo = $this->getTeamLogo($teamName);

                echo "<tr class='{$rankClass}'>";
                echo "<td class='ps-3'>";
                echo "<div class='d-flex align-items-center gap-2'>";
                echo "<span class='text-muted small fw-bold' style='width: 15px;'>{$rank}</span>";
                echo $teamLogo;
                echo "<span class='fw-semibold text-truncate' style='max-width: 150px;' title='{$teamName}'>{$teamName}</span>";
                echo "</div>";
                echo "</td>";
                echo "<td class='text-center text-muted'>{$stats['PL']}</td>";
                echo "<td class='text-center text-muted'>{$stats['W']}</td>";
                echo "<td class='text-center text-muted'>{$stats['D']}</td>";
                echo "<td class='text-center text-muted'>{$stats['L']}</td>";
                echo "<td class='text-center fw-bold {$gdClass}'>{$stats['GD']}</td>";
                echo "<td class='text-center'><span class='badge bg-primary rounded-pill px-2'>{$stats['Pts']}</span></td>";
                echo "</tr>";
                $rank++;
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
            echo "</div>";
        }

        private function getRankClass($rank) {
            switch($rank) {
                case 1: return 'bg-warning-subtle';
                case 2: return 'bg-secondary-subtle';
                case 3: return 'bg-danger-subtle';
                default: return '';
            }
        }

        private function getGDClass($gd) {
            if ($gd > 0) return 'text-success';
            if ($gd < 0) return 'text-danger';
            return 'text-muted';
        }

        public function renderFilters() {
            echo "<div class='card shadow-sm mb-4 border-0 rounded-3'>";
            echo "<div class='card-body'>";

            // Region Filter
            echo "<div class='mb-3'>";
            echo "<label class='form-label fw-bold text-primary'><i class='ti ti-map-pin me-1'></i>" . lang('Common.select_region') . "</label>";
            echo "<div class='d-flex flex-wrap gap-2'>";
            echo "<button class='btn btn-sm btn-outline-primary active rounded-pill px-3' data-filter='region' data-value='all'>" . lang('Common.all') . "</button>";
            echo "<button class='btn btn-sm btn-outline-primary rounded-pill px-3' data-filter='region' data-value='กรุงเทพฯ'>" . lang('Common.region_bangkok') . "</button>";
            echo "<button class='btn btn-sm btn-outline-primary rounded-pill px-3' data-filter='region' data-value='ภาคใต้'>" . lang('Common.region_south') . "</button>";
            echo "<button class='btn btn-sm btn-outline-primary rounded-pill px-3' data-filter='region' data-value='ภาคกลางและภาคตะวันตก'>" . lang('Common.region_central_west') . "</button>";
            echo "<button class='btn btn-sm btn-outline-primary rounded-pill px-3' data-filter='region' data-value='ภาคตะวันออกและปริมณฑล'>" . lang('Common.region_east_metro') . "</button>";
            echo "<button class='btn btn-sm btn-outline-primary rounded-pill px-3' data-filter='region' data-value='ภาคตะวันออกเฉียงเหนือ-ตอนล่าง'>" . lang('Common.region_northeast_lower') . "</button>";
            echo "<button class='btn btn-sm btn-outline-primary rounded-pill px-3' data-filter='region' data-value='ภาคตะวันออกเฉียงเหนือ-ตอนบน'>" . lang('Common.region_northeast_upper') . "</button>";
            echo "<button class='btn btn-sm btn-outline-primary rounded-pill px-3' data-filter='region' data-value='ภาคเหนือ'>" . lang('Common.region_north') . "</button>";
            echo "</div>";
            echo "</div>";

            echo "<div class='row'>";
            // Round Filter
            echo "<div class='col-md-6 mb-3 mb-md-0'>";
            echo "<label class='form-label fw-bold text-primary'><i class='ti ti-tournament me-1'></i>" . lang('Common.select_round') . "</label>";
            echo "<div class='d-flex flex-wrap gap-2'>";
            echo "<button class='btn btn-sm btn-outline-secondary active rounded-pill px-3' data-filter='round' data-value='all'>" . lang('Common.all') . "</button>";
            echo "<button class='btn btn-sm btn-outline-secondary rounded-pill px-3' data-filter='round' data-value='48 ทีม'>48 " . lang('Common.teams') . "</button>";
            echo "<button class='btn btn-sm btn-outline-secondary rounded-pill px-3' data-filter='round' data-value='24 ทีม'>24 " . lang('Common.teams') . "</button>";
            echo "</div>";
            echo "</div>";

            // Group Filter
            echo "<div class='col-md-6'>";
            echo "<label class='form-label fw-bold text-primary'><i class='ti ti-users-group me-1'></i>" . lang('Common.select_group') . "</label>";
            echo "<div class='d-flex flex-wrap gap-2'>";
            echo "<button class='btn btn-sm btn-outline-info active rounded-pill px-3' data-filter='group' data-value='all'>" . lang('Common.all') . "</button>";
            // Generate A-L groups
            for ($i = ord('A'); $i <= ord('L'); $i++) {
                $group = chr($i);
                echo "<button class='btn btn-sm btn-outline-info rounded-pill px-3' data-filter='group' data-value='{$group}'>{$group}</button>";
            }
            echo "</div>";
            echo "</div>";
            echo "</div>";

            echo "</div>";
            echo "</div>";
        }

        public function render() {
            $groups = $this->getGroupedStandings();

            if (empty($groups)) {
                echo "<div class='card shadow-sm border-0 rounded-3 text-center py-5'>";
                echo "<div class='card-body'>";
                echo "<i class='ti ti-trophy-off fs-1 text-muted mb-3 d-block opacity-50'></i>";
                echo "<h4 class='text-muted'>" . lang('Common.no_match_data_yet') . "</h4>";
                echo "<p class='text-muted mb-0'>" . lang('Common.start_match_to_see_standings') . "</p>";
                echo "</div>";
                echo "</div>";
                return;
            }

            // Sort groups alphabetically
            ksort($groups);

            echo "<div class='row groups-grid'>";
            foreach ($groups as $group => $teams) {
                $this->renderGroupTable($group, $teams);
            }
            echo "</div>";
        }
    }

    $tournament = new TournamentStandings($matches);
    $tournament->renderFilters();
    $tournament->render();
    ?>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
const langNoResultsFound = '<?= lang("Common.no_results_found") ?>';
const langTryChangeFilter = '<?= lang("Common.try_change_filter") ?>';

document.addEventListener('DOMContentLoaded', function() {
    const regionButtons = document.querySelectorAll('[data-filter="region"]');
    const roundButtons = document.querySelectorAll('[data-filter="round"]');
    const groupButtons = document.querySelectorAll('[data-filter="group"]');
    const standingsContainers = document.querySelectorAll('.standings-container');

    let currentRegionFilter = 'all';
    let currentRoundFilter = 'all';
    let currentGroupFilter = 'all';

    function updateVisibility() {
        standingsContainers.forEach(container => {
            const regionData = container.getAttribute('data-region') || '';
            const roundData = container.getAttribute('data-round') || '';
            const groupData = container.getAttribute('data-group') || '';

            const regionMatch = currentRegionFilter === 'all' ||
                               regionData.includes(currentRegionFilter) ||
                               (currentRegionFilter === 'กรุงเทพฯ' && regionData.includes('กรุงเทพ'));
            const roundMatch = currentRoundFilter === 'all' || roundData.includes(currentRoundFilter);
            const groupMatch = currentGroupFilter === 'all' || groupData === currentGroupFilter;

            if (regionMatch && roundMatch && groupMatch) {
                container.style.display = 'block';
                // Add fade-in animation
                container.style.opacity = '0';
                container.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    container.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    container.style.opacity = '1';
                    container.style.transform = 'translateY(0)';
                }, 50);
            } else {
                container.style.display = 'none';
            }
        });

        // Check if no results
        const visibleContainers = Array.from(standingsContainers).filter(container =>
            container.style.display !== 'none'
        );

        let noResultsDiv = document.querySelector('.no-results');
        if (visibleContainers.length === 0) {
            if (!noResultsDiv) {
                noResultsDiv = document.createElement('div');
                noResultsDiv.className = 'col-12 no-results';
                noResultsDiv.innerHTML = `
                    <div class="card shadow-sm border-0 rounded-3 text-center py-5">
                        <div class="card-body">
                            <i class="ti ti-search fs-1 text-muted mb-3 d-block opacity-50"></i>
                            <h4 class="text-muted">${langNoResultsFound}</h4>
                            <p class="text-muted mb-0">${langTryChangeFilter}</p>
                        </div>
                    </div>
                `;
                document.querySelector('.groups-grid').appendChild(noResultsDiv);
            }
            noResultsDiv.style.display = 'block';
        } else {
            if (noResultsDiv) {
                noResultsDiv.style.display = 'none';
            }
        }
    }

    function setActiveButton(buttons, activeValue) {
        buttons.forEach(btn => {
            btn.classList.remove('active');
            if (btn.getAttribute('data-value') === activeValue) {
                btn.classList.add('active');
            }
        });
    }

    regionButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentRegionFilter = this.getAttribute('data-value');
            setActiveButton(regionButtons, currentRegionFilter);
            updateVisibility();
        });
    });

    roundButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentRoundFilter = this.getAttribute('data-value');
            setActiveButton(roundButtons, currentRoundFilter);
            updateVisibility();
        });
    });

    groupButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentGroupFilter = this.getAttribute('data-value');
            setActiveButton(groupButtons, currentGroupFilter);
            updateVisibility();
        });
    });

    // Initial animation
    setTimeout(() => {
        standingsContainers.forEach((container, index) => {
            container.style.opacity = '0';
            container.style.transform = 'translateY(30px)';
            setTimeout(() => {
                container.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
                container.style.opacity = '1';
                container.style.transform = 'translateY(0)';
            }, index * 50);
        });
    }, 100);
});
</script>
<?php $this->endSection(); ?>