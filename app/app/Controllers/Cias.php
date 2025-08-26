<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\CiaModel;

class Cias extends BaseController
{
    protected $ciaModel;

    public function __construct()
    {
        $this->ciaModel = new CiaModel();
        helper(['url', 'text']); // url_title(), etc.
    }

    public function index()
    {
        $data = [
            'title' => 'Gestión de Compañías',
            'cias'  => $this->ciaModel->getCiasWithUserCount()
        ];

        return view('cias/index', $data);
    }

    /** Formulario de creación */
    public function create()
    {
        $data = [
            'title'      => 'Nueva Compañía',
            'validation' => \Config\Services::validation()
        ];

        return view('cias/create', $data);
    }

    /** Guardar nueva compañía */
    public function store()
    {
        // Validación principal (logo opcional)
        $rules = [
            'cia_nombre'            => 'required|min_length[3]|max_length[255]',
            'cia_direccion'         => 'permit_empty|max_length[500]',
            'cia_display_name'      => 'permit_empty|max_length[150]',
            'cia_brand_nav_bg'      => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'cia_brand_nav_text'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'cia_brand_side_start'  => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'cia_brand_side_end'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Procesar logo (opcional)
        $logoName = null;
        $logoFile = $this->request->getFile('cia_logo');

        if ($logoFile && $logoFile->isValid() && ! $logoFile->hasMoved()) {
            // Validación manual del archivo
            $allowed = ['image/jpeg','image/jpg','image/png','image/svg+xml'];
            if (! in_array($logoFile->getMimeType(), $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'El logo debe ser JPG, PNG o SVG.');
            }
            if ($logoFile->getSize() > 2 * 1024 * 1024) { // 2MB
                return redirect()->back()->withInput()->with('error', 'El logo no puede superar los 2MB.');
            }

            // Asegurar carpeta
            $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'logos';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            $logoName = $logoFile->getRandomName();
            $logoFile->move($targetDir, $logoName);
        }

        // Colores con fallback
        $navBg     = $this->hexOrDefault($this->request->getPost('cia_brand_nav_bg'), '#0d6efd');
        $navText   = $this->hexOrDefault($this->request->getPost('cia_brand_nav_text'), '#ffffff');
        $sideStart = $this->hexOrDefault($this->request->getPost('cia_brand_side_start'), '#667eea');
        $sideEnd   = $this->hexOrDefault($this->request->getPost('cia_brand_side_end'), '#764ba2');

        $nombre  = trim((string) $this->request->getPost('cia_nombre'));
        $slug    = $this->makeSlug($nombre);

        // Datos a guardar
        $data = [
            'cia_nombre'            => $nombre,
            'cia_display_name'      => trim((string) $this->request->getPost('cia_display_name')) ?: null,
            'cia_slug'              => $slug,
            'cia_direccion'         => trim((string) $this->request->getPost('cia_direccion')),
            'cia_logo'              => $logoName,
            'cia_habil'             => (int) ($this->request->getPost('cia_habil') ?? 1),
            'cia_brand_nav_bg'      => $navBg,
            'cia_brand_nav_text'    => $navText,
            'cia_brand_side_start'  => $sideStart,
            'cia_brand_side_end'    => $sideEnd,
        ];

        if ($this->ciaModel->save($data)) {

            // Tema en sesión (útil mientras no hay login/selector de compañía)
            session()->set('theme', [
                'title'         => $data['cia_display_name'] ?: $data['cia_nombre'],
                'logo'          => $logoName ? base_url('uploads/logos/' . $logoName) : base_url('assets/img/app-logo.svg'),
                'nav_bg'        => $navBg,
                'nav_text'      => $navText,
                'sidebar_start' => $sideStart,
                'sidebar_end'   => $sideEnd,
            ]);

            return redirect()->to('/cias')->with('success', 'Compañía creada exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al crear la compañía');
    }

    /** Ver detalle */
    public function show($id)
    {
        $cia = $this->ciaModel->find($id);
        if (! $cia) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Compañía no encontrada');
        }

        return view('cias/show', [
            'title' => 'Detalles de Compañía',
            'cia'   => $cia
        ]);
    }

    /** Formulario de edición */
    public function edit($id)
    {
        $cia = $this->ciaModel->find($id);
        if (! $cia) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Compañía no encontrada');
        }

        return view('cias/edit', [
            'title'      => 'Editar Compañía',
            'cia'        => $cia,
            'validation' => \Config\Services::validation(),
        ]);
    }

    /** Actualizar */
    public function update($id)
    {
        $cia = $this->ciaModel->find($id);
        if (! $cia) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Compañía no encontrada');
        }

