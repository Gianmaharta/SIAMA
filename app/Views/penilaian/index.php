<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Penilaian Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Penilaian Arsip (Admin Pemkab)</h2>

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

<div style="margin-bottom: 15px;">
    <strong>Filter Status: </strong>
    <a href="<?= base_url('/penilaian?status=semua') ?>" style="margin-right: 10px; <?= $filter == 'semua' ? 'font-weight:bold;' : '' ?>">Semua</a> |
    <a href="<?= base_url('/penilaian?status=belum') ?>" style="margin-right: 10px; margin-left: 10px; <?= $filter == 'belum' ? 'font-weight:bold;' : '' ?>">Belum Dinilai</a> |
    <a href="<?= base_url('/penilaian?status=sudah') ?>" style="margin-left: 10px; <?= $filter == 'sudah' ? 'font-weight:bold;' : '' ?>">Sudah Dinilai</a>
</div>

<table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead>
        <tr>
            <th>No</th>
            <th>Nomor Arsip</th>
            <th>Nama Arsip</th>
            <th>OPD</th>
            <th>Skor Prioritas</th>
            <th>Status Autentikasi</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($arsip)) : ?>
            <?php $i = 1; foreach ($arsip as $row) : ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= esc($row['nomor_arsip']) ?></td>
                    <td><?= esc($row['nama_arsip']) ?></td>
                    <td><?= esc($row['nama_opd']) ?></td>
                    <td style="text-align: center;">
                        <?= $row['skor_prioritas'] !== null ? esc($row['skor_prioritas']) : '-' ?>
                    </td>
                    <td style="text-align: center;">
                        <?php if ($row['status_autentikasi'] === 'Belum Dinilai') : ?>
                            <span style="color: red;">Belum Dinilai</span>
                        <?php elseif ($row['status_autentikasi'] === 'Lolos Evaluasi') : ?>
                            <span style="color: green; font-weight: bold;">Lolos Evaluasi</span>
                        <?php else : ?>
                            <span style="color: orange;"><?= esc($row['status_autentikasi']) ?></span>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <?php if ($row['status_autentikasi'] === 'Belum Dinilai') : ?>
                            <a href="<?= base_url('/penilaian/form/' . $row['id_arsip']) ?>">Beri Penilaian</a>
                        <?php else : ?>
                            <a href="<?= base_url('/penilaian/detail/' . $row['id_arsip']) ?>">Lihat Detail</a> | 
                            <a href="<?= base_url('/penilaian/form/' . $row['id_arsip']) ?>">Edit Penilaian</a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="7" style="text-align: center;">Tidak ada arsip yang sesuai filter.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?= $this->endSection() ?>
