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
                    <?php $selectedSpt = old('id_spt', $selected_spt_id ?? ''); ?>
                    <?php if (!empty($spt_list)) : ?>
                        <?php foreach ($spt_list as $spt) : ?>
                            <option value="<?= esc($spt['id_spt']) ?>" <?= $selectedSpt === $spt['id_spt'] ? 'selected' : '' ?>>
                                <?= esc($spt['nomor_spt']) ?> &ndash; <?= esc($spt['perihal']) ?> (<?= esc($spt['tanggal_spt']) ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
                <div class="form-text small text-secondary mt-1">
                    Pilih Surat Perintah Tugas (SPT) untuk memfilter dan memilih otomatis arsip yang sudah terhubung dengan SPT tersebut.
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

        <div class="px-4 py-2 bg-light border-bottom text-secondary small" id="arsipFilterInfo">
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
                        <th>SPT Terkait</th>
                        <th>Kurun Waktu</th>
                        <th style="padding-right: 20px;">Kondisi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($arsip_list)) : ?>
                        <?php
                        $oldArsipIds = (array) (old('arsip_ids') ?? []);
                        $no = 1;
                        foreach ($arsip_list as $arsip) :
                            $isChecked = in_array($arsip['id_arsip'], $oldArsipIds, true);
                        ?>
                            <tr class="arsip-row <?= $isChecked ? 'row-selected' : '' ?>"
                                data-id="<?= esc($arsip['id_arsip']) ?>"
                                data-id-spt="<?= esc($arsip['id_spt'] ?? '') ?>">
                                <td style="text-align: center; padding-left: 20px;" onclick="event.stopPropagation();">
                                    <input 
                                        class="form-check-input arsip-checkbox" 
                                        type="checkbox" 
                                        name="arsip_ids[]" 
                                        value="<?= esc($arsip['id_arsip']) ?>" 
                                        id="arsip_<?= esc($arsip['id_arsip']) ?>"
                                        <?= $isChecked ? 'checked' : '' ?>
                                    >
                                </td>
                                <td class="arsip-row-no" style="color: #64748B; font-weight: 500;"><?= $no++ ?></td>
                                <td>
                                    <span class="fw-semibold text-primary"><?= esc($arsip['nomor_arsip']) ?></span>
                                </td>
                                <td class="text-dark fw-medium"><?= esc($arsip['nama_arsip']) ?></td>
                                <td>
                                    <?php if (!empty($arsip['nomor_spt'])) : ?>
                                        <span class="badge bg-info-subtle text-info-emphasis border border-info-subtle fw-medium px-2 py-1" style="font-size: 11.5px;">
                                            <?= esc($arsip['nomor_spt']) ?>
                                        </span>
                                    <?php else : ?>
                                        <span class="text-muted small">Tanpa SPT</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-secondary"><?= esc($arsip['kurun_waktu'] ?? '-') ?></td>
                                <td style="padding-right: 20px;">
                                    <span class="badge text-bg-light border text-dark fw-medium px-2 py-1" style="font-size: 12px;">
                                        <?= esc($arsip['kondisi'] ?? 'Baik') ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <tr id="emptyFilterArsipRow" class="d-none">
                            <td colspan="7" class="text-center py-5 text-secondary">
                                <i class="bi bi-folder-x fs-1 text-muted d-block mb-2"></i>
                                <div class="fw-semibold text-dark">Tidak ada arsip yang terhubung dengan SPT terpilih</div>
                                <div class="small text-muted mt-1">Arsip untuk SPT ini belum diunggah atau sudah terlampir di Berita Acara lain.</div>
                            </td>
                        </tr>
                    <?php else : ?>
                        <tr>
                            <td colspan="7" class="text-center py-5 text-secondary">
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
    <div class="d-flex align-items-center justify-content-end gap-2 mt-4 mb-5">
        <a href="<?= base_url('/berita-acara') ?>" class="btn ba-btn-cancel" id="btnBatalBA">
            Batal
        </a>
        <button type="submit" class="btn ba-btn-save" id="btnSimpanDrafBA">
            <i class="bi bi-floppy"></i>
            <span>Simpan Draf Berita Acara</span>
        </button>
    </div>
