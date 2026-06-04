<?php

$id_admin = $_SESSION['admin'];

$queryAdmin = mysqli_query(
    $conn,
    "SELECT *
    FROM admins
    WHERE id_admin='$id_admin'"
);

$admin = mysqli_fetch_assoc($queryAdmin);

$stokMenipis = mysqli_num_rows(
    mysqli_query(
        $conn,
        "SELECT *
        FROM obat
        WHERE status_stok IN
        ('Menipis','Kritis','Habis')"
    )
);

?>

<div class="topbar">

    <h3>
        Sistem Rekam Medis Klinik Hewan
    </h3>

    <div>
        Selamat Datang,
        <?= $admin['nama_admin']; ?>
    </div>

</div>

<?php if($stokMenipis > 0){ ?>

<div class="alert-stock">

⚠ Ada
<b><?= $stokMenipis; ?></b>
obat dengan stok rendah

</div>

<?php } ?>