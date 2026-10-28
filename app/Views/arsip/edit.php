<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Arsip - SIAMA
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
        <h1 class="h3 fw-bold mb-0 text-dark" style="font-size: 22px; font-weight: 700;">Edit Arsip</h1>
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

<!-- Form Edit Arsip -->
<form action="<?= base_url('/arsip/update/' . $arsip['id_arsip']) ?>" method="post" enctype="multipart/form-data" id="formEditArsip">
    <?= csrf_field() ?>

    <!-- 2. CARD 1: INFORMASI UTAMA -->
    <div class="arsip-form-card">
        <div class="arsip-form-card-header">
            <h2 class="arsip-form-title">
                <i class="bi bi-file-earmark-text"></i>
                <span>Informasi Utama</span>
            </h2>
        </div>
        <div class="arsip-form-card-body">
            <div class="row g-3 g-md-4">
                <!-- Baris 1: Nomor Arsip, Nama / Judul Arsip, Kode Klasifikasi -->
                <div class="col-12 col-md-4">
                    <label for="nomor_arsip" class="arsip-form-label">
                        Nomor Arsip <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="nomor_arsip" 
                        name="nomor_arsip" 
                        value="<?= old('nomor_arsip', esc($arsip['nomor_arsip'])) ?>" 
                        placeholder="Contoh: ARS/TI/2026/876" 
                        maxlength="100" 
                        required
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="nama_arsip" class="arsip-form-label">
                        Nama / Judul Arsip <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="text" 
                        class="form-control" 
                        id="nama_arsip" 
                        name="nama_arsip" 
                        value="<?= old('nama_arsip', esc($arsip['nama_arsip'])) ?>" 
                        placeholder="Masukkan judul berkas arsip..." 
                        maxlength="255" 
                        required
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label for="id_kode_klasifikasi" class="arsip-form-label">
                        Kode Klasifikasi <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="id_kode_klasifikasi" name="id_kode_klasifikasi" required>
                        <option value="">-- Pilih Kode Klasifikasi --</option>
                        <?php foreach ($klasifikasi_list as $klas) : ?>
                            <option value="<?= $klas['id_kode_klasifikasi'] ?>" 
                                <?= (old('id_kode_klasifikasi', $arsip['id_kode_klasifikasi']) == $klas['id_kode_klasifikasi']) ? 'selected' : '' ?>>
                                <?= esc($klas['kode']) ?> – <?= esc($klas['nama_klasifikasi']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Baris 2: Bidang, SPT Terkait, Tahun Penciptaan -->
                <div class="col-12 col-md-4">
                    <label for="id_bidang" class="arsip-form-label">
                        Bidang <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="id_bidang" name="id_bidang" required>
                        <option value="">-- Pilih Bidang --</option>
                        <?php foreach ($bidang_list as $bid) : ?>
                            <option value="<?= $bid['id_bidang'] ?>" 
                                <?= (old('id_bidang', $arsip['id_bidang']) == $bid['id_bidang']) ? 'selected' : '' ?>>
                                <?= esc($bid['nama_bidang']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="id_spt" class="arsip-form-label">
                        SPT Terkait <span class="text-secondary fw-normal">(Opsional)</span>
                    </label>
                    <select class="form-select" id="id_spt" name="id_spt">
                        <option value="">-- Tidak ada SPT terkait --</option>
                        <?php foreach ($spt_list as $spt) : ?>
                            <option value="<?= $spt['id_spt'] ?>" 
                                <?= (old('id_spt', $arsip['id_spt']) == $spt['id_spt']) ? 'selected' : '' ?>>
                                <?= esc($spt['nomor_spt']) ?> (<?= esc($spt['tanggal_spt']) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="kurun_waktu" class="arsip-form-label">
                        Tahun Penciptaan <span class="text-danger">*</span>
                    </label>
                    <div class="input-group">
                        <input 
                            type="number" 
                            class="form-control" 
                            id="kurun_waktu" 
                            name="kurun_waktu" 
                            value="<?= old('kurun_waktu', esc($arsip['kurun_waktu'])) ?>" 
                            placeholder="YYYY" 
                            min="1900" 
                            max="<?= date('Y') + 1 ?>"
                            required
                        >
                        <span class="input-group-text bg-white border-start-0" id="btnCalendarKurun" role="button" title="Pilih Tahun" style="cursor: pointer;">
                            <i class="bi bi-calendar3 text-success"></i>
                        </span>
                    </div>
                </div>

                <!-- Baris 3: Kondisi Fisik, Status Verifikasi -->
                <div class="col-12 col-md-4">
                    <label for="kondisi" class="arsip-form-label">
                        Kondisi Fisik <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="kondisi" name="kondisi">
                        <?php $currentKondisi = old('kondisi', $arsip['kondisi'] ?? 'Baik'); ?>
                        <option value="Baik" <?= $currentKondisi === 'Baik' ? 'selected' : '' ?>>Baik</option>
                        <option value="Rusak Ringan" <?= $currentKondisi === 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
                        <option value="Rusak Berat" <?= $currentKondisi === 'Rusak Berat' ? 'selected' : '' ?>>Rusak Berat</option>
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label for="status_verifikasi" class="arsip-form-label">
                        Status Verifikasi <span class="text-danger">*</span>
                    </label>
                    <?php 
                    $currentStatus = old('status_verifikasi', $arsip['status_verifikasi'] ?? 'Draft'); 
                    $isArsiparis = ($user_role ?? session()->get('nama_role')) === 'Arsiparis';
                    $isStatusLocked = in_array(strtolower($currentStatus), ['selesai', 'terverifikasi', 'disetujui']);
                    ?>
                    <?php if ($isArsiparis && $isStatusLocked) : ?>
                        <!-- Status Terkunci (Read-Only) Bagi Arsiparis jika sudah diverifikasi oleh Pejabat -->
                        <input type="text" class="form-control arsip-readonly-control" value="<?= esc($currentStatus) ?>" readonly>
                        <input type="hidden" name="status_verifikasi" value="<?= esc($currentStatus) ?>">
                    <?php else : ?>
                        <select class="form-select" id="status_verifikasi" name="status_verifikasi">
                            <option value="Draft" <?= strtolower($currentStatus) === 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="Menunggu" <?= strtolower($currentStatus) === 'menunggu' ? 'selected' : '' ?>>Menunggu</option>
                            <option value="Dalam Proses" <?= in_array(strtolower($currentStatus), ['dalam proses', 'dalam_proses', 'proses']) ? 'selected' : '' ?>>Dalam Proses</option>
                            <option value="Selesai" <?= in_array(strtolower($currentStatus), ['selesai', 'disetujui', 'terverifikasi']) ? 'selected' : '' ?>>Selesai</option>
                            <option value="Ditolak" <?= in_array(strtolower($currentStatus), ['ditolak', 'revisi']) ? 'selected' : '' ?>>Ditolak / Revisi</option>
                        </select>
                    <?php endif; ?>
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
            <!-- Hidden input untuk kalkulasi masa retensi aktif di controller -->
            <input type="hidden" id="hidden_retensi_aktif" name="retensi_aktif" value="<?= esc($initial_jra['retensi_aktif'] ?? '0') ?>">

            <div class="row g-3 g-md-4">
                <!-- Baris 1: Retensi Aktif, Retensi Inaktif, Keterangan Retensi -->
                <div class="col-12 col-md-4">
                    <label class="arsip-form-label">Retensi Aktif (Tahun)</label>
                    <input 
                        type="text" 
                        class="form-control arsip-readonly-control" 
                        id="jra_retensi_aktif" 
                        value="<?= !empty($initial_jra['retensi_aktif']) ? esc($initial_jra['retensi_aktif']) . ' Tahun' : 'Tidak ada JRA' ?>" 
                        readonly
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label class="arsip-form-label">Retensi Inaktif (Tahun)</label>
                    <input 
                        type="text" 
                        class="form-control arsip-readonly-control" 
                        id="jra_retensi_inaktif" 
                        value="<?= !empty($initial_jra['retensi_inaktif']) ? esc($initial_jra['retensi_inaktif']) . ' Tahun' : 'Tidak ada JRA' ?>" 
                        readonly
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label class="arsip-form-label">Keterangan Retensi</label>
                    <input 
                        type="text" 
                        class="form-control arsip-readonly-control" 
                        id="jra_keterangan" 
                        value="<?= !empty($initial_jra['keterangan_retensi']) ? esc($initial_jra['keterangan_retensi']) : 'Tidak ada JRA' ?>" 
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
                        value="<?= !empty($initial_jra['klasifikasi_keamanan']) ? esc($initial_jra['klasifikasi_keamanan']) : 'Tidak ada JRA' ?>" 
                        readonly
                    >
                </div>

                <div class="col-12 col-md-4">
                    <label class="arsip-form-label">Dasar Pertimbangan</label>
                    <input 
                        type="text" 
                        class="form-control arsip-readonly-control" 
                        id="jra_dasar" 
                        value="<?= !empty($initial_jra['dasar_pertimbangan']) ? esc($initial_jra['dasar_pertimbangan']) : 'Tidak ada JRA' ?>" 
                        readonly
                    >
                </div>
            </div>
        </div>
    </div>

    <!-- 4. CARD 3: BERKAS DIGITAL & WATERMARK -->
    <div class="arsip-form-card">
        <div class="arsip-form-card-header">
            <h2 class="arsip-form-title">
                <i class="bi bi-paperclip"></i>
                <span>Berkas Digital</span>
            </h2>
        </div>
        <div class="arsip-form-card-body">
            <div class="row g-4 align-items-start">
                <!-- Sisi Kiri: Ganti Berkas & Info File Saat Ini -->
                <div class="col-12 col-lg-8">
                    <label for="file_arsip" class="arsip-form-label">
                        Ganti Berkas Digital <span class="fw-normal text-secondary">(PDF, JPG, JPEG, PNG, TIFF – maks. 10 MB)</span>
                    </label>
                    <input 
                        type="file" 
                        class="form-control" 
                        id="file_arsip" 
                        name="file_arsip" 
                        accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif"
                    >
                    <div class="small text-secondary mt-2 d-flex align-items-center gap-1" style="font-size: 12.5px;">
                        <i class="bi bi-clock-history"></i>
                        <span>Kosongkan jika tidak ingin mengganti berkas.</span>
                    </div>

                    <!-- Informasi File Yang Sedang Tersimpan -->
                    <div class="mt-2" style="font-size: 13px;">
                        <?php if (!empty($file_info)) : ?>
                            <span class="text-secondary">File saat ini:</span>
                            <a href="<?= esc($file_info['url']) ?>" target="_blank" class="text-primary text-decoration-underline fw-semibold ms-1" title="Lihat Berkas Digital">
                                <?= esc($file_info['name']) ?>
                            </a>
                            <span class="badge bg-light text-secondary border ms-1 py-1 px-2" style="font-size: 11px;">
                                <?= esc($file_info['format']) ?> • <?= esc($file_info['size']) ?>
                            </span>
                            <?php if (!empty($file_info['is_wm'])) : ?>
                                <span class="badge bg-success-subtle text-success border border-success-subtle ms-1 py-1 px-2" style="font-size: 11px;">
                                    <i class="bi bi-shield-check me-1"></i>Watermarked
                                </span>
                            <?php endif; ?>
                        <?php else : ?>
                            <span class="text-secondary"><i class="bi bi-info-circle me-1"></i>Belum ada berkas digital</span>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Sisi Kanan: Panel Opsi Keamanan PDF (Watermark) -->
                <div class="col-12 col-lg-4">
                    <div class="arsip-watermark-panel">
                        <div class="arsip-watermark-title">Opsi Keamanan PDF (Watermark)</div>
                        <div class="form-check mt-2">
                            <input 
                                class="form-check-input" 
                                type="checkbox" 
                                name="generate_watermark" 
                                value="1" 
                                id="generate_watermark" 
                                <?= (!empty($arsip['is_watermarked']) || old('generate_watermark') === '1') ? 'checked' : '' ?>
                            >
                            <label class="form-check-label fw-semibold text-dark small" for="generate_watermark">
                                Generate Watermark Sistem (Khusus PDF)
                            </label>
                        </div>
                        <p class="small text-primary mb-0 mt-1" style="font-size: 11.5px; line-height: 1.45; color: #1E60EC !important;">
                            Centang untuk menerapkan watermark SIAMA pada file yang diunggah.
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- 5. Tombol Aksi Simpan & Batal -->
    <div class="d-flex align-items-center gap-2 mt-4 mb-5">
        <button type="submit" class="btn arsip-btn-save" id="btnSimpanArsip">
            <i class="bi bi-floppy"></i>
            <span>Simpan Perubahan</span>
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
    const jraRetensiAktif = document.getElementById('jra_retensi_aktif');
    const hiddenRetensiAktif = document.getElementById('hidden_retensi_aktif');
    const jraRetensiInaktif = document.getElementById('jra_retensi_inaktif');
    const jraKeterangan = document.getElementById('jra_keterangan');
    const jraKeamanan = document.getElementById('jra_keamanan');
    const jraDasar = document.getElementById('jra_dasar');

    function updateJraView(data) {
        if (data && data.retensi_aktif !== undefined) {
            jraRetensiAktif.value = data.retensi_aktif ? data.retensi_aktif + ' Tahun' : 'Tidak ada JRA';
            if (hiddenRetensiAktif) hiddenRetensiAktif.value = data.retensi_aktif || '0';
            jraRetensiInaktif.value = data.retensi_inaktif ? data.retensi_inaktif + ' Tahun' : 'Tidak ada JRA';
            jraKeterangan.value = data.keterangan_retensi || 'Tidak ada JRA';
            jraKeamanan.value = data.klasifikasi_keamanan || 'Tidak ada JRA';
            jraDasar.value = data.dasar_pertimbangan || 'Tidak ada JRA';
        } else {
            jraRetensiAktif.value = 'Tidak ada JRA';
            if (hiddenRetensiAktif) hiddenRetensiAktif.value = '0';
            jraRetensiInaktif.value = 'Tidak ada JRA';
            jraKeterangan.value = 'Tidak ada JRA';
            jraKeamanan.value = 'Tidak ada JRA';
            jraDasar.value = 'Tidak ada JRA';
        }
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
                    updateJraView(null);
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
    }

    // 2. Klik Icon Kalender / Button untuk Fokus Input Tahun
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
});
</script>
<?= $this->endSection() ?>
