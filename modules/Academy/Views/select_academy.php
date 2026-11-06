<?= $this->extend('template/layout') ?>
<?= $this->section('content') ?>

<div class="container py-4">
    <h2 class="mb-4">เลือก Academy ที่ต้องการจัดการ</h2>

    <?php if (session()->getFlashdata('error')): ?>
        <div class="alert alert-danger"><?= esc(session()->getFlashdata('error')) ?></div>
    <?php endif; ?>

    <div class="mb-3">
        <input type="text" id="academySearch" class="form-control form-control-lg"
               placeholder="🔎 ค้นหา academy ตามชื่อ...">
        <small id="searchStatus" class="text-muted"></small>
    </div>

    <!-- Inline render initial 189 cards (เร็วเมื่อ no-query) — AJAX จะ replace ตอนพิมพ์ -->
    <div id="academyGrid" class="row g-3">
        <?php foreach ($academies as $a): ?>
            <div class="col-md-4 col-lg-3 academy-card-wrap">
                <form method="POST" action="<?= base_url('academy/switch') ?>">
                    <?= csrf_field() ?>
                    <input type="hidden" name="academy_id" value="<?= (int)$a['id'] ?>">
                    <button type="submit" class="card w-100 text-start border-0 shadow-sm <?= $a['id'] == $current_academy_id ? 'border-primary border-2' : '' ?>"
                            style="cursor:pointer; background:white;">
                        <div class="card-body">
                            <?php if (!empty($a['logo'])): ?>
                                <img src="<?= base_url($a['logo']) ?>" class="rounded mb-2" style="width:48px;height:48px;object-fit:cover" onerror="this.onerror=null;this.src='<?= base_url('public/img/jootball_logo.png') ?>'">
                            <?php else: ?>
                                <div class="rounded mb-2 bg-light d-flex align-items-center justify-content-center"
                                     style="width:48px;height:48px;font-size:20px">🏫</div>
                            <?php endif; ?>
                            <h6 class="mb-1"><?= esc($a['name']) ?></h6>
                            <small class="text-muted">
                                ทีม <?= (int)$a['team_count'] ?> · สมาชิก <?= (int)$a['member_count'] ?>
                            </small>
                        </div>
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>

    <div id="noResult" class="text-muted text-center py-5" style="display:none">ไม่พบ academy</div>
</div>

<script>
(function(){
    const input  = document.getElementById('academySearch');
    const grid   = document.getElementById('academyGrid');
    const empty  = document.getElementById('noResult');
    const status = document.getElementById('searchStatus');
    const SEARCH_URL = '<?= base_url('academy/select-search') ?>';
    const SWITCH_URL = '<?= base_url('academy/switch') ?>';
    const CSRF_NAME  = '<?= csrf_token() ?>';
    const CURRENT_ID = <?= (int)$current_academy_id ?>;
    // base_url() ตัดสแลชท้ายทิ้ง ส่วน path ใน DB ก็ไม่มีสแลชนำหน้า
    // ต่อกันตรง ๆ จะได้ ".../jootballpublic/uploads/..." จึงต้องเติมสแลชคั่นเอง
    const BASE_URL     = '<?= rtrim(base_url(), '/') ?>/';
    const DEFAULT_LOGO = '<?= base_url('public/img/jootball_logo.png') ?>';

    if (!input) return;

    let timer = null;
    let lastQuery = '';

    function escapeHtml(s) {
        return String(s).replace(/[&<>"']/g, c => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c]));
    }

    // path ที่เก็บใน DB เป็น relative (public/uploads/...) แต่เผื่อเจอ URL เต็มด้วย
    function logoUrl(path) {
        return /^https?:\/\//i.test(path)
            ? path
            : BASE_URL + String(path).replace(/^\/+/, '');
    }

    function renderCard(a, csrfHash) {
        const isCurrent = (a.id == CURRENT_ID);
        const logo = a.logo
            ? `<img src="${escapeHtml(logoUrl(a.logo))}" class="rounded mb-2" style="width:48px;height:48px;object-fit:cover" onerror="this.onerror=null;this.src='${DEFAULT_LOGO}'">`
            : `<div class="rounded mb-2 bg-light d-flex align-items-center justify-content-center" style="width:48px;height:48px;font-size:20px">🏫</div>`;
        return `
        <div class="col-md-4 col-lg-3 academy-card-wrap">
            <form method="POST" action="${SWITCH_URL}">
                <input type="hidden" name="${CSRF_NAME}" value="${escapeHtml(csrfHash)}">
                <input type="hidden" name="academy_id" value="${a.id}">
                <button type="submit" class="card w-100 text-start border-0 shadow-sm ${isCurrent ? 'border-primary border-2' : ''}"
                        style="cursor:pointer; background:white;">
                    <div class="card-body">
                        ${logo}
                        <h6 class="mb-1">${escapeHtml(a.name)}</h6>
                    </div>
                </button>
            </form>
        </div>`;
    }

    async function doSearch(q) {
        if (q === lastQuery) return;
        lastQuery = q;
        status.textContent = 'กำลังค้นหา...';
        try {
            const r = await fetch(SEARCH_URL + '?q=' + encodeURIComponent(q), {credentials: 'same-origin'});
            if (!r.ok) throw new Error('HTTP ' + r.status);
            const json = await r.json();
            const csrfHash = '<?= csrf_hash() ?>';  // refresh ทุก search (CSRF token regenerate)
            const html = (json.data || []).map(a => renderCard(a, csrfHash)).join('');
            grid.innerHTML = html;
            empty.style.display = (json.data && json.data.length) ? 'none' : '';
            status.textContent = (json.data || []).length + ' รายการ' + (q !== '' ? ' (สำหรับ "' + q + '")' : '');
        } catch (e) {
            status.textContent = 'ค้นหาไม่สำเร็จ: ' + e.message;
        }
    }

    input.addEventListener('input', function() {
        clearTimeout(timer);
        const q = this.value.trim();
        timer = setTimeout(() => doSearch(q), 300);  // debounce 300ms
    });
})();
</script>

<?= $this->endSection() ?>
