<?php
if (! function_exists('theme')) {
    function theme(): array
    {
        $t = session('theme');
        return array_merge([
            'title'         => env('APP_TITLE', 'InspectZu'),
            'logo'          => base_url('assets/logo.svg'),
            'nav_bg'        => '#0d6efd',
            'nav_text'      => '#ffffff',
            'sidebar_start' => '#667eea',
            'sidebar_end'   => '#764ba2',
        ], is_array($t) ? $t : []);
    }
}

if (! function_exists('theme_set')) {
    function theme_set(array $values): void
    {
        session()->set('theme', array_merge(theme(), $values));
    }
}

if (! function_exists('theme_reset')) {
    function theme_reset(): void
    {
        session()->remove('theme');
    }
}

if (! function_exists('theme_from_company')) {
    function theme_from_company(array $company): array
    {
        return [
            'title'         => $company['display_name']    ?? env('APP_TITLE', 'InspectZu'),
            'logo'          => $company['logo_path']       ?? base_url('assets/logo.svg'),
            'nav_bg'        => $company['brand_nav_bg']    ?? '#0d6efd',
            'nav_text'      => $company['brand_nav_text']  ?? '#ffffff',
            'sidebar_start' => $company['brand_side_start']?? '#667eea',
            'sidebar_end'   => $company['brand_side_end']  ?? '#764ba2',
        ];
    }
}