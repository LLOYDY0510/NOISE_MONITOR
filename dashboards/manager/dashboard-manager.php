<?php
require_once __DIR__ . '/../../includes/config.php';
requireRole('Library Manager');
$user    = currentUser();
$initial = strtoupper(substr($user['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LibraryQuiet – Manager Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css"/>
  <script>window.__LQ_SESSION__ = <?= json_encode($user) ?>;</script>
  <style>
    /* ============================================================
   LibraryQuiet – dashboard-manager.css (teal theme)
   ============================================================ */

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

:root {
  --teal:   #0d9488;
  --teal2:  #0f766e;
  --green:  #10b981;
  --yellow: #f59e0b;
  --red:    #ef4444;
  --blue:   #3b82f6;
  --bg:     #f0f4f8;
  --sidebar:#0f172a;
  --sb2:    #1e293b;
  --border: #e2e8f0;
  --text:   #0f172a;
  --muted:  #64748b;
  --light:  #94a3b8;
  --radius: 16px;
}

body { font-family: 'Plus Jakarta Sans', sans-serif; background: var(--bg); display: flex; height: 100vh; overflow: hidden; }

/* ── SIDEBAR ──────────────────────────────────────────────── */
#sidebar { width: 240px; background: var(--sidebar); display: flex; flex-direction: column; flex-shrink: 0; box-shadow: 2px 0 24px rgba(0,0,0,.3); z-index: 100; transition: width .3s ease; }
#sidebar.collapsed { width: 68px; }
.sb-logo { padding: 18px 15px; border-bottom: 1px solid var(--sb2); display: flex; align-items: center; gap: 12px; min-height: 68px; }
.sb-logo-icon { width: 38px; height: 38px; border-radius: 11px; background: linear-gradient(135deg,#0d9488,#0f766e); display: flex; align-items: center; justify-content: center; font-size: 17px; flex-shrink: 0; box-shadow: 0 4px 12px rgba(13,148,136,.35); }
.sb-logo-name { color: #fff; font-weight: 800; font-size: 14px; }
.sb-logo-sub  { color: #475569; font-size: 11px; }
.sb-logo-text { transition: opacity .2s; }
#sidebar.collapsed .sb-logo-text { opacity: 0; pointer-events: none; }
.sb-user { padding: 10px 12px; border-bottom: 1px solid var(--sb2); }
.sb-user-label { font-size: 10px; color: #475569; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 6px; }
.sb-user-chip { background: var(--sb2); border-radius: 10px; padding: 8px 10px; display: flex; align-items: center; gap: 8px; }
.sb-av { width: 28px; height: 28px; border-radius: 8px; background: linear-gradient(135deg,#0d9488,#0f766e); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 12px; flex-shrink: 0; }
.sb-uname { color: #e2e8f0; font-size: 12px; font-weight: 700; }
.sb-urole { color: #2dd4bf; font-size: 10px; font-weight: 600; }
#sidebar.collapsed .sb-user { display: none; }
nav { flex: 1; padding: 10px 8px; overflow-y: auto; overflow-x: hidden; }
nav::-webkit-scrollbar { width: 3px; }
nav::-webkit-scrollbar-thumb { background: #334155; border-radius: 3px; }
.sb-sec { font-size: 10px; color: #334155; text-transform: uppercase; letter-spacing: 1px; padding: 8px 12px 4px; font-weight: 700; }
.sb-div { height: 1px; background: var(--sb2); margin: 4px 8px; }
#sidebar.collapsed .sb-sec, #sidebar.collapsed .sb-div { display: none; }
.nav-item { width: 100%; display: flex; align-items: center; gap: 12px; padding: 10px 12px; border-radius: 10px; margin-bottom: 2px; background: transparent; color: #64748b; font-size: 13px; font-weight: 500; font-family: 'Plus Jakarta Sans', sans-serif; text-align: left; text-decoration: none; white-space: nowrap; transition: all .2s; }
.nav-item:hover  { background: var(--sb2); color: #cbd5e1; }
.nav-item.active { background: linear-gradient(90deg,rgba(13,148,136,.9),rgba(15,118,110,.8)); color: #fff; font-weight: 700; box-shadow: 0 2px 10px rgba(13,148,136,.3); }
.ni { font-size: 17px; width: 22px; text-align: center; flex-shrink: 0; }
.nl { transition: opacity .2s; }
#sidebar.collapsed .nl { opacity: 0; width: 0; overflow: hidden; }
#sidebar.collapsed .nav-item { justify-content: center; padding: 10px 0; }
.nb { margin-left: auto; background: #ef4444; color: #fff; font-size: 9px; font-weight: 700; border-radius: 20px; padding: 2px 7px; }
#sidebar.collapsed .nb { display: none; }
.sb-bottom { padding: 12px; border-top: 1px solid var(--sb2); display: flex; align-items: center; gap: 8px; }
.sb-av-lg { width: 34px; height: 34px; border-radius: 10px; background: linear-gradient(135deg,#0d9488,#0f766e); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 13px; flex-shrink: 0; }
.sb-binfo { flex: 1; min-width: 0; overflow: hidden; transition: opacity .2s; }
.sb-bname { color: #e2e8f0; font-size: 12px; font-weight: 700; }
.sb-brole { color: #475569; font-size: 10px; }
#sidebar.collapsed .sb-binfo { opacity: 0; width: 0; }
.sb-logout { background: none; border: none; color: #475569; font-size: 15px; text-decoration: none; padding: 4px; transition: color .2s; cursor: pointer; }
.sb-logout:hover { color: #ef4444; }
.sb-toggle { background: none; border: none; color: #475569; font-size: 13px; cursor: pointer; padding: 4px; transition: transform .3s; }
#sidebar.collapsed .sb-toggle { transform: rotate(180deg); }
#sidebar.collapsed .sb-logout { display: none; }

/* ── MAIN ─────────────────────────────────────────────────── */
#main { flex: 1; display: flex; flex-direction: column; overflow: hidden; }
#topbar { background: #fff; border-bottom: 1px solid var(--border); padding: 0 24px; height: 64px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 1px 8px rgba(0,0,0,.05); flex-shrink: 0; }
.tb-title { font-weight: 800; font-size: 17px; color: var(--text); }
.tb-date  { font-size: 11px; color: var(--light); margin-top: 1px; }
.tb-right { display: flex; align-items: center; gap: 12px; }
.role-badge  { padding: 5px 14px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.admin-badge   { background: #fef9c3; color: #713f12; border: 1px solid #fde047; }
.manager-badge { background: #ccfbf1; color: #134e4a; border: 1px solid #5eead4; }
.staff-badge   { background: #faf5ff; color: #7c3aed; border: 1.5px solid #c4b5fd; }
.live-badge { display: flex; align-items: center; gap: 6px; background: #d1fae5; padding: 5px 14px; border-radius: 20px; }
.live-dot { width: 7px; height: 7px; border-radius: 50%; background: #10b981; animation: pulse 1.5s infinite; }
.live-text { font-size: 11px; font-weight: 700; color: #065f46; }
@keyframes pulse { 0%,100%{opacity:1} 50%{opacity:.4} }
.tb-bell { position: relative; width: 36px; height: 36px; border-radius: 10px; background: #f8fafc; border: 1px solid var(--border); display: flex; align-items: center; justify-content: center; font-size: 16px; text-decoration: none; transition: background .2s; }
.tb-bell:hover { background: #f1f5f9; }
.tb-bc { position: absolute; top: -4px; right: -4px; background: #ef4444; color: #fff; font-size: 8px; border-radius: 50%; width: 15px; height: 15px; display: flex; align-items: center; justify-content: center; font-weight: 700; border: 2px solid #fff; }
.tb-av { width: 36px; height: 36px; border-radius: 10px; background: linear-gradient(135deg,#0d9488,#0f766e); display: flex; align-items: center; justify-content: center; color: #fff; font-weight: 800; font-size: 14px; }

/* ── CONTENT ──────────────────────────────────────────────── */
#content { flex: 1; overflow-y: auto; padding: 24px; animation: pageIn .35s ease; }
#content::-webkit-scrollbar { width: 5px; }
#content::-webkit-scrollbar-thumb { background: #e2e8f0; border-radius: 5px; }
@keyframes pageIn { from{opacity:0;transform:translateY(10px)} to{opacity:1;transform:none} }

/* ── WELCOME BANNER ───────────────────────────────────────── */
.welcome-banner { background: linear-gradient(135deg,#0d9488,#0f766e); border-radius: var(--radius); padding: 22px 28px; margin-bottom: 20px; display: flex; justify-content: space-between; align-items: center; box-shadow: 0 4px 20px rgba(13,148,136,.3); }
.wb-title { font-size: 18px; font-weight: 800; color: #fff; margin-bottom: 4px; }
.wb-sub   { font-size: 12px; color: rgba(255,255,255,.75); }
.wb-right { display: flex; gap: 10px; flex-shrink: 0; }
.wb-btn   { padding: 9px 18px; background: #fff; color: #0d9488; border-radius: 10px; font-size: 12px; font-weight: 700; text-decoration: none; transition: all .2s; }
.wb-btn:hover { background: #f0fdfa; }
.wb-btn-outline { background: transparent; color: #fff; border: 1.5px solid rgba(255,255,255,.4); }
.wb-btn-outline:hover { background: rgba(255,255,255,.1); }

/* ── STAT CARDS ───────────────────────────────────────────── */
.stat-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; margin-bottom: 20px; }
.stat-card { background: #fff; border-radius: var(--radius); padding: 20px; box-shadow: 0 1px 8px rgba(0,0,0,.07); border-left: 4px solid; transition: transform .2s, box-shadow .2s; }
.stat-card:hover { transform: translateY(-2px); box-shadow: 0 4px 16px rgba(0,0,0,.1); }
.stat-card.blue   { border-color: #3b82f6; }
.stat-card.green  { border-color: #10b981; }
.stat-card.yellow { border-color: #f59e0b; }
.stat-card.red    { border-color: #ef4444; }
.stat-top   { display: flex; justify-content: space-between; align-items: flex-start; }
.stat-label { font-size: 11px; color: var(--light); text-transform: uppercase; letter-spacing: .5px; font-weight: 600; margin-bottom: 6px; }
.stat-val   { font-size: 30px; font-weight: 900; color: var(--text); line-height: 1; }
.stat-sub   { font-size: 11px; color: var(--muted); margin-top: 5px; }
.stat-icon  { font-size: 26px; opacity: .8; }
.stat-trend { font-size: 11px; font-weight: 700; margin-top: 10px; }
.trend-green { color: #10b981; }
.trend-red   { color: #ef4444; }
.trend-blue  { color: #3b82f6; }

/* ── SENSOR PANEL ─────────────────────────────────────────── */
.sensor-panel { background:#fff; border-radius:16px; padding:22px; box-shadow:0 1px 8px rgba(0,0,0,.07); margin-bottom:16px; border-top: 3px solid #0d9488; }
.sensor-panel-title { font-weight:800; font-size:15px; color:#0f172a; margin-bottom:4px; display:flex; align-items:center; gap:8px; }
.sensor-panel-sub   { font-size:12px; color:#94a3b8; margin-bottom:16px; }
.sensor-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; }
.sensor-card { background:#f8fafc; border:1.5px solid #e2e8f0; border-radius:12px; padding:16px; transition:all .3s; }
.sensor-card.manual { border-color:#0d9488; background:#f0fdfa; box-shadow:0 0 0 2px rgba(13,148,136,.15); }
.sc-name  { font-weight:700; font-size:13px; color:#0f172a; margin-bottom:2px; }
.sc-id    { font-size:10px; color:#94a3b8; font-family:'JetBrains Mono',monospace; margin-bottom:8px; }
.sc-level { font-size:32px; font-weight:900; line-height:1; margin-bottom:6px; }
.sc-bar-track { height:7px; background:#e2e8f0; border-radius:4px; overflow:hidden; margin-bottom:10px; }
.sc-bar-fill  { height:100%; border-radius:4px; transition:width .8s ease, background .4s; }
.sc-manual-badge { font-size:10px; font-weight:700; background:#ccfbf1; color:#134e4a; padding:2px 8px; border-radius:20px; margin-bottom:8px; display:inline-block; }
.sc-input-row { display:flex; gap:6px; align-items:center; margin-top:8px; }
.sc-input { flex:1; padding:7px 10px; border:1.5px solid #e2e8f0; border-radius:8px; font-size:13px; font-weight:700; font-family:'JetBrains Mono',monospace; color:#0f172a; background:#fff; outline:none; transition:border-color .2s; }
.sc-input:focus { border-color:#0d9488; box-shadow:0 0 0 3px rgba(13,148,136,.1); }
.sc-set-btn   { padding:7px 14px; background:#0d9488; color:#fff; border:none; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:background .2s; white-space:nowrap; }
.sc-set-btn:hover { background:#0f766e; }
.sc-clear-btn { padding:7px 10px; background:#fee2e2; color:#991b1b; border:none; border-radius:8px; font-size:12px; font-weight:700; cursor:pointer; font-family:'Plus Jakarta Sans',sans-serif; transition:background .2s; }
.sc-clear-btn:hover { background:#fecaca; }

/* ── QUICK ACTIONS ────────────────────────────────────────── */
.quick-actions { margin-bottom: 20px; }
.qa-title { font-size: 12px; font-weight: 700; color: var(--light); text-transform: uppercase; letter-spacing: .8px; margin-bottom: 12px; }
.qa-grid  { display: grid; grid-template-columns: repeat(6,1fr); gap: 12px; }
.qa-card  { background: #fff; border-radius: 14px; padding: 16px 14px; box-shadow: 0 1px 8px rgba(0,0,0,.06); text-align: center; text-decoration: none; transition: all .2s; border: 1.5px solid transparent; }
.qa-card:hover { transform: translateY(-3px); box-shadow: 0 6px 20px rgba(0,0,0,.1); border-color: #99f6e4; }
.qa-locked { cursor: not-allowed; opacity: .5; }
.qa-locked:hover { transform: none; box-shadow: 0 1px 8px rgba(0,0,0,.06); border-color: transparent; }
.qa-icon  { width: 40px; height: 40px; border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 18px; margin: 0 auto 10px; }
.qa-label { font-size: 12px; font-weight: 700; color: var(--text); margin-bottom: 3px; }
.qa-sub   { font-size: 10px; color: var(--light); }

/* ── LAYOUT ───────────────────────────────────────────────── */
.two-col   { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.three-col { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-top: 16px; }

/* ── CARD ─────────────────────────────────────────────────── */
.card { background: #fff; border-radius: var(--radius); padding: 22px; box-shadow: 0 1px 8px rgba(0,0,0,.07); }
.card-title    { font-weight: 800; font-size: 15px; color: var(--text); margin-bottom: 4px; }
.card-sub      { font-size: 12px; color: var(--light); margin-bottom: 18px; }
.card-sub.mb0  { margin-bottom: 0; }
.card-head-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 18px; }
.view-all { font-size: 12px; font-weight: 700; color: #0d9488; text-decoration: none; padding: 6px 14px; background: #f0fdfa; border-radius: 8px; transition: background .2s; }
.view-all:hover { background: #ccfbf1; }

/* ── ZONE BARS ────────────────────────────────────────────── */
.zone-row  { margin-bottom: 14px; }
.zone-meta { display: flex; justify-content: space-between; align-items: center; margin-bottom: 5px; }
.zone-left { display: flex; align-items: center; gap: 8px; }
.zone-dot  { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }
.zone-name { font-size: 12px; font-weight: 700; color: #1e293b; }
.zone-floor{ font-size: 10px; color: var(--light); background: #f1f5f9; padding: 1px 6px; border-radius: 4px; }
.zone-right{ display: flex; align-items: center; gap: 8px; }
.zone-db   { font-size: 13px; font-weight: 900; }
.zone-badge{ font-size: 10px; padding: 2px 8px; border-radius: 8px; font-weight: 700; text-transform: capitalize; }
.bar-track { height: 7px; background: #f1f5f9; border-radius: 4px; overflow: hidden; }
.bar-fill  { height: 100%; border-radius: 4px; transition: width 1s ease; }

/* ── CHART ────────────────────────────────────────────────── */
.chart-wrap { display: flex; align-items: flex-end; gap: 5px; height: 130px; margin-bottom: 12px; }
.chart-col  { flex: 1; display: flex; flex-direction: column; align-items: center; gap: 4px; }
.chart-bar  { width: 100%; border-radius: 5px 5px 0 0; opacity: .85; transition: height .4s ease; }
.chart-lbl  { font-size: 9px; color: var(--light); }
.chart-legend { display: flex; gap: 14px; flex-wrap: wrap; margin-top: 4px; }
.cl-item { display: flex; align-items: center; gap: 5px; font-size: 10px; color: var(--muted); }
.cl-dot  { width: 10px; height: 10px; border-radius: 3px; }

/* ── TABLE ────────────────────────────────────────────────── */
.tbl-wrap { overflow-x: auto; }
table { width: 100%; border-collapse: collapse; }
thead tr { border-bottom: 2px solid #f1f5f9; }
th { text-align: left; padding: 8px 12px; font-size: 11px; color: var(--light); font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
tbody tr { border-bottom: 1px solid #f8fafc; transition: background .15s; }
tbody tr:hover { background: #fafbfc; }
td { padding: 11px 12px; font-size: 13px; }
.badge { display: inline-flex; align-items: center; padding: 3px 10px; border-radius: 20px; font-size: 11px; font-weight: 700; }
.b-red    { background: #fee2e2; color: #991b1b; }
.b-yellow { background: #fef3c7; color: #92400e; }
.b-green  { background: #d1fae5; color: #065f46; }
.b-gray   { background: #f1f5f9; color: #64748b; }

/* ── SUMMARY CARDS ────────────────────────────────────────── */
.sum-card  { text-align: center; padding: 24px 16px; }
.sum-icon  { width: 48px; height: 48px; border-radius: 14px; display: flex; align-items: center; justify-content: center; font-size: 22px; margin: 0 auto 12px; }
.sum-label { font-size: 11px; color: var(--light); font-weight: 600; margin-bottom: 6px; text-transform: uppercase; letter-spacing: .5px; }
.sum-val   { font-size: 36px; font-weight: 900; line-height: 1; margin-bottom: 4px; }
.sum-sub   { font-size: 11px; color: var(--light); }
.green-text  { color: #10b981; }
.yellow-text { color: #f59e0b; }
.red-text    { color: #ef4444; }

/* ── LEAFLET MAP ──────────────────────────────────────────── */
#zone-map { z-index:1 !important; min-height:400px; }
.leaflet-container { font-family:'Plus Jakarta Sans',sans-serif !important; }
.leaflet-tile-pane { z-index:2 !important; }
.leaflet-overlay-pane { z-index:4 !important; }
.leaflet-marker-pane { z-index:6 !important; }
.leaflet-popup-pane { z-index:7 !important; }
.leaflet-control { z-index:8 !important; }

/* ── RESPONSIVE ───────────────────────────────────────────── */
@media (max-width: 1300px) { .qa-grid { grid-template-columns: repeat(3,1fr); } }
@media (max-width: 1100px) { .stat-grid { grid-template-columns: repeat(2,1fr); } .two-col { grid-template-columns: 1fr; } .three-col { grid-template-columns: 1fr 1fr; } }
@media (max-width: 900px)  { .sensor-grid { grid-template-columns: 1fr 1fr; } }
@media (max-width: 768px)  { #content { padding: 16px; } .qa-grid { grid-template-columns: repeat(2,1fr); } .welcome-banner { flex-direction: column; gap: 16px; } }
@media (max-width: 600px)  { .sensor-grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <aside id="sidebar">
    <div class="sb-logo">
      <div class="sb-logo-icon">📡</div>
      <div class="sb-logo-text"><div class="sb-logo-name">LibraryQuiet</div><div class="sb-logo-sub">Noise Monitor v1.0</div></div>
    </div>
    <div class="sb-user">
      <div class="sb-user-label">Logged in as</div>
      <div class="sb-user-chip">
        <div class="sb-av"><?= $initial ?></div>
        <div><div class="sb-uname"><?= htmlspecialchars($user['name']) ?></div><div class="sb-urole">📋 Library Manager</div></div>
      </div>
    </div>
    <nav>
      <div class="sb-sec">Main</div>
      <a class="nav-item active" href="/NOISE_MONITOR/dashboards/manager/dashboard-manager.php"><span class="ni">⊞</span><span class="nl">Dashboard</span></a>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/monitor-manager.php"><span class="ni">◎</span><span class="nl">Live Monitoring</span></a>
      <div class="sb-div"></div>
      <div class="sb-sec">Management</div>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/alerts-manager.php"><span class="ni">⚑</span><span class="nl">Alert Logs</span><span class="nb" id="alert-nb">0</span></a>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/reports-manager.php"><span class="ni">▤</span><span class="nl">Reports</span></a>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/zones.php"><span class="ni">▦</span><span class="nl">Zone Management</span></a>
    </nav>
    <div class="sb-bottom">
      <div class="sb-av-lg"><?= $initial ?></div>
      <div class="sb-binfo"><div class="sb-bname"><?= htmlspecialchars($user['name']) ?></div><div class="sb-brole">Library Manager</div></div>
      <a class="sb-logout" href="/NOISE_MONITOR/logout.php" title="Logout">⏻</a>
      <button class="sb-toggle" onclick="toggleSidebar()">◀</button>
    </div>
  </aside>

  <div id="main">
    <header id="topbar">
      <div><div class="tb-title">Manager Dashboard</div><div class="tb-date" id="tb-date">Loading...</div></div>
      <div class="tb-right">
    
        <div class="live-badge"><span class="live-dot"></span><span class="live-text">LIVE</span></div>
       
        <div class="tb-av"><?= $initial ?></div>
      </div>
    </header>

    <div id="content">
      <div class="welcome-banner">
      </div>

      <div class="stat-grid">
        <div class="stat-card blue"><div class="stat-top"><div><div class="stat-label">Avg. Noise Level</div><div class="stat-val" id="s-avg">-- dB</div><div class="stat-sub">Across all zones</div></div><div class="stat-icon">🔊</div></div><div class="stat-trend" id="s-avg-trend"></div></div>
        <div class="stat-card green"><div class="stat-top"><div><div class="stat-label">Quiet Zones</div><div class="stat-val" id="s-quiet">--</div><div class="stat-sub">Below 40 dB</div></div><div class="stat-icon">✅</div></div><div class="stat-trend trend-green">↑ Zones in compliance</div></div>
        <div class="stat-card yellow"><div class="stat-top"><div><div class="stat-label">Active Alerts</div><div class="stat-val" id="s-alerts">0</div><div class="stat-sub">Needs attention</div></div><div class="stat-icon">⚠️</div></div><div class="stat-trend trend-red" id="s-alert-trend"></div></div>
        <div class="stat-card red"><div class="stat-top"><div><div class="stat-label">Loud Zones</div><div class="stat-val" id="s-loud">--</div><div class="stat-sub">Above 60 dB</div></div><div class="stat-icon">🔴</div></div><div class="stat-trend trend-red" id="s-loud-lbl"></div></div>
      </div>

      <div class="sensor-panel">
        <div class="sensor-panel-title">📡 Manual Sensor Input <span style="font-size:11px;font-weight:600;background:#ccfbf1;color:#134e4a;padding:2px 10px;border-radius:20px;margin-left:8px;"></span></div>
        <div class="sensor-panel-sub"></div>
        <div class="sensor-grid" id="sensor-grid"></div>
        <div style="margin-top:14px;display:flex;gap:10px;flex-wrap:wrap;">
          <button onclick="clearAllSensors()" style="padding:8px 18px;background:#f1f5f9;border:1.5px solid #e2e8f0;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;color:#64748b;">✕ Clear All Overrides</button>
          <button onclick="simulateRandom()" style="padding:8px 18px;background:#f0fdfa;border:1.5px solid #99f6e4;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;color:#0d9488;">🔀 Simulate Random</button>
        </div>
      </div>
      <div class="card" style="margin-bottom:16px;">
        <div class="card-head-row">
          <div><div class="card-title">🗺️ Zone Map — NBSC Campus</div><div class="card-sub mb0">Live noise markers · Updates every 2s</div></div>
          <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            <span style="font-size:11px;font-weight:700;background:#d1fae5;color:#065f46;padding:4px 10px;border-radius:20px;">🟢 Quiet &lt;40</span>
            <span style="font-size:11px;font-weight:700;background:#fef3c7;color:#92400e;padding:4px 10px;border-radius:20px;">🟡 Moderate 40–60</span>
            <span style="font-size:11px;font-weight:700;background:#fee2e2;color:#991b1b;padding:4px 10px;border-radius:20px;">🔴 Loud &gt;60</span>
            <button onclick="if(window._lqMap)window._lqMap.setView([8.360235,124.868406],18);" style="padding:4px 12px;background:#eff6ff;border:1.5px solid #bfdbfe;color:#1d4ed8;border-radius:8px;font-size:11px;font-weight:700;cursor:pointer;">⌖ Reset</button>
          </div>
        </div>
        <div id="zone-map" style="height:400px;border-radius:12px;overflow:hidden;border:1px solid #e2e8f0;"></div>
      </div>

      <div class="two-col">
        <div class="card"><div class="card-title">Zone Noise Levels</div><div class="card-sub">Live readings per zone</div><div id="zone-bars"></div></div>
        <div class="card"><div class="card-title">Noise Trend Today</div><div class="card-sub">Average dB per hour</div><div class="chart-wrap" id="chart-wrap"></div><div class="chart-legend"><div class="cl-item"><div class="cl-dot" style="background:#3b82f6;"></div>Quiet (&lt;40)</div><div class="cl-item"><div class="cl-dot" style="background:#f59e0b;"></div>Moderate</div><div class="cl-item"><div class="cl-dot" style="background:#ef4444;"></div>Loud (&gt;60)</div></div></div>
      </div>

      <div class="card">
        <div class="card-head-row"><div><div class="card-title">Recent Alerts</div><div class="card-sub mb0">Latest noise violations — you can resolve these</div></div><a class="view-all" href="/NOISE_MONITOR/dashboards/manager/alerts-manager.php">View All →</a></div>
        <div class="tbl-wrap"><table><thead><tr><th>Time</th><th>Zone</th><th>Noise Level</th><th>Type</th><th>Message</th><th>Status</th><th>Action</th></tr></thead><tbody id="alerts-tbody"></tbody></table></div>
      </div>

      <div class="three-col">
        <div class="card sum-card"><div class="sum-icon" style="background:#d1fae5;">✅</div><div class="sum-label">Quiet Zones</div><div class="sum-val green-text" id="sum-quiet">0</div><div class="sum-sub">Below 40 dB</div></div>
        <div class="card sum-card"><div class="sum-icon" style="background:#fef3c7;">⚠️</div><div class="sum-label">Moderate Zones</div><div class="sum-val yellow-text" id="sum-moderate">0</div><div class="sum-sub">40 – 60 dB</div></div>
        <div class="card sum-card"><div class="sum-icon" style="background:#fee2e2;">🔴</div><div class="sum-label">Loud Zones</div><div class="sum-val red-text" id="sum-loud">0</div><div class="sum-sub">Above 60 dB</div></div>
      </div>
    </div>
  </div>

  <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
  <script src="/NOISE_MONITOR/app-data.js"></script>
  <script src="/NOISE_MONITOR/map-init.js"></script>
  <script>
// ============================================================
//  LibraryQuiet – dashboard-manager.js (PHP version)
//  Manager: manual sensor input + resolve alerts
//  Requires: app-data.js loaded first
// ============================================================

const HOURLY = [22,28,35,48,42,38,55,61,58,45,38,30];
const HOURS  = ['8AM','9AM','10AM','11AM','12PM','1PM','2PM','3PM','4PM','5PM','6PM','7PM'];

function noiseColor(db)  { return db<40?'#10b981':db<60?'#f59e0b':'#ef4444'; }
function noiseStatus(db) { return db<40?'quiet':db<60?'moderate':'loud'; }
function noiseLabel(db)  { return db<40?'Quiet':db<60?'Moderate':'Loud'; }
function statusStyle(s)  {
  if (s==='quiet')    return { bg:'#d1fae5', color:'#065f46', dot:'#10b981' };
  if (s==='moderate') return { bg:'#fef3c7', color:'#92400e', dot:'#f59e0b' };
  return                     { bg:'#fee2e2', color:'#991b1b', dot:'#ef4444' };
}
function el(id)        { return document.getElementById(id); }
function setText(id,v) { const e=el(id); if(e) e.textContent=v; }

// ── CLOCK ──────────────────────────────────────────────────
function startClock() {
  const update = () => {
    const now = new Date();
    setText('tb-date',
      now.toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'})
      + ' · ' + now.toLocaleTimeString('en-PH'));
  };
  update(); setInterval(update, 1000);
}

function toggleSidebar() { el('sidebar').classList.toggle('collapsed'); }

// ── STATS ──────────────────────────────────────────────────
function renderStats() {
  const zones = AppData.getZones();
  if (!zones.length) return;
  const avg    = Math.round(zones.reduce((a,z)=>a+z.level,0)/zones.length);
  const quiet  = zones.filter(z=>noiseStatus(z.level)==='quiet').length;
  const loud   = zones.filter(z=>noiseStatus(z.level)==='loud').length;
  const active = AppData.getActiveAlerts().length;

  setText('s-avg',    avg + ' dB');
  setText('s-quiet',  quiet + ' / ' + zones.length);
  setText('s-loud',   loud);
  setText('s-alerts', active);

  const qas = el('qa-alert-sub');
  if (qas) qas.textContent = active + ' active alert' + (active!==1?'s':'');

  const tr = el('s-avg-trend');
  if (tr) {
    if (avg<40)      { tr.textContent='↓ All zones in good range'; tr.className='stat-trend trend-green'; }
    else if (avg<60) { tr.textContent='→ Moderate overall level';  tr.className='stat-trend trend-blue'; }
    else             { tr.textContent='↑ Elevated noise detected'; tr.className='stat-trend trend-red'; }
  }
  const at = el('s-alert-trend');
  if (at) {
    at.textContent = active>0?`↑ ${active} alert${active!==1?'s':''} need attention`:'✓ No active alerts';
    at.className   = active>0?'stat-trend trend-red':'stat-trend trend-green';
  }
  const ll = el('s-loud-lbl');
  if (ll) {
    ll.textContent = loud>0?`↑ ${loud} zone${loud>1?'s':''} above threshold`:'✓ No loud zones';
    ll.className   = loud>0?'stat-trend trend-red':'stat-trend trend-green';
  }
}

// ── MANUAL SENSOR INPUT ────────────────────────────────────
function renderSensorGrid() {
  const grid = el('sensor-grid'); if (!grid) return;
  const zones     = AppData.getZones();
  const overrides = AppData.getSensorOverrides();

  grid.innerHTML = zones.map(z => {
    const col      = noiseColor(z.level);
    const status   = noiseStatus(z.level);
    const sc       = statusStyle(status);
    const pct      = Math.min(100,(z.level/90)*100).toFixed(1);
    const override = overrides[z.id];
    const isManual = z.manualOverride && override;

    return `
      <div class="sensor-card ${isManual?'manual':''}" id="scard-${z.id}">
        ${isManual?`<div class="sc-manual-badge">📡 Manual · Set at ${override.setAt}</div>`:''}
        <div class="sc-name">${z.name}</div>
        <div class="sc-id">${z.sensor} · Floor ${z.floor} · 🔋${z.battery||80}%</div>
        <div class="sc-level" id="slevel-${z.id}" style="color:${col};">${Math.round(z.level)} dB</div>
        <div class="sc-bar-track">
          <div class="sc-bar-fill" id="sbar-${z.id}" style="width:${pct}%;background:${col};"></div>
        </div>
        <div style="font-size:11px;color:#94a3b8;margin-bottom:8px;">
          <span style="background:${sc.bg};color:${sc.color};padding:2px 8px;border-radius:20px;font-weight:700;font-size:10px;">${noiseLabel(z.level)}</span>
          &nbsp;Warn: ${z.warnThreshold} dB &nbsp;Crit: ${z.critThreshold} dB
        </div>
        <div class="sc-input-row">
          <input class="sc-input" id="sinput-${z.id}" type="number" min="0" max="120" step="1"
            value="${Math.round(z.level)}" placeholder="0–120 dB"
            onkeydown="if(event.key==='Enter') setOneSensor('${z.id}')"/>
          <button class="sc-set-btn" onclick="setOneSensor('${z.id}')">Set</button>
          ${isManual?`<button class="sc-clear-btn" onclick="clearOneSensor('${z.id}')">✕</button>`:''}
        </div>
      </div>`;
  }).join('');
}

function updateSensorGrid() {
  AppData.getZones().forEach(z => {
    const col     = noiseColor(z.level);
    const pct     = Math.min(100,(z.level/90)*100).toFixed(1);
    const levelEl = el('slevel-'+z.id);
    const barEl   = el('sbar-'+z.id);
    if (levelEl) { levelEl.textContent = Math.round(z.level)+' dB'; levelEl.style.color=col; }
    if (barEl)   { barEl.style.width=pct+'%'; barEl.style.background=col; }
  });
}

async function setOneSensor(zoneId) {
  const input = el('sinput-'+zoneId);
  if (!input) return;
  const val = parseFloat(input.value);
  if (isNaN(val)||val<0||val>120) {
    input.style.borderColor='#ef4444';
    setTimeout(()=>input.style.borderColor='',1500);
    return;
  }
  await AppData.setSensorLevel(zoneId, val); // auto creates alert if above threshold
  await AppData.loadZones();
  await AppData.loadAlerts();
  renderSensorGrid(); renderStats(); renderZoneBars(); renderSummary(); renderAlerts();
  AppData.updateNotifBadge();
  showToast(`✅ ${AppData.getZone(zoneId)?.name||'Zone'} set to ${Math.round(val)} dB`);
}

async function clearOneSensor(zoneId) {
  await AppData.clearSensorOverride(zoneId);
  await AppData.loadZones();
  renderSensorGrid(); renderStats(); renderZoneBars(); renderSummary();
  showToast(`↩ Override cleared for ${AppData.getZone(zoneId)?.name||'zone'}`);
}

async function clearAllSensors() {
  const zones = AppData.getZones();
  for (const z of zones) { await AppData.clearSensorOverride(z.id); }
  await AppData.loadZones();
  renderSensorGrid(); renderStats(); renderZoneBars(); renderSummary();
  showToast('↩ All manual overrides cleared');
}

async function simulateRandom() {
  const zones = AppData.getZones();
  for (const z of zones) {
    await AppData.setSensorLevel(z.id, Math.floor(Math.random()*90)+10);
  }
  await AppData.loadZones(); // this also triggers check_alerts
  await AppData.loadAlerts();
  renderSensorGrid(); renderStats(); renderZoneBars(); renderSummary(); renderAlerts();
  AppData.updateNotifBadge();
  showToast('🔀 Random noise levels simulated');
}

function showToast(msg) {
  let toast = el('mgr-toast');
  if (!toast) {
    toast = document.createElement('div');
    toast.id = 'mgr-toast';
    toast.style.cssText = 'position:fixed;bottom:24px;right:24px;background:#0f172a;color:#fff;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;z-index:9999;box-shadow:0 4px 20px rgba(0,0,0,.25);font-family:"Plus Jakarta Sans",sans-serif;transition:all .3s;opacity:0;transform:translateY(10px);';
    document.body.appendChild(toast);
  }
  toast.textContent = msg;
  toast.style.opacity = '1'; toast.style.transform = 'translateY(0)';
  clearTimeout(toast._timer);
  toast._timer = setTimeout(()=>{ toast.style.opacity='0'; toast.style.transform='translateY(10px)'; }, 3000);
}

// ── RESOLVE ALERTS ─────────────────────────────────────────
async function resolveAlert(alertId) {
  const session = AppData.getSession();
  const name    = session?.name || 'Library Manager';
  await AppData.resolveAlert(alertId, name);
  await AppData.loadAlerts();
  renderAlerts(); renderStats();
  AppData.updateNotifBadge();
  showToast('✅ Alert resolved');
}

// ── ZONE BARS ──────────────────────────────────────────────
function renderZoneBars() {
  const wrap = el('zone-bars'); if (!wrap) return;
  wrap.innerHTML = AppData.getZones().map(z => {
    const s=noiseStatus(z.level), sc=statusStyle(s);
    const pct=Math.min(100,(z.level/90)*100).toFixed(1), col=noiseColor(z.level);
    return `<div class="zone-row">
      <div class="zone-meta">
        <div class="zone-left">
          <div class="zone-dot" style="background:${sc.dot};"></div>
          <span class="zone-name">${z.name}</span>
          <span class="zone-floor">${z.floor}</span>
          ${z.manualOverride?'<span style="font-size:9px;background:#ccfbf1;color:#134e4a;padding:1px 6px;border-radius:4px;font-weight:700;">📡 Manual</span>':''}
        </div>
        <div class="zone-right">
          <span class="zone-db" style="color:${col};">${Math.round(z.level)} dB</span>
          <span class="zone-badge" style="background:${sc.bg};color:${sc.color};">${s}</span>
        </div>
      </div>
      <div class="bar-track"><div class="bar-fill" style="width:${pct}%;background:${col};"></div></div>
    </div>`;
  }).join('');
}

// ── CHART ──────────────────────────────────────────────────
function renderChart() {
  const wrap = el('chart-wrap'); if (!wrap) return;
  const max = Math.max(...HOURLY);
  wrap.innerHTML = HOURLY.map((v,i) => {
    const h=Math.round((v/max)*120), bg=v>=60?'#ef4444':v>=40?'#f59e0b':'#3b82f6', pk=v===max;
    return `<div class="chart-col">
      ${pk?`<div style="font-size:9px;color:#ef4444;font-weight:700;margin-bottom:2px;">${v}</div>`:''}
      <div class="chart-bar" style="height:${h}px;background:${bg};${pk?'box-shadow:0 0 8px rgba(239,68,68,.4);':''}"></div>
      <span class="chart-lbl">${HOURS[i]}</span>
    </div>`;
  }).join('');
}

// ── ALERTS TABLE WITH RESOLVE ──────────────────────────────
function renderAlerts() {
  const tbody = el('alerts-tbody'); if (!tbody) return;
  tbody.innerHTML = AppData.getAlerts().slice(0,5).map(a => {
    const tb = a.type==='critical'?'<span class="badge b-red">Critical</span>':a.type==='warning'?'<span class="badge b-yellow">Warning</span>':'<span class="badge b-green">Info</span>';
    const sb = a.status==='active'?'<span class="badge b-red">Active</span>':'<span class="badge b-gray">Resolved</span>';
    const action = a.status==='active'
      ? `<button onclick="resolveAlert('${a.id}')" style="padding:5px 12px;background:#0d9488;color:#fff;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;">✅ Resolve</button>`
      : `<span style="font-size:11px;color:#94a3b8;">By ${a.resolvedBy||'Manager'}</span>`;
    return `<tr>
      <td style="color:#64748b;font-size:12px;">${a.time}</td>
      <td style="font-weight:700;">${a.zone}</td>
      <td><span style="font-weight:900;color:${noiseColor(a.level)};">${a.level} dB</span></td>
      <td>${tb}</td>
      <td style="color:#64748b;">${a.msg}</td>
      <td>${sb}</td>
      <td>${action}</td>
    </tr>`;
  }).join('');
}

// ── SUMMARY ────────────────────────────────────────────────
function renderSummary() {
  const zones = AppData.getZones();
  setText('sum-quiet',    zones.filter(z=>noiseStatus(z.level)==='quiet').length);
  setText('sum-moderate', zones.filter(z=>noiseStatus(z.level)==='moderate').length);
  setText('sum-loud',     zones.filter(z=>noiseStatus(z.level)==='loud').length);
}

// ── LIVE UPDATE — reads from DB same as Admin ─────────────
function startLiveUpdate() {
  // Sync with server every 3s — same data source as admin
  setInterval(async () => {
    await Promise.all([
      AppData.loadZones(),
      AppData.loadAlerts(),
      AppData.loadSensorOverrides(),
    ]);
    renderStats();
    renderZoneBars();
    renderSummary();
    renderSensorGrid();
    renderAlerts();
    AppData.updateNotifBadge();
  }, 3000);
}

// ── INIT ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  // Use PHP-injected session
  if (window.__LQ_SESSION__) AppData._session = window.__LQ_SESSION__;

  // Load all data from API
  await Promise.all([
    AppData.loadZones(),
    AppData.loadAlerts(),
    AppData.loadSensorOverrides(),
    AppData.loadUsers(),
    AppData.loadReports(),
  ]);

  AppData.applySession();
  startClock();

  // Welcome banner with real name
  const session = AppData.getSession();
  if (session) {
    const firstName = session.name.split(' ')[0];
    const roleIcon  = '📋';
    const wbTitle   = el('wb-title');
    if (wbTitle) wbTitle.textContent = `Welcome back, ${firstName}! ${roleIcon}`;
  }

  renderStats();
  renderSensorGrid();
  renderZoneBars();
  renderChart();
  renderAlerts();
  renderSummary();
  startLiveUpdate();
});

  </script>
</body>
</html>