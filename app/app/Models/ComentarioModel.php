<?php

namespace App\Models;

use CodeIgniter\Model;
class ComentarioModel extends Model
{
    protected $table = 'comentarios';
    protected $primaryKey = 'comentario_id';
    
    protected $allowedFields = [
        'comentario_nombre',
        'cia_id', 
        'perfil_id',
        'comentario_id_cia_interno',
        'comentario_devuelve',
        'comentario_elimina', 
        'comentario_envia_correo',
        'comentario_habil',  // ? ? ASEGURATE DE QUE ESTE CAMPO EST? AQU?
        // otros campos...
    ];

    protected $useTimestamps = true;
    protected $createdField = 'comentario_created_at';
    protected $updatedField = 'comentario_updated_at';
 

    // Validación
    protected $validationRules = [
        'comentario_nombre'           => 'required|min_length[2]|max_length[2000]',
        'cia_id'                      => 'required|integer|greater_than[0]',
        'comentario_id_cia_interno'   => 'permit_empty|integer',
        'comentario_devuelve'         => 'permit_empty|in_list[0,1]',
        'comentario_elimina'          => 'permit_empty|in_list[0,1]',
        'comentario_envia_correo'     => 'permit_empty|in_list[0,1]',
    ];

    protected $validationMessages = [
        'comentario_nombre' => [
            'required'   => 'El comentario es obligatorio',
            'min_length' => 'El comentario debe tener al menos 2 caracteres',
            'max_length' => 'El comentario no puede exceder 2000 caracteres',
        ],
        'cia_id' => [
            'required'      => 'La compañía es obligatoria',
            'integer'       => 'La compañía debe ser válida',
            'greater_than'  => 'La compañía debe ser válida',
        ],
        'comentario_id_cia_interno' => [
            'integer' => 'El ID interno debe ser un número válido',
        ],
    ];

    protected $skipValidation       = false;
    protected $cleanValidationRules = true;

    // Callbacks
    protected $allowCallbacks = true;
    protected $beforeInsert   = ['sanitizeInputs'];
    protected $beforeUpdate   = ['sanitizeInputs'];

    /* ===================== Callbacks ===================== */

    protected function sanitizeInputs(array $data): array
    {
        if (! isset($data['data'])) return $data;
        $d =& $data['data'];

        // Sanitizar texto del comentario
        if (array_key_exists('comentario_nombre', $d)) {
            $d['comentario_nombre'] = trim((string) $d['comentario_nombre']);
        }

        // Asegurar que los flags sean 0 o 1
        foreach (['comentario_devuelve', 'comentario_elimina', 'comentario_envia_correo'] as $flag) {
            if (array_key_exists($flag, $d)) {
                $d[$flag] = (int) $d[$flag] === 1 ? 1 : 0;
            }
        }

        // Sanitizar IDs
        if (array_key_exists('cia_id', $d)) {
            $d['cia_id'] = (int) $d['cia_id'];
        }
        if (array_key_exists('comentario_id_cia_interno', $d)) {
            $d['comentario_id_cia_interno'] = $d['comentario_id_cia_interno'] ? (int) $d['comentario_id_cia_interno'] : null;
        }

        return $data;
    }

    /* ===================== Consultas personalizadas ===================== */

    /**
     * Obtiene comentarios con información de la compañía
     */
    public function getComentariosWithCia()
    {
        return $this->select('comentarios.*, cias.cia_nombre')
                    ->join('cias', 'cias.cia_id = comentarios.cia_id', 'left')
                    ->orderBy('comentario_id', 'DESC')
                    ->findAll();
    }

    /**
     * Obtiene comentarios por compañía
     */
    public function getComentariosByCia($ciaId)
    {
        return $this->where('cia_id', (int) $ciaId)
                    ->orderBy('comentario_id', 'DESC')
                    ->findAll();
    }

    /**
     * Obtiene comentarios que requieren devolución
     */
    public function getComentariosDevuelve()
    {
        return $this->where('comentario_devuelve', 1)
                    ->orderBy('comentario_id', 'DESC')
                    ->findAll();
    }

    /**
     * Obtiene comentarios que sugieren eliminación
     */
    public function getComentariosElimina()
    {
        return $this->where('comentario_elimina', 1)
                    ->orderBy('comentario_id', 'DESC')
                    ->findAll();
    }

    /**
     * Obtiene comentarios con envío de correo activado
     */
    public function getComentariosConCorreo()
    {
        return $this->where('comentario_envia_correo', 1)
                    ->orderBy('comentario_id', 'DESC')
                    ->findAll();
    }

    /**
     * Búsqueda de comentarios con filtros
     */
    public function searchComentarios($filters = [])
    {
        $builder = $this;

        if (!empty($filters['cia_id'])) {
            $builder = $builder->where('cia_id', (int) $filters['cia_id']);
        }

        if (!empty($filters['q'])) {
            $builder = $builder->groupStart()
                ->like('comentario_nombre', $filters['q'])
                ->orLike('comentario_id_cia_interno', $filters['q'])
            ->groupEnd();
        }

        if (isset($filters['devuelve']) && $filters['devuelve'] !== '') {
            $builder = $builder->where('comentario_devuelve', (int) $filters['devuelve']);
        }

        if (isset($filters['elimina']) && $filters['elimina'] !== '') {
            $builder = $builder->where('comentario_elimina', (int) $filters['elimina']);
        }

        if (isset($filters['envia_correo']) && $filters['envia_correo'] !== '') {
            $builder = $builder->where('comentario_envia_correo', (int) $filters['envia_correo']);
        }

        return $builder->orderBy('comentario_id', 'DESC');
    }

    /**
     * Estadísticas de comentarios
     */
    public function getEstadisticas()
    {
        return [
            'total'         => $this->countAllResults(false),
            'con_devuelve'  => $this->where('comentario_devuelve', 1)->countAllResults(false),
            'con_elimina'   => $this->where('comentario_elimina', 1)->countAllResults(false),
            'con_correo'    => $this->where('comentario_envia_correo', 1)->countAllResults(false),
        ];
    }
}