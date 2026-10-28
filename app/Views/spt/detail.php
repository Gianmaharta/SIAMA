<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Surat Perintah Tugas (SPT) - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/spt-master.css?v=' . (file_exists(FCPATH . 'css/spt-master.css') ? filemtime(FCPATH . 'css/spt-master.css') : time())) ?>">
<style>
.spt-detail-card {
    background: #FFFFFF;
    border: 1px solid #E5E7EB;
    border-radius: 12px;
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.03);
    padding: 24px;
    margin-bottom: 24px;
}
.spt-detail-table th {
    font-size: 13px;
    font-weight: 600;
    color: #64748B;
    width: 32%;
    padding: 10px 14px;
    border-bottom: 1px solid #F1F5F9;
    vertical-align: middle;
}
.spt-detail-table td {
    font-size: 13.5px;
    color: #172033;
    padding: 10px 14px;
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

<!-- Header Halaman: Tombol Back & Judul Sejajar -->
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div class="d-flex align-items-center gap-3">
        <a href="<?= base_url('/spt') ?>" class="spt-btn-back text-decoration-none" title="Kembali ke Daftar SPT" aria-label="Kembali" style="font-size: 24px; color: #0B1528; line-height: 1;">
            <i class="bi bi-arrow-left"></i>
        </a>
        <div>
            <h1 class="spt-page-title mb-1" style="font-size: 24px; font-weight: 700; color: #172033;">Detail Surat Perintah Tugas (SPT)</h1>
            <p class="text-secondary small mb-0">Rincian informasi surat perintah tugas dan arsiparis pelaksana alih media.</p>
        </div>
    </div>

    <!-- Tombol Aksi Tambahan jika belum ditugaskan & Role Kepala Bidang -->
    <?php if (in_array($user_role ?? session()->get('nama_role'), ['Kepala_Bidang', 'Kepala Bidang', 'Kabid']) && ($spt['status_penugasan'] ?? '') === 'belum_ditugaskan') : ?>
        <a href="<?= base_url('/spt/assign/' . $spt['id_spt']) ?>" class="btn btn-primary d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 shadow-sm fw-medium" style="background-color: #1E60EC; border-color: #1E60EC; font-size: 13.5px;">
            <i class="bi bi-person-plus-fill"></i> Tugaskan Arsiparis
        </a>
    <?php endif; ?>
</div>

<!-- Flash Message Feedback -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('success') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <!-- Card Utama: Rincian Surat Perintah Tugas -->
    <div class="col-12 col-xl-7">
        <div class="spt-detail-card h-100">
            <div class="d-flex align-items-center gap-2 pb-3 mb-3 border-bottom">
                <i class="bi bi-file-earmark-text text-primary fs-5"></i>
                <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">Informasi Surat Tugas</h5>
            </div>

            <table class="table spt-detail-table mb-3">
                <tbody>
                    <tr>
                        <th>Nomor SPT</th>
                        <td class="fw-bold text-dark"><?= esc($spt['nomor_spt']) ?></td>
                    </tr>
                    <tr>
                        <th>Tanggal SPT</th>
                        <td><?= esc($spt['tanggal_spt']) ?></td>
                    </tr>
                    <tr>
                        <th>Periode Pelaksanaan</th>
                        <td>
                            <span class="badge bg-light text-dark border fw-normal" style="font-size: 12px;">
                                <i class="bi bi-calendar3 me-1 text-secondary"></i>
                                <?= esc($spt['tanggal_mulai']) ?> s/d <?= esc($spt['tanggal_selesai']) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>OPD</th>
                        <td><?= esc($opd['nama_opd'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>Diterbitkan Oleh</th>
                        <td><?= esc($pimpinan['nama'] ?? 'Pimpinan / Kepala Dinas') ?></td>
                    </tr>
                    <tr>
                        <th>Status SPT</th>
                        <td>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                <i class="bi bi-check-circle-fill me-1"></i><?= esc($spt['status'] ?? 'Aktif') ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>Status Penugasan</th>
                        <td>
                            <?php 
                            $stPenugasan = strtolower(trim($spt['status_penugasan'] ?? 'belum_ditugaskan'));
                            if ($stPenugasan === 'selesai') : ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                    <i class="bi bi-check-all me-1"></i>Selesai
                                </span>
                            <?php elseif (in_array($stPenugasan, ['ditugaskan', 'proses', 'dalam proses'])) : ?>
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                    <i class="bi bi-person-check-fill me-1"></i>Ditugaskan
                                </span>
                            <?php else : ?>
                                <span class="badge bg-warning-subtle text-warning-emphasis border border-warning-subtle px-2 py-1 rounded-pill" style="font-size: 11.5px;">
                                    <i class="bi bi-hourglass-split me-1"></i>Belum Ditugaskan
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </tbody>
            </table>

            <div class="mb-3">
                <label class="form-label small fw-bold text-secondary mb-1">Perihal / Deskripsi Tugas:</label>
                <div class="spt-perihal-box">
                    <?= nl2br(esc($spt['perihal'])) ?>
                </div>
            </div>

            <?php if (!empty($spt['file_spt'])) : ?>
                <div class="pt-3 border-top d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-outline-danger btn-sm d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2" data-bs-toggle="modal" data-bs-target="#modalPdfViewer">
                        <i class="bi bi-file-earmark-pdf-fill"></i> Lihat Dokumen SPT (PDF)
                    </button>
                    <a href="<?= base_url('uploads/spt/' . $spt['file_spt']) ?>" target="_blank" download class="btn btn-light border btn-sm d-inline-flex align-items-center gap-2 rounded-3 px-3 py-2 text-secondary">
                        <i class="bi bi-download"></i> Unduh PDF
                    </a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Card Kedua: Pelaksana Tugas (Arsiparis) -->
    <div class="col-12 col-xl-5">
        <div class="spt-detail-card h-100">
            <div class="d-flex align-items-center justify-content-between pb-3 mb-3 border-bottom">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-people text-primary fs-5"></i>
                    <h5 class="fw-bold mb-0 text-dark" style="font-size: 16px;">Pelaksana Tugas (Arsiparis)</h5>
                </div>
                <span class="badge bg-secondary-subtle text-secondary rounded-pill" style="font-size: 11px;">
                    <?= count($pelaksana_list ?? []) ?> Pelaksana
                </span>
            </div>

            <?php if (!empty($pelaksana_list)) : ?>
                <div class="list-group list-group-flush">
                    <?php foreach ($pelaksana_list as $pelaksana) : ?>
                        <div class="list-group-item px-0 py-3 border-bottom">
                            <div class="d-flex align-items-start gap-3">
                                <div class="d-inline-flex align-items-center justify-content-center rounded-circle text-primary bg-primary-subtle flex-shrink-0" style="width: 40px; height: 40px; font-size: 16px;">
                                    <i class="bi bi-person-fill"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1 text-dark" style="font-size: 14px;"><?= esc($pelaksana['nama']) ?></h6>
                                    <div class="small text-secondary mb-1">
                                        <i class="bi bi-envelope me-1"></i><?= esc($pelaksana['email']) ?>
                                    </div>
                                    <?php if (!empty($pelaksana['nama_bidang'])) : ?>
                                        <div class="small text-secondary mb-1">
                                            <i class="bi bi-layers me-1"></i>Bidang: <strong><?= esc($pelaksana['nama_bidang']) ?></strong>
                                        </div>
                                    <?php endif; ?>
                                    <?php if (!empty($pelaksana['nama_assigner'])) : ?>
                                        <div class="small text-muted" style="font-size: 11.5px;">
                                            Ditugaskan oleh: <?= esc($pelaksana['nama_assigner']) ?>
                                            <?php if (!empty($pelaksana['tgl_ditugaskan'])) : ?>
                                                pada <?= date('d/m/Y H:i', strtotime($pelaksana['tgl_ditugaskan'])) ?>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                </div>
                                <div>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1 rounded-pill" style="font-size: 11px;">
                                        <?= esc(ucfirst($pelaksana['status'] ?? 'Ditugaskan')) ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else : ?>
                <!-- Empty State Pelaksana -->
                <div class="text-center py-5">
                    <div class="d-inline-flex align-items-center justify-content-center rounded-circle bg-light text-secondary mb-3" style="width: 56px; height: 56px; font-size: 26px;">
                        <i class="bi bi-person-x"></i>
                    </div>
                    <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">Belum Ada Arsiparis Ditugaskan</h6>
                    <p class="small text-secondary mb-3 px-3">Surat Perintah Tugas ini belum memiliki arsiparis pelaksana yang ditugaskan.</p>
                    
                    <?php if (in_array($user_role ?? session()->get('nama_role'), ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'])) : ?>
                        <a href="<?= base_url('/spt/assign/' . $spt['id_spt']) ?>" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-1 px-3 py-2 rounded-3">
                            <i class="bi bi-person-plus"></i> Tugaskan Arsiparis Sekarang
                        </a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal PDF Viewer Bootstrap 5 -->
<?php if (!empty($spt['file_spt'])) : ?>
    <div class="modal fade" id="modalPdfViewer" tabindex="-1" aria-labelledby="modalPdfViewerLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl modal-dialog-centered" style="height: 90vh;">
            <div class="modal-content h-100 border-0 rounded-4 shadow-lg overflow-hidden">
                <div class="modal-header bg-light border-bottom px-4 py-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                        <h5 class="modal-title fw-bold text-dark fs-6" id="modalPdfViewerLabel">
                            Dokumen SPT: <?= esc($spt['nomor_spt']) ?>
                        </h5>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <a href="<?= base_url('uploads/spt/' . $spt['file_spt']) ?>" target="_blank" download class="btn btn-outline-secondary btn-sm rounded-3">
                            <i class="bi bi-download me-1"></i> Unduh
                        </a>
                        <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                    </div>
                </div>
                <div class="modal-body p-0 h-100 bg-secondary-subtle">
                    <iframe src="<?= base_url('uploads/spt/' . $spt['file_spt']) ?>" class="w-100 h-100 border-0" title="Viewer Dokumen SPT"></iframe>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
