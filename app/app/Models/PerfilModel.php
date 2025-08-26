<?php

namespace App\Models;

use CodeIgniter\Model;

class PerfilModel extends Model
{
    protected $table            = 'perfiles';
    protected $primaryKey       = 'perfil_id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    protected $allowedFields = [
        'perfil_nombre',
        'perfil_tipo',
        'perfil_descripcion',
        'perfil_permisos',   // JSON
        'perfil_nivel',
        'perfil_habil',
    ];

    // Fechas - ACTUALIZADO con nomenclatura perfil_XXXX
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'perfil_created_at';  // ← CAMBIO AQUÍ
    protected $updatedField  = 'perfil_updated_at';  // ← CAMBIO AQUÍ

    // Validación
    protected $validationRules = [
        'perfil_nombre' => 'required|min_length[3]|max_length[100]',
        'perfil_tipo'   => 'required|in_list[compania,interno]',
        'perfil_nivel'  => 'required|integer|greater_than[0]|less_than[5]',
        'perfil_habil'  => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'perfil_nombre' => [
            'required'   => 'El nombre del perfil es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder 100 caracteres',
        ],
        'perfil_tipo' => [
            'required' => 'El tipo de perfil es obligatorio',
            'in_list'  => 'El tipo debe ser "compania" o "interno"',
        ],
        'perfil_nivel' => [
            'required'     => 'El nivel es obligatorio',
            'integer'      => 'El nivel debe ser un número entero',
            'greater_than' => 'El nivel debe ser mayor a 0',
            'less_than'    => 'El nivel debe ser menor a 5',
        ],
    ];

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['sanitizeInputs', 'encodePermisos'];
    protected $beforeUpdate   = ['sanitizeInputs', 'encodePermisos'];
    protected $afterFind      = ['decodePermisos'];

    /* ===================== Helpers de sanitización ===================== */

    protected function sanitizeInputs(array $data): array
    {
        if (! isset($data['data'])) return $data;

        $d =& $data['data'];

        if (array_key_exists('perfil_nombre', $d)) {
            $d['perfil_nombre'] = trim(strip_tags((string) $d['perfil_nombre']));
        }
        if (array_key_exists('perfil_tipo', $d)) {
            $d['perfil_tipo'] = strtolower(trim((string) $d['perfil_tipo']));
            if (! in_array($d['perfil_tipo'], ['compania', 'interno'], true)) {
                // defensa en profundidad
                $d['perfil_tipo'] = 'interno';
            }
        }
        if (array_key_exists('perfil_descripcion', $d)) {
            // permite texto pero quita tags potencialmente peligrosos
            $d['perfil_descripcion'] = trim(strip_tags((string) $d['perfil_descripcion'], ''));
        }
        if (array_key_exists('perfil_nivel', $d)) {
            $d['perfil_nivel'] = (int) $d['perfil_nivel'];
        }
        if (array_key_exists('perfil_habil', $d)) {
            $d['perfil_habil'] = (int) $d['perfil_habil'] === 1 ? 1 : 0;
        }

        return $data;
    }

    /* ===================== JSON permisos ===================== */

    protected function encodePermisos(array $data): array
    {
        if (! isset($data['data'])) return $data;

        if (isset($data['data']['perfil_permisos'])) {
            // Acepta tanto array asociativo como string
            if (is_array($data['data']['perfil_permisos'])) {
                $data['data']['perfil_permisos'] = json_encode(
                    $data['data']['perfil_permisos'],
                    JSON_UNESCAPED_UNICODE
                );
            } elseif (! is_string($data['data']['perfil_permisos'])) {
                $data['data']['perfil_permisos'] = json_encode(new \stdClass());
            }
        }

        return $data;
    }

    protected function decodePermisos(array $data): array
    {
        if (! isset($data['data'])) return $data;

        $decode = static function (&$row) {
            if (isset($row['perfil_permisos']) && is_string($row['perfil_permisos'])) {
                $decoded = json_decode($row['perfil_permisos'], true);
                $row['perfil_permisos'] = is_array($decoded) ? $decoded : [];
            }
        };

        // múltiples
        if (isset($data['data'][0]) && is_array($data['data'][0])) {
            foreach ($data['data'] as &$row) $decode($row);
        } else { // uno
            $decode($data['data']);
        }

        return $data;
    }

    /* ===================== Consultas ===================== */

    public function onlyActive()
    {
        return $this->where('perfil_habil', 1);
    }

    public function byTipo(?string $tipo)
    {
        if ($tipo && in_array($tipo, ['compania', 'interno'], true)) {
            $this->where('perfil_tipo', $tipo);
        }
        return $this;
    }

    public function getPerfilesByTipo($tipo = null)
    {
        return $this->onlyActive()
                    ->byTipo($tipo)
                    ->orderBy('perfil_tipo', 'ASC')
                    ->orderBy('perfil_nombre', 'ASC')
                    ->findAll();
    }

    public function getPerfilesWithUserCount()
    {
        return $this->select('perfiles.*, COUNT(users.user_id) AS total_usuarios')
                    ->join('users', 'users.user_perfil = perfiles.perfil_id', 'left')
                    ->groupBy('perfiles.perfil_id')
                    ->orderBy('perfil_tipo', 'ASC')
                    ->orderBy('perfil_nombre', 'ASC')
                    ->findAll();
    }

    public function getPerfilesCompania()
    {
        return $this->getPerfilesByTipo('compania');
    }

    public function getPerfilesInternos()
    {
        return $this->getPerfilesByTipo('interno');
    }

    public function toggleStatus($id)
    {
        $perfil = $this->find($id);
        if (! $perfil) return false;

        $new = (int) ($perfil['perfil_habil'] == 1 ? 0 : 1);
        return $this->update($id, ['perfil_habil' => $new]);
    }

    public function canDelete($id): bool
    {
        $db = \Config\Database::connect();
        $count = $db->table('users')->where('user_perfil', $id)->countAllResults();
        return (int) $count === 0;
    }

    /**
     * Permisos disponibles por tipo (keys en ASCII, labels con tildes OK)
     */
    public function getPermisosDisponibles($tipo = 'compania'): array
    {
        if ($tipo === 'interno') {
            return [
                'crear_inspecciones'    => 'Crear inspecciones',
                'editar_inspecciones'   => 'Editar inspecciones',
                'ver_inspecciones'      => 'Ver inspecciones',
                'asignar_inspecciones'  => 'Asignar inspecciones',
                'aprobar_inspecciones'  => 'Aprobar inspecciones',
                'rechazar_inspecciones' => 'Rechazar inspecciones',
                'subir_fotos'           => 'Subir fotografías',
                'ver_reportes'          => 'Ver reportes',
                'generar_reportes'      => 'Generar reportes',
                'gestionar_companias'   => 'Gestionar compañías',
                'gestionar_usuarios'    => 'Gestionar usuarios',
                'gestionar_perfiles'    => 'Gestionar perfiles',
                'configurar_sistema'    => 'Configurar sistema',
                'auditar_sistema'       => 'Auditar sistema',
                'delete_users'          => 'Eliminar usuarios',
                'acceso_total'          => 'Acceso total', 
                'reset_passwords'       => 'Resetear contraseñas',  
            ];
        }

        return [
            'ver_reportes'          => 'Ver reportes',
            'ver_inspecciones'      => 'Ver inspecciones de su compañía',
            'generar_reportes'      => 'Generar reportes',
            'crear_usuarios'        => 'Crear usuarios de la compañía',
            'gestionar_usuarios'    => 'Gestionar usuarios de la compañía',
            'solicitar_inspecciones'=> 'Solicitar inspecciones',
            'ver_estadisticas'      => 'Ver estadísticas',
            'delete_users'          => 'Eliminar usuarios de la compañía',   
        ];
    }

    public function hasPermission($perfilId, $permission): bool
    {
        $perfil = $this->find($perfilId);
        if (! $perfil || (int)($perfil['perfil_habil'] ?? 0) !== 1) {
            return false;
        }

        $permisos = $perfil['perfil_permisos'] ?? [];
        if (isset($permisos['acceso_total']) && $permisos['acceso_total']) {
            return true;
        }

        return isset($permisos[$permission]) && $permisos[$permission] === true;
    }
}