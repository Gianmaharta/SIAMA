<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Penilaian Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/penilaian-master.css?v=' . (file_exists(FCPATH . 'css/penilaian-master.css') ? filemtime(FCPATH . 'css/penilaian-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Flash Messages (Notifikasi Sukses / Gagal) -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>
            <?= session()->getFlashdata('success') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div>
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Header Halaman -->
<div class="penilaian-page-header">
    <h1 class="penilaian-page-title">Penilaian Arsip</h1>
    <p class="penilaian-page-subtitle">Lakukan penilaian terhadap arsip yang telah dialihmediakan sesuai ketentuan dan jadwal retensi arsip (JRA).</p>
</div>

<!-- Kotak Informasi / Empat Kartu Statistik Penilaian Arsip -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Kartu 1: Total Arsip -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="penilaian-stat-card">
            <div class="penilaian-stat-icon-wrapper bg-icon-blue">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="penilaian-stat-body">
                <span class="penilaian-stat-title">Total Arsip</span>
                <div class="penilaian-stat-number-wrap">
                    <span class="penilaian-stat-number" id="statTotalArsip"><?= number_format($totalArsip ?? 0, 0, ',', '.') ?></span>
                    <span class="penilaian-stat-unit">arsip</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 2: Belum Dinilai -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="penilaian-stat-card">
            <div class="penilaian-stat-icon-wrapper bg-icon-red">
                <i class="bi bi-clock-fill"></i>
            </div>
            <div class="penilaian-stat-body">
                <span class="penilaian-stat-title">Belum Dinilai</span>
                <div class="penilaian-stat-number-wrap">
                    <span class="penilaian-stat-number" id="statBelumDinilai"><?= number_format($belumDinilai ?? 0, 0, ',', '.') ?></span>
                    <span class="penilaian-stat-unit">arsip</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 3: Sudah Dinilai -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="penilaian-stat-card">
            <div class="penilaian-stat-icon-wrapper bg-icon-green">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="penilaian-stat-body">
                <span class="penilaian-stat-title">Sudah Dinilai</span>
                <div class="penilaian-stat-number-wrap">
                    <span class="penilaian-stat-number" id="statSudahDinilai"><?= number_format($sudahDinilai ?? 0, 0, ',', '.') ?></span>
                    <span class="penilaian-stat-unit">arsip</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 4: Revisi Metadata -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="penilaian-stat-card">
            <div class="penilaian-stat-icon-wrapper bg-icon-orange">
                <i class="bi bi-arrow-repeat"></i>
            </div>
            <div class="penilaian-stat-body">
                <span class="penilaian-stat-title">Revisi Metadata</span>
                <div class="penilaian-stat-number-wrap">
                    <span class="penilaian-stat-number" id="statRevisiMetadata"><?= number_format($revisiMetadata ?? 0, 0, ',', '.') ?></span>
                    <span class="penilaian-stat-unit">arsip</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel Utama Tabel Penilaian Arsip -->
