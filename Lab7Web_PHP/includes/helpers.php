<?php
// Fungsi bantu yang dipakai di banyak halaman

// Menampilkan teks dengan aman (mencegah injeksi HTML/script)
function e($teks)
{
    return htmlspecialchars((string)$teks, ENT_QUOTES, "UTF-8");
}

// Mengubah angka menjadi format Rupiah, contoh: 150000 -> Rp150.000
function formatRupiah($angka)
{
    return "Rp" . number_format((float)$angka, 0, ",", ".");
}
