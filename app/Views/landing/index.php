<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $siteName    = !empty($settings['site_name']) ? $settings['site_name'] : 'SIAMA';
    $siteTagline = !empty($settings['site_tagline']) ? $settings['site_tagline'] : 'Sistem Informasi Alih Media Arsip';
    $instansi    = !empty($settings['instansi_name']) ? $settings['instansi_name'] : 'Pemerintah Kabupaten Buleleng';
    $desc        = !empty($settings['site_description']) ? $settings['site_description'] : 'Platform terpadu digitalisasi, autentikasi watermark, verifikasi berjenjang, dan manajemen Jadwal Retensi Arsip (JRA) dinamis di lingkungan Pemerintah Kabupaten Buleleng.';
    $logoPath    = !empty($settings['site_logo']) ? $settings['site_logo'] : 'logo siama1.png';
    if (file_exists(FCPATH . $logoPath)) {
        $logoUrl = base_url($logoPath);
    } elseif (file_exists(FCPATH . 'uploads/branding/' . basename($logoPath))) {
        $logoUrl = base_url('uploads/branding/' . basename($logoPath));
    } elseif (file_exists(FCPATH . 'Images/' . basename($logoPath))) {
        $logoUrl = base_url('Images/' . basename($logoPath));
    } elseif (file_exists(FCPATH . 'Images/logo siama1.png')) {
        $logoUrl = base_url('Images/logo siama1.png');
    } else {
        $logoUrl = base_url('logo siama1.png');
    }
    $heroBgUrl   = file_exists(FCPATH . 'Images/TINOL.jpeg') ? base_url('Images/TINOL.jpeg') : (file_exists(FCPATH . 'Images/TINOL.jpg') ? base_url('Images/TINOL.jpg') : '');
    ?>
    <title><?= esc($siteName) ?> &ndash; <?= esc($siteTagline) ?> | <?= esc($instansi) ?></title>
    
    <!-- Google Fonts Poppins -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- Custom Landing Page CSS -->
    <link rel="stylesheet" href="<?= base_url('css/landing.css?v=' . (file_exists(FCPATH . 'css/landing.css') ? filemtime(FCPATH . 'css/landing.css') : time())) ?>">
