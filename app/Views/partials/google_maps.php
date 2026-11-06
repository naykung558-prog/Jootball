<?php if (!empty($_ENV['GOOGLE_MAP_KEY'])): ?>
<script>
var __gm = { loaded: false, loading: false, cbs: [] };
var __gmLang = {
    exceededTitle: '<?= lang("Common.map_quota_exceeded_title") ?>',
    exceededText: '<?= lang("Common.map_quota_exceeded_text") ?>',
    warningTitle: '<?= lang("Common.map_quota_warning_title") ?>',
    warningText: '<?= lang("Common.map_quota_warning_text") ?>'
};
function loadGoogleMapsAPI(callback) {
    if (__gm.loaded) { if (callback) callback(); return; }
    if (callback) __gm.cbs.push(callback);
    if (__gm.loading) return;
    __gm.loading = true;

    $.post('<?= base_url("maps-quota/check") ?>', { page: location.pathname }, function(data) {
        if (!data.allowed) {
            __gm.loading = false;
            __gm.cbs = [];
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'warning',
                    title: __gmLang.exceededTitle,
                    text: __gmLang.exceededText.replace('%s', data.count).replace('%s', data.limit),
                    confirmButtonColor: '#16478d'
                });
            }
            return;
        }
        // Warn at 80%
        if (data.warning && typeof Swal !== 'undefined') {
            Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 5000 }).fire({
                icon: 'info',
                title: __gmLang.warningTitle,
                text: __gmLang.warningText.replace('%s', data.count).replace('%s', data.limit)
            });
        }
        // Load Maps API
        window.__onGoogleMapsReady = function() {
            __gm.loaded = true;
            __gm.loading = false;
            __gm.cbs.forEach(function(cb) { cb(); });
            __gm.cbs = [];
        };
        var s = document.createElement('script');
        s.src = 'https://maps.googleapis.com/maps/api/js?key=<?= $_ENV['GOOGLE_MAP_KEY'] ?>&libraries=places&callback=__onGoogleMapsReady&language=th&region=TH';
        s.async = true;
        document.head.appendChild(s);
    }).fail(function() {
        __gm.loading = false;
    });
}
</script>
<?php endif; ?>
