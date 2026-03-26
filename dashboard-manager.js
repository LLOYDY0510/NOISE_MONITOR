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
  await AppData.setSensorLevel(zoneId, val);
  await AppData.loadZones(); // this also triggers check_alerts
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