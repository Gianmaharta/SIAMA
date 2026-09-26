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
        $nama_role = $session->get('nama_role');
        $id_opd   = $session->get('id_opd');
        $id_bidang = $session->get('id_bidang');
        $user_id  = $session->get('user_id');

        switch ($nama_role) {
            case 'Admin_Pemkab': // Admin_Pemkab: semua arsip se-Kabupaten
                $arsip_list = $this->arsipModel->getArsipWithRelations();
                break;

            case 'Pimpinan': // Pimpinan: semua arsip di OPD-nya
            case 'Admin_OPD': // Admin_OPD: semua arsip di OPD-nya
                $arsip_list = $this->arsipModel->getArsipWithRelations(null, $id_opd);
                break;

            case 'Kepala_Bidang': // Kepala Bidang: arsip di Bidangnya
                $arsip_list = $this->arsipModel->getArsipWithRelations(null, $id_opd, $id_bidang);
                break;

            case 'Arsiparis': // Arsiparis: hanya arsip yang dia buat (created_by)
                $arsip_list = $this->arsipModel
                    ->select('arsip.*, opd.nama_opd, bidang.nama_bidang, kode_klasifikasi.kode AS kode_klasifikasi_text, kode_klasifikasi.nama_klasifikasi, spt.nomor_spt')
                    ->join('opd',              'opd.id_opd = arsip.id_opd',                             'left')
                    ->join('bidang',           'bidang.id_bidang = arsip.id_bidang',                    'left')
                    ->join('kode_klasifikasi', 'kode_klasifikasi.id_kode_klasifikasi = arsip.id_kode_klasifikasi', 'left') // fix join condition while we're at it
                    ->join('spt',              'spt.id_spt = arsip.id_spt',                              'left')
                    ->where('arsip.created_by', $user_id)
                    ->orderBy('arsip.created_at', 'DESC')
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
        $user_id = $session->get('user_id');

        // Ambil daftar SPT Aktif yang ditugaskan secara spesifik kepada Arsiparis yang sedang login
        $spt_list = $this->sptModel->getSptByPelaksana($user_id);


        // Ambil daftar Bidang dalam OPD user (untuk dropdown)
        $bidang_list = $this->bidangModel
            ->where('id_opd', $id_opd)
            ->findAll();

        // Ambil seluruh Kode Klasifikasi (master data)
        $klasifikasi_list = $this->db->table('kode_klasifikasi')
            ->select('id_kode_klasifikasi, kode, nama_klasifikasi')
            ->orderBy('kode', 'ASC')
            ->get()
            ->getResultArray();

        $id_spt_query = $this->request->getGet('id_spt');
        $spt_terpilih = null;
        if (!empty($id_spt_query)) {
            $spt_terpilih = $this->sptModel->find($id_spt_query);
        }

        return view('arsip/create', [
            'title'            => 'Tambah Arsip Baru',
            'spt_list'         => $spt_list,
            'bidang_list'      => $bidang_list,
            'klasifikasi_list' => $klasifikasi_list,
            'spt_terpilih'     => $spt_terpilih,
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
        $id_opd    = $session->get('id_opd');
        $id_bidang = $session->get('id_bidang');
        $user_id   = $session->get('user_id');

        // ----- Validasi Input Form -----
        if (! $this->validate([
            'nomor_arsip'      => 'required|max_length[100]',
            'nama_arsip'       => 'required|max_length[255]',
            'id_kode_klasifikasi' => 'required',
            'id_bidang'        => 'required',
            'kurun_waktu'      => 'permit_empty|max_length[50]',
            'jumlah'           => 'permit_empty|numeric',
            'kondisi'          => 'permit_empty|max_length[50]',
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
            
            // Proses Watermark berdasarkan pilihan radio button
            $watermarkChoice = $this->request->getPost('watermark_source') ?? 'none';
            if ($watermarkChoice === 'system' && $ext === 'pdf') {
                $watermarkService = new \App\Services\WatermarkService();
                $opd_name = 'DINAS/OPD TERKAIT'; // Default
                // Cari nama OPD dari database
                $db = \Config\Database::connect();
                $opd_row = $db->table('opd')->where('id_opd', $id_opd)->get()->getRow();
                if ($opd_row) {
                    $opd_name = $opd_row->nama_opd;
                }
                
                $fullPath = self::UPLOAD_PATH . $fileDigitalName;
                $watermarkedPath = self::UPLOAD_PATH . 'wm_' . $fileDigitalName;
                
                if ($watermarkService->generateWatermark($fullPath, $watermarkedPath, $opd_name)) {
                    // Hapus file asli dan ganti nama file
                    unlink($fullPath);
                    $fileDigitalName = 'wm_' . $fileDigitalName;
                    $is_watermarked = 1;
                    $watermark_source = 'system';
                }
            } elseif ($watermarkChoice === 'offline') {
                $watermark_source = 'offline';
            }
        }

        $is_watermarked = $is_watermarked ?? 0;
        $watermark_source = $watermark_source ?? ($this->request->getPost('watermark_source') ?? 'none');
        
        // Hitung JRA
        $retensi_aktif = (int) $this->request->getPost('retensi_aktif');
        $tanggal_retensi_aktif_berakhir = null;
        if ($retensi_aktif > 0) {
            $tanggal_retensi_aktif_berakhir = date('Y-m-d', strtotime("+$retensi_aktif years"));
        }

        // ----- Susun Data untuk Disimpan -----
        $data = [
            'id_spt'            => $this->request->getPost('id_spt')          ?: null,
            'id_opd'            => $id_opd,
            'id_bidang'         => $this->request->getPost('id_bidang'),
            'id_kode_klasifikasi' => $this->request->getPost('id_kode_klasifikasi'),
            'nomor_arsip'       => $this->request->getPost('nomor_arsip'),
            'nama_arsip'        => $this->request->getPost('nama_arsip'),
            'kurun_waktu'       => $this->request->getPost('kurun_waktu') ?: null,
            'jumlah'            => (int) ($this->request->getPost('jumlah') ?: 0),
            'kondisi'           => $this->request->getPost('kondisi') ?: 'Baik',
            'file_arsip'        => $fileDigitalName,
            'status_verifikasi' => 'Menunggu',
            'status_autentikasi' => 'Belum Dinilai',
            'created_by'        => $user_id,
            'id_user_upload'    => $user_id,
            'tanggal_retensi_aktif_berakhir' => $tanggal_retensi_aktif_berakhir,
            'status_retensi_aktif'           => 'Aktif',
            'is_watermarked'                 => $is_watermarked,
            'watermark_source'               => $watermark_source,
        ];

        // Nonaktifkan validasi model sementara (sudah divalidasi manual di atas)
        $this->arsipModel->skipValidation(true)->insert($data);

        if ($this->arsipModel->errors()) {
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan data arsip. ' . implode(', ', $this->arsipModel->errors()));
        }

        // Tandai SPT selesai jika terkait dengan penugasan
        $id_spt_assigned = $this->request->getPost('id_spt');
        if (!empty($id_spt_assigned)) {
            $this->sptModel->update($id_spt_assigned, ['status_penugasan' => 'selesai']);
            
            $assignmentModel = new \App\Models\SptAssignmentModel();
            $assignment = $assignmentModel->where('id_spt', $id_spt_assigned)
                                          ->where('id_user', $user_id)
                                          ->first();
            if ($assignment) {
                $assignmentModel->update($assignment['id_assignment'], ['status' => 'selesai']);
            }
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
    public function detail($id)
    {
        $arsip = $this->arsipModel->getArsipWithRelations($id);

        if (! $arsip) {
            return redirect()->to('/arsip')->with('error', 'Data arsip tidak ditemukan.');
        }

        // Susun URL berkas (jika ada)
        $file_url = null;
        if (! empty($arsip['file_arsip'])) {
            $file_url = base_url(self::UPLOAD_URL . $arsip['file_arsip']);
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
    public function edit($id)
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
            ->select('id_kode_klasifikasi, kode, nama_klasifikasi')
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
    public function update($id)
    {
        $arsip = $this->arsipModel->find($id);

        if (! $arsip) {
            return redirect()->to('/arsip')->with('error', 'Data arsip tidak ditemukan.');
        }

        // ----- Validasi Input Form -----
        if (! $this->validate([
            'nomor_arsip'      => 'required|max_length[100]',
            'nama_arsip'       => 'required|max_length[255]',
            'id_kode_klasifikasi' => 'required',
            'id_bidang'        => 'required',
            'kurun_waktu'      => 'permit_empty|max_length[50]',
            'jumlah'           => 'permit_empty|numeric',
            'kondisi'          => 'permit_empty|max_length[50]',
        ])) {
            return redirect()->back()->withInput()->with('error', implode('<br>', $this->validator->getErrors()));
        }

        // ----- Proses File Baru (jika ada) -----
        $fileDigitalName = $arsip['file_arsip']; // Pertahankan file lama sebagai default
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
            if (! empty($arsip['file_arsip'])) {
                $oldFilePath = self::UPLOAD_PATH . $arsip['file_arsip'];
                if (file_exists($oldFilePath)) {
                    unlink($oldFilePath);
                }
            }

            // Simpan file baru
            $fileDigitalName = $fileObj->getRandomName();
            $fileObj->move(self::UPLOAD_PATH, $fileDigitalName);
        }
        
        $is_watermarked = $arsip['is_watermarked'];
        $watermark_source = $this->request->getPost('watermark_source') ?? ($arsip['watermark_source'] ?? 'none');

        // Proses Watermark berdasarkan pilihan radio button
        if ($watermark_source === 'system') {
            $ext = pathinfo($fileDigitalName, PATHINFO_EXTENSION);
            if (strtolower($ext) === 'pdf') {
                $watermarkService = new \App\Services\WatermarkService();
                $opd_name = 'DINAS/OPD TERKAIT';
                $db = \Config\Database::connect();
                $opd_row = $db->table('opd')->where('id_opd', $arsip['id_opd'])->get()->getRow();
                if ($opd_row) {
                    $opd_name = $opd_row->nama_opd;
                }
                
                $fullPath = self::UPLOAD_PATH . $fileDigitalName;
                $watermarkedPath = self::UPLOAD_PATH . 'wm_' . basename($fileDigitalName);
                
                if ($watermarkService->generateWatermark($fullPath, $watermarkedPath, $opd_name)) {
                    if (file_exists($fullPath)) {
                        unlink($fullPath);
                    }
                    $fileDigitalName = 'wm_' . basename($fileDigitalName);
                    $is_watermarked = 1;
                }
            }
        } elseif ($watermark_source === 'offline') {
            // Tandai sebagai offline watermark (tidak generate watermark sistem)
        } elseif ($watermark_source === 'none') {
            $is_watermarked = 0;
        }
        
        // Hitung JRA
        $retensi_aktif = (int) $this->request->getPost('retensi_aktif');
        $tanggal_retensi_aktif_berakhir = $arsip['tanggal_retensi_aktif_berakhir'];
        if ($retensi_aktif > 0) {
            $tanggal_retensi_aktif_berakhir = date('Y-m-d', strtotime("+$retensi_aktif years"));
        }

        // ----- Susun Data Update -----
        $data = [
            'id_spt'             => $this->request->getPost('id_spt')           ?: null,
            'id_bidang'          => $this->request->getPost('id_bidang'),
            'id_kode_klasifikasi'  => $this->request->getPost('id_kode_klasifikasi'),
            'nomor_arsip'        => $this->request->getPost('nomor_arsip'),
            'nama_arsip'         => $this->request->getPost('nama_arsip'),
            'kurun_waktu'        => $this->request->getPost('kurun_waktu') ?: null,
            'jumlah'             => (int) ($this->request->getPost('jumlah') ?: 0),
            'kondisi'            => $this->request->getPost('kondisi') ?: 'Baik',
            'file_arsip'         => $fileDigitalName,
            'status_verifikasi'  => $this->request->getPost('status_verifikasi') ?: $arsip['status_verifikasi'],
            'tanggal_retensi_aktif_berakhir' => $tanggal_retensi_aktif_berakhir,
            'is_watermarked'     => $is_watermarked,
            'watermark_source'   => $watermark_source,
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
    public function delete($id)
    {
        $arsip = $this->arsipModel->find($id);

        if (! $arsip) {
            return redirect()->to('/arsip')->with('error', 'Data arsip tidak ditemukan.');
        }

        // Catatan: Sesuai aturan sistem, file PDF fisik TIDAK dihapus dari server
        // untuk menjaga jejak audit dan pelestarian berkas digital.
        // Hanya record di database yang akan dihapus.

        // Cek jika terkait SPT, kembalikan status penugasannya
        if (!empty($arsip['id_spt'])) {
            $sptModel = new \App\Models\SptModel();
            $sptAssignmentModel = new \App\Models\SptAssignmentModel();
            
            // Kembalikan SPT ke status ditugaskan (sehingga muncul lagi di dropdown)
            $sptModel->update($arsip['id_spt'], ['status_penugasan' => 'ditugaskan']);
            
            // Kembalikan status penugasan user ke pending
            $assignment = $sptAssignmentModel->where('id_spt', $arsip['id_spt'])
                                             ->where('id_user', $arsip['created_by'])
                                             ->first();
            if ($assignment) {
                $sptAssignmentModel->update($assignment['id_assignment'], ['status' => 'pending']);
            }
        }

        // Hapus record dari database
        $this->arsipModel->delete($id);

        return redirect()->to('/arsip')->with('success', 'Data arsip berhasil dihapus secara permanen.');
    }

    // =========================================================================
    // verify() — Verifikasi Arsip oleh Kepala Bidang
    // =========================================================================

    public function verify($id)
    {
        $session = session();
        if ($session->get('nama_role') !== 'Kepala_Bidang') {
            return redirect()->to('/arsip')->with('error', 'Akses ditolak. Hanya Kepala Bidang yang dapat memverifikasi arsip.');
        }

        $arsip = $this->arsipModel->find($id);

        if (! $arsip) {
            return redirect()->to('/arsip')->with('error', 'Arsip tidak ditemukan.');
        }

        if ($arsip['status_verifikasi'] !== 'Menunggu') {
            return redirect()->to('/arsip')->with('error', 'Arsip ini sudah diverifikasi.');
        }

        // Pastikan Kabid hanya bisa memverifikasi arsip di OPD-nya
        if ($arsip['id_opd'] != $session->get('id_opd')) {
            return redirect()->to('/arsip')->with('error', 'Anda tidak berhak memverifikasi arsip dari OPD lain.');
        }

        $this->arsipModel->update($id, ['status_verifikasi' => 'Selesai']);

        return redirect()->to('/arsip')->with('success', 'Arsip berhasil diverifikasi.');
    }
}
