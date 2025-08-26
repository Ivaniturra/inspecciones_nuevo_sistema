<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoVehiculoIdCollationFix extends Migration
{
    public function up()
    {
        // 0) Asegura tabla tipo_vehiculo (por si no existe en entornos nuevos)
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
            // Alinea collation de la tabla catálogo (opcional pero recomendado)
            try {
                $this->db->query("ALTER TABLE tipo_vehiculo CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
            } catch (\Throwable $e) {
                // ignora si no puede convertir (privilegios, etc.)
            }
        }

        // 1) Añade columna tipo_vehiculo_id a valores_comunas si no existe
        $fieldsVc = $this->db->getFieldNames('valores_comunas');
        $hadLegacyTipoVehiculo = in_array('tipo_vehiculo', $fieldsVc, true);

        if (!in_array('tipo_vehiculo_id', $fieldsVc, true)) {
            $this->forge->addColumn('valores_comunas', [
                'tipo_vehiculo_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true, // primero lo dejamos NULL para poder rellenar
                    'after'      => 'tipo_usuario',
                ],
            ]);
            $this->db->query('ALTER TABLE `valores_comunas` ADD INDEX (`tipo_vehiculo_id`)');
        }

        // 2) Si existe la columna antigua string, alinea su collation
        if ($hadLegacyTipoVehiculo) {
            try {
                $this->db->query("
                    ALTER TABLE valores_comunas
                    MODIFY COLUMN tipo_vehiculo VARCHAR(50)
                    CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
                ");
            } catch (\Throwable $e) {
                // ignora si ya tiene ese tipo/collation
            }
        }

        // 3) Backfill con JOIN forzando misma collation (evita 'Illegal mix of collations')
        if ($hadLegacyTipoVehiculo) {
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

        // 4) Fallback: lo que siga nulo, asigna 'liviano'
        $this->db->query("
            UPDATE valores_comunas vc
            JOIN tipo_vehiculo tv ON tv.clave = 'liviano'
            SET vc.tipo_vehiculo_id = tv.id
            WHERE vc.tipo_vehiculo_id IS NULL
        ");

        // 5) Vuelve NOT NULL
        try {
            $this->db->query("
                ALTER TABLE `valores_comunas`
                MODIFY `tipo_vehiculo_id` INT(11) UNSIGNED NOT NULL
            ");
        } catch (\Throwable $e) {
            // Si falla, probablemente quedaron nulos: revisa datos y vuelve a correr.
        }

        // 6) Agrega FK (si no existe)
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

        // 7) (Opcional) elimina columna legacy string
        if ($hadLegacyTipoVehiculo) {
            try {
                $this->forge->dropColumn('valores_comunas', 'tipo_vehiculo');
            } catch (\Throwable $e) {
                // ignora si no puede
            }
        }
    }

    public function down()
    {
        // Quita FK y columna
        try {
            $this->db->query("ALTER TABLE `valores_comunas` DROP FOREIGN KEY `fk_valores_comunas_tipo_vehiculo`");
        } catch (\Throwable $e) {}
        try {
            $this->forge->dropColumn('valores_comunas', 'tipo_vehiculo_id');
        } catch (\Throwable $e) {}
    }
}
