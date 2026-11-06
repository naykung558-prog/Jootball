<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'thsarabun', sans-serif;
            font-size: 14px;
            line-height: 1.3;
        }
        .container {
            width: 100%;
            padding: 10px;
        }
        .header {
            text-align: center;
            border: 2px solid #000;
            padding: 8px;
            margin-bottom: 10px;
            background: #f5f5f5;
        }
        .header h1 {
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .header h2 {
            font-size: 16px;
            font-weight: normal;
            color: #333;
        }
        .match-info {
            border: 1px solid #000;
            padding: 8px;
            margin-bottom: 10px;
        }
        .match-info table {
            border: none;
            width: 100%;
        }
        .match-info td {
            border: none;
            padding: 2px 8px;
            font-size: 13px;
        }
        .teams-score {
            margin-bottom: 12px;
        }
        .teams-score table {
            border: none;
            width: 100%;
        }
        .teams-score td {
            border: none;
            vertical-align: middle;
        }
        .team-box {
            text-align: center;
            padding: 8px;
        }
        .team-name {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 3px;
        }
        .team-label {
            font-size: 12px;
            color: #666;
        }
        .score-display {
            text-align: center;
        }
        .score-number {
            display: inline-block;
            width: 50px;
            height: 50px;
            border: 2px solid #000;
            border-radius: 8px;
            line-height: 46px;
            font-size: 32px;
            font-weight: bold;
            background: #fff;
            margin: 0 3px;
        }
        .score-separator {
            font-size: 24px;
            font-weight: bold;
            margin: 0 8px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 8px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 3px 5px;
            font-size: 12px;
        }
        table.data-table th {
            background: #e0e0e0;
            font-weight: bold;
            text-align: center;
        }
        .section-header {
            background: #333;
            color: #fff;
            padding: 5px 8px;
            font-weight: bold;
            font-size: 13px;
            margin-bottom: 0;
        }
        .section-header-blue {
            background: #1565c0;
        }
        .section-header-yellow {
            background: #f9a825;
            color: #000;
        }
        .section-header-red {
            background: #c62828;
        }
        .section-header-green {
            background: #2e7d32;
        }
        .two-col {
            width: 100%;
        }
        .two-col > tbody > tr > td {
            width: 50%;
            vertical-align: top;
            padding: 0 3px;
            border: none;
        }
        .empty-row td {
            height: 20px;
        }
        .penalty-section {
            margin-top: 10px;
            border: 2px solid #000;
            padding: 8px;
            background: #fffde7;
        }
        .penalty-title {
            font-weight: bold;
            font-size: 14px;
            margin-bottom: 8px;
            text-align: center;
        }
        .penalty-score {
            font-size: 22px;
            font-weight: bold;
            text-align: center;
        }
        .signature-section {
            margin-top: 20px;
        }
        .signature-section td {
            border: none;
            text-align: center;
            padding: 8px;
            font-size: 12px;
        }
        .signature-line {
            border-bottom: 1px solid #000;
            width: 150px;
            display: inline-block;
            margin-bottom: 3px;
        }
        .own-goal {
            color: #c62828;
            font-size: 10px;
        }
        .sub-info {
            font-size: 10px;
            color: #666;
        }
        .page-break {
            page-break-before: always;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <h1><?= esc($tournament['name'] ?? lang('Common.football_match')) ?></h1>
            <h2><?= lang('Common.match_report_title') ?></h2>
        </div>

        <!-- Match Info -->
        <div class="match-info">
            <table>
                <tr>
                    <td style="width: 50%;"><strong><?= lang('Common.match_round') ?>:</strong> <?= esc($match['match_round'] ?? '-') ?></td>
                    <td style="width: 50%;"><strong><?= lang('Common.match') ?>:</strong> #<?= esc($match['match_id'] ?? '-') ?></td>
                </tr>
                <tr>
                    <td><strong><?= lang('Common.date') ?>:</strong> <?= !empty($match['date']) ? date('d/m/Y', strtotime($match['date'])) : '-' ?></td>
                    <td><strong><?= lang('Common.time') ?>:</strong> <?= !empty($match['time']) ? $match['time'] : '-' ?> <?= lang('Common.time_suffix') ?></td>
                </tr>
                <tr>
                    <td colspan="2"><strong><?= lang('Common.stadium') ?>:</strong> <?= esc($match['stadium'] ?? '-') ?></td>
                </tr>
            </table>
        </div>

        <!-- Teams and Score -->
        <div class="teams-score">
            <table>
                <tr>
                    <td style="width: 35%;">
                        <div class="team-box">
                            <div class="team-label"><?= lang('Common.home_team') ?> (Home)</div>
                            <div class="team-name"><?= esc($match['home_team_name'] ?? lang('Common.team') . ' A') ?></div>
                        </div>
                    </td>
                    <td style="width: 30%;">
                        <div class="score-display">
                            <span class="score-number"><?= $match['team1_score'] ?? '0' ?></span>
                            <span class="score-separator">-</span>
                            <span class="score-number"><?= $match['team2_score'] ?? '0' ?></span>
                        </div>
                    </td>
                    <td style="width: 35%;">
                        <div class="team-box">
                            <div class="team-label"><?= lang('Common.away_team') ?> (Away)</div>
                            <div class="team-name"><?= esc($match['away_team_name'] ?? lang('Common.team') . ' B') ?></div>
                        </div>
                    </td>
                </tr>
            </table>
        </div>

        <!-- Lineups Section -->
        <table class="two-col">
            <tr>
                <td>
                    <div class="section-header section-header-blue"><?= lang('Common.starters') ?> - <?= esc($match['home_team_name'] ?? lang('Common.team') . ' A') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                                <th width="60"><?= lang('Common.position') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($team1Starters)): ?>
                                <?php foreach ($team1Starters as $player): ?>
                                <tr>
                                    <td style="text-align: center;"><?= esc($player['shirt_number'] ?? '') ?></td>
                                    <td><?= esc(trim(($player['first_name'] ?? '') . ' ' . ($player['last_name'] ?? ''))) ?></td>
                                    <td style="text-align: center;"><?= esc($player['position_name'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="text-align: center; color: #999;"><?= lang('Common.no_data') ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="section-header section-header-blue"><?= lang('Common.starters') ?> - <?= esc($match['away_team_name'] ?? lang('Common.team') . ' B') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                                <th width="60"><?= lang('Common.position') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($team2Starters)): ?>
                                <?php foreach ($team2Starters as $player): ?>
                                <tr>
                                    <td style="text-align: center;"><?= esc($player['shirt_number'] ?? '') ?></td>
                                    <td><?= esc(trim(($player['first_name'] ?? '') . ' ' . ($player['last_name'] ?? ''))) ?></td>
                                    <td style="text-align: center;"><?= esc($player['position_name'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="text-align: center; color: #999;"><?= lang('Common.no_data') ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Substitutes Section -->
        <table class="two-col">
            <tr>
                <td>
                    <div class="section-header" style="background: #546e7a;"><?= lang('Common.substitutes') ?> - <?= esc($match['home_team_name'] ?? lang('Common.team') . ' A') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                                <th width="60"><?= lang('Common.position') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($team1Substitutes)): ?>
                                <?php foreach ($team1Substitutes as $player): ?>
                                <tr>
                                    <td style="text-align: center;"><?= esc($player['shirt_number'] ?? '') ?></td>
                                    <td><?= esc(trim(($player['first_name'] ?? '') . ' ' . ($player['last_name'] ?? ''))) ?></td>
                                    <td style="text-align: center;"><?= esc($player['position_name'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="text-align: center; color: #999;"><?= lang('Common.no_data') ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="section-header" style="background: #546e7a;"><?= lang('Common.substitutes') ?> - <?= esc($match['away_team_name'] ?? lang('Common.team') . ' B') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                                <th width="60"><?= lang('Common.position') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($team2Substitutes)): ?>
                                <?php foreach ($team2Substitutes as $player): ?>
                                <tr>
                                    <td style="text-align: center;"><?= esc($player['shirt_number'] ?? '') ?></td>
                                    <td><?= esc(trim(($player['first_name'] ?? '') . ' ' . ($player['last_name'] ?? ''))) ?></td>
                                    <td style="text-align: center;"><?= esc($player['position_name'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" style="text-align: center; color: #999;"><?= lang('Common.no_data') ?></td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Goals Section -->
        <table class="two-col">
            <tr>
                <td>
                    <div class="section-header"><?= lang('Common.goals_scored') ?> - <?= esc($match['home_team_name'] ?? lang('Common.team') . ' A') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_order') ?></th>
                                <th width="35"><?= lang('Common.minute') ?></th>
                                <th width="35"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $goalCount = max(3, count($team1Goals)); ?>
                            <?php for ($i = 0; $i < $goalCount; $i++): ?>
                            <tr class="<?= $i >= count($team1Goals) ? 'empty-row' : '' ?>">
                                <td style="text-align: center;"><?= $i + 1 ?></td>
                                <td style="text-align: center;"><?= $team1Goals[$i]['minute'] ?? '' ?><?= !empty($team1Goals[$i]['minute']) ? "'" : '' ?></td>
                                <td style="text-align: center;"><?= $team1Goals[$i]['shirt_number'] ?? '' ?></td>
                                <td>
                                    <?= $team1Goals[$i]['name'] ?? '' ?>
                                    <?php if (!empty($team1Goals[$i]['is_own_goal'])): ?>
                                        <span class="own-goal">(OG)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="section-header"><?= lang('Common.goals_scored') ?> - <?= esc($match['away_team_name'] ?? lang('Common.team') . ' B') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_order') ?></th>
                                <th width="35"><?= lang('Common.minute') ?></th>
                                <th width="35"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $goalCount = max(3, count($team2Goals)); ?>
                            <?php for ($i = 0; $i < $goalCount; $i++): ?>
                            <tr class="<?= $i >= count($team2Goals) ? 'empty-row' : '' ?>">
                                <td style="text-align: center;"><?= $i + 1 ?></td>
                                <td style="text-align: center;"><?= $team2Goals[$i]['minute'] ?? '' ?><?= !empty($team2Goals[$i]['minute']) ? "'" : '' ?></td>
                                <td style="text-align: center;"><?= $team2Goals[$i]['shirt_number'] ?? '' ?></td>
                                <td>
                                    <?= $team2Goals[$i]['name'] ?? '' ?>
                                    <?php if (!empty($team2Goals[$i]['is_own_goal'])): ?>
                                        <span class="own-goal">(OG)</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Substitutions Section -->
        <table class="two-col">
            <tr>
                <td>
                    <div class="section-header section-header-green"><?= lang('Common.substitution') ?> - <?= esc($match['home_team_name'] ?? lang('Common.team') . ' A') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_order') ?></th>
                                <th width="35"><?= lang('Common.minute') ?></th>
                                <th><?= lang('Common.sub_out') ?></th>
                                <th><?= lang('Common.sub_in') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $subCount = max(3, count($team1Subs)); ?>
                            <?php for ($i = 0; $i < $subCount; $i++): ?>
                            <tr class="<?= $i >= count($team1Subs) ? 'empty-row' : '' ?>">
                                <td style="text-align: center;"><?= $i + 1 ?></td>
                                <td style="text-align: center;"><?= $team1Subs[$i]['minute'] ?? '' ?><?= !empty($team1Subs[$i]['minute']) ? "'" : '' ?></td>
                                <td>
                                    <?php if (!empty($team1Subs[$i]['name'])): ?>
                                        #<?= $team1Subs[$i]['shirt_number'] ?? '' ?> <?= $team1Subs[$i]['name'] ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($team1Subs[$i]['player2_name'])): ?>
                                        #<?= $team1Subs[$i]['player2_shirt_number'] ?? '' ?> <?= $team1Subs[$i]['player2_name'] ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="section-header section-header-green"><?= lang('Common.substitution') ?> - <?= esc($match['away_team_name'] ?? lang('Common.team') . ' B') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_order') ?></th>
                                <th width="35"><?= lang('Common.minute') ?></th>
                                <th><?= lang('Common.sub_out') ?></th>
                                <th><?= lang('Common.sub_in') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $subCount = max(3, count($team2Subs)); ?>
                            <?php for ($i = 0; $i < $subCount; $i++): ?>
                            <tr class="<?= $i >= count($team2Subs) ? 'empty-row' : '' ?>">
                                <td style="text-align: center;"><?= $i + 1 ?></td>
                                <td style="text-align: center;"><?= $team2Subs[$i]['minute'] ?? '' ?><?= !empty($team2Subs[$i]['minute']) ? "'" : '' ?></td>
                                <td>
                                    <?php if (!empty($team2Subs[$i]['name'])): ?>
                                        #<?= $team2Subs[$i]['shirt_number'] ?? '' ?> <?= $team2Subs[$i]['name'] ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($team2Subs[$i]['player2_name'])): ?>
                                        #<?= $team2Subs[$i]['player2_shirt_number'] ?? '' ?> <?= $team2Subs[$i]['player2_name'] ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Yellow Cards Section -->
        <table class="two-col">
            <tr>
                <td>
                    <div class="section-header section-header-yellow"><?= lang('Common.yellow_card') ?> - <?= esc($match['home_team_name'] ?? lang('Common.team') . ' A') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_order') ?></th>
                                <th width="35"><?= lang('Common.minute') ?></th>
                                <th width="35"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $cardCount = max(2, count($team1Cards)); ?>
                            <?php for ($i = 0; $i < $cardCount; $i++): ?>
                            <tr class="<?= $i >= count($team1Cards) ? 'empty-row' : '' ?>">
                                <td style="text-align: center;"><?= $i + 1 ?></td>
                                <td style="text-align: center;"><?= $team1Cards[$i]['minute'] ?? '' ?><?= !empty($team1Cards[$i]['minute']) ? "'" : '' ?></td>
                                <td style="text-align: center;"><?= $team1Cards[$i]['shirt_number'] ?? '' ?></td>
                                <td><?= $team1Cards[$i]['name'] ?? '' ?></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="section-header section-header-yellow"><?= lang('Common.yellow_card') ?> - <?= esc($match['away_team_name'] ?? lang('Common.team') . ' B') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_order') ?></th>
                                <th width="35"><?= lang('Common.minute') ?></th>
                                <th width="35"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $cardCount = max(2, count($team2Cards)); ?>
                            <?php for ($i = 0; $i < $cardCount; $i++): ?>
                            <tr class="<?= $i >= count($team2Cards) ? 'empty-row' : '' ?>">
                                <td style="text-align: center;"><?= $i + 1 ?></td>
                                <td style="text-align: center;"><?= $team2Cards[$i]['minute'] ?? '' ?><?= !empty($team2Cards[$i]['minute']) ? "'" : '' ?></td>
                                <td style="text-align: center;"><?= $team2Cards[$i]['shirt_number'] ?? '' ?></td>
                                <td><?= $team2Cards[$i]['name'] ?? '' ?></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Red Cards Section -->
        <table class="two-col">
            <tr>
                <td>
                    <div class="section-header section-header-red"><?= lang('Common.red_card') ?> - <?= esc($match['home_team_name'] ?? lang('Common.team') . ' A') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_order') ?></th>
                                <th width="35"><?= lang('Common.minute') ?></th>
                                <th width="35"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $cardCount = max(2, count($team1RedCards)); ?>
                            <?php for ($i = 0; $i < $cardCount; $i++): ?>
                            <tr class="<?= $i >= count($team1RedCards) ? 'empty-row' : '' ?>">
                                <td style="text-align: center;"><?= $i + 1 ?></td>
                                <td style="text-align: center;"><?= $team1RedCards[$i]['minute'] ?? '' ?><?= !empty($team1RedCards[$i]['minute']) ? "'" : '' ?></td>
                                <td style="text-align: center;"><?= $team1RedCards[$i]['shirt_number'] ?? '' ?></td>
                                <td><?= $team1RedCards[$i]['name'] ?? '' ?></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </td>
                <td>
                    <div class="section-header section-header-red"><?= lang('Common.red_card') ?> - <?= esc($match['away_team_name'] ?? lang('Common.team') . ' B') ?></div>
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th width="30"><?= lang('Common.number_order') ?></th>
                                <th width="35"><?= lang('Common.minute') ?></th>
                                <th width="35"><?= lang('Common.number_abbr') ?></th>
                                <th><?= lang('Common.full_name') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php $cardCount = max(2, count($team2RedCards)); ?>
                            <?php for ($i = 0; $i < $cardCount; $i++): ?>
                            <tr class="<?= $i >= count($team2RedCards) ? 'empty-row' : '' ?>">
                                <td style="text-align: center;"><?= $i + 1 ?></td>
                                <td style="text-align: center;"><?= $team2RedCards[$i]['minute'] ?? '' ?><?= !empty($team2RedCards[$i]['minute']) ? "'" : '' ?></td>
                                <td style="text-align: center;"><?= $team2RedCards[$i]['shirt_number'] ?? '' ?></td>
                                <td><?= $team2RedCards[$i]['name'] ?? '' ?></td>
                            </tr>
                            <?php endfor; ?>
                        </tbody>
                    </table>
                </td>
            </tr>
        </table>

        <!-- Penalty Kicks Section (if used) -->
        <?php if (!empty($match['is_penalty_used'])): ?>
        <div class="penalty-section">
            <div class="penalty-title"><?= lang('Common.penalty_shootout_result') ?></div>
            <table style="width: 100%; border: none;">
                <tr>
                    <td style="width: 40%; text-align: center; border: none;">
                        <div style="font-size: 14px; font-weight: bold;"><?= esc($match['home_team_name'] ?? 'ทีม A') ?></div>
                    </td>
                    <td style="width: 20%; border: none;">
                        <div class="penalty-score">
                            <?= $match['team1_penalty'] ?? '0' ?> - <?= $match['team2_penalty'] ?? '0' ?>
                        </div>
                    </td>
                    <td style="width: 40%; text-align: center; border: none;">
                        <div style="font-size: 14px; font-weight: bold;"><?= esc($match['away_team_name'] ?? 'ทีม B') ?></div>
                    </td>
                </tr>
            </table>
        </div>
        <?php endif; ?>

        <!-- Signature Section -->
        <table class="signature-section" style="width: 100%; margin-top: 25px;">
            <tr>
                <td style="width: 33%;">
                    <div class="signature-line"></div><br>
                    ( _______________________ )<br>
                    <strong><?= lang('Common.referee') ?></strong>
                </td>
                <td style="width: 33%;">
                    <div class="signature-line"></div><br>
                    ( _______________________ )<br>
                    <strong><?= lang('Common.assistant_referee') ?> 1</strong>
                </td>
                <td style="width: 33%;">
                    <div class="signature-line"></div><br>
                    ( _______________________ )<br>
                    <strong><?= lang('Common.assistant_referee') ?> 2</strong>
                </td>
            </tr>
        </table>

        <!-- Footer -->
        <div style="text-align: center; margin-top: 15px; font-size: 10px; color: #666;">
            <?= lang('Common.printed_at') ?>: <?= date('d/m/Y H:i:s') ?> <?= lang('Common.time_suffix') ?>
        </div>
    </div>
</body>
</html>
