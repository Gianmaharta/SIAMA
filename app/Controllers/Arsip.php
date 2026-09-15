<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\ArsipModel;
use App\Models\BidangModel;
use App\Models\SptModel;
use CodeIgniter\Database\BaseConnection;

/**
 * Controller: Arsip
 *
 * Mengelola seluruh operasi CRUD untuk modul Pengelolaan Arsip Digital.
 *
 * Hak Akses Role:
 *   1 (Admin_Pemkab) : Dapat melihat seluruh arsip se-Kabupaten.
 *   2 (Pimpinan)     : Dapat melihat rekap & detail arsip tingkat OPD.
 *   3 (Admin_OPD)    : Dapat melihat daftar dan detail arsip di OPD-nya.
 *   4 (Kepala Bidang): Dapat melihat daftar dan detail arsip di Bidangnya.
 *   5 (Arsiparis)    : Dapat mengunggah, mengubah, dan melihat arsip miliknya.
 */
class Arsip extends BaseController
{
    protected ArsipModel $arsipModel;
    protected BidangModel $bidangModel;
    protected SptModel $sptModel;
    protected BaseConnection $db;

    /** Direktori penyimpanan file digital arsip (relatif terhadap FCPATH/public) */
    private const UPLOAD_PATH = FCPATH . 'uploads/arsip/';

    /** URL path untuk mengakses file (relatif terhadap base_url) */
    private const UPLOAD_URL  = 'uploads/arsip/';

    /** Ekstensi file yang diperbolehkan */
    private const ALLOWED_TYPES = 'pdf,jpg,jpeg,png,tiff,tif';

    /** Ukuran maksimum file: 10 MB dalam KB */
    private const MAX_FILE_SIZE_KB = 10240;

    public function __construct()
    {
        $this->db          = \Config\Database::connect();
        $this->arsipModel  = new ArsipModel();
        $this->bidangModel = new BidangModel();
        $this->sptModel    = new SptModel();

        // Pastikan folder upload tersedia
        if (! is_dir(self::UPLOAD_PATH)) {
            mkdir(self::UPLOAD_PATH, 0755, true);
        }
    }

    // =========================================================================
    // index() — Daftar Arsip
    // =========================================================================

    /**
     * Menampilkan daftar arsip berdasarkan filter role pengguna yang sedang login.
     */
    public function index()
    {
        $session  = session();
        $id_role  = (int) $session->get('id_role');
        $id_opd   = $session->get('id_opd');
        $id_bidang = $session->get('id_bidang');
        $user_id  = (int) $session->get('user_id');

        switch ($id_role) {
            case 1: // Admin_Pemkab: semua arsip se-Kabupaten
                $arsip_list = $this->arsipModel->getArsipWithRelations();
                break;

            case 2: // Pimpinan: semua arsip di OPD-nya
            case 3: // Admin_OPD: semua arsip di OPD-nya
                $arsip_list = $this->arsipModel->getArsipWithRelations(null, $id_opd);
                break;

            case 4: // Kepala Bidang: arsip di Bidangnya
                $arsip_list = $this->arsipModel->getArsipWithRelations(null, $id_opd, $id_bidang);
                break;

            case 5: // Arsiparis: hanya arsip yang dia buat (created_by)
                $arsip_list = $this->arsipModel
                    ->select('arsip.*, opd.nama_opd, bidang.nama_bidang, kode_klasifikasi.kode AS kode_klasifikasi, kode_klasifikasi.nama_klasifikasi, spt.nomor_spt')
                    ->join('opd',              'opd.id_opd = arsip.id_opd',                             'left')
                    ->join('bidang',           'bidang.id_bidang = arsip.id_bidang',                    'left')
                    ->join('kode_klasifikasi', 'kode_klasifikasi.id_klasifikasi = arsip.id_klasifikasi', 'left')
                    ->join('spt',              'spt.id_spt = arsip.id_spt',                              'left')
                    ->where('arsip.created_by', $user_id)
                    ->orderBy('arsip.id_arsip', 'DESC')
                    ->findAll();
                break;

            default:
                $arsip_list = [];
                break;
        }

        return view('arsip/index', [
            'arsip_list' => $arsip_list,
            'title'      => 'Daftar Arsip Digital',
        ]);
    }

