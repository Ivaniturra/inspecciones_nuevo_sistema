<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoVehiculoIdToValoresComunas extends Migration
{
    public function up()
    {
        // 0) Asegura tabla catálogo + 'liviano'
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
        } else {
            try { $this->db->query("ALTER TABLE tipo_vehiculo CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci"); } catch (\Throwable $e) {}
        }

        // Inserta 'liviano' si no existe
        $livianoId = $this->db->query("SELECT id FROM tipo_vehiculo WHERE clave='liviano'")->getRow('id');
        if (!$livianoId) {
            $this->db->table('tipo_vehiculo')->insert(['clave' => 'liviano', 'nombre' => 'Liviano', 'activo' => 1]);
            $livianoId = $this->db->insertID();
        }

        // 1) Añade columna tipo_vehiculo_id si no existe
        $fieldsVc = $this->db->getFieldNames('valores_comunas');
        $hasLegacy = in_array('tipo_vehiculo', $fieldsVc, true);

        if (!in_array('tipo_vehiculo_id', $fieldsVc, true)) {
            $this->forge->addColumn('valores_comunas', [
                'tipo_vehiculo_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true, // primero NULL para rellenar
                    'after'      => 'tipo_usuario',
                ],
            ]);
            $this->db->query('ALTER TABLE `valores_comunas` ADD INDEX (`tipo_vehiculo_id`)');
        }

        // 2) SOLO si existe la columna legacy, intenta backfill por clave (con collation forzada)
        if ($hasLegacy) {
            try {
                $this->db->query("
                    ALTER TABLE valores_comunas
                    MODIFY COLUMN tipo_vehiculo VARCHAR(50)
                    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                ");
            } catch (\Throwable $e) {}

            $this->db->query("
                UPDATE valores_comunas vc
                JOIN tipo_vehiculo tv
                  ON CONVERT(tv.clave USING utf8mb4) COLLATE utf8mb4_unicode_ci
                   = CONVERT(vc.tipo_vehiculo USING utf8mb4) COLLATE utf8mb4_unicode_ci
                SET vc.tipo_vehiculo_id = tv.id
                WHERE vc.tipo_vehiculo_id IS NULL
            ");
        }

        // 3) Fallback: lo que siga NULL -> liviano
        $this->db->query("
            UPDATE valores_comunas
            SET tipo_vehiculo_id = {$livianoId}
            WHERE tipo_vehiculo_id IS NULL
        ");

        // 4) Endurecer NOT NULL
        try {
            $this->db->query("
                ALTER TABLE `valores_comunas`
                MODIFY `tipo_vehiculo_id` INT(11) UNSIGNED NOT NULL
            ");
        } catch (\Throwable $e) {
            // si falla, quedaron nulos: deja NULL y revisa datos
        }

        // 5) FK (si no existe)
        try {
            $this->db->query("
                ALTER TABLE `valores_comunas`
                ADD CONSTRAINT `fk_valores_comunas_tipo_vehiculo`
                FOREIGN KEY (`tipo_vehiculo_id`) REFERENCES `tipo_vehiculo`(`id`)
                ON DELETE RESTRICT ON UPDATE CASCADE
            ");
        } catch (\Throwable $e) {
            // ignora si ya existe
        }

        // 6) (Opcional) eliminar columna legacy si aún existe
        if ($hasLegacy) {
            try { $this->forge->dropColumn('valores_comunas', 'tipo_vehiculo'); } catch (\Throwable $e) {}
        }
    }

    public function down()
    {
        try { $this->db->query("ALTER TABLE `valores_comunas` DROP FOREIGN KEY `fk_valores_comunas_tipo_vehiculo`"); } catch (\Throwable $e) {}
        try { $this->forge->dropColumn('valores_comunas', 'tipo_vehiculo_id'); } catch (\Throwable $e) {}
    }
}
