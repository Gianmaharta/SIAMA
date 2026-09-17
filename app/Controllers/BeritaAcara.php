<?php

namespace App\Controllers;

use App\Models\BeritaAcaraModel;
use App\Models\BeritaAcaraDetailModel;
use App\Models\ArsipModel;
use App\Models\SptModel;
use Dompdf\Dompdf;

class BeritaAcara extends BaseController
{
    protected $beritaAcaraModel;
    protected $beritaAcaraDetailModel;
    protected $arsipModel;
    protected $sptModel;

    public function __construct()
    {
        $this->beritaAcaraModel = new BeritaAcaraModel();
        $this->beritaAcaraDetailModel = new BeritaAcaraDetailModel();
        $this->arsipModel = new ArsipModel();
        $this->sptModel = new SptModel();
        helper('logger');
    }

    public function index()
    {
        $role = session()->get('nama_role');
        $id_opd = session()->get('id_opd');
        $id_user = session()->get('user_id');

        // Tarik data berita acara
        // Jika Admin Pemkab, lihat semua. Jika OPD, lihat milik OPD-nya.
        if ($role === 'Admin_Pemkab') {
            $data['berita_acara'] = $this->beritaAcaraModel->getBeritaAcaraWithRelations();
        } else {
            $data['berita_acara'] = $this->beritaAcaraModel->getBeritaAcaraWithRelations(null, $id_opd);
        }

        return view('berita_acara/index', $data);
    }

    public function create()
    {
        // Hanya Arsiparis
        $id_opd = session()->get('id_opd');
        
        // Ambil SPT aktif milik user (atau semua SPT milik OPD)
        $data['spt_list'] = $this->sptModel->where('id_opd', $id_opd)
                                           ->where('status', 'Berjalan')
                                           ->findAll();

        // Ambil Arsip milik OPD yang belum punya id_berita_acara
        $data['arsip_list'] = $this->arsipModel->where('id_opd', $id_opd)
                                               ->where('id_berita_acara', null)
                                               ->findAll();

        return view('berita_acara/create', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $nomor_ba = $this->request->getPost('nomor_ba');
            $tanggal_ba = $this->request->getPost('tanggal_ba');
            $id_spt = $this->request->getPost('id_spt');
            $arsip_ids = $this->request->getPost('arsip_ids'); // array

            if (empty($arsip_ids)) {
                return redirect()->back()->with('error', 'Pilih minimal 1 arsip.');
            }

            // Simpan Header
            $id_ba = \App\Helpers\generate_uuidv4(); // Kita asumsikan model akan generate, tapi butuh ID-nya
            $headerData = [
                'id_berita_acara' => $id_ba,
                'id_opd' => session()->get('id_opd'),
                'id_spt' => $id_spt ?: null,
                'nomor_ba' => $nomor_ba,
                'tanggal_ba' => $tanggal_ba,
                'status_persetujuan' => 'Draf_Kabid',
                'id_pelaksana' => session()->get('user_id'),
            ];

            $this->beritaAcaraModel->insert($headerData);

            // Simpan Detail
            foreach ($arsip_ids as $id_arsip) {
                $this->beritaAcaraDetailModel->insert([
                    'id_berita_acara' => $id_ba,
                    'id_arsip' => $id_arsip
                ]);

                // Update status di tabel arsip agar tak dipilih lagi
                $this->arsipModel->update($id_arsip, ['id_berita_acara' => $id_ba]);
            }

            $db->transCommit();

            log_activity('Berita Acara', 'create', 'Membuat draf Berita Acara nomor: ' . $nomor_ba);
            
            return redirect()->to('/berita-acara')->with('success', 'Draf Berita Acara berhasil dibuat.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }

    public function detail($id)
    {
        $ba = $this->beritaAcaraModel->getBeritaAcaraWithRelations($id);

        if (!$ba) {
            return redirect()->to('/berita-acara')->with('error', 'Data tidak ditemukan.');
        }

        $data['ba'] = $ba;
        $data['arsip_detail'] = $this->beritaAcaraDetailModel->getDetailArsipByBaId($id);

        return view('berita_acara/detail', $data);
    }

    public function verifikasiKabid($id)
    {
        $keputusan = $this->request->getPost('keputusan');
        $catatan = $this->request->getPost('catatan_revisi');

        if ($keputusan == 'Approve') {
            $status = 'Draf_Pimpinan';
            $msg = 'menyetujui';
        } else {
            $status = 'Revisi';
            $msg = 'menolak/revisi';
        }

        $this->beritaAcaraModel->update($id, [
            'status_persetujuan' => $status,
            'catatan_revisi' => $catatan,
            'id_kabid' => session()->get('user_id')
        ]);

        $ba = $this->beritaAcaraModel->find($id);
        log_activity('Berita Acara', 'update', "Kepala Bidang $msg Berita Acara nomor: " . $ba['nomor_ba']);

        return redirect()->to('/berita-acara/detail/' . $id)->with('success', 'Verifikasi berhasil disimpan.');
    }

    public function ttdPimpinan($id)
    {
        $this->beritaAcaraModel->update($id, [
            'status_persetujuan' => 'Selesai',
            'id_pimpinan' => session()->get('user_id')
        ]);

        $ba = $this->beritaAcaraModel->find($id);
        log_activity('Berita Acara', 'update', "Pimpinan mengesahkan Berita Acara nomor: " . $ba['nomor_ba']);

        return redirect()->to('/berita-acara/detail/' . $id)->with('success', 'Berita Acara berhasil disahkan.');
    }

    public function cetak($id)
    {
        $ba = $this->beritaAcaraModel->getBeritaAcaraWithRelations($id);

        if (!$ba || $ba['status_persetujuan'] !== 'Selesai') {
            // Bisa saja diizinkan cetak draf, tapi amannya cek status
            // return redirect()->to('/berita-acara')->with('error', 'Berita Acara belum selesai.');
        }

        $data['ba'] = $ba;
        $data['arsip_detail'] = $this->beritaAcaraDetailModel->getDetailArsipByBaId($id);

        $html = view('berita_acara/cetak_pdf', $data);

        $dompdf = new Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $dompdf->stream("Berita_Acara_" . str_replace('/', '_', $ba['nomor_ba']) . ".pdf", ["Attachment" => 0]);
        exit();
    }
}
