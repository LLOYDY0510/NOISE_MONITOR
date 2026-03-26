// ============================================================
//  LibraryQuiet – dashboard-admin.js (PHP version)
//  Admin: view-only — reads from API, no sensor input, no resolve
// ============================================================

const HOURLY = [22,28,35,48,42,38,55,61,58,45,38,30];
const HOURS  = ['8AM','9AM','10AM','11AM','12PM','1PM','2PM','3PM','4PM','5PM','6PM','7PM'];

function noiseColor(db)  { return db<40?'#10b981':db<60?'#f59e0b':'#ef4444'; }
function noiseStatus(db) { return db<40?'quiet':db<60?'moderate':'loud'; }
function statusStyle(s)  {
  if (s==='quiet')    return { bg:'#d1fae5', color:'#065f46', dot:'#10b981' };
  if (s==='moderate') return { bg:'#fef3c7', color:'#92400e', dot:'#f59e0b' };
  return                     { bg:'#fee2e2', color:'#991b1b', dot:'#ef4444' };
}
function el(id)        { return document.getElementById(id); }
function setText(id,v) { const e=el(id); if(e) e.textContent=v; }

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

function renderStats() {
  const zones  = AppData.getZones();
  if (!zones.length) return;
  const avg    = Math.round(zones.reduce((a,z)=>a+z.level,0)/zones.length);
  const quiet  = zones.filter(z=>noiseStatus(z.level)==='quiet').length;
  const loud   = zones.filter(z=>noiseStatus(z.level)==='loud').length;
  const active = AppData.getActiveAlerts().length;

  setText('s-avg',    avg+' dB');
  setText('s-quiet',  quiet+' / '+zones.length);
  setText('s-loud',   loud);
  setText('s-alerts', active);

  const qas = el('qa-alert-sub');
  if (qas) qas.textContent = active+' active alert'+(active!==1?'s':'');

  const tr = el('s-avg-trend');
  if (tr) {
    if (avg<40)      { tr.textContent='↓ All zones in good range'; tr.className='stat-trend trend-green'; }
    else if (avg<60) { tr.textContent='→ Moderate overall level';  tr.className='stat-trend trend-blue';  }
    else             { tr.textContent='↑ Elevated noise detected';  tr.className='stat-trend trend-red';   }
  }
  const at = el('s-alert-trend');
  if (at) {
    at.textContent = active>0?`↑ ${active} alert${active!==1?'s':''} need attention`:'✓ No active alerts';
    at.className   = active>0?'stat-trend trend-red':'stat-trend trend-green';
  }
  const ll = el('s-loud-lbl');
  if (ll) {
    ll.textContent = loud>0?`↑ ${loud} zone${loud>1?'s':''} need attention`:'✓ No loud zones';
    ll.className   = loud>0?'stat-trend trend-red':'stat-trend trend-green';
  }

  const unread = AppData.getUnreadReports();
  if (unread.length>0) showReportNotif(unread);
  else { const b=el('report-notif'); if(b) b.remove(); }
}

function showReportNotif(unread) {
  let banner = el('report-notif');
  if (!banner) {
    banner = document.createElement('div');
    banner.id = 'report-notif';
    banner.style.cssText = 'background:linear-gradient(135deg,#1d4ed8,#3b82f6);color:#fff;padding:12px 20px;border-radius:12px;margin-bottom:16px;display:flex;justify-content:space-between;align-items:center;font-size:13px;font-weight:600;box-shadow:0 4px 16px rgba(29,78,216,.3);';
    el('content').insertBefore(banner, el('content').firstChild);
  }
  banner.innerHTML = `<span>📋 You have <strong>${unread.length} unread report${unread.length>1?'s':''}</strong> from the Library Manager</span><a href="reports-admin.php" style="background:#fff;color:#1d4ed8;padding:6px 14px;border-radius:8px;font-weight:700;text-decoration:none;font-size:12px;">View Reports →</a>`;
}

