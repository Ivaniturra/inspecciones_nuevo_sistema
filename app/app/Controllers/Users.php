<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\CiaModel;
use App\Models\PerfilModel;
use App\Models\AuditLogModel;

class Users extends BaseController
{
    protected $userModel;
    protected $ciaModel;
    protected $perfilModel;
    protected $auditModel;
    protected $session;

    public function __construct()
    {
        $this->userModel  = new UserModel();
        $this->ciaModel   = new CiaModel();
        $this->perfilModel= new PerfilModel();
        $this->auditModel = new AuditLogModel();
        $this->session    = session();
    }

    /** Listado */
    public function index()
    {
        $stats = $this->userModel->getEnhancedStats();

        $data = [
            'title'     => 'Gestión de Usuarios',
            'usuarios'  => $this->userModel->getUsersWithDetails(),
            'stats'     => $this->userModel->getEnhancedStats(),
            'canCreate' => $this->hasPermission('create_users'),
            'canEdit'   => $this->hasPermission('update_users'),
            'canDelete' => $this->hasPermission('delete_users'),
        ];

        $this->logAuditAction('users_index_viewed', ['total_users' => $stats['total']]);

        return view('users/index', $data);
    }

    /** Form crear */
    public function create()
    {
        $data = [
            'title'             => 'Nuevo Usuario',
            'cias'              => $this->ciaModel->getActiveCias(),
            'perfiles'          => $this->perfilModel->getPerfilesByTipo(),
            'perfilesCompania'  => $this->perfilModel->getPerfilesCompania(),
            'perfilesInternos'  => $this->perfilModel->getPerfilesInternos(),
        ];
        return view('users/create', $data);
    }

