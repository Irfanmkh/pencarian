<?php

include "koneksi.php";

// Ambil data dari database
$data = $_GET['data']; // Ambil data yang dikirim dari AJAX

// Query untuk mengambil data dari tabel yang diinginkan
$sql = "SELECT judul FROM buku WHERE judul = '$data'";
$result = $conn->query($sql);

// Tangani hasil query
if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $judul = $row['judul'];
    $pengarang = $row['pengarang'];
    $penerbit = $row['penerbit'];
    $tahun = $row['tahun'];
    // ... (mengambil kolom data lainnya)
    
    // Formatkan data sesuai kebutuhan
    $response = "Judul: " . $judul . "<br>";
    $response .= "Pengarang: " . $pengarang . "<br>";
    $response .= "Penerbit: " . $penerbit . "<br>";
    $response .= "tahun: " . $tahun . "<br>";
    // ... (formatkan data lainnya)
    
    echo $response; // Kirimkan respons ke AJAX
} else {
    echo "Data tidak ditemukan";
}

// Tutup koneksi ke database
$conn->close();
?>
