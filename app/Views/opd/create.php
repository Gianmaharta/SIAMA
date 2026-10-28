<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Data OPD - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/opd-master.css?v=' . (file_exists(FCPATH . 'css/opd-master.css') ? filemtime(FCPATH . 'css/opd-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

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

<!-- Header Halaman: Tombol Back Sejajar Judul -->
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= base_url('/opd') ?>" class="btn-back-title" title="Kembali ke Daftar OPD" aria-label="Kembali">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h1 class="opd-page-title mb-0">Tambah Data OPD</h1>
        <p class="opd-page-subtitle mb-0">Daftarkan Organisasi Perangkat Daerah baru ke dalam sistem SIAMA Kabupaten Buleleng.</p>
    </div>
</div>

<!-- Card Form Tambah OPD -->
<div class="row">
    <div class="col-12 col-lg-8">
        <div class="opd-table-card p-4">
                <?= csrf_field() ?>

                <div class="mb-3">
                    <label class="opd-form-label text-muted">ID OPD (UUID)</label>
                    <input 
                        type="text" 
                        class="opd-form-control bg-light font-monospace text-muted" 
                        value="(Dihasilkan otomatis oleh sistem)" 
                        readonly 
                        disabled
                        tabindex="-1"
                        style="cursor: not-allowed;"
                    >
                    <div class="opd-form-help">ID UUID dibuat otomatis oleh sistem saat data disimpan (tidak perlu diisi manual).</div>
                </div>

                <!-- Kode OPD (Otomatis Dihasilkan oleh Sistem) -->
                <div class="mb-3">
                    <label class="opd-form-label text-muted">Kode OPD</label>
                    <input 
                        type="text" 
                        class="opd-form-control bg-light font-monospace text-muted" 
                        value="<?= esc($nextKodeOpd ?? 'Otomatis oleh sistem') ?>" 
                        readonly 
                        disabled
                        tabindex="-1"
                        style="cursor: not-allowed;"
                    >
                    <input type="hidden" name="kode_opd" id="kode_opd" value="<?= esc($nextKodeOpd ?? '') ?>">
                </div>

                <div class="mb-3">
                    <label for="nama_opd" class="opd-form-label">
                        Nama OPD <span class="text-danger">*</span>
                    </label>
                    <div class="dropdown opd-select-dropdown position-relative" id="dropdownWrapNamaOpd">
                        <button 
                            type="button" 
                            class="opd-form-control opd-form-select text-start d-flex align-items-center justify-content-between" 
                            id="btnDropdownNamaOpd" 
                            data-bs-toggle="dropdown" 
                            data-bs-display="static" 
                            aria-expanded="false"
                        >
                            <span id="labelSelectedNamaOpd" class="text-truncate text-muted">
                                <?= old('nama_opd') ? esc(old('nama_opd')) : '-- Pilih Nama OPD --' ?>
                            </span>
                        </button>
                        <input type="hidden" name="nama_opd" id="nama_opd" value="<?= old('nama_opd') ?>" required>

                        <div class="dropdown-menu w-100 p-2 opd-dropdown-menu shadow" aria-labelledby="btnDropdownNamaOpd">
                            <div class="px-1 pb-2 border-bottom mb-2">
                                <div class="position-relative w-100">
                                    <i class="bi bi-search position-absolute top-50 start-0 translate-middle-y ms-2 text-muted" style="font-size: 12px; pointer-events: none;"></i>
                                    <input 
                                        type="text" 
                                        id="searchNamaOpd" 
                                        class="form-control form-control-sm" 
                                        placeholder="Cari dinas / badan..."
                                        style="font-size: 12px; height: 32px; border-radius: 6px; padding-left: 28px;"
                                    >
                                </div>
                            </div>
                            <div class="opd-dropdown-items-list" style="max-height: 200px; overflow-y: auto;">
                                <div class="opd-dropdown-header">DINAS DAERAH</div>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Komunikasi, Informatika, Persandian dan Statistik">Dinas Komunikasi, Informatika, Persandian dan Statistik</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pendidikan, Pemuda dan Olahraga">Dinas Pendidikan, Pemuda dan Olahraga</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Kesehatan">Dinas Kesehatan</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pekerjaan Umum, Penataan Ruang, Perumahan dan Kawasan Permukiman">Dinas Pekerjaan Umum, Penataan Ruang, Perumahan dan Kawasan Permukiman</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Arsip dan Perpustakaan Daerah">Dinas Arsip dan Perpustakaan Daerah</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Lingkungan Hidup">Dinas Lingkungan Hidup</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Sosial, Pemberdayaan Perempuan dan Perlindungan Anak">Dinas Sosial, Pemberdayaan Perempuan dan Perlindungan Anak</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pertanian, Ketahanan Pangan dan Perikanan">Dinas Pertanian, Ketahanan Pangan dan Perikanan</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Perhubungan">Dinas Perhubungan</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pariwisata">Dinas Pariwisata</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Kebudayaan">Dinas Kebudayaan</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Perdagangan, Perindustrian dan Koperasi, UKM">Dinas Perdagangan, Perindustrian dan Koperasi, UKM</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Tenaga Kerja">Dinas Tenaga Kerja</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Kependudukan dan Pencatatan Sipil">Dinas Kependudukan dan Pencatatan Sipil</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Pemadam Kebakaran dan Penyelamatan">Dinas Pemadam Kebakaran dan Penyelamatan</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu">Dinas Penanaman Modal dan Pelayanan Terpadu Satu Pintu</button>

                                <div class="opd-dropdown-header mt-2">BADAN DAERAH</div>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Perencanaan Pembangunan Daerah">Badan Perencanaan Pembangunan Daerah</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Pengelolaan Keuangan dan Pendapatan Daerah">Badan Pengelolaan Keuangan dan Pendapatan Daerah</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Kepegawaian dan Pengembangan Sumber Daya Manusia">Badan Kepegawaian dan Pengembangan Sumber Daya Manusia</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Penanggulangan Bencana Daerah">Badan Penanggulangan Bencana Daerah</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Badan Kesatuan Bangsa dan Politik">Badan Kesatuan Bangsa dan Politik</button>

                                <div class="opd-dropdown-header mt-2">SEKRETARIAT & LEMBAGA</div>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Inspektorat Daerah">Inspektorat Daerah</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Satuan Polisi Pamong Praja">Satuan Polisi Pamong Praja</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Sekretariat Daerah">Sekretariat Daerah</button>
                                <button type="button" class="dropdown-item opd-dropdown-item" data-value="Sekretariat DPRD">Sekretariat DPRD</button>

                                <div class="dropdown-divider my-2"></div>
                                <button type="button" class="dropdown-item opd-dropdown-item text-primary fw-semibold" data-value="__custom__">
                                    <i class="bi bi-pencil-square me-1"></i> + Ketik Nama OPD Manual...
                                </button>
                            </div>
                        </div>
                    </div>
                    <div id="wrapCustomNamaOpd" class="mt-2 d-none">
                        <input 
                            type="text" 
                            id="custom_nama_opd" 
                            class="opd-form-control" 
                            placeholder="Ketik nama instansi OPD baru..." 
                        >
                    </div>
                </div>

                <div class="mb-3">
                    <label for="kuota_storage_mb" class="opd-form-label">
                        Kuota Storage (MB) <span class="text-danger">*</span>
                    </label>
                    <input 
                        type="number" 
                        name="kuota_storage_mb" 
                        id="kuota_storage_mb" 
                        class="opd-form-control" 
                        value="<?= old('kuota_storage_mb', 1000) ?>" 
                        min="100" 
                        step="100" 
                        required
                    >
                    <div class="opd-form-help">Batas kuota penyimpanan berkas arsip dalam satuan Megabyte.</div>
                </div>

                <div class="mb-4">
                    <label for="is_active" class="opd-form-label">Status Keaktifan</label>
                    <select name="is_active" id="is_active" class="opd-form-control opd-form-select">
                        <option value="1" <?= old('is_active', '1') == '1' ? 'selected' : '' ?>>Aktif</option>
                        <option value="0" <?= old('is_active') == '0' ? 'selected' : '' ?>>Non Aktif</option>
                    </select>
                </div>

                <div class="d-flex align-items-center gap-2 pt-3 border-top">
                    <button type="submit" class="opd-btn-add px-4">
                        <i class="bi bi-check2 me-1"></i> Simpan Data OPD
                    </button>
                    <a href="<?= base_url('/opd') ?>" class="btn btn-light border px-4" style="height: 42px; border-radius: 8px; font-weight: 500; display: inline-flex; align-items: center;">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnDropdownNama = document.getElementById('btnDropdownNamaOpd');
    const labelSelectedNama = document.getElementById('labelSelectedNamaOpd');
    const hiddenNamaInput = document.getElementById('nama_opd');
    const wrapCustomNama = document.getElementById('wrapCustomNamaOpd');
    const customNamaInput = document.getElementById('custom_nama_opd');
    const form = hiddenNamaInput ? hiddenNamaInput.closest('form') : null;
    const searchNamaOpd = document.getElementById('searchNamaOpd');

    if (searchNamaOpd) {
        searchNamaOpd.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        searchNamaOpd.addEventListener('input', function() {
            const q = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('#dropdownWrapNamaOpd .opd-dropdown-item');
            items.forEach(function(item) {
                if (item.getAttribute('data-value') === '__custom__') return;
                const text = item.textContent.toLowerCase();
                item.style.display = text.includes(q) ? 'block' : 'none';
            });
        });
    }

    document.addEventListener('click', function(e) {
        const item = e.target.closest('#dropdownWrapNamaOpd .opd-dropdown-item');
        if (item) {
            const val = item.getAttribute('data-value');
            if (val === '__custom__') {
                if (labelSelectedNama) {
                    labelSelectedNama.textContent = '+ Ketik Nama OPD Manual...';
                    labelSelectedNama.classList.remove('text-muted');
                }
                if (hiddenNamaInput) hiddenNamaInput.value = '';
                if (wrapCustomNama) wrapCustomNama.classList.remove('d-none');
                if (customNamaInput) {
                    customNamaInput.focus();
                    customNamaInput.required = true;
                }
            } else {
                if (labelSelectedNama) {
                    labelSelectedNama.textContent = val;
                    labelSelectedNama.classList.remove('text-muted');
                }
                if (hiddenNamaInput) hiddenNamaInput.value = val;
                if (wrapCustomNama) wrapCustomNama.classList.add('d-none');
                if (customNamaInput) {
                    customNamaInput.required = false;
                    customNamaInput.value = '';
                }
            }

            if (btnDropdownNama) {
                const dd = bootstrap.Dropdown.getInstance(btnDropdownNama) || new bootstrap.Dropdown(btnDropdownNama);
                dd.hide();
            }
        }
    });

    if (form) {
        form.addEventListener('submit', function(e) {
            if (wrapCustomNama && !wrapCustomNama.classList.contains('d-none')) {
                const customVal = customNamaInput ? customNamaInput.value.trim() : '';
                if (!customVal) {
                    e.preventDefault();
                    if (customNamaInput) customNamaInput.focus();
                    return;
                }
                if (hiddenNamaInput) hiddenNamaInput.value = customVal;
            } else if (!hiddenNamaInput.value) {
                e.preventDefault();
                if (btnDropdownNama) btnDropdownNama.focus();
                alert('Silakan pilih Nama OPD terlebih dahulu.');
            }
        });
    }
});
</script>
<?= $this->endSection() ?>