</form>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sptSelect = document.getElementById('id_spt');
    const checkAll = document.getElementById('checkAllArsip');
    const checkboxes = document.querySelectorAll('.arsip-checkbox');
    const badgeCount = document.getElementById('selectedCountBadge');
    const form = document.getElementById('formBuatBA');
    const alertNoArsip = document.getElementById('alertNoArsipSelected');
    const filterInfo = document.getElementById('arsipFilterInfo');
    const emptyFilterRow = document.getElementById('emptyFilterArsipRow');
    const rows = document.querySelectorAll('.arsip-row');
    const hasOldArsipSelection = <?= !empty(old('arsip_ids')) ? 'true' : 'false' ?>;

    function getVisibleCheckboxes() {
        return Array.from(checkboxes).filter(cb => {
            const row = cb.closest('tr');
            return row && !row.classList.contains('d-none');
        });
    }

    function updateCount() {
        const visibleCheckboxes = getVisibleCheckboxes();
        const checkedCount = visibleCheckboxes.filter(cb => cb.checked).length;
        if (badgeCount) {
            badgeCount.textContent = checkedCount + ' arsip terpilih';
            if (checkedCount > 0) {
                badgeCount.className = 'badge bg-primary text-white fw-semibold px-2 py-1';
                if (alertNoArsip) alertNoArsip.classList.add('d-none');
            } else {
                badgeCount.className = 'badge bg-primary-subtle text-primary border border-primary-subtle fw-semibold px-2 py-1';
            }
        }
        if (checkAll) {
            checkAll.disabled = visibleCheckboxes.length === 0;
            checkAll.checked = (visibleCheckboxes.length > 0 && checkedCount === visibleCheckboxes.length);
            checkAll.indeterminate = (checkedCount > 0 && checkedCount < visibleCheckboxes.length);
        }
    }

    function applySptFilter(autoCheckMatches) {
        const selectedSptId = sptSelect ? sptSelect.value.trim() : '';
        let visibleNo = 1;
        let matchCount = 0;

        rows.forEach(row => {
            const rowSptId = (row.getAttribute('data-id-spt') || '').trim();
            const cb = row.querySelector('.arsip-checkbox');
            const noCell = row.querySelector('.arsip-row-no');
            const isMatch = !selectedSptId || rowSptId === selectedSptId;

            if (isMatch) {
                row.classList.remove('d-none');
                if (noCell) noCell.textContent = visibleNo++;
                matchCount++;

                if (cb && autoCheckMatches) {
                    cb.checked = selectedSptId !== '';
                }
            } else {
                row.classList.add('d-none');
                if (cb) {
                    cb.checked = false;
                }
            }

            if (cb) {
                if (cb.checked) row.classList.add('row-selected');
                else row.classList.remove('row-selected');
            }
        });

        if (emptyFilterRow) {
            if (selectedSptId && matchCount === 0) {
                emptyFilterRow.classList.remove('d-none');
            } else {
                emptyFilterRow.classList.add('d-none');
            }
        }

        if (filterInfo) {
            if (selectedSptId && matchCount > 0) {
                filterInfo.innerHTML = '<i class="bi bi-magic text-primary me-1"></i> Sistem menampilkan dan memilih otomatis <strong>' + matchCount + ' arsip</strong> yang terhubung dengan SPT terpilih. Anda tetap dapat menyesuaikan centang jika diperlukan.';
            } else if (selectedSptId && matchCount === 0) {
                filterInfo.innerHTML = '<i class="bi bi-info-circle text-warning me-1"></i> Belum ada arsip bebas yang terhubung dengan SPT terpilih.';
            } else {
                filterInfo.textContent = 'Centang satu atau beberapa arsip di bawah yang telah selesai dialihmediakan untuk dimasukkan ke dalam Berita Acara ini.';
            }
        }

        updateCount();
    }

    if (sptSelect) {
        sptSelect.addEventListener('change', function() {
            applySptFilter(true);
        });
    }

    // Toggle Check All (khusus baris yang sedang tampil)
    if (checkAll) {
        checkAll.addEventListener('change', function() {
            const visibleCheckboxes = getVisibleCheckboxes();
            visibleCheckboxes.forEach(cb => {
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
            const checkedCount = getVisibleCheckboxes().filter(cb => cb.checked).length;
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

    // Terapkan filter & auto-centang jika SPT sudah terpilih saat halaman dibuka
    if (sptSelect && sptSelect.value.trim() !== '') {
        applySptFilter(!hasOldArsipSelection);
    } else {
        updateCount();
    }
});
</script>
<?= $this->endSection() ?>
