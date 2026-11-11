<?php
// =====================================================
//  DASHBOARD MAHASISWA PLUS  (fitur bonus)
//  - Data disimpan di SESSION (tanpa database)
//  - Tambah, hapus, cari, urutkan
//  - Statistik + grafik batang per program studi
//  - Kartu mahasiswa otomatis (bisa dicetak)
//  - Ekspor CSV, mode gelap (cookie), token CSRF
// =====================================================
session_start();
require_once __DIR__ . "/includes/helpers.php";

// ---------- Mode gelap/terang (disimpan di cookie) ----------
if (isset($_GET["tema"]) && in_array($_GET["tema"], ["terang", "gelap"], true)) {
    setcookie("tema", $_GET["tema"], time() + 86400 * 30, "/");
    $_COOKIE["tema"] = $_GET["tema"];
}
$tema = (($_COOKIE["tema"] ?? "terang") === "gelap") ? "gelap" : "terang";

// ---------- Token CSRF ----------
if (empty($_SESSION["csrf"])) {
    $_SESSION["csrf"] = bin2hex(random_bytes(16));
}
function csrfValid(): bool
{
    return hash_equals($_SESSION["csrf"], (string)($_POST["csrf"] ?? ""));
}

$_SESSION["mhs"] = $_SESSION["mhs"] ?? [];

// ---------- Ekspor CSV (harus sebelum ada output HTML) ----------
if (($_GET["export"] ?? "") === "csv") {
    header("Content-Type: text/csv; charset=UTF-8");
    header("Content-Disposition: attachment; filename=data_mahasiswa.csv");
    $out = fopen("php://output", "w");
    fwrite($out, "\xEF\xBB\xBF"); // BOM agar Excel membaca UTF-8
    fputcsv($out, ["Nama", "NIM", "Program Studi", "Semester", "Email", "Waktu Input"]);
    foreach ($_SESSION["mhs"] as $m) {
        // cegah CSV injection (sel diawali = + - @)
        $baris = [$m["nama"], $m["nim"], $m["prodi"], $m["semester"], $m["email"], $m["waktu"]];
        $baris = array_map(fn($v) => preg_match('/^[=+\-@]/', (string)$v) ? "'" . $v : $v, $baris);
        fputcsv($out, $baris);
    }
    fclose($out);
    exit;
}

// ---------- Pemrosesan form (POST) ----------
$error = [];
$nama = $nim = $prodi = $semesterInput = $email = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    if (!csrfValid()) {
        $error[] = "Token keamanan tidak valid. Muat ulang halaman lalu coba lagi.";
    } else {
        $aksi = $_POST["aksi"] ?? "";

        if ($aksi === "tambah") {
            $nama          = trim($_POST["nama"] ?? "");
            $nim           = trim($_POST["nim"] ?? "");
            $prodi         = trim($_POST["prodi"] ?? "");
            $semesterInput = $_POST["semester"] ?? "";
            $email         = trim($_POST["email"] ?? "");

            if ($nama === "") $error[] = "Nama wajib diisi.";
            if ($nim === "") {
                $error[] = "NIM wajib diisi.";
            } elseif (!ctype_digit($nim) || strlen($nim) < 8 || strlen($nim) > 12) {
                $error[] = "NIM harus berupa angka 8–12 digit.";
            } else {
                foreach ($_SESSION["mhs"] as $m) {
                    if ($m["nim"] === $nim) { $error[] = "NIM $nim sudah terdaftar."; break; }
                }
            }
            if ($prodi === "") $error[] = "Program studi wajib diisi.";
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $error[] = "Format email tidak valid.";
            if (!ctype_digit((string)$semesterInput) || (int)$semesterInput < 1 || (int)$semesterInput > 14) {
                $error[] = "Semester harus berupa angka 1–14.";
            }

            if (!$error) {
                $_SESSION["mhs"][] = [
                    "nama" => $nama, "nim" => $nim, "prodi" => $prodi,
                    "semester" => (int)$semesterInput, "email" => $email,
                    "waktu" => date("d-m-Y H:i"),
                ];
                $_SESSION["flash"] = "Data " . $nama . " berhasil ditambahkan.";
                header("Location: dashboard.php");
                exit;
            }
        } elseif ($aksi === "hapus") {
            $target = (string)($_POST["nim"] ?? "");
            $_SESSION["mhs"] = array_values(array_filter(
                $_SESSION["mhs"], fn($m) => $m["nim"] !== $target
            ));
            $_SESSION["flash"] = "Data dengan NIM $target dihapus.";
            header("Location: dashboard.php");
            exit;
        } elseif ($aksi === "contoh") {
            $contoh = [
                ["Alya Rahma", "20230001", "Teknik Informatika", 3, "alya@kampus.ac.id"],
                ["Bima Pratama", "20230002", "Teknik Informatika", 5, "bima@kampus.ac.id"],
                ["Citra Dewi", "20230003", "Sistem Informasi", 3, "citra@kampus.ac.id"],
                ["Doni Saputra", "20230004", "Sistem Informasi", 7, "doni@kampus.ac.id"],
                ["Eka Putri", "20230005", "Teknik Informatika", 1, "eka@kampus.ac.id"],
                ["Farhan Hidayat", "20230006", "Manajemen", 5, "farhan@kampus.ac.id"],
            ];
            $ada = array_column($_SESSION["mhs"], "nim");
            foreach ($contoh as [$n, $nm, $p, $s, $em]) {
                if (!in_array($nm, $ada, true)) {
                    $_SESSION["mhs"][] = ["nama" => $n, "nim" => $nm, "prodi" => $p,
                        "semester" => $s, "email" => $em, "waktu" => date("d-m-Y H:i")];
                }
            }
            $_SESSION["flash"] = "Data contoh dimuat.";
            header("Location: dashboard.php");
            exit;
        } elseif ($aksi === "kosongkan") {
            $_SESSION["mhs"] = [];
            $_SESSION["flash"] = "Semua data dikosongkan.";
            header("Location: dashboard.php");
            exit;
        }
    }
}

