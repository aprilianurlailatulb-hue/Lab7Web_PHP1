<?php
$judul = "Latihan 7 — Pengolahan Data Sederhana";

$hasil = null;
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $barang       = trim($_POST["barang"] ?? "");
    $hargaInput   = $_POST["harga"] ?? "";
    $jumlahInput  = $_POST["jumlah"] ?? "";

    if ($barang === "" || !is_numeric($hargaInput) ||
        !ctype_digit((string)$jumlahInput) ||
        (float)$hargaInput < 0 || (int)$jumlahInput < 1) {
        $error = "Periksa kembali nama barang, harga, dan jumlah.";
    } else {
        $harga    = (float)$hargaInput;
        $jumlah   = (int)$jumlahInput;
        $subtotal = $harga * $jumlah;
    }
}

include "includes/header.php";
?>
<form method="POST">
    <label>Nama barang
        <input name="barang" value="<?= e($_POST['barang'] ?? '') ?>" required>
    </label>
    <label>Harga
        <input name="harga" type="number" min="0" step="0.01" value="<?= e($_POST['harga'] ?? '') ?>" required>
    </label>
    <label>Jumlah
        <input name="jumlah" type="number" min="1" step="1" value="<?= e($_POST['jumlah'] ?? '') ?>" required>
    </label>
    <button type="submit">Hitung</button>
</form>
<?php
if (isset($error)) {
    echo '<div class="error">' . htmlspecialchars($error) . "</div>";
} elseif (isset($subtotal)) {
    echo '<div class="sukses">';
    echo "<p>Barang: " . htmlspecialchars($barang) . "</p>";
    echo "<p>Subtotal: Rp" . number_format($subtotal, 0, ",", ".") . "</p>";
    echo "</div>";
}
?>
<p><small>Uji dengan harga desimal, jumlah nol, jumlah negatif, dan data tidak sesuai. Validasi HTML hanya membantu pengguna; validasi PHP tetap pemeriksaan utama.</small></p>
<?php include "includes/footer.php"; ?>
