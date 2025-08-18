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
});
