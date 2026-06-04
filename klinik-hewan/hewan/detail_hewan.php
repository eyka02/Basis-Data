<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

$query = mysqli_query(
$conn,
"SELECT
h.*,
p.nama,
p.no_hp,
p.email,
p.alamat
FROM hewan h
JOIN pemilik p
ON h.id_pemilik = p.id_pemilik
WHERE h.id_hewan = '$id'"
);

$data = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>

<html>
<head>

<title>Detail Hewan</title>

<link rel="stylesheet"
href="../assets/css/global.css">

<link rel="stylesheet"
href="../assets/css/sidebar.css">

<link rel="stylesheet"
href="../assets/css/header.css">

<link rel="stylesheet"
href="../assets/css/hewan.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2>Detail Hewan</h2>

<br>

<img
src="../assets/img/uploads/hewan/<?= $data['foto']; ?>"
width="250"
style="border-radius:10px;">

<br><br>

<table class="table">

<tr>
<td width="250">Nama Hewan</td>
<td><?= $data['nama_hewan']; ?></td>
</tr>

<tr>
<td>Jenis</td>
<td><?= $data['jenis']; ?></td>
</tr>

<tr>
<td>Ras</td>
<td><?= $data['ras']; ?></td>
</tr>

<tr>
<td>Tanggal Lahir</td>
<td><?= $data['tanggal_lahir']; ?></td>
</tr>

<tr>
<td>Berat</td>
<td><?= $data['berat']; ?> Kg</td>
</tr>

<tr>
<td>Warna</td>
<td><?= $data['warna']; ?></td>
</tr>

<tr>
<td>Jenis Kelamin</td>
<td><?= $data['jenis_kelamin']; ?></td>
</tr>

<tr>
<td>Riwayat Alergi</td>
<td><?= $data['alergi']; ?></td>
</tr>

<tr>
<td>Status Vaksin</td>
<td><?= $data['status_vaksin']; ?></td>
</tr>

</table>

<br>

<h3>Data Pemilik</h3>

<table class="table">

<tr>
<td width="250">Nama Pemilik</td>
<td><?= $data['nama']; ?></td>
</tr>

<tr>
<td>No HP</td>
<td><?= $data['no_hp']; ?></td>
</tr>

<tr>
<td>Email</td>
<td><?= $data['email']; ?></td>
</tr>

<tr>
<td>Alamat</td>
<td><?= $data['alamat']; ?></td>
</tr>

</table>

<br>

<a
href="hewan.php"
class="btn btn-primary">

Kembali

</a>

</div>

</body>
</html>
