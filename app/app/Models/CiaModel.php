<?php

namespace App\Models;

use CodeIgniter\Model;

class CiaModel extends Model
{
    protected $table      = 'cias';
    protected $primaryKey = 'cia_id';

    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'cia_nombre',
        'cia_logo', 
        'cia_direccion',
        'cia_habil'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    protected $deletedField  = 'deleted_at';

    // Validation
    protected $validationRules = [
        'cia_nombre' => 'required|min_length[3]|max_length[255]',
        'cia_habil'  => 'required|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'cia_nombre' => [
            'required'    => 'El nombre de la compañía es obligatorio',
            'min_length'  => 'El nombre debe tener al menos 3 caracteres',
            'max_length'  => 'El nombre no puede exceder 255 caracteres'
        ],
        'cia_habil' => [
            'required' => 'El estado es obligatorio',
            'in_list'  => 'El estado debe ser Activo o Inactivo'
        ]
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

    // Métodos personalizados
    
    /**
     * Obtener todas las compañías activas
     */
    public function getActiveCias()
    {
        return $this->where('cia_habil', 1)->findAll();
    }

    /**
     * Obtener compañía con conteo de usuarios
     */
    public function getCiasWithUserCount()
    {
        return $this->select('cias.*, COUNT(users.user_id) as total_usuarios')
                   ->join('users', 'users.cia_id = cias.cia_id', 'left')
                   ->groupBy('cias.cia_id')
                   ->findAll();
    }

    /**
     * Cambiar estado de la compañía
     */
    public function toggleStatus($id)
    {
        $cia = $this->find($id);
        if ($cia) {
            $newStatus = $cia['cia_habil'] == 1 ? 0 : 1;
            return $this->update($id, ['cia_habil' => $newStatus]);
        }
        return false;
    }

    /**
     * Verificar si se puede eliminar la compañía
     */
    public function canDelete($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('users');
        $count = $builder->where('cia_id', $id)->countAllResults();
        
        return $count === 0;
    }
}