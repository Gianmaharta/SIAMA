<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use CodeIgniter\Publisher\Publisher;

class SiamaSeeder extends Seeder
{
    private function uuidv4()
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    public function run()
    {
        // Generate UUIDs
        $idOpdKominfo = $this->uuidv4();
        $idBidangArsip = $this->uuidv4();
        
        $idKlasifikasi1 = $this->uuidv4();
        $idKlasifikasi2 = $this->uuidv4();
        $idKlasifikasi3 = $this->uuidv4();

        $idSpt1 = $this->uuidv4();
        $idSpt2 = $this->uuidv4();
        
        $idRole1 = $this->uuidv4();
        $idRole2 = $this->uuidv4();
        $idRole3 = $this->uuidv4();
        $idRole4 = $this->uuidv4();
        $idRole5 = $this->uuidv4();

        $idUser1 = $this->uuidv4();
        $idUser2 = $this->uuidv4();
        $idUser3 = $this->uuidv4();
        $idUser4 = $this->uuidv4();
        $idUser5 = $this->uuidv4();

        // ============================================================
        // 1. SEED TABEL: opd & bidang (Untuk referensi Foreign Key)
        // ============================================================
        $opd = [
            ['id_opd' => $idOpdKominfo, 'kode_opd' => 'OPD-001', 'nama_opd' => 'Dinas Komunikasi dan Informatika'],
        ];
        $this->db->table('opd')->ignore(true)->insertBatch($opd);

        $bidang = [
            ['id_bidang' => $idBidangArsip, 'id_opd' => $idOpdKominfo, 'nama_bidang' => 'Bidang Pengelolaan Arsip Dinamis'],
        ];
        $this->db->table('bidang')->ignore(true)->insertBatch($bidang);

        // ============================================================
        // 2. SEED TABEL: roles
        // ============================================================
        $roles = [
            ['id_role' => $idRole1, 'nama_role' => 'Admin_Pemkab'],
            ['id_role' => $idRole2, 'nama_role' => 'Pimpinan'],
            ['id_role' => $idRole3, 'nama_role' => 'Admin_OPD'],
            ['id_role' => $idRole4, 'nama_role' => 'Kepala_Bidang'],
            ['id_role' => $idRole5, 'nama_role' => 'Arsiparis'],
        ];
        $this->db->table('roles')->ignore(true)->insertBatch($roles);

        // ============================================================
        // 2.5 SEED TABEL: kode_klasifikasi
        // ============================================================
        $klasifikasi = [
            ['id_kode_klasifikasi' => $idKlasifikasi1, 'kode' => '000', 'nama_klasifikasi' => 'Umum', 'deskripsi' => 'Arsip Fasilitatif Umum', 'created_at' => date('Y-m-d H:i:s')],
            ['id_kode_klasifikasi' => $idKlasifikasi2, 'kode' => '100', 'nama_klasifikasi' => 'Pemerintahan', 'deskripsi' => 'Arsip Pemerintahan', 'created_at' => date('Y-m-d H:i:s')],
            ['id_kode_klasifikasi' => $idKlasifikasi3, 'kode' => '800', 'nama_klasifikasi' => 'Kepegawaian', 'deskripsi' => 'Arsip Kepegawaian', 'created_at' => date('Y-m-d H:i:s')],
        ];
        $this->db->table('kode_klasifikasi')->ignore(true)->insertBatch($klasifikasi);
        
        // ============================================================
        // 3. SEED TABEL: users
        // ============================================================
        $hashedPassword = password_hash('Admin123!', PASSWORD_DEFAULT);

        $users = [
            [
                'id_user'             => $idUser1,
                'email'               => 'admin.pemkab@siama.test',
                'password'            => $hashedPassword,
                'nama'                => 'Administrator Pemkab',
                'id_opd'              => null,
                'id_bidang'           => null,
                'is_active'           => 1,
                'is_default_password' => 1,
            ],
            [
                'id_user'             => $idUser2,
                'email'               => 'pimpinan@siama.test',
                'password'            => $hashedPassword,
                'nama'                => 'Kepala Dinas (Pimpinan)',
                'id_opd'              => $idOpdKominfo,
                'id_bidang'           => null,
                'is_active'           => 1,
                'is_default_password' => 1,
            ],
            [
                'id_user'             => $idUser3,
                'email'               => 'admin.opd@siama.test',
                'password'            => $hashedPassword,
                'nama'                => 'Administrator OPD',
                'id_opd'              => $idOpdKominfo,
                'id_bidang'           => null,
                'is_active'           => 1,
                'is_default_password' => 1,
            ],
            [
                'id_user'             => $idUser4,
                'email'               => 'kabid@siama.test',
                'password'            => $hashedPassword,
                'nama'                => 'Kepala Bidang',
                'id_opd'              => $idOpdKominfo,
                'id_bidang'           => $idBidangArsip,
                'is_active'           => 1,
                'is_default_password' => 1,
            ],
            [
                'id_user'             => $idUser5,
                'email'               => 'arsiparis@siama.test',
                'password'            => $hashedPassword,
                'nama'                => 'Budi Santoso (Arsiparis)',
                'id_opd'              => $idOpdKominfo,
                'id_bidang'           => $idBidangArsip,
                'is_active'           => 1,
                'is_default_password' => 1,
            ],
        ];
        $this->db->table('users')->ignore(true)->insertBatch($users);

        // ============================================================
        // 4. SEED TABEL: user_roles
        // ============================================================
        $userRoles = [
            ['id_user' => $idUser1, 'id_role' => $idRole1],
            ['id_user' => $idUser2, 'id_role' => $idRole2],
            ['id_user' => $idUser3, 'id_role' => $idRole3],
            ['id_user' => $idUser4, 'id_role' => $idRole4],
            ['id_user' => $idUser5, 'id_role' => $idRole5],
        ];
        $this->db->table('user_roles')->ignore(true)->insertBatch($userRoles);

        // ============================================================
        // 4.5 SEED TABEL: spt
        // ============================================================
        $spt = [
            [
                'id_spt' => $idSpt1,
                'id_opd' => $idOpdKominfo,
                'nomor_spt' => '090/01/SPT/KOMINFO/2026',
                'tanggal_spt' => '2026-01-10',
                'id_pimpinan' => $idUser2, // Kepala Dinas
                'perihal' => 'Tugas Alih Media Arsip Triwulan I',
                'tanggal_mulai' => '2026-01-15',
                'tanggal_selesai' => '2026-03-31',
                'status' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'id_spt' => $idSpt2,
                'id_opd' => $idOpdKominfo,
                'nomor_spt' => '090/02/SPT/KOMINFO/2026',
                'tanggal_spt' => '2026-04-05',
                'id_pimpinan' => $idUser2,
                'perihal' => 'Tugas Alih Media Arsip Kepegawaian',
                'tanggal_mulai' => '2026-04-10',
                'tanggal_selesai' => '2026-06-30',
                'status' => 'Aktif',
                'created_at' => date('Y-m-d H:i:s')
            ]
        ];
        $this->db->table('spt')->ignore(true)->insertBatch($spt);

        // ============================================================
        // 5. SEED TABEL: arsip (Data Dummy untuk testing Penilaian)
        // ============================================================
        $arsip = [
            [
                'id_arsip' => $this->uuidv4(),
                'id_spt' => $idSpt1,
                'id_opd' => $idOpdKominfo,
                'id_bidang' => $idBidangArsip,
                'id_kode_klasifikasi' => $idKlasifikasi1,
                'nomor_arsip' => '001/ARSIP/KOMINFO/2026',
                'nama_arsip' => 'Laporan Akhir Tahun 2025',
                'kurun_waktu' => '2025',
                'kondisi' => 'Baik',
                'id_user_upload' => $idUser5,
                'status_verifikasi' => 'Selesai',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $idUser5
            ],
            [
                'id_arsip' => $this->uuidv4(),
                'id_spt' => $idSpt1,
                'id_opd' => $idOpdKominfo,
                'id_bidang' => $idBidangArsip,
                'id_kode_klasifikasi' => $idKlasifikasi2,
                'nomor_arsip' => '002/ARSIP/KOMINFO/2026',
                'nama_arsip' => 'SK Kepala Dinas Kominfo 2026',
                'kurun_waktu' => '2026',
                'kondisi' => 'Baik',
                'id_user_upload' => $idUser5,
                'status_verifikasi' => 'Selesai',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $idUser5
            ],
            [
                'id_arsip' => $this->uuidv4(),
                'id_spt' => $idSpt2,
                'id_opd' => $idOpdKominfo,
                'id_bidang' => $idBidangArsip,
                'id_kode_klasifikasi' => $idKlasifikasi3,
                'nomor_arsip' => '003/ARSIP/KOMINFO/2026',
                'nama_arsip' => 'Daftar Aset Bidang SPBE',
                'kurun_waktu' => '2026',
                'kondisi' => 'Rusak Ringan',
                'id_user_upload' => $idUser5,
                'status_verifikasi' => 'Selesai',
                'created_at' => date('Y-m-d H:i:s'),
                'created_by' => $idUser5
            ]
        ];
        $this->db->table('arsip')->ignore(true)->insertBatch($arsip);
    }
}
