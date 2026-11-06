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

.filter-section {
    background: white;
    border-radius: 15px;
    padding: 25px;
    margin-bottom: 30px;
    box-shadow: 0 4px 15px rgba(31, 58, 138, 0.1);
    border: 1px solid #e5e7eb;
}

.filter-label {
    font-weight: 600;
    color: var(--primary-blue);
    margin-bottom: 15px;
    font-size: 1.1rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.modern-select {
    background: var(--bg-light);
    border: 2px solid #cbd5e1;
    border-radius: 12px;
    padding: 12px 16px;
    font-size: 1rem;
    font-weight: 500;
    color: #374151;
    transition: all 0.3s ease;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.modern-select:focus {
    border-color: var(--primary-gold);
    box-shadow: 0 0 0 3px rgba(243, 183, 27, 0.2);
    outline: none;
    background: white;
}

.matches-container {
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
    text-decoration: none;
    display: block;
    transition: all 0.3s ease;
}

.group-header:hover {
    color: white;
    text-decoration: none;
    background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
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

.group-header .view-details {
    position: absolute;
    right: 20px;
    top: 50%;
    transform: translateY(-50%);
    background: rgba(255, 255, 255, 0.2);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    border: 1px solid rgba(255, 255, 255, 0.3);
    transition: all 0.3s ease;
}

.group-header:hover .view-details {
    background: rgba(255, 255, 255, 0.3);
    border-color: rgba(255, 255, 255, 0.5);
}

.matches-table {
    margin: 0;
    border: none;
    background: white;
}

.matches-table thead tr {
    background: var(--primary-blue);
    color: white;
}

.matches-table thead th {
    border: none;
    padding: 18px 15px;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
    color: white;
}

.matches-table tbody tr {
    border-bottom: 1px solid #e5e7eb;
}


.matches-table tbody td {
    padding: 20px 15px;
    border: none;
    vertical-align: middle;
}

.team-cell {
    display: flex;
    align-items: center;
    gap: 15px;
}

.team-cell.left {
    justify-content: flex-start;
}

.team-cell.right {
    /* justify-content: flex-end; */
    flex-direction: row-reverse;
}

.team-logo {
    width: 45px;
    height: 45px;
    border-radius: 8px;
    object-fit: cover;
    border: 2px solid #e5e7eb;
    background: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 13px;
    font-weight: bold;
    color: var(--primary-blue);
    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
    flex-shrink: 0;
}

.team-name {
    font-weight: 600;
    color: #1f2937;
    font-size: 1.1rem;
}

.score-display {
    background: linear-gradient(135deg, var(--primary-gold) 0%, #f59e0b 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: 700;
    font-size: 1.1rem;
    display: inline-block;
    min-width: 80px;
    box-shadow: 0 2px 8px rgba(243, 183, 27, 0.3);
}

.score-display::before,
.score-display::after {
    content: none !important;
}

.score-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 8px;
}

.match-datetime {
    font-size: 0.8rem;
    color: var(--text-gray);
    font-weight: 500;
    margin-top: 5px;
}

.match-time {
    color: var(--primary-blue);
    font-weight: 600;
    font-size: 0.85rem;
}

.no-time {
    color: #9ca3af;
    font-style: italic;
    font-size: 0.8rem;
}


.match-row {
    position: relative;
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
    .team-logo {
        width: 35px;
        height: 35px;
        font-size: 11px;
    }

    .team-name {
        font-size: 0.9rem;
    }

    .team-cell {
        gap: 10px;
    }

    .score-display {
        padding: 6px 12px;
        font-size: 1rem;
        min-width: 70px;
    }

    .group-header {
        padding: 15px 20px;
        font-size: 1.2rem;
    }

    .group-header .view-details {
        right: 15px;
        padding: 6px 12px;
        font-size: 0.75rem;
    }

    .matches-table tbody td,
    .matches-table thead th {
        padding: 12px 8px;
        font-size: 0.85rem;
    }

    .match-datetime {
        font-size: 0.75rem;
        margin-top: 3px;
    }

    .match-time {
        font-size: 0.7rem;
    }

    .no-time {
        font-size: 0.65rem;
    }

    .score-section {
        gap: 5px;
    }
}

.filter-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 20px;
}

@media (max-width: 768px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="container-fluid px-4 py-3">
    <div class="filter-section">
        <div class="filter-grid">
            <?php if(!empty($categories)){ ?>
            <div>
                <div class="filter-label">เลือกประเภท</div>
                <select class="form-control modern-select filter" id="category">
                    <?php foreach ($categories as $key => $r) {
                        $sel = ($key == $cate_id) ? "selected='selected'" : ''; ?>
                        <option <?php echo $sel;?> value="<?php echo $key?>"><?php echo $r;?></option>
                    <?php }?>
                </select>
            </div>
            <?php } ?>
            <div>
                <div class="filter-label">เลือกรอบ</div>
                <select class="form-control modern-select filter" id="round">
                    <?php
                    $firstRound = true;
                    foreach ($rounds as $key => $r) {
                        $sel = '';
                        // Set default to "48 ทีม" if no round is selected and this is "48 ทีม"
                        if (empty($round) && $firstRound && (strpos($r, '48') !== false || strpos($r, '48 ทีม') !== false)) {
                            $sel = "selected='selected'";
                        } elseif ($key == $round) {
                            $sel = "selected='selected'";
                        }
                        $firstRound = false;
                    ?>
                        <option <?php echo $sel;?> value="<?php echo $key?>"><?php echo $r;?></option>
                    <?php }?>
                </select>
            </div>
        </div>
    </div>

    <div class="matches-content">
        <?php

        // ฟังก์ชันแสดงผลการแข่งขันในรูปแบบตาราง
        function displayMatchesModern($tour_id, $cate_id, $round, $matches) {
            if (empty($matches)) {
                echo "<div class='empty-state'>";
                echo "<i class='fas fa-futbol'></i>";
                echo "<h3>ยังไม่มีข้อมูลการแข่งขัน</h3>";
                echo "<p>รอการอัพเดทผลการแข่งขัน</p>";
                echo "</div>";
                return;
            }

            $groupedMatches = [];
            foreach ($matches as $match) {
                $group = $match['group_id'] ?: 'ไม่มีกลุ่ม';
                $groupedMatches[$group][] = $match;
            }

            // Sort groups alphabetically
            ksort($groupedMatches);

            foreach ($groupedMatches as $group => $groupMatches) {
                $link_group = base_url('tournament/group/' . $tour_id . '?group_id=' . urlencode($group) . '&cate_id=' . $cate_id . '&round=' . $round);

                echo "<div class='matches-container'>";
                echo "<a href='" . $link_group . "' class='group-header'>";
                echo "กลุ่ม " . esc($group);
                echo "<span class='view-details'>ดูรายละเอียด</span>";
                echo "</a>";
                echo "<div class='table-responsive'>";
                echo "<table class='matches-table table align-middle'>";
                echo "<thead>";
                echo "<tr>";
                echo "<th style='width: 42%;text-align: center;'>ทีม 1</th>";
                echo "<th style='width: 16%;text-align: center;' class='text-center'>สกอร์</th>";
                echo "<th style='width: 42%;text-align: center;'>ทีม 2</th>";
                echo "</tr>";
                echo "</thead>";
                echo "<tbody>";
                // echo "<pre>";
                //   print_r($groupMatches);
                //   die();
                foreach ($groupMatches as $match) {
                    $logodefault = base_url('public/img/jootball_logo.png');
                    $team1Logo = "<img src='" . ($match['team1_logo'] ?? $logodefault). "' class='team-logo'>" ;
                    $team2Logo = "<img src='" . ($match['team2_logo'] ?? $logodefault). "' class='team-logo'>" ;
                    // $team2Logo = getTeamLogo($match['team2_logo'] ?? '', $match['team2_name']);

                    // จัดรูปแบบวันที่และเวลาแบบไทย
                    $matchDateTime = '';
                    if (!empty($match['date'])) {
                        $date = new DateTime($match['date']);

                        // แปลงเป็นปีพุทธศักราช
                        $day = $date->format('j');
                        $month = $date->format('n');
                        $year = $date->format('Y') + 543;

                        // เดือนภาษาไทย (แบบย่อ)
                        $thaiMonths = [
                            1 => 'ม.ค.', 2 => 'ก.พ.', 3 => 'มี.ค.', 4 => 'เม.ย.',
                            5 => 'พ.ค.', 6 => 'มิ.ย.', 7 => 'ก.ค.', 8 => 'ส.ค.',
                            9 => 'ก.ย.', 10 => 'ต.ค.', 11 => 'พ.ย.', 12 => 'ธ.ค.'
                        ];

                        $matchDateTime = $day . ' ' . $thaiMonths[$month] . ' ' . $year;

                        if (!empty($match['time'])) {
                            $matchDateTime .= '<br><span class="match-time">' . $match['time'] . ' น.</span>';
                        }
                    } else {
                        $matchDateTime = '<span class="no-time">ยังไม่กำหนด</span>';
                    }

                    echo "<tr class='match-row'>";

                    // ทีม 1
                    echo "<td>";
                    echo "<div class='team-cell left'>";
                    echo $team1Logo;
                    echo "<span class='team-name'>" . esc($match['team1_name']) . "</span>";
                    echo "</div>";
                    echo "</td>";

                    // คะแนน
                    echo "<td class='text-center'>";
                    echo "<div class='score-section'>";
                    $score1Display = ($match['team1_score'] !== null && $match['team1_score'] !== '') ? esc($match['team1_score']) : '-';
                    $score2Display = ($match['team2_score'] !== null && $match['team2_score'] !== '') ? esc($match['team2_score']) : '-';
                    echo "<span class='score-display'>" . $score1Display . " : " . $score2Display . "</span>";
                    echo "<div class='match-datetime'>";
                    echo $matchDateTime;
                    echo "</div>";
                    echo "</div>";
                    echo "</td>";

                    // ทีม 2
                    echo "<td>";
                    echo "<div class='team-cell right'>";
                    echo $team2Logo;
                    echo "<span class='team-name'>" . esc($match['team2_name']) . "</span>";
                    echo "</div>";
                    echo "</td>";

                    echo "</tr>";
                }

                echo "</pre></table>";
                echo "</div>";
                echo "</div>";
            }
        }

        // แสดงผลการแข่งขัน
        displayMatchesModern($tour_id, $cate_id, $round, $matches);
        ?>
    </div>
</div>
<?php $this->endSection(); ?>
<?php $this->section('scripts'); ?>
<script>
$(document).ready(function () {
    // Filter change handler with loading animation
    $('.filter').on('change', function() {
        var round = $('#round').val();
        var cate_id = $('#category').val();

        // Add loading state
        $('.matches-content').css('opacity', '0.5');
        $('body').append('<div class="loading-overlay" style="position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(31, 58, 138, 0.1); z-index: 9999; display: flex; align-items: center; justify-content: center;"><div style="background: white; padding: 25px 30px; border-radius: 15px; box-shadow: 0 8px 25px rgba(0,0,0,0.15); text-align: center; font-weight: 600; color: #1f3a8a;"><div class="spinner" style="width: 30px; height: 30px; border: 3px solid #e5e7eb; border-top: 3px solid #1f3a8a; border-radius: 50%; animation: spin 1s linear infinite; margin: 0 auto 15px auto;"></div>กำลังโหลดข้อมูล...</div></div>');

        // Add CSS animation for spinner
        if (!$('#spinner-style').length) {
            $('head').append('<style id="spinner-style">@keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }</style>');
        }

        setTimeout(function() {
            window.location.href = '<?php echo base_url('tournament/result/'.$tour_id)?>?round=' + round + '&cate_id=' + cate_id;
        }, 300);
    });

    // Smooth entrance animation for match containers
    $('.matches-container').each(function(index) {
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