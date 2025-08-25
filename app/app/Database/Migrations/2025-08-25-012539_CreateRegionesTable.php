<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRegionesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'region_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'region_codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => false,
            ],
            'region_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'region_numero' => [
                'type'       => 'INT',
                'constraint' => 2,
                'unsigned'   => true,
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

        $this->forge->addKey('region_id', true);
        $this->forge->addUniqueKey('region_codigo');
        $this->forge->addUniqueKey('region_numero');
        $this->forge->createTable('regiones');

        // Insertar las 16 regiones de Chile
        $this->db->table('regiones')->insertBatch([
            ['region_codigo' => 'XV', 'region_nombre' => 'Arica y Parinacota', 'region_numero' => 15, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'I', 'region_nombre' => 'Tarapacá', 'region_numero' => 1, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'II', 'region_nombre' => 'Antofagasta', 'region_numero' => 2, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'III', 'region_nombre' => 'Atacama', 'region_numero' => 3, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'IV', 'region_nombre' => 'Coquimbo', 'region_numero' => 4, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'V', 'region_nombre' => 'Valparaíso', 'region_numero' => 5, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'VI', 'region_nombre' => 'O\'Higgins', 'region_numero' => 6, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'VII', 'region_nombre' => 'Maule', 'region_numero' => 7, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'XVI', 'region_nombre' => 'Ñuble', 'region_numero' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'VIII', 'region_nombre' => 'Biobío', 'region_numero' => 8, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'IX', 'region_nombre' => 'La Araucanía', 'region_numero' => 9, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'XIV', 'region_nombre' => 'Los Ríos', 'region_numero' => 14, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'X', 'region_nombre' => 'Los Lagos', 'region_numero' => 10, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'XI', 'region_nombre' => 'Aysén', 'region_numero' => 11, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'XII', 'region_nombre' => 'Magallanes y Antártica Chilena', 'region_numero' => 12, 'created_at' => date('Y-m-d H:i:s')],
            ['region_codigo' => 'RM', 'region_nombre' => 'Metropolitana de Santiago', 'region_numero' => 13, 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('regiones');
    }
}