<?php

include "../config/session.php";
include "../config/database.php";

$hewan = mysqli_query($conn,"
    SELECT *
    FROM hewan
    ORDER BY nama_hewan
");

$obat = mysqli_query($conn,"
    SELECT *
    FROM obat
    WHERE stok > 0
");

$vaksin = mysqli_query($conn,"
    SELECT *
    FROM vaksin
    WHERE stok > 0
");

if(isset($_POST['simpan']))
{
    $id_hewan = $_POST['id_hewan'];
    $keluhan = mysqli_real_escape_string($conn,$_POST['keluhan']);
    $diagnosa = mysqli_real_escape_string($conn,$_POST['diagnosa']);
    $tindakan = mysqli_real_escape_string($conn,$_POST['tindakan']);
    $catatan = mysqli_real_escape_string($conn,$_POST['catatan']);
    $biaya_tindakan = $_POST['biaya_tindakan'];

    $total = $biaya_tindakan;

    // Upload Hasil Lab
    $hasil_lab = "";

    if(!empty($_FILES['hasil_lab']['name']))
    {
        $hasil_lab = time()."_".$_FILES['hasil_lab']['name'];

        move_uploaded_file(
            $_FILES['hasil_lab']['tmp_name'],
            "../assets/img/uploads/lab/".$hasil_lab
        );
    }

    // Upload Foto Medis
    $foto_medis = "";

    if(!empty($_FILES['foto_medis']['name']))
    {
        $foto_medis = time()."_".$_FILES['foto_medis']['name'];

        move_uploaded_file(
            $_FILES['foto_medis']['tmp_name'],
            "../assets/img/uploads/medis/".$foto_medis
        );
    }

    mysqli_query($conn,"
        INSERT INTO kunjungan
        (
            id_hewan,
            tanggal_kunjungan,
            keluhan,
            diagnosa,
            tindakan,
            biaya_tindakan,
            catatan,
            hasil_lab,
            foto_medis,
            total_biaya
        )
        VALUES
        (
            '$id_hewan',
            CURDATE(),
            '$keluhan',
            '$diagnosa',
            '$tindakan',
            '$biaya_tindakan',
            '$catatan',
            '$hasil_lab',
            '$foto_medis',
            0
        )
    ");

    $id_kunjungan = mysqli_insert_id($conn);

    // OBAT
    if(isset($_POST['obat']))
    {
        foreach($_POST['obat'] as $id_obat)
        {
            $dataObat = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT * FROM obat WHERE id_obat='$id_obat'"
                )
            );

            $harga = $dataObat['harga'];

            $total += $harga;

            mysqli_query($conn,"
                INSERT INTO detail_obat_kunjungan
                (
                    id_kunjungan,
                    id_obat,
                    jumlah,
                    subtotal
                )
                VALUES
                (
                    '$id_kunjungan',
                    '$id_obat',
                    1,
                    '$harga'
                )
            ");

            mysqli_query($conn,"
                UPDATE obat
                SET stok = stok - 1
                WHERE id_obat='$id_obat'
            ");

            $stokBaru = $dataObat['stok'] - 1;

            if($stokBaru <= 0)
            {
                $status = 'Habis';
            }
            elseif($stokBaru <= 5)
            {
                $status = 'Kritis';
            }
            elseif($stokBaru <= 10)
            {
                $status = 'Menipis';
            }
            else
            {
                $status = 'Aman';
            }

            mysqli_query($conn,"
                UPDATE obat
                SET status_stok='$status'
                WHERE id_obat='$id_obat'
            ");
        }
    }

    // VAKSIN
    if(isset($_POST['vaksin']))
    {
        foreach($_POST['vaksin'] as $id_vaksin)
        {
            $dataVaksin = mysqli_fetch_assoc(
                mysqli_query(
                    $conn,
                    "SELECT * FROM vaksin WHERE id_vaksin='$id_vaksin'"
                )
            );

            $harga = $dataVaksin['harga'];

            $total += $harga;

            mysqli_query($conn,"
                INSERT INTO detail_vaksin_kunjungan
                (
                    id_kunjungan,
                    id_vaksin,
                    jumlah,
                    subtotal
                )
                VALUES
                (
                    '$id_kunjungan',
                    '$id_vaksin',
                    1,
                    '$harga'
                )
            ");

            mysqli_query($conn,"
                UPDATE vaksin
                SET stok = stok - 1
                WHERE id_vaksin='$id_vaksin'
            ");

            $stokBaru = $dataVaksin['stok'] - 1;

            if($stokBaru <= 0)
            {
                $status = 'Habis';
            }
            elseif($stokBaru <= 5)
            {
                $status = 'Kritis';
            }
            elseif($stokBaru <= 10)
            {
                $status = 'Menipis';
            }
            else
            {
                $status = 'Aman';
            }

            mysqli_query($conn,"
                UPDATE vaksin
                SET status_stok='$status'
                WHERE id_vaksin='$id_vaksin'
            ");
        }
    }

    mysqli_query($conn,"
        UPDATE kunjungan
        SET total_biaya='$total'
        WHERE id_kunjungan='$id_kunjungan'
    ");

    header("Location: kunjungan.php");
    exit;
}

?>

<!DOCTYPE html>
<html>

<head>

<meta charset="UTF-8">

<title>Tambah Kunjungan</title>

<link rel="stylesheet"
href="../assets/css/global.css">

<link rel="stylesheet"
href="../assets/css/sidebar.css">

<link rel="stylesheet"
href="../assets/css/header.css">

<link rel="stylesheet"
href="../assets/css/kunjungan.css">

</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2 class="page-title">
Tambah Rekam Medis
</h2>

<div class="card">

<form
method="POST"
enctype="multipart/form-data"
class="medical-form">

<label>Nama Hewan</label>

<select
name="id_hewan"
class="form-control">

<?php while($h=mysqli_fetch_assoc($hewan)){ ?>

<option value="<?= $h['id_hewan']; ?>">

<?= $h['nama_hewan']; ?>

</option>

<?php } ?>

</select>

<label>Keluhan</label>

<textarea
name="keluhan"
class="form-control"></textarea>

<label>Diagnosa</label>

<textarea
name="diagnosa"
class="form-control"></textarea>

<label>Tindakan Medis</label>

<textarea
name="tindakan"
class="form-control"></textarea>

<label>Catatan Dokter</label>

<textarea
name="catatan"
class="form-control"></textarea>

<label>Biaya Tindakan</label>

<input
type="number"
name="biaya_tindakan"
class="form-control"
required>

<label>Obat Digunakan</label>

<select
multiple
name="obat[]"
class="form-control multi-select">

<?php while($o=mysqli_fetch_assoc($obat)){ ?>

<option value="<?= $o['id_obat']; ?>">

<?= $o['nama_obat']; ?>
-
Rp <?= number_format($o['harga']); ?>

</option>

<?php } ?>

</select>

<label>Vaksin Digunakan</label>

<select
multiple
name="vaksin[]"
class="form-control multi-select">

<?php while($v=mysqli_fetch_assoc($vaksin)){ ?>

<option value="<?= $v['id_vaksin']; ?>">

<?= $v['nama_vaksin']; ?>

</option>

<?php } ?>

</select>

<label>Upload Hasil Lab</label>

<input
type="file"
name="hasil_lab"
class="form-control">

<label>Upload Foto Medis</label>

<input
type="file"
name="foto_medis"
class="form-control">

<br>

<button
type="submit"
name="simpan"
class="btn btn-success">

Simpan Kunjungan

</button>

<a
href="kunjungan.php"
class="btn btn-danger">

Batal

</a>

</form>

</div>

</div>

</body>
</html>
```
