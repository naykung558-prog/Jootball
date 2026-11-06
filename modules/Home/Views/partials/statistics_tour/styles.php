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

/* ================= HERO ================= */

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

/* ================= RESULT SECTION ================= */

.result-section{
    padding:30px 0;
}

/* ================= CARD ================= */

.card {
    background: transparent;
}

#home .card {
    max-width:1600px;
    margin:0 auto;
    border-radius:28px;
}

/* ================= STATISTICS ================= */

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
    .stat-box{ height:150px; }
    .stat-value{ font-size:48px; }
}

@media (min-width:1400px){
    .stat-value{ font-size:4.5rem; }
    .win-card .stat-value{ font-size:5rem; }
}

#team-name{
    font-size:5rem;
    letter-spacing:.5px;
}

#home .row{
    --bs-gutter-x:2rem;
    --bs-gutter-y:2rem;
}

/* ================= BUTTON ================= */

#btn_player{
    min-width:200px;
    font-size:2em;
    padding:10px;
    background:transparent;
    color:#fff;
    border:1px #fff solid;
}

/* ================= PLAYER MODAL ================= */

.players-grid{
    display:grid;
    grid-template-columns:repeat(auto-fill,minmax(160px,1fr));
    gap:16px;
}

.player-card{
    background:#fff;
    border-radius:10px;
    padding:12px;
    text-align:center;
    box-shadow:0 2px 6px rgba(0,0,0,.08);
}

.player-photo{
    width:100%;
    aspect-ratio:1/1;
    object-fit:cover;
    background:#f1f1f1;
    border-radius:8px;
}

.player-name{
    font-weight:400;
    margin-top:8px;
    font-size:13px;
}

.player-position{
    font-size:13px;
    color:#666;
}

.team-logo{
    width:28px;
    height:28px;
    object-fit:contain;
    background:#eee;
}

/* ================= SCROLL INDICATOR ================= */

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

