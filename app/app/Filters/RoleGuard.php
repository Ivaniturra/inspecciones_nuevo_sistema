<?php

namespace App\Filters;

use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\Filters\FilterInterface;

class RoleGuard implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Debe estar autenticado
        if (! session('logged_in') || ! session('user_id')) {
            session()->set('intended', current_url());
            return redirect()->to(base_url('/'));
        }

        // --- Normalizar argumentos: soporta ['3,7'] y ['3','7'] indistintamente ---
        $allowed = ['7']; // default si no pasas nada: solo superadmin
        if (!empty($arguments)) {
            $flat = [];
            foreach ((array)$arguments as $arg) {
                foreach (preg_split('/[,\|]/', (string)$arg) as $p) {
                    $p = trim($p);
                    if ($p !== '') $flat[] = $p;
                }
            }
            if ($flat) $allowed = array_values(array_unique($flat));
        }

        // Por si quieres ver qué llega realmente:
        // log_message('debug', 'RoleGuard args='.json_encode($arguments).' allowed='.json_encode($allowed));

        $userPerfil = (string) (session('user_perfil') ?? session('user_perfil_id') ?? '');

        if (!in_array($userPerfil, $allowed, true)) {
            return redirect()->to(base_url('forbidden'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada
    }
}
