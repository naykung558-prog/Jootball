<?php $this->extend('template/layout') ?>

<?php $this->section('content'); ?>
<div class="container-fluid p-0 player-chart-page">
    <?php echo view("Modules\Academy\Views\subnav.php"); ?>

    <!-- ===== Hero Header ===== -->
    <div class="pc-hero">
        <div class="pc-hero__left">
            <div class="pc-hero__crumb">
                <i class="fas fa-images me-2"></i><?= lang('Common.player_chart_title') ?>
            </div>
            <h2 class="pc-hero__title">
                <?= esc($team['name'] ?? '') ?>
                <?php if (!empty($team['name_en'])): ?>
                    <small class="pc-hero__title-en"><?= esc($team['name_en']) ?></small>
                <?php endif; ?>
            </h2>
            <p class="pc-hero__sub"><?= lang('Common.select_player_for_export') ?></p>
        </div>
        <div class="pc-hero__right">
            <div class="pc-hero__stat">
                <div class="pc-hero__stat-value" id="heroPlayerCount"><?= count($players ?? []) ?></div>
                <div class="pc-hero__stat-label"><?= lang('Common.players') ?></div>
            </div>
            <div class="pc-hero__stat">
                <div class="pc-hero__stat-value" id="heroCoachCount"><?= count($coaches ?? []) ?></div>
                <div class="pc-hero__stat-label"><?= lang('Common.coach_staff') ?></div>
            </div>
        </div>
    </div>


    <div class="card shadow-sm border-0 rounded-4 mb-4 pc-section">
        <div class="card-header pc-section__header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
            <div class="d-flex align-items-center gap-2">
                <span class="pc-section__bar" style="background:#16478C;"></span>
                <div>
                    <h5 class="mb-0 fw-bold" style="color:#16478C;">
                        <i class="ti ti-users me-2"></i><?= lang('Common.players') ?>
                    </h5>
                    <small class="text-muted"><?= lang('Common.select_player_for_export') ?></small>
                </div>
            </div>
            <div class="d-flex flex-column flex-md-row gap-2 align-items-md-center">
                <div class="d-flex gap-2 align-items-center">
                    <!-- ซ่อนตอนอยู่มุมมองสองฝั่ง เพราะกล่องเตือนฝั่งขวามีปุ่มส่งรายชื่ออยู่แล้ว -->
                    <button type="button" class="btn btn-outline-success rounded-pill px-4" id="pcTopSubmitBtn" onclick="openSubmitModal()"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="<?= lang('Common.submit_btn_tooltip') ?>">
                        <i class="fa fa-check-circle me-2"></i><?= lang('Common.confirm_submit_list') ?>
                    </button>
                    <button type="button" class="btn btn-primary rounded-pill px-4" onclick="openExportModal()"
                            data-bs-toggle="tooltip" data-bs-placement="top"
                            title="<?= lang('Common.export_btn_tooltip') ?>">
                        <i class="fa fa-cloud-download-alt me-2"></i><?= lang('Common.export_pdf') ?>
                    </button>
                </div>
            </div>
        </div>
        <div class="px-4 pb-2">
            <div class="alert alert-info py-2 px-3 mb-0 small d-flex align-items-start gap-2 rounded-3" style="background:#e7f1ff; border:1px solid #b8d4ff; color:#0d4a8c;">
                <i class="fa fa-info-circle mt-1"></i>
                <div>
                    <strong><?= lang('Common.export_hint_title') ?>:</strong>
                    <?= lang('Common.export_hint_msg') ?>
                </div>
            </div>
        </div>

        <div class="card-body p-0">
            <style>
                /* ===== Player Chart Page Design ===== */
                .player-chart-page {
                    --pc-primary: #16478C;
                    --pc-bg: #F6F9FC;
                    --pc-border: #e5e7eb;
                    --pc-muted: #6b7280;
                }
                /* Hero */
                .pc-hero {
                    background: linear-gradient(135deg, #16478C 0%, #1d5cb1 100%);
                    color: #fff;
                    padding: 22px 26px;
                    border-radius: 16px;
                    margin: 1rem 0 1rem;
                    box-shadow: 0 8px 24px rgba(22, 71, 141, 0.18);
                    display: flex; align-items: center; justify-content: space-between;
                    flex-wrap: wrap; gap: 1rem;
                }
                .pc-hero__crumb {
                    display: inline-flex; align-items: center;
                    background: rgba(255,255,255,0.16);
                    padding: 5px 12px; border-radius: 999px;
                    font-size: .8rem; font-weight: 500;
                    margin-bottom: .5rem;
                }
                .pc-hero__title { font-weight: 700; font-size: 1.4rem; margin: 0; line-height: 1.3; color: #fff; }
                .pc-hero__title-en { display: block; font-size: .85rem; opacity: .75; font-weight: 400; margin-top: 2px; }
                .pc-hero__sub { margin: .35rem 0 0; opacity: .85; font-size: .85rem; }
                .pc-hero__right { display: flex; gap: 16px; }
                .pc-hero__stat {
                    background: rgba(255,255,255,0.14);
                    border: 1px solid rgba(255,255,255,0.22);
                    border-radius: 12px;
                    padding: 12px 18px; min-width: 90px;
                    text-align: center;
                    backdrop-filter: blur(6px);
                }
                .pc-hero__stat-value { font-size: 1.5rem; font-weight: 700; line-height: 1; color: #fff; }
                .pc-hero__stat-label { font-size: .72rem; opacity: .85; margin-top: 4px; text-transform: uppercase; letter-spacing: .04em; }

                /* Steps */
                .pc-steps {
                    display: flex; align-items: center; gap: 6px;
                    background: #fff;
                    border: 1px solid #e5e7eb;
                    border-radius: 14px;
                    padding: 12px 16px;
                    margin-bottom: 1rem;
                    overflow-x: auto;
                }
                .pc-step { display: flex; align-items: center; gap: 10px; padding: 6px 10px; border-radius: 10px; flex-shrink: 0; }
                .pc-step__num {
                    width: 32px; height: 32px; border-radius: 50%;
                    background: #f1f5f9; color: #6b7280;
                    display: inline-flex; align-items: center; justify-content: center;
                    font-weight: 700; font-size: .85rem;
                    flex-shrink: 0;
                }
                .pc-step__title { font-weight: 600; font-size: .85rem; color: #1f2937; }
                .pc-step__hint  { font-size: .72rem; color: #6b7280; }
                .pc-step--active .pc-step__num { background: #16478C; color: #fff; box-shadow: 0 0 0 4px rgba(22,71,140,.15); }
                .pc-step--active .pc-step__title { color: #16478C; }
                .pc-step--done .pc-step__num { background: #16a34a; color: transparent; position: relative; }
                .pc-step--done .pc-step__num::after {
                    content: '\f00c';
                    font-family: 'Font Awesome 6 Free', 'Font Awesome 5 Free', FontAwesome;
                    font-weight: 900;
                    color: #fff;
                    font-size: .85rem;
                    position: absolute; inset: 0;
                    display: flex; align-items: center; justify-content: center;
                }
                .pc-step--done .pc-step__title { color: #16a34a; }
                .pc-step__divider {
                    flex: 1; min-width: 12px; height: 2px;
                    background: linear-gradient(90deg, #e5e7eb, #cbd5e1);
                    border-radius: 1px;
                }

                /* Section header bar */
                .pc-section { overflow: hidden; }
                .pc-section__header { position: relative; }
                .pc-section__bar { display: inline-block; width: 4px; height: 36px; border-radius: 2px; }

                /* ซ่อน option ที่ถูก disabled */
                select option:disabled,
                select option[hidden] {
                    display: none !important;
                }

                .player-card {
                    border: 2px solid #e0e0e0;
                    border-radius: 12px;
                    padding: 16px;
                    transition: all 0.3s ease;
                    position: relative;
                    text-align: center;
                }

                .player-card:hover {
                    border-color: #16478D;
                    box-shadow: 0 8px 16px rgba(22, 71, 141, 0.15);
                }

                .player-card img {
                    width: 150px;
                    height: 150px;
                    object-fit: cover;
                    border-radius: 50%;
                    margin: 0 auto 12px auto;
                    display: block;
                    border: 4px solid #fff;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                }

                .player-card-checkbox {
                    position: absolute;
                    top: 12px;
                    right: 12px;
                    width: 20px;
                    height: 20px;
                    cursor: pointer;
                }

                /* ===== สองฝั่ง: ยังไม่เลือก / เลือกแล้ว ===== */
                .pc-pane {
                    border: 1px solid #e2e8f0;
                    border-radius: 14px;
                    overflow: hidden;
                    background: #fff;
                    display: flex;
                    flex-direction: column;
                }
                .pc-pane__head {
                    background: #64748b;
                    color: #fff;
                    padding: .65rem 1rem;
                    font-weight: 600;
                    display: flex;
                    align-items: center;
                    justify-content: space-between;
                    gap: .5rem;
                }
                .pc-pane--selected .pc-pane__head { background: #16478C; }

                .pc-pane__search { position: relative; padding: .6rem .75rem 0; }
                .pc-pane__search i {
                    position: absolute;
                    left: 1.4rem;
                    top: 1.15rem;
                    color: #94a3b8;
                    font-size: .85rem;
                }
                .pc-pane__search input {
                    padding-left: 2.1rem;
                    border-radius: 999px;
                    font-size: .875rem;
                }

                .pc-pane__body {
                    padding: .6rem .75rem .75rem;
                    /* สูงเท่ากันทั้งสองฝั่ง ยืดตามจอแต่ไม่เตี้ยกว่า 680px */
                    height: calc(100vh - 220px);
                    min-height: 680px;

                    /* scroll ไม่ใช่ auto — จองที่ให้แถบเลื่อนตลอด ผู้ใช้จะได้เห็นว่าเลื่อนดูต่อได้
                       (macOS ตั้งต้นซ่อนแถบเลื่อนจนกว่าจะเลื่อน การกำหนดสไตล์เองบังคับให้แสดงถาวร) */
                    overflow-y: scroll;
                    scrollbar-width: thin;                 /* Firefox */
                    scrollbar-color: #94a3b8 #eef2f7;

                    /* การ์ดเรียงซ้ายไปขวา เต็มแถวแล้วขึ้นแถวใหม่เอง */
                    display: grid;
                    grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
                    gap: .6rem;
                    align-content: start;
                }
                .pc-pane__body::-webkit-scrollbar { width: 10px; }
                .pc-pane__body::-webkit-scrollbar-track {
                    background: #eef2f7;
                    border-radius: 8px;
                }
                .pc-pane__body::-webkit-scrollbar-thumb {
                    background: #94a3b8;
                    border-radius: 8px;
                    border: 2px solid #eef2f7;
                }
                .pc-pane__body::-webkit-scrollbar-thumb:hover { background: #64748b; }

                .pc-pane__warning {
                    display: flex;
                    align-items: center;
                    gap: .75rem;
                    margin: .6rem .75rem 0;
                    padding: .6rem .75rem;
                    border-radius: 10px;
                    background: #fff8e1;
                    border: 1px solid #ffe082;
                    color: #8d6e00;
                }
                .pc-pane__warning > i { font-size: 1.35rem; }

                /* ป้ายเขียว: ส่งรายชื่อแล้ว */
                .pc-pane__ok {
                    display: flex;
                    align-items: center;
                    gap: .75rem;
                    margin: .6rem .75rem 0;
                    padding: .6rem .75rem;
                    border-radius: 10px;
                    background: #e8f5e9;
                    border: 1px solid #a5d6a7;
                    color: #1b5e20;
                }
                .pc-pane__ok > i { font-size: 1.35rem; }
                #pcGoSubmit {
                    font-size: 1rem;
                    border-radius: 999px;
                    box-shadow: 0 3px 10px rgba(255, 193, 7, .35);
                }
                #pcGoSubmit:hover { transform: translateY(-1px); }

                @media (max-width: 575.98px) {
                    /* จอแคบ ปุ่มลงมาอยู่บรรทัดล่างและกินเต็มความกว้าง จะได้กดง่าย */
                    .pc-pane__warning { flex-wrap: wrap; }
                    #pcGoSubmit { width: 100%; }
                }

                /* การ์ดหนึ่งใบต่อหนึ่งคน */
                .pc-item {
                    position: relative;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                    gap: .3rem;
                    padding: .85rem .5rem .6rem;
                    /* grid item ตั้งต้นเป็น min-width:auto ทำให้คำยาวดันการ์ดจนล้น */
                    min-width: 0;
                    /* จองความสูงไว้ตั้งแต่แรก — ไม่งั้นแถวถูกคำนวณก่อนรูปโหลดเสร็จ
                       พอรูปโหลดแล้วเนื้อหาโตขึ้น ชื่อเล่น/ตำแหน่งจะถูก overflow ตัดหายไป */
                    min-height: 172px;
                    overflow: hidden;
                    border: 1px solid #eef2f7;
                    border-radius: 12px;
                    cursor: pointer;
                    background: #fff;
                    transition: background .15s, border-color .15s, box-shadow .15s;
                }
                .pc-item:hover {
                    background: #f8fafc;
                    border-color: #cbd5e1;
                    box-shadow: 0 2px 8px rgba(15,23,42,.06);
                }
                /* ใบที่ถูกเลือกอยู่ (ฝั่งขวา) ให้เห็นชัดว่าติ๊กแล้ว */
                .pc-item:has(input:checked) {
                    border-color: #16478C;
                    background: #f2f6fc;
                }

                /* flex-shrink:0 + ขนาดตายตัว = จองที่ไว้ก่อนรูปโหลด ไม่เกิด reflow ทีหลัง */
                .pc-item img {
                    width: 56px !important;
                    height: 56px !important;
                    min-height: 56px;
                    flex-shrink: 0;
                    border-radius: 50%;
                    object-fit: cover;
                    background: #eef2f7;
                }
                /* เบอร์เสื้อมุมขวาบนของการ์ด */
                .pc-item__no {
                    position: absolute;
                    top: .4rem;
                    right: .45rem;
                    min-width: 22px;
                    padding: 0 .3rem;
                    border-radius: 6px;
                    background: #16478C;
                    color: #fff;
                    font-weight: 700;
                    font-size: .72rem;
                    line-height: 1.5;
                }
                .pc-item__no .text-muted { color: #cbd5e1 !important; }
                /* ป้ายเบอร์กดแก้ได้ ให้รู้ว่าคลิกได้ */
                .pc-item__no--edit { cursor: pointer; transition: background .15s, transform .1s; }
                .pc-item__no--edit:hover { background: #0d2f63; transform: scale(1.08); }
                .pc-item__no--edit:focus-visible { outline: 2px solid #ffd54f; outline-offset: 1px; }

                /* ชื่อ ชื่อเล่น ตำแหน่ง แยกกันคนละบรรทัด */
                .pc-item__name,
                .pc-item__nick,
                .pc-item__pos {
                    display: block;
                    width: 100%;
                    /* กันชื่อยาวคำเดียวดันทะลุขอบการ์ด */
                    overflow-wrap: anywhere;
                    word-break: break-word;
                    /* อย่าให้ flex บีบสามบรรทัดนี้จนหาย เมื่อการ์ดพื้นที่ไม่พอ */
                    flex-shrink: 0;
                }
                /* ล็อกความสูงของแต่ละบรรทัดไว้ ให้การ์ดทุกใบสองฝั่งสูงเท่ากัน
                   ไม่ว่าชื่อจะยาวสั้นต่างกัน หรือมี/ไม่มีชื่อเล่นกับตำแหน่ง */
                .pc-item__name {
                    font-weight: 600;
                    font-size: .8rem;
                    line-height: 1.25;
                    min-height: calc(.8rem * 1.25 * 2);   /* 2 บรรทัด */
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                .pc-item__nick {
                    font-size: .7rem;
                    color: #64748b;
                    line-height: 1.3;
                    min-height: calc(.7rem * 1.3);        /* 1 บรรทัด */
                    white-space: nowrap;
                    overflow: hidden;
                    text-overflow: ellipsis;
                }
                .pc-item__pos {
                    margin-top: .1rem;
                    min-height: 1.15rem;
                }
                .pc-item__pos .badge {
                    font-size: .62rem;
                    white-space: normal;
                    overflow-wrap: anywhere;
                }

                .pc-item input[type="checkbox"] {
                    position: absolute;
                    top: .4rem;
                    left: .45rem;
                    width: 17px;
                    height: 17px;
                    cursor: pointer;
                    margin: 0;
                }

                .pc-pane__empty {
                    /* กินเต็มความกว้างของ grid ไม่ให้ไปแทรกเป็นการ์ดใบหนึ่ง */
                    grid-column: 1 / -1;
                    text-align: center;
                    color: #94a3b8;
                    font-size: .875rem;
                    padding: 2.5rem 1rem;
                }

                /* ===== ฝั่งซ้าย: กลับมาเป็นรายการแถวเดียว (ฝั่งขวายังเป็นการ์ด) ===== */
                #availableList {
                    display: block;            /* ทับ grid ของ .pc-pane__body */
                }
                #availableList .pc-item {
                    /* วางเป็นแถว: [ติ๊ก] [เบอร์] [รูป] [ชื่อ / ชื่อเล่น·ตำแหน่ง] */
                    display: grid;
                    grid-template-columns: auto auto 40px minmax(0, 1fr) auto;
                    grid-template-areas:
                        "cb shirt img name name"
                        "cb shirt img nick pos";
                    align-items: center;
                    column-gap: .6rem;
                    row-gap: 0;
                    text-align: left;
                    min-height: 0;
                    padding: .45rem .6rem;
                    margin-bottom: .4rem;
                }
                /* คืนตำแหน่งจาก absolute (ที่ใช้ตอนเป็นการ์ด) มาอยู่ในผังแถว */
                #availableList .pc-item input[type="checkbox"] {
                    grid-area: cb;
                    position: static;
                }
                #availableList .pc-item__no {
                    grid-area: shirt;
                    position: static;
                    font-size: .75rem;
                }
                #availableList .pc-item img {
                    grid-area: img;
                    width: 40px !important;
                    height: 40px !important;
                    min-height: 40px;
                }
                #availableList .pc-item__name {
                    grid-area: name;
                    min-height: 0;
                    -webkit-line-clamp: 1;
                    font-size: .82rem;
                }
                #availableList .pc-item__nick {
                    grid-area: nick;
                    min-height: 0;
                }
                #availableList .pc-item__pos {
                    grid-area: pos;
                    width: auto;
                    min-height: 0;
                    margin-top: 0;
                    justify-self: end;
                }

                .player-card h5 {
                    font-weight: 600;
                    margin-bottom: 8px;
                    color: #1e293b;
                    font-size: 0.95rem;
                }

                .player-position-badge {
                    display: inline-block;
                    background-color: #16478C;
                    color: white;
                    padding: 6px 14px;
                    border-radius: 20px;
                    font-size: 0.8rem;
                    font-weight: 600;
                    margin-bottom: 8px;
                }

                .player-shirt-number {
                    display: block;
                    font-size: 1.1rem;
                    font-weight: 700;
                    color: #16478C;
                    margin-top: 8px;
                }

                .player-card .text-muted {
                    font-size: 0.75rem;
                    margin-top: 4px;
                }

                /* Source-tournament blacklist: mark blocked players */
                .player-card--ineligible {
                    opacity: 0.55;
                }
                .player-card--ineligible::after {
                    content: attr(data-ineligible-label);
                    position: absolute;
                    top: 8px;
                    left: 8px;
                    background: #e53935;
                    color: #fff;
                    font-size: 0.7rem;
                    font-weight: 600;
                    padding: 2px 8px;
                    border-radius: 999px;
                    z-index: 2;
                }
                tr.player-row--ineligible td {
                    background: #fdecea !important;
                }

                /* ===== Source eligibility banner (redesigned) ===== */
                .src-elig {
                    display: flex;
                    gap: 14px;
                    align-items: flex-start;
                    background: linear-gradient(135deg, #fff8ec 0%, #fdf3e0 100%);
                    border: 1px solid #f0d9a8;
                    border-left: 4px solid #e0932f;
                    border-radius: 12px;
                    padding: 14px 16px;
                    box-shadow: 0 2px 8px rgba(224, 147, 47, 0.08);
                }
                .src-elig__icon {
                    flex-shrink: 0;
                    width: 38px; height: 38px;
                    border-radius: 10px;
                    background: #e0932f;
                    color: #fff;
                    display: flex; align-items: center; justify-content: center;
                    font-size: 1.05rem;
                }
                .src-elig__body { flex: 1; min-width: 0; }
                .src-elig__head {
                    display: flex; align-items: center; justify-content: space-between;
                    flex-wrap: wrap; gap: 8px;
                    margin-bottom: 8px;
                }
                .src-elig__title {
                    font-weight: 700; font-size: .92rem; color: #7a4e12; line-height: 1.35;
                }
                .src-elig__count {
                    display: inline-flex; align-items: center;
                    font-size: .8rem; font-weight: 700;
                    padding: 4px 12px; border-radius: 999px;
                    white-space: nowrap;
                }
                .src-elig__count.is-ok { background: #dcfce7; color: #15803d; border: 1px solid #bbf7d0; }
                .src-elig__count.is-none { background: #fee2e2; color: #b91c1c; border: 1px solid #fecaca; }
                .src-elig__sources {
                    display: flex; flex-wrap: wrap; align-items: center; gap: 6px;
                }
                .src-elig__sources-label {
                    font-size: .78rem; font-weight: 600; color: #9a7434; margin-right: 2px;
                }
                .src-elig__chips { display: inline-flex; flex-wrap: wrap; gap: 6px; align-items: center; }
                .src-chip {
                    display: inline-block;
                    background: #fff;
                    border: 1px solid #ecd9b3;
                    color: #6b4e1e;
                    font-size: .74rem; font-weight: 500;
                    padding: 3px 10px; border-radius: 999px;
                    line-height: 1.4;
                }
                .src-chip-toggle {
                    background: none; border: none;
                    color: #b7791f; font-size: .74rem; font-weight: 700;
                    cursor: pointer; padding: 3px 6px; border-radius: 6px;
                    text-decoration: underline;
                }
                .src-chip-toggle:hover { background: rgba(183,121,31,.1); }

                /* ===== Source check (submit modal): ineligible players ===== */
                .src-check {
                    background: #fff5f5;
                    border: 1px solid #f5c6cb;
                    border-left: 4px solid #dc3545;
                    border-radius: 12px;
                    padding: 14px 16px;
                }
                .src-check__head {
                    display: flex; align-items: center; gap: 8px;
                    margin-bottom: 10px;
                }
                .src-check__icon {
                    flex-shrink: 0;
                    width: 32px; height: 32px; border-radius: 9px;
                    background: #dc3545; color: #fff;
                    display: flex; align-items: center; justify-content: center;
                    font-size: .95rem;
                }
                .src-check__title { font-weight: 700; font-size: .9rem; color: #a71d2a; line-height: 1.35; }
                .src-check__count {
                    margin-left: auto;
                    background: #f8d7da; color: #a71d2a; border: 1px solid #f1b0b7;
                    font-size: .78rem; font-weight: 700;
                    padding: 3px 11px; border-radius: 999px; white-space: nowrap;
                }
                .src-check__block { margin-top: 8px; }
                .src-check__label {
                    font-size: .76rem; font-weight: 600; color: #8a2c34;
                    display: block; margin-bottom: 5px;
                }
                .src-check__chips { display: flex; flex-wrap: wrap; gap: 6px; }
                .src-check__chips .src-chip { border-color: #f1c9cd; color: #7a2830; }
                .name-chip {
                    display: inline-block;
                    background: #fdeaec; border: 1px solid #f3c2c7; color: #a3262f;
                    font-size: .76rem; font-weight: 600;
                    padding: 3px 10px; border-radius: 999px; line-height: 1.4;
                }
            </style>

            <div class="d-flex justify-content-between align-items-center px-4 py-3 border-bottom flex-wrap gap-3">
                <div class="d-flex gap-3 align-items-center flex-grow-1">
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-primary rounded-3" id="viewTableBtn" onclick="switchViewMode('table')" title="<?= lang('Common.table_view') ?>" style="width: 44px; height: 44px; padding: 0; background-color: #16478C; border-color: #16478C;">
                            <i class="ti ti-list-details fs-5"></i>
                        </button>
                        <button type="button" class="btn btn-light rounded-3" id="viewCardBtn" onclick="switchViewMode('card')" title="<?= lang('Common.card_view') ?>" style="width: 44px; height: 44px; padding: 0; border: 2px solid #16478C;">
                            <i class="ti ti-layout-grid fs-5" style="color: #16478C;"></i>
                        </button>
                    </div>
                    <!-- ซ่อนตอนอยู่มุมมองสองฝั่ง เพราะแต่ละฝั่งมีช่องค้นหาของตัวเองแล้ว -->
                    <div class="position-relative flex-grow-1" id="pcTopSearchWrap" style="max-width: 300px;">
                        <input type="text" class="form-control ps-5 rounded-pill" id="customSearch" placeholder="<?= lang('Common.search_placeholder') ?>" style="height: 44px; background-color: #f8f9fa; border: 1px solid #e0e0e0;">
                        <i class="ti ti-search position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    </div>
                    <div style="max-width: 500px;">
                        <select id="tournament_filter" class="form-select rounded-pill border shadow-sm" style="height: 44px; font-size: 1.1rem; font-weight: 500;">
                            <option value=""><?= lang('Common.view') ?> - <?= lang('Common.tournament_label') ?></option>
                            <?php if (!empty($joined_tournaments)): ?>
                                <?php foreach ($joined_tournaments as $tournament): ?>
                                    <?php if ($tournament['team_id'] == ($team['id'] ?? '')): ?>
                                        <?php
                                        // "จบแล้ว" = แอดมินปิด is_active แล้ว และเลยวันจบมาแล้ว
                                        // (end_date คือวันปิดรับสมัคร วันแข่งจริงอยู่ที่ tournament_end_date)
                                        $pickDate = static function ($v) {
                                            return (!empty($v) && strpos((string) $v, '0000') !== 0) ? (string) $v : '';
                                        };
                                        $effEnd    = $pickDate($tournament['tournament_end_date'] ?? null)
                                                  ?: $pickDate($tournament['end_date'] ?? null);
                                        $tourEnded = (int) ($tournament['is_active'] ?? 0) !== 1
                                                  && $effEnd !== '' && $effEnd < date('Y-m-d');
                                        ?>
                                        <option value="<?= $tournament['tour_id'] ?>" data-team-id="<?= $tournament['team_id'] ?? '' ?>" <?= $tourEnded ? 'disabled' : '' ?>>
                                            <?= esc($tournament['name']) ?> (<?= esc($tournament['generation'] ?? '-') ?>)<?= $tourEnded ? ' [' . lang('Common.ended') . ']' : '' ?>
                                        </option>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="selectAllBtn" onclick="selectAllCheckboxes()">
                    <i class="ti ti-check me-1"></i> <?= lang('Common.select_all') ?>
                </button>
            </div>

            <!-- Source-tournament blacklist banner -->
            <!-- ทัวร์ที่จบไปแล้ว: ดูได้อย่างเดียว ติ๊กเลือก/ส่งรายชื่อไม่ได้ -->
            <div id="pcEndedNotice" class="alert alert-secondary d-flex align-items-center gap-2 d-none mx-4 mt-3 mb-0">
                <i class="ti ti-lock fs-5"></i>
                <span><?= lang('Common.tournament_ended_cannot_submit') ?></span>
            </div>

            <div id="sourceEligibilityBanner" class="d-none mx-4 mt-3 mb-4">
                <span id="sourceEligibilityBannerText"></span>
            </div>

            <!-- Select-tournament prompt (shown until a tournament is picked) -->
            <div id="selectTournamentPrompt" class="text-center py-5 px-4">
                <div class="mb-3">
                    <i class="fa fa-trophy fa-3x" style="color:#cbd5e1;"></i>
                </div>
                <h5 class="fw-bold mb-1" style="color:#16478C;"><?= lang('Common.please_select_tournament') ?></h5>
                <p class="text-muted mb-0"><?= lang('Common.select_tournament_from_list') ?></p>
            </div>

            <!-- Table View — สองฝั่ง: ซ้าย = ยังไม่เลือก / ขวา = เลือกแล้ว
                 ตาราง #table ยังอยู่แต่ซ่อนไว้ ใช้เป็นแหล่งข้อมูล (DataTable server-side)
                 แล้ว renderTransferPanes() วาดสองฝั่งจากข้อมูลชุดเดียวกัน -->
            <div id="tableView" style="display: none;">
                <div class="d-none">
                    <table id="table" class="table" style="width:100%">
                        <thead>
                            <tr>
                                <th></th><th></th><th></th><th></th><th></th>
                                <th></th><th></th><th></th><th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>

                <div class="row g-3 p-3 pc-transfer">
                    <!-- ฝั่งซ้าย: ยังไม่เลือก -->
                    <div class="col-lg-6">
                        <div class="pc-pane h-100">
                            <div class="pc-pane__head">
                                <div>
                                    <i class="fa fa-users me-2"></i><?= lang('Common.pc_available_players') ?>
                                    <span class="badge rounded-pill bg-white text-dark ms-1" id="availableCount">0</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-light rounded-pill" id="pcSelectAllAvailable">
                                    <i class="ti ti-check me-1"></i><?= lang('Common.select_all') ?>
                                </button>
                            </div>
                            <div class="pc-pane__search">
                                <i class="fa fa-search"></i>
                                <input type="text" id="pcSearchAvailable" class="form-control"
                                       placeholder="<?= lang('Common.search') ?>" autocomplete="off">
                            </div>
                            <div class="pc-pane__body" id="availableList"></div>
                        </div>
                    </div>

                    <!-- ฝั่งขวา: เลือกแล้ว -->
                    <div class="col-lg-6">
                        <div class="pc-pane pc-pane--selected h-100">
                            <div class="pc-pane__head">
                                <div>
                                    <i class="fa fa-clipboard-check me-2"></i><?= lang('Common.pc_selected_players') ?>
                                    <span class="badge rounded-pill bg-white text-dark ms-1" id="selectedCount">0</span>
                                </div>
                                <button type="button" class="btn btn-sm btn-light rounded-pill" id="pcClearSelected">
                                    <i class="ti ti-x me-1"></i><?= lang('Common.pc_clear_all') ?>
                                </button>
                            </div>

                            <div class="pc-pane__search">
                                <i class="fa fa-search"></i>
                                <input type="text" id="pcSearchSelected" class="form-control"
                                       placeholder="<?= lang('Common.search') ?>" autocomplete="off">
                            </div>

                            <!-- ส่งรายชื่อแล้วและรายชื่อยังตรงกับที่ส่งไป -->
                            <div id="pcSubmittedOk" class="pc-pane__ok d-none">
                                <i class="fa fa-circle-check"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-bold"><?= lang('Common.pc_submitted_title') ?></div>
                                    <div class="small"><?= lang('Common.pc_submitted_hint') ?></div>
                                </div>
                            </div>

                            <!-- ยังไม่ได้ส่ง หรือส่งแล้วแต่แก้รายชื่อทีหลัง -->
                            <div id="pcSubmitWarning" class="pc-pane__warning d-none">
                                <i class="fa fa-triangle-exclamation"></i>
                                <div class="flex-grow-1">
                                    <div class="fw-bold" id="pcWarnTitle"><?= lang('Common.pc_not_submitted_title') ?></div>
                                    <div class="small" id="pcWarnHint"><?= lang('Common.pc_not_submitted_hint') ?></div>
                                </div>
                                <!-- เรียก openSubmitModal() ไม่ใช่เปิด modal ตรง ๆ
                                     เพราะฟังก์ชันนี้ตรวจว่ามีทัวร์นาเมนต์ที่ส่งได้ก่อน แล้ว trigger
                                     ให้ modal คำนวณจำนวนที่เลือกใหม่ ถ้าข้ามไปจะได้ค่าค้างของเดิม -->
                                <button type="button" class="btn btn-warning fw-bold text-nowrap px-4 py-2" id="pcGoSubmit"
                                        onclick="openSubmitModal()">
                                    <i class="fa fa-paper-plane me-2"></i><?= lang('Common.submit_player_list') ?>
                                </button>
                            </div>

                            <div class="pc-pane__body" id="selectedList"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card View -->
            <div id="cardView" class="p-4" style="display: none;">
                <div id="playerCardsContainer" class="row g-4">
                    <!-- Player cards will be populated here -->
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Coach Selection Card -->
<div class="card shadow-sm border-0 rounded-4 mb-4 pc-section">
    <div class="card-header pc-section__header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-2">
            <span class="pc-section__bar" style="background:#f39c12;"></span>
            <div>
                <h5 class="mb-0 fw-bold" style="color: #16478C;">
                    <i class="fa fa-user-tie me-2"></i><?= lang('Common.select_coach') ?>
                    <span class="badge rounded-pill ms-2" id="coachCountBadge" style="background:#f39c12;color:#fff;font-size:.72rem;">0/5</span>
                </h5>
                <small class="text-muted"><?= lang('Common.coach_staff') ?> · max 5</small>
            </div>
            <a class="btn btn-info" href="<?= base_url('academy/team-coach/manage/' . $academy['id'].'/'.$team['id']);?>">จัดการโค้ช</a>
        </div>

        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill" id="selectAllCoachesBtn" onclick="selectAllCoaches()">
            <i class="ti ti-check me-1"></i> <?= lang('Common.select_all') ?>
        </button>

    </div>
    <div class="card-body p-0">
        <?php if (!empty($coaches)): ?>
            <div class="table-responsive">
                <table class="table table-hover mb-0" id="coachTable">
                    <thead class="table-light">
                        <tr>
                            <th width="5%" class="text-center">
                                <input type="checkbox" id="checkAllCoachesHeader" class="form-check-input" onclick="selectAllCoaches()">
                            </th>
                            <th width="8%" class="text-center"><?= lang('Common.image') ?></th>
                            <th width="25%"><?= lang('Common.full_name') ?></th>
                            <th width="15%"><?= lang('Common.nickname') ?></th>
                            <th width="15%" class="text-center"><?= lang('Common.position') ?></th>
                            <th width="15%" class="text-center"><?= lang('Common.status_text') ?></th>
                            <th width="10%" class="text-center coach-order-col"><?= lang('Common.action') ?></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($coaches as $coach): ?>
                            <?php
                            $coachImage = base_url('public/img/img_profile.jpg');
                            if (!empty($coach['line_picture'])) {
                                $coachImage = (strpos($coach['line_picture'], 'http') === 0) ? $coach['line_picture'] : base_url($coach['line_picture']);
                            } elseif (!empty($coach['image_path'])) {
                                $coachImage = (strpos($coach['image_path'], 'http') === 0) ? $coach['image_path'] : base_url($coach['image_path']);
                            }
                            $coachName = trim(($coach['first_name'] ?? '') . ' ' . ($coach['last_name'] ?? ''));
                            if (empty($coachName)) {
                                $coachName = lang('Common.coach_default_name');
                            }
                            //$roleName = $coach['role_name'] ?? lang('Common.coach_default_name');
                            $roleName = $coach['role_name_display'] ?? lang('Common.coach_default_name');
                            $approvalStatus = $coach['approval_status'] ?? 'approved';
                            $isPending = $approvalStatus === 'pending';
                            ?>
                            <tr class="coach-row <?= $isPending ? 'table-warning' : '' ?>" data-coach-id="<?= $coach['id'] ?>" data-approval-status="<?= $approvalStatus ?>">
                                <td class="text-center align-middle">
                                    <input type="checkbox" value="<?= $coach['id'] ?>" class="form-check-input coach-checkbox" data-approval-status="<?= $approvalStatus ?>">
                                </td>
                                <td class="text-center align-middle">
                                    <img src="<?= esc($coachImage) ?>" alt="<?= esc($coachName) ?>"
                                        class="rounded-circle"
                                        style="width: 45px; height: 45px; object-fit: cover;"
                                        onerror="this.src='<?= base_url('public/img/img_profile.jpg') ?>'">
                                </td>
                                <td class="align-middle fw-semibold"><?= esc($coachName) ?></td>
                                <td class="align-middle text-muted"><?= esc($coach['nick_name'] ?? '-') ?></td>
                                <td class="text-center align-middle">
                                    <span class="badge rounded-pill" style="background-color: #f39c12; font-size: 0.8rem;">
                                        <?= esc($roleName) ?>
                                    </span>
                                </td>
                                <td class="text-center align-middle">
                                    <?php if ($isPending): ?>
                                        <span class="badge bg-warning text-dark rounded-pill">
                                            <i class="fa fa-clock me-1"></i><?= lang('Common.pending_approval') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-success rounded-pill">
                                            <i class="fa fa-check me-1"></i><?= lang('Common.approved_status') ?>
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center align-middle coach-order-cell">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-outline-warning" title="<?= lang('Common.edit_role') ?>" onclick="event.stopPropagation(); editCoachRole(<?= $coach['id'] ?>, '<?= esc($coach['role_display_label'] ?? $coach['role'] ?? $roleName ?? '') ?>')"><i class="fa fa-edit"></i></button>
                                        <button type="button" class="btn btn-outline-primary btn-move-up" title="<?= lang('Common.move_up') ?>" onclick="event.stopPropagation(); moveCoach(<?= $coach['id'] ?>, 'up')"><i class="fa fa-arrow-up"></i></button>
                                        <button type="button" class="btn btn-outline-primary btn-move-down" title="<?= lang('Common.move_down') ?>" onclick="event.stopPropagation(); moveCoach(<?= $coach['id'] ?>, 'down')"><i class="fa fa-arrow-down"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <p class="text-muted mb-0"><i class="fa fa-info-circle me-2"></i><?= lang('Common.no_coach_in_team') ?></p>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    #coachTable tbody tr {
        cursor: pointer;
        transition: background-color 0.2s ease;
    }

    #coachTable tbody tr:hover {
        background-color: rgba(22, 71, 141, 0.05);
    }

    #coachTable tbody tr.selected {
        background-color: rgba(22, 71, 141, 0.1);
    }

    #coachTable .form-check-input {
        width: 1.2em;
        height: 1.2em;
        cursor: pointer;
    }

    #coachTable .form-check-input:checked {
        background-color: #16478C;
        border-color: #16478C;
    }
</style>

<!-- Jersey / Kit Card — ชุดเหย้า/เยือน/สาม × ผู้เล่น/ผู้รักษาประตู (ผูกกับทัวร์นาเมนต์ที่เลือก → tournament_team) -->
<?php
$kitBase = rtrim(base_url(), '/');
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
$kitCfg = [
    'home'  => ['label' => lang('Common.jersey_home'),  'icon' => 'fa-house', 'color' => '#16478D'],
    'away'  => ['label' => lang('Common.jersey_away'),  'icon' => 'fa-plane', 'color' => '#e74c3c'],
    'third' => ['label' => lang('Common.jersey_third'), 'icon' => 'fa-star',  'color' => '#7A3FB0'],
];
$setCfg = [
    ''      => ['label' => lang('Common.jersey_player_set'), 'icon' => 'fa-person-running'],
    'goal_' => ['label' => lang('Common.jersey_gk_set'),     'icon' => 'fa-hand-paper'],
];
$partCfg = [
    'shirt'    => lang('Common.jersey_shirt'),
    'trousers' => lang('Common.jersey_trousers'),
    'socks'    => lang('Common.jersey_socks'),
];
$kitSvg = '
<svg class="jk-jersey" viewBox="0 0 130 178" xmlns="http://www.w3.org/2000/svg">
  <path class="jk-shirt" d="M65,14 C57,14 52,18 45,17 L18,28 L9,44 L27,56 L36,49 L36,98 L94,98 L94,49 L103,56 L121,44 L112,28 L85,17 C78,18 73,14 65,14 Z" stroke="rgba(0,0,0,.16)" stroke-width="1.2"/>
  <path class="jk-collar" d="M52,16 Q65,27 78,16 L74,13 Q65,21 56,13 Z" fill="rgba(0,0,0,.14)"/>
  <path class="jk-shorts" d="M37,101 L93,101 L90,141 L68,141 L65,114 L62,141 L40,141 Z" stroke="rgba(0,0,0,.16)" stroke-width="1.2"/>
  <rect class="jk-socks" x="44" y="145" width="13" height="27" rx="4" stroke="rgba(0,0,0,.16)" stroke-width="1.2"/>
  <rect class="jk-socks" x="72" y="145" width="13" height="27" rx="4" stroke="rgba(0,0,0,.16)" stroke-width="1.2"/>
</svg>';
?>
<div class="card shadow-sm border-0 rounded-4 mb-4 pc-section" id="kitSection">
    <div class="card-header pc-section__header bg-white border-0 py-3 px-4 d-flex align-items-center gap-2">
        <span class="pc-section__bar" style="background:#e74c3c;"></span>
        <div>
            <h5 class="mb-0 fw-bold" style="color: #16478C;">
                <i class="fa fa-tshirt me-2"></i><?= lang('Common.jersey_section') ?>
            </h5>
            <small class="text-muted"><?= lang('Common.jersey_home') ?> / <?= lang('Common.jersey_away') ?> / <?= lang('Common.jersey_third') ?></small>
        </div>
    </div>
    <div class="card-body px-4 pb-4">
        <!-- ยังไม่เลือกทัวร์นาเมนต์ -->
        <div id="kitNoTour" class="alert alert-light border rounded-3 text-center text-muted mb-0 py-4">
            <i class="fa fa-circle-info me-2"></i><?= lang('Common.please_select_tournament') ?>
        </div>

        <form id="kitForm" style="display:none;">
            <input type="hidden" name="tour_id" id="kitTourId" value="">

            <!-- Tabs: เหย้า / เยือน / สาม -->
            <ul class="nav jk-tabs gap-2 mb-4" role="tablist">
                <?php $kf = true; foreach ($kitCfg as $kk => $kc): ?>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link <?= $kf ? 'active' : '' ?>" data-bs-toggle="pill"
                                data-bs-target="#kit-tab-<?= $kk ?>" type="button" role="tab">
                            <i class="fas <?= $kc['icon'] ?>"></i><?= esc($kc['label']) ?>
                        </button>
                    </li>
                <?php $kf = false; endforeach; ?>
            </ul>

            <div class="tab-content">
                <?php $kf = true; foreach ($kitCfg as $kk => $kc): ?>
                    <div class="tab-pane fade <?= $kf ? 'show active' : '' ?>" id="kit-tab-<?= $kk ?>" role="tabpanel">
                        <div class="row g-4">
                            <?php foreach ($setCfg as $sk => $sc): ?>
                                <div class="col-lg-6">
                                    <div class="jk-set">
                                        <div class="jk-set-head">
                                            <span class="ico" style="background:<?= $kc['color'] ?>;">
                                                <i class="fas <?= $sc['icon'] ?>"></i>
                                            </span>
                                            <?= esc($kc['label']) ?> · <?= esc($sc['label']) ?>
                                        </div>
                                        <div class="jk-set-body">
                                            <div class="jk-preview-wrap"><?= $kitSvg ?></div>

                                            <?php foreach ($partCfg as $pk => $pl):
                                                $col = "{$kk}_{$sk}{$pk}"; ?>
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
                                                    <input type="hidden" name="<?= $col ?>" class="jk-color-input" data-col="<?= $col ?>" value="">
                                                </div>
                                            <?php endforeach; ?>

                                            <?php $imgCol = "{$kk}_{$sk}set_img"; ?>
                                            <div class="jk-img-field" data-col="<?= $imgCol ?>">
                                                <label class="jk-label"><?= lang('Common.jersey_set_img') ?></label>
                                                <div class="jk-upload">
                                                    <img class="jk-img-preview" src="" style="display:none">
                                                    <div class="jk-img-placeholder">
                                                        <i class="fas fa-cloud-arrow-up"></i>
                                                        <span><?= lang('Common.jersey_upload_hint') ?></span>
                                                    </div>
                                                </div>
                                                <input type="file" accept="image/*" class="d-none jk-img-input">
                                                <input type="hidden" name="<?= $imgCol ?>_data" class="jk-img-data" value="">
                                                <input type="hidden" name="<?= $imgCol ?>_remove" value="0" class="jk-img-remove">
                                                <div class="text-end">
                                                    <button type="button" class="btn btn-sm btn-link text-danger jk-img-del p-0 mt-1" style="display:none">
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
                <?php $kf = false; endforeach; ?>
            </div>

            <div class="d-flex justify-content-end mt-4 pt-3 border-top">
                <button type="button" id="btnSaveKit" class="btn btn-primary rounded-pill px-5 fw-bold" style="background:#16478D;border:none;">
                    <i class="fas fa-save me-2"></i><?= lang('Common.jersey_save') ?>
                </button>
            </div>
        </form>
    </div>
</div>

<style>
    .jk-tabs .nav-link { border-radius:50px; font-weight:600; color:#64748b; padding:8px 22px; display:flex; align-items:center; gap:8px; border:1.5px solid transparent; }
    .jk-tabs .nav-link.active { background:#16478D; color:#fff; box-shadow:0 6px 16px rgba(22,71,141,.25); }
    .jk-set { border:1px solid #e9eef5; border-radius:18px; background:#fff; height:100%; overflow:hidden; }
    .jk-set-head { padding:14px 18px; font-weight:700; color:#1e293b; display:flex; align-items:center; gap:10px; border-bottom:1px solid #eef2f7; }
    .jk-set-head .ico { width:34px; height:34px; border-radius:10px; display:flex; align-items:center; justify-content:center; color:#fff; }
    .jk-set-body { padding:18px; }
    .jk-preview-wrap { display:flex; justify-content:center; background:linear-gradient(180deg,#f8fbff,#eef3fb); border-radius:14px; padding:14px; margin-bottom:16px; }
    .jk-jersey { width:130px; height:auto; filter:drop-shadow(0 8px 12px rgba(0,0,0,.08)); }
    .jk-jersey .jk-shirt, .jk-jersey .jk-shorts, .jk-jersey .jk-socks { fill:#e5e7eb; transition:fill .25s ease; }
    .jk-label { font-size:.8rem; font-weight:600; color:#64748b; margin-bottom:4px; display:block; }
    .jk-color-field { margin-bottom:12px; }
    .jk-dd-btn { width:100%; display:flex; align-items:center; gap:10px; background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:10px; padding:8px 12px; font-weight:500; color:#1e293b; cursor:pointer; transition:border-color .2s; }
    .jk-dd-btn:hover, .jk-dd.show .jk-dd-btn { border-color:#16478D; }
    .jk-swatch { width:22px; height:22px; border-radius:6px; border:1.5px solid rgba(0,0,0,.12); flex:0 0 auto; background:#fff; }
    .jk-swatch-clear { background:linear-gradient(45deg, transparent 45%, #e11 45%, #e11 55%, transparent 55%), #fff !important; }
    .jk-dd-menu { max-height:280px; overflow-y:auto; border-radius:12px; border:1px solid #e2e8f0; box-shadow:0 10px 30px rgba(0,0,0,.1); padding:6px; min-width:200px; }
    .jk-dd-menu .dropdown-item { display:flex; align-items:center; gap:10px; border-radius:8px; padding:7px 10px; font-weight:500; }
    .jk-dd-menu .dropdown-item:active { background:#16478D; color:#fff; }
    .jk-img-field { margin-top:6px; }
    .jk-upload { border:2px dashed #cdd7e5; border-radius:12px; height:120px; display:flex; align-items:center; justify-content:center; cursor:pointer; background:#f8fafc; overflow:hidden; transition:border-color .2s, background .2s; }
    .jk-upload:hover { border-color:#16478D; background:#f1f6ff; }
    .jk-img-preview { max-height:100%; max-width:100%; object-fit:contain; }
    .jk-img-placeholder { text-align:center; color:#94a3b8; }
    .jk-img-placeholder i { font-size:1.6rem; display:block; margin-bottom:6px; }
</style>

<script>
(function () {
    const KIT_COLORS = <?= json_encode(array_map(fn($c) => ['h' => $c[0], 'n' => $c[1]], $KIT_COLORS), JSON_UNESCAPED_UNICODE) ?>;
    const KIT_BASE   = '<?= $kitBase ?>';
    const KIT_LOAD_URL = '<?= base_url('academy/tournament-jersey/' . ($academy['id'] ?? '') . '/' . ($team['id'] ?? '')) ?>';
    const KIT_SAVE_URL = '<?= base_url('academy/tournament-jersey/save/' . ($academy['id'] ?? '') . '/' . ($team['id'] ?? '')) ?>';
    const KIT_LANG = {
        notSet: '<?= lang('Common.jersey_not_set') ?>',
        saving: '<?= lang('Common.saving') ?>',
        netErr: '<?= lang('Common.connection_error_short') ?>',
        pickTour: '<?= lang('Common.please_select_tournament') ?>'
    };

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
            nm.textContent = KIT_LANG.notSet;
            nm.classList.add('text-muted');
        }
    }

    function recolorCard(card) {
        if (!card) return;
        const svg = card.querySelector('.jk-jersey');
        if (!svg) return;
        const pick = part => {
            const f = card.querySelector('.jk-color-field[data-part="' + part + '"] .jk-color-input');
            return (f && f.value) ? f.value : '#e5e7eb';
        };
        svg.querySelectorAll('.jk-shirt').forEach(el => el.style.fill = pick('shirt'));
        svg.querySelectorAll('.jk-shorts').forEach(el => el.style.fill = pick('trousers'));
        svg.querySelectorAll('.jk-socks').forEach(el => el.style.fill = pick('socks'));
    }

    function refreshAll() {
        document.querySelectorAll('#kitForm .jk-color-field').forEach(applyField);
        document.querySelectorAll('#kitForm .jk-set').forEach(recolorCard);
    }

    // เติมค่าจาก server (หรือล้างเป็นค่าว่าง)
    function fillKit(kit) {
        kit = kit || {};
        document.querySelectorAll('#kitForm .jk-color-input').forEach(inp => {
            inp.value = kit[inp.dataset.col] || '';
        });
        document.querySelectorAll('#kitForm .jk-img-field').forEach(f => {
            const col = f.dataset.col;
            const val = kit[col] || '';
            const img = f.querySelector('.jk-img-preview');
            const ph  = f.querySelector('.jk-img-placeholder');
            const del = f.querySelector('.jk-img-del');
            f.querySelector('.jk-img-data').value = '';
            f.querySelector('.jk-img-remove').value = '0';
            f.querySelector('.jk-img-input').value = '';
            if (val) {
                img.src = KIT_BASE + '/' + String(val).replace(/^\//, '');
                img.style.display = '';
                ph.style.display = 'none';
                del.style.display = '';
            } else {
                img.src = '';
                img.style.display = 'none';
                ph.style.display = '';
                del.style.display = 'none';
            }
        });
        refreshAll();
    }

    // เรียกจาก scripts.php ตอนเปลี่ยนทัวร์นาเมนต์
    window.loadTournamentKit = function (tourId) {
        const form = document.getElementById('kitForm');
        const note = document.getElementById('kitNoTour');
        if (!form || !note) return;

        if (!tourId) {
            form.style.display = 'none';
            note.style.display = '';
            document.getElementById('kitTourId').value = '';
            fillKit({});
            return;
        }

        document.getElementById('kitTourId').value = tourId;
        fetch(KIT_LOAD_URL + '?tour_id=' + encodeURIComponent(tourId), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(r => r.json())
        .then(res => {
            fillKit(res && res.status === 'success' ? res.kit : {});
            note.style.display = 'none';
            form.style.display = '';
        })
        .catch(() => { fillKit({}); note.style.display = 'none'; form.style.display = ''; });
    };

    document.addEventListener('DOMContentLoaded', function () {
        // สร้างเมนูสี
        const menuHtml = (function () {
            let h = '<li><a class="dropdown-item jk-opt" href="#" data-hex=""><span class="jk-swatch jk-swatch-clear"></span>' + KIT_LANG.notSet + '</a></li>';
            KIT_COLORS.forEach(c => {
                h += '<li><a class="dropdown-item jk-opt" href="#" data-hex="' + c.h + '"><span class="jk-swatch" style="background:' + c.h + '"></span>' + c.n + '</a></li>';
            });
            return h;
        })();
        document.querySelectorAll('#kitForm .jk-dd-menu').forEach(m => { m.innerHTML = menuHtml; });

        document.querySelectorAll('#kitForm .jk-dd-menu').forEach(menu => {
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

        document.querySelectorAll('#kitForm .jk-upload').forEach(up => {
            up.addEventListener('click', function () {
                const inp = up.closest('.jk-img-field').querySelector('.jk-img-input');
                if (inp) inp.click();
            });
        });

        document.querySelectorAll('#kitForm .jk-img-input').forEach(inp => {
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
                    card.querySelector('.jk-img-data').value = ev.target.result;
                };
                reader.readAsDataURL(file);
            });
        });

        document.querySelectorAll('#kitForm .jk-img-del').forEach(btn => {
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

        const btnSave = document.getElementById('btnSaveKit');
        if (btnSave) btnSave.addEventListener('click', function () {
            const tourId = document.getElementById('kitTourId').value;
            if (!tourId) {
                Swal.fire({ icon: 'warning', title: KIT_LANG.pickTour, confirmButtonColor: '#16478D' });
                return;
            }
            const btn = this;
            const fd = new FormData(document.getElementById('kitForm'));
            const original = btn.innerHTML;
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>' + KIT_LANG.saving;

            fetch(KIT_SAVE_URL, {
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
                    window.loadTournamentKit(tourId); // reload ให้ path รูปเป็นของจริงจาก server
                } else {
                    Swal.fire({ icon: 'error', title: res.message || KIT_LANG.netErr, confirmButtonColor: '#16478D' });
                }
            })
            .catch(() => {
                btn.disabled = false;
                btn.innerHTML = original;
                Swal.fire({ icon: 'error', title: KIT_LANG.netErr, confirmButtonColor: '#16478D' });
            });
        });

        // sync กับทัวร์ที่เลือกอยู่ตอนโหลดหน้า
        const sel = document.getElementById('tournament_filter');
        window.loadTournamentKit(sel ? sel.value : '');
    });
})();
</script>

<!-- Export Modal -->
<div class="modal fade" id="export-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary"><i class="fa fa-file-pdf me-2"></i><?= lang('Common.export_data') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <div class="alert alert-info py-2 px-3 mb-3 small d-flex align-items-start gap-2 rounded-3" style="background:#e7f1ff; border:1px solid #b8d4ff; color:#0d4a8c;">
                    <i class="fa fa-info-circle mt-1"></i>
                    <div><?= lang('Common.export_modal_hint') ?></div>
                </div>
                <div class="mb-3">
                    <label for="tournament_select" class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.select_tournament') ?></label>
                    <select class="form-select bg-light border-0" id="tournament_select" name="tournament_select" onchange="updateGeneration()">
                        <option value=""><?= lang('Common.select_tournament_option') ?></option>
                        <?php if (!empty($joined_tournaments)): ?>
                            <?php
                            $today = date('Y-m-d');
                            foreach ($joined_tournaments as $tournament):
                                if ($tournament['team_id'] != ($team['id'] ?? '')) continue;

                                $endDate = $tournament['tournament_end_date'] ?? $tournament['end_date'] ?? null;
                                $isEnded = !empty($endDate) && $endDate < $today;
                                $regStatus = intval($tournament['register_status'] ?? 0);

                                // แสดงทั้ง อนุมัติแล้ว(1), รออนุมัติ(0), รอชำระเงิน(3) รวมทั้งที่จบแล้ว (ยังต้อง export ได้)
                                if (in_array($regStatus, [0, 1, 3])):
                                    $exportStatusLabel = '';
                                    if ($regStatus === 0) $exportStatusLabel = ' [' . lang('Common.pending_approval') . ']';
                                    elseif ($regStatus === 3) $exportStatusLabel = ' [' . lang('Common.pending_payment') . ']';
                                    if ($isEnded) $exportStatusLabel .= ' [' . lang('Common.ended') . ']';
                                ?>
                                    <?php $listStatus = intval($tournament['player_list_status'] ?? 0); ?>
                                    <option value="<?= $tournament['tour_id'] ?>"
                                            data-name="<?= $tournament['name'] ?>"
                                            data-gen="<?= $tournament['generation'] ?? '' ?>"
                                            data-team-id="<?= $tournament['team_id'] ?? '' ?>"
                                            data-tour-team-id="<?= $tournament['tour_team_id'] ?? '' ?>"
                                            data-register-status="<?= $regStatus ?>"
                                            data-player-list-status="<?= $listStatus ?>">
                                        <?= esc($tournament['name']) ?> (<?= esc($tournament['generation'] ?? '-') ?>)<?= $exportStatusLabel ?>
                                    </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="generation_input" class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.generation') ?></label>
                    <input type="text" class="form-control bg-light border-0" id="generation_input" name="generation_input" readonly placeholder="<?= lang('Common.generation_placeholder') ?>">
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.close') ?></button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="createPDF()">
                    <i class="fa fa-print me-2"></i><?= lang('Common.create_document_btn') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Submit Player List Modal -->
<div class="modal fade" id="submit-player-list-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-success"><i class="fa fa-check-circle me-2"></i><?= lang('Common.confirm_submit_player_list') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <p class="text-muted mb-3"><?= lang('Common.select_tournament_to_submit') ?></p>
                <div class="mb-3">
                    <label for="submit_tournament_select" class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.tournament_label') ?></label>
                    <select class="form-select bg-light border-0" id="submit_tournament_select" name="submit_tournament_select">
                        <option value=""><?= lang('Common.select_tournament_option') ?></option>
                        <?php if (!empty($joined_tournaments)): ?>
                            <?php foreach ($joined_tournaments as $tournament): ?>
                                <?php if ($tournament['team_id'] == ($team['id'] ?? '')): ?>
                                    <?php
                                        $regStatus = intval($tournament['register_status'] ?? 0);
                                        // แสดงทั้ง อนุมัติแล้ว(1), รออนุมัติ(0), รอชำระเงิน(3)
                                        $statusLabel = '';
                                        if ($regStatus === 0) $statusLabel = ' [' . lang('Common.pending_approval') . ']';
                                        elseif ($regStatus === 3) $statusLabel = ' [' . lang('Common.pending_payment') . ']';
                                    ?>
                                        <option value="<?= $tournament['tour_team_id'] ?? '' ?>"
                                            data-tour-id="<?= $tournament['tour_id'] ?? '' ?>"
                                            data-status="<?= $tournament['player_list_status'] ?? 0 ?>"
                                            data-player-count="<?= $tournament['player_count'] ?? 0 ?>"
                                            data-team-id="<?= $tournament['team_id'] ?? '' ?>"
                                            data-register-status="<?= $regStatus ?>">
                                            <?= esc($tournament['name']) ?> (<?= esc($tournament['generation'] ?? '-') ?>)<?= $statusLabel ?>
                                            <?php if (intval($tournament['player_list_status'] ?? 0) === 1): ?>
                                                - <?= lang('Common.already_submitted') ?> ✓
                                            <?php endif; ?>
                                        </option>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                <div id="playerCountInfo" class="alert alert-primary d-none">
                    <i class="fa fa-users me-2"></i>
                    <span id="playerCountInfoText"></span>
                </div>
                <div id="sourceCheckInfo" class="d-none mb-3">
                    <span id="sourceCheckInfoText"></span>
                </div>
                <div id="submitStatusInfo" class="alert alert-info d-none">
                    <i class="fa fa-info-circle me-2"></i><?= lang('Common.already_submitted_info') ?>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-success rounded-pill px-4" id="submitPlayerListBtn" onclick="submitPlayerList()">
                    <i class="fa fa-paper-plane me-2"></i><?= lang('Common.confirm_submit_btn') ?>
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Submitted Players Modal -->
<div class="modal fade" id="view-players-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold" style="color: #16478C;">
                    <i class="fa fa-users me-2"></i><?= lang('Common.submitted_players') ?>
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-3">
                <p class="text-muted mb-3" id="viewPlayersTournamentName"></p>
                <div id="viewPlayersLoading" class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden"><?= lang('Common.loading_msg') ?></span>
                    </div>
                </div>
                <div id="viewPlayersContent" class="d-none">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead style="background-color: #16478C;">
                                <tr style="color: white;">
                                    <th class="ps-3 text-center"><?= lang('Common.order_number') ?></th>
                                    <th class="text-center"><?= lang('Common.image') ?></th>
                                    <th class="text-center"><?= lang('Common.shirt_number') ?></th>
                                    <th><?= lang('Common.full_name') ?></th>
                                </tr>
                            </thead>
                            <tbody id="viewPlayersTableBody">
                                <!-- Players will be populated here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="viewPlayersCount" class="text-muted text-end mt-2 pe-3 small"></div>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.close') ?></button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Coach Role Modal -->
<div class="modal fade" id="edit-coach-role-modal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg rounded-4">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-primary"><i class="fa fa-edit me-2"></i><?= lang('Common.edit_role') ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body pt-4">
                <input type="hidden" id="edit_coach_id">
                <div class="mb-3">
                    <label for="coach_role_select" class="form-label text-muted small fw-bold text-uppercase"><?= lang('Common.position') ?></label>
                    <select class="form-select bg-light border-0" id="coach_role_select">
                        <option value=""><?= lang('Common.select_position_option') ?></option>
                        <?php if (!empty($positionMapping)): ?>
                            <?php foreach ($positionMapping as $key => $value): ?>
                                <option value="<?= esc($key) ?>"><?= esc($value) ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 pb-4">
                <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal"><?= lang('Common.cancel') ?></button>
                <button type="button" class="btn btn-primary rounded-pill px-4" onclick="saveCoachRole()">
                    <i class="fa fa-save me-2"></i><?= lang('Common.save_btn') ?>
                </button>
            </div>
        </div>
    </div>
</div>
<!-- Submission History Card -->
<div class="card shadow-sm border-0 rounded-4 mb-4 pc-section">
    <div class="card-header pc-section__header bg-white border-0 py-3 px-4 d-flex align-items-center gap-2">
        <span class="pc-section__bar" style="background:#16a34a;"></span>
        <div>
            <h5 class="mb-0 fw-bold" style="color: #16478C;"><i class="fa fa-history me-2"></i><?= lang('Common.submission_history') ?></h5>
            <small class="text-muted"><?= lang('Common.submitted_date') ?></small>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr style="color: #16478C;">
                        <th class="ps-4"><?= lang('Common.tournament_label') ?></th>
                        <th><?= lang('Common.generation') ?></th>
                        <th><?= lang('Common.team_label') ?></th>
                        <th class="text-center"><?= lang('Common.registration_status') ?></th>
                        <th class="text-center"><?= lang('Common.player_list_status') ?></th>
                        <th class="text-center"><?= lang('Common.submitted_date') ?></th>
                        <th class="text-center pe-4"><?= lang('Common.manage') ?></th>
                    </tr>
                </thead>
                <tbody id="history-table-body">
                    <?php if (!empty($joined_tournaments)): ?>
                        <?php
                        $hasRows = false;
                        foreach ($joined_tournaments as $tournament):
                            if ($tournament['team_id'] != ($team['id'] ?? '')) continue;
                            $hasRows = true;
                        ?>
                            <tr class="history-row" data-team-id="<?= $tournament['team_id'] ?? '' ?>">
                                <td class="ps-4 fw-medium"><?= esc($tournament['name']) ?></td>
                                <td class="text-muted"><?= esc($tournament['generation'] ?? '-') ?></td>
                                <td class="text-muted"><?= esc($tournament['team_name'] ?? '-') ?></td>
                                <td class="text-center">
                                    <?php
                                    $registerStatus = intval($tournament['register_status'] ?? 0);
                                    if ($registerStatus === 1): ?>
                                        <span class="badge bg-success rounded-pill px-3 py-2">
                                            <i class="fa fa-check me-1"></i><?= lang('Common.approved_label') ?>
                                        </span>
                                    <?php elseif ($registerStatus === 0): ?>
                                        <span class="badge bg-warning text-white rounded-pill px-3 py-2">
                                            <i class="fa fa-clock me-1"></i><?= lang('Common.waiting_approval_label') ?>
                                        </span>
                                    <?php elseif ($registerStatus === 3): ?>
                                        <span class="badge bg-info rounded-pill px-3 py-2">
                                            <i class="fa fa-credit-card me-1"></i><?= lang('Common.waiting_payment') ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary rounded-pill px-3 py-2">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($registerStatus === 1): ?>
                                        <?php if (intval($tournament['player_list_status'] ?? 0) === 1): ?>
                                            <span class="badge bg-success rounded-pill px-3 py-2">
                                                <i class="fa fa-check me-1"></i><?= lang('Common.submitted_label') ?>
                                            </span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary rounded-pill px-3 py-2">
                                                <i class="fa fa-clock me-1"></i><?= lang('Common.not_submitted') ?>
                                            </span>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center text-muted">
                                    <?php if (!empty($tournament['player_list_submitted_at'])): ?>
                                        <?= date('d/m/Y H:i', strtotime($tournament['player_list_submitted_at'])) ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-center pe-4">
                                    <?php if (intval($tournament['player_list_status'] ?? 0) === 1): ?>
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill"
                                            onclick="viewSubmittedPlayers('<?= $tournament['tour_team_id'] ?>', '<?= esc($tournament['name']) ?>')">
                                            <i class="fa fa-eye me-1"></i><?= lang('Common.view_list') ?>
                                        </button>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (!$hasRows): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">
                                    <i class="fa fa-info-circle me-2"></i><?= lang('Common.no_joined_tournaments_msg') ?>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">
                                <i class="fa fa-info-circle me-2"></i><?= lang('Common.no_joined_tournaments_msg') ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>



<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?= $this->include('Modules\Academy\Views\partials\player_chart\scripts') ?>
<?php $this->endSection(); ?>
