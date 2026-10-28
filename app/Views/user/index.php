<?= $this->extend('layouts/main') ?>

<?= $this->section('title') ?>
Manajemen Akun - SIAMA
<?= $this->endSection() ?>

<?= $this->section('styles') ?>
<link rel="stylesheet" href="<?= base_url('css/user-master.css?v=' . (file_exists(FCPATH . 'css/user-master.css') ? filemtime(FCPATH . 'css/user-master.css') : time())) ?>">
<?= $this->endSection() ?>

<?= $this->section('content') ?>

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
        <i class="bi bi-exclamation-circle-fill fs-5 text-danger"></i>
        <div class="fw-medium small">
            <?= session()->getFlashdata('error') ?>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Tutup"></button>
    </div>
<?php endif; ?>

<!-- Header Halaman -->
<div class="user-page-header">
    <h1 class="user-page-title">Manajemen Akun</h1>
    <p class="user-page-subtitle">Kelola data Organisasi Perangkat Daerah (OPD) di Kabupaten Buleleng.</p>
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
                class="user-btn-add" 
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
                    <th scope="col" class="col-no">No</th>
                    <th scope="col" class="col-nama">Nama</th>
                    <th scope="col" class="col-email">Email</th>
                    <th scope="col" class="col-role">Role</th>
                    <th scope="col" class="col-opd">OPD</th>
                    <th scope="col" class="col-status">Status</th>
                    <th scope="col" class="col-aksi">Aksi</th>
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
                            <option value="">-- Pilih Role Pengguna --</option>
                            <?php if (!empty($roles)) : ?>
                                <?php foreach ($roles as $r) : ?>
                                    <option value="<?= esc($r['id_role']) ?>"><?= esc($r['nama_role']) ?></option>
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

                    <!-- Info Password Default -->
                    <div class="p-2 px-3 bg-light rounded-2 border d-flex align-items-center gap-2 small text-muted">
                        <i class="bi bi-info-circle text-primary"></i>
                        <span>Password akun baru akan disetel otomatis ke: <strong class="text-dark font-monospace">Admin123!</strong></span>
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
                            <option value="">-- Pilih Role --</option>
                            <?php if (!empty($roles)) : ?>
                                <?php foreach ($roles as $r) : ?>
                                    <option value="<?= esc($r['id_role']) ?>"><?= esc($r['nama_role']) ?></option>
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

