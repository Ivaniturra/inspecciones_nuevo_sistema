<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateComentarios extends Migration
{
    public function up()
    {
        // Opcional: definir charset/collation por si tu conexi�n no lo hace
        $this->db->query('SET NAMES utf8mb4');
        $this->db->query('SET collation_connection = utf8mb4_unicode_ci');

        $this->forge->addField([
            'comentario_id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'comentario_nombre' => [
                'type'       => 'TEXT', // usa VARCHAR(500) si prefieres longitud fija
                'null'       => false,
            ],
            'cia_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
            'comentario_id_cia_interno' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
            'comentario_devuelve' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
                'comment'    => '0=no, 1=s�',
            ],
            'comentario_elimina' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'comentario_envia_correo' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 0,
            ],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
            'deleted_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('comentario_id', true);     // PK
        $this->forge->addKey('cia_id');                  // �ndice para filtrar por empresa
        $this->forge->addKey('comentario_id_cia_interno');

        // Si quieres for�nea a 'cias(cia_id)', descomenta:
        // $this->forge->addForeignKey('cia_id', 'cias', 'cia_id', 'CASCADE', 'RESTRICT');

        $this->forge->createTable('comentarios', true, [
            'ENGINE'  => 'InnoDB',
            'COMMENT' => 'Comentarios del sistema',
        ]);
    }

    public function down()
    {
        // Si agregaste FK, CodeIgniter la quita autom�ticamente al eliminar la tabla
        $this->forge->dropTable('comentarios', true);
    }
}