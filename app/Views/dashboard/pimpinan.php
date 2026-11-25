<link rel="stylesheet" href="<?= base_url('css/pimpinan-dashboard.css?v=' . (file_exists(FCPATH . 'css/pimpinan-dashboard.css') ? filemtime(FCPATH . 'css/pimpinan-dashboard.css') : time())) ?>">

<div class="pimpinan-dashboard">
    <!-- 1. HEADING & SUBHEADING -->
    <div class="mb-4">
        <h1 class="pimpinan-header-title">Selamat Datang, <?= esc($nama_lengkap ?? 'Kepala Dinas') ?>!</h1>
        <p class="pimpinan-header-desc">Ini adalah ringkasan statistik dan monitoring alih media berdasarkan akses Anda sebagai Kepala Dinas<?= !empty($nama_opd) ? ' - ' . esc($nama_opd) : '' ?></p>
    </div>

    <!-- 1. RINGKASAN STATISTIK (4 KARTU) -->
    <div class="row g-4 mb-4">
        <!-- Card 1: Total SPT Diterbitkan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card pimpinan-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="pimpinan-stat-icon-blue">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <div>
                        <div class="pimpinan-stat-label">Total SPT Diterbitkan</div>
                        <div class="pimpinan-stat-value">
                            <?= number_format($stats['spt_diterbitkan'] ?? 0, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 2: Berita Acara Menanti TTD Pimpinan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card pimpinan-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="pimpinan-stat-icon-purple">
                        <i class="bi bi-pen"></i>
                    </div>
                    <div>
                        <div class="pimpinan-stat-label">Menanti TTD Pimpinan</div>
                        <div class="pimpinan-stat-value" style="color: #7C3AED;">
                            <?= number_format($stats['ba_menanti_pimpinan'] ?? 0, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Arsip Dialihmediakan -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card pimpinan-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="pimpinan-stat-icon-green">
                        <i class="bi bi-archive"></i>
                    </div>
                    <div>
                        <div class="pimpinan-stat-label">Total Arsip Dialihmediakan</div>
                        <div class="pimpinan-stat-value">
                            <?= number_format($stats['total_arsip_opd'] ?? 0, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Card 4: Berita Acara Selesai -->
        <div class="col-12 col-sm-6 col-xl-3">
            <div class="card pimpinan-stat-card h-100">
                <div class="card-body d-flex align-items-center gap-3">
                    <div class="pimpinan-stat-icon-emerald">
                        <i class="bi bi-patch-check"></i>
                    </div>
                    <div>
                        <div class="pimpinan-stat-label">Berita Acara Selesai</div>
                        <div class="pimpinan-stat-value">
                            <?= number_format($stats['ba_selesai'] ?? 0, 0, ',', '.') ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 2. DOKUMEN MEMBUTUHKAN TINDAKAN PIMPINAN (SECTION PRIORITAS) -->
    <div class="card pimpinan-section-card priority-card">
        <div class="pimpinan-section-header">
            <div class="pimpinan-section-title-group">
                <div class="pimpinan-section-icon icon-purple">
                    <i class="bi bi-exclamation-circle"></i>
                </div>
                <div>
                    <h2 class="pimpinan-section-title">Dokumen Membutuhkan Tindakan Pimpinan</h2>
                    <p class="pimpinan-section-subtitle">Berita Acara yang telah diverifikasi Kepala Bidang dan siap untuk ditandatangani atau disahkan.</p>
                </div>
            </div>
            <?php if (!empty($ba_action_required)) : ?>
                <span class="badge-status-pimpinan badge-status-purple">
                    <i class="bi bi-clock-fill" style="font-size: 10px;"></i>
                    <?= count($ba_action_required) ?> Dokumen Menunggu TTD
                </span>
            <?php endif; ?>
        </div>

        <?php if (!empty($ba_action_required)) : ?>
            <div class="table-responsive">
                <table class="table align-middle pimpinan-table mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;">No</th>
                            <th>Nomor Berita Acara</th>
                            <th>Tanggal</th>
                            <th>Perangkat Daerah (OPD)</th>
                            <th>Pelaksana</th>
                            <th class="text-center">Status Dokumen</th>
                            <th class="text-center" style="width: 140px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $noAction = 1; foreach ($ba_action_required as $item) : ?>
                            <tr>
                                <td class="text-muted"><?= $noAction++ ?></td>
                                <td>
                                    <a href="<?= base_url('/berita-acara/detail/' . $item['id_berita_acara']) ?>" class="pimpinan-table-code" title="<?= esc($item['nomor_ba']) ?>">
                                        <?= esc($item['nomor_ba']) ?>
                                    </a>
                                    <?php if (!empty($item['perihal_spt'])) : ?>
                                        <div class="text-muted small text-truncate" style="max-width: 250px;" title="<?= esc($item['perihal_spt']) ?>">
                                            SPT: <?= esc($item['perihal_spt']) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-nowrap text-secondary">
                                    <?= esc(date('d/m/Y', strtotime($item['tanggal_ba'] ?? date('Y-m-d')))) ?>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?= esc($item['nama_opd'] ?? $nama_opd ?? '-') ?></div>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-person text-muted" style="font-size: 13px;"></i>
                                        <span><?= esc($item['nama_pelaksana'] ?? 'Arsiparis') ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status-pimpinan badge-status-purple">
                                        <i class="bi bi-clock" style="font-size: 11px;"></i>
                                        Menunggu TTD Pimpinan
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('/berita-acara/detail/' . $item['id_berita_acara']) ?>"
                                       class="siama-aksi-btn siama-aksi-btn-navy"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       data-bs-title="Tinjau &amp; Sahkan"
                                       aria-label="Tinjau &amp; Sahkan">
                                        <i class="bi bi-pen"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else : ?>
            <div class="pimpinan-empty-state">
                <div class="pimpinan-empty-icon icon-green">
                    <i class="bi bi-check2-circle"></i>
                </div>
                <div class="pimpinan-empty-title">Tidak Ada Dokumen yang Membutuhkan Tindakan</div>
                <div class="pimpinan-empty-desc">
                    Seluruh Berita Acara yang masuk telah diproses, atau belum ada Berita Acara baru yang menunggu tanda tangan Anda.
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- 3. MONITORING PELAKSANAAN SPT -->
    <div class="card pimpinan-section-card">
        <div class="pimpinan-section-header">
            <div class="pimpinan-section-title-group">
                <div class="pimpinan-section-icon icon-blue">
                    <i class="bi bi-file-earmark-text"></i>
                </div>
                <div>
                    <h2 class="pimpinan-section-title">Monitoring Pelaksanaan SPT</h2>
                    <p class="pimpinan-section-subtitle">Pantau progres dan tahapan pelaksanaan alih media arsip dari setiap Surat Perintah Tugas.</p>
                </div>
            </div>
            <a href="<?= base_url('/spt') ?>" class="pimpinan-link-all">
                Lihat Semua SPT <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
            </a>
        </div>

        <div class="table-responsive">
            <table class="table align-middle pimpinan-table mb-0">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Nomor SPT</th>
                        <th>Tanggal SPT</th>
                        <th>Perihal Penugasan</th>
                        <th>Perangkat Daerah (OPD)</th>
                        <th class="text-center">Jumlah Berkas</th>
                        <th class="text-center">Status Pelaksanaan</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($pimpinan_spt_monitoring)) : ?>
                        <?php $noSpt = 1; foreach ($pimpinan_spt_monitoring as $spt) : ?>
                            <tr>
                                <td class="text-muted"><?= $noSpt++ ?></td>
                                <td>
                                    <a href="<?= base_url('/spt/detail/' . $spt['id_spt']) ?>" class="pimpinan-table-code" title="<?= esc($spt['nomor_spt']) ?>">
                                        <?= esc($spt['nomor_spt']) ?>
                                    </a>
                                </td>
                                <td class="text-nowrap text-secondary">
                                    <?= esc(date('d/m/Y', strtotime($spt['tanggal_spt'] ?? date('Y-m-d')))) ?>
                                </td>
                                <td>
                                    <div class="text-dark fw-medium text-truncate" style="max-width: 280px;" title="<?= esc($spt['perihal']) ?>">
                                        <?= esc($spt['perihal']) ?>
                                    </div>
                                </td>
                                <td>
                                    <div class="text-secondary"><?= esc($spt['nama_opd'] ?? $nama_opd ?? '-') ?></div>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11.5px; font-weight: 600;">
                                        <?= number_format($spt['total_arsip'] ?? 0, 0, ',', '.') ?> Berkas
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status-pimpinan <?= esc($spt['status_class'] ?? 'badge-status-blue') ?>">
                                        <?= esc($spt['status_proses'] ?? 'Sedang Diproses') ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('/spt/detail/' . $spt['id_spt']) ?>"
                                       class="siama-aksi-btn siama-aksi-btn-detail"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       data-bs-title="Lihat Detail"
                                       aria-label="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="8" class="text-center py-4 text-muted small">
                                Belum ada data Surat Perintah Tugas (SPT) yang diterbitkan di OPD ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- 4. RIWAYAT BERITA ACARA -->
    <div class="card pimpinan-section-card">
        <div class="pimpinan-section-header">
            <div class="pimpinan-section-title-group">
                <div class="pimpinan-section-icon icon-amber">
                    <i class="bi bi-journal-check"></i>
                </div>
                <div>
                    <h2 class="pimpinan-section-title">Riwayat Berita Acara</h2>
                    <p class="pimpinan-section-subtitle">Rekam jejak persetujuan dan tahapan Berita Acara di lingkungan OPD Anda.</p>
                </div>
            </div>
            <a href="<?= base_url('/berita-acara') ?>" class="pimpinan-link-all">
                Lihat Semua BA <i class="bi bi-chevron-right" style="font-size: 11px;"></i>
            </a>
        </div>

        <!-- Filter Tabs / Pills -->
        <div class="pimpinan-filter-pills">
            <button type="button" class="ba-filter-btn active" data-filter="all">
                Semua <span class="ba-filter-badge"><?= $ba_stats_pimpinan['semua'] ?? 0 ?></span>
            </button>
            <button type="button" class="ba-filter-btn" data-filter="menunggu_ttd">
                Menunggu Tindakan <span class="ba-filter-badge"><?= $ba_stats_pimpinan['menunggu_ttd'] ?? 0 ?></span>
            </button>
            <button type="button" class="ba-filter-btn" data-filter="selesai">
                Selesai <span class="ba-filter-badge"><?= $ba_stats_pimpinan['selesai'] ?? 0 ?></span>
            </button>
            <button type="button" class="ba-filter-btn" data-filter="revisi">
                Perbaikan / Revisi <span class="ba-filter-badge"><?= $ba_stats_pimpinan['revisi'] ?? 0 ?></span>
            </button>
            <button type="button" class="ba-filter-btn" data-filter="draf_kabid">
                Draf Kabid <span class="ba-filter-badge"><?= $ba_stats_pimpinan['draf_kabid'] ?? 0 ?></span>
            </button>
        </div>

        <!-- Table Riwayat BA -->
        <div class="table-responsive">
            <table class="table align-middle pimpinan-table mb-0" id="tableRiwayatBa">
                <thead>
                    <tr>
                        <th style="width: 40px;">No</th>
                        <th>Nomor Berita Acara</th>
                        <th>Tanggal BA</th>
                        <th>Terkait SPT</th>
                        <th>Pelaksana</th>
                        <th class="text-center">Status Dokumen</th>
                        <th class="text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($riwayat_ba)) : ?>
                        <?php 
                        $noBa = 1; 
                        foreach ($riwayat_ba as $ba) : 
                            $statusRaw = $ba['status_persetujuan'] ?? 'Draf_Kabid';
                            $filterCat = 'draf_kabid';
                            $badgeClass = 'badge-status-amber';
                            $statusLabel = 'Draf Kabid';

                            if (in_array($statusRaw, ['Draf_Pimpinan', 'Menunggu_Pimpinan'])) {
                                $filterCat = 'menunggu_ttd';
                                $badgeClass = 'badge-status-purple';
                                $statusLabel = 'Menunggu TTD Pimpinan';
                            } elseif ($statusRaw === 'Selesai') {
                                $filterCat = 'selesai';
                                $badgeClass = 'badge-status-green';
                                $statusLabel = 'Selesai Disahkan';
                            } elseif ($statusRaw === 'Revisi') {
                                $filterCat = 'revisi';
                                $badgeClass = 'badge-status-rose';
                                $statusLabel = 'Perbaikan (Revisi)';
                            } elseif ($statusRaw === 'Draf_Kabid') {
                                $filterCat = 'draf_kabid';
                                $badgeClass = 'badge-status-amber';
                                $statusLabel = 'Verifikasi Kabid';
                            }
                        ?>
                            <tr class="ba-history-row" data-ba-status="<?= esc($filterCat) ?>">
                                <td class="text-muted row-counter"><?= $noBa++ ?></td>
                                <td>
                                    <a href="<?= base_url('/berita-acara/detail/' . $ba['id_berita_acara']) ?>" class="pimpinan-table-code" title="<?= esc($ba['nomor_ba']) ?>">
                                        <?= esc($ba['nomor_ba']) ?>
                                    </a>
                                </td>
                                <td class="text-nowrap text-secondary">
                                    <?= esc(date('d/m/Y', strtotime($ba['tanggal_ba'] ?? date('Y-m-d')))) ?>
                                </td>
                                <td>
                                    <?php if (!empty($ba['nomor_spt']) || !empty($ba['perihal_spt'])) : ?>
                                        <div class="text-dark fw-medium text-truncate" style="max-width: 250px;" title="<?= esc($ba['perihal_spt'] ?? $ba['nomor_spt']) ?>">
                                            <?= esc($ba['nomor_spt'] ?? '-') ?>
                                        </div>
                                        <?php if (!empty($ba['perihal_spt'])) : ?>
                                            <div class="text-muted small text-truncate" style="max-width: 250px;" title="<?= esc($ba['perihal_spt']) ?>">
                                                <?= esc($ba['perihal_spt']) ?>
                                            </div>
                                        <?php endif; ?>
                                    <?php else : ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex align-items-center gap-1">
                                        <i class="bi bi-person text-muted" style="font-size: 13px;"></i>
                                        <span><?= esc($ba['nama_pelaksana'] ?? 'Arsiparis') ?></span>
                                    </div>
                                </td>
                                <td class="text-center">
                                    <span class="badge-status-pimpinan <?= esc($badgeClass) ?>">
                                        <?= esc($statusLabel) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= base_url('/berita-acara/detail/' . $ba['id_berita_acara']) ?>"
                                       class="siama-aksi-btn siama-aksi-btn-detail"
                                       data-bs-toggle="tooltip"
                                       data-bs-placement="top"
                                       data-bs-title="Lihat Detail"
                                       aria-label="Lihat Detail">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="emptyFilterRow" style="display: none;">
                            <td colspan="7" class="text-center py-4 text-muted small">
                                Tidak ada Berita Acara dengan status yang dipilih.
                            </td>
                        </tr>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted small">
                                Belum ada data Berita Acara di OPD ini.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Script Filter Kategori Riwayat Berita Acara -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const filterButtons = document.querySelectorAll('.ba-filter-btn');
    const tableRows = document.querySelectorAll('.ba-history-row');
    const emptyRow = document.getElementById('emptyFilterRow');

    filterButtons.forEach(button => {
        button.addEventListener('click', function () {
            filterButtons.forEach(btn => btn.classList.remove('active'));
            this.classList.add('active');

            const filterValue = this.getAttribute('data-filter');
            let visibleCount = 0;

            tableRows.forEach(row => {
                const rowStatus = row.getAttribute('data-ba-status');
                if (filterValue === 'all' || rowStatus === filterValue) {
                    row.style.display = '';
                    visibleCount++;
                } else {
                    row.style.display = 'none';
                }
            });

            if (emptyRow) {
                emptyRow.style.display = visibleCount === 0 ? '' : 'none';
            }
        });
    });
});
</script>
