<?php

 namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\EstadoModel;

class Estados extends BaseController
{
    protected $estadoModel;

    public function __construct()
    {
        $this->estadoModel = new EstadoModel();
        helper(['url', 'text']);
    }

    public function index()
    {
        try {
            // Método 1: Usar el modelo
            $estados = $this->estadoModel->findAll();
            
            // Debug temporal - descomenta esta línea
            // dd($estados);
            
            $data = [
                'title'   => 'Gestión de Estados',
                'estados' => $estados
            ];

            return view('estados/index', $data);
            
        } catch (\Exception $e) {
            // Si hay error
            echo "Error: " . $e->getMessage();
            die();
        }
    }

    public function show($id)
    {
        $estado = $this->estadoModel->find($id);
        if (!$estado) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Estado no encontrado');
        }

        return view('estados/show', [
            'title'  => 'Detalles del Estado',
            'estado' => $estado
        ]);
    }
}