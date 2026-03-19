<?php
require '../includes/auth.php';
requireLogin();

if ($_SESSION['user']['role'] !== 'Library Manager') {
    header("Location: ../index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Manager Dashboard — Noise Monitor</title>
<link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
<style>
body, html { margin:0; padding:0; font-family:'Plus Jakarta Sans',sans-serif; background:#0f172a; color:white; scroll-behavior:smooth; scroll-snap-type:y mandatory; }
.section { min-height:100vh; padding:100px 40px 60px; scroll-snap-align:start; display:flex; flex-direction:column; gap:20px; }
h1,h2,h3,h4,h5,h6 { margin:0; }
.card { background: rgba(255,255,255,.05); padding:25px; border-radius:15px; }
.btn { padding:10px 15px; border:none; border-radius:8px; cursor:pointer; background:#3b82f6; color:white; font-weight:600; margin-top:10px; text-decoration:none; display:inline-block; text-align:center;}
.status { padding:8px 12px; border-radius:8px; display:inline-block; }
.normal { background:#16a34a; }
.warning { background:#facc15; color:black; }
.danger { background:#dc2626; }
#topbar { position:fixed; top:0; left:0; right:0; height:60px; background:rgba(15,23,42,.95); display:flex; align-items:center; padding:0 20px; z-index:1000; box-shadow:0 2px 8px rgba(0,0,0,.5);}
#topbar h2 { margin:0; font-size:20px; font-weight:800; flex:1; }
.nav-link { margin-left:20px; color:#94a3b8; font-weight:600; cursor:pointer; transition:0.2s; }
.nav-link.active { color:#3b82f6; }
#scrollProgress { position:fixed; top:0; left:0; height:4px; background:#3b82f6; width:0; z-index:1001; }
#noiseCircle { width:200px; height:200px; border-radius:50%; border:10px solid rgba(255,255,255,.2); display:flex; align-items:center; justify-content:center; font-size:24px; font-weight:800; margin-top:20px; }
input[type=range] { width:100%; margin-top:10px; }
.zone-grid { display:grid; grid-template-columns:repeat(auto-fit,minmax(250px,1fr)); gap:15px; }
.zone-card { background: rgba(255,255,255,.05); padding:15px; border-radius:12px; }
#campusMap { height:350px; width:100%; border-radius:12px; margin-top:15px; }
#scrollTopBtn { position:fixed; bottom:20px; right:20px; display:none; padding:12px 16px; border:none; border-radius:8px; background:#3b82f6; color:white; cursor:pointer; font-weight:700; z-index:1000; }

/* Alert Bar Styles */
#alertBarContainer, #alertBarContainerLow, #alertBarContainerAverage, #alertBarContainerHigh { background:#334155; border-radius:8px; height:20px; margin-top:10px; overflow:hidden; }
#alertBar, #alertBarLow, #alertBarAverage, #alertBarHigh { width:0%; height:100%; transition:0.3s; }

/* Reports list */
#reportResult { list-style:none; padding-left:0; }
.report-entry { display:flex; justify-content:space-between; align-items:center; margin-bottom:8px; background: rgba(255,255,255,.1); padding:6px 10px; border-radius:8px; }
.report-entry span { flex:1; word-break:break-word; }
.report-entry button { margin-left:5px; padding:4px 8px; border-radius:6px; font-size:12px; background:#3b82f6; color:white; border:none; cursor:pointer; }
.report-entry button.delete { background:#ef4444; }
</style>
</head>
<body>

<div id="scrollProgress"></div>

<!-- TOPBAR -->
<div id="topbar">
  <h2>Manager Dashboard</h2>
  <span class="nav-link" onclick="scrollToSection('zones')">Zones</span>
  <span class="nav-link" onclick="scrollToSection('monitoring')">Monitoring</span>
  <span class="nav-link" onclick="scrollToSection('alerts')">Alerts</span>
  <span class="nav-link" onclick="scrollToSection('reports')">Reports</span>
  <a href="../logout.php" class="btn" style="margin-left:20px; background:#ef4444;">Logout</a>
</div>

<!-- 📍 ZONES -->
<div class="section" id="zones">
  <h1>📍 Zones</h1>
  <div class="zone-grid">
    <div class="zone-card" id="zone1">Reference — 0 dB</div>
    <div class="zone-card" id="zone2">Corner 1 — 0 dB</div>
    <div class="zone-card" id="zone3">Center — 0 dB</div>
    <div class="zone-card" id="zone4">Corner 2 — 0 dB</div>
  </div>
  <div id="campusMap"></div>
</div>

<!-- 📡 MONITORING -->
<div class="section" id="monitoring">
  <h1>📊 Noise Level</h1>
  <div class="card">
    <div id="noiseCircle">-- dB</div>
    <p>Manual Noise Level: <input type="range" id="manualNoise" min="0" max="100" value="0"></p>
    <button class="btn" onclick="recordNoise()">Record Noise</button>
    <p>Session Stats — Min: <span id="minNoise">--</span> dB | Max: <span id="maxNoise">--</span> dB | Avg: <span id="avgNoise">--</span> dB</p>
  </div>
</div>

<!-- 🚨 ALERTS -->
<div class="section" id="alerts">
  <h1>🚨 Alerts</h1>
  <div class="card" id="alertLow">
    <p>Low Noise</p>
    <div id="alertBarContainerLow"><div id="alertBarLow"></div></div>
    <p id="alertLevelLow">Level: -</p>
  </div>
  <div class="card" id="alertAverage">
    <p>Average Noise</p>
    <div id="alertBarContainerAverage"><div id="alertBarAverage"></div></div>
    <p id="alertLevelAverage">Level: -</p>
  </div>
  <div class="card" id="alertHigh">
    <p>High Noise</p>
    <div id="alertBarContainerHigh"><div id="alertBarHigh"></div></div>
    <p id="alertLevelHigh">Level: -</p>
  </div>
  <button class="btn" onclick="clearAllAlerts()">Clear All</button>
</div>

<!-- 📋 REPORTS -->
<div class="section" id="reports">
  <h1>📋 Reports</h1>
  <div class="card">
    <p>Recorded Noise Events:</p>
    <ul id="reportResult"></ul>
  </div>
</div>

<button id="scrollTopBtn" onclick="scrollToTop()"> Top</button>

<script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
<script>
let noiseHistory = [];
let minNoise=100, maxNoise=0, sumNoise=0;

// Random offset for zones
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

  // Update zones
  document.getElementById("zone1").innerText="Reference — "+(noise+randOffset())+" dB";
  document.getElementById("zone2").innerText="Corner 1 — "+(noise+randOffset())+" dB";
  document.getElementById("zone3").innerText="Center — "+(noise+randOffset())+" dB";
  document.getElementById("zone4").innerText="Corner 2 — "+(noise+randOffset())+" dB";

  // Update alert boxes
  updateAllAlerts(noise);
}

// Manual slider
document.getElementById("manualNoise").addEventListener("input", e => updateNoise(parseInt(e.target.value)));

// Alerts
function updateAllAlerts(noise){
  const alerts = [
    {bar:'alertBarLow', levelText:'alertLevelLow', min:0, max:50, color:'#16a34a', text:'Low'},
    {bar:'alertBarAverage', levelText:'alertLevelAverage', min:51, max:75, color:'#facc15', text:'Average'},
    {bar:'alertBarHigh', levelText:'alertLevelHigh', min:76, max:100, color:'#dc2626', text:'High'}
  ];
  alerts.forEach(a=>{
    const bar = document.getElementById(a.bar);
    const levelText = document.getElementById(a.levelText);
    if(noise >= a.min && noise <= a.max){
      bar.style.width='100%';
      bar.style.background=a.color;
      levelText.innerText=`Level: ${a.text}`;
    } else {
      bar.style.width='0%';
      levelText.innerText='Level: -';
    }
  });
}

// Clear alerts
function clearAllAlerts(){
  ['Low','Average','High'].forEach(l=>{
    document.getElementById('alertBar'+l).style.width='0%';
    document.getElementById('alertLevel'+l).innerText='Level: -';
  });
}

// Record noise and add report with Edit/Delete
function recordNoise(){
  const values = [
    parseInt(document.getElementById("zone1").innerText.split("—")[1]),
    parseInt(document.getElementById("zone2").innerText.split("—")[1]),
    parseInt(document.getElementById("zone3").innerText.split("—")[1]),
    parseInt(document.getElementById("zone4").innerText.split("—")[1])
  ];
  const zones = ["Reference","Corner 1","Center","Corner 2"];
  const maxIndex = values.indexOf(Math.max(...values));
  const zoneName = zones[maxIndex];
  const noise = values[maxIndex];

  updateAllAlerts(noise);

  const li = document.createElement("li");
  li.className = "report-entry";

  const span = document.createElement("span");
  const time = new Date().toLocaleString();
  span.textContent = `${time} — ${noise} dB at ${zoneName}`;

  const editBtn = document.createElement("button");
  editBtn.textContent = "Edit";
  editBtn.onclick = ()=> {
    const newVal = prompt("Edit report:", span.textContent);
    if(newVal) span.textContent = newVal;
  };

  const delBtn = document.createElement("button");
  delBtn.textContent = "Delete";
  delBtn.className = "delete";
  delBtn.onclick = ()=> li.remove();

  li.appendChild(span);
  li.appendChild(editBtn);
  li.appendChild(delBtn);
  document.getElementById("reportResult").appendChild(li);

  scrollToSection('alerts');
}

// Auto-update noise every 3 sec
setInterval(()=>updateNoise(),3000);

// Scroll and nav
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

// Leaflet map
const map = L.map('campusMap').setView([8.35962,124.86808],17);
L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',{attribution:'&copy; OpenStreetMap contributors'}).addTo(map);
const corners=[{lat:8.35962,lng:124.86808,label:"Library"},{lat:8.35980,lng:124.86780,label:"Corner 1"},{lat:8.35940,lng:124.86850,label:"Corner 2"}];
corners.forEach(c=>L.marker([c.lat,c.lng]).addTo(map).bindPopup(c.label).openPopup());
</script>
</body>
</html>