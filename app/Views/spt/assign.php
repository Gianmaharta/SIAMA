<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tugaskan Arsiparis - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/spt-master.css?v=' . (file_exists(FCPATH . 'css/spt-master.css') ? filemtime(FCPATH . 'css/spt-master.css') : time())) ?>">
<style>
.spt-assign-card {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    padding: 24px;
    margin-bottom: 24px;
}
.spt-info-table th {
    font-size: 13px;
    font-weight: 600;
    color: #64748B;
    width: 38%;
    padding: 10px 12px;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}
.spt-info-table td {
    font-size: 13.5px;
    color: #172033;
    padding: 10px 12px;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}
.spt-perihal-box {
    background-color: #F8FAFC;
    border: 1px dashed #CBD5E1;
    border-radius: 8px;
    padding: 14px 16px;
    font-size: 13.5px;
    color: #334155;
    line-height: 1.5;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/spt') ?>" class="spt-btn-back text-decoration-none" title="Kembali ke Daftar SPT" aria-label="Kembali" style="font-size: 24px; color: #0B1528; line-height: 1;">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="spt-page-title mb-1" style="font-size: 24px; font-weight: 700; color: #172033;">Tugaskan Arsiparis Pelaksana</h1>
        <p class="text-secondary small mb-0">Tugaskan Surat Perintah Tugas (SPT) alih media arsip kepada arsiparis pelaksana di bidang Anda.</p>
    </div>
</div>

<!-- Feedback Alerts -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="row g-4">
    <!-- Kolom Kiri: Rincian Dokumen SPT -->
    <div class="col-12 col-lg-5">
        <div class="spt-assign-card h-100">
            <div class="d-flex align-items-center gap-2 pb-3 mb-3 border-bottom">
                <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">Rincian Dokumen SPT</h5>
            </div>

            <table class="table spt-info-table mb-3">
                <tbody>
                    <tr>
                        <th>Nomor SPT</th>
                        <td class="fw-bold text-dark"><?= esc($spt['nomor_spt']) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal Terbit</th>
                        <td><?= esc($spt['tanggal_spt']) ?></td>
                    </tr>
                    <tr>
                        <th>Periode</th>
                        <td>
                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 12px;">
                                <?= esc($spt['tanggal_mulai']) ?> s/d <?= esc($spt['tanggal_selesai']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Status SPT</th>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                <i class="bi bi-check-circle-fill me-1"></i><?= esc($spt['status']) ?>
                            </span>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary mb-1">Perihal Penugasan:</label>
                <div class="spt-perihal-box">
                    <?= nl2br(esc($spt['perihal'])) ?>
                </div>
            </div>

            <?php if (!empty($spt['file_spt'])) : ?>
                <div class="pt-2">
                    <a href="<?= base_url('uploads/spt/' . $spt['file_spt']) ?>" target="_blank" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-2 rounded-3">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Lihat Dokumen SPT (PDF)
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Kolom Kanan: Formulir Penugasan Arsiparis -->
    <div class="col-12 col-lg-7">
        <div class="spt-assign-card h-100">
            <div class="d-flex align-items-center gap-2 pb-3 mb-3 border-bottom">
                <i class="bi bi-person-check text-primary fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">Pilih Arsiparis di Bidang Anda</h5>
            </div>

            <!-- Banner Info Bidang -->
            <div class="d-flex align-items-center gap-3 p-3 rounded-3 mb-4" style="background-color: #EFF6FF; border: 1px solid #BFDBFE;">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle text-primary bg-white shadow-sm flex-shrink-0" style="width: 42px; height: 42px; font-size: 20px;">
                    <i class="bi bi-layers-fill"></i>
                </div>
                <div>
                    <span class="text-uppercase fw-semibold text-primary" style="font-size: 11px; letter-spacing: 0.5px;">Bidang Penugasan</span>
                    <h6 class="fw-bold mb-0 text-dark" style="font-size: 14.5px;">
                        <?= esc($nama_bidang ?? 'Semua Bidang OPD') ?>
                    </h6>
                    <small class="text-secondary" style="font-size: 12px;">Hanya arsiparis pelaksana yang terdaftar pada bidang ini yang dapat ditugaskan.</small>
                </div>
            </div>

            <?php if (!empty($arsiparis_list)) : ?>
                <form action="<?= base_url('/spt/process-assign/' . $spt['id_spt']) ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-4">
                        <label for="id_user" class="form-label fw-semibold text-dark" style="font-size: 13.5px;">
                            Pilih Arsiparis Pelaksana <span class="text-danger">*</span>
                        </label>
                        <select name="id_user" id="id_user" class="form-select form-select-lg" required style="font-size: 14px; border-radius: 8px;">
                            <option value="">-- Pilih Arsiparis --</option>
                            <?php foreach ($arsiparis_list as $arsiparis) : ?>
                                <option value="<?= esc($arsiparis['id_user']) ?>">
                                    <?= esc($arsiparis['nama']) ?> &mdash; <?= esc($arsiparis['email']) ?>
                                    <?php if (!empty($arsiparis['nama_bidang'])) : ?>
                                        (<?= esc($arsiparis['nama_bidang']) ?>)
                                    <?php endif; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text small text-secondary mt-1">
                            Arsiparis yang dipilih akan menerima tugas alih media arsip berdasarkan SPT ini.
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-primary px-4 py-2 d-inline-flex align-items-center gap-2 rounded-3 shadow-sm fw-medium" style="background-color: #1E60EC; border-color: #1E60EC; font-size: 13.5px;">
                            <i class="bi bi-person-check-fill"></i> Simpan & Tugaskan Arsiparis
                        </button>
                        <a href="<?= base_url('/spt') ?>" class="btn btn-light border px-3 py-2 rounded-3 text-secondary fw-medium" style="font-size: 13.5px;">
                            Batal
                        </a>
                    </div>
                </form>
            <?php else : ?>
                <!-- State Jika Belum Ada Arsiparis di Bidang -->
                <div class="alert alert-warning d-flex align-items-start gap-3 rounded-3 border-0 shadow-sm p-3 mb-0" role="alert">
                    <i class="bi bi-exclamation-triangle-fill fs-4 text-warning flex-shrink-0 mt-1"></i>
                    <div>
                        <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;">Belum Ada Arsiparis di Bidang Ini</h6>
                        <p class="small text-secondary mb-0" style="line-height: 1.5;">
                            Tidak ditemukan akun arsiparis aktif yang terdaftar di bidang <strong><?= esc($nama_bidang ?? 'Anda') ?></strong>. 
                            Silakan koordinasikan dengan <strong>Administrator OPD</strong> agar menambahkan atau memetakan akun Arsiparis ke bidang Anda terlebih dahulu.
                        </p>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
