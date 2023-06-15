<?php


$dbHost = 'localhost'; // Ganti dengan host MySQL And
$dbUsername = 'root'; // Ganti dengan username MySQL Anda
$dbPassword = ''; // Ganti dengan password MySQL Anda
$dbName = 'skripsi'; // Ganti dengan nama database Anda

    // Koneksi ke database
    $conn = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);
    if ($conn->connect_error) {
        die("Koneksi ke database gagal: " . $conn->connect_error);
    }


?>