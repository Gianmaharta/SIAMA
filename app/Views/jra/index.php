<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Master Jadwal Retensi Arsip (JRA) - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/jra-master.css?v=' . (file_exists(FCPATH . 'css/jra-master.css') ? filemtime(FCPATH . 'css/jra-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Flash Message Feedback -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('success') ?>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close shadow-none" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php
// Fallback data contoh jika tabel database masih kosong agar visual halaman sesuai mockup
if (empty($jra_list)) {
    $jra_list = [
        [
            'id_jra'               => 'jra-sample-01',
            'kode'                 => '000.9.1',
            'nama_klasifikasi'     => 'Teletest',
            'deskripsi'            => 'Uraian pengujian telekomunikasi',
            'retensi_aktif'        => '2',
            'retensi_inaktif'      => '1',
            'keterangan_retensi'   => 'Musnah',
            'klasifikasi_keamanan' => 'Biasa',
            'hak_akses'            => 'Terbuka',
            'akses_publik'         => 'Terbuka untuk umum',
            'dasar_pertimbangan'   => 'testt',
            'unit_pengolah'        => 'Bagian Umum'
        ]
    ];
}
$totalData = count($jra_list);
?>

<!-- Header Halaman -->
<div class="jra-page-header">
    <h1 class="jra-page-title">Master Jadwal Retensi Arsip (JRA)</h1>
    <p class="jra-page-subtitle">Kelola data jadwal retensi arsip pada OPD Anda.</p>
</div>

