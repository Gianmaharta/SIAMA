<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Notifikasi Sistem - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Notifikasi Sistem</h2>

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

<?php if (empty($notifications)) : ?>
    <p>Belum ada notifikasi saat ini.</p>
<?php else : ?>
    <?php foreach ($notifications as $notif) : ?>
        <div style="border: 1px solid #ccc; padding: 15px; margin-bottom: 15px; background-color: <?= $notif['is_read'] ? '#f9f9f9' : '#e6f7ff' ?>;">
            <h4 style="margin-top: 0;"><?= esc($notif['title']) ?></h4>
            <p><?= esc($notif['message']) ?></p>
            <small style="color: #666;"><?= date('d M Y H:i', strtotime($notif['created_at'])) ?></small>
            
            <?php if ($notif['action_type'] === 'retensi_arsip') : ?>
                <div style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px;">
                    <strong>Tindakan Lanjutan:</strong><br><br>
                    <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" style="display:inline-block;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="perpanjang">
                        <button type="submit" style="background-color: #28a745; color: white; border: none; padding: 5px 10px; cursor: pointer;">Perpanjang (1 Tahun)</button>
                    </form>
                    
                    <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" style="display:inline-block;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="inaktif">
                        <button type="submit" style="background-color: #ffc107; color: black; border: none; padding: 5px 10px; cursor: pointer;">Pindahkan ke Inaktif</button>
                    </form>

                    <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menetapkan arsip ini sebagai Musnah? File fisik tetap dipertahankan.')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="musnahkan">
                        <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer;">Musnahkan</button>
                    </form>
                </div>
            <?php elseif ($notif['action_type'] === 'retensi_inaktif_arsip') : ?>
                <div style="margin-top: 15px; border-top: 1px solid #ddd; padding-top: 10px;">
                    <strong>Tindakan Akhir Retensi:</strong><br><br>
                    <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" style="display:inline-block;" onsubmit="return confirm('Yakin ingin menetapkan arsip ini sebagai Musnah? File fisik tetap dipertahankan.')">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="musnahkan">
                        <button type="submit" style="background-color: #dc3545; color: white; border: none; padding: 5px 10px; cursor: pointer;">Musnahkan</button>
                    </form>

                    <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" style="display:inline-block;">
                        <?= csrf_field() ?>
                        <input type="hidden" name="action" value="permanen">
                        <button type="submit" style="background-color: #007bff; color: white; border: none; padding: 5px 10px; cursor: pointer;">Jadikan Permanen</button>
                    </form>
                </div>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>
