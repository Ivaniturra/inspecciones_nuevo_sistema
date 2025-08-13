<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCiasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cia_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cia_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
            ],
            'cia_logo' => [
                'type'       => 'VARCHAR',
                'constraint' => 255,
                'null'       => true,
            ],
            'cia_direccion' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'cia_habil' => [
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
        
        $this->forge->addPrimaryKey('cia_id');
        $this->forge->addKey('cia_habil');
        $this->forge->createTable('cias');
    }

    public function down()
    {
        $this->forge->dropTable('cias');
    }
}