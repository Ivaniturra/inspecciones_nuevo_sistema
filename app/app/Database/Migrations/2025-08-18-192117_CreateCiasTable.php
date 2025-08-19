<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateCiasTable extends Migration
{
    public function up()
    {
        if ($this->db->tableExists('cias')) {
            return;
        }

        $this->forge->addField([
            'cia_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'cia_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'cia_logo' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
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
            'display_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '150',
                'null'       => true,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'brand_nav_bg' => [
                'type'       => 'VARCHAR',
                'constraint' => '7',
                'null'       => true,
                'comment'    => 'Color de fondo del navbar en formato hex'
            ],
            'brand_nav_text' => [
                'type'       => 'VARCHAR',
                'constraint' => '7',
                'null'       => true,
                'comment'    => 'Color del texto del navbar en formato hex'
            ],
            'brand_side_start' => [
                'type'       => 'VARCHAR',
                'constraint' => '7',
                'null'       => true,
                'comment'    => 'Color inicial del gradiente sidebar en formato hex'
            ],
            'brand_side_end' => [
                'type'       => 'VARCHAR',
                'constraint' => '7',
                'null'       => true,
                'comment'    => 'Color final del gradiente sidebar en formato hex'
            ],
            'logo_path' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => true,
                'comment'    => 'Ruta del archivo de logo'
            ],
        ]);

        // Clave primaria
        $this->forge->addKey('cia_id', true);
        
        // Índices
        $this->forge->addKey('cia_habil');
        $this->forge->addKey('slug');
        
        // Índices únicos
        $this->forge->addUniqueKey('slug');
        
        // Crear la tabla
        $this->forge->createTable('cias', true, [
            'ENGINE' => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_general_ci'
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('cias', true);
    }
}