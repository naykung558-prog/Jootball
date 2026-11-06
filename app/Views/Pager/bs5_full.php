<?php $pager->setSurroundCount(2) ?>

<nav aria-label="Page navigation" class="d-flex justify-content-center">
    <ul class="pagination pagination-md m-0 rounded-pill shadow-sm bg-white p-1">
        <?php if ($pager->hasPrevious()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getFirst() ?>" aria-label="<?= lang('Pager.first') ?>" class="page-link border-0 rounded-circle fw-bold text-dark mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="ti ti-chevrons-left"></i>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getPrevious() ?>" aria-label="<?= lang('Pager.previous') ?>" class="page-link border-0 rounded-circle fw-bold text-dark mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="ti ti-chevron-left"></i>
                </a>
            </li>
        <?php endif ?>

        <?php foreach ($pager->links() as $link) : ?>
            <li class="page-item <?= $link['active'] ? 'active' : '' ?>">
                <a href="<?= $link['uri'] ?>" class="page-link border-0 rounded-circle fw-bold mx-1 d-flex align-items-center justify-content-center <?= $link['active'] ? 'bg-primary text-white shadow-sm' : 'text-dark' ?>" style="width: 38px; height: 38px;">
                    <?= $link['title'] ?>
                </a>
            </li>
        <?php endforeach ?>

        <?php if ($pager->hasNext()) : ?>
            <li class="page-item">
                <a href="<?= $pager->getNext() ?>" aria-label="<?= lang('Pager.next') ?>" class="page-link border-0 rounded-circle fw-bold text-dark mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="ti ti-chevron-right"></i>
                </a>
            </li>
            <li class="page-item">
                <a href="<?= $pager->getLast() ?>" aria-label="<?= lang('Pager.last') ?>" class="page-link border-0 rounded-circle fw-bold text-dark mx-1 d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
                    <i class="ti ti-chevrons-right"></i>
                </a>
            </li>
        <?php endif ?>
    </ul>
</nav>
