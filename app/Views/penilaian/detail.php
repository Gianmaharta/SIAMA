<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Penilaian Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/penilaian-master.css') ?>">
<style>
.penilaian-detail-card {
    background: #FFFFFF;
    border: 1px solid var(--penilaian-border);
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.04);
    padding: 24px;
    margin-bottom: 24px;
}
.penilaian-card-header-title {
    font-size: 16px;
    font-weight: 600;
    color: var(--penilaian-text-main);
    margin-bottom: 20px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.penilaian-detail-table th {
    color: var(--penilaian-text-muted);
    font-size: 13.5px;
    font-weight: 500;
    width: 175px;
    padding: 10px 0;
    border: none;
    vertical-align: top;
}
.penilaian-detail-table td.table-colon {
    color: var(--penilaian-text-muted);
    font-size: 13.5px;
    font-weight: 500;
    width: 14px;
    padding: 10px 6px 10px 0;
    border: none;
    vertical-align: top;
    text-align: left;
}
.penilaian-detail-table td:not(.table-colon) {
    color: var(--penilaian-text-main);
    font-size: 14px;
    font-weight: 500;
    padding: 10px 0;
    border: none;
    vertical-align: top;
}
.btn-back-link {
    display: inline-flex;
    align-items: center;
    gap: 6px;
    color: var(--penilaian-text-muted);
    font-size: 13.5px;
    font-weight: 500;
    text-decoration: none;
    margin-bottom: 20px;
    transition: color 0.15s ease;
}
.btn-back-link:hover {
    color: var(--penilaian-primary-blue);
}
.penilaian-notes-box {
    background: #F8FAFC;
    border: 1px dashed var(--penilaian-border);
    border-radius: 8px;
    padding: 14px 18px;
    color: #334155;
    font-size: 13.5px;
    line-height: 1.5;
    margin: 0;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= base_url('/penilaian') ?>" class="btn-back-title" title="Kembali ke Daftar Penilaian Arsip" aria-label="Kembali">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div class="penilaian-page-header mb-0">
            <h1 class="penilaian-page-title mb-0">Detail Hasil Penilaian Arsip</h1>
            <p class="penilaian-page-subtitle mb-0">Informasi rincian arsip serta hasil verifikasi dan evaluasi autentikasi alih media.</p>
        </div>
    </div>
    <div>
        <a href="<?= base_url('/penilaian/form/' . $arsip['id_arsip']) ?>" class="btn btn-primary px-3 py-2 fw-semibold" style="background-color: var(--penilaian-navy-btn); border-color: var(--penilaian-navy-btn); border-radius: 8px; font-size: 13.5px;">
            <i class="bi bi-pencil-square me-1"></i> Edit Penilaian
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Informasi Berkas Arsip -->
    <div class="col-12 col-lg-6">
        <div class="penilaian-detail-card h-100">
            <h5 class="penilaian-card-header-title">
                <i class="bi bi-file-earmark-text text-primary fs-5"></i> Informasi Dokumen Arsip
            </h5>
            <table class="table penilaian-detail-table mb-0">
                <tr>
                    <th>Nomor Arsip</th>
                    <td class="table-colon">:</td>
                    <td><span class="text-primary fw-bold"><?= esc($arsip['nomor_arsip']) ?></span></td>
                </tr>
                <tr>
                    <th>Nama Arsip</th>
                    <td class="table-colon">:</td>
                    <td><?= esc($arsip['nama_arsip']) ?></td>
                </tr>
                <tr>
                    <th>OPD Pemilik</th>
                    <td class="table-colon">:</td>
                    <td><?= esc($arsip['nama_opd'] ?? '—') ?></td>
                </tr>
                <tr>
                    <th>Bidang Pengolah</th>
                    <td class="table-colon">:</td>
                    <td><?= esc($arsip['nama_bidang'] ?? '—') ?></td>
                </tr>
                <tr>
                    <th>Kurun Waktu</th>
                    <td class="table-colon">:</td>
                    <td><?= esc($arsip['kurun_waktu'] ?? '—') ?></td>
                </tr>
                <tr>
                    <th>Kondisi Fisik</th>
                    <td class="table-colon">:</td>
                    <td><?= esc($arsip['kondisi'] ?? '—') ?></td>
                </tr>
                <tr>
                    <th>Berkas Digital</th>
                    <td class="table-colon">:</td>
                    <td>
                        <?php if (!empty($arsip['file_arsip'])) : ?>
                            <a href="<?= base_url('uploads/arsip/' . $arsip['file_arsip']) ?>" target="_blank" class="text-primary text-decoration-none fw-semibold">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Lihat Dokumen Digital
                            </a>
                        <?php else : ?>
                            <span class="text-muted">Tidak ada berkas</span>
                        <?php endif; ?>
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <!-- Rincian Hasil Penilaian -->
    <div class="col-12 col-lg-6">
        <div class="penilaian-detail-card h-100">
            <h5 class="penilaian-card-header-title">
                <i class="bi bi-award text-success fs-5"></i> Hasil Penilaian & Autentikasi
            </h5>
            <table class="table penilaian-detail-table mb-0">
                <tr>
                    <th>Skor Prioritas</th>
                    <td class="table-colon">:</td>
                    <td>
                        <?php if ($arsip['skor_prioritas'] !== null && $arsip['skor_prioritas'] !== '') : ?>
                            <span class="penilaian-score-badge fs-6 px-3 py-1"><?= esc($arsip['skor_prioritas']) ?> / 100</span>
                        <?php else : ?>
                            <span class="text-muted">—</span>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <th>Status Autentikasi</th>
                    <td class="table-colon">:</td>
                    <td>
                        <?php 
                            $status = $arsip['status_autentikasi'] ?? 'Belum Dinilai';
                            $statusClass = 'badge-neutral';
                            if ($status === 'Lolos Evaluasi') $statusClass = 'badge-approved';
                            elseif ($status === 'Belum Dinilai') $statusClass = 'badge-unassessed';
                            elseif ($status === 'Revisi Metadata') $statusClass = 'badge-revision';
                            elseif ($status === 'Ditolak') $statusClass = 'badge-rejected';
                        ?>
                        <span class="penilaian-badge <?= $statusClass ?>">
                            <span class="badge-dot"></span>
                            <?= esc($status) ?>
                        </span>
                    </td>
                </tr>
                <tr>
                    <th>Tanggal Penilaian</th>
                    <td class="table-colon">:</td>
                    <td><?= !empty($arsip['tanggal_penilaian']) ? esc($arsip['tanggal_penilaian']) : '—' ?></td>
                </tr>
                <tr>
                    <th>Penilai (Admin Pemkab)</th>
                    <td class="table-colon">:</td>
                    <td><?= esc($arsip['nama_penilai'] ?? 'Administrator Pemkab') ?></td>
                </tr>
                <tr>
                    <th>Catatan Evaluasi</th>
                    <td class="table-colon">:</td>
                    <td>
                        <div class="penilaian-notes-box">
                            <?= !empty($arsip['catatan_penilaian']) ? nl2br(esc($arsip['catatan_penilaian'])) : '<span class="text-muted fst-italic">Tidak ada catatan evaluasi.</span>' ?>
                        </div>
                    </td>
                </tr>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
