<style>
    /* Stop page-level horizontal scroll on this page. This <style> block only loads
       on match_setup, so scoping it to html affects this page only. Needed because
       the overflowing element is position:fixed (e.g. the filter drawer/backdrop),
       which escapes any ancestor's overflow clip. */
    html {
        overflow-x: hidden;
    }
    #match-setup-root {
        overflow-x: clip;
    }
    /* Root cause: the filter drawer sits translated off the right edge when closed
       but still adds to the document width (visible as an empty gray panel in
       full-page captures). Drop it from layout entirely while fully closed; the
       open/close animation still runs via the .showing/.hiding states. */
    #matchFilterDrawer.offcanvas:not(.show):not(.showing):not(.hiding) {
        display: none !important;
    }

    :root {
        --bs-font-sans-serif: 'Sarabun', 'Plus Jakarta Sans', sans-serif;
        --theme-primary: var(--bs-primary);
        --theme-secondary: var(--bs-secondary);
        --theme-success: var(--bs-success);
        --theme-danger: var(--bs-danger);
        --theme-warning: var(--bs-warning);
        --card-shadow: var(--box-shadow);

        /* Match Setup Design Tokens (solid only — no gradients) */
        --ms-navy:        #16478D;
        --ms-navy-dark:   #103766;
        --ms-navy-soft:   #e3edfa;
        --ms-navy-border: #c5d6ee;
        --ms-date:        #16478D;
        --ms-date-dark:   #103766;
        --ms-time:        #0aa2c0;
        --ms-time-dark:   #08869f;
        --ms-stadium:     #198754;
        --ms-stadium-dark:#136a41;
        --ms-referee:     #d19e0f;
        --ms-referee-dark:#b88600;
        --ms-gold:        #D19E11;
        --ms-gold-dark:   #a37800;
        --ms-gold-soft:   #fdf4d8;
        --ms-gold-border: #f0d68a;
        --ms-bg:          #f8fafc;
        --ms-border:      #e2e8f0;
        --ms-text:        #1f2a44;
        --ms-muted:       #6b7585;
    }

    /* ============================================================
       Bulk Edit All Modal - Modern UI
       ============================================================ */
    .bulk-edit-all-modal .modal-content.bulk-edit-all-content {
        border-radius: 20px;
        border: none;
        overflow: hidden;
        box-shadow: 0 25px 60px -12px rgba(22, 71, 141, 0.35);
    }
    .bulk-edit-all-header {
        background: var(--ms-navy);
        border: none;
        padding: 1.5rem 1.75rem;
        color: #fff;
        position: relative;
    }
    .bulk-edit-all-header::after {
        content: '';
        position: absolute;
        right: -40px;
        top: -40px;
        width: 160px;
        height: 160px;
        background: rgba(255,255,255,0.08);
        border-radius: 50%;
        pointer-events: none;
    }
    .bulk-edit-all-icon {
        width: 44px;
        height: 44px;
        background: rgba(255,255,255,0.18);
        border-radius: 12px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.35rem;
        color: #fff;
        flex-shrink: 0;
    }
    .bulk-edit-all-modal .modal-title {
        font-size: 1.15rem;
        letter-spacing: 0.01em;
    }
    .bulk-edit-all-modal .btn-close-white {
        opacity: 0.85;
        position: relative;
        z-index: 2;
    }
    .bulk-edit-label {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-weight: 600;
        font-size: 0.875rem;
        color: #475569;
        margin-bottom: 0.5rem;
    }
    .bulk-edit-label > i {
        color: #16478D;
        font-size: 1rem;
    }
    .bulk-edit-input {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        padding: 0.7rem 0.95rem;
        font-size: 0.95rem;
        background-color: #fff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .bulk-edit-input:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.12);
        outline: none;
    }
    .bulk-stadium-group,
    .stadium-field-group {
        flex-wrap: nowrap;
    }
    .bulk-stadium-group > .select2-container,
    .stadium-field-group > .select2-container {
        flex: 1 1 auto;
        min-width: 0;
        max-width: 100%;
    }
    .bulk-add-stadium-btn {
        width: 44px;
        height: 44px;
        flex-shrink: 0;
        border-radius: 12px;
        border: 1px dashed #2a5298;
        color: #16478D;
        background: #f0f6ff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        transition: all 0.15s ease;
        padding: 0;
        line-height: 1;
    }
    .bulk-add-stadium-btn:hover {
        background: #16478D;
        color: #fff;
        border-color: #16478D;
        transform: translateY(-1px);
    }
    /* Inline edit form variant — slightly tighter to match 42px input height */
    .inline-edit-form .bulk-add-stadium-btn {
        width: 42px;
        height: 42px;
        border-radius: 10px;
    }
    .inline-edit-form .stadium-input {
        border-radius: 10px;
    }
    /* Match Select2 single height to inline form inputs */
    .inline-edit-form .select2-container--default .select2-selection--single {
        height: 42px;
        border-radius: 10px;
    }
    .inline-edit-form .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
    }
    .bulk-edit-notice {
        background: var(--ms-navy-soft);
        border: 1px solid var(--ms-navy-border);
        border-radius: 14px;
        padding: 0.95rem 1.1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.85rem;
    }
    .bulk-edit-notice-icon {
        width: 36px;
        height: 36px;
        background: var(--ms-navy);
        border-radius: 10px;
        color: #fff;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        flex-shrink: 0;
    }
    .bulk-edit-notice-title {
        color: #1e3c72;
        font-weight: 600;
        font-size: 0.95rem;
        line-height: 1.4;
    }
    .bulk-edit-notice-sub {
        color: #64748b;
        font-size: 0.82rem;
        margin-top: 2px;
    }
    .bulk-edit-footer {
        border: none;
        padding: 0 1.75rem 1.5rem;
        display: flex;
        justify-content: flex-end;
        gap: 0.75rem;
    }
    .bulk-edit-cancel-btn {
        border-radius: 12px;
        border: 1px solid #e2e8f0;
        color: #64748b;
        background: #fff;
        padding: 0.65rem 1.5rem;
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.15s ease;
    }
    .bulk-edit-cancel-btn:hover {
        background: #f1f5f9;
        color: #334155;
    }
    .bulk-edit-save-btn {
        border-radius: 12px;
        border: none;
        background: var(--ms-navy);
        color: #fff;
        padding: 0.65rem 1.75rem;
        font-weight: 600;
        font-size: 0.9rem;
        box-shadow: 0 6px 14px -4px rgba(22, 71, 141, 0.4);
        transition: all 0.15s ease;
    }
    .bulk-edit-save-btn:hover {
        background: var(--ms-navy-dark);
        color: #fff;
        transform: translateY(-1px);
        box-shadow: 0 8px 18px -4px rgba(22, 71, 141, 0.5);
    }

    /* Select2 - inside bulk-edit-all-modal */
    .bulk-edit-all-modal .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        height: 44px;
        padding: 0.3rem 0.6rem;
        background-color: #fff;
        transition: border-color 0.15s ease, box-shadow 0.15s ease;
    }
    .bulk-edit-all-modal .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: #212529;
        padding-left: 0.5rem;
        font-size: 0.95rem;
    }
    .bulk-edit-all-modal .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 42px;
        right: 8px;
    }
    .bulk-edit-all-modal .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #2a5298;
        box-shadow: 0 0 0 3px rgba(42, 82, 152, 0.12);
    }

    /* Datepicker - theme to navy */
    .datepicker.dropdown-menu {
        border: 1px solid #e2e8f0;
        border-radius: 14px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.12);
        padding: 8px;
        z-index: 1080;
        font-size: 0.85rem;
        max-width: 320px;
    }
    .datepicker.dropdown-menu table { width: 100%; }
    .datepicker.dropdown-menu table tr td,
    .datepicker.dropdown-menu table tr th {
        padding: 6px 4px;
        font-size: 0.85rem;
        text-align: center;
        line-height: 1.4;
    }
    .datepicker.dropdown-menu .datepicker-switch {
        font-weight: 600;
        color: var(--ms-navy);
    }
    .datepicker table tr td.active,
    .datepicker table tr td.active:hover,
    .datepicker table tr td.active.active,
    .datepicker table tr td.active.disabled,
    .datepicker table tr td.active.disabled:hover {
        background-image: none !important;
        background-color: var(--ms-navy) !important;
        border-color: var(--ms-navy) !important;
        color: #fff !important;
        text-shadow: none !important;
    }
    .datepicker table tr td.today,
    .datepicker table tr td.today:hover,
    .datepicker table tr td.today.disabled,
    .datepicker table tr td.today.disabled:hover {
        background: #e7f1ff !important;
        color: #16478D !important;
        border-color: #c9dffe !important;
    }
    .datepicker table tr td.day:hover,
    .datepicker table tr td.focused {
        background: #f0f6ff;
        color: #16478D;
    }

    /* Select2 - Stadium dropdown theme */
    .inline-edit-form .select2-container--default .select2-selection--single {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        height: 42px;
        padding: 0.25rem 0.5rem;
        background-color: #fff;
        transition: border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;
    }
    .inline-edit-form .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 30px;
        color: #212529;
        padding-left: 0.5rem;
        font-size: 0.95rem;
    }
    .inline-edit-form .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 40px;
        right: 8px;
    }
    .inline-edit-form .select2-container--default .select2-selection--single .select2-selection__placeholder {
        color: #94a3b8;
    }
    .inline-edit-form .select2-container--default .select2-selection--single .select2-selection__clear {
        margin-right: 4px;
        font-size: 1.1rem;
        color: #94a3b8;
    }
    .inline-edit-form .select2-container--default.select2-container--open .select2-selection--single {
        border-color: #86b7fe;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.15);
    }
    .select2-dropdown {
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        box-shadow: 0 4px 16px rgba(0,0,0,0.08);
        margin-top: 4px;
        overflow: hidden;
    }
    .select2-search--dropdown .select2-search__field {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }
    .select2-results__option {
        padding: 0.5rem 0.75rem;
        font-size: 0.9rem;
    }

    .stage-card {
        border: none;
        border-radius: var(--border-radius);
        box-shadow: var(--card-shadow);
        margin-bottom: 30px;
        transition: all 0.3s;
        background-color: #fff;
        overflow: hidden;
        position: relative;
    }

    .stage-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 30px rgba(0, 0, 0, 0.12);
    }

    .stage-header {
        background: var(--ms-navy);
        color: white;
        padding: 15px 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 12px rgba(22, 71, 141, 0.2);
        cursor: pointer;
    }

    .stage-header .ti-chevron-down {
        transition: transform 0.3s ease;
    }

    .stage-header.collapsed .ti-chevron-down {
        transform: rotate(-90deg);
    }

    .stage-number {
        background: var(--ms-gold);
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-weight: 700;
        margin-right: 15px;
        font-size: 1rem;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.15), 0 0 0 2px rgba(255, 255, 255, 0.25);
    }

    .round-section {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        margin-bottom: 25px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
    }

    .round-header-row {
        background: #f8f9fa;
        padding: 15px 20px;
        border-bottom: 1px solid #e9ecef;
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: var(--secondary-color);
        font-weight: 600;
    }

    .match-item {
        padding: 15px 20px;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        align-items: center;
        gap: 15px;
        transition: background-color 0.2s;
    }

    .match-item:last-child {
        border-bottom: none;
    }

    .match-item:hover {
        background-color: #f8f9fa;
    }

    .team-input-wrapper {
        flex: 1;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .list-input {
        border: 1px solid #dfe5ef;
        background: #fff;
        width: 100%;
        font-size: 0.95rem;
        padding: 10px 15px;
        border-radius: 8px;
        font-family: 'Kanit', sans-serif;
        transition: all 0.3s;
    }

    .list-input:focus {
        outline: none;
        border-color: var(--ms-navy);
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.12);
        background-color: #fff;
    }

    .list-input.winner-text {
        color: #fff;
        font-weight: bold;
        background-color: var(--bs-success);
        border-color: var(--bs-success);
    }

    .vs-badge {
        font-size: 0.85rem;
        font-weight: 700;
        color: #94a3b8;
        width: 40px;
        text-align: center;
        flex-shrink: 0;
        text-transform: uppercase;
    }

    .btn-winner-check {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        border: 2px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        color: #cbd5e1;
        background: white;
        transition: all 0.2s;
        flex-shrink: 0;
    }

    .btn-winner-check:hover {
        border-color: var(--bs-success);
        color: var(--bs-success);
        background-color: #f0fdf4;
    }

    .btn-winner-check.active {
        background: var(--bs-success);
        color: white;
        border-color: var(--bs-success);
        box-shadow: 0 4px 12px rgba(var(--bs-success-rgb), 0.3);
    }

    .score-input {
        width: 60px;
        text-align: center;
        border: 1px solid #dfe5ef;
        border-radius: 8px;
        font-weight: bold;
        font-family: 'Kanit', sans-serif;
        padding: 8px 4px;
        transition: all 0.3s;
        font-size: 1rem;
    }

    .score-input:focus {
        border-color: var(--ms-navy);
        outline: none;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.12);
    }

    .team-select {
        font-family: 'Kanit', sans-serif;
        padding: 10px 15px;
        transition: all 0.3s;
        border-radius: 8px;
        border: 1px solid #dfe5ef;
    }

    .team-select:focus {
        border-color: var(--ms-navy) !important;
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.12) !important;
    }

    .team-select:hover {
        border-color: #b1b9c8 !important;
    }

    .fade-in {
        animation: fadeIn 0.5s ease-out;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(20px);
        }

        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .btn-add-stage-wrapper {
        text-align: center;
        margin-top: 50px;
        padding: 30px;
        border-top: 1px dashed #e2e8f0;
    }

    /* ให้ scroll ในแนวนอนได้เมื่อเนื้อหาล้น */
    [id^="stage-matches-"] {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
        padding-bottom: 4px;
    }

    /* scrollbar style */
    [id^="stage-matches-"]::-webkit-scrollbar {
        height: 6px;
    }

    [id^="stage-matches-"]::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    [id^="stage-matches-"]::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    [id^="stage-matches-"]::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    /* กำหนด min-width ให้ match row ไม่หดจนเสียรูป */
    .match-row-wrapper {
        min-width: 540px;
    }

    /* ให้ส่วนรายชื่อทีม scroll ในแนวนอนได้เมื่อมีหลายกลุ่ม/สาย */
    .stage-team-inputs {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .stage-team-inputs::-webkit-scrollbar {
        height: 6px;
    }

    .stage-team-inputs::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 3px;
    }

    .stage-team-inputs::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 3px;
    }

    .stage-team-inputs::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }

    .match-card {
        transition: all 0.3s ease;
        border-radius: 12px !important;
        background: #fff;
        border: 1px solid #f1f5f9;
    }

    /* ============================================================
       Match Filter — drawer button + chips + badge
       ============================================================ */
    .match-filter-btn {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        padding: 9px 16px;
        background: #fff;
        border: 1px solid var(--ms-border);
        border-radius: 12px;
        color: var(--ms-text);
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.15s ease;
        position: relative;
    }
    .match-filter-btn > i { color: var(--ms-gold); font-size: 1.1rem; }
    .match-filter-btn:hover {
        border-color: var(--ms-gold);
        background: var(--ms-gold-soft);
        color: var(--ms-gold-dark);
    }
    .match-filter-btn .filter-count {
        background: var(--ms-gold);
        color: #fff;
        min-width: 20px;
        height: 20px;
        padding: 0 6px;
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        display: none;
        align-items: center;
        justify-content: center;
        margin-left: 4px;
    }
    .match-filter-btn.has-active .filter-count { display: inline-flex; }
    .match-filter-btn.has-active { border-color: var(--ms-gold); background: var(--ms-gold-soft); color: var(--ms-gold-dark); }

    /* Filter drawer */
    .filter-drawer.offcanvas {
        width: 420px;
        max-width: 100vw;
        z-index: 1075;
        border-left: 1px solid var(--ms-border);
        box-shadow: -8px 0 32px rgba(0, 0, 0, 0.18);
    }
    /* Backdrop — override admin_layout global hide for filter drawer only */
    body.filter-drawer-open .offcanvas-backdrop,
    body.filter-drawer-open .offcanvas-backdrop.show {
        display: block !important;
        z-index: 1070 !important;
        opacity: 0.5 !important;
        background-color: #000 !important;
        pointer-events: auto;
    }
    .filter-drawer .offcanvas-header {
        background: var(--ms-navy);
        color: #fff;
        padding: 1rem 1.25rem;
        border-bottom: 3px solid var(--ms-gold);
        position: sticky;
        top: 0;
        z-index: 5;
    }
    .filter-drawer .offcanvas-header .btn-close {
        filter: invert(1) brightness(2);
        opacity: 0.9;
        --bs-btn-close-bg: none;
    }
    .filter-drawer .offcanvas-title {
        font-weight: 700;
        font-size: 1.05rem;
        display: inline-flex;
        align-items: center;
        gap: 10px;
    }
    .filter-drawer .offcanvas-title > i {
        background: rgba(255,255,255,.18);
        color: #fff;
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.05rem;
    }
    .filter-drawer.offcanvas {
        display: flex;
        flex-direction: column;
        height: 100vh;
        height: 100dvh;
        max-height: 100vh;
        max-height: 100dvh;
        top: 0;
        bottom: 0;
    }
    .filter-drawer .offcanvas-header { flex-shrink: 0; }
    .filter-drawer .offcanvas-body {
        padding: 0;
        overflow-y: auto;
        position: relative;
        flex: 1 1 auto;
        min-height: 0;
    }
    .filter-section {
        padding: 1.1rem 1.25rem;
        border-bottom: 1px solid var(--ms-border);
    }
    .filter-section:last-of-type { border-bottom: none; }

    .filter-label {
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 700;
        font-size: 0.82rem;
        color: var(--ms-text);
        margin-bottom: 0.7rem;
    }
    .filter-label > i {
        color: var(--ms-gold);
        font-size: 1.05rem;
        background: var(--ms-gold-soft);
        width: 26px;
        height: 26px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    /* Search */
    .filter-search-wrap { position: relative; }
    .filter-search-wrap > i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--ms-muted);
        font-size: 1rem;
        pointer-events: none;
    }
    .filter-search-input {
        width: 100%;
        padding: 0.6rem 0.9rem 0.6rem 36px;
        border-radius: 10px;
        border: 1px solid var(--ms-border);
        font-size: 0.9rem;
        transition: border-color 0.15s;
        background: #fff;
    }
    .filter-search-input:focus {
        outline: none;
        border-color: var(--ms-navy);
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.12);
    }

    /* Chips */
    .filter-chips {
        display: flex;
        flex-wrap: wrap;
        gap: 6px;
    }
    .filter-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 6px 12px;
        border: 1px solid var(--ms-border);
        border-radius: 8px;
        background: #fff;
        font-size: 0.82rem;
        font-weight: 600;
        color: var(--ms-text);
        cursor: pointer;
        user-select: none;
        transition: all 0.12s;
        line-height: 1.3;
        max-width: 100%;
    }
    .filter-chip:hover {
        border-color: var(--ms-navy);
        color: var(--ms-navy);
        background: var(--ms-navy-soft);
    }
    .filter-chip.is-active {
        background: var(--ms-navy);
        color: #fff;
        border-color: var(--ms-navy);
        box-shadow: 0 2px 6px rgba(22, 71, 141, 0.2);
        padding-right: 8px;
    }
    /* X button shown on active chips (use × character) */
    .filter-chip.is-active::after,
    .filter-chip-square.is-active::after {
        content: "×";
        font-weight: 700;
        font-size: 0.95rem;
        margin-left: 4px;
        line-height: 1;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.25);
        color: #fff;
    }
    .filter-chip-square.is-active::after {
        position: absolute;
        top: -5px;
        right: -5px;
        margin-left: 0;
        background: var(--ms-gold);
        color: #fff;
        box-shadow: 0 1px 4px rgba(0, 0, 0, 0.25);
        font-size: 0.85rem;
        width: 18px;
        height: 18px;
    }
    .filter-chip-square { position: relative; }
    .filter-chip.chip-stage {
        max-width: 100%;
        gap: 6px;
    }
    .filter-chip.chip-stage .chip-stage-num {
        background: var(--ms-gold);
        color: #fff;
        min-width: 20px;
        height: 20px;
        padding: 0 5px;
        border-radius: 999px;
        font-size: 0.7rem;
        font-weight: 700;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    .filter-chip.chip-stage .chip-stage-label {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        max-width: 220px;
    }
    .filter-chip.chip-stage.is-active .chip-stage-num {
        background: #fff;
        color: var(--ms-navy);
    }

    /* Group chips → small square boxes */
    .filter-chips-grid {
        display: grid;
        grid-template-columns: repeat(8, 1fr);
        gap: 6px;
    }
    .filter-chip-square {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 100%;
        aspect-ratio: 1 / 1;
        border: 1px solid var(--ms-border);
        border-radius: 8px;
        background: #fff;
        font-size: 0.85rem;
        font-weight: 700;
        color: var(--ms-text);
        cursor: pointer;
        user-select: none;
        transition: all 0.12s;
    }
    .filter-chip-square:hover {
        border-color: var(--ms-navy);
        color: var(--ms-navy);
        background: var(--ms-navy-soft);
    }
    .filter-chip-square.is-active {
        background: var(--ms-navy);
        color: #fff;
        border-color: var(--ms-navy);
        box-shadow: 0 2px 6px rgba(22, 71, 141, 0.2);
    }

    /* Status chips with icon */
    .filter-chip.chip-status-pending > i { color: var(--ms-gold); }
    .filter-chip.chip-status-live > i    { color: #dc3545; }
    .filter-chip.chip-status-done > i    { color: var(--ms-stadium); }
    .filter-chip.chip-status-pending.is-active { background: var(--ms-gold); border-color: var(--ms-gold); color: #fff; }
    .filter-chip.chip-status-live.is-active    { background: #dc3545; border-color: #dc3545; color: #fff; }
    .filter-chip.chip-status-done.is-active    { background: var(--ms-stadium); border-color: var(--ms-stadium); color: #fff; }
    .filter-chip.chip-status-pending.is-active > i,
    .filter-chip.chip-status-live.is-active > i,
    .filter-chip.chip-status-done.is-active > i { color: #fff; }

    /* Date inputs */
    .filter-date-row { display: flex; gap: 8px; }
    .filter-date-row-stack { display: flex; flex-direction: column; gap: 12px; }
    .filter-date-field { display: flex; flex-direction: column; gap: 6px; }
    .filter-date-label {
        font-size: 0.78rem;
        font-weight: 600;
        color: var(--ms-muted);
        margin: 0;
    }
    .filter-date-col { flex: 1; position: relative; }
    .filter-date-col > i {
        position: absolute;
        left: 11px;
        top: 50%;
        transform: translateY(-50%);
        color: var(--ms-muted);
        font-size: 0.95rem;
        pointer-events: none;
    }
    .filter-date-col .form-control {
        border-radius: 10px;
        padding: 0.55rem 0.55rem 0.55rem 34px;
        font-size: 0.85rem;
        border: 1px solid var(--ms-border);
        background: #fff;
        width: 100%;
    }
    .filter-date-col .form-control:focus {
        border-color: var(--ms-navy);
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.12);
        outline: none;
    }

    /* Fixed footer below scrollable body */
    .filter-drawer-footer {
        flex-shrink: 0;
        background: #fff;
        padding: 0.9rem 1.25rem;
        border-top: 1px solid var(--ms-border);
        display: flex;
        gap: 8px;
        box-shadow: 0 -4px 12px rgba(0, 0, 0, 0.05);
        z-index: 6;
    }
    .btn-filter-reset {
        flex: 1;
        padding: 0.65rem;
        border-radius: 10px;
        border: 1px solid var(--ms-border);
        background: #fff;
        color: var(--ms-muted);
        font-weight: 600;
        font-size: 0.9rem;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
    }
    .btn-filter-reset:hover { background: #f1f5f9; color: var(--ms-text); }
    .btn-filter-apply {
        flex: 2;
        padding: 0.65rem;
        border-radius: 10px;
        border: none;
        background: var(--ms-navy);
        color: #fff;
        font-weight: 700;
        font-size: 0.9rem;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 6px;
        box-shadow: 0 3px 10px rgba(22, 71, 141, 0.25);
    }
    .btn-filter-apply:hover { background: var(--ms-navy-dark); }

    /* Empty filter message inside a stage */
    .filter-empty-msg {
        text-align: center;
        padding: 2rem 1rem;
        color: var(--ms-muted);
        font-size: 0.9rem;
    }
    .filter-empty-msg > i { font-size: 2rem; color: var(--ms-gold); display: block; margin-bottom: 8px; }

    /* Match header row: group badge + pair badge + select chip in one line */
    .match-meta-row {
        display: flex;
        align-items: center;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 10px;
    }

    /* Compact select chip */
    .match-select-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        padding: 4px 10px 4px 8px;
        border-radius: 999px;
        border: 1px solid var(--ms-border);
        background: #fff;
        cursor: pointer;
        user-select: none;
        transition: all 0.15s ease;
        font-size: 0.78rem;
        color: var(--ms-muted);
        font-weight: 600;
        line-height: 1.2;
        margin: 0;
    }
    .match-select-pill:hover {
        border-color: var(--ms-navy);
        color: var(--ms-navy);
        background: #fff;
    }
    .match-select-pill .form-check-input {
        margin: 0;
        width: 16px;
        height: 16px;
        flex-shrink: 0;
        cursor: pointer;
        border: 1.5px solid var(--ms-border);
    }
    .match-select-pill .form-check-input:checked {
        background-color: var(--ms-navy);
        border-color: var(--ms-navy);
    }
    .match-select-pill .form-check-input:focus {
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.15);
        border-color: var(--ms-navy);
    }
    .match-select-pill.is-checked {
        border-color: var(--ms-navy);
        color: var(--ms-navy);
        background: var(--ms-navy-soft);
    }

    /* Selectable match card — subtle selected state */
    .match-row-wrapper {
        position: relative;
    }
    .match-row-wrapper.is-selectable {
        cursor: pointer;
    }
    .match-row-wrapper.is-selected {
        background: #fafcff !important;
        border-color: var(--ms-navy-border) !important;
        border-left-width: 6px !important;
        box-shadow: 0 6px 20px rgba(22, 71, 141, 0.10) !important;
    }
    .match-row-wrapper.is-selectable:hover {
        box-shadow: 0 8px 24px rgba(22, 71, 141, 0.08) !important;
    }
    /* Floating check indicator at top-right when selected */
    .match-row-wrapper .selected-indicator {
        position: absolute;
        top: 10px;
        right: 12px;
        width: 22px;
        height: 22px;
        border-radius: 50%;
        background: var(--ms-navy);
        color: #fff;
        display: none;
        align-items: center;
        justify-content: center;
        font-size: 0.85rem;
        box-shadow: 0 2px 8px rgba(22, 71, 141, 0.4);
        pointer-events: none;
        z-index: 2;
    }
    .match-row-wrapper.is-selected .selected-indicator {
        display: inline-flex;
    }

    .match-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 30px rgba(22, 71, 141, 0.10) !important;
        border-color: var(--ms-navy);
    }

    .match-card .badge {
        font-size: 0.75rem;
        padding: 6px 12px;
        font-weight: 600;
        border-radius: 6px;
    }

    .match-card img {
        border: 2px solid #f8f9fa;
        padding: 2px;
        background: white;
        border-radius: 50%;
    }

    /* Floating Edit Panel (Centered) */
    #floating-edit-panel {
        position: fixed;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -45%) scale(0.95);
        width: 400px;
        background: rgba(255, 255, 255, 0.98);
        backdrop-filter: blur(15px);
        box-shadow: 0 20px 60px rgba(0,0,0,0.25);
        z-index: 9999;
        transition: all 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
        border-radius: 24px;
        border: 1px solid rgba(22, 71, 141, 0.15);
        overflow: hidden;
        opacity: 0;
        pointer-events: none;
    }

    #floating-edit-panel.active {
        opacity: 1;
        pointer-events: all;
        transform: translate(-50%, -50%) scale(1);
    }

    #floating-edit-panel .panel-header {
        background: var(--ms-navy);
        color: #fff;
        padding: 1.25rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    #floating-edit-panel .panel-body {
        padding: 1.5rem;
    }

    #floating-edit-panel .panel-footer {
        padding: 1.25rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        background: #fafafa;
    }

    #floating-edit-panel .form-label {
        color: #475569;
        font-size: 0.875rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    #floating-edit-panel .input-group-text {
        background-color: #f8fafc;
        border-color: #e2e8f0;
        color: #64748b;
    }

    #floating-edit-panel .form-control {
        border-color: #e2e8f0;
        padding: 0.625rem 1rem;
    }

    #floating-edit-panel .form-control:focus {
        border-color: #16478D;
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.15);
    }

    /* Bulk Edit Button Area (In-flow) */
    #bulk-edit-btn-container {
        display: none;
        opacity: 0;
        transform: translateY(-10px);
        transition: all 0.3s ease;
        margin-bottom: 20px;
    }

    #bulk-edit-btn-container.active {
        display: block;
        opacity: 1;
        transform: translateY(0);
    }

    /* Bulk Edit Toolbar — unified card */
    .bulk-edit-toolbar {
        background: #fff;
        border: 1px solid var(--ms-border);
        border-radius: 14px;
        padding: 18px 20px;
        box-shadow: 0 2px 10px rgba(15, 23, 42, 0.04);
    }

    .bulk-edit-toolbar-head {
        display: flex;
        align-items: center;
        gap: 12px;
        padding-bottom: 14px;
        margin-bottom: 14px;
        border-bottom: 1px solid var(--ms-border);
    }

    .bulk-edit-toolbar-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: var(--ms-gold-soft);
        color: var(--ms-gold);
        border: 1px solid var(--ms-gold-border);
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        flex-shrink: 0;
    }

    .bulk-edit-toolbar-title {
        margin: 0;
        font-weight: 700;
        color: var(--ms-navy);
        font-size: 1rem;
    }

    .bulk-edit-toolbar-sub {
        margin: 2px 0 0;
        font-size: 0.8rem;
        color: var(--ms-muted);
    }

    .bulk-edit-toolbar-actions {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        align-items: center;
    }

    .bulk-edit-toolbar-actions .divider {
        width: 1px;
        align-self: stretch;
        background: var(--ms-border);
        margin: 0 4px;
    }

    /* Field pill buttons — outline default, fill on hover */
    .btn-bulk-edit {
        padding: 7px 14px;
        border-radius: 999px;
        font-weight: 600;
        background: #fff;
        border: 1px solid var(--ms-border);
        display: inline-flex;
        align-items: center;
        gap: 6px;
        transition: all 0.15s ease;
        cursor: pointer;
        font-size: 0.875rem;
        color: var(--ms-text);
        line-height: 1.3;
    }

    .btn-bulk-edit > i { font-size: 1rem; }

    .btn-bulk-edit .count-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 22px;
        height: 20px;
        padding: 0 6px;
        background: var(--ms-bg);
        border-radius: 999px;
        font-size: 0.72rem;
        font-weight: 700;
        margin-left: 4px;
        color: var(--ms-muted);
    }

    .btn-bulk-date    { border-color: rgba(22, 71, 141, .35); }
    .btn-bulk-date > i { color: var(--ms-date); }
    .btn-bulk-date:hover { background: var(--ms-date); color: #fff; border-color: var(--ms-date); }
    .btn-bulk-date:hover > i { color: #fff; }
    .btn-bulk-date:hover .count-pill { background: rgba(255,255,255,.2); color: #fff; }

    .btn-bulk-time    { border-color: rgba(10, 162, 192, .35); }
    .btn-bulk-time > i { color: var(--ms-time); }
    .btn-bulk-time:hover { background: var(--ms-time); color: #fff; border-color: var(--ms-time); }
    .btn-bulk-time:hover > i { color: #fff; }
    .btn-bulk-time:hover .count-pill { background: rgba(255,255,255,.2); color: #fff; }

    .btn-bulk-stadium { border-color: rgba(25, 135, 84, .35); }
    .btn-bulk-stadium > i { color: var(--ms-stadium); }
    .btn-bulk-stadium:hover { background: var(--ms-stadium); color: #fff; border-color: var(--ms-stadium); }
    .btn-bulk-stadium:hover > i { color: #fff; }
    .btn-bulk-stadium:hover .count-pill { background: rgba(255,255,255,.2); color: #fff; }

    .btn-bulk-referee { border-color: rgba(209, 158, 15, .35); }
    .btn-bulk-referee > i { color: var(--ms-referee); }
    .btn-bulk-referee:hover { background: var(--ms-referee); color: #fff; border-color: var(--ms-referee); }
    .btn-bulk-referee:hover > i { color: #fff; }
    .btn-bulk-referee:hover .count-pill { background: rgba(255,255,255,.2); color: #fff; }

    /* Primary "Edit All" button — solid navy, stands out */
    .btn-bulk-all {
        background: var(--ms-navy);
        color: #fff;
        border-color: var(--ms-navy);
        box-shadow: 0 3px 10px rgba(22, 71, 141, 0.25);
    }
    .btn-bulk-all > i { color: #fff; }
    .btn-bulk-all .count-pill { background: rgba(255,255,255,.22); color: #fff; }
    .btn-bulk-all:hover {
        background: var(--ms-navy-dark);
        color: #fff;
        border-color: var(--ms-navy-dark);
        transform: translateY(-1px);
        box-shadow: 0 5px 14px rgba(22, 71, 141, 0.35);
    }

    .btn-bulk-edit:active { transform: translateY(0); }

    /* DELETE_ME_START */
    @media (max-width: 1024px) {
        .modal-dialog {
            display: flex;
            align-items: center;
            min-height: calc(100% - 3.5rem);
            margin: 1.75rem auto !important;
            max-width: 95% !important;
        }
        .modal-content {
            max-height: 90vh !important;
            overflow: hidden !important;
            display: flex;
            flex-direction: column !important;
            border-radius: 12px !important;
        }
        .modal-body {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
        }
    }

    /* --- Global Modal Fix (v3) --- */
    @media (max-width: 1024px) {
        /* ทำงานเฉพาะตอนที่ Modal ถูกสั่งแสดงผลแล้วเท่านั้น เพื่อไม่ให้ขวางจังหวะกด */
        .modal.show .modal-dialog {
            display: flex !important;
            align-items: center !important;
            min-height: calc(100% - 3.5rem) !important;
            margin: 1.75rem auto !important;
            max-width: 95% !important;
        }
        .modal-content {
            max-height: 85vh !important;
            display: flex !important;
            flex-direction: column !important;
            overflow: hidden !important;
        }
        .modal-body {
            overflow-y: auto !important;
            -webkit-overflow-scrolling: touch !important;
            flex: 1 1 auto !important;
        }
    }
</style>
