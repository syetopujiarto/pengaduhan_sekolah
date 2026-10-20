<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once 'includes/functions.php';

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Cek Login Admin / Users
    $query = "SELECT * FROM users WHERE username = '$username' AND password = '$password'";
    $result = mysqli_query($conn, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $_SESSION['user'] = [
            'id' => $user['id_user'],
            'username' => $user['username'],
            'role' => $user['role']
        ];

        header("Location: admin.php");
        exit;
    } else {
        $error = "Username atau Password salah!";
    }
}

$page_title = "Login Aplikasi Pengaduan Sarana";
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $page_title ?></title>
    <link rel="stylesheet" href="/pengaduan_sekolah/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body style="display: flex; justify-content: center; align-items: center; min-height: 100vh; margin: 0; padding: 20px;">

    <div class="card-3d" style="width: 100%; max-width: 420px; text-align: center; background: rgba(255, 255, 255, 0.96); backdrop-filter: blur(16px); padding: 40px 30px;">
        
        <!-- Logo / Icon Top Header -->
        <div style="width: 60px; height: 60px; background: linear-gradient(135deg, #6366f1 0%, #4338ca 100%); color: white; border-radius: 18px; display: flex; align-items: center; justify-content: center; margin: 0 auto 20px auto; font-size: 26px; box-shadow: 0 8px 20px rgba(79, 70, 229, 0.35);">
            <i class="fa-solid fa-user-shield"></i>
        </div>

        <h2 style="margin: 0 0 6px 0; font-size: 22px; color: #0f172a; font-weight: 800;">Login Aplikasi</h2>
        <p style="margin: 0 0 25px 0; color: #64748b; font-size: 13px;">Masuk sebagai Admin untuk mengelola aspirasi sarana sekolah.</p>

        <?php if (!empty($error)): ?>
            <div style="background: #fee2e2; color: #991b1b; padding: 12px 14px; border-radius: 10px; font-size: 13px; font-weight: 700; margin-bottom: 20px; display: flex; align-items: center; justify-content: center; gap: 8px;">
                <i class="fa-solid fa-triangle-exclamation"></i> <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="POST">
            <div style="text-align: left; margin-bottom: 18px;">
                <label style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; display: block; text-transform: uppercase;">USERNAME / NIS:</label>
                <div style="position: relative;">
                    <input type="text" name="username" class="input-3d" placeholder="Masukkan Username / NIS" required style="padding-left: 40px;">
                    <i class="fa-solid fa-user" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                </div>
            </div>

            <div style="text-align: left; margin-bottom: 25px;">
                <label style="font-size: 11px; font-weight: 700; color: #475569; margin-bottom: 6px; display: block; text-transform: uppercase;">PASSWORD:</label>
                <div style="position: relative;">
                    <input type="password" name="password" class="input-3d" placeholder="Masukkan Password" required style="padding-left: 40px;">
                    <i class="fa-solid fa-lock" style="position: absolute; left: 14px; top: 50%; transform: translateY(-50%); color: #94a3b8; font-size: 14px;"></i>
                </div>
            </div>

            <button type="submit" name="login" class="btn-3d" style="width: 100%; padding: 13px; font-size: 14px; font-weight: 700; margin-bottom: 20px;">
                <i class="fa-solid fa-right-to-bracket"></i> Masuk Sekarang
            </button>
        </form>

        <a href="index.php" style="color: #4f46e5; text-decoration: none; font-size: 13px; font-weight: 700; display: inline-flex; align-items: center; gap: 6px; transition: all 0.2s ease;">
            <i class="fa-solid fa-arrow-left"></i> Kembali ke Beranda
        </a>
    </div>

</body>
</html>
