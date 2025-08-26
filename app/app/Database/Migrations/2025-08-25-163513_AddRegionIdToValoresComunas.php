<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRegionIdToValoresComunas extends Migration
{
    public function up()
    {
        $this->db->query("UPDATE valores_comunas vc
        JOIN tipo_vehiculo tv
        ON tv.clave COLLATE utf8mb4_unicode_ci = vc.tipo_vehiculo COLLATE utf8mb4_unicode_ci
        SET vc.tipo_vehiculo_id = tv.id    ");
        // 1) Agregar columna si no existe (temporalmente NULL para poder hacer backfill)
        $fields = $this->db->getFieldNames('valores_comunas');

        if (!in_array('region_id', $fields, true)) {
            $this->forge->addColumn('valores_comunas', [
                'region_id' => [
                    'type'       => 'INT',
                    'constraint' => 11,
                    'unsigned'   => true,
                    'null'       => true,
                    'after'      => 'comuna_codigo',
                ],
            ]);
            $this->db->query('ALTER TABLE `valores_comunas` ADD INDEX (`region_id`)');
        }

        // 2) Backfill: toma region_id desde la tabla comunas (por comuna_codigo)
        if ($this->db->tableExists('comunas')) {
           $this->db->query(" UPDATE valores_comunas vc
                JOIN tipo_vehiculo tv ON tv.clave = 'liviano'
                SET vc.tipo_vehiculo_id = tv.id
                WHERE vc.tipo_vehiculo_id IS NULL ");
            
        }

        // 3) Convertir a NOT NULL (si todo quedó seteado)
        // Si temes que alguna comuna no exista, puedes omitir esta línea o envolverla en try/catch.
        try {
            $this->db->query("
                ALTER TABLE `valores_comunas`
                MODIFY `region_id` INT(11) UNSIGNED NOT NULL
            ");
        } catch (\Throwable $e) {
            // Si falla, deja la columna como NULL y sigue sin cortar la migración
        }

        // 4) Agregar FK a regiones(region_id) si no existe
        try {
            $this->db->query("
                ALTER TABLE `valores_comunas`
                ADD CONSTRAINT `fk_valores_comunas_region`
                FOREIGN KEY (`region_id`) REFERENCES `regiones`(`region_id`)
                ON DELETE RESTRICT ON UPDATE CASCADE
            ");
        } catch (\Throwable $e) {
            // Ignora si ya existe
        }
    }

    public function down()
    {
        // Quitar FK y columna
        try {
            $this->db->query("ALTER TABLE `valores_comunas` DROP FOREIGN KEY `fk_valores_comunas_region`");
        } catch (\Throwable $e) {}
        try {
            $this->forge->dropColumn('valores_comunas', 'region_id');
        } catch (\Throwable $e) {}
    }
}
