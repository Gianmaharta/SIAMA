<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/arsip-master.css?v=' . (file_exists(FCPATH . 'css/arsip-master.css') ? filemtime(FCPATH . 'css/arsip-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- 1. Header Halaman: Tombol Back Ringkas Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/arsip') ?>" class="arsip-btn-back-header" title="Kembali ke Daftar Arsip" aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="h3 fw-bold mb-0 text-dark" style="font-size: 22px; font-weight: 700;">Tambah Arsip</h1>
        <p class="text-secondary small mb-0 mt-1">Tambahkan data arsip digital baru ke dalam sistem.</p>
    </div>
</div>

<!-- Feedback Error Alerts -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Feedback Info jika terhubung dengan SPT -->
<?php if (!empty($spt_terpilih)) : ?>
    <div class="alert alert-info alert-dismissible fade show d-flex align-items-center gap-3 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-info-circle-fill fs-4 text-info"></i>
        <div>
            <h6 class="fw-bold mb-1" style="font-size: 13.5px;">Arsip Terhubung dengan SPT</h6>
            <div class="small">Nomor SPT: <strong><?= esc($spt_terpilih['nomor_spt']) ?></strong> &bull; Perihal: <?= esc($spt_terpilih['perihal']) ?></div>
        </div>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Form Tambah Arsip -->
