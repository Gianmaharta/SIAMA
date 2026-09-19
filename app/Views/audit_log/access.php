<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Access Log - Audit Log SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Audit Log - Rekam Jejak Akses Sistem</h2>
<p>Menampilkan riwayat seluruh aktivitas masuk (login), keluar (logout), dan percobaan akses yang gagal. Data bersifat <strong>Read-Only</strong>.</p>

<hr>

<!-- FORM FILTER -->
<form method="GET" action="<?= base_url('/audit-log/access') ?>" style="margin-bottom: 20px; background: #f9f9f9; padding: 15px; border: 1px solid #ddd;">
    <strong>Filter Data:</strong>
    <br><br>
    <table cellpadding="5">
        <tr>
            <td><label for="start_date">Tanggal Mulai:</label></td>
            <td><input type="date" id="start_date" name="start_date" value="<?= esc($filters['start_date'] ?? '') ?>"></td>

            <td><label for="end_date">Tanggal Akhir:</label></td>
            <td><input type="date" id="end_date" name="end_date" value="<?= esc($filters['end_date'] ?? '') ?>"></td>

            <td><label for="search">Kata Kunci:</label></td>
            <td><input type="text" id="search" name="search" placeholder="Nama User, IP, Event..." value="<?= esc($filters['search'] ?? '') ?>" style="width: 220px;"></td>

            <td>
                <button type="submit" style="padding: 5px 12px;">Filter</button>
                <a href="<?= base_url('/audit-log/access') ?>"><button type="button" style="padding: 5px 12px; margin-left: 5px;">Reset</button></a>
            </td>
        </tr>
    </table>
</form>

<!-- INFO TOTAL -->
<p>Total rekaman ditemukan: <strong><?= number_format($total) ?></strong> baris.</p>

<!-- TABEL ACCESS LOG -->
<div style="overflow-x: auto;">
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead style="background: #2c3e50; color: white;">
            <tr>
                <th style="width: 40px;">No</th>
                <th>Waktu</th>
                <th>Nama Pengguna</th>
                <th>OPD</th>
                <th>Event</th>
                <th>IP Address</th>
                <th>User Agent (Browser/OS)</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)) : ?>
                <?php
                $page   = (int) ($_GET['page'] ?? 1);
                $perPage = 25;
                $no     = ($page - 1) * $perPage + 1;
                ?>
                <?php foreach ($logs as $row) : ?>
                    <?php
                    // Warna event
                    $eventColor = 'black';
                    if ($row['event'] === 'LOGIN_SUCCESS') $eventColor = 'green';
                    elseif ($row['event'] === 'LOGOUT') $eventColor = '#666';
                    elseif (str_contains($row['event'], 'FAIL') || str_contains($row['event'], 'FAILED')) $eventColor = 'red';
                    ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td style="white-space: nowrap;"><?= esc($row['created_at']) ?></td>
                        <td><?= esc($row['nama_user'] ?: '- (Tidak Dikenal)') ?></td>
                        <td><?= esc($row['nama_opd'] ?: '-') ?></td>
                        <td style="text-align: center;">
                            <strong style="color: <?= $eventColor ?>;"><?= esc($row['event']) ?></strong>
                        </td>
                        <td><code><?= esc($row['ip_address']) ?></code></td>
                        <td style="font-size: 11px; max-width: 300px; word-break: break-all;">
                            <?= esc($row['user_agent']) ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">Tidak ada data rekaman akses yang sesuai dengan filter.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<!-- PAGINASI -->
<?php if (!empty($pager)) : ?>
    <div style="margin-top: 15px;">
        <?= $pager ?>
    </div>
<?php endif; ?>

<?= $this->endSection() ?>
