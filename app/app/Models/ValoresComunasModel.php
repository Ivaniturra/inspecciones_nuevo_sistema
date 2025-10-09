<?php

namespace App\Models;

use CodeIgniter\Model;

class ValoresComunasModel extends Model
{
    protected $table = 'valores_comunas';
    protected $primaryKey = 'valores_id';
    
    protected $allowedFields = [
        'comunas_id',
        'cia_id',
        'tipo_vehiculo_id',
        'valores_tipo_usuario',
        'valores_unidad_medida',
        'valores_valor',
        'valores_moneda',
        'valores_descripcion',
        'valores_fecha_vigencia_desde',
        'valores_fecha_vigencia_hasta',
        'valores_activo', // ← ✅ ASEGURATE DE QUE ESTE CAMPO ESTÉ AQUÍ
    ];

    // ... resto del modelo

    /**
     * Toggle estado activo/inactivo - Método simplificado
     */
    public function toggleStatus($id): bool
    {
        $valor = $this->find($id);
        if (!$valor) return false;
        
        $newStatus = ($valor['valores_activo'] ?? 1) == 1 ? 0 : 1;
        return (bool) $this->update($id, ['valores_activo' => $newStatus]);
    }

    // Fechas
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'valores_created_at';
    protected $updatedField  = 'valores_updated_at';

    // VALIDACIÓN CORREGIDA PARA NOMBRES REALES
    protected $validationRules = [
        'comunas_id'                     => 'required',
        'cia_id'                         => 'required|is_natural_no_zero',
        'tipo_vehiculo_id'               => 'required|is_natural_no_zero',
        'valores_tipo_usuario'           => 'required',
        'valores_unidad_medida'          => 'required|in_list[UF,CLP,UTM]',
        'valores_moneda'                 => 'permit_empty|in_list[UF,CLP,UTM]',
        'valores_valor'                  => 'required|decimal',
        'valores_fecha_vigencia_desde'   => 'required|valid_date',
        'valores_fecha_vigencia_hasta'   => 'permit_empty|valid_date',
        'valores_activo'                 => 'required|in_list[0,1]',
    ];

