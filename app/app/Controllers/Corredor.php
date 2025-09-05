<?php
// app/Controllers/Corredor/Dashboard.php
namespace App\Controllers\Corredor;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function __construct()
    {
        // Verificar autenticación
        if (!session('logged_in')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }

        // Verificar que sea corredor - mejorar la validación
        $perfilTipo = session('perfil_tipo');
        $perfilId = session('user_perfil_id');
        
        if ($perfilTipo !== 'corredor' && $perfilId != 8) {
            // Mejor manejo de errores
            session()->setFlashdata('error', 'No tienes permisos para acceder a esta sección.');
            header('Location: ' . base_url('dashboard'));
            exit;
        }
    }

    public function index()
    {
        // Obtener información del corredor
        $corredorId = session('corredor_id');
        
        // Puedes cargar modelos específicos aquí
        // $solicitudesModel = new \App\Models\SolicitudesModel();
        // $clientesModel = new \App\Models\ClientesModel();
        
        $data = [
            'title' => 'Dashboard Corredor',
            'corredor_id' => $corredorId,
            'corredor_nombre' => session('brand_title'),
            
            // Datos específicos del dashboard
            'stats' => [
                'solicitudes_pendientes' => 0, // Implementar
                'clientes_activos' => 0,       // Implementar  
                'comisiones_mes' => 0,         // Implementar
            ],
            
            // Branding personalizado
            'brand_title' => session('brand_title'),
            'brand_logo' => session('brand_logo'),
            'nav_bg' => session('nav_bg'),
        ];

        return view('pagina_corredor', $data);
    }
    
    /**
     * Verificar si el usuario actual es corredor
     */
    protected function isCorrector(): bool
    {
        return session('perfil_tipo') === 'corredor' || session('user_perfil_id') == 8;
    }
}