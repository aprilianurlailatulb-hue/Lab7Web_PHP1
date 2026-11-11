<?php
$judul = "Latihan 2 — Variabel, Operator, dan Percabangan";

// Nilai awal sesuai modul
$nama = "Siti";
$tugas = 85;
$uts = 78;
$uas = 90;

// Jika form dikirim, pakai nilai dari form (untuk menguji kombinasi nilai)
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama  = trim($_POST["nama"] ?? "Siti");
    $tugas = is_numeric($_POST["tugas"] ?? "") ? (float)$_POST["tugas"] : 0;
    $uts   = is_numeric($_POST["uts"] ?? "") ? (float)$_POST["uts"] : 0;
    $uas   = is_numeric($_POST["uas"] ?? "") ? (float)$_POST["uas"] : 0;
}

$nilaiAkhir = ($tugas * 0.30) + ($uts * 0.30) + ($uas * 0.40);

if ($nilaiAkhir >= 85) {
    $predikat = "A";
} elseif ($nilaiAkhir >= 75) {
    $predikat = "B";
} elseif ($nilaiAkhir >= 60) {
    $predikat = "C";
} else {
    $predikat = "D";
}

include "includes/header.php";
?>
<div class="sukses">
<?php
echo "Nama: " . htmlspecialchars($nama) . "<br>";
echo "Nilai akhir: " . number_format($nilaiAkhir, 2) . "<br>";
echo "Predikat: " . $predikat;
?>
</div>

<h3>Uji kombinasi nilai</h3>
<form method="POST" action="">
    <label for="nama">Nama</label>
    <input id="nama" name="nama" value="<?= e($nama) ?>">
    <label for="tugas">Nilai Tugas (30%)</label>
    <input id="tugas" name="tugas" type="number" min="0" max="100" step="any" value="<?= e($tugas) ?>">
    <label for="uts">Nilai UTS (30%)</label>
    <input id="uts" name="uts" type="number" min="0" max="100" step="any" value="<?= e($uts) ?>">
    <label for="uas">Nilai UAS (40%)</label>
    <input id="uas" name="uas" type="number" min="0" max="100" step="any" value="<?= e($uas) ?>">
    <button type="submit">Hitung</button>
</form>
<?php include "includes/footer.php"; ?>
