<?php

include "../config/session.php";
include "../config/database.php";

$query = mysqli_query(
$conn,
"SELECT
k.*,
h.nama_hewan,
p.nama AS nama_pemilik

FROM kunjungan k

JOIN hewan h
ON k.id_hewan=h.id_hewan

JOIN pemilik p
ON h.id_pemilik=p.id_pemilik

ORDER BY k.id_kunjungan DESC"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Riwayat Kunjungan</title>

<link rel="stylesheet"
href="../assets/css/global.css">

<link rel="stylesheet"
href="../assets/css/sidebar.css">

<link rel="stylesheet"
href="../assets/css/header.css">

<link rel="stylesheet"
href="../assets/css/kunjungan.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2>Riwayat Kunjungan</h2>

<br>

<a
href="tambah_kunjungan.php"
class="btn btn-success">

Tambah Kunjungan

</a>

<br><br>

<table class="table">

<tr>

<th>Tanggal</th>
<th>Hewan</th>
<th>Pemilik</th>
<th>Diagnosa</th>
<th>Total</th>
<th>Aksi</th>

</tr>

<?php while($row=mysqli_fetch_assoc($query)){ ?>

<tr>

<td><?= $row['tanggal_kunjungan']; ?></td>

<td><?= $row['nama_hewan']; ?></td>

<td><?= $row['nama_pemilik']; ?></td>

<td><?= $row['diagnosa']; ?></td>

<td>
Rp <?= number_format($row['total_biaya']); ?>
</td>

<td>

<a
href="detail_kunjungan.php?id=<?= $row['id_kunjungan']; ?>"
class="btn btn-primary">

Detail

</a>

<a
href="cetak_rekam_medis.php?id=<?= $row['id_kunjungan']; ?>"
target="_blank"
class="btn btn-success">

Cetak

</a>

<a
href="delete_kunjungan.php?id=<?= $row['id_kunjungan']; ?>"
onclick="return confirm('Yakin hapus data?')"
class="btn btn-danger">

Hapus

</a>

</td>


</tr>

<?php } ?>

</table>

</div>

</body>
</html>