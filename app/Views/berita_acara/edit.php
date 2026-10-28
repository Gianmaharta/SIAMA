<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Draf Berita Acara - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/berita-acara-master.css?v=' . (file_exists(FCPATH . 'css/berita-acara-master.css') ? filemtime(FCPATH . 'css/berita-acara-master.css') : time())) ?>">
<style>
.ba-table-select-arsip tbody tr {
    cursor: pointer;
    transition: background-color 0.15s ease;
}
.ba-table-select-arsip tbody tr.row-selected {
    background-color: #EFF6FF !important;
}
.ba-table-select-arsip tbody tr:hover {
    background-color: #F8FAFC;
}
</style>
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. Header Halaman: Tombol Back Ringkas Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/berita-acara') ?>" class="ba-btn-back-header" title="Kembali ke Daftar Berita Acara" aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="ba-page-title mb-0" style="font-size: 22px; font-weight: 700; color: #172033;">Edit Draf Berita Acara</h1>
        <p class="text-secondary small mb-0 mt-1">Perbarui nomor, tanggal, referensi SPT, atau kelola arsip terlampir pada draf berita acara ini.</p>
    </div>
</div>

<!-- Banner Catatan Revisi dari Kepala Bidang (Jika Berstatus Revisi) -->
<?php if (($ba['status_persetujuan'] ?? '') === 'Revisi' && !empty($ba['catatan_revisi'])) : ?>
    <div class="alert alert-warning border-0 rounded-3 shadow-sm d-flex align-items-start gap-3 p-3 mb-4" style="background-color: #FEF3C7; color: #92400E;">
        <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0 text-warning mt-1"></i>
        <div class="w-100">
            <div class="fw-bold mb-1" style="font-size: 14.5px;">Draf Berita Acara Memerlukan Revisi</div>
            <div class="small mb-1 text-secondary-emphasis">Kepala Bidang meminta perbaikan dengan catatan:</div>
            <div class="p-2 rounded-2 mt-2 fw-medium" style="background-color: #FDE68A; color: #78350F; font-size: 13px;">
                &ldquo;<?= nl2br(esc($ba['catatan_revisi'])) ?>&rdquo;
            </div>
            <div class="small mt-2 text-muted fst-italic">
                *Setelah Anda memperbarui draf ini, status akan otomatis diajukan kembali ke Kepala Bidang untuk verifikasi ulang.
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- Feedback Flash Messages -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5 text-danger"></i>
        <div class="small fw-medium"><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5 text-success"></i>
        <div class="small fw-medium"><?= session()->getFlashdata('success') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Form Edit Berita Acara -->
