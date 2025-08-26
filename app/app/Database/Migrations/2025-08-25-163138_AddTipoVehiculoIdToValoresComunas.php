<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddTipoVehiculoIdToValoresComunas extends Migration
{
    public function up()
    {
        // Lee campos existentes
        $fieldsVc = $this->db->getFieldNames('valores_comunas');

        // 1) Agrega columna si no existe (temporalmente NULL para poder rellenar)
        if (!in_array('tipo_vehiculo_id', $fieldsVc, true)) {
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

        // 2) Backfill si existe una columna antigua tipo_vehiculo (string/clave)
        $fieldsVc = $this->db->getFieldNames('valores_comunas');
        $hasOld   = in_array('tipo_vehiculo', $fieldsVc, true); // ej. guardabas 'liviano','pesado',...

        if ($this->db->tableExists('tipo_vehiculo')) {
            if ($hasOld) {
                // Mapear por clave
                $this->db->query("
                    UPDATE valores_comunas vc
                    JOIN tipo_vehiculo tv ON tv.clave = vc.tipo_vehiculo
                    SET vc.tipo_vehiculo_id = tv.id
                ");
            }

            // Fallback: cualquier nulo lo asignamos a 'liviano' (ajústalo si prefieres otro)
            $this->db->query("
                UPDATE valores_comunas vc
                JOIN tipo_vehiculo tv ON tv.clave = 'liviano'
                SET vc.tipo_vehiculo_id = tv.id
                WHERE vc.tipo_vehiculo_id IS NULL
            ");
        } else {
            // Si aún no existe la tabla tipo_vehiculo, crea al menos un valor por defecto
            $this->db->query("
                CREATE TABLE IF NOT EXISTS tipo_vehiculo (
                    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
                    clave VARCHAR(50) UNIQUE,
                    nombre VARCHAR(100),
                    descripcion VARCHAR(255) NULL,
                    activo TINYINT(1) NOT NULL DEFAULT 1
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ");

            // Semillas mínimas
            $this->db->table('tipo_vehiculo')->ignore(true)->insertBatch([
                ['clave' => 'liviano',            'nombre' => 'Liviano',            'activo' => 1],
                ['clave' => 'pesado',             'nombre' => 'Pesado',             'activo' => 1],
                ['clave' => 'motocicleta',        'nombre' => 'Motocicleta',        'activo' => 1],
                ['clave' => 'transporte_publico', 'nombre' => 'Transporte Público', 'activo' => 1],
            ]);

            // Set liviano por defecto
            $this->db->query("
                UPDATE valores_comunas vc
                JOIN tipo_vehiculo tv ON tv.clave = 'liviano'
                SET vc.tipo_vehiculo_id = tv.id
                WHERE vc.tipo_vehiculo_id IS NULL
            ");
        }

        // 3) Ahora sí: NOT NULL + FK
        $this->db->query("
            ALTER TABLE `valores_comunas`
            MODIFY `tipo_vehiculo_id` INT(11) UNSIGNED NOT NULL
        ");

        // Agrega la FK si no existe
        // (MySQL/MariaDB no tienen IF NOT EXISTS para FK, usamos nombre estable y atrapamos duplicado si existiera)
        try {
            $this->db->query("
                ALTER TABLE `valores_comunas`
                ADD CONSTRAINT `fk_valores_comunas_tipo_vehiculo`
                FOREIGN KEY (`tipo_vehiculo_id`) REFERENCES `tipo_vehiculo`(`id`)
                ON DELETE RESTRICT ON UPDATE CASCADE
            ");
        } catch (\Throwable $e) {
            // Ignora si ya existe
        }

        // 4) (Opcional) Elimina la columna vieja string si existía
        if ($hasOld) {
            try {
                $this->forge->dropColumn('valores_comunas', 'tipo_vehiculo');
            } catch (\Throwable $e) {
                // Ignora si no se puede
            }
        }
    }

    public function down()
    {
        // Quita FK y columna (si existen)
        try {
            $this->db->query("ALTER TABLE `valores_comunas` DROP FOREIGN KEY `fk_valores_comunas_tipo_vehiculo`");
        } catch (\Throwable $e) {}

        try {
            $this->forge->dropColumn('valores_comunas', 'tipo_vehiculo_id');
        } catch (\Throwable $e) {}
    }
}
