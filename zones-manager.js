// ============================================================
//  LibraryQuiet – zones.js (Manager only)
// ============================================================

function el(id)        { return document.getElementById(id); }
function setText(id,v) { const e=el(id); if(e) e.textContent=v; }
function noiseColor(db){ return db<40?'#10b981':db<60?'#f59e0b':'#ef4444'; }
function noiseStatus(db){ return db<40?'quiet':db<60?'moderate':'loud'; }

let deleteTargetId = '';

// ── CLOCK ──────────────────────────────────────────────────
function startClock(){
  const u=()=>{const n=new Date();setText('tb-date',n.toLocaleDateString('en-PH',{weekday:'long',year:'numeric',month:'long',day:'numeric'})+' · '+n.toLocaleTimeString('en-PH'));};
  u(); setInterval(u,1000);
}
function toggleSidebar(){ el('sidebar').classList.toggle('collapsed'); }

// ── TOAST ──────────────────────────────────────────────────
function showToast(msg, type='success'){
  const t=el('toast'); if(!t) return;
  t.textContent=msg;
  t.className='toast show';
  t.style.background = type==='error'?'#ef4444':type==='info'?'#3b82f6':'#0f172a';
  clearTimeout(t._t);
  t._t=setTimeout(()=>{ t.className='toast'; },3000);
}

// ── STATS ──────────────────────────────────────────────────
function renderStats(){
  const z=AppData.getZones();
  setText('s-total',    z.length);
  setText('s-active',   z.filter(x=>x.status==='active').length);
  setText('s-capacity', z.reduce((a,x)=>a+(x.capacity||0),0));
  setText('s-loud',     z.filter(x=>x.level>=(x.critThreshold||60)).length);
}

// ── FILTER ─────────────────────────────────────────────────
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

// ── TABLE ──────────────────────────────────────────────────
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
        <button class="tbl-btn tb-view" onclick="openViewModal('${z.id}')">👁</button>
        <button class="tbl-btn tb-edit" onclick="openEditModal('${z.id}')">✏️</button>
        <button class="tbl-btn tb-del"  onclick="openDeleteModal('${z.id}')">🗑</button>
      </td>
    </tr>`;
  }).join('');
}

// ── MODAL HELPERS ──────────────────────────────────────────
function openModal(overlayId, modalId){
  const ov=el(overlayId), mo=el(modalId);
  if(ov){ ov.style.display='block'; ov.style.zIndex='200'; }
  if(mo){ mo.style.display='flex'; mo.style.zIndex='300'; }
}
function closeModalById(overlayId, modalId){
  const ov=el(overlayId), mo=el(modalId);
  if(ov) ov.style.display='none';
  if(mo) mo.style.display='none';
}

// ── ADD MODAL ──────────────────────────────────────────────
function openAddModal(){
  el('modal-title').textContent='Add New Zone';
  el('modal-sub').textContent='Fill in zone details';
  el('edit-id').value='';
  el('f-name').value=''; el('f-sensor').value=''; el('f-desc').value='';
  el('f-floor').value=''; el('f-status').value='active';
  el('f-capacity').value=''; el('f-warn').value='40'; el('f-critical').value='60';
  el('modal-save-btn').textContent='Save Zone';
  openModal('modal-overlay','zone-modal');
}

// ── EDIT MODAL ─────────────────────────────────────────────
function openEditModal(id){
  const z=AppData.getZone(id); if(!z) return;
  el('modal-title').textContent='Edit Zone';
  el('modal-sub').textContent='Update zone details';
  el('edit-id').value=z.id;
  el('f-name').value=z.name;
  el('f-floor').value=z.floor;
  el('f-capacity').value=z.capacity;
  el('f-sensor').value=z.sensor||'';
  el('f-warn').value=z.warnThreshold;
  el('f-critical').value=z.critThreshold;
  el('f-desc').value=z.desc||'';
  el('f-status').value=z.status;
  el('modal-save-btn').textContent='Update Zone';
  openModal('modal-overlay','zone-modal');
}

function closeModal(){ closeModalById('modal-overlay','zone-modal'); }

// ── SAVE ZONE ──────────────────────────────────────────────
async function saveZone(){
  const name    = el('f-name').value.trim();
  const floor   = el('f-floor').value;
  const capacity= parseInt(el('f-capacity').value)||0;
  const sensor  = el('f-sensor').value.trim();
  const warn    = parseInt(el('f-warn').value)||40;
  const critical= parseInt(el('f-critical').value)||60;
  const desc    = el('f-desc').value.trim();
  const status  = el('f-status').value;
  const editId  = el('edit-id').value;

  if(!name)          { showToast('⚠️ Zone name is required.','error'); return; }
  if(!floor)         { showToast('⚠️ Please select a floor.','error'); return; }
  if(!capacity||capacity<1){ showToast('⚠️ Enter a valid capacity.','error'); return; }
  if(warn>=critical) { showToast('⚠️ Warning must be less than Critical.','error'); return; }

  const btn=el('modal-save-btn'); if(btn) btn.textContent='Saving...';

  try {
    if(editId){
      await AppData.updateZone({id:editId,name,floor,capacity,warnThreshold:warn,critThreshold:critical,sensor,desc,status});
      showToast('✅ Zone updated successfully!');
    } else {
      await AppData.addZone({name,floor,capacity,occupied:0,level:0,warnThreshold:warn,critThreshold:critical,sensor,battery:100,status,desc});
      showToast('✅ Zone added successfully!');
    }
    await AppData.loadZones();
    closeModal();
    renderStats();
    renderTable();
  } catch(e) {
    showToast('❌ Failed to save zone.','error');
  }
  if(btn) btn.textContent='Save Zone';
}

// ── DELETE MODAL ───────────────────────────────────────────
function openDeleteModal(id){
  const z=AppData.getZone(id); if(!z) return;
  deleteTargetId=id;
  setText('del-zone-name', z.name);
  openModal('del-overlay','del-modal');
}
function closeDeleteModal(){ closeModalById('del-overlay','del-modal'); }

async function confirmDelete(){
  if(!deleteTargetId) return;
  const z=AppData.getZone(deleteTargetId);
  try {
    await AppData.deleteZone(deleteTargetId);
    await AppData.loadZones();
    closeDeleteModal();
    renderStats();
    renderTable();
    showToast(`🗑️ Zone "${z?.name}" deleted.`,'info');
  } catch(e) {
    showToast('❌ Failed to delete zone.','error');
  }
  deleteTargetId='';
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
        <div style="height:6px;background:#f1f5f9;border-radius:4px;overflow:hidden;margin-top:6px;"><div style="width:${pct}%;height:100%;background:${col};border-radius:4px;transition:width .5s;"></div></div>
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
  `;
  const editBtn=el('view-edit-btn');
  if(editBtn){ editBtn.style.display=''; editBtn.onclick=()=>{ closeViewModal(); openEditModal(id); }; }
  openModal('view-overlay','view-modal');
}
function closeViewModal(){ closeModalById('view-overlay','view-modal'); }

// ── KEYBOARD ───────────────────────────────────────────────
document.addEventListener('keydown', e=>{
  if(e.key==='Escape'){ closeModal(); closeDeleteModal(); closeViewModal(); }
});

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