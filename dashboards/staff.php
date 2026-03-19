<?php
require '../includes/auth.php';
requireLogin();

if ($_SESSION['user']['role'] !== 'Library Staff') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Staff Dashboard — Noise Monitor</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

<style>
body, html { margin:0; padding:0; font-family:'Plus Jakarta Sans',sans-serif; background:#0f172a; color:white; scroll-behavior:smooth; scroll-snap-type: y mandatory;}
.section { min-height:100vh; padding:100px 40px 60px; scroll-snap-align:start; display:flex; flex-direction:column; gap:20px; }
h1,h2,h3,h4,h5,h6 { margin:0; }
.card { background: rgba(255,255,255,.05); padding:25px; border-radius:15px; }
.btn { padding:10px 15px; border:none; border-radius:8px; cursor:pointer; background:#3b82f6; color:white; font-weight:600; margin-top:10px; text-decoration:none; display:inline-block; text-align:center;}
#topbar { position:fixed; top:0; left:0; right:0; height:60px; background:rgba(15,23,42,.95); display:flex; align-items:center; padding:0 20px; z-index:1000; box-shadow:0 2px 8px rgba(0,0,0,.5);}
#topbar h2 { margin:0; font-size:20px; font-weight:800; flex:1; }
.nav-link { margin-left:20px; color:#94a3b8; font-weight:600; cursor:pointer; transition:0.2s; }
.nav-link.active { color:#3b82f6; }
#scrollProgress { position:fixed; top:0; left:0; height:4px; background:#3b82f6; width:0; z-index:1001; }
#noiseCircle { width:200px; height:200px; border-radius:50%; border:10px solid rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; margin-top:20px; }
input[type=range] { width:100%; margin-top:10px; }
#scrollTopBtn { position:fixed; bottom:20px; right:20px; display:none; padding:12px 16px; border:none; border-radius:8px; background:#3b82f6; color:white; cursor:pointer; font-weight:700; z-index:1000; }

/* Alert Bar Styles */
#alertBarContainer { background:#334155; border-radius:8px; height:20px; margin-top:15px; overflow:hidden; }
#alertBar { width:0%; height:100%; background:#16a34a; transition:0.3s; }
</style>
</head>
<body>

<div id="scrollProgress"></div>

<!-- TOPBAR -->
<div id="topbar">
  <h2>Staff Dashboard</h2>
  <span class="nav-link" onclick="scrollToSection('monitoring')">Monitoring</span>
  <span class="nav-link" onclick="scrollToSection('alerts')">Alerts</span>
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
    <div id="alertBarContainer">
      <div id="alertBar"></div>
    </div>
    <p id="alertLevel" style="margin-top:5px;">Level: Low</p>
    <button class="btn" onclick="clearAlerts()">Clear All</button>
  </div>
</div>

<button id="scrollTopBtn" onclick="scrollToTop()"> Top</button>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let noiseHistory = [];
let minNoise=100, maxNoise=0, sumNoise=0;

// Random offset simulation
function randOffset(){ return Math.floor(Math.random()*10)-5; }

function updateNoise(val=null){
  let noise = val ?? Math.floor(Math.random()*100);
  document.getElementById("noiseCircle").innerText = noise+" dB";

  noiseHistory.push(noise);
  if(noise<minNoise) minNoise=noise;
  if(noise>maxNoise) maxNoise=noise;
  sumNoise+=noise;
  document.getElementById("minNoise").innerText=minNoise;
  document.getElementById("maxNoise").innerText=maxNoise;
  document.getElementById("avgNoise").innerText=Math.round(sumNoise/noiseHistory.length);

  updateAlertBar(noise);
}

// Manual slider
document.getElementById("manualNoise").addEventListener("input", e => updateNoise(parseInt(e.target.value)));

// Update alert bar
function updateAlertBar(noise){
  const bar = document.getElementById("alertBar");
  const levelText = document.getElementById("alertLevel");
  const alertBox = document.getElementById("alertBox");

  if(noise<50){
    bar.style.width="33%"; bar.style.background="#16a34a"; levelText.innerText="Level: Low";
    alertBox.querySelector('p').innerText=`Low Noise: ${noise} dB`;
  } else if(noise<75){
    bar.style.width="66%"; bar.style.background="#facc15"; levelText.innerText="Level: Average";
    alertBox.querySelector('p').innerText=`Average Noise: ${noise} dB`;
  } else {
    bar.style.width="100%"; bar.style.background="#dc2626"; levelText.innerText="Level: High";
    alertBox.querySelector('p').innerText=`High Noise: ${noise} dB`;
  }
}

// Clear alerts
function clearAlerts(){
  document.getElementById("alertBox").querySelector('p').innerText="No alerts yet.";
  const bar = document.getElementById("alertBar");
  const levelText = document.getElementById("alertLevel");
  bar.style.width="0%"; bar.style.background="#16a34a"; levelText.innerText="Level: Low";
}

// Auto update
setInterval(()=>updateNoise(),3000);

// Scroll highlight
const sections=document.querySelectorAll(".section");
const navLinks=document.querySelectorAll(".nav-link");
window.addEventListener("scroll",()=>{
  let scrollTop=window.scrollY;
  let scrollHeight=document.body.scrollHeight-window.innerHeight;
  document.getElementById("scrollProgress").style.width=(scrollTop/scrollHeight*100)+"%";
  sections.forEach((sec,i)=>{
    const rect=sec.getBoundingClientRect();
    if(rect.top<=80 && rect.bottom>=80){
      navLinks.forEach(l=>l.classList.remove("active"));
      navLinks[i].classList.add("active");
    }
  });
  const btn=document.getElementById("scrollTopBtn");
  btn.style.display=scrollTop>300?"block":"none";
});
function scrollToSection(id){ document.getElementById(id).scrollIntoView({behavior:"smooth"}); }
function scrollToTop(){ window.scrollTo({top:0,behavior:"smooth"}); }

// Leaflet map placeholder
const map = L.map('campusMap')?.setView([8.35962,124.86808],17);
if(map) {
  L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
}
</script>
</body>
</html>