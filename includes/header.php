<?php
/**
 * Header umum (dipakai oleh semua halaman internal setelah login)
 * Variabel opsional: $pageTitle
 */
if (!isset($pageTitle)) $pageTitle = "Sistem Posyandu Tropodo";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= bersih($pageTitle) ?> - Posyandu Desa Tropodo</title>

<!-- Bootstrap 5 -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<!-- Google Font -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
<!-- Tailwind (utility classes saja) -->
<script src="https://cdn.tailwindcss.com"></script>
<!-- Custom Style -->
<link rel="stylesheet" href="/Posyandu_Desa_Tropodo/assets/css/style.css?v=20261117logo">
</head>
<body class="internal-body">
