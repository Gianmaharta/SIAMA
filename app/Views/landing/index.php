<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $siteName    = $settings['site_name'] ?? 'SIAMA';
    $siteTagline = $settings['site_tagline'] ?? 'Sistem Informasi Alih Media Arsip';
    $instansi    = $settings['instansi_name'] ?? 'Pemerintah Kabupaten Buleleng';
    $desc        = $settings['site_description'] ?? 'Penyelenggaraan alih media dokumen fisik kearsipan menjadi format digital autentik, penatausahaan surat perintah tugas, dan sertifikasi digital terpadu bagi seluruh Perangkat Daerah Kabupaten Buleleng.';
    $logoPath    = $settings['site_logo'] ?? 'logo siama1.png';
    $logoUrl     = file_exists(FCPATH . $logoPath) ? base_url($logoPath) : (file_exists(FCPATH . 'Images/logo siama1.png') ? base_url('Images/logo siama1.png') : base_url('logo siama1.png'));
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

    <!-- 1. NAVBAR PUTIH (CLASSY, CLEAN, MODERN) -->
    <nav class="lp-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="<?= base_url('/') ?>" class="d-flex align-items-center gap-3 text-decoration-none">
                <img src="<?= esc($logoUrl) ?>" alt="<?= esc($siteName) ?>" style="height: 42px; max-width: 150px; object-fit: contain;">
                <div>
                    <div class="lp-brand-title"><?= esc($siteName) ?> Buleleng</div>
                    <div class="lp-brand-sub">Dinas Kearsipan &amp; Perpustakaan Daerah</div>
                </div>
            </a>

            <!-- Menu Desktop -->
            <div class="d-none d-lg-flex align-items-center gap-2">
                <a href="#beranda" class="lp-nav-link">Beranda</a>
                <a href="#alur" class="lp-nav-link">Alur Alih Media</a>
                <a href="#peran" class="lp-nav-link">Peran &amp; Otoritas</a>
                <a href="#tata-kelola" class="lp-nav-link">Standar Tata Kelola</a>
                <a href="#cari-arsip" class="lp-nav-link">Cari Arsip</a>
                <a href="#bantuan" class="lp-nav-link">Bantuan</a>
            </div>

            <!-- Tombol Masuk / Login -->
            <div class="d-flex align-items-center gap-2">
                <?php if (!empty($isLoggedIn)) : ?>
                    <a href="<?= base_url('/dashboard') ?>" class="lp-btn-login">
                        <i class="bi bi-speedometer2"></i>
                        <span>Ke Dashboard</span>
                    </a>
                <?php else : ?>
                    <a href="<?= base_url('/login') ?>" class="lp-btn-login">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Login ASN</span>
                    </a>
                <?php endif; ?>

                <!-- Mobile Menu Button -->
                <button class="btn btn-light d-lg-none border-0 text-dark p-2" type="button" data-bs-toggle="collapse" data-bs-target="#mobileNavMenu" aria-controls="mobileNavMenu" aria-expanded="false" aria-label="Toggle navigation">
                    <i class="bi bi-list fs-4"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Menu Dropdown -->
        <div class="collapse d-lg-none px-3 pt-3 pb-2 border-top mt-2 bg-white" id="mobileNavMenu">
            <div class="d-flex flex-column gap-1">
                <a href="#beranda" class="lp-nav-link">Beranda</a>
                <a href="#alur" class="lp-nav-link">Alur Alih Media</a>
                <a href="#peran" class="lp-nav-link">Peran &amp; Otoritas</a>
                <a href="#tata-kelola" class="lp-nav-link">Standar Tata Kelola</a>
                <a href="#cari-arsip" class="lp-nav-link">Cari Arsip</a>
                <a href="#bantuan" class="lp-nav-link">Bantuan &amp; Kontak</a>
            </div>
        </div>
    </nav>

    <!-- 2. HERO SECTION DENGAN BACKGROUND TINOL.JPEG & NAVY OVERLAY -->
    <header class="lp-hero-section" style="<?= $heroBgUrl ? "background-image: url('" . esc($heroBgUrl) . "');" : "background-color: var(--lp-navy-dark);" ?>">
        <div class="lp-hero-overlay"></div>
        <div class="container text-center lp-hero-content">
            
            <!-- SPBE Pill Badge -->
            <div class="lp-pill-badge">
                <span class="lp-pulse-dot"></span>
                <span>Sistem Pemerintahan Berbasis Elektronik (SPBE) &bull; Pemkab Buleleng</span>
            </div>

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
                        <span>Masuk Portal ASN</span>
                    </a>
                <?php endif; ?>
                <a href="#alur" class="lp-btn-cta-outline">
                    <i class="bi bi-file-earmark-text"></i>
                    <span>Panduan &amp; Regulasi</span>
                </a>
            </div>

            <!-- 3. FLOATING SEARCH CARD: CARI ARSIP / CEK STATUS -->
            <div class="lp-hero-search-card" id="heroSearchWidget">
                <div class="d-flex align-items-center justify-content-between mb-1">
                    <div class="lp-search-card-title">Cek Status Alih Media &amp; Keaslian Berkas</div>
                    <span class="badge bg-primary-subtle text-primary fw-semibold" style="font-size: 10px;">Layanan Publik</span>
                </div>
                <div class="lp-search-card-sub">
                    Masukkan nomor arsip, nomor berita acara (BA), atau kata kunci naskah dinas
                </div>

                <!-- Input Group Pencarian -->
                <form id="landingSearchForm" action="<?= base_url('/cari-arsip') ?>" method="GET" onsubmit="executeArchiveSearch(event)">
                    <div class="lp-search-input-wrap">
                        <i class="bi bi-search text-secondary ms-1"></i>
                        <input 
                            type="text" 
                            name="q" 
                            id="landingSearchInput" 
                            class="lp-search-input" 
                            placeholder="Contoh: ARSIP/DISKOMINFO/2026 atau 001/BA/DISKOMINFO/2026..." 
                            value="<?= esc($searchKeyword ?? '') ?>"
                            autocomplete="off"
                        >
                        <button type="submit" class="lp-btn-search" id="btnExecuteSearch">
                            <span class="spinner-border spinner-border-sm d-none" id="searchLoadingSpinner" role="status" aria-hidden="true"></span>
                            <span id="searchBtnText"><i class="bi bi-search me-1"></i> Lacak Berkas</span>
                        </button>
                    </div>
                </form>

                <!-- Area Hasil Pencarian Instan di Hero -->
                <div id="heroSearchResultsContainer" class="mt-3 <?= !empty($searchPerformed) ? '' : 'd-none' ?>">
                    <?php if (!empty($searchPerformed)) : ?>
                        <?php if (!empty($searchResults)) : ?>
                            <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                                <span class="small fw-semibold text-dark">Ditemukan <?= count($searchResults) ?> arsip terkait:</span>
                                <span class="badge bg-success-subtle text-success small">Terverifikasi Database</span>
                            </div>
                            <div style="max-height: 280px; overflow-y: auto; padding-right: 4px;">
                                <?php foreach ($searchResults as $item) : ?>
                                    <div class="p-2.5 rounded-3 border bg-light mb-2" style="font-size: 12.5px;">
                                        <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                            <strong class="text-primary"><?= esc($item['nomor_arsip']) ?></strong>
                                            <span class="badge <?= $item['is_terbuka'] ? 'bg-success text-white' : 'bg-warning text-dark' ?>" style="font-size: 9.5px;">
                                                <?= esc($item['akses_publik']) ?>
                                            </span>
                                        </div>
                                        <div class="fw-semibold text-dark"><?= esc($item['nama_arsip']) ?></div>
                                        <div class="text-secondary small mt-0.5"><?= esc($item['nama_opd']) ?> &bull; BA: <?= esc($item['nomor_ba']) ?></div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else : ?>
                            <div class="p-3 rounded-3 bg-light text-center text-muted small">
                                <i class="bi bi-info-circle me-1"></i> Data arsip dengan kata kunci "<strong><?= esc($searchKeyword ?? '') ?></strong>" tidak ditemukan.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
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

    <!-- 5. ALUR KERJA SISTEM: 5 TAHAPAN ALIH MEDIA ARSIP -->
    <section id="alur" class="py-5" style="padding-top: 85px !important; padding-bottom: 70px !important;">
        <div class="container py-2">
            <div class="text-center mx-auto mb-5" style="max-width: 660px;">
                <div class="lp-section-badge">
                    <i class="bi bi-diagram-3-fill"></i>
                    <span>ALUR TERSTRUKTUR &amp; AKUNTABEL</span>
                </div>
                <h2 class="lp-section-title">5 Tahapan Alih Media Arsip di SIAMA</h2>
                <p class="lp-section-sub">
                    Alur terstandar mulai dari penerbitan surat tugas hingga pengesahan Berita Acara dan penilaian arsip.
                </p>
            </div>

            <!-- 5 Kolom Kartu Tahapan -->
            <div class="row g-3 g-xl-4 align-items-stretch">
                <!-- Tahap 1: Penerbitan SPT -->
                <div class="col-12 col-md-6 col-lg lp-step-col">
                    <div class="lp-step-card-modern">
                        <div>
                            <div class="lp-step-top">
                                <div class="lp-step-icon">
                                    <i class="bi bi-file-earmark-text"></i>
                                </div>
                                <div class="lp-step-watermark-num">01</div>
                            </div>
                            <div class="lp-step-tag">1. PERSIAPAN</div>
                            <h3 class="lp-step-title">Penerbitan SPT</h3>
                            <p class="lp-step-desc">
                                Pimpinan OPD menerbitkan Surat Perintah Tugas (SPT) pelaksanaan alih media arsip.
                            </p>
                        </div>
                        <div class="lp-step-authority">
                            <i class="bi bi-shield-check text-primary"></i>
                            <span>Otoritas: Pimpinan OPD</span>
                        </div>
                    </div>
                </div>

                <!-- Tahap 2: Penugasan Arsiparis -->
                <div class="col-12 col-md-6 col-lg lp-step-col">
                    <div class="lp-step-card-modern">
                        <div>
                            <div class="lp-step-top">
                                <div class="lp-step-icon">
                                    <i class="bi bi-people-fill"></i>
                                </div>
                                <div class="lp-step-watermark-num">02</div>
                            </div>
                            <div class="lp-step-tag">2. PENUGASAN</div>
                            <h3 class="lp-step-title">Penugasan Arsiparis</h3>
                            <p class="lp-step-desc">
                                Kepala Bidang menunjuk Arsiparis pelaksana sesuai SPT yang diterbitkan.
                            </p>
                        </div>
                        <div class="lp-step-authority">
                            <i class="bi bi-shield-check text-primary"></i>
                            <span>Otoritas: Kepala Bidang</span>
                        </div>
                    </div>
                </div>

                <!-- Tahap 3: Unggah & Watermark -->
                <div class="col-12 col-md-6 col-lg lp-step-col">
                    <div class="lp-step-card-modern">
                        <div>
                            <div class="lp-step-top">
                                <div class="lp-step-icon">
                                    <i class="bi bi-cloud-arrow-up-fill"></i>
                                </div>
                                <div class="lp-step-watermark-num">03</div>
                            </div>
                            <div class="lp-step-tag">3. DIGITALISASI</div>
                            <h3 class="lp-step-title">Unggah &amp; Watermark</h3>
                            <p class="lp-step-desc">
                                Arsiparis mengunggah dokumen digital, memilih klasifikasi JRA, dan menyusun draf Berita Acara.
                            </p>
                        </div>
                        <div class="lp-step-authority">
                            <i class="bi bi-shield-check text-primary"></i>
                            <span>Otoritas: Arsiparis Pelaksana</span>
                        </div>
                    </div>
                </div>

                <!-- Tahap 4: Verifikasi Kabid -->
                <div class="col-12 col-md-6 col-lg lp-step-col">
                    <div class="lp-step-card-modern">
                        <div>
                            <div class="lp-step-top">
                                <div class="lp-step-icon">
                                    <i class="bi bi-clipboard-check-fill"></i>
                                </div>
                                <div class="lp-step-watermark-num">04</div>
                            </div>
                            <div class="lp-step-tag">4. VERIFIKASI</div>
                            <h3 class="lp-step-title">Verifikasi Kabid</h3>
                            <p class="lp-step-desc">
                                Kepala Bidang memeriksa kelengkapan berkas digital dan memverifikasi draf Berita Acara.
                            </p>
                        </div>
                        <div class="lp-step-authority">
                            <i class="bi bi-shield-check text-primary"></i>
                            <span>Otoritas: Pengawas / Kabid</span>
                        </div>
                    </div>
                </div>

                <!-- Tahap 5: Pengesahan & Evaluasi (Final Accent) -->
                <div class="col-12 col-md-6 col-lg lp-step-col">
                    <div class="lp-step-card-modern lp-step-card-final">
                        <div>
                            <div class="lp-step-top">
                                <div class="lp-step-icon lp-step-icon-final">
                                    <i class="bi bi-patch-check-fill"></i>
                                </div>
                                <div class="lp-step-watermark-num lp-step-watermark-num-final">05</div>
                            </div>
                            <div class="lp-step-tag lp-step-tag-final">5. PENGESAHAN (FINAL)</div>
                            <h3 class="lp-step-title">Pengesahan &amp; Evaluasi</h3>
                            <p class="lp-step-desc">
                                Pimpinan mengesahkan Berita Acara dan Admin Pemkab melakukan penilaian prioritas arsip.
                            </p>
                        </div>
                        <div class="lp-step-authority lp-step-authority-final">
                            <i class="bi bi-award-fill"></i>
                            <span>Otoritas: Pimpinan &amp; Admin Pemkab</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. PERAN DAN PENGGUNA SISTEM -->
    <section id="peran" class="py-5" style="background-color: var(--lp-bg-gray); padding-top: 80px !important; padding-bottom: 80px !important;">
        <div class="container py-2">
            <div class="text-center mx-auto mb-5" style="max-width: 660px;">
                <div class="lp-section-badge">
                    <i class="bi bi-person-lock"></i>
                    <span>HAK AKSES &amp; OTORITAS</span>
                </div>
                <h2 class="lp-section-title">Siapa Saja yang Menggunakan Aplikasi SIAMA?</h2>
                <p class="lp-section-sub">
                    Sistem berjenjang dengan pembagian wewenang ketat untuk menjaga integritas, kerahasiaan, dan akuntabilitas arsip.
                </p>
            </div>

            <!-- 2x2 Grid Peran Pengguna -->
            <div class="row g-4">
                <!-- Role 1: Pimpinan OPD -->
                <div class="col-12 col-md-6">
                    <div class="lp-role-card">
                        <div class="lp-role-header">
                            <div class="lp-role-icon">
                                <i class="bi bi-bank2"></i>
                            </div>
                            <div>
                                <span class="lp-role-badge">Kepala Perangkat Daerah &bull; Pengesahan</span>
                                <h3 class="lp-role-title mt-1 mb-0">Pimpinan OPD</h3>
                            </div>
                        </div>
                        <p class="lp-role-desc">
                            Menerbitkan Surat Perintah Tugas (SPT) alih media arsip serta mengesahkan Berita Acara Alih Media secara digital di lingkungan OPD masing-masing.
                        </p>
                    </div>
                </div>

                <!-- Role 2: Kepala Bidang -->
                <div class="col-12 col-md-6">
                    <div class="lp-role-card">
                        <div class="lp-role-header">
                            <div class="lp-role-icon">
                                <i class="bi bi-person-badge-fill"></i>
                            </div>
                            <div>
                                <span class="lp-role-badge">Pengawas Unit &bull; Verifikasi</span>
                                <h3 class="lp-role-title mt-1 mb-0">Kepala Bidang</h3>
                            </div>
                        </div>
                        <p class="lp-role-desc">
                            Menunjuk Arsiparis pelaksana sesuai SPT, memeriksa kelengkapan berkas digital hasil alih media, serta memverifikasi draf Berita Acara sebelum disahkan.
                        </p>
                    </div>
                </div>

                <!-- Role 3: Arsiparis Pelaksana -->
                <div class="col-12 col-md-6">
                    <div class="lp-role-card">
                        <div class="lp-role-header">
                            <div class="lp-role-icon">
                                <i class="bi bi-file-earmark-person-fill"></i>
                            </div>
                            <div>
                                <span class="lp-role-badge">Pelaksana Teknis &bull; Digitalisasi</span>
                                <h3 class="lp-role-title mt-1 mb-0">Arsiparis Pelaksana</h3>
                            </div>
                        </div>
                        <p class="lp-role-desc">
                            Melakukan pemindaian arsip fisik, mengunggah dokumen digital ke sistem, menerapkan watermark otomatis OPD, memeriksa kode klasifikasi JRA, dan menyusun draf Berita Acara.
                        </p>
                    </div>
                </div>

                <!-- Role 4: Admin OPD / Admin Pemkab -->
                <div class="col-12 col-md-6">
                    <div class="lp-role-card">
                        <div class="lp-role-header">
                            <div class="lp-role-icon" style="background-color: #FEF3C7; color: #D97706;">
                                <i class="bi bi-shield-fill-check"></i>
                            </div>
                            <div>
                                <span class="lp-role-badge" style="background-color: #FEF3C7; color: #92400E; border-color: #FDE68A;">Pengelola Sistem &bull; Evaluasi &amp; JRA</span>
                                <h3 class="lp-role-title mt-1 mb-0">Admin OPD / Admin Pemkab</h3>
                            </div>
                        </div>
                        <p class="lp-role-desc">
                            Memantau notifikasi jatuh tempo Jadwal Retensi Arsip (JRA), mengelola master data klasifikasi, serta melakukan penilaian prioritas arsip tingkat Kabupaten.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 7. STANDAR TATA KELOLA KEARSIPAN (NAVY BOX CONTAINER) -->
    <section id="tata-kelola" class="py-5">
        <div class="container py-2">
            <div class="lp-governance-box">
                <div class="text-center mx-auto" style="max-width: 700px;">
                    <div class="lp-gov-pill">
                        <i class="bi bi-patch-check-fill me-1.5"></i>
                        <span>Standar &amp; Regulasi Kearsipan Elektronik</span>
                    </div>
                    <h2 class="fw-bold mb-2 text-white" style="font-size: clamp(1.8rem, 2.8vw, 2.4rem); letter-spacing: -0.4px;">
                        Tata Kelola Alih Media Arsip Terstandar &amp; Teruji
                    </h2>
                    <p class="small text-white-50 mb-4" style="line-height: 1.7;">
                        Diselenggarakan selaras dengan pedoman Arsip Nasional Republik Indonesia (ANRI) dan Peraturan Bupati Buleleng tentang Penyelenggaraan SPBE di Lingkungan Pemerintah Kabupaten Buleleng.
                    </p>
                </div>

                <!-- 4 Kotak Tata Kelola -->
                <div class="row g-3 g-md-4 mt-2 mb-4 pb-2">
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="lp-gov-card">
                            <div class="lp-gov-card-icon"><i class="bi bi-file-earmark-check"></i></div>
                            <div class="lp-gov-card-title">Penugasan SPT</div>
                            <p class="lp-gov-card-desc">Didasari wewenang sah Kepala Perangkat Daerah</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="lp-gov-card">
                            <div class="lp-gov-card-icon"><i class="bi bi-calendar2-range"></i></div>
                            <div class="lp-gov-card-title">Jadwal Retensi (JRA)</div>
                            <p class="lp-gov-card-desc">Otomatisasi masa simpan aktif dan inaktif dokumen</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="lp-gov-card">
                            <div class="lp-gov-card-icon"><i class="bi bi-shield-lock"></i></div>
                            <div class="lp-gov-card-title">TTE &amp; Watermark</div>
                            <p class="lp-gov-card-desc">Proteksi dokumen dengan logo instansi watermark PDF</p>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6 col-lg-3">
                        <div class="lp-gov-card">
                            <div class="lp-gov-card-icon"><i class="bi bi-award"></i></div>
                            <div class="lp-gov-card-title">Berita Acara Otentik</div>
                            <p class="lp-gov-card-desc">Naskah legalitas alih media arsip resmi dan akuntabel</p>
                        </div>
                    </div>
                </div>

                <!-- Tombol Masuk ke SIAMA & Helpdesk -->
                <div class="d-flex flex-wrap align-items-center justify-content-center gap-3 pt-2">
                    <a href="<?= base_url('/login') ?>" class="lp-btn-cta-primary" style="padding: 11px 26px;">
                        <span>Masuk ke <?= esc($siteName) ?></span>
                        <i class="bi bi-arrow-up-right"></i>
                    </a>
                    <a href="#bantuan" class="lp-btn-cta-outline" style="padding: 11px 24px;">
                        <i class="bi bi-headset"></i>
                        <span>Hubungi Helpdesk Layanan</span>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- 8. FITUR BARU: SECTION CARI ARSIP INTERAKTIF -->
    <section id="cari-arsip" class="py-5" style="background-color: var(--lp-bg-gray); padding-top: 80px !important; padding-bottom: 80px !important;">
        <div class="container py-2">
            <div class="text-center mx-auto mb-4" style="max-width: 660px;">
                <div class="lp-section-badge">
                    <i class="bi bi-search"></i>
                    <span>LAYANAN PENCARIAN PUBLIK</span>
                </div>
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
                                    <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-2">
                                        <div>
                                            <span class="font-monospace fw-bold text-primary small d-inline-block me-2">
                                                <?= esc($item['nomor_arsip']) ?>
                                            </span>
                                            <?php if ($item['nomor_ba'] !== '-') : ?>
                                                <span class="text-muted small">&bull; BA: <?= esc($item['nomor_ba']) ?></span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <span class="<?= $item['is_terbuka'] ? 'lp-badge-public' : 'lp-badge-internal' ?>">
                                                <i class="bi <?= $item['is_terbuka'] ? 'bi-unlock-fill' : 'bi-lock-fill' ?> me-1"></i>
                                                <?= esc($item['akses_publik']) ?>
                                            </span>
                                        </div>
                                    </div>
                                    <h5 class="fw-bold text-dark mb-2" style="font-size: 16px;">
                                        <?= esc($item['nama_arsip']) ?>
                                    </h5>
                                    <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small mb-3">
                                        <span><i class="bi bi-building me-1"></i> <?= esc($item['nama_opd']) ?></span>
                                        <?php if ($item['nama_bidang'] !== '-') : ?>
                                            <span>&bull; <?= esc($item['nama_bidang']) ?></span>
                                        <?php endif; ?>
                                        <span><i class="bi bi-calendar3 me-1"></i> Kurun Waktu: <?= esc($item['kurun_waktu']) ?></span>
                                    </div>
                                    <div class="p-2.5 rounded-3 bg-light border d-flex flex-wrap align-items-center justify-content-between gap-2" style="font-size: 12px;">
                                        <div class="text-muted">
                                            <i class="bi bi-info-circle me-1 text-primary"></i>
                                            <span>Klasifikasi: <strong><?= esc($item['kode_klasifikasi']) ?></strong></span>
                                        </div>
                                        <div>
                                            <?php if (!empty($isLoggedIn)) : ?>
                                                <a href="<?= base_url('/arsip') ?>" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-2 fw-medium">
                                                    Buka di Dashboard <i class="bi bi-arrow-right ms-1"></i>
                                                </a>
                                            <?php else : ?>
                                                <a href="<?= base_url('/login') ?>" class="btn btn-sm btn-light border py-1 px-3 rounded-2 text-secondary fw-medium">
                                                    <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Unduh Berkas
                                                </a>
                                            <?php endif; ?>
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
                    <?php else : ?>
                        <!-- Initial State -->
                        <div class="text-center py-4 px-3 bg-white rounded-4 border text-muted small">
                            <i class="bi bi-file-earmark-text fs-4 text-primary d-block mb-1"></i>
                            Ketik nomor arsip atau kata kunci untuk menampilkan data naskah kearsipan daerah.
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>

    <!-- 9. PUSAT ASISTENSI & LAYANAN BANTUAN SIAMA -->
    <section id="bantuan" class="py-4 mb-5">
        <div class="container">
            <div class="lp-helpdesk-card">
                <div class="row align-items-center g-4">
                    <div class="col-12 col-lg-7">
                        <div class="d-flex align-items-start gap-3">
                            <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 24px;">
                                <i class="bi bi-headset"></i>
                            </div>
                            <div>
                                <h3 class="fw-bold text-dark mb-1" style="font-size: 17.5px;">Pusat Asistensi &amp; Layanan Bantuan SIAMA</h3>
                                <p class="text-secondary small mb-0" style="line-height: 1.6;">
                                    Tim Helpdesk Dinas Kearsipan dan Perpustakaan Daerah Kabupaten Buleleng siap mendampingi ASN setiap hari kerja Senin &ndash; Jumat, pukul 08.00 &ndash; 16.00 WITA.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-12 col-lg-5 d-flex flex-wrap align-items-center justify-content-lg-end gap-3">
                        <a href="javascript:void(0)" onclick="alert('Buku Panduan SIAMA (PDF) sedang dalam proses pembaruan berkas.')" class="lp-btn-doc" title="Unduh Buku Panduan SIAMA">
                            <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                            <span>Buku Panduan (PDF)</span>
                        </a>
                        <a href="https://wa.me/6281234567890?text=Halo%20Helpdesk%20SIAMA%20Buleleng" target="_blank" rel="noopener" class="lp-btn-helpdesk">
                            <i class="bi bi-whatsapp"></i>
                            <span>Konsultasi Helpdesk</span>
                        </a>
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
                            <div class="small text-white-50 mt-1">Dinas Kearsipan &amp; Perpustakaan Daerah</div>
                        </div>
                    </div>
                    <p class="small text-white-50 mb-3" style="max-width: 440px; line-height: 1.7;">
                        Platform tata kelola kearsipan digital terintegrasi Pemerintah Kabupaten Buleleng yang mempermudah proses alih media, penatausahaan surat, serta autentikasi memori kolektif daerah secara aman dan akuntabel.
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

                <!-- Kolom 3: Didukung Oleh (ANRI, BSrE BSSN, Diskominfo) -->
                <div class="col-12 col-sm-6 col-lg-4">
                    <h6 class="fw-bold text-white mb-3" style="font-size: 13px; letter-spacing: 0.5px;">DIDUKUNG OLEH</h6>
                    
                    <div class="lp-footer-partner-card">
                        <div class="rounded-2 bg-white bg-opacity-10 p-2 text-white">
                            <i class="bi bi-shield-check fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white small">ANRI</div>
                            <div class="text-white-50" style="font-size: 11px;">Standar Kearsipan Nasional</div>
                        </div>
                    </div>

                    <div class="lp-footer-partner-card">
                        <div class="rounded-2 bg-white bg-opacity-10 p-2 text-white">
                            <i class="bi bi-lock-fill fs-5"></i>
                        </div>
                        <div>
                            <div class="fw-bold text-white small">BSrE BSSN</div>
                            <div class="text-white-50" style="font-size: 11px;">Balai Sertifikasi Elektronik</div>
                        </div>
                    </div>

                    <div class="lp-footer-partner-card">
                        <div class="rounded-2 bg-white bg-opacity-10 p-2 text-white">
                            <i class="bi bi-cpu-fill fs-5"></i>
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
    function renderArchiveCard(item, isLoggedIn) {
        const badgeClass = item.is_terbuka ? 'lp-badge-public' : 'lp-badge-internal';
        const iconLock = item.is_terbuka ? 'bi-unlock-fill' : 'bi-lock-fill';
        const bidangText = (item.nama_bidang && item.nama_bidang !== '-') ? ` &bull; ${item.nama_bidang}` : '';
        const baText = (item.nomor_ba && item.nomor_ba !== '-') ? ` &bull; BA: ${item.nomor_ba}` : '';
        
        let actionBtn = '';
        if (isLoggedIn) {
            actionBtn = `<a href="<?= base_url('/arsip') ?>" class="btn btn-sm btn-outline-primary py-1 px-3 rounded-2 fw-medium">
                Buka di Dashboard <i class="bi bi-arrow-right ms-1"></i>
            </a>`;
        } else {
            actionBtn = `<a href="<?= base_url('/login') ?>" class="btn btn-sm btn-light border py-1 px-3 rounded-2 text-secondary fw-medium">
                <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Unduh Berkas
            </a>`;
        }

        return `
            <div class="lp-archive-result-card">
                <div class="d-flex align-items-start justify-content-between gap-3 flex-wrap mb-2">
                    <div>
                        <span class="font-monospace fw-bold text-primary small d-inline-block me-2">${item.nomor_arsip}</span>
                        ${baText ? `<span class="text-muted small">${baText}</span>` : ''}
                    </div>
                    <div>
                        <span class="${badgeClass}">
                            <i class="bi ${iconLock} me-1"></i>${item.akses_publik}
                        </span>
                    </div>
                </div>
                <h5 class="fw-bold text-dark mb-2" style="font-size: 16px;">
                    ${item.nama_arsip}
                </h5>
                <div class="d-flex flex-wrap align-items-center gap-3 text-secondary small mb-3">
                    <span><i class="bi bi-building me-1"></i> ${item.nama_opd}${bidangText}</span>
                    <span><i class="bi bi-calendar3 me-1"></i> Kurun Waktu: ${item.kurun_waktu}</span>
                </div>
                <div class="p-2.5 rounded-3 bg-light border d-flex flex-wrap align-items-center justify-content-between gap-2" style="font-size: 12px;">
                    <div class="text-muted">
                        <i class="bi bi-info-circle me-1 text-primary"></i>
                        <span>Klasifikasi: <strong>${item.kode_klasifikasi}</strong></span>
                    </div>
                    <div>
                        ${actionBtn}
                    </div>
                </div>
            </div>
        `;
    }

    // Pencarian dari Hero Search Card
    function executeArchiveSearch(event) {
        event.preventDefault();
        const input = document.getElementById('landingSearchInput');
        const spinner = document.getElementById('searchLoadingSpinner');
        const container = document.getElementById('heroSearchResultsContainer');
        const q = input ? input.value.trim() : '';

        if (!q || q.length < 2) {
            alert('Silakan masukkan minimal 2 karakter untuk melacak arsip.');
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
            if (container) {
                container.classList.remove('d-none');
                if (res.status === 'success' && res.data && res.data.length > 0) {
                    let html = `
                        <div class="d-flex align-items-center justify-content-between mb-2 pb-1 border-bottom">
                            <span class="small fw-semibold text-dark">Ditemukan ${res.count} arsip terkait:</span>
                            <span class="badge bg-success-subtle text-success small">Terverifikasi Database</span>
                        </div>
                        <div style="max-height: 280px; overflow-y: auto; padding-right: 4px;">
                    `;
                    res.data.forEach(item => {
                        html += `
                            <div class="p-2.5 rounded-3 border bg-light mb-2" style="font-size: 12.5px;">
                                <div class="d-flex align-items-start justify-content-between gap-2 mb-1">
                                    <strong class="text-primary">${item.nomor_arsip}</strong>
                                    <span class="badge ${item.is_terbuka ? 'bg-success text-white' : 'bg-warning text-dark'}" style="font-size: 9.5px;">
                                        ${item.akses_publik}
                                    </span>
                                </div>
                                <div class="fw-semibold text-dark">${item.nama_arsip}</div>
                                <div class="text-secondary small mt-0.5">${item.nama_opd} &bull; BA: ${item.nomor_ba}</div>
                            </div>
                        `;
                    });
                    html += `</div>`;
                    container.innerHTML = html;
                } else {
                    container.innerHTML = `
                        <div class="p-3 rounded-3 bg-light text-center text-muted small">
                            <i class="bi bi-info-circle me-1"></i> Data arsip dengan kata kunci "<strong>${q}</strong>" tidak ditemukan.
                        </div>
                    `;
                }
            }

            // Sinkronkan juga ke input dedicated section jika ada
            const dedicatedInput = document.getElementById('dedicatedSearchInput');
            if (dedicatedInput) dedicatedInput.value = q;
        })
        .catch(err => {
            if (spinner) spinner.classList.add('d-none');
            console.error('Search error:', err);
        });
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

            // Sinkronkan juga ke input hero jika ada
            const heroInput = document.getElementById('landingSearchInput');
            if (heroInput) heroInput.value = q;
        })
        .catch(err => {
            if (spinner) spinner.classList.add('d-none');
            console.error('Dedicated search error:', err);
        });
    }
    </script>
</body>
</html>
