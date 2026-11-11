# Lab7Web_PHP — Praktikum 7: Pemrograman Server-Side dengan PHP

## Tujuan
Memahami perbedaan client-side dan server-side, sintaks dasar PHP, function, pemrosesan form (GET/POST), serta validasi dan sanitasi input.

## Perangkat
Laptop/PC, web browser, Visual Studio Code, PHP, dan XAMPP.

## Cara Menjalankan
1. Salin folder `Lab7Web_PHP` ke `C:\xampp\htdocs\`.
2. Buka XAMPP Control Panel, klik **Start** pada **Apache**.
3. Buka browser: `http://localhost/Lab7Web_PHP/`
4. Jangan membuka file `.php` dengan klik ganda.

## Struktur File
```
Lab7Web_PHP/
├── index.php          # menu utama
├── latihan1.php       # menjalankan PHP
├── latihan2.php       # variabel, operator, percabangan
├── latihan3.php       # perulangan dan array
├── latihan4.php       # function
├── latihan5.php       # form GET
├── latihan6.php       # form POST
├── latihan7.php       # pengolahan data sederhana
├── miniproject.php    # formulir data mahasiswa
├── eksperimen.php     # 5 eksperimen mandiri
├── includes/          # helpers.php, header.php, footer.php
└── assets/style.css
```

## Ringkasan Hasil
(Isi sendiri setelah dijalankan, sertakan screenshot setiap latihan.)

## Kasus Uji Mini Project
| Kasus | Input | Hasil |
|---|---|---|
| Valid 1 | Andi, 12345678, Teknik Informatika, 3, andi@mail.com | Ringkasan tampil |
| Valid 2 | Siti, 20230001, Sistem Informasi, 14, siti@kampus.ac.id | Ringkasan tampil |
| Tidak valid 1 | (semua kosong) | Pesan error wajib diisi |
| Tidak valid 2 | email `andi@`, semester `15`, NIM `abc` | Pesan error email, semester, NIM |

## Jawaban Pertanyaan Evaluasi
(Isi sendiri dengan kata-kata Anda.)
