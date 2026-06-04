<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

$query = mysqli_query(
$conn,
"SELECT *
FROM hewan
WHERE id_hewan='$id'"
);

$data = mysqli_fetch_assoc($query);

$listPemilik = mysqli_query(
$conn,
"SELECT *
FROM pemilik
ORDER BY nama ASC"
);

if(isset($_POST['update']))
{

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

$foto = $data['foto'];

if(!empty($_FILES['foto']['name']))
{

$foto = $_FILES['foto']['name'];

$tmp = $_FILES['foto']['tmp_name'];

move_uploaded_file(
$tmp,
"../assets/img/uploads/hewan/".$foto
);

}

mysqli_query(
$conn,
"UPDATE hewan
SET

id_pemilik='$id_pemilik',
foto='$foto',
nama_hewan='$nama_hewan',
jenis='$jenis',
ras='$ras',
tanggal_lahir='$tanggal_lahir',
berat='$berat',
warna='$warna',
jenis_kelamin='$jenis_kelamin',
alergi='$alergi',
status_vaksin='$status_vaksin'

WHERE id_hewan='$id'"
);

header("Location: hewan.php");
exit;

}

?>

<!DOCTYPE html>

<html>
<head>

<title>Edit Hewan</title>

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

<h2>Edit Hewan</h2>

<form
method="POST"
enctype="multipart/form-data">

<div class="form-group">

<label>Foto Saat Ini</label>

<br>

<img
src="../assets/img/uploads/hewan/<?= $data['foto']; ?>"
width="150">

</div>

<div class="form-group">

<label>Ganti Foto</label>

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

<?php while($p=mysqli_fetch_assoc($listPemilik)){ ?>

<option
value="<?= $p['id_pemilik']; ?>"

<?= ($p['id_pemilik']==$data['id_pemilik'])
? 'selected'
: ''; ?>>

<?= $p['nama']; ?>

</option>

<?php } ?>

</select>

</div>

<div class="form-group">

<label>Nama Hewan</label>

<input
type="text"
name="nama_hewan"
value="<?= $data['nama_hewan']; ?>"
class="form-control">

</div>

<div class="form-group">

<label>Jenis</label>

<input
type="text"
name="jenis"
value="<?= $data['jenis']; ?>"
class="form-control">

</div>

<div class="form-group">

<label>Ras</label>

<input
type="text"
name="ras"
value="<?= $data['ras']; ?>"
class="form-control">

</div>

<div class="form-group">

<label>Tanggal Lahir</label>

<input
type="date"
name="tanggal_lahir"
value="<?= $data['tanggal_lahir']; ?>"
class="form-control">

</div>

<div class="form-group">

<label>Berat</label>

<input
type="number"
step="0.01"
name="berat"
value="<?= $data['berat']; ?>"
class="form-control">

</div>

<div class="form-group">

<label>Warna</label>

<input
type="text"
name="warna"
value="<?= $data['warna']; ?>"
class="form-control">

</div>

<div class="form-group">

<label>Jenis Kelamin</label>

<select
name="jenis_kelamin"
class="form-control">

<option
value="Jantan"
<?= ($data['jenis_kelamin']=='Jantan')
? 'selected' : ''; ?>>

Jantan

</option>

<option
value="Betina"
<?= ($data['jenis_kelamin']=='Betina')
? 'selected' : ''; ?>>

Betina

</option>

</select>

</div>

<div class="form-group">

<label>Riwayat Alergi</label>

<textarea
name="alergi"
class="form-control"><?= $data['alergi']; ?></textarea>

</div>

<div class="form-group">

<label>Status Vaksin</label>

<select
name="status_vaksin"
class="form-control">

<option
value="Lengkap"
<?= ($data['status_vaksin']=='Lengkap')
? 'selected' : ''; ?>>
Lengkap
</option>

<option
value="Belum Lengkap"
<?= ($data['status_vaksin']=='Belum Lengkap')
? 'selected' : ''; ?>>
Belum Lengkap
</option>

<option
value="Belum Pernah"
<?= ($data['status_vaksin']=='Belum Pernah')
? 'selected' : ''; ?>>
Belum Pernah
</option>

</select>

</div>

<button
type="submit"
name="update"
class="btn btn-warning">

Update Data

</button>

<a
href="hewan.php"
class="btn btn-primary">

Kembali

</a>

</form>

</div>

</body>
</html>
