<header style="border-bottom: 1px solid #000; padding-bottom: 10px; margin-bottom: 20px;">

    <?php if (session()->has('original_admin_id')) : ?>
        <div style="background: linear-gradient(90deg, #e74c3c, #c0392b); color: #fff; padding: 10px 20px; margin-bottom: 10px; border-radius: 6px; display: flex; align-items: center; justify-content: space-between; font-size: 14px; box-shadow: 0 2px 8px rgba(0,0,0,0.2);">
            <span>
                ⚠️ <strong>Mode Penyamaran Aktif</strong> — Anda sedang menyamar sebagai:
                <strong><?= esc(session()->get('nama_lengkap')) ?></strong>
                (<?= esc(session()->get('nama_role')) ?> — <?= esc(session()->get('impersonated_opd_nama') ?? 'N/A') ?>)
            </span>
            <a href="<?= base_url('/users/switch-back') ?>"
               style="background: #fff; color: #c0392b; padding: 6px 14px; border-radius: 4px; text-decoration: none; font-weight: bold; font-size: 13px;">
                🔙 Kembali ke Admin Pemkab
            </a>
        </div>
    <?php endif; ?>

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
