<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (! session('logged_in') || ! session('user_id')) {
            // guarda a dónde quería ir para redirigir luego del login
            session()->set('intended', current_url());
            return redirect()->to(base_url('/')); // tu login está en /
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada
    }
}