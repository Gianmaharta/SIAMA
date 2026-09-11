<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Master Bidang - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Master Data Bidang</h2>

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
    <a href="<?= base_url('/bidang/create') ?>"><button type="button">Tambah Bidang</button></a>
</div>

<table border="1" cellpadding="10" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nama OPD</th>
            <th>Nama Bidang</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($bidangs)) : ?>
            <?php foreach ($bidangs as $row) : ?>
                <tr>
                    <td><?= esc($row['id_bidang']) ?></td>
                    <td><?= esc($row['nama_opd']) ?></td>
                    <td><?= esc($row['nama_bidang']) ?></td>
                    <td>
                        <a href="<?= base_url('/bidang/edit/' . $row['id_bidang']) ?>"><button type="button">Edit</button></a>
                        <a href="<?= base_url('/bidang/delete/' . $row['id_bidang']) ?>" onclick="return confirm('Yakin ingin menghapus Bidang ini?');"><button type="button">Hapus</button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="4" style="text-align: center;">Belum ada data Bidang.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