    // =========================================================================
    // create() — Form Tambah Arsip
    // =========================================================================

    /**
     * Menampilkan form untuk menambah arsip baru.
     * Hanya untuk Arsiparis (id_role: 5) — dijaga filter route.
     */
    public function create()
    {
        $session = session();
        $id_opd  = $session->get('id_opd');
        $user_id = (int) $session->get('user_id');

        // Ambil daftar SPT Aktif yang terkait dengan OPD user (atau SPT yang user terlibat sebagai pelaksana)
        $spt_list = $this->sptModel
            ->where('id_opd', $id_opd)
            ->where('status', 'Aktif')
            ->findAll();

        // Ambil daftar Bidang dalam OPD user (untuk dropdown)
        $bidang_list = $this->bidangModel
            ->where('id_opd', $id_opd)
            ->findAll();

        // Ambil seluruh Kode Klasifikasi (master data)
        $klasifikasi_list = $this->db->table('kode_klasifikasi')
            ->select('id_klasifikasi, kode, nama_klasifikasi')
            ->orderBy('kode', 'ASC')
            ->get()
            ->getResultArray();

        return view('arsip/create', [
            'title'            => 'Tambah Arsip Baru',
            'spt_list'         => $spt_list,
            'bidang_list'      => $bidang_list,
            'klasifikasi_list' => $klasifikasi_list,
        ]);
    }

    // =========================================================================
    // store() — Simpan Arsip Baru
    // =========================================================================

    /**
     * Memproses penyimpanan data arsip baru beserta file digital-nya.
     * Hanya untuk Arsiparis (id_role: 5) — dijaga filter route.
     */
    public function store()
    {
        $session   = session();
        $id_opd    = (int) $session->get('id_opd');
        $id_bidang = (int) $session->get('id_bidang');
        $user_id   = (int) $session->get('user_id');

        // ----- Validasi Input Form -----
        if (! $this->validate([
            'nomor_arsip'      => 'required|max_length[100]',
            'nama_arsip'       => 'required|max_length[255]',
            'id_klasifikasi'   => 'required|numeric',
            'id_bidang'        => 'required|numeric',
            'tahun_penciptaan' => 'permit_empty|numeric',
            'skor_prioritas'   => 'permit_empty|numeric',
        ])) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        // ----- Validasi & Upload File Digital -----
        $fileDigitalName = null;
        $fileObj = $this->request->getFile('file_arsip');

        if ($fileObj !== null && $fileObj->isValid() && ! $fileObj->hasMoved()) {
            // Cek ekstensi
            $ext = strtolower($fileObj->getClientExtension());
            $allowedExt = explode(',', self::ALLOWED_TYPES);

            if (! in_array($ext, $allowedExt)) {
                return redirect()->back()->withInput()->with('error', 'Format file tidak diizinkan. Gunakan: PDF, JPG, JPEG, PNG, TIFF.');
            }

            // Cek ukuran (dalam bytes)
            if ($fileObj->getSize() > self::MAX_FILE_SIZE_KB * 1024) {
                return redirect()->back()->withInput()->with('error', 'Ukuran file melebihi batas maksimum 10 MB.');
            }

            // Generate nama file unik & pindahkan ke folder upload
            $fileDigitalName = $fileObj->getRandomName();
            $fileObj->move(self::UPLOAD_PATH, $fileDigitalName);
        }

        // ----- Susun Data untuk Disimpan -----
        $data = [
            'id_spt'            => $this->request->getPost('id_spt')          ?: null,
            'id_opd'            => $id_opd,
            'id_bidang'         => (int) $this->request->getPost('id_bidang'),
            'id_klasifikasi'    => (int) $this->request->getPost('id_klasifikasi'),
            'nomor_arsip'       => $this->request->getPost('nomor_arsip'),
            'nama_arsip'        => $this->request->getPost('nama_arsip'),
            'tahun_penciptaan'  => $this->request->getPost('tahun_penciptaan') ?: null,
            'kategori_jra'      => $this->request->getPost('kategori_jra')     ?: '',
            'kondisi_fisik'     => $this->request->getPost('kondisi_fisik')    ?: 'Baik',
            'metode_alih_media' => $this->request->getPost('metode_alih_media') ?: 'Scan',
            'skor_prioritas'    => (int) ($this->request->getPost('skor_prioritas') ?: 0),
            'file_digital'      => $fileDigitalName,
            'status_autentikasi' => 'Belum Watermark',
            'status_alih_media'  => 'Belum Diajukan',
            'created_by'        => $user_id,
        ];

        // Nonaktifkan validasi model sementara (sudah divalidasi manual di atas)
        $this->arsipModel->skipValidation(true)->insert($data);

        if ($this->arsipModel->errors()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data arsip. ' . implode(', ', $this->arsipModel->errors()));
        }

        return redirect()->to('/arsip')->with('success', 'Arsip berhasil ditambahkan.');
    }

