<?php $this->extend('template/home') ?>
<?php $this->section('content'); ?>

<section id="result-section" class="hero-section hero-khelo">

    <div class="field">

        <div class="center-circle"></div>
        <div class="penalty left"></div>

        <div class="zone goalkeeper"
            data-position="goalkeeper"
            style="left:0; width:16%; height:100%;"
            onclick="selectZone(this)">
            <div class="zone-count">0</div>
            <div class="zone-percent">0%</div>
            <div class="zone-title">ประตู (GK)</div>
        </div>

        <div class="zone defender"
            data-position="defender"
            style="left:16%; width:24%; height:100%;"
            onclick="selectZone(this)">
            <div class="zone-count">0</div>
            <div class="zone-percent">0%</div>
            <div class="zone-title">กองหลัง (CB)</div>
        </div>

        <div class="zone midfielder"
            data-position="midfielder"
            style="left:40%; width:28%; height:100%;"
            onclick="selectZone(this)">
            <div class="zone-count">0</div>
            <div class="zone-percent">0%</div>
            <div class="zone-title">กองกลาง (MF)</div>
        </div>

        <div class="zone forward"
            data-position="forward"
            style="right:0; width:32%; height:100%;"
            onclick="selectZone(this)">
            <div class="zone-count">0</div>
            <div class="zone-percent">0%</div>
            <div class="zone-title">กองหน้า (FW)</div>
        </div>

    </div>

    <!-- ไม่ระบุข้อมูล แสดงนอกสนาม -->
    <div class="no-data-box" id="noDataBox" style="display:none;">
        <div class="no-data-title">ไม่ระบุข้อมูล N/A</div>
        <div class="no-data-count">0</div>
    </div>

</section>

<?php $this->endSection(); ?>

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

.hero-khelo{
    min-height:100vh;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    background:
        linear-gradient(135deg,rgba(33,71,144,.5),rgba(11,21,72,.6),rgba(58,95,208,.5)),
        url('<?= base_url('public/img/bg_home.jpg')?>');
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
    padding:15px;
    font-family:'Kanit', sans-serif;
}

/* ===== FIELD (Responsive) ===== */
.field{
    width:100%;
    max-width:1100px;
    aspect-ratio: 11 / 6;   /* แทน height 600px */
    background:#2e8b57;
    border:4px solid #fff;
    position:relative;
    border-radius:12px;
    box-shadow:0 15px 35px rgba(0,0,0,.35);
    overflow:hidden;
}

/* เส้นกลางสนาม */
.field::before{
    content:'';
    position:absolute;
    left:50%;
    top:0;
    width:2px;
    height:100%;
    background:#fff;
}

/* วงกลมกลาง */
.center-circle{
    position:absolute;
    width:18%;
    aspect-ratio:1/1;
    border:2px solid #fff;
    border-radius:50%;
    top:50%;
    left:50%;
    transform:translate(-50%,-50%);
}

/* เขตโทษ */
.penalty{
    position:absolute;
    width:14%;
    height:50%;
    border:2px solid #fff;
    top:50%;
    transform:translateY(-50%);
}
.penalty.left{ left:0; border-left:none; }

/* ===== ZONE ===== */
.zone{
    position:absolute;
    cursor:pointer;
    transition:.2s ease;
    display:flex;
    flex-direction:column;
    justify-content:center;
    align-items:center;
    color:#fff;
    padding:6px;
    text-align:center;
    overflow:hidden;
}

.zone:hover:not(.active){
    outline:2px solid #fff;
    transform:scale(1.02);
}

.zone.active{
    background:rgba(255,193,7,.8) !important;
    color:#000;
}

.goalkeeper{ background:rgba(0,0,0,0.50); }
.defender{   background:rgba(0,0,0,0.30); }
.midfielder{ background:rgba(255,255,255,0.08); }
.forward{    background:rgba(255,255,255,0.18); }

