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
        'asegurado',
        'rut',
        'patente',
        'marca',
        'modelo',
        'n_poliza',
        'direccion',
        'comuna',
        'celular',
        'telefono',
        'cia_id',
        'user_id',
        'fecha_creacion',
        'estado'
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
    public function getInspeccionesWithDetails()
    {
        return $this->select('
            inspecciones.*,
            cias.cia_nombre,
            users.user_nombre,
            comunas.comunas_nombre
        ')
        ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
        ->join('users', 'users.user_id = inspecciones.user_id', 'left')
        ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left') // ← Agregar
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
        
        $por_estado = $this->select('inspecciones_estado, COUNT(*) as total')
            ->groupBy('inspecciones_estado')
            ->findAll();

        $hoy = $this->where('DATE(inspecciones_created_at)', date('Y-m-d'))
            ->countAllResults();

        return [
            'total' => $total,
            'por_estado' => $por_estado,
            'creadas_hoy' => $hoy
        ];
    }
    public function getInspeccionesByUserWithDetails($userId)
{
    return $this->select('
        inspecciones.*,
        cias.cia_nombre,
        users.user_nombre,
        comunas.comunas_nombre
    ')
    ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
    ->join('users', 'users.user_id = inspecciones.user_id', 'left')
    ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
    ->where('inspecciones.user_id', $userId)
    ->orderBy('inspecciones.created_at', 'DESC')
    ->findAll();
}

/**
 * Obtener una inspección específica con detalles
 */
public function getInspeccionWithDetailsById($id)
{
    return $this->select('
        inspecciones.*,
        cias.cia_nombre,
        cias.cia_email,
        cias.cia_telefono,
        users.user_nombre,
        users.user_email,
        comunas.comunas_nombre,
        regiones.regiones_nombre
    ')
    ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
    ->join('users', 'users.user_id = inspecciones.user_id', 'left')
    ->join('comunas', 'comunas.comunas_id = inspecciones.comunas_id', 'left')
    ->join('regiones', 'regiones.regiones_id = comunas.regiones_id', 'left')
    ->where('inspecciones.inspeccion_id', $id)
    ->first();
}

/**
 * Contar inspecciones por estado para un usuario
 */
public function contarPorEstadoUsuario($userId, $estado)
{
    return $this->where('user_id', $userId)
                ->where('estado', $estado)
                ->countAllResults();
}

/**
 * Obtener inspecciones recientes de un usuario
 */
public function getInspeccionesRecientesUsuario($userId, $limite = 5)
{
    return $this->where('user_id', $userId)
                ->orderBy('created_at', 'DESC')
                ->limit($limite)
                ->findAll();
}

/**
 * Buscar inspecciones de un usuario
 */
public function buscarInspeccionesUsuario($userId, $termino)
{
    return $this->where('user_id', $userId)
                ->groupStart()
                    ->like('asegurado', $termino)
                    ->orLike('rut', $termino)
                    ->orLike('patente', $termino)
                    ->orLike('marca', $termino)
                    ->orLike('modelo', $termino)
                ->groupEnd()
                ->orderBy('created_at', 'DESC')
                ->findAll();
}

/**
 * Obtener estadísticas del usuario por mes
 */
public function getEstadisticasUsuarioMes($userId, $año = null, $mes = null)
{
    if (!$año) $año = date('Y');
    if (!$mes) $mes = date('m');
    
    return $this->select('estado, COUNT(*) as cantidad')
                ->where('user_id', $userId)
                ->where('YEAR(created_at)', $año)
                ->where('MONTH(created_at)', $mes)
                ->groupBy('estado')
                ->findAll();
}

/**
 * Validar RUT chileno
 */
public function validarRut($rut)
{
    // Limpiar el RUT
    $rut = preg_replace('/[^0-9kK]/', '', $rut);
    $rut = strtoupper($rut);
    
    if (strlen($rut) < 8 || strlen($rut) > 9) {
        return false;
    }
    
    $dv = substr($rut, -1);
    $numero = substr($rut, 0, -1);
    
    // Calcular dígito verificador
    $suma = 0;
    $multiplicador = 2;
    
    for ($i = strlen($numero) - 1; $i >= 0; $i--) {
        $suma += $numero[$i] * $multiplicador;
        $multiplicador = $multiplicador == 7 ? 2 : $multiplicador + 1;
    }
    
    $resto = $suma % 11;
    $dvCalculado = 11 - $resto;
    
    if ($dvCalculado == 11) {
        $dvCalculado = '0';
    } elseif ($dvCalculado == 10) {
        $dvCalculado = 'K';
    }
    
    return $dv == $dvCalculado;
}
} 