    /** Guardar nuevo (con rate limit, validación y avatar en WRITEPATH) */
    public function store()
    {
        helper(['form']);
        log_message('debug', 'Users::store POST => ' . json_encode($this->request->getPost()));

        $rules = [
            'user_nombre' => 'required|min_length[3]|max_length[100]|alpha_space',
            'user_email'  => 'required|valid_email|is_unique[users.user_email]',
            'user_perfil' => 'required|integer',
            'user_clave'  => 'required|min_length[8]',
            'confirmar_clave' => 'required|matches[user_clave]',
        ];

        if (!$this->validate($rules)) {
            log_message('error', 'VALIDATION ERRORS: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $data = [
            'user_nombre' => trim((string)$this->request->getPost('user_nombre')),
            'user_email'  => strtolower(trim((string)$this->request->getPost('user_email'))),
            'user_telefono' => trim((string)$this->request->getPost('user_telefono')),
            'user_perfil' => (int)$this->request->getPost('user_perfil'),
            'cia_id'      => ($this->request->getPost('cia_id') === '' ? null : (int)$this->request->getPost('cia_id')),
            'user_clave'  => $this->request->getPost('user_clave'), // el modelo la hashea
            'user_habil'  => (int)($this->request->getPost('user_habil') ?? 1),
        ];

        try {
            if ($id = $this->userModel->insert($data)) {
                log_message('debug', 'INSERT OK user_id=' . $id);
                return redirect()->to('/users')->with('success', 'Usuario creado exitosamente')->with('new_user_id', $id);
            } else {
                log_message('error', 'MODEL ERRORS: ' . json_encode($this->userModel->errors()));
                return redirect()->back()->withInput()->with('errors', $this->userModel->errors() ?: ['insert' => 'Error al crear el usuario']);
            }
        } catch (\Throwable $e) {
            log_message('critical', 'EXCEPTION store(): ' . $e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Error inesperado al crear el usuario');
        }
    }

    /** Ver usuario */
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

        if (! $usuario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Usuario no encontrado');
        }

        $loginHistory     = $this->userModel->getLoginHistory($id, 10);
        $preferences      = $this->userModel->getUserPreferences($id);
        $securitySettings = $this->userModel->getSecuritySettings($id);

        $data = [
            'title'            => 'Detalles del Usuario',
            'usuario'          => $usuario,
            'loginHistory'     => $loginHistory,
            'preferences'      => $preferences,
            'securitySettings' => $securitySettings,
        ];

        $this->logAuditAction('user_viewed', [
            'viewed_user_id'    => $id,
            'viewed_user_email' => $usuario['user_email'] ?? null,
        ]);

        return view('users/show', $data);
    }

    /** Form editar */
    public function edit($id)
    {
        $usuario = $this->userModel->find($id);
        if (! $usuario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Usuario no encontrado');
        }

        if ($this->userModel->needsPasswordChange($id)) {
            $this->session->setFlashdata('warning', 'Este usuario necesita cambiar su contraseña');
        }

        $data = [
            'title'            => 'Editar Usuario',
            'usuario'          => $usuario,
            'cias'             => $this->ciaModel->getActiveCias(),
            'perfiles'         => $this->perfilModel->getPerfilesByTipo(),
            'perfilesCompania' => $this->perfilModel->getPerfilesCompania(),
            'perfilesInternos' => $this->perfilModel->getPerfilesInternos(),
        ];

        return view('users/edit', $data);
    }

    /** Actualizar */
    public function update($id)
    {
        $usuario = $this->userModel->find($id);
        if (! $usuario) {
            throw new \CodeIgniter\Exceptions\PageNotFoundException('Usuario no encontrado');
        }

        $rules = [
            'user_nombre' => [
                'label' => 'Nombre',
                'rules' => 'required|min_length[3]|max_length[100]|regex_match[/^[\p{L}\s\.\'\-]+$/u]',
            ],
            'user_email'  => "required|valid_email|is_unique[users.user_email,user_id,{$id}]",
            'user_perfil' => 'required|integer',
            'user_avatar' => 'permit_empty|is_image[user_avatar]|mime_in[user_avatar,image/jpg,image/jpeg,image/png]|max_size[user_avatar,1024]',
        ];

        // Contraseña opcional
        if (! empty($this->request->getPost('user_clave'))) {
            $rules['user_clave']       = 'regex_match[/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/]';
            $rules['confirmar_clave']  = 'matches[user_clave]';
        }

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }

        $customErrors = $this->userModel->validateUserByProfileType($this->request->getPost());
        if (! empty($customErrors)) {
            return redirect()->back()->withInput()->with('errors', $customErrors);
        }

        // Avatar
        $avatarName = $usuario['user_avatar'];
        $file = $this->request->getFile('user_avatar');
        if ($file && $file->isValid() && ! $file->hasMoved()) {
            // borra anterior si existe
            $dir = WRITEPATH . 'uploads/avatars';
            if ($avatarName) {
                $oldPath = $dir . DIRECTORY_SEPARATOR . $avatarName;
                if (is_file($oldPath)) { @unlink($oldPath); }
            }
            $avatarName = $this->processAvatar($file);
        }

        $ciaId = $this->request->getPost('cia_id');
        $ciaId = ($ciaId === '' || $ciaId === null) ? null : (int) $ciaId;

        $data = [
            'user_nombre' => $this->sanitizeInput($this->request->getPost('user_nombre')),
            'user_email'  => strtolower(trim((string) $this->request->getPost('user_email'))),
            'user_telefono'=> $this->sanitizeInput($this->request->getPost('user_telefono')),
            'user_perfil' => (int) $this->request->getPost('user_perfil'),
            'cia_id'      => $ciaId,
            'user_avatar' => $avatarName,
            'user_habil'  => (int) $this->request->getPost('user_habil'),
        ];

        if (! empty($this->request->getPost('user_clave'))) {
            $data['user_clave']              = (string) $this->request->getPost('user_clave');
            $data['user_debe_cambiar_clave'] = 0;
        }

        $oldData = [
            'nombre' => $usuario['user_nombre'],
            'email'  => $usuario['user_email'],
            'perfil' => $usuario['user_perfil'],
            'habil'  => $usuario['user_habil'],
        ];

        if ($this->userModel->update($id, $data)) {
            $this->logAuditAction('user_updated', [
                'user_id'    => $id,
                'old_data'   => $oldData,
                'new_data'   => $data,
                'updated_by' => $this->session->get('user_id') ?? 'system',
            ]);

            return redirect()->to('/users')->with('success', 'Usuario actualizado exitosamente');
        }

        return redirect()->back()->withInput()->with('error', 'Error al actualizar el usuario');
    }

