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
        if (!in_array($session->get('nama_role'), ['Admin_Pemkab', 'Admin_OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data['title'] = 'Master Jadwal Retensi Arsip (JRA)';
        $data['jra_list'] = $this->jraModel->getJraWithKlasifikasi();

        return view('jra/index', $data);
    }

    public function create()
    {
        if (!in_array(session()->get('nama_role'), ['Admin_Pemkab', 'Admin_OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data['title'] = 'Tambah JRA Baru';
        $data['klasifikasi_list'] = $this->db->table('kode_klasifikasi')->orderBy('kode', 'ASC')->get()->getResultArray();
        
        return view('jra/create', $data);
    }

    public function store()
    {
        if (!in_array(session()->get('nama_role'), ['Admin_Pemkab', 'Admin_OPD'])) {
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

        $this->jraModel->insert([
            'id_kode_klasifikasi' => $id_kode_klasifikasi,
            'retensi_aktif'       => $this->request->getPost('retensi_aktif'),
            'retensi_inaktif'     => $this->request->getPost('retensi_inaktif'),
            'keterangan_retensi'  => $this->request->getPost('keterangan_retensi'),
            'klasifikasi_keamanan'=> $this->request->getPost('klasifikasi_keamanan'),
            'hak_akses'           => $this->request->getPost('hak_akses'),
            'akses_publik'        => $this->request->getPost('akses_publik'),
            'dasar_pertimbangan'  => $this->request->getPost('dasar_pertimbangan'),
            'unit_pengolah'       => $this->request->getPost('unit_pengolah')
        ]);

        return redirect()->to('/jra')->with('success', 'Master JRA berhasil ditambahkan.');
    }

    public function edit($id)
    {
        if (!in_array(session()->get('nama_role'), ['Admin_Pemkab', 'Admin_OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }

        $data['title'] = 'Edit JRA';
        $data['jra'] = $this->jraModel->find($id);
        if (!$data['jra']) {
            return redirect()->to('/jra')->with('error', 'Data tidak ditemukan.');
        }
        $data['klasifikasi'] = $this->db->table('kode_klasifikasi')->where('id_kode_klasifikasi', $data['jra']['id_kode_klasifikasi'])->get()->getRowArray();

        
        return view('jra/edit', $data);
    }

    public function update($id)
    {
        if (!in_array(session()->get('nama_role'), ['Admin_Pemkab', 'Admin_OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
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

        $this->jraModel->update($id, [
            'id_kode_klasifikasi' => $id_kode_klasifikasi,
            'retensi_aktif'       => $this->request->getPost('retensi_aktif'),
            'retensi_inaktif'     => $this->request->getPost('retensi_inaktif'),
            'keterangan_retensi'  => $this->request->getPost('keterangan_retensi'),
            'klasifikasi_keamanan'=> $this->request->getPost('klasifikasi_keamanan'),
            'hak_akses'           => $this->request->getPost('hak_akses'),
            'akses_publik'        => $this->request->getPost('akses_publik'),
            'dasar_pertimbangan'  => $this->request->getPost('dasar_pertimbangan'),
            'unit_pengolah'       => $this->request->getPost('unit_pengolah')
        ]);

        return redirect()->to('/jra')->with('success', 'Master JRA berhasil diperbarui.');
    }

    public function delete($id)
    {
        if (!in_array(session()->get('nama_role'), ['Admin_Pemkab', 'Admin_OPD'])) {
            return redirect()->to('/dashboard')->with('error', 'Akses ditolak.');
        }
        $this->jraModel->delete($id);
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
