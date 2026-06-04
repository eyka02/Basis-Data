<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

$query = mysqli_query(
$conn,
"SELECT *
FROM appointment
WHERE id_appointment='$id'"
);

$data = mysqli_fetch_assoc($query);

if(isset($_POST['update']))
{

    $tanggal = $_POST['tanggal'];
    $jam = $_POST['jam'];
    $keluhan = $_POST['keluhan'];
    $status = $_POST['status'];

    mysqli_query(
    $conn,
    "UPDATE appointment
    SET

    tanggal='$tanggal',
    jam='$jam',
    keluhan='$keluhan',
    status='$status'

    WHERE id_appointment='$id'"
    );

    header("Location: appointment.php");
    exit;
}

?>

<!DOCTYPE html>
<html>
<head>

<title>Edit Appointment</title>

<link rel="stylesheet"
href="../assets/css/global.css">

<link rel="stylesheet"
href="../assets/css/sidebar.css">

<link rel="stylesheet"
href="../assets/css/header.css">

<link rel="stylesheet"
href="../assets/css/appointment.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2 class="page-title">

Edit Appointment

</h2>

<form method="POST">

<div class="form-group">

<label>Tanggal</label>

<input
type="date"
name="tanggal"
class="form-control"
value="<?= $data['tanggal']; ?>"
required>

</div>

<div class="form-group">

<label>Jam</label>

<input
type="time"
name="jam"
class="form-control"
value="<?= $data['jam']; ?>"
required>

</div>

<div class="form-group">

<label>Keluhan</label>

<textarea
name="keluhan"
class="form-control"
rows="5"><?= $data['keluhan']; ?></textarea>

</div>

<div class="form-group">

<label>Status</label>

<select
name="status"
class="form-control">

<option
value="Pending"
<?= ($data['status']=='Pending') ? 'selected' : ''; ?>>
Menunggu
</option>

<option
value="Datang"
<?= ($data['status']=='Datang') ? 'selected' : ''; ?>>
Datang
</option>

<option
value="Selesai"
<?= ($data['status']=='Selesai') ? 'selected' : ''; ?>>
Selesai
</option>

<option
value="Batal"
<?= ($data['status']=='Batal') ? 'selected' : ''; ?>>
Batal
</option>

</select>

</div>

<br>

<button
type="submit"
name="update"
class="btn btn-success">

Update Appointment

</button>

<a
href="appointment.php"
class="btn btn-danger">

Batal

</a>

</form>

</div>

</body>
</html>
