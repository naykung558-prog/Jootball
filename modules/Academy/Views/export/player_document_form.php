<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" href="<?= base_url('public/img/logo01.png') ?>" type="image/icon type">
    <title>ใบส่งชื่อนักกีฬา</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <!-- Google Fonts: Kanit -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@200;300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: 'Kanit', sans-serif;
            background-color: #f8f9fa;
        }
        .form-container {
            background: white;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            margin-top: 20px;
            margin-bottom: 20px;
        }
        .table-player {
            font-size: 14px;
        }
        .table-player th {
            background-color: #1a3a6b;
            color: white;
            text-align: center;
            vertical-align: middle;
            font-weight: 500;
        }
        .table-player td {
            vertical-align: middle;
            text-align: center;
        }
        .player-name {
            text-align: left !important;
        }
        .checkbox-cell {
            width: 40px;
        }
        .match-header {
            background-color: #e9ecef !important;
        }
        .btn-save {
            background-color: #1a3a6b;
            border: none;
            padding: 12px 30px;
            font-size: 16px;
        }
        .btn-save:hover {
            background-color: #2c5282;
        }
        .info-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .info-row {
            margin-bottom: 15px;
        }
        .info-label {
            font-weight: 600;
            color: #1a3a6b;
            min-width: 120px;
            display: inline-block;
        }
        .team-logo-header {
            width: 80px;
            height: 80px;
            object-fit: contain;
        }
        .tournament-select {
            max-width: 400px;
        }
    </style>
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="form-container">
                    <!-- Header -->
                    <div class="text-center mb-4">
                        <?php
                        $teamLogoPath = !empty($team['logo']) ? base_url($team['logo']) : base_url('public/img/jootball_logo.png');
                        ?>
                        <img src="<?= $teamLogoPath ?>" alt="Team Logo" class="team-logo-header mb-2">
                        <h2 style="color: #1a3a6b;">ใบส่งชื่อนักกีฬา</h2>
                        <p class="text-muted">แบบฟอร์มกรอกรายชื่อนักกีฬาและเลือกผู้เล่นในแต่ละนัดการแข่งขัน</p>
                    </div>

                    <!-- Info Section -->
                    <div class="info-section">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">ชื่อทีม:</span>
                                    <span><?= esc($team['name']) ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">เบอร์ผู้ฝึกสอน/โค้ช:</span>
                                    <span><?= !empty($team['coach_phone']) ? esc($team['coach_phone']) : '-' ?></span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="info-row">
                                    <span class="info-label">ทัวร์นาเมนต์:</span>
                                    <span><?= esc($tournament['name']) ?></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Hidden fields -->
                    <input type="hidden" id="matchId" value="<?= $match_id ?? '' ?>">
                    <input type="hidden" id="tournamentId" value="<?= $tournament_id ?>">
                    <input type="hidden" id="teamId" value="<?= $team['id'] ?>">

                    <!-- Player Table -->
                    <div class="table-responsive">
                        <table class="table table-player table-bordered" id="playerTable">
                            <thead>
                                <tr>
                                    <th rowspan="3" width="5%">หมายเลข</th>
                                    <th rowspan="3" width="25%">ชื่อ - นามสกุล</th>
                                    <th colspan="21" class="match-header">การแข่งขันครั้งที่</th>
                                </tr>
                                <tr class="match-header">
                                    <?php for ($i = 1; $i <= 7; $i++) { ?>
                                        <th colspan="3"><?= $i ?></th>
                                    <?php } ?>
                                </tr>
                                <tr class="match-header">
                                    <?php for ($i = 1; $i <= 7; $i++) { ?>
                                        <th width="4%" title="Playing - ตัวจริง">P</th>
                                        <th width="4%" title="Substitution - สำรอง">S</th>
                                        <th width="4%" title="Non Playing - ลงเล่นไม่ได้">N</th>
                                    <?php } ?>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                if (!empty($players)) {
                                    foreach ($players as $key => $player) { 
                                        // Skip staff positions (5=staff, 6=staff, 7=staff)
                                        if (in_array($player['position_id'] ?? 0, [5, 6, 7])) {
                                            continue;
                                        }
                                ?>
                                    <tr>
                                        <td><?= esc($player['shirt_number']) ?></td>
                                        <td class="player-name"><?= esc($player['first_name'] . ' ' . $player['last_name']) ?></td>
                                        <?php for ($i = 1; $i <= 7; $i++) { ?>
                                            <td class="checkbox-cell">
                                                <input type="checkbox" 
                                                       name="players[<?= $player['id'] ?>][match_<?= $i ?>][P]" 
                                                       class="form-check-input player-checkbox" 
                                                       data-player="<?= $player['id'] ?>" 
                                                       data-match="<?= $i ?>" 
                                                       data-status="P">
                                            </td>
                                            <td class="checkbox-cell">
                                                <input type="checkbox" 
                                                       name="players[<?= $player['id'] ?>][match_<?= $i ?>][S]" 
                                                       class="form-check-input player-checkbox" 
                                                       data-player="<?= $player['id'] ?>" 
                                                       data-match="<?= $i ?>" 
                                                       data-status="S">
                                            </td>
                                            <td class="checkbox-cell">
                                                <input type="checkbox" 
                                                       name="players[<?= $player['id'] ?>][match_<?= $i ?>][N]" 
                                                       class="form-check-input player-checkbox" 
                                                       data-player="<?= $player['id'] ?>" 
                                                       data-match="<?= $i ?>" 
                                                       data-status="N">
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php 
                                    }
                                } else {
                                ?>
                                    <tr>
                                        <td colspan="24" class="text-center py-4">
                                            <i class="bi bi-inbox" style="font-size: 2rem;"></i>
                                            <p class="mt-2 text-muted">ไม่พบนักกีฬาในทีม</p>
                                        </td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Legend -->
                    <div class="mt-3 p-3" style="background-color: #f8f9fa; border-radius: 5px;">
                        <strong>หมายเหตุ:</strong>
                        <span class="ms-2">P = PLAYING ผู้เล่นตัวจริง</span>
                        <span class="ms-3">S = SUBSTITUTION ผู้เล่นสำรอง</span>
                        <span class="ms-3">N = NON PLAYING ผู้เล่นที่ลงเล่นไม่ได้</span>
                    </div>

                    <!-- Action Buttons -->
                    <div class="text-center mt-4">
                        <button type="button" class="btn btn-primary btn-save" id="btnSave">
                            <i class="bi bi-save me-2"></i>บันทึกรายชื่อ
                        </button>
                        <a href="<?= base_url('team/player-document-list?id=' . $team['id'] . '&tour_id=' . $tournament_id . '&match_id=' . ($match_id ?? '')) ?>"
                           class="btn btn-secondary btn-save ms-2"
                           target="_blank"
                           id="btnPrintLink">
                            <i class="bi bi-printer me-2"></i>พิมพ์
                        </a>
                        <a href="<?= base_url('lineup/' . $academy_id) ?>"
                           class="btn btn-outline-secondary btn-save ms-2">
                            <i class="bi bi-arrow-left me-2"></i>ย้อนกลับ
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    // Global variables
    var matchId = null;
    var tournamentId = null;
    var teamId = null;
    
    $(document).ready(function() {
        // Get values from hidden fields
        matchId = $('#matchId').val();
        tournamentId = $('#tournamentId').val();
        teamId = $('#teamId').val();
        
        // Fallback: Get from URL query string if hidden fields are empty
        const urlParams = new URLSearchParams(window.location.search);
        if (!matchId) matchId = urlParams.get('match_id');
        if (!tournamentId) tournamentId = urlParams.get('tournament_id');
        if (!teamId) teamId = urlParams.get('team_id');
        
        console.log('Initial values:');
        console.log('teamId:', teamId, 'from hidden:', $('#teamId').val());
        console.log('tournamentId:', tournamentId, 'from hidden:', $('#tournamentId').val());
        console.log('matchId:', matchId, 'from hidden:', $('#matchId').val());

        // Load saved data on page load
        function loadSavedData() {
            if (!matchId) {
                // Clear all checkboxes if no match selected
                $('.player-checkbox').prop('checked', false);
                return;
            }

            $.ajax({
                url: '<?= base_url('lineup/load') ?>?team_id=' + teamId + '&tournament_id=' + tournamentId + '&match_id=' + matchId,
                type: 'GET',
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        // Clear all checkboxes first
                        $('.player-checkbox').prop('checked', false);

                        // Check the saved checkboxes
                        const data = response.data;
                        for (const playerId in data) {
                            for (const matchKey in data[playerId]) {
                                for (const status in data[playerId][matchKey]) {
                                    $(`.player-checkbox[data-player="${playerId}"][data-match="${matchKey.replace('match_', '')}"][data-status="${status}"]`).prop('checked', true);
                                }
                            }
                        }
                    }
                },
                error: function(xhr, status, error) {
                    console.log('Error loading saved data:', error);
                }
            });
        }

        // Load saved data on initial page load
        loadSavedData();

        // Ensure only one checkbox per match per player is selected (P, S, N are mutually exclusive)
        $('.player-checkbox').on('change', function() {
            if ($(this).is(':checked')) {
                const playerId = $(this).data('player');
                const matchNum = $(this).data('match');

                // Uncheck all other checkboxes for this player in this match
                $(`.player-checkbox[data-player="${playerId}"][data-match="${matchNum}"]`).not(this).prop('checked', false);
            }
        });

        // Save button click handler
        $('#btnSave').on('click', function() {
            if (!matchId) {
                Swal.fire({
                    icon: 'warning',
                    title: 'แจ้งเตือน',
                    text: 'ไม่มี match_id กรุณาตรวจสอบ URL',
                    confirmButtonText: 'ตกลง'
                });
                return;
            }

            const playerData = {};
            $('.player-checkbox:checked').each(function() {
                const playerId = $(this).data('player');
                const matchNum = $(this).data('match');
                const status = $(this).data('status');

                if (!playerData[playerId]) {
                    playerData[playerId] = {};
                }
                if (!playerData[playerId]['match_' + matchNum]) {
                    playerData[playerId]['match_' + matchNum] = {};
                }
                playerData[playerId]['match_' + matchNum][status] = 1;
            });

            // Prepare data for submission
            const submitData = {
                tournament_id: tournamentId,
                team_id: teamId,
                match_id: matchId,
                players: playerData
            };

            console.log('Saving data:');
            console.log('team_id:', teamId);
            console.log('tournament_id:', tournamentId);
            console.log('match_id:', matchId);
            console.log('submitData:', submitData);

            // Save data via AJAX
            $.ajax({
                url: '<?= base_url('lineup/save') ?>',
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify(submitData),
                dataType: 'json',
                success: function(response) {
                    if (response.status === 'success') {
                        Swal.fire({
                            icon: 'success',
                            title: 'สำเร็จ',
                            text: 'บันทึกข้อมูลสำเร็จ',
                            timer: 2000,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire({
                            icon: 'error',
                            title: 'เกิดข้อผิดพลาด',
                            text: 'เกิดข้อผิดพลาด: ' + response.message
                        });
                    }
                },
                error: function(xhr, status, error) {
                    Swal.fire({
                        icon: 'error',
                        title: 'เกิดข้อผิดพลาด',
                        text: 'เกิดข้อผิดพลาดในการบันทึก: ' + error
                    });
                }
            });
        });

        // Update print link with current values
        $('#btnPrintLink').attr('href', '<?= base_url('team/player-document-list') ?>?id=' + teamId + '&tour_id=' + tournamentId + '&match_id=' + matchId);
    });
    </script>
</body>

</html>
