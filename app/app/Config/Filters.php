<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Filters\CSRF;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\SecureHeaders;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;

class Filters extends BaseConfig
{
    /**
     * Alias a clases de filtros.
     */
    public array $aliases = [
        'csrf'          => CSRF::class,
        'toolbar'       => DebugToolbar::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,

        // TUS FILTROS
        'theme'         => \App\Filters\ThemeFilter::class,
        'auth'          => \App\Filters\AuthGuard::class,
        'role'          => \App\Filters\RoleGuard::class,
    ];

    /**
     * Filtros globales (todas las rutas).
     * NOTA: No activamos 'auth' global para no bloquear login/recuperación.
     */
    public array $globals = [
        'before' => [
            // 'honeypot',
            'csrf',
            'theme',
            // 'forcehttps', // <-- Habilítalo en producción si usas HTTPS
        ],
        'after' => [
            'toolbar',
            // 'secureheaders',
        ],
    ];

    /**
     * Filtros por método HTTP (opcional).
     */
    public array $methods = [
        // 'post' => ['csrf'],
    ];

    /**
     * Filtros por rutas específicas.
     */
    public array $filters = [
        // ?? Rutas que SÍ requieren sesión
        'auth' => [
            'before' => [
                // Dashboard
                'dashboard',
                'dashboard/*',

                // Módulos de tu app
                'cias',
                'cias/*',
                'users',
                'users/*',
                'perfiles',
                'perfiles/*', 
            ],
            // Puedes excluir explícitamente algo si lo necesitas:
            // 'except' => ['api/public/*'],
        ],

        // ??? RoleGuard se aplica en Routes.php con argumentos (p.ej. 'role:1,7'),
        // por eso NO lo configuramos aquí globalmente.
        // Ejemplo (NO requerido si ya lo pones en Routes.php):
        // 'role' => [
        //     'before' => [
        //         'cias/*', // y el argumento role se define en Routes.php
        //     ],
        // ],

        // (Opcional) Excepciones para recursos estáticos si hiciera falta
        // 'csrf' => [
        //     'except' => [
        //         'assets/*',
        //         'uploads/*',
        //     ],
        // ],
    ];
}