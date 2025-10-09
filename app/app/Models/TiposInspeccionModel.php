<?php

namespace App\Models;

use CodeIgniter\Model;

class TiposInspeccionModel extends Model
{
    protected $table            = 'tipos_inspeccion';
    protected $primaryKey       = 'tipo_inspeccion_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $protectFields    = true;

    protected $allowedFields = [
        'tipo_inspeccion_clave',
        'tipo_inspeccion_nombre',
        'tipo_inspeccion_descripcion',
        'tipo_inspeccion_activo',
    ];

    // Fechas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tipo_inspeccion_created_at';
    protected $updatedField  = 'tipo_inspeccion_updated_at';
    protected $deletedField  = 'tipo_inspeccion_deleted_at';

    // Validación
    protected $validationRules = [
        'tipo_inspeccion_clave'       => 'permit_empty|max_length[50]|is_unique[tipos_inspeccion.tipo_inspeccion_clave,tipo_inspeccion_id,{tipo_inspeccion_id}]',
        'tipo_inspeccion_nombre'      => 'required|min_length[2]|max_length[100]',
        'tipo_inspeccion_descripcion' => 'permit_empty|max_length[255]',
        'tipo_inspeccion_activo'      => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'tipo_inspeccion_clave' => [
            'max_length' => 'La clave no puede exceder 50 caracteres',
            'is_unique'  => 'Ya existe un tipo de inspección con esa clave',
        ],
        'tipo_inspeccion_nombre' => [
            'required'   => 'El nombre del tipo de inspección es obligatorio',
            'min_length' => 'El nombre debe tener al menos 2 caracteres',
            'max_length' => 'El nombre no puede exceder 100 caracteres',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['sanitizeInputs', 'generateClave'];
    protected $beforeUpdate   = ['sanitizeInputs'];

    /* ===================== Callbacks ===================== */

    protected function sanitizeInputs(array $data): array
    {
        if (!isset($data['data'])) return $data;
        $d =& $data['data'];

        foreach (['tipo_inspeccion_clave', 'tipo_inspeccion_nombre', 'tipo_inspeccion_descripcion'] as $field) {
            if (array_key_exists($field, $d)) {
                $d[$field] = trim((string) $d[$field]);
                if ($d[$field] === '') {
                    $d[$field] = null;
                }
            }
        }

        if (array_key_exists('tipo_inspeccion_activo', $d)) {
            $d['tipo_inspeccion_activo'] = (int) $d['tipo_inspeccion_activo'] === 1 ? 1 : 0;
        }

        return $data;
    }

    protected function generateClave(array $data): array
    {
        if (!isset($data['data'])) return $data;
        $d =& $data['data'];

        if (empty($d['tipo_inspeccion_clave']) && !empty($d['tipo_inspeccion_nombre'])) {
            helper(['text', 'url']);
            $baseClave = url_title($d['tipo_inspeccion_nombre'], '_', true);
            $baseClave = strtolower($baseClave);
            
            $clave = $baseClave;
            $contador = 1;
            while ($this->where('tipo_inspeccion_clave', $clave)->first()) {
                $clave = $baseClave . '_' . $contador;
                $contador++;
            }
            
            $d['tipo_inspeccion_clave'] = $clave;
        }

        return $data;
    }

    /* ===================== Consultas ===================== */

    /**
     * Obtener tipos activos
     */
    public function getActivos(): array
    {
        return $this->where('tipo_inspeccion_activo', 1)
                    ->orderBy('tipo_inspeccion_nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Para selects
     */
    public function getListaActivos(): array
    {
        $tipos = $this->select('tipo_inspeccion_id, tipo_inspeccion_nombre')
                      ->where('tipo_inspeccion_activo', 1)
                      ->orderBy('tipo_inspeccion_nombre', 'ASC')
                      ->findAll();

        $result = [];
        foreach ($tipos as $tipo) {
            $result[$tipo['tipo_inspeccion_id']] = $tipo['tipo_inspeccion_nombre'];
        }

        return $result;
    }

    /**
     * Buscar por término
     */
    public function buscar(string $termino): array
    {
        return $this->groupStart()
                    ->like('tipo_inspeccion_nombre', $termino)
                    ->orLike('tipo_inspeccion_clave', $termino)
                    ->orLike('tipo_inspeccion_descripcion', $termino)
                    ->groupEnd()
                    ->orderBy('tipo_inspeccion_nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Por clave
     */
    public function getByClave(string $clave): ?array
    {
        return $this->where('tipo_inspeccion_clave', $clave)->first();
    }

    /**
     * Toggle estado
     */
    public function toggleStatus($id): bool
    {
        $tipo = $this->find($id);
        if (!$tipo) return false;

        $newStatus = (int) ($tipo['tipo_inspeccion_activo'] == 1 ? 0 : 1);
        return $this->update($id, ['tipo_inspeccion_activo' => $newStatus]);
    }

    /**
     * Estadísticas
     */
    public function getEstadisticas(): array
    {
        return [
            'total'     => $this->countAllResults(false),
            'activos'   => $this->where('tipo_inspeccion_activo', 1)->countAllResults(false),
            'inactivos' => $this->where('tipo_inspeccion_activo', 0)->countAllResults(false),
        ];
    }

    /**
     * Verificar si se puede eliminar
     */
    public function canDelete($id): bool
    {
        $db = \Config\Database::connect();
        
        // Verificar en valores_comunas
        $count = $db->table('valores_comunas')
                    ->where('tipo_inspeccion_id', $id)
                    ->countAllResults();
        
        return (int) $count === 0;
    }
}