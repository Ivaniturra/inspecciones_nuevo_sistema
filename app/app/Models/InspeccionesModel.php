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
            'required' => 'El n�mero de celular es obligatorio'
        ]
    ];

    /**
     * Crear inspecci�n con comentario autom�tico en bit�cora
     */
    public function crearInspeccionConBitacora($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Crear inspecci�n
            $inspecciones_id = $this->insert($data);

            if ($inspecciones_id) {
                // Crear comentario autom�tico en bit�cora
                $bitacoraModel = new \App\Models\BitacoraModel();
                
                $comentario_data = [
                    'inspecciones_id' => $inspecciones_id,
                    'user_id' => $data['user_id'],
                    'bitacora_comentario' => 'Inspecci�n creada por ' . session('user_name'),
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
            log_message('error', 'Error al crear inspecci�n: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Obtener inspecciones con datos de compa��a y usuario
     */
    public function getInspeccionesWithDetails()
    {
        return $this->select('
                inspecciones.*,
                cias.cia_nombre,
                users.user_nombre
            ')
            ->join('cias', 'cias.cia_id = inspecciones.cia_id', 'left')
            ->join('users', 'users.user_id = inspecciones.user_id', 'left')
            ->orderBy('inspecciones.inspecciones_created_at', 'DESC')
            ->findAll();
    }

    /**
     * Obtener inspecciones por compa��a
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
     * Obtener estad�sticas generales
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
}
}