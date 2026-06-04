<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

mysqli_query(
$conn,
"DELETE FROM pemilik
WHERE id_pemilik='$id'"
);

header("Location: pemilik.php");
exit;