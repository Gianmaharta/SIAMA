<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Tambah Pengguna - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Tambah Pengguna Baru</h2>
<a href="<?= base_url('/users') ?>">&larr; Kembali ke Daftar Pengguna</a>
<br><br>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/users/store') ?>" method="POST" style="max-width: 600px;">
    <!-- Nama -->
    <div style="margin-bottom: 15px;">
        <label for="nama"><strong>Nama Lengkap <span style="color:red">*</span></strong></label><br>
        <input type="text" id="nama" name="nama" value="<?= old('nama') ?>" style="width: 100%; padding: 8px;" required>
    </div>

    <!-- Email -->
    <div style="margin-bottom: 15px;">
        <label for="email"><strong>Email <span style="color:red">*</span></strong></label><br>
        <input type="email" id="email" name="email" value="<?= old('email') ?>" style="width: 100%; padding: 8px;" required>
        <small style="color: #666;">Digunakan untuk login.</small>
    </div>

    <!-- Role -->
    <div style="margin-bottom: 15px;">
        <label for="id_role"><strong>Hak Akses (Role) <span style="color:red">*</span></strong></label><br>
        <select id="id_role" name="id_role" style="width: 100%; padding: 8px;" required>
            <option value="">-- Pilih Role --</option>
            <?php foreach ($roles as $r) : ?>
                <option value="<?= esc($r['id_role']) ?>" <?= old('id_role') == $r['id_role'] ? 'selected' : '' ?>>
                    <?= esc($r['nama_role']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <!-- OPD -->
    <div style="margin-bottom: 15px;">
        <label for="id_opd"><strong>Perangkat Daerah (OPD) <span style="color:red">*</span></strong></label><br>
        <?php if ($userRole === 'Admin_OPD') : ?>
            <input type="hidden" name="id_opd" value="<?= esc(session()->get('id_opd')) ?>">
            <input type="text" value="[Terkunci ke OPD Anda]" style="width: 100%; padding: 8px; background: #eee;" disabled>
        <?php else : ?>
            <select id="id_opd" name="id_opd" style="width: 100%; padding: 8px;" required>
                <option value="">-- Pilih OPD --</option>
                <?php foreach ($opds as $o) : ?>
                    <option value="<?= esc($o['id_opd']) ?>" <?= old('id_opd') == $o['id_opd'] ? 'selected' : '' ?>>
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
                        <option value="<?= esc($b['id_bidang']) ?>" <?= old('id_bidang') == $b['id_bidang'] ? 'selected' : '' ?>>
                            <?= esc($b['nama_bidang']) ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>
    <?php endif; ?>

    <div style="margin-bottom: 20px; padding: 10px; background: #eaf0fb; border: 1px solid #c2d6f2;">
        <strong>Catatan Keamanan:</strong><br>
        Password pengguna akan secara otomatis diatur menjadi: <code style="font-size: 16px; font-weight: bold; color: #c0392b;">Admin123!</code><br>
        Pengguna wajib mengganti password tersebut saat pertama kali login.
    </div>

    <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; cursor: pointer; font-size: 15px;">
        Simpan Pengguna Baru
    </button>
</form>

<?= $this->endSection() ?>
