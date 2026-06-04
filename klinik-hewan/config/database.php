<?php

$host = "localhost";
$user = "root";
$pass = "";
$db   = "klinik_hewan";

$conn = mysqli_connect(
    $host,
    $user,
    $pass,
    $db
);

if(!$conn){
    die("Koneksi Database Gagal : "
    . mysqli_connect_error());
}

?>