<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Buat Draf Berita Acara - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Buat Draf Berita Acara</h2>

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('/berita-acara') ?>">&larr; Kembali</a>
</div>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/berita-acara/store') ?>" method="post">
    <div style="margin-bottom: 15px;">
        <label for="nomor_ba"><strong>Nomor Berita Acara</strong></label><br>
        <input type="text" name="nomor_ba" id="nomor_ba" required style="width: 300px; padding: 8px;" placeholder="Contoh: 001/BA/OPD/2026">
    </div>

    <div style="margin-bottom: 15px;">
        <label for="tanggal_ba"><strong>Tanggal Berita Acara</strong></label><br>
        <input type="date" name="tanggal_ba" id="tanggal_ba" required style="width: 200px; padding: 8px;">
    </div>

    <div style="margin-bottom: 15px;">
        <label for="id_spt"><strong>Referensi SPT (Pilihan)</strong></label><br>
        <select name="id_spt" id="id_spt" style="width: 300px; padding: 8px;">
            <option value="">-- Tidak Terkait SPT --</option>
            <?php foreach ($spt_list as $spt) : ?>
                <option value="<?= esc($spt['id_spt']) ?>"><?= esc($spt['nomor_spt']) ?> - <?= esc($spt['perihal']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div style="margin-bottom: 20px;">
        <label><strong>Pilih Arsip (Yang Belum Diberkaskan)</strong></label><br>
        <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; margin-top: 10px;">
            <thead>
                <tr>
                    <th width="50">Pilih</th>
                    <th>Nomor Arsip</th>
                    <th>Nama Arsip</th>
                    <th>Kurun Waktu</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($arsip_list)) : ?>
                    <?php foreach ($arsip_list as $arsip) : ?>
                        <tr>
                            <td style="text-align: center;">
                                <input type="checkbox" name="arsip_ids[]" value="<?= esc($arsip['id_arsip']) ?>">
                            </td>
                            <td><?= esc($arsip['nomor_arsip']) ?></td>
                            <td><?= esc($arsip['nama_arsip']) ?></td>
                            <td><?= esc($arsip['kurun_waktu']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else : ?>
                    <tr>
                        <td colspan="4" style="text-align: center;">Semua arsip sudah masuk Berita Acara.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <button type="submit" style="padding: 10px 20px; cursor: pointer; background-color: #4CAF50; color: white; border: none;">Simpan Draf</button>
</form>

<?= $this->endSection() ?>
