<?php
$judul = "Latihan 5 — Memproses Form dengan GET";
include "includes/header.php";
?>
<form method="GET" action="">
    <label for="kata">Kata kunci:</label>
    <input id="kata" name="kata" type="text">
    <button type="submit">Cari</button>
</form>
<?php
if (isset($_GET["kata"])) {
    $kata = trim($_GET["kata"]);
    echo '<div class="sukses">Kata kunci: ' . htmlspecialchars($kata, ENT_QUOTES, "UTF-8") . "</div>";
}
?>
<p><small>Amati perubahan URL setelah form dikirim. Jangan gunakan GET untuk mengirim kata sandi atau data sensitif.</small></p>
<?php include "includes/footer.php"; ?>
