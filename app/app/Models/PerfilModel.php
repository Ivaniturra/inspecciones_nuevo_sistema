<?php

namespace App\Models;

use CodeIgniter\Model;

class PerfilModel extends Model
{
    protected $table      = 'perfiles';
    protected $primaryKey = 'perfil_id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'perfil_nombre',
        'perfil_tipo',
        'perfil_descripcion',
        'perfil_permisos',
        'perfil_nivel',
        'perfil_habil'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validation
    protected $validationRules = [
        'perfil_nombre' => 'required|min_length[3]|max_length[100]',
        'perfil_tipo'   => 'required|in_list[compania,interno]',
        'perfil_nivel'  => 'required|integer|greater_than[0]|less_than[5]',
        'perfil_habil'  => 'required|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'perfil_nombre' => [
            'required'    => 'El nombre del perfil es obligatorio',
            'min_length'  => 'El nombre debe tener al menos 3 caracteres',
            'max_length'  => 'El nombre no puede exceder 100 caracteres'
        ],
        'perfil_tipo' => [
            'required' => 'El tipo de perfil es obligatorio',
            'in_list'  => 'El tipo debe ser "compania" o "interno"'
        ],
        'perfil_nivel' => [
            'required' => 'El nivel es obligatorio',
            'integer'  => 'El nivel debe ser un número entero',
            'greater_than' => 'El nivel debe ser mayor a 0',
            'less_than' => 'El nivel debe ser menor a 5'
        ]
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['encodePermisos'];
    protected $afterInsert    = [];
    protected $beforeUpdate   = ['encodePermisos'];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = ['decodePermisos'];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    // Métodos personalizados
    
    /**
     * Obtener perfiles por tipo
     */
    public function getPerfilesByTipo($tipo = null)
    {
        if ($tipo) {
            return $this->where('perfil_tipo', $tipo)
                       ->where('perfil_habil', 1)
                       ->orderBy('perfil_nombre', 'ASC')
                       ->findAll();
        }
        
        return $this->where('perfil_habil', 1)
                   ->orderBy('perfil_tipo', 'ASC')
                   ->orderBy('perfil_nombre', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener perfiles con conteo de usuarios
     */
    public function getPerfilesWithUserCount()
    {
        return $this->select('perfiles.*, COUNT(users.user_id) as total_usuarios')
                   ->join('users', 'users.user_perfil = perfiles.perfil_id', 'left')
                   ->groupBy('perfiles.perfil_id')
                   ->orderBy('perfil_tipo', 'ASC')
                   ->orderBy('perfil_nombre', 'ASC')
                   ->findAll();
    }

    /**
     * Obtener perfiles de compañía activos
     */
    public function getPerfilesCompania()
    {
        return $this->getPerfilesByTipo('compania');
    }

    /**
     * Obtener perfiles internos activos
     */
    public function getPerfilesInternos()
    {
        return $this->getPerfilesByTipo('interno');
    }

    /**
     * Cambiar estado del perfil
     */
    public function toggleStatus($id)
    {
        $perfil = $this->find($id);
        if ($perfil) {
            $newStatus = $perfil['perfil_habil'] == 1 ? 0 : 1;
            return $this->update($id, ['perfil_habil' => $newStatus]);
        }
        return false;
    }

    /**
     * Verificar si se puede eliminar el perfil
     */
    public function canDelete($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $count = $builder->where('user_perfil', $id)->countAllResults();
        
        return $count === 0;
    }

    /**
     * Obtener permisos disponibles por tipo
     */
    public function getPermisosDisponibles($tipo = 'compania')
    {
        if ($tipo === 'interno') {
            return [
                'crear_inspecciones' => 'Crear inspecciones',
                'editar_inspecciones' => 'Editar inspecciones',
                'ver_inspecciones' => 'Ver inspecciones',
                'asignar_inspecciones' => 'Asignar inspecciones',
                'aprobar_inspecciones' => 'Aprobar inspecciones',
                'rechazar_inspecciones' => 'Rechazar inspecciones',
                'subir_fotos' => 'Subir fotografías',
                'ver_reportes' => 'Ver reportes',
                'generar_reportes' => 'Generar reportes',
                'gestionar_compañias' => 'Gestionar compañías',
                'gestionar_usuarios' => 'Gestionar usuarios',
                'gestionar_perfiles' => 'Gestionar perfiles',
                'configurar_sistema' => 'Configurar sistema',
                'auditar_sistema' => 'Auditar sistema',
                'acceso_total' => 'Acceso total'
            ];
        } else {
            return [
                'ver_reportes' => 'Ver reportes',
                'ver_inspecciones' => 'Ver inspecciones de su compañía',
                'generar_reportes' => 'Generar reportes',
                'crear_usuarios' => 'Crear usuarios de la compañía',
                'gestionar_usuarios' => 'Gestionar usuarios de la compañía',
                'solicitar_inspecciones' => 'Solicitar inspecciones',
                'ver_estadisticas' => 'Ver estadísticas'
            ];
        }
    }

    /**
     * Callback: Codificar permisos antes de guardar
     */
    protected function encodePermisos(array $data)
    {
        if (isset($data['data']['perfil_permisos']) && is_array($data['data']['perfil_permisos'])) {
            $data['data']['perfil_permisos'] = json_encode($data['data']['perfil_permisos']);
        }
        return $data;
    }

    /**
     * Callback: Decodificar permisos después de encontrar
     */
    protected function decodePermisos(array $data)
    {
        if (isset($data['data'])) {
            // Múltiples registros
            if (is_array($data['data']) && isset($data['data'][0])) {
                foreach ($data['data'] as &$row) {
                    if (isset($row['perfil_permisos']) && is_string($row['perfil_permisos'])) {
                        $row['perfil_permisos'] = json_decode($row['perfil_permisos'], true) ?: [];
                    }
                }
            }
            // Registro único
            elseif (isset($data['data']['perfil_permisos']) && is_string($data['data']['perfil_permisos'])) {
                $data['data']['perfil_permisos'] = json_decode($data['data']['perfil_permisos'], true) ?: [];
            }
        }
        return $data;
    }

    /**
     * Verificar si un perfil tiene un permiso específico
     */
    public function hasPermission($perfilId, $permission)
    {
        $perfil = $this->find($perfilId);
        if (!$perfil || !$perfil['perfil_habil']) {
            return false;
        }

        $permisos = $perfil['perfil_permisos'] ?? [];
        
        // Si tiene acceso total
        if (isset($permisos['acceso_total']) && $permisos['acceso_total']) {
            return true;
        }

        return isset($permisos[$permission]) && $permisos[$permission];
    }
}