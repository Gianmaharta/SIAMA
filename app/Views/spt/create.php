<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Terbitkan SPT Baru - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Terbitkan Surat Perintah Tugas (SPT)</h2>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<form action="<?= base_url('/spt/store') ?>" method="POST">
    <?= csrf_field() ?>

    <div style="margin-bottom: 10px;">
        <label for="nomor_spt">Nomor SPT:</label><br>
        <input type="text" name="nomor_spt" id="nomor_spt" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="tanggal_spt">Tanggal Terbit SPT:</label><br>
        <input type="date" name="tanggal_spt" id="tanggal_spt" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="perihal">Perihal / Keterangan Penugasan:</label><br>
        <textarea name="perihal" id="perihal" rows="4" cols="50" required></textarea>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="tanggal_mulai">Tanggal Mulai Pelaksanaan:</label><br>
        <input type="date" name="tanggal_mulai" id="tanggal_mulai" required>
    </div>

    <div style="margin-bottom: 10px;">
        <label for="tanggal_selesai">Tanggal Selesai Pelaksanaan:</label><br>
        <input type="date" name="tanggal_selesai" id="tanggal_selesai" required>
    </div>

    <hr>
    <h3>Pilih Pelaksana (Arsiparis):</h3>
    <div style="margin-bottom: 15px;">
        <?php if (!empty($arsiparis_list)) : ?>
            <ul>
            <?php foreach ($arsiparis_list as $arsiparis) : ?>
                <li>
                    <input type="checkbox" name="pelaksana[]" id="user_<?= $arsiparis['id_user'] ?>" value="<?= $arsiparis['id_user'] ?>">
                    <label for="user_<?= $arsiparis['id_user'] ?>">
                        <?= esc($arsiparis['nama']) ?> (<?= esc($arsiparis['email']) ?>)
                    </label>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else : ?>
            <p style="color:red;">Tidak ada data Arsiparis di OPD ini.</p>
        <?php endif; ?>
    </div>

    <button type="submit">Terbitkan SPT</button>
    <a href="<?= base_url('/spt') ?>"><button type="button">Batal</button></a>
</form>
<?= $this->endSection() ?>
