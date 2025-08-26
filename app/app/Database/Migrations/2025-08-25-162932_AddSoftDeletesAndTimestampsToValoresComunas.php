<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSoftDeletesAndTimestampsToValoresComunas extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('valores_comunas');

        // Agrega created_at y updated_at si no existen
        if (!in_array('created_at', $fields, true)) {
            $this->forge->addColumn('valores_comunas', [
                'created_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'activo'],
            ]);
        }
        if (!in_array('updated_at', $fields, true)) {
            $this->forge->addColumn('valores_comunas', [
                'updated_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'created_at'],
            ]);
        }
        // Agrega deleted_at si no existe (para SoftDeletes)
        if (!in_array('deleted_at', $fields, true)) {
            $this->forge->addColumn('valores_comunas', [
                'deleted_at' => ['type' => 'DATETIME', 'null' => true, 'after' => 'updated_at'],
            ]);
        }
    }

    public function down()
    {
        // Quita solo si existen (por seguridad)
        $fields = $this->db->getFieldNames('valores_comunas');
        if (in_array('deleted_at', $fields, true)) {
            $this->forge->dropColumn('valores_comunas', 'deleted_at');
        }
        if (in_array('updated_at', $fields, true)) {
            $this->forge->dropColumn('valores_comunas', 'updated_at');
        }
        if (in_array('created_at', $fields, true)) {
            $this->forge->dropColumn('valores_comunas', 'created_at');
        }
    }
}
