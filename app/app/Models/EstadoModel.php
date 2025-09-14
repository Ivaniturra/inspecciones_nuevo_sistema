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
        'estado_color',
    ];
    public function getBootstrapClassForColor(string $hexColor): string
    {
        $colorMap = [
            '#17a2b8' => 'info',
            '#007bff' => 'primary',
            '#ffc107' => 'warning', 
            '#6c757d' => 'secondary',
            '#28a745' => 'success',
            '#198754' => 'success',
            '#dc3545' => 'danger',
        ];
        
        return $colorMap[$hexColor] ?? 'secondary';
    }

    // Método para obtener contraste de texto
    public function getTextColorForBackground(string $hexColor): string
    {
        // Convertir hex a RGB
        $r = hexdec(substr($hexColor, 1, 2));
        $g = hexdec(substr($hexColor, 3, 2));
        $b = hexdec(substr($hexColor, 5, 2));
        
        // Calcular luminancia
        $luminance = (0.299 * $r + 0.587 * $g + 0.114 * $b) / 255;
        
        return $luminance > 0.5 ? '#000000' : '#ffffff';
    }
    // Fechas - ACTUALIZADO con nomenclatura estado_XXXX
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'estado_created_at';  // ← CAMBIO AQUÍ
    protected $updatedField  = 'estado_updated_at';  // ← CAMBIO AQUÍ

    // Validación
    protected $validationRules = [
        'estado_nombre' => 'required|min_length[2]|max_length[255]|is_unique[estados.estado_nombre,estado_id,{estado_id}]',
        'estado_color'  => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
    ];
    public function getColorForEstado(int $estadoId): string
    {
        $estado = $this->find($estadoId);
        return $estado['estado_color'] ?? '#6c757d';
    }
    protected $validationMessages = [
        'estado_nombre' => [
            'required'    => 'El nombre del estado es obligatorio',
            'min_length'  => 'El nombre debe tener al menos 2 caracteres',
            'max_length'  => 'El nombre no puede exceder 255 caracteres',
            'is_unique'   => 'Ya existe un estado con ese nombre',
        ],
        'estado_color' => [ // ← NUEVO
        'required'     => 'El color del estado es obligatorio',
        'regex_match'  => 'El color debe ser un código hexadecimal válido (#RRGGBB)',
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