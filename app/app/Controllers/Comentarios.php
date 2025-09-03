<?php

namespace App\Controllers;

use App\Controllers\BaseController;

class Comentarios extends BaseController
{
    protected $comentarioModel;
    protected $ciaModel;
    protected $perfilModel;

    public function __construct()
    {
        // Aseg?rate de tener estos modelos
        $this->comentarioModel = new \App\Models\ComentarioModel();
        $this->ciaModel = new \App\Models\CiaModel();
        $this->perfilModel = new \App\Models\PerfilModel();
        
        // Helpers necesarios
        helper(['form', 'url']);
    }

    /**
     * P?gina principal de comentarios
     */
    public function index()
    {
        // Obtener filtros de la URL
        $filtros = [
            'cia_id' => $this->request->getGet('cia_id') ?: '',
            'perfil_id' => $this->request->getGet('perfil_id') ?: '',
            'estado' => $this->request->getGet('estado') ?: '',
            'q' => $this->request->getGet('q') ?: '',
            'per_page' => (int)($this->request->getGet('per_page') ?: 20)
        ];

        // Construir query b?sica
        $builder = $this->comentarioModel
            ->select('comentarios.*, cias.cia_nombre, perfiles.perfil_nombre')
            ->join('cias', 'cias.cia_id = comentarios.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = comentarios.perfil_id', 'left');

        // Aplicar filtros
        if (!empty($filtros['cia_id'])) {
            $builder->where('comentarios.cia_id', $filtros['cia_id']);
        }
        if (!empty($filtros['perfil_id'])) {
            $builder->where('comentarios.perfil_id', $filtros['perfil_id']);
        }
        if ($filtros['estado'] !== '') {
            $builder->where('comentarios.comentario_habil', (int)$filtros['estado']);
        }
        if (!empty($filtros['q'])) {
            $builder->like('comentarios.comentario_nombre', $filtros['q']);
        }

        // Obtener resultados
        $rows = $builder->orderBy('comentarios.comentario_id', 'DESC')
                       ->findAll();

        // Obtener datos para los dropdowns
        $cias = [];
        foreach ($this->ciaModel->findAll() as $cia) {
            $cias[$cia['cia_id']] = $cia['cia_nombre'];
        }

        $perfiles = [];
        foreach ($this->perfilModel->findAll() as $perfil) {
            $perfiles[$perfil['perfil_id']] = $perfil['perfil_nombre'];
        }

        $data = [
            'title' => 'Gesti?n de Comentarios',
            'rows' => $rows,
            'filtros' => $filtros,
            'cias' => $cias,
            'perfiles' => $perfiles,
            'pager' => null // Agregar paginaci?n despu?s si es necesario
        ];

        return view('comentarios/index', $data);
    }

    /**
     * Formulario crear comentario
     */
    public function create()
    {
        // Obtener datos para dropdowns
        $cias = [];
        foreach ($this->ciaModel->findAll() as $cia) {
            $cias[$cia['cia_id']] = $cia['cia_nombre'];
        }

        $perfiles = [];
        foreach ($this->perfilModel->findAll() as $perfil) {
            $perfiles[$perfil['perfil_id']] = $perfil['perfil_nombre'];
        }

        $data = [
            'title' => 'Nuevo Comentario',
            'cias' => $cias,
            'perfiles' => $perfiles
        ];

        return view('comentarios/create', $data);
    }

