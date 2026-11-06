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
            line-height: 1.4;
        }
        .container {
            width: 100%;
            padding: 10px;
        }

        /* Header with tournament logo */
        .header {
            text-align: center;
            border: 2px solid #06346D;
            border-radius: 6px;
            padding: 15px 12px;
            margin-bottom: 15px;
            background: linear-gradient(135deg, #06346D 0%, #042441 100%);
            color: #fff;
        }
        .header-logo {
            width: 70px;
            height: 70px;
            object-fit: contain;
            margin-bottom: 8px;
            border-radius: 6px;
            border: 2px solid #F3B71B;
            background: #fff;
            padding: 4px;
        }
        .header h1 {
            font-size: 22px;
            font-weight: bold;
            margin-bottom: 4px;
            color: #fff;
        }
        .header h2 {
            font-size: 15px;
            font-weight: normal;
            color: #F3B71B;
        }

        /* Summary bar */
        .summary {
            margin-bottom: 12px;
            font-size: 14px;
            padding: 8px 12px;
            background: #F2F6FA;
            border-left: 4px solid #06346D;
            border-radius: 0 4px 4px 0;
        }
        .summary strong {
            color: #06346D;
        }

        /* Data Table */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        table.data-table th {
            background: #06346D;
            color: #fff;
            font-weight: bold;
            text-align: center;
            padding: 8px;
            font-size: 14px;
            border: 1px solid #042441;
        }
        table.data-table td {
            border: 1px solid #c5d4e3;
            padding: 6px 8px;
            font-size: 14px;
            vertical-align: middle;
        }
        .text-center {
            text-align: center;
        }
        .team-logo {
            width: 35px;
            height: 35px;
            object-fit: contain;
        }
        .row-even {
            background: #F2F6FA;
        }
        .row-odd {
            background: #fff;
        }

        /* Footer */
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 11px;
            color: #888;
            border-top: 1px solid #c5d4e3;
            padding-top: 8px;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <?php if (!empty($tournament['logo'])): ?>
                <img class="header-logo" src="<?= FCPATH . $tournament['logo'] ?>" alt=""><br>
            <?php endif; ?>
            <h1><?= esc($tournament['name'] ?? lang('Common.tournament')) ?></h1>
            <h2><?= lang('Common.team_registration_report') ?></h2>
        </div>

        <!-- Summary -->
        <div class="summary">
            <strong><?= lang('Common.registered_team_count') ?>:</strong> <?= count($teams) ?> <?= lang('Common.team_unit') ?>
        </div>

        <!-- Table -->
        <table class="data-table">
            <thead>
                <tr>
                    <th width="40"><?= lang('Common.order_number') ?></th>
                    <th width="55"><?= lang('Common.logo') ?></th>
                    <th><?= lang('Common.team_name') ?></th>
                    <th width="100"><?= lang('Common.registration_date') ?></th>
                    <th width="70"><?= lang('Common.time') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($teams)): ?>
                    <?php foreach ($teams as $index => $team): ?>
                    <tr class="<?= $index % 2 === 0 ? 'row-odd' : 'row-even' ?>">
                        <td class="text-center"><?= $index + 1 ?></td>
                        <td class="text-center">
                            <?php if (!empty($team['logo'])): ?>
                                <img class="team-logo" src="<?= FCPATH . $team['logo'] ?>" alt="">
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td><?= esc($team['name'] ?? '-') ?></td>
                        <td class="text-center">
                            <?php if (!empty($team['create_date'])): ?>
                                <?= date('d/m/Y', strtotime($team['create_date'])) ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if (!empty($team['create_date'])): ?>
                                <?= date('H:i', strtotime($team['create_date'])) ?> <?= lang('Common.time_suffix') ?>
                            <?php else: ?>
                                -
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="text-center" style="padding: 20px; color: #999;"><?= lang('Common.no_team_registered') ?></td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Footer -->
        <div class="footer">
            <?= lang('Common.printed_at') ?>: <?= date('d/m/Y H:i:s') ?> <?= lang('Common.time_suffix') ?>
        </div>
    </div>
</body>
</html>