$flash = $_SESSION["flash"] ?? "";
unset($_SESSION["flash"]);

// ---------- Cari & urutkan (GET) ----------
$semua = $_SESSION["mhs"];
$cari  = trim($_GET["cari"] ?? "");
$urut  = in_array($_GET["urut"] ?? "", ["nama", "nim", "semester"], true) ? $_GET["urut"] : "";

$tampil = $semua;
if ($cari !== "") {
    $tampil = array_filter($semua, fn($m) =>
        stripos($m["nama"], $cari) !== false ||
        stripos($m["nim"], $cari) !== false ||
        stripos($m["prodi"], $cari) !== false
    );
}
$tampil = array_values($tampil);
if ($urut !== "") {
    usort($tampil, fn($a, $b) => $urut === "semester"
        ? $a["semester"] <=> $b["semester"]
        : strcasecmp($a[$urut], $b[$urut]));
}

// ---------- Statistik ----------
$total = count($semua);
$rataSemester = $total ? array_sum(array_column($semua, "semester")) / $total : 0;
$perProdi = [];
foreach ($semua as $m) {
    $k = ucwords(strtolower($m["prodi"]));
    $perProdi[$k] = ($perProdi[$k] ?? 0) + 1;
}
arsort($perProdi);
$maks = $perProdi ? max($perProdi) : 1;
$prodiTop = $perProdi ? array_key_first($perProdi) : "-";

// ---------- Kartu mahasiswa (GET ?kartu=NIM) ----------
$kartu = null;
if (isset($_GET["kartu"])) {
    foreach ($semua as $m) {
        if ($m["nim"] === (string)$_GET["kartu"]) { $kartu = $m; break; }
    }
}
function inisial(string $nama): string
{
    $kata = preg_split('/\s+/', trim($nama));
    $i = mb_strtoupper(mb_substr($kata[0], 0, 1));
    if (count($kata) > 1) $i .= mb_strtoupper(mb_substr(end($kata), 0, 1));
    return $i;
}

$judul = "Dashboard Mahasiswa Plus";
$kelasWrap = "lebar";
include "includes/header.php";
?>
<a class="btn kecil abu tema-toggle" href="?tema=<?= $tema === "gelap" ? "terang" : "gelap" ?>">
    <?= $tema === "gelap" ? "☀ Mode Terang" : "🌙 Mode Gelap" ?>
</a>
<p>Aplikasi mini untuk mengelola data mahasiswa. Data tersimpan di <b>session</b> PHP (hilang jika browser ditutup).</p>

<?php if ($flash): ?><div class="sukses"><?= e($flash) ?></div><?php endif; ?>
<?php if ($error): ?>
    <div class="error"><strong>Terdapat kesalahan:</strong>
        <ul><?php foreach ($error as $p): ?><li><?= e($p) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<?php if ($kartu): $hue = crc32($kartu["nim"]) % 360; ?>
    <h3>Kartu Mahasiswa</h3>
    <div class="kartu" style="background:linear-gradient(135deg,hsl(<?= $hue ?>,60%,35%),hsl(<?= ($hue + 50) % 360 ?>,70%,50%))">
        <div class="avatar"><?= e(inisial($kartu["nama"])) ?></div>
        <div class="univ">Kartu Tanda Mahasiswa</div>
        <h3><?= e($kartu["nama"]) ?></h3>
        <p>NIM: <b><?= e($kartu["nim"]) ?></b></p>
        <p><?= e($kartu["prodi"]) ?> — Semester <?= e($kartu["semester"]) ?></p>
        <p><?= e($kartu["email"]) ?></p>
    </div>
    <button class="kecil" onclick="window.print()">🖨 Cetak Kartu</button>
    <a class="btn kecil abu" href="dashboard.php">Tutup Kartu</a>
    <hr>