<!-- Card Utama: Toolbar & Tabel Data JRA -->
<div class="jra-table-card">
    <!-- Toolbar Area -->
    <div class="jra-table-toolbar">
        <!-- Kolom Pencarian -->
        <div class="jra-search-wrap">
            <i class="bi bi-search jra-search-icon"></i>
            <input 
                type="text" 
                id="jraSearchInput" 
                class="jra-search-input" 
                placeholder="Cari kode klasifikasi atau keterangan..."
                autocomplete="off"
            >
            <button type="button" class="jra-clear-search d-none" id="jraClearSearchBtn" title="Bersihkan pencarian">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <!-- Tombol Tambah JRA Baru (Membuka Modal Overlay) -->
        <div class="jra-toolbar-actions">
            <button type="button" class="jra-btn-add border-0" data-bs-toggle="modal" data-bs-target="#modalTambahJra">
                <i class="bi bi-plus-lg"></i>
                <span>Tambah JRA Baru</span>
            </button>
        </div>
    </div>

    <!-- Tabel Data JRA -->
    <div class="table-responsive jra-table-responsive">
        <table class="table jra-table align-middle" id="jraTable">
            <colgroup>
                <col style="width: 44px;">
                <col style="width: 22%;">
                <col style="width: 112px;">
                <col style="width: 120px;">
                <col style="width: 118px;">
                <col style="width: 98px;">
                <col style="width: auto;">
                <col style="width: 158px;">
            </colgroup>
            <thead>
                <tr>
                    <th scope="col" class="col-no sortable-th" data-sort-key="no">
                        No <i class="bi bi-arrow-down-up jra-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-kode sortable-th" data-sort-key="kode">
                        Kode Klasifikasi <i class="bi bi-arrow-down-up jra-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-retensi sortable-th" data-sort-key="retensi_aktif">
                        Retensi Aktif <i class="bi bi-arrow-down-up jra-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-retensi sortable-th" data-sort-key="retensi_inaktif">
                        Retensi Inaktif <i class="bi bi-arrow-down-up jra-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-keterangan sortable-th" data-sort-key="keterangan">
                        Keterangan <i class="bi bi-arrow-down-up jra-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-keamanan sortable-th" data-sort-key="keamanan">
                        Keamanan <i class="bi bi-arrow-down-up jra-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-dasar sortable-th" data-sort-key="dasar">
                        Dasar Pertimbangan <i class="bi bi-arrow-down-up jra-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-aksi text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="jraTableBody">
                <?php $no = 1; foreach ($jra_list as $row) : 
                    $kode = $row['kode'] ?? '';
                    $nama = $row['nama_klasifikasi'] ?? '';
                    $kodeText = trim($kode . ' - ' . $nama);
                    $retensiAktif = $row['retensi_aktif'] ?? '0';
                    $retensiInaktif = $row['retensi_inaktif'] ?? '0';
                    $retensiAktifText = esc($retensiAktif) . ' Tahun';
                    $retensiInaktifText = esc($retensiInaktif) . ' Tahun';
                    $keterangan = $row['keterangan_retensi'] ?? '-';
                    $keamanan = $row['klasifikasi_keamanan'] ?? 'Biasa';
                    $dasar = $row['dasar_pertimbangan'] ?? '-';
                    $deskripsi = $row['deskripsi'] ?? '';
                    $hakAkses = $row['hak_akses'] ?? 'Terbuka';
                    $aksesPublik = $row['akses_publik'] ?? '';
                    $unitPengolah = $row['unit_pengolah'] ?? '';

                    // Badge Keterangan
                    $ketLower = strtolower(trim($keterangan));
                    $isMusnah = str_contains($ketLower, 'musnah');
                    $badgeKeteranganClass = 'badge-dot-biasa';
                    if ($isMusnah) {
                        $badgeKeteranganClass = 'badge-dot-musnah';
                    } elseif (str_contains($ketLower, 'permanen')) {
                        $badgeKeteranganClass = 'badge-dot-permanen';
                    } elseif (str_contains($ketLower, 'dinilai')) {
                        $badgeKeteranganClass = 'badge-dot-dinilai';
                    }

                    // Badge Keamanan
                    $keamLower = strtolower(trim($keamanan));
                    $badgeKeamananClass = 'badge-dot-biasa';
                    if (str_contains($keamLower, 'terbatas')) {
                        $badgeKeamananClass = 'badge-dot-terbatas';
                    } elseif (str_contains($keamLower, 'rahasia')) {
                        $badgeKeamananClass = 'badge-dot-rahasia';
                    }
                ?>
                    <tr class="jra-row" 
                        data-no="<?= $no ?>"
                        data-kode="<?= esc(strtolower($kodeText)) ?>"
                        data-retensi-aktif="<?= esc(strtolower($retensiAktifText)) ?>"
                        data-retensi-inaktif="<?= esc(strtolower($retensiInaktifText)) ?>"
                        data-keterangan="<?= esc(strtolower($keterangan)) ?>"
                        data-keamanan="<?= esc(strtolower($keamanan)) ?>"
                        data-dasar="<?= esc(strtolower($dasar)) ?>">
                        <td class="col-no"><?= $no++ ?></td>
                        <td class="col-kode">
                            <span class="fw-bold text-dark"><?= esc($kodeText) ?></span>
                        </td>
                        <td class="col-retensi">
                            <?= esc($retensiAktifText) ?>
                        </td>
                        <td class="col-retensi">
                            <?= esc($retensiInaktifText) ?>
                        </td>
                        <td class="col-keterangan">
                            <span class="badge-dot-pill <?= $badgeKeteranganClass ?>">
                                <span class="badge-dot"></span>
                                <?= esc($keterangan) ?>
                            </span>
                        </td>
                        <td class="col-keamanan">
                            <span class="badge-dot-pill <?= $badgeKeamananClass ?>">
                                <span class="badge-dot"></span>
                                <?= esc($keamanan) ?>
                            </span>
                        </td>
                        <td class="col-dasar">
                            <?= esc($dasar) ?>
                        </td>
                        <td class="col-aksi text-center">
                            <div class="jra-btn-action-group">
                                <?php if ($isMusnah) : ?>
                                    <button 
                                        type="button" 
                                        class="jra-action-btn btn-action-disabled" 
                                        disabled 
                                        title="Data JRA dengan status Musnah tidak dapat diedit"
                                        aria-label="Edit dinonaktifkan (Status Musnah)"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        class="jra-action-btn btn-action-disabled" 
                                        disabled 
                                        title="Data JRA dengan status Musnah tidak dapat dihapus"
                                        aria-label="Hapus dinonaktifkan (Status Musnah)"
                                    >
                                        <i class="bi bi-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                <?php else : ?>
                                    <button 
                                        type="button" 
                                        class="jra-action-btn btn-action-edit" 
                                        title="Edit JRA"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalEditJra"
                                        data-id="<?= esc($row['id_jra']) ?>"
                                        data-kode="<?= esc($kode) ?>"
                                        data-nama="<?= esc($nama) ?>"
                                        data-deskripsi="<?= esc($deskripsi) ?>"
                                        data-retensi-aktif="<?= esc($retensiAktif) ?>"
                                        data-retensi-inaktif="<?= esc($retensiInaktif) ?>"
                                        data-keterangan="<?= esc($keterangan) ?>"
                                        data-keamanan="<?= esc($keamanan) ?>"
                                        data-hak-akses="<?= esc($hakAkses) ?>"
                                        data-akses-publik="<?= esc($aksesPublik) ?>"
                                        data-dasar="<?= esc($dasar) ?>"
                                        data-unit-pengolah="<?= esc($unitPengolah) ?>"
                                    >
                                        <i class="bi bi-pencil-square"></i>
                                        <span>Edit</span>
                                    </button>
                                    <button 
                                        type="button" 
                                        class="jra-action-btn btn-action-delete" 
                                        title="Hapus JRA"
                                        data-bs-toggle="modal" 
                                        data-bs-target="#modalHapusJra"
                                        data-id="<?= esc($row['id_jra']) ?>"
                                        data-kode="<?= esc($kodeText) ?>"
                                        data-keterangan="<?= esc($keterangan) ?>"
                                    >
                                        <i class="bi bi-trash"></i>
                                        <span>Hapus</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Empty State (Ketika data hasil pencarian nihil) -->
    <div class="jra-empty-state d-none" id="jraEmptyState">
        <div class="jra-empty-icon-wrap">
            <i class="bi bi-folder-x"></i>
        </div>
        <h5 class="jra-empty-title">Data JRA Tidak Ditemukan</h5>
        <p class="jra-empty-desc">Tidak ada data jadwal retensi arsip yang sesuai dengan kata kunci pencarian Anda.</p>
    </div>

    <!-- Table Footer & Pagination -->
    <div class="jra-table-footer">
        <div class="jra-pagination-info" id="jraPaginationInfo">
            Menampilkan 1 - <?= $totalData ?> dari <?= $totalData ?> data
        </div>
        <ul class="jra-pagination">
            <li>
                <button type="button" class="jra-page-btn disabled" aria-label="Halaman Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </button>
            </li>
            <li>
                <button type="button" class="jra-page-btn active" aria-label="Halaman 1">
                    1
                </button>
            </li>
            <li>
                <button type="button" class="jra-page-btn disabled" aria-label="Halaman Selanjutnya">
                    <i class="bi bi-chevron-right"></i>
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: TAMBAH MASTER JRA (OVERLAY BESAR)                       -->
<!-- ============================================================== -->
<div class="modal fade" id="modalTambahJra" tabindex="-1" aria-labelledby="modalTambahJraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable jra-modal-dialog-large">
        <div class="modal-content jra-modal-large-content">
            <div class="modal-header jra-modal-header d-flex align-items-start justify-content-between">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1" id="modalTambahJraLabel" style="font-size: 18px; letter-spacing: -0.2px;">
                        Tambah Master JRA
                    </h5>
                    <p class="text-secondary small mb-0" style="font-size: 13px; color: #667085;">
                        Tambahkan aturan jadwal retensi arsip baru ke dalam sistem.
                    </p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            
            <form action="<?= base_url('/jra/store') ?>" method="POST" id="formTambahJra">
                <?= csrf_field() ?>
                <div class="modal-body jra-modal-body">
                    <!-- Baris Pertama: Kode Klasifikasi & Uraian/Nama Klasifikasi -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label for="create_kode" class="form-label jra-form-label">
                                Kode Klasifikasi <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="kode" 
                                id="create_kode" 
                                class="form-control jra-form-control" 
                                placeholder="Contoh: 000.9.1" 
                                value="<?= old('kode') ?>" 
                                required
                            >
                        </div>
                        <div class="col-md-7">
                            <label for="create_nama_klasifikasi" class="form-label jra-form-label">
                                Uraian / Nama Klasifikasi <span class="text-danger">*</span>
                            </label>
                            <input 
                                type="text" 
                                name="nama_klasifikasi" 
                                id="create_nama_klasifikasi" 
                                class="form-control jra-form-control" 
                                placeholder="Contoh: Teletest" 
                                value="<?= old('nama_klasifikasi') ?>" 
                                required
                            >
                        </div>
                    </div>

                    <!-- Baris Kedua: Deskripsi Klasifikasi (Opsional) -->
                    <div class="mb-3">
                        <label for="create_deskripsi" class="form-label jra-form-label">
                            Deskripsi Klasifikasi <span class="text-muted fw-normal" style="font-size: 12px;">(Opsional)</span>
                        </label>
                        <textarea 
                            name="deskripsi" 
                            id="create_deskripsi" 
                            class="form-control jra-form-control" 
                            rows="2" 
                            placeholder="Uraian ringkas mengenai klasifikasi arsip..."
                        ><?= old('deskripsi') ?></textarea>
                    </div>

                    <!-- Baris Ketiga: Retensi Aktif, Inaktif, Keterangan, Keamanan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="create_retensi_aktif" class="form-label jra-form-label">
                                Retensi Aktif (Tahun) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input 
                                    type="number" 
                                    name="retensi_aktif" 
                                    id="create_retensi_aktif" 
                                    class="form-control jra-form-control" 
                                    value="<?= old('retensi_aktif', 2) ?>" 
                                    min="0" 
                                    required
                                >
                                <span class="jra-input-suffix">Tahun</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="create_retensi_inaktif" class="form-label jra-form-label">
                                Retensi Inaktif (Tahun) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input 
                                    type="number" 
                                    name="retensi_inaktif" 
                                    id="create_retensi_inaktif" 
                                    class="form-control jra-form-control" 
                                    value="<?= old('retensi_inaktif', 1) ?>" 
                                    min="0" 
                                    required
                                >
                                <span class="jra-input-suffix">Tahun</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="create_keterangan_retensi" class="form-label jra-form-label">
                                Keterangan Retensi <span class="text-danger">*</span>
                            </label>
                            <select name="keterangan_retensi" id="create_keterangan_retensi" class="form-select jra-form-control" required>
                                <option value="Musnah" <?= old('keterangan_retensi') === 'Musnah' ? 'selected' : '' ?>>Musnah</option>
                                <option value="Permanen" <?= old('keterangan_retensi') === 'Permanen' ? 'selected' : '' ?>>Permanen</option>
                                <option value="Dinilai Kembali" <?= old('keterangan_retensi') === 'Dinilai Kembali' ? 'selected' : '' ?>>Dinilai Kembali</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="create_klasifikasi_keamanan" class="form-label jra-form-label">
                                Keamanan <span class="text-danger">*</span>
                            </label>
                            <select name="klasifikasi_keamanan" id="create_klasifikasi_keamanan" class="form-select jra-form-control" required>
                                <option value="Biasa" <?= old('klasifikasi_keamanan') === 'Biasa' ? 'selected' : '' ?>>Biasa</option>
                                <option value="Terbatas" <?= old('klasifikasi_keamanan') === 'Terbatas' ? 'selected' : '' ?>>Terbatas</option>
                                <option value="Rahasia" <?= old('klasifikasi_keamanan') === 'Rahasia' ? 'selected' : '' ?>>Rahasia</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris Keempat: Hak Akses, Akses Publik, Unit Pengolah -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="create_hak_akses" class="form-label jra-form-label">Hak Akses</label>
                            <select name="hak_akses" id="create_hak_akses" class="form-select jra-form-control">
                                <option value="Terbuka" <?= old('hak_akses') === 'Terbuka' ? 'selected' : '' ?>>Terbuka</option>
                                <option value="Terbatas" <?= old('hak_akses') === 'Terbatas' ? 'selected' : '' ?>>Terbatas</option>
                                <option value="Internal" <?= old('hak_akses') === 'Internal' ? 'selected' : '' ?>>Internal</option>
                                <option value="Rahasia" <?= old('hak_akses') === 'Rahasia' ? 'selected' : '' ?>>Rahasia</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="create_akses_publik" class="form-label jra-form-label">Akses Publik</label>
                            <input 
                                type="text" 
                                name="akses_publik" 
                                id="create_akses_publik" 
                                class="form-control jra-form-control" 
                                placeholder="Akses bagi publik jika ada"
                                value="<?= old('akses_publik') ?>"
                            >
                        </div>
                        <div class="col-md-4">
                            <label for="create_unit_pengolah" class="form-label jra-form-label">Unit Pengolah</label>
                            <input 
                                type="text" 
                                name="unit_pengolah" 
                                id="create_unit_pengolah" 
                                class="form-control jra-form-control" 
                                placeholder="Contoh: Bagian Umum / Sekretariat"
                                value="<?= old('unit_pengolah') ?>"
                            >
                        </div>
                    </div>

                    <!-- Baris Kelima: Dasar Pertimbangan (Opsional) -->
                    <div class="mb-2">
                        <label for="create_dasar_pertimbangan" class="form-label jra-form-label">
                            Dasar Pertimbangan <span class="text-muted fw-normal" style="font-size: 12px;">(Opsional)</span>
                        </label>
                        <textarea 
                            name="dasar_pertimbangan" 
                            id="create_dasar_pertimbangan" 
                            class="form-control jra-form-control" 
                            rows="2" 
                            placeholder="Dasar hukum / peraturan pertimbangan retensi..."
                        ><?= old('dasar_pertimbangan') ?></textarea>
                    </div>
                </div>

                <div class="modal-footer jra-modal-footer d-flex align-items-center justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 text-secondary fw-medium" data-bs-dismiss="modal" style="font-size: 13.5px;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium d-inline-flex align-items-center gap-2" style="background-color: #1E60EC; border-color: #1E60EC; font-size: 13.5px;">
                        <i class="bi bi-check2 fs-6"></i>
                        <span>Simpan JRA</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: EDIT MASTER JRA (OVERLAY BESAR)                         -->
