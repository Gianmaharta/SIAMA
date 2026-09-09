<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserRoleModel;

class Auth extends BaseController
{
    protected $userRoleModel;

    public function __construct()
    {
        // Load the session helper
        $this->session = \Config\Services::session();
        $this->userRoleModel = new UserRoleModel();
    }

    public function index()
    {
        // Jika user sudah login, arahkan langsung ke dashboard
        if ($this->session->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        return view('auth/login');
    }

    public function process()
    {
        // Ambil input dari form
        $email = $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        // Validasi input sederhana
        if (empty($email) || empty($password)) {
            return redirect()->back()->with('error', 'Email dan Password harus diisi.');
        }

        // Cari data user berdasarkan email beserta role-nya menggunakan helper dari UserRoleModel
        $user = $this->userRoleModel->getUserByEmailWithRole($email);

        // Jika user ditemukan
        if ($user) {
            // Cek apakah akun aktif (opsional, asumsikan ada kolom is_active)
            if (isset($user['is_active']) && $user['is_active'] == 0) {
                return redirect()->back()->with('error', 'Akun Anda tidak aktif.');
            }

            // Verifikasi password (asumsi password di DB menggunakan password_hash)
            if (password_verify($password, $user['password'])) {
                // Password cocok, siapkan data session
                $sessionData = [
                    'user_id'      => $user['id_user'],
                    'email'        => $user['email'],
                    'nama_lengkap' => $user['nama'],
                    'id_role'      => $user['id_role'],
                    'nama_role'    => $user['nama_role'],
                    'id_opd'       => $user['id_opd'] ?? null, 
                    'isLoggedIn'   => true
                ];

                // Set session
                $this->session->set($sessionData);

                // Redirect ke dashboard
                return redirect()->to('/dashboard')->with('success', 'Login berhasil. Selamat datang, ' . $user['nama'] . '!');
            } else {
                // Password salah
                return redirect()->back()->with('error', 'Password yang Anda masukkan salah.');
            }
        } else {
            // Username tidak ditemukan
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        }
    }

    public function logout()
    {
        // Hapus seluruh data session
        $this->session->destroy();

        // Redirect ke halaman login
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }
}
