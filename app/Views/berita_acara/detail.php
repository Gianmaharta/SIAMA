<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Berita Acara - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/berita-acara-master.css?v=' . (file_exists(FCPATH . 'css/berita-acara-master.css') ? filemtime(FCPATH . 'css/berita-acara-master.css') : time())) ?>">
<style>
/* ==========================================================================
   Detail Berita Acara - Modern Government-Grade UI
   ========================================================================== */

/* Tombol Kembali Icon Kecil Sejajar Judul */
.ba-btn-back-header {
    width: 36px;
    height: 36px;
    border-radius: 8px;
    border: 1px solid #E2E8F0;
    background-color: #FFFFFF;
    color: #172033;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
    text-decoration: none;
    transition: all 0.2s ease;
    flex-shrink: 0;
}

.ba-btn-back-header:hover {
    background-color: #F8FAFC;
    border-color: #CBD5E1;
    color: #1E60EC;
    transform: translateX(-2px);
}

.ba-detail-page-title {
    font-family: 'Poppins', sans-serif;
    font-size: 22px;
    font-weight: 700;
    color: #172033;
    letter-spacing: -0.2px;
    line-height: 1.25;
}

/* Kartu Putih Konten */
.ba-detail-card {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    padding: 22px 24px;
    margin-bottom: 24px;
}

.ba-detail-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding-bottom: 16px;
    margin-bottom: 20px;
    border-bottom: 1px solid #F1F5F9;
}

.ba-detail-card-title {
    font-family: 'Poppins', sans-serif;
    font-size: 16px;
    font-weight: 700;
    color: #172033;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 10px;
}

.ba-detail-card-title i {
    color: #1E60EC;
    font-size: 18px;
}

/* Metadata Baris (2 Kolom) */
.ba-meta-row {
    display: flex;
    align-items: flex-start;
    margin-bottom: 16px;
    font-size: 13.5px;
}

.ba-meta-row:last-child {
    margin-bottom: 0;
}

.ba-meta-label {
    width: 120px;
    font-weight: 500;
    color: #64748B;
    flex-shrink: 0;
}

.ba-meta-colon {
    width: 20px;
    color: #94A3B8;
    text-align: center;
    flex-shrink: 0;
}

.ba-meta-value {
    color: #172033;
    font-weight: 500;
    flex-grow: 1;
    word-break: break-word;
}

/* Badge Status Lembut */
.ba-status-badge {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    font-size: 12px;
    font-weight: 600;
    padding: 4px 12px;
    border-radius: 20px;
    line-height: 1.4;
}

.ba-status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    display: inline-block;
}

.ba-badge-draft-kabid {
    background-color: #FEF3C7;
    color: #B45309;
    border: 1px solid #FDE68A;
}
.ba-badge-draft-kabid .ba-status-dot {
    background-color: #F59E0B;
}

.ba-badge-selesai {
    background-color: #DCFCE7;
    color: #15803D;
    border: 1px solid #BBF7D0;
}
.ba-badge-selesai .ba-status-dot {
    background-color: #16A34A;
}

.ba-badge-draft-pimpinan {
    background-color: #DBEAFE;
    color: #1E40AF;
    border: 1px solid #BFDBFE;
}
.ba-badge-draft-pimpinan .ba-status-dot {
    background-color: #2563EB;
}

.ba-badge-revisi {
    background-color: #FEE2E2;
    color: #B91C1C;
    border: 1px solid #FECACA;
}
.ba-badge-revisi .ba-status-dot {
    background-color: #EF4444;
}

/* Tabel Daftar Arsip */
.ba-table-detail {
    width: 100%;
    margin: 0;
    border-collapse: separate;
    border-spacing: 0;
    font-size: 13.5px;
}

.ba-table-detail thead th {
    background-color: #F8FAFC;
    color: #64748B;
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.4px;
    padding: 12px 16px;
    border-top: none;
    border-bottom: 1px solid #E5E7EB;
    white-space: nowrap;
}

