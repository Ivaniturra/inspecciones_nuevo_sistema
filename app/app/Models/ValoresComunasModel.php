<?php

namespace App\Models;

use CodeIgniter\Model;

class ValoresComunasModel extends Model
{
    protected $table            = 'valores_comunas';
    protected $primaryKey       = 'valores_id';  // ← Asegúrate que sea esto
    protected $useAutoIncrement = true;

    protected $returnType     = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields  = true;

    protected $allowedFields = [
        'comunas_id',
        'cia_id',
        'tipo_usuario',
        'tipo_vehiculo_id',
        'unidad_medida',
        'valor',
        'moneda',
        'descripcion',
        'fecha_vigencia_desde',
        'fecha_vigencia_hasta',
        'activo',
    ];

    // Fechas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    // Validación
    protected $validationRules = [
        'region_id'            => 'required|is_natural_no_zero',
        'provincias_id'        => 'required|is_natural_no_zero',
        'comunas_id'           => 'required|is_natural_no_zero',
        'cia_id'               => 'required|is_natural_no_zero',
        'tipo_usuario'         => 'required',
        'tipo_vehiculo_id'     => 'required|is_natural_no_zero',
        'unidad_medida'        => 'required|in_list[UF,CLP,UTM]',
        'moneda'               => 'permit_empty|in_list[UF,CLP,UTM]',
        'valor'                => 'required|decimal',
        'fecha_vigencia_desde' => 'required|valid_date',
        'fecha_vigencia_hasta' => 'permit_empty|valid_date',
        'activo'               => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'comunas_id' => [
            'required' => 'El código de comuna es obligatorio',
        ],
        'cia_id' => [
            'required' => 'La compañía es obligatoria',
            'integer'  => 'ID de compañía inválido',
        ],
        'tipo_usuario' => [
            'required' => 'El tipo de usuario es obligatorio',
        ],
        'valor' => [
            'required' => 'El valor es obligatorio',
            'decimal'  => 'El valor debe ser numérico',
        ],
        'fecha_vigencia_desde' => [
            'required'   => 'La fecha de vigencia desde es obligatoria',
            'valid_date' => 'Fecha de vigencia desde inválida',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setDefaults'];
    protected $beforeUpdate   = ['setDefaults'];

    protected function setDefaults(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        if (! isset($d['activo'])) $d['activo'] = 1;
        if (! isset($d['moneda'])) $d['moneda'] = 'CLP';
        if (! isset($d['tipo_usuario'])) $d['tipo_usuario'] = 'general';

        return $data;
    }

    /* ===================== Consultas personalizadas ===================== */

    /**
     * Obtener todos los valores con información de comuna y compañía
     */
    public function getValoresWithDetails(): array
    {
        return $this->select('valores_comunas.*, comunas.comunas_nombre, regiones.region_nombre, cias.cia_nombre')
                    ->join('comunas',    'comunas.comunas_id = valores_comunas.comunas_id', 'left')
                    ->join('provincias', 'provincias.provincias_id = comunas.provincias_id', 'left')
                    ->join('regiones',   'regiones.region_id = provincias.regiones_id', 'left')
                    ->join('cias', 'cias.cia_id = valores_comunas.cia_id', 'left')
                    ->where('valores_comunas.activo', 1)
                    ->orderBy('cias.cia_nombre', 'ASC')
                    ->orderBy('comunas.comunas_nombre', 'ASC')
                    ->orderBy('valores_comunas.tipo_usuario', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener valores por compañía
     */
    public function getValoresByCia($ciaId): array
    {
        return $this->select('valores_comunas.*, comunas.comunas_nombre, regiones.region_nombre')
                    ->join('comunas',    'comunas.comunas_id = valores_comunas.comunas_id', 'left')
                    ->join('provincias', 'provincias.provincias_id = comunas.provincias_id', 'left')
                    ->join('regiones',   'regiones.region_id = provincias.regiones_id', 'left')
                    ->where('valores_comunas.cia_id', $ciaId)
                    ->where('valores_comunas.activo', 1)
                    ->orderBy('comunas.comunas_nombre', 'ASC')
                    ->orderBy('valores_comunas.tipo_usuario', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener valores por comuna
     */
    public function getValoresByComuna($comunaCodigo): array
    {
        return $this->select('valores_comunas.*, cias.cia_nombre, comunas.comunas_nombre')
                    ->join('cias', 'cias.cia_id = valores_comunas.cia_id', 'left')
                    ->join('comunas', 'comunas.comunas_id = valores_comunas.comuncomunas_ida_codigo', 'left')
                    ->where('valores_comunas.comunas_id', $comunaCodigo)
                    ->where('valores_comunas.activo', 1)
                    ->orderBy('cias.cia_nombre', 'ASC')
                    ->orderBy('valores_comunas.tipo_usuario', 'ASC')
                    ->findAll();
    }

    /**
     * Obtener valor específico vigente
     */
    public function getValorVigente($comunaCodigo, $ciaId, $tipoUsuario = 'general'): ?array
    {
        $today = date('Y-m-d');
        
        return $this->where('comunas_id', $comunaCodigo)
                    ->where('cia_id', $ciaId)
                    ->where('tipo_usuario', $tipoUsuario)
                    ->where('activo', 1)
                    ->where('fecha_vigencia_desde <=', $today)
                    ->groupStart()
                        ->where('fecha_vigencia_hasta >=', $today)
                        ->orWhere('fecha_vigencia_hasta', null)
                    ->groupEnd()
                    ->orderBy('fecha_vigencia_desde', 'DESC')
                    ->first();
    }

    /**
     * Verificar si existe un valor para los parámetros dados (versión completa)
     */
    public function existeValorCompleto($comunaCodigo, $ciaId, $tipoUsuario, $tipoVehiculoId, $unidadMedida, $excludeId = null): bool
    {
        $builder = $this->where('comunas_id', $comunaCodigo)
                        ->where('cia_id', $ciaId)
                        ->where('tipo_usuario', $tipoUsuario)
                        ->where('tipo_vehiculo_id', $tipoVehiculoId)
                        ->where('unidad_medida', $unidadMedida)
                        ->where('activo', 1);

        if ($excludeId) {
            $builder->where('valores_id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    /**
     * Obtener tipos de usuario únicos
     */
    public function getTiposUsuario(): array
    {
        return $this->select('tipo_usuario')
                    ->distinct()
                    ->where('activo', 1)
                    ->orderBy('tipo_usuario', 'ASC')
                    ->findColumn('tipo_usuario');
    }

    /**
     * Toggle estado activo/inactivo
     */
    public function toggleStatus($id): bool
    {
        $valor = $this->find($id);
        if (! $valor) return false;
        
        $newStatus = $valor['activo'] == 1 ? 0 : 1;
        return (bool) $this->update($id, ['activo' => $newStatus]);
    }

    /**
     * Obtener estadísticas
     */
    public function getEstadisticas(): array
    {
        $db = \Config\Database::connect();
        
        return [
            'total_valores'   => $this->where('activo', 1)->countAllResults(),
            'total_companias' => $this->select('cia_id')->distinct()->where('activo', 1)->countAllResults(),
            'total_comunas'   => $this->select('comunas_id')->distinct()->where('activo', 1)->countAllResults(),
            'tipos_usuario'   => count($this->getTiposUsuario()),
        ];
    }
}