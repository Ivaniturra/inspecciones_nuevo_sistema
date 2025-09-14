<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoCarroceriaModel extends Model
{
    protected $table            = 'tipo_carroceria';
    protected $primaryKey       = 'tipo_carroceria_id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    protected $allowedFields = [
        'tipo_carroceria_nombre',
        'tipo_inspeccion_id',
        'tipo_carroceria_descripcion',
        'tipo_carroceria_activo',
    ];

    // Fechas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tipo_carroceria_created_at';
    protected $updatedField  = 'tipo_carroceria_updated_at';

    // Validación
    protected $validationRules = [
        'tipo_carroceria_nombre' => 'required|min_length[2]|max_length[100]',
        'tipo_inspeccion_id' => 'required|integer|greater_than[0]',
        'tipo_carroceria_activo' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'tipo_carroceria_nombre' => [
            'required'    => 'El nombre de la carrocería es obligatorio',
            'min_length'  => 'El nombre debe tener al menos 2 caracteres',
            'max_length'  => 'El nombre no puede exceder 100 caracteres',
        ],
        'tipo_inspeccion_id' => [
            'required'      => 'El tipo de inspección es obligatorio',
            'integer'       => 'Debe seleccionar un tipo de inspección válido',
            'greater_than'  => 'Debe seleccionar un tipo de inspección válido',
        ],
        'tipo_carroceria_activo' => [
            'required' => 'El estado es obligatorio',
            'in_list'  => 'El estado debe ser Activo o Inactivo',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['normalize'];
    protected $beforeUpdate   = ['normalize'];

    /* ===================== Callbacks ===================== */

    protected function normalize(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        if (array_key_exists('tipo_carroceria_nombre', $d)) {
            $d['tipo_carroceria_nombre'] = trim((string) $d['tipo_carroceria_nombre']);
        }

        if (array_key_exists('tipo_carroceria_descripcion', $d)) {
            $d['tipo_carroceria_descripcion'] = trim((string) $d['tipo_carroceria_descripcion']);
        }

        if (array_key_exists('tipo_carroceria_activo', $d)) {
            $d['tipo_carroceria_activo'] = (int) $d['tipo_carroceria_activo'] === 1 ? 1 : 0;
        }

        return $data;
    }

    /* ===================== Consultas personalizadas ===================== */

    /**
     * Obtiene carrocerías por tipo de inspección
     */
    public function getCarroceriasByTipoInspeccion(int $tipoInspeccionId): array
    {
        return $this->where('tipo_inspeccion_id', $tipoInspeccionId)
                    ->where('tipo_carroceria_activo', 1)
                    ->orderBy('tipo_carroceria_nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene carrocerías para select por tipo de inspección
     */
    public function getCarroceriasForSelect(int $tipoInspeccionId): array
    {
        $carrocerias = $this->select('tipo_carroceria_id, tipo_carroceria_nombre')
                           ->where('tipo_inspeccion_id', $tipoInspeccionId)
                           ->where('tipo_carroceria_activo', 1)
                           ->orderBy('tipo_carroceria_nombre', 'ASC')
                           ->findAll();

        $result = [];
        foreach ($carrocerias as $carroceria) {
            $result[$carroceria['tipo_carroceria_id']] = $carroceria['tipo_carroceria_nombre'];
        }

        return $result;
    }

    /**
     * Obtiene carrocería con información del tipo de inspección
     */
    public function getCarroceriaWithTipo(int $carroceriaId): ?array
    {
        return $this->select('
                tc.*,
                ti.tipo_inspeccion_nombre,
                ti.tipo_inspeccion_codigo,
                ti.tipo_inspeccion_descripcion as tipo_descripcion
            ')
            ->join('tipos_inspeccion ti', 'ti.tipo_inspeccion_id = tc.tipo_inspeccion_id', 'left')
            ->where('tc.tipo_carroceria_id', $carroceriaId)
            ->where('tc.tipo_carroceria_activo', 1)
            ->first();
    }

    /**
     * Obtiene todas las carrocerías con información del tipo
     */
    public function getAllCarroceriasWithTipo(): array
    {
        return $this->select('
                tc.*,
                ti.tipo_inspeccion_nombre,
                ti.tipo_inspeccion_codigo
            ')
            ->join('tipos_inspeccion ti', 'ti.tipo_inspeccion_id = tc.tipo_inspeccion_id', 'left')
            ->where('tc.tipo_carroceria_activo', 1)
            ->orderBy('ti.tipo_inspeccion_nombre', 'ASC')
            ->orderBy('tc.tipo_carroceria_nombre', 'ASC')
            ->findAll();
    }

    /**
     * Busca carrocerías por término
     */
    public function buscarCarrocerias(string $termino, int $tipoInspeccionId = null): array
    {
        $builder = $this->select('
                tc.*,
                ti.tipo_inspeccion_nombre,
                ti.tipo_inspeccion_codigo
            ')
            ->join('tipos_inspeccion ti', 'ti.tipo_inspeccion_id = tc.tipo_inspeccion_id', 'left')
            ->like('tc.tipo_carroceria_nombre', $termino)
            ->where('tc.tipo_carroceria_activo', 1);

        if ($tipoInspeccionId) {
            $builder->where('tc.tipo_inspeccion_id', $tipoInspeccionId);
        }

        return $builder->orderBy('tc.tipo_carroceria_nombre', 'ASC')
                       ->findAll();
    }

    /**
     * Estadísticas de carrocerías
     */
    public function getEstadisticas(): array
    {
        $db = \Config\Database::connect();
        
        return [
            'total_carrocerias' => $this->countAllResults(false),
            'carrocerias_activas' => $this->where('tipo_carroceria_activo', 1)->countAllResults(false),
            'por_tipo_inspeccion' => $db->table('tipo_carroceria tc')
                ->select('ti.tipo_inspeccion_nombre, COUNT(tc.tipo_carroceria_id) as total')
                ->join('tipos_inspeccion ti', 'ti.tipo_inspeccion_id = tc.tipo_inspeccion_id', 'left')
                ->where('tc.tipo_carroceria_activo', 1)
                ->groupBy('tc.tipo_inspeccion_id')
                ->get()
                ->getResultArray(),
        ];
    }

    /**
     * Cambia el estado activo/inactivo
     */
    public function toggleStatus($id): bool
    {
        $carroceria = $this->find($id);
        if (! $carroceria) return false;
        
        $newStatus = (int) ($carroceria['tipo_carroceria_activo'] == 1 ? 0 : 1);
        return (bool) $this->update($id, ['tipo_carroceria_activo' => $newStatus]);
    }
}