function renderSensorView() {
  const grid = el('sensor-view-grid'); if (!grid) return;
  const zones = AppData.getZones();
  const overrides = AppData.getSensorOverrides();
  grid.innerHTML = zones.map(z => {
    const db = Math.round(z.level);
    const col = noiseColor(db);
    const st  = noiseStatus(db);
    const sc  = statusStyle(st);
    const lbl = db<40?'Quiet':db<60?'Moderate':'Loud';
    const pct = Math.min(100,(z.level/90)*100).toFixed(1);
    const ov  = overrides[z.id];
    const isM = z.manualOverride && ov;
    return `<div class="svp-card ${st}${isM?' manual':''}" id="svpc-${z.id}">
      ${isM?`<div class="svp-manual-badge">⚙ Set by Manager · ${ov.setAt}</div>`:''}
      <div class="svp-name">${z.name}</div>
      <div class="svp-id">${z.sensor} · Floor ${z.floor} · 🔋${z.battery||80}%</div>
      <div class="svp-level" id="svpl-${z.id}" style="color:${col};">${db} <span style="font-size:14px;font-weight:600;color:#94a3b8;">dB</span></div>
      <div class="svp-bar-track"><div class="svp-bar-fill" id="svpb-${z.id}" style="width:${pct}%;background:${col};"></div></div>
      <div class="svp-footer">
        <span style="background:${sc.bg};color:${sc.color};padding:2px 8px;border-radius:20px;font-weight:700;font-size:10px;">${lbl}</span>
        <span>Warn: ${z.warnThreshold} / Crit: ${z.critThreshold} dB</span>
      </div>
      <div class="svp-readonly">🔒 Read only — <a href="dashboard-manager.php" style="color:#0d9488;font-weight:700;">Manager</a> controls sensor input</div>
    </div>`;
  }).join('');
}

function updateSensorView() {
  AppData.getZones().forEach(z => {
    const db=Math.round(z.level), col=noiseColor(db), pct=Math.min(100,(z.level/90)*100).toFixed(1);
    const lEl=el('svpl-'+z.id), bEl=el('svpb-'+z.id), cEl=el('svpc-'+z.id);
    const isM=z.manualOverride&&AppData.getSensorOverrides()[z.id];
    if(lEl){lEl.innerHTML=db+' <span style="font-size:14px;font-weight:600;color:#94a3b8;">dB</span>';lEl.style.color=col;}
    if(bEl){bEl.style.width=pct+'%';bEl.style.background=col;}
    if(cEl){cEl.className='svp-card '+noiseStatus(db)+(isM?' manual':'');}
  });
}

function renderZoneBars() {
  const wrap = el('zone-bars'); if (!wrap) return;
  wrap.innerHTML = AppData.getZones().map(z => {
    const s=noiseStatus(z.level), sc=statusStyle(s), pct=Math.min(100,(z.level/90)*100).toFixed(1), col=noiseColor(z.level);
    return `<div class="zone-row"><div class="zone-meta"><div class="zone-left"><div class="zone-dot" style="background:${sc.dot};"></div><span class="zone-name">${z.name}</span><span class="zone-floor">${z.floor}</span>${z.manualOverride?'<span style="font-size:9px;background:#fef3c7;color:#92400e;padding:1px 6px;border-radius:4px;font-weight:700;">⚙ Manual</span>':''}</div><div class="zone-right"><span class="zone-db" style="color:${col};">${Math.round(z.level)} dB</span><span class="zone-badge" style="background:${sc.bg};color:${sc.color};">${s}</span></div></div><div class="bar-track"><div class="bar-fill" style="width:${pct}%;background:${col};"></div></div></div>`;
  }).join('');
}

function renderChart() {
  const wrap = el('chart-wrap'); if (!wrap) return;
  const max = Math.max(...HOURLY);
  wrap.innerHTML = HOURLY.map((v,i) => {
    const h=Math.round((v/max)*120), bg=v>=60?'#ef4444':v>=40?'#f59e0b':'#3b82f6', pk=v===max;
    return `<div class="chart-col">${pk?`<div style="font-size:9px;color:#ef4444;font-weight:700;margin-bottom:2px;">${v}</div>`:''}<div class="chart-bar" style="height:${h}px;background:${bg};${pk?'box-shadow:0 0 8px rgba(239,68,68,.4);':''}"></div><span class="chart-lbl">${HOURS[i]}</span></div>`;
  }).join('');
}

function renderActiveUsers() {
  const wrap=el('active-users-list'); if(!wrap) return;
  const session=AppData.getSession();
  const users=AppData.getUsers().filter(u=>u.status==='active');
  const roleColor=r=>r==='Administrator'?'#3b82f6':r==='Library Manager'?'#0d9488':'#8b5cf6';
  const roleIcon=r=>r==='Administrator'?'👑':r==='Library Manager'?'📋':'👤';
  const onlineNow=users.map((u,i)=>({...u,online:(session&&u.email===session.email)||i===0}));
  setText('active-user-count',onlineNow.filter(u=>u.online).length+' online');
  wrap.innerHTML=onlineNow.map(u=>`<div class="active-user-row"><div class="au-av" style="background:${roleColor(u.role)};">${u.name[0].toUpperCase()}</div><div class="au-info"><div class="au-name">${u.name}${u.online&&session&&u.email===session.email?' <span class="au-you">(You)</span>':''}</div><div class="au-role">${roleIcon(u.role)} ${u.role}</div></div><div class="au-status"><div class="au-dot ${u.online?'dot-online':'dot-away'}"></div><span class="au-status-txt">${u.online?'Online':'Away'}</span></div><div class="au-time">${u.lastLogin||'—'}</div></div>`).join('');
}

