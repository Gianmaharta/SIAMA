<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\UserRoleModel;
use App\Models\OpdModel;
use App\Models\BidangModel;

class UserController extends BaseController
{
    protected $userModel;
    protected $userRoleModel;
    protected $opdModel;
    protected $bidangModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->userRoleModel = new UserRoleModel();
        $this->opdModel = new OpdModel();
        $this->bidangModel = new BidangModel();
        helper(['logger', 'setting']);
    }

    private function uuidv4(): string
    {
        $data = random_bytes(16);
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    private function generateRandomPassword(int $length = 10): string
    {
        $upper   = 'ABCDEFGHJKLMNPQRSTUVWXYZ';
        $lower   = 'abcdefghijkmnopqrstuvwxyz';
        $digits  = '23456789';
        $symbols = '!@#$%';

        $password  = $upper[random_int(0, strlen($upper) - 1)];
        $password .= $lower[random_int(0, strlen($lower) - 1)];
        $password .= $digits[random_int(0, strlen($digits) - 1)];
        $password .= $symbols[random_int(0, strlen($symbols) - 1)];

        $all = $upper . $lower . $digits;
        for ($i = 4; $i < $length; $i++) {
            $password .= $all[random_int(0, strlen($all) - 1)];
        }

        return str_shuffle($password);
    }

    public function index()
    {
        $role = session()->get('nama_role');
        $idOpd = session()->get('id_opd');

        // Fetch users based on role hierarchy
        $builder = $this->userModel->db->table('users')
            ->select('users.*, roles.nama_role, roles.id_role, opd.nama_opd, bidang.nama_bidang')
            ->join('user_roles', 'user_roles.id_user = users.id_user', 'left')
            ->join('roles', 'roles.id_role = user_roles.id_role', 'left')
            ->join('opd', 'opd.id_opd = users.id_opd', 'left')
            ->join('bidang', 'bidang.id_bidang = users.id_bidang', 'left');

        if ($role === 'Super_Admin') {
            // Super Admin adalah level tertinggi, dapat melihat seluruh user
        } elseif ($role === 'Admin_Pemkab') {
            // Admin Pemkab melihat seluruh user Pemkab ke bawah, tetapi TIDAK menampilkan Super_Admin
            $builder->where("(roles.nama_role IS NULL OR roles.nama_role NOT IN ('Super_Admin', 'Super Admin'))", null, false);
        } elseif ($role === 'Admin_OPD') {
            // Admin OPD hanya mengelola user di OPD yang sama di bawah levelnya
            $builder->where('users.id_opd', $idOpd);
            $builder->whereNotIn('roles.nama_role', ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD']);
        } else {
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak.');
        }

        $users = $builder->orderBy('users.created_at', 'DESC')->get()->getResultArray();

        $data['users']           = $users;
        $data['totalUsers']      = count($users);
        $data['activeUsers']     = count(array_filter($users, fn($u) => (int)($u['is_active'] ?? 0) === 1));
        $data['inactiveUsers']   = count(array_filter($users, fn($u) => (int)($u['is_active'] ?? 0) === 0));
        $data['defaultPassword'] = get_setting('default_password', 'Admin123!');
        $data['passwordMode']    = get_setting('password_mode', 'default');

        // Ambil data OPD untuk filter & modal
        if ($role === 'Admin_OPD') {
            $data['opds'] = $this->opdModel->where('id_opd', $idOpd)->findAll();
            $data['id_opd_locked'] = $idOpd;
        } else {
            $data['opds'] = $this->opdModel->orderBy('nama_opd', 'ASC')->findAll();
            $data['id_opd_locked'] = null;
        }

        // Ambil data Role untuk modal sesuai hierarki
        $rolesBuilder = $this->userModel->db->table('roles')
            ->select('MAX(id_role) as id_role, nama_role')
            ->groupBy('nama_role')
            ->orderBy('nama_role', 'ASC');

        if ($role === 'Admin_Pemkab') {
            $rolesBuilder->whereNotIn('nama_role', ['Super_Admin', 'Super Admin']);
        } elseif ($role === 'Admin_OPD') {
            $rolesBuilder->whereNotIn('nama_role', ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD']);
        }
        $data['roles'] = $rolesBuilder->get()->getResultArray();

        // Ambil data Bidang untuk modal
        if ($role === 'Admin_OPD') {
            $data['bidangs'] = $this->bidangModel->where('id_opd', $idOpd)->orderBy('nama_bidang', 'ASC')->findAll();
        } else {
            $data['bidangs'] = $this->bidangModel->orderBy('nama_bidang', 'ASC')->findAll();
        }

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'           => 'success',
                'current_role'     => $role,
                'total_users'      => $data['totalUsers'],
                'active_users'     => $data['activeUsers'],
                'inactive_users'   => $data['inactiveUsers'],
                'default_password' => $data['defaultPassword'],
                'password_mode'    => $data['passwordMode'],
                'roles_available'  => $data['roles'],
                'users'            => array_map(static function (array $u): array {
                    unset($u['password']);
                    return $u;
                }, $users),
            ]);
        }

        return view('user/index', $data);
    }

    /**
     * Endpoint Server-Side DataTables untuk Manajemen Akun (GET /users/datatable)
     */
    public function datatable()
    {
        $role  = session()->get('nama_role');
        $idOpd = session()->get('id_opd');

        if (!in_array($role, ['Super_Admin', 'Admin_Pemkab', 'Admin_OPD'], true)) {
            return $this->response->setJSON(['status' => 'error', 'message' => 'Akses Ditolak.'])->setStatusCode(403);
        }

        $draw   = (int) ($this->request->getGet('draw') ?? 1);
        $length = max(1, min(100, (int) ($this->request->getGet('length') ?: $this->request->getGet('per_page') ?: 25)));
        $start  = $this->request->getGet('start') !== null
            ? max(0, (int) $this->request->getGet('start'))
            : (max(1, (int) ($this->request->getGet('page') ?: 1)) - 1) * $length;

        $searchParam = $this->request->getGet('search');
        $search = is_array($searchParam)
            ? trim((string) ($searchParam['value'] ?? ''))
            : trim((string) ($searchParam ?? ''));

        $filterRole   = trim((string) ($this->request->getGet('role') ?? ''));
        $filterOpd    = trim((string) ($this->request->getGet('id_opd') ?? ''));
        $filterStatus = $this->request->getGet('is_active');

        $baseBuilder = $this->userModel->db->table('users')
            ->select('users.id_user, users.nama, users.email, users.id_opd, users.id_bidang, users.is_active, users.is_default_password, users.created_at, roles.nama_role, roles.id_role, opd.nama_opd, bidang.nama_bidang')
            ->join('user_roles', 'user_roles.id_user = users.id_user', 'left')
            ->join('roles', 'roles.id_role = user_roles.id_role', 'left')
            ->join('opd', 'opd.id_opd = users.id_opd', 'left')
            ->join('bidang', 'bidang.id_bidang = users.id_bidang', 'left');

        if ($role === 'Admin_Pemkab') {
            $baseBuilder->where("(roles.nama_role IS NULL OR roles.nama_role NOT IN ('Super_Admin', 'Super Admin'))", null, false);
        } elseif ($role === 'Admin_OPD') {
            $baseBuilder->where('users.id_opd', $idOpd);
            $baseBuilder->whereNotIn('roles.nama_role', ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD']);
        }

        $totalBuilder = clone $baseBuilder;
        $recordsTotal = (int) $totalBuilder->countAllResults();

        if ($search !== '') {
            $baseBuilder->groupStart()
                ->like('users.nama', $search)
                ->orLike('users.email', $search)
                ->orLike('roles.nama_role', $search)
                ->orLike('opd.nama_opd', $search)
                ->orLike('bidang.nama_bidang', $search)
                ->groupEnd();
        }
        if ($filterRole !== '') {
            $baseBuilder->where('roles.nama_role', $filterRole);
        }
        if ($filterOpd !== '' && $role !== 'Admin_OPD') {
            $baseBuilder->where('users.id_opd', $filterOpd);
        }
        if ($filterStatus !== null && $filterStatus !== '') {
            $baseBuilder->where('users.is_active', (int) $filterStatus);
        }

        $filteredBuilder = clone $baseBuilder;
        $recordsFiltered = (int) $filteredBuilder->countAllResults();

        $rows = $baseBuilder
            ->orderBy('users.created_at', 'DESC')
            ->limit($length, $start)
            ->get()
            ->getResultArray();

        return $this->response->setJSON([
            'status'          => 'success',
            'draw'            => $draw,
            'recordsTotal'    => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data'            => $rows,
        ]);
    }

    public function create()
    {
        $role = session()->get('nama_role');
        $data['opds'] = $this->opdModel->findAll();

        $rolesBuilder = $this->userModel->db->table('roles')
            ->select('MAX(id_role) as id_role, nama_role')
            ->groupBy('nama_role');

        if ($role === 'Admin_Pemkab') {
            $rolesBuilder->whereNotIn('nama_role', ['Super_Admin', 'Super Admin']);
        } elseif ($role === 'Admin_OPD') {
            $rolesBuilder->whereNotIn('nama_role', ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD']);
            $data['bidangs'] = $this->bidangModel->where('id_opd', session()->get('id_opd'))->findAll();
        }
        $data['roles']           = $rolesBuilder->get()->getResultArray();
        $data['userRole']        = $role;
        $data['defaultPassword'] = get_setting('default_password', 'Admin123!');
        $data['passwordMode']    = get_setting('password_mode', 'default');

        return view('user/create', $data);
    }

    public function store()
    {
        $roleLogin = session()->get('nama_role');

        $rules = [
            'nama'    => 'required|max_length[100]',
            'email'   => 'required|valid_email|is_unique[users.email]',
            'id_role' => 'required',
        ];

        $roleTarget = $this->userModel->db->table('roles')->where('id_role', $this->request->getPost('id_role'))->get()->getRow();
        $targetRoleName = $roleTarget->nama_role ?? '';

        if ($roleLogin === 'Admin_Pemkab') {
            if (in_array($targetRoleName, ['Super_Admin', 'Super Admin'], true)) {
                return redirect()->back()->withInput()->with('error', 'Akses Ditolak: Anda tidak dapat menetapkan peran Super Admin.');
            }
            if (!in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab'], true)) {
                $rules['id_opd'] = 'required';
            }
        } elseif ($roleLogin === 'Admin_OPD') {
            if (in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD'], true)) {
                return redirect()->back()->withInput()->with('error', 'Akses Ditolak: Anda tidak dapat menetapkan peran Administrator.');
            }
        } elseif ($roleLogin === 'Super_Admin') {
            if (!in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab'], true)) {
                $rules['id_opd'] = 'required';
            }
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Silakan periksa kembali data Anda: ' . implode(', ', $this->validator->getErrors()));
        }

        // Aturan Hierarki Organisasi:
        // - Super_Admin & Admin_Pemkab: tidak punya id_opd & id_bidang
        // - Admin_OPD & Pimpinan: punya id_opd, tetapi TIDAK punya id_bidang
        // - Kepala_Bidang & Arsiparis: punya id_opd dan dapat memiliki id_bidang
        if (in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab'], true)) {
            $idOpd    = null;
            $idBidang = null;
        } else {
            $idOpd = ($roleLogin === 'Admin_OPD') ? session()->get('id_opd') : ($this->request->getPost('id_opd') ?: null);
            if (in_array($targetRoleName, ['Admin_OPD', 'Administrator OPD', 'Pimpinan', 'Kepala_Dinas', 'Kepala Dinas'], true)) {
                $idBidang = null;
            } else {
                $idBidang = $this->request->getPost('id_bidang') ?: null;
            }
        }

        // Opsi Mekanisme Password: 'default' (dari Pengaturan Website) atau 'random'
        $passwordOption = trim((string) $this->request->getPost('password_option'));
        if (!in_array($passwordOption, ['default', 'random'], true)) {
            $passwordOption = get_setting('password_mode', 'default');
        }

        if ($passwordOption === 'random') {
            $plainPassword = $this->generateRandomPassword(10);
        } else {
            $plainPassword = get_setting('default_password', 'Admin123!');
        }

        $userId = $this->uuidv4();
        $userData = [
            'id_user'             => $userId,
            'nama'                => $this->request->getPost('nama'),
            'email'               => $this->request->getPost('email'),
            'password'            => password_hash($plainPassword, PASSWORD_DEFAULT),
            'id_opd'              => $idOpd,
            'id_bidang'           => $idBidang,
            'is_active'           => 1,
            'is_default_password' => 1,
        ];

        $this->userModel->db->transStart();
        $this->userModel->insert($userData);
        $this->userRoleModel->insert([
            'id_user' => $userId,
            'id_role' => $this->request->getPost('id_role'),
        ]);
        $this->userModel->db->transComplete();

        if ($this->userModel->db->transStatus() === false) {
            return redirect()->back()->withInput()->with('error', 'Gagal membuat akun.');
        }

        log_activity('Users', 'CREATE', 'Membuat akun pengguna baru: ' . $userData['nama']);

        try {
            notification_service()->notifyUserManagement($userId, 'create', session()->get('user_id'));
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengirim notifikasi pembuatan user: ' . $e->getMessage());
        }

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'             => 'success',
                'message'            => 'Akun berhasil dibuat.',
                'user_id'            => $userId,
                'password_mode'      => $passwordOption,
                'generated_password' => $plainPassword,
            ]);
        }

        $redirect = redirect()->to('/users')->with('success', 'Akun berhasil dibuat dengan password awal: ' . $plainPassword);
        if ($passwordOption === 'random') {
            $redirect->with('generated_password', $plainPassword)
                     ->with('generated_user_name', $userData['nama'])
                     ->with('generated_user_email', $userData['email']);
        }

        return $redirect;
    }

    public function edit($id)
    {
        $role = session()->get('nama_role');
        $idOpd = session()->get('id_opd');

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $editedUserRole = $this->userRoleModel->where('id_user', $id)->first();
        $roleInfo = $this->userModel->db->table('roles')->where('id_role', $editedUserRole['id_role'] ?? '')->get()->getRowArray();
        $targetRoleName = $roleInfo['nama_role'] ?? '';

        if ($role === 'Admin_Pemkab') {
            if (in_array($targetRoleName, ['Super_Admin', 'Super Admin'], true)) {
                return redirect()->to('/users')->with('error', 'Akses Ditolak.');
            }
        } elseif ($role === 'Admin_OPD') {
            if ($user['id_opd'] !== $idOpd || in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD'], true)) {
                return redirect()->to('/users')->with('error', 'Akses Ditolak.');
            }
        }

        $data['user']         = $user;
        $data['current_role'] = $editedUserRole['id_role'] ?? '';
        $data['opds']         = $this->opdModel->findAll();

        $rolesBuilder = $this->userModel->db->table('roles')->select('MAX(id_role) as id_role, nama_role')->groupBy('nama_role');
        if ($role === 'Admin_Pemkab') {
            $rolesBuilder->whereNotIn('nama_role', ['Super_Admin', 'Super Admin']);
        } elseif ($role === 'Admin_OPD') {
            $rolesBuilder->whereNotIn('nama_role', ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD']);
            $data['bidangs'] = $this->bidangModel->where('id_opd', session()->get('id_opd'))->findAll();
        }
        $data['roles']    = $rolesBuilder->get()->getResultArray();
        $data['userRole'] = $role;

        return view('user/edit', $data);
    }

    public function update($id)
    {
        $roleLogin = session()->get('nama_role');
        $idOpdSession = session()->get('id_opd');
        $user = $this->userModel->find($id);

        if (!$user) {
            return redirect()->to('/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $existingRole = $this->userRoleModel->where('id_user', $id)->first();
        $existingRoleInfo = $this->userModel->db->table('roles')->where('id_role', $existingRole['id_role'] ?? '')->get()->getRowArray();
        $existingRoleName = $existingRoleInfo['nama_role'] ?? '';

        $roleTarget = $this->userModel->db->table('roles')->where('id_role', $this->request->getPost('id_role'))->get()->getRow();
        $targetRoleName = $roleTarget->nama_role ?? '';

        if ($roleLogin === 'Admin_Pemkab') {
            if (in_array($existingRoleName, ['Super_Admin', 'Super Admin'], true) || in_array($targetRoleName, ['Super_Admin', 'Super Admin'], true)) {
                return redirect()->to('/users')->with('error', 'Akses Ditolak: Anda tidak dapat mengelola akun Super Admin.');
            }
        } elseif ($roleLogin === 'Admin_OPD') {
            if ($user['id_opd'] !== $idOpdSession || in_array($existingRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD'], true) || in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD'], true)) {
                return redirect()->to('/users')->with('error', 'Akses Ditolak.');
            }
        }

        $rules = [
            'nama'    => 'required|max_length[100]',
            'id_role' => 'required',
            'email'   => 'required|valid_email|is_unique[users.email,id_user,' . $id . ']',
        ];

        if (in_array($roleLogin, ['Super_Admin', 'Admin_Pemkab'], true)) {
            if (!in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab'], true)) {
                $rules['id_opd'] = 'required';
            }
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Silakan periksa kembali data Anda: ' . implode(', ', $this->validator->getErrors()));
        }

        if (in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab'], true)) {
            $idOpd    = null;
            $idBidang = null;
        } else {
            $idOpd = ($roleLogin === 'Admin_OPD') ? $idOpdSession : ($this->request->getPost('id_opd') ?: null);
            if (in_array($targetRoleName, ['Admin_OPD', 'Administrator OPD', 'Pimpinan', 'Kepala_Dinas', 'Kepala Dinas'], true)) {
                $idBidang = null;
            } else {
                $idBidang = $this->request->getPost('id_bidang') ?: null;
            }
        }

        $userData = [
            'nama'      => $this->request->getPost('nama'),
            'email'     => $this->request->getPost('email'),
            'id_opd'    => $idOpd,
            'id_bidang' => $idBidang,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0,
        ];

        $this->userModel->db->transStart();
        $updUser = $this->userModel->update($id, $userData);
        if (!$updUser) {
            $this->userModel->db->transRollback();
            return redirect()->back()->withInput()->with('error', 'Gagal update data user: ' . implode(', ', $this->userModel->errors()));
        }

        $newRoleId = $this->request->getPost('id_role');
        if ($existingRole) {
            $this->userModel->db->table('user_roles')->where('id_user', $id)->update(['id_role' => $newRoleId]);
        } else {
            $this->userModel->db->table('user_roles')->insert(['id_user' => $id, 'id_role' => $newRoleId]);
        }
        $this->userModel->db->transComplete();

        if ($this->userModel->db->transStatus() === false) {
            $error = $this->userModel->db->error();
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui akun: ' . ($error['message'] ?? 'Unknown DB error'));
        }

        $user['id_role'] = $existingRole['id_role'] ?? null;
        $userData['id_role'] = $newRoleId;

        log_activity('Users', 'UPDATE', 'Memperbarui data pengguna: ' . $userData['nama'], $user, $userData);
        return redirect()->to('/users')->with('success', 'Akun berhasil diperbarui.');
    }

    /**
     * Toggle Cepat Status Aktif / Nonaktif langsung dari kolom Status tabel Manajemen Akun
     */
    public function toggleStatus($id)
    {
        $roleLogin = session()->get('nama_role');
        $idOpd     = session()->get('id_opd');
        $isAjax    = $this->request->isAJAX();

        $user = $this->userModel->find($id);
        if (!$user) {
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => 'Pengguna tidak ditemukan.'])->setStatusCode(404);
            }
            return redirect()->to('/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        if ($id === session()->get('user_id') || (session()->has('original_admin_id') && $id === session()->get('original_admin_id'))) {
            $msg = 'Anda tidak dapat menonaktifkan akun Anda sendiri yang sedang aktif.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(403);
            }
            return redirect()->to('/users')->with('error', $msg);
        }

        $editedUserRole = $this->userRoleModel->where('id_user', $id)->first();
        $roleInfo = $this->userModel->db->table('roles')->where('id_role', $editedUserRole['id_role'] ?? '')->get()->getRowArray();
        $targetRoleName = $roleInfo['nama_role'] ?? '';

        if ($roleLogin === 'Admin_Pemkab' && in_array($targetRoleName, ['Super_Admin', 'Super Admin'], true)) {
            $msg = 'Akses Ditolak: Anda tidak dapat mengubah status Super Admin.';
            if ($isAjax) {
                return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(403);
            }
            return redirect()->to('/users')->with('error', $msg);
        } elseif ($roleLogin === 'Admin_OPD') {
            if ($user['id_opd'] !== $idOpd || in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD'], true)) {
                $msg = 'Akses Ditolak.';
                if ($isAjax) {
                    return $this->response->setJSON(['status' => 'error', 'message' => $msg])->setStatusCode(403);
                }
                return redirect()->to('/users')->with('error', $msg);
            }
        }

        $newStatus = ((int) ($user['is_active'] ?? 0) === 1) ? 0 : 1;
        $this->userModel->update($id, ['is_active' => $newStatus]);

        $statusLabel = $newStatus === 1 ? 'diaktifkan' : 'dinonaktifkan';
        log_activity('Users', 'UPDATE', "Status akun {$user['nama']} berhasil {$statusLabel}.");

        if ($isAjax) {
            return $this->response->setJSON([
                'status'    => 'success',
                'is_active' => $newStatus,
                'message'   => "Status akun {$user['nama']} berhasil {$statusLabel}.",
            ]);
        }

        return redirect()->to('/users')->with('success', "Status akun {$user['nama']} berhasil {$statusLabel}.");
    }

    public function delete($id)
    {
        $roleLogin = session()->get('nama_role');
        $idOpd = session()->get('id_opd');

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $editedUserRole = $this->userRoleModel->where('id_user', $id)->first();
        $roleInfo = $this->userModel->db->table('roles')->where('id_role', $editedUserRole['id_role'] ?? '')->get()->getRowArray();
        $targetRoleName = $roleInfo['nama_role'] ?? '';

        if ($id === session()->get('user_id') || (session()->has('original_admin_id') && $id === session()->get('original_admin_id'))) {
            return redirect()->to('/users')->with('error', 'Keamanan Tingkat Tinggi: Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if (in_array($targetRoleName, ['Super_Admin', 'Super Admin'], true)) {
            return redirect()->to('/users')->with('error', 'Akun Super Admin tidak dapat dihapus.');
        }

        if ($roleLogin === 'Admin_Pemkab') {
            // Admin Pemkab dapat menghapus user di bawah Super Admin
        } elseif ($roleLogin === 'Admin_OPD') {
            if ($user['id_opd'] !== $idOpd || in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD'], true)) {
                return redirect()->to('/users')->with('error', 'Akses Ditolak.');
            }
        }

        $this->userModel->db->transStart();
        $this->userRoleModel->where('id_user', $id)->delete();
        $this->userModel->delete($id);
        $this->userModel->db->transComplete();

        if ($this->userModel->db->transStatus() === false) {
            return redirect()->to('/users')->with('error', 'Gagal menghapus akun.');
        }

        log_activity('Users', 'DELETE', 'Menghapus akun pengguna: ' . $user['nama']);
        return redirect()->to('/users')->with('success', 'Akun berhasil dihapus.');
    }

    public function resetPassword($id)
    {
        $roleLogin = session()->get('nama_role');
        $idOpd = session()->get('id_opd');

        $user = $this->userModel->find($id);
        if (!$user) {
            return redirect()->to('/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $editedUserRole = $this->userRoleModel->where('id_user', $id)->first();
        $roleInfo = $this->userModel->db->table('roles')->where('id_role', $editedUserRole['id_role'] ?? '')->get()->getRowArray();
        $targetRoleName = $roleInfo['nama_role'] ?? '';

        if ($roleLogin === 'Admin_Pemkab') {
            if (in_array($targetRoleName, ['Super_Admin', 'Super Admin'], true)) {
                return redirect()->to('/users')->with('error', 'Akses Ditolak.');
            }
        } elseif ($roleLogin === 'Admin_OPD') {
            if ($user['id_opd'] !== $idOpd || in_array($targetRoleName, ['Super_Admin', 'Super Admin', 'Admin_Pemkab', 'Admin_OPD'], true)) {
                return redirect()->to('/users')->with('error', 'Akses Ditolak.');
            }
        }

        $mode = trim((string) ($this->request->getPost('mode') ?: $this->request->getGet('mode') ?: 'default'));
        if ($mode === 'random') {
            $newPlainPassword = $this->generateRandomPassword(10);
        } else {
            $newPlainPassword = get_setting('default_password', 'Admin123!');
        }

        $this->userModel->update($id, [
            'password'            => password_hash($newPlainPassword, PASSWORD_DEFAULT),
            'is_default_password' => 1,
        ]);

        log_activity('Users', 'UPDATE', 'Mereset password pengguna: ' . $user['nama']);

        try {
            notification_service()->notifyUserManagement($id, 'reset', session()->get('user_id'));
        } catch (\Throwable $e) {
            log_message('error', 'Gagal mengirim notifikasi reset user: ' . $e->getMessage());
        }

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'       => 'success',
                'message'      => 'Password berhasil di-reset.',
                'user_id'      => $id,
                'mode'         => $mode,
                'new_password' => $newPlainPassword,
            ]);
        }

        $redirect = redirect()->back()->with('success', 'Password berhasil di-reset ke: ' . $newPlainPassword . '. Pengguna wajib mengganti password saat login.');
        if ($mode === 'random') {
            $redirect->with('generated_password', $newPlainPassword)
                     ->with('generated_user_name', $user['nama'])
                     ->with('generated_user_email', $user['email']);
        }

        return $redirect;
    }

    // =========================================================================
    // Switch Role / User Impersonation (KHUSUS SUPER ADMIN)
    // =========================================================================

    /**
     * Super Admin "menyamar" sebagai user lain untuk inspeksi.
     * Menyimpan data session asli Super Admin ke `original_admin_*`.
     */
    public function switchUser($userId)
    {
        $session = session();

        $currentRole = $session->get('nama_role');
        $isAlreadySwitched = $session->has('original_admin_id');

        if (!in_array($currentRole, ['Super_Admin', 'Super Admin'], true) || $isAlreadySwitched) {
            if ($this->wantsJson()) {
                return $this->response->setJSON([
                    'status'  => 'error',
                    'message' => 'Akses Ditolak. Fitur Masuk Sebagai hanya dapat digunakan oleh Super Admin.',
                ])->setStatusCode(403);
            }
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak. Fitur Masuk Sebagai hanya dapat digunakan oleh Super Admin.');
        }

        $targetUser = $this->userModel->find($userId);
        if (!$targetUser) {
            return redirect()->to('/users')->with('error', 'Pengguna tidak ditemukan.');
        }

        $targetUserRole = $this->userRoleModel->where('id_user', $userId)->first();
        if (!$targetUserRole) {
            return redirect()->to('/users')->with('error', 'Role pengguna tidak ditemukan.');
        }
        $roleInfo = $this->userModel->db->table('roles')->where('id_role', $targetUserRole['id_role'])->get()->getRowArray();

        if ($targetUser['id_user'] === $session->get('user_id')) {
            return redirect()->to('/users')->with('error', 'Anda sedang aktif menggunakan akun ini.');
        }

        if ($isAlreadySwitched && $targetUser['id_user'] === $session->get('original_admin_id')) {
            return $this->switchBack();
        }

        if (!$isAlreadySwitched) {
            $session->set([
                'original_admin_id'        => $session->get('user_id'),
                'original_admin_email'     => $session->get('email'),
                'original_admin_nama'      => $session->get('nama_lengkap'),
                'original_admin_role'      => $session->get('nama_role'),
                'original_admin_id_role'   => $session->get('id_role'),
                'original_admin_id_opd'    => $session->get('id_opd'),
                'original_admin_id_bidang' => $session->get('id_bidang'),
            ]);
        }

        $namaOpd = '-';
        if ($targetUser['id_opd']) {
            $opd = $this->opdModel->find($targetUser['id_opd']);
            $namaOpd = $opd ? $opd['nama_opd'] : '-';
        }

        $session->set([
            'user_id'               => $targetUser['id_user'],
            'email'                 => $targetUser['email'],
            'nama_lengkap'          => $targetUser['nama'],
            'id_role'               => $targetUserRole['id_role'],
            'nama_role'             => $roleInfo['nama_role'],
            'id_opd'                => $targetUser['id_opd'],
            'id_bidang'             => $targetUser['id_bidang'],
            'is_default_password'   => 0,
            'impersonated_opd_nama' => $namaOpd,
        ]);

        log_activity('Users', 'SWITCH_ROLE', 'Super Admin masuk sebagai: ' . $targetUser['nama'] . ' (' . str_replace('_', ' ', $roleInfo['nama_role']) . ')');

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'            => 'success',
                'message'           => 'Berhasil masuk sebagai: ' . $targetUser['nama'] . ' (' . str_replace('_', ' ', $roleInfo['nama_role']) . ')',
                'impersonated_user' => [
                    'user_id'   => $targetUser['id_user'],
                    'nama'      => $targetUser['nama'],
                    'email'     => $targetUser['email'],
                    'nama_role' => $roleInfo['nama_role'],
                    'id_opd'    => $targetUser['id_opd'],
                    'nama_opd'  => $namaOpd,
                ],
                'original_admin_id' => $session->get('original_admin_id'),
            ]);
        }

        return redirect()->to('/dashboard')->with('success', 'Berhasil masuk sebagai: ' . $targetUser['nama'] . ' (' . str_replace('_', ' ', $roleInfo['nama_role']) . ')');
    }

    /**
     * Mengembalikan session Super Admin ke akun aslinya.
     */
    public function switchBack()
    {
        $session = session();

        if (!$session->has('original_admin_id')) {
            return redirect()->to('/dashboard')->with('error', 'Anda tidak sedang dalam mode penyamaran.');
        }

        $session->set([
            'user_id'             => $session->get('original_admin_id'),
            'email'               => $session->get('original_admin_email'),
            'nama_lengkap'        => $session->get('original_admin_nama'),
            'nama_role'           => $session->get('original_admin_role'),
            'id_role'             => $session->get('original_admin_id_role'),
            'id_opd'              => $session->get('original_admin_id_opd'),
            'id_bidang'           => $session->get('original_admin_id_bidang'),
            'is_default_password' => 0,
        ]);

        $session->remove([
            'original_admin_id', 'original_admin_email', 'original_admin_nama',
            'original_admin_role', 'original_admin_id_role',
            'original_admin_id_opd', 'original_admin_id_bidang',
            'impersonated_opd_nama',
        ]);

        log_activity('Users', 'SWITCH_BACK', 'Super Admin kembali ke akun asli.');

        if ($this->wantsJson()) {
            return $this->response->setJSON([
                'status'  => 'success',
                'message' => 'Anda telah kembali ke akun Super Admin.',
                'user'    => [
                    'user_id'   => $session->get('user_id'),
                    'nama'      => $session->get('nama_lengkap'),
                    'nama_role' => $session->get('nama_role'),
                ],
            ]);
        }

        return redirect()->to('/dashboard')->with('success', 'Anda telah kembali ke akun Super Admin.');
    }
}
