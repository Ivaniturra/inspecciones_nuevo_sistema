<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Ubicaci?n: app/Models/UserModel.php
 * Reemplaza tu UserModel actual con este c?digo
 */
class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'user_id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;

    // Campos permitidos - AGREGADOS los nuevos campos JSON
    protected $allowedFields = [
        'user_nombre',
        'user_email',
        'user_telefono',
        'user_perfil',
        'cia_id',
        'user_clave',
        'user_avatar',
        'user_ultimo_acceso',
        'user_intentos_login',
        'user_token_reset',
        'user_habil',
        'user_debe_cambiar_clave',      // NUEVO
        'user_metadata',                 // NUEVO - JSON
        'user_preferences',              // NUEVO - JSON
        'user_security_settings',        // NUEVO - JSON
        'user_login_history'            // NUEVO - JSON
    ];

    // Callbacks
    protected $beforeInsert = ['normalizeInput', 'hashPassword', 'setDefaultJsonFields'];
    protected $beforeUpdate = ['normalizeInput', 'hashPasswordIfProvided', 'updateMetadata'];
    protected $afterFind    = ['parseJsonFields'];

    // Validaci?n
    protected $validationRules = [
        'user_nombre' => 'required|min_length[3]|max_length[100]',
        'user_email'  => 'required|valid_email|max_length[255]|is_unique[users.user_email,user_id,{user_id}]',
        'user_perfil' => 'required|integer|is_not_unique[perfiles.perfil_id]',
        'user_clave'  => 'permit_empty|min_length[6]',
        'user_habil'  => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'user_nombre' => [
            'required'    => 'El nombre es obligatorio',
            'min_length'  => 'El nombre debe tener al menos 3 caracteres',
            'max_length'  => 'El nombre no puede exceder 100 caracteres',
        ],
        'user_email' => [
            'required'    => 'El email es obligatorio',
            'valid_email' => 'Debe ser un email v?lido',
            'is_unique'   => 'Este email ya est? registrado',
        ],
        'user_perfil' => [
            'required'      => 'El perfil es obligatorio',
            'is_not_unique' => 'El perfil seleccionado no existe',
        ],
        'user_clave' => [
            'min_length' => 'La contrase?a debe tener al menos 6 caracteres',
        ],
    ];

    // =====================================================
    // M?TODOS EXISTENTES (los que ya ten?as)
    // =====================================================
    
    public function getUsersWithDetails(): array
    {
        return $this->select(
                'users.*, ' .
                'cias.cia_nombre, ' .
                'perfiles.perfil_nombre, ' .
                'perfiles.perfil_tipo, ' .
                'perfiles.perfil_nivel'
            )
            ->join('cias', 'cias.cia_id = users.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
            ->orderBy('users.user_nombre', 'ASC')
            ->findAll();
    }

    public function getUsersByCompany(int $ciaId): array
    {
        return $this->select(
                'users.*, ' .
                'cias.cia_nombre, ' .
                'perfiles.perfil_nombre, ' .
                'perfiles.perfil_tipo'
            )
            ->join('cias', 'cias.cia_id = users.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
            ->where('users.cia_id', $ciaId)
            ->where('users.user_habil', 1)
            ->orderBy('users.user_nombre', 'ASC')
            ->findAll();
    }

    public function getInternalUsers(): array
    {
        return $this->select(
                'users.*, ' .
                'perfiles.perfil_nombre, ' .
                'perfiles.perfil_tipo'
            )
            ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
            ->where('users.cia_id IS NULL')
            ->where('users.user_habil', 1)
            ->orderBy('users.user_nombre', 'ASC')
            ->findAll();
    }

    public function getUsersByProfileType(string $tipo): array
    {
        return $this->select(
                'users.*, ' .
                'cias.cia_nombre, ' .
                'perfiles.perfil_nombre, ' .
                'perfiles.perfil_tipo'
            )
            ->join('cias', 'cias.cia_id = users.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
            ->where('perfiles.perfil_tipo', $tipo)
            ->where('users.user_habil', 1)
            ->orderBy('users.user_nombre', 'ASC')
            ->findAll();
    }

    public function findByEmail(string $email): ?array
    {
        $email = strtolower(trim($email));

        $row = $this->select(
                'users.*, ' .
                'cias.cia_nombre, ' .
                'perfiles.perfil_nombre, ' .
                'perfiles.perfil_tipo, ' .
                'perfiles.perfil_permisos'
            )
            ->join('cias', 'cias.cia_id = users.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
            ->where('users.user_email', $email)
            ->where('users.user_habil', 1)
            ->first();

        return $row ?: null;
    }

    public function verifyPassword(string $password, string $hash): bool
    {
        return password_verify($password, $hash);
    }

    public function updateLastAccess(int $userId): bool
    {
        return (bool) $this->update($userId, [
            'user_ultimo_acceso' => date('Y-m-d H:i:s'),
            'user_intentos_login' => 0,
        ]);
    }

    public function incrementLoginAttempts(int $userId): int
    {
        $this->builder()
            ->set('user_intentos_login', 'user_intentos_login + 1', false)
            ->where('user_id', $userId)
            ->update();

        $user = $this->find($userId);
        if ($user) {
            $intentos = (int) ($user['user_intentos_login'] ?? 0);

            // Bloquear despu?s de 5 intentos
            if ($intentos >= 5) {
                $this->update($userId, ['user_habil' => 0]);
            }
            return $intentos;
        }
        return 0;
    }

    public function toggleStatus(int $id): bool
    {
        $user = $this->find($id);
        if (!$user) {
            return false;
        }
        $newStatus = (int) ($user['user_habil'] == 1 ? 0 : 1);
        return (bool) $this->update($id, [
            'user_habil' => $newStatus,
            'user_intentos_login' => 0,
        ]);
    }

    public function generateResetToken(string $email)
    {
        $email = strtolower(trim($email));
        $user = $this->where('user_email', $email)->first();
        if (!$user) {
            return false;
        }

        $token = bin2hex(random_bytes(32));
        $this->update((int)$user['user_id'], ['user_token_reset' => $token]);
        return $token;
    }

    public function canDelete(int $id): bool
    {
        // Aqu? puedes agregar validaciones adicionales
        return true;
    }

    public function getStats(): array
    {
        return [
            'total'     => $this->countAll(),
            'activos'   => $this->where('user_habil', 1)->countAllResults(false),
            'inactivos' => $this->where('user_habil', 0)->countAllResults(false),
            'internos'  => $this->where('cia_id IS NULL')->countAllResults(false),
            'externos'  => $this->where('cia_id IS NOT NULL')->countAllResults(false),
        ];
    }

    public function validateUserByProfileType(array $data): array
    {
        $perfilModel = new \App\Models\PerfilModel();
        $perfil = $perfilModel->find((int) $data['user_perfil']);

        if (!$perfil) {
            return ['user_perfil' => 'El perfil seleccionado no existe'];
        }

        $errors = [];

        if (($perfil['perfil_tipo'] ?? null) === 'compania' && empty($data['cia_id'])) {
            $errors['cia_id'] = 'Los usuarios con perfil de compa??a deben tener una compa??a asignada';
        }

        if (($perfil['perfil_tipo'] ?? null) === 'interno' && !empty($data['cia_id'])) {
            $errors['cia_id'] = 'Los usuarios con perfil interno no pueden tener compa??a asignada';
        }

        return $errors;
    }

    // =====================================================
    // M?TODOS NUEVOS PARA SEGURIDAD Y JSON
    // =====================================================

    /**
     * Configurar campos JSON por defecto al crear usuario
     */
    protected function setDefaultJsonFields(array $data): array
    {
        if (!isset($data['data'])) return $data;

        // Metadata por defecto
        $data['data']['user_metadata'] = json_encode([
            'created_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'created_user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
            'created_at' => date('Y-m-d H:i:s'),
            'last_password_change' => date('Y-m-d H:i:s'),
            'password_history' => [],
            'failed_login_attempts' => []
        ]);

        // Preferencias por defecto
        $data['data']['user_preferences'] = json_encode([
            'theme' => 'light',
            'language' => 'es',
            'notifications' => [
                'email' => true,
                'sms' => false,
                'push' => true
            ],
            'timezone' => 'America/Santiago',
            'date_format' => 'd/m/Y',
            'items_per_page' => 25
        ]);

        // Configuraciones de seguridad
        $data['data']['user_security_settings'] = json_encode([
            'two_factor_enabled' => false,
            'two_factor_method' => null,
            'session_timeout' => 3600,
            'ip_whitelist' => [],
            'trusted_devices' => [],
            'security_questions' => []
        ]);

        // Historial de login vac?o
        $data['data']['user_login_history'] = json_encode([]);

        return $data;
    }

    /**
     * Actualizar metadata cuando se modifica el usuario
     */
    protected function updateMetadata(array $data): array
    {
        if (!isset($data['data']) || !isset($data['id'])) return $data;

        // Si se est? cambiando la contrase?a
        if (isset($data['data']['user_clave']) && !empty($data['data']['user_clave'])) {
            $user = $this->find($data['id'][0] ?? $data['id']);
            if ($user && isset($user['user_metadata'])) {
                $metadata = json_decode($user['user_metadata'] ?? '{}', true);
                
                // Actualizar historial de cambios de contrase?a
                $metadata['password_history'][] = [
                    'changed_at' => date('Y-m-d H:i:s'),
                    'changed_by_ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                ];
                
                // Mantener solo los ?ltimos 5 cambios
                $metadata['password_history'] = array_slice($metadata['password_history'], -5);
                $metadata['last_password_change'] = date('Y-m-d H:i:s');
                
                $data['data']['user_metadata'] = json_encode($metadata);
                $data['data']['user_debe_cambiar_clave'] = 0;
            }
        }

        return $data;
    }

    /**
     * Parsear campos JSON despu?s de obtener datos
     */
    protected function parseJsonFields(array $data): array
    {
        if (!isset($data['data'])) return $data;

        $jsonFields = ['user_metadata', 'user_preferences', 'user_security_settings', 'user_login_history'];

        // Para m?ltiples registros
        if (isset($data['data'][0]) && is_array($data['data'][0])) {
            foreach ($data['data'] as &$row) {
                foreach ($jsonFields as $field) {
                    if (isset($row[$field]) && is_string($row[$field])) {
                        $row[$field] = json_decode($row[$field], true) ?? [];
                    }
                }
            }
        }
        // Para un solo registro
        else {
            foreach ($jsonFields as $field) {
                if (isset($data['data'][$field]) && is_string($data['data'][$field])) {
                    $data['data'][$field] = json_decode($data['data'][$field], true) ?? [];
                }
            }
        }

        return $data;
    }

    /**
     * Normalizar datos de entrada
     */
    protected function normalizeInput(array $data): array
    {
        if (!isset($data['data'])) return $data;

        if (isset($data['data']['user_nombre'])) {
            $data['data']['user_nombre'] = trim($data['data']['user_nombre']);
        }
        if (isset($data['data']['user_email'])) {
            $data['data']['user_email'] = strtolower(trim($data['data']['user_email']));
        }
        if (isset($data['data']['user_telefono'])) {
            $data['data']['user_telefono'] = trim($data['data']['user_telefono']);
        }

        return $data;
    }

    /**
     * Hashear contrase?a en INSERT
     */
    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['user_clave']) && !empty($data['data']['user_clave'])) {
            $data['data']['user_clave'] = password_hash($data['data']['user_clave'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Hashear contrase?a en UPDATE solo si se proporciona
     */
    protected function hashPasswordIfProvided(array $data): array
    {
        if (isset($data['data']['user_clave']) && !empty($data['data']['user_clave'])) {
            // Verificar que no est? ya hasheada
            $info = password_get_info($data['data']['user_clave']);
            if ($info['algo'] === null) {
                $data['data']['user_clave'] = password_hash($data['data']['user_clave'], PASSWORD_DEFAULT);
            }
        } else {
            // Si no se proporciona contrase?a, quitarla del update
            unset($data['data']['user_clave']);
        }
        return $data;
    }

    /**
     * Registrar intento de login (exitoso o fallido)
     */
    public function logLoginAttempt(int $userId, bool $success, string $ip = null): void
    {
        $user = $this->find($userId);
        if (!$user) return;

        $history = json_decode($user['user_login_history'] ?? '[]', true);
        
        // Agregar nuevo intento
        $attempt = [
            'timestamp' => date('Y-m-d H:i:s'),
            'success' => $success,
            'ip' => $ip ?? $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown'
        ];
        
        array_unshift($history, $attempt);
        
        // Mantener solo los ?ltimos 50 intentos
        $history = array_slice($history, 0, 50);
        
        // Si es un intento fallido, actualizar metadata tambi?n
        if (!$success) {
            $metadata = json_decode($user['user_metadata'] ?? '{}', true);
            $metadata['failed_login_attempts'][] = $attempt;
            $metadata['failed_login_attempts'] = array_slice($metadata['failed_login_attempts'], -10);
            
            $this->update($userId, [
                'user_login_history' => json_encode($history),
                'user_metadata' => json_encode($metadata),
                'user_intentos_login' => ($user['user_intentos_login'] ?? 0) + 1
            ]);
        } else {
            $this->update($userId, [
                'user_login_history' => json_encode($history),
                'user_ultimo_acceso' => date('Y-m-d H:i:s'),
                'user_intentos_login' => 0
            ]);
        }
    }

    /**
     * Obtener preferencias del usuario
     */
    public function getUserPreferences(int $userId): array
    {
        $user = $this->find($userId);
        if (!$user || !isset($user['user_preferences'])) return [];
        
        if (is_string($user['user_preferences'])) {
            return json_decode($user['user_preferences'], true) ?? [];
        }
        
        return $user['user_preferences'] ?? [];
    }

    /**
     * Actualizar preferencias del usuario
     */
    public function updateUserPreferences(int $userId, array $preferences): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;
        
        $currentPrefs = $this->getUserPreferences($userId);
        $newPrefs = array_merge($currentPrefs, $preferences);
        
        return $this->update($userId, [
            'user_preferences' => json_encode($newPrefs)
        ]);
    }

    /**
     * Verificar si el usuario necesita cambiar contrase?a
     */
    public function needsPasswordChange(int $userId): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;
        
        // Verificar flag directo
        if ($user['user_debe_cambiar_clave'] ?? false) {
            return true;
        }
        
        // Verificar por antig?edad (90 d?as)
        if (isset($user['user_metadata'])) {
            $metadata = is_string($user['user_metadata']) 
                ? json_decode($user['user_metadata'], true) 
                : $user['user_metadata'];
                
            $lastChange = $metadata['last_password_change'] ?? $user['created_at'] ?? null;
            
            if ($lastChange) {
                $daysSinceChange = (time() - strtotime($lastChange)) / (60 * 60 * 24);
                if ($daysSinceChange > 90) {
                    return true;
                }
            }
        }
        
        return false;
    }

    /**
     * Obtener configuraciones de seguridad del usuario
     */
    public function getSecuritySettings(int $userId): array
    {
        $user = $this->find($userId);
        if (!$user || !isset($user['user_security_settings'])) return [];
        
        if (is_string($user['user_security_settings'])) {
            return json_decode($user['user_security_settings'], true) ?? [];
        }
        
        return $user['user_security_settings'] ?? [];
    }

    /**
     * Obtener historial de login
     */
    public function getLoginHistory(int $userId, int $limit = 10): array
    {
        $user = $this->find($userId);
        if (!$user || !isset($user['user_login_history'])) return [];
        
        $history = is_string($user['user_login_history']) 
            ? json_decode($user['user_login_history'], true) 
            : $user['user_login_history'];
            
        return array_slice($history ?? [], 0, $limit);
    }

    /**
     * Obtener estad?sticas mejoradas
     */
    public function getEnhancedStats(): array
    {
        $stats = $this->getStats();
        
        // Agregar estad?sticas adicionales
        $allUsers = $this->findAll();
        
        $stats['need_password_change'] = 0;
        $stats['recent_logins_24h'] = 0;
        $stats['locked_accounts'] = 0;
        
        foreach ($allUsers as $user) {
            // Usuarios que necesitan cambiar contrase?a
            if ($this->needsPasswordChange($user['user_id'])) {
                $stats['need_password_change']++;
            }
            
            // Logins en las ?ltimas 24 horas
            if ($user['user_ultimo_acceso'] ?? false) {
                $hoursSinceLogin = (time() - strtotime($user['user_ultimo_acceso'])) / 3600;
                if ($hoursSinceLogin <= 24) {
                    $stats['recent_logins_24h']++;
                }
            }
            
            // Cuentas bloqueadas
            if (($user['user_intentos_login'] ?? 0) >= 5) {
                $stats['locked_accounts']++;
            }
        }
        
        return $stats;
    }
}