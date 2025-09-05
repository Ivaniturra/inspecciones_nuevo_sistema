<?php

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

// === PÚBLICAS ===
$routes->get('/',        'Auth::login');
$routes->post('login',   'Auth::attempt');
$routes->get('logout',   'Auth::logout');
$routes->get('forgot',        'Auth::forgot');       // formulario "olvidaste"
$routes->post('forgot',       'Auth::sendReset');    // procesa envío del mail
$routes->get('reset/(:segment)', 'Auth::reset/$1'); // formulario para nueva clave
$routes->post('reset',        'Auth::processReset'); // procesa nueva clave

// === PROTEGIDAS ===
$routes->group('', ['filter' => 'auth'], static function($routes) {

    $routes->get('dashboard', 'Dashboard::index', ['as' => 'dashboard']);

    // CIAS (solo super admin = 7)
    $routes->group('cias', ['filter' => 'role:7'], static function($routes) {
        $routes->get('/',                 'Cias::index');
        $routes->get('create',            'Cias::create');
        $routes->post('store',            'Cias::store');
        $routes->get('show/(:num)',       'Cias::show/$1');
        $routes->get('edit/(:num)',       'Cias::edit/$1');
        $routes->match(['POST','PUT'],    'update/(:num)', 'Cias::update/$1');
        $routes->match(['POST','DELETE'], 'delete/(:num)', 'Cias::delete/$1');
        $routes->post('toggleStatus/(:num)', 'Cias::toggleStatus/$1');
    });

    // PERFILES
    $routes->group('perfiles', ['filter' => 'role:7'], static function($routes) {
        $routes->get('/',                 'Perfiles::index');
        $routes->get('create',            'Perfiles::create');
        $routes->post('store',            'Perfiles::store');
        $routes->get('show/(:num)',       'Perfiles::show/$1');
        $routes->get('edit/(:num)',       'Perfiles::edit/$1');
        $routes->match(['POST','PUT'],    'update/(:num)', 'Perfiles::update/$1');
        $routes->match(['POST','DELETE'], 'delete/(:num)', 'Perfiles::delete/$1');
        $routes->post('toggleStatus/(:num)', 'Perfiles::toggleStatus/$1');
        $routes->get('getByTipo/(:segment)', 'Perfiles::getByTipo/$1');
        $routes->get('getSelect',         'Perfiles::getSelect');
    });

    // USERS (3,7)
    $routes->group('users',['filter' => 'role:3,7'], static function($routes) {
        $routes->get('/',                 'Users::index');
        $routes->get('create',            'Users::create');
        $routes->post('store',            'Users::store');
        $routes->get('show/(:num)',       'Users::show/$1');
        $routes->get('edit/(:num)',       'Users::edit/$1');
        $routes->match(['POST','PUT'],    'update/(:num)', 'Users::update/$1');
        $routes->match(['POST','DELETE'], 'delete/(:num)', 'Users::delete/$1');

        $routes->post('toggleStatus/(:num)', 'Users::toggleStatus/$1');
        $routes->post('resetPassword/(:num)','Users::resetPassword/$1');
        $routes->get('getByCompany/(:num)',  'Users::getByCompany/$1');
        $routes->get('getStats',             'Users::getStats');
    });
    // Comentarios (Web)
    $routes->group('comentarios', ['namespace' => 'App\Controllers', 'filter' => 'role:3,7'], static function($routes) {
        $routes->get('/',                 'Comentarios::index');              // listado
        $routes->get('create',            'Comentarios::create');             // formulario crear
        $routes->post('store',            'Comentarios::store');              // guarda nuevo
        $routes->get('show/(:num)',       'Comentarios::show/$1');            // detalle
        $routes->get('edit/(:num)',       'Comentarios::edit/$1');            // formulario editar
        $routes->match(['post','put'],    'update/(:num)', 'Comentarios::update/$1'); // actualiza
        $routes->match(['post','delete'], 'delete/(:num)', 'Comentarios::delete/$1'); // elimina
        
        // ✅ CORREGIDO: Quitar "comentarios/" duplicado
        $routes->post('toggleStatus/(:num)', 'Comentarios::toggleStatus/$1');
        
        // Rutas AJAX adicionales (si las necesitas)
        $routes->post('toggleDevuelve/(:num)',     'Comentarios::toggleDevuelve/$1'); 
        $routes->post('toggleEnviarCorreo/(:num)', 'Comentarios::toggleEnviarCorreo/$1');
    });
    // Rutas para Estados (solo lectura)
    $routes->group('estados', ['namespace' => 'App\Controllers', 'filter' => 'role:3,7'], static function($routes) {
        $routes->get('/', 'Estados::index');
        $routes->get('show/(:num)', 'Estados::show/$1');
        $routes->post('getSelect', 'Estados::getSelect'); // Para AJAX si lo necesitas
    });
   
    $routes->group('valores-comunas',['namespace' => 'App\Controllers', 'filter' => 'role:3,7'], static function($routes) {
         $routes->get('/',                 'ValoresComunas::index', ['filter' => 'role:3,7']);
        $routes->get('create',            'ValoresComunas::create', ['filter' => 'role:3,7']);
        $routes->post('store',            'ValoresComunas::store', ['filter' => 'role:3,7']);
        $routes->get('edit/(:num)',       'ValoresComunas::edit/$1', ['filter' => 'role:3,7']);
        $routes->post('update/(:num)',    'ValoresComunas::update/$1', ['filter' => 'role:3,7']);
        $routes->get('show/(:num)',       'ValoresComunas::show/$1', ['filter' => 'role:3,7']);
        $routes->get('delete/(:num)',     'ValoresComunas::delete/$1', ['filter' => 'role:3,7']);
        
        // ✅ Ruta AJAX sin filtro restrictivo (solo autenticación básica si la necesitas)
        $routes->post('toggle/(:num)',    'ValoresComunas::toggleStatus/$1');

        // AJAX dependientes sin filtro
        $routes->get('getProvinciasByRegion/(:num)',  'ValoresComunas::getProvinciasByRegion/$1');
        $routes->get('getComunasByProvincia/(:num)',  'ValoresComunas::getComunasByProvincia/$1');
        $routes->get('getComunasByRegion/(:num)',     'ValoresComunas::getComunasByRegion/$1');
    });
    $routes->group('TipoVehiculos',['namespace' => 'App\Controllers', 'filter' => 'role:3,7'], static function($routes) {
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
        $routes->get('getSelCorredorect', 'TipoVehiculos::getSelect');
        
    });
    $routes->group('corredor', ['filter' => 'role:9,10'], static function($routes) {
    $routes->get('/', 'Corredor\Dashboard::index', ['as' => 'corredor.dashboard']);
     
    // etc.
    });
});
