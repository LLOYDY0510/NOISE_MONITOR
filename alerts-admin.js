// ============================================================
//  LibraryQuiet – alerts-admin.js
//  Admin: VIEW ONLY — auto-polls every 3s
//  Cannot resolve, delete, or simulate alerts
// ============================================================

let currentTab     = 'all';
let viewTargetId   = null;
let currentFilters = { search:'', type:'', zone:'' };
let selectedIds    = new Set();
let lastAlertCount = 0;

function noiseColor(db){ return db<40?'#10b981':db<60?'#f59e0b':'#ef4444'; }
function el(id)        { return document.getElementById(id); }
function setText(id,v) { const e=el(id); if(e) e.textContent=v; }
function escapeHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>'); }

function startClock(){
  const u=()=>{const n=new Date();setText('tb-date',n.toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' · '+n.toLocaleTimeString('en-PH'));};
  u(); setInterval(u,1000);
}
function toggleSidebar(){ el('sidebar').classList.toggle('collapsed'); }

function showToast(msg,type='info'){
  const t=el('toast'); if(!t) return;
  t.textContent=msg; t.className='toast show';
  t.style.background=type==='error'?'#ef4444':type==='success'?'#0d9488':'#0f172a';
  clearTimeout(t._t); t._t=setTimeout(()=>t.className='toast',4000);
}

// ── NEW ALERT NOTIFICATION ─────────────────────────────────
function notifyNewAlerts(newCount){
  if(newCount <= lastAlertCount) return;
  const diff=newCount-lastAlertCount;
  let blink=0; const orig=document.title;
  const iv=setInterval(()=>{
    document.title=blink%2===0?`🔔 ${diff} NEW ALERT${diff>1?'S':''}!`:orig;
    blink++; if(blink>6){clearInterval(iv);document.title=orig;}
  },600);
  showToast(`🔔 ${diff} new alert${diff>1?'s':''} detected! Waiting for Manager to resolve.`,'error');
  const bc=el('bell-count');
  if(bc){bc.classList.add('pulse-badge');setTimeout(()=>bc.classList.remove('pulse-badge'),3000);}
}

// ── STATS ──────────────────────────────────────────────────
function renderStats(){
  const alerts=AppData.getAlerts();
  const active=alerts.filter(a=>a.status==='active').length;
  setText('s-active',   active);
  setText('s-critical', alerts.filter(a=>a.type==='critical').length);
  setText('s-resolved', alerts.filter(a=>a.status==='resolved').length);
  setText('s-total',    alerts.length);
  const bc=el('bell-count'); if(bc){bc.textContent=active;bc.style.display=active>0?'':'none';}
  const nb=el('alert-nb');   if(nb){nb.textContent=active;nb.style.display=active>0?'':'none';}
}

function populateZoneFilter(){
  const sel=el('zone-filter'); if(!sel) return;
  const zones=[...new Set(AppData.getAlerts().map(a=>a.zone))];
  const cur=sel.value;
  sel.innerHTML='<option value="">All Zones</option>'+zones.map(z=>`<option${cur===z?' selected':''}>${z}</option>`).join('');
}

function getFiltered(){
  let a=AppData.getAlerts();
  if(currentTab==='active')   a=a.filter(x=>x.status==='active');
  if(currentTab==='resolved') a=a.filter(x=>x.status==='resolved');
  if(currentFilters.search)   a=a.filter(x=>x.zone.toLowerCase().includes(currentFilters.search.toLowerCase())||x.msg.toLowerCase().includes(currentFilters.search.toLowerCase()));
  if(currentFilters.type)     a=a.filter(x=>x.type===currentFilters.type);
  if(currentFilters.zone)     a=a.filter(x=>x.zone===currentFilters.zone);
  return a;
}

