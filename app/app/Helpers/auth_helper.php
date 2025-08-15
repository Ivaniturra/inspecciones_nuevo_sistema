<?php
if (!function_exists('can')) {
    function can(string $permission): bool
    {
        // provisional: permitir todo
        return true;
    }
}