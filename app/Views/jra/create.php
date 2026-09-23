<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Master JRA - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Tambah Master JRA</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/jra/store') ?>" method="post">
    <?= csrf_field() ?>

    <div style="margin-bottom: 12px;">
        <label><strong>Kode Klasifikasi <span style="color:red;">*</span></strong></label><br>
        <input type="text" name="kode" value="<?= old('kode') ?>" required style="width: 100%; max-width: 200px;" placeholder="Contoh: 000.1.1">
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Uraian / Nama Klasifikasi <span style="color:red;">*</span></strong></label><br>
        <input type="text" name="nama_klasifikasi" value="<?= old('nama_klasifikasi') ?>" required style="width: 100%; max-width: 400px;" placeholder="Contoh: Telekomunikasi">
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Deskripsi (Opsional)</strong></label><br>
        <textarea name="deskripsi" style="width: 100%; max-width: 400px; height: 60px;"><?= old('deskripsi') ?></textarea>
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Retensi Aktif (Tahun) <span style="color:red;">*</span></strong></label><br>
        <input type="number" name="retensi_aktif" value="<?= old('retensi_aktif') ?>" required style="width: 100%; max-width: 100px;">
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Retensi Inaktif (Tahun) <span style="color:red;">*</span></strong></label><br>
        <input type="number" name="retensi_inaktif" value="<?= old('retensi_inaktif') ?>" required style="width: 100%; max-width: 100px;">
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Keterangan Retensi <span style="color:red;">*</span></strong></label><br>
        <select name="keterangan_retensi" required style="width: 100%; max-width: 200px;">
            <option value="Musnah" <?= old('keterangan_retensi') == 'Musnah' ? 'selected' : '' ?>>Musnah</option>
            <option value="Permanen" <?= old('keterangan_retensi') == 'Permanen' ? 'selected' : '' ?>>Permanen</option>
            <option value="Dinilai Kembali" <?= old('keterangan_retensi') == 'Dinilai Kembali' ? 'selected' : '' ?>>Dinilai Kembali</option>
        </select>
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Klasifikasi Keamanan <span style="color:red;">*</span></strong></label><br>
        <select name="klasifikasi_keamanan" required style="width: 100%; max-width: 200px;">
            <option value="Biasa" <?= old('klasifikasi_keamanan') == 'Biasa' ? 'selected' : '' ?>>Biasa</option>
            <option value="Rahasia" <?= old('klasifikasi_keamanan') == 'Rahasia' ? 'selected' : '' ?>>Rahasia</option>
            <option value="Sangat Rahasia" <?= old('klasifikasi_keamanan') == 'Sangat Rahasia' ? 'selected' : '' ?>>Sangat Rahasia</option>
        </select>
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Hak Akses</strong></label><br>
        <select name="hak_akses" style="width: 100%; max-width: 200px;">
            <option value="Terbuka" <?= old('hak_akses') == 'Terbuka' ? 'selected' : '' ?>>Terbuka</option>
            <option value="Tertutup" <?= old('hak_akses') == 'Tertutup' ? 'selected' : '' ?>>Tertutup</option>
        </select>
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Akses Publik</strong></label><br>
        <input type="text" name="akses_publik" value="<?= old('akses_publik') ?>" style="width: 100%; max-width: 400px;">
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Dasar Pertimbangan (Opsional)</strong></label><br>
        <textarea name="dasar_pertimbangan" style="width: 100%; max-width: 400px; height: 60px;"><?= old('dasar_pertimbangan') ?></textarea>
    </div>

    <div style="margin-bottom: 12px;">
        <label><strong>Unit Pengolah</strong></label><br>
        <input type="text" name="unit_pengolah" value="<?= old('unit_pengolah') ?>" style="width: 100%; max-width: 400px;">
    </div>

    <button type="submit">Simpan</button>
    <a href="<?= base_url('/jra') ?>"><button type="button">Batal</button></a>
</form>
<?= $this->endSection() ?>
