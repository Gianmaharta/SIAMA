<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Master Data OPD - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/opd-master.css?v=' . (file_exists(FCPATH . 'css/opd-master.css') ? filemtime(FCPATH . 'css/opd-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

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

<!-- Header Halaman -->
<div class="opd-page-header">
    <h1 class="opd-page-title">Master Data OPD</h1>
    <p class="opd-page-subtitle">Kelola data Organisasi Perangkat Daerah (OPD) di Kabupaten Buleleng.</p>
</div>

<!-- Kartu Statistik (3 Kartu dalam 1 Baris) -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Kartu 1: Total OPD Terdaftar -->
    <div class="col-12 col-md-4">
        <div class="opd-stat-card">
            <div class="opd-stat-icon-wrapper bg-icon-blue">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="opd-stat-body">
                <span class="opd-stat-title">Total OPD Terdaftar</span>
                <div class="opd-stat-number-wrap">
                    <span class="opd-stat-number" id="statTotalOpd"><?= number_format($totalOpd ?? 0, 0, ',', '.') ?></span>
                    <span class="opd-stat-unit">OPD</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 2: OPD Aktif -->
    <div class="col-12 col-md-4">
        <div class="opd-stat-card">
            <div class="opd-stat-icon-wrapper bg-icon-green">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="opd-stat-body">
                <span class="opd-stat-title">OPD Aktif</span>
                <div class="opd-stat-number-wrap">
                    <span class="opd-stat-number" id="statActiveOpd"><?= number_format($activeOpd ?? 0, 0, ',', '.') ?></span>
                    <span class="opd-stat-unit">OPD</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 3: OPD Non Aktif -->
    <div class="col-12 col-md-4">
        <div class="opd-stat-card">
            <div class="opd-stat-icon-wrapper bg-icon-red">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="opd-stat-body">
                <span class="opd-stat-title">OPD Non Aktif</span>
                <div class="opd-stat-number-wrap">
                    <span class="opd-stat-number" id="statInactiveOpd"><?= number_format($inactiveOpd ?? 0, 0, ',', '.') ?></span>
                    <span class="opd-stat-unit">OPD</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel Tabel Master OPD -->
