<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

mysqli_query(
$conn,
"DELETE FROM vaksin
WHERE id_vaksin='$id'"
);

header("Location: vaksin.php");
exit;