<?php
$url = uri_string();
$sub_nav_active = explode('/', $url);
?>
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; overflow: hidden;">
    <div class="card-body p-2" style="background: #f8f9fa;">
        <ul class="nav nav-pills nav-fill gap-2">
            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-center gap-2 <?= ($sub_nav_active[0] == 'admin' && ($sub_nav_active[1] =='coach') && !isset($sub_nav_active[2])) ? 'active shadow-sm fw-bold' : '' ?>"
                   href="<?= base_url('admin/coach/') ?>"
                   style="border-radius: 8px; transition: all 0.2s;">
                    <i class="ti ti-user-circle fs-5"></i>
                    <span><?= lang('Common.coach_manager_info') ?></span>
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link d-flex align-items-center justify-content-center gap-2 <?= ($sub_nav_active[0] == 'admin' && (@$sub_nav_active[2] == 'certs-manage') ) ? 'active shadow-sm fw-bold' : '' ?>"
                   href="<?= base_url('admin/coach/certs-manage/'.@$sub_nav_active[3]) ?>"
                   onclick="<?php if(@$mode=='add'){?> Swal.fire('<?= lang("Common.warning") ?>', '<?= lang("Common.please_save_coach_first") ?>', 'warning'); return false;<?php }?>"
                   style="border-radius: 8px; transition: all 0.2s;">
                    <i class="ti ti-certificate fs-5"></i>
                    <span>Licence</span>
                </a>
            </li>
        </ul>
    </div>
</div>

<style>
    .nav-pills .nav-link {
        color: #64748b;
        background: transparent;
    }
    .nav-pills .nav-link:hover {
        background: #e2e8f0;
        color: #0f172a;
    }
    .nav-pills .nav-link.active {
        background: var(--bs-primary);
        color: white !important;
    }
</style>