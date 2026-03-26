// ============================================================
//  LibraryQuiet – alerts-manager.js
//  Auto-detects new alerts every 3s — Manager can resolve
// ============================================================

let currentTab     = 'all';
let viewTargetId   = null;
let currentFilters = { search:'', type:'', zone:'' };
let lastAlertCount = 0; // track new alerts

function noiseColor(db){ return db<40?'#10b981':db<60?'#f59e0b':'#ef4444'; }
function el(id)        { return document.getElementById(id); }
function setText(id,v) { const e=el(id); if(e) e.textContent=v; }
function escapeHtml(s) { return String(s).replace(/&/g,'&amp;').replace(/</g,'&lt;').replace(/>/g,'&gt;').replace(/\n/g,'<br>'); }

function startClock(){
  const u=()=>{const n=new Date();setText('tb-date',n.toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' · '+n.toLocaleTimeString('en-PH'));};
  u(); setInterval(u,1000);
}
function toggleSidebar(){ el('sidebar').classList.toggle('collapsed'); }

// ── TOAST ──────────────────────────────────────────────────
function showToast(msg, type='success'){
  const t=el('toast'); if(!t) return;
  t.textContent=msg; t.className='toast show';
  t.style.background=type==='error'?'#ef4444':type==='info'?'#3b82f6':'#0d9488';
  clearTimeout(t._t); t._t=setTimeout(()=>t.className='toast',4000);
}

// ── NEW ALERT NOTIFICATION ─────────────────────────────────
function notifyNewAlerts(newCount){
  if(newCount <= lastAlertCount) return;
  const diff = newCount - lastAlertCount;
  // Flash the page title
  let blink=0;
  const orig = document.title;
  const iv = setInterval(()=>{
    document.title = blink%2===0 ? `🔔 ${diff} NEW ALERT${diff>1?'S':''}!` : orig;
    blink++;
    if(blink>6){ clearInterval(iv); document.title=orig; }
  },600);
  // Show toast notification
  showToast(`🔔 ${diff} new alert${diff>1?'s':''} detected!`, 'error');
  // Flash the alert badge
  const bc=el('bell-count');
  if(bc){ bc.classList.add('pulse-badge'); setTimeout(()=>bc.classList.remove('pulse-badge'),3000); }
}

