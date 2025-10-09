<?php

namespace App\Models;

use CodeIgniter\Model;

class RegionesModel extends Model
{
    protected $table            = 'regiones';
    protected $primaryKey       = 'region_id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    
    protected $allowedFields    = [
        'region_nombre',
        'region_numero',
        'region_codigo',
        'region_activo',
        'region_orden'
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'region_created_at';
    protected $updatedField  = 'region_updated_at';
    protected $deletedField  = 'region_deleted_at';

    protected $validationRules = [
        'region_nombre' => 'required|min_length[3]|max_length[100]',
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;
    protected $allowCallbacks       = true;

    /**
     * Obtener regiones activas para select
     */
    public function getListaActivas(): array
    {
        return $this->select('region_id, region_nombre, region_numero')
            ->where('region_activo', 1)
            ->orderBy('region_orden', 'ASC')
            ->findAll();
    }

    /**
     * Obtener región con conteo de provincias
     */
    public function getRegionWithStats($id): ?array
    {
        $db = \Config\Database::connect();
        
        $region = $this->find($id);
        if (!$region) return null;

        $region['total_provincias'] = $db->table('provincias')
            ->where('regiones_id', $id)
            ->countAllResults();

        $region['total_comunas'] = $db->table('comunas c')
            ->join('provincias p', 'p.provincias_id = c.provincias_id')
            ->where('p.regiones_id', $id)
            ->countAllResults();

        return $region;
    }

    /**
     * Cambiar estado
     */
    public function toggleStatus($id): bool
    {
        $region = $this->find($id);
        if (!$region) return false;
        
        $newStatus = ($region['region_activo'] ?? 1) == 1 ? 0 : 1;
        return (bool) $this->update($id, ['region_activo' => $newStatus]);
    }
}