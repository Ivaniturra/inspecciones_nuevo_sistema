<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CorredorModel;
use App\Models\CiaModel;

class Corredores extends BaseController
{
    protected $corredorModel;
    protected $ciaModel;

    public function __construct()
    {
        $this->corredorModel = new CorredorModel();
        $this->ciaModel = new CiaModel();
        helper(['url', 'text']);
    }

    public function index()
    {
        $search = $this->request->getGet('search');
        $ciaId = $this->request->getGet('cia_id');

        if (!empty($search) || !empty($ciaId)) {
            $corredores = $this->corredorModel->searchCorredores($search, $ciaId);
        } else {
            $corredores = $this->corredorModel->getCorredoresWithCias();
        }

        $data = [
            'title'      => 'Gestión de Corredores',
            'corredores' => $corredores,
            'cias'       => $this->ciaModel->getActiveCias(),
            'search'     => $search,
            'ciaId'      => $ciaId
        ];

        return view('corredores/index', $data);
    }

    /** Formulario de creación */
    public function create()
    {
        $data = [
            'title'      => 'Nuevo Corredor',
            'cias'       => $this->ciaModel->getActiveCias(),
            'validation' => \Config\Services::validation()
        ];

        return view('corredores/create', $data);
    }

    /** Guardar nuevo corredor */
    public function store()
    {
        $rules = [
            'corredor_nombre'            => 'required|min_length[3]|max_length[255]',
            'corredor_email'             => 'permit_empty|valid_email|max_length[255]',
            'corredor_telefono'          => 'permit_empty|max_length[50]',
            'corredor_direccion'         => 'permit_empty|max_length[500]',
            'corredor_rut'               => 'permit_empty|max_length[20]',
            'corredor_display_name'      => 'permit_empty|max_length[150]',
            'corredor_brand_nav_bg'      => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'corredor_brand_nav_text'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'corredor_brand_side_start'  => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'corredor_brand_side_end'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'cias'                       => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Procesar logo (opcional)
        $logoName = null;
        $logoFile = $this->request->getFile('corredor_logo');

        if ($logoFile && $logoFile->isValid() && ! $logoFile->hasMoved()) {
            $allowed = ['image/jpeg','image/jpg','image/png','image/svg+xml'];
            if (! in_array($logoFile->getMimeType(), $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'El logo debe ser JPG, PNG o SVG.');
            }
            if ($logoFile->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'El logo no puede superar los 2MB.');
            }

            $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'corredores';

            // Crear directorio si no existe
            if (!is_dir($targetDir)) {
                if (!@mkdir($targetDir, 0755, true)) {
                    return redirect()->back()->withInput()
                        ->with('error', 'Error: No se pudo crear el directorio de uploads. Contacta al administrador.');
                }
            }

            // Verificar que es escribible
            if (!is_writable($targetDir)) {
                return redirect()->back()->withInput()
                    ->with('error', 'Error: El directorio de uploads no tiene permisos de escritura.');
            }

            $logoName = $logoFile->getRandomName();
            $logoFile->move($targetDir, $logoName);
        }

        // Colores con fallback
        $navBg     = $this->hexOrDefault($this->request->getPost('corredor_brand_nav_bg'), '#0d6efd');
        $navText   = $this->hexOrDefault($this->request->getPost('corredor_brand_nav_text'), '#ffffff');
        $sideStart = $this->hexOrDefault($this->request->getPost('corredor_brand_side_start'), '#667eea');
        $sideEnd   = $this->hexOrDefault($this->request->getPost('corredor_brand_side_end'), '#764ba2');

        $nombre  = trim((string) $this->request->getPost('corredor_nombre'));
        $slug    = $this->makeSlug($nombre);

        $data = [
            'corredor_nombre'            => $nombre,
            'corredor_email'             => trim((string) $this->request->getPost('corredor_email')),
            'corredor_telefono'          => trim((string) $this->request->getPost('corredor_telefono')),
            'corredor_direccion'         => trim((string) $this->request->getPost('corredor_direccion')),
            'corredor_rut'               => trim((string) $this->request->getPost('corredor_rut')),
            'corredor_display_name'      => trim((string) $this->request->getPost('corredor_display_name')) ?: null,
            'corredor_slug'              => $slug,
            'corredor_logo'              => $logoName,
            'corredor_habil'             => (int) ($this->request->getPost('corredor_habil') ?? 1),
            'corredor_brand_nav_bg'      => $navBg,
            'corredor_brand_nav_text'    => $navText,
            'corredor_brand_side_start'  => $sideStart,
            'corredor_brand_side_end'    => $sideEnd,
        ];

        $corredorId = $this->corredorModel->insert($data);
        if ($corredorId) {
            // Asignar compañías al corredor
            $ciaIds = $this->request->getPost('cias');
            if (!empty($ciaIds)) {
                $this->corredorModel->updateCorredorCias($corredorId, $ciaIds);
            }
            
            return redirect()->to('/corredores')->with('success', 'Corredor creado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear el corredor');
    }

    /** Ver detalle */
    public function show($id)
    {
        $corredor = $this->corredorModel->find($id);
        if (! $corredor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Corredor no encontrado');
        }

        // Obtener compañías del corredor
        $cias = $this->corredorModel->getCiasDelCorredor($id);

        return view('corredores/show', [
            'title'    => 'Detalles de Corredor',
            'corredor' => $corredor,
            'cias'     => $cias
        ]);
    }

    /** Formulario de edición */
    public function edit($id)
    {
        $corredor = $this->corredorModel->find($id);
        if (! $corredor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Corredor no encontrado');
        }

        // Obtener compañías del corredor
        $ciasDelCorredor = $this->corredorModel->getCiasDelCorredor($id);
        $ciaIds = array_column($ciasDelCorredor, 'cia_id');

        return view('corredores/edit', [
            'title'           => 'Editar Corredor',
            'corredor'        => $corredor,
            'cias'            => $this->ciaModel->getActiveCias(),
            'ciasDelCorredor' => $ciaIds,
            'validation'      => \Config\Services::validation(),
        ]);
    }

    /** Actualizar */
    public function update($id)
    {
        $corredor = $this->corredorModel->find($id);
        if (! $corredor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Corredor no encontrado');
        }

        $rules = [
            'corredor_nombre'            => 'required|min_length[3]|max_length[255]',
            'corredor_email'             => 'permit_empty|valid_email|max_length[255]',
            'corredor_telefono'          => 'permit_empty|max_length[50]',
            'corredor_direccion'         => 'permit_empty|max_length[500]',
            'corredor_rut'               => 'permit_empty|max_length[20]',
            'corredor_display_name'      => 'permit_empty|max_length[150]',
            'corredor_brand_nav_bg'      => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'corredor_brand_nav_text'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'corredor_brand_side_start'  => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'corredor_brand_side_end'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'cias'                       => 'required',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Logo nuevo (opcional)
        $logoName = $corredor['corredor_logo'];
        $logoFile = $this->request->getFile('corredor_logo');

        if ($logoFile && $logoFile->isValid() && ! $logoFile->hasMoved()) {
            $allowed = ['image/jpeg','image/jpg','image/png','image/svg+xml'];
            if (! in_array($logoFile->getMimeType(), $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'El logo debe ser JPG, PNG o SVG.');
            }
            if ($logoFile->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'El logo no puede superar los 2MB.');
            }

            $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'corredores';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            // Borrar anterior
            if (! empty($corredor['corredor_logo'])) {
                $oldPath = $targetDir . DIRECTORY_SEPARATOR . $corredor['corredor_logo'];
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $logoName = $logoFile->getRandomName();
            $logoFile->move($targetDir, $logoName);
        }

        // Colores
        $navBg     = $this->hexOrDefault($this->request->getPost('corredor_brand_nav_bg'), $corredor['corredor_brand_nav_bg'] ?? '#0d6efd');
        $navText   = $this->hexOrDefault($this->request->getPost('corredor_brand_nav_text'), $corredor['corredor_brand_nav_text'] ?? '#ffffff');
        $sideStart = $this->hexOrDefault($this->request->getPost('corredor_brand_side_start'), $corredor['corredor_brand_side_start'] ?? '#667eea');
        $sideEnd   = $this->hexOrDefault($this->request->getPost('corredor_brand_side_end'), $corredor['corredor_brand_side_end'] ?? '#764ba2');

        $nombre = trim((string) $this->request->getPost('corredor_nombre'));
        $slug   = $corredor['corredor_slug'] ?: $this->makeSlug($nombre);

        $data = [
            'corredor_nombre'            => $nombre,
            'corredor_email'             => trim((string) $this->request->getPost('corredor_email')),
            'corredor_telefono'          => trim((string) $this->request->getPost('corredor_telefono')),
            'corredor_direccion'         => trim((string) $this->request->getPost('corredor_direccion')),
            'corredor_rut'               => trim((string) $this->request->getPost('corredor_rut')),
            'corredor_display_name'      => trim((string) $this->request->getPost('corredor_display_name')) ?: null,
            'corredor_slug'              => $slug,
            'corredor_logo'              => $logoName,
            'corredor_habil'             => (int) $this->request->getPost('corredor_habil'),
            'corredor_brand_nav_bg'      => $navBg,
            'corredor_brand_nav_text'    => $navText,
            'corredor_brand_side_start'  => $sideStart,
            'corredor_brand_side_end'    => $sideEnd,
        ];

        if ($this->corredorModel->update($id, $data)) {
            // Actualizar compañías del corredor
            $ciaIds = $this->request->getPost('cias');
            $this->corredorModel->updateCorredorCias($id, $ciaIds);
            
            return redirect()->to('/corredores')->with('success', 'Corredor actualizado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar el corredor');
    }

    /** Eliminar */
    public function delete($id)
    {
        $corredor = $this->corredorModel->find($id);
        if (! $corredor) {
            return redirect()->to('/corredores')->with('error', 'Corredor no encontrado');
        }

        if (! $this->corredorModel->canDelete($id)) {
            return redirect()->to('/corredores')->with('error', 'No se puede eliminar el corredor porque tiene registros asociados');
        }

        // Borrar logo físico
        if (! empty($corredor['corredor_logo'])) {
            $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'corredores' . DIRECTORY_SEPARATOR . $corredor['corredor_logo'];
            if (is_file($path)) {
                @unlink($path);
            }
        }

        if ($this->corredorModel->delete($id)) {
            return redirect()->to('/corredores')->with('success', 'Corredor eliminado exitosamente');
        }

        return redirect()->to('/corredores')->with('error', 'Error al eliminar el corredor');
    }

    /** Toggle estado (AJAX) */
    public function toggleStatus($id)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/corredores');
        }

        if ($this->corredorModel->toggleStatus($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Estado actualizado correctamente']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al actualizar el estado']);
    }

    /** Select de corredores activos por compañía (AJAX) */
    public function getByCia($ciaId)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/corredores');
        }

        $corredores = $this->corredorModel->getCorredoresByCia($ciaId);
        return $this->response->setJSON($corredores);
    }

    /* =================== Helpers privados =================== */

    private function makeSlug(string $name): string
    {
        $slug = url_title(convert_accented_characters($name), '-', true);
        return $slug ?: 'corredor-' . uniqid();
    }

    private function hexOrDefault(?string $value, string $default): string
    {
        $v = trim((string) $value);
        if ($v === '') return $default;
        return preg_match('/^#([A-Fa-f0-9]{6})$/', $v) ? $v : $default;
    }
}