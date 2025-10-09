<?php
namespace App\Controllers;

use App\Models\CorredorModel;
use App\Models\CiasModel;

class Corredores extends BaseController
{
    protected $corredorModel;
    protected $ciasModel;

    public function __construct()
    {
        $this->corredorModel = new CorredorModel();
        $this->ciasModel = new CiasModel();
        
        // Verificar autenticación y permisos
        if (!session('logged_in')) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Acceso denegado');
        }
        
        // Opcional: verificar permisos específicos
        // if (!can('gestionar_corredores')) {
        //     throw new \CodeIgniter\Exceptions\PageNotFoundException('Sin permisos');
        // }
    }

    public function index()
    {
        // Obtener parámetros de búsqueda
        $search = $this->request->getGet('search') ?? '';
        $ciaId = $this->request->getGet('cia_id') ?? '';

        // Obtener corredores con búsqueda
        if (!empty($search) || !empty($ciaId)) {
            $corredores = $this->corredorModel->searchCorredores($search, $ciaId);
        } else {
            $corredores = $this->corredorModel->getCorredoresWithCias();
        }

        // Obtener todas las compañías activas para el filtro
        $cias = $this->ciasModel->getActiveCias();

        $data = [
            'title' => 'Gestión de Corredores',
            'corredores' => $corredores,
            'cias' => $cias,
            'search' => $search,
            'ciaId' => $ciaId,
        ];

        return view('corredores/index', $data);
    }

    public function show($id)
    {
        $corredor = $this->corredorModel->find($id);
        
        if (!$corredor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Corredor no encontrado');
        }

        // Obtener compañías del corredor
        $cias = $this->corredorModel->getCiasDelCorredor($id);

        $data = [
            'title' => 'Detalle de Corredor',
            'corredor' => $corredor,
            'cias' => $cias,
        ];

        return view('corredores/show', $data);
    }

    public function create()
    {
        // Obtener todas las compañías activas
        $cias = $this->ciasModel->getActiveCias();

        $data = [
            'title' => 'Nuevo Corredor',
            'cias' => $cias,
        ];

        return view('corredores/create', $data);
    }

    public function store()
    {
        // Validar datos
        $rules = [
            'corredor_nombre' => 'required|min_length[3]|max_length[255]',
            'corredor_email' => 'permit_empty|valid_email',
            'corredor_rut' => 'permit_empty|max_length[20]',
            'cias' => 'required', // Al menos una compañía
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Preparar datos del corredor
            $data = [
                'corredor_nombre' => $this->request->getPost('corredor_nombre'),
                'corredor_display_name' => $this->request->getPost('corredor_display_name'),
                'corredor_email' => $this->request->getPost('corredor_email') ?: null,
                'corredor_telefono' => $this->request->getPost('corredor_telefono') ?: null,
                'corredor_rut' => $this->request->getPost('corredor_rut') ?: null,
                'corredor_direccion' => $this->request->getPost('corredor_direccion') ?: null,
                'corredor_habil' => (int)$this->request->getPost('corredor_habil'),
                'corredor_brand_nav_bg' => $this->request->getPost('corredor_brand_nav_bg') ?: '#0D6EFD',
                'corredor_brand_nav_text' => $this->request->getPost('corredor_brand_nav_text') ?: '#FFFFFF',
                'corredor_brand_side_start' => $this->request->getPost('corredor_brand_side_start') ?: '#667EEA',
                'corredor_brand_side_end' => $this->request->getPost('corredor_brand_side_end') ?: '#764BA2',
            ];

            // Manejar logo
            $logo = $this->request->getFile('corredor_logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                $newName = $logo->getRandomName();
                $logo->move(WRITEPATH . '../public/uploads/corredores', $newName);
                $data['corredor_logo'] = $newName;
            }

            // Insertar corredor
            $corredorId = $this->corredorModel->insert($data);

            if (!$corredorId) {
                throw new \Exception('Error al crear el corredor');
            }

            // Asignar compañías
            $ciaIds = $this->request->getPost('cias') ?? [];
            if (!empty($ciaIds)) {
                $this->corredorModel->updateCorredorCias($corredorId, $ciaIds);
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción');
            }

            return redirect()->to(base_url('corredores'))
                ->with('success', 'Corredor creado exitosamente');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al crear el corredor: ' . $e->getMessage());
        }
    }

    public function edit($id)
    {
        $corredor = $this->corredorModel->find($id);
        
        if (!$corredor) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Corredor no encontrado');
        }

        // Obtener todas las compañías activas
        $cias = $this->ciasModel->getActiveCias();

        // Obtener compañías del corredor (IDs)
        $ciasDelCorredor = array_column(
            $this->corredorModel->getCiasDelCorredor($id),
            'cia_id'
        );

        $data = [
            'title' => 'Editar Corredor',
            'corredor' => $corredor,
            'cias' => $cias,
            'ciasDelCorredor' => $ciasDelCorredor,
        ];

        return view('corredores/edit', $data);
    }

    public function update($id)
    {
        $corredor = $this->corredorModel->find($id);
        
        if (!$corredor) {
            return redirect()->back()->with('error', 'Corredor no encontrado');
        }

        // Validar
        $rules = [
            'corredor_nombre' => 'required|min_length[3]|max_length[255]',
            'corredor_email' => 'permit_empty|valid_email',
            'cias' => 'required',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Preparar datos
            $data = [
                'corredor_nombre' => $this->request->getPost('corredor_nombre'),
                'corredor_display_name' => $this->request->getPost('corredor_display_name'),
                'corredor_email' => $this->request->getPost('corredor_email') ?: null,
                'corredor_telefono' => $this->request->getPost('corredor_telefono') ?: null,
                'corredor_rut' => $this->request->getPost('corredor_rut') ?: null,
                'corredor_direccion' => $this->request->getPost('corredor_direccion') ?: null,
                'corredor_habil' => (int)$this->request->getPost('corredor_habil'),
                'corredor_brand_nav_bg' => $this->request->getPost('corredor_brand_nav_bg'),
                'corredor_brand_nav_text' => $this->request->getPost('corredor_brand_nav_text'),
                'corredor_brand_side_start' => $this->request->getPost('corredor_brand_side_start'),
                'corredor_brand_side_end' => $this->request->getPost('corredor_brand_side_end'),
            ];

            // Manejar logo nuevo
            $logo = $this->request->getFile('corredor_logo');
            if ($logo && $logo->isValid() && !$logo->hasMoved()) {
                // Eliminar logo anterior
                if (!empty($corredor['corredor_logo'])) {
                    $oldPath = WRITEPATH . '../public/uploads/corredores/' . $corredor['corredor_logo'];
                    if (file_exists($oldPath)) {
                        @unlink($oldPath);
                    }
                }

                $newName = $logo->getRandomName();
                $logo->move(WRITEPATH . '../public/uploads/corredores', $newName);
                $data['corredor_logo'] = $newName;
            }

            // Actualizar corredor
            $this->corredorModel->update($id, $data);

            // Actualizar compañías
            $ciaIds = $this->request->getPost('cias') ?? [];
            $this->corredorModel->updateCorredorCias($id, $ciaIds);

            $db->transComplete();

            return redirect()->to(base_url('corredores/show/' . $id))
                ->with('success', 'Corredor actualizado exitosamente');

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()
                ->withInput()
                ->with('error', 'Error al actualizar: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        $corredor = $this->corredorModel->find($id);
        
        if (!$corredor) {
            return redirect()->back()->with('error', 'Corredor no encontrado');
        }

        // Eliminar logo si existe
        if (!empty($corredor['corredor_logo'])) {
            $logoPath = WRITEPATH . '../public/uploads/corredores/' . $corredor['corredor_logo'];
            if (file_exists($logoPath)) {
                @unlink($logoPath);
            }
        }

        if ($this->corredorModel->delete($id)) {
            return redirect()->to(base_url('corredores'))
                ->with('success', 'Corredor eliminado exitosamente');
        } else {
            return redirect()->back()
                ->with('error', 'Error al eliminar el corredor');
        }
    }

    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setStatusCode(403)->setJSON([
                'success' => false,
                'message' => 'Acceso denegado'
            ]);
        }

        try {
            $corredor = $this->corredorModel->find($id);

            if (!$corredor) {
                return $this->response->setStatusCode(404)->setJSON([
                    'success' => false,
                    'message' => 'Corredor no encontrado'
                ]);
            }

            $nuevoEstado = $corredor['corredor_habil'] == 1 ? 0 : 1;
            
            if ($this->corredorModel->update($id, ['corredor_habil' => $nuevoEstado])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Estado actualizado correctamente',
                    'enabled' => $nuevoEstado
                ]);
            } else {
                return $this->response->setStatusCode(500)->setJSON([
                    'success' => false,
                    'message' => 'Error al actualizar el estado'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error en toggleStatus: ' . $e->getMessage());
            return $this->response->setStatusCode(500)->setJSON([
                'success' => false,
                'message' => 'Error del servidor: ' . $e->getMessage()
            ]);
        }
    }
}