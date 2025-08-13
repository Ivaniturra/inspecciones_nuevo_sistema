<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\PerfilModel;

class Perfiles extends BaseController
{
    protected $perfilModel;

    public function __construct()
    {
        $this->perfilModel = new PerfilModel();
    }

    /**
     * Mostrar listado de perfiles
     */
    public function index()
    {
        $data = [
            'title' => 'Gestión de Perfiles',
            'perfiles' => $this->perfilModel->getPerfilesWithUserCount()
        ];

        return view('perfiles/index', $data);
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $data = [
            'title' => 'Nuevo Perfil',
            'permisosCompania' => $this->perfilModel->getPermisosDisponibles('compania'),
            'permisosInternos' => $this->perfilModel->getPermisosDisponibles('interno')
        ];

        return view('perfiles/create', $data);
    }

    /**
     * Procesar creación de perfil
     */
    public function store()
    {
        // Validación básica
        if (!$this->validate([
            'perfil_nombre' => 'required|min_length[3]|max_length[100]',
            'perfil_tipo' => 'required|in_list[compania,interno]',
            'perfil_nivel' => 'required|integer|greater_than[0]|less_than[5]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Procesar permisos
        $permisos = [];
        $permisosPost = $this->request->getPost('permisos') ?? [];
        
        foreach ($permisosPost as $permiso) {
            $permisos[$permiso] = true;
        }

        // Guardar datos
        $data = [
            'perfil_nombre' => $this->request->getPost('perfil_nombre'),
            'perfil_tipo' => $this->request->getPost('perfil_tipo'),
            'perfil_descripcion' => $this->request->getPost('perfil_descripcion'),
            'perfil_permisos' => $permisos,
            'perfil_nivel' => $this->request->getPost('perfil_nivel'),
            'perfil_habil' => $this->request->getPost('perfil_habil') ?? 1
        ];

        if ($this->perfilModel->save($data)) {
            return redirect()->to('/perfiles')->with('success', 'Perfil creado exitosamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al crear el perfil');
        }
    }

    /**
     * Mostrar detalles de un perfil
     */
    public function show($id)
    {
        $perfil = $this->perfilModel->find($id);
        
        if (!$perfil) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Perfil no encontrado');
        }

        $data = [
            'title' => 'Detalles del Perfil',
            'perfil' => $perfil,
            'permisosDisponibles' => $this->perfilModel->getPermisosDisponibles($perfil['perfil_tipo'])
        ];

        return view('perfiles/show', $data);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $perfil = $this->perfilModel->find($id);
        
        if (!$perfil) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Perfil no encontrado');
        }

        $data = [
            'title' => 'Editar Perfil',
            'perfil' => $perfil,
            'permisosCompania' => $this->perfilModel->getPermisosDisponibles('compania'),
            'permisosInternos' => $this->perfilModel->getPermisosDisponibles('interno')
        ];

        return view('perfiles/edit', $data);
    }

    /**
     * Procesar actualización de perfil
     */
    public function update($id)
    {
        $perfil = $this->perfilModel->find($id);
        
        if (!$perfil) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Perfil no encontrado');
        }

        // Validación
        if (!$this->validate([
            'perfil_nombre' => 'required|min_length[3]|max_length[100]',
            'perfil_tipo' => 'required|in_list[compania,interno]',
            'perfil_nivel' => 'required|integer|greater_than[0]|less_than[5]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Procesar permisos
        $permisos = [];
        $permisosPost = $this->request->getPost('permisos') ?? [];
        
        foreach ($permisosPost as $permiso) {
            $permisos[$permiso] = true;
        }

        // Actualizar datos
        $data = [
            'perfil_nombre' => $this->request->getPost('perfil_nombre'),
            'perfil_tipo' => $this->request->getPost('perfil_tipo'),
            'perfil_descripcion' => $this->request->getPost('perfil_descripcion'),
            'perfil_permisos' => $permisos,
            'perfil_nivel' => $this->request->getPost('perfil_nivel'),
            'perfil_habil' => $this->request->getPost('perfil_habil')
        ];

        if ($this->perfilModel->update($id, $data)) {
            return redirect()->to('/perfiles')->with('success', 'Perfil actualizado exitosamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el perfil');
        }
    }

    /**
     * Eliminar perfil
     */
    public function delete($id)
    {
        $perfil = $this->perfilModel->find($id);
        
        if (!$perfil) {
            return redirect()->to('/perfiles')->with('error', 'Perfil no encontrado');
        }

        // Verificar si se puede eliminar
        if (!$this->perfilModel->canDelete($id)) {
            return redirect()->to('/perfiles')->with('error', 'No se puede eliminar el perfil porque tiene usuarios asociados');
        }

        if ($this->perfilModel->delete($id)) {
            return redirect()->to('/perfiles')->with('success', 'Perfil eliminado exitosamente');
        } else {
            return redirect()->to('/perfiles')->with('error', 'Error al eliminar el perfil');
        }
    }

    /**
     * Cambiar estado de perfil (AJAX)
     */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/perfiles');
        }

        if ($this->perfilModel->toggleStatus($id)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Estado actualizado correctamente'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al actualizar el estado'
            ]);
        }
    }

    /**
     * Obtener perfiles por tipo (AJAX)
     */
    public function getByTipo($tipo = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/perfiles');
        }

        $perfiles = $this->perfilModel->getPerfilesByTipo($tipo);
        
        return $this->response->setJSON($perfiles);
    }

    /**
     * Obtener perfiles para select (AJAX)
     */
    public function getSelect()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/perfiles');
        }

        $tipo = $this->request->getGet('tipo');
        $perfiles = $this->perfilModel->getPerfilesByTipo($tipo);
        
        return $this->response->setJSON($perfiles);
    }
}