// ============================================================
//  LibraryQuiet – login.js
// ============================================================
let loginAttempts = 0;
const MAX_ATTEMPTS = 5;
function el(id){return document.getElementById(id);}
function showError(id,msg){const b=el(id);if(!b)return;b.querySelector('span:last-child').textContent=msg;b.style.display='flex';}
function hideMsg(id){const b=el(id);if(b)b.style.display='none';}
function showSuccess(id,msg){const b=el(id);if(!b)return;b.querySelector('span:last-child').textContent=msg;b.style.display='flex';}
function showTab(tab){
  const s=tab==='signin';
  el('panel-signin').style.display=s?'':'none';
  el('panel-register').style.display=s?'none':'';
  el('tab-signin').classList.toggle('active',s);
  el('tab-register').classList.toggle('active',!s);
  hideMsg('error-msg');hideMsg('reg-error');hideMsg('reg-success');
}
function togglePw(inputId,btnId){
  const inp=el(inputId);if(!inp)return;
  inp.type=inp.type==='password'?'text':'password';
  const btn=el(btnId);if(btn)btn.textContent=inp.type==='password'?'👁':'🙈';
}
function checkStrength(){
  const val=el('reg-password')?.value||'';
  const bar=el('pw-strength'),fill=el('pw-fill'),label=el('pw-label');
  if(!bar)return;if(!val){bar.style.display='none';return;}
  bar.style.display='flex';let score=0;
  if(val.length>=6)score++;if(val.length>=10)score++;
  if(/[A-Z]/.test(val))score++;if(/[0-9]/.test(val))score++;if(/[^A-Za-z0-9]/.test(val))score++;
  const lvls=[{pct:'20%',bg:'#ef4444',lbl:'Weak'},{pct:'40%',bg:'#f97316',lbl:'Fair'},{pct:'60%',bg:'#f59e0b',lbl:'Good'},{pct:'80%',bg:'#10b981',lbl:'Strong'},{pct:'100%',bg:'#059669',lbl:'Very Strong'}];
  const l=lvls[Math.min(score,4)];fill.style.width=l.pct;fill.style.background=l.bg;label.textContent=l.lbl;label.style.color=l.bg;
}
function forgotPassword(){alert('Please contact your Administrator to reset your password.');}
function fillDemo(email,password){if(el('email'))el('email').value=email;if(el('password'))el('password').value=password;hideMsg('error-msg');}

async function doLogin(){
  const email=(el('email')?.value||'').trim().toLowerCase();
  const password=(el('password')?.value||'').trim();
  const btnText=el('btn-text');
  hideMsg('error-msg');
  if(!email||!password){showError('error-msg','Please enter your email and password.');return;}
  if(loginAttempts>=MAX_ATTEMPTS){showError('error-msg','Too many failed attempts. Please refresh.');return;}
  if(btnText)btnText.textContent='Signing in...';
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
      let p=0;const bar=el('progress-bar');
      const iv=setInterval(()=>{p+=2;if(bar)bar.style.width=p+'%';if(p>=100){clearInterval(iv);window.location.href=data.redirect;}},30);
    }else{
      loginAttempts++;
      const rem=MAX_ATTEMPTS-loginAttempts;
      showError('error-msg',data.error+(rem>0?` ${rem} attempt${rem!==1?'s':''} remaining.`:''));
      if(btnText)btnText.textContent='Sign In →';
    }
  }catch(e){showError('error-msg','Connection error. Make sure XAMPP Apache is running.');if(btnText)btnText.textContent='Sign In →';}
}

async function doRegister(){
  const name=(el('reg-name')?.value||'').trim();
  const email=(el('reg-email')?.value||'').trim().toLowerCase();
  const role=el('reg-role')?.value||'';
  const password=el('reg-password')?.value||'';
  const confirm=el('reg-confirm')?.value||'';
  const btnText=el('reg-btn-text');
  hideMsg('reg-error');hideMsg('reg-success');
  if(!name){showError('reg-error','Please enter your full name.');return;}
  if(!email||!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)){showError('reg-error','Please enter a valid email.');return;}
  if(!role){showError('reg-error','Please select your role.');return;}
  if(password.length<6){showError('reg-error','Password must be at least 6 characters.');return;}
  if(password!==confirm){showError('reg-error','Passwords do not match.');return;}
  if(btnText)btnText.textContent='Creating account...';
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
      showSuccess('reg-success',`Account created for ${name}! Signing in...`);
      if(btnText)btnText.textContent='Create Account →';
      setTimeout(()=>{showTab('signin');if(el('email'))el('email').value=email;},2000);
    }else{showError('reg-error',data.error||'Registration failed.');if(btnText)btnText.textContent='Create Account →';}
  }catch(e){showError('reg-error','Connection error.');if(btnText)btnText.textContent='Create Account →';}
}

document.addEventListener('DOMContentLoaded',()=>{
  const pw=el('password');if(pw)pw.addEventListener('keydown',e=>{if(e.key==='Enter')doLogin();});
  const rc=el('reg-confirm');if(rc)rc.addEventListener('keydown',e=>{if(e.key==='Enter')doRegister();});
  const c=el('particles');
  if(c){for(let i=0;i<18;i++){const p=document.createElement('div');p.className='particle';
    p.style.cssText=`left:${Math.random()*100}%;top:${Math.random()*100}%;animation-delay:${Math.random()*8}s;animation-duration:${6+Math.random()*6}s;width:${3+Math.random()*4}px;height:${3+Math.random()*4}px;opacity:${0.2+Math.random()*0.4};`;
    c.appendChild(p);}}
});