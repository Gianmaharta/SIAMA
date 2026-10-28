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

        $filters = [
            'search'  => trim((string) $this->request->getGet('search')),
            'tanggal' => trim((string) $this->request->getGet('tanggal')),
            'status'  => trim((string) $this->request->getGet('status')),
        ];

        // Tarik data berita acara
        // Jika Admin Pemkab, lihat semua. Jika OPD, lihat milik OPD-nya.
        if ($role === 'Admin_Pemkab') {
            $data['berita_acara'] = $this->beritaAcaraModel->getBeritaAcaraWithRelations(null, null, $filters);
        } else {
            $data['berita_acara'] = $this->beritaAcaraModel->getBeritaAcaraWithRelations(null, $id_opd, $filters);
        }

        $data['filters'] = $filters;

        return view('berita_acara/index', $data);
    }

    public function create()
    {
        $session = session();
        $id_opd = $session->get('id_opd');
        $user_id = $session->get('user_id');
        $role = $session->get('nama_role');
        
        // Ambil SPT aktif milik user (atau semua SPT milik OPD)
        $sptQuery = $this->sptModel;
        if (!empty($id_opd)) {
            $sptQuery = $sptQuery->where('id_opd', $id_opd);
        }
        $data['spt_list'] = $sptQuery->where('status', 'Aktif')->findAll();

        // Ambil Arsip milik OPD yang belum punya id_berita_acara
        $arsipQuery = $this->arsipModel;
        if (!empty($id_opd)) {
            $arsipQuery = $arsipQuery->where('id_opd', $id_opd);
        }
        $data['arsip_list'] = $arsipQuery->where('id_berita_acara', null)->findAll();
        $data['user_role'] = $role;

        return view('berita_acara/create', $data);
    }

    public function store()
    {
        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $nomor_ba = trim((string) $this->request->getPost('nomor_ba'));
            $tanggal_ba = trim((string) $this->request->getPost('tanggal_ba'));
            $id_spt = $this->request->getPost('id_spt');
            $arsip_ids = $this->request->getPost('arsip_ids'); // array

            if (empty($nomor_ba) || empty($tanggal_ba)) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Nomor Berita Acara dan Tanggal wajib diisi.');
            }

            if (empty($arsip_ids) || !is_array($arsip_ids)) {
                $db->transRollback();
                return redirect()->back()->withInput()->with('error', 'Pilih minimal 1 arsip yang akan dimasukkan ke dalam Berita Acara.');
            }

            // Generate UUID manually for the header to use it in details
            $data_uuid = random_bytes(16);
            $data_uuid[6] = chr(ord($data_uuid[6]) & 0x0f | 0x40);
            $data_uuid[8] = chr(ord($data_uuid[8]) & 0x3f | 0x80);
            $id_ba = vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data_uuid), 4));
            
            $session = session();
            $id_opd = $session->get('id_opd');
            if (empty($id_opd)) {
                $firstArsip = $this->arsipModel->find($arsip_ids[0]);
                $id_opd = $firstArsip['id_opd'] ?? null;
            }

            $headerData = [
                'id_berita_acara' => $id_ba,
                'id_opd' => $id_opd,
                'id_spt' => $id_spt ?: null,
                'nomor_ba' => $nomor_ba,
                'tanggal_ba' => $tanggal_ba,
                'status_persetujuan' => 'Draf_Kabid',
                'id_pelaksana' => $session->get('user_id'),
            ];

            $this->beritaAcaraModel->insert($headerData);

            // Simpan Detail
            foreach ($arsip_ids as $id_arsip) {
                $insertRes = $this->beritaAcaraDetailModel->insert([
                    'id_berita_acara' => $id_ba,
                    'id_arsip' => $id_arsip
                ]);
                
                if (!$insertRes) {
                    throw new \Exception('Gagal menyimpan rincian arsip: ' . implode(', ', $this->beritaAcaraDetailModel->errors()));
                }

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

    public function edit($id)
    {
        $ba = $this->beritaAcaraModel->find($id);

        if (!$ba || !in_array($ba['status_persetujuan'], ['Draf_Kabid', 'Revisi'])) {
            return redirect()->to('/berita-acara')->with('error', 'Berita Acara ini tidak dapat diedit karena sudah diproses atau disahkan.');
        }

        $session = session();
        $id_opd = $session->get('id_opd') ?? $ba['id_opd'];
        $data['ba'] = $ba;
        $data['spt_list'] = $this->sptModel->where('id_opd', $id_opd)
                                           ->where('status', 'Aktif')
                                           ->findAll();

        // 1. Arsip yang saat ini terlampir pada BA ini
        $arsipTerlampir = $this->beritaAcaraDetailModel->getDetailArsipByBaId($id);

        // 2. Arsip OPD yang belum terkait BA manapun (tersedia untuk ditambahkan)
        $arsipTersediaQuery = $this->arsipModel->where('id_berita_acara', null);
        if (!empty($id_opd)) {
            $arsipTersediaQuery->where('id_opd', $id_opd);
        }
        $arsipTersedia = $arsipTersediaQuery->orderBy('created_at', 'DESC')->findAll();

        // Gabungkan untuk ditampilkan: arsip terlampir (is_attached = true) + arsip tersedia (is_attached = false)
        $listArsip = [];
        foreach ($arsipTerlampir as $item) {
            $item['is_attached'] = true;
            $listArsip[] = $item;
        }
        foreach ($arsipTersedia as $item) {
            $item['is_attached'] = false;
            $listArsip[] = $item;
        }

        $data['list_arsip'] = $listArsip;
        $data['arsip_terpilih_count'] = count($arsipTerlampir);

        return view('berita_acara/edit', $data);
    }

    public function update($id)
    {
        $ba = $this->beritaAcaraModel->find($id);

        if (!$ba || !in_array($ba['status_persetujuan'], ['Draf_Kabid', 'Revisi'])) {
            return redirect()->to('/berita-acara')->with('error', 'Berita Acara ini tidak dapat diedit karena sudah diproses atau disahkan.');
        }

        $nomor_ba = trim((string) $this->request->getPost('nomor_ba'));
        $tanggal_ba = trim((string) $this->request->getPost('tanggal_ba'));
        $id_spt = $this->request->getPost('id_spt');
        $arsip_ids = (array) $this->request->getPost('arsip_ids');

        if (empty($nomor_ba) || empty($tanggal_ba)) {
            return redirect()->back()->withInput()->with('error', 'Nomor dan Tanggal Berita Acara wajib diisi.');
        }

        if (empty($arsip_ids)) {
            return redirect()->back()->withInput()->with('error', 'Minimal satu arsip harus dipilih untuk Berita Acara ini.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            $dataUpdate = [
                'nomor_ba'   => $nomor_ba,
                'tanggal_ba' => $tanggal_ba,
                'id_spt'     => $id_spt ?: null,
            ];

            // Jika statusnya Revisi, kembalikan ke Draf_Kabid agar dapat diverifikasi ulang oleh Kabid
            if ($ba['status_persetujuan'] === 'Revisi') {
                $dataUpdate['status_persetujuan'] = 'Draf_Kabid';
            }

            $this->beritaAcaraModel->update($id, $dataUpdate);

            // Kelola relasi arsip
            $currentDetails = $this->beritaAcaraDetailModel->where('id_berita_acara', $id)->findAll();
            $currentArsipIds = array_column($currentDetails, 'id_arsip');

            // 1. Arsip yang dilepas (ada di lama, tidak ada di baru)
            $removedIds = array_diff($currentArsipIds, $arsip_ids);
            foreach ($removedIds as $remId) {
                $this->arsipModel->update($remId, ['id_berita_acara' => null]);
                $this->beritaAcaraDetailModel->where('id_berita_acara', $id)
                                             ->where('id_arsip', $remId)
                                             ->delete();
            }

            // 2. Arsip baru yang ditambahkan (ada di baru, tidak ada di lama)
            $addedIds = array_diff($arsip_ids, $currentArsipIds);
            foreach ($addedIds as $addId) {
                $this->beritaAcaraDetailModel->insert([
                    'id_berita_acara' => $id,
                    'id_arsip'        => $addId,
                ]);
                $this->arsipModel->update($addId, ['id_berita_acara' => $id]);
            }

            $db->transCommit();

            log_activity('Berita Acara', 'UPDATE', 'Memperbarui draf Berita Acara nomor: ' . $nomor_ba, $ba, $dataUpdate);

            return redirect()->to('/berita-acara')->with('success', 'Draf Berita Acara berhasil diperbarui.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $ba = $this->beritaAcaraModel->find($id);

        if (!$ba || !in_array($ba['status_persetujuan'], ['Draf_Kabid', 'Revisi'])) {
            return redirect()->to('/berita-acara')->with('error', 'Berita Acara ini tidak dapat dihapus.');
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // 1. Kosongkan id_berita_acara di tabel arsip
            $arsipTerkait = $this->beritaAcaraDetailModel->where('id_berita_acara', $id)->findAll();
            foreach ($arsipTerkait as $detail) {
                $upd = $this->arsipModel->update($detail['id_arsip'], ['id_berita_acara' => null]);
                if (!$upd) {
                    throw new \Exception('Gagal mereset status arsip: ' . implode(',', $this->arsipModel->errors()));
                }
            }

            // 2. Hapus detail
            $this->beritaAcaraDetailModel->where('id_berita_acara', $id)->delete();

            // 3. Hapus header
            $this->beritaAcaraModel->delete($id);

            $db->transCommit();
            log_activity('Berita Acara', 'delete', 'Menghapus Berita Acara nomor: ' . $ba['nomor_ba']);
            
            return redirect()->to('/berita-acara')->with('success', 'Berita Acara berhasil dihapus beserta seluruh kaitannya.');
        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Gagal menghapus Berita Acara: ' . $e->getMessage());
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
