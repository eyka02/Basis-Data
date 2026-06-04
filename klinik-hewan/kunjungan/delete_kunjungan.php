<?php

include "../config/session.php";
include "../config/database.php";

if(!isset($_GET['id']))
{
    header("Location: kunjungan.php");
    exit;
}

$id = $_GET['id'];

mysqli_query(
$conn,
"DELETE FROM kunjungan
WHERE id_kunjungan='$id'"
);

header("Location: kunjungan.php");
exit;