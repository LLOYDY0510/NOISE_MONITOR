<?php
require_once __DIR__ . '/includes/config.php';

// Already logged in → redirect to correct dashboard
if (isLoggedIn()) {
    $role = $_SESSION['user_role'];
    if ($role === 'Administrator') {
        header('Location: /NOISE_MONITOR/dashboards/admin/dashboard-admin.php'); exit;
    } elseif ($role === 'Library Manager') {
        header('Location: /NOISE_MONITOR/dashboards/manager/dashboard-manager.php'); exit;
    } else {
        header('Location: /NOISE_MONITOR/dashboards/staff.php'); exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LibraryQuiet – Sign In</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/NOISE_MONITOR/assets/login.css"/>
  <style>
    /* ── INLINE FALLBACK STYLES ─────────────────────────── */
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    body{font-family:'Plus Jakarta Sans',sans-serif;min-height:100vh;background:linear-gradient(135deg,#0f172a 0%,#1e293b 50%,#0f172a 100%);display:flex;align-items:center;justify-content:center;overflow:hidden;position:relative;}
    .bg-grid{position:fixed;inset:0;background-image:linear-gradient(rgba(99,102,241,.04) 1px,transparent 1px),linear-gradient(90deg,rgba(99,102,241,.04) 1px,transparent 1px);background-size:40px 40px;pointer-events:none;}
    .bg-glow1{position:fixed;top:-20%;left:-10%;width:600px;height:600px;background:radial-gradient(circle,rgba(99,102,241,.15) 0%,transparent 70%);pointer-events:none;}
    .bg-glow2{position:fixed;bottom:-20%;right:-10%;width:500px;height:500px;background:radial-gradient(circle,rgba(13,148,136,.12) 0%,transparent 70%);pointer-events:none;}
    .bg-glow3{position:fixed;top:50%;left:50%;transform:translate(-50%,-50%);width:800px;height:800px;background:radial-gradient(circle,rgba(139,92,246,.06) 0%,transparent 70%);pointer-events:none;}
    .particle{position:fixed;border-radius:50%;background:rgba(99,102,241,.4);animation:float linear infinite;pointer-events:none;}
    @keyframes float{0%{transform:translateY(100vh) rotate(0);opacity:0;}10%{opacity:1;}90%{opacity:1;}100%{transform:translateY(-100px) rotate(720deg);opacity:0;}}
    .success-overlay{position:fixed;inset:0;background:rgba(15,23,42,.9);backdrop-filter:blur(10px);z-index:1000;display:flex;align-items:center;justify-content:center;}
    .success-card{background:#fff;border-radius:24px;padding:48px 40px;text-align:center;max-width:360px;width:90%;box-shadow:0 24px 60px rgba(0,0,0,.3);}
    .success-icon{font-size:48px;margin-bottom:16px;}
    .success-name{font-size:22px;font-weight:800;color:#0f172a;margin-bottom:6px;}
    .success-role{font-size:14px;color:#64748b;margin-bottom:8px;}
    .success-sub{font-size:13px;color:#94a3b8;margin-bottom:20px;}
    .success-bar{height:5px;background:#f1f5f9;border-radius:5px;overflow:hidden;}
    .success-bar-fill{height:100%;width:0;background:linear-gradient(90deg,#6366f1,#8b5cf6);border-radius:5px;transition:width .03s linear;}
    .login-card{background:#fff;border-radius:24px;padding:36px 32px;width:100%;max-width:480px;max-height:95vh;overflow-y:auto;box-shadow:0 24px 80px rgba(0,0,0,.4);position:relative;z-index:10;}
    .login-card::-webkit-scrollbar{width:4px;}.login-card::-webkit-scrollbar-thumb{background:#e2e8f0;border-radius:4px;}
    .logo-wrap{display:flex;align-items:center;gap:14px;margin-bottom:20px;}
    .logo-icon{width:48px;height:48px;border-radius:14px;background:linear-gradient(135deg,#6366f1,#8b5cf6);display:flex;align-items:center;justify-content:center;font-size:22px;box-shadow:0 4px 16px rgba(99,102,241,.4);}
    .logo-name{font-size:20px;font-weight:800;color:#0f172a;}
    .logo-sub{font-size:11px;color:#94a3b8;}
    .sys-badge{display:inline-flex;align-items:center;gap:6px;background:#f0fdf4;border:1px solid #bbf7d0;padding:5px 14px;border-radius:20px;margin-bottom:20px;}
    .sys-dot{width:7px;height:7px;border-radius:50%;background:#10b981;animation:pulse 1.5s infinite;}
    .sys-text{font-size:11px;font-weight:700;color:#065f46;}
    @keyframes pulse{0%,100%{opacity:1;}50%{opacity:.4;}}
    .auth-tabs{display:flex;gap:4px;background:#f8fafc;border-radius:12px;padding:4px;margin-bottom:24px;}
    .auth-tab{flex:1;padding:9px;border:none;border-radius:9px;font-family:inherit;font-size:13px;font-weight:600;cursor:pointer;background:transparent;color:#64748b;transition:all .2s;}
    .auth-tab.active{background:#fff;color:#0f172a;box-shadow:0 1px 6px rgba(0,0,0,.1);}
    .panel-title{font-size:20px;font-weight:800;color:#0f172a;margin-bottom:4px;}
    .panel-sub{font-size:13px;color:#94a3b8;margin-bottom:20px;}
    .form-group{margin-bottom:16px;}
    .form-label{font-size:12px;font-weight:700;color:#475569;display:block;margin-bottom:6px;}
    .input-wrap{position:relative;display:flex;align-items:center;}
    .input-icon{position:absolute;left:14px;font-size:15px;z-index:1;}
    .form-input{width:100%;padding:12px 14px 12px 42px;border:1.5px solid #e2e8f0;border-radius:10px;font-size:14px;font-family:inherit;outline:none;transition:border-color .2s,box-shadow .2s;background:#fff;color:#0f172a;}
    .form-input:focus{border-color:#6366f1;box-shadow:0 0 0 3px rgba(99,102,241,.1);}
    .form-select{padding-left:42px;appearance:none;cursor:pointer;}
    .toggle-pw{position:absolute;right:12px;background:none;border:none;cursor:pointer;font-size:15px;color:#94a3b8;padding:4px;}
    .form-extras{display:flex;justify-content:space-between;align-items:center;margin-bottom:16px;}
    .remember-wrap{display:flex;align-items:center;gap:8px;cursor:pointer;}
    .remember-check{width:16px;height:16px;border-radius:4px;border:2px solid #e2e8f0;transition:all .2s;}
    .remember-check.checked{background:#6366f1;border-color:#6366f1;}
    .remember-label{font-size:12px;color:#64748b;}
    .forgot-link{background:none;border:none;font-family:inherit;font-size:12px;color:#6366f1;cursor:pointer;font-weight:600;}
    .login-btn{width:100%;padding:14px;background:linear-gradient(135deg,#6366f1,#8b5cf6);color:#fff;border:none;border-radius:12px;font-family:inherit;font-size:15px;font-weight:700;cursor:pointer;transition:all .2s;margin-bottom:20px;}
    .login-btn:hover{transform:translateY(-1px);box-shadow:0 6px 20px rgba(99,102,241,.4);}
    .register-btn{background:linear-gradient(135deg,#0d9488,#0f766e);}
    .register-btn:hover{box-shadow:0 6px 20px rgba(13,148,136,.4);}
    .demo-section{margin-bottom:20px;}
    .demo-label{font-size:11px;color:#94a3b8;font-weight:600;text-transform:uppercase;letter-spacing:.5px;margin-bottom:10px;}
    .demo-chips{display:flex;gap:8px;}
    .demo-chip{flex:1;background:#f8fafc;border:1.5px solid #e2e8f0;border-radius:12px;padding:10px 8px;text-align:center;cursor:pointer;transition:all .2s;}
    .demo-chip:hover{border-color:#6366f1;background:#f5f3ff;transform:translateY(-1px);}
    .chip-icon{font-size:18px;margin-bottom:3px;}
    .chip-role{font-size:11px;font-weight:700;margin-bottom:2px;}
    .chip-email{font-size:10px;color:#94a3b8;}
    .chip-pass{font-size:10px;color:#cbd5e1;font-family:'JetBrains Mono',monospace;}
    .switch-link{text-align:center;font-size:13px;color:#64748b;}
    .switch-link button{background:none;border:none;font-family:inherit;font-size:13px;color:#6366f1;font-weight:600;cursor:pointer;}
    .error-msg{display:flex;align-items:center;gap:8px;background:#fef2f2;border:1px solid #fecaca;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#991b1b;}
    .success-msg{display:flex;align-items:center;gap:8px;background:#f0fdf4;border:1px solid #bbf7d0;border-radius:10px;padding:10px 14px;margin-bottom:16px;font-size:13px;color:#065f46;}
    .role-note{font-size:11px;color:#f59e0b;margin-top:6px;font-weight:600;}
    .pw-strength{display:flex;align-items:center;gap:8px;margin-top:6px;}
    .pw-strength-bar{flex:1;height:4px;background:#f1f5f9;border-radius:4px;overflow:hidden;}
    .pw-strength-fill{height:100%;border-radius:4px;transition:all .3s;}
    .pw-strength-label{font-size:11px;font-weight:700;min-width:70px;}
    #particles{position:fixed;inset:0;pointer-events:none;z-index:1;}
  </style>
</head>
<body>
  <div class="bg-grid"></div>
  <div class="bg-glow1"></div>
  <div class="bg-glow2"></div>
  <div class="bg-glow3"></div>
  <div id="particles"></div>

  <!-- SUCCESS OVERLAY -->
  <div class="success-overlay" id="success-overlay" style="display:none;">
    <div class="success-card">
      <div class="success-icon">✅</div>
      <div class="success-name" id="success-name">Welcome!</div>
      <div class="success-role" id="success-role">Administrator</div>
      <div class="success-sub">Redirecting to your dashboard...</div>
      <div class="success-bar"><div class="success-bar-fill" id="progress-bar"></div></div>
    </div>
  </div>

  <!-- LOGIN CARD -->
  <div class="login-card" id="login-card">

    <div class="logo-wrap">
      <div class="logo-icon">📡</div>
      <div><div class="logo-name">LibraryQuiet</div><div class="logo-sub">Noise Monitoring System</div></div>
    </div>

    <div class="sys-badge"><span class="sys-dot"></span><span class="sys-text">System Online</span></div>

    <div class="auth-tabs">
      <button class="auth-tab active" id="tab-signin" onclick="showTab('signin')">Sign In</button>
      <button class="auth-tab" id="tab-register" onclick="showTab('register')">Create Account</button>
    </div>

    <!-- ══ SIGN IN ══ -->
    <div id="panel-signin">
      <div class="panel-title">Welcome</div>
    
      <div class="error-msg" id="error-msg" style="display:none;"><span>⚠️</span><span id="error-text">Invalid email or password.</span></div>
      <div class="form-group">
        <label class="form-label">Email Address</label>
        <div class="input-wrap"><span class="input-icon">✉️</span>
          <input class="form-input" id="email" type="email" placeholder="Enter your email address" autocomplete="email"/>
        </div>
      </div>
      <div class="form-group">
        <label class="form-label">Password</label>
        <div class="input-wrap"><span class="input-icon">🔒</span>
          <input class="form-input" id="password" type="password" placeholder="Enter your password"/>
          <button class="toggle-pw" onclick="togglePw('password','toggle-pw')" id="toggle-pw" type="button">👁</button>
        </div>
      </div>
      <div class="form-extras">
        <label class="remember-wrap">
          <input type="checkbox" id="remember" style="display:none;"/>
          <div class="remember-check" id="remember-check" onclick="document.getElementById('remember').click()"></div>
          <span class="remember-label">Remember me</span>
        </label>
        <button class="forgot-link" onclick="forgotPassword()" type="button">Forgot password?</button>
      </div>
      <button class="login-btn" id="login-btn" onclick="doLogin()" type="button"><span id="btn-text">Sign In →</span></button>
      </div>
    

    <!-- ══ CREATE ACCOUNT ══ -->
    <div id="panel-register" style="display:none;">
      <div class="panel-title">Create Account</div>
     
      <div class="error-msg" id="reg-error" style="display:none;"><span>⚠️</span><span id="reg-error-text">Please fill all fields.</span></div>
      <div class="success-msg" id="reg-success" style="display:none;"><span>✅</span><span id="reg-success-text">Account created!</span></div>
      <div class="form-group"><label class="form-label">Full Name</label>
        <div class="input-wrap"><span class="input-icon">👤</span>
          <input class="form-input" id="reg-name" type="text" placeholder="Enter your full name"/>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Email Address</label>
        <div class="input-wrap"><span class="input-icon">✉️</span>
          <input class="form-input" id="reg-email" type="email" placeholder="Enter your email address"/>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Role</label>
        <div class="input-wrap"><span class="input-icon">🏷</span>
          <select class="form-input form-select" id="reg-role">
            <option value="">— Select your role —</option>
            <option value="Library Staff">👤 Library Staff</option>
            <option value="Library Manager">📋 Library Manager</option>
          </select>
        </div>
      
      </div>
      <div class="form-group"><label class="form-label">Password</label>
        <div class="input-wrap"><span class="input-icon">🔒</span>
          <input class="form-input" id="reg-password" type="password" placeholder="Create a password (min 6 chars)" oninput="checkStrength()"/>
          <button class="toggle-pw" onclick="togglePw('reg-password','toggle-reg-pw')" id="toggle-reg-pw" type="button">👁</button>
        </div>
        <div class="pw-strength" id="pw-strength" style="display:none;">
          <div class="pw-strength-bar"><div class="pw-strength-fill" id="pw-fill"></div></div>
          <span class="pw-strength-label" id="pw-label">Weak</span>
        </div>
      </div>
      <div class="form-group"><label class="form-label">Confirm Password</label>
        <div class="input-wrap"><span class="input-icon">🔒</span>
          <input class="form-input" id="reg-confirm" type="password" placeholder="Re-enter your password"/>
        </div>
      </div>
      <button class="login-btn register-btn" id="reg-btn" onclick="doRegister()" type="button"><span id="reg-btn-text">Create Account →</span></button>
      <div class="switch-link">Already have an account? <button onclick="showTab('signin')" type="button">Sign In →</button></div>
    </div>

  </div>

  <script>
  // ============================================================
  //  LibraryQuiet – Login JS (inline — no external dependency)
  // ============================================================
  let loginAttempts = 0;
  const MAX_ATTEMPTS = 5;

  function el(id){ return document.getElementById(id); }
  function showError(id,msg){ const b=el(id); if(!b)return; b.querySelector('span:last-child').textContent=msg; b.style.display='flex'; }
  function hideMsg(id){ const b=el(id); if(b)b.style.display='none'; }
  function showSuccess(id,msg){ const b=el(id); if(!b)return; b.querySelector('span:last-child').textContent=msg; b.style.display='flex'; }

  function showTab(tab){
    const s=tab==='signin';
    el('panel-signin').style.display=s?'':'none';
    el('panel-register').style.display=s?'none':'';
    el('tab-signin').classList.toggle('active',s);
    el('tab-register').classList.toggle('active',!s);
    hideMsg('error-msg'); hideMsg('reg-error'); hideMsg('reg-success');
  }

  function togglePw(inputId,btnId){
    const inp=el(inputId); if(!inp)return;
    inp.type=inp.type==='password'?'text':'password';
    const btn=el(btnId); if(btn)btn.textContent=inp.type==='password'?'👁':'🙈';
  }

  function checkStrength(){
    const val=el('reg-password')?.value||'';
    const bar=el('pw-strength'),fill=el('pw-fill'),label=el('pw-label');
    if(!bar)return;
    if(!val){bar.style.display='none';return;}
    bar.style.display='flex';
    let score=0;
    if(val.length>=6)score++;if(val.length>=10)score++;
    if(/[A-Z]/.test(val))score++;if(/[0-9]/.test(val))score++;if(/[^A-Za-z0-9]/.test(val))score++;
    const lvls=[{pct:'20%',bg:'#ef4444',lbl:'Weak'},{pct:'40%',bg:'#f97316',lbl:'Fair'},{pct:'60%',bg:'#f59e0b',lbl:'Good'},{pct:'80%',bg:'#10b981',lbl:'Strong'},{pct:'100%',bg:'#059669',lbl:'Very Strong'}];
    const l=lvls[Math.min(score,4)];
    fill.style.width=l.pct; fill.style.background=l.bg;
    label.textContent=l.lbl; label.style.color=l.bg;
  }

  function forgotPassword(){ alert('Please contact your Administrator to reset your password.'); }
  function fillDemo(email,password){ if(el('email'))el('email').value=email; if(el('password'))el('password').value=password; hideMsg('error-msg'); }

  async function doLogin(){
    const email=(el('email')?.value||'').trim().toLowerCase();
    const password=(el('password')?.value||'').trim();
    const btnText=el('btn-text');
    hideMsg('error-msg');
    if(!email||!password){ showError('error-msg','Please enter your email and password.'); return; }
    if(loginAttempts>=MAX_ATTEMPTS){ showError('error-msg','Too many failed attempts. Please refresh.'); return; }
    if(btnText) btnText.textContent='Signing in...';
    try{
      const res=await fetch('/NOISE_MONITOR/api.php?action=login',{
        method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({email,password}),
      });
      const data=await res.json();
      if(data.success){
        const icon=data.user.role==='Administrator'?'👑':data.user.role==='Library Manager'?'📋':'👤';
        el('success-name').textContent=`${icon} ${data.user.name}`;
        el('success-role').textContent=data.user.role;
        el('login-card').style.display='none';
        el('success-overlay').style.display='flex';
        let p=0; const bar=el('progress-bar');
        const iv=setInterval(()=>{ p+=2; if(bar)bar.style.width=p+'%'; if(p>=100){clearInterval(iv);window.location.href=data.redirect;} },30);
      } else {
        loginAttempts++;
        const rem=MAX_ATTEMPTS-loginAttempts;
        showError('error-msg',data.error+(rem>0?` ${rem} attempt${rem!==1?'s':''} remaining.`:''));
        if(btnText) btnText.textContent='Sign In →';
      }
    } catch(e){
      showError('error-msg','Connection error. Make sure XAMPP Apache is running.');
      if(btnText) btnText.textContent='Sign In →';
    }
  }

  async function doRegister(){
    const name=(el('reg-name')?.value||'').trim();
    const email=(el('reg-email')?.value||'').trim().toLowerCase();
    const role=el('reg-role')?.value||'';
    const password=el('reg-password')?.value||'';
    const confirm=el('reg-confirm')?.value||'';
    const btnText=el('reg-btn-text');
    hideMsg('reg-error'); hideMsg('reg-success');
    if(!name)                              { showError('reg-error','Please enter your full name.'); return; }
    if(!email||!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){ showError('reg-error','Please enter a valid email.'); return; }
    if(!role)                              { showError('reg-error','Please select your role.'); return; }
    if(password.length<6)                  { showError('reg-error','Password must be at least 6 characters.'); return; }
    if(password!==confirm)                 { showError('reg-error','Passwords do not match.'); return; }
    if(btnText) btnText.textContent='Creating account...';
    try{
      const res=await fetch('/NOISE_MONITOR/api.php?action=register',{
        method:'POST',headers:{'Content-Type':'application/json'},
        body:JSON.stringify({name,email,password,role}),
      });
      const data=await res.json();
      if(data.success){
        ['reg-name','reg-email','reg-password','reg-confirm'].forEach(id=>{const i=el(id);if(i)i.value='';});
        if(el('reg-role'))el('reg-role').value='';
        if(el('pw-strength'))el('pw-strength').style.display='none';
        showSuccess('reg-success',`Account created for ${name}! Redirecting to sign in...`);
        if(btnText) btnText.textContent='Create Account →';
        setTimeout(()=>{ showTab('signin'); if(el('email'))el('email').value=email; },2000);
      } else {
        showError('reg-error',data.error||'Registration failed.');
        if(btnText) btnText.textContent='Create Account →';
      }
    } catch(e){
      showError('reg-error','Connection error. Make sure XAMPP Apache is running.');
      if(btnText) btnText.textContent='Create Account →';
    }
  }

  // Enter key support
  document.addEventListener('DOMContentLoaded',()=>{
    const pw=el('password'); if(pw) pw.addEventListener('keydown',e=>{ if(e.key==='Enter') doLogin(); });
    const rc=el('reg-confirm'); if(rc) rc.addEventListener('keydown',e=>{ if(e.key==='Enter') doRegister(); });
    // Floating particles
    const c=document.getElementById('particles');
    if(c){ for(let i=0;i<18;i++){
      const p=document.createElement('div'); p.className='particle';
      p.style.cssText=`left:${Math.random()*100}%;animation-delay:${Math.random()*8}s;animation-duration:${6+Math.random()*6}s;width:${3+Math.random()*4}px;height:${3+Math.random()*4}px;opacity:${0.2+Math.random()*0.4};`;
      c.appendChild(p);
    }}
  });
  </script>
</body>
</html>