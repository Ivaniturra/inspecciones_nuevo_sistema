<?php
namespace App\Models;

use CodeIgniter\Model;

class BitacoraModel extends Model
{
    protected $table = 'bitacora';
    protected $primaryKey = 'bitacora_id';
    protected $useAutoIncrement = true;
    protected $returnType = 'array';
    protected $useSoftDeletes = false;
    protected $protectFields = true;
    protected $allowedFields = [
        'inspecciones_id',
        'user_id',
        'bitacora_comentario',
        'bitacora_tipo_comentario',
        'bitacora_estado_anterior',
        'bitacora_estado_nuevo',
        'bitacora_es_privado',
        'bitacora_adjuntos'
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat = 'datetime';
    protected $createdField = 'bitacora_created_at';
    protected $updatedField = 'bitacora_updated_at';

    // Validation
    protected $validationRules = [
        'inspecciones_id' => 'required|is_natural_no_zero',
        'user_id' => 'required|is_natural_no_zero',
        'bitacora_comentario' => 'required|min_length[3]|max_length[2000]',
        'bitacora_tipo_comentario' => 'required|in_list[general,estado_cambio,observacion,seguimiento]',
        'bitacora_es_privado' => 'in_list[0,1]'
    ];

    /**
     * Obtener bitácora de una inspección con datos del usuario
     */
    public function getBitacoraByInspeccion($inspecciones_id, $incluir_privados = false)
    {
        $query = $this->select('
                bitacora.*,
                users.user_nombre,
                users.user_email,
                perfiles.perfil_nombre
            ')
            ->join('users', 'users.user_id = bitacora.user_id', 'left')
            ->join('perfiles', 'perfiles.perfil_id = users.user_perfil', 'left')
            ->where('bitacora.inspecciones_id', $inspecciones_id);

        // Si no incluir privados, filtrar
        if (!$incluir_privados) {
            $query->where('bitacora.bitacora_es_privado', 0);
        }

        return $query->orderBy('bitacora.bitacora_created_at', 'DESC')->findAll();
    }

    /**
     * Agregar comentario a la bitácora y actualizar contador
     */
    public function agregarComentario($data)
    {
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insertar comentario
            $comentario_id = $this->insert($data);

            if ($comentario_id) {
                // Actualizar contador en inspecciones
                $inspeccionesModel = new \App\Models\InspeccionesModel();
                $inspeccionesModel->where('inspecciones_id', $data['inspecciones_id'])
                    ->set('inspecciones_total_comentarios', 'inspecciones_total_comentarios + 1', false)
                    ->update();

                $db->transCommit();
                return $comentario_id;
            }

            $db->transRollback();
            return false;

        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Error al agregar comentario: ' . $e->getMessage());
            return false;
        }
    }
}
