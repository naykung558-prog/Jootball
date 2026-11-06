<?php
/**
 * Popup แสดงรายการทั้งหมดพร้อมช่องค้นหา — ใช้ร่วมกันทุกตารางใน admin
 *
 * ปุ่มที่จะเปิด popup ต้องมี attribute:
 *   data-list-popup : JSON array ของ {name, label?, cls?}  (ชื่อเป็นค่าดิบ ไม่ต้อง escape)
 *   data-list-total : จำนวนรายการทั้งหมด
 *   data-list-title : หัวข้อ popup
 *
 * ค่าใน attribute ถูก escape ไว้ 1 ชั้นจากฝั่ง PHP เบราว์เซอร์ถอดให้เองตอนอ่าน attr()
 * จึงได้ชื่อดิบมาใช้ค้นหา แล้ว escape อีกทีตอน render
 */
?>
<script>
(function () {
    function escHtml(s) {
        return String(s ?? '').replace(/[&<>"']/g, c => ({
            '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;'
        }[c]));
    }

    const LANG = {
        search:   '<?= lang('Common.search') ?>',
        noResult: '<?= lang('Common.ag_no_result') ?>',
        close:    '<?= lang('Common.close') ?>',
    };

    function renderList(list) {
        if (!list.length) {
            return '<div class="text-muted fst-italic small">' + escHtml(LANG.noResult) + '</div>';
        }
        return list.map(function (it) {
            const badge = it.label
                ? '<span class="badge ' + escHtml(it.cls || 'bg-secondary-subtle text-secondary')
                  + ' me-1">' + escHtml(it.label) + '</span>'
                : '';
            return '<div class="mb-1 text-start">' + badge
                 + '<span class="small">' + escHtml(it.name) + '</span></div>';
        }).join('');
    }

    $(document).on('click', '[data-list-popup]', function () {
        let items = [];
        try {
            items = JSON.parse($(this).attr('data-list-popup')) || [];
        } catch (e) {
            return;
        }

        const title = $(this).attr('data-list-title') || '';
        const total = $(this).attr('data-list-total') || items.length;

        Swal.fire({
            title: title + ' (' + total + ')',
            width: 600,
            confirmButtonText: LANG.close,
            html:
                '<input type="text" id="listPopupSearch" class="form-control mb-3"'
              + ' placeholder="' + escHtml(LANG.search) + '" autocomplete="off">'
              + '<div id="listPopupBody" class="text-start" style="max-height:55vh;overflow-y:auto;">'
              + renderList(items) + '</div>',
            didOpen: function () {
                const $input = $('#listPopupSearch');
                $input.trigger('focus');
                $input.on('input', function () {
                    const q = this.value.trim().toLowerCase();
                    const filtered = q === '' ? items : items.filter(function (it) {
                        return String(it.name || '').toLowerCase().includes(q)
                            || String(it.label || '').toLowerCase().includes(q);
                    });
                    $('#listPopupBody').html(renderList(filtered));
                });
            }
        });
    });
})();
</script>
