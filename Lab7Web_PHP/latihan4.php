<?php
$judul = "Latihan 4 — Membuat Function";

function hitungDiskon($harga, $persen)
{
    $potongan = $harga * ($persen / 100);
    return $harga - $potongan;
}

// Function tambahan
function hitungLuasPersegiPanjang($panjang, $lebar)
{
    return $panjang * $lebar;
}

function tentukanKelulusan($nilai)
{
    return $nilai >= 60 ? "LULUS" : "TIDAK LULUS";
}

include "includes/header.php";

$hargaAkhir = hitungDiskon(150000, 10);
echo "<p>Harga setelah diskon: Rp" . number_format($hargaAkhir, 0, ",", ".") . "</p>";

echo "<p>Luas persegi panjang (8 x 4): " . hitungLuasPersegiPanjang(8, 4) . "</p>";

foreach ([85, 60, 45] as $n) {
    echo "<p>Nilai $n: " . tentukanKelulusan($n) . "</p>";
}

include "includes/footer.php";