<div class="opd-table-card">
    <!-- Toolbar Tabel -->
    <div class="opd-table-toolbar">
        <!-- Pencarian OPD -->
        <div class="opd-search-wrap">
            <i class="bi bi-search opd-search-icon"></i>
            <input 
                type="text" 
                id="opdSearchInput" 
                class="opd-search-input" 
                placeholder="Cari nama OPD, kode, atau ID..."
                autocomplete="off"
            >
            <button type="button" class="opd-clear-search d-none" id="opdClearSearchBtn" title="Bersihkan pencarian">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <!-- Filter Status, Tampilkan & Tombol Tambah OPD -->
        <div class="opd-toolbar-actions">
            <!-- Pilihan Jumlah Data Per Halaman -->
            <div class="opd-filter-item">
                <label for="opdPerPageSelect" class="opd-filter-label">Tampilkan</label>
                <select id="opdPerPageSelect" class="opd-select-control opd-select-per-page" aria-label="Jumlah Data Per Halaman">
                    <option value="5">5</option>
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="all">Semua</option>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="opd-filter-item">
                <label for="opdStatusFilter" class="opd-filter-label">Status</label>
                <select id="opdStatusFilter" class="opd-select-control opd-select-status" aria-label="Filter Status OPD">
                    <option value="all">Semua Status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Non Aktif</option>
                </select>
            </div>

            <!-- Tombol Tambah OPD -->
            <button 
                type="button" 
                class="opd-btn-add" 
                data-bs-toggle="modal" 
                data-bs-target="#modalTambahOpd"
            >
                <i class="bi bi-plus-lg"></i>
                <span>Tambah OPD</span>
            </button>
        </div>
    </div>

    <!-- Tabel Data OPD -->
    <div class="table-responsive opd-table-responsive">
        <table class="table opd-table align-middle" id="opdTable">
            <thead>
                <tr>
                    <th scope="col" class="col-no">NO</th>
                    <th scope="col" class="col-id">ID</th>
                    <th scope="col" class="col-kode">KODE OPD</th>
                    <th scope="col" class="col-nama">NAMA OPD</th>
                    <th scope="col" class="col-kuota">KUOTA STORAGE (MB)</th>
                    <th scope="col" class="col-status">STATUS AKTIF</th>
                    <th scope="col" class="col-aksi">AKSI</th>
                </tr>
            </thead>
            <tbody id="opdTableBody">
                <!-- Data baris akan dirender dinamis via script untuk pagination dan pencarian -->
            </tbody>
        </table>
    </div>

    <!-- Empty State (Ketika data tidak ditemukan) -->
    <div class="opd-empty-state d-none" id="opdEmptyState">
        <div class="empty-icon-wrap">
            <i class="bi bi-search"></i>
        </div>
        <h5 class="empty-title">Data OPD Tidak Ditemukan</h5>
        <p class="empty-desc">Tidak ada hasil yang cocok dengan kata kunci atau filter status yang Anda pilih.</p>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnResetFilter">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter & Pencarian
        </button>
    </div>

    <!-- Footer & Pagination -->
    <div class="opd-table-footer">
        <div class="opd-pagination-info" id="opdPaginationInfo">
            Menampilkan 0 dari 0 data
        </div>
        <div class="opd-pagination-controls" id="opdPaginationControls">
            <!-- Tombol Pagination -->
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: TAMBAH DATA OPD                                          -->
<!-- ============================================================== -->
<div class="modal fade" id="modalTambahOpd" tabindex="-1" aria-labelledby="modalTambahOpdLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable opd-modal-dialog">
        <div class="modal-content opd-modal">
            <div class="modal-header opd-modal-header">
                <h5 class="modal-title opd-modal-title" id="modalTambahOpdLabel">
                    <i class="bi bi-bank me-2 text-primary"></i>Tambah Data OPD
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= base_url('/opd/store') ?>" method="POST" id="formTambahOpd">
                <?= csrf_field() ?>
                <div class="modal-body opd-modal-body">
                    <!-- ID OPD (Otomatis & Tidak Dapat Diedit) -->
                    <div class="mb-2">
                        <label class="opd-form-label text-muted">ID OPD (UUID)</label>
                        <input 
                            type="text" 
                            class="opd-form-control bg-light font-monospace text-muted" 
                            value="(Dihasilkan otomatis oleh sistem)" 
                            readonly 
                            disabled
                            tabindex="-1"
                            style="cursor: not-allowed;"
                        >
                    </div>

                    <!-- Kode OPD (Otomatis Dihasilkan oleh Sistem) -->
                    <div class="mb-2">
                        <label class="opd-form-label text-muted">Kode OPD</label>
                        <input 
                            type="text" 
                            id="create_display_kode_opd"
                            class="opd-form-control bg-light font-monospace text-muted" 
                            value="<?= esc($nextKodeOpd ?? 'Otomatis oleh sistem') ?>" 
                            readonly 
                            disabled
                            tabindex="-1"
                            style="cursor: not-allowed;"
                        >
                        <input type="hidden" name="kode_opd" id="create_kode_opd" value="<?= esc($nextKodeOpd ?? '') ?>">
                    </div>

                    <div class="mb-2">
                        <label for="create_nama_opd" class="opd-form-label">
                            Nama OPD <span class="text-danger">*</span>
                        </label>
                        <div class="dropdown opd-select-dropdown position-relative" id="dropdownWrapNamaOpd">
                            <button 
                                type="button" 
                                class="opd-form-control opd-form-select text-start d-flex align-items-center justify-content-between" 
                                id="btnDropdownNamaOpd" 
                                data-bs-toggle="dropdown" 
                                data-bs-display="static" 
                                aria-expanded="false"
                            >
                                <span id="labelSelectedNamaOpd" class="text-truncate text-muted">-- Pilih Nama OPD --</span>
                            </button>
                            <input type="hidden" name="nama_opd" id="create_nama_opd" required>

                            <div class="dropdown-menu w-100 p-2 opd-dropdown-menu shadow" aria-labelledby="btnDropdownNamaOpd">
                                <div class="px-1 pb-2 border-bottom mb-2">
                                    <div class="position-relative w-100">
                                        <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 12px; pointer-events: none;"></i>
                                        <input 
                                            type="text" 
                                            id="searchNamaOpd" 
                                            class="form-control form-control-sm" 
                                            placeholder="Cari dinas / badan..."
                                            style="font-size: 12px; height: 32px; border-radius: 6px; padding-left: 28px;"
                                        >
                                    </div>
                                </div>
                                <div class="opd-dropdown-items-list" style="max-height: 200px; overflow-y: auto;">
                                    <div class="opd-dropdown-header">DINAS DAERAH</div>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Komunikasi, Informatika, Persandian dan Statistik">Dinas Komunikasi, Informatika, Persandian dan Statistik</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pendidikan, Pemuda dan Olahraga">Dinas Pendidikan, Pemuda dan Olahraga</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Kesehatan">Dinas Kesehatan</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pekerjaan Umum, Penataan Ruang, Perumahan dan Kawasan Permukiman">Dinas Pekerjaan Umum, Penataan Ruang, Perumahan dan Kawasan Permukiman</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Arsip dan Perpustakaan Daerah">Dinas Arsip dan Perpustakaan Daerah</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Lingkungan Hidup">Dinas Lingkungan Hidup</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Sosial, Pemberdayaan Perempuan dan Perlindungan Anak">Dinas Sosial, Pemberdayaan Perempuan dan Perlindungan Anak</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pertanian, Ketahanan Pangan dan Perikanan">Dinas Pertanian, Ketahanan Pangan dan Perikanan</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Perhubungan">Dinas Perhubungan</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pariwisata">Dinas Pariwisata</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Kebudayaan">Dinas Kebudayaan</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Perdagangan, Perindustrian dan Koperasi, UKM">Dinas Perdagangan, Perindustrian dan Koperasi, UKM</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Tenaga Kerja">Dinas Tenaga Kerja</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Kependudukan dan Pencatatan Sipil">Dinas Kependudukan dan Pencatatan Sipil</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pemadam Kebakaran dan Penyelamatan">Dinas Pemadam Kebakaran dan Penyelamatan</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu">Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu</button>

                                    <div class="opd-dropdown-header mt-2">BADAN DAERAH</div>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Perencanaan Pembangunan Daerah">Badan Perencanaan Pembangunan Daerah</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Pengelolaan Keuangan dan Pendapatan Daerah">Badan Pengelolaan Keuangan dan Pendapatan Daerah</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Kepegawaian dan Pengembangan Sumber Daya Manusia">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Penanggulangan Bencana Daerah">Badan Penanggulangan Bencana Daerah</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Kesatuan Bangsa dan Politik">Badan Kesatuan Bangsa dan Politik</button>

                                    <div class="opd-dropdown-header mt-2">SEKRETARIAT & LEMBAGA</div>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Inspektorat Daerah">Inspektorat Daerah</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Satuan Polisi Pamong Praja">Satuan Polisi Pamong Praja</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Sekretariat Daerah">Sekretariat Daerah</button>
                                    <button type="button" class="dropdown-item opd-dropdown-item" data-value="Sekretariat DPRD">Sekretariat DPRD</button>

                                    <div class="dropdown-divider my-2"></div>
                                    <button type="button" class="dropdown-item opd-dropdown-item text-primary fw-semibold" data-value="__custom__">
                                        <i class="bi bi-pencil-square me-1"></i> + Ketik Nama OPD Manual...
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div id="wrapCustomNamaOpd" class="mt-2 d-none">
                            <input 
                                type="text" 
                                id="custom_nama_opd" 
                                class="opd-form-control" 
                                placeholder="Ketik nama instansi OPD baru..." 
                            >
                        </div>
                    </div>

                    <div class="mb-2">
                        <label for="create_kuota_storage_mb" class="opd-form-label">
                            Kuota Storage (MB) <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="kuota_storage_mb" 
                            id="create_kuota_storage_mb" 
                            class="opd-form-control" 
                            value="1000" 
                            min="100" 
                            step="100" 
                            required
                        >
                    </div>

                    <div class="mb-2">
                        <label for="create_is_active" class="opd-form-label">Status Keaktifan</label>
                        <select name="is_active" id="create_is_active" class="opd-form-control opd-form-select">
                            <option value="1" selected>Aktif</option>
                            <option value="0">Non Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer opd-modal-footer">
                    <button type="button" class="opd-btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="opd-btn-add px-3">
                        <i class="bi bi-check2 me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: EDIT DATA OPD                                            -->
