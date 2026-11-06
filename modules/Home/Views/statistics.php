<?php $this->extend('template/home') ?>
<?php $this->section('content'); ?>


<section id="result" class="result-section">
    <h1 class="mt-5" align="center"><?= lang('Common.statistics') ?></h1>
<!--
    <div class="swiper tournament-swiper mt-2" id="teamList">
        <div class="swiper-wrapper" id="teamListWrapper"></div>
        <div class="swiper-pagination" id="teamList-pagination"></div>
    </div>
-->
</section>

<section id="home" class="hero-section hero-khelo">
    <div class="hero-bg-wrapper">
        <div class="tour-panel">
            <div class="tour-toolbar">
                <div class="tour-search">
                    <i class="fas fa-search tour-search-icon"></i>
                    <input type="text" id="tourSearch" class="tour-search-input"
                        placeholder="<?= lang('Common.search_placeholder') ?>" autocomplete="off">
                </div>
                <span class="tour-count" id="tourCount"></span>
            </div>

            <div class="tour-scroll">
                <div class="swiper tournament-swiper" id="tourList">
                    <div class="swiper-wrapper" id="tourListWrapper"></div>
                </div>
                <div class="tour-empty" id="tourEmpty" style="display:none;">
                    <i class="fas fa-trophy"></i>
                    <p><?= lang('Common.no_tournament_found') ?></p>
                </div>
            </div>
        </div>
    </div>

    <div class="scroll-down-indicator">
        <span class="scroll-line"></span>
        <span class="scroll-text"><?= lang('Common.scroll') ?></span>
    </div>
</section>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
<?php $this->section('scripts'); ?>
<style>
/* ===== Navbar Styling for Map Page ===== */
.navbar .navbar-brand,
.navbar .navbar-brand:hover,
.navbar .nav-link,
.navbar .nav-link:hover,
.navbar .nav-link:focus,
.navbar .dropdown-toggle,
.navbar .dropdown-toggle:hover,
.navbar .navbar-nav .nav-link,
.navbar .navbar-nav.ms-auto .nav-link{
    color: #000000 !important;
}

.navbar .nav-link:hover,
.navbar .dropdown-toggle:hover,
.navbar .navbar-nav .nav-link:hover,
.navbar .navbar-nav.ms-auto .nav-link:hover{
    color: #f3b71a !important;
}

.navbar{
    background-color: rgba(255, 255, 255, 0.95) !important;
    backdrop-filter: blur(10px);
    border-bottom: 1px solid rgba(0, 0, 0, 0.1) !important;
}

.navbar .sponsor-badge{
    background: rgba(0, 0, 0, 0.05);
    border-left: 1px solid rgba(0, 0, 0, 0.1);
}
/* ===== End Navbar ===== */

:root {
    --primary-blue:#214790;
    --primary-yellow:#FBC02D;
    --dark-blue:#0B1548;
    --light-blue:#3A5FD0;
    --white:#fff;
    --text-light:#7F8C9F;
}

.hero-khelo{
    position:relative;
    min-height:100vh;
    display:flex;
    align-items:center;
    overflow:hidden;
    background:
        linear-gradient(135deg,rgba(33,71,144,.5),rgba(11,21,72,.6),rgba(58,95,208,.5)),
        url('<?= base_url('public/img/bg_home.jpg')?>');
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    padding:10px;
}

.hero-bg-wrapper,
.hero-bg-overlay,
.hero-pattern{
    position:absolute;
    inset:0;
}
.hero-bg-wrapper{
    padding:15px !important;
}
.hero-bg-overlay{
    background:linear-gradient(135deg,rgba(11,21,72,.3),rgba(33,71,144,.25),rgba(11,21,72,.35));
}

.pattern-circle{
    position:absolute;
    border-radius:50%;
    opacity:.04;
}
.pattern-circle.c1{
    width:400px;height:400px;
    background:var(--primary-blue);
    top:-100px;right:-100px;
}
.pattern-circle.c2{
    width:300px;height:300px;
    background:var(--primary-yellow);
    bottom:-80px;left:-80px;
}
.pattern-circle.c3{
    width:200px;height:200px;
    background:var(--light-blue);
    top:50%;left:30%;
}

