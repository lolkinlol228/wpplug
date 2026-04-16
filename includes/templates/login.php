<?php if (!defined('ABSPATH')) exit; ?>
<style>
.tabel-login-wrap {
  --bg: #fafbfc; --surface: #ffffff; --border: #e1e4e8;
  --text: #24292f; --text-secondary: #57606a; --text-tertiary: #8c959f;
  --primary: #0969da; --primary-hover: #0550ae; --danger: #cf222e; --radius: 6px;
  --shadow-md: 0 3px 6px rgba(0,0,0,0.08), 0 3px 6px rgba(0,0,0,0.1);
  font-family: 'Inter', -apple-system, sans-serif;
  display: flex; align-items: center; justify-content: center; min-height: 60vh; padding: 24px;
}
.tabel-login-wrap .login-page { width:100%; max-width:400px; }
.tabel-login-wrap .login-header { text-align:center; margin-bottom:28px; }
.tabel-login-wrap .login-icon {
  width:52px; height:52px; background:var(--primary); border-radius:12px;
  display:inline-flex; align-items:center; justify-content:center;
  font-size:24px; margin-bottom:14px; box-shadow:0 4px 12px rgba(9,105,218,.25);
}
.tabel-login-wrap .login-header h1 { font-size:20px; font-weight:700; margin-bottom:4px; }
.tabel-login-wrap .login-header p { font-size:13px; color:var(--text-secondary); }
.tabel-login-wrap .login-card {
  background:var(--surface); border:1px solid var(--border);
  border-radius:var(--radius); padding:24px; box-shadow:var(--shadow-md);
}
.tabel-login-wrap .form-group { display:flex; flex-direction:column; gap:6px; margin-bottom:14px; }
.tabel-login-wrap .form-group label { font-size:12px; font-weight:600; color:var(--text-secondary); text-transform:uppercase; letter-spacing:.4px; }
.tabel-login-wrap .form-group input {
  padding:9px 12px; background:var(--surface); border:1px solid var(--border);
  border-radius:var(--radius); color:var(--text); font-family:inherit; font-size:14px; outline:none;
}
.tabel-login-wrap .form-group input:focus { border-color:var(--primary); box-shadow:0 0 0 3px rgba(9,105,218,.1); }
.tabel-login-wrap .btn-login {
  width:100%; padding:10px 16px; background:var(--primary); border:none;
  border-radius:var(--radius); color:#fff; font-size:14px; font-weight:600; cursor:pointer; margin-top:6px;
}
.tabel-login-wrap .btn-login:hover { background:var(--primary-hover); }
.tabel-login-wrap .btn-login:disabled { opacity:.6; cursor:not-allowed; }
.tabel-login-wrap .error-msg {
  display:none; background:#ffebe9; border:1px solid #ffadb5;
  border-radius:var(--radius); padding:9px 12px; font-size:13px;
  color:var(--danger); margin-bottom:14px; gap:8px; align-items:center;
}
.tabel-login-wrap .error-msg.show { display:flex; }
.tabel-login-wrap .login-footer { text-align:center; margin-top:20px; font-size:12px; color:var(--text-tertiary); }
</style>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<div class="tabel-login-wrap">
<div class="login-page">
  <div class="login-header">
    <div class="login-icon">📋</div>
    <h1>Табель учёта</h1>
    <p>Войдите в систему для продолжения</p>
  </div>
  <div class="login-card">
    <div class="error-msg" id="tabelErrMsg"><span>⚠️</span><span id="tabelErrText"></span></div>
    <div class="form-group">
      <label>Логин</label>
      <input type="text" id="tabelUsername" placeholder="Введите логин" autocomplete="username" autofocus>
    </div>
    <div class="form-group">
      <label>Пароль</label>
      <input type="password" id="tabelPassword" placeholder="••••••••" autocomplete="current-password">
    </div>
    <button class="btn-login" onclick="tabelDoLogin()" id="tabelLoginBtn">Войти</button>
  </div>
  <div class="login-footer">Система учёта рабочего времени</div>
</div>
</div>
<script>
(function(){
var TABEL_API = <?php echo json_encode($api_base); ?>;
document.addEventListener('keydown', function(e) { if(e.key==='Enter') tabelDoLogin(); });
window.tabelDoLogin = async function() {
  var username = document.getElementById('tabelUsername').value.trim();
  var password = document.getElementById('tabelPassword').value;
  var btn = document.getElementById('tabelLoginBtn');
  var err = document.getElementById('tabelErrMsg');
  if(!username||!password) { document.getElementById('tabelErrText').textContent='Введите логин и пароль'; err.classList.add('show'); return; }
  btn.textContent='Входим...'; btn.disabled=true; err.classList.remove('show');
  try {
    var res = await fetch(TABEL_API + 'login',{method:'POST',headers:{'Content-Type':'application/json'},body:JSON.stringify({username:username,password:password})});
    var data;
    try { data = await res.json(); } catch(je) {
      var errText = res.status === 503 ? '🔧 Система на техническом обслуживании. Попробуйте позже.' : 'Ошибка сервера (' + res.status + ')';
      document.getElementById('tabelErrText').textContent = errText;
      err.classList.add('show'); btn.textContent='Войти'; btn.disabled=false; return;
    }
    if(data.ok) { window.location.reload(); }
    else { document.getElementById('tabelErrText').textContent=data.error||'Неверный логин или пароль'; err.classList.add('show'); btn.textContent='Войти'; btn.disabled=false; }
  } catch(e) { document.getElementById('tabelErrText').textContent='Ошибка соединения. Проверьте интернет.'; err.classList.add('show'); btn.textContent='Войти'; btn.disabled=false; }
};
})();
</script>