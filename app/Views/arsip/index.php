<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Daftar Arsip Digital - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Daftar Arsip Digital</h2>

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

<?php $id_role = session()->get('id_role'); ?>

<?php if ($id_role == 5) : // Tombol Tambah Arsip hanya untuk Arsiparis ?>
    <div style="margin-bottom: 15px;">
        <a href="<?= base_url('/arsip/create') ?>">
            <button type="button">+ Tambah Arsip Baru</button>
        </a>
    </div>
<?php endif; ?>

<table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Arsip</th>
            <th>Nama Arsip</th>
            <th>Kode Klasifikasi</th>
            <th>Bidang</th>
            <th>Status Alih Media</th>
            <th>Berkas File</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (! empty($arsip_list)) : ?>
            <?php $no = 1; foreach ($arsip_list as $row) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($row['nomor_arsip']) ?></td>
                    <td><?= esc($row['nama_arsip']) ?></td>
                    <td>
                        <?= esc($row['kode_klasifikasi'] ?? '-') ?>
                        <?php if (! empty($row['nama_klasifikasi'])) : ?>
                            <br><small><?= esc($row['nama_klasifikasi']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($row['nama_bidang'] ?? '-') ?></td>
                    <td><?= esc($row['status_alih_media']) ?></td>
                    <td>
                        <?php if (! empty($row['file_digital'])) : ?>
                            <a href="<?= base_url('uploads/arsip/' . $row['file_digital']) ?>" target="_blank">Lihat Berkas</a>
                        <?php else : ?>
                            <em>Belum ada</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('/arsip/detail/' . $row['id_arsip']) ?>">
                            <button type="button">Detail</button>
                        </a>
                        <?php if ($id_role == 5) : ?>
                            <a href="<?= base_url('/arsip/edit/' . $row['id_arsip']) ?>">
                                <button type="button">Edit</button>
                            </a>
                            <a href="<?= base_url('/arsip/delete/' . $row['id_arsip']) ?>"
                               onclick="return confirm('Yakin ingin menghapus arsip ini beserta file digitalnya?')">
                                <button type="button">Hapus</button>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="8" style="text-align: center;">
                    Belum ada data arsip.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
