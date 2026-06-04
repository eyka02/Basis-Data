<?php

include "../config/session.php";
include "../config/database.php";

if(isset($_POST['simpan']))
{

$nama = $_POST['nama_obat'];

$kategori = $_POST['kategori'];

$stok = $_POST['stok'];

$harga = $_POST['harga'];

$supplier = $_POST['supplier'];

if($stok<=0)
{
$status='Habis';
}
elseif($stok<=5)
{
$status='Kritis';
}
elseif($stok<=10)
{
$status='Menipis';
}
else
{
$status='Aman';
}

mysqli_query(
$conn,
"INSERT INTO obat
(
nama_obat,
kategori,
stok,
harga,
supplier,
status_stok
)
VALUES
(
'$nama',
'$kategori',
'$stok',
'$harga',
'$supplier',
'$status'
)"
);

header("Location: obat.php");

}
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

<form method="POST">

<label>Nama Obat</label>
<input
type="text"
name="nama_obat"
class="form-control">

<label>Kategori</label>
<input
type="text"
name="kategori"
class="form-control">

<label>Stok</label>
<input
type="number"
name="stok"
class="form-control">

<label>Harga</label>
<input
type="number"
name="harga"
class="form-control">

<label>Supplier</label>
<input
type="text"
name="supplier"
class="form-control">

<br>

<button
name="simpan"
class="btn btn-success">

Simpan

</button>

<a href="obat.php"
class="btn btn-primary">

Kembali
</a>

</form>