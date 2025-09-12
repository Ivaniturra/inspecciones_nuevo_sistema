 <?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// ===================================================
// RUTAS PÚBLICAS
// ===================================================
$routes->get('/', 'Auth::login');
$routes->post('login', 'Auth::attempt');
$routes->get('logout', 'Auth::logout');
$routes->get('forgot', 'Auth::forgot');
$routes->post('forgot', 'Auth::sendReset');
$routes->get('reset/(:segment)', 'Auth::reset/$1');
$routes->post('reset', 'Auth::processReset');

// ===================================================
// RUTAS PROTEGIDAS (requieren autenticación)
// ===================================================
$routes->group('', ['filter' => 'auth'], static function($routes) {

    // ===================================================
    // DASHBOARD - REDIRECCIÓN INTELIGENTE SEGÚN ROL
    // ===================================================
    $routes->get('dashboard', function() {
        $userRole = session('user_perfil_id');
        
        switch($userRole) {
            case 7: // Super admin
                return view('dashboard/index'); // Vista administrativa
            case 3: // Inspector/Admin
                return redirect()->to('/inspecciones');
            case 9:
            case 10: // Corredores
                return redirect()->to('/corredor');
            default:
                return redirect()->to('/corredor'); // Por defecto al corredor
        }
    });

    // ===================================================
    // CORREDORES - GESTIÓN ADMINISTRATIVA (solo super admin)
    // ===================================================
    $routes->group('corredores', ['filter' => 'role:7'], static function($routes) {
        $routes->get('/', 'Corredores::index');
        $routes->get('create', 'Corredores::create');
        $routes->post('store', 'Corredores::store');
        $routes->get('show/(:num)', 'Corredores::show/$1');
        $routes->get('edit/(:num)', 'Corredores::edit/$1');
        $routes->match(['POST','PUT'], 'update/(:num)', 'Corredores::update/$1');
        $routes->match(['POST','DELETE'], 'delete/(:num)', 'Corredores::delete/$1');
        $routes->post('toggleStatus/(:num)', 'Corredores::toggleStatus/$1');
    });

    // ===================================================
    // CORREDOR - DASHBOARD Y OPERACIONES (corredores y admins)
    // ===================================================
     $routes->group('corredor', ['filter' => 'role:9,10'], static function($routes) {
        // Dashboard principal
        $routes->get('/', 'Corredor::index');
        $routes->get('dashboard', 'Corredor::index');
        
        // CRUD de Inspecciones
        $routes->get('create', 'Corredor::create');
        $routes->post('store', 'Corredor::store');
        $routes->get('show/(:num)', 'Corredor::show/$1');
        $routes->get('edit/(:num)', 'Corredor::edit/$1');
        $routes->post('update/(:num)', 'Corredor::update/$1'); 

        $routes->get('delete/(:num)', 'Corredor::delete/$1');
        
        // Rutas AJAX
        $routes->get('filter-status', 'Corredor::filterByStatus');
        $routes->get('stats', 'Corredor::getStats');
        
        // Rutas de perfil del corredor
        $routes->get('perfil', 'Corredor::perfil');
        $routes->post('actualizar-perfil', 'Corredor::actualizarPerfil');
        $routes->post('actualizar-branding', 'Corredor::actualizarBranding');
        
        // Reportes del corredor
        $routes->get('reportes', 'Corredor::reportes');
        $routes->get('reporte-mensual/(:num)/(:num)', 'Corredor::reporteMensual/$1/$2');
        $routes->get('export-excel', 'Corredor::exportExcel');
        $routes->get('export-pdf/(:num)', 'Corredor::exportPdf/$1');
    });

    // ===================================================
    // INSPECCIONES - GESTIÓN ADMINISTRATIVA COMPLETA
    // ===================================================
    $routes->group('inspecciones', ['filter' => 'role:3,7'], static function($routes) {
        $routes->get('/', 'Inspecciones::index');
        $routes->get('create', 'Inspecciones::create');
        $routes->post('store', 'Inspecciones::store');
        $routes->get('show/(:num)', 'Inspecciones::show/$1'); 
        $routes->get('edit/(:num)', 'Inspecciones::edit/$1');
        $routes->post('update/(:num)', 'Inspecciones::update/$1');
        $routes->match(['POST','DELETE'], 'delete/(:num)', 'Inspecciones::delete/$1');
        
        // Gestión de bitácora y estados
        $routes->post('agregarComentario', 'Inspecciones::agregarComentario');
        $routes->post('cambiarEstado', 'Inspecciones::cambiarEstado');
        $routes->delete('eliminarComentario/(:num)', 'Inspecciones::eliminarComentario/$1');
        
        // Reportes administrativos
        $routes->get('reportes', 'Inspecciones::reportes');
        $routes->get('estadisticas', 'Inspecciones::estadisticas');
        $routes->get('export/(:segment)', 'Inspecciones::export/$1');
    });

    // ===================================================
    // COMPAÑÍAS DE SEGUROS (solo super admin)
    // ===================================================
    $routes->group('cias', ['filter' => 'role:7'], static function($routes) {
        $routes->get('/', 'Cias::index');
        $routes->get('create', 'Cias::create');
        $routes->post('store', 'Cias::store');
        $routes->get('show/(:num)', 'Cias::show/$1');
        $routes->get('edit/(:num)', 'Cias::edit/$1');
        $routes->match(['POST','PUT'], 'update/(:num)', 'Cias::update/$1');
        $routes->match(['POST','DELETE'], 'delete/(:num)', 'Cias::delete/$1');
        $routes->post('toggleStatus/(:num)', 'Cias::toggleStatus/$1');
    });

    // ===================================================
    // PERFILES DE USUARIOS (solo super admin)
    // ===================================================
    $routes->group('perfiles', ['filter' => 'role:7'], static function($routes) {
        $routes->get('/', 'Perfiles::index');
        $routes->get('create', 'Perfiles::create');
        $routes->post('store', 'Perfiles::store');
        $routes->get('show/(:num)', 'Perfiles::show/$1');
        $routes->get('edit/(:num)', 'Perfiles::edit/$1');
        $routes->match(['POST','PUT'], 'update/(:num)', 'Perfiles::update/$1');
        $routes->match(['POST','DELETE'], 'delete/(:num)', 'Perfiles::delete/$1');
        $routes->post('toggleStatus/(:num)', 'Perfiles::toggleStatus/$1');
        
        // AJAX
        $routes->get('getByTipo/(:segment)', 'Perfiles::getByTipo/$1');
        $routes->get('getSelect', 'Perfiles::getSelect');
    });

    // ===================================================
    // USUARIOS (admin e inspectores)
    // ===================================================
    $routes->group('users', ['filter' => 'role:3,7'], static function($routes) {
        $routes->get('/', 'Users::index');
        $routes->get('create', 'Users::create');
        $routes->post('store', 'Users::store');
        $routes->get('show/(:num)', 'Users::show/$1');
        $routes->get('edit/(:num)', 'Users::edit/$1');
        $routes->match(['POST','PUT'], 'update/(:num)', 'Users::update/$1');
        $routes->match(['POST','DELETE'], 'delete/(:num)', 'Users::delete/$1');
        
        // Gestión de usuarios
        $routes->post('toggleStatus/(:num)', 'Users::toggleStatus/$1');
        $routes->post('resetPassword/(:num)', 'Users::resetPassword/$1');
        
        // AJAX
        $routes->get('getByCompany/(:num)', 'Users::getByCompany/$1');
        $routes->get('getStats', 'Users::getStats');
    });

    // ===================================================
    // COMENTARIOS/BITÁCORA (admin e inspectores)
    // ===================================================
    $routes->group('comentarios', ['namespace' => 'App\Controllers', 'filter' => 'role:3,7'], static function($routes) {
        $routes->get('/', 'Comentarios::index');
        $routes->get('create', 'Comentarios::create');
        $routes->post('store', 'Comentarios::store');
        $routes->get('show/(:num)', 'Comentarios::show/$1');
        $routes->get('edit/(:num)', 'Comentarios::edit/$1');
        $routes->match(['post','put'], 'update/(:num)', 'Comentarios::update/$1');
        $routes->match(['post','delete'], 'delete/(:num)', 'Comentarios::delete/$1');
        
        // Gestión de estados
        $routes->post('toggleStatus/(:num)', 'Comentarios::toggleStatus/$1');
        $routes->post('toggleDevuelve/(:num)', 'Comentarios::toggleDevuelve/$1'); 
        $routes->post('toggleEnviarCorreo/(:num)', 'Comentarios::toggleEnviarCorreo/$1');
    });

    // ===================================================
    // ESTADOS (solo lectura para admin e inspectores)
    // ===================================================
    $routes->group('estados', ['namespace' => 'App\Controllers', 'filter' => 'role:3,7'], static function($routes) {
        $routes->get('/', 'Estados::index');
        $routes->get('show/(:num)', 'Estados::show/$1');
        $routes->post('getSelect', 'Estados::getSelect');
    });

    // ===================================================
    // VALORES COMUNAS (admin e inspectores)
    // ===================================================
    $routes->group('valores-comunas', ['namespace' => 'App\Controllers', 'filter' => 'role:3,7'], static function($routes) {
        $routes->get('/', 'ValoresComunas::index');
        $routes->get('create', 'ValoresComunas::create');
        $routes->post('store', 'ValoresComunas::store');
        $routes->get('edit/(:num)', 'ValoresComunas::edit/$1');
        $routes->post('update/(:num)', 'ValoresComunas::update/$1');
        $routes->get('show/(:num)', 'ValoresComunas::show/$1');
        $routes->get('delete/(:num)', 'ValoresComunas::delete/$1');
        
        // AJAX sin filtro restrictivo
        $routes->post('toggle/(:num)', 'ValoresComunas::toggleStatus/$1');
        $routes->get('getProvinciasByRegion/(:num)', 'ValoresComunas::getProvinciasByRegion/$1');
        $routes->get('getComunasByProvincia/(:num)', 'ValoresComunas::getComunasByProvincia/$1');
        $routes->get('getComunasByRegion/(:num)', 'ValoresComunas::getComunasByRegion/$1');
    });

    // ===================================================
    // TIPOS DE VEHÍCULOS (admin e inspectores)
    // ===================================================
    $routes->group('TipoVehiculos', ['namespace' => 'App\Controllers', 'filter' => 'role:3,7'], static function($routes) {
        $routes->get('/', 'TipoVehiculos::index');
        $routes->get('create', 'TipoVehiculos::create');
        $routes->post('store', 'TipoVehiculos::store');
        $routes->get('show/(:num)', 'TipoVehiculos::show/$1');
        $routes->get('edit/(:num)', 'TipoVehiculos::edit/$1');
        $routes->post('update/(:num)', 'TipoVehiculos::update/$1');
        $routes->put('update/(:num)', 'TipoVehiculos::update/$1');
        $routes->delete('delete/(:num)', 'TipoVehiculos::delete/$1');
        $routes->post('delete/(:num)', 'TipoVehiculos::delete/$1');
        $routes->post('toggleStatus/(:num)', 'TipoVehiculos::toggleStatus/$1');
        $routes->get('getSelect', 'TipoVehiculos::getSelect');
    });

    // ===================================================
    // API ENDPOINTS (sin filtros de rol adicionales)
    // ===================================================
    $routes->group('api', static function($routes) {
        $routes->get('comunas/search', 'Api\ComunasController::search');
        $routes->get('estados/list', 'Api\EstadosController::list');
        $routes->get('cias/search', 'Api\CiasController::search');
        $routes->post('inspecciones/status', 'Api\InspeccionesController::updateStatus');
    });

    // ===================================================
    // RUTAS DE REDIRECCIÓN Y COMPATIBILIDAD
    // ===================================================
    
    // Redirecciones para compatibilidad con URLs anteriores
    $routes->get('inspecciones-old', 'Corredor::index');
    $routes->get('dashboard-corredor', 'Corredor::index');
    
    // Redirección por defecto según rol
    $routes->get('home', function() {
        $userRole = session('user_perfil_id');
        
        switch($userRole) {
            case 7: // Super admin
                return redirect()->to('/dashboard');
            case 3: // Inspector/Admin
                return redirect()->to('/inspecciones');
            case 9:
            case 10: // Corredores
                return redirect()->to('/corredor');
            default:
                return redirect()->to('/logout');
        }
    });

});

// ===================================================
// MANEJO DE ERRORES
// ===================================================

// Ruta para páginas no encontradas
$routes->set404Override(function() {
    if (session('logged_in')) {
        // Usuario autenticado - mostrar 404 personalizado
        return view('errors/404_custom');
    } else {
        // Usuario no autenticado - redirigir al login
        return redirect()->to('/');
    }
});

// ===================================================
// RUTAS DE DESARROLLO (solo en environment de development)
// ===================================================
if (ENVIRONMENT === 'development') {
    $routes->group('dev', static function($routes) {
        $routes->get('test-email', 'Dev\TestController::testEmail');
        $routes->get('test-db', 'Dev\TestController::testDatabase');
        $routes->get('phpinfo', function() { phpinfo(); });
    });
}