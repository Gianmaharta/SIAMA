<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

/**
 * ArsipSeeder
 *
 * Mengisi data dummy untuk testing Modul Pengelolaan Arsip Digital.
 * Mencakup: kode_klasifikasi, spt, dan arsip (dengan variasi status & kondisi).
 *
 * Cara menjalankan:
 *   php spark db:seed ArsipSeeder
 *
 * Pastikan SiamaSeeder sudah dijalankan terlebih dahulu (agar user, opd, bidang tersedia).
 */
class ArsipSeeder extends Seeder
{
    public function run()
    {
        // ============================================================
        // 1. SEED: kode_klasifikasi
        // ============================================================
        $klasifikasi = [
            [
                'id_klasifikasi'  => 1,
                'kode'            => 'PD',
                'nama_klasifikasi'=> 'Pemerintahan Daerah',
                'retensi_aktif'   => 5,
                'retensi_inaktif' => 10,
            ],
            [
                'id_klasifikasi'  => 2,
                'kode'            => 'KU',
                'nama_klasifikasi'=> 'Keuangan',
                'retensi_aktif'   => 10,
                'retensi_inaktif' => 15,
            ],
            [
                'id_klasifikasi'  => 3,
                'kode'            => 'KP',
                'nama_klasifikasi'=> 'Kepegawaian',
                'retensi_aktif'   => 5,
                'retensi_inaktif' => 10,
            ],
            [
                'id_klasifikasi'  => 4,
                'kode'            => 'TI',
                'nama_klasifikasi'=> 'Teknologi Informasi',
                'retensi_aktif'   => 3,
                'retensi_inaktif' => 7,
            ],
        ];
        $this->db->table('kode_klasifikasi')->ignore(true)->insertBatch($klasifikasi);

        // ============================================================
        // 2. SEED: spt (Surat Perintah Tugas) — untuk dropdown arsip
        // ============================================================
        $spt = [
            [
                'id_spt'          => 1,
                'id_opd'          => 1,
                'nomor_spt'       => 'SPT/001/IX/2026',
                'tanggal_spt'     => '2026-09-01',
                'id_pimpinan'     => 2,
                'perihal'         => 'Alih Media Arsip Kepegawaian Tahun 2022-2023',
                'tanggal_mulai'   => '2026-09-01',
                'tanggal_selesai' => '2026-09-30',
                'status'          => 'Aktif',
                'file_spt'        => null,
            ],
            [
                'id_spt'          => 2,
                'id_opd'          => 1,
                'nomor_spt'       => 'SPT/002/IX/2026',
                'tanggal_spt'     => '2026-09-05',
                'id_pimpinan'     => 2,
                'perihal'         => 'Alih Media Arsip Keuangan Semester I 2025',
                'tanggal_mulai'   => '2026-09-05',
                'tanggal_selesai' => '2026-09-25',
                'status'          => 'Aktif',
                'file_spt'        => null,
            ],
        ];
        $this->db->table('spt')->ignore(true)->insertBatch($spt);

        // ============================================================
        // 3. SEED: arsip — data dummy dengan variasi lengkap
        // ============================================================
        $arsip = [
            [
                'id_arsip'           => 1,
                'id_spt'             => 1,
                'id_opd'             => 1,
                'id_bidang'          => 1,
                'id_klasifikasi'     => 3,
                'nomor_arsip'        => 'ARS/KP/2026/001',
                'nama_arsip'         => 'SK Pengangkatan PNS Periode 2022',
                'tahun_penciptaan'   => 2022,
                'kategori_jra'       => 'Permanen',
                'kondisi_fisik'      => 'Baik',
                'metode_alih_media'  => 'Scan',
                'skor_prioritas'     => 85,
                'file_digital'       => null,
                'status_autentikasi' => 'Belum Watermark',
                'status_alih_media'  => 'Belum Diajukan',
                'created_by'         => 5,
            ],
            [
                'id_arsip'           => 2,
                'id_spt'             => 1,
                'id_opd'             => 1,
                'id_bidang'          => 1,
                'id_klasifikasi'     => 3,
                'nomor_arsip'        => 'ARS/KP/2026/002',
                'nama_arsip'         => 'Daftar Nominatif Pegawai Tahun 2023',
                'tahun_penciptaan'   => 2023,
                'kategori_jra'       => 'Permanen',
                'kondisi_fisik'      => 'Rusak Ringan',
                'metode_alih_media'  => 'Scan',
                'skor_prioritas'     => 70,
                'file_digital'       => null,
                'status_autentikasi' => 'Sudah Watermark',
                'status_alih_media'  => 'Diajukan',
                'created_by'         => 5,
            ],
            [
                'id_arsip'           => 3,
                'id_spt'             => 2,
                'id_opd'             => 1,
                'id_bidang'          => 1,
                'id_klasifikasi'     => 2,
                'nomor_arsip'        => 'ARS/KU/2026/001',
                'nama_arsip'         => 'Laporan Keuangan Semester I Tahun 2025',
                'tahun_penciptaan'   => 2025,
                'kategori_jra'       => 'Musnah',
                'kondisi_fisik'      => 'Baik',
                'metode_alih_media'  => 'Digitalisasi',
                'skor_prioritas'     => 60,
                'file_digital'       => null,
                'status_autentikasi' => 'Terautentikasi',
                'status_alih_media'  => 'Disetujui',
                'created_by'         => 5,
            ],
        ];
        $this->db->table('arsip')->ignore(true)->insertBatch($arsip);

        echo "ArsipSeeder: Data dummy kode_klasifikasi, spt, dan arsip berhasil diinsert.\n";
    }
}
