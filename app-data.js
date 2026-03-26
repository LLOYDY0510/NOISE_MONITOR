// ============================================================
//  LibraryQuiet – app-data.js
// ============================================================
const AppData = (() => {
  const API = '/NOISE_MONITOR/api.php';
  let _session = window.__LQ_SESSION__ || null;

  async function post(action,data={}){
    try{const res=await fetch(`${API}?action=${action}`,{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify(data)});return await res.json();}
    catch(e){console.error(`API[${action}]:`,e);return{error:e.message};}
  }
  async function get(action){
    try{const res=await fetch(`${API}?action=${action}`);return await res.json();}
    catch(e){console.error(`API[${action}]:`,e);return null;}
  }

  // SESSION
  async function loadSession(){const d=await get('session');_session=d?.user||null;return _session;}
  function getSession(){return _session;}
  function isAdmin(){return _session?.role==='Administrator';}
  function isManager(){return _session?.role==='Library Manager';}
  function isStaff(){return _session?.role==='Library Staff';}
  async function clearSession(){await post('logout');_session=null;window.location.href='/NOISE_MONITOR/logout.php';}

  function applySession(){
    if(!_session)return;
    const name=_session.name||_session.email;
    const initial=name.charAt(0).toUpperCase();
    const isAdm=isAdmin(),isMgr=isManager();
    const roleIcon=isAdm?'👑 ':isMgr?'📋 ':'👤 ';
    const roleColor=isAdm?'linear-gradient(135deg,#1d4ed8,#3b82f6)':isMgr?'linear-gradient(135deg,#0d9488,#0f766e)':'linear-gradient(135deg,#7c3aed,#8b5cf6)';
    document.querySelectorAll('.sb-uname,.sb-bname').forEach(el=>el.textContent=name);
    document.querySelectorAll('.sb-urole').forEach(el=>el.textContent=roleIcon+_session.role);
    document.querySelectorAll('.sb-brole').forEach(el=>el.textContent=_session.role);
    document.querySelectorAll('.sb-av,.sb-av-lg,.tb-av,.top-av').forEach(el=>{el.textContent=initial;el.style.background=roleColor;});
    const badge=document.getElementById('role-badge');
    if(badge){badge.textContent=roleIcon+_session.role;badge.className='role-badge '+(isAdm?'admin-badge':isMgr?'manager-badge':'staff-badge');}
    updateNotifBadge();
  }

  // ZONES
  let _zones=[];
  async function loadZones(){
    _zones=await get('get_zones')||[];
    // Auto-check if any zones now exceed thresholds → create alerts
    await post('check_alerts');
    return _zones;
  }
  function getZones(){return _zones;}
  function getZone(id){return _zones.find(z=>z.id===id);}
  async function setSensorLevel(zoneId,db){await post('set_sensor',{id:zoneId,level:db});const z=_zones.find(z=>z.id===zoneId);if(z){z.level=db;z.manualOverride=true;}}
  async function clearSensorOverride(zoneId){await post('clear_sensor',{id:zoneId});const z=_zones.find(z=>z.id===zoneId);if(z)z.manualOverride=false;}
  let _overrides={};
  async function loadSensorOverrides(){_overrides=await get('get_sensor_overrides')||{};return _overrides;}
  function getSensorOverrides(){return _overrides;}
  async function addZone(z){return await post('add_zone',z);}
  async function updateZone(z){return await post('update_zone',z);}
  async function deleteZone(id){return await post('delete_zone',{id});}

  // ALERTS
  let _alerts=[];
  async function loadAlerts(){_alerts=await get('get_alerts')||[];return _alerts;}
  function getAlerts(){return _alerts;}
  function getActiveAlerts(){return _alerts.filter(a=>a.status==='active');}
  async function resolveAlert(id,by){await post('resolve_alert',{id,resolvedBy:by});const a=_alerts.find(a=>a.id===id);if(a){a.status='resolved';a.resolvedBy=by;}updateNotifBadge();}
  async function addAlertMessage(alertId,message){await post('add_alert_message',{alertId,message});}
  function getAlertMessages(alertId){return _alerts.find(a=>a.id===alertId)?.messages||[];}
  async function deleteAlert(id){await post('delete_alert',{id});_alerts=_alerts.filter(a=>a.id!==id);}
  async function addAlert(a){return await post('add_alert',a);}

  // REPORTS
  let _reports=[];
  async function loadReports(){_reports=await get('get_reports')||[];return _reports;}
  function getReports(){return _reports;}
  function getUnreadReports(){return _reports.filter(r=>r.sentToAdmin&&!r.adminReadAt);}
  async function addReport(r){return await post('add_report',r);}
  async function sendReportToAdmin(id){return await post('send_report',{id});}
  async function markReportRead(id){return await post('mark_report_read',{id});}
  async function deleteReport(id){await post('delete_report',{id});_reports=_reports.filter(r=>r.id!==id);}

  // USERS
  let _users=[];
  async function loadUsers(){_users=await get('get_users')||[];return _users;}
  function getUsers(){return _users;}
  function getUserByEmail(email){return _users.find(u=>u.email.toLowerCase()===email.toLowerCase());}
  async function addUser(u){return await post('add_user',u);}
  async function updateUser(id,data){return await post('update_user',{id,...data});}
  async function deleteUser(id){return await post('delete_user',{id});}
  async function updatePassword(id,pw){return await post('update_password',{id,password:pw});}

  // BADGE
  function updateNotifBadge(){
    const active=getActiveAlerts().length;
    ['alert-nb','bell-count'].forEach(id=>{const el=document.getElementById(id);if(el){el.textContent=active;el.style.display=active>0?'':'none';}});
  }

  async function loadAll(){await Promise.all([loadZones(),loadAlerts(),loadSensorOverrides(),loadUsers(),loadReports()]);}

  return {
    loadSession,getSession,isAdmin,isManager,isStaff,clearSession,applySession,
    loadZones,getZones,getZone,setSensorLevel,clearSensorOverride,loadSensorOverrides,getSensorOverrides,addZone,updateZone,deleteZone,
    loadAlerts,getAlerts,getActiveAlerts,resolveAlert,addAlertMessage,getAlertMessages,deleteAlert,addAlert,
    loadReports,getReports,getUnreadReports,addReport,sendReportToAdmin,markReportRead,deleteReport,
    loadUsers,getUsers,getUserByEmail,addUser,updateUser,deleteUser,updatePassword,
    updateNotifBadge,loadAll,
    get _session(){return _session;}, set _session(v){_session=v;}
  };
})();