    /** Eliminar */
    public function delete($id)
    {
        $id     = (int) $id;
        $isAjax = $this->request->isAJAX();

        // 1) Permisos
        if (! $this->hasPermission('delete_users')) {
            $msg = 'No tienes permisos para eliminar usuarios';
            return $isAjax
                ? $this->response->setJSON(['success' => false, 'message' => $msg])->setStatusCode(403)
                : redirect()->to('/users')->with('error', $msg);
        }

        // 2) Existe el usuario
        $usuario = $this->userModel->find($id);
        if (! $usuario) {
            $msg = 'Usuario no encontrado';
            return $isAjax
                ? $this->response->setJSON(['success' => false, 'message' => $msg])->setStatusCode(404)
                : redirect()->to('/users')->with('error', $msg);
        }

        // 3) No permitir auto-eliminación
        if ($id === (int) ($this->session->get('user_id'))) {
            $msg = 'No puedes eliminar tu propio usuario';
            return $isAjax
                ? $this->response->setJSON(['success' => false, 'message' => $msg])->setStatusCode(409)
                : redirect()->to('/users')->with('error', $msg);
        }

        // 4) Reglas de negocio
        if (! $this->userModel->canDelete($id)) {
            $msg = 'No se puede eliminar el usuario porque tiene datos asociados';
            return $isAjax
                ? $this->response->setJSON(['success' => false, 'message' => $msg])->setStatusCode(409)
                : redirect()->to('/users')->with('error', $msg);
        }

        // 5) Borrar avatar desde /public/uploads/avatars
        if (! empty($usuario['user_avatar'])) {
            $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'avatars' . DIRECTORY_SEPARATOR . $usuario['user_avatar'];
            if (is_file($path)) { @unlink($path); }
        }

        // 6) Eliminar
        if ($this->userModel->delete($id)) {
            $this->logAuditAction('user_deleted', [
                'deleted_user_id'    => $id,
                'deleted_user_email' => $usuario['user_email'] ?? null,
                'deleted_by'         => $this->session->get('user_id') ?? 'system',
            ]);

            $msg = 'Usuario eliminado exitosamente';
            return $isAjax
                ? $this->response->setJSON(['success' => true, 'message' => $msg])
                : redirect()->to('/users')->with('success', $msg);
        }

        $msg = 'Error al eliminar el usuario';
        return $isAjax
            ? $this->response->setJSON(['success' => false, 'message' => $msg])->setStatusCode(500)
            : redirect()->to('/users')->with('error', $msg);
    }