</head>
<body id="beranda">

    <!-- 1. NAVBAR KAPSUL FROSTED GLASS (PREMIUM GLASSMORPHISM) -->
    <header class="lp-navbar-wrapper" id="navbarWrapper">
        <nav class="lp-navbar" id="mainNavbar">
            <div class="d-flex align-items-center justify-content-between w-100 lp-nav-container">
                <!-- Bagian Kiri: Identitas & Logo -->
                <div class="d-flex align-items-center">
                    <a href="<?= base_url('/') ?>" class="d-flex align-items-center gap-3 text-decoration-none lp-brand-wrap">
                        <img src="<?= esc($logoUrl) ?>" alt="<?= esc($siteName) ?>" class="lp-brand-logo">
                        <div class="d-flex flex-column justify-content-center">
                            <span class="lp-brand-title"><?= esc($siteName) ?> Buleleng</span>
                            <span class="lp-brand-sub"><?= esc($siteTagline) ?></span>
                        </div>
                    </a>
                </div>

                <!-- Bagian Tengah: Menu Navigasi Desktop -->
                <div class="d-none d-lg-flex align-items-center lp-nav-center-wrap">
                    <div class="lp-nav-divider d-none d-xl-block"></div>
                    <nav class="d-flex align-items-center gap-1 lp-nav-menu" aria-label="Navigasi Utama">
                        <a href="#beranda" class="lp-nav-link active">Beranda</a>
                        <a href="#alur" class="lp-nav-link">Alur Alih Media</a>
                        <a href="#peran" class="lp-nav-link">Peran &amp; Otoritas</a>
                        <a href="#tata-kelola" class="lp-nav-link">Standar Tata Kelola</a>
                        <a href="#cari-arsip" class="lp-nav-link">Cari Arsip</a>
                        <a href="#faq" class="lp-nav-link">FAQ</a>
                    </nav>
                </div>

                <!-- Bagian Kanan: Tombol Aksi Dashboard & Hamburger -->
                <div class="d-flex align-items-center gap-2.5 lp-nav-right">
                    <?php if (!empty($isLoggedIn)) : ?>
                        <a href="<?= base_url('/dashboard') ?>" class="lp-btn-login" title="Akses Dashboard SIAMA">
                            <i class="bi bi-speedometer2"></i>
                            <span>Ke Dashboard</span>
                        </a>
                    <?php else : ?>
                        <a href="<?= base_url('/login') ?>" class="lp-btn-login" title="Login ke Dashboard SIAMA">
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Masuk</span>
                        </a>
                    <?php endif; ?>

                    <!-- Mobile Menu Button -->
                    <button class="lp-hamburger-btn d-lg-none" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavMenu" aria-controls="mobileNavMenu" aria-expanded="false" aria-label="Toggle navigation">
                        <i class="bi bi-list fs-4"></i>
                    </button>
                </div>
            </div>

            <!-- Mobile Menu Dropdown Panel (Frosted Glass) -->
            <div class="collapse d-lg-none w-100" id="mobileNavMenu">
                <div class="lp-mobile-menu-inner">
                    <a href="#beranda" class="lp-mobile-nav-link active" onclick="closeMobileNav(this)">Beranda</a>
                    <a href="#alur" class="lp-mobile-nav-link" onclick="closeMobileNav(this)">Alur Alih Media</a>
                    <a href="#peran" class="lp-mobile-nav-link" onclick="closeMobileNav(this)">Peran &amp; Otoritas</a>
                    <a href="#tata-kelola" class="lp-mobile-nav-link" onclick="closeMobileNav(this)">Standar Tata Kelola</a>
                    <a href="#cari-arsip" class="lp-mobile-nav-link" onclick="closeMobileNav(this)">Cari Arsip</a>
                    <a href="#faq" class="lp-mobile-nav-link" onclick="closeMobileNav(this)">FAQ</a>
                </div>
            </div>
        </nav>
    </header>

    <!-- 2. HERO SECTION DENGAN BACKGROUND TINOL.JPEG & NAVY OVERLAY -->
    <header class="lp-hero-section" style="<?= $heroBgUrl ? "background-image: url('" . esc($heroBgUrl) . "');" : "background-color: var(--lp-navy-dark);" ?>">
        <div class="lp-hero-overlay"></div>
        <div class="container text-center lp-hero-content">
            
            <!-- Judul Utama -->
            <h1 class="lp-hero-title mb-3">
                Layanan Alih Media Arsip
                <span class="d-block lp-hero-title-accent">Digital</span>
            </h1>

            <!-- Deskripsi Resmi -->
            <p class="lp-hero-desc">
                <?= esc($desc) ?>
            </p>

            <!-- Tombol Aksi Utama -->
            <div class="d-flex flex-wrap align-items-center justify-content-center gap-3">
                <?php if (!empty($isLoggedIn)) : ?>
                    <a href="<?= base_url('/dashboard') ?>" class="lp-btn-cta-primary">
                        <i class="bi bi-speedometer2"></i>
                        <span>Buka Portal Dashboard</span>
                    </a>
                <?php else : ?>
                    <a href="<?= base_url('/login') ?>" class="lp-btn-cta-primary">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Masuk ke SIAMA</span>
                    </a>
                <?php endif; ?>
                <a href="#bantuan" class="lp-btn-cta-outline">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Lihat Buku Panduan</span>
                </a>
            </div>

        </div>
    </header>

    <!-- 4. STATISTIK RINGKAS DARI DATABASE (HORIZONTAL CARD ROW) -->
    <section class="container lp-stat-section">
        <div class="row g-3 g-md-4">
            <!-- Card 1: Perangkat Daerah -->
            <div class="col-6 col-lg-3">
                <div class="lp-stat-card">
                    <div class="lp-stat-icon" style="background-color: #EFF6FF; color: #1E60EC;">
                        <i class="bi bi-bank2"></i>
                    </div>
                    <div>
                        <div class="lp-stat-num"><?= number_format($stats['total_opd'] ?? 0, 0, ',', '.') ?></div>
                        <div class="lp-stat-label">Perangkat Daerah</div>
                    </div>
                </div>
            </div>

            <!-- Card 2: Master Kode JRA -->
            <div class="col-6 col-lg-3">
                <div class="lp-stat-card">
                    <div class="lp-stat-icon" style="background-color: #ECFDF5; color: #10B981;">
                        <i class="bi bi-journal-bookmark-fill"></i>
                    </div>
                    <div>
                        <div class="lp-stat-num"><?= number_format($stats['total_jra'] ?? 0, 0, ',', '.') ?></div>
                        <div class="lp-stat-label">Master Kode JRA</div>
                    </div>
                </div>
            </div>

            <!-- Card 3: Arsip Digital -->
            <div class="col-6 col-lg-3">
                <div class="lp-stat-card">
                    <div class="lp-stat-icon" style="background-color: #F0F9FF; color: #0284C7;">
                        <i class="bi bi-folder-check"></i>
                    </div>
                    <div>
                        <div class="lp-stat-num"><?= number_format($stats['total_arsip'] ?? 0, 0, ',', '.') ?></div>
                        <div class="lp-stat-label">Arsip Digital</div>
                    </div>
                </div>
            </div>

            <!-- Card 4: Berita Acara Sah -->
            <div class="col-6 col-lg-3">
                <div class="lp-stat-card">
                    <div class="lp-stat-icon" style="background-color: #FFFBEB; color: #D97706;">
                        <i class="bi bi-award-fill"></i>
                    </div>
                    <div>
                        <div class="lp-stat-num"><?= number_format($stats['total_ba'] ?? 0, 0, ',', '.') ?></div>
                        <div class="lp-stat-label">Berita Acara Sah</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 5. ALUR KERJA SISTEM: 5 TAHAPAN ALIH MEDIA ARSIP (HORIZONTAL TIMELINE STEPPER DENGAN ANIMASI PERJALANAN) -->
    <section id="alur" class="lp-timeline-section">
        <div class="container">
            <div class="text-center mx-auto mb-4" style="max-width: 680px;">
                <h2 class="lp-timeline-main-title">5 Tahapan Alih Media Arsip di SIAMA</h2>
                <p class="lp-timeline-main-sub">
                    Alur terstandar mulai dari penerbitan surat tugas hingga pengesahan Berita Acara dan penilaian arsip.
                </p>
            </div>

            <!-- Horizontal Stepper Wrapper -->
            <div class="lp-timeline-stepper" id="workflowStepper">
                <!-- Connecting Line Track -->
                <div class="lp-timeline-track d-none d-md-block">
                    <div class="lp-timeline-track-base"></div>
                    <div class="lp-timeline-track-progress" id="stepperProgressBar"></div>
                    <div class="lp-timeline-traveling-dot" id="stepperTravelDot"></div>
                </div>

                <!-- 5 Stepper Items -->
                <div class="row g-4 lp-timeline-row">
                    <!-- Step 1: Penerbitan SPT -->
                    <div class="col-12 col-md lp-timeline-col" data-step="0">
                        <div class="lp-timeline-item active" id="timelineItem0">
                            <div class="lp-timeline-node-wrap">
                                <div class="lp-timeline-node active" id="timelineNode0">
                                    <span>01</span>
                                </div>
                            </div>
                            <h4 class="lp-timeline-title">Penerbitan SPT</h4>
                            <p class="lp-timeline-desc">
                                Pimpinan OPD menerbitkan surat perintah tugas (SPT) pelaksanaan alih media arsip.
                            </p>
                            <div class="lp-timeline-badge-wrap">
                                <span class="lp-timeline-badge">Oleh: Pimpinan OPD</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 2: Penugasan Arsiparis -->
                    <div class="col-12 col-md lp-timeline-col" data-step="1">
                        <div class="lp-timeline-item" id="timelineItem1">
                            <div class="lp-timeline-node-wrap">
                                <div class="lp-timeline-node" id="timelineNode1">
                                    <span>02</span>
                                </div>
                            </div>
                            <h4 class="lp-timeline-title">Penugasan Arsiparis</h4>
                            <p class="lp-timeline-desc">
                                Kepala Bidang menugaskan Arsiparis pelaksana sesuai SPT yang diterbitkan.
                            </p>
                            <div class="lp-timeline-badge-wrap">
                                <span class="lp-timeline-badge">Oleh: Kepala Bidang</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Unggah & Watermark -->
                    <div class="col-12 col-md lp-timeline-col" data-step="2">
                        <div class="lp-timeline-item" id="timelineItem2">
                            <div class="lp-timeline-node-wrap">
                                <div class="lp-timeline-node" id="timelineNode2">
                                    <span>03</span>
                                </div>
                            </div>
                            <h4 class="lp-timeline-title">Unggah &amp; Watermark</h4>
                            <p class="lp-timeline-desc">
                                Arsiparis mengunggah dokumen digital, menambahkan watermark, dan menyusun draf Berita Acara.
                            </p>
                            <div class="lp-timeline-badge-wrap">
                                <span class="lp-timeline-badge">Oleh: Arsiparis</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 4: Verifikasi Hasil -->
                    <div class="col-12 col-md lp-timeline-col" data-step="3">
                        <div class="lp-timeline-item" id="timelineItem3">
                            <div class="lp-timeline-node-wrap">
                                <div class="lp-timeline-node" id="timelineNode3">
                                    <span>04</span>
                                </div>
                            </div>
                            <h4 class="lp-timeline-title">Verifikasi Hasil</h4>
                            <p class="lp-timeline-desc">
                                Kepala Bidang memeriksa kelengkapan berkas digital dan memverifikasi draf Berita Acara.
                            </p>
                            <div class="lp-timeline-badge-wrap">
                                <span class="lp-timeline-badge">Oleh: Kepala Bidang</span>
                            </div>
                        </div>
                    </div>

                    <!-- Step 5: Pengesahan & Evaluasi (Final Accent) -->
                    <div class="col-12 col-md lp-timeline-col" data-step="4">
                        <div class="lp-timeline-item lp-timeline-item-final" id="timelineItem4">
                            <div class="lp-timeline-node-wrap">
                                <div class="lp-timeline-node lp-timeline-node-final" id="timelineNode4">
                                    <span>05</span>
                                </div>
                            </div>
                            <h4 class="lp-timeline-title">Pengesahan &amp; Evaluasi</h4>
                            <p class="lp-timeline-desc">
                                Pimpinan mengesahkan Berita Acara dan menetapkan penilaian prioritas arsip.
                            </p>
                            <div class="lp-timeline-badge-wrap">
                                <span class="lp-timeline-badge lp-timeline-badge-final">Oleh: Pimpinan OPD</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. PERAN DAN PENGGUNA SISTEM -->
    <section id="peran" style="background-color: #FFFFFF; padding-top: 24px !important; padding-bottom: 24px !important;">
        <div class="container">
            <div class="text-center mx-auto mb-4" style="max-width: 680px;">
                <h2 class="lp-section-title">Siapa Saja yang Menggunakan Aplikasi SIAMA?</h2>
                <p class="lp-section-sub">
                    Sistem dirancang untuk mendukung kolaborasi antar peran dalam proses alih media arsip di lingkungan Pemerintah Kabupaten Buleleng.
                </p>
            </div>

            <!-- Baris Atas: 3 Card Peran -->
            <div class="row g-3 g-lg-4 mb-3 mb-lg-4">
                <!-- 1. Pimpinan OPD -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="lp-role-card">
                        <div class="lp-role-icon">
                            <i class="bi bi-people-fill"></i>
                        </div>
                        <div class="lp-role-content">
                            <h3 class="lp-role-title">Pimpinan OPD</h3>
                            <p class="lp-role-desc">
                                Menerbitkan SPT alih media arsip, mengesahkan Berita Acara, dan memberikan penilaian.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 2. Kepala Bidang -->
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="lp-role-card">
                        <div class="lp-role-icon">
                            <i class="bi bi-briefcase-fill"></i>
                        </div>
                        <div class="lp-role-content">
                            <h3 class="lp-role-title">Kepala Bidang</h3>
                            <p class="lp-role-desc">
                                Menunjuk Arsiparis pelaksana, memeriksa kelengkapan berkas digital, dan memverifikasi hasil alih media.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 3. Arsiparis Pelaksana -->
                <div class="col-12 col-md-12 col-lg-4">
                    <div class="lp-role-card">
                        <div class="lp-role-icon">
                            <i class="bi bi-file-earmark-text-fill"></i>
                        </div>
                        <div class="lp-role-content">
                            <h3 class="lp-role-title">Arsiparis Pelaksana</h3>
                            <p class="lp-role-desc">
                                Melakukan identifikasi arsip, alih media, mengunggah dokumen digital, memberikan watermark, dan menyusun draf Berita Acara.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Baris Bawah: 2 Card Peran (Admin OPD & Admin Pemkab) -->
            <div class="row g-3 g-lg-4">
                <!-- 4. Admin OPD -->
                <div class="col-12 col-md-6">
                    <div class="lp-role-card">
                        <div class="lp-role-icon">
                            <i class="bi bi-gear-fill"></i>
                        </div>
                        <div class="lp-role-content">
                            <h3 class="lp-role-title">Admin OPD</h3>
                            <p class="lp-role-desc">
                                Mengelola pengguna di lingkup OPD, mengelola master data, dan memantau progres alih media arsip.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- 5. Admin Pemkab -->
                <div class="col-12 col-md-6">
                    <div class="lp-role-card">
                        <div class="lp-role-icon">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="lp-role-content">
                            <h3 class="lp-role-title">Admin Pemkab</h3>
                            <p class="lp-role-desc">
                                Mengelola data master JRA dan klasifikasi, memverifikasi konfigurasi sistem, serta memantau progres seluruh OPD.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. STANDAR TATA KELOLA KEARSIPAN (CLEAN WHITE CARD CONTAINER) -->
    <section id="tata-kelola" style="background-color: #FFFFFF; padding-top: 24px !important; padding-bottom: 24px !important;">
        <div class="container">
            <div class="lp-governance-box">
                <div class="text-center mx-auto mb-4 pb-2" style="max-width: 760px;">
                    <h2 class="lp-gov-title">
                        Tata Kelola Alih Media Arsip
                        <span class="d-block lp-gov-subtitle">Terstandar &amp; Teruji</span>
                    </h2>
                    <p class="lp-gov-desc">
                        Diselenggarakan selaras dengan pedoman Arsip Nasional Republik Indonesia (ANRI) dan Peraturan Bupati Buleleng tentang Penyelenggaraan SPBE di Lingkungan Pemerintah Kabupaten Buleleng.
                    </p>
                </div>

                <!-- 4 Kotak Tata Kelola Horizontal -->
                <div class="row g-3 g-xl-4">
                    <!-- 1. Penugasan SPT -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="lp-gov-card">
                            <div class="lp-gov-card-icon">
                                <i class="bi bi-file-earmark-check"></i>
                            </div>
                            <div class="lp-gov-card-body">
                                <h3 class="lp-gov-card-title">Penugasan SPT</h3>
                                <p class="lp-gov-card-desc">Didasari wewenang sah Kepala Perangkat Daerah.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 2. Jadwal Retensi (JRA) -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="lp-gov-card">
                            <div class="lp-gov-card-icon">
                                <i class="bi bi-layers"></i>
                            </div>
                            <div class="lp-gov-card-body">
                                <h3 class="lp-gov-card-title">Jadwal Retensi (JRA)</h3>
                                <p class="lp-gov-card-desc">Otomatisasi masa simpan aktif dan inaktif dokumen.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 3. TTE & Watermark -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="lp-gov-card">
                            <div class="lp-gov-card-icon">
                                <i class="bi bi-shield"></i>
                            </div>
                            <div class="lp-gov-card-body">
                                <h3 class="lp-gov-card-title">TTE &amp; Watermark</h3>
                                <p class="lp-gov-card-desc">Proteksi dokumen dengan logo instansi dan watermark PDF.</p>
                            </div>
                        </div>
                    </div>

                    <!-- 4. Berita Acara Otentik -->
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="lp-gov-card">
                            <div class="lp-gov-card-icon">
                                <i class="bi bi-award"></i>
                            </div>
                            <div class="lp-gov-card-body">
                                <h3 class="lp-gov-card-title">Berita Acara Otentik</h3>
                                <p class="lp-gov-card-desc">Naskah legalitas alih media arsip resmi dan akuntabel.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FITUR BARU: SECTION CARI ARSIP INTERAKTIF -->
    <section id="cari-arsip" style="background-color: #FFFFFF; padding-top: 24px !important; padding-bottom: 24px !important;">
        <div class="container">
            <div class="text-center mx-auto mb-4" style="max-width: 660px;">
                <h2 class="lp-section-title">Cari Arsip</h2>
                <p class="lp-section-sub">
                    Temukan informasi arsip berdasarkan kata kunci atau identitas arsip yang tersedia.
                </p>
            </div>

            <!-- Form Pencarian Interaktif -->
            <div class="mx-auto" style="max-width: 760px;">
                <div class="bg-white p-3.5 p-md-4 rounded-4 border shadow-sm mb-4">
                    <form id="dedicatedSearchForm" onsubmit="executeDedicatedSearch(event)">
                        <label for="dedicatedSearchInput" class="form-label fw-semibold text-dark small mb-2">
                            Kata Kunci atau Nomor Arsip
                        </label>
                        <div class="lp-search-input-wrap">
                            <i class="bi bi-search text-secondary ms-1"></i>
                            <input 
                                type="text" 
                                id="dedicatedSearchInput" 
                                class="lp-search-input" 
                                placeholder="Ketik nomor arsip, nomor BA, OPD, atau uraian naskah..." 
                                value="<?= esc($searchKeyword ?? '') ?>"
                                autocomplete="off"
                            >
                            <button type="submit" class="lp-btn-search" id="btnDedicatedSearch">
                                <span class="spinner-border spinner-border-sm d-none" id="dedicatedLoadingSpinner" role="status" aria-hidden="true"></span>
                                <span id="dedicatedSearchText"><i class="bi bi-search me-1"></i> Cari Arsip</span>
                            </button>
                        </div>
                    </form>
                    <div class="form-text small text-muted mt-2 d-flex align-items-center justify-content-between flex-wrap gap-2">
                        <span><i class="bi bi-shield-check text-success me-1"></i> Pencarian hanya menampilkan metadata naskah terverifikasi.</span>
                        <span class="text-secondary">Akses file digital memerlukan autentikasi login.</span>
                    </div>
                </div>

                <!-- Wadah Hasil Pencarian -->
                <div id="dedicatedResultsArea">
                    <?php if (!empty($searchPerformed)) : ?>
                        <?php if (!empty($searchResults)) : ?>
                            <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                                <span class="fw-bold text-dark small">Hasil Pencarian untuk: "<?= esc($searchKeyword) ?>"</span>
                                <span class="badge bg-primary-subtle text-primary"><?= count($searchResults) ?> Arsip Ditemukan</span>
                            </div>
                            <?php foreach ($searchResults as $item) : ?>
                                <div class="lp-archive-result-card">
                                    <!-- Baris 1: Identitas Arsip -->
                                    <div class="lp-card-identity-row">
                                        <span class="lp-card-no-arsip"><?= esc($item['nomor_arsip']) ?></span>
                                        <?php if (!empty($item['nomor_ba']) && $item['nomor_ba'] !== '-') : ?>
                                            <span class="lp-card-sep">|</span>
                                            <span class="lp-card-no-ba">BA: <?= esc($item['nomor_ba']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Baris 2: Judul Arsip -->
                                    <h3 class="lp-card-archive-title">
                                        <?= esc($item['nama_arsip']) ?>
                                    </h3>

                                    <!-- Baris 4: Informasi OPD -->
                                    <div class="lp-card-opd-row">
                                        <span class="lp-card-opd"><?= esc($item['nama_opd']) ?></span>
                                        <?php if (!empty($item['nama_bidang']) && $item['nama_bidang'] !== '-') : ?>
                                            <span class="lp-card-dot">&bull;</span>
                                            <span class="lp-card-bidang"><?= esc($item['nama_bidang']) ?></span>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Baris 5: Kurun Waktu -->
                                    <div class="lp-card-year-row">
                                        <span>Kurun Waktu: <strong class="lp-card-year-val"><?= esc($item['kurun_waktu']) ?></strong></span>
                                    </div>

                                    <!-- Baris 6: Informasi Klasifikasi dan Aksi -->
                                    <div class="lp-card-bottom-bar">
                                        <div class="lp-card-klasifikasi">
                                            <span class="lp-card-klasifikasi-label">Klasifikasi:</span>
                                            <span class="lp-card-klasifikasi-val"><?= esc($item['kode_klasifikasi']) ?></span>
                                        </div>
                                        <div class="lp-card-action">
                                            <a href="<?= !empty($isLoggedIn) ? base_url('/arsip') : base_url('/login') ?>" class="lp-btn-card-action">
                                                <span>Buka di Dashboard</span>
                                                <i class="bi bi-arrow-right"></i>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php else : ?>
                            <!-- Empty State -->
                            <div class="text-center py-5 px-3 bg-white rounded-4 border">
                                <div class="rounded-circle bg-light text-secondary d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 24px;">
                                    <i class="bi bi-search"></i>
                                </div>
                                <h6 class="fw-bold text-dark mb-1">Arsip Tidak Ditemukan</h6>
                                <p class="text-secondary small mb-3" style="max-width: 440px; margin: 0 auto;">
                                    Tidak ditemukan arsip yang cocok dengan kata kunci "<strong><?= esc($searchKeyword ?? '') ?></strong>". Pastikan nomor arsip atau kata kunci yang dimasukkan sudah tepat.
                                </p>
                                <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1.5 rounded-3" onclick="document.getElementById('dedicatedSearchInput').focus();">
                                    Coba Kata Kunci Lain
                                </button>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. PERTANYAAN YANG SERING DIAJUKAN (FAQ) -->
    <section id="faq" style="background-color: #FFFFFF; padding-top: 24px !important; padding-bottom: 56px !important;">
        <div class="container">
            <div class="text-center mx-auto mb-4 pb-2" style="max-width: 680px;">
                <h2 class="lp-section-title">Pertanyaan yang Sering Diajukan</h2>
                <p class="lp-section-sub">
                    Informasi penting dan panduan praktis terkait alih media arsip digital di lingkungan Pemerintah Kabupaten Buleleng.
                </p>
            </div>

            <div class="mx-auto" style="max-width: 780px;">
                <div class="accordion lp-faq-accordion" id="faqAccordion">
                    <!-- FAQ 1 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faqHeading1">
                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse1" aria-expanded="true" aria-controls="faqCollapse1">
                                Apa dasar hukum pelaksanaan alih media arsip melalui aplikasi SIAMA?
                            </button>
                        </h3>
                        <div id="faqCollapse1" class="accordion-collapse collapse show" aria-labelledby="faqHeading1">
                            <div class="accordion-body">
                                Pelaksanaan alih media arsip didasarkan pada pedoman Arsip Nasional Republik Indonesia (ANRI) dan Peraturan Bupati Buleleng tentang Penyelenggaraan Sistem Pemerintahan Berbasis Elektronik (SPBE), yang menjamin naskah digital memiliki keabsahan hukum dan akuntabilitas kearsipan yang sama dengan arsip fisik aslinya.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 2 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faqHeading2">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse2" aria-expanded="false" aria-controls="faqCollapse2">
                                Apakah dokumen fisik boleh langsung dimusnahkan setelah dialihmediakan?
                            </button>
                        </h3>
                        <div id="faqCollapse2" class="accordion-collapse collapse" aria-labelledby="faqHeading2">
                            <div class="accordion-body">
                                Tidak. Dokumen fisik tidak boleh dimusnahkan secara sembarangan. Proses penyusutan atau pemusnahan arsip fisik tetap wajib berpedoman pada Jadwal Retensi Arsip (JRA), penilaian tim kearsipan, serta penerbitan Berita Acara Pemusnahan yang disahkan oleh Pimpinan Perangkat Daerah dan Lembaga Kearsipan Daerah.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 3 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faqHeading3">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse3" aria-expanded="false" aria-controls="faqCollapse3">
                                Bagaimana cara memastikan keaslian naskah digital hasil alih media?
                            </button>
                        </h3>
                        <div id="faqCollapse3" class="accordion-collapse collapse" aria-labelledby="faqHeading3">
                            <div class="accordion-body">
                                Setiap dokumen hasil alih media dibubuhi watermark autentikasi instansi Pemerintah Kabupaten Buleleng, diverifikasi melalui alur berjenjang (Kepala Bidang dan Pimpinan OPD), serta diikat dengan Berita Acara (BA) Alih Media resmi bernomor unik.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 4 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faqHeading4">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse4" aria-expanded="false" aria-controls="faqCollapse4">
                                Siapa saja yang memiliki hak akses untuk mengunduh naskah digital utuh?
                            </button>
                        </h3>
                        <div id="faqCollapse4" class="accordion-collapse collapse" aria-labelledby="faqHeading4">
                            <div class="accordion-body">
                                Masyarakat umum dapat melihat ringkasan metadata arsip yang bersifat publik melalui fitur Cari Arsip. Namun, pengunduhan berkas naskah digital utuh dibatasi khusus bagi ASN Perangkat Daerah pengelola dan pihak berwenang yang telah melakukan login dengan akun terverifikasi.
                            </div>
                        </div>
                    </div>

                    <!-- FAQ 5 -->
                    <div class="accordion-item">
                        <h3 class="accordion-header" id="faqHeading5">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faqCollapse5" aria-expanded="false" aria-controls="faqCollapse5">
                                Ke mana harus berkonsultasi jika mengalami kendala dalam penggunaan sistem?
                            </button>
                        </h3>
                        <div id="faqCollapse5" class="accordion-collapse collapse" aria-labelledby="faqHeading5">
                            <div class="accordion-body">
                                Anda dapat menghubungi Tim Layanan Kearsipan Dinas Kearsipan dan Perpustakaan Daerah Kabupaten Buleleng melalui kontak resmi yang tertera di bagian footer halaman ini pada hari dan jam kerja.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 10. FOOTER NAVY GELAP DENGAN INFORMASI RESMI & MITRA SPBE -->
    <footer id="kontak" class="lp-footer-section">
        <div class="container">
            <div class="row g-4 pb-4 border-bottom border-secondary border-opacity-25">
                <!-- Kolom 1: Profil SIAMA Buleleng -->
                <div class="col-12 col-lg-5">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= esc($logoUrl) ?>" alt="<?= esc($siteName) ?>" style="height: 44px; max-width: 150px; object-fit: contain;">
                        <div>
                            <div class="fw-bold text-white fs-5 lh-1"><?= esc($siteName) ?> Buleleng</div>
                            <div class="small text-white-50 mt-1"><?= esc($siteTagline) ?></div>
                        </div>
                    </div>
                    <p class="small text-white-50 mb-3" style="max-width: 440px; line-height: 1.7; text-align: justify;">
                        <?= esc($desc) ?>
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <?php if (!empty($settings['social_website'])) : ?>
                            <a href="<?= esc($settings['social_website']) ?>" target="_blank" rel="noopener" class="lp-social-btn" title="Website Resmi">
                                <i class="bi bi-globe2"></i>
                            </a>
                        <?php else : ?>
                            <a href="https://dapda.bulelengkab.go.id" target="_blank" rel="noopener" class="lp-social-btn" title="Website Resmi DAPDA">
                                <i class="bi bi-globe2"></i>
                            </a>
                        <?php endif; ?>
                        <a href="https://instagram.com/pemkabbuleleng" target="_blank" rel="noopener" class="lp-social-btn" title="Instagram">
                            <i class="bi bi-instagram"></i>
                        </a>
                        <a href="https://facebook.com/pemkabbuleleng" target="_blank" rel="noopener" class="lp-social-btn" title="Facebook">
                            <i class="bi bi-facebook"></i>
                        </a>
                    </div>
                </div>

                <!-- Kolom 2: Kontak & Alamat Layanan -->
                <div class="col-12 col-sm-6 col-lg-3">
                    <h6 class="fw-bold text-white mb-3" style="font-size: 13px; letter-spacing: 0.5px;">KONTAK &amp; ALAMAT LAYANAN</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2.5 small text-white-50 mb-0">
                        <li class="d-flex align-items-start gap-2">
                            <i class="bi bi-geo-alt-fill text-info mt-1"></i>
                            <span><?= esc($settings['contact_address'] ?? 'Jl. Mayor Metra No. 8, Singaraja, Bali 81116') ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-telephone-fill text-info"></i>
                            <span><?= esc($settings['contact_phone'] ?? '(0362) 22411') ?></span>
                        </li>
                        <li class="d-flex align-items-center gap-2">
                            <i class="bi bi-envelope-fill text-info"></i>
                            <span><?= esc($settings['contact_email'] ?? 'dapda@bulelengkab.go.id') ?></span>
                        </li>
                    </ul>
                </div>

                <!-- Kolom 3: Didukung Oleh (BSrE BSSN & Diskominfosanti) -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <h6 class="fw-bold text-white mb-3" style="font-size: 13px; letter-spacing: 0.5px;">DIDUKUNG OLEH</h6>

                    <div class="lp-footer-partner-card">
                        <div class="lp-footer-partner-logo-wrap">
                            <img src="<?= base_url('Images/logo_bsre.png') ?>" alt="BSrE BSSN" class="lp-footer-partner-logo">
                        </div>
                        <div>
                            <div class="fw-bold text-white small">BSrE BSSN</div>
                            <div class="text-white-50" style="font-size: 11px;">Balai Sertifikasi Elektronik</div>
                        </div>
                    </div>

                    <div class="lp-footer-partner-card">
                        <div class="lp-footer-partner-logo-wrap">
                            <img src="<?= base_url('Images/Logo_Kominfosanti.jpg') ?>" alt="Diskominfosanti" class="lp-footer-partner-logo">
                        </div>
                        <div>
                            <div class="fw-bold text-white small">Diskominfosanti</div>
                            <div class="text-white-50" style="font-size: 11px;">Infrastruktur SPBE Buleleng</div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Copyright & Legal Links -->
            <div class="pt-4 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 small text-white-50">
                <div>&copy; <?= date('Y') ?> Pemerintah Kabupaten Buleleng. Hak Cipta Dilindungi Undang-Undang.</div>
                <div class="d-flex align-items-center gap-3">
                    <a href="javascript:void(0)" class="text-white-50 text-decoration-none">Kebijakan Privasi</a>
                    <span>&bull;</span>
                    <a href="javascript:void(0)" class="text-white-50 text-decoration-none">Syarat &amp; Ketentuan</a>
                </div>
            </div>
        </div>
    </footer>

    <!-- Bootstrap 5 Bundle JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Client-side Interactive Search Script -->
    <script>
    function escapeHtml(str) {
        if (!str) return '';
        return String(str)
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
    }

    function renderArchiveCard(item, isLoggedIn) {
        const hasBa = item.nomor_ba && item.nomor_ba !== '-';
        const baHtml = hasBa ? `<span class="lp-card-sep">|</span><span class="lp-card-no-ba">BA: ${escapeHtml(item.nomor_ba)}</span>` : '';
        const hasBidang = item.nama_bidang && item.nama_bidang !== '-';
        const bidangHtml = hasBidang ? `<span class="lp-card-dot">&bull;</span><span class="lp-card-bidang">${escapeHtml(item.nama_bidang)}</span>` : '';
        const actionUrl = isLoggedIn ? '<?= base_url('/arsip') ?>' : '<?= base_url('/login') ?>';

        return `
            <div class="lp-archive-result-card">
                <!-- Baris 1: Identitas Arsip -->
                <div class="lp-card-identity-row">
                    <span class="lp-card-no-arsip">${escapeHtml(item.nomor_arsip)}</span>
                    ${baHtml}
                </div>

                <!-- Baris 2: Judul Arsip -->
                <h3 class="lp-card-archive-title">
                    ${escapeHtml(item.nama_arsip)}
                </h3>

                <!-- Baris 4: Informasi OPD -->
                <div class="lp-card-opd-row">
                    <span class="lp-card-opd">${escapeHtml(item.nama_opd)}</span>
                    ${bidangHtml}
                </div>

                <!-- Baris 5: Kurun Waktu -->
                <div class="lp-card-year-row">
                    <span>Kurun Waktu: <strong class="lp-card-year-val">${escapeHtml(item.kurun_waktu)}</strong></span>
                </div>

                <!-- Baris 6: Informasi Klasifikasi dan Aksi -->
                <div class="lp-card-bottom-bar">
                    <div class="lp-card-klasifikasi">
                        <span class="lp-card-klasifikasi-label">Klasifikasi:</span>
                        <span class="lp-card-klasifikasi-val">${escapeHtml(item.kode_klasifikasi)}</span>
                    </div>
                    <div class="lp-card-action">
                        <a href="${actionUrl}" class="lp-btn-card-action">
                            <span>Buka di Dashboard</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    }

    // Pencarian dari Dedicated Section #cari-arsip
    function executeDedicatedSearch(event) {
        event.preventDefault();
        const input = document.getElementById('dedicatedSearchInput');
        const spinner = document.getElementById('dedicatedLoadingSpinner');
        const resultsArea = document.getElementById('dedicatedResultsArea');
        const q = input ? input.value.trim() : '';

        if (!q || q.length < 2) {
            alert('Silakan masukkan minimal 2 karakter untuk mencari arsip.');
            if (input) input.focus();
            return;
        }

        if (spinner) spinner.classList.remove('d-none');

        fetch(`<?= base_url('/cari-arsip') ?>?q=${encodeURIComponent(q)}`, {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(response => response.json())
        .then(res => {
            if (spinner) spinner.classList.add('d-none');
            if (resultsArea) {
                if (res.status === 'success' && res.data && res.data.length > 0) {
                    let html = `
                        <div class="d-flex align-items-center justify-content-between mb-3 px-1">
                            <span class="fw-bold text-dark small">Hasil Pencarian untuk: "${res.keyword}"</span>
                            <span class="badge bg-primary-subtle text-primary">${res.count} Arsip Ditemukan</span>
                        </div>
                    `;
                    res.data.forEach(item => {
                        html += renderArchiveCard(item, <?= !empty($isLoggedIn) ? 'true' : 'false' ?>);
                    });
                    resultsArea.innerHTML = html;
                } else {
                    resultsArea.innerHTML = `
                        <div class="text-center py-5 px-3 bg-white rounded-4 border">
                            <div class="rounded-circle bg-light text-secondary d-inline-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; font-size: 24px;">
                                <i class="bi bi-search"></i>
                            </div>
                            <h6 class="fw-bold text-dark mb-1">Arsip Tidak Ditemukan</h6>
                            <p class="text-secondary small mb-3" style="max-width: 440px; margin: 0 auto;">
                                Tidak ditemukan arsip yang cocok dengan kata kunci "<strong>${q}</strong>". Pastikan nomor arsip atau kata kunci yang dimasukkan sudah tepat.
                            </p>
                            <button type="button" class="btn btn-sm btn-outline-secondary px-3 py-1.5 rounded-3" onclick="document.getElementById('dedicatedSearchInput').focus();">
                                Coba Kata Kunci Lain
                            </button>
                        </div>
                    `;
                }
            }
        })
        .catch(err => {
            if (spinner) spinner.classList.add('d-none');
            console.error('Dedicated search error:', err);
        });
    }

    // Navbar Scroll & Glassmorphism Transition
    window.addEventListener('scroll', function() {
        const navbar = document.getElementById('mainNavbar');
        if (navbar) {
            if (window.scrollY > 30) {
                navbar.classList.add('scrolled');
            } else {
                navbar.classList.remove('scrolled');
            }
        }
    }, { passive: true });

    // Active Nav Indicator on Click
    document.querySelectorAll('.lp-nav-link').forEach(link => {
        link.addEventListener('click', function() {
            document.querySelectorAll('.lp-nav-link').forEach(l => l.classList.remove('active'));
            this.classList.add('active');
        });
    });

    // Mobile Menu Dropdown Handler
    const mobileMenu = document.getElementById('mobileNavMenu');
    if (mobileMenu) {
        mobileMenu.addEventListener('show.bs.collapse', function() {
            const navbar = document.getElementById('mainNavbar');
            if (navbar) navbar.classList.add('menu-open');
        });
        mobileMenu.addEventListener('hidden.bs.collapse', function() {
            const navbar = document.getElementById('mainNavbar');
            if (navbar) navbar.classList.remove('menu-open');
        });
    }
    function closeMobileNav(linkEl) {
        if (linkEl) {
            document.querySelectorAll('.lp-mobile-nav-link').forEach(l => l.classList.remove('active'));
            linkEl.classList.add('active');
        }
        const mobileMenu = document.getElementById('mobileNavMenu');
        if (mobileMenu && window.bootstrap) {
            const bsCollapse = bootstrap.Collapse.getInstance(mobileMenu);
            if (bsCollapse) bsCollapse.hide();
        }
    }

    // Animated Workflow Journey: 01 -> 02 -> 03 -> 04 -> 05
    (function initWorkflowJourney() {
        const stepper = document.getElementById('workflowStepper');
        if (!stepper) return;

        const progressBar = document.getElementById('stepperProgressBar');
        const travelDot = document.getElementById('stepperTravelDot');
        const items = [
            document.getElementById('timelineItem0'),
            document.getElementById('timelineItem1'),
            document.getElementById('timelineItem2'),
            document.getElementById('timelineItem3'),
            document.getElementById('timelineItem4')
        ];
        const nodes = [
            document.getElementById('timelineNode0'),
            document.getElementById('timelineNode1'),
            document.getElementById('timelineNode2'),
            document.getElementById('timelineNode3'),
            document.getElementById('timelineNode4')
        ];

        const percentages = [0, 25, 50, 75, 100];
        let currentStep = 0;
        let isPaused = false;
        let journeyInterval = null;

        function updateStep(index) {
            currentStep = index;
            const pct = percentages[currentStep];

            if (progressBar) progressBar.style.width = pct + '%';
            if (travelDot) {
                travelDot.style.left = pct + '%';
                if (currentStep === 4) {
                    travelDot.style.background = '#F59E0B';
                    travelDot.style.boxShadow = '0 0 14px rgba(245, 158, 11, 0.9), 0 0 0 4px rgba(245, 158, 11, 0.25)';
                } else {
                    travelDot.style.background = '#1E60EC';
                    travelDot.style.boxShadow = '0 0 12px rgba(30, 96, 236, 0.8), 0 0 0 4px rgba(30, 96, 236, 0.2)';
                }
            }

            nodes.forEach((node, i) => {
                if (node) {
                    if (i <= currentStep) {
                        node.classList.add('active');
                    } else {
                        node.classList.remove('active');
                    }
                }
            });

            items.forEach((item, i) => {
                if (item) {
                    if (i === currentStep) {
                        item.classList.add('active');
                    } else {
                        item.classList.remove('active');
                    }
                }
            });
        }

        updateStep(0);

        function startCycle() {
            if (journeyInterval) clearInterval(journeyInterval);
            journeyInterval = setInterval(() => {
                if (isPaused) return;
                let next = (currentStep + 1) % 5;
                updateStep(next);
            }, 2400);
        }

        startCycle();

        stepper.addEventListener('mouseenter', () => { isPaused = true; });
        stepper.addEventListener('mouseleave', () => { isPaused = false; });

        items.forEach((item, index) => {
            if (item) {
                item.addEventListener('click', () => {
                    updateStep(index);
                });
            }
        });
    })();
    </script>
</body>
</html>
