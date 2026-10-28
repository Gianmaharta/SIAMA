<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Surat Perintah Tugas - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/spt-master.css?v=' . (file_exists(FCPATH . 'css/spt-master.css') ? filemtime(FCPATH . 'css/spt-master.css') : time())) ?>">
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
<div class="spt-page-header">
    <h1 class="spt-page-title">Surat Perintah Tugas</h1>
    <p class="spt-page-subtitle">Kelola surat perintah tugas alih media arsip.</p>
</div>

<!-- Kotak Informasi / Empat Kartu Statistik SPT -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Kartu 1: Total Semua SPT -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="spt-stat-card">
            <div class="spt-stat-icon-wrapper bg-icon-blue">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="spt-stat-body">
                <span class="spt-stat-title">Total Semua SPT</span>
                <div class="spt-stat-number-wrap">
                    <span class="spt-stat-number" id="statTotalSpt"><?= number_format($totalSpt ?? 0, 0, ',', '.') ?></span>
                    <span class="spt-stat-unit">SPT</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 2: Belum Ditugaskan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="spt-stat-card">
            <div class="spt-stat-icon-wrapper bg-icon-orange">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="spt-stat-body">
                <span class="spt-stat-title">Belum Ditugaskan</span>
                <div class="spt-stat-number-wrap">
                    <span class="spt-stat-number" id="statBelumDitugaskan"><?= number_format($belumDitugaskan ?? 0, 0, ',', '.') ?></span>
                    <span class="spt-stat-unit">SPT</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 3: Sedang Ditugaskan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="spt-stat-card">
            <div class="spt-stat-icon-wrapper bg-icon-indigo">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div class="spt-stat-body">
                <span class="spt-stat-title">Sedang Ditugaskan</span>
                <div class="spt-stat-number-wrap">
                    <span class="spt-stat-number" id="statDitugaskan"><?= number_format($ditugaskan ?? 0, 0, ',', '.') ?></span>
                    <span class="spt-stat-unit">SPT</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 4: Selesai Dilaksanakan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="spt-stat-card">
            <div class="spt-stat-icon-wrapper bg-icon-green">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="spt-stat-body">
                <span class="spt-stat-title">Selesai Dilaksanakan</span>
                <div class="spt-stat-number-wrap">
                    <span class="spt-stat-number" id="statSelesai"><?= number_format($selesai ?? 0, 0, ',', '.') ?></span>
                    <span class="spt-stat-unit">SPT</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel Tabel SPT -->
