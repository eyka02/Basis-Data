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

p.nama AS nama_pemilik,
p.no_hp,
p.alamat

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
    die("Data invoice tidak ditemukan");
}

?>

<!DOCTYPE html>
<html>

<head>

<title>Cetak Invoice</title>

<link rel="stylesheet" href="../assets/css/pembayaran.css">

</head>

<body onload="window.print()">

<div class="invoice">

<h1>KLINIK HEWAN</h1>

<hr>

<table>

<tr>
<td class="label">Invoice</td>
<td>:</td>
<td><?= $data['invoice']; ?></td>
</tr>

<tr>
<td class="label">Tanggal</td>
<td>:</td>
<td><?= $data['created_at']; ?></td>
</tr>

<tr>
<td class="label">Pemilik</td>
<td>:</td>
<td><?= $data['nama_pemilik']; ?></td>
</tr>

<tr>
<td class="label">No HP</td>
<td>:</td>
<td><?= $data['no_hp']; ?></td>
</tr>

<tr>
<td class="label">Alamat</td>
<td>:</td>
<td><?= $data['alamat']; ?></td>
</tr>

<tr>
<td class="label">Hewan</td>
<td>:</td>
<td><?= $data['nama_hewan']; ?></td>
</tr>

<tr>
<td class="label">Metode</td>
<td>:</td>
<td><?= $data['metode']; ?></td>
</tr>

</table>

<hr>

<p class="total">
Total Pembayaran: Rp <?= number_format($data['total']); ?>
</p>

<div class="footer">

<p>Terima kasih telah menggunakan layanan Klinik Hewan</p>

</div>

</div>

</body>

</html>