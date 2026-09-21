<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Edit Pengguna - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Edit Pengguna</h2>
<a href="<?= base_url('/users') ?>">&larr; Kembali ke Daftar Pengguna</a>
<br><br>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/users/update/' . $user['id_user']) ?>" method="POST" style="max-width: 600px;">
    <!-- Nama -->
    <div style="margin-bottom: 15px;">
        <label for="nama"><strong>Nama Lengkap <span style="color:red">*</span></strong></label><br>
        <input type="text" id="nama" name="nama" value="<?= old('nama', esc($user['nama'])) ?>" style="width: 100%; padding: 8px;" required>
    </div>

    <!-- Email -->
    <div style="margin-bottom: 15px;">
        <label for="email"><strong>Email <span style="color:red">*</span></strong></label><br>
        <input type="email" id="email" name="email" value="<?= old('email', esc($user['email'])) ?>" style="width: 100%; padding: 8px;" required>
    </div>

    <!-- Role -->
    <div style="margin-bottom: 15px;">
        <label for="id_role"><strong>Hak Akses (Role) <span style="color:red">*</span></strong></label><br>
        <select id="id_role" name="id_role" style="width: 100%; padding: 8px;" required>
            <option value="">-- Pilih Role --</option>
            <?php foreach ($roles as $r) : ?>
                <option value="<?= esc($r['id_role']) ?>" <?= old('id_role', $current_role) == $r['id_role'] ? 'selected' : '' ?>>
                    <?= esc($r['nama_role']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- OPD -->
    <div style="margin-bottom: 15px;">
        <label for="id_opd"><strong>Perangkat Daerah (OPD) <span style="color:red">*</span></strong></label><br>
        <?php if ($userRole === 'Admin_OPD') : ?>
            <input type="hidden" name="id_opd" value="<?= esc($user['id_opd']) ?>">
            <input type="text" value="[Terkunci ke OPD Anda]" style="width: 100%; padding: 8px; background: #eee;" disabled>
        <?php else : ?>
            <select id="id_opd" name="id_opd" style="width: 100%; padding: 8px;" required>
                <option value="">-- Pilih OPD --</option>
                <?php foreach ($opds as $o) : ?>
                    <option value="<?= esc($o['id_opd']) ?>" <?= old('id_opd', $user['id_opd']) == $o['id_opd'] ? 'selected' : '' ?>>
                        <?= esc($o['nama_opd']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        <?php endif; ?>
    </div>

    <!-- Bidang -->
    <?php if ($userRole === 'Admin_OPD') : ?>
        <div style="margin-bottom: 15px;">
            <label for="id_bidang"><strong>Bidang <small>(Opsional)</small></strong></label><br>
            <select id="id_bidang" name="id_bidang" style="width: 100%; padding: 8px;">
                <option value="">-- Tidak Terikat Bidang --</option>
                <?php if (!empty($bidangs)) : ?>
                    <?php foreach ($bidangs as $b) : ?>
                        <option value="<?= esc($b['id_bidang']) ?>" <?= old('id_bidang', $user['id_bidang']) == $b['id_bidang'] ? 'selected' : '' ?>>
                            <?= esc($b['nama_bidang']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    <?php endif; ?>

    <!-- Status Aktif -->
    <div style="margin-bottom: 20px;">
        <label>
            <input type="checkbox" name="is_active" value="1" <?= old('is_active', $user['is_active']) ? 'checked' : '' ?>>
            <strong>Akun Aktif</strong> (Bisa digunakan untuk login)
        </label>
    </div>

    <button type="submit" style="background-color: #d68910; color: white; padding: 10px 20px; border: none; cursor: pointer; font-size: 15px;">
        Simpan Perubahan
    </button>
</form>

<hr style="margin: 30px 0;">

<div style="background-color: #f9ebea; border: 1px solid #e6b0aa; padding: 15px; border-radius: 4px; max-width: 600px;">
    <h3 style="color: #c0392b; margin-top: 0;">Keamanan & Sandi</h3>
    <p style="font-size: 14px; margin-bottom: 15px;">Gunakan tombol ini jika pengguna lupa kata sandi. Password akan dikembalikan ke <strong>Admin123!</strong> dan pengguna akan diminta segera menggantinya saat pertama kali masuk kembali.</p>
    <a href="<?= base_url('/users/reset-password/' . $user['id_user']) ?>" onclick="return confirm('Yakin ingin me-reset password pengguna ini ke bawaan sistem?')" style="background-color: #c0392b; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px; display: inline-block;">
        Reset Password ke Default
    </a>
</div>

<?= $this->endSection() ?>
