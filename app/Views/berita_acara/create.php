<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Buat Berita Acara - SIAMA
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
        <h1 class="ba-page-title mb-0" style="font-size: 22px; font-weight: 700; color: #172033;">Buat Berita Acara</h1>
        <p class="text-secondary small mb-0 mt-1">Buat draf berita acara alih media arsip baru untuk diajukan ke Kepala Bidang.</p>
    </div>
</div>

<!-- Feedback Flash Messages -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill me-2 fs-5 text-danger"></i>
        <div class="small fw-medium"><?= session()->getFlashdata('error') ?></div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Form Tambah Berita Acara -->
<form action="<?= base_url('/berita-acara/store') ?>" method="post" id="formBuatBA" class="needs-validation" novalidate>
    <?= csrf_field() ?>

    <!-- CARD 1: INFORMASI BERITA ACARA -->
    <div class="ba-form-card">
        <div class="ba-form-card-header">
            <h2 class="ba-form-card-title">
                <i class="bi bi-file-earmark-text"></i>
                <span>Informasi Berita Acara</span>
            </h2>
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
                    value="<?= old('nomor_ba') ?>" 
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
                    value="<?= old('tanggal_ba', date('Y-m-d')) ?>" 
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
                            <option value="<?= esc($spt['id_spt']) ?>" <?= old('id_spt') === $spt['id_spt'] ? 'selected' : '' ?>>
                                <?= esc($spt['nomor_spt']) ?> &ndash; <?= esc($spt['perihal']) ?> (<?= esc($spt['tanggal_spt']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <div class="form-text small text-secondary mt-1">
                    Pilih Surat Perintah Tugas (SPT) yang mendasari pelaksanaan alih media arsip ini.
                </div>
            </div>
        </div>
    </div>

    <!-- CARD 2: PILIH ARSIP HASIL ALIH MEDIA -->
    <div class="ba-form-card p-0 overflow-hidden">
        <div class="ba-form-card-header px-4 pt-4 pb-3 mb-0">
            <h2 class="ba-form-card-title">
                <i class="bi bi-folder2-open"></i>
                <span>Pilih Arsip yang Dialihmediakan</span>
                <span class="text-danger">*</span>
            </h2>
            <span id="selectedCountBadge" class="badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-2 py-1" style="font-size: 12px;">
                0 arsip terpilih
            </span>
        </div>

        <div class="px-4 py-2 bg-light border-bottom text-secondary small">
            Centang satu atau beberapa arsip di bawah yang telah selesai dialihmediakan untuk dimasukkan ke dalam Berita Acara ini.
        </div>

        <div class="table-responsive">
            <table class="table ba-table-select-arsip align-middle mb-0" id="tableSelectArsip">
                <thead>
                    <tr>
                        <th style="width: 50px; text-align: center; padding-left: 20px;">
                            <input class="form-check-input" type="checkbox" id="checkAllArsip" title="Pilih Semua Arsip">
                        </th>
                        <th style="width: 60px;">No</th>
                        <th>Nomor Arsip</th>
                        <th>Nama / Judul Arsip</th>
                        <th>Kurun Waktu</th>
                        <th style="padding-right: 20px;">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($arsip_list)) : ?>
                        <?php $no = 1; foreach ($arsip_list as $arsip) : ?>
                            <tr class="arsip-row" data-id="<?= esc($arsip['id_arsip']) ?>">
                                <td style="text-align: center; padding-left: 20px;" onclick="event.stopPropagation();">
                                    <input 
                                        class="form-check-input arsip-checkbox" 
                                        type="checkbox" 
                                        name="arsip_ids[]" 
                                        value="<?= esc($arsip['id_arsip']) ?>" 
                                        id="arsip_<?= esc($arsip['id_arsip']) ?>"
                                    >
                                </td>
                                <td style="color: #64748B; font-weight: 500;"><?= $no++ ?></td>
                                <td>
                                    <span class="fw-semibold text-primary"><?= esc($arsip['nomor_arsip']) ?></span>
                                </td>
                                <td class="text-dark fw-medium"><?= esc($arsip['nama_arsip']) ?></td>
                                <td class="text-secondary"><?= esc($arsip['kurun_waktu'] ?? '-') ?></td>
                                <td style="padding-right: 20px;">
                                    <span class="badge text-bg-light border text-dark fw-medium px-2 py-1" style="font-size: 12px;">
                                        <?= esc($arsip['kondisi'] ?? 'Baik') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-secondary">
                                <i class="bi bi-folder-x fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-semibold text-dark">Tidak ada arsip yang dapat dipilih</div>
                                <div class="small text-muted mt-1">Semua arsip sudah masuk ke dalam Berita Acara atau belum ada arsip yang dialihmediakan.</div>
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
        <div class="small fw-medium">Harap pilih minimal 1 arsip sebelum menyimpan draf Berita Acara.</div>
    </div>

    <!-- Tombol Aksi Simpan Draf & Batal -->
    <div class="d-flex align-items-center gap-2 mt-4 mb-5">
        <button type="submit" class="btn ba-btn-save" id="btnSimpanDrafBA">
            <i class="bi bi-floppy"></i>
            <span>Simpan Draf Berita Acara</span>
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
    const form = document.getElementById('formBuatBA');
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
