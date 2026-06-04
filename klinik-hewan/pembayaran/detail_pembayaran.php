<?php

include "../config/session.php";
include "../config/database.php";

$id = mysqli_real_escape_string($conn, $_GET['id']);

$query = mysqli_query(
$conn,
"SELECT

pb.id_pembayaran,
pb.invoice,
pb.total,
pb.metode,
pb.created_at,

h.nama_hewan,
h.jenis,
h.ras,

p.nama AS nama_pemilik,
p.no_hp,
p.email

FROM pembayaran pb

JOIN kunjungan k
ON pb.id_kunjungan = k.id_kunjungan

JOIN hewan h
ON k.id_hewan = h.id_hewan

JOIN pemilik p
ON h.id_pemilik = p.id_pemilik

WHERE pb.id_pembayaran = '$id'
LIMIT 1"
);

$data = mysqli_fetch_assoc($query);

if(!$data){
    die("Data pembayaran tidak ditemukan");
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Detail Pembayaran</title>

<link rel="stylesheet" href="../assets/css/global.css">
<link rel="stylesheet" href="../assets/css/sidebar.css">
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/pembayaran.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2 class="page-title">Detail Pembayaran</h2>

<div class="detail-card">

<table class="table">

<tr>
<td>Invoice</td>
<td><?= $data['invoice']; ?></td>
</tr>

<tr>
<td>Tanggal</td>
<td><?= $data['created_at']; ?></td>
</tr>

<tr>
<td>Pemilik</td>
<td><?= $data['nama_pemilik']; ?></td>
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
<td>Nama Hewan</td>
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
<td>Metode</td>
<td><?= $data['metode']; ?></td>
</tr>

<tr>
<td>Total</td>
<td>
<strong>Rp <?= number_format($data['total']); ?></strong>
</td>
</tr>

</table>

<br>

<a href="cetak_invoice.php?id=<?= $data['id_pembayaran']; ?>"
class="btn btn-success"
target="_blank">

Cetak Invoice

</a>

<a href="pembayaran.php"
class="btn btn-secondary">

Kembali

</a>

</div>

</div>

</body>

</html>