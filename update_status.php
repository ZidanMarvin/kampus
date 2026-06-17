<?php
session_start();
include 'config.php';

$id     = (int)$_POST['id'];
$status = $_POST['status']; // jangan escape dulu, cek dulu

// Nilai EXACT sama dengan ENUM di database
$allowed = ['Menunggu', 'Diverifikasi', 'Dalam Perbaikan', 'Selesai'];

if (!in_array($status, $allowed)) {
    echo "Status tidak valid: [" . $status . "]";
    exit;
}

$status = mysqli_real_escape_string($conn, $status);
$result = mysqli_query($conn, "UPDATE laporan SET status='$status' WHERE id=$id");

if ($result) {
    echo "OK";
} else {
    echo "Gagal: " . mysqli_error($conn);
}
?>