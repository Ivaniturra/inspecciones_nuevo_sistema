<?php
namespace App\Models;

use CodeIgniter\Model;

class InspeccionesModel extends Model
{
    protected $table = 'inspecciones';
    protected $primaryKey = 'inspeccion_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'inspecciones_asegurado',
        'inspecciones_rut',
        'inspecciones_email', // ← NUEVO
        'inspecciones_patente',
        'inspecciones_marca',
        'inspecciones_modelo',
        'inspecciones_n_poliza',
        'inspecciones_direccion',
        'inspecciones_celular',
        'inspecciones_telefono',
        'inspecciones_observaciones', // ← NUEVO
        'cia_id',
        'comunas_id',
        'tipo_inspeccion_id', // ← NUEVO
        'tipo_carroceria_id', // ← NUEVO
        'user_id',
        'estado_id' // ← CAMBIO: ahora es estado_id en lugar de estado
    ]; 

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    // Validation
    protected $validationRules = [
        'inspecciones_asegurado' => 'required|min_length[3]|max_length[100]',
        'inspecciones_rut' => 'required|min_length[8]|max_length[12]',
        'inspecciones_patente' => 'required|min_length[6]|max_length[8]',
        'inspecciones_marca' => 'required|min_length[2]|max_length[50]',
        'inspecciones_modelo' => 'required|min_length[2]|max_length[50]',
        'inspecciones_n_poliza' => 'required|min_length[3]|max_length[20]',
        'inspecciones_direccion' => 'required|min_length[5]|max_length[200]',
        'inspecciones_comuna' => 'required|min_length[3]|max_length[50]',
        'inspecciones_celular' => 'required|min_length[8]|max_length[15]',
        'inspecciones_telefono' => 'permit_empty|min_length[8]|max_length[15]',
        'cia_id' => 'required|is_natural_no_zero',
            'comunas_id' => 'required|is_natural_no_zero',  
        'user_id' => 'required|is_natural_no_zero'
    ];

    protected $validationMessages = [
        'inspecciones_asegurado' => [
            'required' => 'El nombre del asegurado es obligatorio',
            'min_length' => 'El nombre debe tener al menos 3 caracteres',
            'max_length' => 'El nombre no puede superar los 100 caracteres'
        ],
        'inspecciones_rut' => [
            'required' => 'El RUT es obligatorio',
            'min_length' => 'El RUT debe tener al menos 8 caracteres',
            'max_length' => 'El RUT no puede superar los 12 caracteres'
        ],
        'inspecciones_patente' => [
            'required' => 'La patente es obligatoria',
            'min_length' => 'La patente debe tener al menos 6 caracteres'
        ],
        'inspecciones_celular' => [
            'required' => 'El número de celular es obligatorio'
        ]
    ];

    /**
     * Crear inspección con comentario automático en bitácora
     */
    public function crearInspeccionConBitacora($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Crear inspección
            $inspecciones_id = $this->insert($data);

            if ($inspecciones_id) {
                // Crear comentario automático en bitácora
                $bitacoraModel = new \App\Models\BitacoraModel();
                
                $comentario_data = [
                    'inspecciones_id' => $inspecciones_id,
                    'user_id' => $data['user_id'],
                    'bitacora_comentario' => 'Inspección creada por ' . session('user_name'),
                    'bitacora_tipo_comentario' => 'estado_cambio',
                    'bitacora_estado_nuevo' => 'pendiente',
                    'bitacora_es_privado' => 0
                ];

                $bitacoraModel->insert($comentario_data);

                // Actualizar contador
                $this->update($inspecciones_id, [
                    'inspecciones_total_comentarios' => 1
                ]);

                $db->transCommit();
                return $inspecciones_id;
            }

            $db->transRollback();
            return false;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error al crear inspección: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener inspecciones con datos de compañía y usuario
     */
    public function searchInspeccionesWithEstados($filters = []): array
    {
        $builder = $this->select('
            inspecciones.*,
            cias.cia_nombre,
            users.user_nombre,
            comunas.comunas_nombre,
            estados.estado_nombre,
            estados.estado_color
        ')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left');

        // Aplicar filtros
        if (!empty($filters['estado_id'])) {
            $builder->where('inspecciones.estado_id', $filters['estado_id']);
        }
        
        if (!empty($filters['user_id'])) {
            $builder->where('inspecciones.user_id', $filters['user_id']);
        }
        
        if (!empty($filters['cia_id'])) {
            $builder->where('inspecciones.cia_id', $filters['cia_id']);
        }
        
        if (!empty($filters['fecha_desde'])) {
            $builder->where('inspecciones.inspecciones_created_at >=', $filters['fecha_desde']);
        }
        
        if (!empty($filters['fecha_hasta'])) {
            $builder->where('inspecciones.inspecciones_created_at <=', $filters['fecha_hasta']);
        }

        return $builder->orderBy('inspecciones.inspecciones_created_at', 'DESC')
                    ->findAll();
    }
    public function getInspeccionWithDetails($id): ?array
    {
        return $this->select('
            inspecciones.*,
            cias.cia_nombre,
            cias.cia_logo,
            users.user_nombre,
            users.user_email,
            comunas.comunas_nombre,
            estados.estado_nombre,
            estados.estado_color
        ')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left') // ← NUEVO
        ->where('inspecciones.inspecciones_id', $id)
        ->first();
    }
    public function getInspeccionesWithDetails(): array
    {
        return $this->select('
            inspecciones.*,
            cias.cia_nombre,
            cias.cia_logo,
            users.user_nombre,
            users.user_email,
            comunas.comunas_nombre,
            estados.estado_nombre,
            estados.estado_color
        ')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left') // ← NUEVO
        ->orderBy('inspecciones.inspecciones_created_at', 'DESC')
        ->findAll();
    }


    /**
     * Obtener inspecciones por compañía
     */
    public function getInspeccionesByCompany($cia_id)
    {
        return $this->where('cia_id', $cia_id)
            ->orderBy('inspecciones_created_at', 'DESC')
            ->findAll();
    }

    /**
     * Obtener inspecciones por usuario
     */
    public function getInspeccionesByUser($user_id)
    {
        return $this->where('user_id', $user_id)
            ->orderBy('inspecciones_created_at', 'DESC')
            ->findAll();
    }

    /**
     * Obtener estadísticas generales
     */
    public function getEstadisticas()
    {
        $total = $this->countAllResults();
        
        // Obtener estadísticas por estado_id con nombres de estados
        $por_estado = $this->select('
            estados.estado_nombre,
            estados.estado_color,
            COUNT(*) as total
        ')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left')
        ->groupBy('inspecciones.estado_id, estados.estado_nombre, estados.estado_color')
        ->findAll();

        $hoy = $this->where('DATE(inspecciones_created_at)', date('Y-m-d'))
            ->countAllResults();

        return [
            'total' => $total,
            'por_estado' => $por_estado,
            'creadas_hoy' => $hoy
        ];
    }
    public function getEstadisticasByUser($userId)
    {
        $total = $this->where('user_id', $userId)->countAllResults();
        
        $por_estado = $this->select('
            estados.estado_nombre,
            estados.estado_color,
            COUNT(*) as total
        ')
        ->join('estados', 'estados.estado_id = inspecciones.estado_id', 'left')
        ->where('inspecciones.user_id', $userId)
        ->groupBy('inspecciones.estado_id, estados.estado_nombre, estados.estado_color')
        ->findAll();

        $hoy = $this->where('user_id', $userId)
            ->where('DATE(inspecciones_created_at)', date('Y-m-d'))
            ->countAllResults();

        return [
            'total' => $total,
            'por_estado' => $por_estado,
            'creadas_hoy' => $hoy
        ];
    }

} 