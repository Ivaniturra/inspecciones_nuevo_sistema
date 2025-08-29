<?php

namespace App\Models;

use CodeIgniter\Model;

class CorredorModel extends Model
{
    protected $table            = 'corredores';
    protected $primaryKey       = 'corredor_id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    protected $allowedFields = [
        'corredor_nombre',
        'corredor_email',
        'corredor_telefono',
        'corredor_direccion',
        'corredor_rut',
        'corredor_logo',
        'corredor_habil',
        'corredor_display_name',
        'corredor_slug',
        'corredor_brand_nav_bg',
        'corredor_brand_nav_text',
        'corredor_brand_side_start',
        'corredor_brand_side_end',
        'corredor_logo_path',
    ];

    // Fechas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'corredor_created_at';
    protected $updatedField  = 'corredor_updated_at';

    // Validación
    protected $validationRules = [
        'corredor_nombre'         => 'required|min_length[3]|max_length[255]',
        'corredor_email'          => 'permit_empty|valid_email|max_length[255]',
        'corredor_telefono'       => 'permit_empty|max_length[50]',
        'corredor_direccion'      => 'permit_empty|max_length[500]',
        'corredor_rut'            => 'permit_empty|max_length[20]',
        'corredor_display_name'   => 'permit_empty|max_length[150]',
        'corredor_slug'           => 'permit_empty|regex_match[/^[a-z0-9-]+$/]',
        'corredor_logo'           => 'permit_empty|max_length[255]',
        'corredor_habil'          => 'required|in_list[0,1]',
        'corredor_brand_nav_bg'   => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'corredor_brand_nav_text' => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'corredor_brand_side_start' => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'corredor_brand_side_end' => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'corredor_logo_path'      => 'permit_empty|max_length[255]',
    ];

    protected $validationMessages = [
        'corredor_nombre' => [
            'required'   => 'El nombre del corredor es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede exceder 255 caracteres',
        ],
        'corredor_email' => [
            'valid_email' => 'Debe ingresar un email válido',
            'max_length'  => 'El email no puede exceder 255 caracteres',
        ],
        'corredor_habil' => [
            'required' => 'El estado es obligatorio',
            'in_list'  => 'El estado debe ser Activo o Inactivo',
        ],
        'corredor_slug' => [
            'regex_match' => 'El slug solo puede contener minúsculas, números y guiones (-).',
        ],
        'corredor_brand_nav_bg'      => ['regex_match' => 'Color de navegación inválido. Usa formato HEX (#RRGGBB).'],
        'corredor_brand_nav_text'    => ['regex_match' => 'Color de texto de navegación inválido. Usa formato HEX (#RRGGBB).'],
        'corredor_brand_side_start'  => ['regex_match' => 'Color inicial del sidebar inválido. Usa formato HEX (#RRGGBB).'],
        'corredor_brand_side_end'    => ['regex_match' => 'Color final del sidebar inválido. Usa formato HEX (#RRGGBB).'],
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

        foreach (['corredor_nombre','corredor_email','corredor_telefono','corredor_direccion','corredor_rut','corredor_display_name','corredor_slug'] as $k) {
            if (array_key_exists($k, $d)) {
                $d[$k] = trim((string) $d[$k]);
            }
        }

        if (array_key_exists('corredor_habil', $d)) {
            $d['corredor_habil'] = (int) $d['corredor_habil'] === 1 ? 1 : 0;
        }

        // Normalizar HEX a #RRGGBB
        foreach (['corredor_brand_nav_bg','corredor_brand_nav_text','corredor_brand_side_start','corredor_brand_side_end'] as $k) {
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

        if (! isset($d['corredor_habil'])) $d['corredor_habil'] = 1;

        // Fallbacks de color
        $d['corredor_brand_nav_bg']      = $d['corredor_brand_nav_bg']      ?? '#0D6EFD';
        $d['corredor_brand_nav_text']    = $d['corredor_brand_nav_text']    ?? '#FFFFFF';
        $d['corredor_brand_side_start']  = $d['corredor_brand_side_start']  ?? '#667EEA';
        $d['corredor_brand_side_end']    = $d['corredor_brand_side_end']    ?? '#764BA2';

        return $data;
    }

    protected function ensureSlug(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        // Generar slug si no viene
        if (empty($d['corredor_slug'])) {
            helper(['text', 'url']);
            $base = $d['corredor_nombre'] ?? '';
            $slug = url_title($base, '-', true);
            $d['corredor_slug'] = $slug !== '' ? $slug : 'corredor-' . uniqid();
        }
        return $data;
    }

    /* ===================== Consultas personalizadas ===================== */

    public function getActiveCorredores(): array
    {
        return $this->where('corredor_habil', 1)->orderBy('corredor_nombre','ASC')->findAll();
    }

    public function getCorredoresWithCias(): array
    {
        return $this->select('corredores.*, GROUP_CONCAT(CONCAT(cias.cia_nombre, " (", COALESCE(cias.cia_display_name, ""), ")") SEPARATOR ", ") as cias_nombres, COUNT(DISTINCT corredor_cias.cia_id) as total_cias')
                    ->join('corredor_cias', 'corredor_cias.corredor_id = corredores.corredor_id', 'left')
                    ->join('cias', 'cias.cia_id = corredor_cias.cia_id', 'left')
                    ->where('corredor_cias.corredor_cia_activo', 1)
                    ->groupBy('corredores.corredor_id')
                    ->orderBy('corredor_nombre', 'ASC')
                    ->findAll();
    }

    public function getCorredoresByCia($ciaId): array
    {
        return $this->select('corredores.*')
                    ->join('corredor_cias', 'corredor_cias.corredor_id = corredores.corredor_id')
                    ->where('corredor_cias.cia_id', $ciaId)
                    ->where('corredor_cias.corredor_cia_activo', 1)
                    ->where('corredores.corredor_habil', 1)
                    ->orderBy('corredor_nombre', 'ASC')
                    ->findAll();
    }

    public function toggleStatus($id): bool
    {
        $corredor = $this->find($id);
        if (! $corredor) return false;
        $new = (int) ($corredor['corredor_habil'] == 1 ? 0 : 1);
        return (bool) $this->update($id, ['corredor_habil' => $new]);
    }

    public function canDelete($id): bool
    {
        $db = \Config\Database::connect();
        // Verificar si tiene usuarios u otras relaciones asociadas
        // Ajusta según tu estructura de datos
        $count = $db->table('users')->where('corredor_id', $id)->countAllResults();
        return (int) $count === 0;
    }

    public function searchCorredores($term = '', $ciaId = null): array
    {
        $builder = $this->select('corredores.*, GROUP_CONCAT(CONCAT(cias.cia_nombre, " (", COALESCE(cias.cia_display_name, ""), ")") SEPARATOR ", ") as cias_nombres, COUNT(DISTINCT corredor_cias.cia_id) as total_cias')
                        ->join('corredor_cias', 'corredor_cias.corredor_id = corredores.corredor_id', 'left')
                        ->join('cias', 'cias.cia_id = corredor_cias.cia_id', 'left')
                        ->where('corredor_cias.corredor_cia_activo', 1);

        if (!empty($term)) {
            $builder->groupStart()
                   ->like('corredor_nombre', $term)
                   ->orLike('corredor_email', $term)
                   ->orLike('corredor_rut', $term)
                   ->groupEnd();
        }

        if ($ciaId) {
            $builder->where('corredor_cias.cia_id', $ciaId);
        }

        return $builder->groupBy('corredores.corredor_id')
                       ->orderBy('corredor_nombre', 'ASC')
                       ->findAll();
    }

    /* ===================== Métodos para gestionar relaciones ===================== */

    public function getCiasDelCorredor($corredorId): array
    {
        $db = \Config\Database::connect();
        return $db->table('corredor_cias cc')
                 ->select('cc.*, c.cia_nombre, c.cia_display_name, c.cia_logo')
                 ->join('cias c', 'c.cia_id = cc.cia_id')
                 ->where('cc.corredor_id', $corredorId)
                 ->where('cc.corredor_cia_activo', 1)
                 ->orderBy('c.cia_nombre', 'ASC')
                 ->get()
                 ->getResultArray();
    }

    public function assignCiaToCorrector($corredorId, $ciaId): bool
    {
        $db = \Config\Database::connect();
        
        // Verificar si ya existe la relación
        $exists = $db->table('corredor_cias')
                    ->where('corredor_id', $corredorId)
                    ->where('cia_id', $ciaId)
                    ->countAllResults();

        if ($exists > 0) {
            // Activar si existe pero está inactiva
            return $db->table('corredor_cias')
                     ->where('corredor_id', $corredorId)
                     ->where('cia_id', $ciaId)
                     ->update([
                         'corredor_cia_activo' => 1,
                         'corredor_cia_updated_at' => date('Y-m-d H:i:s')
                     ]);
        } else {
            // Crear nueva relación
            return $db->table('corredor_cias')
                     ->insert([
                         'corredor_id' => $corredorId,
                         'cia_id' => $ciaId,
                         'corredor_cia_activo' => 1,
                         'corredor_cia_created_at' => date('Y-m-d H:i:s'),
                         'corredor_cia_updated_at' => date('Y-m-d H:i:s')
                     ]);
        }
    }

    public function removeCiaFromCorrector($corredorId, $ciaId): bool
    {
        $db = \Config\Database::connect();
        return $db->table('corredor_cias')
                 ->where('corredor_id', $corredorId)
                 ->where('cia_id', $ciaId)
                 ->update([
                     'corredor_cia_activo' => 0,
                     'corredor_cia_updated_at' => date('Y-m-d H:i:s')
                 ]);
    }

    public function updateCorredorCias($corredorId, $ciaIds): bool
    {
        $db = \Config\Database::connect();
        
        try {
            $db->transStart();

            // Desactivar todas las relaciones actuales
            $db->table('corredor_cias')
              ->where('corredor_id', $corredorId)
              ->update([
                  'corredor_cia_activo' => 0,
                  'corredor_cia_updated_at' => date('Y-m-d H:i:s')
              ]);

            // Activar o crear las relaciones seleccionadas
            if (!empty($ciaIds)) {
                foreach ($ciaIds as $ciaId) {
                    $exists = $db->table('corredor_cias')
                                ->where('corredor_id', $corredorId)
                                ->where('cia_id', $ciaId)
                                ->countAllResults();

                    if ($exists > 0) {
                        $db->table('corredor_cias')
                          ->where('corredor_id', $corredorId)
                          ->where('cia_id', $ciaId)
                          ->update([
                              'corredor_cia_activo' => 1,
                              'corredor_cia_updated_at' => date('Y-m-d H:i:s')
                          ]);
                    } else {
                        $db->table('corredor_cias')
                          ->insert([
                              'corredor_id' => $corredorId,
                              'cia_id' => $ciaId,
                              'corredor_cia_activo' => 1,
                              'corredor_cia_created_at' => date('Y-m-d H:i:s'),
                              'corredor_cia_updated_at' => date('Y-m-d H:i:s')
                          ]);
                    }
                }
            }

            $db->transComplete();
            return $db->transStatus();
            
        } catch (\Exception $e) {
            $db->transRollback();
            return false;
        }
    }
}