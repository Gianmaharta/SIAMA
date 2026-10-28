<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Pengelolaan Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/arsip-master.css?v=' . (file_exists(FCPATH . 'css/arsip-master.css') ? filemtime(FCPATH . 'css/arsip-master.css') : time())) ?>">
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
<div class="arsip-page-header mb-4">
    <h1 class="arsip-page-title mb-1">Pengelolaan Arsip</h1>
    <p class="arsip-page-subtitle">Kelola dan unggah arsip digital sesuai ketentuan dan jadwal retensi arsip (JRA).</p>
</div>

<!-- Panel Tabel Pengelolaan Arsip -->
<div class="arsip-table-card">
    <!-- Toolbar Tabel Horizontal: Search Kiri, Filter & CTA Kanan -->
    <div class="arsip-table-toolbar">
        <!-- Kolom Pencarian (Kiri) -->
        <div class="arsip-search-wrap">
            <i class="bi bi-search arsip-search-icon"></i>
            <input 
                type="text" 
                id="arsipSearchInput" 
                class="arsip-search-input" 
                placeholder="Cari nomor, nama arsip, atau bidang..."
                autocomplete="off"
            >
            <button type="button" class="arsip-clear-search d-none" id="arsipClearSearchBtn" title="Bersihkan pencarian">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <!-- Filter & Tombol Aksi (Kanan) -->
        <div class="arsip-toolbar-filters">
            <!-- Filter Bidang -->
            <div class="arsip-filter-group">
                <label for="filterBidang" class="arsip-filter-label">Bidang</label>
                <select id="filterBidang" class="arsip-filter-select" aria-label="Filter Bidang Pengolah">
                    <option value="all" selected>Semua Bidang</option>
                    <?php if (!empty($bidang_list)) : ?>
                        <?php foreach ($bidang_list as $b) : ?>
                            <option value="<?= esc($b['nama_bidang']) ?>"><?= esc($b['nama_bidang']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>

            <!-- Filter Status -->
            <div class="arsip-filter-group">
                <label for="filterStatus" class="arsip-filter-label">Status</label>
                <select id="filterStatus" class="arsip-filter-select" aria-label="Filter Status Verifikasi">
                    <option value="all" selected>Semua Status</option>
                    <option value="Selesai">Selesai</option>
                    <option value="Dalam Proses">Dalam Proses</option>
                    <option value="Menunggu">Menunggu</option>
                    <option value="Draft">Draft</option>
                    <option value="Ditolak">Ditolak / Revisi</option>
                </select>
            </div>

            <!-- Tombol Utama + Tambah Arsip (CTA Utama) -->
            <?php if (in_array($user_role ?? session()->get('nama_role'), ['Arsiparis', 'Admin_Pemkab'])) : ?>
                <a href="<?= base_url('/arsip/create') ?>" class="btn arsip-btn-add" id="btnTambahArsip" title="Tambah Arsip Baru">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Arsip</span>
                </a>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel Pengelolaan Arsip (9 Kolom) -->
    <div class="table-responsive arsip-table-responsive">
        <table class="table arsip-table align-middle" id="arsipTable">
            <thead>
                <tr>
                    <th scope="col" class="col-no">NO</th>
                    <th scope="col" class="col-nomor-arsip sortable-th" data-sort-key="nomor_arsip" title="Klik untuk mengurutkan Nomor Arsip">
                        NOMOR ARSIP <i class="bi bi-arrow-down-up arsip-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-nama-arsip sortable-th" data-sort-key="nama_arsip" title="Klik untuk mengurutkan Nama Arsip">
                        NAMA ARSIP <i class="bi bi-arrow-down-up arsip-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-kode-klasifikasi sortable-th text-center" data-sort-key="kode_klasifikasi_text" title="Klik untuk mengurutkan Kode Klasifikasi">
                        KODE KLASIFIKASI <i class="bi bi-arrow-down-up arsip-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-bidang sortable-th" data-sort-key="nama_bidang" title="Klik untuk mengurutkan Bidang">
                        BIDANG <i class="bi bi-arrow-down-up arsip-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-skor-prioritas sortable-th text-center" data-sort-key="skor_prioritas" title="Klik untuk mengurutkan Skor Prioritas">
                        SKOR PRIORITAS <i class="bi bi-arrow-down-up arsip-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-status-verifikasi sortable-th text-center" data-sort-key="status_verifikasi" title="Klik untuk mengurutkan Status Verifikasi">
                        STATUS VERIFIKASI <i class="bi bi-arrow-down-up arsip-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-berkas-file sortable-th text-center" data-sort-key="file_arsip" title="Klik untuk mengurutkan Berkas File">
                        BERKAS FILE <i class="bi bi-arrow-down-up arsip-sort-icon"></i>
                    </th>
                    <th scope="col" class="col-aksi text-center">
                        AKSI
                    </th>
                </tr>
            </thead>
            <tbody id="arsipTableBody">
                <!-- Baris tabel dirender secara dinamis oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Empty State: Ditampilkan jika belum ada arsip atau pencarian tidak ditemukan -->
    <div class="arsip-empty-state d-none" id="arsipEmptyState">
        <div class="arsip-empty-icon-wrap">
            <i class="bi bi-folder2-open"></i>
        </div>
        <h5 class="arsip-empty-title" id="emptyTitle">Belum ada arsip</h5>
        <p class="arsip-empty-desc" id="emptyDesc">Belum ada arsip yang tersedia untuk dikelola.</p>
        <div class="d-flex align-items-center justify-content-center gap-2 mt-3" id="emptyActions">
            <?php if (in_array($user_role ?? session()->get('nama_role'), ['Arsiparis', 'Admin_Pemkab'])) : ?>
                <a href="<?= base_url('/arsip/create') ?>" class="btn arsip-btn-add" id="emptyBtnTambahArsip">
                    <i class="bi bi-plus-lg"></i>
                    <span>Tambah Arsip</span>
                </a>
            <?php endif; ?>
            <button type="button" class="arsip-btn-reset d-none" id="btnResetFilter">
                <i class="bi bi-arrow-counterclockwise"></i> Reset Filter & Pencarian
            </button>
        </div>
    </div>

    <!-- Informasi Data dan Pagination -->
    <div class="arsip-table-footer">
        <div class="arsip-pagination-info" id="arsipPaginationInfo">
            Menampilkan 0 – 0 dari 0 data
        </div>
        <div class="arsip-pagination-controls" id="arsipPaginationControls">
            <!-- Tombol navigasi pagination dirender dinamis -->
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari Controller CI4
    const rawData = <?= json_encode($arsip_list ?? [], JSON_UNESCAPED_SLASHES) ?>;
    const baseDetailUrl = "<?= base_url('/arsip/detail') ?>";
    const baseEditUrl = "<?= base_url('/arsip/edit') ?>";
    const baseDeleteUrl = "<?= base_url('/arsip/delete') ?>";
    const baseFileUrl = "<?= base_url('uploads/arsip') ?>";
    const currentUserRole = "<?= esc($user_role ?? session()->get('nama_role') ?? '') ?>";

    // Konfigurasi & State
    let perPage = 10;
    let currentPage = 1;
    let searchQuery = '';
    let filterBidang = 'all';
    let filterStatus = 'all';
    let currentSort = {
        key: null,
        direction: 'asc' // 'asc' atau 'desc'
    };
    let filteredData = [...rawData];

    // Elemen DOM
    const searchInput = document.getElementById('arsipSearchInput');
    const clearSearchBtn = document.getElementById('arsipClearSearchBtn');
    const selectBidang = document.getElementById('filterBidang');
    const selectStatus = document.getElementById('filterStatus');
    const tableElement = document.getElementById('arsipTable');
    const tableBody = document.getElementById('arsipTableBody');
    const emptyState = document.getElementById('arsipEmptyState');
    const emptyTitle = document.getElementById('emptyTitle');
    const emptyDesc = document.getElementById('emptyDesc');
    const emptyBtnTambahArsip = document.getElementById('emptyBtnTambahArsip');
    const paginationInfo = document.getElementById('arsipPaginationInfo');
    const paginationControls = document.getElementById('arsipPaginationControls');
    const btnResetFilter = document.getElementById('btnResetFilter');
    const sortHeaders = document.querySelectorAll('.sortable-th');

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
    function renderSkorPrioritas(skor, namaKlasifikasi) {
        if (skor !== null && skor !== undefined && String(skor).trim() !== '' && !isNaN(skor)) {
            const categoryText = (namaKlasifikasi && String(namaKlasifikasi).trim() !== '') ? escapeHtml(namaKlasifikasi) : '';
            return `
                <div class="d-inline-flex flex-column align-items-center">
                    <span class="arsip-skor-badge">${escapeHtml(skor)}</span>
                    ${categoryText ? `<span class="arsip-deskripsi-text mt-1">${categoryText}</span>` : ''}
                </div>
            `;
        }
        return `<span class="arsip-skor-empty">Belum Dinilai</span>`;
    }

    /**
     * Render Badge Status Verifikasi
     */
    function renderStatusVerifikasiBadge(status) {
        const val = String(status || '').trim();
        const lower = val.toLowerCase();

        if (['selesai', 'disetujui', 'terverifikasi', 'lolos evaluasi'].includes(lower)) {
            return `
                <span class="arsip-badge arsip-badge-success">
                    <span class="arsip-badge-dot"></span>
                    Selesai
                </span>
            `;
        } else if (['proses', 'dalam_proses', 'dalam proses', 'sedang diproses'].includes(lower)) {
            return `
                <span class="arsip-badge arsip-badge-warning">
                    <span class="arsip-badge-dot"></span>
                    Dalam Proses
                </span>
            `;
        } else if (['ditolak', 'revisi', 'gagal', 'dibatalkan'].includes(lower)) {
            return `
                <span class="arsip-badge arsip-badge-danger">
                    <span class="arsip-badge-dot"></span>
                    ${escapeHtml(val)}
                </span>
            `;
        } else {
            return `
                <span class="arsip-badge arsip-badge-neutral">
                    <span class="arsip-badge-dot"></span>
                    ${escapeHtml(val || 'Draft')}
                </span>
            `;
        }
    }

    /**
     * Render Berkas File
     */
    function renderBerkasFile(fileName) {
        if (fileName && String(fileName).trim() !== '') {
            const fileUrl = baseFileUrl + '/' + encodeURIComponent(fileName);
            return `
                <a href="${fileUrl}" target="_blank" class="arsip-file-status file-ready" title="Buka berkas ${escapeHtml(fileName)}">
                    <span class="arsip-file-dot"></span> Selesai
                </a>
            `;
        }
        return `
            <span class="arsip-file-status file-empty">
                <i class="bi bi-dash-circle me-1"></i>Belum Diunggah
            </span>
        `;
    }

    /**
     * Render Kolom Kode Klasifikasi
     */
    function renderKodeKlasifikasi(kode, nama) {
        const kodeStr = (kode !== null && kode !== undefined && String(kode).trim() !== '') ? String(kode).trim() : '';
        const namaStr = (nama !== null && nama !== undefined && String(nama).trim() !== '') ? String(nama).trim() : '';

        if (!kodeStr) {
            return `<span class="text-muted">—</span>`;
        }

        return `
            <div class="arsip-klasifikasi-wrap">
                <span class="arsip-kode-text">${escapeHtml(kodeStr)}</span>
                ${namaStr ? `<span class="arsip-deskripsi-text">${escapeHtml(namaStr)}</span>` : ''}
            </div>
        `;
    }

    /**
     * Render Kolom Aksi
     */
    function renderAksi(row) {
        const detailUrl = baseDetailUrl + '/' + encodeURIComponent(row.id_arsip);
        const editUrl = baseEditUrl + '/' + encodeURIComponent(row.id_arsip);
        const deleteUrl = baseDeleteUrl + '/' + encodeURIComponent(row.id_arsip);
        const hasFile = row.file_arsip && String(row.file_arsip).trim() !== '';
        const fileUrl = hasFile ? baseFileUrl + '/' + encodeURIComponent(row.file_arsip) : '';
        const canManage = ['Arsiparis', 'Admin_Pemkab'].includes(currentUserRole);

        let dropdownItems = `
            <li>
                <a class="dropdown-item" href="${detailUrl}">
                    <i class="bi bi-eye me-2 text-primary"></i> Detail Lengkap
                </a>
            </li>
        `;

        if (canManage) {
            dropdownItems += `
                <li>
                    <a class="dropdown-item" href="${editUrl}">
                        <i class="bi bi-pencil me-2 text-secondary"></i> Edit Data
                    </a>
                </li>
            `;

            if (!hasFile) {
                dropdownItems += `
                    <li>
                        <a class="dropdown-item text-primary" href="${editUrl}">
                            <i class="bi bi-upload me-2"></i> Unggah Berkas
                        </a>
                    </li>
                `;
            }
        }

        if (hasFile) {
            dropdownItems += `
                <li>
                    <a class="dropdown-item" href="${fileUrl}" target="_blank" download>
                        <i class="bi bi-download me-2 text-success"></i> Unduh Berkas
                    </a>
                </li>
            `;
        }

        if (canManage) {
            dropdownItems += `
                <li><hr class="dropdown-divider my-1"></li>
                <li>
                    <a class="dropdown-item text-danger" href="${deleteUrl}" onclick="return confirm('Apakah Anda yakin ingin menghapus data arsip ini? Data yang dihapus tidak dapat dipulihkan.');">
                        <i class="bi bi-trash me-2"></i> Hapus Arsip
                    </a>
                </li>
            `;
        }

        return `
            <div class="d-inline-flex align-items-center gap-1 justify-content-center">
                <a href="${detailUrl}" class="arsip-btn-action-detail" title="Lihat Detail Arsip">
                    <i class="bi bi-eye"></i> Detail
                </a>
                <div class="dropdown">
                    <button class="arsip-btn-dots" type="button" data-bs-toggle="dropdown" aria-expanded="false" title="Opsi Aksi">
                        <i class="bi bi-three-dots-vertical"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0 py-1" style="border-radius: 10px; font-size: 13px; min-width: 160px; z-index: 1050;">
                        ${dropdownItems}
                    </ul>
                </div>
            </div>
        `;
    }

    /**
     * Terapkan Filter & Pencarian
     */
    function applyFilters() {
        const query = searchQuery.trim().toLowerCase();

        filteredData = rawData.filter(function(item) {
            // Filter Bidang
            if (filterBidang !== 'all') {
                const itemBidang = String(item.nama_bidang || '').trim().toLowerCase();
                if (itemBidang !== filterBidang.trim().toLowerCase()) {
                    return false;
                }
            }

            // Filter Status
            if (filterStatus !== 'all') {
                const itemStatus = String(item.status_verifikasi || '').trim().toLowerCase();
                const filterLower = filterStatus.trim().toLowerCase();
                
                if (filterLower === 'ditolak') {
                    if (!['ditolak', 'revisi', 'gagal', 'dibatalkan'].includes(itemStatus)) {
                        return false;
                    }
                } else if (filterLower === 'selesai') {
                    if (!['selesai', 'disetujui', 'terverifikasi', 'lolos evaluasi'].includes(itemStatus)) {
                        return false;
                    }
                } else if (filterLower === 'dalam proses') {
                    if (!['proses', 'dalam_proses', 'dalam proses', 'sedang diproses'].includes(itemStatus)) {
                        return false;
                    }
                } else if (itemStatus !== filterLower) {
                    return false;
                }
            }

            // Pencarian (berdasarkan nomor arsip, nama arsip, bidang, atau kode klasifikasi)
            if (query !== '') {
                const nomor = String(item.nomor_arsip || '').toLowerCase();
                const nama = String(item.nama_arsip || '').toLowerCase();
                const bidang = String(item.nama_bidang || '').toLowerCase();
                const kode = String(item.kode_klasifikasi_text || '').toLowerCase();

                return nomor.includes(query) || nama.includes(query) || bidang.includes(query) || kode.includes(query);
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
            let valA = a[key];
            let valB = b[key];

            // Handle skor prioritas (numeric sort)
            if (key === 'skor_prioritas') {
                const numA = (valA !== null && valA !== undefined && valA !== '') ? parseFloat(valA) : -1;
                const numB = (valB !== null && valB !== undefined && valB !== '') ? parseFloat(valB) : -1;
                return dir === 'asc' ? numA - numB : numB - numA;
            }

            valA = (valA !== null && valA !== undefined) ? String(valA).toLowerCase() : '';
            valB = (valB !== null && valB !== undefined) ? String(valB).toLowerCase() : '';

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
        const totalPages = Math.ceil(totalRows / perPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        const startIndex = (currentPage - 1) * perPage;
        const endIndex = Math.min(startIndex + perPage, totalRows);
        const pageData = filteredData.slice(startIndex, endIndex);

        tableBody.innerHTML = '';

        if (totalRows === 0) {
            tableElement.parentElement.classList.add('d-none');
            emptyState.classList.remove('d-none');

            // Cek apakah kondisi empty karena filter/search atau memang belum ada data
            const isFiltering = (searchQuery.trim() !== '' || filterBidang !== 'all' || filterStatus !== 'all');
            if (isFiltering && rawData.length > 0) {
                emptyTitle.textContent = 'Data Arsip Tidak Ditemukan';
                emptyDesc.textContent = 'Tidak ada arsip yang cocok dengan kata kunci atau filter yang Anda pilih.';
                btnResetFilter.classList.remove('d-none');
                if (emptyBtnTambahArsip) emptyBtnTambahArsip.classList.add('d-none');
            } else {
                emptyTitle.textContent = 'Belum ada arsip';
                emptyDesc.textContent = 'Belum ada arsip yang tersedia untuk dikelola.';
                btnResetFilter.classList.add('d-none');
                if (emptyBtnTambahArsip) emptyBtnTambahArsip.classList.remove('d-none');
            }

            paginationInfo.textContent = 'Menampilkan 0 – 0 dari 0 data';
            paginationControls.innerHTML = '';
            return;
        }

        tableElement.parentElement.classList.remove('d-none');
        emptyState.classList.add('d-none');

        // Render Baris Data
        pageData.forEach(function(row, idx) {
            const rowNumber = startIndex + idx + 1;
            const detailUrl = baseDetailUrl + '/' + encodeURIComponent(row.id_arsip);
            const tr = document.createElement('tr');

            tr.innerHTML = `
                <td class="col-no">${rowNumber}</td>
                <td class="col-nomor-arsip">
                    <a href="${detailUrl}" class="arsip-table-link" title="Buka Detail Arsip">
                        ${escapeHtml(row.nomor_arsip || '-')}
                    </a>
                </td>
                <td class="col-nama-arsip">
                    <div class="arsip-nama-text">
                        ${escapeHtml(row.nama_arsip || '-')}
                    </div>
                </td>
                <td class="col-kode-klasifikasi text-center">
                    ${renderKodeKlasifikasi(row.kode_klasifikasi_text, row.nama_klasifikasi)}
                </td>
                <td class="col-bidang">
                    <div class="arsip-bidang-text">
                        ${escapeHtml(row.nama_bidang || '-')}
                    </div>
                </td>
                <td class="col-skor-prioritas text-center">
                    ${renderSkorPrioritas(row.skor_prioritas, row.nama_klasifikasi)}
                </td>
                <td class="col-status-verifikasi text-center">
                    ${renderStatusVerifikasiBadge(row.status_verifikasi)}
                </td>
                <td class="col-berkas-file text-center">
                    ${renderBerkasFile(row.file_arsip)}
                </td>
                <td class="col-aksi text-center">
                    ${renderAksi(row)}
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
            singleBtn.className = 'arsip-page-btn active';
            singleBtn.textContent = '1';
            paginationControls.appendChild(singleBtn);
            return;
        }

        // Tombol Sebelumnya (<)
        const prevBtn = document.createElement('button');
        prevBtn.type = 'button';
        prevBtn.className = 'arsip-page-btn' + (currentPage === 1 ? ' disabled' : '');
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
                pageBtn.className = 'arsip-page-btn' + (i === currentPage ? ' active' : '');
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
                ellipsis.className = 'arsip-page-ellipsis';
                ellipsis.textContent = '...';
                paginationControls.appendChild(ellipsis);
            }
        }

        // Tombol Berikutnya (>)
        const nextBtn = document.createElement('button');
        nextBtn.type = 'button';
        nextBtn.className = 'arsip-page-btn' + (currentPage === totalPages ? ' disabled' : '');
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

    // =========================================================================
    // Event Listeners
    // =========================================================================

    // Input Pencarian
    if (searchInput) {
        searchInput.addEventListener('input', function(e) {
            searchQuery = e.target.value;
            if (searchQuery.trim().length > 0) {
                clearSearchBtn.classList.remove('d-none');
            } else {
                clearSearchBtn.classList.add('d-none');
            }
            applyFilters();
        });
    }

    // Tombol Bersihkan Pencarian (x)
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            searchQuery = '';
            clearSearchBtn.classList.add('d-none');
            searchInput.focus();
            applyFilters();
        });
    }

    // Filter Bidang
    if (selectBidang) {
        selectBidang.addEventListener('change', function(e) {
            filterBidang = e.target.value;
            applyFilters();
        });
    }

    // Filter Status
    if (selectStatus) {
        selectStatus.addEventListener('change', function(e) {
            filterStatus = e.target.value;
            applyFilters();
        });
    }

    // Tombol Reset Filter & Pencarian
    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', function() {
            searchQuery = '';
            filterBidang = 'all';
            filterStatus = 'all';
            if (searchInput) searchInput.value = '';
            if (clearSearchBtn) clearSearchBtn.classList.add('d-none');
            if (selectBidang) selectBidang.value = 'all';
            if (selectStatus) selectStatus.value = 'all';
            currentSort.key = null;
            currentSort.direction = 'asc';

            sortHeaders.forEach(function(th) {
                th.classList.remove('active-sort');
                const icon = th.querySelector('.arsip-sort-icon');
                if (icon) {
                    icon.className = 'bi bi-arrow-down-up arsip-sort-icon';
                }
            });

            applyFilters();
        });
    }

    // Pengurutan Kolom (Sorting Headers)
    sortHeaders.forEach(function(th) {
        th.addEventListener('click', function() {
            const sortKey = th.getAttribute('data-sort-key');
            if (!sortKey) return;

            if (currentSort.key === sortKey) {
                currentSort.direction = currentSort.direction === 'asc' ? 'desc' : 'asc';
            } else {
                currentSort.key = sortKey;
                currentSort.direction = 'asc';
            }

            // Update Icon UI
            sortHeaders.forEach(function(item) {
                item.classList.remove('active-sort');
                const icon = item.querySelector('.arsip-sort-icon');
                if (icon) {
                    icon.className = 'bi bi-arrow-down-up arsip-sort-icon';
                }
            });

            th.classList.add('active-sort');
            const activeIcon = th.querySelector('.arsip-sort-icon');
            if (activeIcon) {
                activeIcon.className = currentSort.direction === 'asc' 
                    ? 'bi bi-arrow-up arsip-sort-icon' 
                    : 'bi bi-arrow-down arsip-sort-icon';
            }

            applyFilters();
        });
    });

    // Inisialisasi Pertama
    applyFilters();
});
</script>
<?= $this->endSection() ?>
