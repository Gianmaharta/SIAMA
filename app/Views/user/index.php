<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Pengguna - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Manajemen Pengguna</h2>
<p>Kelola data akun pengguna di sistem SIAMA.</p>

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
    <a href="<?= base_url('/users/create') ?>" style="background: #1a5276; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;">+ Tambah Pengguna</a>
</div>

<table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead style="background: #f4f4f4;">
        <tr>
            <th>No</th>
            <th>Nama</th>
            <th>Email</th>
            <th>Role</th>
            <th>OPD</th>
            <th>Status</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($users)) : ?>
            <?php $no = 1; foreach ($users as $user) : ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><?= esc($user['nama']) ?></td>
                    <td><?= esc($user['email']) ?></td>
                    <td><strong style="color: #1a5276;"><?= esc($user['nama_role'] ?: '-') ?></strong></td>
                    <td><?= esc($user['nama_opd'] ?: '-') ?></td>
                    <td style="text-align: center;">
                        <?php if ($user['is_active']) : ?>
                            <span style="color: green; font-weight: bold;">Aktif</span>
                        <?php else : ?>
                            <span style="color: red; font-weight: bold;">Non-Aktif</span>
                        <?php endif; ?>
                        
                        <?php if ($user['is_default_password']) : ?>
                            <br><small style="color: #c0392b;">(Blm Ganti Password)</small>
                        <?php endif; ?>
                    </td>
                    <td style="text-align: center;">
                        <?php if (session()->get('nama_role') === 'Admin_Pemkab' || session()->has('original_admin_id')) : ?>
                            <a href="<?= base_url('/users/switch/' . $user['id_user']) ?>" style="color: #8e44ad; text-decoration: none; font-weight: bold;" onclick="return confirm('Anda akan menyamar sebagai <?= esc($user['nama']) ?>. Lanjutkan?')">🔀 Masuk Sebagai</a> |
                        <?php endif; ?>
                        <a href="<?= base_url('/users/edit/' . $user['id_user']) ?>" style="color: #d68910; text-decoration: none;">Edit</a> | 
                        <a href="<?= base_url('/users/delete/' . $user['id_user']) ?>" style="color: #c0392b; text-decoration: none;" onclick="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?')">Hapus</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="8" style="text-align: center; padding: 20px;">Belum ada data pengguna.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>
<?= $this->endSection() ?>
