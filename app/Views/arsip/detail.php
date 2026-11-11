<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/arsip-master.css?v=' . (file_exists(FCPATH . 'css/arsip-master.css') ? filemtime(FCPATH . 'css/arsip-master.css') : time())) ?>">
<style>
.arsip-detail-hero {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    padding: 24px 28px;
    margin-bottom: 24px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
}

.arsip-detail-nomor {
    font-size: 13.5px;
    font-weight: 600;
    color: #1E60EC;
    letter-spacing: 0.3px;
    margin-bottom: 6px;
    display: flex;
    align-items: center;
    gap: 8px;
}

.arsip-detail-nama {
    font-size: 22px;
    font-weight: 700;
    color: #0F172A;
    line-height: 1.3;
    margin-bottom: 14px;
}

.arsip-badge-group {
    display: flex;
    flex-wrap: wrap;
    align-items: center;
    gap: 8px;
}

.arsip-pill-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 50px;
    line-height: 1.4;
}

.badge-verif-selesai { background: #DEF7EC; color: #03543F; }
.badge-verif-selesai .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #0E9F6E; }

.badge-verif-menunggu { background: #FEF3C7; color: #92400E; }
.badge-verif-menunggu .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #D97706; }

.badge-verif-draft { background: #F1F5F9; color: #475569; }
.badge-verif-draft .badge-dot { width: 6px; height: 6px; border-radius: 50%; background: #94A3B8; }

.badge-retensi-aktif { background: #DCFCE7; color: #166534; }
.badge-retensi-inaktif { background: #FEF3C7; color: #92400E; }
.badge-retensi-musnah { background: #FEE2E2; color: #991B1B; }
.badge-retensi-permanen { background: #E0E7FF; color: #3730A3; }

.badge-skor-pill {
    background: #EFF6FF;
    color: #1E60EC;
    border: 1px solid #DBEAFE;
    font-weight: 700;
}

.arsip-detail-card {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 24px;
    margin-bottom: 24px;
}

.arsip-detail-card-header {
    display: flex;
    align-items: center;
    gap: 12px;
    margin-bottom: 20px;
    padding-bottom: 14px;
    border-bottom: 1px solid #F1F5F9;
}

.arsip-detail-card-icon {
    width: 38px;
    height: 38px;
    border-radius: 10px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 18px;
    flex-shrink: 0;
}

.icon-box-blue { background: #EFF6FF; color: #1E60EC; }
.icon-box-indigo { background: #EEF2FF; color: #4F46E5; }
.icon-box-red { background: #FEF2F2; color: #DC2626; }

.arsip-detail-card-title {
    font-size: 16.5px;
    font-weight: 700;
    color: #0F172A;
    margin-bottom: 0;
}

.arsip-meta-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.arsip-meta-row {
    display: flex;
    flex-direction: column;
    gap: 3px;
}

.arsip-meta-label {
    font-size: 12px;
    font-weight: 600;
    color: #64748B;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.arsip-meta-value {
    font-size: 14px;
    font-weight: 500;
    color: #1E293B;
    word-break: break-word;
}

.arsip-file-box {
    background: #F8FAFC;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 16px 20px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 16px;
}

.arsip-file-info {
    display: flex;
    align-items: center;
    gap: 14px;
}

.arsip-file-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    background: #FEE2E2;
    color: #DC2626;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 22px;
    flex-shrink: 0;
}

.arsip-file-name {
    font-size: 14px;
    font-weight: 600;
    color: #0F172A;
    margin-bottom: 2px;
}

.arsip-file-sub {
    font-size: 12px;
    color: #64748B;
}

.btn-pdf-preview {
    background-color: #1E60EC;
    color: #FFFFFF;
    border: none;
    padding: 8px 16px;
    font-size: 13.5px;
    font-weight: 600;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    transition: background-color 0.15s ease;
}

.btn-pdf-preview:hover {
    background-color: #144bc4;
    color: #FFFFFF;
}

.btn-file-download {
    background-color: #FFFFFF;
    color: #1E293B;
    border: 1px solid #CBD5E1;
    padding: 8px 16px;
    font-size: 13.5px;
    font-weight: 600;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    gap: 6px;
    text-decoration: none;
    transition: all 0.15s ease;
}

.btn-file-download:hover {
    background-color: #F8FAFC;
    color: #1E60EC;
    border-color: #1E60EC;
}

/* Modal PDF Custom */
.pdf-modal-dialog {
    max-width: 900px;
    margin: 1.75rem auto;
}

.pdf-modal-content {
    border-radius: 12px;
    border: none;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
}

.pdf-modal-header {
    background-color: #0B1528;
    color: #FFFFFF;
    padding: 14px 20px;
    border-bottom: none;
}

.pdf-modal-header .btn-close {
    filter: invert(1);
}

/* Cegah modal tertutup backdrop */
#modalVerifikasiArsip,
#modalPdfViewer {
    z-index: 1065 !important;
}
.modal-backdrop {
    z-index: 1050 !important;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= base_url('/arsip') ?>" class="btn-back-title" title="Kembali ke Daftar Arsip" aria-label="Kembali">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="arsip-page-title mb-1" style="font-size: 24px; font-weight: 700; color: #0F172A;">Detail Arsip Digital</h1>
            <p class="text-secondary mb-0" style="font-size: 13.5px; color: #64748B;">Rincian dokumen arsip, informasi klasifikasi, retensi, dan berkas digital.</p>
        </div>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <?php 
        $roleSession = session()->get('nama_role') ?? session()->get('role');
        $idRole = session()->get('id_role');
        $userOpd = session()->get('id_opd');
        
        $isArsiparis = ($idRole == 5 || $roleSession === 'Arsiparis');
        $isKabid = in_array($roleSession, ['Kepala_Bidang', 'Kabid', 'Admin_Pemkab'], true);
        
        // Status verifikasi arsip
        $stVerifRaw = $arsip['status_verifikasi'] ?? 'Menunggu';
        $stVerifLower = strtolower(trim((string)$stVerifRaw));
        $isOpdMatch = ($roleSession === 'Admin_Pemkab') || empty($userOpd) || ($userOpd == ($arsip['id_opd'] ?? ''));
        $canVerify = $isKabid && $isOpdMatch && ($stVerifLower === 'menunggu' || $stVerifLower === 'draft');
        ?>

        <?php if ($canVerify) : ?>
            <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalVerifikasiArsip" style="background-color: #0E9F6E; border-color: #0E9F6E; font-size: 13.5px; padding: 8px 18px; border-radius: 8px; font-weight: 600; box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);">
                <i class="bi bi-patch-check-fill fs-6"></i>
                <span>Verifikasi Arsip</span>
            </button>
        <?php endif; ?>

        <?php if ($isArsiparis) : ?>
            <a href="<?= base_url('/arsip/edit/' . $arsip['id_arsip']) ?>" class="btn btn-primary d-inline-flex align-items-center gap-2" style="background-color: #1E60EC; border-color: #1E60EC; font-size: 13.5px; padding: 8px 16px; border-radius: 8px;">
                <i class="bi bi-pencil-square"></i>
                <span>Edit Data Arsip</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Flash Message: Success -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('success') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Flash Message: Error -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- 1. Hero Card: Nomor, Judul & Status Badges -->
<div class="arsip-detail-hero">
    <div class="arsip-detail-nomor">
        <i class="bi bi-file-earmark-text"></i>
        <span><?= esc($arsip['nomor_arsip']) ?></span>
    </div>
    <h2 class="arsip-detail-nama"><?= esc($arsip['nama_arsip']) ?></h2>

    <div class="arsip-badge-group">
        <!-- Status Verifikasi -->
        <?php
        $stVerif = strtolower(trim((string)($arsip['status_verifikasi'] ?? '')));
        if ($stVerif === 'selesai') {
            echo '<span class="arsip-pill-badge badge-verif-selesai"><span class="badge-dot"></span>Verifikasi: Selesai</span>';
        } elseif ($stVerif === 'menunggu') {
            echo '<span class="arsip-pill-badge badge-verif-menunggu"><span class="badge-dot"></span>Verifikasi: Menunggu</span>';
        } else {
            echo '<span class="arsip-pill-badge badge-verif-draft"><span class="badge-dot"></span>Verifikasi: Draft</span>';
        }
        ?>

        <!-- Status Autentikasi / Penilaian -->
        <?php
        $stAuten = trim((string)($arsip['status_autentikasi'] ?? 'Belum Dinilai'));
        ?>
        <span class="arsip-pill-badge" style="background-color: #F1F5F9; color: #334155; border: 1px solid #CBD5E1;">
            <i class="bi bi-patch-check"></i>
            <span>Penilaian: <?= esc($stAuten) ?></span>
        </span>

        <!-- Skor Prioritas -->
        <?php if (!empty($arsip['skor_prioritas'])) : ?>
            <span class="arsip-pill-badge badge-skor-pill">
                <i class="bi bi-star-fill" style="font-size: 11px;"></i>
                <span>Skor: <?= esc($arsip['skor_prioritas']) ?></span>
            </span>
        <?php endif; ?>

        <!-- Status Retensi -->
        <?php
        $stRetensi = esc($arsip['status_retensi_aktif'] ?? 'Aktif');
        $retClass = 'badge-retensi-aktif';
        if ($stRetensi === 'Inaktif') $retClass = 'badge-retensi-inaktif';
        elseif ($stRetensi === 'Musnah') $retClass = 'badge-retensi-musnah';
        elseif ($stRetensi === 'Permanen') $retClass = 'badge-retensi-permanen';
        ?>
        <span class="arsip-pill-badge <?= $retClass ?>">
            <i class="bi bi-clock-history"></i>
            <span>Retensi: <?= $stRetensi ?></span>
        </span>
    </div>
</div>

<!-- 2. Grid Metadata: Card Kiri (Informasi Metadata) & Card Kanan (Penilaian & Retensi) -->
<div class="row g-4 mb-4">
    <!-- Card Kiri: Informasi Metadata Arsip -->
    <div class="col-12 col-lg-7">
        <div class="arsip-detail-card h-100 mb-0">
            <div class="arsip-detail-card-header">
                <div class="arsip-detail-card-icon icon-box-blue">
                    <i class="bi bi-info-circle"></i>
                </div>
                <h3 class="arsip-detail-card-title">Informasi Dokumen & Metadata</h3>
            </div>

            <div class="arsip-meta-list">
                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <div class="arsip-meta-row">
                            <span class="arsip-meta-label">Perangkat Daerah (OPD)</span>
                            <span class="arsip-meta-value"><?= esc($arsip['nama_opd'] ?? '-') ?></span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="arsip-meta-row">
                            <span class="arsip-meta-label">Bidang Pengolah</span>
                            <span class="arsip-meta-value"><?= esc($arsip['nama_bidang'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>

                <div class="arsip-meta-row">
                    <span class="arsip-meta-label">Kode & Nama Klasifikasi</span>
                    <span class="arsip-meta-value">
                        <span class="fw-bold text-primary"><?= esc($arsip['kode_klasifikasi_text'] ?? '-') ?></span>
                        <?php if (!empty($arsip['nama_klasifikasi'])) : ?>
                            <span class="text-secondary">— <?= esc($arsip['nama_klasifikasi']) ?></span>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-sm-6">
                        <div class="arsip-meta-row">
                            <span class="arsip-meta-label">Nomor SPT Terkait</span>
                            <span class="arsip-meta-value">
                                <?php if (!empty($arsip['nomor_spt'])) : ?>
                                    <span class="text-primary fw-semibold"><?= esc($arsip['nomor_spt']) ?></span>
                                <?php else : ?>
                                    <span class="text-muted">Tidak ada SPT terkait</span>
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-6">
                        <div class="arsip-meta-row">
                            <span class="arsip-meta-label">Tahun Penciptaan / Kurun Waktu</span>
                            <span class="arsip-meta-value"><?= esc($arsip['kurun_waktu'] ?? '-') ?></span>
                        </div>
                    </div>
                </div>

                <div class="row g-3">
                    <div class="col-12 col-sm-4">
                        <div class="arsip-meta-row">
                            <span class="arsip-meta-label">Jumlah Berkas</span>
                            <span class="arsip-meta-value"><?= esc($arsip['jumlah'] ?? 1) ?> Berkas</span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="arsip-meta-row">
                            <span class="arsip-meta-label">Kondisi Fisik</span>
                            <span class="arsip-meta-value"><?= esc($arsip['kondisi'] ?? 'Baik') ?></span>
                        </div>
                    </div>
                    <div class="col-12 col-sm-4">
                        <div class="arsip-meta-row">
                            <span class="arsip-meta-label">Klasifikasi Arsip</span>
                            <span class="arsip-meta-value"><?= !empty($arsip['nama_klasifikasi']) ? esc($arsip['nama_klasifikasi']) : (!empty($arsip['kode_klasifikasi_text']) ? esc($arsip['kode_klasifikasi_text']) : '-') ?></span>
                        </div>
                    </div>
                </div>

                <div class="arsip-meta-row pt-2 border-top">
                    <span class="arsip-meta-label">Diunggah / Dibuat Oleh</span>
                    <span class="arsip-meta-value text-secondary">
                        <i class="bi bi-person me-1"></i>
                        <?= esc($arsip['nama_arsiparis'] ?? 'Petugas Arsip') ?>
                        <?php if (!empty($arsip['created_at'])) : ?>
                            <span class="text-muted ms-1 small">(<?= date('d M Y, H:i', strtotime($arsip['created_at'])) ?>)</span>
                        <?php endif; ?>
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Card Kanan: Verifikasi, Penilaian & Jadwal Retensi (JRA) -->
    <div class="col-12 col-lg-5">
        <div class="arsip-detail-card h-100 mb-0">
            <div class="arsip-detail-card-header">
                <div class="arsip-detail-card-icon icon-box-indigo">
                    <i class="bi bi-clock-history"></i>
                </div>
                <h3 class="arsip-detail-card-title">Status Retensi & Penilaian</h3>
            </div>

            <div class="arsip-meta-list">
                <div class="arsip-meta-row">
                    <span class="arsip-meta-label">Status Retensi Aktif</span>
                    <span class="arsip-meta-value">
                        <span class="arsip-pill-badge <?= $retClass ?>">
                            <?= $stRetensi ?>
                        </span>
                    </span>
                </div>

                <?php if (!empty($arsip['tanggal_retensi_aktif_berakhir'])) : ?>
                    <div class="arsip-meta-row">
                        <span class="arsip-meta-label">Berakhir Retensi Aktif</span>
                        <span class="arsip-meta-value">
                            <i class="bi bi-calendar3 me-1 text-muted"></i>
                            <?= date('d M Y', strtotime($arsip['tanggal_retensi_aktif_berakhir'])) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($arsip['tanggal_retensi_inaktif_berakhir'])) : ?>
                    <div class="arsip-meta-row">
                        <span class="arsip-meta-label">Berakhir Retensi Inaktif</span>
                        <span class="arsip-meta-value">
                            <i class="bi bi-calendar-check me-1 text-muted"></i>
                            <?= date('d M Y', strtotime($arsip['tanggal_retensi_inaktif_berakhir'])) ?>
                        </span>
                    </div>
                <?php endif; ?>

                <div class="arsip-meta-row pt-2 border-top">
                    <span class="arsip-meta-label">Skor Prioritas Penilaian</span>
                    <span class="arsip-meta-value">
                        <?php if (!empty($arsip['skor_prioritas'])) : ?>
                            <span class="arsip-skor-badge"><?= esc($arsip['skor_prioritas']) ?></span>
                        <?php else : ?>
                            <span class="arsip-skor-empty">Belum Dinilai</span>
                        <?php endif; ?>
                    </span>
                </div>

                <div class="arsip-meta-row">
                    <span class="arsip-meta-label">Status Autentikasi</span>
                    <span class="arsip-meta-value">
                        <span class="fw-semibold text-dark"><?= esc($arsip['status_autentikasi'] ?? 'Belum Dinilai') ?></span>
                    </span>
                </div>

                <?php if (!empty($arsip['catatan_penilaian'])) : ?>
                    <div class="arsip-meta-row">
                        <span class="arsip-meta-label">Catatan Penilaian</span>
                        <div class="p-2 bg-light rounded border text-muted small">
                            <?= nl2br(esc($arsip['catatan_penilaian'])) ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- 3. Card Dokumen Digital Alih Media -->
<div class="arsip-detail-card mb-4">
    <div class="arsip-detail-card-header">
        <div class="arsip-detail-card-icon icon-box-red">
            <i class="bi bi-file-earmark-pdf"></i>
        </div>
        <h3 class="arsip-detail-card-title">Berkas Digital Hasil Alih Media</h3>
    </div>

    <?php if (isset($arsip['status_retensi_aktif']) && $arsip['status_retensi_aktif'] === 'Musnah') : ?>
        <div class="alert alert-warning d-flex align-items-center gap-2 mb-0 rounded-3">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
            <div>
                <strong>Berkas Telah Dimusnahkan:</strong> Berkas fisik dan digital arsip ini telah dimusnahkan secara sistem sesuai Jadwal Retensi Arsip (JRA).
            </div>
        </div>
    <?php elseif ($file_url !== null) : ?>
        <div class="arsip-file-box">
            <div class="arsip-file-info">
                <div class="arsip-file-icon">
                    <?php 
                    $ext = strtolower(pathinfo($arsip['file_arsip'] ?? '', PATHINFO_EXTENSION));
                    if ($ext === 'pdf') {
                        echo '<i class="bi bi-file-earmark-pdf"></i>';
                    } elseif (in_array($ext, ['jpg', 'jpeg', 'png', 'tif', 'tiff'])) {
                        echo '<i class="bi bi-file-earmark-image"></i>';
                    } else {
                        echo '<i class="bi bi-file-earmark"></i>';
                    }
                    ?>
                </div>
                <div>
                    <div class="arsip-file-name"><?= esc($arsip['file_arsip']) ?></div>
                    <div class="arsip-file-sub d-flex align-items-center gap-2">
                        <span>Format: <?= strtoupper($ext) ?></span>
                        <?php if (!empty($arsip['is_watermarked'])) : ?>
                            <span class="badge bg-success-subtle text-success border border-success-subtle py-0 px-2 rounded-pill" style="font-size: 11px;">
                                <i class="bi bi-shield-check me-1"></i>Watermarked (<?= esc($arsip['watermark_source'] ?? 'Sistem') ?>)
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2 flex-wrap">
                <?php if ($canVerify) : ?>
                    <button type="button" class="btn btn-success d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalVerifikasiArsip" style="background-color: #0E9F6E; border-color: #0E9F6E; font-size: 13.5px; font-weight: 600; padding: 8px 16px; border-radius: 8px;">
                        <i class="bi bi-patch-check-fill"></i>
                        <span>Verifikasi Berkas</span>
                    </button>
                <?php endif; ?>

                <?php if ($ext === 'pdf') : ?>
                    <button type="button" class="btn-pdf-preview" data-bs-toggle="modal" data-bs-target="#modalPdfViewer">
                        <i class="bi bi-eye"></i>
                        <span>Lihat Berkas (PDF)</span>
                    </button>
                <?php else : ?>
                    <a href="<?= $file_url ?>" target="_blank" class="btn-pdf-preview text-decoration-none">
                        <i class="bi bi-eye"></i>
                        <span>Lihat Berkas</span>
                    </a>
                <?php endif; ?>

                <a href="<?= $file_url ?>" download="<?= esc($arsip['file_arsip']) ?>" class="btn-file-download">
                    <i class="bi bi-download"></i>
                    <span>Unduh Berkas</span>
                </a>
            </div>
        </div>

        <?php if ($ext === 'pdf') : ?>
            <!-- Modal PDF Viewer Bootstrap 5 -->
            <div class="modal fade" id="modalPdfViewer" tabindex="-1" aria-labelledby="modalPdfViewerLabel" aria-hidden="true">
                <div class="modal-dialog modal-xl modal-dialog-centered pdf-modal-dialog">
                    <div class="modal-content pdf-modal-content">
                        <div class="modal-header pdf-modal-header">
                            <h5 class="modal-title fs-6 fw-semibold text-white d-flex align-items-center gap-2" id="modalPdfViewerLabel">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <span>Dokumen: <?= esc($arsip['nomor_arsip']) ?> — <?= esc($arsip['nama_arsip']) ?></span>
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
                        </div>
                        <div class="modal-body p-0" style="height: 78vh; background: #525659;">
                            <iframe src="<?= $file_url ?>" width="100%" height="100%" style="border: none;"></iframe>
                        </div>
                        <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between align-items-center">
                            <span class="small text-muted">File: <?= esc($arsip['file_arsip']) ?></span>
                            <div class="d-flex gap-2">
                                <a href="<?= $file_url ?>" target="_blank" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Tab Baru
                                </a>
                                <a href="<?= $file_url ?>" download="<?= esc($arsip['file_arsip']) ?>" class="btn btn-sm btn-primary" style="background-color: #1E60EC; border-color: #1E60EC;">
                                    <i class="bi bi-download me-1"></i> Unduh PDF
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    <?php else : ?>
        <div class="p-4 text-center text-muted bg-light rounded-3 border">
            <i class="bi bi-folder-x fs-1 d-block mb-2 text-secondary opacity-50"></i>
            <span class="fw-medium">Belum ada berkas digital yang diunggah untuk arsip ini.</span>
        </div>
    <?php endif; ?>
</div>

<!-- 4. Aksi Verifikasi Kepala Bidang (Role Based) -->
<?php if ($canVerify) : ?>
    <div class="arsip-detail-card mb-4" style="border: 1px solid #86EFAC; background-color: #F0FDF4; border-radius: 12px; padding: 22px 24px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);">
        <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom flex-wrap gap-2" style="border-color: #DCFCE7 !important;">
            <div class="d-flex align-items-center gap-2">
                <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 38px; height: 38px; background-color: #DEF7EC; color: #0E9F6E; font-size: 19px;">
                    <i class="bi bi-shield-check"></i>
                </div>
                <div>
                    <h3 class="mb-0 fw-bold" style="font-size: 16px; color: #03543F;">Aksi Verifikasi Kepala Bidang</h3>
                    <span class="small" style="color: #046C4E;">Pemeriksaan keabsahan dokumen dan kelayakan berkas alih media</span>
                </div>
            </div>
            <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-3 py-2 rounded-pill fw-semibold" style="font-size: 12px;">
                <i class="bi bi-clock-history me-1"></i>Menunggu Verifikasi
            </span>
        </div>
        
        <p class="small mb-3" style="color: #1F2937; line-height: 1.6;">
            Berkas digital hasil alih media di atas telah selesai diunggah oleh Arsiparis pelaksana. Sebagai <strong>Kepala Bidang</strong>, Anda memiliki wewenang untuk memeriksa keterbacaan, keabsahan metadata, dan kelengkapan hasil pemindaian berkas. Klik tombol di bawah ini untuk memverifikasi dan menyetujui arsip ini agar dapat disertakan dalam draf Berita Acara Alih Media.
        </p>

        <div class="d-flex align-items-center gap-2 flex-wrap">
            <button type="button" class="btn btn-success fw-semibold px-4 py-2 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalVerifikasiArsip" style="background-color: #0E9F6E; border-color: #0E9F6E; border-radius: 8px; font-size: 13.5px;">
                <i class="bi bi-patch-check-fill"></i>
                <span>Verifikasi & Setujui Arsip</span>
            </button>
            <?php if (!empty($arsip['file_arsip'])) : ?>
                <?php if ($ext === 'pdf') : ?>
                    <button type="button" class="btn btn-outline-secondary px-3 py-2 d-inline-flex align-items-center gap-2" data-bs-toggle="modal" data-bs-target="#modalPdfViewer" style="border-radius: 8px; font-size: 13.5px;">
                        <i class="bi bi-eye"></i>
                        <span>Tinjau Berkas Terlebih Dahulu</span>
                    </button>
                <?php else : ?>
                    <a href="<?= $file_url ?>" target="_blank" class="btn btn-outline-secondary px-3 py-2 d-inline-flex align-items-center gap-2" style="border-radius: 8px; font-size: 13.5px;">
                        <i class="bi bi-eye"></i>
                        <span>Tinjau Berkas</span>
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
<?php elseif ($stVerifLower === 'selesai') : ?>
    <div class="arsip-detail-card mb-4" style="border: 1px solid #DEF7EC; background-color: #F9FDFB; border-radius: 12px; padding: 18px 22px;">
        <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; background-color: #DEF7EC; color: #0E9F6E; font-size: 20px;">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div>
                <h4 class="mb-1 fw-bold" style="font-size: 15px; color: #03543F;">Arsip Telah Diverifikasi (Status: Selesai)</h4>
                <p class="mb-0 small" style="color: #046C4E;">Dokumen alih media ini telah berhasil diverifikasi oleh Kepala Bidang dan memenuhi syarat keabsahan untuk proses Berita Acara.</p>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Modal Konfirmasi Verifikasi Arsip -->
<div class="modal fade" id="modalVerifikasiArsip" tabindex="-1" aria-labelledby="modalVerifikasiArsipLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 440px;">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0 pt-4 px-4">
                <div class="d-flex align-items-center gap-3">
                    <div class="rounded-circle d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; background-color: #DEF7EC; color: #0E9F6E; font-size: 24px;">
                        <i class="bi bi-patch-check-fill"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold text-dark mb-0" id="modalVerifikasiArsipLabel" style="font-size: 17px;">Konfirmasi Verifikasi</h5>
                        <p class="text-secondary small mb-0">Verifikasi kelayakan arsip digital</p>
                    </div>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <div class="modal-body px-4 py-3">
                <p class="text-secondary small mb-3" style="line-height: 1.5;">
                    Apakah Anda yakin ingin memverifikasi berkas arsip berikut?
                </p>
                <div class="p-3 rounded-3 bg-light border mb-3">
                    <div class="small fw-bold text-primary mb-1"><?= esc($arsip['nomor_arsip']) ?></div>
                    <div class="fw-semibold text-dark small mb-1"><?= esc($arsip['nama_arsip']) ?></div>
                    <div class="text-muted" style="font-size: 11.5px;">
                        Pengunggah: <?= esc($arsip['nama_uploader'] ?? $arsip['created_by'] ?? 'Arsiparis') ?>
                    </div>
                </div>
                <div class="small text-muted" style="font-size: 12px; line-height: 1.5;">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    Setelah diverifikasi, status verifikasi akan berubah menjadi <strong>Selesai</strong> dan notifikasi otomatis akan dikirimkan kepada Arsiparis.
                </div>
            </div>
            <div class="modal-footer border-0 pt-0 px-4 pb-4 d-flex justify-content-end gap-2">
                <button type="button" class="btn btn-light border px-3 py-2 rounded-3 text-secondary fw-medium" data-bs-dismiss="modal" style="font-size: 13.5px;">
                    Batal
                </button>
                <form action="<?= base_url('/arsip/verify/' . $arsip['id_arsip']) ?>" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-success px-4 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" style="font-size: 13.5px; background-color: #0E9F6E; border-color: #0E9F6E;">
                        <i class="bi bi-check2"></i>
                        <span>Ya, Verifikasi Arsip</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>


<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Pindahkan modals ke <body> segera setelah DOM siap agar bebas dari stacking context .app-layout
    ['modalVerifikasiArsip', 'modalPdfViewer'].forEach(function(modalId) {
        const m = document.getElementById(modalId);
        if (m && m.parentElement !== document.body) {
            document.body.appendChild(m);
        }
    });
});
</script>
<?= $this->endSection() ?>