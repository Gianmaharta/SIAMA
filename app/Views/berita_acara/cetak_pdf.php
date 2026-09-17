<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Berita Acara - <?= esc($ba['nomor_ba']) ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.5;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #000;
            padding-bottom: 10px;
            margin-bottom: 20px;
        }
        .header h2, .header h3 {
            margin: 0;
            padding: 0;
        }
        .title {
            text-align: center;
            font-weight: bold;
            font-size: 14px;
            text-decoration: underline;
            margin-bottom: 5px;
        }
        .subtitle {
            text-align: center;
            margin-bottom: 20px;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #000;
            padding: 5px;
        }
        table.data-table th {
            background-color: #eee;
        }
        .signatures {
            width: 100%;
            margin-top: 50px;
        }
        .sig-col {
            width: 33.33%;
            float: left;
            text-align: center;
        }
        .sig-name {
            margin-top: 70px;
            font-weight: bold;
            text-decoration: underline;
        }
        .clearfix::after {
            content: "";
            clear: both;
            display: table;
        }
    </style>
</head>
<body>

    <div class="header">
        <h2>PEMERINTAH KABUPATEN BULELENG</h2>
        <h3><?= esc(strtoupper($ba['nama_opd'])) ?></h3>
    </div>

    <div class="title">BERITA ACARA ALIH MEDIA ARSIP</div>
    <div class="subtitle">Nomor: <?= esc($ba['nomor_ba']) ?></div>

    <p>Pada hari ini, tanggal <?= date('d-m-Y', strtotime($ba['tanggal_ba'])) ?>, bertempat di lingkungan <?= esc($ba['nama_opd']) ?>, kami yang bertanda tangan di bawah ini telah melakukan alih media dan/atau verifikasi terhadap arsip-arsip sebagai berikut:</p>

    <table class="data-table">
        <thead>
            <tr>
                <th>No</th>
                <th>Nomor Arsip</th>
                <th>Nama Arsip</th>
                <th>Kurun Waktu</th>
                <th>Kondisi Fisik</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($arsip_detail)) : ?>
                <?php $i=1; foreach ($arsip_detail as $arsip) : ?>
                    <tr>
                        <td style="text-align: center;"><?= $i++ ?></td>
                        <td><?= esc($arsip['nomor_arsip']) ?></td>
                        <td><?= esc($arsip['nama_arsip']) ?></td>
                        <td><?= esc($arsip['kurun_waktu']) ?></td>
                        <td><?= esc($arsip['kondisi']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="5" style="text-align: center;">Tidak ada data arsip</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <p>Demikian Berita Acara ini dibuat dengan sebenar-benarnya untuk dapat dipergunakan sebagaimana mestinya.</p>

    <div class="signatures clearfix">
        <div class="sig-col">
            <p>Dibuat Oleh,<br>Pelaksana / Arsiparis</p>
            <div class="sig-name"><?= esc($ba['nama_pelaksana']) ?></div>
        </div>
        <div class="sig-col">
            <p>Diverifikasi Oleh,<br>Kepala Bidang</p>
            <div class="sig-name"><?= $ba['nama_kabid'] ? esc($ba['nama_kabid']) : '(............................)' ?></div>
        </div>
        <div class="sig-col">
            <p>Mengesahkan,<br>Pimpinan / Kepala Dinas</p>
            <div class="sig-name"><?= $ba['nama_pimpinan'] ? esc($ba['nama_pimpinan']) : '(............................)' ?></div>
        </div>
    </div>

</body>
</html>
