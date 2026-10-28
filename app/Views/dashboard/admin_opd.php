<!-- ============================================================== -->
<!-- DASHBOARD KHUSUS ADMINISTRATOR OPD - SIAMA                      -->
<!-- ============================================================== -->

<!-- 1. HEADER DASHBOARD -->
<div class="dashboard-header mb-4">
    <h1 class="h3 fw-bold text-dark mb-1" style="font-size: 24px; letter-spacing: -0.3px;">Selamat Datang, <?= esc($nama_lengkap ?? 'Administrator OPD') ?>!</h1>
    <p class="text-secondary mb-0" style="font-size: 14px;">Ini adalah ringkasan statistik berdasarkan akses Anda sebagai Admin OPD<?= !empty($nama_opd) ? ' - ' . esc($nama_opd) : '' ?>.</p>
</div>

<!-- 2. STATISTIC CARDS (3 Cards Horizontal) -->
<div class="row g-3 g-md-4 mb-4">
    <!-- Card 1: Total Pengguna di OPD Anda -->
    <div class="col-12 col-md-4">
        <div class="card border h-100 shadow-none opd-stat-card opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background-color: #EFF6FF;">
                    <i class="bi bi-people fs-4 text-primary"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-secondary small mb-1" style="font-size: 13px; font-weight: 500;">Total Pengguna di OPD Anda</div>
                    <div class="fw-bold text-dark" style="font-size: 26px; line-height: 1;"><?= number_format($stats['total_users_opd'] ?? count($users_opd ?? []), 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 2: Total Bidang di OPD Anda -->
    <div class="col-12 col-md-4">
        <div class="card border h-100 shadow-none opd-stat-card opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background-color: #ECFDF5;">
                    <i class="bi bi-building fs-4" style="color: #10B981;"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-secondary small mb-1" style="font-size: 13px; font-weight: 500;">Total Bidang di OPD Anda</div>
                    <div class="fw-bold text-dark" style="font-size: 26px; line-height: 1;"><?= number_format($stats['total_bidang_opd'] ?? count($bidang_opd ?? []), 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Card 3: Total Arsip di OPD Anda -->
    <div class="col-12 col-md-4">
        <div class="card border h-100 shadow-none opd-stat-card opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden; transition: transform 0.2s ease, box-shadow 0.2s ease;">
            <div class="card-body p-3 p-md-4 d-flex align-items-center gap-3">
                <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 52px; height: 52px; background-color: #FAF5FF;">
                    <i class="bi bi-file-earmark-text fs-4" style="color: #8B5CF6;"></i>
                </div>
                <div class="min-w-0">
                    <div class="text-secondary small mb-1" style="font-size: 13px; font-weight: 500;">Total Arsip di OPD Anda</div>
                    <div class="fw-bold text-dark" style="font-size: 26px; line-height: 1;"><?= number_format($stats['total_arsip_opd'] ?? 0, 0, ',', '.') ?></div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3. MIDDLE ROW: SECTION 1 (PENGGUNA OPD) & SECTION 2 (BIDANG DI OPD ANDA) -->
<div class="row g-4 mb-4">
    <!-- Section 1: Pengguna OPD -->
    <div class="col-12 col-lg-6">
        <div class="card border h-100 shadow-none opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden;">
            <div class="card-body p-0 d-flex flex-column">
                <!-- Header Card -->
                <div class="p-3 px-4 d-flex align-items-center justify-content-between border-bottom" style="border-color: #F1F5F9 !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-people text-primary fs-5"></i>
                        <span class="fw-bold text-dark" style="font-size: 15px;">Pengguna OPD</span>
                    </div>
                    <a href="<?= base_url('/users') ?>" class="text-primary text-decoration-none fw-semibold small d-flex align-items-center gap-1">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <!-- Table Pengguna -->
                <div class="table-responsive flex-grow-1">
                    <table class="table align-middle mb-0" style="font-size: 13.5px;">
                        <thead style="border-bottom: 1px solid #E5E7EB;">
                            <tr>
                                <th class="text-secondary fw-semibold ps-4 py-3" style="width: 50px;">No</th>
                                <th class="text-secondary fw-semibold py-3">Nama</th>
                                <th class="text-secondary fw-semibold py-3">Role</th>
                                <th class="text-secondary fw-semibold pe-4 py-3 text-center" style="width: 100px;">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($users_opd)) : ?>
                                <?php $no = 1; foreach ($users_opd as $u) : ?>
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td class="ps-4 text-muted"><?= $no++ ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($u['nama']) ?></td>
                                        <td class="text-secondary"><?= esc($u['nama_role'] ?? 'Pengguna') ?></td>
                                        <td class="pe-4 text-center">
                                            <?php if ((int)($u['is_active'] ?? 1) === 1) : ?>
                                                <span class="badge rounded-pill" style="background-color: #DEF7EC; color: #03543F; font-weight: 500; font-size: 12px; padding: 4px 12px;">Aktif</span>
                                            <?php else : ?>
                                                <span class="badge rounded-pill" style="background-color: #FDE8E8; color: #9B1C1C; font-weight: 500; font-size: 12px; padding: 4px 12px;">Nonaktif</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4 text-muted small">
                                        Belum ada data pengguna di OPD ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Section 2: Bidang di OPD Anda -->
    <div class="col-12 col-lg-6">
        <div class="card border h-100 shadow-none opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden;">
            <div class="card-body p-0 d-flex flex-column">
                <!-- Header Card -->
                <div class="p-3 px-4 d-flex align-items-center justify-content-between border-bottom" style="border-color: #F1F5F9 !important;">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-building text-primary fs-5"></i>
                        <span class="fw-bold text-dark" style="font-size: 15px;">Bidang di OPD Anda</span>
                    </div>
                    <a href="<?= base_url('/bidang') ?>" class="text-primary text-decoration-none fw-semibold small d-flex align-items-center gap-1">
                        Lihat Semua <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <!-- Table Bidang -->
                <div class="table-responsive flex-grow-1">
                    <table class="table align-middle mb-0" style="font-size: 13.5px;">
                        <thead style="border-bottom: 1px solid #E5E7EB;">
                            <tr>
                                <th class="text-secondary fw-semibold ps-4 py-3" style="width: 50px;">No</th>
                                <th class="text-secondary fw-semibold py-3">Nama Bidang</th>
                                <th class="text-secondary fw-semibold pe-4 py-3 text-center" style="width: 140px;">Jumlah Arsip</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($bidang_opd)) : ?>
                                <?php $no = 1; foreach ($bidang_opd as $b) : ?>
                                    <tr style="border-bottom: 1px solid #F1F5F9;">
                                        <td class="ps-4 text-muted"><?= $no++ ?></td>
                                        <td class="fw-semibold text-dark"><?= esc($b['nama_bidang']) ?></td>
                                        <td class="pe-4 text-center text-secondary"><?= number_format($b['total_arsip'] ?? 0, 0, ',', '.') ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else : ?>
                                <tr>
                                    <td colspan="3" class="text-center py-4 text-muted small">
                                        Belum ada data bidang di OPD ini.
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4. BOTTOM SECTION: SECTION 3 (DAFTAR ARSIP TERBARU) -->
<div class="card border shadow-none mb-4 opd-dashboard-card" style="border-radius: 14px; border-color: #E5E7EB; background-color: #FFFFFF; overflow: hidden;">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table align-middle mb-0" style="font-size: 13.5px;">
                <thead style="border-bottom: 1px solid #E5E7EB;">
                    <tr>
                        <th class="text-secondary fw-semibold ps-4 py-3" style="width: 60px;">No</th>
                        <th class="text-secondary fw-semibold py-3" style="width: 200px;">Nomor Arsip</th>
                        <th class="text-secondary fw-semibold py-3">Nama Arsip</th>
                        <th class="text-secondary fw-semibold py-3" style="width: 160px;">Tanggal</th>
                        <th class="text-secondary fw-semibold py-3 text-center" style="width: 140px;">Status</th>
                        <th class="text-secondary fw-semibold pe-4 py-3 text-center" style="width: 100px;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($recent_arsip_opd)) : ?>
                        <?php $no = 1; foreach ($recent_arsip_opd as $a) : ?>
                            <tr style="border-bottom: 1px solid #F1F5F9;">
                                <td class="ps-4 text-muted"><?= $no++ ?></td>
                                <td class="fw-semibold">
                                    <a href="<?= base_url('/arsip/detail/' . $a['id_arsip']) ?>" class="text-primary text-decoration-none">
                                        <?= esc($a['nomor_arsip']) ?>
                                    </a>
                                </td>
                                <td class="text-dark"><?= esc($a['nama_arsip']) ?></td>
                                <td class="text-secondary small"><?= date('d M Y', strtotime($a['created_at'])) ?></td>
                                <td class="text-center">
                                    <?php 
                                    $status = strtolower($a['status_verifikasi'] ?? 'draft');
                                    if ($status === 'terverifikasi' || $status === 'verified') : ?>
                                        <span class="badge rounded-pill" style="background-color: #DEF7EC; color: #03543F; font-weight: 500; font-size: 12px; padding: 4px 12px;">Terverifikasi</span>
                                    <?php elseif ($status === 'menunggu_verifikasi' || $status === 'pending') : ?>
                                        <span class="badge rounded-pill" style="background-color: #FEF08A; color: #854D0E; font-weight: 500; font-size: 12px; padding: 4px 12px;">Menunggu</span>
                                    <?php else : ?>
                                        <span class="badge rounded-pill" style="background-color: #F1F5F9; color: #475569; font-weight: 500; font-size: 12px; padding: 4px 12px;"><?= ucfirst($status) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td class="pe-4 text-center">
                                    <a href="<?= base_url('/arsip/detail/' . $a['id_arsip']) ?>" class="btn btn-sm btn-light border opd-btn-action" style="border-radius: 8px;" title="Detail Arsip">
                                        <i class="bi bi-eye text-secondary"></i>
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else : ?>
                        <!-- Clean Empty State sesuai Desain Acuan -->
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <div class="d-flex flex-column align-items-center justify-content-center py-4">
                                    <div class="rounded-3 d-flex align-items-center justify-content-center mb-3" style="width: 56px; height: 56px; border: 1px solid #E2E8F0; background-color: #F8FAFC;">
                                        <i class="bi bi-file-earmark fs-3" style="color: #94A3B8;"></i>
                                    </div>
                                    <h6 class="fw-bold text-dark mb-1" style="font-size: 16px;">Belum ada data</h6>
                                    <p class="text-muted small mb-0">Saat ini belum ada arsip yang tercatat di OPD Anda.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
