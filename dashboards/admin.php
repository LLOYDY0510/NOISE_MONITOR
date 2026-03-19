<?php
require '../includes/auth.php';
requireLogin();

// Only admin can access
if ($_SESSION['user']['role'] !== 'Administrator') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Dashboard — Noise Monitor</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">

<style>
/* ===== GENERAL ===== */
body, html {
  margin:0;
  padding:0;
  font-family:'Plus Jakarta Sans',sans-serif;
  background:#0f172a;
  color:white;
  scroll-behavior:smooth;
  scroll-snap-type: y mandatory;
}
.section { min-height:100vh; padding:100px 40px 60px; scroll-snap-align:start; display:flex; flex-direction:column; gap:20px; }
h1,h2,h3,h4,h5,h6 { margin:0; }
.card { background: rgba(255,255,255,.05); padding:25px; border-radius:15px; }
.btn { padding:10px 15px; border:none; border-radius:8px; cursor:pointer; background:#3b82f6; color:white; font-weight:600; margin-top:10px; text-decoration:none; display:inline-block; text-align:center;}
.status { padding:8px 12px; border-radius:8px; display:inline-block; }
.normal { background:#16a34a; }
.warning { background:#facc15; color:black; }
.danger { background:#dc2626; }

/* ===== TOPBAR & SCROLL ===== */
#topbar { position:fixed; top:0; left:0; right:0; height:60px; background:rgba(15,23,42,.95); display:flex; align-items:center; padding:0 20px; z-index:1000; box-shadow:0 2px 8px rgba(0,0,0,.5); }
#topbar h2 { margin:0; font-size:20px; font-weight:800; flex:1; }
.nav-link { margin-left:20px; color:#94a3b8; font-weight:600; cursor:pointer; transition:0.2s; }
.nav-link.active { color:#3b82f6; }
#scrollProgress { position:fixed; top:0; left:0; height:4px; background:#3b82f6; width:0; z-index:1001; }

/* ===== NOISE CIRCLE ===== */
#noiseCircle { width:200px; height:200px; border-radius:50%; border:10px solid rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; margin-top:20px; }

/* ===== SLIDER ===== */
input[type=range] { width:100%; margin-top:10px; }

/* ===== USERS GRID ===== */
.user-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:15px; }
.user-card { background: rgba(255,255,255,.05); padding:15px; border-radius:12px; display:flex; justify-content:space-between; align-items:center; }

/* ===== SCROLL TOP BUTTON ===== */
#scrollTopBtn { position:fixed; bottom:20px; right:20px; display:none; padding:12px 16px; border:none; border-radius:8px; background:#3b82f6; color:white; cursor:pointer; font-weight:700; z-index:1000; }
</style>
</head>
<body>

<div id="scrollProgress"></div>

<!-- TOPBAR -->
<div id="topbar">
  <h2>Admin Dashboard</h2>
  <span class="nav-link" onclick="scrollToSection('monitoring')">Monitoring</span>
  <span class="nav-link" onclick="scrollToSection('alerts')">Alerts</span>
  <span class="nav-link" onclick="scrollToSection('reports')">Reports</span>
  <span class="nav-link" onclick="scrollToSection('users')">Users</span>
  <a href="../logout.php" class="btn" style="margin-left:20px; background:#ef4444;">Logout</a>
</div>

<!-- 📡 MONITORING -->
<div class="section" id="monitoring">
  <h1>📊 Noise Level</h1>
  <div class="card">
    <div id="noiseCircle">-- dB</div>
    <p>Manual Noise Level: <input type="range" id="manualNoise" min="0" max="100" value="0"></p>
    <p>Session Stats — Min: <span id="minNoise">--</span> dB | Max: <span id="maxNoise">--</span> dB | Avg: <span id="avgNoise">--</span> dB</p>
  </div>
</div>

<!-- 🚨 ALERTS -->
<div class="section" id="alerts">
  <h1>🚨 Alerts</h1>
  <div class="card" id="alertBox">
    <p>No alerts yet.</p>
    <button class="btn" onclick="clearAlerts()">Clear All</button>
  </div>
</div>

<!-- 📋 REPORTS -->
<div class="section" id="reports">
  <h1>📋 Reports</h1>
  <div class="card">
    <p>Daily, Weekly, Alert Incident, Zone Compliance</p>
    <button class="btn" onclick="generateReport()">Generate Report</button>
    <p id="reportResult"></p>
  </div>
</div>

<!-- 👥 USERS -->
<div class="section" id="users">
  <h1>👥 Users</h1>
  <div class="user-grid">
    <?php
      // Example users array
      $users = [
        ['name'=>'Manager 1','role'=>'Library Manager','status'=>'active'],
        ['name'=>'Staff 1','role'=>'Library Staff','status'=>'active'],
        ['name'=>'Staff 2','role'=>'Library Staff','status'=>'inactive'],
      ];
      foreach($users as $u){
        echo "<div class='user-card'>
                <span>{$u['name']} — {$u['role']} — <span class='{$u['status']}'>{$u['status']}</span></span>
                <button class='btn' onclick='editUser(\"{$u['name']}\")'>Edit</button>
              </div>";
      }
    ?>
  </div>
</div>

<button id="scrollTopBtn" onclick="scrollToTop()"> Top</button>

<script>
// ---------- VARIABLES ----------
let noiseHistory = [];
let minNoise = 100, maxNoise = 0, sumNoise = 0;

// ---------- SIMULATE NOISE ----------
function updateNoise(val=null) {
  let noise = val ?? Math.floor(Math.random()*100);
  document.getElementById("noiseCircle").innerText = noise + " dB";

  // Update stats
  noiseHistory.push(noise);
  if (noise < minNoise) minNoise=noise;
  if (noise > maxNoise) maxNoise=noise;
  sumNoise += noise;
  document.getElementById("minNoise").innerText = minNoise;
  document.getElementById("maxNoise").innerText = maxNoise;
  document.getElementById("avgNoise").innerText = Math.round(sumNoise/noiseHistory.length);

  // Update alerts
  const alertBox = document.getElementById("alertBox");
  if(noise<50) alertBox.innerHTML="<p>No alerts.</p><button class='btn' onclick='clearAlerts()'>Clear All</button>";
  else if(noise<75) alertBox.innerHTML="<p>⚠️ Noise Warning: "+noise+" dB</p><button class='btn' onclick='clearAlerts()'>Clear All</button>";
  else alertBox.innerHTML="<p>🚨 Critical Noise Alert: "+noise+" dB</p><button class='btn' onclick='clearAlerts()'>Clear All</button>";
}

// Manual slider
document.getElementById("manualNoise").addEventListener("input", e => updateNoise(parseInt(e.target.value)));

// Clear alerts
function clearAlerts() {
  document.getElementById("alertBox").innerHTML="<p>No alerts.</p><button class='btn' onclick='clearAlerts()'>Clear All</button>";
}

// Generate report
function generateReport() {
  document.getElementById("reportResult").innerText="✅ Report generated (demo)";
}

// Edit user (only when necessary)
function editUser(name){
  let newRole = prompt("Edit role for " + name + " (Library Manager / Library Staff):");
  if(newRole) alert("✅ " + name + " role updated to: " + newRole + " (demo only)");
}

// Auto update
setInterval(() => updateNoise(),3000);

// ---------- SCROLL NAV HIGHLIGHT ----------
const sections = document.querySelectorAll(".section");
const navLinks = document.querySelectorAll(".nav-link");
window.addEventListener("scroll", () => {
  let scrollTop = window.scrollY;
  let scrollHeight = document.body.scrollHeight - window.innerHeight;
  document.getElementById("scrollProgress").style.width = (scrollTop/scrollHeight*100)+"%";

  sections.forEach((sec,i)=>{
    const rect = sec.getBoundingClientRect();
    if(rect.top<=80 && rect.bottom>=80) {
      navLinks.forEach(l=>l.classList.remove("active"));
      navLinks[i].classList.add("active");
    }
  });

  // Scroll top button
  const btn = document.getElementById("scrollTopBtn");
  btn.style.display = scrollTop>300 ? "block" : "none";
});

function scrollToSection(id) { document.getElementById(id).scrollIntoView({behavior:"smooth"}); }
function scrollToTop() { window.scrollTo({top:0,behavior:"smooth"}); }
</script>

</body>
</html>