<?php endif; ?>

<h3>Statistik</h3>
<div class="grid">
    <div class="stat"><b><?= $total ?></b><small>Total mahasiswa</small></div>
    <div class="stat"><b><?= number_format($rataSemester, 1) ?></b><small>Rata-rata semester</small></div>
    <div class="stat"><b><?= count($perProdi) ?></b><small>Jumlah program studi</small></div>
    <div class="stat"><b style="font-size:1.1rem"><?= e($prodiTop) ?></b><small>Prodi terbanyak</small></div>
</div>
<?php foreach ($perProdi as $nm => $jml): ?>
    <div class="bar-row">
        <div class="label" title="<?= e($nm) ?>"><?= e($nm) ?></div>
        <div class="bar"><span style="width:<?= round($jml / $maks * 100) ?>%"></span></div>
        <div><?= $jml ?></div>
    </div>
<?php endforeach; ?>

<hr>
<div class="cols">
    <div>
        <h3>Tambah Mahasiswa</h3>
        <form method="POST" action="" novalidate>
            <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
            <input type="hidden" name="aksi" value="tambah">
            <label for="nama">Nama</label>
            <input id="nama" name="nama" value="<?= e($nama) ?>">
            <label for="nim">NIM (8–12 digit)</label>
            <input id="nim" name="nim" value="<?= e($nim) ?>">
            <label for="prodi">Program Studi</label>
            <input id="prodi" name="prodi" value="<?= e($prodi) ?>">
            <label for="semester">Semester (1–14)</label>
            <input id="semester" name="semester" value="<?= e($semesterInput) ?>">
            <label for="email">Email</label>
            <input id="email" name="email" value="<?= e($email) ?>">
            <button type="submit">Simpan</button>
            <button type="reset">Reset</button>
        </form>
        <form method="POST" style="display:inline">
            <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
            <input type="hidden" name="aksi" value="contoh">
            <button class="kecil abu" type="submit">Muat data contoh</button>
        </form>
    </div>

    <div>
        <h3>Daftar Mahasiswa (<?= count($tampil) ?>)</h3>
        <form method="GET" class="toolbar">
            <div><label for="cari">Cari</label>
                <input id="cari" name="cari" placeholder="nama / NIM / prodi" value="<?= e($cari) ?>"></div>
            <div><label for="urut">Urutkan</label>
                <select id="urut" name="urut">
                    <option value="">Urutan input</option>
                    <option value="nama" <?= $urut === "nama" ? "selected" : "" ?>>Nama (A-Z)</option>
                    <option value="nim" <?= $urut === "nim" ? "selected" : "" ?>>NIM</option>
                    <option value="semester" <?= $urut === "semester" ? "selected" : "" ?>>Semester</option>
                </select></div>
            <button type="submit">Terapkan</button>
        </form>

        <?php if (!$tampil): ?>
            <div class="kosong"><?= $total ? "Tidak ada data yang cocok." : "Belum ada data. Tambahkan atau muat data contoh." ?></div>
        <?php else: ?>
            <div style="overflow-x:auto">
            <table>
                <tr><th style="width:auto">Nama</th><th style="width:auto">NIM</th><th style="width:auto">Prodi</th><th style="width:auto">Smt</th><th style="width:auto">Aksi</th></tr>
                <?php foreach ($tampil as $m): ?>
                    <tr>
                        <td><?= e($m["nama"]) ?></td>
                        <td><?= e($m["nim"]) ?></td>
                        <td><?= e($m["prodi"]) ?></td>
                        <td><?= e($m["semester"]) ?></td>
                        <td style="white-space:nowrap">
                            <a class="btn kecil" href="?kartu=<?= urlencode($m["nim"]) ?>">Kartu</a>
                            <form method="POST" style="display:inline" onsubmit="return confirm('Hapus data ini?')">
                                <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
                                <input type="hidden" name="aksi" value="hapus">
                                <input type="hidden" name="nim" value="<?= e($m["nim"]) ?>">
                                <button class="kecil merah" type="submit">Hapus</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            </div>
        <?php endif; ?>

        <?php if ($total): ?>
            <a class="btn kecil" href="?export=csv">⬇ Ekspor CSV</a>
            <form method="POST" style="display:inline" onsubmit="return confirm('Kosongkan semua data?')">
                <input type="hidden" name="csrf" value="<?= e($_SESSION["csrf"]) ?>">
                <input type="hidden" name="aksi" value="kosongkan">
                <button class="kecil merah" type="submit">Kosongkan semua</button>
            </form>
        <?php endif; ?>
    </div>
</div>
<?php include "includes/footer.php"; ?>
