// app/Controllers/Corredor/Dashboard.php
<?php
namespace App\Controllers\Corredor;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function __construct()
    {
        // Verificar que sea corredor
        if (session('perfil_tipo') !== 'corredor' && session('user_perfil_id') != 8) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException();
        }
    }

    public function index()
    {
        $data = [
            'title' => 'Dashboard Corredor',
            'corredor_id' => session('corredor_id'),
            'corredor_nombre' => session('brand_title') 
        ];

        return view('corredor/dashboard', $data);
    } 
}