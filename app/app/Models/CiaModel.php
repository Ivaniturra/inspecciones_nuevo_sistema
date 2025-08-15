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

    // 👇 Campos consistentes con el controller (sin duplicados)
    protected $allowedFields = [
        'cia_nombre',
        'display_name',
        'slug',
        'cia_direccion',
        'cia_logo',
        'cia_habil',
        'brand_nav_bg',
        'brand_nav_text',
        'brand_side_start',
        'brand_side_end',
    ];

    // Fechas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // NOTE: No declaramos $deletedField porque no usamos SoftDeletes

    // Validación
    protected $validationRules = [
        'cia_nombre'        => 'required|min_length[3]|max_length[255]',
        'display_name'      => 'permit_empty|max_length[100]',
        'slug'              => 'permit_empty|regex_match[/^[a-z0-9-]+$/]',
        'cia_direccion'     => 'permit_empty|max_length[500]',
        'cia_logo'          => 'permit_empty|max_length[255]',
        'cia_habil'         => 'required|in_list[0,1]',
        'brand_nav_bg'      => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'brand_nav_text'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'brand_side_start'  => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
        'brand_side_end'    => 'permit_empty|regex_match[/^#([A-Fa-f0-9]{6})$/]',
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
        'slug' => [
            'regex_match' => 'El slug solo puede contener minúsculas, números y guiones (-).',
        ],
        'brand_nav_bg' => ['regex_match' => 'Color de navegación inválido. Usa formato HEX (#RRGGBB).'],
        'brand_nav_text' => ['regex_match' => 'Color de texto de navegación inválido. Usa formato HEX (#RRGGBB).'],
        'brand_side_start' => ['regex_match' => 'Color inicial del sidebar inválido. Usa formato HEX (#RRGGBB).'],
        'brand_side_end' => ['regex_match' => 'Color final del sidebar inválido. Usa formato HEX (#RRGGBB).'],
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

        foreach (['cia_nombre','display_name','cia_direccion','slug'] as $k) {
            if (array_key_exists($k, $d)) {
                $d[$k] = trim((string) $d[$k]);
            }
        }

        if (array_key_exists('cia_habil', $d)) {
            $d['cia_habil'] = (int) $d['cia_habil'] === 1 ? 1 : 0;
        }

        // Normalizar HEX a #RRGGBB si vienen en minúsculas/espacios
        foreach (['brand_nav_bg','brand_nav_text','brand_side_start','brand_side_end'] as $k) {
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
        $d['brand_nav_bg']      = $d['brand_nav_bg']      ?? '#0D6EFD';
        $d['brand_nav_text']    = $d['brand_nav_text']    ?? '#FFFFFF';
        $d['brand_side_start']  = $d['brand_side_start']  ?? '#667EEA';
        $d['brand_side_end']    = $d['brand_side_end']    ?? '#764BA2';

        return $data;
    }

    protected function ensureSlug(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        // Generar slug si no viene
        if (empty($d['slug'])) {
            helper(['text', 'url']);
            $base = $d['cia_nombre'] ?? '';
            $slug = url_title($base, '-', true); // minúsculas + guiones
            $d['slug'] = $slug !== '' ? $slug : 'compania-' . uniqid();
        }
        return $data;
    }

    /* ===================== Consultas personalizadas ===================== */

    public function getActiveCias(): array
    {
        return $this->where('cia_habil', 1)->orderBy('cia_nombre','ASC')->findAll();
    }

    public function getCiasWithUserCount(): array
    {
        return $this->select('cias.*, COUNT(users.user_id) AS total_usuarios')
                    ->join('users', 'users.cia_id = cias.cia_id', 'left')
                    ->groupBy('cias.cia_id')
                    ->orderBy('cia_nombre', 'ASC')
                    ->findAll();
    }

    public function toggleStatus($id): bool
    {
        $cia = $this->find($id);
        if (! $cia) return false;
        $new = (int) ($cia['cia_habil'] == 1 ? 0 : 1);
        return (bool) $this->update($id, ['cia_habil' => $new]);
    }

    public function canDelete($id): bool
    {
        $db = \Config\Database::connect();
        $count = $db->table('users')->where('cia_id', $id)->countAllResults();
        return (int) $count === 0;
    }
}
