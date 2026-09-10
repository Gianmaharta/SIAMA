<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'Sistem Informasi Alih Media' ?></title>
</head>
<body style="font-family: sans-serif; margin: 20px;">

    <?= $this->include('layouts/header') ?>

    <div style="display: flex;">
        <!-- Sidebar Kiri -->
        <div style="width: 250px;">
            <?= $this->include('layouts/sidebar') ?>
        </div>

        <!-- Konten Utama Kanan -->
        <div style="flex-grow: 1; padding-left: 20px;">
            <?= $this->renderSection('content') ?>
        </div>
    </div>

    <?= $this->include('layouts/footer') ?>

</body>
</html>
