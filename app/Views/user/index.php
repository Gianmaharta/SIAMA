<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Akun - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/user-master.css?v=' . (file_exists(FCPATH . 'css/user-master.css') ? filemtime(FCPATH . 'css/user-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

<!-- Header Halaman (Dibedakan sesuai Role Super Admin vs Admin Pemkab vs Admin OPD) -->
<?php $loginRoleHeader = session()->get('nama_role'); ?>
<div class="user-page-header d-flex flex-wrap align-items-center justify-content-between gap-3">
    <div>
        <?php if ($loginRoleHeader === 'Super_Admin') : ?>
            <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill mb-2" style="background-color: #F3E8FF; color: #7C3AED; font-size: 11.5px; font-weight: 600;">
                <i class="bi bi-shield-fill-check"></i>
                <span>Super Admin &bull; Akses Hierarki Tertinggi &amp; Switch User</span>
            </div>
            <h1 class="user-page-title">Manajemen Akun &amp; Impersonation</h1>
            <p class="user-page-subtitle">Kendali penuh seluruh akun sistem lintas level (termasuk Admin Pemkab) beserta fitur Masuk Sebagai (Switch User).</p>
        <?php elseif ($loginRoleHeader === 'Admin_Pemkab') : ?>
            <div class="d-inline-flex align-items-center gap-1.5 px-2.5 py-1 rounded-pill mb-2" style="background-color: #EFF6FF; color: #1E60EC; font-size: 11.5px; font-weight: 600;">
                <i class="bi bi-building-gear"></i>
                <span>Admin Pemkab &bull; Manajemen Akun Perangkat Daerah</span>
            </div>
            <h1 class="user-page-title">Manajemen Akun Perangkat Daerah</h1>
            <p class="user-page-subtitle">Kelola akun pengguna tingkat Kabupaten dan OPD (Admin OPD, Pimpinan, Kepala Bidang, dan Arsiparis).</p>
        <?php else : ?>
            <h1 class="user-page-title">Manajemen Akun Internal OPD</h1>
            <p class="user-page-subtitle">Kelola akun Kepala Bidang, Pimpinan, dan Arsiparis di lingkungan Perangkat Daerah Anda.</p>
        <?php endif; ?>
    </div>
    <?php if ($loginRoleHeader === 'Super_Admin') : ?>
        <a href="<?= base_url('/settings') ?>" class="btn btn-outline-primary btn-sm px-3 py-2 rounded-3 fw-medium d-inline-flex align-items-center gap-2">
            <i class="bi bi-key-fill"></i>
            <span>Atur Password Default (<?= esc($defaultPassword ?? 'Admin123!') ?>)</span>
        </a>
    <?php endif; ?>
</div>

<!-- Tiga Kartu Statistik Pengguna -->
<div class="row g-3 g-xl-4 mb-4">
    <!-- Kartu 1: Total Semua Pengguna -->
    <div class="col-12 col-md-4">
        <div class="user-stat-card">
            <div class="user-stat-icon-wrapper bg-icon-blue">
                <i class="bi bi-person-fill"></i>
            </div>
            <div class="user-stat-body">
                <span class="user-stat-title">total Semua pengguna</span>
                <div class="user-stat-number-wrap">
                    <span class="user-stat-number" id="statTotalUsers"><?= number_format($totalUsers ?? 0, 0, ',', '.') ?></span>
                    <span class="user-stat-unit">Pengguna</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 2: Total Pengguna Aktif -->
    <div class="col-12 col-md-4">
        <div class="user-stat-card">
            <div class="user-stat-icon-wrapper bg-icon-green">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="user-stat-body">
                <span class="user-stat-title">Total Pengguna Aktif</span>
                <div class="user-stat-number-wrap">
                    <span class="user-stat-number" id="statActiveUsers"><?= number_format($activeUsers ?? 0, 0, ',', '.') ?></span>
                    <span class="user-stat-unit">Pengguna</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Kartu 3: Total Pengguna tidak Aktif -->
    <div class="col-12 col-md-4">
        <div class="user-stat-card">
            <div class="user-stat-icon-wrapper bg-icon-red">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="user-stat-body">
                <span class="user-stat-title">Total Pengguna tidak Aktif</span>
                <div class="user-stat-number-wrap">
                    <span class="user-stat-number" id="statInactiveUsers"><?= number_format($inactiveUsers ?? 0, 0, ',', '.') ?></span>
                    <span class="user-stat-unit">Pengguna</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Panel Tabel Manajemen Akun -->