<form action="<?= base_url('/arsip/store') ?>" method="post" enctype="multipart/form-data" id="formTambahArsip" class="needs-validation" novalidate>
    <?= csrf_field() ?>

    <!-- 2. CARD 1: INFORMASI ARSIP -->
    <div class="arsip-form-card">
        <div class="arsip-form-card-header">
            <h2 class="arsip-form-title">
                <i class="bi bi-file-earmark-text"></i>
                <span>Informasi Arsip</span>
            </h2>
        </div>
        <div class="arsip-form-card-body">
            <div class="row g-3 g-md-4">
                <!-- Baris 1: Nomor Arsip & Nama / Judul Arsip -->
                <div class="col-12 col-md-6">
                    <label for="nomor_arsip" class="arsip-form-label">
                        Nomor Arsip <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="nomor_arsip" 
                        name="nomor_arsip" 
                        value="<?= old('nomor_arsip') ?>" 
                        placeholder="Masukkan nomor arsip" 
                        maxlength="100" 
                        required
                    >
                    <div class="invalid-feedback">
                        Nomor arsip wajib diisi.
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="nama_arsip" class="arsip-form-label">
                        Nama / Judul Arsip <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="nama_arsip" 
                        name="nama_arsip" 
                        value="<?= old('nama_arsip') ?>" 
                        placeholder="Masukkan nama atau judul arsip" 
                        maxlength="255" 
                        required
                    >
                    <div class="invalid-feedback">
                        Nama atau judul arsip wajib diisi.
                    </div>
                </div>

                <!-- Baris 2: Kode Klasifikasi & Bidang -->
                <div class="col-12 col-md-6">
                    <label for="id_kode_klasifikasi" class="arsip-form-label">
                        Kode Klasifikasi <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="id_kode_klasifikasi" name="id_kode_klasifikasi" required>
                        <option value="">-- Pilih Kode Klasifikasi --</option>
                        <?php foreach ($klasifikasi_list as $klas) : ?>
                            <option value="<?= $klas['id_kode_klasifikasi'] ?>" 
                                <?= old('id_kode_klasifikasi') == $klas['id_kode_klasifikasi'] ? 'selected' : '' ?>>
                                <?= esc($klas['kode']) ?> – <?= esc($klas['nama_klasifikasi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Kode klasifikasi wajib dipilih.
                    </div>
                </div>

                <div class="col-12 col-md-6">
                    <label for="id_bidang" class="arsip-form-label">
                        Bidang <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="id_bidang" name="id_bidang" required>
                        <option value="">-- Pilih Bidang --</option>
                        <?php foreach ($bidang_list as $bid) : ?>
                            <option value="<?= $bid['id_bidang'] ?>" 
                                <?= old('id_bidang') == $bid['id_bidang'] ? 'selected' : '' ?>>
                                <?= esc($bid['nama_bidang']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="invalid-feedback">
                        Bidang wajib dipilih.
                    </div>
                </div>

                <!-- Baris 3: SPT Terkait & Tahun Penciptaan -->
                <div class="col-12 col-md-6">
                    <label for="id_spt" class="arsip-form-label">
                        SPT Terkait <span class="text-secondary fw-normal">(Opsional)</span>
                    </label>
                    <select class="form-select" id="id_spt" name="id_spt">
                        <option value="">-- Tidak ada SPT terkait --</option>
                        <?php 
                        $sptTerpilihId = !empty($spt_terpilih['id_spt']) ? $spt_terpilih['id_spt'] : old('id_spt'); 
                        ?>
                        <?php foreach ($spt_list as $spt) : ?>
                            <option value="<?= $spt['id_spt'] ?>" 
                                <?= $sptTerpilihId == $spt['id_spt'] ? 'selected' : '' ?>>
                                <?= esc($spt['nomor_spt']) ?> (<?= esc($spt['tanggal_spt']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label for="kurun_waktu" class="arsip-form-label">
                        Tahun Penciptaan <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input 
                            type="number" 
                            class="form-control" 
                            id="kurun_waktu" 
                            name="kurun_waktu" 
                            value="<?= old('kurun_waktu', date('Y')) ?>" 
                            placeholder="Masukkan tahun (contoh: 2023)" 
                            min="1900" 
                            max="<?= date('Y') + 1 ?>"
                            required
                        >
                        <span class="input-group-text bg-white border-start-0" id="btnCalendarKurun" role="button" title="Pilih Tahun" style="cursor: pointer;">
                            <i class="bi bi-calendar3 text-success"></i>
                        </span>
                        <div class="invalid-feedback">
                            Tahun penciptaan wajib diisi.
                        </div>
                    </div>
                </div>

                <!-- Baris 4: Kondisi Fisik -->
                <div class="col-12 col-md-6">
                    <label for="kondisi" class="arsip-form-label">
                        Kondisi Fisik <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="kondisi" name="kondisi" required>
                        <?php $currentKondisi = old('kondisi', 'Baik'); ?>
                        <option value="Baik" <?= $currentKondisi === 'Baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="Rusak Ringan" <?= $currentKondisi === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                        <option value="Rusak Berat" <?= $currentKondisi === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                    </select>
                    <div class="invalid-feedback">
                        Kondisi fisik wajib dipilih.
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 3. CARD 2: INFORMASI JADWAL RETENSI ARSIP (JRA) -->
    <div class="arsip-form-card">
        <div class="arsip-form-card-header">
            <h2 class="arsip-form-title">
                <i class="bi bi-journal-bookmark"></i>
                <span>Informasi Jadwal Retensi Arsip (JRA)</span>
            </h2>
            <p class="arsip-form-subtitle-blue">Terisi otomatis berdasarkan Kode Klasifikasi.</p>
        </div>
        <div class="arsip-form-card-body">
            <!-- Hidden input untuk masa retensi aktif (dikirim ke controller) -->
            <input type="hidden" id="hidden_retensi_aktif" name="retensi_aktif" value="0">

            <!-- Placeholder jika Kode Klasifikasi belum dipilih -->
            <div id="jra_placeholder_box" class="py-3 px-3 text-secondary text-center small rounded-3" style="background-color: #F8FAFC; border: 1px dashed #CBD5E1;">
                <i class="bi bi-info-circle me-1 text-primary"></i> Pilih kode klasifikasi untuk melihat informasi JRA.
            </div>

            <!-- Kontainer Field JRA (Read-Only) -->
            <div id="jra_fields_container" class="d-none">
                <div class="row g-3 g-md-4">
                    <!-- Baris 1: Retensi Aktif, Retensi Inaktif, Keterangan Retensi -->
                    <div class="col-12 col-md-4">
                        <label class="arsip-form-label">Retensi Aktif (Tahun)</label>
                        <input 
                            type="text" 
                            class="form-control arsip-readonly-control" 
                            id="jra_retensi_aktif" 
                            value="Tidak ada JRA" 
                            readonly
                        >
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="arsip-form-label">Retensi Inaktif (Tahun)</label>
                        <input 
                            type="text" 
                            class="form-control arsip-readonly-control" 
                            id="jra_retensi_inaktif" 
                            value="Tidak ada JRA" 
                            readonly
                        >
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="arsip-form-label">Keterangan Retensi</label>
                        <input 
                            type="text" 
                            class="form-control arsip-readonly-control" 
                            id="jra_keterangan" 
                            value="Tidak ada JRA" 
                            readonly
                        >
                    </div>

                    <!-- Baris 2: Tingkat Keamanan, Dasar Pertimbangan -->
                    <div class="col-12 col-md-4">
                        <label class="arsip-form-label">Tingkat Keamanan</label>
                        <input 
                            type="text" 
                            class="form-control arsip-readonly-control" 
                            id="jra_keamanan" 
                            value="Tidak ada JRA" 
                            readonly
                        >
                    </div>

                    <div class="col-12 col-md-4">
                        <label class="arsip-form-label">Dasar Pertimbangan</label>
                        <input 
                            type="text" 
                            class="form-control arsip-readonly-control" 
                            id="jra_dasar" 
                            value="Tidak ada JRA" 
                            readonly
                        >
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 4. CARD 3: BERKAS DIGITAL (HANYA SATU SECTION) -->
    <div class="arsip-form-card">
        <div class="arsip-form-card-header">
            <h2 class="arsip-form-title">
                <i class="bi bi-paperclip"></i>
                <span>Berkas Digital</span>
            </h2>
        </div>
        <div class="arsip-form-card-body">
            <div class="row g-4 align-items-start">
                <!-- Sisi Kiri: Upload Berkas Digital & Preview Info -->
                <div class="col-12 col-lg-7">
                    <label for="file_arsip" class="arsip-form-label">
                        Upload Berkas Digital <span class="fw-normal text-secondary">(PDF, JPG, JPEG, PNG, TIFF — maks. 10 MB)</span>
                    </label>
                    <input 
                        type="file" 
                        class="form-control" 
                        id="file_arsip" 
                        name="file_arsip" 
                        accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif"
                    >
                    <div class="form-text small text-secondary mt-2" style="font-size: 12.5px;">
                        Format file yang didukung: <strong>PDF, JPG, JPEG, PNG, TIFF</strong>. Maksimal ukuran <strong>10 MB</strong>.
                    </div>

                    <!-- Box Preview File yang dipilih user -->
                    <div id="file_preview_box" class="d-none mt-3 p-3 rounded-3 d-flex align-items-center gap-3" style="background-color: #F8FAFC; border: 1px solid #E2E8F0;">
                        <div id="file_preview_icon" class="fs-2 text-danger">
                            <i class="bi bi-file-earmark-pdf"></i>
                        </div>
                        <div class="flex-grow-1 small">
                            <div class="fw-bold text-dark" id="file_preview_name" style="word-break: break-all;">berkas.pdf</div>
                            <div class="text-secondary" id="file_preview_info">PDF Document &bull; 1.2 MB</div>
                        </div>
                        <button type="button" class="btn btn-sm btn-light border text-danger" id="btnRemoveFile" title="Hapus Berkas Pilihan">
                            <i class="bi bi-x-lg"></i>
                        </button>
                    </div>
                </div>

                <!-- Sisi Kanan: Panel Opsi Keamanan PDF (Watermark) -->
                <div class="col-12 col-lg-5">
                    <div class="arsip-watermark-panel" id="watermark_box">
                        <div class="arsip-watermark-title d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-primary"></i>
                            <span>Opsi Keamanan PDF (Watermark)</span>
                        </div>
                        <div class="form-check mt-2">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="generate_watermark" 
                                value="1" 
                                id="generate_watermark" 
                                checked
                            >
                            <label class="form-check-label fw-semibold text-dark small" for="generate_watermark">
                                Generate Watermark Sistem (Khusus PDF)
                            </label>
                        </div>
                        <p class="small text-primary mb-0 mt-1" id="watermark_helper_text" style="font-size: 11.5px; line-height: 1.45; color: #1E60EC !important;">
                            Centang untuk menerapkan watermark SIAMA pada file PDF yang diunggah.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Tombol Aksi Simpan Arsip & Batal -->
    <div class="d-flex align-items-center gap-2 mt-4 mb-5">
        <button type="submit" class="btn arsip-btn-save" id="btnSimpanArsip">
            <i class="bi bi-floppy"></i>
            <span>Simpan Arsip</span>
        </button>
        <a href="<?= base_url('/arsip') ?>" class="btn arsip-btn-cancel" id="btnBatalArsip">
            Batal
        </a>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // 1. Sinkronisasi Data JRA Otomatis saat Kode Klasifikasi Berubah
    const klasifikasiSelect = document.getElementById('id_kode_klasifikasi');
    const jraPlaceholderBox = document.getElementById('jra_placeholder_box');
    const jraFieldsContainer = document.getElementById('jra_fields_container');
    const jraRetensiAktif = document.getElementById('jra_retensi_aktif');
    const hiddenRetensiAktif = document.getElementById('hidden_retensi_aktif');
    const jraRetensiInaktif = document.getElementById('jra_retensi_inaktif');
    const jraKeterangan = document.getElementById('jra_keterangan');
    const jraKeamanan = document.getElementById('jra_keamanan');
    const jraDasar = document.getElementById('jra_dasar');

    function updateJraView(data) {
        if (!data || !data.retensi_aktif) {
            jraPlaceholderBox.classList.remove('d-none');
            jraFieldsContainer.classList.add('d-none');
            if (hiddenRetensiAktif) hiddenRetensiAktif.value = '0';
            return;
        }

        jraPlaceholderBox.classList.add('d-none');
        jraFieldsContainer.classList.remove('d-none');

        jraRetensiAktif.value = data.retensi_aktif ? data.retensi_aktif + ' Tahun' : 'Tidak ada JRA';
        if (hiddenRetensiAktif) hiddenRetensiAktif.value = data.retensi_aktif || '0';
        jraRetensiInaktif.value = data.retensi_inaktif ? data.retensi_inaktif + ' Tahun' : 'Tidak ada JRA';
        jraKeterangan.value = data.keterangan_retensi || 'Tidak ada JRA';
        jraKeamanan.value = data.klasifikasi_keamanan || 'Tidak ada JRA';
        jraDasar.value = data.dasar_pertimbangan || 'Tidak ada JRA';
    }

    function fetchJraData(idKlasifikasi) {
        if (!idKlasifikasi) {
            updateJraView(null);
            return;
        }

        fetch('<?= base_url('jra/get_by_klasifikasi/') ?>' + encodeURIComponent(idKlasifikasi))
            .then(response => response.json())
            .then(res => {
                if (res.status === 'success' && res.data) {
                    updateJraView(res.data);
                } else {
                    // Jika klasifikasi dipilih tetapi belum ada data JRA master
                    jraPlaceholderBox.classList.add('d-none');
                    jraFieldsContainer.classList.remove('d-none');
                    jraRetensiAktif.value = 'Tidak ada JRA';
                    if (hiddenRetensiAktif) hiddenRetensiAktif.value = '0';
                    jraRetensiInaktif.value = 'Tidak ada JRA';
                    jraKeterangan.value = 'Tidak ada JRA';
                    jraKeamanan.value = 'Tidak ada JRA';
                    jraDasar.value = 'Tidak ada JRA';
                }
            })
            .catch(err => {
                console.error('Error fetching JRA:', err);
                updateJraView(null);
            });
    }

    if (klasifikasiSelect) {
        klasifikasiSelect.addEventListener('change', function() {
            fetchJraData(this.value);
        });

        if (klasifikasiSelect.value) {
            fetchJraData(klasifikasiSelect.value);
        }
    }

    // 2. Klik Icon Kalender untuk Fokus & Pilih Tahun
    const btnCalendar = document.getElementById('btnCalendarKurun');
    const inputKurun = document.getElementById('kurun_waktu');
    if (btnCalendar && inputKurun) {
        btnCalendar.addEventListener('click', function() {
            inputKurun.focus();
            if (typeof inputKurun.showPicker === 'function') {
                try {
                    inputKurun.showPicker();
                } catch(e) {}
            }
        });
    }

    // 3. File Input Preview & Watermark Checkbox Behavior
    const fileInput = document.getElementById('file_arsip');
    const previewBox = document.getElementById('file_preview_box');
    const previewName = document.getElementById('file_preview_name');
    const previewInfo = document.getElementById('file_preview_info');
    const previewIcon = document.getElementById('file_preview_icon');
    const btnRemoveFile = document.getElementById('btnRemoveFile');
    const checkWatermark = document.getElementById('generate_watermark');
    const wmHelperText = document.getElementById('watermark_helper_text');

    if (fileInput) {
        fileInput.addEventListener('change', function() {
            const file = this.files[0];
            if (!file) {
                previewBox.classList.add('d-none');
                return;
            }

            // Hitung ukuran
            let sizeStr = '';
            if (file.size >= 1048576) {
                sizeStr = (file.size / 1048576).toFixed(2) + ' MB';
            } else if (file.size >= 1024) {
                sizeStr = (file.size / 1024).toFixed(2) + ' KB';
            } else {
                sizeStr = file.size + ' Bytes';
            }

            const ext = file.name.split('.').pop().toLowerCase();
            const isPdf = (ext === 'pdf');

            previewName.textContent = file.name;
            previewInfo.textContent = ext.toUpperCase() + ' Document • ' + sizeStr;

            if (isPdf) {
                previewIcon.className = 'fs-2 text-danger';
                previewIcon.innerHTML = '<i class="bi bi-file-earmark-pdf"></i>';
                if (checkWatermark) {
                    checkWatermark.disabled = false;
                    checkWatermark.checked = true;
                }
                if (wmHelperText) {
                    wmHelperText.textContent = 'Centang untuk menerapkan watermark SIAMA pada file PDF yang diunggah.';
                    wmHelperText.classList.remove('text-muted');
                    wmHelperText.classList.add('text-primary');
                }
            } else {
                previewIcon.className = 'fs-2 text-primary';
                previewIcon.innerHTML = '<i class="bi bi-file-earmark-image"></i>';
                if (checkWatermark) {
                    checkWatermark.checked = false;
                    checkWatermark.disabled = true;
                }
                if (wmHelperText) {
                    wmHelperText.textContent = 'Watermark sistem hanya tersedia untuk berkas berformat PDF.';
                    wmHelperText.classList.remove('text-primary');
                    wmHelperText.classList.add('text-muted');
                }
            }

            previewBox.classList.remove('d-none');
        });
    }

    if (btnRemoveFile && fileInput) {
        btnRemoveFile.addEventListener('click', function() {
            fileInput.value = '';
            previewBox.classList.add('d-none');
            if (checkWatermark) {
                checkWatermark.disabled = false;
                checkWatermark.checked = true;
            }
            if (wmHelperText) {
                wmHelperText.textContent = 'Centang untuk menerapkan watermark SIAMA pada file PDF yang diunggah.';
                wmHelperText.classList.remove('text-muted');
                wmHelperText.classList.add('text-primary');
            }
        });
    }

    // 4. Client-side Form Validation
    const form = document.getElementById('formTambahArsip');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        }, false);
    }
});
</script>
<?= $this->endSection() ?>
