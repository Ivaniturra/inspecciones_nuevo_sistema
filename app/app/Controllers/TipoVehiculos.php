<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TipoVehiculoModel;

class TipoVehiculos extends BaseController
{
    protected $tipoVehiculoModel;

    public function __construct()
    {
        $this->tipoVehiculoModel = new TipoVehiculoModel();
        helper(['url', 'text']);
    }

    /**
     * Listado de tipos de vehículo
     */
    public function index()
    {
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 10;

        $buscar = trim((string)$this->request->getGet('q'));
        $estado = $this->request->getGet('estado'); // 'activo', 'inactivo', o null para todos

        $builder = $this->tipoVehiculoModel->orderBy('tipo_vehiculo_nombre', 'ASC');

        // Filtro por estado
        if ($estado === 'activo') {
            $builder->where('tipo_vehiculo_activo', 1);
        } elseif ($estado === 'inactivo') {
            $builder->where('tipo_vehiculo_activo', 0);
        }

        // Filtro por búsqueda
        if ($buscar !== '') {
            $builder->groupStart()
                ->like('tipo_vehiculo_nombre', $buscar)
                ->orLike('tipo_vehiculo_clave', $buscar)
                ->orLike('tipo_vehiculo_descripcion', $buscar)
            ->groupEnd();
        }

        $tipos = $builder->paginate($perPage);
        $pager = $this->tipoVehiculoModel->pager;

        $data = [
            'title'        => 'Gestión de Tipos de Vehículo',
            'tipos'        => $tipos,
            'pager'        => $pager,
            'estadisticas' => $this->tipoVehiculoModel->getEstadisticas(),
            'filtros'      => [
                'q'        => $buscar,
                'estado'   => $estado,
                'per_page' => $perPage
            ],
        ];

        return view('tipo_vehiculos/index', $data);
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $data = [
            'title'      => 'Nuevo Tipo de Vehículo',
            'validation' => \Config\Services::validation()
        ];

        return view('tipo_vehiculos/create', $data);
    }

    /**
     * Guardar nuevo tipo de vehículo
     */
    public function store()
    {
        $rules = [
            'tipo_vehiculo_nombre'      => 'required|min_length[2]|max_length[100]',
            'tipo_vehiculo_clave'       => 'permit_empty|max_length[50]|is_unique[tipo_vehiculo.tipo_vehiculo_clave]',
            'tipo_vehiculo_descripcion' => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tipo_vehiculo_nombre'      => trim($this->request->getPost('tipo_vehiculo_nombre')),
            'tipo_vehiculo_clave'       => trim($this->request->getPost('tipo_vehiculo_clave')),
            'tipo_vehiculo_descripcion' => trim($this->request->getPost('tipo_vehiculo_descripcion')),
            'tipo_vehiculo_activo'      => (int) ($this->request->getPost('tipo_vehiculo_activo') ?? 1),
        ];

        if ($this->tipoVehiculoModel->save($data)) {
            return redirect()->to('/tipo_vehiculos')->with('success', 'Tipo de vehículo creado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear el tipo de vehículo');
    }

    /**
     * Ver detalle
     */
    public function show($id)
    {
        $tipo = $this->tipoVehiculoModel->find($id);
        if (! $tipo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tipo de vehículo no encontrado');
        }

        return view('tipo_vehiculos/show', [
            'title' => 'Detalles del Tipo de Vehículo',
            'tipo'  => $tipo
        ]);
    }

    /**
     * Formulario de edición
     */
    public function edit($id)
    {
        $tipo = $this->tipoVehiculoModel->find($id);
        if (! $tipo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tipo de vehículo no encontrado');
        }

        return view('tipo_vehiculos/edit', [
            'title'      => 'Editar Tipo de Vehículo',
            'tipo'       => $tipo,
            'validation' => \Config\Services::validation(),
        ]);
    }

    /**
     * Actualizar tipo de vehículo
     */
    public function update($id)
    {
        $tipo = $this->tipoVehiculoModel->find($id);
        if (! $tipo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tipo de vehículo no encontrado');
        }

        $rules = [
            'tipo_vehiculo_nombre'      => 'required|min_length[2]|max_length[100]',
            'tipo_vehiculo_clave'       => 'permit_empty|max_length[50]|is_unique[tipo_vehiculo.tipo_vehiculo_clave,tipo_vehiculo_id,' . $id . ']',
            'tipo_vehiculo_descripcion' => 'permit_empty|max_length[255]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tipo_vehiculo_nombre'      => trim($this->request->getPost('tipo_vehiculo_nombre')),
            'tipo_vehiculo_clave'       => trim($this->request->getPost('tipo_vehiculo_clave')),
            'tipo_vehiculo_descripcion' => trim($this->request->getPost('tipo_vehiculo_descripcion')),
            'tipo_vehiculo_activo'      => (int) $this->request->getPost('tipo_vehiculo_activo'),
        ];

        if ($this->tipoVehiculoModel->update($id, $data)) {
            return redirect()->to('/tipo_vehiculos')->with('success', 'Tipo de vehículo actualizado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar el tipo de vehículo');
    }

    /**
     * Eliminar tipo de vehículo (soft delete)
     */
    public function delete($id)
    {
        $tipo = $this->tipoVehiculoModel->find($id);
        if (! $tipo) {
            return redirect()->to('/tipo_vehiculos')->with('error', 'Tipo de vehículo no encontrado');
        }

        if (! $this->tipoVehiculoModel->canDelete($id)) {
            return redirect()->to('/tipo_vehiculos')->with('error', 'No se puede eliminar el tipo de vehículo porque está siendo utilizado');
        }

        if ($this->tipoVehiculoModel->delete($id)) {
            return redirect()->to('/tipo_vehiculos')->with('success', 'Tipo de vehículo eliminado exitosamente');
        }

        return redirect()->to('/tipo_vehiculos')->with('error', 'Error al eliminar el tipo de vehículo');
    }

    /**
     * Toggle estado (AJAX)
     */
    public function toggleStatus($id)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/tipo_vehiculos');
        }

        if ($this->tipoVehiculoModel->toggleStatus($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Estado actualizado correctamente']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al actualizar el estado']);
    }

    /**
     * Obtener tipos para select (AJAX)
     */
    public function getSelect()
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/tipo_vehiculos');
        }

        $tipos = $this->tipoVehiculoModel->getForSelect();
        return $this->response->setJSON($tipos);
    }
}