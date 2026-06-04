<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

$query = mysqli_query(
$conn,
"SELECT

a.*,

h.nama_hewan,
h.jenis,
h.ras,

p.nama,
p.no_hp,
p.email,
p.alamat

FROM appointment a

JOIN hewan h
ON a.id_hewan = h.id_hewan

JOIN pemilik p
ON h.id_pemilik = p.id_pemilik

WHERE a.id_appointment = '$id'"
);

$data = mysqli_fetch_assoc($query);

?>

<!DOCTYPE html>
<html>
<head>

<title>Detail Appointment</title>

<link rel="stylesheet"
href="../assets/css/global.css">

<link rel="stylesheet"
href="../assets/css/sidebar.css">

<link rel="stylesheet"
href="../assets/css/header.css">

<link rel="stylesheet"
href="../assets/css/appointment.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2 class="page-title">
Detail Appointment
</h2>

<div class="detail-card">

<h3>Data Hewan</h3>

<table class="table-detail">

<tr>
<td>Nama Hewan</td>
<td>: <?= $data['nama_hewan']; ?></td>
</tr>

<tr>
<td>Jenis</td>
<td>: <?= $data['jenis']; ?></td>
</tr>

<tr>
<td>Ras</td>
<td>: <?= $data['ras']; ?></td>
</tr>

</table>

</div>

<br>

<div class="detail-card">

<h3>Data Pemilik</h3>

<table class="table-detail">

<tr>
<td>Nama</td>
<td>: <?= $data['nama']; ?></td>
</tr>

<tr>
<td>No HP</td>
<td>: <?= $data['no_hp']; ?></td>
</tr>

<tr>
<td>Email</td>
<td>: <?= $data['email']; ?></td>
</tr>

<tr>
<td>Alamat</td>
<td>: <?= $data['alamat']; ?></td>
</tr>

</table>

</div>

<br>

<div class="detail-card">

<h3>Data Appointment</h3>

<table class="table-detail">

<tr>
<td>Tanggal</td>
<td>: <?= $data['tanggal']; ?></td>
</tr>

<tr>
<td>Jam</td>
<td>: <?= $data['jam']; ?></td>
</tr>

<tr>
<td>Keluhan</td>
<td>: <?= $data['keluhan']; ?></td>
</tr>

<tr>
<td>Status</td>
<td>: <?= $data['status']; ?></td>
</tr>

</table>

</div>

<br>

<a
href="appointment.php"
class="btn btn-primary">

Kembali

</a>

<a
href="edit_appointment.php?id=<?= $data['id_appointment']; ?>"
class="btn btn-warning">

Edit

</a>

</div>

</body>
</html>

