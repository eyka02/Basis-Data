```php
<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

$data = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT
            k.*,
            h.nama_hewan,
            h.jenis,
            h.ras,
            p.nama AS pemilik
        FROM kunjungan k
        JOIN hewan h
            ON k.id_hewan=h.id_hewan
        JOIN pemilik p
            ON h.id_pemilik=p.id_pemilik
        WHERE k.id_kunjungan='$id'"
    )
);

$obat = mysqli_query(
    $conn,
    "SELECT
        o.nama_obat,
        d.jumlah,
        d.subtotal
    FROM detail_obat_kunjungan d
    JOIN obat o
        ON d.id_obat=o.id_obat
    WHERE d.id_kunjungan='$id'"
);

$vaksin = mysqli_query(
    $conn,
    "SELECT
        v.nama_vaksin,
        d.jumlah,
        d.subtotal
    FROM detail_vaksin_kunjungan d
    JOIN vaksin v
        ON d.id_vaksin=v.id_vaksin
    WHERE d.id_kunjungan='$id'"
);
?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Detail Rekam Medis</title>

<link rel="stylesheet" href="../assets/css/global.css">
<link rel="stylesheet" href="../assets/css/sidebar.css">
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/kunjungan.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="page-header">

<h2>📋 Detail Rekam Medis</h2>

<div>

<a href="kunjungan.php" class="btn btn-secondary">
Kembali
</a>

<a href="cetak_rekam_medis.php?id=<?= $data['id_kunjungan']; ?>"
target="_blank"
class="btn btn-success">
Cetak
</a>

</div>

</div>

<!-- INFORMASI PASIEN -->

<div class="medical-card">

<h3>Informasi Pasien</h3>

<div class="info-grid">

<div>
<label>Nama Hewan</label>
<p><?= $data['nama_hewan']; ?></p>
</div>

<div>
<label>Jenis</label>
<p><?= $data['jenis']; ?></p>
</div>

<div>
<label>Ras</label>
<p><?= $data['ras']; ?></p>
</div>

<div>
<label>Pemilik</label>
<p><?= $data['pemilik']; ?></p>
</div>

<div>
<label>Tanggal Kunjungan</label>
<p><?= date('d F Y', strtotime($data['tanggal_kunjungan'])); ?></p>
</div>

<div>
<label>Total Biaya</label>
<p class="price">
Rp <?= number_format($data['total_biaya']); ?>
</p>
</div>

</div>

</div>

<!-- PEMERIKSAAN -->

<div class="medical-card">

<h3>Hasil Pemeriksaan</h3>

<div class="detail-section">

<label>Keluhan</label>

<div class="detail-box">
<?= nl2br($data['keluhan']); ?>
</div>

<label>Diagnosa</label>

<div class="detail-box">
<?= nl2br($data['diagnosa']); ?>
</div>

<label>Tindakan Medis</label>

<div class="detail-box">
<?= nl2br($data['tindakan']); ?>
</div>

<label>Catatan Dokter</label>

<div class="detail-box">
<?= nl2br($data['catatan']); ?>
</div>

</div>

</div>

<!-- OBAT -->

<div class="medical-card">

<h3>Obat Digunakan</h3>

<table class="table">

<thead>

<tr>
<th>Nama Obat</th>
<th>Jumlah</th>
<th>Subtotal</th>
</tr>

</thead>

<tbody>

<?php while($o=mysqli_fetch_assoc($obat)){ ?>

<tr>

<td><?= $o['nama_obat']; ?></td>

<td><?= $o['jumlah']; ?></td>

<td>
Rp <?= number_format($o['subtotal']); ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<!-- VAKSIN -->

<div class="medical-card">

<h3>Vaksin Digunakan</h3>

<table class="table">

<thead>

<tr>
<th>Nama Vaksin</th>
<th>Jumlah</th>
<th>Subtotal</th>
</tr>

</thead>

<tbody>

<?php while($v=mysqli_fetch_assoc($vaksin)){ ?>

<tr>

<td><?= $v['nama_vaksin']; ?></td>

<td><?= $v['jumlah']; ?></td>

<td>
Rp <?= number_format($v['subtotal']); ?>
</td>

</tr>

<?php } ?>

</tbody>

</table>

</div>

<!-- LAMPIRAN -->

<div class="medical-card">

<h3>Lampiran Medis</h3>

<?php if($data['hasil_lab'] != ''){ ?>

<a
href="../assets/img/uploads/lab/<?= $data['hasil_lab']; ?>"
target="_blank"
class="btn btn-primary">

Lihat Hasil Lab

</a>

<?php } ?>

<br><br>

<?php if($data['foto_medis'] != ''){ ?>

<img
src="../assets/img/uploads/medis/<?= $data['foto_medis']; ?>"
class="medical-image">

<?php } else { ?>

<p>Tidak ada foto medis.</p>

<?php } ?>

</div>

</div>

</body>
</html>