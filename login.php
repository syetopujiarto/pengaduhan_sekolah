<?php
session_start();
require_once 'includes/functions.php';

$error = "";

if (isset($_POST['login'])) {
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    $query = mysqli_query($conn, "SELECT * FROM users WHERE username = '$username' AND password = '$password'");
    if (mysqli_num_rows($query) == 1) {
        $user = mysqli_fetch_assoc($query);
        $_SESSION['user'] = $user;

        if ($user['role'] == 'admin') {
            header("Location: admin.php");
            exit;
        } else {
            header("Location: index.php");
            exit;
        }
    } else {
        $error = "Username atau password salah!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login - Aplikasi Pengaduan Sekolah</title>
    <link rel="stylesheet" href="/pengaduan_sekolah/assets/css/style.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; height: 100vh; margin: 0;">

<div class="container" style="width: 100%; max-width: 400px; padding: 30px;">
    <h2 style="text-align: center; border: none; margin-bottom: 20px;">Login Aplikasi</h2>
    
    <?php if (!empty($error)): ?>
        <div style='background: #fee2e2; color: #991b1b; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-weight: 600; font-size: 13px; text-align: center;'><?= $error ?></div>
    <?php endif; ?>

    <form action="" method="POST">
        <div class="form-group">
            <label>Username / NIS:</label>
            <input type="text" name="username" required placeholder="Masukkan Username atau NIS">
        </div>
        <div class="form-group">
            <label>Password:</label>
            <input type="password" name="password" required placeholder="Masukkan password">
        </div>
        <button type="submit" name="login" style="width: 100%; margin-top: 10px;">Masuk</button>
    </form>
    
    <div style="text-align: center; margin-top: 20px;">
        <a href="index.php" style="font-size: 13px;">&larr; Kembali ke Beranda</a>
    </div>
</div>

</body>
</html>