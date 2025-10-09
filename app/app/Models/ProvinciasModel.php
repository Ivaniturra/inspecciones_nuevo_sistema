<?php

namespace App\Models;

use CodeIgniter\Model;

class ProvinciasModel extends Model
{
    protected $table            = 'provincias';
    protected $primaryKey       = 'provincias_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'regiones_id',
        'provincias_nombre',
        'provincias_codigo',
        'provincias_activo'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'provincias_created_at';
    protected $updatedField  = 'provincias_updated_at';
    protected $deletedField  = 'provincias_deleted_at';

    // Validation
    protected $validationRules = [
        'regiones_id'       => 'required|is_natural_no_zero',
        'provincias_nombre' => 'required|min_length[3]|max_length[100]',
    ];

    protected $validationMessages = [
        'regiones_id' => [
            'required' => 'La región es obligatoria',
            'is_natural_no_zero' => 'ID de región inválido',
        ],
        'provincias_nombre' => [
            'required' => 'El nombre de la provincia es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = [];
    protected $afterInsert    = [];
    protected $beforeUpdate   = [];
    protected $afterUpdate    = [];
    protected $beforeFind     = [];
    protected $afterFind      = [];
    protected $beforeDelete   = [];
    protected $afterDelete    = [];

    /**
     * Obtener provincias activas
     */
    public function getListaActivas(): array
    {
        return $this->select('provincias_id, provincias_nombre')
            ->where('provincias_activo', 1)
            ->orderBy('provincias_nombre', 'ASC')
            ->findAll();
    }

    /**
     * Obtener provincias por región
     */
    public function getByRegion($regionId): array
    {
        return $this->select('provincias_id, provincias_nombre')
            ->where('regiones_id', $regionId)
            ->where('provincias_activo', 1)
            ->orderBy('provincias_nombre', 'ASC')
            ->findAll();
    }

    /**
     * Obtener provincia con datos de región
     */
    public function getProvinciaWithRegion($id): ?array
    {
        return $this->select('
                provincias.*,
                regiones.region_nombre,
                regiones.region_id
            ')
            ->join('regiones', 'regiones.region_id = provincias.regiones_id', 'left')
            ->find($id);
    }

    /**
     * Obtener todas las provincias con datos de región
     */
    public function getProvinciasWithRegion(): array
    {
        return $this->select('
                provincias.*,
                regiones.region_nombre,
                regiones.region_id
            ')
            ->join('regiones', 'regiones.region_id = provincias.regiones_id', 'left')
            ->where('provincias.provincias_activo', 1)
            ->orderBy('regiones.region_nombre', 'ASC')
            ->orderBy('provincias.provincias_nombre', 'ASC')
            ->findAll();
    }

    /**
     * Contar comunas por provincia
     */
    public function contarComunas($provinciaId): int
    {
        $db = \Config\Database::connect();
        return $db->table('comunas')
            ->where('provincias_id', $provinciaId)
            ->countAllResults();
    }

    /**
     * Verificar si existe una provincia por nombre
     */
    public function existePorNombre(string $nombre, $excludeId = null): bool
    {
        $builder = $this->where('provincias_nombre', $nombre);
        
        if ($excludeId) {
            $builder->where('provincias_id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

    /**
     * Cambiar estado activo/inactivo
     */
    public function toggleStatus($id): bool
    {
        $provincia = $this->find($id);
        if (!$provincia) return false;
        
        $newStatus = ($provincia['provincias_activo'] ?? 1) == 1 ? 0 : 1;
        return (bool) $this->update($id, ['provincias_activo' => $newStatus]);
    }

    /**
     * Obtener estadísticas
     */
    public function getEstadisticas(): array
    {
        $db = \Config\Database::connect();
        
        return [
            'total_provincias' => $this->countAllResults(),
            'provincias_activas' => $this->where('provincias_activo', 1)->countAllResults(),
            'provincias_inactivas' => $this->where('provincias_activo', 0)->countAllResults(),
            'total_comunas' => $db->table('comunas')->countAllResults(),
        ];
    }
}