    /** Toggle status (AJAX) */
    public function toggleStatus($id)
    {
         if (!$this->request->isAJAX()) {
            return redirect()->to('/users');
        }

        if (!$this->hasPermission('gestionar_usuarios')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No tienes permisos para cambiar estados de usuarios'
            ]);
        }

        $usuario = $this->userModel->find($id);
        if (! $usuario) {
            return $this->response->setJSON(['success' => false, 'message' => 'Usuario no encontrado']);
        }

        $oldStatus = (int) $usuario['user_habil'];

        if ($this->userModel->toggleStatus($id)) {
            $newStatus = $oldStatus === 1 ? 0 : 1;

            $this->logAuditAction('user_status_changed', [
                'user_id'    => $id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $this->session->get('user_id') ?? 'system',
            ]);

            return $this->response->setJSON(['success' => true, 'message' => 'Estado actualizado correctamente', 'new_status' => $newStatus]);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al actualizar el estado']);
    }

    /** Reset password (AJAX) */
    public function resetPassword($id)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/users')->with('error', 'Método no permitido');
        }

        if (! $this->hasPermission('reset_passwords')) {
            return $this->response->setJSON(['success' => false, 'message' => 'No tienes permisos para resetear contraseñas']);
        }

        // Rate limit para reset
        if (! $this->checkRateLimit('reset_password', 10, 60)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Demasiados intentos. Intenta más tarde.']);
        }

        $usuario = $this->userModel->find($id);
        if (! $usuario) {
            log_message('warning', "Intento de reset de contraseña para usuario inexistente: {$id} por IP: {$this->request->getIPAddress()}");
            return $this->response->setJSON(['success' => false, 'message' => 'Usuario no encontrado']);
        }

        $tempPassword = $this->generateSecurePassword(12);

        try {
            $updateData = [
                'user_clave'              => $tempPassword,
                'user_intentos_login'     => 0,
                'user_token_reset'        => null,
                'user_debe_cambiar_clave' => 1,
            ];

            if ($this->userModel->update($id, $updateData)) {
                $this->logAuditAction('password_reset', [
                    'target_user_id'    => $id,
                    'target_user_email' => $usuario['user_email'] ?? null,
                    'reset_by'          => $this->session->get('user_id') ?? 'system',
                    'ip_address'        => $this->request->getIPAddress(),
                ]);

                return $this->response->setJSON([
                    'success'      => true,
                    'message'      => 'Contraseña reseteada exitosamente',
                    'tempPassword' => $tempPassword,
                    'userName'     => $usuario['user_nombre'] ?? '',
                    'userEmail'    => $usuario['user_email'] ?? '',
                ]);
            }

            throw new \Exception('Error al actualizar la base de datos');
        } catch (\Exception $e) {
            log_message('error', 'Error reseteando contraseña: ' . $e->getMessage());
            return $this->response->setJSON(['success' => false, 'message' => 'Error al procesar la solicitud']);
        }
    }

    /** Preferencias (AJAX) */
    public function updatePreferences($id)
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/users');
        }

        if ($id != $this->session->get('user_id') && ! $this->hasPermission('manage_users')) {
            return $this->response->setJSON(['success' => false, 'message' => 'No tienes permisos para modificar estas preferencias']);
        }

        $preferences = $this->request->getJSON(true);
        $allowed     = ['theme','language','notifications','timezone','date_format','items_per_page'];
        $filtered    = array_intersect_key($preferences ?? [], array_flip($allowed));

        if ($this->userModel->updateUserPreferences($id, $filtered)) {
            return $this->response->setJSON(['success' => true, 'message' => 'Preferencias actualizadas correctamente']);
        }

        return $this->response->setJSON(['success' => false, 'message' => 'Error al actualizar preferencias']);
    }

    /** Stats (AJAX) */
    public function getStats()
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/users');
        }
        $stats = $this->userModel->getEnhancedStats();
        return $this->response->setJSON($stats);
    }

    /** Check fuerza password (AJAX) */
    public function checkPasswordStrength()
    {
        if (! $this->request->isAJAX()) {
            return redirect()->to('/users');
        }
        $password = (string) $this->request->getPost('password');
        if ($password === '') {
            return $this->response->setJSON([
                'strength' => 0,
                'level'    => 'empty',
                'feedback' => ['La contraseña no puede estar vacía'],
            ]);
        }
        return $this->response->setJSON($this->getPasswordStrength($password));
    }

    /* ===================== Helpers privados ===================== */

    private function hasPermission(string $permission): bool
    {
        // Bypass temporal mientras no haya login (solo en dev)
        if (! $this->session->get('user_id')) {
            // ENVIRONMENT es 'development' | 'testing' | 'production'
            return (ENVIRONMENT !== 'production'); // true en dev/testing, false en prod
        }

        $user = $this->userModel
            ->select('users.user_id, users.user_perfil, perfiles.perfil_permisos, perfiles.perfil_nivel')
            ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
            ->where('users.user_id', (int)$this->session->get('user_id'))
            ->first();

        if (! $user) {
            return false;
        }

        // Admin por nivel o acceso_total
        $permisos = is_array($user['perfil_permisos'])
            ? $user['perfil_permisos']
            : (json_decode((string)$user['perfil_permisos'], true) ?: []);

        if (!empty($permisos['acceso_total']) || (int)($user['perfil_nivel'] ?? 0) >= 4) {
            return true;
        }

        $alias = [
            'view_users'   => 'gestionar_usuarios',
            'create_users' => 'gestionar_usuarios',
            'update_users' => 'gestionar_usuarios',
            'delete_users' => 'gestionar_usuarios',
        ];
        $key = $alias[$permission] ?? $permission;

        return !empty($permisos[$key]);
    }

    private function logAuditAction(string $action, array $details): void
    {
        try {
            $this->auditModel->insert([
                'action'     => $action,
                'user_id'    => $this->session->get('user_id'),
                'ip_address' => $this->request->getIPAddress(),
                'user_agent' => $this->request->getUserAgent()->getAgentString(),
                'details'    => json_encode($details, JSON_UNESCAPED_UNICODE),
                'created_at' => date('Y-m-d H:i:s'),
            ]);
        } catch (\Exception $e) {
            log_message('error', 'Error registrando auditoría: ' . $e->getMessage());
        }
    }

    /** Rate limit seguro usando Throttler */
    private function checkRateLimit(string $action, int $maxAttempts = 5, int $windowSeconds = 900): bool
    {
        $ip  = $this->request->getIPAddress();
        $ua  = $this->request->getUserAgent()->getAgentString();
        $key = 'rl_' . md5($action . '|' . $ip . '|' . $ua); // sin caracteres reservados
        return service('throttler')->check($key, $maxAttempts, $windowSeconds);
    }

    private function generateSecurePassword(int $length = 12): string
    {
        $uppercase = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $lowercase = 'abcdefghijklmnopqrstuvwxyz';
        $numbers   = '0123456789';
        $special   = '!@#$%^&*()_+-=[]{}|;:,.<>?';

        $password  = '';
        $password .= $uppercase[random_int(0, strlen($uppercase) - 1)];
        $password .= $lowercase[random_int(0, strlen($lowercase) - 1)];
        $password .= $numbers[random_int(0, strlen($numbers) - 1)];
        $password .= $special[random_int(0, strlen($special) - 1)];

        $allChars  = $uppercase . $lowercase . $numbers . $special;
        for ($i = 4; $i < $length; $i++) {
            $password .= $allChars[random_int(0, strlen($allChars) - 1)];
        }
        return str_shuffle($password);
    }

    private function getPasswordStrength(string $password): array
    {
        $strength = 0;
        $feedback = [];

        if (strlen($password) >= 8)  $strength += 20;
        if (strlen($password) >= 12) $strength += 20; else $feedback[] = 'Usar al menos 12 caracteres';

        if (preg_match('/[a-z]/', $password)) $strength += 15; else $feedback[] = 'Agregar letras minúsculas';
        if (preg_match('/[A-Z]/', $password)) $strength += 15; else $feedback[] = 'Agregar letras mayúsculas';
        if (preg_match('/[0-9]/', $password)) $strength += 15; else $feedback[] = 'Agregar números';
        if (preg_match('/[^a-zA-Z0-9]/', $password)) $strength += 15; else $feedback[] = 'Agregar caracteres especiales';

        $level = 'weak';
        if ($strength >= 60) $level = 'medium';
        if ($strength >= 80) $level = 'strong';
        if ($strength >= 100) $level = 'very_strong';

        return ['strength' => $strength, 'level' => $level, 'feedback' => $feedback];
    }

    private function sanitizeInput($input)
    {
        if (is_array($input)) {
            return array_map([$this, 'sanitizeInput'], $input);
        }
        return htmlspecialchars(trim((string) $input), ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    /** Guardar avatar en WRITEPATH/uploads/avatars y devolver nombre */
    private function processAvatar($file): string
    {
        $newName = $file->getRandomName();
        $dir = WRITEPATH . 'uploads/avatars';
        if (! is_dir($dir)) { @mkdir($dir, 0775, true); }
        $file->move($dir, $newName);
        return $newName;
    }
}
