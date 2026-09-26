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
            <li><a href="<?= base_url('/pengawasan') ?>"><strong>📊 Pengawasan OPD</strong></a></li>
        <?php endif; ?>
        <li><a href="<?= base_url('/berita-acara') ?>">Berita Acara</a></li>
        <?php if (in_array(session()->get('nama_role'), ['Admin_Pemkab', 'Admin_OPD'])) : ?>
            <li><a href="<?= base_url('/users') ?>">Manajemen Pengguna</a></li>
        <?php endif; ?>
        <?php if (session()->get('nama_role') === 'Admin_Pemkab') : ?>
            <li>
                Audit Log:
                <ul>
                    <li><a href="<?= base_url('/audit-log/access') ?>">Access Log</a></li>
                    <li><a href="<?= base_url('/audit-log/activity') ?>">Activity Log</a></li>
                </ul>
            </li>
        <?php endif; ?>
        <?php if (in_array(session()->get('nama_role'), ['Admin_Pemkab', 'Admin_OPD'])) : ?>
            <li><a href="<?= base_url('/jra') ?>">Master JRA</a></li>
        <?php endif; ?>
        <?php if (in_array(session()->get('nama_role'), ['Admin_Pemkab', 'Admin_OPD', 'Pimpinan', 'Kepala_Bidang', 'Arsiparis'])) : ?>
            <li>
                <?php
                    $notifModel = new \App\Models\NotificationModel();
                    $unread = $notifModel->getUnreadCount(session()->get('user_id'));
                ?>
                <a href="<?= base_url('/notification') ?>">
                    Notifikasi 
                    <?php if ($unread > 0): ?>
                        <span style="background-color: red; color: white; border-radius: 50%; padding: 2px 6px; font-size: 12px;"><?= $unread ?></span>
                    <?php endif; ?>
                </a>
            </li>
        <?php endif; ?>
    </ul>
</nav>
