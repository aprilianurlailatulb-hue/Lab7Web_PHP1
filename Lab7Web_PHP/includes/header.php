<?php require_once __DIR__ . "/helpers.php"; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($judul ?? "Praktikum PHP") ?></title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body class="<?= e($tema ?? "terang") ?>">
<div class="wrap <?= e($kelasWrap ?? "") ?>">
    <a class="back" href="index.php">&larr; Kembali ke menu</a>
    <div class="card">
        <h1><?= e($judul ?? "Praktikum PHP") ?></h1>