<form action="<?= base_url('/berita-acara/update/' . $ba['id_berita_acara']) ?>" method="post" id="formEditBA" class="needs-validation" novalidate>
    <?= csrf_field() ?>

    <!-- CARD 1: INFORMASI BERITA ACARA -->
    <div class="ba-form-card">
        <div class="ba-form-card-header">
            <h2 class="ba-form-card-title">
                <i class="bi bi-file-earmark-text"></i>
                <span>Informasi Berita Acara</span>
            </h2>
            <div>
                <?php 
                    $badgeClass = ($ba['status_persetujuan'] === 'Revisi') ? 'ba-badge-revisi' : 'ba-badge-draf-kabid';
                    $statusLabel = str_replace('_', ' ', $ba['status_persetujuan'] ?? 'Draf Kabid');
                ?>
                <span class="ba-status-badge <?= $badgeClass ?>">
                    <i class="bi <?= ($ba['status_persetujuan'] === 'Revisi') ? 'bi-arrow-repeat' : 'bi-clock' ?>"></i>
                    <span>Status: <?= esc($statusLabel) ?></span>
                </span>
            </div>
        </div>

        <div class="row g-3 g-md-4">
            <!-- Nomor BA -->
            <div class="col-12 col-md-6">
                <label for="nomor_ba" class="ba-form-label">
                    Nomor Berita Acara <span class="text-danger">*</span>
                </label>
                <input 
                    type="text" 
                    name="nomor_ba" 
                    id="nomor_ba" 
                    class="form-control" 
                    placeholder="Contoh: 018/BA/BPKPD/2026" 
                    value="<?= old('nomor_ba', esc($ba['nomor_ba'])) ?>" 
                    maxlength="100" 
                    required
                >
                <div class="invalid-feedback">
                    Nomor Berita Acara wajib diisi.
                </div>
            </div>

            <!-- Tanggal BA -->
            <div class="col-12 col-md-6">
                <label for="tanggal_ba" class="ba-form-label">
                    Tanggal Berita Acara <span class="text-danger">*</span>
                </label>
                <input 
                    type="date" 
                    name="tanggal_ba" 
                    id="tanggal_ba" 
                    class="form-control" 
                    value="<?= old('tanggal_ba', esc($ba['tanggal_ba'])) ?>" 
                    required
                >
                <div class="invalid-feedback">
                    Tanggal Berita Acara wajib dipilih.
                </div>
            </div>

            <!-- Referensi SPT -->
            <div class="col-12">
                <label for="id_spt" class="ba-form-label">
                    Referensi SPT <span class="text-secondary fw-normal">(Opsional)</span>
                </label>
                <select name="id_spt" id="id_spt" class="form-select">
                    <option value="">-- Tidak Terkait SPT --</option>
                    <?php if (!empty($spt_list)) : ?>
                        <?php foreach ($spt_list as $spt) : ?>
                            <option value="<?= esc($spt['id_spt']) ?>" <?= old('id_spt', $ba['id_spt']) == $spt['id_spt'] ? 'selected' : '' ?>>
                                <?= esc($spt['nomor_spt']) ?> &ndash; <?= esc($spt['perihal']) ?> (<?= esc($spt['tanggal_spt']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <div class="form-text small text-secondary mt-1">
                    Surat Perintah Tugas (SPT) yang menjadi dasar pelaksanaan kegiatan alih media arsip ini.
                </div>
            </div>
        </div>
    </div>

    <!-- CARD 2: KELOLA ARSIP TERLAMPIR -->
    <div class="ba-form-card p-0 overflow-hidden">
        <div class="ba-form-card-header px-4 pt-4 pb-3 mb-0">
            <h2 class="ba-form-card-title">
                <i class="bi bi-folder-check"></i>
                <span>Kelola Arsip Terlampir</span>
            </h2>
            <div class="d-flex align-items-center gap-2">
                <span id="selectedCountBadge" class="badge bg-primary text-white fw-semibold px-2 py-1">
                    <?= !empty($arsip_terpilih_count) ? $arsip_terpilih_count : 0 ?> arsip terpilih
                </span>
            </div>
        </div>

        <div class="px-4 py-3 bg-light border-bottom">
            <div class="d-flex align-items-center justify-content-between flex-wrap gap-2">
                <div class="small text-secondary">
                    <i class="bi bi-info-circle me-1 text-primary"></i>
                    Centang untuk menyertakan arsip, atau hilangkan centang untuk melepas arsip dari Berita Acara ini.
                </div>
                <div class="form-check m-0">
                    <input class="form-check-input" type="checkbox" id="checkAllArsip" style="cursor: pointer;">
                    <label class="form-check-label small fw-semibold text-dark" for="checkAllArsip" style="cursor: pointer;">
                        Pilih Semua Arsip
                    </label>
                </div>
            </div>
        </div>

        <div class="table-responsive">
            <table class="ba-table-select-arsip">
                <thead>
                    <tr>
                        <th style="width: 46px; text-align: center;">#</th>
                        <th style="width: 50px;">No</th>
                        <th>Nomor Arsip</th>
                        <th>Nama / Judul Arsip</th>
                        <th>Kurun Waktu</th>
                        <th>Kondisi</th>
                        <th style="text-align: center;">Status Lampiran</th>
                    </tr>
                </thead>
                <tbody id="tbodyArsip">
                    <?php if (!empty($list_arsip)) : ?>
                        <?php 
                            $no = 1; 
                            $oldArsipIds = old('arsip_ids');
                            foreach ($list_arsip as $arsip) : 
                                $isAttachedDefault = !empty($arsip['is_attached']);
                                $isChecked = is_array($oldArsipIds) ? in_array($arsip['id_arsip'], $oldArsipIds) : $isAttachedDefault;
                        ?>
                            <tr class="arsip-row <?= $isChecked ? 'row-selected' : '' ?>">
                                <td style="text-align: center;" onclick="event.stopPropagation();">
                                    <input 
                                        type="checkbox" 
                                        name="arsip_ids[]" 
                                        value="<?= esc($arsip['id_arsip']) ?>" 
                                        class="form-check-input arsip-checkbox" 
                                        style="cursor: pointer;"
                                        <?= $isChecked ? 'checked' : '' ?>
                                        aria-label="Pilih arsip <?= esc($arsip['nomor_arsip']) ?>"
                                    >
                                </td>
                                <td class="text-secondary"><?= $no++ ?></td>
                                <td>
                                    <span class="fw-semibold text-primary">
                                        <?= esc($arsip['nomor_arsip']) ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-medium text-dark"><?= esc($arsip['nama_arsip']) ?></div>
                                </td>
                                <td class="text-secondary"><?= esc($arsip['kurun_waktu'] ?? '-') ?></td>
                                <td>
                                    <span class="badge text-bg-light border text-dark fw-medium px-2 py-1" style="font-size: 11.5px;">
                                        <?= esc($arsip['kondisi'] ?? 'Baik') ?>
                                    </span>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($isAttachedDefault) : ?>
                                        <span class="badge bg-primary-subtle text-primary border border-primary-subtle fw-medium px-2 py-1" style="font-size: 11.5px;">
                                            <i class="bi bi-paperclip me-1"></i> Terlampir di BA ini
                                        </span>
                                    <?php else : ?>
                                        <span class="badge bg-light text-secondary border fw-medium px-2 py-1" style="font-size: 11.5px;">
                                            <i class="bi bi-plus-circle me-1"></i> Tersedia Ditambahkan
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="7">
                                <div class="ba-empty-state py-4">
                                    <div class="ba-empty-icon mb-2">
                                        <i class="bi bi-folder-x"></i>
                                    </div>
                                    <div class="ba-empty-title">Tidak Ada Arsip</div>
                                    <div class="ba-empty-desc">Tidak ada data arsip yang terlampir atau tersedia untuk ditambahkan.</div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Alert Validasi Arsip Belum Dipilih -->
    <div id="alertNoArsipSelected" class="alert alert-warning d-none d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
        <div class="small fw-medium">Harap pilih minimal 1 arsip sebelum menyimpan perubahan Berita Acara.</div>
    </div>

    <!-- Tombol Aksi Simpan Perubahan & Batal -->
    <div class="d-flex align-items-center gap-2 mt-4 mb-5">
        <button type="submit" class="btn ba-btn-save" id="btnSimpanEditBA">
            <i class="bi bi-floppy"></i>
            <span>Simpan Perubahan</span>
        </button>
        <a href="<?= base_url('/berita-acara') ?>" class="btn ba-btn-cancel" id="btnBatalBA">
            Batal
        </a>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('checkAllArsip');
    const checkboxes = document.querySelectorAll('.arsip-checkbox');
    const badgeCount = document.getElementById('selectedCountBadge');
    const form = document.getElementById('formEditBA');
    const alertNoArsip = document.getElementById('alertNoArsipSelected');
    const rows = document.querySelectorAll('.arsip-row');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.arsip-checkbox:checked').length;
        if (badgeCount) {
            badgeCount.textContent = checkedCount + ' arsip terpilih';
            if (checkedCount > 0) {
                badgeCount.className = 'badge bg-primary text-white fw-semibold px-2 py-1';
                if (alertNoArsip) alertNoArsip.classList.add('d-none');
            } else {
                badgeCount.className = 'badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-2 py-1';
            }
        }
        if (checkAll && checkboxes.length > 0) {
            checkAll.checked = (checkedCount === checkboxes.length);
            checkAll.indeterminate = (checkedCount > 0 && checkedCount < checkboxes.length);
        }
    }

    // Toggle Check All
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            checkboxes.forEach(cb => {
                cb.checked = checkAll.checked;
                const row = cb.closest('tr');
                if (row) {
                    if (cb.checked) row.classList.add('row-selected');
                    else row.classList.remove('row-selected');
                }
            });
            updateCount();
        });
    }

    // Individual Checkbox Click
    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            const row = this.closest('tr');
            if (row) {
                if (this.checked) row.classList.add('row-selected');
                else row.classList.remove('row-selected');
            }
            updateCount();
        });
    });

    // Row Click to Toggle
    rows.forEach(row => {
        row.addEventListener('click', function(e) {
            if (e.target.tagName.toLowerCase() === 'input') return;
            const cb = this.querySelector('.arsip-checkbox');
            if (cb) {
                cb.checked = !cb.checked;
                if (cb.checked) this.classList.add('row-selected');
                else this.classList.remove('row-selected');
                updateCount();
            }
        });
    });

    // Validasi Form saat Submit
    if (form) {
        form.addEventListener('submit', function(e) {
            const checkedCount = document.querySelectorAll('.arsip-checkbox:checked').length;
            let isValid = form.checkValidity();

            if (checkedCount === 0) {
                isValid = false;
                if (alertNoArsip) {
                    alertNoArsip.classList.remove('d-none');
                    alertNoArsip.scrollIntoView({ behavior: 'smooth', block: 'center' });
                }
            } else {
                if (alertNoArsip) alertNoArsip.classList.add('d-none');
            }

            if (!isValid) {
                e.preventDefault();
                e.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    updateCount();
});
</script>
<?= $this->endSection() ?>