    /**
     * Guardar nuevo comentario
     */
    public function store()
    {
        // Validaci?n b?sica
        $rules = [
            'comentario_nombre' => 'required|min_length[2]|max_length[2000]',
            'cia_id' => 'required|integer',
            'perfil_id' => 'permit_empty|integer',
            'comentario_id_cia_interno' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Preparar datos
        $data = [
            'comentario_nombre' => $this->request->getPost('comentario_nombre'),
            'cia_id' => (int)$this->request->getPost('cia_id'),
            'perfil_id' => $this->request->getPost('perfil_id') ?: null,
            'comentario_id_cia_interno' => $this->request->getPost('comentario_id_cia_interno') ?: null,
            'comentario_devuelve' => (int)($this->request->getPost('comentario_devuelve') ?? 0),
            'comentario_elimina' => (int)($this->request->getPost('comentario_elimina') ?? 0),
            'comentario_envia_correo' => (int)($this->request->getPost('comentario_envia_correo') ?? 0),
            'comentario_habil' => (int)($this->request->getPost('comentario_habil') ?? 1)
        ];

        if ($this->comentarioModel->insert($data)) {
            return redirect()->to('/comentarios')
                ->with('success', 'Comentario creado exitosamente');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Error al crear el comentario');
    }

    /**
     * Formulario editar comentario
     */
    public function edit($id)
    {
        $comentario = $this->comentarioModel
            ->select('comentarios.*, cias.cia_nombre, perfiles.perfil_nombre')
            ->join('cias', 'cias.cia_id = comentarios.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = comentarios.perfil_id', 'left')
            ->find($id);

        if (!$comentario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Comentario no encontrado');
        }

        // Obtener datos para dropdowns
        $cias = [];
        foreach ($this->ciaModel->findAll() as $cia) {
            $cias[$cia['cia_id']] = $cia['cia_nombre'];
        }

        $perfiles = [];
        foreach ($this->perfilModel->findAll() as $perfil) {
            $perfiles[$perfil['perfil_id']] = $perfil['perfil_nombre'];
        }

        $data = [
            'title' => 'Editar Comentario',
            'comentario' => $comentario,
            'cias' => $cias,
            'perfiles' => $perfiles
        ];

        return view('comentarios/edit', $data);
    }

    /**
     * Actualizar comentario
     */
    public function update($id)
    {
        $comentario = $this->comentarioModel->find($id);
        if (!$comentario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Comentario no encontrado');
        }

        // Validaci?n
        $rules = [
            'comentario_nombre' => 'required|min_length[2]|max_length[2000]',
            'cia_id' => 'required|integer',
            'perfil_id' => 'permit_empty|integer',
            'comentario_id_cia_interno' => 'permit_empty|integer'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Preparar datos
        $data = [
            'comentario_nombre' => $this->request->getPost('comentario_nombre'),
            'cia_id' => (int)$this->request->getPost('cia_id'),
            'perfil_id' => $this->request->getPost('perfil_id') ?: null,
            'comentario_id_cia_interno' => $this->request->getPost('comentario_id_cia_interno') ?: null,
            'comentario_devuelve' => (int)($this->request->getPost('comentario_devuelve') ?? 0),
            'comentario_elimina' => (int)($this->request->getPost('comentario_elimina') ?? 0),
            'comentario_envia_correo' => (int)($this->request->getPost('comentario_envia_correo') ?? 0),
            'comentario_habil' => (int)($this->request->getPost('comentario_habil') ?? 1)
        ];

        if ($this->comentarioModel->update($id, $data)) {
            return redirect()->to('/comentarios')
                ->with('success', 'Comentario actualizado exitosamente');
        }

        return redirect()->back()
            ->withInput()
            ->with('error', 'Error al actualizar el comentario');
    }

    /**
     * Toggle status AJAX
     */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/comentarios')->with('error', 'M?todo no permitido');
        }

        $id = (int) $id;
        $comentario = $this->comentarioModel->find($id);
        
        if (!$comentario) {
            return $this->response
                ->setHeader('X-CSRF-TOKEN', csrf_hash())
                ->setJSON([
                    'success' => false,
                    'message' => 'Comentario no encontrado'
                ])->setStatusCode(404);
        }

        $oldStatus = (int) ($comentario['comentario_habil'] ?? 1);
        $newStatus = $oldStatus === 1 ? 0 : 1;

        try {
            if ($this->comentarioModel->update($id, ['comentario_habil' => $newStatus])) {
                return $this->response
                    ->setHeader('X-CSRF-TOKEN', csrf_hash())
                    ->setJSON([
                        'success' => true,
                        'message' => 'Estado actualizado correctamente',
                        'new_status' => $newStatus,
                        'status_text' => $newStatus ? 'Activo' : 'Inactivo',
                    ]);
            }

            throw new \Exception('Error al actualizar en la base de datos');
            
        } catch (\Exception $e) {
            log_message('error', 'Error en toggleStatus comentario: ' . $e->getMessage());
            
            return $this->response
                ->setHeader('X-CSRF-TOKEN', csrf_hash())
                ->setJSON([
                    'success' => false,
                    'message' => 'Error interno al actualizar el estado'
                ])->setStatusCode(500);
        }
    }
}