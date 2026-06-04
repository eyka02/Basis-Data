<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

$pemilik = mysqli_fetch_assoc(

mysqli_query(
$conn,
"SELECT *
FROM pemilik
WHERE id_pemilik='$id'"
)

);

$hewan = mysqli_query(
$conn,
"SELECT *
FROM hewan
WHERE id_pemilik='$id'"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Detail Pemilik</title>

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

<h2>Detail Pemilik</h2>

<br>

<table class="table">

<tr>
<td>Nama</td>
<td><?= $pemilik['nama']; ?></td>
</tr>

<tr>
<td>No HP</td>
<td><?= $pemilik['no_hp']; ?></td>
</tr>

<tr>
<td>Email</td>
<td><?= $pemilik['email']; ?></td>
</tr>

<tr>
<td>Alamat</td>
<td><?= $pemilik['alamat']; ?></td>
</tr>

</table>

<br>

<h3>Data Hewan</h3>

<table class="table">

<tr>

<th>Nama Hewan</th>
<th>Jenis</th>
<th>Ras</th>

</tr>

<?php while($h = mysqli_fetch_assoc($hewan)){ ?>

<tr>

<td><?= $h['nama_hewan']; ?></td>
<td><?= $h['jenis']; ?></td>
<td><?= $h['ras']; ?></td>

</tr>

<?php } ?>

</table>

<br>

<a href="pemilik.php"
class="btn btn-primary">

Kembali
</a>

</div>



</body>
</html>