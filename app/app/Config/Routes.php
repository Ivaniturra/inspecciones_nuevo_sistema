<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */ 
// Rutas para Cia
 
$routes->group('cias', function($routes) {
    $routes->get('/', 'Cias::index');
    $routes->get('create', 'Cias::create');
    $routes->post('store', 'Cias::store');
    $routes->get('show/(:num)', 'Cias::show/$1');
    $routes->get('edit/(:num)', 'Cias::edit/$1');

    // Cualquiera de estas dos líneas funciona:
    $routes->put('update/(:num)', 'Cias::update/$1'); 
    // ó: $routes->match(['post','put'], 'update/(:num)', 'Cias::update/$1');

    $routes->delete('delete/(:num)', 'Cias::delete/$1');
    $routes->post('toggleStatus/(:num)', 'Cias::toggleStatus/$1');
});
// Rutas para Perfiles
$routes->group('perfiles', function($routes) {
    $routes->get('/', 'Perfiles::index');
    $routes->get('create', 'Perfiles::create');
    $routes->post('store', 'Perfiles::store');
    $routes->get('show/(:num)', 'Perfiles::show/$1');
    $routes->get('edit/(:num)', 'Perfiles::edit/$1'); 
    $routes->delete('delete/(:num)', 'Perfiles::delete/$1');
    $routes->post('toggleStatus/(:num)', 'Perfiles::toggleStatus/$1');
    $routes->get('getByTipo/(:segment)', 'Perfiles::getByTipo/$1');
    $routes->get('getSelect', 'Perfiles::getSelect');
    $routes->match(['POST','PUT'], 'update/(:num)', 'Perfiles::update/$1');

});
$routes->group('users', static function($routes) {
    $routes->get('/', 'Users::index');
    $routes->get('create', 'Users::create');
    $routes->post('store', 'Users::store');
    $routes->get('show/(:num)', 'Users::show/$1');
    $routes->get('edit/(:num)', 'Users::edit/$1');

    // Acepta PUT real y POST (spoofing o compatibilidad)
    $routes->match(['PUT','POST'], 'update/(:num)', 'Users::update/$1');

    // Si DELETE real diera problemas en tu hosting, añade POST también:
    // $routes->match(['DELETE','POST'], 'delete/(:num)', 'Users::delete/$1');

    $routes->delete('delete/(:num)', 'Users::delete/$1');
    $routes->post('toggleStatus/(:num)', 'Users::toggleStatus/$1');
    $routes->post('resetPassword/(:num)', 'Users::resetPassword/$1');
    $routes->get('getByCompany/(:num)', 'Users::getByCompany/$1');
    $routes->get('getStats', 'Users::getStats');
});