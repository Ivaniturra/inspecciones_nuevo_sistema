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
        $ciasSeleccionadas = (array) $this->request->getPost('cias');
        $ciasSeleccionadas = array_values(array_filter($ciasSeleccionadas, fn($v) => ctype_digit((string)$v))); // sanitiza

        if (count($ciasSeleccionadas) < 1) {
            return redirect()->back()->withInput()->with('errors', [
                'cias' => 'Selecciona al menos una compañía.'
            ]);
        }
        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Procesar logo (opcional)
        $logoName = null;
        $logoFile = $this->request->getFile('corredor_logo');

        if ($logoFile && $logoFile->isValid() && ! $logoFile->hasMoved()) {
            // Validación manual del archivo
            $allowed = ['image/jpeg','image/jpg','image/png','image/svg+xml'];
            if (! in_array($logoFile->getMimeType(), $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'El logo debe ser JPG, PNG o SVG.');
            }
            if ($logoFile->getSize() > 2 * 1024 * 1024) { // 2MB
                return redirect()->back()->withInput()->with('error', 'El logo no puede superar los 2MB.');
            }
            $targetDir = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'corredores';
 
            
            // Verificar/crear directorio padre primero
            $uploadsDir = FCPATH . 'uploads';
            if (!is_dir($uploadsDir)) {
                if (!@mkdir($uploadsDir, 0755, true)) {
                    return redirect()->back()->withInput()
                        ->with('error', 'No se pudo crear el directorio base de uploads. Verifica permisos del servidor.');
                }
            }
            
            // Crear directorio específico para corredores
            if (!is_dir($targetDir)) {
                if (!@mkdir($targetDir, 0755, true)) {
                    return redirect()->back()->withInput()
                        ->with('error', 'No se pudo crear el directorio de corredores. Verifica permisos: ' . $targetDir);
                }
            }

            // Verificar permisos de escritura
            if (!is_writable($targetDir)) {
                return redirect()->back()->withInput()
                    ->with('error', 'El directorio no tiene permisos de escritura: ' . $targetDir);
            }

            try {
                $logoName = $logoFile->getRandomName();
                $logoFile->move($targetDir, $logoName);
            } catch (\Exception $e) {
                return redirect()->back()->withInput()
                    ->with('error', 'Error al mover el archivo: ' . $e->getMessage());
            }
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
        if (!$corredor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Corredor no encontrado');
        }
        $ciasSeleccionadas = (array) $this->request->getPost('cias');
        $ciasSeleccionadas = array_values(array_filter($ciasSeleccionadas, fn($v) => ctype_digit((string)$v))); // sanitiza

        if (count($ciasSeleccionadas) < 1) {
            return redirect()->back()->withInput()->with('errors', [
                'cias' => 'Selecciona al menos una compañía.'
            ]);
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

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // ===== Manejo de LOGO =====
        $logoName = $corredor['corredor_logo'];           // nombre actual en BD
        $logoFile = $this->request->getFile('corredor_logo');

        // Rutas destino (público) y posible origen histórico (writable)
        $publicDir   = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'corredores';
        $oldPubCorr  = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'corredores';
        $oldPubLogos = rtrim(FCPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'logos';
        $oldWritable = rtrim(WRITEPATH, '/\\') . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'corredores';
        if (!is_dir($publicDir)) {
            @mkdir($publicDir, 0755, true);
        }

        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            // Validación tolerante (evita falsos negativos con proxies)
            $allowed   = ['image/jpeg','image/jpg','image/png','image/svg+xml','image/svg'];
            $clientMime = strtolower((string) $logoFile->getClientMimeType());

            if (!in_array($clientMime, $allowed, true)) {
                return redirect()->back()->withInput()->with('error', 'El logo debe ser JPG, PNG o SVG.');
            }
            if ($logoFile->getSize() > 2 * 1024 * 1024) {
                return redirect()->back()->withInput()->with('error', 'El logo no puede superar los 2MB.');
            }

            // Borrar anterior en /public si existe
            if (!empty($logoName)) {
                $oldPath = $publicDir . DIRECTORY_SEPARATOR . $logoName;
                if (is_file($oldPath)) {
                    @unlink($oldPath);
                }
            }

            // Guardar nuevo
            $logoName = $logoFile->getRandomName();
            $logoFile->move($publicDir, $logoName);
        } else {
            // Si no subiste archivo, pero el anterior está en writable, migrarlo a public
            if (!empty($logoName)) {
                $oldWritable = $writableDir . DIRECTORY_SEPARATOR . $logoName;
                $newPublic   = $publicDir   . DIRECTORY_SEPARATOR . $logoName;
                if (is_file($oldWritable) && !is_file($newPublic)) {
                    @rename($oldWritable, $newPublic);
                }
            }
        }
        // ===== Fin manejo LOGO =====

        // Colores
        $navBg     = $this->hexOrDefault($this->request->getPost('corredor_brand_nav_bg'),     $corredor['corredor_brand_nav_bg']    ?? '#0d6efd');
        $navText   = $this->hexOrDefault($this->request->getPost('corredor_brand_nav_text'),   $corredor['corredor_brand_nav_text']  ?? '#ffffff');
        $sideStart = $this->hexOrDefault($this->request->getPost('corredor_brand_side_start'), $corredor['corredor_brand_side_start']?? '#667eea');
        $sideEnd   = $this->hexOrDefault($this->request->getPost('corredor_brand_side_end'),   $corredor['corredor_brand_side_end']  ?? '#764ba2');

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
            'corredor_logo'              => $logoName, // <- nombre final guardado en /public/uploads/corredores
            'corredor_habil'             => (int) $this->request->getPost('corredor_habil'),
            'corredor_brand_nav_bg'      => $navBg,
            'corredor_brand_nav_text'    => $navText,
            'corredor_brand_side_start'  => $sideStart,
            'corredor_brand_side_end'    => $sideEnd,
        ];

        if ($this->corredorModel->update($id, $data)) {
            // Sincronizar compañías
            $ciaIds = (array) $this->request->getPost('cias');
            $ciaIds = array_values(array_unique(array_map('intval', $ciaIds)));
            $this->corredorModel->updateCorredorCias($id, $ciaIds);

            return redirect()->to('/corredores')->with('success', 'Corredor actualizado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar el corredor');
    }

 

    /** Toggle estado (AJAX) */
   public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/corredores');
        }

        $res = $this->corredorModel->toggleStatusCascade((int)$id);

        return $this->response
        ->setHeader('X-CSRF-TOKEN', csrf_hash())
        ->setJSON([
            'success' => $ok,
            'message' => $msg,
            // 'token' => ['name' => csrf_token(), 'hash' => csrf_hash()], // opcional
        ]);
    }

    public function enable($id)
    {
        if (!$this->request->isAJAX()) return redirect()->to('/corredores');

        $ok = $this->corredorModel->setEnabledCascade((int)$id, true);

        return $this->response->setJSON([
            'success' => $ok,
            'enabled' => true,
            'message' => $ok ? 'Corredor activado en cascada.' : 'No se pudo activar el corredor.',
        ]);
    }

    public function disable($id)
    {
        if (!$this->request->isAJAX()) return redirect()->to('/corredores');

        $ok = $this->corredorModel->setEnabledCascade((int)$id, false);

        return $this->response->setJSON([
            'success' => $ok,
            'enabled' => false,
            'message' => $ok ? 'Corredor desactivado en cascada.' : 'No se pudo desactivar el corredor.',
        ]);
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