<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\TiposInspeccionModel;

class TiposInspeccion extends BaseController
{
    protected $tiposInspeccionModel;

    public function __construct()
    {
        $this->tiposInspeccionModel = new TiposInspeccionModel();
        helper(['url', 'text']);
    }

    public function index()
    {
        $perPage = (int)($this->request->getGet('per_page') ?? 10);
        $perPage = in_array($perPage, [10,20,50,100]) ? $perPage : 10;

        $buscar = trim((string)$this->request->getGet('q'));
        $estado = $this->request->getGet('estado');

        $builder = $this->tiposInspeccionModel->orderBy('tipo_inspeccion_nombre', 'ASC');

        if ($estado === 'activo') {
            $builder->where('tipo_inspeccion_activo', 1);
        } elseif ($estado === 'inactivo') {
            $builder->where('tipo_inspeccion_activo', 0);
        }

        if ($buscar !== '') {
            $builder->groupStart()
                ->like('tipo_inspeccion_nombre', $buscar)
                ->orLike('tipo_inspeccion_clave', $buscar)
                ->orLike('tipo_inspeccion_descripcion', $buscar)
            ->groupEnd();
        }

        $tipos = $builder->paginate($perPage);
        $pager = $this->tiposInspeccionModel->pager;

        $data = [
            'title'        => 'Gestión de Tipos de Inspección',
            'tipos'        => $tipos,
            'pager'        => $pager,
            'estadisticas' => $this->tiposInspeccionModel->getEstadisticas(),
            'filtros'      => [
                'q'        => $buscar,
                'estado'   => $estado,
                'per_page' => $perPage
            ],
        ];

        return view('tipos_inspeccion/index', $data);
    }

    public function create()
    {
        $data = [
            'title'      => 'Nuevo Tipo de Inspección',
            'validation' => \Config\Services::validation()
        ];

        return view('tipos_inspeccion/create', $data);
    }

    public function store()
    {
        $rules = [
            'tipo_inspeccion_nombre'      => 'required|min_length[2]|max_length[100]',
            'tipo_inspeccion_clave'       => 'permit_empty|max_length[50]|is_unique[tipos_inspeccion.tipo_inspeccion_clave]',
            'tipo_inspeccion_descripcion' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tipo_inspeccion_nombre'      => trim($this->request->getPost('tipo_inspeccion_nombre')),
            'tipo_inspeccion_clave'       => trim($this->request->getPost('tipo_inspeccion_clave')),
            'tipo_inspeccion_descripcion' => trim($this->request->getPost('tipo_inspeccion_descripcion')),
            'tipo_inspeccion_activo'      => (int) ($this->request->getPost('tipo_inspeccion_activo') ?? 1),
        ];

        if ($this->tiposInspeccionModel->save($data)) {
            return redirect()->to('/tipos-inspeccion')->with('success', 'Tipo de inspección creado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear el tipo de inspección');
    }

    public function edit($id)
    {
        $tipo = $this->tiposInspeccionModel->find($id);
        if (!$tipo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tipo de inspección no encontrado');
        }

        return view('tipos_inspeccion/edit', [
            'title'      => 'Editar Tipo de Inspección',
            'tipo'       => $tipo,
            'validation' => \Config\Services::validation(),
        ]);
    }

    public function update($id)
    {
        $tipo = $this->tiposInspeccionModel->find($id);
        if (!$tipo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tipo de inspección no encontrado');
        }

        $rules = [
            'tipo_inspeccion_nombre'      => 'required|min_length[2]|max_length[100]',
            'tipo_inspeccion_clave'       => 'permit_empty|max_length[50]|is_unique[tipos_inspeccion.tipo_inspeccion_clave,tipo_inspeccion_id,' . $id . ']',
            'tipo_inspeccion_descripcion' => 'permit_empty|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'tipo_inspeccion_nombre'      => trim($this->request->getPost('tipo_inspeccion_nombre')),
            'tipo_inspeccion_clave'       => trim($this->request->getPost('tipo_inspeccion_clave')),
            'tipo_inspeccion_descripcion' => trim($this->request->getPost('tipo_inspeccion_descripcion')),
            'tipo_inspeccion_activo'      => (int) $this->request->getPost('tipo_inspeccion_activo'),
        ];

        if ($this->tiposInspeccionModel->update($id, $data)) {
            return redirect()->to('/tipos-inspeccion')->with('success', 'Tipo de inspección actualizado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar el tipo de inspección');
    }

    public function delete($id)
    {
        $tipo = $this->tiposInspeccionModel->find($id);
        if (!$tipo) {
            return redirect()->to('/tipos-inspeccion')->with('error', 'Tipo de inspección no encontrado');
        }

        if (!$this->tiposInspeccionModel->canDelete($id)) {
            return redirect()->to('/tipos-inspeccion')->with('error', 'No se puede eliminar porque está siendo utilizado');
        }

        if ($this->tiposInspeccionModel->delete($id)) {
            return redirect()->to('/tipos-inspeccion')->with('success', 'Tipo de inspección eliminado exitosamente');
        }

        return redirect()->to('/tipos-inspeccion')->with('error', 'Error al eliminar');
    }

    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)
                ->setHeader('X-CSRF-TOKEN', csrf_hash())
                ->setJSON(['success' => false, 'message' => 'Solicitud inválida']);
        }

        try {
            $tipo = $this->tiposInspeccionModel->find($id);
            if (!$tipo) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tipo no encontrado']);
            }

            $newStatus = (int)($tipo['tipo_inspeccion_activo'] == 1 ? 0 : 1);
            
            if ($this->tiposInspeccionModel->update($id, ['tipo_inspeccion_activo' => $newStatus])) {
                $message = $newStatus ? 'Tipo activado correctamente' : 'Tipo desactivado correctamente';
                
                return $this->response
                    ->setHeader('X-CSRF-TOKEN', csrf_hash())
                    ->setJSON([
                        'success' => true,
                        'newStatus' => $newStatus,
                        'message' => $message
                    ]);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'No se pudo actualizar']);

        } catch (\Exception $e) {
            log_message('error', 'Error en toggleStatus tipos_inspeccion: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error interno']);
        }
    }

    public function getSelect()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/tipos-inspeccion');
        }

        $tipos = $this->tiposInspeccionModel->getListaActivos();
        return $this->response->setJSON($tipos);
    }
    public function show($id)
    {
        $tipo = $this->tiposInspeccionModel->find($id);
        
        if (!$tipo) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Tipo de inspección no encontrado');
        }

        $data = [
            'title' => 'Detalles del Tipo de Inspección',
            'tipo'  => $tipo
        ];

        return view('tipos_inspeccion/show', $data);
    }
}