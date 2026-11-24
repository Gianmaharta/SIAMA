<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <?php
    $siteName    = $settings['site_name'] ?? 'SIAMA';
    $siteTagline = $settings['site_tagline'] ?? 'Sistem Informasi Alih Media Arsip';
    $instansi    = $settings['instansi_name'] ?? 'Pemerintah Kabupaten Buleleng';
    $desc        = $settings['site_description'] ?? 'Platform terpadu digitalisasi, autentikasi watermark, verifikasi berjenjang, dan manajemen Jadwal Retensi Arsip (JRA) dinamis.';
    $logoPath    = $settings['site_logo'] ?? 'logo siama1.png';
    $logoUrl     = file_exists(FCPATH . $logoPath) ? base_url($logoPath) : (file_exists(FCPATH . 'Images/logo siama1.png') ? base_url('Images/logo siama1.png') : base_url('logo siama1.png'));
    ?>
    <title><?= esc($siteName) ?> &ndash; <?= esc($siteTagline) ?> | <?= esc($instansi) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --brand-dark: #0F2342;
            --brand-primary: #1E60EC;
            --brand-accent: #38BDF8;
        }
        body {
            font-family: 'Poppins', sans-serif;
            color: #1E293B;
            background-color: #F8FAFC;
            overflow-x: hidden;
        }
        .lp-navbar {
            background: rgba(15, 35, 66, 0.96);
            backdrop-filter: blur(10px);
            padding: 14px 0;
            position: sticky;
            top: 0;
            z-index: 1030;
            border-bottom: 1px solid rgba(255, 255, 255, 0.08);
        }
        .lp-hero {
            background: radial-gradient(circle at 85% 20%, rgba(56, 189, 248, 0.22), transparent 45%),
                        linear-gradient(135deg, #0B1930 0%, #153263 55%, #1E4E9E 100%);
            color: #FFFFFF;
            padding: 84px 0 96px;
            position: relative;
        }
        .lp-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: rgba(56, 189, 248, 0.16);
            border: 1px solid rgba(56, 189, 248, 0.35);
            color: #7DD3FC;
            font-size: 12.5px;
            font-weight: 600;
            padding: 6px 14px;
            border-radius: 50px;
            margin-bottom: 20px;
        }
        .lp-hero-title {
            font-size: clamp(2rem, 3.6vw, 3.1rem);
            font-weight: 800;
            line-height: 1.2;
            letter-spacing: -0.5px;
        }
        .lp-stat-box {
            background: #FFFFFF;
            border-radius: 16px;
            padding: 24px;
            box-shadow: 0 12px 32px rgba(15, 35, 66, 0.07);
            border: 1px solid #E2E8F0;
            height: 100%;
            transition: transform 0.2s ease;
        }
        .lp-stat-box:hover {
            transform: translateY(-4px);
        }
        .lp-feature-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 28px 24px;
            height: 100%;
            transition: all 0.2s ease;
        }
        .lp-feature-card:hover {
            border-color: #93C5FD;
            box-shadow: 0 12px 28px rgba(30, 96, 236, 0.08);
            transform: translateY(-3px);
        }
        .lp-feature-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            margin-bottom: 18px;
        }
        .lp-step-card {
            background: #FFFFFF;
            border: 1px solid #E2E8F0;
            border-radius: 16px;
            padding: 24px 20px;
            height: 100%;
            position: relative;
        }
        .lp-step-num {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            background: #1E60EC;
            color: #FFFFFF;
            font-weight: 700;
            font-size: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 14px;
        }
        .lp-footer {
            background: #0B172A;
            color: #CBD5E1;
            padding: 60px 0 28px;
        }
        .lp-social-btn {
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.08);
            color: #FFFFFF;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.2s ease;
        }
        .lp-social-btn:hover {
            background: #1E60EC;
            color: #FFFFFF;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

    <!-- NAVBAR -->
    <nav class="lp-navbar">
        <div class="container d-flex align-items-center justify-content-between">
            <a href="<?= base_url('/') ?>" class="d-flex align-items-center gap-3 text-decoration-none">
                <img src="<?= esc($logoUrl) ?>" alt="<?= esc($siteName) ?>" style="height: 40px; max-width: 150px; object-fit: contain;">
                <div class="d-none d-sm-block">
                    <div class="fw-bold text-white lh-1" style="font-size: 17px;"><?= esc($siteName) ?></div>
                    <div class="text-white-50" style="font-size: 11.5px;"><?= esc($instansi) ?></div>
                </div>
            </a>
            <div class="d-flex align-items-center gap-3">
                <a href="#fitur" class="text-white-50 text-decoration-none small fw-medium d-none d-md-inline">Fitur Utama</a>
                <a href="#alur" class="text-white-50 text-decoration-none small fw-medium d-none d-md-inline">Alur Alih Media</a>
                <a href="#kontak" class="text-white-50 text-decoration-none small fw-medium d-none d-md-inline">Kontak</a>
                <?php if (!empty($isLoggedIn)) : ?>
                    <a href="<?= base_url('/dashboard') ?>" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" style="background-color: #1E60EC; border-color: #1E60EC;">
                        <i class="bi bi-speedometer2"></i>
                        <span>Ke Dashboard</span>
                    </a>
                <?php else : ?>
                    <a href="<?= base_url('/login') ?>" class="btn btn-primary btn-sm px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" style="background-color: #1E60EC; border-color: #1E60EC;">
                        <i class="bi bi-box-arrow-in-right"></i>
                        <span>Masuk Aplikasi</span>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <!-- HERO SECTION -->
    <section class="lp-hero">
        <div class="container">
            <div class="row align-items-center g-5">
                <div class="col-12 col-lg-7">
                    <div class="lp-badge">
                        <i class="bi bi-shield-check"></i>
                        <span><?= esc($instansi) ?></span>
                    </div>
                    <h1 class="lp-hero-title mb-3">
                        <?= esc($siteName) ?> &ndash; <?= esc($siteTagline) ?>
                    </h1>
                    <p class="text-white-50 fs-6 mb-4" style="line-height: 1.75; max-width: 620px;">
                        <?= esc($desc) ?>
                    </p>
                    <div class="d-flex flex-wrap align-items-center gap-3">
                        <?php if (!empty($isLoggedIn)) : ?>
                            <a href="<?= base_url('/dashboard') ?>" class="btn btn-primary px-4 py-2.5 rounded-3 fw-semibold d-inline-flex align-items-center gap-2 shadow" style="background-color: #1E60EC; border-color: #1E60EC; height: 46px;">
                                <i class="bi bi-grid-1x2-fill"></i>
                                <span>Buka Dashboard <?= esc($siteName) ?></span>
                            </a>
                        <?php else : ?>
                            <a href="<?= base_url('/login') ?>" class="btn btn-primary px-4 py-2.5 rounded-3 fw-semibold d-inline-flex align-items-center gap-2 shadow" style="background-color: #1E60EC; border-color: #1E60EC; height: 46px;">
                                <i class="bi bi-box-arrow-in-right"></i>
                                <span>Login ke Sistem</span>
                            </a>
                        <?php endif; ?>
                        <a href="#alur" class="btn btn-outline-light px-4 py-2.5 rounded-3 fw-medium d-inline-flex align-items-center gap-2" style="height: 46px;">
                            <i class="bi bi-diagram-3"></i>
                            <span>Pelajari Alur Pengarsipan</span>
                        </a>
                    </div>
                </div>
                <div class="col-12 col-lg-5">
                    <div class="p-4 rounded-4 border border-light border-opacity-25 shadow-lg" style="background: rgba(255, 255, 255, 0.07); backdrop-filter: blur(12px);">
                        <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom border-light border-opacity-10">
                            <div class="rounded-3 bg-primary bg-opacity-25 text-info d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; font-size: 22px;">
                                <i class="bi bi-file-earmark-lock2-fill"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-white">Standar Kearsipan Daerah</div>
                                <div class="small text-white-50">Peraturan Bupati Buleleng No. 41 Tahun 2024</div>
                            </div>
                        </div>
                        <ul class="list-unstyled mb-0 d-flex flex-column gap-3 small text-white-50">
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-info mt-0.5"></i>
                                <span><strong class="text-white">Klasifikasi & JRA Terintegrasi:</strong> Ribuan kode klasifikasi arsip lengkap beserta retensi aktif, inaktif, dan klasifikasi keamanan.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-info mt-0.5"></i>
                                <span><strong class="text-white">Watermark Otomatis per OPD:</strong> Perlindungan dokumen PDF dengan watermark diagonal sesuai identitas Perangkat Daerah.</span>
                            </li>
                            <li class="d-flex align-items-start gap-2">
                                <i class="bi bi-check-circle-fill text-info mt-0.5"></i>
                                <span><strong class="text-white">Pengesahan Berita Acara Berjenjang:</strong> Alur terstruktur dari SPT, Arsiparis, Verifikasi Kepala Bidang, hingga Pengesahan Pimpinan.</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STATISTIK RINGKAS -->
    <section class="py-4" style="margin-top: -48px; position: relative; z-index: 10;">
        <div class="container">
            <div class="row g-3 g-md-4">
                <div class="col-6 col-lg-3">
                    <div class="lp-stat-box d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-primary-subtle text-primary d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 22px;">
                            <i class="bi bi-bank2"></i>
                        </div>
                        <div>
                            <div class="h4 fw-bold text-dark mb-0"><?= number_format($stats['total_opd'] ?? 0, 0, ',', '.') ?></div>
                            <div class="small text-secondary">Perangkat Daerah</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="lp-stat-box d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-success-subtle text-success d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 22px;">
                            <i class="bi bi-journal-bookmark-fill"></i>
                        </div>
                        <div>
                            <div class="h4 fw-bold text-dark mb-0"><?= number_format($stats['total_jra'] ?? 0, 0, ',', '.') ?></div>
                            <div class="small text-secondary">Master Kode JRA</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="lp-stat-box d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-info-subtle text-info-emphasis d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 22px;">
                            <i class="bi bi-folder-check"></i>
                        </div>
                        <div>
                            <div class="h4 fw-bold text-dark mb-0"><?= number_format($stats['total_arsip'] ?? 0, 0, ',', '.') ?></div>
                            <div class="small text-secondary">Arsip Digital</div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <div class="lp-stat-box d-flex align-items-center gap-3">
                        <div class="rounded-3 bg-warning-subtle text-warning-emphasis d-flex align-items-center justify-content-center flex-shrink-0" style="width: 48px; height: 48px; font-size: 22px;">
                            <i class="bi bi-award-fill"></i>
                        </div>
                        <div>
                            <div class="h4 fw-bold text-dark mb-0"><?= number_format($stats['total_ba'] ?? 0, 0, ',', '.') ?></div>
                            <div class="small text-secondary">Berita Acara Sah</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FITUR UTAMA -->
    <section id="fitur" class="py-5">
        <div class="container py-3">
            <div class="text-center mx-auto mb-5" style="max-width: 640px;">
                <span class="badge bg-primary-subtle text-primary px-3 py-1.5 rounded-pill fw-semibold mb-2">FITUR UNGGULAN</span>
                <h2 class="fw-bold text-dark">Manajemen Alih Media Arsip yang Terukur & Akuntabel</h2>
                <p class="text-secondary small mb-0">Dirancang khusus untuk mendukung tata kelola arsip elektronik lintas Organisasi Perangkat Daerah (OPD).</p>
            </div>
            <div class="row g-4">
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="lp-feature-card">
                        <div class="lp-feature-icon bg-primary-subtle text-primary">
                            <i class="bi bi-file-earmark-person"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Penugasan Berbasis SPT</h5>
                        <p class="text-secondary small mb-0">Setiap kegiatan alih media didasari Surat Perintah Tugas (SPT) resmi dari Pimpinan OPD dan ditugaskan oleh Kepala Bidang kepada Arsiparis pelaksana.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="lp-feature-card">
                        <div class="lp-feature-icon bg-success-subtle text-success">
                            <i class="bi bi-clock-history"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Otomatisasi Jadwal Retensi (JRA)</h5>
                        <p class="text-secondary small mb-0">Perhitungan masa retensi aktif dan inaktif otomatis sesuai kode klasifikasi beserta notifikasi peringatan jatuh tempo bagi Admin OPD.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg-4">
                    <div class="lp-feature-card">
                        <div class="lp-feature-icon bg-warning-subtle text-warning-emphasis">
                            <i class="bi bi-patch-check"></i>
                        </div>
                        <h5 class="fw-bold text-dark mb-2">Berita Acara & Evaluasi</h5>
                        <p class="text-secondary small mb-0">Penerbitan Berita Acara Alih Media dengan verifikasi berjenjang, pencetakan PDF resmi, serta penilaian kualitas arsip tingkat Kabupaten.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ALUR 5 TAHAP PENGARSIPAN -->
    <section id="alur" class="py-5 bg-white border-top border-bottom">
        <div class="container py-3">
            <div class="text-center mx-auto mb-5" style="max-width: 640px;">
                <span class="badge bg-info-subtle text-info-emphasis px-3 py-1.5 rounded-pill fw-semibold mb-2">ALUR KERJA SISTEM</span>
                <h2 class="fw-bold text-dark">5 Tahapan Alih Media Arsip di <?= esc($siteName) ?></h2>
                <p class="text-secondary small mb-0">Alur terstandar mulai dari penerbitan surat tugas hingga pengesahan Berita Acara dan penilaian arsip.</p>
            </div>
            <div class="row g-3 g-lg-4">
                <div class="col-12 col-md-6 col-lg">
                    <div class="lp-step-card">
                        <div class="lp-step-num">1</div>
                        <h6 class="fw-bold text-dark mb-2">Penerbitan SPT</h6>
                        <p class="text-secondary small mb-0">Pimpinan OPD menerbitkan Surat Perintah Tugas (SPT) pelaksanaan alih media arsip.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg">
                    <div class="lp-step-card">
                        <div class="lp-step-num">2</div>
                        <h6 class="fw-bold text-dark mb-2">Penugasan Arsiparis</h6>
                        <p class="text-secondary small mb-0">Kepala Bidang menunjuk Arsiparis pelaksana sesuai SPT yang diterbitkan.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg">
                    <div class="lp-step-card">
                        <div class="lp-step-num">3</div>
                        <h6 class="fw-bold text-dark mb-2">Unggah & Watermark</h6>
                        <p class="text-secondary small mb-0">Arsiparis mengunggah dokumen digital, memilih klasifikasi JRA, dan menyusun draf Berita Acara.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg">
                    <div class="lp-step-card">
                        <div class="lp-step-num">4</div>
                        <h6 class="fw-bold text-dark mb-2">Verifikasi Kabid</h6>
                        <p class="text-secondary small mb-0">Kepala Bidang memeriksa kelengkapan berkas digital dan memverifikasi draf Berita Acara.</p>
                    </div>
                </div>
                <div class="col-12 col-md-6 col-lg">
                    <div class="lp-step-card">
                        <div class="lp-step-num">5</div>
                        <h6 class="fw-bold text-dark mb-2">Pengesahan & Evaluasi</h6>
                        <p class="text-secondary small mb-0">Pimpinan mengesahkan Berita Acara dan Admin Pemkab melakukan penilaian prioritas arsip.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FOOTER DINAMIS (DIATUR DARI PENGATURAN WEBSITE OLEH SUPER ADMIN) -->
    <footer id="kontak" class="lp-footer">
        <div class="container">
            <div class="row g-4 pb-4 border-bottom border-secondary border-opacity-25">
                <div class="col-12 col-lg-5">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <img src="<?= esc($logoUrl) ?>" alt="<?= esc($siteName) ?>" style="height: 42px; max-width: 150px; object-fit: contain;">
                        <div>
                            <div class="fw-bold text-white fs-5 lh-1"><?= esc($siteName) ?></div>
                            <div class="small text-white-50 mt-1"><?= esc($siteTagline) ?></div>
                        </div>
                    </div>
                    <p class="small text-white-50 mb-3" style="max-width: 430px; line-height: 1.7;">
                        <?= esc($desc) ?>
                    </p>
                    <div class="d-flex align-items-center gap-2">
                        <?php if (!empty($settings['social_website'])) : ?>
                            <a href="<?= esc($settings['social_website']) ?>" target="_blank" rel="noopener" class="lp-social-btn" title="Website Resmi">
                                <i class="bi bi-globe2"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_instagram'])) : ?>
                            <a href="<?= esc($settings['social_instagram']) ?>" target="_blank" rel="noopener" class="lp-social-btn" title="Instagram">
                                <i class="bi bi-instagram"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_facebook'])) : ?>
                            <a href="<?= esc($settings['social_facebook']) ?>" target="_blank" rel="noopener" class="lp-social-btn" title="Facebook">
                                <i class="bi bi-facebook"></i>
                            </a>
                        <?php endif; ?>
                        <?php if (!empty($settings['social_youtube'])) : ?>
                            <a href="<?= esc($settings['social_youtube']) ?>" target="_blank" rel="noopener" class="lp-social-btn" title="YouTube">
                                <i class="bi bi-youtube"></i>
                            </a>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-12 col-sm-6 col-lg-3">
                    <h6 class="fw-bold text-white mb-3">Tautan Cepat</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2 small">
                        <li><a href="<?= base_url('/') ?>" class="text-white-50 text-decoration-none">Beranda</a></li>
                        <li><a href="#fitur" class="text-white-50 text-decoration-none">Fitur Utama</a></li>
                        <li><a href="#alur" class="text-white-50 text-decoration-none">Alur Alih Media</a></li>
                        <li><a href="<?= base_url('/login') ?>" class="text-white-50 text-decoration-none">Halaman Login</a></li>
                    </ul>
                </div>

                <div class="col-12 col-sm-6 col-lg-4">
                    <h6 class="fw-bold text-white mb-3">Kontak & Alamat</h6>
                    <ul class="list-unstyled d-flex flex-column gap-2.5 small text-white-50 mb-0">
                        <?php if (!empty($settings['contact_address'])) : ?>
                            <li class="d-flex align-items-start gap-2 mb-2">
                                <i class="bi bi-geo-alt-fill text-info mt-1"></i>
                                <span><?= esc($settings['contact_address']) ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($settings['contact_phone'])) : ?>
                            <li class="d-flex align-items-center gap-2 mb-2">
                                <i class="bi bi-telephone-fill text-info"></i>
                                <span><?= esc($settings['contact_phone']) ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($settings['contact_email'])) : ?>
                            <li class="d-flex align-items-center gap-2">
                                <i class="bi bi-envelope-fill text-info"></i>
                                <span><?= esc($settings['contact_email']) ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>

            <div class="pt-4 d-flex flex-column flex-sm-row align-items-center justify-content-between gap-2 small text-white-50">
                <div>Hak Cipta &copy; <?= date('Y') ?> <?= esc($instansi) ?>. Seluruh hak cipta dilindungi.</div>
                <div><?= esc($siteName) ?> &bull; <?= esc($siteTagline) ?></div>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
