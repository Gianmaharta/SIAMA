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
    <div style="margin-bottom: 12px;">
        <label for="id_klasifikasi"><strong>Kode Klasifikasi <span style="color:red;">*</span></strong></label><br>
        <select id="id_klasifikasi" name="id_klasifikasi" required style="width: 100%; max-width: 400px;">
            <option value="">-- Pilih Kode Klasifikasi --</option>
            <?php foreach ($klasifikasi_list as $klas) : ?>
                <option value="<?= $klas['id_klasifikasi'] ?>"
                    <?= (old('id_klasifikasi', $arsip['id_klasifikasi']) == $klas['id_klasifikasi']) ? 'selected' : '' ?>>
                    <?= esc($klas['kode']) ?> — <?= esc($klas['nama_klasifikasi']) ?>
                </option>
            <?php endforeach; ?>
        </select>
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
        <label for="tahun_penciptaan"><strong>Tahun Penciptaan</strong></label><br>
        <input type="number"
               id="tahun_penciptaan"
               name="tahun_penciptaan"
               value="<?= old('tahun_penciptaan', esc($arsip['tahun_penciptaan'])) ?>"
               min="1900"
               max="<?= date('Y') ?>"
               style="width: 150px;">
    </div>

    <!-- Kategori JRA -->
    <div style="margin-bottom: 12px;">
        <label for="kategori_jra"><strong>Kategori JRA</strong></label><br>
        <select id="kategori_jra" name="kategori_jra" style="width: 100%; max-width: 300px;">
            <option value="">-- Pilih Kategori --</option>
            <?php $currentJra = old('kategori_jra', $arsip['kategori_jra']); ?>
            <option value="Permanen"    <?= $currentJra == 'Permanen'    ? 'selected' : '' ?>>Permanen</option>
            <option value="Musnah"      <?= $currentJra == 'Musnah'      ? 'selected' : '' ?>>Musnah</option>
            <option value="Diserahkan"  <?= $currentJra == 'Diserahkan'  ? 'selected' : '' ?>>Diserahkan</option>
        </select>
    </div>

    <!-- Kondisi Fisik -->
    <div style="margin-bottom: 12px;">
        <label for="kondisi_fisik"><strong>Kondisi Fisik</strong></label><br>
        <select id="kondisi_fisik" name="kondisi_fisik" style="width: 100%; max-width: 300px;">
            <?php $currentKondisi = old('kondisi_fisik', $arsip['kondisi_fisik']); ?>
            <option value="Baik"         <?= $currentKondisi == 'Baik'         ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak Ringan" <?= $currentKondisi == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak Berat"  <?= $currentKondisi == 'Rusak Berat'  ? 'selected' : '' ?>>Rusak Berat</option>
        </select>
    </div>

    <!-- Metode Alih Media -->
    <div style="margin-bottom: 12px;">
        <label for="metode_alih_media"><strong>Metode Alih Media</strong></label><br>
        <select id="metode_alih_media" name="metode_alih_media" style="width: 100%; max-width: 300px;">
            <?php $currentMetode = old('metode_alih_media', $arsip['metode_alih_media']); ?>
            <option value="Scan"         <?= $currentMetode == 'Scan'         ? 'selected' : '' ?>>Scan</option>
            <option value="Fotografi"    <?= $currentMetode == 'Fotografi'    ? 'selected' : '' ?>>Fotografi</option>
            <option value="Digitalisasi" <?= $currentMetode == 'Digitalisasi' ? 'selected' : '' ?>>Digitalisasi</option>
        </select>
    </div>

    <!-- Skor Prioritas -->
    <div style="margin-bottom: 12px;">
        <label for="skor_prioritas"><strong>Skor Prioritas</strong></label><br>
        <input type="number"
               id="skor_prioritas"
               name="skor_prioritas"
               value="<?= old('skor_prioritas', esc($arsip['skor_prioritas'])) ?>"
               min="0"
               max="100"
               style="width: 100px;">
    </div>

    <!-- Status Autentikasi -->
    <div style="margin-bottom: 12px;">
        <label for="status_autentikasi"><strong>Status Autentikasi</strong></label><br>
        <select id="status_autentikasi" name="status_autentikasi" style="width: 100%; max-width: 300px;">
            <?php $currentAuth = old('status_autentikasi', $arsip['status_autentikasi']); ?>
            <option value="Belum Watermark" <?= $currentAuth == 'Belum Watermark' ? 'selected' : '' ?>>Belum Watermark</option>
            <option value="Sudah Watermark" <?= $currentAuth == 'Sudah Watermark' ? 'selected' : '' ?>>Sudah Watermark</option>
            <option value="Terautentikasi"  <?= $currentAuth == 'Terautentikasi'  ? 'selected' : '' ?>>Terautentikasi</option>
        </select>
    </div>

    <!-- Status Alih Media -->
    <div style="margin-bottom: 12px;">
        <label for="status_alih_media"><strong>Status Alih Media</strong></label><br>
        <select id="status_alih_media" name="status_alih_media" style="width: 100%; max-width: 300px;">
            <?php $currentAlih = old('status_alih_media', $arsip['status_alih_media']); ?>
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
        <?php if (! empty($arsip['file_digital'])) : ?>
            <br>
            <small>
                File saat ini:
                <a href="<?= base_url('uploads/arsip/' . $arsip['file_digital']) ?>" target="_blank">
                    <?= esc($arsip['file_digital']) ?>
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
<?= $this->endSection() ?>
