<?php
/**
 * จัดการชุดทีม (เหย้า/เยือน/สาม × ผู้เล่น/ผู้รักษาประตู)
 * ตัวแปร: $academy, $team, $kit (24 คอลัมน์), $perms
 */
$kit  = $kit ?? [];
$base = rtrim(base_url(), '/');

// จานสี 16 สีมาตรฐาน
$KIT_COLORS = [
    ['#FFFFFF', lang('Common.color_white')],
    ['#1A1A1A', lang('Common.color_black')],
    ['#9AA1A9', lang('Common.color_grey')],
    ['#E1352C', lang('Common.color_red')],
    ['#7A1F2B', lang('Common.color_maroon')],
    ['#F26A1B', lang('Common.color_orange')],
    ['#F4C20D', lang('Common.color_yellow')],
    ['#1E9E54', lang('Common.color_green')],
    ['#38BDF8', lang('Common.color_sky')],
    ['#1E40AF', lang('Common.color_blue')],
    ['#0B1F4D', lang('Common.color_navy')],
    ['#6B2FB3', lang('Common.color_purple')],
    ['#EC4899', lang('Common.color_pink')],
    ['#0E9F9A', lang('Common.color_teal')],
    ['#9ACD32', lang('Common.color_lime')],
    ['#7A4B2B', lang('Common.color_brown')],
];

// คอนฟิกชุด
$kits = [
    'home'  => ['label' => lang('Common.jersey_home'),  'icon' => 'fa-house',  'color' => '#16478D'],
    'away'  => ['label' => lang('Common.jersey_away'),  'icon' => 'fa-plane',  'color' => '#e74c3c'],
    'third' => ['label' => lang('Common.jersey_third'), 'icon' => 'fa-star',   'color' => '#7A3FB0'],
];
$sets = [
    ''      => ['label' => lang('Common.jersey_player_set'), 'icon' => 'fa-person-running'],
    'goal_' => ['label' => lang('Common.jersey_gk_set'),     'icon' => 'fa-hand-paper'],
];
$parts = [
    'shirt'    => lang('Common.jersey_shirt'),
    'trousers' => lang('Common.jersey_trousers'),
    'socks'    => lang('Common.jersey_socks'),
];

// SVG ชุดฟุตบอลสำหรับพรีวิว
$jerseySvg = '
<svg class="jk-jersey" viewBox="0 0 130 178" xmlns="http://www.w3.org/2000/svg">
  <path class="jk-shirt" d="M65,14 C57,14 52,18 45,17 L18,28 L9,44 L27,56 L36,49 L36,98 L94,98 L94,49 L103,56 L121,44 L112,28 L85,17 C78,18 73,14 65,14 Z" stroke="rgba(0,0,0,.16)" stroke-width="1.2"/>
  <path class="jk-collar" d="M52,16 Q65,27 78,16 L74,13 Q65,21 56,13 Z" fill="rgba(0,0,0,.14)"/>
  <path class="jk-shorts" d="M37,101 L93,101 L90,141 L68,141 L65,114 L62,141 L40,141 Z" stroke="rgba(0,0,0,.16)" stroke-width="1.2"/>
  <rect class="jk-socks" x="44" y="145" width="13" height="27" rx="4" stroke="rgba(0,0,0,.16)" stroke-width="1.2"/>
  <rect class="jk-socks" x="72" y="145" width="13" height="27" rx="4" stroke="rgba(0,0,0,.16)" stroke-width="1.2"/>
</svg>';
?>
<?php $this->extend('template/layout') ?>

