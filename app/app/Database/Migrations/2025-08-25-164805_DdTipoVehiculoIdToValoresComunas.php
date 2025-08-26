<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoVehiculoIdToValoresComunas extends Migration
{
    public function up()
    {
        // Asegura tabla catalogo y su collation (opcional)
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
            $this->db->table('tipo_vehiculo')->ignore(true)->insertBatch([
                ['clave' => 'liviano',            'nombre' => 'Liviano',            'activo' => 1],
                ['clave' => 'pesado',             'nombre' => 'Pesado',             'activo' => 1],
                ['clave' => 'motocicleta',        'nombre' => 'Motocicleta',        'activo' => 1],
                ['clave' => 'transporte_publico', 'nombre' => 'Transporte Público', 'activo' => 1],
            ]);
        } else {
            try {
                $this->db->query("ALTER TABLE tipo_vehiculo CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Throwable $e) {}
        }

        // Añade la columna si no existe
        $fields = $this->db->getFieldNames('valores_comunas');
        $hadLegacy = in_array('tipo_vehiculo', $fields, true);

        if (!in_array('tipo_vehiculo_id', $fields, true)) {
            $this->forge->addColumn('valores_comunas', [
                'tipo_vehiculo_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true, // primero NULL para poder rellenar
                    'after'      => 'tipo_usuario',
                ],
            ]);
            $this->db->query('ALTER TABLE `valores_comunas` ADD INDEX (`tipo_vehiculo_id`)');
        }

        // Alinea collation del campo legacy si existe
        if ($hadLegacy) {
            try {
                $this->db->query("
                    ALTER TABLE valores_comunas
                    MODIFY COLUMN tipo_vehiculo VARCHAR(50)
                    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                ");
            } catch (\Throwable $e) {}
        }

        // BACKFILL con collation forzada (esta línea era la que fallaba)
        if ($hadLegacy) {
            $this->db->query("
                UPDATE valores_comunas vc
                JOIN tipo_vehiculo tv
                  ON CONVERT(tv.clave USING utf8mb4) COLLATE utf8mb4_unicode_ci
                   = CONVERT(vc.tipo_vehiculo USING utf8mb4) COLLATE utf8mb4_unicode_ci
                SET vc.tipo_vehiculo_id = tv.id
                WHERE vc.tipo_vehiculo IS NOT NULL
                  AND vc.tipo_vehiculo_id IS NULL
            ");
        }

        // Fallback: asigna 'liviano' a lo que quede nulo
        $this->db->query("
            UPDATE valores_comunas vc
            JOIN tipo_vehiculo tv ON tv.clave = 'liviano'
            SET vc.tipo_vehiculo_id = tv.id
            WHERE vc.tipo_vehiculo_id IS NULL
        ");

        // Vuelve NOT NULL
        try {
            $this->db->query("
                ALTER TABLE `valores_comunas`
                MODIFY `tipo_vehiculo_id` INT(11) UNSIGNED NOT NULL
            ");
        } catch (\Throwable $e) {}

        // FK (si no existe)
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
        try { $this->db->query("ALTER TABLE `valores_comunas` DROP FOREIGN KEY `fk_valores_comunas_tipo_vehiculo`"); } catch (\Throwable $e) {}
        try { $this->forge->dropColumn('valores_comunas', 'tipo_vehiculo_id'); } catch (\Throwable $e) {}
    }
}
