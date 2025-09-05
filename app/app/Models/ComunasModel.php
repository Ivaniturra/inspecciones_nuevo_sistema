<?php
namespace App\Models;

use CodeIgniter\Model;

class ComunasModel extends Model
{
    protected $table = 'comunas';
    protected $primaryKey = 'comunas_id';
    protected $allowedFields = ['comunas_nombre', 'provincias_id'];
    
    /**
     * Obtener comunas con provincia
     */
    public function getComunasWithProvincia()
    {
        return $this->select('comunas.*, provincias.provincias_nombre')
            ->join('provincias', 'provincias.provincias_id = comunas.provincias_id', 'left')
            ->orderBy('comunas.comunas_nombre', 'ASC')
            ->findAll();
    }
}