<?php $this->extend('template/admin_layout') ?>

<?php $this->section('content'); ?>
<div class="card shadow-sm mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div>
                <h4 class="card-title fw-bold mb-1"><?= lang('Common.player_report_title') ?></h4>
                <p class="text-muted mb-0"><?= lang('Common.search_view_all_players') ?></p>
            </div>
            <div class="d-none d-md-block">
                <i class="ti ti-users fs-1 text-primary opacity-50"></i>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 col-lg-4">
                <div class="input-group">
                    <span class="input-group-text bg-light border-end-0"><i class="ti ti-search"></i></span>
                    <input type="search" id="searchInput" class="form-control border-start-0 ps-0" placeholder="<?= lang('Common.search_name_team_position') ?>">
                </div>
            </div>
            <div class="col-md-6 col-lg-8 text-end">
                <!-- Filters can go here if needed -->
            </div>
        </div>

        <div class="table-responsive">
            <table id="table" class="table table-hover align-middle text-nowrap mb-0">
                <thead class="bg-light">
                    <tr>
                        <th scope="col" class="ps-3"><?= lang('Common.player') ?></th>
                        <th scope="col" class="text-center"><?= lang('Common.team_label') ?></th>
                        <th scope="col" class="text-center"><?= lang('Common.position') ?></th>
                        <th scope="col" class="text-center"><?= lang('Common.age') ?></th>
                        <th scope="col" class="text-center"><?= lang('Common.manage') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <!-- DataTables will populate this -->
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const langYearsUnit = '<?= lang("Common.years_unit") ?>';

    var player_position_value = '<?= json_encode($player_position_value) ?>';
    var positions = [];
    var position_counts = [];

    if(player_position_value) {
        try {
            JSON.parse(player_position_value).forEach(item => {
                positions.push(item.name_th);
                position_counts.push(Number(item.count_value));
            });
        } catch(e) {
            console.error("Error parsing position data", e);
        }
    }

    $(document).ready(function() {
        var table = $('#table').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            pageLength: 20,
            lengthMenu: [20, 50, 100],
            language: dtLanguage,
            dom: '<"d-none"f>rt<"d-flex justify-content-between align-items-center mt-3"ip>',
            ajax: {
                url: '<?= base_url('admin/report-guest/get-players') ?>',
                method: "POST",
                data: function(d) {
                    d.player_position_id = $('#player_position_id').val();
                }
            },
            columns: [{
                    name: 'first_name',
                    data: 'player_detail',
                    className: 'ps-3 text-start',
                    render: function(data, type, row) {
                        // Assuming data contains HTML or just name, we can enhance it if needed
                        // But keeping it simple as per original logic which seemed to return HTML
                        return '<div class="fw-semibold text-dark">' + data + '</div>';
                    }
                },
                {
                    data: 'team_name',
                    className: 'text-center',
                    render: function(data) {
                        return '<span class="badge bg-light text-dark border">' + data + '</span>';
                    }
                },
                {
                    data: 'position',
                    className: 'text-center',
                    render: function(data) {
                        return '<span class="badge bg-primary-subtle text-primary">' + data + '</span>';
                    }
                },
                {
                    data: 'age',
                    className: 'text-center',
                    render: function(data) {
                        return data + ' ' + langYearsUnit;
                    }
                },
                {
                    data: null,
                    className: 'text-center',
                    orderable: false,
                    render: function(data, type, row) {
                        // Assuming there's a link to details, usually it's constructed from ID
                        // Since I don't see the ID in the original columns explicitly, I'll assume 'player_detail' might have a link or I need to guess.
                        // Wait, the original code didn't have an action column.
                        // But usually a report needs a "View" button.
                        // The original code had: columns: [player_detail, team_name, position, age]
                        // Let's check if 'player_detail' is a link.
                        // If not, I should probably add a view button if I can get the ID.
                        // For now, I will stick to the original columns but style them.
                        // Actually, looking at the original code, there was no action column.
                        // But the user might want to click to see details.
                        // I will assume the 'player_detail' column sent from server might contain the link.
                        // If not, I'll leave it as is for now to avoid breaking logic.
                        return '';
                    }
                }
            ],
            columnDefs: [
                { targets: -1, visible: false } // Hiding the action column for now as I don't have the ID logic confirmed
            ]
        });

        $("#searchInput").keyup(function(){
            table.search($(this).val()).draw();
        });
    });
</script>
<?php $this->endSection(); ?>