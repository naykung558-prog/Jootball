<?= $this->extend('\App\Views\template\admin_layout') ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-lg-12 d-flex align-items-stretch">
        <div class="card w-100">
            <div class="card-body p-4">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="card-title fw-semibold"><?= $title ?></h5>
                    <a href="<?= base_url('admin/role/user-roles') ?>" class="btn btn-outline-secondary">
                        <i class="ti ti-arrow-left"></i> <?= lang('Common.back') ?>
                    </a>
                </div>

                <?php if (session()->getFlashdata('error')): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= session()->getFlashdata('error') ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                <?php endif; ?>

                <div class="row mb-4">
                    <div class="col-md-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><?= lang('Common.user_information') ?></h6>
                                <div class="row">
                                    <div class="col-md-12">
                                        <p><strong><?= lang('Common.username') ?>:</strong> <?= esc($user['username']) ?></p>
                                        <p><strong><?= lang('Common.status') ?>:</strong> <?= $user['access_login'] ? lang('Common.active') : lang('Common.inactive') ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="<?= base_url('admin/role/save-user-roles') ?>" method="post" class="once-only">
                    <?= csrf_field() ?>
                    <input type="hidden" name="member_id" value="<?= $user['id'] ?>">

                    <div class="mb-4">
                        <label class="form-label"><?= lang('Common.select_roles_for_user') ?></label>
                        <div class="row">
                            <?php if (!empty($roles)): ?>
                                <?php foreach ($roles as $role): ?>
                                    <div class="col-md-6 col-lg-4 mb-3">
                                        <div class="card border h-100">
                                            <div class="card-body">
                                                <div class="form-check">
                                                    <input class="form-check-input" type="checkbox" 
                                                           name="roles[]" value="<?= $role['id'] ?>" 
                                                           id="role_<?= $role['id'] ?>"
                                                           <?= in_array($role['id'], $userRoles ?? []) ? 'checked' : '' ?>>
                                                    <label class="form-check-label" for="role_<?= $role['id'] ?>">
                                                        <h6 class="fw-semibold mb-1"><?= esc($role['display_name']) ?></h6>
                                                        <p class="text-muted small mb-0"><?= esc($role['description']) ?></p>
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-warning">
                                        <i class="ti ti-alert-triangle"></i>
                                        <?= lang('Common.no_assignable_roles') ?>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= base_url('admin/role/user-roles') ?>" class="btn btn-outline-secondary"><?= lang('Common.cancel') ?></a>
                        <button type="submit" class="btn btn-primary btn-submit">
                            <i class="ti ti-check"></i> <?= lang('Common.save') ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>