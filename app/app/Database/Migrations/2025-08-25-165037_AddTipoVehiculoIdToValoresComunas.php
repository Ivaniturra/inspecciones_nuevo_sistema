<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoVehiculoIdToValoresComunas extends Migration
{
    public function up()
{ 
    // Asegura catálogo y 'liviano'
    if (! $this->db->tableExists('tipo_vehiculo')) {
        $this->db->query("
            CREATE TABLE IF NOT EXISTS tipo_vehiculo (
                id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                clave VARCHAR(50) UNIQUE,
                nombre VARCHAR(100),
                descripcion VARCHAR(255) NULL,
                activo TINYINT(1) NOT NULL DEFAULT 1
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");
    }
    $livianoId = $this->db->query("SELECT id FROM tipo_vehiculo WHERE clave='liviano'")->getRow('id');
    if (!$livianoId) {
        $this->db->table('tipo_vehiculo')->insert(['clave' => 'liviano', 'nombre' => 'Liviano', 'activo' => 1]);
        $livianoId = $this->db->insertID();
    }

    // Añade columna si no existe (primero NULL)
    $fields = $this->db->getFieldNames('valores_comunas');
    if (!in_array('tipo_vehiculo_id', $fields, true)) {
        $this->forge->addColumn('valores_comunas', [
            'tipo_vehiculo_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
                'after'      => 'tipo_usuario',
            ],
        ]);
        $this->db->query('ALTER TABLE `valores_comunas` ADD INDEX (`tipo_vehiculo_id`)');
    }

    // ?? IMPORTANTE: NO usar vc.tipo_vehiculo (puede no existir). Eliminamos ese backfill.

    // Fallback: todo lo NULL ? liviano
    $this->db->query("
        UPDATE valores_comunas
        SET tipo_vehiculo_id = {$livianoId}
        WHERE tipo_vehiculo_id IS NULL
    ");

    // NOT NULL + FK (si no existen)
    try {
        $this->db->query("ALTER TABLE `valores_comunas` MODIFY `tipo_vehiculo_id` INT(11) UNSIGNED NOT NULL");
    } catch (\Throwable $e) {}

    try {
        $this->db->query("
            ALTER TABLE `valores_comunas`
            ADD CONSTRAINT `fk_valores_comunas_tipo_vehiculo`
            FOREIGN KEY (`tipo_vehiculo_id`) REFERENCES `tipo_vehiculo`(`id`)
            ON DELETE RESTRICT ON UPDATE CASCADE
        ");
    } catch (\Throwable $e) {}
} 

    public function down()
    {
        //
    }
}
