<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Form Penilaian Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Form Penilaian Arsip</h2>

<div style="margin-bottom: 20px;">
    <a href="<?= base_url('/penilaian') ?>">&larr; Kembali ke Daftar Penilaian</a>
</div>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<div style="display: flex; gap: 20px;">
    <!-- Kolom Kiri: Document Viewer (60%) -->
    <div style="flex: 6; border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9;">
        <h3 style="margin-top: 0;">Pratinjau Dokumen</h3>
        
        <?php if (!empty($arsip['file_arsip'])) : ?>
            <div style="margin-bottom: 10px;">
                <a href="<?= base_url('uploads/arsip/' . $arsip['file_arsip']) ?>" target="_blank" style="display: inline-block; padding: 5px 10px; background-color: #007bff; color: white; text-decoration: none; border-radius: 3px;">Buka Dokumen di Tab Baru</a>
            </div>
            <iframe src="<?= base_url('uploads/arsip/' . $arsip['file_arsip']) ?>" width="100%" height="680px" style="border: 1px solid #ccc;"></iframe>
        <?php else : ?>
            <div style="padding: 20px; background-color: #fff3cd; color: #856404; border: 1px solid #ffeeba; text-align: center;">
                Berkas arsip digital tidak ditemukan atau belum diunggah.
            </div>
        <?php endif; ?>
    </div>

    <!-- Kolom Kanan: Informasi & Form Penilaian (40%) -->
    <div style="flex: 4;">
        <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
            <h3 style="margin-top: 0;">Informasi Arsip</h3>
            <table border="0" cellpadding="5" width="100%">
                <tr>
                    <td width="120"><strong>Nomor Arsip</strong></td>
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

        <div style="border: 1px solid #ccc; padding: 15px; background-color: #f9f9f9;">
            <h3 style="margin-top: 0;">Form Input Penilaian</h3>
            <form action="<?= base_url('/penilaian/store/' . $arsip['id_arsip']) ?>" method="post">
                <div style="margin-bottom: 15px;">
                    <label for="skor_prioritas"><strong>Skor Prioritas (0 - 100)</strong></label><br>
                    <input type="number" name="skor_prioritas" id="skor_prioritas" min="0" max="100" required style="width: 100%; padding: 8px; box-sizing: border-box;" value="<?= esc($arsip['skor_prioritas']) ?>">
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="status_autentikasi"><strong>Status Autentikasi</strong></label><br>
                    <select name="status_autentikasi" id="status_autentikasi" required style="width: 100%; padding: 8px; box-sizing: border-box;">
                        <option value="">-- Pilih Status --</option>
                        <option value="Lolos Evaluasi" <?= $arsip['status_autentikasi'] == 'Lolos Evaluasi' ? 'selected' : '' ?>>Lolos Evaluasi</option>
                        <option value="Revisi Metadata" <?= $arsip['status_autentikasi'] == 'Revisi Metadata' ? 'selected' : '' ?>>Revisi Metadata</option>
                        <option value="Ditolak" <?= $arsip['status_autentikasi'] == 'Ditolak' ? 'selected' : '' ?>>Ditolak</option>
                        <option value="Menunggu Verifikasi Lanjutan" <?= $arsip['status_autentikasi'] == 'Menunggu Verifikasi Lanjutan' ? 'selected' : '' ?>>Menunggu Verifikasi Lanjutan</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="catatan_penilaian"><strong>Catatan Penilaian</strong></label><br>
                    <textarea name="catatan_penilaian" id="catatan_penilaian" rows="5" style="width: 100%; padding: 8px; box-sizing: border-box;"><?= esc($arsip['catatan_penilaian']) ?></textarea>
                </div>

                <button type="submit" style="padding: 10px 20px; cursor: pointer; width: 100%; background-color: #4CAF50; color: white; border: none; font-weight: bold;">Simpan Penilaian</button>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
