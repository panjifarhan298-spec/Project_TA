
<?php
session_start();

// ===============================
// DATA BALITA
// ===============================

$dataFile = __DIR__ . '/data/balita.json';

if (!file_exists($dataFile)) {
    if (!is_dir(__DIR__ . '/data')) {
        mkdir(__DIR__ . '/data', 0755, true);
    }

    $dataAwal = [
        [
            "id" => 1,
            "nama" => "Wira Arkana",
            "umur" => "2 Tahun 5 Bulan",
            "ttl" => "Sidoarjo, 25 Maret 2024",
            "jk" => "Laki-laki",
            "nik" => "0908378468",
            "status" => "Normal",
            "berat" => 12.4,
            "tinggi" => 86
        ],
        [
            "id" => 2,
            "nama" => "Aura Ana",
            "umur" => "2 Tahun 5 Bulan",
            "ttl" => "Sidoarjo, 10 April 2024",
            "jk" => "Perempuan",
            "nik" => "0908378469",
            "status" => "Normal",
            "berat" => 11.8,
            "tinggi" => 84
        ]
    ];

    file_put_contents(
        $dataFile,
        json_encode($dataAwal, JSON_PRETTY_PRINT)
    );
}

$balita = json_decode(file_get_contents($dataFile), true) ?: [];

// ===============================
// LOGOUT
// ===============================

if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: index.php?page=login");
    exit;
}

// ===============================
// LOGIN DAN SIGNUP
// ===============================

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $action = $_POST['action'] ?? '';

    if ($action === 'signup') {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';
        $confirm = $_POST['confirm'] ?? '';

        if ($username === '' || $password === '') {
            $error = "Username dan password wajib diisi.";
        } elseif ($password !== $confirm) {
            $error = "Konfirmasi password tidak sesuai.";
        } else {
            $_SESSION['user'] = $username;
            $_SESSION['name'] = $username;

            header("Location: index.php?page=balita");
            exit;
        }
    }

    if ($action === 'login') {

        $username = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (
            ($username === 'admin' && $password === 'admin123') ||
            ($username !== '' && $password !== '')
        ) {
            $_SESSION['user'] = $username;
            $_SESSION['name'] = $username;

            header("Location: index.php?page=balita");
            exit;
        }

        $error = "Username atau password salah.";
    }
}

// ===============================
// HALAMAN
// ===============================

$page = $_GET['page'] ?? 'welcome';
$loggedIn = isset($_SESSION['user']);

// Setelah login, hanya boleh membuka Data Balita.
if ($loggedIn && !in_array($page, ['balita'], true)) {
    header("Location: index.php?page=balita");
    exit;
}

// Data Balita wajib login.
if ($page === 'balita' && !$loggedIn) {
    header("Location: index.php?page=login");
    exit;
}

// Halaman lain yang sudah tidak digunakan.
if (!in_array($page, ['welcome', 'intro', 'login', 'signup', 'balita'], true)) {
    header("Location: index.php?page=" . ($loggedIn ? 'balita' : 'login'));
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Data Balita - Posyandu Desa Tropodo</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="assets/style.css">

    <style>

        /* HALAMAN DATA BALITA */

        .data-page {
            max-width: 1250px;
            width: 100%;
            margin: auto;
            padding: 30px;
        }

        .data-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }

        .data-heading h1 {
            font-size: 28px;
            font-weight: 800;
            margin-bottom: 5px;
        }

        .data-heading p {
            color: #888;
            font-size: 15px;
        }

        .total-card {
            background: #f9dff1;
            color: #b34d9c;
            padding: 15px 22px;
            border-radius: 14px;
            font-weight: 700;
        }

        .data-card {
            background: white;
            border-radius: 18px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.06);
            overflow: hidden;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        .table-balita {
            width: 100%;
            min-width: 750px;
            border-collapse: collapse;
            font-size: 14px;
        }

        .table-balita th {
            background: #f9e8f4;
            color: #854576;
            padding: 16px;
            text-align: left;
            white-space: nowrap;
        }

        .table-balita td {
            padding: 16px;
            border-bottom: 1px solid #eee;
        }

        .table-balita tr:hover {
            background: #fff8fc;
        }

        .status {
            padding: 7px 13px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
            background: #dff5e5;
            color: #278548;
            white-space: nowrap;
        }

        .status.berisiko {
            background: #fff0cc;
            color: #9a6b00;
        }

        .status.stunting {
            background: #ffe0e0;
            color: #c03939;
        }

        .empty-data {
            text-align: center;
            padding: 30px !important;
            color: #888;
        }

        @media(max-width: 768px) {
            .data-page {
                padding: 20px 12px;
            }

            .data-heading h1 {
                font-size: 23px;
            }

            .data-card {
                padding: 12px;
            }
        }

    </style>
</head>

<body>

<?php if ($page === 'welcome'): ?>

    <main class="splash">
        <div class="splash-logo">
            <div class="house-logo">⌂</div>
            <b>PenTing Balita Desa Tropodo</b>
        </div>

        <a class="next-arrow" href="index.php?page=intro">→</a>
    </main>

