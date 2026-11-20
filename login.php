<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: " . ($_SESSION['role'] === 'admin' ? 'dashboard-admin.php' : 'dashboard-bidan.php'));
    exit;
}
require_once 'includes/functions.php';

$error = "";
if (isset($_GET['error']) && $_GET['error'] === 'akses_ditolak') {
    $error = "Akses ditolak. Silakan login dengan akun yang sesuai.";
} elseif (isset($_GET['error']) && $_GET['error'] === 'gagal') {
    $error = "Username atau password salah.";
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Masuk ke Akun - Posyandu Digital Sidoarjo</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
  * { box-sizing: border-box; }
  body {
    font-family: 'Poppins', sans-serif;
    background: #F0F7FF;
    min-height: 100vh;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    padding: 20px;
  }
  .login-split-card {
    display: flex;
    width: 100%;
    max-width: 880px;
    min-height: 530px;
    background: #FFFFFF;
    border-radius: 24px;
    overflow: hidden;
    box-shadow: 0 20px 45px rgba(18, 59, 112, 0.12), 0 4px 12px rgba(96, 165, 250, 0.1);
    border: 1px solid rgba(96, 165, 250, 0.2);
  }
  .login-left-banner {
    flex: 0 0 44%;
    background: linear-gradient(145deg, #123B70 0%, #1A4D8F 45%, #60A5FA 100%);
    color: #FFFFFF;
    padding: 38px 30px;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: space-between;
    text-align: center;
    position: relative;
    overflow: hidden;
  }
  .login-left-banner::before {
    content: '';
    position: absolute;
    top: -50px;
    right: -50px;
    width: 220px;
    height: 220px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.08);
  }
  .login-left-banner::after {
    content: '';
    position: absolute;
    bottom: -60px;
    left: -40px;
    width: 240px;
    height: 240px;
    border-radius: 50%;
    background: rgba(255, 255, 255, 0.06);
  }
  .badge-posyandu-digital {
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.35);
    border-radius: 99px;
    padding: 4px 14px;
    font-size: 0.72rem;
    font-weight: 600;
    letter-spacing: 0.5px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    margin-top: 10px;
    backdrop-filter: blur(4px);
  }
  .login-right-form {
    flex: 1;
    padding: 45px 42px;
    display: flex;
    flex-direction: column;
    justify-content: center;
    background: #FFFFFF;
  }
  .form-control-custom {
    border-radius: 12px;
    border: 1px solid rgba(96, 165, 250, 0.35);
    padding: 11px 16px;
    font-size: 0.88rem;
    background: #F8FAFC;
    transition: all 0.2s ease;
  }
  .form-control-custom:focus {
    background: #FFFFFF;
    border-color: #60A5FA;
    box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.2);
  }
  .input-group-custom {
    position: relative;
  }
  .input-icon-left {
    position: absolute;
    left: 14px;
    top: 50%;
    transform: translateY(-50%);
    color: #64748B;
    z-index: 5;
    font-size: 0.85rem;
  }
  .input-with-icon {
    padding-left: 38px;
  }
  .btn-toggle-pw {
    position: absolute;
    right: 14px;
    top: 50%;
    transform: translateY(-50%);
    background: transparent;
    border: none;
    color: #64748B;
    cursor: pointer;
    z-index: 5;
  }
  .btn-login-pill {
    background: linear-gradient(135deg, #123B70 0%, #60A5FA 100%);
    color: #FFFFFF;
    font-weight: 700;
    border-radius: 99px;
    padding: 12px 24px;
    border: none;
    box-shadow: 0 6px 18px rgba(18, 59, 112, 0.25);
    transition: transform 0.2s ease, box-shadow 0.2s ease;
  }
  .btn-login-pill:hover {
    color: #FFFFFF;
    transform: translateY(-2px);
    box-shadow: 0 10px 24px rgba(18, 59, 112, 0.35);
  }
  .btn-back-home {
    color: rgba(255, 255, 255, 0.88);
    text-decoration: none;
    font-size: 0.8rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    padding: 6px 14px;
    border-radius: 99px;
    background: rgba(255, 255, 255, 0.12);
    transition: background 0.2s ease;
    z-index: 2;
  }
  .btn-back-home:hover {
    color: #FFFFFF;
    background: rgba(255, 255, 255, 0.22);
  }
  @media (max-width: 767.98px) {
    .login-split-card {
      flex-direction: column;
      max-width: 420px;
    }
    .login-left-banner {
      padding: 30px 20px;
    }
    .login-right-form {
      padding: 32px 24px;
    }
  }
</style>
</head>
<body>

<div class="login-split-card">
  <!-- Kolom Kiri: Banner Gradien Biru Sidoarjo Sesuai Gambar 2 -->
  <div class="login-left-banner">
    <div class="banner-top text-center w-100 position-relative z-1">
      <img src="assets/img/logo-sidoarjo.png" alt="Logo Kabupaten Sidoarjo" style="width: 82px; height: auto;" class="mb-2 drop-shadow">
      <div>
        <span class="badge-posyandu-digital"><i class="fa-solid fa-heart-pulse"></i> POSYANDU DIGITAL</span>
      </div>
    </div>

    <div class="banner-middle position-relative z-1 my-3">
      <h3 class="fw-bold mb-2" style="font-size: 1.85rem; letter-spacing: -0.3px;">Hello, Welcome</h3>
      <p class="small mb-0 opacity-90 px-2" style="font-size: 0.82rem; line-height: 1.5;">
        Kelola layanan kesehatan ibu, anak, dan lansia dalam satu sistem informasi posyandu yang mudah digunakan.
      </p>
    </div>

    <div class="banner-bottom position-relative z-1">
      <a href="index.php" class="btn-back-home">
        <i class="fa-solid fa-house"></i> Kembali ke Beranda
      </a>
    </div>
  </div>

  <!-- Kolom Kanan: Form Login Sesuai Gambar 2 -->
  <div class="login-right-form">
    <div class="mb-4">
      <span class="d-block fw-bold small text-uppercase mb-1" style="color: #60A5FA; letter-spacing: 0.8px; font-size: 0.75rem;">
        SELAMAT DATANG KEMBALI
      </span>
      <h3 class="fw-bold mb-1" style="color: #123B70; font-size: 1.7rem;">Masuk ke akun Anda</h3>
      <p class="text-muted small mb-0" style="font-size: 0.82rem;">Gunakan username dan password untuk melanjutkan.</p>
    </div>

    <?php if ($error): ?>
      <div class="alert alert-danger py-2 small rounded-3 mb-3 d-flex align-items-center gap-2">
        <i class="fa-solid fa-circle-exclamation"></i>
        <span><?= bersih($error) ?></span>
      </div>
    <?php endif; ?>

    <form action="proses/login.php" method="POST" autocomplete="off">
      <div class="mb-3">
        <label class="form-label small fw-semibold text-muted mb-1"><i class="fa-regular fa-user me-1"></i> Username</label>
        <div class="input-group-custom">
          <i class="fa-solid fa-user input-icon-left"></i>
          <input type="text" name="username" class="form-control form-control-custom input-with-icon" placeholder="Masukkan username" required autofocus>
        </div>
      </div>

      <div class="mb-4">
        <label class="form-label small fw-semibold text-muted mb-1"><i class="fa-solid fa-lock me-1"></i> Password</label>
        <div class="input-group-custom">
          <i class="fa-solid fa-lock input-icon-left"></i>
          <input type="password" name="password" id="inputPassword" class="form-control form-control-custom input-with-icon" placeholder="Masukkan password" required>
          <button type="button" class="btn-toggle-pw" onclick="togglePasswordVisibility()" aria-label="Toggle password">
            <i class="fa-solid fa-eye" id="eyeIcon"></i>
          </button>
        </div>
      </div>

      <button type="submit" class="btn btn-login-pill w-100 py-2 fs-6 mb-3">
        Login ke Sistem
      </button>

      <div class="text-center mt-3">
        <p class="small text-muted mb-0" style="font-size: 0.78rem;">
          Akun petugas dan kader dibuat oleh <strong>Admin Utama</strong>.
        </p>
      </div>
    </form>
  </div>
</div>

<script>
function togglePasswordVisibility() {
  const pwInput = document.getElementById('inputPassword');
  const eye = document.getElementById('eyeIcon');
  if (pwInput.type === 'password') {
    pwInput.type = 'text';
    eye.classList.remove('fa-eye');
    eye.classList.add('fa-eye-slash');
  } else {
    pwInput.type = 'password';
    eye.classList.remove('fa-eye-slash');
    eye.classList.add('fa-eye');
  }
}
</script>

</body>
</html>
