<?php
require_once __DIR__ . '/includes/config.php';
if (isLoggedIn()) {
    $role = $_SESSION['user_role'];
    if ($role === 'Administrator')    { header('Location: /NOISE_MONITOR/dashboards/admin/dashboard-admin.php'); exit; }
    if ($role === 'Library Manager')  { header('Location: /NOISE_MONITOR/dashboards/manager/dashboard-manager.php'); exit; }
    header('Location: /NOISE_MONITOR/dashboards/staff.php'); exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>LibraryQuiet – Sign In</title>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="/NOISE_MONITOR/assets/style.css"/>
</head>
<body>
  <div class="bg-grid"></div>
  <div class="bg-glow1"></div>
  <div class="bg-glow2"></div>
  <div class="bg-glow3"></div>
  <div class="particles" id="particles"></div>
  <div class="success-overlay" id="success-overlay" style="display:none;">
    <div class="success-card">
      <div class="success-icon">✅</div>
      <div class="success-name" id="success-name">Welcome!</div>
      <div class="success-role" id="success-role">Administrator</div>
      <div class="success-sub">Redirecting to your dashboard...</div>
      <div class="success-bar"><div class="success-bar-fill" id="progress-bar"></div></div>
    </div>
  </div>
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
    <!-- SIGN IN -->
    <div id="panel-signin">
      <div class="panel-title">Welcome back</div>
      <div class="panel-sub">Sign in to access the monitoring dashboard</div>
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
      <div class="demo-section">
        <div class="demo-label">Quick Demo Access</div>
        <div class="demo-chips">
          <div class="demo-chip" onclick="fillDemo('admin@library.edu','admin123')">
            <div class="chip-icon">👑</div><div class="chip-role" style="color:#3b82f6;">Admin</div>
            <div class="chip-email">admin@library.edu</div><div class="chip-pass">admin123</div>
          </div>
          <div class="demo-chip" onclick="fillDemo('james@library.edu','james123')">
            <div class="chip-icon">📋</div><div class="chip-role" style="color:#10b981;">Manager</div>
            <div class="chip-email">james@library.edu</div><div class="chip-pass">james123</div>
          </div>
          <div class="demo-chip" onclick="fillDemo('staff@library.edu','staff123')">
            <div class="chip-icon">👤</div><div class="chip-role" style="color:#8b5cf6;">Staff</div>
            <div class="chip-email">staff@library.edu</div><div class="chip-pass">staff123</div>
          </div>
        </div>
      </div>
      <div class="switch-link">No account yet? <button onclick="showTab('register')" type="button">Create Account →</button></div>
    </div>
    <!-- REGISTER -->
    <div id="panel-register" style="display:none;">
      <div class="panel-title">Create Account</div>
      <div class="panel-sub">Register to access the monitoring system</div>
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
        <div class="role-note">⚠️ Administrator accounts can only be created by an existing Admin.</div>
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
  <script src="/NOISE_MONITOR/login.js"></script>
</body>
</html>