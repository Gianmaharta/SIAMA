<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Surat Perintah Tugas (SPT) - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Daftar Surat Perintah Tugas (SPT)</h2>

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
<?php if (in_array($id_role, [1, 2, 3])) : // Hanya Admin Pemkab, Pimpinan, dan Admin OPD ?>
<div style="margin-bottom: 15px;">
    <a href="<?= base_url('/spt/create') ?>"><button type="button">Terbitkan SPT Baru</button></a>
</div>
<?php endif; ?>

<table border="1" cellpadding="10" cellspacing="0" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor SPT</th>
            <th>Tanggal SPT</th>
            <th>Perihal</th>
            <th>Periode Pelaksanaan</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($spt_list)) : ?>
            <?php $no = 1; foreach ($spt_list as $row) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= esc($row['nomor_spt']) ?></td>
                    <td><?= esc($row['tanggal_spt']) ?></td>
                    <td><?= esc($row['perihal']) ?></td>
                    <td><?= esc($row['tanggal_mulai']) ?> s/d <?= esc($row['tanggal_selesai']) ?></td>
                    <td><?= esc($row['status']) ?></td>
                    <td>
                        <a href="<?= base_url('/spt/detail/' . $row['id_spt']) ?>"><button type="button">Detail</button></a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="7" style="text-align: center;">Belum ada data SPT.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
