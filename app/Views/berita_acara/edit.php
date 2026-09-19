<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Draf Berita Acara - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Edit Draf Berita Acara</h2>

<a href="<?= base_url('/berita-acara') ?>">&larr; Kembali</a>
<br><br>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/berita-acara/update/' . $ba['id_berita_acara']) ?>" method="POST">
    <!-- Nomor Berita Acara -->
    <div style="margin-bottom: 12px;">
        <label for="nomor_ba"><strong>Nomor Berita Acara</strong></label><br>
        <input type="text" id="nomor_ba" name="nomor_ba" style="width: 100%; max-width: 300px;" value="<?= old('nomor_ba', esc($ba['nomor_ba'])) ?>" required>
    </div>

    <!-- Tanggal Berita Acara -->
    <div style="margin-bottom: 12px;">
        <label for="tanggal_ba"><strong>Tanggal Berita Acara</strong></label><br>
        <input type="date" id="tanggal_ba" name="tanggal_ba" value="<?= old('tanggal_ba', esc($ba['tanggal_ba'])) ?>" required>
    </div>

    <!-- Referensi SPT -->
    <div style="margin-bottom: 12px;">
        <label for="id_spt"><strong>Referensi SPT (Pilihan)</strong></label><br>
        <select id="id_spt" name="id_spt" style="width: 100%; max-width: 400px;">
            <option value="">-- Tidak Terkait SPT --</option>
            <?php if (!empty($spt_list)) : ?>
                <?php foreach ($spt_list as $spt) : ?>
                    <option value="<?= $spt['id_spt'] ?>" <?= old('id_spt', $ba['id_spt']) == $spt['id_spt'] ? 'selected' : '' ?>>
                        <?= esc($spt['nomor_spt']) ?> - <?= esc($spt['nama_kegiatan']) ?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
    </div>

    <p><em>*Catatan: Anda hanya dapat mengubah informasi kerangka surat. Jika Anda ingin mengubah daftar arsip yang terkait, silakan hapus Berita Acara ini dan buat yang baru.</em></p>

    <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 15px; border: none; cursor: pointer;">
        Perbarui Draf
    </button>
</form>

<?= $this->endSection() ?>
