<?php $this->extend('template/tournament') ?>

<?php $this->section('content'); ?>
<style>
:root {
    --primary-blue: #1f3a8a;
    --primary-gold: #f3b71b;
    --text-gray: #6b7280;
    --bg-light: #f9fafb;
    --success-green: #059669;
    --danger-red: #dc2626;
}

.standings-container {
    background: linear-gradient(135deg, var(--bg-light) 0%, #ffffff 100%);
    border-radius: 20px;
    box-shadow: 0 10px 30px rgba(31, 58, 138, 0.1);
    overflow: hidden;
    margin-bottom: 30px;
}

.group-header {
    background: linear-gradient(135deg, var(--primary-blue) 0%, #2563eb 100%);
    color: white;
    padding: 20px 30px;
    margin: 0;
    font-size: 1.5rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 1px;
    position: relative;
    overflow: hidden;
}

.group-header::before {
    content: '';
    position: absolute;
    top: 0;
    right: -10px;
    width: 100px;
    height: 100%;
    background: var(--primary-gold);
    opacity: 0.2;
    transform: skewX(-15deg);
}

.modern-table {
    margin: 0;
    border: none;
    background: white;
}

.modern-table thead tr {
    background: var(--primary-blue);
    color: white;
}

.modern-table thead th {
    border: none;
    padding: 18px 15px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    color: white;
}

.modern-table tbody tr {
    border-bottom: 1px solid #e5e7eb;
}

.modern-table tbody td {
    padding: 20px 15px;
    border: none;
    vertical-align: middle;
    font-weight: 500;
}

.team-cell {
    display: flex;
    align-items: center;
    gap: 12px;
}

.team-logo {
    width: 40px;
    height: 40px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid #e5e7eb;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 12px;
    font-weight: bold;
    color: var(--primary-blue);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.team-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 1rem;
}

.points-badge {
    background: linear-gradient(135deg, var(--primary-gold) 0%, #f59e0b 100%);
    color: white;
    padding: 8px 12px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 1rem;
    min-width: 45px;
    text-align: center;
    box-shadow: 0 2px 8px rgba(243, 183, 27, 0.3);
    display: inline-block;
}

.gd-positive { color: var(--success-green); font-weight: 600; }
.gd-negative { color: var(--danger-red); font-weight: 600; }
.gd-zero { color: var(--text-gray); font-weight: 500; }

.rank-1 { background: linear-gradient(90deg, rgba(255, 215, 0, 0.1) 0%, rgba(255, 215, 0, 0.05) 100%); }
.rank-2 { background: linear-gradient(90deg, rgba(192, 192, 192, 0.1) 0%, rgba(192, 192, 192, 0.05) 100%); }
.rank-3 { background: linear-gradient(90deg, rgba(205, 127, 50, 0.1) 0%, rgba(205, 127, 50, 0.05) 100%); }

.stat-number {
    font-weight: 600;
    color: #374151;
    text-align: center;
}

.empty-state {
    text-align: center;
    padding: 60px 20px;
    color: var(--text-gray);
    background: var(--bg-light);
    border-radius: 15px;
    margin: 20px 0;
}

.empty-state i {
    font-size: 3rem;
    color: var(--primary-blue);
    margin-bottom: 20px;
}

@media (max-width: 768px) {
    .modern-table thead th,
    .modern-table tbody td {
        padding: 12px 8px;
        font-size: 0.8rem;
    }

    .team-logo {
        width: 32px;
        height: 32px;
        font-size: 10px;
    }

    .team-name {
        font-size: 0.9rem;
    }

    .group-header {
        padding: 15px 20px;
        font-size: 1.2rem;
    }

    .points-badge {
        padding: 6px 10px;
        font-size: 0.9rem;
        min-width: 40px;
    }
}

.table-responsive {
    border-radius: 0 0 20px 20px;
    overflow: hidden;
}
</style>

<div class="container-fluid px-4 py-3">
    <?php
    // แผนที่ชื่อทีมและโลโก้ (สร้างจากข้อมูลแมตช์)
    $teamNames = [];
    $teamLogos = [];
    foreach ($matches as $match) {
        // เก็บข้อมูลทีม 1
        if (!empty($match['team1_id'])) {
            $teamNames[$match['team1_id']] = $match['team1_name'];
            if (!empty($match['team1_logo'])) {
                $teamLogos[$match['team1_id']] = $match['team1_logo'];
            }
        }

        // เก็บข้อมูลทีม 2
        if (!empty($match['team2_id'])) {
            $teamNames[$match['team2_id']] = $match['team2_name'];
            if (!empty($match['team2_logo'])) {
                $teamLogos[$match['team2_id']] = $match['team2_logo'];
            }
        }
    }

    // เตรียมตารางคะแนน
    $standings = [];

    function updateTeam(&$standings, $teamId, $group, $scored, $conceded, $win, $draw, $loss) {
        if (!isset($standings[$teamId])) {
            $standings[$teamId] = [
                'P' => 0,      // Played
                'W' => 0,      // Win
                'D' => 0,      // Draw
                'L' => 0,      // Loss
                'G' => 0,      // Goals For
                'GA' => 0,     // Goals Against
                'GD' => 0,     // Goal Difference
                'PTS' => 0,    // Points
                'group' => $group ?: 'No Group'
            ];
        }

        $standings[$teamId]['P'] += 1;
        $standings[$teamId]['W'] += $win;
        $standings[$teamId]['D'] += $draw;
        $standings[$teamId]['L'] += $loss;
        $standings[$teamId]['G'] += $scored;
        $standings[$teamId]['GA'] += $conceded;
        $standings[$teamId]['GD'] = $standings[$teamId]['G'] - $standings[$teamId]['GA'];
        $standings[$teamId]['PTS'] += ($win * 3) + ($draw * 1);
    }

    // ประมวลผล
    foreach ($matches as $match) {
        $t1 = $match['team1_id'];
        $t2 = $match['team2_id'];
        $s1 = $match['team1_score'];
        $s2 = $match['team2_score'];
        $group = $match['group_id'] ?: 'No Group';

        // Skip matches without scores (null or empty)
        if ($s1 === null || $s1 === '' || $s2 === null || $s2 === '') {
            continue;
        }

        if ($s1 > $s2) {
            updateTeam($standings, $t1, $group, $s1, $s2, 1, 0, 0);
            updateTeam($standings, $t2, $group, $s2, $s1, 0, 0, 1);
        } elseif ($s1 < $s2) {
            updateTeam($standings, $t1, $group, $s1, $s2, 0, 0, 1);
            updateTeam($standings, $t2, $group, $s2, $s1, 1, 0, 0);
        } else {
            updateTeam($standings, $t1, $group, $s1, $s2, 0, 1, 0);
            updateTeam($standings, $t2, $group, $s2, $s1, 0, 1, 0);
        }
    }

    // จัดกลุ่ม
    $groups = [];
    foreach ($standings as $teamId => $data) {
        $groups[$data['group']][$teamId] = $data;
    }

    // เรียงคะแนนภายในแต่ละกลุ่ม
    foreach ($groups as &$teams) {
        uasort($teams, function($a, $b) {
            if ($a['PTS'] !== $b['PTS']) {
                return $b['PTS'] - $a['PTS'];
            } elseif ($a['GD'] !== $b['GD']) {
                return $b['GD'] - $a['GD'];
            } else {
                return $b['G'] - $a['G'];
            }
        });
    }
    unset($teams);

    // เรียงกลุ่มตามลำดับตัวอักษร
    ksort($groups);

    // แสดงผล
    if (empty($groups)) {
        echo "<div class='empty-state'>";
        echo "<i class='fas fa-trophy'></i>";
        echo "<h3>ยังไม่มีข้อมูลการแข่งขัน</h3>";
        echo "<p>รอการอัพเดทผลการแข่งขัน</p>";
        echo "</div>";
    } else {
        foreach ($groups as $group => $teams) {
            $groupTitle = ($group === 'No Group') ? 'ทีมที่ไม่มีกลุ่ม' : "กลุ่ม " . esc($group);

            echo "<div class='standings-container'>";
            echo "<h2 class='group-header'>{$groupTitle}</h2>";
            echo "<div class='table-responsive'>";
            echo "<table class='modern-table table align-middle'>";
            echo "<thead>";
            echo "<tr>";
            echo "<th style='width: 35%;'>ทีม</th>";
            echo "<th class='text-center'>P</th>";
            echo "<th class='text-center'>W</th>";
            echo "<th class='text-center'>D</th>";
            echo "<th class='text-center'>L</th>";
            echo "<th class='text-center'>GD</th>";
            echo "<th class='text-center'>G</th>";
            echo "<th class='text-center'>PTS</th>";
            echo "</tr>";
            echo "</thead>";
            echo "<tbody>";

            $rank = 1;
            foreach ($teams as $teamId => $stats) {
                $rankClass = ($rank <= 3) ? "rank-{$rank}" : '';
                $gdClass = ($stats['GD'] > 0) ? 'gd-positive' : (($stats['GD'] < 0) ? 'gd-negative' : 'gd-zero');
                $teamName = esc($teamNames[$teamId]);
                $logodefault = base_url('public/img/jootball_logo.png');
                $teamLogo = "<img src='" . ($teamLogos[$teamId] ?? $logodefault). "' class='team-logo'>" ;
                echo "<tr class='{$rankClass}'>";
                echo "<td>";
                echo "<div class='team-cell'>";
                echo $teamLogo;
                echo "<span class='team-name'>{$teamName}</span>";
                echo "</div>";
                echo "</td>";
                echo "<td class='text-center stat-number'>{$stats['P']}</td>";
                echo "<td class='text-center stat-number'>{$stats['W']}</td>";
                echo "<td class='text-center stat-number'>{$stats['D']}</td>";
                echo "<td class='text-center stat-number'>{$stats['L']}</td>";
                echo "<td class='text-center {$gdClass}'>{$stats['GD']}</td>";
                echo "<td class='text-center stat-number'>{$stats['G']}</td>";
                echo "<td class='text-center'><span class='points-badge'>{$stats['PTS']}</span></td>";
                echo "</tr>";
                $rank++;
            }

            echo "</tbody>";
            echo "</table>";
            echo "</div>";
            echo "</div>";
        }
    }
    ?>
</div>
<?php $this->endSection(); ?>
<?php $this->section('scripts'); ?>
<script>
$(document).ready(function () {
    // Smooth entrance animation for standings containers
    $('.standings-container').each(function(index) {
        $(this).css({
            'opacity': '0',
            'transform': 'translateY(30px)'
        });

        setTimeout(() => {
            $(this).css({
                'transition': 'opacity 0.5s ease, transform 0.5s ease',
                'opacity': '1',
                'transform': 'translateY(0)'
            });
        }, index * 100);
    });
});
</script>
<?php $this->endSection(); ?>