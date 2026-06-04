<?php

include "../config/session.php";
include "../config/database.php";

/*
|--------------------------------------------------------------------------
| FILTER & SEARCH
|--------------------------------------------------------------------------
*/

$status = '';
$keyword = '';

if(isset($_GET['status']))
{
    $status = mysqli_real_escape_string(
        $conn,
        $_GET['status']
    );
}

if(isset($_GET['keyword']))
{
    $keyword = mysqli_real_escape_string(
        $conn,
        $_GET['keyword']
    );
}

/*
|--------------------------------------------------------------------------
| STATISTIK OBAT
|--------------------------------------------------------------------------
*/

$amanObat = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_obat
FROM obat
WHERE status_stok='Aman'"
));

$menipisObat = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_obat
FROM obat
WHERE status_stok='Menipis'"
));

$kritisObat = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_obat
FROM obat
WHERE status_stok='Kritis'"
));

$habisObat = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_obat
FROM obat
WHERE status_stok='Habis'"
));

/*
|--------------------------------------------------------------------------
| STATISTIK VAKSIN
|--------------------------------------------------------------------------
*/

$amanVaksin = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_vaksin
FROM vaksin
WHERE status_stok='Aman'"
));

$menipisVaksin = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_vaksin
FROM vaksin
WHERE status_stok='Menipis'"
));

$kritisVaksin = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_vaksin
FROM vaksin
WHERE status_stok='Kritis'"
));

$habisVaksin = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_vaksin
FROM vaksin
WHERE status_stok='Habis'"
));

/*
|--------------------------------------------------------------------------
| TOTAL STATISTIK
|--------------------------------------------------------------------------
*/

$totalAman     = $amanObat + $amanVaksin;
$totalMenipis  = $menipisObat + $menipisVaksin;
$totalKritis   = $kritisObat + $kritisVaksin;
$totalHabis    = $habisObat + $habisVaksin;

/*
|--------------------------------------------------------------------------
| TOTAL NILAI INVENTORI OBAT
|--------------------------------------------------------------------------
*/

$nilaiObat = mysqli_fetch_assoc(

mysqli_query(
$conn,
"SELECT
SUM(stok * harga) AS total
FROM obat"

));

/*
|--------------------------------------------------------------------------
| TOTAL NILAI INVENTORI VAKSIN
|--------------------------------------------------------------------------
*/

$nilaiVaksin = mysqli_fetch_assoc(

mysqli_query(
$conn,
"SELECT
SUM(stok * harga) AS total
FROM vaksin"

));

/*
|--------------------------------------------------------------------------
| FILTER QUERY OBAT
|--------------------------------------------------------------------------
*/

$filterObat = [];

if($status != '')
{
    $filterObat[] =
    "status_stok='$status'";
}

if($keyword != '')
{
    $filterObat[] = "

    (
        nama_obat LIKE '%$keyword%'
        OR kategori LIKE '%$keyword%'
        OR supplier LIKE '%$keyword%'
    )

    ";
}

$whereObat = '';

if(count($filterObat) > 0)
{
    $whereObat =
    'WHERE '.implode(
        ' AND ',
        $filterObat
    );
}

/*
|--------------------------------------------------------------------------
| FILTER QUERY VAKSIN
|--------------------------------------------------------------------------
*/

$filterVaksin = [];

if($status != '')
{
    $filterVaksin[] =
    "status_stok='$status'";
}

if($keyword != '')
{
    $filterVaksin[] = "

    (
        nama_vaksin LIKE '%$keyword%'
        OR jenis_vaksin LIKE '%$keyword%'
        OR supplier LIKE '%$keyword%'
    )

    ";
}

$whereVaksin = '';

if(count($filterVaksin) > 0)
{
    $whereVaksin =
    'WHERE '.implode(
        ' AND ',
        $filterVaksin
    );
}

/*
|--------------------------------------------------------------------------
| DATA OBAT
|--------------------------------------------------------------------------
*/

$obat = mysqli_query(

$conn,

"SELECT *

FROM obat

$whereObat

ORDER BY nama_obat ASC"

);

/*
|--------------------------------------------------------------------------
| DATA VAKSIN
|--------------------------------------------------------------------------
*/

$vaksin = mysqli_query(

$conn,

"SELECT *

FROM vaksin

$whereVaksin

ORDER BY nama_vaksin ASC"

);

/*
|--------------------------------------------------------------------------
| TOTAL DATA
|--------------------------------------------------------------------------
*/

$totalObat = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_obat
FROM obat"
));

$totalVaksin = mysqli_num_rows(
mysqli_query(
$conn,
"SELECT id_vaksin
FROM vaksin"
));

?>

<!DOCTYPE html>
<html>

<head>

    <meta charset="UTF-8">

    <title>
        Manajemen Stok
    </title>

    <link
    rel="stylesheet"
    href="../assets/css/global.css">

    <link
    rel="stylesheet"
    href="../assets/css/sidebar.css">

    <link
    rel="stylesheet"
    href="../assets/css/header.css">

    <link
    rel="stylesheet"
    href="../assets/css/stock.css">

    <link
    rel="stylesheet"
    href="../assets/css/dashboard.css">



