<?php

include "../config/session.php";
include "../config/database.php";

if(!isset($_GET['id']))
{
    die("ID vaksin tidak ditemukan");
}

$id = (int)$_GET['id'];

$query = mysqli_query(
$conn,
"SELECT *
FROM vaksin
WHERE id_vaksin='$id'"
);

$data = mysqli_fetch_assoc($query);

if(!$data)
{
    die("Data vaksin tidak ditemukan");
}

if(isset($_POST['update']))
{

    $nama      = mysqli_real_escape_string($conn,$_POST['nama_vaksin']);
    $jenis     = mysqli_real_escape_string($conn,$_POST['jenis_vaksin']);
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
    "UPDATE vaksin SET

        nama_vaksin='$nama',
        jenis_vaksin='$jenis',
        stok='$stok',
        harga='$harga',
        supplier='$supplier',
        status_stok='$status'

    WHERE id_vaksin='$id'"
    );

    header("Location: vaksin.php?success=update");
    exit;
}

?>

<!DOCTYPE html>
<html lang="id">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Edit Vaksin</title>

<link rel="stylesheet" href="../assets/css/global.css">
<link rel="stylesheet" href="../assets/css/sidebar.css">
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/vaksin.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<div class="page-header">

<h2>Edit Data Vaksin</h2>

</div>

<div class="form-card">

<form method="POST">

<div class="form-group">

<label>Nama Vaksin</label>

<input
type="text"
name="nama_vaksin"
class="form-control"
required
value="<?= htmlspecialchars($data['nama_vaksin']); ?>">

</div>

<div class="form-group">

<label>Jenis Vaksin</label>

<input
type="text"
name="jenis_vaksin"
class="form-control"
required
value="<?= htmlspecialchars($data['jenis_vaksin']); ?>">

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

<div class="button-group">

<button
type="submit"
name="update"
class="btn btn-success">

Update Data

</button>

<a
href="vaksin.php"
class="btn btn-primary">

Batal

</a>

</div>

</form>

</div>

</div>

</body>
</html>