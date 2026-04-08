<?php
require_once __DIR__ . '/../../includes/config.php';
requireRole('Library Staff');
$user    = currentUser();
$initial = strtoupper(substr($user['name'], 0, 1));
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LibraryQuiet – Staff Dashboard</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <script>window.__LQ_SESSION__ = <?= json_encode($user) ?>;</script>
  <style>
/* ============================================================
   LibraryQuiet – dashboard-staff (combined)
   ============================================================ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--blue:#1d4ed8;--blue2:#3b82f6;--green:#10b981;--yellow:#f59e0b;--red:#ef4444;--teal:#0d9488;--bg:#f0f4f8;--sidebar:#0f172a;--sb2:#1e293b;--border:#e2e8f0;--text:#0f172a;--muted:#64748b;--light:#94a3b8;--radius:16px}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);display:flex;height:100vh;overflow:hidden}
#sidebar{width:240px;background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;box-shadow:2px 0 24px rgba(0,0,0,.3);z-index:100;transition:width .3s ease}
#sidebar.collapsed{width:68px}
.sb-logo{padding:18px 15px;border-bottom:1px solid var(--sb2);display:flex;align-items:center;gap:12px;min-height:68px}
.sb-logo-icon{width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0;box-shadow:0 4px 12px rgba(124,58,237,.35)}
.sb-logo-name{color:#fff;font-weight:800;font-size:14px}.sb-logo-sub{color:#475569;font-size:11px}
.sb-logo-text{transition:opacity .2s}
#sidebar.collapsed .sb-logo-text{opacity:0;pointer-events:none}
.sb-user{padding:10px 12px;border-bottom:1px solid var(--sb2)}
.sb-user-label{font-size:10px;color:#475569;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
.sb-user-chip{background:var(--sb2);border-radius:10px;padding:8px 10px;display:flex;align-items:center;gap:8px}
.sb-av{width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:12px;flex-shrink:0}
.sb-uname{color:#e2e8f0;font-size:12px;font-weight:700}.sb-urole{color:#a78bfa;font-size:10px;font-weight:600}
#sidebar.collapsed .sb-user{display:none}
nav{flex:1;padding:10px 8px;overflow-y:auto;overflow-x:hidden}
nav::-webkit-scrollbar{width:3px}
nav::-webkit-scrollbar-thumb{background:#334155;border-radius:3px}
.sb-sec{font-size:10px;color:#334155;text-transform:uppercase;letter-spacing:1px;padding:8px 12px 4px;font-weight:700}
.sb-div{height:1px;background:var(--sb2);margin:4px 8px}
#sidebar.collapsed .sb-sec,#sidebar.collapsed .sb-div{display:none}
.nav-item{width:100%;display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;margin-bottom:2px;background:transparent;color:#64748b;font-size:13px;font-weight:500;font-family:'Plus Jakarta Sans',sans-serif;text-align:left;text-decoration:none;white-space:nowrap;transition:all .2s}
.nav-item:hover{background:var(--sb2);color:#cbd5e1}
.nav-item.active{background:linear-gradient(135deg,rgba(124,58,237,.15),rgba(139,92,246,.1));color:#7c3aed;font-weight:700;border-left:3px solid #7c3aed}
.nav-locked{display:flex;align-items:center;gap:10px;padding:10px 16px;border-radius:10px;color:#475569;opacity:.45;cursor:not-allowed;font-size:13px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;margin-bottom:2px;user-select:none}
.lock-tag{margin-left:auto;font-size:11px}
.ni{font-size:17px;width:22px;text-align:center;flex-shrink:0}.nl{transition:opacity .2s}
#sidebar.collapsed .nl{opacity:0;width:0;overflow:hidden}
#sidebar.collapsed .nav-item{justify-content:center;padding:10px 0}
.nb{margin-left:auto;background:#7c3aed;color:#fff;font-size:9px;font-weight:700;border-radius:20px;padding:2px 7px}
#sidebar.collapsed .nb{display:none}
.sb-bottom{padding:12px;border-top:1px solid var(--sb2);display:flex;align-items:center;gap:8px}
.sb-av-lg{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:13px;flex-shrink:0}
.sb-binfo{flex:1;min-width:0;overflow:hidden;transition:opacity .2s}
.sb-bname{color:#e2e8f0;font-size:12px;font-weight:700}.sb-brole{color:#475569;font-size:10px}
#sidebar.collapsed .sb-binfo{opacity:0;width:0}
.sb-logout{background:none;border:none;color:#475569;font-size:15px;text-decoration:none;padding:4px;transition:color .2s}
.sb-logout:hover{color:#ef4444}
.sb-toggle{background:none;border:none;color:#475569;font-size:13px;cursor:pointer;padding:4px;transition:transform .3s}
#sidebar.collapsed .sb-toggle{transform:rotate(180deg)}
#sidebar.collapsed .sb-logout{display:none}
#main{flex:1;display:flex;flex-direction:column;overflow:hidden}
#topbar{background:#fff;border-bottom:1px solid var(--border);padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 8px rgba(0,0,0,.05);flex-shrink:0}
.tb-title{font-weight:800;font-size:17px;color:var(--text)}.tb-date{font-size:11px;color:var(--light);margin-top:1px}
.tb-right{display:flex;align-items:center;gap:12px}
.role-badge{padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700}
.staff-badge{background:#faf5ff;color:#7c3aed;border:1.5px solid #c4b5fd;padding:5px 14px;border-radius:20px;font-size:12px;font-weight:700}
.live-badge{display:flex;align-items:center;gap:6px;background:#d1fae5;padding:5px 14px;border-radius:20px}
.live-dot{width:7px;height:7px;border-radius:50%;background:#10b981;animation:pulse 1.5s infinite}
.live-text{font-size:11px;font-weight:700;color:#065f46}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.tb-bell{position:relative;width:36px;height:36px;border-radius:10px;background:#f8fafc;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:16px;text-decoration:none}
.tb-bell:hover{background:#f1f5f9}
.tb-bc{position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:8px;border-radius:50%;width:15px;height:15px;display:flex;align-items:center;justify-content:center;font-weight:700;border:2px solid #fff}
.tb-av{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:14px}
#content{flex:1;overflow-y:auto;padding:24px;animation:pageIn .35s ease}
#content::-webkit-scrollbar{width:5px}
#content::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:5px}
@keyframes pageIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.welcome-banner{background:linear-gradient(135deg,#7c3aed 0%,#8b5cf6 60%,#a78bfa 100%);border-radius:var(--radius);padding:22px 28px;margin-bottom:20px;display:flex;justify-content:space-between;align-items:center;box-shadow:0 4px 20px rgba(124,58,237,.3)}
.wb-title{font-size:18px;font-weight:800;color:#fff;margin-bottom:4px}
.wb-sub{font-size:12px;color:rgba(255,255,255,.75)}
.wb-right{display:flex;gap:10px;flex-shrink:0}
.wb-btn{padding:9px 18px;background:#fff;color:#7c3aed;border-radius:10px;font-size:12px;font-weight:700;text-decoration:none;transition:all .2s}
.wb-btn:hover{background:#faf5ff}
.wb-btn-outline{background:transparent;color:#fff;border:1.5px solid rgba(255,255,255,.4)}
.wb-btn-outline:hover{background:rgba(255,255,255,.1)}
.access-notice{display:flex;align-items:flex-start;gap:12px;background:#faf5ff;border:1.5px solid #c4b5fd;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#5b21b6}
.an-icon{font-size:18px;flex-shrink:0;margin-top:1px}
.an-text{line-height:1.6}
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px}
.stat-card{background:#fff;border-radius:var(--radius);padding:20px;box-shadow:0 1px 8px rgba(0,0,0,.07);border-left:4px solid;transition:transform .2s,box-shadow .2s}
.stat-card:hover{transform:translateY(-2px);box-shadow:0 4px 16px rgba(0,0,0,.1)}
.stat-card.blue{border-color:#3b82f6}.stat-card.green{border-color:#10b981}
.stat-card.yellow{border-color:#f59e0b}.stat-card.red{border-color:#ef4444}
.stat-top{display:flex;justify-content:space-between;align-items:flex-start}
.stat-label{font-size:11px;color:var(--light);text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:6px}
.stat-val{font-size:30px;font-weight:900;color:var(--text);line-height:1}
.stat-sub{font-size:11px;color:var(--muted);margin-top:5px}
.stat-icon{font-size:26px;opacity:.8}
.stat-trend{font-size:11px;font-weight:700;margin-top:10px}
.trend-green{color:#10b981}.trend-red{color:#ef4444}.trend-blue{color:#3b82f6}
.quick-actions{margin-bottom:20px}
.qa-title{font-size:12px;font-weight:700;color:var(--light);text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px}
.qa-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:12px}
.qa-card{background:#fff;border-radius:14px;padding:16px 14px;box-shadow:0 1px 8px rgba(0,0,0,.06);text-align:center;text-decoration:none;transition:all .2s;border:1.5px solid transparent}
.qa-card:hover{transform:translateY(-3px);box-shadow:0 6px 20px rgba(0,0,0,.1);border-color:#c4b5fd}
.qa-locked{cursor:not-allowed;opacity:.45}
.qa-locked:hover{transform:none;box-shadow:0 1px 8px rgba(0,0,0,.06);border-color:transparent}
.qa-icon{width:40px;height:40px;border-radius:12px;display:flex;align-items:center;justify-content:center;font-size:18px;margin:0 auto 10px}
.qa-label{font-size:12px;font-weight:700;color:var(--text);margin-bottom:3px}
.qa-sub{font-size:10px;color:var(--light)}
.two-col{display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:16px}
.three-col{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;margin-top:16px}
.card{background:#fff;border-radius:var(--radius);padding:22px;box-shadow:0 1px 8px rgba(0,0,0,.07)}
.card-title{font-weight:800;font-size:15px;color:var(--text);margin-bottom:4px}
.card-sub{font-size:12px;color:var(--light);margin-bottom:18px}.card-sub.mb0{margin-bottom:0}
.card-head-row{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:18px}
.view-all{font-size:12px;font-weight:700;color:#7c3aed;text-decoration:none;padding:6px 14px;background:#faf5ff;border-radius:8px;transition:background .2s}
.view-all:hover{background:#ede9fe}
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead tr{border-bottom:2px solid #f1f5f9}
th{text-align:left;padding:8px 12px;font-size:11px;color:var(--light);font-weight:700;text-transform:uppercase;letter-spacing:.5px}
tbody tr{border-bottom:1px solid #f8fafc;transition:background .15s}
tbody tr:hover{background:#fafbfc}
td{padding:11px 12px;font-size:13px}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.b-red{background:#fee2e2;color:#991b1b}.b-yellow{background:#fef3c7;color:#92400e}
.b-green{background:#d1fae5;color:#065f46}.b-gray{background:#f1f5f9;color:#64748b}
.chart-wrap{display:flex;align-items:flex-end;gap:5px;height:130px;margin-bottom:12px}
.chart-legend{display:flex;gap:14px;flex-wrap:wrap;margin-top:4px}
.cl-item{display:flex;align-items:center;gap:5px;font-size:10px;color:var(--muted)}
.cl-dot{width:10px;height:10px;border-radius:3px}
.sum-card{text-align:center;padding:24px 16px}
.sum-icon{width:48px;height:48px;border-radius:14px;display:flex;align-items:center;justify-content:center;font-size:22px;margin:0 auto 12px}
.sum-label{font-size:11px;color:var(--light);font-weight:600;margin-bottom:6px;text-transform:uppercase;letter-spacing:.5px}
.sum-val{font-size:36px;font-weight:900;line-height:1;margin-bottom:4px}
.sum-sub{font-size:11px;color:var(--light)}
.green-text{color:#10b981}.yellow-text{color:#f59e0b}.red-text{color:#ef4444}
@media(max-width:1100px){.stat-grid{grid-template-columns:repeat(2,1fr)}.two-col{grid-template-columns:1fr}.three-col{grid-template-columns:1fr 1fr}}
@media(max-width:768px){#content{padding:16px}.qa-grid{grid-template-columns:repeat(2,1fr)}.welcome-banner{flex-direction:column;gap:16px}}
  </style>
</head>
<body>

  <aside id="sidebar">
    <div class="sb-logo">
      <div class="sb-logo-icon">📡</div>
      <div class="sb-logo-text">
        <div class="sb-logo-name">LibraryQuiet</div>
        <div class="sb-logo-sub">Noise Monitor v1.0</div>
      </div>
    </div>
    <div class="sb-user">
      <div class="sb-user-label">Logged in as</div>
      <div class="sb-user-chip">
        <div class="sb-av"><?= $initial ?></div>
        <div>
          <div class="sb-uname"><?= htmlspecialchars($user['name']) ?></div>
          <div class="sb-urole">👤 Library Staff</div>
        </div>
      </div>
    </div>
    <nav>
      <div class="sb-sec">Main</div>
      <a class="nav-item active" href="/NOISE_MONITOR/dashboards/staff/staff.php"><span class="ni">⊞</span><span class="nl">Dashboard</span></a>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/staff/staff-monitoring.php"><span class="ni">◎</span><span class="nl">Live Monitoring</span></a>
      <div class="sb-div"></div>
      <div class="sb-sec">View Only</div>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/staff/staff-alerts.php"><span class="ni">⚑</span><span class="nl">Alert Logs</span><span class="nb" id="alert-nb">0</span></a>
      <div class="sb-div"></div>
      <div class="sb-sec">Restricted</div>
      <div class="nav-item nav-locked"><span class="ni">▤</span><span class="nl">Reports</span><span class="lock-tag">🔒</span></div>
      <div class="nav-item nav-locked"><span class="ni">▦</span><span class="nl">Zone Management</span><span class="lock-tag">🔒</span></div>
      <div class="nav-item nav-locked"><span class="ni">◉</span><span class="nl">User Management</span><span class="lock-tag">🔒</span></div>
    </nav>
    <div class="sb-bottom">
      <div class="sb-av-lg"><?= $initial ?></div>
      <div class="sb-binfo">
        <div class="sb-bname"><?= htmlspecialchars($user['name']) ?></div>
        <div class="sb-brole">Library Staff</div>
      </div>
      <a class="sb-logout" href="/NOISE_MONITOR/logout.php" title="Logout">⏻</a>
      <button class="sb-toggle" onclick="toggleSidebar()">◀</button>
    </div>
  </aside>

  <div id="main">
    <header id="topbar">
      <div>
        <div class="tb-title">Staff Dashboard</div>
        <div class="tb-date" id="tb-date">Loading...</div>
      </div>
      <div class="tb-right">
       
        <div class="live-badge"><span class="live-dot"></span><span class="live-text">LIVE</span></div>
      
        <div class="tb-av"><?= $initial ?></div>
      </div>
    </header>

    <div id="content">


      <div class="stat-grid">
        <div class="stat-card blue"><div class="stat-top"><div><div class="stat-label">Avg. Noise Level</div><div class="stat-val" id="s-avg">-- dB</div><div class="stat-sub">Across all zones</div></div><div class="stat-icon">🔊</div></div><div class="stat-trend" id="s-avg-trend"></div></div>
        <div class="stat-card green"><div class="stat-top"><div><div class="stat-label">Quiet Zones</div><div class="stat-val" id="s-quiet">--</div><div class="stat-sub">Below 40 dB</div></div><div class="stat-icon">✅</div></div><div class="stat-trend trend-green">↑ Zones in compliance</div></div>
        <div class="stat-card yellow"><div class="stat-top"><div><div class="stat-label">Active Alerts</div><div class="stat-val" id="s-alerts">0</div><div class="stat-sub">Needs attention</div></div><div class="stat-icon">⚠️</div></div><div class="stat-trend trend-red" id="s-alert-trend"></div></div>
        <div class="stat-card red"><div class="stat-top"><div><div class="stat-label">Loud Zones</div><div class="stat-val" id="s-loud">--</div><div class="stat-sub">Above 60 dB</div></div><div class="stat-icon">🔴</div></div><div class="stat-trend trend-red" id="s-loud-lbl"></div></div>
      </div>
   

      <div class="two-col">
        <div class="card"><div class="card-title">Zone Noise Levels</div><div class="card-sub">Live readings — view only</div><div id="zone-bars"></div></div>
        <div class="card"><div class="card-title">Noise Trend Today</div><div class="card-sub">Average dB per hour</div><div class="chart-wrap" id="chart-wrap"></div><div class="chart-legend"><div class="cl-item"><div class="cl-dot" style="background:#3b82f6;"></div>Quiet (&lt;40)</div><div class="cl-item"><div class="cl-dot" style="background:#f59e0b;"></div>Moderate</div><div class="cl-item"><div class="cl-dot" style="background:#ef4444;"></div>Loud (&gt;60)</div></div></div>
      </div>

      <div class="card">
        <div class="card-head-row">
          <div><div class="card-title">Recent Alerts</div><div class="card-sub mb0">Latest noise violations — view only</div></div>
          <a class="view-all" href="/NOISE_MONITOR/dashboards/staff/staff-alerts.php">View All →</a>
        </div>
        <div class="tbl-wrap">
          <table>
            <thead><tr><th>Time</th><th>Zone</th><th>Noise Level</th><th>Type</th><th>Message</th><th>Status</th></tr></thead>
            <tbody id="alerts-tbody"></tbody>
          </table>
        </div>
      </div>

      <div class="three-col">
        <div class="card sum-card"><div class="sum-icon" style="background:#d1fae5;">✅</div><div class="sum-label">Quiet Zones</div><div class="sum-val green-text" id="sum-quiet">0</div><div class="sum-sub">Below 40 dB</div></div>
        <div class="card sum-card"><div class="sum-icon" style="background:#fef3c7;">⚠️</div><div class="sum-label">Moderate Zones</div><div class="sum-val yellow-text" id="sum-moderate">0</div><div class="sum-sub">40 – 60 dB</div></div>
        <div class="card sum-card"><div class="sum-icon" style="background:#fee2e2;">🔴</div><div class="sum-label">Loud Zones</div><div class="sum-val red-text" id="sum-loud">0</div><div class="sum-sub">Above 60 dB</div></div>
      </div>

    </div>
  </div>

  <script>
  // ============================================================
  //  AppData — app-data.js (inlined)
  // ============================================================
  const AppData = (() => {
    const API = '/NOISE_MONITOR/api.php';
    let _session = window.__LQ_SESSION__ || null;

    async function post(action, data = {}) {
      try {
        const res = await fetch(`${API}?action=${action}`, { method: 'POST', headers: { 'Content-Type': 'application/json' }, body: JSON.stringify(data) });
        return await res.json();
      } catch(e) { console.error(`API[${action}]:`, e); return { error: e.message }; }
    }
    async function get(action) {
      try {
        const res = await fetch(`${API}?action=${action}`);
        return await res.json();
      } catch(e) { console.error(`API[${action}]:`, e); return null; }
    }

    // SESSION
    async function loadSession() { const d = await get('session'); _session = d?.user || null; return _session; }
    function getSession()        { return _session; }
    function isAdmin()           { return _session?.role === 'Administrator'; }
    function isManager()         { return _session?.role === 'Library Manager'; }
    function isStaff()           { return _session?.role === 'Library Staff'; }
    async function clearSession(){ await post('logout'); _session = null; window.location.href = '/NOISE_MONITOR/logout.php'; }

    function applySession() {
      if (!_session) return;
      const name = _session.name || _session.email;
      const initial = name.charAt(0).toUpperCase();
      const isAdm = isAdmin(), isMgr = isManager();
      const roleIcon  = isAdm ? '👑 ' : isMgr ? '📋 ' : '👤 ';
      const roleColor = isAdm ? 'linear-gradient(135deg,#1d4ed8,#3b82f6)' : isMgr ? 'linear-gradient(135deg,#0d9488,#0f766e)' : 'linear-gradient(135deg,#7c3aed,#8b5cf6)';
      document.querySelectorAll('.sb-uname,.sb-bname').forEach(el => el.textContent = name);
      document.querySelectorAll('.sb-urole').forEach(el => el.textContent = roleIcon + _session.role);
      document.querySelectorAll('.sb-brole').forEach(el => el.textContent = _session.role);
      document.querySelectorAll('.sb-av,.sb-av-lg,.tb-av,.top-av').forEach(el => { el.textContent = initial; el.style.background = roleColor; });
      const badge = document.getElementById('role-badge');
      if (badge) { badge.textContent = roleIcon + _session.role; badge.className = 'role-badge ' + (isAdm ? 'admin-badge' : isMgr ? 'manager-badge' : 'staff-badge'); }
      updateNotifBadge();
    }

    // ZONES
    let _zones = [];
    async function loadZones()          { _zones = await get('get_zones') || []; return _zones; }
    function getZones()                  { return _zones; }
    function getZone(id)                 { return _zones.find(z => z.id === id); }
    async function setSensorLevel(zoneId, db) {
      await post('set_sensor', { id: zoneId, level: db });
      const z = _zones.find(z => z.id === zoneId);
      if (z) { z.level = db; z.manualOverride = true; }
      _alerts = await get('get_alerts') || _alerts;
    }
    async function clearSensorOverride(zoneId) { await post('clear_sensor', { id: zoneId }); const z = _zones.find(z => z.id === zoneId); if (z) z.manualOverride = false; }
    let _overrides = {};
    async function loadSensorOverrides() { _overrides = await get('get_sensor_overrides') || {}; return _overrides; }
    function getSensorOverrides()         { return _overrides; }
    async function addZone(z)             { return await post('add_zone', z); }
    async function updateZone(z)          { return await post('update_zone', z); }
    async function deleteZone(id)         { return await post('delete_zone', { id }); }

    // ALERTS
    let _alerts = [];
    async function loadAlerts()           { _alerts = await get('get_alerts') || []; return _alerts; }
    function getAlerts()                  { return _alerts; }
    function getActiveAlerts()            { return _alerts.filter(a => a.status === 'active'); }
    async function resolveAlert(id, by)   { await post('resolve_alert', { id, resolvedBy: by }); const a = _alerts.find(a => a.id === id); if (a) { a.status = 'resolved'; a.resolvedBy = by; } updateNotifBadge(); }
    async function addAlertMessage(alertId, message) { await post('add_alert_message', { alertId, message }); }
    function getAlertMessages(alertId)    { return _alerts.find(a => a.id === alertId)?.messages || []; }
    async function deleteAlert(id)        { await post('delete_alert', { id }); _alerts = _alerts.filter(a => a.id !== id); }
    async function addAlert(a)            { return await post('add_alert', a); }

    // REPORTS
    let _reports = [];
    async function loadReports()          { _reports = await get('get_reports') || []; return _reports; }
    function getReports()                 { return _reports; }
    function getUnreadReports()           { return _reports.filter(r => r.sentToAdmin && !r.adminReadAt); }
    async function addReport(r)           { return await post('add_report', r); }
    async function sendReportToAdmin(id)  { return await post('send_report', { id }); }
    async function markReportRead(id)     { return await post('mark_report_read', { id }); }
    async function deleteReport(id)       { await post('delete_report', { id }); _reports = _reports.filter(r => r.id !== id); }

    // USERS
    let _users = [];
    async function loadUsers()            { _users = await get('get_users') || []; return _users; }
    function getUsers()                   { return _users; }
    function getUserByEmail(email)        { return _users.find(u => u.email.toLowerCase() === email.toLowerCase()); }
    async function addUser(u)             { return await post('add_user', u); }
    async function updateUser(id, data)   { return await post('update_user', { id, ...data }); }
    async function deleteUser(id)         { return await post('delete_user', { id }); }
    async function updatePassword(id, pw) { return await post('update_password', { id, password: pw }); }

    // BADGE
    function updateNotifBadge() {
      const active = getActiveAlerts().length;
      ['alert-nb', 'bell-count'].forEach(id => {
        const el = document.getElementById(id);
        if (el) { el.textContent = active; el.style.display = active > 0 ? '' : 'none'; }
      });
    }

    async function loadAll() { await Promise.all([loadZones(), loadAlerts(), loadSensorOverrides(), loadUsers(), loadReports()]); }

    return {
      loadSession, getSession, isAdmin, isManager, isStaff, clearSession, applySession,
      loadZones, getZones, getZone, setSensorLevel, clearSensorOverride, loadSensorOverrides, getSensorOverrides, addZone, updateZone, deleteZone,
      loadAlerts, getAlerts, getActiveAlerts, resolveAlert, addAlertMessage, getAlertMessages, deleteAlert, addAlert,
      loadReports, getReports, getUnreadReports, addReport, sendReportToAdmin, markReportRead, deleteReport,
      loadUsers, getUsers, getUserByEmail, addUser, updateUser, deleteUser, updatePassword,
      updateNotifBadge, loadAll,
      get _session() { return _session; }, set _session(v) { _session = v; }
    };
  })();

  // ============================================================
  //  dashboard-staff.js (inlined)
  //  Staff: view only — no edit/resolve/delete
  // ============================================================

  const HOURLY = [22, 28, 35, 48, 42, 38, 55, 61, 58, 45, 38, 30];
  const HOURS  = ['8AM','9AM','10AM','11AM','12PM','1PM','2PM','3PM','4PM','5PM','6PM','7PM'];

  function noiseColor(db)  { return db < 40 ? '#10b981' : db < 60 ? '#f59e0b' : '#ef4444'; }
  function noiseStatus(db) { return db < 40 ? 'quiet' : db < 60 ? 'moderate' : 'loud'; }
  function el(id)          { return document.getElementById(id); }
  function setText(id, v)  { const e = el(id); if (e) e.textContent = v; }

  function startClock() {
    const u = () => { const n = new Date(); setText('tb-date', n.toLocaleDateString('en-PH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' · ' + n.toLocaleTimeString('en-PH')); };
    u(); setInterval(u, 1000);
  }

  function toggleSidebar() { el('sidebar').classList.toggle('collapsed'); }

  function renderStats() {
    const zones = AppData.getZones(); if (!zones.length) return;
    const avg    = Math.round(zones.reduce((a, z) => a + z.level, 0) / zones.length);
    const quiet  = zones.filter(z => noiseStatus(z.level) === 'quiet').length;
    const loud   = zones.filter(z => noiseStatus(z.level) === 'loud').length;
    const active = AppData.getActiveAlerts().length;
    setText('s-avg',   avg + ' dB');
    setText('s-quiet', quiet + ' / ' + zones.length);
    setText('s-loud',  loud);
    setText('s-alerts', active);
    const qas = el('qa-alert-sub'); if (qas) qas.textContent = active + ' active alert' + (active !== 1 ? 's' : '');
    const tr = el('s-avg-trend');
    if (tr) {
      if (avg < 40)      { tr.textContent = '↓ All zones quiet'; tr.className = 'stat-trend trend-green'; }
      else if (avg < 60) { tr.textContent = '→ Moderate level';  tr.className = 'stat-trend trend-blue'; }
      else               { tr.textContent = '↑ Elevated noise!'; tr.className = 'stat-trend trend-red'; }
    }
    const at = el('s-alert-trend');
    if (at) { at.textContent = active > 0 ? `↑ ${active} alert${active !== 1 ? 's' : ''} active` : '✓ No active alerts'; at.className = active > 0 ? 'stat-trend trend-red' : 'stat-trend trend-green'; }
    const ll = el('s-loud-lbl');
    if (ll) { ll.textContent = loud > 0 ? `↑ ${loud} zone${loud > 1 ? 's' : ''} above threshold` : '✓ No loud zones'; ll.className = loud > 0 ? 'stat-trend trend-red' : 'stat-trend trend-green'; }
    const bc = el('bell-count'); if (bc) { bc.textContent = active; bc.style.display = active > 0 ? '' : 'none'; }
    const nb = el('alert-nb');   if (nb) { nb.textContent = active; nb.style.display = active > 0 ? '' : 'none'; }
  }

  function renderZoneBars() {
    const wrap = el('zone-bars'); if (!wrap) return;
    wrap.innerHTML = AppData.getZones().map(z => {
      const s = noiseStatus(z.level), col = noiseColor(z.level);
      const pct = Math.min(100, (z.level / 90) * 100).toFixed(1);
      const bg = s === 'quiet' ? '#d1fae5' : s === 'moderate' ? '#fef3c7' : '#fee2e2';
      const tc = s === 'quiet' ? '#065f46' : s === 'moderate' ? '#92400e' : '#991b1b';
      return `<div style="margin-bottom:14px;">
        <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:5px;">
          <div style="display:flex;align-items:center;gap:8px;">
            <div style="width:8px;height:8px;border-radius:50%;background:${col};"></div>
            <span style="font-size:13px;font-weight:700;">${z.name}</span>
            <span style="font-size:10px;background:#f1f5f9;color:#64748b;padding:1px 6px;border-radius:4px;">${z.floor}</span>
          </div>
          <div style="display:flex;align-items:center;gap:8px;">
            <span style="font-size:14px;font-weight:900;color:${col};">${Math.round(z.level)} dB</span>
            <span style="font-size:10px;background:${bg};color:${tc};padding:2px 8px;border-radius:8px;font-weight:700;">${s}</span>
          </div>
        </div>
        <div style="height:7px;background:#f1f5f9;border-radius:4px;overflow:hidden;">
          <div style="width:${pct}%;height:100%;background:${col};border-radius:4px;transition:width 1s;"></div>
        </div>
      </div>`;
    }).join('');
  }

  function renderChart() {
    const wrap = el('chart-wrap'); if (!wrap) return;
    const max = Math.max(...HOURLY);
    wrap.innerHTML = HOURLY.map((v, i) => {
      const h  = Math.round((v / max) * 120);
      const bg = v >= 60 ? '#ef4444' : v >= 40 ? '#f59e0b' : '#3b82f6';
      const pk = v === max;
      return `<div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
        ${pk ? `<div style="font-size:9px;color:#ef4444;font-weight:700;">${v}</div>` : ''}
        <div style="width:100%;height:${h}px;background:${bg};border-radius:5px 5px 0 0;opacity:.85;"></div>
        <span style="font-size:9px;color:#94a3b8;">${HOURS[i]}</span>
      </div>`;
    }).join('');
  }

  function renderAlerts() {
    const tbody  = el('alerts-tbody'); if (!tbody) return;
    const alerts = AppData.getAlerts().slice(0, 5);
    if (!alerts.length) { tbody.innerHTML = `<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">No alerts yet.</td></tr>`; return; }
    tbody.innerHTML = alerts.map(a => {
      const tb = a.type === 'critical' ? '<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">🔴 Critical</span>' : a.type === 'warning' ? '<span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">⚠️ Warning</span>' : '<span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">ℹ Info</span>';
      const sb = a.status === 'active' ? '<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Active</span>' : '<span style="background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Resolved</span>';
      return `<tr>
        <td style="color:#64748b;font-size:12px;">${a.time}</td>
        <td style="font-weight:700;">${a.zone}</td>
        <td><span style="font-weight:900;color:${noiseColor(a.level)};">${a.level} dB</span></td>
        <td>${tb}</td>
        <td style="color:#64748b;font-size:12px;">${a.msg}</td>
        <td>${sb}</td>
      </tr>`;
    }).join('');
  }

  function renderSummary() {
    const zones = AppData.getZones();
    setText('sum-quiet',    zones.filter(z => noiseStatus(z.level) === 'quiet').length);
    setText('sum-moderate', zones.filter(z => noiseStatus(z.level) === 'moderate').length);
    setText('sum-loud',     zones.filter(z => noiseStatus(z.level) === 'loud').length);
  }

  function renderStaffInfo() {
    const wrap = el('staff-info'); if (!wrap) return;
    const session = AppData.getSession(); if (!session) return;
    wrap.innerHTML = `
      <div style="display:grid;grid-template-columns:repeat(3,1fr);gap:16px;">
        <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Name</div><div style="font-weight:700;">${session.name}</div></div>
        <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Email</div><div style="font-weight:600;font-size:13px;">${session.email}</div></div>
        <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Role</div><div style="font-weight:700;">👤 ${session.role}</div></div>
        <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Access Level</div><div style="background:#faf5ff;color:#7c3aed;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;">View Only</div></div>
        <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">System</div><div style="font-weight:600;">LibraryQuiet v1.0</div></div>
        <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Status</div><div style="background:#d1fae5;color:#065f46;padding:4px 12px;border-radius:20px;font-size:11px;font-weight:700;display:inline-block;">● Active</div></div>
      </div>`;
  }

  document.addEventListener('DOMContentLoaded', async () => {
    if (window.__LQ_SESSION__) AppData._session = window.__LQ_SESSION__;
    await Promise.all([AppData.loadZones(), AppData.loadAlerts()]);
    AppData.applySession();
    startClock();
    renderStats();
    renderZoneBars();
    renderChart();
    renderAlerts();
    renderSummary();
    renderStaffInfo();
    AppData.updateNotifBadge();

    setInterval(async () => {
      await Promise.all([AppData.loadZones(), AppData.loadAlerts()]);
      renderStats();
      renderZoneBars();
      renderSummary();
      renderAlerts();
      AppData.updateNotifBadge();
    }, 5000);
  });
  </script>
</body>
</html>