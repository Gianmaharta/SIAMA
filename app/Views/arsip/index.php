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

<?php $nama_role = session()->get('nama_role'); ?>

<?php if ($nama_role === 'Arsiparis') : // Tombol Tambah Arsip hanya untuk Arsiparis ?>
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
            <th>Skor Prioritas</th>
            <th>Status Verifikasi</th>
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
                        <?= esc($row['kode_klasifikasi_text'] ?? '-') ?>
                        <?php if (! empty($row['nama_klasifikasi'])) : ?>
                            <br><small><?= esc($row['nama_klasifikasi']) ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($row['nama_bidang'] ?? '-') ?></td>
                    <td><?= (! empty($row['skor_prioritas'])) ? esc($row['skor_prioritas']) : 'Belum Dinilai' ?></td>
                    <td><?= esc($row['status_verifikasi']) ?></td>
                    <td>
                        <?php if (! empty($row['file_arsip'])) : ?>
                            <a href="<?= base_url('uploads/arsip/' . $row['file_arsip']) ?>" target="_blank">Lihat Berkas</a>
                        <?php else : ?>
                            <em>Belum ada</em>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="<?= base_url('/arsip/detail/' . $row['id_arsip']) ?>">
                            <button type="button">Detail</button>
                        </a>
                        <?php if ($nama_role === 'Arsiparis') : ?>
                            <a href="<?= base_url('/arsip/edit/' . $row['id_arsip']) ?>">
                                <button type="button">Edit</button>
                            </a>
                            <a href="<?= base_url('/arsip/delete/' . $row['id_arsip']) ?>"
                               onclick="return confirm('Yakin ingin menghapus arsip ini beserta file fisiknya?')">
                                <button type="button">Hapus</button>
                            </a>
                        <?php endif; ?>
                        
                        <?php if ($nama_role === 'Kepala_Bidang' && $row['status_verifikasi'] === 'Menunggu') : ?>
                            <form action="<?= base_url('/arsip/verify/' . $row['id_arsip']) ?>" method="POST" style="display:inline-block; margin-left: 5px;">
                                <?= csrf_field() ?>
                                <button type="submit" style="background-color: #27ae60; color: white; border: none; padding: 4px 8px; cursor: pointer; border-radius: 3px;" onclick="return confirm('Verifikasi arsip ini sebagai Selesai?')">Verifikasi</button>
                            </form>
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
