<?php
require_once __DIR__ . "/includes/helpers.php";
$judul = "Eksperimen Mandiri";
?>
<?php
$judul = "Eksperimen Mandiri";

// ---------- 3. Function format Rupiah ada di includes/helpers.php (formatRupiah) ----------

// ---------- 1. Nilai rata-rata ----------
function hitungRataRata(array $nilai)
{
    return count($nilai) ? array_sum($nilai) / count($nilai) : 0;
}
$rata = null;
$errRata = "";
if (isset($_POST["aksi"]) && $_POST["aksi"] === "rata") {
    $n1 = $_POST["n1"] ?? "";
    $n2 = $_POST["n2"] ?? "";
    $n3 = $_POST["n3"] ?? "";
    if (is_numeric($n1) && is_numeric($n2) && is_numeric($n3)) {
        $rata = hitungRataRata([(float)$n1, (float)$n2, (float)$n3]);
    } else {
        $errRata = "Ketiga nilai harus berupa angka.";
    }
}

// ---------- 2. Cari nama dari array (GET) ----------
$daftarNama = ["Alya", "Bima", "Citra", "Doni", "Eka", "Farhan", "Gita", "Hana"];
$kataCari = trim($_GET["cari"] ?? "");
$hasilCari = [];
if ($kataCari !== "") {
    foreach ($daftarNama as $n) {
        if (stripos($n, $kataCari) !== false) {
            $hasilCari[] = $n;
        }
    }
}

// ---------- 3. Rupiah ----------
$rupiah = null;
if (isset($_POST["aksi"]) && $_POST["aksi"] === "rupiah") {
    $angka = $_POST["angka"] ?? "";
    if (is_numeric($angka)) {
        $rupiah = formatRupiah($angka);
    }
}

// ---------- 4. Validasi NIM (angka saja, 8-12 digit) ----------
$pesanNim = "";
$nimOk = null;
if (isset($_POST["aksi"]) && $_POST["aksi"] === "nim") {
    $nimInput = trim($_POST["nim"] ?? "");
    if (!ctype_digit($nimInput)) {
        $pesanNim = "NIM hanya boleh berisi angka.";
        $nimOk = false;
    } elseif (strlen($nimInput) < 8 || strlen($nimInput) > 12) {
        $pesanNim = "Panjang NIM harus 8–12 digit.";
        $nimOk = false;
    } else {
        $pesanNim = "NIM valid: " . $nimInput;
        $nimOk = true;
    }
}

include "includes/header.php";
?>

<h2>1. Rata-rata tiga nilai</h2>
<form method="POST">
    <input type="hidden" name="aksi" value="rata">
    <label>Nilai 1 <input name="n1" type="number" step="any" value="<?= e($_POST['n1'] ?? '') ?>"></label>
    <label>Nilai 2 <input name="n2" type="number" step="any" value="<?= e($_POST['n2'] ?? '') ?>"></label>
    <label>Nilai 3 <input name="n3" type="number" step="any" value="<?= e($_POST['n3'] ?? '') ?>"></label>
    <button type="submit">Hitung Rata-rata</button>
</form>
<?php if ($errRata): ?><div class="error"><?= e($errRata) ?></div><?php endif; ?>
<?php if ($rata !== null): ?><div class="sukses">Rata-rata: <?= number_format($rata, 2) ?></div><?php endif; ?>

<hr>
<h2>2. Cari nama (GET)</h2>
<p>Data: <?= e(implode(", ", $daftarNama)) ?></p>
<form method="GET">
    <label>Kata kunci <input name="cari" value="<?= e($kataCari) ?>"></label>
    <button type="submit">Cari</button>
</form>
<?php if ($kataCari !== ""): ?>
    <?php if ($hasilCari): ?>
        <div class="sukses">Ditemukan: <?= e(implode(", ", $hasilCari)) ?></div>
    <?php else: ?>
        <div class="error">Nama "<?= e($kataCari) ?>" tidak ditemukan.</div>
    <?php endif; ?>
<?php endif; ?>

<hr>
<h2>3. Format Rupiah</h2>
<form method="POST">
    <input type="hidden" name="aksi" value="rupiah">
    <label>Angka <input name="angka" type="number" step="any" value="<?= e($_POST['angka'] ?? '') ?>"></label>
    <button type="submit">Ubah ke Rupiah</button>
</form>
<?php if ($rupiah !== null): ?><div class="sukses">Hasil: <?= e($rupiah) ?></div><?php endif; ?>

<hr>
<h2>4. Validasi NIM (angka, 8–12 digit)</h2>
<form method="POST">
    <input type="hidden" name="aksi" value="nim">
    <label>NIM <input name="nim" value="<?= e($_POST['nim'] ?? '') ?>"></label>
    <button type="submit">Validasi</button>
</form>
<?php if ($nimOk !== null): ?>
    <div class="<?= $nimOk ? 'sukses' : 'error' ?>"><?= e($pesanNim) ?></div>
<?php endif; ?>

<hr>
<h2>5. Tombol Reset vs pemrosesan ulang di server</h2>
<form method="POST" action="">
    <label>Contoh isian <input name="contoh" value="<?= e($_POST['contoh'] ?? '') ?>"></label>
    <button type="submit">Kirim</button>
    <button type="reset">Reset</button>
</form>
<p><small>
    <strong>Reset</strong> hanya terjadi di browser: mengembalikan isian ke nilai awal HTML tanpa mengirim permintaan ke server.
    Setelah <strong>Kirim</strong>, server memproses ulang halaman dan bisa mengisi kembali input dari data POST
    (di sini terisi <code><?= e($_POST['contoh'] ?? '(kosong)') ?></code>), sehingga tombol Reset akan kembali ke nilai
    yang dirender server tersebut, bukan ke kosong.
</small></p>

<?php include "includes/footer.php"; ?>
