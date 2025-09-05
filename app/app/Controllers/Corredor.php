<?php
namespace App\Controllers;
class Corredor extends BaseController 
{
    public function __construct()
    {
        // Verificar autenticación
        if (!session('logged_in')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }

        // Verificar que sea corredor
        $perfilTipo = session('perfil_tipo');
        $perfilId = session('user_perfil_id');
         
    }

    public function index()
    {
        // Obtener información del corredor
        $corredorId = session('corredor_id');
        
        $data = [
            'title' => 'Dashboard Corredor',
            'corredor_id' => $corredorId,
            'corredor_nombre' => session('brand_title'),
            
            // Estadísticas del dashboard
            'stats' => [
                'solicitudes_pendientes' => 5,
                'clientes_activos' => 23,
                'comisiones_mes' => 850000,
            ],
            
            // Branding personalizado
            'brand_title' => session('brand_title'),
            'brand_logo' => session('brand_logo'),
            'nav_bg' => session('nav_bg'),
        ];

        return view('pagina_corredor/index', $data); 
    }
} // ← ASEGÚRATE DE QUE ESTÉ ESTA LLAVE DE CIERRE