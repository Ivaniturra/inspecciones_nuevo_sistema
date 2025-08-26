<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class RenameCiasColumns extends Migration
{
    public function up()
    {
        // Unificamos colación (opcional pero recomendado)
        $this->db->query("ALTER TABLE `cias` CONVERT TO CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");

        // Renombres (id, nombre, habil)
        // Si tu PK es cia_id, este cambio mantiene el PRIMARY KEY (MySQL lo migra al nuevo nombre).
        $this->db->query("ALTER TABLE `cias` CHANGE `cia_id` `cias_id` INT UNSIGNED NOT NULL AUTO_INCREMENT");

        // Ajusta el tamaño de VARCHAR si usas otro
        $this->db->query("ALTER TABLE `cias` CHANGE `cia_nombre` `cias_nombre` VARCHAR(150) NOT NULL");

        // Si cia_habil era tinyint(1) o boolean-like:
        $this->db->query("ALTER TABLE `cias` CHANGE `cia_habil` `cias_habil` TINYINT(1) NOT NULL DEFAULT 1");
    }

    public function down()
    {
        // Revertir a los nombres antiguos
        $this->db->query("ALTER TABLE `cias` CHANGE `cias_id` `cia_id` INT UNSIGNED NOT NULL AUTO_INCREMENT");
        $this->db->query("ALTER TABLE `cias` CHANGE `cias_nombre` `cia_nombre` VARCHAR(150) NOT NULL");
        $this->db->query("ALTER TABLE `cias` CHANGE `cias_habil` `cia_habil` TINYINT(1) NOT NULL DEFAULT 1");
    }
}
