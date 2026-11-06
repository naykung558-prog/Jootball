<style>
.day-header{font-size:1.2rem;font-weight:700;margin:20px 0 10px;padding:8px 12px;background:#f1f3f5;border-radius:6px;}
.match-cell{border:1px solid #e5e5e5;padding:12px;}
.match-cell.left{border-right:0;}
.group{font-weight:700;margin-bottom:6px;}
.match-cell .team{display:flex;justify-content:space-between;align-items:center;margin:4px 0; font-size: 14px;}
.team .team-left{display:flex;align-items:center;gap:8px;}
.team .team-logo,.team-info .team-logo{width:28px;height:28px;object-fit:contain;background:#eee;}

.score{font-weight:700;min-width:30px;text-align:center;}
.match-info{text-align:right;}
.match-btn{font-size:.75rem;background:#0d6efd;color:#fff;padding:2px 6px;border-radius:4px;display:inline-block;margin-top:4px}
.match-btn.btn-danger{background:#dc3545;}
.match-btn.btn-danger:hover{background:#bb2d3b;}

#bracketWrapper{position:relative;overflow-x:auto;padding-bottom:20px;}
#bracketLines{position:absolute;top:0;left:0;width:100%;height:100%;pointer-events:none;}
#bracketRoundTabs{
  width:fit-content;
  max-width:100%;
  margin:0 auto 22px;
  padding:6px;
  gap:4px;
  border:1px solid #e3e7eb;
  border-radius:8px;
  background:#f8f9fa;
  overflow-x:auto;
  flex-wrap:nowrap;
  box-shadow:inset 0 1px 0 rgba(255,255,255,.8);
}
#bracketRoundTabs .nav-item{
  flex:0 0 auto;
}
#bracketRoundTabs .nav-link{
  min-width:96px;
  border:0;
  border-radius:6px;
  color:#495057;
  font-size:13px;
  font-weight:700;
  white-space:nowrap;
  transition:background-color .15s ease,color .15s ease,box-shadow .15s ease;
}
#bracketRoundTabs .nav-link:hover{
  color:#212529;
  background:#fff;
}
#bracketRoundTabs .nav-link.active{
  color:#fff;
  background:#303236;
  box-shadow:0 4px 10px rgba(33,37,41,.18);
}
.bracket-col{min-width:260px;}
.bracket-title{font-weight:700;text-align:center;margin-bottom:12px;}
.bracket-match{border:1px solid #e5e5e5;border-radius:8px;padding:10px;margin-bottom:16px;background:#fff;}
.bracket-team{display:flex;justify-content:space-between;align-items:center;margin:4px 0;}
.bracket-left{display:flex;align-items:center;gap:8px; font-size: 14px;}
.bracket-logo{width:26px;height:26px;object-fit:contain;background:#eee;}
.bracket-seq{
  display:inline-flex;
  align-items:center;
  justify-content:center;
  min-width:36px;
  height:22px;
  margin-bottom:8px;
  border-radius:6px;
  background:#303236;
  color:#fff;
  font-size:12px;
  font-weight:700;
}
.bracket-pair-container{display:flex;flex-direction:column;align-items:flex-end;gap:24px;padding:8px 20% 8px 8px;overflow:visible;}
.bracket-pair{position:relative;width:100%;}
.bracket-match-item{
  position:relative;
  border:1px solid #e3e7eb;
  border-radius:8px;
  padding:12px 14px;
  background:#fff;
  box-shadow:0 6px 18px rgba(33,37,41,.07);
}
.bracket-match-item .bracket-team{
  position:relative;
  min-height:38px;
  margin:6px 0;
  padding:6px 12px 6px 8px;
  border:1px solid transparent;
  border-radius:6px;
  background:#f8f9fa;
}
.bracket-team.is-winner{
  font-weight:700;
  color:#198754;
  border-color:#b7e4c7;
  background:#eefaf2;
}
.bracket-team.is-loser{
  color:#6c757d;
  background:#fff;
}
.bracket-match-item .bracket-connector-v{
  position:absolute;
  right:-12%;
  top:50%;
  width:12%;
  height:50px;
  border-top:2px solid #c9d1d9;
  border-right:2px solid #c9d1d9;
  border-bottom:2px solid #c9d1d9;
  border-radius:0 8px 8px 0;
  transform:translateY(-50%);
}
.bracket-match-item .bracket-connector-h{
  position:absolute;
  right:-20%;
  top:50%;
  width:8%;
  border-top:2px solid #c9d1d9;
}
.bracket-match-item.no-output .bracket-connector-v,
.bracket-match-item.no-output .bracket-connector-h{
  display:none;
}
.bracket-match-item .bracket-team.is-winner::after{
  content:'';
  position:absolute;
  right:-20%;
  top:50%;
  width:20%;
  border-top:3px solid #198754;
  transform:translateY(-50%);
  z-index:2;
}
.bracket-match-item.no-output .bracket-team.is-winner::after{
  display:none;
}

.players-grid{
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(160px, 1fr));
  gap: 16px;
}

.player-card{
  display: flex;
  flex-direction: column;
}

.player-card{
  background: #fff;
  border-radius: 10px;
  padding: 12px;
  text-align: center;
  box-shadow: 0 2px 6px rgba(0,0,0,.08);
}

.player-photo{
  width: 100%;
  aspect-ratio: 1 / 1;
  object-fit: cover;
  background: #f1f1f1;
  border-radius: 8px;
}

.player-name{
  font-weight: 400;
  margin-top: 8px;
  font-size: 13px;
}

.player-position{
  font-size: 13px;
  color: #666;
}

.team-info {
  font-size: 13px
}


.group-title{font-weight:700;margin:5px 0 8px;font-size:15px;}
.ranking-table{width:100%;border-collapse:separate;border-spacing:0;background:#fff;}
.ranking-table thead th{background:#f8f9fa;font-weight:600;font-size:13px;padding:10px;border-bottom:1px solid #dee2e6;text-align:center;}
.ranking-table tbody td{padding:10px;font-size:13px;border-bottom:1px solid #eee;text-align:center;}
.rank-team{display:flex;align-items:center;gap:8px;text-align:left;}
.rank-team img{width:20px;height:20px;object-fit:contain;}
.rank-pos{font-weight:600;}
.match-btn {background-color:#303236}
/* ===== highlight อันดับ 1–2 (ไม่ทำให้ตารางล้น) ===== */
#rankingContainer {
  padding: 10px;
}
.ranking-table tbody tr.top-rank td:first-child{
  position: relative;
  padding-left: 16px; /* เผื่อที่ให้แถบ */
}

.ranking-table tbody tr.top-rank td:first-child::before{
  content:'';
  position:absolute;
  left:4px;
  top:50%;
  transform:translateY(-50%);
  width:6px;
  height:70%;
  /*background:#0d6efd; */
  border-radius:4px;
}
.team-name {
  font-size: 12px;
}

/* ===== Summary Stats Cards - Premium Version ===== */
.stat-card {
  background: #ffffff;
  border-radius: 20px;
  padding: 1.75rem 1rem;
  border: 1px solid #f0f0f0;
  box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
  transition: all 0.3s ease;
  height: 100%;
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
  overflow: hidden;
}

.stat-card::before {
  content: "";
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 4px;
  background: transparent;
  transition: all 0.3s ease;
}

.stat-card:hover {
  transform: translateY(-8px);
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.08);
  border-color: #e0e0e0;
}

.stat-card:hover::before {
  background: currentColor; /* Uses the text color of the card for the top bar */
}

.stat-icon-wrapper {
  width: 64px;
  height: 64px;
  border-radius: 18px;
  display: flex;
  align-items: center;
  justify-content: center;
  font-size: 1.75rem;
  margin-bottom: 1.25rem;
  box-shadow: 0 8px 15px rgba(0, 0, 0, 0.05);
}

.stat-value {
  font-size: 2.25rem;
  font-weight: 800;
  line-height: 1.2;
  margin-bottom: 0.25rem;
  color: #1a202c;
  letter-spacing: -1px;
}

.stat-label {
  font-size: 0.8rem;
  color: #a0aec0;
  font-weight: 700;
  text-transform: uppercase;
  letter-spacing: 1px;
}

/* Gradients for icons */
.grad-blue   { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: #fff; }
.grad-purple { background: linear-gradient(135deg, #a18cd1 0%, #fbc2eb 100%); color: #fff; }
.grad-green  { background: linear-gradient(135deg, #0ba360 0%, #3cba92 100%); color: #fff; }
.grad-orange { background: linear-gradient(135deg, #f6d365 0%, #fda085 100%); color: #fff; }
.grad-red    { background: linear-gradient(135deg, #ff0844 0%, #ffb199 100%); color: #fff; }
.grad-cyan   { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); color: #fff; }

.stat-card.blue   { color: #667eea; }
.stat-card.purple { color: #a18cd1; }
.stat-card.green  { color: #0ba360; }
.stat-card.orange { color: #fda085; }
.stat-card.red    { color: #ff0844; }
.stat-card.cyan   { color: #4facfe; }

/* ===== LineUP MVP ===== */
/* ===== FIELD ===== */
#footballField{
  position:relative;width:100%;height:680px;border-radius:16px;
  overflow:hidden;border:3px solid #fff;
  background:repeating-linear-gradient(
    180deg,#2e7d32 0,#2e7d32 45px,#1b5e20 45px,#1b5e20 90px
  );
}
#footballField::before{
  content:"";position:absolute;left:0;right:0;top:50%;
  height:2px;background:#fff;z-index:1;
}
#footballField::after{
  content:"";position:absolute;top:50%;left:50%;
  width:110px;height:110px;border:2px solid #fff;
  border-radius:50%;transform:translate(-50%,-50%);
}

/* เขตโทษ */
.penalty-top,.penalty-bottom{
  position:absolute;left:50%;
  width:240px;height:120px;
  border:2px solid #fff;
  transform:translateX(-50%);
}
.penalty-top{top:0}
.penalty-bottom{bottom:0}

/* ===== ZONES ===== */
.zone{position:absolute;width:100%;display:flex;justify-content:center;gap:5px;min-height:100px;z-index:2;flex-wrap: wrap;align-content: flex-start;}
.zone.fw{top:60px}
.zone.mf{top:200px}
.zone.df{top:400px}
.zone.gk{bottom:50px}
.manager-zone{margin-top:12px;display:flex;justify-content:center}
.zone.manager{min-height:60px}

/* ===== PLAYER ===== */
.player{
  scale: 0.7;
  background:#fff;border-radius:20px;
  padding:6px 12px 6px 6px;
  font-size:11px;min-width:85px;
  box-shadow:0 2px 5px rgba(0,0,0,.25);
  cursor:move;position:relative;
  display:flex;align-items:center;gap:4px;
}
.player img.photo{width:32px;height:32px;border-radius:50%;object-fit:cover}
.player img.logo{
  width:18px;height:18px;
  position:absolute;left:4px;bottom:-8px;
  background:#fff;border-radius:50%
}
.player .pos{font-size:10px;font-weight:bold;color:#198754}
.player .remove{
  position:absolute;right:6px;top:2px;
  font-size:12px;color:#dc3545;cursor:pointer
}
.player.active{outline:2px solid #0d6efd}
.zone.manager .player{background:#fff3cd;border:1px solid #ffe69c}

/* ===== PLAYER INFO ===== */
#playerInfo{
  background:linear-gradient(135deg,#ffffff,#f1f3f5);
  border-radius:16px;padding:16px;min-height:150px;
  box-shadow:0 6px 14px rgba(0,0,0,.15);
}
.player-info-card{display:flex;gap:16px;align-items:center}
.player-info-card img.photo{
  width:96px;height:96px;border-radius:50%;
  border:3px solid #0d6efd;object-fit:cover
}
.player-info-card .name{font-size:1.2rem;font-weight:700}
.player-info-card .team{display:flex;align-items:center;gap:6px;font-size:.95rem}
.player-info-card .meta{font-size:.9rem;color:#555}
.empty-info{text-align:center;color:#999;padding:30px 0}

.final-ranking-row{
  display:flex;
  align-items:center;
  gap:14px;
  padding:10px 0;
  border-bottom:1px solid #eef0f2;
}
.final-ranking-row:last-child{border-bottom:0}
.final-ranking-no{
  flex:0 0 88px;
  font-weight:700;
  color:#303236;
}
.final-ranking-team{max-width:520px}
.ranking-team-option{
  display:flex;
  align-items:center;
  gap:8px;
}
.ranking-team-option img{
  width:24px;
  height:24px;
  object-fit:contain;
  border-radius:4px;
  background:#f1f3f5;
}
#finalRanking .select2-container--default .select2-selection--single{
  min-height:38px;
  border-color:#dee2e6;
}
#finalRanking .select2-container--default .select2-selection--single .select2-selection__rendered{
  line-height:36px;
}
#finalRanking .select2-container--default .select2-selection--single .select2-selection__arrow{
  height:36px;
}

/* ===== END LineUP MVP ===== */

</style>
