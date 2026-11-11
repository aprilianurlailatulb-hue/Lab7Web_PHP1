<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Praktikum PHP</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="wrap"><div class="card">
    <a class="back" href="index.php">&larr; Kembali ke menu</a>
    <h1>Belajar PHP</h1>
    <?php
    echo "<p>PHP berhasil dijalankan.</p>";
    echo "<p>Waktu server: " . date("d-m-Y H:i:s") . "</p>";
    ?>
</div></div>
</body>
</html>
