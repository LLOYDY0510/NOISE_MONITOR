<?php
require_once __DIR__ . '/includes/config.php';
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
if($_SERVER['REQUEST_METHOD']==='OPTIONS'){http_response_code(200);exit;}

$action=$_GET['action']??$_POST['action']??'';
$b=getBody();

switch($action){

  case 'login':
    $email=strtolower(trim($b['email']??''));$pass=$b['password']??'';
    if(!$email||!$pass) jsonResponse(['error'=>'Email and password required.'],400);
    $db=getDB();$stmt=$db->prepare("SELECT * FROM users WHERE LOWER(email)=? AND password=? AND status='active' LIMIT 1");
    $stmt->bind_param('ss',$email,$pass);$stmt->execute();$u=$stmt->get_result()->fetch_assoc();
    if(!$u) jsonResponse(['error'=>'Invalid email or password.'],401);
    $now=date('F j, Y').' '.date('h:i A');
    $upd=$db->prepare("UPDATE users SET last_login=? WHERE id=?");$upd->bind_param('ss',$now,$u['id']);$upd->execute();
    $_SESSION['user_id']=$u['id'];$_SESSION['user_name']=$u['name'];$_SESSION['user_email']=$u['email'];$_SESSION['user_role']=$u['role'];
    $redirect=$u['role']==='Administrator'?'/NOISE_MONITOR/dashboards/admin/dashboard-admin.php':($u['role']==='Library Manager'?'/NOISE_MONITOR/dashboards/manager/dashboard-manager.php':'/NOISE_MONITOR/dashboards/staff/staff.php');
    jsonResponse(['success'=>true,'user'=>['id'=>$u['id'],'name'=>$u['name'],'email'=>$u['email'],'role'=>$u['role']],'redirect'=>$redirect]);

  case 'register':
    $name=trim($b['name']??'');$email=strtolower(trim($b['email']??''));$pass=$b['password']??'';$role=$b['role']??'';
    if(!$name||!$email||!$pass||!$role) jsonResponse(['error'=>'All fields required.'],400);
    if(strlen($pass)<6) jsonResponse(['error'=>'Password must be at least 6 characters.'],400);
    if(!in_array($role,['Library Staff','Library Manager'])) jsonResponse(['error'=>'Invalid role.'],400);
    $db=getDB();$chk=$db->prepare("SELECT id FROM users WHERE LOWER(email)=? LIMIT 1");
    $chk->bind_param('s',$email);$chk->execute();
    if($chk->get_result()->num_rows>0) jsonResponse(['error'=>'Email already exists.'],409);
    $id='U-'.time();$stmt=$db->prepare("INSERT INTO users (id,name,email,password,role,status) VALUES (?,?,?,?,?,'active')");
    $stmt->bind_param('sssss',$id,$name,$email,$pass,$role);$stmt->execute();
    jsonResponse(['success'=>true,'message'=>"Account created for $name!"]);

  case 'logout':
    session_destroy();jsonResponse(['success'=>true]);

  case 'session':
    if(empty($_SESSION['user_id'])) jsonResponse(['user'=>null]);
    jsonResponse(['user'=>['id'=>$_SESSION['user_id'],'name'=>$_SESSION['user_name'],'email'=>$_SESSION['user_email'],'role'=>$_SESSION['user_role']]]);

  // ── ZONES ───────────────────────────────────────────────────
  case 'get_zones':
    $db=getDB();$res=$db->query("SELECT * FROM zones ORDER BY id");$zones=[];
    while($r=$res->fetch_assoc()) $zones[]=['id'=>$r['id'],'name'=>$r['name'],'floor'=>$r['floor'],
      'capacity'=>(int)$r['capacity'],'occupied'=>(int)$r['occupied'],'level'=>(float)$r['level'],
      'warnThreshold'=>(int)$r['warn_threshold'],'critThreshold'=>(int)$r['crit_threshold'],
      'sensor'=>$r['sensor'],'status'=>$r['status'],'battery'=>(int)$r['battery'],
      'manualOverride'=>(bool)$r['manual_override'],'desc'=>$r['description']??''];
    jsonResponse($zones);

  case 'set_sensor':
    // Manager manually sets a dB level for a zone
    $id=$b['id']??'';$lvl=(float)($b['level']??0);$by=$_SESSION['user_name']??'Manager';
    if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$now=date('h:i A');$dt=date('F j, Y');
    // Update zone level
    $s=$db->prepare("UPDATE zones SET level=?,manual_override=1 WHERE id=?");
    $s->bind_param('ds',$lvl,$id);$s->execute();
    // Save override record
    $ins=$db->prepare("REPLACE INTO sensor_overrides (zone_id,level,set_by,set_at,set_date) VALUES (?,?,?,?,?)");
    $ins->bind_param('sdsss',$id,$lvl,$by,$now,$dt);$ins->execute();
    // AUTO CREATE ALERT if above threshold
    createAlertIfNeeded($db,$id,$lvl);
    jsonResponse(['success'=>true]);

  case 'clear_sensor':
    $id=$b['id']??'';if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();
    $s=$db->prepare("UPDATE zones SET manual_override=0,level=0 WHERE id=?");$s->bind_param('s',$id);$s->execute();
    $d=$db->prepare("DELETE FROM sensor_overrides WHERE zone_id=?");$d->bind_param('s',$id);$d->execute();
    jsonResponse(['success'=>true]);

  case 'get_sensor_overrides':
    $db=getDB();$res=$db->query("SELECT * FROM sensor_overrides");$out=[];
    while($r=$res->fetch_assoc()) $out[$r['zone_id']]=['level'=>(float)$r['level'],'setAt'=>$r['set_at'],'setDate'=>$r['set_date'],'setBy'=>$r['set_by']];
    jsonResponse($out);

  case 'update_zone_level':
    $id=$b['id']??'';$lvl=(float)($b['level']??0);$occ=(int)($b['occupied']??0);
    if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();
    $s=$db->prepare("UPDATE zones SET level=?,occupied=? WHERE id=?");$s->bind_param('dis',$lvl,$occ,$id);$s->execute();
    createAlertIfNeeded($db,$id,$lvl);
    jsonResponse(['success'=>true]);

  case 'add_zone':
    $db=getDB();$id='Z-'.time();
    $n=$b['name']??'';$f=$b['floor']??'1F';$cap=(int)($b['capacity']??50);
    $occ=(int)($b['occupied']??0);$lvl=(float)($b['level']??0);
    $wt=(int)($b['warnThreshold']??40);$ct=(int)($b['critThreshold']??60);
    $sn=$b['sensor']??'SNS-000';$bt=(int)($b['battery']??100);$ds=$b['desc']??'';
    $s=$db->prepare("INSERT INTO zones (id,name,floor,capacity,occupied,level,warn_threshold,crit_threshold,sensor,battery,description) VALUES (?,?,?,?,?,?,?,?,?,?,?)");
    $s->bind_param('sssiidiiiss',$id,$n,$f,$cap,$occ,$lvl,$wt,$ct,$sn,$bt,$ds);$s->execute();
    jsonResponse(['success'=>true,'id'=>$id]);

  case 'update_zone':
    $id=$b['id']??'';if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$n=$b['name']??'';$f=$b['floor']??'1F';$cap=(int)($b['capacity']??50);
    $wt=(int)($b['warnThreshold']??40);$ct=(int)($b['critThreshold']??60);
    $sn=$b['sensor']??'';$ds=$b['desc']??'';$st=$b['status']??'active';
    $s=$db->prepare("UPDATE zones SET name=?,floor=?,capacity=?,warn_threshold=?,crit_threshold=?,sensor=?,description=?,status=? WHERE id=?");
    $s->bind_param('ssiiiisss',$n,$f,$cap,$wt,$ct,$sn,$ds,$st,$id);$s->execute();
    jsonResponse(['success'=>true]);

  case 'delete_zone':
    $id=$b['id']??'';if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$s=$db->prepare("DELETE FROM zones WHERE id=?");$s->bind_param('s',$id);$s->execute();
    jsonResponse(['success'=>true]);

  // ── ALERTS ──────────────────────────────────────────────────
  case 'get_alerts':
    $db=getDB();$res=$db->query("SELECT * FROM alerts ORDER BY created_at DESC");$alerts=[];
    while($r=$res->fetch_assoc()){
      $msgs=[];$mr=$db->query("SELECT * FROM alert_messages WHERE alert_id='{$r['id']}' ORDER BY created_at ASC");
      while($m=$mr->fetch_assoc()) $msgs[]=['id'=>$m['id'],'from'=>$m['from_name'],'role'=>$m['from_role'],'text'=>$m['message'],'time'=>$m['msg_time'],'date'=>$m['msg_date'],'system'=>(bool)$m['is_system']];
      $alerts[]=['id'=>$r['id'],'zone'=>$r['zone_name'],'level'=>(float)$r['level'],'type'=>$r['type'],
        'msg'=>$r['msg'],'status'=>$r['status'],'resolvedBy'=>$r['resolved_by'],'resolvedAt'=>$r['resolved_at'],
        'sentToAdmin'=>(bool)$r['sent_to_admin'],'date'=>$r['alert_date'],'time'=>$r['alert_time'],'messages'=>$msgs];
    }
    jsonResponse($alerts);

  case 'add_alert':
    $db=getDB();$id='A-'.time().'-'.rand(100,999);
    $zone=$b['zone']??'';$lvl=(float)($b['level']??0);
    $type=$b['type']??'warning';$msg=$b['msg']??'';
    $date=$b['date']??date('F j, Y');$time=$b['time']??date('h:i A');
    $s=$db->prepare("INSERT INTO alerts (id,zone_name,level,type,msg,status,alert_date,alert_time) VALUES (?,?,?,?,?,'active',?,?)");
    $s->bind_param('sdssss',$id,$zone,$lvl,$type,$msg,$date,$time);$s->execute();
    jsonResponse(['success'=>true,'id'=>$id]);

  case 'resolve_alert':
    $id=$b['id']??'';$by=$b['resolvedBy']??($_SESSION['user_name']??'Manager');
    if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$now=date('h:i A');
    $s=$db->prepare("UPDATE alerts SET status='resolved',resolved_by=?,resolved_at=? WHERE id=?");
    $s->bind_param('sss',$by,$now,$id);$s->execute();
    jsonResponse(['success'=>true]);

  case 'add_alert_message':
    $aid=$b['alertId']??'';$msg=$b['message']??[];if(!$aid||!$msg) jsonResponse(['error'=>'Missing data'],400);
    $db=getDB();$mid='M-'.time().'-'.rand(100,999);
    $from=$msg['from']??'';$role=$msg['role']??'';$text=$msg['text']??'';
    $time=$msg['time']??date('h:i A');$date=$msg['date']??date('F j, Y');$sys=(int)($msg['system']??0);
    $s=$db->prepare("INSERT INTO alert_messages (id,alert_id,from_name,from_role,message,msg_time,msg_date,is_system) VALUES (?,?,?,?,?,?,?,?)");
    $s->bind_param('sssssssi',$mid,$aid,$from,$role,$text,$time,$date,$sys);$s->execute();
    jsonResponse(['success'=>true]);

  case 'delete_alert':
    $id=$b['id']??'';if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$s=$db->prepare("DELETE FROM alerts WHERE id=?");$s->bind_param('s',$id);$s->execute();
    jsonResponse(['success'=>true]);

  // ── REPORTS ─────────────────────────────────────────────────
  case 'get_reports':
    $db=getDB();$res=$db->query("SELECT * FROM reports ORDER BY created_at DESC");$reports=[];
    while($r=$res->fetch_assoc()) $reports[]=['id'=>$r['id'],'type'=>$r['type'],'generatedBy'=>$r['generated_by'],
      'role'=>$r['role'],'date'=>$r['report_date'],'time'=>$r['report_time'],
      'sentToAdmin'=>(bool)$r['sent_to_admin'],'sentAt'=>$r['sent_at'],'adminReadAt'=>$r['admin_read_at'],'notes'=>$r['notes']];
    jsonResponse($reports);

  case 'add_report':
    $db=getDB();$id='R-'.time();
    $type=$b['type']??'';$by=$b['generatedBy']??'';$role=$b['role']??'';
    $date=$b['date']??date('F j, Y');$time=$b['time']??date('h:i A');$notes=$b['notes']??'';
    $s=$db->prepare("INSERT INTO reports (id,type,generated_by,role,report_date,report_time,notes) VALUES (?,?,?,?,?,?,?)");
    $s->bind_param('sssssss',$id,$type,$by,$role,$date,$time,$notes);$s->execute();
    jsonResponse(['success'=>true,'id'=>$id]);

  case 'send_report':
    $id=$b['id']??'';if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$now=date('h:i A');
    $s=$db->prepare("UPDATE reports SET sent_to_admin=1,sent_at=? WHERE id=?");$s->bind_param('ss',$now,$id);$s->execute();
    jsonResponse(['success'=>true]);

  case 'mark_report_read':
    $id=$b['id']??'';if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$now=date('h:i A');
    $s=$db->prepare("UPDATE reports SET admin_read_at=? WHERE id=?");$s->bind_param('ss',$now,$id);$s->execute();
    jsonResponse(['success'=>true]);

  case 'delete_report':
    $id=$b['id']??'';if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$s=$db->prepare("DELETE FROM reports WHERE id=?");$s->bind_param('s',$id);$s->execute();
    jsonResponse(['success'=>true]);

  // ── USERS ───────────────────────────────────────────────────
  case 'get_users':
    $db=getDB();$res=$db->query("SELECT id,name,email,role,status,last_login,created_at FROM users ORDER BY id");$users=[];
    while($r=$res->fetch_assoc()) $users[]=['id'=>$r['id'],'name'=>$r['name'],'email'=>$r['email'],'role'=>$r['role'],'status'=>$r['status'],'lastLogin'=>$r['last_login'],'createdAt'=>$r['created_at']];
    jsonResponse($users);

  case 'add_user':
    $db=getDB();$id='U-'.time();
    $n=$b['name']??'';$e=strtolower($b['email']??'');$p=$b['password']??'';$r=$b['role']??'Library Staff';
    $chk=$db->prepare("SELECT id FROM users WHERE LOWER(email)=? LIMIT 1");$chk->bind_param('s',$e);$chk->execute();
    if($chk->get_result()->num_rows>0) jsonResponse(['error'=>'Email already exists.'],409);
    $s=$db->prepare("INSERT INTO users (id,name,email,password,role) VALUES (?,?,?,?,?)");
    $s->bind_param('sssss',$id,$n,$e,$p,$r);$s->execute();
    jsonResponse(['success'=>true,'id'=>$id]);

  case 'update_user':
    $id=$b['id']??'';if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$n=$b['name']??'';$r=$b['role']??'';$st=$b['status']??'active';
    $s=$db->prepare("UPDATE users SET name=?,role=?,status=? WHERE id=?");$s->bind_param('ssss',$n,$r,$st,$id);$s->execute();
    jsonResponse(['success'=>true]);

  case 'update_password':
    $id=$b['id']??'';$p=$b['password']??'';if(!$id||!$p) jsonResponse(['error'=>'ID and password required.'],400);
    $db=getDB();$s=$db->prepare("UPDATE users SET password=? WHERE id=?");$s->bind_param('ss',$p,$id);$s->execute();
    jsonResponse(['success'=>true]);

  case 'delete_user':
    $id=$b['id']??'';if(!$id) jsonResponse(['error'=>'ID required'],400);
    $db=getDB();$s=$db->prepare("DELETE FROM users WHERE id=?");$s->bind_param('s',$id);$s->execute();
    jsonResponse(['success'=>true]);

  // ── CHECK ALL ZONES → AUTO CREATE ALERTS ───────────────────
  case 'check_alerts':
    $db=getDB();
    $res=$db->query("SELECT * FROM zones WHERE status='active'");
    $created=0;
    while($zone=$res->fetch_assoc()){
      $level=(float)$zone['level'];
      if($level<$zone['warn_threshold']) continue;
      $type=$level>=$zone['crit_threshold']?'critical':'warning';
      $zn=$db->real_escape_string($zone['name']);
      // Only create if no active alert exists for this zone
      $ex=$db->query("SELECT id FROM alerts WHERE zone_name='$zn' AND status='active' LIMIT 1");
      if($ex->num_rows>0) continue;
      $aid='A-'.time().'-'.rand(100,999);
      $now=date('h:i A');$date=date('F j, Y');
      $msg=$type==='critical'?'Critical noise level detected':'Noise threshold exceeded';
      $s=$db->prepare("INSERT INTO alerts (id,zone_name,level,type,msg,status,alert_date,alert_time) VALUES (?,?,?,?,?,'active',?,?)");
      $s->bind_param('sdssss',$aid,$zone['name'],$level,$type,$msg,$date,$now);
      $s->execute();
      $created++;
      usleep(2000);
    }
    jsonResponse(['success'=>true,'created'=>$created]);

  default:
    jsonResponse(['error'=>'Unknown action: '.$action],404);
}

