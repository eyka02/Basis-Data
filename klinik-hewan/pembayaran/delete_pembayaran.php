<?php

include "../config/session.php";
include "../config/database.php";

if(!isset($_GET['id']))
{
    header("Location: pembayaran.php");
    exit;
}

$id = $_GET['id'];

mysqli_query(
$conn,
"DELETE FROM pembayaran
WHERE id_pembayaran='$id'"
);

header("Location: pembayaran.php");
exit;