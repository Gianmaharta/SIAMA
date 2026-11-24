<?php

namespace App\Controllers;

use App\Models\JraModel;
use App\Models\KodeKlasifikasiModel;

class Jra extends BaseController
{
    protected $jraModel;
    protected $klasifikasiModel;
    protected $db;

    public function __construct()
    {
        $this->jraModel = new JraModel();
        // Menggunakan query builder untuk klasifikasi karena mungkin belum ada modelnya
        $this->db = \Config\Database::connect();
    }

    public function index()
    {
        $session = session();
        if (!in_array($session->get('nama_role'), ['Super_Admin', 'Admin_Pemkab', 'Admin_OPD', 'Administrator OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $perPage  = max(1, min(100, (int) ($this->request->getGet('per_page') ?: 25)));
        $page     = max(1, (int) ($this->request->getGet('page') ?: 1));
        $offset   = ($page - 1) * $perPage;
        $totalJra = (int) $this->jraModel->countAllResults();
        $jraList  = $this->jraModel->getJraWithKlasifikasi(null, $perPage, $offset);

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'     => 'success',
                'total_data' => $totalJra,
                'page'       => $page,
                'per_page'   => $perPage,
                'data'       => $jraList,
            ]);
        }

        $data['title']     = 'Master Jadwal Retensi Arsip (JRA)';
        $data['jra_list']  = $jraList;
        $data['total_jra'] = $totalJra;

        return view('jra/index', $data);
    }

    /**
     * Endpoint Server-Side DataTables untuk Master JRA (GET /jra/datatable)
     */
    public function datatable()
    {
        $session = session();
        if (!in_array($session->get('nama_role'), ['Super_Admin', 'Admin_Pemkab', 'Admin_OPD', 'Administrator OPD'])) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses ditolak.'])->setStatusCode(403);
        }

        $draw   = (int) ($this->request->getGet('draw') ?? 1);
        $length = (int) ($this->request->getGet('length') ?: $this->request->getGet('per_page') ?: 25);
        $length = max(1, min(100, $length));

        if ($this->request->getGet('start') !== null) {
            $start = max(0, (int) $this->request->getGet('start'));
        } else {
            $page  = max(1, (int) ($this->request->getGet('page') ?: 1));
            $start = ($page - 1) * $length;
        }

        $searchParam = $this->request->getGet('search');
        $search = is_array($searchParam)
            ? trim((string) ($searchParam['value'] ?? ''))
            : trim((string) ($searchParam ?? ''));

        $colMap = [
            '0'               => 'kode_klasifikasi.kode',
            '1'               => 'kode_klasifikasi.kode',
            '2'               => 'jra.retensi_aktif',
            '3'               => 'jra.retensi_inaktif',
            '4'               => 'jra.keterangan_retensi',
            '5'               => 'jra.klasifikasi_keamanan',
            '6'               => 'jra.dasar_pertimbangan',
            'no'              => 'kode_klasifikasi.kode',
            'kode'            => 'kode_klasifikasi.kode',
            'retensi_aktif'   => 'jra.retensi_aktif',
            'retensi_inaktif' => 'jra.retensi_inaktif',
            'keterangan'      => 'jra.keterangan_retensi',
            'keamanan'        => 'jra.klasifikasi_keamanan',
            'dasar'           => 'jra.dasar_pertimbangan',
        ];

        $orderParam = $this->request->getGet('order');
        if (is_array($orderParam) && isset($orderParam[0]['column'])) {
            $colKey   = (string) $orderParam[0]['column'];
            $orderCol = $colMap[$colKey] ?? 'kode_klasifikasi.kode';
            $orderDir = strtoupper((string) ($orderParam[0]['dir'] ?? 'ASC')) === 'DESC' ? 'DESC' : 'ASC';
        } else {
            $sortBy   = (string) ($this->request->getGet('sort_by') ?: 'kode');
            $orderCol = $colMap[$sortBy] ?? 'kode_klasifikasi.kode';
            $orderDir = strtoupper((string) ($this->request->getGet('sort_dir') ?: 'ASC')) === 'DESC' ? 'DESC' : 'ASC';
        }

        $result = $this->jraModel->getJraDatatable($start, $length, $search, $orderCol, $orderDir);

