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
     * Listado de perfiles
     */
    public function index()
    {
        $data = [
            'title'    => 'Gestión de Perfiles',
            'perfiles' => $this->perfilModel->getPerfilesWithUserCount(),
        ];

        return view('perfiles/index', $data);
    }

    /**
     * Formulario de creación
     */
    public function create()
    {
        $data = [
            'title'            => 'Nuevo Perfil',
            'permisosCompania' => $this->perfilModel->getPermisosDisponibles('compania'),
            'permisosInternos' => $this->perfilModel->getPermisosDisponibles('interno'),
        ];

        return view('perfiles/create', $data);
    }

    /**
     * Guardar nuevo perfil
     */
    public function store()
    {
        // Validación
        $rules = [
            'perfil_nombre' => 'required|min_length[3]|max_length[100]',
            'perfil_tipo'   => 'required|in_list[compania,interno]',
            'perfil_nivel'  => 'required|integer|greater_than[0]|less_than[5]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        // Normaliza y filtra permisos por tipo
        $tipo              = $this->request->getPost('perfil_tipo');
        $permisosMarcados  = (array) ($this->request->getPost('permisos') ?? []);
        $permisosCompania  = $this->perfilModel->getPermisosDisponibles('compania');
        $permisosInternos  = $this->perfilModel->getPermisosDisponibles('interno');
        $permisosFiltrados = $this->filtrarPermisos($tipo, $permisosMarcados, $permisosCompania, $permisosInternos);

        $data = [
            'perfil_nombre'      => trim((string) $this->request->getPost('perfil_nombre')),
            'perfil_tipo'        => $tipo,
            'perfil_descripcion' => (string) $this->request->getPost('perfil_descripcion'),
            'perfil_permisos'    => $permisosFiltrados, // PerfilModel puede guardar como json automáticamente, si no, json_encode aquí
            'perfil_nivel'       => (int) $this->request->getPost('perfil_nivel'),
            'perfil_habil'       => (int) ($this->request->getPost('perfil_habil') ?? 1),
        ];

        // Si tu PerfilModel NO json_encode automáticamente, usa:
        // $data['perfil_permisos'] = json_encode($permisosFiltrados, JSON_UNESCAPED_UNICODE);

        if ($this->perfilModel->save($data)) {
            return redirect()->to('/perfiles')->with('success', 'Perfil creado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear el perfil');
    }

    /**
     * Detalle de un perfil
     */
    public function show($id)
    {
        $perfil = $this->perfilModel->find((int) $id);
        if (! $perfil) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Perfil no encontrado');
        }

        $data = [
            'title'               => 'Detalles del Perfil',
            'perfil'              => $perfil,
            'permisosDisponibles' => $this->perfilModel->getPermisosDisponibles($perfil['perfil_tipo'] ?? 'interno'),
        ];

        return view('perfiles/show', $data);
    }

    /**
     * Formulario de edición
     */
    public function edit($id)
    {
        $perfil = $this->perfilModel->find((int) $id);
        if (! $perfil) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Perfil no encontrado');
        }

        $data = [
            'title'            => 'Editar Perfil',
            'perfil'           => $perfil,
            'permisosCompania' => $this->perfilModel->getPermisosDisponibles('compania'),
            'permisosInternos' => $this->perfilModel->getPermisosDisponibles('interno'),
        ];

        return view('perfiles/edit', $data);
    }

    /**
     * Actualizar perfil
     */
    public function update($id)
    {
        $id     = (int) $id;
        $perfil = $this->perfilModel->find($id);
        if (! $perfil) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Perfil no encontrado');
        }

        $rules = [
            'perfil_nombre' => 'required|min_length[3]|max_length[100]',
            'perfil_tipo'   => 'required|in_list[compania,interno]',
            'perfil_nivel'  => 'required|integer|greater_than[0]|less_than[5]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $tipo              = $this->request->getPost('perfil_tipo');
        $permisosMarcados  = (array) ($this->request->getPost('permisos') ?? []);
        $permisosCompania  = $this->perfilModel->getPermisosDisponibles('compania');
        $permisosInternos  = $this->perfilModel->getPermisosDisponibles('interno');
        $permisosFiltrados = $this->filtrarPermisos($tipo, $permisosMarcados, $permisosCompania, $permisosInternos);

        $data = [
            'perfil_nombre'      => trim((string) $this->request->getPost('perfil_nombre')),
            'perfil_tipo'        => $tipo,
            'perfil_descripcion' => (string) $this->request->getPost('perfil_descripcion'),
            'perfil_permisos'    => $permisosFiltrados,
            'perfil_nivel'       => (int) $this->request->getPost('perfil_nivel'),
            'perfil_habil'       => (int) $this->request->getPost('perfil_habil'),
        ];

        // Si tu PerfilModel NO json_encode automáticamente, usa:
        // $data['perfil_permisos'] = json_encode($permisosFiltrados, JSON_UNESCAPED_UNICODE);

        if ($this->perfilModel->update($id, $data)) {
            return redirect()->to('/perfiles')->with('success', 'Perfil actualizado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar el perfil');
    }

    /**
     * Eliminar perfil
     */
    public function delete($id)
    {
        $id     = (int) $id;
        $perfil = $this->perfilModel->find($id);
        if (! $perfil) {
            return redirect()->to('/perfiles')->with('error', 'Perfil no encontrado');
        }

        if (! $this->perfilModel->canDelete($id)) {
            return redirect()->to('/perfiles')->with('error', 'No se puede eliminar el perfil porque tiene usuarios asociados');
        }

        if ($this->perfilModel->delete($id)) {
            return redirect()->to('/perfiles')->with('success', 'Perfil eliminado exitosamente');
        }

        return redirect()->to('/perfiles')->with('error', 'Error al eliminar el perfil');
    }

    /**
     * Cambiar estado (AJAX)
     */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(400)
                ->setHeader('X-CSRF-TOKEN', csrf_hash())
                ->setJSON(['success' => false, 'message' => 'Solicitud inválida']);
        }

        try {
            $perfil = $this->perfilModel->find($id);
            if (!$perfil) {
                return $this->response->setJSON(['success' => false, 'message' => 'Perfil no encontrado']);
            }

            $newStatus = (int)($perfil['perfil_habil'] == 1 ? 0 : 1);
            
            if ($this->perfilModel->update($id, ['perfil_habil' => $newStatus])) {
                $message = $newStatus ? 'Perfil activado correctamente' : 'Perfil desactivado correctamente';
                
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
            log_message('error', 'Error en toggleStatus perfiles: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error interno del servidor']);
        }
    }

    /**
     * Obtener perfiles por tipo (AJAX)
     */
    public function getByTipo($tipo = null)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/perfiles');
        }

        $tipo = in_array($tipo, ['compania', 'interno'], true) ? $tipo : null;
        $perfiles = $this->perfilModel->getPerfilesByTipo($tipo);

        return $this->response->setJSON($perfiles);
    }

    /**
     * Obtener perfiles para select (AJAX)
     */
    public function getSelect()
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/perfiles');
        }

        $tipo = $this->request->getGet('tipo');
        $tipo = in_array($tipo, ['compania', 'interno'], true) ? $tipo : null;

        $perfiles = $this->perfilModel->getPerfilesByTipo($tipo);
        return $this->response->setJSON($perfiles);
    }

    // =======================
    // Helpers privados
    // =======================

    /**
     * Filtra y normaliza los permisos marcados según el tipo.
     * Devuelve array asociativo: ['clave_permiso' => true, ...]
     */
    private function filtrarPermisos(string $tipo, array $marcados, array $compania, array $interno): array
    {
        // Lista blanca según tipo
        $permitidos = ($tipo === 'compania') ? array_keys($compania) : array_keys($interno);

        // Mantener solo los que están permitidos
        $marcados = array_values(array_unique(array_map('strval', $marcados)));
        $marcados = array_intersect($marcados, array_map('strval', $permitidos));

        // Volver asociativo key => true
        $asoc = [];
        foreach ($marcados as $k) {
            $asoc[(string) $k] = true;
        }
        return $asoc;
    }
}
