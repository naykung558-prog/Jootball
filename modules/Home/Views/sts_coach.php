<?php $this->extend('template/home') ?>
<?php $this->section('content'); ?>

<section class="hero-khelo">
    <div class="stack-wrapper">
        <div class="labels" id="labels"></div>
        <div class="pyramid" id="pyramid"></div>
    </div>

    <div class="modal fade" id="coachModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content">

            <div class="modal-header">
                <h5 id="coachModalHeader" class="modal-title">
                
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>

            <div id="coachModalBody" class="modal-body" >

            </div>

            <!--
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
            -->

            </div>
        </div>
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
    justify-content:center;
    align-items:center;
    background:
        linear-gradient(135deg,rgba(33,71,144,.85),rgba(11,21,72,.95)),
        url('<?= base_url('public/img/bg_home.jpg')?>');
    background-size:cover;
    background-position:center;
    font-family:'Kanit', sans-serif;
}

.stack-wrapper{
    width:100%;
    max-width:1000px;
    padding:0 15px;
    box-sizing:border-box;
    display:flex;
    flex-direction:column;
}

.labels{
    display:flex;
    justify-content:space-between;
    gap:10px;
    margin-bottom:5px;
    flex-wrap:wrap;
}

.label-item{
    flex:1;
    display:flex;              /* ✅ เพิ่ม */
    justify-content:center;    /* กึ่งกลางแนวนอน */
    align-items:center;        /* ✅ กึ่งกลางแนวตั้ง */

    text-align:center;
    font-size:.95rem;
    font-weight:600;
    color:#fff;
    padding:8px 10px;
    border-radius:8px;
    transition:.3s;
    cursor:pointer;
    background:rgba(255,255,255,0.05);
}


.label-item.active{
    background:rgba(255,255,255,0.18);
    transform:translateY(-4px);
}

.pyramid{
    display:flex;
    flex-direction:column;
    gap:10px;
}

.level{
    width:100%;
    display:flex;
    justify-content:center;
    align-items:center;
    padding:10px 0;
    transition:.3s;
    cursor:pointer;
}

.level.active{
    transform: scale(1.05);
}

.people{
    width:100%;
    display:grid;
    grid-template-columns: repeat(auto-fit, minmax(8px, max-content));
    justify-content:center;
    gap:4px;
    max-width:1200px;
    margin:0 auto;
}

.level.no-data{
    width:100vw;
    margin-left:calc(-50vw + 50%);
}

.level.no-data .people{
    width:100%;
    flex-wrap:wrap;
    white-space:normal;
}

.people i{
    font-size:12px;
    transition:.3s;
}

#coachModalBody {
    max-height: 70vh; /* ให้สูงได้สูงสุด 60% ของความสูงหน้าจอ */
    overflow-y: auto; /* ถ้าเกินให้มี scroll bar แนวดิ่ง */
    overflow-x: hidden; /* ป้องกัน scroll bar แนวนอน */
    padding-right: 10px; /* เว้นช่องไฟให้ scroll bar ไม่ทับเนื้อหา */
}

/* ตกแต่ง Scrollbar ให้ดูสวยทันสมัย (Optional) */
#coachModalBody::-webkit-scrollbar {
    width: 6px;
}
#coachModalBody::-webkit-scrollbar-thumb {
    background-color: #ddd;
    border-radius: 10px;
}
</style>

<script>
var base_url='<?php echo base_url();?>';
const language = '<?=session()->get('language')?>';
const DEFAULT_LOGO='https://www.gstatic.com/onebox/sports/logos/inverse-crest.svg';
const NO_PROFILE = 'https://cdn-icons-png.flaticon.com/512/149/149071.png';
var coach_arr = [];

$(function(){
    loadTeamStatistics();
});

$(function () {
    $('.navbar-nav a[href^="#"]').each(function () {
        const hash = $(this).attr('href');
        $(this).attr('href', base_url + hash);
    });
});

