// ============================================================
//  LibraryQuiet – map-init.js
//  Leaflet map — NBSC Campus zone markers
//  Requires: leaflet.js + app-data.js loaded before this
// ============================================================

document.addEventListener('DOMContentLoaded', () => {
  // Wait for zones to load then init map
  function initMap() {
    if (!window.L) { console.error('Leaflet not loaded'); return; }
    const container = document.getElementById('zone-map');
    if (!container) return;

    // Fix container height explicitly
    container.style.height   = '420px';
    container.style.width    = '100%';
    container.style.position = 'relative';
    container.style.zIndex   = '1';

    const NBSC_LAT = 8.360235;
    const NBSC_LNG = 124.868406;
    const ZONE_COORDS = {
      'Z-001': { lat: 8.359236, lng: 124.867988 },
      'Z-002': { lat: 8.359294, lng: 124.867806 },
      'Z-003': { lat: 8.359347, lng: 124.867666 },
    };

    function mColor(db) { return db<40?'#10b981':db<60?'#f59e0b':'#ef4444'; }
    function mStatus(db){ return db<40?'Quiet':db<60?'Moderate':'Loud'; }

    function makeIcon(color, db) {
      return L.divIcon({
        className: '',
        html: `<div style="width:52px;height:52px;border-radius:50%;background:${color};border:3px solid #fff;box-shadow:0 0 14px ${color}99;display:flex;align-items:center;justify-content:center;color:#fff;font-weight:900;font-size:12px;font-family:'Plus Jakarta Sans',sans-serif;flex-direction:column;line-height:1.2;text-align:center;"><div>${Math.round(db)}</div><div style="font-size:9px;opacity:.85;">dB</div></div>`,
        iconSize: [52,52], iconAnchor: [26,26], popupAnchor: [0,-32],
      });
    }

    function makePopup(z) {
      const col  = mColor(z.level);
      const st   = mStatus(z.level);
      const sc   = z.level<40?{bg:'#d1fae5',tc:'#065f46'}:z.level<60?{bg:'#fef3c7',tc:'#92400e'}:{bg:'#fee2e2',tc:'#991b1b'};
      const pct  = Math.min(100,(z.level/90)*100).toFixed(1);
      const coords = ZONE_COORDS[z.id];
      return `<div style="font-family:'Plus Jakarta Sans',sans-serif;min-width:180px;padding:2px;">
        <div style="font-weight:800;font-size:14px;color:#0f172a;margin-bottom:6px;">📍 ${z.name}</div>
        <div style="display:flex;gap:6px;margin-bottom:8px;">
          <span style="font-size:10px;background:#f1f5f9;color:#64748b;padding:2px 8px;border-radius:6px;">Floor ${z.floor}</span>
          <span style="font-size:10px;font-weight:700;background:${sc.bg};color:${sc.tc};padding:2px 8px;border-radius:20px;">${st}</span>
          ${z.manualOverride?'<span style="font-size:10px;background:#fef9c3;color:#92400e;padding:2px 8px;border-radius:20px;">⚙ Manual</span>':''}
        </div>
        <div style="font-size:30px;font-weight:900;color:${col};line-height:1;">${Math.round(z.level)} <span style="font-size:13px;color:#64748b;font-weight:600;">dB</span></div>
        <div style="height:6px;background:#f1f5f9;border-radius:4px;overflow:hidden;margin:8px 0;">
          <div style="width:${pct}%;height:100%;background:${col};border-radius:4px;"></div>
        </div>
        <div style="font-size:11px;color:#94a3b8;">👥 ${z.occupied}/${z.capacity} occupied</div>
        <div style="font-size:11px;color:#94a3b8;">Sensor: ${z.sensor} · 🔋${z.battery||80}%</div>
        <div style="font-size:11px;color:#94a3b8;">Warn: ${z.warnThreshold} dB / Crit: ${z.critThreshold} dB</div>
        ${coords?`<div style="font-size:10px;color:#cbd5e1;margin-top:6px;font-family:'JetBrains Mono',monospace;">${coords.lat}, ${coords.lng}</div>`:''}
      </div>`;
    }

    // Destroy old map if exists
    if (window._lqMap) {
      window._lqMap.remove();
      window._lqMap = null;
    }

    // Init map
    const map = L.map('zone-map', {
      zoomControl: true,
      scrollWheelZoom: true,
    }).setView([NBSC_LAT, NBSC_LNG], 18);
    window._lqMap = map;

    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '© <a href="https://openstreetmap.org">OpenStreetMap</a> contributors',
      maxZoom: 20,
    }).addTo(map);

    // NBSC label marker
    L.marker([NBSC_LAT, NBSC_LNG], {
      icon: L.divIcon({
        className: '',
        html: `<div style="background:#1d4ed8;color:#fff;padding:5px 12px;border-radius:8px;font-size:11px;font-weight:800;font-family:'Plus Jakarta Sans',sans-serif;white-space:nowrap;box-shadow:0 2px 10px rgba(29,78,216,.45);border:2px solid #fff;">📡 NBSC Library</div>`,
        iconAnchor: [65, 14],
      })
    }).addTo(map);

    // Zone markers
    const markers = {};
    const zones   = AppData.getZones();

    if (zones.length === 0) {
      // Zones not loaded yet — place default markers
      Object.entries(ZONE_COORDS).forEach(([id, coords]) => {
        markers[id] = L.marker([coords.lat, coords.lng], {
          icon: makeIcon('#94a3b8', 0)
        }).addTo(map);
      });
    } else {
      zones.forEach(z => {
        const coords = ZONE_COORDS[z.id];
        if (!coords) return;
        markers[z.id] = L.marker([coords.lat, coords.lng], {
          icon: makeIcon(mColor(z.level), z.level)
        }).addTo(map).bindPopup(makePopup(z), { maxWidth: 240 });
      });
    }

    // Force map to recalculate size (fixes grey tiles issue)
    setTimeout(() => { map.invalidateSize(); }, 300);

    // Live update markers every 2s
    setInterval(() => {
      AppData.getZones().forEach(z => {
        const m = markers[z.id]; if (!m) return;
        m.setIcon(makeIcon(mColor(z.level), z.level));
        m.setPopupContent(makePopup(z));
      });
    }, 2000);
  }

  // Wait 800ms for zones to load from API first
  setTimeout(initMap, 800);
});