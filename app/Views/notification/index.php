<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Pusat Notifikasi - SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<!-- Header Halaman Notifikasi -->
<div class="d-flex flex-column flex-md-row align-items-start align-items-md-center justify-content-between gap-3 notif-page-header">
    <div>
        <h1 class="notif-page-title d-flex align-items-center gap-2">
            <i class="bi bi-bell-fill text-primary fs-4"></i>
            Pusat Notifikasi
        </h1>
        <p class="notif-page-subtitle">Pantau penugasan alih media, alur verifikasi berkas, pengesahan Berita Acara, dan aktivitas akun Anda.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <?php if ($totalUnread > 0) : ?>
            <button type="button" class="btn btn-outline-primary btn-sm d-inline-flex align-items-center gap-2 px-3 py-2 rounded-3 fw-semibold shadow-sm" id="btnMarkAllReadIndex" style="font-size: 13px;">
                <i class="bi bi-check2-all fs-6"></i>
                <span>Tandai Semua Dibaca</span>
            </button>
        <?php endif; ?>
    </div>
</div>

<!-- Flash Message: Success -->
<?php if (session()->getFlashdata('success')) : ?>
    <div class="alert alert-success alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-check-circle-fill fs-5 text-success"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('success') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Flash Message: Error -->
<?php if (session()->getFlashdata('error')) : ?>
    <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center gap-2 mb-4 rounded-3 border-0 shadow-sm" role="alert">
        <i class="bi bi-exclamation-triangle-fill fs-5 text-danger"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Filter Tabs Navigation -->
<div class="notif-filter-nav">
    <a href="<?= base_url('/notification?filter=all') ?>" class="notif-filter-btn <?= $currentFilter === 'all' ? 'active' : '' ?>">
        <i class="bi bi-collection"></i>
        <span>Semua</span>
        <span class="filter-count"><?= $totalAll ?></span>
    </a>
    <a href="<?= base_url('/notification?filter=unread') ?>" class="notif-filter-btn <?= $currentFilter === 'unread' ? 'active' : '' ?>">
        <i class="bi bi-envelope-exclamation"></i>
        <span>Belum Dibaca</span>
        <span class="filter-count"><?= $totalUnread ?></span>
    </a>
    <a href="<?= base_url('/notification?filter=read') ?>" class="notif-filter-btn <?= $currentFilter === 'read' ? 'active' : '' ?>">
        <i class="bi bi-envelope-check"></i>
        <span>Sudah Dibaca</span>
        <span class="filter-count"><?= $totalRead ?></span>
    </a>
</div>

