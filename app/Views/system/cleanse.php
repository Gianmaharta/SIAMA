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
                <i class="bi bi-shield-lock me-1"></i> Khusus Super Admin
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

    <!-- Panel Backup Database Mandiri (Khusus Super Admin) -->
    <?php if (session()->get('nama_role') === 'Super_Admin') : ?>
        <div class="card border-0 shadow-sm rounded-4 mb-4 border-primary border-top border-4">
            <div class="card-header bg-white border-0 py-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h5 class="fw-bold mb-1 text-dark d-flex align-items-center gap-2">
                        <i class="bi bi-database-fill-check text-primary"></i>
                        Backup Database (.SQL) — Khusus Super Admin
                    </h5>
                    <p class="text-secondary small mb-0">Buat dan unduh salinan penuh database SIAMA kapan saja tanpa menghapus atau mengubah data operasional.</p>
                </div>
                <form action="<?= base_url('/system-cleanse/backup') ?>" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" style="background-color: #1E60EC; border-color: #1E60EC;">
                        <i class="bi bi-cloud-arrow-down-fill"></i>
                        <span>Buat Backup Sekarang</span>
                    </button>
                </form>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($backups)) : ?>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0" style="font-size: 13.5px;">
                            <thead class="table-light">
                                <tr>
                                    <th class="ps-4">Tanggal & Waktu Backup</th>
                                    <th>Nama File Unduhan</th>
                                    <th>Tipe</th>
                                    <th>Ukuran</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backups as $bk) : ?>
                                    <tr>
                                        <td class="ps-4 fw-medium text-dark"><?= esc($bk['created_at']) ?></td>
                                        <td>
                                            <span class="font-monospace text-primary small"><?= esc($bk['filename']) ?></span>
                                        </td>
                                        <td>
                                            <?php if (!empty($bk['has_uploads'])) : ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-2.5 py-1">Database + Storage</span>
                                            <?php else : ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-2.5 py-1">Database (.sql)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary"><?= esc($bk['size_formatted']) ?></td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex align-items-center gap-1">
                                                <a href="<?= base_url('/system-cleanse/backup-download/' . $bk['folder']) ?>"
                                                   class="btn btn-sm btn-outline-primary rounded-2 d-inline-flex align-items-center justify-content-center"
                                                   style="width: 32px; height: 32px; padding: 0;"
                                                   data-bs-toggle="tooltip"
                                                   title="Download <?= esc($bk['filename']) ?>">
                                                    <i class="bi bi-download"></i>
                                                </a>
                                                <form action="<?= base_url('/system-cleanse/backup-delete/' . $bk['folder']) ?>"
                                                      method="post"
                                                      class="m-0 d-inline"
                                                      data-swal-title="Hapus File Backup?"
                                                      data-swal-confirm="File backup <?= esc($bk['filename']) ?> akan dihapus dari server."
                                                      data-swal-danger="true"
                                                      data-swal-btn="Ya, Hapus">
                                                    <?= csrf_field() ?>
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger rounded-2 d-inline-flex align-items-center justify-content-center"
                                                            style="width: 32px; height: 32px; padding: 0;"
                                                            data-bs-toggle="tooltip"
                                                            title="Hapus Backup">
                                                        <i class="bi bi-trash"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else : ?>
                    <div class="text-center py-4 text-secondary small">
                        <i class="bi bi-inbox fs-4 d-block mb-1 text-muted"></i>
                        Belum ada file backup database yang tersimpan. Klik <strong>Buat Backup Sekarang</strong> untuk membuat cadangan `.sql` pertama.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

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

                    <form action="<?= base_url('/system-cleanse/execute') ?>" method="post" 
                          data-swal-confirm="PERINGATAN KEAMANAN: Seluruh data operasional (SPT, Penugasan, Berita Acara, Pengesahan, Log Aktivitas, dan Notifikasi) akan dibersihkan secara permanen setelah cadangan otomatis disimpan. Apakah Anda yakin ingin melanjutkan?"
                          data-swal-title="Reset Seluruh Data Operasional?"
                          data-swal-danger="true"
                          data-swal-btn="Ya, Reset Sekarang">
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
