// Versi statis untuk GitHub Pages.
// Data contoh disimpan di browser (localStorage), belum terhubung ke MySQL/Supabase.
// Jangan gunakan data pribadi balita atau autentikasi ini untuk produksi.

const app = document.getElementById("app");
const seedBalita = [
  {id:1,nama:"Contoh Balita 1",umur:"2 Tahun 5 Bulan",jk:"Laki-laki",berat:12.4,tinggi:86,status:"Normal"},
  {id:2,nama:"Contoh Balita 2",umur:"2 Tahun 5 Bulan",jk:"Perempuan",berat:11.8,tinggi:84,status:"Normal"}
];

function getBalita(){
  try {
    const saved = JSON.parse(localStorage.getItem("pt_balita") || "null");
    if (Array.isArray(saved)) return saved;
  } catch(e) {}
  localStorage.setItem("pt_balita", JSON.stringify(seedBalita));
  return seedBalita;
}
function setPage(page){ location.hash = page; render(); }
function loggedIn(){ return localStorage.getItem("pt_demo_user") !== null; }
function esc(value){ return String(value ?? "-").replace(/[&<>"']/g, c => ({"&":"&amp;","<":"&lt;",">":"&gt;",'"':"&quot;","'":"&#39;"}[c])); }

function welcome(){
  return `<main class="splash"><div class="splash-logo"><div class="house-logo">⌂</div><b>PenTing Balita Desa Tropodo</b></div><a class="next-arrow" href="#intro" aria-label="Lanjut">→</a></main>`;
}
function intro(){
  return `<main class="intro screen"><div class="intro-art">✚</div><div class="intro-copy"><h1>Posyandu Desa Tropodo</h1><p>Memantau data dan perkembangan balita secara digital.</p><a class="btn primary" href="#login">Mulai</a></div></main>`;
}
function authPage(page){
  const signup = page === "signup";
  return `<main class="auth screen"><section class="auth-card"><a class="back" href="#intro">← Kembali</a><h1>${signup?"Sign Up":"Log In"}</h1><form id="authForm"><input id="username" placeholder="Username" autocomplete="username" required><input id="password" type="password" placeholder="Password" autocomplete="current-password" required>${signup?'<input id="confirm" type="password" placeholder="Konfirmasi Password" required>':''}<button class="btn primary" type="submit">${signup?"Daftar":"Log In"}</button><p id="authError" class="error" aria-live="polite"></p></form><p class="switch-auth">${signup?"Sudah punya akun?":"Belum punya akun?"} <a href="#${signup?"login":"signup"}">${signup?"Log In":"Sign Up"}</a></p><p class="switch-auth">Mode demo: akun hanya tersimpan di browser ini.</p></section></main>`;
}
function balitaPage(){
  const rows = getBalita().map((b,i)=>{
    const cls = b.status === "Berisiko" ? "berisiko" : b.status === "Stunting" ? "stunting" : "";
    return `<tr><td>${i+1}</td><td>${esc(b.nama)}</td><td>${esc(b.umur)}</td><td>${esc(b.jk)}</td><td>${esc(b.berat)} kg</td><td>${esc(b.tinggi)} cm</td><td><span class="status ${cls}">${esc(b.status)}</span></td></tr>`;
  }).join("");
  return `<div class="app-shell"><header class="topbar"><div class="brand"><span class="brand-dot"></span><strong>PenTing Balita Desa Tropodo</strong></div><button class="btn primary" id="logoutBtn">Log Out</button></header><nav class="side-menu"><a class="active" href="#balita">Data Balita</a><a href="#balita">Dashboard</a></nav><main class="content"><section class="data-page"><div class="data-heading"><div><h1>Data Balita</h1><p>Daftar data balita Posyandu Desa Tropodo.</p></div><div class="total-card">Total Balita: ${getBalita().length}</div></div><div class="data-card"><div class="table-wrapper"><table class="table-balita"><thead><tr><th>No.</th><th>Nama Balita</th><th>Umur</th><th>Jenis Kelamin</th><th>Berat Badan</th><th>Tinggi Badan</th><th>Status</th></tr></thead><tbody>${rows || '<tr><td colspan="7" class="empty-data">Belum ada data balita.</td></tr>'}</tbody></table></div></div></section></main><footer>Posyandu | Desa Tropodo</footer></div>`;
}
function render(){
  let page = location.hash.replace("#","") || "welcome";
  if (loggedIn() && ["welcome","intro","login","signup",""].includes(page)) page = "balita";
  if (page === "balita" && !loggedIn()) page = "login";
  if (!["welcome","intro","login","signup","balita"].includes(page)) page = loggedIn()?"balita":"login";
  app.innerHTML = page==="welcome" ? welcome() : page==="intro" ? intro() : page==="balita" ? balitaPage() : authPage(page);

  const form = document.getElementById("authForm");
  if(form) form.addEventListener("submit", e=>{
    e.preventDefault();
    const username = document.getElementById("username").value.trim();
    const password = document.getElementById("password").value;
    const confirm = document.getElementById("confirm");
    const error = document.getElementById("authError");
    if(!username || !password){ error.textContent="Username dan password wajib diisi."; return; }
    if(confirm && password !== confirm.value){ error.textContent="Konfirmasi password tidak sesuai."; return; }
    // Demo saja: bukan autentikasi aman. Ganti dengan Supabase Auth sebelum produksi.
    localStorage.setItem("pt_demo_user", username);
    setPage("balita");
  });
  const logout = document.getElementById("logoutBtn");
  if(logout) logout.addEventListener("click", ()=>{ localStorage.removeItem("pt_demo_user"); setPage("login"); });
}
window.addEventListener("hashchange", render);
render();
