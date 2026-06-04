<?php

include "../config/session.php";
include "../config/database.php";

$search = '';

if(isset($_GET['search']))
{
    $search = mysqli_real_escape_string(
        $conn,
        $_GET['search']
    );
}

$query = mysqli_query(
$conn,
"SELECT
h.*,
p.nama AS nama_pemilik
FROM hewan h
JOIN pemilik p
ON h.id_pemilik=p.id_pemilik
WHERE h.nama_hewan
LIKE '%$search%'
ORDER BY h.id_hewan DESC"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Data Hewan</title>

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

<h2>Data Hewan</h2>

<br>

<a href="tambah_hewan.php"
class="btn btn-success">
Tambah Hewan
</a>

<br><br>

<form method="GET">

<input
type="text"
name="search"
placeholder="Cari Hewan..."
class="form-control">

<br>

<button class="btn btn-primary">
Cari
</button>

</form>

<br>

<table class="table">

<tr>

<th>Foto</th>
<th>Nama</th>
<th>Jenis</th>
<th>Ras</th>
<th>Pemilik</th>
<th>Status Vaksin</th>
<th>Aksi</th>

</tr>

<?php while($row=mysqli_fetch_assoc($query)){ ?>

<tr>

<td>

<img
src="../assets/img/uploads/hewan/<?= $row['foto']; ?>"
width="70">

</td>

<td><?= $row['nama_hewan']; ?></td>

<td><?= $row['jenis']; ?></td>

<td><?= $row['ras']; ?></td>

<td><?= $row['nama_pemilik']; ?></td>

<td><?= $row['status_vaksin']; ?></td>

<td>

<a
href="detail_hewan.php?id=<?= $row['id_hewan']; ?>"
class="btn btn-primary">
Detail
</a>

<a
href="edit_hewan.php?id=<?= $row['id_hewan']; ?>"
class="btn btn-warning">
Edit
</a>

<a
href="delete_hewan.php?id=<?= $row['id_hewan']; ?>"
class="btn btn-danger"
onclick="return confirm('Hapus Data?')">
Hapus
</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>