/* ===== Responsive Font Scale ===== */
.zone-title{
    font-size:clamp(10px, 1.8vw, 22px);
    font-weight:700;
    line-height:1.2;
    white-space:nowrap;
    text-overflow:ellipsis;
    overflow:hidden;
}

.zone-count{
    font-size:clamp(16px, 4.5vw, 60px);
    font-weight:900;
    line-height:1;
}

.zone-percent{
    font-size:clamp(9px, 1.4vw, 18px);
    font-weight:600;
    margin-top:3px;
}

/* ===== จอเล็กมาก (มือถือเล็ก) ===== */
@media (max-width:480px){

    .zone{
        padding:3px;
    }

    .zone-title{
        font-size:10px;
    }

    .zone-count{
        font-size:14px;
    }

    .zone-percent{
        font-size:9px;
    }
}

/* ===== ไม่ระบุข้อมูล ===== */
.no-data-box{
    margin-top:20px;
    background:rgba(255,255,255,.08);
    border:1px solid rgba(255,255,255,.3);
    border-radius:10px;
    padding:15px;
    text-align:center;
    color:#fff;
    backdrop-filter:blur(6px);
    width:100%;
    max-width:500px;
}

.no-data-title{
    font-size:clamp(14px,2vw,18px);
    font-weight:600;
    margin-bottom:6px;
}

.no-data-count{
    font-size:clamp(20px,4vw,36px);
    font-weight:900;
}
</style>

<script>
    var base_url='<?php echo base_url();?>';
    const language = '<?=session()->get('language')?>';
    const DEFAULT_LOGO='https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg';
    $(function(){
        //getTour();
    });

    $(function () {
        $('.navbar-nav a[href^="#"]').each(function () {
            const hash = $(this).attr('href');
            $(this).attr('href', base_url + hash);
        });
    });
</script>

<script>

$(function(){
    loadFieldData();
});

function selectZone(el){
    document.querySelectorAll('.zone').forEach(z=>z.classList.remove('active'));
    el.classList.add('active');
}

function loadFieldData(){

    $.ajax({
        url: base_url + "/admin/tournament-pg/get-dashboard-statistic",
        type: "GET",
        dataType: "json",
        success: function(response){

            if(!response?.data?.player_position){
                console.warn("No player_position data");
                return;
            }

            let data = {};
            let noDataCount = 0;

            response.data.player_position.forEach(item => {

                const name = (item.name_en || "")
                    .toString()
                    .toLowerCase()
                    .trim();

                const count = parseInt(item.total_player || 0);

                switch(name){

                    case "attacker":
                        data["forward"] = count;
                        break;

                    case "midfielder":
                        data["midfielder"] = count;
                        break;

                    case "defender":
                        data["defender"] = count;
                        break;

                    case "goalkeeper":
                        data["goalkeeper"] = count;
                        break;

                    case "ไม่ระบุข้อมูล":
                    case "":
                    default:
                        noDataCount += count;
                }
            });

            renderField(data, noDataCount);
        },
        error: function(xhr){
            console.error("AJAX Error:", xhr.responseText);
        }
    });
}

function renderField(data, noDataCount){

    // รวมเฉพาะตำแหน่งในสนาม
    const total = Object.values(data).reduce((a,b)=>a+b,0);

    document.querySelectorAll('.zone').forEach(zone=>{
        const key = zone.dataset.position;
        const count = data[key] || 0;

        zone.querySelector('.zone-count').innerText =
            count.toLocaleString();

        const percent = total > 0
            ? ((count / total) * 100).toFixed(2)
            : 0;

        zone.querySelector('.zone-percent').innerText =
            percent + " %";
    });

    // แสดงไม่ระบุข้อมูลนอกสนาม
    const box = document.getElementById("noDataBox");

    if(noDataCount > 0){
        box.style.display = "block";
        box.querySelector(".no-data-count").innerText =
            noDataCount.toLocaleString();
    }else{
        box.style.display = "none";
    }
}
</script>

<?php $this->endSection(); ?>
