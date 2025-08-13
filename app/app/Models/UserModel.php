<?php
declare(strict_types=1);

namespace App\Models;

use CodeIgniter\Model;
use Throwable;

/**
 * UserModel (hardened)
 * - Mantiene la misma interfaz p?blica del modelo original (m?todos y nombres).
 * - Endurece validaciones, callbacks y operaciones sensibles sin cambiar la l?gica funcional.
 */
class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'user_id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false; // mantener comportamiento original
    protected $protectFields    = true;

    /** S?lo campos permitidos (del archivo original) */
    protected $allowedFields    = [
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
    ];

    /** Callbacks para normalizar e hashear */
    protected $beforeInsert = ['normalizeInput', 'hashPassword'];
    protected $beforeUpdate = ['normalizeInput', 'hashPassword'];
    protected $afterFind    = ['hidePassword'];

    // Reglas m?s seguras: user_clave no es obligatoria en UPDATE (if_exist)
    // y el email se valida como ?nico exceptuando el propio registro.
    protected $validationRules = [
        'user_nombre' => 'required|min_length[3]|max_length[100]',
        'user_email'  => 'required|valid_email|max_length[255]|is_unique[users.user_email,user_id,{user_id}]',
        'user_perfil' => 'required|integer|is_not_unique[perfiles.perfil_id]',
        'user_clave'  => 'if_exist|min_length[6]',
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
        'user_habil' => [
            'required' => 'El estado es obligatorio',
            'in_list'  => 'Estado inv?lido',
        ],
    ];

    /* =====================
     * M?todos de consulta
     * ===================== */

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
        // Normalizar email a min?sculas para evitar duplicados por casing
        $email = mb_strtolower(trim($email));

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

    /* =====================
     * Autenticaci?n / Login
     * ===================== */

    public function verifyPassword(string $password, string $hash): bool
    {
        $ok = password_verify($password, $hash);
        if ($ok && password_needs_rehash($hash, PASSWORD_DEFAULT)) {
            // Si hay rehash, el controlador que conozca al usuario deber?a actualizarlo.
            // Aqu? no sabemos el user_id, por eso s?lo devolvemos true.
        }
        return $ok;
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
        // Evitar condiciones de carrera usando una ?nica sentencia
        $this->builder()
            ->set('user_intentos_login', 'user_intentos_login + 1', false)
            ->where('user_id', $userId)
            ->update();

        $user = $this->find($userId);
        if ($user) {
            $intentos = (int) ($user['user_intentos_login'] ?? 0);

            // Bloquear usuario despu?s de 5 intentos (misma l?gica original)
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
            'user_intentos_login' => 0, // misma l?gica
        ]);
    }

    /**
     * Genera un token para reset de contrase?a.
     * Mantiene la l?gica original (guardar en user_token_reset),
     * pero genera aleatoriedad criptogr?fica.
     * Nota: Idealmente se deber?a guardar un hash del token y manejar expiraci?n.
     */
    public function generateResetToken(string $email)
    {
        $email = mb_strtolower(trim($email));
        $user = $this->where('user_email', $email)->first();
        if (!$user) {
            return false;
        }

        try {
            $token = bin2hex(random_bytes(32)); // 64 chars
        } catch (Throwable $e) {
            // Fallback m?nimo si random_bytes falla (muy improbable)
            $token = bin2hex(openssl_random_pseudo_bytes(32));
        }

        $this->update((int)$user['user_id'], ['user_token_reset' => $token]);
        return $token; // Se retorna el token en claro para que el controlador lo env?e por email
    }

    /* =====================
     * Integridad / Stats
     * ===================== */

    public function canDelete(int $id): bool
    {
        // Punto de extensi?n: validar FK antes de borrar (mantener true como en original)
        return true;
    }

    public function getStats(): array
    {
        // countAllResults(false) para reusar el builder y ahorrar queries
        return [
            'total'     => $this->countAll(),
            'activos'   => $this->where('user_habil', 1)->countAllResults(false),
            'inactivos' => $this->where('user_habil', 0)->countAllResults(false),
            'internos'  => $this->where('cia_id IS NULL')->countAllResults(false),
            'externos'  => $this->where('cia_id IS NOT NULL')->countAllResults(false),
        ];
    }

    /* =====================
     * Validaci?n adicional
     * ===================== */

    public function validateUserByProfileType(array $data): array
    {
        $perfilModel = new \App\Models\PerfilModel();
        $perfil = $perfilModel->find((int) $data['user_perfil']);

        if (!$perfil) {
            return ['user_perfil' => 'El perfil seleccionado no existe'];
        }

        $errors = [];

        // Si es perfil de compa??a, debe tener cia_id
        if (($perfil['perfil_tipo'] ?? null) === 'compania' && empty($data['cia_id'])) {
            $errors['cia_id'] = 'Los usuarios con perfil de compa??a deben tener una compa??a asignada';
        }

        // Si es perfil interno, no debe tener cia_id
        if (($perfil['perfil_tipo'] ?? null) === 'interno' && !empty($data['cia_id'])) {
            $errors['cia_id'] = 'Los usuarios con perfil interno no pueden tener compa??a asignada';
        }

        return $errors;
    }

    /* =====================
     * Callbacks
     * ===================== */

    protected function normalizeInput(array $data): array
    {
        if (!isset($data['data']) || !is_array($data['data'])) {
            return $data;
        }

        // Trim y normalizaci?n b?sica
        if (isset($data['data']['user_nombre'])) {
            $data['data']['user_nombre'] = trim((string) $data['data']['user_nombre']);
        }
        if (isset($data['data']['user_email'])) {
            $data['data']['user_email'] = mb_strtolower(trim((string) $data['data']['user_email']));
        }
        if (isset($data['data']['user_telefono'])) {
            $data['data']['user_telefono'] = trim((string) $data['data']['user_telefono']);
        }
        if (isset($data['data']['cia_id']) && $data['data']['cia_id'] !== null && $data['data']['cia_id'] !== '') {
            $data['data']['cia_id'] = (int) $data['data']['cia_id'];
        }
        if (isset($data['data']['user_perfil'])) {
            $data['data']['user_perfil'] = (int) $data['data']['user_perfil'];
        }
        if (isset($data['data']['user_habil'])) {
            $data['data']['user_habil'] = (int) $data['data']['user_habil'];
        }

        return $data;
    }

    protected function hashPassword(array $data): array
    {
        if (isset($data['data']['user_clave']) && $data['data']['user_clave'] !== '') {
            $pwd = (string) $data['data']['user_clave'];

            // Evitar doble hash
            $info = password_get_info($pwd);
            if (empty($info['algo'])) {
                $data['data']['user_clave'] = password_hash($pwd, PASSWORD_DEFAULT);
            }
        }
        return $data;
    }

    protected function hidePassword(array $data): array
    {
        // Ocultar el hash de salida (tanto en registros m?ltiples como ?nico)
        if (!isset($data['data'])) {
            return $data;
        }

        if (isset($data['data'][0]) && is_array($data['data'][0])) {
            foreach ($data['data'] as &$row) {
                if (is_array($row)) {
                    unset($row['user_clave']);
                }
            }
        } elseif (isset($data['data']['user_clave'])) {
            unset($data['data']['user_clave']);
        }

        return $data;
    }
}
