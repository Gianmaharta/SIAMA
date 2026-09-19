<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Berita Acara - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Daftar Berita Acara</h2>

<?php if (session()->getFlashdata('success')) : ?>
    <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<?php if (session()->get('nama_role') === 'Arsiparis') : ?>
    <div style="margin-bottom: 15px;">
        <a href="<?= base_url('/berita-acara/create') ?>"><button style="padding: 10px 15px;">Buat Draf Berita Acara</button></a>
    </div>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor BA</th>
            <th>Tanggal</th>
            <th>Nomor SPT</th>
            <th>Pelaksana</th>
            <th>Status Persetujuan</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($berita_acara)) : ?>
            <?php $i = 1; foreach ($berita_acara as $row) : ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= esc($row['nomor_ba']) ?></td>
                    <td><?= esc($row['tanggal_ba']) ?></td>
                    <td><?= esc($row['nomor_spt'] ?: '-') ?></td>
                    <td><?= esc($row['nama_pelaksana']) ?></td>
                    <td style="text-align: center;">
                        <?php
                        $status = $row['status_persetujuan'];
                        $color = 'black';
                        if ($status === 'Draf_Kabid') $color = 'orange';
                        elseif ($status === 'Draf_Pimpinan') $color = 'blue';
                        elseif ($status === 'Revisi') $color = 'red';
                        elseif ($status === 'Selesai') $color = 'green';
                        ?>
                        <span style="color: <?= $color ?>; font-weight: bold;"><?= esc($status) ?></span>
                    </td>
                    <td style="text-align: center;">
                        <a href="<?= base_url('/berita-acara/detail/' . $row['id_berita_acara']) ?>">Detail</a>
                        <?php if ($row['status_persetujuan'] === 'Selesai' || $row['status_persetujuan'] === 'Draf_Pimpinan' || $row['status_persetujuan'] === 'Draf_Kabid') : ?>
                            | <a href="<?= base_url('/berita-acara/cetak/' . $row['id_berita_acara']) ?>" target="_blank">Cetak PDF</a>
                        <?php endif; ?>
                        
                        <?php if (session()->get('nama_role') === 'Arsiparis' && ($row['status_persetujuan'] === 'Draf_Kabid' || $row['status_persetujuan'] === 'Revisi')) : ?>
                            | <a href="<?= base_url('/berita-acara/edit/' . $row['id_berita_acara']) ?>">Edit</a>
                            | <a href="<?= base_url('/berita-acara/delete/' . $row['id_berita_acara']) ?>" onclick="return confirm('Yakin ingin menghapus Berita Acara ini? Arsip yang terkait akan dikembalikan statusnya.')">Hapus</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="7" style="text-align: center;">Belum ada Berita Acara.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
