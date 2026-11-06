<?php $this->extend('template/layout') ?>

<?= $this->section('content') ?>
<div class="content">

    <!-- Start Content-->
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <h4 class="header-title"><?= lang('Common.academy_join_search') ?></h4>
                        <p class="sub-header">
                            <?= lang('Common.academy_join_desc') ?>
                        </p>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <input type="text" class="form-control" id="search-team-input" placeholder="<?= lang('Common.academy_search_placeholder') ?>">
                                    <button class="btn btn-primary" type="button" id="btn-search-team">
                                        <i class="fas fa-search"></i> <?= lang('Common.search') ?>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <div id="search-results" class="row">
                            <!-- Results will be displayed here -->
                            <div class="col-12 text-center text-muted mt-5">
                                <p><?= lang('Common.academy_search_prompt') ?></p>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const btnSearch = document.getElementById('btn-search-team');
        const inputSearch = document.getElementById('search-team-input');
        const resultsDiv = document.getElementById('search-results');

        function searchTeams() {
            const keyword = inputSearch.value.trim();
            if(!keyword) return;

            resultsDiv.innerHTML = '<div class="col-12 text-center"><div class="spinner-border text-primary" role="status"></div></div>';

            fetch('<?= base_url("academy/api/search") ?>?keyword=' + encodeURIComponent(keyword))
                .then(response => response.json())
                .then(data => {
                    resultsDiv.innerHTML = '';
                    if(data.length === 0) {
                        resultsDiv.innerHTML = '<div class="col-12 text-center text-muted"><p><?= lang('Common.academy_not_found') ?></p></div>';
                        return;
                    }

                    data.forEach(academy => {
                        const logoUrl = academy.logo ? '<?= base_url() ?>/' + academy.logo : '<?= base_url("public/img/no-image.png") ?>';
                        const html = `
                            <div class="col-md-4 mb-3">
                                <div class="card border">
                                    <div class="card-body text-center">
                                        <img src="${logoUrl}" alt="${academy.name}" class="rounded-circle mb-2" style="width: 80px; height: 80px; object-fit: cover;">
                                        <h5 class="card-title">${academy.name}</h5>
                                        ${academy.name_en ? '<p class="text-muted small mb-2">' + academy.name_en + '</p>' : ''}
                                        <button class="btn btn-success btn-sm btn-join" data-id="${academy.id}"><?= lang('Common.academy_join_request') ?></button>
                                    </div>
                                </div>
                            </div>
                        `;
                        resultsDiv.insertAdjacentHTML('beforeend', html);
                    });

                    document.querySelectorAll('.btn-join').forEach(btn => {
                        btn.addEventListener('click', function() {
                            const teamId = this.getAttribute('data-id');
                            joinTeam(teamId);
                        });
                    });
                })
                .catch(err => {
                    console.error(err);
                    resultsDiv.innerHTML = '<div class="col-12 text-center text-danger"><p><?= lang('Common.error') ?></p></div>';
                });
        }

        function joinTeam(academyId) {
            Swal.fire({
                title: '<?= lang('Common.academy_confirm_join') ?>',
                text: "<?= lang('Common.academy_confirm_join_msg') ?>",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '<?= lang('Common.academy_yes_send_request') ?>',
                cancelButtonText: '<?= lang('Common.cancel') ?>'
            }).then((result) => {
                if (result.isConfirmed) {
                    fetch('<?= base_url("academy/api/join-request") ?>', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ academy_id: academyId })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if(data.success) {
                            Swal.fire('<?= lang('Common.success') ?>', data.message, 'success');
                        } else {
                            Swal.fire('<?= lang('Common.notification') ?>', data.message, 'warning');
                        }
                    })
                    .catch(err => {
                        Swal.fire('<?= lang('Common.error') ?>', '<?= lang('Common.academy_join_error') ?>', 'error');
                    });
                }
            });
        }

        btnSearch.addEventListener('click', searchTeams);
        inputSearch.addEventListener('keyup', function(e) {
            if (e.key === 'Enter') searchTeams();
        });

        // Check for keyword in URL
        const urlParams = new URLSearchParams(window.location.search);
        const keyword = urlParams.get('keyword');
        if (keyword) {
            inputSearch.value = keyword;
            searchTeams();
        }

        <?php if (session()->getFlashdata('error')) : ?>
            Swal.fire({
                icon: 'info',
                title: '<?= lang('Common.notification') ?>',
                text: '<?= session()->getFlashdata('error') ?>',
                confirmButtonText: '<?= lang('Common.confirm') ?>'
            });
        <?php endif; ?>
    });
</script>
<?= $this->endSection() ?>
