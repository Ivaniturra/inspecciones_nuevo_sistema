<?php

namespace App\Models;

use CodeIgniter\Model;

class ComentarioModel extends Model
{
    protected $table            = 'comentarios';
    protected $primaryKey       = 'comentario_id';
    protected $returnType       = 'array';

    // Si tu tabla tiene deleted_at, created_at, updated_at:
    protected $useSoftDeletes   = true;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $allowedFields = [
        'comentario_nombre',
        'comentario_id_cia_interno',
        'cia_id',
        'comentario_devuelve',
        'comentario_elimina',
        'comentario_envia_correo',
    ];

    // Reglas de validación (ajusta longitudes a tu esquema real)
    protected $validationRules = [
        'comentario_nombre'         => 'required|min_length[2]|max_length[5000]',
        'cia_id'                    => 'required|is_natural_no_zero',
        'comentario_id_cia_interno' => 'permit_empty|is_natural',
        'comentario_devuelve'       => 'permit_empty|in_list[0,1]',
        'comentario_elimina'        => 'permit_empty|in_list[0,1]',
        'comentario_envia_correo'   => 'permit_empty|in_list[0,1]',
    ];
    protected $cleanValidationRules = true;

    // Normalizaciones útiles antes de guardar
    protected $beforeInsert = ['normalizeFlags', 'trimStrings'];
    protected $beforeUpdate = ['normalizeFlags', 'trimStrings'];

    protected function normalizeFlags(array $data): array
    {
        if (!isset($data['data'])) return $data;
        foreach (['comentario_devuelve','comentario_elimina','comentario_envia_correo'] as $f) {
            if (array_key_exists($f, $data['data'])) {
                $data['data'][$f] = (int) ((bool) $data['data'][$f]);
            }
        }
        return $data;
    }

    protected function trimStrings(array $data): array
    {
        if (!isset($data['data'])) return $data;
        if (isset($data['data']['comentario_nombre']) && is_string($data['data']['comentario_nombre'])) {
            $data['data']['comentario_nombre'] = trim($data['data']['comentario_nombre']);
        }
        return $data;
    }
}