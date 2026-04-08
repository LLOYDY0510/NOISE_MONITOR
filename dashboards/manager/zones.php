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
  <title>LibraryQuiet – Zone Management</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap" rel="stylesheet"/>
  <script>window.__LQ_SESSION__ = <?= json_encode($user) ?>;</script>
  <style>
/* ============================================================
   LibraryQuiet – zones.css
   ============================================================ */
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{--blue:#1d4ed8;--blue2:#3b82f6;--green:#10b981;--yellow:#f59e0b;--red:#ef4444;--bg:#f0f4f8;--sidebar:#0f172a;--sb2:#1e293b;--border:#e2e8f0;--text:#0f172a;--muted:#64748b;--light:#94a3b8;--radius:16px}
body{font-family:'Plus Jakarta Sans',sans-serif;background:var(--bg);display:flex;height:100vh;overflow:hidden}

/* SIDEBAR */
#sidebar{width:240px;background:var(--sidebar);display:flex;flex-direction:column;flex-shrink:0;box-shadow:2px 0 24px rgba(0,0,0,.3);z-index:100;transition:width .3s}
#sidebar.collapsed{width:68px}
.sb-logo{padding:18px 15px;border-bottom:1px solid var(--sb2);display:flex;align-items:center;gap:12px;min-height:68px}
.sb-logo-icon{width:38px;height:38px;border-radius:11px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);display:flex;align-items:center;justify-content:center;font-size:17px;flex-shrink:0}
.sb-logo-name{color:#fff;font-weight:800;font-size:14px}.sb-logo-sub{color:#475569;font-size:11px}
.sb-logo-text{transition:opacity .2s}
#sidebar.collapsed .sb-logo-text{opacity:0;pointer-events:none}
.sb-user{padding:10px 12px;border-bottom:1px solid var(--sb2)}
.sb-user-label{font-size:10px;color:#475569;text-transform:uppercase;letter-spacing:1px;margin-bottom:6px}
.sb-user-chip{background:var(--sb2);border-radius:10px;padding:8px 10px;display:flex;align-items:center;gap:8px}
.sb-av{width:28px;height:28px;border-radius:8px;background:linear-gradient(135deg,#1d4ed8,#3b82f6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:12px;flex-shrink:0}
.sb-uname{color:#e2e8f0;font-size:12px;font-weight:700}.sb-urole{color:#f59e0b;font-size:10px;font-weight:600}
#sidebar.collapsed .sb-user{display:none}
nav{flex:1;padding:10px 8px;overflow-y:auto;overflow-x:hidden}
nav::-webkit-scrollbar{width:3px}nav::-webkit-scrollbar-thumb{background:#334155;border-radius:3px}
.sb-sec{font-size:10px;color:#334155;text-transform:uppercase;letter-spacing:1px;padding:8px 12px 4px;font-weight:700}
.sb-div{height:1px;background:var(--sb2);margin:4px 8px}
#sidebar.collapsed .sb-sec,#sidebar.collapsed .sb-div{display:none}
.nav-item{width:100%;display:flex;align-items:center;gap:12px;padding:10px 12px;border-radius:10px;margin-bottom:2px;background:transparent;color:#64748b;font-size:13px;font-weight:500;font-family:'Plus Jakarta Sans',sans-serif;text-align:left;text-decoration:none;white-space:nowrap;transition:all .2s}
.nav-item:hover{background:var(--sb2);color:#cbd5e1}
.nav-item.active{background:linear-gradient(90deg,rgba(29,78,216,.9),rgba(37,99,235,.8));color:#fff;font-weight:700}
.ni{font-size:17px;width:22px;text-align:center;flex-shrink:0}.nl{transition:opacity .2s}
#sidebar.collapsed .nl{opacity:0;width:0;overflow:hidden}
#sidebar.collapsed .nav-item{justify-content:center;padding:10px 0}
.nb{margin-left:auto;background:#ef4444;color:#fff;font-size:9px;font-weight:700;border-radius:20px;padding:2px 7px}
#sidebar.collapsed .nb{display:none}
.sb-bottom{padding:12px;border-top:1px solid var(--sb2);display:flex;align-items:center;gap:8px}
.sb-av-lg{width:34px;height:34px;border-radius:10px;background:linear-gradient(135deg,#1d4ed8,#3b82f6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:13px;flex-shrink:0}
.sb-binfo{flex:1;min-width:0;overflow:hidden;transition:opacity .2s}
.sb-bname{color:#e2e8f0;font-size:12px;font-weight:700}.sb-brole{color:#475569;font-size:10px}
#sidebar.collapsed .sb-binfo{opacity:0;width:0}
.sb-logout{background:none;border:none;color:#475569;font-size:15px;text-decoration:none;padding:4px;transition:color .2s}
.sb-logout:hover{color:#ef4444}
.sb-toggle{background:none;border:none;color:#475569;font-size:13px;cursor:pointer;padding:4px;transition:transform .3s}
#sidebar.collapsed .sb-toggle{transform:rotate(180deg)}
#sidebar.collapsed .sb-logout{display:none}

/* MAIN */
#main{flex:1;display:flex;flex-direction:column;overflow:hidden}
#topbar{background:#fff;border-bottom:1px solid var(--border);padding:0 24px;height:64px;display:flex;align-items:center;justify-content:space-between;box-shadow:0 1px 8px rgba(0,0,0,.05);flex-shrink:0}
.tb-title{font-weight:800;font-size:17px;color:var(--text)}.tb-date{font-size:11px;color:var(--light);margin-top:1px}
.tb-right{display:flex;align-items:center;gap:12px}
.role-badge{padding:5px 14px;border-radius:20px;font-size:11px;font-weight:700}
.admin-badge{background:#fef9c3;color:#713f12;border:1px solid #fde047}
.manager-badge{background:#ccfbf1;color:#0f766e;border:1px solid #99f6e4}
.live-badge{display:flex;align-items:center;gap:6px;background:#d1fae5;padding:5px 14px;border-radius:20px}
.live-dot{width:7px;height:7px;border-radius:50%;background:#10b981;animation:pulse 1.5s infinite}
.live-text{font-size:11px;font-weight:700;color:#065f46}
@keyframes pulse{0%,100%{opacity:1}50%{opacity:.4}}
.tb-bell{position:relative;width:36px;height:36px;border-radius:10px;background:#f8fafc;border:1px solid var(--border);display:flex;align-items:center;justify-content:center;font-size:16px;text-decoration:none}
.tb-bc{position:absolute;top:-4px;right:-4px;background:#ef4444;color:#fff;font-size:8px;border-radius:50%;width:15px;height:15px;display:flex;align-items:center;justify-content:center;font-weight:700;border:2px solid #fff}
.tb-av{width:36px;height:36px;border-radius:10px;background:linear-gradient(135deg,#1d4ed8,#3b82f6);display:flex;align-items:center;justify-content:center;color:#fff;font-weight:800;font-size:14px}

/* CONTENT */
#content{flex:1;overflow-y:auto;padding:24px;animation:pageIn .35s ease}
#content::-webkit-scrollbar{width:5px}
#content::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:5px}
@keyframes pageIn{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:none}}

/* STAT CARDS */
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

/* CARD */
.card{background:#fff;border-radius:var(--radius);padding:22px;box-shadow:0 1px 8px rgba(0,0,0,.07)}

/* TOOLBAR */
.toolbar{display:flex;justify-content:space-between;align-items:center;margin-bottom:18px;gap:12px;flex-wrap:wrap}
.toolbar-left{display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.search-wrap{position:relative}
.search-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);font-size:14px}
.search-input{padding:9px 14px 9px 36px;border:1.5px solid var(--border);border-radius:10px;font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;background:#f8fafc;color:var(--text);width:200px;transition:all .2s}
.search-input:focus{border-color:var(--blue2);background:#fff;box-shadow:0 0 0 3px rgba(59,130,246,.1)}
.filter-select{padding:9px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;background:#f8fafc;color:var(--text);cursor:pointer}
.filter-select:focus{border-color:var(--blue2)}

/* BUTTONS */
.btn-primary{padding:9px 20px;background:linear-gradient(135deg,#3b82f6,#1d4ed8);border:none;border-radius:10px;color:#fff;font-size:13px;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;transition:all .2s;box-shadow:0 2px 8px rgba(59,130,246,.35)}
.btn-primary:hover{transform:translateY(-1px);box-shadow:0 4px 12px rgba(59,130,246,.4)}
.btn-secondary{padding:9px 16px;background:#f1f5f9;border:1.5px solid var(--border);border-radius:10px;color:var(--muted);font-size:13px;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;transition:all .2s}
.btn-secondary:hover{background:#e2e8f0}
.btn-danger{padding:9px 20px;background:linear-gradient(135deg,#ef4444,#dc2626);border:none;border-radius:10px;color:#fff;font-size:13px;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;transition:all .2s}
.btn-danger:hover{transform:translateY(-1px)}

/* TABLE */
.tbl-wrap{overflow-x:auto}
table{width:100%;border-collapse:collapse}
thead tr{border-bottom:2px solid #f1f5f9}
th{text-align:left;padding:10px 14px;font-size:11px;color:var(--light);font-weight:700;text-transform:uppercase;letter-spacing:.5px;white-space:nowrap}
tbody tr{border-bottom:1px solid #f8fafc;transition:background .15s}
tbody tr:hover{background:#fafbfc}
td{padding:13px 14px;font-size:13px}
.mono{font-family:'JetBrains Mono',monospace;font-size:12px;font-weight:600;color:var(--muted)}
.badge{display:inline-flex;align-items:center;padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700}
.b-green{background:#d1fae5;color:#065f46}.b-red{background:#fee2e2;color:#991b1b}
.b-yellow{background:#fef3c7;color:#92400e}.b-gray{background:#f1f5f9;color:#64748b}
.b-blue{background:#dbeafe;color:#1e40af}
.tbl-footer{padding:12px 4px 0;font-size:12px;color:var(--light);font-weight:600}
.action-btns{display:flex;gap:6px}
.tbl-btn{padding:5px 12px;border:none;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;font-family:'Plus Jakarta Sans',sans-serif;transition:all .2s}
.tb-view{background:#eff6ff;color:#1d4ed8}.tb-view:hover{background:#dbeafe}
.tb-edit{background:#f0fdf4;color:#15803d}.tb-edit:hover{background:#dcfce7}
.tb-del{background:#fff5f5;color:#dc2626}.tb-del:hover{background:#fee2e2}
.noise-val{font-weight:900;font-family:'JetBrains Mono',monospace;font-size:13px}

/* MODAL */
.modal-overlay{position:fixed;inset:0;background:rgba(15,23,42,.55);backdrop-filter:blur(4px);z-index:200;display:none}
.modal{position:fixed;top:50%;left:50%;transform:translate(-50%,-48%);width:560px;max-width:95vw;max-height:90vh;background:#fff;border-radius:20px;box-shadow:0 24px 60px rgba(0,0,0,.2);z-index:300;overflow:hidden;flex-direction:column;display:none;animation:slideUp .3s cubic-bezier(.16,1,.3,1)}
.modal-sm{width:420px}
.modal.show{display:flex}
.modal-overlay.show{display:block}
@keyframes slideUp{from{opacity:0;transform:translate(-50%,-44%)}to{opacity:1;transform:translate(-50%,-48%)}}
.modal-header{padding:22px 24px 18px;border-bottom:1px solid var(--border);display:flex;justify-content:space-between;align-items:flex-start;flex-shrink:0}
.modal-title{font-weight:800;font-size:16px;color:var(--text)}.modal-sub{font-size:12px;color:var(--light);margin-top:2px}
.modal-close{background:#f1f5f9;border:none;width:30px;height:30px;border-radius:8px;cursor:pointer;font-size:13px;color:var(--muted);transition:all .2s}
.modal-close:hover{background:#fee2e2;color:#ef4444}
.modal-body{padding:22px 24px;overflow-y:auto;flex:1}
.modal-footer{padding:16px 24px;border-top:1px solid var(--border);display:flex;justify-content:flex-end;gap:10px;flex-shrink:0}

/* FORM */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:14px}
.form-group{margin-bottom:14px}
.form-group:last-child{margin-bottom:0}
.form-label{display:block;font-size:12px;font-weight:700;color:var(--text);margin-bottom:6px}
.req{color:#ef4444}
.form-input{width:100%;padding:10px 14px;border:1.5px solid var(--border);border-radius:10px;font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;outline:none;background:#f8fafc;color:var(--text);transition:all .2s;resize:vertical}
.form-input:focus{border-color:var(--blue2);background:#fff;box-shadow:0 0 0 3px rgba(59,130,246,.1)}
.form-input.error{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.1)}
.form-err{font-size:11px;color:#ef4444;margin-top:4px;font-weight:600}

/* TOAST */
.toast{position:fixed;bottom:24px;right:24px;padding:12px 20px;border-radius:12px;font-size:13px;font-weight:600;z-index:999;box-shadow:0 4px 20px rgba(0,0,0,.15);transform:translateY(20px);opacity:0;transition:all .3s;pointer-events:none;font-family:'Plus Jakarta Sans',sans-serif}
.toast.show{transform:translateY(0);opacity:1}
.toast.success{background:#065f46;color:#d1fae5}
.toast.error{background:#991b1b;color:#fee2e2}
.toast.info{background:#1e3a5f;color:#bfdbfe}

@media(max-width:1100px){.stat-grid{grid-template-columns:repeat(2,1fr)}}
@media(max-width:768px){#content{padding:16px}.form-row{grid-template-columns:1fr}}
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
          <div class="sb-urole">📋 Library Manager</div>
        </div>
      </div>
    </div>
    <nav>
      <div class="sb-sec">Main</div>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/dashboard-manager.php"><span class="ni">⊞</span><span class="nl">Dashboard</span></a>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/monitor-manager.php"><span class="ni">◎</span><span class="nl">Live Monitoring</span></a>
      <div class="sb-div"></div>
      <div class="sb-sec">Management</div>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/alerts-manager.php"><span class="ni">⚑</span><span class="nl">Alert Logs</span><span class="nb" id="alert-nb">0</span></a>
      <a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/reports-manager.php"><span class="ni">▤</span><span class="nl">Reports</span></a>
      <a class="nav-item active" href="/NOISE_MONITOR/dashboards/manager/zones.php"><span class="ni">▦</span><span class="nl">Zone Management</span></a>
    </nav>
    <div class="sb-bottom">
      <div class="sb-av-lg"><?= $initial ?></div>
      <div class="sb-binfo">
        <div class="sb-bname"><?= htmlspecialchars($user['name']) ?></div>
        <div class="sb-brole">Library Manager</div>
      </div>
      <a class="sb-logout" href="/NOISE_MONITOR/logout.php" title="Logout">⏻</a>
      <button class="sb-toggle" onclick="toggleSidebar()">◀</button>
    </div>
  </aside>

  <div id="main">
    <header id="topbar">
      <div>
        <div class="tb-title">Zone Management</div>
        <div class="tb-date" id="tb-date">Loading...</div>
      </div>
      <div class="tb-right">
     
        <div class="live-badge"><span class="live-dot"></span><span class="live-text">LIVE</span></div>
        <div class="tb-av"><?= $initial ?></div>
      </div>
    </header>

    <div id="content">

      <div class="stat-grid">
        <div class="stat-card blue"><div class="stat-top"><div><div class="stat-label">Total Zones</div><div class="stat-val" id="s-total">0</div><div class="stat-sub">Monitored areas</div></div><div class="stat-icon">▦</div></div></div>
        <div class="stat-card green"><div class="stat-top"><div><div class="stat-label">Active Zones</div><div class="stat-val" id="s-active">0</div><div class="stat-sub">Currently online</div></div><div class="stat-icon">✅</div></div></div>
        <div class="stat-card yellow"><div class="stat-top"><div><div class="stat-label">Total Capacity</div><div class="stat-val" id="s-capacity">0</div><div class="stat-sub">Max persons</div></div><div class="stat-icon">👥</div></div></div>
        <div class="stat-card red"><div class="stat-top"><div><div class="stat-label">Loud Zones Now</div><div class="stat-val" id="s-loud">0</div><div class="stat-sub">Above threshold</div></div><div class="stat-icon">🔴</div></div></div>
      </div>

      <div class="card">
        <div class="toolbar">
          <div class="toolbar-left">
            <div class="search-wrap">
              <span class="search-icon">🔍</span>
              <input class="search-input" id="search-input" type="text" placeholder="Search zones..." oninput="filterZones()"/>
            </div>
            <select class="filter-select" id="floor-filter" onchange="filterZones()">
              <option value="">All Floors</option>
              <option value="1F">Floor 1</option>
              <option value="2F">Floor 2</option>
              <option value="3F">Floor 3</option>
            </select>
            <select class="filter-select" id="status-filter" onchange="filterZones()">
              <option value="">All Status</option>
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
            <button class="btn-secondary" onclick="resetFilters()">↺ Reset</button>
          </div>
          <button class="btn-primary" onclick="openAddModal()">＋ Add New Zone</button>
        </div>

        <div class="tbl-wrap">
          <table>
            <thead>
              <tr>
                <th>Zone ID</th><th>Zone Name</th><th>Floor</th><th>Capacity</th>
                <th>Live dB</th><th>Warn</th><th>Critical</th>
                <th>Sensor</th><th>Status</th><th>Actions</th>
              </tr>
            </thead>
            <tbody id="zones-tbody"></tbody>
          </table>
        </div>
        <div class="tbl-footer" id="tbl-count">Loading...</div>
      </div>

    </div>
  </div>

  <!-- ADD/EDIT MODAL -->
  <div class="modal-overlay" id="modal-overlay" onclick="closeModal()"></div>
  <div class="modal" id="zone-modal">
    <div class="modal-header">
      <div><div class="modal-title" id="modal-title">Add New Zone</div><div class="modal-sub" id="modal-sub">Fill in zone details</div></div>
      <button class="modal-close" onclick="closeModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="edit-id"/>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Zone Name <span class="req">*</span></label><input class="form-input" id="f-name" type="text" placeholder="e.g. Reading Hall A"/></div>
        <div class="form-group"><label class="form-label">Floor <span class="req">*</span></label>
          <select class="form-input" id="f-floor"><option value="">Select floor</option><option value="1F">Floor 1</option><option value="2F">Floor 2</option><option value="3F">Floor 3</option></select>
        </div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Capacity <span class="req">*</span></label><input class="form-input" id="f-capacity" type="number" min="1" placeholder="e.g. 50"/></div>
        <div class="form-group"><label class="form-label">Sensor ID</label><input class="form-input" id="f-sensor" type="text" placeholder="e.g. SNS-007"/></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label class="form-label">Warning Threshold (dB) <span class="req">*</span></label><input class="form-input" id="f-warn" type="number" min="1" max="90" placeholder="e.g. 40"/></div>
        <div class="form-group"><label class="form-label">Critical Threshold (dB) <span class="req">*</span></label><input class="form-input" id="f-critical" type="number" min="1" max="90" placeholder="e.g. 60"/></div>
      </div>
      <div class="form-group"><label class="form-label">Description</label><textarea class="form-input" id="f-desc" rows="2" placeholder="Brief description..."></textarea></div>
      <div class="form-group"><label class="form-label">Status</label>
        <select class="form-input" id="f-status"><option value="active">Active</option><option value="inactive">Inactive</option></select>
      </div>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeModal()">Cancel</button>
      <button class="btn-primary" id="modal-save-btn" onclick="saveZone()">Save Zone</button>
    </div>
  </div>

  <!-- DELETE MODAL -->
  <div class="modal-overlay" id="del-overlay" onclick="closeDeleteModal()"></div>
  <div class="modal modal-sm" id="del-modal">
    <div class="modal-header">
      <div><div class="modal-title">Delete Zone</div><div class="modal-sub">This cannot be undone</div></div>
      <button class="modal-close" onclick="closeDeleteModal()">✕</button>
    </div>
    <div class="modal-body" style="text-align:center;padding:28px 24px;">
      <div style="font-size:40px;margin-bottom:12px;">🗑️</div>
      <p style="font-size:14px;color:#64748b;line-height:1.6;">Are you sure you want to delete <strong id="del-zone-name">this zone</strong>?</p>
    </div>
    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeDeleteModal()">Cancel</button>
      <button class="btn-danger" onclick="confirmDelete()">Yes, Delete</button>
    </div>
  </div>

  <!-- VIEW MODAL -->
  <div class="modal-overlay" id="view-overlay" onclick="closeViewModal()"></div>
  <div class="modal" id="view-modal">
    <div class="modal-header">
      <div><div class="modal-title" id="view-title">Zone Details</div><div class="modal-sub">Full zone information</div></div>
      <button class="modal-close" onclick="closeViewModal()">✕</button>
    </div>
    <div class="modal-body" id="view-body"></div>
    <div class="modal-footer">
      <button class="btn-secondary" onclick="closeViewModal()">Close</button>
      <button class="btn-primary" id="view-edit-btn">✏️ Edit Zone</button>
    </div>
  </div>

  <div class="toast" id="toast"></div>

  <script>
// ============================================================
//  LibraryQuiet – app-data.js  (inlined)
// ============================================================
const AppData = (() => {
  const API = '/NOISE_MONITOR/api.php';
  let _session = window.__LQ_SESSION__ || null;

  async function post(action, data = {}) {
    try {
      const res = await fetch(`${API}?action=${action}`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
      });
      return await res.json();
    } catch (e) {
      console.error(`API[${action}]:`, e);
      return { error: e.message };
    }
  }

  async function get(action) {
    try {
      const res = await fetch(`${API}?action=${action}`);
      return await res.json();
    } catch (e) {
      console.error(`API[${action}]:`, e);
      return null;
    }
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
    const name    = _session.name || _session.email;
    const initial = name.charAt(0).toUpperCase();
    const isAdm   = isAdmin(), isMgr = isManager();
    const roleIcon  = isAdm ? '👑 ' : isMgr ? '📋 ' : '👤 ';
    const roleColor = isAdm
      ? 'linear-gradient(135deg,#1d4ed8,#3b82f6)'
      : isMgr
        ? 'linear-gradient(135deg,#0d9488,#0f766e)'
        : 'linear-gradient(135deg,#7c3aed,#8b5cf6)';

    document.querySelectorAll('.sb-uname,.sb-bname').forEach(el => el.textContent = name);
    document.querySelectorAll('.sb-urole').forEach(el => el.textContent = roleIcon + _session.role);
    document.querySelectorAll('.sb-brole').forEach(el => el.textContent = _session.role);
    document.querySelectorAll('.sb-av,.sb-av-lg,.tb-av,.top-av').forEach(el => {
      el.textContent = initial;
      el.style.background = roleColor;
    });
    const badge = document.getElementById('role-badge');
    if (badge) {
      badge.textContent = roleIcon + _session.role;
      badge.className   = 'role-badge ' + (isAdm ? 'admin-badge' : isMgr ? 'manager-badge' : 'staff-badge');
    }
    updateNotifBadge();
  }

  // ZONES
  let _zones = [];
  async function loadZones()              { _zones = await get('get_zones') || []; return _zones; }
  function  getZones()                    { return _zones; }
  function  getZone(id)                   { return _zones.find(z => z.id === id); }
  async function setSensorLevel(zoneId, db) {
    await post('set_sensor', { id: zoneId, level: db });
    const z = _zones.find(z => z.id === zoneId);
    if (z) { z.level = db; z.manualOverride = true; }
    _alerts = await get('get_alerts') || _alerts;
  }
  async function clearSensorOverride(zoneId) {
    await post('clear_sensor', { id: zoneId });
    const z = _zones.find(z => z.id === zoneId);
    if (z) z.manualOverride = false;
  }
  let _overrides = {};
  async function loadSensorOverrides()    { _overrides = await get('get_sensor_overrides') || {}; return _overrides; }
  function  getSensorOverrides()          { return _overrides; }
  async function addZone(z)              { return await post('add_zone', z); }
  async function updateZone(z)           { return await post('update_zone', z); }
  async function deleteZone(id)          { return await post('delete_zone', { id }); }

  // ALERTS
  let _alerts = [];
  async function loadAlerts()            { _alerts = await get('get_alerts') || []; return _alerts; }
  function  getAlerts()                  { return _alerts; }
  function  getActiveAlerts()            { return _alerts.filter(a => a.status === 'active'); }
  async function resolveAlert(id, by)    { await post('resolve_alert', { id, resolvedBy: by }); const a = _alerts.find(a => a.id === id); if (a) { a.status = 'resolved'; a.resolvedBy = by; } updateNotifBadge(); }
  async function addAlertMessage(alertId, message) { await post('add_alert_message', { alertId, message }); }
  function  getAlertMessages(alertId)    { return _alerts.find(a => a.id === alertId)?.messages || []; }
  async function deleteAlert(id)         { await post('delete_alert', { id }); _alerts = _alerts.filter(a => a.id !== id); }
  async function addAlert(a)             { return await post('add_alert', a); }

  // REPORTS
  let _reports = [];
  async function loadReports()           { _reports = await get('get_reports') || []; return _reports; }
  function  getReports()                 { return _reports; }
  function  getUnreadReports()           { return _reports.filter(r => r.sentToAdmin && !r.adminReadAt); }
  async function addReport(r)            { return await post('add_report', r); }
  async function sendReportToAdmin(id)   { return await post('send_report', { id }); }
  async function markReportRead(id)      { return await post('mark_report_read', { id }); }
  async function deleteReport(id)        { await post('delete_report', { id }); _reports = _reports.filter(r => r.id !== id); }

  // USERS
  let _users = [];
  async function loadUsers()             { _users = await get('get_users') || []; return _users; }
  function  getUsers()                   { return _users; }
  function  getUserByEmail(email)        { return _users.find(u => u.email.toLowerCase() === email.toLowerCase()); }
  async function addUser(u)              { return await post('add_user', u); }
  async function updateUser(id, data)    { return await post('update_user', { id, ...data }); }
  async function deleteUser(id)          { return await post('delete_user', { id }); }
  async function updatePassword(id, pw)  { return await post('update_password', { id, password: pw }); }

  // BADGE
  function updateNotifBadge() {
    const active = getActiveAlerts().length;
    ['alert-nb', 'bell-count'].forEach(id => {
      const el = document.getElementById(id);
      if (el) { el.textContent = active; el.style.display = active > 0 ? '' : 'none'; }
    });
  }

  async function loadAll() {
    await Promise.all([loadZones(), loadAlerts(), loadSensorOverrides(), loadUsers(), loadReports()]);
  }

  return {
    loadSession, getSession, isAdmin, isManager, isStaff, clearSession, applySession,
    loadZones, getZones, getZone, setSensorLevel, clearSensorOverride, loadSensorOverrides, getSensorOverrides, addZone, updateZone, deleteZone,
    loadAlerts, getAlerts, getActiveAlerts, resolveAlert, addAlertMessage, getAlertMessages, deleteAlert, addAlert,
    loadReports, getReports, getUnreadReports, addReport, sendReportToAdmin, markReportRead, deleteReport,
    loadUsers, getUsers, getUserByEmail, addUser, updateUser, deleteUser, updatePassword,
    updateNotifBadge, loadAll,
    get _session()  { return _session; },
    set _session(v) { _session = v; }
  };
})();


// ============================================================
//  LibraryQuiet – zones-manager.js  (inlined)
// ============================================================

function el(id)        { return document.getElementById(id); }
function setText(id,v) { const e=el(id); if(e) e.textContent=v; }
function noiseColor(db){ return db<40?'#10b981':db<60?'#f59e0b':'#ef4444'; }
function noiseStatus(db){ return db<40?'quiet':db<60?'moderate':'loud'; }

let deleteTargetId = '';

// ── CLOCK ──────────────────────────────────────────────────
function startClock(){
  const u = () => {
    const n = new Date();
    setText('tb-date',
      n.toLocaleDateString('en-PH', { weekday:'long', year:'numeric', month:'long', day:'numeric' })
      + ' · '
      + n.toLocaleTimeString('en-PH')
    );
  };
  u(); setInterval(u, 1000);
}

function toggleSidebar(){ el('sidebar').classList.toggle('collapsed'); }

// ── TOAST ──────────────────────────────────────────────────
function showToast(msg, type = 'success'){
  const t = el('toast'); if (!t) return;
  t.textContent = msg;
  t.className   = 'toast show';
  t.style.background = type === 'error' ? '#ef4444' : type === 'info' ? '#3b82f6' : '#0f172a';
  clearTimeout(t._t);
  t._t = setTimeout(() => { t.className = 'toast'; }, 3000);
}

// ── STATS ──────────────────────────────────────────────────
function renderStats(){
  const z = AppData.getZones();
  setText('s-total',    z.length);
  setText('s-active',   z.filter(x => x.status === 'active').length);
  setText('s-capacity', z.reduce((a, x) => a + (x.capacity || 0), 0));
  setText('s-loud',     z.filter(x => x.level >= (x.critThreshold || 60)).length);
}

// ── FILTER ─────────────────────────────────────────────────
function getFiltered(){
  const q = (el('search-input')?.value || '').toLowerCase();
  const f = el('floor-filter')?.value  || '';
  const s = el('status-filter')?.value || '';
  return AppData.getZones().filter(z => {
    return (!q || z.name.toLowerCase().includes(q) || z.id.toLowerCase().includes(q) || (z.sensor||'').toLowerCase().includes(q))
        && (!f || z.floor   === f)
        && (!s || z.status  === s);
  });
}
function filterZones(){ renderTable(); }
function resetFilters(){
  if (el('search-input'))  el('search-input').value  = '';
  if (el('floor-filter'))  el('floor-filter').value  = '';
  if (el('status-filter')) el('status-filter').value = '';
  renderTable();
}

// ── TABLE ──────────────────────────────────────────────────
function renderTable(){
  const tbody = el('zones-tbody'); if (!tbody) return;
  const zones  = getFiltered();
  const all    = AppData.getZones().length;
  setText('tbl-count', `Showing ${zones.length} of ${all} zone${all !== 1 ? 's' : ''}`);

  if (!zones.length) {
    tbody.innerHTML = `<tr><td colspan="10" style="text-align:center;padding:32px;color:#94a3b8;">No zones found.</td></tr>`;
    return;
  }

  tbody.innerHTML = zones.map(z => {
    const col   = noiseColor(z.level);
    const stBg  = z.status === 'active' ? '#d1fae5' : '#f1f5f9';
    const stCol = z.status === 'active' ? '#065f46' : '#64748b';
    return `<tr>
      <td style="font-family:'JetBrains Mono',monospace;font-size:11px;color:#64748b;">${z.id}</td>
      <td style="font-weight:700;">${z.name}</td>
      <td><span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;">${z.floor}</span></td>
      <td style="font-weight:600;">${z.capacity}</td>
      <td><span style="font-weight:900;color:${col};">${Math.round(z.level)} dB</span></td>
      <td style="color:#f59e0b;font-weight:700;">${z.warnThreshold} dB</td>
      <td style="color:#ef4444;font-weight:700;">${z.critThreshold} dB</td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:11px;">${z.sensor || '—'}</td>
      <td><span style="background:${stBg};color:${stCol};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">${z.status}</span></td>
      <td>
        <button class="tbl-btn tb-view" onclick="openViewModal('${z.id}')">👁</button>
        <button class="tbl-btn tb-edit" onclick="openEditModal('${z.id}')">✏️</button>
        <button class="tbl-btn tb-del"  onclick="openDeleteModal('${z.id}')">🗑</button>
      </td>
    </tr>`;
  }).join('');
}

// ── MODAL HELPERS ──────────────────────────────────────────
function openModal(overlayId, modalId){
  const ov = el(overlayId), mo = el(modalId);
  if (ov) { ov.classList.add('show'); }
  if (mo) { mo.classList.add('show'); }
}
function closeModalById(overlayId, modalId){
  const ov = el(overlayId), mo = el(modalId);
  if (ov) ov.classList.remove('show');
  if (mo) mo.classList.remove('show');
}

// ── ADD MODAL ──────────────────────────────────────────────
function openAddModal(){
  el('modal-title').textContent = 'Add New Zone';
  el('modal-sub').textContent   = 'Fill in zone details';
  el('edit-id').value           = '';
  el('f-name').value            = '';
  el('f-sensor').value          = '';
  el('f-desc').value            = '';
  el('f-floor').value           = '';
  el('f-status').value          = 'active';
  el('f-capacity').value        = '';
  el('f-warn').value            = '40';
  el('f-critical').value        = '60';
  el('modal-save-btn').textContent = 'Save Zone';
  openModal('modal-overlay', 'zone-modal');
}

// ── EDIT MODAL ─────────────────────────────────────────────
function openEditModal(id){
  const z = AppData.getZone(id); if (!z) return;
  el('modal-title').textContent    = 'Edit Zone';
  el('modal-sub').textContent      = 'Update zone details';
  el('edit-id').value              = z.id;
  el('f-name').value               = z.name;
  el('f-floor').value              = z.floor;
  el('f-capacity').value           = z.capacity;
  el('f-sensor').value             = z.sensor || '';
  el('f-warn').value               = z.warnThreshold;
  el('f-critical').value           = z.critThreshold;
  el('f-desc').value               = z.desc || '';
  el('f-status').value             = z.status;
  el('modal-save-btn').textContent = 'Update Zone';
  openModal('modal-overlay', 'zone-modal');
}

function closeModal(){ closeModalById('modal-overlay', 'zone-modal'); }

// ── SAVE ZONE ──────────────────────────────────────────────
async function saveZone(){
  const name     = el('f-name').value.trim();
  const floor    = el('f-floor').value;
  const capacity = parseInt(el('f-capacity').value) || 0;
  const sensor   = el('f-sensor').value.trim();
  const warn     = parseInt(el('f-warn').value)     || 40;
  const critical = parseInt(el('f-critical').value) || 60;
  const desc     = el('f-desc').value.trim();
  const status   = el('f-status').value;
  const editId   = el('edit-id').value;

  if (!name)                       { showToast('⚠️ Zone name is required.', 'error');           return; }
  if (!floor)                      { showToast('⚠️ Please select a floor.', 'error');           return; }
  if (!capacity || capacity < 1)   { showToast('⚠️ Enter a valid capacity.', 'error');          return; }
  if (warn >= critical)            { showToast('⚠️ Warning must be less than Critical.', 'error'); return; }

  const btn = el('modal-save-btn');
  if (btn) btn.textContent = 'Saving…';

  try {
    if (editId) {
      await AppData.updateZone({ id: editId, name, floor, capacity, warnThreshold: warn, critThreshold: critical, sensor, desc, status });
      showToast('✅ Zone updated successfully!');
    } else {
      await AppData.addZone({ name, floor, capacity, occupied: 0, level: 0, warnThreshold: warn, critThreshold: critical, sensor, battery: 100, status, desc });
      showToast('✅ Zone added successfully!');
    }
    await AppData.loadZones();
    closeModal();
    renderStats();
    renderTable();
  } catch (e) {
    showToast('❌ Failed to save zone.', 'error');
  }
  if (btn) btn.textContent = 'Save Zone';
}

// ── DELETE MODAL ───────────────────────────────────────────
function openDeleteModal(id){
  const z = AppData.getZone(id); if (!z) return;
  deleteTargetId = id;
  setText('del-zone-name', z.name);
  openModal('del-overlay', 'del-modal');
}
function closeDeleteModal(){ closeModalById('del-overlay', 'del-modal'); }

async function confirmDelete(){
  if (!deleteTargetId) return;
  const z = AppData.getZone(deleteTargetId);
  try {
    await AppData.deleteZone(deleteTargetId);
    await AppData.loadZones();
    closeDeleteModal();
    renderStats();
    renderTable();
    showToast(`🗑️ Zone "${z?.name}" deleted.`, 'info');
  } catch (e) {
    showToast('❌ Failed to delete zone.', 'error');
  }
  deleteTargetId = '';
}

// ── VIEW MODAL ─────────────────────────────────────────────
function openViewModal(id){
  const z = AppData.getZone(id); if (!z) return;
  el('view-title').textContent = z.name;
  const col = noiseColor(z.level);
  const pct = Math.min(100, (z.level / 90) * 100).toFixed(1);
  el('view-body').innerHTML = `
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:4px 0;">
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Zone ID</div><div style="font-family:'JetBrains Mono',monospace;font-weight:700;">${z.id}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Floor</div><div style="font-weight:700;">${z.floor}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Capacity</div><div style="font-weight:700;">${z.capacity} persons</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Occupied</div><div style="font-weight:700;">${z.occupied}</div></div>
      <div>
        <div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Current Noise</div>
        <div style="font-size:28px;font-weight:900;color:${col};">${Math.round(z.level)} dB</div>
        <div style="height:6px;background:#f1f5f9;border-radius:4px;overflow:hidden;margin-top:6px;">
          <div style="width:${pct}%;height:100%;background:${col};border-radius:4px;transition:width .5s;"></div>
        </div>
      </div>
      <div>
        <div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Status</div>
        <span style="background:${z.status==='active'?'#d1fae5':'#f1f5f9'};color:${z.status==='active'?'#065f46':'#64748b'};padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">${z.status}</span>
      </div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Sensor</div><div style="font-family:'JetBrains Mono',monospace;font-weight:700;">${z.sensor || '—'}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Battery</div><div style="font-weight:700;">🔋 ${z.battery || 80}%</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Warn Threshold</div><div style="font-weight:700;color:#f59e0b;">${z.warnThreshold} dB</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Critical Threshold</div><div style="font-weight:700;color:#ef4444;">${z.critThreshold} dB</div></div>
    </div>
    ${z.desc ? `<div style="margin-top:14px;padding:12px;background:#f8fafc;border-radius:8px;font-size:13px;color:#64748b;">${z.desc}</div>` : ''}
  `;
  const editBtn = el('view-edit-btn');
  if (editBtn) {
    editBtn.style.display = '';
    editBtn.onclick = () => { closeViewModal(); openEditModal(id); };
  }
  openModal('view-overlay', 'view-modal');
}
function closeViewModal(){ closeModalById('view-overlay', 'view-modal'); }

// ── KEYBOARD ───────────────────────────────────────────────
document.addEventListener('keydown', e => {
  if (e.key === 'Escape'){ closeModal(); closeDeleteModal(); closeViewModal(); }
});

// ── INIT ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  if (window.__LQ_SESSION__) AppData._session = window.__LQ_SESSION__;
  await Promise.all([AppData.loadZones(), AppData.loadAlerts()]);
  AppData.applySession();
  startClock();
  renderStats();
  renderTable();
  AppData.updateNotifBadge();

  setInterval(async () => {
    await Promise.all([AppData.loadZones(), AppData.loadAlerts()]);
    renderStats();
    renderTable();
    AppData.updateNotifBadge();
  }, 5000);
});
  </script>
</body>
</html>