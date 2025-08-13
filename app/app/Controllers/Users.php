<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CiaModel;
use App\Models\PerfilModel;

class Users extends BaseController
{
    protected $userModel;
    protected $ciaModel;
    protected $perfilModel;

    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->ciaModel = new CiaModel();
        $this->perfilModel = new PerfilModel();
    }

    /**
     * Mostrar listado de usuarios
     */
    public function index()
    {
        $data = [
            'title' => 'Gestión de Usuarios',
            'usuarios' => $this->userModel->getUsersWithDetails(),
            'stats' => $this->userModel->getStats()
        ];

        return view('users/index', $data);
    }

    /**
     * Mostrar formulario de creación
     */
    public function create()
    {
        $data = [
            'title' => 'Nuevo Usuario',
            'cias' => $this->ciaModel->getActiveCias(),
            'perfiles' => $this->perfilModel->getPerfilesByTipo(),
            'perfilesCompania' => $this->perfilModel->getPerfilesCompania(),
            'perfilesInternos' => $this->perfilModel->getPerfilesInternos()
        ];

        return view('users/create', $data);
    }

    /**
     * Procesar creación de usuario
     */
    public function store()
    {
        // Validación básica
        if (!$this->validate([
            'user_nombre' => 'required|min_length[3]|max_length[100]',
            'user_email' => 'required|valid_email|is_unique[users.user_email]',
            'user_perfil' => 'required|integer',
            'user_clave' => 'required|min_length[6]',
            'confirmar_clave' => 'required|matches[user_clave]'
        ])) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validación personalizada según tipo de perfil
        $customErrors = $this->userModel->validateUserByProfileType($this->request->getPost());
        if (!empty($customErrors)) {
            return redirect()->back()->withInput()->with('errors', $customErrors);
        }

        // Procesar avatar si se subió
        $avatarName = null;
        $avatarFile = $this->request->getFile('user_avatar');
        
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            // Validar imagen
            if ($avatarFile->getSize() > 1048576 || !in_array($avatarFile->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png'])) {
                return redirect()->back()->withInput()->with('error', 'El avatar debe ser una imagen válida menor a 1MB');
            }
            
            $avatarName = $avatarFile->getRandomName();
            $avatarFile->move('uploads/avatars/', $avatarName);
        }

        // Preparar datos
        $ciaId = $this->request->getPost('cia_id');
        if (empty($ciaId)) {
            $ciaId = null; // Para usuarios internos
        }

        $data = [
            'user_nombre' => $this->request->getPost('user_nombre'),
            'user_email' => $this->request->getPost('user_email'),
            'user_telefono' => $this->request->getPost('user_telefono'),
            'user_perfil' => $this->request->getPost('user_perfil'),
            'cia_id' => $ciaId,
            'user_clave' => $this->request->getPost('user_clave'),
            'user_avatar' => $avatarName,
            'user_habil' => $this->request->getPost('user_habil') ?? 1
        ];

        if ($this->userModel->save($data)) {
            return redirect()->to('/users')->with('success', 'Usuario creado exitosamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al crear el usuario');
        }
    }

    /**
     * Mostrar detalles de un usuario
     */
    public function show($id)
    {
        $usuario = $this->userModel->select('users.*, 
                                          cias.cia_nombre, 
                                          perfiles.perfil_nombre, 
                                          perfiles.perfil_tipo,
                                          perfiles.perfil_nivel,
                                          perfiles.perfil_permisos')
                                 ->join('cias', 'cias.cia_id = users.cia_id', 'left')
                                 ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
                                 ->find($id);
        
        if (!$usuario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Usuario no encontrado');
        }

        $data = [
            'title' => 'Detalles del Usuario',
            'usuario' => $usuario
        ];

        return view('users/show', $data);
    }

    /**
     * Mostrar formulario de edición
     */
    public function edit($id)
    {
        $usuario = $this->userModel->find($id);
        
        if (!$usuario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Usuario no encontrado');
        }

        $data = [
            'title' => 'Editar Usuario',
            'usuario' => $usuario,
            'cias' => $this->ciaModel->getActiveCias(),
            'perfiles' => $this->perfilModel->getPerfilesByTipo(),
            'perfilesCompania' => $this->perfilModel->getPerfilesCompania(),
            'perfilesInternos' => $this->perfilModel->getPerfilesInternos()
        ];

        return view('users/edit', $data);
    }

    /**
     * Procesar actualización de usuario
     */
    public function update($id)
    {
        $usuario = $this->userModel->find($id);
        
        if (!$usuario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Usuario no encontrado');
        }

        // Validación (excluir email actual del is_unique)
        $rules = [
            'user_nombre' => 'required|min_length[3]|max_length[100]',
            'user_email' => "required|valid_email|is_unique[users.user_email,user_id,$id]",
            'user_perfil' => 'required|integer'
        ];

        // Solo validar contraseña si se proporciona
        if (!empty($this->request->getPost('user_clave'))) {
            $rules['user_clave'] = 'min_length[6]';
            $rules['confirmar_clave'] = 'matches[user_clave]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validación personalizada según tipo de perfil
        $customErrors = $this->userModel->validateUserByProfileType($this->request->getPost());
        if (!empty($customErrors)) {
            return redirect()->back()->withInput()->with('errors', $customErrors);
        }

        // Procesar avatar si se subió uno nuevo
        $avatarName = $usuario['user_avatar']; // Mantener el actual
        $avatarFile = $this->request->getFile('user_avatar');
        
        if ($avatarFile && $avatarFile->isValid() && !$avatarFile->hasMoved()) {
            // Validar imagen
            if ($avatarFile->getSize() > 1048576 || !in_array($avatarFile->getMimeType(), ['image/jpeg', 'image/jpg', 'image/png'])) {
                return redirect()->back()->withInput()->with('error', 'El avatar debe ser una imagen válida menor a 1MB');
            }
            
            // Eliminar avatar anterior si existe
            if ($usuario['user_avatar'] && file_exists('uploads/avatars/' . $usuario['user_avatar'])) {
                unlink('uploads/avatars/' . $usuario['user_avatar']);
            }
            
            $avatarName = $avatarFile->getRandomName();
            $avatarFile->move('uploads/avatars/', $avatarName);
        }

        // Preparar datos
        $ciaId = $this->request->getPost('cia_id');
        if (empty($ciaId)) {
            $ciaId = null; // Para usuarios internos
        }

        $data = [
            'user_nombre' => $this->request->getPost('user_nombre'),
            'user_email' => $this->request->getPost('user_email'),
            'user_telefono' => $this->request->getPost('user_telefono'),
            'user_perfil' => $this->request->getPost('user_perfil'),
            'cia_id' => $ciaId,
            'user_avatar' => $avatarName,
            'user_habil' => $this->request->getPost('user_habil')
        ];

        // Solo actualizar contraseña si se proporciona
        if (!empty($this->request->getPost('user_clave'))) {
            $data['user_clave'] = $this->request->getPost('user_clave');
        }

        if ($this->userModel->update($id, $data)) {
            return redirect()->to('/users')->with('success', 'Usuario actualizado exitosamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al actualizar el usuario');
        }
    }

    /**
     * Eliminar usuario
     */
    public function delete($id)
    {
        $usuario = $this->userModel->find($id);
        
        if (!$usuario) {
            return redirect()->to('/users')->with('error', 'Usuario no encontrado');
        }

        // Verificar si se puede eliminar
        if (!$this->userModel->canDelete($id)) {
            return redirect()->to('/users')->with('error', 'No se puede eliminar el usuario porque tiene datos asociados');
        }

        // Eliminar avatar si existe
        if ($usuario['user_avatar'] && file_exists('uploads/avatars/' . $usuario['user_avatar'])) {
            unlink('uploads/avatars/' . $usuario['user_avatar']);
        }

        if ($this->userModel->delete($id)) {
            return redirect()->to('/users')->with('success', 'Usuario eliminado exitosamente');
        } else {
            return redirect()->to('/users')->with('error', 'Error al eliminar el usuario');
        }
    }

    /**
     * Cambiar estado de usuario (AJAX)
     */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/users');
        }

        if ($this->userModel->toggleStatus($id)) {
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
     * Resetear contraseña
     */
    public function resetPassword($id)
    {
        // Verificar si es una petición AJAX
        if (!$this->request->isAJAX()) {
            return redirect()->to('/users')->with('error', 'Método no permitido');
        }

        $usuario = $this->userModel->find($id);
        
        if (!$usuario) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ]);
        }

        // Generar contraseña temporal más segura
        $tempPassword = 'Temp@' . rand(1000, 9999) . chr(rand(65, 90)); // Ej: Temp@1234A
        
        try {
            // IMPORTANTE: Hashear la contraseña antes de guardarla
            $hashedPassword = password_hash($tempPassword, PASSWORD_DEFAULT);
            
            if ($this->userModel->update($id, [
                'user_clave' => $hashedPassword,  // Guardar el hash, NO el texto plano
                'user_intentos_login' => 0,
                'user_token_reset' => null,
                'user_debe_cambiar_clave' => 1  // Marcar que debe cambiar la contraseña
            ])) {
                // Retornar respuesta JSON para el AJAX
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Contraseña reseteada exitosamente',
                    'tempPassword' => $tempPassword,  // Enviar la contraseña para mostrarla
                    'userName' => $usuario['user_nombre'],
                    'userEmail' => $usuario['user_email']
                ]);
            } else {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Error al resetear la contraseña'
                ]);
            }
        } catch (\Exception $e) {
            log_message('error', 'Error reseteando contraseña: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al procesar la solicitud'
            ]);
        }
    }

    /**
     * Obtener usuarios por compañía (AJAX)
     */
    public function getByCompany($ciaId = null)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/users');
        }

        if ($ciaId) {
            $usuarios = $this->userModel->getUsersByCompany($ciaId);
        } else {
            $usuarios = $this->userModel->getInternalUsers();
        }
        
        return $this->response->setJSON($usuarios);
    }

    /**
     * Obtener estadísticas (AJAX)
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/users');
        }

        $stats = $this->userModel->getStats();
        
        return $this->response->setJSON($stats);
    }
}