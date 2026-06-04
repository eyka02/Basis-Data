<?php

include "../config/session.php";
include "../config/database.php";

$query = mysqli_query(
$conn,
"SELECT

a.*,
h.nama_hewan,
p.nama AS pemilik

FROM appointment a

JOIN hewan h
ON a.id_hewan=h.id_hewan

JOIN pemilik p
ON h.id_pemilik=p.id_pemilik

ORDER BY a.tanggal DESC,
a.jam DESC"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Appointment</title>

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

<h2>Appointment</h2>

<a
href="tambah_appointment.php"
class="btn btn-success">

Tambah Appointment

</a>

<br><br>

<table class="table">

<tr>

<th>Tanggal</th>
<th>Jam</th>
<th>Hewan</th>
<th>Pemilik</th>
<th>Status</th>
<th>Aksi</th>

</tr>

<?php while($row=mysqli_fetch_assoc($query)){ ?>

<tr>

<td><?= $row['tanggal']; ?></td>

<td><?= $row['jam']; ?></td>

<td><?= $row['nama_hewan']; ?></td>

<td><?= $row['pemilik']; ?></td>

<td>

<?php

switch($row['status'])
{
case 'Pending':
echo '<span class="badge-warning">Pending</span>';
break;

case 'Datang':
echo '<span class="badge-primary">Datang</span>';
break;

case 'Selesai':
echo '<span class="badge-success">Selesai</span>';
break;

case 'Batal':
echo '<span class="badge-danger">Batal</span>';
break;
}

?>

</td>

<td>

<a
href="detail_appointment.php?id=<?= $row['id_appointment']; ?>"
class="btn btn-primary">

Detail

</a>

<a
href="edit_appointment.php?id=<?= $row['id_appointment']; ?>"
class="btn btn-warning">

Edit

</a>

<a
href="delete_appointment.php?id=<?= $row['id_appointment']; ?>"
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