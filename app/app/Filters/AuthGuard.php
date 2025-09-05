<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class AuthGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        if (session('logged_in') && session('user_id')) {
            return; // ya hay sesión
        }

        // Intento de autologin con remember cookie
        helper('cookie');
        $cookie = get_cookie('remember');
        if ($cookie) {
            [$selector, $validator] = array_pad(explode(':', $cookie, 2), 2, null);
            if ($selector && $validator) {
                $users = new \App\Models\UserModel();
                $user  = $users->where('user_remember_selector', $selector)->first();
                if ($user && !empty($user['user_remember_expires']) && strtotime($user['user_remember_expires']) > time()) {
                    if (password_verify($validator, (string)$user['user_remember_validator_hash'])) {
                        // Reconstituir sesión
                        session()->regenerate(true);
                        session()->set([
                            'user_id'     => (int)$user['user_id'],
                            'user_name'   => $user['user_nombre'],
                            'user_email'  => $user['user_email'],
                            'user_perfil' => (string)$user['user_perfil'],
                            'logged_in'   => true,
                        ]);
                        return; // permitir
                    }
                }
            }
            // Si la cookie no es válida, borrarla
            delete_cookie('remember');
        }

        // No hay sesión ? guardar intended y redirigir a login
        session()->set('intended', current_url());
        return redirect()->to(base_url('/'));
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada
    }
}