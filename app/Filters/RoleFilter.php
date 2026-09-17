<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments Array of roles (e.g. ['1', '3'])
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Pastikan user sudah login terlebih dahulu
        if (! session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu.');
        }

        // Jika filter dideklarasikan tanpa argumen
        if (empty($arguments)) {
            return;
        }

        // Ambil role dari session
        $userRole = session()->get('nama_role');

        // Cek apakah role user ada di dalam daftar argumen (role yang diizinkan)
        if (! in_array((string)$userRole, $arguments)) {
            // Jika role tidak sesuai, tolak akses dan arahkan ke dashboard/halaman sebelumnya
            return redirect()->to('/dashboard')->with('error', 'Akses Ditolak: Anda tidak memiliki hak akses untuk halaman ini.');
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed.
     *
     * @param RequestInterface  $request
     * @param ResponseInterface $response
     * @param array|null        $arguments
     *
     * @return ResponseInterface|void
     */
    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // Do nothing
    }
}
