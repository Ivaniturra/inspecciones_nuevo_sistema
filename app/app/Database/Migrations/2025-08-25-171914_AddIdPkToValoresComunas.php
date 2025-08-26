<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddIdPkToValoresComunas extends Migration
{
    public function up()
    {
        // Si no existe la columna 'id', la agregamos como PK autoincremental
        $fields = $this->db->getFieldNames('valores_comunas');

        if (!in_array('id', $fields, true)) {
            // Quita PK previa si existiera (composite u otra)
            try {
                $this->db->query("ALTER TABLE `valores_comunas` DROP PRIMARY KEY");
            } catch (\Throwable $e) {
                // Ignora si no había PK
            }

            // Agrega columna 'id' al inicio
            $this->db->query("
                ALTER TABLE `valores_comunas`
                ADD COLUMN `id` INT UNSIGNED NOT NULL AUTO_INCREMENT FIRST,
                ADD PRIMARY KEY (`id`)
            ");
        }
    }

    public function down()
    {
        // No quitamos 'id' para no dejar la tabla sin PK
    }
}
