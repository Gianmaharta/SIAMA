<nav style="border-right: 1px solid #000; padding-right: 20px; min-height: 400px;">
    <h3>Navigasi</h3>
    <ul>
        <li><a href="<?= base_url('/dashboard') ?>">Dashboard</a></li>
        <li><a href="<?= base_url('/opd') ?>">Master OPD</a></li>
        <li><a href="<?= base_url('/bidang') ?>">Master Bidang</a></li>
        <li><a href="<?= base_url('/spt') ?>">Surat Perintah Tugas (SPT)</a></li>
        <li><a href="<?= base_url('/arsip') ?>">Pengelolaan Arsip</a></li>
        <?php if (session()->get('nama_role') === 'Admin_Pemkab') : ?>
            <li><a href="<?= base_url('/penilaian') ?>">Penilaian Arsip</a></li>
        <?php endif; ?>
        <li><a href="<?= base_url('/berita-acara') ?>">Berita Acara</a></li>
        <?php if (session()->get('nama_role') === 'Admin_Pemkab') : ?>
            <li>
                Audit Log:
                <ul>
                    <li><a href="<?= base_url('/audit-log/access') ?>">Access Log</a></li>
                    <li><a href="<?= base_url('/audit-log/activity') ?>">Activity Log</a></li>
                </ul>
            </li>
        <?php endif; ?>
    </ul>
</nav>