<!-- Daftar Kartu Notifikasi -->
<div class="notif-list-container">
    <?php if (empty($notifications)) : ?>
        <div class="card border-0 shadow-sm rounded-4 text-center py-5 px-3">
            <div class="card-body">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle mb-3" style="width: 64px; height: 64px; background-color: #F1F5F9; color: #94A3B8; font-size: 28px;">
                    <i class="bi bi-bell-slash"></i>
                </div>
                <h5 class="fw-bold text-dark mb-1" style="font-size: 16px;">Tidak Ada Notifikasi</h5>
                <p class="text-secondary small mb-0" style="max-width: 420px; margin: 0 auto; line-height: 1.5;">
                    <?php if ($currentFilter === 'unread') : ?>
                        Hebat! Semua notifikasi telah Anda baca dan tindak lanjuti.
                    <?php elseif ($currentFilter === 'read') : ?>
                        Belum ada notifikasi yang ditandai sebagai sudah dibaca.
                    <?php else : ?>
                        Belum ada riwayat aktivitas atau tugas yang membutuhkan perhatian Anda saat ini.
                    <?php endif; ?>
                </p>
            </div>
        </div>
    <?php else : ?>
        <?php foreach ($notifications as $notif) : ?>
            <?php 
                $isUnread = ((int)$notif['is_read'] === 0);
                $meta = $notif['meta'];
            ?>
            <div class="notif-card <?= $isUnread ? 'unread' : '' ?>" id="notif-card-<?= esc($notif['id_notification']) ?>">
                <!-- Icon Activity -->
                <div class="notif-card-icon" style="background-color: <?= $meta['bg'] ?>; color: <?= $meta['color'] ?>;">
                    <i class="bi <?= $meta['icon'] ?>"></i>
                </div>

                <!-- Body Content -->
                <div class="notif-card-body">
                    <div class="notif-card-header">
                        <div class="d-flex align-items-center gap-2 flex-wrap">
                            <h5 class="notif-card-title"><?= esc($notif['title']) ?></h5>
                            <span class="badge rounded-pill" style="background-color: <?= $meta['bg'] ?>; color: <?= $meta['color'] ?>; font-size: 11px; font-weight: 600; padding: 3px 9px;">
                                <?= $meta['label'] ?>
                            </span>
                        </div>
                        <div>
                            <?php if ($isUnread) : ?>
                                <span class="badge bg-primary text-white badge-status-unread" style="font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 6px;">
                                    Belum Dibaca
                                </span>
                            <?php else : ?>
                                <span class="badge bg-light text-secondary border" style="font-size: 11px; font-weight: 500; padding: 4px 10px; border-radius: 6px;">
                                    Sudah Dibaca
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <p class="notif-card-desc"><?= esc($notif['message']) ?></p>

                    <!-- Tindakan Khusus JRA Retention (Jika Ada) -->
                    <?php if ($notif['action_type'] === 'retensi_arsip') : ?>
                        <div class="notif-jra-actions mb-3">
                            <strong class="d-block small text-dark mb-2"><i class="bi bi-clock-history me-1"></i> Tindakan Lanjutan Masa Retensi:</strong>
                            <div class="d-flex flex-wrap gap-2">
                                <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="perpanjang">
                                    <button type="submit" class="btn btn-sm btn-success px-3 py-1 fw-medium" style="font-size: 12px; border-radius: 6px;">
                                        <i class="bi bi-arrow-repeat me-1"></i> Perpanjang (1 Tahun)
                                    </button>
                                </form>
                                <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="inaktif">
                                    <button type="submit" class="btn btn-sm btn-warning px-3 py-1 fw-medium text-dark" style="font-size: 12px; border-radius: 6px;">
                                        <i class="bi bi-box-arrow-right me-1"></i> Pindahkan ke Inaktif
                                    </button>
                                </form>
                                <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menetapkan arsip ini sebagai Musnah? File fisik tetap dipertahankan.')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="musnahkan">
                                    <button type="submit" class="btn btn-sm btn-danger px-3 py-1 fw-medium" style="font-size: 12px; border-radius: 6px;">
                                        <i class="bi bi-trash me-1"></i> Musnahkan
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php elseif ($notif['action_type'] === 'retensi_inaktif_arsip') : ?>
                        <div class="notif-jra-actions mb-3">
                            <strong class="d-block small text-dark mb-2"><i class="bi bi-clock-history me-1"></i> Tindakan Akhir Retensi Inaktif:</strong>
                            <div class="d-flex flex-wrap gap-2">
                                <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" class="d-inline" onsubmit="return confirm('Yakin ingin menetapkan arsip ini sebagai Musnah? File fisik tetap dipertahankan.')">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="musnahkan">
                                    <button type="submit" class="btn btn-sm btn-danger px-3 py-1 fw-medium" style="font-size: 12px; border-radius: 6px;">
                                        <i class="bi bi-trash me-1"></i> Musnahkan
                                    </button>
                                </form>
                                <form action="<?= base_url('/notification/action/' . $notif['id_notification']) ?>" method="post" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="action" value="permanen">
                                    <button type="submit" class="btn btn-sm btn-primary px-3 py-1 fw-medium" style="font-size: 12px; border-radius: 6px;">
                                        <i class="bi bi-shield-lock me-1"></i> Jadikan Permanen
                                    </button>
                                </form>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Footer Meta & Actions -->
                    <div class="d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-2 pt-1 border-top" style="border-color: #F1F5F9 !important;">
                        <div class="notif-card-meta">
                            <span>
                                <i class="bi bi-calendar3 me-1"></i>
                                <?= !empty($notif['created_at']) ? date('d M Y, H:i', strtotime($notif['created_at'])) . ' WITA' : '-' ?>
                            </span>
                            <span>
                                <i class="bi bi-clock-history me-1"></i>
                                <?= esc($notif['time_ago']) ?>
                            </span>
                        </div>

                        <div class="notif-card-actions">
                            <?php if ($isUnread) : ?>
                                <button type="button" class="btn-notif-action btn-notif-light btn-mark-single-read" data-id="<?= esc($notif['id_notification']) ?>">
                                    <i class="bi bi-check2"></i> Tandai Dibaca
                                </button>
                            <?php endif; ?>

                            <?php if (!empty($notif['target_url']) && !in_array($notif['action_type'], ['retensi_arsip', 'retensi_inaktif_arsip'])) : ?>
                                <a href="<?= base_url('/notification/read-and-redirect/' . $notif['id_notification']) ?>" class="btn-notif-action btn-notif-primary">
                                    <span>Buka Tautan</span>
                                    <i class="bi bi-box-arrow-up-right" style="font-size: 11px;"></i>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>
