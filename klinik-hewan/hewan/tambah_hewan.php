<?php

include "../config/session.php";
include "../config/database.php";

$pemilik = mysqli_query(
$conn,
"SELECT * FROM pemilik
ORDER BY nama ASC"
);

if(isset($_POST['simpan']))
{

$foto = $_FILES['foto']['name'];

$tmp = $_FILES['foto']['tmp_name'];

move_uploaded_file(
$tmp,
"../assets/img/uploads/hewan/".$foto
);

$id_pemilik = $_POST['id_pemilik'];

$nama_hewan = $_POST['nama_hewan'];

$jenis = $_POST['jenis'];

$ras = $_POST['ras'];

$tanggal_lahir = $_POST['tanggal_lahir'];

$berat = $_POST['berat'];

$warna = $_POST['warna'];

$jenis_kelamin = $_POST['jenis_kelamin'];

$alergi = $_POST['alergi'];

$status_vaksin = $_POST['status_vaksin'];

mysqli_query(
$conn,
"INSERT INTO hewan
(
id_pemilik,
foto,
nama_hewan,
jenis,
ras,
tanggal_lahir,
berat,
warna,
jenis_kelamin,
alergi,
status_vaksin
)
VALUES
(
'$id_pemilik',
'$foto',
'$nama_hewan',
'$jenis',
'$ras',
'$tanggal_lahir',
'$berat',
'$warna',
'$jenis_kelamin',
'$alergi',
'$status_vaksin'
)"
);

header("Location: hewan.php");

}

?>

<!DOCTYPE html>
<html>
<head>

<title>Tambah Hewan</title>

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

<h2>Tambah Hewan</h2>

<form
method="POST"
enctype="multipart/form-data">

<div class="form-group">
<label>Foto</label>
<input
type="file"
name="foto"
class="form-control">
</div>

<div class="form-group">
<label>Pemilik</label>

<select
name="id_pemilik"
class="form-control">

<?php while($p=mysqli_fetch_assoc($pemilik)){ ?>

<option
value="<?= $p['id_pemilik']; ?>">

<?= $p['nama']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="form-group">
<label>Nama Hewan</label>
<input type="text"
name="nama_hewan"
class="form-control">
</div>

<div class="form-group">
<label>Jenis</label>
<input type="text"
name="jenis"
class="form-control">
</div>

<div class="form-group">
<label>Ras</label>
<input type="text"
name="ras"
class="form-control">
</div>

<div class="form-group">
<label>Tanggal Lahir</label>
<input type="date"
name="tanggal_lahir"
class="form-control">
</div>

<div class="form-group">
<label>Berat</label>
<input type="number"
step="0.01"
name="berat"
class="form-control">
</div>

<div class="form-group">
<label>Warna</label>
<input type="text"
name="warna"
class="form-control">
</div>

<div class="form-group">
<label>Jenis Kelamin</label>

<select
name="jenis_kelamin"
class="form-control">

<option>Jantan</option>
<option>Betina</option>

</select>

</div>

<div class="form-group">
<label>Alergi</label>
<textarea
name="alergi"
class="form-control"></textarea>
</div>

<div class="form-group">
<label>Status Vaksin</label>

<select
name="status_vaksin"
class="form-control">

<option>Lengkap</option>
<option>Belum Lengkap</option>
<option>Belum Pernah</option>

</select>

</div>

<button
name="simpan"
class="btn btn-success">

Simpan

</button>

<a href="hewan.php"
class="btn btn-primary">

Kembali
</a>

</form>

</div>

</body>
</html>