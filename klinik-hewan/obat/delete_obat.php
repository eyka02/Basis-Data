<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

mysqli_query(
$conn,
"DELETE FROM obat
WHERE id_obat='$id'"
);

header("Location: obat.php");