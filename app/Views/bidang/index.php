<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Master Data Bidang - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/bidang-master.css?v=' . (file_exists(FCPATH . 'css/bidang-master.css') ? filemtime(FCPATH . 'css/bidang-master.css') : time())) ?>">
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
<div class="bidang-page-header">
    <h1 class="bidang-page-title">Master Data Bidang</h1>
    <p class="bidang-page-subtitle">Kelola data bidang pada masing-masing Organisasi Perangkat Daerah (OPD).</p>
</div>

<!-- Panel Data Bidang -->
<div class="bidang-table-card">
    <!-- Toolbar Tabel -->
    <div class="bidang-table-toolbar">
        <!-- Kolom Pencarian -->
        <div class="bidang-search-wrap">
            <i class="bi bi-search bidang-search-icon"></i>
            <input 
                type="text" 
                id="bidangSearchInput" 
                class="bidang-search-input" 
                placeholder="Cari nama OPD, Nama Bidang, ID..."
                autocomplete="off"
            >
            <button type="button" class="bidang-clear-search d-none" id="bidangClearSearchBtn" title="Bersihkan pencarian">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <!-- Tombol Tambah Bidang -->
        <div class="bidang-toolbar-actions">
            <button 
                type="button" 
                class="bidang-btn-add" 
                data-bs-toggle="modal" 
                data-bs-target="#modalTambahBidang"
            >
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Bidang</span>
            </button>
        </div>
    </div>

    <!-- Tabel Data Bidang -->
    <div class="table-responsive bidang-table-responsive">
        <table class="table bidang-table align-middle" id="bidangTable">
            <thead>
                <tr>
                    <th scope="col" class="col-no">NO</th>
                    <th scope="col" class="col-id sortable-th" data-sort-key="id_bidang" title="Klik untuk mengurutkan berdasarkan ID">
                        ID <i class="bi bi-arrow-down-up bidang-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-nama-opd sortable-th" data-sort-key="nama_opd" title="Klik untuk mengurutkan berdasarkan Nama OPD">
                        NAMA OPD <i class="bi bi-arrow-down-up bidang-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-nama-bidang sortable-th" data-sort-key="nama_bidang" title="Klik untuk mengurutkan berdasarkan Nama Bidang">
                        NAMA BIDANG <i class="bi bi-arrow-down-up bidang-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-aksi">AKSI</th>
                </tr>
            </thead>
            <tbody id="bidangTableBody">
                <!-- Baris tabel dirender secara dinamis oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Empty State (Ketika data tidak ditemukan) -->
    <div class="bidang-empty-state d-none" id="bidangEmptyState">
        <div class="empty-icon-wrap">
            <i class="bi bi-search"></i>
        </div>
        <h5 class="empty-title">Data Bidang Tidak Ditemukan</h5>
        <p class="empty-desc">Tidak ada hasil yang cocok dengan kata kunci pencarian Anda.</p>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnResetSearch">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Pencarian
        </button>
    </div>

    <!-- Informasi Data dan Pagination -->
    <div class="bidang-table-footer">
        <div class="bidang-pagination-info" id="bidangPaginationInfo">
            Menampilkan 0 dari 0 data
        </div>
        <div class="bidang-pagination-controls" id="bidangPaginationControls">
            <!-- Tombol navigasi pagination -->
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: TAMBAH DATA BIDANG                                       -->
<!-- ============================================================== -->
<div class="modal fade" id="modalTambahBidang" tabindex="-1" aria-labelledby="modalTambahBidangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable bidang-modal-dialog">
        <div class="modal-content bidang-modal">
            <div class="modal-header bidang-modal-header">
                <h5 class="modal-title bidang-modal-title" id="modalTambahBidangLabel">
                    <i class="bi bi-layers me-2 text-primary"></i>Tambah Data Bidang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= base_url('/bidang/store') ?>" method="POST" id="formTambahBidang">
                <?= csrf_field() ?>
                <div class="modal-body bidang-modal-body">
                    <!-- ID Bidang (Otomatis & Readonly) -->
                    <div class="mb-3">
                        <label class="bidang-form-label text-muted">ID Bidang (UUID)</label>
                        <input 
                            type="text" 
                            class="bidang-form-control bg-light font-monospace text-muted" 
                            value="(Dihasilkan otomatis oleh sistem)" 
                            readonly 
                            disabled
                            tabindex="-1"
                            style="cursor: not-allowed;"
                        >
                    </div>

                    <!-- Pilihan OPD -->
                    <div class="mb-3">
                        <label for="create_id_opd" class="bidang-form-label">
                            Organisasi Perangkat Daerah (OPD) <span class="text-danger">*</span>
                        </label>
                        <?php if (!empty($id_opd_locked)) : ?>
                            <?php 
                                $lockedName = 'OPD Anda';
                                foreach ($opds as $opd) {
                                    if ($opd['id_opd'] == $id_opd_locked) {
                                        $lockedName = $opd['nama_opd'];
                                        break;
                                    }
                                }
                            ?>
                            <input 
                                type="text" 
                                class="bidang-form-control bg-light text-muted" 
                                value="<?= esc($lockedName) ?>" 
                                readonly 
                                disabled
                                style="cursor: not-allowed;"
                            >
                            <input type="hidden" name="id_opd" value="<?= esc($id_opd_locked) ?>">
                        <?php else : ?>
                            <!-- Custom Searchable Dropdown OPD -->
                            <div class="dropdown bidang-select-dropdown position-relative" id="dropdownWrapTambahOpd">
                                <button 
                                    type="button" 
                                    class="bidang-form-control bidang-form-select text-start d-flex align-items-center justify-content-between" 
                                    id="btnDropdownTambahOpd" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-display="static" 
                                    aria-expanded="false"
                                >
                                    <span id="labelSelectedTambahOpd" class="text-truncate text-muted">-- Pilih Organisasi Perangkat Daerah --</span>
                                </button>
                                <input type="hidden" name="id_opd" id="create_id_opd" required>

                                <div class="dropdown-menu w-100 p-2 shadow" aria-labelledby="btnDropdownTambahOpd">
                                    <div class="px-1 pb-2 border-bottom mb-2">
                                        <div class="position-relative w-100">
                                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 12px; pointer-events: none;"></i>
                                            <input 
                                                type="text" 
                                                id="searchTambahOpd" 
                                                class="form-control form-control-sm" 
                                                placeholder="Cari dinas / badan..."
                                                style="font-size: 12.5px; height: 34px; border-radius: 6px; padding-left: 28px;"
                                                autocomplete="off"
                                            >
                                        </div>
                                    </div>
                                    <div class="bidang-dropdown-items-list" id="listTambahOpd" style="max-height: 220px; overflow-y: auto;">
                                        <?php if (!empty($opds)) : ?>
                                            <?php foreach ($opds as $opd) : ?>
                                                <button 
                                                    type="button" 
                                                    class="dropdown-item bidang-dropdown-item" 
                                                    data-id="<?= esc($opd['id_opd']) ?>" 
                                                    data-name="<?= esc($opd['nama_opd']) ?>"
                                                >
                                                    <?= esc($opd['nama_opd']) ?>
                                                </button>
                                            <?php endforeach; ?>
                                        <?php else : ?>
                                            <div class="p-2 text-muted small text-center">Belum ada data OPD.</div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Nama Bidang -->
                    <div class="mb-2">
                        <label for="create_nama_bidang" class="bidang-form-label">
                            Nama Bidang <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama_bidang" 
                            id="create_nama_bidang" 
                            class="bidang-form-control" 
                            placeholder="Contoh: Bidang Tata Kelola"
                            required
                            autocomplete="off"
                        >
                    </div>
                </div>
                <div class="modal-footer bidang-modal-footer">
                    <button type="button" class="bidang-btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="bidang-btn-add px-3">
                        <i class="bi bi-check2 me-1"></i> Simpan Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: EDIT DATA BIDANG                                         -->