<div class="user-table-card">
    <!-- Toolbar Tabel -->
    <div class="user-table-toolbar">
        <!-- Kolom Pencarian -->
        <div class="user-search-wrap">
            <i class="bi bi-search user-search-icon"></i>
            <input 
                type="text" 
                id="userSearchInput" 
                class="user-search-input" 
                placeholder="Cari nama OPD, Nama Bidang..."
                autocomplete="off"
            >
            <button type="button" class="user-clear-search d-none" id="userClearSearchBtn" title="Bersihkan pencarian">
                <i class="bi bi-x"></i>
            </button>
        </div>

        <!-- Filter OPD, Pilihan Tampilkan & Tombol Tambah Pengguna -->
        <div class="user-toolbar-actions">
            <!-- Pilihan Jumlah Data Per Halaman -->
            <div class="user-filter-item">
                <label for="userPerPageSelect" class="user-filter-label">Tampilkan</label>
                <select id="userPerPageSelect" class="user-select-control user-select-per-page" aria-label="Jumlah Data Per Halaman">
                    <option value="10" selected>10</option>
                    <option value="15">15</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                    <option value="all">Semua</option>
                </select>
            </div>

            <!-- Filter OPD -->
            <select id="userOpdFilter" class="user-select-control" aria-label="Filter Berdasarkan OPD">
                <option value="all" selected>Semua OPD</option>
                <?php if (!empty($opds)) : ?>
                    <?php foreach ($opds as $opd) : ?>
                        <option value="<?= esc($opd['id_opd']) ?>"><?= esc($opd['nama_opd']) ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>

            <!-- Tombol Tambah Pengguna -->
            <button 
                type="button" 
                class="user-btn-add btn-toolbar-primary" 
                data-bs-toggle="modal" 
                data-bs-target="#modalTambahUser"
            >
                <i class="bi bi-plus-lg"></i>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>

    <!-- Tabel Data Akun Pengguna -->
    <div class="table-responsive user-table-responsive">
        <table class="table user-table align-middle" id="userTable">
            <thead>
                <tr>
                    <th scope="col" class="col-no text-center">No</th>
                    <th scope="col" class="col-nama text-center">Nama</th>
                    <th scope="col" class="col-email text-center">Email</th>
                    <th scope="col" class="col-role text-center">Role</th>
                    <th scope="col" class="col-opd text-center">OPD</th>
                    <th scope="col" class="col-status text-center">Status</th>
                    <th scope="col" class="col-aksi text-center">Aksi</th>
                </tr>
            </thead>
            <tbody id="userTableBody">
                <!-- Baris tabel dirender secara dinamis oleh JavaScript -->
            </tbody>
        </table>
    </div>

    <!-- Empty State (Ketika data tidak ditemukan) -->
    <div class="user-empty-state d-none" id="userEmptyState">
        <div class="empty-icon-wrap">
            <i class="bi bi-search"></i>
        </div>
        <h5 class="empty-title">Data Pengguna Tidak Ditemukan</h5>
        <p class="empty-desc">Tidak ada akun pengguna yang cocok dengan kriteria pencarian atau filter OPD Anda.</p>
        <button type="button" class="btn btn-sm btn-outline-secondary rounded-pill px-3" id="btnResetFilter">
            <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Filter & Pencarian
        </button>
    </div>

    <!-- Informasi Data dan Pagination -->
    <div class="user-table-footer">
        <div class="user-pagination-info" id="userPaginationInfo">
            Menampilkan 0 dari 0 data
        </div>
        <div class="user-pagination-controls" id="userPaginationControls">
            <!-- Tombol navigasi pagination -->
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: TAMBAH PENGGUNA                                          -->
<!-- ============================================================== -->
<div class="modal fade" id="modalTambahUser" tabindex="-1" aria-labelledby="modalTambahUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable user-modal-dialog">
        <div class="modal-content user-modal">
            <div class="modal-header user-modal-header">
                <h5 class="modal-title user-modal-title" id="modalTambahUserLabel">
                    <i class="bi bi-person-plus me-2 text-primary"></i>Tambah Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="<?= base_url('/users/store') ?>" method="POST" id="formTambahUser">
                <?= csrf_field() ?>
                <div class="modal-body user-modal-body">
                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="create_nama" class="user-form-label">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama" 
                            id="create_nama" 
                            class="user-form-control" 
                            placeholder="Contoh: Administrator OPD" 
                            required 
                            autocomplete="off"
                        >
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="create_email" class="user-form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="create_email" 
                            class="user-form-control" 
                            placeholder="Contoh: admin.opd@bulelengkab.go.id" 
                            required 
                            autocomplete="off"
                        >
                    </div>

                    <!-- Role Pengguna -->
                    <div class="mb-3">
                        <label for="create_id_role" class="user-form-label">
                            Role Pengguna <span class="text-danger">*</span>
                        </label>
                        <select name="id_role" id="create_id_role" class="user-form-control user-form-select" required>
                            <option value="" data-role-name="">-- Pilih Role Pengguna --</option>
                            <?php if (!empty($roles)) : ?>
                                <?php foreach ($roles as $r) : ?>
                                    <option value="<?= esc($r['id_role']) ?>" data-role-name="<?= esc($r['nama_role']) ?>"><?= esc(str_replace('_', ' ', $r['nama_role'])) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Organisasi Perangkat Daerah (OPD) -->
                    <div class="mb-3" id="wrapCreateOpd">
                        <label for="create_id_opd" class="user-form-label">
                            Organisasi Perangkat Daerah (OPD)
                        </label>
                        <?php if (!empty($id_opd_locked)) : ?>
                            <input 
                                type="text" 
                                class="user-form-control bg-light text-muted" 
                                value="<?php 
                                    foreach ($opds as $o) {
                                        if ($o['id_opd'] == $id_opd_locked) {
                                            echo esc($o['nama_opd']);
                                            break;
                                        }
                                    }
                                ?>" 
                                readonly 
                                disabled
                                style="cursor: not-allowed;"
                            >
                            <input type="hidden" name="id_opd" value="<?= esc($id_opd_locked) ?>">
                        <?php else : ?>
                            <select name="id_opd" id="create_id_opd" class="user-form-control user-form-select">
                                <option value="">-- Pilih OPD (Opsional untuk Admin Pemkab) --</option>
                                <?php if (!empty($opds)) : ?>
                                    <?php foreach ($opds as $opd) : ?>
                                        <option value="<?= esc($opd['id_opd']) ?>"><?= esc($opd['nama_opd']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <!-- Bidang (Opsional) -->
                    <div class="mb-3" id="wrapCreateBidang">
                        <label for="create_id_bidang" class="user-form-label">
                            Bidang (Opsional)
                        </label>
                        <select name="id_bidang" id="create_id_bidang" class="user-form-control user-form-select">
                            <option value="">-- Pilih Bidang (Opsional) --</option>
                            <?php if (!empty($bidangs)) : ?>
                                <?php foreach ($bidangs as $b) : ?>
                                    <option value="<?= esc($b['id_bidang']) ?>" data-opd="<?= esc($b['id_opd']) ?>">
                                        <?= esc($b['nama_bidang']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Opsi Mekanisme Password Akun Baru -->
                    <?php
                    $defaultPwVal = $defaultPassword ?? 'Admin123!';
                    $pwModeDefault = $passwordMode ?? 'default';
                    ?>
                    <div class="mb-3">
                        <label class="user-form-label">Mekanisme Password Awal <span class="text-danger">*</span></label>
                        <div class="d-flex flex-column gap-2 p-2.5 px-3 bg-light rounded-3 border">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="password_option" id="pw_opt_default" value="default" <?= $pwModeDefault !== 'random' ? 'checked' : '' ?>>
                                <label class="form-check-label small text-dark" for="pw_opt_default">
                                    Gunakan Password Default Pengaturan Website (<code class="fw-bold text-primary"><?= esc($defaultPwVal) ?></code>)
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="password_option" id="pw_opt_random" value="random" <?= $pwModeDefault === 'random' ? 'checked' : '' ?>>
                                <label class="form-check-label small text-dark" for="pw_opt_random">
                                    Generate Password Random Otomatis <span class="text-muted">(Ditampilkan & bisa di-copy setelah disimpan)</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer user-modal-footer">
                    <button type="button" class="user-btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="user-btn-add px-3">
                        <i class="bi bi-check2 me-1"></i> Simpan Pengguna
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: EDIT PENGGUNA                                            -->
<!-- ============================================================== -->
<div class="modal fade" id="modalEditUser" tabindex="-1" aria-labelledby="modalEditUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable user-modal-dialog">
        <div class="modal-content user-modal">
            <div class="modal-header user-modal-header">
                <h5 class="modal-title user-modal-title" id="modalEditUserLabel">
                    <i class="bi bi-pencil-square me-2 text-primary"></i>Edit Pengguna
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Tutup"></button>
            </div>
            <form action="" method="POST" id="formEditUser">
                <?= csrf_field() ?>
                <input type="hidden" name="id_user" id="edit_id_user">
                <div class="modal-body user-modal-body">
                    <!-- Nama Lengkap -->
                    <div class="mb-3">
                        <label for="edit_nama" class="user-form-label">
                            Nama Lengkap <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="text" 
                            name="nama" 
                            id="edit_nama" 
                            class="user-form-control" 
                            placeholder="Nama Lengkap" 
                            required 
                            autocomplete="off"
                        >
                    </div>

                    <!-- Email -->
                    <div class="mb-3">
                        <label for="edit_email" class="user-form-label">
                            Email <span class="text-danger">*</span>
                        </label>
                        <input 
                            type="email" 
                            name="email" 
                            id="edit_email" 
                            class="user-form-control" 
                            placeholder="Email pengguna" 
                            required 
                            autocomplete="off"
                        >
                    </div>

                    <!-- Role Pengguna -->
                    <div class="mb-3">
                        <label for="edit_id_role" class="user-form-label">
                            Role Pengguna <span class="text-danger">*</span>
                        </label>
                        <select name="id_role" id="edit_id_role" class="user-form-control user-form-select" required>
                            <option value="" data-role-name="">-- Pilih Role --</option>
                            <?php if (!empty($roles)) : ?>
                                <?php foreach ($roles as $r) : ?>
                                    <option value="<?= esc($r['id_role']) ?>" data-role-name="<?= esc($r['nama_role']) ?>"><?= esc(str_replace('_', ' ', $r['nama_role'])) ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- OPD -->
                    <div class="mb-3" id="wrapEditOpd">
                        <label for="edit_id_opd" class="user-form-label">
                            Organisasi Perangkat Daerah (OPD)
                        </label>
                        <?php if (!empty($id_opd_locked)) : ?>
                            <input 
                                type="text" 
                                class="user-form-control bg-light text-muted" 
                                value="<?php 
                                    foreach ($opds as $o) {
                                        if ($o['id_opd'] == $id_opd_locked) {
                                            echo esc($o['nama_opd']);
                                            break;
                                        }
                                    }
                                ?>" 
                                readonly 
                                disabled
                                style="cursor: not-allowed;"
                            >
                            <input type="hidden" name="id_opd" id="edit_id_opd" value="<?= esc($id_opd_locked) ?>">
                        <?php else : ?>
                            <select name="id_opd" id="edit_id_opd" class="user-form-control user-form-select">
                                <option value="">-- Tidak Terikat OPD (Admin Pemkab) --</option>
                                <?php if (!empty($opds)) : ?>
                                    <?php foreach ($opds as $opd) : ?>
                                        <option value="<?= esc($opd['id_opd']) ?>"><?= esc($opd['nama_opd']) ?></option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        <?php endif; ?>
                    </div>

                    <!-- Bidang -->
                    <div class="mb-3" id="wrapEditBidang">
                        <label for="edit_id_bidang" class="user-form-label">
                            Bidang (Opsional)
                        </label>
                        <select name="id_bidang" id="edit_id_bidang" class="user-form-control user-form-select">
                            <option value="">-- Pilih Bidang --</option>
                            <?php if (!empty($bidangs)) : ?>
                                <?php foreach ($bidangs as $b) : ?>
                                    <option value="<?= esc($b['id_bidang']) ?>" data-opd="<?= esc($b['id_opd']) ?>">
                                        <?= esc($b['nama_bidang']) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Status Keaktifan -->
                    <div class="mb-2">
                        <label for="edit_is_active" class="user-form-label">Status Keaktifan</label>
                        <select name="is_active" id="edit_is_active" class="user-form-control user-form-select">
                            <option value="1">Aktif</option>
                            <option value="0">Tidak Aktif</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer user-modal-footer">
                    <button type="button" class="user-btn-cancel" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="user-btn-add px-3">
                        <i class="bi bi-save me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
$currentRoleSession = session()->get('nama_role');
// Fitur "Masuk Sebagai" (Switch User) KHUSUS untuk Super_Admin asli (bukan Admin_Pemkab atau Admin_OPD)
$isCanSwitchRole = in_array($currentRoleSession, ['Super_Admin', 'Super Admin'], true) && !session()->has('original_admin_id');
?>
<?php if ($isCanSwitchRole) : ?>
<!-- ============================================================== -->
<!-- MODAL: KONFIRMASI MASUK SEBAGAI (SWITCH USER)                  -->
<!-- ============================================================== -->
<div class="modal fade" id="modalSwitchUser" tabindex="-1" aria-labelledby="modalSwitchUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 420px;">
        <div class="modal-content border-0 position-relative p-2" style="border-radius: 16px; box-shadow: 0 16px 36px rgba(0, 0, 0, 0.12);">
            <div class="modal-body text-center pt-2 pb-2 px-2 position-relative">
                <!-- Close Button -->
                <button type="button" class="btn-close position-absolute top-0 end-0 m-2 shadow-none" id="btnCloseSwitchUser" data-bs-dismiss="modal" aria-label="Tutup" style="z-index: 1055; cursor: pointer;"></button>

                <!-- Top Purple Icon Badge -->
                <div class="mb-2 mx-auto d-flex align-items-center justify-content-center" style="width: 46px; height: 46px; border-radius: 50%; background-color: #F3E8FF; color: #7C3AED; font-size: 22px;">
                    <i class="bi bi-box-arrow-in-right"></i>
                </div>

                <!-- Judul & Subjudul -->
                <h5 class="fw-bold text-dark mb-1" id="modalSwitchUserLabel" style="font-size: 18px; letter-spacing: -0.2px;">Masuk Sebagai Pengguna?</h5>
                <p class="text-secondary small mb-3 px-1" style="font-size: 12px; line-height: 1.4; color: #64748B;">
                    Anda akan dialihkan (<em>impersonate</em>) ke akun pengguna ini tanpa perlu memasukkan password.
                </p>

                <!-- Information Card -->
                <div class="p-2 px-3 mb-3 rounded-3 text-start border" style="background-color: #F8FAFC; border-color: #E2E8F0 !important; border-radius: 12px !important;">
                    <!-- Item 1: Nama Pengguna -->
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 32px; height: 32px; background-color: #EFF6FF; color: #1E60EC; font-size: 16px;">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="text-secondary fw-medium" style="font-size: 10.5px; color: #64748B; line-height: 1.2;">Nama Pengguna</div>
                            <div class="fw-bold text-dark text-truncate" style="font-size: 13.5px; line-height: 1.25;" id="switchUserName">-</div>
                            <div id="switchUserNameRole" class="d-none"></div>
                        </div>
                    </div>

                    <div class="border-top" style="margin: 6px 0; border-color: #E2E8F0 !important;"></div>

                    <!-- Item 2: Email -->
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 32px; height: 32px; border-radius: 8px; background-color: #EFF6FF; color: #1E60EC; font-size: 15px;">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="text-secondary fw-medium" style="font-size: 10.5px; color: #64748B; line-height: 1.2;">Email</div>
                            <div class="text-dark fw-medium text-truncate" style="font-size: 12.5px; line-height: 1.25;" id="switchUserEmail">-</div>
                        </div>
                    </div>

                    <div class="border-top" style="margin: 6px 0; border-color: #E2E8F0 !important;"></div>

                    <!-- Item 3: Role -->
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center me-2 flex-shrink-0" style="width: 32px; height: 32px; border-radius: 8px; background-color: #ECFDF5; color: #10B981; font-size: 15px;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-secondary fw-medium" style="font-size: 10.5px; color: #64748B; line-height: 1.2;">Role</div>
                            <div>
                                <span class="badge" id="switchUserRole" style="background-color: #EBF2FE; color: #1E60EC; font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 5px;">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-top" style="margin: 6px 0; border-color: #E2E8F0 !important;"></div>

                    <!-- Item 4: OPD -->
                    <div class="d-flex align-items-start">
                        <div class="d-flex align-items-center justify-content-center me-2 flex-shrink-0 mt-0.5" style="width: 32px; height: 32px; border-radius: 8px; background-color: #FEF3C7; color: #D97706; font-size: 15px;">
                            <i class="bi bi-bank"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-secondary fw-medium" style="font-size: 10.5px; color: #64748B; line-height: 1.2;">OPD</div>
                            <div class="text-dark fw-medium" style="font-size: 12px; line-height: 1.35;" id="switchUserOpd">-</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Batal & Masuk Sekarang -->
                <div class="d-flex align-items-center gap-2 pt-1">
                    <button type="button" class="btn btn-light border py-1 px-3 fw-semibold text-dark flex-fill" data-bs-dismiss="modal" style="border-radius: 8px; height: 38px; font-size: 13.5px;">
                        Batal
                    </button>
                    <a href="#" id="btnConfirmSwitchUser" class="btn btn-primary py-1 px-3 fw-semibold text-white d-flex align-items-center justify-content-center gap-2 flex-fill" style="background-color: #1E60EC; border-color: #1E60EC; border-radius: 8px; height: 38px; font-size: 13.5px;">
                        <i class="bi bi-box-arrow-in-right fs-6"></i>
                        <span>Masuk Sekarang</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari Controller CI4
    const rawData = <?= json_encode($users ?? []) ?>;
    const allBidangs = <?= json_encode($bidangs ?? []) ?>;
    const currentUserId = "<?= esc(session()->get('user_id') ?? '') ?>";
    const isCanSwitch = <?= $isCanSwitchRole ? 'true' : 'false' ?>;
    const baseEditUrl = "<?= base_url('/users/update') ?>";
    const baseDeleteUrl = "<?= base_url('/users/delete') ?>";
    const baseSwitchUrl = "<?= base_url('/users/switch') ?>";

    // Konfigurasi & State
    let currentPage = 1;
    let filteredData = [...rawData];

    // Elemen DOM
    const perPageSelect = document.getElementById('userPerPageSelect');
    const searchInput = document.getElementById('userSearchInput');
    const clearSearchBtn = document.getElementById('userClearSearchBtn');
    const opdFilter = document.getElementById('userOpdFilter');
    const tableBody = document.getElementById('userTableBody');
    const emptyState = document.getElementById('userEmptyState');
    const paginationInfo = document.getElementById('userPaginationInfo');
    const paginationControls = document.getElementById('userPaginationControls');
    const btnResetFilter = document.getElementById('btnResetFilter');

    // Elemen Modal Switch User
    const switchUserName = document.getElementById('switchUserName');
    const switchUserNameRole = document.getElementById('switchUserNameRole');
    const switchUserEmail = document.getElementById('switchUserEmail');
    const switchUserRole = document.getElementById('switchUserRole');
    const switchUserOpd = document.getElementById('switchUserOpd');
    const btnConfirmSwitchUser = document.getElementById('btnConfirmSwitchUser');

    // Elemen Modal Edit
    const formEditUser = document.getElementById('formEditUser');
    const editIdInput = document.getElementById('edit_id_user');
    const editNamaInput = document.getElementById('edit_nama');
    const editEmailInput = document.getElementById('edit_email');
    const editRoleInput = document.getElementById('edit_id_role');
    const editOpdInput = document.getElementById('edit_id_opd');
    const editBidangInput = document.getElementById('edit_id_bidang');
    const editActiveInput = document.getElementById('edit_is_active');

    /**
     * Escape string HTML untuk keamanan
     */
    function escapeHtml(text) {
        if (!text) return '';
        const map = {
            '&': '&amp;',
            '<': '&lt;',
            '>': '&gt;',
            '"': '&quot;',
            "'": '&#039;'
        };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }

    let searchTimeout = null;
    let totalFiltered = 0;

    /**
     * Ambil data pengguna dari Server-Side DataTables (/users/datatable)
     */
    async function fetchUsersPage() {
        const query = searchInput ? searchInput.value.trim() : '';
        const selectedOpd = opdFilter ? opdFilter.value : 'all';
        const perPageVal = perPageSelect ? perPageSelect.value : '10';
        const effectivePerPage = perPageVal === 'all' ? 100 : (parseInt(perPageVal, 10) || 10);
        const startIdx = (currentPage - 1) * effectivePerPage;

        if (clearSearchBtn) {
            clearSearchBtn.classList.toggle('d-none', query.length === 0);
        }

        const params = new URLSearchParams({
            start: String(startIdx),
            length: String(effectivePerPage),
            search: query
        });
        if (selectedOpd && selectedOpd !== 'all') {
            params.set('id_opd', selectedOpd);
        }

        try {
            const res = await fetch('<?= base_url('users/datatable') ?>?' + params.toString(), {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            if (!res.ok) return;
            const json = await res.json();
            totalFiltered = Number(json.recordsFiltered ?? 0);
            renderRows(json.data || [], startIdx, effectivePerPage, perPageVal);
        } catch (err) {
            console.error('Gagal memuat data pengguna:', err);
        }
    }

    function applyFilters() {
        currentPage = 1;
        fetchUsersPage();
    }

    function renderTable() {
        fetchUsersPage();
    }

    /**
     * Render Baris Tabel dan Navigasi Pagination
     */
    function renderRows(pageItems, startIndex, effectivePerPage, perPageVal) {
        const totalItems = totalFiltered;
        const totalPages = Math.ceil(totalItems / effectivePerPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        // Tampilkan / Sembunyikan Empty State
        if (totalItems === 0 || pageItems.length === 0) {
            tableBody.innerHTML = '';
            emptyState.classList.remove('d-none');
            paginationInfo.textContent = 'Menampilkan 0 data';
            paginationControls.innerHTML = '';
            return;
        } else {
            emptyState.classList.add('d-none');
        }

        const endIndex = Math.min(startIndex + pageItems.length, totalItems);

        // Render Baris Data
        let htmlRows = '';
        pageItems.forEach((row, idx) => {
            const rowNumber = startIndex + idx + 1;
            const safeId = escapeHtml(row.id_user);
            const safeNama = escapeHtml(row.nama);
            const safeEmail = escapeHtml(row.email);
            const safeRole = escapeHtml(row.nama_role || '-');
            const safeRoleId = escapeHtml(row.id_role || '');
            const safeOpd = escapeHtml(row.nama_opd || 'Semua OPD (Pemkab)');
            const opdId = escapeHtml(row.id_opd || '');
            const bidangId = escapeHtml(row.id_bidang || '');
            const isActive = parseInt(row.is_active) === 1;
            const isSelf = (row.id_user === currentUserId);

            // Badge Role dengan warna khusus sesuai peran
            let roleBadgeClass = 'role-badge-default';
            const cleanRole = (row.nama_role || '').trim().toLowerCase().replace(/\s+/g, '_');
            if (cleanRole === 'admin_pemkab') {
                roleBadgeClass = 'role-badge-admin-pemkab';
            } else if (cleanRole === 'admin_opd') {
                roleBadgeClass = 'role-badge-admin-opd';
            } else if (cleanRole === 'arsiparis') {
                roleBadgeClass = 'role-badge-arsiparis';
            } else if (cleanRole === 'pimpinan') {
                roleBadgeClass = 'role-badge-pimpinan';
            } else if (cleanRole === 'kepala_bidang' || cleanRole === 'kabid') {
                roleBadgeClass = 'role-badge-kepala-bidang';
            }

            const roleBadge = `<span class="user-role-badge ${roleBadgeClass}">${safeRole}</span>`;

            // Cek perlindungan akun (Diri sendiri atau Super Admin)
            const isSuperAdminTarget = cleanRole === 'super_admin';
            const isProtected = isSelf || isSuperAdminTarget;

            // Status Indicator + Interactive Toggle Switch (Catatan #8)
            const toggleDisabledAttr = isProtected ? 'disabled title="Status akun ini tidak dapat dinonaktifkan"' : `title="Klik untuk ${isActive ? 'menonaktifkan' : 'mengaktifkan'} akun ${safeNama}"`;
            const statusHtml = `
                <div class="d-inline-flex align-items-center justify-content-center gap-2">
                    <div class="form-check form-switch m-0 d-flex align-items-center">
                        <input 
                            class="form-check-input user-status-toggle" 
                            type="checkbox" 
                            role="switch"
                            style="cursor: ${isProtected ? 'not-allowed' : 'pointer'}; width: 36px; height: 19px;"
                            data-id="${safeId}"
                            data-nama="${safeNama}"
                            ${isActive ? 'checked' : ''}
                            ${toggleDisabledAttr}
                        >
                    </div>
                    <span class="user-status-badge ${isActive ? 'status-aktif' : 'status-nonaktif'}" id="statusBadge_${safeId}">
                        <span class="status-dot ${isActive ? 'dot-aktif' : 'dot-nonaktif'}"></span>
                        <span class="status-label-text">${isActive ? 'Aktif' : 'Nonaktif'}</span>
                    </span>
                </div>
            `;

            // Aksi Buttons: Ikon Saja dengan Tooltip (Catatan #7 & #1)
            let actionHtml = `<div class="user-action-group justify-content-center">`;

            // 1. Tombol Switch User / Masuk Sebagai (Khusus Super_Admin)
            if (isCanSwitch) {
                if (isSelf) {
                    actionHtml += `
                        <button 
                            type="button" 
                            class="user-action-btn btn-action-switch current-user px-2" 
                            disabled
                            aria-disabled="true"
                            title="Akun Super Admin yang sedang aktif"
                            data-bs-toggle="tooltip"
                        >
                            <i class="bi bi-person-check-fill"></i>
                        </button>
                    `;
                } else {
                    actionHtml += `
                        <button 
                            type="button" 
                            class="user-action-btn btn-action-switch btn-switch-user px-2" 
                            data-bs-toggle="tooltip"
                            data-id="${safeId}"
                            data-nama="${safeNama}"
                            data-email="${safeEmail}"
                            data-role="${safeRole}"
                            data-opd="${safeOpd}"
                            title="Masuk sebagai ${safeNama} (${safeRole})"
                        >
                            <i class="bi bi-box-arrow-in-right"></i>
                        </button>
                    `;
                }
            }

            // 2. Tombol Reset Password (Ikon Kunci dengan Tooltip)
            actionHtml += `
                <button 
                    type="button" 
                    class="user-action-btn btn-action-edit btn-reset-pw-user px-2" 
                    data-bs-toggle="tooltip"
                    data-id="${safeId}"
                    data-nama="${safeNama}"
                    data-email="${safeEmail}"
                    title="Reset Password (${safeNama})"
                    style="color: #D97706; border-color: #FDE68A; background-color: #FFFBEB;"
                >
                    <i class="bi bi-key-fill"></i>
                </button>
            `;

            // 3. Tombol Edit Pengguna (Ikon Pensil dengan Tooltip)
            actionHtml += `
                <button 
                    type="button" 
                    class="user-action-btn btn-action-edit btn-edit-user px-2" 
                    data-bs-toggle="tooltip"
                    data-id="${safeId}"
                    data-nama="${safeNama}"
                    data-email="${safeEmail}"
                    data-role-id="${safeRoleId}"
                    data-opd-id="${opdId}"
                    data-bidang-id="${bidangId}"
                    data-active="${isActive ? '1' : '0'}"
                    title="Edit Akun (${safeNama})"
                >
                    <i class="bi bi-pencil-square"></i>
                </button>
            `;

            // 4. Tombol Hapus Pengguna (Ikon Trash dengan Tooltip)
            if (isProtected) {
                actionHtml += `
                    <button 
                        type="button" 
                        class="user-action-btn btn-action-delete disabled px-2" 
                        disabled
                        aria-disabled="true"
                        title="Akun ini dilindungi dari penghapusan"
                        data-bs-toggle="tooltip"
                    >
                        <i class="bi bi-trash"></i>
                    </button>
                `;
            } else {
                actionHtml += `
                    <button 
                        type="button" 
                        class="user-action-btn btn-action-delete btn-delete-user px-2" 
                        data-bs-toggle="tooltip"
                        data-id="${safeId}"
                        data-nama="${safeNama}"
                        data-email="${safeEmail}"
                        data-opd="${safeOpd}"
                        title="Hapus Akun (${safeNama})"
                    >
                        <i class="bi bi-trash"></i>
                    </button>
                `;
            }

            actionHtml += `</div>`;

            htmlRows += `
                <tr>
                    <td class="col-no">${rowNumber}</td>
                    <td class="col-nama">${safeNama}</td>
                    <td class="col-email">${safeEmail}</td>
                    <td class="col-role">${roleBadge}</td>
                    <td class="col-opd">${safeOpd}</td>
                    <td class="col-status text-center">${statusHtml}</td>
                    <td class="col-aksi">${actionHtml}</td>
                </tr>
            `;
        });

        tableBody.innerHTML = htmlRows;

        // Update Info Pagination (misal: "Menampilkan 1 – 6 dari 6 data")
        const displayStart = totalItems === 0 ? 0 : startIndex + 1;
        const displayEnd = endIndex;
        paginationInfo.textContent = `Menampilkan ${displayStart} – ${displayEnd} dari ${totalItems} data`;

        // Render Tombol Pagination
        renderPaginationButtons(totalPages);

        // Re-inisialisasi Bootstrap Tooltip
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.forEach(function (tooltipTriggerEl) {
            new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    /**
     * Render Tombol Kontrol Pagination
     */
    function renderPaginationButtons(totalPages) {
        const perPageVal = perPageSelect ? perPageSelect.value : '10';
        if (totalPages <= 1 || perPageVal === 'all') {
            paginationControls.innerHTML = `
                <button type="button" class="user-page-btn disabled" disabled title="Halaman Sebelumnya">
                    <i class="bi bi-chevron-left"></i>
                </button>
                <button type="button" class="user-page-btn active" data-page="1">1</button>
                <button type="button" class="user-page-btn disabled" disabled title="Halaman Berikutnya">
                    <i class="bi bi-chevron-right"></i>
                </button>
            `;
            return;
        }

        let buttonsHtml = '';

        // Tombol Previous (<)
        const isPrevDisabled = currentPage === 1 ? 'disabled' : '';
        buttonsHtml += `
            <button type="button" class="user-page-btn ${isPrevDisabled}" data-page="${currentPage - 1}" title="Halaman Sebelumnya">
                <i class="bi bi-chevron-left"></i>
            </button>
        `;

        // Algoritma Pagination
        const delta = 1;
        const range = [];
        const rangeWithDots = [];
        let l;

        for (let i = 1; i <= totalPages; i++) {
            if (i === 1 || i === totalPages || (i >= currentPage - delta && i <= currentPage + delta)) {
                range.push(i);
            }
        }

        for (let i of range) {
            if (l) {
                if (i - l === 2) {
                    rangeWithDots.push(l + 1);
                } else if (i - l !== 1) {
                    rangeWithDots.push('...');
                }
            }
            rangeWithDots.push(i);
            l = i;
        }

        rangeWithDots.forEach(page => {
            if (page === '...') {
                buttonsHtml += `<span class="user-page-ellipsis">...</span>`;
            } else {
                const isActive = page === currentPage ? 'active' : '';
                buttonsHtml += `
                    <button type="button" class="user-page-btn ${isActive}" data-page="${page}">
                        ${page}
                    </button>
                `;
            }
        });

        // Tombol Next (>)
        const isNextDisabled = currentPage === totalPages ? 'disabled' : '';
        buttonsHtml += `
            <button type="button" class="user-page-btn ${isNextDisabled}" data-page="${currentPage + 1}" title="Halaman Berikutnya">
                <i class="bi bi-chevron-right"></i>
            </button>
        `;

        paginationControls.innerHTML = buttonsHtml;
    }

    // ==============================================================
    // EVENT LISTENERS
    // ==============================================================

    // 1. Pencarian Real-Time (Debounce 250ms ke Server)
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            searchTimeout = setTimeout(applyFilters, 250);
        });
    }

    // 2. Clear Pencarian
    if (clearSearchBtn) {
        clearSearchBtn.addEventListener('click', function() {
            searchInput.value = '';
            applyFilters();
            searchInput.focus();
        });
    }

    // 3. Filter OPD
    if (opdFilter) {
        opdFilter.addEventListener('change', applyFilters);
    }

    // 3b. Pilihan Jumlah Data Per Halaman
    if (perPageSelect) {
        perPageSelect.addEventListener('change', function() {
            currentPage = 1;
            renderTable();
        });
    }

    // 4. Reset Filter
    if (btnResetFilter) {
        btnResetFilter.addEventListener('click', function() {
            searchInput.value = '';
            if (opdFilter) opdFilter.value = 'all';
            if (perPageSelect) perPageSelect.value = '10';
            applyFilters();
            searchInput.focus();
        });
    }

    // 5. Navigasi Pagination
    if (paginationControls) {
        paginationControls.addEventListener('click', function(e) {
            const btn = e.target.closest('.user-page-btn');
            if (!btn || btn.classList.contains('disabled') || btn.classList.contains('active')) return;
            const targetPage = parseInt(btn.getAttribute('data-page'));
            if (!isNaN(targetPage)) {
                currentPage = targetPage;
                renderTable();
                const tableCard = document.querySelector('.user-table-card');
                if (window.innerWidth < 768 && tableCard) {
                    tableCard.scrollIntoView({ behavior: 'smooth' });
                }
            }
        });
    }

    // Helper: Isi Modal Edit
    function populateEditModal(btn) {
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama');
        const email = btn.getAttribute('data-email');
        const roleId = btn.getAttribute('data-role-id');
        const opdId = btn.getAttribute('data-opd-id');
        const bidangId = btn.getAttribute('data-bidang-id');
        const active = btn.getAttribute('data-active');

        if (formEditUser) formEditUser.action = baseEditUrl + '/' + id;
        if (editIdInput) editIdInput.value = id;
        if (editNamaInput) editNamaInput.value = nama;
        if (editEmailInput) editEmailInput.value = email;
        if (editRoleInput) editRoleInput.value = roleId;
        if (editOpdInput) editOpdInput.value = opdId;
        if (editActiveInput) editActiveInput.value = active;

        // Filter bidang sesuai OPD & sesuaikan visibilitas Role -> OPD -> Bidang di modal edit
        updateBidangOptions(editOpdInput ? editOpdInput.value : '', editBidangInput, bidangId);
        handleRoleFieldVisibility(editRoleInput, document.getElementById('wrapEditOpd'), editOpdInput, document.getElementById('wrapEditBidang'), editBidangInput);
    }

    // Logika Dinamis Tampil/Sembunyi OPD & Bidang Berdasarkan Role (Catatan #10 & Transkrip Hal. 6)
    function handleRoleFieldVisibility(roleSelectEl, opdWrapEl, opdInputEl, bidangWrapEl, bidangSelectEl) {
        if (!roleSelectEl) return;
        const selectedOpt = roleSelectEl.options[roleSelectEl.selectedIndex];
        const rawRoleName = selectedOpt ? (selectedOpt.getAttribute('data-role-name') || selectedOpt.textContent || '') : '';
        const normRole = rawRoleName.trim().toLowerCase().replace(/\s+/g, '_');

        const isGlobalRole = ['super_admin', 'admin_pemkab'].includes(normRole);
        const isBidangRole = ['arsiparis', 'kepala_bidang', 'kabid'].includes(normRole);

        if (opdWrapEl) {
            if (!normRole || isGlobalRole) {
                opdWrapEl.classList.add('d-none');
                if (opdInputEl && opdInputEl.tagName === 'SELECT') {
                    opdInputEl.value = '';
                    opdInputEl.removeAttribute('required');
                }
            } else {
                opdWrapEl.classList.remove('d-none');
                if (opdInputEl && opdInputEl.tagName === 'SELECT') {
                    opdInputEl.setAttribute('required', 'required');
                }
            }
        }

        if (bidangWrapEl) {
            if (isBidangRole) {
                bidangWrapEl.classList.remove('d-none');
            } else {
                bidangWrapEl.classList.add('d-none');
                if (bidangSelectEl) bidangSelectEl.value = '';
            }
        }
    }

    // Helper: Isi Modal Switch User
    function populateSwitchModal(btn) {
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama');
        const email = btn.getAttribute('data-email');
        const role = btn.getAttribute('data-role');
        const opd = btn.getAttribute('data-opd');

        const cleanRole = role ? role.replace(/_/g, ' ') : '-';

        if (switchUserName) switchUserName.textContent = nama;
        if (switchUserNameRole) switchUserNameRole.textContent = role ? `(${cleanRole})` : '';
        if (switchUserEmail) switchUserEmail.textContent = email;
        if (switchUserRole) switchUserRole.textContent = cleanRole;
        if (switchUserOpd) switchUserOpd.textContent = opd;
        if (btnConfirmSwitchUser) btnConfirmSwitchUser.href = baseSwitchUrl + '/' + id;
    }

    // Filter Bidang dropdown berdasarkan OPD yang dipilih
    function updateBidangOptions(selectedOpdId, selectElement, selectBidangIdToSet) {
        if (!selectElement) return;
        const currentVal = selectBidangIdToSet || '';
        selectElement.innerHTML = '<option value="">-- Pilih Bidang (Opsional) --</option>';

        if (!selectedOpdId) {
            allBidangs.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id_bidang;
                opt.textContent = b.nama_bidang;
                if (b.id_bidang === currentVal) opt.selected = true;
                selectElement.appendChild(opt);
            });
            return;
        }

        const filtered = allBidangs.filter(b => b.id_opd === selectedOpdId);
        filtered.forEach(b => {
            const opt = document.createElement('option');
            opt.value = b.id_bidang;
            opt.textContent = b.nama_bidang;
            if (b.id_bidang === currentVal) opt.selected = true;
            selectElement.appendChild(opt);
        });
    }

    // Event listener perubahan Role & OPD di modal tambah
    const createRoleSelect = document.getElementById('create_id_role');
    const createOpdSelect = document.getElementById('create_id_opd');
    const createBidangSelect = document.getElementById('create_id_bidang');
    const wrapCreateOpd = document.getElementById('wrapCreateOpd');
    const wrapCreateBidang = document.getElementById('wrapCreateBidang');

    if (createRoleSelect) {
        createRoleSelect.addEventListener('change', function() {
            handleRoleFieldVisibility(createRoleSelect, wrapCreateOpd, createOpdSelect, wrapCreateBidang, createBidangSelect);
        });
        handleRoleFieldVisibility(createRoleSelect, wrapCreateOpd, createOpdSelect, wrapCreateBidang, createBidangSelect);
    }

    if (createOpdSelect && createBidangSelect) {
        createOpdSelect.addEventListener('change', function() {
            updateBidangOptions(this.value, createBidangSelect);
        });
    }

    // Reset form modal tambah setiap kali dibuka kembali (Transkrip Hal. 3 [07:22])
    const modalTambahEl = document.getElementById('modalTambahUser');
    const formTambahUser = document.getElementById('formTambahUser');
    if (modalTambahEl && formTambahUser) {
        modalTambahEl.addEventListener('show.bs.modal', function() {
            formTambahUser.reset();
            handleRoleFieldVisibility(createRoleSelect, wrapCreateOpd, createOpdSelect, wrapCreateBidang, createBidangSelect);
        });
    }

    // Event listener perubahan Role & OPD di modal edit
    if (editRoleInput) {
        editRoleInput.addEventListener('change', function() {
            handleRoleFieldVisibility(editRoleInput, document.getElementById('wrapEditOpd'), editOpdInput, document.getElementById('wrapEditBidang'), editBidangInput);
        });
    }
    if (editOpdInput && editBidangInput) {
        editOpdInput.addEventListener('change', function() {
            updateBidangOptions(this.value, editBidangInput);
        });
    }

    // 6. Event Modal Edit (show.bs.modal & delegation)
    const modalEditEl = document.getElementById('modalEditUser');
    if (modalEditEl) {
        modalEditEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button && button.classList.contains('btn-edit-user')) {
                populateEditModal(button);
            }
        });
    }

    // 7. Event Modal Switch User (show.bs.modal & delegation)
    const modalSwitchEl = document.getElementById('modalSwitchUser');
    if (modalSwitchEl) {
        modalSwitchEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button && button.classList.contains('btn-switch-user')) {
                populateSwitchModal(button);
            }
        });

        const btnCloseSwitch = modalSwitchEl.querySelector('.btn-close');
        if (btnCloseSwitch) {
            btnCloseSwitch.addEventListener('click', function(e) {
                e.preventDefault();
                const modal = bootstrap.Modal.getOrCreateInstance(modalSwitchEl);
                modal.hide();
            });
        }
    }

    // Delegasi klik fallback
    document.addEventListener('click', function(e) {
        const editBtn = e.target.closest('.btn-edit-user');
        if (editBtn) {
            populateEditModal(editBtn);
            if (modalEditEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalEditEl);
                modal.show();
            }
        }

        const switchBtn = e.target.closest('.btn-switch-user');
        if (switchBtn) {
            populateSwitchModal(switchBtn);
            if (modalSwitchEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalSwitchEl);
                modal.show();
            }
        }

        const resetPwBtn = e.target.closest('.btn-reset-pw-user');
        if (resetPwBtn) {
            const id = resetPwBtn.getAttribute('data-id');
            const nama = resetPwBtn.getAttribute('data-nama');
            const defaultPwLabel = <?= json_encode($defaultPassword ?? 'Admin123!') ?>;
            const baseResetUrl = "<?= base_url('/users/reset-password') ?>/" + id;

            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'question',
                    title: 'Reset Password Pengguna?',
                    html: `<div class="small text-secondary mb-2">Pilih metode reset password untuk <strong>${escapeHtml(nama)}</strong>:</div>`,
                    showDenyButton: true,
                    showCancelButton: true,
                    confirmButtonText: `Default (${defaultPwLabel})`,
                    denyButtonText: 'Password Random',
                    cancelButtonText: 'Batal',
                    confirmButtonColor: '#1E60EC',
                    denyButtonColor: '#0D9488'
                }).then((result) => {
                    if (result.isConfirmed) {
                        window.location.href = baseResetUrl + '?mode=default';
                    } else if (result.isDenied) {
                        window.location.href = baseResetUrl + '?mode=random';
                    }
                });
            } else if (confirm('Reset password ' + nama + ' ke password default?')) {
                window.location.href = baseResetUrl;
            }
        }
    });

    // Event Listener: Toggle Cepat Aktif/Nonaktif di Kolom Status (Catatan #8)
    document.addEventListener('change', function(e) {
        const toggleEl = e.target.closest('.user-status-toggle');
        if (!toggleEl) return;

        const id = toggleEl.getAttribute('data-id');
        const nama = toggleEl.getAttribute('data-nama');
        const intendedState = toggleEl.checked ? 1 : 0;
        toggleEl.disabled = true;

        fetch("<?= base_url('/users/toggle-status') ?>/" + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: "<?= csrf_token() ?>=<?= csrf_hash() ?>"
        })
        .then(res => res.json())
        .then(data => {
            toggleEl.disabled = false;
            if (data.status === 'success') {
                const newActive = parseInt(data.is_active, 10) === 1;
                toggleEl.checked = newActive;

                // Update rawData & badge
                const targetItem = rawData.find(u => u.id_user === id);
                if (targetItem) targetItem.is_active = newActive ? 1 : 0;

                const badgeEl = document.getElementById('statusBadge_' + id);
                if (badgeEl) {
                    badgeEl.className = 'user-status-badge ' + (newActive ? 'status-aktif' : 'status-nonaktif');
                    const dot = badgeEl.querySelector('.status-dot');
                    const lbl = badgeEl.querySelector('.status-label-text');
                    if (dot) dot.className = 'status-dot ' + (newActive ? 'dot-aktif' : 'dot-nonaktif');
                    if (lbl) lbl.textContent = newActive ? 'Aktif' : 'Nonaktif';
                }

                // Update kartu statistik
                const activeCnt = rawData.filter(u => parseInt(u.is_active, 10) === 1).length;
                const inactiveCnt = rawData.length - activeCnt;
                const statActiveEl = document.getElementById('statActiveUsers');
                const statInactiveEl = document.getElementById('statInactiveUsers');
                if (statActiveEl) statActiveEl.textContent = activeCnt.toLocaleString('id-ID');
                if (statInactiveEl) statInactiveEl.textContent = inactiveCnt.toLocaleString('id-ID');

                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Diperbarui',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                }
            } else {
                toggleEl.checked = !intendedState;
                if (typeof Swal !== 'undefined') {
                    Swal.fire({ icon: 'error', title: 'Gagal', text: data.message || 'Gagal mengubah status.' });
                }
            }
        })
        .catch(() => {
            toggleEl.disabled = false;
            toggleEl.checked = !intendedState;
        });
    });

    // Pindahkan modals ke <body> agar terhindar dari stacking context
    ['modalTambahUser', 'modalEditUser', 'modalSwitchUser'].forEach(function(modalId) {
        const m = document.getElementById(modalId);
        if (m && m.parentElement !== document.body) {
            document.body.appendChild(m);
        }
    });

    // Inisialisasi awal render tabel
    renderTable();
});
</script>
<?= $this->endSection() ?>
