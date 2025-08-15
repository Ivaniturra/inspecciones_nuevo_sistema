<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ThemeFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();

        // Si no hay tema en sesión, setea defaults (puedes ajustar a tu gusto)
        if (! $session->has('theme')) {
            $session->set('theme', [
                'title'         => env('APP_TITLE', 'InspectZu'),
                'brand_title'   => env('APP_BRAND_TITLE', env('APP_TITLE', 'InspectZu')),
                'logo'          => base_url('assets/logo.svg'),
                'nav_bg'        => '#0d6efd',
                'nav_text'      => '#ffffff',
                'sidebar_start' => '#667eea',
                'sidebar_end'   => '#764ba2',
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada
    }
}