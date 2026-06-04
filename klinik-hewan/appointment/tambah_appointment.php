<?php

include "../config/session.php";
include "../config/database.php";

$hewan = mysqli_query(
$conn,
"SELECT *
FROM hewan
ORDER BY nama_hewan ASC"
);

if(isset($_POST['simpan']))
{

$id_hewan = mysqli_real_escape_string($conn, $_POST['id_hewan']);
$tanggal  = mysqli_real_escape_string($conn, $_POST['tanggal']);
$jam      = mysqli_real_escape_string($conn, $_POST['jam']);
$keluhan  = mysqli_real_escape_string($conn, $_POST['keluhan']);

/*
|--------------------------------------------------------------------------
| VALIDASI SEDERHANA
|--------------------------------------------------------------------------
*/

if($id_hewan == '' || $tanggal == '' || $jam == '')
{
    echo "<script>alert('Data wajib diisi!');history.back();</script>";
    exit;
}

/*
|--------------------------------------------------------------------------
| INSERT DATA
|--------------------------------------------------------------------------
*/

mysqli_query(
$conn,
"INSERT INTO appointment
(
id_hewan,
tanggal,
jam,
keluhan,
status
)
VALUES
(
'$id_hewan',
'$tanggal',
'$jam',
'$keluhan',
'Menunggu'
)"
);

header("Location: appointment.php");
exit;

}

?>

<!DOCTYPE html>
<html>

<head>

<title>Tambah Appointment</title>

<link rel="stylesheet" href="../assets/css/global.css">
<link rel="stylesheet" href="../assets/css/sidebar.css">
<link rel="stylesheet" href="../assets/css/header.css">
<link rel="stylesheet" href="../assets/css/appointment.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2 class="page-title">Tambah Appointment</h2>

<form method="POST" class="form-card">

<label>Hewan</label>

<select name="id_hewan" class="form-control" required>

<option value="">-- Pilih Hewan --</option>

<?php while($h=mysqli_fetch_assoc($hewan)){ ?>

<option value="<?= $h['id_hewan']; ?>">
    <?= $h['nama_hewan']; ?>
</option>

<?php } ?>

</select>

<label>Tanggal</label>

<input type="date" name="tanggal" class="form-control" required>

<label>Jam</label>

<input type="time" name="jam" class="form-control" required>

<label>Keluhan</label>

<textarea name="keluhan" class="form-control" rows="4"></textarea>

<br>

<button name="simpan" class="btn btn-success">
    Simpan
</button>

<a href="appointment.php" class="btn btn-danger">
    Kembali
</a>

</form>

</div>

</body>

</html>