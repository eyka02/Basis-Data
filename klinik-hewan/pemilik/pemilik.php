<?php

include "../config/session.php";
include "../config/database.php";

$keyword = '';

if(isset($_GET['search'])){
    $keyword = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );
}

$query = mysqli_query(
    $conn,
    "SELECT *
    FROM pemilik
    WHERE nama LIKE '%$keyword%'
    ORDER BY id_pemilik DESC"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Data Pemilik</title>

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

<h2>Data Pemilik</h2>

<br>

<a href="tambah_pemilik.php"
class="btn btn-success">
Tambah Pemilik
</a>

<br><br>

<form method="GET">

<input
type="text"
name="search"
placeholder="Cari Nama Pemilik"
class="form-control">

<br>

<button class="btn btn-primary">
Cari
</button>

</form>

<br>

<table class="table">

<tr>

<th>ID</th>
<th>Nama</th>
<th>No HP</th>
<th>Email</th>
<th>Registrasi</th>
<th>Aksi</th>

</tr>

<?php while($row = mysqli_fetch_assoc($query)){ ?>

<tr>

<td><?= $row['id_pemilik']; ?></td>

<td><?= $row['nama']; ?></td>

<td><?= $row['no_hp']; ?></td>

<td><?= $row['email']; ?></td>

<td><?= $row['tanggal_registrasi']; ?></td>

<td>

<a
href="detail_pemilik.php?id=<?= $row['id_pemilik']; ?>"
class="btn btn-primary">
Detail
</a>

<a
href="edit_pemilik.php?id=<?= $row['id_pemilik']; ?>"
class="btn btn-warning">
Edit
</a>

<a
href="delete_pemilik.php?id=<?= $row['id_pemilik']; ?>"
class="btn btn-danger"
onclick="return confirm('Hapus data?')">
Hapus
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>