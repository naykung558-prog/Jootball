<style>
    :root {
        --bs-font-sans-serif: 'Sarabun', 'Plus Jakarta Sans', sans-serif;
        --theme-primary: var(--bs-primary);
        --theme-success: var(--bs-success);
        --theme-danger: var(--bs-danger);
        --theme-warning: var(--bs-warning);
        --card-shadow: var(--box-shadow);
    }

    .round-section {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        margin-bottom: 25px;
        overflow: hidden;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.03);
    }

    .round-header {
        background: linear-gradient(135deg, #16478D 0%, #1e5bb5 100%);
        color: white;
        padding: 15px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        transition: all 0.3s;
    }

    .round-header:hover {
        background: linear-gradient(135deg, #1e5bb5 0%, #16478D 100%);
    }

    .round-header .ti-chevron-down {
        transition: transform 0.3s ease;
    }

    .round-header.collapsed .ti-chevron-down {
        transform: rotate(-90deg);
    }

    .match-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        margin: 15px;
        padding: 20px;
        transition: all 0.3s;
    }

    .match-card:hover {
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
    }

    .match-card.match-ended {
        background: #f8fdf9;
        border-color: #28a745;
    }

    .team-display {
        display: flex;
        align-items: center;
        gap: 12px;
        flex: 1;
    }

    .team-logo {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        object-fit: cover;
        border: 2px solid #e9ecef;
        background: #fff;
    }

    .team-name {
        font-weight: 600;
        font-size: 1rem;
        color: #333;
    }

    .team-name.winner {
        color: #28a745;
    }

    .score-input-wrapper {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .score-input {
        width: 60px;
        height: 50px;
        text-align: center;
        font-size: 1.5rem;
        font-weight: bold;
        border: 2px solid #e9ecef;
        border-radius: 10px;
        transition: all 0.3s;
    }

    .score-input:focus {
        border-color: #16478D;
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.1);
        outline: none;
    }

    .vs-badge {
        font-size: 1.2rem;
        font-weight: 700;
        color: #94a3b8;
        padding: 0 15px;
    }

    .penalty-section {
        background: #fff3cd;
        border: 1px dashed #ffc107;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        display: none;
    }

    .penalty-section.show {
        display: block;
    }

    .penalty-label {
        font-size: 0.85rem;
        color: #856404;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .penalty-input {
        width: 50px;
        height: 40px;
        text-align: center;
        font-size: 1.2rem;
        font-weight: bold;
        border: 2px solid #ffc107;
        border-radius: 8px;
        background: #fff;
    }

    .winner-select-section {
        background: #d4edda;
        border: 1px dashed #28a745;
        border-radius: 8px;
        padding: 15px;
        margin-top: 15px;
        display: none;
    }

    .winner-select-section.show {
        display: block;
    }

    .winner-label {
        font-size: 0.85rem;
        color: #155724;
        font-weight: 600;
        margin-bottom: 10px;
    }

    .match-status-badge {
        font-size: 0.75rem;
        padding: 4px 12px;
        border-radius: 20px;
        font-weight: 600;
    }

    .status-live {
        background: linear-gradient(135deg, #dc3545 0%, #ff6b6b 100%);
        color: #fff;
        animation: pulse 2s infinite;
    }

    .status-ended {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        color: #fff;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.7;
        }
    }

    .team-name.winner {
        color: #28a745;
        font-weight: 700;
    }

    .score-input.score-readonly,
    .penalty-input.score-readonly {
        background: #e9ecef;
        color: #495057;
        cursor: not-allowed;
    }

    .action-buttons {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        justify-content: flex-end;
    }

    .btn-save-score {
        background: #6c757d;
        border: none;
        color: #fff;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-save-score:hover {
        background: #5a6268;
        color: #fff;
    }

    .btn-end-match {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        color: #fff;
        padding: 8px 20px;
        border-radius: 8px;
    }

    .btn-export-result {
        background: linear-gradient(135deg, #17a2b8 0%, #138496 100%);
        border: none;
        color: #fff;
        padding: 8px 20px;
        border-radius: 8px;
        font-weight: 600;
        transition: all 0.3s;
        text-decoration: none;
    }

    .btn-export-result:hover {
        background: linear-gradient(135deg, #138496 0%, #117a8b 100%);
        color: #fff;
        transform: translateY(-1px);
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-end-match:hover {
        background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
        color: #fff;
        transform: translateY(-2px);
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        margin-bottom: 20px;
        opacity: 0.5;
    }

    /* Lineup Section Styles */
    .lineup-section {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-top: 15px;
        overflow: hidden;
    }

    .lineup-header {
        background: #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 600;
        color: #495057;
    }

    .lineup-header:hover {
        background: #dee2e6;
    }

    .lineup-content {
        padding: 15px;
    }

    .lineup-team {
        margin-bottom: 10px;
    }

    .lineup-team-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #333;
        margin-bottom: 5px;
    }

    .lineup-players {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .player-badge {
        display: inline-flex;
        align-items: center;
        padding: 3px 8px;
        border-radius: 15px;
        font-size: 0.75rem;
        background: #fff;
        border: 1px solid #dee2e6;
    }

    .player-badge.starter {
        background: #d4edda;
        border-color: #28a745;
        color: #155724;
    }

    .player-badge.substitute {
        background: #fff3cd;
        border-color: #ffc107;
        color: #856404;
    }

    .player-number {
        font-weight: 700;
        margin-right: 5px;
    }

    /* Events Section Styles */
    .events-section {
        background: #f8f9fa;
        border: 1px solid #e9ecef;
        border-radius: 8px;
        margin-top: 10px;
        overflow: hidden;
    }

    .events-header {
        background: #e9ecef;
        padding: 10px 15px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        cursor: pointer;
        font-weight: 600;
        color: #495057;
    }

    .events-header:hover {
        background: #dee2e6;
    }

    .events-content {
        padding: 15px;
        max-height: 250px;
        overflow-y: auto;
    }

    .event-item {
        display: flex;
        align-items: center;
        padding: 8px 10px;
        border-bottom: 1px solid #e9ecef;
        gap: 10px;
    }

    .event-item:last-child {
        border-bottom: none;
    }

    .event-minute {
        font-weight: 700;
        font-size: 0.85rem;
        color: #16478D;
        min-width: 35px;
    }

    .event-icon {
        font-size: 1.1rem;
        min-width: 25px;
        text-align: center;
    }

    .event-details {
        flex: 1;
        font-size: 0.85rem;
    }

    .event-team {
        font-size: 0.75rem;
        color: #6c757d;
        margin-left: auto;
    }

    .event-delete {
        color: #dc3545;
        cursor: pointer;
        opacity: 0.6;
    }

    .event-delete:hover {
        opacity: 1;
    }

    .no-events {
        text-align: center;
        color: #6c757d;
        font-size: 0.85rem;
        padding: 20px;
    }

    /* Modal Styles */
    .player-select-table {
        font-size: 0.85rem;
    }

    .player-select-table th {
        background: #f8f9fa;
        font-weight: 600;
    }

    .player-select-table td {
        vertical-align: middle;
    }

    .btn-sm-action {
        padding: 4px 10px;
        font-size: 0.75rem;
    }

    /* Page-specific Micromodal Styling */
    .micromodal {
        position: fixed;
        inset: 0;
        z-index: 1080;
        display: none;
    }

    .micromodal.is-open {
        display: block;
    }

    .micromodal-overlay {
        position: fixed;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: flex;
        align-items: flex-start;
        justify-content: center;
        padding: 50px 16px;
        background: rgba(5, 20, 40, 0.45);
        overflow-y: auto;
        -webkit-overflow-scrolling: touch;
        z-index: 1081;
    }

    /* SweetAlert ตั้ง z-index มาที่ 1060 ต่ำกว่า micromodal (1080/1081)
       Swal ที่เด้งตอน modal เปิดอยู่จึงไปอยู่ข้างหลังจนมองไม่เห็น */
    .swal2-container {
        z-index: 2000 !important;
    }

    .micromodal-container {
        position: relative !important;
        top: auto !important;
        left: auto !important;
        transform: none !important;
        width: min(100%, 680px);
        max-height: calc(100vh - 100px);
        margin: 0 auto;
        background: #fff;
        border-radius: 14px;
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }

    #lineup-modal .micromodal-container {
        max-width: 950px;
    }

    #event-modal .micromodal-container {
        max-width: 500px;
    }

    /* โหมดเปลี่ยนตัว มีสองฝั่งวางคู่กัน ต้องกว้างกว่าฟอร์มเดี่ยวมาก */
    #event-modal .micromodal-container.modal-lg {
        max-width: 1000px;
    }

    .sub-hint {
        font-size: .82rem;
        color: #664d03;
        background: #fff8e1;
        border: 1px solid #ffe69c;
        border-radius: 8px;
        padding: 6px 10px;
    }

    .sub-pane {
        border: 1px solid #e3e6ea;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
    }

    .sub-pane__head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 8px 12px;
        font-weight: 600;
        font-size: .88rem;
        background: #f8f9fa;
        border-bottom: 1px solid #e3e6ea;
    }

    .sub-pane__body {
        /* บังคับให้เห็นว่าเลื่อนได้ ผู้ใช้จะได้รู้ว่ามีรายชื่อต่อข้างล่าง */
        max-height: 300px;
        overflow-y: scroll;
        padding: 8px;
        display: flex;
        flex-direction: column;
        gap: 6px;
    }

    .sub-card {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 7px 10px;
        border: 1px solid #e3e6ea;
        border-radius: 8px;
        background: #fff;
        cursor: pointer;
        transition: all .12s ease;
        text-align: left;
        width: 100%;
    }

    .sub-card:hover:not(.is-used) {
        border-color: #16478C;
        background: #f2f6ff;
    }

    .sub-card.is-picked {
        border-color: #16478C;
        background: #e7efff;
        box-shadow: inset 0 0 0 1px #16478C;
    }

    /* จับคู่ไปแล้ว = กดซ้ำไม่ได้ แต่ยังเห็นว่ามีคนนี้อยู่ */
    .sub-card.is-used {
        opacity: .45;
        cursor: not-allowed;
        background: #f1f3f5;
    }

    .sub-card__no {
        min-width: 34px;
        height: 26px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background: #eef1f5;
        font-weight: 700;
        font-size: .8rem;
    }

    .sub-card__name {
        flex: 1 1 auto;
        min-width: 0;
        font-size: .86rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sub-pair {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 10px;
        border: 1px solid #d7e3ff;
        border-radius: 8px;
        background: #f7faff;
        margin-bottom: 6px;
    }

    .sub-pair__no {
        min-width: 22px;
        font-weight: 700;
        color: #16478C;
    }

    .sub-pair__side {
        flex: 1 1 0;
        min-width: 0;
        font-size: .85rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    .sub-pair__minute {
        width: 84px;
        flex: 0 0 84px;
    }

    @media (max-width: 767.98px) {
        .sub-pane__body { max-height: 220px; }
        .sub-pair { flex-wrap: wrap; }
    }

    .micromodal-header,
    .micromodal-footer {
        padding: 16px 20px;
        background: #fff;
        flex-shrink: 0;
    }

    .micromodal-header {
        border-bottom: 1px solid #e9ecef;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .micromodal-title {
        margin: 0;
        font-size: 1.1rem;
        font-weight: 700;
        color: #22304a;
    }

    .micromodal-close {
        border: 0;
        background: transparent;
        font-size: 1.75rem;
        line-height: 1;
        color: #6c757d;
        cursor: pointer;
    }

    .micromodal-body {
        padding: 20px;
        overflow-y: auto;
        flex: 1 1 auto;
    }

    .micromodal-footer {
        border-top: 1px solid #e9ecef;
        display: flex;
        justify-content: flex-end;
        gap: 10px;
    }

    /* Force hide for tab panes */
    .tab-pane.force-hide {
        display: none !important;
    }

    /* Lineup Modal Tabs */
    #lineup-modal .nav-tabs {
        border-bottom: 2px solid #e9ecef;
        margin-bottom: 20px;
    }

    #lineup-modal .nav-tabs .nav-link {
        color: #6c757d;
        font-weight: 600;
        padding: 12px 24px;
        border: none;
        border-bottom: 3px solid transparent;
        margin-bottom: -2px;
        transition: all 0.2s;
    }

    #lineup-modal .nav-tabs .nav-link:hover {
        color: #16478D;
        border-color: transparent;
    }

    #lineup-modal .nav-tabs .nav-link.active {
        color: #16478D;
        background: transparent;
        border-bottom: 3px solid #16478D;
    }

    /* Player Table Styling */
    #lineup-modal .table {
        margin-bottom: 0;
    }

    #lineup-modal .table thead th {
        background: #f8f9fa;
        border-bottom: 2px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 12px 8px;
        font-size: 0.85rem;
    }

    #lineup-modal .table tbody td {
        padding: 10px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f4;
    }

    #lineup-modal .table tbody tr:hover {
        background-color: #f5f5f5;
    }

    /* Custom row colors for lineup modal */
    #lineup-modal .table tbody tr.table-success {
        background-color: #e8f5e9 !important;
    }

    #lineup-modal .table tbody tr.table-success:hover {
        background-color: #c8e6c9 !important;
    }

    #lineup-modal .table tbody tr.table-warning {
        background-color: #fff3e0 !important;
    }

    #lineup-modal .table tbody tr.table-warning:hover {
        background-color: #ffe0b2 !important;
    }

    #lineup-modal .form-check-input {
        width: 18px;
        height: 18px;
        cursor: pointer;
    }

    #lineup-modal .form-check-input:checked {
        background-color: #16478D;
        border-color: #16478D;
    }

    /* Save Button */
    #lineup-modal .btn-primary {
        background: linear-gradient(135deg, #16478D 0%, #1e5bb5 100%);
        border: none;
        padding: 10px 24px;
        font-weight: 600;
        border-radius: 8px;
    }

    #lineup-modal .btn-primary:hover {
        background: linear-gradient(135deg, #1e5bb5 0%, #16478D 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(22, 71, 141, 0.3);
    }

    /* Event Modal Form */
    #event-modal .form-label {
        font-weight: 600;
        color: #495057;
        margin-bottom: 6px;
    }

    #event-modal .form-select,
    #event-modal .form-control {
        border-radius: 8px;
        border: 1px solid #dee2e6;
        padding: 10px 12px;
    }

    #event-modal .form-select:focus,
    #event-modal .form-control:focus {
        border-color: #16478D;
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.1);
    }

    #event-modal .btn-success {
        background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        border: none;
        padding: 10px 20px;
        font-weight: 600;
        border-radius: 8px;
    }

    #event-modal .btn-success:hover {
        background: linear-gradient(135deg, #20c997 0%, #28a745 100%);
    }

    /* Team Management Cards - New UI */
    .team-management-card {
        background: #fff;
        border: 1px solid #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        height: 100%;
    }

    .team1-card {
        border-top: 3px solid #16478D;
    }

    .team2-card {
        border-top: 3px solid #28a745;
    }

    .team-management-header {
        background: #f8f9fa;
        padding: 10px 15px;
        display: flex;
        align-items: center;
        gap: 10px;
        border-bottom: 1px solid #e9ecef;
    }

    .team-mini-logo {
        width: 30px;
        height: 30px;
        border-radius: 50%;
        object-fit: cover;
        border: 1px solid #e9ecef;
    }

    .team-management-name {
        font-weight: 600;
        font-size: 0.95rem;
        color: #333;
    }

    .team-lineup-section,
    .team-events-section {
        border-bottom: 1px solid #f1f3f4;
    }

    .team-lineup-section:last-child,
    .team-events-section:last-child {
        border-bottom: none;
    }

    .team-section-header {
        padding: 8px 12px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        font-weight: 600;
        color: #495057;
        background: #fafbfc;
    }

    .team-section-header .btn {
        position: relative;
        z-index: 10;
    }

    .team-lineup-content {
        padding: 0;
        max-height: 250px;
        overflow-y: auto;
    }

    .team-events-content {
        padding: 10px 12px;
        max-height: 150px;
        overflow-y: auto;
    }

    .btn-xs {
        padding: 2px 6px;
        font-size: 0.7rem;
        border-radius: 4px;
    }

    /* Lineup Table */
    .lineup-table {
        font-size: 0.75rem;
    }

    .lineup-table thead th {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        font-weight: 600;
        color: #495057;
        padding: 6px 8px;
        font-size: 0.7rem;
    }

    .lineup-table tbody td {
        padding: 4px 8px;
        vertical-align: middle;
        border-bottom: 1px solid #f1f3f4;
    }

    .lineup-section-header td {
        background: #e3f2fd;
        color: #1565c0;
        padding: 4px 8px;
        font-size: 0.7rem;
    }

    .lineup-section-header.sub-header td {
        background: #fce4ec;
        color: #c2185b;
    }

    .lineup-starter-row {
        background: #f5f5f5;
    }

    .lineup-starter-row:hover {
        background: #eeeeee;
    }

    .lineup-sub-row {
        background: #fafafa;
    }

    .lineup-sub-row:hover {
        background: #f5f5f5;
    }

    /* Legacy: Team Player Badges (kept for compatibility) */
    .team-player-badges {
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        margin-bottom: 8px;
    }

    .team-player-badge {
        display: inline-flex;
        align-items: center;
        padding: 2px 8px;
        border-radius: 12px;
        font-size: 0.7rem;
        background: #d4edda;
        border: 1px solid #28a745;
        color: #155724;
    }

    .team-player-badge.substitute {
        background: #fff3cd;
        border-color: #ffc107;
        color: #856404;
    }

    .team-player-badge .player-num {
        font-weight: 700;
        margin-right: 3px;
    }

    .team-subs-label {
        font-size: 0.7rem;
        color: #6c757d;
        margin: 6px 0 4px 0;
    }

    /* Team Events List */
    .team-event-item {
        display: flex;
        align-items: center;
        padding: 5px 0;
        border-bottom: 1px solid #f1f3f4;
        gap: 8px;
        font-size: 0.8rem;
    }

    .team-event-item:last-child {
        border-bottom: none;
    }

    .team-event-minute {
        font-weight: 700;
        color: #16478D;
        min-width: 28px;
        font-size: 0.75rem;
    }

    .team-event-icon {
        font-size: 0.9rem;
    }

    .team-event-details {
        flex: 1;
        font-size: 0.75rem;
    }

    .team-event-delete {
        color: #dc3545;
        cursor: pointer;
        opacity: 0.5;
        font-size: 0.8rem;
    }

    .team-event-delete:hover {
        opacity: 1;
    }

    .no-team-events {
        text-align: center;
        color: #adb5bd;
        font-size: 0.75rem;
        padding: 10px;
    }

    /* Edit Button for Ended Matches */
    .btn-edit-ended {
        border-color: #ffc107;
        color: #856404;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-edit-ended:hover {
        background-color: #ffc107;
        border-color: #ffc107;
        color: #000;
    }

    /* Editing Mode Styles */
    .match-card.editing-mode {
        border: 2px solid #ffc107 !important;
        box-shadow: 0 0 10px rgba(255, 193, 7, 0.3);
    }

    .match-card.editing-mode::before {
        content: '<?= lang("Common.edit_mode") ?>';
        position: absolute;
        top: -10px;
        right: 15px;
        background: #ffc107;
        color: #000;
        padding: 2px 10px;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .match-card {
        position: relative;
    }

    /* Date Filter Styles */
    #dateFilter {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 6px 12px;
        font-size: 0.875rem;
        transition: all 0.2s;
        background-color: #fff;
    }

    #dateFilter:focus {
        border-color: #16478D;
        box-shadow: 0 0 0 3px rgba(22, 71, 141, 0.1);
        outline: none;
    }

    #dateFilter:hover {
        border-color: #16478D;
    }

    @media (max-width: 768px) {
        .d-flex.align-items-center.gap-3 {
            flex-direction: column;
            align-items: stretch !important;
            gap: 10px !important;
        }

        .d-flex.align-items-center.gap-2 {
            flex-direction: column;
            align-items: stretch !important;
            gap: 5px !important;
        }

        #dateFilter {
            width: 100% !important;
            min-width: unset !important;
        }

        .micromodal-overlay {
            align-items: flex-start;
            padding: 16px 10px;
        }

        .micromodal-container {
            width: 100%;
            max-height: calc(100vh - 32px);
        }

        .micromodal-header,
        .micromodal-body,
        .micromodal-footer {
            padding-left: 14px;
            padding-right: 14px;
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
