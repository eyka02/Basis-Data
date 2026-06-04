<?php

include "../config/session.php";
include "../config/database.php";

$query = mysqli_query(
$conn,
"SELECT

pb.id_pembayaran,
pb.invoice,
pb.total,
pb.metode,
pb.created_at,

h.nama_hewan,
p.nama AS pemilik

FROM pembayaran pb

JOIN kunjungan k
ON pb.id_kunjungan = k.id_kunjungan

JOIN hewan h
ON k.id_hewan = h.id_hewan

JOIN pemilik p
ON h.id_pemilik = p.id_pemilik

ORDER BY pb.id_pembayaran DESC"
);

?>

<!DOCTYPE html>
<html>

<head>

<title>Data Pembayaran</title>

<link rel="stylesheet" href="../assets/css/global.css">
<link rel="stylesheet" href="../assets/css/sidebar.css">
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/pembayaran.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2 class="page-title">Data Pembayaran</h2>

<a href="tambah_pembayaran.php" class="btn btn-success">
    + Tambah Pembayaran
</a>

<br><br>

<table class="table">

<tr>
<th>Invoice</th>
<th>Tanggal</th>
<th>Pemilik</th>
<th>Hewan</th>
<th>Total</th>
<th>Metode</th>
<th>Aksi</th>
</tr>

<?php if(mysqli_num_rows($query) > 0){ ?>

<?php while($row=mysqli_fetch_assoc($query)){ ?>

<tr>

<td><?= $row['invoice']; ?></td>

<td>
    <?= $row['created_at'] ?? '-' ?>
</td>

<td><?= $row['pemilik']; ?></td>

<td><?= $row['nama_hewan']; ?></td>

<td>
    Rp <?= number_format($row['total']); ?>
</td>

<td><?= $row['metode']; ?></td>

<td>

<a href="detail_pembayaran.php?id=<?= $row['id_pembayaran']; ?>"
class="btn btn-primary">

Detail

</a>

<a href="cetak_invoice.php?id=<?= $row['id_pembayaran']; ?>"
target="_blank"
class="btn btn-success">

Invoice

</a>

</td>

</tr>

<?php } ?>

<?php } else { ?>

<tr>
<td colspan="7" style="text-align:center;">
Tidak ada data pembayaran
</td>
</tr>

<?php } ?>

</table>

</div>

</body>

</html>