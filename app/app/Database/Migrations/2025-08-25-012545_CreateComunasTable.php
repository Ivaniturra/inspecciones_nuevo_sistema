<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComunasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'comuna_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'comuna_codigo' => [
                'type'       => 'VARCHAR',
                'constraint' => '10',
                'null'       => false,
            ],
            'comuna_nombre' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
                'null'       => false,
            ],
            'region_id' => [
                'type'       => 'INT',
                'constraint' => 11,
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

        $this->forge->addKey('comuna_id', true);
        $this->forge->addUniqueKey('comuna_codigo');
        $this->forge->addForeignKey('region_id', 'regiones', 'region_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('comunas');

        // Insertar algunas comunas de ejemplo (principales de RM y otras regiones)
        $this->db->table('comunas')->insertBatch([
            // Región Metropolitana (RM - region_id: 16)
            ['comuna_codigo' => '13101', 'comuna_nombre' => 'Santiago', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13102', 'comuna_nombre' => 'Cerrillos', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13103', 'comuna_nombre' => 'Cerro Navia', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13104', 'comuna_nombre' => 'Conchalí', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13105', 'comuna_nombre' => 'El Bosque', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13106', 'comuna_nombre' => 'Estación Central', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13107', 'comuna_nombre' => 'Huechuraba', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13108', 'comuna_nombre' => 'Independencia', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13109', 'comuna_nombre' => 'La Cisterna', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13110', 'comuna_nombre' => 'La Florida', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13111', 'comuna_nombre' => 'La Granja', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13112', 'comuna_nombre' => 'La Pintana', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13113', 'comuna_nombre' => 'La Reina', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13114', 'comuna_nombre' => 'Las Condes', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13115', 'comuna_nombre' => 'Lo Barnechea', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13116', 'comuna_nombre' => 'Lo Espejo', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13117', 'comuna_nombre' => 'Lo Prado', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13118', 'comuna_nombre' => 'Macul', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13119', 'comuna_nombre' => 'Maipú', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13120', 'comuna_nombre' => 'Ñuñoa', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13121', 'comuna_nombre' => 'Pedro Aguirre Cerda', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13122', 'comuna_nombre' => 'Peñalolén', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13123', 'comuna_nombre' => 'Providencia', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13124', 'comuna_nombre' => 'Pudahuel', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13125', 'comuna_nombre' => 'Quilicura', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13126', 'comuna_nombre' => 'Quinta Normal', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13127', 'comuna_nombre' => 'Recoleta', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13128', 'comuna_nombre' => 'Renca', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13129', 'comuna_nombre' => 'San Joaquín', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13130', 'comuna_nombre' => 'San Miguel', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13131', 'comuna_nombre' => 'San Ramón', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '13132', 'comuna_nombre' => 'Vitacura', 'region_id' => 16, 'created_at' => date('Y-m-d H:i:s')],

            // Región de Valparaíso (V - region_id: 6)
            ['comuna_codigo' => '05101', 'comuna_nombre' => 'Valparaíso', 'region_id' => 6, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '05109', 'comuna_nombre' => 'Viña del Mar', 'region_id' => 6, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '05102', 'comuna_nombre' => 'Casablanca', 'region_id' => 6, 'created_at' => date('Y-m-d H:i:s')],

            // Región del Biobío (VIII - region_id: 10)
            ['comuna_codigo' => '08101', 'comuna_nombre' => 'Concepción', 'region_id' => 10, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '08108', 'comuna_nombre' => 'Talcahuano', 'region_id' => 10, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '08112', 'comuna_nombre' => 'Los Ángeles', 'region_id' => 10, 'created_at' => date('Y-m-d H:i:s')],

            // Región de Antofagasta (II - region_id: 3)
            ['comuna_codigo' => '02101', 'comuna_nombre' => 'Antofagasta', 'region_id' => 3, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '02104', 'comuna_nombre' => 'Calama', 'region_id' => 3, 'created_at' => date('Y-m-d H:i:s')],

            // Región de La Araucanía (IX - region_id: 11)
            ['comuna_codigo' => '09101', 'comuna_nombre' => 'Temuco', 'region_id' => 11, 'created_at' => date('Y-m-d H:i:s')],
            ['comuna_codigo' => '09120', 'comuna_nombre' => 'Villarrica', 'region_id' => 11, 'created_at' => date('Y-m-d H:i:s')],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('comunas');
    }
}