<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Berita Acara - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Detail Berita Acara</h2>

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('/berita-acara') ?>">&larr; Kembali</a>
</div>

<?php if (session()->getFlashdata('success')) : ?>
    <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('success') ?>
    </div>
<?php endif; ?>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
    <h3>Informasi Metadata</h3>
    <table border="0" cellpadding="5">
        <tr><td width="150"><strong>Nomor BA</strong></td><td>: <?= esc($ba['nomor_ba']) ?></td></tr>
        <tr><td><strong>Tanggal</strong></td><td>: <?= esc($ba['tanggal_ba']) ?></td></tr>
        <tr><td><strong>Nomor SPT</strong></td><td>: <?= esc($ba['nomor_spt'] ?: '-') ?></td></tr>
        <tr><td><strong>OPD</strong></td><td>: <?= esc($ba['nama_opd']) ?></td></tr>
        <tr>
            <td><strong>Status</strong></td>
            <td>: 
                <span style="font-weight: bold; color: <?= $ba['status_persetujuan'] === 'Selesai' ? 'green' : ($ba['status_persetujuan'] === 'Revisi' ? 'red' : 'orange') ?>">
                    <?= esc($ba['status_persetujuan']) ?>
                </span>
            </td>
        </tr>
        <?php if ($ba['status_persetujuan'] === 'Revisi' && !empty($ba['catatan_revisi'])) : ?>
            <tr><td><strong>Catatan Revisi</strong></td><td style="color: red;">: <?= nl2br(esc($ba['catatan_revisi'])) ?></td></tr>
        <?php endif; ?>
    </table>
</div>

<div style="margin-bottom: 20px;">
    <h3>Daftar Arsip</h3>
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse;">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Arsip</th>
                <th>Nama Arsip</th>
                <th>Kurun Waktu</th>
                <th>Kondisi</th>
            </tr>
        </thead>
        <tbody>
            <?php $i=1; foreach ($arsip_detail as $arsip) : ?>
                <tr>
                    <td><?= $i++ ?></td>
                    <td><?= esc($arsip['nomor_arsip']) ?></td>
                    <td><?= esc($arsip['nama_arsip']) ?></td>
                    <td><?= esc($arsip['kurun_waktu']) ?></td>
                    <td><?= esc($arsip['kondisi']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div style="border: 1px solid #4CAF50; padding: 15px; margin-bottom: 20px; background-color: #e8f5e9;">
    <h3>Riwayat Penandatangan</h3>
    <ul>
        <li><strong>Pelaksana/Arsiparis:</strong> <?= esc($ba['nama_pelaksana']) ?></li>
        <li><strong>Kepala Bidang:</strong> <?= $ba['nama_kabid'] ? esc($ba['nama_kabid']) : '<i style="color:#888;">Belum diverifikasi</i>' ?></li>
        <li><strong>Pimpinan:</strong> <?= $ba['nama_pimpinan'] ? esc($ba['nama_pimpinan']) : '<i style="color:#888;">Belum ditandatangani</i>' ?></li>
    </ul>
</div>

<?php 
$role = session()->get('nama_role');
$status = $ba['status_persetujuan'];
?>

<?php if ($role === 'Kepala_Bidang' && $status === 'Draf_Kabid') : ?>
    <div style="border: 1px solid orange; padding: 15px; background-color: #fff3cd;">
        <h3>Aksi Verifikasi Kepala Bidang</h3>
        <form action="<?= base_url('/berita-acara/verifikasi-kabid/' . $ba['id_berita_acara']) ?>" method="post">
            <div style="margin-bottom: 15px;">
                <label><strong>Keputusan:</strong></label><br>
                <input type="radio" name="keputusan" value="Approve" required id="kep_approve"> <label for="kep_approve">Setujui (Teruskan ke Pimpinan)</label><br>
                <input type="radio" name="keputusan" value="Reject" required id="kep_reject"> <label for="kep_reject">Tolak (Minta Revisi)</label>
            </div>
            <div style="margin-bottom: 15px;">
                <label><strong>Catatan Revisi (Opsional jika Setuju):</strong></label><br>
                <textarea name="catatan_revisi" rows="3" style="width: 100%; padding: 8px;"></textarea>
            </div>
            <button type="submit" style="padding: 10px 20px; cursor: pointer; background-color: orange; color: white; border: none;">Kirim Keputusan</button>
        </form>
    </div>
<?php endif; ?>

<?php if ($role === 'Pimpinan' && $status === 'Draf_Pimpinan') : ?>
    <div style="border: 1px solid blue; padding: 15px; background-color: #cce5ff;">
        <h3>Aksi Pengesahan Pimpinan</h3>
        <p>Anda akan mengesahkan dokumen Berita Acara ini secara final.</p>
        <form action="<?= base_url('/berita-acara/ttd-pimpinan/' . $ba['id_berita_acara']) ?>" method="post">
            <button type="submit" style="padding: 10px 20px; cursor: pointer; background-color: blue; color: white; border: none;">Tandatangani & Sahkan</button>
        </form>
    </div>
<?php endif; ?>

<?php if ($status === 'Selesai') : ?>
    <div style="border: 1px solid #27ae60; padding: 15px; background-color: #d5f5e3; margin-top: 15px;">
        <h3>✅ Berita Acara Telah Disahkan</h3>
        <p>Dokumen ini telah ditandatangani dan disahkan secara resmi. Anda dapat mengunduh salinan PDF-nya.</p>
        <a href="<?= base_url('/berita-acara/cetak/' . $ba['id_berita_acara']) ?>" target="_blank"
           style="display: inline-block; background: #27ae60; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px; font-weight: bold;">
            📄 Unduh Berita Acara (PDF)
        </a>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
