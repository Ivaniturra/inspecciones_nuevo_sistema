<?php
namespace App\Models;

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'user_id';
    protected $useAutoIncrement = true;

    protected $returnType       = 'array';
    protected $protectFields    = true;

    protected $allowedFields = [
    'user_nombre','user_email','user_telefono','user_perfil','cia_id','corredor_id',
    'user_clave','user_avatar','user_ultimo_acceso','user_intentos_login',
    'user_token_reset','user_habil','user_debe_cambiar_clave',
    'user_metadata','user_preferences','user_security_settings','user_login_history', 
    'user_remember_selector','user_remember_validator_hash','user_remember_expires',
    'user_token_reset_expires'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'user_created_at';  // CAMBIADO
    protected $updatedField  = 'user_updated_at';  // CAMBIADO

    /** Validación mínima (el controlador aplica la validación fuerte) */
    protected $validationRules = [
        'user_nombre' => 'required|min_length[3]|max_length[100]',
        'user_email'  => 'required|valid_email|max_length[255]|is_unique[users.user_email,user_id,{user_id}]',
        'user_perfil' => 'required|integer',
        'user_clave'  => 'permit_empty',     // el controlador exige regex fuerte donde corresponda
        'user_habil'  => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'user_email' => [
            'is_unique' => 'Este email ya está registrado.',
        ],
    ];

    /** Callbacks */
    protected $beforeInsert = ['normalizeInput', 'ensureDefaults', 'hashPassword', 'setDefaultJsonFields'];
    protected $beforeUpdate = ['normalizeInput', 'ensureDefaults', 'hashPasswordIfProvided', 'updateMetadata'];
    protected $afterFind    = ['parseJsonFields'];

    /* --------------------- Helpers de normalización y defaults --------------------- */
    public function updateUser($id, array $data)
    {
        try {
            // Log para debug
            log_message('debug', '=== UserModel::updateUser ===');
            log_message('debug', 'ID: ' . $id);
            log_message('debug', 'Data: ' . json_encode($data));
            
            // Verificar que el usuario existe
            $user = $this->find($id);
            if (!$user) {
                log_message('error', 'Usuario no encontrado con ID: ' . $id);
                return [
                    'status' => false,
                    'error' => 'Usuario no encontrado'
                ];
            }
            
            // Intentar actualizar - skipValidation si ya validaste en el controlador
            $result = $this->skipValidation(true)->update($id, $data);
            
            log_message('debug', 'Update result: ' . ($result ? 'true' : 'false'));
            
            if ($result === false) {
                $errors = $this->errors();
                log_message('error', 'Model errors: ' . json_encode($errors));
                return [
                    'status' => false,
                    'error' => $errors ?: 'Error desconocido al actualizar'
                ];
            }
            
            return ['status' => true];
            
        } catch (\Exception $e) {
            log_message('critical', 'Exception en updateUser: ' . $e->getMessage());
            return [
                'status' => false,
                'error' => $e->getMessage()
            ];
        }
    } 

    /** Normaliza strings (espacios y casing del email) */
    protected function normalizeInput(array $data): array
    {
        if (!isset($data['data'])) return $data;

        if (array_key_exists('user_nombre', $data['data'])) {
            $data['data']['user_nombre'] = trim((string) $data['data']['user_nombre']);
        }
        if (array_key_exists('user_email', $data['data'])) {
            $data['data']['user_email'] = strtolower(trim((string) $data['data']['user_email']));
        }
        if (array_key_exists('user_telefono', $data['data'])) {
            $data['data']['user_telefono'] = trim((string) $data['data']['user_telefono']);
        }

        // Normaliza CIA null si viene vacío
        if (array_key_exists('cia_id', $data['data']) && $data['data']['cia_id'] === '') {
            $data['data']['cia_id'] = null;
        }

        return $data;
    }

    /** Defaults seguros para campos NOT NULL (evita errores de BD si faltan) */
    protected function ensureDefaults(array $data): array
    {
        if (!isset($data['data'])) return $data;

        $d =& $data['data'];
        if (!isset($d['user_habil']))              $d['user_habil'] = 1;
        if (!isset($d['user_debe_cambiar_clave'])) $d['user_debe_cambiar_clave'] = 0;
        if (!isset($d['user_intentos_login']))     $d['user_intentos_login'] = 0;

        return $data;
    }

    /* --------------------- Password --------------------- */

    protected function hashPassword(array $data): array
    {
        if (!isset($data['data'])) return $data;
        if (!empty($data['data']['user_clave'])) {
            $data['data']['user_clave'] = password_hash((string)$data['data']['user_clave'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    protected function hashPasswordIfProvided(array $data): array
    {
        if (!isset($data['data'])) return $data;

        if (isset($data['data']['user_clave']) && $data['data']['user_clave'] !== '' && $data['data']['user_clave'] !== null) {
            $info = password_get_info((string)$data['data']['user_clave']);
            if ($info['algo'] === null) {
                $data['data']['user_clave'] = password_hash((string)$data['data']['user_clave'], PASSWORD_DEFAULT);
            }
        } else {
            unset($data['data']['user_clave']);
        }

        return $data;
    }

    /* --------------------- JSON por defecto / parse / metadata --------------------- */

    protected function setDefaultJsonFields(array $data): array
    {
        if (!isset($data['data'])) return $data;
        $ip  = $_SERVER['REMOTE_ADDR']     ?? 'unknown';
        $ua  = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
        $now = date('Y-m-d H:i:s');

        // Solo setear si el campo no viene definido
        $data['data']['user_metadata']          = $data['data']['user_metadata']          ?? json_encode([
            'created_ip'            => $ip,
            'created_user_agent'    => $ua,
            'created_at'            => $now,
            'last_password_change'  => $now,
            'password_history'      => [],
            'failed_login_attempts' => [],
        ], JSON_UNESCAPED_UNICODE);

        $data['data']['user_preferences']       = $data['data']['user_preferences']       ?? json_encode([
            'theme'         => 'light',
            'language'      => 'es',
            'notifications' => ['email' => true, 'sms' => false, 'push' => true],
            'timezone'      => 'America/Santiago',
            'date_format'   => 'd/m/Y',
            'items_per_page'=> 25,
        ], JSON_UNESCAPED_UNICODE);

        $data['data']['user_security_settings'] = $data['data']['user_security_settings'] ?? json_encode([
            'two_factor_enabled' => false,
            'two_factor_method'  => null,
            'session_timeout'    => 3600,
            'ip_whitelist'       => [],
            'trusted_devices'    => [],
            'security_questions' => [],
        ], JSON_UNESCAPED_UNICODE);

        $data['data']['user_login_history']     = $data['data']['user_login_history']     ?? json_encode([], JSON_UNESCAPED_UNICODE);

        return $data;
    }

    protected function updateMetadata(array $data): array
    {
        if (!isset($data['data'])) return $data;

        // Tomar id de update de forma robusta
        $id = $data['id'] ?? null;
        if (is_array($id)) $id = $id[0] ?? null;
        if (!$id) return $data;

        // Si cambia la contraseña, actualiza metadata
        if (array_key_exists('user_clave', $data['data'])) {
            $user = $this->find($id);
            if ($user) {
                $metadata = is_string($user['user_metadata'] ?? null)
                    ? json_decode($user['user_metadata'], true)
                    : ($user['user_metadata'] ?? []);

                $metadata['password_history']   = $metadata['password_history'] ?? [];
                $metadata['password_history'][] = [
                    'changed_at'   => date('Y-m-d H:i:s'),
                    'changed_by_ip'=> $_SERVER['REMOTE_ADDR'] ?? 'unknown',
                ];
                // Mantener últimos 5
                $metadata['password_history']      = array_slice($metadata['password_history'], -5);
                $metadata['last_password_change']  = date('Y-m-d H:i:s');

                $data['data']['user_metadata'] = json_encode($metadata, JSON_UNESCAPED_UNICODE);
                // Si se cambia la clave manualmente desde panel, ya no debe cambiarla al iniciar sesión
                $data['data']['user_debe_cambiar_clave'] = 0;
            }
        }

        return $data;
    }

    protected function parseJsonFields(array $data): array
    {
        if (!isset($data['data'])) return $data;

        $jsonFields = ['user_metadata','user_preferences','user_security_settings','user_login_history'];

        // múltiples
        if (isset($data['data'][0]) && is_array($data['data'][0])) {
            foreach ($data['data'] as &$row) {
                foreach ($jsonFields as $f) {
                    if (isset($row[$f]) && is_string($row[$f])) {
                        $row[$f] = json_decode($row[$f], true) ?? [];
                    }
                }
            }
        } else { // uno
            foreach ($jsonFields as $f) {
                if (isset($data['data'][$f]) && is_string($data['data'][$f])) {
                    $data['data'][$f] = json_decode($data['data'][$f], true) ?? [];
                }
            }
        }

        return $data;
    }

    /* --------------------- Consultas de negocio --------------------- */

    public function getUsersWithDetails(): array
    {
        return $this->select(
                'users.*,' .
                'cias.cia_nombre,' .
                'perfiles.perfil_nombre,' .
                'perfiles.perfil_tipo,' .
                'perfiles.perfil_nivel'
            )
            ->join('cias', 'cias.cia_id = users.cia_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
            ->orderBy('users.user_id', 'DESC')      // ✅ más reciente arriba
            ->findAll();
    }

    public function getUsersByCompany(int $ciaId): array
    {
        return $this->select(
                'users.*,' .
                'cias.cia_nombre,' .
                'perfiles.perfil_nombre,' .
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
                'users.*,' .
                'perfiles.perfil_nombre,' .
                'perfiles.perfil_tipo'
            )
            ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
            ->where('users.cia_id', null)  // IS NULL
            ->where('users.user_habil', 1)
            ->orderBy('users.user_nombre', 'ASC')
            ->findAll();
    }

    public function getUsersByProfileType(string $tipo): array
    {
        return $this->select(
                'users.*,' .
                'cias.cia_nombre,' .
                'perfiles.perfil_nombre,' .
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
        $query = $this->db->table('users u')
            ->select('
                u.*,
                p.perfil_nombre,
                p.perfil_tipo,
                p.perfil_nivel,
                c.cia_id,
                c.cia_nombre,
                c.cia_brand_nav_bg,
                c.cia_brand_nav_text,
                c.cia_brand_side_start,
                c.cia_brand_side_end,
                cor.corredor_id,
                cor.corredor_nombre,
                cor.corredor_brand_nav_bg,
                cor.corredor_brand_nav_text,
                cor.corredor_brand_side_start,
                cor.corredor_brand_side_end,
                cor.corredor_logo_path
            ')
            ->join('perfiles p', 'p.perfil_id = u.user_perfil', 'left')
            ->join('cias c', 'c.cia_id = u.cia_id', 'left')
            ->join('corredores cor', 'cor.corredor_id = u.corredor_id', 'left') // ← VERIFICAR ESTA LÍNEA
            ->where('u.user_email', $email)
            ->where('u.user_habil', 1);
        
        $result = $query->get()->getRowArray();
        
        // ✅ DEBUG TEMPORAL
        if ($result) {
            log_message('debug', '=== USER DATA FROM DB ===');
            log_message('debug', 'user_perfil: ' . ($result['user_perfil'] ?? 'NULL'));
            log_message('debug', 'perfil_tipo: ' . ($result['perfil_tipo'] ?? 'NULL'));
            log_message('debug', 'corredor_id: ' . ($result['corredor_id'] ?? 'NULL'));
            log_message('debug', 'user_corredor_id: ' . ($result['user_corredor_id'] ?? 'NULL'));
        }
        
        return $result ?: null;
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
        if (!$user) return false;

        $new = (int) ($user['user_habil'] == 1 ? 0 : 1);
        return (bool) $this->update($id, [
            'user_habil' => $new,
            'user_intentos_login' => 0,
        ]);
    }

    public function generateResetToken(string $email)
    {
        $email = strtolower(trim($email));
        $user  = $this->where('user_email', $email)->first();
        if (!$user) return false;

        $token = bin2hex(random_bytes(32));
        $this->update((int)$user['user_id'], ['user_token_reset' => $token]);
        return $token;
    }

    public function canDelete(int $id): bool
    {
        return true;
    }

    /** Stats sin acumular condiciones */
    public function getStats(): array
    {
        return [
            'total'     => $this->countAll(),
            'activos'   => $this->where('user_habil', 1)->countAllResults(true),
            'inactivos' => $this->where('user_habil', 0)->countAllResults(true),
            'internos'  => $this->where('cia_id', null)->countAllResults(true),                         // IS NULL
            'externos'  => $this->where('cia_id IS NOT NULL', null, false)->countAllResults(true),      // IS NOT NULL
        ];
    }

    public function getEnhancedStats(): array
    {
        $stats = $this->getStats();
        $allUsers = $this->findAll();

        $stats['need_password_change'] = 0;
        $stats['recent_logins_24h']    = 0;
        $stats['locked_accounts']      = 0;
        $stats['corredores']           = 0;
        $stats['inspectores']          = 0;

        foreach ($allUsers as $u) {
            if ($this->needsPasswordChange($u['user_id'])) $stats['need_password_change']++;

            if (!empty($u['user_ultimo_acceso'])) {
                $hours = (time() - strtotime($u['user_ultimo_acceso'])) / 3600;
                if ($hours <= 24) $stats['recent_logins_24h']++;
            }

            if ((int)($u['user_intentos_login'] ?? 0) >= 5) $stats['locked_accounts']++;
            
            // Contar por tipo usando corredor_id
            if (!empty($u['corredor_id'])) $stats['corredores']++;
        }
        
        // Contar inspectores por perfil
        $stats['inspectores'] = $this->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
                                    ->where('perfiles.perfil_tipo', 'inspector')
                                    ->countAllResults(true);
        
        return $stats;
    }

    public function validateUserByProfileType(array $data): array
    {
        $perfilModel = new \App\Models\PerfilModel();
        $perfil = $perfilModel->find((int) ($data['user_perfil'] ?? 0));

        if (!$perfil) {
            return ['user_perfil' => 'El perfil seleccionado no existe'];
        }

        $errors = [];
        $tipo = $perfil['perfil_tipo'] ?? null;

        if ($tipo === 'compania' && empty($data['cia_id'])) {
            $errors['cia_id'] = 'Los usuarios con perfil de compañía deben tener una compañía asignada';
        }
        if ($tipo === 'interno' && !empty($data['cia_id'])) {
            $errors['cia_id'] = 'Los usuarios con perfil interno no pueden tener compañía asignada';
        }

        return $errors;
    }

    /* Preferencias / Seguridad / Historial */

    public function getUserPreferences(int $userId): array
    {
        $user = $this->find($userId);
        if (!$user) return [];

        $prefs = $user['user_preferences'] ?? [];
        return is_string($prefs) ? (json_decode($prefs, true) ?? []) : $prefs;
    }

    public function updateUserPreferences(int $userId, array $preferences): bool
    {
        $current = $this->getUserPreferences($userId);
        $merged  = array_merge($current, $preferences);
        return $this->update($userId, ['user_preferences' => json_encode($merged, JSON_UNESCAPED_UNICODE)]);
    }

    public function needsPasswordChange(int $userId): bool
    {
        $user = $this->find($userId);
        if (!$user) return false;

        if (!empty($user['user_debe_cambiar_clave'])) return true;

        $meta = $user['user_metadata'] ?? [];
        $meta = is_string($meta) ? (json_decode($meta, true) ?? []) : $meta;

        // CAMBIADO: usar user_created_at en lugar de created_at
        $last = $meta['last_password_change'] ?? $user['user_created_at'] ?? null;
        if ($last) {
            $days = (time() - strtotime($last)) / 86400;
            return $days > 90;
        }
        return false;
    }

    public function getSecuritySettings(int $userId): array
    {
        $user = $this->find($userId);
        if (!$user) return [];
        $sec = $user['user_security_settings'] ?? [];
        return is_string($sec) ? (json_decode($sec, true) ?? []) : $sec;
    }

    public function getLoginHistory(int $userId, int $limit = 10): array
    {
        $user = $this->find($userId);
        if (!$user) return [];
        $hist = $user['user_login_history'] ?? [];
        $hist = is_string($hist) ? (json_decode($hist, true) ?? []) : $hist;
        return array_slice($hist, 0, $limit);
    }

    public function logLoginAttempt(int $userId, bool $success, string $ip = null): void
    {
        $user = $this->find($userId);
        if (!$user) return;

        $history = $user['user_login_history'] ?? [];
        $history = is_string($history) ? (json_decode($history, true) ?? []) : $history;

        $attempt = [
            'timestamp'   => date('Y-m-d H:i:s'),
            'success'     => $success,
            'ip'          => $ip ?? ($_SERVER['REMOTE_ADDR'] ?? 'unknown'),
            'user_agent'  => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        ];

        array_unshift($history, $attempt);
        $history = array_slice($history, 0, 50);

        if (!$success) {
            $metadata = $user['user_metadata'] ?? [];
            $metadata = is_string($metadata) ? (json_decode($metadata, true) ?? []) : $metadata;

            $metadata['failed_login_attempts']   = $metadata['failed_login_attempts'] ?? [];
            $metadata['failed_login_attempts'][] = $attempt;
            $metadata['failed_login_attempts']   = array_slice($metadata['failed_login_attempts'], -10);

            $this->update($userId, [
                'user_login_history' => json_encode($history, JSON_UNESCAPED_UNICODE),
                'user_metadata'      => json_encode($metadata, JSON_UNESCAPED_UNICODE),
                'user_intentos_login'=> (int)($user['user_intentos_login'] ?? 0) + 1,
            ]);
        } else {
            $this->update($userId, [
                'user_login_history' => json_encode($history, JSON_UNESCAPED_UNICODE),
                'user_ultimo_acceso' => date('Y-m-d H:i:s'),
                'user_intentos_login'=> 0,
            ]);
        }
    }
    public function find($id = null)
    {
        // Si no se pasa ID, comportamiento normal
        if ($id === null) {
            return parent::find();
        }
        
        // Si es array de IDs
        if (is_array($id)) {
            return $this->select('users.*')->whereIn('user_id', $id)->findAll();
        }
        
        // Si es un solo ID, asegurar que traiga todos los campos
        return $this->select('users.*')->where('user_id', $id)->first();
    }
}