<!-- ============================================================== -->
<div class="modal fade" id="modalEditOpd" tabindex="-1" aria-labelledby="modalEditOpdLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable opd-modal-dialog">
        <div class="modal-content opd-modal">
            <div class="modal-header opd-modal-header">
                <h5 class="modal-title opd-modal-title" id="modalEditOpdLabel">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data OPD
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="" method="POST" id="formEditOpd">
                <?= csrf_field() ?>
                <input type="hidden" name="id_opd" id="edit_id_opd">
                <div class="modal-body opd-modal-body">
                    <!-- ID OPD (Permanen & Tidak Dapat Diedit) -->
                    <div class="mb-2">
                        <label class="opd-form-label text-muted">ID OPD (UUID)</label>
                        <input 
                            type="text" 
                            id="edit_display_id_opd" 
                            class="opd-form-control bg-light font-monospace text-muted" 
                            value="" 
                            readonly 
                            disabled
                            tabindex="-1"
                            style="cursor: not-allowed;"
                        >
                    </div>

                    <div class="mb-2">
                        <label for="edit_kode_opd" class="opd-form-label text-muted">
                            Kode OPD <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="kode_opd" 
                            id="edit_kode_opd" 
                            class="opd-form-control bg-light font-monospace text-muted" 
                            placeholder="Contoh: OPD-023"
                            readonly
                            tabindex="-1"
                            style="cursor: not-allowed;"
                            required
                        >
                    </div>

                    <div class="mb-2">
                        <label for="edit_nama_opd" class="opd-form-label">
                            Nama OPD <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama_opd" 
                            id="edit_nama_opd" 
                            class="opd-form-control" 
                            placeholder="Contoh: Dinas Komunikasi dan Informatika"
                            required
                        >
                    </div>

                    <div class="mb-2">
                        <label for="edit_kuota_storage_mb" class="opd-form-label">
                            Kuota Storage (MB) <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="number" 
                            name="kuota_storage_mb" 
                            id="edit_kuota_storage_mb" 
                            class="opd-form-control" 
                            min="100" 
                            step="100" 
                            required
                        >
                    </div>

                    <div class="mb-2">
                        <label for="edit_is_active" class="opd-form-label">Status Keaktifan</label>
                        <select name="is_active" id="edit_is_active" class="opd-form-control opd-form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Non Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer opd-modal-footer">
                    <button type="button" class="opd-btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="opd-btn-add px-3">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: KONFIRMASI HAPUS OPD                                     -->
