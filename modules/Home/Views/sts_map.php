<?php $this->extend('template/home') ?>
<?php $this->section('content'); ?>

<section id="result-section" class="hero-section hero-khelo">

    <div id="map"></div>
</section>

<?php $this->endSection(); ?>

<?php $this->section('scripts'); ?>
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.css"/>
    <link rel="stylesheet" href="https://unpkg.com/leaflet.markercluster/dist/MarkerCluster.Default.css"/>

    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="https://unpkg.com/leaflet.markercluster/dist/leaflet.markercluster.js"></script>

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
        height:100vh; /* เปลี่ยนจาก min-height */
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
        /*padding:15px;*/
        font-family:'Kanit', sans-serif;
    }

    #map{
        //margin-top: 60px;
        width:100%;
        /*max-width:1200px;*/
        height:100%;
        /*border-radius:12px;*/
        box-shadow:0 15px 35px rgba(0,0,0,.35);
    }

        /* 📱 จอเล็ก (มือถือ) */
    @media (max-width:768px){
        #map{
            margin-top:0;
        }
    }

    body {
        margin:0;
        font-family: 'Segoe UI', sans-serif;
        background:#f4f8ff;
    }
    /* ===== โลโก้ ===== */
    .custom-marker { background:none; border:none; }

    .team-logo {
        width:48px;
        height:48px;
        border-radius:50%;
        overflow:hidden;
        border:3px solid #1565c0;
        background:#ffffff;
        box-shadow:0 6px 16px rgba(0,0,0,0.25);
        transition:0.2s ease;
    }

    .team-logo:hover{
        transform:scale(1.1);
    }

    .team-logo img{
        width:100%;
        height:100%;
        object-fit:contain;
    }

    /* ===== Cluster โทนน้ำเงิน ===== */
    .marker-cluster-small {
        background-color: rgba(33,150,243,0.5);
    }
    .marker-cluster-small div {
        background-color: rgba(33,150,243,0.9);
    }

    .marker-cluster-medium {
        background-color: rgba(25,118,210,0.5);
    }
    .marker-cluster-medium div {
        background-color: rgba(25,118,210,0.9);
    }

    .marker-cluster-large {
        background-color: rgba(13,71,161,0.5);
    }
    .marker-cluster-large div {
        background-color: rgba(13,71,161,0.9);
    }

    .marker-cluster div {
        color:#fff;
        font-weight:bold;
    }

    /* ===== popup ===== */
    .leaflet-popup-content-wrapper {
        border-radius:14px;
        background:#ffffff;
        box-shadow:0 8px 25px rgba(0,0,0,0.2);
    }

    .leaflet-popup-tip {
        background:#ffffff;
    }

    .team-popup h4{
        margin:0 0 6px;
        font-size:16px;
        color:#1565c0;
    }

    .team-stat {
        display:flex;
        justify-content:space-between;
        font-size:13px;
        margin-bottom:4px;
    }

    .leaflet-bottom.leaflet-right{
        margin-bottom:20px;
        margin-right:20px;
    }

</style>

<script>
    var base_url='<?php echo base_url();?>';
    const language = '<?=session()->get('language')?>';
    const DEFAULT_LOGO='<?= base_url('public/img/jootball_logo.png') ?>';
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
$(document).ready(function(){

    var map = L.map('map', {
        zoomControl: false
    }).setView([13.7563,100.5018], 6);

    L.control.zoom({
        position: 'bottomright'
    }).addTo(map);

    L.tileLayer(
        'https://{s}.basemaps.cartocdn.com/rastertiles/voyager/{z}/{x}/{y}{r}.png',
        { attribution: '&copy; OpenStreetMap & Carto' }
    ).addTo(map);

    var markers = L.markerClusterGroup({
        maxClusterRadius: 60
    });

    var defaultLogo = "https://jootball.com/public/img/jootball_logo.png";

    function random(min,max){
        return Math.floor(Math.random()*(max-min+1))+min;
    }

    // ✅ โหลดข้อมูลจาก ajax
    $.ajax({
        url: base_url + "/admin/tournament-pg/get-map-statistic",
        type: "GET",
        dataType: "json",
        success: function(response){

            if(!response.data || response.data.length === 0) return;

            markers.clearLayers();

            response.data.forEach(function(team){

                if(!team.latitude || !team.longitude) return;

                var matches = team.total_match;
                var coaches = team.total_coach;
                var players = team.total_player;

                var logoIcon = L.divIcon({
                    className: "custom-marker",
                    html: `
                        <div class="team-logo">
                            <img src="${team.logo ? `<?=base_url()?>/`+team.logo : DEFAULT_LOGO}" onerror="this.src='${DEFAULT_LOGO}'">
                        </div>
                    `,
                    iconSize: [48,48],
                    iconAnchor: [24,48]
                });

                var popupHTML = `
                    <div class="team-popup">
                        <h4>${team.name}</h4>
                        <hr>
                        <div class="team-stat" style="display:none"><span><?= lang('Common.games') ?></span><span>${matches}</span></div>
                        <div class="team-stat"><span><?= lang('Common.coaches') ?></span><span>${coaches}</span></div>
                        <div class="team-stat"><span><?= lang('Common.players') ?></span><span>${players}</span></div>
                    </div>
                `;

                var marker = L.marker([parseFloat(team.latitude), parseFloat(team.longitude)], { icon: logoIcon })
                    .bindPopup(popupHTML);

                markers.addLayer(marker);
            });

            map.addLayer(markers);

            var bounds = L.latLngBounds(
                response.data.map(t => [
                    parseFloat(t.latitude),
                    parseFloat(t.longitude)
                ])
            );
            map.fitBounds(bounds);

        },
        error: function(err){
            console.error("Load map data failed", err);
        }
    });

});
</script>

<?php $this->endSection(); ?>