function renderActivityLog() {
  const tbody=el('activity-tbody'); if(!tbody) return;
  const session=AppData.getSession();
  const roleColor=r=>r==='Administrator'?'#3b82f6':r==='Library Manager'?'#0d9488':'#8b5cf6';
  const roleBg=r=>r==='Administrator'?'#eff6ff':r==='Library Manager'?'#f0fdfa':'#faf5ff';
  tbody.innerHTML=AppData.getUsers().map(u=>{
    const isMe=session&&u.email===session.email;
    const badge=isMe?'<span class="badge" style="background:#d1fae5;color:#065f46;">🟢 Online</span>':u.status==='active'?'<span class="badge" style="background:#fef3c7;color:#92400e;">🟡 Away</span>':'<span class="badge" style="background:#fee2e2;color:#991b1b;">🔴 Inactive</span>';
    return `<tr><td><div style="display:flex;align-items:center;gap:8px;"><div style="width:28px;height:28px;border-radius:8px;background:${roleColor(u.role)};color:#fff;display:flex;align-items:center;justify-content:center;font-weight:800;font-size:12px;">${u.name[0].toUpperCase()}</div><div><div style="font-weight:700;font-size:13px;">${u.name}</div><div style="font-size:11px;color:#94a3b8;">${u.email}</div></div></div></td><td><span class="badge" style="background:${roleBg(u.role)};color:${roleColor(u.role)};">${u.role}</span></td><td style="font-size:12px;color:#64748b;">${u.lastLogin||'—'}</td><td>${badge}</td></tr>`;
  }).join('');
}

function renderAlerts() {
  const tbody=el('alerts-tbody'); if(!tbody) return;
  tbody.innerHTML=AppData.getAlerts().slice(0,5).map(a=>{
    const tb=a.type==='critical'?'<span class="badge b-red">Critical</span>':a.type==='warning'?'<span class="badge b-yellow">Warning</span>':'<span class="badge b-green">Info</span>';
    const sb=a.status==='active'?'<span class="badge b-red">Active</span>':'<span class="badge b-gray">Resolved</span>';
    const ri=a.status==='resolved'?`<span style="font-size:11px;color:#94a3b8;">By ${a.resolvedBy||'Manager'}</span>`:'<span style="font-size:11px;color:#94a3b8;">Pending</span>';
    return `<tr><td style="color:#64748b;font-size:12px;">${a.time}</td><td style="font-weight:700;">${a.zone}</td><td><span style="font-weight:900;color:${noiseColor(a.level)};">${a.level} dB</span></td><td>${tb}</td><td style="color:#64748b;">${a.msg}</td><td>${sb}</td><td>${ri}</td></tr>`;
  }).join('');
}

function renderSummary() {
  const zones=AppData.getZones();
  setText('sum-quiet',    zones.filter(z=>noiseStatus(z.level)==='quiet').length);
  setText('sum-moderate', zones.filter(z=>noiseStatus(z.level)==='moderate').length);
  setText('sum-loud',     zones.filter(z=>noiseStatus(z.level)==='loud').length);
}

// ── LIVE UPDATE — reads from DB every 3s ─────────────────
function startLiveUpdate() {
  setInterval(async () => {
    await Promise.all([
      AppData.loadZones(),
      AppData.loadAlerts(),
      AppData.loadSensorOverrides(),
    ]);
    renderStats();
    renderZoneBars();
    renderSummary();
    renderAlerts();
    updateSensorView();
    AppData.updateNotifBadge();
  }, 3000);
}

document.addEventListener('DOMContentLoaded', async () => {
  // Use PHP-injected session if available
  if (window.__LQ_SESSION__) {
    AppData._session = window.__LQ_SESSION__;
  }
  startClock();
  AppData.applySession();
  await Promise.all([
    AppData.loadZones(),
    AppData.loadAlerts(),
    AppData.loadSensorOverrides(),
    AppData.loadUsers(),
    AppData.loadReports(),
  ]);
  renderStats(); renderSensorView(); renderZoneBars();
  renderChart(); renderAlerts(); renderSummary();
  renderActiveUsers(); renderActivityLog();
  startLiveUpdate();
});