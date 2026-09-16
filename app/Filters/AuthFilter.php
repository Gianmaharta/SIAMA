<?php

namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class AuthFilter implements FilterInterface
{
    /**
     * Do whatever processing this filter needs to do.
     * By default it should not return anything during
     * normal execution. However, when an abnormal state
     * is found, it should return an instance of
     * CodeIgniter\HTTP\Response. If it does, script
     * execution will end and that Response will be
     * sent back to the client, allowing for error pages,
     * redirects, etc.
     *
     * @param RequestInterface $request
     * @param array|null       $arguments
     *
     * @return RequestInterface|ResponseInterface|string|void
     */
    public function before(RequestInterface $request, $arguments = null)
    {
        // Cek apakah session isLoggedIn bernilai true
        if (! session()->get('isLoggedIn')) {
            // Jika belum login, redirect ke halaman login dengan pesan error
            return redirect()->to('/login')->with('error', 'Silakan login terlebih dahulu untuk mengakses halaman tersebut.');
        }

        // Cek force change password
        if (session()->get('is_default_password') == 1) {
            // Biarkan lewat jika mengakses route /change-password
            $uri = $request->getUri()->getPath();
            if ($uri !== 'change-password' && $uri !== 'change-password/process' && $uri !== 'logout') {
                return redirect()->to('/change-password')->with('error', 'Anda harus mengganti password default Anda terlebih dahulu sebelum dapat menggunakan sistem.');
            }
        }
    }

    /**
     * Allows After filters to inspect and modify the response
     * object as needed. This method does not allow any way
     * to stop execution of other after filters, short of
     * throwing an Exception or Error.
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