<?php elseif ($page === 'intro'): ?>

    <main class="intro screen">

        <div class="intro-art">
            <div class="bubble">◉<small>Ukur Lingkar<br>Kepala</small></div>
            <div class="bubble">▰<small>Ukur Tinggi<br>Badan</small></div>
            <div class="bubble">◒<small>Ukur Berat<br>Badan</small></div>
        </div>

        <div class="intro-copy">
            <h1>Posyandu Desa Tropodo</h1>
            <p>Memantau data dan perkembangan balita secara digital.</p>

            <a class="btn primary" href="index.php?page=login">
                Mulai
            </a>
        </div>

    </main>

<?php elseif ($page === 'login' || $page === 'signup'): ?>

    <main class="auth screen">

        <a class="back" href="index.php?page=intro">←</a>

        <div class="auth-card">

            <div class="mini-dash"></div>

            <h1>
                <?= $page === 'login' ? 'Log In' : 'Sign Up' ?>
            </h1>

            <form method="post">

                <input type="hidden" name="action"
                    value="<?= $page === 'login' ? 'login' : 'signup' ?>">

                <input
                    name="username"
                    placeholder="Username"
                    required
                >

                <input
                    name="password"
                    type="password"
                    placeholder="Password"
                    required
                >

                <?php if ($page === 'signup'): ?>

                    <input
                        name="confirm"
                        type="password"
                        placeholder="Konfirmasi Password"
                        required
                    >

                <?php endif; ?>

                <button class="btn primary" type="submit">
                    <?= $page === 'login' ? 'Log In' : 'Daftar' ?>
                </button>

            </form>

            <?php if ($error): ?>
                <p class="error"><?= htmlspecialchars($error) ?></p>
            <?php endif; ?>

            <p class="switch-auth">

                <?= $page === 'login'
                    ? 'Belum punya akun?'
                    : 'Sudah punya akun?' ?>

                <a href="index.php?page=<?= $page === 'login' ? 'signup' : 'login' ?>">
                    <?= $page === 'login' ? 'Sign Up' : 'Log In' ?>
                </a>

            </p>

        </div>

    </main>

<?php elseif ($page === 'balita'): ?>

    <div class="app-shell">

        <!-- NAVBAR -->

        <header class="topbar">

            <div class="brand">
                <span class="brand-dot"></span>
                <strong>PenTing Balita Desa Tropodo</strong>
            </div>

            <div class="top-actions">
                <a class="btn primary"
                   href="index.php?logout=1"
                   style="text-decoration:none;">
                    Log Out
                </a>
            </div>

        </header>

        <!-- SIDEBAR -->

        <nav class="side-menu" id="sideMenu">

            <a class="active" href="index.php?page=balita">
                Data Balita
            </a>

            <a href="index.php?logout=1">
                Log Out
            </a>

        </nav>

        <!-- KONTEN -->

        <main class="content">

            <section class="data-page">

                <div class="data-heading">

                    <div>
                        <h1>Data Balita</h1>
                        <p>
                            Daftar data balita Posyandu Desa Tropodo.
                        </p>
                    </div>

                    <div class="total-card">
                        Total Balita: <?= count($balita) ?>
                    </div>

                </div>

                <div class="data-card">

                    <div class="table-wrapper">

                        <table class="table-balita">

                            <thead>
                                <tr>
                                    <th>No.</th>
                                    <th>Nama Balita</th>
                                    <th>Umur</th>
                                    <th>Jenis Kelamin</th>
                                    <th>Berat Badan</th>
                                    <th>Tinggi Badan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>

                            <tbody>

                            <?php if (empty($balita)): ?>

                                <tr>
                                    <td colspan="7" class="empty-data">
                                        Belum ada data balita.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($balita as $no => $b): ?>

                                    <?php
                                        $status = $b['status'] ?? '-';

                                        $statusClass = '';

                                        if ($status === 'Berisiko') {
                                            $statusClass = 'berisiko';
                                        } elseif ($status === 'Stunting') {
                                            $statusClass = 'stunting';
                                        }
                                    ?>

                                    <tr>

                                        <td><?= $no + 1 ?></td>

                                        <td>
                                            <?= htmlspecialchars($b['nama'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($b['umur'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars($b['jk'] ?? '-') ?>
                                        </td>

                                        <td>
                                            <?= htmlspecialchars((string)($b['berat'] ?? '-')) ?> kg
                                        </td>

                                        <td>
                                            <?= htmlspecialchars((string)($b['tinggi'] ?? '-')) ?> cm
                                        </td>

                                        <td>
                                            <span class="status <?= $statusClass ?>">
                                                <?= htmlspecialchars($status) ?>
                                            </span>
                                        </td>

                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>

                            </tbody>

                        </table>

                    </div>

                </div>

            </section>

        </main>

        <footer>
            Posyandu | Desa Tropodo
        </footer>

    </div>

<?php endif; ?>

</body>
</html>
