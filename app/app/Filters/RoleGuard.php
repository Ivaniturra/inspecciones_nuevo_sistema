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

        // Roles permitidos: vienen como ['7'] o ['7,3'] o ['7|3|2']
        $allowed = [];
        if (!empty($arguments[0])) {
            $allowed = preg_split('/[,\|]/', (string)$arguments[0]); // admite coma o pipe
            $allowed = array_map('trim', $allowed);
        }

        // Si no se pasaron roles, negar por seguro
        if (empty($allowed)) {
            return redirect()->to(base_url('forbidden'));
        }

        $userPerfil = (string) (session('user_perfil') ?? session('user_perfil_id') ?? '');

        // Permitir si el perfil del usuario está en la lista
        if (! in_array($userPerfil, $allowed, true)) {
            // Opciones: 403 o redirigir a una vista/route propia
            // return service('response')->setStatusCode(403, 'No autorizado');
            return redirect()->to(base_url('forbidden'));
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada
    }
}
