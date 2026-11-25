<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Reset &amp; Data Cleansing - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<style>
    .cl-card {
        background-color: #FFFFFF;
        border: 1px solid #E5EAF1;
        border-radius: 12px;
        box-shadow: 0 1px 4px rgba(15, 23, 42, 0.03);
    }

    .cl-stat-label {
        font-size: 12.5px;
        font-weight: 500;
        color: #64748B;
        margin-bottom: 6px;
    }

    .cl-stat-val {
        font-size: 24px;
        font-weight: 700;
        line-height: 1.2;
        margin-bottom: 4px;
        letter-spacing: -0.3px;
    }

    .cl-stat-sub {
        font-size: 12px;
        color: #64748B;
    }

    .cl-table th {
        font-size: 12px;
        font-weight: 600;
        color: #172033;
        background-color: #F8FAFC;
        border-bottom: 1px solid #E5EAF1;
        padding: 11px 16px;
        white-space: nowrap;
    }

    .cl-table td {
        font-size: 13px;
        color: #172033;
        padding: 12px 16px;
        border-bottom: 1px solid #F1F5F9;
        vertical-align: middle;
    }

    .cl-table tbody tr:last-child td {
        border-bottom: none;
    }

    .cl-danger-accent {
        width: 4px;
        height: 18px;
        background-color: #DC2626;
        border-radius: 4px;
        flex-shrink: 0;
    }

    .cl-count-pill {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 11.5px;
        font-weight: 600;
        padding: 3px 12px;
        border-radius: 50px;
        min-width: 68px;
    }
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="container-fluid px-0">
    <!-- Header Halaman -->
    <div class="mb-4">
        <h1 class="h4 fw-bold mb-1" style="color: #0B1528; letter-spacing: -0.3px;">
            Reset Seluruh Data Operasional (Data Cleansing)
        </h1>
        <p class="text-secondary small mb-0">
            Kembalikan aplikasi ke kondisi bersih seperti instalasi baru tanpa menghapus data master maupun struktur database.
        </p>
    </div>

    <!-- 4 Kartu Status Saat Ini -->
    <div class="row g-3 mb-4">
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cl-card p-3 px-4 h-100">
                <div class="cl-stat-label">Total Data Transaksi</div>
                <div class="cl-stat-val" style="color: #0B1528;"><?= number_format($summary['total_transactions']) ?></div>
                <div class="cl-stat-sub">Baris di seluruh tabel transaksi</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cl-card p-3 px-4 h-100">
                <div class="cl-stat-label">File Upload Storage</div>
                <div class="cl-stat-val" style="color: #0B1528;"><?= number_format($summary['files']['total_files']) ?></div>
                <div class="cl-stat-sub">File PDF arsip &amp; SPT di storage</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cl-card p-3 px-4 h-100">
                <div class="cl-stat-label">Master OPD &amp; Klasifikasi</div>
                <div class="cl-stat-val" style="color: #16A36A;">Utuh &amp; Aman</div>
                <div class="cl-stat-sub"><?= $summary['masters']['opd'] ?> OPD, <?= $summary['masters']['kode_klasifikasi'] ?> Klasifikasi</div>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-xl-3">
            <div class="cl-card p-3 px-4 h-100">
                <div class="cl-stat-label">Akun Utama Admin</div>
                <div class="cl-stat-val" style="color: #1E60EC;">Aktif</div>
                <div class="cl-stat-sub">admin.pemkab@siama.test</div>
            </div>
        </div>
    </div>

    <!-- Panel Backup Database Mandiri (Khusus Super Admin) -->
    <?php if (session()->get('nama_role') === 'Super_Admin') : ?>
        <div class="card cl-card mb-4">
            <div class="card-header bg-white border-0 px-4 pt-3 pb-3 d-flex flex-wrap align-items-center justify-content-between gap-2">
                <div>
                    <h2 class="h6 fw-bold mb-1" style="color: #0B1528;">Backup Database (.SQL)</h2>
                    <p class="text-secondary small mb-0">Buat dan unduh salinan penuh database SIAMA kapan saja tanpa menghapus atau mengubah data operasional.</p>
                </div>
                <form action="<?= base_url('/system-cleanse/backup') ?>" method="post" class="m-0">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-primary btn-sm px-3 py-2 rounded-3 fw-semibold d-inline-flex align-items-center gap-2" style="background-color: #1E60EC; border-color: #1E60EC;">
                        <i class="bi bi-cloud-arrow-down"></i>
                        <span>Buat Backup Sekarang</span>
                    </button>
                </form>
            </div>
            <div class="card-body p-0">
                <?php if (!empty($backups)) : ?>
                    <div class="table-responsive">
                        <table class="table cl-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Tanggal &amp; Waktu Backup</th>
                                    <th>Nama File Unduhan</th>
                                    <th>Tipe</th>
                                    <th>Ukuran</th>
                                    <th class="text-end pe-4">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($backups as $bk) : ?>
                                    <tr>
                                        <td class="ps-4 fw-medium"><?= esc($bk['created_at']) ?></td>
                                        <td>
                                            <a href="<?= base_url('/system-cleanse/backup-download/' . $bk['folder']) ?>" class="font-monospace text-decoration-underline small" style="color: #1E60EC;">
                                                <?= esc($bk['filename']) ?>
                                            </a>
                                        </td>
                                        <td>
                                            <?php if (!empty($bk['has_uploads'])) : ?>
                                                <span class="badge bg-info-subtle text-info border border-info-subtle rounded-pill px-3 py-1" style="font-size: 11px;">Database + Storage</span>
                                            <?php else : ?>
                                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle rounded-pill px-3 py-1" style="font-size: 11px;">Database (.sql)</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="text-secondary"><?= esc($bk['size_formatted']) ?></td>
                                        <td class="text-end pe-4">
                                            <div class="d-inline-flex align-items-center gap-2">
                                                <a href="<?= base_url('/system-cleanse/backup-download/' . $bk['folder']) ?>"
                                                   class="btn btn-sm btn-outline-primary rounded-2 d-inline-flex align-items-center gap-1 px-2.5"
                                                   style="font-size: 12px;">
                                                    <i class="bi bi-download"></i>
                                                    <span>Unduh</span>
                                                </a>
                                                <form action="<?= base_url('/system-cleanse/backup-delete/' . $bk['folder']) ?>"
                                                      method="post"
                                                      class="m-0 d-inline"
                                                      data-swal-title="Hapus File Backup?"
                                                      data-swal-confirm="File backup <?= esc($bk['filename']) ?> akan dihapus dari server."
                                                      data-swal-danger="true"
                                                      data-swal-btn="Iya">
                                                    <?= csrf_field() ?>
                                                    <button type="submit"
                                                            class="btn btn-sm btn-outline-danger rounded-2 d-inline-flex align-items-center gap-1 px-2.5"
                                                            style="font-size: 12px;">
                                                        <i class="bi bi-trash"></i>
                                                        <span>Hapus</span>
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
                    <div class="text-center py-4 text-secondary small border-top">
                        Belum ada file backup database yang tersimpan. Klik <strong>Buat Backup Sekarang</strong> untuk membuat cadangan `.sql` pertama.
                    </div>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Ringkasan Rinci & Prosedur Keamanan -->
    <div class="row g-4 align-items-start">
        <div class="col-12 col-lg-7">
            <div class="card cl-card">
                <div class="card-header bg-white border-0 px-4 pt-3 pb-2">
                    <h2 class="h6 fw-bold mb-0" style="color: #0B1528;">Rincian Data yang Akan Dikosongkan</h2>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table cl-table mb-0">
                            <thead>
                                <tr>
                                    <th class="ps-4">Tabel / Modul</th>
                                    <th>Kategori</th>
                                    <th class="text-center pe-4">Jumlah Data Saat Ini</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($summary['transactions'] as $tbl => $cnt) : ?>
                                    <tr>
                                        <td class="ps-4 fw-medium font-monospace"><?= esc($tbl) ?></td>
                                        <td>
                                            <span class="badge bg-light text-secondary border fw-normal">Operasional / Transaksi</span>
                                        </td>
                                        <td class="text-center pe-4">
                                            <span class="cl-count-pill <?= $cnt > 0 ? 'bg-danger bg-opacity-75 text-white' : 'bg-secondary bg-opacity-75 text-white' ?>">
                                                <?= number_format($cnt) ?> baris
                                            </span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <tr>
                                    <td class="ps-4 fw-medium">uploads/arsip/*.pdf</td>
                                    <td><span class="badge bg-light text-secondary border fw-normal">File Fisik Digital</span></td>
                                    <td class="text-center pe-4">
                                        <span class="cl-count-pill <?= $summary['files']['arsip_count'] > 0 ? 'bg-warning text-dark' : 'bg-secondary bg-opacity-75 text-white' ?>">
                                            <?= $summary['files']['arsip_count'] ?> file
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="ps-4 fw-medium">uploads/spt/*.pdf</td>
                                    <td><span class="badge bg-light text-secondary border fw-normal">File Fisik SPT</span></td>
                                    <td class="text-center pe-4">
                                        <span class="cl-count-pill <?= $summary['files']['spt_count'] > 0 ? 'bg-warning text-dark' : 'bg-secondary bg-opacity-75 text-white' ?>">
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

        <div class="col-12 col-lg-5">
            <div class="card cl-card">
                <div class="card-body p-4">
                    <div class="d-flex align-items-center gap-2 mb-3">
                        <span class="cl-danger-accent" aria-hidden="true"></span>
                        <h2 class="h6 fw-bold text-danger mb-0">Prosedur &amp; Konfirmasi Keamanan</h2>
                    </div>
                    <ul class="text-secondary small ps-3 mb-3" style="line-height: 1.75;">
                        <li><strong>Backup Otomatis:</strong> Sistem akan membuat dump database dan duplikat file storage di folder <code>backups/</code> sebelum proses pembersihan dimulai.</li>
                        <li><strong>Integritas Database:</strong> Struktur tabel, kolom, view, index, dan foreign key <strong>tidak akan dihapus/diubah</strong>.</li>
                        <li><strong>Master Data Terjaga:</strong> Seluruh OPD (<?= $summary['masters']['opd'] ?>), Bidang (3), Klasifikasi (<?= $summary['masters']['kode_klasifikasi'] ?>), Role (5), dan akun Admin Pemkab tetap utuh.</li>
                        <li><strong>Transaksi Aman:</strong> Operasi database dijalankan dalam transaksi dan dapat dibatalkan jika terjadi error.</li>
                    </ul>

                    <div class="alert alert-danger border-danger border-opacity-25 rounded-3 py-2 px-3 mb-3 small" role="alert" style="line-height: 1.6;">
                        <strong>Peringatan:</strong> Seluruh data operasional (SPT, arsip, berita acara, notifikasi, log aktivitas, dan file PDF terkait) akan <strong>dihapus permanen dan hilang</strong> dari sistem setelah reset dijalankan.
                    </div>

                    <form action="<?= base_url('/system-cleanse/execute') ?>" method="post"
                          data-swal-confirm="PERINGATAN KEAMANAN: Seluruh data operasional (SPT, Penugasan, Berita Acara, Pengesahan, Log Aktivitas, dan Notifikasi) akan dibersihkan secara permanen setelah cadangan otomatis disimpan. Apakah Anda yakin ingin melanjutkan?"
                          data-swal-title="Reset Seluruh Data Operasional?"
                          data-swal-danger="true"
                          data-swal-btn="Iya">
                        <?= csrf_field() ?>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-danger py-2.5 fw-semibold rounded-3" style="background-color: #DC2626; border-color: #DC2626; height: 44px;">
                                Reset Seluruh Data Sekarang
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>