// ── STATS ──────────────────────────────────────────────────
function renderStats(){
  const alerts=AppData.getAlerts();
  const active=alerts.filter(a=>a.status==='active').length;
  setText('s-active',   active);
  setText('s-critical', alerts.filter(a=>a.type==='critical').length);
  setText('s-resolved', alerts.filter(a=>a.status==='resolved').length);
  setText('s-total',    alerts.length);
  const bc=el('bell-count'); if(bc){ bc.textContent=active; bc.style.display=active>0?'':'none'; }
  const nb=el('alert-nb');   if(nb){ nb.textContent=active; nb.style.display=active>0?'':'none'; }
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

// ── TABLE ──────────────────────────────────────────────────
function renderTable(){
  const tbody=el('alerts-tbody'); if(!tbody) return;
  const alerts=getFiltered();
  const all=AppData.getAlerts().length;
  setText('tbl-count',`Showing ${alerts.length} of ${all} alert${all!==1?'s':''}`);

  if(!alerts.length){
    tbody.innerHTML=`<tr><td colspan="9" style="text-align:center;padding:40px;color:#94a3b8;">
      ${currentTab==='active'?'✅ No active alerts — all clear!':'No alerts found.'}
    </td></tr>`;
    return;
  }

  tbody.innerHTML=alerts.map((a,i)=>{
    const tb=a.type==='critical'
      ?'<span class="badge b-red">🔴 Critical</span>'
      :a.type==='warning'
      ?'<span class="badge b-yellow">⚠️ Warning</span>'
      :'<span class="badge b-green">ℹ Info</span>';
    const sb=a.status==='active'
      ?'<span class="badge b-red pulse-active">● Active</span>'
      :'<span class="badge b-gray">✅ Resolved</span>';
    const resolveBtn=a.status==='active'
      ?`<button class="tbl-btn tb-ack" onclick="quickResolve('${a.id}',event)" style="background:#0d9488;color:#fff;border:none;padding:5px 12px;border-radius:7px;font-size:11px;font-weight:700;cursor:pointer;">✅ Resolve</button>`
      :'';
    const msgCount=(a.messages||[]).length;
    const msgBubble=msgCount>0?`<span class="msg-bubble">${msgCount}</span>`:'';
    const rowStyle=a.type==='critical'&&a.status==='active'?'background:#fff5f5;':'';
    return `<tr style="${rowStyle}">
      <td style="color:#94a3b8;">${i+1}</td>
      <td style="font-size:12px;color:#64748b;white-space:nowrap;">${a.date||''}<br><strong>${a.time}</strong></td>
      <td style="font-weight:800;">${a.zone}</td>
      <td><span style="font-weight:900;color:${noiseColor(a.level)};font-size:15px;">${a.level} dB</span></td>
      <td>${tb}</td>
      <td style="color:#64748b;font-size:12px;">${a.msg}</td>
      <td>${sb}</td>
      <td style="font-size:12px;color:#64748b;">${a.resolvedBy||'—'}</td>
      <td>
        <div style="display:flex;gap:4px;align-items:center;">
          <button class="tbl-btn tb-view" onclick="openView('${a.id}')">💬 ${msgBubble}</button>
          ${resolveBtn}
        </div>
      </td>
    </tr>`;
  }).join('');
}

// ── QUICK RESOLVE ──────────────────────────────────────────
async function quickResolve(id,e){
  e.stopPropagation();
  const s=AppData.getSession();
  await AppData.resolveAlert(id, s?.name||'Library Manager');
  await AppData.loadAlerts();
  renderStats(); renderTable();
  if(viewTargetId===id) refreshViewModal(id);
  showToast('✅ Alert resolved successfully!');
}

// ── RESOLVE ALL ────────────────────────────────────────────
async function resolveAllActive(){
  const active=AppData.getActiveAlerts();
  if(!active.length){ showToast('No active alerts to resolve.','info'); return; }
  const name=AppData.getSession()?.name||'Library Manager';
  for(const a of active){ await AppData.resolveAlert(a.id,name); }
  await AppData.loadAlerts();
  renderStats(); renderTable(); AppData.updateNotifBadge();
  showToast(`✅ ${active.length} alert(s) resolved.`);
}

// ── SIMULATE ALERT ─────────────────────────────────────────
async function simulateAlert(){
  const zones=AppData.getZones();
  if(!zones.length){ showToast('⚠️ No zones loaded.','error'); return; }
  const zone=zones[Math.floor(Math.random()*zones.length)];
  const level=Math.floor(Math.random()*40)+55;
  const type=level>=75?'critical':'warning';
  const now=new Date();
  await AppData.addAlert({
    zone:zone.name, level, type,
    msg:type==='critical'?'Critical noise level detected':'Noise threshold exceeded',
    time:now.toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'}),
    date:now.toLocaleDateString('en-PH',{year:'numeric',month:'long',day:'numeric'}),
  });
  await AppData.loadAlerts();
  renderStats(); populateZoneFilter(); renderTable(); AppData.updateNotifBadge();
  showToast(`🔔 Simulated ${type} alert for ${zone.name} (${level} dB)`,'error');
}

// ── VIEW MODAL ─────────────────────────────────────────────
function openView(id){
  viewTargetId=id; refreshViewModal(id);
  const ov=el('view-overlay'),mo=el('view-modal');
  if(ov){ov.style.display='block';ov.style.zIndex='200';}
  if(mo){mo.style.display='flex';mo.style.zIndex='300';}
  setTimeout(()=>{const t=el('msg-thread');if(t)t.scrollTop=t.scrollHeight;},80);
}

function refreshViewModal(id){
  const a=AppData.getAlerts().find(x=>x.id===id); if(!a) return;
  const col=noiseColor(a.level);
  const session=AppData.getSession();
  const msgs=a.messages||[];
  const btn=el('modal-resolve-btn');
  if(btn){
    if(a.status==='resolved'){
      btn.disabled=true; btn.textContent='✓ Already Resolved'; btn.style.opacity='0.5'; btn.onclick=null;
    } else {
      btn.disabled=false; btn.textContent='✅ Resolve Alert'; btn.style.opacity='1';
      btn.style.background='linear-gradient(135deg,#0d9488,#0f766e)'; btn.style.color='#fff'; btn.style.border='none';
      btn.onclick=async()=>{
        await AppData.resolveAlert(id, session?.name||'Manager');
        await AppData.loadAlerts();
        refreshViewModal(id); renderStats(); renderTable();
        showToast('✅ Alert resolved!');
      };
    }
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
    <div style="border-top:1px solid #f1f5f9;padding-top:16px;">
      <div style="font-weight:800;font-size:13px;margin-bottom:4px;">💬 Notes & Messages <span style="background:#f1f5f9;color:#64748b;font-size:11px;padding:2px 8px;border-radius:20px;font-weight:600;">${msgs.length}</span></div>
      <div style="font-size:11px;color:#94a3b8;margin-bottom:12px;">Shared with Administrator</div>
      <div id="msg-thread" style="max-height:200px;overflow-y:auto;padding:4px 0;margin-bottom:12px;">
        ${msgs.length===0?'<div style="text-align:center;color:#94a3b8;font-size:12px;padding:20px;">No notes yet.</div>':msgs.map(m=>bubbleHtml(m,session)).join('')}
      </div>
      <div style="display:flex;gap:8px;align-items:flex-end;">
        <textarea id="msg-input" placeholder="Leave a note..." rows="2" style="flex:1;padding:10px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:13px;font-family:'Plus Jakarta Sans',sans-serif;resize:none;outline:none;" onfocus="this.style.borderColor='#0d9488'" onblur="this.style.borderColor='#e2e8f0'"></textarea>
        <button onclick="sendMessage('${id}')" style="padding:10px 16px;background:#0d9488;color:#fff;border:none;border-radius:10px;font-size:12px;font-weight:700;cursor:pointer;white-space:nowrap;">📨 Send</button>
      </div>
    </div>`;
  setTimeout(()=>{const t=el('msg-thread');if(t)t.scrollTop=t.scrollHeight;},30);
}

function bubbleHtml(m,session){
  const isMe=session&&m.from===session.name;
  const bg=isMe?'#f0fdfa':'#f8fafc';
  const align=isMe?'flex-end':'flex-start';
  return `<div style="display:flex;justify-content:${align};margin-bottom:8px;">
    <div style="max-width:80%;background:${bg};border-radius:12px;padding:8px 12px;border:1px solid ${isMe?'#99f6e4':'#e2e8f0'};">
      <div style="font-size:10px;color:#94a3b8;margin-bottom:3px;">${m.from} · ${m.role==='Administrator'?'👑':'📋'} · ${m.time}</div>
      <div style="font-size:13px;color:#0f172a;">${escapeHtml(m.text)}</div>
    </div>
  </div>`;
}

async function sendMessage(alertId){
  const input=el('msg-input'); const text=input?.value.trim();
  if(!text){ if(input)input.style.borderColor='#ef4444'; setTimeout(()=>{if(input)input.style.borderColor='#e2e8f0';},1500); return; }
  const session=AppData.getSession(); const now=new Date();
  await AppData.addAlertMessage(alertId,{
    from:session?.name||'Library Manager', role:session?.role||'Library Manager', text,
    time:now.toLocaleTimeString('en-PH',{hour:'2-digit',minute:'2-digit'}),
    date:now.toLocaleDateString('en-PH'),
  });
  await AppData.loadAlerts();
  if(input) input.value='';
  refreshViewModal(alertId); renderTable();
  setTimeout(()=>{const t=el('msg-thread');if(t)t.scrollTop=t.scrollHeight;},50);
  showToast('📨 Note sent!');
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
  renderStats();
  renderTable();
  AppData.updateNotifBadge();
  if(viewTargetId) refreshViewModal(viewTargetId);
}

// ── INIT ───────────────────────────────────────────────────
document.addEventListener('DOMContentLoaded', async () => {
  if(window.__LQ_SESSION__) AppData._session = window.__LQ_SESSION__;
  await Promise.all([AppData.loadAlerts(), AppData.loadZones()]);
  AppData.applySession();
  startClock();
  lastAlertCount = AppData.getActiveAlerts().length;
  renderStats();
  populateZoneFilter();
  renderTable();
  AppData.updateNotifBadge();
  // Poll every 3 seconds for new alerts
  setInterval(pollAlerts, 3000);
});