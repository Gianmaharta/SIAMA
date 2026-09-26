<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Data Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Edit Data Arsip</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/arsip/update/' . $arsip['id_arsip']) ?>" method="post" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- Nomor Arsip -->
    <div style="margin-bottom: 12px;">
        <label for="nomor_arsip"><strong>Nomor Arsip <span style="color:red;">*</span></strong></label><br>
        <input type="text"
               id="nomor_arsip"
               name="nomor_arsip"
               value="<?= old('nomor_arsip', esc($arsip['nomor_arsip'])) ?>"
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
               value="<?= old('nama_arsip', esc($arsip['nama_arsip'])) ?>"
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
                    <?= (old('id_kode_klasifikasi', $arsip['id_kode_klasifikasi']) == $klas['id_kode_klasifikasi']) ? 'selected' : '' ?>>
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
                    <?= (old('id_bidang', $arsip['id_bidang']) == $bid['id_bidang']) ? 'selected' : '' ?>>
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
                    <?= (old('id_spt', $arsip['id_spt']) == $spt['id_spt']) ? 'selected' : '' ?>>
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
               value="<?= old('kurun_waktu', esc($arsip['kurun_waktu'])) ?>"
               min="1900"
               max="<?= date('Y') ?>"
               style="width: 150px;">
    </div>

    <!-- Kondisi Fisik -->
    <div style="margin-bottom: 12px;">
        <label for="kondisi"><strong>Kondisi Fisik</strong></label><br>
        <select id="kondisi" name="kondisi" style="width: 100%; max-width: 300px;">
            <?php $currentKondisi = old('kondisi', $arsip['kondisi']); ?>
            <option value="Baik"         <?= $currentKondisi == 'Baik'         ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak Ringan" <?= $currentKondisi == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak Berat"  <?= $currentKondisi == 'Rusak Berat'  ? 'selected' : '' ?>>Rusak Berat</option>
        </select>
    </div>







    <!-- Status Verifikasi -->
    <div style="margin-bottom: 12px;">
        <label for="status_verifikasi"><strong>Status Verifikasi</strong></label><br>
        <select id="status_verifikasi" name="status_verifikasi" style="width: 100%; max-width: 300px;">
            <?php $currentAlih = old('status_verifikasi', $arsip['status_verifikasi']); ?>
            <option value="Belum Diajukan" <?= $currentAlih == 'Belum Diajukan' ? 'selected' : '' ?>>Belum Diajukan</option>
            <option value="Diajukan"       <?= $currentAlih == 'Diajukan'       ? 'selected' : '' ?>>Diajukan</option>
            <option value="Disetujui"      <?= $currentAlih == 'Disetujui'      ? 'selected' : '' ?>>Disetujui</option>
            <option value="Ditolak"        <?= $currentAlih == 'Ditolak'        ? 'selected' : '' ?>>Ditolak</option>
            <option value="Selesai"        <?= $currentAlih == 'Selesai'        ? 'selected' : '' ?>>Selesai</option>
        </select>
    </div>

    <!-- File Digital Arsip -->
    <div style="margin-bottom: 12px;">
        <label for="file_arsip"><strong>Ganti Berkas Digital</strong></label>
        <em>(PDF, JPG, JPEG, PNG, TIFF — maks. 10 MB. Kosongkan jika tidak ingin mengganti.)</em><br>
        <input type="file"
               id="file_arsip"
               name="file_arsip"
               accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif">
        
        <!-- Opsi Watermark -->
        <div style="margin-top: 15px; padding: 10px; border: 1px solid #ccc; background-color: #f0f8ff; max-width: 500px;">
            <label><strong>Opsi Keamanan PDF (Watermark)</strong></label><br>
            <?php $currentWm = old('watermark_source', $arsip['watermark_source'] ?? 'none'); ?>
            <div style="margin-top: 8px;">
                <label style="display: block; margin-bottom: 6px; font-weight: normal; font-size: 14px;">
                    <input type="radio" name="watermark_source" value="system" <?= $currentWm == 'system' ? 'checked' : '' ?>>
                    🔒 Tambahkan Watermark Otomatis Sistem (Nama OPD)
                </label>
                <label style="display: block; margin-bottom: 6px; font-weight: normal; font-size: 14px;">
                    <input type="radio" name="watermark_source" value="offline" <?= $currentWm == 'offline' ? 'checked' : '' ?>>
                    📄 Berkas sudah memiliki Watermark Fisik/Offline
                </label>
                <label style="display: block; margin-bottom: 6px; font-weight: normal; font-size: 14px;">
                    <input type="radio" name="watermark_source" value="none" <?= $currentWm == 'none' ? 'checked' : '' ?>>
                    ❌ Tanpa Watermark
                </label>
            </div>
            <?php if ($arsip['is_watermarked']) : ?>
                <div style="font-size: 12px; color: #155724; margin-top: 5px; padding: 5px; background: #d4edda; border-radius: 3px;">
                    ✔ Berkas saat ini sudah memiliki Watermark Sistem.
                </div>
            <?php endif; ?>
        </div>
        <?php if (! empty($arsip['file_arsip'])) : ?>
            <br>
            <small>
                File saat ini:
                <a href="<?= base_url('uploads/arsip/' . $arsip['file_arsip']) ?>" target="_blank">
                    <?= esc($arsip['file_arsip']) ?>
                </a>
            </small>
        <?php endif; ?>
    </div>

    <!-- Tombol -->
    <div style="margin-top: 20px;">
        <button type="submit">Simpan Perubahan</button>
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

    if (klasifikasiSelect.value) {
        fetchJra(klasifikasiSelect.value);
    }
});
</script>
<?= $this->endSection() ?>
