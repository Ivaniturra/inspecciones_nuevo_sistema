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
                'comment'    => 'Tipo de usuario: general, inspector, supervisor, etc.',
            ],
            'valor' => [
                'type'       => 'DECIMAL',
                'constraint' => '10,2',
                'null'       => false,
                'default'    => '0.00',
                'comment'    => 'Valor en pesos chilenos',
            ],
            'moneda' => [
                'type'       => 'VARCHAR',
                'constraint' => '3',
                'null'       => false,
                'default'    => 'CLP',
                'comment'    => 'Código de moneda: CLP, USD, etc.',
            ],
            'descripcion' => [
                'type'       => 'TEXT',
                'null'       => true,
                'comment'    => 'Descripción del valor o concepto',
            ],
            'fecha_vigencia_desde' => [
                'type'    => 'DATE',
                'null'    => false,
                'default' => '2025-01-01',
                'comment' => 'Fecha desde cuando es válido este valor',
            ],
            'fecha_vigencia_hasta' => [
                'type'    => 'DATE',
                'null'    => true,
                'comment' => 'Fecha hasta cuando es válido (NULL = indefinido)',
            ],
            'activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
                'comment'    => '1 = activo, 0 = inactivo',
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
        
        // Índices para mejorar rendimiento
        $this->forge->addKey(['comuna_codigo', 'cia_id', 'tipo_usuario'], false, 'idx_comuna_cia_tipo');
        $this->forge->addKey(['cia_id'], false, 'idx_cia');
        $this->forge->addKey(['comuna_codigo'], false, 'idx_comuna');
        $this->forge->addKey(['activo'], false, 'idx_activo');
        $this->forge->addKey(['fecha_vigencia_desde', 'fecha_vigencia_hasta'], false, 'idx_vigencia');

        // Foreign keys
        $this->forge->addForeignKey('cia_id', 'cias', 'cia_id', 'CASCADE', 'CASCADE');
        // Nota: comuna_codigo se relaciona con comunas.comuna_codigo, pero como es VARCHAR, 
        // CodeIgniter podría tener problemas. Si quieres FK, considera cambiar a comuna_id (INT)

        $this->forge->createTable('valores_comunas');

        // Insertar algunos valores de ejemplo
        $this->db->table('valores_comunas')->insertBatch([
            // Ejemplo: Compañía 1, diferentes tipos de usuarios
            [
                'comuna_codigo' => '13101', // Santiago
                'cia_id' => 1,
                'tipo_usuario' => 'general',
                'valor' => 50000.00,
                'moneda' => 'CLP',
                'descripcion' => 'Valor base para usuarios generales en Santiago',
                'fecha_vigencia_desde' => '2025-01-01',
                'fecha_vigencia_hasta' => null,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'comuna_codigo' => '13101', // Santiago
                'cia_id' => 1,
                'tipo_usuario' => 'inspector',
                'valor' => 75000.00,
                'moneda' => 'CLP',
                'descripcion' => 'Valor especial para inspectores en Santiago',
                'fecha_vigencia_desde' => '2025-01-01',
                'fecha_vigencia_hasta' => null,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'comuna_codigo' => '13101', // Santiago
                'cia_id' => 1,
                'tipo_usuario' => 'supervisor',
                'valor' => 100000.00,
                'moneda' => 'CLP',
                'descripcion' => 'Valor para supervisores en Santiago',
                'fecha_vigencia_desde' => '2025-01-01',
                'fecha_vigencia_hasta' => null,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],

            // Misma compañía, otra comuna
            [
                'comuna_codigo' => '13114', // Las Condes
                'cia_id' => 1,
                'tipo_usuario' => 'general',
                'valor' => 60000.00,
                'moneda' => 'CLP',
                'descripcion' => 'Valor base para usuarios generales en Las Condes',
                'fecha_vigencia_desde' => '2025-01-01',
                'fecha_vigencia_hasta' => null,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],
            [
                'comuna_codigo' => '13114', // Las Condes
                'cia_id' => 1,
                'tipo_usuario' => 'inspector',
                'valor' => 85000.00,
                'moneda' => 'CLP',
                'descripcion' => 'Valor especial para inspectores en Las Condes',
                'fecha_vigencia_desde' => '2025-01-01',
                'fecha_vigencia_hasta' => null,
                'activo' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ],

            // Otra compañía, misma comuna
            [
                'comuna_codigo' => '13101', // Santiago
                'cia_id' => 2, // Asumiendo que existe cia_id = 2
                'tipo_usuario' => 'general',
                'valor' => 45000.00,
                'moneda' => 'CLP',
                'descripcion' => 'Valor de otra compañía para usuarios generales en Santiago',
                'fecha_vigencia_desde' => '2025-01-01',
                'fecha_vigencia_hasta' => null,
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