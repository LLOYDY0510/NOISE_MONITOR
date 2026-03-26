// ============================================================
//  LibraryQuiet – monitoring-manager.js
//  Shared by: Library Manager + Library Staff (view only)
//  Requires:  app-data.js loaded first
// ============================================================

// ── HELPERS ────────────────────────────────────────────────
function noiseColor(db)  { return db < 40 ? '#10b981' : db < 60 ? '#f59e0b' : '#ef4444'; }
function noiseStatus(db) { return db < 40 ? 'quiet'   : db < 60 ? 'moderate' : 'loud'; }
function statusStyle(s)  {
  if (s==='quiet')    return { bg:'#d1fae5', color:'#065f46', dot:'#10b981' };
  if (s==='moderate') return { bg:'#fef3c7', color:'#92400e', dot:'#f59e0b' };
  return                     { bg:'#fee2e2', color:'#991b1b', dot:'#ef4444' };
}
function battColor(p) { return p>60 ? '#10b981' : p>30 ? '#f59e0b' : '#ef4444'; }
function el(id)        { return document.getElementById(id); }
function setText(id,v) { const e=el(id); if(e) e.textContent=v; }

// ── CLOCK ──────────────────────────────────────────────────
function startClock() {
  const update = () => {
    const now = new Date();
    setText('tb-date', now.toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'}) + ' · ' + now.toLocaleTimeString('en-PH'));
  };
  update(); setInterval(update, 1000);
}

function toggleSidebar() { el('sidebar').classList.toggle('collapsed'); }

// ── ROLE-AWARE SIDEBAR ─────────────────────────────────────
function buildSidebar() {
  const session = AppData.getSession();
  if (!session) { window.location.href = 'login.html'; return; }

  const isStaff   = session.role === 'Library Staff';
  const initial   = session.name[0].toUpperCase();
  const roleIcon  = isStaff ? '👤' : '📋';
  const roleColor = isStaff
    ? 'linear-gradient(135deg,#7c3aed,#8b5cf6)'
    : 'linear-gradient(135deg,#0d9488,#0f766e)';
  const roleTextColor = isStaff ? '#a78bfa' : '#2dd4bf';
  const dashLink  = isStaff ? 'dashboard-staff.html' : '/NOISE_MONITOR/dashboards/manager/dashboard-manager.php';

  // Avatars
  ['sb-av','sb-av-lg','tb-av'].forEach(id => {
    const e = el(id);
    if (e) { e.textContent = initial; e.style.background = roleColor; }
  });

  setText('sb-uname', session.name);
  setText('sb-bname', session.name);
  setText('sb-brole', session.role);

  const urole = el('sb-urole');
  if (urole) { urole.textContent = roleIcon + ' ' + session.role; urole.style.color = roleTextColor; }

  // Topbar role badge
  const badge = el('role-badge');
  if (badge) {
    badge.textContent = roleIcon + ' ' + session.role;
    badge.className   = isStaff ? 'role-badge staff-badge' : 'role-badge manager-badge';
  }

  // Nav links — staff cannot access reports
  const reportsNav = isStaff
    ? `<div class="nav-item nav-locked" style="opacity:.4;cursor:not-allowed;">
         <span class="ni">▤</span><span class="nl">Reports 🔒</span>
       </div>`
    : `<a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/reports-manager.php"><span class="ni">▤</span><span class="nl">Reports</span></a>`;

  const nav = el('sidebar-nav');
  if (nav) nav.innerHTML = `
    <div class="sb-sec">Main</div>
    <a class="nav-item" href="${dashLink}"><span class="ni">⊞</span><span class="nl">Dashboard</span></a>
    <a class="nav-item active" href="/NOISE_MONITOR/dashboards/manager/monitor-manager.php"><span class="ni">◎</span><span class="nl">Live Monitoring</span></a>
    <div class="sb-div"></div>
    <div class="sb-sec">View Only</div>
    <a class="nav-item" href="/NOISE_MONITOR/dashboards/manager/alerts-manager.php">
      <span class="ni">⚑</span><span class="nl">Alert Logs</span>
      <span class="nb" id="alert-nb">0</span>
    </a>
    ${reportsNav}
  `;
}

// ── OVERALL METER ──────────────────────────────────────────
function renderOverall() {
  const zones  = AppData.getZones();
  const avg    = zones.reduce((a,z) => a + z.level, 0) / zones.length;
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

// ── ZONE CARDS ─────────────────────────────────────────────
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

// ── SENSOR TABLE ───────────────────────────────────────────
function renderSensorTable() {
  const tbody = el('sensor-tbody'); if (!tbody) return;
  const zones = AppData.getZones();
  tbody.innerHTML = zones.map((z, i) => {
    const col = noiseColor(z.level);
    const bc  = battColor(z.battery || 80);
    return `<tr>
      <td class="mono">SNS-00${i + 1}</td>
      <td style="font-weight:700;">${z.name}</td>
      <td>${z.floor}</td>
      <td>
        <span id="sval-${z.id}" style="font-weight:900;color:${col};font-family:'JetBrains Mono',monospace;">
          ${Math.round(z.level)} dB
        </span>
      </td>
      <td><span class="badge b-green">● Online</span></td>
      <td>
        <div class="batt-wrap">
          <div class="batt-bar">
            <div class="batt-fill" style="width:${z.battery || 80}%;background:${bc};"></div>
          </div>
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

// ── LIVE UPDATE (read only) ────────────────────────────────
function startLiveUpdate() {
  setInterval(async () => {
    await Promise.all([AppData.loadZones(), AppData.loadAlerts()]);
    renderOverall();
    updateZoneCards();
    updateSensorTable();
    AppData.updateNotifBadge();
  }, 2000);
}

// ── INIT ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  if (window.__LQ_SESSION__) AppData._session = window.__LQ_SESSION__;
  await Promise.all([AppData.loadZones(), AppData.loadAlerts(), AppData.loadSensorOverrides()]);
  buildSidebar();
  startClock();
  renderOverall();
  renderZoneGrid();
  renderSensorTable();
  AppData.updateNotifBadge();
  startLiveUpdate();
});