.hero-shape{
    position:absolute;
    width:300px;height:300px;
    opacity:.05;
}
.shape-left{
    top:20%;left:-150px;
    background:linear-gradient(135deg,var(--primary-blue),var(--light-blue));
    clip-path:polygon(50% 0,100% 50%,50% 100%,0 50%);
}
.shape-right{
    bottom:10%;right:-100px;
    background:linear-gradient(135deg,var(--primary-yellow),#FFD54F);
    border-radius:50%;
}

.hero-row{
    position:relative;
    z-index:2;
    min-height:70vh;
}

.scroll-down-indicator{
    position:absolute;
    bottom:40px;
    left:50%;
    transform:translateX(-50%);
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:10px;
    z-index:10;
}
.scroll-line{
    width:2px;
    height:50px;
    background:linear-gradient(to bottom,var(--primary-yellow),transparent);
}
.scroll-text{
    font-size:.75rem;
    color:var(--text-light);
    font-weight:600;
    text-transform:uppercase;
    letter-spacing:1.5px;
    writing-mode:vertical-rl;
    transform:rotate(180deg);
}

.result-section{
    padding:30px 0;
}

/* =======================================================
   MODERN TOUR LIST (NEW DESIGN)
   ======================================================= */

.tournament-swiper{
    width:95%;
    max-width:1440px;
    margin:0 auto;
    position:relative;
}

/* Toolbar: ช่องค้นหา + ตัวนับ */
.tour-panel{
    width:95%;
    max-width:1200px;
    margin:0 auto;
    position:relative;
    z-index:2;
}
.tour-toolbar{
    display:flex;
    align-items:center;
    justify-content:space-between;
    gap:15px;
    flex-wrap:wrap;
    margin-bottom:18px;
}
.tour-search{
    position:relative;
    flex:1 1 320px;
    max-width:420px;
}
.tour-search-icon{
    position:absolute;
    top:50%;
    left:18px;
    transform:translateY(-50%);
    color:var(--primary-blue);
    font-size:15px;
    pointer-events:none;
}
.tour-search-input{
    width:100%;
    height:48px;
    border:none;
    border-radius:100px;
    padding:0 20px 0 46px;
    font-size:15px;
    color:#1e293b;
    background:rgba(255,255,255,.95);
    box-shadow:0 8px 24px rgba(0,0,0,.12);
    outline:none;
    transition:box-shadow .3s ease;
}
.tour-search-input:focus{
    box-shadow:0 8px 28px rgba(251,192,45,.35);
}
.tour-search-input::placeholder{ color:#94a3b8; }
.tour-count{
    color:#fff;
    font-weight:600;
    font-size:14px;
    background:rgba(255,255,255,.15);
    border:1px solid rgba(255,255,255,.25);
    padding:8px 16px;
    border-radius:100px;
    white-space:nowrap;
}

/* กล่อง scroll จำกัดความสูง + scrollbar สวย */
.tour-scroll{
    max-height:64vh;
    overflow-y:auto;
    padding:6px 10px 12px;
    border-radius:20px;
}
.tour-scroll .tournament-swiper{
    width:100%;
    max-width:none;
}
.tour-scroll::-webkit-scrollbar{ width:8px; }
.tour-scroll::-webkit-scrollbar-track{ background:rgba(255,255,255,.08); border-radius:100px; }
.tour-scroll::-webkit-scrollbar-thumb{ background:rgba(255,255,255,.35); border-radius:100px; }
.tour-scroll::-webkit-scrollbar-thumb:hover{ background:var(--primary-yellow); }
.tour-scroll{ scrollbar-width:thin; scrollbar-color:rgba(255,255,255,.35) transparent; }

/* สถานะไม่พบข้อมูล */
.tour-empty{
    text-align:center;
    color:#fff;
    padding:60px 20px;
}
.tour-empty i{
    font-size:48px;
    opacity:.5;
    margin-bottom:14px;
}
.tour-empty p{
    font-size:16px;
    font-weight:600;
    opacity:.85;
    margin:0;
}

/* ===== การ์ดทัวร์นาเมนต์ (Grid) ===== */
#tourList .swiper-wrapper{
    display:grid !important;
    grid-template-columns:repeat(auto-fill, minmax(190px,1fr));
    gap:22px;
    width:100%;
}

/* ป้องกัน swiper บังคับ width */
#tourList .swiper-slide{
    width:100% !important;
    margin:0 !important;
    display:flex;
}

