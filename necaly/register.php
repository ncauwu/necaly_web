<?php
include 'config/koneksi.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username']);
    $email    = trim($_POST['email']);
    $password = $_POST['password'];

    // enkripsi password
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = mysqli_prepare($koneksi, "INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
    mysqli_stmt_bind_param($stmt, "sss", $username, $email, $password_hash);

    if (mysqli_stmt_execute($stmt)) {
        // berhasil → arahkan ke login
        header("Location: login.php?daftar=sukses");
        exit;
    } else {
        if (mysqli_errno($koneksi) == 1062) {
            $error = "Username atau email sudah terdaftar!";
        } else {
            $error = "Gagal daftar: " . mysqli_error($koneksi);
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Register - necaly</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
    <div class="login">
        <h1 class="title">necaly</h1>

        <form class="card" method="POST" action="">
            <label class="label">Username:</label>
            <input type="text" name="username" class="input" required />

            <label class="label">E-mail:</label>
            <input type="email" name="email" class="input" required />

            <label class="label">Password:</label>
            <input type="password" name="password" class="input" required />

            <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>

            <button type="submit" class="btn">Daftar</button>
        </form>

        <p class="register-link">
            Sudah punya akun? <a href="login.php">Login di sini</a>
        </p>
    </div>
</body>
</html>