        return $this->response->setJSON([
            'status'          => 'success',
            'draw'            => $draw,
            'recordsTotal'    => $result['recordsTotal'],
            'recordsFiltered' => $result['recordsFiltered'],
            'data'            => $result['data'],
        ]);
    }

    public function create()
    {
        if (!in_array(session()->get('nama_role'), ['Super_Admin', 'Admin_Pemkab', 'Admin_OPD', 'Administrator OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data['title'] = 'Tambah JRA Baru';
        $data['klasifikasi_list'] = $this->db->table('kode_klasifikasi')->orderBy('kode', 'ASC')->get()->getResultArray();
        
        return view('jra/create', $data);
    }

    public function store()
    {
        if (!in_array(session()->get('nama_role'), ['Super_Admin', 'Admin_Pemkab', 'Admin_OPD', 'Administrator OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        if (!$this->validate([
            'kode'                => 'required',
            'nama_klasifikasi'    => 'required',
            'retensi_aktif'       => 'required|numeric',
            'retensi_inaktif'     => 'required|numeric',
            'keterangan_retensi'  => 'required',
            'klasifikasi_keamanan'=> 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Semua kolom wajib diisi dengan benar.');
        }

        $kode = $this->request->getPost('kode');
        $nama_klasifikasi = $this->request->getPost('nama_klasifikasi');
        $deskripsi = $this->request->getPost('deskripsi');

        // Cari atau Buat Kode Klasifikasi
        $klasifikasi = $this->db->table('kode_klasifikasi')->where('kode', $kode)->get()->getRowArray();
        if ($klasifikasi) {
            $id_kode_klasifikasi = $klasifikasi['id_kode_klasifikasi'];
            // Sinkronkan nama dan deskripsi
            $this->db->table('kode_klasifikasi')->where('id_kode_klasifikasi', $id_kode_klasifikasi)->update([
                'nama_klasifikasi' => $nama_klasifikasi,
                'deskripsi'        => $deskripsi,
                'updated_at'       => date('Y-m-d H:i:s')
            ]);
        } else {
            $dataBytes = random_bytes(16);
            $dataBytes[6] = chr(ord($dataBytes[6]) & 0x0f | 0x40);
            $dataBytes[8] = chr(ord($dataBytes[8]) & 0x3f | 0x80);
            $id_kode_klasifikasi = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($dataBytes), 4));
            $this->db->table('kode_klasifikasi')->insert([
                'id_kode_klasifikasi' => $id_kode_klasifikasi,
                'kode'                => $kode,
                'nama_klasifikasi'    => $nama_klasifikasi,
                'deskripsi'           => $deskripsi,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s')
            ]);
        }

        // Cek apakah kode klasifikasi sudah punya JRA
        $existing = $this->jraModel->getByKlasifikasi($id_kode_klasifikasi);
        if ($existing) {
            return redirect()->back()->withInput()->with('error', 'Kode Klasifikasi tersebut sudah memiliki jadwal JRA.');
        }

        $jraData = [
            'id_kode_klasifikasi' => $id_kode_klasifikasi,
            'retensi_aktif'       => $this->request->getPost('retensi_aktif'),
            'retensi_inaktif'     => $this->request->getPost('retensi_inaktif'),
            'keterangan_retensi'  => $this->request->getPost('keterangan_retensi'),
            'klasifikasi_keamanan'=> $this->request->getPost('klasifikasi_keamanan'),
            'hak_akses'           => $this->request->getPost('hak_akses'),
            'akses_publik'        => $this->request->getPost('akses_publik'),
            'dasar_pertimbangan'  => $this->request->getPost('dasar_pertimbangan'),
            'unit_pengolah'       => $this->request->getPost('unit_pengolah')
        ];

        $this->jraModel->insert($jraData);
        log_activity('JRA', 'CREATE', "Menambahkan data Master JRA baru: {$kode} - {$nama_klasifikasi}");

        return redirect()->to('/jra')->with('success', 'Master JRA berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('nama_role'), ['Super_Admin', 'Admin_Pemkab', 'Admin_OPD', 'Administrator OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data['title'] = 'Edit JRA';
        $data['jra'] = $this->jraModel->find($id);
        if (!$data['jra']) {
            return redirect()->to('/jra')->with('error', 'Data tidak ditemukan.');
        }

        // Data dengan keterangan retensi Musnah tidak dapat diedit
        if (str_contains(strtolower(trim($data['jra']['keterangan_retensi'] ?? '')), 'musnah')) {
            return redirect()->to('/jra')->with('error', 'Data JRA dengan status Musnah tidak dapat diedit.');
        }

        $data['klasifikasi'] = $this->db->table('kode_klasifikasi')->where('id_kode_klasifikasi', $data['jra']['id_kode_klasifikasi'])->get()->getRowArray();

        
        return view('jra/edit', $data);
    }

    public function update($id)
    {
        if (!in_array(session()->get('nama_role'), ['Super_Admin', 'Admin_Pemkab', 'Admin_OPD', 'Administrator OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $jra = $this->jraModel->find($id);
        if (!$jra) {
            return redirect()->to('/jra')->with('error', 'Data tidak ditemukan.');
        }

        // Data dengan keterangan retensi Musnah tidak dapat diedit
        if (str_contains(strtolower(trim($jra['keterangan_retensi'] ?? '')), 'musnah')) {
            return redirect()->to('/jra')->with('error', 'Data JRA dengan status Musnah tidak dapat diedit.');
        }

        if (!$this->validate([
            'kode'                => 'required',
            'nama_klasifikasi'    => 'required',
            'retensi_aktif'       => 'required|numeric',
            'retensi_inaktif'     => 'required|numeric',
            'keterangan_retensi'  => 'required',
            'klasifikasi_keamanan'=> 'required',
        ])) {
            return redirect()->back()->withInput()->with('error', 'Semua kolom wajib diisi dengan benar.');
        }

        $kode = $this->request->getPost('kode');
        $nama_klasifikasi = $this->request->getPost('nama_klasifikasi');
        $deskripsi = $this->request->getPost('deskripsi');

        // Cari atau Buat Kode Klasifikasi
        $klasifikasi = $this->db->table('kode_klasifikasi')->where('kode', $kode)->get()->getRowArray();
        if ($klasifikasi) {
            $id_kode_klasifikasi = $klasifikasi['id_kode_klasifikasi'];
            // Sinkronkan nama dan deskripsi
            $this->db->table('kode_klasifikasi')->where('id_kode_klasifikasi', $id_kode_klasifikasi)->update([
                'nama_klasifikasi' => $nama_klasifikasi,
                'deskripsi'        => $deskripsi,
                'updated_at'       => date('Y-m-d H:i:s')
            ]);
        } else {
            $dataBytes = random_bytes(16);
            $dataBytes[6] = chr(ord($dataBytes[6]) & 0x0f | 0x40);
            $dataBytes[8] = chr(ord($dataBytes[8]) & 0x3f | 0x80);
            $id_kode_klasifikasi = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($dataBytes), 4));
            $this->db->table('kode_klasifikasi')->insert([
                'id_kode_klasifikasi' => $id_kode_klasifikasi,
                'kode'                => $kode,
                'nama_klasifikasi'    => $nama_klasifikasi,
                'deskripsi'           => $deskripsi,
                'created_at'          => date('Y-m-d H:i:s'),
                'updated_at'          => date('Y-m-d H:i:s')
            ]);
        }

        // Pastikan kode klasifikasi tidak bentrok dengan JRA lain
        $conflict = $this->jraModel->where('id_kode_klasifikasi', $id_kode_klasifikasi)
                                   ->where('id_jra !=', $id)
                                   ->first();
        if ($conflict) {
            return redirect()->back()->withInput()->with('error', 'Kode Klasifikasi tersebut sudah digunakan pada jadwal JRA lain.');
        }

        $updateData = [
            'id_kode_klasifikasi' => $id_kode_klasifikasi,
            'retensi_aktif'       => $this->request->getPost('retensi_aktif'),
            'retensi_inaktif'     => $this->request->getPost('retensi_inaktif'),
            'keterangan_retensi'  => $this->request->getPost('keterangan_retensi'),
            'klasifikasi_keamanan'=> $this->request->getPost('klasifikasi_keamanan'),
            'hak_akses'           => $this->request->getPost('hak_akses'),
            'akses_publik'        => $this->request->getPost('akses_publik'),
            'dasar_pertimbangan'  => $this->request->getPost('dasar_pertimbangan'),
            'unit_pengolah'       => $this->request->getPost('unit_pengolah')
        ];

        $this->jraModel->update($id, $updateData);
        log_activity('JRA', 'UPDATE', "Memperbarui data Master JRA: {$kode} - {$nama_klasifikasi}", $jra, $updateData);

        return redirect()->to('/jra')->with('success', 'Master JRA berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('nama_role'), ['Super_Admin', 'Admin_Pemkab', 'Admin_OPD', 'Administrator OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $jra = $this->jraModel->find($id);
        if (!$jra) {
            return redirect()->to('/jra')->with('error', 'Data tidak ditemukan.');
        }

        // Data dengan keterangan retensi Musnah tidak dapat dihapus
        if (str_contains(strtolower(trim($jra['keterangan_retensi'] ?? '')), 'musnah')) {
            return redirect()->to('/jra')->with('error', 'Data JRA dengan status Musnah tidak dapat dihapus.');
        }

        $this->jraModel->delete($id);
        log_activity('JRA', 'DELETE', 'Menghapus data Master JRA ID: ' . $id);
        return redirect()->to('/jra')->with('success', 'Data JRA berhasil dihapus.');
    }

    // Endpoint AJAX
    public function get_by_klasifikasi($id_kode_klasifikasi)
    {
        $jra = $this->jraModel->getByKlasifikasi($id_kode_klasifikasi);
        if ($jra) {
            return $this->response->setJSON(['status' => 'success', 'data' => $jra]);
        }
        return $this->response->setJSON(['status' => 'not_found']);
    }
}
