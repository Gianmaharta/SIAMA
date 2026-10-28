<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Penilaian Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/penilaian-master.css') ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<?php
$currentSkor = (isset($arsip['skor_prioritas']) && $arsip['skor_prioritas'] !== null && $arsip['skor_prioritas'] !== '') ? (int)$arsip['skor_prioritas'] : 0;
if ($currentSkor < 0) $currentSkor = 0;
if ($currentSkor > 100) $currentSkor = 100;
?>

<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/penilaian') ?>" class="btn-back-title" title="Kembali ke Daftar Penilaian Arsip" aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div class="penilaian-page-header mb-0">
        <h1 class="penilaian-page-title mb-0">Penilaian Arsip</h1>
        <p class="penilaian-page-subtitle mb-0">Lakukan penilaian terhadap arsip yang telah dialihmediakan sesuai ketentuan dan jadwal retensi arsip (JRA).</p>
    </div>
</div>

<!-- Notifikasi Flash Error / Success -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-exclamation-triangle-fill me-2 fs-5"></i>
        <div>
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center mb-4" role="alert">
        <i class="bi bi-check-circle-fill me-2 fs-5"></i>
        <div>
            <?= session()->getFlashdata('success') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<div class="row g-4 align-items-stretch">
    <!-- Kolom Kiri: Panel Pratinjau Dokumen -->
    <div class="col-12 col-lg-7 d-flex flex-column">
        <div class="penilaian-form-card flex-grow-1 mb-0 d-flex flex-column">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <div class="penilaian-card-header-title mb-0">
                    <div class="card-icon-badge">
                        <i class="bi bi-file-earmark-text"></i>
                    </div>
                    <span>Pratinjau Dokumen</span>
                </div>
                <?php if (!empty($arsip['file_arsip'])) : ?>
                    <a href="<?= base_url('uploads/arsip/' . $arsip['file_arsip']) ?>" target="_blank" class="btn btn-sm btn-outline-primary" style="font-size: 12.5px; border-radius: 6px;">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Buka di Tab Baru
                    </a>
                <?php endif; ?>
            </div>

            <div class="penilaian-preview-box flex-grow-1">
                <?php if (!empty($arsip['file_arsip'])) : ?>
                    <iframe src="<?= base_url('uploads/arsip/' . $arsip['file_arsip']) ?>" width="100%" height="100%" style="min-height: 520px; border: none; border-radius: 8px;"></iframe>
                <?php else : ?>
                    <div class="preview-empty-center">
                        <div class="preview-circle-icon">
                            <i class="bi bi-file-earmark"></i>
                            <span class="badge-alert-dot">
                                <i class="bi bi-exclamation-lg"></i>
                            </span>
                        </div>
                    </div>
                    <div class="preview-warning-alert">
                        <div class="alert-heading">Berkas arsip digital tidak ditemukan atau belum diunggah.</div>
                        <div class="alert-sub">Pastikan berkas telah dialihmediakan dan unggah melalui menu pengelolaan arsip.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Informasi Arsip & Form Input Penilaian -->
    <div class="col-12 col-lg-5 d-flex flex-column">
        <!-- Panel Informasi Arsip -->
        <div class="penilaian-form-card mb-4">
            <div class="penilaian-card-header-title">
                <div class="card-icon-badge">
                    <i class="bi bi-info-circle"></i>
                </div>
                <span>Informasi Arsip</span>
            </div>
            <table class="table penilaian-info-table mb-0">
                <tbody>
                    <tr>
                        <th>Nomor Arsip</th>
                        <td class="table-colon">:</td>
                        <td><span class="fw-medium"><?= esc($arsip['nomor_arsip'] ?? '-') ?></span></td>
                    </tr>
                    <tr>
                        <th>Nama Arsip</th>
                        <td class="table-colon">:</td>
                        <td><?= esc($arsip['nama_arsip'] ?? '-') ?></td>
                    </tr>
                    <tr>
                        <th>OPD Pemilik</th>
                        <td class="table-colon">:</td>
                        <td><?= esc(!empty($arsip['nama_opd']) ? $arsip['nama_opd'] : '-') ?></td>
                    </tr>
                    <tr>
                        <th>Kurun Waktu</th>
                        <td class="table-colon">:</td>
                        <td><?= esc(!empty($arsip['kurun_waktu']) ? $arsip['kurun_waktu'] : '-') ?></td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Panel Form Input Penilaian -->
        <div class="penilaian-form-card flex-grow-1 mb-0 d-flex flex-column">
            <div class="penilaian-card-header-title">
                <div class="card-icon-badge">
                    <i class="bi bi-pencil-square"></i>
                </div>
                <span>Form Input Penilaian</span>
            </div>

            <form action="<?= base_url('/penilaian/store/' . $arsip['id_arsip']) ?>" method="post" id="formPenilaian" class="d-flex flex-column flex-grow-1" novalidate>
                <?= csrf_field() ?>

                <!-- Skor Prioritas (Slider + Input Angka) -->
                <div class="mb-4">
                    <div class="skor-header-row">
                        <label for="skor_slider" class="form-label-custom mb-0">
                            Skor Prioritas (0–100) <span class="text-danger">*</span>
                        </label>
                        <span class="skor-indicator-badge" id="skorValueBadge" title="Nilai skor saat ini">
                            <span id="skorBadgeNumber"><?= esc($currentSkor) ?></span>
                        </span>
                    </div>

                    <div class="skor-control-wrapper">
                        <div class="skor-slider-wrap">
                            <div class="slider-track-wrap">
                                <input 
                                    type="range" 
                                    id="skor_slider" 
                                    class="custom-skor-slider" 
                                    min="0" 
                                    max="100" 
                                    step="1" 
                                    value="<?= esc($currentSkor) ?>"
                                    aria-label="Slider Skor Prioritas"
                                >
                            </div>
                            <div class="skor-slider-labels">
                                <span>0</span>
                                <span>100</span>
                            </div>
                        </div>
                        <div class="skor-input-wrap">
                            <input 
                                type="number" 
                                name="skor_prioritas" 
                                id="skor_prioritas" 
                                class="form-control skor-number-input" 
                                min="0" 
                                max="100" 
                                placeholder="0–100" 
                                value="<?= esc($currentSkor) ?>"
                                required
                                aria-label="Input Angka Skor Prioritas"
                            >
                        </div>
                    </div>
                    <div id="skorFeedback" class="text-danger small d-none mt-1" style="font-size: 12px;">
                        Skor prioritas harus berupa angka antara 0 hingga 100.
                    </div>
                </div>

                <!-- Status Autentikasi -->
                <div class="mb-4">
                    <label for="status_autentikasi" class="form-label-custom">
                        Status Autentikasi <span class="text-danger">*</span>
                    </label>
                    <select 
                        name="status_autentikasi" 
                        id="status_autentikasi" 
                        class="form-select form-select-custom w-100" 
                        required
                    >
                        <option value="">-- Pilih Status --</option>
                        <option value="Lolos Evaluasi" <?= (($arsip['status_autentikasi'] ?? '') === 'Lolos Evaluasi') ? 'selected' : '' ?>>Lolos Evaluasi</option>
                        <option value="Revisi Metadata" <?= (($arsip['status_autentikasi'] ?? '') === 'Revisi Metadata') ? 'selected' : '' ?>>Revisi Metadata</option>
                        <option value="Ditolak" <?= (($arsip['status_autentikasi'] ?? '') === 'Ditolak') ? 'selected' : '' ?>>Ditolak</option>
                        <option value="Menunggu Verifikasi Lanjutan" <?= (($arsip['status_autentikasi'] ?? '') === 'Menunggu Verifikasi Lanjutan') ? 'selected' : '' ?>>Menunggu Verifikasi Lanjutan</option>
                    </select>
                    <div id="statusFeedback" class="text-danger small d-none mt-1" style="font-size: 12px;">
                        Status autentikasi wajib dipilih.
                    </div>
                </div>

                <!-- Catatan Penilaian -->
                <div class="mb-4">
                    <label for="catatan_penilaian" class="form-label-custom">
                        Catatan Penilaian
                    </label>
                    <textarea 
                        name="catatan_penilaian" 
                        id="catatan_penilaian" 
                        rows="4" 
                        class="form-control form-textarea-custom w-100" 
                        placeholder="Tulis catatan penilaian di sini..."
                    ><?= esc($arsip['catatan_penilaian'] ?? '') ?></textarea>
                </div>

                <!-- Tombol Simpan Penilaian -->
                <button type="submit" class="btn btn-save-penilaian mt-auto" id="btnSimpanPenilaian">
                    <i class="bi bi-box-arrow-in-down fs-5"></i>
                    <span>Simpan Penilaian</span>
                </button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const slider = document.getElementById('skor_slider');
    const input = document.getElementById('skor_prioritas');
    const badge = document.getElementById('skorBadgeNumber');
    const form = document.getElementById('formPenilaian');
    const statusSelect = document.getElementById('status_autentikasi');
    const skorFeedback = document.getElementById('skorFeedback');
    const statusFeedback = document.getElementById('statusFeedback');

    // Fungsi update progress track slider (#1E60EC untuk filled, #E2E8F0 untuk unfilled)
    function updateSliderTrack(val) {
        const clamped = Math.max(0, Math.min(100, val));
        slider.style.setProperty('--range-progress', clamped + '%');
    }

    // Inisialisasi awal
    let initialValue = parseInt(input.value, 10);
    if (isNaN(initialValue) || initialValue < 0) initialValue = 0;
    if (initialValue > 100) initialValue = 100;
    
    slider.value = initialValue;
    input.value = initialValue;
    badge.textContent = initialValue;
    updateSliderTrack(initialValue);

    // Event ketika slider digeser
    slider.addEventListener('input', function() {
        const val = parseInt(this.value, 10) || 0;
        input.value = val;
        badge.textContent = val;
        updateSliderTrack(val);
        input.classList.remove('is-invalid');
        skorFeedback.classList.add('d-none');
    });

    // Event ketika pengguna mengetik nilai pada input angka
    input.addEventListener('input', function() {
        let rawVal = this.value;

        // Jika input dikosongkan sementara saat mengetik
        if (rawVal === '') {
            badge.textContent = '0';
            slider.value = 0;
            updateSliderTrack(0);
            return;
        }

        let numVal = parseInt(rawVal, 10);

        // Batasi nilai dari 0 hingga 100
        if (isNaN(numVal)) {
            numVal = 0;
            this.value = 0;
        } else if (numVal > 100) {
            numVal = 100;
            this.value = 100;
        } else if (numVal < 0) {
            numVal = 0;
            this.value = 0;
        }

        slider.value = numVal;
        badge.textContent = numVal;
        updateSliderTrack(numVal);
        input.classList.remove('is-invalid');
        skorFeedback.classList.add('d-none');
    });

    // Event ketika input angka kehilangan fokus (blur)
    input.addEventListener('blur', function() {
        if (this.value === '' || isNaN(parseInt(this.value, 10))) {
            this.value = 0;
            slider.value = 0;
            badge.textContent = 0;
            updateSliderTrack(0);
        }
    });

    // Validasi saat form dikirim
    form.addEventListener('submit', function(e) {
        let isValid = true;

        // Validasi Skor Prioritas
        const skorVal = input.value.trim();
        const num = parseInt(skorVal, 10);
        if (skorVal === '' || isNaN(num) || num < 0 || num > 100) {
            isValid = false;
            input.classList.add('is-invalid');
            skorFeedback.classList.remove('d-none');
            input.focus();
        } else {
            input.classList.remove('is-invalid');
            skorFeedback.classList.add('d-none');
        }

        // Validasi Status Autentikasi
        if (!statusSelect.value) {
            isValid = false;
            statusSelect.classList.add('is-invalid');
            statusFeedback.classList.remove('d-none');
            if (isValid) statusSelect.focus();
        } else {
            statusSelect.classList.remove('is-invalid');
            statusFeedback.classList.add('d-none');
        }

        if (!isValid) {
            e.preventDefault();
            return false;
        }
    });

    // Reset status error ketika dropdown status dipilih
    statusSelect.addEventListener('change', function() {
        if (this.value) {
            this.classList.remove('is-invalid');
            statusFeedback.classList.add('d-none');
        }
    });
});
</script>
<?= $this->endSection() ?>
