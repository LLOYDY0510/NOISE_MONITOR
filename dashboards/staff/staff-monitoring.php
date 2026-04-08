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
  <title>LibraryQuiet – Live Monitoring (Staff)</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <script>window.__LQ_SESSION__ = <?= json_encode($user) ?>;</script>
  <style>
/* ============================================================
   LibraryQuiet – monitoring-staff (combined)
   ============================================================ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--blue:#1d4ed8;--teal:#0d9488;--green:#10b981;--yellow:#f59e0b;--red:#ef4444;--bg:#f0f4f8;--sidebar:#0f172a;--sb2:#1e293b;--border:#e2e8f0;--text:#0f172a;--muted:#64748b;--light:#94a3b8;--radius:16px}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);display:flex;height:100vh;overflow:hidden}
#sidebar{width:240px;background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;box-shadow:2px 0 24px rgba(0,0,0,.3);z-index:100;transition:width .3s}
#sidebar.collapsed{width:68px}
.sb-logo{padding:18px 15px;border-bottom:1px solid var(--sb2);display:flex;align-items:center;gap:12px;min-height:68px}
.sb-logo-icon{width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);box-shadow:0 4px 12px rgba(124,58,237,.35);display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
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
.sb-sec{font-size:10px;color:#334155;text-transform:uppercase;letter-spacing:1px;padding:8px 12px 4px;font-weight:700}
.sb-div{height:1px;background:var(--sb2);margin:4px 8px}
#sidebar.collapsed .sb-sec,#sidebar.collapsed .sb-div{display:none}
.nav-item{width:100%;display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;margin-bottom:2px;background:transparent;color:#64748b;font-size:13px;font-weight:500;font-family:'Plus Jakarta Sans',sans-serif;text-align:left;text-decoration:none;white-space:nowrap;transition:all .2s}
.nav-item:hover{background:var(--sb2);color:#cbd5e1}
.nav-item.active{background:linear-gradient(90deg,rgba(124,58,237,.9),rgba(139,92,246,.8));color:#fff;font-weight:700;box-shadow:0 2px 10px rgba(124,58,237,.3)}
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
.nav-locked{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;color:#475569;opacity:.4;cursor:not-allowed;font-size:13px;font-weight:600;user-select:none}
.lock-tag{margin-left:auto;font-size:11px}
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
.sensor-count{display:flex;align-items:center;gap:6px;background:#f0fdfa;border:1px solid #99f6e4;padding:5px 12px;border-radius:20px}
.sc-icon{font-size:13px}.sc-label{font-size:11px;font-weight:600;color:#0f766e}
#sensor-online{font-size:12px;font-weight:800;color:#0d9488}
.tb-bell{position:relative;width:36px;height:36px;border-radius:10px;background:#f8fafc;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:16px;text-decoration:none}
.tb-bc{position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:8px;border-radius:50%;width:15px;height:15px;display:flex;align-items:center;justify-content:center;font-weight:700;border:2px solid #fff}
.tb-av{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:14px}
#content{flex:1;overflow-y:auto;padding:24px;animation:pageIn .35s ease}
@keyframes pageIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}
.view-only-notice{display:flex;align-items:flex-start;gap:12px;background:#faf5ff;border:1.5px solid #c4b5fd;border-radius:12px;padding:12px 18px;margin-bottom:20px;font-size:13px;color:#5b21b6;font-weight:500}
.von-icon{font-size:16px;flex-shrink:0;margin-top:1px}
.overall-card{background:#fff;border-radius:var(--radius);padding:28px;box-shadow:0 1px 8px rgba(0,0,0,.07);margin-bottom:24px;display:flex;align-items:center;gap:40px}
.ov-label{font-size:11px;color:var(--light);text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:10px}
.ov-db{font-size:56px;font-weight:900;line-height:1;transition:color .5s}
.ov-status{font-size:14px;font-weight:700;margin-top:8px;transition:color .5s;margin-bottom:16px}
.ov-right{flex:1}
.mgr-actions{display:flex;gap:8px;flex-wrap:wrap;margin-top:8px}
.ma-btn{padding:7px 16px;background:#faf5ff;border:1.5px solid #c4b5fd;border-radius:8px;font-size:12px;font-weight:700;color:#7c3aed;text-decoration:none;transition:all .2s}
.ma-btn:hover{background:#ede9fe}
.meter-labels-top{display:flex;justify-content:space-between;font-size:10px;color:var(--light);font-weight:600;margin-bottom:6px}
.meter-track{height:22px;border-radius:12px;overflow:visible;position:relative;margin-bottom:8px;background:#f1f5f9}
.meter-zones{position:absolute;inset:0;border-radius:12px;overflow:hidden;display:flex}
.mz{height:100%}
.quiet-zone{width:44.4%;background:rgba(16,185,129,.15)}
.moderate-zone{width:22.2%;background:rgba(245,158,11,.15)}
.loud-zone{width:33.4%;background:rgba(239,68,68,.15)}
.meter-fill{position:absolute;top:0;left:0;height:100%;border-radius:12px;transition:width 1.2s ease,background .5s;opacity:.7}
.meter-needle{position:absolute;top:-6px;bottom:-6px;width:4px;border-radius:2px;background:#0f172a;box-shadow:0 0 8px rgba(0,0,0,.4);transition:left 1.2s ease}
.meter-needle::after{content:'';position:absolute;top:-4px;left:-5px;width:0;height:0;border-left:7px solid transparent;border-right:7px solid transparent;border-bottom:8px solid #0f172a}
.meter-labels-bot{display:flex;justify-content:space-between;font-size:10px;color:var(--light)}
.ov-legend{display:flex;gap:16px;margin-top:14px;flex-wrap:wrap}
.ol{font-size:11px;font-weight:600}
.green-text{color:#10b981}.yellow-text{color:#f59e0b}.red-text{color:#ef4444}
.section-label{font-size:12px;font-weight:700;color:var(--light);text-transform:uppercase;letter-spacing:.8px;margin-bottom:12px;display:flex;align-items:center;gap:10px}
.sl-note{font-size:11px;font-weight:500;color:#0f766e;background:#f0fdfa;padding:2px 10px;border-radius:20px;text-transform:none;letter-spacing:0;border:1px solid #99f6e4}
.zone-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.zone-card{background:#fff;border-radius:var(--radius);padding:20px;border:2px solid;box-shadow:0 1px 8px rgba(0,0,0,.06);transition:all .4s ease}
.zone-card.quiet{border-color:#a7f3d0;background:linear-gradient(135deg,#f0fdf4,#fff)}
.zone-card.moderate{border-color:#fde68a;background:linear-gradient(135deg,#fffdf0,#fff)}
.zone-card.loud{border-color:#fca5a5;background:linear-gradient(135deg,#fff5f5,#fff);animation:loudPulse 2s infinite}
@keyframes loudPulse{0%,100%{box-shadow:0 0 0 rgba(239,68,68,0)}50%{box-shadow:0 0 16px rgba(239,68,68,.2)}}
.zc-header{display:flex;justify-content:space-between;align-items:flex-start;margin-bottom:14px}
.zc-name{font-weight:800;font-size:14px;color:#1e293b;margin-bottom:2px}.zc-floor{font-size:11px;color:var(--light)}
.zc-badge{font-size:10px;padding:3px 10px;border-radius:20px;font-weight:700;text-transform:uppercase}
.zc-db-row{display:flex;align-items:flex-end;gap:6px;margin-bottom:10px}
.zc-db{font-size:48px;font-weight:900;line-height:1;transition:color .5s}
.zc-unit{font-size:16px;font-weight:600;color:var(--muted);padding-bottom:6px}
.zc-bar-track{height:8px;background:rgba(0,0,0,.07);border-radius:4px;overflow:hidden;margin-bottom:14px}
.zc-bar-fill{height:100%;border-radius:4px;transition:width 1.2s ease,background .5s}
.zc-footer{display:flex;justify-content:space-between;align-items:center}
.zc-occ{font-size:11px;color:var(--muted);display:flex;align-items:center;gap:4px}
.zc-pulse{width:8px;height:8px;border-radius:50%;animation:pulse 1.5s infinite}
.zc-limit{font-size:10px;color:var(--light);font-family:'JetBrains Mono',monospace}
.card{background:#fff;border-radius:var(--radius);padding:22px;box-shadow:0 1px 8px rgba(0,0,0,.07)}
.card-title{font-weight:800;font-size:15px;color:var(--text);margin-bottom:4px}
.card-sub{font-size:12px;color:var(--light);margin-bottom:18px}.card-sub.mb0{margin-bottom:0}
.card-head-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px}
.all-online-badge{display:flex;align-items:center;gap:6px;background:#d1fae5;padding:5px 12px;border-radius:20px;font-size:11px;font-weight:700;color:#065f46}
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead tr{border-bottom:2px solid #f1f5f9}
th{text-align:left;padding:8px 12px;font-size:11px;color:var(--light);font-weight:700;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
tbody tr{border-bottom:1px solid #f8fafc;transition:background .15s}
tbody tr:hover{background:#fafbfc}
td{padding:11px 12px;font-size:13px}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.b-green{background:#d1fae5;color:#065f46}.b-red{background:#fee2e2;color:#991b1b}
.mono{font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600}
.batt-wrap{display:flex;align-items:center;gap:6px}
.batt-bar{width:40px;height:8px;background:#f1f5f9;border-radius:4px;overflow:hidden}
.batt-fill{height:100%;border-radius:4px}.batt-pct{font-size:11px;font-weight:700}
.toast{position:fixed;bottom:24px;right:24px;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;z-index:999;box-shadow:0 4px 20px rgba(0,0,0,.15);transform:translateY(20px);opacity:0;transition:all .3s;pointer-events:none;font-family:'Plus Jakarta Sans',sans-serif}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{background:#065f46;color:#d1fae5}.toast.info{background:#134e4a;color:#ccfbf1}
@media(max-width:1200px){.zone-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:900px){.overall-card{flex-direction:column;gap:20px}}
@media(max-width:640px){.zone-grid{grid-template-columns:1fr}#content{padding:16px}}
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
        <div>
          <div class="sb-uname"><?= htmlspecialchars($user['name']) ?></div>
          <div class="sb-urole">👤 Library Staff</div>
        </div>
      </div>
    </div>
    <nav>
      <div class="sb-sec">Main</div>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/staff/staff.php"><span class="ni">⊞</span><span class="nl">Dashboard</span></a>
      <a class="nav-item active" href="/NOISE_MONITOR/dashboards/staff/staff-monitoring.php"><span class="ni">◎</span><span class="nl">Live Monitoring</span></a>
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
        <div class="tb-title">Live Monitoring</div>
        <div class="tb-date" id="tb-date">Loading...</div>
      </div>
      <div class="tb-right">
  
        <div class="live-badge"><span class="live-dot"></span><span class="live-text">LIVE</span></div>
        <div class="sensor-count">
          <span class="sc-icon">📡</span>
          <span id="sensor-online">0/0</span>
          <span class="sc-label">Sensors Online</span>
        </div>
        
        <div class="tb-av"><?= $initial ?></div>
      </div>
    </header>

    <div id="content">

  

      <div class="overall-card">
        <div class="ov-left">
          <div class="ov-label">Overall Library Noise</div>
          <div class="ov-db" id="ov-db">-- dB</div>
          <div class="ov-status" id="ov-status">Loading...</div>
          <div class="mgr-actions">
      
          </div>
        </div>
        <div class="ov-right">
          <div class="meter-labels-top"><span>Quiet</span><span>Moderate</span><span>Loud</span></div>
          <div class="meter-track">
            <div class="meter-zones">
              <div class="mz quiet-zone"></div>
              <div class="mz moderate-zone"></div>
              <div class="mz loud-zone"></div>
            </div>
            <div class="meter-fill" id="meter-fill"></div>
            <div class="meter-needle" id="meter-needle"></div>
          </div>
          <div class="meter-labels-bot"><span>0 dB</span><span>30</span><span>60</span><span>90 dB</span></div>
          <div class="ov-legend">
            <span class="ol green-text">● Quiet &lt;40 dB</span>
            <span class="ol yellow-text">● Moderate 40–60 dB</span>
            <span class="ol red-text">● Loud &gt;60 dB</span>
          </div>
        </div>
      </div>

      <div class="section-label">Zone Readings <span ></span></div>
      <div class="zone-grid" id="zone-grid"></div>

      <div class="card" style="margin-top:16px;">
        <div class="card-head-row">
          <div><div class="card-title">Sensor Status</div><div class="card-sub mb0">All connected noise sensor devices</div></div>
          <div class="all-online-badge"><span class="live-dot"></span> All Sensors Online</div>
        </div>
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr><th>Sensor ID</th><th>Zone</th><th>Floor</th><th>Current Reading</th><th>Status</th><th>Battery</th><th>Last Ping</th></tr>
            </thead>
            <tbody id="sensor-tbody"></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>

  <div class="toast" id="toast"></div>

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
  //  monitoring-staff.js (inlined)
  //  Staff: view only, no admin/manager controls
  // ============================================================

  function noiseColor(db)  { return db < 40 ? '#10b981' : db < 60 ? '#f59e0b' : '#ef4444'; }
  function noiseStatus(db) { return db < 40 ? 'quiet'   : db < 60 ? 'moderate' : 'loud'; }
  function statusStyle(s)  {
    if (s === 'quiet')    return { bg: '#d1fae5', color: '#065f46', dot: '#10b981' };
    if (s === 'moderate') return { bg: '#fef3c7', color: '#92400e', dot: '#f59e0b' };
    return                       { bg: '#fee2e2', color: '#991b1b', dot: '#ef4444' };
  }
  function battColor(p) { return p > 60 ? '#10b981' : p > 30 ? '#f59e0b' : '#ef4444'; }
  function el(id)        { return document.getElementById(id); }
  function setText(id, v){ const e = el(id); if (e) e.textContent = v; }

  function startClock() {
    const update = () => {
      const now = new Date();
      setText('tb-date', now.toLocaleDateString('en-PH', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) + ' · ' + now.toLocaleTimeString('en-PH'));
    };
    update(); setInterval(update, 1000);
  }

  function toggleSidebar() { el('sidebar').classList.toggle('collapsed'); }

  function applySession() {
    const session = AppData.getSession();
    if (!session) { window.location.href = 'login.php'; return; }
    const initial = session.name[0].toUpperCase();
    ['sb-av', 'sb-av-lg', 'tb-av'].forEach(id => {
      const e = el(id); if (e) e.textContent = initial;
    });
    setText('sb-uname', session.name);
    setText('sb-bname', session.name);
  }

  function renderOverall() {
    const zones  = AppData.getZones();
    const avg    = zones.reduce((a, z) => a + z.level, 0) / zones.length;
    const avgR   = Math.round(avg);
    const status = noiseStatus(avgR);
    const color  = noiseColor(avgR);
    const pct    = Math.min(100, (avgR / 90) * 100);

    const dbEl = el('ov-db');
    if (dbEl) { dbEl.textContent = avgR + ' dB'; dbEl.style.color = color; }

    const stEl = el('ov-status');
    if (stEl) {
      const labels = {
        quiet:    '✅ Quiet — Library is peaceful',
        moderate: '⚠️ Moderate — Some noise detected',
        loud:     '🔴 Loud — Noise exceeds acceptable level'
      };
      stEl.textContent = labels[status]; stEl.style.color = color;
    }

    const fill   = el('meter-fill');
    const needle = el('meter-needle');
    if (fill)   { fill.style.width = pct + '%'; fill.style.background = color; }
    if (needle) needle.style.left = `calc(${pct}% - 2px)`;
  }

  function renderZoneGrid() {
    const grid = el('zone-grid'); if (!grid) return;
    const zones = AppData.getZones();
    grid.innerHTML = zones.map(z => {
      const status = noiseStatus(z.level);
      const sc  = statusStyle(status);
      const col = noiseColor(z.level);
      const pct = Math.min(100, (z.level / 90) * 100).toFixed(1);
      return `
        <div class="zone-card ${status}">
          <div class="zc-header">
            <div>
              <div class="zc-name">${z.name}</div>
              <div class="zc-floor">Floor ${z.floor}</div>
            </div>
            <span class="zc-badge" id="zbadge-${z.id}" style="background:${sc.bg};color:${sc.color};">
              ${status.toUpperCase()}
            </span>
          </div>
          <div class="zc-db-row">
            <div class="zc-db" id="zdb-${z.id}" style="color:${col};">${Math.round(z.level)}</div>
            <div class="zc-unit">dB</div>
          </div>
          <div class="zc-bar-track">
            <div class="zc-bar-fill" id="zbar-${z.id}" style="width:${pct}%;background:${col};"></div>
          </div>
          <div class="zc-footer">
            <div class="zc-occ">
              <span class="zc-pulse" style="background:${col};"></span>
              👥 ${z.occupied}/${z.capacity}
            </div>
            <div class="zc-limit">Limit: ${z.critThreshold} dB</div>
          </div>
        </div>`;
    }).join('');
  }

  function updateZoneCards() {
    AppData.getZones().forEach(z => {
      const status = noiseStatus(z.level);
      const sc  = statusStyle(status);
      const col = noiseColor(z.level);
      const pct = Math.min(100, (z.level / 90) * 100).toFixed(1);
      const dbEl  = el('zdb-'    + z.id);
      const barEl = el('zbar-'   + z.id);
      const bdgEl = el('zbadge-' + z.id);
      if (dbEl)  { dbEl.textContent  = Math.round(z.level); dbEl.style.color = col; }
      if (barEl) { barEl.style.width = pct + '%'; barEl.style.background = col; }
      if (bdgEl) { bdgEl.textContent = status.toUpperCase(); bdgEl.style.background = sc.bg; bdgEl.style.color = sc.color; }
    });
  }

  function renderSensorTable() {
    const tbody = el('sensor-tbody'); if (!tbody) return;
    const zones = AppData.getZones();
    const total = zones.length;
    const sonEl = el('sensor-online');
    if (sonEl) sonEl.textContent = `${total}/${total}`;
    tbody.innerHTML = zones.map((z, i) => {
      const col = noiseColor(z.level);
      const bc  = battColor(z.battery || 80);
      return `<tr>
        <td class="mono">SNS-00${i + 1}</td>
        <td style="font-weight:700;">${z.name}</td>
        <td>${z.floor}</td>
        <td><span id="sval-${z.id}" style="font-weight:900;color:${col};font-family:'JetBrains Mono',monospace;">${Math.round(z.level)} dB</span></td>
        <td><span class="badge b-green">● Online</span></td>
        <td>
          <div class="batt-wrap">
            <div class="batt-bar"><div class="batt-fill" style="width:${z.battery || 80}%;background:${bc};"></div></div>
            <span class="batt-pct" style="color:${bc};">${z.battery || 80}%</span>
          </div>
        </td>
        <td style="color:var(--light);font-size:12px;">Just now</td>
      </tr>`;
    }).join('');
  }

  function updateSensorTable() {
    AppData.getZones().forEach(z => {
      const col  = noiseColor(z.level);
      const sval = el('sval-' + z.id);
      if (sval) { sval.textContent = Math.round(z.level) + ' dB'; sval.style.color = col; }
    });
  }

  function startLiveUpdate() {
    setInterval(async () => {
      await Promise.all([AppData.loadZones(), AppData.loadAlerts()]);
      renderOverall();
      updateZoneCards();
      updateSensorTable();
      AppData.updateNotifBadge();
    }, 2000);
  }

  document.addEventListener('DOMContentLoaded', async () => {
    if (window.__LQ_SESSION__) AppData._session = window.__LQ_SESSION__;
    await Promise.all([AppData.loadZones(), AppData.loadAlerts(), AppData.loadSensorOverrides()]);
    applySession();
    startClock();
    renderOverall();
    renderZoneGrid();
    renderSensorTable();
    AppData.updateNotifBadge();
    startLiveUpdate();
  });
  </script>
</body>
</html>