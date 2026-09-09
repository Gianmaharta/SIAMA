<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistem Informasi Alih Media (SIAMA)</title>
</head>
<body>
    <h2>Dashboard SIAMA</h2>

    <?php if (session()->getFlashdata('error')) : ?>
        <div style="color: red; border: 1px solid red; padding: 10px; margin-bottom: 15px;">
            <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('success')) : ?>
        <div style="color: green; border: 1px solid green; padding: 10px; margin-bottom: 15px;">
            <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 20px;">
        <h3>Informasi Pengguna Aktif</h3>
        <p><strong>Nama Lengkap:</strong> <?= session()->get('nama_lengkap') ?></p>
        <p><strong>Email:</strong> <?= session()->get('email') ?></p>
        <p><strong>Role Aktif:</strong> <?= session()->get('nama_role') ?> (ID Role: <?= session()->get('id_role') ?>)</p>
        <p><strong>ID OPD:</strong> <?= session()->get('id_opd') ?? '<i>Tidak ada (Pemkab)</i>' ?></p>
    </div>

    <div>
        <a href="<?= base_url('/logout') ?>"><button type="button">Logout</button></a>
    </div>

    <hr>
    
    <p><i>Halaman ini hanya bisa diakses oleh pengguna yang sudah login.</i></p>

</body>
</html>
