<?php

namespace App\Models;

use CodeIgniter\Model;

class CiaModel extends Model
{
    protected $table            = 'cias';
    protected $primaryKey       = 'cia_id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    // Todos los campos siguen la convención cia_XXXX
    protected $allowedFields = [
        'cia_nombre',
        'cia_display_name',
        'cia_slug',
        'cia_direccion',
        'cia_logo',
        'cia_habil',
        'cia_brand_nav_bg',
        'cia_brand_nav_text',
        'cia_brand_side_start',
        'cia_brand_side_end',
        'cia_logo_path',
    ];

    // Fechas - ACTUALIZADO con nomenclatura cia_XXXX
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'cia_created_at';
    protected $updatedField  = 'cia_updated_at';

    // Validación
    protected $validationRules = [
        'cia_nombre'            => 'required|min_length[3]|max_length[255]',
        'cia_display_name'      => 'permit_empty|max_length[150]',
        'cia_slug'              => 'permit_empty|regex_match[/^[a-z0-9-]+$/]',
        'cia_direccion'         => 'permit_empty|max_length[500]',
        'cia_logo'              => 'permit_empty|max_length[255]',
        'cia_habil'             => 'required|in_list[0,1]',
        'cia_brand_nav_bg'      => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'cia_brand_nav_text'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'cia_brand_side_start'  => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'cia_brand_side_end'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'cia_logo_path'         => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'cia_nombre' => [
            'required'   => 'El nombre de la compañía es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder 255 caracteres',
        ],
        'cia_habil' => [
            'required' => 'El estado es obligatorio',
            'in_list'  => 'El estado debe ser Activo o Inactivo',
        ],
        'cia_slug' => [
            'regex_match' => 'El slug solo puede contener minúsculas, números y guiones (-).',
        ],
        'cia_brand_nav_bg'      => ['regex_match' => 'Color de navegación inválido. Usa formato HEX (#RRGGBB).'],
        'cia_brand_nav_text'    => ['regex_match' => 'Color de texto de navegación inválido. Usa formato HEX (#RRGGBB).'],
        'cia_brand_side_start'  => ['regex_match' => 'Color inicial del sidebar inválido. Usa formato HEX (#RRGGBB).'],
        'cia_brand_side_end'    => ['regex_match' => 'Color final del sidebar inválido. Usa formato HEX (#RRGGBB).'],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['normalize', 'ensureDefaults', 'ensureSlug'];
    protected $beforeUpdate   = ['normalize', 'ensureDefaults', 'ensureSlug'];

    /* ===================== Callbacks ===================== */

    protected function normalize(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        foreach (['cia_nombre','cia_display_name','cia_direccion','cia_slug'] as $k) {
            if (array_key_exists($k, $d)) {
                $d[$k] = trim((string) $d[$k]);
            }
        }

        if (array_key_exists('cia_habil', $d)) {
            $d['cia_habil'] = (int) $d['cia_habil'] === 1 ? 1 : 0;
        }

        // Normalizar HEX a #RRGGBB si vienen en minúsculas/espacios
        foreach (['cia_brand_nav_bg','cia_brand_nav_text','cia_brand_side_start','cia_brand_side_end'] as $k) {
            if (array_key_exists($k, $d)) {
                $v = strtoupper(trim((string) $d[$k]));
                if ($v !== '' && preg_match('/^#([A-F0-9]{6})$/', $v)) {
                    $d[$k] = $v;
                }
            }
        }
        return $data;
    }

    protected function ensureDefaults(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        if (! isset($d['cia_habil'])) $d['cia_habil'] = 1;

        // Fallbacks de color (coinciden con los del controller)
        $d['cia_brand_nav_bg']      = $d['cia_brand_nav_bg']      ?? '#0D6EFD';
        $d['cia_brand_nav_text']    = $d['cia_brand_nav_text']    ?? '#FFFFFF';
        $d['cia_brand_side_start']  = $d['cia_brand_side_start']  ?? '#667EEA';
        $d['cia_brand_side_end']    = $d['cia_brand_side_end']    ?? '#764BA2';

        return $data;
    }

    protected function ensureSlug(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        // Generar slug si no viene
        if (empty($d['cia_slug'])) {
            helper(['text', 'url']);
            $base = $d['cia_nombre'] ?? '';
            $slug = url_title($base, '-', true); // minúsculas + guiones
            $d['cia_slug'] = $slug !== '' ? $slug : 'compania-' . uniqid();
        }
        return $data;
    }

    /* ===================== Consultas personalizadas ===================== */

    /**
     * Obtiene compañías activas
     */
    public function getActiveCias(): array
    {
        return $this->where('cia_habil', 1)->orderBy('cia_nombre','ASC')->findAll();
    }

    /**
     * Obtiene compañías con conteo de usuarios asociados
     */
    public function getCiasWithUserCount(): array
    {
        return $this->select('cias.*, COUNT(users.user_id) AS total_usuarios')
                    ->join('users', 'users.cia_id = cias.cia_id', 'left')
                    ->groupBy('cias.cia_id')
                    ->orderBy('cia_nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Cambia el estado de una compañía (activa/inactiva)
     * Actualizado para trabajar en conjunto con la desactivación de usuarios
     */
    public function toggleStatus($id): bool
    {
        $cia = $this->find($id);
        if (! $cia) return false;
        
        $newStatus = (int) ($cia['cia_habil'] == 1 ? 0 : 1);
        return (bool) $this->update($id, ['cia_habil' => $newStatus]);
    }

    /**
     * Obtiene estadísticas básicas de una compañía
     */
    public function getCompanyStats($id): array
    {
        $cia = $this->find($id);
        if (!$cia) {
            return [
                'exists' => false,
                'active' => false,
                'users_count' => 0
            ];
        }

        // Contar usuarios asociados si existe la tabla users
        $db = \Config\Database::connect();
        $usersCount = 0;
        
        if ($db->tableExists('users')) {
            $usersCount = $db->table('users')
                            ->where('cia_id', $id)
                            ->countAllResults();
        }

        return [
            'exists' => true,
            'active' => (bool) $cia['cia_habil'],
            'users_count' => $usersCount,
            'name' => $cia['cia_nombre'],
            'display_name' => $cia['cia_display_name'],
            'created_at' => $cia['cia_created_at']
        ];
    }

    /**
     * Verifica si una compañía tiene usuarios activos asociados
     */
    public function hasActiveUsers($id): bool
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('users')) {
            return false;
        }

        $count = $db->table('users')
                   ->where('cia_id', $id)
                   ->where('user_habil', 1)
                   ->countAllResults();

        return $count > 0;
    }

    /**
     * Obtiene el conteo de usuarios por estado para una compañía
     */
    public function getUsersCountByStatus($id): array
    {
        $db = \Config\Database::connect();
        
        if (!$db->tableExists('users')) {
            return ['total' => 0, 'active' => 0, 'inactive' => 0];
        }

        $total = $db->table('users')->where('cia_id', $id)->countAllResults();
        $active = $db->table('users')->where('cia_id', $id)->where('user_habil', 1)->countAllResults();
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active
        ];
    }

    /**
     * Busca compañías por nombre o display_name
     */
    public function searchByName(string $term, bool $activeOnly = false): array
    {
        $builder = $this->groupStart()
                       ->like('cia_nombre', $term)
                       ->orLike('cia_display_name', $term)
                       ->groupEnd();

        if ($activeOnly) {
            $builder->where('cia_habil', 1);
        }

        return $builder->orderBy('cia_nombre', 'ASC')->findAll();
    }

    /**
     * Obtiene compañías para dropdown con formato personalizado
     */
    public function getForDropdown(bool $activeOnly = true): array
    {
        $builder = $this->select('cia_id, cia_nombre, cia_display_name');
        
        if ($activeOnly) {
            $builder->where('cia_habil', 1);
        }
        
        $results = $builder->orderBy('cia_nombre', 'ASC')->findAll();
        
        $options = [];
        foreach ($results as $cia) {
            $label = $cia['cia_nombre'];
            if (!empty($cia['cia_display_name']) && $cia['cia_display_name'] !== $cia['cia_nombre']) {
                $label .= ' (' . $cia['cia_display_name'] . ')';
            }
            $options[$cia['cia_id']] = $label;
        }
        
        return $options;
    }
}