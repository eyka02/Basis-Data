<?php

include "../config/session.php";
include "../config/database.php";

$search = '';

if(isset($_GET['search']))
{
    $search = $_GET['search'];
}

$query = mysqli_query(
$conn,
"SELECT *
FROM obat
WHERE nama_obat
LIKE '%$search%'
ORDER BY nama_obat ASC"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Data Obat</title>

<link rel="stylesheet"
href="../assets/css/global.css">

<link rel="stylesheet"
href="../assets/css/sidebar.css">

<link rel="stylesheet"
href="../assets/css/header.css">

<link rel="stylesheet"
href="../assets/css/obat.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2>Data Obat</h2>

<a
href="tambah_obat.php"
class="btn btn-success">

Tambah Obat

</a>

<br><br>

<form>

<input
type="text"
name="search"
class="form-control"
placeholder="Cari Obat">

<br>

<button class="btn btn-primary">
Cari
</button>

</form>

<br>

<table class="table">

<tr>

<th>Nama</th>
<th>Kategori</th>
<th>Stok</th>
<th>Harga</th>
<th>Status</th>
<th>Aksi</th>

</tr>

<?php while($row=mysqli_fetch_assoc($query)){ ?>

<tr>

<td><?= $row['nama_obat']; ?></td>

<td><?= $row['kategori']; ?></td>

<td><?= $row['stok']; ?></td>

<td>
Rp <?= number_format($row['harga']); ?>
</td>

<td>

<?php

if($row['status_stok']=='Aman')
{
echo '<span class="badge-success">Aman</span>';
}
elseif($row['status_stok']=='Menipis')
{
echo '<span class="badge-warning">Menipis</span>';
}
elseif($row['status_stok']=='Kritis')
{
echo '<span class="badge-danger">Kritis</span>';
}
else
{
echo '<span class="badge-dark">Habis</span>';
}

?>

</td>

<td>

<a
href="edit_obat.php?id=<?= $row['id_obat']; ?>"
class="btn btn-warning">

Edit

</a>

<a
href="delete_obat.php?id=<?= $row['id_obat']; ?>"
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