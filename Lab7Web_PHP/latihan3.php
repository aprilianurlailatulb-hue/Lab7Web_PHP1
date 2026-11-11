<?php
$judul = "Latihan 3 — Perulangan dan Array";
include "includes/header.php";

// 4 nama dari modul + 3 nama tambahan
$mahasiswa = ["Alya", "Bima", "Citra", "Doni", "Eka", "Farhan", "Gita"];

echo "<h3>Daftar Mahasiswa</h3><ol>";
foreach ($mahasiswa as $nama) {
    echo "<li>" . htmlspecialchars($nama) . "</li>";
}
echo "</ol>";
echo "<p>Jumlah mahasiswa: " . count($mahasiswa) . "</p>";

include "includes/footer.php";