function filterAlerts(){ currentFilters.search=el('search-input').value; currentFilters.type=el('type-filter').value; currentFilters.zone=el('zone-filter').value; renderTable(); }
function setTab(tab,btn){ currentTab=tab; document.querySelectorAll('.ftab').forEach(b=>b.classList.remove('active')); btn.classList.add('active'); renderTable(); }
function resetFilters(){ currentTab='all'; currentFilters={search:'',type:'',zone:''}; el('search-input').value=''; el('type-filter').value=''; el('zone-filter').value=''; document.querySelectorAll('.ftab').forEach(b=>b.classList.remove('active')); document.querySelector('.ftab').classList.add('active'); renderTable(); }
function toggleSelectAll(cb){ selectedIds=new Set(cb.checked?getFiltered().map(a=>a.id):[]); renderTable(); updateBulkBar(); }
function toggleSelect(id,cb){ cb.checked?selectedIds.add(id):selectedIds.delete(id); updateBulkBar(); }
function clearSelection(){ selectedIds.clear(); renderTable(); updateBulkBar(); }
function updateBulkBar(){ const bar=el('bulk-bar'); if(bar) bar.style.display=selectedIds.size>0?'flex':'none'; setText('bulk-count',selectedIds.size); }

// ── TABLE ──────────────────────────────────────────────────
function renderTable(){
  const tbody=el('alerts-tbody'); if(!tbody) return;
  const alerts=getFiltered();
  const all=AppData.getAlerts().length;
  setText('tbl-count',`Showing ${alerts.length} of ${all} alert${all!==1?'s':''}`);

  if(!alerts.length){
    tbody.innerHTML=`<tr><td colspan="10" style="text-align:center;padding:40px;color:#94a3b8;">
      ${currentTab==='active'?'✅ No active alerts — all clear!':'No alerts found.'}
    </td></tr>`;
    return;
  }

  tbody.innerHTML=alerts.map((a,i)=>{
    const tb=a.type==='critical'?'<span class="badge b-red">🔴 Critical</span>':a.type==='warning'?'<span class="badge b-yellow">⚠️ Warning</span>':'<span class="badge b-green">ℹ Info</span>';
    const sb=a.status==='active'?'<span class="badge b-red">● Active</span>':'<span class="badge b-gray">✅ Resolved</span>';
    const rowStyle=a.type==='critical'&&a.status==='active'?'background:#fff5f5;':'';
    const checked=selectedIds.has(a.id)?'checked':'';
    const ri=a.status==='resolved'?`<span style="font-size:11px;color:#64748b;">By ${a.resolvedBy||'Manager'}</span>`:'<span style="font-size:11px;color:#94a3b8;">Pending</span>';
    return `<tr style="${rowStyle}">
      <td><input type="checkbox" ${checked} onchange="toggleSelect('${a.id}',this)"/></td>
      <td style="color:#94a3b8;">${i+1}</td>
      <td style="font-size:12px;color:#64748b;white-space:nowrap;">${a.date||''}<br><strong>${a.time}</strong></td>
      <td style="font-weight:800;">${a.zone}</td>
      <td><span style="font-weight:900;color:${noiseColor(a.level)};font-size:15px;">${a.level} dB</span></td>
      <td>${tb}</td>
      <td style="color:#64748b;font-size:12px;">${a.msg}</td>
      <td>${sb}</td>
      <td>${ri}</td>
      <td>
        <button class="tbl-btn tb-view" onclick="openView('${a.id}')">👁 View</button>
        ${a.status==='active'?'<span style="font-size:10px;background:#fef9c3;color:#713f12;padding:2px 7px;border-radius:6px;font-weight:600;">🔒 Manager resolves</span>':''}
      </td>
    </tr>`;
  }).join('');
}

// ── BULK DELETE (admin can delete) ─────────────────────────
async function bulkDelete(){
  if(!selectedIds.size) return;
  if(!confirm(`Delete ${selectedIds.size} alert(s)?`)) return;
  for(const id of [...selectedIds]){ await AppData.deleteAlert(id); }
  await AppData.loadAlerts();
  selectedIds.clear();
  renderStats(); renderTable(); populateZoneFilter(); updateBulkBar();
  showToast(`🗑️ ${selectedIds.size} alert(s) deleted.`,'info');
}

// ── VIEW MODAL ─────────────────────────────────────────────
function openView(id){
  viewTargetId=id; refreshViewModal(id);
  const ov=el('view-overlay'),mo=el('view-modal');
  if(ov){ov.style.display='block';ov.style.zIndex='200';}
  if(mo){mo.style.display='flex';mo.style.zIndex='300';}
}

