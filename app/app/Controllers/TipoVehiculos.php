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
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)
                ->setHeader('X-CSRF-TOKEN', csrf_hash())
                ->setJSON(['success' => false, 'message' => 'Solicitud inválida']);
        }

        try {
            $tipo = $this->tipoVehiculoModel->find($id);
            if (!$tipo) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tipo de vehículo no encontrado']);
            }

            $newStatus = (int)($tipo['tipo_vehiculo_activo'] == 1 ? 0 : 1);
            
            if ($this->tipoVehiculoModel->update($id, ['tipo_vehiculo_activo' => $newStatus])) {
                $message = $newStatus ? 'Tipo de vehículo activado correctamente' : 'Tipo de vehículo desactivado correctamente';
                
                return $this->response
                    ->setHeader('X-CSRF-TOKEN', csrf_hash())
                    ->setJSON([
                        'success' => true,
                        'newStatus' => $newStatus,
                        'message' => $message
                    ]);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'No se pudo actualizar el estado']);

        } catch (\Exception $e) {
            log_message('error', 'Error en toggleStatus tipo_vehiculos: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Duplicar tipo de vehículo (opcional)
     */
    public function duplicate()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('TipoVehiculos');
        }

        try {
            $nuevoNombre = $this->request->getPost('nuevo_nombre');
            $origenId = $this->request->getPost('tipo_origen_id');

            if (!$nuevoNombre || !$origenId) {
                return $this->response->setJSON(['success' => false, 'message' => 'Datos incompletos']);
            }

            $tipoOrigen = $this->tipoVehiculoModel->find($origenId);
            if (!$tipoOrigen) {
                return $this->response->setJSON(['success' => false, 'message' => 'Tipo origen no encontrado']);
            }

            // Preparar datos para el nuevo tipo
            $newData = [
                'tipo_vehiculo_nombre' => trim($nuevoNombre),
                'tipo_vehiculo_clave' => $this->generateUniqueKey(trim($nuevoNombre)),
                'tipo_vehiculo_descripcion' => $tipoOrigen['tipo_vehiculo_descripcion'],
                'tipo_vehiculo_activo' => 1 // Nuevo tipo siempre activo
            ];

            $newId = $this->tipoVehiculoModel->insert($newData);

            if ($newId) {
                return $this->response
                    ->setHeader('X-CSRF-TOKEN', csrf_hash())
                    ->setJSON([
                        'success' => true,
                        'message' => 'Tipo de vehículo duplicado correctamente',
                        'new_id' => $newId
                    ]);
            }

            return $this->response->setJSON(['success' => false, 'message' => 'Error al duplicar el tipo']);

        } catch (\Exception $e) {
            log_message('error', 'Error en duplicate tipo_vehiculos: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Genera una clave única basada en el nombre
     */
    private function generateUniqueKey($nombre)
    {
        helper(['text', 'url']);
        
        $baseKey = strtolower($nombre);
        $baseKey = remove_accents($baseKey); // Si tienes helper para esto
        $baseKey = preg_replace('/[^a-z0-9]/', '_', $baseKey);
        $baseKey = preg_replace('/_+/', '_', $baseKey);
        $baseKey = trim($baseKey, '_');
        $baseKey = substr($baseKey, 0, 45); // Dejar espacio para sufijo

        // Verificar si existe
        $counter = 1;
        $finalKey = $baseKey;
        
        while ($this->tipoVehiculoModel->where('tipo_vehiculo_clave', $finalKey)->first()) {
            $finalKey = $baseKey . '_' . $counter;
            $counter++;
            
            // Prevenir loop infinito
            if ($counter > 999) {
                $finalKey = $baseKey . '_' . uniqid();
                break;
            }
        }

        return $finalKey;
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