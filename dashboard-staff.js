// ============================================================
//  LibraryQuiet – dashboard-staff.js (PHP version)
//  Staff: view only — no edit/resolve/delete
// ============================================================

const HOURLY = [22,28,35,48,42,38,55,61,58,45,38,30];
const HOURS  = ['8AM','9AM','10AM','11AM','12PM','1PM','2PM','3PM','4PM','5PM','6PM','7PM'];

function noiseColor(db)  { return db<40?'#10b981':db<60?'#f59e0b':'#ef4444'; }
function noiseStatus(db) { return db<40?'quiet':db<60?'moderate':'loud'; }
function el(id)          { return document.getElementById(id); }
function setText(id,v)   { const e=el(id); if(e) e.textContent=v; }

function startClock(){
  const u=()=>{const n=new Date();setText('tb-date',n.toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' · '+n.toLocaleTimeString('en-PH'));};
  u(); setInterval(u,1000);
}
function toggleSidebar(){ el('sidebar').classList.toggle('collapsed'); }

function renderStats(){
  const zones=AppData.getZones(); if(!zones.length) return;
  const avg=Math.round(zones.reduce((a,z)=>a+z.level,0)/zones.length);
  const quiet=zones.filter(z=>noiseStatus(z.level)==='quiet').length;
  const loud=zones.filter(z=>noiseStatus(z.level)==='loud').length;
  const active=AppData.getActiveAlerts().length;
  setText('s-avg',   avg+' dB');
  setText('s-quiet', quiet+' / '+zones.length);
  setText('s-loud',  loud);
  setText('s-alerts',active);
  const qas=el('qa-alert-sub'); if(qas) qas.textContent=active+' active alert'+(active!==1?'s':'');
  const tr=el('s-avg-trend');
  if(tr){
    if(avg<40)      { tr.textContent='↓ All zones quiet'; tr.className='stat-trend trend-green'; }
    else if(avg<60) { tr.textContent='→ Moderate level';  tr.className='stat-trend trend-blue'; }
    else            { tr.textContent='↑ Elevated noise!'; tr.className='stat-trend trend-red'; }
  }
  const at=el('s-alert-trend');
  if(at){ at.textContent=active>0?`↑ ${active} alert${active!==1?'s':''} active`:'✓ No active alerts'; at.className=active>0?'stat-trend trend-red':'stat-trend trend-green'; }
  const ll=el('s-loud-lbl');
  if(ll){ ll.textContent=loud>0?`↑ ${loud} zone${loud>1?'s':''} above threshold`:'✓ No loud zones'; ll.className=loud>0?'stat-trend trend-red':'stat-trend trend-green'; }
  const bc=el('bell-count'); if(bc){bc.textContent=active;bc.style.display=active>0?'':'none';}
  const nb=el('alert-nb');   if(nb){nb.textContent=active;nb.style.display=active>0?'':'none';}
}

function renderZoneBars(){
  const wrap=el('zone-bars'); if(!wrap) return;
  wrap.innerHTML=AppData.getZones().map(z=>{
    const s=noiseStatus(z.level),col=noiseColor(z.level);
    const pct=Math.min(100,(z.level/90)*100).toFixed(1);
    const bg=s==='quiet'?'#d1fae5':s==='moderate'?'#fef3c7':'#fee2e2';
    const tc=s==='quiet'?'#065f46':s==='moderate'?'#92400e':'#991b1b';
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

function renderChart(){
  const wrap=el('chart-wrap'); if(!wrap) return;
  const max=Math.max(...HOURLY);
  wrap.innerHTML=HOURLY.map((v,i)=>{
    const h=Math.round((v/max)*120),bg=v>=60?'#ef4444':v>=40?'#f59e0b':'#3b82f6',pk=v===max;
    return `<div style="flex:1;display:flex;flex-direction:column;align-items:center;gap:4px;">
      ${pk?`<div style="font-size:9px;color:#ef4444;font-weight:700;">${v}</div>`:''}
      <div style="width:100%;height:${h}px;background:${bg};border-radius:5px 5px 0 0;opacity:.85;"></div>
      <span style="font-size:9px;color:#94a3b8;">${HOURS[i]}</span>
    </div>`;
  }).join('');
}

function renderAlerts(){
  const tbody=el('alerts-tbody'); if(!tbody) return;
  const alerts=AppData.getAlerts().slice(0,5);
  if(!alerts.length){ tbody.innerHTML=`<tr><td colspan="6" style="text-align:center;padding:20px;color:#94a3b8;">No alerts yet.</td></tr>`; return; }
  tbody.innerHTML=alerts.map(a=>{
    const tb=a.type==='critical'?'<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">🔴 Critical</span>':a.type==='warning'?'<span style="background:#fef3c7;color:#92400e;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">⚠️ Warning</span>':'<span style="background:#d1fae5;color:#065f46;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">ℹ Info</span>';
    const sb=a.status==='active'?'<span style="background:#fee2e2;color:#991b1b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Active</span>':'<span style="background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:20px;font-size:11px;font-weight:700;">Resolved</span>';
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

function renderSummary(){
  const zones=AppData.getZones();
  setText('sum-quiet',    zones.filter(z=>noiseStatus(z.level)==='quiet').length);
  setText('sum-moderate', zones.filter(z=>noiseStatus(z.level)==='moderate').length);
  setText('sum-loud',     zones.filter(z=>noiseStatus(z.level)==='loud').length);
}

function renderStaffInfo(){
  const wrap=el('staff-info'); if(!wrap) return;
  const session=AppData.getSession(); if(!session) return;
  wrap.innerHTML=`
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
  if(window.__LQ_SESSION__) AppData._session = window.__LQ_SESSION__;
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

  // Live refresh every 5s
  setInterval(async()=>{
    await Promise.all([AppData.loadZones(), AppData.loadAlerts()]);
    renderStats();
    renderZoneBars();
    renderSummary();
    renderAlerts();
    AppData.updateNotifBadge();
  }, 5000);
});