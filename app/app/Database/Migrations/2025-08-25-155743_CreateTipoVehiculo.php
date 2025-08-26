<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTipoVehiculo extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'clave' => [ // código estable para usar en selects: liviano, pesado, etc.
                'type'       => 'VARCHAR',
                'constraint' => 50,
            ],
            'nombre' => [ // label visible
                'type'       => 'VARCHAR',
                'constraint' => 100,
            ],
            'descripcion' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
            'activo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'null'       => false,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true], // si luego usas SoftDeletes
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('clave');
        $this->forge->addKey('activo');

        // Atributos de tabla para MySQL (Docker)
        $attributes = [
            'ENGINE'  => 'InnoDB',
            'DEFAULT CHARSET' => 'utf8mb4',
            'COLLATE' => 'utf8mb4_unicode_ci',
        ];

        $this->forge->createTable('tipo_vehiculo', true, $attributes);

        // (Opcional) Semillas mínimas dentro de la migración:
        $now = date('Y-m-d H:i:s');
        $data = [
            ['clave' => 'liviano',            'nombre' => 'Liviano',            'descripcion' => null, 'activo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['clave' => 'pesado',             'nombre' => 'Pesado',             'descripcion' => null, 'activo' => 1, 'created_at' => $now, 'updated_at' => $now],
            ['clave' => 'motocicleta',        'nombre' => 'Motocicleta',        'descripcion' => null, 'activo' => 1, 'created_at' => $now, 'updated_at' => $now], 
        ];
        $this->db->table('tipo_vehiculo')->insertBatch($data);
    }

    public function down()
    {
        $this->forge->dropTable('tipo_vehiculo', true);
    }
}
