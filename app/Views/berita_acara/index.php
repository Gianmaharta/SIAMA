<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Daftar Berita Acara - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/berita-acara-master.css?v=' . (file_exists(FCPATH . 'css/berita-acara-master.css') ? filemtime(FCPATH . 'css/berita-acara-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman -->
<div class="ba-page-header">
    <h1 class="ba-page-title">Daftar Berita Acara</h1>
    <p class="ba-page-subtitle">Kelola dan pantau seluruh berita acara hasil alih media arsip pada setiap OPD.</p>
</div>

<!-- Notifikasi Alert -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div><?= session()->getFlashdata('success') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Card Utama Daftar Berita Acara -->
<div class="ba-card">
    <!-- Toolbar Filter & Pencarian -->
    <form method="get" action="<?= base_url('/berita-acara') ?>" id="baFilterForm" class="ba-toolbar">
            <!-- Search Input -->
            <div class="ba-search-wrap">
                <i class="bi bi-search ba-search-icon"></i>
                <input 
                    type="text" 
                    name="search" 
                    id="baSearchInput" 
                    class="form-control ba-search-input" 
                    placeholder="Cari nomor BA, pelaksana, nomor SPT..." 
                    value="<?= esc($filters['search'] ?? '') ?>"
                    autocomplete="off"
                >
            </div>

            <!-- Filter Tanggal & Status -->
            <div class="ba-filter-group">
                <!-- Date Picker -->
                <div class="ba-date-input-wrap">
                    <input 
                        type="date" 
                        name="tanggal" 
                        id="baDateInput" 
                        class="form-control ba-date-input" 
                        value="<?= esc($filters['tanggal'] ?? '') ?>"
                        title="Pilih tanggal Berita Acara"
                    >
                </div>

                <!-- Dropdown Status Persetujuan -->
                <div class="ba-status-filter-wrap">
                    <label for="baStatusSelect" class="ba-filter-label mb-0">Status Persetujuan</label>
                    <select name="status" id="baStatusSelect" class="form-select ba-status-select">
                        <option value="">Semua</option>
                        <option value="Draf_Kabid" <?= (($filters['status'] ?? '') === 'Draf_Kabid') ? 'selected' : '' ?>>Draf Kabid</option>
                        <option value="Draf_Pimpinan" <?= (($filters['status'] ?? '') === 'Draf_Pimpinan') ? 'selected' : '' ?>>Draf Pimpinan</option>
                        <option value="Selesai" <?= (($filters['status'] ?? '') === 'Selesai') ? 'selected' : '' ?>>Selesai</option>
                        <option value="Revisi" <?= (($filters['status'] ?? '') === 'Revisi') ? 'selected' : '' ?>>Revisi</option>
                    </select>
                </div>

                <!-- Tombol Buat Berita Acara (CTA) -->
                <?php if (in_array(session()->get('nama_role'), ['Arsiparis', 'Admin_Pemkab', 'Admin_OPD'])) : ?>
                    <a href="<?= base_url('/berita-acara/create') ?>" class="btn ba-btn-add" id="btnTambahBA" title="Buat Draf Berita Acara Baru">
                        <i class="bi bi-plus-lg"></i>
                        <span>Buat Berita Acara</span>
                    </a>
                <?php endif; ?>
            </div>
        </form>

    <!-- Tabel Berita Acara -->
    <div class="ba-table-responsive">
        <table class="table ba-table" id="baTable">
            <thead>
                <tr>
                    <th class="ba-col-no text-center">NO</th>
                    <th class="sortable-th" data-column="1">NOMOR BA <span class="ba-sort-arrows">↑↓</span></th>
                    <th class="sortable-th" data-column="2">TANGGAL <span class="ba-sort-arrows">↑↓</span></th>
                    <th class="sortable-th" data-column="3">NOMOR SPT <span class="ba-sort-arrows">↑↓</span></th>
                    <th class="sortable-th" data-column="4">PELAKSANA <span class="ba-sort-arrows">↑↓</span></th>
                    <th class="sortable-th text-center" data-column="5">STATUS PERSETUJUAN <span class="ba-sort-arrows">↑↓</span></th>
                    <th class="text-center" style="width: 170px;">AKSI</th>
                </tr>
            </thead>
            <tbody id="baTableBody">
                <?php if (!empty($berita_acara)) : ?>
                    <?php $no = 1; foreach ($berita_acara as $row) : ?>
                        <?php
                            // Parsing Pelaksana & Role
                            $namaPelaksana = $row['nama_pelaksana'] ?? '-';
                            $rolePelaksana = '';
                            if (preg_match('/^(.*?)\s*\((.*?)\)$/', $namaPelaksana, $m)) {
                                $displayName = trim($m[1]);
                                $rolePelaksana = '(' . trim($m[2]) . ')';
                            } else {
                                $displayName = $namaPelaksana;
                                $rolePelaksana = '(Arsiparis)';
                            }

                            // Normalisasi Status Badge
                            $rawStatus = $row['status_persetujuan'] ?? 'Draf_Kabid';
                            $statusNormalized = str_replace('_', ' ', $rawStatus);
                            $badgeClass = 'ba-badge-draf-kabid';
                            $iconClass = 'bi-clock';

                            if ($rawStatus === 'Selesai') {
                                $badgeClass = 'ba-badge-selesai';
                                $iconClass = 'bi-check2';
                            } elseif ($rawStatus === 'Draf_Pimpinan') {
                                $badgeClass = 'ba-badge-draf-pimpinan';
                                $iconClass = 'bi-clock';
                            } elseif ($rawStatus === 'Revisi') {
                                $badgeClass = 'ba-badge-revisi';
                                $iconClass = 'bi-arrow-repeat';
                            }
                        ?>
                        <tr class="ba-data-row" 
                            data-nomor="<?= strtolower(esc($row['nomor_ba'])) ?>"
                            data-pelaksana="<?= strtolower(esc($row['nama_pelaksana'] ?? '')) ?>"
                            data-spt="<?= strtolower(esc($row['nomor_spt'] ?? '')) ?>"
                            data-tanggal="<?= esc($row['tanggal_ba']) ?>"
                            data-status="<?= esc($row['status_persetujuan']) ?>"
                        >
                            <td class="text-center ba-col-no"><?= $no++ ?></td>
                            <td>
                                <a href="<?= base_url('/berita-acara/detail/' . $row['id_berita_acara']) ?>" class="ba-nomor-ba-link">
                                    <?= esc($row['nomor_ba']) ?>
                                </a>
                            </td>
                            <td class="ba-tanggal"><?= esc($row['tanggal_ba']) ?></td>
                            <td class="ba-nomor-spt <?= !empty($row['nomor_spt']) ? 'has-spt' : '' ?>">
                                <?= !empty($row['nomor_spt']) ? esc($row['nomor_spt']) : '-' ?>
                            </td>
                            <td>
                                <div class="ba-pelaksana-wrap">
                                    <span class="ba-pelaksana-name"><?= esc($displayName) ?></span>
                                    <span class="ba-pelaksana-role"><?= esc($rolePelaksana) ?></span>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="ba-status-badge <?= $badgeClass ?>">
                                    <i class="bi <?= $iconClass ?>"></i>
                                    <span><?= esc($statusNormalized) ?></span>
                                </span>
                            </td>
                            <td class="text-center">
                                <div class="ba-action-group justify-content-center">
                                    <a href="<?= base_url('/berita-acara/detail/' . $row['id_berita_acara']) ?>" class="btn-ba-action btn-ba-detail" title="Lihat Detail Berita Acara">
                                        <i class="bi bi-file-earmark-text"></i>
                                        <span>Detail</span>
                                    </a>
                                    <?php 
                                        $userRole = session()->get('nama_role') ?? session()->get('role');
                                        if (in_array($rawStatus, ['Draf_Kabid', 'Revisi']) && in_array($userRole, ['Arsiparis', 'Admin_Pemkab', 'Admin_OPD'])) : 
                                    ?>
                                        <a href="<?= base_url('/berita-acara/edit/' . $row['id_berita_acara']) ?>" class="btn-ba-action btn-ba-edit" title="Edit Draf Berita Acara">
                                            <i class="bi bi-pencil-square"></i>
                                            <span>Edit</span>
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($rawStatus === 'Selesai' || $rawStatus === 'Draf_Pimpinan' || $rawStatus === 'Draf_Kabid') : ?>
                                        <a href="<?= base_url('/berita-acara/cetak/' . $row['id_berita_acara']) ?>" target="_blank" class="btn-ba-action btn-ba-print" title="Cetak Berita Acara (PDF)">
                                            <i class="bi bi-printer"></i>
                                            <span>Cetak</span>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr id="baEmptyDbRow">
                        <td colspan="7">
                            <div class="ba-empty-state">
                                <div class="ba-empty-icon">
                                    <i class="bi bi-folder2-open"></i>
                                </div>
                                <div class="ba-empty-title">Belum Ada Berita Acara</div>
                                <div class="ba-empty-desc">Data berita acara hasil alih media arsip belum tersedia atau belum dibuat.</div>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
                <tr id="baNoResultsRow" class="d-none">
                    <td colspan="7">
                        <div class="ba-empty-state">
                            <div class="ba-empty-icon">
                                <i class="bi bi-search"></i>
                            </div>
                            <div class="ba-empty-title">Data Tidak Ditemukan</div>
                            <div class="ba-empty-desc">Tidak ada berita acara yang cocok dengan kriteria pencarian dan filter Anda.</div>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Footer & Pagination -->
    <?php $totalData = count($berita_acara); ?>
    <div class="ba-card-footer">
        <div class="ba-footer-info" id="baFooterInfo">
            Menampilkan <?= $totalData > 0 ? "1 – {$totalData}" : '0' ?> dari <?= $totalData ?> data
        </div>
        <nav aria-label="Navigasi Halaman Berita Acara">
            <ul class="ba-pagination" id="baPagination">
                <li>
                    <button type="button" class="ba-page-btn disabled" id="baPrevBtn" aria-label="Halaman Sebelumnya">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                </li>
                <li>
                    <button type="button" class="ba-page-btn active" id="baPageNum">1</button>
                </li>
                <li>
                    <button type="button" class="ba-page-btn disabled" id="baNextBtn" aria-label="Halaman Berikutnya">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </li>
            </ul>
        </nav>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('baSearchInput');
    const dateInput = document.getElementById('baDateInput');
    const statusSelect = document.getElementById('baStatusSelect');
    const tableBody = document.getElementById('baTableBody');
    const rows = Array.from(document.querySelectorAll('.ba-data-row'));
    const noResultsRow = document.getElementById('baNoResultsRow');
    const footerInfo = document.getElementById('baFooterInfo');
    const totalData = rows.length;

    // Realtime Client-side Filter Function
    function applyFilters() {
        const query = searchInput.value.toLowerCase().trim();
        const dateVal = dateInput.value.trim();
        const statusVal = statusSelect.value.trim();

        let visibleCount = 0;

        rows.forEach(function(row) {
            const nomor = row.getAttribute('data-nomor') || '';
            const pelaksana = row.getAttribute('data-pelaksana') || '';
            const spt = row.getAttribute('data-spt') || '';
            const rowDate = row.getAttribute('data-tanggal') || '';
            const rowStatus = row.getAttribute('data-status') || '';

            const matchQuery = !query || nomor.includes(query) || pelaksana.includes(query) || spt.includes(query);
            const matchDate = !dateVal || rowDate === dateVal;
            const matchStatus = !statusVal || rowStatus === statusVal;

            if (matchQuery && matchDate && matchStatus) {
                row.style.display = '';
                visibleCount++;
                // Update row number dynamically
                const noCell = row.querySelector('.ba-col-no');
                if (noCell) noCell.textContent = visibleCount;
            } else {
                row.style.display = 'none';
            }
        });

        // Show/hide empty state
        if (visibleCount === 0 && totalData > 0) {
            if (noResultsRow) noResultsRow.classList.remove('d-none');
        } else {
            if (noResultsRow) noResultsRow.classList.add('d-none');
        }

        // Update footer text
        if (footerInfo) {
            if (visibleCount === 0) {
                footerInfo.textContent = 'Menampilkan 0 dari ' + totalData + ' data';
            } else {
                footerInfo.textContent = 'Menampilkan 1 – ' + visibleCount + ' dari ' + totalData + ' data';
            }
        }
    }

    // Attach Event Listeners
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
    }
    if (dateInput) {
        dateInput.addEventListener('change', applyFilters);
    }
    if (statusSelect) {
        statusSelect.addEventListener('change', applyFilters);
    }

    // Client-side Column Sorting
    const sortableHeaders = document.querySelectorAll('.sortable-th');
    let sortDirections = {};

    sortableHeaders.forEach(function(th) {
        th.addEventListener('click', function() {
            const colIdx = parseInt(th.getAttribute('data-column'), 10);
            const currentDir = sortDirections[colIdx] || 'asc';
            const nextDir = currentDir === 'asc' ? 'desc' : 'asc';
            sortDirections[colIdx] = nextDir;

            // Reset other headers
            sortableHeaders.forEach(h => {
                const arr = h.querySelector('.ba-sort-arrows');
                if (arr) arr.textContent = '↑↓';
            });

            // Set current header arrow
            const arr = th.querySelector('.ba-sort-arrows');
            if (arr) arr.textContent = nextDir === 'asc' ? '↑' : '↓';

            // Sort rows
            rows.sort(function(a, b) {
                let cellA = a.children[colIdx] ? a.children[colIdx].innerText.trim().toLowerCase() : '';
                let cellB = b.children[colIdx] ? b.children[colIdx].innerText.trim().toLowerCase() : '';

                if (cellA < cellB) return nextDir === 'asc' ? -1 : 1;
                if (cellA > cellB) return nextDir === 'asc' ? 1 : -1;
                return 0;
            });

            // Re-append sorted rows
            rows.forEach(function(row) {
                tableBody.appendChild(row);
            });

            applyFilters();
        });
    });
});
</script>
<?= $this->endSection() ?>