<div class="spt-table-card">
    <!-- Toolbar Tabel -->
    <div class="spt-table-toolbar">
        <!-- Kolom Pencarian SPT -->
        <div class="spt-search-wrap">
            <i class="bi bi-search spt-search-icon"></i>
            <input 
                type="text" 
                id="sptSearchInput" 
                class="spt-search-input" 
                placeholder="Cari nomor SPT atau perihal..."
                autocomplete="off"
            >
            <button type="button" class="spt-clear-search d-none" id="sptClearSearchBtn" title="Bersihkan pencarian">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <!-- Filter & Tampilkan Data -->
        <div class="spt-toolbar-actions">
            <!-- Pilihan Jumlah Data Per Halaman -->
            <div class="spt-filter-wrap">
                <label for="sptPerPageSelect" class="spt-filter-label">Tampilkan</label>
                <select id="sptPerPageSelect" class="spt-filter-select spt-select-per-page" aria-label="Jumlah Data Per Halaman">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">Semua</option>
                </select>
            </div>

            <!-- Filter Penugasan -->
            <div class="spt-filter-wrap">
                <label for="sptPenugasanFilter" class="spt-filter-label">Penugasan</label>
                <select id="sptPenugasanFilter" class="spt-filter-select" aria-label="Filter Status Penugasan">
                    <option value="all" selected>Semua</option>
                    <option value="belum_ditugaskan">Belum Ditugaskan</option>
                    <option value="ditugaskan">Ditugaskan</option>
                    <option value="selesai">Selesai</option>
                </select>
            </div>

            <?php if (in_array(session()->get('nama_role'), ['Pimpinan', 'Kepala_Dinas', 'Kepala Dinas'])) : ?>
                <button type="button" class="spt-btn-create ms-sm-1" data-bs-toggle="modal" data-bs-target="#modalTerbitkanSpt">
                    <i class="bi bi-plus-lg"></i>
                    <span>Terbitkan SPT</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel Surat Perintah Tugas -->
    <div class="table-responsive spt-table-responsive">
        <table class="table spt-table align-middle" id="sptTable">
            <thead>
                <tr>
                    <th scope="col" class="col-no">NO</th>
                    <th scope="col" class="col-nomor-spt sortable-th" data-sort-key="nomor_spt" title="Klik untuk mengurutkan berdasarkan Nomor SPT">
                        NOMOR SPT <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-tanggal-spt sortable-th" data-sort-key="tanggal_spt" title="Klik untuk mengurutkan berdasarkan Tanggal SPT">
                        TANGGAL SPT <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-perihal sortable-th" data-sort-key="perihal" title="Klik untuk mengurutkan berdasarkan Perihal">
                        PERIHAL <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-periode sortable-th" data-sort-key="tanggal_mulai" title="Klik untuk mengurutkan berdasarkan Periode Pelaksanaan">
                        PERIODE PELAKSANAAN <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-status-spt sortable-th" data-sort-key="status" title="Klik untuk mengurutkan berdasarkan Status SPT">
                        STATUS SPT <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-status-penugasan sortable-th" data-sort-key="status_penugasan" title="Klik untuk mengurutkan berdasarkan Status Penugasan">
                        STATUS PENUGASAN <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-aksi text-center">AKSI</th>
                </tr>
            </thead>
            <tbody id="sptTableBody">
                <!-- Baris tabel dirender secara dinamis oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Empty State (Ketika data tidak ditemukan) -->
    <div class="spt-empty-state d-none" id="sptEmptyState">
        <div class="spt-empty-icon-wrap">
            <i class="bi bi-search"></i>
        </div>
        <h5 class="spt-empty-title">Data SPT Tidak Ditemukan</h5>
        <p class="spt-empty-desc">Tidak ada surat perintah tugas yang sesuai dengan pencarian atau filter penugasan Anda.</p>
        <button type="button" class="spt-btn-reset" id="btnResetFilter">
            <i class="bi bi-arrow-counterclockwise"></i> Reset Filter & Pencarian
        </button>
    </div>

    <!-- Informasi Data dan Pagination -->
    <div class="spt-table-footer">
        <div class="spt-pagination-info" id="sptPaginationInfo">
            Menampilkan 0 dari 0 data
        </div>
        <div class="spt-pagination-controls" id="sptPaginationControls">
            <!-- Tombol navigasi pagination dirender dinamis -->
        </div>
    </div>
</div>

