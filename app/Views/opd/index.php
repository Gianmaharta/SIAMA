<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Master OPD - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Master Data OPD</h2>

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
    <a href="<?= base_url('/opd/create') ?>"><button type="button">Tambah OPD</button></a>
</div>

<table border="1" cellpadding="10" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>ID</th>
            <th>Kode OPD</th>
            <th>Nama OPD</th>
            <th>Kuota Storage (MB)</th>
            <th>Status Aktif</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($opds)) : ?>
            <?php foreach ($opds as $row) : ?>
                <tr>
                    <td><?= esc($row['id_opd']) ?></td>
                    <td><?= esc($row['kode_opd']) ?></td>
                    <td><?= esc($row['nama_opd']) ?></td>
                    <td><?= esc($row['kuota_storage_mb']) ?></td>
                    <td><?= $row['is_active'] ? 'Aktif' : 'Non-Aktif' ?></td>
                    <td>
                        <a href="<?= base_url('/opd/edit/' . $row['id_opd']) ?>"><button type="button">Edit</button></a>
                        <a href="<?= base_url('/opd/delete/' . $row['id_opd']) ?>" onclick="return confirm('Yakin ingin menghapus OPD ini?');"><button type="button">Hapus</button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="6" style="text-align: center;">Belum ada data OPD.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
