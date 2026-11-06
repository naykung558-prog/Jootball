<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Player Cards - <?= esc($team['name'] ?? 'Team') ?></title>
    <style>
        @page {
            margin: 8mm;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'thsarabun', sans-serif;
            background: #ffffff;
        }

        .cards-container {
            width: 100%;
        }

        .card-wrapper {
            display: inline-block;
            width: 48%;
            vertical-align: top;
            padding: 4mm;
        }

        .player-card {
            width: 100%;
            height: 130mm;
            background: #06346D;
            border-radius: 12px;
            overflow: hidden;
            position: relative;
        }

        /* Top Section - Navy Blue */
        .card-top {
            height: 25mm;
            background: #06346D;
            position: relative;
            padding: 8px 12px;
        }

        .team-logo-container {
            position: absolute;
            top: 8px;
            left: 12px;
            width: 45px;
            height: 45px;
            background: #ffffff;
            border-radius: 8px;
            padding: 4px;
        }

        .team-logo {
            width: 100%;
            height: 100%;
            object-fit: contain;
        }

        .shirt-number-container {
            position: absolute;
            bottom: 8px;
            left: 12px;
        }

        .shirt-number {
            font-size: 42pt;
            font-weight: bold;
            color: #F3B71B;
            line-height: 1;
        }

        /* Middle Section - Gold */
        .card-middle {
            height: 35mm;
            background: #F3B71B;
            padding: 10px 12px;
            position: relative;
        }

        .team-info {
            text-align: center;
            padding-top: 5px;
        }

        .team-name {
            font-size: 18pt;
            font-weight: bold;
            color: #06346D;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .tournament-name {
            font-size: 11pt;
            color: #042441;
            margin-top: 3px;
            text-transform: uppercase;
        }

        /* Photo Section */
        .photo-section {
            height: 45mm;
            background: #06346D;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 10px;
        }

        .photo-frame {
            width: 95px;
            height: 120px;
            border-radius: 8px;
            overflow: hidden;
            border: 3px solid #F3B71B;
            background: #ffffff;
        }

        .player-photo {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Player Info Section */
        .player-info-section {
            height: 20mm;
            background: linear-gradient(180deg, #0a4a7a 0%, #06346D 100%);
            padding: 8px 12px;
            text-align: center;
            border-top: 2px solid #F3B71B;
        }

        .player-name {
            font-size: 16pt;
            font-weight: bold;
            color: #ffffff;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }

        .player-position {
            font-size: 12pt;
            color: #F3B71B;
            font-weight: 600;
        }

        /* Footer */
        .card-footer {
            height: 5mm;
            background: #042441;
            padding: 3px 12px;
            text-align: center;
            border-top: 1px solid #F3B71B;
        }

        .jootball-logo {
            height: 35px;
            opacity: 0.9;
        }

        /* Page break */
        .page-break {
            page-break-after: always;
        }

        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>

<body>
    <div class="cards-container">
        <?php
        $defaultImg = base_url('public/img/img_profile.jpg');
        $teamLogo = !empty($team['logo']) ? base_url($team['logo']) : base_url('public/img/jootball_logo.png');
        $tournamentName = !empty($tournament['name']) ? $tournament['name'] : '';
        $count = 0;

        if (!empty($players)):
            foreach ($players as $player):
                $count++;
                $playerImg = !empty($player['image_path']) && $player['image_path'] != '' ? $player['image_path'] : $defaultImg;

                // Get position
                $position = $player['position_name'] ?? 'ผู้เล่น';
        ?>
                <div class="card-wrapper">
                    <div class="player-card">
                        <!-- Top Navy Section -->
                        <div class="card-top">
                            <div class="team-logo-container">
                                <img src="<?= $teamLogo ?>" alt="Logo" class="team-logo">
                            </div>
                            <div class="shirt-number-container">
                                <div class="shirt-number"><?= esc($player['shirt_number'] ?? '-') ?></div>
                            </div>
                        </div>

                        <!-- Gold Section -->
                        <div class="card-middle">
                            <div class="team-info">
                                <div class="team-name"><?= esc($team['name'] ?? 'TEAM') ?></div>
                                <?php if ($tournamentName): ?>
                                    <div class="tournament-name"><?= esc($tournamentName) ?></div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Photo -->
                        <div class="photo-section">
                            <div class="photo-frame">
                                <img src="<?= $playerImg ?>" alt="<?= esc($player['first_name'] ?? 'Player') ?>" class="player-photo">
                            </div>
                        </div>

                        <!-- Player Info -->
                        <div class="player-info-section">
                            <div class="player-name">
                                <?= esc($player['first_name'] ?? '') ?> <?= esc($player['last_name'] ?? '') ?>
                            </div>
                            <div class="player-position"><?= esc($position) ?></div>
                        </div>

                        <!-- Footer -->
                        <div class="card-footer">
                            <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="Jootball" class="jootball-logo">
                        </div>
                    </div>
                </div>

                <?php
                // Add page break every 4 cards
                if ($count % 4 == 0 && $count < count($players)):
                ?>
                    <div class="page-break clearfix"></div>
        <?php
                endif;
            endforeach;
        endif;
        ?>
    </div>
</body>

</html>