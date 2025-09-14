<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoInspeccionModel extends Model
{
    protected $table            = 'tipos_inspeccion';
    protected $primaryKey       = 'tipo_inspeccion_id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    protected $allowedFields = [
        'tipo_inspeccion_nombre',
        'tipo_inspeccion_descripcion',
        'tipo_inspeccion_codigo',
        'tipo_inspeccion_activo',
    ];

    // Fechas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tipo_inspeccion_created_at';
    protected $updatedField  = 'tipo_inspeccion_updated_at';

    // Validación
    protected $validationRules = [
        'tipo_inspeccion_nombre' => 'required|min_length[3]|max_length[100]|is_unique[tipos_inspeccion.tipo_inspeccion_nombre,tipo_inspeccion_id,{tipo_inspeccion_id}]',
        'tipo_inspeccion_codigo' => 'required|min_length[2]|max_length[20]|is_unique[tipos_inspeccion.tipo_inspeccion_codigo,tipo_inspeccion_id,{tipo_inspeccion_id}]',
        'tipo_inspeccion_activo' => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'tipo_inspeccion_nombre' => [
            'required'    => 'El nombre del tipo de inspección es obligatorio',
            'min_length'  => 'El nombre debe tener al menos 3 caracteres',
            'max_length'  => 'El nombre no puede exceder 100 caracteres',
            'is_unique'   => 'Ya existe un tipo de inspección con ese nombre',
        ],
        'tipo_inspeccion_codigo' => [
            'required'    => 'El código es obligatorio',
            'min_length'  => 'El código debe tener al menos 2 caracteres',
            'max_length'  => 'El código no puede exceder 20 caracteres',
            'is_unique'   => 'Ya existe un tipo de inspección con ese código',
        ],
        'tipo_inspeccion_activo' => [
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

        if (array_key_exists('tipo_inspeccion_nombre', $d)) {
            $d['tipo_inspeccion_nombre'] = trim((string) $d['tipo_inspeccion_nombre']);
        }

        if (array_key_exists('tipo_inspeccion_codigo', $d)) {
            $d['tipo_inspeccion_codigo'] = strtoupper(trim((string) $d['tipo_inspeccion_codigo']));
        }

        if (array_key_exists('tipo_inspeccion_descripcion', $d)) {
            $d['tipo_inspeccion_descripcion'] = trim((string) $d['tipo_inspeccion_descripcion']);
        }

        if (array_key_exists('tipo_inspeccion_activo', $d)) {
            $d['tipo_inspeccion_activo'] = (int) $d['tipo_inspeccion_activo'] === 1 ? 1 : 0;
        }

        return $data;
    }

    /* ===================== Consultas personalizadas ===================== */

    /**
     * Obtiene todos los tipos de inspección activos
     */
    public function getTiposActivos(): array
    {
        return $this->where('tipo_inspeccion_activo', 1)
                    ->orderBy('tipo_inspeccion_nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene tipos para usar en selects
     */
    public function getTiposForSelect(): array
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
     * Obtiene tipo por código
     */
    public function getTipoByCodigo(string $codigo): ?array
    {
        return $this->where('tipo_inspeccion_codigo', strtoupper($codigo))
                    ->where('tipo_inspeccion_activo', 1)
                    ->first();
    }

    /**
     * Obtiene carrocerías asociadas a un tipo de inspección
     */
    public function getCarroceriasByTipo(int $tipoInspeccionId): array
    {
        $db = \Config\Database::connect();
        
        return $db->table('tipo_carroceria tc')
                  ->select('tc.tipo_carroceria_id, tc.tipo_carroceria_nombre, tc.tipo_carroceria_descripcion')
                  ->where('tc.tipo_inspeccion_id', $tipoInspeccionId)
                  ->where('tc.tipo_carroceria_activo', 1)
                  ->orderBy('tc.tipo_carroceria_nombre', 'ASC')
                  ->get()
                  ->getResultArray();
    }

    /**
     * Obtiene información completa con carrocerías
     */
    public function getTipoWithCarrocerias(int $tipoInspeccionId): array
    {
        $tipo = $this->find($tipoInspeccionId);
        if (!$tipo) return [];

        $tipo['carrocerias'] = $this->getCarroceriasByTipo($tipoInspeccionId);
        
        return $tipo;
    }

    /**
     * Estadísticas
     */
    public function getEstadisticas(): array
    {
        $db = \Config\Database::connect();
        
        $stats = [
            'total_tipos' => $this->countAllResults(false),
            'tipos_activos' => $this->where('tipo_inspeccion_activo', 1)->countAllResults(false),
        ];

        // Contar carrocerías por tipo
        $tiposConCarrocerias = $db->table('tipos_inspeccion ti')
                                 ->select('ti.tipo_inspeccion_nombre, COUNT(tc.tipo_carroceria_id) as total_carrocerias')
                                 ->join('tipo_carroceria tc', 'tc.tipo_inspeccion_id = ti.tipo_inspeccion_id', 'left')
                                 ->where('ti.tipo_inspeccion_activo', 1)
                                 ->where('tc.tipo_carroceria_activo', 1)
                                 ->groupBy('ti.tipo_inspeccion_id')
                                 ->get()
                                 ->getResultArray();

        $stats['carrocerias_por_tipo'] = $tiposConCarrocerias;
        
        return $stats;
    }

    /**
     * Cambia el estado activo/inactivo
     */
    public function toggleStatus($id): bool
    {
        $tipo = $this->find($id);
        if (! $tipo) return false;
        
        $newStatus = (int) ($tipo['tipo_inspeccion_activo'] == 1 ? 0 : 1);
        return (bool) $this->update($id, ['tipo_inspeccion_activo' => $newStatus]);
    }
}