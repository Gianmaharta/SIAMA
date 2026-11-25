<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
<?= ($user_role === 'Arsiparis') ? 'Tugas Alih Media - SIAMA' : 'Surat Perintah Tugas - SIAMA' ?>
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/spt-master.css?v=' . (file_exists(FCPATH . 'css/spt-master.css') ? filemtime(FCPATH . 'css/spt-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman -->
<div class="spt-page-header">
    <h1 class="spt-page-title"><?= ($user_role === 'Arsiparis') ? 'Tugas Alih Media' : 'Surat Perintah Tugas' ?></h1>
    <p class="spt-page-subtitle"><?= ($user_role === 'Arsiparis') ? 'Kelola dan tindak lanjuti penugasan alih media arsip yang ditugaskan kepada Anda.' : 'Kelola surat perintah tugas alih media arsip.' ?></p>
</div>

<!-- Kotak Informasi / Empat Kartu Statistik SPT -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Kartu 1: Total Semua SPT / Total Tugas -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="spt-stat-card">
            <div class="spt-stat-icon-wrapper bg-icon-blue">
                <i class="bi bi-file-earmark-text-fill"></i>
            </div>
            <div class="spt-stat-body">
                <span class="spt-stat-title"><?= ($user_role === 'Arsiparis') ? 'Total Tugas' : 'Total Semua SPT' ?></span>
                <div class="spt-stat-number-wrap">
                    <span class="spt-stat-number" id="statTotalSpt"><?= number_format($totalSpt ?? 0, 0, ',', '.') ?></span>
                    <span class="spt-stat-unit"><?= ($user_role === 'Arsiparis') ? 'Tugas' : 'SPT' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 2: Belum Ditugaskan / Belum Dikerjakan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="spt-stat-card">
            <div class="spt-stat-icon-wrapper bg-icon-orange">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="spt-stat-body">
                <span class="spt-stat-title"><?= ($user_role === 'Arsiparis') ? 'Belum Dikerjakan' : 'Belum Ditugaskan' ?></span>
                <div class="spt-stat-number-wrap">
                    <span class="spt-stat-number" id="statBelumDitugaskan"><?= number_format($belumDitugaskan ?? 0, 0, ',', '.') ?></span>
                    <span class="spt-stat-unit"><?= ($user_role === 'Arsiparis') ? 'Tugas' : 'SPT' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 3: Sedang Ditugaskan / Sedang Diproses -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="spt-stat-card">
            <div class="spt-stat-icon-wrapper bg-icon-indigo">
                <i class="bi bi-person-check-fill"></i>
            </div>
            <div class="spt-stat-body">
                <span class="spt-stat-title"><?= ($user_role === 'Arsiparis') ? 'Sedang Diproses' : 'Sedang Ditugaskan' ?></span>
                <div class="spt-stat-number-wrap">
                    <span class="spt-stat-number" id="statDitugaskan"><?= number_format($ditugaskan ?? 0, 0, ',', '.') ?></span>
                    <span class="spt-stat-unit"><?= ($user_role === 'Arsiparis') ? 'Tugas' : 'SPT' ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 4: Selesai Dilaksanakan / Selesai Dikerjakan -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="spt-stat-card">
            <div class="spt-stat-icon-wrapper bg-icon-green">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="spt-stat-body">
                <span class="spt-stat-title"><?= ($user_role === 'Arsiparis') ? 'Selesai Dikerjakan' : 'Selesai Dilaksanakan' ?></span>
                <div class="spt-stat-number-wrap">
                    <span class="spt-stat-number" id="statSelesai"><?= number_format($selesai ?? 0, 0, ',', '.') ?></span>
                    <span class="spt-stat-unit"><?= ($user_role === 'Arsiparis') ? 'Tugas' : 'SPT' ?></span>
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
                placeholder="<?= ($user_role === 'Arsiparis') ? 'Cari nomor SPT atau uraian pekerjaan...' : 'Cari nomor SPT atau perihal...' ?>"
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

            <!-- Filter Penugasan / Status -->
            <div class="spt-filter-wrap">
                <label for="sptPenugasanFilter" class="spt-filter-label">Status</label>
                <select id="sptPenugasanFilter" class="spt-filter-select" aria-label="Filter Status">
                    <option value="all" selected>Semua Status</option>
                    <?php if ($user_role === 'Arsiparis') : ?>
                        <option value="belum_dimulai">Belum Dikerjakan</option>
                        <option value="sedang_diproses">Sedang Diproses</option>
                        <option value="selesai">Selesai</option>
                    <?php else : ?>
                        <option value="belum_ditugaskan">Belum Ditugaskan</option>
                        <option value="ditugaskan">Ditugaskan</option>
                        <option value="selesai">Selesai</option>
                    <?php endif; ?>
                </select>
            </div>

            <?php if (in_array(session()->get('nama_role'), ['Pimpinan', 'Kepala_Dinas', 'Kepala Dinas'])) : ?>
                <button type="button" class="spt-btn-create btn-toolbar-primary ms-sm-1" data-bs-toggle="modal" data-bs-target="#modalTerbitkanSpt">
                    <i class="bi bi-plus-lg"></i>
                    <span>Terbitkan SPT</span>
                </button>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tabel Surat Perintah Tugas / Tugas Alih Media -->
    <div class="table-responsive spt-table-responsive">
        <table class="table spt-table align-middle" id="sptTable">
            <thead>
                <?php if ($user_role === 'Arsiparis') : ?>
                    <tr>
                        <th scope="col" class="col-no">NO</th>
                        <th scope="col" class="col-nomor-spt sortable-th" data-sort-key="nomor_spt" title="Klik untuk mengurutkan berdasarkan Nomor SPT">
                            NOMOR SPT <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                        </th>
                        <th scope="col" class="col-tanggal-penugasan sortable-th" data-sort-key="tanggal_penugasan" title="Klik untuk mengurutkan berdasarkan Tanggal Penugasan">
                            TANGGAL PENUGASAN <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                        </th>
                        <th scope="col" class="col-perihal sortable-th" data-sort-key="perihal" title="Klik untuk mengurutkan berdasarkan Uraian Pekerjaan">
                            URAIAN PEKERJAAN <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                        </th>
                        <th scope="col" class="col-jumlah-arsip text-center sortable-th" data-sort-key="jumlah_arsip" title="Klik untuk mengurutkan berdasarkan Jumlah Arsip">
                            JUMLAH ARSIP <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                        </th>
                        <th scope="col" class="col-status-pekerjaan text-center sortable-th" data-sort-key="status_pekerjaan" title="Klik untuk mengurutkan berdasarkan Status Pekerjaan">
                            STATUS PEKERJAAN <i class="bi bi-arrow-down-up spt-sort-icon"></i>
                        </th>
                        <th scope="col" class="col-aksi text-center">AKSI</th>
                    </tr>
                <?php else : ?>
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
                <?php endif; ?>
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

