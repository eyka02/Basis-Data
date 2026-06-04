<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

mysqli_query(
$conn,
"DELETE FROM hewan
WHERE id_hewan='$id'"
);

header("Location: hewan.php");
exit;