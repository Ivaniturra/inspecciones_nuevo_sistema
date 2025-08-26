<?php

namespace App\Models;

use CodeIgniter\Model;

class TipoVehiculoModel extends Model
{
    protected $table            = 'tipo_vehiculo';
    protected $primaryKey       = 'tipo_vehiculo_id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = true; // Usa soft deletes
    protected $protectFields  = true;

    protected $allowedFields = [
        'tipo_vehiculo_clave',
        'tipo_vehiculo_nombre',
        'tipo_vehiculo_descripcion',
        'tipo_vehiculo_activo',
    ];

    // Fechas - ACTUALIZADO con nomenclatura tipo_vehiculo_XXXX
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'tipo_vehiculo_created_at';  
    protected $updatedField  = 'tipo_vehiculo_updated_at';  
    protected $deletedField  = 'tipo_vehiculo_deleted_at';  

    // Validación
    protected $validationRules = [
        'tipo_vehiculo_clave'       => 'permit_empty|max_length[50]|is_unique[tipo_vehiculo.tipo_vehiculo_clave,tipo_vehiculo_id,{tipo_vehiculo_id}]',
        'tipo_vehiculo_nombre'      => 'required|min_length[2]|max_length[100]',
        'tipo_vehiculo_descripcion' => 'permit_empty|max_length[255]',
        'tipo_vehiculo_activo'      => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'tipo_vehiculo_clave' => [
            'max_length'  => 'La clave no puede exceder 50 caracteres',
            'is_unique'   => 'Ya existe un tipo de vehículo con esa clave',
        ],
        'tipo_vehiculo_nombre' => [
            'required'    => 'El nombre del tipo de vehículo es obligatorio',
            'min_length'  => 'El nombre debe tener al menos 2 caracteres',
            'max_length'  => 'El nombre no puede exceder 100 caracteres',
        ],
        'tipo_vehiculo_descripcion' => [
            'max_length'  => 'La descripción no puede exceder 255 caracteres',
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
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        // Sanitizar textos
        foreach (['tipo_vehiculo_clave', 'tipo_vehiculo_nombre', 'tipo_vehiculo_descripcion'] as $field) {
            if (array_key_exists($field, $d)) {
                $d[$field] = trim((string) $d[$field]);
                if ($d[$field] === '') {
                    $d[$field] = null;
                }
            }
        }

        // Asegurar que activo sea 0 o 1
        if (array_key_exists('tipo_vehiculo_activo', $d)) {
            $d['tipo_vehiculo_activo'] = (int) $d['tipo_vehiculo_activo'] === 1 ? 1 : 0;
        }

        return $data;
    }

    protected function generateClave(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        // Si no se proporciona clave, generar una basada en el nombre
        if (empty($d['tipo_vehiculo_clave']) && !empty($d['tipo_vehiculo_nombre'])) {
            helper(['text', 'url']);
            $baseClave = url_title($d['tipo_vehiculo_nombre'], '_', true);
            $baseClave = strtolower($baseClave);
            
            // Verificar unicidad
            $clave = $baseClave;
            $contador = 1;
            while ($this->where('tipo_vehiculo_clave', $clave)->first()) {
                $clave = $baseClave . '_' . $contador;
                $contador++;
            }
            
            $d['tipo_vehiculo_clave'] = $clave;
        }

        return $data;
    }

    /* ===================== Consultas personalizadas ===================== */

    /**
     * Obtiene solo tipos de vehículo activos
     */
    public function getActivos()
    {
        return $this->where('tipo_vehiculo_activo', 1)
                    ->orderBy('tipo_vehiculo_nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene tipos de vehículo para usar en selects
     */
    public function getForSelect(): array
    {
        $tipos = $this->select('tipo_vehiculo_id, tipo_vehiculo_nombre')
                      ->where('tipo_vehiculo_activo', 1)
                      ->orderBy('tipo_vehiculo_nombre', 'ASC')
                      ->findAll();

        $result = [];
        foreach ($tipos as $tipo) {
            $result[$tipo['tipo_vehiculo_id']] = $tipo['tipo_vehiculo_nombre'];
        }

        return $result;
    }

    /**
     * Busca tipos de vehículo por nombre o clave
     */
    public function buscar(string $termino)
    {
        return $this->groupStart()
                    ->like('tipo_vehiculo_nombre', $termino)
                    ->orLike('tipo_vehiculo_clave', $termino)
                    ->orLike('tipo_vehiculo_descripcion', $termino)
                    ->groupEnd()
                    ->orderBy('tipo_vehiculo_nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene tipo de vehículo por clave
     */
    public function getByClave(string $clave): ?array
    {
        return $this->where('tipo_vehiculo_clave', $clave)->first();
    }

    /**
     * Toggle estado activo/inactivo
     */
    public function toggleStatus($id): bool
    {
        $tipo = $this->find($id);
        if (!$tipo) return false;

        $newStatus = (int) ($tipo['tipo_vehiculo_activo'] == 1 ? 0 : 1);
        return $this->update($id, ['tipo_vehiculo_activo' => $newStatus]);
    }

    /**
     * Estadísticas
     */
    public function getEstadisticas(): array
    {
        return [
            'total'    => $this->countAllResults(false),
            'activos'  => $this->where('tipo_vehiculo_activo', 1)->countAllResults(false),
            'inactivos'=> $this->where('tipo_vehiculo_activo', 0)->countAllResults(false),
        ];
    }

    /**
     * Verifica si se puede eliminar (no tiene vehículos asociados)
     */
    public function canDelete($id): bool
    {
        // Aquí verificarías si hay vehículos asociados
        // Por ahora retorna true, ajusta según tu lógica de negocio
        $db = \Config\Database::connect();
        
        // Ejemplo: verificar en tabla vehiculos si existe
        // $count = $db->table('vehiculos')->where('tipo_vehiculo_id', $id)->countAllResults();
        // return (int) $count === 0;
        
        return true; // Temporal, ajustar según necesidades
    }
}