// ============================================================
//  createAlertIfNeeded — called after any sensor level change
//  Automatically creates alert if zone exceeds threshold
// ============================================================
function createAlertIfNeeded($db, $zoneId, $level) {
  $eid  = $db->real_escape_string($zoneId);
  $zone = $db->query("SELECT * FROM zones WHERE id='$eid'")->fetch_assoc();
  if (!$zone) return;

  $warn = (int)$zone['warn_threshold'];
  $crit = (int)$zone['crit_threshold'];

  // Below warning — no alert needed
  if ($level < $warn) return;

  $type = $level >= $crit ? 'critical' : 'warning';
  $zn   = $db->real_escape_string($zone['name']);

  // Check if active alert already exists for this zone+type
  $ex = $db->query("SELECT id FROM alerts WHERE zone_name='$zn' AND status='active' AND type='$type' LIMIT 1");
  if ($ex->num_rows > 0) return; // already alerted

  // Create new alert
  $id   = 'A-' . time() . '-' . rand(100, 999);
  $now  = date('h:i A');
  $date = date('F j, Y');
  $msg  = $type === 'critical' ? 'Critical noise level detected' : 'Noise threshold exceeded';

  $s = $db->prepare("INSERT INTO alerts (id,zone_name,level,type,msg,status,alert_date,alert_time) VALUES (?,?,?,?,?,'active',?,?)");
  $s->bind_param('sdssss', $id, $zone['name'], $level, $type, $msg, $date, $now);
  $s->execute();
}