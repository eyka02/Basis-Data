<?php

include "../config/session.php";
include "../config/database.php";

if(isset($_POST['simpan']))
{

$nama = $_POST['nama'];
$nohp = $_POST['no_hp'];
$email = $_POST['email'];
$alamat = $_POST['alamat'];

mysqli_query(
$conn,
"INSERT INTO pemilik
(
nama,
no_hp,
email,
alamat,
tanggal_registrasi
)
VALUES
(
'$nama',
'$nohp',
'$email',
'$alamat',
CURDATE()
)"
);

header("Location: pemilik.php");

}
?>

<!DOCTYPE html>
<html>
<head>

<title>Tambah Pemilik</title>

<link rel="stylesheet"
href="../assets/css/global.css">

<link rel="stylesheet"
href="../assets/css/sidebar.css">

<link rel="stylesheet"
href="../assets/css/header.css">

<link rel="stylesheet"
href="../assets/css/pemilik.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2>Tambah Pemilik</h2>

<form method="POST">

<div class="form-group">

<label>Nama</label>

<input
type="text"
name="nama"
class="form-control"
required>

</div>

<div class="form-group">

<label>No HP</label>

<input
type="text"
name="no_hp"
class="form-control">

</div>

<div class="form-group">

<label>Email</label>

<input
type="email"
name="email"
class="form-control">

</div>

<div class="form-group">

<label>Alamat</label>

<textarea
name="alamat"
class="form-control"></textarea>

</div>

<button
name="simpan"
class="btn btn-success">

Simpan

</button>

<a href="pemilik.php"
class="btn btn-primary">

Kembali
</a>

</form>

</div>

</body>
</html>