<!-- ============================================================== -->
<!-- MODAL: KONFIRMASI HAPUS PENGGUNA                                -->
<!-- ============================================================== -->
<div class="modal fade" id="modalHapusUser" tabindex="-1" aria-labelledby="modalHapusUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" style="max-width: 430px;">
        <div class="modal-content user-modal text-center p-2">
            <div class="modal-body user-modal-body pt-4 pb-2">
                <div class="empty-icon-wrap bg-danger-subtle text-danger mb-3" style="width: 60px; height: 60px; font-size: 26px;">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                </div>
                <h5 class="user-modal-title justify-content-center mb-2">Hapus Akun Pengguna?</h5>
                <p class="text-secondary small mb-3">
                    Apakah Anda yakin ingin menghapus akun pengguna ini? Tindakan ini tidak dapat dibatalkan.
                </p>
                <div class="p-3 mb-3 bg-light rounded-3 text-start border small text-break">
                    <div class="text-muted small mb-1">Nama Pengguna:</div>
                    <strong class="d-block text-dark fs-6 mb-2" id="deleteUserName">Nama Pengguna</strong>
                    <div class="text-muted small mb-1">Email:</div>
                    <div class="text-secondary mb-2" id="deleteUserEmail">email@domain.com</div>
                    <div class="text-muted small mb-1">Organisasi Perangkat Daerah:</div>
                    <span class="text-secondary fw-medium" id="deleteUserOpd">Nama OPD</span>
                </div>
            </div>
            <div class="modal-footer user-modal-footer justify-content-center bg-transparent border-0 pt-0 pb-3 gap-2">
                <button type="button" class="btn btn-light border px-3" data-bs-dismiss="modal">Batal</button>
                <a href="#" id="btnConfirmDeleteUser" class="btn btn-danger px-4 fw-medium">
                    <i class="bi bi-trash me-1"></i> Hapus
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ============================================================== -->
<!-- MODAL: KONFIRMASI MASUK SEBAGAI (SWITCH USER)                  -->
<!-- ============================================================== -->
<div class="modal fade" id="modalSwitchUser" tabindex="-1" aria-labelledby="modalSwitchUserLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 470px;">
        <div class="modal-content border-0 position-relative p-3" style="border-radius: 18px; box-shadow: 0 20px 45px rgba(0, 0, 0, 0.12);">
            <!-- Close Button -->
            <button type="button" class="btn-close position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Tutup"></button>

            <div class="modal-body text-center pt-3 pb-2 px-2">
                <!-- Top Purple Icon Badge -->
                <div class="mb-3 mx-auto d-flex align-items-center justify-content-center" style="width: 68px; height: 68px; border-radius: 50%; background-color: #F3E8FF; color: #7C3AED; font-size: 28px;">
                    <i class="bi bi-box-arrow-in-right"></i>
                </div>

                <!-- Judul & Subjudul -->
                <h5 class="fw-bold text-dark mb-2" id="modalSwitchUserLabel" style="font-size: 21px; letter-spacing: -0.2px;">Masuk Sebagai Pengguna?</h5>
                <p class="text-secondary small mb-4 px-2" style="font-size: 13px; line-height: 1.5; color: #64748B;">
                    Anda akan dialihkan (<em>impersonate</em>) ke akun pengguna ini tanpa perlu memasukkan password.
                </p>

                <!-- Information Card -->
                <div class="p-3 mb-4 rounded-3 text-start border" style="background-color: #F8FAFC; border-color: #E2E8F0 !important; border-radius: 14px !important;">
                    <!-- Item 1: Nama Pengguna -->
                    <div class="d-flex align-items-center">
                        <div class="rounded-circle d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 50px; height: 50px; background-color: #EFF6FF; color: #1E60EC; font-size: 24px;">
                            <i class="bi bi-person"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="text-secondary fw-medium mb-1" style="font-size: 11.5px; color: #64748B;">Nama Pengguna</div>
                            <div class="fw-bold text-dark text-truncate" style="font-size: 15px; line-height: 1.3;" id="switchUserName">-</div>
                            <div class="fw-bold text-dark" style="font-size: 14px; line-height: 1.3;" id="switchUserNameRole"></div>
                        </div>
                    </div>

                    <div class="border-top my-3" style="border-color: #E2E8F0 !important;"></div>

                    <!-- Item 2: Email -->
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 38px; height: 38px; border-radius: 8px; background-color: #EFF6FF; color: #1E60EC; font-size: 18px;">
                            <i class="bi bi-envelope"></i>
                        </div>
                        <div class="flex-grow-1 overflow-hidden">
                            <div class="text-secondary fw-medium mb-1" style="font-size: 11.5px; color: #64748B;">Email</div>
                            <div class="text-dark fw-medium text-truncate" style="font-size: 13.5px;" id="switchUserEmail">-</div>
                        </div>
                    </div>

                    <div class="border-top my-3" style="border-color: #E2E8F0 !important;"></div>

                    <!-- Item 3: Role -->
                    <div class="d-flex align-items-center">
                        <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0" style="width: 38px; height: 38px; border-radius: 8px; background-color: #ECFDF5; color: #10B981; font-size: 18px;">
                            <i class="bi bi-shield-check"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-secondary fw-medium mb-1" style="font-size: 11.5px; color: #64748B;">Role</div>
                            <div>
                                <span class="badge" id="switchUserRole" style="background-color: #EBF2FE; color: #1E60EC; font-size: 12px; font-weight: 600; padding: 4px 10px; border-radius: 6px;">-</span>
                            </div>
                        </div>
                    </div>

                    <div class="border-top my-3" style="border-color: #E2E8F0 !important;"></div>

                    <!-- Item 4: OPD -->
                    <div class="d-flex align-items-start">
                        <div class="d-flex align-items-center justify-content-center me-3 flex-shrink-0 mt-1" style="width: 38px; height: 38px; border-radius: 8px; background-color: #FEF3C7; color: #D97706; font-size: 18px;">
                            <i class="bi bi-bank"></i>
                        </div>
                        <div class="flex-grow-1">
                            <div class="text-secondary fw-medium mb-1" style="font-size: 11.5px; color: #64748B;">OPD</div>
                            <div class="text-dark fw-medium" style="font-size: 13px; line-height: 1.45;" id="switchUserOpd">-</div>
                        </div>
                    </div>
                </div>

                <!-- Action Buttons: Batal & Masuk Sekarang -->
                <div class="d-flex align-items-center gap-3 pt-1">
                    <button type="button" class="btn btn-light border py-2 px-4 fw-semibold text-dark flex-fill" data-bs-dismiss="modal" style="border-radius: 10px; height: 44px; font-size: 14px;">
                        Batal
                    </button>
                    <a href="#" id="btnConfirmSwitchUser" class="btn btn-primary py-2 px-4 fw-semibold text-white d-flex align-items-center justify-content-center gap-2 flex-fill" style="background-color: #1E60EC; border-color: #1E60EC; border-radius: 10px; height: 44px; font-size: 14px;">
                        <i class="bi bi-box-arrow-in-right fs-5"></i>
                        <span>Masuk Sekarang</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Data dari Controller CI4
    const rawData = <?= json_encode($users ?? []) ?>;
    const allBidangs = <?= json_encode($bidangs ?? []) ?>;
    const currentUserId = "<?= esc(session()->get('user_id') ?? '') ?>";
    const isCanSwitch = <?= (session()->get('nama_role') === 'Admin_Pemkab' || session()->has('original_admin_id')) ? 'true' : 'false' ?>;
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

    // Elemen Modal Hapus
    const deleteUserName = document.getElementById('deleteUserName');
    const deleteUserEmail = document.getElementById('deleteUserEmail');
    const deleteUserOpd = document.getElementById('deleteUserOpd');
    const btnConfirmDeleteUser = document.getElementById('btnConfirmDeleteUser');

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

    /**
     * Filter data pengguna berdasarkan teks pencarian dan pilihan OPD
     */
    function applyFilters() {
        const query = searchInput.value.trim().toLowerCase();
        const selectedOpd = opdFilter ? opdFilter.value : 'all';

        // Tampilkan/sembunyikan tombol clear search
        if (query.length > 0) {
            clearSearchBtn.classList.remove('d-none');
        } else {
            clearSearchBtn.classList.add('d-none');
        }

        filteredData = rawData.filter(item => {
            // Pencarian Nama, Email, OPD, Bidang, atau Role
            const matchQuery = !query || 
                (item.nama && item.nama.toLowerCase().includes(query)) ||
                (item.email && item.email.toLowerCase().includes(query)) ||
                (item.nama_opd && item.nama_opd.toLowerCase().includes(query)) ||
                (item.nama_bidang && item.nama_bidang.toLowerCase().includes(query)) ||
                (item.nama_role && item.nama_role.toLowerCase().includes(query));

            // Filter OPD
            let matchOpd = true;
            if (selectedOpd !== 'all') {
                matchOpd = item.id_opd === selectedOpd;
            }

            return matchQuery && matchOpd;
        });

        currentPage = 1;
        renderTable();
    }

    /**
     * Render Baris Tabel dan Navigasi Pagination
     */
    function renderTable() {
        const totalItems = filteredData.length;
        const perPageVal = perPageSelect ? perPageSelect.value : '10';
        const effectivePerPage = perPageVal === 'all' ? Math.max(totalItems, 1) : (parseInt(perPageVal, 10) || 10);
        const totalPages = Math.ceil(totalItems / effectivePerPage) || 1;

        if (currentPage > totalPages) {
            currentPage = totalPages;
        }

        // Tampilkan / Sembunyikan Empty State
        if (totalItems === 0) {
            tableBody.innerHTML = '';
            emptyState.classList.remove('d-none');
            paginationInfo.textContent = 'Menampilkan 0 data';
            paginationControls.innerHTML = '';
            return;
        } else {
            emptyState.classList.add('d-none');
        }

        // Slice data untuk halaman aktif
        const startIndex = (currentPage - 1) * effectivePerPage;
        const endIndex = Math.min(startIndex + effectivePerPage, totalItems);
        const pageItems = filteredData.slice(startIndex, endIndex);

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

            // Status Indicator
            let statusHtml = '';
            if (isActive) {
                statusHtml = `
                    <div class="user-status-wrap">
                        <span class="user-status-badge status-aktif">
                            <span class="status-dot dot-aktif"></span>
                            Aktif
                        </span>
                        <span class="user-status-sub">(Akun dari Pemkab)</span>
                    </div>
                `;
            } else {
                statusHtml = `
                    <div class="user-status-wrap">
                        <span class="user-status-badge status-nonaktif">
                            <span class="status-dot dot-nonaktif"></span>
                            Tidak Aktif
                        </span>
                    </div>
                `;
            }

            // Aksi Buttons: Switch User, Edit, and Hapus (with icon + text)
            let actionHtml = `<div class="user-action-group">`;

            // Tombol Switch User / Masuk Sebagai (Khusus Admin Pemkab)
            if (isCanSwitch) {
                if (isSelf) {
                    actionHtml += `
                        <button 
                            type="button" 
                            class="user-action-btn btn-action-switch current-user" 
                            disabled
                            aria-disabled="true"
                            title="Akun Anda yang sedang aktif"
                        >
                            <i class="bi bi-person-check-fill"></i>
                            <span>Akun Aktif</span>
                        </button>
                    `;
                } else {
                    actionHtml += `
                        <button 
                            type="button" 
                            class="user-action-btn btn-action-switch btn-switch-user" 
                            data-bs-toggle="modal"
                            data-bs-target="#modalSwitchUser"
                            data-id="${safeId}"
                            data-nama="${safeNama}"
                            data-email="${safeEmail}"
                            data-role="${safeRole}"
                            data-opd="${safeOpd}"
                            title="Masuk sebagai ${safeNama} (${safeRole})"
                        >
                            <i class="bi bi-box-arrow-in-right"></i>
                            <span>Masuk Sebagai</span>
                        </button>
                    `;
                }
            }

            actionHtml += `
                    <button 
                        type="button" 
                        class="user-action-btn btn-action-edit btn-edit-user" 
                        data-bs-toggle="modal"
                        data-bs-target="#modalEditUser"
                        data-id="${safeId}"
                        data-nama="${safeNama}"
                        data-email="${safeEmail}"
                        data-role-id="${safeRoleId}"
                        data-opd-id="${opdId}"
                        data-bidang-id="${bidangId}"
                        data-active="${isActive ? '1' : '0'}"
                        title="Edit Akun Pengguna"
                    >
                        <i class="bi bi-pencil-square"></i>
                        <span>Edit</span>
                    </button>
            `;

            // Cek apakah akun ini adalah Admin Pemkab atau akun yang sedang login (dilindungi dari penghapusan)
            const isAdminPemkab = (row.nama_role || '').trim().toLowerCase() === 'admin_pemkab';
            const isProtected = isSelf || isAdminPemkab;

            if (isProtected) {
                actionHtml += `
                    <button 
                        type="button" 
                        class="user-action-btn btn-action-delete disabled" 
                        disabled
                        aria-disabled="true"
                        title="Akun Administrator Pemkab tidak dapat dihapus"
                        data-bs-toggle="tooltip"
                    >
                        <i class="bi bi-trash"></i>
                        <span>Hapus</span>
                    </button>
                `;
            } else {
                actionHtml += `
                    <button 
                        type="button" 
                        class="user-action-btn btn-action-delete btn-delete-user" 
                        data-bs-toggle="modal"
                        data-bs-target="#modalHapusUser"
                        data-id="${safeId}"
                        data-nama="${safeNama}"
                        data-email="${safeEmail}"
                        data-opd="${safeOpd}"
                        title="Hapus Akun Pengguna"
                        data-bs-toggle="tooltip"
                    >
                        <i class="bi bi-trash"></i>
                        <span>Hapus</span>
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
                    <td class="col-status">${statusHtml}</td>
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

    // 1. Pencarian Real-Time
    if (searchInput) {
        searchInput.addEventListener('input', applyFilters);
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

        // Filter bidang sesuai OPD di modal edit
        updateBidangOptions(editOpdInput ? editOpdInput.value : '', editBidangInput, bidangId);
    }

    // Helper: Isi Modal Hapus
    function populateDeleteModal(btn) {
        if (!btn) return;
        const id = btn.getAttribute('data-id');
        const nama = btn.getAttribute('data-nama');
        const email = btn.getAttribute('data-email');
        const opd = btn.getAttribute('data-opd');

        if (deleteUserName) deleteUserName.textContent = nama;
        if (deleteUserEmail) deleteUserEmail.textContent = email;
        if (deleteUserOpd) deleteUserOpd.textContent = opd;
        if (btnConfirmDeleteUser) btnConfirmDeleteUser.href = baseDeleteUrl + '/' + id;
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

    // Event listener perubahan OPD di modal tambah
    const createOpdSelect = document.getElementById('create_id_opd');
    const createBidangSelect = document.getElementById('create_id_bidang');
    if (createOpdSelect && createBidangSelect) {
        createOpdSelect.addEventListener('change', function() {
            updateBidangOptions(this.value, createBidangSelect);
        });
    }

    // Event listener perubahan OPD di modal edit
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

    // 7. Event Modal Hapus (show.bs.modal & delegation)
    const modalHapusEl = document.getElementById('modalHapusUser');
    if (modalHapusEl) {
        modalHapusEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button && button.classList.contains('btn-delete-user')) {
                populateDeleteModal(button);
            }
        });
    }

    // 8. Event Modal Switch User (show.bs.modal & delegation)
    const modalSwitchEl = document.getElementById('modalSwitchUser');
    if (modalSwitchEl) {
        modalSwitchEl.addEventListener('show.bs.modal', function(event) {
            const button = event.relatedTarget;
            if (button && button.classList.contains('btn-switch-user')) {
                populateSwitchModal(button);
            }
        });
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

        const deleteBtn = e.target.closest('.btn-delete-user');
        if (deleteBtn) {
            populateDeleteModal(deleteBtn);
            if (modalHapusEl) {
                const modal = bootstrap.Modal.getOrCreateInstance(modalHapusEl);
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
    });

    // Pindahkan modals ke <body> agar terhindar dari stacking context
    ['modalTambahUser', 'modalEditUser', 'modalHapusUser', 'modalSwitchUser'].forEach(function(modalId) {
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
