<?php
// ===== BAGIAN PEMROSESAN DATA (dipisah dari tampilan) =====
$judul = "Mini Project — Formulir Data Mahasiswa";
$data  = null;
$error = [];

// Nilai awal form (agar input tidak hilang jika ada error)
$nama = $nim = $prodi = $semesterInput = $email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $nama          = trim($_POST["nama"] ?? "");
    $nim           = trim($_POST["nim"] ?? "");
    $prodi         = trim($_POST["prodi"] ?? "");
    $semesterInput = $_POST["semester"] ?? "";
    $email         = trim($_POST["email"] ?? "");

    if ($nama === "")  $error[] = "Nama wajib diisi.";
    if ($nim === "")   $error[] = "NIM wajib diisi.";
    elseif (!ctype_digit($nim) || strlen($nim) < 8 || strlen($nim) > 12) {
        $error[] = "NIM harus berupa angka dengan panjang 8–12 digit.";
    }
    if ($prodi === "") $error[] = "Program studi wajib diisi.";

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error[] = "Format email tidak valid.";
    }

    if (!ctype_digit((string)$semesterInput) ||
        (int)$semesterInput < 1 || (int)$semesterInput > 14) {
        $error[] = "Semester harus berupa angka 1–14.";
    }

    if (!$error) {
        $data = compact("nama", "nim", "prodi", "email");
        $data["semester"] = (int)$semesterInput;
    }
}

// ===== BAGIAN TAMPILAN =====
include "includes/header.php";
?>

<?php if ($error): ?>
    <div class="error">
        <strong>Terdapat kesalahan:</strong>
        <ul>
            <?php foreach ($error as $pesan): ?>
                <li><?= e($pesan) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<?php if ($data): ?>
    <div class="sukses"><strong>Data berhasil diterima.</strong></div>
    <h3>Ringkasan Data Mahasiswa</h3>
    <table>
        <tr><th>Nama</th><td><?= e($data["nama"]) ?></td></tr>
        <tr><th>NIM</th><td><?= e($data["nim"]) ?></td></tr>
        <tr><th>Program Studi</th><td><?= e($data["prodi"]) ?></td></tr>
        <tr><th>Semester</th><td><?= e($data["semester"]) ?></td></tr>
        <tr><th>Email</th><td><?= e($data["email"]) ?></td></tr>
    </table>
    <a class="btn" href="miniproject.php">Isi Data Baru</a>
<?php else: ?>
    <form method="POST" action="" novalidate>
        <label for="nama">Nama</label>
        <input id="nama" name="nama" value="<?= e($nama) ?>">

        <label for="nim">NIM</label>
        <input id="nim" name="nim" value="<?= e($nim) ?>">

        <label for="prodi">Program Studi</label>
        <input id="prodi" name="prodi" value="<?= e($prodi) ?>">

        <label for="semester">Semester (1–14)</label>
        <input id="semester" name="semester" value="<?= e($semesterInput) ?>">

        <label for="email">Email</label>
        <input id="email" name="email" value="<?= e($email) ?>">

        <button type="submit">Kirim</button>
        <button type="reset">Reset</button>
    </form>
    <p><small>Atribut <code>novalidate</code> sengaja dipakai agar validasi server-side mudah diuji.</small></p>
<?php endif; ?>

<?php include "includes/footer.php"; ?>
