-- phpMyAdmin SQL Dump
-- Database: praktikum_crud
-- Generated: 2025

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

-- Create and use database
CREATE DATABASE IF NOT EXISTS `praktikum_crud`
  DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `praktikum_crud`;

-- --------------------------------------------------------
-- Table structure: mahasiswa
-- --------------------------------------------------------
CREATE TABLE `mahasiswa` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `nim`        VARCHAR(20)  NOT NULL,
  `nama`       VARCHAR(100) NOT NULL,
  `jurusan`    VARCHAR(100) NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `alamat`     TEXT         DEFAULT NULL,
  `foto`       VARCHAR(255) DEFAULT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `nim` (`nim`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Table structure: users
-- --------------------------------------------------------
CREATE TABLE `users` (
  `id`         INT(11)      NOT NULL AUTO_INCREMENT,
  `username`   VARCHAR(50)  NOT NULL,
  `email`      VARCHAR(100) NOT NULL,
  `password`   VARCHAR(255) NOT NULL,
  `full_name`  VARCHAR(100) NOT NULL,
  `created_at` TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `username` (`username`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------
-- Default admin account (password: admin123)
-- --------------------------------------------------------
INSERT INTO `users` (`username`, `email`, `password`, `full_name`) VALUES
('admin', 'admin@localhost.com',
 '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
 'Administrator');

-- --------------------------------------------------------
-- Sample mahasiswa data
-- --------------------------------------------------------
INSERT INTO `mahasiswa` (`nim`, `nama`, `jurusan`, `email`, `alamat`) VALUES
('20230001', 'Budi Santoso',    'Teknik Informatika', 'budi@example.com',    'Jl. Merdeka No.1, Yogyakarta'),
('20230002', 'Siti Rahayu',     'Sistem Informasi',   'siti@example.com',    'Jl. Malioboro No.5, Yogyakarta'),
('20230003', 'Agus Prakoso',    'Teknik Elektro',     'agus@example.com',    'Jl. Kaliurang No.10, Yogyakarta'),
('20230004', 'Dewi Lestari',    'Teknik Informatika', 'dewi@example.com',    'Jl. Gejayan No.3, Yogyakarta'),
('20230005', 'Rizky Firmansyah','Sistem Informasi',   'rizky@example.com',   'Jl. Colombo No.7, Yogyakarta'),
('20230006', 'Annisa Putri',    'Teknik Komputer',    'annisa@example.com',  'Jl. Seturan No.2, Yogyakarta');

COMMIT;
