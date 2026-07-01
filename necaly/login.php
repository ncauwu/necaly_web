<?php
session_start();
include 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    $stmt = mysqli_prepare($koneksi, "SELECT id, username, password FROM users WHERE email = ?");
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id']  = $user['id'];
        $_SESSION['username'] = $user['username'];
        header("Location: dashboard.php");
        exit;
    } else {
        $error = "Email atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Login - necaly</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="login">
        <h1 class="title">necaly</h1>

        <form class="card" method="POST" action="">
            <label class="label">E-mail :</label>
            <input type="email" name="email" class="input" required />

            <label class="label">Password :</label>
            <input type="password" name="password" class="input" required />

            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

            <button type="submit" class="btn">Login</button>
        </form>

        <p class="register-link">
            Belum punya akun? <a href="register.php">Daftar di sini</a>
        </p>
    </div>
</body>
</html>