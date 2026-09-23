<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail Arsip - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Detail Arsip Digital</h2>

<div style="margin-bottom: 15px;">
    <a href="<?= base_url('/arsip') ?>"><button type="button">&larr; Kembali ke Daftar</button></a>
    <?php if (session()->get('id_role') == 5) : ?>
        <a href="<?= base_url('/arsip/edit/' . $arsip['id_arsip']) ?>">
            <button type="button">Edit Data Arsip</button>
        </a>
    <?php endif; ?>
</div>

<?php if (session()->getFlashdata('error')) : ?>
    <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
        <?= session()->getFlashdata('error') ?>
    </div>
<?php endif; ?>

<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; max-width: 700px;">
    <tbody>
        <tr>
            <th style="width: 220px; text-align: left; background: #f2f2f2;">Nomor Arsip</th>
            <td><?= esc($arsip['nomor_arsip']) ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Nama / Judul Arsip</th>
            <td><?= esc($arsip['nama_arsip']) ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">OPD</th>
            <td><?= esc($arsip['nama_opd'] ?? '-') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Bidang</th>
            <td><?= esc($arsip['nama_bidang'] ?? '-') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Kode Klasifikasi</th>
            <td>
                <?= esc($arsip['kode_klasifikasi_text'] ?? '-') ?>
                <?php if (! empty($arsip['nama_klasifikasi'])) : ?>
                    — <?= esc($arsip['nama_klasifikasi']) ?>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Nomor SPT Terkait</th>
            <td><?= esc($arsip['nomor_spt'] ?? 'Tidak ada SPT terkait') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Tahun Penciptaan</th>
            <td><?= esc($arsip['kurun_waktu'] ?? '-') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Kondisi Fisik</th>
            <td><?= esc($arsip['kondisi']) ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Status Retensi</th>
            <td>
                <?php 
                    $status = esc($arsip['status_retensi_aktif'] ?? 'Aktif');
                    if ($status === 'Aktif') {
                        echo '<span style="background-color: #28a745; color: white; padding: 2px 5px; border-radius: 3px;">Aktif</span>';
                    } elseif ($status === 'Inaktif') {
                        echo '<span style="background-color: #ffc107; color: black; padding: 2px 5px; border-radius: 3px;">Inaktif</span>';
                    } elseif ($status === 'Musnah' || $status === 'Permanen') {
                        echo '<span style="background-color: ' . ($status === 'Musnah' ? '#dc3545' : '#17a2b8') . '; color: white; padding: 2px 5px; border-radius: 3px;">' . $status . '</span>';
                    } else {
                        echo esc($status);
                    }
                ?>
            </td>
        </tr>
        <?php if (!empty($arsip['tanggal_retensi_inaktif_berakhir'])): ?>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Berakhir Inaktif</th>
            <td><?= date('d M Y', strtotime($arsip['tanggal_retensi_inaktif_berakhir'])) ?></td>
        </tr>
        <?php endif; ?>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Skor Prioritas</th>
            <td><?= esc($arsip['skor_prioritas'] ?? 'Belum dinilai') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Status Autentikasi</th>
            <td><?= esc($arsip['status_autentikasi']) ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Status Verifikasi</th>
            <td><?= esc($arsip['status_verifikasi']) ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Diinput oleh</th>
            <td><?= esc($arsip['nama_arsiparis'] ?? '-') ?></td>
        </tr>
        <tr>
            <th style="text-align: left; background: #f2f2f2;">Berkas Digital</th>
            <td>
                <?php if (isset($arsip['status_retensi_aktif']) && $arsip['status_retensi_aktif'] === 'Musnah') : ?>
                    <em>Berkas telah dimusnahkan secara sistem.</em>
                <?php elseif ($file_url !== null) : ?>
                    <a href="<?= $file_url ?>" target="_blank">
                        <button type="button">&#128196; Lihat / Download Berkas</button>
                    </a>
                    <br>
                    <small>Nama file: <?= esc($arsip['file_arsip']) ?></small>
                <?php else : ?>
                    <em>Belum ada berkas digital yang diunggah.</em>
                <?php endif; ?>
            </td>
        </tr>
    </tbody>
</table>
<?= $this->endSection() ?>