    protected $validationMessages = [
        'comunas_id' => [
            'required' => 'El código de comuna es obligatorio',
        ],
        'cia_id' => [
            'required' => 'La compañía es obligatoria',
            'is_natural_no_zero' => 'ID de compañía inválido',
        ],
        'valores_tipo_usuario' => [
            'required' => 'El tipo de usuario es obligatorio',
        ],
        'valores_valor' => [
            'required' => 'El valor es obligatorio',
            'decimal'  => 'El valor debe ser numérico',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    protected $allowCallbacks = true;
    protected $beforeInsert   = ['setDefaults'];
    protected $beforeUpdate   = ['setDefaults'];

    protected function setDefaults(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        if (! isset($d['valores_activo'])) $d['valores_activo'] = 1;
        if (! isset($d['valores_moneda'])) $d['valores_moneda'] = 'CLP';
        if (! isset($d['valores_tipo_usuario'])) $d['valores_tipo_usuario'] = 'general';

        return $data;
    }

    /* ===================== CONSULTAS CON ESTRUCTURA REAL ===================== */

    /**
     * Query principal con nombres reales de campos y tablas
     */
    public function getValoresWithDetails(): array
    {
        return $this->select('
                valores_comunas.*,
                valores_comunas.valores_tipo_usuario AS tipo_usuario,
                comunas.comunas_nombre,
                provincias.provincias_nombre,
                regiones.region_nombre,
                cias.cia_nombre,
                tv.tipo_inspeccion_nombre
            ')
            ->join('comunas',          'comunas.comunas_id = valores_comunas.comunas_id', 'left')
            ->join('provincias',       'provincias.provincias_id = comunas.provincias_id', 'left')
            ->join('regiones',         'regiones.region_id = provincias.regiones_id', 'left')
            ->join('cias',             'cias.cia_id = valores_comunas.cia_id', 'left')
            ->join('tipos_inspeccion tv', 'tv.tipo_inspeccion_id = valores_comunas.tipo_inspeccion_id', 'left') // verifica el nombre real de la tabla
            ->where('valores_comunas.valores_activo', 1)
            ->orderBy('cias.cia_nombre', 'ASC')
            ->orderBy('comunas.comunas_nombre', 'ASC')
            ->orderBy('valores_comunas.valores_tipo_usuario', 'ASC')
            ->findAll();
    }

    public function getValoresByCia($ciaId): array
    {
        return $this->select('
                valores_comunas.*, 
                comunas.comunas_nombre, 
                provincias.provincias_nombre,
                regiones.region_nombre,
                tv.tipo_vehiculo_nombre
            ')
            ->join('comunas',         'comunas.comunas_id = valores_comunas.comunas_id', 'left')
            ->join('provincias',      'provincias.provincias_id = comunas.provincias_id', 'left')
            ->join('regiones',        'regiones.region_id = provincias.regiones_id', 'left')
            ->join('tipos_inspeccion tv', 'tv.tipo_inspeccion_id = valores_comunas.tipo_inspeccion_id', 'left')
            ->where('valores_comunas.cia_id', $ciaId)
            ->where('valores_comunas.valores_activo', 1)
            ->orderBy('comunas.comunas_nombre', 'ASC')
            ->orderBy('valores_comunas.valores_tipo_usuario', 'ASC')
            ->findAll();
    }

    public function getValoresByComuna($comunaCodigo): array
    {
        return $this->select('
                valores_comunas.*, 
                cias.cia_nombre, 
                comunas.comunas_nombre,
                tv.tipo_vehiculo_nombre
            ')
            ->join('cias',            'cias.cia_id = valores_comunas.cia_id', 'left')
            ->join('comunas',         'comunas.comunas_id = valores_comunas.comunas_id', 'left')
            ->join('tipos_inspeccion tv', 'tv.tipo_inspeccion_id = valores_comunas.tipo_inspeccion_id', 'left')
            ->where('valores_comunas.comunas_id', $comunaCodigo)
            ->where('valores_comunas.valores_activo', 1)
            ->orderBy('cias.cia_nombre', 'ASC')
            ->orderBy('valores_comunas.valores_tipo_usuario', 'ASC')
            ->findAll();
    }

    /**
     * Obtener valor específico vigente
     */
    public function getValorVigente($comunaCodigo, $ciaId, $tipoUsuario = 'general', $tipoVehiculoId = null): ?array
    {
        $today = date('Y-m-d');
        
        $builder = $this->where('comunas_id', $comunaCodigo)
                        ->where('cia_id', $ciaId)
                        ->where('valores_tipo_usuario', $tipoUsuario)
                        ->where('valores_activo', 1)
                        ->where('valores_fecha_vigencia_desde <=', $today)
                        ->groupStart()
                            ->where('valores_fecha_vigencia_hasta >=', $today)
                            ->orWhere('valores_fecha_vigencia_hasta', null)
                        ->groupEnd();

        if ($tipoVehiculoId) {
            $builder->where('tipo_inspeccion_id', $tipoVehiculoId);
        }

        return $builder->orderBy('valores_fecha_vigencia_desde', 'DESC')->first();
    }

    /**
     * Verificar duplicados
     */
    public function existeValorCompleto($comunaCodigo, $ciaId, $tipoUsuario, $tipoVehiculoId, $unidadMedida, $excludeId = null): bool
    {
        $builder = $this->where('comunas_id', $comunaCodigo)
                        ->where('cia_id', $ciaId)
                        ->where('valores_tipo_usuario', $tipoUsuario)
                        ->where('tipo_inspeccion_id', $tipoVehiculoId)
                        ->where('valores_unidad_medida', $unidadMedida)
                        ->where('valores_activo', 1);

        if ($excludeId) {
            $builder->where('valores_id !=', $excludeId);
        }

        return $builder->countAllResults() > 0;
    }

    public function existeValor($comunaCodigo, $ciaId, $tipoUsuario, $excludeId = null): bool
    {
        $builder = $this->where('comunas_id', $comunaCodigo)
                        ->where('cia_id', $ciaId)
                        ->where('valores_tipo_usuario', $tipoUsuario)
                        ->where('valores_activo', 1);

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
        return $this->select('valores_tipo_usuario')
                    ->distinct()
                    ->where('valores_activo', 1)
                    ->orderBy('valores_tipo_usuario', 'ASC')
                    ->findColumn('valores_tipo_usuario');
    }
 
    public function getEstadisticas(): array
    {
        return [
            'total_valores'   => $this->where('valores_activo', 1)->countAllResults(),
            'total_companias' => $this->select('cia_id')->distinct()->where('valores_activo', 1)->countAllResults(),
            'total_comunas'   => $this->select('comunas_id')->distinct()->where('valores_activo', 1)->countAllResults(),
            'tipos_usuario'   => count($this->getTiposUsuario()),
        ];
    }

    /**
     * Obtener valor con todos los detalles
     */
    public function getValorWithFullDetails($id): ?array
    {
        return $this->select('
                valores_comunas.*, 
                comunas.comunas_nombre, 
                provincias.provincias_nombre,
                regiones.region_nombre, 
                cias.cia_nombre,
                tv.tipo_vehiculo_nombre
            ')
            ->join('comunas',         'comunas.comunas_id = valores_comunas.comunas_id', 'left')
            ->join('provincias',      'provincias.provincias_id = comunas.provincias_id', 'left')
            ->join('regiones',        'regiones.region_id = provincias.regiones_id', 'left')
            ->join('cias',            'cias.cia_id = valores_comunas.cia_id', 'left')
            ->join('tipos_inspeccion tv', 'tv.tipo_inspeccion_id = valores_comunas.tipo_inspeccion_id', 'left')
            ->find($id);
    }
}