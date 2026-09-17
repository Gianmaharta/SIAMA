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

    <!-- Tingkat Perkembangan -->
    <div style="margin-bottom: 12px;">
        <label for="tingkat_perkembangan"><strong>Tingkat Perkembangan</strong></label><br>
        <select id="tingkat_perkembangan" name="tingkat_perkembangan" style="width: 100%; max-width: 300px;">
            <option value="">-- Pilih Kategori --</option>
            <option value="Permanen"    <?= old('tingkat_perkembangan') == 'Permanen'    ? 'selected' : '' ?>>Permanen</option>
            <option value="Musnah"      <?= old('tingkat_perkembangan') == 'Musnah'      ? 'selected' : '' ?>>Musnah</option>
            <option value="Diserahkan"  <?= old('tingkat_perkembangan') == 'Diserahkan'  ? 'selected' : '' ?>>Diserahkan</option>
        </select>
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
