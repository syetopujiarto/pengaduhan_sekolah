<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? $page_title : 'Aplikasi Pengaduan Sarana Sekolah' ?></title>
    <link rel="stylesheet" href="/pengaduan_sekolah/assets/css/style.css">
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

<div class="app-header" style="display: flex; justify-content: space-between; align-items: center; padding: 18px 30px; flex-wrap: wrap; gap: 15px;">
    <div style="display: flex; align-items: center; gap: 12px;">
        <div style="width: 45px; height: 45px; background: rgba(255,255,255,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
            <i class="fa-solid fa-school-flag"></i>
        </div>
        <div>
            <h2 style="margin: 0; font-size: 18px; font-weight: 700;">Aplikasi Pengaduan Sarana Sekolah</h2>
            <p style="margin: 2px 0 0 0; font-size: 12px; opacity: 0.85;">Sistem Pengaduan dan Aspirasi Fasilitas Berbasis Web</p>
        </div>
    </div>
    
    <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
        <a href="/pengaduan_sekolah/index.php" class="btn-3d" style="padding: 8px 14px; font-size: 12px; background: rgba(255,255,255,0.15); box-shadow: none;"><i class="fa-solid fa-house"></i> Portal Siswa</a>
        <?php if (isset($_SESSION['user']) && $_SESSION['user']['role'] == 'admin'): ?>
            <a href="/pengaduan_sekolah/admin.php" class="btn-3d" style="padding: 8px 14px; font-size: 12px; background: #4f46e5;"><i class="fa-solid fa-user-shield"></i> Panel Admin</a>
            <span style="font-weight: 600; font-size: 13px; background: rgba(255,255,255,0.1); padding: 6px 12px; border-radius: 8px;"><i class="fa-solid fa-user"></i> <?= htmlspecialchars($_SESSION['user']['username']) ?></span>
            <a href="/pengaduan_sekolah/logout.php" class="btn-3d" style="background: #ef4444; padding: 8px 14px; font-size: 12px; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.3);"><i class="fa-solid fa-right-from-bracket"></i> Logout</a>
        <?php else: ?>
            <a href="/pengaduan_sekolah/login.php" class="btn-3d" style="background: #4f46e5; padding: 8px 14px; font-size: 12px;"><i class="fa-solid fa-lock"></i> Login Admin</a>
        <?php endif; ?>
    </div>
</div>