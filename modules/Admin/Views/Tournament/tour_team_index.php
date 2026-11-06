<?php $this->extend('template/admin_layout') ?>

<?php $this->section('style'); ?>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.css" />
<style>
    .btn-view-slip {
        border-radius: 25px;
        border: 2px solid var(--secondary-color);
        color: var(--secondary-color);
        padding: 3px 12px;
        font-size: 0.85rem;
        font-weight: 600;
        transition: all 0.3s ease;
        background: transparent;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 8px;
        /* ปุ่มสลิปกับปุ่มเอกสารซ้อนกันในคอลัมน์เดียว ให้กว้างเท่ากันและไม่ตัดบรรทัด */
        white-space: nowrap;
        min-width: 120px;
    }
    /* คอลัมน์สถานะชำระเงินเรียงลงมา: ป้าย → ปุ่มสลิป → ปุ่มเอกสาร */
    #table td .badge {
        font-size: 0.8rem;
        font-weight: 600;
    }
    .btn-view-slip:hover {
        background: var(--secondary-color);
        color: white;
    }
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<!-- <> -->
<div class="card border-0 shadow-sm" style="border-radius: var(--border-radius);">
    <div class="card-body p-4">
        <?php echo view("Modules\Admin\Views\Tournament\subnav.php"); ?>

        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 mb-4 mt-3">
            <div class="d-flex align-items-center">
                <div class="d-flex align-items-center justify-content-center rounded-circle me-3" style="width: 50px; height: 50px; background: var(--gold-gradient); box-shadow: var(--gold-shadow);">
                    <i class="ti ti-users-group text-white fs-4"></i>
                </div>
                <div>
                    <h4 class="fw-bold mb-1" style="color: var(--secondary-color);"><?= lang('Common.participating_teams') ?></h4>
                    <p class="text-muted mb-0 small"><?= lang('Common.manage_teams_in_tournament') ?></p>
                </div>
            </div>

            <div class="d-flex gap-2">
                <a href="<?= base_url("/admin/tournament/export_players/" . $tour_id) ?>" target="_blank" class="btn btn-success px-3 py-2 fw-bold shadow-sm" style="border-radius: 25px; border: none; color: white;">
                    <i class="ti ti-file-export me-2"></i> Export Player
                </a>
                <a href="<?= base_url("/admin/tournament/match-setup/" . $tour_id) ?>" class="btn btn-primary px-3 py-2 fw-bold shadow-sm" style="border-radius: 25px; background: var(--gold-gradient); border: none; box-shadow: var(--gold-shadow); color: white;">
                    <i class="ti ti-calendar-event me-2"></i> <?= lang('Common.create_match') ?>
                </a>
                <a href="<?= base_url("/admin/tournament/tournament-team-manage/" . $tour_id) ?>" class="btn btn-light border px-3 py-2 fw-bold text-muted shadow-sm" style="border-radius: 25px;">
                    <i class="ti ti-plus me-2"></i> <?= lang('Common.add_team_to_tournament') ?>
                </a>
            </div>

        </div>
    </div>

    <div class="alert alert-light border-0 shadow-sm d-flex align-items-center mb-4" role="alert" style="border-radius: var(--border-radius); background: rgba(255, 193, 7, 0.1); color: #856404;">
        <i class="ti ti-info-circle me-2 fs-5"></i>
        <div>
            <strong><?= lang('Common.recommendation') ?>:</strong> <?= lang('Common.approve_teams_before_match') ?>
        </div>
    </div>

    <div class="table-responsive">
        <table id="table" class="table table-hover align-middle mb-0" style="width: 100%;">
            <thead style="background: #f8f9fa; color: var(--secondary-color);">
                <tr>
                    <th class="py-3 ps-4 text-center" width="10%" style="border-radius: var(--border-radius) 0 0 0;"><?= lang('Common.order_number') ?></th>
                    <th class="py-3 text-center" width="15%"><?= lang('Common.logo') ?></th>
                    <th class="py-3 text-start"><?= lang('Common.team_name') ?></th>
                    <th class="py-3 text-center"><?= lang('Common.payment_status') ?></th>
                    <th class="py-3 text-center" width="12%"><?= lang('Common.player_jersey') ?></th>
                    <th class="py-3 text-center" width="15%"><?= lang('Common.status') ?></th>
                    <th class="py-3 pe-4 text-center" width="20%" style="border-radius: 0 var(--border-radius) 0 0;"><?= lang('Common.tools') ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
</div>
<?php $this->endSection() ?>

<?php $this->section('scripts'); ?>
<script src="https://cdn.jsdelivr.net/npm/@fancyapps/ui@5.0/dist/fancybox/fancybox.umd.js"></script>
<script>
    const langConfirmDelete = '<?= lang("Common.confirm_delete_question") ?>';
    const langCannotRecover = '<?= lang("Common.cannot_recover_data") ?>';
    const langYesDeleteBtn = '<?= lang("Common.yes_delete") ?>';
    const langCancel = '<?= lang("Common.cancel") ?>';

    // Fancybox initialization
    Fancybox.bind("[data-fancybox]", {
        // Your custom options
    });

    // DataTable สำหรับแสดงทีมที่เข้าร่วม
    (function($) {
        $('#table').DataTable({
            processing: true,
            serverSide: true,
            language: dtLanguage,
            ajax: {
                url: '<?= base_url('admin/tournament/get-tournament-teams/' . $tour_id) ?>',
                method: "GET"
            },
            columnDefs: [{
                targets: -1,
                orderable: false
            }],
            columns: [{
                    data: '',
                    className: 'text-center'
                },
                {
                    data: 'logo',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'name',
                    className: 'text-left fw-bold'
                },
                {
                    data: 'payment_status',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'jerseys',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'register_tools',
                    className: 'text-center',
                    orderable: false
                },
                {
                    data: 'action',
                    className: 'text-center',
                    orderable: false
                }
            ],
            lengthMenu: [50, 100],
            drawCallback: function(settings) {
                console.log('DataTable has been redrawn.');
                // Re-initialize tooltips or other UI elements if needed
            }
        });
    })(jQuery);

    function deleteTournamentTeam(tour_team_id) {
        Swal.fire({
            title: langConfirmDelete,
            text: langCannotRecover,
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#3085d6",
            confirmButtonText: langYesDeleteBtn,
            cancelButtonText: langCancel
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    method: "POST",
                    url: "<?= base_url('admin/tournament/delete-tournament-team') ?>",
                    data: {
                        tour_team_id: tour_team_id
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

    function changeRegisterStatus(tour_team_id, status) {
        console.log(tour_team_id, status);
        $.ajax({
            method: "POST",
            url: "<?= base_url('admin/tournament/change-register-status') ?>",
            data: {
                tour_team_id: tour_team_id,
                register_status: status
            },
            success: function(res) {
                Swal.fire({
                    position: "center",
                    icon: res.status,
                    title: res.msg,
                    showConfirmButton: false,
                    timer: 1500
                }).then(() => {
                    $('#table').DataTable().ajax.reload(null, false);
                });
            }
        })
    }
</script>
<?php $this->endSection() ?>
