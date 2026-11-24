<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Reset & Data Cleansing - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h1 class="h3 fw-bold text-gray-800 mb-1 d-flex align-items-center gap-2">
                <i class="bi bi-shield-slash text-danger"></i>
                Reset Seluruh Data Operasional (Data Cleansing)
            </h1>
            <p class="text-secondary small mb-0">Kembalikan aplikasi ke kondisi bersih seperti instalasi baru tanpa menghapus data master maupun struktur database.</p>
        </div>
        <div>
            <span class="badge bg-danger px-3 py-2 fs-6 rounded-pill">
                <i class="bi bi-shield-lock me-1"></i> Khusus Admin Pemkab
            </span>
        </div>
    </div>

    <!-- Status Saat Ini -->
    <div class="row g-4 mb-4">
        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-semibold">Total Data Transaksi</span>
                        <div class="rounded-circle p-2 bg-danger bg-opacity-10 text-danger">
                            <i class="bi bi-trash fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($summary['total_transactions']) ?></h3>
                    <small class="text-muted">Baris di seluruh tabel transaksi</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-semibold">File Upload Storage</span>
                        <div class="rounded-circle p-2 bg-warning bg-opacity-10 text-warning">
                            <i class="bi bi-file-earmark-pdf fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-dark"><?= number_format($summary['files']['total_files']) ?></h3>
                    <small class="text-muted">File PDF arsip & SPT di storage</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-semibold">Master OPD & Klasifikasi</span>
                        <div class="rounded-circle p-2 bg-success bg-opacity-10 text-success">
                            <i class="bi bi-check-circle fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-success">Utuh & Aman</h3>
                    <small class="text-muted"><?= $summary['masters']['opd'] ?> OPD, <?= $summary['masters']['kode_klasifikasi'] ?> Klasifikasi</small>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-lg-3">
            <div class="card border-0 shadow-sm rounded-4 h-100">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-secondary small fw-semibold">Akun Utama Admin</span>
                        <div class="rounded-circle p-2 bg-primary bg-opacity-10 text-primary">
                            <i class="bi bi-person-badge fs-5"></i>
                        </div>
                    </div>
                    <h3 class="fw-bold mb-0 text-primary">Aktif</h3>
                    <small class="text-muted">admin.pemkab@siama.test</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Ringkasan Rinci & Tombol Aksi -->
    <div class="row g-4">
        <div class="col-lg-7">
            <div class="card border-0 shadow-sm rounded-4 mb-4">
                <div class="card-header bg-white border-0 py-3">
                    <h5 class="fw-bold mb-0 text-dark">Rincian Data yang Akan Dikosongkan</h5>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Tabel / Modul</th>
                                    <th>Kategori</th>
                                    <th class="text-end">Jumlah Data Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($summary['transactions'] as $tbl => $cnt) : ?>
                                    <tr>
                                        <td class="fw-medium font-monospace"><?= esc($tbl) ?></td>
                                        <td>
                                            <span class="badge bg-light text-secondary border">Operasional / Transaksi</span>
                                        </td>
                                        <td class="text-end">
                                            <span class="badge <?= $cnt > 0 ? 'bg-danger' : 'bg-secondary' ?> px-2.5 py-1">
                                                <?= number_format($cnt) ?> baris
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="fw-medium">uploads/arsip/*.pdf</td>
                                    <td><span class="badge bg-light text-secondary border">File Fisik Digital</span></td>
                                    <td class="text-end">
                                        <span class="badge <?= $summary['files']['arsip_count'] > 0 ? 'bg-warning text-dark' : 'bg-secondary' ?> px-2.5 py-1">
                                            <?= $summary['files']['arsip_count'] ?> file
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-medium">uploads/spt/*.pdf</td>
                                    <td><span class="badge bg-light text-secondary border">File Fisik SPT</span></td>
                                    <td class="text-end">
                                        <span class="badge <?= $summary['files']['spt_count'] > 0 ? 'bg-warning text-dark' : 'bg-secondary' ?> px-2.5 py-1">
                                            <?= $summary['files']['spt_count'] ?> file
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 border-danger border-top border-4 mb-4">
                <div class="card-body p-4">
                    <h5 class="fw-bold text-danger mb-3 d-flex align-items-center gap-2">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                        Prosedur & Konfirmasi Keamanan
                    </h5>
                    <ul class="text-secondary small ps-3 mb-4" style="line-height: 1.8;">
                        <li><strong>Backup Otomatis:</strong> Sistem akan membuat dump database dan duplikat file storage di folder <code>backups/</code> sebelum proses pembersihan dimulai.</li>
                        <li><strong>Integritas Database:</strong> Struktur tabel, kolom, view, index, dan foreign key <strong>tidak akan dihapus/diubah</strong>.</li>
                        <li><strong>Master Data Terjaga:</strong> Seluruh OPD (22), Bidang (3), Klasifikasi (3), Role (5), dan akun Admin Pemkab tetap utuh.</li>
                        <li><strong>Transaksi Aman:</strong> Operasi database dijalankan dalam transaksi dan dapat dibatalkan jika terjadi error.</li>
                    </ul>

                    <form action="<?= base_url('/system-cleanse/execute') ?>" method="post" onsubmit="return confirm('PERINGATAN: Anda akan menghapus seluruh data SPT, Arsip, Berita Acara, Log Aktivitas, dan Notifikasi secara permanen setelah backup dibuat.\n\nKetik OK untuk melanjutkan proses reset data?')">
                        <?= csrf_field() ?>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger btn-lg py-3 fw-bold rounded-3 shadow-sm d-flex align-items-center justify-content-center gap-2">
                                <i class="bi bi-trash-fill fs-5"></i>
                                <span>Reset Seluruh Data Sekarang</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
