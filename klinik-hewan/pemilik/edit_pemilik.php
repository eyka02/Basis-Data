<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

$data = mysqli_fetch_assoc(

mysqli_query(
$conn,
"SELECT *
FROM pemilik
WHERE id_pemilik='$id'"
)

);

if(isset($_POST['update']))
{

$nama = $_POST['nama'];
$nohp = $_POST['no_hp'];
$email = $_POST['email'];
$alamat = $_POST['alamat'];

mysqli_query(
$conn,
"UPDATE pemilik
SET
nama='$nama',
no_hp='$nohp',
email='$email',
alamat='$alamat'
WHERE id_pemilik='$id'"
);

header("Location: pemilik.php");

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Pemilik</title>

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

<h2>Edit Pemilik</h2>

<form method="POST">

<div class="form-group">

<label>Nama</label>

<input
type="text"
name="nama"
value="<?= $data['nama']; ?>"
class="form-control">

</div>

<div class="form-group">

<label>No HP</label>

<input
type="text"
name="no_hp"
value="<?= $data['no_hp']; ?>"
class="form-control">

</div>

<div class="form-group">

<label>Email</label>

<input
type="email"
name="email"
value="<?= $data['email']; ?>"
class="form-control">

</div>

<div class="form-group">

<label>Alamat</label>

<textarea
name="alamat"
class="form-control"><?= $data['alamat']; ?></textarea>

</div>

<button
name="update"
class="btn btn-warning">

Update

</button>

<a href="pemilik.php"
class="btn btn-primary">

Kembali
</a>

</form>

</div>

</body>
</html>