function refreshViewModal(id){
  const a=AppData.getAlerts().find(x=>x.id===id); if(!a) return;
  const col=noiseColor(a.level);
  const session=AppData.getSession();
  const msgs=a.messages||[];
  // Admin cannot resolve
  const btn=el('modal-resolve-btn');
  if(btn){
    btn.disabled=true;
    btn.textContent='🔒 Only Manager Can Resolve';
    btn.style.opacity='0.5';
    btn.style.cursor='not-allowed';
    btn.onclick=()=>showToast('🔒 Only Library Manager can resolve alerts.','error');
  }
  el('view-body').innerHTML=`
    <div style="display:grid;grid-template-columns:1fr 1fr;gap:14px;margin-bottom:16px;">
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Zone</div><div style="font-weight:800;font-size:15px;">${a.zone}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Noise Level</div><div style="font-size:26px;font-weight:900;color:${col};">${a.level} dB</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Type</div><div style="font-weight:700;">${a.type==='critical'?'🔴 Critical':a.type==='warning'?'⚠️ Warning':'ℹ Info'}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Status</div><div style="font-weight:700;">${a.status==='active'?'🔴 Active':'✅ Resolved'}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Date & Time</div><div style="font-size:12px;font-weight:600;">${a.date||''} · ${a.time}</div></div>
      <div><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Resolved By</div><div style="font-weight:600;">${a.resolvedBy||'—'}</div></div>
      <div style="grid-column:1/-1;"><div style="font-size:10px;color:#94a3b8;font-weight:600;text-transform:uppercase;margin-bottom:4px;">Message</div><div style="color:#64748b;font-size:13px;">${a.msg}</div></div>
    </div>
    ${msgs.length>0?`
    <div style="border-top:1px solid #f1f5f9;padding-top:14px;">
      <div style="font-weight:800;font-size:13px;margin-bottom:12px;">💬 Notes & Messages <span style="background:#f1f5f9;color:#64748b;font-size:11px;padding:2px 8px;border-radius:20px;">${msgs.length}</span></div>
      <div style="max-height:180px;overflow-y:auto;">
        ${msgs.map(m=>bubbleHtml(m,session)).join('')}
      </div>
    </div>`:''}
    ${a.status==='active'?`<div style="margin-top:14px;padding:10px 14px;background:#fef9c3;border:1px solid #fde047;border-radius:8px;font-size:12px;color:#713f12;font-weight:600;">🔒 Only Library Manager can resolve this alert.</div>`:''}
  `;
}

function bubbleHtml(m,session){
  const isMe=session&&m.from===session.name;
  const bg=isMe?'#eff6ff':'#f8fafc';
  const align=isMe?'flex-end':'flex-start';
  return `<div style="display:flex;justify-content:${align};margin-bottom:8px;">
    <div style="max-width:80%;background:${bg};border-radius:12px;padding:8px 12px;border:1px solid ${isMe?'#bfdbfe':'#e2e8f0'};">
      <div style="font-size:10px;color:#94a3b8;margin-bottom:3px;">${m.from} · ${m.role==='Administrator'?'👑':'📋'} · ${m.time}</div>
      <div style="font-size:13px;color:#0f172a;">${escapeHtml(m.text)}</div>
    </div>
  </div>`;
}

function closeView(){
  const ov=el('view-overlay'),mo=el('view-modal');
  if(ov)ov.style.display='none'; if(mo)mo.style.display='none';
  viewTargetId=null;
}

document.addEventListener('keydown',e=>{ if(e.key==='Escape') closeView(); });

// ── LIVE POLL — every 3s ───────────────────────────────────
async function pollAlerts(){
  await AppData.loadAlerts();
  const active=AppData.getActiveAlerts().length;
  notifyNewAlerts(active);
  lastAlertCount=active;
  renderStats(); renderTable(); populateZoneFilter(); AppData.updateNotifBadge();
  if(viewTargetId) refreshViewModal(viewTargetId);
}

// ── INIT ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  if(window.__LQ_SESSION__) AppData._session = window.__LQ_SESSION__;
  await Promise.all([AppData.loadAlerts(), AppData.loadZones()]);
  AppData.applySession();
  startClock();
  lastAlertCount=AppData.getActiveAlerts().length;
  renderStats();
  populateZoneFilter();
  renderTable();
  AppData.updateNotifBadge();
  // Poll every 3 seconds
  setInterval(pollAlerts, 3000);
});