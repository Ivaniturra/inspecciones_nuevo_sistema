<?php

namespace App\Models;

use CodeIgniter\Model;

class EstadoModel extends Model
{
    protected $table            = 'estados';
    protected $primaryKey       = 'estado_id';
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    protected $allowedFields = [
        'estado_nombre',
    ];

    // Fechas - ACTUALIZADO con nomenclatura estado_XXXX
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'estado_created_at';  // ← CAMBIO AQUÍ
    protected $updatedField  = 'estado_updated_at';  // ← CAMBIO AQUÍ

    // Validación
    protected $validationRules = [
        'estado_nombre' => 'required|min_length[2]|max_length[255]|is_unique[estados.estado_nombre,estado_id,{estado_id}]',
    ];

    protected $validationMessages = [
        'estado_nombre' => [
            'required'    => 'El nombre del estado es obligatorio',
            'min_length'  => 'El nombre debe tener al menos 2 caracteres',
            'max_length'  => 'El nombre no puede exceder 255 caracteres',
            'is_unique'   => 'Ya existe un estado con ese nombre',
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

        if (array_key_exists('estado_nombre', $d)) {
            $d['estado_nombre'] = trim((string) $d['estado_nombre']);
        }

        return $data;
    }

    /* ===================== Consultas personalizadas ===================== */

    /**
     * Obtiene todos los estados ordenados por nombre
     */
    public function getAllEstados(): array
    {
        return $this->orderBy('estado_nombre', 'ASC')->findAll();
    }

    /**
     * Obtiene estados para usar en selects
     */
    public function getEstadoForSelect(): array
    {
        $estados = $this->select('estado_id, estado_nombre')
                       ->orderBy('estado_nombre', 'ASC')
                       ->findAll();

        $result = [];
        foreach ($estados as $estado) {
            $result[$estado['estado_id']] = $estado['estado_nombre'];
        }

        return $result;
    }

    /**
     * Obtiene estados ordenados por ID (orden de flujo)
     */
    public function getEstadosPorFlujo(): array
    {
        return $this->orderBy('estado_id', 'ASC')->findAll();
    }

    /**
     * Busca estados por nombre
     */
    public function buscarEstados(string $termino): array
    {
        return $this->like('estado_nombre', $termino)
                    ->orderBy('estado_nombre', 'ASC')
                    ->findAll();
    }

    /**
     * Obtiene el estado por nombre exacto
     */
    public function getEstadoByNombre(string $nombre): ?array
    {
        return $this->where('estado_nombre', $nombre)->first();
    }

    /**
     * Estadísticas de estados
     */
    public function getEstadisticas(): array
    {
        return [
            'total_estados' => $this->countAllResults(false),
        ];
    }
}