<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateValoresComunasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'valores_id' => [
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
            'cia_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'tipo_usuario' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
                'default'    => 'general',
            ],
            'tipo_vehiculo' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => false,
                'default'    => 'liviano',
            ],
            'unidad_medida' => [
                'type'       => 'VARCHAR',
                'constraint' => '20',
                'null'       => false,
                'default'    => 'CLP',
            ],
            'valor' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => '0.00',
            ],
            'moneda' => [
                'type'       => 'VARCHAR',
                'constraint' => '3',
                'null'       => false,
                'default'    => 'CLP',
            ],
            'descripcion' => [
                'type'       => 'TEXT',
                'null'       => true,
            ],
            'fecha_vigencia_desde' => [
                'type'    => 'DATE',
                'null'    => false,
                'default' => '2025-01-01',
            ],
            'fecha_vigencia_hasta' => [
                'type'    => 'DATE',
                'null'    => true,
            ],
            'activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
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

        $this->forge->addKey('valores_id', true);
        $this->forge->addForeignKey('cia_id', 'cias', 'cia_id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('valores_comunas');

        // Usar $this->db directamente, NO modelos
        $this->db->table('valores_comunas')->insertBatch([
            [
                'comuna_codigo' => '13101',
                'cia_id' => 1,
                'tipo_usuario' => 'inspector',
                'tipo_vehiculo' => 'liviano',
                'unidad_medida' => 'UF',
                'valor' => 1.5,
                'moneda' => 'UF',
                'descripcion' => 'Inspección vehículo liviano - Inspector',
                'fecha_vigencia_desde' => '2025-01-01',
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'comuna_codigo' => '13101',
                'cia_id' => 1,
                'tipo_usuario' => 'compania',
                'tipo_vehiculo' => 'liviano',
                'unidad_medida' => 'UF',
                'valor' => 2.0,
                'moneda' => 'UF',
                'descripcion' => 'Inspección vehículo liviano - Compañía',
                'fecha_vigencia_desde' => '2025-01-01',
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropTable('valores_comunas');
    }
}