<!-- ============================================================== -->
<div class="modal fade" id="modalEditJra" tabindex="-1" aria-labelledby="modalEditJraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable jra-modal-dialog-large">
        <div class="modal-content jra-modal-large-content">
            <div class="modal-header jra-modal-header d-flex align-items-start justify-content-between">
                <div>
                    <h5 class="modal-title fw-bold text-dark mb-1" id="modalEditJraLabel" style="font-size: 18px; letter-spacing: -0.2px;">
                        Edit Master JRA
                    </h5>
                    <p class="text-secondary small mb-0" style="font-size: 13px; color: #667085;">
                        Perbarui aturan jadwal retensi arsip pada sistem.
                    </p>
                </div>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            
            <form action="#" method="POST" id="formEditJra">
                <?= csrf_field() ?>
                <div class="modal-body jra-modal-body">
                    <!-- Baris Pertama: Kode Klasifikasi & Uraian/Nama Klasifikasi -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-5">
                            <label for="edit_kode" class="form-label jra-form-label">
                                Kode Klasifikasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="kode" id="edit_kode" class="form-control jra-form-control" required>
                        </div>
                        <div class="col-md-7">
                            <label for="edit_nama_klasifikasi" class="form-label jra-form-label">
                                Uraian / Nama Klasifikasi <span class="text-danger">*</span>
                            </label>
                            <input type="text" name="nama_klasifikasi" id="edit_nama_klasifikasi" class="form-control jra-form-control" required>
                        </div>
                    </div>

                    <!-- Baris Kedua: Deskripsi Klasifikasi (Opsional) -->
                    <div class="mb-3">
                        <label for="edit_deskripsi" class="form-label jra-form-label">
                            Deskripsi Klasifikasi <span class="text-muted fw-normal" style="font-size: 12px;">(Opsional)</span>
                        </label>
                        <textarea name="deskripsi" id="edit_deskripsi" class="form-control jra-form-control" rows="2"></textarea>
                    </div>

                    <!-- Baris Ketiga: Retensi Aktif, Inaktif, Keterangan, Keamanan -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-3">
                            <label for="edit_retensi_aktif" class="form-label jra-form-label">
                                Retensi Aktif (Tahun) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="retensi_aktif" id="edit_retensi_aktif" class="form-control jra-form-control" min="0" required>
                                <span class="jra-input-suffix">Tahun</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_retensi_inaktif" class="form-label jra-form-label">
                                Retensi Inaktif (Tahun) <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <input type="number" name="retensi_inaktif" id="edit_retensi_inaktif" class="form-control jra-form-control" min="0" required>
                                <span class="jra-input-suffix">Tahun</span>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_keterangan_retensi" class="form-label jra-form-label">
                                Keterangan Retensi <span class="text-danger">*</span>
                            </label>
                            <select name="keterangan_retensi" id="edit_keterangan_retensi" class="form-select jra-form-control" required>
                                <option value="Musnah">Musnah</option>
                                <option value="Permanen">Permanen</option>
                                <option value="Dinilai Kembali">Dinilai Kembali</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label for="edit_klasifikasi_keamanan" class="form-label jra-form-label">
                                Keamanan <span class="text-danger">*</span>
                            </label>
                            <select name="klasifikasi_keamanan" id="edit_klasifikasi_keamanan" class="form-select jra-form-control" required>
                                <option value="Biasa">Biasa</option>
                                <option value="Terbatas">Terbatas</option>
                                <option value="Rahasia">Rahasia</option>
                            </select>
                        </div>
                    </div>

                    <!-- Baris Keempat: Hak Akses, Akses Publik, Unit Pengolah -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label for="edit_hak_akses" class="form-label jra-form-label">Hak Akses</label>
                            <select name="hak_akses" id="edit_hak_akses" class="form-select jra-form-control">
                                <option value="Terbuka">Terbuka</option>
                                <option value="Terbatas">Terbatas</option>
                                <option value="Internal">Internal</option>
                                <option value="Rahasia">Rahasia</option>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label for="edit_akses_publik" class="form-label jra-form-label">Akses Publik</label>
                            <input type="text" name="akses_publik" id="edit_akses_publik" class="form-control jra-form-control">
                        </div>
                        <div class="col-md-4">
                            <label for="edit_unit_pengolah" class="form-label jra-form-label">Unit Pengolah</label>
                            <input type="text" name="unit_pengolah" id="edit_unit_pengolah" class="form-control jra-form-control">
                        </div>
                    </div>

                    <!-- Baris Kelima: Dasar Pertimbangan (Opsional) -->
                    <div class="mb-2">
                        <label for="edit_dasar_pertimbangan" class="form-label jra-form-label">
                            Dasar Pertimbangan <span class="text-muted fw-normal" style="font-size: 12px;">(Opsional)</span>
                        </label>
                        <textarea name="dasar_pertimbangan" id="edit_dasar_pertimbangan" class="form-control jra-form-control" rows="2"></textarea>
                    </div>
                </div>

                <div class="modal-footer jra-modal-footer d-flex align-items-center justify-content-end gap-2">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 text-secondary fw-medium" data-bs-dismiss="modal" style="font-size: 13.5px;">
                        Batal
                    </button>
                    <button type="submit" class="btn btn-primary px-4 py-2 rounded-3 fw-medium d-inline-flex align-items-center gap-2" style="background-color: #1E60EC; border-color: #1E60EC; font-size: 13.5px;">
                        <i class="bi bi-check2 fs-6"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: KONFIRMASI HAPUS JRA                                    -->
