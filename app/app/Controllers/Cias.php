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
    }
 
    public function index()
    {
        $data = [
            'title' => 'Gestión de Compañias',
            'cias'  => $this->ciaModel->getCiasWithUserCount()
        ];

        return view('cias/index', $data);
    }

    /**
     * Mostrar formulario de      * Procesar creacion de Compañias

     */
    public function create()
    {
        $data = [
            'title' => 'Nueva Compañias',
            'validation' => \Config\Services::validation()
        ];

        return view('cias/create', $data);
    }

    /**
     * Procesar creacion de Compañias
     */
    public function store()
    {
        // Validaci�n
        if (!$this->validate([
            'cia_nombre' => 'required|min_length[3]|max_length[255]',
            'cia_direccion' => 'max_length[500]',
            'cia_logo' => [
                'uploaded[cia_logo]',
                'is_image[cia_logo]',
                'mime_in[cia_logo,image/jpg,image/jpeg,image/png]',
                'max_size[cia_logo,2048]'
            ]
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Procesar subida de logo
        $logoName = null;
        $logoFile = $this->request->getFile('cia_logo');
        
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            $logoName = $logoFile->getRandomName();
            $logoFile->move('uploads/logos/', $logoName);
        }

        // Guardar datos
        $data = [
            'cia_nombre'    => $this->request->getPost('cia_nombre'),
            'cia_direccion' => $this->request->getPost('cia_direccion'),
            'cia_logo'      => $logoName,
            'cia_habil'     => $this->request->getPost('cia_habil') ?? 1
        ];

        if ($this->ciaModel->save($data)) {
            return redirect()->to('/cias')->with('success', 'Compañia creada exitosamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al crear la compa��a');
        }
    }

    /**
     * Mostrar detalles de una Compañia
     */
    public function show($id)
    {
        $cia = $this->ciaModel->find($id);
        
        if (!$cia) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Compañia no encontrada');
        }

        $data = [
            'title' => 'Detalles de Compañia',
            'cia'   => $cia
        ];

        return view('cias/show', $data);
    }
 
    public function edit($id)
    {
        $cia = $this->ciaModel->find($id);
        
        if (!$cia) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Compañia no encontrada');
        }

        $data = [
            'title' => 'Editar Compañia',
            'cia'   => $cia,
            'validation' => \Config\Services::validation()
        ];

        return view('cias/edit', $data);
    }

    /**
     * Procesar actualizacion de Compañia
     */
    public function update($id)
    {
        $cia = $this->ciaModel->find($id);
        
        if (!$cia) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Compañia no encontrada');
        }

        // Validaci�n
        if (!$this->validate([
            'cia_nombre' => 'required|min_length[3]|max_length[255]',
            'cia_direccion' => 'max_length[500]',
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Procesar logo si se subi� uno nuevo
        $logoName = $cia['cia_logo']; // Mantener el logo actual
        $logoFile = $this->request->getFile('cia_logo');
        
        if ($logoFile && $logoFile->isValid() && !$logoFile->hasMoved()) {
            // Validar imagen
            if ($logoFile->getSize() > 2048000 || !in_array($logoFile->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png'])) {
                return redirect()->back()->withInput()->with('error', 'El logo debe ser una imagen válida menor a 2MB');
            }
            
            // Eliminar logo anterior si existe
            if ($cia['cia_logo'] && file_exists('uploads/logos/' . $cia['cia_logo'])) {
                unlink('uploads/logos/' . $cia['cia_logo']);
            }
            
            $logoName = $logoFile->getRandomName();
            $logoFile->move('uploads/logos/', $logoName);
        }

        // Actualizar datos
        $data = [
            'cia_nombre'    => $this->request->getPost('cia_nombre'),
            'cia_direccion' => $this->request->getPost('cia_direccion'),
            'cia_logo'      => $logoName,
            'cia_habil'     => $this->request->getPost('cia_habil')
        ];

        if ($this->ciaModel->update($id, $data)) {
            return redirect()->to('/cias')->with('success', 'Compañia actualizada exitosamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar la Compañia');
        }
    }

    /**
     * Eliminar Compañia
     */
    public function delete($id)
    {
        $cia = $this->ciaModel->find($id);
        
        if (!$cia) {
            return redirect()->to('/cias')->with('error', 'Compañia no encontrada');
        }

        // Verificar si se puede eliminar
        if (!$this->ciaModel->canDelete($id)) {
            return redirect()->to('/cias')->with('error', 'No se puede eliminar la Compañia porque tiene usuarios asociados');
        }

        // Eliminar logo si existe
        if ($cia['cia_logo'] && file_exists('uploads/logos/' . $cia['cia_logo'])) {
            unlink('uploads/logos/' . $cia['cia_logo']);
        }

        if ($this->ciaModel->delete($id)) {
            return redirect()->to('/cias')->with('success', 'Compañia eliminada exitosamente');
        } else {
            return redirect()->to('/cias')->with('error', 'Error al eliminar la Compañia');
        }
    } 
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/cias');
        }

        if ($this->ciaModel->toggleStatus($id)) {
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
     * Obtener compa��as para select (AJAX)
     */
    public function getSelect()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/cias');
        }

        $cias = $this->ciaModel->getActiveCias();
        
        return $this->response->setJSON($cias);
    }
}