<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePerfilesTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('perfiles')) {
            return;
        }

        $this->forge->addField([
            'perfil_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'perfil_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => false,
            ],
            'perfil_tipo' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => false,
            ],
            'perfil_descripcion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'perfil_permisos' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'perfil_nivel' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 1,
                'null'       => false,
            ],
            'perfil_habil' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        // Clave primaria
        $this->forge->addKey('perfil_id', true);
        
        // Índices
        $this->forge->addKey('perfil_tipo');
        $this->forge->addKey('perfil_nivel');
        $this->forge->addKey('perfil_habil');
        
        // Índice único para nombre del perfil
        $this->forge->addUniqueKey('perfil_nombre');
        
        // Crear la tabla
        $this->forge->createTable('perfiles', true, [
            'ENGINE' => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('perfiles', true);
    }
}