<!-- Modal PDF Viewer Bootstrap 5 -->
<div class="modal fade" id="modalPdfViewerIndex" tabindex="-1" aria-labelledby="modalPdfViewerIndexLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="height: 90vh;">
        <div class="modal-content h-100 border-0 rounded-4 shadow-lg overflow-hidden">
            <div class="modal-header bg-light border-bottom px-4 py-3">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-file-earmark-pdf-fill text-danger fs-5"></i>
                    <h5 class="modal-title fw-bold text-dark fs-6" id="modalPdfViewerIndexLabel">
                        Dokumen SPT: <span id="pdfViewerNomorSpt"></span>
                    </h5>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <a href="#" id="pdfViewerDownloadBtn" target="_blank" download class="btn btn-outline-secondary btn-sm rounded-3">
                        <i class="bi bi-download me-1"></i> Unduh
                    </a>
                    <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal" aria-label="Tutup"></button>
                </div>
            </div>
            <div class="modal-body p-0 h-100 bg-secondary-subtle">
                <iframe id="pdfViewerFrame" src="" class="w-100 h-100 border-0" title="Viewer Dokumen SPT"></iframe>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Fungsi Global untuk Viewer PDF SPT dari tabel
function viewSptPdf(nomorSpt, fileUrl) {
    document.getElementById('pdfViewerNomorSpt').textContent = nomorSpt;
    document.getElementById('pdfViewerDownloadBtn').href = fileUrl;
    document.getElementById('pdfViewerFrame').src = fileUrl;
    const modalElement = document.getElementById('modalPdfViewerIndex');
    const modal = bootstrap.Modal.getOrCreateInstance(modalElement);
    modal.show();
}