function loadTeamStatistics(){

    $.ajax({
        url: base_url + "/admin/tournament-pg/get-dashboard-statistic",
        type: "GET",
        dataType: "json",
        success: function(response){

            let data = [];

            if(Array.isArray(response)){
                data = response;
            }
            else if(response.data && response.data.coach){
                data = response.data.coach;
            }
            else if(response.coach){
                data = response.coach;
            }

            if(response.data && response.data.coach_arr){
                coach_arr = response.data.coach_arr;
            }

            if(data.length === 0) return;

            // ✅ แก้ตรงนี้เท่านั้น
            data = data.map(item => {

                let levelName = item.level_name 
                                ? String(item.level_name).trim() 
                                : "";

                if(
                    levelName === "" ||
                    levelName === "ไม่ระบุข้อมูล" ||
                    levelName === null ||
                    levelName === undefined
                ){
                    levelName = "ไม่ระบุข้อมูล N/A";
                }

                return {
                    id : item.id,
                    level: levelName,
                    count: parseInt(item.total_coach || 0)
                };
            });

            data.sort((a,b) => a.count - b.count);

            render(data);
        }
    });
}

function render(data){

    const pyramid = document.getElementById("pyramid");
    const labels = document.getElementById("labels");

    pyramid.innerHTML = "";
    labels.innerHTML = "";

    const colors = [
        "#FBC02D",
        "#29B6F6",
        "#66BB6A",
        "#EF5350",
        "#AB47BC",
        "#FF7043"
    ];

    data.forEach((item,index) => {

        const color = colors[index % colors.length];

        const label = document.createElement("div");
        label.className = "label-item";
        label.innerText = `${item.level} (${item.count})`;
        labels.appendChild(label);

        const layer = document.createElement("div");
        layer.className = "level";

        layer.setAttribute("data-cert_level_id", item.id);
        layer.setAttribute("data-cert_name", item.level);
        
        const people = document.createElement("div");
        people.className = "people";
        

        for(let i=0;i<item.count;i++){
            const icon = document.createElement("i");
            icon.className = "fa-solid fa-user";
            icon.style.color = color;
            people.appendChild(icon);
        }

        layer.appendChild(people);
        pyramid.appendChild(layer);

        layer.addEventListener("mouseenter",function(){
            layer.classList.add("active");
            label.classList.add("active");
        });

        layer.addEventListener("mouseleave",function(){
            layer.classList.remove("active");
            label.classList.remove("active");
        });

        label.addEventListener("mouseenter",function(){
            layer.classList.add("active");
            label.classList.add("active");
        });

        label.addEventListener("mouseleave",function(){
            layer.classList.remove("active");
            label.classList.remove("active");
        });

    });
}

// เปลี่ยนจากแบบเดิม เป็นแบบ .on()
$(document).on("click", ".level", function() {
    $("#coachModalHeader").empty();
    $("#coachModalBody").empty();
    const certId = $(this).attr("data-cert_level_id");     
    const certName = $(this).attr("data-cert_name");     
    $("#coachModalHeader").html(certName);
// ล้างข้อมูลเก่าใน Modal Body ก่อนแสดงใหม่
    coach_arr.forEach((item, index) => {
        if (item.cert_level_id == certId || (certId==0 && item.cert_level_id==null)) {
            // สร้างโครงสร้าง HTML ที่ดูดีขึ้นด้วย Bootstrap Classes
            const coachHtml = `
                <div class="d-flex align-items-center p-3 mb-2 border rounded shadow-sm bg-light">
                    <div class="flex-shrink-0">
                        <img 
                            src="<?= base_url() ?>/${item.image_path}" 
                            onerror="this.src='${NO_PROFILE}'" 
                            class="rounded-circle border" 
                            style="width: 50px; height: 50px; object-fit: cover;"
                            alt="Coach Profile"
                        >
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h5 class="mb-0 fw-bold text-dark">
                            ${item.first_name} ${item.last_name}
                        </h5>
                        <small class="text-muted text-uppercase" style="font-size: 0.75rem;">Certified Coach</small>
                    </div>
                    <div class="ms-auto">
                        <i class="fa-solid fa-chevron-right text-secondary opacity-50"></i>
                    </div>
                </div>
            `;
            $(coachHtml).appendTo("#coachModalBody");
        }
    });

    $('#coachModal').modal('show');
});
</script>

<?php $this->endSection(); ?>
