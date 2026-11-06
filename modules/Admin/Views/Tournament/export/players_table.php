<!doctype html>
<html lang="th">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Export Players</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f8fb;
            color: #172033;
            font-family: Arial, "Helvetica Neue", sans-serif;
            padding: 24px;
        }
        .export-header {
            align-items: center;
            display: flex;
            gap: 16px;
            justify-content: space-between;
            margin-bottom: 18px;
        }
        .export-title {
            margin: 0;
            font-size: 22px;
            font-weight: 700;
        }
        .export-subtitle {
            color: #64748b;
            font-size: 13px;
            margin: 4px 0 0;
        }
        .table-wrap {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 8px;
            box-shadow: 0 10px 26px rgba(15, 23, 42, .07);
            overflow: hidden;
        }
        table {
            margin-bottom: 0 !important;
        }
        thead th {
            background: #102a4c !important;
            color: #fff !important;
            font-size: 12px;
            white-space: nowrap;
        }
        tbody td {
            font-size: 12px;
            vertical-align: middle;
        }
        .text-muted-row {
            color: #94a3b8;
        }
        @media print {
            body {
                background: #fff;
                padding: 0;
            }
            .no-print {
                display: none !important;
            }
            .table-wrap {
                border: 0;
                box-shadow: none;
            }
        }
    </style>
</head>
<body>
    <div class="export-header">
        <div>
            <h1 class="export-title">Export Players</h1>
            <p class="export-subtitle">
                Tournament: <?= esc($tournament['name'] ?? '-') ?> |
                Tour ID: <?= esc($tour_id) ?> |
                Total: <?= number_format(count($players ?? [])) ?>
            </p>
        </div>
        <div class="no-print">
            <!-- เพิ่มปุ่ม Export CSV -->
            <button type="button" class="btn btn-success btn-sm me-2" onclick="exportTableToCSV('players_export.csv')">Export CSV</button>
            <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">Print</button>
        </div>
    </div>

    <div class="table-wrap">
        <div class="table-responsive">
            <table id="playerTable" class="table table-bordered table-striped table-hover align-middle">
                <thead>
                    <tr>
                        <th class="text-center" style="width:60px;">No.</th>
                        <th class="text-center">Player ID</th>
                        <th class="text-center">Team Name</th>
                        <th class="text-center">Registration State</th>
                        <th class="text-center">First Name</th>
                        <th class="text-center">Last Name</th>
                        <th class="text-center">​Shirt No.</th>
                        <th class="text-center">Position</th>
                        <th class="text-center">Date of Birth</th>
                        <th class="text-center">Province</th>
                        <th class="text-center">District</th>
                        <th class="text-center">Sub-district</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($players)): ?>
                        <tr>
                            <td colspan="12" class="text-center text-muted-row py-4">No player data found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($players as $index => $player): ?>
                            <tr>
                                <td class="text-center"><?= $index + 1 ?></td>
                                <td><?= esc($player['player_id'] ?? '') ?></td>
                                <td class="text-center"><?= esc($player['team_name'] ?? '') ?></td>
                                <td class="text-center"><?= esc($player['register_status_text'] ?? '') ?></td>
                                <td class="text-center"><?= esc($player['first_name'] ?? '') ?></td>
                                <td class="text-center"><?= esc($player['last_name'] ?? '') ?></td>
                                <td class="text-center"><?= esc($player['shirt_number'] ?? '') ?></td>
                                <td class="text-center"><?= esc($player['position_name'] ?? '') ?></td>
                                <td class="text-center"><?= esc($player['birth_date'] ?? '-') ?></td>
                                <td class="text-center"><?= esc($player['province_name'] ?? 'N/A') ?></td>
                                <td class="text-center"><?= esc($player['district_name'] ?? 'N/A') ?></td>
                                <td class="text-center"><?= esc($player['subdistrict_name'] ?? 'N/A') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function exportTableToCSV(filename) {
            var csv = [];
            var rows = document.querySelectorAll("#playerTable tr");
            
            for (var i = 0; i < rows.length; i++) {
                var row = [], cols = rows[i].querySelectorAll("td, th");
                
                for (var j = 0; j < cols.length; j++) {
                    // ทำความสะอาดข้อมูล: ลบช่องว่างและใส่ฟันหนูครอบกรณีมีคอมม่าในเนื้อหา
                    let data = cols[j].innerText.replace(/"/g, '""');
                    row.push('"' + data + '"');
                }
                csv.push(row.join(","));
            }

            // ดาวน์โหลดไฟล์
            var csvFile = new Blob(["\ufeff" + csv.join("\n")], { type: "text/csv;charset=utf-8;" });
            var downloadLink = document.createElement("a");
            downloadLink.download = filename;
            downloadLink.href = window.URL.createObjectURL(csvFile);
            downloadLink.style.display = "none";
            document.body.appendChild(downloadLink);
            downloadLink.click();
        }
    </script>
</body>
</html>