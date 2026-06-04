<?php

include "../config/session.php";
include "../config/database.php";

/*
|--------------------------------------------------------------------------
| TOTAL PENDAPATAN
|--------------------------------------------------------------------------
*/

$totalPendapatan = mysqli_fetch_assoc(
mysqli_query(
$conn,
"SELECT IFNULL(SUM(total),0) AS total
FROM pembayaran"
)
);

/*
|--------------------------------------------------------------------------
| TOTAL KUNJUNGAN
|--------------------------------------------------------------------------
*/

$totalKunjungan = mysqli_fetch_assoc(
mysqli_query(
$conn,
"SELECT COUNT(*) AS total FROM kunjungan"
)
);

/*
|--------------------------------------------------------------------------
| OBAT TERLARIS
|--------------------------------------------------------------------------
*/

$obatTerlaris = mysqli_query(
$conn,
"SELECT
o.nama_obat,
COUNT(d.id_obat) AS jumlah

FROM detail_obat_kunjungan d

JOIN obat o
ON d.id_obat = o.id_obat

GROUP BY d.id_obat, o.nama_obat

ORDER BY jumlah DESC
LIMIT 5"
);

$obatData = [];

while($row = mysqli_fetch_assoc($obatTerlaris)){
    $obatData[] = $row;
}

/*
|--------------------------------------------------------------------------
| VAKSIN TERLARIS
|--------------------------------------------------------------------------
*/

$vaksinTerlaris = mysqli_query(
$conn,
"SELECT
v.nama_vaksin,
COUNT(d.id_vaksin) AS jumlah

FROM detail_vaksin_kunjungan d

JOIN vaksin v
ON d.id_vaksin = v.id_vaksin

GROUP BY d.id_vaksin, v.nama_vaksin

ORDER BY jumlah DESC
LIMIT 5"
);

$vaksinData = [];

while($row = mysqli_fetch_assoc($vaksinTerlaris)){
    $vaksinData[] = $row;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">

<title>Laporan Klinik Hewan</title>

<link rel="stylesheet" href="../assets/css/global.css">
<link rel="stylesheet" href="../assets/css/sidebar.css">
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/laporan.css">
<link rel="stylesheet" href="../assets/css/dashboard.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2 class="page-title">Laporan Klinik Hewan</h2>

<!-- CARD STATISTIK -->
<div class="card-container">

    <div class="card">
        <h3>Total Pendapatan</h3>
        <h1>
            Rp <?= number_format($totalPendapatan['total'] ?? 0); ?>
        </h1>
    </div>

    <div class="card">
        <h3>Total Kunjungan</h3>
        <h1>
            <?= $totalKunjungan['total'] ?? 0; ?>
        </h1>
    </div>

</div>

<br>

<!-- OBAT TERLARIS -->
<div class="detail-card">

<h3>Top 5 Obat Terlaris</h3>

<table class="table">

<tr>
<th>Nama Obat</th>
<th>Total Pemakaian</th>
</tr>

<?php if(count($obatData) > 0){ ?>

<?php foreach($obatData as $o){ ?>

<tr>
<td><?= $o['nama_obat']; ?></td>
<td><?= $o['jumlah']; ?></td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
<td colspan="2" style="text-align:center;">
Tidak ada data
</td>
</tr>

<?php } ?>

</table>

</div>

<br>

<!-- VAKSIN TERLARIS -->
<div class="detail-card">

<h3>Top 5 Vaksin Terlaris</h3>

<table class="table">

<tr>
<th>Nama Vaksin</th>
<th>Total Pemakaian</th>
</tr>

<?php if(count($vaksinData) > 0){ ?>

<?php foreach($vaksinData as $v){ ?>

<tr>
<td><?= $v['nama_vaksin']; ?></td>
<td><?= $v['jumlah']; ?></td>
</tr>

<?php } ?>

<?php } else { ?>

<tr>
<td colspan="2" style="text-align:center;">
Tidak ada data
</td>
</tr>

<?php } ?>

</table>

</div>

</div>

</body>

</html>