<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoVehiculoToValoresComunas extends Migration
{
    public function up()
    {
        // 1) Agregar columna
        $this->forge->addColumn('valores_comunas', [
            'tipo_vehiculo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
                'after'      => 'tipo_usuario', // opcional
            ],
        ]);

        // 2) Índices / FK
        $this->forge->addKey('tipo_vehiculo_id');
        $this->db->query("
            ALTER TABLE `valores_comunas`
            ADD CONSTRAINT `fk_valores_comunas_tipo_vehiculo`
            FOREIGN KEY (`tipo_vehiculo_id`) REFERENCES `tipo_vehiculo`(`id`)
            ON DELETE RESTRICT ON UPDATE CASCADE
        ");
    }

    public function down()
    {
        // Quitar FK y columna
        $this->db->query("ALTER TABLE `valores_comunas` DROP FOREIGN KEY `fk_valores_comunas_tipo_vehiculo`");
        $this->forge->dropColumn('valores_comunas', 'tipo_vehiculo_id');
    }
}
