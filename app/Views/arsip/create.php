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
    <div style="margin-bottom: 12px;">
        <label for="id_klasifikasi"><strong>Kode Klasifikasi <span style="color:red;">*</span></strong></label><br>
        <select id="id_klasifikasi" name="id_klasifikasi" required style="width: 100%; max-width: 400px;">
            <option value="">-- Pilih Kode Klasifikasi --</option>
            <?php foreach ($klasifikasi_list as $klas) : ?>
                <option value="<?= $klas['id_klasifikasi'] ?>"
                    <?= old('id_klasifikasi') == $klas['id_klasifikasi'] ? 'selected' : '' ?>>
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
        <label for="tahun_penciptaan"><strong>Tahun Penciptaan</strong></label><br>
        <input type="number"
               id="tahun_penciptaan"
               name="tahun_penciptaan"
               value="<?= old('tahun_penciptaan') ?>"
               min="1900"
               max="<?= date('Y') ?>"
               placeholder="Contoh: 2023"
               style="width: 150px;">
    </div>

    <!-- Kategori JRA -->
    <div style="margin-bottom: 12px;">
        <label for="kategori_jra"><strong>Kategori JRA</strong></label><br>
        <select id="kategori_jra" name="kategori_jra" style="width: 100%; max-width: 300px;">
            <option value="">-- Pilih Kategori --</option>
            <option value="Permanen"    <?= old('kategori_jra') == 'Permanen'    ? 'selected' : '' ?>>Permanen</option>
            <option value="Musnah"      <?= old('kategori_jra') == 'Musnah'      ? 'selected' : '' ?>>Musnah</option>
            <option value="Diserahkan"  <?= old('kategori_jra') == 'Diserahkan'  ? 'selected' : '' ?>>Diserahkan</option>
        </select>
    </div>

    <!-- Kondisi Fisik -->
    <div style="margin-bottom: 12px;">
        <label for="kondisi_fisik"><strong>Kondisi Fisik</strong></label><br>
        <select id="kondisi_fisik" name="kondisi_fisik" style="width: 100%; max-width: 300px;">
            <option value="Baik"         <?= old('kondisi_fisik') == 'Baik'         ? 'selected' : '' ?>>Baik</option>
            <option value="Rusak Ringan" <?= old('kondisi_fisik') == 'Rusak Ringan' ? 'selected' : '' ?>>Rusak Ringan</option>
            <option value="Rusak Berat"  <?= old('kondisi_fisik') == 'Rusak Berat'  ? 'selected' : '' ?>>Rusak Berat</option>
        </select>
    </div>

    <!-- Metode Alih Media -->
    <div style="margin-bottom: 12px;">
        <label for="metode_alih_media"><strong>Metode Alih Media</strong></label><br>
        <select id="metode_alih_media" name="metode_alih_media" style="width: 100%; max-width: 300px;">
            <option value="Scan"         <?= old('metode_alih_media') == 'Scan'         ? 'selected' : '' ?>>Scan</option>
            <option value="Fotografi"    <?= old('metode_alih_media') == 'Fotografi'    ? 'selected' : '' ?>>Fotografi</option>
            <option value="Digitalisasi" <?= old('metode_alih_media') == 'Digitalisasi' ? 'selected' : '' ?>>Digitalisasi</option>
        </select>
    </div>

    <!-- Skor Prioritas -->
    <div style="margin-bottom: 12px;">
        <label for="skor_prioritas"><strong>Skor Prioritas</strong></label><br>
        <input type="number"
               id="skor_prioritas"
               name="skor_prioritas"
               value="<?= old('skor_prioritas', 0) ?>"
               min="0"
               max="100"
               style="width: 100px;">
        <small>(0 = terendah)</small>
    </div>

    <!-- File Digital Arsip -->
    <div style="margin-bottom: 12px;">
        <label for="file_arsip"><strong>Upload Berkas Digital</strong></label>
        <em>(PDF, JPG, JPEG, PNG, TIFF — maks. 10 MB)</em><br>
        <input type="file"
               id="file_arsip"
               name="file_arsip"
               accept=".pdf,.jpg,.jpeg,.png,.tiff,.tif">
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
<?= $this->endSection() ?>
