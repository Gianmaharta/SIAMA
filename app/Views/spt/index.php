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

<?php $nama_role = session()->get('nama_role'); ?>
<?php if ($nama_role === 'Pimpinan') : ?>
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
            <th>Status SPT</th>
            <th>Status Penugasan</th>
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
                    <td><?= esc($row['status_penugasan']) ?></td>
                    <td>
                        <a href="<?= base_url('/spt/detail/' . $row['id_spt']) ?>"><button type="button">Detail</button></a>
                        <?php if (!empty($row['file_spt'])) : ?>
                            <a href="<?= base_url('uploads/spt/' . $row['file_spt']) ?>" target="_blank" style="margin-left: 5px; text-decoration: none; font-size: 14px;">📄 PDF</a>
                        <?php endif; ?>
                        
                        <?php if ($nama_role === 'Pimpinan') : ?>
                            <form action="<?= base_url('/spt/delete/' . $row['id_spt']) ?>" method="POST" style="display:inline-block; margin-left: 5px;">
                                <?= csrf_field() ?>
                                <button type="submit" style="background-color: #e74c3c; color: white; border: none; padding: 4px 8px; cursor: pointer; border-radius: 3px;" onclick="return confirm('Yakin ingin menghapus SPT ini? File PDF-nya juga akan terhapus secara permanen.')">Hapus</button>
                            </form>
                        <?php endif; ?>

                        <?php if (in_array($nama_role, ['Kepala_Bidang', 'Admin_OPD']) && $row['status_penugasan'] === 'belum_ditugaskan') : ?>
                            <a href="<?= base_url('/spt/assign/' . $row['id_spt']) ?>" style="margin-left: 5px;"><button type="button" style="background-color: #3498db; color: white; border: none; padding: 4px 8px; border-radius: 3px; cursor: pointer;">Tugaskan Arsiparis</button></a>
                        <?php endif; ?>
                        
                        <?php if ($nama_role === 'Arsiparis' && in_array($row['status_penugasan'], ['ditugaskan', 'proses'])) : ?>
                            <form action="<?= base_url('/spt/start-process/' . $row['id_spt']) ?>" method="POST" style="display:inline-block; margin-left: 5px;">
                                <?= csrf_field() ?>
                                <button type="submit" style="background-color: #2ecc71; color: white; border: none; padding: 4px 8px; cursor: pointer; border-radius: 3px;">Mulai Proses Pengarsipan</button>
                            </form>
                        <?php endif; ?>
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
