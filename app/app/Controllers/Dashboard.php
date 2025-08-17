<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Dashboard extends BaseController
{
    public function index()
    {
        // Aquí puedes cargar datos reales desde modelos
        $stats = [
            'total_users'   => 15,
            'active_cias'   => 3,
            'pending_tasks' => 7,
        ];

        return view('dashboard/index', [
            'title' => 'Panel Principal',
            'stats' => $stats,
        ]);
    }
}