    // =========================================================================
    // detail() — Detail Arsip
    // =========================================================================

    /**
     * Menampilkan halaman rincian satu arsip beserta link berkas digitalnya.
     *
     * @param int $id id_arsip
     */
    public function detail(int $id)
    {
        $arsip = $this->arsipModel->getArsipWithRelations($id);

        if (! $arsip) {
            return redirect()->to('/arsip')->with('error', 'Data arsip tidak ditemukan.');
        }

        // Susun URL berkas (jika ada)
        $file_url = null;
        if (! empty($arsip['file_digital'])) {
            $file_url = base_url(self::UPLOAD_URL . $arsip['file_digital']);
        }

        return view('arsip/detail', [
            'title'    => 'Detail Arsip',
            'arsip'    => $arsip,
            'file_url' => $file_url,
        ]);
    }

    // =========================================================================
    // edit() — Form Edit Arsip
    // =========================================================================

    /**
     * Menampilkan form untuk mengubah data arsip yang sudah ada.
     * Hanya untuk Arsiparis (id_role: 5) — dijaga filter route.
     *
     * @param int $id id_arsip
     */
    public function edit(int $id)
    {
        $session = session();
        $id_opd  = $session->get('id_opd');

        $arsip = $this->arsipModel->find($id);

        if (! $arsip) {
            return redirect()->to('/arsip')->with('error', 'Data arsip tidak ditemukan.');
        }

        // Ambil data dropdown (sama dengan create)
        $spt_list = $this->sptModel
            ->where('id_opd', $id_opd)
            ->where('status', 'Aktif')
            ->findAll();

        $bidang_list = $this->bidangModel
            ->where('id_opd', $id_opd)
            ->findAll();

        $klasifikasi_list = $this->db->table('kode_klasifikasi')
            ->select('id_klasifikasi, kode, nama_klasifikasi')
            ->orderBy('kode', 'ASC')
            ->get()
            ->getResultArray();

        return view('arsip/edit', [
            'title'            => 'Edit Data Arsip',
            'arsip'            => $arsip,
            'spt_list'         => $spt_list,
            'bidang_list'      => $bidang_list,
            'klasifikasi_list' => $klasifikasi_list,
        ]);
    }

    // =========================================================================
    // update() — Simpan Perubahan Arsip
    // =========================================================================

