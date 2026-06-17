<?php

include 'config.php';

$query = mysqli_query(
    $conn,
    "SELECT * FROM laporan"
);

$data = [];

while($row = mysqli_fetch_assoc($query)){
    $data[] = $row;
}

echo json_encode($data);
?>