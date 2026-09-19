<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Activity Log - Audit Log SIAMA
<?= $this->endSection() ?>

<?= $this->section('content') ?>
<h2>Audit Log - Rekam Jejak Aktivitas Operasional</h2>
<p>Menampilkan riwayat seluruh aktivitas operasional pengguna (unggah arsip, penilaian, persetujuan, dsb.). Data bersifat <strong>Read-Only</strong>.</p>

<hr>

<!-- FORM FILTER -->
<form method="GET" action="<?= base_url('/audit-log/activity') ?>" style="margin-bottom: 20px; background: #f9f9f9; padding: 15px; border: 1px solid #ddd;">
    <strong>Filter Data:</strong>
    <br><br>
    <table cellpadding="5">
        <tr>
            <td><label for="start_date">Tanggal Mulai:</label></td>
            <td><input type="date" id="start_date" name="start_date" value="<?= esc($filters['start_date'] ?? '') ?>"></td>

            <td><label for="end_date">Tanggal Akhir:</label></td>
            <td><input type="date" id="end_date" name="end_date" value="<?= esc($filters['end_date'] ?? '') ?>"></td>

            <td><label for="module">Modul:</label></td>
            <td>
                <select id="module" name="module" style="padding: 3px;">
                    <option value="">-- Semua Modul --</option>
                    <?php if (!empty($modules)) : ?>
                        <?php foreach ($modules as $mod) : ?>
                            <option value="<?= esc($mod['module']) ?>" <?= ($filters['module'] ?? '') === $mod['module'] ? 'selected' : '' ?>>
                                <?= esc($mod['module']) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </td>

            <td><label for="search">Kata Kunci:</label></td>
            <td><input type="text" id="search" name="search" placeholder="Nama User, Deskripsi, Aksi..." value="<?= esc($filters['search'] ?? '') ?>" style="width: 220px;"></td>

            <td>
                <button type="submit" style="padding: 5px 12px;">Filter</button>
                <a href="<?= base_url('/audit-log/activity') ?>"><button type="button" style="padding: 5px 12px; margin-left: 5px;">Reset</button></a>
            </td>
        </tr>
    </table>
</form>

<!-- INFO TOTAL -->
<p>Total rekaman ditemukan: <strong><?= number_format($total) ?></strong> baris.</p>

<!-- TABEL ACTIVITY LOG -->
<div style="overflow-x: auto;">
    <table border="1" cellpadding="8" cellspacing="0" style="width: 100%; border-collapse: collapse; font-size: 13px;">
        <thead style="background: #1a5276; color: white;">
            <tr>
                <th style="width: 40px;">No</th>
                <th>Waktu</th>
                <th>Nama Pengguna</th>
                <th>OPD</th>
                <th>Modul</th>
                <th>Aksi</th>
                <th>Deskripsi</th>
                <th>Perubahan Data</th>
            </tr>
        </thead>
        <tbody>
            <?php if (!empty($logs)) : ?>
                <?php
                $page    = (int) ($_GET['page'] ?? 1);
                $perPage = 25;
                $no      = ($page - 1) * $perPage + 1;
                ?>
                <?php foreach ($logs as $row) : ?>
                    <?php
                    // Warna berdasarkan action
                    $actionColor = 'black';
                    $action = strtoupper($row['action'] ?? '');
                    if ($action === 'CREATE' || $action === 'create') $actionColor = '#1a8c4e';
                    elseif ($action === 'UPDATE' || $action === 'update') $actionColor = '#d68910';
                    elseif ($action === 'DELETE' || $action === 'delete') $actionColor = '#c0392b';
                    elseif ($action === 'VERIFY' || $action === 'verify') $actionColor = '#1a5276';
                    ?>
                    <tr>
                        <td style="text-align: center;"><?= $no++ ?></td>
                        <td style="white-space: nowrap;"><?= esc($row['created_at']) ?></td>
                        <td><?= esc($row['nama_user'] ?: '-') ?></td>
                        <td><?= esc($row['nama_opd'] ?: '-') ?></td>
                        <td style="text-align: center;">
                            <span style="background: #eaf0fb; padding: 2px 7px; border-radius: 4px; font-size: 11px; font-weight: bold;">
                                <?= esc($row['module']) ?>
                            </span>
                        </td>
                        <td style="text-align: center;">
                            <strong style="color: <?= $actionColor ?>;"><?= esc($row['action']) ?></strong>
                        </td>
                        <td><?= esc($row['description']) ?></td>
                        <td style="min-width: 200px;">
                            <?php if (!empty($row['old_data']) || !empty($row['new_data'])) : ?>
                                <details>
                                    <summary style="cursor: pointer; color: #555; font-size: 12px;">Lihat Perubahan Data</summary>
                                    <?php if (!empty($row['old_data'])) : ?>
                                        <p style="margin: 4px 0; font-size: 11px; font-weight: bold; color: #c0392b;">Data Lama (Before):</p>
                                        <pre style="background: #fdf2f2; border: 1px solid #e9bebe; padding: 6px; font-size: 10px; overflow-x: auto; max-width: 350px; white-space: pre-wrap;"><?= esc(json_encode(json_decode($row['old_data'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                    <?php endif; ?>
                                    <?php if (!empty($row['new_data'])) : ?>
                                        <p style="margin: 4px 0; font-size: 11px; font-weight: bold; color: #1a8c4e;">Data Baru (After):</p>
                                        <pre style="background: #f2fdf5; border: 1px solid #bee9c9; padding: 6px; font-size: 10px; overflow-x: auto; max-width: 350px; white-space: pre-wrap;"><?= esc(json_encode(json_decode($row['new_data'], true), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) ?></pre>
                                    <?php endif; ?>
                                </details>
                            <?php else : ?>
                                <span style="color: #aaa; font-size: 11px;">-</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else : ?>
                <tr>
                    <td colspan="8" style="text-align: center; padding: 20px;">Tidak ada data aktivitas yang sesuai dengan filter.</td>
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
