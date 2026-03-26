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
  <title>LibraryQuiet – Alert Logs (Staff)</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/NOISE_MONITOR/assets/alerts-staff.css"/>
  <script>window.__LQ_SESSION__ = <?= json_encode($user) ?>;</script>
  <style>
    /* ============================================================
   LibraryQuiet – alerts-staff.css (purple staff theme)
   ============================================================ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--purple:#7c3aed;--purple2:#8b5cf6;--green:#10b981;--yellow:#f59e0b;--red:#ef4444;--blue2:#3b82f6;--bg:#f0f4f8;--sidebar:#0f172a;--sb2:#1e293b;--border:#e2e8f0;--text:#0f172a;--muted:#64748b;--light:#94a3b8;--radius:16px}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);display:flex;height:100vh;overflow:hidden}

/* ── SIDEBAR ──────────────────────────────────────────────── */
#sidebar{width:240px;background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;box-shadow:2px 0 24px rgba(0,0,0,.3);z-index:100;transition:width .3s}
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
.sb-sec{font-size:10px;color:#334155;text-transform:uppercase;letter-spacing:1px;padding:8px 12px 4px;font-weight:700}
.sb-div{height:1px;background:var(--sb2);margin:4px 8px}
#sidebar.collapsed .sb-sec,#sidebar.collapsed .sb-div{display:none}
.nav-item{width:100%;display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;margin-bottom:2px;background:transparent;color:#64748b;font-size:13px;font-weight:500;font-family:'Plus Jakarta Sans',sans-serif;text-align:left;text-decoration:none;white-space:nowrap;transition:all .2s}
.nav-item:hover{background:var(--sb2);color:#cbd5e1}
.nav-item.active{background:linear-gradient(90deg,rgba(124,58,237,.9),rgba(139,92,246,.8));color:#fff;font-weight:700;box-shadow:0 2px 10px rgba(124,58,237,.3)}
.nav-locked{display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;margin-bottom:2px;color:#475569;opacity:.4;cursor:not-allowed;font-size:13px;font-weight:600;font-family:'Plus Jakarta Sans',sans-serif;user-select:none}
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
.sb-logout{background:none;border:none;color:#475569;font-size:15px;text-decoration:none;padding:4px;transition:color .2s;cursor:pointer}
.sb-logout:hover{color:#ef4444}
.sb-toggle{background:none;border:none;color:#475569;font-size:13px;cursor:pointer;padding:4px;transition:transform .3s}
#sidebar.collapsed .sb-toggle{transform:rotate(180deg)}
#sidebar.collapsed .sb-logout{display:none}

/* ── MAIN ─────────────────────────────────────────────────── */
#main{flex:1;display:flex;flex-direction:column;overflow:hidden}
#topbar{background:#fff;border-bottom:1px solid var(--border);padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 8px rgba(0,0,0,.05);flex-shrink:0}
.tb-title{font-weight:800;font-size:17px;color:var(--text)}.tb-date{font-size:11px;color:var(--light);margin-top:1px}
.tb-right{display:flex;align-items:center;gap:12px}
.role-badge{padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700}
.staff-badge{background:#faf5ff;color:#7c3aed;border:1.5px solid #c4b5fd}
.live-badge{display:flex;align-items:center;gap:6px;background:#d1fae5;padding:5px 14px;border-radius:20px}
.live-dot{width:7px;height:7px;border-radius:50%;background:#10b981;animation:pulse 1.5s infinite}
.live-text{font-size:11px;font-weight:700;color:#065f46}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.tb-bell{position:relative;width:36px;height:36px;border-radius:10px;background:#f8fafc;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:16px;text-decoration:none}
.tb-bc{position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:8px;border-radius:50%;width:15px;height:15px;display:flex;align-items:center;justify-content:center;font-weight:700;border:2px solid #fff}
.tb-av{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#7c3aed,#8b5cf6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:14px}

/* ── CONTENT ──────────────────────────────────────────────── */
#content{flex:1;overflow-y:auto;padding:24px;animation:pageIn .35s ease}
@keyframes pageIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}

/* ── VIEW ONLY NOTICE ─────────────────────────────────────── */
.view-only-notice{display:flex;align-items:flex-start;gap:12px;background:#faf5ff;border:1.5px solid #c4b5fd;border-radius:12px;padding:14px 18px;margin-bottom:20px;font-size:13px;color:#5b21b6}
.von-icon{font-size:18px;flex-shrink:0;margin-top:1px}

/* ── STAT CARDS ───────────────────────────────────────────── */
.stat-grid{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:20px}
.stat-card{background:#fff;border-radius:var(--radius);padding:20px;box-shadow:0 1px 8px rgba(0,0,0,.07);border-left:4px solid;transition:transform .2s}
.stat-card:hover{transform:translateY(-2px)}
.stat-card.blue{border-color:#3b82f6}.stat-card.green{border-color:#10b981}
.stat-card.yellow{border-color:#f59e0b}.stat-card.red{border-color:#ef4444}
.stat-top{display:flex;justify-content:space-between;align-items:flex-start}
.stat-label{font-size:11px;color:var(--light);text-transform:uppercase;letter-spacing:.5px;font-weight:600;margin-bottom:6px}
.stat-val{font-size:30px;font-weight:900;color:var(--text);line-height:1}
.stat-sub{font-size:11px;color:var(--muted);margin-top:5px}
.stat-icon{font-size:26px;opacity:.8}

/* ── CARD ─────────────────────────────────────────────────── */
.card{background:#fff;border-radius:var(--radius);padding:22px;box-shadow:0 1px 8px rgba(0,0,0,.07)}
.card-title{font-weight:800;font-size:15px;color:var(--text);margin-bottom:4px}
.card-sub{font-size:12px;color:var(--light);margin-bottom:18px}.card-sub.mb0{margin-bottom:0}
.card-head-row{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;flex-wrap:wrap;gap:10px}
.view-only-badge{padding:7px 16px;background:#faf5ff;border:1.5px solid #c4b5fd;border-radius:10px;font-size:12px;font-weight:700;color:#7c3aed}
.btn-secondary{padding:9px 16px;background:#f1f5f9;border:1.5px solid var(--border);border-radius:10px;color:var(--muted);font-size:13px;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;transition:all .2s}
.btn-secondary:hover{background:#e2e8f0}

/* ── FILTERS ──────────────────────────────────────────────── */
.filter-bar{display:flex;align-items:center;gap:10px;margin-bottom:18px;flex-wrap:wrap}
.search-wrap{position:relative}
.search-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:14px}
.search-input{padding:9px 14px 9px 36px;border:1.5px solid var(--border);border-radius:10px;font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;background:#f8fafc;color:var(--text);width:200px;transition:all .2s}
.search-input:focus{border-color:#7c3aed;background:#fff;box-shadow:0 0 0 3px rgba(124,58,237,.1)}
.filter-select{padding:9px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;background:#f8fafc;color:var(--text);cursor:pointer}
.filter-tabs{display:flex;background:#f1f5f9;border-radius:10px;padding:3px;gap:2px}
.ftab{padding:6px 16px;border:none;border-radius:8px;font-size:12px;font-weight:700;cursor:pointer;background:transparent;color:var(--light);font-family:'Plus Jakarta Sans',sans-serif;transition:all .2s}
.ftab.active{background:#fff;color:var(--text);box-shadow:0 1px 4px rgba(0,0,0,.1)}

/* ── TABLE ────────────────────────────────────────────────── */
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead tr{border-bottom:2px solid #f1f5f9}
th{text-align:left;padding:10px 12px;font-size:11px;color:var(--light);font-weight:700;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
tbody tr{border-bottom:1px solid #f8fafc;transition:background .15s}
tbody tr:hover{background:#fafbfc}
tbody tr.row-critical{border-left:3px solid #ef4444;background:#fff5f5}
td{padding:12px 12px;font-size:13px}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.b-red{background:#fee2e2;color:#991b1b}.b-yellow{background:#fef3c7;color:#92400e}
.b-green{background:#d1fae5;color:#065f46}.b-gray{background:#f1f5f9;color:#64748b}
.tbl-footer{padding:12px 4px 0;font-size:12px;color:var(--light);font-weight:600}
.tbl-btn{padding:5px 12px;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;transition:all .2s}
.tb-view{background:#faf5ff;color:#7c3aed}.tb-view:hover{background:#ede9fe}

/* ── MODAL ────────────────────────────────────────────────── */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);z-index:200}
.modal-overlay.show{display:block}
.modal{display:none;position:fixed;top:50%;left:50%;transform:translate(-50%,-48%);width:520px;max-width:95vw;max-height:90vh;background:#fff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.2);z-index:300;flex-direction:column;overflow:hidden;animation:slideUp .3s cubic-bezier(.16,1,.3,1)}
.modal.show{display:flex}
@keyframes slideUp{from{opacity:0;transform:translate(-50%,-44%)}to{opacity:1;transform:translate(-50%,-48%)}}
.modal-header{padding:22px 24px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:flex-start;flex-shrink:0}
.modal-title{font-weight:800;font-size:16px;color:var(--text)}.modal-sub{font-size:12px;color:var(--light);margin-top:2px}
.modal-close{background:#f1f5f9;border:none;width:30px;height:30px;border-radius:8px;cursor:pointer;font-size:13px;color:var(--muted)}
.modal-close:hover{background:#fee2e2;color:#ef4444}
.modal-body{padding:22px 24px;overflow-y:auto;flex:1}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;flex-shrink:0}
.view-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
.view-field{background:#f8fafc;border-radius:10px;padding:12px 14px}
.vf-label{font-size:10px;color:var(--light);text-transform:uppercase;letter-spacing:.5px;font-weight:700;margin-bottom:4px}
.vf-val{font-size:14px;font-weight:700;color:var(--text)}
.view-divider{grid-column:1/-1;height:1px;background:var(--border);margin:4px 0}

/* ── TOAST ────────────────────────────────────────────────── */
.toast{position:fixed;bottom:24px;right:24px;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;z-index:999;box-shadow:0 4px 20px rgba(0,0,0,.15);transform:translateY(20px);opacity:0;transition:all .3s;pointer-events:none;font-family:'Plus Jakarta Sans',sans-serif}
.toast.show{transform:translateY(0);opacity:1}
.toast.info{background:#4c1d95;color:#ede9fe}

@media(max-width:1100px){.stat-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){#content{padding:16px}}
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
        <div><div class="sb-uname"><?= htmlspecialchars($user['name']) ?></div><div class="sb-urole">👤 Library Staff</div></div>
      </div>
    </div>
    <nav>
      <div class="sb-sec">Main</div>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/staff.php"><span class="ni">⊞</span><span class="nl">Dashboard</span></a>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/staff-monitoring.php"><span class="ni">◎</span><span class="nl">Live Monitoring</span></a>
      <div class="sb-div"></div>
      <div class="sb-sec">View Only</div>
      <a class="nav-item active" href="/NOISE_MONITOR/dashboards/staff-alerts.php"><span class="ni">⚑</span><span class="nl">Alert Logs</span><span class="nb" id="alert-nb">0</span></a>
      <div class="sb-div"></div>
      <div class="sb-sec">Restricted</div>
      <div class="nav-item nav-locked"><span class="ni">▤</span><span class="nl">Reports</span><span class="lock-tag">🔒</span></div>
      <div class="nav-item nav-locked"><span class="ni">▦</span><span class="nl">Zone Management</span><span class="lock-tag">🔒</span></div>
      <div class="nav-item nav-locked"><span class="ni">◉</span><span class="nl">User Management</span><span class="lock-tag">🔒</span></div>
    </nav>
    <div class="sb-bottom">
      <div class="sb-av-lg"><?= $initial ?></div>
      <div class="sb-binfo"><div class="sb-bname"><?= htmlspecialchars($user['name']) ?></div><div class="sb-brole">Library Staff</div></div>
      <a class="sb-logout" href="/NOISE_MONITOR/logout.php" title="Logout">⏻</a>
      <button class="sb-toggle" onclick="toggleSidebar()">◀</button>
    </div>
  </aside>

  <div id="main">
    <header id="topbar">
      <div>
        <div class="tb-title">Alert Logs</div>
        <div class="tb-date" id="tb-date">Loading...</div>
      </div>
      <div class="tb-right">
        <div class="role-badge staff-badge" id="role-badge">👤 Library Staff</div>
        <div class="live-badge"><span class="live-dot"></span><span class="live-text">LIVE</span></div>
        <a class="tb-bell" href="/NOISE_MONITOR/dashboards/staff-alerts.php">🔔<span class="tb-bc" id="bell-count">0</span></a>
        <div class="tb-av"><?= $initial ?></div>
      </div>
    </header>

    <div id="content">

      <div class="view-only-notice">
        <div class="von-icon">👁️</div>
        <div><strong>View Only</strong> — You can view all alert logs. Only the <strong>Library Manager</strong> can resolve or acknowledge alerts.</div>
      </div>

      <div class="stat-grid">
        <div class="stat-card red"><div class="stat-top"><div><div class="stat-label">Active Alerts</div><div class="stat-val" id="s-active">0</div><div class="stat-sub">Needs attention</div></div><div class="stat-icon">🔴</div></div></div>
        <div class="stat-card yellow"><div class="stat-top"><div><div class="stat-label">Critical</div><div class="stat-val" id="s-critical">0</div><div class="stat-sub">Critical violations</div></div><div class="stat-icon">⚠️</div></div></div>
        <div class="stat-card green"><div class="stat-top"><div><div class="stat-label">Resolved</div><div class="stat-val" id="s-resolved">0</div><div class="stat-sub">Cleared today</div></div><div class="stat-icon">✅</div></div></div>
        <div class="stat-card blue"><div class="stat-top"><div><div class="stat-label">Total Logged</div><div class="stat-val" id="s-total">0</div><div class="stat-sub">All records</div></div><div class="stat-icon">📋</div></div></div>
      </div>

      <div class="card">
        <div class="card-head-row">
          <div><div class="card-title">Alert Logs</div><div class="card-sub mb0">All noise violation records — view only</div></div>
          <div class="view-only-badge">👁 View Only</div>
        </div>
        <div class="filter-bar">
          <div class="search-wrap"><span class="search-icon">🔍</span><input class="search-input" id="search-input" type="text" placeholder="Search zone or message..." oninput="filterAlerts()"/></div>
          <div class="filter-tabs">
            <button class="ftab active" onclick="setTab('all',this)">All</button>
            <button class="ftab" onclick="setTab('active',this)">Active</button>
            <button class="ftab" onclick="setTab('resolved',this)">Resolved</button>
          </div>
          <select class="filter-select" id="type-filter" onchange="filterAlerts()"><option value="">All Types</option><option value="critical">Critical</option><option value="warning">Warning</option></select>
          <select class="filter-select" id="zone-filter" onchange="filterAlerts()"><option value="">All Zones</option></select>
          <button class="btn-secondary" onclick="resetFilters()">↺ Reset</button>
        </div>
        <div class="tbl-wrap">
          <table>
            <thead>
              <tr><th>#</th><th>Date & Time</th><th>Zone</th><th>Noise Level</th><th>Type</th><th>Message</th><th>Status</th><th>Resolved By</th><th>Action</th></tr>
            </thead>
            <tbody id="alerts-tbody"></tbody>
          </table>
        </div>
        <div class="tbl-footer" id="tbl-count">Loading...</div>
      </div>

    </div>
  </div>

  <!-- VIEW MODAL -->
  <div class="modal-overlay" id="view-overlay" onclick="closeView()"></div>
  <div class="modal" id="view-modal">
    <div class="modal-header">
      <div><div class="modal-title">Alert Details</div><div class="modal-sub">View only — contact Manager to resolve</div></div>
      <button class="modal-close" onclick="closeView()">✕</button>
    </div>
    <div class="modal-body" id="view-body"></div>
    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeView()">Close</button>
    </div>
  </div>

  <div class="toast" id="toast"></div>
  <script src="/NOISE_MONITOR/app-data.js"></script>
  <script src="/NOISE_MONITOR/alerts-staff.js"></script>
</body>
</html>