<?php $this->section('style'); ?>
<style>
    .jk-tabs .nav-link {
        border-radius: 50px;
        font-weight: 600;
        color: #64748b;
        padding: 8px 22px;
        display: flex;
        align-items: center;
        gap: 8px;
        border: 1.5px solid transparent;
    }
    .jk-tabs .nav-link.active {
        background: #16478D;
        color: #fff;
        box-shadow: 0 6px 16px rgba(22,71,141,.25);
    }
    .jk-set {
        border: 1px solid #e9eef5;
        border-radius: 18px;
        background: #fff;
        height: 100%;
        overflow: hidden;
    }
    .jk-set-head {
        padding: 14px 18px;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #eef2f7;
    }
    .jk-set-head .ico {
        width: 34px; height: 34px;
        border-radius: 10px;
        display: flex; align-items: center; justify-content: center;
        color: #fff;
    }
    .jk-set-body { padding: 18px; }
    .jk-preview-wrap {
        display: flex; justify-content: center;
        background: linear-gradient(180deg,#f8fbff,#eef3fb);
        border-radius: 14px;
        padding: 14px;
        margin-bottom: 16px;
    }
    .jk-jersey { width: 130px; height: auto; filter: drop-shadow(0 8px 12px rgba(0,0,0,.08)); }
    .jk-jersey .jk-shirt, .jk-jersey .jk-shorts, .jk-jersey .jk-socks {
        fill: #e5e7eb;
        transition: fill .25s ease;
    }

    .jk-label {
        font-size: .8rem;
        font-weight: 600;
        color: #64748b;
        margin-bottom: 4px;
        display: block;
    }
    .jk-color-field { margin-bottom: 12px; }
    .jk-dd-btn {
        width: 100%;
        display: flex;
        align-items: center;
        gap: 10px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        padding: 8px 12px;
        font-weight: 500;
        color: #1e293b;
        cursor: pointer;
        transition: border-color .2s;
    }
    .jk-dd-btn:hover, .jk-dd.show .jk-dd-btn { border-color: #16478D; }
    .jk-swatch {
        width: 22px; height: 22px;
        border-radius: 6px;
        border: 1.5px solid rgba(0,0,0,.12);
        flex: 0 0 auto;
        background: #fff;
    }
    .jk-swatch-clear {
        background:
            linear-gradient(45deg, transparent 45%, #e11 45%, #e11 55%, transparent 55%),
            #fff !important;
    }
    .jk-dd-menu {
        max-height: 280px; overflow-y: auto;
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 10px 30px rgba(0,0,0,.1);
        padding: 6px;
        min-width: 200px;
    }
    .jk-dd-menu .dropdown-item {
        display: flex; align-items: center; gap: 10px;
        border-radius: 8px;
        padding: 7px 10px;
        font-weight: 500;
    }
    .jk-dd-menu .dropdown-item:active { background: #16478D; color: #fff; }

    .jk-img-field { margin-top: 6px; }
    .jk-upload {
        border: 2px dashed #cdd7e5;
        border-radius: 12px;
        height: 120px;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer;
        background: #f8fafc;
        overflow: hidden;
        transition: border-color .2s, background .2s;
    }
    .jk-upload:hover { border-color: #16478D; background: #f1f6ff; }
    .jk-img-preview { max-height: 100%; max-width: 100%; object-fit: contain; }
    .jk-img-placeholder { text-align: center; color: #94a3b8; }
    .jk-img-placeholder i { font-size: 1.6rem; display: block; margin-bottom: 6px; }
    .jk-save-bar {
        position: sticky; bottom: 0;
        background: rgba(255,255,255,.92);
        backdrop-filter: blur(8px);
        border-top: 1px solid #eef2f7;
        padding: 14px 18px;
        margin: 0 -1px -1px;
        display: flex; justify-content: flex-end;
        z-index: 5;
    }
</style>
<?php $this->endSection(); ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0">
    <?= $this->include('Modules\\Academy\\Views\\subnav') ?>

    <div class="card shadow-sm border-0 rounded-4 mb-4">
        <div class="card-header bg-white border-0 py-4 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div>
                <h4 class="mb-1 fw-bold" style="color:#16478D;">
                    <i class="fas fa-tshirt me-2"></i><?= lang('Common.jersey_manager') ?>
                </h4>
                <p class="text-muted small mb-0">
                    <?= lang('Common.jersey_subtitle') ?>
                    <?php if (!empty($team['name'])): ?>
                        &mdash; <strong class="text-dark"><?= esc($team['name']) ?></strong>
                    <?php endif; ?>
                </p>
            </div>
            <a href="<?= base_url('academy/info/' . ($academy['id'] ?? '')) ?>" class="btn btn-light rounded-pill px-4 fw-bold text-muted border">
                <i class="fas fa-arrow-left me-2"></i><?= lang('Common.back') ?>
            </a>
        </div>

        <form id="jerseyForm" enctype="multipart/form-data"
              action="<?= base_url('academy/team-jersey/save/' . ($academy['id'] ?? '') . '/' . ($team['id'] ?? '')) ?>">
            <div class="card-body p-4">
                <!-- Tabs -->
                <ul class="nav jk-tabs gap-2 mb-4" role="tablist">
                    <?php $first = true; foreach ($kits as $kk => $kc): ?>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link <?= $first ? 'active' : '' ?>" data-bs-toggle="pill"
                                    data-bs-target="#tab-<?= $kk ?>" type="button" role="tab"
                                    style="<?= $first ? '' : '' ?>">
                                <i class="fas <?= $kc['icon'] ?>"></i><?= esc($kc['label']) ?>
                            </button>
                        </li>
                    <?php $first = false; endforeach; ?>
                </ul>

                <div class="tab-content">
                    <?php $first = true; foreach ($kits as $kk => $kc): ?>
                        <div class="tab-pane fade <?= $first ? 'show active' : '' ?>" id="tab-<?= $kk ?>" role="tabpanel">
                            <div class="row g-4">
                                <?php foreach ($sets as $sk => $sc): ?>
                                    <div class="col-lg-6">
                                        <div class="jk-set jk-card">
                                            <div class="jk-set-head">
                                                <span class="ico" style="background:<?= $kc['color'] ?>;">
                                                    <i class="fas <?= $sc['icon'] ?>"></i>
                                                </span>
                                                <?= esc($kc['label']) ?> · <?= esc($sc['label']) ?>
                                            </div>
                                            <div class="jk-set-body">
                                                <!-- Live preview -->
                                                <div class="jk-preview-wrap"><?= $jerseySvg ?></div>

                                                <!-- Color pickers -->
                                                <?php foreach ($parts as $pk => $pl):
                                                    $col = "{$kk}_{$sk}{$pk}";
                                                    $val = $kit[$col] ?? '';
                                                ?>
                                                    <div class="jk-color-field" data-part="<?= $pk ?>">
                                                        <label class="jk-label"><?= esc($pl) ?></label>
                                                        <div class="dropdown jk-dd">
                                                            <button class="jk-dd-btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                                <span class="jk-swatch jk-swatch-clear"></span>
                                                                <span class="jk-cname text-muted"><?= lang('Common.jersey_not_set') ?></span>
                                                                <i class="fas fa-chevron-down ms-auto small text-muted"></i>
                                                            </button>
                                                            <ul class="dropdown-menu jk-dd-menu"></ul>
                                                        </div>
                                                        <input type="hidden" name="<?= $col ?>" class="jk-color-input" value="<?= esc($val) ?>">
                                                    </div>
                                                <?php endforeach; ?>

                                                <!-- Set image -->
                                                <?php
                                                    $imgCol = "{$kk}_{$sk}set_img";
                                                    $imgVal = $kit[$imgCol] ?? '';
                                                    $imgSrc = $imgVal ? $base . '/' . ltrim($imgVal, '/') : '';
                                                ?>
                                                <div class="jk-img-field">
                                                    <label class="jk-label"><?= lang('Common.jersey_set_img') ?></label>
                                                    <div class="jk-upload">
                                                        <img class="jk-img-preview" src="<?= esc($imgSrc) ?>" style="<?= $imgVal ? '' : 'display:none' ?>">
                                                        <div class="jk-img-placeholder" style="<?= $imgVal ? 'display:none' : '' ?>">
                                                            <i class="fas fa-cloud-arrow-up"></i>
                                                            <span><?= lang('Common.jersey_upload_hint') ?></span>
                                                        </div>
                                                    </div>
                                                    <input type="file" accept="image/*" class="d-none jk-img-input">
                                                    <input type="hidden" name="<?= $imgCol ?>_data" class="jk-img-data" value="">
                                                    <input type="hidden" name="<?= $imgCol ?>_remove" value="0" class="jk-img-remove">
                                                    <div class="text-end">
                                                        <button type="button" class="btn btn-sm btn-link text-danger jk-img-del p-0 mt-1" style="<?= $imgVal ? '' : 'display:none' ?>">
                                                            <i class="fas fa-trash me-1"></i><?= lang('Common.jersey_remove_img') ?>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php $first = false; endforeach; ?>
                </div>
            </div>

            <div class="jk-save-bar">
                <button type="button" id="btnSaveJersey" class="btn btn-primary rounded-pill px-5 fw-bold" style="background:#16478D;border:none;">
                    <i class="fas fa-save me-2"></i><?= lang('Common.jersey_save') ?>
                </button>
            </div>
        </form>
    </div>
</div>
<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<script>
    const KIT_COLORS = <?= json_encode(array_map(fn($c) => ['h' => $c[0], 'n' => $c[1]], $KIT_COLORS), JSON_UNESCAPED_UNICODE) ?>;
    const JK_SAVE_URL = '<?= base_url('academy/team-jersey/save/' . ($academy['id'] ?? '') . '/' . ($team['id'] ?? '')) ?>';
    const JK_LANG = {
        notSet: '<?= lang('Common.jersey_not_set') ?>',
        saving: '<?= lang('Common.saving') ?? 'Saving...' ?>',
        netErr: '<?= lang('Common.connection_error_short') ?? 'Connection error' ?>'
    };

    document.addEventListener('DOMContentLoaded', function () {
        // ---- build color menus ----
        const menuHtml = (function () {
            let h = `<li><a class="dropdown-item jk-opt" href="#" data-hex=""><span class="jk-swatch jk-swatch-clear"></span>${JK_LANG.notSet}</a></li>`;
            KIT_COLORS.forEach(c => {
                h += `<li><a class="dropdown-item jk-opt" href="#" data-hex="${c.h}"><span class="jk-swatch" style="background:${c.h}"></span>${c.n}</a></li>`;
            });
            return h;
        })();
        document.querySelectorAll('.jk-dd-menu').forEach(m => m.innerHTML = menuHtml);

        // ---- helpers ----
        function applyField(field) {
            const hex = field.querySelector('.jk-color-input').value;
            const sw  = field.querySelector('.jk-dd-btn .jk-swatch');
            const nm  = field.querySelector('.jk-cname');
            if (hex) {
                const c = KIT_COLORS.find(x => x.h.toUpperCase() === hex.toUpperCase());
                sw.style.background = hex;
                sw.classList.remove('jk-swatch-clear');
                nm.textContent = c ? c.n : hex;
                nm.classList.remove('text-muted');
            } else {
                sw.style.background = '';
                sw.classList.add('jk-swatch-clear');
                nm.textContent = JK_LANG.notSet;
                nm.classList.add('text-muted');
            }
        }

        function recolorCard(card) {
            if (!card) return;
            const svg = card.querySelector('.jk-jersey');
            if (!svg) return;
            const pick = part => {
                const f = card.querySelector(`.jk-color-field[data-part="${part}"] .jk-color-input`);
                return (f && f.value) ? f.value : '#e5e7eb';
            };
            // ใช้ inline style ให้ชนะ CSS rule (fill ใน CSS จะ override presentation attribute)
            svg.querySelectorAll('.jk-shirt').forEach(el => el.style.fill = pick('shirt'));
            svg.querySelectorAll('.jk-shorts').forEach(el => el.style.fill = pick('trousers'));
            svg.querySelectorAll('.jk-socks').forEach(el => el.style.fill = pick('socks'));
        }

        // ---- color option click ----
        document.querySelectorAll('.jk-dd-menu').forEach(menu => {
            menu.addEventListener('click', e => {
                const opt = e.target.closest('.jk-opt');
                if (!opt) return;
                e.preventDefault();
                const field = opt.closest('.jk-color-field');
                field.querySelector('.jk-color-input').value = opt.dataset.hex || '';
                applyField(field);
                recolorCard(field.closest('.jk-set'));
            });
        });

        // ---- init existing values ----
        document.querySelectorAll('.jk-color-field').forEach(applyField);
        document.querySelectorAll('.jk-set').forEach(recolorCard);

        // ---- click upload box -> open file dialog ----
        document.querySelectorAll('.jk-upload').forEach(up => {
            up.addEventListener('click', function () {
                const inp = up.closest('.jk-img-field').querySelector('.jk-img-input');
                if (inp) inp.click();
            });
        });

        // ---- image upload preview ----
        document.querySelectorAll('.jk-img-input').forEach(inp => {
            inp.addEventListener('change', function (e) {
                const card = inp.closest('.jk-img-field');
                const file = e.target.files[0];
                if (!file) return;
                const reader = new FileReader();
                reader.onload = ev => {
                    const img = card.querySelector('.jk-img-preview');
                    img.src = ev.target.result;
                    img.style.display = '';
                    card.querySelector('.jk-img-placeholder').style.display = 'none';
                    card.querySelector('.jk-img-del').style.display = '';
                    card.querySelector('.jk-img-remove').value = '0';
                    card.querySelector('.jk-img-data').value = ev.target.result; // base64 ส่งให้ server
                };
                reader.readAsDataURL(file);
            });
        });
        document.querySelectorAll('.jk-img-del').forEach(btn => {
            btn.addEventListener('click', function () {
                const card = btn.closest('.jk-img-field');
                card.querySelector('.jk-img-input').value = '';
                card.querySelector('.jk-img-data').value = '';
                const img = card.querySelector('.jk-img-preview');
                img.src = '';
                img.style.display = 'none';
                card.querySelector('.jk-img-placeholder').style.display = '';
                btn.style.display = 'none';
                card.querySelector('.jk-img-remove').value = '1';
            });
        });

        // ---- save ----
        document.getElementById('btnSaveJersey').addEventListener('click', function () {
            const btn = this;
            const form = document.getElementById('jerseyForm');
            const fd = new FormData(form);
            btn.disabled = true;
            const original = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + JK_LANG.saving;

            fetch(JK_SAVE_URL, {
                method: 'POST',
                body: fd,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(r => r.json())
            .then(res => {
                btn.disabled = false;
                btn.innerHTML = original;
                if (res.status === 'success') {
                    Swal.fire({ icon: 'success', title: res.message, timer: 1600, showConfirmButton: false });
                } else {
                    Swal.fire({ icon: 'error', title: res.message || JK_LANG.netErr, confirmButtonColor: '#16478D' });
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = original;
                Swal.fire({ icon: 'error', title: JK_LANG.netErr, confirmButtonColor: '#16478D' });
            });
        });
    });
</script>
<?php $this->endSection(); ?>
