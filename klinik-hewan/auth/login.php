<?php

session_start();
include "../config/database.php";

$error = "";

if(isset($_POST['login']))
{
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = md5($_POST['password']);

    $query = mysqli_query(
        $conn,
        "SELECT * FROM admins
        WHERE username='$username'
        AND password='$password'"
    );

    if(mysqli_num_rows($query) > 0)
    {
        $data = mysqli_fetch_assoc($query);

        $_SESSION['admin'] = $data['id_admin'];

        header("Location: ../dashboard/dashboard.php");
        exit;
    }
    else
    {
        $error = "Username atau password salah!";
    }
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Login Admin - Klinik Hewan</title>

<link rel="stylesheet" href="../assets/css/login.css">


</head>

<body>

<div class="login-wrapper">

    <div class="login-card">

        <div class="login-header">
            <h1>🐾 Klinik Hewan</h1>
            <p>Admin Panel Login</p>
        </div>

        <?php if($error != ""){ ?>
            <div class="alert-error">
                <?= $error; ?>
            </div>
        <?php } ?>

        <form method="POST" class="login-form">

            <label>Username</label>
            <input type="text" name="username" placeholder="Masukkan username" required>

            <label>Password</label>
            <input type="password" name="password" placeholder="Masukkan password" required>

            <button type="submit" name="login">
                Masuk
            </button>

        </form>

        <div class="login-footer">
            © <?= date('Y'); ?> Klinik Hewan Admin Panel
        </div>

    </div>

</div>

</body>

</html>