    /**
     * Memproses pembaruan data arsip.
     * Jika ada file baru yang diunggah, file lama akan dihapus dan diganti.
     * Hanya untuk Arsiparis (id_role: 5) — dijaga filter route.
     *
     * @param int $id id_arsip
     */
    public function update(int $id)
    {
        $arsip = $this->arsipModel->find($id);

        if (! $arsip) {
            return redirect()->to('/arsip')->with('error', 'Data arsip tidak ditemukan.');
        }

        // ----- Validasi Input Form -----
        if (! $this->validate([
            'nomor_arsip'      => 'required|max_length[100]',
            'nama_arsip'       => 'required|max_length[255]',
            'id_klasifikasi'   => 'required|numeric',
            'id_bidang'        => 'required|numeric',
            'tahun_penciptaan' => 'permit_empty|numeric',
            'skor_prioritas'   => 'permit_empty|numeric',
        ])) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        // ----- Proses File Baru (jika ada) -----
        $fileDigitalName = $arsip['file_digital']; // Pertahankan file lama sebagai default
        $fileObj = $this->request->getFile('file_arsip');

        if ($fileObj !== null && $fileObj->isValid() && ! $fileObj->hasMoved()) {
            $ext = strtolower($fileObj->getClientExtension());
            $allowedExt = explode(',', self::ALLOWED_TYPES);

            if (! in_array($ext, $allowedExt)) {
                return redirect()->back()->withInput()->with('error', 'Format file tidak diizinkan. Gunakan: PDF, JPG, JPEG, PNG, TIFF.');
            }

            if ($fileObj->getSize() > self::MAX_FILE_SIZE_KB * 1024) {
                return redirect()->back()->withInput()->with('error', 'Ukuran file melebihi batas maksimum 10 MB.');
            }

            // Hapus file lama jika ada
            if (! empty($arsip['file_digital'])) {
                $oldFilePath = self::UPLOAD_PATH . $arsip['file_digital'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Simpan file baru
            $fileDigitalName = $fileObj->getRandomName();
            $fileObj->move(self::UPLOAD_PATH, $fileDigitalName);
        }

        // ----- Susun Data Update -----
        $data = [
            'id_spt'             => $this->request->getPost('id_spt')           ?: null,
            'id_bidang'          => (int) $this->request->getPost('id_bidang'),
            'id_klasifikasi'     => (int) $this->request->getPost('id_klasifikasi'),
            'nomor_arsip'        => $this->request->getPost('nomor_arsip'),
            'nama_arsip'         => $this->request->getPost('nama_arsip'),
            'tahun_penciptaan'   => $this->request->getPost('tahun_penciptaan')  ?: null,
            'kategori_jra'       => $this->request->getPost('kategori_jra')      ?: '',
            'kondisi_fisik'      => $this->request->getPost('kondisi_fisik')     ?: 'Baik',
            'metode_alih_media'  => $this->request->getPost('metode_alih_media') ?: 'Scan',
            'skor_prioritas'     => (int) ($this->request->getPost('skor_prioritas') ?: 0),
            'file_digital'       => $fileDigitalName,
            'status_autentikasi' => $this->request->getPost('status_autentikasi') ?: $arsip['status_autentikasi'],
            'status_alih_media'  => $this->request->getPost('status_alih_media')  ?: $arsip['status_alih_media'],
        ];

        $this->arsipModel->skipValidation(true)->update($id, $data);

        return redirect()->to('/arsip')->with('success', 'Data arsip berhasil diperbarui.');
    }

    // =========================================================================
    // delete() — Hapus Arsip
    // =========================================================================

    /**
     * Menghapus data arsip beserta file fisik digitalnya dari server.
     * Hanya untuk Arsiparis (id_role: 5) — dijaga filter route.
     *
     * @param int $id id_arsip
     */
    public function delete(int $id)
    {
        $arsip = $this->arsipModel->find($id);

        if (! $arsip) {
            return redirect()->to('/arsip')->with('error', 'Data arsip tidak ditemukan.');
        }

        // Hapus file fisik dari server (jika ada)
        if (! empty($arsip['file_digital'])) {
            $filePath = self::UPLOAD_PATH . $arsip['file_digital'];
            if (file_exists($filePath)) {
                unlink($filePath);
            }
        }

        // Hapus record dari database
        $this->arsipModel->delete($id);

        return redirect()->to('/arsip')->with('success', 'Data arsip berhasil dihapus.');
    }
}