<div class="penilaian-table-card">
    <!-- Toolbar Tabel: Pencarian, Filter & Reset -->
    <div class="penilaian-table-toolbar">
        <!-- Kolom Pencarian Arsip -->
        <div class="penilaian-search-wrap">
            <i class="bi bi-search penilaian-search-icon"></i>
            <input 
                type="text" 
                id="penilaianSearchInput" 
                class="penilaian-search-input" 
                placeholder="Cari nomor arsip, nama arsip, atau OPD..."
                autocomplete="off"
            >
            <button type="button" class="penilaian-clear-search d-none" id="penilaianClearSearchBtn" title="Bersihkan pencarian">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <!-- Toolbar Actions: Tampilkan, Filter Status & Tombol Reset -->
        <div class="penilaian-toolbar-actions">
            <!-- Pilihan Jumlah Data Per Halaman -->
            <div class="penilaian-filter-wrap">
                <label for="penilaianPerPageSelect" class="penilaian-filter-label">Tampilkan</label>
                <select id="penilaianPerPageSelect" class="penilaian-filter-select penilaian-select-per-page" aria-label="Jumlah Data Per Halaman">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">Semua</option>
                </select>
            </div>

            <!-- Filter Status Autentikasi Penilaian -->
            <div class="penilaian-filter-wrap">
                <label for="penilaianStatusFilter" class="penilaian-filter-label">Status Autentikasi Penilaian</label>
                <select id="penilaianStatusFilter" class="penilaian-filter-select" aria-label="Filter Status Autentikasi">
                    <option value="all" selected>Semua Status (<?= number_format($totalArsip ?? 0, 0, ',', '.') ?>)</option>
                    <option value="Belum Dinilai">Belum Dinilai (<?= number_format($belumDinilai ?? 0, 0, ',', '.') ?>)</option>
                    <option value="Lolos Evaluasi">Lolos Evaluasi (<?= number_format($sudahDinilai ?? 0, 0, ',', '.') ?>)</option>
                    <option value="Revisi Metadata">Revisi Metadata (<?= number_format($revisiMetadata ?? 0, 0, ',', '.') ?>)</option>
                    <option value="Ditolak">Ditolak (<?= number_format($ditolak ?? 0, 0, ',', '.') ?>)</option>
                </select>
            </div>

            <!-- Tombol Reset Filter & Pencarian -->
            <button type="button" class="penilaian-btn-reset-toolbar" id="btnResetFilterToolbar" title="Kembalikan pencarian dan filter ke kondisi awal">
                <i class="bi bi-arrow-counterclockwise"></i> Reset
            </button>
        </div>
    </div>

    <!-- Tabel Penilaian Arsip (7 Kolom) -->
    <div class="table-responsive penilaian-table-responsive">
        <table class="table penilaian-table align-middle" id="penilaianTable">
            <thead>
                <tr>
                    <th scope="col" class="col-no">NO</th>
                    <th scope="col" class="col-nomor-arsip sortable-th" data-sort-key="nomor_arsip" title="Klik untuk mengurutkan berdasarkan Nomor Arsip">
                        NOMOR ARSIP <i class="bi bi-arrow-down-up penilaian-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-nama-arsip sortable-th" data-sort-key="nama_arsip" title="Klik untuk mengurutkan berdasarkan Nama Arsip">
                        NAMA ARSIP <i class="bi bi-arrow-down-up penilaian-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-opd sortable-th" data-sort-key="nama_opd" title="Klik untuk mengurutkan berdasarkan OPD">
                        OPD <i class="bi bi-arrow-down-up penilaian-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-skor sortable-th text-center" data-sort-key="skor_prioritas" title="Klik untuk mengurutkan berdasarkan Skor Prioritas">
                        SKOR PRIORITAS <i class="bi bi-arrow-down-up penilaian-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-status sortable-th" data-sort-key="status_autentikasi" title="Klik untuk mengurutkan berdasarkan Status Autentikasi">
                        STATUS AUTENTIKASI <i class="bi bi-arrow-down-up penilaian-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-aksi">
                        AKSI
                    </th>
                </tr>
            </thead>
            <tbody id="penilaianTableBody">
                <!-- Baris tabel dirender secara dinamis oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Empty State (Ketika data tidak ditemukan) -->
    <div class="penilaian-empty-state d-none" id="penilaianEmptyState">
        <div class="penilaian-empty-icon-wrap">
            <i class="bi bi-search"></i>
        </div>
        <h5 class="penilaian-empty-title">Data Arsip Tidak Ditemukan</h5>
        <p class="penilaian-empty-desc">Tidak ada data arsip yang cocok dengan kata kunci pencarian atau filter status yang Anda pilih.</p>
        <button type="button" class="penilaian-btn-reset" id="btnResetFilterEmpty">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter & Pencarian
        </button>
    </div>

    <!-- Informasi Data dan Pagination -->
    <div class="penilaian-table-footer">
        <div class="penilaian-pagination-info" id="penilaianPaginationInfo">
            Menampilkan 0 dari 0 data
        </div>
        <div class="penilaian-pagination-controls" id="penilaianPaginationControls">
            <!-- Navigasi tombol pagination dirender dinamis -->
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Sumber Data dari Controller
    const rawData = <?= json_encode($arsip ?? [], JSON_UNESCAPED_SLASHES) ?>;
    const baseFormUrl = "<?= base_url('/penilaian/form') ?>";
    const baseDetailUrl = "<?= base_url('/penilaian/detail') ?>";

    // Konfigurasi & State
    let currentPage = 1;
    let searchQuery = '';
    let filterStatus = 'all';
    let currentSort = {
        key: null,
        direction: 'asc'
    };
    let filteredData = [...rawData];

    // Elemen DOM
    const searchInput = document.getElementById('penilaianSearchInput');
    const clearSearchBtn = document.getElementById('penilaianClearSearchBtn');
    const statusFilter = document.getElementById('penilaianStatusFilter');
    const perPageSelect = document.getElementById('penilaianPerPageSelect');
    const btnResetToolbar = document.getElementById('btnResetFilterToolbar');
    const btnResetEmpty = document.getElementById('btnResetFilterEmpty');
    const tableBody = document.getElementById('penilaianTableBody');
    const emptyState = document.getElementById('penilaianEmptyState');
    const paginationInfo = document.getElementById('penilaianPaginationInfo');
    const paginationControls = document.getElementById('penilaianPaginationControls');
    const sortHeaders = document.querySelectorAll('.sortable-th');

    /**
     * Hitung & Sinkronkan Angka Kartu Statistik dari Data
     */
    function updateStatCounts() {
        let total = rawData.length;
        let belum = 0;
        let sudah = 0;
        let revisi = 0;
        let ditolak = 0;

        rawData.forEach(function(item) {
            const st = String(item.status_autentikasi || '').trim().toLowerCase();
            if (st === 'belum dinilai' || st === '') {
                belum++;
            } else if (st === 'revisi metadata' || st === 'perlu revisi') {
                revisi++;
            } else if (st === 'ditolak') {
                ditolak++;
            } else {
                sudah++;
            }
        });

        const elTotal = document.getElementById('statTotalArsip');
        const elBelum = document.getElementById('statBelumDinilai');
        const elSudah = document.getElementById('statSudahDinilai');
        const elRevisi = document.getElementById('statRevisiMetadata');

        if (elTotal) elTotal.textContent = total;
        if (elBelum) elBelum.textContent = belum;
        if (elSudah) elSudah.textContent = sudah;
        if (elRevisi) elRevisi.textContent = revisi;
    }

    /**
     * Helper Escape HTML
     */
    function escapeHtml(text) {
        if (text === null || text === undefined) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return String(text).replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    /**
     * Render Skor Prioritas
     */
    function renderSkorPrioritas(skor) {
        if (skor === null || skor === undefined || skor === '' || isNaN(skor)) {
            return '<span class="penilaian-dash">—</span>';
        }
        return `<span class="penilaian-score-badge">${escapeHtml(skor)}</span>`;
    }

    /**
     * Render Badge Status Autentikasi
     */
    function renderStatusAutentikasiBadge(status) {
        const val = String(status || '').trim();
        const lower = val.toLowerCase();

        if (lower === 'belum dinilai' || lower === '') {
            return `
                <span class="penilaian-badge badge-unassessed">
                    <span class="badge-dot"></span>
                    Belum Dinilai
                </span>
            `;
        } else if (lower === 'lolos evaluasi' || lower === 'autentik') {
            return `
                <span class="penilaian-badge badge-approved">
                    <span class="badge-dot"></span>
                    Lolos Evaluasi
                </span>
            `;
        } else if (lower === 'revisi metadata' || lower === 'perlu revisi') {
            return `
                <span class="penilaian-badge badge-revision">
                    <span class="badge-dot"></span>
                    Revisi Metadata
                </span>
            `;
        } else if (lower === 'ditolak') {
            return `
                <span class="penilaian-badge badge-rejected">
                    <span class="badge-dot"></span>
                    Ditolak
                </span>
            `;
        } else {
            return `
                <span class="penilaian-badge badge-neutral">
                    <span class="badge-dot"></span>
                    ${escapeHtml(val)}
                </span>
            `;
        }
    }

    /**
     * Render Tombol Aksi Sesuai Status Arsip
     */
    function renderAksiButtons(row) {
        const st = String(row.status_autentikasi || '').trim().toLowerCase();
        const idArsip = encodeURIComponent(row.id_arsip);
        const formUrl = baseFormUrl + '/' + idArsip;
        const detailUrl = baseDetailUrl + '/' + idArsip;

        if (st === 'belum dinilai' || st === '') {
            return `
                <a href="${formUrl}" class="btn-beri-penilaian" title="Beri Penilaian untuk Arsip ini">
                    Beri Penilaian
                </a>
            `;
        } else {
            return `
                <div class="penilaian-action-stack">
                    <a href="${detailUrl}" class="btn-detail-penilaian" title="Lihat Hasil Penilaian">
                        Detail
                    </a>
                    <a href="${formUrl}" class="btn-edit-penilaian" title="Ubah Penilaian">
                        <i class="bi bi-pencil-square"></i> Edit
                    </a>
                </div>
            `;
        }
    }

    /**
     * Terapkan Filter & Pencarian
     */
    function applyFilters() {
        const query = searchQuery.trim().toLowerCase();

        filteredData = rawData.filter(function(item) {
            // Filter Status Autentikasi
            if (filterStatus !== 'all') {
                const itemStatus = String(item.status_autentikasi || '').trim().toLowerCase();
                const targetStatus = filterStatus.toLowerCase();

                if (targetStatus === 'belum dinilai') {
                    if (itemStatus !== 'belum dinilai' && itemStatus !== '') {
                        return false;
                    }
                } else if (itemStatus !== targetStatus) {
                    return false;
                }
            }

            // Filter Pencarian (Nomor Arsip, Nama Arsip, atau OPD)
            if (query !== '') {
                const nomor = String(item.nomor_arsip || '').toLowerCase();
                const nama = String(item.nama_arsip || '').toLowerCase();
                const opd = String(item.nama_opd || '').toLowerCase();

                return nomor.includes(query) || nama.includes(query) || opd.includes(query);
            }

            return true;
        });

        // Terapkan Pengurutan jika aktif
        if (currentSort.key) {
            applySorting();
        }

        currentPage = 1;
        renderTable();
    }

    /**
     * Terapkan Pengurutan Kolom
     */
    function applySorting() {
        const key = currentSort.key;
        const dir = currentSort.direction;

        filteredData.sort(function(a, b) {
            if (key === 'skor_prioritas') {
                const valA = (a.skor_prioritas !== null && a.skor_prioritas !== undefined && a.skor_prioritas !== '') ? Number(a.skor_prioritas) : -1;
                const valB = (b.skor_prioritas !== null && b.skor_prioritas !== undefined && b.skor_prioritas !== '') ? Number(b.skor_prioritas) : -1;
                return dir === 'asc' ? valA - valB : valB - valA;
            }

            let strA = (a[key] !== null && a[key] !== undefined) ? String(a[key]).toLowerCase() : '';
            let strB = (b[key] !== null && b[key] !== undefined) ? String(b[key]).toLowerCase() : '';

            if (strA < strB) return dir === 'asc' ? -1 : 1;
            if (strA > strB) return dir === 'asc' ? 1 : -1;
            return 0;
        });
    }

    /**
     * Render Tabel dan Pagination
     */
    function renderTable() {
        const totalRows = filteredData.length;
        const perPageVal = perPageSelect ? perPageSelect.value : '10';
        const effectivePerPage = perPageVal === 'all' ? Math.max(totalRows, 1) : (parseInt(perPageVal, 10) || 10);
        const totalPages = Math.ceil(totalRows / effectivePerPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * effectivePerPage;
        const endIndex = Math.min(startIndex + effectivePerPage, totalRows);
        const pageData = filteredData.slice(startIndex, endIndex);

        tableBody.innerHTML = '';

        if (totalRows === 0) {
            emptyState.classList.remove('d-none');
            paginationInfo.textContent = 'Menampilkan 0 dari 0 data';
            paginationControls.innerHTML = '';
            return;
        }

        emptyState.classList.add('d-none');

        // Render Baris Data
        pageData.forEach(function(row, idx) {
            const rowNumber = startIndex + idx + 1;
            const detailUrl = baseDetailUrl + '/' + encodeURIComponent(row.id_arsip);
            const tr = document.createElement('tr');

            const opdText = row.nama_opd ? escapeHtml(row.nama_opd) : '<span class="penilaian-dash">—</span>';

            tr.innerHTML = `
                <td class="col-no">${rowNumber}</td>
                <td class="col-nomor-arsip">
                    <a href="${detailUrl}" class="penilaian-link" title="Buka Detail Penilaian Arsip">
                        ${escapeHtml(row.nomor_arsip)}
                    </a>
                </td>
                <td class="col-nama-arsip">
                    <span class="penilaian-nama-text">
                        ${escapeHtml(row.nama_arsip)}
                    </span>
                </td>
                <td class="col-opd">
                    <span class="penilaian-opd-text">
                        ${opdText}
                    </span>
                </td>
                <td class="col-skor text-center">
                    ${renderSkorPrioritas(row.skor_prioritas)}
                </td>
                <td class="col-status">
                    ${renderStatusAutentikasiBadge(row.status_autentikasi)}
                </td>
                <td class="col-aksi">
                    ${renderAksiButtons(row)}
                </td>
            `;

            tableBody.appendChild(tr);
        });

        // Informasi Pagination
        const displayStart = totalRows > 0 ? startIndex + 1 : 0;
        paginationInfo.textContent = `Menampilkan ${displayStart} – ${endIndex} dari ${totalRows} data`;

        // Render Navigasi Pagination
        renderPagination(totalPages);
    }

    /**
     * Render Navigasi Kontrol Pagination
     */
    function renderPagination(totalPages) {
        paginationControls.innerHTML = '';

        if (totalPages <= 1) {
            const singleBtn = document.createElement('button');
            singleBtn.type = 'button';
            singleBtn.className = 'penilaian-page-btn active';
            singleBtn.textContent = '1';
            paginationControls.appendChild(singleBtn);
            return;
        }

        // Tombol Sebelumnya (<)
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = 'penilaian-page-btn' + (currentPage === 1 ? ' disabled' : '');
        prevBtn.innerHTML = '<i class="bi bi-chevron-left"></i>';
        prevBtn.title = 'Halaman Sebelumnya';
        prevBtn.addEventListener('click', function() {
            if (currentPage > 1) {
                currentPage--;
                renderTable();
            }
        });
        paginationControls.appendChild(prevBtn);

        // Nomor Halaman
        for (let i = 1; i <= totalPages; i++) {
            if (
                i === 1 || 
                i === totalPages || 
                (i >= currentPage - 1 && i <= currentPage + 1)
            ) {
                const pageBtn = document.createElement('button');
                pageBtn.type = 'button';
                pageBtn.className = 'penilaian-page-btn' + (i === currentPage ? ' active' : '');
                pageBtn.textContent = i;
                pageBtn.addEventListener('click', (function(pageNum) {
                    return function() {
                        currentPage = pageNum;
                        renderTable();
                    };
                })(i));
                paginationControls.appendChild(pageBtn);
            } else if (
                (i === currentPage - 2 && i > 1) || 
                (i === currentPage + 2 && i < totalPages)
            ) {
                const ellipsis = document.createElement('span');
                ellipsis.className = 'penilaian-page-ellipsis';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
        }

        // Tombol Berikutnya (>)
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = 'penilaian-page-btn' + (currentPage === totalPages ? ' disabled' : '');
        nextBtn.innerHTML = '<i class="bi bi-chevron-right"></i>';
        nextBtn.title = 'Halaman Berikutnya';
        nextBtn.addEventListener('click', function() {
            if (currentPage < totalPages) {
                currentPage++;
                renderTable();
            }
        });
        paginationControls.appendChild(nextBtn);
    }

    /**
     * Handler Pencarian
     */
    searchInput.addEventListener('input', function() {
        searchQuery = this.value;
        if (searchQuery.trim().length > 0) {
            clearSearchBtn.classList.remove('d-none');
        } else {
            clearSearchBtn.classList.add('d-none');
        }
        applyFilters();
    });

    clearSearchBtn.addEventListener('click', function() {
        searchInput.value = '';
        searchQuery = '';
        this.classList.add('d-none');
        searchInput.focus();
        applyFilters();
    });

    /**
     * Handler Filter Status Autentikasi
     */
    statusFilter.addEventListener('change', function() {
        filterStatus = this.value;
        applyFilters();
    });

    /**
     * Handler Per-Page Dropdown
     */
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            currentPage = 1;
            renderTable();
        });
    }

    /**
     * Handler Tombol Reset (Toolbar & Empty State)
     */
    function resetAllFilters() {
        searchInput.value = '';
        searchQuery = '';
        clearSearchBtn.classList.add('d-none');
        statusFilter.value = 'all';
        filterStatus = 'all';
        if (perPageSelect) {
            perPageSelect.value = '10';
        }
        currentSort = { key: null, direction: 'asc' };

        sortHeaders.forEach(function(th) {
            th.classList.remove('active-sort');
            const icon = th.querySelector('.penilaian-sort-icon');
            if (icon) {
                icon.className = 'bi bi-arrow-down-up penilaian-sort-icon';
            }
        });

        applyFilters();
    }

    if (btnResetToolbar) {
        btnResetToolbar.addEventListener('click', resetAllFilters);
    }

    if (btnResetEmpty) {
        btnResetEmpty.addEventListener('click', resetAllFilters);
    }

    /**
     * Handler Pengurutan Kolom
     */
    sortHeaders.forEach(function(th) {
        th.addEventListener('click', function() {
            const sortKey = this.getAttribute('data-sort-key');
            if (!sortKey) return;

            if (currentSort.key === sortKey) {
                if (currentSort.direction === 'asc') {
                    currentSort.direction = 'desc';
                } else {
                    currentSort.key = null;
                    currentSort.direction = 'asc';
                }
            } else {
                currentSort.key = sortKey;
                currentSort.direction = 'asc';
            }

            // Perbarui Tampilan Ikon Sort
            sortHeaders.forEach(function(header) {
                const icon = header.querySelector('.penilaian-sort-icon');
                if (!icon) return;

                if (header.getAttribute('data-sort-key') === currentSort.key) {
                    header.classList.add('active-sort');
                    if (currentSort.direction === 'asc') {
                        icon.className = 'bi bi-arrow-up penilaian-sort-icon';
                    } else {
                        icon.className = 'bi bi-arrow-down penilaian-sort-icon';
                    }
                } else {
                    header.classList.remove('active-sort');
                    icon.className = 'bi bi-arrow-down-up penilaian-sort-icon';
                }
            });

            if (currentSort.key) {
                applySorting();
            } else {
                applyFilters();
            }

            renderTable();
        });
    });

    // Inisialisasi Tampilan Awal
    updateStatCounts();
    applyFilters();
});
</script>
<?= $this->endSection() ?>
