<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Arsip Baru - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Tambah Arsip Baru</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/arsip/store') ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <?php if (!empty($spt_terpilih)) : ?>
        <div style="background-color: #d1ecf1; border-left: 4px solid #0c5460; padding: 10px; margin-bottom: 15px;">
            <strong>ℹ️ Arsip ini terhubung dengan SPT:</strong><br>
            Nomor SPT: <?= esc($spt_terpilih['nomor_spt']) ?><br>
            Perihal: <?= esc($spt_terpilih['perihal']) ?>
        </div>
        <input type="hidden" name="id_spt" value="<?= esc($spt_terpilih['id_spt']) ?>">
    <?php endif; ?>

    <!-- Nomor Arsip -->
    <div style="margin-bottom: 12px;">
        <label for="nomor_arsip"><strong>Nomor Arsip <span style="color:red;">*</span></strong></label><br>
        <input type="text"
               id="nomor_arsip"
               name="nomor_arsip"
               value="<?= old('nomor_arsip') ?>"
               maxlength="100"
               required
               style="width: 100%; max-width: 400px;">
    </div>

    <!-- Nama Arsip -->
    <div style="margin-bottom: 12px;">
        <label for="nama_arsip"><strong>Nama / Judul Arsip <span style="color:red;">*</span></strong></label><br>
        <input type="text"
               id="nama_arsip"
               name="nama_arsip"
               value="<?= old('nama_arsip') ?>"
               maxlength="255"
               required
               style="width: 100%; max-width: 500px;">
    </div>

    <!-- Kode Klasifikasi -->
    <div style="margin-bottom: 15px;">
        <label for="id_kode_klasifikasi"><strong>Kode Klasifikasi <span style="color:red;">*</span></strong></label><br>
        <select id="id_kode_klasifikasi" name="id_kode_klasifikasi" required style="width: 100%; max-width: 400px;">
            <option value="">-- Pilih Kode Klasifikasi --</option>
            <?php foreach ($klasifikasi_list as $klas) : ?>
                <option value="<?= $klas['id_kode_klasifikasi'] ?>"
                    <?= old('id_kode_klasifikasi') == $klas['id_kode_klasifikasi'] ? 'selected' : '' ?>>
                    <?= esc($klas['kode']) ?> - <?= esc($klas['nama_klasifikasi']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Info JRA (Auto-fill) -->
    <div id="jra_info_box" style="margin-bottom: 15px; padding: 10px; border: 1px solid #ccc; background-color: #f9f9f9; display: none; max-width: 400px;">
        <strong>Informasi Jadwal Retensi Arsip (JRA)</strong><br>
        <span style="font-size: 12px; color: #555;">(Terisi otomatis berdasarkan Kode Klasifikasi)</span>
        <div style="margin-top: 10px;">
            <label>Retensi Aktif (Tahun):</label>
            <input type="text" id="jra_retensi_aktif" name="retensi_aktif" readonly style="width: 100%; background: #eee; border: 1px solid #ddd;">
        </div>
        <div style="margin-top: 10px;">
            <label>Retensi Inaktif (Tahun):</label>
            <input type="text" id="jra_retensi_inaktif" name="retensi_inaktif" readonly style="width: 100%; background: #eee; border: 1px solid #ddd;">
        </div>
        <div style="margin-top: 10px;">
            <label>Keterangan Retensi:</label>
            <input type="text" id="jra_keterangan" name="keterangan_retensi" readonly style="width: 100%; background: #eee; border: 1px solid #ddd;">
        </div>
        <div style="margin-top: 10px;">
            <label>Tingkat Keamanan:</label>
            <input type="text" id="jra_keamanan" name="klasifikasi_keamanan" readonly style="width: 100%; background: #eee; border: 1px solid #ddd;">
        </div>
        <div style="margin-top: 10px;">
            <label>Dasar Pertimbangan:</label>
            <input type="text" id="jra_dasar" name="dasar_pertimbangan" readonly style="width: 100%; background: #eee; border: 1px solid #ddd;">
        </div>
    </div>

    <!-- Bidang -->
    <div style="margin-bottom: 12px;">
        <label for="id_bidang"><strong>Bidang <span style="color:red;">*</span></strong></label><br>
        <select id="id_bidang" name="id_bidang" required style="width: 100%; max-width: 400px;">
            <option value="">-- Pilih Bidang --</option>
            <?php foreach ($bidang_list as $bid) : ?>
                <option value="<?= $bid['id_bidang'] ?>"
                    <?= old('id_bidang') == $bid['id_bidang'] ? 'selected' : '' ?>>
                    <?= esc($bid['nama_bidang']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- SPT Terkait (Opsional) -->
    <div style="margin-bottom: 12px;">
        <label for="id_spt"><strong>SPT Terkait</strong> <em>(opsional)</em></label><br>
        <select id="id_spt" name="id_spt" style="width: 100%; max-width: 400px;">
            <option value="">-- Tidak ada SPT terkait --</option>
            <?php foreach ($spt_list as $spt) : ?>
                <option value="<?= $spt['id_spt'] ?>"
                    <?= old('id_spt') == $spt['id_spt'] ? 'selected' : '' ?>>
                    <?= esc($spt['nomor_spt']) ?> (<?= esc($spt['tanggal_spt']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- Tahun Penciptaan -->
    <div style="margin-bottom: 12px;">
        <label for="kurun_waktu"><strong>Tahun Penciptaan</strong></label><br>
        <input type="number"
               id="kurun_waktu"
               name="kurun_waktu"
               value="<?= old('kurun_waktu') ?>"
               min="1900"
               max="<?= date('Y') ?>"
               placeholder="Contoh: 2023"
               style="width: 150px;">
    </div>

    <!-- Kondisi Fisik -->
    <div style="margin-bottom: 12px;">
        <label for="kondisi"><strong>Kondisi Fisik</strong></label><br>
        <select id="kondisi" name="kondisi" style="width: 100%; max-width: 300px;">
            <option value="Baik"         <?= old('kondisi') == 'Baik'         ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak Ringan" <?= old('kondisi') == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak Berat"  <?= old('kondisi') == 'Rusak Berat'  ? 'selected' : '' ?>>Rusak Berat</option>
        </select>
    </div>



    <!-- File Arsip -->
    <div style="margin-bottom: 12px;">
        <label for="file_arsip"><strong>Upload Berkas Digital</strong></label>
        <em>(PDF, JPG, JPEG, PNG, TIFF — maks. 10 MB)</em><br>
        <input type="file"
               id="file_arsip"
               name="file_arsip"
               accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif">
    </div>

    <!-- Opsi Watermark -->
    <div style="margin-bottom: 20px; padding: 10px; border: 1px solid #ccc; background-color: #f0f8ff; max-width: 400px;">
        <label><strong>Opsi Keamanan PDF (Watermark)</strong></label><br>
        <label style="font-weight: normal; font-size: 14px;">
            <input type="checkbox" name="generate_watermark" value="1">
            Generate Watermark Sistem (Khusus PDF)
        </label>
        <div style="font-size: 12px; color: #666; margin-top: 5px;">
            Sistem akan secara otomatis menambahkan watermark miring bertuliskan nama instansi/OPD Anda pada berkas PDF yang diunggah.
        </div>
    </div>

    <!-- Tombol -->
    <div style="margin-top: 20px;">
        <button type="submit">Simpan Arsip</button>
        &nbsp;
        <a href="<?= base_url('/arsip') ?>">
            <button type="button">Batal</button>
        </a>
    </div>
</form>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const klasifikasiSelect = document.getElementById('id_kode_klasifikasi');
    const jraBox = document.getElementById('jra_info_box');
    
    function fetchJra(id) {
        if (!id) {
            jraBox.style.display = 'none';
            return;
        }
        
        fetch('<?= base_url('jra/get_by_klasifikasi/') ?>' + id)
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    jraBox.style.display = 'block';
                    document.getElementById('jra_retensi_aktif').value = data.data.retensi_aktif;
                    document.getElementById('jra_retensi_inaktif').value = data.data.retensi_inaktif;
                    document.getElementById('jra_keterangan').value = data.data.keterangan_retensi;
                    document.getElementById('jra_keamanan').value = data.data.klasifikasi_keamanan;
                    document.getElementById('jra_dasar').value = data.data.dasar_pertimbangan || '-';
                } else {
                    jraBox.style.display = 'block';
                    document.getElementById('jra_retensi_aktif').value = 'Tidak ada JRA';
                    document.getElementById('jra_retensi_inaktif').value = 'Tidak ada JRA';
                    document.getElementById('jra_keterangan').value = 'Tidak ada JRA';
                    document.getElementById('jra_keamanan').value = 'Tidak ada JRA';
                    document.getElementById('jra_dasar').value = 'Tidak ada JRA';
                }
            })
            .catch(error => {
                console.error('Error fetching JRA:', error);
            });
    }

    klasifikasiSelect.addEventListener('change', function() {
        fetchJra(this.value);
    });

    // Trigger on load if already selected (e.g. returning from validation error)
    if (klasifikasiSelect.value) {
        fetchJra(klasifikasiSelect.value);
    }
});
</script>
<?= $this->endSection() ?>
