<?php
if (! function_exists('theme')) {
    function theme(): array
    {
        // Primero, valores que *podr�an* venir de la sesi�n si el login los carg�.
        $fromSession = [
            'title'         => session('app_title'),
            'brand_title'   => session('brand_title'),
            'logo'          => session('brand_logo'),
            'nav_bg'        => session('nav_bg'),
            'nav_text'      => session('nav_text'),
            'sidebar_start' => session('sidebar_start'),
            'sidebar_end'   => session('sidebar_end'),
        ];

        // Si algo no est� en sesi�n, cae al .env (sin setear nada en sesi�n)
        // Ajusta las keys a tu convenci�n: 'app.title' o 'APP_TITLE' pero s� consistente.
        return [
            'title'         => $fromSession['title']         ?? env('app.title'),
            'brand_title'   => $fromSession['brand_title']   ?? env('app.brand_title') ?? env('app.title'),
            'logo'          => $fromSession['logo']          ?? env('app.brand_logo'),
            'nav_bg'        => $fromSession['nav_bg']        ?? env('app.nav_bg'),
            'nav_text'      => $fromSession['nav_text']      ?? env('app.nav_text'),
            'sidebar_start' => $fromSession['sidebar_start'] ?? env('app.sidebar_start'),
            'sidebar_end'   => $fromSession['sidebar_end']   ?? env('app.sidebar_end'),
        ];
    }
}

if (! function_exists('theme_value')) {
    function theme_value(string $key, $default = null)
    {
        $t = theme();
        return $t[$key] ?? $default;
    }
}