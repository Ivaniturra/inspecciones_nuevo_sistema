<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class ThemeFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        // Ya no escribimos en sesión. Si quieres,
        // puedes exponer el theme a todas las vistas:
        helper('theme');
        service('renderer')->setVar('theme', theme());
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        // nada
    }
}