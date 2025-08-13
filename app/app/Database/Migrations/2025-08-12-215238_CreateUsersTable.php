<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'user_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'user_perfil' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'cia_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
            ],
            'user_clave' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'user_habil' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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
        
        $this->forge->addPrimaryKey('user_id');
        $this->forge->addKey('cia_id');
        $this->forge->addKey('user_perfil');
        $this->forge->addKey('user_habil');
        
        // Crear índices para optimizar consultas
        $this->forge->addKey(['cia_id', 'user_habil']);
        
        $this->forge->createTable('users');
        
        // Agregar claves foráneas (opcional, dependiendo de tu motor de BD)
        /*
        $this->forge->addForeignKey('cia_id', 'cias', 'cia_id', 'CASCADE', 'RESTRICT');
        $this->forge->addForeignKey('user_perfil', 'perfiles', 'perfil_id', 'CASCADE', 'RESTRICT');
        */
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}