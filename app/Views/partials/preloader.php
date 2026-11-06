<!-- Jootball page loader -->
<div id="jootball-preloader" aria-hidden="true">
    <div class="jb-loader-box">
        <div class="jb-logo-wrap">
            <span class="jb-ring"></span>
            <img src="<?= base_url('public/img/jootball_logo.png') ?>" alt="Jootball" class="jb-logo">
        </div>
        <div class="jb-dots"><span></span><span></span><span></span></div>
    </div>
</div>

<style>
    #jootball-preloader{
        position:fixed;inset:0;z-index:2147483647;
        display:flex;align-items:center;justify-content:center;
        background:#ffffff;
        transition:opacity .45s ease, visibility .45s ease;
    }
    #jootball-preloader.jb-hide{opacity:0;visibility:hidden;pointer-events:none;}
    .jb-loader-box{display:flex;flex-direction:column;align-items:center;gap:22px;}
    .jb-logo-wrap{position:relative;width:110px;height:110px;display:flex;align-items:center;justify-content:center;}
    .jb-logo{
        width:78px;height:78px;object-fit:contain;
        animation:jb-pulse 1.4s ease-in-out infinite;
    }
    .jb-ring{
        position:absolute;inset:0;border-radius:50%;
        border:4px solid rgba(22,71,140,.15);
        border-top-color:#16478C;
        border-right-color:#F2B81B;
        animation:jb-spin 1s linear infinite;
    }
    .jb-dots{display:flex;gap:8px;}
    .jb-dots span{
        width:9px;height:9px;border-radius:50%;background:#16478C;
        opacity:.35;animation:jb-bounce 1.2s ease-in-out infinite;
    }
    .jb-dots span:nth-child(2){animation-delay:.2s;background:#F2B81B;}
    .jb-dots span:nth-child(3){animation-delay:.4s;}
    @keyframes jb-spin{to{transform:rotate(360deg);}}
    @keyframes jb-pulse{0%,100%{transform:scale(1);}50%{transform:scale(1.12);}}
    @keyframes jb-bounce{0%,100%{opacity:.3;transform:translateY(0);}50%{opacity:1;transform:translateY(-6px);}}
    @media (prefers-color-scheme:dark){
        #jootball-preloader{background:#111827;}
        .jb-ring{border-color:rgba(255,255,255,.12);border-top-color:#F2B81B;border-right-color:#16478C;}
    }
</style>

<script>
    (function () {
        var el = document.getElementById('jootball-preloader');
        if (!el) return;
        var hidden = false;
        function hide() {
            if (hidden) return;
            hidden = true;
            el.classList.add('jb-hide');
            setTimeout(function () { if (el && el.parentNode) el.parentNode.removeChild(el); }, 500);
        }
        // Hide once the page has fully loaded...
        if (document.readyState === 'complete') {
            hide();
        } else {
            window.addEventListener('load', hide);
        }
        // ...and a safety timeout so it never gets stuck.
        setTimeout(hide, 6000);
    })();
</script>