<!-- ============================================================== -->
<div class="modal fade" id="modalEditBidang" tabindex="-1" aria-labelledby="modalEditBidangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable bidang-modal-dialog">
        <div class="modal-content bidang-modal">
            <div class="modal-header bidang-modal-header">
                <h5 class="modal-title bidang-modal-title" id="modalEditBidangLabel">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Data Bidang
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="" method="POST" id="formEditBidang">
                <?= csrf_field() ?>
                <input type="hidden" name="id_bidang" id="edit_id_bidang">
                <div class="modal-body bidang-modal-body">
                    <!-- ID Bidang (Permanen & Readonly) -->
                    <div class="mb-3">
                        <label class="bidang-form-label text-muted">ID Bidang (UUID)</label>
                        <input 
                            type="text" 
                            id="edit_display_id_bidang" 
                            class="bidang-form-control bg-light font-monospace text-muted" 
                            value="" 
                            readonly 
                            disabled
                            tabindex="-1"
                            style="cursor: not-allowed;"
                        >
                    </div>

                    <!-- Pilihan OPD -->
                    <div class="mb-3">
                        <label for="edit_id_opd" class="bidang-form-label">
                            Organisasi Perangkat Daerah (OPD) <span class="text-danger">*</span>
                        </label>
                        <?php if (!empty($id_opd_locked)) : ?>
                            <input 
                                type="text" 
                                id="edit_locked_opd_name"
                                class="bidang-form-control bg-light text-muted" 
                                value="" 
                                readonly 
                                disabled
                                style="cursor: not-allowed;"
                            >
                            <input type="hidden" name="id_opd" id="edit_id_opd" value="<?= esc($id_opd_locked) ?>">
                        <?php else : ?>
                            <div class="dropdown bidang-select-dropdown position-relative" id="dropdownWrapEditOpd">
                                <button 
                                    type="button" 
                                    class="bidang-form-control bidang-form-select text-start d-flex align-items-center justify-content-between" 
                                    id="btnDropdownEditOpd" 
                                    data-bs-toggle="dropdown" 
                                    data-bs-display="static" 
                                    aria-expanded="false"
                                >
                                    <span id="labelSelectedEditOpd" class="text-truncate">-- Pilih Organisasi Perangkat Daerah --</span>
                                </button>
                                <input type="hidden" name="id_opd" id="edit_id_opd" required>

                                <div class="dropdown-menu w-100 p-2 shadow" aria-labelledby="btnDropdownEditOpd">
                                    <div class="px-1 pb-2 border-bottom mb-2">
                                        <div class="position-relative w-100">
                                            <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 12px; pointer-events: none;"></i>
                                            <input 
                                                type="text" 
                                                id="searchEditOpd" 
                                                class="form-control form-control-sm" 
                                                placeholder="Cari dinas / badan..."
                                                style="font-size: 12.5px; height: 34px; border-radius: 6px; padding-left: 28px;"
                                                autocomplete="off"
                                            >
                                        </div>
                                    </div>
                                    <div class="bidang-dropdown-items-list" id="listEditOpd" style="max-height: 220px; overflow-y: auto;">
                                        <?php if (!empty($opds)) : ?>
                                            <?php foreach ($opds as $opd) : ?>
                                                <button 
                                                    type="button" 
                                                    class="dropdown-item bidang-dropdown-item" 
                                                    data-id="<?= esc($opd['id_opd']) ?>" 
                                                    data-name="<?= esc($opd['nama_opd']) ?>"
                                                >
                                                    <?= esc($opd['nama_opd']) ?>
                                                </button>
                                            <?php endforeach; ?>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Nama Bidang -->
                    <div class="mb-2">
                        <label for="edit_nama_bidang" class="bidang-form-label">
                            Nama Bidang <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama_bidang" 
                            id="edit_nama_bidang" 
                            class="bidang-form-control" 
                            placeholder="Contoh: Bidang Tata Kelola"
                            required
                            autocomplete="off"
                        >
                    </div>
                </div>
                <div class="modal-footer bidang-modal-footer">
                    <button type="button" class="bidang-btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="bidang-btn-add px-3">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: KONFIRMASI HAPUS BIDANG                                  -->
