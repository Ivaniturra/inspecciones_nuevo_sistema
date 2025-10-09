<?php

namespace App\Models;

use CodeIgniter\Model;

class ComunasModel extends Model
{
    protected $table            = 'comunas';
    protected $primaryKey       = 'comunas_id';
    protected $useAutoIncrement = false; // Porque usa código como ID
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'comunas_id',
        'provincias_id',
        'comunas_nombre',
        'comunas_codigo',
        'comunas_activo'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'comunas_created_at';
    protected $updatedField  = 'comunas_updated_at';
    protected $deletedField  = 'comunas_deleted_at';

    protected $validationRules = [
        'comunas_id'     => 'required',
        'provincias_id'  => 'required|is_natural_no_zero',
        'comunas_nombre' => 'required|min_length[3]|max_length[100]',
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
    protected $allowCallbacks       = true;

    /**
     * Obtener comunas activas
     */
    public function getListaActivas(): array
    {
        return $this->select('comunas_id, comunas_nombre')
            ->where('comunas_activo', 1)
            ->orderBy('comunas_nombre', 'ASC')
            ->findAll();
    }

    /**
     * Obtener comunas por provincia
     */
    public function getByProvincia($provinciaId): array
    {
        return $this->select('comunas_id, comunas_nombre')
            ->where('provincias_id', $provinciaId)
            ->where('comunas_activo', 1)
            ->orderBy('comunas_nombre', 'ASC')
            ->findAll();
    }

    /**
     * Obtener comuna con datos completos
     */
    public function getComunaWithDetails($id): ?array
    {
        return $this->select('
                comunas.*,
                provincias.provincias_nombre,
                provincias.provincias_id,
                regiones.region_nombre,
                regiones.region_id
            ')
            ->join('provincias', 'provincias.provincias_id = comunas.provincias_id', 'left')
            ->join('regiones', 'regiones.region_id = provincias.regiones_id', 'left')
            ->find($id);
    }

    /**
     * Obtener todas las comunas con detalles
     */
    public function getComunasWithDetails(): array
    {
        return $this->select('
                comunas.*,
                provincias.provincias_nombre,
                regiones.region_nombre
            ')
            ->join('provincias', 'provincias.provincias_id = comunas.provincias_id', 'left')
            ->join('regiones', 'regiones.region_id = provincias.regiones_id', 'left')
            ->where('comunas.comunas_activo', 1)
            ->orderBy('regiones.region_nombre', 'ASC')
            ->orderBy('provincias.provincias_nombre', 'ASC')
            ->orderBy('comunas.comunas_nombre', 'ASC')
            ->findAll();
    }

    /**
     * Cambiar estado
     */
    public function toggleStatus($id): bool
    {
        $comuna = $this->find($id);
        if (!$comuna) return false;
        
        $newStatus = ($comuna['comunas_activo'] ?? 1) == 1 ? 0 : 1;
        return (bool) $this->update($id, ['comunas_activo' => $newStatus]);
    }

    /**
     * Buscar comunas por texto
     */
    public function buscar(string $texto): array
    {
        return $this->select('
                comunas.*,
                provincias.provincias_nombre,
                regiones.region_nombre
            ')
            ->join('provincias', 'provincias.provincias_id = comunas.provincias_id', 'left')
            ->join('regiones', 'regiones.region_id = provincias.regiones_id', 'left')
            ->like('comunas.comunas_nombre', $texto)
            ->where('comunas.comunas_activo', 1)
            ->orderBy('comunas.comunas_nombre', 'ASC')
            ->limit(20)
            ->findAll();
    }
}