<?php
session_start();
include 'config.php';

$user_id   = isset($_SESSION['id']) ? (int)$_SESSION['id'] : (int)$_POST['user_id'];
$judul     = mysqli_real_escape_string($conn, $_POST['judul']);
$kategori  = mysqli_real_escape_string($conn, $_POST['kategori']);
$lokasi    = mysqli_real_escape_string($conn, $_POST['lokasi']);
$deskripsi = mysqli_real_escape_string($conn, $_POST['deskripsi']);

$namaFoto = '';
if (!empty($_FILES['foto']['name'])) {
    $namaFoto  = time() . '_' . basename($_FILES['foto']['name']);
    $uploadDir = 'uploads/';
    if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);
    move_uploaded_file($_FILES['foto']['tmp_name'], $uploadDir . $namaFoto);
}

// Status awal = Menunggu (sesuai ENUM database)
$sql = "INSERT INTO laporan (user_id, judul, kategori, lokasi, deskripsi, foto, status)
        VALUES ('$user_id','$judul','$kategori','$lokasi','$deskripsi','$namaFoto','Menunggu')";

if (mysqli_query($conn, $sql)) {
    echo "<script>alert('Laporan berhasil dikirim!'); window.location='dashboard.php';</script>";
} else {
    echo "<script>alert('Gagal: " . mysqli_error($conn) . "'); history.back();</script>";
}
?>