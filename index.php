<!doctype html>
<html lang="en">

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css"
        integrity="sha384-xOolHFLEh07PJGoPkLv1IbcEPTNtaed2xpHsD9ESMhqIYd0nLMwNLD69Npy4HI+N" crossorigin="anonymous">

    <!-- my css -->
    <link rel="stylesheet" href="style.css" />

    <!-- font awesome css -->
    <link rel="stylesheet" href="font-awesome-4.7.0/css/font-awesome.min.css">

    <!-- google fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Comfortaa:wght@300;400&display=swap" rel="stylesheet">

    <title>UPT PERPUSTAKAAN UTM</title>
</head>

<body>

    <!-- navbar -->
    <div class="container">
        <nav class="navbar navbar-expand-lg navbar-dark fixed-top">
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav m-auto">
                    <li class="nav-item">
                        <a class="navbar-brand" aria-current="page" href="#home">
                            <img src="img/Logo UTM KEMDIKBUDRISTEK.png" width="75" height="75"
                                class="d-inline-block align-top" alt="">

                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="navbar-brand">
                            <h2>
                                UPT. PERPUSTAKAAN UNIVERSITAS TRUNOJOYO MADURA
                            </h2>
                            <h6>
                                Jl. Raya Telang Po.Box.2 Kamal, Bangkalan – Madura 031-3012707
                            </h6>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </div>

    <!-- end navbar -->

    <section id="isi">
        <div class="container">
            <div class="search-form">
                <h1>Pencarian Judul Buku</h1>
                <form action="index.php" method="GET">
                    <div class="row mb-4">
                        <div class="form-group col-md-12">
                            <div class="input-group-append">
                                <input id="exampleFormControlInput5" type="text" name="keyword" id="keyword"
                                    placeholder="Silahkan Cari disini..." aria-describedby="button-addon1"
                                    class="form-control form-control-underlined border-primary">
                                <button id="button-addon1" type="submit" class="btn btn-link text-primary"><i
                                        class="fa fa-search"></i></button>


                            </div>
                        </div>
                    </div>

                    <!-- <label for="keyword">Keyword:</label>
        <input type="text" name="keyword" id="keyword">
        <button type="submit">Cari</button> -->
                </form>
            </div>

            <?php

            function preprocess($text)
            {
                // Case folding: mengubah semua karakter menjadi huruf kecil
                $text = strtolower($text);

                // Tokenizing: memisahkan kata-kata berdasarkan spasi
                $tokens = explode(' ', $text);

                // Menghapus spasi di awal dan akhir setiap token
                $tokens = array_map('trim', $tokens);

                // Hapus duplikat kata
                $tokens = array_unique($tokens);

                return $tokens;
            }

            function levenshtein_distance($str1, $str2)
            {
                $len1 = strlen($str1);
                $len2 = strlen($str2);

                $matrix = [];

                for ($i = 0; $i <= $len1; ++$i) {
                    $matrix[$i] = [$i];
                }

                for ($j = 0; $j <= $len2; ++$j) {
                    $matrix[0][$j] = $j;
                }

                for ($i = 1; $i <= $len1; ++$i) {
                    for ($j = 1; $j <= $len2; ++$j) {
                        $cost = ($str1[$i - 1] != $str2[$j - 1]);
                        $matrix[$i][$j] = min(
                            $matrix[$i - 1][$j] + 1,
                            $matrix[$i][$j - 1] + 1,
                            $matrix[$i - 1][$j - 1] + $cost
                        );
                    }
                }

                return $matrix[$len1][$len2];
            }

            // Fungsi untuk melakukan pencarian judul buku berdasarkan kata kunci
            function searchBook($keyword, $threshold)
            {
                include 'koneksi.php';
                // Escape kata kunci untuk mencegah SQL Injection
                $keyword = $conn->real_escape_string($keyword);

                // Preproses keyword
                $keywordTokens = preprocess($keyword);

                // Query untuk mencari judul buku dengan jarak Levenshtein Distance di bawah threshold
                $sql = 'SELECT judul FROM buku ';

                $result = $conn->query($sql);

                // Array untuk menyimpan hasil pencarian
                $searchResults = [];


                if ($result->num_rows > 0) {
                    // Looping melalui setiap judul buku di database
                    while ($row = $result->fetch_assoc()) {
                        $judulBuku = $row['judul'];

                        // Preproses judul buku
                        $judulBukuTokens = preprocess($judulBuku);

                        // Menghitung jarak Levenshtein Distance antara keyword dan judul buku
                        $minDistance = PHP_INT_MAX;
                        foreach ($keywordTokens as $keywordToken) {
                            foreach ($judulBukuTokens as $judulBukuToken) {
                                $distance = levenshtein_distance($keywordToken, $judulBukuToken);
                                if ($distance < $minDistance) {
                                    $minDistance = $distance;
                                }
                            }
                        }

                        // Menambahkan judul buku ke dalam hasil pencarian jika jarak di bawah threshold
                        if ($minDistance <= $threshold) {
                            $searchResults[] = $judulBuku;
                        }
                        if ($searchResults !== null) {
                            usort($searchResults, function ($a, $b) use ($keyword) {
                                // Menghitung jarak Levenshtein Distance antara keyword dan judul buku
                                $distanceA = levenshtein_distance($keyword, $a);
                                $distanceB = levenshtein_distance($keyword, $b);

                                // Membandingkan jarak Levenshtein Distance untuk pengurutan
                                if ($distanceA == $distanceB) {
                                    return 0;
                                } elseif ($distanceA < $distanceB) {
                                    return -1;
                                } else {
                                    return 1;
                                }
                            });
                        }
                    }
                }

                // Menutup koneksi database

                return $searchResults;
            }

            function getClosestWord($keyword, $allTokens)
            {
                // Menghitung jarak Levenshtein Distance antara keyword dan setiap token
                $distances = array();
                foreach ($allTokens as $token) {
                    $distances[$token] = levenshtein_distance($keyword, $token);
                }

                // Mengurutkan token berdasarkan jarak Levenshtein Distance terkecil
                asort($distances);

                // Mengambil token dengan jarak Levenshtein Distance terkecil
                $closestToken = key($distances);

                return $closestToken;
            }
            // Mengecek apakah keyword telah dikirim melalui form
            if (isset($_GET['keyword'])) {
                // Mengambil keyword dari form
                $keyword = $_GET['keyword'];
                // Tokenisasi keyword
                $keywordTokens = preprocess($keyword);

                // Koneksi ke database
                include 'koneksi.php';

                // Query untuk mengambil semua token dari data di database
                $sql = 'SELECT judul FROM buku';
                $result = $conn->query($sql);

                // Array untuk menyimpan semua token
                $allTokens = [];

                if ($result->num_rows > 0) {
                    // Looping melalui setiap token di database
                    while ($row = $result->fetch_assoc()) {
                        $token = $row['judul'];
                        $allTokens[] = $token;
                    }
                }

                // Menutup koneksi database

                // Mencari kata terdekat dari keyword berdasarkan data di database
                $closestWord = getClosestWord($keyword, $allTokens);

                if ($closestWord !== '') {
                    echo "Mungkin maksud Anda: <b>{$closestWord}</b><br>";
                }

                // Ambang batas jarak Levenshtein Distance
                $threshold = 2;


                // Melakukan pencarian judul buku berdasarkan keyword
                $results = searchBook($keyword, $threshold);
                if (count($results) > 0) {
                    echo "<h2>Hasil pencarian untuk kata kunci '<b>{$keyword}</b>'</h2>";



                    echo "<table class='table table-striped'>
        <thead class='bg-primary'>
            <tr>
            <th scope='col'>No. </th>
            <th scope='col'>Judul Buku</th>
            </tr>
        </thead>
        <tbody>";
                    $no = 1;
                    foreach ($results as $result) {
                        echo "<tr>
            <td>{$no}</td>
            <td><button type='button' class='btn btn-transparent' onClick='showPopup()'>{$result}</button></td>
            </tr>";
                        $no++;
                    }
                    echo '</tbody>
        </table>';
                } else {
                    echo "<p>Tidak ditemukan hasil pencarian untuk kata kunci '{$keyword}'.</p>";
                }
            }

            ?>

        </div>

    </section>


    <!-- Optional JavaScript; choose one of the two! -->

    <!-- Option 1: jQuery and Bootstrap Bundle (includes Popper) -->
    <script>
    function showPopup(data) {
        // Mengirim permintaan AJAX ke file get_data.php dengan menggunakan XMLHttpRequest
        var xhr = new XMLHttpRequest();
        xhr.open('GET', 'detail.php?data=' + data, true);
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Tangani respons yang diterima
                var responseData = xhr.responseText;
                // Tampilkan respons dalam popup
                showPopupWindow(responseData);
            }
        };
        xhr.send();
    }

    // Fungsi untuk menampilkan jendela popup dengan konten yang diterima
    function showPopupWindow(content) {
        // Logika untuk menampilkan jendela popup di sini
        // Misalnya, menggunakan library pop-up atau mengubah konten dari elemen HTML
        // Anda dapat menyesuaikan fungsi ini sesuai dengan preferensi dan kebutuhan Anda
        alert(content);
    }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.5.1/dist/jquery.slim.min.js"
        integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-Fy6S3B9q64WdZWQUiU+q4/2Lc9npb8tCaSX9FK7E8HnRr0Jz8D6OP9dO5Vg3Q9ct" crossorigin="anonymous">
    </script>



</body>

</html>