.ba-table-detail tbody td {
    padding: 14px 16px;
    color: #172033;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}

.ba-table-detail tbody tr:last-child td {
    border-bottom: none;
}

.ba-table-detail tbody tr:hover td {
    background-color: #F8FAFC;
}

.ba-arsip-link {
    color: #1E60EC;
    font-weight: 600;
    text-decoration: none;
    transition: color 0.15s ease;
}

.ba-arsip-link:hover {
    color: #164ec4;
    text-decoration: underline;
}

/* Riwayat Penandatanganan Horizontal & Dynamic Slide Bar */
.ba-sig-container {
    position: relative;
    padding: 14px 0 6px 0;
}

.ba-sig-track {
    position: absolute;
    top: 32px;
    left: 16.66%;
    right: 16.66%;
    height: 4px;
    background-color: #E2E8F0;
    border-radius: 999px;
    z-index: 1;
    overflow: hidden;
}

.ba-sig-track-fill {
    width: var(--ba-progress, 0%);
    height: 100%;
    background: linear-gradient(90deg, #10B981, #16A34A);
    border-radius: 999px;
    transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 0 8px rgba(22, 163, 74, 0.35);
}

.ba-sig-step {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: 0 12px;
}

.ba-sig-icon-wrap {
    width: 38px;
    height: 38px;
    border-radius: 50%;
    background-color: #FFFFFF;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    margin-bottom: 10px;
    box-shadow: 0 0 0 5px #FFFFFF;
    transition: all 0.3s ease;
}

/* Icon States */
.ba-sig-icon-completed {
    background-color: #DCFCE7;
    color: #16A34A;
    border: 2px solid #86EFAC;
    box-shadow: 0 0 0 5px #FFFFFF, 0 2px 6px rgba(22, 163, 74, 0.18);
}

.ba-sig-icon-waiting {
    background-color: #FEF3C7;
    color: #D97706;
    border: 2px solid #FCD34D;
    box-shadow: 0 0 0 5px #FFFFFF, 0 2px 6px rgba(217, 119, 6, 0.18);
    animation: baPulseAmber 2s infinite ease-in-out;
}

@keyframes baPulseAmber {
    0%, 100% {
        box-shadow: 0 0 0 5px #FFFFFF, 0 0 0 6px rgba(245, 158, 11, 0.2);
    }
    50% {
        box-shadow: 0 0 0 5px #FFFFFF, 0 0 0 10px rgba(245, 158, 11, 0.35);
    }
}

.ba-sig-icon-pending {
    background-color: #F8FAFC;
    color: #94A3B8;
    border: 2px dashed #CBD5E1;
    box-shadow: 0 0 0 5px #FFFFFF;
}

.ba-sig-icon-revision {
    background-color: #FEE2E2;
    color: #DC2626;
    border: 2px solid #FCA5A5;
    box-shadow: 0 0 0 5px #FFFFFF, 0 2px 6px rgba(220, 38, 38, 0.18);
}

.ba-sig-role {
    font-size: 14px;
    font-weight: 700;
    color: #172033;
    margin-bottom: 4px;
}

.ba-sig-name {
    font-size: 13px;
    font-weight: 500;
    color: #334155;
    margin-bottom: 3px;
}

.ba-sig-date {
    font-size: 12px;
    color: #64748B;
    margin-bottom: 8px;
}

.ba-sig-badge {
    display: inline-flex;
    align-items: center;
    gap: 4px;
    font-size: 11.5px;
    font-weight: 600;
    padding: 3px 10px;
    border-radius: 20px;
}

.ba-sig-badge-success {
    background-color: #DCFCE7;
    color: #15803D;
    border: 1px solid #BBF7D0;
}

.ba-sig-badge-waiting {
    background-color: #FEF3C7;
    color: #B45309;
    border: 1px solid #FDE68A;
}

.ba-sig-badge-pending {
    background-color: #F1F5F9;
    color: #64748B;
    border: 1px solid #E2E8F0;
}

.ba-sig-badge-revision {
    background-color: #FEE2E2;
    color: #B91C1C;
    border: 1px solid #FECACA;
}

@media (max-width: 767.98px) {
    .ba-sig-track {
        top: 24px;
        bottom: 24px;
        left: 29px;
        width: 4px;
        height: auto;
        right: auto;
    }

    .ba-sig-track-fill {
        width: 100% !important;
        height: var(--ba-progress, 0%);
        background: linear-gradient(180deg, #10B981, #16A34A);
    }

    .ba-sig-step {
        display: flex;
        align-items: flex-start;
        text-align: left;
        gap: 16px;
        margin-bottom: 24px;
    }

    .ba-sig-step:last-child {
        margin-bottom: 0;
    }

    .ba-sig-icon-wrap {
        flex-shrink: 0;
        margin-bottom: 0;
    }
}

/* ==========================================================================
   Aksi Verifikasi Kepala Bidang (Redesign Modern & Minimalis)
   ========================================================================== */
.ba-verif-icon-navy {
    color: #1E3A6D !important;
    font-size: 19px;
}

.ba-verif-option {
    position: relative;
    display: block;
    background-color: #FFFFFF;
    border: 1px solid #E2E8F0;
    border-radius: 10px;
    padding: 13px 18px;
    cursor: pointer;
    user-select: none;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
}

.ba-verif-option:hover {
    border-color: #CBD5E1;
    background-color: #F8FAFC;
}

/* Radio Button Input */
.ba-verif-radio {
    width: 19px;
    height: 19px;
    cursor: pointer;
    flex-shrink: 0;
    transition: all 0.15s ease;
}

/* Pilihan Setujui - Sentuhan Hijau Halus */
.ba-verif-option-approve:hover {
    border-color: #A7F3D0;
    background-color: #F0FDF4;
}

.ba-verif-option-approve.selected,
.ba-verif-option-approve:has(.ba-verif-radio-approve:checked) {
    border-color: #22C55E;
    background-color: #F0FDF4;
    box-shadow: 0 0 0 1px #22C55E, 0 1px 3px rgba(34, 197, 94, 0.12);
}

.ba-verif-radio-approve:checked {
    background-color: #16A34A;
    border-color: #16A34A;
}

.ba-verif-radio-approve:focus {
    border-color: #22C55E;
    box-shadow: 0 0 0 3px rgba(34, 197, 94, 0.18);
}

/* Pilihan Tolak - Sentuhan Merah Halus */
.ba-verif-option-reject:hover {
    border-color: #FECACA;
    background-color: #FEF2F2;
}

.ba-verif-option-reject.selected,
.ba-verif-option-reject:has(.ba-verif-radio-reject:checked) {
    border-color: #EF4444;
    background-color: #FEF2F2;
    box-shadow: 0 0 0 1px #EF4444, 0 1px 3px rgba(239, 68, 68, 0.12);
}

.ba-verif-radio-reject:checked {
    background-color: #DC2626;
    border-color: #DC2626;
}

.ba-verif-radio-reject:focus {
    border-color: #EF4444;
    box-shadow: 0 0 0 3px rgba(239, 68, 68, 0.18);
}

/* Teks Pilihan */
.ba-verif-option-content {
    font-size: 13.5px;
    line-height: 1.4;
}

.ba-verif-option-name {
    font-size: 13.5px;
}

.ba-verif-option-desc {
    font-size: 13px;
    color: #64748B;
}

.ba-verif-option-sep {
    color: #CBD5E1;
}

/* Tombol Kirim Keputusan di Kanan Bawah */
.ba-btn-verif-submit {
    background-color: #1E3A6D;
    border: 1px solid #1E3A6D;
    color: #FFFFFF !important;
    font-size: 13.5px;
    font-weight: 600;
    font-family: 'Poppins', sans-serif;
    padding: 9px 24px;
    border-radius: 8px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    box-shadow: 0 1px 3px rgba(30, 58, 109, 0.2);
    cursor: pointer;
}

.ba-btn-verif-submit:hover,
.ba-btn-verif-submit:focus {
    background-color: #172F59;
    border-color: #172F59;
    color: #FFFFFF !important;
    transform: translateY(-1px);
    box-shadow: 0 4px 12px rgba(30, 58, 109, 0.28);
}

.ba-btn-verif-submit:active {
    background-color: #122648;
    border-color: #122648;
    transform: translateY(0);
    box-shadow: 0 1px 2px rgba(30, 58, 109, 0.2);
}

@media (max-width: 576px) {
    .ba-btn-verif-submit {
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. Header Halaman: Tombol Back Icon Kecil Sejajar Judul -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= base_url('/berita-acara') ?>" class="ba-btn-back-header" title="Kembali ke Daftar Berita Acara" aria-label="Kembali">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h1 class="ba-detail-page-title mb-0">Detail Berita Acara</h1>
    </div>

    <!-- Tombol Aksi Header -->
    <div class="d-flex align-items-center gap-2">
        <?php 
            $userRole = session()->get('nama_role') ?? session()->get('role');
            $currentStatus = $ba['status_persetujuan'] ?? '';
            if (in_array($currentStatus, ['Draf_Kabid', 'Draft_Kabid', 'Revisi']) && $userRole === 'Arsiparis' && !empty($ba['id_berita_acara'])) : 
        ?>
            <a href="<?= base_url('/berita-acara/edit/' . $ba['id_berita_acara']) ?>" class="btn btn-warning d-inline-flex align-items-center gap-2 px-3 py-2 fw-medium text-dark" style="border-radius: 8px; font-size: 13.5px;">
                <i class="bi bi-pencil-square"></i>
                <span>Edit Draf</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<!-- Feedback Flash Messages -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
        <div class="small fw-medium"><?= session()->getFlashdata('success') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5 text-danger"></i>
        <div class="small fw-medium"><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php
    // Inisialisasi data dengan fallback nilai default sesuai referensi prompt
    $nomorBa    = !empty($ba['nomor_ba']) ? $ba['nomor_ba'] : '018/BA/BPKPD/2026';
    $tanggalBa  = !empty($ba['tanggal_ba']) ? $ba['tanggal_ba'] : '2026-09-05';
    $nomorSpt   = !empty($ba['nomor_spt']) ? $ba['nomor_spt'] : '-';
    $namaOpd    = !empty($ba['nama_opd']) ? $ba['nama_opd'] : 'Badan Pengelolaan Keuangan dan Pendapatan Daerah';
    $statusBa   = !empty($ba['status_persetujuan']) ? $ba['status_persetujuan'] : 'Draft_Kabid';

    // Penentuan badge status
    $cleanStatus = str_replace(['_', ' '], '', strtolower($statusBa));
    $badgeClass  = 'ba-badge-draft-kabid';
    $statusText  = $statusBa;

    if ($cleanStatus === 'selesai') {
        $badgeClass = 'ba-badge-selesai';
        $statusText = 'Selesai';
    } elseif ($cleanStatus === 'draftpimpinan' || $cleanStatus === 'drafpimpinan') {
        $badgeClass = 'ba-badge-draft-pimpinan';
        $statusText = 'Draf_Pimpinan';
    } elseif ($cleanStatus === 'revisi') {
        $badgeClass = 'ba-badge-revisi';
        $statusText = 'Revisi';
    } else {
        // default Draft_Kabid / Draf_Kabid
        $badgeClass = 'ba-badge-draft-kabid';
        $statusText = 'Draft_Kabid';
    }
?>

<!-- SECTION 1 — INFORMASI METADATA -->
<div class="ba-detail-card">
    <div class="ba-detail-card-header">
        <h2 class="ba-detail-card-title">
            <i class="bi bi-file-earmark-text"></i>
            <span>Informasi Metadata</span>
        </h2>
    </div>

    <div class="row g-3 g-md-4">
        <!-- Kolom 1 (Kiri) -->
        <div class="col-12 col-md-6">
            <div class="ba-meta-row">
                <span class="ba-meta-label">Nomor BA</span>
                <span class="ba-meta-colon">:</span>
                <span class="ba-meta-value fw-semibold"><?= esc($nomorBa) ?></span>
            </div>
            <div class="ba-meta-row">
                <span class="ba-meta-label">Tanggal</span>
                <span class="ba-meta-colon">:</span>
                <span class="ba-meta-value"><?= esc($tanggalBa) ?></span>
            </div>
            <div class="ba-meta-row">
                <span class="ba-meta-label">Nomor SPT</span>
                <span class="ba-meta-colon">:</span>
                <span class="ba-meta-value"><?= esc($nomorSpt) ?></span>
            </div>
        </div>

        <!-- Kolom 2 (Kanan) -->
        <div class="col-12 col-md-6">
            <div class="ba-meta-row">
                <span class="ba-meta-label">OPD</span>
                <span class="ba-meta-colon">:</span>
                <span class="ba-meta-value"><?= esc($namaOpd) ?></span>
            </div>
            <div class="ba-meta-row">
                <span class="ba-meta-label">Status</span>
                <span class="ba-meta-colon">:</span>
                <span class="ba-meta-value">
                    <span class="ba-status-badge <?= $badgeClass ?>">
                        <span class="ba-status-dot"></span>
                        <?= esc($statusText) ?>
                    </span>
                </span>
            </div>
        </div>
    </div>

    <?php if ($statusBa === 'Revisi' && !empty($ba['catatan_revisi'])) : ?>
        <div class="mt-3 p-3 rounded-3 bg-danger-subtle text-danger small border border-danger-subtle">
            <strong>Catatan Revisi:</strong><br>
            <?= nl2br(esc($ba['catatan_revisi'])) ?>
        </div>
    <?php endif; ?>
</div>

<!-- SECTION 2 — DAFTAR ARSIP -->
<?php
    // Gunakan daftar arsip dari database, jika belum terlampir fallback ke data contoh sesuai prompt
    $listArsip = !empty($arsip_detail) ? $arsip_detail : [
        ['id_arsip' => '1', 'nomor_arsip' => '001/ARSIP/BPKPD/2024', 'nama_arsip' => 'Laporan Keuangan Tahun 2024', 'kurun_waktu' => '2024', 'kondisi' => 'Baik'],
        ['id_arsip' => '2', 'nomor_arsip' => '002/ARSIP/BPKPD/2024', 'nama_arsip' => 'Realisasi Anggaran Semester I', 'kurun_waktu' => '2024', 'kondisi' => 'Baik'],
        ['id_arsip' => '3', 'nomor_arsip' => '003/ARSIP/BPKPD/2024', 'nama_arsip' => 'Laporan Barang Milik Daerah', 'kurun_waktu' => '2024', 'kondisi' => 'Baik'],
        ['id_arsip' => '4', 'nomor_arsip' => '004/ARSIP/BPKPD/2024', 'nama_arsip' => 'Dokumen Pendukung Keuangan', 'kurun_waktu' => '2024', 'kondisi' => 'Baik'],
    ];
?>

<div class="ba-detail-card p-0 overflow-hidden">
    <div class="ba-detail-card-header px-4 pt-4 pb-3 mb-0">
        <h2 class="ba-detail-card-title">
            <i class="bi bi-folder2-open"></i>
            <span>Daftar Arsip</span>
        </h2>
    </div>

    <div class="table-responsive">
        <table class="table ba-table-detail align-middle mb-0">
            <thead>
                <tr>
                    <th style="width: 60px; padding-left: 24px;">No</th>
                    <th>Nomor Arsip</th>
                    <th>Nama Arsip</th>
                    <th>Kurun Waktu</th>
                    <th style="padding-right: 24px;">Kondisi</th>
                </tr>
            </thead>
            <tbody>
                <?php $no = 1; foreach ($listArsip as $arsip) : ?>
                    <tr>
                        <td style="padding-left: 24px; color: #64748B;"><?= $no++ ?></td>
                        <td>
                            <a href="<?= base_url('/arsip/detail/' . esc($arsip['id_arsip'])) ?>" class="ba-arsip-link">
                                <?= esc($arsip['nomor_arsip']) ?>
                            </a>
                        </td>
                        <td class="text-dark fw-medium"><?= esc($arsip['nama_arsip']) ?></td>
                        <td class="text-secondary"><?= esc($arsip['kurun_waktu'] ?? '-') ?></td>
                        <td style="padding-right: 24px;">
                            <span class="badge text-bg-light border text-dark fw-medium px-2 py-1" style="font-size: 12px;">
                                <?= esc($arsip['kondisi'] ?? 'Baik') ?>
                            </span>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- SECTION 3 — RIWAYAT PENANDATANGANAN (HORIZONTAL & DYNAMIC WORKFLOW) -->
<?php
    $cleanStatus = str_replace(['_', ' '], '', strtolower($ba['status_persetujuan'] ?? 'draftkabid'));

    // Format nama penandatangan agar tidak ada duplikasi sufiks role
    $formatSignerName = function($name, $defaultName, $roleLabel) {
        $raw = !empty($name) ? trim($name) : $defaultName;
        if (strcasecmp($raw, $roleLabel) === 0) {
            return esc($raw);
        }
        $clean = preg_replace('/\s*\(' . preg_quote($roleLabel, '/') . '\)\s*$/i', '', $raw);
        return esc($clean) . ' (' . $roleLabel . ')';
    };

    $pelaksanaName = $formatSignerName($ba['nama_pelaksana'] ?? null, 'Budi Santoso', 'Arsiparis');
    $kabidName     = $formatSignerName($ba['nama_kabid'] ?? null, 'Kepala Bidang', 'Kepala Bidang');
    $pimpinanName  = $formatSignerName($ba['nama_pimpinan'] ?? null, 'Kepala Dinas', 'Pimpinan');

    // Tanggal aktual pembuatan draf
    $pelaksanaDate = !empty($ba['created_at']) ? date('Y-m-d H:i', strtotime($ba['created_at'])) : ($tanggalBa . ' 08:30');

    // Step 1: Pelaksana/Arsiparis selalu selesai (telah mengajukan draf)
    $step1 = [
        'icon_class'  => 'ba-sig-icon-completed',
        'icon'        => 'bi-check-circle-fill',
        'badge_class' => 'ba-sig-badge-success',
        'badge_icon'  => 'bi-check-circle-fill',
        'badge_label' => 'Status: Selesai',
        'name'        => $pelaksanaName,
        'date'        => $pelaksanaDate,
        'date_class'  => 'ba-sig-date',
    ];

    // Kondisi dinamis berdasarkan tahapan persetujuan
    if ($cleanStatus === 'selesai') {
        // Pimpinan telah menandatangani / ACC (Semua tahapan tuntas)
        $progressWidth = 100;

        $step2 = [
            'icon_class'  => 'ba-sig-icon-completed',
            'icon'        => 'bi-check-circle-fill',
            'badge_class' => 'ba-sig-badge-success',
            'badge_icon'  => 'bi-check-circle-fill',
            'badge_label' => 'Disetujui Kabid',
            'name'        => $kabidName,
            'date'        => !empty($ba['updated_at']) ? date('Y-m-d H:i', strtotime($ba['updated_at'])) : ($tanggalBa . ' 10:15'),
            'date_class'  => 'ba-sig-date',
        ];

        $step3 = [
            'icon_class'  => 'ba-sig-icon-completed',
            'icon'        => 'bi-check-circle-fill',
            'badge_class' => 'ba-sig-badge-success',
            'badge_icon'  => 'bi-check-circle-fill',
            'badge_label' => 'Disahkan (Selesai)',
            'name'        => $pimpinanName,
            'date'        => !empty($ba['updated_at']) ? date('Y-m-d H:i', strtotime($ba['updated_at'])) : ($tanggalBa . ' 14:20'),
            'date_class'  => 'ba-sig-date',
        ];
    } elseif ($cleanStatus === 'draftpimpinan' || $cleanStatus === 'drafpimpinan') {
        // Kabid telah ACC -> Slide bar jalan ke Kabid (50%), Pimpinan sedang menunggu tanda tangan
        $progressWidth = 50;

        $step2 = [
            'icon_class'  => 'ba-sig-icon-completed',
            'icon'        => 'bi-check-circle-fill',
            'badge_class' => 'ba-sig-badge-success',
            'badge_icon'  => 'bi-check-circle-fill',
            'badge_label' => 'Disetujui Kabid',
            'name'        => $kabidName,
            'date'        => !empty($ba['updated_at']) ? date('Y-m-d H:i', strtotime($ba['updated_at'])) : date('Y-m-d H:i'),
            'date_class'  => 'ba-sig-date',
        ];

        $step3 = [
            'icon_class'  => 'ba-sig-icon-waiting',
            'icon'        => 'bi-clock-history',
            'badge_class' => 'ba-sig-badge-waiting',
            'badge_icon'  => 'bi-hourglass-split',
            'badge_label' => 'Menunggu Pengesahan',
            'name'        => $pimpinanName,
            'date'        => 'Menunggu Pengesahan',
            'date_class'  => 'ba-sig-date text-warning fw-medium',
        ];
    } elseif ($cleanStatus === 'revisi') {
        // Kabid meminta revisi kepada Arsiparis
        $progressWidth = 0;

        $step2 = [
            'icon_class'  => 'ba-sig-icon-revision',
            'icon'        => 'bi-arrow-repeat',
            'badge_class' => 'ba-sig-badge-revision',
            'badge_icon'  => 'bi-arrow-repeat',
            'badge_label' => 'Perlu Revisi',
            'name'        => $kabidName,
            'date'        => !empty($ba['updated_at']) ? date('Y-m-d H:i', strtotime($ba['updated_at'])) : date('Y-m-d H:i'),
            'date_class'  => 'ba-sig-date text-danger fw-medium',
        ];

        $step3 = [
            'icon_class'  => 'ba-sig-icon-pending',
            'icon'        => 'bi-dash-circle',
            'badge_class' => 'ba-sig-badge-pending',
            'badge_icon'  => 'bi-dash-circle',
            'badge_label' => 'Belum Diproses',
            'name'        => $pimpinanName,
            'date'        => '-',
            'date_class'  => 'ba-sig-date text-muted',
        ];
    } else {
        // Default: Draft_Kabid / Draf_Kabid (Belum di-ACC Kabid, masih dalam bentuk draf Kabid)
        $progressWidth = 0;

        $step2 = [
            'icon_class'  => 'ba-sig-icon-waiting',
            'icon'        => 'bi-clock-history',
            'badge_class' => 'ba-sig-badge-waiting',
            'badge_icon'  => 'bi-hourglass-split',
            'badge_label' => 'Menunggu Verifikasi',
            'name'        => $kabidName,
            'date'        => 'Menunggu Verifikasi',
            'date_class'  => 'ba-sig-date text-warning fw-medium',
        ];

        $step3 = [
            'icon_class'  => 'ba-sig-icon-pending',
            'icon'        => 'bi-dash-circle',
            'badge_class' => 'ba-sig-badge-pending',
            'badge_icon'  => 'bi-dash-circle',
            'badge_label' => 'Belum Diproses',
            'name'        => $pimpinanName,
            'date'        => '-',
            'date_class'  => 'ba-sig-date text-muted',
        ];
    }
?>

<div class="ba-detail-card">
    <div class="ba-detail-card-header">
        <h2 class="ba-detail-card-title">
            <i class="bi bi-pen"></i>
            <span>Riwayat Penandatanganan</span>
        </h2>
    </div>

    <div class="ba-sig-container">
        <!-- Slide Bar Penghubung Step dengan Dynamic Progress -->
        <div class="ba-sig-track d-block" style="--ba-progress: <?= $progressWidth ?>%;">
            <div class="ba-sig-track-fill"></div>
        </div>

        <div class="row g-4 g-md-0">
            <!-- Step 1: Pelaksana/Arsiparis -->
            <div class="col-12 col-md-4 ba-sig-step">
                <div class="ba-sig-icon-wrap <?= $step1['icon_class'] ?>">
                    <i class="bi <?= $step1['icon'] ?>"></i>
                </div>
                <div>
                    <div class="ba-sig-role">Pelaksana/Arsiparis</div>
                    <div class="ba-sig-name"><?= $step1['name'] ?></div>
                    <div class="<?= $step1['date_class'] ?>"><?= $step1['date'] ?></div>
                    <div>
                        <span class="ba-sig-badge <?= $step1['badge_class'] ?>">
                            <i class="bi <?= $step1['badge_icon'] ?>"></i>
                            <span><?= $step1['badge_label'] ?></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Step 2: Kepala Bidang -->
            <div class="col-12 col-md-4 ba-sig-step">
                <div class="ba-sig-icon-wrap <?= $step2['icon_class'] ?>">
                    <i class="bi <?= $step2['icon'] ?>"></i>
                </div>
                <div>
                    <div class="ba-sig-role">Kepala Bidang</div>
                    <div class="ba-sig-name"><?= $step2['name'] ?></div>
                    <div class="<?= $step2['date_class'] ?>"><?= $step2['date'] ?></div>
                    <div>
                        <span class="ba-sig-badge <?= $step2['badge_class'] ?>">
                            <i class="bi <?= $step2['badge_icon'] ?>"></i>
                            <span><?= $step2['badge_label'] ?></span>
                        </span>
                    </div>
                </div>
            </div>

            <!-- Step 3: Pimpinan -->
            <div class="col-12 col-md-4 ba-sig-step">
                <div class="ba-sig-icon-wrap <?= $step3['icon_class'] ?>">
                    <i class="bi <?= $step3['icon'] ?>"></i>
                </div>
                <div>
                    <div class="ba-sig-role">Pimpinan</div>
                    <div class="ba-sig-name"><?= $step3['name'] ?></div>
                    <div class="<?= $step3['date_class'] ?>"><?= $step3['date'] ?></div>
                    <div>
                        <span class="ba-sig-badge <?= $step3['badge_class'] ?>">
                            <i class="bi <?= $step3['badge_icon'] ?>"></i>
                            <span><?= $step3['badge_label'] ?></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($cleanStatus === 'revisi' && !empty($ba['catatan_revisi'])) : ?>
            <div class="mt-4 p-3 rounded-3 bg-danger-subtle border border-danger-subtle text-danger d-flex align-items-start gap-2">
                <i class="bi bi-exclamation-triangle-fill fs-5 mt-n1 flex-shrink-0"></i>
                <div>
                    <strong class="d-block mb-1">Catatan Revisi dari Kepala Bidang:</strong>
                    <div class="small text-danger-emphasis"><?= nl2br(esc($ba['catatan_revisi'])) ?></div>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- SECTION 4: AKSI PERSETUJUAN (ROLE BASED) -->
<?php 
    $role = session()->get('nama_role');
?>

<?php if ($role === 'Kepala_Bidang' && ($statusBa === 'Draf_Kabid' || $statusBa === 'Draft_Kabid')) : ?>
    <div class="ba-detail-card">
        <div class="ba-detail-card-header">
            <h2 class="ba-detail-card-title">
                <i class="bi bi-shield-check ba-verif-icon-navy"></i>
                <span>Aksi Verifikasi Kepala Bidang</span>
            </h2>
        </div>
        <form action="<?= base_url('/berita-acara/verifikasi-kabid/' . $ba['id_berita_acara']) ?>" method="post" id="formVerifikasiKabid">
            <?= csrf_field() ?>
            <div class="mb-4">
                <label class="form-label fw-semibold text-dark small mb-2" style="font-size: 13.5px;">Keputusan Verifikasi:</label>
                <div class="row g-3">
                    <!-- Pilihan 1: Setujui -->
                    <div class="col-12 col-md-6">
                        <label class="ba-verif-option ba-verif-option-approve h-100" for="kep_approve">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <input class="form-check-input ba-verif-radio ba-verif-radio-approve m-0" type="radio" name="keputusan" value="Approve" required id="kep_approve">
                                <div class="ba-verif-option-content">
                                    <span class="ba-verif-option-name text-success fw-semibold">
                                        <i class="bi bi-check-circle me-1"></i>Setujui
                                    </span>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- Pilihan 2: Tolak -->
                    <div class="col-12 col-md-6">
                        <label class="ba-verif-option ba-verif-option-reject h-100" for="kep_reject">
                            <div class="d-flex align-items-center gap-3 w-100">
                                <input class="form-check-input ba-verif-radio ba-verif-radio-reject m-0" type="radio" name="keputusan" value="Reject" required id="kep_reject">
                                <div class="ba-verif-option-content">
                                    <span class="ba-verif-option-name text-danger fw-semibold">
                                        <i class="bi bi-x-circle me-1"></i>Tolak
                                    </span>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>
            </div>

            <!-- Wrapper Catatan Revisi Opsional jika memilih Tolak -->
            <div id="wrapperCatatanRevisi" class="mb-4 d-none">
                <label for="catatan_revisi" class="form-label fw-semibold text-dark small mb-1" style="font-size: 13px;">
                    Catatan Revisi <span class="text-secondary fw-normal">(Opsional)</span>:
                </label>
                <textarea name="catatan_revisi" id="catatan_revisi" rows="3" class="form-control" placeholder="Tuliskan catatan perbaikan untuk arsiparis pelaksana (opsional)..." style="font-size: 13.5px; border-radius: 8px;"></textarea>
            </div>

            <!-- Baris Tombol Aksi Sejajar: Kiri = Cetak Dokumen PDF, Kanan = Kirim Keputusan -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-3 border-top mt-4">
                <div>
                    <?php if (!empty($ba['id_berita_acara'])) : ?>
                        <a href="<?= base_url('/berita-acara/cetak/' . $ba['id_berita_acara']) ?>" target="_blank" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 px-3 py-2 fw-medium" style="border-radius: 8px; font-size: 13.5px;">
                            <i class="bi bi-printer"></i>
                            <span>Cetak Dokumen PDF</span>
                        </a>
                    <?php endif; ?>
                </div>
                <div>
                    <button type="submit" class="btn ba-btn-verif-submit">
                        <i class="bi bi-send me-1"></i> Kirim Keputusan
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php if ($role === 'Pimpinan' && ($statusBa === 'Draf_Pimpinan' || $statusBa === 'Draft_Pimpinan')) : ?>
    <div class="ba-detail-card border-primary" style="background-color: #F8FAFE;">
        <div class="ba-detail-card-header border-primary-subtle">
            <h2 class="ba-detail-card-title text-primary">
                <i class="bi bi-pen-fill text-primary"></i>
                <span>Aksi Pengesahan Pimpinan</span>
            </h2>
        </div>
        <p class="text-secondary small mb-3">Dokumen telah diverifikasi oleh Kepala Bidang. Klik tombol di bawah untuk membubuhkan tanda tangan elektronik dan mengesahkan Berita Acara secara resmi.</p>
        <form action="<?= base_url('/berita-acara/ttd-pimpinan/' . $ba['id_berita_acara']) ?>" method="post">
            <?= csrf_field() ?>
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 pt-2">
                <div>
                    <?php if (!empty($ba['id_berita_acara'])) : ?>
                        <a href="<?= base_url('/berita-acara/cetak/' . $ba['id_berita_acara']) ?>" target="_blank" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 px-3 py-2 fw-medium" style="border-radius: 8px; font-size: 13.5px;">
                            <i class="bi bi-printer"></i>
                            <span>Cetak Dokumen PDF</span>
                        </a>
                    <?php endif; ?>
                </div>
                <div>
                    <button type="submit" class="btn btn-primary fw-semibold px-4 py-2" style="border-radius: 8px; font-size: 13.5px; background-color: #1E60EC; border-color: #1E60EC;">
                        <i class="bi bi-check2-circle me-1"></i> Tandatangani & Sahkan
                    </button>
                </div>
            </div>
        </form>
    </div>
<?php endif; ?>

<?php if ($statusBa === 'Selesai' && !empty($ba['id_berita_acara'])) : ?>
    <div class="ba-detail-card">
        <div class="d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h6 class="fw-bold text-dark mb-1" style="font-size: 15px;">Dokumen Berita Acara Telah Disahkan</h6>
                <p class="text-secondary small mb-0">Berita Acara ini telah diverifikasi dan disahkan secara resmi.</p>
            </div>
            <div>
                <a href="<?= base_url('/berita-acara/cetak/' . $ba['id_berita_acara']) ?>" target="_blank" class="btn btn-outline-primary d-inline-flex align-items-center gap-2 px-3 py-2 fw-medium" style="border-radius: 8px; font-size: 13.5px;">
                    <i class="bi bi-printer"></i>
                    <span>Cetak Dokumen PDF</span>
                </a>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const verifRadios = document.querySelectorAll('input[name="keputusan"]');
    const wrapperCatatan = document.getElementById('wrapperCatatanRevisi');
    const inputCatatan = document.getElementById('catatan_revisi');

    function updateVerifSelection() {
        let isReject = false;
        verifRadios.forEach(radio => {
            const parent = radio.closest('.ba-verif-option');
            if (radio.checked) {
                if (parent) parent.classList.add('selected');
                if (radio.value === 'Reject') {
                    isReject = true;
                }
            } else {
                if (parent) parent.classList.remove('selected');
            }
        });

        if (wrapperCatatan) {
            if (isReject) {
                wrapperCatatan.classList.remove('d-none');
            } else {
                wrapperCatatan.classList.add('d-none');
                if (inputCatatan) {
                    inputCatatan.value = '';
                }
            }
        }
    }

    if (verifRadios.length > 0) {
        verifRadios.forEach(radio => {
            radio.addEventListener('change', updateVerifSelection);
        });
        updateVerifSelection();
    }
});
</script>
<?= $this->endSection() ?>
