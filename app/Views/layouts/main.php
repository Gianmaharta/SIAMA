<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $this->renderSection('title') ?? 'SIAMA - Sistem Informasi Alih Media Arsip' ?></title>
    
    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    
    <!-- Custom Dashboard CSS -->
    <link rel="stylesheet" href="<?= base_url('css/dashboard.css') ?>">
    <?= $this->renderSection('styles') ?>
</head>
<body>
    <div class="app-layout">
        <!-- Sidebar Kiri -->
        <?= $this->include('layouts/sidebar') ?>

        <!-- Area Konten Kanan -->
        <div class="main-wrapper">
            <!-- Top Navbar -->
            <?= $this->include('layouts/header') ?>

            <!-- Konten Halaman -->
            <main class="main-content">
                <?= $this->renderSection('content') ?>
            </main>

            <!-- Footer -->
            <?= $this->include('layouts/footer') ?>
        </div>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <?= $this->renderSection('scripts') ?>
</body>
</html>
