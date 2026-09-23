<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Detail SPT - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Detail Surat Perintah Tugas (SPT)</h2>

<div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px; background-color: #f9f9f9;">
    <p><strong>Nomor SPT:</strong> <?= esc($spt['nomor_spt']) ?></p>
    <p><strong>Tanggal SPT:</strong> <?= esc($spt['tanggal_spt']) ?></p>
    <p><strong>Status:</strong> <?= esc($spt['status']) ?></p>
    <p><strong>Periode Pelaksanaan:</strong> <?= esc($spt['tanggal_mulai']) ?> s/d <?= esc($spt['tanggal_selesai']) ?></p>
    
    <hr>
    <p><strong>Perihal Penugasan:</strong></p>
    <div style="padding: 10px; background-color: #fff; border: 1px dashed #999; margin-bottom: 15px;">
        <?= nl2br(esc($spt['perihal'])) ?>
    </div>

    <?php if (!empty($spt['file_spt'])) : ?>
        <p><strong>Dokumen Fisik SPT:</strong></p>
        <button type="button" onclick="document.getElementById('modal_pdf_detail').style.display='block'" style="background-color: #e74c3c; color: white; padding: 10px 20px; border: none; font-weight: bold; border-radius: 4px; cursor: pointer;">
            📄 Lihat Dokumen SPT (PDF)
        </button>

        <!-- Modal PDF Detail -->
        <div id="modal_pdf_detail" style="display:none; position:fixed; z-index:999; left:0; top:0; width:100%; height:100%; overflow:auto; background-color:rgba(0,0,0,0.8);">
            <div style="background-color:#fff; margin: 5% auto; padding: 20px; border: 1px solid #888; width: 80%; height: 80%; position:relative;">
                <span onclick="document.getElementById('modal_pdf_detail').style.display='none'" style="position:absolute; top:10px; right:20px; color:#aaa; font-size:28px; font-weight:bold; cursor:pointer;">&times;</span>
                <h3>Viewer Dokumen SPT: <?= esc($spt['nomor_spt']) ?></h3>
                <iframe src="<?= base_url('uploads/spt/' . $spt['file_spt']) ?>" width="100%" height="90%" style="border:none;"></iframe>
            </div>
        </div>
    <?php endif; ?>
</div>

<h3>Daftar Pelaksana Tugas (Arsiparis):</h3>
<?php if (!empty($pelaksana_list)) : ?>
    <ul>
        <?php foreach ($pelaksana_list as $p) : ?>
            <li><?= esc($p['nama']) ?> (<?= esc($p['email']) ?>)</li>
        <?php endforeach; ?>
    </ul>
<?php else : ?>
    <p>Tidak ada data pelaksana yang terdaftar pada SPT ini.</p>
<?php endif; ?>

<div style="margin-top: 20px;">
    <?php $nama_role = session()->get('nama_role'); ?>
    <?php if (in_array($nama_role, ['Kepala_Bidang', 'Admin_OPD']) && $spt['status_penugasan'] === 'belum_ditugaskan') : ?>
        <a href="<?= base_url('/spt/assign/' . $spt['id_spt']) ?>"><button type="button" style="background-color: #3498db; color: white; border: none; padding: 8px 15px; cursor: pointer; margin-right: 10px;">Tugaskan Arsiparis</button></a>
    <?php endif; ?>
    <a href="<?= base_url('/spt') ?>"><button type="button" style="padding: 8px 15px;">Kembali ke Daftar SPT</button></a>
</div>

<?= $this->endSection() ?>