/* การ์ด */
.tournament-item{
    position:relative;
    width:100%;
    min-height:232px;
    border-radius:22px;
    padding:24px 16px 18px;
    background:linear-gradient(180deg,#ffffff 0%,#fbfcff 100%);
    text-align:center;
    cursor:pointer;
    display:flex;
    flex-direction:column;
    align-items:center;
    box-shadow:0 8px 26px rgba(15,23,42,.06);
    transition:transform .4s cubic-bezier(.165,.84,.44,1), box-shadow .4s ease, border-color .3s ease;
    border:1px solid rgba(15,23,42,.05);
    text-decoration:none;
    color:#333;
    overflow:hidden;
}

/* แถบไล่สีด้านบน โผล่ตอน hover/active */
.tournament-item::before{
    content:"";
    position:absolute;
    top:0; left:0; right:0;
    height:4px;
    background:linear-gradient(90deg,var(--primary-blue),var(--primary-yellow));
    transform:scaleX(0);
    transform-origin:left;
    transition:transform .35s ease;
    z-index:1;
}
.tournament-item:hover::before,
.tournament-item.active::before{ transform:scaleX(1); }

.tournament-item.active{
    border-color:var(--primary-yellow);
    box-shadow:0 12px 30px rgba(251,192,45,.22);
}

.tournament-item:hover{
    transform:translateY(-8px);
    box-shadow:0 22px 44px rgba(15,23,42,.14);
    border-color:rgba(33,71,144,.25);
}

/* Shimmer effect on hover */
.tournament-item::after{
    content:"";
    position:absolute;
    top:-50%; left:-150%;
    width:200%; height:200%;
    background:linear-gradient(to right,transparent,rgba(255,255,255,.45),transparent);
    transform:rotate(45deg);
    transition:.7s;
    pointer-events:none;
}
.tournament-item:hover::after{ left:150%; }

/* logo */
.tournament-logo-wrapper{
    width:88px;
    height:88px;
    background:#f8fafc;
    border-radius:22px;
    padding:12px;
    margin-bottom:14px;
    display:flex;
    align-items:center;
    justify-content:center;
    box-shadow:inset 0 2px 6px rgba(0,0,0,.03);
    transition:all .35s ease;
}
.tournament-item:hover .tournament-logo-wrapper{
    background:#ffffff;
    transform:scale(1.08) rotate(2deg);
    box-shadow:0 8px 18px rgba(33,71,144,.12);
}
.tournament-logo{
    width:100%;
    height:100%;
    object-fit:contain;
}

/* name */
.tournament-name{
    font-size:15px;
    font-weight:700;
    line-height:1.25;
    color:#1e293b;
    margin-bottom:8px;
    min-height:38px;
    display:-webkit-box;
    -webkit-line-clamp:2;
    -webkit-box-orient:vertical;
    overflow:hidden;
    transition:color .3s ease;
}
.tournament-item:hover .tournament-name{ color:var(--primary-blue); }

/* meta: รุ่น + วันที่ */
.tournament-meta{
    margin-top:auto;
    display:flex;
    flex-direction:column;
    align-items:center;
    gap:8px;
}
.tournament-gen{
    display:inline-block;
    padding:3px 12px;
    background:#eef2ff;
    color:#4f46e5;
    font-size:11px;
    font-weight:600;
    border-radius:100px;
    text-transform:uppercase;
    letter-spacing:.5px;
    transition:all .3s ease;
}
.tournament-date{
    display:inline-flex;
    align-items:center;
    gap:5px;
    font-size:11.5px;
    color:#64748b;
    font-weight:500;
}
.tournament-date i{ font-size:10.5px; color:#94a3b8; }

/* responsive */
@media (min-width:1200px){
    #tourList .swiper-wrapper{ grid-template-columns:repeat(5,1fr); }
}
@media (max-width:991px){
    #tourList .swiper-wrapper{ grid-template-columns:repeat(3,1fr); }
}
@media (max-width:767px){
    #tourList .swiper-wrapper{ grid-template-columns:repeat(2,1fr); gap:15px; }
    .tournament-item{ min-height:216px; padding:20px 12px 16px; }
    .tournament-logo-wrapper{ width:72px; height:72px; }
}
@media (max-width:420px){
    .tournament-name{ font-size:14px; }
}

/* ======================================================= */
/* ส่วนอื่นคงเดิม */
/* ======================================================= */

.card{
    background:transparent;
}

#home .card{
    max-width:1600px;
    margin:0 auto;
    border-radius:28px;
}

.stat-box{
    height:180px;
    border-radius:24px;
    padding:20px;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    text-align:center;
    box-shadow:0 10px 28px rgba(0,0,0,.12);
}
.stat-card{
    background:rgba(255,255,255,0.08);
}
.win-card{
    background:linear-gradient(135deg,#1976d2,#0d47a1);
    color:#fff;
}
.stat-label{
    font-size:18px;
    font-weight:600;
    margin-bottom:12px;
}
.stat-value{
    font-size:60px;
    font-weight:900;
    line-height:1;
    font-variant-numeric:tabular-nums;
    color:#fff;
}
@media (max-width:768px){
    .stat-box{
        height:150px;
    }
    .stat-value{
        font-size:48px;
    }
}
</style>

<script>
    var tourId = 0;
    var team_id = 0;
    var base_url='<?php echo base_url();?>';
    const language = '<?=session()->get('language')?>';
    const DEFAULT_LOGO='https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg';
    var allTournaments = [];
    const langTournament = '<?= lang('Common.tournament') ?>';
    $(function(){
        getTour();
    });

$(function () {
    $('.navbar-nav a[href^="#"]').each(function () {
        const hash = $(this).attr('href');
        $(this).attr('href', base_url + hash);
    });
});
function getTour() {
    $.getJSON('<?= base_url('admin/tournament-pg/get-tournament-list') ?>', { page: 'statistic' }, res => {
        if (!res?.data) return;

        // เรียงตามวันที่สิ้นสุดล่าสุดขึ้นก่อน
        allTournaments = res.data.slice().sort(function (a, b) {
            return String(b.tournament_end_date || '').localeCompare(String(a.tournament_end_date || ''));
        });

        renderTournaments(allTournaments);
    });
}

function renderTournaments(list) {
    const $wrapper = $('#tourListWrapper');
    $wrapper.empty();

    if (!list || list.length === 0) {
        $('#tourEmpty').show();
        $('#tourCount').text('');
        return;
    }
    $('#tourEmpty').hide();

    list.forEach(function (t) {
        const logo = t.logo
            ? `<?= base_url() ?>/${t.logo}`
            : DEFAULT_LOGO;

        const genHtml = t.generation
            ? `<span class="tournament-gen">${t.generation}</span>`
            : '';

        const dateStr = formatTourDate(null, t.tournament_end_date);
        const dateHtml = dateStr
            ? `<span class="tournament-date"><i class="fas fa-calendar"></i>${dateStr}</span>`
            : '';

        $wrapper.append(`
            <div class="swiper-slide">
                <a href="` + base_url + '/home/statistics_tour/' + t.tour_id + `"
                class="tournament-item ${tourId == t.tour_id ? 'active' : ''}"
                data-id="${t.tour_id}">

                    <div class="tournament-logo-wrapper">
                        <img src="${logo}" class="tournament-logo"
                            onerror="this.src='${DEFAULT_LOGO}'">
                    </div>

                    <div class="tournament-name">${t.name}</div>

                    <div class="tournament-meta">${genHtml}${dateHtml}</div>
                </a>
            </div>
        `);
    });

    $('#tourCount').text(list.length + ' ' + langTournament);
}

// แปลงวันที่เป็นรูปแบบอ่านง่าย (รองรับ ไทย/อังกฤษ) แสดงช่วงเริ่ม - สิ้นสุด
function formatTourDate(start, end) {
    const loc = (language === 'en') ? 'en-GB' : 'th-TH';
    const fmt = function (d) {
        if (!d) return '';
        const dt = new Date(d + 'T00:00:00');
        if (isNaN(dt.getTime())) return '';
        return dt.toLocaleDateString(loc, { day: 'numeric', month: 'short', year: 'numeric' });
    };
    const s = fmt(start), e = fmt(end);
    if (s && e) return s + ' - ' + e;
    return s || e;
}

// ค้นหาแบบ realtime (ชื่อ / ชื่อ EN / รุ่น)
$(function () {
    $('#tourSearch').on('input', function () {
        const kw = $(this).val().trim().toLowerCase();
        if (!kw) {
            renderTournaments(allTournaments);
            return;
        }
        const filtered = allTournaments.filter(function (t) {
            return String(t.name || '').toLowerCase().includes(kw)
                || String(t.name_en || '').toLowerCase().includes(kw)
                || String(t.generation || '').toLowerCase().includes(kw);
        });
        renderTournaments(filtered);
    });
});


</script>
<?php $this->endSection(); ?>
