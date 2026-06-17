-- Database: campusfix
-- Jalankan SQL ini di phpMyAdmin atau MySQL CLI

CREATE DATABASE IF NOT EXISTS campusfix;
USE campusfix;

CREATE TABLE IF NOT EXISTS users (
    id       INT AUTO_INCREMENT PRIMARY KEY,
    nama     VARCHAR(100) NOT NULL,
    email    VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role     ENUM('mahasiswa', 'teknisi') DEFAULT 'mahasiswa'
);

CREATE TABLE IF NOT EXISTS laporan (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    judul      VARCHAR(200) NOT NULL,
    kategori   VARCHAR(50),
    lokasi     VARCHAR(200),
    deskripsi  TEXT,
    foto       VARCHAR(255),
    status     ENUM('Pending','Diproses','Selesai') DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id)
);