document.addEventListener('DOMContentLoaded', function() {
    // Data dari Controller CI4
    const rawData = <?= json_encode($spt_list ?? [], JSON_UNESCAPED_SLASHES) ?>;
    const baseDetailUrl = "<?= base_url('/spt/detail') ?>";
    const baseAssignUrl = "<?= base_url('/spt/assign') ?>";
    const baseArsipCreateUrl = "<?= base_url('/arsip/create') ?>";
    const baseFileUrl   = "<?= base_url('uploads/spt') ?>";
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
            if (currentUserRole === 'Arsiparis') {
                const sp = String(item.status_pekerjaan || '').trim().toLowerCase();
                if (sp === 'selesai') {
                    selesai++;
                } else if (sp === 'sedang diproses') {
                    tugas++;
                } else {
                    belum++;
                }
            } else {
                const s = String(item.status_penugasan || '').trim().toLowerCase();
                if (s === 'selesai') {
                    selesai++;
                } else if (s === 'ditugaskan' || s === 'proses' || s === 'dalam proses') {
                    tugas++;
                } else {
                    belum++;
                }
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
     * Format Tanggal & Jam (WITA)
     */
    function formatDateTime(dtStr) {
        if (!dtStr) return '-';
        const d = new Date(dtStr.replace(/-/g, '/'));
        if (isNaN(d.getTime())) {
            return escapeHtml(dtStr.substring(0, 16));
        }
        const months = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        const day = String(d.getDate()).padStart(2, '0');
        const month = months[d.getMonth()];
        const year = d.getFullYear();
        const hours = String(d.getHours()).padStart(2, '0');
        const mins = String(d.getMinutes()).padStart(2, '0');
        return `${day} ${month} ${year}, ${hours}:${mins} WITA`;
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
     * Render Badge Status Penugasan (Kepala Bidang / Umum)
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
     * Render Badge Status Pekerjaan (Role Arsiparis)
     */
    function renderStatusPekerjaanBadge(status) {
        const val = String(status || '').trim().toLowerCase();

        if (val === 'selesai') {
            return `
                <span class="spt-badge spt-badge-success">
                    <span class="spt-badge-dot"></span>
                    Selesai
                </span>
            `;
        } else if (val === 'sedang diproses' || val === 'proses') {
            return `
                <span class="spt-badge spt-badge-info">
                    <span class="spt-badge-dot"></span>
                    Sedang Diproses
                </span>
            `;
        } else {
            return `
                <span class="spt-badge spt-badge-warning">
                    <span class="spt-badge-dot"></span>
                    Belum Dimulai
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
            // Filter Penugasan / Status
            if (filterPenugasan !== 'all') {
                if (currentUserRole === 'Arsiparis') {
                    const itemStatusPekerjaan = String(item.status_pekerjaan || '').toLowerCase();
                    if (filterPenugasan === 'belum_dimulai' && itemStatusPekerjaan !== 'belum dimulai') {
                        return false;
                    }
                    if (filterPenugasan === 'sedang_diproses' && itemStatusPekerjaan !== 'sedang diproses') {
                        return false;
                    }
                    if (filterPenugasan === 'selesai' && itemStatusPekerjaan !== 'selesai') {
                        return false;
                    }
                } else {
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
            // Handle sorting untuk tanggal
            if (key === 'tanggal_spt' || key === 'tanggal_mulai' || key === 'tanggal_penugasan') {
                const dateA = new Date(String(a[key] || '').replace(/-/g, '/')).getTime() || 0;
                const dateB = new Date(String(b[key] || '').replace(/-/g, '/')).getTime() || 0;
                return dir === 'asc' ? dateA - dateB : dateB - dateA;
            }

            // Handle sorting untuk jumlah arsip
            if (key === 'jumlah_arsip') {
                const countA = parseInt(a.jumlah_arsip || 0, 10);
                const countB = parseInt(b.jumlah_arsip || 0, 10);
                return dir === 'asc' ? countA - countB : countB - countA;
            }

            let valA = (a[key] !== null && a[key] !== undefined) ? String(a[key]).toLowerCase() : '';
            let valB = (b[key] !== null && b[key] !== undefined) ? String(b[key]).toLowerCase() : '';

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

        // Render Baris Data Sesuai Role
        pageData.forEach(function(row, idx) {
            const rowNumber = startIndex + idx + 1;
            const detailUrl = baseDetailUrl + '/' + encodeURIComponent(row.id_spt);
            const assignUrl = baseAssignUrl + '/' + encodeURIComponent(row.id_spt);
            const tr = document.createElement('tr');

            const isArsiparis = (currentUserRole === 'Arsiparis');
            const statusPenugasanLower = String(row.status_penugasan || '').trim().toLowerCase();
            const isBelumDitugaskan = !statusPenugasanLower || statusPenugasanLower === 'belum_ditugaskan' || statusPenugasanLower === 'pending';
            const canAssign = ['Kepala_Bidang', 'Kepala Bidang', 'Kabid'].includes(currentUserRole);

            if (isArsiparis) {
                // Tampilan Baris Khusus Role Arsiparis (Halaman Tugas Alih Media)
                const isBelumMulai = (row.status_pekerjaan === 'Belum Dimulai');

                let actionHtml = `<div class="spt-action-group">`;
                if (isBelumMulai) {
                    actionHtml += `
                        <a href="${baseArsipCreateUrl}?id_spt=${encodeURIComponent(row.id_spt)}"
                           class="spt-action-btn spt-btn-assign spt-icon-only"
                           style="background-color: #1E60EC; color: #FFFFFF;"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           data-bs-title="Mulai Alih Media"
                           aria-label="Mulai Alih Media">
                            <i class="bi bi-folder-plus"></i>
                        </a>
                    `;
                }
                actionHtml += `
                    <a href="${detailUrl}"
                       class="spt-action-btn spt-btn-detail spt-icon-only"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       data-bs-title="Lihat Detail SPT"
                       aria-label="Lihat Detail SPT">
                        <i class="bi bi-eye"></i>
                    </a>
                `;
                if (row.file_spt && String(row.file_spt).trim() !== '') {
                    const fileUrl = baseFileUrl + '/' + encodeURIComponent(row.file_spt);
                    const safeNomorSpt = escapeHtml(row.nomor_spt || 'SPT').replace(/'/g, "\\'");
                    actionHtml += `
                        <button type="button"
                                class="spt-action-btn spt-btn-doc spt-icon-only"
                                onclick="viewSptPdf('${safeNomorSpt}', '${fileUrl}')"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                data-bs-title="Lihat Dokumen SPT"
                                aria-label="Lihat Dokumen SPT">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </button>
                    `;
                }
                actionHtml += `</div>`;

                let kabidInfo = '';
                if (row.nama_kabid) {
                    kabidInfo = `<div class="text-muted small mt-0.5" style="font-size: 11px;"><i class="bi bi-person me-1"></i>Oleh: ${escapeHtml(row.nama_kabid)}</div>`;
                }

                tr.innerHTML = `
                    <td class="col-no">${rowNumber}</td>
                    <td class="col-nomor-spt">
                        <a href="${detailUrl}" class="spt-link fw-semibold" title="Buka Detail SPT">
                            ${escapeHtml(row.nomor_spt)}
                        </a>
                    </td>
                    <td class="col-tanggal-penugasan">
                        <div class="spt-date-text fw-medium">${formatDateTime(row.tanggal_penugasan)}</div>
                        ${kabidInfo}
                    </td>
                    <td class="col-perihal">
                        <div class="spt-perihal-text fw-medium text-dark" title="${escapeHtml(row.perihal)}">
                            ${escapeHtml(row.perihal || 'Tugas Alih Media Arsip')}
                        </div>
                        <div class="text-muted small" style="font-size: 11px;">
                            Periode: ${formatPeriode(row.tanggal_mulai, row.tanggal_selesai)}
                        </div>
                    </td>
                    <td class="col-jumlah-arsip text-center">
                        <span class="badge bg-light text-secondary border px-2.5 py-1 rounded-pill fw-medium" style="font-size: 11.5px;">
                            ${parseInt(row.jumlah_arsip || 0, 10)} Arsip
                        </span>
                    </td>
                    <td class="col-status-pekerjaan text-center">
                        ${renderStatusPekerjaanBadge(row.status_pekerjaan)}
                    </td>
                    <td class="col-aksi text-center">
                        ${actionHtml}
                    </td>
                `;
            } else {
                // Tampilan Baris Role Kepala Bidang / Pimpinan / Admin OPD
                let actionHtml = `<div class="spt-action-group">`;
                if (canAssign && isBelumDitugaskan) {
                    actionHtml += `
                        <a href="${assignUrl}"
                           class="spt-action-btn spt-btn-assign spt-icon-only"
                           data-bs-toggle="tooltip"
                           data-bs-placement="top"
                           data-bs-title="Tugaskan Arsiparis"
                           aria-label="Tugaskan Arsiparis">
                            <i class="bi bi-person-plus"></i>
                        </a>
                    `;
                }
                actionHtml += `
                    <a href="${detailUrl}"
                       class="spt-action-btn spt-btn-detail spt-icon-only"
                       data-bs-toggle="tooltip"
                       data-bs-placement="top"
                       data-bs-title="Lihat Detail SPT"
                       aria-label="Lihat Detail SPT">
                        <i class="bi bi-eye"></i>
                    </a>
                `;
                if (row.file_spt && String(row.file_spt).trim() !== '') {
                    const fileUrl = baseFileUrl + '/' + encodeURIComponent(row.file_spt);
                    const safeNomorSpt = escapeHtml(row.nomor_spt || 'SPT').replace(/'/g, "\\'");
                    actionHtml += `
                        <button type="button"
                                class="spt-action-btn spt-btn-doc spt-icon-only"
                                onclick="viewSptPdf('${safeNomorSpt}', '${fileUrl}')"
                                data-bs-toggle="tooltip"
                                data-bs-placement="top"
                                data-bs-title="Lihat Dokumen SPT"
                                aria-label="Lihat Dokumen SPT">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </button>
                    `;
                }
                actionHtml += `</div>`;

                tr.innerHTML = `
                    <td class="col-no">${rowNumber}</td>
                    <td class="col-nomor-spt">
                        <a href="${detailUrl}" class="spt-link fw-semibold" title="Buka Detail SPT">
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
            }

            tableBody.appendChild(tr);
        });

        // Informasi Pagination
        const displayStart = totalRows > 0 ? startIndex + 1 : 0;
        paginationInfo.textContent = `Menampilkan ${displayStart} – ${endIndex} dari ${totalRows} data`;

        // Render Navigasi Pagination
        renderPagination(totalPages);

        // Inisialisasi ulang Bootstrap 5 Tooltip pada setiap render (termasuk AJAX / filter / pagination)
        initSptActionTooltips();
    }

    /**
     * Inisialisasi Bootstrap 5 Tooltip untuk tombol kolom AKSI
     * container: 'body' mencegah tooltip terpotong oleh table-responsive overflow
     */
    function initSptActionTooltips() {
        if (typeof bootstrap === 'undefined' || !bootstrap.Tooltip) return;
        document.querySelectorAll('.tooltip.bs-tooltip-auto, .tooltip.bs-tooltip-top').forEach(t => t.remove());
        const triggers = tableBody.querySelectorAll('[data-bs-toggle="tooltip"]');
        triggers.forEach(el => {
            const existing = bootstrap.Tooltip.getInstance(el);
            if (existing) existing.dispose();
            new bootstrap.Tooltip(el, {
                container: 'body',
                boundary: 'window',
                trigger: 'hover focus'
            });
        });
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
