<?php

include "../config/session.php";
include "../config/database.php";

$id = $_GET['id'];

mysqli_query(
$conn,
"DELETE FROM appointment
WHERE id_appointment='$id'"
);

header("Location: appointment.php");
exit;