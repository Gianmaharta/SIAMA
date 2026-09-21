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
        helper('logger');

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
            // Cek apakah akun aktif
            if (isset($user['is_active']) && $user['is_active'] == 0) {
                return redirect()->back()->with('error', 'Akun Anda tidak aktif.');
            }

            // Verifikasi password
            if (password_verify($password, $user['password'])) {
                // Password cocok, siapkan data session
                $sessionData = [
                    'user_id'             => $user['id_user'],
                    'email'               => $user['email'],
                    'nama_lengkap'        => $user['nama'],
                    'id_role'             => $user['id_role'],
                    'nama_role'           => $user['nama_role'],
                    'id_opd'              => $user['id_opd'] ?? null,
                    'id_bidang'           => $user['id_bidang'] ?? null,
                    'isLoggedIn'          => true,
                    'is_default_password' => $user['is_default_password'] ?? 0
                ];

                // Set session
                $this->session->set($sessionData);
                
                log_access($user['id_user'], 'login');

                if ($sessionData['is_default_password'] == 1) {
                    return redirect()->to('/change-password');
                }

                return redirect()->to('/dashboard')->with('success', 'Login berhasil. Selamat datang, ' . $user['nama'] . '!');
            } else {
                if (isset($user['id_user'])) {
                    log_access($user['id_user'], 'failed_login');
                }
                return redirect()->back()->with('error', 'Password yang Anda masukkan salah.');
            }
        } else {
            return redirect()->back()->with('error', 'Email tidak ditemukan.');
        }
    }

    public function logout()
    {
        helper('logger');
        if ($this->session->has('user_id')) {
            log_access($this->session->get('user_id'), 'logout');
        }

        // Hapus seluruh data session
        $this->session->destroy();

        // Redirect ke halaman login
        return redirect()->to('/login')->with('success', 'Anda telah berhasil logout.');
    }

    public function changePassword()
    {
        if (! $this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }
        return view('auth/change_password');
    }

    public function processChangePassword()
    {
        if (! $this->session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $newPassword = (string) $this->request->getPost('new_password');
        $confirmPassword = (string) $this->request->getPost('confirm_password');

        if (empty($newPassword) || empty($confirmPassword)) {
            return redirect()->back()->with('error', 'Semua kolom harus diisi.');
        }

        if ($newPassword !== $confirmPassword) {
            return redirect()->back()->with('error', 'Konfirmasi password tidak cocok.');
        }

        if (strlen($newPassword) < 8) {
            return redirect()->back()->with('error', 'Password minimal 8 karakter.');
        }

        $userModel = new \App\Models\UserModel();
        $userId = $this->session->get('user_id');

        $userModel->update($userId, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT),
            'is_default_password' => 0
        ]);

        $this->session->set('is_default_password', 0);
        helper('logger');
        log_activity('Users', 'update', 'User mengganti password default');

        return redirect()->to('/dashboard')->with('success', 'Password berhasil diubah. Selamat datang di SIAMA!');
    }
}
