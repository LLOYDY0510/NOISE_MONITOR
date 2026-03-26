// ============================================================
//  LibraryQuiet – zones-admin.js (Admin: VIEW ONLY)
// ============================================================

function el(id)        { return document.getElementById(id); }
function setText(id,v) { const e=el(id); if(e) e.textContent=v; }
function noiseColor(db){ return db<40?'#10b981':db<60?'#f59e0b':'#ef4444'; }

function startClock(){
  const u=()=>{const n=new Date();setText('tb-date',n.toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' · '+n.toLocaleTimeString('en-PH'));};
  u(); setInterval(u,1000);
}
function toggleSidebar(){ el('sidebar').classList.toggle('collapsed'); }

function showToast(msg,type='info'){
  const t=el('toast'); if(!t) return;
  t.textContent=msg; t.className='toast show';
  t.style.background=type==='error'?'#ef4444':'#0f172a';
  clearTimeout(t._t); t._t=setTimeout(()=>t.className='toast',3000);
}

function renderStats(){
  const z=AppData.getZones();
  setText('s-total',    z.length);
  setText('s-active',   z.filter(x=>x.status==='active').length);
  setText('s-capacity', z.reduce((a,x)=>a+(x.capacity||0),0));
  setText('s-loud',     z.filter(x=>x.level>=(x.critThreshold||60)).length);
}

function getFiltered(){
  const q=(el('search-input')?.value||'').toLowerCase();
  const f=el('floor-filter')?.value||'';
  const s=el('status-filter')?.value||'';
  return AppData.getZones().filter(z=>{
    return (!q||z.name.toLowerCase().includes(q)||z.id.toLowerCase().includes(q)||(z.sensor||'').toLowerCase().includes(q))
        && (!f||z.floor===f)
        && (!s||z.status===s);
  });
}
function filterZones(){ renderTable(); }
function resetFilters(){
  if(el('search-input'))  el('search-input').value='';
  if(el('floor-filter'))  el('floor-filter').value='';
  if(el('status-filter')) el('status-filter').value='';
  renderTable();
}

function renderTable(){
  const tbody=el('zones-tbody'); if(!tbody) return;
  const zones=getFiltered();
  const all=AppData.getZones().length;
  setText('tbl-count',`Showing ${zones.length} of ${all} zone${all!==1?'s':''}`);

  if(!zones.length){
    tbody.innerHTML=`<tr><td colspan="10" style="text-align:center;padding:32px;color:#94a3b8;">No zones found.</td></tr>`;
    return;
  }

  tbody.innerHTML=zones.map(z=>{
    const col=noiseColor(z.level);
    const stBg=z.status==='active'?'#d1fae5':'#f1f5f9';
    const stCol=z.status==='active'?'#065f46':'#64748b';
    return `<tr>
      <td style="font-family:'JetBrains Mono',monospace;font-size:11px;color:#64748b;">${z.id}</td>
      <td style="font-weight:700;">${z.name}</td>
      <td><span style="background:#f1f5f9;padding:2px 8px;border-radius:6px;font-size:11px;font-weight:600;">${z.floor}</span></td>
      <td style="font-weight:600;">${z.capacity}</td>
      <td><span style="font-weight:900;color:${col};">${Math.round(z.level)} dB</span></td>
      <td style="color:#f59e0b;font-weight:700;">${z.warnThreshold} dB</td>
      <td style="color:#ef4444;font-weight:700;">${z.critThreshold} dB</td>
      <td style="font-family:'JetBrains Mono',monospace;font-size:11px;">${z.sensor||'—'}</td>
      <td><span style="background:${stBg};color:${stCol};padding:3px 10px;border-radius:20px;font-size:11px;font-weight:700;">${z.status}</span></td>
      <td>
        <button class="tbl-btn tb-view" onclick="openViewModal('${z.id}')">👁 View</button>
        <span style="font-size:10px;background:#fef9c3;color:#713f12;padding:2px 8px;border-radius:6px;font-weight:600;">🔒 View Only</span>
      </td>
    </tr>`;
  }).join('');
}

// ── VIEW MODAL ─────────────────────────────────────────────
function openViewModal(id){
  const z=AppData.getZone(id); if(!z) return;
  el('view-title').textContent=z.name;
  const col=noiseColor(z.level);
  const pct=Math.min(100,(z.level/90)*100).toFixed(1);
  el('view-body').innerHTML=`
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;padding:4px 0;">
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Zone ID</div><div style="font-family:'JetBrains Mono',monospace;font-weight:700;">${z.id}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Floor</div><div style="font-weight:700;">${z.floor}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Capacity</div><div style="font-weight:700;">${z.capacity} persons</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Occupied</div><div style="font-weight:700;">${z.occupied}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Current Noise</div>
        <div style="font-size:28px;font-weight:900;color:${col};">${Math.round(z.level)} dB</div>
        <div style="height:6px;background:#f1f5f9;border-radius:4px;overflow:hidden;margin-top:6px;"><div style="width:${pct}%;height:100%;background:${col};border-radius:4px;"></div></div>
      </div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Status</div>
        <span style="background:${z.status==='active'?'#d1fae5':'#f1f5f9'};color:${z.status==='active'?'#065f46':'#64748b'};padding:4px 12px;border-radius:20px;font-size:12px;font-weight:700;">${z.status}</span>
      </div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Sensor</div><div style="font-family:'JetBrains Mono',monospace;font-weight:700;">${z.sensor||'—'}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Battery</div><div style="font-weight:700;">🔋 ${z.battery||80}%</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Warn Threshold</div><div style="font-weight:700;color:#f59e0b;">${z.warnThreshold} dB</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Critical Threshold</div><div style="font-weight:700;color:#ef4444;">${z.critThreshold} dB</div></div>
    </div>
    ${z.desc?`<div style="margin-top:14px;padding:12px;background:#f8fafc;border-radius:8px;font-size:13px;color:#64748b;">${z.desc}</div>`:''}
    <div style="margin-top:14px;padding:10px 14px;background:#fef9c3;border:1px solid #fde047;border-radius:8px;font-size:12px;color:#713f12;font-weight:600;">
      🔒 Admin view only — contact Library Manager to edit zones.
    </div>
  `;
  // Show modal
  const ov=el('view-overlay'), mo=el('view-modal');
  if(ov){ ov.style.display='block'; ov.style.zIndex='200'; }
  if(mo){ mo.style.display='flex'; mo.style.zIndex='300'; }
}

function closeViewModal(){
  const ov=el('view-overlay'), mo=el('view-modal');
  if(ov) ov.style.display='none';
  if(mo) mo.style.display='none';
}

document.addEventListener('keydown', e=>{ if(e.key==='Escape') closeViewModal(); });

// ── INIT ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  if(window.__LQ_SESSION__) AppData._session = window.__LQ_SESSION__;
  await Promise.all([AppData.loadZones(), AppData.loadAlerts()]);
  AppData.applySession();
  startClock();
  renderStats();
  renderTable();
  AppData.updateNotifBadge();

  setInterval(async()=>{
    await Promise.all([AppData.loadZones(), AppData.loadAlerts()]);
    renderStats();
    renderTable();
    AppData.updateNotifBadge();
  }, 5000);
});