        $rules = [
            'cia_nombre'            => 'required|min_length[3]|max_length[255]',
            'cia_direccion'         => 'permit_empty|max_length[500]',
            'cia_display_name'      => 'permit_empty|max_length[150]',
            'cia_brand_nav_bg'      => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'cia_brand_nav_text'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'cia_brand_side_start'  => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
            'cia_brand_side_end'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Logo nuevo (opcional)
        $logoName = $cia['cia_logo'];
        $logoFile = $this->request->getFile('cia_logo');

        if ($logoFile && $logoFile->isValid() && ! $logoFile->hasMoved()) {
            $allowed = ['image/jpeg','image/jpg','image/png','image/svg+xml'];
            if (! in_array($logoFile->getMimeType(), $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'El logo debe ser JPG, PNG o SVG.');
            }
            if ($logoFile->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'El logo no puede superar los 2MB.');
            }

            $targetDir = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'logos';
            if (! is_dir($targetDir)) {
                @mkdir($targetDir, 0755, true);
            }

            // Borrar anterior
            if (! empty($cia['cia_logo'])) {
                $oldPath = $targetDir . DIRECTORY_SEPARATOR . $cia['cia_logo'];
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            $logoName = $logoFile->getRandomName();
            $logoFile->move($targetDir, $logoName);
        }

        // Colores
        $navBg     = $this->hexOrDefault($this->request->getPost('cia_brand_nav_bg'), $cia['cia_brand_nav_bg'] ?? '#0d6efd');
        $navText   = $this->hexOrDefault($this->request->getPost('cia_brand_nav_text'), $cia['cia_brand_nav_text'] ?? '#ffffff');
        $sideStart = $this->hexOrDefault($this->request->getPost('cia_brand_side_start'), $cia['cia_brand_side_start'] ?? '#667eea');
        $sideEnd   = $this->hexOrDefault($this->request->getPost('cia_brand_side_end'), $cia['cia_brand_side_end'] ?? '#764ba2');

        $nombre = trim((string) $this->request->getPost('cia_nombre'));
        $slug   = $cia['cia_slug'] ?: $this->makeSlug($nombre);

        $data = [
            'cia_nombre'            => $nombre,
            'cia_display_name'      => trim((string) $this->request->getPost('cia_display_name')) ?: null,
            'cia_slug'              => $slug,
            'cia_direccion'         => trim((string) $this->request->getPost('cia_direccion')),
            'cia_logo'              => $logoName,
            'cia_habil'             => (int) $this->request->getPost('cia_habil'),
            'cia_brand_nav_bg'      => $navBg,
            'cia_brand_nav_text'    => $navText,
            'cia_brand_side_start'  => $sideStart,
            'cia_brand_side_end'    => $sideEnd,
        ];

        if ($this->ciaModel->update($id, $data)) {

            // Refrescar theme en sesión (mientras no hay login/selector)
            session()->set('theme', [
                'title'         => $data['cia_display_name'] ?: $data['cia_nombre'],
                'logo'          => $logoName ? base_url('uploads/logos/' . $logoName) : base_url('assets/img/app-logo.svg'),
                'nav_bg'        => $navBg,
                'nav_text'      => $navText,
                'sidebar_start' => $sideStart,
                'sidebar_end'   => $sideEnd,
            ]);

            return redirect()->to('/cias')->with('success', 'Compañía actualizada exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar la compañía');
    }

    /** Eliminar */
    public function delete($id)
    {
        $cia = $this->ciaModel->find($id);
        if (! $cia) {
            return redirect()->to('/cias')->with('error', 'Compañía no encontrada');
        }

        if (! $this->ciaModel->canDelete($id)) {
            return redirect()->to('/cias')->with('error', 'No se puede eliminar la compañía porque tiene usuarios asociados');
        }

        // Borrar logo físico
        if (! empty($cia['cia_logo'])) {
            $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'logos' . DIRECTORY_SEPARATOR . $cia['cia_logo'];
            if (is_file($path)) {
                @unlink($path);
            }
        }

        if ($this->ciaModel->delete($id)) {
            return redirect()->to('/cias')->with('success', 'Compañía eliminada exitosamente');
        }

        return redirect()->to('/cias')->with('error', 'Error al eliminar la compañía');
    }

    /** Toggle estado (AJAX) */
    public function toggleStatus($id)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/cias');
        }

        if ($this->ciaModel->toggleStatus($id)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Estado actualizado correctamente']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al actualizar el estado']);
    }

    /** Select de compañías activas (AJAX) */
    public function getSelect()
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/cias');
        }

        $cias = $this->ciaModel->getActiveCias();
        return $this->response->setJSON($cias);
    }

    /* =================== Helpers privados =================== */

    private function makeSlug(string $name): string
    {
        $slug = url_title(convert_accented_characters($name), '-', true);
        return $slug ?: 'compania-' . uniqid();
    }

    private function hexOrDefault(?string $value, string $default): string
    {
        $v = trim((string) $value);
        if ($v === '') return $default;
        return preg_match('/^#([A-Fa-f0-9]{6})$/', $v) ? $v : $default;
    }
}