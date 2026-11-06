<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid px-4 py-4">
    <div class="row justify-content-center">
        <div class="col-md-12">

            <?php
            // แผนที่ชื่อทีม (สร้างจากข้อมูลแมตช์)
            $teamNames = [];
            foreach ($matches as $match) {
                $teamNames[$match['team1_id']] = $match['team1_name'];
                $teamNames[$match['team2_id']] = $match['team2_name'];
            }

            // ฟังก์ชันแสดงผลการแข่งขันในรูปแบบตาราง
            function displayMatches($matches)
            {
                $groupedMatches = [];
                foreach ($matches as $match) {
                    $group = $match['group_id'] ?: 'No Group';
                    $groupedMatches[$group][] = $match;
                }

                foreach ($groupedMatches as $group => $groupMatches) {
            ?>
                    <div class="card border-0 shadow-sm mb-4" style="border-radius: var(--border-radius);">
                        <div class="card-header bg-white border-bottom py-3">
                            <h5 class="card-title fw-bold mb-0 text-dark">
                                <i class="ti ti-tournament me-2 text-primary"></i> <?= lang('Common.group') ?> <?= $group ?>
                            </h5>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover align-middle mb-0">
                                    <thead style="background: #f8f9fa; color: var(--primary-color);">
                                        <tr>
                                            <th class="py-3 ps-4" width="35%"><?= lang('Common.home_team') ?></th>
                                            <th class="py-3 text-center" width="10%"><?= lang('Common.score') ?></th>
                                            <th class="py-3 text-center" width="10%">-</th>
                                            <th class="py-3 text-center" width="10%"><?= lang('Common.score') ?></th>
                                            <th class="py-3 pe-4 text-end" width="35%"><?= lang('Common.away_team') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($groupMatches as $match) { ?>
                                            <tr>
                                                <td class="ps-4 fw-bold text-dark"><?= $match['team1_name'] ?></td>
                                                <td class="text-center fw-bold text-primary fs-5"><?= $match['team1_score'] ?></td>
                                                <td class="text-center text-muted">VS</td>
                                                <td class="text-center fw-bold text-danger fs-5"><?= $match['team2_score'] ?></td>
                                                <td class="pe-4 text-end fw-bold text-dark"><?= $match['team2_name'] ?></td>
                                            </tr>
                                        <?php } ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
            <?php
                }
            }

            // แสดงผลการแข่งขัน
            displayMatches($matches);
            ?>

        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?php $this->endSection(); ?>