<!-- ============================================================== -->
<div class="modal fade" id="modalHapusJra" tabindex="-1" aria-labelledby="modalHapusJraLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered jra-modal-dialog">
        <div class="modal-content jra-modal-content text-center p-3 p-sm-4 position-relative">
            <div class="modal-body p-0 pt-2 pb-2">
                <button type="button" class="btn-close position-absolute top-0 end-0 m-3 shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 58px; height: 58px; background-color: #FEE2E2; color: #DC2626; font-size: 24px;">
                    <i class="bi bi-trash3-fill"></i>
                </div>
                
                <h5 class="fw-bold mb-2 text-dark" id="modalHapusJraLabel" style="font-size: 18px;">Hapus Data JRA?</h5>
                
                <p class="text-secondary small mb-3 px-2">
                    Apakah Anda yakin ingin menghapus data jadwal retensi arsip ini? Tindakan ini tidak dapat dibatalkan.
                </p>

                <div class="p-3 mb-3 bg-light rounded-3 text-start border small">
                    <div class="text-muted small mb-1">Kode Klasifikasi:</div>
                    <strong class="d-block text-dark mb-2" id="deleteJraKode">000.9.1 - Teletest</strong>
                    <div class="text-muted small mb-1">Keterangan Retensi:</div>
                    <span class="text-dark fw-medium" id="deleteJraKeterangan">Musnah</span>
                </div>

                <div class="d-flex align-items-center justify-content-center gap-2 pt-1">
                    <button type="button" class="btn btn-light border px-4 py-2 rounded-3 text-secondary fw-medium flex-fill" data-bs-dismiss="modal" style="font-size: 13.5px;">
                        Batal
                    </button>
                    <a href="#" id="btnConfirmDeleteJra" class="btn btn-danger px-4 py-2 rounded-3 fw-medium flex-fill d-inline-flex align-items-center justify-content-center gap-2" style="font-size: 13.5px; background-color: #DC2626; border-color: #DC2626;">
                        <i class="bi bi-trash"></i>
                        <span>Ya, Hapus</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('jraSearchInput');
    const clearBtn = document.getElementById('jraClearSearchBtn');
    const tableBody = document.getElementById('jraTableBody');
    const emptyState = document.getElementById('jraEmptyState');
    const paginationInfo = document.getElementById('jraPaginationInfo');
    const tableRows = Array.from(document.querySelectorAll('.jra-row'));
    const totalData = tableRows.length;

    // Filter Pencarian Real-Time
    function filterTable() {
        const query = searchInput.value.trim().toLowerCase();
        
        if (query.length > 0) {
            clearBtn.classList.remove('d-none');
        } else {
            clearBtn.classList.add('d-none');
        }

        let visibleCount = 0;
        tableRows.forEach(row => {
            const kode = row.getAttribute('data-kode') || '';
            const retensiAktif = row.getAttribute('data-retensi-aktif') || '';
            const retensiInaktif = row.getAttribute('data-retensi-inaktif') || '';
            const keterangan = row.getAttribute('data-keterangan') || '';
            const keamanan = row.getAttribute('data-keamanan') || '';
            const dasar = row.getAttribute('data-dasar') || '';

            const match = kode.includes(query) ||
                          retensiAktif.includes(query) ||
                          retensiInaktif.includes(query) ||
                          keterangan.includes(query) ||
                          keamanan.includes(query) ||
                          dasar.includes(query);

            if (match) {
                row.style.display = '';
                visibleCount++;
            } else {
                row.style.display = 'none';
            }
        });

        // Update Empty State & Info Pagination
        if (visibleCount === 0) {
            emptyState.classList.remove('d-none');
            if (paginationInfo) {
                paginationInfo.textContent = 'Menampilkan 0 data';
            }
        } else {
            emptyState.classList.add('d-none');
            if (paginationInfo) {
                paginationInfo.textContent = `Menampilkan 1 - ${visibleCount} dari ${totalData} data`;
            }
        }
    }

    if (searchInput) {
        searchInput.addEventListener('input', filterTable);
    }

    if (clearBtn) {
        clearBtn.addEventListener('click', function() {
            searchInput.value = '';
            filterTable();
            searchInput.focus();
        });
    }

    // Modal Edit JRA Populate Form Data
    const modalEditJra = document.getElementById('modalEditJra');
    if (modalEditJra) {
        modalEditJra.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button || button.disabled) {
                event.preventDefault();
                return;
            }

            const keterangan = (button.getAttribute('data-keterangan') || '').toLowerCase();
            if (keterangan.includes('musnah')) {
                event.preventDefault();
                return;
            }

            const id = button.getAttribute('data-id');
            const formEdit = document.getElementById('formEditJra');
            if (formEdit) {
                formEdit.action = '<?= base_url('/jra/update/') ?>/' + id;
            }

            document.getElementById('edit_kode').value = button.getAttribute('data-kode') || '';
            document.getElementById('edit_nama_klasifikasi').value = button.getAttribute('data-nama') || '';
            document.getElementById('edit_deskripsi').value = button.getAttribute('data-deskripsi') || '';
            document.getElementById('edit_retensi_aktif').value = button.getAttribute('data-retensi-aktif') || '2';
            document.getElementById('edit_retensi_inaktif').value = button.getAttribute('data-retensi-inaktif') || '1';
            document.getElementById('edit_keterangan_retensi').value = button.getAttribute('data-keterangan') || 'Musnah';
            document.getElementById('edit_klasifikasi_keamanan').value = button.getAttribute('data-keamanan') || 'Biasa';
            document.getElementById('edit_hak_akses').value = button.getAttribute('data-hak-akses') || 'Terbuka';
            document.getElementById('edit_akses_publik').value = button.getAttribute('data-akses-publik') || '';
            document.getElementById('edit_unit_pengolah').value = button.getAttribute('data-unit-pengolah') || '';
            document.getElementById('edit_dasar_pertimbangan').value = button.getAttribute('data-dasar') || '';
        });
    }

    // Modal Konfirmasi Hapus Data
    const modalHapusJra = document.getElementById('modalHapusJra');
    if (modalHapusJra) {
        modalHapusJra.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (!button || button.disabled) {
                event.preventDefault();
                return;
            }

            const keterangan = (button.getAttribute('data-keterangan') || '').toLowerCase();
            if (keterangan.includes('musnah')) {
                event.preventDefault();
                return;
            }

            const id = button.getAttribute('data-id');
            const kode = button.getAttribute('data-kode');
            const ketVal = button.getAttribute('data-keterangan');

            const deleteKodeEl = document.getElementById('deleteJraKode');
            const deleteKetEl = document.getElementById('deleteJraKeterangan');
            const btnConfirm = document.getElementById('btnConfirmDeleteJra');

            if (deleteKodeEl) deleteKodeEl.textContent = kode || '-';
            if (deleteKetEl) deleteKetEl.textContent = ketVal || '-';
            if (btnConfirm) {
                btnConfirm.href = '<?= base_url('/jra/delete/') ?>/' + id;
            }
        });
    }

    // Sortable Headers
    let sortDirection = {};
    document.querySelectorAll('.sortable-th').forEach(th => {
        th.addEventListener('click', function() {
            const sortKey = this.getAttribute('data-sort-key');
            if (!sortKey) return;

            const isAsc = !sortDirection[sortKey];
            sortDirection = {}; // reset others
            sortDirection[sortKey] = isAsc;

            // Reset icons
            document.querySelectorAll('.jra-sort-icon').forEach(icon => {
                icon.className = 'bi bi-arrow-down-up jra-sort-icon';
            });

            const currentIcon = this.querySelector('.jra-sort-icon');
            if (currentIcon) {
                currentIcon.className = isAsc ? 'bi bi-sort-down jra-sort-icon text-primary' : 'bi bi-sort-up jra-sort-icon text-primary';
            }

            const rows = Array.from(tableBody.querySelectorAll('.jra-row'));
            rows.sort((a, b) => {
                let valA = a.getAttribute('data-' + sortKey.replace('_', '-')) || '';
                let valB = b.getAttribute('data-' + sortKey.replace('_', '-')) || '';

                // Try numeric comparison if retensi
                const numA = parseInt(valA, 10);
                const numB = parseInt(valB, 10);
                if (!isNaN(numA) && !isNaN(numB)) {
                    return isAsc ? numA - numB : numB - numA;
                }

                return isAsc ? valA.localeCompare(valB) : valB.localeCompare(valA);
            });

            // Re-append sorted rows and re-index column No
            let newNo = 1;
            rows.forEach(r => {
                const noCell = r.querySelector('.col-no');
                if (noCell) noCell.textContent = newNo++;
                tableBody.appendChild(r);
            });
        });
    });

    // Pindahkan modals ke <body> agar terhindar dari stacking context .app-layout yang fixed
    ['modalTambahJra', 'modalEditJra', 'modalHapusJra'].forEach(function(modalId) {
        const m = document.getElementById(modalId);
        if (m && m.parentElement !== document.body) {
            document.body.appendChild(m);
        }
    });

    <?php if (session()->getFlashdata('error') && old('kode')) : ?>
        // Otomatis buka kembali modal jika ada error validasi
        const modalTambah = document.getElementById('modalTambahJra');
        if (modalTambah) {
            bootstrap.Modal.getOrCreateInstance(modalTambah).show();
        }
    <?php endif; ?>
});
</script>
<?= $this->endSection() ?>