<!-- ============================================================== -->
<div class="modal fade" id="modalHapusBidang" tabindex="-1" aria-labelledby="modalHapusBidangLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 430px;">
        <div class="modal-content bidang-modal text-center p-2">
            <div class="modal-body bidang-modal-body pt-4 pb-2">
                <div class="empty-icon-wrap bg-danger-subtle text-danger mb-3" style="width: 60px; height: 60px; font-size: 26px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="bidang-modal-title justify-content-center mb-2">Hapus Data Bidang?</h5>
                <p class="text-secondary small mb-3">
                    Apakah Anda yakin ingin menghapus data bidang ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="p-3 mb-3 bg-light rounded-3 text-start border small text-break">
                    <div class="text-muted small mb-1">Nama Bidang:</div>
                    <strong class="d-block text-dark fs-6 mb-2" id="deleteBidangName">Nama Bidang</strong>
                    <div class="text-muted small mb-1">Organisasi Perangkat Daerah:</div>
                    <span class="text-secondary fw-medium" id="deleteOpdName">Nama OPD</span>
                </div>
            </div>
            <div class="modal-footer bidang-modal-footer justify-content-center bg-transparent border-0 pt-0 pb-3 gap-2">
                <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnConfirmDeleteBidang" class="btn btn-danger px-4 fw-medium">
                    <i class="bi bi-trash me-1"></i> Hapus Bidang
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Toast Notifikasi Copy ID -->
<div class="bidang-toast-container">
    <div id="copyToast" class="bidang-toast" role="alert" aria-live="assertive" aria-atomic="true">
        <i class="bi bi-clipboard-check text-success fs-5"></i>
        <span>ID Bidang disalin ke clipboard!</span>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari Controller CI4
    const rawData = <?= json_encode($bidangs ?? []) ?>;
    const baseEditUrl = "<?= base_url('/bidang/update') ?>";
    const baseDeleteUrl = "<?= base_url('/bidang/delete') ?>";

    // Konfigurasi & State
    const perPage = 10;
    let currentPage = 1;
    let currentSort = {
        key: null,
        direction: 'asc' // 'asc' atau 'desc'
    };
    let filteredData = [...rawData];

    // Elemen DOM Tabel
    const searchInput = document.getElementById('bidangSearchInput');
    const clearSearchBtn = document.getElementById('bidangClearSearchBtn');
    const tableBody = document.getElementById('bidangTableBody');
    const emptyState = document.getElementById('bidangEmptyState');
    const paginationInfo = document.getElementById('bidangPaginationInfo');
    const paginationControls = document.getElementById('bidangPaginationControls');
    const btnResetSearch = document.getElementById('btnResetSearch');

    // Elemen Modal Edit
    const formEditBidang = document.getElementById('formEditBidang');
    const editIdInput = document.getElementById('edit_id_bidang');
    const editDisplayId = document.getElementById('edit_display_id_bidang');
    const editNamaInput = document.getElementById('edit_nama_bidang');
    const editOpdInput = document.getElementById('edit_id_opd');
    const labelSelectedEditOpd = document.getElementById('labelSelectedEditOpd');
    const editLockedOpdName = document.getElementById('edit_locked_opd_name');

    // Elemen Modal Hapus
    const deleteBidangName = document.getElementById('deleteBidangName');
    const deleteOpdName = document.getElementById('deleteOpdName');
    const btnConfirmDeleteBidang = document.getElementById('btnConfirmDeleteBidang');

    // Toast
    const copyToast = document.getElementById('copyToast');
    let toastTimeout = null;

    /**
     * Tampilkan Toast saat ID Bidang disalin
     */
    function showCopyToast(id) {
        if (!copyToast) return;
        copyToast.querySelector('span').textContent = 'ID (' + id.substring(0, 8) + '...) disalin ke clipboard!';
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
     * Filter data berdasarkan teks pencarian (ID, Nama OPD, Nama Bidang)
     */
    function applyFilters() {
        const query = searchInput.value.trim().toLowerCase();

        // Tampilkan/sembunyikan tombol clear search
        if (query.length > 0) {
            clearSearchBtn.classList.remove('d-none');
        } else {
            clearSearchBtn.classList.add('d-none');
        }

        filteredData = rawData.filter(item => {
            if (!query) return true;
            const matchId = item.id_bidang && item.id_bidang.toLowerCase().includes(query);
            const matchOpd = item.nama_opd && item.nama_opd.toLowerCase().includes(query);
            const matchBidang = item.nama_bidang && item.nama_bidang.toLowerCase().includes(query);
            return matchId || matchOpd || matchBidang;
        });

        // Terapkan sorting jika ada kolom yang diurutkan
        if (currentSort.key) {
            applySort();
        }

        currentPage = 1;
        renderTable();
    }

    /**
     * Urutkan data berdasarkan currentSort
     */
    function applySort() {
        if (!currentSort.key) return;

        filteredData.sort((a, b) => {
            const valA = (a[currentSort.key] || '').toString().toLowerCase();
            const valB = (b[currentSort.key] || '').toString().toLowerCase();

            if (valA < valB) return currentSort.direction === 'asc' ? -1 : 1;
            if (valA > valB) return currentSort.direction === 'asc' ? 1 : -1;
            return 0;
        });
    }

    /**
     * Update ikon pengurutan pada header tabel
     */
    function updateSortIcons() {
        const headers = document.querySelectorAll('.sortable-th');
        headers.forEach(th => {
            const key = th.getAttribute('data-sort-key');
            const icon = th.querySelector('.bidang-sort-icon');

            th.classList.remove('sorted-asc', 'sorted-desc');
            if (key === currentSort.key) {
                if (currentSort.direction === 'asc') {
                    th.classList.add('sorted-asc');
                    icon.className = 'bi bi-sort-up bidang-sort-icon';
                } else {
                    th.classList.add('sorted-desc');
                    icon.className = 'bi bi-sort-down bidang-sort-icon';
                }
            } else {
                icon.className = 'bi bi-arrow-down-up bidang-sort-icon';
            }
        });
    }

    /**
     * Render Baris Tabel dan Navigasi Pagination
     */
    function renderTable() {
        const totalItems = filteredData.length;
        const totalPages = Math.ceil(totalItems / perPage) || 1;

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
        const startIndex = (currentPage - 1) * perPage;
        const endIndex = Math.min(startIndex + perPage, totalItems);
        const pageItems = filteredData.slice(startIndex, endIndex);

        // Render Baris Data
        let htmlRows = '';
        pageItems.forEach((row, idx) => {
            const rowNumber = startIndex + idx + 1;
            const safeId = escapeHtml(row.id_bidang);
            const safeOpd = escapeHtml(row.nama_opd || '-');
            const safeBidang = escapeHtml(row.nama_bidang || '-');
            const opdId = escapeHtml(row.id_opd || '');

            htmlRows += `
                <tr>
                    <td class="col-no">${rowNumber}</td>
                    <td class="col-id">
                        <span 
                            class="bidang-id-link" 
                            data-copy-id="${safeId}" 
                            title="Klik untuk menyalin UUID: ${safeId}"
                            data-bs-toggle="tooltip"
                        >${safeId}</span>
                    </td>
                    <td class="col-nama-opd">${safeOpd}</td>
                    <td class="col-nama-bidang">${safeBidang}</td>
                    <td class="col-aksi">
                        <div class="bidang-action-group">
                            <button 
                                type="button" 
                                class="bidang-action-btn btn-action-edit btn-edit-bidang" 
                                data-bs-toggle="modal"
                                data-bs-target="#modalEditBidang"
                                data-id="${safeId}"
                                data-opd-id="${opdId}"
                                data-opd-name="${safeOpd}"
                                data-bidang="${safeBidang}"
                                title="Edit Bidang"
                            >
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button 
                                type="button" 
                                class="bidang-action-btn btn-action-delete btn-delete-bidang" 
                                data-bs-toggle="modal"
                                data-bs-target="#modalHapusBidang"
                                data-id="${safeId}"
                                data-opd-name="${safeOpd}"
                                data-bidang="${safeBidang}"
                                title="Hapus Bidang"
                            >
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            `;
        });

        tableBody.innerHTML = htmlRows;

        // Update Info Pagination (misal: "Menampilkan 1 – 2 dari 2 data")
        const displayStart = totalItems === 0 ? 0 : startIndex + 1;
        const displayEnd = endIndex;
        paginationInfo.textContent = `Menampilkan ${displayStart} – ${displayEnd} dari ${totalItems} data`;

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
            // Jika hanya 1 halaman, tampilkan nomor halaman 1 aktif
            paginationControls.innerHTML = `
                <button type="button" class="bidang-page-btn disabled" disabled title="Halaman Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="bidang-page-btn active" data-page="1">1</button>
                <button type="button" class="bidang-page-btn disabled" disabled title="Halaman Berikutnya">
                    <i class="bi bi-chevron-right"></i>
                </button>
            `;
            return;
        }

        let buttonsHtml = '';

        // Tombol Previous (<)
        const isPrevDisabled = currentPage === 1 ? 'disabled' : '';
        buttonsHtml += `
            <button type="button" class="bidang-page-btn ${isPrevDisabled}" data-page="${currentPage - 1}" title="Halaman Sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </button>
        `;

        // Algoritma Jendela Pagination
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
                buttonsHtml += `<span class="bidang-page-ellipsis">...</span>`;
            } else {
                const isActive = page === currentPage ? 'active' : '';
                buttonsHtml += `
                    <button type="button" class="bidang-page-btn ${isActive}" data-page="${page}">
                        ${page}
                    </button>
                `;
            }
        });

        // Tombol Next (>)
        const isNextDisabled = currentPage === totalPages ? 'disabled' : '';
        buttonsHtml += `
            <button type="button" class="bidang-page-btn ${isNextDisabled}" data-page="${currentPage + 1}" title="Halaman Berikutnya">
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

    // 3. Reset Pencarian dari Empty State
    if (btnResetSearch) {
        btnResetSearch.addEventListener('click', function() {
            searchInput.value = '';
            applyFilters();
            searchInput.focus();
        });
    }

    // 4. Pengurutan Kolom (Sorting)
    document.querySelectorAll('.sortable-th').forEach(th => {
        th.addEventListener('click', function() {
            const key = this.getAttribute('data-sort-key');
            if (currentSort.key === key) {
                // Toggle arah
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.key = key;
                currentSort.direction = 'asc';
            }

            applySort();
            updateSortIcons();
            renderTable();
        });
    });

    // 5. Navigasi Pagination
    if (paginationControls) {
        paginationControls.addEventListener('click', function(e) {
            const btn = e.target.closest('.bidang-page-btn');
            if (!btn || btn.classList.contains('disabled') || btn.classList.contains('active')) return;
            const targetPage = parseInt(btn.getAttribute('data-page'));
            if (!isNaN(targetPage)) {
                currentPage = targetPage;
                renderTable();
                const tableCard = document.querySelector('.bidang-table-card');
                if (window.innerWidth < 768 && tableCard) {
                    tableCard.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    }

    // 6. Klik Salin UUID (ID Bidang)
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

    // Helper: Isi Modal Edit
    function populateEditModal(btn) {
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        const opdId = btn.getAttribute('data-opd-id');
        const opdName = btn.getAttribute('data-opd-name');
        const bidang = btn.getAttribute('data-bidang');

        if (formEditBidang) formEditBidang.action = baseEditUrl + '/' + id;
        if (editIdInput) editIdInput.value = id;
        if (editDisplayId) editDisplayId.value = id;
        if (editNamaInput) editNamaInput.value = bidang;
        if (editOpdInput) editOpdInput.value = opdId;

        if (labelSelectedEditOpd) {
            labelSelectedEditOpd.textContent = opdName || '-- Pilih Organisasi Perangkat Daerah --';
            labelSelectedEditOpd.classList.remove('text-muted');
        }

        if (editLockedOpdName) {
            editLockedOpdName.value = opdName;
        }
    }

    // Helper: Isi Modal Hapus
    function populateDeleteModal(btn) {
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        const opdName = btn.getAttribute('data-opd-name');
        const bidang = btn.getAttribute('data-bidang');

        if (deleteBidangName) deleteBidangName.textContent = bidang;
        if (deleteOpdName) deleteOpdName.textContent = opdName;
        if (btnConfirmDeleteBidang) btnConfirmDeleteBidang.href = baseDeleteUrl + '/' + id;
    }

    // 7. Event Modal Edit (show.bs.modal & delegation)
    const modalEditEl = document.getElementById('modalEditBidang');
    if (modalEditEl) {
        modalEditEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button && button.classList.contains('btn-edit-bidang')) {
                populateEditModal(button);
            }
        });
    }

    // 8. Event Modal Hapus (show.bs.modal & delegation)
    const modalHapusEl = document.getElementById('modalHapusBidang');
    if (modalHapusEl) {
        modalHapusEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button && button.classList.contains('btn-delete-bidang')) {
                populateDeleteModal(button);
            }
        });
    }

    // Delegasi klik fallback
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.btn-edit-bidang');
        if (editBtn) {
            populateEditModal(editBtn);
            if (modalEditEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEditEl);
                modal.show();
            }
        }

        const deleteBtn = e.target.closest('.btn-delete-bidang');
        if (deleteBtn) {
            populateDeleteModal(deleteBtn);
            if (modalHapusEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalHapusEl);
                modal.show();
            }
        }
    });

    // 9. Dropdown OPD di Modal Tambah Data
    const btnDropdownTambahOpd = document.getElementById('btnDropdownTambahOpd');
    const labelSelectedTambahOpd = document.getElementById('labelSelectedTambahOpd');
    const hiddenCreateOpdInput = document.getElementById('create_id_opd');
    const searchTambahOpd = document.getElementById('searchTambahOpd');

    if (searchTambahOpd) {
        searchTambahOpd.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        searchTambahOpd.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('#listTambahOpd .bidang-dropdown-item');
            items.forEach(function(item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(q) ? 'block' : 'none';
            });
        });
    }

    document.addEventListener('click', function(e) {
        const item = e.target.closest('#listTambahOpd .bidang-dropdown-item');
        if (item) {
            const idVal = item.getAttribute('data-id');
            const nameVal = item.getAttribute('data-name');

            if (labelSelectedTambahOpd) {
                labelSelectedTambahOpd.textContent = nameVal;
                labelSelectedTambahOpd.classList.remove('text-muted');
            }
            if (hiddenCreateOpdInput) {
                hiddenCreateOpdInput.value = idVal;
            }

            if (btnDropdownTambahOpd) {
                const dd = bootstrap.Dropdown.getInstance(btnDropdownTambahOpd) || new bootstrap.Dropdown(btnDropdownTambahOpd);
                dd.hide();
            }
        }
    });

    const modalTambahEl = document.getElementById('modalTambahBidang');
    if (modalTambahEl) {
        modalTambahEl.addEventListener('hidden.bs.modal', function() {
            const form = document.getElementById('formTambahBidang');
            if (form) form.reset();
            if (hiddenCreateOpdInput) hiddenCreateOpdInput.value = '';
            if (labelSelectedTambahOpd) {
                labelSelectedTambahOpd.textContent = '-- Pilih Organisasi Perangkat Daerah --';
                labelSelectedTambahOpd.classList.add('text-muted');
            }
            if (searchTambahOpd) {
                searchTambahOpd.value = '';
                const items = document.querySelectorAll('#listTambahOpd .bidang-dropdown-item');
                items.forEach(item => item.style.display = 'block');
            }
        });
    }

    // 10. Dropdown OPD di Modal Edit Data
    const btnDropdownEditOpd = document.getElementById('btnDropdownEditOpd');
    const searchEditOpd = document.getElementById('searchEditOpd');

    if (searchEditOpd) {
        searchEditOpd.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        searchEditOpd.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('#listEditOpd .bidang-dropdown-item');
            items.forEach(function(item) {
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(q) ? 'block' : 'none';
            });
        });
    }

    document.addEventListener('click', function(e) {
        const item = e.target.closest('#listEditOpd .bidang-dropdown-item');
        if (item) {
            const idVal = item.getAttribute('data-id');
            const nameVal = item.getAttribute('data-name');

            if (labelSelectedEditOpd) {
                labelSelectedEditOpd.textContent = nameVal;
                labelSelectedEditOpd.classList.remove('text-muted');
            }
            if (editOpdInput) {
                editOpdInput.value = idVal;
            }

            if (btnDropdownEditOpd) {
                const dd = bootstrap.Dropdown.getInstance(btnDropdownEditOpd) || new bootstrap.Dropdown(btnDropdownEditOpd);
                dd.hide();
            }
        }
    });

    // Pindahkan modals ke <body> agar terhindar dari stacking context .app-layout yang fixed
    ['modalTambahBidang', 'modalEditBidang', 'modalHapusBidang'].forEach(function(modalId) {
        const m = document.getElementById(modalId);
        if (m && m.parentElement !== document.body) {
            document.body.appendChild(m);
        }
    });

    // Inisialisasi awal render tabel
    renderTable();
});
</script>
<?= $this->endSection() ?>
