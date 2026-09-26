<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Dashboard Pengawasan Capaian Alih Media OPD - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>📊 Dashboard Pengawasan Capaian Alih Media OPD</h2>
<p>Pemantauan kinerja digitalisasi arsip seluruh OPD di Kabupaten Buleleng.</p>

<!-- Filter Tahun -->
<div style="margin-bottom: 20px;">
    <form method="get" action="<?= base_url('/pengawasan') ?>" style="display: inline-flex; align-items: center; gap: 10px;">
        <label for="tahun"><strong>Filter Tahun Kearsipan:</strong></label>
        <select name="tahun" id="tahun" style="padding: 6px 12px; border: 1px solid #ccc; border-radius: 4px;">
            <?php foreach ($tahun_list as $t) : ?>
                <option value="<?= $t ?>" <?= ($tahun == $t) ? 'selected' : '' ?>><?= $t ?></option>
            <?php endforeach; ?>
        </select>
        <button type="submit" style="background: #1a5276; color: white; padding: 6px 15px; border: none; border-radius: 4px; cursor: pointer;">Tampilkan</button>
    </form>
</div>

<!-- Tabel Pengawasan -->
<table border="1" cellpadding="10" cellspacing="0" style="width: 100%; border-collapse: collapse;">
    <thead style="background: #1a5276; color: white;">
        <tr>
            <th>No</th>
            <th>Nama OPD</th>
            <th style="text-align: center;">Total SPT Terbit</th>
            <th style="text-align: center;">Total Berkas Target</th>
            <th style="text-align: center;">Berkas Selesai</th>
            <th style="text-align: center;">Persentase Capaian (%)</th>
            <th style="text-align: center;">Status Kinerja</th>
        </tr>
    </thead>
    <tbody>
        <?php if (!empty($data_pengawasan)) : ?>
            <?php $no = 1; foreach ($data_pengawasan as $row) : ?>
                <tr>
                    <td style="text-align: center;"><?= $no++ ?></td>
                    <td><strong><?= esc($row['nama_opd']) ?></strong></td>
                    <td style="text-align: center;"><?= $row['total_spt'] ?></td>
                    <td style="text-align: center;"><?= $row['total_berkas'] ?></td>
                    <td style="text-align: center;"><?= $row['berkas_selesai'] ?></td>
                    <td style="text-align: center;">
                        <!-- Progress bar visual -->
                        <div style="background: #ecf0f1; border-radius: 10px; height: 22px; position: relative; overflow: hidden;">
                            <div style="background: <?= $row['status_color'] ?>; height: 100%; width: <?= min($row['persentase'], 100) ?>%; border-radius: 10px; transition: width 0.5s;"></div>
                            <span style="position: absolute; top: 2px; left: 50%; transform: translateX(-50%); font-size: 12px; font-weight: bold; color: #2c3e50;">
                                <?= $row['persentase'] ?>%
                            </span>
                        </div>
                    </td>
                    <td style="text-align: center;">
                        <span style="background: <?= $row['status_color'] ?>; color: white; padding: 4px 12px; border-radius: 12px; font-size: 13px; font-weight: bold;">
                            <?= $row['status_kinerja'] ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else : ?>
            <tr>
                <td colspan="7" style="text-align: center; padding: 30px;">Belum ada data OPD.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<!-- Legenda -->
<div style="margin-top: 20px; padding: 12px; background: #f9f9f9; border: 1px solid #ddd; border-radius: 6px;">
    <strong>Keterangan Status Kinerja:</strong><br>
    <span style="display: inline-block; width: 14px; height: 14px; background: #27ae60; border-radius: 50%; vertical-align: middle;"></span> <strong>Sangat Baik</strong> (&gt; 80%) &nbsp; | &nbsp;
    <span style="display: inline-block; width: 14px; height: 14px; background: #f39c12; border-radius: 50%; vertical-align: middle;"></span> <strong>Baik</strong> (50% — 80%) &nbsp; | &nbsp;
    <span style="display: inline-block; width: 14px; height: 14px; background: #e74c3c; border-radius: 50%; vertical-align: middle;"></span> <strong>Kurang</strong> (&lt; 50%)
</div>

<?= $this->endSection() ?>