<!-- Modal Overlay: Terbitkan SPT -->
<?= $this->include('spt/modal_terbitkan') ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari Controller CI4
    const rawData = <?= json_encode($spt_list ?? [], JSON_UNESCAPED_SLASHES) ?>;
    const baseDetailUrl = "<?= base_url('/spt/detail') ?>";
    const baseAssignUrl = "<?= base_url('/spt/assign') ?>";
    const currentUserRole = "<?= esc($user_role ?? session()->get('nama_role') ?? '') ?>";

    // Konfigurasi & State
    let currentPage = 1;
    let searchQuery = '';
    let filterPenugasan = 'all';
    let currentSort = {
        key: null,
        direction: 'asc' // 'asc' atau 'desc'
    };
    let filteredData = [...rawData];

    // Elemen DOM
    const searchInput = document.getElementById('sptSearchInput');
    const clearSearchBtn = document.getElementById('sptClearSearchBtn');
    const filterSelect = document.getElementById('sptPenugasanFilter');
    const perPageSelect = document.getElementById('sptPerPageSelect');
    const tableBody = document.getElementById('sptTableBody');
    const emptyState = document.getElementById('sptEmptyState');
    const paginationInfo = document.getElementById('sptPaginationInfo');
    const paginationControls = document.getElementById('sptPaginationControls');
    const btnResetFilter = document.getElementById('btnResetFilter');
    const sortHeaders = document.querySelectorAll('.sortable-th');

    /**
     * Hitung & Sinkronkan Angka Kartu Statistik dari Data
     */
    function updateStatCounts() {
        let total = rawData.length;
        let belum = 0;
        let tugas = 0;
        let selesai = 0;

        rawData.forEach(function(item) {
            const s = String(item.status_penugasan || '').trim().toLowerCase();
            if (s === 'selesai') {
                selesai++;
            } else if (s === 'ditugaskan' || s === 'proses' || s === 'dalam proses') {
                tugas++;
            } else {
                belum++;
            }
        });

        const elTotal = document.getElementById('statTotalSpt');
        const elBelum = document.getElementById('statBelumDitugaskan');
        const elTugas = document.getElementById('statDitugaskan');
        const elSelesai = document.getElementById('statSelesai');

        if (elTotal) elTotal.textContent = total;
        if (elBelum) elBelum.textContent = belum;
        if (elTugas) elTugas.textContent = tugas;
        if (elSelesai) elSelesai.textContent = selesai;
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
     * Render Badge Status SPT (Status Penerbitan)
     */
    function renderStatusSptBadge(status) {
        const val = String(status || '').trim();
        const lower = val.toLowerCase();

        if (lower === 'aktif') {
            return `
                <span class="spt-badge spt-badge-success">
                    <span class="spt-badge-dot"></span>
                    Aktif
                </span>
            `;
        } else if (lower === 'nonaktif' || lower === 'tidak aktif' || lower === 'dibatalkan') {
            return `
                <span class="spt-badge spt-badge-neutral">
                    <span class="spt-badge-dot"></span>
                    ${escapeHtml(val || 'Tidak Aktif')}
                </span>
            `;
        } else {
            return `
                <span class="spt-badge spt-badge-success">
                    <span class="spt-badge-dot"></span>
                    ${escapeHtml(val || 'Aktif')}
                </span>
            `;
        }
    }

    /**
     * Render Badge Status Penugasan
     */
    function renderStatusPenugasanBadge(status) {
        const val = String(status || '').trim().toLowerCase();

        if (val === 'selesai') {
            return `
                <span class="spt-badge spt-badge-success">
                    <span class="spt-badge-dot"></span>
                    Selesai
                </span>
            `;
        } else if (val === 'ditugaskan' || val === 'proses' || val === 'dalam proses') {
            return `
                <span class="spt-badge spt-badge-info">
                    <span class="spt-badge-dot"></span>
                    ${val === 'proses' ? 'Dalam Proses' : 'Ditugaskan'}
                </span>
            `;
        } else if (val === 'belum_ditugaskan' || val === 'belum ditugaskan' || val === 'pending') {
            return `
                <span class="spt-badge spt-badge-warning">
                    <span class="spt-badge-dot"></span>
                    Belum Ditugaskan
                </span>
            `;
        } else {
            return `
                <span class="spt-badge spt-badge-neutral">
                    <span class="spt-badge-dot"></span>
                    ${escapeHtml(status || 'Belum Ditugaskan')}
                </span>
            `;
        }
    }

    /**
     * Render Format Periode Pelaksanaan
     */
    function formatPeriode(mulai, selesai) {
        const tMulai = mulai ? mulai.substring(0, 10) : '-';
        const tSelesai = selesai ? selesai.substring(0, 10) : '-';
        return `${tMulai} s/d ${tSelesai}`;
    }

    /**
     * Terapkan Filter & Pencarian
     */
    function applyFilters() {
        const query = searchQuery.trim().toLowerCase();

        filteredData = rawData.filter(function(item) {
            // Filter Penugasan
            if (filterPenugasan !== 'all') {
                const itemStatusPenugasan = String(item.status_penugasan || '').toLowerCase();
                if (filterPenugasan === 'belum_ditugaskan') {
                    if (itemStatusPenugasan !== 'belum_ditugaskan' && itemStatusPenugasan !== 'pending') {
                        return false;
                    }
                } else if (filterPenugasan === 'ditugaskan') {
                    if (itemStatusPenugasan !== 'ditugaskan' && itemStatusPenugasan !== 'proses' && itemStatusPenugasan !== 'dalam proses') {
                        return false;
                    }
                } else if (filterPenugasan === 'selesai') {
                    if (itemStatusPenugasan !== 'selesai') {
                        return false;
                    }
                }
            }

            // Pencarian SPT (berdasarkan nomor SPT atau perihal)
            if (query !== '') {
                const nomor = String(item.nomor_spt || '').toLowerCase();
                const perihal = String(item.perihal || '').toLowerCase();
                return nomor.includes(query) || perihal.includes(query);
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
     * Terapkan Pengurutan
     */
    function applySorting() {
        const key = currentSort.key;
        const dir = currentSort.direction;

        filteredData.sort(function(a, b) {
            let valA = (a[key] !== null && a[key] !== undefined) ? String(a[key]).toLowerCase() : '';
            let valB = (b[key] !== null && b[key] !== undefined) ? String(b[key]).toLowerCase() : '';

            // Handle sorting untuk tanggal
            if (key === 'tanggal_spt' || key === 'tanggal_mulai') {
                const dateA = new Date(valA).getTime() || 0;
                const dateB = new Date(valB).getTime() || 0;
                return dir === 'asc' ? dateA - dateB : dateB - dateA;
            }

            if (valA < valB) return dir === 'asc' ? -1 : 1;
            if (valA > valB) return dir === 'asc' ? 1 : -1;
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
            const detailUrl = baseDetailUrl + '/' + encodeURIComponent(row.id_spt);
            const assignUrl = baseAssignUrl + '/' + encodeURIComponent(row.id_spt);
            const tr = document.createElement('tr');

            const statusPenugasanLower = String(row.status_penugasan || '').trim().toLowerCase();
            const isBelumDitugaskan = !statusPenugasanLower || statusPenugasanLower === 'belum_ditugaskan' || statusPenugasanLower === 'pending';
            const canAssign = ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'].includes(currentUserRole);

            let actionHtml = `<div class="spt-action-group">`;
            if (canAssign && isBelumDitugaskan) {
                actionHtml += `
                    <a href="${assignUrl}" class="spt-action-btn spt-btn-assign" title="Tugaskan Arsiparis">
                        <i class="bi bi-person-plus"></i> <span>Tugaskan</span>
                    </a>
                `;
            }
            actionHtml += `
                <a href="${detailUrl}" class="spt-action-btn spt-btn-detail" title="Lihat Detail SPT">
                    <i class="bi bi-eye"></i> <span>Detail</span>
                </a>
            </div>`;

            tr.innerHTML = `
                <td class="col-no">${rowNumber}</td>
                <td class="col-nomor-spt">
                    <a href="${detailUrl}" class="spt-link" title="Buka Detail SPT">
                        ${escapeHtml(row.nomor_spt)}
                    </a>
                </td>
                <td class="col-tanggal-spt">
                    <span class="spt-date-text">${escapeHtml(row.tanggal_spt ? row.tanggal_spt.substring(0, 10) : '-')}</span>
                </td>
                <td class="col-perihal">
                    <span class="spt-perihal-text" title="${escapeHtml(row.perihal)}">
                        ${escapeHtml(row.perihal)}
                    </span>
                </td>
                <td class="col-periode">
                    <span class="spt-period-text">
                        ${formatPeriode(row.tanggal_mulai, row.tanggal_selesai)}
                    </span>
                </td>
                <td class="col-status-spt">
                    ${renderStatusSptBadge(row.status)}
                </td>
                <td class="col-status-penugasan">
                    ${renderStatusPenugasanBadge(row.status_penugasan)}
                </td>
                <td class="col-aksi text-center">
                    ${actionHtml}
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
     * Render Tombol Kontrol Pagination
     */
    function renderPagination(totalPages) {
        paginationControls.innerHTML = '';

        if (totalPages <= 1) {
            const singleBtn = document.createElement('button');
            singleBtn.type = 'button';
            singleBtn.className = 'spt-page-btn active';
            singleBtn.textContent = '1';
            paginationControls.appendChild(singleBtn);
            return;
        }

        // Tombol Sebelumnya (<)
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = 'spt-page-btn' + (currentPage === 1 ? ' disabled' : '');
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
                pageBtn.className = 'spt-page-btn' + (i === currentPage ? ' active' : '');
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
                ellipsis.className = 'spt-page-ellipsis';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
        }

        // Tombol Berikutnya (>)
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = 'spt-page-btn' + (currentPage === totalPages ? ' disabled' : '');
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
     * Handler Pilihan Jumlah Data Per Halaman
     */
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            currentPage = 1;
            renderTable();
        });
    }

    /**
     * Handler Filter Penugasan
     */
    filterSelect.addEventListener('change', function() {
        filterPenugasan = this.value;
        applyFilters();
    });

    /**
     * Handler Reset Filter & Pencarian
     */
    btnResetFilter.addEventListener('click', function() {
        searchInput.value = '';
        searchQuery = '';
        clearSearchBtn.classList.add('d-none');
        filterSelect.value = 'all';
        filterPenugasan = 'all';
        if (perPageSelect) {
            perPageSelect.value = '10';
        }
        currentSort = { key: null, direction: 'asc' };

        sortHeaders.forEach(function(th) {
            th.classList.remove('active-sort');
            const icon = th.querySelector('.spt-sort-icon');
            if (icon) {
                icon.className = 'bi bi-arrow-down-up spt-sort-icon';
            }
        });

        applyFilters();
    });

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

            // Update Tampilan Ikon Sort
            sortHeaders.forEach(function(header) {
                const icon = header.querySelector('.spt-sort-icon');
                if (!icon) return;

                if (header.getAttribute('data-sort-key') === currentSort.key) {
                    header.classList.add('active-sort');
                    if (currentSort.direction === 'asc') {
                        icon.className = 'bi bi-arrow-up spt-sort-icon';
                    } else {
                        icon.className = 'bi bi-arrow-down spt-sort-icon';
                    }
                } else {
                    header.classList.remove('active-sort');
                    icon.className = 'bi bi-arrow-down-up spt-sort-icon';
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
