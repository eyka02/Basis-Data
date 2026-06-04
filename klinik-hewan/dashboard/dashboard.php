<?php

include "../config/session.php";
include "../config/database.php";

/*
|--------------------------------------------------------------------------
| STATISTIK
|--------------------------------------------------------------------------
*/

$totalPemilik = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM pemilik"
    )
);

$totalHewan = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM hewan"
    )
);

$totalObat = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM obat"
    )
);

$totalVaksin = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT * FROM vaksin"
    )
);

$totalKunjunganHariIni = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT *
        FROM kunjungan
        WHERE DATE(tanggal_kunjungan)=CURDATE()"
    )
);

$totalAppointmentHariIni = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT *
        FROM appointment
        WHERE tanggal=CURDATE()"
    )
);

$stokMenipis = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT *
        FROM obat
        WHERE status_stok IN
        (
            'Menipis',
            'Kritis',
            'Habis'
        )"
    )
);

$pendapatan = mysqli_fetch_assoc(
    mysqli_query(
        $conn,
        "SELECT
        IFNULL(SUM(total),0) AS total
        FROM pembayaran
        WHERE MONTH(tanggal_bayar)=MONTH(CURDATE())
        AND YEAR(tanggal_bayar)=YEAR(CURDATE())"
    )
);

/*
|--------------------------------------------------------------------------
| APPOINTMENT HARI INI
|--------------------------------------------------------------------------
*/

$jadwalHariIni = mysqli_query(
    $conn,
    "SELECT

    a.*,
    h.nama_hewan

    FROM appointment a

    JOIN hewan h
    ON a.id_hewan=h.id_hewan

    WHERE a.tanggal=CURDATE()

    ORDER BY a.jam ASC"
);

/*
|--------------------------------------------------------------------------
| STOK KRITIS
|--------------------------------------------------------------------------
*/

$stokAlert = mysqli_query(
    $conn,
    "SELECT
    nama_obat,
    stok,
    status_stok

    FROM obat

    WHERE status_stok IN
    (
        'Menipis',
        'Kritis',
        'Habis'
    )

    ORDER BY stok ASC"
);

/*
|--------------------------------------------------------------------------
| AKTIVITAS
|--------------------------------------------------------------------------
*/

$aktivitas = mysqli_query(
    $conn,
    "SELECT *
    FROM activity_logs
    ORDER BY id_log DESC
    LIMIT 10"
);

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Dashboard</title>

<link rel="stylesheet"
href="../assets/css/global.css">

<link rel="stylesheet"
href="../assets/css/sidebar.css">

<link rel="stylesheet"
href="../assets/css/header.css">

<link rel="stylesheet"
href="../assets/css/dashboard.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2>Dashboard Admin</h2>

<!-- CARD STATISTIK -->

<div class="card-container">

<div class="card">
<h3>Total Pemilik</h3>
<h1><?= $totalPemilik; ?></h1>
</div>

<div class="card">
<h3>Total Hewan</h3>
<h1><?= $totalHewan; ?></h1>
</div>

<div class="card">
<h3>Kunjungan Hari Ini</h3>
<h1><?= $totalKunjunganHariIni; ?></h1>
</div>

<div class="card">
<h3>Total Obat</h3>
<h1><?= $totalObat; ?></h1>
</div>

<div class="card">
<h3>Total Vaksin</h3>
<h1><?= $totalVaksin; ?></h1>
</div>

<div class="card">
<h3>Appointment Hari Ini</h3>
<h1><?= $totalAppointmentHariIni; ?></h1>
</div>

<div class="card">
<h3>Stok Menipis</h3>
<h1><?= $stokMenipis; ?></h1>
</div>

<div class="card">
<h3>Pendapatan Bulan Ini</h3>
<h1>
Rp <?= number_format($pendapatan['total']); ?>
</h1>
</div>

</div>

<br>

<!-- ALERT STOK -->

<div class="detail-card">

<h3>Alert Stok Obat</h3>

<table class="table">

<tr>
<th>Nama Obat</th>
<th>Stok</th>
<th>Status</th>
</tr>

<?php while($s=mysqli_fetch_assoc($stokAlert)){ ?>

<tr>

<td><?= $s['nama_obat']; ?></td>

<td><?= $s['stok']; ?></td>

<td>

<?php

switch($s['status_stok'])
{
    case 'Menipis':
        echo '<span class="badge-warning">Menipis</span>';
    break;

    case 'Kritis':
        echo '<span class="badge-danger">Kritis</span>';
    break;

    case 'Habis':
        echo '<span class="badge-dark">Habis</span>';
    break;
}

?>

</td>

</tr>

<?php } ?>

</table>

</div>

<br>

<!-- APPOINTMENT -->

<div class="detail-card">

<h3>Jadwal Appointment Hari Ini</h3>

<table class="table">

<tr>
<th>Jam</th>
<th>Nama Hewan</th>
<th>Status</th>
</tr>

<?php while($j=mysqli_fetch_assoc($jadwalHariIni)){ ?>

<tr>

<td><?= $j['jam']; ?></td>

<td><?= $j['nama_hewan']; ?></td>

<td><?= $j['status']; ?></td>

</tr>

<?php } ?>

</table>

</div>

<br>

<!-- AKTIVITAS -->

<div class="detail-card">

<h3>Aktivitas Terbaru</h3>

<ul>

<?php while($a=mysqli_fetch_assoc($aktivitas)){ ?>

<li>

<?= $a['aktivitas']; ?>

-

<?= date(
'd-m-Y H:i',
strtotime($a['created_at'])
); ?>

</li>

<?php } ?>

</ul>

</div>

</div>

</body>
</html>