<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Penilaian Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Detail Penilaian Arsip</h2>

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('/penilaian') ?>">&larr; Kembali ke Daftar Penilaian</a>
</div>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
    <h3>Informasi Arsip</h3>
    <table border="0" cellpadding="5">
        <tr>
            <td width="200"><strong>Nomor Arsip</strong></td>
            <td>: <?= esc($arsip['nomor_arsip']) ?></td>
        </tr>
        <tr>
            <td><strong>Nama Arsip</strong></td>
            <td>: <?= esc($arsip['nama_arsip']) ?></td>
        </tr>
        <tr>
            <td><strong>OPD Pemilik</strong></td>
            <td>: <?= esc($arsip['nama_opd']) ?></td>
        </tr>
        <tr>
            <td><strong>Kurun Waktu</strong></td>
            <td>: <?= esc($arsip['kurun_waktu']) ?></td>
        </tr>
    </table>
</div>

<div style="border: 1px solid #4CAF50; padding: 15px; background-color: #e8f5e9;">
    <h3 style="color: #2E7D32;">Hasil Penilaian</h3>
    <table border="0" cellpadding="5">
        <tr>
            <td width="200"><strong>Skor Prioritas</strong></td>
            <td>: <span style="font-size: 1.2em; font-weight: bold;"><?= esc($arsip['skor_prioritas']) ?> / 100</span></td>
        </tr>
        <tr>
            <td><strong>Status Autentikasi</strong></td>
            <td>: 
                <?php if ($arsip['status_autentikasi'] === 'Lolos Evaluasi') : ?>
                    <span style="color: green; font-weight: bold;">Lolos Evaluasi</span>
                <?php else : ?>
                    <span style="color: orange; font-weight: bold;"><?= esc($arsip['status_autentikasi']) ?></span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td><strong>Catatan Penilaian</strong></td>
            <td>: <br><div style="margin-top: 10px; padding: 10px; background-color: #fff; border: 1px dashed #ccc;"><?= nl2br(esc($arsip['catatan_penilaian'])) ?></div></td>
        </tr>
        <tr>
            <td><strong>Tanggal Penilaian</strong></td>
            <td>: <?= esc($arsip['tanggal_penilaian']) ?></td>
        </tr>
        <tr>
            <td><strong>Penilai (Admin Pemkab)</strong></td>
            <td>: <?= esc($arsip['nama_penilai']) ?></td>
        </tr>
    </table>
</div>

<div style="margin-top: 20px;">
    <a href="<?= base_url('/penilaian/form/' . $arsip['id_arsip']) ?>"><button style="padding: 10px 20px; cursor: pointer;">Edit Penilaian</button></a>
</div>

<?= $this->endSection() ?>