/* Ranking */
.ranking-table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;}
.ranking-table thead th{background:#f8f9fa;font-weight:600;font-size:13px;padding:10px;border-bottom:1px solid #dee2e6;text-align:center;}
.ranking-table tbody td{padding:3px;font-size:13px;border-bottom:1px solid #eee;text-align:center;}
.rank-team{display:flex;align-items:center;gap:8px;text-align:left;}
.rank-team img{width:20px;height:20px;object-fit:contain;}
.rank-pos{font-weight:600;}
/* ===== highlight อันดับ 1–2 (ไม่ทำให้ตารางล้น) ===== */
#rankingContainer {
  padding: 10px;
  background: #fff !important;
}
.ranking-table tbody tr.top-rank td:first-child{
  position: relative;
  padding-left: 3px; /* เผื่อที่ให้แถบ */
}

.ranking-table tbody tr.top-rank td:first-child::before{
  content:'';
  position:absolute;
  left:4px;
  top:50%;
  transform:translateY(-50%);
  width:6px;
  height:70%;
  /*background:#0d6efd;*/
  border-radius:4px;
}


/* ===== TAB BASE ===== */
.nav-tabs {
    border-bottom: none;
    gap: 8px;
}

.nav-tabs .nav-item {
    margin-bottom: 0;
}

/* ===== TAB BUTTON ===== */
.nav-tabs .nav-link {
    border: none;
    background: #f1f3f5;
    color: #495057;
    font-weight: 600;
    padding: 10px 20px;
    border-radius: 12px;
    transition: all 0.25s ease;
    position: relative;
}

/* Hover */
.nav-tabs .nav-link:hover {
    background: #e9ecef;
    color: #000;
    transform: translateY(-2px);
}

/* Active */
.nav-tabs .nav-link.active {
    background: linear-gradient(135deg, #1e3c72, #2a5298);
    color: #fff !important;
    box-shadow: 0 6px 18px rgba(0,0,0,0.15);
}

/* Active underline effect */
.nav-tabs .nav-link.active::after {
    content: "";
    position: absolute;
    bottom: -6px;
    left: 20%;
    width: 60%;
    height: 4px;
    border-radius: 4px;
    background: #ffc107;
}

/* ===== TAB CONTENT ===== */
.tab-content {
    margin-top: 20px;
}

/* Optional: smooth fade animation */
.tab-pane {
    animation: fadeInTab 0.25s ease;
    min-height: 500px;
}

@keyframes fadeInTab {
    from { opacity: 0; transform: translateY(5px); }
    to { opacity: 1; transform: translateY(0); }
}

.nav-tabs .nav-link.active {
    background: #000;
    color: #fff;
    border: 2px solid #ffc107;
}

.match-btn{font-size:.75rem;background:#0d6efd;color:#fff;padding:2px 6px;border-radius:4px;display:inline-block;margin-top:4px;text-align:center;}
.match-seq{
    display:inline-flex;
    align-items:center;
    justify-content:center;
    min-width:42px;
    height:24px;
    border-radius:8px;
    background:#000;
    color:#fff;
    font-size:12px;
    font-weight:700;
}
.match-date{
    font-size:12px;
    color:#6c757d;
}

/* ===== MATCH CARD LAYOUT FIX ===== */

.match-row{
    display: grid;
    grid-template-columns: 1fr auto 1fr; /* ซ้าย-กลาง-ขวา เท่ากัน */
    align-items: center;
    gap: 10px;
}

/* บล็อคทีม */
.team-block{
    text-align: center;
    padding: 6px;
    min-width: 0; /* สำคัญมาก ป้องกันดัน layout */
}

.team-block img{
    max-width: 100%;
}

/* ชื่อทีม */
.team-block .fw-bold{
    display: block;
    margin-top: 6px;
    font-size: 14px;
    word-break: break-word;       /* ตัดคำยาว */
    overflow-wrap: break-word;    /* กันคำยาวดันคอลัมน์ */
}

/* สกอร์ */
.score-block{
    font-size: 22px;
    min-width: 60px;
    text-align: center;
}

/* ===== Tournament Final Results ===== */
.tour-results{
    --result-primary-blue:#0033a0;
    --result-accent-blue:#007bff;
    --result-gold:#ffcc00;
    --result-glass-white:rgba(255,255,255,.9);
    --result-blue-glow:rgba(0,123,255,.3);
    color:var(--result-primary-blue);
    padding:38px 14px 20px;
}
.tour-results .header-title{
    font-weight:800;
    color:var(--result-primary-blue);
    margin-bottom:62px;
    text-shadow:0 4px 15px rgba(0,51,160,.1);
}
.podium-wrapper{
    display:flex;
    align-items:flex-end;
    justify-content:center;
    max-width:900px;
    margin:0 auto 46px;
    padding:0 15px;
}
.podium-column{
    flex:1;
    display:flex;
    justify-content:center;
}
.podium-card{
    background:var(--result-glass-white);
    backdrop-filter:blur(12px);
    border:2px solid rgba(255,255,255,.7);
    border-radius:35px;
    padding:30px 15px;
    text-align:center;
    width:100%;
    max-width:180px;
    box-shadow:0 15px 35px rgba(0,51,160,.08);
    transition:all .4s cubic-bezier(.175,.885,.32,1.275);
    position:relative;
    overflow:hidden;
}
.podium-card::after{
    content:"";
    position:absolute;
    top:-50%;
    left:-150%;
    width:200%;
    height:200%;
    background:linear-gradient(45deg,transparent,rgba(255,255,255,.6),transparent);
    transform:rotate(45deg);
    transition:.8s;
}
.podium-card:hover::after{left:100%;}
.podium-card:hover{
    transform:translateY(-20px) scale(1.02);
    box-shadow:0 30px 50px var(--result-blue-glow);
    border-color:var(--result-accent-blue);
}
.podium-card.rank-1{
    height:360px;
    max-width:220px;
    z-index:10;
    border:3px solid var(--result-gold) !important;
    background:linear-gradient(180deg,#fff 0%,#fffbeb 100%);
}
.podium-card.rank-2{height:280px;}
.podium-card.rank-3{height:250px;}
.avatar-wrap{
    position:relative;
    margin-bottom:20px;
    display:inline-block;
}
.avatar-img{
    width:85px;
    height:85px;
    border-radius:50%;
    border:5px solid #fff;
    box-shadow:0 8px 25px rgba(0,0,0,.1);
    background:#fff;
    display:flex;
    align-items:center;
    justify-content:center;
    overflow:hidden;
}
.avatar-img img{
    width:100%;
    height:100%;
    object-fit:contain;
    padding:10px;
}
.rank-1 .avatar-img{
    width:110px;
    height:110px;
    border-color:var(--result-gold);
    box-shadow:0 0 30px rgba(245,158,11,.3);
}
.crown-icon{
    position:absolute;
    top:-45px;
    left:50%;
    transform:translateX(-50%);
    font-size:45px;
    filter:drop-shadow(0 5px 10px rgba(0,0,0,.2));
    z-index:11;
}
.name-label{
    font-weight:800;
    color:var(--result-primary-blue);
    font-size:1.1rem;
    margin-top:10px;
    overflow-wrap:anywhere;
}
.list-container{
    max-width:620px;
    margin:0 auto;
    padding:0 20px;
}
.section-subtitle{
    text-align:left;
    font-weight:800;
    color:var(--result-accent-blue);
    margin-bottom:20px;
    font-size:.9rem;
    text-transform:uppercase;
    letter-spacing:1px;
}
.result-list-item{
    background:var(--result-glass-white);
    border:1px solid rgba(255,255,255,.8);
    border-radius:20px;
    padding:18px 25px;
    margin-bottom:15px;
    display:flex;
    align-items:center;
    transition:all .3s ease;
    box-shadow:0 5px 15px rgba(0,51,160,.04);
}
.result-list-item:hover{
    transform:translateX(12px);
    background:#fff;
    box-shadow:0 15px 30px var(--result-blue-glow);
    border-color:var(--result-accent-blue);
}
.rank-num{
    font-weight:800;
    color:var(--result-accent-blue);
    font-size:1.3rem;
    width:45px;
}
.mention-badge{
    font-size:.65rem;
    font-weight:800;
    background:var(--result-accent-blue);
    color:#fff;
    padding:4px 12px;
    border-radius:10px;
    text-transform:uppercase;
    display:inline-block;
    margin-bottom:4px;
}
.result-team-logo{
    width:44px;
    height:44px;
    object-fit:contain;
    border-radius:12px;
    background:#fff;
    padding:6px;
    margin-right:14px;
    box-shadow:0 5px 15px rgba(0,51,160,.08);
}
.score-val{
    margin-left:auto;
    background:rgba(0,123,255,.08);
    color:var(--result-primary-blue);
    font-weight:800;
    padding:8px 18px;
    border-radius:15px;
    font-size:.95rem;
    white-space:nowrap;
}
@media (max-width:576px){
    .podium-wrapper{gap:5px;padding:0;}
    .podium-card{padding:15px 5px;border-radius:20px;}
    .podium-card.rank-1{height:300px;}
    .podium-card.rank-2{height:230px;}
    .podium-card.rank-3{height:210px;}
    .avatar-img{width:60px;height:60px;}
    .rank-1 .avatar-img{width:85px;height:85px;}
    .crown-icon{font-size:35px;top:-35px;}
    .name-label{font-size:.88rem;}
    .result-list-item{padding:14px 16px;}
}

</style>
