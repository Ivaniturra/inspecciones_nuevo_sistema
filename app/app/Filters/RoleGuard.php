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
        if (!session('logged_in') || !session('user_id')) {
            session()->set('intended', current_url());
            return redirect()->to(base_url('/'));
        }

        // Normalizar argumentos
        $allowed = ['7']; // default: solo superadmin
        $allowedTypes = []; // para tipos de perfil
        
        if (!empty($arguments)) {
            $flat = [];
            $types = [];
            
            foreach ((array)$arguments as $arg) {
                foreach (preg_split('/[,\|]/', (string)$arg) as $p) {
                    $p = trim($p);
                    if ($p === '') continue;
                    
                    // Si es numérico, es un perfil_id
                    if (is_numeric($p)) {
                        $flat[] = $p;
                    } 
                    // Si es string, es un perfil_tipo
                    else {
                        $types[] = strtolower($p);
                    }
                }
            }
            
            if ($flat) $allowed = array_values(array_unique($flat));
            if ($types) $allowedTypes = array_values(array_unique($types));
        }

        // Obtener datos del usuario
        $userPerfilId = (string) (session('user_perfil_id') ?? '');
        $userPerfilTipo = strtolower((string) (session('perfil_tipo') ?? ''));
        
        // Verificar por ID o por tipo
        $hasAccess = false;
        
        // Verificar por perfil_id
        if (in_array($userPerfilId, $allowed, true)) {
            $hasAccess = true;
        }
        
        // Verificar por perfil_tipo
        if (!$hasAccess && !empty($allowedTypes)) {
            if (in_array($userPerfilTipo, $allowedTypes, true)) {
                $hasAccess = true;
            }
        }
        
        if (!$hasAccess) {
            return redirect()->to(base_url('dashboard'))
                ->with('error', 'No tienes permisos para acceder a esta sección');
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada
    }
}