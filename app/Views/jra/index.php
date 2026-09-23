<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Master JRA - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Master Jadwal Retensi Arsip (JRA)</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('success')) : ?>
    <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div style="margin-bottom: 15px;">
    <a href="<?= base_url('/jra/create') ?>">
        <button type="button">+ Tambah JRA Baru</button>
    </a>
</div>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Kode Klasifikasi</th>
            <th>Retensi Aktif</th>
            <th>Retensi Inaktif</th>
            <th>Keterangan</th>
            <th>Keamanan</th>
            <th>Dasar Pertimbangan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($jra_list)) : ?>
            <?php $no = 1; foreach ($jra_list as $row) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($row['kode']) ?> - <?= esc($row['nama_klasifikasi']) ?></td>
                    <td><?= esc($row['retensi_aktif']) ?> Tahun</td>
                    <td><?= esc($row['retensi_inaktif']) ?> Tahun</td>
                    <td><?= esc($row['keterangan_retensi']) ?></td>
                    <td><?= esc($row['klasifikasi_keamanan']) ?></td>
                    <td><?= esc($row['dasar_pertimbangan']) ?></td>
                    <td>
                        <a href="<?= base_url('/jra/edit/' . $row['id_jra']) ?>">
                            <button type="button">Edit</button>
                        </a>
                        <a href="<?= base_url('/jra/delete/' . $row['id_jra']) ?>" onclick="return confirm('Yakin hapus?')">
                            <button type="button">Hapus</button>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="7" style="text-align: center;">Belum ada data JRA.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
