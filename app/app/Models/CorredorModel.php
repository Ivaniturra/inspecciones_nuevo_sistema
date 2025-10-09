<?php
namespace App\Models;

use CodeIgniter\Model;

class CiasModel extends Model
{
    protected $table = 'cias';
    protected $primaryKey = 'cia_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    
    protected $allowedFields = [
        'cia_nombre',
        'cia_display_name',
        'cia_logo',
        'cia_habil',
        // ... otros campos
    ];

    protected $useTimestamps = true;
    protected $createdField = 'cia_created_at';
    protected $updatedField = 'cia_updated_at';

    /**
     * Obtener todas las compañías activas
     */
    public function getActiveCias(): array
    {
        return $this->where('cia_habil', 1)
                    ->orderBy('cia_nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener compañías para select (id => nombre)
     */
    public function getCiasForSelect(): array
    {
        $cias = $this->getActiveCias();
        $result = [];
        
        foreach ($cias as $cia) {
            $result[$cia['cia_id']] = $cia['cia_display_name'] ?: $cia['cia_nombre'];
        }
        
        return $result;
    }
}