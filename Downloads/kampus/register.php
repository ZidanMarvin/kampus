<?php

include "config.php";

$nama     = mysqli_real_escape_string($conn, $_POST['nama']);
$email    = mysqli_real_escape_string($conn, $_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$role     = mysqli_real_escape_string($conn, $_POST['role']);

// Cek apakah email sudah terdaftar
$cek = mysqli_query($conn, "SELECT id FROM users WHERE email='$email'");
if (mysqli_num_rows($cek) > 0) {
    echo "<script>alert('Email sudah terdaftar!'); window.location='register.html';</script>";
    exit;
}

$sql = "INSERT INTO users (nama, email, password, role)
        VALUES ('$nama', '$email', '$password', '$role')";

if (mysqli_query($conn, $sql)) {

    echo "<script>alert('Registrasi berhasil! Silakan login.'); window.location='login.html';</script>";

} else {

    echo "<script>alert('Gagal registrasi, coba lagi.'); window.location='register.html';</script>";

}
?>