<!-- ============================================================== -->
<div class="modal fade" id="modalHapusOpd" tabindex="-1" aria-labelledby="modalHapusOpdLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 420px;">
        <div class="modal-content opd-modal text-center p-2">
            <div class="modal-body opd-modal-body pt-4 pb-2">
                <div class="empty-icon-wrap bg-danger-subtle text-danger mb-3" style="width: 60px; height: 60px; font-size: 26px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="opd-modal-title mb-2">Hapus Data OPD?</h5>
                <p class="text-secondary small mb-3" id="deleteOpdMessage">
                    Apakah Anda yakin ingin menghapus data OPD ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="p-2 mb-3 bg-light rounded-2 text-start border small text-break" id="deleteOpdDetailBox">
                    <strong class="d-block text-dark" id="deleteOpdName">Nama OPD</strong>
                    <span class="text-muted" id="deleteOpdCode">OPD-000</span>
                </div>
            </div>
            <div class="modal-footer opd-modal-footer justify-content-center bg-transparent border-0 pt-0 pb-3 gap-2">
                <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnConfirmDelete" class="btn btn-danger px-4 fw-medium">
                    <i class="bi bi-trash me-1"></i> Hapus OPD
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifikasi Copy ID -->
<div class="opd-toast-container">
    <div id="copyToast" class="opd-toast" role="alert" aria-live="assertive" aria-atomic="true">
        <i class="bi bi-clipboard-check text-success fs-5"></i>
        <span>ID OPD disalin ke clipboard!</span>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sumber Data dari Controller CI4
    const rawData = <?= json_encode($opds ?? []) ?>;
    const baseEditUrl = "<?= base_url('/opd/update') ?>";
    const baseDeleteUrl = "<?= base_url('/opd/delete') ?>";

    // Konfigurasi & State
    let currentPage = 1;
    let filteredData = [...rawData];

    // Elemen DOM
    const searchInput = document.getElementById('opdSearchInput');
    const clearSearchBtn = document.getElementById('opdClearSearchBtn');
    const statusFilter = document.getElementById('opdStatusFilter');
    const perPageSelect = document.getElementById('opdPerPageSelect');
    const tableBody = document.getElementById('opdTableBody');
    const emptyState = document.getElementById('opdEmptyState');
    const paginationInfo = document.getElementById('opdPaginationInfo');
    const paginationControls = document.getElementById('opdPaginationControls');
    const btnResetFilter = document.getElementById('btnResetFilter');

    // Elemen Modal Edit
    const formEditOpd = document.getElementById('formEditOpd');
    const editIdInput = document.getElementById('edit_id_opd');
    const editKodeInput = document.getElementById('edit_kode_opd');
    const editNamaInput = document.getElementById('edit_nama_opd');
    const editKuotaInput = document.getElementById('edit_kuota_storage_mb');
    const editActiveSelect = document.getElementById('edit_is_active');

    // Elemen Modal Hapus
    const deleteOpdName = document.getElementById('deleteOpdName');
    const deleteOpdCode = document.getElementById('deleteOpdCode');
    const btnConfirmDelete = document.getElementById('btnConfirmDelete');

    // Toast
    const copyToast = document.getElementById('copyToast');
    let toastTimeout = null;

    /**
     * Tampilkan Toast saat ID OPD disalin
     */
    function showCopyToast(id) {
        if (!copyToast) return;
        copyToast.querySelector('span').textContent = 'ID (' + id.substring(0, 8) + '...) disalin!';
        copyToast.classList.add('show');
        if (toastTimeout) clearTimeout(toastTimeout);
        toastTimeout = setTimeout(() => {
            copyToast.classList.remove('show');
        }, 2200);
    }

    /**
     * Escape string HTML untuk keamanan
     */
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Filter data berdasarkan teks pencarian dan pilihan status
     */
    function applyFilters() {
        const query = searchInput.value.trim().toLowerCase();
        const statusVal = statusFilter.value;

        // Toggle tombol clear search
        if (query.length > 0) {
            clearSearchBtn.classList.remove('d-none');
        } else {
            clearSearchBtn.classList.add('d-none');
        }

        filteredData = rawData.filter(item => {
            // Pencarian nama, kode, atau id
            const matchQuery = !query || 
                (item.nama_opd && item.nama_opd.toLowerCase().includes(query)) ||
                (item.kode_opd && item.kode_opd.toLowerCase().includes(query)) ||
                (item.id_opd && item.id_opd.toLowerCase().includes(query));

            // Filter status
            let matchStatus = true;
            if (statusVal === '1') {
                matchStatus = parseInt(item.is_active) === 1;
            } else if (statusVal === '0') {
                matchStatus = parseInt(item.is_active) === 0;
            }

            return matchQuery && matchStatus;
        });

        currentPage = 1; // Reset ke halaman pertama saat filter berubah
        renderTable();
    }

    /**
     * Render Baris Tabel dan Navigasi Pagination
     */
    function renderTable() {
        const totalItems = filteredData.length;
        const perPageVal = perPageSelect ? perPageSelect.value : '10';
        const effectivePerPage = perPageVal === 'all' ? Math.max(totalItems, 1) : parseInt(perPageVal);
        const totalPages = Math.ceil(totalItems / effectivePerPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        // Tampilkan / Sembunyikan Empty State
        if (totalItems === 0) {
            tableBody.innerHTML = '';
            emptyState.classList.remove('d-none');
            paginationInfo.textContent = 'Menampilkan 0 data';
            paginationControls.innerHTML = '';
            return;
        } else {
            emptyState.classList.add('d-none');
        }

        // Slice data untuk halaman aktif
        const startIndex = (currentPage - 1) * effectivePerPage;
        const endIndex = Math.min(startIndex + effectivePerPage, totalItems);
        const pageItems = filteredData.slice(startIndex, endIndex);

        // Render Baris Data
        let htmlRows = '';
        pageItems.forEach((row, idx) => {
            const rowNumber = startIndex + idx + 1;
            const isActive = parseInt(row.is_active) === 1;
            const safeId = escapeHtml(row.id_opd);
            const safeKode = escapeHtml(row.kode_opd);
            const safeNama = escapeHtml(row.nama_opd);
            const kuota = escapeHtml(row.kuota_storage_mb || '1000');

            const statusBadge = isActive 
                ? `<span class="badge-status-pill status-aktif"><span class="status-dot dot-aktif"></span>Aktif</span>`
                : `<span class="badge-status-pill status-nonaktif"><span class="status-dot dot-nonaktif"></span>Non Aktif</span>`;

            htmlRows += `
                <tr>
                    <td class="col-no">${rowNumber}</td>
                    <td class="col-id">
                        <span 
                            class="opd-id-link" 
                            data-copy-id="${safeId}" 
                            title="Klik untuk menyalin UUID: ${safeId}"
                            data-bs-toggle="tooltip"
                        >${safeId}</span>
                    </td>
                    <td class="col-kode">${safeKode}</td>
                    <td class="col-nama">${safeNama}</td>
                    <td class="col-kuota">${kuota}</td>
                    <td class="col-status">${statusBadge}</td>
                    <td class="col-aksi">
                        <div class="opd-action-group">
                            <button 
                                type="button" 
                                class="opd-action-btn btn-action-edit btn-edit-opd" 
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditOpd"
                                data-id="${safeId}"
                                data-kode="${safeKode}"
                                data-nama="${safeNama}"
                                data-kuota="${kuota}"
                                data-active="${isActive ? '1' : '0'}"
                                title="Edit Data OPD"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button 
                                type="button" 
                                class="opd-action-btn btn-action-delete btn-delete-opd" 
                                data-bs-toggle="modal"
                                data-bs-target="#modalHapusOpd"
                                data-id="${safeId}"
                                data-kode="${safeKode}"
                                data-nama="${safeNama}"
                                title="Hapus Data OPD"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = htmlRows;

        // Update Info Pagination (misal: "Menampilkan 1 – 10 dari 28 data")
        if (perPageVal === 'all') {
            paginationInfo.textContent = `Menampilkan semua ${totalItems} data`;
        } else {
            const displayStart = totalItems === 0 ? 0 : startIndex + 1;
            const displayEnd = endIndex;
            paginationInfo.textContent = `Menampilkan ${displayStart} – ${displayEnd} dari ${totalItems} data`;
        }

        // Render Tombol Pagination
        renderPaginationButtons(totalPages);

        // Re-inisialisasi Bootstrap Tooltip
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Render Tombol Kontrol Pagination dengan Nomor Halaman
     */
    function renderPaginationButtons(totalPages) {
        if (totalPages <= 1) {
            paginationControls.innerHTML = '';
            return;
        }

        let buttonsHtml = '';

        // Tombol Previous (<)
        const isPrevDisabled = currentPage === 1 ? 'disabled' : '';
        buttonsHtml += `
            <button type="button" class="opd-page-btn ${isPrevDisabled}" data-page="${currentPage - 1}" title="Halaman Sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </button>
        `;

        // Helper algoritma jendela pagination (1, 2, 3, ... N)
        const delta = 1;
        const range = [];
        const rangeWithDots = [];
        let l;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
                range.push(i);
            }
        }

        for (let i of range) {
            if (l) {
                if (i - l === 2) {
                    rangeWithDots.push(l + 1);
                } else if (i - l !== 1) {
                    rangeWithDots.push('...');
                }
            }
            rangeWithDots.push(i);
            l = i;
        }

        rangeWithDots.forEach(page => {
            if (page === '...') {
                buttonsHtml += `<span class="opd-page-ellipsis">...</span>`;
            } else {
                const isActive = page === currentPage ? 'active' : '';
                buttonsHtml += `
                    <button type="button" class="opd-page-btn ${isActive}" data-page="${page}">
                        ${page}
                    </button>
                `;
            }
        });

        // Tombol Next (>)
        const isNextDisabled = currentPage === totalPages ? 'disabled' : '';
        buttonsHtml += `
            <button type="button" class="opd-page-btn ${isNextDisabled}" data-page="${currentPage + 1}" title="Halaman Berikutnya">
                <i class="bi bi-chevron-right"></i>
            </button>
        `;

        paginationControls.innerHTML = buttonsHtml;
    }

    // ==============================================================
    // EVENT LISTENERS
    // ==============================================================

    // 1. Pencarian Real-Time
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }

    // 2. Clear Pencarian
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            applyFilters();
            searchInput.focus();
        });
    }

    // 3. Filter Jumlah Data Per Halaman
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            currentPage = 1;
            renderTable();
        });
    }

    // 4. Filter Status
    if (statusFilter) {
        statusFilter.addEventListener('change', applyFilters);
    }

    // 5. Reset Filter dari Empty State
    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', function() {
            searchInput.value = '';
            statusFilter.value = 'all';
            if (perPageSelect) perPageSelect.value = '10';
            applyFilters();
        });
    }

    // 6. Klik Pagination
    if (paginationControls) {
        paginationControls.addEventListener('click', function(e) {
            const btn = e.target.closest('.opd-page-btn');
            if (!btn || btn.classList.contains('disabled') || btn.classList.contains('active')) return;
            const targetPage = parseInt(btn.getAttribute('data-page'));
            if (!isNaN(targetPage)) {
                currentPage = targetPage;
                renderTable();
                // Scroll halus ke atas tabel pada layar mobile jika perlu
                const tableCard = document.querySelector('.opd-table-card');
                if (window.innerWidth < 768 && tableCard) {
                    tableCard.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    }

    // 6. Klik Salin UUID (ID OPD)
    document.addEventListener('click', function(e) {
        const copyEl = e.target.closest('[data-copy-id]');
        if (copyEl) {
            const idToCopy = copyEl.getAttribute('data-copy-id');
            if (navigator.clipboard) {
                navigator.clipboard.writeText(idToCopy).then(() => {
                    showCopyToast(idToCopy);
                }).catch(() => {
                    prompt('Salin ID:', idToCopy);
                });
            } else {
                prompt('Salin ID:', idToCopy);
            }
        }
    });

    // Helper fungsi mengisi form Edit
    function populateEditForm(btn) {
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        const kode = btn.getAttribute('data-kode');
        const nama = btn.getAttribute('data-nama');
        const kuota = btn.getAttribute('data-kuota');
        const active = btn.getAttribute('data-active');

        if (formEditOpd) formEditOpd.action = baseEditUrl + '/' + id;
        if (editIdInput) editIdInput.value = id;
        const editDisplayId = document.getElementById('edit_display_id_opd');
        if (editDisplayId) editDisplayId.value = id;
        if (editKodeInput) editKodeInput.value = kode;
        if (editNamaInput) editNamaInput.value = nama;
        if (editKuotaInput) editKuotaInput.value = kuota;
        if (editActiveSelect) editActiveSelect.value = active;
    }

    // Helper fungsi mengisi modal Hapus
    function populateDeleteModal(btn) {
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        const kode = btn.getAttribute('data-kode');
        const nama = btn.getAttribute('data-nama');

        if (deleteOpdName) deleteOpdName.textContent = nama;
        if (deleteOpdCode) deleteOpdCode.textContent = kode;
        if (btnConfirmDelete) btnConfirmDelete.href = baseDeleteUrl + '/' + id;
    }

    // 7. Event show.bs.modal untuk Modal Edit
    const modalEditEl = document.getElementById('modalEditOpd');
    if (modalEditEl) {
        modalEditEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button && button.classList.contains('btn-edit-opd')) {
                populateEditForm(button);
            }
        });
    }

    // 8. Event show.bs.modal untuk Modal Hapus
    const modalHapusEl = document.getElementById('modalHapusOpd');
    if (modalHapusEl) {
        modalHapusEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button && button.classList.contains('btn-delete-opd')) {
                populateDeleteModal(button);
            }
        });
    }

    // Delegasi klik sebagai fallback terpercaya
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.btn-edit-opd');
        if (editBtn) {
            populateEditForm(editBtn);
            if (modalEditEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEditEl);
                modal.show();
            }
        }

        const deleteBtn = e.target.closest('.btn-delete-opd');
        if (deleteBtn) {
            populateDeleteModal(deleteBtn);
            if (modalHapusEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalHapusEl);
                modal.show();
            }
        }
    });

    // 9. Logika custom dropdown Nama OPD Tambah Data (pilihan preset vs custom input)
    const btnDropdownNama = document.getElementById('btnDropdownNamaOpd');
    const labelSelectedNama = document.getElementById('labelSelectedNamaOpd');
    const hiddenNamaInput = document.getElementById('create_nama_opd');
    const wrapCustomNama = document.getElementById('wrapCustomNamaOpd');
    const customNamaInput = document.getElementById('custom_nama_opd');
    const formTambahOpd = document.getElementById('formTambahOpd');
    const searchNamaOpd = document.getElementById('searchNamaOpd');

    // Mencegah dropdown tertutup saat mengklik input search
    if (searchNamaOpd) {
        searchNamaOpd.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        searchNamaOpd.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('#dropdownWrapNamaOpd .opd-dropdown-item');
            items.forEach(function(item) {
                if (item.getAttribute('data-value') === '__custom__') return;
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(q) ? 'block' : 'none';
            });
        });
    }

    // Klik item dropdown
    document.addEventListener('click', function(e) {
        const item = e.target.closest('#dropdownWrapNamaOpd .opd-dropdown-item');
        if (item) {
            const val = item.getAttribute('data-value');
            if (val === '__custom__') {
                if (labelSelectedNama) {
                    labelSelectedNama.textContent = '+ Ketik Nama OPD Manual...';
                    labelSelectedNama.classList.remove('text-muted');
                }
                if (hiddenNamaInput) hiddenNamaInput.value = '';
                if (wrapCustomNama) wrapCustomNama.classList.remove('d-none');
                if (customNamaInput) {
                    customNamaInput.focus();
                    customNamaInput.required = true;
                }
            } else {
                if (labelSelectedNama) {
                    labelSelectedNama.textContent = val;
                    labelSelectedNama.classList.remove('text-muted');
                }
                if (hiddenNamaInput) hiddenNamaInput.value = val;
                if (wrapCustomNama) wrapCustomNama.classList.add('d-none');
                if (customNamaInput) {
                    customNamaInput.required = false;
                    customNamaInput.value = '';
                }
            }

            // Tutup dropdown menu secara rapi
            if (btnDropdownNama) {
                const dd = bootstrap.Dropdown.getInstance(btnDropdownNama) || new bootstrap.Dropdown(btnDropdownNama);
                dd.hide();
            }
        }
    });

    if (formTambahOpd) {
        formTambahOpd.addEventListener('submit', function(e) {
            if (wrapCustomNama && !wrapCustomNama.classList.contains('d-none')) {
                const customVal = customNamaInput ? customNamaInput.value.trim() : '';
                if (!customVal) {
                    e.preventDefault();
                    if (customNamaInput) customNamaInput.focus();
                    return;
                }
                if (hiddenNamaInput) hiddenNamaInput.value = customVal;
            } else if (!hiddenNamaInput.value) {
                e.preventDefault();
                if (btnDropdownNama) btnDropdownNama.focus();
                alert('Silakan pilih Nama OPD terlebih dahulu.');
            }
        });
    }

    const modalTambahEl = document.getElementById('modalTambahOpd');
    if (modalTambahEl) {
        modalTambahEl.addEventListener('hidden.bs.modal', function() {
            if (wrapCustomNama) wrapCustomNama.classList.add('d-none');
            if (customNamaInput) customNamaInput.value = '';
            if (hiddenNamaInput) hiddenNamaInput.value = '';
            if (labelSelectedNama) {
                labelSelectedNama.textContent = '-- Pilih Nama OPD --';
                labelSelectedNama.classList.add('text-muted');
            }
            if (searchNamaOpd) {
                searchNamaOpd.value = '';
                const items = document.querySelectorAll('#dropdownWrapNamaOpd .opd-dropdown-item');
                items.forEach(item => item.style.display = 'block');
            }
        });
    }

    // Pindahkan modals ke <body> agar terhindar dari stacking context .app-layout yang fixed
    ['modalTambahOpd', 'modalEditOpd', 'modalHapusOpd'].forEach(function(modalId) {
        const m = document.getElementById(modalId);
        if (m && m.parentElement !== document.body) {
            document.body.appendChild(m);
        }
    });

    // Render awal saat dokumen siap
    renderTable();
});
</script>
<?= $this->endSection() ?>
