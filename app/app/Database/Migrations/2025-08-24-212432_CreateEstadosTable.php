<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateEstadosTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'estado_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'estado_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);

        $this->forge->addKey('estado_id', true);
        $this->forge->createTable('estados');

        // Insertar algunos estados de ejemplo
        $this->db->table('estados')->insertBatch([
            ['estado_nombre' => 'Solicitud', 'created_at' => date('Y-m-d H:i:s')],
            ['estado_nombre' => 'Coordinador', 'created_at' => date('Y-m-d H:i:s')],
            ['estado_nombre' => 'Es Control de Calidad', 'created_at' => date('Y-m-d H:i:s')],
            ['estado_nombre' => 'En Inspector', 'created_at' => date('Y-m-d H:i:s')],
            ['estado_nombre' => 'Terminada', 'created_at' => date('Y-m-d H:i:s')],
             ['estado_nombre' => 'Aceptada', 'created_at' => date('Y-m-d H:i:s')],
              ['estado_nombre' => 'Rechazada', 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('estados');
    }
}