<header style="border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 20px;">
    <h1>Sistem Informasi Alih Media Arsip (SIAMA)</h1>
    <p>
        <strong>Pengguna:</strong> <?= esc(session()->get('nama_lengkap')) ?> | 
        <strong>Role:</strong> <?= esc(session()->get('nama_role')) ?> | 
        <strong>ID OPD:</strong> <?= esc(session()->get('id_opd') ?? 'N/A') ?>
    </p>
    <div>
        <a href="<?= base_url('/logout') ?>"><button type="button">Logout</button></a>
    </div>
</header>
