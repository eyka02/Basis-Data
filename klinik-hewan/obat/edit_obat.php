<?php

include "../config/session.php";
include "../config/database.php";

if(!isset($_GET['id']))
{
    die("ID Obat tidak ditemukan");
}

$id = (int)$_GET['id'];

$query = mysqli_query(
$conn,
"SELECT *
FROM obat
WHERE id_obat='$id'"
);

$data = mysqli_fetch_assoc($query);

if(!$data)
{
    die("Data obat tidak ditemukan");
}

if(isset($_POST['update']))
{

    $nama      = mysqli_real_escape_string($conn,$_POST['nama_obat']);
    $kategori  = mysqli_real_escape_string($conn,$_POST['kategori']);
    $stok      = (int)$_POST['stok'];
    $harga     = (int)$_POST['harga'];
    $supplier  = mysqli_real_escape_string($conn,$_POST['supplier']);

    if($stok <= 0)
    {
        $status = 'Habis';
    }
    elseif($stok <= 5)
    {
        $status = 'Kritis';
    }
    elseif($stok <= 10)
    {
        $status = 'Menipis';
    }
    else
    {
        $status = 'Aman';
    }

    mysqli_query(
    $conn,
    "UPDATE obat SET

        nama_obat='$nama',
        kategori='$kategori',
        stok='$stok',
        harga='$harga',
        supplier='$supplier',
        status_stok='$status'

    WHERE id_obat='$id'"
    );

    header("Location: obat.php?success=update");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Obat</title>

<link rel="stylesheet" href="../assets/css/global.css">
<link rel="stylesheet" href="../assets/css/sidebar.css">
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/obat.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="page-header">

<h2>Edit Data Obat</h2>

<a href="obat.php" class="btn btn-secondary">
Kembali
</a>

</div>

<div class="form-card">

<form method="POST">

<div class="form-group">

<label>Nama Obat</label>

<input
type="text"
name="nama_obat"
class="form-control"
required
value="<?= htmlspecialchars($data['nama_obat']); ?>">

</div>

<div class="form-group">

<label>Kategori</label>

<input
type="text"
name="kategori"
class="form-control"
required
value="<?= htmlspecialchars($data['kategori']); ?>">

</div>

<div class="form-group">

<label>Stok</label>

<input
type="number"
name="stok"
class="form-control"
required
value="<?= $data['stok']; ?>">

</div>

<div class="form-group">

<label>Harga</label>

<input
type="number"
name="harga"
class="form-control"
required
value="<?= $data['harga']; ?>">

</div>

<div class="form-group">

<label>Supplier</label>

<input
type="text"
name="supplier"
class="form-control"
value="<?= htmlspecialchars($data['supplier']); ?>">

</div>

<div class="form-group">

<label>Status Stok Saat Ini</label>

<input
type="text"
class="form-control"
readonly
value="<?= $data['status_stok']; ?>">

</div>

<br>

<button
type="submit"
name="update"
class="btn btn-success">

Update Data

</button>

</form>

</div>

</div>

</body>
</html>