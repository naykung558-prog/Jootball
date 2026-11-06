<?php
// Toggle the maintenance popup on/off. Set to true to show it again.
$showMaintenanceAnnounce = false;
if (!$showMaintenanceAnnounce) { return; }
?>
<!-- Maintenance announcement popup (floating overlay, shows on every page load) -->
<div id="jb-announce-backdrop" class="jb-announce-backdrop">
    <div id="jb-announce" class="jb-announce" role="alertdialog" aria-labelledby="jb-announce-title">
        <button type="button" class="jb-announce-close" id="jb-announce-close" aria-label="Dismiss">&times;</button>
        <div class="jb-announce-inner">
            <div class="jb-announce-icon">⚠️</div>
            <div class="jb-announce-title" id="jb-announce-title">MAINTENANCE IN PROGRESS</div>
            <div class="jb-announce-sub">
                The system is currently under maintenance.<br>
                <strong>Please do NOT add or edit any data</strong> &mdash; any changes may be lost.
            </div>
            <button type="button" class="jb-announce-ok" id="jb-announce-ok">I understand</button>
        </div>
    </div>
</div>

<style>
    .jb-announce-backdrop{
        position:fixed;inset:0;z-index:2147483000;
        display:flex;align-items:center;justify-content:center;padding:20px;
        background:rgba(15,15,20,.55);backdrop-filter:blur(4px);-webkit-backdrop-filter:blur(4px);
        opacity:0;visibility:hidden;transition:opacity .35s ease, visibility .35s ease;
    }
    .jb-announce-backdrop.jb-show{opacity:1;visibility:visible;}
    .jb-announce{
        position:relative;width:min(720px,92vw);min-height:50vh;
        display:flex;align-items:center;justify-content:center;
        background:linear-gradient(135deg,#B91C1C 0%,#7F1D1D 100%);
        color:#fff;font-family:'Kanit',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,sans-serif;
        border-radius:20px;border:3px solid #FDE047;
        box-shadow:0 30px 80px rgba(0,0,0,.5);
        transform:translateY(28px) scale(.94);opacity:0;
        transition:transform .4s cubic-bezier(.16,1,.3,1), opacity .4s ease;
    }
    .jb-announce-backdrop.jb-show .jb-announce{transform:translateY(0) scale(1);opacity:1;}
    .jb-announce-inner{width:100%;text-align:center;padding:40px 28px;}
    .jb-announce-icon{
        font-size:88px;line-height:1;margin-bottom:14px;
        animation:jb-blink 1.2s ease-in-out infinite;
    }
    .jb-announce-title{
        font-size:clamp(30px,5.5vw,56px);font-weight:800;letter-spacing:1px;color:#FDE047;
        text-transform:uppercase;line-height:1.1;margin-bottom:16px;
        text-shadow:0 2px 8px rgba(0,0,0,.35);
    }
    .jb-announce-sub{font-size:clamp(17px,2.4vw,24px);line-height:1.5;font-weight:500;}
    .jb-announce-sub strong{color:#FDE047;}
    .jb-announce-ok{
        margin-top:26px;background:#FDE047;color:#7F1D1D;border:0;
        font-family:inherit;font-weight:700;font-size:18px;
        padding:12px 34px;border-radius:999px;cursor:pointer;
        transition:transform .15s ease, box-shadow .2s ease;
        box-shadow:0 6px 18px rgba(0,0,0,.25);
    }
    .jb-announce-ok:hover{transform:translateY(-2px);box-shadow:0 10px 24px rgba(0,0,0,.35);}
    .jb-announce-close{
        position:absolute;right:16px;top:16px;
        background:rgba(255,255,255,.18);border:0;color:#fff;
        width:44px;height:44px;border-radius:50%;font-size:30px;line-height:1;
        cursor:pointer;display:flex;align-items:center;justify-content:center;
        transition:background .2s;z-index:2;
    }
    .jb-announce-close:hover{background:rgba(255,255,255,.35);}
    @keyframes jb-blink{0%,100%{opacity:1;transform:scale(1);}50%{opacity:.55;transform:scale(1.1);}}
    @media (max-width:576px){
        .jb-announce{min-height:56vh;}
        .jb-announce-icon{font-size:64px;}
        .jb-announce-close{width:38px;height:38px;font-size:26px;right:10px;top:10px;}
    }
</style>

<script>
    (function () {
        var back = document.getElementById('jb-announce-backdrop');
        if (!back) return;
        var closeEls = [document.getElementById('jb-announce-close'), document.getElementById('jb-announce-ok')];

        // Smooth entrance on load (reappears on every page change — not remembered).
        requestAnimationFrame(function () {
            requestAnimationFrame(function () { back.classList.add('jb-show'); });
        });

        function dismiss() {
            back.classList.remove('jb-show');
            setTimeout(function () { if (back && back.parentNode) back.parentNode.removeChild(back); }, 400);
        }
        closeEls.forEach(function (el) { if (el) el.addEventListener('click', dismiss); });
        // Click on the dimmed backdrop (outside the card) also closes it.
        back.addEventListener('click', function (e) { if (e.target === back) dismiss(); });
    })();
</script>
