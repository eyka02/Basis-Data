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
"SELECT *
FROM vaksin
WHERE nama_vaksin
LIKE '%$search%'
ORDER BY nama_vaksin ASC"
);

?>

<!DOCTYPE html>
<html>
<head>

<title>Data Vaksin</title>

<link rel="stylesheet"
href="../assets/css/global.css">

<link rel="stylesheet"
href="../assets/css/sidebar.css">

<link rel="stylesheet"
href="../assets/css/header.css">

<link rel="stylesheet"
href="../assets/css/vaksin.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2>Data Vaksin</h2>

<a
href="tambah_vaksin.php"
class="btn btn-success">

Tambah Vaksin

</a>

<br><br>

<form method="GET">

<input
type="text"
name="search"
placeholder="Cari Vaksin..."
class="form-control">

<br>

<button class="btn btn-primary">

Cari

</button>

</form>

<br>

<table class="table">

<tr>

<th>Nama</th>
<th>Stok</th>
<th>Harga</th>
<th>Status</th>
<th>Aksi</th>

</tr>

<?php while($row=mysqli_fetch_assoc($query)){ ?>

<tr>

<td><?= $row['nama_vaksin']; ?></td>


<td><?= $row['stok']; ?></td>

<td>
Rp <?= number_format($row['harga']); ?>
</td>

<td>

<?php

switch($row['status_stok'])
{
    case 'Aman':
        echo '<span class="badge-success">Aman</span>';
    break;

    case 'Menipis':
        echo '<span class="badge-warning">Menipis</span>';
    break;

    case 'Kritis':
        echo '<span class="badge-danger">Kritis</span>';
    break;

    case 'Habis':
        echo '<span class="badge-dark">Habis</span>';
    break;
}

?>

</td>

<td>

<a
href="edit_vaksin.php?id=<?= $row['id_vaksin']; ?>"
class="btn btn-warning">

Edit

</a>

<a
href="delete_vaksin.php?id=<?= $row['id_vaksin']; ?>"
class="btn btn-danger"
onclick="return confirm('Hapus vaksin?')">

Hapus

</a>

</td>

</tr>

<?php } ?>

</table>

</div>

</body>
</html>