</head>

<body>

<?php include "../includes/sidebar.php"; ?>

<div class="main-content">

<?php include "../includes/header.php"; ?>

<h2 class="page-title">
    Manajemen Stok
</h2>

<!-- ALERT STOK KRITIS -->
<?php if($totalKritis > 0 || $totalHabis > 0){ ?>

<div class="alert-stock">

    ⚠️ Peringatan Stok!

    <br>

    Kritis:
    <b><?= $totalKritis; ?></b>
    |

    Habis:
    <b><?= $totalHabis; ?></b>

</div>

<?php } ?>

<!-- STATISTIK CARD -->
<div class="card-container">

    <div class="card">
        <h4>Aman</h4>
        <h2><?= $totalAman; ?></h2>
    </div>

    <div class="card">
        <h4>Menipis</h4>
        <h2><?= $totalMenipis; ?></h2>
    </div>

    <div class="card">
        <h4>Kritis</h4>
        <h2><?= $totalKritis; ?></h2>
    </div>

    <div class="card">
        <h4>Habis</h4>
        <h2><?= $totalHabis; ?></h2>
    </div>

</div>

<br>

<!-- INVENTORY VALUE -->
<div class="card-container">

    <div class="card">
        <h4>Total Nilai Obat</h4>
        <h2>Rp <?= number_format($nilaiObat['total']); ?></h2>
    </div>

    <div class="card">
        <h4>Total Nilai Vaksin</h4>
        <h2>Rp <?= number_format($nilaiVaksin['total']); ?></h2>
    </div>

</div>

<br>

<!-- FILTER + SEARCH -->
<form method="GET" class="filter-form">

    <input
    type="text"
    name="keyword"
    value="<?= $keyword; ?>"
    placeholder="Cari obat / vaksin..."
    class="form-control">

    <select
    name="status"
    class="form-control">

        <option value="">Semua Status</option>

        <option value="Aman" <?= ($status=='Aman')?'selected':''; ?>>
            Aman
        </option>

        <option value="Menipis" <?= ($status=='Menipis')?'selected':''; ?>>
            Menipis
        </option>

        <option value="Kritis" <?= ($status=='Kritis')?'selected':''; ?>>
            Kritis
        </option>

        <option value="Habis" <?= ($status=='Habis')?'selected':''; ?>>
            Habis
        </option>

    </select>

    <button type="submit" class="btn btn-primary">
        Filter
    </button>

    <a href="stok.php" class="btn btn-secondary">
        Reset
    </a>

</form>

<br>

<!-- TABEL OBAT -->

<div class="detail-card">

    <h3>Stok Obat</h3>

    <br>

    <table class="table">

        <tr>

            <th>No</th>
            <th>Nama Obat</th>
            <th>Kategori</th>
            <th>Supplier</th>
            <th>Stok</th>
            <th>Harga</th>
            <th>Status</th>

        </tr>

        <?php if(mysqli_num_rows($obat) > 0){ ?>

        <?php $no = 1; ?>

        <?php while($row = mysqli_fetch_assoc($obat)){ ?>

        <tr>

            <td><?= $no++; ?></td>

            <td><?= htmlspecialchars($row['nama_obat']); ?></td>

            <td><?= htmlspecialchars($row['kategori']); ?></td>

            <td><?= htmlspecialchars($row['supplier']); ?></td>

            <td><?= (int)$row['stok']; ?></td>

            <td>Rp <?= number_format($row['harga']); ?></td>

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

                    default:
                        echo '<span class="badge-dark">-</span>';
                }
                ?>

            </td>

        </tr>

        <?php } ?>

        <?php } else { ?>

        <tr>

            <td colspan="7" style="text-align:center; padding:20px;">

                Tidak ada data obat

            </td>

        </tr>

        <?php } ?>

    </table>

</div>

<br>
<!-- TABEL VAKSIN -->

<div class="detail-card">

    <h3>Stok Vaksin</h3>

    <br>

    <table class="table">

        <tr>

            <th>No</th>
            <th>Nama Vaksin</th>
            <th>Jenis</th>
            <th>Supplier</th>
            <th>Stok</th>
            <th>Harga</th>
            <th>Status</th>

        </tr>

        <?php if(mysqli_num_rows($vaksin) > 0){ ?>

        <?php $no = 1; ?>

        <?php while($row = mysqli_fetch_assoc($vaksin)){ ?>

        <tr>

            <td><?= $no++; ?></td>

            <td>
                <?= htmlspecialchars($row['nama_vaksin']); ?>
            </td>

            <td>
                <?= htmlspecialchars($row['jenis_vaksin']); ?>
            </td>

            <td>
                <?= htmlspecialchars($row['supplier']); ?>
            </td>

            <td>
                <?= $row['stok']; ?>
            </td>

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

        </tr>

        <?php } ?>

        <?php } else { ?>

        <tr>

            <td colspan="7" style="text-align:center;">
                Tidak ada data vaksin
            </td>

        </tr>

        <?php } ?>

    </table>

</div>

</body>
</html>